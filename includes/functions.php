<?php
// includes/functions.php

/**
 * Helper functions for the Studio Visjon CMS.
 */

/**
 * Sanitize input data to prevent XSS attacks.
 *
 * @param string $data The input data to sanitize.
 * @return string The sanitized data.
 */
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to a specified URL.
 *
 * @param string $url The URL to redirect to.
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Check if the admin is logged in.
 *
 * @return bool True if the admin is logged in, false otherwise.
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Generate a CSRF token and store it in the session.
 *
 * @return string The generated CSRF token.
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate a CSRF token.
 *
 * @param string $token The CSRF token to validate.
 * @return bool True if the token is valid, false otherwise.
 */
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Hash a password using a secure algorithm.
 *
 * @param string $password The password to hash.
 * @return string The hashed password.
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify a password against a hashed password.
 *
 * @param string $password The plain text password.
 * @param string $hashed_password The hashed password.
 * @return bool True if the password matches, false otherwise.
 */
function verify_password($password, $hashed_password) {
    return password_verify($password, $hashed_password);
}

/**
 * Fetch a single setting value from the database.
 *
 * @param string $option_name The name of the setting.
 * @return string|null The value of the setting, or null if not found.
 */
function get_setting($option_name) {
    $conn = db_connect();
    $stmt = $conn->prepare('SELECT option_value FROM settings WHERE option_name = ?');
    $stmt->bind_param('s', $option_name);
    $stmt->execute();
    $stmt->bind_result($option_value);
    $stmt->fetch();
    $stmt->close();
    $conn->close();
    return $option_value ?? null;
}

/**
 * Update a setting value in the database.
 *
 * @param string $option_name The name of the setting.
 * @param string $option_value The new value of the setting.
 * @return bool True if the update was successful, false otherwise.
 */
function update_setting($option_name, $option_value) {
    $conn = db_connect();
    $stmt = $conn->prepare('UPDATE settings SET option_value = ? WHERE option_name = ?');
    $stmt->bind_param('ss', $option_value, $option_name);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

/**
 * Fetch all rows from a database query as an associative array.
 *
 * @param mysqli_stmt $stmt The prepared statement to execute.
 * @return array The fetched rows.
 */
function fetch_all_assoc($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

/**
 * Format a date for display.
 *
 * @param string $date The date string to format.
 * @return string The formatted date.
 */
function format_date($date) {
    return date('F j, Y, g:i a', strtotime($date));
}
