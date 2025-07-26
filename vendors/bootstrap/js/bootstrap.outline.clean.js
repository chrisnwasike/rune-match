// Frontend treasure interaction system
// Note: All actual solutions and prize logic remain server-side
const TreasureSystem = {
    init() {
        if (!signedIn) return;
        this.setupInteractions();
        this.initializeListeners();
    },

    _debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    setupInteractions() {
        // Add subtle interactive elements that could be treasures
        this.addHiddenElements();
        this.setupHoverEffects();
        this.createMysteriousPatterns();
    },

    initializeListeners() {
        // Setup interaction listeners
        this.setupClickPatterns();
        this.setupKeyPatterns();
        // this.setupScrollPatterns();
        // this.setupTimingPatterns();
    },

    addHiddenElements() {
        // Add barely visible interactive elements
        document.querySelectorAll('.eph-nft-card').forEach(ephcard => {
            const hiddenSpot = document.createElement('div');
            hiddenSpot.className = 'hidden-interactive';
            hiddenSpot.style.cssText = `
                position: absolute;
                background-color: red;
                width: 5px;
                height: 5px;
                cursor: pointer;
                opacity: 0.01;
                z-index: 2;
            `;
            // Random position within the image
            hiddenSpot.style.left = Math.random() * 90 + 5 + '%';
            hiddenSpot.style.top = Math.random() * 90 + 5 + '%';
            ephcard.style.position = 'relative';
            ephcard.appendChild(hiddenSpot);
            console.log('added eph secret')
        });
    },

    setupHoverEffects() {
        // Add subtle hover effects to various elements
        const elements = document.querySelectorAll('.feature, .newsimage, .team-img');
        elements.forEach(el => {
            el.addEventListener('mousemove', (e) => {
                const rect = el.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                // Create subtle light effect
                el.style.backgroundImage = `radial-gradient(circle at ${x}px ${y}px, rgba(255,255,255,0.1), transparent)`;
            });
            
            el.addEventListener('mouseleave', () => {
                el.style.backgroundImage = '';
            });
        });
    },

    createMysteriousPatterns() {
        // Add subtle patterns that might be interactive
        const sections = document.querySelectorAll('section');
        sections.forEach(section => {
            const pattern = document.createElement('div');
            pattern.className = 'mysterious-pattern';
            pattern.style.cssText = `
                position: absolute;
                width: 100%;
                height: 100%;
                top: 0;
                left: 0;
                pointer-events: none;
                background-image: radial-gradient(circle at 50% 50%, transparent 99%, rgba(255,255,255,0.05) 100%);
                background-size: 50px 50px;
            `;
            if (section.style.position !== 'absolute') {
                section.style.position = 'relative';
            }
            section.appendChild(pattern);
        });
    },



    setupClickPatterns() {
        document.addEventListener('click', async (e) => {
            const target = e.target;
            if (target.classList.contains('hidden-interactive')) {
                await this.checkTreasure('click', {
                    x: e.clientX,
                    y: e.clientY,
                    elementType: target.parentElement.className,
                    timestamp: Date.now()
                });
            }
        });
    },

    setupKeyPatterns() {
        return;
        
        let keySequence = [];
        document.addEventListener('keydown', async (e) => {
            keySequence.push(e.key);
            if (keySequence.length > 10) keySequence.shift();
            
            await this.checkTreasure('keys', {
                sequence: keySequence.join(','),
                timestamp: Date.now()
            });
        });
    },

    setupScrollPatterns() {
        let lastScrollPosition = 0;
        let scrollPattern = [];
        
        document.addEventListener('scroll', this._debounce(async () => {
            const currentScroll = window.scrollY;
            const direction = currentScroll > lastScrollPosition ? 'down' : 'up';
            scrollPattern.push(direction);
            if (scrollPattern.length > 5) scrollPattern.shift();
            
            await this.checkTreasure('scroll', {
                pattern: scrollPattern.join(','),
                position: currentScroll,
                timestamp: Date.now()
            });
            
            lastScrollPosition = currentScroll;
        }, 100));
    },

    // setupTimingPatterns() {
    //     let clickTimes = [];
    //     document.addEventListener('click', this._debounce(async (e) => {
    //         const now = Date.now();
    //         clickTimes.push(now);
    //         if (clickTimes.length > 5) clickTimes.shift();
            
    //         const intervals = [];
    //         for (let i = 1; i < clickTimes.length; i++) {
    //             intervals.push(clickTimes[i] - clickTimes[i-1]);
    //         }
            
    //         await this.checkTreasure('timing', {
    //             intervals: intervals.join(','),
    //             elementType: e.target.className,
    //             timestamp: now
    //         });
    //     }, 100));
    // },

    async checkTreasure(type, data) {
        try {
            const response = await fetch('inc/check-treasure', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({
                    type,
                    data
                })
            });
            
            if (response.ok) {
                const result = await response.json();
                if (result.found) {
                    await this.claimTreasure(result.prize);
                }
                return result;
            }
            else {
                // Shhh
            }
        } catch (error) {
            console.error('Error checking treasure:', error);
        }
    },

    async claimTreasure(prize) {
        try {
            const response = await fetch('inc/claim-treasure', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({
                    prize
                })
            });
            
            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    this.showTreasureFound(result.prize);
                }
            }
        } catch (error) {
            console.error('Error checking treasure:', error);
        }
    },

    showTreasureFound(prize) {
        const modal = document.createElement('div');
        modal.className = 'treasure-modal';
        modal.innerHTML = `
            <div class="treasure-content">
                <h3>🎉 Treasure Found! 🎉</h3>
                <p>You've discovered ${prize.type}!</p>
                <p class="prize">${prize.quantity} ${prize.name}</p>
                <button onclick="this.parentElement.parentElement.remove()">Claim</button>
            </div>
        `;
        document.body.appendChild(modal);
    }
};

