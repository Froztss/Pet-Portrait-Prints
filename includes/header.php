<?php require_once __DIR__ . '/../includes/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Portrait Prints</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Pet Portrait Prints</h1>
        <nav>
            <a href="<?php echo BASE_URL; ?>index.php">Home</a>
            <a href="<?php echo BASE_URL; ?>customers/view_customers.php">Customers</a>
            <a href="<?php echo BASE_URL; ?>orders/view_orders.php">Orders</a>
            <?php if (isAdmin()): ?>
                <a href="<?php echo BASE_URL; ?>users/view_users.php">Users</a>
            <?php endif; ?>
            <?php if (isLoggedIn()): ?>
                <span class="user-info">
                    Welcome, <?php echo escape(getCurrentUserName()); ?>
                    <?php if (isAdmin()): ?>
                        <span class="role-badge admin">Admin</span>
                    <?php else: ?>
                        <span class="role-badge user">User</span>
                    <?php endif; ?>
                </span>
                <a href="<?php echo BASE_URL; ?>logout.php" class="button" style="padding: 5px 10px; font-size: 12px;">Logout</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>login.php" class="button" style="padding: 5px 10px; font-size: 12px;">Login</a>
                <a href="<?php echo BASE_URL; ?>register.php" class="button" style="padding: 5px 10px; font-size: 12px;">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
