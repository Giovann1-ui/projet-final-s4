<?= view('operateur/partials/header', ['activePage' => 'gains']) ?>

<h3 class="mb-4"><i class="bi bi-graph-up"></i> Situation des gains</h3>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="total-box text-center">
            <div>Total Gains sur retraits</div>
            <div class="montant"><?= number_format((float) ($totalGainsRetraitToutOperateurs ?? 0), 0, ',', ' ') ?> Ar</div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="total-box text-center">
            <div>Total Gains sur transferts</div>
            <div class="montant"><?= number_format((float) ($totalGainsTransfertToutOperateurs ?? 0), 0, ',', ' ') ?> Ar</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="total-box text-center">
            <div>Nos Gains sur retraits</div>
            <div class="montant"><?= number_format((float) ($totalGainsRetrait ?? 0), 0, ',', ' ') ?> Ar</div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="total-box text-center">
            <div>Nos Gains sur transferts</div>
            <div class="montant"><?= number_format((float) ($totalGainsTransfert ?? 0), 0, ',', ' ') ?> Ar</div>
        </div>
    </div>
</div>

<h5 class="mb-3">Autres opérateurs</h5>
<div class="table-responsive mb-4">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Opérateur</th>
                <th class="text-end">Commission</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($autresOperateurs)): ?>
                <tr><td colspan="2" class="text-center text-muted">Aucun autre opérateur.</td></tr>
            <?php else: ?>
                <?php foreach ($autresOperateurs as $operateur): ?>
                <tr>
                    <td><?= esc($operateur['libelle'] ?? '—') ?></td>
                    <td class="text-end"><?= number_format((float) ($operateur['commission'] ?? 0), 0, ',', ' ') ?> %</td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<h5 class="mb-3">Opérations des autres opérateurs</h5>
<div class="table-responsive mb-4">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Type</th>
                <th>Client source</th>
                <th>Client destination</th>
                <th class="text-end">Montant brut</th>
                <th class="text-end">Frais</th>
                <th class="text-end">Commission</th>
                <th class="text-end">Montant sortant</th>
                <th class="text-end">Montant entrant</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listeOperationsAutres)): ?>
                <tr><td colspan="7" class="text-center text-muted">Aucune opération.</td></tr>
            <?php else: ?>
                <?php foreach ($listeOperationsAutres as $operation): ?>
                <tr>
                    <td><?= esc($operation['type_libelle'] ?? '—') ?></td>
                    <td><?= esc($operation['num_tel_source'] ?? $operation['client_source'] ?? '—') ?></td>
                    <td><?= esc($operation['num_tel_dest'] ?? $operation['client_dest'] ?? '—') ?></td>
                    <td class="text-end"><?= number_format((float) ($operation['montant_brut'] ?? 0), 0, ',', ' ') ?> Ar</td>
                    <td class="text-end"><?= number_format((float) ($operation['frais'] ?? 0), 0, ',', ' ') ?> Ar</td>
                    <td class="text-end"><?= number_format((float) ($operation['commission'] ?? 0), 0, ',', ' ') ?> %</td>
                    <td class="text-end"><?= number_format((float) ($operation['montant_sortant'] ?? 0), 0, ',', ' ') ?> Ar</td>
                    <td class="text-end"><?= number_format((float) ($operation['montant_entrant'] ?? 0), 0, ',', ' ') ?> Ar</td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Remplacer la section "Opérations de retrait" par celle-ci -->
<h5 class="mb-3">Opérations de retrait</h5>
<div class="table-responsive mb-4">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Numéro du client</th>
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
                    <td><?= esc($operation['num_tel_source'] ?? '—') ?></td>
                    <td class="text-end"><?= number_format((float) ($operation['montant_brut'] ?? 0), 0, ',', ' ') ?> Ar</td>
                    <td class="text-end"><?= number_format((float) ($operation['frais'] ?? 0), 0, ',', ' ') ?> Ar</td>
                    <td class="text-end"><?= number_format((float) ($operation['montant_sortant'] ?? 0), 0, ',', ' ') ?> Ar</td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Remplacer la section "Opérations de transfert" par celle-ci -->
<h5 class="mb-3">Opérations de transfert</h5>
<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Client source</th>
                <th>Client destination</th>
                <th class="text-end">Montant brut</th>
                <th class="text-end">Frais/Gain</th>
                <th class="text-end">Frais de retrait</th>
                <th class="text-end">Montant sortant</th>
                <th class="text-end">Montant entrant</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listeOperationsTransfert)): ?>
                <tr><td colspan="7" class="text-center text-muted">Aucune opération.</td></tr>
            <?php else: ?>
                <?php foreach ($listeOperationsTransfert as $operation): ?>
                <tr>
                    <td><?= esc($operation['num_tel_source'] ?? '—') ?></td>
                    <td><?= esc($operation['num_tel_dest'] ?? '—') ?></td>
                    <td class="text-end"><?= number_format((float) ($operation['montant_brut'] ?? 0), 0, ',', ' ') ?> Ar</td>
                    <td class="text-end"><?= number_format((float) ($operation['frais'] ?? 0), 0, ',', ' ') ?> Ar</td>
                    <td class="text-end"><?= number_format((float) ($operation['frais_retrait'] ?? 0), 0, ',', ' ') ?> Ar</td>
                    <td class="text-end"><?= number_format((float) ($operation['montant_sortant'] ?? 0), 0, ',', ' ') ?> Ar</td>
                    <td class="text-end"><?= number_format((float) ($operation['montant_entrant'] ?? 0), 0, ',', ' ') ?> Ar</td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= view('operateur/partials/footer') ?>
