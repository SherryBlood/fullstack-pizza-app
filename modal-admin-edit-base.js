document.addEventListener("DOMContentLoaded", function () {
  const modal = document.querySelector(".backdrop-base-edit");
  const closeModalBtn = document.querySelector(".modal-base-edit-btn-close");
  const openModalBtn = document.querySelector(".modal-edit-price-base-btn-open"); // одна кнопка

  const priceInput = document.getElementById("base-price-edit");

  openModalBtn.addEventListener("click", function () {
    const price = this.getAttribute("data-price");
    priceInput.value = price || '';
    modal.classList.remove("is-hidden");
  });

  closeModalBtn.addEventListener("click", function () {
    modal.classList.add("is-hidden");
  });
});
