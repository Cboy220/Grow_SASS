@extends('landlord.layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- bread crumbs -->
        @include('landlord.misc.crumbs')
        <!-- bread crumbs -->

        <!-- action buttons -->
        @include('landlord.packages.actions.page-actions')
        <!-- action buttons -->

    </div>
    <!--page heading-->


    <!-- page content -->
    <div class="row">
        <div class="col-12">
        
            @if(@count($packages) > 0)
              <div class="row" id="packages-container">
                  @foreach($packages as $package)
                  @include('landlord.packages.packages')
                  @endforeach
              </div>
            @else
            <!--nothing found-->
            @include('notifications.no-results-found')
            @endif
        </div>
    </div>
    <!--page content -->

</div>
<!--main content -->
@endsection