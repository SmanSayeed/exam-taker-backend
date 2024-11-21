<div class="page-content-wrapper py-3">
    @php
        $buttonLink = 'https://www.facebook.com/looopsacademy?mibextid=ZbWKwL';
    @endphp

    <!-- Top Products -->
    <div class="top-products-area">
        <div class="container">
            <div class="row g-3">

                @include('pages.pageComponents.courses.course-card', [
                    'title' => 'Medical course 1',
                    'image' => 'img/bg-img/p1.jpg',
                    'price' => '250 tk',
                    'discount' => '300 tk',
                    'button_link' => $buttonLink,
                    'details' => 'Medical course 1 details. Medical course 1 detailsMedical course 1 detailsMedical course 1 detailsMedical course 1 detailsMedical course 1 detailsMedical course 1 detailsMedical course 1 detailsMedical course 1 detailsMedical course 1 detailsMedical course 1 detailsMedical course 1 detailsMedical course 1 details',
                ])

                @include('pages.pageComponents.courses.course-card', [
                    'title' => 'Medical course 2',
                    'image' => 'img/bg-img/p1.jpg',
                    'price' => '150 tk',
                    'discount' => '200 tk',
                    'button_link' => $buttonLink,
                    'details' => 'Medical course 2 details',
                ])

                <!-- Sample -->
                {{-- <div class="col-6 col-sm-4 col-lg-3">
            <div class="card single-product-card">
              <div class="card-body p-3">
                <!-- Product Thumbnail -->
                <a class="product-thumbnail d-block" href="shop-details.html">
                  <img src="img/bg-img/p2.jpg" alt="">
                  <!-- Badge -->
                  <span class="badge bg-primary">-10%</span>
                </a>
                <!-- Product Title -->
                <a class="product-title d-block text-truncate" href="shop-details.html">Atoms
                  Musk</a>
                <!-- Product Price -->
                <p class="sale-price">$3.36<span>$5.99</span></p>
                <a class="btn btn-primary rounded-pill btn-sm" href="#">Add to Cart</a>
              </div>
            </div>
          </div> --}}



            </div>
        </div>
    </div>

</div>
