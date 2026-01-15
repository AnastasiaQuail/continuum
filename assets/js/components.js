export class Storage {
    #key;

    /**
     * @param {string} key
     */
    constructor(key) {
        this.#key = key;

        const value = this.get()

        if (value !== null) {
            this.set(value);
        } else {
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
        console.warn('You must implement this function.');
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
        localStorage.setItem(this.#key, value);

        this.set(value);
    }
}
