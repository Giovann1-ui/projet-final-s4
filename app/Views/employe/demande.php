<?= $this->extend('Layout/app') ?>
<?= $this->section('title') ?>Nouvelle demande<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Nouvelle demande de congé<?= $this->endSection() ?>
<?= $this->section('breadcrumb') ?><a href="<?= base_url('employe') ?>">Accueil</a> <i class="fas fa-chevron-right" style="font-size:.6rem"></i> Nouvelle demande<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-grid-2">
    <div>
        <div class="form-section">
            <h3>Détails de la demande</h3>
            <form action="<?= base_url('employe/demande') ?>" method="post">
                <?= csrf_field() ?>
                <div class="f-group">
                    <label class="f-label">Type de congé <span class="required">*</span></label>
                    <select name="type_conge_id" class="f-select <?= $validation && $validation->hasError('type_conge_id') ? 'is-invalid' : '' ?>" required>
                        <option value="">— Sélectionner —</option>
                        <?php foreach ($types as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= old('type_conge_id') == $t['id'] ? 'selected' : '' ?>>
                            <?= esc($t['libelle']) ?> (reste: <?= $soldeParType[$t['id']] ?? 'N/A' ?> j)
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($validation && $validation->hasError('type_conge_id')): ?>
                    <div class="f-error"><?= $validation->getError('type_conge_id') ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-grid-2">
                    <div class="f-group">
                        <label class="f-label">Date de début <span class="required">*</span></label>
                        <input type="date" name="date_debut" class="f-input <?= $validation && $validation->hasError('date_debut') ? 'is-invalid' : '' ?>" value="<?= old('date_debut') ?>" required>
                        <?php if ($validation && $validation->hasError('date_debut')): ?>
                        <div class="f-error"><?= $validation->getError('date_debut') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Date de fin <span class="required">*</span></label>
                        <input type="date" name="date_fin" class="f-input <?= $validation && $validation->hasError('date_fin') ? 'is-invalid' : '' ?>" value="<?= old('date_fin') ?>" required>
                        <?php if ($validation && $validation->hasError('date_fin')): ?>
                        <div class="f-error"><?= $validation->getError('date_fin') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="f-group">
                    <label class="f-label">Motif <span style="font-weight:400;color:var(--muted)">(optionnel)</span></label>
                    <textarea name="motif" rows="3" class="f-textarea" placeholder="Précisez le motif de votre demande si nécessaire..."><?= old('motif') ?></textarea>
                    <div class="f-hint">Le motif est visible par le responsable RH.</div>
                </div>

                <div class="form-actions">
                    <button class="btn-forest" type="submit"><i class="fas fa-paper-plane"></i> Soumettre la demande</button>
                    <a href="<?= base_url('employe') ?>" class="btn-secondary"><i class="fas fa-times"></i> Annuler</a>
                </div>
            </form>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:1rem">
        <?php if (!empty($soldes)): ?>
        <div class="data-card" style="margin:0">
            <div class="data-card-head"><h3><i class="fas fa-piggy-bank" style="color:var(--forest);margin-right:5px"></i>Vos soldes actuels</h3></div>
            <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.75rem">
                <?php foreach ($soldes as $s): ?>
                <?php $pct = (int) $s['jours_attribues'] > 0 ? round(((int) $s['jours_pris'] / (int) $s['jours_attribues']) * 100) : 0; ?>
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                        <span style="font-size:.8rem;color:var(--ink)"><?= esc($s['libelle']) ?></span>
                        <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:<?= $s['reste'] <= 2 ? 'var(--warn)' : 'var(--forest)' ?>;font-weight:500"><?= (int) $s['reste'] ?> j</span>
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
        <div style="background:var(--cream);border:1px solid var(--border);border-radius:8px;padding:.85rem 1rem">
            <div style="font-size:.78rem;font-weight:500;color:var(--ink);margin-bottom:.5rem"><i class="fas fa-clipboard-check" style="color:var(--forest);margin-right:5px"></i>Rappel des règles</div>
            <ul style="margin:0;padding-left:1rem;font-size:.75rem;color:var(--muted);line-height:1.7">
                <li>Préavis minimum : 48h avant la date de début</li>
                <li>Pas de chevauchement avec une demande en cours</li>
                <li>Solde insuffisant = demande refusée automatiquement</li>
            </ul>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
