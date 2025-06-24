@extends('pages.client.wrapper')

@section('tab-content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Client Expectations</h5>
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#expectationModal">
            <i class="fa fa-plus"></i> Add Goal
        </button>
    </div>
    <div class="card-body p-0">
        @include('pages.client.expectations.table')
    </div>
</div>
@include('pages.client.expectations.modal')
@endsection

@push('scripts')
    @include('pages.client.expectations.js')
@endpush 