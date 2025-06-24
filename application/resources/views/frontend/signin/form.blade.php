<div class="signup-form">

    <div class="x-heading m-t-30">
        <h4>{{ $section->frontend_data_1 }}</h4>
    </div>
    <div class="x-sub-heading">
        {{ $section->frontend_data_2 }}
    </div>
    <form class="form-horizontal form-material x-form p-t-30" id="loginForm" novalidate="novalidate" _lpchecked="1">
        <div class="input-group m-t-0 m-b-50">
            <input type="text" class="form-control" placeholder="{{ $section->frontend_data_4 }}" name="domain_name" aria-describedby="basic-addon2">
            <span class="input-group-addon x-sub-domain" id="basic-addon2">.{{ config('system.settings_base_domain') }}</span>
        </div>
        <div class="form-group text-center m-t-10 p-b-10">
            <div class="col-xs-12">
                <button class="btn btn-info btn-lg btn-block position-relative ajax-request" id="loginSubmitButton"
                    data-button-loading-annimation="yes"
                    data-url="{{ url('account/login') }}" 
                    data-type="form" 
                    data-form-id="loginForm" 
                    data-ajax-type="post"
                    data-button-loading-annimation="yes"
                    data-progress-bar="hidden"
                    type="submit">{{ $section->frontend_data_3 }}</button>
            </div>
        </div>
    </form>

</div>