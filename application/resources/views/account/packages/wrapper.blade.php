@extends('account.wrapper')
@section('account-page')
<div class="account-wrapper">

    
    <!--currently unsubscribed-->
    @if(config('system.settings_saas_status') == 'unsubscribed' || config('system.settings_saas_package_id') == '')
    <div class="row">
        <div class="col-12">
            <div class="alert alert-danger">
                @lang('lang.not_currently_subscribed')
            </div>
        </div>
    </div>
    @endif
    
    <!-- page content -->
    <div class="row">
        <div class="col-12">

            @if(@count($packages) > 0)
            <div class="row" id="packages-container">
                @foreach($packages as $package)
                <!--use same table as landlord-->
                @if($package->package_status == 'active' || ($package->package_status == 'archived' &&
                config('system.settings_saas_package_id') == $package->package_id))
                @include('landlord.packages.packages')
                @endif
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
@endsection