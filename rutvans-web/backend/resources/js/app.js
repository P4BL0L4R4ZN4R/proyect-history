import './bootstrap';

document.addEventListener('show.bs.modal', function (event) {
    const modal = event.target;
    modal.setAttribute('aria-hidden', 'false');
});

document.addEventListener('hide.bs.modal', function (event) {
    const modal = event.target;
    modal.setAttribute('aria-hidden', 'true');
});
