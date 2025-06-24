@extends('landlord.frontend.wrapper')
@section('settings_content')


<!--page heading-->
<div class="row page-titles">

    <!-- action buttons -->
    @include('landlord.frontend.pages.actions.page-actions')
    <!-- action buttons -->

</div>
<!--page heading-->

<!--form-->
<div class="card">
    <div class="card-body" id="landlord-settings-form">


        <div class="table-responsive list-table-wrapper">
            @if (@count($pages) > 0)
            <table id="pages-list-table" class="table m-t-0 m-b-0 table-hover no-wrap item-list" data-page-size="10"
                data-type="form" data-form-id="pages-td-container" data-ajax-type="post"
                data-url="{{ url('/app-admin/frontend/pages/update-positions') }}">
                <thead>
                    <tr>
                        <!--page_title-->
                        <th class="col_page_title"><a class="js-ajax-ux-request js-list-sorting" id="sort_page_title"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('app-admin/pages?action=sort&orderby=page_title&sortorder=asc') }}">@lang('lang.title')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                        <!--creator-->
                        <th class="col_creator"><a class="js-ajax-ux-request js-list-sorting" id="sort_creator"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('app-admin/pages?action=sort&orderby=creator&sortorder=asc') }}">@lang('lang.created_by')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                        <!--page_created-->
                        <th class="col_page_created"><a class="js-ajax-ux-request js-list-sorting" id="sort_page_title"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('app-admin/pages?action=sort&orderby=page_created&sortorder=asc') }}">@lang('lang.created')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                        <!--page_status-->
                        <th class="col_page_status"><a class="js-ajax-ux-request js-list-sorting" id="sort_page_title"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('app-admin/pages?action=sort&orderby=page_status&sortorder=asc') }}">@lang('lang.status')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                        <!--actions-->
                        <th class="col_action w-px-100"><a href="javascript:void(0)">@lang('lang.action')</a></th>
                    </tr>
                </thead>
                <tbody id="pages-td-container">
                    <!--ajax content here-->
                    @include('landlord.frontend.pages.table.ajax')

                    <!--ajax content here-->

                    <!--bulk actions - change category-->
                    <input type="hidden" id="checkbox_actions_items_category">
                </tbody>
            </table>
            @endif @if (@count($pages) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>
@endsection