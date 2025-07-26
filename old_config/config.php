<?php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Change this in production
define('DB_NAME', 'rune_match');

// Application paths
define('BASE_URL', 'http://localhost/match3-norse-game'); // Change in production
define('ASSETS_URL', BASE_URL . '/assets');
define('API_URL', BASE_URL . '/includes/api');

// Game configuration
define('GRID_SIZE', 12); // 8x8 grid
define('GAME_DURATION', 120); // 2 minutes in seconds
define('MIN_MATCH', 3); // Minimum match size
define('SCORE_PER_MATCH', 10); // Base score per matched rune
define('COINS_PER_SCORE', 0.1); // Conversion rate from score to coins
define('SEEDS_PER_SCORE', 0.05); // Conversion rate from score to seeds
define('COMBO_MULTIPLIER', 1.5); // Score multiplier for combos

// Rune types
define('RUNE_TYPES_TWO', json_encode([
    'wunjo', 'uruz', 'sowilo ', 'raido', 'phurisaz', 'perthro', 'naudhiz', 'kenaz', 'jera', 'hagalaz ', 'isa', 'eihwaz', 'gebo', 'algiz', 'ansuz'
]));
define('RUNE_TYPES', json_encode([
    'wunjo', 'uruz', 'sowilo ', 'raido', 'phurisaz'
]));

// Special piece types
define('SPECIAL_PIECES', json_encode([
    'thors_hammer', // Clears entire row
    'odins_ravens', // Clears matched type throughout the board
    'yggdrasil', // Clears a 3x3 area
    'heimdall', // Converts nearby runes to same type
    'rainbow_rune' // Can match with any rune
]));

// Rarity definitions
define('RARITY_CHANCES', json_encode([
    'common' => 70,
    'uncommon' => 20,
    'rare' => 8,
    'legendary' => 2
]));

// Session configuration
ini_set('session.cookie_lifetime', 86400); // 1 day
ini_set('session.gc_maxlifetime', 86400); // 1 day
session_start();

// Error reporting in development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load utilities
require_once __DIR__ . '/../includes/classes/Database.php';
require_once __DIR__ . '/../includes/classes/User.php';
require_once __DIR__ . '/../includes/classes/GameSession.php';
require_once __DIR__ . '/../includes/classes/Challenge.php';
require_once __DIR__ . '/../includes/classes/Item.php';
require_once __DIR__ . '/../includes/classes/Leaderboard.php';
?>


