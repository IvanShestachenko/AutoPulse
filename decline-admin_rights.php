<?php
/**
 * @file
 * @brief This script handles the decline of user requests for admin rights by updating the value of `admin` column in users database.
 * 
 * Connects to the database and updates the value of 'admin_requested' column of 'users' table of the database back 0, thus giving
 * back the ability to the user to make another request for the admin rights. The 'admin' column value (0) stays unchanged,
 * as the request for the admin rights is declined in this case. 
 * Terminates in case of connection- or update- error and redirects to the main page with an error context info as
 * a parameter of the redirecting GET request.
*/

/**
 * Redirects the user to a specified location with an error parameter.
 *
 * @param string $errorParam The error parameter to append to the URL.
 */
function terminateDeclineAdminWithError($errorParam){
    header("Location: index.php?error=$errorParam");
    exit;
}

require "connection.php";

/**
 * Checks if an error occurred during database connection and redirects with an error parameter.
 */
if (isset($error)){
    terminateDeclineAdminWithError("decline_admin_db_access_failed");
}

try {
    /**
     * Prepares and executes an SQL statement to update the admin_requested flag for a user.
     * 
     * The user ID is retrieved from the $_GET superglobal array.
     */
    $stmt = $pdo->prepare("
    UPDATE users 
    SET 
        admin_requested = 0
    WHERE id = :id
    ");
    $stmt->execute([
        'id' => $_GET['id']
    ]);
} catch (PDOException $e) {
    /**
     * Handles PDO exceptions by redirecting with an error parameter.
     */
    terminateDeclineAdminWithError("decline_admin_update_data_failed");
}

/**
 * Redirects to the admin rights requests page upon successful execution.
 */
header("Location: get_admin_rights_requests.php");
