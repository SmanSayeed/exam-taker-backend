<!-- Preloader -->
<div id="preloader">
    <div class="spinner-grow text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- Internet Connection Status -->
<div class="internet-connection-status" id="internetStatus"></div>

<!-- Header Area -->
<div class="header-area" id="headerArea">
    <div class="container">
        <!-- Header Content -->
        <div
            class="header-content header-style-five position-relative d-flex align-items-center justify-content-between">

            <!-- Navbar Toggler -->
            <div class="navbar--toggler" id="affanNavbarToggler" data-bs-toggle="offcanvas"
                data-bs-target="#affanOffcanvas" aria-controls="affanOffcanvas">
                <span class="d-block"></span>
                <span class="d-block"></span>
                <span class="d-block"></span>
            </div>

            <!-- Logo Wrapper -->
            <div class="logo-wrapper">
                <a href="home.html">
                    <img src="img/core-img/logo.png" alt="">
                </a>
            </div>
            <!-- Login Button -->
            <div class="login-btn-wrapper">
                <a href="https://app.loopsexam.xyz" class="btn btn-primary">Login</a>
            </div>
            {{-- <!-- Profile -->
            <div class="logo-wrapper">
                <a href="https://app.loopsexam.xyz" class="profile d-flex align-items-center justify-content-center"
                    target="_blank">
                    <i class="fa-solid fa-user"></i>
                </a>
            </div> --}}


        </div>
    </div>
</div>

<!-- # Sidenav Left -->
<div class="offcanvas offcanvas-start" id="affanOffcanvas" data-bs-scroll="true" tabindex="-1"
    aria-labelledby="affanOffcanvsLabel">
    <button class="btn-close btn-close-white text-reset" type="button" data-bs-dismiss="offcanvas"
        aria-label="Close"></button>

    <div class="offcanvas-body p-0">
        <div class="sidenav-wrapper">
            <!-- Sidenav Profile -->
            <div class="sidenav-profile bg-gradient">
                <div class="sidenav-style1"></div>

                <!-- User Thumbnail -->
                <div>
                    <img class="sidebar-logo" src="img/core-img/logo.png" alt="">
                </div>

                <!-- User Info -->
                <div class="user-info">
                    <span>Exam preparation platform</span>
                </div>
            </div>

            <!-- Sidenav Nav -->
            <ul class="sidenav-nav ps-0">
                <li>
                    <a href={{ route('home') }}><i class="bi bi-house-door"></i> Home</a>
                </li>
            </ul>

            <!-- Social Info -->
            <div class="social-info-wrap">
                <a href="#">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="#">
                    <i class="bi bi-twitter"></i>
                </a>
                <a href="#">
                    <i class="bi bi-linkedin"></i>
                </a>
            </div>

            <!-- Copyright Info -->
            <div class="copyright-info">
                <!-- Copyright info if needed -->
            </div>
        </div>
    </div>
</div>
