<?php
include('config.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit;
}

// Handle roll number assignment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check password for confirmation
    $password = trim($_POST['admin_password']); // Trim any spaces
    
    // Correct password (Make sure to change this to a more secure way)
    $correct_password = 'adminpassword';  // Replace this with your actual admin password
    
    if ($password == $correct_password) {
        // Check if registration is closed
        $status_query = "SELECT setting_value FROM system_settings WHERE setting_name = 'registration_status'";
        $status_result = mysqli_query($conn, $status_query);
        $status = mysqli_fetch_assoc($status_result);
        
        if ($status['setting_value'] != 'closed') {
            echo "Registration is still open. Cannot assign roll numbers.";
            exit;
        }

        // Fetch all students who do not have a roll number assigned, ordered by their ID
        $result = mysqli_query($conn, "SELECT * FROM students ORDER BY id ASC"); // Fetching all students, regardless of whether roll number is assigned
        $students = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        // If there are no students to assign roll numbers, exit early
        if (count($students) == 0) {
            echo "No students to assign roll numbers.";
            exit;
        }

        // Randomize the students' list to ensure roll numbers are assigned randomly
        shuffle($students);
        
        // Assign roll numbers starting from 4001
        $roll_number = 4001;

        // Loop through the students and assign roll numbers
        foreach ($students as $student) {
            // Forcefully update roll number, regardless of whether it was previously assigned
            $query = "UPDATE students SET roll_number = '$roll_number' WHERE id = " . $student['id'];
            if (mysqli_query($conn, $query)) {
                $roll_number++;
            } else {
                echo "Error assigning roll number to student " . $student['id'];
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Roll Numbers</title>
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #6610f2;
        }
        label {
            font-size: 14px;
            margin-bottom: 5px;
        }
        input[type="password"], button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #6610f2;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #5208c2;
        }
        .error {
            color: red;
            text-align: center;
        }
        .success {
            color: green;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="dashboard-header">
        <h1>Assign Roll Numbers</h1>
        <div class="header-buttons">
            <a href="admin_dashboard.php" class="header-button">Go to Dashboard</a>
            <a href="logout.php" class="header-button">Logout</a>
        </div>
    </div>
    <div class="container">
        <h2>Assign Roll Numbers</h2>

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($password) && $password != $correct_password): ?>
            <div class="error">Incorrect password. Please try again.</div>
        <?php endif; ?>

        <form action="assign_roll_numbers.php" method="POST">
            <label for="admin_password">Enter Admin Password</label>
            <input type="password" name="admin_password" required placeholder="Enter Admin Password">

            <button type="submit">Assign Roll Numbers</button>
        </form>

        <?php
        // Display the success message if roll numbers have been assigned
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $password == $correct_password) {
            echo "<div class='success'>Roll numbers have been assigned forcefully!</div>";
        }
        ?>
    </div>
</body>
</html>