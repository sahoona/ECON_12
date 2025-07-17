export function initDarkMode() {
    const htmlEl = document.documentElement;

    // Function to set the theme state
    function setThemeState(isDark) {
        if (isDark) {
            htmlEl.classList.add('dark-mode-active');
            localStorage.setItem('darkMode', 'true');
        } else {
            htmlEl.classList.remove('dark-mode-active');
            localStorage.setItem('darkMode', 'false');
        }
        // Update the button's aria-pressed state if it exists
        const darkModeToggle = document.getElementById('darkModeToggle');
        if (darkModeToggle) {
            darkModeToggle.setAttribute('aria-pressed', isDark.toString());
        }
    }

    // Apply the theme state on initial load
    const currentIsDark = htmlEl.classList.contains('dark-mode-active');
    setThemeState(currentIsDark);

    // Use event delegation on the document to handle clicks on the dynamic button
    document.addEventListener('click', function(event) {
        // Find the closest ancestor which is the toggle button
        const darkModeToggle = event.target.closest('#darkModeToggle');

        if (darkModeToggle) {
            const isCurrentlyDark = htmlEl.classList.contains('dark-mode-active');
            setThemeState(!isCurrentlyDark);

            // The ripple effect is handled in floatingButtons.js, so no need to add 'clicked' class here.
        }
    });

    // The function should only run once, so we can use a flag on the body or html
    if (document.body.dataset.darkModeInitialized) {
        return;
    }
    document.body.dataset.darkModeInitialized = 'true';
}
