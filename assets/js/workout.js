document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.form-row .btn-description-show').forEach(btn => {
        btn.onclick = () => btn.closest('.form-row').classList.add('description-show');
    })
});
