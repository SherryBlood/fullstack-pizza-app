function highPriceAdmin() {
  sortProducts('desc');
}

function lowPriceAdmin() {
  sortProducts('asc');
}

function sortProducts(order) {
  const container = document.getElementById('products-container');
  if (!container) {
    console.error('Контейнер з продуктами не знайдено.');
    return;
  }

  const products = Array.from(container.querySelectorAll('.menu-pizzas-container'));


  products.sort((a, b) => {
    const priceA = parseFloat(a.querySelector('h4').innerText.replace(',', '.').replace(/[^\d.]/g, ''));
    const priceB = parseFloat(b.querySelector('h4').innerText.replace(',', '.').replace(/[^\d.]/g, ''));
    return order === 'asc' ? priceA - priceB : priceB - priceA;
  });


  products.forEach(product => {
    product.style.opacity = '0';
  });


  setTimeout(() => {
    container.innerHTML = '';
    products.forEach(product => {
      product.style.opacity = '0';
      container.appendChild(product);
      setTimeout(() => {
        product.style.opacity = '1';
      }, 50);
    });
  }, 400);
}
