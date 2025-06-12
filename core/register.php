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
     * @brief Form for user registration.
     * 
     * In case of form being returned by the processing script, triggers popup message providing context of the occured error,
     * and pre-fills form fields with the previous inputs of the user. Pre-fill is secured by htmlspecialchars() function to prevent XSS attempts.  
    */
    if (isset($_GET["error"]) && $_GET['error'] != "reg_email_taken"){
                include "authFailurePopUp.php";} ?>
    <a href="index.php" class="logo-reg-link">
                <img class="logo-reg-img" src="../assets/logotypes&buttons/logo7.webp" alt="AutoPulse Logo"></a>
    <div class="auth-container">
        <h1>Registrace</h1>
        <p class="login-link">Již máte účet? <a href="login.php">Přihlásit se</a></p>
        <form action="process-register.php" method="POST" id="auth-form">
            <div class="form-group">
                <label for="user-type">Vyberte typ osoby:</label>
                <select id="user-type" name="user_type">
                    <option value="private" selected>Soukromá osoba</option>
                    <option value="company">Společnost</option>
                </select>
            </div>

            <div id="private-fields">
                <div class="form-group">
                    <label for="first-name">Jméno:</label>
                    <input type="text" id="first-name" name="first_name" value="<?php echo htmlspecialchars($_GET['first_name'] ?? ''); ?>"> 
                    <div class="info hidden" id="firstname-empty">! Tato položka je nutná k vyplnění</div>
                    <div class="error hidden" id="firstname-wrongformat">! Nesprávný formát jména.</div>
                </div>
                <div class="form-group">
                    <label for="last-name">Příjmení:</label>
                    <input type="text" id="last-name" name="last_name" value="<?php echo htmlspecialchars($_GET['last_name'] ?? ''); ?>">
                    <div class="info hidden" id="lastname-empty">! Tato položka je nutná k vyplnění</div>
                    <div class="error hidden" id="lastname-wrongformat">! Nesprávný formát příjmení.</div>
                </div>
            </div>

            <div id="company-fields" class="hidden">
                <div class="form-group">
                    <label for="company-name">Název společnosti:</label>
                    <input type="text" id="company-name" name="company_name" value="<?php echo htmlspecialchars($_GET['company_name'] ?? ''); ?>">
                    <div class="info hidden" id="companyname-empty">! Tato položka je nutná k vyplnění</div>
                    <div class="error hidden" id="companyname-wrongformat">! Nesprávný formát názvu společnosti.</div>
                </div>
            </div>

            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? '');?>">
                <div class="info hidden" id="email-empty">! Tato položka je nutná k vyplnění</div>
                <div class="error hidden" id="email-wrongformat">! Nesprávný formát e-mailu.</div>
                <div class="error "><?php if (isset($_GET['error']) && $_GET['error'] == "reg_email_taken"):
                         echo 'Na tomto e-mailu již existuje účet AutoPulse. Použijte prosím jiný e-mail nebo přihlašte se.'; endif;?></div>
            </div>

            <div class="form-group">
                <label for="password">Heslo:</label>
                <input type="password" id="password" name="password">
                <div class="info hidden" id="password-empty">! Tato položka je nutná k vyplnění</div>
                <div class="error hidden" id="password-wrongformat">! Nesprávný formát hesla. Musí být 8 až 32 znaků dlouhé a obsahovat nejméně jednu číslici a jeden speciální znak.</div>
            </div>

            <div class="form-group">
                <label for="confirm-password">Potvrzení hesla:</label>
                <input type="password" id="confirm-password" name="confirm_password">
                <div class="info hidden" id="confirmpassword-empty">! Tato položka je nutná k vyplnění</div>
                <div class="error hidden" id="confirmpassword-wrong">! Hesla se neshodují.</div>
            </div>

            <button type="submit" class="btn-login">Zaregistrovat se</button>
        </form>
    </div>
    <?php include "footer.html" ?>
    <script src="../scripts/register.js" defer></script>
</body>
</html>