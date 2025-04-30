$(function () {
    // Header mission add button animation
    $('.content .header .btn').css("animation-delay", "2s");
    setTimeout(() => {
        $('.content .header .btn').css("animation-delay", "0s");
    }, 2500);
    // Attaching the header on scroll down
    window.onscroll = () => {
        let header = document.querySelector(".content .header");
        // Offset Condition
        if (window.scrollY > 250) {
            $(header).css("position", "fixed");
        }
        else {
            $(header).css("position", "relative");
        }
    }
    // Spinner animation loading
    setTimeout(() => {
        $('#loading').css("display", "none");
    }, 3100);
    // Showing the alert after the spinner
    setTimeout(() => {
        $('.alert').css("animation-name", "reverse-alert-margin-control");
    }, 3500);
    // Alert icon animation control
    document.querySelector(".alert i").addEventListener('animationiteration', () => {
        // Set the delay after each complete cycle
        this.style.animationDelay = '2s'; // 2 seconds delay before reanimating
    })
    // Ripple background effect
    document.querySelector('.mission-creator').addEventListener('click', function (e) {
        // Create the ripple element
        const ripple = document.createElement('span');
        ripple.classList.add('ripple');

        // Get the click position
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;

        // Set size and position
        ripple.style.width = ripple.style.height = `${size}px`;
        ripple.style.left = `${x}px`;
        ripple.style.top = `${y}px`;

        // Append and remove after animation
        this.appendChild(ripple);
        ripple.addEventListener('animationend', () => ripple.remove());
    });
    // Showing objective details
    $('.card .btn').click(function (e) { 
        e.preventDefault();
        $('#objectiveShow').toggleClass("modal-dialog-centered");
    });
    // Clear the query string recorded in page
    const url = new URL(window.location);
    const params = new URLSearchParams(url.search);
    
    // Store the value of the parameter you want to exclude
    const exceptValue = params.get("campaign");
    
    // Remove all other query parameters
    params.forEach((value, key) => {
        if (key !== "campaign") {
            params.delete(key);
        }
    });
    
    // Rebuild the URL without other query parameters but keeping the "campaign"
    let newUrl = url.origin + url.pathname;
    if (exceptValue) {
        newUrl += `?campaign=${exceptValue}`;
    }

    // Update the URL without reloading the page
    window.history.replaceState({}, document.title, newUrl);
});