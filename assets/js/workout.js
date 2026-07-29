document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.card-container-workout-exercise .btn-description-show').forEach(btn => {
        btn.onclick = () => btn.closest('.form-row').classList.add('description-show');
    })

    document.querySelectorAll('.card-container-workout-exercise.card-container-collapse .form-row-header').forEach(btn => {
        btn.onclick = () => btn.closest('.card-container').classList.remove('card-container-collapse');
    })

    document.querySelectorAll('.card-container-workout-exercise .form-subrow-time-last').forEach(element => {
        const elementTime = new Date(element.dataset.time * 1000);
        const label = element.querySelector('.form-time-offset');

        const intervalId = setInterval(
            () => {
                const minutes = Math.floor((new Date() - elementTime) / 1000 / 60);

                if (minutes > 60) {
                    label.innerText = '> ' + 60 + ' minutes';
                    clearInterval(intervalId);
                } else {
                    label.innerText = minutes + (minutes === 1 ? ' minute' : ' minutes');
                }

                if (minutes >= 10) {
                    element.dataset.generalType = minutes >= 20 ? 'red' : 'yellow';
                }

                element.classList.add('loading-show');
                setTimeout(() => element.classList.remove('loading-show'), 500);
            },
            10000
        )
    })
});
