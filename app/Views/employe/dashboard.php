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
$annee = $annee ?? (int) date('Y');
$statuts = $statuts ?? [
  'en_attente' => 0,
  'approuve' => 0,
  'refuse' => 0,
];
$soldes = $soldes ?? [
  'annuel' => ['label' => 'Congé annuel', 'attribues' => 0, 'pris' => 0, 'restants' => 0, 'progress' => 0],
  'maladie' => ['label' => 'Congé maladie', 'attribues' => 0, 'pris' => 0, 'restants' => 0, 'progress' => 0],
  'special' => ['label' => 'Congé spécial', 'attribues' => 0, 'pris' => 0, 'restants' => 0, 'progress' => 0],
];
$demandes = $demandes ?? [];
$flashSuccess = $flashSuccess ?? null;
$joursAnnuels = $soldes['annuel']['attribues'] ?? 0;
$joursRestantsAnnuels = $soldes['annuel']['restants'] ?? 0;
$roleLabel = (($employe['role'] ?? 'employe') === 'employe') ? 'Employé' : ucfirst((string) ($employe['role'] ?? 'Employé'));
?>

<section id="page-dashboard-employe" style="margin-top:3rem">
<div class="app-wrap">

  <!-- SIDEBAR EMPLOYÉ -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
      <li><a href="#page-dashboard-employe" class="active"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="/employe/conge/create"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li>
        <a href="/employe/conges">
          <i class="bi bi-calendar3"></i> Mes demandes
          <span class="nav-badge alert"><?= esc((string) array_sum($statuts)) ?></span>
        </a>
      </li>
      <li><a href="#page-profil-employe"><i class="bi bi-person"></i> Mon profil</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-green"><?= esc((string) $employe['initiales']) ?></div>
        <div>
          <div class="user-name"><?= esc(trim((string) ($employe['prenom'] ?? '') . ' ' . (string) ($employe['nom'] ?? 'Employé'))) ?></div>
          <div class="user-role"><?= esc($roleLabel) ?><?= !empty($employe['departement']) ? ' · ' . esc((string) $employe['departement']) : '' ?></div>
        </div>
        <a href="/logout" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Tableau de bord</div>
        <div class="topbar-breadcrumb">Accueil</div>
      </div>
      <div class="topbar-actions">
        <a href="/employe/conge/create" class="btn-forest" style="padding:7px 14px;font-size:.82rem">
          <i class="bi bi-plus-lg"></i> Nouvelle demande
        </a>
      </div>
    </div>

    <div class="content">

      <!-- Flash succès -->
      <?php if (!empty($flashSuccess)) : ?>
      <div class="flash flash-success">
        <i class="bi bi-check-circle-fill"></i>
        <?= esc((string) $flashSuccess) ?>
      </div>
      <?php endif; ?>

      <!-- Métriques -->
      <div class="metrics">
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
          <div class="metric-val"><?= esc((string) ($statuts['en_attente'] ?? 0)) ?></div>
          <div class="metric-label">En attente</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div></div>
          <div class="metric-val"><?= esc((string) ($statuts['approuve'] ?? 0)) ?></div>
          <div class="metric-label">Approuvées</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-calendar-check"></i></div></div>
          <div class="metric-val"><?= esc((string) $joursRestantsAnnuels) ?></div>
          <div class="metric-label">Jours restants</div>
          <div class="metric-sub">sur <?= esc((string) $joursAnnuels) ?> cette année</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div>
          <div class="metric-val"><?= esc((string) ($statuts['refuse'] ?? 0)) ?></div>
          <div class="metric-label">Refusée</div>
        </div>
      </div>

      <!-- Soldes de congés -->
      <div class="data-card">
        <div class="data-card-head"><h3>Mes soldes de congés — <?= esc((string) $annee) ?></h3></div>
        <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem">
          <?php foreach (['annuel', 'maladie', 'special'] as $soldeKey) : ?>
            <?php $solde = $soldes[$soldeKey] ?? ['attribues' => 0, 'pris' => 0, 'restants' => 0, 'progress' => 0]; ?>
            <div class="solde-card" style="margin:0">
              <div class="solde-header">
                <span class="solde-type"><?= esc((string) ($solde['label'] ?? '')) ?></span>
                <span class="solde-nums"><strong><?= esc((string) ($solde['restants'] ?? 0)) ?></strong> / <?= esc((string) ($solde['attribues'] ?? 0)) ?> j</span>
              </div>
              <div class="solde-bar"><div class="solde-fill<?= $soldeKey === 'special' ? ' warn' : '' ?>" style="width:<?= esc((string) ($solde['progress'] ?? 0)) ?>%"></div></div>
              <div class="solde-label"><?= esc((string) ($solde['restants'] ?? 0)) ?> jour(s) restant(s) · <?= esc((string) ($solde['pris'] ?? 0)) ?> pris</div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Dernières demandes -->
      <div class="data-card">
        <div class="data-card-head">
          <h3>Mes dernières demandes</h3>
          <a href="/employe/conges" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout →</a>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Type</th><th>Du</th><th>Au</th><th>Durée</th><th>Statut</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php if (!empty($demandes)) : ?>
              <?php foreach ($demandes as $demande) : ?>
                <tr>
                  <td><span class="type-badge <?= esc((string) ($demande['type_class'] ?? 't-annuel')) ?>"><?= esc((string) ($demande['type_label'] ?? 'Annuel')) ?></span></td>
                  <td class="td-muted"><?= esc(!empty($demande['date_debut']) ? date('d/m/Y', strtotime((string) $demande['date_debut'])) : '0') ?></td>
                  <td class="td-muted"><?= esc(!empty($demande['date_fin']) ? date('d/m/Y', strtotime((string) $demande['date_fin'])) : '0') ?></td>
                  <td class="td-mono"><?= esc((string) ($demande['nb_jours'] ?? 0)) ?> j</td>
                  <td><span class="statut <?= esc((string) ($demande['statut_class'] ?? 's-attente')) ?>"><?= esc((string) ($demande['statut_label'] ?? 'en attente')) ?></span></td>
                  <td>
                    <?php if (!empty($demande['can_cancel'])) : ?>
                      <button class="btn-sm btn-cancel"><i class="bi bi-x"></i> Annuler</button>
                    <?php else : ?>
                      <span class="td-muted" style="font-size:.75rem">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="6" class="td-muted" style="text-align:center;padding:1rem">0 demande enregistrée pour le moment.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc((string) date('Y')) ?> <span>TechMada RH</span> — Projet CodeIgniter 4</div>
  </div>

</div>
</section>

<?= $this->endSection() ?>