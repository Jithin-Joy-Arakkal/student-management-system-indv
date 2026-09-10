<?php

$host = "localhost";
$username = "root";
$password = "";
$db = "student_management";

try {

    $conn = new mysqli(
        $host,
        $username,
        $password,
        $db
    );

    if ($conn->connect_error) {
        throw new Exception(
            "Database connection failed."
        );
    }

} catch (Exception $e) {

    echo "Error: " . $e->getMessage();

}

?>