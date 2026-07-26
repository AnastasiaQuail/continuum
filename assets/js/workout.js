document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.form-row .btn-description-show').forEach(btn => {
        btn.onclick = () => btn.closest('.form-row').classList.add('description-show');
    })

    document.querySelectorAll('.form-row .btn-container-toggler').forEach(btn => {
        btn.onclick = () => btn.closest('.card-container').classList.remove('card-container-collapse');
    })
});
