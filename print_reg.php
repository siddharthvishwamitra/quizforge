<?php
include('staff/config.php');
session_start();

// Fetch settings from system_settings
$query = "SELECT setting_value FROM system_settings WHERE setting_name = 'reg_print'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$reg_print = $row['setting_value'] ?? 'enabled'; // Default to enabled

// If reg_print is disabled, show the message and exit
if ($reg_print == 'disabled') {
    $disabled_message = "Printing of registration details is currently not available.";
    $show_form = false; // Disable the form if printing is disabled
} else {
    $disabled_message = "";
    $show_form = true; // Enable the form if printing is enabled
}

// CAPTCHA generation
$captcha_text = "";
$captcha_error = false;
$search_error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle CAPTCHA validation
    if ($_POST['captcha'] != $_SESSION['captcha_text']) {
        $captcha_error = true;
    }

    // Search for the student based on the entered details
    if (!$captcha_error) {
        $name = mysqli_real_escape_string($conn, $_POST['full_name']);
        $dob = mysqli_real_escape_string($conn, $_POST['dob']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);

        // Query to fetch student details
        $query = "SELECT * FROM students WHERE full_name = '$name' AND dob = '$dob' AND phone = '$phone' LIMIT 1";
        $result = mysqli_query($conn, $query);
        $student = mysqli_fetch_assoc($result);

        // If no student is found
        if (!$student) {
            $search_error = true;
        }
    }

    // Generate a new CAPTCHA for the next search attempt
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $captcha_text = substr(str_shuffle($characters), 0, 6);
    $_SESSION['captcha_text'] = $captcha_text;

} else {
    // Generate CAPTCHA on page load
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $captcha_text = substr(str_shuffle($characters), 0, 6);
    $_SESSION['captcha_text'] = $captcha_text;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Registration</title>
    <link rel="stylesheet" href="css/font.css">
    <style>
        body{background-color:#f4f4f4;margin:0;padding:20px;text-align:center}.container{max-width:600px;margin:auto;background:#fff;padding:20px;border-radius:8px;box-shadow:0 0 4px rgba(0,0,0,0.1)}h2{color:#6610f2}label{font-size:16px;margin-bottom:5px}input[type="text"],input[type="date"]{width:100%;padding:10px;margin:10px 0;border:1px solid #ccc;border-radius:4px;background:#fff}.captcha-container{margin:0;text-align:center}.captcha { font-size: 24px; font-weight: bold; letter-spacing: 3px; background: #ddd; padding: 10px; display: inline-block; margin-top: 5px; }.captcha-input{padding:8px;margin-top:5px;width:60%;border:1px solid #ccc;border-radius:4px}.button{padding:12px;background-color:#6610f2;color:white;border:none;border-radius:4px;cursor:pointer;width:100%}.button:hover{background-color:#5208c5}.error-message{color:red;text-align:center}.result-table{margin-top:20px;width:100%;border-collapse:collapse}.result-table th,.result-table td{padding:10px;border:1px solid #ddd;text-align:left}.result-table th{background-color:#6610f2;color:white}.result-table td{background-color:#f9f9f9}.print-btn{width:100%;margin-top:20px;padding:10px 20px;background-color:#28a745;color:white;border:none;border-radius:4px;cursor:pointer}.print-btn:hover{background-color:#218838}.disabled-message{color: red;font-weight:bold;}@media print{body{background-color:white}.container{box-shadow:none;border-radius:0;width:100%;padding:0}.print-btn{display:none;}}
        @media print{form{display:none!important;}}
        .result-container{max-width:600px;margin:0 auto;}
    </style>
</head>
<body>
    <div class="container">
        <h2>Search Your Registration</h2>

        <!-- Display disabled message -->
        <?php if ($disabled_message) echo "<p class='disabled-message'>$disabled_message</p>"; ?>

        <?php if ($show_form) { ?>
            <!-- Display error message for CAPTCHA or search failure -->
            <?php if ($captcha_error) { ?>
                <p class="error-message">Incorrect CAPTCHA. Please try again.</p>
            <?php } ?>

            <?php if ($search_error) { ?>
                <p class="error-message">No student found with the provided details. Please check and try again.</p>
            <?php } ?>

            <!-- Search Form -->
            <form method="POST" action="">
                <input type="text" name="full_name" id="full_name" pattern="[A-Za-z ]{3,50}" maxlength="50" title="Only English letters and spaces, between 3 to 50 characters" placeholder="Full Name" required>
                <input type="date" value="2002-02-01" min="1990-02-01" max="2021-02-01" name="dob" id="dob" required>
                <input type="text" name="phone" id="phone" pattern="[0-9]{10}" maxlength="10" minlength="10" title="Enter exactly 10 digits" placeholder="Phone Number" required>

                <div class="captcha-container">
                    <span class="captcha"><?php echo $captcha_text; ?></span><br>
                    <input type="text" name="captcha" class="captcha-input" placeholder="Enter CAPTCHA" required>
                </div>

                <button type="submit" class="button">Search</button>
            </form>
</div>
            <?php if (isset($student)) { ?>
                <!-- Show Student Data (Excluding Photo) -->
                <div class="result-container">
                <table class="result-table">
                    <tr>
                        <th>Reg No</th>
                        <td><?php echo $student['reg_no']; ?></td>
                    </tr>
                    <tr>
                        <th>Full Name</th>
                        <td><?php echo $student['full_name']; ?></td>
                    </tr>
                    <tr>
                        <th>Father's Name</th>
                        <td><?php echo $student['father_name']; ?></td>
                    </tr>
                    <tr>
                        <th>Date of Birth</th>
                        <td><?php echo $student['dob']; ?></td>
                    </tr>
                    <tr>
                        <th>Gender</th>
                        <td><?php echo ($student['gender'] == '1') ? 'Male' : 'Female'; ?></td>
                    </tr>
                    <tr>
                        <th>School Name</th>
                        <td><?php echo $student['school_name']; ?></td>
                    </tr>
                    <tr>
                        <th>Level</th>
                        <td><?php echo ($student['level'] == '1') ? 'Junior' : 'Senior'; ?></td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td><?php echo $student['phone']; ?></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td><?php echo $student['address']; ?></td>
                    </tr>
                </table>

                <!-- Print Button -->
                <button class="print-btn" onclick="window.print();">Print Registration</button>
            <?php } ?>
        <?php } ?>
</div>
</body>
</html>