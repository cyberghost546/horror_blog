<?php
session_start();
require 'includes/db.php';
global $db;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: log_in.php");
    exit();
}
// Define timeout (e.g. last 5 minutes = online)
$onlineWindow = date('Y-m-d H:i:s', time() - 300); // 5 minutes ago

$onlineUsersStmt = $db->prepare("
    SELECT u.id, u.username, u.email
    FROM users u
    JOIN user_sessions s ON u.id = s.user_id
    WHERE s.last_active >= ?
");
$onlineUsersStmt->execute([$onlineWindow]);
$onlineUsers = $onlineUsersStmt->fetchAll();

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: log_in.php");
    exit();
}

$newUsersStmt = $db->prepare("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$newUsersStmt->execute();
$newUsersCount = $newUsersStmt->fetchColumn();


// Fetch data
$visitorCount = $db->query("SELECT COUNT(*) FROM visits")->fetchColumn();
$userCount = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$popularStories = $db->query("SELECT * FROM stories ORDER BY views DESC LIMIT 5")->fetchAll();
$users = $db->query("SELECT id, username, email, role FROM users")->fetchAll();
$allStories = $db->query("SELECT * FROM stories ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Silent Evidence Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #0a0a0f;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background: #1b1b2f;
            height: 100vh;
            position: fixed;
            width: 220px;
            top: 0;
            left: 0;
            padding: 1rem;
            box-shadow: 0 0 130px #ff0040;
        }

        .sidebar h3 {
            color: #ff0040;
            font-weight: bold;
            margin-bottom: 2rem;
        }

        .sidebar a {
            display: block;
            color: #ffffff;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #ff000052;
            color: #fff;
        }

        .main {
            margin-left: 240px;
            padding: 2rem;
        }

        .card {
            background: #14141f;
            border: 1px solid #333;
            margin-bottom: 2rem;
        }

        .card h5 {
            color: #ff0040;
        }

        .btn-horror {
            background-color: #ff0040;
            color: #fff;
        }

        .btn-horror:hover {
            background-color: #c10030;
        }

        .table-dark th {
            color: #ff0040;
        }

        .icon {
            color: #ff0040;
            margin-right: 10px;
        }

        input,
        textarea,
        select {
            background-color: #1c1c28 !important;
            color: #fff !important;
            border: 1px solid #ff0040 !important;
        }

        input:focus,
        textarea:focus,
        select:focus {
            box-shadow: 0 0 5px #ff0040;
        }

        .form-control::placeholder {
            color: #999;
        }

        .btn-horror {
            background-color: #8b0000;
            color: white;
            border: none;
        }

        .btn-horror:hover {
            background-color: #a80000;
        }
    </style>
</head>

<body>
    <div class="sidebar bg-dark">
        <h3><i class="bi bi-emoji-sunglasses icon"></i>Silent Admin</h3>
        <a href="#" class="active bg-success"><i class="bi bi-house icon"></i> Dashboard</a>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-light" href="admin_users.php">
                    <i class="bi bi-people me-2"></i> Users
                </a>

            </li>
            <li class="nav-item">
                <a class="nav-link text-light" href="admin_stories.php">
                    <i class="fas fa-file-alt me-2"></i> Stories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-light" href="admin_settings.php">
                    <i class="fas fa-cogs me-2"></i> Settings
                </a>
            </li>
        </ul>
        <a href="logout.php"><i class="bi bi-box-arrow-right icon"></i> Logout</a>
    </div>

    <div class="main">
        <h2 class="mb-4 text-danger">Admin Dashboard</h2>


        <!-- STATS -->
        <div class="row">
            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h5>Total Users</h5>
                    <p class="display-6 text-success"><?= $userCount ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h5>Total Stories</h5>
                    <p class="display-6 text-success"><?= count($allStories) ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h5>New Users</h5>
                    <p id="new-users-count" class="display-6 text-success"><?= $newUsersCount ?></p>
                </div>
            </div>

            <div class="card bg-secondary mb-3">
                <div class="card-body">
                    <h4>Total Website Visits: <?= $visitorCount ?></h4>
                </div>
            </div>
        </div>

        <!-- COUNTDOWN -->
        <?php

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $drop_time = $_POST['drop_time'];
            $stmt = $db->prepare("UPDATE story_drop SET drop_time = ? WHERE id = 1");
            $stmt->execute([$drop_time]);
            echo "Drop time updated.";
        }

        $dropTimeStmt = $db->query("SELECT drop_time FROM story_drop WHERE id = 1");
        $currentDropTime = $dropTimeStmt->fetchColumn();
        ?>
        <style>
            .card1 {
                background-color: #1a1a1a;
                padding: 20px;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(255, 0, 0, 0.2);
                max-width: 400px;
                font-family: Arial, sans-serif;
                color: #fff;
            }

            .card1 h3 {
                margin: 0 0 15px 0;
                font-size: 18px;
                color: #ff4d4d;
                letter-spacing: 1px;
                border-bottom: 1px solid rgba(255, 77, 77, 0.3);
                padding-bottom: 8px;
            }

            .card1 label {
                display: block;
                font-size: 14px;
                margin-bottom: 8px;
                color: #ccc;
            }

            .card1 input[type="datetime-local"] {
                width: 100%;
                padding: 10px;
                border-radius: 6px;
                background-color: #2a2a2a;
                border: none;
                color: white;
                font-size: 14px;
                box-shadow: inset 0 0 5px rgba(255, 0, 0, 0.3);
            }

            .card1 input[type="datetime-local"]:focus {
                outline: none;
                box-shadow: inset 0 0 10px rgba(255, 0, 0, 0.6);
            }

            .card1 button {
                margin-top: 12px;
                width: 100%;
                padding: 10px;
                border: none;
                border-radius: 6px;
                font-size: 14px;
                font-weight: bold;
                color: white;
                background: linear-gradient(90deg, #ff0000, #990000);
                cursor: pointer;
                transition: background 0.3s ease;
            }

            .card1 button:hover {
                background: linear-gradient(90deg, #cc0000, #660000);
            }
        </style>

        <div class="card1 text-center align-items-center">
            <h3>Next Story Drop</h3>
            <form method="post">
                <label>Drop Time</label>
                <input type="datetime-local" name="drop_time" required value="<?= date('Y-m-d\TH:i', strtotime($currentDropTime)) ?>">
                <button type="submit">Save</button>
            </form>
        </div>


        <!-- POPULAR STORIES -->
        <div class="card p-3">
            <h5 class="text-danger">Popular Stories</h5>
            <ul class="list-group bg-dark">
                <?php foreach ($popularStories as $story): ?>
                    <li class="list-group-item bg-dark text-light border-danger">
                        <i class="bi bi-star-fill text-warning"></i>
                        <?= htmlspecialchars($story['title']) ?> (<?= $story['views'] ?> views)
                        <form action="feature_story.php" method="POST" class="d-inline">
                            <input type="hidden" name="story_id" value="<?= $story['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Feature</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- ADD STORY -->
        <div class="card p-3 bg-dark text-light border-danger">
            <h5 class="text-danger">Add New Story</h5>
            <form action="add_story.php" method="POST">
                <div class="mb-3">
                    <label for="title" class="form-label">Story Title</label>
                    <input class="form-control bg-dark text-light border-danger" type="text" name="title" id="title" placeholder="Story Title" required>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Story Content</label>
                    <textarea class="form-control bg-dark text-light border-danger" name="content" id="content" rows="6" placeholder="Write your horror story..." required></textarea>
                </div>

                <div class="mb-3">
                    <label for="category" class="form-label text-light">Choose a Category</label>
                    <select name="category" id="category" class="form-select bg-dark text-light border-danger" required>
                        <option value="True Scary Stories">True Scary Stories</option>
                        <option value="Haunted Houses">Haunted Houses</option>
                        <option value="Highway Horror">Highway Horror</option>
                        <option value="Late Night Encounters">Late Night Encounters</option>
                        <option value="Paranormal Witness">Paranormal Witness</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-danger w-100">Submit Story</button>
            </form>
        </div>


        <!-- USER ROLES -->
        <div class="card p-3">
            <h5 class="text-danger">User Role Management</h5>
            <table class="table table-dark table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= $user['role'] ?></td>
                            <td>
                                <form action="update_role.php" method="POST" class="d-flex">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <select name="new_role" class="form-select me-2">
                                        <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-danger">Change</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- DELETE STORY -->
        <div class="card p-3">
            <h5 class="text-danger">Moderate Stories</h5>
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allStories as $story): ?>
                        <tr>
                            <td><?= $story['id'] ?></td>
                            <td><?= htmlspecialchars($story['title']) ?></td>
                            <td><?= $story['created_at'] ?></td>
                            <td>
                                <form action="delete_story.php" method="POST" onsubmit="return confirm('Remove this story?');">
                                    <input type="hidden" name="story_id" value="<?= $story['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="countdown"></div>

    <script>
        fetch('get_drop_time.php')
            .then(res => res.json())
            .then(data => {
                const dropTime = new Date(data.drop_time).getTime();

                function updateCountdown() {
                    const now = new Date().getTime();
                    const distance = dropTime - now;

                    if (distance <= 0) {
                        document.getElementById("countdown").innerHTML = "00:00:00";
                        return;
                    }

                    const hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                    const minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                    const seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');

                    document.getElementById("countdown").innerHTML = `${hours}:${minutes}:${seconds}`;
                }

                updateCountdown();
                setInterval(updateCountdown, 1000);
            });
    </script>

</body>

</html>