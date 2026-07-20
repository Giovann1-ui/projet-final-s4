<?= $this->extend('Layout/app') ?>
<?= $this->section('title') ?>Mon solde<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Mon solde de congés<?= $this->endSection() ?>
<?= $this->section('breadcrumb') ?><a href="<?= base_url('employe') ?>">Accueil</a> <i class="fas fa-chevron-right" style="font-size:.6rem"></i> Mon solde<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php if (empty($soldes)): ?>
<div class="data-card">
    <div class="empty">
        <i class="fas fa-coins"></i>
        <p>Aucun solde disponible.</p>
    </div>
</div>
<?php else: ?>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1rem;margin-bottom:1.5rem">
    <?php foreach ($soldes as $s): ?>
    <?php $pct = (int) $s['jours_attribues'] > 0 ? round(((int) $s['jours_pris'] / (int) $s['jours_attribues']) * 100) : 0; ?>
    <div class="solde-card" style="margin:0">
        <div class="solde-header">
            <span class="solde-type"><?= esc($s['libelle']) ?></span>
            <span class="solde-nums"><strong><?= (int) $s['reste'] ?></strong> / <?= (int) $s['jours_attribues'] ?> j</span>
        </div>
        <div class="solde-bar"><div class="solde-fill <?= $pct >= 100 ? 'danger' : ($pct >= 75 ? 'warn' : '') ?>" style="width:<?= min($pct, 100) ?>%"></div></div>
        <div class="solde-label"><?= (int) $s['reste'] ?> jours restants · <?= (int) $s['jours_pris'] ?> pris · Année <?= (int) $s['annee'] ?></div>
    </div>
    <?php endforeach; ?>
</div>

<div class="data-card">
    <div class="data-card-head"><h3>Détail des soldes</h3></div>
    <table class="tbl">
        <thead>
            <tr><th>Type de congé</th><th>Année</th><th>Attribués</th><th>Pris</th><th>Reste</th><th>Utilisation</th></tr>
        </thead>
        <tbody>
            <?php foreach ($soldes as $s): ?>
            <?php $pct = (int) $s['jours_attribues'] > 0 ? round(((int) $s['jours_pris'] / (int) $s['jours_attribues']) * 100) : 0; ?>
            <tr>
                <td class="td-name"><?= esc($s['libelle']) ?></td>
                <td class="td-mono"><?= (int) $s['annee'] ?></td>
                <td class="td-mono"><?= (int) $s['jours_attribues'] ?></td>
                <td class="td-mono"><?= (int) $s['jours_pris'] ?></td>
                <td><span class="td-mono" style="font-weight:500;color:<?= (int) $s['reste'] <= 0 ? 'var(--danger)' : ((int) $s['reste'] <= 2 ? 'var(--warn)' : 'var(--success)') ?>"><?= (int) $s['reste'] ?> j</span></td>
                <td>
                    <div style="display:flex;align-items:center;gap:8px">
                        <div class="solde-bar" style="flex:1;max-width:120px"><div class="solde-fill <?= $pct >= 100 ? 'danger' : ($pct >= 75 ? 'warn' : '') ?>" style="width:<?= min($pct, 100) ?>%"></div></div>
                        <span style="font-size:.72rem;color:var(--muted)"><?= $pct ?>%</span>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<?= $this->endSection() ?>
