export class Storage {
    #key;
    #dataKey;

    /**
     * @param {string} key
     * @param {boolean} useDefault
     */
    constructor(key, useDefault = false) {
        this.#key = 'continuum-ls-' + key;
        this.#dataKey = key;

        const value = this.get()

        if (value !== null) {
            this.set(value);
        } else if (useDefault) {
            this.save(this.getDefault());
        }

        window.addEventListener('storage', (event) => {
            if (event.key === this.#key) {
                this.set(event.newValue);
            }
        });
    }

    /**
     * @param {string} value
     */
    set(value) {
        document.documentElement.setAttribute('data-' + this.#dataKey, value);
        document.dispatchEvent(new CustomEvent('app:root-data:' + this.#dataKey));
    }

    /**
     * @return {string}
     */
    getDefault() {
        return '';
    }

    /**
     * @return {string|null}
     */
    get() {
        return localStorage.getItem(this.#key);
    }

    /**
     * @param {string} value
     */
    save(value) {
        if (value === '') {
            localStorage.removeItem(this.#key);
        } else {
            localStorage.setItem(this.#key, value);
        }

        this.set(value);
    }
}

export class ToggleStorage extends Storage {
    /**
     * @param {string} key
     * @param {string} id
     * @param {boolean} useDefault
     */
    constructor(key, id, useDefault = false) {
        super(key, useDefault);

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector(id)?.addEventListener('click', (event) => {
                this.onClick(event);
            })
        })
    }

    /**
     * @param {Event} event
     */
    onClick(event) {
        this.save(this.get() === 'collapsed' ? '' : 'collapsed');
    }
}
