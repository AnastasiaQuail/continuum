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
        document.documentElement.dataset.sidebar = this.#COLLAPSED;
    }

    #remove() {
        localStorage.removeItem(this.#lsKey);
        delete document.documentElement.dataset.sidebar;
    }

    init() {
        if (this.#isCollapsed()) {
            this.#set();
        }
    }

    toggle() {
        if (this.#isCollapsed()) {
            this.#remove();
        } else {
            this.#set();
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
