<?php

/**
 * @file
 * @brief This script provides validation of user login form inputs, comparison of user authorization data from the input with the one set in the database for this user, and starting a session with set parameters of 'user_id' and 'admin'.
 * 
 * Terminates and redirects back to the form page with error context parameter and all the user inputs except passwords as parameters of
 * the redirecting GET request in case of error of data validation or database communication.
 */

/**
 * Redirects to the editdata page with the specified error parameter and current user inputs except passwords, and terminates the script.
 *
 * @param string $errorParam The error parameter to be appended to the URL.
 */
function terminateLoginWithError($errorParam){
    $postData = $_POST;
    $postData['error'] = $errorParam;
    unset($postData['password']);
    $queryString = http_build_query($postData);
    header("Location: login.php?$queryString");
    exit;
}

session_start();

require "connection.php";

/**
 * Check if there was a database access error and terminate the login process if so.
 */
if (isset($error) && $error === "db_access_failed"){
    terminateLoginWithError("login_db_access_failed");
}

// Extract email and password from POST parameters.
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Define validation patterns for email and password.
$emailPattern = "/^[a-z0-9.]+@[a-z0-9.]+\.[a-z0-9]{2,8}$/";
$passwordPattern = "/^(?=.*[0-9])(?=.*[!@#$%^&*-_])[a-zA-Z0-9!@#$%^&*-_]{8,32}$/";

// Validate email and password format.
if (!preg_match($emailPattern, $email) || !preg_match($passwordPattern, $password)) {
    terminateLoginWithError("login_user_input_invalid");
}

try {
    // Fetch the user data based on the provided email.
    $stmt = $pdo->prepare("SELECT id, `password`, admin FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify the user's credentials.
    if (!$user || !password_verify($password, $user['password'])) {
        terminateLoginWithError("login_user_invalid");
    }
} catch (PDOException $e) {
    // Handle errors during user validation.
    terminateLoginWithError("login_user_validation_failed");
}

// Set session variables for the authenticated user.
$_SESSION["user_id"] = $user['id'];
$_SESSION["admin"] = $user['admin'];

// Regenerate the session ID to prevent session fixation attacks.
session_regenerate_id(true);

// Close the database connection and redirect to the index page.
$pdo = null; 
header("Location: index.php");
