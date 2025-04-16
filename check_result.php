<?php
include('staff/config.php');
session_start();

// Fetch settings from system_settings
$query = "SELECT setting_value FROM system_settings WHERE setting_name = 'result_status'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$result_status = $row['setting_value'] ?? 'enabled'; // Default to enabled

// Initialize the result message
$result_message = "";
$show_result_card = false;

// If results are disabled, show a message in HTML
if ($result_status == 'disabled') {
    $disabled_message = "Results are currently not available.";
} else {
    $disabled_message = "";
}

// CAPTCHA generation
if (!isset($_SESSION['captcha_text'])) {
    $_SESSION['captcha_text'] = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 6);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reg_no = mysqli_real_escape_string($conn, $_POST['reg_no']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $captcha_input = $_POST['captcha'];

    // Validate CAPTCHA
    if ($captcha_input !== $_SESSION['captcha_text']) {
        $result_message = "<p style='color: red;'>Incorrect CAPTCHA. Please try again.</p>";
    } else {
        // Query to check student and fetch result
        $sql = "SELECT * FROM students WHERE reg_no = '$reg_no' AND full_name = '$full_name' AND dob = '$dob'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $student = mysqli_fetch_assoc($result);
            if (!empty($student['obtained_marks'])) {
                $obtained_marks = $student['obtained_marks'];
                $result_status = $student['result_status'];

                // Display result
                $result_message = "
                <h2>Result for " . htmlspecialchars($student['full_name']) . "</h2>
                <p><strong>Reg No:</strong> " . htmlspecialchars($student['reg_no']) . "</p>
                <p><strong>Roll No:</strong> " . htmlspecialchars($student['roll_number']) . "</p>
                <p><strong>Obtained Marks:</strong> " . htmlspecialchars($obtained_marks) . " / 100</p>
                <p><strong>Result Status:</strong> " . htmlspecialchars($result_status) . "</p>";

                $show_result_card = true;
            } else {
                $result_message = "<p style='color: red;'>Result not available yet.</p>";
            }
        } else {
            $result_message = "<p style='color: red;'>No student found with the provided information.</p>";
        }
    }
    $_SESSION['captcha_text'] = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 6); // Refresh CAPTCHA
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Results</title>
    <link rel="stylesheet" href="css/font.css">
    <style>
       body {background-color: #f4f4f4; padding: 20px; color: #333; text-align: center; margin: 0;}
        .container { max-width: 400px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 4px rgba(0, 0, 0, 0.1); text-align: center;}
        h2 { color: #6610f2;}
        input, button { width: 100%; padding: 10px; margin: 10px 0; border-radius: 4px; border: 1px solid #ccc; }
        button { background: #6610f2; color: white; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .captcha { font-size: 24px; font-weight: bold; letter-spacing: 3px; background: #ddd; padding: 10px; display: inline-block; margin-top: 5px; text-align: center;}
        .result-container { margin-top: 20px; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 4px rgba(0, 0, 0, 0.1); }
        .result { text-align: center; }
        .result h2 { color: #28a745; }
        .disabled-message { color: red; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h2>Check Results</h2>
    
    <?php if ($disabled_message) echo "<p class='disabled-message'>$disabled_message</p>"; ?>

    <!-- Display form if results are enabled -->
    <?php if ($result_status != 'disabled'): ?>
    <form method="POST" action="check_result.php">
        
        <input type="text" name="reg_no" pattern="^\d{4}$" placeholder="Registration Number" title="Enter 4 digits" required>
        
        <input type="text" name="full_name" pattern="[A-Za-z ]{3,50}" placeholder="Full Name" maxlength="50" title="Only English letters and spaces, between 3 to 50 characters" required>

        <input type="date" value="2002-02-01" min="1990-02-01" max="2021-02-01" name="dob" required>

        <div class="captcha"><?php echo $_SESSION['captcha_text']; ?></div>
        <input type="text" name="captcha" placeholder="Enter CAPTCHA" required>

        <button type="submit">Check Result</button>
    </form>
</div>
    <?php endif; ?>

    <!-- Display the result card only if the result is found -->
    <?php if ($show_result_card): ?>
        <div class="result-container">
            <div class="result">
                <?php echo $result_message; ?>
            </div>
        </div>
    <?php else: ?>
        <p style="text-align: center;"><?php echo $result_message; ?></p>
    <?php endif; ?>


</body>
</html>