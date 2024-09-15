import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('searchModal');
    const modalBackdrop = document.getElementById('modalBackdrop');
    const openModalButton = document.getElementById('openSearchModal');
    const closeModalButton = document.getElementById('closeSearchModal');

    function openModal() {
        modal.classList.remove('hidden');
        modalBackdrop.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.add('ease-out', 'duration-300', 'opacity-100');
            modalBackdrop.classList.add('ease-out', 'duration-300', 'opacity-100');
        }, 10);
    }

    function closeModal() {
        modal.classList.add('ease-in', 'duration-200', 'opacity-0');
        modalBackdrop.classList.add('ease-in', 'duration-200', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modalBackdrop.classList.add('hidden');
        }, 200);
    }

    openModalButton.addEventListener('click', openModal);
    closeModalButton.addEventListener('click', closeModal);

    modalBackdrop.addEventListener('click', closeModal);
});