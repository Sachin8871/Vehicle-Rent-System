<?php
session_start();
include("../connect.php");

if (isset($_GET['vid']) && isset($_GET['cid'])) {
    $vid = $_GET['vid'];
    $cid = $_GET['cid'];

    // Fetch vehicle details
    $sql = "SELECT * FROM Vehicle WHERE vid = :vid AND cid = :cid";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':vid', $vid);
    $stmt->bindParam(':cid', $cid);
    $stmt->execute();

    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get updated values from the form
    $type = $_POST['type'];
    $no_of_wheel = $_POST['no_of_wheel'];
    $no_of_seat = $_POST['no_of_seat'];
    $fuel = $_POST['fuel'];
    $mileage = $_POST['mileage'];
    $rent = $_POST['rent'];
    $no_of_vehicle = $_POST['no_of_vehicle'];
    $img = $_POST['img']; // Assuming you handle image uploads separately

    // Update the vehicle details in the database
    $updateSql = "UPDATE Vehicle SET type = :type, no_of_wheel = :no_of_wheel, no_of_seat = :no_of_seat, 
                  fuel = :fuel, mileage = :mileage, rent = :rent, no_of_vehicle = :no_of_vehicle, img = :img 
                  WHERE vid = :vid AND cid = :cid";

    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bindParam(':type', $type);
    $updateStmt->bindParam(':no_of_wheel', $no_of_wheel);
    $updateStmt->bindParam(':no_of_seat', $no_of_seat);
    $updateStmt->bindParam(':fuel', $fuel);
    $updateStmt->bindParam(':mileage', $mileage);
    $updateStmt->bindParam(':rent', $rent);
    $updateStmt->bindParam(':no_of_vehicle', $no_of_vehicle);
    $updateStmt->bindParam(':img', $img);
    $updateStmt->bindParam(':vid', $vid);
    $updateStmt->bindParam(':cid', $cid);
    $updateStmt->execute();

    // Redirect back to the company dashboard
    header("Location: company.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/edit.css">
    <title>Edit Vehicle</title>
</head>
<body>
    <div class="login-container">
        <h1>Edit Vehicle</h1>
        <form method="post" action="">
            <label for="type">Type:</label>
            <input type="text" name="type" value="<?php echo htmlspecialchars($vehicle['type']); ?>" required>
            
            <div class="row">
                <label for="no_of_wheel">No. of Wheels:</label>
                <input type="number" name="no_of_wheel" value="<?php echo htmlspecialchars($vehicle['no_of_wheel']); ?>" required>
                
                <label for="no_of_seat">No. of Seats:</label>
                <input type="number" name="no_of_seat" value="<?php echo htmlspecialchars($vehicle['no_of_seat']); ?>" required>
            </div>
            
            <label for="fuel">Fuel:</label>
            <select name="fuel" required>
                <option value="petrol" <?php echo ($vehicle['fuel'] == 'petrol') ? 'selected' : ''; ?>>Petrol</option>
                <option value="diesel" <?php echo ($vehicle['fuel'] == 'diesel') ? 'selected' : ''; ?>>Diesel</option>
            </select>
            
            <label for="mileage">Mileage:</label>
            <input type="number" step="0.01" name="mileage" value="<?php echo htmlspecialchars($vehicle['mileage']); ?>" required>
            
            <label for="rent">Rent:</label>
            <input type="number" step="0.01" name="rent" value="<?php echo htmlspecialchars($vehicle['rent']); ?>" required>
            
            <label for="no_of_vehicle">No. of Vehicles:</label>
            <input type="number" name="no_of_vehicle" value="<?php echo htmlspecialchars($vehicle['no_of_vehicle']); ?>" required>
            
            <label for="img">Image URL:</label>
            <input type="text" name="img" value="<?php echo htmlspecialchars($vehicle['img']); ?>" required>
            
            <button type="submit">Update Vehicle</button>
        </form>
    </div>
</body>
</html>
