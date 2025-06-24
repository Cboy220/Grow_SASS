<!--MYSQL USER-->
<div class="panel-contrast-3 m-b-20 hidden setup_database_options" id="setup_database_mysql_user">
    <form class="form-horizontal">

        <!--chrome workaround prevent autofill (as of dec 2016)-->
        <div class="fx-fake-login">
            <input type="text" name="username_remembered">
            <input type="password" name="password_remembered">
        </div>

        <!--username-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">MySQL Username</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="database_config_mysql_username"
                    name="database_config_mysql_username" value="">
            </div>
        </div>


        <!--password-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">MySQL Password</label>
            <div class="col-sm-12 col-lg-9">
                <input type="password" class="form-control form-control-sm" id="database_config_mysql_password"
                    name="database_config_mysql_password" value="">
            </div>
        </div>

        <!--server host-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">MySQL Host</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="database_config_mysql_host"
                    name="database_config_mysql_host" value="localhost">
            </div>
        </div>

        <!--server port-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">MySQL Port</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="database_config_mysql_port"
                    name="database_config_mysql_port" value="3306">
            </div>
        </div>

        <!--database prefix-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">Database Prefix
                (optional)</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="database_config_mysql_prefix"
                    name="database_config_mysql_prefix" value="growcrm_">
            </div>
        </div>

        <!--continue-->
        <div class="x-button text-right p-t-20">
            <button class="btn waves-effect waves-light btn-info btn-extra-padding ajax-request"
                data-button-loading-annimation="yes" data-button-disable-on-click="yes" data-type="form"
                data-ajax-type="post" data-form-id="setup_database_mysql_user" id="continueButton" type="submit"
                data-url="{{url('install/database/mysql') }}">Continue</button>
        </div>
    </form>
</div>