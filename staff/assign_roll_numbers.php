<?php
include('config.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Fetch settings from system_settings
$query = "SELECT setting_value FROM system_settings WHERE setting_name = 'gen_roll_number'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$gen_roll_number = $row['setting_value'] ?? 'enabled';

if ($gen_roll_number == 'disabled') {
    $disabled_message = "You can't assign roll numbers!";
    $show_form = false;
} else {
    $disabled_message = "";
    $show_form = true;
}

$error_message = "";
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = trim($_POST['admin_password']);
    $correct_password = 'adminpassword';

    if ($password == $correct_password) {
        $status_query = "SELECT setting_value FROM system_settings WHERE setting_name = 'registration_status'";
        $status_result = mysqli_query($conn, $status_query);
        $status = mysqli_fetch_assoc($status_result);

        if ($status['setting_value'] != 'closed') {
            $error_message = "Registration is still open. Cannot assign roll numbers.";
        } else {
            $result = mysqli_query($conn, "SELECT * FROM students ORDER BY id ASC");
            $students = mysqli_fetch_all($result, MYSQLI_ASSOC);

            if (count($students) == 0) {
                $error_message = "No students to assign roll numbers.";
            } else {
                shuffle($students);
                $roll_number = 4001;

                foreach ($students as $student) {
                    $query = "UPDATE students SET roll_number = '$roll_number' WHERE id = " . $student['id'];
                    if (mysqli_query($conn, $query)) {
                        $roll_number++;
                    } else {
                        $error_message = "Error assigning roll number to student " . $student['id'];
                        break;
                    }
                }

                if (!$error_message) {
                    $success_message = "Roll numbers have been assigned forcefully!";
                }
            }
        }
    } else {
        $error_message = "Incorrect password. Please try again.";
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
            margin-top: 10px;
            font-size: 14px;
        }
        .success {
            color: green;
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
        }
        .disabled-message {
            color: red;
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

    <?php if ($disabled_message) echo "<p class='disabled-message'>$disabled_message</p>"; ?>

    <?php if ($show_form) { ?>

        <form action="assign_roll_numbers.php" method="POST">
            <label for="admin_password">Enter Admin Password</label>
            <input type="password" name="admin_password" required placeholder="Enter Admin Password">
            <button type="submit">Assign Roll Numbers</button>
        </form>
        
        <?php if (!empty($success_message)): ?>
            <div class="success"><?= $success_message ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="error"><?= $error_message ?></div>
        <?php endif; ?>
        
    <?php } ?>
</div>
</body>
</html>