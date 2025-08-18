<?php
session_start();
require_once 'conexao.php';

//verifica se usuario tem permissao de adm
if($_SESSION['perfil']!=1){
    echo "<script>alert('Acesso Negado!'); window.location.href='principal.php';</script>";
    exit();
}

// OBTENDO O NOME DO PERFIL DO USUARIO LOGADO
$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil', $id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
$nome_perfil = $perfil['nome_perfil'];

// DEFINIÇÃO DAS PERMISSÕES POR PERFIL
$permissoes = [
    1 => ["Cadastrar"=>["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php","cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],
    
    "Buscar"=>["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
    
    "Alterar"=>["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
    
    "Excluir"=>["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]],
    
    2 => ["Cadastrar"=>["cadastro_cliente.php"],
    
    "Buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
    
    "Alterar"=>["alterar_fornecedor.php", "alterar_produto.php"],
    
    "Excluir"=>["excluir_produto.php"]],
    
    3 => ["Cadastrar"=>["cadastro_fornecedor.php", "cadastro_produto.php"],
    
    "Buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
    
    "Alterar"=>["alterar_fornecedor.php", "alterar_produto.php"],
    
    "Excluir"=>["excluir_produto.php"]],
    
    4 => ["Cadastrar"=>["cadastro_cliente.php"],
    
    "Buscar"=>["buscar_produto.php"],
    
    "Alterar"=>["alterar_cliente.php"]],
];

// obtém as opções disponíveis para o perfil
$opcoes_menu = $permissoes[$id_perfil];

//inicializa variavel para armazenar usuarios
$usuarios = [];

//busca todos os usuarios cadastrados em ordem alfabetica
$sql = "SELECT * FROM usuario ORDER BY nome ASC";
$stmt=$pdo->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

//se um id for passado via get exclui usuario
if (isset($_GET['id']) && is_numeric($_GET['id'])) {

    $id_usuario = $_GET['id'];

    //exclui o usuario do banco 
    $sql="DELETE FROM usuario WHERE id_usuario = :id";
    $stmt=$pdo->prepare($sql);
    $stmt->bindParam(':id',$id_usuario,PDO::PARAM_INT);

    if($stmt->execute()){
        echo "<script>alert('Usuário excluído com sucesso!'); window.location.href='excluir_usuario.php';</script>";
    }else{
        echo "<script>alert('Erro ao excluir usuário!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Usuário</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Menu do sistema -->
    <nav>
        <ul class="menu">
            <?php foreach($opcoes_menu as $categoria => $arquivos): ?>
                <li class="dropdown">
                    <a href="#"><?= $categoria ?></a>
                    <ul class="dropdown-menu">
                        <?php foreach($arquivos as $arquivo): ?>
                            <li><a href="<?= $arquivo ?>"><?= ucfirst(str_replace("_", " ", basename($arquivo, ".php"))) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <h2>Excluir Usuário</h2>
    <?php if(!empty($usuarios)): ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Perfil</th>
                <th>Ações</th>
            </tr>
            <?php foreach($usuarios as $usuario): ?>
                <tr>
                    <td><?=htmlspecialchars($usuario['id_usuario'])?></td>
                    <td><?=htmlspecialchars($usuario['nome'])?></td>
                    <td><?=htmlspecialchars($usuario['email'])?></td>
                    <td><?=htmlspecialchars($usuario['id_perfil'])?></td>
                    <td>
                        <a href="excluir_usuario.php?id=<?=htmlspecialchars($usuario['id_usuario'])?>" onclick="return confirm('Tem certeza que deseja excluir esse usuário?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?> 
        </table>
    <?php else: ?>
        <p>Nenhum usuário encontrado</p>
    <?php endif; ?>
    
    <a href="principal.php">Voltar</a>
</body>
</html>
