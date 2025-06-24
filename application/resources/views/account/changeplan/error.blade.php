<div class="w-100 text-center">


    <img class="m-t-30 w-px-250" src="{{ url('public/images/saas/warning.png') }}" alt="@lang('lang.change_plan')" />

    <div class="m-l-30 m-r-30 m-t-30 m-b-40">
        <div class="alert alert-danger">
            @lang('lang.change_plan_limits_error')
        </div>
        <div class="alert alert-info">
            @lang('lang.change_plan_limits_error_2')
        </div>
        <div class="m-l-auto m-r-auto m-b-o m-t-20">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>@lang('lang.feature')</th>
                            <th>@lang('lang.plan_limits')</th>
                            <th>@lang('lang.current_usage')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 0; $i < count($over_limits); $i++)
                        <tr>
                            <td>{{ $over_limits[$i]['feature'] }}</td>
                            <td>{{ $over_limits[$i]['limits'] }}</td>
                            <td>{{ $over_limits[$i]['usage'] }}</td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>