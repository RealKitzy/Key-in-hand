<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["username"];
    $senha = $_POST["password"];

    if ($usuario == "admin" && $senha == "123456") {
        $_SESSION["usuario"] = $usuario;
        header("Location: painel.php");
        exit();
    } else {
        header("Location: index.html?erro=1");
        exit();
    }
} else {
    header("Location: index.html");
    exit();
}
?>