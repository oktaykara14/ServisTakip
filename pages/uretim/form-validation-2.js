var FormValidationUretim = function () {

    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation

        $("#gelis").inputmask("d-m-y",{ "placeholder": "*" ,removeMaskOnSubmit: false});
        var form = $('#form_sample');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);

        $('#serino').select2({
            placeholder: "Seri Numarası Ekleyin",
            allowClear: true
        });
        var serinolar;
        if($( "#serinolar" ).hasClass( "serinolar" ))
        {
            serinolar =document.getElementById("serinolar").innerHTML;
            serinolar = serinolar.replace(/\s+/g,' ').trim();
            serinolar = serinolar.split(" ");
            if(serinolar[0]!==""){
                $.each(serinolar, function (i) {
                    if(serinolar!=="")
                        $('#serino').append('<option value="' + serinolar[i] + '"> ' + serinolar[i] + '</option>');
                });
                $('#serino').select2("val",serinolar);
            }
        }

        if($( "#serinoekli" ).hasClass( "serinoekli" ))
        {
            serinolar =document.getElementById("serinoekli").innerHTML;
            serinolar = serinolar.split(",");
            if(serinolar[0]!==""){
                $.each(serinolar, function (i) {
                    if(serinolar!=="")
                        $('#serino').append('<option value="' + serinolar[i] + '"> ' + serinolar[i] + '</option>');
                });
                $('#serino').select2("val",serinolar);
            }
        }


        $('#eklibarkod').select2({
            placeholder: "Barkodları Barkod Okuyucu ile Ekleyin",
            allowClear: true
        });
        var barkodlar;
        if($( "#eklibarkodlar" ).hasClass( "eklibarkodlar" ))
        {
            barkodlar =document.getElementById("eklibarkodlar").innerHTML;
            barkodlar = barkodlar.replace(/\s+/g,' ').trim();
            barkodlar = barkodlar.split(" ");
            if(barkodlar[0]!==""){
                $.each(barkodlar, function (i) {
                    if(barkodlar!=="")
                        $('#eklibarkod').append('<option value="' + barkodlar[i] + '"> ' + barkodlar[i] + '</option>');
                });
                $('#eklibarkod').select2("val",barkodlar);
            }
        }

        if($( "#barkodlarekli" ).hasClass( "barkodlarekli" ))
        {
            barkodlar =document.getElementById("barkodlarekli").innerHTML;
            barkodlar = barkodlar.split(",");
            if(barkodlar[0]!==""){
                $.each(barkodlar, function (i) {
                    if(barkodlar!=="")
                        $('#eklibarkod').append('<option value="' + barkodlar[i] + '"> ' + barkodlar[i] + '</option>');
                });
                $('#eklibarkod').select2("val",barkodlar);
            }
        }
        $('#tumbarkod').select2({
            placeholder: "Barkodları Barkod Okuyucu ile Ekleyin",
            allowClear: true
        });
        if($( "#tumbarkodlar" ).hasClass( "tumbarkodlar" ))
        {
            barkodlar =document.getElementById("tumbarkodlar").innerHTML;
            barkodlar = barkodlar.replace(/\s+/g,' ').trim();
            barkodlar = barkodlar.split(" ");
            if(barkodlar[0]!==""){
                $.each(barkodlar, function (i) {
                    if(barkodlar!=="")
                        $('#tumbarkod').append('<option value="' + barkodlar[i] + '"> ' + barkodlar[i] + '</option>');
                });
                $('#tumbarkod').select2("val",barkodlar);
            }
        }

        if($( "#tumbarkodlarekli" ).hasClass( "tumbarkodlarekli" ))
        {
            barkodlar =document.getElementById("tumbarkodlarekli").innerHTML;
            barkodlar = barkodlar.split(",");
            if(barkodlar[0]!==""){
                $.each(barkodlar, function (i) {
                    if(barkodlar!=="")
                        $('#tumbarkod').append('<option value="' + barkodlar[i] + '"> ' + barkodlar[i] + '</option>');
                });
                $('#tumbarkod').select2("val",barkodlar);
            }
        }
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: true, // do not focus the last invalid input
            ignore: ":not(:visible)", // validate all fields including form hidden input
            rules: {
                isemri: {
                    required: true
                },
                cikisdepo: {
                    required: true
                },
                girisdepo: {
                    required: true
                },
                "serino[]":{
                    required: true
                }
            },
            messages:{
                "serino[]": "Seri No bilgisi girilmeli"
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