<?php
session_start();
require_once 'conexao.php';

// VERIFICA SE O USUARIO TEM PERMISSAO (perfil 1 = administrador)
if ($_SESSION['perfil'] != 1) {
    echo "Acesso Negado!";
    exit;
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

// Processo do cadastro de usuário
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $id_perfil_form = $_POST['id_perfil'];

    // Verifica se o e-mail já está cadastrado
    $verificaEmail = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE email = :email");
    $verificaEmail->bindParam(':email', $email);
    $verificaEmail->execute();
    $emailExiste = $verificaEmail->fetchColumn();

    if ($emailExiste > 0) {
        echo "<script>alert('Este e-mail já está cadastrado. Por favor, use outro.');</script>";
    } else {
        $sql = "INSERT INTO usuario (nome, email, senha, id_perfil) VALUES (:nome, :email, :senha, :id_perfil)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':id_perfil', $id_perfil_form);

        if ($stmt->execute()) {
            echo "<script>alert('Usuário cadastrado com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar usuário!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuários</title>
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

    <h2>Cadastrar Usuário</h2>
    <form action="cadastro_usuario.php" method="POST">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <label for="id_perfil">Perfil:</label>
        <select id="id_perfil" name="id_perfil" required>
            <option value="1">Administrador</option>
            <option value="2">Secretária</option>
            <option value="3">Almoxarife</option>
            <option value="4">Cliente</option>
        </select>

        <button type="submit">Cadastrar</button>
        <button type="reset">Cancelar</button>
    </form>
    <a href="principal.php">Voltar</a>
</body>
</html>
