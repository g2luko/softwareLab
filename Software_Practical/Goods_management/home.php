<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'database.php';

$username = $_SESSION['username'];

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['send_date'];
    $action = $_POST['action'];

    if ($action == 'send_goods') {
        $sql = "SELECT * FROM goods_schedule WHERE send_date = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            $stmt->close();

            $sql = "INSERT INTO goods_schedule (username, send_date) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $date);

            if ($stmt->execute()) {
                $success_message = "Goods will be sent on " . htmlspecialchars($date) . ".";
            } else {
                $error_message = "Failed to schedule goods sending.";
            }
            $stmt->close();
        } else {
            $error_message = "Date already allocated, choose another date.";
            $stmt->close();
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goods Sending System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .success-message {
            color: green;
            font-size: 14px;
        }
        .error-message {
            color: red;
            font-size: 14px;
        }
        .button-spacing {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="user-details">
            <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        </div>
        <div class="goods-schedule">
            <h2>Send Goods</h2>
            <form action="home.php" method="POST">
                <label for="send_date">Select Date:</label>
                <input type="date" id="send_date" name="send_date" required><br>
                <button type="submit" name="action" value="send_goods" class="button-spacing">Send Goods</button>
            </form>
            <?php
            if (!empty($success_message)) {
                echo '<div class="success-message">' . htmlspecialchars($success_message) . '</div>';
            }
            if (!empty($error_message)) {
                echo '<div class="error-message">' . htmlspecialchars($error_message) . '</div>';
            }
            ?>
        </div>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</body>
</html>
