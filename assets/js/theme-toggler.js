import {ToggleStorage} from './components.js';

export default class extends ToggleStorage {
    constructor(id) {
        super('theme', id, true);
    }

    /**
     * @return string
     */
    getDefault() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    /**
     * @param {Event} event
     */
    onClick(event) {
        this.save(this.get() === 'light' ? 'dark' : 'light');
    }
}
