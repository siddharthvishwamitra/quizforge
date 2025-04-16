<?php
include "config.php";
session_start();

// Redirect if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Password required to fetch student data
$data_fetch_password = "adminpassword"; 

// Handle form submission (Level + Password)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_level = $_POST['level'] ?? '';
    $entered_password = $_POST['password'] ?? '';

    if ($selected_level && $entered_password === $data_fetch_password) {
        $_SESSION['selected_level'] = $selected_level;
        $_SESSION['data_access'] = true;
    } else {
        echo "<script>alert('Incorrect Password or Selection!');</script>";
    }
}

// Remove access if page is refreshed or closed
if (!isset($_SESSION['selected_level']) || !isset($_SESSION['data_access'])) {
    unset($_SESSION['selected_level']);
    unset($_SESSION['data_access']);
}

// Fetch student data only if access is granted
$students = [];
if (isset($_SESSION['data_access']) && $_SESSION['data_access'] === true) {
    $level_filter = $_SESSION['selected_level'];
    $sql = "SELECT roll_number, reg_no, full_name, father_name, dob, gender, phone, aadhar_card 
            FROM students 
            WHERE level = $level_filter 
            ORDER BY roll_number ASC";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/font.css"></link>
    <title>Print Students List</title>
    <style>
        body {
            text-align: center;
            margin: 8px;
        }
        form {
            margin-bottom: 8px;
        }
        .table-cont {
            overflow: scroll;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 12px;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #f4f4f4;
        }
        button {
          
            margin-top: 20px;
            cursor: pointer;
        }
        .links-hz {
            display: flex;
            justify-content: center;
            gap: 10px;
            text-align: center;
            
        }
        .links-hz a {
           text-decoration: none;
        }
        .links-hz a:nth-child(1) {
            color: #3b5999;
            
        }
        .links-hz a:nth-child(2) {
            color: darkred;
        }
        @media (max-width: 600px) {
            table {
                font-size: 12px;
            }
            th, td {
                padding: 5px;
            }
        }
        @media print {
            * {
                -webkit-overflow-scrolling: auto;
                scrollbar-width: none;
            }
            form, .title-sd, .print-btn, .links-hz {
                display: none;
            }
        }
    </style>
    <script>
        function printPage() {
            window.print();
        }

        // Remove access when the tab is closed or refreshed
        window.addEventListener("beforeunload", function () {
            fetch(window.location.href, { method: "POST", body: new URLSearchParams({ clear_session: "1" }) });
        });
    </script>
</head>
<body>
<div class="links-hz">
<a href="admin_dashboard.php">Go to Dashboard </a>
<a href="logout.php">Logout</a>
</div>
<h2 class="title-sd">Print Students List</h2>

<!-- Selection Form (Level + Password) -->
<form method="POST">
    <label>Select Level: 
        <select name="level" required>
            <option value="" disabled selected>Select</option>
            <option value="1" <?php echo (isset($_SESSION['selected_level']) && $_SESSION['selected_level'] == 1) ? "selected" : ""; ?>>Junior</option>
            <option value="2" <?php echo (isset($_SESSION['selected_level']) && $_SESSION['selected_level'] == 2) ? "selected" : ""; ?>>Senior</option>
        </select>
    </label>
    
    <label>
        <input type="password" name="password" maxlength="20" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" required>
    </label>
    <button type="submit">Submit</button>
</form>

<?php if (isset($_SESSION['data_access']) && $_SESSION['data_access'] === true): ?>
    <h3>List of <?php echo ($_SESSION['selected_level'] == 1) ? "Junior" : "Senior"; ?> Students</h3>

    <?php if (count($students) > 0): ?>
    <div class="table-cont">
        <table>
            <thead>
                <tr>
                    <th>Roll No</th>
                    <th>Reg No</th>
                    <th>Full Name</th>
                    <th>Father's Name</th>
                    <th>DOB</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Aadhar No</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['roll_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['reg_no']);?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['father_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['dob']); ?></td>
                        <td><?php echo ($row['gender'] == 1) ? "Male" : "Female"; ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['aadhar_card']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <button class="print-btn" onclick="printPage()">Print</button>
    <?php else: ?>
        <p>No records found for <?php echo ($_SESSION['selected_level'] == 1) ? "Junior" : "Senior"; ?>.</p>
    <?php endif; ?>
<?php endif; ?>

</body>
</html>

<?php
// Clear session if requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_session'])) {
    unset($_SESSION['data_access']);
    unset($_SESSION['selected_level']);
    exit;
}

$conn->close();
?>