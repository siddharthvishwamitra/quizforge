<?php
include('config.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit;
}

// Fetch student details for editing
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM students WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $student = mysqli_fetch_assoc($result);
} else {
    // Redirect if no student id is provided
    header("Location: admin_students.php");
    exit;
}

// Handle form submission for updating student details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $father_name = mysqli_real_escape_string($conn, $_POST['father_name']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $school_name = mysqli_real_escape_string($conn, $_POST['school_name']);
    $level = mysqli_real_escape_string($conn, $_POST['level']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $aadhar_card = mysqli_real_escape_string($conn, $_POST['aadhar_card']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // Handle photo upload
    $image = $_FILES['image']['name'];
    $target_dir = "../uploads/";
    $image_extension = pathinfo($image, PATHINFO_EXTENSION);
    $new_image_name = $student['reg_no'] . '.' . $image_extension; // Rename to reg_no.jpg/png/etc.
    $target_file = $target_dir . $new_image_name;
    
    // Check if an image has been uploaded
    if (empty($image)) {
        // Retain the existing image if no new one is uploaded
        $image = $student['image'];
    } else {
        // Remove the old image from the server if it exists
        if (!empty($student['image']) && file_exists($target_dir . $student['image'])) {
            unlink($target_dir . $student['image']);
        }
        
        // Upload new image and update the image field
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = $new_image_name; // Use the new image name (reg_no)
        } else {
            $image = $student['image'];  // Keep the old image if upload fails
        }
    }

    // Update query to update the student's details
    $update_query = "UPDATE students SET 
                        full_name = '$full_name', 
                        father_name = '$father_name', 
                        dob = '$dob', 
                        gender = '$gender', 
                        school_name = '$school_name', 
                        level = '$level', 
                        phone = '$phone', 
                        aadhar_card = '$aadhar_card',
                        address = '$address', 
                        image = '$image' 
                    WHERE id = $id";

    if (mysqli_query($conn, $update_query)) {
        // Redirect to student list after successful update
        header("Location: admin_students.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="../css/font.css">
</head>
<body>
    <div class="container">
        <h2>Edit Student</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo $student['full_name']; ?>" required><br><br>

            <label for="father_name">Father's Name</label>
            <input type="text" id="father_name" name="father_name" value="<?php echo $student['father_name']; ?>" required><br><br>

            <label for="dob">Date of Birth</label>
            <input type="date" id="dob" name="dob" value="<?php echo $student['dob']; ?>" required><br><br>

            <label for="gender">Gender</label>
            <select name="gender" id="gender" required>
                <option value="1" <?php echo ($student['gender'] == '1') ? 'selected' : ''; ?>>Male</option>
                <option value="2" <?php echo ($student['gender'] == '2') ? 'selected' : ''; ?>>Female</option>
            </select><br><br>

            <label for="school_name">School Name</label>
            <input type="text" id="school_name" name="school_name" value="<?php echo $student['school_name']; ?>" required><br><br>

            <label for="level">Level</label>
            <select name="level" id="level" required>
                <option value="1" <?php echo ($student['level'] == '1') ? 'selected' : ''; ?>>Junior</option>
                <option value="2" <?php echo ($student['level'] == '2') ? 'selected' : ''; ?>>Senior</option>
            </select><br><br>

            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" value="<?php echo $student['phone']; ?>" required><br><br>
            
            <label for="aadhar_card">Aadhaar No</label>
            <input type="text" id="aadhar_card" name="aadhar_card" value="<?php echo $student['aadhar_card']; ?>" required><br><br>

            <label for="address">Address</label>
            <textarea id="address" name="address" required><?php echo $student['address']; ?></textarea><br><br>

            <label for="image">Student Image</label><br>
            <!-- Display existing image if present -->
            <?php if (!empty($student['image'])): ?>
                <img src="../uploads/<?php echo $student['image']; ?>" alt="Student Image" width="128" height="128"><br><br>
            <?php endif; ?>
            <input type="file" id="image" name="image"><br><br>

            <input type="submit" value="Update Student">
        </form>
    </div>
    <style>
/* Basic styling */
body {
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.container {
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    color: #333;
}

label {
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
    color: #555;
}

input[type="text"],
input[type="date"],
input[type="file"],
textarea,
select {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
}

input[type="submit"] {
    width: 100%;
    padding: 12px;
    background-color: #6610F2;
    color: white;
    border: none;
    font-size: 16px;
    cursor: pointer;
    border-radius: 4px;
}

input[type="submit"]:hover {
    background-color: #5500b3;
}

img {
    margin-bottom: 10px;
    border-radius: 4px;
}
</style>
</body>
</html>