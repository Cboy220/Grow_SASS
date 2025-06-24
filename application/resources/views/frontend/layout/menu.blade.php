    <!-- HEADING AREA START -->
    <header class="heading" id="frontend-top-menu">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-4">
                    <div class="heading_mobile">
                        <a href="/" class="heading_logo">
                            <img src="{{ runtimeLogoFrontEnd() }}" alt="">
                        </a>
                        <div class="heading_mobile_thum"></div>
                    </div>
                </div>
                <div class="col-md-9 col-8 text-right">
                    <nav class="heading_menu">
                        <ul>
                            @foreach($mainmenu as $menu)
                            <li class="heading_menu_list">
                                <a href="{{ $menu->frontend_data_2 }}"
                                    target="{{ saasLinktarget($menu->frontend_data_6) }}"
                                    class="heading_menu_list_links {{ runtimeFrontendMenuSignup($menu->frontend_data_2) }}">{{ $menu->frontend_data_1 }}</a></li>
                            @endforeach
                        </ul>
                    </nav>
                </div>
            </div>

            <!--nav-->
            <a href="javascript:void(0);" class="mobile-menu-icon" id="mobile-menu-icon">
                <i class="sl-icon-menu"></i>
            </a>
        </div>

        <!--mobile menu-->
        <div class="mobile-menu-container">
            <div class="mobile-menu hidden" id="mobile-menu">
                <ul>
                    @foreach($mainmenu as $menu)
                    <li class="heading_menu_list">
                        <a href="{{ $menu->frontend_data_2 }}" target="{{ saasLinktarget($menu->frontend_data_6) }}"
                            class='heading_menu_list_links'>{{ $menu->frontend_data_1 }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </header>
    <!-- HEADING AREA END -->