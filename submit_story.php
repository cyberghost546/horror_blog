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
}
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
    $allowedCategories = ['true', 'paranormal', 'urban', 'short'];
    if (!in_array($category, $allowedCategories, true)) {
        $category = 'true';
    }
    if (!$errors) {
        $slug = make_slug($title, $pdo);
        $stmt = $pdo->prepare('INSERT INTO stories (user_id, title, slug, category, content, is_published) VALUES (:uid, :title, :slug, :cat, :content, :pub)');
        $stmt->execute([':uid' => $userId, ':title' => $title, ':slug' => $slug, ':cat' => $category, ':content' => $content, ':pub' => $publish,]);
        $newId = (int) $pdo->lastInsertId();
        if ($publish) {
            header('Location: story.php?id=' . $newId);
            exit;
        } else {
            $success = 'Draft saved';
            $title = '';
            $content = '';
            $category = 'true';
        }
    }
} ?>
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

        .badge-cat {
            font-size: 0.75rem;
            border-radius: 999px;
            padding: 3px 9px;
        }
    </style>
</head>

<body> <?php include 'include/header.php'; ?> <div class="page-wrapper">
        <div class="mb-2 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Share your horror story</h1>
                <p class="page-subtitle"> True encounter, paranormal experience, urban legend or a short nightmare. Write it down. </p>
            </div> <a href="my_stories.php" class="btn btn-outline-silent d-none d-md-inline-block"> My stories </a>
        </div> <?php if ($success): ?>
            <div class="alert alert-success py-2">
                <?php echo htmlspecialchars($success); ?>
            </div>

        <?php endif; ?> <?php if ($errors): ?>
            <div class="alert alert-danger py-2">
                <?php foreach ($errors as $err) echo '<div>' . htmlspecialchars($err) . '</div>'; ?>
            </div>

        <?php endif; ?> <div class="card card-dark">
            <div class="card-dark-header"> New story </div>
            <div class="card-dark-body">
                <form method="post" novalidate>
                    <div class="mb-3"> <label class="form-label">Title</label> <input type="text" name="title" class="form-control" placeholder="The thing that watched me from the hallway" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                        <div class="small-hint mt-1"> Make it short and scary. This is what users will see first. </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <?php
                            $currentCat = $_POST['category'] ?? 'true';
                            ?>
                            <option value="true" <?php echo $currentCat === 'true' ? 'selected' : ''; ?>>
                                True story
                            </option>
                            <option value="paranormal" <?php echo $currentCat === 'paranormal' ? 'selected' : ''; ?>>
                                Paranormal
                            </option>
                            <option value="urban" <?php echo $currentCat === 'urban' ? 'selected' : ''; ?>>
                                Urban legend
                            </option>
                            <option value="short" <?php echo $currentCat === 'short' ? 'selected' : ''; ?>>
                                Short nightmare
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Your story</label>
                        <textarea
                            name="content"
                            class="form-control"
                            placeholder="You can start with when, where, who was there, and what happened..."><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                        <div class="small-hint mt-1">
                            No HTML needed. Just write. You can always edit later.
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="1" id="publish" name="publish" checked>
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