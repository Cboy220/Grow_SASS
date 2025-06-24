<!--CRUMBS CONTAINER (RIGHT)-->
<div class="col-md-12 align-self-center text-right {{ $page['list_page_actions_size'] ?? '' }} {{ $page['list_page_container_class'] ?? '' }}"
    id="list-page-actions-container">
    <div id="list-page-actions">
        <!--ADD NEW ITEM-->
        <a type="button" class="btn btn-danger btn-add-circle" href="{{ url('/app-admin/frontend/pages/create') }}">
            <i class="ti-plus"></i>
        </a>
    </div>
</div>