<?php
    session_start();

    include("../connect.php");
    $error = "";
    $message = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_SESSION['cid'])) {
            $cid = $_SESSION['cid'];
            $vid = $_POST["vid"];
            $type = $_POST["type"];
            $no_of_wheel = $_POST["no_of_wheel"];
            $no_of_seat = $_POST["no_of_seat"];
            $fuel = $_POST["fuel"];
            $mileage = $_POST["mileage"];
            $rent = $_POST["rent"];
            $no_of_vehicle = $_POST["no_of_vehicle"];
            $booked = 0;

            // Image upload handling
            if (isset($_FILES["img"]) && $_FILES["img"]["error"] == 0) {
                $img_name = $_FILES["img"]["name"];
                $img_tmp_name = $_FILES["img"]["tmp_name"];
                $img_path = "uploads/" . $img_name;

                // Ensure uploads folder exists
                if (!file_exists("uploads")) {
                    mkdir("uploads", 0777, true);
                }

                // Move the uploaded file
                if (move_uploaded_file($img_tmp_name, $img_path)) {
                    $img = $img_path;
                } else {
                    $error = "Failed to upload image.";
                }
            } else {
                $error = "No image selected or an error occurred.";
            }

            // Proceed only if there are no errors
            if (empty($error)) {
                $sql = "INSERT INTO Vehicle (vid, cid, type, no_of_wheel, no_of_seat, fuel, mileage, rent, img, no_of_vehicle, booked) 
                        VALUES (:vid, :cid, :type, :no_of_wheel, :no_of_seat, :fuel, :mileage, :rent, :img, :no_of_vehicle, :booked)";

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":vid", $vid, PDO::PARAM_STR);
                $stmt->bindParam(":cid", $cid, PDO::PARAM_STR);
                $stmt->bindParam(":type", $type, PDO::PARAM_STR);
                $stmt->bindParam(":no_of_wheel", $no_of_wheel, PDO::PARAM_INT);
                $stmt->bindParam(":no_of_seat", $no_of_seat, PDO::PARAM_INT);
                $stmt->bindParam(":no_of_vehicle", $no_of_vehicle, PDO::PARAM_INT);
                $stmt->bindParam(":fuel", $fuel, PDO::PARAM_STR);
                $stmt->bindParam(":mileage", $mileage, PDO::PARAM_INT);
                $stmt->bindParam(":rent", $rent, PDO::PARAM_INT);
                $stmt->bindParam(":img", $img, PDO::PARAM_STR);
                $stmt->bindParam(":booked", $booked, PDO::PARAM_INT);
                $stmt->execute();

                $message = "Vehicle added successfully.";
            } else {
                echo "<p style='color: red;'>$error</p>";
            }
        } else {
            $error = "Session 'cid' not set.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/add_vehicle.css">
    <title>Add Vehicle</title>
</head>
<body>
    <div class="login-container">
        <h2>Add Vehicle</h2>
        <form action="add_vehicle.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="vid" placeholder="Vehicle ID" required> 
            <input type="text" name="type" placeholder="Type" required> 
            <input type="number" name="no_of_wheel" placeholder="Number Of Wheels" required> 
            <input type="number" name="no_of_seat" placeholder="Number Of Seats" required>
            <select name="fuel" required>
                <option value="petrol">Petrol</option>
                <option value="diesel">Diesel</option>
            </select>
            <input type="number" name="mileage" placeholder="Mileage" required> 
            <input type="number" name="rent" placeholder="Rent (per hour)" required>
            <input type="number" name="no_of_vehicle" placeholder="Number Of Vehicle" required>
            <input type="file" name="img" accept="image/*" required>
            <button type="submit">Add</button>
            <button type="reset">Reset</button>
        </form>
        <?php
            if (!empty($error)) {
                echo "<p style='color: red;'>$error</p>";
            } else {
                echo "<p style='color: green;'>$message</p>";
            }
        ?>
    </div>
</body>
</html>
