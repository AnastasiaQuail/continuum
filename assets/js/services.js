import {ToggleStorage} from './components.js';

export class SidebarToggler extends ToggleStorage {
    constructor(id) {
        super('sidebar', id);
    }

    /**
     * @param {Event} event
     */
    onClick(event) {
        const type = getComputedStyle(document.documentElement).getPropertyValue('--device-type');

        if ("'desktop'" === type || '"desktop"' === type) {
            super.onClick(event);
        } else {
            document.dispatchEvent(new CustomEvent('app:root-click:sidebar-view'));
        }
    }
}

export class Sidebar {
    #transitionEndListener;
    #backdropClickListener;
    #navDropdowns = [];
    #className = 'dropdown-active';

    /**
     * @param {string} id
     */
    applyTo(id) {
        const dataset = document.documentElement.dataset;
        const sidebar = document.querySelector(id);
        const classes = document.body.classList;
        const backdrop = document.body.querySelector('.body');

        // open-close sidebar animations

        document.addEventListener('app:root-click:sidebar-view', () => {
            sidebar.removeEventListener('transitionend', this.#transitionEndListener);
            backdrop.removeEventListener('click', this.#backdropClickListener);

            sidebar.addEventListener('transitionend', this.#transitionEndListener = (e) => {
                if (e.target !== sidebar || e.propertyName !== 'transform') {
                    return;
                }

                if (!classes.contains('sidebar-toggling')) {
                    delete dataset.sidebarView;
                }
            });

            if (!classes.contains('sidebar-toggling')) {
                dataset.sidebarView = 'show';

                backdrop.addEventListener('click', this.#backdropClickListener = () => {
                    document.dispatchEvent(new CustomEvent('app:root-click:sidebar-view'));
                });
            }

            classes.toggle('sidebar-toggling');
        });

        sidebar.querySelectorAll('a.nav-link').forEach(element => {
            element.addEventListener('click', () => {
                element.classList.add('loading');
                setTimeout(() => element.classList.remove('loading'), 5000)
            });
        });

        // mobile sidebar handle

        const handle = document.querySelector('#sidebar-handle');
        const height = window.innerHeight;

        let dragging = false;
        let startHeight = 0;
        let currentPercent = 0;

        handle.addEventListener('pointerdown', (e) => {
            dragging = true;
            startHeight = e.clientY;
            currentPercent = 100;

            handle.setPointerCapture(e.pointerId);
            dataset.sidebarDragging = '';
        });

        handle.addEventListener('pointermove', (e) => {
            if (!dragging) {
                return;
            }

            currentPercent = (height + startHeight - e.clientY) / height * 100;

            document.body.style.setProperty('--sidebar-view-offset', currentPercent.toString());
        });

        function onPointerUp() {
            if (!dragging) {
                return;
            }

            dragging = false;

            if (currentPercent <= 60) {
                document.dispatchEvent(new CustomEvent('app:root-click:sidebar-view'));
            }

            delete dataset.sidebarDragging;
            document.body.style.removeProperty('--sidebar-view-offset');
        }

        handle.addEventListener('pointerup', onPointerUp);
        handle.addEventListener('pointercancel', onPointerUp);

        // dropdowns

        this.#navDropdowns = sidebar.querySelectorAll('.dropdown');

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
        alert.classList.add('alert-dismissible');
        alert.classList.add('hidden');

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

        fetch('/users/timezone', {method: 'PATCH', body: formData})
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

export class FlashMessages {
    /**
     * @param {string} id
     */
    show(id) {
        document.addEventListener('DOMContentLoaded', () => {
            const alerts = [...document.querySelectorAll(id + ' .alert')];

            alerts.forEach((alert, i) => {
                setTimeout(() => alert.classList.remove('hidden'), i * 300);
            });

            alerts.filter(alert => alert.classList.contains('alert-dismissible')).forEach((alert, i) => {
                setTimeout(() => {
                    alert.classList.add('hide');
                    setTimeout(() => alert.remove(), 2000);
                }, 2000 * i + 4000);
            });
        });
    }
}

export class CalendarUpcomingEventsToggler extends ToggleStorage {
    constructor(id) {
        super('calendar-upcoming', id);
    }
}

export class InputWrapper {
    apply() {
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('input[data-value-prev]').forEach(input => {
                this.#togglePrevValue(input);
            })

            document.querySelectorAll('input[data-mask="postfix"][data-postfix]').forEach(input => {
                this.#maskPostfix(input);
            })
        });
    }

