<?php
/**
 * @file
 * @brief This script handles reflecting in the database the fact that the user has requested for the admin rights.
 * 
 * Connects to the database. Checks if there exists a record in 'users' table with id equal to the one set in $_SESSION['user_id'].
 * If true, updates the value of 'admin_requested' column to 'true' and redirects back user profile page. Terminates and redirects
 * to the main page in case of error with error context provided as a parameter of the redirecting GET request.
 */

/**
 * Redirects to the index page with a specified error parameter and terminates the script.
 *
 * @param string $errorParam The error parameter to be appended to the URL.
 */
function terminateARRWithError($errorParam){
    header("Location: index.php?error=$errorParam");
    exit;
}

session_start();

require "connection.php";

/**
 * Check if there was a database access error and terminate the request if so.
 */
if (isset($error) && $error === "db_access_failed"){
    terminateARRWithError("arr_db_access_failed");
}

try {
    // Check if the user exists in the database.
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $userExists = $stmt->fetchColumn();

    // If the user ID is invalid, terminate the request.
    if (!$userExists){
        terminateARRWithError('session_invalid_id');
    }

    // Update the user's record to mark the admin request.
    $stmt = $pdo->prepare("
    UPDATE users 
    SET 
        admin_requested = :admin_requested
    WHERE id = :id
    ");
    $stmt->execute([
        'admin_requested' => 1,
        'id' => $_SESSION['user_id']
    ]);

} catch(PDOException $e){
    // Handle errors if the database update fails.
    terminateARRWithError('arr_insertion_failed');
}

// Redirect to the profile page after a successful update.
header("Location: myprofile.php");