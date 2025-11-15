<footer class="footer-dark mt-5 pt-4 pb-4">
    <div class="container">

        <div class="row g-4">

            <!-- Brand -->
            <div class="col-md-4">
                <h4 class="footer-title">Silent Evidence</h4>
                <p class="footer-text mb-2">
                    Night stories, unexplained events, and encounters that stay in your head.
                </p>

                <p class="footer-text small mb-0 flicker-soft">
                    © <?php echo date('Y'); ?> Silent Evidence
                </p>
            </div>

            <!-- Navigation -->
            <div class="col-md-2">
                <h6 class="footer-sub">Explore</h6>
                <ul class="footer-list small">
                    <li><a href="stories.php" class="footer-link">Stories</a></li>
                    <li><a href="category_stories.php?cat=paranormal" class="footer-link">Paranormal</a></li>
                    <li><a href="category_stories.php?cat=true" class="footer-link">True stories</a></li>
                    <li><a href="top.php" class="footer-link">Top stories</a></li>
                </ul>
            </div>

            <!-- Community -->
            <div class="col-md-2">
                <h6 class="footer-sub">Community</h6>
                <ul class="footer-list small">
                    <li><a href="signup.php" class="footer-link">Join us</a></li>
                    <li><a href="login.php" class="footer-link">Login</a></li>
                    <li><a href="about.php" class="footer-link">About</a></li>
                </ul>
            </div>

            <!-- Social -->
            <div class="col-md-4">
                <h6 class="footer-sub">Social</h6>
                <p class="footer-text small mb-2">Stay updated on new drops.</p>

                <div class="d-flex gap-3">

                    <a href="#" class="social-icon">
                        <i class="bi bi-instagram"></i>
                    </a>

                    <a href="#" class="social-icon">
                        <i class="bi bi-tiktok"></i>
                    </a>

                    <a href="#" class="social-icon">
                        <i class="bi bi-youtube"></i>
                    </a>

                    <a href="#" class="social-icon">
                        <i class="bi bi-discord"></i>
                    </a>

                </div>

                <p class="creepy-quote text-secondary small mt-3"></p>

            </div>

        </div>

    </div>
</footer>

<script>
// random creepy quotes
const quotes = [
    "Someone else is awake with you right now.",
    "You heard that, right?",
    "Not every shadow is empty.",
    "Check your window. Just to be sure.",
    "Footsteps stop. Breathing doesn't.",
    "If you feel watched, you probably are.",
    "Something moved behind you."
];

document.querySelector(".creepy-quote").innerText =
    quotes[Math.floor(Math.random() * quotes.length)];
</script>

<style>
.footer-dark {
    background:#0b0f19;
    border-top:1px solid #1f2937;
}

.footer-title {
    color:#f8fafc;
    font-weight:700;
    text-shadow:0 0 8px rgba(255,0,0,0.4);
}

.footer-text {
    color:#94a3b8;
}

.footer-sub {
    color:#f1f5f9;
    font-size:0.95rem;
    margin-bottom:6px;
    text-shadow:0 0 6px rgba(255,0,0,0.25);
}

.footer-list {
    list-style:none;
    padding:0;
    margin:0;
}

.footer-link {
    color:#94a3b8;
    text-decoration:none;
    transition:0.2s;
}

.footer-link:hover {
    color:#f60000;
    padding-left:4px;
}

.social-icon {
    font-size:1.35rem;
    color:#94a3b8;
    transition:0.2s;
}

.social-icon:hover {
    color:#f60000;
    text-shadow:0 0 12px rgba(246,0,0,0.6);
}

.flicker-soft {
    animation:flicker 4s infinite;
    color:#e2e8f0;
}

@keyframes flicker {
    0%, 100% { opacity:1; }
    45% { opacity:0.85; }
    50% { opacity:0.55; }
    53% { opacity:0.9; }
    70% { opacity:0.8; }
    80% { opacity:0.65; }
}
</style>
