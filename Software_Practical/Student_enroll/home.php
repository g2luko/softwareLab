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

$courses = ["Python", "C", "C++", "Java", "Html & CSS"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course = $_POST['course'];
    $action = $_POST['action'];

    if ($action == 'enroll') {
        $sql = "SELECT * FROM student_courses WHERE username = ? AND course = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $course);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 0) {
            $stmt->close();

            $sql = "INSERT INTO student_courses (username, course) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $course);

            if ($stmt->execute()) {
                $success_message = "Successfully enrolled in $course.";
            } else {
                $error_message = "Failed to enroll in $course.";
            }
            $stmt->close();
        } else {
            $error_message = "You are already enrolled in $course.";
            $stmt->close();
        }
    } else if ($action == 'unenroll') {
        $sql = "DELETE FROM student_courses WHERE username = ? AND course = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $course);

        if ($stmt->execute()) {
            $success_message = "Successfully unenrolled from $course.";
        } else {
            $error_message = "Failed to unenroll from $course.";
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Enrollment System</title>
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
        .course-button {
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="user-details">
            <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        </div>
        <div class="course-enrollment">
            <h2>Course Enrollment</h2>
            <form action="home.php" method="POST">
                <?php foreach ($courses as $course): ?>
                    <?php
                    // Check if the user is enrolled in the course
                    $conn = new mysqli('localhost', 'root', '', 'user_auth');
                    $sql = "SELECT * FROM student_courses WHERE username = ? AND course = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $username, $course);
                    $stmt->execute();
                    $stmt->store_result();
                    $is_enrolled = $stmt->num_rows > 0;
                    $stmt->close();
                    $conn->close();
                    ?>
                    <button type="submit" name="course" value="<?php echo $course; ?>" class="course-button">
                        <?php echo htmlspecialchars($course); ?>: 
                        <?php if ($is_enrolled): ?>
                            <input type="hidden" name="action" value="unenroll">Unenroll
                        <?php else: ?>
                            <input type="hidden" name="action" value="enroll">Enroll
                        <?php endif; ?>
                    </button>
                <?php endforeach; ?>
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
