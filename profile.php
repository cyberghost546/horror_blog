<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// User info
$stmt = $db->prepare("SELECT username, profile_picture FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// User stories with counts
$stmt = $db->prepare("
    SELECT s.id, s.title, s.image, s.created_at, 
           (SELECT COUNT(*) FROM story_votes sv WHERE sv.story_id = s.id AND sv.vote_type='like') AS likes,
           (SELECT COUNT(*) FROM comments c WHERE c.story_id = s.id) AS comments
    FROM stories s
    WHERE s.user_id = ?
    ORDER BY s.created_at DESC
");
$stmt->execute([$user_id]);
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// User comments
$stmt = $db->prepare("
    SELECT c.comment, c.created_at, s.title, s.id AS story_id
    FROM comments c
    JOIN stories s ON c.story_id = s.id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$user_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Liked stories
$stmt = $db->prepare("
    SELECT s.id, s.title, s.image, s.created_at
    FROM stories s
    JOIN story_votes sv ON s.id = sv.story_id
    WHERE sv.user_id = ? AND sv.vote_type = 'like'
    ORDER BY sv.created_at DESC
");
$stmt->execute([$user_id]);
$liked_stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $user['username']; ?>'s Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #111;
            color: #eee;
            font-family: 'Arial', sans-serif;
        }

        a {
            text-decoration: none;
        }

        .profile-sidebar {
            background: #1a1a1a;
            padding: 20px;
            border-radius: 10px;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #c00;
        }

        .nav-link {
            color: #eee;
        }

        .nav-link.active {
            background: #c00;
            border-radius: 5px;
            color: #fff;
        }

        .card {
            background: #1a1a1a;
            border: none;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 15px #c00;
        }

        .card-img-top {
            height: 150px;
            object-fit: cover;
        }

        .tab-content {
            margin-top: 20px;
        }

        .badge-like {
            background: #c00;
            margin-right: 5px;
        }

        .badge-comment {
            background: #555;
        }
    </style>
</head>

<body>

    <div class="container-fluid py-5">
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="profile-sidebar text-center">
                    <img src="<?php echo $user['profile_picture'] ?: 'default-avatar.png'; ?>" class="profile-img mb-3">
                    <h3><?php echo $user['username']; ?></h3>
                    <form action="upload_profile.php" method="POST" enctype="multipart/form-data" class="mt-3">
                        <input type="file" name="profile_picture" accept="image/*" class="form-control form-control-sm mb-2">
                        <button type="submit" class="btn btn-sm btn-danger w-100">Change Picture</button>
                    </form>
                    <ul class="nav flex-column mt-4">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#stories"><i class="fas fa-book-open me-2"></i>Stories</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#comments"><i class="fas fa-comment me-2"></i>Comments</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#liked"><i class="fas fa-heart me-2"></i>Liked</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-9">
                <div class="tab-content">
                    <!-- Stories Tab -->
                    <div id="stories" class="tab-pane fade show active">
                        <div class="row">
                            <?php if ($stories): ?>
                                <?php foreach ($stories as $s): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card">
                                            <?php if ($s['image']): ?>
                                                <img src="<?php echo $s['image']; ?>" class="card-img-top">
                                            <?php else: ?>
                                                <img src="default-story.png" class="card-img-top">
                                            <?php endif; ?>
                                            <div class="card-body">
                                                <a href="view_story.php?id=<?php echo $s['id']; ?>" class="text-light h5 d-block"><?php echo htmlspecialchars($s['title']); ?></a>
                                                <small class="text-muted"><?php echo $s['created_at']; ?></small>
                                                <div class="mt-2">
                                                    <span class="badge badge-like"><i class="fas fa-heart"></i> <?php echo $s['likes']; ?></span>
                                                    <span class="badge badge-comment"><i class="fas fa-comment"></i> <?php echo $s['comments']; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No stories yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Comments Tab -->
                    <div id="comments" class="tab-pane fade">
                        <ul class="list-group">
                            <?php if ($comments): ?>
                                <?php foreach ($comments as $c): ?>
                                    <li class="list-group-item bg-secondary text-light mb-2">
                                        <strong>On:</strong> <a href="view_story.php?id=<?php echo $c['story_id']; ?>" class="text-light"><?php echo htmlspecialchars($c['title']); ?></a>
                                        <p><?php echo htmlspecialchars($c['comment']); ?></p>
                                        <span class="float-end"><?php echo $c['created_at']; ?></span>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item bg-secondary">No comments yet</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Liked Tab -->
                    <div id="liked" class="tab-pane fade">
                        <div class="row">
                            <?php if ($liked_stories): ?>
                                <?php foreach ($liked_stories as $l): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card">
                                            <?php if ($l['image']): ?>
                                                <img src="<?php echo $l['image']; ?>" class="card-img-top">
                                            <?php else: ?>
                                                <img src="default-story.png" class="card-img-top">
                                            <?php endif; ?>
                                            <div class="card-body">
                                                <a href="view_story.php?id=<?php echo $l['id']; ?>" class="text-light h5 d-block"><?php echo htmlspecialchars($l['title']); ?></a>
                                                <small class="text-muted"><?php echo $l['created_at']; ?></small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No liked stories yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>