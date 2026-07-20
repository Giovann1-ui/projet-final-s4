<?= view('client/partials/header', ['activePage' => 'depot']) ?>

<h3 class="mb-4"><i class="bi bi-plus-circle"></i> Dépôt</h3>

<form method="post" action="<?= base_url('client/depot') ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">Montant à déposer (Ar)</label>
        <input type="number" step="1" min="1" name="montant" class="form-control form-control-lg" value="<?= esc(old('montant')) ?>" required>
    </div>
    <button type="submit" class="btn btn-primary btn-lg w-100">Valider le dépôt</button>
</form>

<?= view('client/partials/footer') ?>
