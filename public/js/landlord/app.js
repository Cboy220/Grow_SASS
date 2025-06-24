"use strict";

$(document).ready(function () {

    /**--------------------------------------------------------------------------------------
     * [SESSION MESSAGES]
     * @blade : views/landlord/layout/footerjs.blade.php
     * @description: show session set noty messages
     * -------------------------------------------------------------------------------------*/
    if ($("#js-trigger-session-message").length) {
        var session_message = $("#js-trigger-session-message").attr('data-message');
        var message_type = $("#js-trigger-session-message").attr('data-type');
        NX.notification({
            'type': message_type,
            'duration': 7000,
            'message': session_message,
        });
    }



    nxTinyMCEExtended(200, '.tinymce-textarea-footer');

});

/**--------------------------------------------------------------------------------------
 * [SETTINGS - EMAIL TEMPLATES]
 * @description: load email templates editor
 * -------------------------------------------------------------------------------------*/
function NXSaaSEmailTemplates() {

    //text editor
    nxTinyMCEAdvanced(500, '#emailtemplate_body', 'fullpage spellchecker', '');
    setTimeout(function () {
        $("#email-templates-editing").removeClass('loading');
        $("#email-templates-editing-container").show();
        $("#emailEditContainer").show();
        $("#emailEditWrapper").removeClass('loading');
    }, 1000);


    //fix for validator
    $("#fix-form-email-templates").validate({});
}



/**--------------------------------------------------------------------------------------
 * [PACKAGES - CREATE AND EDIT]
 * @description: form validation
 * -------------------------------------------------------------------------------------*/
function NXPackagesCreate() {
    //add category - form validation
    $("#commonModalForm").validate().destroy();
    $("#commonModalForm").validate({
        rules: {
            package_name: "required",
            package_limits_clients: "required",
            package_limits_projects: "required",
            package_limits_team: "required",
        },
        submitHandler: function (form) {
            nxAjaxUxRequest($("#commonModalSubmitButton"));
        }
    });

}


/**--------------------------------------------------------------------------------------
 * [CUSTOMERS - CREATE AND EDIT]
 * @description: form validation
 * -------------------------------------------------------------------------------------*/
function NXCustomerCreate() {
    //add category - form validation
    $("#commonModalForm").validate().destroy();
    $("#commonModalForm").validate({
        rules: {
            full_name: "required",
            email_address: "required",
            domain: "required",
        },
        submitHandler: function (form) {
            nxAjaxUxRequest($("#commonModalSubmitButton"));
        }
    });
}


/**--------------------------------------------------------------------------------------
 * [CUSTOMERS - CHANGE PLAN]
 * @description: form validation
 * -------------------------------------------------------------------------------------*/
function NXCustomerChangePlan() {
    //add category - form validation
    $("#commonModalForm").validate().destroy();
    $("#commonModalForm").validate({
        rules: {},
        submitHandler: function (form) {
            nxAjaxUxRequest($("#commonModalSubmitButton"));
        }
    });
}


/**--------------------------------------------------------------------------------------
 * [UPDATE ADMINPROFILE]
 * @description: form validation
 * -------------------------------------------------------------------------------------*/
function NXCustomerBasicEdit() {
    //add category - form validation
    $("#commonModalForm").validate().destroy();
    $("#commonModalForm").validate({
        rules: {
            full_name: "required",
            email_address: "required",
            account_name: "required",
        },
        submitHandler: function (form) {
            nxAjaxUxRequest($("#commonModalSubmitButton"));
        }
    });
}


/**--------------------------------------------------------------------------------------
 * [CUSTOMERS - CREATE AND EDIT]
 * @description: form validation
 * -------------------------------------------------------------------------------------*/
function NXLPaymentCreate() {
    //add category - form validation
    $("#commonModalForm").validate().destroy();
    $("#commonModalForm").validate({
        rules: {
            payment_amount: "required",
            payment_date: "required",
            payment_gateway: "required",
        },
        submitHandler: function (form) {
            nxAjaxUxRequest($("#commonModalSubmitButton"));
        }
    });

}



