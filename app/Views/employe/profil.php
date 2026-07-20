<?= $this->extend('Layout/app') ?>
<?= $this->section('title') ?>Mon profil<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Mon profil<?= $this->endSection() ?>
<?= $this->section('breadcrumb') ?><a href="<?= base_url('employe') ?>">Accueil</a> <i class="fas fa-chevron-right" style="font-size:.6rem"></i> Mon profil<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-grid-2" style="grid-template-columns:1fr 1fr">
    <div>
        <div class="form-section">
            <h3><i class="fas fa-user" style="color:var(--forest);margin-right:6px"></i>Informations personnelles</h3>
            <form action="<?= base_url('employe/profil') ?>" method="POST">
                <?= csrf_field() ?>

                <?php if ($validation): ?>
                <div class="flash flash-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul style="list-style:none;padding:0;margin:0">
                    <?php foreach ($validation as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <div class="f-group">
                    <label class="f-label">Département</label>
                    <input type="text" class="f-input" value="<?= esc($employe_info['departement_nom'] ?? '-') ?>" disabled style="background:var(--cream);color:var(--muted)">
                </div>
                <div class="form-grid-2">
                    <div class="f-group">
                        <label class="f-label">Prénom <span class="required">*</span></label>
                        <input type="text" name="prenom" class="f-input" value="<?= old('prenom', $employe_info['prenom']) ?>" required>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Nom <span class="required">*</span></label>
                        <input type="text" name="nom" class="f-input" value="<?= old('nom', $employe_info['nom']) ?>" required>
                    </div>
                </div>
                <div class="f-group">
                    <label class="f-label">Email <span class="required">*</span></label>
                    <input type="email" name="email" class="f-input" value="<?= old('email', $employe_info['email']) ?>" required>
                </div>
                <div class="f-group">
                    <label class="f-label">Nouveau mot de passe <span style="font-weight:400;color:var(--muted)">(laisser vide pour ne pas changer)</span></label>
                    <input type="password" name="password" class="f-input" minlength="6" placeholder="••••••••">
                </div>
                <div class="form-actions" style="justify-content:flex-end">
                    <button type="submit" class="btn-forest"><i class="fas fa-save"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
    <div>
        <div class="data-card" style="margin:0">
            <div class="data-card-head"><h3><i class="fas fa-id-card" style="color:var(--forest);margin-right:5px"></i>Résumé</h3></div>
            <div style="padding:1.25rem;text-align:center">
                <div class="avatar" style="width:64px;height:64px;font-size:1.3rem;margin:0 auto 1rem;background:var(--forest2)"><?= strtoupper(substr($employe_info['prenom'] ?? '', 0, 1) . substr($employe_info['nom'] ?? '', 0, 1)) ?></div>
                <div style="font-size:1.1rem;font-weight:600;color:var(--ink)"><?= esc($employe_info['prenom'] . ' ' . $employe_info['nom']) ?></div>
                <div style="font-size:.85rem;color:var(--muted);margin-bottom:1rem"><?= esc($employe_info['email']) ?></div>
                <div style="display:flex;justify-content:center;gap:1.5rem;flex-wrap:wrap">
                    <div><div style="font-size:.75rem;color:var(--muted)">Rôle</div><div style="font-weight:500"><?= $employe_info['role'] === 'admin' ? 'Administrateur' : ($employe_info['role'] === 'rh' ? 'RH' : 'Employé') ?></div></div>
                    <div><div style="font-size:.75rem;color:var(--muted)">Département</div><div style="font-weight:500"><?= esc($employe_info['departement_nom'] ?? '-') ?></div></div>
                    <div><div style="font-size:.75rem;color:var(--muted)">Embauche</div><div style="font-weight:500"><?= date('d/m/Y', strtotime($employe_info['date_embauche'])) ?></div></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
