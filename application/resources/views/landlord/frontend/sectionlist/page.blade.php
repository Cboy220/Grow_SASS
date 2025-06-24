@extends('landlord.frontend.wrapper')
@section('settings_content')

<!--tabs menu-->
@include('landlord.frontend.components.home-menu')

<!--form-->
<div class="card">
    <div class="card-body" id="landlord-settings-form">

        <form class="form" id="settingsForm">


            <!--heading_1-->
            <div class="form-group row">
                <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.heading_1')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_1" name="frontend_data_1"
                        value="{{ $section->frontend_data_1 ?? '' }}">
                </div>
            </div>

            <!--heading_2-->
            <div class="form-group row">
                <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.heading_2')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_2" name="frontend_data_2"
                        value="{{ $section->frontend_data_2 ?? '' }}">
                </div>
            </div>

            <!--submit-->
            <div class="text-right">
                <button type="submit" class="btn btn-rounded-x btn-danger btn-sm waves-effect text-left ajax-request"
                    data-url="{{ url('/app-admin/frontend/section/'.request()->segment(4).'/list') }}" data-form-id="landlord-settings-form"
                    data-loading-target="" data-ajax-type="post" data-type="form"
                    data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
            </div>



            <div class="line m-t-30 m-b-10"></div>

            <!--each sub section-->
            @foreach($items as $item)
            @include('landlord.frontend.components.icon-section')
            @endforeach

        </form>
    </div>
</div>
@endsection