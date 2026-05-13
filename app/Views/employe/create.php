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
$soldes = $soldes ?? [
  'annuel' => ['label' => 'Congé annuel', 'attribues' => 0, 'pris' => 0, 'restants' => 0, 'progress' => 0],
  'maladie' => ['label' => 'Congé maladie', 'attribues' => 0, 'pris' => 0, 'restants' => 0, 'progress' => 0],
  'special' => ['label' => 'Congé spécial', 'attribues' => 0, 'pris' => 0, 'restants' => 0, 'progress' => 0],
];
$typesConge = $typesConge ?? [];
$flashError = $flashError ?? null;
$flashSuccess = $flashSuccess ?? null;
$oldTypeCongeId = $oldTypeCongeId ?? '';
$oldDateDebut = $oldDateDebut ?? '';
$oldDateFin = $oldDateFin ?? '';
$oldMotif = $oldMotif ?? '';

$startDate = !empty($oldDateDebut) ? new DateTimeImmutable($oldDateDebut) : null;
$endDate = !empty($oldDateFin) ? new DateTimeImmutable($oldDateFin) : null;
$calculatedDays = $calculatedDays ?? (($startDate && $endDate && $endDate >= $startDate) ? $startDate->diff($endDate)->days + 1 : 0);
$dateRangeLabel = ($startDate && $endDate && $endDate >= $startDate)
  ? $startDate->format('l d/m/Y') . ' au ' . $endDate->format('l d/m/Y')
  : 'du lundi 23 au vendredi 27 juin 2025';
$selectedTypeKey = 'annuel';

foreach ($typesConge as $typeConge) {
  if ((string) ($typeConge['id'] ?? '') === (string) $oldTypeCongeId) {
    $selectedTypeKey = (string) ($typeConge['key'] ?? 'annuel');
    break;
  }
}
?>

