<?php
// admin/dashboard.php

session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Fetch admin details
$conn = db_connect();
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare('SELECT username FROM admins WHERE id = ?');
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$stmt->bind_result($admin_username);
$stmt->fetch();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Studio Visjon</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Welcome, <?php echo htmlspecialchars($admin_username); ?>!</h1>
            <nav class="dashboard-nav">
                <ul>
                    <li><a href="content-editor.php">Content Editor</a></li>
                    <li><a href="media-library.php">Media Library</a></li>
                    <li><a href="services-manager.php">Services Manager</a></li>
                    <li><a href="settings.php">Settings</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>
        <main class="dashboard-main">
            <section class="dashboard-overview">
                <h2>Dashboard Overview</h2>
                <div class="overview-cards">
                    <div class="card">
                        <h3>Total Posts</h3>
                        <p>42</p>
                    </div>
                    <div class="card">
                        <h3>Total Media Files</h3>
                        <p>128</p>
                    </div>
                    <div class="card">
                        <h3>Total Services</h3>
                        <p>8</p>
                    </div>
                </div>
            </section>
        </main>
        <footer class="dashboard-footer">
            <p>&copy; 2023 Studio Visjon. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
