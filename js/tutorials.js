document.addEventListener('DOMContentLoaded', () => {
    // Initialize scroll controls
    initializeScrollControls();
    // Initialize filters
    initializeFilters();

    // Добавляем обработчики для карточек туториалов
    const tutorialCards = document.querySelectorAll('.tutorial-card');
    tutorialCards.forEach(card => {
        card.addEventListener('click', () => {
            const tutorialId = card.dataset.id;
            openTutorialModal(tutorialId);
        });
    });
});

function openTutorialModal(tutorialId) {
    fetch(`tutorial-player.php?id=${tutorialId}`)
        .then(response => response.text())
        .then(html => {
            const modal = document.createElement('div');
            modal.className = 'tutorial-modal';
            modal.innerHTML = html;
            document.body.appendChild(modal);
            
            // Make sure video is loaded with correct sources
            const video = modal.querySelector('#tutorialVideo');
            video.innerHTML = `
                <source src="/uploads/tutorials/videos/${video.dataset.src}" type="video/mp4">
                <source src="/uploads/tutorials/videos/${video.dataset.src}" type="video/webm">
            `;
            
            initializeTutorialPlayer();
        });
}
function initializeScrollControls() {
    const container = document.querySelector('.frame-6');
    const prevBtn = document.querySelector('.frame-13.prev');
    const nextBtn = document.querySelector('.frame-13.next');
    
    if (!container || !prevBtn || !nextBtn) return;
    
    let scrollPosition = 0;
    prevBtn.style.display = 'none';
    
    nextBtn.addEventListener('click', () => {
        scrollPosition += 300;
        container.scrollLeft = scrollPosition;
        prevBtn.style.display = 'flex';
        
        if (scrollPosition >= container.scrollWidth - container.clientWidth) {
            nextBtn.style.display = 'none';
        }
    });
    
    prevBtn.addEventListener('click', () => {
        scrollPosition -= 300;
        container.scrollLeft = scrollPosition;
        nextBtn.style.display = 'flex';
        
        if (scrollPosition <= 0) {
            prevBtn.style.display = 'none';
            scrollPosition = 0;
        }
    });
}

function initializeFilters() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    if (!filterBtns.length) return;
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });
}
function getActiveFilters() {
    const filters = {
        category: document.querySelector('.filter-group:first-child .filter-btn.active').textContent,
        level: document.querySelector('.filter-group:last-child .filter-btn.active')?.textContent
    };
    return filters;
}

function loadTutorials(filters) {
    // Here you'll implement the AJAX call to load filtered tutorials
    // This is just a placeholder for the actual implementation
    console.log('Loading tutorials with filters:', filters);
}

function initializeTutorialPlayer() {
    // Wait for elements to be in DOM
    setTimeout(() => {
        const video = document.querySelector('#tutorialVideo');
        const playBtn = document.querySelector('.play-btn');
        const volumeBtn = document.querySelector('.volume-btn');
        const fullscreenBtn = document.querySelector('.fullscreen-btn');

        if (!video || !playBtn) return; // Exit if elements don't exist

        function togglePlay() {
            if (video.paused) {
                video.play();
            } else {
                video.pause();
            }
        }

        playBtn.addEventListener('click', togglePlay);
        video.addEventListener('click', togglePlay);

        if (volumeBtn) {
            volumeBtn.addEventListener('click', () => {
                video.muted = !video.muted;
            });
        }

        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', () => {
                if (!document.fullscreenElement) {
                    video.requestFullscreen();
                } else {
                    document.exitFullscreen();
                }
            });
        }
    }, 100);
}