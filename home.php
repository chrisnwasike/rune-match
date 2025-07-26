<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rune Match - Norse Puzzle Adventure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/home.css">
</head>
<body class="home-page">
    <div class="home-container">
        <!-- Animated Background -->
        <div class="northern-lights"></div>
        <div class="stars"></div>
        <div class="mountains"></div>
        
        <!-- Logo and Main Content -->
        <div class="content-container">
            <div class="game-logo">
                <img src="assets/images/logo.png" alt="Rune Match">
                <div class="logo-subtitle">A Norse Puzzle Adventure</div>
            </div>
            
            <div class="menu-container">
                <div class="main-menu">
                    <button id="play-btn" class="unthemed-button">
                        <span class="norse-btn-text">Play</span>
                    </button>

                    <button id="leaderboard-btn" class="unthemed-button">
                        <span class="norse-btn-text">Leaderboard</span>
                    </button>
                    
                    <button id="rune-lore-btn" class="unthemed-button">
                        <span class="norse-btn-text">Rune Lore</span>
                    </button>
                    
                    <button id="how-to-play-btn" class="unthemed-button">
                        <span class="norse-btn-text">How to Play</span>
                    </button>
                    
                    <button id="settings-btn" class="unthemed-button">
                        <span class="norse-btn-text">Settings</span>
                    </button>
                </div>
                
                <!-- <div class="menu-footer">
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="bi bi-github"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-linkedin"></i></a>
                    </div>
                    <div class="copyright">
                        © 2025 ChrisDBuilder · <a href="#" id="credits-link">Credits</a>
                    </div>
                </div> -->
            </div>
            
        </div>
    </div>
    
    <!-- How to Play Modal -->
    <div class="modal fade" id="howToPlayModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-light">
                    <h5 class="modal-title">How to Play</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="how-to-play-container">
                        <div class="gameplay-carousel">
                            <!-- Carousel slides with gameplay instructions -->
                            <div id="instructionCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-indicators">
                                    <button type="button" data-bs-target="#instructionCarousel" data-bs-slide-to="0" class="active"></button>
                                    <button type="button" data-bs-target="#instructionCarousel" data-bs-slide-to="1"></button>
                                    <button type="button" data-bs-target="#instructionCarousel" data-bs-slide-to="2"></button>
                                    <button type="button" data-bs-target="#instructionCarousel" data-bs-slide-to="3"></button>
                                </div>
                                
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <div class="instruction-slide">
                                            <div class="instruction-image">
                                                <img src="assets/images/tutorial/match-basic.png" alt="Basic Matching">
                                            </div>
                                            <div class="instruction-text">
                                                <h3>Basic Matching</h3>
                                                <p>Swap adjacent runes to create matches of 3 or more identical runes. Matched runes will disappear, and new ones will fall from above.</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="carousel-item">
                                        <div class="instruction-slide">
                                            <div class="instruction-image">
                                                <img src="assets/images/tutorial/special-pieces.png" alt="Special Pieces">
                                            </div>
                                            <div class="instruction-text">
                                                <h3>Special Pieces</h3>
                                                <p>Match 4 or more runes to create special pieces with powerful effects. Thor's Hammer clears an entire row, while Odin's Ravens clear all runes of one type.</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="carousel-item">
                                        <div class="instruction-slide">
                                            <div class="instruction-image">
                                                <img src="assets/images/tutorial/combos.png" alt="Combos">
                                            </div>
                                            <div class="instruction-text">
                                                <h3>Combos</h3>
                                                <p>When matches cascade into more matches, you earn combo multipliers. Chain multiple matches quickly to trigger Combo Fever for double points!</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="carousel-item">
                                        <div class="instruction-slide">
                                            <div class="instruction-image">
                                                <img src="assets/images/tutorial/challenges.png" alt="Challenges">
                                            </div>
                                            <div class="instruction-text">
                                                <h3>Daily Challenges</h3>
                                                <p>Complete daily challenges to earn special rewards. Play consecutive days to increase your rewards and unlock rare items for your collection.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <button class="carousel-control-prev" type="button" data-bs-target="#instructionCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#instructionCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="special-pieces-guide">
                            <h4>Special Pieces</h4>
                            <div class="special-piece-list">
                                <div class="special-piece-item">
                                    <img src="assets/images/items/thors_hammer.png" alt="Thor's Hammer">
                                    <div class="special-piece-info">
                                        <h5>Thor's Hammer</h5>
                                        <p>Clears an entire row</p>
                                    </div>
                                </div>
                                
                                <div class="special-piece-item">
                                    <img src="assets/images/items/odins_ravens.png" alt="Odin's Ravens">
                                    <div class="special-piece-info">
                                        <h5>Odin's Ravens</h5>
                                        <p>Clears all runes of one type</p>
                                    </div>
                                </div>
                                
                                <div class="special-piece-item">
                                    <img src="assets/images/items/yggdrasil.png" alt="Yggdrasil">
                                    <div class="special-piece-info">
                                        <h5>Yggdrasil</h5>
                                        <p>Clears a 3×3 area</p>
                                    </div>
                                </div>
                                
                                <div class="special-piece-item">
                                    <img src="assets/images/items/heimdall.png" alt="Heimdall">
                                    <div class="special-piece-info">
                                        <h5>Heimdall</h5>
                                        <p>Converts nearby runes to same type</p>
                                    </div>
                                </div>
                                
                                <div class="special-piece-item">
                                    <img src="assets/images/items/rainbow_rune.png" alt="Rainbow Rune">
                                    <div class="special-piece-info">
                                        <h5>Rainbow Rune</h5>
                                        <p>Can match with any rune type</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="controls-guide">
                            <h4>Controls</h4>
                            <div class="controls-list">
                                <div class="control-item">
                                    <div class="control-icon">🖱️</div>
                                    <div class="control-description">Click on a rune, then click on an adjacent rune to swap</div>
                                </div>
                                
                                <div class="control-item">
                                    <div class="control-icon">⏸️</div>
                                    <div class="control-description">Press ESC or the pause button to pause the game</div>
                                </div>
                                
                                <div class="control-item">
                                    <div class="control-icon">🔄</div>
                                    <div class="control-description">If no moves are available, the board will automatically shuffle</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn unthemed-button btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn unthemed-button btn-primary" id="play-tutorial-btn">Play Tutorial</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Rune Lore Modal -->
    <div class="modal fade" id="runeLoreModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-light">
                    <h5 class="modal-title">Rune Lore</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="rune-lore-container">
                        <div class="rune-categories">
                            <button class="rune-category active" data-category="all">All Runes</button>
                            <button class="rune-category" data-category="common">Common</button>
                            <button class="rune-category" data-category="special">Special</button>
                            <button class="rune-category" data-category="discovered">Discovered</button>
                        </div>
                        
                        <div class="rune-grid">
                            <!-- Example runes, would be dynamically generated based on player progress -->
                            <div class="rune-item" data-rune="fehu">
                                <div class="rune-image">
                                    <img src="assets/images/runes/fehu.png" alt="Fehu">
                                </div>
                                <div class="rune-name">Fehu</div>
                            </div>
                            
                            <div class="rune-item" data-rune="uruz">
                                <div class="rune-image">
                                    <img src="assets/images/runes/uruz.png" alt="Uruz">
                                </div>
                                <div class="rune-name">Uruz</div>
                            </div>
                            
                            <div class="rune-item" data-rune="thurisaz">
                                <div class="rune-image">
                                    <img src="assets/images/runes/thurisaz.png" alt="Thurisaz">
                                </div>
                                <div class="rune-name">Thurisaz</div>
                            </div>
                            
                            <div class="rune-item" data-rune="ansuz">
                                <div class="rune-image">
                                    <img src="assets/images/runes/ansuz.png" alt="Ansuz">
                                </div>
                                <div class="rune-name">Ansuz</div>
                            </div>
                            
                            <div class="rune-item" data-rune="thors_hammer">
                                <div class="rune-image special-rune">
                                    <img src="assets/images/items/thors_hammer.png" alt="Thor's Hammer">
                                </div>
                                <div class="rune-name">Thor's Hammer</div>
                            </div>
                            
                            <!-- More runes would be added here -->
                            
                            <!-- Locked/undiscovered runes -->
                            <div class="rune-item locked" data-rune="locked">
                                <div class="rune-image">
                                    <div class="locked-overlay">
                                        <i class="bi bi-lock-fill"></i>
                                    </div>
                                </div>
                                <div class="rune-name">???</div>
                            </div>
                        </div>
                        
                        <div class="rune-detail">
                            <h3 class="rune-detail-title">Select a rune to view its lore</h3>
                            <div class="rune-detail-content">
                                <p class="text-center text-muted">The wisdom of the ancients awaits...</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn unthemed-button btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Leaderboard Modal -->
    <div class="modal fade" id="leaderboardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-light">
                    <h5 class="modal-title">Leaderboard</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="leaderboard-container">
                        <div class="leaderboard-tabs">
                            <button class="leaderboard-tab active" data-period="daily">Today</button>
                            <button class="leaderboard-tab" data-period="weekly">This Week</button>
                            <button class="leaderboard-tab" data-period="alltime">All Time</button>
                            <button class="leaderboard-tab" data-period="friends">Friends</button>
                        </div>
                        
                        <div class="leaderboard-table-container">
                            <table class="leaderboard-table">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Player</th>
                                        <th>Score</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Example entries, would be dynamically loaded -->
                                    <tr class="highlighted-player">
                                        <td>1</td>
                                        <td>
                                            <div class="player-entry">
                                                <img src="assets/images/ui/default-avatar.png" alt="Avatar" class="player-avatar-small">
                                                <span>RuneMaster92</span>
                                            </div>
                                        </td>
                                        <td>15,750</td>
                                        <td>Today, 10:30</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>
                                            <div class="player-entry">
                                                <img src="assets/images/ui/default-avatar.png" alt="Avatar" class="player-avatar-small">
                                                <span>OdinsFriend</span>
                                            </div>
                                        </td>
                                        <td>12,480</td>
                                        <td>Today, 14:15</td>
                                    </tr>
                                    <!-- More entries would be dynamically loaded -->
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="your-rank">
                            <div class="rank-label">Your Best Rank</div>
                            <div class="rank-value">#42</div>
                            <div class="rank-score">8,930 points</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn unthemed-button btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn unthemed-button btn-primary" id="play-beat-score-btn">Play & Beat Score</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Settings Modal -->
    <div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-light">
                    <h5 class="modal-title">Settings</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="settings-container">
                        <div class="setting-group">
                            <h5>Audio</h5>
                            <div class="setting-item">
                                <div class="setting-label">Sound Effects</div>
                                <div class="setting-control">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="soundEffectsToggle" checked>
                                        <label class="form-check-label" for="soundEffectsToggle"></label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="setting-item">
                                <div class="setting-label">Music</div>
                                <div class="setting-control">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="musicToggle" checked>
                                        <label class="form-check-label" for="musicToggle"></label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="setting-item">
                                <div class="setting-label">Volume</div>
                                <div class="setting-control">
                                    <input type="range" class="form-range" min="0" max="100" value="80" id="volumeSlider">
                                </div>
                            </div>
                        </div>
                        
                        <div class="setting-group">
                            <h5>Display</h5>
                            <div class="setting-item">
                                <div class="setting-label">Animations</div>
                                <div class="setting-control">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="animationsToggle" checked>
                                        <label class="form-check-label" for="animationsToggle"></label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="setting-item">
                                <div class="setting-label">Background Effects</div>
                                <div class="setting-control">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="backgroundEffectsToggle" checked>
                                        <label class="form-check-label" for="backgroundEffectsToggle"></label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="setting-item">
                                <div class="setting-label">Colorblind Mode</div>
                                <div class="setting-control">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="colorblindToggle">
                                        <label class="form-check-label" for="colorblindToggle"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="setting-group">
                            <h5>Account</h5>
                            <div class="setting-item">
                                <div class="setting-label">Username</div>
                                <div class="setting-control">
                                    <input type="text" class="form-control" id="usernameInput" value="Traveler">
                                </div>
                            </div>
                            
                            <div class="setting-item">
                                <div class="setting-label">Profile Picture</div>
                                <div class="setting-control">
                                    <button class="btn unthemed-button btn-outline-light btn-sm" id="changeAvatarBtn">Change</button>
                                </div>
                            </div>
                            
                            <div class="setting-item">
                                <div class="setting-label">Clear Data</div>
                                <div class="setting-control">
                                    <button class="btn unthemed-button btn-outline-danger btn-sm" id="clearDataBtn">Reset Progress</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn unthemed-button btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn unthemed-button btn-primary" id="save-settings-btn">Save Settings</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Credits Modal -->
    <div class="modal fade" id="creditsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-light">
                    <h5 class="modal-title">Credits</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="credits-container">
                        <div class="credit-section">
                            <h5>Development</h5>
                            <p>Created by Your Name</p>
                            <p>Built with HTML, CSS, JavaScript, PHP, and MySQL</p>
                        </div>
                        
                        <div class="credit-section">
                            <h5>Artwork</h5>
                            <p>Rune designs inspired by the Elder Futhark</p>
                            <p>Background illustrations by Your Name</p>
                        </div>
                        
                        <div class="credit-section">
                            <h5>Sound</h5>
                            <p>Music and sound effects from [Source]</p>
                        </div>
                        
                        <div class="credit-section">
                            <h5>Special Thanks</h5>
                            <p>To everyone who followed along during the public development</p>
                            <p>To all playtesters who provided valuable feedback</p>
                        </div>
                        
                        <div class="credit-section">
                            <h5>Open Source Libraries</h5>
                            <p>Bootstrap 5, jQuery, and [other libraries]</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn unthemed-button btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/home.js"></script>
</body>
</html>