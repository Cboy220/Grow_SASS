@extends('landlord.layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- bread crumbs -->
        @include('landlord.misc.crumbs')
        <!-- bread crumbs -->

        <!-- action buttons -->
        @include('landlord.home.components.page-actions')
        <!-- action buttons -->

    </div>
    <!--page heading-->

</div>
<!--main content -->
@endsection