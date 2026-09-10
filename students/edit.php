<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/database.php";

$error = "";
$success = "";

if (!isset($_GET["id"]) && !isset($_POST["id"])) {
    die("Student ID not provided.");
}

if (isset($_POST["id"])) {
    $id = $_POST["id"];
} else {
    $id = $_GET["id"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $roll_no = trim($_POST["roll_no"]);
    $name = trim($_POST["name"]);
    $department = trim($_POST["department"]);
    $semester = $_POST["semester"];
    $email = trim($_POST["email"]);

    $marks1 = $_POST["marks1"];
    $marks2 = $_POST["marks2"];
    $marks3 = $_POST["marks3"];

    $marks = [$marks1, $marks2, $marks3];

    if (
        empty($roll_no) ||
        empty($name) ||
        empty($department) ||
        empty($semester) ||
        empty($email)
    ) {
        $error = "All fields are required.";
    }

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    }

    elseif ($semester < 1 || $semester > 8) {
        $error = "Semester must be between 1 and 8.";
    }

    else {
        foreach ($marks as $mark) {
            if (!is_numeric($mark) || $mark < 0 || $mark > 100) {
                $error = "Marks must be between 0 and 100.";
                break;
            }
        }
    }

    if (empty($error)) {

        try {
            $sql = "UPDATE students
                    SET roll_no = ?,
                        name = ?,
                        department = ?,
                        semester = ?,
                        email = ?,
                        marks1 = ?,
                        marks2 = ?,
                        marks3 = ?
                    WHERE id = ?";

            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                throw new Exception("Failed to prepare SQL statement.");
            }

            $stmt->bind_param(
                "sssisiiii",
                $roll_no,
                $name,
                $department,
                $semester,
                $email,
                $marks1,
                $marks2,
                $marks3,
                $id
            );

            if (!$stmt->execute()) {
                throw new Exception("Failed to update student details.");
            }

            $stmt->close();

            $success = "Student details updated successfully.";

        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

} else {

    try {

        $sql = "SELECT * FROM students WHERE id = ?";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Failed to prepare SQL statement.");
        }

        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            throw new Exception("Failed to retrieve student details.");
        }

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $student = $result->fetch_assoc();
        } else {
            throw new Exception("Student not found.");
        }

        $stmt->close();

    } catch (Exception $e) {

        $error = $e->getMessage();

    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<h1>Edit Student</h1>

<a href="view.php">Back to Students</a>

<br><br>

<?php if (!empty($error)): ?>
    <p><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <p><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>


<form action="edit.php" method="POST">

    <input type="hidden" name="id"
           value="<?php echo htmlspecialchars($id); ?>">


    <label>Roll Number:</label>
    <input type="text"
           name="roll_no"
           value="<?php echo htmlspecialchars($student["roll_no"] ?? $roll_no ?? ""); ?>"
           required>

    <br><br>


    <label>Name:</label>
    <input type="text"
           name="name"
           value="<?php echo htmlspecialchars($student["name"] ?? $name ?? ""); ?>"
           required>

    <br><br>


    <label>Department:</label>
    <input type="text"
           name="department"
           value="<?php echo htmlspecialchars($student["department"] ?? $department ?? ""); ?>"
           required>

    <br><br>


    <label>Semester:</label>
    <input type="number"
           name="semester"
           min="1"
           max="8"
           value="<?php echo htmlspecialchars($student["semester"] ?? $semester ?? ""); ?>"
           required>

    <br><br>


    <label>Email:</label>
    <input type="email"
           name="email"
           value="<?php echo htmlspecialchars($student["email"] ?? $email ?? ""); ?>"
           required>

    <br><br>


    <label>Subject 1 Marks:</label>
    <input type="number"
           name="marks1"
           min="0"
           max="100"
           value="<?php echo htmlspecialchars($student["marks1"] ?? $marks1 ?? ""); ?>"
           required>

    <br><br>


    <label>Subject 2 Marks:</label>
    <input type="number"
           name="marks2"
           min="0"
           max="100"
           value="<?php echo htmlspecialchars($student["marks2"] ?? $marks2 ?? ""); ?>"
           required>

    <br><br>


    <label>Subject 3 Marks:</label>
    <input type="number"
           name="marks3"
           min="0"
           max="100"
           value="<?php echo htmlspecialchars($student["marks3"] ?? $marks3 ?? ""); ?>"
           required>

    <br><br>

    <button type="submit">Update Student</button>

</form>

</body>
</html>