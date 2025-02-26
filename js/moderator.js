class ModeratorSwipe {
    constructor() {
        this.card = document.querySelector('.card-container');
        this.approveBtn = document.querySelector('.approve-btn');
        this.rejectBtn = document.querySelector('.reject-btn');
        this.imagesSection = document.querySelector('.images-section');
        
        this.isDragging = false;
        this.startX = 0;
        this.currentX = 0;
        this.threshold = 100;

        this.layouts = {
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

        this.setupLayout();
        this.setupButtons();
        this.setupSwipe();
    }

    setupLayout() {
        if (this.imagesSection) {
            const imagesCount = this.imagesSection.children.length;
            if (this.layouts[imagesCount]) {
                this.imagesSection.style = this.layouts[imagesCount].grid;
            }
        }
    }

    setupButtons() {
        if (this.approveBtn && this.rejectBtn) {
            this.approveBtn.addEventListener('click', () => this.processAction('approve'));
            this.rejectBtn.addEventListener('click', () => this.processAction('reject'));
        }
    }

    setupSwipe() {
        if (!this.card) return;

        this.card.addEventListener('mousedown', (e) => {
            this.isDragging = true;
            this.startX = e.clientX;
            this.card.style.transition = 'none';
        });

        document.addEventListener('mousemove', (e) => {
            if (!this.isDragging) return;
            
            this.currentX = e.clientX - this.startX;
            const rotate = this.currentX * 0.1;
            
            this.card.style.transform = `translateX(${this.currentX}px) rotate(${rotate}deg)`;
            
            if (this.currentX > this.threshold) {
                this.card.classList.add('approve-indicator');
                this.card.classList.remove('reject-indicator');
            } else if (this.currentX < -this.threshold) {
                this.card.classList.add('reject-indicator');
                this.card.classList.remove('approve-indicator');
            } else {
                this.card.classList.remove('approve-indicator', 'reject-indicator');
            }
        });

        document.addEventListener('mouseup', () => {
            if (!this.isDragging) return;
            
            this.isDragging = false;
            this.card.style.transition = 'transform 0.5s ease';
            
            if (Math.abs(this.currentX) > this.threshold) {
                const direction = this.currentX > 0 ? 'approve' : 'reject';
                const throwDistance = direction === 'approve' ? window.innerWidth : -window.innerWidth;
                
                this.card.style.transform = `translateX(${throwDistance}px) rotate(${this.currentX > 0 ? 45 : -45}deg)`;
                this.processAction(direction);
            } else {
                this.card.style.transform = '';
            }
            
            this.card.classList.remove('approve-indicator', 'reject-indicator');
        });
    }

    processAction(action) {
        if (!this.card) return;
        
        const designId = this.card.dataset.id;
        fetch('api/moderate-design.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                designId: designId,
                action: action
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new ModeratorSwipe();
});
