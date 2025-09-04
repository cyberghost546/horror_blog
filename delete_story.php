<?php
// Handle delete story
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_story_id'])) {
    $storyId = (int) $_POST['delete_story_id'];

    try {
        $stmt = $db->prepare("DELETE FROM stories WHERE id = ?");
        $stmt->execute([$storyId]);

        echo json_encode(['status' => 'success', 'message' => 'Story deleted successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit();
}

?>