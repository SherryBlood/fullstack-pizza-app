document.addEventListener("DOMContentLoaded", function () {
  const input = document.querySelector('.client-order-datetime');

  function pad(num) {
    return String(num).padStart(2, '0');
  }

  function getValidMinTime() {
    const now = new Date();
    const future = new Date(now.getTime() + 41 * 60000);

    const earliestToday = new Date();
    earliestToday.setHours(8, 0, 0, 0);

    const latestToday = new Date();
    latestToday.setHours(22, 0, 0, 0);

    if (future < earliestToday) {
      return earliestToday;
    } else if (future > latestToday) {
      const tomorrow = new Date(now.getTime() + 86400000);
      tomorrow.setHours(8, 0, 0, 0);
      return tomorrow;
    }

    return future;
  }

  function updateTimeInput() {
    const minTime = getValidMinTime();
    const formatted = `${minTime.getFullYear()}-${pad(minTime.getMonth() + 1)}-${pad(minTime.getDate())}T${pad(minTime.getHours())}:${pad(minTime.getMinutes())}`;

    input.value = formatted;
    input.min = formatted;
  }

  updateTimeInput();
  setInterval(updateTimeInput, 60000);
});
