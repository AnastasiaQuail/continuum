import {Storage} from './components.js';

export class SidebarToggler extends Storage {
    constructor() {
        super('continuum-ls-sidebar');
    }

    /**
     * @param {string} value
     */
    set(value) {
        document.documentElement.dataset.sidebar = value;
        document.dispatchEvent(new CustomEvent('app:sidebar:changed'));
    }

    /**
     * @param {string} id
     */
    onClick(id) {
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector(id).onclick = (event) => {
                const type = getComputedStyle(event.currentTarget).getPropertyValue('--sidebar-toggler');

                if ("'desktop'" === type || '"desktop"' === type) {
                    this.save(this.get() === 'collapsed' ? '' : 'collapsed');
                } else {
                    const dataset = document.documentElement.dataset;
                    dataset.sidebarView = dataset.sidebarView !== 'open' ? 'open' : '';
                }
            };
        })
    }
}

export class Sidebar {
    #navDropdowns = [];
    #className = 'dropdown-active';

    /**
     * @param {string} id
     */
    applyTo(id) {
        this.#navDropdowns = document.querySelector(id).querySelectorAll('.dropdown');

        for (const navDropdown of this.#navDropdowns) {
            navDropdown.querySelector('.nav-link').onclick = () => this.#onclick(navDropdown);
        }
    }

    /**
     * @param {HTMLLIElement} navDropdown
     */
    #onclick(navDropdown) {
        const isActive = navDropdown.classList.contains(this.#className);

        for (const nDropdown of this.#navDropdowns) {
            nDropdown.classList.remove(this.#className);
        }

        if (!isActive) {
            navDropdown.classList.add(this.#className);
        }
    }
}

export class TimezoneDetector {
    constructor() {
        this.currentTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    }

    /**
     * @param {string} id
     */
    detect(id) {
        if (this.currentTimezone !== document.documentElement.dataset.timezone) {
            document.querySelector(id).prepend(this.#buildAlert());
        }
    }

    /**
     * @return {HTMLDivElement|null}
     */
    #buildAlert() {
        const alert = document.createElement('div');
        alert.classList.add('alert');
        alert.classList.add('alert-warning');
        alert.classList.add('alert-inline');

        const mismatchText = document.createElement('strong');
        mismatchText.innerText = 'Time zone mismatch!';
        alert.append(mismatchText);

        const offsetHours = -new Date().getTimezoneOffset() / 60;

        const spanText = document.createElement('span');
        spanText.innerText = 'Save "' + this.currentTimezone
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

            this.#sendRequest(alert);
        };
        alert.append(button);

        return alert;
    }

    /**
     * @param {HTMLDivElement} alert
     */
    #sendRequest(alert) {
        const formData = new FormData();
        formData.append('timezone', this.currentTimezone);

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
    }
}

export class CalendarNotificationToggler extends Storage {
    constructor() {
        super('continuum-ls-calendar-notifications');
    }

    /**
     * @param {string} value
     */
    set(value) {
        document.documentElement.dataset.calendarNotifications = value;
    }

    /**
     * @param {string} id
     */
    onClick(id) {
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector(id)?.addEventListener('click', () => {
                this.save(this.get() === 'open' ? '' : 'open');
            })
        })
    }
}
