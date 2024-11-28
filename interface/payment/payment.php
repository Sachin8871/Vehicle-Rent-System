<?php
session_start();
include("../../connect.php");

$uid = $_POST['uid'] ?? '';
$vid = $_POST['vid'] ?? '';
$cid = $_POST['cid'] ?? '';
$destination = $_POST['destination'] ?? '';
$origin = $_POST['origin'] ?? '';  // Ensure 'origin' is received correctly
$rent = floatval($_POST['rent'] ?? '');
$booked = $_POST['booked'] ?? '';
$start_datetime = $_POST['start_datetime'] ?? '';
$end_datetime = $_POST['end_datetime'] ?? '';
$status = $_POST['status'] ?? '';

// Debugging: Ensure that the origin value is being received correctly
// echo "Origin: " . htmlspecialchars($origin);

// Convert string dates to DateTime objects and handle format
try {
    $start_datetime_obj = new DateTime($start_datetime);
    $end_datetime_obj = new DateTime($end_datetime);
    $start_datetime_str = $start_datetime_obj->format('Y-m-d H:i:s');
    $end_datetime_str = $end_datetime_obj->format('Y-m-d H:i:s');
} catch (Exception $e) {
    die("Invalid date/time format provided.");
}

// Calculate total cost
$duration = $end_datetime_obj->diff($start_datetime_obj);
$duration_hours = $duration->days * 24 + $duration->h + ($duration->i / 60);
$total_cost = floatval($rent * $duration_hours);
$total_cost = (string) $total_cost;

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['payment_method'])) {
    $payment_method = $_POST['payment_method'];
    $uid = htmlspecialchars($_POST['uid']);
    $vid = htmlspecialchars($_POST['vid']);
    $cid = htmlspecialchars($_POST['cid']);

    // Redirect to the appropriate payment page
    switch ($payment_method) {
        case 'DC':
            header("Location: payment_debit.php?uid=$uid&vid=$vid&cid=$cid&total_cost=$total_cost");
            exit;
        case 'CC':
            header("Location: payment_credit.php?uid=$uid&vid=$vid&cid=$cid&total_cost=$total_cost");
            exit;
        case 'NB':
            header("Location: payment_netbanking.php?uid=$uid&vid=$vid&cid=$cid&total_cost=$total_cost");
            exit;
        case 'UPI':
            header("Location: payment_upi.php?uid=$uid&vid=$vid&cid=$cid&total_cost=$total_cost");
            exit;
        default:
            echo "Invalid payment method.";
            exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="../../css/payment.css">
</head>
<body>
    <div class="payment-container" style="align-items: center;">
        <?php if (!isset($payment_status)) : ?>
            <h2>Payment</h2>
            <p>Total Amount: ₹<?php echo htmlspecialchars($total_cost); ?></p>

            <form action="payment.php" method="POST">
                <h2>Choose Payment Method:</h2>
                <select name="payment_method" required>
                    <option value="DC">Debit Card</option>
                    <option value="CC">Credit Card</option>
                    <option value="NB">Net Banking</option>
                    <option value="UPI">UPI</option>
                </select>
                <input type="hidden" name="uid" value="<?php echo htmlspecialchars($uid); ?>">
                <input type="hidden" name="vid" value="<?php echo htmlspecialchars($vid); ?>">
                <input type="hidden" name="cid" value="<?php echo htmlspecialchars($cid); ?>">
                <button type="submit">Proceed</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
