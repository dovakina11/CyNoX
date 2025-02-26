/* script.js */

// Wait for the DOM to fully load before executing scripts
document.addEventListener("DOMContentLoaded", () => {
    // Mobile Navigation Menu Toggle
    const navToggle = document.querySelector(".nav-toggle");
    const navList = document.querySelector(".nav-list");

    navToggle.addEventListener("click", () => {
        navList.classList.toggle("active");
        navToggle.classList.toggle("active");
    });

    // Smooth Scrolling for Anchor Links
    const smoothScrollLinks = document.querySelectorAll('a[href^="#"]');

    smoothScrollLinks.forEach(link => {
        link.addEventListener("click", (event) => {
            event.preventDefault();
            const targetId = link.getAttribute("href").substring(1);
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop,
                    behavior: "smooth"
                });
            }
        });
    });

    // Form Validation
    const contactForm = document.querySelector(".contact-form");

    if (contactForm) {
        contactForm.addEventListener("submit", (event) => {
            const nameInput = document.getElementById("name");
            const emailInput = document.getElementById("email");
            const messageInput = document.getElementById("message");

            let isValid = true;

            // Validate Name
            if (!nameInput.value.trim()) {
                alert("Please enter your name.");
                isValid = false;
            }

            // Validate Email
            if (!emailInput.value.trim() || !validateEmail(emailInput.value)) {
                alert("Please enter a valid email address.");
                isValid = false;
            }

            // Validate Message
            if (!messageInput.value.trim()) {
                alert("Please enter your message.");
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault();
            }
        });
    }

    // Helper Function to Validate Email
    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
});
