<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<?php
$currentUser = $currentUser ?? [
  'nom' => 'Administrateur',
  'prenom' => '',
  'departement' => '',
  'initiales' => 'AD',
  'role' => 'admin',
];
$metrics = $metrics ?? [
  'activeEmployees' => 0,
  'pendingRequests' => 0,
  'approvedThisMonth' => 0,
  'departmentsCount' => 0,
  'absentTodayCount' => 0,
];
$recentDemandes = $recentDemandes ?? [];
$absentsToday = $absentsToday ?? [];
$criticalSoldes = $criticalSoldes ?? [];
$employees = $employees ?? [];
$departments = $departments ?? [];
$typesConge = $typesConge ?? [];
$search = $search ?? '';
$departmentFilter = $departmentFilter ?? '';

$typeClassForLabel = static function (string $label): string {
  $normalized = strtolower(trim($label));

  return match ($normalized) {
    'congé maladie', 'conge maladie', 'maladie' => 't-maladie',
    'congé spécial', 'conge spécial', 'conge special', 'spécial', 'special' => 't-special',
    'congé sans solde', 'conge sans solde' => 't-sans-solde',
    default => 't-annuel',
  };
};

$balanceClassForValue = static function (int $joursRestants): string {
  if ($joursRestants <= 0) {
    return 'solde-danger';
  }

  if ($joursRestants <= 5) {
    return 'solde-warn';
  }

  return 'solde-ok';
};

$formatDate = static function (?string $date, string $format = 'd/m/Y'): string {
  if (empty($date)) {
    return '—';
  }

  return date($format, strtotime($date));
};
?>

