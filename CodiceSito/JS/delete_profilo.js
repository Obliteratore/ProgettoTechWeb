const link = document.getElementById('deleteProfiloLink');
const dialog = document.getElementById('deleteProfiloDialog');

if (link && dialog && dialog.showModal) {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        dialog.showModal();
    });

    dialog.querySelector('button[value="cancel"]').addEventListener('click', () => dialog.close());
}