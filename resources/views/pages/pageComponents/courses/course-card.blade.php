<div class="col-6 col-lg-3">
    <div class="card single-product-card">
        <div class="card-body p-2">
            <!-- Product Thumbnail -->
            <a class="product-thumbnail d-block" href="{{ $button_link }}">
                <img src="{{ $image }}" alt="">
            </a>
            <!-- Product Title -->
            <a class="product-title d-block text-truncate" href="shop-details.html">{{ $title }}</a>
            <!-- Product Price -->
            <p class="sale-price">{{ $price }}<span>{{ $discount }}</span></p>
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-primary rounded-pill btn-sm details-btn" data-bs-toggle="modal"
                    data-bs-target="#productDetailsModal" data-title="{{ $title }}"
                    data-image="{{ $image }}" data-price="{{ $price }}"
                    data-discount="{{ $discount }}"
                    data-details="@php
                        $details = preg_replace(
                            '/(https?:\/\/[^\s]+)/',
                            '<a href=\'$1\' target=\'_blank\' class=\'btn btn-outline-primary btn-sm mt-2\'>Click here</a>',
                            $details
                        );
                        echo $details; @endphp">
                    Details
                </button>
            </div>
        </div>
    </div>
</div>
