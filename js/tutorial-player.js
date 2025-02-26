  document.addEventListener('DOMContentLoaded', function() {
      const video = document.querySelector('#tutorialVideo');
      const playBtn = document.querySelector('.play-btn');
      const volumeBtn = document.querySelector('.volume-btn');
      const fullscreenBtn = document.querySelector('.fullscreen-btn');
    
      // Simple play/pause
      playBtn.addEventListener('click', () => {
          if(video.paused) {
              video.play();
          } else {
              video.pause();
          }
      });
    
      // Mute/unmute
      volumeBtn.addEventListener('click', () => {
          video.muted = !video.muted;
      });
    
      // Fullscreen
      fullscreenBtn.addEventListener('click', () => {
          if (!document.fullscreenElement) {
              video.requestFullscreen();
          } else {
              document.exitFullscreen();
          }
      });
  });