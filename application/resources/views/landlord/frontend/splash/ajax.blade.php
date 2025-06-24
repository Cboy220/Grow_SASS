<div class="col-sm-12 col-md-6 col-lg-4" id="splash_item_{{ $item->frontend_id }}">
    <div class="splah-item-editing">
        <img
        src="{{ url('storage/frontend/'.$item->frontend_directory.'/'.$item->frontend_filename) }}">
        <div class="p-t-8 p-b-8"><h4>{{ $item->frontend_data_1 }}</h4></div>
        <div>
            <button class="btn btn-rounded-x btn-info btn-xs waves-effect text-left edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" 
                data-target="#commonModal" 
                data-url="{{ url('/app-admin/frontend/section/'.$item->frontend_id.'/splash') }}"
                data-loading-target="commonModalBody" 
                data-modal-title="@lang('lang.edit')" 
                data-action-url="{{ url('/app-admin/frontend/section/'.$item->frontend_id.'/splash') }}"
                data-action-ajax-class="js-ajax-ux-request"
                data-action-method="POST">@lang('lang.edit')</button>
        </div>
    </div>
</div>