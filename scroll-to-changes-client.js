document.getElementById('scroll-to-save-changes-client-btn').addEventListener('click', function () {
  const target = document.getElementById('save-changes-client-btn');
  if (target) {
    target.scrollIntoView({ behavior: 'smooth' });
  }
});
