<?php
include('staff/config.php');
session_start();

// Fetch settings from system_settings
$query = "SELECT setting_value FROM system_settings WHERE setting_name = 'admit_card_status'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$admit_card_status = $row['setting_value'] ?? 'enabled'; // Default to enabled

// If admit card status is disabled, show a message in HTML
if ($admit_card_status == 'disabled') {
    $disabled_message = "Admit card download is currently not available.";
} else {
    $disabled_message = "";
}

// Generate a random CAPTCHA code if not set
if (!isset($_SESSION['captcha'])) {
    $_SESSION['captcha'] = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6);
}

// Check if form is submitted
$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reg_no = mysqli_real_escape_string($conn, $_POST['reg_no']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $dob = $_POST['dob'];
    $captcha_input = strtoupper(trim($_POST['captcha']));

    // Check CAPTCHA
    if ($captcha_input !== $_SESSION['captcha']) {
        $error = "Invalid CAPTCHA. Please try again.";
        $_SESSION['captcha'] = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6);
    } else {
        // Validate student details
        $query = "SELECT * FROM students WHERE reg_no = '$reg_no' AND full_name = '$full_name' AND dob = '$dob'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            // Student found, fetch details
            $student = mysqli_fetch_assoc($result);
            $_SESSION['student'] = $student; // Store details in session
            header("Location: show_admit_card.php");
            exit;
        } else {
            $error = "No admit card found. Please check your details.";
            $_SESSION['captcha'] = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6); // Regenerate CAPTCHA
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Admit Cards</title>
    <link rel="stylesheet" href="css/font.css">
    <style>
        body {background-color: #f4f4f4; margin: 0; padding: 20px; color: #333; }
        h2 {color: #6610f2;}
        .container { max-width: 400px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 4px rgba(0, 0, 0, 0.1); text-align: center; }
        input, button { width: 100%; padding: 10px; margin: 10px 0; border-radius: 4px; border: 1px solid #ccc; }
        button { background: #6610f2; color: white; cursor: pointer; }
        .captcha { font-size: 24px; font-weight: bold; letter-spacing: 3px; background: #ddd; padding: 10px; display: inline-block; margin-top: 5px; }
        .error { color: red; }
        .disabled-message { color: red; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h2>Download Admit Card</h2>
    
    <?php if ($disabled_message) echo "<p class='disabled-message'>$disabled_message</p>"; ?>

    <?php if ($error) echo "<p class='error'>$error</p>"; ?>

    <?php if ($admit_card_status != 'disabled'): ?>
    <form action="admit_card.php" method="POST">
        <input type="text" name="reg_no" placeholder="Registration Number" required>
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="date" value="2002-02-01" min="1990-02-01" max="2021-02-01" name="dob" required>
        
        <!-- CAPTCHA -->
        <div class="captcha"><?php echo $_SESSION['captcha']; ?></div>
        <input type="text" name="captcha" placeholder="Enter CAPTCHA" required>

        <button type="submit">Download Admit Card</button>
    </form>
    <?php endif; ?>
</div>

</body>
</html>