const link = document.getElementById('deleteProfiloLink');
const dialog = document.getElementById('deleteProfiloDialog');
const cancelBtn = document.getElementById('cancelBtn');

if (link && dialog && dialog.showModal) {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        dialog.showModal();
    });

    cancelBtn.addEventListener('click', () => dialog.close());
}