function hamburger_menu() {
    const hamburgerMenuBtn = document.getElementById("hamburger-menu-btn");
    const hamburgerMenu = document.getElementById("hamburger-menu");
    const header = document.querySelector("header");

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

    let startY = 0;
    let endY = 0;
    const swipeThreshold = 50; // soglia minima in px

    // Rileva inizio tocco
    header.addEventListener("touchstart", (event) => {
        startY = event.touches[0].clientY;
    });

    // Rileva fine tocco
    header.addEventListener("touchend", (event) => {
        endY = event.changedTouches[0].clientY;
        handleSwipe();
    });

    function handleSwipe() {
        const deltaY = startY - endY;

        // Swipe verso il basso → APRI menu
        if (deltaY < -swipeThreshold && !hamburgerMenu.classList.contains("active")) {
            hamburgerMenu.classList.add("active");
            hamburgerMenuBtn.classList.add("active");
            hamburgerMenuBtn.setAttribute("aria-expanded", "true");
        }

        // Swipe verso l’alto → CHIUDI menu
        if (deltaY > swipeThreshold && hamburgerMenu.classList.contains("active")) {
            hamburgerMenu.classList.remove("active");
            hamburgerMenuBtn.classList.remove("active");
            hamburgerMenuBtn.setAttribute("aria-expanded", "false");
        }
    }

}

hamburger_menu();