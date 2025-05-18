<?php



// Display errors for debugging

ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);



if ($_SERVER["REQUEST_METHOD"] == "POST") {

 $name = $_POST["name"] ?? '';

 $email = $_POST["email"] ?? '';

 $password = $_POST["password"] ?? '';

 $confirm_password = $_POST["confirm_password"] ?? '';

 $birthdate = $_POST["birthdate"] ?? null;

 $city = $_POST["city"] ?? '';

 $avatar = $_POST["avatar"] ?? '';

 $banner = $_POST["banner"] ?? '';

 $token = $_POST["token"] ?? '';



 if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {

header("Location: criar.html?error=1");

 exit();

 }



 if ($password !== $confirm_password) {

 header("Location: criar.html?error=2");

 exit();

 }



$hashed_password = password_hash($password, PASSWORD_DEFAULT);



 $servername = "localhost";

 $username_db = "root";

 $password_db = "";

 $dbname = "t3";

 $port = 3308;





$conn = new mysqli($servername, $username_db, $password_db, $dbname, $port);



if ($conn->connect_error) {

 die("Database connection failed: " . $conn->connect_error);

 }



 $sql_check_email = "SELECT id FROM user WHERE email = ?";

$stmt_check_email = $conn->prepare($sql_check_email);

 if ($stmt_check_email) {

 $stmt_check_email->bind_param("s", $email);

 $stmt_check_email->execute();

 $result_check_email = $stmt_check_email->get_result();



 if ($result_check_email->num_rows > 0) {

 header("Location: index.html?error=3");

 $stmt_check_email->close();

 $conn->close();

 exit();

 }

 $stmt_check_email->close();

 } else {

 echo "Error preparing email check query: " . $conn->error;

 $conn->close();

 exit();

}

 $sql_insert = "INSERT INTO user (name, email, password, brithdate, city, avatar, banner, token) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
 $stmt_insert = $conn->prepare($sql_insert);

 if ($stmt_insert) {

 $stmt_insert->bind_param("ssssssss", $name, $email, $hashed_password, $birthdate, $city, $avatar, $banner, $token);



 if ($stmt_insert->execute()) {

 header("Location: register_success.html");

 } else {

 echo "Registration error: " . $stmt_insert->error;

 }

 $stmt_insert->close();

} else {

 echo "Error preparing insert query: " . $conn->error;

 }



$conn->close();



} else {

header("Location: criar.html");

exit();

}



?>