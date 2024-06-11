<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'database.php';

$username = $_SESSION['username'];

// Fetch user email
$sql = "SELECT email FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($email);
$stmt->fetch();
$stmt->close();

$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reason = $_POST['reason'];

    $sql = "INSERT INTO leave_applications (username, date, time, reason) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $date, $time, $reason);

    if ($stmt->execute()) {
        $success_message = "Leave application submitted successfully.";
    } else {
        $success_message = "Failed to submit leave application.";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .success-message {
            color: green;
            font-size: 14px;
        }
        textarea {
            height: 50px;
            width: 300px;
            resize: none; /* Prevent resizing */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="user-details">
            <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        </div>
        <div class="leave-application">
            <h2>Apply for Leave</h2>
            <form action="home.php" method="POST">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required><br>
                <label for="time">Time:</label>
                <input type="time" id="time" name="time" required><br>
                <label for="reason">Reason:</label>
                <textarea id="reason" name="reason" required></textarea><br>
                <button type="submit">Submit</button>
            </form>
            <?php
            if (!empty($success_message)) {
                echo '<div class="success-message">' . htmlspecialchars($success_message) . '</div>';
            }
            ?>
        </div>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</body>
</html>
