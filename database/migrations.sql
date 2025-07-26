-- Create the database
CREATE DATABASE IF NOT EXISTS rune_match;
USE rune_match;

-- Users table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    wallet_address VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    total_coins INT DEFAULT 0,
    total_seeds INT DEFAULT 0
);

-- Game sessions table
CREATE TABLE game_sessions (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP NULL,
    score INT DEFAULT 0,
    coins_earned INT DEFAULT 0,
    seeds_earned INT DEFAULT 0,
    special_items_earned JSON NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Daily challenges table
CREATE TABLE daily_challenges (
    challenge_id INT AUTO_INCREMENT PRIMARY KEY,
    challenge_date DATE NOT NULL,
    description VARCHAR(255) NOT NULL,
    target_score INT NOT NULL,
    reward_type ENUM('coins', 'seeds', 'special_item') NOT NULL,
    reward_amount INT NOT NULL,
    reward_item_id INT NULL
);

-- User challenges table
CREATE TABLE user_challenges (
    user_id INT NOT NULL,
    challenge_id INT NOT NULL,
    completed BOOLEAN DEFAULT FALSE,
    completion_time TIMESTAMP NULL,
    PRIMARY KEY (user_id, challenge_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (challenge_id) REFERENCES daily_challenges(challenge_id)
);

-- Leaderboards table
CREATE TABLE leaderboards (
    entry_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    score INT NOT NULL,
    session_id INT NOT NULL,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    leaderboard_type ENUM('daily', 'weekly', 'all_time') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (session_id) REFERENCES game_sessions(session_id)
);

-- Special items table
CREATE TABLE special_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL,
    item_description TEXT NOT NULL,
    item_type ENUM('decoration', 'plant', 'rune_lore', 'boost') NOT NULL,
    rarity ENUM('common', 'uncommon', 'rare', 'legendary') NOT NULL,
    image_path VARCHAR(255) NOT NULL
);

-- User inventory table
CREATE TABLE user_inventory (
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT DEFAULT 1,
    acquired_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, item_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (item_id) REFERENCES special_items(item_id)
);

-- User stats table
CREATE TABLE user_stats (
    user_id INT PRIMARY KEY,
    games_played INT DEFAULT 0,
    total_score INT DEFAULT 0,
    highest_score INT DEFAULT 0,
    total_playtime INT DEFAULT 0, -- in seconds
    consecutive_days INT DEFAULT 0,
    last_played_date DATE NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Create indexes for optimization
CREATE INDEX idx_game_sessions_user_id ON game_sessions(user_id);
CREATE INDEX idx_user_challenges_challenge_id ON user_challenges(challenge_id);
CREATE INDEX idx_leaderboards_user_id ON leaderboards(user_id);
CREATE INDEX idx_leaderboards_type_score ON leaderboards(leaderboard_type, score DESC);
CREATE INDEX idx_user_inventory_item_id ON user_inventory(item_id);

-- Insert some initial special items
INSERT INTO marketplace (item_name, description, item_img, category_id, item_affects, price, is_ingame_object, stock) VALUES
('Thor\'s Hammer', 'Clears an entire row off the board.', 'thors_hammer.png', 8, 'ingame_rune_match', 1000, 1, 100),
('Odin\'s Ravens', 'Clears selected rune type throughout the board', 'odins_ravens.png', 8, 'ingame_rune_match', 1000, 1, 100),
('Yggdrasil', 'Clears a 3 x 3 area from the seleced rune of the board.', 'yggdrasil.png', 8, 'ingame_rune_match', 1000, 1, 100),
('Heimdall\'s Horn', 'Clears an entire column off the board.',  'heimdall.png', 8, 'ingame_rune_match', 1000, 1, 100),

-- Create some sample daily challenges
INSERT INTO daily_challenges (challenge_date, description, target_score, reward_type, reward_amount, reward_item_id) VALUES
(CURDATE(), 'Score 5000 points in a single game', 5000, 'coins', 100, NULL),
(CURDATE(), 'Match 50 runes in a single game', 50, 'seeds', 50, NULL),
(CURDATE(), 'Trigger Thor\'s Hammer power-up 3 times', 3, 'special_item', 1, 1),
(DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'Score 7500 points in a single game', 7500, 'coins', 150, NULL),
(DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'Match 75 runes in a single game', 75, 'seeds', 75, NULL),
(DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'Find 5 hidden runes', 5, 'special_item', 1, 3);

