<?php
session_start();

include("connect.php");
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM Log_in WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        $error = "User or Company does not exist!";
    } else {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Use password_verify to check the entered password against the stored hash
        if (!password_verify($password, $row['password'])) {
            $error = "Wrong Password!";
        } else {
            $_SESSION['email'] = $email;
            if ($row['category'] === 'user'){
                header("Location: interface/user.php");
                exit();
            } else {
                header("Location: interface/company.php");
                exit();
            }
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $error = "";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="css/index_style.css">
</head>
<body>

<div class="login-container">
    <h2>Login</h2>
    <form action="" method="post">
        <input type="text" name="email" placeholder="Email Id" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Log In</button>
    </form>
    <br>
    <a class="link1" href="sign_in/user_sign_in.php">Create Account</a>
    <a class="link2" href="sign_in/forgot_password.php">Forgot Password</a>

    <?php
        if (!empty($error)) {
            echo "<p style='color: red;'>$error</p>";
        }
    ?>
</div>

</body>
</html>
