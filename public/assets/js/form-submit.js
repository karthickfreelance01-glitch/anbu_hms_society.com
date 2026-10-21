document.addEventListener("DOMContentLoaded", function () {

    // Listen to all form submissions
    document.querySelectorAll("form").forEach(function (form) {

        form.addEventListener("submit", function (e) {

            // If form already submitting, prevent double submit
            if (form.dataset.submitting === "true") {
                e.preventDefault();
                return false;
            }

            // Mark as submitting
            form.dataset.submitting = "true";

            // Find the submit button
            let btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.dataset.originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Please wait...';
            }

        });

    });

});
