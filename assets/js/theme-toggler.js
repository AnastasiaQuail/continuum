export default class {
    #lsKey = 'site-theme';
    #DARK = 'dark';
    #LIGHT = 'light';

    /**
     * @return {string|null}
     */
    #get() {
        const theme = localStorage.getItem(this.#lsKey);

        if (theme === this.#DARK || theme === this.#LIGHT) {
            return theme;
        }

        return null;
    }

    /**
     * @return {string}
     */
    #detect() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? this.#DARK : this.#LIGHT;
    }

    /**
     * @param {string} theme
     */
    #set(theme) {
        localStorage.setItem(this.#lsKey, theme);
        document.documentElement.dataset.theme = theme;
    }

    init() {
        this.#set(this.#get() ?? this.#detect());
    }

    toggle() {
        this.#set(this.#get() === this.#LIGHT ? this.#DARK : this.#LIGHT);
    }
}
