<?php
require_once __DIR__ . '/includes/db_connect.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim($_POST['username'] ?? '');
    $password     = $_POST['password'] ?? '';
    $confirm      = $_POST['confirm_password'] ?? '';
    $firstName    = trim($_POST['first_name'] ?? '');
    $lastName     = trim($_POST['last_name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $address      = trim($_POST['address'] ?? '');
    $age          = $_POST['age'] ?? '';
    $phone        = trim($_POST['phone'] ?? '');

    // Validation
    if ($username === '' || $password === '' || $firstName === '' || $lastName === '' || $email === '') {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif ($age !== '' && (!is_numeric($age) || $age < 0 || $age > 120)) {
        $error = 'Please enter a valid age.';
    } else {
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = 'Username or email already registered.';
        } else {
            // Hash password and insert user
            $hash = password_hash($password, PASSWORD_DEFAULT);
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO users (username, password_hash, first_name, last_name, email, address, age, phone)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$username, $hash, $firstName, $lastName, $email, $address, $age ?: null, $phone]);
                $success = 'Registration successful! You can now <a href="login.php">login</a>.';
            } catch (PDOException $e) {
                $error = 'Registration failed: ' . $e->getMessage();
            }
        }
    }
}
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="auth-container">
    <h2>Register</h2>

    <?php if ($error): ?>
        <div class="flash flash-error"><?php echo escape($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="flash flash-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php" id="registerForm">
        <label class="label" for="username">Username *</label>
        <input class="input" type="text" id="username" name="username" required autofocus>

        <label class="label" for="password">Password *</label>
        <input class="input" type="password" id="password" name="password" required minlength="6">

        <label class="label" for="confirm_password">Confirm Password *</label>
        <input class="input" type="password" id="confirm_password" name="confirm_password" required>

        <label class="label" for="first_name">First Name *</label>
        <input class="input" type="text" id="first_name" name="first_name" required>

        <label class="label" for="last_name">Last Name *</label>
        <input class="input" type="text" id="last_name" name="last_name" required>

        <label class="label" for="email">Email *</label>
        <input class="input" type="email" id="email" name="email" required>

        <label class="label" for="address">Address</label>
        <input class="input" type="text" id="address" name="address">

        <label class="label" for="age">Age</label>
        <input class="input input-small" type="number" id="age" name="age" min="0" max="120">

        <label class="label" for="phone">Phone</label>
        <input class="input" type="text" id="phone" name="phone">

        <input class="button" type="submit" value="Register" id="registerBtn">
        <a class="link" href="login.php">Already have an account? Login</a>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    var btn = document.getElementById('registerBtn');
    btn.classList.add('btn-loading');
    btn.value = '';
});
</script>