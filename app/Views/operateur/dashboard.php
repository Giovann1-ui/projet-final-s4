<?= view('operateur/partials/header', ['activePage' => 'dashboard']) ?>

<h3 class="mb-4"><i class="bi bi-speedometer2"></i> Tableau de bord opérateur</h3>

<div class="row g-3">
    <div class="col-12 col-md-4">
        <a href="<?= base_url('operateur/creer') ?>" class="text-decoration-none">
            <div class="op-box">
                <div class="libelle">Préfixes opérateur</div>
                <div class="montant"><i class="bi bi-plus-circle"></i> Gérer</div>
            </div>
        </a>
    </div>
    <div class="col-12 col-md-4">
        <a href="<?= base_url('operateur/situation-gains') ?>" class="text-decoration-none">
            <div class="op-box">
                <div class="libelle">Gains</div>
                <div class="montant"><i class="bi bi-graph-up"></i> Situation des gains</div>
            </div>
        </a>
    </div>
    <div class="col-12 col-md-4">
        <a href="<?= base_url('operateur/situation-clients') ?>" class="text-decoration-none">
            <div class="op-box">
                <div class="libelle">Clients</div>
                <div class="montant"><i class="bi bi-people"></i> Situation des clients</div>
            </div>
        </a>
    </div>
</div>

<?= view('operateur/partials/footer') ?>
