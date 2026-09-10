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

    $sql = "SELECT * FROM students";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Failed to retrieve student records.");
    }

    $content = "";

    while ($student = $result->fetch_assoc()) {

        $marks = [
            $student["marks1"],
            $student["marks2"],
            $student["marks3"]
        ];

        $total = array_sum($marks);
        $average = $total / count($marks);

        if ($average >= 90) {
            $grade = "S";
        } elseif ($average >= 80) {
            $grade = "A";
        } elseif ($average >= 70) {
            $grade = "B";
        } elseif ($average >= 60) {
            $grade = "C";
        } elseif ($average >= 50) {
            $grade = "D";
        } else {
            $grade = "F";
        }

        $content .= "Roll Number: " . $student["roll_no"] . "\n";
        $content .= "Name: " . $student["name"] . "\n";
        $content .= "Department: " . $student["department"] . "\n";
        $content .= "Semester: " . $student["semester"] . "\n";
        $content .= "Email: " . $student["email"] . "\n";
        $content .= "Marks: " . implode(", ", $marks) . "\n";
        $content .= "Total: " . $total . "\n";
        $content .= "Average: " . number_format($average, 2) . "\n";
        $content .= "Grade: " . $grade . "\n";
        $content .= "----------------------------------------\n";
    }

    $file = "../files/students.txt";

    if (file_put_contents($file, $content) === false) {
        throw new Exception("Failed to update student text file.");
    }

    header("Location: view.php");
    exit();

} catch (Exception $e) {

    echo "Error: " . htmlspecialchars($e->getMessage());

}

?>