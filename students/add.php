<?php 
    session_start();

    if (!isset($_SESSION["admin_id"])) {
        header("Location: auth/login.php");
        exit();
    }

    require_once "../config/database.php";

    $error = "";
    $success = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $roll_no = trim($_POST["roll_no"]);
        $std_name = trim($_POST["std_name"]);
        $dept = trim($_POST["dept"]);
        $sem = $_POST["sem"];
        $email = trim($_POST["email"]);
        $marks1 = $_POST["marks1"];
        $marks2 = $_POST["marks2"];
        $marks3 = $_POST["marks3"];
        $marks = [$marks1, $marks2, $marks3];
        
        if (empty($roll_no) || empty($std_name) || empty($dept) || empty($sem) || empty($email)){
            $error = "All fields are required.";
        } elseif (!is_numeric($sem) || $sem < 1 || $sem > 8){
            $error = "Semester should be between 1 - 8.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email address.";
        } else {
            foreach ($marks as $mark) {
                if (!is_numeric($mark) || $mark < 0 || $mark > 100) {
                    $error = "Marks must be between 0 and 100.";
                    break;
                }
            } 
        }
        if (empty($error)) {
            $sql = "SELECT id FROM students WHERE roll_no = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $roll_no);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = "Roll number already exists.";
            }

            $stmt->close();
        }

        if (empty($error)) {
            function calculateTotal($marks){
                return array_sum($marks);
            }

            function calculateAverage($marks){
                return array_sum($marks) / count($marks);
            }

            function calculateGrade($average){
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

            $total = calculateTotal($marks);
            $average = calculateAverage($marks);
            $grade = calculateGrade($average);

            $sql = "INSERT INTO students
                    (roll_no, name, department, semester, email, marks1, marks2, marks3)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "sssisiii",
                $roll_no,
                $std_name,
                $dept,
                $sem,
                $email,
                $marks1,
                $marks2,
                $marks3
            );

            if ($stmt->execute()) {
                $file = "../files/students.txt";

                $data = "Roll Number: " . $roll_no . "\n";
                $data .= "Name: " . $std_name . "\n";
                $data .= "Department: " . $dept . "\n";
                $data .= "Semester: " . $sem . "\n";
                $data .= "Email: " . $email . "\n";
                $data .= "Marks: " . implode(", ", $marks) . "\n";
                $data .= "Total: " . $total . "\n";
                $data .= "Average: " . number_format($average, 2) . "\n";
                $data .= "Grade: " . $grade . "\n";
                $data .= "----------------------------------------\n";

                if (file_put_contents($file, $data, FILE_APPEND) !== false) {

                    $success = "Student added successfully.";

                } else {

                    $error = "Student was added to database, but writing to file failed.";
                }

            } else {

                $error = "Failed to add student to database.";
            }

            $stmt->close();
        }
    }
?>

<!DOCTYPE html>
    <head>
        <title>Add Student</title>
    </head>
    <body>
        <h2>Add student</h2>
        <form action="add.php" method="POST">
            <label>Roll No: </label>
            <input
            type="number"
            name="roll_no"
            min="1"
            required>

            <br><br>

            <label>Name: </label>
            <input
            type="text"
            name="std_name"
            required>

            <br><br>

            <label>Department: </label>
            <input
            type="text"
            name="dept"
            required>

            <br><br>

            <label>Semester: </label>
            <input
            type="number"
            name="sem"
            min="1"
            max="8"
            required>

            <br><br>

            <label>Email: </label>
            <input
            type="email"
            name="email"
            required>

            <br><br>

            <label>Subject 1 marks: </label>
            <input
            type="number"
            name="marks1"
            min="0"
            max="100"
            required>

            <br><br>

            <label>Subject 2 marks: </label>
            <input
            type="number"
            name="marks2"
            min="0"
            max="100"
            required>

            <br><br>

            <label>Subject 3 marks: </label>
            <input
            type="number"
            name="marks3"
            min="0"
            max="100"
            required>

            <br><br><br>

            <button type="submit">Add Student</button>
        </form>

        <?php if (!empty($error)): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
    <body>
</html>