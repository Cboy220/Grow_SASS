@extends('layout.wrapper') @section('content')
    <!-- main content -->
    <div class="container-fluid">

        <!--page heading-->
        <div class="row page-titles">

            <!-- Page Title & Bread Crumbs -->
            @include('misc.heading-crumbs')
            <!--Page Title & Bread Crumbs -->


            <!-- action buttons -->
            @include('pages.expectation.components.misc.list-page-actions')
            <!-- action buttons -->

        </div>
        <!--page heading-->

        <!-- page content -->
        <div class="row">
            <div class="col-sm-12 col-lg-9 m-auto" id="expectation-lists-part-wrapper">
                <div class="container py-4">
                    <h5><i class="fas fa-bullseye mr-2"></i>{{ __('lang.my_expectations') }}</h5>

                    <!-- Search box -->
                    <div class="input-group mb-3">
                        <input type="text" id="searchInput" class="form-control" placeholder="{{ __('lang.search_expectations_placeholder') }}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Stats and progress bar -->
                    <div id="expectationStatsContainer"></div>

                    <!-- Expectation list -->
                    <div id="expectationListContainer"></div>
                </div>
            </div>
        </div>
        <!--page content -->
    </div>
    {{-- script --}}
    @include('pages.expectation.js')
    <!--main content -->
@endsection
