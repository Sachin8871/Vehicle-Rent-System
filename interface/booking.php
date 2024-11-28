<?php
    session_start();
    include("../connect.php");

    // Retrieve POST data
    $f_name = $_POST['f_name'] ?? '';
    $l_name = $_POST['l_name'] ?? '';
    $uid = $_POST['uid'] ?? '';
    $email = $_SESSION['email'] ?? '';
    $origin = $_POST['origin'];
    $destination = $_POST['destination'];
    $start_datetime = $_POST['start_datetime'];
    $end_datetime = $_POST['end_datetime'];
    $license = $_POST['license'];

    try {
        $sql = "SELECT * FROM Company c RIGHT JOIN Vehicle v ON c.cid = v.cid";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/interface_booking.css">
    <title>Vehicle Booking</title>
</head>
<body>
    <div class="main-content">
        <div class="Info">
            <div class="cname">
                <p>🧑 <?php echo htmlspecialchars($f_name) . " " . htmlspecialchars($l_name); ?></p>
            </div>
            <div class="cemail">
                <p>✉️ <?php echo htmlspecialchars($email); ?></p>
                <p>🆔 <?php echo htmlspecialchars($uid); ?></p>
            </div>
        </div>
    
        <div class="table">
            <div class="table-name">
                <h2>Vehicles</h2>
            </div>
            <table class="vehicle-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Type</th>
                        <th>Vehicle ID</th>
                        <th>Company ID</th>
                        <th>Company Name</th>
                        <th>No. of Wheels</th>
                        <th>No. of Seats</th>
                        <th>Fuel</th>
                        <th>Mileage</th>
                        <th>Rent</th>
                        <th>Availability</th>
                        <th>Book</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($vehicles)) : ?>
                        <?php foreach ($vehicles as $vehicle) : ?>
                            <tr>
                                <td>
                                    <?php if (!empty($vehicle['img'])) : ?>
                                        <img src="<?php echo htmlspecialchars($vehicle['img']); ?>" alt="Vehicle Image" width="50" height="50">
                                    <?php else : ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($vehicle['type']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['vid']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['cid']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['name']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['no_of_wheel']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['no_of_seat']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['fuel']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['mileage']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['rent']); ?></td>
                                <td>
                                    <?php
                                        if ($vehicle['no_of_vehicle'] - $vehicle['booked'] > 0) {
                                            echo htmlspecialchars("Yes");
                                        } else {
                                            echo htmlspecialchars("No");
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($vehicle['no_of_vehicle'] - $vehicle['booked'] > 0) : ?>
                                        <form action="payment/payment.php" method="POST">
                                            <!-- Pass necessary details as hidden inputs -->
                                            <input type="hidden" name="uid" value="<?php echo htmlspecialchars($uid); ?>">
                                            <input type="hidden" name="vid" value="<?php echo htmlspecialchars($vehicle['vid']); ?>">
                                            <input type="hidden" name="cid" value="<?php echo htmlspecialchars($vehicle['cid']); ?>">
                                            <input type="hidden" name="rent" value="<?php echo htmlspecialchars($vehicle['rent']); ?>">
                                            <input type="hidden" name="booked" value="<?php echo htmlspecialchars($vehicle['booked']); ?>">
                                            <input type="hidden" name="origin" value="<?php echo htmlspecialchars($origin ?? ''); ?>">
                                            <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination ?? ''); ?>">
                                            <input type="hidden" name="start_datetime" value="<?php echo htmlspecialchars($start_datetime ?? ''); ?>">
                                            <input type="hidden" name="end_datetime" value="<?php echo htmlspecialchars($end_datetime ?? ''); ?>">
                                            <button type="submit">Book</button>
                                        </form>
                                    <?php else : ?>
                                        <button disabled>Unavailable</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="10">No vehicles found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
