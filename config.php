<?php
/**
 * Global configuration for OCPB
 *
 * Adjust DB_* constants to match your local MySQL setup and imported db.sql.
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'ocpb'); // Change to the database name where you import db.sql
define('DB_USER', 'root'); // XAMPP default, change if needed
define('DB_PASS', '');     // XAMPP default, change if needed
define('DB_CHARSET', 'utf8mb4');

// Application configuration
define('APP_NAME', 'ANANTA OCPB - Online Cheque Processing Book');
define('BASE_URL', '/account'); // Adjust if the project is in a different web root folder

// Session configuration
define('SESSION_TIMEOUT', 3600); // seconds
date_default_timezone_set('Asia/Kolkata'); // Change to your preferred timezone

