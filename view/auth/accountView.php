
<?php

if ( !isset($payload) || !isset($payload['sub']) || !isset($payload['name']) || !isset($payload['email']) ) {
    // define("ERROR_MSG", "You are not logged in");
    header('Location: /login?error=You are not logged in');
    exit;
}

?>


<p>UserID: <?= $payload['sub'] ?></p>
<p>Username: <?= $payload['name'] ?></p>
<p>Email: <?= $payload['email'] ?></p>
<a href="/logout">Logout</a>

<form action="/modifyPassword" method="post" name="modifyAccountForm">

    <input type="password" name="newPassword" placeholder="New Password" required>
    <label for="newPassword">New Password</label>
    <br>
    <input type="password" name="newPasswordConfirm" placeholder="Confirm new Password" required>
    <label for="newPasswordConfirm">Confirm New Password</label>
    <br>
    <input type="password" name="oldPassword" placeholder="Old Password" required>
    <label for="oldPassword">Old Password</label>
    <br>

    <input type="submit" name="submit">

</form>