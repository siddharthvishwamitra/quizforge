<?php
$servername = "auth-db1339.hstgr.io";
$username = "u163031191_phpquiz";
$password = "siddharthAa1#";
$dbname = "u163031191_phpquiz";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
