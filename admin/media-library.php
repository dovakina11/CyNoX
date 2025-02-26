<?php
// admin/media-library.php

session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Fetch media files from the database
$conn = db_connect();
$stmt = $conn->prepare('SELECT id, file_name, file_path, uploaded_at FROM media ORDER BY uploaded_at DESC');
$stmt->execute();
$result = $stmt->get_result();
$media_files = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_media_id'])) {
    $delete_media_id = intval($_POST['delete_media_id']);
    $conn = db_connect();
    $stmt = $conn->prepare('DELETE FROM media WHERE id = ?');
    $stmt->bind_param('i', $delete_media_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header('Location: media-library.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Library - Studio Visjon</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="media-library-container">
        <header class="dashboard-header">
            <h1>Media Library</h1>
            <nav class="dashboard-nav">
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="content-editor.php">Content Editor</a></li>
                    <li><a href="services-manager.php">Services Manager</a></li>
                    <li><a href="settings.php">Settings</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>
        <main class="media-library-main">
            <section class="media-library-section">
                <h2>Manage Media Files</h2>
                <a href="upload-media.php" class="btn">Upload New Media</a>
                <?php if (!empty($media_files)): ?>
                    <table class="media-table">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Uploaded At</th>
                                <th>Preview</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($media_files as $media): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($media['file_name']); ?></td>
                                    <td><?php echo htmlspecialchars($media['uploaded_at']); ?></td>
                                    <td>
                                        <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $media['file_name'])): ?>
                                            <img src="<?php echo htmlspecialchars($media['file_path']); ?>" alt="Media Preview" class="media-preview">
                                        <?php else: ?>
                                            <span>Preview not available</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form action="media-library.php" method="POST" class="inline-form">
                                            <input type="hidden" name="delete_media_id" value="<?php echo $media['id']; ?>">
                                            <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this media file?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No media files found. Start by uploading new media.</p>
                <?php endif; ?>
            </section>
        </main>
        <footer class="dashboard-footer">
            <p>&copy; 2023 Studio Visjon. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
