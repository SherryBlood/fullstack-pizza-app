document.addEventListener("DOMContentLoaded", function () {
  window.editInfoClient = function () {
    const inputs = document.querySelectorAll('.form-input-info-client');
    const changeButton = document.querySelector('.client-change-button');

    inputs.forEach(input => {
      input.removeAttribute('readonly');
    });

    changeButton.disabled = false;


    inputs[0].focus();
  };
});
