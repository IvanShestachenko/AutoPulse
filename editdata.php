<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>AutoPulse</title>
    <link rel="stylesheet" href="styles/auth.css">
</head>
<body>
    <?php
        /**
         * @file
         * @brief Form for user data editing.
         * 
         * In case of form being returned by the processing script, triggers popup message providing context of the occured error,
         * and pre-fills form fields with the previous inputs of the user. Pre-fill is secured by htmlspecialchars() function to prevent XSS attempts.  
        */

        if (isset($_GET["error"]) && $_GET['error'] != "editdata_wrong_password"){
            include "authFailurePopUp.php";
        }
        include "header-plain.html"; ?>
    <div class="auth-container">
        <h1>Úprava údajů</h1>
        <form action="process-editdata.php" method="POST" id="auth-form">
            <input type="text" class="hidden" id="user-type" name="person_type" value="<?php echo ($_GET['person_type'] ?? '');?>">
            <?php if (isset($_GET['person_type']) && $_GET['person_type'] === 'private'):?>
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
            <?php elseif (isset($_GET['person_type']) && $_GET['person_type'] === 'company'):?>
                <div id="company-fields">
                    <div class="form-group">
                        <label for="company-name">Název společnosti:</label>
                        <input type="text" id="company-name" name="company_name" value="<?php echo htmlspecialchars($_GET['company_name'] ?? ''); ?>">
                        <div class="info hidden" id="companyname-empty">! Tato položka je nutná k vyplnění</div>
                        <div class="error hidden" id="companyname-wrongformat">! Nesprávný formát názvu společnosti.</div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="current-password">Původní heslo:</label>
                <input type="password" id="current-password" name="current_password">
                <div class="info hidden" id="current-password-empty">! Tato položka je nutná k vyplnění</div>
                <div class="error hidden" id="current-password-wrongformat">! Nesprávný formát hesla. Musí být 8 až 32 znaků dlouhé a obsahovat nejméně jednu číslici a jeden speciální znak.</div>
                <div class="error"><?php if (isset($_GET['error']) && $_GET['error'] == "editdata_wrong_password"):
                            echo 'Nesprávné heslo'; endif;?></div>
            </div>

            <div class="form-group">
                <label for="new-password">Nové heslo:</label>
                <input type="password" id="new-password" name="new_password">
                <div class="info hidden" id="new-password-empty">! Tato položka je nutná k vyplnění</div>
                <div class="error hidden" id="new-password-wrongformat">! Nesprávný formát hesla. Musí být 8 až 32 znaků dlouhé a obsahovat nejméně jednu číslici a jeden speciální znak.</div>
            </div>

            <div class="form-group">
                <label for="confirm-password">Potvrzení hesla:</label>
                <input type="password" id="confirm-password" name="confirm_password">
                <div class="info hidden" id="confirmpassword-empty">! Tato položka je nutná k vyplnění</div>
                <div class="error hidden" id="confirmpassword-wrong">! Hesla se neshodují.</div>
            </div>

            <button type="submit" class="btn-login">Uložit</button>
        </form>
    </div>
    <?php include "footer.html" ?>
    <script src="scripts/editdata.js" defer></script>
</body>
</html>