document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll(".carousel-track").forEach(track => {

        // Move to middle (after first render)
        const half = track.scrollWidth / 2;
        track.scrollLeft = half;

        // Infinite loop on manual scroll
        track.addEventListener("scroll", () => {
            handleInfinite(track);
        });
    });

    // Search functionality
    const searchForm = document.querySelector('.search-form');
    const searchInput = document.getElementById('searchInput');

    if (searchForm && searchInput) {
        // Prevent empty search submission
        searchForm.addEventListener('submit', (e) => {
            const searchValue = searchInput.value.trim();
            if (!searchValue) {
                e.preventDefault();
                searchInput.focus();
                searchInput.style.borderColor = '#ef4444';
                setTimeout(() => {
                    searchInput.style.borderColor = '';
                }, 1500);
            }
        });

        // Clear error styling on input
        searchInput.addEventListener('input', () => {
            searchInput.style.borderColor = '';
        });

        // Focus on search input
        searchInput.addEventListener('focus', () => {
            searchInput.style.boxShadow = '0 0 8px rgba(56, 189, 248, 0.5)';
        });

        searchInput.addEventListener('blur', () => {
            searchInput.style.boxShadow = '';
        });
    }

});

/* GLOBAL FUNCTIONS */
window.slideRight = function (btn) {
    const track = btn.parentElement.querySelector('.carousel-track');
    track.scrollBy({
        left: track.clientWidth,
        behavior: 'smooth'
    });
};

window.slideLeft = function (btn) {
    const track = btn.parentElement.querySelector('.carousel-track');
    track.scrollBy({
        left: -track.clientWidth,
        behavior: 'smooth'
    });
};

function handleInfinite(track) {
    const half = track.scrollWidth / 2;

    // Right edge → jump to middle
    if (track.scrollLeft >= track.scrollWidth - track.clientWidth - 10) {
        track.scrollLeft = half;
    }

    // Left edge → jump to middle
    if (track.scrollLeft <= 10) {
        track.scrollLeft = half;
    }
}



const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('navMenu');

hamburger?.addEventListener('click', () => {
    navMenu.classList.toggle('active');
});