<section id="page-dashboard-admin" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
      <div class="sidebar-brand-name">TechMada RH
        <span>Administration</span>
      </div>
    </div>
    <div class="sidebar-section">Gestion</div>
    <ul class="sidebar-nav">
      <li><a href="#page-dashboard-admin" class="active"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
      <li>
        <a href="#page-liste-rh">
          <i class="bi bi-inbox"></i> Toutes les demandes
          <span class="nav-badge alert"><?= esc((string) ($metrics['pendingRequests'] ?? 0)) ?></span>
        </a>
      </li>
      <li><a href="<?= base_url('admin/employes') ?>"><i class="bi bi-people"></i> Employés</a></li>
      <li><a href="<?= base_url('admin/employes') ?>#page-admin-departements"><i class="bi bi-building"></i> Départements</a></li>
      <li><a href="<?= base_url('admin/employes') ?>#page-admin-types"><i class="bi bi-tags"></i> Types de congé</a></li>
      <li><a href="<?= base_url('admin/employes') ?>#page-admin-soldes"><i class="bi bi-sliders"></i> Soldes annuels</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-slate" style="width:32px;height:32px;font-size:.7rem"><?= esc((string) $currentUser['initiales']) ?></div>
        <div><div class="user-name"><?= esc(trim((string) ($currentUser['prenom'] ?? '') . ' ' . (string) ($currentUser['nom'] ?? 'Administrateur'))) ?></div><div class="user-role">Admin système<?= !empty($currentUser['departement']) ? ' · ' . esc((string) $currentUser['departement']) : '' ?></div></div>
        <a href="/logout" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Vue d'ensemble</div>
        <div class="topbar-breadcrumb">Administration</div>
      </div>
      <div class="topbar-actions">
        <a href="<?= base_url('admin/employes') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter un employé</a>
      </div>
    </div>

    <div class="content">

      <?php if (session()->getFlashdata('success')): ?>
      <div class="flash flash-success">
        <i class="bi bi-check-circle-fill"></i>
        <?= esc((string) session()->getFlashdata('success')) ?>
      </div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')): ?>
      <div class="flash flash-error">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <?= esc((string) session()->getFlashdata('error')) ?>
      </div>
      <?php endif; ?>

      <!-- Métriques admin -->
      <div class="metrics">
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-people"></i></div></div>
          <div class="metric-val"><?= esc((string) ($metrics['activeEmployees'] ?? 0)) ?></div>
          <div class="metric-label">Employés actifs</div>
          <div class="metric-sub up"><i class="bi bi-arrow-up-short"></i> Données réelles</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
          <div class="metric-val"><?= esc((string) ($metrics['pendingRequests'] ?? 0)) ?></div>
          <div class="metric-label">Demandes en attente</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-calendar-check"></i></div></div>
          <div class="metric-val"><?= esc((string) ($metrics['approvedThisMonth'] ?? 0)) ?></div>
          <div class="metric-label">Approuvées ce mois</div>
          <div class="metric-sub up"><i class="bi bi-arrow-up-short"></i> Selon les congés validés</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-blue"><i class="bi bi-building"></i></div></div>
          <div class="metric-val"><?= esc((string) ($metrics['departmentsCount'] ?? 0)) ?></div>
          <div class="metric-label">Départements</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-person-slash"></i></div></div>
          <div class="metric-val"><?= esc((string) ($metrics['absentTodayCount'] ?? 0)) ?></div>
          <div class="metric-label">Absents aujourd'hui</div>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">

        <!-- Demandes récentes -->
        <div class="data-card" style="margin:0">
          <div class="data-card-head">
            <h3>Demandes récentes</h3>
            <a href="#page-liste-rh" style="font-size:.8rem;color:var(--forest);text-decoration:none">Tout voir →</a>
          </div>
          <table class="tbl">
            <thead>
              <tr><th>Employé</th><th>Type</th><th>Période</th><th>Durée</th><th>Statut</th></tr>
            </thead>
            <tbody>
              <?php if (!empty($recentDemandes)): ?>
              <?php foreach ($recentDemandes as $demande): ?>
              <tr>
                <td>
                  <div style="display:flex;align-items:center;gap:7px">
                    <div class="avatar <?= esc((string) ($demande['avatar_class'] ?? 'av-green')) ?>" style="width:28px;height:28px;font-size:.62rem"><?= esc((string) ($demande['initiales'] ?? '')) ?></div>
                    <span class="td-name" style="font-size:.84rem"><?= esc(trim((string) ($demande['prenom'] ?? '') . ' ' . (string) ($demande['nom'] ?? ''))) ?></span>
                  </div>
                </td>
                <td><span class="type-badge <?= esc((string) ($demande['type_class'] ?? 't-annuel')) ?>"><?= esc((string) ($demande['type_label'] ?? 'Congé annuel')) ?></span></td>
                <td class="td-muted" style="font-size:.78rem"><?= esc($formatDate((string) ($demande['date_debut'] ?? null), 'd/m')) ?> – <?= esc($formatDate((string) ($demande['date_fin'] ?? null), 'd/m/Y')) ?></td>
                <td class="td-mono"><?= esc((string) ($demande['nb_jours'] ?? 0)) ?> j</td>
                <td><span class="statut <?= esc((string) ($demande['statut_class'] ?? 's-attente')) ?>"><?= esc((string) ($demande['statut_label'] ?? 'en attente')) ?></span></td>
              </tr>
              <?php endforeach; ?>
              <?php else: ?>
              <tr><td colspan="5" class="td-muted" style="text-align:center;padding:1rem">Aucune demande récente.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Absents du jour + soldes critiques -->
        <div style="display:flex;flex-direction:column;gap:1rem">
          <div class="data-card" style="margin:0">
            <div class="data-card-head"><h3><i class="bi bi-person-slash" style="color:var(--muted);margin-right:5px"></i>Absents aujourd'hui</h3></div>
            <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.6rem">
              <?php if (!empty($absentsToday)): ?>
              <?php foreach ($absentsToday as $absent): ?>
              <div style="display:flex;align-items:center;gap:8px">
                <div class="avatar <?= esc((string) ($absent['avatar_class'] ?? 'av-green')) ?>" style="width:30px;height:30px;font-size:.65rem"><?= esc((string) ($absent['initiales'] ?? '')) ?></div>
                <div><div style="font-size:.83rem;font-weight:500;color:var(--ink)"><?= esc(trim((string) ($absent['prenom'] ?? '') . ' ' . (string) ($absent['nom'] ?? ''))) ?></div><div style="font-size:.72rem;color:var(--muted)"><?= esc((string) ($absent['type_libelle'] ?? 'Congé')) ?> · retour <?= esc($formatDate((string) ($absent['return_date'] ?? null), 'd/m')) ?></div></div>
              </div>
              <?php endforeach; ?>
              <?php else: ?>
              <div class="td-muted" style="font-size:.8rem">Aucun absent aujourd'hui.</div>
              <?php endif; ?>
            </div>
          </div>
          <div class="flash flash-warn" style="margin:0">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span style="font-size:.8rem"><?= esc((string) count($criticalSoldes)) ?> employés ont un solde critique (≤ 5 jours). <a href="#page-admin-soldes" style="color:var(--warn);font-weight:500">Voir les soldes →</a></span>
          </div>
        </div>

      </div>

    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc((string) date('Y')) ?> <span>TechMada RH</span></div>
  </div>

</div>
</section>

<?= $this->endSection() ?>
