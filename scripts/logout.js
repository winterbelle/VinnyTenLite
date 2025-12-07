document.addEventListener("DOMContentLoaded", () => {
    const alertBox = document.getElementById('logout-alert');
    if (alertBox) {
        setTimeout(() => {
            alertBox.remove();
        }, 1800); // matches animation duration
    }
});

