
    <!--CPANEL API-->
    <div class="panel-contrast-3 m-b-20 hidden setup_database_options" id="setup_database_cpanel_api">
        <form class="form-horizontal">


            <!--username-->
            <div class="form-group row">
                <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">Cpanel Username</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="database_config_cpanel_username"
                        name="database_config_cpanel_username" value="">
                </div>
            </div>


            <!--password-->
            <div class="form-group row">
                <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">Cpanel API Key</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="database_config_cpanel_api_key"
                        name="database_config_cpanel_api_key" value="">
                </div>
            </div>

            <!--cpanel URL-->
            <div class="form-group row">
                <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">Cpanel URL</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="database_config_cpanel_api_url"
                        name="database_config_cpanel_api_url" value="{{ cpanelURL() }}">
                </div>
            </div>

            <!--server host-->
            <div class="form-group row">
                <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">MySQL Host</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="database_config_cpanel_host"
                        name="database_config_cpanel_host" value="localhost">
                </div>
            </div>

            <!--server port-->
            <div class="form-group row">
                <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">MySQL Port</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="database_config_cpanel_port"
                        name="database_config_cpanel_port" value="3306">
                </div>
            </div>

            <!--continue-->
            <div class="x-button text-right p-t-20">
                <button class="btn waves-effect waves-light btn-info btn-extra-padding ajax-request"
                    data-button-loading-annimation="yes" data-button-disable-on-click="yes" data-type="form"
                    data-ajax-type="post" data-form-id="setup_database_cpanel_api" id="continueButton" type="submit"
                    data-url="{{ url('install/database/cpanel') }}">Continue</button>
            </div>
        </form>
    </div>