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

$formatDate = static function (?string $date, string $format = 'Y-m-d'): string {
  if (empty($date)) {
    return '—';
  }

  return date($format, strtotime($date));
};
?>

<section id="page-admin-employes" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Administration</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="<?= base_url('admin/dashboard') ?>"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
      <li><a href="<?= base_url('admin/dashboard') ?>"><i class="bi bi-inbox"></i> Toutes les demandes</a></li>
      <li><a href="<?= base_url('admin/employes') ?>" class="active"><i class="bi bi-people"></i> Employés</a></li>
      <li><a href="#page-admin-departements"><i class="bi bi-building"></i> Départements</a></li>
      <li><a href="#page-admin-types"><i class="bi bi-tags"></i> Types de congé</a></li>
      <li><a href="#page-admin-soldes"><i class="bi bi-sliders"></i> Soldes annuels</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-slate" style="width:32px;height:32px;font-size:.7rem"><?= esc((string) $currentUser['initiales']) ?></div>
        <div><div class="user-name"><?= esc(trim((string) ($currentUser['prenom'] ?? '') . ' ' . (string) ($currentUser['nom'] ?? 'Administrateur'))) ?></div><div class="user-role">Admin système<?= !empty($currentUser['departement']) ? ' · ' . esc((string) $currentUser['departement']) : '' ?></div></div>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Gestion des employés</div>
        <div class="topbar-breadcrumb"><a href="<?= base_url('admin/dashboard') ?>">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Employés</div>
      </div>
      <div class="topbar-actions">
        <a href="#page-admin-add-employe" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter</a>
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

      <!-- Formulaire ajout -->
      <div id="page-admin-add-employe" class="form-section">
        <h3><i class="bi bi-person-plus" style="color:var(--forest);margin-right:6px"></i>Ajouter un employé</h3>
        <form method="POST" action="<?= base_url('admin/employes') ?>">
          <?= csrf_field() ?>
          <div class="form-grid-2" style="margin-bottom:1rem">
            <div class="f-group">
              <label class="f-label">Prénom</label>
              <input type="text" class="f-input" name="prenom" value="<?= esc((string) old('prenom')) ?>" required/>
            </div>
            <div class="f-group">
              <label class="f-label">Nom</label>
              <input type="text" class="f-input" name="nom" value="<?= esc((string) old('nom')) ?>" required/>
            </div>
            <div class="f-group">
              <label class="f-label">Email</label>
              <input type="email" class="f-input" name="email" value="<?= esc((string) old('email')) ?>" required/>
            </div>
            <div class="f-group">
              <label class="f-label">Mot de passe initial</label>
              <input type="password" class="f-input" name="password" required/>
            </div>
            <div class="f-group">
              <label class="f-label">Département</label>
              <select class="f-select" name="departement_id" required>
                <option value="">Choisir...</option>
                <?php foreach ($departments as $department): ?>
                <option value="<?= esc((string) $department['id']) ?>"><?= esc((string) $department['nom']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="f-group">
              <label class="f-label">Rôle</label>
              <select class="f-select" name="role" required>
                <option value="employe">Employé</option>
                <option value="rh">Responsable RH</option>
                <option value="admin">Administrateur</option>
              </select>
            </div>
            <div class="f-group">
              <label class="f-label">Date d'embauche</label>
              <input type="date" class="f-input" name="date_embauche" value="<?= esc((string) date('Y-m-d')) ?>" required/>
            </div>
          </div>
          <div class="flash flash-info" style="margin-bottom:1rem">
            <i class="bi bi-info-circle-fill"></i>
            <span style="font-size:.82rem">Les soldes de congés seront initialisés automatiquement selon les types de congé configurés.</span>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn-forest"><i class="bi bi-plus"></i> Créer l'employé</button>
            <a href="<?= base_url('admin/employes') ?>" class="btn-secondary">Réinitialiser</a>
          </div>
        </form>
      </div>

      <!-- Liste employés -->
      <div class="data-card">
        <div class="data-card-head">
          <h3>Tous les employés</h3>
          <form method="GET" action="<?= base_url('admin/employes') ?>" style="display:flex;gap:6px;flex-wrap:wrap">
            <input type="text" class="f-input" name="search" value="<?= esc((string) $search) ?>" placeholder="Rechercher..." style="width:200px;padding:6px 10px;font-size:.8rem"/>
            <select class="f-select" name="department" style="font-size:.8rem;padding:6px 10px;width:auto">
              <option>Tous les depts</option>
              <?php foreach ($departments as $department): ?>
              <option value="<?= esc((string) $department['nom']) ?>" <?= $departmentFilter === $department['nom'] ? 'selected' : '' ?>><?= esc((string) $department['nom']) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-sm btn-approve" style="padding:6px 12px">Filtrer</button>
          </form>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Employé</th><th>Département</th><th>Rôle</th><th>Embauche</th><th>Statut</th><th>Solde annuel</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php if (!empty($employees)): ?>
            <?php foreach ($employees as $employee): ?>
            <tr>
              <td>
                <div class="profile-row">
                  <div class="avatar <?= esc((string) ($employee['avatar_class'] ?? 'av-green')) ?>" style="width:32px;height:32px;font-size:.68rem"><?= esc((string) ($employee['initiales'] ?? '')) ?></div>
                  <div class="profile-info"><div class="pname"><?= esc(trim((string) ($employee['prenom'] ?? '') . ' ' . (string) ($employee['nom'] ?? ''))) ?></div><div class="pdept"><?= esc((string) ($employee['email'] ?? '')) ?></div></div>
                </div>
              </td>
              <td class="td-muted"><?= esc((string) ($employee['dept_nom'] ?? '')) ?></td>
              <td><span class="type-badge <?= esc((string) ($employee['role_class'] ?? 't-annuel')) ?>"><?= esc((string) ($employee['role_label'] ?? $employee['role'] ?? 'Employé')) ?></span></td>
              <td class="td-muted td-mono" style="font-size:.78rem"><?= esc($formatDate((string) ($employee['date_embauche'] ?? null))) ?></td>
              <td><span class="statut <?= esc((string) ($employee['status_class'] ?? 's-attente')) ?>"><?= esc((string) ($employee['status_label'] ?? 'actif')) ?></span></td>
              <td><span class="solde-badge <?= esc($balanceClassForValue((int) ($employee['annual_balance'] ?? 0))) ?>"><?= esc((string) ($employee['annual_balance'] ?? 0)) ?> j</span></td>
              <td>
                <div class="action-btns">
                  <form method="POST" action="<?= base_url('admin/employes/' . $employee['id'] . '/toggle') ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-sm <?= (int) ($employee['actif'] ?? 0) === 1 ? 'btn-del' : 'btn-view' ?>"><i class="bi <?= esc((string) ($employee['toggle_icon'] ?? 'bi-slash-circle')) ?>"></i> <?= esc((string) ($employee['toggle_label'] ?? 'Désactiver')) ?></button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr><td colspan="7" class="td-muted" style="text-align:center;padding:1rem">Aucun employé trouvé.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div id="page-admin-departements" class="data-card" style="margin-top:1.5rem">
        <div class="data-card-head"><h3>Départements</h3></div>
        <table class="tbl">
          <thead><tr><th>Département</th><th>Description</th></tr></thead>
          <tbody>
            <?php if (!empty($departments)): ?>
            <?php foreach ($departments as $department): ?>
            <tr>
              <td class="td-name"><?= esc((string) $department['nom']) ?></td>
              <td class="td-muted"><?= esc((string) ($department['description'] ?? '')) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr><td colspan="2" class="td-muted" style="text-align:center;padding:1rem">Aucun département trouvé.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div id="page-admin-types" class="data-card" style="margin-top:1.5rem">
        <div class="data-card-head"><h3>Types de congé</h3></div>
        <table class="tbl">
          <thead><tr><th>Type</th><th>Jours annuels</th><th>Déductible</th></tr></thead>
          <tbody>
            <?php if (!empty($typesConge)): ?>
            <?php foreach ($typesConge as $typeConge): ?>
            <tr>
              <td><span class="type-badge <?= esc($typeClassForLabel((string) ($typeConge['libelle'] ?? ''))) ?>"><?= esc((string) ($typeConge['libelle'] ?? '')) ?></span></td>
              <td class="td-mono"><?= esc((string) ($typeConge['jours_annuels'] ?? 0)) ?> j</td>
              <td><span class="statut <?= ((int) ($typeConge['deductible'] ?? 0) === 1) ? 's-attente' : 's-approuvee' ?>"><?= ((int) ($typeConge['deductible'] ?? 0) === 1) ? 'oui' : 'non' ?></span></td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr><td colspan="3" class="td-muted" style="text-align:center;padding:1rem">Aucun type de congé trouvé.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div id="page-admin-soldes" class="data-card" style="margin-top:1.5rem">
        <div class="data-card-head"><h3>Soldes annuels</h3></div>
        <div class="flash flash-info" style="margin:0;border-left:4px solid var(--forest)">
          <i class="bi bi-info-circle-fill"></i>
          <span style="font-size:.82rem">Les soldes sont initialisés automatiquement lors de la création d’un employé.</span>
        </div>
      </div>

    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc((string) date('Y')) ?> <span>TechMada RH</span></div>
  </div>

</div>
</section>

<?= $this->endSection() ?>