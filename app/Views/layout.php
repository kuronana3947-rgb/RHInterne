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
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
    <!-- <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/> -->
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