// Add required CSS
const style = document.createElement('style');
style.textContent = `
.treasure-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.treasure-content {
    background: linear-gradient(45deg, #2f2f2f, #505050);
    padding: 2rem;
    border-radius: 10px;
    text-align: center;
    color: white;
    border: 2px solid #ffc108;
    box-shadow: 0 0 20px rgba(255, 193, 8, 0.3);
}

.treasure-content .prize {
    font-size: 1.5rem;
    color: #ffc108;
    margin: 1rem 0;
}

.treasure-content button {
    background: #ffc108;
    border: none;
    padding: 0.5rem 2rem;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease;
}

.treasure-content button:hover {
    background: #ffcd39;
    transform: scale(1.05);
}

.hidden-interactive:hover {
    opacity: 0.05;
}
`;
document.head.appendChild(style);

// Initialize the treasure system
document.addEventListener('DOMContentLoaded', () => TreasureSystem.init());


let readyToMove;
$('.eph-nft-card .card-inner').on('click', async function() {

    if (!signedIn) return;
    $('.eph-nft-card .card-inner').removeClass('backflip')
    $(this).addClass('backflip');

    if ($('.eph-nft-card:nth-child(2) .card-inner').hasClass('backflip')) {

        if ($('.eph22').length == 0) {
            $('.eph-nft-card:nth-child(2)').append(`<div class="eph22"></div>`)
        }

        $('.eph-nft-card:nth-child(2)').off('click', "**" );
        $(".eph-nft-card:nth-child(2)").draggable({
            handle: ".eph22", // Only draggable from handle
            containment: "body", // Restrict dragging to hero section
            scroll: false,
            start: function(event, ui) {
                let el = $(this);
                el.css({
                    width: el.width() + 'px',
                    height: el.height() + 'px',
                    top: el.offset().top + 'px',
                    left: el.offset().left + 'px'
                }).appendTo('body');
            },
            drag: function(event, ui) {
                let e = event.originalEvent.originalEvent;
                $(".eph-nft-card.ui-draggable").css({
                    position: 'absolute',
                    left: e.x + 'px',
                    top: e.y + 'px'
                });
            },
            stop: function(event, ui) {
                $(this).removeClass('dragging'); // Remove class after drag ends
            }
        });
        $('[data-garden="secret"]').droppable({
            drop: function( event, ui ) {
                $( this ).css('transform', 'scale(1.25)')
                let oldStyle = $( this ).find('.garden-front').attr('style');
                $( this ).find('.garden-front').removeAttr('style');
                ui.helper.prependTo('[data-garden="secret"]').css(
                    {
                        'width':'100%', 'height': '100%', 'position':'absolute', top:'0', left: '0', 'margin-left': '0', 'transform': 'none', 'border-radius': '0px'
                    });
                ui.helper.find('.backflip').removeClass('backflip')
                ui.helper.find('.eph22.ui-draggable-handle').removeClass('eph22')
                $( this ).css('transform', 'scale(1)')
                $( this ).prepend('<div class="ripe_essence"></div>')
                let newHeight = 100, hieghtInterval =
                setInterval(() => {
                    newHeight -= 10; 
                    ui.helper.css('height', newHeight+'%')
                    if (newHeight <= 0) { clearInterval(hieghtInterval); addGiftBox('[data-garden="secret"]', 'flipcards', 'archseedling') }
                }, 1000);
            }
        });
    } else {
        $('.eph-nft-card:nth-child(2) .card-inner').on('click', function() {
            if (!signedIn) return;
            $('.eph-nft-card .card-inner').removeClass('backflip')
            $(this).addClass('backflip');
        });
    }
});


