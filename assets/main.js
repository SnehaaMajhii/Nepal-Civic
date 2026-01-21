/**
 * Nepal Civic - Main JavaScript
 * Covers: Admin Modals, Registration Validation, and UI Alerts.
 */

/* =========================================
   PART 1: ADMIN DASHBOARD LOGIC
   (Must be outside DOMContentLoaded so HTML buttons can see these functions)
   ========================================= */

// 1. Open Modal by ID
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = "block";
}

// 2. Close Modal by ID
function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = "none";
}

// 3. Toggle Admin Decision Fields
// If Reject -> Show Reason Box. If Approve -> Show Ward Assignment & Date.
function toggleDecision(id) {
    const select = document.getElementById('decision-' + id);
    const reasonBox = document.getElementById('reason-box-' + id);
    const assignBox = document.getElementById('assign-box-' + id); 
    
    // Inputs to toggle 'required' attribute
    const textarea = reasonBox.querySelector('textarea');
    const wardSelect = assignBox.querySelector('select');
    const dateInput = assignBox.querySelector('input[type="date"]');

    if (select && select.value === 'reject') {
        reasonBox.style.display = 'block';
        assignBox.style.display = 'none';
        
        if(textarea) textarea.setAttribute('required', 'required');
        if(wardSelect) wardSelect.removeAttribute('required');
        if(dateInput) dateInput.removeAttribute('required');
    } else {
        reasonBox.style.display = 'none';
        assignBox.style.display = 'block';
        
        if(textarea) textarea.removeAttribute('required');
        if(wardSelect) wardSelect.setAttribute('required', 'required');
        if(dateInput) dateInput.setAttribute('required', 'required');
    }
}

// 4. Close modal if user clicks outside the white box
window.onclick = function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.style.display = "none";
    }
};


/* =========================================
   PART 2: REGISTRATION & FORM VALIDATION
   (Runs after the page loads)
   ========================================= */

document.addEventListener('DOMContentLoaded', function() {

    // --- A. REGISTRATION FORM VALIDATION ---
    const registerForm = document.getElementById('registrationForm');

    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let isValid = true;

            // 1. Reset Errors and Borders
            document.querySelectorAll('.val-error').forEach(el => el.textContent = "");
            document.querySelectorAll('input').forEach(el => {
                el.style.borderColor = "#ccc"; 
            });

            // 2. Helper function to show error
            const setError = (id, msg, inputName) => {
                const errorElement = document.getElementById(id);
                if (errorElement) errorElement.textContent = msg;
                
                const input = registerForm.querySelector(`input[name="${inputName}"]`);
                if (input) input.style.borderColor = "#C41E3A"; // Crimson Red
                
                isValid = false;
            };

            // 3. Full Name Check
            const nameInput = registerForm.querySelector('input[name="name"]');
            if (nameInput && nameInput.value.trim() === "") {
                setError('name-err', "Full name is required.", 'name');
            }

            // 4. Email Validation
            const emailInput = registerForm.querySelector('input[name="email"]');
            if (emailInput) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailInput.value.trim())) {
                    setError('email-err', "Enter a valid email address.", 'email');
                }
            }

            // 5. Password Validation (8+ chars, letters + numbers)
            const passInput = registerForm.querySelector('input[name="password"]');
            if (passInput) {
                const passRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
                if (!passRegex.test(passInput.value)) {
                    setError('pass-err', "Min 8 chars, including letters and numbers.", 'password');
                }
            }

            // 6. STRICT Citizenship Validation (Format: 00-00-00-00000)
            const idInput = registerForm.querySelector('input[name="national_id"]');
            if (idInput) {
                const strictIdRegex = /^[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{5}$/; 
                if (!strictIdRegex.test(idInput.value.trim())) {
                    setError('id-err', "Strict format required: 00-00-00-00000", 'national_id');
                }
            }

            // 7. Address Check
            const addrInput = registerForm.querySelector('input[name="address"]');
            if (addrInput && addrInput.value.trim() === "") {
                setError('addr-err', "Address is required.", 'address');
            }

            // Prevent submission if validation fails
            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // --- B. AUTO-HIDE SYSTEM MESSAGES ---
    // Automatically fades out green/red success messages after 5 seconds
    const systemAlerts = document.querySelectorAll('.error-msg, .success-msg');
    systemAlerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

});