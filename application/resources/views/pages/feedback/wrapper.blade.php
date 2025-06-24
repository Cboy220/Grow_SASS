@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- Page Title & Bread Crumbs -->
        @include('misc.heading-crumbs')
        <!--Page Title & Bread Crumbs -->


        <!-- action buttons -->
        @include('pages.feedback.components.misc.list-page-actions')
        <!-- action buttons -->

    </div>
    <!--page heading-->

    <!-- page content -->
    <div class="row">
        <div class="col-sm-12 col-lg-9 m-auto" id="feedback-lists-part-wrapper">
            <!--feedback list part-->
            @include('pages.feedback.components.misc.feedback-list-page')
            <!--feedback list part-->
        </div>
    </div>
    <!--page content -->
</div>
{{-- script --}}
@include('pages.feedback.js')
<!--main content -->
@endsection