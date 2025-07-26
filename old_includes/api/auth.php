<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

$user = new User();

switch ($action) {
    case 'register':
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $email = $_POST['email'] ?? '';
        
        if (empty($username) || empty($password) || empty($email)) {
            $response = ['success' => false, 'message' => 'All fields are required'];
            break;
        }
        
        $response = $user->register($username, $password, $email);
        break;
        
    case 'login':
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $response = ['success' => false, 'message' => 'Username and password are required'];
            break;
        }
        
        $response = $user->login($username, $password);
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

echo json_encode($response);
?>