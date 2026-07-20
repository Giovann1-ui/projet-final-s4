<?= view('client/partials/header', ['activePage' => 'transfert']) ?>

<h3 class="mb-4"><i class="bi bi-people"></i> Envoi multiple</h3>
<p class="text-muted">
    Envoyez un montant reparti egalement entre plusieurs numéros. Réservé aux destinataires du même opérateur que vous.
</p>

<form method="post" action="<?= base_url('client/envoi-multiple') ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">Numeros des destinataires (un par ligne, meme opérateur)</label>
        <textarea name="num_tels_dest" class="form-control" rows="5" placeholder="0331234567&#10;0331112222" required><?= esc(old('num_tels_dest')) ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Montant total a repartir (Ar)</label>
        <input type="number" step="1" min="1" name="montant" class="form-control form-control-lg" value="<?= esc(old('montant')) ?>" required>
    </div>
    <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="inclure_frais_retrait" name="inclure_frais_retrait" value="1" <?= old('inclure_frais_retrait') ? 'checked' : '' ?>>
        <label class="form-check-label" for="inclure_frais_retrait">
            Inclure les frais de retrait pour chaque destinataire.
        </label>
    </div>
    <button type="submit" class="btn btn-primary btn-lg w-100">Envoyer</button>
</form>

<?= view('client/partials/footer') ?>
