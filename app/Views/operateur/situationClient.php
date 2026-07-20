<?= view('operateur/partials/header', ['activePage' => 'clients']) ?>

<h3 class="mb-4"><i class="bi bi-people"></i> Situation des comptes clients</h3>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="op-box">
            <div class="libelle">Clients suivis</div>
            <div class="montant"><?= count($listeClients ?? []) ?></div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="op-box">
            <div class="libelle">Solde total cumulé</div>
            <div class="montant"><?= number_format(array_sum(array_map(static fn ($client) => (float) ($client['solde'] ?? 0), $listeClients ?? [])), 0, ',', ' ') ?> Ar</div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Numéro</th>
                <th>Date d'inscription</th>
                <th class="text-end">Solde</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listeClients)): ?>
                <tr><td colspan="4" class="text-center text-muted">Aucun client disponible.</td></tr>
            <?php else: ?>
                <?php foreach ($listeClients as $client): ?>
                <tr>
                    <td><?= esc($client['num_tel']) ?></td>
                    <td><?= !empty($client['date_inscription']) ? date('d/m/Y H:i', strtotime($client['date_inscription'])) : '—' ?></td>
                    <td class="text-end <?= (float) $client['solde'] < 0 ? 'text-danger' : 'text-success' ?>">
                        <?= number_format((float) $client['solde'], 0, ',', ' ') ?> Ar
                    </td>
                    <td>
                        <a href="<?= base_url('operateur/situation-clients/' . $client['id']) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> Détails
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= view('operateur/partials/footer') ?>
