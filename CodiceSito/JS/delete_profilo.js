const link = document.getElementById('deleteProfiloLink');
const dialog = document.getElementById('deleteProfiloDialog');
const cancelBtn = document.getElementById('cancelDeleteBtn');

if(link && dialog && dialog.showModal) {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        dialog.showModal();
        cancelBtn.focus();
    });

    cancelBtn.addEventListener('click', () => {
        dialog.close();
        link.focus();
    });
}