<!--CUSTOMER HAS A SUBSCRIPTION-->
@if(config('visibility.has_subscription'))
<div class="subscription-details p-t-10">

    <div class="x-heading">
        {{ $subscription->package_name ?? '---' }}
    </div>

    <!--SUMMARY-->
    @include('landlord.subscriptions.misc.info-summary')


    @if($subscription->subscription_type == 'paid')
    <div class="line  m-t-30 p-b-40"></div>

    <h5 class="p-b-10">@lang('lang.payment_gateway_information')</h5>

    @include('landlord.subscriptions.misc.info-gateways')

    @endif


</div>
@endif


<!--CUSTOMER DOES NPT HAVE A SUBSCRIPTION-->
@if(!config('visibility.has_subscription'))
<div class="subscription-details text-center p-t-40">
    <img class="x-image" src="{{ url('/') }}/public/images/404.png" alt="404 - Not found" />
    <div class="x-message p-t-30">
        <h3>{{ $error['message'] ?? __('lang.no_subscription_exists_for_customer') }}</h3>
    </div>
</div>
@endif