"use strict";


$(document).ready(function () {

    /**-------------------------------------------------------------
     * PROOF FILE UPLOAD
     * ------------------------------------------------------------*/
    if ($("#fileupload_proof_of_payment").length) {
        //get the url
        var upload_url = $("#fileupload_proof_of_payment").attr('data-upload-url');
        $("#fileupload_proof_of_payment").dropzone({
            url: upload_url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            init: function () {
                this.on("error", function (file, message, xhr) {
                    //is there a message from backend [abort() response]
                    if (typeof xhr != 'undefined' && typeof xhr.response != 'undefined') {
                        var error = $.parseJSON(xhr.response);
                        if (typeof error === 'object' && typeof error.notification != 'undefined') {
                            var message = error.notification.value;
                        } else {
                            var message = NXLANG.generic_error;
                        }
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
                //hide bank details & file upload section
                $("#proof_of_payment_container").hide();
                $("#offline_payment_details").hide();
                $(".alert-info").hide();
                //show thank you message
                $("#proof_of_payment_thankyou").removeClass('hidden');
                //remove the file
                this.removeFile(file);
            }
        });
    }


});