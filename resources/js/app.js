// ==========================================
// PETANGBULA — Global JS
// ==========================================

// Auto-dismiss alert setelah 5 detik
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.auto-dismiss').forEach(function(el) {
        setTimeout(function() {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(function() { el.remove(); }, 500);
        }, 5000);
    });
});