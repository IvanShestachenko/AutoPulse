<?php
    /**
     * @file
     * @brief This scripts handles displaying user his profile with some account managing interface.
     * 
     * Checks if the $_SESSION['user_id'] is set as this functionality is only available to the authorized users. Connects to the database.
     * Checks if there exists a record with id equal to the one set in $_SESSION['user_id']. If true, retrieves the whole user record from the
     * 'users' table of the database and displays them. User first_name and last_name are displayed for the users of type 'private'. 
     * User 'company_name' is displayed for the users of type 'company' instead. Displays buttons that enable users to edit their 
     * account data or delete their account. For the users of type 'private', there's also a button that enables to make a request 
     * for admin rights, which is active and clickable, if the value of 'admin_requested' column for the current user is false.
     * Terminates and redirects to the main page in case of error with error context provided as a parameter of the redirecting GET request.
    */

    /**
     * @brief Starts a session and includes profile management functionality.
     */

    session_start();

    /**
     * @brief Redirects to the index page with an error parameter.
     * 
     * @param string $errorParam The error parameter to append to the URL.
     */
    function terminateProfileWithError($errorParam){
        header("Location: index.php?error=$errorParam");
        exit;
    }

    // Require the database connection file.
    require "connection.php";

    // Check for database access error and terminate if it exists.
    if (isset($error) && $error === "db_access_failed"){
        terminateProfileWithError("profile_db_access_failed");
    }

    try{
        /**
         * @brief Fetches user data from the database based on the session user ID.
         */
        $stmt = $pdo->prepare("SELECT person_type, first_name, last_name, company_name, email, admin_requested FROM users WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // If no user data is found, terminate with an error.
        if (!$user) {
            terminateProfileWithError("session_invalid_id");
        }  
    } catch (PDOException $e){
        // Terminate with an error if user data retrieval fails.
        terminateProfileWithError("profile_user_getdata_failed");
    }

    // Close the database connection.
    $pdo = null;

    /**
     * @brief Parameters for profile editing, initialized based on the user type.
     */
    $editdata_params = [];

    if ($user['person_type'] === 'private'){
        // Set parameters for private individuals.
        $editdata_params = [
            'person_type' => $user['person_type'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name']
        ];
    } elseif($user['person_type'] === 'company'){
        // Set parameters for companies.
        $editdata_params = [
            'person_type' => $user['person_type'],
            'company_name' => $user['company_name']
        ];
    }
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <link rel='stylesheet' href="../styles/myprofile.css">
    <title>AutoPulse</title>
</head>

<body>
    <?php include "header.php";?>
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-icon-container">
                <img class="profile-icon-img" src="../assets/logotypes&buttons/user_icon.png">
            </div>
            <div class="profile-details">
                <?php if (!($user['first_name'] === null)): ?>   
                    <div class="profile-detail">Jméno: <strong><?php echo htmlspecialchars($user['first_name']);?></strong></div>
                <?php endif; ?>
                <?php if (!($user['last_name'] === null)): ?>   
                    <div class="profile-detail">Příjmení: <strong><?php echo htmlspecialchars($user['last_name']); ?></strong></div>
                <?php elseif (!($user['company_name'] === null)): ?>   
                    <div class="profile-detail">Název společnosti: <strong><?php echo htmlspecialchars($user['company_name']); ?></strong></div>
                <?php endif; ?>
                <div class="profile-detail">Email: <?php echo htmlspecialchars($user['email']); ?></strong></div>
            </div>
        </div>
        <div class="profile-buttons">
            <a href="editdata.php?<?php echo http_build_query($editdata_params)?>" class="profile-button">Upravit údaje</a>

            <a href="process-accountDelete.php" id="deleteAccountButton" class="profile-button">Smazat účet</a>
            <?php include "confirmAccountDelete.html" ?>
            
            <?php if ($user['admin_requested'] === 0 && $user['person_type'] === 'private'):?>
                <a href="process-adminRightsRequest.php" class="profile-button">Požádat o roli správce</a>
            <?php elseif ($user['admin_requested'] === 1 && $user['person_type'] === 'private'):?>
                <a class="profile-button-disabled">O roli správce požádáno</a>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'footer.html'; ?>
</body>
</html>
