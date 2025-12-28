/**
 * Nepal Civic - Main JavaScript
 * Handles strict real-time validation and UI feedback.
 */

document.addEventListener('DOMContentLoaded', function() {

    const registerForm = document.getElementById('registrationForm');

    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let isValid = true;

            // 1. Reset Errors and Borders
            document.querySelectorAll('.val-error').forEach(el => el.textContent = "");
            document.querySelectorAll('input').forEach(el => {
                el.style.borderColor = "#ccc"; // Reset to default gray
            });

            // 2. Helper function to show error
            const setError = (id, msg, inputName) => {
                const errorElement = document.getElementById(id);
                if (errorElement) {
                    errorElement.textContent = msg;
                }
                const input = registerForm.querySelector(`input[name="${inputName}"]`);
                if (input) {
                    input.style.borderColor = "#C41E3A"; // Nepal Crimson Red
                }
                isValid = false;
            };

            // 3. Full Name Check
            const name = registerForm.querySelector('input[name="name"]').value.trim();
            if (name === "") {
                setError('name-err', "Full name is required.", 'name');
            }

            // 4. Email Validation
            const email = registerForm.querySelector('input[name="email"]').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                setError('email-err', "Enter a valid email address.", 'email');
            }

            // 5. Password Validation (8+ chars, 1 letter, 1 number)
            const password = registerForm.querySelector('input[name="password"]').value;
            const passRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
            if (!passRegex.test(password)) {
                setError('pass-err', "Min 8 chars, including letters and numbers.", 'password');
            }

            // 6. STRICT Citizenship Validation
            // Required Format: 00-00-00-00000
            // Regex Breakdown: 2 digits, dash, 2 digits, dash, 2 digits, dash, 5 digits
            const nationalId = registerForm.querySelector('input[name="national_id"]').value.trim();
            const strictIdRegex = /^[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{5}$/; 
            
            if (!strictIdRegex.test(nationalId)) {
                setError('id-err', "Strict format required: 00-00-00-00000", 'national_id');
            }

            // 7. Address Check
            const address = registerForm.querySelector('input[name="address"]').value.trim();
            if (address === "") {
                setError('addr-err', "Address is required.", 'address');
            }

            // Prevent submission if validation fails
            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // --- AUTO-HIDE SYSTEM MESSAGES (Login/Register alerts) ---
    const systemAlerts = document.querySelectorAll('.error-msg');
    systemAlerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

});