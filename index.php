<?php
session_start();
require 'include/db.php';

$latestStories = [
    [
        'tag' => 'True event',
        'title' => 'The footsteps in my hallway',
        'excerpt' => 'It started with one soft step on a wooden floor that did not exist in my house.',
        'read_time' => '8 min read',
        'comments' => 214
    ],
    [
        'tag' => 'Sleep paralysis',
        'title' => 'The man in the corner of my room',
        'excerpt' => 'Every night at 3:17 he moves one step closer to my bed.',
        'read_time' => '6 min read',
        'comments' => 142
    ],
    [
        'tag' => 'Urban legend',
        'title' => 'The number you should never call',
        'excerpt' => 'We dialed it as a joke. The voice that answered knew our names.',
        'read_time' => '10 min read',
        'comments' => 98
    ],
];
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
                <h2 class="section-title mb-0">Latest stories</h2> <a href="stories.php" class="small text-secondary">View all</a>
            </div>

            <div class="row g-3">
                <?php foreach ($latestStories as $story): ?>
                    <div class="col-md-4">
                        <article class="story-card h-100 p-3">
                            <p class="story-tag mb-1"><?php echo htmlspecialchars($story['tag']) ?></p>
                            <h3 class="story-title mb-2"><?php echo htmlspecialchars($story['title']) ?></h3>
                            <p class="story-excerpt mb-3">
                                <?php echo htmlspecialchars($story['excerpt']) ?>
                            </p>
                            <div class="story-meta d-flex justify-content-between">
                                <span><?php echo htmlspecialchars($story['read_time']) ?></span>
                                <span><?php echo (int)$story['comments'] ?> comments</span>
                            </div>
                        </article>
                    </div>
                <?php endforeach ?>
            </div>
        </section>

        <!-- Categories -->
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-baseline mb-3">
                <h2 class="section-title mb-0">Browse by vibe</h2>
            </div>
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <a href="stories.php?cat=true" class="category-card d-block h-100 p-3">
                        <h3 class="h6 text-light mb-1">True stories</h3>
                        <p class="small text-secondary mb-0">Real experiences users claim actually happened.</p>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="stories.php?cat=paranormal" class="category-card d-block h-100 p-3">
                        <h3 class="h6 text-light mb-1">Paranormal</h3>
                        <p class="small text-secondary mb-0">Ghosts, spirits, haunted houses, cursed objects.</p>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="stories.php?cat=urban" class="category-card d-block h-100 p-3">
                        <h3 class="h6 text-light mb-1">Urban legends</h3>
                        <p class="small text-secondary mb-0">Stories that spread online and feel too real.</p>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="stories.php?cat=short" class="category-card d-block h-100 p-3">
                        <h3 class="h6 text-light mb-1">Short nightmares</h3>
                        <p class="small text-secondary mb-0">Quick reads that hit fast.</p>
                    </a>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <a href="stories.php?cat=true" class="category-card d-block h-100 p-3">
                        <h3 class="h6 text-light mb-1">True stories</h3>
                        <p class="small text-secondary mb-0">Real experiences users claim actually happened.</p>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="stories.php?cat=paranormal" class="category-card d-block h-100 p-3">
                        <h3 class="h6 text-light mb-1">Paranormal</h3>
                        <p class="small text-secondary mb-0">Ghosts, spirits, haunted houses, cursed objects.</p>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="stories.php?cat=urban" class="category-card d-block h-100 p-3">
                        <h3 class="h6 text-light mb-1">Urban legends</h3>
                        <p class="small text-secondary mb-0">Stories that spread online and feel too real.</p>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="stories.php?cat=short" class="category-card d-block h-100 p-3">
                        <h3 class="h6 text-light mb-1">Short nightmares</h3>
                        <p class="small text-secondary mb-0">Quick reads that hit fast.</p>
                    </a>
                </div>
            </div>
        </section>

        <!-- Community section -->
        <section class="mb-4">
            <div class="community-box p-4">
                <div class="row g-4 align-items-start">
                    <div class="col-md-8">
                        <h2 class="section-title mb-2">Built for horror fans</h2>
                        <p class="small text-secondary mb-3"> On silent_evidence you join a community that lives for late night stories, wild comment threads, and that moment you need to double check your door. </p>
                        <ul class="small">
                            <li>Post your own stories with an account</li>
                            <li>Comment and rate other stories</li>
                            <li>Bookmark favorites to read later</li>
                            <li>Stay anonymous if you want</li>
                        </ul>

                        <a href="signup.php" class="btn btn-silent-primary btn-sm mt-2">Join the community</a>
                    </div>

                    <div class="col-md-4">
                        <div class="community-panel p-3 small">
                            <p class="mb-3 fw-semibold text-light">Tonight on silent_evidence</p>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="panel-label">Stories posted</span>
                                <span class="panel-value">127</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="panel-label">Users online</span>
                                <span class="panel-value panel-live">34</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="panel-label">Average read time</span>
                                <span class="panel-value">7 min</span>
                            </div>

                            <p class="text-secondary mt-3 mb-0" style="font-size: 11px;">
                                These numbers are placeholders. You can hook them to your database later.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Footer -->
        <footer class="text-center pt-3 border-top border-dark mt-4">
            <p class="footer-text mb-1"> silent_evidence © <?php echo date('Y') ?> All rights reserved </p>
            <p class="footer-text mb-0"> Built for people who read horror with the lights off. </p>
        </footer>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>