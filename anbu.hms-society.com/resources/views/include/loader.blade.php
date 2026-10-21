<!-- Page Loader -->
<div id="page-loader" class="loader-overlay">
    <div class="loader-wrapper">
        <div class="loader-ring"></div>
        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="loader-logo">
    </div>
</div>

<style>
:root {
    --loader-size: 120px; /* adjust size: 80px small, 150px large */
}

/* Overlay */
.loader-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(4px);
    transition: opacity 0.5s ease, visibility 0.5s ease;
}

/* Wrapper */
.loader-wrapper {
    position: relative;
    width: var(--loader-size);
    height: var(--loader-size);
}

/* Glowing Ring */
.loader-ring {
    position: absolute;
    top: 0; left: 0;
    width: var(--loader-size);
    height: var(--loader-size);
    border: 4px solid rgba(255,255,255,0.2);
    border-top: 4px solid #00d1b2;
    border-radius: 50%;
    animation: spin 1.5s linear infinite;
    box-shadow: 0 0 20px rgba(0,209,178,0.4);
}

/* Logo in center */
.loader-logo {
    position: absolute;
    top: 50%; left: 50%;
    width: calc(var(--loader-size) * 0.5);
    height: calc(var(--loader-size) * 0.5);
    transform: translate(-50%, -50%);
    z-index: 2;
    border-radius: 50%;
    animation: pulse 1.2s infinite ease-in-out;
    box-shadow: 0 0 15px rgba(0,0,0,0.2);
}

/* Spinner Animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Pulse Animation for logo */
@keyframes pulse {
    0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    50% { transform: translate(-50%, -50%) scale(1.1); opacity: 0.8; }
}

/* Fade out */
.fade-out {
    opacity: 0;
    visibility: hidden;
}
</style>

<script>
// Fade out loader on page load
window.addEventListener("load", () => {
    const loader = document.getElementById("page-loader");
    loader.classList.add("fade-out");
    setTimeout(() => loader.style.display = "none", 500);
});

// Show loader on navigation
document.querySelectorAll("a").forEach(link => {
    link.addEventListener("click", (e) => {
        const href = link.getAttribute("href");
        if (href && !href.startsWith("#") && !href.startsWith("javascript")) {
            document.getElementById("page-loader").style.display = "flex";
        }
    });
});
</script>
