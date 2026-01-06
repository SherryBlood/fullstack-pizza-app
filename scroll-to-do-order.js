document.getElementById('scroll-to-do-order-btn').addEventListener('click', function () {
  const target = document.getElementById('to-do-order-btn');
  if (target) {
    target.scrollIntoView({ behavior: 'smooth' });
  }
});
