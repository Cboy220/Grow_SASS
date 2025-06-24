<!--offline payment details -->
<div id="offline_payment_details">

    {!! _clean($landlord_settings->settings_offline_payments_details) !!}

</div>


<div class="modal-selector m-t-40" id="proof_of_payment_container">

    <div class="m-b-30">{!! _clean($landlord_settings->settings_offline_proof_of_payment_message) !!}</div>

    <!--fileupload-->
    <div class="form-group row">
        <div class="col-12">
            <div class="dropzone dz-clickable" id="fileupload_proof_of_payment"
            data-upload-url="{{ url('settings/account/proof-of-payment') }}">
                <div class="dz-default dz-message">
                    <i class="icon-Upload-toCloud"></i>
                    <span>@lang('lang.attach_proof_of_payment')</span>
                </div>
            </div>
        </div>
    </div>
    <!--fileupload-->
</div>


<div class="modal-selector m-t-40 p-t-60 p-b-60 text-center hidden" id="proof_of_payment_thankyou">

    {!! _clean($landlord_settings->settings_offline_proof_of_payment_thank_you) !!}

</div>

<!--GENERAL CHECKOUT JS-->
<script src="/public/js/landlord/frontend/offline.js?v={{ config('system.versioning') }}"></script>