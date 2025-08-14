<?php
session_start();
if (!isset($_SESSION['username'])) $_SESSION['username'] = 'User';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Comment Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    </style>
</head>

<body>
    <div class="container-fluid p-3">
        <h3>Your Comment Dashboard</h3>

        <div class="card mt-4 p-3">
            <h6>Your Comments</h6>
            <table class="table table-dark table-striped mt-2" id="commentTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Story</th>
                        <th>Comment</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <script>
        async function loadComments() {
            const res = await fetch('fetch_user_comments.php');
            const comments = await res.json();
            const tbody = document.querySelector('#commentTable tbody');
            tbody.innerHTML = '';
            comments.forEach(c => {
                tbody.innerHTML += `<tr>
      <td>${c.id}</td>
      <td>${c.story_title}</td>
      <td>${c.comment}</td>
      <td>${c.created_at}</td>
      <td>
        <a href="edit_comment.php?id=${c.id}" class="btn btn-sm btn-warning">Edit</a>
        <a href="delete_comment.php?id=${c.id}" class="btn btn-sm btn-danger">Delete</a>
      </td>
    </tr>`;
            });
        }

        loadComments();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>