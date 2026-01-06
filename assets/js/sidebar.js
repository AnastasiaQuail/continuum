export class SidebarToggler {
    #lsKey = 'site-sidebar-collapsed';
    #COLLAPSED = 'collapsed';

    /**
     * @return {boolean}
     */
    #isCollapsed() {
        return localStorage.getItem(this.#lsKey) === this.#COLLAPSED;
    }

    #set() {
        localStorage.setItem(this.#lsKey, this.#COLLAPSED);
    }

    #remove() {
        localStorage.removeItem(this.#lsKey);
    }

    #setDataset() {
        document.documentElement.dataset.sidebar = this.#COLLAPSED;
    }

    #removeDataset() {
        delete document.documentElement.dataset.sidebar;
    }

    init() {
        if (this.#isCollapsed()) {
            this.#set();
            this.#setDataset();
        }

        window.addEventListener('storage', (event) => {
            if (event.key === this.#lsKey) {
                if (event.newValue === this.#COLLAPSED) {
                    this.#setDataset();
                } else {
                    this.#removeDataset();
                }
            }
        });
    }

    toggle() {
        if (this.#isCollapsed()) {
            this.#remove();
            this.#removeDataset();
        } else {
            this.#set();
            this.#setDataset();
        }
    }
}

export class Sidebar {
    #className = 'dropdown-active';

    /**
     * @param {string} id
     */
    constructor(id) {
        this.sidebar = document.getElementById(id);
        this.navDropdowns = this.sidebar.querySelectorAll('.dropdown');
    }

    init() {
        for (const navDropdown of this.navDropdowns) {
            navDropdown.onclick = () => this.#onclick(navDropdown);
        }
    }

    /**
     * @param {HTMLLIElement} navDropdown
     */
    #onclick(navDropdown) {
        const isActive = navDropdown.classList.contains(this.#className);

        for (const nDropdown of this.navDropdowns) {
            nDropdown.classList.remove(this.#className);
        }

        if (!isActive) {
            navDropdown.classList.add(this.#className);
        }
    }
}
