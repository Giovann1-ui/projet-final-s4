<?= view('client/partials/header', ['activePage' => 'transfert']) ?>

<h3 class="mb-4"><i class="bi bi-arrow-left-right"></i> Transfert</h3>
<p class="text-muted">Des frais s'ajoutent au montant transféré, à votre charge. Le destinataire reçoit exactement le montant saisi.</p>

<form method="post" action="<?= base_url('client/transfert') ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">Numéro du destinataire</label>
        <input type="text" name="num_tel_dest" class="form-control form-control-lg" value="<?= esc(old('num_tel_dest')) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Montant à transférer (Ar)</label>
        <input type="number" step="1" min="1" name="montant" class="form-control form-control-lg" value="<?= esc(old('montant')) ?>" required>
    </div>
    <button type="submit" class="btn btn-primary btn-lg w-100">Valider le transfert</button>
</form>

<?= view('client/partials/footer') ?>
