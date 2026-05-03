document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.exercise .btn-description-show').forEach(btn => {
        btn.onclick = () => btn.closest('.exercise').classList.add('exercise-description-show');
    })
});
