<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/database.php";

function calculateTotal($marks)
{
    return array_sum($marks);
}

function calculateAverage($marks)
{
    return array_sum($marks) / count($marks);
}

function calculateGrade($average)
{
    if ($average >= 90) {
        return "S";
    } elseif ($average >= 80) {
        return "A";
    } elseif ($average >= 70) {
        return "B";
    } elseif ($average >= 60) {
        return "C";
    } elseif ($average >= 50) {
        return "D";
    } else {
        return "F";
    }
}

$sql = "SELECT * FROM students";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Students</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<h1>Student Records</h1>

<a href="../dashboard.php">Back to Dashboard</a>

<br><br>

<?php if ($result && $result->num_rows > 0): ?>

<table border="1" cellpadding="10">

    <tr>
        <th>Roll Number</th>
        <th>Name</th>
        <th>Department</th>
        <th>Semester</th>
        <th>Email</th>
        <th>Subject 1</th>
        <th>Subject 2</th>
        <th>Subject 3</th>
        <th>Total</th>
        <th>Average</th>
        <th>Grade</th>
        <th>Actions</th>
    </tr>

    <?php while ($student = $result->fetch_assoc()): ?>

        <?php
        // Store marks in an array
        $marks = [
            $student["marks1"],
            $student["marks2"],
            $student["marks3"]
        ];

        // Calculate results
        $total = calculateTotal($marks);
        $average = calculateAverage($marks);
        $grade = calculateGrade($average);
        ?>

        <tr>
            <td><?php echo htmlspecialchars($student["roll_no"]); ?></td>

            <td><?php echo htmlspecialchars($student["name"]); ?></td>

            <td><?php echo htmlspecialchars($student["department"]); ?></td>

            <td><?php echo $student["semester"]; ?></td>

            <td><?php echo htmlspecialchars($student["email"]); ?></td>

            <td><?php echo $student["marks1"]; ?></td>

            <td><?php echo $student["marks2"]; ?></td>

            <td><?php echo $student["marks3"]; ?></td>

            <td><?php echo $total; ?></td>

            <td><?php echo number_format($average, 2); ?></td>

            <td><?php echo $grade; ?></td>

            <td>
                <a href="edit.php?id=<?php echo $student["id"]; ?>">
                    Edit
                </a>

                |

                <a href="delete.php?id=<?php echo $student["id"]; ?>"
                   onclick="return confirm('Are you sure you want to delete this student?');">
                    Delete
                </a>
            </td>
        </tr>

    <?php endwhile; ?>

</table>

<?php else: ?>

    <p>No student records found.</p>

<?php endif; ?>

</body>
</html>