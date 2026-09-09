<?php 
    session_start();

    if (!isset($_SESSION["admin_id"])) {
        header("Location: auth/login.php");
        exit();
    }

    $username = $_SESSION["username"];
?>

<!DOCTYPE html>
    <head>
        <title>Student Management System</title>
    </head>
    <body>
        <h2>Student Management System<h2>
        <h3>Welcome, <?php echo htmlspecialchars($username); ?>!</h3>
        <br>
        <div className="dashboard-btns">
            <a href="students/add.php">[ Add Student ]</a>
            <br>
            <a href="students/view.php">[ View Students ]</a>
            <br>
            <a href="read_file.php">[ Read Student File ]</a>
            <br>
            <a href="auth/logout.php" className="logout-btn">[ Logout ]</a>
        <div>
    </body>
</html>