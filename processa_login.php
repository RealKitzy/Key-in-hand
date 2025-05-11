<?php 

if ($_SERVER["REQUEST_METHOD"]== "POST") {
    $usuario = $_POST["usuario"];
    $senha = $_POST["senha"];

    if ($usuario == "admin" && $senha == "123456") {
        session_start();
        $_SESSION["usuario"] = $usuario;
        header("Location: painel.php");
        exit();
    } else {
        echo "<script>alert('Usuário ou senha inválidos!');</script>";
    }

    $usuario_valido = "usuario123";
    $senha_valida = "senha456";

    if ($username === $usuario_valido && $password === $senha_valida) {
        // Autenticação bem-sucedida
        $_SESSION["username"] = $username; // Definir a sessão do usuário
        header("Location: paginainicial.html"); // Redirecionar para a página inicial
        exit();
    } else {
        // Falha na autenticação
        $erro = "Usuário ou senha incorretos.";
    }
}
