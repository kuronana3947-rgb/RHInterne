<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <title>Document</title>
</head>
<body>
    <section id="page-login">
    <div class="auth-page geo-bg">
    <div class="auth-split">

    <!-- Panneau gauche -->
    <div class="auth-left">
        <div>
        <p class="auth-left-brand">TechMada RH<span>Gestion des congés</span></p>
        <p class="auth-left-text" style="margin-top:2rem">
            <strong>Bienvenue sur votre espace RH.</strong>
            Gérez vos demandes de congés, consultez votre solde et suivez l'état de vos demandes en temps réel.
        </p>
        </div>
        <div class="auth-roles">
        <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,.25);margin-bottom:4px">Comptes de démonstration</div>
        <?php if (!empty($demoAccounts)) : ?>
            <?php foreach ($demoAccounts as $demoAccount) : ?>
                <div class="role-pill">
                    <i class="bi <?= esc((string) $demoAccount['icon']) ?>"></i>
                    <div>
                        <div class="role-pill-name"><?= esc((string) $demoAccount['label']) ?></div>
                        <div class="role-pill-cred"><?= esc((string) $demoAccount['email']) ?> · <?= esc((string) $demoAccount['password']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="role-pill">
                <i class="bi bi-person"></i>
                <div><div class="role-pill-name">Aucun compte de démonstration</div><div class="role-pill-cred">Ajoutez des données de test dans la base.</div></div>
            </div>
        <?php endif; ?>
        </div>
    </div>

    <!-- Panneau droit -->
    <div class="auth-right">
        <p class="auth-title">Connexion</p>
        <p class="auth-sub">Entrez vos identifiants pour accéder à votre espace.</p>

        <?php $loginError = session()->getFlashdata('error'); ?>

        <?php if (!empty($loginError)) : ?>
        <div class="flash flash-error">
        <i class="bi bi-exclamation-circle-fill"></i>
        <?= esc(is_string($loginError) ? $loginError : '') ?>
        </div>
        <?php endif; ?>

        <form action="/auth/login" method="post">
        <?= csrf_field() ?>
        <div class="f-group">
            <label class="f-label">Adresse email</label>
            <input type="email" class="f-input" name="email" placeholder="vous@techmada.mg" value="employe@techmada.mg" required/>
        </div>
        <div class="f-group">
            <label class="f-label">Mot de passe</label>
            <input type="password" class="f-input" name="password" placeholder="••••••••" value="emp123" required/>
        </div>
        <button type="submit" class="btn-primary" style="margin-top:.5rem">
            Se connecter <i class="bi bi-arrow-right-short"></i>
        </button>
        </form>
    </div>

    </div>
    </div>
    </section>
</body>
</html>