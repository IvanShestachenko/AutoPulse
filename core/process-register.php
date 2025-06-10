<?php
/**
 * @file
 * @brief This script provides validation of user registration form inputs, saving new user data to the database in case of successful validation and starting a session for the new user.
 * 
 * Terminates and redirects back to the form page with error context parameter and all the user inputs except passwords as parameters of
 * the redirecting GET request in case of error of data validation or database communication.
 */

/**
 * Redirects to the editdata page with the specified error parameter and current user inputs except passwords, and terminates the script.
 *
 * @param string $errorParam The error parameter to be appended to the URL.
 */
function terminateRegWithError($errorParam){
    $postData = $_POST; ///< Stores POST data.
    $postData['error'] = $errorParam; ///< Sets the error parameter to true.
    unset($postData['password'], $postData['confirm_password']); ///< Removes sensitive data from POST data.
    $queryString = http_build_query($postData); ///< Builds a query string from POST data.
    header("Location: register.php?$queryString"); ///< Redirects to the registration page with the error parameter.
    exit; ///< Terminates script execution.
}

session_start(); ///< Starts a new session or resumes the existing session.

require "connection.php"; ///< Includes the database connection file.

if (isset($error) && $error === "db_access_failed") {
    /**
     * Checks if there was a database access error.
     * If so, terminates the operation with an appropriate error parameter.
     */
    terminateRegWithError("reg_db_access_failed");
}

/**
 * Validates input against a specified regular expression pattern.
 *
 * @param string $pattern The regular expression pattern.
 * @param string $input   The input string to validate.
 * @return bool Returns true if the input matches the pattern, false otherwise.
 */
function validateInput($pattern, $input) {
    return preg_match($pattern, $input);
}

$user_type = $_POST['user_type'] ?? ''; ///< Type of user (e.g., 'private' or 'company').
$first_name = $_POST['first_name'] ?? ''; ///< First name of the user.
$last_name = $_POST['last_name'] ?? ''; ///< Last name of the user.
$company_name = $_POST['company_name'] ?? ''; ///< Company name, if applicable.
$email = $_POST['email'] ?? ''; ///< User's email address.
$password = $_POST['password'] ?? ''; ///< Password provided by the user.
$confirm_password = $_POST['confirm_password'] ?? ''; ///< Confirmation of the password.

$patterns = [
    'first_name' => '/^[a-zA-ZčČřŘžŽáÁíÍéÉěĚýÝůŮúÚóÓďĎťŤňŇ.\-]{2,20}$/', ///< Regex for first name validation.
    'last_name' => '/^[a-zA-ZčČřŘžŽáÁíÍéÉěĚýÝůŮúÚóÓďĎťŤňŇ.\-]{2,20}$/', ///< Regex for last name validation.
    'company_name' => '/^[a-zA-ZčČřŘžŽáÁíÍéÉěĚýÝůŮúÚóÓďĎťŤňŇ., \- ]{2,30}$/', ///< Regex for company name validation.
    'email' => '/^[a-z0-9._]+@[a-z0-9.]+\.[a-z0-9]{2,8}$/', ///< Regex for email validation.
    'password' => '/^(?=.*[0-9])(?=.*[!@#$%^&*-_])[a-zA-Z0-9!@#$%^&*-_]{8,32}$/' ///< Regex for password validation.
];

$isFormValid = true; ///< Flag to indicate whether the form input is valid.

if ($user_type === "private") {
    $isFormValid = validateInput($patterns['first_name'], $first_name) && $isFormValid;
    $isFormValid = validateInput($patterns['last_name'], $last_name) && $isFormValid;
} elseif ($user_type === 'company') {
    $isFormValid = validateInput($patterns['company_name'], $company_name) && $isFormValid;
}
$isFormValid = validateInput($patterns['email'], $email) && $isFormValid;
$isFormValid = validateInput($patterns['password'], $password) && $isFormValid;
$isFormValid = ($password == $confirm_password) && $isFormValid;

if (!$isFormValid) {
    /**
     * If any form validation fails, terminates the registration process with an error.
     */
    terminateRegWithError("reg_user_input_invalid");
}

try {
    /**
     * Checks if the email is already registered in the `users` table.
     *
     * @param string $email The email address to check.
     */
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        terminateRegWithError("reg_email_taken");
    }
} catch (PDOException $e) {
    terminateRegWithError("reg_user_add_failed");
}

$hashed_password = password_hash($password, PASSWORD_BCRYPT); ///< Hashes the password securely.

try {
    /**
     * Inserts a new user record into the `users` table.
     *
     * @param string $user_type       Type of user (private or company).
     * @param string|null $first_name First name (if user is private).
     * @param string|null $last_name  Last name (if user is private).
     * @param string|null $company_name Company name (if user is a company).
     * @param string $email           User's email address.
     * @param string $hashed_password Hashed password.
     */
    $stmt = $pdo->prepare("INSERT INTO users (person_type, first_name, last_name, company_name, email, password) VALUES (:person_type, :first_name, :last_name, :company_name, :email, :password)");
    $stmt->execute([
        'person_type' => $user_type,
        'first_name' => $user_type === 'private' ? $first_name : null,
        'last_name' => $user_type === 'private' ? $last_name : null,
        'company_name' => $user_type === 'company' ? $company_name : null,
        'email' => $email,
        'password' => $hashed_password
    ]);

    $_SESSION["user_id"] = $pdo->lastInsertId(); ///< Stores the last inserted user ID in the session.
    session_regenerate_id(true); ///< Regenerates the session ID to prevent session fixation attacks.
    $pdo = null; ///< Closes the database connection.
    header("Location: index.php"); ///< Redirects to the main page upon successful registration.
    exit;
} catch (PDOException $e) {
    terminateRegWithError("reg_user_add_failed");
}
