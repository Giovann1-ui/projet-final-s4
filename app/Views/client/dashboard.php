<?= view('client/partials/header', ['activePage' => 'dashboard']) ?>

<h3 class="mb-4"><i class="bi bi-wallet2"></i> Mon solde</h3>

<div class="total-box mb-4 text-center">
    <div>Solde disponible</div>
    <div class="montant"><?= number_format($solde['solde'], 0, ',', ' ') ?> Ar</div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="op-box">
            <div class="libelle">Reçu</div>
            <div class="montant text-success"><?= number_format($solde['entrant'], 0, ',', ' ') ?> Ar</div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="op-box">
            <div class="libelle">Envoyé</div>
            <div class="montant text-danger"><?= number_format($solde['sortant'], 0, ',', ' ') ?> Ar</div>
        </div>
    </div>
</div>

<div class="row g-2">
    <div class="col-6 col-md-3">
        <a href="<?= base_url('client/depot') ?>" class="btn btn-outline-primary w-100"><i class="bi bi-plus-circle"></i> Dépôt</a>
    </div>
    <div class="col-6 col-md-3">
        <a href="<?= base_url('client/retrait') ?>" class="btn btn-outline-primary w-100"><i class="bi bi-dash-circle"></i> Retrait</a>
    </div>
    <div class="col-6 col-md-3">
        <a href="<?= base_url('client/transfert') ?>" class="btn btn-outline-primary w-100"><i class="bi bi-arrow-left-right"></i> Transfert</a>
    </div>
    <div class="col-6 col-md-3">
        <a href="<?= base_url('client/historique') ?>" class="btn btn-outline-primary w-100"><i class="bi bi-clock-history"></i> Historique</a>
    </div>
</div>

<?= view('client/partials/footer') ?>
