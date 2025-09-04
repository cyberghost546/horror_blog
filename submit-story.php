<?php
session_start();
require 'includes/db.php'; // Your DB connection must set $db variable
include 'track_visit.php';

$errors = [];
$success = '';

function slugify($text)
{
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
}

function censorBadWords($text, $badWords)
{
    foreach ($badWords as $word) {
        $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
        $text = preg_replace($pattern, str_repeat('*', strlen($word)), $text);
    }
    return $text;
}

$badWords = ['fuck', 'shit', 'nude', 'porn', 'sex', 'dick', 'pussy', 'bitch', 'asshole', 'cock', 'boobs'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
    $summary = htmlspecialchars(trim($_POST['summary']), ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars(trim($_POST['content']), ENT_QUOTES, 'UTF-8');
    $category = htmlspecialchars(trim($_POST['category']), ENT_QUOTES, 'UTF-8');
    $author = htmlspecialchars(trim($_POST['author']), ENT_QUOTES, 'UTF-8') ?: 'Anonymous';

    function makeUniqueSlug($db, $baseSlug) {
    $slug = $baseSlug;
    $i = 1;
    while (true) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM stories WHERE slug = ?");
        $stmt->execute([$slug]);
        $count = $stmt->fetchColumn();
        if ($count == 0) {
            return $slug;
        }
        $slug = $baseSlug . '-' . $i;
        $i++;
    }
}

    $slug = slugify($title);
    $slug = makeUniqueSlug($db, $slug);

    $title = censorBadWords($title, $badWords);
    $summary = censorBadWords($summary, $badWords);
    $content = censorBadWords($content, $badWords);

    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $target = __DIR__ . "/uploads/" . $imageName;
        $ext = pathinfo($imageName, PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($ext), $allowed)) {
            move_uploaded_file($_FILES['image']['tmp_name'], $target);
        } else {
            $errors[] = "Invalid image format.";
        }
    }

    if (empty($title) || empty($summary) || empty($content) || empty($category)) {
        $errors[] = "All fields except image are required.";
    }

    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO stories (title, slug, summary, content, category, author, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $slug, $summary, $content, $category, $author, $imageName]);
        $success = "Your tale has been unleashed!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Submit a Story | Silent Evidence</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            background-color: #000;
            color: #ff0000;
            font-family: 'Arial', sans-serif;
        }

        h1,
        .form-label {
            font-family: 'Creepster', cursive;
            color: #ff0000;
            text-shadow: 2px 2px 4px #990000;
        }

        .form-control,
        .form-control:focus {
            background-color: #111;
            color: #fff;
            border: 1px solid #ff0000;
        }

        .btn-danger {
            background-color: #ff0000;
            border: none;
            font-weight: bold;
            box-shadow: 0 0 10px #ff0000;
        }

        .btn-danger:hover {
            background-color: #cc0000;
            box-shadow: 0 0 20px #ff0000;
        }

        .container {
            max-width: 700px;
        }

        .alert {
            border-radius: 0;
        }

        .shadow-card {
            background-color: #111;
            padding: 30px;
            border: 2px solid #ff0000;
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.4);
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <h1 class="text-center mb-4">Tell Your Horror</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success text-dark">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="shadow-card rounded">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Summary</label>
                <textarea name="summary" class="form-control" rows="3" required maxlength="500" minlength="10"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Full Story</label>
                <textarea name="content" class="form-control" rows="8" required></textarea>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label text-light">Choose a Category</label>
                <select name="category" id="category" class="form-select bg-dark text-light border-danger mb-3" required>
                    <option value="True Scary Stories">True Scary Stories</option>
                    <option value="Haunted Houses">Haunted Houses</option>
                    <option value="Highway Horror">Highway Horror</option>
                    <option value="Late Night Encounters">Late Night Encounters</option>
                    <option value="Paranormal Witness">Paranormal Witness</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Your Name (Optional)</label>
                <input type="text" name="author" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Upload Image (Optional)</label>
                <input type="file" name="image" class="form-control">
            </div>
            <button type="submit" class="btn btn-danger w-100">Submit My Story</button>
        </form>
    </div>

    <?php include 'partials/footer.php'; ?>
</body>

</html>
