<!--ALL THIRD PART JAVASCRIPTS-->
<script src="public/vendor/js/vendor.footer.js?v={{ config('system.versioning') }}"></script>

<!--nextloop.core.js-->
<script src="public/js/core/ajax.js?v={{ config('system.versioning') }}"></script>

<!--MAIN JS - AT END-->
<script src="public/js/core/boot.js?v={{ config('system.versioning') }}"></script>

<!--EVENTS-->
<script src="public/js/core/events.js?v={{ config('system.versioning') }}"></script>

<!--CORE-->
<script src="public/js/core/app.js?v={{ config('system.versioning') }}"></script>

<!--SAAS EVENTS-->
<script src="public/js/landlord/events.js?v={{ config('system.versioning') }}"></script>
<script src="public/js/landlord/app.js?v={{ config('system.versioning') }}"></script>


<!--bootstrap-timepicker-->
<script type="text/javascript" src="/public/vendor/js/bootstrap-timepicker/bootstrap-timepicker.js?v={{ config('system.versioning') }}">
</script>

<!--calendaerfull js [v6.1.13]-->
<script src="/public/vendor/js/fullcalendar/index.global.min.js?v={{ config('system.versioning') }}"></script>

<!--flash messages-->
@if (Session::has('success-notification'))
<span id="js-trigger-session-message" data-type="success"
    data-message="{{ Session::get('success-notification') }}"></span>
@endif
@if (Session::has('error-notification'))
<span id="js-trigger-session-message" data-type="warning"
    data-message="{{ Session::get('error-notification') }}"></span>
@endif