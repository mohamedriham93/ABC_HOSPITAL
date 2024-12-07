// main.js
document.addEventListener("DOMContentLoaded", function() {
    const toggleButton = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav ul');

    toggleButton.addEventListener('click', function() {
        navMenu.classList.toggle('active');
    });
});
