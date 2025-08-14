<style>
    body {
      background-color: #0f0f10;
      color: #eaeaea
    }

    .sidebar {
      height: 100vh;
      background: #151517;
      position: fixed;
      width: 260px;
      padding: 20px 14px
    }

    .brand {
      font-weight: 800;
      color: #ff3b3b;
      letter-spacing: 0.5px
    }

    .nav-link {
      color: #bdbdbd;
      border-radius: 10px;
      padding: 10px 12px;
      display: flex;
      gap: 10px;
      align-items: center
    }

    .nav-link:hover,
    .nav-link.active {
      background: #ff3b3b;
      color: #fff
    }

    .content {
      margin-left: 260px;
      padding: 20px
    }

    .topbar {
      background: #151517;
      border-radius: 12px;
      padding: 10px 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 18px
    }

    .card {
      background: #16181c;
      border: 0;
      border-radius: 16px
    }

    .stat h6 {
      color: #a7a7a7;
      margin-bottom: 6px
    }

    .stat h3 {
      margin: 0
    }

    .circle {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background: #ff0000ff;
      display: grid;
      place-items: center
    }

    .profile img {
      width: 40px;
      height: 40px;
      border-radius: 50%
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 16px
    }

    @media (max-width: 1200px) {
      .grid {
        grid-template-columns: repeat(2, 1fr)
      }
    }

    @media (max-width: 700px) {
      .grid {
        grid-template-columns: 1fr
      }

      .content {
        margin-left: 0
      }

      .sidebar {
        position: static;
        width: 100%;
        height: auto
      }
    }
  </style>

  <aside class="sidebar">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <div class="brand">Silent Evidence</div>
    </div>
    <nav class="d-grid gap-2">
      <a class="nav-link active" href="#"><i class="fa-solid fa-table-columns"></i><span>Dashboard</span></a>
      <a class="nav-link" href="story_dashboard.php"><i class="fa-solid fa-book"></i><span>Stories</span></a>
      <a class="nav-link" href="user_story_dashboard.php"><i class="fa-solid fa-users"></i><span>Users</span></a>
      <a class="nav-link" href="user_comment_dashboard.php"><i class="fa-solid fa-comments"></i><span>Comments</span></a>
      <a class="nav-link" href="#"><i class="fa-solid fa-gear"></i><span>Settings</span></a>
      <a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
    </nav>
  </aside>