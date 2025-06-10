<?php
/**
 * @file
 * @brief This script retrieves the unqiue values of the 'make' column of the 'models' table and sends them as .json response.
*/

/**
 * Sets the content type for the response to JSON.
 */
header('Content-Type: application/json');

require "connection.php";

/**
 * Checks for a database access error and terminates the script if it occurs.
 */
if (isset($error) && $error === "db_access_failed"){
    die(json_encode(['error' => 'Database connection failed']));
}

try{
    /**
     * Queries the database to fetch distinct car makes.
     */
    $stmt = $pdo->query('SELECT DISTINCT make FROM models');
    $makes = [];

    /**
     * Loops through the query results and collects distinct makes into an array.
     */
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $makes[] = $row['make'];
    }

    /**
     * Encodes the collected makes as a JSON array and outputs it.
     */
    echo json_encode($makes);
} catch (PDOException $e) {
    /**
     * Handles query execution errors by returning a JSON-encoded error message.
     */
    echo json_encode(['error' => 'Query execution failed']);
}

