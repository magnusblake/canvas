
document.addEventListener('DOMContentLoaded', function() {
    const infoHeader = document.querySelector('.info-header');
    const awardsHeader = document.querySelector('.awards-header');

    infoHeader.addEventListener('click', function() {
        const content = document.querySelector('.info-content');
        const arrow = this.querySelector('.vector');
        content.classList.toggle('visible');
        arrow.classList.toggle('rotated');
    });

    awardsHeader.addEventListener('click', function() {
        const content = document.querySelector('.awards-content');
        const arrow = this.querySelector('.vector');
        content.classList.toggle('visible');
        arrow.classList.toggle('rotated');
    });
      // Обработка подписки
      function handleSubscribe(button) {
          const userId = button.dataset.userId;
          const isSubscribed = button.classList.contains('subscribed');
        
          fetch('/api/follow.php', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: `user_id=${userId}&action=${isSubscribed ? 'unfollow' : 'follow'}`
          })
          .then(response => {
              if (response.ok) {
                  button.classList.toggle('subscribed');
                  button.textContent = isSubscribed ? 'Подписаться' : 'Отписаться';
                  
                  // Update followers count
                  const followersCountElement = document.querySelector('.stat-item:nth-child(2) .stat-value');
                  let currentCount = parseInt(followersCountElement.textContent);
                  followersCountElement.textContent = isSubscribed ? currentCount - 1 : currentCount + 1;
              }
          });
      }
      document.querySelectorAll('.subscribe-btn').forEach(btn => {
          btn.addEventListener('click', function() {
              handleSubscribe(this);
          });
      });
});
