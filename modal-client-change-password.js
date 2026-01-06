document.addEventListener("DOMContentLoaded", function () {
  const modal = document.querySelector(".backdrop-change-client-pass");
  const openBtn = document.querySelector(".client-change-password-button");
  const closeBtn = document.querySelector(".modal-btn-close-pass-client");

  const form = document.querySelector('form[name="modal-form-changge-client-pass"]');
  const nowPasswordInput = document.getElementById("client-now-pass-changge");
  const newPasswordInput = document.getElementById("client-new-password-changge");
  const confirmPasswordInput = document.getElementById("client-accept-password-changge");
  const submitButton = form?.querySelector("button[type='submit']");

  if (!modal || !form || !openBtn || !closeBtn || !newPasswordInput || !confirmPasswordInput || !submitButton) return;


  openBtn.addEventListener("click", () => {
    modal.classList.remove("is-hidden");
    nowPasswordInput.value = "";
    newPasswordInput.value = "";
    confirmPasswordInput.value = "";
    submitButton.disabled = true;
  });


  closeBtn.addEventListener("click", () => {
    modal.classList.add("is-hidden");
  });


  const errorText = document.createElement("p");
  errorText.classList.add("client-error-accept");
  errorText.style.color = "var(--color-secondary-red)";
  errorText.style.fontSize = "14px";
  errorText.style.marginTop = "2px";
  errorText.textContent = "Passwords do not match.";
  errorText.style.display = "none";
  confirmPasswordInput.parentNode.appendChild(errorText);

  function validatePasswords() {
    if (newPasswordInput.value !== confirmPasswordInput.value || newPasswordInput.value === "") {
      errorText.style.display = "block";
      submitButton.disabled = true;
    } else {
      errorText.style.display = "none";
      submitButton.disabled = false;
    }
  }

  newPasswordInput.addEventListener("input", validatePasswords);
  confirmPasswordInput.addEventListener("input", validatePasswords);

  form.addEventListener("submit", function (event) {
    if (newPasswordInput.value !== confirmPasswordInput.value) {
      event.preventDefault();
    }
  });

  submitButton.disabled = true;
});
