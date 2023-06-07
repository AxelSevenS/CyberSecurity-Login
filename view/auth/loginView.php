<form method="post" name="loginForm">

    <?php 
        if(session_id() === "") {
            session_start();
        }
        if (isset($_SESSION['user'])): 
    ?>
        <span>You are already logged in.</span>
        <a href="/logout">Logout</a>
        <br>
    <?php 
        endif; 
    ?>

    <input type="text" name="identifier" placeholder="Identifiant" <?= isset($_GET["email"]) ? "value='".$_GET["email"]."'" : ""; ?> required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="checkbox" name="rememberMe" value="rememberMe">
    <label for="rememberMe">Remember me</label>

    <input type="submit" name="submit">

</form>
<p>Don't have an Account? <a href="register">Register</a></p>