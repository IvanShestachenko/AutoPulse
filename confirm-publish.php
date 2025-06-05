<?php
/**
 * @file
 * @brief This script handles the approval of publishing of the particular insertion gived by website administrators. Upadates insertion status from "waiting" to "published".
 * 
 * Connects to the database, updates the value of 'insertion_status' column of the 'insertions' table from 'waiting' to 'published' for
 * the insertion of a particular id provided as a parameter of GET request. Terminates in case of connection- or update- error and redirects to the main page with an error context info as
 * a parameter of the redirecting GET request.
*/

/**
 * Redirects to the index page with an error parameter.
 *
 * @param string $errorParam The error parameter to append to the URL.
 */
function terminateConfirmPublish($errorParam){
    header("Location: index.php?error=$errorParam");
    exit;
}

require "connection.php";

/** 
 * Check if there is an error, and terminate with an appropriate error message.
 */
if (isset($error)){
    terminateConfirmPublish("confirm_publish_db_access_failed");
}

/** 
 * @var string $insertion_status The status to update for the insertion, set to "published".
 * @var int $id The ID of the insertion retrieved from the GET request.
 */
$insertion_status = "published";
$id = $_GET['id'];

try {
    /**
     * Prepare and execute a SQL statement to update the insertion status.
     *
     * The `insertions` table is updated to set the `insertion_status` field to "published"
     * for the record with the provided `id` parameter from the GET request.
     */
    $stmt = $pdo->prepare("
    UPDATE insertions 
    SET 
        insertion_status = :insertion_status
    WHERE id = :id
    ");
    $stmt->execute([
        'insertion_status' => $insertion_status,
        'id' => $_GET['id']
    ]);
} catch (PDOException $e) {
    /**
     * Handle errors during the SQL execution by redirecting with an error message.
     */
    terminateConfirmPublish("confirm_publish_update_data_failed");
}

/**
 * Redirect to the page displaying insertions with a "waiting" status after successful execution.
 */
header("Location: get_insertions.php?status=waiting");
