<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<section id="page-liste-rh" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-person-check"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace responsable</span></div>
    </div>
    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
      <li><a href="#page-dashboard-rh"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li>
        <a href="#page-liste-rh" class="active">
          <i class="bi bi-inbox"></i> Demandes à traiter
          <span class="nav-badge alert">4</span>
        </a>
      </li>
      <li><a href="#page-liste-rh"><i class="bi bi-archive"></i> Historique</a></li>
      <li><a href="#page-liste-rh"><i class="bi bi-people"></i> Soldes employés</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-blue">MR</div>
        <div><div class="user-name">Marie Rabe</div><div class="user-role">Responsable RH</div></div>
        <a href="#page-login" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Demandes à traiter</div>
        <div class="topbar-breadcrumb"><a href="#page-dashboard-rh">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Demandes</div>
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
        <?= session()->getFlashdata('success') ?>
      </div>
      <?php endif; ?>

      <!-- Filtre -->
      <div style="display:flex;gap:8px;margin-bottom:1.25rem;flex-wrap:wrap">
        <a href="<?= base_url('rh?filter=tous') ?>" class="<?= $filter === 'tous' ? 'btn-active' : '' ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--forest);background:var(--forest);color:var(--white);cursor:pointer;text-decoration:none">Tous (<?= count($demandes) ?>)</a>
        <a href="<?= base_url('rh?filter=attente') ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);cursor:pointer;text-decoration:none">En attente (<?= count(array_filter($demandes, fn($d) => $d['statut_id'] == 1)) ?>)</a>
        <a href="<?= base_url('rh?filter=approuvees') ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);cursor:pointer;text-decoration:none">Approuvées (<?= count(array_filter($demandes, fn($d) => $d['statut_id'] == 2)) ?>)</a>
        <a href="<?= base_url('rh?filter=refusees') ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);cursor:pointer;text-decoration:none">Refusées (<?= count(array_filter($demandes, fn($d) => $d['statut_id'] == 3)) ?>)</a>
        <select class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto;margin-left:auto" onchange="window.location.href='<?= base_url('rh') ?>?department=' + this.value">
          <option value="">Tous les départements</option>
          <?php foreach ($departments as $dept): ?>
          <option value="<?= $dept['nom'] ?>" <?= $department === $dept['nom'] ? 'selected' : '' ?>><?= $dept['nom'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="data-card">
        <div class="data-card-head"><h3>Toutes les demandes</h3></div>
        <table class="tbl">
          <thead>
            <tr><th>Employé</th><th>Type</th><th>Période</th><th>Durée</th><th>Solde dispo</th><th>Statut</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php foreach ($demandes as $demande): ?>
            <tr>
              <td>
                <div class="profile-row">
                  <div class="avatar" style="width:32px;height:32px;font-size:.7rem"><?= substr($demande['nom'], 0, 1) . substr($demande['prenom'], 0, 1) ?></div>
                  <div class="profile-info">
                    <div class="pname"><?= $demande['nom'] ?> <?= $demande['prenom'] ?></div>
                    <div class="pdept"><?= $demande['dept_nom'] ?></div>
                  </div>
                </div>
              </td>
              <td><span class="type-badge"><?= $demande['type_libelle'] ?></span></td>
              <td class="td-muted" style="font-size:.8rem"><?= date('d/m', strtotime($demande['date_debut'])) ?> – <?= date('d/m/Y', strtotime($demande['date_fin'])) ?></td>
              <td class="td-mono"><?= $demande['nb_jours'] ?> j</td>
              <td><span style="font-family:'DM Mono',monospace;font-size:.82rem"><?= $demande['nb_jours'] ?> j</span></td>
              <td><span class="statut"><?= $demande['statut_libelle'] ?></span></td>
              <td>
                <?php if ($demande['statut_id'] == 1): ?>
                <div class="action-btns">
                  <form method="POST" action="<?= base_url('rh/approuver/' . $demande['id']) ?>" style="display:inline">
                    <button class="btn-sm btn-approve"><i class="bi bi-check-lg"></i> Approuver</button>
                  </form>
                  <button class="btn-sm btn-refuse" onclick="openRefusalForm(<?= $demande['id'] ?>)"><i class="bi bi-x-lg"></i> Refuser</button>
                </div>
                <?php else: ?>
                <span class="td-muted" style="font-size:.75rem">Traité</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
  </div>

</div>
</section>

<?= $this->endSection() ?>