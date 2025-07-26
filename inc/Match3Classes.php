<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Change this in production
define('DB_NAME', 'secret_garden_live');
session_start();

class Database {
    private $conn;
    private static $instance = null;
    
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function insert($table, $data) {
        $fields = array_keys($data);
        $placeholders = array_map(function($field) {
            return ":$field";
        }, $fields);
        
        $sql = "INSERT INTO $table (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
        $stmt = $this->conn->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        return $this->conn->lastInsertId();
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $setClause = array_map(function($field) {
            return "$field = :$field";
        }, array_keys($data));
        
        $sql = "UPDATE $table SET " . implode(", ", $setClause) . " WHERE $where";
        $stmt = $this->conn->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        foreach ($whereParams as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        return $stmt->rowCount();
    }
    
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
}


class GameSession {
    private $db;
    private $sessionId;
    private $userId;
    
    public function __construct($userId = null) {
        $this->db = Database::getInstance();
        $this->userId = $userId ?: $_SESSION['wallet_addr'] ?? null;
    }
    
    public function startSession() {
        if (!$this->userId) {
            return ['success' => false, 'message' => 'User not logged in'];
        }
        
        $this->sessionId = $this->db->insert('game_sessions', [
            'user_id' => $this->userId,
            'start_time' => date('Y-m-d H:i:s')
        ]);
        
        return ['success' => true, 'session_id' => $this->sessionId];
    }
    
    public function endSession($score, $coinsEarned, $seedsEarned, $specialItems = []) {
        if (!$this->sessionId) {
            return ['success' => false, 'message' => 'No active game session'];
        }
        
        // Update the session record
        $this->db->update(
            'game_sessions',
            [
                'end_time' => date('Y-m-d H:i:s'),
                'score' => $score,
                'coins_earned' => $coinsEarned,
                'seeds_earned' => $seedsEarned,
                'special_items_earned' => json_encode($specialItems)
            ],
            'session_id = :session_id',
            [':session_id' => $this->sessionId]
        );
        
        // Update user stats
        $userData = $this->db->fetch(
            "SELECT * FROM user_stats WHERE user_id = :user_id",
            [':user_id' => $this->userId]
        );
        
        $playTime = $this->getSessionDuration();
        $highestScore = max($userData['highest_score'], $score);
        
        $this->db->update(
            'user_stats',
            [
                'games_played' => $userData['games_played'] + 1,
                'total_score' => $userData['total_score'] + $score,
                'highest_score' => $highestScore,
                'total_playtime' => $userData['total_playtime'] + $playTime
            ],
            'user_id = :user_id',
            [':user_id' => $this->userId]
        );
        
        // Add to leaderboards
        $this->addToLeaderboards($score);
        
        // Update user resources
        $user = new User();
        $user->updateResources($coinsEarned, $seedsEarned);
        
        // Check for completed challenges
        $challenge = new Challenge($this->userId);
        $challenge->checkCompletions($score, $specialItems);
        
        return [
            'success' => true,
            'session_id' => $this->sessionId,
            'score' => $score,
            'coins_earned' => $coinsEarned,
            'seeds_earned' => $seedsEarned
        ];
    }
    
    private function getSessionDuration() {
        $session = $this->db->fetch(
            "SELECT TIMESTAMPDIFF(SECOND, start_time, NOW()) as duration FROM game_sessions WHERE session_id = :session_id",
            [':session_id' => $this->sessionId]
        );
        
        return $session['duration'] ?? 0;
    }
    
    private function addToLeaderboards($score) {
        // Add to daily leaderboard
        $this->db->insert('leaderboards', [
            'user_id' => $this->userId,
            'score' => $score,
            'session_id' => $this->sessionId,
            'leaderboard_type' => 'daily'
        ]);
        
        // Add to weekly leaderboard
        $this->db->insert('leaderboards', [
            'user_id' => $this->userId,
            'score' => $score,
            'session_id' => $this->sessionId,
            'leaderboard_type' => 'weekly'
        ]);
        
        // Add to all-time leaderboard
        $this->db->insert('leaderboards', [
            'user_id' => $this->userId,
            'score' => $score,
            'session_id' => $this->sessionId,
            'leaderboard_type' => 'all_time'
        ]);
    }
}


class User {
    private $db;
    private $userData;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // public function register($username, $password, $email) {
    //     // Check if username or email already exists
    //     $existingUser = $this->db->fetch(
    //         "SELECT user_id, wallet_address FROM users WHERE username = :username OR email = :email",
    //         [':username' => $username, ':email' => $email]
    //     );
        
