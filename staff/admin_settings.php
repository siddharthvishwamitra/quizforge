<?php
include('config.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Handle AJAX request for updating settings
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_settings') {
    $allowed_values = [
        'registration_status' => ['open', 'closed'],
        'admit_card_status' => ['enabled', 'disabled'],
        'result_status' => ['enabled', 'disabled'],
        'reg_print' => ['enabled', 'disabled']
    ];

    $errors = [];

    // Validate and update settings
    foreach ($allowed_values as $setting => $valid_values) {
        if (isset($_POST[$setting]) && in_array($_POST[$setting], $valid_values)) {
            $value = $_POST[$setting];

            $stmt = $conn->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_name = ?");
            $stmt->bind_param("ss", $value, $setting);

            if (!$stmt->execute()) {
                $errors[] = "Failed to update $setting.";
            }
            $stmt->close();
        } else {
            $errors[] = "Invalid value for $setting.";
        }
    }

    if (empty($errors)) {
        echo json_encode(['status' => 'success', 'message' => 'Settings updated successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => implode(" ", $errors)]);
    }
    exit;
}

// Fetch current settings
$settings = [];
$setting_names = ['registration_status', 'admit_card_status', 'result_status', 'reg_print'];

foreach ($setting_names as $name) {
    $query = "SELECT setting_value FROM system_settings WHERE setting_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->bind_result($value);
    $stmt->fetch();
    $stmt->close();

    $settings[$name] = $value ?? 'open'; // Default to 'open' if not set
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
        }
        .container {
            width: 100%;
            max-width: 100%;
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
            display: block;
        }
        select, button {
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
        .status-message {
            display: none;
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <h1>Admin System Settings</h1>
        <div class="header-buttons">
            <a href="admin_dashboard.php" class="header-button">Go to Dashboard</a>
            <a href="logout.php" class="header-button">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <h2>Admin Settings</h2>
        <div id="statusMessage" class="status-message"></div>
        <br>
        <form id="settingsForm">
            <label for="registration_status">Registration Status:</label>
            <select name="registration_status" id="registration_status" required>
                <option value="open" <?php echo ($settings['registration_status'] == 'open') ? 'selected' : ''; ?>>Open</option>
                <option value="closed" <?php echo ($settings['registration_status'] == 'closed') ? 'selected' : ''; ?>>Closed</option>
            </select>
            
            <label for="reg_print">Print Registration:</label>
            <select name="reg_print" id="reg_print" required>
                <option value="enabled" <?php echo ($settings['reg_print'] == 'enabled') ? 'selected' : ''; ?>>Enabled</option>
                <option value="disabled" <?php echo ($settings['reg_print'] == 'disabled') ? 'selected' : ''; ?>>Disabled</option>
            </select>

            <label for="admit_card_status">Admit Card Status:</label>
            <select name="admit_card_status" id="admit_card_status" required>
                <option value="enabled" <?php echo ($settings['admit_card_status'] == 'enabled') ? 'selected' : ''; ?>>Enabled</option>
                <option value="disabled" <?php echo ($settings['admit_card_status'] == 'disabled') ? 'selected' : ''; ?>>Disabled</option>
            </select>

            <label for="result_status">Check Result Status:</label>
            <select name="result_status" id="result_status" required>
                <option value="enabled" <?php echo ($settings['result_status'] == 'enabled') ? 'selected' : ''; ?>>Enabled</option>
                <option value="disabled" <?php echo ($settings['result_status'] == 'disabled') ? 'selected' : ''; ?>>Disabled</option>
            </select>

            <button type="submit">Update Settings</button>
        </form>
    </div>

    <script>
        document.getElementById('settingsForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            let formData = new FormData(this);
            formData.append('action', 'update_settings');

            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'admin_settings.php', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    let response = JSON.parse(xhr.responseText);
                    let statusMessage = document.getElementById('statusMessage');

                    if (response.status === 'success') {
                        statusMessage.className = 'status-message success';
                    } else {
                        statusMessage.className = 'status-message error';
                    }

                    statusMessage.innerHTML = response.message;
                    statusMessage.style.display = 'block';

                    setTimeout(() => {
                        statusMessage.style.display = 'none';
                    }, 3000);
                }
            };
            
            xhr.send(formData);
        });
    </script>
</body>
</html>