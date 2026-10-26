<?php
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

$id = $_GET['id'] ?? 0;
$id = (int)$id;

if ($id <= 0) {
    setFlash('error', 'Invalid user ID.');
    header('Location: view_users.php');
    exit;
}

if ($id === currentUserId()) {
    setFlash('error', 'You cannot delete yourself.');
    header('Location: view_users.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    setFlash('error', 'User not found.');
    header('Location: view_users.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'User deleted successfully.');
        header('Location: view_users.php');
        exit;
    } catch (PDOException $e) {
        echo '<div class="flash flash-error">Error deleting user: ' . escape($e->getMessage()) . '</div>';
    }
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<h2>Delete User</h2>

<div class="flash flash-error">
    <p><strong>Are you sure you want to delete this user?</strong></p>
</div>

<table>
    <tr>
        <th>ID</th>
        <td><?php echo escape($user['user_id']); ?></td>
    </tr>
    <tr>
        <th>Username</th>
        <td><?php echo escape($user['username']); ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?php echo escape($user['first_name'] . ' ' . $user['last_name']); ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><?php echo escape($user['email']); ?></td>
    </tr>
    <tr>
        <th>Role</th>
        <td><span class="role-badge <?php echo escape($user['role']); ?>"><?php echo escape(ucfirst($user['role'])); ?></span></td>
    </tr>
    <tr>
        <th>Created</th>
        <td><?php echo escape($user['created_at']); ?></td>
    </tr>
</table>

<form method="POST" action="delete_user.php?id=<?php echo escape($id); ?>">
    <input class="button" type="submit" value="Yes, Delete User" onclick="return confirm('Are you sure? This cannot be undone.');">
    <a class="link" href="view_users.php">Cancel</a>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>