/**--------------------------------------------------------------------------------------
 * [SUBSCRIPTION - CREATE] - Paid subscription
 * @description: form validation
 * -------------------------------------------------------------------------------------*/
function NXSubscriptionCreatePaid() {
    //add category - form validation
    $("#commonModalForm").validate().destroy();
    $("#commonModalForm").validate({
        rules: {
            package_id: "required",
            billing_cycle: "required",
            subscription_payment_method: "required",
            free_trial: "required",
        },
        submitHandler: function (form) {
            nxAjaxUxRequest($("#commonModalSubmitButton"));
        }
    });
}


/**--------------------------------------------------------------------------------------
 * [SUBSCRIPTION - CREATE] - Free subscription
 * @description: form validation
 * -------------------------------------------------------------------------------------*/
function NXSubscriptionCreateFree() {
    //add category - form validation
    $("#commonModalForm").validate().destroy();
    $("#commonModalForm").validate({
        rules: {
            package_id: "required",
        },
        submitHandler: function (form) {
            nxAjaxUxRequest($("#commonModalSubmitButton"));
        }
    });
}



/**--------------------------------------------------------------------------------------
 * [UPDATE ADMINPROFILE]
 * @description: form validation
 * -------------------------------------------------------------------------------------*/
function NXUpdateAdminProfile() {
    //add category - form validation
    $("#commonModalForm").validate().destroy();
    $("#commonModalForm").validate({
        rules: {
            first_name: "required",
            last_name: "required",
            email: "required",
        },
        submitHandler: function (form) {
            nxAjaxUxRequest($("#commonModalSubmitButton"));
        }
    });
}



/**--------------------------------------------------------------------------------------
 * [SETTINGS - UPLOAD LOGO]
 * @description: apload app logo
 * -------------------------------------------------------------------------------------*/
function NXSettingsLandlordLogo() {

    //set variables and payload
    var logo_size = $("#js-settings-logos-modal").attr('data-size');

    //upload logo
    $("#fileupload_landlord_logo").dropzone({
        url: "/app-admin/upload-logo?logo_size=" + logo_size,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        maxFiles: 1,
        maxFilesize: 2, // MB
        acceptedFiles: 'image/jpeg, image/png, image/vnd.microsoft.icon, image/x-icon, image/icon',
        thumbnailWidth: null,
        thumbnailHeight: null,
        init: function () {
            this.on("error", function (file, message, xhr) {

                //is there a message from backend [abort() response]
                if (typeof xhr != 'undefined' && typeof xhr.response != 'undefined') {
                    var error = $.parseJSON(xhr.response);
                    var message = error.notification.value;
                }

                //any other message
                var message = (typeof message == 'undefined' || message == '' ||
                    typeof message == 'object') ? NXLANG.generic_error : message;

                //error message
                NX.notification({
                    type: 'error',
                    message: message
                });
                //remove the file
                this.removeFile(file);
            });
        },
        success: function (file, response) {
            //get the priview box dom elemen
            var $preview = $(file.previewElement);
            //create a hidden form field for this file
            $preview.append('<input type="hidden" name="logo_filename"  value="' + response
                .filename + '">');
            $preview.append('<input type="hidden" name="logo_directory"  value="' + response
                .uniqueid + '">');
            $preview.append('<input type="hidden" name="logo_size"  value="' + response
                .logo_size + '">');
        }
    });


    //upload logo - form validation
    $("#commonModalForm").validate().destroy();
    $("#commonModalForm").validate({
        rules: {},
        submitHandler: function (form) {
            nxAjaxUxRequest($("#commonModalSubmitButton"));
        }
    });
}


/**--------------------------------------------------------------------------------------
 * [USER - UPDATE AVATAR]
 * @description: validation for user avatar modal
 * -------------------------------------------------------------------------------------*/
