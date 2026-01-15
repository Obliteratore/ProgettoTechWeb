function changeQty(delta) {
    const input = document.getElementById('quantita');

    if (input) {
        let valore = parseInt(input.value) || 1;

        const min = parseInt(input.getAttribute('min')) || 1;
        const max = parseInt(input.getAttribute('max')) || 99;

        valore += delta;

        if (valore < min) valore = min;
        if (valore > max) valore = max;

        input.value = valore;
    }
}