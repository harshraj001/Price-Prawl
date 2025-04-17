// js/navigation.js

document.addEventListener('DOMContentLoaded', () => {
    const hamburgerButton = document.getElementById('hamburger-button');
    const mobileMenu = document.getElementById('mobile-menu');

    if (hamburgerButton && mobileMenu) {
        hamburgerButton.addEventListener('click', (event) => {
            event.stopPropagation(); // Prevent click from immediately closing menu via document listener
            const isOpen = !mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden');
            // Optional: Add transition classes
            if (!isOpen) {
                // Start opening animation (optional)
                mobileMenu.classList.remove('scale-y-0');
                mobileMenu.classList.add('scale-y-100');
            } else {
                // Start closing animation (optional)
                mobileMenu.classList.remove('scale-y-100');
                mobileMenu.classList.add('scale-y-0');
                 // Use timeout matching transition duration if using transition classes
                 // setTimeout(() => mobileMenu.classList.add('hidden'), 300); // If using scale-y-*
            }

            hamburgerButton.setAttribute('aria-expanded', !isOpen);
        });

        // Close menu when clicking outside
        document.addEventListener('click', (event) => {
            if (!mobileMenu.classList.contains('hidden') &&
                !mobileMenu.contains(event.target) &&
                !hamburgerButton.contains(event.target))
             {
                mobileMenu.classList.add('hidden');
                 // Optional: Animation classes reset
                 mobileMenu.classList.remove('scale-y-100');
                 mobileMenu.classList.add('scale-y-0');
                hamburgerButton.setAttribute('aria-expanded', 'false');
            }
        });

        // Close menu on Escape key
         document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                 mobileMenu.classList.add('hidden');
                  // Optional: Animation classes reset
                 mobileMenu.classList.remove('scale-y-100');
                 mobileMenu.classList.add('scale-y-0');
                 hamburgerButton.setAttribute('aria-expanded', 'false');
            }
         });


    } else {
        console.warn('Hamburger button or mobile menu element not found for navigation.');
    }
});