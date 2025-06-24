"use strict";

$(document).ready(function () {

    /** --------------------------------------------------------------------------------------------------
     *  [install] - toggle database management tyope
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("select2:select", "#database_config_type", function (e) {

        var selection = e.params.data.id;

        //hide all
        $(".setup_database_options").hide();

        //toggle permissions
        if (selection == 'direct') {
            $("#setup_database_mysql_user").show();
        } 
        
        if (selection == 'cpanel') {
            $("#setup_database_cpanel_api").show();
        }

        if (selection == 'plesk') {
            $("#setup_database_plesk_api").show();
        }
    });


    /** --------------------------------------------------------------------------------------------------
     *  [base_domain] - update the example subdomain as a customer is typing
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("input", "#settings_base_domain", function () {
        var new_value = $(this).val();
        //update the example in the notification panel
        $("#settings_base_domain_example_1").html(new_value);
        $("#settings_base_domain_example_2").html(new_value);
    });


    /** --------------------------------------------------------------------------------------------------
     *  [email_domain] - update the example subdomain as a customer is typing
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("input", "#settings_email_domain", function () {
        var new_value = $(this).val();
        //update the example in the notification panel
        $("#settings_email_domain_example").html(new_value);
    });



});