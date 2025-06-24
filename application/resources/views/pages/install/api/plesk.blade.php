<!--PLESK API-->
<div class="panel-contrast-3 m-b-20 hidden setup_database_options" id="setup_database_plesk_api">
    <form class="form-horizontal">


        <!--chrome workaround prevent autofill (as of dec 2016)-->
        <div class="fx-fake-login">
            <input type="text" name="username_remembered">
            <input type="password" name="password_remembered">
        </div>

        <!--admin username-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-4 text-left control-label col-form-label">Plesk - <strong>(Admin)</strong>
                Username <span class="align-middle text-info font-16" data-toggle="tooltip" title="Plesk API requires the server 'admin' login details"
                data-placement="top"><i class="ti-info-alt"></i></span></label> 
            <div class="col-sm-12 col-lg-8">
                <input type="text" class="form-control form-control-sm" id="database_config_plesk_username"
                    name="database_config_plesk_username" value="">
            </div>
        </div>


        <!--password-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-4 text-left control-label col-form-label">Plesk - <strong>(Admin)</strong>
                Password</label>
            <div class="col-sm-12 col-lg-8">
                <input type="password" class="form-control form-control-sm" id="database_config_plesk_password"
                    name="database_config_plesk_password" value="">
            </div>
        </div>

        <!--plesk domain-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-4 text-left control-label col-form-label">Plesk - Login URL</label>
            <div class="col-sm-12 col-lg-8">
                <input type="text" class="form-control form-control-sm" id="database_config_plesk_url"
                    name="database_config_plesk_url" value="">

                <div class="alert alert-info m-t-10 m-b-0">
                    <h5 class="text-info"><i class="sl-icon-info"></i> Plesk URL Example</h5>
                    https://web-hosting-server-address:8443
                </div>
            </div>
        </div>

        <!--plesk domain-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-4 text-left control-label col-form-label">Plesk - Account Domain</label>
            <div class="col-sm-12 col-lg-8">
                <input type="text" class="form-control form-control-sm" id="database_config_plesk_domain"
                    name="database_config_plesk_domain" value="{{ pleskDomain() }}">
            </div>
        </div>

        <!--server host-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-4 text-left control-label col-form-label">MySQL Host</label>
            <div class="col-sm-12 col-lg-8">
                <input type="text" class="form-control form-control-sm" id="database_config_plesk_host"
                    name="database_config_plesk_host" value="localhost">
            </div>
        </div>

        <!--database prefix-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-4 text-left control-label col-form-label">Database Prefix
                (optional)</label>
            <div class="col-sm-12 col-lg-8">
                <input type="text" class="form-control form-control-sm" id="database_config_plesk_prefix"
                    name="database_config_plesk_prefix" value="growcrm_">
            </div>
        </div>

        <!--server port-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-4 text-left control-label col-form-label">MySQL Port</label>
            <div class="col-sm-12 col-lg-8">
                <input type="text" class="form-control form-control-sm" id="database_config_plesk_port"
                    name="database_config_plesk_port" value="3306">
            </div>
        </div>

        <!--continue-->
        <div class="x-button text-right p-t-20">
            <button class="btn waves-effect waves-light btn-info btn-extra-padding ajax-request"
                data-button-loading-annimation="yes" data-button-disable-on-click="yes" data-type="form"
                data-ajax-type="post" data-form-id="setup_database_plesk_api" id="continueButton" type="submit"
                data-url="{{ url('install/database/plesk') }}">Continue</button>
        </div>
    </form>
</div>