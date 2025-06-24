@extends('account.wrapper')
@section('account-page')
<div class="account-wrapper">

    <!--PAYPAL JS (note: this needs to preload here, instead of paypla.blade.php)-->
    @if($landlord_settings->settings_paypal_status == 'enabled')
    @if($landlord_settings->settings_paypal_mode == 'live')
    <script
        src="https://www.paypal.com/sdk/js?client-id={{ $landlord_settings->settings_paypal_live_client_id ?? '' }}&vault=true&intent=subscription">
    </script>
    @else
    <script
        src="https://www.paypal.com/sdk/js?client-id={{ $landlord_settings->settings_paypal_sandbox_client_id ?? '' }}&vault=true&intent=subscription">
    </script>
    @endif
    @endif

    <!--RAZORPAY JS (note: this needs to preload here, instead of razorpay.blade.php)-->
    @if($landlord_settings->settings_razorpay_status == 'enabled')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    @endif

    <!--GENERAL CHECKOUT JS-->
    <script src="public/js/landlord/frontend/checkout.js?v={{ config('system.versioning') }}"></script>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <h4>@lang('lang.make_a_payment')</h4>
                    <div class="line m-t-10"></div>
                    <table class="table no-border">
                        <tbody>
                            <!--plan name-->
                            <tr>
                                <td>@lang('lang.plan')</td>
                                <td class="font-medium w-30">
                                    {{ $package->package_name }}</td>
                            </tr>

                            <!--status-->
                            <tr>
                                <td>@lang('lang.status')</td>
                                <td class="font-medium"><span
                                        class="label {{ runtimeSubscriptionStatusColors($subscription->subscription_status) }}">{{ runtimeSubscriptionStatusLang($subscription->subscription_status) }}</span>
                                </td>
                            </tr>

                            <!--billing cycle-->
                            <tr>
                                <td>@lang('lang.billing_cycle')</td>
                                <td class="font-medium w-30 text-ucw">
                                    {{ $subscription->subscription_gateway_billing_cycle }}</td>
                            </tr>

                            <!--amount-->
                            <tr>
                                <td>@lang('lang.amount')</td>
                                <td class="font-medium w-30">
                                    {{ runtimeMoneyFormatSaaS($subscription->subscription_amount) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!--ONLINE PAYMENTS - INFO-->
                    @if($subscription->subscription_payment_method == 'automatic')
                    <div class="alert alert-info">@lang('lang.payment_required_to_activate_account')</div>
                    @endif

                    <!--MANUAL PAYMENTS - INFO-->
                    @if($subscription->subscription_payment_method == 'offline')
                    <div id="online-payment-form">
                        <div class="alert alert-info">
                            {!! _clean($landlord_settings->settings_offline_payments_details) !!}
                        </div>
                    </div>
                    @endif

                    <div class="line"></div>




                    <!--ONLINE PAYMENTS FORMS-->
                    @if($subscription->subscription_payment_method == 'automatic')
                    <div id="online-payment-form">
                        <!--select a payment method-->
                        <div class="div row justify-content-end" id="payment_now_selector_container" data-url=""
                            data-base-url="{{ url('settings/account/'.$subscription->subscription_uniqueid.'/pay/') }}"
                            data-type="form" data-form-id="payment_now_selector_container" data-ajax-type="post"
                            data-loading-target="none">
                            <div class="col-sm-12 col-lg-4">
                                <div class="div-checkout-buttons-selector">
                                    <!--item-->
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <select class="select2-basic form-control" id="payment_now_method_selector"
                                                name="payment_now_method_selector"
                                                data-placeholder="@lang('lang.select_payment_method')">
                                                <option></option>
                                                <!--stripe-->
                                                @if($landlord_settings->settings_stripe_status == 'enabled')
                                                <option value="stripe">
                                                    {{ $landlord_settings->settings_stripe_display_name }}
                                                </option>
                                                @endif
                                                <!--stripe-->
                                                @if($landlord_settings->settings_paypal_status == 'enabled')
                                                <option value="paypal">
                                                    {{ $landlord_settings->settings_paypal_display_name }}
                                                </option>
                                                @endif
                                                <!--paystack-->
                                                @if($landlord_settings->settings_paystack_status == 'enabled')
                                                <option value="paystack">
                                                    {{ $landlord_settings->settings_paystack_display_name }}
                                                </option>
                                                @endif
                                                <!--razorpay-->
                                                @if($landlord_settings->settings_razorpay_status == 'enabled')
                                                <option value="razorpay">
                                                    {{ $landlord_settings->settings_razorpay_display_name }}
                                                </option>
                                                @endif
                                                <!--offline payments (also show it here)-->
                                                @if($landlord_settings->settings_offline_payments_status == 'enabled')
                                                <option value="offline">
                                                    {{ $landlord_settings->settings_offline_payments_display_name }}
                                                </option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--dynamic payment buttons-->
                        <div>
                            <div class="div row justify-content-end">
                                <div class="col-sm-12 col-lg-4">
                                    <div class="div-checkout-buttons-container text-right"
                                        id="payment_now_buttons_container">
                                        <!--place holder button-->
                                        <button type="button" class="btn waves-effect waves-light btn-block btn-default"
                                            id="payment_now_placeholder_button" disabled>@lang('lang.pay_now')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

</div>
@endsection