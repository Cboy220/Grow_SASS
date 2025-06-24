<!DOCTYPE html>
<html lang="en">

@include('frontend.layout.header')

<body class="account">

    <!--overlay-->
    <div class="page-wrapper-overlay hidden" data-target="">
        <div class="preloader">
            <div class="loader">
                <div class="loader-loading"></div>
            </div>
        </div>
    </div>
    <!--overlay-->

    <!--LOGO & MENU-->
    @if(config('system.settings_frontend_status') == 'enabled')
    @include('frontend.layout.menu')
    @else
    <header class="heading">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="heading_mobile">
                        <a href="javascript:void(0);" class="heading_logo">
                            <img src="{{ runtimeLogoFrontEnd() }}" alt="">
                        </a>
                        <div class="heading_mobile_thum"></div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    @endif


    <!--main section-->
    <div class="row signup">

        <div class="col-lg-6">
            <div class="x-splash-image">
                <img src="public/themes/frontend/assets/images/signup-splash.png">
            </div>
        </div>
        <div class="col-lg-6">

            @include('frontend.signup.form')

        </div>

    </div>
    
    @include('frontend.signup.terms')
    @include('frontend.layout.footerjs')

</body>

</html>