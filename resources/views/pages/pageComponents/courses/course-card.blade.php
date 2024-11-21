<div class="col-6 col-lg-3">
    <div class="card single-product-card">
        <div class="card-body p-3">
            <!-- Product Thumbnail -->
            <a class="product-thumbnail d-block" href={{$button_link}}>
                <img src="{{$image}}" alt="">
            </a>
            <!-- Product Title -->
            <a class="product-title d-block text-truncate" href="shop-details.html">{{$title}}</a>
            <!-- Product Price -->
            <p class="sale-price">{{$price}}<span>{{$discount}}</span></p>
            <div class="d-flex justify-content-between align-items-center">
                <a class="btn btn-primary rounded-pill btn-sm" href={{$button_link}}>JOIN</a>
                <button
                    class="btn btn-secondary rounded-pill btn-sm details-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#productDetailsModal"
                    data-title="{{$title}}"
                    data-image="{{$image}}"
                    data-price="{{$price}}"
                    data-discount="{{$discount}}"
                    data-details="{{$details}}">
                    Details
                </button>
            </div>

        </div>
    </div>
</div>