    //     if ($existingUser) {
    //         return ['success' => false, 'message' => 'Username or email already exists'];
    //     }
        
    //     // Hash the password
    //     $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
    //     // Insert the new user
    //     $userId = $this->db->insert('users', [
    //         'username' => $username,
    //         'password_hash' => $passwordHash,
    //         'email' => $email
    //     ]);
        
    //     // Initialize user stats
    //     $this->db->insert('user_stats', [
    //         'user_id' => $userId
    //     ]);
        
    //     return ['success' => true, 'user_id' => $userId];
    // }
    
    public function login($walletAddress = null) {
        if (isset($_SESSION['gardeners']['wallet_addr'])) {
            $walletAddress = $_SESSION['gardeners']['wallet_addr'];
        }

        if ($walletAddress == null) { 
            return ['success' => false, 'message' => 'Wallet address needed'];
        }

        $user = $this->db->fetch(
            "SELECT * FROM gardeners WHERE wallet_address = :wallet",
            [':wallet' => $walletAddress]
        );
        
        if (!$user) {
            return ['success' => false, 'message' => 'Wallet not found in Secret World'];
        }
        $user_id = $user['wallet_address'];

        // Set session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $user['player_name'];
        
        return ['success' => true, 'user_id' => $user['wallet_address']];
    }
    
    // public function logout() {
    //     // Unset all session variables
    //     $_SESSION = [];
        
    //     // Destroy the session
    //     session_destroy();
        
    //     return ['success' => true];
    // }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        if (!$this->userData) {
            $this->userData = $this->db->fetch(
                "SELECT p.* FROM gardeners p WHERE p.wallet_address = :user_id",
                ['user_id' => $_SESSION['user_id']]
            );
        }
        
        return $this->userData;
    }
    
    public function updateResources($coins = 0) {
        if (!$this->isLoggedIn()) {
            return ['success' => false, 'message' => 'User not logged in'];
        }
        
        $user = $this->getCurrentUser();
        $newCoins = $user['nursery_coins'] + $coins;
        
        $this->db->update(
            'gardeners',
            [
                'nursery_coins' => $newCoins,
            ],
            'wallet_address = :user_id',
            ['user_id' => $user['user_id']]
        );
        
        // Update local data
        $this->userData['total_coins'] = $newCoins;
        $this->userData['total_seeds'] = 0;
        
        return [
            'success' => true,
            'total_coins' => $newCoins,
            'total_seeds' => 0
        ];
    }
    
    public function updateConsecutiveDays() {
        return ['success' => false];

        if (!$this->isLoggedIn()) {
            return ['success' => false, 'message' => 'User not logged in'];
        }
        
        $user = $this->getCurrentUser();
        $lastPlayed = $user['last_played_date'];
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        $consecutiveDays = $user['consecutive_days'];
        
        // If last played was yesterday, increment consecutive days
        if ($lastPlayed == $yesterday) {
            $consecutiveDays++;
        } 
        // If last played was not yesterday and not today, reset to 1
        else if ($lastPlayed != $today) {
            $consecutiveDays = 1;
        }
        // If last played is today, don't change
        
        $this->db->update(
            'user_stats',
            [
                'consecutive_days' => $consecutiveDays,
                'last_played_date' => $today
            ],
            'user_id = :user_id',
            ['user_id' => $user['user_id']]
        );
        
        // Update local data
        $this->userData['consecutive_days'] = $consecutiveDays;
        $this->userData['last_played_date'] = $today;
        
        return [
            'success' => true,
            'consecutive_days' => $consecutiveDays
        ];
    }
}


