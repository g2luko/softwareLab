<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'database.php';

$username = $_SESSION['username'];

$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $pickup_location = $_POST['pickup_location'];
    $drop_location = $_POST['drop_location'];
    $pickup_date = $_POST['pickup_date'];
    $pickup_time = $_POST['pickup_time'];

    $sql = "INSERT INTO bus_reservations (username, name, pickup_location, drop_location, pickup_date, pickup_time) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $username, $name, $pickup_location, $drop_location, $pickup_date, $pickup_time);

    if ($stmt->execute()) {
        $success_message = "Bus reservation submitted successfully.";
    } else {
        $success_message = "Failed to submit bus reservation.";
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
    <title>Bus Management System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .success-message {
            color: green;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="user-details">
            <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        </div>
        <div class="bus-reservation">
            <h2>Bus Reservation</h2>
            <form action="home.php" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br>
                <label for="pickup_location">Pickup Location:</label>
                <input type="text" id="pickup_location" name="pickup_location" required><br>
                <label for="drop_location">Drop Location:</label>
                <input type="text" id="drop_location" name="drop_location" required><br>
                <label for="pickup_date">Date of Pickup:</label>
                <input type="date" id="pickup_date" name="pickup_date" required><br>
                <label for="pickup_time">Time of Pickup:</label>
                <input type="time" id="pickup_time" name="pickup_time" required><br>
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
