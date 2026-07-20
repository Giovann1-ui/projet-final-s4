<?= view('operateur/partials/header', ['activePage' => 'gains']) ?>

<h3 class="mb-4"><i class="bi bi-graph-up"></i> Situation des gains</h3>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="total-box text-center">
            <div>Gains sur retraits</div>
            <div class="montant"><?= number_format((float) ($totalGainsRetrait ?? 0), 0, ',', ' ') ?> Ar</div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="total-box text-center">
            <div>Gains sur transferts</div>
            <div class="montant"><?= number_format((float) ($totalGainsTransfert ?? 0), 0, ',', ' ') ?> Ar</div>
        </div>
    </div>
</div>

<h5 class="mb-3">Opérations de retrait</h5>
<div class="table-responsive mb-4">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Id du client</th>
                <th class="text-end">Montant brut</th>
                <th class="text-end">Frais/Gain</th>
                <th class="text-end">Total sortant</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listeOperationsRetrait)): ?>
                <tr><td colspan="4" class="text-center text-muted">Aucune opération.</td></tr>
            <?php else: ?>
                <?php foreach ($listeOperationsRetrait as $operation): ?>
                <tr>
                    <td><?= esc($operation['client_source'] ?? '—') ?></td>
                    <td class="text-end"><?= number_format((float) ($operation['montant_brut'] ?? 0), 0, ',', ' ') ?> Ar</td>
                    <td class="text-end"><?= number_format((float) ($operation['frais'] ?? 0), 0, ',', ' ') ?> Ar</td>
                    <td class="text-end"><?= number_format((float) ($operation['montant_sortant'] ?? 0), 0, ',', ' ') ?> Ar</td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<h5 class="mb-3">Opérations de transfert</h5>
<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Client source</th>
                <th>Client destination</th>
                <th class="text-end">Montant brut</th>
                <th class="text-end">Frais/Gain</th>
                <th class="text-end">Montant sortant</th>
                <th class="text-end">Montant entrant</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listeOperationsTransfert)): ?>
                <tr><td colspan="6" class="text-center text-muted">Aucune opération.</td></tr>
            <?php else: ?>
                <?php foreach ($listeOperationsTransfert as $operation): ?>
                <tr>
                    <td><?= esc($operation['client_source'] ?? '—') ?></td>
                    <td><?= esc($operation['client_dest'] ?? '—') ?></td>
                    <td class="text-end"><?= number_format((float) ($operation['montant_brut'] ?? 0), 0, ',', ' ') ?> Ar</td>
                    <td class="text-end"><?= number_format((float) ($operation['frais'] ?? 0), 0, ',', ' ') ?> Ar</td>
                    <td class="text-end"><?= number_format((float) ($operation['montant_sortant'] ?? 0), 0, ',', ' ') ?> Ar</td>
                    <td class="text-end"><?= number_format((float) ($operation['montant_entrant'] ?? 0), 0, ',', ' ') ?> Ar</td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= view('operateur/partials/footer') ?>
