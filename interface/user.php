<?php
session_start();
include("../connect.php");

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    
    // Fetch user details
    $sql = "SELECT * FROM user WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $uid = $row['user_id'];
        $f_name = $row['f_name'];
        $l_name = $row['l_name'];
        $license = $row['license'];
        $contact_no = $row['contact_no'];

        // Fetch user's current bookings
        $sql = "SELECT * FROM Makes_booking m 
                LEFT JOIN Booking b ON m.bid = b.bid 
                LEFT JOIN Vehicle v ON b.vid = v.vid 
                WHERE m.user_id = :uid AND b.status = 'booked'";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':uid', $uid, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch all vehicle data associated with the booking
        $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (isset($_GET['cancel_bid'])) {
    $bid = $_GET['cancel_bid'];
    
    $sql1 = "SELECT * FROM Booking WHERE bid = :bid";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bindParam(':bid', $bid);
    $stmt1->execute();
    
    $row = $stmt1->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $uid = $row['user_id'];
        $cid = $row['cid'];
        $vid = $row['vid'];
        
        // Update Booking status to 'cancel'
        $sql2 = "UPDATE Booking SET status = 'cancel' WHERE bid = :bid";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bindParam(':bid', $bid);
        $stmt2->execute();
        
        // Update Vehicle booked count
        $sql3 = "SELECT booked FROM Vehicle WHERE vid = :vid AND cid = :cid";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bindParam(':vid', $vid);
        $stmt3->bindParam(':cid', $cid);
        $stmt3->execute();
        
        $row = $stmt3->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $booked = $row['booked'];
            $booked--;
            
            // Update Vehicle booked count
            $sql4 = "UPDATE Vehicle SET booked = :booked WHERE vid = :vid AND cid = :cid";
            $stmt4 = $conn->prepare($sql4);
            $stmt4->bindParam(':vid', $vid);
            $stmt4->bindParam(':cid', $cid);
            $stmt4->bindParam(':booked', $booked);
            $stmt4->execute();
        }
    }

    // Redirect back to the same page to avoid resubmission
    header("Location: user.php");
    exit();
}

if (isset($_GET['return_bid'])) {
    $bid = $_GET['return_bid'];
    
    $sql1 = "SELECT * FROM Booking WHERE bid = :bid";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bindParam(':bid', $bid);
    $stmt1->execute();
    
    $row = $stmt1->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $uid = $row['user_id'];
        $cid = $row['cid'];
        $vid = $row['vid'];
        
        // Update Booking status to 'complete'
        $sql2 = "UPDATE Booking SET status = 'complete' WHERE bid = :bid";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bindParam(':bid', $bid);
        $stmt2->execute();
        
        // Update Vehicle booked count
        $sql3 = "SELECT booked FROM Vehicle WHERE vid = :vid AND cid = :cid";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bindParam(':vid', $vid);
        $stmt3->bindParam(':cid', $cid);
        $stmt3->execute();
        
        $row = $stmt3->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $booked = $row['booked'];
            $booked--;
            
            // Update Vehicle booked count
            $sql4 = "UPDATE Vehicle SET booked = :booked WHERE vid = :vid AND cid = :cid";
            $stmt4 = $conn->prepare($sql4);
            $stmt4->bindParam(':vid', $vid);
            $stmt4->bindParam(':cid', $cid);
            $stmt4->bindParam(':booked', $booked);
            $stmt4->execute();
        }
    }

    // Redirect back to the same page to avoid resubmission
    header("Location: user.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($f_name); ?></title>
    <link rel="stylesheet" href="../css/interface_user_style.css">
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

        <div class="content-row">
            <div class="table">
                <div class="table-name">
                    <h2>Current Booking</h2>
                </div>
                <table class="vehicle-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Vehicle ID</th>
                            <th>Type</th>
                            <th>No. of Wheels</th>
                            <th>No. of Seats</th>
                            <th>Fuel</th>
                            <th>Mileage</th>
                            <th>Rent</th>
                            <th>Cancel</th>
                            <th>Return</th>
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
                                    <td><?php echo htmlspecialchars($vehicle['vid']); ?></td>
                                    <td><?php echo htmlspecialchars($vehicle['type']); ?></td>
                                    <td><?php echo htmlspecialchars($vehicle['no_of_wheel']); ?></td>
                                    <td><?php echo htmlspecialchars($vehicle['no_of_seat']); ?></td>
                                    <td><?php echo htmlspecialchars($vehicle['fuel']); ?></td>
                                    <td><?php echo htmlspecialchars($vehicle['mileage']); ?></td>
                                    <td><?php echo htmlspecialchars($vehicle['rent']); ?></td>
                                    <td><button onclick="if(confirm('Are you sure you want to cancel this booking?')) { window.location.href='user.php?cancel_bid=<?php echo htmlspecialchars($vehicle['bid']); ?>'; }">Cancel</button></td>
                                    <td><button onclick="if(confirm('Are you sure you want to return this booking?')) { window.location.href='user.php?return_bid=<?php echo htmlspecialchars($vehicle['bid']); ?>'; }">Return</button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="10">No Current Booking.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="U1">
                <div class="login-container">
                    <form action="booking.php" method="POST">
                        <h2>Booking</h2>
                        <input type="hidden" name="f_name" value="<?php echo htmlspecialchars($f_name); ?>">
                        <input type="hidden" name="l_name" value="<?php echo htmlspecialchars($l_name); ?>">
                        <input type="hidden" name="uid" value="<?php echo htmlspecialchars($uid); ?>">
                        <input type="text" name="origin" placeholder="Origin" required>
                        <input type="text" name="destination" placeholder="Destination" required>

                        <label for="start_date">Start Date & Time</label>
                        <input type="datetime-local" name="start_datetime" required>
                        <label for="end_date">End Date & Time</label>
                        <input type="datetime-local" name="end_datetime" required>

                        <input type="text" name="license" placeholder="License Number" required>
                        <button type="submit">Go</button>
                    </form>
                </div>
        </div>
    </div>
</body>
</html>
