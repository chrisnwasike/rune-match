<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

// Check if user is logged in
$user = new User();
if (!$user->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

$item = new Item();

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
}

echo json_encode($response);
?>