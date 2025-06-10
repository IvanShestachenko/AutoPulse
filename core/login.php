<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>AutoPulse</title>
    <link rel="stylesheet" href="../styles/auth.css">
</head>
<body>
    <?php
    /**
         * @file
         * @brief Form for user login.
         * 
         * In case of form being returned by the processing script, triggers popup message providing context of the occured error,
         * and pre-fills form fields with the previous inputs of the user. Pre-fill is secured by htmlspecialchars() function to prevent XSS attempts.  
        */
    if (isset($_GET["error"]) && $_GET["error"] != "login_user_invalid"){
                include "authFailurePopUp.php";} ?>
    <?php include "header-plain.html"?>
    <div class="auth-container">
        <h1>Přihlášení</h1>
        <p class="login-link">Nemáte účet? <a href="register.php">Zaregistrovat se</a></p>
        <form action="process-login.php" method="POST" id="auth-form">
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? '');?>">
                <div class="info hidden" id="email-empty">! Tato položka je nutná k vyplnění</div>
                <div class="error hidden" id="email-wrongformat">! Nesprávný formát e-mailu.</div>
            </div>
            <div class="form-group">
                <label for="password">Heslo:</label>
                <input type="password" id="password" name="password">
                <div class="info hidden" id="password-empty">! Tato položka je nutná k vyplnění</div>
                <div class="error hidden" id="password-wrongformat">! Nesprávný formát hesla. Musí být 8 až 32 znaků dlouhé a obsahovat nejméně jednu číslici a jeden speciální znak.</div>
                <div class="error"><?php if (isset($_GET['error']) && $_GET['error'] == "login_user_invalid"):
                        echo 'Nesprávné heslo nebo e-mail.'; endif;?></div>
            </div>

            <button type="submit" class="btn-login">Přihlásit se</button>
        </form>
    </div>
    <?php include "footer.html" ?>
    <script src="../scripts/login.js" defer></script>
</body>
</html>