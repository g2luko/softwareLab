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
    $departure_location = $_POST['departure_location'];
    $arrival_location = $_POST['arrival_location'];
    $departure_date = $_POST['departure_date'];
    $departure_time = $_POST['departure_time'];

    $sql = "INSERT INTO airplane_reservations (username, name, departure_location, arrival_location, departure_date, departure_time) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $username, $name, $departure_location, $arrival_location, $departure_date, $departure_time);

    if ($stmt->execute()) {
        $success_message = "Airplane reservation submitted successfully.";
    } else {
        $success_message = "Failed to submit airplane reservation.";
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
    <title>Airplane Management System</title>
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
        <div class="airplane-reservation">
            <h2>Airplane Reservation</h2>
            <form action="home.php" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br>
                <label for="departure_location">Departure Location:</label>
                <input type="text" id="departure_location" name="departure_location" required><br>
                <label for="arrival_location">Arrival Location:</label>
                <input type="text" id="arrival_location" name="arrival_location" required><br>
                <label for="departure_date">Date of Departure:</label>
                <input type="date" id="departure_date" name="departure_date" required><br>
                <label for="departure_time">Time of Departure:</label>
                <input type="time" id="departure_time" name="departure_time" required><br>
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
