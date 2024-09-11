document.addEventListener('DOMContentLoaded', function() {
    // JavaScript for dark mode toggle
    const toggleButton = document.getElementById('darkModeToggle');
    const darkModeIcon = document.getElementById('darkModeIcon');
    const currentMode = localStorage.getItem('mode') || 'light-mode';
    document.body.classList.add(currentMode);

    if (currentMode === 'dark-mode') {
        darkModeIcon.classList.replace('fa-moon', 'fa-sun');
    }

    toggleButton.addEventListener('click', () => {
        if (document.body.classList.contains('light-mode')) {
            document.body.classList.remove('light-mode');
            document.body.classList.add('dark-mode');
            darkModeIcon.classList.replace('fa-moon', 'fa-sun');
            localStorage.setItem('mode', 'dark-mode');
        } else {
            document.body.classList.remove('dark-mode');
            document.body.classList.add('light-mode');
            darkModeIcon.classList.replace('fa-sun', 'fa-moon');
            localStorage.setItem('mode', 'light-mode');
        }
    });
});
