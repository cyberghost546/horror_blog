<?php
include 'track_visit.php';
include '/xampp/htdocs/horror-blog/includes/db.php';
global $db;

$stmt = $db->query("SELECT * FROM stories ORDER BY created_at DESC");
$stories = $stmt->fetchAll();

// Get top 5 popular stories by views or likes (likes + views weighted)
$popularStmt = $db->query("
  SELECT s.*, 
    (SELECT COUNT(*) FROM story_votes WHERE story_id = s.id AND vote = 1) AS likes
  FROM stories s
  ORDER BY (s.views + (SELECT COUNT(*) FROM story_votes WHERE story_id = s.id AND vote = 1) * 10) DESC
  LIMIT 5
");
$popularStories = $popularStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>True Scary Stories | Silent Evidence</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Styles & Fonts -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Creepster&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
  <style>
    body {
      background-color: #000;
      color: #ff4d4d;
    }

    .views {
      color: #ff4d4d;
    }
    .horror-title {
      font-family: 'Creepster', cursive;
    }

    .legend-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .legend-card:hover {
      transform: scale(1.02);
      box-shadow: 0 0 20px red;
    }

    .carousel-caption {
      font-size: 1rem;
    }

    .card-text {
      font-size: 0.95rem;
    }
  </style>
</head>

<body>

  <?php include 'partials/navbar.php'; ?>

  <section class="container py-5">
    <h1 class="text-center mb-5 horror-title">True Scary Stories</h1>

    <?php
    // Fetch stories with likes count
    $stmt = $db->query("
  SELECT s.*, 
    (SELECT COUNT(*) FROM story_votes WHERE story_id = s.id AND vote = 1) AS likes,
    (SELECT COUNT(*) FROM story_votes WHERE story_id = s.id AND vote = -1) AS dislikes
  FROM stories s
  ORDER BY created_at DESC
");
    $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $userId = $_SESSION['user_id'] ?? null;

    // Fetch bookmarks for logged-in user to mark bookmarked stories
    $bookmarkedStories = [];
    if ($userId) {
      $bmStmt = $db->prepare("SELECT story_id FROM bookmarks WHERE user_id = ?");
      $bmStmt->execute([$userId]);
      $bookmarkedStories = $bmStmt->fetchAll(PDO::FETCH_COLUMN);
    }
    ?>

    <section class="container py-4">
      <h2 class="text-danger text-center mb-4">Most Popular Stories</h2>
      <div id="popularStoriesCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-inner rounded">

          <?php foreach ($popularStories as $index => $story): ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
              <?php if ($story['image']): ?>
                <img src="uploads/<?= htmlspecialchars($story['image']) ?>" class="d-block w-100" style="height: 400px; object-fit: cover;" alt="<?= htmlspecialchars($story['title']) ?>">
              <?php endif; ?>
              <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded p-3">
                <h5 class="text-danger"><?= htmlspecialchars($story['title']) ?></h5>
                <p><?= htmlspecialchars(substr($story['summary'], 0, 120)) ?>...</p>
                <p>👁 <?= (int)$story['views'] ?> views | 👍 <?= (int)$story['likes'] ?> likes</p>
                <span class="badge bg-danger">Popular</span>
                <a href="story.php?slug=<?= urlencode($story['slug']) ?>" class="btn btn-danger btn-sm mt-2">Read More</a>
              </div>
            </div>
          <?php endforeach; ?>

        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#popularStoriesCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#popularStoriesCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
    </section>

    <section class="container py-5">
      <h2 class="text-center text-danger mb-5">Stories</h2>
      <div class="row g-4">
        <?php foreach ($stories as $story): ?>
          <div class="col-md-6 col-lg-4">
            <div class="card bg-dark text-light border border-danger h-100">
              <?php if ($story['image']): ?>
                <img src="uploads/<?= htmlspecialchars($story['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($story['title']) ?>">
              <?php endif; ?>
              <div class="card-body d-flex flex-column">
                <h5 class="card-title text-danger"><?= htmlspecialchars($story['title']) ?></h5>
                <p class="card-text flex-grow-1"><?= htmlspecialchars(substr($story['summary'], 0, 120)) ?>...</p>
                <p class="text-danger text-muted small">👁 <?= (int)$story['views'] ?> views</p>

                <?php if ($story['views'] > 10000 || $story['likes'] > 500): ?>
                  <span class="badge bg-danger mb-2">Popular</span>
                <?php endif; ?>

                <div class="mb-2">
                  <button
                    class="btn btn-outline-danger btn-sm bookmark-btn <?= in_array($story['id'], $bookmarkedStories) ? 'bookmarked' : '' ?>"
                    data-story-id="<?= $story['id'] ?>">
                    <i class="<?= in_array($story['id'], $bookmarkedStories) ? 'fa-solid' : 'fa-regular' ?> fa-bookmark"></i>
                    <?= in_array($story['id'], $bookmarkedStories) ? 'Bookmarked' : 'Bookmark' ?>
                  </button>
                </div>

                <div class="d-flex gap-2 align-items-center story-votes" data-story-id="<?= $story['id'] ?>">
                  <button class="btn btn-sm btn-outline-danger vote-btn" data-vote="1">
                    <i class="fa-regular fa-thumbs-up"></i> Like <span class="likes-count"><?= (int)$story['likes'] ?></span>
                  </button>
                  <button class="btn btn-sm btn-outline-danger vote-btn" data-vote="-1">
                    <i class="fa-regular fa-thumbs-down"></i> Dislike <span class="dislikes-count"><?= (int)$story['dislikes'] ?></span>
                  </button>
                </div>

                <a href="story.php?slug=<?= urlencode($story['slug']) ?>" class="btn btn-danger mt-auto">Read More</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <script>
      document.querySelectorAll('.bookmark-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          const storyId = btn.dataset.storyId;
          const isBookmarked = btn.classList.contains('bookmarked');
          const action = isBookmarked ? 'remove' : 'add';

          fetch('bookmark.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
              },
              body: `story_id=${storyId}&action=${action}`
            })
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                btn.classList.toggle('bookmarked');
                btn.innerHTML = btn.classList.contains('bookmarked') ?
                  '<i class="fa-solid fa-bookmark"></i> Bookmarked' :
                  '<i class="fa-regular fa-bookmark"></i> Bookmark';
              }
            });
        });
      });

      document.querySelectorAll('.story-votes').forEach(container => {
        container.querySelectorAll('.vote-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            const storyId = container.dataset.storyId;
            const vote = btn.dataset.vote;

            fetch('vote.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `story_id=${storyId}&vote=${vote}`
              })
              .then(res => res.json())
              .then(data => {
                if (data.success) {
                  container.querySelector('.likes-count').textContent = data.likes;
                  container.querySelector('.dislikes-count').textContent = data.dislikes;
                }
              });
          });
        });
      });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

  </section>
   <?php include 'partials/footer.php'; ?>
</body>

</html>