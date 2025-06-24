    <!-- Row -->
    <div class="row">
        <!-- Column -->
        <div class="col-12">
            <div class="card">
                <div class="card-body dynamic-content-container">

                    <h5 class="card-title m-b-30 align-self-center">{{ cleanLang(__('lang.events')) }}</h5>

                    <div id="dynamic-content-container">
                        <!--dynamic events-->
                    </div>

                    <!--load more button-->
                    @include('landlord.misc.load-more-dynamic-button')
                </div>
            </div>
        </div>
        <!-- Column -->
    </div>
    <!-- Row -->


    <!--dynamically load timeline events-->
    <script src="public/js/landlord/dynamic/timeline.js?v={{ config('system.versioning') }}"
        id="dynamic-load-timeline-events" data-loading-target="dynamic-content-container" data-progress-bar="hidden"
        data-url="{{ url('app-admin/events?ref-source=home') }}">
    </script>