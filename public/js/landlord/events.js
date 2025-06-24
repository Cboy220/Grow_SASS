"use strict";

$(document).ready(function () {


    /** --------------------------------------------------------------------------------------------------
     *  [updates] - checking fo update
     * -------------------------------------------------------------------------------------------------*/
    if ($('#updates-checking').length) {
        nxAjaxUxRequest($('#updates-checking'));
    }


    /** --------------------------------------------------------------------------------------------------
     *  [add package modal] - toggle free plan
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", ".inner-menu-item", function () {
        var parent = $(this).closest(".group-menu-wrapper");
        var child_menu = parent.children('ul')

        //toggle all other menus
        $(".group-menu-wrapper").removeClass('active');
        $(".inner-menu-item ").removeClass('active');
        $(".group-menu-wrapper").find('ul').each(function () {
            $(this).hide();
        });

        //toggle this item
        $(this).addClass('active');
        parent.addClass('active');
        child_menu.toggle();
    });





    /** --------------------------------------------------------------------------------------------------
     *  [add package modal] - toggle free plan
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("change", ".js-toggle-free-plan", function () {
        if ($(this).is(':checked')) {
            $("#payment_options").hide();
        } else {
            $("#payment_options").show();
        }
    });


    /** --------------------------------------------------------------------------------------------------
     *  [signup form] - changed plan
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("select2:select", "#plan, #changed_package_id", function (e) {
        var billing_cycle = $(this).find(':selected').attr('data-option');
        $("#billing_cycle").val(billing_cycle);
        if (billing_cycle == 'free') {
            $("#free_plan_container").hide();
            $("#toggle_subscription_payment_method").hide();
            $("#free_trial").select2({
                minimumResultsForSearch: Infinity,
                allowClear: false,
            }).val('no').trigger("change");
        } else {
            $("#free_plan_container").show();
            $("#toggle_subscription_payment_method").show();
        }
    });


    /** --------------------------------------------------------------------------------------------------
     *  [add package modal] - chaging billing cycle
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("select2:select", "#package_subscription_options", function (e) {
        var subscription_options = e.params.data.id;
        if (subscription_options == 'paid') {
            $(".option-billable").show();
        }
        if (subscription_options == 'free') {
            $(".option-billable").hide();
        }
    });


    /** --------------------------------------------------------------------------------------------------
     *  [add customer modal] - chaging package
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("select2:select", "#tenant_package_id", function (e) {
        var package_subscription_options = e.params.data.title;
        if (package_subscription_options == 'free') {
            $("#package-type-paid").hide();
            $("#package-type-free").show();
        } else {
            $("#package-type-free").hide();
            $("#package-type-paid").show();
        }
    });

    /** --------------------------------------------------------------------------------------------------
     *  [create subscription modal] - chaging payment billing method
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("select2:select", "#subscription_payment_method", function (e) {
        var payment_option = e.params.data.id;
        if (payment_option == 'offline') {
            $("#automatic_payments_toggle").hide();
            $("#offline_payments_toggle").show();
        } else {
            $("#offline_payments_toggle").hide();
            $("#automatic_payments_toggle").show();
        }
    });

    /** --------------------------------------------------------------------------------------------------
     *  [create subscription modal] - free trial toggle
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("select2:select", "#free_trial", function (e) {
        var free_trial = e.params.data.id;
        if (free_trial == 'yes') {
            $("#toggle_trial_date").show();
        } else {
            $("#toggle_trial_date").hide();
        }
    });


    /** --------------------------------------------------------------------------------------------------
     *  [create subscription modal] - billing type
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("select2:select", "#subscription_package_id", function (e) {
        var free_trial = $(this).find(':selected').attr('data-option');
        if (free_trial == 'yes') {
            $("#toggle_trial_date").show();
        } else {
            $("#toggle_trial_date").hide();
        }
    });

    /** --------------------------------------------------------------------------------------------------
     *  [settings - free trial] - toggle free trial yes/no
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("select2:select", "#settings_free_trial", function (e) {
        var settings_free_trial = e.params.data.id;
        if (settings_free_trial == 'no') {
            $("#settings_free_trial_days").val(0);
            $("#settings_free_trial_days").prop('disabled', true);
        } else {
            $("#settings_free_trial_days").prop('disabled', false);
        }
    });



    /** --------------------------------------------------------------------------------------------------
     *  add main menumodal] - menu type changed
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("select2:select", "#landlord_main_menu_selector", function (e) {
        var menu_type = e.params.data.id;

        //reset menu
        $('.landlord-main-menu-types').hide();

        if (menu_type == 'internal') {
            $("#link_type_internal").show();
        }
        if (menu_type == 'manual') {
            $("#link_type_manual").show();
            $("#link_type_manual_target").show();
        }
    });


    /** ----------------------------------------------------------
     *   adding a subscription payments - options
     * -----------------------------------------------------------*/
    $(document).on('change', '#subscription_renewal_options', function () {
        if ($(this).is(':checked')) {
            $("#renewal_options_container").show();
        } else {
            $("#renewal_options_container").hide();
        }
    });

    $(document).on("select2:select", "#subscription_status", function (e) {
        var selection = e.params.data.id;
        //toggle permissions
        if (selection != 'active') {
            $('#subscription_renewal_pickadate').val('');
            $('#subscription_renewal_date').val('');
            $('#subscription_renewal_period').val('');
            $('#subscription_renewal_period').trigger("change");
        }
    });

    $(document).on("select2:select", "#subscription_renewal_period", function (e) {
        var selection = e.params.data.id;
        //toggle permissions
        switch (selection) {
            case 'one_week_from_today':
                $('#subscription_renewal_date').val($(this).attr('data-date-week-mysql'));
                $('#subscription_renewal_pickadate').datepicker({
                    format: NX.date_picker_format,
                    language: "lang",
                    autoclose: true,
                    class: "datepicker-default",
                    todayHighlight: true
                }).datepicker("update", $(this).attr('data-date-week-picker'));
                break;
            case 'one_month_from_today':
                $('#subscription_renewal_date').val($(this).attr('data-date-month-mysql'));
                $('#subscription_renewal_pickadate').datepicker({
                    format: NX.date_picker_format,
                    language: "lang",
                    autoclose: true,
                    class: "datepicker-default",
                    todayHighlight: true
                }).datepicker("update", $(this).attr('data-date-month-picker'));
                break;
            case 'one_year_from_today':
                $('#subscription_renewal_date').val($(this).attr('data-date-year-mysql'));
                $('#subscription_renewal_pickadate').datepicker({
                    format: NX.date_picker_format,
                    language: "lang",
                    autoclose: true,
                    class: "datepicker-default",
                    todayHighlight: true
                }).datepicker("update", $(this).attr('data-date-year-picker'));
                break;
            default:
                $('#subscription_renewal_pickadate').val('');
                $('#subscription_renewal_date').val('');
        }

    });




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
        } else {
            $("#setup_database_cpanel_api").hide();
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

    /** --------------------------------------------------------------------------------------------------
     *  [email_domain] - why is this needed toggle
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", "#why_is_this_needed_question", function () {
        if ($("#why_is_this_needed_answer").is(":visible")) {
            $(".email_questions_answers").hide();
        } else {
            $(".email_questions_answers").hide();
            $("#why_is_this_needed_answer").show();
        }
    });
    $(document).on("click", "#how_can_i_automate_question", function () {
        if ($("#how_can_i_automate_answer").is(":visible")) {
            $(".email_questions_answers").hide();
        } else {
            $(".email_questions_answers").hide();
            $("#how_can_i_automate_answer").show();
        }
    });



    /** --------------------------------------------------------------------------------------------------
     *  [pages editor] - checking fo update
     * -------------------------------------------------------------------------------------------------*/
    if ($('#landlord_pages_editor').length) {
        nxTinyMCEExtended(800, '.tinymce-textarea-extended');
    }


    //create the page slug (permalink) when the title is being typed
    $("#pages_page_title").on("input", function () {
        //only do this when we are creating a page for the first time
        if ($(this).attr('data-mode') == 'create') {
            var title = $(this).val();
            var slug = generatePageSlug(title);
            $("#page_permanent_link").val(slug);
        }

        //update meta title and content
        if ($(this).attr('data-mode') == 'create') {
            $("#page_meta_title").val(title);
            $("#page_meta_description").val(title);
        }

    });

});


//function to generate the slug from the page title
function generatePageSlug(input) {
    return input
        //convert to lowercase
        .toLowerCase()
        //replace whitespace with dashes
        .replace(/\s+/g, '-')
        //remove non-alphanumeric characters except dashes
        .replace(/[^\w-]+/g, '')
        //replace double dashes with single dash
        .replace(/-+/g, '-');
}