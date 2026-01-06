document.addEventListener("DOMContentLoaded", function () {
  const modal = document.querySelector(".backdrop-add-crust");
  const openBtn = document.querySelector(".modal-add-crust-btn-open");
  const closeBtn = document.querySelector(".modal-add-crust-btn-close");

  if (openBtn && modal && closeBtn) {
    openBtn.addEventListener("click", () => {
      modal.classList.remove("is-hidden");
    });
    closeBtn.addEventListener("click", () => {
      modal.classList.add("is-hidden");
    });
  }
});