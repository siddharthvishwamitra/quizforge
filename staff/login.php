<?php
include('config.php');
session_start();

// Check if admin is already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_dashboard.php");
    exit;
}

// Initialize session variables if not set
if (!isset($_SESSION['failed_attempts'])) {
    $_SESSION['failed_attempts'] = 0;
    $_SESSION['last_attempt_time'] = 0;
}

// Generate CAPTCHA if not already set
if (!isset($_SESSION['captcha_code'])) {
    $_SESSION['captcha_code'] = generateCaptcha();
}

// Function to generate alphanumeric CAPTCHA code
function generateCaptcha() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $captcha_code = '';
    for ($i = 0; $i < 6; $i++) {
        $captcha_code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $captcha_code;
}

// Check if user is locked out
if ($_SESSION['failed_attempts'] >= 5 && time() - $_SESSION['last_attempt_time'] < 60) {
    $_SESSION['error_message'] = "Too many failed attempts. Please wait 1 minute.";
} else {
    // Reset failed attempts if more than 1 minute has passed
    if (time() - $_SESSION['last_attempt_time'] >= 60) {
        $_SESSION['failed_attempts'] = 0;
    }

    // Handle login form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $captcha_input = $_POST['captcha'];

        // Check CAPTCHA
        if ($captcha_input != $_SESSION['captcha_code']) {
            $_SESSION['error_message'] = "Invalid CAPTCHA!";
        } else {
            // Prepared statement for checking admin credentials
            $query = "SELECT * FROM admin WHERE username = ? AND password = ?";
            $stmt = mysqli_prepare($conn, $query);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ss", $username, $password);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) > 0) {
                    // Successful login, reset failed attempts
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = $username;
                    $_SESSION['failed_attempts'] = 0;
                    header("Location: admin_dashboard.php");
                    exit;
                } else {
                    $_SESSION['failed_attempts']++;
                    $_SESSION['last_attempt_time'] = time();
                    $_SESSION['error_message'] = "Invalid username or password!";
                }
                mysqli_stmt_close($stmt);
            } else {
                $_SESSION['error_message'] = "Error preparing query!";
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
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/font.css">
    <style>
        body {
            background-color: #f0f4f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 90vh;
        }
        .login-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }
        h2 {
            text-align: center;
            color: #6610f2;
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-size: 14px;
            margin-bottom: 8px;
            color: #333;
        }
        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        input:focus {
            border-color: #4A90E2;
            outline: none;
        }
        button {
            width: 100%;
            padding: 15px;
            background-color: #6610f2;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .captcha-container {
            text-align: center;
            margin-bottom: 10px;
        }
        .captcha-code {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            background-color: #f7f7f7;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        @media (max-width: 600px) {
            .login-container {
                padding: 20px;
            }
            h2 {
                font-size: 20px;
            }
            input, button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<div style="color:red;">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']); // Clear error after displaying
        }
        ?>
        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" pattern="[A-Za-z]{3,20}" required><br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" pattern="[A-Za-z0-9@#]{4,20}" minlength="4" maxlength="20" required><br>
            <div class="captcha-container">
                <div class="captcha-code"><?php echo $_SESSION['captcha_code']; ?></div>
                <input type="text" name="captcha" id="captcha" pattern="[A-Za-z0-9]{6}" required><br>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>