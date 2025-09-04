<?php
// You can include this sidebar on all pages to keep UI consistent
function e($string) {
  return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

$currentPage = basename($_SERVER['PHP_SELF']);
$user = $_SESSION['user'] ?? ['username' => 'Guest', 'profile_picture' => null];
?>

<nav class="sidebar" aria-label="Main navigation">
  <h3>Silent Evidence</h3>
  <div class="nav-links" role="navigation" aria-label="Profile sections">
    <a href="profile.php" class="<?= $currentPage == 'profile.php' ? 'active' : '' ?>">
      <i class="fas fa-user"></i> Profile
    </a>
    <a href="my-stories.php" class="<?= $currentPage == 'my-stories.php' ? 'active' : '' ?>">
      <i class="fas fa-book"></i> My Stories
    </a>
    <a href="liked-stories.php" class="<?= $currentPage == 'liked-stories.php' ? 'active' : '' ?>">
      <i class="fas fa-heart"></i> Liked
    </a>
    <a href="bookmarks.php" class="<?= $currentPage == 'bookmarks.php' ? 'active' : '' ?>">
      <i class="fas fa-bookmark"></i> Bookmarks
    </a>
    <a href="friends.php" class="<?= $currentPage == 'friends.php' ? 'active' : '' ?>">
      <i class="fas fa-user-friends"></i> Friends
    </a>
    <a href="settings.php" class="<?= $currentPage == 'settings.php' ? 'active' : '' ?>">
      <i class="fas fa-cog"></i> Settings
    </a>
  </div>
  <div class="bottom-links">
    <a href="chat.php"><i class="fas fa-comments"></i> Chat with Friends</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</nav>
