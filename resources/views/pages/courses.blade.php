@section('title', 'Home')
@extends('layouts.app')
@section('content')


     @include('pages.pageComponents.courses.course-header')

     @include('pages.pageComponents.courses.course-tabs')

      <div class="pb-3"></div>

@include('pages.pageComponents.courses.course-modal')
@endsection
@section('scripts')
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const modalTitle = document.getElementById("modalProductTitle");
    const modalImage = document.getElementById("modalProductImage");
    const modalPrice = document.getElementById("modalProductPrice");
    const modalDiscount = document.getElementById("modalProductDiscount");
    const modalDetails = document.getElementById("modalProductDetails");

    // Use event delegation to attach event listeners to dynamically added buttons
    document.body.addEventListener("click", function (e) {
        if (e.target && e.target.classList.contains("details-btn")) {
            const button = e.target;

            // Retrieve data from the clicked button
            const title = button.getAttribute("data-title");
            const image = button.getAttribute("data-image");
            const price = button.getAttribute("data-price");
            const discount = button.getAttribute("data-discount");
            const details = button.getAttribute("data-details");

            // Update modal content
            modalTitle.textContent = title;
            modalImage.src = image;
            modalPrice.textContent = `Price: ${price}`;
            modalDiscount.textContent = `Discount: ${discount}`;
            modalDetails.textContent = details;
        }
    });
});

</script>
@endsection
