document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.getElementById("toggleSidebar");
    const menuToggles = document.querySelectorAll(".menu-toggle");
    const loader = document.getElementById("pageLoader");

    // Sidebar collapse toggle
    toggleBtn.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
    });

    // Submenu dropdown toggle
    menuToggles.forEach(toggle => {
        toggle.addEventListener("click", () => {
            const parent = toggle.closest(".submenu");
            parent.classList.toggle("active");
        });
    });

    // Top bar loader effect on link click
    const links = document.querySelectorAll("a[href]");
    links.forEach(link => {
        link.addEventListener("click", (e) => {
            const href = link.getAttribute("href");
            if (href && !href.startsWith("#") && !href.startsWith("javascript")) {
                e.preventDefault();
                startLoader();
                setTimeout(() => {
                    window.location.href = href;
                }, 500);
            }
        });
    });

    function startLoader() {
        loader.style.width = "0%";
        loader.style.display = "block";
        setTimeout(() => loader.style.width = "100%", 100);
        setTimeout(() => {
            loader.style.width = "0%";
            loader.style.display = "none";
        }, 1000);
    }
});
