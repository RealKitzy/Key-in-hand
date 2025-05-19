<?php
session_start();

class UserLogin {
    private $conn;

    public function __construct() {
        $servername = "localhost";
        $username_db = "root";
        $password_db = "";
        $dbname = "t3";
        $port = 3308;

        $this->conn = new mysqli($servername, $username_db, $password_db, $dbname, $port);

        if ($this->conn->connect_error) {
            die("Erro na conexão com o banco de dados: " . $this->conn->connect_error);
        }
    }

    public function __destruct() {
        $this->conn->close();
    }

    public function login($email, $senha) {
        error_log("[LOGIN DEBUG] Tentativa de login com email: " . $email);

        $sql = "SELECT id, email, password FROM user WHERE email = ?";
        $stmt = $this->conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();

                error_log("[LOGIN DEBUG] Senha hasheada do banco: " . $row["password"]);
                $senha_correta = password_verify($senha, $row["password"]);
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
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_digitado = $_POST["username"] ?? '';
    $senha_digitada = $_POST["password"] ?? '';

    $login = new UserLogin();
    $login->login($email_digitado, $senha_digitada);
} else {
    header("Location: index.html");
   exit();
}
?>