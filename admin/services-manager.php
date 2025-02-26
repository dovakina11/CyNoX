<?php
// admin/services-manager.php

session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Fetch services from the database
$conn = db_connect();
$stmt = $conn->prepare('SELECT id, name, description, created_at FROM services ORDER BY created_at DESC');
$stmt->execute();
$result = $stmt->get_result();
$services = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

// Handle add, edit, and delete requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = db_connect();

    // Add a new service
    if (isset($_POST['add_service'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);

        if (!empty($name) && !empty($description)) {
            $stmt = $conn->prepare('INSERT INTO services (name, description, created_at) VALUES (?, ?, NOW())');
            $stmt->bind_param('ss', $name, $description);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Edit an existing service
    if (isset($_POST['edit_service_id'])) {
        $edit_service_id = intval($_POST['edit_service_id']);
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);

        if (!empty($name) && !empty($description)) {
            $stmt = $conn->prepare('UPDATE services SET name = ?, description = ? WHERE id = ?');
            $stmt->bind_param('ssi', $name, $description, $edit_service_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Delete a service
    if (isset($_POST['delete_service_id'])) {
        $delete_service_id = intval($_POST['delete_service_id']);
        $stmt = $conn->prepare('DELETE FROM services WHERE id = ?');
        $stmt->bind_param('i', $delete_service_id);
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();
    header('Location: services-manager.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Manager - Studio Visjon</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="services-manager-container">
        <header class="dashboard-header">
            <h1>Services Manager</h1>
            <nav class="dashboard-nav">
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="content-editor.php">Content Editor</a></li>
                    <li><a href="media-library.php">Media Library</a></li>
                    <li><a href="settings.php">Settings</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>
        <main class="services-manager-main">
            <section class="services-manager-section">
                <h2>Manage Services</h2>
                <form action="services-manager.php" method="POST" class="add-service-form">
                    <h3>Add New Service</h3>
                    <div class="form-group">
                        <label for="name">Service Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3" required></textarea>
                    </div>
                    <button type="submit" name="add_service" class="btn">Add Service</button>
                </form>
                <?php if (!empty($services)): ?>
                    <table class="services-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $service): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($service['name']); ?></td>
                                    <td><?php echo htmlspecialchars($service['description']); ?></td>
                                    <td><?php echo htmlspecialchars($service['created_at']); ?></td>
                                    <td>
                                        <form action="services-manager.php" method="POST" class="inline-form">
                                            <input type="hidden" name="edit_service_id" value="<?php echo $service['id']; ?>">
                                            <input type="text" name="name" value="<?php echo htmlspecialchars($service['name']); ?>" required>
                                            <textarea name="description" rows="2" required><?php echo htmlspecialchars($service['description']); ?></textarea>
                                            <button type="submit" class="btn btn-edit">Edit</button>
                                        </form>
                                        <form action="services-manager.php" method="POST" class="inline-form">
                                            <input type="hidden" name="delete_service_id" value="<?php echo $service['id']; ?>">
                                            <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this service?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No services found. Start by adding a new service.</p>
                <?php endif; ?>
            </section>
        </main>
        <footer class="dashboard-footer">
            <p>&copy; 2023 Studio Visjon. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
