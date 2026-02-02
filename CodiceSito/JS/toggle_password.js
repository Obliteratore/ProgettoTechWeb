document.addEventListener("DOMContentLoaded", function () {
    const passwordInput = document.getElementById("password");
    const confermaPasswordInput = document.getElementById("confermaPassword");
    const togglePassword = document.getElementById("togglePassword");

    if(!passwordInput || !togglePassword) return;

    togglePassword.addEventListener("change", function () {
        passwordInput.type = this.checked ? "text" : "password";
        if(confermaPasswordInput)
            confermaPasswordInput.type = this.checked ? "text" : "password";
    });
});