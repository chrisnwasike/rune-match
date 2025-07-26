
<?php
// profile.php - User profile page
require_once 'config/config.php';

$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$userData = $user->getCurrentUser();
$item = new Item();
$inventory = $item->getUserInventory();
$leaderboard = new Leaderboard();
$rank = $leaderboard->getUserRank($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rune Match - Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-dark text-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-light">
        <div class="container">
            <a class="navbar-brand" href="#">Rune Match</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="game.php">Play</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="leaderboard.php">Leaderboard</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="badge bg-warning text-dark me-1"><?php echo $userData['total_coins']; ?></span>
                        <span>Coins</span>
                    </div>
                    <div class="me-3">
                        <span class="badge bg-success me-1"><?php echo $userData['total_seeds']; ?></span>
                        <span>Seeds</span>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            <?php echo htmlspecialchars($userData['username']); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><a class="dropdown-item" href="#" id="logout-btn">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="container py-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-dark border-light mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Player Info</h5>
                    </div>
                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($userData['username']); ?></h3>
                        <p class="lead mb-4">Rank: #<?php echo $rank ?: 'N/A'; ?></p>
                        
                        <div class="mb-3">
                            <strong>Games Played:</strong> <?php echo $userData['games_played']; ?>
                        </div>
                        <div class="mb-3">
                            <strong>Highest Score:</strong> <?php echo $userData['highest_score']; ?>
                        </div>
                        <div class="mb-3">
                            <strong>Total Score:</strong> <?php echo $userData['total_score']; ?>
                        </div>
                        <div class="mb-3">
                            <strong>Consecutive Days:</strong> <?php echo $userData['consecutive_days']; ?>
                        </div>
                        <div>
                            <strong>Total Playtime:</strong> <?php echo floor($userData['total_playtime'] / 60); ?> minutes
                        </div>
                    </div>
                </div>
                
                <div class="card bg-dark border-light">
                    <div class="card-header">
                        <h5 class="mb-0">Resources</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>Coins:</h5>
                            <h5><span class="badge bg-warning text-dark"><?php echo $userData['total_coins']; ?></span></h5>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Seeds:</h5>
                            <h5><span class="badge bg-success"><?php echo $userData['total_seeds']; ?></span></h5>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card bg-dark border-light">
                    <div class="card-header">
                        <h5 class="mb-0">Inventory</h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="inventoryTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">All</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="decorations-tab" data-bs-toggle="tab" data-bs-target="#decorations" type="button" role="tab">Decorations</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="plants-tab" data-bs-toggle="tab" data-bs-target="#plants" type="button" role="tab">Plants</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="lore-tab" data-bs-toggle="tab" data-bs-target="#lore" type="button" role="tab">Rune Lore</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="boosts-tab" data-bs-toggle="tab" data-bs-target="#boosts" type="button" role="tab">Boosts</button>
                            </li>
                        </ul>
                        <div class="tab-content p-3" id="inventoryTabsContent">
                            <div class="tab-pane fade show active" id="all" role="tabpanel">
                                <?php if (count($inventory['inventory']) > 0): ?>
                                    <div class="row">
                                        <?php foreach ($inventory['inventory'] as $item): ?>
                                            <div class="col-md-4 col-6 mb-3">
                                                <div class="card bg-dark border-light">
                                                    <img src="<?php echo $item['image_path']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                                                    <div class="card-body">
                                                        <h6 class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></h6>
                                                        <p class="card-text small"><?php echo htmlspecialchars($item['item_description']); ?></p>
                                                        <div class="d-flex justify-content-between">
                                                            <span class="badge bg-<?php 
                                                                switch($item['rarity']) {
                                                                    case 'common': echo 'secondary'; break;
                                                                    case 'uncommon': echo 'info'; break;
                                                                    case 'rare': echo 'primary'; break;
                                                                    case 'legendary': echo 'warning text-dark'; break;
                                                                }
                                                            ?>"><?php echo ucfirst($item['rarity']); ?></span>
                                                            <span>x<?php echo $item['quantity']; ?></span>
                                                        </div>
                                                        <?php if ($item['item_type'] == 'boost'): ?>
                                                            <button class="btn btn-sm btn-primary mt-2 use-item" data-item-id="<?php echo $item['item_id']; ?>">Use</button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-dark">
                                        No items in your inventory yet. Play more games to earn special items!
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Other tabs with filtered items by type -->
                            <div class="tab-pane fade" id="decorations" role="tabpanel">
                                <!-- Decorations -->
                            </div>
                            <div class="tab-pane fade" id="plants" role="tabpanel">
                                <!-- Plants -->
                            </div>
                            <div class="tab-pane fade" id="lore" role="tabpanel">
                                <!-- Rune Lore -->
                            </div>
                            <div class="tab-pane fade" id="boosts" role="tabpanel">
                                <!-- Boosts -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/profile.js"></script>
    <script src="assets/js/api.js"></script>
</body>
</html>
