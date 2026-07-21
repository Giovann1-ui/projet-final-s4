<?= view('client/partials/header', ['activePage' => 'historique']) ?>

<h3 class="mb-4"><i class="bi bi-clock-history"></i> Historique</h3>

<form method="get" action="<?= base_url('client/historique') ?>" class="row g-2 mb-4">
    <div class="col-6 col-md-3">
        <select name="type" class="form-select">
            <option value="">Tous les types</option>
            <?php foreach (['DEPOT', 'RETRAIT', 'TRANSFERT'] as $t): ?>
                <option value="<?= esc($t) ?>" <?= $filtres['type'] === $t ? 'selected' : '' ?>><?= esc($t) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-6 col-md-3">
        <input type="date" name="date_debut" class="form-control" value="<?= esc($filtres['date_debut'] ?? '') ?>" placeholder="Du">
    </div>
    <div class="col-6 col-md-3">
        <input type="date" name="date_fin" class="form-control" value="<?= esc($filtres['date_fin'] ?? '') ?>" placeholder="Au">
    </div>
    <div class="col-6 col-md-3">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter"></i> Filtrer</button>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="op-box">
            <div class="libelle">Reçu</div>
            <div class="montant text-success"><?= number_format($totaux['entrant'], 0, ',', ' ') ?> Ar</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="op-box">
            <div class="libelle">Envoyé</div>
            <div class="montant text-danger"><?= number_format($totaux['sortant'], 0, ',', ' ') ?> Ar</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="op-box">
            <div class="libelle">Frais payés</div>
            <div class="montant"><?= number_format($totaux['frais'], 0, ',', ' ') ?> Ar</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="op-box">
            <div class="libelle">Opérations</div>
            <div class="montant"><?= (int) $totaux['nombre'] ?></div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Contrepartie</th>
                <th class="text-end">Montant</th>
                <th class="text-end">Frais</th>
                <th class="text-end">Sens</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($operations)): ?>
                <tr><td colspan="6" class="text-center text-muted">Aucune opération pour ces filtres.</td></tr>
            <?php endif; ?>
            <?php foreach ($operations as $op): ?>
                <?php $estEntrant = (int) $op['client_dest'] === session('client')['id']; ?>
                <tr>
                    <td><?= esc($op['date']) ?></td>
                    <td><?= esc($op['type_libelle']) ?></td>
                    <td><?= esc($estEntrant ? ($op['num_tel_source'] ?? '—') : ($op['num_tel_dest'] ?? '—')) ?></td>
                    <td class="text-end"><?= number_format((float) $op['montant_brut'], 0, ',', ' ') ?> Ar</td>
                    <td class="text-end"><?= number_format((float) $op['frais'], 0, ',', ' ') ?> Ar</td>
                    <td class="text-end">
                        <?php if ($estEntrant): ?>
                            <span class="text-success"><i class="bi bi-arrow-down-circle"></i> Reçu</span>
                        <?php else: ?>
                            <span class="text-danger"><i class="bi bi-arrow-up-circle"></i> Envoyé</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= view('client/partials/footer') ?>
