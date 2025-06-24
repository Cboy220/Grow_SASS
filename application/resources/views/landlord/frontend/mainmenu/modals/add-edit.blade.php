<!--menu name-->
<div class="form-group row">
    <label class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.name')</label>
    <div class="col-sm-12 col-lg-9">
        <input type="text" class="form-control form-control-sm" id="frontend_data_1" name="frontend_data_1"
            value="{{ $menu->frontend_data_1 ?? '' }}">
    </div>
</div>

<div class="modal-selector m-t-20 ">

    <!--link type-->
    <div class="form-group row">
        <label
            class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.link_type')</label>
        <div class="col-sm-12 col-lg-9">
            <select class="select2-basic form-control form-control-sm select2-preselected"
                id="landlord_main_menu_selector" name="frontend_data_3"
                data-preselected="{{ $menu->frontend_data_3 ?? 'internal'}}">
                <option></option>
                <option value="internal">@lang('lang.internal_page')</option>
                <option value="manual">@lang('lang.manual_link')</option>
            </select>
        </div>
    </div>


    <!--internal link-->
    <div class="landlord-main-menu-types {{ saasLinkTypeToggle($payload['link_type'], 'internal') }}" id="link_type_internal">
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.page')</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control form-control-sm select2-preselected" id="link_internal"
                    name="link_internal" data-preselected="{{ $menu->frontend_data_2 ?? '/'}}">
                    <option></option>
                    <option value="/">@lang('lang.home')</option>
                    <option value="/pricing">@lang('lang.pricing')</option>
                    <option value="/faq">@lang('lang.faq')</option>
                    <option value="/contact">@lang('lang.contact_us')</option>
                    <option value="/account/login">@lang('lang.log_in')</option>
                    <option value="/account/signup">@lang('lang.sign_up')</option>
                    @foreach($internal_pages as $internal_page)
                    <option value="/page/{{ $internal_page->page_permanent_link }}">{{ $internal_page->page_title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>


    <!--manual link-->
    <div class="landlord-main-menu-types {{ saasLinkTypeToggle($payload['link_type'], 'manual') }}" id="link_type_manual">
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.url')</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="link_manual" name="link_manual"
                    value="{{ $menu->frontend_data_2 ?? '' }}">
            </div>
        </div>
    </div>

        <!--manual link-->
        <div class="landlord-main-menu-types {{ saasLinkTypeToggle($payload['link_type'], 'manual') }}" id="link_type_manual_target">
            <div class="form-group row">
                <label class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.target')</label>
                <div class="col-sm-12 col-lg-9">
                    <select class="select2-basic form-control form-control-sm select2-preselected" id="link_target"
                        name="link_target" data-preselected="{{ $menu->frontend_data_6 ?? 'same_window'}}">
                        <option></option>
                        <option value="same_window">@lang('lang.same_window')</option>
                        <option value="new_window">@lang('lang.new_window')</option>
                    </select>
                </div>
            </div>
        </div>

</div>