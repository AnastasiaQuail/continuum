import './styles/app.css';
import ThemeToggler from './js/theme-toggler.js';
import {SidebarToggler, Sidebar} from './js/sidebar.js';

const themeToggler = new ThemeToggler();
themeToggler.init();

const sidebarToggler = new SidebarToggler();
sidebarToggler.init();

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('theme-toggler').onclick = () => themeToggler.toggle();
    document.getElementById('sidebar-toggler').onclick = () => sidebarToggler.toggle();

    new Sidebar('sidebar').init();
});
