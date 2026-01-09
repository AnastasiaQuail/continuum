import './styles/app.css';
import './styles/public.css';
import ThemeToggler from './js/theme-toggler.js';

const themeToggler = new ThemeToggler();
themeToggler.init();

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('theme-toggler').onclick = () => themeToggler.toggle();

    (function () {
        const inputEmail = document.getElementById('username');
        const inputPassword = document.getElementById('password');
        const buttonSubmit = document.getElementById('submit');

        /**
         * @param {HTMLInputElement|HTMLButtonElement} element
         * @param {boolean} isValid
         */
        const toggle = function (element, isValid = false) {
            const formGroup = element.closest('.form-control-group');

            if (isValid) {
                element.disabled = false;
                formGroup.classList.add('form-control-active');
            } else {
                element.disabled = true;
                formGroup.classList.remove('form-control-active');

                if (element instanceof HTMLInputElement) {
                    element.value = '';
                }
            }
        }

        inputEmail.addEventListener('input', () => {
            const isValid = inputEmail.checkValidity();

            toggle(inputPassword, isValid);

            if (!isValid) {
                toggle(buttonSubmit);
            }
        });

        inputEmail.dispatchEvent(new CustomEvent('input'));

        inputPassword.addEventListener('input', () => {
            toggle(buttonSubmit, inputPassword.checkValidity());
        });
    })();
});
