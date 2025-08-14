<?php
session_start();
require_once "conexao.php";

// VERIFICA SE O USUÁRIO TEM PERMISSÃO DE ADM
if ($_SESSION['perfil'] !=1) {
    echo "<script>alert('Acesso Negado!'); window.location.href='principal.php';</script>";
    exit();
}

//inicializa variaveis
$usuario = null;

if($_SERVER["REQUEST_METHOD"]=="POST"){
    if(!empty($_POST['busca_usuario'])){
        $busca = trim($_POST['busca_usuario']);

        //verifica se a busca é um numero (id) ou um nome
        if(is_numeric($busca)){
            $sql = "SELECT*FROM usuario WHERE id_usuario = :busca";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca',$busca,PDO::PARAM_INT);
        }else{
            $sql = "SELECT*FROM usuario WHERE nome LIKE :busca_nome";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca_nome',"%$busca%",PDO::PARAM_STR); 
        }

        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        //se o usuario nao for encontrado exibe um alerta
        if(!$usuario){
            echo "<script>alert('usuario nao encontrado!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar usuario</title>
    <link rel="stylesheet" href="styles.css">

<!--  certifica se o javascript esta carrgando correta mente-->
    <script>src="scripts.js"</script>
</head>
<body>
    <h2>Alterar usuario</h2>
    <form action="alterar_usuario.php" method="POST">
        <label for="busca_usuario">Disgite o id ou nome do usuario</label>
        <input type="text" name="busca_usuario" id="busca_usuario" required onkeyup="buscarSugestoes()">
        <button type="submit">Pesquisar</button>
    </form>
</body>
</html>