import './bootstrap';
import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

window.Alpine = Alpine;

const initDatePickers = (root = document) => {
    const nodes = root.querySelectorAll
        ? root.querySelectorAll('input[type="date"]:not(.flatpickr-input)')
        : [];
    nodes.forEach((el) => {
        flatpickr(el, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd-m-Y',
            allowInput: true,
            altInputClass: el.className,
        });
    });
};

document.addEventListener('DOMContentLoaded', () => initDatePickers());

const observer = new MutationObserver((mutations) => {
    for (const m of mutations) {
        m.addedNodes.forEach((node) => {
            if (node.nodeType !== 1) return;
            if (node.matches && node.matches('input[type="date"]')) {
                initDatePickers(node.parentNode);
            } else {
                initDatePickers(node);
            }
        });
    }
});
observer.observe(document.body, { childList: true, subtree: true });

Alpine.start();
