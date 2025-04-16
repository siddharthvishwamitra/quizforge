<?php
session_start();
include('config.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Search, sort, and filter functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'roll_number ASC'; // Default sorting by Roll Number
$status_filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';

// Pagination setup
$students_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $students_per_page;

$sql = "SELECT * FROM students WHERE (full_name LIKE '%$search%' OR reg_no LIKE '%$search%' OR roll_number LIKE '%$search%')";
if ($status_filter) {
    $sql .= " AND result_status = '$status_filter'";
}
$sql .= " ORDER BY $order_by LIMIT $start_from, $students_per_page";
$result = mysqli_query($conn, $sql);

$total_sql = "SELECT COUNT(*) AS total_students FROM students WHERE (full_name LIKE '%$search%' OR reg_no LIKE '%$search%' OR roll_number LIKE '%$search%')";
if ($status_filter) {
    $total_sql .= " AND result_status = '$status_filter'";
}
$total_result = mysqli_query($conn, $total_sql);
$total_row = mysqli_fetch_assoc($total_result);
$total_students = $total_row['total_students'];
$total_pages = ceil($total_students / $students_per_page);

// Handle AJAX request for updating the result
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax'])) {
    if (isset($_POST['reg_no']) && isset($_POST['obtained_marks']) && isset($_POST['result_status'])) {
        $reg_no = $_POST['reg_no'];
        $obtained_marks = $_POST['obtained_marks'];
        $result_status = $_POST['result_status'];

        if ($obtained_marks >= 0 && $obtained_marks <= 100) {
            $sql_update = "UPDATE students SET obtained_marks = ?, result_status = ? WHERE reg_no = ?";
            $stmt = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt, 'iss', $obtained_marks, $result_status, $reg_no);

            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['status' => 'success', 'obtained_marks' => $obtained_marks, 'result_status' => $result_status]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update the result.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Marks must be between 0 and 100.']);
        }
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Results</title>
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/style.css">
    <script>
        function updateResult(regNo) {
            var obtainedMarks = document.getElementById('marks_' + regNo).value;
            var resultStatus = document.getElementById('status_' + regNo).value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "manage_results.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        document.getElementById('result_status_' + regNo).innerText = response.result_status;
                        document.getElementById('obtained_marks_' + regNo).innerText = response.obtained_marks;
                    } else {
                        alert(response.message);
                    }
                }
            };
            xhr.send("ajax=true&reg_no=" + regNo + "&obtained_marks=" + obtainedMarks + "&result_status=" + resultStatus);
        }
    </script>
</head>
<body>

<div class="dashboard-header">
    <h1>Manage Results</h1>
    <div class="header-buttons">
        <a href="admin_dashboard.php" class="header-button">Go to Dashboard</a>
        <a href="logout.php" class="header-button">Logout</a>
    </div>
</div>

<div id="error-message" class="error-message" style="display: none;"><?php echo $error_message; ?></div>

<form method="GET" class="search-form">
    <input type="text" name="search" placeholder="Search by name, roll number or reg no" value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<div class="filters">
    <form method="GET" class="filter-form">
        <select name="status_filter" onchange="this.form.submit()">
            <option value="">Filter by status</option>
            <option value="Pass" <?php echo ($status_filter == 'Pass') ? 'selected' : ''; ?>>Pass</option>
            <option value="Fail" <?php echo ($status_filter == 'Fail') ? 'selected' : ''; ?>>Fail</option>
        </select>

        <select name="order_by" onchange="this.form.submit()">
            <option value="roll_number ASC" <?php echo ($order_by == 'roll_number ASC') ? 'selected' : ''; ?>>Sort by Roll Number</option>
            <option value="obtained_marks DESC" <?php echo ($order_by == 'obtained_marks DESC') ? 'selected' : ''; ?>>Sort by Higher Marks</option>
            <option value="obtained_marks ASC" <?php echo ($order_by == 'obtained_marks ASC') ? 'selected' : ''; ?>>Sort by Lower Marks</option>
        </select>
    </form>
</div>

