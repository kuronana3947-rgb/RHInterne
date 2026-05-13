<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<?php
$currentUser = $currentUser ?? [
  'nom' => 'Responsable RH',
  'prenom' => '',
  'departement' => '',
  'initiales' => 'RH',
];
$demandes = $demandes ?? [];
$departments = $departments ?? [];
$statusCounts = $statusCounts ?? ['tous' => 0, 'attente' => 0, 'approuvees' => 0, 'refusees' => 0];
$pendingCount = $pendingCount ?? 0;
$filter = $filter ?? 'tous';
$department = $department ?? '';

$avatarClassForDepartment = static function (string $departmentName): string {
  $normalized = strtolower(trim($departmentName));

  if ($normalized === 'ressources humaines' || $normalized === 'rh') {
    return 'av-blue';
  }

  if ($normalized === 'comptabilite' || $normalized === 'finance') {
    return 'av-amber';
  }

  return 'av-green';
};

$typeClassForLabel = static function (string $label): string {
  $normalized = strtolower(trim($label));

  return match ($normalized) {
    'congé maladie', 'conge maladie' => 't-maladie',
    'congé spécial', 'conge spécial', 'conge special' => 't-special',
    'congé sans solde', 'conge sans solde' => 't-sans-solde',
    default => 't-annuel',
  };
};

$statutClassForId = static function (int $statutId): string {
  return match ($statutId) {
    2 => 's-approuvee',
    3 => 's-refusee',
    4 => 's-annulee',
    default => 's-attente',
  };
};

$soldeClassForValue = static function (int $joursRestants): string {
  if ($joursRestants <= 0) {
    return 'solde-danger';
  }

  if ($joursRestants <= 5) {
    return 'solde-warn';
  }

  return 'solde-ok';
};
?>

