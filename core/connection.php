<?php
/**
 * @file
 * @brief This file handles the default database connection. Is imported in all the scripts that require one.
 * 
 * Creates PDO instance of database connection with particular host, database name, user name and user password parameters.
 * In case of unsuccesful connection, set $error value to provide error context for the importing scripts.
*/

// $host = 'localhost';
// $dbname = 'autopulse_db';
// $username = 'root';
// $password = '';

/** 
 * @var string $host The hostname of the database server.
 * @var string $dbname The name of the database to connect to.
 * @var string $username The username for authenticating with the database.
 * @var string $password The password for authenticating with the database.
 */
$host = 'localhost';
$dbname = 'shestiva';
$username = 'shestiva';
$password = '***************';

try {
    /**
     * Create a new PDO instance for connecting to the database.
     *
     * The connection uses the provided host, database name, username, and password.
     * UTF-8 character encoding is enforced, and exceptions are enabled for error handling.
     *
     * @var PDO $pdo The PDO instance for database operations.
     */
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    /**
     * Handle a failed database connection by setting an error message.
     *
     * @var string $error Error message indicating database access failure.
     */
    $error = "db_access_failed";
}
