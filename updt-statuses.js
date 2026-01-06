setInterval(() => {
  fetch("update_statuses.php?t=" + new Date().getTime())
    .then(res => res.text())
    .then(data => console.log("Statuses updated successfully"));
}, 60000);
