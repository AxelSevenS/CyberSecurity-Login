<!DOCTYPE html>

<html lang="fr">
    <head>
        <script src="/public/js/hashFormPasswords.js" type="module" defer></script>
        <title><?= $title ?? "Sécurité" ?></title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <div>
            <a href="/login">Login</a>
            <a href="/register">Register</a>
            <a href="/account">Account</a>
            <a href="/logout">Logout</a>
        </div>
        <br>
        <br>
        <?= isset($_GET["error"]) ? "<p>".$_GET["error"]."</p>" : "" ?>
        <?= $content ?>
    </body>
</html>