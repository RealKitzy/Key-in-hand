<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class UserRegistration {
    private $conn;

    public function __construct() {
        $servername = "localhost";
        $username_db = "root";
        $password_db = "";
        $dbname = "t3";
        $port = 3308;

        $this->conn = new mysqli($servername, $username_db, $password_db, $dbname, $port);

        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }
    }

    public function __destruct() {
        $this->conn->close();
    }

    public function register($name, $email, $password, $confirm_password, $birthdate, $city, $token) {
        if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
            header("Location: criar.html?error=1");
            exit();
        }

        if ($password !== $confirm_password) {
            header("Location: criar.html?error=2");
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if ($this->emailExists($email)) {
            header("Location: index.html?error=3");
            exit();
        }

        $this->insertUser($name, $email, $hashed_password, $birthdate, $city, $token);
    }

    private function emailExists($email) {
        $sql = "SELECT id FROM user WHERE email = ?";
        $stmt = $this->conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result->num_rows > 0;
        } else {
            echo "Error preparing email check query: " . $this->conn->error;
            return true;
        }
    }

    private function insertUser($name, $email, $hashed_password, $birthdate, $city, $token) {
        $sql = "INSERT INTO user (name, email, password, brithdate, city, token) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssssss", $name, $email, $hashed_password, $birthdate, $city, $token);

            if ($stmt->execute()) {
                header("Location: register_success.html");
            } else {
                echo "Registration error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error preparing insert query: " . $this->conn->error;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"] ?? '';
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';
    $confirm_password = $_POST["confirm_password"] ?? '';
    $birthdate = $_POST["birthdate"] ?? null;
    $city = $_POST["city"] ?? '';
    $token = $_POST["token"] ?? '';

    $registration = new UserRegistration();
    $registration->register($name, $email, $password, $confirm_password, $birthdate, $city, $token);
} else {
    header("Location: criar.html");
exit();
}
?>