<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Silent Evidence</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="css/style.css" />

  <style>
  </style>
</head>

<body>

  <?php include 'includes/header.php'; ?>

  <!-- Search Bar -->
  <section class="container my-5">
    <form class="d-flex" role="search">
      <input class="form-control me-3" type="search" placeholder="Search horror stories" aria-label="Search" />
      <button class="btn btn-success px-4">Search</button>
    </form>
  </section>

  <main class="container">

    <!-- Featured Horror Stories -->
    <section class="container my-5">
      <h2 class="mb-3 text-center" style="letter-spacing: 5px;">
        Top 10 Horror Stories
      </h2>

      <div id="topHorrorCarousel" class="carousel slide shadow-lg rounded" data-bs-ride="carousel">
        <div class="carousel-indicators">
          <?php for ($i = 0; $i < 10; $i++): ?>
            <button type="button" data-bs-target="#topHorrorCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i == 0 ? 'active' : '' ?>" style="background:#b30000;"></button>
          <?php endfor; ?>
        </div>

        <div class="carousel-inner">
          <div class="carousel-item active">
            <img src="https://placehold.co/900x400/000000/ff0000?text=Haunted+House" class="d-block w-100 rounded" alt="Haunted House" />
            <div class="carousel-caption d-none d-md-block">
              <h5>Haunted House</h5>
              <p>Where every creak could be your last.</p>
            </div>
          </div>

          <div class="carousel-item">
            <img src="https://placehold.co/900x400/000000/ff0000?text=Forest+of+Shadows" class="d-block w-100 rounded" alt="Forest of Shadows" />
            <div class="carousel-caption d-none d-md-block">
              <h5>Forest of Shadows</h5>
              <p>Some paths should never be walked.</p>
            </div>
          </div>

          <!-- More items here -->
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#topHorrorCarousel" data-bs-slide="prev" style="filter: drop-shadow(0 0 5px #ff0000);">
          <span class="carousel-control-prev-icon"></span>
          <span class="visually-hidden">Previous</span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#topHorrorCarousel" data-bs-slide="next" style="filter: drop-shadow(0 0 5px #ff0000);">
          <span class="carousel-control-next-icon"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
    </section>

    <!-- Trending Horror Reads -->
    <section class="mb-5">
      <h2>Trending Horror Reads</h2>
      <div class="row g-4">
        <div class="col-6 col-md-3">
          <div class="card">
            <img src="https://placehold.co/200x300/000000/ff0000?text=The+Dollmaker" class="card-img-top" alt="The Dollmaker" />
            <div class="card-body">
              <h5 class="card-title">The Dollmaker</h5>
              <p class="card-text">Every doll has a soul... but whose?</p>
            </div>
          </div>
        </div>
        <!-- More horror cards -->
      </div>
    </section>

    <!-- User Reviews -->
    <section class="mb-5">
      <h2>User Reviews</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card p-3">
            <h5 class="card-title">ShadowHunter</h5>
            <p class="card-text fst-italic">"Haunted House chilled me to the bone. Couldn’t sleep after reading."</p>
            <div class="mb-2 text-warning">
              ★★★★☆
            </div>
            <small class="text-muted">Posted 2 days ago</small>
          </div>
        </div>
        <!-- More reviews -->
      </div>
    </section>

    <!-- Newsletter -->
    <section class="mb-5 p-4 text-center" style="background: #111; border: 1.5px solid #b30000;">
      <h2>Join the Dark Side</h2>
      <form class="d-flex justify-content-center mt-3" style="max-width: 500px; margin:auto;">
        <input type="email" class="form-control me-3" placeholder="Enter your email" required />
        <button type="submit" class="btn btn-success px-4">Subscribe</button>
      </form>
    </section>

    <!-- Social Media -->
    <section class="mb-5 text-center">
      <h2>Follow the Fear</h2>
      <a href="#" class="btn btn-outline-success me-3 px-4">Twitter</a>
      <a href="#" class="btn btn-outline-success me-3 px-4">Instagram</a>
      <a href="#" class="btn btn-outline-success px-4">Discord</a>
    </section>

    <!-- About Author -->
    <section class="mb-5 p-4" style="background: #111; border: 1.5px solid #b30000;">
      <h2>About The Author</h2>
      <p>I’m Chris. I collect the most spine-chilling horror tales from around the world. If you dare, step inside...</p>
    </section>

  </main>

  <?php include 'includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>