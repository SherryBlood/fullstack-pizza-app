fetch('analytics_data_category_month.php')
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


    const today = new Date();
    const year = today.getFullYear();
    const month = today.getMonth();
    const firstDay = new Date(year, month, 2);
    const lastDay = new Date(year, month + 1, 1);

    const allDates = [];
    let date = new Date(firstDay);
    while (date <= lastDay) {
      const isoDate = date.toISOString().split('T')[0];
      allDates.push(isoDate);
      date.setDate(date.getDate() + 1);
    }


    const datasets = Object.entries(data).map(([categoryKey, categoryData]) => {
      const dataPoints = allDates.map(date => categoryData[date] || 0);
      return {
        label: categoryNamesMap[categoryKey] || categoryKey,
        data: dataPoints,
        fill: false,
        tension: 0.3,
        borderWidth: 2
      };
    });

    new Chart(document.getElementById('categoryChartMonth').getContext('2d'), {
      type: 'line',
      data: {
        labels: allDates,
        datasets: datasets
      },
      options: {
        responsive: true,
        plugins: {
        },
        scales: {
          x: {
            title: {
              display: true,
              text: 'Date'
            },
            ticks: {
              maxRotation: 90,
              minRotation: 45
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
