@php
   $page['meta_title'] = __('lang.error_404');
@endphp
@extends('layout.wrapperplain')
@section('content')
<!-- main content -->
<div class="container-fluid">
    <!-- page content -->
    <div class="row">
        <div class="col-12">
            <div class="permision-denied text-center p-t-50">
                <img src="{{ url('/') }}/public/images/404.png" style="width:300px; margin-top:80px;margin-bottom:30px;" alt="404 - Not found" /> 
                <div class="x-message"><h4>{{ $error['message'] ?? __('lang.error_not_found') }}</h4></div>
            </div>
        </div>
    </div>
    <!--page content -->
</div>
<!--main content -->
@endsection