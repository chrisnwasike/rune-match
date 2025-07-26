<?php


class Leaderboard {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getDailyLeaderboard($limit = 10) {
        $today = date('Y-m-d');
        
        $leaderboard = $this->db->fetchAll(
            "SELECT l.score, u.username, l.recorded_at
            FROM leaderboards l
            JOIN users u ON l.user_id = u.user_id
            WHERE l.leaderboard_type = 'daily'
              AND DATE(l.recorded_at) = :today
            ORDER BY l.score DESC
            LIMIT :limit",
            [':today' => $today, ':limit' => $limit]
        );
        
        return $leaderboard;
    }
    
    public function getWeeklyLeaderboard($limit = 10) {
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
        
        $leaderboard = $this->db->fetchAll(
            "SELECT l.score, u.username, l.recorded_at
            FROM leaderboards l
            JOIN users u ON l.user_id = u.user_id
            WHERE l.leaderboard_type = 'weekly'
              AND DATE(l.recorded_at) BETWEEN :start AND :end
            ORDER BY l.score DESC
            LIMIT :limit",
            [':start' => $startOfWeek, ':end' => $endOfWeek, ':limit' => $limit]
        );
        
        return $leaderboard;
    }
    
    public function getAllTimeLeaderboard($limit = 10) {
        $leaderboard = $this->db->fetchAll(
            "SELECT l.score, u.username, l.recorded_at
            FROM leaderboards l
            JOIN users u ON l.user_id = u.user_id
            WHERE l.leaderboard_type = 'all_time'
            ORDER BY l.score DESC
            LIMIT :limit",
            [':limit' => $limit]
        );
        
        return $leaderboard;
    }
    
    public function getFriendsLeaderboard($userId, $limit = 10) {
        // This is a placeholder for friends leaderboard
        // Would need a friends table to implement fully
        
        // For now, just return all users
        $leaderboard = $this->db->fetchAll(
            "SELECT l.score, u.username, l.recorded_at
            FROM leaderboards l
            JOIN users u ON l.user_id = u.user_id
            WHERE l.leaderboard_type = 'all_time'
            ORDER BY l.score DESC
            LIMIT :limit",
            [':limit' => $limit]
        );
        
        return $leaderboard;
    }
    
    public function getUserRank($userId, $type = 'all_time') {
        $userScore = $this->db->fetch(
            "SELECT MAX(score) as score
            FROM leaderboards
            WHERE user_id = :user_id
              AND leaderboard_type = :type",
            [':user_id' => $userId, ':type' => $type]
        );
        
        if (!$userScore || !$userScore['score']) {
            return null;
        }
        
        $rank = $this->db->fetch(
            "SELECT COUNT(*) as rank
            FROM leaderboards
            WHERE leaderboard_type = :type
              AND score > :score",
            [':type' => $type, ':score' => $userScore['score']]
        );
        
        return $rank['rank'] + 1; // +1 because we count users with higher scores
    }
}
