@extends('landlord.settings.wrapper')
@section('settings_content')

<!--form-->
<div class="card">
    <div class="card-body" id="landlord-settings-form">

        <h5 class="m-b-20">@lang('lang.system_information')</h5>

        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td class="font-weight-400">@lang('lang.crm_version')</td>
                    <td>v{{ $data['crm_version'] }}</td>
                </tr>
                <tr>
                    <td class="font-weight-400">@lang('lang.info_php_version')</td>
                    <td>{{ $data['php_version'] }}</td>
                </tr>
                <tr>
                    <td class="font-weight-400">@lang('lang.info_memory_limit') <span class="align-middle text-info font-16"
                            data-toggle="tooltip" title="@lang('lang.info_server_ini')" data-placement="top"><i
                                class="ti-info-alt"></i></span></td>
                    <td>{{ $data['memory_limit'] }}</td>
                </tr>
                <tr>
                    <td class="font-weight-400">@lang('lang.info_max_upload_size') <span class="align-middle text-info font-16"
                        data-toggle="tooltip" title="@lang('lang.info_server_ini')" data-placement="top"><i
                            class="ti-info-alt"></i></span></td>
                    <td>{{ $data['upload_size_limit'] }}</td>
                </tr>
                <tr>
                    <td class="font-weight-400">@lang('lang.info_landlord_database')</td>
                    <td>{{ $data['landlord_database'] }}</td>
                </tr>
                <tr>
                    <td class="font-weight-400">@lang('lang.info_database_creation_method')</td>
                    <td>{{ db_creation_method($data['mysql_creation_type']) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection