import './styles/app.css';
import ThemeToggler from './js/theme-toggler.js';
import * as Services from './js/services.js';

new ThemeToggler('#theme-toggler');

new Services.SidebarToggler('#sidebar-toggler');
new Services.Sidebar().applyTo('#sidebar');
new Services.TimezoneDetector().detect('#body-messages');
new Services.CalendarUpcomingEventsToggler('#calendar-upcoming-events');
new Services.InputMasker().apply();

/**
 * @param {HTMLElement} element
 * @return {boolean}
 */
window.submitBy = function (element) {
    element.closest('form').submit();
    element.disabled = true;

    return false;
};

document.addEventListener('DOMContentLoaded', () => {
    const toolbar = document.getElementById('footer-toolbar');
    const sfToolbar = document.querySelector('body > .sf-toolbar');

    if (toolbar && sfToolbar) {
        const buildToolbar = function () {
            const sfDatabase = sfToolbar.querySelector('.sf-toolbar-block-db');

            toolbar.querySelector('span').innerText = sfDatabase.querySelector('.sf-toolbar-status').innerText.trim();
            toolbar.onclick = () => {
                window.open(sfDatabase.querySelector('a').href, '_blank');
            };
        }

        if (!sfToolbar.querySelector('.sf-toolbar-block-db')) {
            new MutationObserver(() => buildToolbar()).observe(sfToolbar, {childList: true});
        } else {
            buildToolbar();
        }
    }

    document.getElementById('footer-bird').onclick = () => {
        const data = document.documentElement.dataset;
        const number = Number(data.eggBird ?? 0) + 1;

        data.eggBird = number > 3 ? '0' : number.toString();
    }
});
