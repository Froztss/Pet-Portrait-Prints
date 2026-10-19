<?php require_once __DIR__ . '/../includes/header.php'; ?>

<h2>All Customers</h2>

<?php echo flashHtml('success'); ?>
<?php echo flashHtml('error'); ?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Orders</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Get all customers and count their orders
        $stmt = $pdo->query("
            SELECT c.*, COUNT(o.order_id) AS order_count
            FROM customers c
            LEFT JOIN pet_portrait_orders o ON c.customer_id = o.customer_id
            GROUP BY c.customer_id
            ORDER BY c.customer_id DESC
        ");
        $customers = $stmt->fetchAll();

        if (count($customers) === 0) {
            echo '<tr><td colspan="7" class="text-center">No customers found.</td></tr>';
        }

        foreach ($customers as $customer) {
            echo '<tr>';
            echo '<td>' . escape($customer['customer_id']) . '</td>';
            echo '<td>' . escape($customer['customer_name']) . '</td>';
            echo '<td>' . escape($customer['email']) . '</td>';
            echo '<td>' . escape($customer['phone']) . '</td>';
            echo '<td>' . escape($customer['order_count']) . '</td>';
            echo '<td>' . escape($customer['created_at']) . '</td>';
            echo '<td class="action-links">';
            echo '<a class="link" href="edit_customer.php?id=' . escape($customer['customer_id']) . '">Edit</a>';
            echo '<a class="link" href="delete_customer.php?id=' . escape($customer['customer_id']) . '">Delete</a>';
            echo '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

<p><a class="button" href="add_customer.php">Add New Customer</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
