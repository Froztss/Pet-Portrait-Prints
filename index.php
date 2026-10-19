<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="hero">
    <h1>Welcome to Pet Portrait Prints</h1>
    <p class="tagline">Custom illustrated portraits of your beloved pets — digital downloads, framed prints, mugs, and shirts.</p>
</div>

<div class="features">
    <div class="feature">
        <h3>🎨 Custom Artwork</h3>
        <p>Hand-drawn illustrations capturing your pet's unique personality.</p>
    </div>
    <div class="feature">
        <h3>🖼️ Multiple Formats</h3>
        <p>Digital downloads, framed prints, mugs, shirts, and more.</p>
    </div>
    <div class="feature">
        <h3>🚀 Fast Turnaround</h3>
        <p>Receive your custom portrait within 5-7 business days.</p>
    </div>
</div>

<div class="dashboard">
    <h2>Quick Links</h2>
    <p>
        <a class="button" href="<?php echo BASE_URL; ?>customers/view_customers.php">View All Customers</a>
        <a class="button" href="<?php echo BASE_URL; ?>customers/add_customer.php">Add New Customer</a>
        <a class="button" href="<?php echo BASE_URL; ?>orders/view_orders.php">View All Orders</a>
        <a class="button" href="<?php echo BASE_URL; ?>orders/add_order.php">Add New Order</a>
    </p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
