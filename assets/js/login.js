
// Get the form and input elements
const loginForm = document.getElementById('loginForm');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const emailError = document.getElementById('emailError');
const passwordError = document.getElementById('passwordError');

// Email validation regex
const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

// Password validation criteria
const passwordPattern = /^(?=.*[A-Z])(?=.*\d{3,})(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/;

loginForm.addEventListener('submit', function(event) {
// Prevent form submission
event.preventDefault();

// Clear previous error messages 
emailError.textContent = '';
passwordError.textContent = '';

let isValid = true;

// Validate email format
if (!emailPattern.test(emailInput.value)) {
    emailError.textContent = 'Please enter a valid email address.';
    isValid = false;
}

});
