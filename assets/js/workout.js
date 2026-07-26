document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.card-container-workout-exercise .btn-description-show').forEach(btn => {
        btn.onclick = () => btn.closest('.form-row').classList.add('description-show');
    })

    document.querySelectorAll('.card-container-workout-exercise.card-container-collapse .form-row-header-edit').forEach(btn => {
        btn.onclick = () => btn.closest('.card-container').classList.remove('card-container-collapse');
    })
});