function createConfetti() {
    const colors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff', '#00ffff'];
    
    for (let i = 0; i < 50; i++) {
        const confetti = $('<div>').addClass('confetti');
        confetti.css({
            'left': Math.random() * 100 + 'vw',
            'background-color': colors[Math.floor(Math.random() * colors.length)],
            'animation': 'confettiFall ' + (Math.random() * 2 + 1) + 's linear forwards'
        });
        
        $('.celebration-container').append(confetti);
        
        // Remove confetti after animation
        setTimeout(() => {
            confetti.remove();
        }, 3000);
    }
}

function showCelebration(prize) {
    $('.treasure-button').remove();

    let holderThis = $('body');
    let transformation;
    if ($('.underbody:visible').length) {
        holderThis = $('.underbody');
        transformation = 'style="transform: scaleX(-1)"';
    }

    holderThis.append(`
        <div class="celebration-container" ${transformation}>
            <div class="min-vh-100 d-flex align-items-center justify-content-center celebration-content">
                <div class="text-center">
                    <div class="prize-text text-white mb-4">
                        Congratulations! 🎉<br>
                        You've found a treasure: <span class="prize-value text-warning">${prize.quantity} ${prize.name}</span>!
                    </div>
                    <button class="btn btn-light px-4" onclick="closeCelebration()">Close</button>
                </div>
            </div>
        </div>
    `)

    $('.celebration-container').fadeIn(300, function() {
        createConfetti();
        setTimeout(() => {
            $('.prize-text').addClass('show');
        }, 200);
    });
}

function closeCelebration() {
    $('.celebration-container').fadeOut(300);
    $('.prize-text').removeClass('show');
    $('.confetti').remove();
    $('.celebration-container').remove();
}


function createFlames() {
    const flamesContainer = document.createElement('div');
    flamesContainer.className = 'flames-container';

    // Create multiple flame layers for depth
    const positions = [
        { left: '20%', delay: '0s' },
        { left: '40%', delay: '0.2s' },
        { left: '60%', delay: '0.1s' },
        { left: '80%', delay: '0.3s' }
    ];

    positions.forEach(pos => {
        const flameLayer = document.createElement('div');
        flameLayer.className = 'flame-layer';

        // Create core flame
        const coreFlame = document.createElement('div');
        coreFlame.className = 'flame core';
        coreFlame.style.left = pos.left;
        coreFlame.style.animationDelay = pos.delay;

        // Create inner flame
        const innerFlame = document.createElement('div');
        innerFlame.className = 'flame inner';
        innerFlame.style.left = `calc(${pos.left} + 0.25rem)`;
        innerFlame.style.animationDelay = pos.delay;

        // Create outer flame
        const outerFlame = document.createElement('div');
        outerFlame.className = 'flame outer';
        outerFlame.style.left = `calc(${pos.left} - 0.25rem)`;
        outerFlame.style.animationDelay = pos.delay;

        // Create flickering flames
        const flicker1 = document.createElement('div');
        flicker1.className = 'flame flicker';
        flicker1.style.left = `calc(${pos.left} - 1rem)`;
        flicker1.style.animationDelay = `${Math.random() * 0.5}s`;

        const flicker2 = document.createElement('div');
        flicker2.className = 'flame flicker';
        flicker2.style.left = `calc(${pos.left} + 1rem)`;
        flicker2.style.animationDelay = `${Math.random() * 0.5}s`;

        flameLayer.appendChild(outerFlame);
        flameLayer.appendChild(coreFlame);
        flameLayer.appendChild(innerFlame);
        flameLayer.appendChild(flicker1);
        flameLayer.appendChild(flicker2);

        flamesContainer.appendChild(flameLayer);
    });

    // Add heat distortion effect
    const heatDistortion = document.createElement('div');
    heatDistortion.className = 'heat-distortion';
    flamesContainer.appendChild(heatDistortion);
    flamesContainer.style.zIndex = 5;

    return flamesContainer;
}

