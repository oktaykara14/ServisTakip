var FormValidationSube = function () {
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation

        $("#acilmatarihi").inputmask("d-m-y",{ "placeholder": "*" ,removeMaskOnSubmit: false});
        $("#kapanmatarihi").inputmask("d-m-y",{ "placeholder": "*" ,removeMaskOnSubmit: false});
        $("#takilmatarihi").inputmask("d-m-y",{ "placeholder": "*" ,removeMaskOnSubmit: false});
        var form = $('#form_sample');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        $("#abonetelefon").mask("0(999) 999 99 99");
        $("#ilgilitel").mask("0(999) 999 99 99");

        $('#ekstra').select2({
            placeholder: "Ücret Seçin...",
            allowClear: true
        });

        var ekstralar;
        if($( "#ekstraekli" ).hasClass( "ekstraekli" ))
        {
            ekstralar =document.getElementById("ekstraekli").innerHTML;
            ekstralar = ekstralar.split(",");
            $('#ekstra').select2("val",ekstralar);
        }

        if($( "#ekstralar" ).hasClass( "ekstralar" ))
        {
            ekstralar =document.getElementById("ekstralar").innerHTML;
            ekstralar = ekstralar.replace(/\s+/g,' ').trim();
            ekstralar = ekstralar.split(" ");
            $('#ekstra').select2("val",ekstralar);
        }

        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: true, // do not focus the last invalid input
            ignore: ":not(:visible)", // validate all fields including form hidden input
            rules: {
                abone: {
                    required: true
                },
                abonesayac: {
                    required: true
                },
                abonetelefon:{
                    required: true,
                    minlength: 16
                },
                sayacadres: {
                    required: true
                },
                durum: {
                    required: true
                },
                acilmatarihi: {
                    required: true
                },
                takilmatarihi: {
                    required: true
                },
                ilgilitel:{
                    required: true,
                    minlength: 16
                }
            },
            errorPlacement: function (error, element) { // render error placement for each input type
                var icon = $(element).closest('.input-icon').children('i');
                icon.removeClass('fa-check').addClass("fa-warning");
                icon.attr("data-original-title", error.text()).tooltip({
                    'container': 'body'
                });
            },

            invalidHandler: function () { //display error alert on form submit
                success.hide();
                error.show();
                Metronic.scrollTo(error, -200);
            },

            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').removeClass("has-success").addClass('has-error'); // set error class to the control group
            },

            unhighlight: function (element) { // revert the change done by highlight

            },

            success: function (label, element) {
                var icon = $(element).closest('.input-icon').children('i');
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                icon.removeClass("fa-warning").addClass("fa-check");
            },

            submitHandler: function (form) {
                success.show();
                error.hide();
                Metronic.scrollTo(success, -200);
                form.submit(); // submit the form
                $.blockUI();
            }
        });

         //apply validation on select2 dropdown value change, this only needed for chosen dropdown integration.
        $('.select2me', form).change(function () {
            form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
        });
    };

    var handleDatePickers = function () {
        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "left",
                autoclose: true,
                language: 'tr'
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
        }

        /* Workaround to restrict daterange past date select: http://stackoverflow.com/questions/11933173/how-to-restrict-the-selectable-date-ranges-in-bootstrap-datepicker */
    };
    return {
        //main function to initiate the module
        init: function () {
            handleValidation();
            handleDatePickers();

        }

    };

}();