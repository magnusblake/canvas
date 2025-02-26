<div class="design-modal" id="workModal">
    <div class="design-modal__content">
        <button class="design-modal__close">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M18 6L6 18M6 6l12 12" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        <div class="design-modal__layout">
            <div class="design-modal__images" id="modalImagesGrid"></div>
            <div class="design-modal__info">
                <div class="design-modal__header">
                    <div class="design-modal__author">
                        <img class="design-modal__avatar" src="" alt="">
                        <div class="design-modal__author-info">
                            <a class="design-modal__author-name" href=""></a>
                            <span class="design-modal__date"></span>
                        </div>
                    </div>
                    <h2 class="design-modal__title"></h2>
                    <p class="design-modal__description"></p>
                </div>
                <div class="design-modal__stats">
    <div class="design-modal__stat">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        <span class="design-modal__likes"></span>
    </div>
    <div class="design-modal__stat">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
        <span class="design-modal__views"></span>
    </div>
</div>

<div class="design-modal__comments">
    <h3 class="comments-title">Комментарии</h3>
    <div class="comments-list"></div>
    <form class="comment-form" onsubmit="return false;"> <!-- ВАЖНО! -->
        <textarea class="comment-input" placeholder="Напишите комментарий..."></textarea>
        <button type="submit" class="comment-submit">Отправить</button>
    </form>
</div>

            </div>

        </div>
    </div>
</div>


<!-- At the top of the file -->
<link rel="stylesheet" href="css/work-modal.css">

<!-- At the bottom of the file, after the modal HTML -->
<script src="js/work-modal.js"></script>
