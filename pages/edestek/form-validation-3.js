var FormValidationEdestek = function () {

    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation

        $("#mask_phone").inputmask("mask", {
            "mask": "(999) 999-9999"
        });
        $("#mask_phone2").inputmask("mask", {
            "mask": "(999) 999-9999"
        });
        $("#baslangic").inputmask("d-m-y",{ "placeholder": "*",removeMaskOnSubmit: false });
        $("#bitis").inputmask("d-m-y",{ "placeholder": "*" ,removeMaskOnSubmit: false});
        $('#options3').select2({
            placeholder: "Ürün Seçin",
            allowClear: true
        });
        var urunler,programlar,baskiturler;
        if($( "#urunler" ).hasClass( "urunler" ))
        {
            urunler =document.getElementById("urunler").innerHTML;
            urunler = urunler.replace(/\s+/g,' ').trim();
            urunler = urunler.split(" ");
            $('#options3').select2("val",urunler);
        }

        if($( "#urunekli" ).hasClass( "urunekli" ))
        {
            urunler =document.getElementById("urunekli").innerHTML;
            urunler = urunler.split(",");
            $('#options3').select2("val",urunler);
        }

        $('#options4').select2({
            placeholder: "Program Seçin",
            allowClear: true
        });

        if($( "#programlar" ).hasClass( "programlar" ))
        {
            programlar =document.getElementById("programlar").innerHTML;
            programlar = programlar.replace(/\s+/g,' ').trim();
            programlar = programlar.split(" ");
            $('#options4').select2("val",programlar);
        }

        if($( "#programekli" ).hasClass( "programekli" ))
        {
            programlar =document.getElementById("programekli").innerHTML;
            programlar = programlar.split(",");
            $('#options4').select2("val",programlar);
        }

        $('#options9').select2({
            placeholder: "Baskı Türü Seçin",
            allowClear: true
        });

        if($( "#baskiturler" ).hasClass( "baskiturler" ))
        {
            baskiturler =document.getElementById("baskiturler").innerHTML;
            baskiturler = baskiturler.replace(/\s+/g,' ').trim();
            baskiturler = baskiturler.split(" ");
            $('#options9').select2("val",baskiturler);
        }

        if($( "#baskiturekli" ).hasClass( "baskiturekli" ))
        {
            baskiturler =document.getElementById("baskiturekli").innerHTML;
            baskiturler = baskiturler.split(",");
            $('#options9').select2("val",baskiturler);
        }

        var form = $('#form_sample');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: true, // do not focus the last invalid input
            ignore: ":not(:visible)", // validate all fields including form hidden input
            rules: {
                adi: {
                    required: true
                },
                mail:{
                    email: true
                },
                resim: {
                    accept: "jpg|jpeg|png|gif"
                },
                suontaraf: {
                    accept: "jpg|jpeg|png|gif"
                },
                suarkataraf: {
                    accept: "jpg|jpeg|png|gif"
                },
                klmontaraf: {
                    accept: "jpg|jpeg|png|gif"
                },
                klmarkataraf: {
                    accept: "jpg|jpeg|png|gif"
                },
                trifazeontaraf: {
                    accept: "jpg|jpeg|png|gif"
                },
                trifazearkataraf: {
                    accept: "jpg|jpeg|png|gif"
                },
                monoontaraf: {
                    accept: "jpg|jpeg|png|gif"
                },
                monoarkataraf: {
                    accept: "jpg|jpeg|png|gif"
                },
                klimaontaraf: {
                    accept: "jpg|jpeg|png|gif"
                },
                klimaarkataraf: {
                    accept: "jpg|jpeg|png|gif"
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