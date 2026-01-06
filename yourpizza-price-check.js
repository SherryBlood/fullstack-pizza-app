function ingredientPrice() {

  const baseElement = document.getElementById("pizza-base-price");
  const basePrice = parseFloat(baseElement.getAttribute("data-base-price")) || 0;
  let totalPrice = basePrice;


  document.querySelectorAll("input[type=checkbox]").forEach((checkbox) => {
    if (checkbox.checked) {
      totalPrice += parseFloat(checkbox.value);
      checkbox.nextElementSibling.classList.add("checked");
    } else {
      checkbox.nextElementSibling.classList.remove("checked");
    }
  });


  const selectedCrust = document.querySelector("input[name='client-basket-crust']:checked");
  if (selectedCrust) {
    const crustPrice = parseFloat(selectedCrust.getAttribute("data-price")) || 0;
    totalPrice += crustPrice;
  }


  document.getElementById("total-price").textContent =  "$" + totalPrice.toFixed(2);
}
