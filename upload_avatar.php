<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "t3";
$db_port = 3308;
$user_id = $_SESSION['user_id'];

$msg = "";
$avatar_file = "";

if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    $maxSize = 500 * 1024;
    $requiredWidth = 300;
    $requiredHeight = 300;

    if ($file['error'] == 0) {
        if ($file['size'] > $maxSize) {
            $msg = "Imagem muito pesada! (máx 500KB)";
        } else {
            $imgInfo = getimagesize($file['tmp_name']);
            if (!$imgInfo) {
                $msg = "Arquivo não é uma imagem válida!";
            } else {
                list($width, $height) = $imgInfo;
                if ($width != $requiredWidth || $height != $requiredHeight) {
                    $msg = "A imagem deve ser exatamente 300x300 pixels!";
                } else {
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $filename = uniqid('avatar_', true) . '.' . $ext;
                        $path = "uploads/" . $filename;
                        if (move_uploaded_file($file['tmp_name'], $path)) {
                            $conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
                            if ($conn->connect_errno) {
                                $msg = "Erro ao conectar ao banco.";
                            } else {
                                $res = $conn->query("SELECT avatar FROM user WHERE id=$user_id");
                                if ($res && $row = $res->fetch_assoc()) {
                                    if ($row['avatar'] && file_exists("uploads/" . $row['avatar'])) {
                                        unlink("uploads/" . $row['avatar']);
                                    }
                                }
                                $stmt = $conn->prepare("UPDATE user SET avatar=? WHERE id=?");
                                $stmt->bind_param("si", $filename, $user_id);
                                if ($stmt->execute()) {
                                    $msg = "Avatar atualizado com sucesso!";
                                    $avatar_file = $filename;
                                } else {
                                    $msg = "Erro ao atualizar banco.";
                                    unlink($path);
                                }
                                $stmt->close();
                                $conn->close();
                            }
                        } else {
                            $msg = "Erro ao salvar arquivo.";
                        }
                    } else {
                        $msg = "Formato de imagem inválido!";
                    }
                }
            }
        }
    } else {
        $msg = "Nenhum arquivo selecionado.";
    }
}

$params = "msg=" . urlencode($msg);
header("Location: avatar.html?$params");
exit;