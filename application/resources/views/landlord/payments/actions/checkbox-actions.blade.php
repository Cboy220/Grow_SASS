<div class="col-12 align-self-center hidden checkbox-actions box-shadow-minimum" id="foos-checkbox-actions-container">
    <!--button-->
    <div class="x-buttons">
        <button type="button" class="btn btn-sm btn-default waves-effect waves-dark confirm-action-danger"
            data-type="form" data-ajax-type="POST" data-form-id="foos-list-table" data-url="{{ url('/foos/delete') }}"
            data-confirm-title="{{ cleanLang(__('lang.delete_selected_foos')) }}" data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
            id="checkbox-actions-delete-foos">
            <i class="sl-icon-trash"></i> @lang('lang.delete')
        </button>
    </div>
</div>