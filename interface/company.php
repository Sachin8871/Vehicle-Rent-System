<?php
session_start();
include("../connect.php");
$error = "";

// Logout functionality
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    
    // Fetch company details
    $sql = "SELECT * FROM company WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $cid = $row['cid'];
    $name = $row['name'];
    $location = $row['location'];
    $contact_no = $row['contact_no'];
    
    // Fetch vehicles for the company
    $sql2 = "SELECT * FROM Company c LEFT JOIN Vehicle v ON c.cid = v.cid WHERE c.email = :email";
    $stmt = $conn->prepare($sql2);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle delete request
if (isset($_GET['delete_vid']) && isset($_GET['delete_cid'])) {
    $delete_vid = $_GET['delete_vid'];
    $delete_cid = $_GET['delete_cid'];

    // Prepare and execute delete statement
    $deleteSql = "DELETE FROM Vehicle WHERE vid = :vid AND cid = :cid";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bindParam(':vid', $delete_vid);
    $deleteStmt->bindParam(':cid', $delete_cid);
    $deleteStmt->execute();

    // Redirect back to the same page to avoid resubmission
    header("Location: company.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/interface_company_style.css">
    <title>Company Dashboard</title>
</head>
<body>
    <div class="main-content">
    <div class="Info">
        <div class="cname">
            <p><?php echo htmlspecialchars($name); ?></p>
        </div>
        <!-- <div class="cemail"><p><?php echo htmlspecialchars($email); ?></p></div> -->
        <div class="logout"><a href="company.php?logout=true">Logout</a></div>
    </div>

        <div class="table">
            <div class="table-name">
                <h2>Vehicles</h2>
                <?php $_SESSION['cid'] = $cid; ?>
                <button onclick="window.location.href='add_vehicle.php'">Add Vehicle</button>
            </div>
            <table class="vehicle-table">
                <thead>
                    <tr>
                        <th>Vehicle ID</th>
                        <th>Type</th>
                        <th>No. of Wheels</th>
                        <th>No. of Seats</th>
                        <th>Fuel</th>
                        <th>Mileage</th>
                        <th>Rent</th>
                        <th>No. of Vehicles</th>
                        <th>Booked</th>
                        <th>Image</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($vehicles) > 0) : ?>
                        <?php foreach ($vehicles as $vehicle) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($vehicle['vid']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['type']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['no_of_wheel']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['no_of_seat']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['fuel']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['mileage']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['rent']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['no_of_vehicle']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['booked']); ?></td>
                                <td>
                                    <?php if (!empty($vehicle['img'])) : ?>
                                        <img src="<?php echo htmlspecialchars($vehicle['img']); ?>" alt="Vehicle Image" width="50" height="50">
                                    <?php else : ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button onclick="window.location.href='edit.php?vid=<?php echo htmlspecialchars($vehicle['vid']); ?>&cid=<?php echo htmlspecialchars($vehicle['cid']); ?>'">Edit</button>
                                </td>
                                <td>
                                    <button onclick="if(confirm('Are you sure you want to delete this vehicle?')) { window.location.href='company.php?delete_vid=<?php echo htmlspecialchars($vehicle['vid']); ?>&delete_cid=<?php echo htmlspecialchars($vehicle['cid']); ?>'; }">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="12">No vehicles found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