class Leaderboard {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getDailyLeaderboard($limit = 10) {
        $today = date('Y-m-d');
        
        $leaderboard = $this->db->fetchAll(
            "SELECT l.score, u.username, l.recorded_at
            FROM leaderboards l
            JOIN users u ON l.user_id = u.user_id
            WHERE l.leaderboard_type = 'daily'
              AND DATE(l.recorded_at) = :today
            ORDER BY l.score DESC
            LIMIT :limit",
            [':today' => $today, ':limit' => $limit]
        );
        
        return $leaderboard;
    }
    
    public function getWeeklyLeaderboard($limit = 10) {
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
        
        $leaderboard = $this->db->fetchAll(
            "SELECT l.score, u.username, l.recorded_at
            FROM leaderboards l
            JOIN users u ON l.user_id = u.user_id
            WHERE l.leaderboard_type = 'weekly'
              AND DATE(l.recorded_at) BETWEEN :start AND :end
            ORDER BY l.score DESC
            LIMIT :limit",
            [':start' => $startOfWeek, ':end' => $endOfWeek, ':limit' => $limit]
        );
        
        return $leaderboard;
    }
    
    public function getAllTimeLeaderboard($limit = 10) {
        $leaderboard = $this->db->fetchAll(
            "SELECT l.score, u.username, l.recorded_at
            FROM leaderboards l
            JOIN users u ON l.user_id = u.user_id
            WHERE l.leaderboard_type = 'all_time'
            ORDER BY l.score DESC
            LIMIT :limit",
            [':limit' => $limit]
        );
        
        return $leaderboard;
    }
    
    public function getFriendsLeaderboard($userId, $limit = 10) {
        // This is a placeholder for friends leaderboard
        // Would need a friends table to implement fully
        
        // For now, just return all users
        $leaderboard = $this->db->fetchAll(
            "SELECT l.score, u.username, l.recorded_at
            FROM leaderboards l
            JOIN users u ON l.user_id = u.user_id
            WHERE l.leaderboard_type = 'all_time'
            ORDER BY l.score DESC
            LIMIT :limit",
            [':limit' => $limit]
        );
        
        return $leaderboard;
    }
    
    public function getUserRank($userId, $type = 'all_time') {
        $userScore = $this->db->fetch(
            "SELECT MAX(score) as score
            FROM leaderboards
            WHERE user_id = :user_id
              AND leaderboard_type = :type",
            [':user_id' => $userId, ':type' => $type]
        );
        
        if (!$userScore || !$userScore['score']) {
            return null;
        }
        
        $rank = $this->db->fetch(
            "SELECT COUNT(*) as rank
            FROM leaderboards
            WHERE leaderboard_type = :type
              AND score > :score",
            [':type' => $type, ':score' => $userScore['score']]
        );
        
        return $rank['rank'] + 1; // +1 because we count users with higher scores
    }
}


class Item {
    private $db;
    private $userId;
    
    public function __construct($userId = null) {
        $this->db = Database::getInstance();
        $this->userId = $userId ?: $_SESSION['user_id'] ?? null;
    }
    
    public function getUserInventory($attempt = 1) {
        if (!$this->userId) {
            return ['success' => false, 'message' => 'User not logged in'];
        }
    
        // Safety: prevent endless recursion
        if ($attempt > 3) {
            return ['success' => false, 'message' => 'Inventory retrieval failed after multiple attempts'];
        }
    
        $inventory = $this->db->fetchAll(
            "SELECT *
            FROM players_inventory
            WHERE player_wallet = :user_id 
              AND item_id IN (
                  SELECT id FROM marketplace WHERE item_affects = 'ingame_rune_match'
              )",
            [':user_id' => $this->userId]
        );
    
        if (count($inventory) == 0) {
            $allPowerUps = $this->allPowerUps()['data'];

            for ($i=0; $i < count($allPowerUps); $i++) { 
                $oneTagname = strtolower(str_replace(['\'', ' '], ['', '_'], $allPowerUps[$i]['item_name']));
                $this->addItemToUser($oneTagname, 5);
            }

            return $this->getUserInventory($attempt + 1);
        }
    
        return [
            'success' => true,
            'inventory' => $inventory,
        ];
    }

