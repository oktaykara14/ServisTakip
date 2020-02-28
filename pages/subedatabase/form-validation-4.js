var FormValidationSubeDatabase = function () {

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
            initTable1();
            initTable2();
        }

    };

}();