<?php
/**
 * @file
 * @brief Script for dynamically constructing the error message interface and content according to the error context taken as a GET request parameter.
 *
 * This script evaluates error context based on GET parameter, determining the type of error
 * and generating the corresponding message content, button links, and button labels. 
 * It provides error feedback for registration, login, data publishing, or editing operations, and customizes the displayed buttons accordingly.
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

$message_content = "";
$right_button_text = "Domovská stránka";
$right_button_link = "index.php";
$left_button_declare = '<span class="retryButton" id="retryButton">Zkusit znovu</span>';
$close_popup_button_declare = '<span class="closeButton" id="closeButton">&#x2716;</span>';
if (isset($_GET["error"])){
    if ($_GET["error"] == "reg_db_access_failed" || $_GET["error"] == "reg_user_add_failed") {
        $message_content = "<p>Nepodařilo se zaregistrovat.<br>Prosím, zkuste později.</p>";
    } elseif ($_GET["error"] == "login_db_access_failed" || $_GET["error"] == "login_user_validation_failed") {
        $message_content = "<p>Nepodařilo se přihlásit.<br>Prosím, zkuste později.</p>";
    } elseif ($_GET["error"] == "reg_user_input_invalid" || 
      $_GET["error"] == "login_user_input_invalid" || 
      $_GET["error"] == "publish_user_input_invalid") {
        $message_content = "<p>Nesprávný formát vstupu.<br>Prosím, zkuste znovu.</p>";
    } elseif ($_GET["error"] == "editdata_user_input_invalid") {
        $message_content = "<p>Nesprávný formát vstupu.<br>Prosím, zkuste znovu.</p>";
        $right_button_text = "Můj profil";
        $right_button_link = "myprofile.php";
    } elseif ($_GET["error"] == "editdata_db_access_failed" || 
      $_GET["error"] == "editdata_insert_failed") {
        $message_content = "<p>Nepodařilo se upravit údaje.<br>Prosím, zkuste později.</p>";
        $right_button_text = "Můj profil";
        $right_button_link = "myprofile.php";
    } elseif ($_GET["error"] == "session_invalid_id") {
        $message_content = "<p>Nesprávné id uživatele.<br>Prosím, zopakujte své přihlašovací údaje.</p>";
        $right_button_text = "Zrušit";
        $right_button_link = "process-logout.php";
        $left_button_declare = '<a href="login.php" class="retryButton" id="retryButton">Přihlásit se znovu</a>';
        $close_popup_button_declare = '<a href="login.php" class="closeButton" id="closeButton">&#x2716;</a>';
    } elseif ($_GET["error"] == "publish_db_access_failed" || 
      $_GET["error"] == "publish_insertion_failed") {
        $message_content = "<p>Nepodařilo se vložit inzerát.<br>Prosím, zkuste později.</p>";
    } elseif ($_GET["error"] == "publish_error_loading_files") {
        $message_content = "<p>Chyba při nahrání souborů.<br>Prosím, zkuste znovu.</p>";
    }
} else {
    exit;
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
                <?php echo $close_popup_button_declare; ?>
            </div>
            <?php echo $message_content; ?>
            <div class="button-container">
                <?php echo $left_button_declare; ?>
                <a href="<?php echo $right_button_link?>" class="homePageButton"><?php echo $right_button_text?></a>
            </div>
        </div>
    </div>
    <script src="../scripts/authFailurePopUp.js" defer></script>
</body>
</html>
