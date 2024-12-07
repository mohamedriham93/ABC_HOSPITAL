document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('toggleButton');
    const navMenu = document.getElementById('navMenu');

    toggleButton.addEventListener('click', function() {
        navMenu.classList.toggle('active'); // Toggle the 'active' class on the menu
    });
});
