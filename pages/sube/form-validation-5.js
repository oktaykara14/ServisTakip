var FormValidationSube = function () {
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation

        $("#tarih").inputmask("d-m-y",{ "placeholder": "*" ,removeMaskOnSubmit: false});
        var form = $('#form_sample');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        var form2 = $('#form_sample_abone');
        $("#aboneserino").inputmask("mask", {
            mask:"9",repeat:15,greedy:!1
        });
        $("#tckimlikno").inputmask("mask", {
            mask:"9",repeat:11,greedy:!1
        });
        $("#abonetckimlikno").inputmask("mask", {
            mask:"9",repeat:11,greedy:!1
        });
        $("#telefon").mask("0(999) 999 99 99");
        $("#abonetelefon").mask("0(999) 999 99 99");
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: true, // do not focus the last invalid input
            ignore: ":not(:visible)", // validate all fields including form hidden input
            rules: {
                abone: {
                    required: true
                },
                telefon: {
                    required: true,
                    minlength:16
                },
                tckimlikno: {
                    required: true
                },
                cariadi: {
                    required: true
                },
                tarih: {
                    required: true
                },
                adres: {
                    required: true,
                    maxlength:100
                },
                faturaadresi: {
                    required: true,
                    maxlength:100
                },
                faturail: {
                    required: true
                },
                faturailce:{
                    required: true
                },
                'urunadi[]': {
                    required: true
                },
                'abonesayac[][]': {
                    required: true
                },
                "fiyat[]": {
                    required: true
                },
                odemesekli: {
                    required: true
                },
                faturano: {
                    required: true
                },
                aciklama: {
                    required: true
                },
                kasakod: {
                    required: true
                },
                kasakod2: {
                    required: true
                },
                taksit: {
                    required: true
                },
                taksit2: {
                    required: true
                }
            },
            messages:{
                "urunadi[]": "Bir ürün seçilmeli",
                "fiyat[]": "Bir fiyat girilmelidir"
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
        form2.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: true, // do not focus the last invalid input
            ignore: ":not(:visible)", // validate all fields including form hidden input
            rules: {
                abonecariadi: {
                    required: true
                },
                aboneadisoyadi: {
                    required: true
                },
                aboneuretimyer: {
                    required: true
                },
                abonetelefon: {
                    required: true,
                    minlength:16
                },
                abonetckimlikno: {
                    required: true
                },
                aboneadres: {
                    required: true,
                    maxlength:100
                },
                aboneil: {
                    required: true
                },
                aboneilce:{
                    required: true
                },
                "aboneserino[]":{
                    required: true
                },
                "abonesayacturleri[]":{
                    required: true
                },
                "abonesayacadlari[]":{
                    required: true
                },
                "abonesayaccap[]":{
                    required: true
                },
                "abonesayacadresi[]":{
                    required: true
                }
            },
            messages:{
                "aboneserino[]": "Bir serino girilmeli",
                "abonesayacturleri[]": "Bir sayaç türü seçilmelidir",
                "abonesayacadlari[]": "Bir sayaç adı seçilmelidir",
                "abonesayaccap[]": "Bir sayaç çapı seçilmelidir",
                "abonesayacadresi[]": "Sayaç montaj adresi girilmeli"
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
        //apply validation on select2 dropdown value change, this only needed for chosen dropdown integration.
        $('.select2me', form2).change(function () {
            form2.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
        });
    };

    /*var handleDatePickers = function () {
        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "left",
                autoclose: true,
                language: 'tr'
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
        }

        /!* Workaround to restrict daterange past date select: http://stackoverflow.com/questions/11933173/how-to-restrict-the-selectable-date-ranges-in-bootstrap-datepicker *!/
    };*/
    return {
        //main function to initiate the module
        init: function () {
            handleValidation();
            //handleDatePickers();
        }

    };

}();