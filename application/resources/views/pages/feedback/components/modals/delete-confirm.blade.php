<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content shadow">

        <!-- Header with soft blue and bold white title -->
        <div class="modal-header" style="background-color: #339af0;">
            <h5 class="modal-title mb-0 text-white font-weight-bold" id="smartDeleteModalLabel">
                <i class="fas fa-trash-alt mr-2"></i> {{ __('lang.confirm_deletion') }}
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span style="font-size: 22px;" aria-hidden="true">&times;</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body text-center py-3">
            <p class="mb-2">{{ __('lang.are_you_sure_delete_colon') }}</p>
            <strong id="itemToDelete">{{ __('lang.this_item') }}</strong>?
        </div>

        <!-- Compact Footer Buttons -->
        <div class="modal-footer px-3 py-2 d-flex justify-content-end">
            <button type="button" class="btn btn-secondary btn-sm mr-2" data-dismiss="modal">
                <i class="fas fa-times mr-1"></i>{{ __('lang.cancel') }}
            </button>
            <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn" 
            data-id="{{isset($id) ? $id : ''}}" data-action-url="{{isset($id) ? route($page['action_route'], $id) : ''}}">
                <i class="fas fa-check mr-1"></i>{{ __('lang.yes_delete') }}
            </button>
        </div>

    </div>
</div>
