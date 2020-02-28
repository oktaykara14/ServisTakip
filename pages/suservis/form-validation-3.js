var FormValidationSuServis = function () {

    var handleValidation = function() {

        $("#uretimtarih").inputmask("d-m-y",{ "placeholder": "*" ,removeMaskOnSubmit: false});
        var form = $('#form_sample');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        $("#serino").inputmask("mask", {
            mask:"9",repeat:10,greedy:!1
        });
        $("#kalan").inputmask("decimal",{
            radixPoint:",",
            groupSeparator: "",
            digits: 3,
            autoGroup: true
        });
        $("#harcanan").inputmask("decimal",{
            radixPoint:",",
            groupSeparator: "",
            digits: 3,
            autoGroup: true
        });
        $("#mekanik").inputmask("decimal",{
            radixPoint:",",
            groupSeparator: "",
            digits: 3,
            autoGroup: true
        });
        $("#ilkkredi").inputmask("decimal",{
            radixPoint:",",
            groupSeparator: "",
            digits: 3,
            autoGroup: true
        });
        $("#ilkharcanan").inputmask("decimal",{
            radixPoint:",",
            groupSeparator: "",
            digits: 3,
            autoGroup: true
        });
        $("#ilkmekanik").inputmask("decimal",{
            radixPoint:",",
            groupSeparator: "",
            digits: 3,
            autoGroup: true
        });
        $('#arizalar').multiSelect({
            keepOrder: true,
            selectableHeader: "<input type='text' style='width:100%' class='search-input ariza_search' autocomplete='off' placeholder='Aramak için giriniz' tabindex='12'>",
            selectionHeader: "<input type='text' style='width:100%' class='search-input ariza_search' autocomplete='off' placeholder='Aramak için giriniz' tabindex='13'>",
            afterInit: function(ms){
                var that = this,
                    $selectableSearch = that.$selectableUl.prev(),
                    $selectionSearch = that.$selectionUl.prev(),
                    selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                    selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

                that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                    .on('keydown', function(e){
                        if (e.which === 40){
                            that.$selectableUl.focus();
                            return false;
                        }
                    });

                that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                    .on('keydown', function(e){
                        if (e.which == 40){
                            that.$selectionUl.focus();
                            return false;
                        }
                    });
            },
            afterSelect: function(value){
                var get_val = $("#arizalist").val();
                var hidden_val = (get_val != "") ? get_val+"," : get_val;
                $("#arizalist").val(hidden_val+""+value);
                $('.ariza_search').val('');
                this.qs1.cache();
                this.qs2.cache();
            },
            afterDeselect: function(value){
                var result = [];
                var get_val = $("#arizalist").val().split(',');
                for (var i=0; i<get_val.length; i++) {
                    if (get_val[i] != value) {
                        result.push(get_val[i]);
                    }
                }
                $("#arizalist").val(result.join(','));
                $('.ariza_search').val('');
                this.qs1.cache();
                this.qs2.cache();
            }
        });

        $('#yapilanlar').multiSelect({
            keepOrder: true,
            selectableHeader: "<input type='text' style='width:100%' class='search-input yapilan_search' autocomplete='off' placeholder='Aramak için giriniz' tabindex='16'>",
            selectionHeader: "<input type='text' style='width:100%' class='search-input yapilan_search' autocomplete='off' placeholder='Aramak için giriniz' tabindex='17'>",
            afterInit: function(ms){
                var that = this,
                    $selectableSearch = that.$selectableUl.prev(),
                    $selectionSearch = that.$selectionUl.prev(),
                    selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                    selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

                that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                    .on('keydown', function(e){
                        if (e.which === 40){
                            that.$selectableUl.focus();
                            return false;
                        }
                    });

                that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                    .on('keydown', function(e){
                        if (e.which == 40){
                            that.$selectionUl.focus();
                            return false;
                        }
                    });
            },
            afterSelect: function(value){
                var get_val = $("#yapilanlist").val();
                var hidden_val = (get_val != "") ? get_val+"," : get_val;
                $("#yapilanlist").val(hidden_val+""+value);
                $('.yapilan_search').val('');
                this.qs1.cache();
                this.qs2.cache();
            },
            afterDeselect: function(value){
                var result = [];
                var get_val = $("#yapilanlist").val().split(',');
                for (var i=0; i<get_val.length; i++) {
                    if (get_val[i] != value) {
                        result.push(get_val[i]);
                    }
                }
                $("#yapilanlist").val(result.join(','));
                $('.yapilan_search').val('');
                this.qs1.cache();
                this.qs2.cache();
            }
        });

        $('#degisenler').multiSelect({
            keepOrder: true,
            selectableHeader: "<input type='text' style='width:100%' class='search-input degisen_search' autocomplete='off' placeholder='Aramak için giriniz' tabindex='14'>",
            selectionHeader: "<input type='text' style='width:100%' class='search-input degisen_search' autocomplete='off' placeholder='Aramak için giriniz' tabindex='15'>",
            afterInit: function(ms){
                var that = this,
                    $selectableSearch = that.$selectableUl.prev(),
                    $selectionSearch = that.$selectionUl.prev(),
                    selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                    selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

                that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                    .on('keydown', function(e){
                        if (e.which === 40){
                            that.$selectableUl.focus();
                            return false;
                        }
                    });

                that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                    .on('keydown', function(e){
                        if (e.which == 40){
                            that.$selectionUl.focus();
                            return false;
                        }
                    });
            },
            afterSelect: function(value){
                var get_val = $("#degisenlist").val();
                var hidden_val = (get_val != "") ? get_val+"," : get_val;
                $("#degisenlist").val(hidden_val+""+value);
                $('.degisen_search').val('');
                this.qs1.cache();
                this.qs2.cache();
            },
            afterDeselect: function(value){
                var result = [];
                var get_val = $("#degisenlist").val().split(',');
                for (var i=0; i<get_val.length; i++) {
                    if (get_val[i] != value) {
                        result.push(get_val[i]);
                    }
                }
                $("#degisenlist").val(result.join(','));
                $('.degisen_search').val('');
                this.qs1.cache();
                this.qs2.cache();
            }
        });

        $('#uyarilar').multiSelect({
            keepOrder: true,
            selectableHeader: "<input type='text' style='width:100%' class='search-input uyari_search' autocomplete='off' placeholder='Aramak için giriniz' tabindex='18'>",
            selectionHeader: "<input type='text' style='width:100%' class='search-input uyari_search' autocomplete='off' placeholder='Aramak için giriniz' tabindex='19'>",
            afterInit: function(ms){
                var that = this,
                    $selectableSearch = that.$selectableUl.prev(),
                    $selectionSearch = that.$selectionUl.prev(),
                    selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                    selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

                that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                    .on('keydown', function(e){
                        if (e.which === 40){
                            that.$selectableUl.focus();
                            return false;
                        }
                    });

                that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                    .on('keydown', function(e){
                        if (e.which == 40){
                            that.$selectionUl.focus();
                            return false;
                        }
                    });
            },
            afterSelect: function(value){
                var get_val = $("#uyarilist").val();
                var hidden_val = (get_val != "") ? get_val+"," : get_val;
                $("#uyarilist").val(hidden_val+""+value);
                $('.uyari_search').val('');
                this.qs1.cache();
                this.qs2.cache();
            },
            afterDeselect: function(value){
                var result = [];
                var get_val = $("#uyarilist").val().split(',');
                for (var i=0; i<get_val.length; i++) {
                    if (get_val[i] != value) {
                        result.push(get_val[i]);
                    }
                }
                $("#uyarilist").val(result.join(','));
                $('.uyari_search').val('');
                this.qs1.cache();
                this.qs2.cache();
            }
        });
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: true, // do not focus the last invalid input
            ignore: ":not(:visible)", // validate all fields including form hidden input
            rules: {
                cariadi: {
                    required: true
                },
                istek: {
                    required: true
                },
                uretimyer: {
                    required: true
                },
                serino: {
                    required: true
                },
                uretim: {
                    required: true
                },
                sayacadi: {
                    required: true
                },
                sayaccap: {
                    required: true
                },
                garanti: {
                    required: true
                },
                ilkkredi: {
                    required: true
                },
                ilkharcanan: {
                    required: true
                },
                ilkmekanik: {
                    required: true
                },
                kalan: {
                    required: true
                },
                harcanan: {
                    required: true
                },
                mekanik: {
                    required: true
                },
                "arizalar[]": {
                    required: true
                },
                "yapilanlar[]": {
                    required: true
                },
                "degisenler[]": {
                    required: true
                },
                "uyarilar[]": {
                    required: true
                }
            },
            messages:{
                "arizalar[]": "Bir ariza nedeni seçilmelidir",
                "yapilanlar[]": "Bir yapılan işlem seçilmelidir",
                "degisenler[]": "Bir değişen parça seçilmelidir",
                "uyarilar[]": "Bir uyarı ya da sonuç seçilmelidir"
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

    var handleValidation2 = function() {
        // for more info visit the official plugin documentation:
        // http://docs.jquery.com/Plugins/Validation

        var form = $('#form_sample4');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);

        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: true, // do not focus the last invalid input
            ignore: ":not(:visible)", // validate all fields including form hidden input
            rules: {
                yapilanyeni: {
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

    var handleValidation3 = function() {
        // for more info visit the official plugin documentation:
        // http://docs.jquery.com/Plugins/Validation

        var form = $('#form_sample3');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);

        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: true, // do not focus the last invalid input
            ignore: ":not(:visible)", // validate all fields including form hidden input
            rules: {
                arizayeni: {
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

    var handleValidation4 = function() {
        // for more info visit the official plugin documentation:
        // http://docs.jquery.com/Plugins/Validation

        var form = $('#form_sample6');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);

        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: true, // do not focus the last invalid input
            ignore: ":not(:visible)", // validate all fields including form hidden input
            rules: {
                uyariyeni: {
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

    var handleValidation5 = function() {
        // for more info visit the official plugin documentation:
        // http://docs.jquery.com/Plugins/Validation

        var form = $('#form_sample7');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);

        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: true, // do not focus the last invalid input
            ignore: ":not(:visible)", // validate all fields including form hidden input
            rules: {
                serinoyeni: {
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

    var initTable1 = function () {

        var table = $('#sample_1');
        // begin first table
        table.DataTable({
            "bPaginate": false,
            "searching": false,
            "ordering": false,
            bInfo: false
        });

        var tableWrapper = jQuery('#sample_1_wrapper');

        table.on('change', 'tbody tr .checkboxes', function () {
            if($(this).prop('checked'))
            {
                $("tbody tr .checkboxes").prop('checked', false);
                $("tbody tr").removeClass("active");
                $(this).prop('checked',true);
                $(this).parents('tr').toggleClass("active");
            }else{
                $(this).prop('checked',false);
                $(this).parents('tr').removeClass("active");
            }
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    };
    var initTable2 = function () {

        var table = $('#sample_2');
        // begin first table
        table.DataTable({
            "bPaginate": false,
            "searching": false,
            "ordering": false,
            bInfo: false
        });

        var tableWrapper = jQuery('#sample_2_wrapper');

        table.on('change', 'tbody tr .checkboxes', function () {
            if($(this).prop('checked'))
            {
                $("tbody tr .checkboxes").prop('checked', false);
                $("tbody tr").removeClass("active");
                $(this).prop('checked',true);
                $(this).parents('tr').toggleClass("active");
            }else{
                $(this).prop('checked',false);
                $(this).parents('tr').removeClass("active");
            }
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    };

    return {
        //main function to initiate the module
        init: function () {

            handleValidation();
            handleValidation2();
            handleValidation3();
            handleValidation4();
            handleValidation5();
            handleDatePickers();
            initTable1();
            initTable2();
        }

    };

}();