    public function allPowerUps($completeRows = true) {
        // Check if item exists
        $items = $this->db->fetchAll(
            "SELECT * FROM marketplace WHERE item_affects = 'ingame_rune_match'"
        );

        if ($completeRows) {
            return ['success' => true, 'data' => $items];
        }

        $allTagnames = [];
        for ($i=0; $i < count($items); $i++) { 
            $oneTagname = strtolower(str_replace(['\'', ' '], ['', '_'], $items[$i]['item_name']));
            array_push($allTagnames, $oneTagname);
        }

        return ['success' => true, 'data' => $allTagnames];
    }

    public function getMarketplaceItem($itemName) {
        // Check if item exists
        $item = $this->db->fetch(
            "SELECT * FROM marketplace WHERE (item_img LIKE :item_img OR item_img = :item_imgfull) AND item_affects = :item_affects",
            [':item_img' => '%'.$itemName, ':item_imgfull' => $itemName.'.png', ':item_affects' => 'ingame_rune_match']
        );

        if (!$item) {
            return ['success' => false, 'message' => 'Item not found'];
        }
        
        return ['success' => true, 'data' => $item];
    }
    
    public function addItemToUser($itemName, $quantity = 1) {
        if (!$this->userId) {
            return ['success' => false, 'message' => 'User not logged in'];
        }
        
        if ($itemName == 'heimdall') {
            $itemName == 'heimdalls_horn';
        }
        $allPowerUps = $this->allPowerUps(false)['data'];
        if (!in_array($itemName, $allPowerUps)) {
            return ['success' => true, 'message' => 'Cursed rune played another trick'];
        }

        $marketItem = $this->getMarketplaceItem($itemName);

        $item = $marketItem['data'];
        $itemId = $item['id'];
        if ($item['stock'] <= 0) {
            return ['success' => false, 'message' => 'Item out of stock'];
        }
        // Check if user already has this items
        $existingItem = $this->db->fetch(
            "SELECT * FROM players_inventory WHERE player_wallet = :user_id AND item_id = :item_id",
            [':user_id' => $this->userId, ':item_id' => $itemId]
        );
 
        if ($existingItem) {
            // Update quantity
            $sql = "UPDATE players_inventory SET quantity = quantity + $quantity WHERE player_wallet = '".$this->userId."' AND item_id = '".$itemId."' LIMIT 1";
            $query = $this->db->query($sql, []);

            return ['success' => true, 'message' => $quantity .' '. $itemName . ' added.'];

        } else {
            // Add new item to inventory
            $this->db->insert('players_inventory', [
                'player_wallet' => $this->userId,
                'item_id' => $itemId,
                'item_img' => $item['item_img'],
                'item_name' => $item['item_name'],
                'quantity' => $quantity,
                'is_ingame_object' => '1'
            ]);
        }

        return [
            'success' => true,
            'item' => $item,
            'quantity' => ($existingItem ? $existingItem['quantity'] + $quantity : $quantity)
        ];
    }
    
    public function useItem($itemName) {
        if (!$this->userId) {
            return ['success' => false, 'message' => 'User not logged in'];
        }

        $marketItem = $this->getMarketplaceItem($itemName);
        if (!$marketItem['success']) {
            return ['success' => false, 'message' => 'Item not found'];
        }

        $item = $marketItem['data'];
        $itemId = $item['id'];

        // Get player item details
        $userItem = $this->db->fetch(
            "SELECT * FROM players_inventory WHERE item_id = :item_id AND 'player_wallet = :user_id",
            [':item_id' => $itemId, ':user_id' => $this->userId]
        );
        
        // Apply item effect (this would depend on the item type)
        // This is a placeholder for item effects
        // $effectResult = $this->applyItemEffect($item);
        
        // Decrement quantity
        $this->db->update(
            'players_inventory',
            ['quantity' => $userItem['quantity'] - 1],
            'user_id = :user_id AND item_id = :item_id',
            [':user_id' => $this->userId, ':item_id' => $itemId]
        );
        
        return [
            'success' => true,
            'item' => $item,
            'effect_result' => null,
            'remaining_quantity' => $userItem['quantity'] - 1
        ];
    }
    
