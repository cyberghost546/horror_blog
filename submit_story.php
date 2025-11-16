<?php session_start();
require 'include/db.php';
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$userId = (int) $_SESSION['user_id'];
$errors = [];
$success = '';
function make_slug(string $title, PDO $pdo): string
{
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    $slug = trim($slug, '-');
    if ($slug === '') {
        $slug = 'story';
    }
    $base = $slug;
    $i = 1;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM stories WHERE slug = :slug');
    while (true) {
        $stmt->execute([':slug' => $slug]);
        $count = (int) $stmt->fetchColumn();
        if ($count === 0) {
            break;
        }
        $slug = $base . '-' . ++$i;
    }
    return $slug;
} // all categories you support, same as DB enum 
$categoryMap = [
    'true' => 'True stories',
    'paranormal' => 'Paranormal',
    'urban' => 'Urban legends',
    'short' => 'Short nightmares',
    'haunted' => 'Haunted places',
    'ghosts' => 'Ghost encounters',
    'missing' => 'Missing persons',
    'crime' => 'Crime & mystery',
    'sleep' => 'Sleep paralysis',
    'forest' => 'Forest horror',
    'night' => 'Night shift stories',
    'calls' => 'Strange phone calls',
    'creatures' => 'Creature sightings',
    'abandoned' => 'Abandoned places',
    'psychological' => 'Psychological horror',
];

$allowedCategories = array_keys($categoryMap);

