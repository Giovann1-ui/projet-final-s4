<?= view('client/partials/header', ['activePage' => 'promotion']) ?>

<h3 class="mb-4"><i class="bi bi-percent"></i> Epargne pourcentage</h3>

<p class="text-muted">
    Ce pourcentage deduit les montant de transfert pour transformer  en epargne
</p>

<form action="<?= base_url('client/epargne') ?>" method="post" class="row g-2 mb-4">
    <?= csrf_field() ?>
    <div class="col-8 col-md-9">
        <label for="pourcentage" class="visually-hidden">Pourcentage</label>
        <div class="input-group input-group-lg">
            <input
                type="number"
                id="pourcentage"
                name="pourcentage"
                class="form-control"
                step="0.01"
                min="0"
                max="100"
                value="<?= esc(old('pourcentage') ?? (string) $pourcentage) ?>"
                required
            >
            <span class="input-group-text">%</span>
        </div>
    </div>
    <div class="col-4 col-md-3">
        <button type="submit" class="btn btn-primary btn-lg w-100">Valider</button>
    </div>
</form>

