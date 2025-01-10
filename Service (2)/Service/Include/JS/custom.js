document.querySelector('a[href="logout.php"]').addEventListener('click', function(event) {
    if (!confirm('Do you want to logout?')) {
        event.preventDefault();
    }
});
