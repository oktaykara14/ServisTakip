var EdestekPage = function () {

    var handleTable = function () {
        var table = $('#sample_editable_1');
        
        table.DataTable({
            "lengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "Hepsi"] // change per page values here
            ],

            "pageLength": 10,

            "language": {
                "emptyTable": "Veri Bulunamadı",
                "info": "  _TOTAL_ Kayıttan _START_ - _END_ Arası Kayıtlar",
                "infoEmpty": "Kayıt Yok",
                "infoFiltered": "( _MAX_ Kayıt İçerisinden Bulunan)",
                "lengthMenu": "Sayfada _MENU_ Kayıt Göster",
                "paginate": {
                    "previous": "Önceki",
                    "next": "Sonraki"
                },
                "search": "Bul:",
                "zeroRecords": "Eşleşen Kayıt Bulunmadı"
                
            },
            "columnDefs": [{ // set default column settings
                'orderable': true,
                'targets': [1]
            }, {
                "searchable": true,
                "targets": [1]
            }],
            "order": [
                [1, "asc"]
            ] // set first column as a default sort by asc
        });
    };
    
    var handleTable2 = function () {
        var table = $('#sample_editable_2');
        
        table.DataTable({
            "lengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "Hepsi"] // change per page values here
            ],
            "pageLength": 10,

            "language": {
                "emptyTable": "Veri Bulunamadı",
                "info": "  _TOTAL_ Kayıttan _START_ - _END_ Arası Kayıtlar",
                "infoEmpty": "Kayıt Yok",
                "infoFiltered": "( _MAX_ Kayıt İçerisinden Bulunan)",
                "lengthMenu": "Sayfada _MENU_ Kayıt Göster",
                "paginate": {
                    "previous": "Önceki",
                    "next": "Sonraki"
                },
                "search": "Bul:",
                "zeroRecords": "Eşleşen Kayıt Bulunmadı"
                
            },
            "columnDefs": [{ // set default column settings
                'orderable': true,
                'targets': [5]
            }, {
                "searchable": true,
                "targets": [1]
            }],
            "order": [
                [5, "desc"]
            ] // set first column as a default sort by asc
        });
    };
    
    return {

        //main function to initiate the module
        init: function () {
            handleTable();
            handleTable2();
        }

    };
}();