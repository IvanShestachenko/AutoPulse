<?php
/**
 * @file
 * @brief This script handles the deletion of the user account initiated by the user himself.
 * 
 * Connects to the database, checks if the user with id of the one set in $_SESSION['user_id'] exists in 'users' table of the database,
 * deletes the record if true. Terminates and redirects to the main page in case of error
 *  with error context provided as a parameter of the redirecting GET request.
 */

/**
 * Redirects to the index page with a specified error parameter and terminates the script.
 *
 * @param string $errorParam The error parameter to be appended to the URL.
 */
function terminateAccountDeleteWithError($errorParam){
    header("Location: index.php?error=$errorParam");
    exit;
}

session_start();

// Get the user ID from the session.
$user_id = $_SESSION['user_id'];

require "connection.php";

/**
 * Check if there was a database access error and terminate the account deletion process if so.
 */
if (isset($error) && $error === "db_access_failed"){
    terminateAccountDeleteWithError("accountdelete_db_access_failed");
}

try {
    // Check if the user exists in the database.
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $userExists = $stmt->fetchColumn();

    // If the user ID is invalid, terminate the account deletion process.
    if (!$userExists){
        terminateAccountDeleteWithError('session_invalid_id');
    }

    // Delete the user record from the database.
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

} catch(PDOException $e){
    // Handle database deletion errors.
    terminateAccountDeleteWithError('accountdelete_deletion_failed');
}

// Clear session data.
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, 
        $params["path"], $params["domain"], 
        $params["secure"], $params["httponly"]
    );
}
session_unset();
session_destroy();

try {
    // Attempt to delete the user record from the database again (duplicate logic).
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
} catch(PDOException $e){
    // Handle errors if the second deletion attempt fails.
    terminateAccountDeleteWithError('accountdelete_deletion_failed');
}

// Redirect to the index page after successful account deletion.
header("Location: index.php");
