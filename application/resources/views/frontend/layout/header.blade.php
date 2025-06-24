<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" id="meta-csrf" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @if(isset($page['meta_description']))
    <meta name="description" content="{{ $page['meta_description'] }}">
    @else
    <meta name="description" content="{{ config('system.settings_code_meta_description') }}">
    @endif

    @if(isset($page['meta_title']))
    <title>{{ $page['meta_title'] }}</title>
    @else
    <title>{{ config('system.settings_code_meta_title') }}</title>
    @endif

    <!--BASEURL-->
    <base href="{{ url('/') }}" target="_self">

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

    <link rel='stylesheet' id='saspik-fonts-css'
        href='https://fonts.googleapis.com/css?family=Poppins%3A300%2C400%2C500%2C600%2C700%2C800%2C900&#038;subset'
        type='text/css' media='all' />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="public/themes/frontend/assets/css/bootstrap.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="public/themes/frontend/assets/css/fontawesome.all.min.css">

    <!-- Mobile Menu CSS -->
    <link rel="stylesheet" href="public/themes/frontend/assets/plugins/meanmenu/meanmenu.min.css">


    <!-- Main css -->
    <link rel="stylesheet" href="public/themes/frontend/assets/css/style.css">

    <!-- Custom css -->
    <link rel="stylesheet" href="public/themes/frontend/assets/css/custom.css">

    <!-- Vendor css -->
    <link rel="stylesheet" href="public/themes/frontend/assets/css/vendor.css">

    <!-- Simple Line icons -->
    <link rel="stylesheet" href="public/themes/frontend/assets/fonts/simplelineicons/css/simple-line-icons.css">


    <!-- Simple Line icons -->
    <link rel="stylesheet"
        href="public/themes/frontend/assets/fonts/material-design-iconic-font/material-design-icons.css">

    <!-- jQuery JS -->
    <script src="public/themes/frontend/assets/js/jquery.min.js"></script>


    <!--[if lt IE 9]>
            <script src="https://www.microsoft.com/en-us/download/details.aspx?id=38270"></script>
        <![endif]-->

    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16"
        href="storage/logos/app/{{ config('system.settings_favicon_frontend_filename') }}">

    <!--SET DYNAMIC VARIABLE IN JAVASCRIPT-->
    <script type="text/javascript">
        //name space & settings
        NX = (typeof NX == 'undefined') ? {} : NX;
        NXJS = (typeof NXJS == 'undefined') ? {} : NXJS;
        NXLANG = (typeof NXLANG == 'undefined') ? {} : NXLANG;
        NXINVOICE = (typeof NXINVOICE == 'undefined') ? {} : NXINVOICE;
        NX.data = (typeof NX.data == 'undefined') ? {} : NX.data;

        NXINVOICE.DATA = {};
        NXINVOICE.DOM = {};
        NXINVOICE.CALC = {};

        //variables
        NX.site_url = "{{ url('/') }}";
        NX.csrf_token = "{{ csrf_token() }}";
        NX.notification_position = "{{ config('settings.notification_position') }}";
        NX.notification_error_duration = "{{ config('settings.notification_error_duration') }}";
        NX.notification_success_duration = "{{ config('settings.notification_success_duration') }}";

        //javascript console debug modes
        NX.debug_javascript = "{{ config('app.debug_javascript') }}";

        //popover template
        NX.basic_popover_template = '<div class="popover card-popover" role="tooltip">' +
            '<span class="popover-close" onclick="$(this).closest(\'div.popover\').popover(\'hide\');" aria-hidden="true">' +
            '<i class="ti-close"></i></span>' +
            '<div class="popover-header"></div><div class="popover-body" id="popover-body"></div></div>';

        //lang - used in .js files
        NXLANG.delete_confirmation = "{{ cleanLang(__('lang.delete_confirmation')) }}";
        NXLANG.are_you_sure_delete = "{{ cleanLang(__('lang.are_you_sure_delete')) }}";
        NXLANG.cancel = "{{ cleanLang(__('lang.cancel')) }}";
        NXLANG.continue = "{{ cleanLang(__('lang.continue')) }}";
        NXLANG.are_you_sure = "{{ cleanLang(__('lang.are_you_sure')) }}";
        NXLANG.ok = "{{ cleanLang(__('lang.ok')) }}";
        NXLANG.cancel = "{{ cleanLang(__('lang.cancel')) }}";
        NXLANG.close = "{{ cleanLang(__('lang.close')) }}";

        //arrays to use generically
        NX.array_1 = [];
        NX.array_2 = [];
        NX.array_3 = [];
        NX.array_4 = [];
    </script>

    <!--XDYANMIC TRUSTED CONTENT - No sanitizing required] for this trusted content (Google reCAPTCHA)-->
    @if(@config('system.settings_captcha_status') == 'enabled')
    {!! htmlScriptTagJsApi([]) !!}
    @endif

    <!--customer header code-->
    {!! _clean(config('system.settings_code_head')) !!}
</head>