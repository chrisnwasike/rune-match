<?php
//
// User class for handling user-related operations
class User {
    private $db;
    private $userData;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function register($username, $password, $email) {
        // Check if username or email already exists
        $existingUser = $this->db->fetch(
            "SELECT user_id, wallet_address FROM users WHERE username = :username OR email = :email",
            [':username' => $username, ':email' => $email]
        );
        
        if ($existingUser) {
            return ['success' => false, 'message' => 'Username or email already exists'];
        }
        
        // Hash the password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert the new user
        $userId = $this->db->insert('users', [
            'username' => $username,
            'password_hash' => $passwordHash,
            'email' => $email
        ]);
        
        // Initialize user stats
        $this->db->insert('user_stats', [
            'user_id' => $userId
        ]);
        
        return ['success' => true, 'user_id' => $userId];
    }
    
    public function login($username, $password) {
        $user = $this->db->fetch(
            "SELECT user_id, username, password_hash FROM users WHERE username = :username",
            [':username' => $username]
        );
        
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Invalid username or password'];
        }
        $user_id = $user['user_id'];

        // Update last login time
        $this->db->update(
            'users',
            ['last_login' => date('Y-m-d H:i:s')],
            'user_id = :user_id',
            ['user_id' => $user_id]
        );
        
        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        
        return ['success' => true, 'user_id' => $user['user_id']];
    }
    
    public function logout() {
        // Unset all session variables
        $_SESSION = [];
        
        // Destroy the session
        session_destroy();
        
        return ['success' => true];
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        if (!$this->userData) {
            $this->userData = $this->db->fetch(
                "SELECT u.*, us.* FROM users u 
                JOIN user_stats us ON u.user_id = us.user_id 
                WHERE u.user_id = :user_id",
                ['user_id' => $_SESSION['user_id']]
            );
        }
        
        return $this->userData;
    }
    
    public function updateResources($coins = 0, $seeds = 0) {
        if (!$this->isLoggedIn()) {
            return ['success' => false, 'message' => 'User not logged in'];
        }
        
        $user = $this->getCurrentUser();
        $newCoins = $user['total_coins'] + $coins;
        $newSeeds = $user['total_seeds'] + $seeds;
        
        $this->db->update(
            'users',
            [
                'total_coins' => $newCoins,
                'total_seeds' => $newSeeds
            ],
            'user_id = :user_id',
            ['user_id' => $user['user_id']]
        );
        
        // Update local data
        $this->userData['total_coins'] = $newCoins;
        $this->userData['total_seeds'] = $newSeeds;
        
        return [
            'success' => true,
            'total_coins' => $newCoins,
            'total_seeds' => $newSeeds
        ];
    }
    
    public function updateConsecutiveDays() {
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
