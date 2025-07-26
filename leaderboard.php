
<?php
// leaderboard.php - Leaderboard page
require_once 'config/config.php';

$user = new User();
$isLoggedIn = $user->isLoggedIn();
$userData = $isLoggedIn ? $user->getCurrentUser() : null;

$leaderboard = new Leaderboard();
$dailyLeaderboard = $leaderboard->getDailyLeaderboard();
$weeklyLeaderboard = $leaderboard->getWeeklyLeaderboard();
$allTimeLeaderboard = $leaderboard->getAllTimeLeaderboard();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rune Match - Leaderboard</title>
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
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="leaderboard.php">Leaderboard</a>
                    </li>
                </ul>
                <?php if ($isLoggedIn): ?>
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
                <?php else: ?>
                <a href="index.php" class="btn btn-outline-light">Login / Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <div class="container py-4">
        <h1 class="mb-4">Leaderboard</h1>
        
        <ul class="nav nav-tabs mb-4" id="leaderboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily" type="button" role="tab">Daily</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="weekly-tab" data-bs-toggle="tab" data-bs-target="#weekly" type="button" role="tab">Weekly</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="all-time-tab" data-bs-toggle="tab" data-bs-target="#all-time" type="button" role="tab">All Time</button>
            </li>
            <?php if ($isLoggedIn): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="friends-tab" data-bs-toggle="tab" data-bs-target="#friends" type="button" role="tab">Friends</button>
            </li>
            <?php endif; ?>
        </ul>
        
        <div class="tab-content" id="leaderboardTabsContent">
            <div class="tab-pane fade show active" id="daily" role="tabpanel">
                <div class="card bg-dark border-light">
                    <div class="card-header">
                        <h5 class="mb-0">Today's Top Players</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-dark table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Player</th>
                                    <th scope="col">Score</th>
                                    <th scope="col">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($dailyLeaderboard) > 0): ?>
                                    <?php foreach ($dailyLeaderboard as $index => $entry): ?>
                                        <tr<?php echo ($isLoggedIn && $entry['username'] == $userData['username']) ? ' class="table-primary"' : ''; ?>>
                                            <th scope="row"><?php echo $index + 1; ?></th>
                                            <td><?php echo htmlspecialchars($entry['username']); ?></td>
                                            <td><?php echo $entry['score']; ?></td>
                                            <td><?php echo date('H:i', strtotime($entry['recorded_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No scores recorded today</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="weekly" role="tabpanel">
                <div class="card bg-dark border-light">
                    <div class="card-header">
                        <h5 class="mb-0">This Week's Top Players</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-dark table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Player</th>
                                    <th scope="col">Score</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($weeklyLeaderboard) > 0): ?>
                                    <?php foreach ($weeklyLeaderboard as $index => $entry): ?>
                                        <tr<?php echo ($isLoggedIn && $entry['username'] == $userData['username']) ? ' class="table-primary"' : ''; ?>>
                                            <th scope="row"><?php echo $index + 1; ?></th>
                                            <td><?php echo htmlspecialchars($entry['username']); ?></td>
                                            <td><?php echo $entry['score']; ?></td>
                                            <td><?php echo date('D, H:i', strtotime($entry['recorded_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No scores recorded this week</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="all-time" role="tabpanel">
                <div class="card bg-dark border-light">
                    <div class="card-header">
                        <h5 class="mb-0">All Time Top Players</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-dark table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Player</th>
                                    <th scope="col">Score</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($allTimeLeaderboard) > 0): ?>
                                    <?php foreach ($allTimeLeaderboard as $index => $entry): ?>
                                        <tr<?php echo ($isLoggedIn && $entry['username'] == $userData['username']) ? ' class="table-primary"' : ''; ?>>
                                            <th scope="row"><?php echo $index + 1; ?></th>
                                            <td><?php echo htmlspecialchars($entry['username']); ?></td>
                                            <td><?php echo $entry['score']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($entry['recorded_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No scores recorded yet</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <?php if ($isLoggedIn): ?>
            <div class="tab-pane fade" id="friends" role="tabpanel">
                <div class="card bg-dark border-light">
                    <div class="card-header">
                        <h5 class="mb-0">Friends Leaderboard</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-dark">
                            Friends feature coming soon!
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/api.js"></script>
</body>
</html>