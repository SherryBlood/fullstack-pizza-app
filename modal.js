document.addEventListener("DOMContentLoaded", function () {
    const modal = document.querySelector('.backdrop');
    const modalBtnOpen = document.querySelector('.modal-btn-open');
    const modalBtnClose = document.querySelector('.modal-btn-close');

    const toggleModal = () => modal.classList.toggle('is-hidden');

    if (modalBtnOpen) modalBtnOpen.addEventListener('click', toggleModal);
    if (modalBtnClose) modalBtnClose.addEventListener('click', toggleModal);


    const registerForm = document.querySelector('form[name="modal-form-client-register"]');
    const password = document.getElementById("client-new-password");
    const confirmPassword = document.getElementById("client-accept-password");
    const submitButton = registerForm?.querySelector(".form-button.button");

    if (registerForm && password && confirmPassword && submitButton) {
        const errorText = document.createElement("p");
        errorText.classList.add("client-error-accept");
        errorText.style.color = "var(--color-secondary-red)";
        errorText.style.fontSize = "14px";
        errorText.style.marginTop = "2px";
        errorText.textContent = "Passwords do not match.";
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

});
