<?= view('operateur/partials/header', ['activePage' => 'creer']) ?>

<h3 class="mb-4"><i class="bi bi-plus-circle"></i> Préfixes opérateur</h3>

<form action="<?= base_url('operateur/creer') ?>" method="post" class="row g-2 mb-4">
    <?= csrf_field() ?>
    <div class="col-8 col-md-9">
        <label for="prefixe" class="visually-hidden">Préfixe</label>
        <input type="text" id="prefixe" name="prefixe" class="form-control form-control-lg" placeholder="Ex: 034" required>
    </div>
    <div class="col-4 col-md-3">
        <button type="submit" class="btn btn-primary btn-lg w-100">Valider</button>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr><th>Code préfixe</th></tr>
        </thead>
        <tbody>
            <?php if (empty($liste)): ?>
                <tr><td class="text-center text-muted">Aucun préfixe enregistré.</td></tr>
            <?php else: ?>
                <?php foreach ($liste as $item): ?>
                    <tr><td><?= esc($item['code']) ?></td></tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= view('operateur/partials/footer') ?>
