<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<section style="margin-top:3rem">
    <div class="form-section" style="max-width:760px;margin:0 auto">
        <h3><?= esc($title ?? 'Tableau de bord') ?></h3>
        <p class="td-muted" style="margin:0 0 1rem"><?= esc($roleDescription ?? '') ?></p>
        <div class="flash flash-info">
            <i class="bi bi-info-circle-fill"></i>
            <?= esc($roleTitle ?? 'Rôle') ?> connecté avec succès.
        </div>
        <p class="td-muted" style="margin:0">Cette page sert uniquement de point d’arrivée pour les comptes de test.</p>
    </div>
</section>

<?= $this->endSection() ?>