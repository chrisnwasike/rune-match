<?php

header('Content-Type: application/json');

// Check if user is logged in
// $user = new User();
// if (!$user->isLoggedIn()) {
//     echo json_encode(['success' => false, 'message' => 'User not logged in']);
//     exit;
// }

$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];
$gameSession = new GameSession();

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
        $BASE_GRID_SIZE = 5;
        $BASE_GAME_DURATION = 60; 
        $BASE_MIN_MATCH = 3;
        $BASE_SCORE_PER_MATCH = 100;
        $BASE_COINS_PER_SCORE = 0.1;
        $BASE_SEEDS_PER_SCORE = 0.05;
        $BASE_RUNE_TYPES = 3;
        $level = 1;
        $ALL_RUNES = [
            'wunjo', 'uruz', 'sowilo ', 'raido', 'phurisaz', 'perthro', 'naudhiz', 'kenaz', 'jera', 'hagalaz ', 'isa', 'eihwaz', 'gebo', 'algiz', 'ansuz'
        ];

        // Scale factors (how quickly values change per level)
        $GRID_SIZE_SCALE = 0.5;       // +0.5 every level, rounded to nearest integer
        $DURATION_SCALE = -2;         // -2 seconds per level (gets harder)
        $MIN_DURATION = 30;           // Minimum duration (seconds)
        $MIN_MATCH_INTERVAL = 5;      // Increase min_match every 5 levels
        $SCORE_SCALE = 1.15;          // 15% increase per level
        $COINS_SCALE = 1.05;          // 5% increase per level
        $SEEDS_SCALE = 1.08;          // 8% increase per level


        $response = [
            'success' => true,
            'grid_size' => round($BASE_GRID_SIZE + ($GRID_SIZE_SCALE * ($level - 1))),
            'game_duration' => max($MIN_DURATION, $BASE_GAME_DURATION + ($DURATION_SCALE * ($level - 1))),
            'min_match' => $BASE_MIN_MATCH + floor(($level - 1) / $MIN_MATCH_INTERVAL),
            'score_per_match' => round($BASE_SCORE_PER_MATCH * pow($SCORE_SCALE, $level - 1)),
            'coins_per_score' => floatval(($BASE_COINS_PER_SCORE * pow($COINS_SCALE, $level - 1))),
            'seeds_per_score' => floatval(($BASE_SEEDS_PER_SCORE * pow($SEEDS_SCALE, $level - 1))),
            'rune_types' => json_encode(array_slice($ALL_RUNES, 0, $BASE_RUNE_TYPES)),
        ];

        // $response = [
        //     'success' => true,
        //     'grid_size' => GRID_SIZE,
        //     'game_duration' => GAME_DURATION,
        //     'min_match' => MIN_MATCH,
        //     'score_per_match' => SCORE_PER_MATCH,
        //     'coins_per_score' => COINS_PER_SCORE,
        //     'seeds_per_score' => SEEDS_PER_SCORE,
        //     'rune_types' => json_decode($ALL_RUNES),
        // ];
        break;
}

echo json_encode($response);
?>