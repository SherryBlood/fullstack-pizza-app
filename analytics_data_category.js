fetch('analytics_data_category.php')
  .then(res => res.json())
  .then(data => {
    const categoryNamesMap = {
      'pizza': 'Pizza',
      'pizza-roll': 'Pizza Rolls',
      'snack': 'Snacks',
      'dessert': 'Desserts',
      'drink': 'Drinks',
      'set': 'Sets',
      'custom': 'Custom Pizzas'
    };

    const desiredOrder = ['pizza', 'pizza-roll', 'snack', 'dessert', 'drink', 'set', 'custom'];

    const labels = [];
    const values = [];

    desiredOrder.forEach(key => {
      if (data[key] !== undefined) {
        labels.push(categoryNamesMap[key]);
        values.push(data[key]);
      }
    });

    new Chart(document.getElementById('categoryChart').getContext('2d'), {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Order count by category',
          data: values,
          backgroundColor: 'rgba(255, 99, 132, 0.6)'
        }]
      },
      options: {
        responsive: true,
        scales: {
          x: {
            title: {
              display: true,
              text: 'Category'
            }
          },
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Quantity'
            }
          }
        }
      }
    });
  });
