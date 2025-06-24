@extends('landlord.layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid saas-home p-l-30 p-r-30">

    <!-- top panel stats -->
    @include('landlord.home.components.panel-top-stats')

    <!-- income chart -->
    @include('landlord.home.components.panel-income-chart')


        <!-- events timeline -->
        @include('landlord.home.components.panel-events')


</div>
<!--main content -->
@endsection