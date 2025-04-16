<?php
include('config.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/style.css">
    
    <style>
        /* General Styles */
        body {
            background-color: #f4f7f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h2 {
            font-size: 2rem;
            color: #007bff;
            margin: 20px 0;
            text-align: center;
        }

        /* Dashboard Container */
        .dashboard-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Dashboard Header */
        .dashboard-header {
            text-align: center;
            margin-bottom: 40px;
        }

        /* Dashboard Links */
        .dashboard-links {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 40px;
            max-width: 800px;
            margin: 0 auto;
            padding: 10px;
        }

        .dashboard-link {
            display: flex;
            justify-content: center;
            text-align: center;
            align-items: center;
            padding: 12px 20px;
            background-color: #e0f7fa;
            border: 1px solid #ddd;
            color: #000;
            text-decoration: none;
            font-size: 18px;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.3s;
            height: 100px;
        }

        .dashboard-links .dashboard-link:nth-last-child(1):nth-child(odd):not(:nth-child(even)) {
            grid-column: span 2;
        }

        .dashboard-link:hover {
            background-color: #0056b3;
            color: #fff;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

        <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <div class="header-buttons">
            <form action="logout.php" method="POST">
            <button type="submit" class="header-button">Logout</button>
        </form>
        </div>
    </div>

        <div class="dashboard-links">
            <a href="admin_settings.php" class="dashboard-link">Manage System Settings</a>
            <a href="admin_students.php" class="dashboard-link">Manage Students</a>
            <a href="assign_roll_numbers.php" class="dashboard-link">Assign Roll Numbers</a>
            <a href="manage_results.php" class="dashboard-link">Manage Results</a>
            <a href="print_students_data.php" class="dashboard-link">Print Students List</a>
        </div>

    </div>
<p class="center" style="margin-top:50px; font-size:14px;">Made by <a style="text-decoration:none;" href="http://instagram.com/siddharthvishwamitra" target="_blank">Siddharth Vishwamitra</a></p>
</body>
</html>

