@extends('landlord.settings.wrapper')
@section('settings_content')

<!--page heading-->
<div class="row page-titles">
    @include('landlord.misc.crumbs')

    <!--select dropdown-->
    <div class="col-md-12 col-lg-7 clearfix p-t-19">
        <div id="list-page-actions" class="pull-right w-px-300 select-email-template-dropdown"
            id="fx-settings-emailtemplates-dropdown">
            <form id="fix-form-email-templates">
                <select class="select2-basic form-control form-control-sm text-left" data-url=""
                    id="selectEmailTemplate" name="selectEmailTemplate">
                    <option value="0">@lang('lang.select_a_template')</option>
                    <!--customer emails-->
                    <optgroup label="{{ cleanLang(__('lang.customer')) }}">
                        @foreach($customer as $template)
                        <option value="{{ url('app-admin/settings/emailtemplates/'.$template->emailtemplate_id) }}">
                            {{ runtimeLang($template->emailtemplate_lang) }}
                        </option>
                        @endforeach
                    </optgroup>
                    <!--admin   -->
                    <optgroup label="{{ cleanLang(__('lang.admin')) }}">
                        @foreach($admin as $template)
                        <option value="{{ url('app-admin/settings/emailtemplates/'.$template->emailtemplate_id) }}">
                            {{ runtimeLang($template->emailtemplate_lang) }}
                        </option>
                        @endforeach
                    </optgroup>
                    <!--other-->
                    <optgroup label="{{ cleanLang(__('lang.other')) }}">
                        @foreach($other as $template)
                        <option value="{{ url('app-admin/settings/emailtemplates/'.$template->emailtemplate_id) }}">
                            {{ runtimeLang($template->emailtemplate_lang) }}
                        </option>
                        @endforeach
                    </optgroup>
                    <!--system-->
                    <optgroup label="{{ cleanLang(__('lang.system')) }}">
                        @foreach($system as $template)
                        <option value="{{ url('app-admin/settings/emailtemplates/'.$template->emailtemplate_id) }}">
                            {{ runtimeLang($template->emailtemplate_lang) }}
                        </option>
                        @endforeach
                    </optgroup>
                </select>
            </form>
        </div>
    </div>
</div>





<!--form-->
<div class="card">
    <div class="card-body min-h-400" id="landlord-settings-form">
        <!--welcome-->
        <div class="row">
            <div class="col-12">
                <div class="page-notification-imaged">
                    <img src="{{ url('/') }}/public/images/email.png" alt="Application Settings" />
                    <div class="message">
                        <h4>{{ cleanLang(__('lang.select_email_template_from_dropdown')) }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection