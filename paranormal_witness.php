<?php
include 'track_visit.php';
include '/xampp/htdocs/horror-blog/includes/db.php';
global $db;

$stmt = $db->query("SELECT * FROM stories ORDER BY created_at DESC");
$stories = $stmt->fetchAll();
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

    .carousel-inner img {
      height: 400px;
      object-fit: cover;
    }
  </style>
</head>

<body>

  <?php include 'partials/navbar.php'; ?>

  <!-- FEATURED SLIDESHOW -->
  <section class="container mt-4 mb-5">
    <h2 class="text-center mb-4 horror-title">Featured</h2>
    <div id="featuredCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner rounded">
        <?php foreach ($stories as $index => $story): if ($index > 2) break; ?>
          <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
            <img src="uploads/<?= htmlspecialchars($story['image']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($story['title']) ?>">
            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 p-3 rounded">
              <h5 class="text-danger"><?= htmlspecialchars($story['title']) ?></h5>
              <p><?= htmlspecialchars(substr($story['summary'], 0, 100)) ?>...</p>
              <a href="story.php?slug=<?= urlencode($story['slug']) ?>" class="btn btn-danger btn-sm">Read More</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>
    </div>
  </section>

  <!-- MAIN STORY GRID -->
  <section class="container py-4">
    <h1 class="text-center mb-5 horror-title">True Scary Stories</h1>
    <div class="row g-4">
      <?php foreach ($stories as $story): ?>
        <div class="col-md-6 col-lg-4">
          <div class="card bg-dark text-light border border-danger legend-card h-100">
            <?php if ($story['image']): ?>
              <img src="uploads/<?= htmlspecialchars($story['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($story['title']) ?>">
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title text-danger"><?= htmlspecialchars($story['title']) ?></h5>
              <p class="card-text flex-grow-1"><?= htmlspecialchars(substr($story['summary'], 0, 120)) ?>...</p>
              <p class="text-muted small">👁 <?= (int)$story['views'] ?> views</p>
              <a href="story.php?slug=<?= urlencode($story['slug']) ?>" class="btn btn-outline-danger mt-auto">Read More</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <?php include 'partials/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
