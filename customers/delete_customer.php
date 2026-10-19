<?php
require_once __DIR__ . '/../includes/db_connect.php';

// Get customer ID from URL
$id = $_GET['id'] ?? 0;
$id = (int)$id;

if ($id <= 0) {
    setFlash('error', 'Invalid customer ID.');
    header('Location: view_customers.php');
    exit;
}

// Fetch the customer
$stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->execute([$id]);
$customer = $stmt->fetch();

if (!$customer) {
    setFlash('error', 'Customer not found.');
    header('Location: view_customers.php');
    exit;
}

// Process deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("DELETE FROM customers WHERE customer_id = ?");
        $stmt->execute([$id]);
        // Orders cascade delete via foreign key ON DELETE CASCADE
        setFlash('success', 'Customer and all related orders deleted successfully.');
        header('Location: view_customers.php');
        exit;
    } catch (PDOException $e) {
        echo '<div class="flash flash-error">Error deleting customer: ' . escape($e->getMessage()) . '</div>';
    }
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<h2>Delete Customer</h2>

<div class="flash flash-error">
    <p><strong>Are you sure you want to delete this customer?</strong></p>
    <p>This will also delete all portrait orders linked to this customer.</p>
</div>

<table>
    <tr>
        <th>ID</th>
        <td><?php echo escape($customer['customer_id']); ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?php echo escape($customer['customer_name']); ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><?php echo escape($customer['email']); ?></td>
    </tr>
    <tr>
        <th>Phone</th>
        <td><?php echo escape($customer['phone']); ?></td>
    </tr>
    <tr>
        <th>Orders Count</th>
        <td>
            <?php
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM pet_portrait_orders WHERE customer_id = ?");
            $countStmt->execute([$id]);
            echo escape($countStmt->fetchColumn());
            ?>
        </td>
    </tr>
</table>

<form method="POST" action="delete_customer.php?id=<?php echo escape($id); ?>">
    <input class="button" type="submit" value="Yes, Delete Customer" onclick="return confirm('Are you sure? This cannot be undone.');">
    <a class="link" href="view_customers.php">Cancel</a>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
