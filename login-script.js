// login-script.js

// Form elements
const loginForm = document.getElementById('loginForm');
const usernameInput = document.getElementById('username');
const passwordInput = document.getElementById('password');
const successAlert = document.getElementById('successAlert');

// Check if coming from account creation (login.php?new=true)
window.addEventListener('load', function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('new') === 'true') {
        successAlert.style.display = 'block';
        setTimeout(function () {
            successAlert.style.display = 'none';
        }, 5000);
    }
});

// Login form validation function
function validateLoginForm() {
    let isValid = true;

    // Username validation
    const username = usernameInput.value.trim();
    const usernameError = document.getElementById('usernameError');

    if (!username) {
        usernameError.textContent = 'Username or email is required';
        usernameError.classList.add('show');
        usernameInput.classList.add('error');
        isValid = false;
    } else {
        usernameError.classList.remove('show');
        usernameInput.classList.remove('error');
    }

    // Password validation
    const password = passwordInput.value;
    const passwordError = document.getElementById('passwordError');

    if (!password) {
        passwordError.textContent = 'Password is required';
        passwordError.classList.add('show');
        passwordInput.classList.add('error');
        isValid = false;
    } else {
        passwordError.classList.remove('show');
        passwordInput.classList.remove('error');
    }

    return isValid;
}

// Form submission (NOW submits to PHP)
loginForm.addEventListener('submit', function (e) {
    // Clear previous errors
    document.querySelectorAll('.error-message').forEach(el => el.classList.remove('show'));
    document.querySelectorAll('input').forEach(el => el.classList.remove('error'));

    // Validate first
    if (!validateLoginForm()) {
        e.preventDefault();
        return;
    }

    // Optional loading state (PHP will redirect after login)
    const submitBtn = loginForm.querySelector('.btn-submit');
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Logging in...';

    // IMPORTANT:
    // Do not e.preventDefault() here.
    // The form will submit to auth_login.php (set in login.php action="auth_login.php").
});

// Remove error on input
usernameInput.addEventListener('input', function () {
    this.classList.remove('error');
    document.getElementById('usernameError').classList.remove('show');
});

passwordInput.addEventListener('input', function () {
    this.classList.remove('error');
    document.getElementById('passwordError').classList.remove('show');
});

// Forgot password link (still placeholder)
document.querySelector('.forgot-password').addEventListener('click', function (e) {
    e.preventDefault();
    alert('Password reset functionality would be implemented here.\n\nIn a real application, this would send a password reset email to the user.');
});
