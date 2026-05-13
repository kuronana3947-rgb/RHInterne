<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (isset($title)) { ?>
        <title><?= esc($title) ?></title>
    <?php } else { ?>
        <title>Document</title>
    <?php } ?>
    <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <head></head>

    <main class="container main-content">
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="site-footer">
    </footer>
</body>
</html>