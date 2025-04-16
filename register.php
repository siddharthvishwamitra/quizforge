<?php
session_start();
include('staff/config.php');

$query = "SELECT * FROM system_settings WHERE setting_name = 'registration_status'";
$result = mysqli_query($conn, $query);
$settings = mysqli_fetch_assoc($result);

if (!isset($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}

$registration_success = false;
$submitted_data = [];
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_token']) && $_POST['form_token'] === $_SESSION['form_token'] && $settings['setting_value'] == 'open') {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));

    $full_name = trim($_POST['full_name']);
    $father_name = trim($_POST['father_name']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $school_name = trim($_POST['school_name']);
    $level = $_POST['level'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $aadhar_card = trim($_POST['aadhar_card']);

    // Check for duplicate Aadhar Card
    $aadhar_check = "SELECT * FROM students WHERE aadhar_card = '$aadhar_card'";
    $aadhar_result = mysqli_query($conn, $aadhar_check);
    if (mysqli_num_rows($aadhar_result) > 0) {
        $error_message = "Aadhar number already in use! Contact orgnizer for correction in from.";
    }

    // Check image size before processing
    if ($_FILES['image']['size'] > 102400 && empty($error_message)) {
        $error_message = "Image size must be under 100KB.";
    }

    if (empty($error_message)) {
        do {
            $reg_no = rand(1000, 9999);
            $check_query = "SELECT * FROM students WHERE reg_no = '$reg_no'";
            $check_result = mysqli_query($conn, $check_query);
        } while (mysqli_num_rows($check_result) > 0);

        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_extension = pathinfo($image, PATHINFO_EXTENSION);
        $new_image_name = $reg_no . '.' . $image_extension;
        $image_folder = 'uploads/' . $new_image_name;
        move_uploaded_file($image_tmp, $image_folder);

        $sql = "INSERT INTO students (full_name, father_name, dob, gender, school_name, level, phone, address, aadhar_card, image, reg_no) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssssssssi", $full_name, $father_name, $dob, $gender, $school_name, $level, $phone, $address, $aadhar_card, $new_image_name, $reg_no);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['submitted_data'] = [
                    'full_name' => $full_name,
                    'father_name' => $father_name,
                    'dob' => $dob,
                    'gender' => ($gender == 1 ? "Male" : "Female"),
                    'school_name' => $school_name,
                    'level' => ($level == 1 ? "Junior" : "Senior"),
                    'phone' => $phone,
                    'address' => $address,
                    'aadhar_card' => $aadhar_card,
                    'reg_no' => $reg_no
                ];
                $registration_success = true;
            }
            mysqli_stmt_close($stmt);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    unset($_SESSION['submitted_data']);
}

if (isset($_SESSION['submitted_data'])) {
    $submitted_data = $_SESSION['submitted_data'];
    $registration_success = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Form</title>
  <link rel="stylesheet" href="css/font.css">
  <link rel="stylesheet" href="css/reg.css">
</head>
<body>
<?php include 'part/header.php'; ?>

<?php if ($settings['setting_value'] == 'closed'): ?>
    <div class="form-container">
    <div style="padding: 8px;">
      <h2>Registration is closed!</h2>
      <p>Sorry, we are not accepting registrations at the moment.</p>
    </div></div>
    <?php include 'part/footer.php'; ?>
    <?php exit; ?>
<?php endif; ?>
<div class="form-container">
  <?php if ($registration_success): ?>
    <div class="result-container">
      <h3>Registration Successful!</h3>
      <p><strong>Registration ID:</strong> <?= $submitted_data['reg_no']; ?></p>
      <p><strong>Aadhar Card:</strong> <?= $submitted_data['aadhar_card']; ?></p>
      <p><strong>Full Name:</strong> <?= $submitted_data['full_name']; ?></p>
      <p><strong>Father's Name:</strong> <?= $submitted_data['father_name']; ?></p>
      <p><strong>Date of Birth:</strong> <?= $submitted_data['dob']; ?></p>
      <p><strong>Gender:</strong> <?= $submitted_data['gender']; ?></p>
      <p><strong>School Name:</strong> <?= $submitted_data['school_name']; ?></p>
      <p><strong>Level:</strong> <?= $submitted_data['level']; ?></p>
      <p><strong>Phone:</strong> <?= $submitted_data['phone']; ?></p>
      <p><strong>Address:</strong> <?= $submitted_data['address']; ?></p>
    </div>
    <center><a href="register" class="home-link-pr">Homepage</a></center>
  <?php else: ?>
    <h2>Registration Form</h2>
    <?php if (!empty($error_message)): ?>
      <div style="color: red; margin-bottom: 10px;"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="form_token" value="<?php echo $_SESSION['form_token']; ?>">
      <input type="text" name="aadhar_card" pattern="[0-9]{12}" maxlength="12" placeholder="Aadhar Card Number" required>
      <input type="text" pattern="[A-Za-z ]{3,50}" maxlength="50" name="full_name" placeholder="Full Name" required>
      <input type="text" pattern="[A-Za-z ]{3,50}" maxlength="50" name="father_name" placeholder="Father's Name" required>
      <input type="date" value="2002-02-01" min="1990-02-01" max="2021-02-01" name="dob" required>
      <select name="gender" required>
        <option value="" disabled selected>Select Gender</option>
        <option value="1">Male</option>
        <option value="2">Female</option>
      </select>
      <input type="text" name="school_name" placeholder="School Name" required>
      <select name="level" required>
        <option value="" disabled selected>Select Level</option>
        <option value="1">Junior</option>
        <option value="2">Senior</option>
      </select>
      <input type="tel" pattern="[0-9]{10}" maxlength="10" name="phone" placeholder="Phone" required>
      <textarea name="address" placeholder="Address" maxlength="50" minlength="5" style="height:100px;" required></textarea>
      <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" required>
      <img id="imagePreview" style="display:none;width:128px;height:128px;border:1px solid #ccc;margin:10px 0;">
      <div id="fileError" style="color:red;"></div>

      <small>
        <b>Instructions:</b>
        <ol>
          <li>Photo must be formal and selfies are not allowed.</li>
          <li>Photo background must be white or grey.</li>
          <li>Image size must not be more than 100kb.</li>
          <li>Image width and height should be 512x512 pixels.</li>
          <li>Image formats allowed: JPG, JPEG, PNG & WEBP.</li>
          <li>If uploaded informal image, it may lead to rejection.</li>
        </ol>
      </small>
      <button type="submit">Submit</button>
    </form>
  <?php endif; ?>
</div>
<?php include 'part/footer.php'; ?>
<script>
document.getElementById('image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('imagePreview');
    const errorMessage = document.getElementById('fileError');
    errorMessage.textContent = '';

    if (file) {
        const fileSizeInKB = file.size / 1024;
        const validFormats = ['image/jpeg', 'image/png', 'image/webp'];

        if (fileSizeInKB > 100) {
            errorMessage.textContent = 'File size exceeds 100KB.';
            event.target.value = '';
            preview.style.display = 'none';
            return;
        }

        if (!validFormats.includes(file.type)) {
            errorMessage.textContent = 'Invalid file format.';
            event.target.value = '';
            preview.style.display = 'none';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
});
</script>
</body>
</html>