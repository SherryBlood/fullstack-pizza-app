function editInfoClient() {
  document.querySelectorAll('.client-edit-input:not(.readonly-always)').forEach(input => {
    input.removeAttribute('readonly');
  });
  document.getElementById('save-changes-client-btn').disabled = false;
}
