"use strict";


/**--------------------------------------------------------------------------------------
 * select2 defaults
 * -------------------------------------------------------------------------------------*/
$.fn.select2.defaults.set("theme", "bootstrap");
$.fn.select2.defaults.set("containerCssClass", ":all:");
$.fn.select2.defaults.set("width", null);
$.fn.select2.defaults.set("maximumSelectionSize", 6);
$.fn.select2.defaults.set("allowClear", true);
$.fn.select2.defaults.set("placeholder", ""); //we must have something to for allowClear to work


/**--------------------------------------------------------------------------------------
 * validator defaults
 * -------------------------------------------------------------------------------------*/
$.validator.setDefaults({
    errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) {
            //radios and checkbox
            error.insertAfter(element.parent());
        } else if (element.hasClass('select2-hidden-accessible')) {
            //select 2 dropdowns - add error class to rendered child element
            element.next('span').addClass('error').removeClass('valid');
        } else {
            //regular input field - add error class
            element.addClass('error').removeClass('valid');
        }
    }
});


//basic select2 - no search box
$(".select2-basic").select2({
    minimumResultsForSearch: Infinity,
    allowClear: false, //use data-allow-clear="true" to change this in the html
});


//basic select2 - with search box
$(".select2-basic-with-search").select2({
    minimumResultsForSearch: 1
});

//select2 simple ajax
$(".js-select2-basic-search").select2({
    theme: "bootstrap",
    width: null,
    containerCssClass: ':all:',
    minimumInputLength: 1,
    minimumResultsForSearch: 1,
    ajax: {
        dataType: "json",
        type: "GET",
        data: function (params) {
            var queryParameters = {
                term: params.term
            }
            return queryParameters;
        },
        processResults: function (data) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.value,
                        id: item.id
                    }
                })
            };
        }
    }
});

//select 2 multiple tags - with search
$(".js-select2-tags-search").select2({
    theme: "bootstrap",
    width: null,
    containerCssClass: ':all:',
    tags: true,
    multiple: true,
    tokenSeparators: [',', ' '],
    minimumInputLength: 1,
    minimumResultsForSearch: 1,
    ajax: {
        dataType: "json",
        type: "GET",
        data: function (params) {
            var queryParameters = {
                term: params.term
            }
            return queryParameters;
        },
        processResults: function (data) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.value,
                        id: item.id
                    }
                })
            };
        }
    }
});

/*
 * select2 simple ajax (copy of above, but for modals)
 * this is a fix for select2 dropdowns not working in bootstrap modal
 * added parent modal -- dropdownParent: $("#commonModal"),
 */
$(".js-select2-basic-search-modal").select2({
    theme: "bootstrap",
    width: null,
    containerCssClass: ':all:',
    minimumInputLength: 1,
    minimumResultsForSearch: 1,
    ajax: {
        dataType: "json",
        type: "GET",
        data: function (params) {
            var queryParameters = {
                term: params.term
            }
            return queryParameters;
        },
        processResults: function (data) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.value,
                        id: item.id
                    }
                })
            };
        }
    }
});

//select 2 tags
$(".select2-tags").select2({
    theme: "bootstrap",
    width: null,
    containerCssClass: ':all:',
    tags: true,
    multiple: true,
    tokenSeparators: [',', ' '],
});

//select 2 tags (with spaces)
$(".select2-tags-with-spaces").select2({
    theme: "bootstrap",
    width: null,
    containerCssClass: ':all:',
    tags: true,
    multiple: true,
    tokenSeparators: [','],
});

//select 2 tags
$(".select2-new-options").select2({
    theme: "bootstrap",
    width: null,
    containerCssClass: ':all:',
    tags: true,
});

//select 2 (with text field to add on text)
$(".select2-combo").select2({
    width: null,
    containerCssClass: ':all:',
    tags: true,
});

//select 2 (with text field to add on text)
$(".select2-combo").select2({
    width: null,
    tags: true,
});

//select 2 (with text field to add on text)
$(".select2-with-text-input").select2({
    tags: true
});


$(document).ready(function () {



    /**--------------------------------------------------------------------------------------
     * [HIDE LOADER]
     * @description: form validation
     * -------------------------------------------------------------------------------------*/
    $(".page-wrapper-overlay").hide();


    /** ----------------------------------------------------------
     *  preselect any select2 dropwnd
     * - the dropdown must have a class [select2-preselected]
     * - the dropdown must have an attr [data-preselected='foo']
     * ---------------------------------------------------------*/
    $(document).find(".select2-preselected").each(function () {
        var preselected = $(this).attr('data-preselected');
        if (preselected != '') {
            $(this).val(preselected);
            $(this).trigger('change');
        }
    });


    /**--------------------------------------------------------------------------------------
     * [SIGNUP FORM]
     * @description: form validation
     * -------------------------------------------------------------------------------------*/
    if ($("#signup-form").length) {
        $("#signup-form").validate({
            rules: {
                package_name: "required",
                package_limits_clients: "required",
                package_limits_projects: "required",
                package_limits_team: "required",
            },
            submitHandler: function (form) {
                nxAjaxUxRequest($("#accountSignupButton"));
            }
        });
    }



    /**--------------------------------------------------------------------------------------
     * [SIGNUP FORM]
     * @description: form validation
     * -------------------------------------------------------------------------------------*/
    if ($("#signup-form").length) {
        $("#signup-form").validate({
            rules: {
                package_name: "required",
                package_limits_clients: "required",
                package_limits_projects: "required",
                package_limits_team: "required",
            },
            submitHandler: function (form) {
                nxAjaxUxRequest($("#accountSignupButton"));
            }
        });
    }


    /** --------------------------------------------------------------------------------------------------
     *  [signup form] - changed plan
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("select2:select", "#plan", function (e) {
        var billing_cycle = $(this).find(':selected').attr('data-option');
        $("#billing_cycle").val(billing_cycle);
    });


    /** --------------------------------------------------------------------------------------------------
     *  [homepage splash]
     * -------------------------------------------------------------------------------------------------*/
    $(".js_home_showcase").on('click', function () {

        var element = $(this).attr('data-element');

        $(".js_home_showcase").removeClass('active');

        $(this).addClass('active');

        $(".splash-images").fadeOut('slow');

        $("#" + element).fadeIn('slow');

    });

});