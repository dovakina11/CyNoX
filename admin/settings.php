<?php
// admin/settings.php

session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Fetch current settings from the database
$conn = db_connect();
$stmt = $conn->prepare('SELECT option_name, option_value FROM settings');
$stmt->execute();
$result = $stmt->get_result();
$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[$row['option_name']] = $row['option_value'];
}
$stmt->close();

// Handle form submission to update settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $option_name => $option_value) {
        $stmt = $conn->prepare('UPDATE settings SET option_value = ? WHERE option_name = ?');
        $stmt->bind_param('ss', $option_value, $option_name);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: settings.php');
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Studio Visjon</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="settings-container">
        <header class="dashboard-header">
            <h1>Settings</h1>
            <nav class="dashboard-nav">
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="content-editor.php">Content Editor</a></li>
                    <li><a href="media-library.php">Media Library</a></li>
                    <li><a href="services-manager.php">Services Manager</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>
        <main class="settings-main">
            <section class="settings-section">
                <h2>Website Settings</h2>
                <form action="settings.php" method="POST" class="settings-form">
                    <div class="form-group">
                        <label for="site_name">Site Name</label>
                        <input type="text" id="site_name" name="site_name" value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="site_url">Site URL</label>
                        <input type="url" id="site_url" name="site_url" value="<?php echo htmlspecialchars($settings['site_url'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_email">Admin Email</label>
                        <input type="email" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($settings['admin_email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="posts_per_page">Posts Per Page</label>
                        <input type="number" id="posts_per_page" name="posts_per_page" value="<?php echo htmlspecialchars($settings['posts_per_page'] ?? ''); ?>" required>
                    </div>
                    <button type="submit" class="btn">Save Settings</button>
                </form>
            </section>
        </main>
        <footer class="dashboard-footer">
            <p>&copy; 2023 Studio Visjon. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
