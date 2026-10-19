<?php
require_once __DIR__ . '/../includes/db_connect.php';

// Dropdown choices
$petTypes = ['Dog', 'Cat', 'Rabbit', 'Bird', 'Hamster', 'Guinea Pig', 'Horse', 'Reptile', 'Other'];
$portraitStyles = ['Watercolor', 'Cartoon', 'Pencil Sketch', 'Oil Painting', 'Pop Art', 'Minimalist', 'Royal/Renaissance', 'Other'];

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

// Determine selected values for dropdowns (handle custom values)
$savedPetType = $order['pet_type'];
$savedStyle = $order['portrait_style'];
$petTypeSelected = in_array($savedPetType, $petTypes) ? $savedPetType : 'Other';
$styleSelected = in_array($savedStyle, $portraitStyles) ? $savedStyle : 'Other';
$petTypeOtherValue = ($petTypeSelected === 'Other') ? $savedPetType : '';
$styleOtherValue = ($styleSelected === 'Other') ? $savedStyle : '';

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
        if ($petTypeOther === '') {
            setFlash('error', 'Please specify the pet type.');
            header('Location: edit_order.php?id=' . $id);
            exit;
        }
        $petType = $petTypeOther;
    }
    if ($portraitStyle === 'Other') {
        if ($styleOther === '') {
            setFlash('error', 'Please specify the portrait style.');
            header('Location: edit_order.php?id=' . $id);
            exit;
        }
        $portraitStyle = $styleOther;
    }

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
    <select class="input" id="pet_type" name="pet_type" required onchange="toggleOther(this, 'pet_type_other')">
        <option value="">-- Select Pet Type --</option>
        <?php foreach ($petTypes as $type): ?>
            <option value="<?php echo escape($type); ?>" <?php echo ($type === $petTypeSelected) ? 'selected' : ''; ?>><?php echo escape($type); ?></option>
        <?php endforeach; ?>
    </select>
    <input class="input" type="text" id="pet_type_other" name="pet_type_other" placeholder="Please specify" value="<?php echo escape($petTypeOtherValue); ?>" style="display:<?php echo $petTypeSelected === 'Other' ? 'block' : 'none'; ?>; margin-top:5px;">

    <label class="label" for="portrait_style">Portrait Style *</label>
    <select class="input" id="portrait_style" name="portrait_style" required onchange="toggleOther(this, 'portrait_style_other')">
        <option value="">-- Select Portrait Style --</option>
        <?php foreach ($portraitStyles as $style): ?>
            <option value="<?php echo escape($style); ?>" <?php echo ($style === $styleSelected) ? 'selected' : ''; ?>><?php echo escape($style); ?></option>
        <?php endforeach; ?>
    </select>
    <input class="input" type="text" id="portrait_style_other" name="portrait_style_other" placeholder="Please specify" value="<?php echo escape($styleOtherValue); ?>" style="display:<?php echo $styleSelected === 'Other' ? 'block' : 'none'; ?>; margin-top:5px;">

    <label class="label" for="price">Price *</label>
    <input class="input input-small" type="number" id="price" name="price" step="0.01" min="0" value="<?php echo escape($order['price']); ?>" required>

    <label class="label" for="order_status">Order Status</label>
    <input class="input input-medium" type="text" id="order_status" name="order_status" value="<?php echo escape($order['order_status']); ?>">

    <input class="button" type="submit" value="Update Order">
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
