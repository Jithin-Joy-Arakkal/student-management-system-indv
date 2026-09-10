<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: auth/login.php");
    exit();
}

$file = "files/students.txt";

?>

<!DOCTYPE html>
<html>

<head>
    <title>Read Student File</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<h1>Student Details File</h1>

<a href="dashboard.php">Back to Dashboard</a>

<br><br>

<?php if (file_exists($file)): ?>

    <?php if (filesize($file) > 0): ?>

        <a href="files/students.txt" download>
            Download Text File
        </a>

        <br><br>

        <pre><?php echo htmlspecialchars(file_get_contents($file)); ?></pre>

    <?php else: ?>

        <p>No student details have been stored yet.</p>

    <?php endif; ?>

<?php else: ?>

    <p>Student details file does not exist.</p>

<?php endif; ?>

</body>
</html>