    /**
     * @param {HTMLInputElement} input
     */
    #togglePrevValue(input) {
        input.addEventListener('focusin', () => {
            if (input.value === '') {
                input.value = input.dataset.valuePrev;
                input.dispatchEvent(new Event('input'))
            }
        });
    }

    /**
     * @param {HTMLInputElement} input
     */
    #maskPostfix(input) {
        const mask = this.#wrapInput(input);

        this.#setPostfix(input, mask);

        input.addEventListener('input', () => {
            this.#setPostfix(input, mask);
        });
    }

    /**
     * @param {HTMLInputElement} input
     * @return {HTMLDivElement}
     */
    #wrapInput(input) {
        const wrap = document.createElement('div');
        wrap.classList.add('form-control-number');

        input.replaceWith(wrap);
        wrap.appendChild(input);

        const mask = document.createElement('div');
        mask.classList.add('form-mask');
        wrap.appendChild(mask);

        return mask;
    }

    /**
     * @param {HTMLInputElement} input
     * @param {HTMLDivElement} mask
     */
    #setPostfix(input, mask) {
        mask.innerHTML = '';

        if (input.value === '') {
            return;
        }

        const value = document.createElement('span');
        value.innerText = input.value;
        mask.appendChild(value);

        const m = document.createElement('span');
        m.classList.add('mask');
        m.innerText = input.dataset.postfix;
        mask.appendChild(m);
    }
}

export class TabToggler {
    apply() {
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-tabs]').forEach(element => {
                this.#activate(element);
            })
        });
    }

    /**
     * @param {HTMLElement} element
     */
    #activate(element) {
        /** @type {NodeListOf<HTMLElement>} */
        const tabs = element.querySelectorAll('[data-tab-id]');
        /** @type {NodeListOf<HTMLElement>} */
        const tabContents = element.querySelectorAll('[data-tab-content-id]');

        tabs.forEach(tab => {
            tab.onclick = () => {
                element.dataset.generalType = tab.dataset.generalType;

                this.#showTab(tab, tabs);
                this.#showContent(tab, tabContents);
            }
        });
    }

    /**
     * @param {HTMLElement} tabClicked
     * @param {NodeListOf<HTMLElement>} tabs
     */
    #showTab(tabClicked, tabs) {
        tabs.forEach(tab => {
            tab.classList.remove('active');

            if (tabClicked.dataset.tabId === tab.dataset.tabId) {
                tab.classList.add('active');
            }
        })
    }

    /**
     * @param {HTMLElement} tabClicked
     * @param {NodeListOf<HTMLElement>} tabContents
     */
    #showContent(tabClicked, tabContents) {
        tabContents.forEach(tabContent => {
            tabContent.classList.remove('active');

            if (tabClicked.dataset.tabId === tabContent.dataset.tabContentId) {
                tabContent.classList.add('active');
            }
        })
    }
}

export class EasterEggs {
    apply() {
        document.addEventListener('DOMContentLoaded', () => {
            this.#dog();
        });
    }

    /**
     * @return {HTMLDivElement|null}
     */
    #dog() {
        document.getElementById('footer-dog')?.addEventListener('click', event => {
            const container = document.querySelector('.body-container');
            container.classList.add('loading');

            fetch(event.currentTarget.dataset.href, {method: 'POST'})
                .then(response => {
                    container.classList.remove('loading');

                    if (!response.ok) {
                        throw new Error('Unknown error');
                    }

                    return response.json();
                })
                .then(json => {
                    const img = document.createElement('img');
                    img.src = json.href;

                    container.classList.add('container-justify')
                    container.innerHTML = '';
                    container.appendChild(img);
                });
        })
    }
}
