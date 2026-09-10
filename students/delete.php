<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/database.php";

try {

    if (!isset($_GET["id"])) {
        throw new Exception("Student ID not provided.");
    }

    $id = $_GET["id"];

    $sql = "DELETE FROM students WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Failed to prepare SQL statement.");
    }

    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        throw new Exception("Failed to delete student.");
    }

    $stmt->close();

    header("Location: view.php");
    exit();

} catch (Exception $e) {

    echo "Error: " . htmlspecialchars($e->getMessage());

}

?>