function startBurn() {
    setTimeout(() => {
        const div = document.querySelector('.eph-nft-card:first-child');
        div.querySelector('.card-inner').classList.remove('backflip');
        const flames = createFlames();
        div.prepend(flames);
        $('.eph-nft-card:first-child').css(
            {
                'transform': 'scale(1.25) rotateY(0deg)',
                'z-index': '5',
            }
        ).prepend('<div class="fire"></div>');
        $('.eph-nft-card:first-child .card-front').css(
            {
                'background-size': '95%',
                'background-position': 'center',
                'border': 'none',
                'border-radius': '0px'
            }
        )
        burnOut()
    }, 1000);
}

function burnOut() {
    let flameUp = 100;
    let increaseFlame = 
        setInterval(() => {
            flameUp += 5;
            $('.flames-container').css('height', flameUp+'%');
            if (flameUp >= 150) { 
                clearInterval(increaseFlame); 
                addGiftBox('.eph-nft-card:first-child', 'flipcards', 'ephemeral')
            }
        }, 1000);
}

function addGiftBox(selector, type, data) {
    $(selector).parent().append(
        `<button class="treasure-button" style="">
            <span></span>
        </button>`)
    $(selector).children().hide('slow');
    $(selector).css('z-index', 0);

    $('.treasure-button').on('click', async function() {
        let treasureFound = await TreasureSystem.checkTreasure(type, data);
        console.log(treasureFound);
        if (treasureFound.success) {
            showCelebration(treasureFound.prize);
        }
    })

}

let firstLife = false, firstBornClick = 1, opacity;
$('.eph-nft-card:first-child .card-back .message').on('click', function() {
    
    firstBornClick++;
    if (firstBornClick < 4) {
        opacity = 1 - (0.35 * firstBornClick)
        $(this).css('opacity', opacity);
    }
    else if (firstBornClick == 4) { opacity = 0;  $(this).css('opacity', opacity); $(this).html('Highly inflammable the second time.') }
    else if (firstBornClick > 4 && firstBornClick < 8) {
        opacity =  (1 / (8 - firstBornClick)).toFixed(2);
        $(this).css('opacity', opacity);
    }
    else if (firstBornClick >= 7) {
        if (!firstLife) {
            firstBornClick = 1;
            firstLife = true;
        }  else {
            startBurn(); return;
        }
    }

    console.log(opacity)
});




$('.eph-nft-card:last-child .card-back .message').on('mouseenter', function() {
    $(this).css('left', '100%');
    setTimeout(() => {
        $(this).css('opacity', '0.5').addClass('glideText');
    }, 3000);
    setTimeout(() => {
        $(this).remove();
    }, 15000);
});




$('.testimonial_player').on('click', async function() {
    $('.testimonial_player').removeClass('selectedPlayer');
    $(this).addClass('selectedPlayer');
    $('.bigavatar').addClass('hide');
    $('.bigtext').addClass('hide');

    await new Promise(resolve => setTimeout(resolve, 300));

    let playertestimoney  = $(this).find('.playertestimoney ').html()
    let playername = $(this).find('.playername').html()
    let playeravatar = $(this).find('.playeravatar').css('background-image');

    $('.bigavatar').css('background-image', playeravatar)
    $('.bigtext').html(`<p>${playertestimoney} <br><br>-${playername}</p>`)

    await new Promise(resolve => setTimeout(resolve, 300));

    $('.bigavatar').removeClass('hide');
    $('.bigtext').removeClass('hide');
})



$(document).ready(function() {
    handleWindowSize();
 });
 
 // On resize
 $(window).on('resize', function() {
    handleWindowSize();
 });

function handleWindowSize() {
    let width = $(window).width();
    let test = $('.testimonial_player').length;
    widthBox = 100 / ((test / 2) + 2);
    
    if (window.innerWidth < 560) {
        if ($('.mobile-scroller').length <= 0) {
            $('.social-proof').append('<div class="mobile-scroller" style="width:100%"></div>')
        }
        // Set width dynamically
        $('.testimonial_players').appendTo('.mobile-scroller')
        $('.testimonial_players').css('width', (100 * test) + 'px');
        $('.testimonial_player').css('width', '100px').css('padding-bottom', '100px');
    } else {
        $('.testimonial_players').appendTo('.social-proof')
        $('.testimonial_players').css('width', '80%');
        $('.testimonial_player').css('width', widthBox+'%').css('padding-bottom',  widthBox+'%');
    }

}


