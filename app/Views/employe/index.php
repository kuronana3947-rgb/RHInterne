<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<?php
$employe = $employe ?? [
  'nom' => 'Employé',
  'prenom' => '',
  'role' => 'employe',
  'departement' => '',
  'initiales' => 'E',
];
$demandes = $demandes ?? [];
$statutsOptions = $statutsOptions ?? [
  'en_attente' => 'En attente',
  'approuve' => 'Approuvée',
  'refuse' => 'Refusée',
  'annule' => 'Annulée',
];
$selectedStatut = $selectedStatut ?? '';
$flashSuccess = $flashSuccess ?? null;
$flashError = $flashError ?? null;
?>

<section id="page-mes-conges" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="/employe/dashboard"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="/employe/conge/create"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li><a href="/employe/conges" class="active"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
      <li><a href="/employe/dashboard#page-profil-employe"><i class="bi bi-person"></i> Mon profil</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-green"><?= esc((string) $employe['initiales']) ?></div>
        <div><div class="user-name"><?= esc(trim((string) ($employe['prenom'] ?? '') . ' ' . (string) ($employe['nom'] ?? 'Employé'))) ?></div><div class="user-role">Employé<?= !empty($employe['departement']) ? ' · ' . esc((string) $employe['departement']) : '' ?></div></div>
        <a href="/logout" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Mes demandes de congé</div>
        <div class="topbar-breadcrumb"><a href="/employe/dashboard">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Mes demandes</div>
      </div>
      <div class="topbar-actions">
        <a href="/employe/conge/create" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-plus-lg"></i> Nouvelle demande</a>
      </div>
    </div>

    <div class="content">
      <?php if (!empty($flashSuccess)) : ?>
        <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i><?= esc((string) $flashSuccess) ?></div>
      <?php endif; ?>
      <?php if (!empty($flashError)) : ?>
        <div class="flash flash-error"><i class="bi bi-exclamation-circle-fill"></i><?= esc((string) $flashError) ?></div>
      <?php endif; ?>

      <div class="data-card">
        <div class="data-card-head">
          <h3>Toutes mes demandes</h3>
          <div style="display:flex;gap:6px">
            <form method="get" action="/employe/conges" style="margin:0">
              <select class="f-select" name="statut" onchange="this.form.submit()" style="font-size:.8rem;padding:6px 10px;width:auto">
                <option value="" <?= $selectedStatut === '' ? 'selected' : '' ?>>Tous les statuts</option>
                <?php foreach ($statutsOptions as $key => $label) : ?>
                  <option value="<?= esc($key) ?>" <?= $selectedStatut === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach; ?>
              </select>
            </form>
          </div>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Type</th><th>Début</th><th>Fin</th><th>Durée</th><th>Statut</th><th>Commentaire RH</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php if (!empty($demandes)) : ?>
              <?php foreach ($demandes as $demande) : ?>
                <tr>
                  <td><span class="type-badge <?= esc((string) ($demande['type_class'] ?? 't-annuel')) ?>"><?= esc((string) ($demande['type_label'] ?? 'Annuel')) ?></span></td>
                  <td class="td-muted"><?= esc(!empty($demande['date_debut']) ? date('d M Y', strtotime((string) $demande['date_debut'])) : '0') ?></td>
                  <td class="td-muted"><?= esc(!empty($demande['date_fin']) ? date('d M Y', strtotime((string) $demande['date_fin'])) : '0') ?></td>
                  <td class="td-mono"><?= esc((string) ($demande['nb_jours'] ?? 0)) ?> j</td>
                  <td><span class="statut <?= esc((string) ($demande['statut_class'] ?? 's-attente')) ?>"><?= esc((string) ($demande['statut_label'] ?? 'en attente')) ?></span></td>
                  <td class="td-muted" style="font-size:.78rem">
                    <?= !empty($demande['commentaire_rh']) ? esc((string) $demande['commentaire_rh']) : '—' ?>
                  </td>
                  <td>
                    <?php if (!empty($demande['can_cancel'])) : ?>
                      <form action="/employe/conges/cancel" method="post" style="display:inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="demande_id" value="<?= esc((string) ($demande['id'] ?? 0)) ?>">
                        <button class="btn-sm btn-cancel" type="submit"><i class="bi bi-x"></i> Annuler</button>
                      </form>
                    <?php else : ?>
                      <span class="td-muted" style="font-size:.75rem">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="7" class="td-muted" style="text-align:center;padding:1rem">Aucune demande enregistrée pour le moment.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc((string) date('Y')) ?> <span>TechMada RH</span></div>
  </div>

</div>
</section>

<?= $this->endSection() ?>
