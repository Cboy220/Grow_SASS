<div class="section-element-box clearfix" id="section_image_{{ $item->frontend_id }}">
    <div class="x-image">
        <img src="{{ url('storage/frontend/'.$item->frontend_directory.'/'.$item->frontend_filename) }}"
            alt="@lang('lang.error_404')" />
    </div>
    <div class="x-title">
        <h4>{{ $item->frontend_data_1 }}</h4>
        {{ $item->frontend_data_2 }}
        <div class="text-right">
            <button class="btn btn-rounded-x btn-info btn-xs waves-effect text-left edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" 
                data-target="#commonModal" 
                data-url="{{ url('/app-admin/frontend/section/'.$item->frontend_id.'/image-content') }}"
                data-loading-target="commonModalBody" 
                data-modal-title="@lang('lang.edit')" 
                data-action-url="{{ url('/app-admin/frontend/section/'.$item->frontend_id.'/image-content') }}"
                data-action-ajax-class="js-ajax-ux-request"
                data-action-method="POST">@lang('lang.edit')</button>
        </div>
    </div>
</div>