// important fix 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category = $_POST['category'] ?? 'true';
    $content = trim($_POST['content'] ?? '');
    $publish = isset($_POST['publish']) ? 1 : 0;
    if ($title === '') {
        $errors[] = 'Title is required';
    }
    if ($content === '') {
        $errors[] = 'Story text is required';
    }
    if (!in_array(
        $category,
        $allowedCategories,
        true
    )) {
        $category = 'true';
    }
    // default, no image 
    $imagePath = null;
    // handle upload if there is a file 
    if (!empty($_FILES['story_image']['name']) && $_FILES['story_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['story_image']['tmp_name'];
        $fileName = $_FILES['story_image']['name'];
        $fileInfo = pathinfo($fileName);
        $ext = strtolower($fileInfo['extension'] ?? '');
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowedExt, true)) {
            $errors[] = 'Story image must be JPG, PNG, or WEBP.';
        } else {
            $uploadDir = __DIR__ . '/uploads/stories/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            $newName = 'story_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destPath = $uploadDir . $newName;
            if (!move_uploaded_file($fileTmp, $destPath)) {
                $errors[] = 'Could not save the story image.';
            } else {
                // path used on the site 
                $imagePath = 'uploads/stories/' . $newName;
            }
        }
    }
    if (!$errors) {
        $slug = make_slug($title, $pdo);
        $stmt = $pdo->prepare('INSERT INTO stories (user_id, title, slug, category, content, image_path, is_published) VALUES (:uid, :title, :slug, :cat, :content, :img, :pub)');
        $stmt->execute([
            ':uid' => $userId,
            ':title' => $title,
            ':slug' => $slug,
            ':cat' => $category,
            ':content' => $content,
            ':img' => $imagePath,
            ':pub' => $publish,
        ]);
        $newId = (int) $pdo->lastInsertId();
        if ($publish) {
            // detail page is story.php, not stories.php 
            header('Location: story.php?id=' . $newId);
            exit;
        } else {
            $success = 'Draft saved';
            $title = '';
            $content = '';
            $category = 'true';
            $_POST = [];
        }
    }
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Submit story | silent_evidence</title>
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

        .page-title {
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .page-subtitle {
            font-size: 0.9rem;
            color: #9ca3af;
            margin-bottom: 18px;
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

        .form-control::placeholder {
            color: #6b7280;
        }

        textarea.form-control {
            min-height: 220px;
            resize: vertical;
        }

        .btn-primary-silent {
            background-color: #f60000;
            border-color: #f60000;
            color: #0b1120;
            border-radius: 999px;
            font-size: 0.9rem;
            padding: 0.6rem 1.4rem;
        }

        .btn-primary-silent:hover {
            background-color: #dc2626;
            border-color: #dc2626;
            color: #0b1120;
        }

        .btn-outline-silent {
            border-color: #4b5563;
            color: #e5e7eb;
            border-radius: 999px;
            font-size: 0.85rem;
            padding: 0.5rem 1.2rem;
        }

        .btn-outline-silent:hover {
            background-color: #111827;
            border-color: #6b7280;
            color: #ffffff;
        }

        .small-hint {
            font-size: 0.8rem;
            color: #6b7280;
        }
    </style>
</head>

<body>
    <?php include 'include/header.php'; ?>
    <div class="page-wrapper">
        <div class="mb-2 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Share your horror story</h1>
                <p class="page-subtitle"> True encounter, paranormal experience, urban legend or a short nightmare. Write it down. </p>
            </div> <a href="my_stories.php" class="btn btn-outline-silent d-none d-md-inline-block">My stories</a>
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
                New story
            </div>
            <div class="card-dark-body">
                <?php $currentCat = $_POST['category'] ?? 'true'; ?>
                <form method="post" enctype="multipart/form-data" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            placeholder="The thing that watched me from the hallway"
                            value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                            required>
                        <div class="small-hint mt-1">
                            Make it short and scary. This is what users will see first.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="true" <?php echo $currentCat === 'true' ? 'selected' : ''; ?>>True story</option>
                            <option value="paranormal" <?php echo $currentCat === 'paranormal' ? 'selected' : ''; ?>>Paranormal</option>
                            <option value="urban" <?php echo $currentCat === 'urban' ? 'selected' : ''; ?>>Urban legend</option>
                            <option value="short" <?php echo $currentCat === 'short' ? 'selected' : ''; ?>>Short nightmare</option>
                            <option value="haunted" <?php echo $currentCat === 'haunted' ? 'selected' : ''; ?>>Haunted places</option>
                            <option value="ghosts" <?php echo $currentCat === 'ghosts' ? 'selected' : ''; ?>>Ghost encounters</option>
                            <option value="missing" <?php echo $currentCat === 'missing' ? 'selected' : ''; ?>>Missing persons</option>
                            <option value="crime" <?php echo $currentCat === 'crime' ? 'selected' : ''; ?>>Crime and mystery</option>
                            <option value="sleep" <?php echo $currentCat === 'sleep' ? 'selected' : ''; ?>>Sleep paralysis</option>
                            <option value="forest" <?php echo $currentCat === 'forest' ? 'selected' : ''; ?>>Forest horror</option>
                            <option value="calls" <?php echo $currentCat === 'calls' ? 'selected' : ''; ?>>Strange phone calls</option>
                            <option value="creatures" <?php echo $currentCat === 'creatures' ? 'selected' : ''; ?>>Creature sightings</option>
                            <option value="abandoned" <?php echo $currentCat === 'abandoned' ? 'selected' : ''; ?>>Abandoned places</option>
                            <option value="psychological" <?php echo $currentCat === 'psychological' ? 'selected' : ''; ?>>Psychological horror</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Your story</label>
                        <textarea
                            name="content"
                            class="form-control"
                            placeholder="You can start with when, where, who was there, and what happened..."
                            style="min-height:220px; max-height:220px; resize:none;"><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                        <div class="small-hint mt-1">
                            No HTML needed. Just write. You can always edit later.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Story image</label>
                        <input
                            type="file"
                            name="story_image"
                            class="form-control"
                            accept="image/png, image/jpeg, image/webp">
                        <div class="small-hint mt-1">
                            Optional. Use a clear image that matches your story.
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div class="form-check mb-2">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="1"
                                id="publish"
                                name="publish"
                                <?php echo isset($_POST['publish']) ? 'checked' : 'checked'; ?>>
                            <label class="form-check-label" for="publish">
                                Publish immediately
                            </label>
                        </div>

                        <div class="d-flex gap-2 mb-2">
                            <a href="index.php" class="btn btn-outline-silent">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary-silent">
                                Save story
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>