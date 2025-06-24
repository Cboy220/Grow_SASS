<!--CRUMBS CONTAINER (RIGHT)-->
<div class="col-md-12  col-lg-7 p-b-9 align-self-center text-right {{ $page['list_page_actions_size'] ?? '' }} {{ $page['list_page_container_class'] ?? '' }}"
    id="list-page-actions-container">
    <div id="list-page-actions">
       <!--archived-->
        <a data-toggle="tooltip" title="{{ cleanLang(__('lang.archived')) }}"
            class="list-actions-button btn btn-page-actions waves-effect waves-dark {{ saasToggleArchivedPackages() }}"
            href="{{ url('app-admin/packages?package_status=archived') }}">
            <i class="ti-archive"></i>
        </a>

        <!--ADD NEW ITEM-->
        <button type="button"
            class="btn btn-danger btn-add-circle edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
            data-toggle="modal" data-target="#commonModal" data-url="{{ url('/app-admin/packages/create') }}"
            data-loading-target="commonModalBody" data-modal-title="@lang('lang.create_package')"
            data-action-url="{{ url('/app-admin/packages') }}" data-action-method="POST" data-modal-size="modal-xl"
            data-action-ajax-loading-target="commonModalBody">
            <i class="ti-plus"></i>
        </button>
    </div>
</div>