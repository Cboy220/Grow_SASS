@extends('landlord.layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- bread crumbs -->
        @include('landlord.misc.crumbs')
        <!-- bread crumbs -->

    </div>
    <!--page heading-->


    <!-- Row -->
    <div class="row">
        <!-- Column -->
        <div class="col-12">
            <div class="card">
                <div class="card-body dynamic-content-container">
                    <div id="dynamic-content-container">
                        <!--dynamic events-->
                    </div>

                    <!--load more button-->
                    @include('landlord.misc.load-more-dynamic-button')
                </div>
            </div>
        </div>
        <!-- Column -->
    </div>
    <!-- Row -->

</div>
<!--main content -->

<!--dynamically load timeline events-->
<script src="public/js/landlord/dynamic/timeline.js?v={{ config('system.versioning') }}" id="dynamic-load-timeline-events"
    data-loading-target="dynamic-content-container" data-progress-bar="hidden" data-url="{{ url('app-admin/events?source=events') }}">
</script>
@endsection