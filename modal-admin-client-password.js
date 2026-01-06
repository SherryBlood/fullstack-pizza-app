const modal = document.querySelector('.backdrop-admin-client-pass');
const closeBtn = document.querySelector('.modal-btn-close-pass');
const changeButtons = document.querySelectorAll('.change-password-btn');

const emailInput = document.getElementById('client-email-admin');
const passwordInput = document.getElementById('client-new-password-admin');
const confirmInput = document.getElementById('client-accept-password-admin');


let hiddenIdInput = document.getElementById('client-id-admin');
if (!hiddenIdInput) {
    hiddenIdInput = document.createElement('input');
    hiddenIdInput.type = 'hidden';
    hiddenIdInput.name = 'client-id';
    hiddenIdInput.id = 'client-id-admin';
    document.querySelector('.modal-pass-client form').appendChild(hiddenIdInput);
}

changeButtons.forEach(button => {
    button.addEventListener('click', () => {
        const email = button.getAttribute('data-email');
        const id = button.getAttribute('data-id');

        emailInput.value = email;
        hiddenIdInput.value = id;
        passwordInput.value = '';
        confirmInput.value = '';

        modal.classList.remove('is-hidden');
    });
});

closeBtn.addEventListener('click', () => {
    modal.classList.add('is-hidden');
});


    const registerForm = document.querySelector('form[name="modal-form-admin-client-pass"]');
    const password = document.getElementById("client-new-password-admin");
    const confirmPassword = document.getElementById("client-accept-password-admin");
    const submitButton = registerForm?.querySelector(".form-button.button");

    if (registerForm && password && confirmPassword && submitButton) {
        const errorText = document.createElement("p");
        errorText.classList.add("client-error-accept");
        errorText.style.color = "var(--color-secondary-red)";
        errorText.style.fontSize = "14px";
        errorText.style.marginTop = "2px";
        errorText.textContent = "Паролі не співпадають.";
        errorText.style.display = "none";
        confirmPassword.parentNode.appendChild(errorText);

        function validatePasswords() {
            if (password.value !== confirmPassword.value || password.value === "") {
                errorText.style.display = "block";
                submitButton.disabled = true;
            } else {
                errorText.style.display = "none";
                submitButton.disabled = false;
            }
        }

        confirmPassword.addEventListener("input", validatePasswords);
        password.addEventListener("input", validatePasswords);

        registerForm.addEventListener("submit", function (event) {
            if (password.value !== confirmPassword.value) {
                event.preventDefault();
            }
        });

        submitButton.disabled = true;
    }