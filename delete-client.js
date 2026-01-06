document.addEventListener("DOMContentLoaded", function () {
  const deleteButtons = document.querySelectorAll(".delete-client-btn");

  deleteButtons.forEach(button => {
    button.addEventListener("click", function () {
      const id = parseInt(this.getAttribute("data-id"));

      if (id === 1) {
        alert("This client cannot be deleted.");
        return;
      }

      if (!confirm("Are you sure you want to delete this client?")) return;

      fetch("delete_client.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "client_id=" + encodeURIComponent(id)
      })
        .then(response => response.text())
        .then(result => {
          alert("Client deleted successfully!");
          location.reload();
        })
        .catch(err => {
          alert("Error deleting client.");
          console.error(err);
        });
    });
  });
});
