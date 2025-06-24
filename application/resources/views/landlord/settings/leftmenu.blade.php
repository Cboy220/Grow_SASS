<ul class="inner-menu p-b-70">

    <!--general settings-->
    <li>
        <a class="inner-menu-item {{ $page['inner_menu_general'] ?? '' }}"
            href="{{ url('app-admin/settings/general') }}">@lang('lang.general_settings')</a>
    </li>

    <!--domain settings-->
    <li>
        <a class="inner-menu-item {{ $page['inner_menu_domain'] ?? '' }}"
            href="{{ url('app-admin/settings/domain') }}">@lang('lang.domain_settings')</a>
    </li>

    <!--account_settings-->
    <li>
        <a class="inner-menu-item {{ $page['inner_menu_defaults'] ?? '' }}"
            href="{{ url('app-admin/settings/defaults') }}">@lang('lang.account_settings')</a>
    </li>

    <!--company details-->
    <li>
        <a class="inner-menu-item {{ $page['inner_menu_company'] ?? '' }}"
            href="{{ url('app-admin/settings/company') }}">@lang('lang.company_details')</a>
    </li>

    <!--email-->
    <li class="group-menu-wrapper {{ $page['inner_group_menu_email'] ?? '' }}">
        <a class="inner-menu-item {{ $page['inner_group_menu_email'] ?? '' }}" href="javascript:void(0);"
            aria-expanded="false">@lang('lang.email')</a>
        <ul aria-expanded="false" class="hidden">
            <!--email_templates-->
            <li>
                <a class="{{ $page['inner_menu_emailtemplates'] ?? '' }}"
                    href="{{ url('app-admin/settings/emailtemplates') }}">@lang('lang.email_templates')</a>
            </li>

            <!--email_settings-->
            <li>
                <a class="{{ $page['inner_menu_email'] ?? '' }}"
                    href="{{ url('app-admin/settings/email') }}">@lang('lang.email_settings')</a>
            </li>


            <!--email_log-->
            <li>
                <a class="{{ $page['inner_menu_email_log'] ?? '' }}"
                    href="{{ url('app-admin/settings/emaillog') }}">@lang('lang.email_log')</a>
            </li>

            <!--smtp settings-->
            <li>
                <a class="{{ $page['inner_menu_smtp'] ?? '' }}"
                    href="{{ url('app-admin/settings/smtp') }}">@lang('lang.smtp_settings')</a>
            </li>
        </ul>
    </li>


    <!--currency settings-->
    <li>
        <a class="inner-menu-item {{ $page['inner_menu_currency'] ?? '' }}"
            href="{{ url('app-admin/settings/currency') }}">@lang('lang.currency')</a>
    </li>


    <!--logo settings-->
    <li>
        <a class="inner-menu-item {{ $page['inner_menu_logo'] ?? '' }}"
            href="{{ url('app-admin/settings/logo') }}">@lang('lang.logo')</a>
    </li>


    <!--cronjob-->
    <li>
        <a class="inner-menu-item {{ $page['inner_menu_cronjob'] ?? '' }}"
            href="{{ url('app-admin/settings/cronjob') }}">@lang('lang.cronjob')</a>
    </li>

    <!--free trial-->
    <li>
        <a class="inner-menu-item {{ $page['inner_menu_free_trial'] ?? '' }}"
            href="{{ url('app-admin/settings/freetrial') }}">@lang('lang.free_trial')</a>
    </li>



    <!--payment_gateways-->
    <li class="group-menu-wrapper {{ $page['inner_group_menu_billing'] ?? '' }}">
        <a class="inner-menu-item {{ $page['inner_group_menu_billing'] ?? '' }}" href="javascript:void(0);"
            aria-expanded="false">@lang('lang.payment_gateways')</a>
        <ul aria-expanded="false" class="hidden">
            <!--general-->
            <li>
                <a class="{{ $page['inner_menu_gateways'] ?? '' }}"
                    href="{{ url('app-admin/settings/gateways') }}">@lang('lang.general_settings')</a>
            </li>
            <!--stripe-->
            <li>
                <a class="{{ $page['inner_menu_stripe'] ?? '' }}"
                    href="{{ url('app-admin/settings/stripe') }}">Stripe</a>
            </li>
            <!--paypal-->
            <li>
                <a class="{{ $page['inner_menu_paypal'] ?? '' }}"
                    href="{{ url('app-admin/settings/paypal') }}">Paypal</a>
            </li>
            <!--paystack-->
            <li>
                <a class="{{ $page['inner_menu_paystack'] ?? '' }}"
                    href="{{ url('app-admin/settings/paystack') }}">Paystack</a>
            </li>
            <!--razorpay-->
            <li>
                <a class="{{ $page['inner_menu_razorpay'] ?? '' }}"
                    href="{{ url('app-admin/settings/razorpay') }}">Razorpay</a>
            </li>
            <!--offline-->
            <li>
                <a class="{{ $page['inner_menu_offline_payment'] ?? '' }}"
                    href="{{ url('app-admin/settings/offlinepayments') }}">@lang('lang.offline_payments')</a>
            </li>
        </ul>
    </li>

    <!--system-->
    <li>
        <a class="inner-menu-item {{ $page['inner_menu_system'] ?? '' }}"
            href="{{ url('app-admin/settings/system') }}">@lang('lang.system')</a>
    </li>


    <!--reCPATCH-->
    <li>
        <a class="inner-menu-item {{ $page['inner_menu_captcha'] ?? '' }}"
            href="{{ url('app-admin/settings/captcha') }}">reCAPTCHA</a>
    </li>


    <!--updates-->
    <li>
        <a class="inner-menu-item {{ $page['inner_menu_updates'] ?? '' }}"
            href="{{ url('app-admin/settings/updates') }}">@lang('lang.updates')</a>
    </li>

    <!--debugging-->
    <li class="group-menu-wrapper {{ $page['inner_group_menu_debugging'] ?? '' }}">
        <a class="inner-menu-item {{ $page['inner_group_menu_debugging'] ?? '' }}" href="javascript:void(0);"
            aria-expanded="false">@lang('lang.debugging')</a>
        <ul aria-expanded="false" class="hidden">
            <!--updates_log-->
            <li>
                <a class="{{ $page['inner_menu_updating_log'] ?? '' }}"
                    href="{{ url('app-admin/settings/updateslog') }}">@lang('lang.updates_log')</a>
            </li>
            <!--eror log-->
            <li>
                <a class="{{ $page['inner_menu_error_logs'] ?? '' }}"
                    href="{{ url('app-admin/settings/errorlogs') }}">@lang('lang.error_logs')</a>
            </li>
        </ul>
    </li>


</ul>