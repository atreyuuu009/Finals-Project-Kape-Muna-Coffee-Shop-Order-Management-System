// script.js

// Form elements
const form = document.getElementById('staffForm');
const roleCards = document.querySelectorAll('.role-card');
const cancelBtn = document.getElementById('cancelBtn');
const successModal = document.getElementById('successModal');
const closeModal = document.getElementById('closeModal');

// Hidden role input (for PHP)
const roleInput = document.getElementById('role');

let selectedRole = null;

// Role selection
roleCards.forEach(card => {
    card.addEventListener('click', function() {
        roleCards.forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');

        selectedRole = this.getAttribute('data-role');
        roleInput.value = selectedRole; // IMPORTANT

        document.getElementById('roleError').classList.remove('show');
    });
});

// Real-time validation for username
document.getElementById('username').addEventListener('input', function(e) {
    const username = e.target.value;
    const error = document.getElementById('usernameError');

    if (username.length > 0 && username.length < 4) {
        error.textContent = 'Username must be at least 4 characters long';
        error.classList.add('show');
        e.target.classList.add('error');
    } else {
        error.classList.remove('show');
        e.target.classList.remove('error');
    }
});

// Real-time validation for email
document.getElementById('email').addEventListener('input', function(e) {
    const email = e.target.value;
    const error = document.getElementById('emailError');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email.length > 0 && !emailRegex.test(email)) {
        error.textContent = 'Please enter a valid email address';
        error.classList.add('show');
        e.target.classList.add('error');
    } else {
        error.classList.remove('show');
        e.target.classList.remove('error');
    }
});

// Real-time validation for confirm password
document.getElementById('confirmPassword').addEventListener('input', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = e.target.value;
    const error = document.getElementById('confirmPasswordError');

    if (confirmPassword.length > 0 && password !== confirmPassword) {
        error.textContent = 'Passwords do not match';
        error.classList.add('show');
        e.target.classList.add('error');
    } else {
        error.classList.remove('show');
        e.target.classList.remove('error');
    }
});

// Form validation function
function validateForm() {
    let isValid = true;

    // First Name validation
    const firstName = document.getElementById('firstName').value.trim();
    if (!firstName) {
        document.getElementById('firstNameError').textContent = 'First name is required';
        document.getElementById('firstNameError').classList.add('show');
        document.getElementById('firstName').classList.add('error');
        isValid = false;
    }

    // Last Name validation
    const lastName = document.getElementById('lastName').value.trim();
    if (!lastName) {
        document.getElementById('lastNameError').textContent = 'Last name is required';
        document.getElementById('lastNameError').classList.add('show');
        document.getElementById('lastName').classList.add('error');
        isValid = false;
    }

    // Email validation
    const email = document.getElementById('email').value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email) {
        document.getElementById('emailError').textContent = 'Email is required';
        document.getElementById('emailError').classList.add('show');
        document.getElementById('email').classList.add('error');
        isValid = false;
    } else if (!emailRegex.test(email)) {
        document.getElementById('emailError').textContent = 'Please enter a valid email';
        document.getElementById('emailError').classList.add('show');
        document.getElementById('email').classList.add('error');
        isValid = false;
    }

    // Username validation
    const username = document.getElementById('username').value.trim();
    if (!username) {
        document.getElementById('usernameError').textContent = 'Username is required';
        document.getElementById('usernameError').classList.add('show');
        document.getElementById('username').classList.add('error');
        isValid = false;
    } else if (username.length < 4) {
        document.getElementById('usernameError').textContent = 'Username must be at least 4 characters';
        document.getElementById('usernameError').classList.add('show');
        document.getElementById('username').classList.add('error');
        isValid = false;
    }

    // Password validation
    const password = document.getElementById('password').value;
    const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$/;
    if (!password) {
        document.getElementById('passwordError').textContent = 'Password is required';
        document.getElementById('passwordError').classList.add('show');
        document.getElementById('password').classList.add('error');
        isValid = false;
    } else if (!passwordRegex.test(password)) {
        document.getElementById('passwordError').textContent = 'Password must be 8+ characters with letters and numbers';
        document.getElementById('passwordError').classList.add('show');
        document.getElementById('password').classList.add('error');
        isValid = false;
    }

    // Confirm Password validation
    const confirmPassword = document.getElementById('confirmPassword').value;
    if (!confirmPassword) {
        document.getElementById('confirmPasswordError').textContent = 'Please confirm password';
        document.getElementById('confirmPasswordError').classList.add('show');
        document.getElementById('confirmPassword').classList.add('error');
        isValid = false;
    } else if (password !== confirmPassword) {
        document.getElementById('confirmPasswordError').textContent = 'Passwords do not match';
        document.getElementById('confirmPasswordError').classList.add('show');
        document.getElementById('confirmPassword').classList.add('error');
        isValid = false;
    }

    // Role validation
    if (!selectedRole) {
        document.getElementById('roleError').textContent = 'Please select a role';
        document.getElementById('roleError').classList.add('show');
        isValid = false;
    }

    return isValid;
}

// Form submission -> let PHP handle insert + redirect
form.addEventListener('submit', function(e) {
    // Clear previous errors
    document.querySelectorAll('.error-message').forEach(el => el.classList.remove('show'));
    document.querySelectorAll('input').forEach(el => el.classList.remove('error'));

    if (!validateForm()) {
        e.preventDefault();
        return;
    }

    // Ensure hidden role is set
    roleInput.value = selectedRole || "";

    // Loading state (optional)
    const submitBtn = form.querySelector('.btn-submit');
    submitBtn.disabled = true;
    submitBtn.textContent = "Creating Account...";
    // DO NOT preventDefault -> submits to auth_register.php
});

// Cancel button
// Cancel button -> confirm then go back (fallback)
cancelBtn.addEventListener('click', function () {
    if (!confirm('Are you sure you want to cancel? All entered data will be lost.')) return;

    // Optional: clear form UI before leaving
    form.reset();
    roleCards.forEach(c => c.classList.remove('selected'));
    selectedRole = null;
    roleInput.value = "";
    document.querySelectorAll('.error-message').forEach(el => el.classList.remove('show'));
    document.querySelectorAll('input').forEach(el => el.classList.remove('error'));

    // Go back if possible, else fallback
    if (document.referrer && document.referrer !== window.location.href) {
        window.location.href = document.referrer;
    } else {
        window.location.href = "home.php"; // or dashboard.php
    }
});


// If your modal is ever shown manually, keep buttons consistent
closeModal.addEventListener('click', function() {
    window.location.href = 'login.php?new=true';
});

successModal.addEventListener('click', function(e) {
    if (e.target === successModal) {
        window.location.href = 'login.php?new=true';
    }
    
});

