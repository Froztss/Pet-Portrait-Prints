<?php
require_once __DIR__ . '/includes/db_connect.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$error = '';

$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        $passwordMatches = false;
        if ($user) {
            $storedPassword = $user['password_hash'];
            // Continue accepting accounts created before plain-text storage was enabled.
            $isLegacyHash = password_get_info($storedPassword)['algoName'] !== 'unknown';
            $passwordMatches = $isLegacyHash
                ? password_verify($password, $storedPassword)
                : $password === $storedPassword;
        }

        if ($passwordMatches) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['role'] = $user['role'];

            header('Location: ' . BASE_URL . 'index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="auth-container">
    <h2>Login</h2>

    <?php if ($error): ?>
        <div class="flash flash-error">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php" id="loginForm">
        <label class="label" for="username">Username *</label>
        <input
            class="input"
            type="text"
            id="username"
            name="username"
            value="<?php echo $username; ?>"
            required
            autofocus
        >

        <label class="label" for="password">Password *</label>
        <input
            class="input"
            type="password"
            id="password"
            name="password"
            required
        >

        <input
            class="button"
            type="submit"
            value="Login"
            id="loginBtn"
        >

        <a class="link" href="register.php">Create an account</a>
    </form>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    var btn = document.getElementById('loginBtn');

    btn.classList.add('btn-loading');
    btn.value = '';
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
