<?php 
session_start();
require_once 'conexao.php';
require_once 'funcoes_email.php'; // arquivo com as funções que geram a senha e simulam envio

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // verifica se o email existe no banco
    $sql = "SELECT * FROM usuario WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // gera uma senha temporária e aleatória
        $senha_temporaria = gerarSenhaTemporaria();
        $senha_hash = password_hash($senha_temporaria, PASSWORD_DEFAULT);

        // atualiza a senha do usuário no banco
        $sql = "UPDATE usuario SET senha = :senha, senha_temporaria = TRUE WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':senha', $senha_hash);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // simula o envio do email gravando em txt
        simularEnvioEmail($email, $senha_temporaria);
        echo "<script>alert('Uma senha temporária foi gerada e enviada (simulação). Verifique o arquivo emails_simulador.txt'); window.location.href = 'login.php';</script>";
    } else {
        echo "<script>alert('E-mail não encontrado');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Recuperar Senha</h2>
    <form action="recuperar_senha.php" method="POST">
        <label for="email">Digite seu email cadastrado</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Enviar a senha temporária</button>
    </form>
</body>
</html>
