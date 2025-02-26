<?php
// includes/config.php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'password');
define('DB_NAME', 'studio_visjon');

// Site settings
define('SITE_NAME', 'Studio Visjon');
define('SITE_URL', 'http://localhost/studio-visjon');
define('ADMIN_EMAIL', 'admin@studio-visjon.com');

// Establish a database connection
function db_connect() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Check for connection errors
    if ($conn->connect_error) {
        die('Database connection failed: ' . $conn->connect_error);
    }

    return $conn;
}

// Set default timezone
date_default_timezone_set('UTC');