<section id="page-form-conge" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="/employe/dashboard"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="/employe/conge/create" class="active"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li><a href="/employe/conges"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
      <li><a href="/employe/dashboard#page-profil-employe"><i class="bi bi-person"></i> Mon profil</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-green"><?= esc((string) $employe['initiales']) ?></div>
        <div>
          <div class="user-name"><?= esc(trim((string) ($employe['prenom'] ?? '') . ' ' . (string) ($employe['nom'] ?? 'Employé'))) ?></div>
          <div class="user-role">Employé<?= !empty($employe['departement']) ? ' · ' . esc((string) $employe['departement']) : '' ?></div>
        </div>
        <a href="/logout" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
          <div class="topbar-title">Nouvelle demande de congé</div>
          <div class="topbar-breadcrumb">
          <a href="/employe/dashboard">Accueil</a>
          <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Nouvelle demande
        </div>
      </div>
    </div>

    <div class="content">

      <?php if (!empty($flashSuccess)) : ?>
        <div class="flash flash-success">
          <i class="bi bi-check-circle-fill"></i>
          <?= esc((string) $flashSuccess) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($flashError)) : ?>
        <div class="flash flash-error">
          <i class="bi bi-exclamation-circle-fill"></i>
          <?= esc((string) $flashError) ?>
        </div>
      <?php endif; ?>

      <form action="/employe/conge/create" method="post" style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start" class="form-layout">
        <?= csrf_field() ?>

        <!-- Formulaire principal -->
        <div>
          <div class="form-section">
            <h3>Détails de la demande</h3>

            <div class="f-group" style="margin-bottom:1rem">
              <label class="f-label">Type de congé <span style="color:var(--danger)">*</span></label>
              <select class="f-select" name="type_conge_id" required>
                <option value="">-- Choisir un type --</option>
                <?php if (!empty($typesConge)) : ?>
                  <?php foreach ($typesConge as $typeConge) : ?>
                    <?php
                      $typeId = (string) ($typeConge['id'] ?? '');
                      $typeKey = (string) ($typeConge['key'] ?? 'annuel');
                      $soldesInfo = $soldes[$typeKey] ?? ['restants' => 0, 'attribues' => 0];
                    ?>
                    <option value="<?= esc($typeId) ?>" <?= ((string) $oldTypeCongeId === $typeId) ? 'selected' : '' ?>>
                      <?= esc((string) ($typeConge['libelle'] ?? 'Type')) ?> (<?= esc((string) ($soldesInfo['restants'] ?? 0)) ?> j restants)
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
              <?php if (empty($oldTypeCongeId) && !empty($flashError)) : ?>
                <div class="f-error"><i class="bi bi-exclamation-circle"></i> Ce champ est requis.</div>
              <?php endif; ?>
            </div>

            <div class="form-grid-2" style="margin-bottom:1rem">
              <div class="f-group">
                <label class="f-label">Date de début <span style="color:var(--danger)">*</span></label>
                <input type="date" class="f-input" name="date_debut" value="<?= esc((string) $oldDateDebut) ?>" required/>
              </div>
              <div class="f-group">
                <label class="f-label">Date de fin <span style="color:var(--danger)">*</span></label>
                <input type="date" class="f-input" name="date_fin" value="<?= esc((string) $oldDateFin) ?>" required/>
              </div>
            </div>

            <!-- Calcul automatique côté PHP (affiché après soumission ou en JS) -->
            <div class="f-computed">
              <div class="f-computed-num"><?= esc((string) $calculatedDays) ?></div>
              <div class="f-computed-label">jours calendaires calculés<br><span style="font-size:.7rem;opacity:.7"><?= esc($dateRangeLabel) ?></span></div>
            </div>

            <div class="f-group" style="margin-bottom:1rem">
              <label class="f-label">Motif (optionnel)</label>
              <textarea class="f-textarea" name="motif" placeholder="Précisez le motif de votre demande si nécessaire..."><?= esc((string) $oldMotif) ?></textarea>
              <div class="f-hint">Le motif est visible par le responsable RH.</div>
            </div>

            <div class="form-actions">
              <button class="btn-forest" type="submit"><i class="bi bi-send"></i> Soumettre la demande</button>
              <a href="/employe/dashboard" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a>
            </div>
          </div>
        </div>

        <!-- Panneau latéral : solde & règles -->
        <div style="display:flex;flex-direction:column;gap:1rem">
          <div class="data-card" style="margin:0">
            <div class="data-card-head"><h3><i class="bi bi-piggy-bank" style="color:var(--forest);margin-right:5px"></i>Vos soldes actuels</h3></div>
            <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.75rem">
              <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                  <span style="font-size:.8rem;color:var(--ink)">Congé annuel</span>
                  <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--forest);font-weight:500"><?= esc((string) ($soldes['annuel']['restants'] ?? 0)) ?> j</span>
                </div>
                <div class="solde-bar"><div class="solde-fill" style="width:<?= esc((string) ($soldes['annuel']['progress'] ?? 0)) ?>%"></div></div>
              </div>
              <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                  <span style="font-size:.8rem;color:var(--ink)">Maladie</span>
                  <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--forest);font-weight:500"><?= esc((string) ($soldes['maladie']['restants'] ?? 0)) ?> j</span>
                </div>
                <div class="solde-bar"><div class="solde-fill" style="width:<?= esc((string) ($soldes['maladie']['progress'] ?? 0)) ?>%"></div></div>
              </div>
              <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                  <span style="font-size:.8rem;color:var(--ink)">Spécial</span>
                  <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--warn);font-weight:500"><?= esc((string) ($soldes['special']['restants'] ?? 0)) ?> j</span>
                </div>
                <div class="solde-bar"><div class="solde-fill warn" style="width:<?= esc((string) ($soldes['special']['progress'] ?? 0)) ?>%"></div></div>
              </div>
            </div>
          </div>
          <div class="flash flash-info" style="margin:0">
            <i class="bi bi-info-circle-fill"></i>
            <span style="font-size:.8rem">Le solde est déduit uniquement à l'approbation de votre responsable.</span>
          </div>
          <div style="background:var(--cream);border:1px solid var(--border);border-radius:8px;padding:.85rem 1rem">
            <div style="font-size:.78rem;font-weight:500;color:var(--ink);margin-bottom:.5rem"><i class="bi bi-clipboard-check" style="color:var(--forest);margin-right:5px"></i>Rappel des règles</div>
            <ul style="margin:0;padding-left:1rem;font-size:.75rem;color:var(--muted);line-height:1.7">
              <li>Préavis minimum : 48h avant la date de début</li>
              <li>Pas de chevauchement avec une demande en cours</li>
              <li>Solde insuffisant = demande refusée automatiquement</li>
            </ul>
          </div>
        </div>

      </form>
    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc((string) date('Y')) ?> <span>TechMada RH</span></div>
  </div>

</div>
</section>
<?= $this->endSection() ?>