<?php
require_once __DIR__ . '/../includes/db_connect.php';
requireAuth();

// Get order ID from URL
$id = $_GET['id'] ?? 0;
$id = (int)$id;

if ($id <= 0) {
    setFlash('error', 'Invalid order ID.');
    header('Location: view_orders.php');
    exit;
}

// Fetch the order with customer name
$stmt = $pdo->prepare("
    SELECT o.*, c.customer_name
    FROM pet_portrait_orders o
    JOIN customers c ON o.customer_id = c.customer_id
    WHERE o.order_id = ?
");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    setFlash('error', 'Order not found.');
    header('Location: view_orders.php');
    exit;
}

// Process deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("DELETE FROM pet_portrait_orders WHERE order_id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Order deleted successfully.');
        header('Location: view_orders.php');
        exit;
    } catch (PDOException $e) {
        echo '<div class="flash flash-error">Error deleting order: ' . escape($e->getMessage()) . '</div>';
    }
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<h2>Delete Order</h2>

<div class="flash flash-error">
    <p><strong>Are you sure you want to delete this order?</strong></p>
    <p>This action cannot be undone.</p>
</div>

<table>
    <tr>
        <th>ID</th>
        <td><?php echo escape($order['order_id']); ?></td>
    </tr>
    <tr>
        <th>Customer</th>
        <td><?php echo escape($order['customer_name']); ?></td>
    </tr>
    <tr>
        <th>Pet Name</th>
        <td><?php echo escape($order['pet_name']); ?></td>
    </tr>
    <tr>
        <th>Pet Type</th>
        <td><?php echo escape($order['pet_type']); ?></td>
    </tr>
    <tr>
        <th>Portrait Style</th>
        <td><?php echo escape($order['portrait_style']); ?></td>
    </tr>
    <tr>
        <th>Price</th>
        <td>$<?php echo escape(number_format($order['price'], 2)); ?></td>
    </tr>
    <tr>
        <th>Status</th>
        <td><?php echo escape($order['order_status']); ?></td>
    </tr>
</table>

<form method="POST" action="delete_order.php?id=<?php echo escape($id); ?>">
    <input class="button" type="submit" value="Yes, Delete Order" onclick="return confirm('Are you sure? This cannot be undone.');">
    <a class="link" href="view_orders.php">Cancel</a>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
