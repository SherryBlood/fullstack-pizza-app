
const modalAdmin = document.querySelector('.backdrop-admin');
const modalBtnOpenAdmin = document.querySelector('.modal-btn-open-admin');
const modalBtnCloseAdmin = document.querySelector('.modal-btn-close-admin');

const toggleModalAdmin = () => modalAdmin.classList.toggle('is-hidden');

modalBtnOpenAdmin.addEventListener('click', toggleModalAdmin);
modalBtnCloseAdmin.addEventListener('click', toggleModalAdmin);


const modalCourier = document.querySelector('.backdrop-courier');
const modalBtnOpenCourier = document.querySelector('.modal-btn-open-courier');
const modalBtnCloseCourier = document.querySelector('.modal-btn-close-courier');

const toggleModalCourier = () => modalCourier.classList.toggle('is-hidden');

modalBtnOpenCourier.addEventListener('click', toggleModalCourier);
modalBtnCloseCourier.addEventListener('click', toggleModalCourier);