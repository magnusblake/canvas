class WorkModal {
    constructor() {
        this.modal = document.getElementById('workModal');
        this.imagesSection = this.modal.querySelector('.images-section');
        this.closeBtn = this.modal.querySelector('.design-modal__close');
        
        this.layouts = {
            1: {
                grid: `grid-template-columns: 1fr;
                      grid-template-rows: 1fr;
                      gap: 20px;`
            },
            2: {
                grid: `grid-template-columns: 1.2fr 0.8fr;
                      grid-template-rows: 1fr;
                      gap: 20px;`
            },
            3: {
                grid: `grid-template-columns: repeat(2, 1fr);
                      grid-template-rows: 1fr 1fr;
                      gap: 20px;
                      grid-template-areas: 
                        "featured featured"
                        "second third";`
            },
            4: {
                grid: `grid-template-columns: repeat(3, 1fr);
                      grid-template-rows: 1fr 1fr;
                      gap: 20px;
                      grid-template-areas: 
                        "big big normal1"
                        "normal2 normal3 normal1";`
            },
            5: {
                grid: `grid-template-columns: repeat(6, 1fr);
                      grid-template-rows: repeat(6, 1fr);
                      gap: 20px;
                      grid-template-areas: 
                        "main main main side1 side1 side2"
                        "main main main side1 side1 side2"
                        "main main main side3 side3 side4"
                        "main main main side3 side3 side4";`
            }
        };

        this.commentForm = this.modal.querySelector('.comment-form');
        this.commentInput = this.modal.querySelector('.comment-input');
        this.commentsList = this.modal.querySelector('.comments-list');

        this.bindEvents();
        this.submitComment = this.submitComment.bind(this); // ФИКСИМ THIS
        this.bindCommentEvents();
    }

    bindEvents() {
        if (this.closeBtn) {
            this.closeBtn.onclick = () => this.close();
        }
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.close();
        });
    }

    bindCommentEvents() {
        console.log('НАЧИНАЕМ СЛУШАТЬ КОММЕНТЫ!');
        
        // ТОЛЬКО ОДИН ОБРАБОТЧИК НА ФОРМУ
        this.commentForm.addEventListener('submit', (e) => {
            console.log('ФОРМА ОТПРАВЛЯЕТСЯ!');
            e.preventDefault();
            this.submitComment();
        });
        
        // УБИРАЕМ ОБРАБОТЧИК С КНОПКИ - ОН НЕ НУЖЕН
        // Форма сама обработает клик по кнопке submit
    }

    submitComment() {
        console.log('ОТПРАВЛЯЕМ КОММЕНТ!');
        const text = this.commentInput.value.trim();
        if (!text) return;

        fetch('api/add-comment.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({
                design_id: this.currentWorkId,
                text: text
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('ПОЛУЧИЛИ ОТВЕТ:', data);
            if (data.success) {
                this.commentsList.insertAdjacentHTML('afterbegin', this.createCommentHTML(data.comment));
                this.commentInput.value = '';
            }
        });
    }

    createCommentHTML(comment) {
        // Only create HTML if we have all required data
        if (!comment.username || !comment.text) return '';
      
        const avatar = comment.user_avatar || 'default-avatar.svg';
        const date = new Date(comment.created_at).toLocaleString();
      
        return `
            <div class="comment-item">
                <img src="uploads/avatars/${avatar}" class="comment-avatar" alt="${comment.username}" onerror="this.src='uploads/avatars/default-avatar.png'">
                <div class="comment-content">
                    <div class="comment-header">
                        <a href="profile.php?id=${comment.user_id}" class="comment-author">${comment.username}</a>
                        <span class="comment-date">${date}</span>
                    </div>
                    <div class="comment-text">${comment.text}</div>
                </div>
            </div>
        `;
    }

      loadComments(workId) {
          fetch(`api/get-comments.php?design_id=${workId}`)
              .then(response => response.json())
              .then(data => {
                  if (data.success) {
                      this.renderComments(data.comments);
                  }
              });
      }

      renderComments(comments) {
          this.commentsList.innerHTML = comments.map(comment => 
              this.createCommentHTML(comment)).join('');
      }

    createCommentHTML(comment) {
        const date = new Date(comment.created_at).toLocaleString();
        return `
            <div class="comment-item">
                <img src="uploads/avatars/${comment.user_avatar}" class="comment-avatar" alt="${comment.username}">
                <div class="comment-content">
                    <div class="comment-header">
                        <a href="profile.php?id=${comment.user_id}" class="comment-author">${comment.username}</a>
                        <span class="comment-date">${date}</span>
                    </div>
                    <div class="comment-text">${comment.text}</div>
                </div>
            </div>
        `;
    }

    open(workId) {
        this.currentWorkId = workId;
        
        // Track view when modal opens
        fetch('api/handle_view.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ design_id: workId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update view count in modal if needed
                const viewsElement = this.modal.querySelector('.design-modal__views');
                if (viewsElement) {
                    viewsElement.textContent = data.views_count;
                }
            }
        });

        // Existing modal open code
        fetch(`api/get-work.php?id=${workId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.fillModalData(data.data);
                    this.modal.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                    this.loadComments(workId);
                }
            });
    }

    fillModalData(data) {
        // Проверяем и логируем данные
        console.log('Received data:', data);
        
        let allImages = [...data.images];
        
        // Базовая проверка на существование изображений
        if (allImages.length === 0 && data.image_path) {
            allImages.push({
                image_path: data.image_path,
                is_main: '1',
                position: 0
            });
        }

        const imagesSection = this.modal.querySelector('.design-modal__images');
        imagesSection.setAttribute('data-count', allImages.length);
        
        // Применяем layout в зависимости от количества изображений
        if (this.layouts[allImages.length]) {
            imagesSection.style.cssText = this.layouts[allImages.length].grid;
        }
        
        imagesSection.innerHTML = '';

        // Отрисовываем изображения
        allImages.forEach((img, index) => {
            const imageDiv = document.createElement('div');
            imageDiv.className = 'design-modal__image';
            
            const image = new Image();
            image.src = `uploads/designs/${img.image_path}`;
            image.alt = data.title || '';
            
            imageDiv.appendChild(image);
            imagesSection.appendChild(imageDiv);
        });

        const elements = {
            '.design-modal__avatar': el => el.src = `uploads/avatars/${data.author_avatar}`,
            '.design-modal__author-name': el => {
                el.textContent = data.author_name;
                el.href = `profile.php?id=${data.user_id}`;
            },
            '.design-modal__title': el => el.textContent = data.title,
            '.design-modal__description': el => el.textContent = data.description,
            '.design-modal__likes': el => el.textContent = data.likes_count || '0',
            '.design-modal__views': el => el.textContent = data.views_count || '0'
        };

        Object.entries(elements).forEach(([selector, update]) => {
            const element = this.modal.querySelector(selector);
            if (element) update(element);
        });
    }

    getGridArea(totalImages, index) {
        switch(totalImages) {
            case 3: return ['featured', 'second', 'third'][index];
            case 4: return ['big', 'normal1', 'normal2', 'normal3'][index];
            case 5: return ['main', 'side1', 'side2', 'side3', 'side4'][index];
            default: return '';
        }
    }

    close() {
        if (this.modal) {
            this.modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
}

const workModal = new WorkModal();
window.WorkModal = WorkModal;
window.workModal = workModal;

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.project-card').forEach(card => {
        card.addEventListener('click', (e) => {
            if (!e.target.closest('.action-btn')) {
                const workId = card.dataset.id;
                if (workId) workModal.open(workId);
            }
        });
    });
});
