@foreach($board['leads'] as $lead)
<!--each card-->
<div class="kanban-card show-modal-button reset-card-modal-form js-ajax-ux-request {{ $lead->pinned_status ?? '' }}"
    data-toggle="modal" data-target="#cardModal" data-url="{{ urlResource('/leads/'.$lead->lead_id) }}"
    data-lead-id="{{ $lead->lead_id }}" data-loading-target="main-top-nav-bar" id="card_lead_{{ $lead->lead_id }}">
    <!--cover image-->
    <div id="kanban-card-cover-image-{{ $lead->lead_id }}"
        class="kanban-card-cover-image {{ runtimeKanbanCoverImage($lead->lead_cover_image) }}" {!!
        runtimeCoverImage($lead->lead_cover_image_uniqueid, $lead->lead_cover_image_filename) !!}>

    </div>
    <div class="kanban-card-content-comntainer">
        <div class="x-title wordwrap" id="kanban_lead_title_{{ $lead->lead_id }}">{{ $lead->lead_title }}
            <span class="x-action-button" id="card-action-button-{{ $lead->lead_id }}" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false"><i class="mdi mdi-dots-vertical"></i></span>
            <div class="dropdown-menu dropdown-menu-small dropdown-menu-right js-stop-propagation"
                aria-labelledby="card-action-button-{{ $lead->lead_id }}">
                @php $count_actions = 0 ; @endphp

                <!--send email-->
                <a class="dropdown-item data-toggle-action-tooltip edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    data-toggle="modal" data-target="#commonModal"
                    data-url="{{ url('/appwebmail/compose?view=modal&webmail_template_type=leads&resource_type=lead&resource_id='.$lead->lead_id) }}"
                    data-loading-target="commonModalBody" data-modal-title="@lang('lang.send_email')"
                    data-action-url="{{ url('/appwebmail/send') }}" data-action-method="POST" data-modal-size="modal-xl"
                    data-action-ajax-loading-target="leads-td-container">
                    @lang('lang.send_email')
                </a>

                <!--delete-->
                @if($lead->permission_delete_lead)
                <a class="dropdown-item confirm-action-danger js-stop-propagation"
                    data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
                    data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                    data-url="{{ url('/') }}/leads/{{ $lead->lead_id }}">{{ cleanLang(__('lang.delete')) }}</a>
                @php $count_actions ++ ; @endphp
                @endif


                <!--clone lead (team only)-->
                @if($lead->permission_edit_lead)
                <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    data-toggle="modal" data-target="#commonModal" data-modal-title="@lang('lang.clone_lead')"
                    data-url="{{ urlResource('/leads/'.$lead->lead_id.'/clone') }}"
                    data-action-url="{{ urlResource('/leads/'.$lead->lead_id.'/clone') }}" data-modal-size="modal-lg"
                    data-loading-target="commonModalBody" data-action-method="POST" aria-expanded="false">
                    @lang('lang.clone_lead')
                </a>
                @php $count_actions ++ ; @endphp
                @endif

                <!--archive-->
                @if($lead->permission_edit_lead && runtimeArchivingOptions())
                <a class="dropdown-item confirm-action-info  {{ runtimeActivateOrAchive('archive-button', $lead->lead_active_state) }} card_archive_button_{{ $lead->lead_id }}"
                    id="card_archive_button_{{ $lead->lead_id }}"
                    data-confirm-title="{{ cleanLang(__('lang.archive_lead')) }}"
                    data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="PUT"
                    data-url="{{ urlResource('/leads/'.$lead->lead_id.'/archive') }}">
                    {{ cleanLang(__('lang.archive')) }}
                </a>
                @php $count_actions ++ ; @endphp
                @endif

                <!--activate-->
                @if($lead->permission_edit_lead && runtimeArchivingOptions())
                <a class="dropdown-item confirm-action-info {{ runtimeActivateOrAchive('activate-button', $lead->lead_active_state) }} card_restore_button_{{ $lead->lead_id }}"
                    id="card_restore_button_{{ $lead->lead_id }}"
                    data-confirm-title="{{ cleanLang(__('lang.restore_lead')) }}"
                    data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="PUT"
                    data-url="{{ urlResource('/leads/'.$lead->lead_id.'/activate') }}">
                    {{ cleanLang(__('lang.restore')) }}
                </a>
                @php $count_actions ++ ; @endphp
                @endif

                <!--no actions-->
                @if($count_actions == 0)
                <a class="dropdown-item js-stop-propagation"
                    href="javascript:void(0);">{{ cleanLang(__('lang.no_actions_available')) }}</a>
                @endif
            </div>
        </div>
        <div class="x-meta">
            <!--name-->
            <label class="label label-outline-default p-t-3 p-b-3 p-r-8 p-l-8">{{ $lead->lead_firstname }}
                {{ $lead->lead_lastname }}</label>
            <!--value-->
            @if(config('system.settings_leads_kanban_value') == 'show')
            <div><label
                    class="label label-light-info p-t-3 p-b-3 p-r-8 p-l-8">{{ runtimeMoneyFormat($lead->lead_value) }}</label>
            </div>
            @endif

            <!--tags-->
            @if(config('system.settings_leads_kanban_tags') == 'show')
            <div class="wrap-words">
                @foreach($lead->tags as $lead_tag)
                <label class="label label-light-primary p-t-3 p-b-3 p-r-8 p-l-8">{{ $lead_tag->tag_title }}</label>
                @endforeach
            </div>
            @endif


            <!--telephone-->
            @if(config('system.settings_leads_kanban_telephone') == 'show')
            <span class="wordwrap"><strong>{{ cleanLang(__('lang.telephone')) }}:</strong>
                {{ $lead->lead_phone ?? '---' }}</span>
            @endif
            <!--date created-->
            @if(config('system.settings_leads_kanban_date_created') == 'show')
            <span><strong>{{ cleanLang(__('lang.created')) }}:</strong> {{ runtimeDate($lead->lead_created) }}</span>
            @endif
            <!--date contacted-->
            @if(config('system.settings_leads_kanban_date_contacted') == 'show')
            <span><strong>{{ cleanLang(__('lang.contacted')) }}:</strong>
                {{ runtimeDate($lead->lead_last_contacted ) }}</span>
            @endif
            <!--category-->
            @if(config('system.settings_leads_kanban_category') == 'show')
            <span><strong>{{ cleanLang(__('lang.category')) }}:</strong> {{ $lead->category_name ?? '---' }}</span>
            @endif
            <!--email-->
            @if(config('system.settings_leads_kanban_email') == 'show')
            <span class="wordwrap"><strong>{{ cleanLang(__('lang.email')) }}:</strong>
                {{ $lead->lead_email ?? '---' }}</span>
            @endif
            <!--source-->
            @if(config('system.settings_leads_kanban_source') == 'show')
            <span class="wordwrap"><strong>{{ cleanLang(__('lang.source')) }}:</strong>
                {{ $lead->lead_source ?? '---'  }}</span>
            @endif

            <!--show enabled custom fields-->
            @foreach($lead->fields as $field)
            @if($field->customfields_show_lead_summary == 'yes')
            <span><strong>{{ $field->customfields_title }}:</strong>:
                {{ strip_tags(customFieldValue($field->customfields_name, $lead, $field->customfields_datatype)) }}</span>
            @endif
            @endforeach

            <!--reminder-->
            @if(config('system.settings_leads_kanban_reminder') == 'show')
            <div class="wrap-words" id="reminder_card_view_container_{{ $lead->lead_id ?? '' }}">
                @foreach($lead->reminders as $reminder)
                <!--only show current users reminders-->
                @if($reminder->reminder_userid == auth()->user()->id)
                @include('pages.reminders.cards.kanban')
                @endif
                @endforeach
            </div>
            @endif

        </div>
        <div class="x-footer row">
            <div class="col-6 x-icons">

                <!--AI Analysis-->
                <button type="button"
                    class="x-icon display-inline-block vm p-t-2 mr-1 data-toggle-action-tooltip font-14 js-ajax-ux-request"
                    data-toggle="modal"
                    data-target="#basicModal"
                    data-url="{{ url('/leads/analyze-ai/modal?lead_id=' . $lead->lead_id) }}"
                    data-ajax-type="GET"
                    data-loading-target="basicModalContent"
                    data-loading-class="loading"
                    style="background:none; border:none; box-shadow:none; padding:0;">
                    <i class="fas fa-robot text-info"></i>
                </button>
                <!--/AI Analysis-->
                <!--pin-->
                <span title="{{ cleanLang(__('lang.pinning')) }}" data-parent="lead_{{ $lead->lead_id }}"
                    data-url="{{ url('/leads/'.$lead->lead_id.'/pinning') }}"
                    class="x-icon display-inline-block vm p-t-2 data-toggle-action-tooltip opacity-4 js-toggle-pinning font-14">
                    <i class="ti-pin2"></i>
                </span>

                <!--captured via a webform-->
                @if($lead->lead_input_source == 'webform')
                <span class="x-icon" data-toggle="tooltip" title="@lang('lang.source_webform')"><i
                        class="sl-icon-cloud-upload"></i></span>
                @endif

                <!--created by you-->
                @if($lead->lead_creatorid == auth()->user()->id)
                <span class="x-icon text-info" data-toggle="tooltip" title="@lang('lang.you_created_this_lead')"
                    data-placement="top"><i class="mdi mdi-account-circle"></i></span>
                @endif


                <!--converted-->
                @if($lead->lead_converted == 'yes')
                <span class="x-icon text-success"><i class="mdi mdi-star" data-toggle="tooltip"
                        title="{{ cleanLang(__('lang.customer')) }}"></i></span>
                @endif

                <!--archived-->
                @if(runtimeArchivingOptions())
                <span class="x-icon {{ runtimeActivateOrAchive('archived-icon', $lead->lead_active_state) }}"
                    id="archived_icon_{{ $lead->lead_id }}" data-toggle="tooltip" title="@lang('lang.archived')"><i
                        class="ti-archive font-15"></i></span>
                @endif

                <!--attachments-->
                @if($lead->has_attachments)
                <span class="x-icon"><i class="mdi mdi-attachment"></i>
                    @if($lead->count_unread_attachments > 0)
                    <span class="x-notification" id="card_notification_attachment_{{ $lead->lead_id }}"></span>
                    @endif
                </span>
                @endif
                <!--comments-->
                @if($lead->has_comments)
                <span class="x-icon"><i class="mdi mdi-comment-text-outline"></i>
                    @if($lead->count_unread_comments > 0)
                    <span class="x-notification" id="card_notification_comment_{{ $lead->lead_id }}"></span>
                    @endif
                </span>
                @endif
                <!--checklists-->
                @if($lead->has_checklist)
                <span class="x-icon"><i class="mdi mdi-checkbox-marked-outline"></i></span>
                @endif

            </div>
            <div class="col-6 x-assigned">
                @foreach($lead->assigned as $user)
                <img src="{{ getUsersAvatar($user->avatar_directory, $user->avatar_filename) }}" data-toggle="tooltip"
                    title="" data-placement="top" alt="{{ $user->first_name }}" class="img-circle avatar-xsmall"
                    data-original-title="{{ $user->first_name }}">
                @endforeach
            </div>
        </div>
    </div>
</div>
@endforeach