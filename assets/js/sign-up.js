const registerForm = document.getElementById('registerForm');
const firstNameInput = document.getElementById('firstName');
const lastNameInput = document.getElementById('lastName');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const confirmPasswordInput = document.getElementById('confirmPassword');
const firstNameError = document.getElementById('firstNameError');
const lastNameError = document.getElementById('lastNameError');
const emailError = document.getElementById('emailError');
const passwordError = document.getElementById('passwordError');
const confirmPasswordError = document.getElementById('confirmPasswordError');

// Email validation regex
const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

// Password validation criteria
const passwordPattern = /^(?=.*[A-Z])(?=.*\d{3,})(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/;

registerForm.addEventListener('submit', function(event) {
// Prevent form submission
event.preventDefault();

// Clear previous error messages
firstNameError.textContent = '';
lastNameError.textContent = '';
emailError.textContent = '';
passwordError.textContent = '';
confirmPasswordError.textContent = '';

let isValid = true;

// Validate first name
if (firstNameInput.value.trim() === '') {
    firstNameError.textContent = 'First name is required.';
    isValid = false;
}

// Validate last name
if (lastNameInput.value.trim() === '') {
    lastNameError.textContent = 'Last name is required.';
    isValid = false;
}

// Validate email format
if (!emailPattern.test(emailInput.value)) {
    emailError.textContent = 'Please enter a valid email address.';
    isValid = false;
}

// Validate password strength
if (!passwordPattern.test(passwordInput.value)) {
    passwordError.textContent = 'Password must be at least 8 characters, contain an uppercase letter, three digits, and a special character.';
    isValid = false;
}

});