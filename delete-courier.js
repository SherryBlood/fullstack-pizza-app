document.addEventListener("DOMContentLoaded", function () {
  const deleteButtons = document.querySelectorAll(".delete-client-btn");

  deleteButtons.forEach(button => {
    button.addEventListener("click", function () {
      const id = this.getAttribute("data-id");

      if (!confirm("This courier cannot be deleted.")) return;

      fetch("delete_courier.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "courier_id=" + encodeURIComponent(id)
      })
      .then(response => response.text())
      .then(result => {
        alert("Courier deleted successfully!");
        location.reload();
      })
      .catch(err => {
        alert("Error deleting courier.");
        console.error(err);
      });
    });
  });
});
