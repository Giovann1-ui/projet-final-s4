<?php
    $anciensNumeros = old('num_tels_dest') ?: ['', ''];
    if (count($anciensNumeros) < 2) {
        $anciensNumeros = array_pad($anciensNumeros, 2, '');
    }
?>
<?= view('client/partials/header', ['activePage' => 'transfert']) ?>

<h3 class="mb-4"><i class="bi bi-people"></i> Envoi multiple</h3>
<p class="text-muted">
    Envoyez un montant réparti également entre plusieurs numéros. Réservé aux destinataires du même opérateur que vous.
</p>

<form method="post" action="<?= base_url('client/envoi-multiple') ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">Numéros des destinataires (même opérateur)</label>
        <div id="destinataires-list">
            <?php foreach ($anciensNumeros as $numero): ?>
            <div class="input-group mb-2 destinataire-row">
                <input
                    type="text"
                    name="num_tels_dest[]"
                    class="form-control"
                    placeholder="Numéro du destinataire"
                    value="<?= esc($numero) ?>"
                    required
                >
                <button type="button" class="btn btn-outline-danger btn-remove-destinataire" <?= count($anciensNumeros) <= 2 ? 'disabled' : '' ?>>
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="btn-add-destinataire" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-plus-lg"></i> Ajouter un numéro
        </button>
    </div>

    <div class="mb-3">
        <label class="form-label">Montant total à répartir (Ar)</label>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    var list = document.getElementById('destinataires-list');
    var addBtn = document.getElementById('btn-add-destinataire');

    function updateRemoveButtons() {
        var rows = list.querySelectorAll('.destinataire-row');
        rows.forEach(function (row) {
            row.querySelector('.btn-remove-destinataire').disabled = rows.length <= 2;
        });
    }

    addBtn.addEventListener('click', function () {
        var row = document.createElement('div');
        row.className = 'input-group mb-2 destinataire-row';
        row.innerHTML =
            '<input type="text" name="num_tels_dest[]" class="form-control" placeholder="Numéro du destinataire" required>' +
            '<button type="button" class="btn btn-outline-danger btn-remove-destinataire"><i class="bi bi-x-lg"></i></button>';
        list.appendChild(row);
        updateRemoveButtons();
    });

    list.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-remove-destinataire');
        if (!btn) {
            return;
        }
        if (list.querySelectorAll('.destinataire-row').length > 2) {
            btn.closest('.destinataire-row').remove();
            updateRemoveButtons();
        }
    });

    updateRemoveButtons();
});
</script>

<?= view('client/partials/footer') ?>
