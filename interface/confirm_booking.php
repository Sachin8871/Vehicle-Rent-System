<?php
session_start();
include("../connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Retrieve POST data
    $uid = $_POST['uid'];
    $vid = $_POST['vid'];
    $rent = $_POST['rent'];
    $origin = $_POST['origin'];
    $destination = $_POST['destination'];
    $start_date = $_POST['start_date'];
    $start_time = $_POST['start_time'];
    $end_date = $_POST['end_date'];
    $end_time = $_POST['end_time'];
    $status = 'booked';
    
    // Calculate start and end datetime
    $start_datetime = $start_date . ' ' . $start_time;
    $end_datetime = $end_date . ' ' . $end_time;

    // Calculate total cost based on rent and duration
    $start = new DateTime($start_datetime);
    $end = new DateTime($end_datetime);
    $duration = $end->diff($start)->h + ($end->diff($start)->days * 24); // Duration in hours
    $total_cost = $duration * $rent;

    try {
        // Prepare SQL for inserting booking data
        $sql1 = "INSERT INTO Booking (destination, start_time, end_time, total_cost, user_id, vid, origin, status)
                 VALUES (:destination, :start_time, :end_time, :total_cost, :user_id, :vid, :origin, :status)";
        
        $stmt = $conn->prepare($sql1);
        $stmt->bindParam(':user_id', $uid, PDO::PARAM_STR);
        $stmt->bindParam(':vid', $vid, PDO::PARAM_STR);
        $stmt->bindParam(':origin', $origin, PDO::PARAM_STR);
        $stmt->bindParam(':destination', $destination, PDO::PARAM_STR);
        $stmt->bindParam(':start_time', $start_datetime, PDO::PARAM_STR);
        $stmt->bindParam(':end_time', $end_datetime, PDO::PARAM_STR);
        $stmt->bindParam(':total_cost', $total_cost, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);

        $stmt->execute();
        
        // Get the bid of the last inserted booking
        $bid = $conn->lastInsertId();

        // Store bid and total_cost in session variables for further use
        $_SESSION['bid'] = $bid;
        $_SESSION['total_cost'] = $total_cost;

        echo "Booking confirmed!<br>";
        echo "Booking ID: " . htmlspecialchars($bid) . "<br>";
        echo "Total Cost: " . htmlspecialchars($total_cost) . "<br>";
        
        $sql2 = "INSERT INTO Makes_booking (user_id, bid) VALUES (:user_id, :bid)";
        
        $stmt = $conn->prepare($sql2);
        $stmt->bindParam(':user_id', $uid, PDO::PARAM_STR);
        $stmt->bindParam(':bid', $bid, PDO::PARAM_STR);
        $stmt->execute();

        header("Location: payment.php");
        exit();

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
