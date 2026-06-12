document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('#calendar .month.month-current')?.scrollIntoView({
        behavior: 'smooth',
        inline: 'start',
        block: 'nearest'
    });

    document.querySelectorAll('#calendar-date .calendar-event-guesses .calendar-event-guess').forEach(button => {
        button.onclick = event => {
            event.preventDefault();

            const form = button.closest('form');

            form.querySelector('input#title').value = button.dataset.eventTitle;
            form.querySelector(`input#type_${button.dataset.generalType}`).checked = true;
            form.querySelector('input#time').value = button.dataset.eventTime ?? null;
        }
    });
});
