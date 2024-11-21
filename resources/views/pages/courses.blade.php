@section('title', 'Home')
@extends('layouts.app')
@section('content')


     @include('pages.pageComponents.courses.course-header')
     @include('pages.pageComponents.courses.courses-cards')

      <div class="pb-3"></div>
@endsection
