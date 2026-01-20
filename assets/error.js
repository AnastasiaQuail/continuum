import './styles/app.css';

document.addEventListener('DOMContentLoaded', () => {
    requestAnimationFrame(() => {
        document.documentElement.dataset.errorPage = 'opened';
    });

    document.querySelectorAll('[data-button-action]').forEach((button) => {
        button.onclick = () => {
            switch (button.dataset.buttonAction) {
                case 'retry':
                    location.reload();
                    break;
                case 'back':
                    history.back();
                    break;
            }
        }
    })
});
