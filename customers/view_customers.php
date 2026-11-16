<?php
require_once __DIR__ . '/../includes/db_connect.php';
requireAuth();

$search = trim($_GET['search'] ?? '');

$customerQuery = "
    SELECT c.*, u1.username AS added_by_name, u2.username AS last_updated_by_name, COUNT(o.order_id) AS order_count
    FROM customers c
    LEFT JOIN pet_portrait_orders o ON c.customer_id = o.customer_id
    LEFT JOIN users u1 ON c.added_by = u1.user_id
    LEFT JOIN users u2 ON c.last_updated_by = u2.user_id
";

if ($search !== '') {
    $customerQuery .= "
        WHERE c.customer_name LIKE ? OR c.email LIKE ? OR c.phone LIKE ?
            OR CAST(c.customer_id AS CHAR) LIKE ?
    ";
}

$customerQuery .= "
    GROUP BY c.customer_id
    ORDER BY c.customer_id DESC
";

$stmt = $pdo->prepare($customerQuery);
if ($search !== '') {
    $searchTerm = '%' . $search . '%';
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
} else {
    $stmt->execute();
}
$customers = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<h2>All Customers</h2>

<?php echo flashHtml('success'); ?>
<?php echo flashHtml('error'); ?>

<form class="search-form" method="GET" action="view_customers.php">
    <div class="form-field">
        <label class="label" for="customer-search">Search customers</label>
        <input class="input" type="search" id="customer-search" name="search" value="<?php echo rawOutput($search); ?>" placeholder="Name, email, phone, or ID">
    </div>
    <button type="submit">Search</button>
    <?php if ($search !== ''): ?>
        <a class="button" href="view_customers.php">Clear</a>
    <?php endif; ?>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Orders</th>
            <th>Added By</th>
            <th>Last Updated By</th>
            <th>Date Added</th>
            <th>Last Updated</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (count($customers) === 0) {
            $emptyMessage = $search === '' ? 'No customers found.' : 'No customers found matching "' . rawOutput($search) . '".';
            echo '<tr><td colspan="10" class="text-center">' . $emptyMessage . '</td></tr>';
        }

        foreach ($customers as $customer) {
            echo '<tr>';
            echo '<td>' . rawOutput($customer['customer_id']) . '</td>';
            echo '<td>' . rawOutput($customer['customer_name']) . '</td>';
            echo '<td>' . rawOutput($customer['email']) . '</td>';
            echo '<td>' . rawOutput($customer['phone']) . '</td>';
            echo '<td>' . rawOutput($customer['order_count']) . '</td>';
            echo '<td>' . rawOutput($customer['added_by_name'] ?? 'N/A') . '</td>';
            $lastUpdatedBy = $customer['last_updated_by'] === null
                ? 'N/A'
                : '#' . $customer['last_updated_by'] . ' (' . ($customer['last_updated_by_name'] ?? 'Unknown user') . ')';
            echo '<td>' . rawOutput($lastUpdatedBy) . '</td>';
            echo '<td>' . rawOutput(formatDateTime($customer['date_added'])) . '</td>';
            echo '<td>' . rawOutput(formatDateTime($customer['last_updated'])) . '</td>';
            echo '<td class="action-links">';
            echo '<a class="link" href="edit_customer.php?id=' . rawOutput($customer['customer_id']) . '">Edit</a>';
            echo '<a class="link" href="delete_customer.php?id=' . rawOutput($customer['customer_id']) . '">Delete</a>';
            echo '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

<p><a class="button" href="add_customer.php">Add New Customer</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
