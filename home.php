<?php
session_start();

// Check if logged in
$isLoggedIn = isset($_SESSION['user']);
$userName = $isLoggedIn ? $_SESSION['user']['first'] : null;

// Check for logout message (from logout.php)
$logoutMsg = "";
if (isset($_SESSION['logout_message'])) {
    $logoutMsg = $_SESSION['logout_message'];
    unset($_SESSION['logout_message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/dropdown.css">
    <script src="scripts/slideShow.js" defer></script>
    <script src="scripts/youtubeAPICall.js" defer></script>
    <title>Vinny Ten Racing</title>

    <style>
        /* ACCOUNT DROPDOWN STYLE */
       

        /* 🔥 Logout Alert Styling */
        #logout-alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);

            background-color: #013783;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 18px;

            z-index: 9999;
            animation: fadeOut 1.8s ease forwards;
        }

        /* 🔥 Fade out animation */
        @keyframes fadeOut {
            0%   { opacity: 1; transform: translateX(-50%) translateY(0); }
            70%  { opacity: 1; }
            100% { opacity: 0; transform: translateX(-50%) translateY(-20px); }
        }
    </style>
</head>

<body>
    <?php if (!empty($logoutMsg)): ?>
        <div id="logout-alert" class="top-alert success"><?= $logoutMsg ?></div>
    <?php endif; ?>

    <?php include "header.php"; ?>


    <!-- ★★ REST OF YOUR HOMEPAGE CONTENT (unchanged) ★★ -->
    <div class="banner">
        <div class="promo-banner-slideshow">
            <div class="slide">
                <img src="./assets/subi-outside.jpg" alt="Promo 1">
            </div>
            <div class="slide">
                <img src="./assets/subi-wrx.jpg" alt="Promo 2">
            </div>
            <div class="slide">
                <img src="./assets/vinny-z.jpg" alt="Promo 3">
            </div>

            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
        </div>
    </div>

    <div class="dots" style="text-align:center">
        <span class="dot" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
        <span class="dot" onclick="currentSlide(3)"></span>
    </div>

    <div class="main-content">
        <section class="content-grid">
            <aside class="left-sidebar">
                <div class="card latest-video">
                    <h2>Latest Video</h2>
                    <iframe
                        id="latest-video"
                        width="100%"
                        height="250"
                        title="Vinny Ten Racing latest YouTube upload"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen>
                    </iframe>
                </div>
                <div class="newsletter-signup">
                    <h2>Newsletter</h2>
                    <form action="#">
                        <input type="email" placeholder="Enter your email" required>
                        <button type="submit">Subscribe</button>
                    </form>
                </div>
                <div class="socials">
                    <h2>Follow Us</h2>
                    <a href="https://www.facebook.com/VinnyTenRacing" target="_blank">Facebook</a>
                    <a href="https://www.instagram.com/vinnytenracing/" target="_blank">Instagram</a>
                    <a href="https://www.youtube.com/@VinnyTenTV" target="_blank">YouTube</a>
                </div>
                <div class="vinny-ten-info">
                    <h2>Vinny Ten Info</h2>
                    <ul class="info-section">
                        <li>
                            <details>
                                <summary>Hours of Operation</summary>
                                <ul>
                                    <li>Monday - Saturday: 9 AM - 6 PM</li>
                                    <li>Sunday: Closed</li>
                                    <li>**DYNO and TUNING can be available by appointment outside normal business hours**</li>
                                </ul>
                            </details>
                        </li>
                        <li><a href="direction.html">Directions</a></li>
                        <li><a href="privacy.html">Privacy Statement</a></li>
                        <li><a href="terms.html">Terms & Conditions</a></li>
                    </ul>
                </div>
            </aside>
            <main class="right-content">
                <div class="attention-grabber">
                    <h2>Built for Speed, Tuned for Perfection</h2>
                    <p>With over 30 years of turbocharging, tuning, and race engineering experience, we turn performance dreams 
                        into reality. From full custom builds to everyday maintenance, our team delivers precision, power, and 
                        trust — all under one roof. Whether it’s street, drag, drift, or rally, we’ve got the tools, tech, and 
                        passion to make it happen.</p>
                </div>
                <div class="featured-specials">
                    <div class="section-header">
                        <h2>Featured Specials</h2>
                        <p>view all</p>
                    </div>
                    <div class="specials-grid">
                        <div class="special-item">
                            <img src="./assets/special1.jpg" alt="Special 1">
                            <h3>UpRev</h3>
                            <p>$600.00</p>
                        </div>
                        <div class="special-item">
                            <img src="./assets/special2.jpg" alt="Special 2">
                            <h3>HP Tuners</h3>
                            <p>$600.00</p>
                        </div>
                        <div class="special-item">
                            <img src="./assets/special3.jpg" alt="Special 3">
                            <h3>Mesiterschaft Axleback Exhaust</h3>
                            <p>$250.00</p>
                        </div>
                    </div>
                </div>
                <div class="featured-packages">
                    <div class="section-header">
                        <h2>Featured Packages</h2>
                        <p>view all</p>
                    </div>
                    <div class="packages-grid">
                        <div class="package-item">
                            <img src="./assets/package1.jpg" alt="Package 1">
                            <h3>VTR Stage 1 Infiniti G37 Package</h3>
                            <button>Read More</button>
                        </div>
                        <div class="package-item">
                            <img src="./assets/package2.jpg" alt="Package 2">
                            <h3>VTR Stage 1 Nissan 370z Package</h3>
                            <button>Read More</button>
                        </div>
                        <div class="package-item">
                            <img src="./assets/package3.jpg" alt="Package 3">
                            <h3>Agency Power Subaru Turbo Back Exhaust Package</h3>
                            <button>Read More</button>
                        </div>
                        <div class="package-item">
                            <img src="./assets/package4.jpg" alt="Package 4">
                            <h3>Jim Wolf Technology 530BB Twin Turbo Kit</h3>
                            <button>Read More</button>
                        </div>
                    </div>
                </div>
            </main>
        </section>
    </div>

    <?php include "footer.php"; ?>


</body>
</html>
