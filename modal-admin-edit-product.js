document.addEventListener("DOMContentLoaded", function () {
  const modal = document.querySelector(".backdrop-products-edit");
  const closeModalBtn = document.querySelector(".modal-products-edit-btn-close");
  const openModalButtons = document.querySelectorAll(".modal-edit-product-btn-open");

  const categoryInput = document.getElementById("product-category-edit");
  const nameInput = document.getElementById("product-name-edit");
  const descriptionInput = document.getElementById("product-description-edit");
  const priceInput = document.getElementById("product-price-edit");

  const hiddenPromoProductId = document.getElementById("product-id-for-promo");

  const form = document.querySelector('form[name="modal-form-edit-product"]');

  openModalButtons.forEach(button => {
    button.addEventListener("click", function () {
      const id = this.getAttribute("data-id");
      const category = this.getAttribute("data-category");
      const name = this.getAttribute("data-name");
      const description = this.getAttribute("data-description");
      const price = this.getAttribute("data-price");


      categoryInput.value = category;
      nameInput.value = name;
      descriptionInput.value = description;
      priceInput.value = price;

      hiddenPromoProductId.value = id;


      let hiddenIdInput = document.getElementById("edit-product-id");
      if (!hiddenIdInput) {
        hiddenIdInput = document.createElement("input");
        hiddenIdInput.type = "hidden";
        hiddenIdInput.name = "product-id";
        hiddenIdInput.id = "edit-product-id";
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
