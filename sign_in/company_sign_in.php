<?php
    function isValidPassword($password) {
        return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
    }

    include("../connect.php");
    $error = "";
    $message = "";

    try {
    
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            
            $cid = $_POST["cid"];
            $name = $_POST["name"];
            $email = $_POST["email"];
            $location = $_POST["location"];
            $contact_no = $_POST["contact_no"];
            $password = $_POST["password"];
            $category = "company";

            $sql = "SELECT * FROM Log_in WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $message = "Accout Already exist !";
            }
            else {

                if (!isValidPassword($password)){
                    $error = "Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character";
                }
                else {
                    $hashpassword = password_hash($password, PASSWORD_DEFAULT);

                    $sql1 = "INSERT INTO Log_in(email,password,category) VALUES (:email, :password, :category)";
                    
                    // Prepare and bind parameters
                    $stmt = $conn->prepare($sql1);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':password', $hashpassword, PDO::PARAM_STR);
                    $stmt->bindParam(':category', $category, PDO::PARAM_STR);
        
                    $stmt->execute();
        
                    $sql2 = "INSERT INTO Company (cid, name, email, location, contact_no) VALUES (:cid, :name, :email, :location, :contact_no)";
        
                    // Prepare and bind parameters
                    $stmt = $conn->prepare($sql2);
                    $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':location', $location, PDO::PARAM_STR);
                    $stmt->bindParam(':contact_no', $contact_no, PDO::PARAM_STR);
                    
                    $stmt->execute();
                    
                    $message = "Company Added Successfully";
                }
            }

        }
    }
    catch(PDOException $e) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            
            $email = $_POST["email"];            
            $sql = "DELETE FROM Log_in WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $errro = $e->getMessage();
        }
    }
    // Close the connection
    $conn = null;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Sign In</title>
    <link rel="stylesheet" href="../css/sign_in_style.css">
</head>
<body>
    <div>
        <div class="navbar">
            <a href="user_sign_in.php">User</a>
            <a href="company_sign_in.php">Company</a>
        </div>
        <div class="login-container">
            <h2>Company</h2>
            <form action="company_sign_in.php" method="POST">
                <input type="text" name="cid" placeholder="Company ID" required>
                <input type="text" name="name" placeholder="Name" required>
                <input type="text" name="email" placeholder="Email ID" required>
                <input type="text" name="location" placeholder="Location" required>
                <input type="text" name="contact_no" placeholder="Contact No." required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Submit</button>
                <button type="reset">Reset</button>
            </form>

            <?php
                if(!empty($error)) {
                    echo "<p style='color: red;'>$error</p>";
                }
                else {
                    echo "<p style='color: green;'>$message</p>";
                }
            ?>

        </div>
    </div>
</body>
</html>