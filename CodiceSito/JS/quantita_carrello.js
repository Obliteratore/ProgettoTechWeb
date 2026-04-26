document.addEventListener('DOMContentLoaded', function(){

    const inputQuantita = document.querySelectorAll('.quantita-carrello');
    inputQuantita.forEach(input => {
        input.addEventListener('change', function() {
            this.form.submit();
        });
    });
});