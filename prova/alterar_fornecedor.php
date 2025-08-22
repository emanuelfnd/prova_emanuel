<?php
session_start();
require_once 'conexao.php';

// verifica se é adm
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso Negado!'); window.location.href='index.php';</script>";
    exit();
}

$fornecedor = null;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!empty($_POST['busca_fornecedor'])) {
        $busca = trim($_POST['busca_fornecedor']);

        // busca por numero ou nome
        if (is_numeric($busca)) {
            $sql = "SELECT * FROM fornecedor WHERE id_fornecedor = :busca";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":busca", $busca, PDO::PARAM_INT);
        } else {
            $sql = "SELECT * FROM fornecedor WHERE nome_fornecedor LIKE :busca_nome";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":busca_nome", "$busca%", PDO::PARAM_STR);
        }
        $stmt->execute();
        $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);
        //valida se nn tem simbolos no campo nome

        if (!$fornecedor) {
            echo "<script>alert('Fornecedor não encontrado!');</script>";
        }
    }
}

// obtendo nome do fornecedors
$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil', $id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
$nome_perfil = $perfil['nome_perfil'];

 // Permissoes
 $permissoes = [
    // permissoes adm
    1 => ["Cadastrar"=>["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],
          "Buscar"=>["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
          "Alterar"=>["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
          "Excluir"=>["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]],
    //permissoes secretaria
    2 => ["Cadastrar"=>["cadastro_cliente.php"],
          "Buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
          "Alterar"=>["alterar_fornecedor.php", "alterar_produto.php"],
          "Excluir"=>["excluir_produto.php"]],
    // permissoes almoxarife
    3 => ["Cadastrar"=>["cadastro_fornecedor.php", "cadastro_produto.php"],
          "Buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
          "Alterar"=>["alterar_fornecedor.php", "alterar_produto.php"],
          "Excluir"=>["excluir_produto.php"]],
    // permissoes cliente
    4 => ["Cadastrar"=>["cadastro_cliente.php"],
          "Buscar"=>["buscar_cliente.php"],
          "Alterar"=>["alterar_cliente.php"]],
];

//informaçoes do usuario logado
$opcoes_menu = $permissoes["$id_perfil"];


?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Fornecedor</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts.js"></script>
</head>
<body>
<nav>
        <ul class="menu">
            <?php foreach($opcoes_menu as $categoria => $arquivos) { ?>
                <li class="dropdown">
                    <a href="#"><?= $categoria ?></a>

                    <ul class="dropdown-menu">
                        <?php foreach($arquivos as $arquivo) { ?>
                            <li>   
                                <a href="<?= $arquivo ?>"><?= ucfirst(str_replace("_", " ", basename($arquivo, ".php"))) ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </nav>
    <h2>Alterar Fornecedor</h2>
    
    <form action="alterar_fornecedor.php" method="POST">
        <label for="busca_fornecedor">Digite o ID ou NOME do Fornecedor:</label>
        <input type="text" name="busca_fornecedor" required onkeyup= "BuscarSugestoes()">
     

        <div id="sugestoes"></div>
        <button type="submit">Buscar</button>
    </form>

    <?php if ($fornecedor): ?>
        <form action="processa_alteracao_fornecedor.php" method="POST">
            <input type="hidden" name="id_fornecedor" value="<?=htmlspecialchars($fornecedor['id_fornecedor'])?>">
           
           
            <label for="nome_fornecedor">Nome:</label>
            <input type="nome_fornecedor" name="nome_fornecedor" value="<?=htmlspecialchars($fornecedor['nome_fornecedor'])?>"required>
            <br>
            <label for="telefone">Telefone:</label>
            <input type="tel" id="telefone" name="telefone" value="<?=htmlspecialchars($fornecedor['telefone'])?>" required>
            <br>
            <label for="endereco">Endereço:</label>
            <input type="endereco" name="endereco" value="<?=htmlspecialchars($fornecedor['endereco'])?>" required>
            <br>
            <label for="email">E-mail:</label>
            <input type="email" name="email" value="<?=htmlspecialchars($fornecedor['email'])?>" required>
            <br>
            <label for="contato">Contato:</label>
            <input type="contato" name="contato" value="<?=htmlspecialchars($fornecedor['contato'])?>" required>
      
    

    
        
        <button type="submit">Alterar</button>
        <button type="reset">Cancelar</button>
    </form>
    <!-- Máscaras de entrada -->
  <script src="https://cdn.jsdelivr.net/npm/inputmask/dist/inputmask.min.js"></script>
  <script>
    Inputmask({ mask: "(99) 99999-9999" }).mask("#telefone");
  </script>

    <?php endif; ?>
    <a href="principal.php">Voltar</a>
    <h4>emanuel fernandes</h4>
</body>
</html>