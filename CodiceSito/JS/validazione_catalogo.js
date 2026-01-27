document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('form-container-catalogo');
    const minInput = document.getElementById('prezzo_min');
    const maxInput = document.getElementById('prezzo_max');
    const msgErrore = document.getElementById('msg-errore-prezzi');

    function validaPrezzo(e){
        if(!minInput || !maxInput) return;

        const min = parseInt(minInput.value);
        const max = parseInt(maxInput.value);

        if (!isNaN(min) && !isNaN(max)){
            if(min>max){
                if(e) e.preventDefault();
                msgErrore.classList.remove('hidden');
                minInput.classList.add('input-errore');
                maxInput.classList.add('input-errore');
                
                return false;
            }
        }
        
        msgErrore.classList.add('hidden');
        minInput.classList.remove('input-errore');
        maxInput.classList.remove('input-errore');
                
        return true;
    }

    if (form){
        form.addEventListener('submit', validaPrezzo)
    }

    if (minInput && maxInput) {
        minInput.addEventListener('input', () => validaPrezzo(null));
        maxInput.addEventListener('input', () => validaPrezzo(null))
    }
});