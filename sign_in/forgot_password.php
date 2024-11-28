<?php

    function isValidPassword($password) {
        return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
    }
    
    include("../connect.php");
    $error = "";
    $message = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["email"];
        $old_password = $_POST["old_password"];
        $new_password = $_POST["new_password"];
        $c_new_password = $_POST["c_new_password"];

        $sql = "SELECT email, password FROM Log_in WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $error = "Account does not exit !";
        }
        else {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row['password'] != $old_password) {
                $error = "Old Password incorrect !";
            }
            else {
                if (!isValidPassword($new_password)) {
                    $error = "New password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character.";
                }
                else {
                    if ($c_new_password != $new_password) {
                        $error = "New Password does not match !";
                    }
                    else {
                        $sql = "UPDATE Log_in SET password = :new_password WHERE email = :email";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                        $stmt->bindParam(':new_password', $new_password, PDO::PARAM_STR);
                        $stmt->execute();
    
                        $message = "Password changed Successfully";
                    }
                }
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../css/index_style.css">
</head>
<body>
    <div class="login-container">
        <h2>Change Password</h2>
        <form action="forgot_password.php" method="POST">
            <input type="text" name="email" placeholder="Email"> 
            <input type="password" name="old_password" placeholder="Old Password"> 
            <input type="password" name="new_password" placeholder="New Password"> 
            <input type="password" name="c_new_password" placeholder="Confirm New Password">
            <button type="submit">Submit</button>
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
</body>
</html>