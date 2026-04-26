function isMobile() {
    return window.innerWidth <= 767;
}

function hamburger_menu() {
    const hamburgerMenuBtn = document.getElementById("hamburger-menu-btn");
    const hamburgerMenu = document.getElementById("hamburger-menu");
    const header = document.querySelector("header");

    if(!hamburgerMenuBtn || !hamburgerMenu) return;

    function setState(state) {
        hamburgerMenu.classList.toggle('hidden-menu', !state);
        hamburgerMenuBtn.classList.toggle('active', state);
        hamburgerMenuBtn.setAttribute('aria-expanded', state.toString());
    }

    setState(false);

    hamburgerMenuBtn.addEventListener('click', () => {
        const isOpen = hamburgerMenuBtn.getAttribute('aria-expanded') === 'true';
        setState(!isOpen);
    });

    window.addEventListener('resize', () => {
        if(!isMobile())
            setState(false);
    });

    let startY = 0;
    let endY = 0;
    const swipeThreshold = 50;

    if(header) {
        header.addEventListener("touchstart", (event) => {
            if(!isMobile()) return;
            startY = event.touches[0].clientY;
        });

        header.addEventListener("touchend", (event) => {
            if(!isMobile()) return;
            endY = event.changedTouches[0].clientY;
            handleSwipe();
        });
    } else
        return;

    function handleSwipe() {
        const deltaY = startY - endY;
        const isOpen = hamburgerMenuBtn.getAttribute('aria-expanded') === 'true';

        if (deltaY < -swipeThreshold && !isOpen) {
            setState(true);
        }

        if (deltaY > swipeThreshold && isOpen) {
            setState(false);
        }
    }
}

hamburger_menu();