document.addEventListener('DOMContentLoaded', () => {
    // Burger menu toggle
    document.querySelector('.burger-menu-icon')?.addEventListener('click', () => {
        document.querySelector('.burger-menu').classList.toggle('active');
    });

    // Login button handler
    document.querySelector('.login-btn')?.addEventListener('click', () => {
        const authModal = document.querySelector('.auth-modal');
        if (authModal) {
            authModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    });

    // Profile link handler - only show modal if not logged in
// Profile link handler
const isLoggedIn = document.body.dataset.loggedIn === 'true';

document.querySelectorAll('.dropdown-item').forEach(item => {
    if (!isLoggedIn && !item.id.includes('logoutBtn')) {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const authModal = document.querySelector('.auth-modal');
            if (authModal) {
                authModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        });
    }
});


    // Logout handler
    document.getElementById('logoutBtn')?.addEventListener('click', (e) => {
        e.preventDefault();
        fetch('src/handlers/logout.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
    });
});

const searchInput = document.getElementById('searchInput');
const searchResults = document.querySelector('.search-results');
const searchForm = document.querySelector('.search-bar');

// Мгновенный поиск при вводе
searchInput.addEventListener('input', (e) => {
    const query = e.target.value.trim();
    
    if (query) {
        fetch(`api/search.php?query=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    displaySearchResults(data.results);
                }
            });
    } else {
        searchResults.style.display = 'none';
    }
});

// Переход на страницу результатов при отправке формы
searchForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const query = searchInput.value.trim();
    if (query) {
        window.location.href = `search.php?q=${encodeURIComponent(query)}`;
    }
});

// Обработка клика по кнопке поиска
document.querySelector('.search-button').addEventListener('click', () => {
    const query = searchInput.value.trim();
    if (query) {
        window.location.href = `search.php?q=${encodeURIComponent(query)}`;
    }
});

function displaySearchResults(results) {
    const html = `
        <div class="search-section">
            ${results.designs.length ? `
                <div class="section-title">Работы</div>
                ${results.designs.map(design => `
                    <a href="project.php?id=${design.id}" class="search-item design">
                        <img src="uploads/designs/${design.image_path}" alt="${design.title}">
                        <span>${design.title}</span>
                    </a>
                `).join('')}
            ` : ''}
        </div>
        <div class="search-section">
            ${results.users.length ? `
                <div class="section-title">Авторы</div>
                ${results.users.map(user => `
                    <a href="profile.php?id=${user.id}" class="search-item user">
                        <img src="uploads/avatars/${user.avatar}" alt="${user.username}">
                        <span>${user.username}</span>
                    </a>
                `).join('')}
            ` : ''}
        </div>
    `;
    
    searchResults.innerHTML = html;
    searchResults.style.display = 'block';
}

// Закрытие результатов при клике вне
document.addEventListener('click', (e) => {
    if (!e.target.closest('.search-bar')) {
        searchResults.style.display = 'none';
    }
});

// Add scroll color transition
window.addEventListener('scroll', () => {
    const header = document.querySelector('header');
    const searchInput = document.getElementById('search-bar');
    const searchButton = document.querySelector('.search-button');
    const burgerMenu = document.querySelector('.burger-menu-icon');
    const dropmenu = document.querySelector('.username');


    
    if (window.scrollY > 50) {
        header.classList.add('scrolled');
        searchButton.style.color = '#fff';
        burgerMenu.style.color = '#fff';
        dropmenu.style.color = '#000';
    } else {
        header.classList.remove('scrolled');
        searchButton.style.color = '#000';
        burgerMenu.style.color = '#000';
        dropmenu.style.color = '#fff';
    }
});

// Add this to existing search functionality
searchInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        const query = searchInput.value.trim();
        if (query) {
            window.location.href = `search.php?q=${encodeURIComponent(query)}`;
        }
    }
});
