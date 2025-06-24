@foreach($users as $user)
<!--each row-->
<tr id="user_{{ $user->id }}">
    <!--name-->
    <td class="col_name">
        <img src="{{ getUsersAvatar($user->avatar_directory, $user->avatar_filename, $user->id) }}" alt="user"
            class="img-circle avatar-xsmall">
        {{ $user->first_name }} {{ $user->last_name }}
        @if($user->id == 1)
        <span class="sl-icon-star text-warning p-l-5" data-toggle="tooltip"
            title="{{ cleanLang(__('lang.primary_admin')) }}"></span>
        @endif
    </td>
    <!--email-->
    <td class="col_email">
        {{ $user->email }}
    </td>
    <!--created-->
    <td class="col_created">
        {{ runtimeDate($user->created) }}
    </td>

    <!--actions-->
    <td class="col_action actions_column">

        @if(auth()->user()->primary_admin == 'yes')
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--delete-->
            @if($user->primary_admin == 'yes')
            <span class="btn btn-outline-default btn-circle btn-sm disabled {{ runtimePlaceholdeActionsButtons() }}"
                data-toggle="tooltip" title="{{ cleanLang(__('lang.actions_not_available')) }}"><i
                    class="sl-icon-trash"></i></span>
            @else
            <button type="button" title="@lang('lang.delete')"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="@lang('lang.delete_user')" data-confirm-text="@lang('lang.are_you_sure')"
                data-ajax-type="DELETE" data-url="{{ url('/app-admin/team/'.$user->id) }}">
                <i class="sl-icon-trash"></i>
            </button>
            @endif

            <!--edit-->
            <button type="button" title="@lang('lang.edit')"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/app-admin/team/'.$user->id.'/edit') }}" data-loading-target="commonModalBody"
                data-modal-title="@lang('lang.edit_user')"
                data-action-url="{{ urlResource('/app-admin/team/'.$user->id) }}" data-action-method="PUT"
                data-action-ajax-class="" data-action-ajax-loading-target="users-td-container">
                <i class="sl-icon-note"></i>
            </button>
        </span>
        @endif
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->