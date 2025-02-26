class TopProjectsSlider {
    constructor() {
        this.mainProject = document.querySelector('.frame-6');
        this.sideProjects = Array.from(document.querySelectorAll('.image-wrapper'));
        this.nextButton = document.querySelector('.frame-13:not(.prev)');
        this.prevButton = document.querySelector('.frame-13.prev');
        this.frame5 = document.querySelector('.frame-5');
        this.currentIndex = 0;
        this.totalProjects = this.sideProjects.length + 1;
        this.visibleCount = 5;
        
        // Initial setup
        this.mainProject.style.width = '680px';
        this.mainProject.style.left = '0';
        this.prevButton.style.display = 'none';
        
        // Position side projects
        this.sideProjects.forEach((project, index) => {
            project.style.width = '96px';
            project.style.left = `${700 + (index * 116)}px`;
            if (index >= 4) {
                project.style.display = 'none';
                project.style.opacity = '0';
                project.style.transition = 'opacity 0.3s ease';
            }
        });
        
        this.nextButton.addEventListener('click', () => this.handleNext());
        this.prevButton.addEventListener('click', () => this.handlePrev());
}
    handleNext() {
        const allProjects = [this.mainProject, ...this.sideProjects];
    
        if (this.currentIndex < this.totalProjects - 1) {
            this.currentIndex++;
        } else {
            // Reset to beginning when reaching the end
            this.currentIndex = 0;
        }
    
        // Reset all projects
        allProjects.forEach(project => {
            project.style.width = '96px';
            project.style.transition = 'all 0.3s ease';
        });
    
        // Set current project to large
        allProjects[this.currentIndex].style.width = '680px';
    
        // Position all visible projects
        let currentLeft = 0;
    
        // Show current and next 4 projects
        for (let i = this.currentIndex; i < this.currentIndex + 5 && i < allProjects.length; i++) {
            const width = i === this.currentIndex ? 680 : 96;
            allProjects[i].style.display = 'block';
            allProjects[i].style.opacity = '1';
            allProjects[i].style.left = `${currentLeft}px`;
            currentLeft += width + 20;
        }
    
        // Hide all other projects
        for (let i = 0; i < this.currentIndex; i++) {
            allProjects[i].style.opacity = '0';
            allProjects[i].style.display = 'none';
        }
    
        for (let i = this.currentIndex + 5; i < allProjects.length; i++) {
            allProjects[i].style.opacity = '0';
            allProjects[i].style.display = 'none';
        }
    
        // Handle prev button visibility
        this.prevButton.style.display = this.currentIndex > 0 ? 'flex' : 'none';
    }
    handlePrev() {
        const allProjects = [this.mainProject, ...this.sideProjects];
    
        if (this.currentIndex > 0) {
            this.currentIndex--;
        
            // Reset all projects
            allProjects.forEach(project => {
                project.style.width = '96px';
                project.style.transition = 'all 0.3s ease';
            });
        
            // Set current project to large
            allProjects[this.currentIndex].style.width = '680px';
        
            // Position all visible projects
            let currentLeft = 0;
        
            // Show current and next 4 projects
            for (let i = this.currentIndex; i < this.currentIndex + 5 && i < allProjects.length; i++) {
                const width = i === this.currentIndex ? 680 : 96;
                allProjects[i].style.display = 'block';
                allProjects[i].style.opacity = '1';
                allProjects[i].style.left = `${currentLeft}px`;
                currentLeft += width + 20;
            }
        
            // Hide all other projects
            for (let i = 0; i < this.currentIndex; i++) {
                allProjects[i].style.opacity = '0';
                allProjects[i].style.display = 'none';
            }
        
            for (let i = this.currentIndex + 5; i < allProjects.length; i++) {
                allProjects[i].style.opacity = '0';
                allProjects[i].style.display = 'none';
            }
        
            // Handle prev button visibility
            this.prevButton.style.display = this.currentIndex > 0 ? 'flex' : 'none';
        }
    }}

new TopProjectsSlider();

// В методе где создаются проекты добавляем overlay

