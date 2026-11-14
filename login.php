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
        $isLegacyHash = false;
        if ($user) {
            $storedPassword = $user['password_hash'];
            // Verify hashes while allowing older demo accounts to be upgraded on login.
            $isLegacyHash = password_get_info($storedPassword)['algoName'] !== 'unknown';
            $passwordMatches = $isLegacyHash
                ? password_verify($password, $storedPassword)
                : $password === $storedPassword;
        }

        if ($user && $passwordMatches) {
            // Replace an older plain-text password with a hash after a successful login.
            if (!$isLegacyHash) {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $update = $pdo->prepare('UPDATE users SET password_hash = ? WHERE user_id = ?');
                $update->execute([$passwordHash, $user['user_id']]);
            }
            session_regenerate_id(true);
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
            <?php echo rawOutput($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php" id="loginForm">
        <label class="label" for="username">Username *</label>
        <input
            class="input"
            type="text"
            id="username"
            name="username"
            value="<?php echo rawOutput($username); ?>"
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
document.getElementById('loginForm').addEventListener('submit', function() {
    var btn = document.getElementById('loginBtn');

    btn.classList.add('btn-loading');
    btn.value = 'Logging in...';
    btn.disabled = true;
});

window.addEventListener('pageshow', function() {
    var btn = document.getElementById('loginBtn');
    btn.classList.remove('btn-loading');
    btn.value = 'Login';
    btn.disabled = false;
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
