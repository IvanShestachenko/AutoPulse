<?php
/**
 * @file
 * @brief This script handles the deletion of an insertion from the database.
 * 
 * It deletes a specific insertion identified by its ID and redirects the user based on the success or failure of the operation.
 */

/**
 * @brief Redirects the user to the main page with an error parameter.
 * 
 * @param string $errorParam The query parameter name indicating the error type.
 */
function terminateDeleteInsertion($errorParam){
    header("Location: index.php?$errorParam=true");
    exit;
}

require "connection.php";

/**
 * Checks if an error occurred during database connection and redirects with an error parameter.
 */
if (isset($error)){
    terminateDeleteInsertion("db_access_failed");
}

$id = $_GET['id']; /**< @var int ID of the insertion to delete. */

try {
    // Prepare and execute the delete query
    $stmt = $pdo->prepare("DELETE FROM insertions WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
} catch (PDOException $e) {
    // Redirect to the main page if the deletion fails
    terminateDeleteInsertion("delete_insertion_deletion_failed");
}

// Redirect to the main page after successful deletion
header("Location: index.php");
