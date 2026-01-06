document.addEventListener("DOMContentLoaded", function () {
  const deleteButtons = document.querySelectorAll(".delete-client-btn");

  deleteButtons.forEach(button => {
    button.addEventListener("click", function () {
      const id = this.getAttribute("data-id");

      if (!confirm("Are you sure you want to delete this ingredient?")) return;

      fetch("delete_ingredient.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "ingredient_id=" + encodeURIComponent(id)
      })
      .then(response => response.text())
      .then(result => {
        alert("Ingredient deleted successfully!");
        location.reload();
      })
      .catch(err => {
        alert("Error deleting ingredient.");
        console.error(err);
      });
    });
  });
});
