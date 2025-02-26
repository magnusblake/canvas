class Interactions {
    static async likeDesign(designId) {
        const response = await fetch('/api/like.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `design_id=${designId}&action=like`
        });
        return response.json();
    }

    static async unlikeDesign(designId) {
        const response = await fetch('/api/like.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `design_id=${designId}&action=unlike`
        });
        return response.json();
    }

    static async followUser(userId) {
        const response = await fetch('/api/subscribe.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `user_id=${userId}&action=follow`
        });
        return response.json();
    }

    static async unfollowUser(userId) {
        const response = await fetch('/api/subscribe.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `user_id=${userId}&action=unfollow`
        });
        return response.json();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Открытие модального окна при клике на работу
    document.querySelectorAll('.work-item').forEach(item => {
        item.addEventListener('click', () => {
            const designId = item.dataset.id;
            workModal.open(designId);
        });
    });

    // Обработка лайков
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation(); // Предотвращаем открытие модалки
            const designId = btn.dataset.designId;
            handleLike(designId, btn);
        });
    });
});

async function handleLike(designId, button) {
    try {
        const response = await fetch('api/like.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `design_id=${designId}`
        });
        
        if (response.ok) {
            button.classList.toggle('liked');
        }
    } catch (error) {
        console.error('Error:', error);
    }
}