<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?></title>
</head>
<body>
    <form action="/atualizar-perfil" method="post">
        <label for="senha_atual">Digite a sua senha atual: </label>
        <input type="password" name="senha_atual" id="senha_atual">
        <?php if (isset($erros_de_validacao['senha_atual'])): ?>
            <span style="color: red; font-weight: bolder"><?= $erros_de_validacao['senha_atual'] ?></span>
        <?php endif; ?>
        <br> <br>

        <label for="usuario">usuario</label>
        <input type="text" name="usuario" id="usuario" value="<?= $usuario ?>">
        <?php if (isset($erros_de_validacao['usuario'])): ?>
            <span style="color: red; font-weight: bolder"><?= $erros_de_validacao['usuario'] ?></span>
        <?php endif; ?>
        <br>

        <label for="senha">nova senha: </label>
        <input type="password" name="senha" id="senha">
        <?php if (isset($erros_de_validacao['senha'])): ?>
            <span style="color: red; font-weight: bolder"><?= $erros_de_validacao['senha'] ?></span>
        <?php endif; ?>
        <br>

        <label for="confirmar_senha">confirme a nova senha</label>
        <input type="password" name="confirmar_senha" id="confirmar_senha">
        <?php if (isset($erros_de_validacao['confirmar_senha'])): ?>
            <span style="color: red; font-weight: bolder"><?= $erros_de_validacao['confirmar_senha'] ?></span>
        <?php endif; ?>
        <br>

        <input type="submit" value="atualizar">
    </form>
    <br>
    <br>

    <p>Para excluir o seu perfil <a href="/excluir-perfil"><span style="color: red">clique aqui</span></a></p>
</body>
</html>