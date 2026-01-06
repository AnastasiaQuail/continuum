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
     * @param {string} theme
     */
    #set(theme) {
        localStorage.setItem(this.#lsKey, theme);
    }

    /**
     * @param {string} theme
     */
    #setDataset(theme) {
        document.documentElement.dataset.theme = theme;
    }

    /**
     * @return {string}
     */
    #detect() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? this.#DARK : this.#LIGHT;
    }

    init() {
        const theme = this.#get() ?? this.#detect();

        this.#set(theme);
        this.#setDataset(theme);

        window.addEventListener('storage', (event) => {
            if (event.key === this.#lsKey) {
                this.#setDataset(event.newValue);
            }
        });
    }

    toggle() {
        const theme = this.#get() === this.#LIGHT ? this.#DARK : this.#LIGHT;

        this.#set(theme);
        this.#setDataset(theme);
    }
}