let timer;
let timeLeft = 20;
let challengeCompleted = false;
async function goUnderground()
{
    if (!signedIn) return;
    addUnderworldCSS();

    $('body').css({
        'transition': 'transform 5s linear',
        'transform': 'rotateY(180deg)'
    })
    await new Promise(resolve => setTimeout(resolve, 2500));
    window.scrollTo(0, 0);
    $('body').append('<div class="underbody" style="position: fixed; left: 0; right: 0; bottom: 0; top: 0; backdrop-filter: blur(5px) hue-rotate(98deg); z-index: 2000; transform: scaleX(-1)"></div>')
    await new Promise(resolve => setTimeout(resolve, 2500));

    $('.eph-nft-card:last-of-type').clone().appendTo('.underbody')

    let alreadyClaimedThis = await TreasureSystem.checkTreasure('flipcards', 'undersite');
    let theUnderWorld;
    if (alreadyClaimedThis.message === "You have already claimed this treasure") {
        theUnderWorld = `
            <button class="btn btn-danger exitUndersite" onclick="window.location.reload()" style="; position: absolute;">Exit</button>
            <div id="portal">
                <div class="container mt-4">
                    <h2 class="text-center">The Shrouded Merchant</h2>
                    <div class="row">
                        <!-- Inventory Section -->
                        <div class="col-md-6">
                            <div class="tradeZoneInfo"><h4>Your Inventory</h4> </div>
                            <div class="list-group" id="inventoryList">
                                <!-- Items will be dynamically loaded -->
                            </div>
                        </div>
                        
                        <!-- Trade Zone -->
                        <div class="col-md-6">
                            <div class="npc-container mb-3">
                                <img src="assets/img/landing/under-trader.png" alt="Mysterious NPC" class="npc-img" id="npcImg">
                                <div class="npcFeedback" class="p-2">
                                    <div id="npcResponse">"Ah... what do you have for me today?"</div>
                                    <div><strong>Payment:</strong> <span id="goldEarned">0</span> Seed</div>
                                </div>

                            </div>
                            <div class="trade-zone" id="tradeZone">Drop items here to sell</div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('.underbody').html(theUnderWorld);
        window.npcShop = new NPCShop();

        document.addEventListener('mouseover', (e) => {
            const item = e.target.closest('#portal .list-group-item');
            if (item) {
                const rect = item.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                item.style.setProperty('--mouse-x', `${x}px`);
                item.style.setProperty('--mouse-y', `${y}px`);
            }
        });

    } else {
        theUnderWorld = `
            <button class="btn btn-danger exitUndersite" onclick="window.location.reload()" style="position: absolute;">Exit</button>
            <div id="underriddle">Solve riddle to claim treasure</div>
            <div id="countdown">⏳ Time left: <span id="underbodytimer">${timeLeft}</span>s</div>
            <div id="puzzle-box"><span id="puzzle-question">Where doth death end?</span>
                <input type="text" id="puzzle-answer" placeholder="Your answer">
                <button onclick="checkPuzzle()">Submit</button>
            </div>
            
            <div id="portal"></div>
        `
        $('.underbody').html(theUnderWorld);
        startCountdown();
    }
}

function addUnderworldCSS() {
    const style = document.createElement('style');
    style.textContent = `
    .underbody {
        color: #fff;

        .npc-container { text-align: center; }
        .npc-img:hover { transform: scale(1.1); }
        .inventory-item { cursor: pointer; transition: 0.3s; display: flex; justify-content: space-between; align-items: center; }
        .inventory-item:hover { transform: scale(1.1); }
        .trade-zone { border: 2px dashed #fff; padding: 20px; text-align: center; height: 300px; }
        .sold-item { opacity: 0.5; text-decoration: line-through; }
        .quantity-badge { background-color: #ffc107; color: black; padding: 5px 10px; border-radius: 5px; }
    }
    `;
    document.head.appendChild(style);
}



function startCountdown() {
    timer = setInterval(() => {
        timeLeft--;
        document.getElementById('underbodytimer').textContent = timeLeft;
        if (timeLeft <= 0 && !challengeCompleted) {
            failChallenge();
        }
    }, 1000);
}

// If the player runs out of time
function failChallenge() {
    clearInterval(timer);
    alert("You hesitated for too long... The underground fades away!");
    document.getElementById("underground").remove();
    timeLeft = 20;
}

document.addEventListener("click", function (event) {
    if (!signedIn) return;

    if (event.target.id === "treasure") {
        completeChallenge("treasure");
    }
});

function checkPuzzle() {
    let answer = document.getElementById("puzzle-answer").value;
    if (answer.search('life') >= 0 && answer.search('begin') >= 0) { 
        completedUnsersiteChallenge("puzzle");
    } else {
    }
}

async function completedUnsersiteChallenge() {
    clearInterval(timer);
    challengeCompleted = true;
    $('#underriddle').remove();
    $('#countdown').remove();
    $('#puzzle-box').html('<div class="underprizebox"></div>')
    addGiftBox('#puzzle-box .underprizebox', 'flipcards', 'undersite');
    await new Promise(resolve => setTimeout(resolve, 1000));
    $('.celebration-container').appendTo('.underbody').css('transform', 'scaleX(-1)')
}



let keyPosY = placeGoldenKey().y;
$(window).scroll(function() {
    if (!signedIn) return;
    if (!$('.eph-nft-card:last-of-type .card-inner').hasClass('backflip')) return;

    if(($(window).scrollTop() + $(window).height() - (keyPosY+125)) > 0 && ($(window).scrollTop() + $(window).height() - (keyPosY+125)) < 70) {
        // User has scrolled to within 100px of the bottom
        // console.log("Reached end of page!");
        $('.year-text').html('2<i class="fa-solid fa-2xs fa-skull"></i>25');
        $('.hint-text').html('The golden key opens this portal!');

        if ($('#golden-key').length == 0) {
            $('body').append('<div id="golden-key" style="position: absolute; top: '+keyPosY+'px; right: 10px; width: 10px; height: 10px; opacity: 0.3; cursor: pointer; transition: opacity 0.3s;"><i class="fa-solid fa-2xs fa-key" style="color:gold"></i></div>');
        
            let hasGoldenKey = false; // Track if player has found the key

            // When the golden pixel is clicked, player "collects" it
            $('.floating-roadmap-trigger').attr('data-bs-toggle', '').on('click', function() {
                $('#golden-key').remove();
                hasGoldenKey = true;
                $('body').append('<div class="msg" style="color: yellow; position: absolute; bottom: unset; top: '+(keyPosY-50)+'px; right: 150px"><p>You\'ve found the door to the undersite. Proceed? </p><div class=""msgBtns"><button class="btn btn-primary btn-sm me-2 undersiteyes">Yes</button><button class="btn btn-primary btn-sm">No</button></div></div>')
                $('.undersiteyes').on('click', function() {
                    $('.msg').remove();
                    goUnderground();
                }); 
            });
        }
    }
    else {
        $('.floating-roadmap-trigger').attr('data-bs-toggle', 'modal').removeAttr('onclick');
        $('#golden-key').remove();
        $('.year-text').html('2025');
        $('.hint-text').html('Click to see what\'s coming!');
    }
});

function placeGoldenKey() {
    return goldenKeyPos = {
        y: Math.max((Math.random() * ($('body').height()-200)), (window.innerHeight*2)),
        x: window.innerWidth * 0.9
    }
}








class NPCShop {
    constructor() {
        this.goldEarned = 0;
        this.inventory = [];
        this.proposedSales = [];
        this.pickedItem;

        this.npcResponses = [
            "Hmm... interesting piece you have there.",
            "Ah, a fine addition to my collection!",
            "I've been looking for something like this...",
            "Your taste in items is... peculiar.",
            "Now this... this is something special!"
        ];
        this.initializeElements();
        this.loadResourcesInventory();
        this.setupEventListeners();
    }

    initializeElements() {
        this.inventoryList = document.getElementById('inventoryList');
        this.tradeZone = document.getElementById('tradeZone');
        this.npcResponse = document.getElementById('npcResponse');
        this.goldCounter = document.getElementById('goldEarned');
        this.npcImg = document.getElementById('npcImg');
    }

    setupEventListeners() {
        // Drag and Drop listeners for trade zone
        // NPC interaction
        this.npcImg.addEventListener('click', () => this.triggerNPCInteraction());
    }

    addResourceToBlack(resourceID, qty) {
        this.inventory.push({ resourceID, qty });
    }

    async loadResourcesInventory() {
        this.inventoryList = $('#inventoryList');
        this.inventoryList.empty();
    
        $.get('getPlayerResources', (response) => { // Use arrow function to retain 'this'
            if (response.status === "success") {
                this.inventory = response.inventory;
                response.inventory.forEach(item => {
                    this.inventoryList.append(`<div class="list-group-item inventory-item" style="background-image:url('assets/img/${item.item_img}')"
                        draggable="true" 
                        data-id="${item.id}" 
                        data-resourcename="${item.resource_name}" 
                        data-value="${item.resource_id}" 
                        data-quantity="${item.quantity}">
                        <span class="resource-name">${item.item_name}</span>
                        <span class="quantity-badge">x${item.quantity}</span>
                    </div>`);
                });
    

                $('.inventory-item').on('dragstart', (e) => {
                    this.pickedItem = e.currentTarget.getAttribute('data-resourcename');
                    console.log(this.pickedItem);
                    // e.originalEvent.dataTransfer.setData("text", $(this).data('id'));
                    $(this).addClass('dragging');
                });
                
                $('.inventory-item').on('dragend', (e) => {
                    $(this).removeClass('dragging');
                });

                $('#tradeZone').on('dragover', (e) => {
                    e.preventDefault();
                    $(this).addClass('drag-over');
                });
                
                $('#tradeZone').on('dragleave', (e) => {
                    $(this).removeClass('drag-over');
                });
                
                $('#tradeZone').on('drop', (e) => {
                    e.preventDefault();
                    $(this).removeClass('drag-over');
                    console.log(this.pickedItem);
                    let itemname = this.pickedItem;
                    this.pickedItem = null;

                    // let itemId = e.originalEvent.dataTransfer.getData("text");
                    let item = this.inventory.find(i => i.resource_name == itemname);

                    if (item) {
                        if (item.quantity > 1) {
                            item.quantity--;
                            if ($('#tradeZone .inventory-item').length <= 0) { $('#tradeZone').empty(); }
                            this.proposedSales[itemname] = (this.proposedSales[itemname]) ? this.proposedSales[itemname]+1 : 1;
                            $(`.inventory-item[data-id='${item.id}'] .quantity-badge`).text(`x${item.quantity}`);
                        } else {
                            $(`.inventory-item[data-id='${item.id}']`).addClass('sold-item').off('dragstart dragend');
                        }
                        
                        if ($('#tradeZone .'+item.resource_name).length > 0) {
                            let oldQty = $('#tradeZone .'+item.resource_name).attr('data-quantity');
                            let newQty = parseInt(oldQty) + 1;
                            $('#tradeZone .'+item.resource_name).attr('data-quantity', newQty);
                            $('#tradeZone .'+item.resource_name+' .quantity-badge').html('x'+newQty);
                        } else {
                            $('#tradeZone').append(`<div class="list-group-item inventory-item ${item.resource_name}" style="background-image:url('assets/img/${item.item_img}')"
                                draggable="true" 
                                data-id="${item.id}" 
                                data-resourcename="${item.resource_name}" 
                                data-value="${item.resource_id}" 
                                data-quantity="${this.proposedSales[itemname]}">
                                <span class="resource-name">${item.item_name}</span>
                                <span class="quantity-badge">x${this.proposedSales[itemname]}</span>
                            </div>`);
                        }

                        this.goldEarned += item.value;
                        $('#goldEarned').text(this.goldEarned);
                        $('#npcResponse').text(this.npcResponses[Math.floor(Math.random() * 4)]);
                    }
                });

            } else {
                this.inventoryList.append("<p class='text-center text-warning'>No items found</p>");
            }
        }, "json");
    }
    

    sellItem(item) {
        // Add gold
        this.goldEarned += item.value;
        this.goldCounter.textContent = this.goldEarned;
        
        // Remove item from inventory
        this.inventory = this.inventory.filter(i => i.id !== item.id);
        
        // Update display
        this.renderInventory();
        
        // Show sale animation
        this.showSaleAnimation(item.value);
        
        // Trigger NPC response
        this.triggerNPCResponse('sale');
    }

    showSaleAnimation(value) {
        const animation = document.createElement('div');
        animation.className = 'sale-animation';
        animation.textContent = `+${value} 🪙`;
        this.tradeZone.appendChild(animation);

        // Remove animation after it completes
        setTimeout(() => animation.remove(), 1000);
    }

    triggerNPCResponse(type = 'inspect') {
        const responses = type === 'sale' ? [
            "A fine trade indeed!",
            "Your offering pleases me.",
            "A fair price for such an item.",
            "You drive a hard bargain, but I accept.",
            "Ah, this will sell nicely in my shop."
        ] : this.npcResponses;

        const response = responses[Math.floor(Math.random() * responses.length)];
        this.npcResponse.textContent = response;

        // Add temporary highlight to NPC
        this.npcImg.classList.add('speaking');
        setTimeout(() => this.npcImg.classList.remove('speaking'), 1000);
    }

    triggerNPCInteraction() {
        const interactions = [
            "Looking to trade? Show me what you've got!",
            "My collection grows daily, thanks to adventurers like you.",
            "Rare items fetch a better price, you know...",
            "I've seen many treasures, but I'm always eager for more.",
            "Don't be shy, every item has its value!"
        ];

        const interaction = interactions[Math.floor(Math.random() * interactions.length)];
        this.npcResponse.textContent = interaction;

        // Add interaction animation
        this.npcImg.classList.add('interacting');
        setTimeout(() => this.npcImg.classList.remove('interacting'), 500);
    }

    enhanceInventoryItem(item) {
        const rarity = this.calculateRarity(item.value);
        const tooltip = this.generateTooltip(item);
        
        return `
            <div class="list-group-item ${item.rarity}" 
                 draggable="true" 
                 data-id="${item.id}"
                 data-value="${item.value}"
                 data-tooltip="${tooltip}">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="item-name">${item.name}</span>
                    <span class="item-value">${item.value} 🪙</span>
                </div>
                <div class="rarity-badge">${item.rarity}</div>
                <div class="item-details">
                    ${this.generateItemStats(item)}
                </div>
            </div>
        `;
    }

    calculateRarity(value) {
        if (value >= 300) return 'legendary';
        if (value >= 200) return 'epic';
        if (value >= 100) return 'rare';
        return 'common';
    }

    generateTooltip(item) {
        return `${item.name} (${item.rarity})\nValue: ${item.value} gold`;
    }

    generateItemStats(item) {
        // Simulate random stats for items
        const stats = {
            power: Math.floor(Math.random() * 100),
            durability: Math.floor(Math.random() * 100),
            magic: Math.floor(Math.random() * 100)
        };

        return `
            <div class="stats-container mt-2">
                <small class="text-muted">
                    ⚔️ ${stats.power} | 🛡️ ${stats.durability} | ✨ ${stats.magic}
                </small>
            </div>
        `;
    }

    animateGoldEarned(amount) {
        const counter = this.goldCounter;
        const duration = 1000;
        const steps = 20;
        const increment = amount / steps;
        const stepDuration = duration / steps;
        
        let currentValue = parseInt(counter.textContent);
        const targetValue = currentValue + amount;
        
        const updateCounter = () => {
            currentValue = Math.min(currentValue + increment, targetValue);
            counter.textContent = Math.floor(currentValue);
            counter.classList.add('gold-increase');
            
            if (currentValue < targetValue) {
                setTimeout(updateCounter, stepDuration);
            } else {
                setTimeout(() => counter.classList.remove('gold-increase'), 500);
            }
        };

        updateCounter();
    }

    addParticleEffect(x, y) {
        const particles = 10;
        const colors = ['#ffd700', '#ffa500', '#ffff00'];

        for (let i = 0; i < particles; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            particle.style.left = `${x}px`;
            particle.style.top = `${y}px`;

            document.body.appendChild(particle);

            const angle = (Math.PI * 2 * i) / particles;
            const velocity = 2 + Math.random() * 2;
            const moveX = Math.cos(angle) * velocity;
            const moveY = Math.sin(angle) * velocity;

            particle.animate([
                { transform: 'translate(0, 0) scale(1)', opacity: 1 },
                { transform: `translate(${moveX * 50}px, ${moveY * 50}px) scale(0)`, opacity: 0 }
            ], {
                duration: 1000,
                easing: 'cubic-bezier(0, .9, .57, 1)',
                fill: 'forwards'
            }).onfinish = () => particle.remove();
        }
    }

    sellItem(item) {
        // Get the position of the trade zone for particle effect
        const rect = this.tradeZone.getBoundingClientRect();
        const x = rect.left + rect.width / 2;
        const y = rect.top + rect.height / 2;

        // Add particle effect
        this.addParticleEffect(x, y);

        // Animate gold earning
        this.animateGoldEarned(item.value);

        // Remove item from inventory with fade out
        const itemElement = this.inventoryList.querySelector(`[data-id="${item.id}"]`);
        itemElement.style.animation = 'fadeOut 0.5s ease forwards';

        setTimeout(() => {
            // Remove item from inventory
            this.inventory = this.inventory.filter(i => i.id !== item.id);
            this.renderInventory();

            // Check if inventory is empty
            if (this.inventory.length === 0) {
                this.showEmptyState();
            }
        }, 500);

        // Trigger NPC response
        this.triggerNPCResponse('sale');
    }

    showEmptyState() {
        this.inventoryList.innerHTML = `
            <div class="empty-inventory">
                <p>Your inventory is empty!</p>
                <p>Go find more treasures to trade...</p>
            </div>
        `;
    }

}

// Initialize when DOM is loaded