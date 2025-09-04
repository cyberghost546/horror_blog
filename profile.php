<?php
session_start();
include 'track_visit.php';
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: log_in.php');
  exit();
}

$userId = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT username, profile_picture FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
  session_destroy();
  header('Location: log_in.php');
  exit();
}

$submitted = $db->prepare("SELECT id, title FROM stories WHERE user_id = ? ORDER BY created_at DESC");
$submitted->execute([$userId]);
$submittedStories = $submitted->fetchAll();

$liked = $db->prepare("SELECT s.id, s.title FROM stories s JOIN likes l ON s.id = l.story_id WHERE l.user_id = ?");
$liked->execute([$userId]);
$likedStories = $liked->fetchAll();

$bookmarked = $db->prepare("SELECT s.id, s.title FROM stories s JOIN bookmarks b ON s.id = b.story_id WHERE b.user_id = ?");
$bookmarked->execute([$userId]);
$bookmarkedStories = $bookmarked->fetchAll();

$friends = $db->prepare("SELECT u.id, u.username FROM users u JOIN friends f ON f.friend_id = u.id WHERE f.user_id = ?");
$friends->execute([$userId]);
$friendList = $friends->fetchAll();

function e($string)
{
  return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title><?= e($user['username']) ?>'s Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      background: url('images/dark-forest.jpg') no-repeat center center fixed;
      background-size: cover;
      background-color: #121212;
      color: #fff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
      margin: 0;
      padding: 0;
    }

    .navbar-dark .navbar-nav .nav-link.active,
    .navbar-dark .navbar-nav .nav-link:hover {
      color: #ff0000;
    }

    .profile-picture {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid #ff0000;
    }

    .card {
      background-color: #121212;
      border: 1px solid #333;
      border-radius: 8px;
      color: #eee;
    }

    .card-header {
      background-color: #181818;
      color: #ff4d4d;
      font-weight: bold;
      user-select: none;
    }

    a {
      color: #ff0000;
      text-decoration: none;
    }

    a:hover {
      color: #ff4d4d;
      text-decoration: underline;
    }

    label {
      color: #ccc;
      font-weight: 600;
    }
  </style>
</head>

<body>
  <!-- Navbar for mobile & desktop -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top border-bottom border-danger">
    <div class="container-fluid">
      <a class="navbar-brand text-danger fw-bold" href="#">Silent Evidence</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarProfile" aria-controls="navbarProfile" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarProfile">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" href="#"><i class="fas fa-user"></i> Profile</a></li>
          <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-book"></i> My Stories</a></li>
          <li class="nav-item"><a class="nav-link" href="/liked-stories.php"><i class="fas fa-heart"></i> Liked</a></li>
          <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-bookmark"></i> Bookmarks</a></li>
          <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-user-friends"></i> Friends</a></li>
          <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-cog"></i> Settings</a></li>
        </ul>
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="chat.php"><i class="fas fa-comments"></i> Chat</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="container py-4">
    <section class="text-center mb-4" aria-label="User profile header">
      <img src="<?= e($user['profile_picture']) ? 'uploads/' . e($user['profile_picture']) : 'uploads/default.png' ?>" alt="<?= e($user['username']) ?>'s Profile Picture" class="profile-picture mb-3" />
      <h2 class="text-danger fw-bold mb-3"><?= e($user['username']) ?></h2>

      <form action="upload-profile-picture.php" method="POST" enctype="multipart/form-data" class="d-flex flex-column align-items-center" aria-label="Upload profile picture form" style="max-width: 300px; margin: 0 auto;">
        <label for="profile_picture" class="form-label">Change Profile Picture</label>
        <input type="file" name="profile_picture" id="profile_picture" class="form-control bg-dark text-light border-danger mb-2" accept=".jpg,.jpeg,.png,.gif" required aria-required="true" />
        <button type="submit" class="btn btn-danger btn-sm fw-semibold">Upload</button>
      </form>
      <br>
      <a href="index.php" class="btn btn-danger fw-semibold mb-3">
        <i class="fas fa-arrow-left"></i> Back to Home
      </a>
    </section>

    <div class="row gy-4">
      <section class="col-12 col-md-6" aria-label="Submitted Stories">
        <div class="card">
          <div class="card-header">📖 Submitted Stories</div>
          <ul class="list-group list-group-flush">
            <?php if ($submittedStories): ?>
              <?php foreach ($submittedStories as $story): ?>
                <li class="list-group-item bg-dark d-flex justify-content-between align-items-center">
                  <a href="story.php?id=<?= (int)$story['id'] ?>" class="flex-grow-1"><?= e($story['title']) ?></a>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="list-group-item text-danger text-center">No stories submitted yet.</li>
            <?php endif; ?>
          </ul>
        </div>
      </section>

      <section class="col-12 col-md-6" aria-label="Liked Stories">
        <div class="card">
          <div class="card-header">❤️ Liked Stories</div>
          <ul class="list-group list-group-flush">
            <?php if ($likedStories): ?>
              <?php foreach ($likedStories as $story): ?>
                <li class="list-group-item bg-dark d-flex justify-content-between align-items-center">
                  <a href="story.php?id=<?= (int)$story['id'] ?>" class="flex-grow-1"><?= e($story['title']) ?></a>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="list-group-item text-danger text-center">No liked stories yet.</li>
            <?php endif; ?>
          </ul>
        </div>
      </section>

      <section class="col-12 col-md-6" aria-label="Bookmarked Stories">
        <div class="card">
          <div class="card-header">🔖 Bookmarked Stories</div>
          <ul class="list-group list-group-flush">
            <?php if ($bookmarkedStories): ?>
              <?php foreach ($bookmarkedStories as $story): ?>
                <li class="list-group-item bg-dark d-flex justify-content-between align-items-center">
                  <a href="story.php?id=<?= (int)$story['id'] ?>" class="flex-grow-1"><?= e($story['title']) ?></a>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="list-group-item text-danger text-center">No bookmarks yet.</li>
            <?php endif; ?>
          </ul>
        </div>
      </section>

      <section class="col-12 col-md-6" aria-label="Friends List">
        <div class="card">
          <div class="card-header">👥 Friends</div>
          <ul class="list-group list-group-flush">
            <?php if ($friendList): ?>
              <?php foreach ($friendList as $friend): ?>
                <li class="list-group-item bg-dark d-flex justify-content-between align-items-center">
                  <?= e($friend['username']) ?>
                  <form action="remove-friend.php" method="POST" class="m-0" onsubmit="return confirm('Remove friend <?= e($friend['username']) ?>?');">
                    <input type="hidden" name="friend_id" value="<?= (int)$friend['id'] ?>" />
                    <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Remove friend <?= e($friend['username']) ?>">Remove</button>
                  </form>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="list-group-item text-danger text-center">You haven't added any friends yet.</li>
            <?php endif; ?>
          </ul>
        </div>
      </section>

      <section class="col-12" aria-label="Add Friend">
        <div class="card p-3">
          <form action="add-friend.php" method="POST" novalidate>
            <label for="friend_username" class="form-label">Add Friend by Username</label>
            <input type="text" id="friend_username" name="friend_username" class="form-control mb-2" required aria-required="true" />
            <button type="submit" class="btn btn-outline-danger">Add Friend</button>
          </form>
        </div>
      </section>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>