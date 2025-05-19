<?php

session_start();



$servername = "localhost";

$username_db = "root";

$password_db = "";

$dbname = "t3";

$port = 3308;



if ($_SERVER["REQUEST_METHOD"] == "POST") {

$email_digitado = $_POST["username"];

$senha_digitada = $_POST["password"];



error_log("[LOGIN DEBUG] Tentativa de login com email: " . $email_digitado);



 $conn = new mysqli($servername, $username_db, $password_db, $dbname, $port);



 if ($conn->connect_error) {

 die("Erro na conexão com o banco de dados: " . $conn->connect_error);

 }



$sql = "SELECT id, email, password FROM user WHERE email = ?";

 $stmt = $conn->prepare($sql);



if ($stmt) {

$stmt->bind_param("s", $email_digitado);

 $stmt->execute();

 $result = $stmt->get_result();



 if ($result->num_rows == 1) {

 $row = $result->fetch_assoc();

error_log("[LOGIN DEBUG] Senha hasheada do banco: " . $row["password"]);

 $senha_correta = password_verify($senha_digitada, $row["password"]);

 error_log("[LOGIN DEBUG] Resultado da verificação da senha: " . ($senha_correta ? 'true' : 'false'));



 if ($senha_correta) {

 $_SESSION["user_id"] = $row["id"];

 $_SESSION["username"] = $row["email"];

 header("Location: paginainicial.html");

 exit();

 } else {

header("Location: index.html?error=1");

exit();

 }

 } else {

 header("Location: index.html?error=1");

 exit();

}

 $stmt->close();

 } else {

header("Location: index.html?error=2");

 exit();

 }

 $conn->close();

} else {

 header("Location: index.html");

 exit();

}

?>