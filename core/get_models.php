<?php
/**
 * @file
 * @brief This script retrieves the values of the 'model' column of the 'models' table according to 'make' parameter of the GET request
 * and sends them back as .json response.
*/

/**
 * Sets the content type for the response to JSON.
 */
header('Content-Type: application/json');

/**
 * Checks if the 'make' parameter is provided in the request.
 * If not, returns an empty JSON array and exits the script.
 */
if (!isset($_GET['make']) || empty($_GET['make'])) {
    echo json_encode([]);
    exit;
}

require "connection.php";

/**
 * Checks for a database access error and terminates the script if it occurs.
 */
if (isset($error) && $error === "db_access_failed"){
    die(json_encode(['error' => 'Database connection failed']));
}

try{
    /**
     * Prepares a query to fetch distinct car models for the specified make.
     */
    $stmt = $pdo->prepare('SELECT DISTINCT model FROM models WHERE make = :make');

    /**
     * Binds the 'make' parameter from the GET request to the prepared query.
     */
    $stmt->bindParam(':make', $_GET['make'], PDO::PARAM_STR);
    $stmt->execute();

    $models = [];

    /**
     * Loops through the query results and collects distinct models into an array.
     */
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $models[] = $row['model'];
    }

    /**
     * Encodes the collected models as a JSON array and outputs it.
     */
    echo json_encode($models);
} catch (PDOException $e) {
    /**
     * Handles query execution errors by returning a JSON-encoded error message.
     */
    echo json_encode(['error' => 'Query execution failed']);
}
?>
