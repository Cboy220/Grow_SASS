@extends('landlord.settings.wrapper')
@section('settings_content')

<!--form-->
<div class="card">
    <div class="card-body" id="landlord-settings-form">


        <!--settings_offline_payments_status-->
        <div class="form-group row">
            <label
                class="col-sm-12 text-left control-label col-form-label">@lang('lang.offline_payments_status')</label>
            <div class="col-sm-12">
                <select class="select2-basic form-control form-control-sm select2-preselected"
                    id="settings_offline_payments_status" name="settings_offline_payments_status"
                    data-preselected="{{ $settings->settings_offline_payments_status ?? 'disabled' }}">
                    <option></option>
                    <option value="enabled">@lang('lang.enabled')</option>
                    <option value="disabled">@lang('lang.disabled')</option>
                </select>
            </div>
        </div>


        <!--settings_offline_payments_display_name-->
        <div class="form-group row">
            <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.display_name')) }}</label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="settings_offline_payments_display_name"
                    name="settings_offline_payments_display_name"
                    value="{{ $settings->settings_offline_payments_display_name ?? '' }}">
            </div>
        </div>

        <!--settings_offline_payments_details-->
        <div class="form-group row">
            <label
                class="col-sm-12 text-left control-label col-form-label required">@lang('lang.payment_details_instructions')*</label>
            <div class="col-sm-12">
                <textarea class="form-control form-control-sm tinymce-textarea" rows="5"
                    name="settings_offline_payments_details"
                    id="settings_offline_payments_details">{{ $settings->settings_offline_payments_details ?? '' }}</textarea>
            </div>
        </div>

        <div class="line"></div>


        <!--settings_offline_proof_of_payment_message-->
        <div class="form-group row">
            <label
                class="col-sm-12 text-left control-label col-form-label">@lang('lang.proof_of_payment_information')</label>
            <div class="col-sm-12">
                <textarea class="form-control form-control-sm tinymce-textarea-extended-lite" rows="5"
                    name="html_settings_offline_proof_of_payment_message"
                    id="html_settings_offline_proof_of_payment_message">{{ $settings->settings_offline_proof_of_payment_message ?? '' }}</textarea>
            </div>
        </div>


        <!--settings_offline_proof_of_payment_hankyou_message-->
        <div class="form-group row">
            <label
                class="col-sm-12 text-left control-label col-form-label p-b-10">@lang('lang.proof_of_payment_thank_you')</label>
            <div class="col-sm-12">
                <textarea class="form-control form-control-sm tinymce-textarea-extended-lite" rows="5"
                    name="html_settings_offline_proof_of_payment_thank_you"
                    id="html_settings_offline_proof_of_payment_thank_you">{{ $settings->settings_offline_proof_of_payment_thank_you ?? '' }}</textarea>
            </div>
        </div>


        <!--submit-->
        <div class="text-right">
            <button type="submit" id="commonModalSubmitButton"
                class="btn btn-rounded-x btn-danger waves-effect text-left ajax-request"
                data-url="{{ url('app-admin/settings/offlinepayments') }}" data-form-id="landlord-settings-form"
                data-loading-target="" data-ajax-type="post" data-type="form"
                data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
        </div>
    </div>
</div>
@endsection