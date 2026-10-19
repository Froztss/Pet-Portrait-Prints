<?php
require_once __DIR__ . '/../includes/db_connect.php';

// Dropdown choices
$petTypes = ['Dog', 'Cat', 'Rabbit', 'Bird', 'Hamster', 'Guinea Pig', 'Horse', 'Reptile', 'Other'];
$portraitStyles = ['Watercolor', 'Cartoon', 'Pencil Sketch', 'Oil Painting', 'Pop Art', 'Minimalist', 'Royal/Renaissance', 'Other'];

// Get all customers for the dropdown
$stmt = $pdo->query("SELECT * FROM customers ORDER BY customer_name");
$customers = $stmt->fetchAll();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId    = (int)($_POST['customer_id'] ?? 0);
    $petName       = trim($_POST['pet_name'] ?? '');
    $petType       = trim($_POST['pet_type'] ?? '');
    $petTypeOther  = trim($_POST['pet_type_other'] ?? '');
    $portraitStyle = trim($_POST['portrait_style'] ?? '');
    $styleOther    = trim($_POST['portrait_style_other'] ?? '');
    $price         = $_POST['price'] ?? '';
    $orderStatus   = trim($_POST['order_status'] ?? 'Pending');

    // Resolve "Other" values
    if ($petType === 'Other') {
        $petType = $petTypeOther;
    }
    if ($portraitStyle === 'Other') {
        $portraitStyle = $styleOther;
    }

    // Prevent adding an order without selecting an existing customer
    if ($customerId <= 0) {
        setFlash('error', 'Please select an existing customer.');
        header('Location: add_order.php');
        exit;
    }
    // Validate required fields
    if ($petName === '' || $petType === '' || $portraitStyle === '' || $price === '') {
        setFlash('error', 'All required fields must not be empty.');
        header('Location: add_order.php');
        exit;
    }
    // Validate price is non-negative
    if (!is_numeric($price) || $price < 0) {
        setFlash('error', 'Price must be a non-negative number.');
        header('Location: add_order.php');
        exit;
    }
    // Verify customer exists
    $check = $pdo->prepare("SELECT customer_id FROM customers WHERE customer_id = ?");
    $check->execute([$customerId]);
    if ($check->rowCount() === 0) {
        setFlash('error', 'Selected customer does not exist.');
        header('Location: add_order.php');
        exit;
    }
    // Insert into database
    try {
        $stmt = $pdo->prepare("
            INSERT INTO pet_portrait_orders
                (customer_id, pet_name, pet_type, portrait_style, price, order_status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$customerId, $petName, $petType, $portraitStyle, $price, $orderStatus]);
        $orderId = $pdo->lastInsertId();
        setFlash('success', 'Order #' . $orderId . ' added: ' . htmlspecialchars($petName, ENT_QUOTES, 'UTF-8'));
        header('Location: view_orders.php');
        exit;
    } catch (PDOException $e) {
        setFlash('error', 'Database error: ' . $e->getMessage());
        header('Location: add_order.php');
        exit;
    }
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<h2>Add New Order</h2>

<?php echo flashHtml('success'); ?>
<?php echo flashHtml('error'); ?>

<form method="POST" action="add_order.php">
    <label class="label" for="customer_id">Customer *</label>
    <select class="input" id="customer_id" name="customer_id" required>
        <option value="">-- Select a Customer --</option>
        <?php
        foreach ($customers as $customer) {
            echo '<option value="' . escape($customer['customer_id']) . '">' . escape($customer['customer_name']) . '</option>';
        }
        ?>
    </select>

    <label class="label" for="pet_name">Pet Name *</label>
    <input class="input" type="text" id="pet_name" name="pet_name" required>

    <label class="label" for="pet_type">Pet Type *</label>
    <select class="input" id="pet_type" name="pet_type" required onchange="toggleOther(this, 'pet_type_other')">
        <option value="">-- Select Pet Type --</option>
        <?php foreach ($petTypes as $type): ?>
            <option value="<?php echo escape($type); ?>"><?php echo escape($type); ?></option>
        <?php endforeach; ?>
    </select>
    <input class="input" type="text" id="pet_type_other" name="pet_type_other" placeholder="Please specify" style="display:none; margin-top:5px;">

    <label class="label" for="portrait_style">Portrait Style *</label>
    <select class="input" id="portrait_style" name="portrait_style" required onchange="toggleOther(this, 'portrait_style_other')">
        <option value="">-- Select Portrait Style --</option>
        <?php foreach ($portraitStyles as $style): ?>
            <option value="<?php echo escape($style); ?>"><?php echo escape($style); ?></option>
        <?php endforeach; ?>
    </select>
    <input class="input" type="text" id="portrait_style_other" name="portrait_style_other" placeholder="Please specify" style="display:none; margin-top:5px;">

    <label class="label" for="price">Price *</label>
    <input class="input input-small" type="number" id="price" name="price" step="0.01" min="0" required>

    <label class="label" for="order_status">Order Status</label>
    <input class="input input-medium" type="text" id="order_status" name="order_status" value="Pending">

    <input class="button" type="submit" value="Save Order">
    <a class="link" href="view_orders.php">Cancel</a>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<script>
function toggleOther(select, otherId) {
    var other = document.getElementById(otherId);
    if (select.value === 'Other') {
        other.style.display = 'block';
        other.required = true;
    } else {
        other.style.display = 'none';
        other.required = false;
        other.value = '';
    }
}
</script>