<section id="page-liste-rh" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-person-check"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace responsable</span></div>
    </div>
    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
      <li><a href="/rh/dashboard"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li>
        <a href="/rh/dashboard" class="active">
          <i class="bi bi-inbox"></i> Demandes à traiter
          <span class="nav-badge alert"><?= esc((string) $pendingCount) ?></span>
        </a>
      </li>
      <li><a href="/rh/dashboard?filter=refusees"><i class="bi bi-archive"></i> Historique</a></li>
      <li><a href="/rh/soldes"><i class="bi bi-people"></i> Soldes employés</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-blue"><?= esc((string) $currentUser['initiales']) ?></div>
        <div><div class="user-name"><?= esc(trim((string) ($currentUser['prenom'] ?? '') . ' ' . (string) ($currentUser['nom'] ?? 'Responsable RH'))) ?></div><div class="user-role">Responsable RH<?= !empty($currentUser['departement']) ? ' · ' . esc((string) $currentUser['departement']) : '' ?></div></div>
        <a href="/logout" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Demandes à traiter</div>
        <div class="topbar-breadcrumb"><a href="/rh/dashboard">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Demandes</div>
      </div>
      <div class="topbar-actions">
        <span style="font-size:.8rem;color:var(--muted);background:var(--warn-bg);border:1px solid var(--warn-br);border-radius:6px;padding:5px 10px;display:flex;align-items:center;gap:5px;color:var(--warn)">
          <i class="bi bi-hourglass-split"></i> <?= $pendingCount ?> en attente
        </span>
      </div>
    </div>

    <div class="content">

      <!-- Flash -->
      <?php if (session()->getFlashdata('success')): ?>
      <div class="flash flash-success">
        <i class="bi bi-check-circle-fill"></i>
        <?= esc((string) session()->getFlashdata('success')) ?>
      </div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('info')): ?>
      <div class="flash flash-info">
        <i class="bi bi-info-circle-fill"></i>
        <?= esc((string) session()->getFlashdata('info')) ?>
      </div>
      <?php endif; ?>

      <!-- Filtre -->
      <div style="display:flex;gap:8px;margin-bottom:1.25rem;flex-wrap:wrap;align-items:center">
        <a href="<?= base_url('rh/dashboard?filter=tous') ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--forest);background:<?= $filter === 'tous' ? 'var(--forest)' : 'var(--white)' ?>;color:<?= $filter === 'tous' ? 'var(--white)' : 'var(--muted)' ?>;cursor:pointer;text-decoration:none">Tous (<?= esc((string) ($statusCounts['tous'] ?? 0)) ?>)</a>
        <a href="<?= base_url('rh/dashboard?filter=attente') ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:<?= $filter === 'attente' ? 'var(--forest)' : 'var(--white)' ?>;color:<?= $filter === 'attente' ? 'var(--white)' : 'var(--muted)' ?>;cursor:pointer;text-decoration:none">En attente (<?= esc((string) ($statusCounts['attente'] ?? 0)) ?>)</a>
        <a href="<?= base_url('rh/dashboard?filter=approuvees') ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:<?= $filter === 'approuvees' ? 'var(--forest)' : 'var(--white)' ?>;color:<?= $filter === 'approuvees' ? 'var(--white)' : 'var(--muted)' ?>;cursor:pointer;text-decoration:none">Approuvées (<?= esc((string) ($statusCounts['approuvees'] ?? 0)) ?>)</a>
        <a href="<?= base_url('rh/dashboard?filter=refusees') ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:<?= $filter === 'refusees' ? 'var(--forest)' : 'var(--white)' ?>;color:<?= $filter === 'refusees' ? 'var(--white)' : 'var(--muted)' ?>;cursor:pointer;text-decoration:none">Refusées (<?= esc((string) ($statusCounts['refusees'] ?? 0)) ?>)</a>
        <form method="GET" action="<?= base_url('rh/dashboard') ?>" style="margin-left:auto;display:flex;gap:8px;align-items:center">
          <select class="f-select" name="department" style="font-size:.8rem;padding:6px 10px;width:auto">
            <option value="">Tous les départements</option>
            <?php foreach ($departments as $dept): ?>
            <option value="<?= esc((string) $dept['nom']) ?>" <?= $department === $dept['nom'] ? 'selected' : '' ?>><?= esc((string) $dept['nom']) ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn-sm btn-approve" style="padding:6px 12px">Filtrer</button>
        </form>
      </div>

      <div class="data-card">
        <div class="data-card-head"><h3>Toutes les demandes</h3></div>
        <table class="tbl">
          <thead>
            <tr><th>Employé</th><th>Type</th><th>Période</th><th>Durée</th><th>Solde dispo</th><th>Statut</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php if (!empty($demandes)) : ?>
            <?php foreach ($demandes as $demande): ?>
            <?php
              $typeClass = $typeClassForLabel((string) ($demande['type_libelle'] ?? ''));
              $statutClass = $statutClassForId((int) ($demande['statut_id'] ?? 0));
              $joursRestants = (int) ($demande['jours_restants'] ?? 0);
              $soldeClass = $soldeClassForValue($joursRestants);
              $avatarClass = $avatarClassForDepartment((string) ($demande['dept_nom'] ?? ''));
            ?>
            <tr>
              <td>
                <div class="profile-row">
                  <div class="avatar <?= esc($avatarClass) ?>" style="width:32px;height:32px;font-size:.7rem"><?= esc(strtoupper(substr((string) $demande['nom'], 0, 1) . substr((string) $demande['prenom'], 0, 1))) ?></div>
                  <div class="profile-info">
                    <div class="pname"><?= esc((string) $demande['nom']) ?> <?= esc((string) $demande['prenom']) ?></div>
                    <div class="pdept"><?= esc((string) ($demande['dept_nom'] ?? '')) ?></div>
                  </div>
                </div>
              </td>
              <td><span class="type-badge <?= esc($typeClass) ?>"><?= esc((string) $demande['type_libelle']) ?></span></td>
              <td class="td-muted" style="font-size:.8rem"><?= esc(date('d/m', strtotime((string) $demande['date_debut']))) ?> – <?= esc(date('d/m/Y', strtotime((string) $demande['date_fin']))) ?></td>
              <td class="td-mono"><?= esc((string) ($demande['nb_jours'] ?? 0)) ?> j</td>
              <td><span class="solde-badge <?= esc($soldeClass) ?>"><?= esc((string) $joursRestants) ?> j</span></td>
              <td><span class="statut <?= esc($statutClass) ?>"><?= esc((string) $demande['statut_libelle']) ?></span></td>
              <td>
                <?php if ((int) ($demande['statut_id'] ?? 0) == 1): ?>
                <div class="action-btns">
                  <form method="POST" action="<?= base_url('rh/approuver/' . $demande['id']) ?>" style="display:inline">
                    <?= csrf_field() ?>
                    <button class="btn-sm btn-approve"><i class="bi bi-check-lg"></i> Approuver</button>
                  </form>
                  <form method="POST" action="<?= base_url('rh/refuser/' . $demande['id']) ?>" style="display:inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="commentaire" value="">
                    <button class="btn-sm btn-refuse" type="submit"><i class="bi bi-x-lg"></i> Refuser</button>
                  </form>
                </div>
                <?php else: ?>
                <span class="td-muted" style="font-size:.75rem">Traité</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php else : ?>
            <tr>
              <td colspan="7" class="td-muted" style="text-align:center;padding:1rem">Aucune demande à afficher.</td>
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