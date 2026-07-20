window.addEventListener('load', () => {
    const currentMonth = document.querySelector('#calendar .month.month-current');
    if (!currentMonth) {
        return;
    }

    currentMonth.scrollIntoView({behavior: 'smooth', inline: 'start'});

    setTimeout(() => {
        currentMonth.scrollIntoView({behavior: 'smooth', inline: 'start'});
    }, 100);
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('#calendar-date button.calendar-event-guess').forEach(button => {
        button.onclick = event => {
            event.preventDefault();

            const form = button.closest('form');

            form.querySelector('input#title').value = button.dataset.eventTitle;
            form.querySelector(`input#type_${button.dataset.generalType}`).checked = true;
            form.querySelector('input#time').value = button.dataset.eventTime ?? null;
        }
    });
});

