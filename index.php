<?php
session_start();
require 'include/db.php';

// load active slides for homepage carousel
$slidesStmt = $pdo->query("
    SELECT id, title, caption, image_url
    FROM carousel_slides
    WHERE is_active = 1
    ORDER BY sort_order, id
");
$slides = $slidesStmt->fetchAll();


// Latest 3 published stories from DB
$stmt = $pdo->prepare(
    'SELECT 
         s.id,
         s.title,
         s.category,
         s.content,
         s.created_at,
         s.views,
         s.likes,
         u.display_name,
         u.username,
         COUNT(c.id) AS comment_count
     FROM stories s
     JOIN users u 
       ON u.id = s.user_id
     LEFT JOIN story_comments c 
       ON c.story_id = s.id
     WHERE s.is_published = 1
     GROUP BY s.id
     ORDER BY s.created_at DESC
     LIMIT 3'
);
$stmt->execute();
$latestStories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// helper for category label
function se_category_label(string $cat): string
{
    if ($cat === 'true')       return 'TRUE EVENT';
    if ($cat === 'paranormal') return 'SLEEP PARALYSIS';
    if ($cat === 'urban')      return 'URBAN LEGEND';
    if ($cat === 'short')      return 'SHORT NIGHTMARE';
    return strtoupper($cat);
}


?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>silent_evidence | Horror Stories</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Custom styles for the horror vibe -->
    <style>
        body {
            background-color: #020617;
            color: #e5e7eb;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        a {
            text-decoration: none;
        }

        .hero-section {
            position: relative;
            margin-top: 24px;
            margin-bottom: 40px;
            padding: 48px 32px;
            border-radius: 20px;
            overflow: hidden;
            background: radial-gradient(circle at top left, #f60000 0, #111827 45%, #020617 80%);
            color: #f9fafb;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 10% 0, rgba(255, 255, 255, 0.08) 0, transparent 55%), radial-gradient(circle at 90% 100%, rgba(0, 0, 0, 0.8) 0, transparent 60%);
            opacity: 0.6;
        }

        .hero-content {
            position: relative;
            max-width: 560px;
            z-index: 1;
        }

        .hero-tag {
            display: inline-block;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            color: #fecaca;
            margin-bottom: 10px;
        }

        .hero-title {
            font-size: 32px;
            line-height: 1.2;
            margin-bottom: 12px;
        }

        .hero-subtitle {
            font-size: 14px;
            color: #e5e7eb;
            max-width: 460px;
            margin-bottom: 20px;
        }

        .hero-buttons a {
            border-radius: 999px;
        }

        .btn-silent-primary {
            background-color: #f60000;
            color: #f9fafb;
            box-shadow: 0 8px 20px rgba(246, 0, 0, 0.45);
        }

        .btn-silent-primary:hover {
            background-color: #dc2626;
            color: #f9fafb;
            box-shadow: 0 12px 28px rgba(246, 0, 0, 0.6);
        }

        .btn-silent-ghost {
            background-color: rgba(15, 23, 42, 0.85);
            color: #f9fafb;
            border-color: rgba(248, 250, 252, 0.2);
        }

        .btn-silent-ghost:hover {
            background-color: rgba(15, 23, 42, 1);
            color: #f9fafb;
        }

        .hero-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #fecaca;
            margin-top: 10px;
        }

        .dot {
            width: 4px;
            height: 4px;
            border-radius: 999px;
            background: #fecaca;
        }

        .section-title {
            font-size: 20px;
            color: #f9fafb;
        }

        .story-card {
            background: #020617;
            border-radius: 16px;
            border: 1px solid #1f2933;
            transition: border 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
            cursor: pointer;
        }

        .story-card:hover {
            border-color: #f60000;
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.5);
        }

        .story-tag {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            color: #f87171;
        }

        .story-title {
            font-size: 16px;
            color: #f9fafb;
        }

        .story-excerpt {
            font-size: 13px;
            color: #9ca3af;
        }

        .story-meta {
            font-size: 11px;
            color: #6b7280;
        }

        .category-card {
            background: #020617;
            border-radius: 14px;
            border: 1px solid #111827;
            transition: border 0.15s ease, background 0.15s ease, transform 0.15s ease;
        }

        .category-card:hover {
            border-color: #f60000;
            background: #020817;
            transform: translateY(-1px);
        }

        .community-box {
            background: #020617;
            border-radius: 18px;
            border: 1px solid #111827;
        }

        .community-panel {
            background: #020817;
            border-radius: 16px;
            border: 1px solid #1f2933;
        }

        .panel-label {
            color: #9ca3af;
        }

        .panel-value {
            color: #f9fafb;
            font-weight: 600;
        }

        .panel-live {
            color: #f97373;
        }

        .footer-text {
            font-size: 12px;
            color: #6b7280;
        }

        @media (max-width: 767.98px) {
            .hero-section {
                padding: 32px 20px;
            }

            .hero-title {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <?php
    include 'include/header.php';
    ?>
    <main class="container py-4"> <!-- Hero section -->

        <!-- Featured Slideshow -->
        <section class="mb-5">
            <section class="mb-5">
                <?php if ($slides): ?>
                    <div id="silentCarousel" class="carousel slide" data-bs-ride="carousel">

                        <div class="carousel-indicators">
                            <?php foreach ($slides as $index => $slide): ?>
                                <button
                                    type="button"
                                    data-bs-target="#silentCarousel"
                                    data-bs-slide-to="<?php echo $index; ?>"
                                    class="<?php echo $index === 0 ? 'active' : ''; ?>">
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <div class="carousel-inner rounded-4 shadow-lg" style="border:1px solid #1f2937;">

                            <?php foreach ($slides as $index => $slide): ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <img
                                        src="<?php echo htmlspecialchars($slide['image_url']); ?>"
                                        class="d-block w-100"
                                        style="object-fit:cover;height:360px;">
                                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded-3 p-3">
                                        <h5><?php echo htmlspecialchars($slide['title']); ?></h5>
                                        <p><?php echo htmlspecialchars($slide['caption']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        </div>

                        <button class="carousel-control-prev" type="button" data-bs-target="#silentCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#silentCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>

                    </div>
                <?php else: ?>
                    <!-- fallback, if no slides in DB -->
                    <p class="text-secondary small">
                        No featured slides configured yet.
                    </p>
                <?php endif; ?>
            </section>

        </section>

        <section class="hero-section">
            <div class="hero-overlay">
            </div>
            <div class="hero-content">
                <p class="hero-tag mb-1">silent_evidence</p>
                <h1 class="hero-title">Real stories that keep you up at night</h1>
                <p class="hero-subtitle">
                    Read true horror from people around the world. Or drop your own story and let others feel your fear.
                </p>

                <div class="hero-buttons d-flex flex-wrap gap-2 mb-2">
                    <a href="stories.php" class="btn btn-silent-primary btn-sm">Read stories</a>
                    <a href="submit_story.php" class="btn btn-outline-light btn-silent-ghost btn-sm">Share your story</a>
                </div>

                <div class="hero-meta">
                    <span>Live stories</span>
                    <span class="dot"></span>
                    <span>Community driven</span>
                    <span class="dot"></span>
                    <span>No fake jumpscares. Just dread</span>
                </div>
            </div>
        </section>

        <!-- Latest stories -->
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-baseline mb-3">
                <h2 class="section-title mb-0">Latest stories</h2>
                <a href="stories.php" class="small text-secondary">View all</a>
            </div>

            <div class="row g-3">
                <?php if (!$latestStories): ?>
                    <p class="text-secondary small mb-0">No stories yet. Be the first to post one.</p>
                <?php else: ?>
                    <?php foreach ($latestStories as $story): ?>
                        <?php
                        // estimate read time from content
                        $wordCount = str_word_count(strip_tags($story['content']));
                        $minutes   = max(1, ceil($wordCount / 200));
                        ?>
                        <div class="col-md-4">
                            <a href="story.php?id=<?php echo (int)$story['id']; ?>" style="text-decoration:none;">
                                <article class="story-card h-100 p-3">
                                    <p class="story-tag mb-1">
                                        <?php echo htmlspecialchars(se_category_label($story['category'])); ?>
                                    </p>

                                    <h3 class="story-title mb-2">
                                        <?php echo htmlspecialchars($story['title']); ?>
                                    </h3>

                                    <p class="story-excerpt mb-3">
                                        <?php
                                        $preview = substr(strip_tags($story['content']), 0, 140);
                                        echo htmlspecialchars($preview) . '...';
                                        ?>
                                    </p>

                                    <div class="story-meta d-flex justify-content-between">
                                        <span><?php echo $minutes; ?> min read</span>
                                        <span><?php echo (int)$story['comment_count']; ?> comments</span>
                                    </div>
                                </article>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>


        <!-- Categories -->
        <section class="mb-5">
            <hr>
            <!-- Paranormal Feature Card -->
            <section class="mb-4">
                <div class="paranormal-card p-4 rounded-4 d-flex align-items-center justify-content-between flex-wrap"
                    style="background:#0b0f19;border:1px solid #1a2233;">

                    <div class="mb-3">
                        <h2 class="text-light mb-2">Enter the Paranormal</h2>
                        <p class="text-secondary mb-3" style="max-width:420px;">
                            Ghosts, spirits, haunted houses, cursed objects. Explore the most unsettling corners of Silent Evidence.
                        </p>
                        <a href="stories.php?cat=paranormal"
                            class="btn btn-silent-primary btn-sm px-4 py-2">
                            Explore Paranormal Stories
                        </a>
                    </div>

                    <img src="https://images.unsplash.com/photo-1500375592092-40eb2168fd21?q=80"
                        class="rounded-4 shadow-lg"
                        style="width:260px;height:160px;object-fit:cover;border:1px solid #1f2937;">
                </div>
            </section>
            <hr>

            <div class="d-flex justify-content-between align-items-baseline mb-3">
                <h2 class="section-title mb-0">Browse by vibe</h2>
            </div>

            <!-- Category Cards -->
            <div class="row g-3 mb-3">

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=true" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">TRUE</h3>
                        <h4 class="text-light fs-5 mb-1">True stories</h4>
                        <p class="text-secondary small mb-0">Real experiences users claim actually happened.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=paranormal" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">PARANORMAL</h3>
                        <h4 class="text-light fs-5 mb-1">Paranormal</h4>
                        <p class="text-secondary small mb-0">Ghosts, spirits, haunted houses, cursed objects.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=urban" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">URBAN</h3>
                        <h4 class="text-light fs-5 mb-1">Urban legends</h4>
                        <p class="text-secondary small mb-0">Stories that spread online and feel too real.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=short" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">SHORT</h3>
                        <h4 class="text-light fs-5 mb-1">Short nightmares</h4>
                        <p class="text-secondary small mb-0">Quick reads that hit fast.</p>
                    </a>
                </div>

            </div>

            <hr>

            <div class="row g-3">

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=haunted" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">HAUNTED</h3>
                        <h4 class="text-light fs-5 mb-1">Haunted places</h4>
                        <p class="text-secondary small mb-0">Real locations with disturbing history.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=ghosts" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">GHOSTS</h3>
                        <h4 class="text-light fs-5 mb-1">Ghost encounters</h4>
                        <p class="text-secondary small mb-0">Unexplainable sightings and hauntings.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=missing" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">MISSING</h3>
                        <h4 class="text-light fs-5 mb-1">Missing persons</h4>
                        <p class="text-secondary small mb-0">Cases that leave more questions than answers.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=psychological" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">PSYCHO</h3>
                        <h4 class="text-light fs-5 mb-1">Psychological horror</h4>
                        <p class="text-secondary small mb-0">Stories that mess with your head.</p>
                    </a>
                </div>

            </div>


            <hr>

            <div class="row g-3 mt-2">

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=crime" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">CRIME</h3>
                        <h4 class="text-light fs-5 mb-1">Crime and mystery</h4>
                        <p class="text-secondary small mb-0">Dark events that defy explanation.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=sleep" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">SLEEP</h3>
                        <h4 class="text-light fs-5 mb-1">Sleep paralysis</h4>
                        <p class="text-secondary small mb-0">The figures you cannot move away from.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=forest" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">FOREST</h3>
                        <h4 class="text-light fs-5 mb-1">Forest horror</h4>
                        <p class="text-secondary small mb-0">What hides between the trees.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=shifts" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">NIGHT</h3>
                        <h4 class="text-light fs-5 mb-1">Night shift stories</h4>
                        <p class="text-secondary small mb-0">Late hours that get way too strange.</p>
                    </a>
                </div>

            </div>
            <hr>
            <div class="row g-3 mt-2">

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=calls" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">CALLS</h3>
                        <h4 class="text-light fs-5 mb-1">Strange phone calls</h4>
                        <p class="text-secondary small mb-0">Voices that should not exist.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=creatures" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">CREATURES</h3>
                        <h4 class="text-light fs-5 mb-1">Creature sightings</h4>
                        <p class="text-secondary small mb-0">Encounters with things not human.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=abandoned" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">ABANDONED</h3>
                        <h4 class="text-light fs-5 mb-1">Abandoned places</h4>
                        <p class="text-secondary small mb-0">Ruins that feel alive inside.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=psychological" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">PSYCHO</h3>
                        <h4 class="text-light fs-5 mb-1">Psychological horror</h4>
                        <p class="text-secondary small mb-0">Mind-bending tales that mess with your head.</p>
                    </a>
                </div>

            </div>

            <hr>

            <div class="row g-3">

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=demons" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">DEMONS</h3>
                        <h4 class="text-light fs-5 mb-1">Demonic encounters</h4>
                        <p class="text-secondary small mb-0">Stories involving dark entities and malevolent forces.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=possessed" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">POSSESSION</h3>
                        <h4 class="text-light fs-5 mb-1">Possession cases</h4>
                        <p class="text-secondary small mb-0">Terrifying accounts of losing control to something else.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=cursed" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">CURSED</h3>
                        <h4 class="text-light fs-5 mb-1">Cursed objects</h4>
                        <p class="text-secondary small mb-0">Items that bring bad luck or worse.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=rituals" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">RITUALS</h3>
                        <h4 class="text-light fs-5 mb-1">Dark rituals</h4>
                        <p class="text-secondary small mb-0">Games and rituals with disturbing outcomes.</p>
                    </a>
                </div>

            </div>
            <hr>
            <div class="row g-3 mt-2">

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=shadow" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">SHADOW</h3>
                        <h4 class="text-light fs-5 mb-1">Shadow figures</h4>
                        <p class="text-secondary small mb-0">Dark silhouettes that follow you at night.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=backrooms" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">BACKROOMS</h3>
                        <h4 class="text-light fs-5 mb-1">Backrooms stories</h4>
                        <p class="text-secondary small mb-0">Unsettling tales of strange liminal spaces.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=entities" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">ENTITIES</h3>
                        <h4 class="text-light fs-5 mb-1">Unknown entities</h4>
                        <p class="text-secondary small mb-0">Beings that defy logic and explanation.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=dreams" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">DREAMS</h3>
                        <h4 class="text-light fs-5 mb-1">Nightmare realms</h4>
                        <p class="text-secondary small mb-0">Dreams that feel too real and too dangerous.</p>
                    </a>
                </div>

            </div>
            <hr>
            <div class="row g-3 mt-2">

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=technology" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">TECH</h3>
                        <h4 class="text-light fs-5 mb-1">Glitched technology</h4>
                        <p class="text-secondary small mb-0">Devices acting with a mind of their own.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=internet" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">WEB</h3>
                        <h4 class="text-light fs-5 mb-1">Internet horror</h4>
                        <p class="text-secondary small mb-0">Creepy posts, accounts, and unexplained online events.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=hospital" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">HOSPITAL</h3>
                        <h4 class="text-light fs-5 mb-1">Hospital horror</h4>
                        <p class="text-secondary small mb-0">Midnight shifts inside medical nightmares.</p>
                    </a>
                </div>

                <div class="col-md-3 col-sm-6">
                    <a href="category_stories.php?cat=doppelganger" class="category-card d-block p-4 rounded-4">
                        <h3 class="text-danger fw-bold small mb-2" style="letter-spacing:0.15em;">MIRROR</h3>
                        <h4 class="text-light fs-5 mb-1">Doppelgänger</h4>
                        <p class="text-secondary small mb-0">When you see yourself somewhere you shouldn’t.</p>
                    </a>
                </div>

            </div>


        </section>

        <!-- Community section -->
        <section class="mb-4">
            <div class="community-box p-4">
                <div class="row g-4 align-items-start">
                    <!-- left side: pitch -->
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h2 class="section-title mb-0">Built for horror fans</h2>
                            <span class="badge bg-danger bg-opacity-75 rounded-pill small">
                                No AI stories. Real people only.
                            </span>
                        </div>

                        <p class="small text-secondary mb-3">
                            On silent_evidence you hang out with people who love late-night horror,
                            long comment threads, and that moment you double check your door before sleeping.
                        </p>

                        <ul class="small mb-3">
                            <li>Post your own stories with a free account.</li>
                            <li>Comment, react, and bookmark the ones that get in your head.</li>
                            <li>Filter by true stories, paranormal, urban legends, or short nightmares.</li>
                            <li>Use a nickname and stay as anonymous as you want.</li>
                        </ul>

                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <a href="signup.php" class="btn btn-silent-primary">
                                Join the community
                            </a>
                            <a href="stories.php" class="btn btn-outline-light btn-sm">
                                Browse stories first
                            </a>
                        </div>
                    </div>

                    <!-- right side: live stats -->
                    <div class="col-md-4">
                        <div class="community-panel p-3 small h-100 d-flex flex-column">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-2">
                                <p class="mb-1 fw-semibold text-light">
                                    Tonight on silent_evidence
                                </p>

                                <span class="badge bg-danger bg-opacity-75 rounded-pill panel-live mt-1 mt-sm-0">
                                    Live
                                </span>
                            </div>


                            <div class="d-flex justify-content-between mb-2">
                                <span class="panel-label">Stories published</span>
                                <span class="panel-value">
                                    <?php echo number_format($totalStories ?? 0); ?>
                                </span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="panel-label">Registered members</span>
                                <span class="panel-value">
                                    <?php echo number_format($totalUsers ?? 0); ?>
                                </span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="panel-label">Comments posted</span>
                                <span class="panel-value">
                                    <?php echo number_format($totalComments ?? 0); ?>
                                </span>
                            </div>

                            <hr class="border-secondary border-opacity-25 my-3">

                            <p class="text-secondary mb-0" style="font-size: 11px;">
                                These stats update straight from the database, so new stories and comments
                                show up here in real time.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

        </section>
        <!-- Footer -->
        <?php include 'include/footer.php'; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>