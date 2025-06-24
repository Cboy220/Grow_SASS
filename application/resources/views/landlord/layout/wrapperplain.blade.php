<!DOCTYPE html>
<html lang="en" class="app-admin logged-out">

<!--html header-->
@include('landlord.layout.header')
<!--html header-->

<body class="{{ $page['page'] ?? '' }}">
    <!--preloader-->
    <div class="preloader">
        <div class="loader">
            <div class="loader-loading"></div>
        </div>
    </div>
    <!--preloader-->

    <!--main content-->
    <div id="main-wrapper">
        @yield('content')
    </div>
</body>

@include('landlord.layout.footerjs')
</html>