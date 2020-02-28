var FormValidationElkDatabase = function () {

    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation

        var form = $('#form_sample');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);

        $('#parca').select2({
            placeholder: "Parça Seçin",
            allowClear: true
        });

        $('#parcadurum').on('change',function(){
            var value = $('#parcadurum').val();
            if(value==='1'){
                $('.kontrol').removeClass("hide");
            }else{
                $('.kontrol').addClass("hide");
                $('#parca').select2("val",[]);
            }
        });
        var parcalar;
        if($( "#parcaekli" ).hasClass( "parcaekli" ))
        {
            parcalar =document.getElementById("parcaekli").innerHTML;
            parcalar = parcalar.split(",");
            $('#parca').select2("val",parcalar);
        }

        if($( "#parcalar" ).hasClass( "parcalar" ))
        {
            parcalar =document.getElementById("parcalar").innerHTML;
            parcalar = parcalar.replace(/\s+/g,' ').trim();
            parcalar = parcalar.split(" ");
            $('#parca').select2("val",parcalar);
        }

        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: true, // do not focus the last invalid input
            ignore: ":not(:visible)", // validate all fields including form hidden input
            rules: {
                tanim: {
                    required: true
                },
                parcadurum: {
                    required: true
                },
                stokkontrol: {
                    required: true
                },
                durum: {
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
    return {
        //main function to initiate the module
        init: function () {

            handleValidation();
            
        }

    };

}();