function NXLandlordUpdateAvatar() {

    //upload avatar
    $("#fileupload_avatar").dropzone({
        url: "/app-admin/avatarupload",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        maxFiles: 1,
        maxFilesize: 2, // MB
        acceptedFiles: 'image/jpeg, image/png',
        thumbnailWidth: null,
        thumbnailHeight: null,
        init: function () {
            this.on("error", function (file, message, xhr) {

                //is there a message from backend [abort() response]
                if (typeof xhr != 'undefined' && typeof xhr.response != 'undefined') {
                    var error = $.parseJSON(xhr.response);
                    var message = error.notification.value;
                }

                //any other message
                message = (typeof message == 'undefined' || message == '' ||
                    typeof message == 'object') ? NXLANG.generic_error : message;

                //error message
                NX.notification({
                    type: 'error',
                    message: message
                });
                //remove the file
                this.removeFile(file);
            });
        },
        success: function (file, response) {
            //get the priview box dom elemen
            var $preview = $(file.previewElement);
            //create a hidden form field for this file
            $preview.append('<input type="hidden" name="avatar_filename"  value="' + response.filename + '">');
            $preview.append('<input type="hidden" name="avatar_directory"  value="' + response.uniqueid + '">');
        }
    });


    //validation
    $("#commonModalForm").validate().destroy();
    $("#commonModalForm").validate({
        rules: {},
        submitHandler: function (form) {
            nxAjaxUxRequest($("#commonModalSubmitButton"));
        }
    });

}



/**--------------------------------------------------------------------------------------
 * [HERO]
 * @description: validation for uploading hero image
 * -------------------------------------------------------------------------------------*/
function NXLandlordUploadImage() {

    $("#fileupload_image").dropzone({
        url: "/app-admin/upload-image",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        maxFiles: 1,
        maxFilesize: 2, // MB
        acceptedFiles: 'image/jpeg, image/png, image/gif',
        thumbnailWidth: null,
        thumbnailHeight: null,
        init: function () {
            this.on("error", function (file, message, xhr) {

                //is there a message from backend [abort() response]
                if (typeof xhr != 'undefined' && typeof xhr.response != 'undefined') {
                    var error = $.parseJSON(xhr.response);
                    var message = error.notification.value;
                }

                //any other message
                message = (typeof message == 'undefined' || message == '' ||
                    typeof message == 'object') ? NXLANG.generic_error : message;

                //error message
                NX.notification({
                    type: 'error',
                    message: message
                });
                //remove the file
                this.removeFile(file);
            });
        },
        success: function (file, response) {
            //get the priview box dom elemen
            var $preview = $(file.previewElement);
            //create a hidden form field for this file
            $preview.append('<input type="hidden" name="image_filename"  value="' + response.filename + '">');
            $preview.append('<input type="hidden" name="image_directory"  value="' + response.uniqueid + '">');
        }
    });
}
if ($("#fileupload_image").length) {
    NXLandlordUploadImage();
}


/**--------------------------------------------------------------------------------------
 * [HERO]
 * @description: validation for uploading hero image
 * -------------------------------------------------------------------------------------*/
function NXLandlordUploadImage2() {

    $("#fileupload_image_2").dropzone({
        url: "/app-admin/upload-image",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        maxFiles: 1,
        maxFilesize: 2, // MB
        acceptedFiles: 'image/jpeg, image/png, image/gif',
        thumbnailWidth: null,
        thumbnailHeight: null,
        init: function () {
            this.on("error", function (file, message, xhr) {

                //is there a message from backend [abort() response]
                if (typeof xhr != 'undefined' && typeof xhr.response != 'undefined') {
                    var error = $.parseJSON(xhr.response);
                    var message = error.notification.value;
                }

                //any other message
                message = (typeof message == 'undefined' || message == '' ||
                    typeof message == 'object') ? NXLANG.generic_error : message;

                //error message
                NX.notification({
                    type: 'error',
                    message: message
                });
                //remove the file
                this.removeFile(file);
            });
        },
        success: function (file, response) {
            //get the priview box dom elemen
            var $preview = $(file.previewElement);
            //create a hidden form field for this file
            $preview.append('<input type="hidden" name="image_filename_2"  value="' + response.filename + '">');
            $preview.append('<input type="hidden" name="image_directory_2"  value="' + response.uniqueid + '">');
        }
    });
}
if ($("#fileupload_image_2").length) {
    NXLandlordUploadImage2();
}

