
<?= defined("LOGGED_MSG") ? "<p>".LOGGED_MSG."</p>" : "" ?>

<form method="post" name="loginForm">

    <input type="text" name="identifier" placeholder="Identifiant" <?= isset($_GET["email"]) ? "value='".$_GET["email"]."'" : ""; ?> required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="checkbox" name="rememberMe" value="rememberMe">
    <label for="rememberMe">Remember me</label>

    <input type="submit" name="submit">

</form>

<p>Don't have an Account? <a href="register">Register</a></p>