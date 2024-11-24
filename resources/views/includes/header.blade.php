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
                 <a href={{ route('home') }}>
                     <img src="img/core-img/logo.png" alt="">
                 </a>
             </div>


             <!-- Profile -->
             <div class="logo-wrapper ">
                 <a href="https://app.loopsexam.xyz/exams-starting"
                     class="profile d-flex align-items-center justify-content-center" target="_self">
                     <i class="fa-solid fa-user"></i>
                 </a>
             </div>
         </div>
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
                     {{-- <h6 class="user-name mb-0">Loopsacademy</h6> --}}
                     <span>Exam preparation platform</span>
                 </div>
             </div>

             <!-- Sidenav Nav -->
             <ul class="sidenav-nav ps-0">
                 <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
                     <a href="{{ route('home') }}">
                         <i class="bi bi-house"></i>
                         <span>Home</span>
                     </a>
                 </li>

                 <li class="{{ request()->is('question-bank*') ? 'active' : '' }}">
                     <a href="https://app.loopsexam.xyz/questions">
                         <i class="fa-solid fa-list-check"></i>
                         <span>Question Bank</span>
                     </a>
                 </li>

                 <li class="{{ request()->is('practice*') ? 'active' : '' }}">
                     <a href="https://app.loopsexam.xyz/exams-starting">
                         <i class="fa-regular fa-pen-to-square"></i>
                         <span>Practice</span>
                     </a>
                 </li>

                 <li class="{{ request()->is('courses*') ? 'active' : '' }}">
                     <a href={{ route('courses') }}>
                         <i class="fa-solid fa-person-chalkboard"></i>
                         <span>Course</span>
                     </a>
                 </li>
             </ul>


             <!-- Social Info -->
             <div class="social-info-wrap">
                 <a href="https://www.facebook.com/looopsacademy?mibextid=ZbWKwL">
                     <i class="bi bi-facebook"></i>
                 </a>
                 <!--<a href="https://www.facebook.com/looopsacademy?mibextid=ZbWKwL">-->
                 <!--  <i class="fa-brands fa-facebook-messenger"></i>-->
                 <!--</a>-->
                 <!--<a href="01881628483">-->
                 <!--  <i class="fa-brands fa-whatsapp"></i>-->
                 <!--</a>-->
                 <a href="https://t.me/loopsacademymedical">
                     <i class="fa-brands fa-telegram"></i>
                 </a>
             </div>

             <!-- Copyright Info -->
             <div class="copyright-info">
                 <p>
                     <span id="copyrightYear"></span>
                     &copy; All rights reserved by <a href="#"> LA</a>
                 </p>
             </div>
         </div>
     </div>
 </div>
