<ul class="p-b-70">

    <!--start-->
    <li>
        <a class="{{ $page['inner_menu_start'] ?? '' }}"
            href="{{ url('app-admin/frontend/start') }}">@lang('lang.start')</a>
    </li>

    <!--payment_gateways-->
    <li class="group-menu-wrapper {{ $page['inner_group_menu_theme'] ?? '' }}">
        <a class="inner-menu-item {{ $page['inner_group_menu_theme'] ?? '' }}" href="javascript:void(0);"
            aria-expanded="false">@lang('lang.theme')</a>
        <ul aria-expanded="false" class="hidden">
            <!--logo-->
            <li>
                <a class="{{ $page['inner_menu_logo'] ?? '' }}"
                    href="{{ url('app-admin/frontend/logo') }}">@lang('lang.logo')</a>
            </li>
            <!--metat tags-->
            <li>
                <a class="{{ $page['inner_menu_metatags'] ?? '' }}"
                    href="{{ url('app-admin/frontend/metatags') }}">@lang('lang.meta_tags')</a>
            </li>
            <!--code-->
            <li>
                <a class="{{ $page['inner_menu_customcode'] ?? '' }}"
                    href="{{ url('app-admin/frontend/customcode') }}">@lang('lang.code')</a>
            </li>
        </ul>
    </li>

    <!--top menu-->
    <li>
        <a class="{{ $page['inner_menu_main_menu'] ?? '' }}"
            href="{{ url('app-admin/frontend/mainmenu') }}">@lang('lang.main_menu')</a>
    </li>

    <!--hero header-->
    <li>
        <a class="{{ $page['inner_menu_hero'] ?? '' }}"
            href="{{ url('app-admin/frontend/hero') }}">@lang('lang.hero_header')</a>
    </li>

    <!--home-->
    <li>
        <a class="{{ $page['inner_menu_section_home'] ?? '' }}"
            href="{{ url('app-admin/frontend/section/1/list') }}">@lang('lang.home_features')</a>
    </li>

    <!--pricing-->
    <li>
        <a class="{{ $page['inner_menu_pricing'] ?? '' }}"
            href="{{ url('app-admin/frontend/pricing') }}">@lang('lang.pricing')</a>
    </li>

    <!--contact us-->
    <li>
        <a class="{{ $page['inner_menu_contact'] ?? '' }}"
            href="{{ url('app-admin/frontend/contact') }}">@lang('lang.contact_us')</a>
    </li>

    <!--signup-->
    <li>
        <a class="{{ $page['inner_menu_signup'] ?? '' }}"
            href="{{ url('app-admin/frontend/signup') }}">@lang('lang.sign_up')</a>
    </li>

    <!--login-->
    <li>
        <a class="{{ $page['inner_menu_login'] ?? '' }}"
            href="{{ url('app-admin/frontend/login') }}">@lang('lang.log_in')</a>
    </li>

    <!--FAQ-->
    <li>
        <a class="{{ $page['inner_menu_faq'] ?? '' }}" href="{{ url('app-admin/frontend/faq') }}">@lang('lang.faq')</a>
    </li>

    <!--PAGES-->
    <li>
        <a class="{{ $page['inner_menu_pages'] ?? '' }}"
            href="{{ url('app-admin/frontend/pages') }}">@lang('lang.pages')</a>
    </li>

    <!--footer-->
    <li>
        <a class="{{ $page['inner_menu_footer'] ?? '' }}"
            href="{{ url('app-admin/frontend/footer') }}">@lang('lang.footer')</a>
    </li>


    <!--footer cta-->
    <li>
        <a class="{{ $page['inner_menu_footercta'] ?? '' }}"
            href="{{ url('app-admin/frontend/footercta') }}">@lang('lang.footer_cta')</a>
    </li>
</ul>