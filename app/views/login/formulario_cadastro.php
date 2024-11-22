<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?></title>
</head>
<body>
    <form action="/cadastrar-usuario-submit" method="post">
        <label for="usuario">Usuário</label>
        <input type="text" name="usuario" id="usuario">
        <?php if (isset($erros_de_validacao['usuario'])): ?>
            <span style="color: red; font-weight: bolder"><?= $erros_de_validacao['usuario'] ?></span>
        <?php endif; ?>
        <br>

        <label for="senha">Senha</label>
        <input type="password" name="senha" id="senha">
        <?php if (isset($erros_de_validacao['senha'])): ?>
            <span style="color: red; font-weight: bolder"><?= $erros_de_validacao['senha'] ?></span>
        <?php endif; ?>
        <br>

        <label for="confirmar_senha">Confirme a senha</label>
        <input type="password" name="confirmar_senha" id="confirmar_senha">
        <?php if (isset($erros_de_validacao['confirmar_senha'])): ?>
            <span style="color: red; font-weight: bolder"><?= $erros_de_validacao['confirmar_senha'] ?></span>
        <?php endif; ?>
        <br>
        
        <input type="submit" value="cadastrar">
    </form>
</body>
</html>