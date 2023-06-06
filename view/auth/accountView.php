<form method="post" name="accountForm">

    <input type="text" name="identifier" placeholder="Identifiant" <?php echo isset($_GET["email"]) ? "value='".$_GET["email"]."'" : ""; ?> required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="checkbox" name="rememberMe" value="rememberMe">
    <label for="rememberMe">Remember me</label>

    <input type="submit" name="submit">

</form>
<p>Don't have an Account? <a href="register">Register</a></p>