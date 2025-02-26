document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('tutorialForm');
    const thumbnailInput = form.querySelector('input[name="thumbnail"]');
    const previewDiv = document.querySelector('.thumbnail-preview');

    thumbnailInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewDiv.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            };
            reader.readAsDataURL(file);
        }
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        try {
            const response = await fetch('api/upload-tutorial.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                window.location.href = 'profile.php';
            } else {
                alert(data.message || 'Произошла ошибка при загрузке');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Произошла ошибка при загрузке');
        }
    });
});
