<?= view('operateur/partials/header', ['activePage' => 'clients']) ?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h3 class="mb-1"><?= esc($client['num_tel'] ?? 'Client inconnu') ?></h3>
        <div class="text-muted">ID client : <?= (int) ($clientId ?? 0) ?></div>
    </div>
    <a href="<?= base_url('operateur/situation-clients') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour à la liste
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="total-box text-center">
            <div>Solde total</div>
            <div class="montant"><?= number_format((float) ($soldeTotal ?? 0), 0, ',', ' ') ?> Ar</div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="op-box">
            <div class="libelle">Transactions</div>
            <div class="montant"><?= count($transactions ?? []) ?></div>
        </div>
    </div>
</div>

<h5 class="mb-3">Solde par type d'opération</h5>
<div class="table-responsive mb-4">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Type</th>
                <th class="text-end">Entrant</th>
                <th class="text-end">Sortant</th>
                <th class="text-end">Solde</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($soldeParTypeOperation)): ?>
                <tr><td colspan="4" class="text-center text-muted">Aucune opération trouvée pour ce client.</td></tr>
            <?php else: ?>
                <?php foreach ($soldeParTypeOperation as $ligne): ?>
                <tr>
                    <td><?= esc($ligne['libelle']) ?></td>
                    <td class="text-end"><?= number_format((float) $ligne['entrant'], 0, ',', ' ') ?> Ar</td>
                    <td class="text-end"><?= number_format((float) $ligne['sortant'], 0, ',', ' ') ?> Ar</td>
                    <td class="text-end <?= (float) $ligne['solde'] < 0 ? 'text-danger' : 'text-success' ?>">
                        <?= number_format((float) $ligne['solde'], 0, ',', ' ') ?> Ar
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<h5 class="mb-3">Transactions du client</h5>
<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Source</th>
                <th>Destination</th>
                <th class="text-end">Montant brut</th>
                <th class="text-end">Frais</th>
                <th class="text-end">Entrant</th>
                <th class="text-end">Sortant</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)): ?>
                <tr><td colspan="8" class="text-center text-muted">Aucune transaction disponible.</td></tr>
            <?php else: ?>
                <?php foreach ($transactions as $transaction): ?>
                <?php
                    $isIncoming = (int) ($transaction['client_dest'] ?? 0) === (int) ($clientId ?? 0);
                    $direction  = $isIncoming ? 'Entrée' : 'Sortie';
                ?>
                <tr>
                    <td><?= !empty($transaction['date']) ? date('d/m/Y H:i', strtotime($transaction['date'])) : '—' ?></td>
                    <td><?= esc($transaction['type_libelle'] ?? '—') ?> <span class="text-muted small"><?= $direction ?></span></td>
                    <td><?= $transaction['num_tel_source'] !== null ?  $transaction['num_tel_source'] : '—' ?></td>
                    <td><?= $transaction['num_tel_dest'] !== null ?  $transaction['num_tel_dest'] : '—' ?></td>
                    <td class="text-end"><?= number_format((float) ($transaction['montant_brut'] ?? 0), 0, ',', ' ') ?> Ar</td>
                    <td class="text-end"><?= number_format((float) ($transaction['frais'] ?? 0), 0, ',', ' ') ?> Ar</td>
                    <td class="text-end <?= $isIncoming ? 'text-success' : 'text-muted' ?>"><?= number_format((float) ($transaction['montant_entrant'] ?? 0), 0, ',', ' ') ?> Ar</td>
                    <td class="text-end <?= !$isIncoming ? 'text-danger' : 'text-muted' ?>"><?= number_format((float) ($transaction['montant_sortant'] ?? 0), 0, ',', ' ') ?> Ar</td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= view('operateur/partials/footer') ?>
