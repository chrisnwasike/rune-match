<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

$leaderboard = new Leaderboard();

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

echo json_encode($response);
?>