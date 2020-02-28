var FormValidationEdestek = function () {

    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation

        $("#giristarihi").inputmask("d-m-y",{ "placeholder": "*" ,removeMaskOnSubmit: false});
        var form = $('#form_sample');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);

        $('#durum').on('switchChange.bootstrapSwitch', function (event, state) {
            if( state===true)
                $('#durum').attr('checked',true);
            else
                $('#durum').attr('checked',false);
        });

        $('#options2').select2({
            placeholder: "Konu Seçin",
            allowClear: true
        });

        var ilgiler;
        if($( "#ilgiler" ).hasClass( "ilgiler" ))
        {
            ilgiler =document.getElementById("ilgiler").innerHTML;
            ilgiler = ilgiler.replace(/\s+/g,' ').trim();
            ilgiler = ilgiler.split(" ");
            $('#options2').select2("val",ilgiler);
        }

        if($( "#ilgiekli" ).hasClass( "ilgiekli" ))
        {
            ilgiler =document.getElementById("ilgiekli").innerHTML;
            ilgiler = ilgiler.split(",");
            $('#options2').select2("val",ilgiler);
        }

        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: true, // do not focus the last invalid input
            ignore: ":not(:visible)", // validate all fields including form hidden input
            rules: {
                adisoyadi: {
                    minlength: 5,
                    required: true
                },
                options1: {
                    required: true
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