"use strict";


$(document).ready(function () {

    /**-------------------------------------------------------------
     * CANCEL ACCOUNT
     * ------------------------------------------------------------*/
    $(document).on('click', '#cancel_my_subscription_button', function () {

        //hide the button
        $("#cancel_my_subscription_button_container").hide();

        //show confirm panel
        $("#cancel_account_last_step").show();

    });

});