    private function applyItemEffect($item) {
        // This is a placeholder for item effects
        // Would be implemented based on item types
        
        switch ($item['item_type']) {
            case 'boost':
                // Apply boost effects
                return ['boost_applied' => true, 'message' => 'Boost activated!'];
                
            case 'rune_lore':
                // Unlock rune lore
                return ['lore_unlocked' => true, 'message' => 'New lore unlocked!'];
                
            case 'decoration':
            case 'plant':
                // These are passive items for garden decoration
                return ['message' => 'Item is now in your garden!'];
                
            default:
                return ['message' => 'Item used!'];
        }
    }
}

class Challenge {
    private $db;
    private $userId;
    
    public function __construct($userId = null) {
        $this->db = Database::getInstance();
        $this->userId = $userId ?: $_SESSION['user_id'] ?? null;
    }
    
    public function getDailyChallenges() {
        $today = date('Y-m-d');
        
        $challenges = $this->db->fetchAll(
            "SELECT dc.*, uc.completed 
            FROM daily_challenges dc
            LEFT JOIN user_challenges uc ON dc.challenge_id = uc.challenge_id AND uc.user_id = :user_id
            WHERE dc.challenge_date = :today",
            [':user_id' => $this->userId, ':today' => $today]
        );
        
        return $challenges;
    }
    
    public function checkCompletions($score, $specialItems = []) {
        if (!$this->userId) {
            return ['success' => false, 'message' => 'User not logged in'];
        }
        
        $dailyChallenges = $this->getDailyChallenges();
        $completedChallenges = [];
        
        foreach ($dailyChallenges as $challenge) {
            // Skip already completed challenges
            if ($challenge['completed']) {
                continue;
            }
            
            $completed = false;
            
            // Check if challenge is completed based on score
            if ($challenge['target_score'] <= $score) {
                $completed = true;
            }
            
            // Here would be more logic to check other types of challenges
            // like matching specific types of runes, using special powers, etc.
            
            if ($completed) {
                // Mark challenge as completed
                $this->db->insert('user_challenges', [
                    'user_id' => $this->userId,
                    'challenge_id' => $challenge['challenge_id'],
                    'completed' => true,
                    'completion_time' => date('Y-m-d H:i:s')
                ]);
                
                // Grant rewards
                $this->grantChallengeReward($challenge);
                
                $completedChallenges[] = $challenge;
            }
        }
        
        return [
            'success' => true,
            'completed_challenges' => $completedChallenges
        ];
    }
    
    private function grantChallengeReward($challenge) {
        $user = new User();
        
        switch ($challenge['reward_type']) {
            case 'coins':
                $user->updateResources($challenge['reward_amount'], 0);
                break;
                
            case 'seeds':
                $user->updateResources(0, $challenge['reward_amount']);
                break;
                
            case 'special_item':
                $itemId = $challenge['reward_item_id'];
                
                // Check if user already has this item
                $existingItem = $this->db->fetch(
                    "SELECT * FROM user_inventory WHERE user_id = :user_id AND item_id = :item_id",
                    [':user_id' => $this->userId, ':item_id' => $itemId]
                );
                
                if ($existingItem) {
                    // Increment quantity
                    $this->db->update(
                        'user_inventory',
                        ['quantity' => $existingItem['quantity'] + $challenge['reward_amount']],
                        'user_id = :user_id AND item_id = :item_id',
                        [':user_id' => $this->userId, ':item_id' => $itemId]
                    );
                } else {
                    // Add new item to inventory
                    $this->db->insert('user_inventory', [
                        'user_id' => $this->userId,
                        'item_id' => $itemId,
                        'quantity' => $challenge['reward_amount']
                    ]);
                }
                break;
        }
    }
}
