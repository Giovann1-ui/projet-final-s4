<?= view('client/partials/header', ['activePage' => 'retrait']) ?>

<h3 class="mb-4"><i class="bi bi-dash-circle"></i> Retrait</h3>
<p class="text-muted">Des frais s'ajoutent au montant retiré selon le barème en vigueur.</p>

<form method="post" action="<?= base_url('client/retrait') ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">Montant à retirer (Ar)</label>
        <input type="number" step="1" min="1" name="montant" class="form-control form-control-lg" value="<?= esc(old('montant')) ?>" required>
    </div>
    <button type="submit" class="btn btn-primary btn-lg w-100">Valider le retrait</button>
</form>

<?= view('client/partials/footer') ?>
