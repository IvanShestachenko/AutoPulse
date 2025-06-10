<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>AutoPulse</title>
</head>
<body>
    <?php
    /**
     * @file
     * @brief Script for displaying the main website page. Includes components. Displays error message pop-up in case of error context parameter set in GET request.
     * 
     * 'session_invalid_id' and 'wrong_admin_rights' errors occurence means that the user managed to access the interface elements that
     * require proper user authorization (of the levels of 'authorized user' of 'admin') without one, and leads to the mandatory
     * interrupt of the current session and logout of the user.
     */
    if (isset($_GET['error'])){
            include "getDataErrorPopup.php";
    }
    include "header.php";
    include "searchbar.php";
    include "get_insertions.php";
    include "footer.html";
    ?>
</body>
</html>