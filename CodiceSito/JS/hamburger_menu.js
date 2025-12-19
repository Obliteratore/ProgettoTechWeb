function hamburger_menu() {
    const hamburgerMenuBtn = document.getElementById("hamburger-menu-btn");
    const hamburgerMenu = document.getElementById("hamburger-menu");

    hamburgerMenuBtn.addEventListener("click", () => {
    const isActive = hamburgerMenu.classList.toggle("active");      // mostra/nasconde il menu
    hamburgerMenuBtn.classList.toggle("active");   // mostra l'icona corretta

    // indica se il menu è aperto o chiuso
    hamburgerMenuBtn.setAttribute("aria-expanded", isActive);
    });

    // Chiudi il menu se la finestra è più larga di 768px
    window.addEventListener("resize", () => {
        if (window.innerWidth >= 768) {
            hamburgerMenu.classList.remove("active");
            hamburgerMenuBtn.classList.remove("active");
        }
    });
}

hamburger_menu();