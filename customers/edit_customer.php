<?php
require_once __DIR__ . '/../includes/db_connect.php';
requireAuth();

// Get customer ID from URL
$id = $_GET['id'] ?? 0;
$id = (int)$id;

if ($id <= 0) {
    setFlash('error', 'Invalid customer ID.');
    header('Location: view_customers.php');
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = trim($_POST['customer_name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');

    // Validation: required fields
    if ($customerName === '' || $email === '') {
        setFlash('error', 'Name and Email are required fields.');
        header('Location: edit_customer.php?id=' . $id);
        exit;
    }
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlash('error', 'Please enter a valid email address.');
        header('Location: edit_customer.php?id=' . $id);
        exit;
    }
    // Update the database
    try {
        $stmt = $pdo->prepare("UPDATE customers SET customer_name = ?, email = ?, phone = ?, updated_by = ? WHERE customer_id = ?");
        $stmt->execute([$customerName, $email, $phone, currentUserId(), $id]);
        // Fetch updated name for the success message
        $stmt = $pdo->prepare("SELECT customer_name FROM customers WHERE customer_id = ?");
        $stmt->execute([$id]);
        $updatedName = $stmt->fetchColumn();
        setFlash('success', 'Customer updated: ' . htmlspecialchars($updatedName ?? '', ENT_QUOTES, 'UTF-8'));
        header('Location: view_customers.php');
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            setFlash('error', 'This email is already registered.');
        } else {
            setFlash('error', 'Database error: ' . $e->getMessage());
        }
        header('Location: edit_customer.php?id=' . $id);
        exit;
    }
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
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<h2>Edit Customer</h2>

<?php echo flashHtml('success'); ?>
<?php echo flashHtml('error'); ?>

<form method="POST" action="edit_customer.php?id=<?php echo escape($id); ?>">
    <label class="label" for="customer_name">Name *</label>
    <input class="input" type="text" id="customer_name" name="customer_name" value="<?php echo escape($customer['customer_name']); ?>" required>

    <label class="label" for="email">Email *</label>
    <input class="input" type="email" id="email" name="email" value="<?php echo escape($customer['email']); ?>" required>

    <label class="label" for="phone">Phone</label>
    <input class="input" type="text" id="phone" name="phone" value="<?php echo escape($customer['phone']); ?>">

    <input class="button" type="submit" value="Update Customer">
    <a class="link" href="view_customers.php">Cancel</a>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
