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

$success_message = "";
$error_message = "";
$show_employees = false;
$add_employee = false;
$employees = [];

// Handle the display of employee details
if (isset($_POST['show_employees'])) {
    $show_employees = true;

    // Fetch employee details
    $sql = "SELECT id, name, phone_number, address, skills, achievements FROM employees";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }
    }
}

// Handle the addition of a new employee form display
if (isset($_POST['show_add_employee_form'])) {
    $add_employee = true;
}

// Handle addition of a new employee
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_employee'])) {
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $skills = $_POST['skills'];
    $achievements = $_POST['achievements'];

    $sql = "INSERT INTO employees (name, phone_number, address, skills, achievements) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $phone_number, $address, $skills, $achievements);

    if ($stmt->execute()) {
        $success_message = "Employee details added successfully.";
        $show_employees = true; // Show employee details after adding

        // Fetch employee details again
        $sql = "SELECT id, name, phone_number, address, skills, achievements FROM employees";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $employees[] = $row;
            }
        }
    } else {
        $error_message = "Failed to add employee details.";
    }

    $stmt->close();
}

// Handle removal of an employee
if (isset($_POST['remove_employee_id'])) {
    $employee_id = $_POST['remove_employee_id'];
    $sql = "DELETE FROM employees WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employee_id);

    if ($stmt->execute()) {
        $success_message = "Employee removed successfully.";
        $show_employees = true; // Show employee details after removing

        // Fetch employee details again
        $sql = "SELECT id, name, phone_number, address, skills, achievements FROM employees";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $employees[] = $row;
            }
        }
    } else {
        $error_message = "Failed to remove employee.";
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
    <title>Admin Dashboard</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .button-group {
            display: flex;
            gap: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="user-details">
            <h1>Welcome, Admin <?php echo htmlspecialchars($username); ?>!</h1>
        </div>
        <form action="home.php" method="POST" class="button-group">
            <button type="submit" name="show_employees">Show Employee Details</button>
            <button type="submit" name="show_add_employee_form">Add Employee</button>
        </form>

        <?php
        if (!empty($success_message)) {
            echo '<div class="success-message">' . htmlspecialchars($success_message) . '</div>';
        }
        if (!empty($error_message)) {
            echo '<div class="error-message">' . htmlspecialchars($error_message) . '</div>';
        }
        ?>

        <?php if ($show_employees): ?>
            <div class="employee-details">
                <h2>Employee Details</h2>
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Skills</th>
                        <th>Achievements</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($employee['name']); ?></td>
                            <td><?php echo htmlspecialchars($employee['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($employee['address']); ?></td>
                            <td><?php echo htmlspecialchars($employee['skills']); ?></td>
                            <td><?php echo htmlspecialchars($employee['achievements']); ?></td>
                            <td>
                                <form action="home.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="remove_employee_id" value="<?php echo $employee['id']; ?>">
                                    <button type="submit">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endif; ?>

        <?php if ($add_employee): ?>
            <div class="employee-form">
                <h2>Add Employee Details</h2>
                <form action="home.php" method="POST">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required><br>
                    <label for="phone_number">Phone Number:</label>
                    <input type="text" id="phone_number" name="phone_number" required><br>
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" required><br>
                    <label for="skills">Skills:</label>
                    <input type="text" id="skills" name="skills" required><br>
                    <label for="achievements">Achievements:</label>
                    <input type="text" id="achievements" name="achievements" required><br>
                    <input type="hidden" name="add_employee" value="1">
                    <button type="submit">Submit</button>
                </form>
            </div>
        <?php endif; ?>

        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</body>
</html>
