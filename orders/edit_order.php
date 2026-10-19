<?php
require_once __DIR__ . '/../includes/db_connect.php';

// Get order ID from URL
$id = $_GET['id'] ?? 0;
$id = (int)$id;

if ($id <= 0) {
    setFlash('error', 'Invalid order ID.');
    header('Location: view_orders.php');
    exit;
}

// Fetch order and all customers
$stmt = $pdo->prepare("SELECT * FROM pet_portrait_orders WHERE order_id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    setFlash('error', 'Order not found.');
    header('Location: view_orders.php');
    exit;
}

$custStmt = $pdo->query("SELECT * FROM customers ORDER BY customer_name");
$customers = $custStmt->fetchAll();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId    = (int)($_POST['customer_id'] ?? 0);
    $petName       = trim($_POST['pet_name'] ?? '');
    $petType       = trim($_POST['pet_type'] ?? '');
    $portraitStyle = trim($_POST['portrait_style'] ?? '');
    $price         = $_POST['price'] ?? '';
    $orderStatus   = trim($_POST['order_status'] ?? 'Pending');

    // Prevent updating an order without selecting an existing customer
    if ($customerId <= 0) {
        setFlash('error', 'Please select an existing customer.');
        header('Location: edit_order.php?id=' . $id);
        exit;
    }
    // Validate required fields
    if ($petName === '' || $petType === '' || $portraitStyle === '' || $price === '') {
        setFlash('error', 'All required fields must not be empty.');
        header('Location: edit_order.php?id=' . $id);
        exit;
    }
    // Validate price is non-negative
    if (!is_numeric($price) || $price < 0) {
        setFlash('error', 'Price must be a non-negative number.');
        header('Location: edit_order.php?id=' . $id);
        exit;
    }
    // Verify customer exists
    $check = $pdo->prepare("SELECT customer_id FROM customers WHERE customer_id = ?");
    $check->execute([$customerId]);
    if ($check->rowCount() === 0) {
        setFlash('error', 'Selected customer does not exist.');
        header('Location: edit_order.php?id=' . $id);
        exit;
    }
    // Update the database
    try {
        $stmt = $pdo->prepare("
            UPDATE pet_portrait_orders
            SET customer_id = ?, pet_name = ?, pet_type = ?, portrait_style = ?, price = ?, order_status = ?
            WHERE order_id = ?
        ");
        $stmt->execute([$customerId, $petName, $petType, $portraitStyle, $price, $orderStatus, $id]);
        setFlash('success', 'Order #' . $id . ' updated: ' . htmlspecialchars($petName, ENT_QUOTES, 'UTF-8'));
        header('Location: view_orders.php');
        exit;
    } catch (PDOException $e) {
        setFlash('error', 'Database error: ' . $e->getMessage());
        header('Location: edit_order.php?id=' . $id);
        exit;
    }
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<h2>Edit Order</h2>

<?php echo flashHtml('success'); ?>
<?php echo flashHtml('error'); ?>

<form method="POST" action="edit_order.php?id=<?php echo escape($id); ?>">
    <label class="label" for="customer_id">Customer *</label>
    <select class="input" id="customer_id" name="customer_id" required>
        <option value="">-- Select a Customer --</option>
        <?php
        foreach ($customers as $customer) {
            $selected = ($customer['customer_id'] == $order['customer_id']) ? 'selected' : '';
            echo '<option value="' . escape($customer['customer_id']) . '" ' . $selected . '>' . escape($customer['customer_name']) . '</option>';
        }
        ?>
    </select>

    <label class="label" for="pet_name">Pet Name *</label>
    <input class="input" type="text" id="pet_name" name="pet_name" value="<?php echo escape($order['pet_name']); ?>" required>

    <label class="label" for="pet_type">Pet Type *</label>
    <input class="input" type="text" id="pet_type" name="pet_type" value="<?php echo escape($order['pet_type']); ?>" required>

    <label class="label" for="portrait_style">Portrait Style *</label>
    <input class="input" type="text" id="portrait_style" name="portrait_style" value="<?php echo escape($order['portrait_style']); ?>" required>

    <label class="label" for="price">Price *</label>
    <input class="input input-small" type="number" id="price" name="price" step="0.01" min="0" value="<?php echo escape($order['price']); ?>" required>

    <label class="label" for="order_status">Order Status</label>
    <input class="input input-medium" type="text" id="order_status" name="order_status" value="<?php echo escape($order['order_status']); ?>">

    <input class="button" type="submit" value="Update Order">
    <a class="link" href="view_orders.php">Cancel</a>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
