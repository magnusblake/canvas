class ProjectsCarousel {
    constructor() {
        this.items = document.querySelectorAll('.carousel-item');
        this.navButton = document.getElementById('carouselNext');
        this.currentIndex = 0;
        this.maxIndex = this.items.length - 1;
        
        this.navButton.addEventListener('click', () => this.handleNavigation());
    }
    
    handleNavigation() {
        if (this.currentIndex === this.maxIndex) {
            // Возвращаемся к началу
            this.currentIndex = 0;
            this.items.forEach(item => item.classList.remove('compressed'));
            this.navButton.classList.remove('reversed');
        } else {
            // Переход к следующему проекту
            this.items[this.currentIndex].classList.add('compressed');
            this.currentIndex++;
            
            if (this.currentIndex === this.maxIndex) {
                this.navButton.classList.add('reversed');
            }
        }
        
        this.items.forEach((item, index) => {
            item.classList.toggle('active', index === this.currentIndex);
        });
    }
}
