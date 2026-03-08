document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.calendar .month.month-current')?.scrollIntoView({
        behavior: 'smooth',
        inline: 'start',
        block: 'nearest'
    });
});
