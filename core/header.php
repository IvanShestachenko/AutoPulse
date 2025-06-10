<!DOCTYPE html>
<html lang="cz">
<head>
    <meta charset="UTF-8">
    <link rel='stylesheet' href="../styles/header.css">
</head>
<body>
    <header>
        <?php 
        /**
         * @file
         * @brief Script for dispaying the page header. Most of the buttons are displayed and available to user according to his authorization status ('user_id' and 'admin' parameters of $_SESSION). 
         */
        ?>
        <a href="index.php" class="logo-link">
            <img class="logo-img" src="../logotypes&buttons/logo7.webp" alt="AutoPulse Logo">
        </a>
        <div class=".header-buttons-container">
            <a class="header-button" id="publishInsertionLink" href="publish-insertion.php">+Přidat inzerát</a>
            <?php if (isset($_SESSION['user_id'])): ?>
            <a class="header-button" id="myInsertionsLink" href="get_insertions.php?personal=true">Moje inzeráty</a>
            <?php endif; ?>
            <a class="header-button" id="myProfileLink" href="myprofile.php">Můj profil</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a id="logoutLink" class="header-button" href="process-logout.php">Odhlásit se</a>
                <?php include "confirmLogoutPopup.html" ?>
                <script src="../scripts/confirmPopup.js" defer></script>
            <?php else: ?>
                <a class="header-button" href="login.php">Prihlásit se</a>
                <script src="../scripts/loginPopUp.js"></script>
            <?php endif; ?>
            <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1):?>
                <a class="header-button" href="get_insertions.php?status=waiting">♕Inzeráty</a>
                <a class="header-button" href="get_admin_rights_requests.php">♕Uživatele</a>
            <?php endif?>
        </div>
    </header>
</body>
</html>