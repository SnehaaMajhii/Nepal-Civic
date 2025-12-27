/**
 * Nepal Civic - Main JavaScript
 * Handles real-time validation and UI feedback.
 */

document.addEventListener('DOMContentLoaded', function() {

    const registerForm = document.getElementById('registrationForm');

    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let isValid = true;

            // 1. Reset Errors
            document.querySelectorAll('.val-error').forEach(el => el.textContent = "");
            document.querySelectorAll('input').forEach(el => el.style.borderColor = "#ccc");

            // 2. Helper function to show error
            const setError = (id, msg, inputName) => {
                document.getElementById(id).textContent = msg;
                registerForm.querySelector(`input[name="${inputName}"]`).style.borderColor = "red";
                isValid = false;
            };

            // 3. Email Validation
            const email = registerForm.querySelector('input[name="email"]').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                setError('email-err', "Enter a valid email address.", 'email');
            }

            // 4. Password Validation (8+ chars, 1 letter, 1 number)
            const password = registerForm.querySelector('input[name="password"]').value;
            const passRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
            if (!passRegex.test(password)) {
                setError('pass-err', "Min 8 chars, must include letters and numbers.", 'password');
            }

            // 5. National ID (Citizenship) Validation
            // Allows numbers, dashes, and slashes. Min 5 characters.
            const nationalId = registerForm.querySelector('input[name="national_id"]').value.trim();
            const idRegex = /^[0-9\/\-]{5,20}$/; 
            if (!idRegex.test(nationalId)) {
                setError('id-err', "Invalid format (Numbers, - or / only).", 'national_id');
            }

            // 6. Basic empty checks for other fields
            if (registerForm.querySelector('input[name="name"]').value.trim() === "") {
                setError('name-err', "Full name is required.", 'name');
            }
            if (registerForm.querySelector('input[name="address"]').value.trim() === "") {
                setError('addr-err', "Address is required.", 'address');
            }

            if (!isValid) {
                e.preventDefault(); // Stop submission
            }
        });
    }

    // --- AUTO-HIDE SYSTEM MESSAGES ---
    const systemAlerts = document.querySelectorAll('.error-msg');
    systemAlerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

});