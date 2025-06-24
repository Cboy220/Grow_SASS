@foreach($items as $item)
<!--each row-->
<tr id="item_{{ $item->frontend_id }}">
    <td class="col_name">
        <!--sorting data-->
        <input type="hidden" name="sort-menu[{{ $item->frontend_id }}]" value="{{ $item->frontend_id }}">
        <span class="mdi mdi-drag-vertical cursor-pointer"></span>
         {{ $item->frontend_data_1 }}
    </td>
    <td class="col_link">
        {{ $item->frontend_data_2 }}
    </td>
    <td class="col_action actions_column" id="col_action_{{ $item->frontend_id }}">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--delete-->
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="@lang('lang.delete_item')" data-confirm-text="@lang('lang.are_you_sure')"
                data-ajax-type="DELETE" data-url="{{ url('/app-admin/frontend/mainmenu/'.$item->frontend_id) }}">
                <i class="sl-icon-trash"></i>
            </button>
            <!--edit-->
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/app-admin/frontend/mainmenu/'.$item->frontend_id.'/edit') }}"
                data-loading-target="commonModalBody" data-modal-title="@lang('lang.edit_menu_item')"
                data-action-url="{{ urlResource('/app-admin/frontend/mainmenu/'.$item->frontend_id.'?ref=list') }}"
                data-action-method="PUT" data-action-ajax-class="" data-action-ajax-loading-target="items-td-container">
                <i class="sl-icon-note"></i>
            </button>
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->