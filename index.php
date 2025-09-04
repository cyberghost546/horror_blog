<?php

include 'auth.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: log_in.php');
  exit();
}

$userId = $_SESSION['user_id'];

// Get users active in the last 5 minutes
$onlineWindow = date('Y-m-d H:i:s', time() - 300);

$onlineUsersStmt = $db->prepare("
  SELECT u.username FROM users u
  INNER JOIN user_sessions us ON u.id = us.user_id
  WHERE us.last_active >= ?
");
$onlineUsersStmt->execute([$onlineWindow]);
$onlineUsers = $onlineUsersStmt->fetchAll(PDO::FETCH_COLUMN);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Silent Evidence</title>
</head>

<body>
    <!-- Header -->
    <header>
        <?php include 'partials/navbar.php'; ?>
    </header>

    <!-- popular-stories -->
    <!-- popular-stories -->
<section class="popular-stories py-4">
    <h2 class="text-danger mb-4 text-center">Most Popular Stories</h2>

    <?php
    require 'includes/db.php';

    // Get top 3 stories sorted by views or likes
    $popularStmt = $db->query("
        SELECT id, title, content, image 
        FROM stories 
        ORDER BY views DESC 
        LIMIT 3
    ");
    $popularStories = $popularStmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <?php if ($popularStories): ?>
        <div id="popularStoriesCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-inner">

                <?php foreach ($popularStories as $index => $story): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <img src="<?= htmlspecialchars($story['image']) ?>" 
                             class="d-block w-100 rounded" 
                             alt="<?= htmlspecialchars($story['title']) ?>">
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded p-3">
                            <h5><?= htmlspecialchars($story['title']) ?></h5>
                            <p><?= substr(strip_tags($story['content']), 0, 100) ?>...</p>
                            <a href="story.php?id=<?= $story['id'] ?>" class="btn btn-danger btn-sm">Read More</a>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>

            <!-- Carousel Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#popularStoriesCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#popularStoriesCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    <?php else: ?>
        <p class="text-center text-muted">No stories found.</p>
    <?php endif; ?>
</section>


    <!-- 🔥 1. Featured Story Section -->
    <section class="featured-story bg-dark text-danger py-5">
        <div class="container text-center">
            <h2 class="mb-4 text-danger">Featured Nightmare</h2>
            <img src="/images/A weathered, rustic .png" alt="Featured Story" class="img-fluid rounded shadow mb-3">
            <?php
            require 'includes/db.php';

            $featuredStmt = $db->query("SELECT * FROM stories WHERE featured = 1 LIMIT 1");
            $featured = $featuredStmt->fetch();
            ?>

            <?php if ($featured): ?>
                <h3 class="fw-bold mt-3"><?= htmlspecialchars($featured['title']) ?></h3>
                <p class="lead fst-italic"><?= substr(strip_tags($featured['content']), 0, 150) ?>...</p>
                <a href="story.php?id=<?= $featured['id'] ?>" class="btn btn-danger rounded-pill px-4 py-2">Read Full Story</a>
            <?php else: ?>
                <h3 class="fw-bold mt-3">No Featured Story Yet</h3>
            <?php endif; ?>
        </div>
    </section>

    <!-- 2. Submit Your Story CTA -->
    <section class="submit-cta bg-black text-danger text-center py-5">
        <div class="container">
            <h2 class="mb-3">Have a True Story to Share?</h2>
            <p class="mb-4">We accept real paranormal and horror encounters from people just like you.</p>
            <a href="/submit-story.html" class="btn btn-outline-danger rounded-pill px-4 py-2">Submit Yours</a>
        </div>
    </section>

    <!-- 3. Short Horror Video/Audio Clips -->
    <section class="horror-media bg-dark text-light py-5">
        <div class="container">
            <h2 class="text-danger text-center mb-4">Watch or Listen If You Dare</h2>
            <div class="row justify-content-center">
                <div class="col-md-6 mb-4">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/YOUR_VIDEO_ID" title="YouTube video"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
                <div class="col-md-6">
                    <audio controls class="w-100 rounded shadow">
                        <source src="audio/creepy-story.mp3" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                    <p class="text-center mt-2">A reading of “The Woods Were Never Empty”</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Reader Testimonials -->
    <section class="testimonials bg-black text-danger py-5">
        <div class="container text-center">
            <h2 class="mb-5">What Our Readers Say</h2>
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <blockquote class="blockquote">
                        <p>“Gave me chills! I couldn't sleep after reading ‘The Wraith’.”</p>
                        <footer class="blockquote-footer text-light">Alex from Ohio</footer>
                    </blockquote>
                </div>
                <div class="col-md-4">
                    <blockquote class="blockquote">
                        <p>“The stories feel too real. It's like you're there...”</p>
                        <footer class="blockquote-footer text-light">Maya from Cape Town</footer>
                    </blockquote>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Category Cards Grid (instead of dropdown) -->
    <section class="story-categories py-5 bg-dark text-light">
        <div class="container">
            <h2 class="text-danger text-center mb-4">Browse by Category</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <div class="card bg-black border-danger h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title text-danger">Haunted Houses</h5>
                            <p class="card-text">Terrifying tales from within cursed walls.</p>
                            <a href="#" class="btn btn-danger btn-sm">Explore</a>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card bg-black border-danger h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title text-danger">Haunted Houses</h5>
                            <p class="card-text">Terrifying tales from within cursed walls.</p>
                            <a href="#" class="btn btn-danger btn-sm">Explore</a>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card bg-black border-danger h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title text-danger">Haunted Houses</h5>
                            <p class="card-text">Terrifying tales from within cursed walls.</p>
                            <a href="#" class="btn btn-danger btn-sm">Explore</a>
                        </div>
                    </div>
                </div>
                <!-- Repeat for other categories -->
            </div>
        </div>
    </section>

    <!-- 6. Horror Countdown Timer  -->
    <section class="countdown-timer bg-black text-danger text-center py-5">
        <div class="container">
            <h2 class="mb-4 horror-title">Next Story Drops In</h2>
            <div id="timer" class="fs-1 fw-bold neon-text">--:--:--</div>
        </div>
    </section>

    <!-- 7. Footer -->
    <?php include 'partials/footer.php'; ?>
</body>
<script src="js/main.js"></script>

</html>