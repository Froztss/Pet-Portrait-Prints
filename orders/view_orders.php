<?php require_once __DIR__ . '/../includes/header.php'; ?>

<h2>All Orders</h2>

<?php echo flashHtml('success'); ?>
<?php echo flashHtml('error'); ?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Pet Name</th>
            <th>Pet Type</th>
            <th>Portrait Style</th>
            <th>Price</th>
            <th>Status</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Get all orders with customer name
        $stmt = $pdo->query("
            SELECT o.*, c.customer_name
            FROM pet_portrait_orders o
            JOIN customers c ON o.customer_id = c.customer_id
            ORDER BY o.order_id DESC
        ");
        $orders = $stmt->fetchAll();

        if (count($orders) === 0) {
            echo '<tr><td colspan="9" class="text-center">No orders found.</td></tr>';
        }

        foreach ($orders as $order) {
            echo '<tr>';
            echo '<td>' . escape($order['order_id']) . '</td>';
            echo '<td>' . escape($order['customer_name']) . '</td>';
            echo '<td>' . escape($order['pet_name']) . '</td>';
            echo '<td>' . escape($order['pet_type']) . '</td>';
            echo '<td>' . escape($order['portrait_style']) . '</td>';
            echo '<td>' . escape(number_format($order['price'], 2)) . '</td>';
            echo '<td>' . escape($order['order_status']) . '</td>';
            echo '<td>' . escape($order['created_at']) . '</td>';
            echo '<td class="action-links">';
            echo '<a class="link" href="edit_order.php?id=' . escape($order['order_id']) . '">Edit</a>';
            echo '<a class="link" href="delete_order.php?id=' . escape($order['order_id']) . '">Delete</a>';
            echo '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

<p><a class="button" href="add_order.php">Add New Order</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
