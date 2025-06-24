@foreach($foos as $foo)
<!--each row-->
<tr id="foo_{{ $foo->foo_id }}">
    <td class="foos_col_checkbox checkfoo" id="foos_col_checkbox_{{ $foo->foo_id }}">
        <!--list checkbox-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-foos-{{ $foo->foo_id }}" name="ids[{{ $foo->foo_id }}]"
                class="listcheckbox listcheckbox-foos filled-in chk-col-light-blue foos-checkbox"
                data-actions-container-class="foos-checkbox-actions-container" data-foo-id="{{ $foo->foo_id }}"
                data-unit="{{ $foo->foo_unit }}" data-quantity="1" data-description="{{ $foo->foo_description }}"
                data-rate="{{ $foo->foo_rate }}">
            <label for="listcheckbox-foos-{{ $foo->foo_id }}"></label>
        </span>
    </td>
    <td class="foos_col_rate" id="foos_col_rate_{{ $foo->foo_id }}">
        {{ $foo->foo_rate }}
    </td>
    <td class="foos_col_action actions_column" id="foos_col_action_{{ $foo->foo_id }}">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--delete-->
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_product')) }}" data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                data-ajax-type="DELETE" data-url="{{ url('/') }}/foos/{{ $foo->foo_id }}">
                <i class="sl-icon-trash"></i>
            </button>
            <!--edit-->
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/foos/'.$foo->foo_id.'/edit') }}" data-loading-target="commonModalBody"
                data-modal-title="{{ cleanLang(__('lang.edit_product')) }}"
                data-action-url="{{ urlResource('/foos/'.$foo->foo_id.'?ref=list') }}" data-action-method="PUT"
                data-action-ajax-class="" data-action-ajax-loading-target="foos-td-container">
                <i class="sl-icon-note"></i>
            </button>
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->