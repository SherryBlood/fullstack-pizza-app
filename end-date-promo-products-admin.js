const startDateInput = document.getElementById('product-start-date-add-promo');
const endDateInput = document.getElementById('product-end-date-add-promo');
startDateInput.addEventListener('change', function() {
  endDateInput.min = this.value;

  if (endDateInput.value < this.value) {
      endDateInput.value = '';
  }
});