@section('title', 'Home')
@extends('layouts.app')
@section('content')

    <!-- Welcome Toast -->


    <!-- Tiny Slider One Wrapper -->
    @include('pages.pageComponents.slider')

      <div class="pt-3"></div>

   @include('pages.pageComponents.cards')

     @include('pages.pageComponents.fullcard')

     {{-- @include('pages.pageComponents.cards') --}}

     @include('pages.pageComponents.fullcard-blue')



     {{-- @include('pages.pageComponents.cards') --}}
     {{-- @include('pages.pageComponents.review') --}}



      <div class="pb-3"></div>
@endsection
