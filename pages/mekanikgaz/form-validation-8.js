var FormValidationGazServis = function () {
    var handleValidation = function() {
        // for more info visit the official plugin documentation:
        // http://docs.jquery.com/Plugins/Validation

        $("#depogelen").select2();

        $("#sayaclar").select2();

        $('#arizalar').multiSelect({
            keepOrder: true,
            selectableHeader: "<input type='text' style='width:100%' class='search-input ariza_search' autocomplete='off' placeholder='Aramak için giriniz' tabindex='14'>",
            selectionHeader: "<input type='text' style='width:100%' class='search-input ariza_search' autocomplete='off' placeholder='Aramak için giriniz' tabindex='15'>",
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
            selectableHeader: "<input type='text' style='width:100%' class='search-input yapilan_search' autocomplete='off' placeholder='Aramak için giriniz' tabindex='18'>",
            selectionHeader: "<input type='text' style='width:100%' class='search-input yapilan_search' autocomplete='off' placeholder='Aramak için giriniz' tabindex='19'>",
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
            selectableHeader: "<input type='text' style='width:100%' class='search-input degisen_search' autocomplete='off' placeholder='Aramak için giriniz' tabindex='16'>",
            selectionHeader: "<input type='text' style='width:100%' class='search-input degisen_search' autocomplete='off' placeholder='Aramak için giriniz' tabindex='17'>",
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
            selectableHeader: "<input type='text' style='width:100%' class='search-input uyari_search' autocomplete='off' placeholder='Aramak için giriniz' tabindex='20'>",
            selectionHeader: "<input type='text' style='width:100%' class='search-input uyari_search' autocomplete='off' placeholder='Aramak için giriniz' tabindex='21'>",
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

        var form = $('#form_sample_2');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: true, // do not focus the last invalid input
            ignore: ":not(:visible)", // validate all fields including form hidden input
            rules: {
                depogelen: {
                    required: true
                },
                sayaclar: {
                    required: true
                },
                durum: {
                    required: true
                },
                personel: {
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