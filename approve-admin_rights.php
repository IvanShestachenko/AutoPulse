<?php
/**
 * @file
 * @brief This script handles the approval of a user request for admin rights by updating the value of `admin` column in users database.
 * 
 * Connects to the database and updates the `admin` column value for the record of user, whose id is provided as a parameter
 * of GET request. In case of success, redirects back to the page displaying all the user requests for admin rights. Terminates in case of connection- or update- error and redirects to the main page with an error context info as
 * a parameter of the redirecting GET request.
*/

/**
 * Redirects to the index page with an error parameter.
 *
 * @param string $errorParam The error parameter to append to the URL.
 */
function terminateApproveAdminWithError($errorParam){
    header("Location: index.php?error=$errorParam");
    exit;
}

require "connection.php";

/** 
 * Check if there is an error, and terminate with an appropriate error message.
 */
if (isset($error)){
    terminateApproveAdminWithError("approve_admin_db_access_failed");
}

try {
    /**
     * Prepare and execute a SQL statement to update the user's admin status.
     *
     * The `users` table is updated to set the `admin` field to 1 for the user
     * with the provided `id` parameter from the GET request.
     */
    $stmt = $pdo->prepare("
    UPDATE users 
    SET 
        admin = 1
    WHERE id = :id
    ");
    $stmt->execute([
        'id' => $_GET['id']
    ]);
} catch (PDOException $e) {
    /**
     * Handle errors during the SQL execution by redirecting with an error message.
     */
    terminateApproveAdminWithError("approve_admin_update_data_failed");
}

/**
 * Redirect to the page displaying admin rights requests after successful execution.
 */
header("Location: get_admin_rights_requests.php");
