<?php
/**
 * @file
 * @brief Script for dynamically constructing the error message interface and content according to the error context taken as a GET request parameter.
 *
 * This script evaluates error context based on GET parameter, determining the type of error
 * and generating the corresponding message content, button links, and button labels. 
 * It is used to inform users about issues related to their profile, account, 
 * administrative permissions, or other operations.
 */

/** 
 * @var string $message_content
 * HTML content of the message displayed to the user in case of an error.
 */
$message_content = "";

/** 
 * @var string $right_button_link
 * URL for the right button displayed in the interface.
 */
$right_button_link = "";

/** 
 * @var string $left_button_link
 * URL for the left button displayed in the interface.
 */
$left_button_link = "";

/** 
 * @var string $left_button_text
 * Text label for the left button displayed in the interface.
 */
$left_button_text = "Zkusit znovu";

// Logic for determining error context and constructing the interface.

if (isset($_GET["error"])){
        if ($_GET["error"] == "profile_db_access_failed" || $_GET["error"] == "profile_user_getdata_failed") {
                $message_content = "<p>Nepodařilo se zobrazit Váš profil.<br>Prosím, zkuste později.</p>";
                $left_button_link = "myprofile.php";
                $right_button_link = "index.php";

        } elseif ($_GET["error"] == "accountdelete_db_access_failed" || $_GET["error"] == "accountdelete_deletion_failed") {
                $message_content = "<p>Nepodařilo se smázat Váš účet.<br>Prosím, zkuste později.</p>";
                $left_button_link = "process-accountDelete.php";
                $right_button_link = "index.php";
        } elseif ($_GET["error"] == "arr_db_access_failed" || $_GET["error"] == "arr_insertion_failed") {
                $message_content = "<p>Operace neúspěšná.<br>Prosím, zkuste později.</p>";
                $left_button_link = "process-adminRightsRequest.php";
                $right_button_link = "index.php";
        } elseif ($_GET["error"] == "session_invalid_id") {
                $message_content = "<p>Nesprávné id uživatele.<br>Prosím, zopakujte své přihlašovací údaje.</p>";
                $right_button_text = "Zrušit";
                $right_button_link = "process-logout.php";
                $left_button_text = "Přihlásit se znovu";
                $left_button_link = "login.php";
        } elseif ($_GET["error"] == "wrong_admin_rights") {
                $message_content = "<p>Chyba oprávnění správce.<br>Prosím, zopakujte své přihlašovací údaje.</p>";
                $right_button_text = "Zrušit";
                $right_button_link = "process-logout.php";
                $left_button_text = "Přihlásit se znovu";
                $left_button_link = "login.php";
        } elseif ($_GET["error"] == "insertion_does_not_exist") {
                $message_content = "<p>Inzerát neexistuje</p>";
                $right_button_text = "Zrušit";
                $right_button_link = "index.php";
                $left_button_text = "Ok";
                $left_button_link = "index.php";
        } elseif ($_GET["error"] == "ins_getdata_failed") {
                $message_content = "<p>Nepodařilo se zobrazit inzerát</p>";
                $right_button_text = "Zrušit";
                $right_button_link = "index.php";
                $left_button_text = "Ok";
                $left_button_link = "index.php";
        } elseif ($_GET["error"] == "db_access_failed") {
                $message_content = "<p>Činnost neúspěšná.</p>";
                $right_button_text = "Zrušit";
                $right_button_link = "index.php";
                $left_button_text = "Ok";
                $left_button_link = "index.php";
        }      
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel='stylesheet' href="../styles/getDataErrorPopup.css">
</head>
<body>
    <div class="popupOverlay" id="popUpOverlay">
        <div class="popupWindow">
            <div class="close-container">
                <a href="<?php echo $right_button_link; ?>" class="closeButton" id="closeButton">&#x2716;</a>
            </div>
            <?php echo $message_content; ?>
            <div class="button-container">
                <a href="<?php echo $left_button_link; ?>" class="retryButton" id="retryButton"><?php echo $left_button_text; ?></a>
                <a href="<?php echo $right_button_link; ?>" class="homePageButton">Zrušit</a>
            </div>
        </div>
    </div>
    <script src="../scripts/authFailurePopUp.js" defer></script>
</body>
</html>