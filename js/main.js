class ProjectsManager {
    constructor() {
        this.projectsGrid = document.querySelector('.projects-grid');
        if (!this.projectsGrid) return;
        this.loadMoreBtn = document.querySelector('.load-more-btn');
        this.page = 1;
        this.initFilters();
        this.initLoadMore();
        this.initEvents();
        this.initDropdownFilters();
    }

    initFilters() {
        const primaryBtns = document.querySelectorAll('.primary-filters .filter-btn');
        primaryBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                primaryBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                this.page = 1;
                this.loadProjects();
            });
        });
    }

    initDropdownFilters() {
        const toggleBtn = document.querySelector('.filters-toggle-btn');
        const dropdown = document.querySelector('.filters-dropdown');
        const resetBtn = document.querySelector('.reset-filters-btn');

        // Toggle dropdown
        toggleBtn?.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent document click from closing immediately
            dropdown?.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.filters-section')) {
                dropdown?.classList.remove('active');
            }
        });

        // Reset filters
        resetBtn?.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector('[data-type="all"]').classList.add('active');
            this.page = 1;
            this.loadProjects();
            dropdown?.classList.remove('active');
        });

        // Handle secondary category clicks
        document.querySelectorAll('.filter-group .filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.classList.contains('active')) {
                    btn.classList.remove('active');
                } else {
                    document.querySelectorAll('.filter-group .filter-btn').forEach(b => 
                        b.classList.remove('active')
                    );
                    btn.classList.add('active');
                }
                this.page = 1;
                this.loadProjects();
                dropdown?.classList.remove('active');
            });
        });
    }

    initLoadMore() {
        this.loadMoreBtn?.addEventListener('click', () => {
            this.page++;
            this.loadProjects(true); // true means APPEND new projects!
        });
    }

    loadProjects(append = false) {
        // Получаем активные фильтры
        const typeBtn = document.querySelector('.primary-filters .filter-btn.active');
        const categoryBtn = document.querySelector('.filter-group .filter-btn.active');
        
        // Выводим в консоль для проверки
        console.log('Type button:', typeBtn?.dataset.type);
        console.log('Category button:', categoryBtn?.dataset.category);

        const params = new URLSearchParams();
        params.append('type', typeBtn ? typeBtn.dataset.type : 'all');
        params.append('offset', ((this.page - 1) * 8).toString());
        
        if (categoryBtn) {
            params.append('category', categoryBtn.dataset.category);
        }

        // Выводим URL для проверки
        console.log('Request URL:', `api/get_filtered_designs.php?${params.toString()}`);

        fetch(`api/get_filtered_designs.php?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                // Выводим ответ сервера
                console.log('Server response:', data);
                
                if (data.success) {
                    this.loadMoreBtn.style.display = data.data.length === 8 ? 'block' : 'none';
                    this.displayProjects(data.data, append);
                }
            });
    }
    
    initEvents() {
        this.projectsGrid.addEventListener('click', (e) => {
            const likeBtn = e.target.closest('.like-btn');
            const hideBtn = e.target.closest('.hide-btn');
            const card = e.target.closest('.project-card');

            if (likeBtn || hideBtn) {
                e.preventDefault();
                e.stopPropagation();
                
                if (likeBtn) {
                    this.handleLike(likeBtn, likeBtn.dataset.id);
                } else {
                    this.handleHide(card, hideBtn.dataset.id);
                }
            }
            
            // Open work modal only when clicking on card (not buttons)
            if (card && !likeBtn && !hideBtn) {
                const workId = card.dataset.id;
                workModal.open(workId); // Используем существующий экземпляр!
            }
        });
    }

    handleLike(button, designId) {
        // Сразу проверяем авторизацию через PHP сессию
        fetch('api/like_design.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                // Добавляем заголовок для проверки AJAX запроса
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ design_id: designId }),
            // Важно! Отправляем куки с сессией
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const likeIcon = button.querySelector('svg');
                const likeCount = button.closest('.project-card')
                    .querySelector('.project-info-stats .stat-item:last-child span');
              
                button.classList.toggle('liked');
                likeIcon.setAttribute('fill', data.action === 'liked' ? '#FF4444' : 'none');
                likeCount.textContent = data.likes_count;
            }
        });
    }

    handleHide(card, designId) {
        fetch('api/hide_design.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ design_id: designId }),
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                card.style.opacity = '0';
                card.style.transform = 'scale(0.8)';
                setTimeout(() => card.remove(), 500);
            }
        });
    }

    displayProjects(projects, append = false) {
        if (!projects.length) {
            if (!append) { // Only show "no projects" if this isn't a "load more"
                this.projectsGrid.innerHTML = `
                    <div class="no-projects">
                        <h3>Проекты не найдены</h3>
                    </div>`;
            }
            return;
        }

        const projectsHTML = projects.map(project => `
        <div class="project-card" data-id="${project.id}">
            <div class="project-image">
                <img src="uploads/designs/${project.image_path || 'default.jpg'}" alt="${project.title}">
                <div class="project-overlay">
                    <h3 class="project-title">${project.title}</h3>
                    <div class="author-info">
                        <img src="uploads/avatars/${project.user_avatar}" class="author-avatar">
                        <span>${project.username}</span>
                    </div>
                    <div class="action-buttons">
                        <button class="action-btn like-btn ${project.is_liked ? 'liked' : ''}" data-id="${project.id}">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </button>
                        <button class="action-btn hide-btn" data-id="${project.id}">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="project-info">
                <div class="project-info-left">
                    <img src="uploads/avatars/${project.user_avatar}" class="project-info-avatar" alt="${project.username}">
                    <div class="project-info-content">
                        <div class="project-info-title">${project.title}</div>
                        <a href="profile.php?id=${project.user_id}" class="author-link">${project.username}</a>
                        <div class="project-info-stats">
                            <div class="stat-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" color="black">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <span>${project.views_count}</span>
                            </div>
                            <div class="stat-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="red" stroke="currentColor" color="red">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                                <span>${project.likes_count}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');

        if (append) {
            this.projectsGrid.insertAdjacentHTML('beforeend', projectsHTML);
        } else {
            this.projectsGrid.innerHTML = projectsHTML;
        }
    }
}document.addEventListener('DOMContentLoaded', () => new ProjectsManager());

document.querySelectorAll('.subscribe-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const userId = btn.dataset.userId;
        
        fetch('api/handle_subscription.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ following_id: userId }),
            credentials: 'include'
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btn.classList.toggle('subscribed');
                btn.textContent = data.action === 'subscribed' ? 'Отписаться' : 'Подписаться';
            }
        });
    });
});

  function createTopProjectConfetti() {
      const container = document.querySelector('.overlap-group');
      const colors = ['#FF4400', '#FFD700', '#FF69B4', '#FFFFFF'];
    
      function spawnConfetti() {
          const confetti = document.createElement('div');
          confetti.className = 'confetti-particle';
          confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
          confetti.style.left = Math.random() * 100 + '%';
          container.appendChild(confetti);
        
          setTimeout(() => confetti.remove(), 2000);
      }
    
      setInterval(spawnConfetti, 50);
  }

  document.addEventListener('DOMContentLoaded', createTopProjectConfetti);
