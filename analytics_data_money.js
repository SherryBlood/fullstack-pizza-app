fetch('analytics_data_money.php')
  .then(res => res.json())
  .then(issuedOrders => {
    const now = new Date();
    const year = now.getFullYear();
    const month = now.getMonth();

    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const allDates = [];

    for (let day = 2; day <= daysInMonth+1; day++) {
      const date = new Date(year, month, day);
      const isoDate = date.toISOString().split('T')[0];
      allDates.push(isoDate);
    }

    const dailyTotals = {};
    issuedOrders.forEach(order => {
      const date = new Date(order.date).toISOString().split('T')[0];
      if (!dailyTotals[date]) {
        dailyTotals[date] = 0;
      }
      dailyTotals[date] += order.total;
    });

    const profits = allDates.map(date => +(dailyTotals[date] || 0).toFixed(2));

    const ctx = document.getElementById('moneyChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: allDates,
        datasets: [{
          label: 'Profit (USD)',
          data: profits,
          borderColor: 'rgba(75, 192, 192, 0.8)',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          fill: true,
          tension: 0.4,
          pointRadius: 4,
          pointBackgroundColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top'
          }
        },
        scales: {
          x: {
            title: {
              display: true,
              text: 'Date'
            },
            ticks: {
              maxRotation: 45,
              minRotation: 45
            }
          },
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Profit (USD)'
            }
          }
        }
      }
    });
  })
  .catch(error => {
    console.error('Data loading error:', error);
  });
