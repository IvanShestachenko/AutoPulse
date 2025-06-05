<?php

/**
 * @file
 * @brief This script provides validation of user editdata form inputs and updating user data in the database in case of successful validation.
 * 
 * Terminates and redirects back to the form page with error context parameter and all the user inputs except passwords as parameters of
 * the redirecting GET request in case of error of data validation or database communication.
 */

/**
 * Redirects to the editdata page with the specified error parameter and current user inputs except passwords, and terminates the script.
 *
 * @param string $errorParam The error parameter to be appended to the URL.
 */
function terminateEditdataWithError($errorParam){
    $postData = $_POST;
    $postData['error'] = $errorParam;
    unset($postData['current_password'], $postData['new_password'], $postData['confirm_password']);
    $queryString = http_build_query($postData);
    header("Location: editdata.php?$queryString");
    exit;
}

session_start();

/**
 * Validates input based on the provided regular expression pattern.
 *
 * @param string $pattern The regex pattern to validate against.
 * @param string $input The input to be validated.
 * @return bool True if the input matches the pattern, otherwise false.
 */
function validateInput($pattern, $input) {
    return preg_match($pattern, $input);
}

// Extract POST parameters with fallback to empty strings.
$person_type = $_POST['person_type'] ?? '';
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$company_name = $_POST['company_name'] ?? '';
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Define validation patterns.
$patterns = [
    'first_name' => '/^[a-zA-ZčČřŘžŽáÁíÍéÉěĚýÝůŮúÚóÓďĎťŤňŇ.\-]{2,20}$/',
    'last_name' => '/^[a-zA-ZčČřŘžŽáÁíÍéÉěĚýÝůŮúÚóÓďĎťŤňŇ.\-]{2,20}$/',
    'company_name' => '/^[a-zA-ZčČřŘžŽáÁíÍéÉěĚýÝůŮúÚóÓďĎťŤňŇ.\- ]{2,20}$/',
    'password' => '/^(?=.*[0-9])(?=.*[!@#$%^&*-_])[a-zA-Z0-9!@#$%^&*-_]{8,32}$/'
];

// Validate the form input.
$isFormValid = true;
if ($person_type === "private") {
    $isFormValid = validateInput($patterns['first_name'], $first_name) && $isFormValid;
    $isFormValid = validateInput($patterns['last_name'], $last_name) && $isFormValid;
} elseif ($user_type === 'company') {
    $isFormValid = validateInput($patterns['company_name'], $company_name) && $isFormValid;
}
$isFormValid = validateInput($patterns['password'], $current_password) && $isFormValid;
$isFormValid = validateInput($patterns['password'], $new_password) && $isFormValid;
$isFormValid = ($new_password == $confirm_password) && $isFormValid;

// If the form input is invalid, terminate with an error.
if (!$isFormValid){
    terminateEditdataWithError("editdata_user_input_invalid");
}

require "connection.php";

/**
 * Check if there was a database access error and terminate the process if so.
 */
if (isset($error) && $error === "db_access_failed"){
    terminateEditdataWithError("editdata_db_access_failed");
}

try {
    // Fetch the current password of the user.
    $stmt = $pdo->prepare("SELECT `password` FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user){
        // If the user session ID is invalid, terminate the process.
        terminateEditdataWithError("editdata_session_invalid_id");
    }

    // Verify if the provided current password matches the stored password.
    if (!password_verify($current_password, $user['password'])) {
        terminateEditdataWithError("editdata_wrong_password");
    }

    // Hash the new password.
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update the user information in the database.
    $stmt = $pdo->prepare("
    UPDATE users 
    SET 
        first_name = :first_name,
        last_name = :last_name,
        company_name = :company_name,
        password = :password
    WHERE id = :id
    ");
    $stmt->execute([
        'first_name' => $person_type === 'private' ? $first_name : null,
        'last_name' => $person_type === 'private' ? $last_name : null,
        'company_name' => $person_type === 'company' ? $company_name : null,
        'password' => $hashed_password,
        'id' => $_SESSION['user_id']
    ]);

} catch (PDOException $e) {
    // Handle errors if the database update fails.
    terminateEditdataWithError("editdata_insert_failed");
}

// Redirect to the profile page after a successful update.
header("Location: myprofile.php");


    
