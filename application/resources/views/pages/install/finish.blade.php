<div class="setup-welcome">

    <!--image-->
    <div class="x-image">
        <img src="public/images/success.png">
    </div>

    <!--title-->
    <div class="x-title">
        <h2>Congratulations!!</h2>
    </div>

    <div class="x-subtitle">
        Setup is now complete. <br/></br/><strong>IMPORTANT - </strong> You must now complete the following steps, inside your web hosting control panel.
    </div>

    <div class="alert alert-warning p-b-15">
        <h3 class="text-warning"><i class="fa fa-exclamation-triangle"></i> (1) Final Steps - Cron Jobs</h3>
        <div class="m-b-20 m-t-10">Use your web hosting control panel to create <strong>both</strong> of the Cron Jobs shown below. 
            <a href="https://growcrm.io/documentation/saas-installation" target="_blank">[help]</a></div>
        <div class="cronjob m-b-20">
            <input class="col-12 form-control form-control-sm" type="text" value="{{ $cron_path_1 ?? '---' }}" disabled>
        </div>
        <div class="cronjob m-t-10 m-b-10">
            <input class="col-12 form-control form-control-sm" type="text" value="{{ $cron_path_2 ?? '---'}}" disabled>
        </div>
    </div>


    <div class="alert alert-warning m-t-40 p-b-15">   
        <h3 class="text-warning"><i class="fa fa-exclamation-triangle"></i> (2) Final Steps - Wildcard Domain</h3>
        <div class="m-b-20 m-t-10">Use your web hosting control panel to create <strong>wildcard domain</strong> below.
            <a href="https://growcrm.io/documentation/saas-installation" target="_blank">[help]</a></div>
        <div class="cronjob m-t-10 m-b-10">
            <input class="col-12 form-control form-control-sm" type="text" value="{{ $wildcard_domain ?? '---'}}" disabled>
        </div>
    </div>


    <div class="x-button m-t-20">
        <a href="{{ url('/app-admin') }}" class="btn waves-effect waves-light btn-block btn-info">Go To My Dashboard</a>
    </div>


</div>