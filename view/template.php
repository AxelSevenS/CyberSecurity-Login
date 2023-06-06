<DOCTYPE html>

<html lang="fr">
    <head>
        <!-- <link rel="stylesheet" href="public/css/style.css"> -->
        <script src="/public/js/hashFormPasswords.js" type="module" defer></script>
        <title><?= $title ?? "Sécurité" ?></title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php if (isset($error) && $error != ""): ?>
            <p><?= $error ?></p>
        <?php endif; ?>
        <?= $content ?>
    </body>
</html>