<?php

class GameSession {
    private $db;
    private $sessionId;
    private $userId;
    
    public function __construct($userId = null) {
        $this->db = Database::getInstance();
        $this->userId = $userId ?: $_SESSION['user_id'] ?? null;
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
