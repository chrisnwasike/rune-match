<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Norse Realms Map</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --midgard-color: #5d8b5e;
            --niflheim-color: #a8d0db;
            --muspelheim-color: #d94e4e;
            --jotunheim-color: #7e6b8f;
            --yggdrasil-color: #c3b59f;
        }

        body {
            background-color: #1e1e24;
            color: #f4f4f6;
            font-family: 'Times New Roman', serif;
        }

        .map-container {
            position: relative;
            width: 100%;
            height: 100vh;
            margin: 0rem auto;
            background-image: url('/api/placeholder/800/600');
            background-size: cover;
            background-position: center;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        .map-path {
            position: absolute;
            top: 10%;
            left: 10%;
            width: 80%;
            height: 80%;
            z-index: 1;
        }

        .realm {
            position: absolute;
            width: 100px;
            height: 100px;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-weight: bold;
            text-align: center;
            cursor: pointer;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
            overflow: hidden;
            border: 3px solid rgba(255, 255, 255, 0.8);
            opacity: 1;
        }

        .realm.active {
            opacity: 1;
            transform: scale(1.2);
            z-index: 10;
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.8);
        }

        .realm.locked {
            filter: grayscale(100%);
            opacity: 1;
            cursor: not-allowed;
        }

        .realm.unlocked {
            animation: pulse 2s infinite;
        }

        .realm-icon {
            font-size: 2.5rem;
            margin-bottom: 0.25rem;
        }

        /* Positions for each realm */
        #midgard {
            bottom: 10%;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--midgard-color);
        }

        #niflheim {
            bottom: 35%;
            left: 20%;
            background-color: var(--niflheim-color);
        }

        #muspelheim {
            bottom: 50%;
            right: 20%;
            background-color: var(--muspelheim-color);
        }

        #jotunheim {
            bottom: 60%;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--jotunheim-color);
        }

        #yggdrasil {
            top: 5%;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--yggdrasil-color);
        }

        .path-line {
            position: absolute;
            background-color: rgba(255, 255, 255, 0.3);
            transform-origin: bottom center;
            z-index: 0;
            border-radius: 10px;
        }

        .progress-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            background-color: rgba(255, 215, 0, 0.4);
            width: 100%;
            height: 0%;
            transition: height 0.5s ease;
        }

        .path-midgard-niflheim {
            width: 6px;
            height: 52%;
            bottom: 18%;
            left: 48%;
            transform: rotate(-67deg);
        }

        .path-midgard-muspelheim {
            width: 6px;
            height: 60%;
            bottom: 18%;
            right: 48%;
            transform: rotate(53deg);
        }

        .path-muspelheim-jotunheim {
            width: 6px;
            height: 47%;
            bottom: 56%;
            right: 25%;
            transform: rotate(-78deg);
        }

        .path-niflheim-jotunheim {
            width: 6px;
            height: 51%;
            bottom: 42%;
            left: 25%;
            transform: rotate(65deg);
        }

        .path-jotunheim-yggdrasil {
            width: 6px;
            height: 13%;
            bottom: 71%;
            left: 50%;
            transform: translateX(-50%);
        }

        .realm-details {
            background-color: rgba(30, 30, 36, 0.9);
            border-radius: 10px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            padding: 1.5rem;
            margin-top: 1rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .score-display {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 1.2rem;
            z-index: 100;
        }

        .level-badges {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .level-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
            text-align: center;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
            }
            70% {
                box-shadow: 0 0 0 15px rgba(255, 255, 255, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }

        /* Bootstrap Overrides */
        .btn-realm {
            color: white;
            border: none;
            padding: 10px 25px;
            font-weight: bold;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-midgard {
            background-color: var(--midgard-color);
        }

        .btn-niflheim {
            background-color: var(--niflheim-color);
        }

        .btn-muspelheim {
            background-color: var(--muspelheim-color);
        }

        .btn-jotunheim {
            background-color: var(--jotunheim-color);
        }

        .btn-yggdrasil {
            background-color: var(--yggdrasil-color);
        }

        .modal-content {
            background-color: #1e1e24;
            color: #f4f4f6;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        
        <div class="score-display">
            Score: <span id="player-score">0</span> / <span id="max-score">5000</span>
        </div>
        
        <div class="map-container">
            <h1 class="text-center mb-4">Norse Realms</h1>
            <!-- Path Lines -->
            <div class="path-line path-midgard-niflheim"><div class="progress-overlay" id="progress-midgard-niflheim"></div></div>
            <div class="path-line path-midgard-muspelheim"><div class="progress-overlay" id="progress-midgard-muspelheim"></div></div>
            <div class="path-line path-niflheim-jotunheim"><div class="progress-overlay" id="progress-niflheim-jotunheim"></div></div>
            <div class="path-line path-muspelheim-jotunheim"><div class="progress-overlay" id="progress-muspelheim-jotunheim"></div></div>
            <div class="path-line path-jotunheim-yggdrasil"><div class="progress-overlay" id="progress-jotunheim-yggdrasil"></div></div>
            
            <!-- Realms -->
            <div class="realm active" id="midgard" data-min-score="0">
                <div>
                    <div class="realm-icon">🏡</div>
                    Midgard
                </div>
            </div>
            
            <div class="realm locked" id="niflheim" data-min-score="1000">
                <div>
                    <div class="realm-icon">❄️</div>
                    Niflheim
                </div>
            </div>
            
            <div class="realm locked" id="muspelheim" data-min-score="1000">
                <div>
                    <div class="realm-icon">🔥</div>
                    Muspelheim
                </div>
            </div>
            
            <div class="realm locked" id="jotunheim" data-min-score="2500">
                <div>
                    <div class="realm-icon">⛰️</div>
                    Jotunheim
                </div>
            </div>
            
            <div class="realm locked" id="yggdrasil" data-min-score="4000">
                <div>
                    <div class="realm-icon">🌳</div>
                    Yggdrasil
                </div>
            </div>
        </div>
        
        <div class="realm-details">
            <h3 id="detail-title">Midgard Village</h3>
            <p id="detail-description">Welcome to Midgard, the realm of humans. This rustic Norse settlement features wooden longhouses, a central mead hall, and training grounds. Here you'll find a marketplace, blacksmith, rune stones, and fishing docks.</p>
            
            <h5>Realm Challenges:</h5>
            <p id="detail-mechanics">Resource Management: Collect resources through matches to build and upgrade village structures. Complete quests from villagers that require specific match combinations. Trade matched runes at the marketplace. Gameplay changes based on Norse seasonal festivals.</p>
            
            <div class="level-badges">
                <div class="level-badge" style="background-color: var(--midgard-color);">Level 1-5</div>
                <div class="level-badge" style="background-color: var(--niflheim-color);">Level 6-10</div>
                <div class="level-badge" style="background-color: var(--muspelheim-color);">Level 11-15</div>
                <div class="level-badge" style="background-color: var(--jotunheim-color);">Level 16-20</div>
                <div class="level-badge" style="background-color: var(--yggdrasil-color);">Level 21+</div>
            </div>
            
            <div class="text-center">
                <button class="btn btn-realm btn-midgard" id="play-button">Play Midgard</button>
            </div>
        </div>
    </div>
    
    <!-- Travel Modal -->
    <div class="modal fade" id="travelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Travel to New Realm</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="travel-message">You've unlocked a new realm! Would you like to travel there now?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Stay Here</button>
                    <button type="button" class="btn btn-primary" id="confirm-travel">Travel Now</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Realm data
        const realms = {
            'midgard': {
                name: 'Midgard Village',
                description: 'Welcome to Midgard, the realm of humans. This rustic Norse settlement features wooden longhouses, a central mead hall, and training grounds. Here you\'ll find a marketplace, blacksmith, rune stones, and fishing docks.',
                mechanics: 'Resource Management: Collect resources through matches to build and upgrade village structures. Complete quests from villagers that require specific match combinations. Trade matched runes at the marketplace. Gameplay changes based on Norse seasonal festivals.',
                minScore: 0,
                buttonClass: 'btn-midgard'
            },
            'niflheim': {
                name: 'Niflheim (Icy Realm)',
                description: 'A frigid, mist-shrouded land of perpetual winter and ice formations. Features frozen waterfalls, ice caves, snow-covered forests, and magnificent aurora borealis skies.',
                mechanics: 'Frozen Runes: Ice gradually forms over random runes each turn. Use special fire runes to thaw adjacent frozen runes. Beware of Frost Giants that move across the board freezing runes. Avalanche system brings cascading ice blocks from the top.',
                minScore: 1000,
                buttonClass: 'btn-niflheim'
            },
            'muspelheim': {
                name: 'Muspelheim (Fire Realm)',
                description: 'A volcanic landscape with rivers of lava, obsidian formations, and ash storms. Features fire geysers, molten metal pools, smoke-filled skies, and glowing embers everywhere.',
                mechanics: 'Burning Runes: Certain runes become "burning" and will destroy adjacent runes if not matched quickly. The Heat Meter fills as fire runes are matched, unlocking powerful "Inferno" special moves. Water runes create powerful chain reactions when matched. Volcanic eruptions periodically scramble the board.',
                minScore: 1000,
                buttonClass: 'btn-muspelheim'
            },
            'jotunheim': {
                name: 'Jotunheim (Giant Mountains)',
                description: 'Towering, rugged mountain ranges where giants dwell among ancient stone structures. Features massive boulders, hidden valleys, ancient ruins, and misty peaks.',
                mechanics: 'Gravity Shift: Matches cause runes to fall in different directions based on mountain slopes. Giant\'s Challenge brings enormous multi-space runes requiring specific patterns to break. Echo Matches duplicate matches elsewhere on the board. Climbing Mechanic requires creating matches that form continuous upward paths.',
                minScore: 2500,
                buttonClass: 'btn-jotunheim'
            },
            'yggdrasil': {
                name: 'Yggdrasil Grove (World Tree)',
                description: 'A mystical forest centered around a colossal tree with roots and branches extending beyond sight. Features magical pools, glowing runes, animal spirits, and dimensional portals.',
                mechanics: 'Rune Transformation: Matched runes transform into different types. Special "root" and "branch" runes connect to other realms, bringing their unique mechanics temporarily. Destiny Weaving lets you see moves ahead, but future board states change based on current matches. World Tree Growth provides increasingly powerful bonuses as it grows during gameplay.',
                minScore: 4000,
                buttonClass: 'btn-yggdrasil'
            }
        };

        // Paths between realms
        const paths = [
            { from: 'midgard', to: 'niflheim', element: 'progress-midgard-niflheim' },
            { from: 'midgard', to: 'muspelheim', element: 'progress-midgard-muspelheim' },
            { from: 'niflheim', to: 'jotunheim', element: 'progress-niflheim-jotunheim' },
            { from: 'muspelheim', to: 'jotunheim', element: 'progress-muspelheim-jotunheim' },
            { from: 'jotunheim', to: 'yggdrasil', element: 'progress-jotunheim-yggdrasil' }
        ];

        let currentRealm = 'midgard';
        let playerScore = 0;
        const maxScore = 5000;
        
        // Initialize the UI
        updateScore(0);
        updatePaths();
        
        // Score simulation for demo purposes
        // In your actual game, this would be connected to your game's scoring system
        function simulateScore() {
            // Simulate score increases for testing
            const increment = Math.floor(Math.random() * 200) + 100;
            updateScore(Math.min(playerScore + increment, maxScore));
            
            // Check for newly unlocked realms
            checkUnlocks();
            
            // Update path progress
            updatePaths();
        }
        
        // Update the score display
        function updateScore(newScore) {
            playerScore = newScore;
            $('#player-score').text(playerScore);
        }
        
        // Update all path progress based on score
        function updatePaths() {
            paths.forEach(path => {
                updatePathProgress(path);
            });
        }
        
        // Update individual path progress
        function updatePathProgress(path) {
            const fromRealm = realms[path.from];
            const toRealm = realms[path.to];
            
            if (playerScore < fromRealm.minScore) {
                // Not yet reached starting realm
                $(`#${path.element}`).css('height', '0%');
            } else if (playerScore >= toRealm.minScore) {
                // Fully unlocked path
                $(`#${path.element}`).css('height', '100%');
            } else {
                // Partially unlocked
                const scoreRange = toRealm.minScore - fromRealm.minScore;
                const progress = (playerScore - fromRealm.minScore) / scoreRange * 100;
                $(`#${path.element}`).css('height', `${progress}%`);
            }
        }
        
        // Check for newly unlocked realms
        function checkUnlocks() {
            let newlyUnlocked = [];
            
            // Check each realm
            for (const [id, realm] of Object.entries(realms)) {
                const realmElement = $(`#${id}`);
                
                if (playerScore >= realm.minScore && realmElement.hasClass('locked')) {
                    realmElement.removeClass('locked').addClass('unlocked');
                    newlyUnlocked.push(id);
                }
            }
            
            // If any new realms were unlocked, show the travel modal
            if (newlyUnlocked.length > 0) {
                const newRealmId = newlyUnlocked[0];
                showTravelModal(newRealmId);
            }
        }
        
        // Show travel modal when unlocking a new realm
        function showTravelModal(realmId) {
            const realm = realms[realmId];
            $('#travel-message').text(`You've unlocked ${realm.name}! Would you like to travel there now?`);
            
            // Set up the confirm button
            $('#confirm-travel').off('click').on('click', function() {
                selectRealm(realmId);
                $('#travelModal').modal('hide');
            });
            
            // Show the modal
            $('#travelModal').modal('show');
        }
        
        // Select a realm and update the UI
        function selectRealm(realmId) {
            // Only allow selecting unlocked realms
            if ($(`#${realmId}`).hasClass('locked')) {
                const neededScore = realms[realmId].minScore;
                alert(`You need ${neededScore} points to unlock this realm. Keep playing to earn more!`);
                return;
            }
            
            // Update active state
            $('.realm').removeClass('active');
            $(`#${realmId}`).addClass('active');
            
            // Update current realm
            currentRealm = realmId;
            
            // Update details panel
            const realm = realms[realmId];
            $('#detail-title').text(realm.name);
            $('#detail-description').text(realm.description);
            $('#detail-mechanics').text(realm.mechanics);
            
            // Update play button
            $('#play-button').text(`Play ${realm.name.split(' ')[0]}`)
                .removeClass('btn-midgard btn-niflheim btn-muspelheim btn-jotunheim btn-yggdrasil')
                .addClass(realm.buttonClass);
        }
        
        // Set up click handlers for realms
        $('.realm').on('click', function() {
            const realmId = $(this).attr('id');
            selectRealm(realmId);
        });
        
        // Set up play button
        $('#play-button').on('click', function() {
            // This would redirect to or start the game for the current realm
            alert(`Starting game in ${realms[currentRealm].name}`);
            
            // For demo: simulate score increase after playing
            simulateScore();
        });
        
        // For demo purposes: Add a key handler to increase score with spacebar
        $(document).on('keydown', function(e) {
            if (e.key === ' ' || e.key === 'Spacebar') {
                simulateScore();
                e.preventDefault();
            }
        });
    </script>
</body>
</html>