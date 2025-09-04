<?php
require 'includes/db.php';

// Detect logged-in user and role
$userId = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? null;
$role = $_SESSION['role'] ?? null;
$profileImage = $_SESSION['profile_image'] ?? 'https://via.placeholder.com/40';

// Get online users (last 5 mins)
$onlineWindow = date('Y-m-d H:i:s', time() - 300);
$onlineStmt = $db->prepare("
    SELECT username FROM users 
    WHERE last_active >= ?
");
$onlineStmt->execute([$onlineWindow]);
$onlineUsers = $onlineStmt->fetchAll(PDO::FETCH_COLUMN);

// Redirect after login based on role
if (isset($_GET['login']) && $userId) {
    if ($role === 'admin') {
        header("Location: /horror-blog/admin-dashboard.php");
    } else {
        header("Location: /horror-blog/profile.php?user=" . urlencode($username));
    }
    exit();
}
?>

<header>
  <nav class="navbar navbar-expand-lg navbar-dark bg-black shadow py-2 border-bottom border-danger">
    <div class="container-fluid">
      <!-- Brand -->
      <a class="navbar-brand text-danger fw-bold fs-3" href="/horror-blog/index.php">
        Silent <span class="d-none d-sm-inline">Evidence</span>
      </a>

      <!-- Mobile Toggle -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
        aria-controls="navbarContent" aria-expanded="false">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navbar Content -->
      <div class="collapse navbar-collapse" id="navbarContent">
        
        <!-- Left Menu -->
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 fs-5">
          <li class="nav-item">
            <a class="nav-link text-danger" href="/horror-blog/index.php">Home</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-danger" href="#" data-bs-toggle="dropdown">Explore</a>
            <ul class="dropdown-menu bg-black border border-danger">
              <li><a class="dropdown-item text-danger" href="/horror-blog/true-scary-stories.php">True Scary Stories</a></li>
              <li><a class="dropdown-item text-danger" href="/horror-blog/haunted_houses.php">Haunted Houses</a></li>
              <li><a class="dropdown-item text-danger" href="/horror-blog/whispers_beneath_the_pines.php">Whispers Beneath the Pines</a></li>
              <li><a class="dropdown-item text-danger" href="/horror-blog/late_night_encounters.php">Late Night Encounters</a></li>
              <li><a class="dropdown-item text-danger" href="/horror-blog/paranormal_witness.php">Paranormal Witness</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link text-danger" href="/horror-blog/submit-story.php">Submit Story</a>
          </li>
        </ul>

        <!-- Center Search -->
        <form class="d-flex mx-auto" style="max-width: 350px;" action="/horror-blog/search.php" method="GET">
          <div class="input-group">
            <input type="text" name="query" class="form-control bg-dark text-danger border-danger rounded-start-pill px-3"
              placeholder="Search stories..." required>
            <button class="btn btn-danger rounded-end-pill" type="submit">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </form>

        <!-- Right Menu -->
        <ul class="navbar-nav mb-2 mb-lg-0">
          <?php if ($userId): ?>
            <!-- Profile Dropdown -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-danger d-flex align-items-center"
                 href="#" data-bs-toggle="dropdown">
                <img src="<?= htmlspecialchars($profileImage) ?>" alt="Profile" class="rounded-circle me-2" width="35" height="35">
                <?= htmlspecialchars($username) ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end bg-black border border-danger mt-2">
                <?php if ($role === 'admin'): ?>
                  <li><a class="dropdown-item text-danger" href="/horror-blog/admin-dashboard.php">Dashboard</a></li>
                <?php else: ?>
                  <li><a class="dropdown-item text-danger" href="/horror-blog/profile.php?user=<?= urlencode($username) ?>">Profile</a></li>
                <?php endif; ?>
                <li><a class="dropdown-item text-danger" href="/horror-blog/logout.php">Log Out</a></li>
              </ul>
            </li>
          <?php else: ?>
            <!-- Login / Signup -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-danger border border-danger rounded-pill px-3"
                 href="#" data-bs-toggle="dropdown">
                Sign In
              </a>
              <ul class="dropdown-menu dropdown-menu-end bg-black border border-danger mt-2">
                <li><a class="dropdown-item text-danger" href="/horror-blog/log_in.php">Log In</a></li>
                <li><a class="dropdown-item text-danger" href="/horror-blog/sign_up.php">Sign Up</a></li>
              </ul>
            </li>
          <?php endif; ?>

          <!-- Online Users -->
        </ul>
      </div>
    </div>
  </nav>
</header>