<div class="table-cont">
    <table class="results-table">
        <thead>
        <tr>
            <th>Reg No</th>
            <th>Roll No</th>
            <th>Full Name</th>
            <th>Obtained Marks</th>
            <th>Total Marks</th>
            <th>Result Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['reg_no']; ?></td>
                <td><?php echo $row['roll_number']; ?></td>
                <td><?php echo $row['full_name']; ?></td>
                <td id="obtained_marks_<?php echo $row['reg_no']; ?>"><?php echo $row['obtained_marks']; ?></td>
                <td><?php echo $row['total_marks']; ?></td>
                <td id="result_status_<?php echo $row['reg_no']; ?>"><?php echo $row['result_status']; ?></td>
                <td class="inline">
                    <input type="number" id="marks_<?php echo $row['reg_no']; ?>" value="<?php echo $row['obtained_marks']; ?>" max="100" min="0">
                    <select id="status_<?php echo $row['reg_no']; ?>">
                        <option value="Pass" <?php echo ($row['result_status'] == 'Pass') ? 'selected' : ''; ?>>Pass</option>
                        <option value="Fail" <?php echo ($row['result_status'] == 'Fail') ? 'selected' : ''; ?>>Fail</option>
                    </select>
                    <button onclick="updateResult(<?php echo $row['reg_no']; ?>)">Update</button>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<div class="pagination">
    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
        <a href="manage_results.php?page=<?php echo $i; ?>&search=<?php echo $search; ?>&status_filter=<?php echo $status_filter; ?>&order_by=<?php echo $order_by; ?>" class="page-link"><?php echo $i; ?></a>
    <?php } ?>
</div>

<style>
body {
    background-color: #ffffff;
    margin: 0;
    padding: 0;
    color: #333;
}

h2 {
    text-align: center;
    color: #007bff;
}

/* Error message styling */
.error-message {
    background-color: #f8d7da;
    color: #721c24;
    padding: 10px;
    border-radius: 4px;
    margin: 20px auto;
    width: 80%;
    font-size: 12px;
    display: none;
}

/* Search form */
.search-form {
    max-width: 600px;
    margin: 20px auto;
    text-align: center;
}

.search-form input {
    padding: 8px;
    width: 70%;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background: #ffffff;
}

.search-form button {
    padding: 8px 12px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.search-form button:hover {
    background-color: #0056b3;
}

/* Filter and sort dropdown */
.filters {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 20px;
}

.filter-form select {
    padding: 10px;
    border-radius: 4px;
    font-size: 12px;
    border: 1px solid #ccc;
}

/* Results Table */
.table-cont {
    max-width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    padding: 15px;
    background: #fff;
    padding-top: 0px;
}

.results-table {
    width: 100%;
    margin: 8px auto;
    border-collapse: collapse;
    text-align: left;
}

.results-table th,
.results-table td {
    padding: 12px;
    border: 1px solid #ddd;
    position: relative;
    vertical-align: top;
}

.results-table th {
    background-color: #6610f2;
    color: white;
}

.results-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.results-table tr:hover {
    background-color: #f1f1f1;
}

.results-table input,
.results-table select {
    padding: 5px;
    margin: 5px;
    border-radius: 4px;
    border: 1px solid #ccc;
    width: 100%;
}

.results-table button {
    padding: 5px 10px;
    margin: 5px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
}

.results-table button:hover {
    background-color: #218838;
}

/* Pagination */
.pagination {
    text-align: center;
    margin-top: 20px;
    margin-bottom: 20px;
    font-size: 12px;
}

.page-link {
    padding: 8px 12px;
    margin: 0 5px;
    background-color: #6610f2;
    border: 1px solid #6610f2;
    color: #ffffff;
    text-decoration: none;
    border-radius: 4px;
}

.page-link:hover {
    background-color: #ffffff;
    color: #6610f2;
}

/* Special td.inline behavior */
td.inline {
    display: flex;
    align-items: center;
    gap: 10px;
    justify-content: center;
    height: 100%;
    box-sizing: border-box;
}

td.inline select,
td.inline button,
td.inline input {
    flex: 1;
    min-width: 70px;
}

@media (max-width: 768px) {
    table {
        font-size: 12px;
    }

    td.inline {
        flex-direction: row;
        flex-wrap: wrap;
        align-items: stretch;
        min-height: 100%;
        gap: 5px;
    }

    td.inline select,
    td.inline button,
    td.inline input {
        min-width: 40px;
        flex: 1;
        font-size: 12px;
    }
}

</style>
</body>
</html>