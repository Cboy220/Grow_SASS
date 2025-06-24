@extends('account.wrapper')
@section('account-page')
<div class="account-wrapper">

    <!-- thank you -->
    @if($payload['status'] == 'success')
    <div class="row">
        <div class="col-12">
            <div class="permision-denied">
                <img src="{{ url('/') }}/public/images/thank-you-payment.png" alt="@lang('lang.thank_you')" />
                <div class="x-message">
                    <h1>@lang('lang.thank_you')</h1>
                </div>
                <div class="x-sub-message p-t-10">
                    <h4>@lang('lang.your_payment_is_now_processing')</h4>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="permision-denied">
                <img src="{{ url('/') }}/public/images/error.png" class="w-px-200" alt="@lang('lang.thank_you')" />
                <div class="x-message p-t-10">
                    <h2>@lang('lang.payment_error')</h2>
                </div>
                <div class="x-sub-message p-t-10">
                    <h4>@lang('lang.error_processing_payment')</h4>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!--page content -->
</div>
@endsection