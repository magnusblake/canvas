document.addEventListener('DOMContentLoaded', function() {
    initializeImageUploads();
    initializeScrollSpy();
});

function initializeImageUploads() {
    const avatarInput = document.getElementById('avatar');
    const bannerInput = document.getElementById('banner');
    const avatarImg = document.querySelector('.avatar-upload img');
    const bannerImg = document.querySelector('.banner-upload img');

    // Avatar upload handling
    document.querySelector('.avatar-upload').onclick = () => avatarInput.click();
    avatarInput.onchange = () => handleImageUpload(avatarInput, avatarImg);

    // Banner upload handling
    document.querySelector('.banner-upload').onclick = () => bannerInput.click();
    bannerInput.onchange = () => handleImageUpload(bannerInput, bannerImg);
}

function handleImageUpload(input, imgElement) {
    if (input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => imgElement.src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}

function initializeScrollSpy() {
    const sections = document.querySelectorAll('.input-section');
    const navItems = document.querySelectorAll('.nav-item');

    window.addEventListener('scroll', () => {
        const scrollPosition = window.scrollY + window.innerHeight / 2;
        let current = '';

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;

            if (scrollPosition >= sectionTop && scrollPosition <= sectionTop + sectionHeight) {
                current = section.getAttribute('id');
            }
        });

        navItems.forEach(item => {
            const href = item.getAttribute('href');
            item.classList.toggle('active', href && href.substring(1) === current);
        });
    });
}
