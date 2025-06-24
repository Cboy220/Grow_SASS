"use strict";


$(document).ready(function () {

    /** --------------------------------------------------------------------------------------------------
     *  [select payment method]
     * -------------------------------------------------------------------------------------------------*/
    $(document).off("select2:select", "#payment_now_method_selector").on("select2:select", "#payment_now_method_selector", function (e) {

        //get the selected payment gateway
        var selection = $(this).find(':selected').val();


        $("#payment_now_placeholder_button").addClass('button-loading-annimation');
        $("#payment_now_placeholder_button").html(NXLANG.please_wait);

        /*---------------------------------------------------------------------
         * chnage the data url to match wed route for the selected gateway
         * (e.g. settings/account/pay/paypal or settings/account/pay/stripe)
         *--------------------------------------------------------------------*/
        var base_url = $("#payment_now_selector_container").attr('data-base-url');
        var data_url = base_url + '/' + selection;
        $("#payment_now_selector_container").attr('data-url', data_url);

        //make the request
        nxAjaxUxRequest($("#payment_now_selector_container"));
    });


});