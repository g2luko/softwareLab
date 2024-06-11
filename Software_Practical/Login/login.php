<?php
include 'database.php';
session_start();

$error_message = ""; // Variable to hold the error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $username, $db_password);
    $stmt->fetch();

    // Compare plain text passwords directly
    if ($stmt->num_rows == 1 && $password === $db_password) {
        $_SESSION['username'] = $username;
        $_SESSION['id'] = $id;
        header("Location: home.php");
        exit(); // Ensure script termination after redirect
    } else {
        $error_message = "Invalid username or password."; // Set error message
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
    <style>
        .error-message {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <form action="login.php" method="POST">
                <h2>Login</h2>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <?php
                if (!empty($error_message)) {
                    echo '<div class="error-message">' . $error_message . '</div>';
                }
                ?>
                <button type="submit">Login</button>
                <p>Don't have an account? <a href="signup.php">Sign up</a></p>
            </form>
        </div>
    </div>
</body>
</html>
