<?php
/**
 * @file
 * @brief This script handles logout initiated by the user. Unsets and destroys session, clears cookies, redirects back to the main page.
 */
session_start();

/**
 * Clears all session data and destroys the session.
 */

// Reset the session array.
$_SESSION = [];

// If cookies are used for sessions, invalidate the session cookie.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, 
        $params["path"], $params["domain"], 
        $params["secure"], $params["httponly"]
    );
}

// Unset session variables and destroy the session.
session_unset();
session_destroy();

// Redirect to the index page.
header("Location: index.php");