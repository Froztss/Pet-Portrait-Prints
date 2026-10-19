<?php
require_once __DIR__ . '/../includes/db_connect.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = trim($_POST['customer_name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');

    // Validation: required fields
    if ($customerName === '' || $email === '') {
        setFlash('error', 'Name and Email are required fields.');
        header('Location: add_customer.php');
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlash('error', 'Please enter a valid email address.');
        header('Location: add_customer.php');
        exit;
    }

    // Insert into database
    try {
        $stmt = $pdo->prepare("INSERT INTO customers (customer_name, email, phone) VALUES (?, ?, ?)");
        $stmt->execute([$customerName, $email, $phone]);
        setFlash('success', 'Customer added successfully!');
        header('Location: view_customers.php');
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            setFlash('error', 'This email is already registered.');
        } else {
            setFlash('error', 'Database error: ' . $e->getMessage());
        }
        header('Location: add_customer.php');
        exit;
    }
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<h2>Add New Customer</h2>

<?php echo flashHtml('success'); ?>
<?php echo flashHtml('error'); ?>

<form method="POST" action="add_customer.php">
    <label class="label" for="customer_name">Name *</label>
    <input class="input" type="text" id="customer_name" name="customer_name" required>

    <label class="label" for="email">Email *</label>
    <input class="input" type="email" id="email" name="email" required>

    <label class="label" for="phone">Phone</label>
    <input class="input" type="text" id="phone" name="phone">

    <input class="button" type="submit" value="Save Customer">
    <a class="link" href="view_customers.php">Cancel</a>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
