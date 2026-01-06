document.addEventListener("DOMContentLoaded", function () {
  const modal = document.querySelector(".backdrop-produсts-sets");
  const openBtn = document.querySelector(".modal-add-products-sets-btn-open");
  const closeBtn = document.querySelector(".modal-produсts-sets-btn-close");

  if (openBtn && modal && closeBtn) {
    openBtn.addEventListener("click", () => {
      modal.classList.remove("is-hidden");
    });
    closeBtn.addEventListener("click", () => {
      modal.classList.add("is-hidden");
    });
  }
});