class TodayWorks {
    constructor() {
        this.container = document.querySelector('.platforms-container');
        this.platforms = [];
        this.loadWorks();
    }

    loadWorks() {
        fetch('api/get_today_works.php')
            .then(response => response.json())
            .then(works => {
                this.works = works;
                this.createPlatforms();
                this.animate();
            });
    }
    createPlatforms() {
        const positions = [
            // Row 1
            { x: -1000, y: -600 }, { x: -500, y: -600 }, { x: 0, y: -600 }, { x: 500, y: -600 }, { x: 1000, y: -600 },
            // Row 2 - плотный ряд без пропуска
            { x: -1000, y: -200 }, { x: -500, y: -200 }, { x: 0, y: -200 }, { x: 500, y: -200 },{ x: 1000, y: -200 },
            // Row 3 - центр с увеличенным пространством
            { x: -1200, y: 200 }, { x: -700, y: 200 }, { x: 700, y: 200 }, { x: 1200, y: 200 },
            // Row 4
            { x: -1000, y: 600 }, { x: -500, y: 600 }, { x: 0, y: 600 }, { x: 500, y: 600 }, { x: 1000, y: 600 },
            // Row 5
            { x: -1000, y: 1000 }, { x: -500, y: 1000 }, { x: 0, y: 1000 }, { x: 500, y: 1000 }, { x: 1000, y: 1000 }
        ];
    
    
        // Only create platforms for available works
        for(let i = 0; i < Math.min(positions.length, this.works.length); i++) {
            const platform = this.createPlatform(positions[i].x, positions[i].y, i);
            this.platforms.push(platform);
            this.container.appendChild(platform);
        }
    }

    createPlatform(x, y, index) {
        // Создаем основную платформу
        const platform = document.createElement('div');
        platform.className = 'platform';
        
        // Создаем фоновую платформу
        
        // Добавляем изображения если есть работа
        if (this.works[index]) {
            const img = document.createElement('img');
            img.src = `uploads/designs/${this.works[index].image_path}`;
            platform.appendChild(img);
        }

        platform.style.transform = `translate3d(${x}px, ${y}px, 0)`;

        return platform;
    }
    animate() {
        const animate = () => {
            const time = Date.now() * 0.001;
            
            this.platforms.forEach((platform, index) => {
                const delay = index * 0.4;
                const waveProgress = (time - delay) % 4;
                let offset = 0;
                
                if (waveProgress < 1) {
                    offset = Math.sin(waveProgress * Math.PI) * 100;
                }
                
                const baseTransform = platform.style.transform.split('translateZ')[0];
                platform.style.transform = `${baseTransform} translateZ(${offset}px)`;
            });
            
            requestAnimationFrame(animate);
        };
        
        animate();
    }
}

new TodayWorks();
