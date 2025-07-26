
<?php
// game.php - Game page
// require_once 'config/config.php';

// $user = new User();
// if (!$user->isLoggedIn()) {
//     header('Location: index.php');
//     exit;
// }

// $userData = $user->getCurrentUser();
// $challenge = new Challenge();
// $dailyChallenges = $challenge->getDailyChallenges();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rune Match - Play</title>
    <link href="./vendors/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-dark text-light" style="
    background-size: cover; 
    height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background-attachment: fixed;">
    
    <!-- <div class="paused-screen"></div> -->
    <div class="container py-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card card_box left mb-4">
                    <div>
                        <div class="card-title">TIME</div>
                        <div id="game-timer" class="badge">0:00</div>

                        <div class="card-title">MATCHES</div>
                        <div id="runes-matched" class="badge">0</div>

                        <div class="card-title">SCORE</div>
                        <div id="game-score" class="badge">0</div>
                    </div>
                    <div class="card-footer text-center">
                        <button class="btn" title="Pause Game (ESC)" id="pause-game">Pause (ESC)</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card game-holder">
                <div class="dots_border"></div>
                    <div class="card-body p-0">
                        <div id="game-board" class="game-board"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card card_box right mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Special Items</h5>
                    </div>
                    
                    <div class="specials-items-holder">
                        <div class="special-items">
                            <div class="special-rune-holder thors_hammer">
                                <div class="special-rune" data-special-type="thors_hammer">
                                    <img src="assets/images/items/thors_hammer.png" alt="Thor's Hammer" class="special-rune-img">
                                </div>
                                <div class="special-rune-counter">0</div>
                            </div>
                            <div class="special-rune-holder odins_ravens">
                                <div class="special-rune" data-special-type="odins_ravens">
                                    <img src="assets/images/items/odins_ravens.png" alt="Odin's Ravens" class="special-rune-img">
                                </div>
                                <div class="special-rune-counter">0</div>
                            </div>
                            <div class="special-rune-holder heimdalls_horn">
                                <div class="special-rune" data-special-type="heimdall">
                                    <img src="assets/images/items/heimdall.png" alt="Heimdall's Horn" class="special-rune-img">
                                </div>
                                <div class="special-rune-counter">0</div>
                            </div>
                            <div class="special-rune-holder yggdrasil">
                                <div class="special-rune" data-special-type="yggdrasil">
                                    <img src="assets/images/items/yggdrasil.png" alt="Yggdrasil" class="special-rune-img">
                                </div>
                                <div class="special-rune-counter">0</div>
                            </div>
                        </div>

                        <div class="special-items-nfts">

                        </div>
                    </div>



                    <!-- <div class="card-body p-0">
                        <ul class="list-group list-group-flush challenges-list">
                            <?php foreach ($dailyChallenges as $challenge): ?>
                                <li class="list-group-item bg-dark text-light border-light <?php echo $challenge['completed'] ? 'completed' : ''; ?>">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($challenge['description']); ?></h6>
                                            <small>
                                                Reward: 
                                                <?php if ($challenge['reward_type'] == 'coins'): ?>
                                                    <span class="text-warning"><?php echo $challenge['reward_amount']; ?> Coins</span>
                                                <?php elseif ($challenge['reward_type'] == 'seeds'): ?>
                                                    <span class="text-success"><?php echo $challenge['reward_amount']; ?> Seeds</span>
                                                <?php else: ?>
                                                    <span class="text-info">Special Item</span>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <?php if ($challenge['completed']): ?>
                                            <span class="badge bg-success">Completed</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?php echo $challenge['target_score']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div> -->
                </div>
                
                <!-- <div class="card bg-dark border-light">
                    <div class="card-header">
                        <h5 class="mb-0">Game Instructions</h5>
                    </div>
                    <div class="card-body">
                        <p>Match 3 or more identical Norse runes by swapping adjacent pieces.</p>
                        <ul>
                            <li>Click on one rune, then click on an adjacent rune to swap them.</li>
                            <li>Match 3 or more identical runes to clear them.</li>
                            <li>Special combinations create power-ups with Norse-themed effects.</li>
                            <li>Earn points to convert directly to garden resources.</li>
                        </ul>
                        <p><strong>Special Pieces:</strong></p>
                        <ul>
                            <li><strong>Thor's Hammer</strong>: Clears an entire row</li>
                            <li><strong>Odin's Ravens</strong>: Clears all runes of one type</li>
                            <li><strong>Yggdrasil</strong>: Clears a 3x3 area</li>
                        </ul>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
    
    <!-- Game Results Modal -->
    <div class="modal fade" id="gameResultsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Game Results</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
 
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 id="coins-earned">0</h4>
                            <p>Runes Matched</p>
                        </div>
                        <div class="col-6">
                            <h4 id="final-score">0</h4>
                            <p>Total Points</p>
                        </div>
                    </div>
                    <div class="text-center mb-4">
                        <h2 id="seeds-earned">0</h2>
                        <p class="lead">Petals Earned</p>
                    </div>

                    <div id="special-items-earned" class="mt-3 d-none">
                        <h5>Special Items Earned:</h5>
                        <ul id="items-list" class="list-group list-group-flush"></ul>
                    </div>
                    <div id="challenges-completed" class="mt-3 d-none">
                        <h5>Challenges Completed:</h5>
                        <ul id="challenges-list" class="list-group list-group-flush"></ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn" id="play-again-btn">Play Again</button>
                </div>
            </div>
        </div>
    </div>
    


    <!-- Pause Screen Modal -->
    <div class="modal fade" id="pauseModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered">
            <div class="modal-content bg-dark text-light">
                <div class="modal-body d-flex flex-column justify-content-center">
                    <div class="container">
                        <div class="row justify-content-center mb-5">
                            <div class="col-md-6 text-center">
                                <h4 class="mb-5">Take a moment to reflect on your rune mastery</h4>
                                
                                <div class="d-grid gap-4">
                                    <button id="resume-game" class="btn">
                                        Resume Game
                                    </button>
                                    <button id="restart-game" class="btn">
                                        Restart Game
                                    </button>
                                    <button id="exit-game" class="btn">
                                        Exit to Menu
                                    </button>
                                </div>

                                <div class="soundbtns text-center">
                                    <p class="">Sound & Music</p>
                                    <div class="btn-group" role="group" aria-label="Sound Controls">
                                        <button id="sound-toggle" class="btn btn-small">
                                            <i class="fa-solid fa-volume-xmark"></i> Sounds
                                        </button>
                                        <button id="music-toggle" class="btn btn-small">
                                            <i class="fa-solid fa-volume-high"></i> Music
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card card_box mb-4">
                                    <div class="card-header">
                                        <h4 class="mb-0">Rune Master Tips</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div id="tip-carousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                                                    <div class="carousel-inner">
                                                        <div class="carousel-item active">
                                                            <h5>Match Strategy</h5>
                                                            <p>Focus on creating matches at the bottom of the board first. This creates cascading effects that can trigger combo chains.</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <h5>Special Pieces</h5>
                                                            <p>Create T and L shapes to generate powerful special pieces with unique effects. Thor's Hammer clears entire rows!</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <h5>Combo Fever</h5>
                                                            <p>Chain matches quickly to trigger Combo Fever mode. All points are doubled during this special state!</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <h5>Daily Challenges</h5>
                                                            <p>Complete daily challenges to earn special rewards for your garden. Check them before playing to focus your strategy.</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <h5>Rainbow Runes</h5>
                                                            <p>Rainbow Runes can match with any rune type. Use them strategically to clear difficult board positions.</p>
                                                        </div>
                                                    </div>
                                                    <button class="carousel-control-prev" type="button" data-bs-target="#tip-carousel" data-bs-slide="prev">
                                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                        <span class="visually-hidden">Previous</span>
                                                    </button>
                                                    <button class="carousel-control-next" type="button" data-bs-target="#tip-carousel" data-bs-slide="next">
                                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                        <span class="visually-hidden">Next</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h5>Special Piece Effects</h5>
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item tip-item d-flex align-items-center">
                                                        <img src="assets/images/items/thors_hammer.png" alt="Thor's Hammer" class="me-3" style="width: 40px; height: 40px;">
                                                        <div>
                                                            <strong>Thor's Hammer</strong>
                                                            <small class="d-block">Clears an entire row</small>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item tip-item d-flex align-items-center">
                                                        <img src="assets/images/items/odins_ravens.png" alt="Odin's Ravens" class="me-3" style="width: 40px; height: 40px;">
                                                        <div>
                                                            <strong>Odin's Ravens</strong>
                                                            <small class="d-block">Clears all runes of one type</small>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item tip-item d-flex align-items-center">
                                                        <img src="assets/images/items/yggdrasil.png" alt="Yggdrasil" class="me-3" style="width: 40px; height: 40px;">
                                                        <div>
                                                            <strong>Yggdrasil</strong>
                                                            <small class="d-block">Clears a 3x3 area</small>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="effects-container"></div>
    <div id="loading-container">
        <div class="loading-spinner">
            <div class="spinner-rune">
            <div class="runeblock"></div>
            <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <path d="M50 15 L65 35 L50 85 L35 35 Z" fill="none" stroke="#4b2315" stroke-width="2" style="z-index: 2; filter: drop-shadow(0 0 2px rgb(255, 166, 0))">
                <animate attributeName="stroke-dasharray" from="0,300" to="300,0" dur="2s" repeatCount="indefinite" />
                </path>
                <circle cx="50" cy="50" r="30" fill="none" stroke="#4b2315" stroke-width="2" stroke-dasharray="188.5" style="z-index: 2;  filter: drop-shadow(0px 0px 2px #e2bfb2)">
                <animateTransform attributeName="transform" type="" from="0 50 50" to="360 50 50" dur="3s" repeatCount="indefinite" />
                </circle>
            </svg>
            </div>
            <p class="loading-text">Loading Rune Match Assets...</p>
        </div>

        <div class="countdown-content d-none">
            <div class="countdown-number">3</div>
            <div class="rune-circle">
            <svg viewBox="0 0 200 200" class="rune-circle-svg">
                <circle cx="100" cy="100" r="90" class="circle-bg" />
                <circle cx="100" cy="100" r="90" class="circle-progress" />
            </svg>
            <div class="rune-symbol" data-count="3">ᚠ</div>
            <div class="rune-symbol" data-count="2">ᚢ</div>
            <div class="rune-symbol" data-count="1">ᚦ</div>
            </div>
        </div>

        
    </div>

    <script src="./vendors/jquery/jquery-3.6.3.min.js"></script>
    <script src="./vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ethers/6.7.0/ethers.umd.min.js"></script>
    <script src="assets/js/api.js"></script>

    <!-- Game engine components (in correct dependency order) -->
    <script src="assets/js/game/config.js"></script>
    <script src="assets/js/game/rune.js"></script> <!-- Add the new Rune class first -->
    <script src="assets/js/game/special-effects.js"></script>
    <script src="assets/js/game/music.js"></script>
    <script src="assets/js/game/board2.js"></script>
    <script src="assets/js/game/match.js"></script>
    <script src="assets/js/game/score.js"></script>
    <script src="assets/js/game/timer.js"></script>
    <script src="assets/js/game/plugins/cursedRunesPlugin.js"></script>
    <script src="assets/js/game/plugins/elementalCruciblePlugin.js"></script>
    <script src="assets/js/game/animation.js"></script>
    <script src="assets/js/game/sound.js"></script>
    <script src="assets/js/game/pause.js"></script>
    <script src="assets/js/game/main.js"></script>
</body>
</html>
