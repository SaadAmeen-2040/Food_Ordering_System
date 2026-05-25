// main.js
document.addEventListener('DOMContentLoaded', function() {
    // Basic interaction for cart and alerts could go here
    
    // Auto-hide alerts after 4 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 4000);
    });
});
