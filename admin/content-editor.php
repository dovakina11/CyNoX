<?php
// admin/content-editor.php

session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Fetch posts from the database
$conn = db_connect();
$stmt = $conn->prepare('SELECT id, title, created_at FROM posts ORDER BY created_at DESC');
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    $delete_post_id = intval($_POST['delete_post_id']);
    $conn = db_connect();
    $stmt = $conn->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->bind_param('i', $delete_post_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header('Location: content-editor.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Editor - Studio Visjon</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="content-editor-container">
        <header class="dashboard-header">
            <h1>Content Editor</h1>
            <nav class="dashboard-nav">
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="media-library.php">Media Library</a></li>
                    <li><a href="services-manager.php">Services Manager</a></li>
                    <li><a href="settings.php">Settings</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>
        <main class="content-editor-main">
            <section class="content-editor-section">
                <h2>Manage Posts</h2>
                <a href="create-post.php" class="btn">Create New Post</a>
                <?php if (!empty($posts)): ?>
                    <table class="content-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                                    <td><?php echo htmlspecialchars($post['created_at']); ?></td>
                                    <td>
                                        <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="btn btn-edit">Edit</a>
                                        <form action="content-editor.php" method="POST" class="inline-form">
                                            <input type="hidden" name="delete_post_id" value="<?php echo $post['id']; ?>">
                                            <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this post?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No posts found. Start by creating a new post.</p>
                <?php endif; ?>
            </section>
        </main>
        <footer class="dashboard-footer">
            <p>&copy; 2023 Studio Visjon. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
