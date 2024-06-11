<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'database.php';

$username = $_SESSION['username'];

$sql = "SELECT email FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($email);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="user-details">
            <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
            <p>Email: <?php echo htmlspecialchars($email); ?></p>
        </div>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</body>
</html>
