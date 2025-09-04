<?php
session_start();
require_once "config.php"; // your DB connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch liked stories
$sql = "SELECT s.id, s.title, s.slug, s.summary, s.image, s.views, s.created_at 
        FROM likes l
        JOIN stories s ON l.story_id = s.id
        WHERE l.user_id = ?
        ORDER BY l.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$liked_stories = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Liked Stories</title>
    <style>
        body {
            background-color: #0d0d0d;
            color: #f2f2f2;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        header {
            background-color: #1a1a1a;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            border-bottom: 1px solid #333;
        }
        .container {
            width: 90%;
            max-width: 1100px;
            margin: 20px auto;
        }
        .story-card {
            background-color: #1a1a1a;
            border: 1px solid #333;
            margin-bottom: 20px;
            display: flex;
            overflow: hidden;
            border-radius: 5px;
        }
        .story-card img {
            width: 200px;
            object-fit: cover;
        }
        .story-content {
            padding: 15px;
            flex: 1;
        }
        .story-title {
            font-size: 20px;
            margin: 0 0 10px;
            color: #ff3c3c;
        }
        .story-summary {
            font-size: 14px;
            color: #ccc;
        }
        .meta {
            font-size: 12px;
            color: #777;
            margin-top: 10px;
        }
        .read-more {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 12px;
            background-color: #ff3c3c;
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            border-radius: 3px;
        }
        .read-more:hover {
            background-color: #cc3232;
        }
    </style>
</head>
<body>
<header>Stories You Liked</header>
<div class="container">
    <?php if (count($liked_stories) > 0): ?>
        <?php foreach ($liked_stories as $story): ?>
            <div class="story-card">
                <?php if (!empty($story['image'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($story['image']); ?>" alt="<?php echo htmlspecialchars($story['title']); ?>">
                <?php else: ?>
                    <img src="images/default-story.jpg" alt="No Image">
                <?php endif; ?>
                <div class="story-content">
                    <h2 class="story-title"><?php echo htmlspecialchars($story['title']); ?></h2>
                    <p class="story-summary"><?php echo htmlspecialchars(mb_strimwidth($story['summary'], 0, 180, "...")); ?></p>
                    <div class="meta">
                        Views: <?php echo $story['views']; ?> | 
                        Liked on: <?php echo date("M d, Y", strtotime($story['created_at'])); ?>
                    </div>
                    <a class="read-more" href="story.php?slug=<?php echo urlencode($story['slug']); ?>">Read More</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You have not liked any stories yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
