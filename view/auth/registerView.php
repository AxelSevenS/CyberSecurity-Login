
<?= defined("LOGGED_MSG") ? "<p>".LOGGED_MSG."</p>" : "" ?>

<form method="post" name="registerForm">

    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="password" name="confirmPassword" placeholder="Confirm Password" required>

    <input type="submit" name="submit">

</form>

<p>Already have an account? <a href="login">Login</a></p>