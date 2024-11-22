<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?></title>
</head>
<body>
    <form action="/login-submit" method="post">
        <label for="usuario">Usuário</label>
        <input type="text" name="usuario" id="usuario">
        <br>

        <label for="senha">Senha</label>
        <input type="password" name="senha" id="senha">
        <br>

        <?php if (isset($erros_de_validacao)): ?>
            <span style="color: red; font-weight: bolder"><?= $erros_de_validacao ?></span>
            <br>
        <?php endif; ?>

        <input type="submit" value="Login">
    </form>
</body>
</html>