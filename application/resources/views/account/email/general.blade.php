@extends('account.wrapper')
@section('account-page')
<div class="account-wrapper">


    <!--warning messsage-->
    @if(request()->session()->has('smtp-required-warning'))
    <div class="alert alert-danger m-b-30">
        <h5 class="text-danger"><i class="sl-icon-info"></i> @lang('lang.info')</h5>@lang('lang.smtp_only_message')
    </div>
    @endif

    @if($landlord_defaults->defaults_email_delivery == 'smtp_and_sendmail')
    <div class="modal-selector m-l--25 m-r--25">
        <h4>@lang('lang.mail_delivery_server')</h4>
        <div class="p-t-10">
            <div class="form-group form-group-checkbox row pull-left m-r-20 m-b-0">
                <div class="col-12 p-t-5">
                    <input type="checkbox" id="email_option_sendmail" name="email_option_sendmail"
                        data-target="email_settings_local" class="filled-in chk-col-light-blue saas_email_option"
                        {{ runtimePreChecked2($settings->settings_email_server_type, 'sendmail') }}>
                    <label class="p-l-30" for="email_option_sendmail">@lang('lang.use_our_server')</label>
                </div>
            </div>
            <div class="form-group form-group-checkbox row pull-left m-b-0">
                <div class="col-12 p-t-5">
                    <input type="checkbox" id="email_option_smtp" name="email_option_smtp"
                        data-target="email_settings_smtp" class="filled-in chk-col-light-blue saas_email_option"
                        {{ runtimePreChecked2($settings->settings_email_server_type, 'smtp') }}>
                    <label class="p-l-30" for="email_option_smtp">@lang('lang.use_smtp_server')</label>
                </div>
            </div>
            <div class="clear-both"></div>
        </div>
    </div>

    <!--LOCAL EMAIL-->
    <div class="email_settings {{ runtimeVisibilitySection($settings->settings_email_server_type, 'sendmail') }}"
        id="email_settings_local">



        <div class="form-group row">
            <label class="col-sm-12 text-left control-label col-form-label">@lang('lang.email_address') <span
                    class="align-middle text-info font-16" data-toggle="tooltip"
                    title="@lang('lang.use_smtp_server_info')" data-placement="top"><i
                        class="ti-info-alt"></i></span></label>
            <div class="col-sm-12">
                <input type="text" class="form-control form-control-sm saas_email_selector"
                    id="settings_saas_email_local_address" name="settings_saas_email_local_address" disabled
                    value="{{ $settings->settings_saas_email_local_address ?? '' }}">
            </div>
        </div>
        <!--from name-->
        <div class="form-group row">
            <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.from_name')) }}</label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="settings_email_from_name"
                    name="settings_email_from_name" value="{{ $settings->settings_email_from_name ?? '' }}">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-12 text-left control-label col-form-label">@lang('lang.forward_replies_to') <span
                    class="align-middle text-info font-16" data-toggle="tooltip"
                    title="@lang('lang.forward_replies_info')" data-placement="top"><i
                        class="ti-info-alt"></i></span></label>
            <div class="col-sm-12">
                <input type="text" class="form-control form-control-sm saas_email_selector"
                    id="settings_saas_email_forwarding_address" name="settings_saas_email_forwarding_address"
                    value="{{ $settings->settings_saas_email_forwarding_address ?? '' }}">
            </div>
        </div>


        <div class="alert alert-danger m-t-30">
            @lang('lang.use_our_server_info').
        </div>

        <div class="text-right p-t-30">
            <button type="submit" class="btn btn-danger waves-effect text-left ajax-request"
                data-url="{{ url('/settings/account/email/local') }}" data-loading-target="" data-ajax-type="POST"
                data-type="form" data-form-id="email_settings_local"
                data-on-start-submit-button="disable">@lang('lang.save_changes')</button>
        </div>
    </div>
    @endif





    <!--SMTP EMAIL-->
    <div class="email_settings {{ runtimeVisibilitySection($settings->settings_email_server_type, 'smtp') }}"
        id="email_settings_smtp">

        <div class="form-group row">
            <label class="col-sm-12 text-left control-label col-form-label">@lang('lang.email_address') <span
                    class="align-middle text-info font-16" data-toggle="tooltip"
                    title="@lang('lang.use_smtp_server_info')" data-placement="top"><i
                        class="ti-info-alt"></i></span></label>
            <div class="col-sm-12">
                <input type="text" class="form-control form-control-sm saas_email_selector"
                    id="settings_email_from_address	" name="settings_email_from_address"
                    value="{{ $settings->settings_email_from_address	 ?? '' }}">
            </div>
        </div>
        <!--from name-->
        <div class="form-group row">
            <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.from_name')) }}</label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="settings_email_from_name"
                    name="settings_email_from_name" value="{{ $settings->settings_email_from_name ?? '' }}">
            </div>
        </div>
        <div class="line"></div>

        <!--smtp host-->
        <div class="form-group row">
            <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.smtp_host')) }}</label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="settings_email_smtp_host"
                    name="settings_email_smtp_host" value="{{ $settings->settings_email_smtp_host ?? '' }}">
            </div>
        </div>

        <!--port-->
        <div class="form-group row">
            <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.smtp_port')) }}</label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="settings_email_smtp_port"
                    name="settings_email_smtp_port" value="{{ $settings->settings_email_smtp_port ?? '' }}">
            </div>
        </div>

        <!--usrname-->
        <div class="form-group row">
            <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.username')) }}</label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="settings_email_smtp_username"
                    name="settings_email_smtp_username" value="{{ $settings->settings_email_smtp_username ?? '' }}">
            </div>
        </div>

        <!--password-->
        <div class="form-group row">
            <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.password')) }}</label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="settings_email_smtp_password"
                    name="settings_email_smtp_password" value="{{ $settings->settings_email_smtp_password ?? '' }}">
            </div>
        </div>

        <!--ensryption-->
        <div class="form-group row">
            <label for="example-month-input"
                class="col-12 col-form-label text-left">{{ cleanLang(__('lang.encryption')) }}</label>
            <div class="col-12">
                <select class="select2-basic form-control form-control-sm" id="settings_email_smtp_encryption"
                    name="settings_email_smtp_encryption">
                    <option value="none">None</option>
                    <option value="tls"
                        {{ runtimePreselected('tls', $settings->settings_email_smtp_encryption ?? '') }}>
                        TLS</option>
                    <option value="starttls"
                        {{ runtimePreselected('starttls', $settings->settings_email_smtp_encryption ?? '') }}>
                        STARTTLS</option>
                    <option value="ssl"
                        {{ runtimePreselected('ssl', $settings->settings_email_smtp_encryption ?? '') }}>
                        SSL</option>
                </select>
            </div>
        </div>

        <div class="text-right p-t-30">
            <button type="submit" class="btn btn-danger waves-effect text-left ajax-request"
                data-url="{{ url('/settings/account/email/smtp') }}" data-loading-target="" data-ajax-type="POST"
                data-type="form" data-form-id="email_settings_smtp"
                data-on-start-submit-button="disable">@lang('lang.save_changes')</button>
        </div>

    </div>

</div>
@endsection