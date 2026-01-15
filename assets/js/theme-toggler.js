import {Storage} from './components.js';

export default class extends Storage {
    constructor() {
        super('continuum-ls-theme');
    }

    /**
     * @param {string} value
     */
    set(value) {
        document.documentElement.dataset.theme = value;
    }

    /**
     * @return string
     */
    getDefault() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    /**
     * @param {string} id
     */
    onClick(id) {
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector(id).onclick = () => {
                this.save(this.get() === 'light' ? 'dark' : 'light');
            };
        })
    }
}
