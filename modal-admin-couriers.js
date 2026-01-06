document.addEventListener("DOMContentLoaded", function () {
  const modal = document.querySelector(".backdrop-admin-add-courier");
  const openBtn = document.querySelector(".modal-add-btn-open");
  const closeBtn = document.querySelector(".modal-btn-close-add-courier");

  if (openBtn && modal && closeBtn) {
    openBtn.addEventListener("click", () => {
      modal.classList.remove("is-hidden");
    });
    closeBtn.addEventListener("click", () => {
      modal.classList.add("is-hidden");
    });
  }
});
