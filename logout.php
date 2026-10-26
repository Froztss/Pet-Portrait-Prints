<?php
require_once __DIR__ . '/includes/db_connect.php';

// If it's a POST request (from logout form), destroy session and redirect
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_destroy();
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="logout-overlay" id="logoutOverlay">
    <div class="spinner"></div>
</div>

<main style="text-align: center; padding: 60px 20px;">
    <h2>Logging out...</h2>
    <p>Please wait while we sign you out.</p>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit logout form
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = 'logout.php';
    document.body.appendChild(form);
    form.submit();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>