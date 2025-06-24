<ul class="nav nav-tabs customtab" role="tablist">


    <li class="nav-item"> <a class="nav-link {{ $page['inner_menu_tab_section_1'] ?? '' }}"
            href="{{ url('app-admin/frontend/section/1/list') }}" role="tab">@lang('lang.section_1')</a> </li>

    <li class="nav-item"> <a class="nav-link {{ $page['inner_menu_tab_section_2'] ?? '' }}"
            href="{{ url('app-admin/frontend/section/section-2/feature') }}" role="tab">@lang('lang.section_2')</a>
    </li>

    <li class="nav-item"> <a class="nav-link {{ $page['inner_menu_tab_section_3'] ?? '' }}"
            href="{{ url('app-admin/frontend/section/section-3/cta') }}" role="tab">@lang('lang.section_3')</a>
    </li>

    <li class="nav-item"> <a class="nav-link {{ $page['inner_menu_tab_section_4'] ?? '' }}"
            href="{{ url('app-admin/frontend/section/section-4/feature') }}" role="tab">@lang('lang.section_4')</a>
    </li>

    <li class="nav-item"> <a class="nav-link {{ $page['inner_menu_tab_section_5'] ?? '' }}"
            href="{{ url('app-admin/frontend/section/5/list') }}" role="tab">@lang('lang.section_5')</a>
    </li>

    <li class="nav-item"> <a class="nav-link {{ $page['inner_menu_tab_section_6'] ?? '' }}"
            href="{{ url('app-admin/frontend/section/splash') }}" role="tab">@lang('lang.section_6')</a>
    </li>

</ul>