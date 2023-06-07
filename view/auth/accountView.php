
<h1>Account</h1>

<p>UserID: <?= $_SESSION['userID'] ?></p>
<p>Username: <?= $_SESSION['userName'] ?></p>
<p>Email: <?= $_SESSION['userEmail'] ?></p>
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