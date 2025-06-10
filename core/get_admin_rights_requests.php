<?php

/**
 * @file
 * @brief This script executes retrieving and displaying of the user requests for admin rights with options to aprrove or decline them.
 * 
 * Checks if 'user_id' and 'admin' is set in $_SESSION. Connects to the database. Checks if the user with the user_id set in $_SESSION has in
 * fact the admin rights by checking the value of 'admin' column in 'users' table of the database for the current 'user_id' in $_SESSION.
 * If the user is a valid website admin, retrieves all the users from the database, whose admin_requested == 1, sorting the retrieved records
 * email alphabetically. Displays the results. Buttons of 'approve' and 'decline' are available next to the each displayed user 
 * to approve of decline their requests for admin rights.
 * In case of invalid user_id, invaid admin rights or error occuring during the communication with the database, terminates 
 * and redirects to the main page with an error context info as a parameter of the redirecting GET request.   
*/

/**
 * Redirects the user to a specified location with an error parameter.
 *
 * @param string $errorParam The error parameter to append to the URL.
 */
function terminateGARRWithError($errorParam){
    header("Location: index.php?error=$errorParam");
    exit;
}

session_start();

/**
 * Checks if the user session contains a valid user ID.
 * If not, redirects with an error parameter indicating an invalid session.
 */
if (!isset($_SESSION['user_id'])){
    terminateGARRWithError('session_invalid_id');
}

/**
 * Checks if the user has admin rights.
 * If not, redirects with an error parameter indicating insufficient rights.
 */
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1){
    terminateGARRWithError('wrong_admin_rights');
}

require 'connection.php';

/**
 * Verifies if there was a database access failure during connection setup.
 * If such an error occurred, redirects with an error parameter.
 */
if (isset($error) && $error === "db_access_failed"){
    terminateGARRWithError("garr_db_access_failed");
}

try {
    /**
     * Prepares and executes an SQL statement to retrieve the admin status of the logged-in user.
     * 
     * The user ID is retrieved from the session data. If the user is not an admin,
     * the script redirects with an error parameter.
     */
    $stmt = $pdo->prepare("SELECT admin FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user || (int)$user['admin'] != 1) {
        terminateGARRWithError("wrong_admin_rights");
    }  
} catch (PDOException $e) {
    /**
     * Handles PDO exceptions that occur while fetching the admin status by redirecting with an error parameter.
     */
    terminateGARRWithError("garr_getdata_failed");
}

try {
    /**
     * Prepares and executes an SQL statement to retrieve a list of users who have requested admin rights.
     * 
     * Only users with admin_requested set to 1 and admin set to 0 are included, and the results
     * are ordered by email.
     */
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email FROM users WHERE admin_requested = 1 AND admin = 0 ORDER BY email");
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    /**
     * Handles PDO exceptions that occur while retrieving admin requests by redirecting with an error parameter.
     */
    terminateGARRWithError("garr_getdata_failed");
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <link rel='stylesheet' href="../styles/get_admin_rights_requests.css">
    <title>AutoPulse</title>
</head>
<body>
    <?php include "header.php"; ?>

    <div class="external-container">
        <div class="feed-header">
            <div class="feed-name">Žádosti o oprávnění správce</div>
            <form action="get_admin_rights_requests.php" method="get" id="filter-form">
                <label for="filter-select">Seřadit podle:</label>
                <select id="filter-select" class="feed-filter">
                    <option value="date">Email</option>
                </select>
            </form>
        </div>
        
        <?php if (count(value: $records) === 0):?><div class="error-message"><?php echo "Žádosti nenalezeny."; ?></div>
        <?php else: ?>
            <div class="requests-container">
            <?php foreach ($records as $record): ?>
            <div class="single-request">
                <a href="approve-admin_rights.php?id=<?php echo $record['id']; ?>" class="admin-button">Schválit</a>
                <a href="decline-admin_rights.php?id=<?php echo $record['id']; ?>" class="admin-button">Zamítnout</a>
                <div class="user-email"><?php echo htmlspecialchars($record['email'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div><?php echo htmlspecialchars($record['first_name'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($record['last_name'], ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php include "footer.html"; ?>
</body>
</html>


