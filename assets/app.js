import './styles/app.css';
import ThemeToggler from './js/theme-toggler.js';
import * as Services from './js/services.js';

new ThemeToggler().onClick('#theme-toggler');

new Services.SidebarToggler().onClick('#sidebar-toggler');
new Services.Sidebar().applyTo('#sidebar');
new Services.TimezoneDetector().detect('#body-messages');
new Services.CalendarNotificationToggler().onClick('#calendar-notifications');

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('footer-toolbar')?.addEventListener('click', () => {
        const link = document.querySelector('body > .sf-toolbar .sf-toolbar-block-request > a');

        if (link) {
            window.open(link.href, '_blank');
        }
    })

    document.getElementById('footer-bird').onclick = () => {
        const data = document.documentElement.dataset;
        const number = Number(data.eggBird ?? 0) + 1;

        data.eggBird = number > 3 ? '0' : number.toString();
    }
});
