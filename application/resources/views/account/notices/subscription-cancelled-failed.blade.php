@extends('account.wrapper')
@section('account-page')
<div class="account-wrapper">

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="page-notification">
                        <img class="m-b-30" src="{{ url('/') }}/public/images/relax.png"
                            alt="@lang('lang.no_notices_for_account')" />
                        <h2 class="m-b-10 font-weight-200"> @lang('lang.account_was_cancelled_notice_1') </h2>
                        <h4 class="m-b-30 font-weight-200"> @lang('lang.contact_customer_support') </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection