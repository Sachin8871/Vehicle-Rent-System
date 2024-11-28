<?php
    function isValidPassword($password) {
        return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
    }

    include("../connect.php");
    $error = "";
    $message = "";

    try {
    
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            
            $user_id = $_POST["user_id"];
            $f_name = $_POST["f_name"];
            $l_name = $_POST["l_name"];
            $gender = $_POST["gender"];
            $age = $_POST["age"];
            $email = $_POST["email"];
            $contact_no = $_POST["contact_no"];
            $license = $_POST["license"];
            $password = $_POST["password"];
            $category = "user";

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
        
                    $sql2 = "INSERT INTO User (user_id, f_name, l_name, gender, age, email, contact_no, license) VALUES (:user_id, :f_name, :l_name, :gender, :age, :email, :contact_no, :license)";
        
                    // Prepare and bind parameters
                    $stmt = $conn->prepare($sql2);
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
                    $stmt->bindParam(':f_name', $f_name, PDO::PARAM_STR);
                    $stmt->bindParam(':l_name', $l_name, PDO::PARAM_STR);
                    $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
                    $stmt->bindParam(':age', $age, PDO::PARAM_INT);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':contact_no', $contact_no, PDO::PARAM_STR);
                    $stmt->bindParam(':license', $lisence, PDO::PARAM_STR);
                    
                    $stmt->execute();
                    
                    $message = "Account Created Successfully";
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

            $error = $e->getMessage();
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
    <title>User Sign In</title>
    <link rel="stylesheet" href="../css/sign_in_style.css">
</head>
<body>
    <div>
        <div class="navbar">
            <a href="user_sign_in.php">User</a>
            <a href="company_sign_in.php">Company</a>
        </div>
        <div class="login-container">
            <h2>User</h2>
            <form action="user_sign_in.php" method="POST">
                <input type="text" name="user_id" placeholder="User ID" required>
                <div class="row">
                    <input type="text" name="f_name" placeholder="First Name" required>
                    <input type="text" name="l_name" placeholder="Last Name" required>
                </div>
                <div class="row">
                    <select name="gender">
                        <option value="">Gender</option>
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                        <option value="T">Transgender</option>
                    </select>
                    <input type="number" name="age" placeholder="Age" required>
                </div>
                <div class="row">
                    <input type="text" name="email" placeholder="Email ID" required>
                    <input type="text" name="contact_no" placeholder="Contact No." required>
                </div>
                <input type="text" name="license" placeholder="License Number" required>
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