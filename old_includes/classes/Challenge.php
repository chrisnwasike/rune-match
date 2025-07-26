<?php

class Challenge {
    private $db;
    private $userId;
    
    public function __construct($userId = null) {
        $this->db = Database::getInstance();
        $this->userId = $userId ?: $_SESSION['user_id'] ?? null;
    }
    
    public function getDailyChallenges() {
        $today = date('Y-m-d');
        
        $challenges = $this->db->fetchAll(
            "SELECT dc.*, uc.completed 
            FROM daily_challenges dc
            LEFT JOIN user_challenges uc ON dc.challenge_id = uc.challenge_id AND uc.user_id = :user_id
            WHERE dc.challenge_date = :today",
            [':user_id' => $this->userId, ':today' => $today]
        );
        
        return $challenges;
    }
    
    public function checkCompletions($score, $specialItems = []) {
        if (!$this->userId) {
            return ['success' => false, 'message' => 'User not logged in'];
        }
        
        $dailyChallenges = $this->getDailyChallenges();
        $completedChallenges = [];
        
        foreach ($dailyChallenges as $challenge) {
            // Skip already completed challenges
            if ($challenge['completed']) {
                continue;
            }
            
            $completed = false;
            
            // Check if challenge is completed based on score
            if ($challenge['target_score'] <= $score) {
                $completed = true;
            }
            
            // Here would be more logic to check other types of challenges
            // like matching specific types of runes, using special powers, etc.
            
            if ($completed) {
                // Mark challenge as completed
                $this->db->insert('user_challenges', [
                    'user_id' => $this->userId,
                    'challenge_id' => $challenge['challenge_id'],
                    'completed' => true,
                    'completion_time' => date('Y-m-d H:i:s')
                ]);
                
                // Grant rewards
                $this->grantChallengeReward($challenge);
                
                $completedChallenges[] = $challenge;
            }
        }
        
        return [
            'success' => true,
            'completed_challenges' => $completedChallenges
        ];
    }
    
    private function grantChallengeReward($challenge) {
        $user = new User();
        
        switch ($challenge['reward_type']) {
            case 'coins':
                $user->updateResources($challenge['reward_amount'], 0);
                break;
                
            case 'seeds':
                $user->updateResources(0, $challenge['reward_amount']);
                break;
                
            case 'special_item':
                $itemId = $challenge['reward_item_id'];
                
                // Check if user already has this item
                $existingItem = $this->db->fetch(
                    "SELECT * FROM user_inventory WHERE user_id = :user_id AND item_id = :item_id",
                    [':user_id' => $this->userId, ':item_id' => $itemId]
                );
                
                if ($existingItem) {
                    // Increment quantity
                    $this->db->update(
                        'user_inventory',
                        ['quantity' => $existingItem['quantity'] + $challenge['reward_amount']],
                        'user_id = :user_id AND item_id = :item_id',
                        [':user_id' => $this->userId, ':item_id' => $itemId]
                    );
                } else {
                    // Add new item to inventory
                    $this->db->insert('user_inventory', [
                        'user_id' => $this->userId,
                        'item_id' => $itemId,
                        'quantity' => $challenge['reward_amount']
                    ]);
                }
                break;
        }
    }
}
