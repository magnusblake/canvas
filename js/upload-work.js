class ImageUploader {
    constructor() {
        this.uploadZone = document.getElementById('uploadZone');
        this.imageInput = document.getElementById('imageInput');
        this.previewGrid = document.getElementById('previewGrid');
        this.uploadForm = document.getElementById('uploadForm');
        this.addMoreBtn = document.getElementById('addMoreBtn');
        this.uploadPlaceholder = document.querySelector('.upload-placeholder');
        
        this.images = [];
        this.selectedLayout = null;
        this.mainPhotoIndex = 0;
        
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

        this.initEvents();
        this.initFormSubmit();
    }

    initEvents() {
        this.uploadZone.onclick = () => this.imageInput.click();
        
        this.imageInput.onchange = (e) => {
            const files = Array.from(e.target.files);
            if (files.length > 5) {
                alert('Максимум 5 изображений');
                return;
            }
            this.handleFiles(files);
        };

        document.querySelectorAll('.template').forEach(template => {
            template.onclick = () => {
                const imageCount = parseInt(template.dataset.images);
                if (this.images.length === imageCount) {
                    this.applyLayout(imageCount);
                } else {
                    alert(`Загрузите ${imageCount} изображений для этого шаблона`);
                }
            };
        });

        if (this.addMoreBtn) {
            this.addMoreBtn.onclick = () => this.imageInput.click();
        }
    }

    initFormSubmit() {
        if (this.uploadForm) {
            this.uploadForm.onsubmit = (e) => {
                e.preventDefault();
                const formData = new FormData(this.uploadForm);
                
                // Add images
                this.images.forEach((image, index) => {
                    formData.append(`images[]`, image.file);
                });
                
                formData.append('main_photo_index', this.mainPhotoIndex);
                formData.append('layout_type', this.selectedLayout || 'grid');

                fetch('api/upload-work.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'profile.php';
                    }
                });
            };
        }
    }

    handleFiles(files) {
        files.forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.images.push({
                    file: file,
                    preview: e.target.result
                });
                this.updatePreview();
            };
            reader.readAsDataURL(file);
        });
    }

    updatePreview() {
        this.previewGrid.innerHTML = this.images.map((image, index) => `
            <div class="preview-item">
                <img src="${image.preview}" alt="Preview">
                <div class="main-photo-badge" data-index="${index}">
                    Сделать главной
                </div>
                <div class="delete-badge" data-index="${index}">×</div>
            </div>
        `).join('');

        document.querySelectorAll('.preview-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                if (!e.target.closest('.main-photo-badge')) {
                    const index = item.querySelector('.delete-badge').dataset.index;
                    this.deleteImage(index);
                }
            });
        });

        document.querySelectorAll('.main-photo-badge').forEach(badge => {
            badge.addEventListener('click', (e) => {
                e.stopPropagation();
                const index = badge.dataset.index;
                this.setMainPhoto(index);
            });
        });

        this.previewGrid.classList.add('active');
        this.uploadZone.classList.add('has-images');
        if (this.addMoreBtn) {
            this.addMoreBtn.hidden = false;
        }
    }

    setMainPhoto(index) {
        this.mainPhotoIndex = parseInt(index);
        document.querySelectorAll('.preview-item').forEach(item => {
            item.classList.remove('is-main');
        });
        document.querySelectorAll('.preview-item')[index].classList.add('is-main');
    }

    deleteImage(index) {
        this.images.splice(index, 1);
        this.updatePreview();
        if (this.images.length > 0) {
            this.applyLayout(this.images.length);
        } else {
            this.resetUploader();
        }
    }

    resetUploader() {
        this.uploadZone.classList.remove('has-images');
        this.previewGrid.classList.remove('active');
        if (this.addMoreBtn) {
            this.addMoreBtn.hidden = true;
        }
        if (this.uploadPlaceholder) {
            this.uploadPlaceholder.style.display = 'block';
        }
    }

    applyLayout(count) {
        this.selectedLayout = count;
        this.previewGrid.style = this.layouts[count].grid;
    }
}

new ImageUploader();
