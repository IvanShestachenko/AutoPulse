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

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0; /**< @var int ID of the insertion to delete. */
$mediaDir = '../media/';
$avatarDir = '../avatar/';

try {
    // Retrieve the value of filepath to the avatar of the insertion that's being deleted
    $stmt_avatar_select = $pdo->prepare("SELECT `avatar_path` FROM insertions WHERE id = :id");
    $stmt_avatar_select->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_avatar_select->execute();
    $avatar_path = $stmt_avatar_select->fetchColumn();

    // Remove the file of the avatar image of the insertion
    $avatarFile = $avatarDir . $avatar_path;
    if (file_exists($avatarFile)) {
        unlink($avatarFile); 
    }

    // Retrieve the values of filepathes to all the images of the insertion that's being deleted
    $stmt_media_select = $pdo->prepare("SELECT `image_path` FROM images WHERE insertion_id = :id");
    $stmt_media_select->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_media_select->execute();
    $media_pathes = $stmt_media_select->fetchAll(PDO::FETCH_COLUMN);

    // Remove the files of all the images of the insertion
    foreach ($media_pathes as $file) {
        $filePath = $mediaDir . $file;
        if (file_exists($filePath)) {
            unlink($filePath); 
        }
    }

    // Prepare and execute the delete query
    $stmt_insertion_delete = $pdo->prepare("DELETE FROM insertions WHERE id = :id");
    $stmt_insertion_delete->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_insertion_delete->execute();
} catch (PDOException $e) {
    // Redirect to the main page if the deletion fails
    terminateDeleteInsertion("delete_insertion_deletion_failed");
}

// Redirect to the main page after successful deletion
header("Location: index.php");
