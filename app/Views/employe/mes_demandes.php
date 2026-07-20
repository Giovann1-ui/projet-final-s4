<?= $this->extend('Layout/app') ?>
<?= $this->section('title') ?>Mes demandes<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Mes demandes de congé<?= $this->endSection() ?>
<?= $this->section('breadcrumb') ?><a href="<?= base_url('employe') ?>">Accueil</a> <i class="fas fa-chevron-right" style="font-size:.6rem"></i> Mes demandes<?= $this->endSection() ?>
<?= $this->section('page_actions') ?>
<a href="<?= base_url('employe/demande') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem">
    <i class="fas fa-plus"></i> Nouvelle demande
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="data-card">
    <div class="data-card-head">
        <h3>Toutes mes demandes</h3>
    </div>
    <?php if (!empty($demandes)): ?>
    <table class="tbl">
        <thead>
            <tr><th>Type</th><th>Début</th><th>Fin</th><th>Durée</th><th>Statut</th><th>Commentaire RH</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php foreach ($demandes as $d): ?>
            <tr>
                <td><span class="type-badge t-<?= strpos($d['libelle'], 'annuel') !== false ? 'annuel' : (strpos($d['libelle'], 'maladie') !== false ? 'maladie' : (strpos($d['libelle'], 'spécial') !== false || strpos($d['libelle'], 'special') !== false ? 'special' : 'sans-solde')) ?>"><?= esc($d['libelle']) ?></span></td>
                <td class="td-muted"><?= date('d/m/Y', strtotime($d['date_debut'])) ?></td>
                <td class="td-muted"><?= date('d/m/Y', strtotime($d['date_fin'])) ?></td>
                <td class="td-mono"><?= (int) $d['nb_jours'] ?> j</td>
                <td><span class="statut s-<?= $d['statut'] === 'en_attente' ? 'attente' : ($d['statut'] === 'approuvee' ? 'approuvee' : ($d['statut'] === 'refusee' ? 'refusee' : 'annulee')) ?>"><?= $d['statut'] === 'en_attente' ? 'en attente' : ($d['statut'] === 'approuvee' ? 'approuvée' : ($d['statut'] === 'refusee' ? 'refusée' : 'annulée')) ?></span></td>
                <td style="font-size:.78rem;color:<?= $d['statut'] === 'approuvee' ? 'var(--success)' : ($d['statut'] === 'refusee' ? 'var(--danger)' : 'var(--muted)') ?>">
                    <?= esc($d['commentaire_rh'] ?? '—') ?>
                </td>
                <td>
                    <?php if ($d['statut'] === 'en_attente'): ?>
                    <form action="<?= base_url('employe/annuler/' . $d['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Annuler cette demande de congé ?')">
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
        <p>Vous n'avez encore soumis aucune demande de congé.</p>
        <a href="<?= base_url('employe/demande') ?>" class="btn-forest"><i class="fas fa-plus"></i> Nouvelle demande</a>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
