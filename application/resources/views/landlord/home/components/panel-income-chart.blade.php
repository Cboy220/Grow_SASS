<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex m-b-30">
                    <h5 class="card-title m-b-0 align-self-center">{{ cleanLang(__('lang.payments')) }}</h5>
                    <div class="ml-auto align-self-center">
                        <ul class="list-inline font-12">
                            <li><span class="label label-success label-rounded"><i class="fa fa-circle"></i>
                                    {{ $payload['income']['year'] }}</span></li>
                        </ul>
                    </div>
                </div>
                <div class="incomeexpenses saasincome campaign ct-charts" id="admin-dhasboard-income-vs-expenses"></div>
                <div class="row text-center">
                    <div class="col-lg-6 col-md-6 m-t-20">
                        <h2 class="m-b-0 font-light">{{ $payload['income']['year'] }}</h2>
                        <small>{{ cleanLang(__('lang.period')) }}</small>
                    </div>
                    <div class="col-lg-6 col-md-6 m-t-20">
                        <h2 class="m-b-0 font-light">{{ runtimeMoneyFormat($payload['income']['total']) }}</h2>
                        <small>{{ cleanLang(__('lang.income')) }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!--[DYNAMIC INLINE SCRIPT] - Backend Variables to Javascript Variables-->
<script>
    NX.saas_home_chart_income = JSON.parse('{!! json_encode(_clean($payload["income"]["monthly"])) !!}', true);
</script>

<script src="public/js/landlord/dynamic/home.income.stats.js?v={{ config('system.versioning') }}"></script>