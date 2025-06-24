@extends('landlord.frontend.wrapper')
@section('settings_content')


<!--page heading-->
<div class="row page-titles">

    <!-- action buttons -->
    @include('landlord.frontend.mainmenu.actions.page-actions')
    <!-- action buttons -->

</div>
<!--page heading-->

<!--form-->
<div class="card">
    <div class="card-body" id="landlord-settings-form">

        <div class="table-responsive list-table-wrapper">
            @if (@count($items) > 0)
            <table id="main-menu-list-table" class="table m-t-0 m-b-0 table-hover no-wrap item-list" data-page-size="10"
                data-type="form" data-form-id="main-menu-list-table" data-ajax-type="post"
                data-url="{{ url('/app-admin/frontend/mainmenu/update-positions') }}">
                <thead>
                    <tr>
                        <!--item-->
                        <th class="col_name">
                            @lang('lang.name')
                        </th>
                        <!--item-->
                        <th class="col_link">
                            @lang('lang.link')
                        </th>
                        <!--actions-->
                        <th class="col_action w-px-100"><a href="javascript:void(0)">@lang('lang.action')</a></th>
                    </tr>
                </thead>
                <tbody id="main-menu-td-container">
                    <!--ajax content here-->
                    @include('landlord.frontend.mainmenu.table.ajax')

                    <!--ajax content here-->

                    <!--bulk actions - change category-->
                    <input type="hidden" id="checkbox_actions_items_category">
                </tbody>
            </table>
            @endif @if (@count($items) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>

<script src="public/js/landlord/dynamic/mainmenu.sortable.js?v={{ config('system.versioning') }}"></script>
@endsection