<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debit Card Payment</title>
    <link rel="stylesheet" href="../../css/payment.css">
</head>
<body>
    <div class="payment-container">
    <h2>Debit Card Payment</h2>
    <form action="process_credit.php" method="POST">
        <label for="card_number">Card Number:</label>
        <input type="text" name="card_number" required>
        
        <label for="expiry_date">Expiry Date:</label>
        <input type="month" name="expiry_date" required>

        <label for="cvv">CVV:</label>
        <input type="password" name="cvv" required>
        
        <button type="submit">Pay Now</button>
    </form>
    </div>
</body>
</html>
