<?= $this->extend('Layout/app') ?>
<?= $this->section('title') ?>Accueil Employé<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Tableau de bord<?= $this->endSection() ?>
<?= $this->section('breadcrumb') ?>Accueil<?= $this->endSection() ?>
<?= $this->section('page_actions') ?>
<a href="<?= base_url('employe/demande') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem">
    <i class="fas fa-plus"></i> Nouvelle demande
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="metrics">
    <div class="metric">
        <div class="metric-top"><div class="metric-icon mi-forest"><i class="fas fa-wallet"></i></div></div>
        <div class="metric-val"><?= $solde ?? '—' ?></div>
        <div class="metric-label">Jours restants</div>
        <?php if (isset($solde) && $solde !== '—'): ?>
        <div class="metric-sub">solde total</div>
        <?php endif; ?>
    </div>
    <div class="metric">
        <div class="metric-top"><div class="metric-icon mi-amber"><i class="fas fa-hourglass-half"></i></div></div>
        <div class="metric-val"><?= $en_attente ?? 0 ?></div>
        <div class="metric-label">En attente</div>
    </div>
    <div class="metric">
        <div class="metric-top"><div class="metric-icon mi-green"><i class="fas fa-check-circle"></i></div></div>
        <div class="metric-val"><?= $approuvees ?? 0 ?></div>
        <div class="metric-label">Approuvées</div>
    </div>
    <div class="metric">
        <div class="metric-top"><div class="metric-icon mi-red"><i class="fas fa-times-circle"></i></div></div>
        <div class="metric-val"><?= 0 ?></div>
        <div class="metric-label">Refusées</div>
    </div>
</div>

<?php if (!empty($soldes)): ?>
<div class="data-card">
    <div class="data-card-head"><h3><i class="fas fa-coins" style="color:var(--forest);margin-right:6px"></i>Mes soldes de congés — <?= date('Y') ?></h3></div>
    <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem">
        <?php foreach ($soldes as $s): ?>
        <?php $pct = (int) $s['jours_attribues'] > 0 ? round(((int) $s['jours_pris'] / (int) $s['jours_attribues']) * 100) : 0; ?>
        <div class="solde-card" style="margin:0">
            <div class="solde-header">
                <span class="solde-type"><?= esc($s['libelle']) ?></span>
                <span class="solde-nums"><strong><?= (int) $s['reste'] ?></strong> / <?= (int) $s['jours_attribues'] ?> j</span>
            </div>
            <div class="solde-bar">
                <div class="solde-fill <?= $pct >= 100 ? 'danger' : ($pct >= 75 ? 'warn' : '') ?>" style="width:<?= min($pct, 100) ?>%"></div>
            </div>
            <div class="solde-label"><?= (int) $s['reste'] ?> jours restants · <?= (int) $s['jours_pris'] ?> pris</div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="content-grid-wide">
    <div class="data-card" style="margin:0">
        <div class="data-card-head">
            <h3>Mes dernières demandes</h3>
            <a href="<?= base_url('employe/mes-demandes') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout →</a>
        </div>
        <?php if (!empty($demandes)): ?>
        <table class="tbl">
            <thead>
                <tr><th>Type</th><th>Du</th><th>Au</th><th>Durée</th><th>Statut</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach ($demandes as $d): ?>
                <tr>
                    <td><span class="type-badge t-<?= strpos($d['libelle'], 'annuel') !== false ? 'annuel' : (strpos($d['libelle'], 'maladie') !== false ? 'maladie' : (strpos($d['libelle'], 'spécial') !== false || strpos($d['libelle'], 'special') !== false ? 'special' : 'sans-solde')) ?>"><?= esc($d['libelle']) ?></span></td>
                    <td class="td-muted"><?= date('d/m/Y', strtotime($d['date_debut'])) ?></td>
                    <td class="td-muted"><?= date('d/m/Y', strtotime($d['date_fin'])) ?></td>
                    <td class="td-mono"><?= (int) $d['nb_jours'] ?> j</td>
                    <td><span class="statut s-<?= $d['statut'] === 'en_attente' ? 'attente' : ($d['statut'] === 'approuvee' ? 'approuvee' : ($d['statut'] === 'refusee' ? 'refusee' : 'annulee')) ?>"><?= $d['statut'] === 'en_attente' ? 'en attente' : ($d['statut'] === 'approuvee' ? 'approuvée' : ($d['statut'] === 'refusee' ? 'refusée' : 'annulée')) ?></span></td>
                    <td>
                        <?php if ($d['statut'] === 'en_attente'): ?>
                        <form action="<?= base_url('employe/annuler/' . $d['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Annuler cette demande ?')">
                            <?= csrf_field() ?>
                            <button class="btn-sm btn-cancel"><i class="fas fa-times"></i> Annuler</button>
                        </form>
                        <?php else: ?>
                        <span class="td-muted" style="font-size:.75rem">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty">
            <i class="fas fa-calendar-times"></i>
            <p>Aucune demande pour le moment.</p>
        </div>
        <?php endif; ?>
    </div>

    <div style="display:flex;flex-direction:column;gap:1rem">
        <?php if (!empty($soldes)): ?>
        <div class="data-card" style="margin:0">
            <div class="data-card-head"><h3><i class="fas fa-piggy-bank" style="color:var(--forest);margin-right:5px"></i>Vue rapide</h3></div>
            <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.75rem">
                <?php foreach (array_slice($soldes, 0, 3) as $s): ?>
                <?php $pct = (int) $s['jours_attribues'] > 0 ? round(((int) $s['jours_pris'] / (int) $s['jours_attribues']) * 100) : 0; ?>
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                        <span style="font-size:.8rem;color:var(--ink)"><?= esc($s['libelle']) ?></span>
                        <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--forest);font-weight:500"><?= (int) $s['reste'] ?> j</span>
                    </div>
                    <div class="solde-bar"><div class="solde-fill <?= $pct >= 100 ? 'danger' : ($pct >= 75 ? 'warn' : '') ?>" style="width:<?= min($pct, 100) ?>%"></div></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <div class="flash flash-info" style="margin:0">
            <i class="fas fa-info-circle"></i>
            <span style="font-size:.8rem">Le solde est déduit uniquement à l'approbation de votre responsable.</span>
        </div>
    </div>
</div>

<?php if (!empty($stats_par_type)): ?>
<div class="data-card">
    <div class="data-card-head"><h3>Historique & statistiques</h3></div>
    <table class="tbl">
        <thead>
            <tr><th>Type de congé</th><th>Total de demandes</th></tr>
        </thead>
        <tbody>
            <?php foreach ($stats_par_type as $stat): ?>
            <tr>
                <td class="td-name"><?= esc($stat['libelle']) ?></td>
                <td class="td-mono"><?= (int) $stat['total'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div class="data-card">
    <div class="empty">
        <i class="fas fa-chart-bar"></i>
        <p>Aucune statistique disponible pour le moment.</p>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>
