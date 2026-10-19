<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="business-list">
    <h2>Business Choices</h2>
    <ol>
        <li class="business-choice">Pet Portrait Prints — a business that creates custom illustrated portraits of customers' pets and sells them as digital downloads, framed prints, mugs, and shirts.</li>
        <li>Mobile Pet Grooming — a business that provides at-home grooming services for dogs and cats.</li>
        <li>Eco-Friendly Cleaning Kits — a business that sells sustainable cleaning supplies through subscription boxes.</li>
    </ol>
</div>

<div class="dashboard">
    <h2>One-to-Many Relationship</h2>
    <p><strong>ONE CUSTOMER can have MANY PET PORTRAIT ORDERS</strong> registered in the database.</p>
    <ul>
        <li>Parent entity: <strong>Customer</strong></li>
        <li>Child entity: <strong>Pet Portrait Order</strong></li>
        <li>Foreign key: <code>customer_id</code> in the <code>pet_portrait_orders</code> table</li>
        <li>Deleting a customer also deletes all their orders (ON DELETE CASCADE).</li>
    </ul>

    <h2 style="margin-top: 30px;">Quick Links</h2>
    <p>
        <a class="button" href="/AppDev_10_19_2026/customers/view_customers.php">View All Customers</a>
        <a class="button" href="/AppDev_10_19_2026/customers/add_customer.php">Add New Customer</a>
        <a class="button" href="/AppDev_10_19_2026/orders/view_orders.php">View All Orders</a>
        <a class="button" href="/AppDev_10_19_2026/orders/add_order.php">Add New Order</a>
    </p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
