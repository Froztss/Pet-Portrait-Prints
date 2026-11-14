<?php
require_once __DIR__ . '/../includes/db_connect.php';
requireAdmin();
require_once __DIR__ . '/../includes/header.php';
?>

<h2>All Users</h2>

<?php echo flashHtml('success'); ?>
<?php echo flashHtml('error'); ?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $pdo->query("SELECT * FROM users ORDER BY user_id DESC");
        $users = $stmt->fetchAll();

        if (count($users) === 0) {
            echo '<tr><td colspan="7" class="text-center">No users found.</td></tr>';
        }

        foreach ($users as $user) {
            echo '<tr>';
            echo '<td>' . rawOutput($user['user_id']) . '</td>';
            echo '<td>' . rawOutput($user['username']) . '</td>';
            echo '<td>' . rawOutput($user['first_name'] . ' ' . $user['last_name']) . '</td>';
            echo '<td>' . rawOutput($user['email']) . '</td>';
            echo '<td><span class="role-badge ' . rawOutput($user['role']) . '">' . rawOutput(ucfirst($user['role'])) . '</span></td>';
            echo '<td>' . rawOutput(formatDateTime($user['created_at'])) . '</td>';
            echo '<td class="action-links">';
            if ($user['user_id'] !== currentUserId()) {
                echo '<a class="link" href="delete_user.php?id=' . rawOutput($user['user_id']) . '" onclick="return confirm(\'Delete this user?\');">Delete</a>';
            }
            echo '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

<p><a class="button" href="register.php">Add New User</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>