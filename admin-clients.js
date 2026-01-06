function editInfoClient() {
  document.querySelectorAll('.client-edit-input').forEach(input => input.removeAttribute('readonly'));
  document.getElementById('save-changes-client-btn').disabled = false;
}