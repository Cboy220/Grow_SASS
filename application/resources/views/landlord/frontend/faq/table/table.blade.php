@extends('landlord.frontend.wrapper')
@section('settings_content')


<!--page heading-->
<div class="row page-titles">

    <!-- action buttons -->
    @include('landlord.frontend.faq.actions.page-actions')
    <!-- action buttons -->

</div>
<!--page heading-->

<!--form-->
<div class="card">
    <div class="card-body" id="landlord-settings-form">


        <!--heading-->
        <div class="form-group row">
            <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.heading')</label>
            <div class="col-sm-12">
                <input type="text" class="form-control form-control-sm" id="frontend_data_1" name="frontend_data_1"
                    value="{{ $section->frontend_data_1 ?? '' }}">
            </div>
        </div>

        <!--subheading-->
        <div class="form-group row">
            <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.subheading')</label>
            <div class="col-sm-12">
                <input type="text" class="form-control form-control-sm" id="frontend_data_2" name="frontend_data_2"
                    value="{{ $section->frontend_data_2 ?? '' }}">
            </div>
        </div>

        <div class="table-responsive list-table-wrapper">
            @if (@count($faqs) > 0)
            <table id="faq-list-table" class="table m-t-0 m-b-0 table-hover no-wrap item-list" data-page-size="10"
                data-type="form" data-form-id="faq-td-container" data-ajax-type="post"
                data-url="{{ url('/app-admin/frontend/faq/update-positions') }}">
                <thead>
                    <tr>
                        <!--item-->
                        <th class="col_name">
                            @lang('lang.title')
                        </th>
                        <!--actions-->
                        <th class="col_action w-px-100"><a href="javascript:void(0)">@lang('lang.action')</a></th>
                    </tr>
                </thead>
                <tbody id="faq-td-container">
                    <!--ajax content here-->
                    @include('landlord.frontend.faq.table.ajax')

                    <!--ajax content here-->

                    <!--bulk actions - change category-->
                    <input type="hidden" id="checkbox_actions_items_category">
                </tbody>
            </table>
            @endif @if (@count($faqs) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>

        <!--submit-->
        <div class="text-right">
            <button type="submit"
                class="btn btn-rounded-x btn-danger btn-sm waves-effect text-left ajax-request"
                data-url="{{ url('/app-admin/frontend/faq/update') }}" data-form-id="landlord-settings-form"
                data-loading-target="" data-ajax-type="post" data-type="form"
                data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
        </div>
    </div>
</div>

<script src="public/js/landlord/dynamic/faq.sortable.js?v={{ config('system.versioning') }}"></script>
@endsection