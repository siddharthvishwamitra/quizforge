<?php
session_start();

// Prevent direct access
if (!isset($_SESSION['student'])) {
    header("Location: admit_card.php");
    exit;
}

// Fetch student details
$student = $_SESSION['student'];

// Destroy session on page load so that it cannot be revisited
unset($_SESSION['student']);
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admit Card</title>
    <link rel="stylesheet" href="css/font.css">
    <style>
        body {background: white; text-align: center; padding: 20px; }
        .container { max-width: 600px; background: white; padding: 20px; margin: auto; }
        .header { font-size: 22px; font-weight: bold; color: #6610f2; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { color: black; }
        .photo img { width: 128px; height: 128px; border-radius: 5px; }
        .print-btn { padding: 10px 20px; background: #6610f2; color: white; border: none; cursor: pointer; margin-top: 20px; }
        @media print{.print-btn{display:none!important;}}
    </style>
</head>
<body>

<div class="container">
    <div class="header">Print Admit Card</div>

    <table>
        <tr>
            <th>Full Name</th>
            <td><?php echo $student['full_name']; ?></td>
        </tr>
        <tr>
            <th>Registration No</th>
            <td><?php echo $student['reg_no']; ?></td>
        </tr>
        <tr>
            <th>Roll Number</th>
            <td><?php echo $student['roll_number'] ?: 'Not Assigned'; ?></td>
        </tr>
        <tr>
            <th>Date of Birth</th>
            <td><?php echo $student['dob']; ?></td>
        </tr>
        <tr>
            <th>School</th>
            <td><?php echo $student['school_name']; ?></td>
        </tr>
        <tr>
            <th>Level</th>
            <td><?php echo ($student['level'] == 1) ? 'Junior' : 'Senior'; ?></td>
        </tr>
        <tr>
            <th>Photo</th>
            <td class="photo">
                <?php if ($student['image']) { ?>
                    <img src="uploads/<?php echo $student['image']; ?>" alt="Student Photo">
                <?php } else { ?>
                    No Photo Available
                <?php } ?>
            </td>
        </tr>
    </table>

    <button class="print-btn" onclick="window.print()">Print Admit Card</button>
</div>

</body>
</html>
