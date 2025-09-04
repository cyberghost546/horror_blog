<?php
// Handle update user role
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['role'])) {
    $userId = (int) $_POST['user_id'];
    $newRole = $_POST['role'];

    if (!in_array($newRole, ['user', 'admin'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid role selected.']);
        exit();
    }

    try {
        $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$newRole, $userId]);

        echo json_encode(['status' => 'success', 'message' => 'User role updated successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit();
}

?>