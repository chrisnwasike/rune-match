<?php
//
// Item class for handling item-related operations
class Item {
    private $db;
    private $userId;
    
    public function __construct($userId = null) {
        $this->db = Database::getInstance();
        $this->userId = $userId ?: $_SESSION['user_id'] ?? null;
    }
    
    public function getUserInventory() {
        if (!$this->userId) {
            return ['success' => false, 'message' => 'User not logged in'];
        }
        
        $inventory = $this->db->fetchAll(
            "SELECT ui.*, si.* 
            FROM user_inventory ui
            JOIN special_items si ON ui.item_id = si.item_id
            WHERE ui.user_id = :user_id",
            [':user_id' => $this->userId]
        );
        
        return [
            'success' => true,
            'inventory' => $inventory
        ];
    }
    
    public function addItemToUser($itemId, $quantity = 1) {
        if (!$this->userId) {
            return ['success' => false, 'message' => 'User not logged in'];
        }
        
        // Check if item exists
        $item = $this->db->fetch(
            "SELECT * FROM special_items WHERE item_id = :item_id",
            [':item_id' => $itemId]
        );
        
        if (!$item) {
            return ['success' => false, 'message' => 'Item does not exist'];
        }
        
        // Check if user already has this item
        $existingItem = $this->db->fetch(
            "SELECT * FROM user_inventory WHERE user_id = :user_id AND item_id = :item_id",
            [':user_id' => $this->userId, ':item_id' => $itemId]
        );
        
        if ($existingItem) {
            // Update quantity
            $this->db->update(
                'user_inventory',
                ['quantity' => $existingItem['quantity'] + $quantity],
                'user_id = :user_id AND item_id = :item_id',
                [':user_id' => $this->userId, ':item_id' => $itemId]
            );
        } else {
            // Add new item to inventory
            $this->db->insert('user_inventory', [
                'user_id' => $this->userId,
                'item_id' => $itemId,
                'quantity' => $quantity
            ]);
        }
        
        return [
            'success' => true,
            'item' => $item,
            'quantity' => ($existingItem ? $existingItem['quantity'] + $quantity : $quantity)
        ];
    }
    
    public function useItem($itemId) {
        if (!$this->userId) {
            return ['success' => false, 'message' => 'User not logged in'];
        }
        
        // Check if user has this item
        $userItem = $this->db->fetch(
            "SELECT * FROM user_inventory WHERE user_id = :user_id AND item_id = :item_id",
            [':user_id' => $this->userId, ':item_id' => $itemId]
        );
        
        if (!$userItem || $userItem['quantity'] <= 0) {
            return ['success' => false, 'message' => 'Item not in inventory'];
        }
        
        // Get item details
        $item = $this->db->fetch(
            "SELECT * FROM special_items WHERE item_id = :item_id",
            [':item_id' => $itemId]
        );
        
        // Apply item effect (this would depend on the item type)
        // This is a placeholder for item effects
        $effectResult = $this->applyItemEffect($item);
        
        // Decrement quantity
        $this->db->update(
            'user_inventory',
            ['quantity' => $userItem['quantity'] - 1],
            'user_id = :user_id AND item_id = :item_id',
            [':user_id' => $this->userId, ':item_id' => $itemId]
        );
        
        // If quantity is 0, remove from inventory
        if ($userItem['quantity'] - 1 <= 0) {
            $this->db->delete(
                'user_inventory',
                'user_id = :user_id AND item_id = :item_id',
                [':user_id' => $this->userId, ':item_id' => $itemId]
            );
        }
        
        return [
            'success' => true,
            'item' => $item,
            'effect_result' => $effectResult,
            'remaining_quantity' => $userItem['quantity'] - 1
        ];
    }
    
    private function applyItemEffect($item) {
        // This is a placeholder for item effects
        // Would be implemented based on item types
        
        switch ($item['item_type']) {
            case 'boost':
                // Apply boost effects
                return ['boost_applied' => true, 'message' => 'Boost activated!'];
                
            case 'rune_lore':
                // Unlock rune lore
                return ['lore_unlocked' => true, 'message' => 'New lore unlocked!'];
                
            case 'decoration':
            case 'plant':
                // These are passive items for garden decoration
                return ['message' => 'Item is now in your garden!'];
                
            default:
                return ['message' => 'Item used!'];
        }
    }
}
