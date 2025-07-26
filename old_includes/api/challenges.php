<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

// Check if user is logged in
$user = new User();
if (!$user->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$action = $_GET['action'] ?? '';
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

echo json_encode($response);
?>