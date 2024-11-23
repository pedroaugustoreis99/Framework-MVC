<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir perfil</title>
</head>
<body>
    <form action="/exclusao-confirmada" method="POST">
        <label for="senha">Para excluir seu perfil, confirme sua senha</label>
        <input type="password" name="senha" id="senha">

        <?php if (isset($erro_de_validacao)): ?>
            <span style='color:red'><?= $erro_de_validacao ?></span>
        <?php endif; ?>

        <button 
            type="submit" 
            style="
                background-color: #ff0000; 
                color: white; 
                border: none; 
                padding: 10px 20px; 
                font-size: 16px; 
                border-radius: 5px; 
                cursor: pointer;
                transition: background-color 0.3s ease;
            " 
            onmouseover="this.style.backgroundColor='#cc0000';" 
            onmouseout="this.style.backgroundColor='#ff0000';">
            Excluir Perfil
        </button>
    </form>
</body>
</html>