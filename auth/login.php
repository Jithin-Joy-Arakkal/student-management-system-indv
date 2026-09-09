<?php
    session_start();

    require_once "../config/database.php";

    $error = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $username = $_POST["username"];
        $password = $_POST["password"];

        if (empty($username) || empty($password)){
            $error = "Username and password are required.";
        } else {
            $sql = "SELECT id, username, password
                    FROM admins
                    WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows == 1){
                $admin = $result->fetch_assoc();

                if (password_verify($password, $admin["password"])){
                    $_SESSION["admin_id"] = $admin["id"];
                    $_SESSION["username"] = $admin["username"];

                    if (isset($_POST["remember"])){
                        setcookie(
                            "username",
                            $admin["username"],
                            time()+ (86400 * 30),
                            "/"
                        );
                    }

                    header("Location: ../dashboard.php");
                    exit();
                
                } else {
                    $error = "Invalid username or password.";
                }

            } else {
                $error = "Invalid username or password.";
            }
            $stmt->close();
        }
    }
?>

<html>
    <head>
        <title>Login</title>
    </head>
    <body>
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label>Username: </label>
            <input type="text" name="username">

            <br><br>

            <label>Password: </label>
            <input type="password" name="password">

            <br><br><br>

            <label>Remember Me </label>
            <input type="checkbox" name="remember">

            <br><br>

            <button type="submit" name="login-btn">Login</button>
        </form>

        <?php if (!empty($error)): ?>
        <p><?php echo $error; ?></p>
        <?php endif; ?>
    <body>
</html>