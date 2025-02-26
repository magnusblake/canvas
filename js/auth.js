document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('registerForm');

    // Get the form by ID
    const loginForm = document.getElementById('loginForm');

    // Add event listener with preventDefault
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault(); // STOP IT RIGHT HERE! 
        e.stopPropagation(); // DOUBLE KILL THE RELOAD!
        
        const formData = new FormData(this);
        
        fetch('api/auth/login.php', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Manual redirect only after successful login
                window.location.href = '/';
            } else {
                const errorDiv = document.querySelector('.error-message');
                errorDiv.textContent = data.message;
                errorDiv.style.display = 'block';
            }
        });
        
        return false; // TRIPLE KILL THE RELOAD! 
    });
    
    
    registerForm?.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(registerForm);
        
        fetch('api/auth/register.php', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                showError(registerForm, data.message);
            }
        });
    });
});

function showError(form, message) {
    const errorDiv = form.querySelector('.error-message') || document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    form.insertBefore(errorDiv, form.firstChild);
}

// Check auth status on page load
fetch('api/auth/check_auth.php', {
    credentials: 'include'
})
.then(response => response.json())
.then(data => {
    if (data.success && data.isAuthenticated) {
        document.body.classList.add('authenticated');
    }
});


function updateUI(isLoggedIn) {
    const authButtons = document.querySelector('.auth-buttons');
    const userMenu = document.querySelector('.user-menu');
    
    if (isLoggedIn) {
        authButtons.style.display = 'none';
        userMenu.style.display = 'block';
    } else {
        authButtons.style.display = 'block';
        userMenu.style.display = 'none';
    }
}

document.getElementById('logoutBtn').addEventListener('click', function(e) {
    e.preventDefault();
    
    fetch('src/handlers/logout.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'index.php';
            }
        });
});

// Handle dropdown and auth modal
document.addEventListener('DOMContentLoaded', () => {
    const userDropdown = document.getElementById('userDropdown');
    const authModal = document.querySelector('.auth-modal');
    
    if (userDropdown) {
        userDropdown.addEventListener('click', (e) => {
            if (!window.isUserAuthenticated) {
                e.preventDefault();
                e.stopPropagation();
                // Kill bootstrap dropdown
                const bsDropdown = bootstrap.Dropdown.getInstance(userDropdown);
                if (bsDropdown) {
                    bsDropdown.hide();
                }
                // Show auth modal
                authModal.style.display = 'flex';
            }
        });
    }
    
    // Close modal on outside click
    authModal.addEventListener('click', (e) => {
        if (e.target === authModal) {
            authModal.style.display = 'none';
        }
    });
});
