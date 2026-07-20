<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Créer un Opérateur</h1>
    <!-- <?= base_url('operateur/creer') ?> -->
    <?php if (!empty($liste)) { foreach ($liste as $list) { ?>
        <p><?= $list['code'] ?></p>
    <?php } } ?>
    <form action="<?= base_url('operateur/creer') ?>" method="post">
        <?= csrf_field() ?>
         <label for="prefixe">Préfixe</label>
         <input type="text" id="prefixe" name="prefixe">
         <input type="submit" value="Valider">
    </form>
</body>
</html>