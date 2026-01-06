import './styles/app.css';
import './styles/public.css';
import ThemeToggler from './js/theme-toggler.js';

const themeToggler = new ThemeToggler();
themeToggler.init();

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('theme-toggler').onclick = () => themeToggler.toggle();
});
