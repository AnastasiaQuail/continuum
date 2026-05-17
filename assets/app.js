import './styles/app.css';
import ThemeToggler from './js/theme-toggler.js';
import * as Services from './js/services.js';

new ThemeToggler('#theme-toggler');

new Services.SidebarToggler('#sidebar-toggler');
new Services.Sidebar().applyTo('#sidebar');
new Services.TimezoneDetector().detect('#body-messages');
new Services.FlashMessages().show('#body-messages');
new Services.CalendarUpcomingEventsToggler('#calendar-upcoming-events');
new Services.InputWrapper().apply();
new Services.TabToggler().apply();
new Services.EasterEggs().apply();

/**
 * @param {HTMLFormElement|HTMLElement} element
 * @return {boolean}
 */
window.submitBy = function (element) {
    const form = element instanceof HTMLFormElement ? element : element.closest('form');
    form.submit();
    form.classList.add('disabled');

    for (let field of form.elements) {
        field.disabled = true;
    }

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
});
