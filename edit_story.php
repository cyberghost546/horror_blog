<?php
session_start();
require 'include/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId  = (int) $_SESSION['user_id'];
$storyId = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int) $_GET['id'] : 0;

if ($storyId <= 0) {
    header('Location: my_stories.php');
    exit;
}

// categories
$categoryMap = [
    'true'          => 'True stories',
    'paranormal'    => 'Paranormal',
    'urban'         => 'Urban legends',
    'short'         => 'Short nightmares',
    'haunted'       => 'Haunted places',
    'ghosts'        => 'Ghost encounters',
    'missing'       => 'Missing persons',
    'crime'         => 'Crime & mystery',
    'sleep'         => 'Sleep paralysis',
    'forest'        => 'Forest horror',
    'night'         => 'Night shift stories',
    'calls'         => 'Strange phone calls',
    'creatures'     => 'Creature sightings',
    'abandoned'     => 'Abandoned places',
    'psychological' => 'Psychological horror',
];

$allowedCategories = array_keys($categoryMap);

$errors  = [];
$success = '';

// load story (only your own)
$stmt = $pdo->prepare(
    'SELECT id, title, category, content, image_path, is_published
       FROM stories
      WHERE id = :id AND user_id = :uid
      LIMIT 1'
);
$stmt->execute([':id' => $storyId, ':uid' => $userId]);
$story = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$story) {
    header('Location: my_stories.php?msg=' . urlencode('Story not found'));
    exit;
}

// handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $category = $_POST['category'] ?? $story['category'];
    $content  = trim($_POST['content'] ?? '');
    $publish  = isset($_POST['publish']) ? 1 : 0;

    if ($title === '') {
        $errors[] = 'Title is required';
    }

    if ($content === '') {
        $errors[] = 'Story text is required';
    }

    if (!in_array($category, $allowedCategories, true)) {
        $category = 'true';
    }

    $imagePath = $story['image_path'];

    // optional new image
    if (!empty($_FILES['story_image']['name']) && $_FILES['story_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmp  = $_FILES['story_image']['tmp_name'];
        $fileName = $_FILES['story_image']['name'];

        $fileInfo = pathinfo($fileName);
        $ext      = strtolower($fileInfo['extension'] ?? '');

        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowedExt, true)) {
            $errors[] = 'Story image must be JPG, PNG, or WEBP.';
        } else {
            $uploadDir = __DIR__ . '/uploads/stories/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            $newName  = 'story_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destPath = $uploadDir . $newName;

            if (!move_uploaded_file($fileTmp, $destPath)) {
                $errors[] = 'Could not save the story image.';
            } else {
                $imagePath = 'uploads/stories/' . $newName;
            }
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            'UPDATE stories
                SET title = :title,
                    category = :cat,
                    content = :content,
                    image_path = :img,
                    is_published = :pub
              WHERE id = :id AND user_id = :uid'
        );

        $stmt->execute([
            ':title'   => $title,
            ':cat'     => $category,
            ':content' => $content,
            ':img'     => $imagePath,
            ':pub'     => $publish,
            ':id'      => $storyId,
            ':uid'     => $userId,
        ]);

        $success = 'Story updated';
        // refresh data for form
        $story['title']       = $title;
        $story['category']    = $category;
        $story['content']     = $content;
        $story['image_path']  = $imagePath;
        $story['is_published'] = $publish;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Edit story | silent_evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #020617;
            color: #e5e7eb;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .page-wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 24px 16px 40px;
        }

        .card-dark {
            background-color: #020617;
            border-radius: 16px;
            border: 1px solid #111827;
        }

        .card-dark-header {
            border-bottom: 1px solid #111827;
            padding: 14px 16px;
            font-size: 0.95rem;
            color: #9ca3af;
        }

        .card-dark-body {
            padding: 18px 16px 18px;
        }

        .form-control,
        .form-select {
            background-color: #020617;
            border-color: #1f2937;
            color: #e5e7eb;
            font-size: 0.9rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #6366f1;
            box-shadow: none;
        }

        .btn-primary-silent {
            background-color: #f60000;
            border-color: #f60000;
            color: #0b1120;
            border-radius: 999px;
            font-size: 0.9rem;
            padding: 0.6rem 1.4rem;
        }

        .btn-outline-silent {
            border-color: #4b5563;
            color: #e5e7eb;
            border-radius: 999px;
            font-size: 0.85rem;
            padding: 0.5rem 1.2rem;
        }

        .story-thumb-preview {
            max-width: 200px;
            border-radius: 12px;
            border: 1px solid #111827;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php include 'include/header.php'; ?>

    <div class="page-wrapper">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Edit story</h1>
            <a href="my_stories.php" class="btn btn-outline-silent btn-sm">Back to my stories</a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success py-2">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="alert alert-danger py-2">
                <?php foreach ($errors as $err) echo '<div>' . htmlspecialchars($err) . '</div>'; ?>
            </div>
        <?php endif; ?>

        <div class="card card-dark">
            <div class="card-dark-header">
                Update your story
            </div>
            <div class="card-dark-body">
                <form method="post" enctype="multipart/form-data" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            value="<?php echo htmlspecialchars($story['title']); ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <?php foreach ($categoryMap as $key => $label): ?>
                                <option value="<?php echo htmlspecialchars($key); ?>"
                                    <?php echo $story['category'] === $key ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Your story</label>
                        <textarea
                            name="content"
                            class="form-control"
                            rows="8"><?php echo htmlspecialchars($story['content']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Story image</label><br>
                        <?php if (!empty($story['image_path'])): ?>
                            <img
                                src="<?php echo htmlspecialchars($story['image_path']); ?>"
                                alt="Current image"
                                class="story-thumb-preview">
                            <br>
                        <?php endif; ?>
                        <input
                            type="file"
                            name="story_image"
                            class="form-control mt-2"
                            accept="image/png, image/jpeg, image/webp">
                        <div class="form-text text-muted">
                            Leave empty if you want to keep the current image.
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="publish"
                            name="publish"
                            value="1"
                            <?php echo $story['is_published'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="publish">
                            Published
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary-silent">
                        Save changes
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>