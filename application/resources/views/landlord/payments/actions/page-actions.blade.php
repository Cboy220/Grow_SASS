<!--CRUMBS CONTAINER (RIGHT)-->
<div class="col-md-12  col-lg-7 p-b-9 align-self-center text-right list-page-actions-containers {{ $page['list_page_actions_size'] ?? '' }} {{ $page['list_page_container_class'] ?? '' }}"
    id="list-page-actions-container-payments">
    <div id="list-page-actions">
        <!--SEARCH BOX-->
        <div class="header-search" id="header-search">
            <i class="sl-icon-magnifier"></i>
            <input type="text" class="form-control search-records list-actions-search"
                data-url="{{ url('app-admin/payments/search?action=search') }}" data-type="form" data-ajax-type="post"
                data-form-id="header-search" id="search_query" name="search_query"
                placeholder="{{ cleanLang(__('lang.search')) }}">
        </div>

        <!--ADD NEW ITEM-->
        <button type="button"
            class="btn btn-danger btn-add-circle edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
            data-toggle="modal" data-target="#commonModal" data-url="{{ urlResource('/app-admin/payments/create') }}"
            data-loading-target="commonModalBody" data-modal-title="@lang('lang.home')"
            data-action-url="{{ urlResource('/app-admin/payments') }}"
            data-action-method="POST"
            data-modal-size="modal-lg"
            data-action-ajax-loading-target="commonModalBody">
            <i class="ti-plus"></i>
        </button>
    </div>
</div>