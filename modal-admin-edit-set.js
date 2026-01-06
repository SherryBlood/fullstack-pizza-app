document.addEventListener("DOMContentLoaded", function () {
  const modal = document.querySelector(".backdrop-sets-edit");
  const closeModalBtn = document.querySelector(".modal-sets-edit-btn-close");
  const openModalButtons = document.querySelectorAll(".modal-edit-set-btn-open");

  const nameInput = document.getElementById("set-name-edit");
  const descriptionInput = document.getElementById("set-description-edit");
  const priceInput = document.getElementById("set-price-edit");


  const form = document.querySelector('form[name="modal-form-edit-set"]');

  openModalButtons.forEach(button => {
    button.addEventListener("click", function () {
      const id = this.getAttribute("data-id");
      const name = this.getAttribute("data-name");
      const description = this.getAttribute("data-description");
      const price = this.getAttribute("data-price");


      nameInput.value = name;
      descriptionInput.value = description;
      priceInput.value = price;



      let hiddenIdInput = document.getElementById("edit-set-id");
      if (!hiddenIdInput) {
        hiddenIdInput = document.createElement("input");
        hiddenIdInput.type = "hidden";
        hiddenIdInput.name = "set-id";
        hiddenIdInput.id = "edit-set-id";
        form.appendChild(hiddenIdInput);
      }
      hiddenIdInput.value = id;

      modal.classList.remove("is-hidden");
    });
  });

  closeModalBtn.addEventListener("click", function () {
    modal.classList.add("is-hidden");
  });
});
