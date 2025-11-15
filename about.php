<?php
session_start();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>About Silent Evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #020617;
            color: #e5e7eb;
            font-family: system-ui, sans-serif;
        }

        .page-wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px 16px 60px;
        }

        h1 {
            font-weight: 700;
            font-size: 2rem;
            color: #f9fafb;
        }

        h2 {
            font-weight: 600;
            color: #f9fafb;
            font-size: 1.4rem;
        }

        p {
            color: #cbd5e1;
            font-size: 0.95rem;
        }

        .section-box {
            background-color: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 20px;
        }

        .section-box:hover {
            border-color: #f60000;
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.15);
            transition: 0.25s;
        }

        .highlight {
            color: #f60000;
            font-weight: 600;
        }
    </style>
</head>

<body>

<?php include 'include/header.php'; ?>

<div class="page-wrapper">

    <header class="mb-4">
        <h1>About Silent Evidence</h1>
        <p class="text-secondary mt-1">
            The home of true horror, unexplained events, and stories you read with the lights on.
        </p>
    </header>

    <div class="section-box">
        <h2>What is Silent Evidence?</h2>
        <p>
            Silent Evidence is a community for people who love horror. 
            Stories that feel real. Encounters that sound impossible. 
            Late night reads that make you check your door twice.
        </p>

        <p>
            You can read stories, post your own, comment on others, and build your own following.
            Everything is powered by users, not corporations.
        </p>
    </div>

    <div class="section-box">
        <h2>Our Purpose</h2>
        <p>
            We created Silent Evidence to give people a place to share the moments they never forget.
            Whether your story is real, paranormal, or something you can't explain,
            this platform exists to give it a voice.
        </p>
        <p>
            Horror hits different when it sounds like it happened to someone like you.
        </p>
    </div>

    <div class="section-box">
        <h2>How the community works</h2>
        <p>
            ✔ Post your own stories  
            ✔ Read thousands of horror tales  
            ✔ Comment, like, and bookmark  
            ✔ Stay anonymous if you want  
            ✔ Explore categories from <span class="highlight">true stories</span> to
            <span class="highlight">paranormal</span>, <span class="highlight">urban legends</span>, 
            and more
        </p>
    </div>

    <div class="section-box">
        <h2>Why the name Silent Evidence?</h2>
        <p>
            Because the scariest stories are the ones with just enough proof to make you wonder.
            The ones that stay quiet, but stay in your head.
        </p>
    </div>

    <div class="section-box mb-4">
        <h2>Created by the Community</h2>
        <p>
            Silent Evidence grows with its users. If you want to help build features, submit ideas, or
            share feedback, you are part of it. This place evolves because of you.
        </p>
    </div>

    <footer class="text-secondary mt-4 small">
        Silent Evidence © <?php echo date('Y'); ?>. All rights reserved.
    </footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