/**--------------------------------------------------------------------------------------
 * [USER - UPDATE AVATAR]
 * @description: validation for user avatar modal
 * -------------------------------------------------------------------------------------*/
function NXLandlordMainMenuEdit() {


    //validation
    $("#commonModalForm").validate().destroy();
    $("#commonModalForm").validate({
        rules: {
            frontend_data_1: "required",
        },
        submitHandler: function (form) {
            nxAjaxUxRequest($("#commonModalSubmitButton"));
        }
    });
}



/**--------------------------------------------------------------------------------------
 * [EDIT FAQ]
 * @description: validation creating new FAQ for frontend
 * -------------------------------------------------------------------------------------*/
function NXLandlordFAQEdit() {


    //validation
    $("#commonModalForm").validate().destroy();
    $("#commonModalForm").validate({
        rules: {
            frontend_data_1: "required",
        },
        submitHandler: function (form) {
            nxAjaxUxRequest($("#commonModalSubmitButton"));
        }
    });
}

/**--------------------------------------------------------------------------------------
 * @description: update customers password
 * -------------------------------------------------------------------------------------*/
function NXSaaSEditCustomerPassword() {
    //validation
    $("#commonModalForm").validate().destroy();
    $("#commonModalForm").validate({
        rules: {
            password: {
                required: true,
            },
            password_confirmation: {
                equalTo: "#password"
            },
        },
        submitHandler: function (form) {
            nxAjaxUxRequest($("#commonModalSubmitButton"));
        }
    });
}




/**--------------------------------------------------------------------------------------
 * [PACKAGES - UPDATE TENANT PASSWORD]
 * @description: form validation
 * -------------------------------------------------------------------------------------*/
function NXCustomerUpdatePassword() {
    //add category - form validation
    $("#commonModalForm").validate().destroy();
    $("#commonModalForm").validate({
        rules: {
            password: {
                required: true,
            },
            confirm_password: {
                equalTo: "#password"
            },
        },
        submitHandler: function (form) {
            nxAjaxUxRequest($("#commonModalSubmitButton"));
        }
    });
}



/**--------------------------------------------------------------------------------------
 * [PACKAGES - UPDATE TENANT ACTIVE STATUS]
 * @description: form validation
 * -------------------------------------------------------------------------------------*/
function NXCustomerSetStatusActive() {
    //add category - form validation
    $("#commonModalForm").validate().destroy();
    $("#commonModalForm").validate({
        rules: {
            expiry_date: {
                required: true,
            },
        },
        submitHandler: function (form) {
            nxAjaxUxRequest($("#commonModalSubmitButton"));
        }
    });
}


/**--------------------------------------------------------------------------------------
 * [MENU SCROLL BAR]
 * @description: show scroll bar
 * -------------------------------------------------------------------------------------*/
function nxLandlordSettingsMenuScroll() {
    const navLeftScroll = new PerfectScrollbar('#landlord-left-inner-menu', {
        wheelSpeed: 2,
        wheelPropagation: true,
    });
}
$(document).ready(function () {
    if ($("#landlord-left-inner-menu").length) {
        nxLandlordSettingsMenuScroll();
    }
});


/**--------------------------------------------------------------------------------------
 * [TEAM - ADD/EDIT]
 * @description: form validation
 * -------------------------------------------------------------------------------------*/
function NXLandlordTeamCreate() {
    //add category - form validation
    $("#commonModalForm").validate().destroy();
    $("#commonModalForm").validate({
        rules: {
            first_name: {
                required: true,
            },
            last_name: {
                required: true,
            },
            email: {
                required: true,
            },
        },
        submitHandler: function (form) {
            nxAjaxUxRequest($("#commonModalSubmitButton"));
        }
    });
}