<?php
require_once __DIR__ . '/../includes/header.php';
requireAdmin();
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
            echo '<td>' . escape($user['user_id']) . '</td>';
            echo '<td>' . escape($user['username']) . '</td>';
            echo '<td>' . escape($user['first_name'] . ' ' . $user['last_name']) . '</td>';
            echo '<td>' . escape($user['email']) . '</td>';
            echo '<td><span class="role-badge ' . escape($user['role']) . '">' . escape(ucfirst($user['role'])) . '</span></td>';
            echo '<td>' . escape($user['created_at']) . '</td>';
            echo '<td class="action-links">';
            if ($user['user_id'] !== currentUserId()) {
                echo '<a class="link" href="delete_user.php?id=' . escape($user['user_id']) . '" onclick="return confirm(\'Delete this user?\');">Delete</a>';
            }
            echo '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

<p><a class="button" href="register.php">Add New User</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>