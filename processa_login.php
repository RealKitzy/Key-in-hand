<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["username"]; // Certifique-se de que o 'name' no HTML é 'username'
    $senha = $_POST["password"];   // Certifique-se de que o 'name' no HTML é 'password'

    if ($usuario == "admin" && $senha == "123456") {
        $_SESSION["usuario"] = $usuario; // Opcional: guardar o usuário na sessão
        header("Location: paginainicial.html"); // Redirecionar para paginainicial.html
        exit();
    } else {
        header("Location: index.html?erro=1"); // Redirecionar de volta com erro
        exit();
    }
} else {
    header("Location: index.html"); // Se alguém acessar diretamente
    exit();
}
?>