<div class="subscription-details p-t-10 email-settings">

    <div class="x-heading">
        @if($customer->tenant_email_config_type == 'local')
        @lang('lang.mail_local')
        @else
        @lang('lang.mail_smtp')
        @endif
    </div>


    @if($customer->tenant_email_config_type == 'local')
    <table class="table no-border">
        <tbody>
            <tr>
                <td>@lang('lang.local_email_address')</td>
                <td class="font-medium w-30">{{ $customer->tenant_email_local_email ?? '---' }}</td>
            </tr>
            <tr>
                <td>@lang('lang.forward_to')</td>
                <td class="font-medium w-30">{{ $customer->tenant_email_forwarding_email ?? '---' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="line  m-t-30 p-b-40"></div>

    <h5 class="p-b-10">@lang('lang.actions')</h5>


    @if($customer->tenant_email_config_type == 'local' && $customer->tenant_email_config_status == 'completed')
    <div class="alert alert-info">
        @lang('lang.no_action_is_required')
    </div>
    @endif

    @if($customer->tenant_email_config_type == 'local' && $customer->tenant_email_config_status == 'pending')
    <div class="p-b-20 email_settings_pending_{{ $customer->tenant_id }}">@lang('lang.email_settings_comment')</div>

    <div class="email_settings_pending_{{ $customer->tenant_id }}">
        <div class="alert alert-danger">
            <div><strong>(@lang('lang.step_1'))</strong> - @lang('lang.forwarding_email_address_needs_setting')</div>
            <div class="m-t-8 font-weight-400 text-danger">{{ $customer->tenant_email_local_email }}</div>
        </div>
        <div class="alert alert-danger">
            <div><strong>(@lang('lang.step_2'))</strong> - @lang('lang.forwarding_email_address_needs_setting_2')</div>
            <div class="m-t-8 font-weight-400 text-danger">{{ $customer->tenant_email_forwarding_email }}</div>
        </div>
    </div>
    <div class="alert alert-info hidden" id="email_settings_completed_{{ $customer->tenant_id }}">
        @lang('lang.no_action_is_required')
    </div>

    <!--questions about this process-->
    <div class="m-b-30 email_settings_pending_{{ $customer->tenant_id }}">

        <div class="p-t-7"><a href="javascript:void(0);"
                id="why_is_this_needed_question">@lang('lang.why_is_this_needed')?</a></div>

        <div class="p-t-7"><a href="javascript:void(0);"
                id="how_can_i_automate_question">@lang('lang.how_can_i_automate_question')?</a></div>

        <!--why_is_this_needed-->
        <div class="modal-selector email_questions_answers m-t-20 m-l-0 m-r-0 p-b-25 hidden" id="why_is_this_needed_answer">
            @lang('lang.why_is_this_needed_1')
            </br /></br />
            @lang('lang.why_is_this_needed_2')
            </br /></br />
            @lang('lang.why_is_this_needed_3')
        </div>

        <!--how_can_i_automate-->
        <div class="modal-selector email_questions_answers m-t-20 m-l-0 m-r-0 p-b-25 hidden" id="how_can_i_automate_answer">
            @lang('lang.how_can_i_automate_answer')
        </br />
        </div>

    </div>

    <div class="text-center p-t-10" id="email_settings_button">
        <button id="submitButton" class="btn btn-danger waves-effect text-left ajax-request"
            data-url="{{ url('app-admin/customers/'.$customer->tenant_id.'/updated-email-forwarding?source=page') }}"
            data-button-loading-annimation="yes"
            data-ajax-type="GET"
            data-on-start-submit-button="disable">@lang('lang.mark_as_done')</button>
    </div>
    @endif
    @endif

    @if($customer->tenant_email_config_type == 'smtp')
    <div class="alert alert-info">
        @lang('lang.customer_is_using_own_stmp')
    </div>
    @endif



</div>