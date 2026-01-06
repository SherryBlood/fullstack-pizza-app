document.addEventListener("DOMContentLoaded", function () {
  const deleteButtons = document.querySelectorAll(".delete-client-btn");

  deleteButtons.forEach(button => {
    button.addEventListener("click", function () {
      const id = this.getAttribute("data-id");

      if (!confirm("Are you sure you want to delete this crust?")) return;

      fetch("delete_crust.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "crust_id=" + encodeURIComponent(id)
      })
      .then(response => response.text())
      .then(result => {
        alert("Crust deleted successfully!");
        location.reload();
      })
      .catch(err => {
        alert("Error deleting crust.");
        console.error(err);
      });
    });
  });
});
