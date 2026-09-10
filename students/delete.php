<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/database.php";

// Check whether student ID is provided
if (!isset($_GET["id"])) {
    die("Student ID not provided.");
}

$id = $_GET["id"];

// Delete student
$sql = "DELETE FROM students WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: view.php");
    exit();
} else {
    echo "Failed to delete student.";
}

$stmt->close();

?>