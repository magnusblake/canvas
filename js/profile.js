document.addEventListener('DOMContentLoaded', function() {
    loadUserAwards();

    // Существующий код для модального окна
    const modal = document.getElementById('uploadModal');
    const createWorkBtn = document.querySelector('.create-work');
    const closeBtn = document.querySelector('.modal .close');
    const uploadForm = document.getElementById('uploadForm');
    const imageUpload = document.getElementById('imageUpload');
    const previewContainer = document.querySelector('.preview-container');
          const worksTab = document.querySelector('[data-tab="works"]');
          const followingTab = document.querySelector('[data-tab="following"]');
          const tutorialsTab = document.querySelector('[data-tab="tutorials"]');
    
          const worksContent = document.querySelector('.works-grid');
          const followingContent = document.querySelector('.following-grid');
          const tutorialsContent = document.querySelector('.tutorials-grid');

          // Обработчик для таба "Работы"
          worksTab.addEventListener('click', () => {
              worksContent.style.display = 'grid';
              followingContent.style.display = 'none';
              if(tutorialsContent) tutorialsContent.style.display = 'none';
        
              worksTab.classList.add('active');
              followingTab.classList.remove('active');
              if(tutorialsTab) tutorialsTab.classList.remove('active');
          });

          // Обработчик для таба "Подписки"
          followingTab.addEventListener('click', () => {
              worksContent.style.display = 'none';
              followingContent.style.display = 'grid';
              if(tutorialsContent) tutorialsContent.style.display = 'none';
        
              worksTab.classList.remove('active');
              followingTab.classList.add('active');
              if(tutorialsTab) tutorialsTab.classList.remove('active');
          });

          // Обработчик для таба "Туториалы"
          if(tutorialsTab) {
              tutorialsTab.addEventListener('click', () => {
                  worksContent.style.display = 'none';
                  followingContent.style.display = 'none';
                  tutorialsContent.style.display = 'grid';
            
                  worksTab.classList.remove('active');
                  followingTab.classList.remove('active');
                  tutorialsTab.classList.add('active');
              });
          }
      });    if (createWorkBtn) {
        createWorkBtn.addEventListener('click', () => {
            modal.style.display = 'block';
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }

    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            // ... existing code ...
        });
    }

    imageUpload.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContainer.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" style="max-width: 100%; max-height: 100%; border-radius: 10px;">
                `;
            }
            reader.readAsDataURL(file);
        }
    });


function loadUserAwards() {
    const awardsContainer = document.querySelector('.awards-container');
    
    fetch('/api/get_awards.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(awards => {
            awards.forEach(award => {
                const awardElement = `
                    <div class="award-item" title="${award.description}">
                        <img src="/uploads/awards/${award.icon}" alt="${award.name}">
                        <span class="award-name">${award.name}</span>
                    </div>
                `;
                awardsContainer.insertAdjacentHTML('beforeend', awardElement);
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
}const uploadForm = document.getElementById('uploadForm');

uploadForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('upload_design.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Добавляем новую работу в сетку без перезагрузки
            const worksGrid = document.querySelector('.works-grid');
            const newWorkHTML = `
                <div class="work-item">
                    <img src="uploads/designs/${data.image_path}" alt="${data.title}">
                    <div class="work-info">
                        <h3>${data.title}</h3>
                        <div class="work-stats">
                            <span>★ 0.0</span>
                            <span>👁 0</span>
                        </div>
                    </div>
                </div>
            `;
            worksGrid.insertAdjacentHTML('beforeend', newWorkHTML);
            
            // Закрываем модальное окно
            modal.style.display = 'none';
            uploadForm.reset();
        }
    });
});

document.querySelector('.follow-btn')?.addEventListener('click', async function() {
    const userId = this.dataset.userId;
    const isFollowing = this.classList.contains('following');
    
    const response = await fetch('/api/follow.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `user_id=${userId}&action=${isFollowing ? 'unfollow' : 'follow'}`
    });
    
    if (response.ok) {
        this.classList.toggle('following');
        this.textContent = isFollowing ? 'Follow' : 'Following';
    }
});
document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.tab');
    const contentSections = document.querySelectorAll('[data-content]');

    console.log('Found tabs:', tabs);
    console.log('Found sections:', contentSections);

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetTab = tab.getAttribute('data-tab');
            
            tabs.forEach(t => t.classList.remove('active'));
            contentSections.forEach(section => section.classList.remove('active'));
            
            tab.classList.add('active');
            const targetContent = document.querySelector(`[data-content="${targetTab}"]`);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });

    // Обработчик для dropmenu
});