<?php
include_once('Match3Classes.php');

// Check if user is logged in
// $user = new User();
// if (!$user->isLoggedIn()) {
//     echo json_encode(['success' => false, 'message' => 'User not logged in']);
//     exit;
// }

$group = $_GET['endpnt'] ?? '';
$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

$gameSession = new GameSession();
$leaderboard = new Leaderboard();
$item = new Item();
$challenges = new Challenge();
$user = new User();
$user->login();

if ($group == 'auth') {
    
    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'message' => 'Invalid action'];

    switch ($action) {
        // case 'register':
        //     $username = $_POST['username'] ?? '';
        //     $password = $_POST['password'] ?? '';
        //     $email = $_POST['email'] ?? '';
            
        //     if (empty($username) || empty($password) || empty($email)) {
        //         $response = ['success' => false, 'message' => 'All fields are required'];
        //         break;
        //     }
            
        //     $response = $user->register($username, $password, $email);
        //     break;
            
        case 'login':
            $wallet_address = $_POST['wallet_address'] ?? $_SESSION['gardeners']['wallet_addr'];
            
            if (empty($wallet_address)) {
                $response = ['success' => false, 'message' => 'Wallet address is required'];
                break;
            }
            
            $response = $user->login($wallet_address);
            break;
            
        case 'logout':
            $response = $user->logout();
            break;
            
        case 'check_auth':
            $response = [
                'success' => true,
                'is_logged_in' => $user->isLoggedIn(),
                'user' => $user->isLoggedIn() ? $user->getCurrentUser() : null
            ];
            break;
    }

}
elseif ($group == 'game') {
    switch ($action) {
        case 'start_game':
            $response = $gameSession->startSession();
            
            // Update consecutive days when starting a game
            // $user->updateConsecutiveDays();
            break;
            
        case 'end_game':
            $score = (int)($_POST['score'] ?? 0);
            $coinsEarned = (int)($_POST['coins_earned'] ?? 0);
            $seedsEarned = (int)($_POST['seeds_earned'] ?? 0);
            $specialItems = json_decode($_POST['special_items'] ?? '[]', true);
            
            $response = $gameSession->endSession($score, $coinsEarned, $seedsEarned, $specialItems);
            break;
            
        case 'get_game_config':
            $level = $_POST['level'];
            $ALL_THEMES = ['wood', 'stone', 'volcanic'];
            $rand_theme = $ALL_THEMES[rand(0, count($ALL_THEMES)-1)];
            $BASE_GRID_SIZE = 7;
            $BASE_GAME_DURATION = 120; 
            $BASE_MIN_MATCH = 3;
            $BASE_SCORE_PER_MATCH = 10;
            $BASE_COINS_PER_SCORE = 0.1;
            $BASE_SEEDS_PER_SCORE = 0.5;
            $ALL_RUNES = [
                'ansuz', 'algiz', 'eihwaz', 'hagalaz', 'sowilo', 'phurisaz', 'raido', 'uruz', 'wunjo'
                // 'wunjo', 'uruz', 'sowilo', 'raido', 'phurisaz', 'perthro', 'naudhiz', 'kenaz', 'jera', 'hagalaz ', 'isa', 'eihwaz', 'gebo', 'algiz', 'ansuz'
            ];
            $ALL_RUNES_SYMBOLS = [
                'ᚨ', 'ᛉ', 'ᛇ', 'ᚻ', 'ᛋ', 'ᚦ', 'ᚱ', 'ᚢ', 'ᚹ'
                // 'ᚹ', 'ᚢ', 'ᛋ', 'ᚱ', 'ᚦ', 'ᛈ', '	ᚾ', '<', 'ᛃ', 'ᚻ', '|', 'ᛇ', 'x', 'ᛉ', 'ᚨ'
            ];

            $RUNES = [
                'types' =>  [
                    'ansuz', 'algiz', 'eihwaz', 'hagalaz', 'uruz', 'phurisaz', 'raido', 'sowilo', 'wunjo'
                ],
                'symbols' => [
                    'ᚨ', 'ᛉ', 'ᛇ', 'ᚻ', 'ᚢ', 'ᚦ', 'ᚱ', 'ᛋ', 'ᚹ'
                ],
                'color' => [
                    '0b9cff', 'c3c3c3', '8BC34A', 'FFAB00', 'c83f33', 'FFD700', '00BCD4', 'f7a600', 'C0C0C0'
                ],
                'glow' => [
                    '6A1B9A', '90A4AE', '43A047', 'FF3D00', 'ff3523', 'D50000', '1565C0', 'FFD600', '6A1B9A'
                ]
            ];


    
            // Scale factors (how quickly values change per level)
            $GRID_SIZE_SCALE = 0.5;       // +0.5 every level, rounded to nearest integer
            $DURATION_SCALE = -2;         // -2 seconds per level (gets harder)
            $MIN_DURATION = 30;           // Minimum duration (seconds)
            $MIN_MATCH_INTERVAL = 5;      // Increase min_match every 5 levels
            $SCORE_SCALE = 1.15;          // 15% increase per level
            $COINS_SCALE = 1.05;          // 5% increase per level
            $SEEDS_SCALE = 1.08;          // 8% increase per level
            $BASE_RUNE_TYPES = min(ceil(round($BASE_GRID_SIZE + ($GRID_SIZE_SCALE * ($level - 1))) / 2) + 1, count($RUNES['types']));
    
            $response = [
                'success' => true,
                'player_level' => 1,
                'grid_size' => round($BASE_GRID_SIZE + ($GRID_SIZE_SCALE * ($level - 1))),
                'game_duration' => max($MIN_DURATION, $BASE_GAME_DURATION + ($DURATION_SCALE * ($level - 1))),
                'min_match' => $BASE_MIN_MATCH + floor(($level - 1) / $MIN_MATCH_INTERVAL),
                'score_per_match' => round($BASE_SCORE_PER_MATCH * pow($SCORE_SCALE, $level - 1)),
                'coins_per_score' => floatval(($BASE_COINS_PER_SCORE * pow($COINS_SCALE, $level - 1))),
                'seeds_per_score' => floatval(($BASE_SEEDS_PER_SCORE * pow($SEEDS_SCALE, $level - 1))),
                'rune_types' => (array_slice($RUNES['types'], 0, $BASE_RUNE_TYPES)),
                'rune_types_symbols' => (array_slice($RUNES['symbols'], 0, $BASE_RUNE_TYPES)),
                'rune_types_color' => (array_slice($RUNES['color'], 0, $BASE_RUNE_TYPES)),
                'rune_types_glow' => (array_slice($RUNES['glow'], 0, $BASE_RUNE_TYPES)),
                'game_theme' => $rand_theme,
                'nft_contracts' => ''
            ];
            break;
    }
}
elseif ($group == 'leaderboard') {
    $action = $_GET['action'] ?? '';
    $response = ['success' => false, 'message' => 'Invalid action'];

    switch ($action) {
        case 'daily':
            $limit = (int)($_GET['limit'] ?? 10);
            $response = [
                'success' => true,
                'leaderboard' => $leaderboard->getDailyLeaderboard($limit)
            ];
            break;
            
        case 'weekly':
            $limit = (int)($_GET['limit'] ?? 10);
            $response = [
                'success' => true,
                'leaderboard' => $leaderboard->getWeeklyLeaderboard($limit)
            ];
            break;
            
        case 'all_time':
            $limit = (int)($_GET['limit'] ?? 10);
            $response = [
                'success' => true,
                'leaderboard' => $leaderboard->getAllTimeLeaderboard($limit)
            ];
            break;
            
        case 'friends':
            $user = new User();
            if (!$user->isLoggedIn()) {
                $response = ['success' => false, 'message' => 'User not logged in'];
                break;
            }
            
            $userId = $_SESSION['user_id'];
            $limit = (int)($_GET['limit'] ?? 10);
            
            $response = [
                'success' => true,
                'leaderboard' => $leaderboard->getFriendsLeaderboard($userId, $limit)
            ];
            break;
            
        case 'user_rank':
            $user = new User();
            if (!$user->isLoggedIn()) {
                $response = ['success' => false, 'message' => 'User not logged in'];
                break;
            }
            
            $userId = $_SESSION['user_id'];
            $type = $_GET['type'] ?? 'all_time';
            
            $response = [
                'success' => true,
                'rank' => $leaderboard->getUserRank($userId, $type)
            ];
            break;
    }

}
elseif ($group == 'inventory') {
    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'message' => 'Invalid action'];

    switch ($action) {
        case 'get_inventory':
            $response = $item->getUserInventory();
        break;  
            
        case 'use_item':
            $itemId = (int)($_POST['item_id'] ?? 0);
            
            if ($itemId <= 0) {
                $response = ['success' => false, 'message' => 'Invalid item ID'];
                break;
            }
            
            $response = $item->useItem($itemId);
        break;
        
        case 'add_item':
            $itemName = ($_POST['itemName'] ?? 0);
            $itemQty = ($_POST['itemQty'] ?? 1);
            
            if ($itemName == '') {
                $response = ['success' => false, 'message' => 'Invalid item ID'];
                break;
            }
            
            $response = $item->addItemToUser($itemName, $itemQty);
        break;
    }
}
elseif ($group == 'challenges') {
    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'message' => 'Invalid action'];
    $challenge = new Challenge();

    switch ($action) {
        case 'get_daily_challenges':
            $challenges = $challenge->getDailyChallenges();
            $response = [
                'success' => true,
                'challenges' => $challenges
            ];
            break;
    }
}


header('Content-Type: application/json');

echo json_encode($response);