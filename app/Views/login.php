<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <form  action="/login" method="post">
        <?= csrf_field() ?>
        <div class="form-row">
            <label class="field-label" for="username">Nom d'utilisateur</label>
            <input class="input" type="text" id="username" name="username" placeholder="Ex: alice" required>
        </div>

        <div class="form-row">
            <label class="field-label" for="password">Mot de passe</label>
            <input class="input" type="password" id="password" name="password" placeholder="Votre mot de passe" required>
        </div>

        <button class="btn btn-primary btn-login" type="submit">Se connecter</button>
    </form>
</body>
</html>