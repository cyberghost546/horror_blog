<?php
session_start();
if (!isset($_SESSION['username'])) {
  $_SESSION['username'] = 'Admin';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Silent Evidence Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
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
</head>

<body>
  <?php
  include 'aside_dashboard.php';
  ?>  

  <main class="content">
    <div class="topbar">
      <div class="d-flex align-items-center gap-2">
        <div class="circle"><i class="fa-solid fa-magnifying-glass"></i></div>
        <input id="q" class="form-control form-control-sm bg-dark text-white border-0" style="width: 260px" placeholder="Search...">
      </div>
      <div class="d-flex align-items-center gap-3 profile">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" id="darkToggle" checked>
          <label class="form-check-label" for="darkToggle">Dark</label>
        </div>
        <img src="https://i.pravatar.cc/100" alt="pfp">
        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
      </div>
    </div>

    <section class="grid">
      <div class="card p-3 stat">
        <div class="d-flex justify-content-between align-items-center">
          <h6>Total Users</h6>
          <div class="circle"><i class="fa-solid fa-user"></i></div>
        </div>
        <h3 id="statUsers">0</h3>
      </div>
      <div class="card p-3 stat">
        <div class="d-flex justify-content-between align-items-center">
          <h6>Total Stories</h6>
          <div class="circle"><i class="fa-solid fa-book"></i></div>
        </div>
        <h3 id="statStories">0</h3>
      </div>
      <div class="card p-3 stat">
        <div class="d-flex justify-content-between align-items-center">
          <h6>Comments</h6>
          <div class="circle"><i class="fa-solid fa-comment"></i></div>
        </div>
        <h3 id="statComments">0</h3>
      </div>
      <div class="card p-3 stat">
        <div class="d-flex justify-content-between align-items-center">
          <h6>Total Visits</h6>
          <div class="circle"><i class="fa-solid fa-chart-line"></i></div>
        </div>
        <h3 id="statVisits">0</h3>
      </div>
    </section>

    <section class="mt-4">
      <div class="card p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h6 class="m-0">Visits, last 7 days</h6>
          <small id="lastUpdated">Updated just now</small>
        </div>
        <canvas id="visitsChart" height="110"></canvas>
      </div>
    </section>
  </main>

  <script>
    let chart
    const elU = document.getElementById('statUsers')
    const elS = document.getElementById('statStories')
    const elC = document.getElementById('statComments')
    const elV = document.getElementById('statVisits')
    const elLU = document.getElementById('lastUpdated')

    async function loadStats() {
      try {
        const res = await fetch('fetch_stats.php', {
          cache: 'no-store'
        })
        const data = await res.json()
        elU.textContent = data.users
        elS.textContent = data.stories
        elC.textContent = data.comments
        elV.textContent = data.visits
        elLU.textContent = 'Updated ' + new Date().toLocaleTimeString()

        const labels = data.visits7.map(x => x.d)
        const values = data.visits7.map(x => x.c)

        if (!chart) {
          chart = new Chart(document.getElementById('visitsChart'), {
            type: 'line',
            data: {
              labels,
              datasets: [{
                label: 'Visits',
                data: values,
                tension: 0.35,
                fill: true
              }]
            },
            options: {
              plugins: {
                legend: {
                  display: false
                }
              },
              scales: {
                x: {
                  grid: {
                    display: false
                  }
                },
                y: {
                  beginAtZero: true,
                  ticks: {
                    precision: 0
                  }
                }
              }
            }
          })
        } else {
          chart.data.labels = labels
          chart.data.datasets[0].data = values
          chart.update()
        }
      } catch (e) {
        console.error(e)
      }
    }

    loadStats()
    setInterval(loadStats, 15000)

    // Dark mode toggle just flips body class
    const darkToggle = document.getElementById('darkToggle')
    darkToggle.addEventListener('change', () => {
      document.body.classList.toggle('bg-white')
      document.body.classList.toggle('text-dark')
    })
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>