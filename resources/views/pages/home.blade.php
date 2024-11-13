@section('title', 'Home')
@extends('layouts.app')
@section('content')

    <!-- Welcome Toast -->


    <!-- Tiny Slider One Wrapper -->
    <div class="tiny-slider-one-wrapper">
        <div class="tiny-slider-one">
          <!-- Single Hero Slide -->
          <div>
            <div class="single-hero-slide bg-overlay" style="background-image: url('img/bg-img/31.jpg')">
              <div class="h-100 d-flex align-items-center text-center">
                <div class="container">
                  <h3 class="text-white mb-1">Build with Bootstrap 5</h3>
                  <p class="text-white mb-4">Build fast, responsive sites with Bootstrap.</p>
                  <a class="btn btn-creative btn-warning" href="#">Buy Now</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Single Hero Slide -->
          <div>
            <div class="single-hero-slide bg-overlay" style="background-image: url('img/bg-img/33.jpg')">
              <div class="h-100 d-flex align-items-center text-center">
                <div class="container">
                  <h3 class="text-white mb-1">Vanilla JavaScript</h3>
                  <p class="text-white mb-4">The whole code is written with vanilla JS.</p>
                  <a class="btn btn-creative btn-warning" href="#">Buy Now</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Single Hero Slide -->
          <div>
            <div class="single-hero-slide bg-overlay" style="background-image: url('img/bg-img/32.jpg')">
              <div class="h-100 d-flex align-items-center text-center">
                <div class="container">
                  <h3 class="text-white mb-1">PWA Ready</h3>
                  <p class="text-white mb-4">Click the "Install Now" button &amp; <br> enjoy it like an app.</p>
                  <a class="btn btn-creative btn-warning" href="#">Buy Now</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Single Hero Slide -->
          <div>
            <div class="single-hero-slide bg-overlay" style="background-image: url('img/bg-img/33.jpg')">
              <div class="h-100 d-flex align-items-center text-center">
                <div class="container">
                  <h3 class="text-white mb-1">Lots of Elements &amp; Pages</h3>
                  <p class="text-white mb-4">Create your website in days, not months.</p>
                  <a class="btn btn-creative btn-warning" href="#">Buy Now</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Single Hero Slide -->
          <div>
            <div class="single-hero-slide bg-overlay" style="background-image: url('img/bg-img/1.jpg')">
              <div class="h-100 d-flex align-items-center text-center">
                <div class="container">
                  <h3 class="text-white mb-1">Dark &amp; RTL Ready</h3>
                  <p class="text-white mb-4">You can use the Dark or <br> RTL mode of your choice.</p>
                  <a class="btn btn-creative btn-warning" href="#">Buy Now</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="pt-3"></div>

      <div class="container direction-rtl">
        <div class="card mb-3">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-4">
                <div class="feature-card mx-auto text-center">
                  <div class="card mx-auto bg-gray">
                    <img src="img/demo-img/pwa.png" alt="">
                  </div>
                  <p class="mb-0">PWA Ready</p>
                </div>
              </div>

              <div class="col-4">
                <div class="feature-card mx-auto text-center">
                  <div class="card mx-auto bg-gray">
                    <img src="img/demo-img/bootstrap.png" alt="">
                  </div>
                  <p class="mb-0">Bootstrap 5</p>
                </div>
              </div>

              <div class="col-4">
                <div class="feature-card mx-auto text-center">
                  <div class="card mx-auto bg-gray">
                    <img src="img/demo-img/js.png" alt="">
                  </div>
                  <p class="mb-0">Vanilla JS</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="container">
        <div class="card card-bg-img bg-img bg-overlay mb-3" style="background-image: url('img/bg-img/3.jpg')">
          <div class="card-body direction-rtl p-4">
            <h2 class="text-white">220+ Reusable Elements</h2>
            <p class="mb-3 text-white">More than 220+ reusable modern design elements. Just copy the code and paste it on
              your desired page.</p>
            <a class="btn btn-warning" href="elements.html">All elements <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>
      </div>

      <div class="container direction-rtl">
        <div class="card mb-3">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-4">
                <div class="feature-card mx-auto text-center">
                  <div class="card mx-auto bg-gray">
                    <img src="img/demo-img/sass.png" alt="">
                  </div>
                  <p class="mb-0">SCSS</p>
                </div>
              </div>

              <div class="col-4">
                <div class="feature-card mx-auto text-center">
                  <div class="card mx-auto bg-gray">
                    <img src="img/demo-img/npm.png" alt="">
                  </div>
                  <p class="mb-0">npm</p>
                </div>
              </div>

              <div class="col-4">
                <div class="feature-card mx-auto text-center">
                  <div class="card mx-auto bg-gray">
                    <img src="img/demo-img/gulp.png" alt="">
                  </div>
                  <p class="mb-0">Gulp 4</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="container">
        <div class="card bg-primary mb-3 bg-img" style="background-image: url('img/core-img/1.png')">
          <div class="card-body direction-rtl p-4">
            <h2 class="text-white">35+ Ready Pages</h2>
            <p class="mb-3 text-white">Already designed more than 35+ pages for your needs. Such as - Authentication,
              Chats, eCommerce, Blog &amp; Miscellaneous pages.</p>
            <a class="btn btn-warning" href="pages.html">All Pages <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>
      </div>

      <div class="container direction-rtl">
        <div class="card mb-3">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-4">
                <div class="feature-card mx-auto text-center">
                  <div class="card mx-auto bg-gray">
                    <img src="img/demo-img/dark.png" alt="">
                  </div>
                  <p class="mb-0">Dark Mode</p>
                </div>
              </div>

              <div class="col-4">
                <div class="feature-card mx-auto text-center">
                  <div class="card mx-auto bg-gray">
                    <img src="img/demo-img/rtl.png" alt="">
                  </div>
                  <p class="mb-0">RTL Ready</p>
                </div>
              </div>

              <div class="col-4">
                <div class="feature-card mx-auto text-center">
                  <div class="card mx-auto bg-gray">
                    <img src="img/demo-img/code.png" alt="">
                  </div>
                  <p class="mb-0">Easy Code</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="container">
        <div class="card mb-3">
          <div class="card-body">
            <h3>Customer Review</h3>

            <div class="testimonial-slide-three-wrapper">
              <div class="testimonial-slide3 testimonial-style3">

                <!-- Single Testimonial Slide -->
                <div class="single-testimonial-slide">
                  <div class="text-content">
                    <span class="d-inline-block badge bg-warning mb-2">
                      <i class="bi bi-star-fill me-1"></i>
                      <i class="bi bi-star-fill me-1"></i>
                      <i class="bi bi-star-fill me-1"></i>
                      <i class="bi bi-star-fill me-1"></i>
                      <i class="bi bi-star-fill"></i>
                    </span>
                    <h6 class="mb-2">The code looks clean, and the designs are excellent. I recommend.</h6>
                    <span class="d-block">Mrrickez, Themeforest</span>
                  </div>
                </div>

                <!-- Single Testimonial Slide -->
                <div class="single-testimonial-slide">
                  <div class="text-content">
                    <span class="d-inline-block badge bg-warning mb-2">
                      <i class="bi bi-star-fill me-1"></i>
                      <i class="bi bi-star-fill me-1"></i>
                      <i class="bi bi-star-fill me-1"></i>
                      <i class="bi bi-star-fill me-1"></i>
                      <i class="bi bi-star-fill"></i>
                    </span>
                    <h6 class="mb-2">All complete, <br> great craft.</h6>
                    <span class="d-block">Mazatlumm, Themeforest</span>
                  </div>
                </div>

                <!-- Single Testimonial Slide -->
                <div class="single-testimonial-slide">
                  <div class="text-content">
                    <span class="d-inline-block badge bg-warning mb-2">
                      <i class="bi bi-star-fill me-1"></i>
                      <i class="bi bi-star-fill me-1"></i>
                      <i class="bi bi-star-fill me-1"></i>
                      <i class="bi bi-star-fill me-1"></i>
                      <i class="bi bi-star-fill"></i>
                    </span>
                    <h6 class="mb-2">Awesome template! <br> Excellent support!</h6>
                    <span class="d-block">Vguntars, Themeforest</span>
                  </div>
                </div>

                <!-- Single Testimonial Slide -->
                <div class="single-testimonial-slide">
                  <div class="text-content">
                    <span class="d-inline-block badge bg-warning mb-2">
                      <i class="bi bi-star-fill me-1"></i>
                      <i class="bi bi-star-fill me-1"></i>
                      <i class="bi bi-star-fill me-1"></i>
                      <i class="bi bi-star-fill me-1"></i>
                      <i class="bi bi-star-fill"></i>
                    </span>
                    <h6 class="mb-2">Nice modern design, <br> I love the product.</h6>
                    <span class="d-block">electroMEZ, Themeforest</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="container direction-rtl">
        <div class="card">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-4">
                <div class="feature-card mx-auto text-center">
                  <div class="card mx-auto bg-gray">
                    <img src="img/demo-img/star.png" alt="">
                  </div>
                  <p class="mb-0">Best Rated</p>
                </div>
              </div>

              <div class="col-4">
                <div class="feature-card mx-auto text-center">
                  <div class="card mx-auto bg-gray">
                    <img src="img/demo-img/elegant.png" alt="">
                  </div>
                  <p class="mb-0">Elegant</p>
                </div>
              </div>

              <div class="col-4">
                <div class="feature-card mx-auto text-center">
                  <div class="card mx-auto bg-gray">
                    <img src="img/demo-img/lightning.png" alt="">
                  </div>
                  <p class="mb-0">Trendsetter</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="pb-3"></div>
@endsection
