<?php
require_once __DIR__ . '/../includes/db_connect.php';
requireAuth();

$search = trim($_GET['search'] ?? '');

$orderQuery = "
    SELECT o.*, c.customer_name, u1.username AS added_by_name, u2.username AS last_updated_by_name
    FROM pet_portrait_orders o
    JOIN customers c ON o.customer_id = c.customer_id
    LEFT JOIN users u1 ON o.added_by = u1.user_id
    LEFT JOIN users u2 ON o.last_updated_by = u2.user_id
";

if ($search !== '') {
    $orderQuery .= "
        WHERE c.customer_name LIKE ? OR o.pet_name LIKE ? OR o.pet_type LIKE ?
            OR o.portrait_style LIKE ? OR o.order_status LIKE ?
            OR CAST(o.order_id AS CHAR) LIKE ? OR CAST(o.customer_id AS CHAR) LIKE ?
    ";
}

$orderQuery .= " ORDER BY o.order_id DESC";

$stmt = $pdo->prepare($orderQuery);
if ($search !== '') {
    $searchTerm = '%' . $search . '%';
    $stmt->execute(array_fill(0, 7, $searchTerm));
} else {
    $stmt->execute();
}
$orders = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<h2>All Orders</h2>

<?php echo flashHtml('success'); ?>
<?php echo flashHtml('error'); ?>

<form class="search-form" method="GET" action="view_orders.php">
    <div class="form-field">
        <label class="label" for="order-search">Search orders</label>
        <input class="input" type="search" id="order-search" name="search" value="<?php echo rawOutput($search); ?>" placeholder="Customer, pet, status, or ID">
    </div>
    <button type="submit">Search</button>
    <?php if ($search !== ''): ?>
        <a class="button" href="view_orders.php">Clear</a>
    <?php endif; ?>
</form>

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
            <th>Added By</th>
            <th>Last Updated By</th>
            <th>Date Added</th>
            <th>Last Updated</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (count($orders) === 0) {
            $emptyMessage = $search === '' ? 'No orders found.' : 'No orders found matching "' . rawOutput($search) . '".';
            echo '<tr><td colspan="12" class="text-center">' . $emptyMessage . '</td></tr>';
        }

        foreach ($orders as $order) {
            $rowClass = ($order['order_status'] === 'Completed') ? ' class="status-completed"' : '';
            echo '<tr' . $rowClass . '>';
            echo '<td>' . rawOutput($order['order_id']) . '</td>';
            echo '<td>' . rawOutput($order['customer_name']) . '</td>';
            echo '<td>' . rawOutput($order['pet_name']) . '</td>';
            echo '<td>' . rawOutput($order['pet_type']) . '</td>';
            echo '<td>' . rawOutput($order['portrait_style']) . '</td>';
            echo '<td>' . rawOutput(number_format($order['price'], 2)) . '</td>';
            echo '<td>' . rawOutput($order['order_status']) . '</td>';
            echo '<td>' . rawOutput($order['added_by_name'] ?? 'N/A') . '</td>';
            $lastUpdatedBy = $order['last_updated_by'] === null
                ? 'N/A'
                : '#' . $order['last_updated_by'] . ' (' . ($order['last_updated_by_name'] ?? 'Unknown user') . ')';
            echo '<td>' . rawOutput($lastUpdatedBy) . '</td>';
            echo '<td>' . rawOutput(formatDateTime($order['date_added'])) . '</td>';
            echo '<td>' . rawOutput(formatDateTime($order['last_updated'])) . '</td>';
            echo '<td class="action-links">';
            echo '<a class="link" href="edit_order.php?id=' . rawOutput($order['order_id']) . '">Edit</a>';
            echo '<a class="link" href="delete_order.php?id=' . rawOutput($order['order_id']) . '">Delete</a>';
            echo '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

<p><a class="button" href="add_order.php">Add New Order</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
