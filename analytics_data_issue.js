const convertTimeToNumber = (timeStr) => {
  const [h, m] = timeStr.split(':').map(Number);
  return h + m / 60;
};

fetch('analytics_data_issue.php')
  .then(res => res.json())
  .then(data => {

    const readyPoints = data.pickup.map(d => ({ x: convertTimeToNumber(d.ready), y: convertTimeToNumber(d.ready) }));
    const issuedPoints = data.pickup.map(d => ({ x: convertTimeToNumber(d.issued), y: convertTimeToNumber(d.issued) }));

    const plannedPoints = data.delivery.map(d => ({ x: convertTimeToNumber(d.planned), y: convertTimeToNumber(d.planned) }));
    const actualPoints = data.delivery.map(d => ({ x: convertTimeToNumber(d.actual), y: convertTimeToNumber(d.actual) }));

    const commonOptions = {
      responsive: true,
      plugins: {
        tooltip: {
          callbacks: {
            label: ctx => {
              const h = Math.floor(ctx.parsed.x);
              const m = Math.round((ctx.parsed.x - h) * 60).toString().padStart(2, '0');
              return `${ctx.dataset.label}: ${h}:${m}`;
            }
          }
        }
      },
      scales: {
        x: {
          type: 'linear',
          min: 8,
          max: 23,
          title: { display: true, text: 'Time (hours)' },
          ticks: {
            stepSize: 0.5,
            callback: v => {
              const h = Math.floor(v);
              const m = v % 1 === 0.5 ? '30' : '00';
              return `${h}:${m}`;
            }
          }
        },
        y: {
          type: 'linear',
          min: 8,
          max: 23,
          title: { display: true, text: 'Time (hours)' },
          ticks: {
            stepSize: 1,
            callback: v => `${Math.floor(v)}:00`
          }
        }
      }
    };

    const createChart = (ctx, title, datasets) => new Chart(ctx, {
      type: 'scatter',
      data: {
        datasets: datasets.map(ds => ({
          label: ds.label,
          data: ds.data,
          borderColor: ds.borderColor,
          backgroundColor: ds.backgroundColor,
          showLine: true,
          fill: false,
          pointRadius: 5,
          tension: 0.3
        }))
      },
      options: {
        ...commonOptions,
        plugins: {
          ...commonOptions.plugins,
          title: { display: true, text: title }
        }
      }
    });

    createChart(
      document.getElementById('pickupChart').getContext('2d'),
      'Pickup (Ready vs Picked Up)',
      [
        { label: 'Ready time', data: readyPoints, borderColor: 'orange', backgroundColor: 'orange' },
        { label: 'Pickup time', data: issuedPoints, borderColor: 'green', backgroundColor: 'green' }
      ]
    );

    createChart(
      document.getElementById('deliveryChart').getContext('2d'),
      'Delivery (Planned vs Actual)',
      [
        { label: 'Estimated delivery', data: plannedPoints, borderColor: 'blue', backgroundColor: 'blue' },
        { label: 'Actual delivery', data: actualPoints, borderColor: 'red', backgroundColor: 'red' }
      ]
    );
  });
