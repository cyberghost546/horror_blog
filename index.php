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
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <!-- Search Bar -->
    <section class="container my-5">
        <form class="d-flex" role="search">
            <input class="form-control me-3" type="search" placeholder="Search anime or manga" aria-label="Search" />
            <button class="btn btn-success px-4">Search</button>
        </form>
    </section>

    <main class="container">

        <!-- Featured Anime of the Week -->
        <section class="container my-5">
            <h2 class="mb-3 text-center" style="color:#ff0000; text-transform: uppercase; letter-spacing: 2px;">
                Top 10 Anime & Manga
            </h2>

            <div id="topAnimeCarousel" class="carousel slide shadow-lg rounded" data-bs-ride="carousel" style="background: #0b0f14;">
                <div class="carousel-indicators">
                    <!-- 10 indicators -->
                    <button type="button" data-bs-target="#topAnimeCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1" style="background:#ff0000;"></button>
                    <button type="button" data-bs-target="#topAnimeCarousel" data-bs-slide-to="1" aria-label="Slide 2" style="background:#ff0000;"></button>
                    <button type="button" data-bs-target="#topAnimeCarousel" data-bs-slide-to="2" aria-label="Slide 3" style="background:#ff0000;"></button>
                    <button type="button" data-bs-target="#topAnimeCarousel" data-bs-slide-to="3" aria-label="Slide 4" style="background:#ff0000;"></button>
                    <button type="button" data-bs-target="#topAnimeCarousel" data-bs-slide-to="4" aria-label="Slide 5" style="background:#ff0000;"></button>
                    <button type="button" data-bs-target="#topAnimeCarousel" data-bs-slide-to="5" aria-label="Slide 6" style="background:#ff0000;"></button>
                    <button type="button" data-bs-target="#topAnimeCarousel" data-bs-slide-to="6" aria-label="Slide 7" style="background:#ff0000;"></button>
                    <button type="button" data-bs-target="#topAnimeCarousel" data-bs-slide-to="7" aria-label="Slide 8" style="background:#ff0000;"></button>
                    <button type="button" data-bs-target="#topAnimeCarousel" data-bs-slide-to="8" aria-label="Slide 9" style="background:#ff0000;"></button>
                    <button type="button" data-bs-target="#topAnimeCarousel" data-bs-slide-to="9" aria-label="Slide 10" style="background:#ff0000;"></button>
                </div>

                <div class="carousel-inner">
                    <!-- Each item with anime image + title + short desc -->
                    <div class="carousel-item active">
                        <img src="https://placehold.co/900x400?text=Attack+on+Titan" class="d-block w-100 rounded" alt="Attack on Titan" />
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded p-3" style="box-shadow: 0 0 80px #ff0000;">
                            <h5 style="color:#ff0000;">Attack on Titan</h5>
                            <p>Epic dark fantasy with giant Titans and brutal battles.</p>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <img src="https://placehold.co/900x400?text=My+Hero+Academia" class="d-block w-100 rounded" alt="My Hero Academia" />
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded p-3" style="box-shadow: 0 0 80px #ff0000;">
                            <h5 style="color:#ff0000;">My Hero Academia</h5>
                            <p>Superhero action with heartfelt character development.</p>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <img src="https://placehold.co/900x400?text=Demon+Slayer" class="d-block w-100 rounded" alt="Demon Slayer" />
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded p-3" style="box-shadow: 0 0 80px #ff0000;">
                            <h5 style="color:#ff0000;">Demon Slayer: Kimetsu no Yaiba</h5>
                            <p>Stunning animation and emotional storytelling.</p>
                        </div>
                    </div>

                    <!-- Add 7 more items similarly -->

                    <div class="carousel-item">
                        <img src="https://placehold.co/900x400?text=Naruto" class="d-block w-100 rounded" alt="Naruto" />
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded p-3" style="box-shadow: 0 0 80px #ff0000;">
                            <h5 style="color:#ff0000;">Naruto</h5>
                            <p>Classic ninja tale with a huge worldwide fanbase.</p>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <img src="https://placehold.co/900x400?text=One+Piece" class="d-block w-100 rounded" alt="One Piece" />
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded p-3" style="box-shadow: 0 0 80px #ff0000;">
                            <h5 style="color:#ff0000;">One Piece</h5>
                            <p>Epic pirate adventure with crazy world-building.</p>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <img src="https://placehold.co/900x400?text=Fullmetal+Alchemist" class="d-block w-100 rounded" alt="Fullmetal Alchemist" />
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded p-3" style="box-shadow: 0 0 80px #ff0000;">
                            <h5 style="color:#ff0000;">Fullmetal Alchemist: Brotherhood</h5>
                            <p>Deep story with alchemy and strong themes.</p>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <img src="https://placehold.co/900x400?text=Death+Note" class="d-block w-100 rounded" alt="Death Note" />
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded p-3" style="box-shadow: 0 0 80px #ff0000;">
                            <h5 style="color:#ff0000;">Death Note</h5>
                            <p>Thrilling psychological cat-and-mouse game.</p>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <img src="https://placehold.co/900x400?text=Tokyo+Ghoul" class="d-block w-100 rounded" alt="Tokyo Ghoul" />
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded p-3" style="box-shadow: 0 0 80px #ff0000;">
                            <h5 style="color:#ff0000;">Tokyo Ghoul</h5>
                            <p>Dark fantasy with a gritty atmosphere.</p>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <img src="https://placehold.co/900x400?text=Hunter+x+Hunter" class="d-block w-100 rounded" alt="Hunter x Hunter" />
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded p-3" style="box-shadow: 0 0 15px #ff0000;">
                            <h5 style="color:#ff0000;">Hunter x Hunter</h5>
                            <p>Adventure and strategy with complex characters.</p>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <img src="https://placehold.co/900x400?text=One+Punch+Man" class="d-block w-100 rounded" alt="One Punch Man" />
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded p-3" style="box-shadow: 0 0 80px #ff0000;">
                            <h5 style="color:#ff0000;">One Punch Man</h5>
                            <p>Action comedy with a hilarious twist.</p>
                        </div>
                    </div>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#topAnimeCarousel" data-bs-slide="prev" style="filter: drop-shadow(0 0 5px #ff0000);">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="visually-hidden">Previous</span>
                </button>

                <button class="carousel-control-next" type="button" data-bs-target="#topAnimeCarousel" data-bs-slide="next" style="filter: drop-shadow(0 0 5px #ff0000);">
                    <span class="carousel-control-next-icon"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </section>


        <!-- Trending Manga -->
        <section class="mb-5">
            <h2>Trending Manga</h2>
            <div class="row g-4">
                <div class="col-6 col-md-3">
                    <div class="card">
                        <img src="https://placehold.co/200x300?text=Chainsaw+Man" class="card-img-top" alt="Chainsaw Man" />
                        <div class="card-body">
                            <h5 class="card-title">Chainsaw Man</h5>
                            <p class="card-text">Dark fantasy with wild action and a unique style.</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card">
                        <img src="https://placehold.co/200x300?text=Spy+x+Family" class="card-img-top" alt="Spy x Family" />
                        <div class="card-body">
                            <h5 class="card-title">Spy x Family</h5>
                            <p class="card-text">Comedy and espionage blend perfectly in this hit manga.</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card">
                        <img src="https://placehold.co/200x300?text=Tokyo+Revengers" class="card-img-top" alt="Tokyo Revengers" />
                        <div class="card-body">
                            <h5 class="card-title">Tokyo Revengers</h5>
                            <p class="card-text">Time travel and gang drama keep you hooked.</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card">
                        <img src="https://placehold.co/200x300?text=Blue+Lock" class="card-img-top" alt="Blue Lock" />
                        <div class="card-body">
                            <h5 class="card-title">Blue Lock</h5>
                            <p class="card-text">High-stakes soccer manga with intense rivalries.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- User Reviews -->
        <section class="mb-5">
            <h2>User Reviews</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card p-3">
                        <h5 class="card-title">JohnD92</h5>
                        <p class="card-text fst-italic">"Attack on Titan blew me away. The story and animation are top-notch."</p>
                        <small class="text-muted">Posted 2 days ago</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3">
                        <h5 class="card-title">MangaFan88</h5>
                        <p class="card-text fst-italic">"Chainsaw Man's unique style is unmatched. Can't wait for the next chapter!"</p>
                        <small class="text-muted">Posted 5 days ago</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3">
                        <h5 class="card-title">AnimeLover</h5>
                        <p class="card-text fst-italic">"My Hero Academia keeps getting better season after season."</p>
                        <small class="text-muted">Posted 1 week ago</small>
                    </div>
                </div>
            </div>
        </section>

        <!-- Newsletter Signup -->
        <section class="mb-5 p-4 rounded shadow text-center" style="background: #161b22; border: 1.5px solid #ff0000;">
            <h2>Subscribe to Our Newsletter</h2>
            <form class="d-flex justify-content-center mt-3" action="#" method="post" style="max-width: 500px; margin:auto;">
                <input type="email" name="email" class="form-control me-3" placeholder="Enter your email" required />
                <button type="submit" class="btn btn-success px-4">Subscribe</button>
            </form>
        </section>

        <!-- Social Media Links -->
        <section class="mb-5 text-center">
            <h2>Follow Us</h2>
            <a href="https://twitter.com" target="_blank" class="btn btn-outline-success me-3 px-4">Twitter</a>
            <a href="https://instagram.com" target="_blank" class="btn btn-outline-success me-3 px-4">Instagram</a>
            <a href="https://discord.com" target="_blank" class="btn btn-outline-success px-4">Discord</a>
        </section>

        <!-- Author Bio -->
        <section class="mb-5 p-4 rounded shadow" style="background: #161b22; border: 1.5px solid #ff0000;">
            <h2>About The Author</h2>
            <p>Hey, I’m Chris, a software developer and hardcore anime fan. This blog is my way of sharing anime reviews, news, and all things otaku culture. Thanks for visiting!</p>
        </section>

    </main>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>


    <!-- Bootstrap JS Bundle CDN (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>