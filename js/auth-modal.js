class AuthModal {
    constructor() {
        this.modal = document.querySelector('.auth-modal');
        this.closeBtn = this.modal.querySelector('.modal-close');
        this.loginForm = document.getElementById('loginForm');
        this.registerForm = document.getElementById('registerForm');
        this.switchBtns = this.modal.querySelectorAll('.switch-auth');
        
        this.init();
    }

    init() {
        this.closeBtn.addEventListener('click', () => this.hide());
        
        this.switchBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const type = e.target.dataset.type;
                document.querySelector('.login').classList.toggle('active');
                document.querySelector('.register').classList.toggle('active');
            });
        });

        this.loginForm.addEventListener('submit', (e) => this.handleLogin(e));
        this.registerForm.addEventListener('submit', (e) => this.handleRegister(e));
    }

    handleLogin(e) {
        e.preventDefault();
        const formData = new FormData(this.loginForm);
        
        const data = {
            email: formData.get('email'),
            password: formData.get('password')
        };

        fetch('api/auth/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data),
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                this.showError(this.loginForm, data.message);
            }
        });
    }
    handleRegister(e) {
        e.preventDefault();
        const formData = new FormData(this.registerForm);
        
        const data = {
            name: formData.get('name'),
            username: formData.get('username'), 
            email: formData.get('email'),
            password: formData.get('password')
        };

        fetch('api/auth/register.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                this.showError(this.registerForm, data.message);
            }
        });
    }

    switchTab(tabName) {
        this.tabButtons.forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.auth-modal .tab-content').forEach(content => content.classList.remove('active'));
        
        document.querySelector(`.auth-modal .tab-btn[data-tab="${tabName}"]`).classList.add('active');
        document.querySelector(`.auth-modal .tab-content.${tabName}`).classList.add('active');
    }

    show() {
        this.modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    hide() {
        this.modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    showError(form, message) {
        const errorDiv = form.querySelector('.error-message') || document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        form.insertBefore(errorDiv, form.firstChild);
    }
}
// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    const authModal = new AuthModal();
    
    // Привязка к кнопке входа в шапке
    document.querySelector('.login-btn')?.addEventListener('click', () => {
        authModal.show();
    });
});
