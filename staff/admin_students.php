<?php
include('config.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");  // Redirect to login page after logout
    exit;
}

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search functionality
$search = "";
$whereClause = "";
if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $whereClause = "WHERE reg_no LIKE '%$search%' OR full_name LIKE '%$search%' OR phone LIKE '%$search%' OR aadhar_card LIKE '%$search%'";
}

// Sorting functionality
$order_by = "ORDER BY id DESC";
if (isset($_GET['sort_by'])) {
    if ($_GET['sort_by'] == 'roll_no') {
        $order_by = "ORDER BY roll_number ASC";
    } elseif ($_GET['sort_by'] == 'level') {
        $order_by = "ORDER BY level ASC";
    }
}

// Fetch students with pagination, search filter, and sorting
$query = "SELECT * FROM students $whereClause $order_by LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
$total_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM students $whereClause"))['count'];
$total_pages = ceil($total_students / $limit);

// Handle student deletion through AJAX (see JavaScript below for the AJAX call)
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    // Fetch the student's data to get the image path
    $query = "SELECT image FROM students WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $student = mysqli_fetch_assoc($result);

    if ($student) {
        // Delete the student record from the database
        $delete_query = "DELETE FROM students WHERE id = $id";
        if (mysqli_query($conn, $delete_query)) {
            // If the student has an image, delete it from the uploads folder
            if (!empty($student['image']) && file_exists("../uploads/" . $student['image'])) {
                unlink("../uploads/" . $student['image']);
            }
            echo json_encode(['status' => 'success', 'message' => 'Student deleted successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($conn)]);
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
    <title>Admin - Manage Students</title>
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 100%;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: #6610f2;
        }
        .table-cont {
            max-width: 100%; 
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #6610f2;
            color: white;
        }

        img {
            width: 64px;
            height: 64px;
            border-radius: 50%;
        }

        .action {
            white-space: nowrap;
        }

        .action a {
            text-decoration: none;
            display: inline-block;
            color: white;
        }

        .edit-btn {
            padding: 5px 10px;
            background: green;
            margin-right: 5px;
            border-radius: 5px;
        }

        .delete-btn {
            padding: 5px 10px;
            background: red;
            border-radius: 5px;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            padding: 8px 12px;
            text-decoration: none;
            border: 1px solid #6610f2;
            color: #6610f2;
            border-radius: 4px;
            margin: 2px;
        }

        .pagination a.active, .pagination a:hover {
            background: #6610f2;
            color: white;
        }

        .search-box {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type='text'] {
            padding: 8px;
            width: 200px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 8px 12px;
            background: #6610f2;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .filters {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        select {
            padding: 8px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        @media (max-width: 768px) {
            table {
                font-size: 12px;
            }

            .pagination a {
                font-size: 12px;
            }

            .filters {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <h1>Manage Students</h1>
        <div class="header-buttons">
            <a href="admin_dashboard.php" class="header-button">Go to Dashboard</a>
            <a href="logout.php" class="header-button">Logout</a>
        </div>
    </div>
    <div class="container">
        <h2>Student Management</h2>

        <!-- Search Form -->
        <div class="search-box">
            <form method="GET" action="">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by Reg No, Name, Aadhaar No or Phone" required>
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Filters and Sorting -->
        <div class="filters">
            <form method="GET" action="">
                <select name="sort_by" onchange="this.form.submit()">
                    <option value="">Sort By</option>
                    <option value="roll_no" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'roll_no' ? 'selected' : ''; ?>>Roll Number</option>
                    <option value="level" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'level' ? 'selected' : ''; ?>>Junior/Senior</option>
                </select>
            </form>
        </div>

        <div class="table-cont">
            <!-- Student Table -->
            <table>
                <thead>
                    <tr>
                        <th>Reg No</th>
                        <th>Full Name</th>
                        <th>Father's Name</th>
                        <th>DOB</th>
                        <th>Gender</th>
                        <th>School</th>
                        <th>Level</th>
                        <th>Phone</th>
                        <th>Aadhaar No</th>
                        <th>Address</th>
                        <th>Photo</th>
                        <th>Roll No</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($student = mysqli_fetch_assoc($result)) { ?>
                        <tr id="student_<?php echo $student['id']; ?>">
                            <td><?php echo $student['reg_no']; ?></td>
                            <td><?php echo $student['full_name']; ?></td>
                            <td><?php echo $student['father_name']; ?></td>
                            <td><?php echo $student['dob']; ?></td>
                            <td><?php echo ($student['gender'] == '1') ? 'Male' : 'Female'; ?></td>
                            <td><?php echo $student['school_name']; ?></td>
                            <td><?php echo ($student['level'] == '1') ? 'Junior' : 'Senior'; ?></td>
                            <td><?php echo $student['phone']; ?></td>
                            <td><?php echo $student['aadhar_card']; ?></td>
                            <td><?php echo $student['address']; ?></td>
                            <td>
                                <?php if ($student['image']) { ?>
                                    <img src="../uploads/<?php echo $student['image']; ?>" alt="Student Photo">
                                <?php } else { ?>
                                    No Photo
                                <?php } ?>
                            </td>
                            <td><?php echo !empty($student['roll_number']) ? $student['roll_number'] : 'Not Assigned'; ?></td>
                            <td class="action">
                                <a class="edit-btn" href="edit_student.php?id=<?php echo $student['id']; ?>">Edit</a> 
                                <a href="javascript:void(0);" class="delete_student delete-btn" data-id="<?php echo $student['id']; ?>">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort_by=<?php echo isset($_GET['sort_by']) ? $_GET['sort_by'] : ''; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php } ?>
        </div>
<center style="padding:8px;"><?php
$result = $conn->query("SELECT COUNT(*) AS total FROM students");
echo "Total entries are " . $result->fetch_assoc()['total'];
$conn->close();
?></center>
</div>

    <script src="../js/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".delete_student").click(function() {
                var studentId = $(this).data('id');

                if (confirm('Are you sure you want to delete this student?')) {
                    $.ajax({
                        type: 'POST',
                        url: 'admin_students.php',
                        data: { delete_id: studentId },
                        success: function(response) {
                            var result = JSON.parse(response);
                            if (result.status == 'success') {
                                $("#student_" + studentId).fadeOut();
                                alert(result.message);
                            } else {
                                alert(result.message);
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>