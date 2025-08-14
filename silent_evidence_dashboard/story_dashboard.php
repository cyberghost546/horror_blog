<?php
session_start();
if (!isset($_SESSION['username'])) $_SESSION['username'] = 'Admin';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Story Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        body {
            background: #0f0f10;
            color: #eaeaea;
        }

        .card {
            background: #16181c;
            border-radius: 16px;
            border: 0;
        }

        .table th,
        .table td {
            color: #ff0000ff;
        }

        .navbar {
            background: #ff0000ff;
        }
    </style>
</head>

<body>

    <div class="container-fluid p-3">
        <h3>Story Dashboard</h3>

        <div class="row g-3 mt-2">
            <div class="col-md-3">
                <div class="card p-3">
                    <h6>Total Stories</h6>
                    <h3 id="statStories">0</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <h6>Total Views</h6>
                    <h3 id="statViews">0</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <h6>Comments</h6>
                    <h3 id="statComments">0</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <h6>Likes</h6>
                    <h3 id="statLikes">0</h3>
                </div>
            </div>
        </div>

        <div class="card mt-4 p-3">
            <h6>Story Views Last 7 Days</h6>
            <canvas id="storyChart" height="100"></canvas>
        </div>

        <div class="card mt-4 p-3">
            <h6>All Stories</h6>
            <table class="table table-dark table-striped mt-2" id="storyTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Views</th>
                        <th>Likes</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <script>
        let chart;
        async function loadStats() {
            const res = await fetch('fetch_story_stats.php');
            const data = await res.json();
            document.getElementById('statStories').textContent = data.stories;
            document.getElementById('statViews').textContent = data.views;
            document.getElementById('statComments').textContent = data.comments;
            document.getElementById('statLikes').textContent = data.likes;

            const labels = data.visits7.map(x => x.d);
            const values = data.visits7.map(x => x.c);

            if (!chart) {
                chart = new Chart(document.getElementById('storyChart'), {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Views',
                            data: values,
                            fill: true,
                            tension: 0.35
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            } else {
                chart.data.labels = labels;
                chart.data.datasets[0].data = values;
                chart.update();
            }
        }

        async function loadTable() {
            const res = await fetch('fetch_story_table.php');
            const stories = await res.json();
            const tbody = document.querySelector('#storyTable tbody');
            tbody.innerHTML = '';
            stories.forEach(s => {
                tbody.innerHTML += `<tr>
      <td>${s.id}</td>
      <td>${s.title}</td>
      <td>${s.author}</td>
      <td>${s.category}</td>
      <td>${s.views}</td>
      <td>${s.likes}</td>
      <td>${s.created_at}</td>
      <td>
        <a href="edit_story.php?id=${s.id}" class="btn btn-sm btn-warning">Edit</a>
        <a href="delete_story.php?id=${s.id}" class="btn btn-sm btn-danger">Delete</a>
      </td>
    </tr>`;
            });
        }

        loadStats();
        loadTable();
        setInterval(loadStats, 15000);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>