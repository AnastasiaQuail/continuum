import './styles/app.css';
import ThemeToggler from './js/theme-toggler.js';
import {SidebarToggler, Sidebar} from './js/sidebar.js';

const themeToggler = new ThemeToggler();
themeToggler.init();

const sidebarToggler = new SidebarToggler();
sidebarToggler.init();

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('theme-toggler').onclick = () => themeToggler.toggle();
    document.getElementById('sidebar-toggler').onclick = () => sidebarToggler.toggle();

    new Sidebar('sidebar').init();

    const userTimezone = document.documentElement.dataset.timezone;
    const currentTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    // todo check hours offset and move to separate function/class
    if (userTimezone !== currentTimezone) {
        const offsetHours = -new Date().getTimezoneOffset() / 60;

        const alert = document.createElement('div');
        alert.classList.add('alert');
        alert.classList.add('alert-warning');
        alert.classList.add('alert-timezone-mismatch');
        document.getElementById('body-messages').prepend(alert);

        const mismatchText = document.createElement('strong');
        mismatchText.innerText = 'Time zone mismatch!';
        alert.append(mismatchText);

        const spanText = document.createElement('span');
        spanText.innerText = 'Save "' + currentTimezone
            + (offsetHours !== 0 ? ` (UTC${offsetHours > 0 ? '+' + offsetHours : offsetHours})` : '') + '"?';
        alert.append(spanText);

        const button = document.createElement('button');
        button.classList.add('btn');
        button.classList.add('btn-warning');
        button.classList.add('btn-xs');
        button.innerText = 'Save';
        button.onclick = () => {
            button.disabled = true;
            button.innerText = 'Saving...';

            const formData = new FormData();
            formData.append('timezone', currentTimezone);

            fetch('/user/timezone', {method: 'PATCH', body: formData})
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Unknown error');
                    }

                    return response.json();
                })
                .then(() => {
                    alert.classList.remove('alert-warning');
                    alert.classList.add('alert-success');
                    alert.innerText = 'Time zone saved!';

                    setTimeout(() => location.reload(), 3000);
                })
                .catch(() => {
                    alert.classList.remove('alert-warning');
                    alert.classList.add('alert-danger');
                    alert.innerText = 'Something went wrong! Reload the page and try again.';
                });
        };
        alert.append(button);
    }
});
