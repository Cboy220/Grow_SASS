<div class="setup-welcome" id="setup-welcome">

    <!--image-->
    <div class="x-image">
        <img src="public/images/wizard.png">
    </div>

    <div class="x-subtitle p-t-20">
        For installation instructions, please refer the the instruction manual included in the package zip file.</a>.
        </br>
    </div>

    <!--item-->
    <div class="form-group row">
        <label class="col-sm-12 control-label col-form-label required">Product Purchase Code</label>
        <div class="col-sm-12">
            <input type="text" class="form-control form-control-sm" name="purchase_code"
                value="">
        </div>
    </div>

    <div class="alert alert-info">Your product purchase code is available inside your Envato (Codecanyon) dashboard. 
        <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">More Details</a></div>

       
    <div class="x-button m-t-30">
        <button type="button" class="btn waves-effect waves-light btn-block btn-info js-ajax-request"
            data-button-loading-annimation="yes" data-button-disable-on-click="yes"
            data-type="form" 
            data-form-id="setup-welcome" 
            data-ajax-type="post"
            data-url="{{ url('install/requirements') }}">Start Installation</button>
    </div>

</div>