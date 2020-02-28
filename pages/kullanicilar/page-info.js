var KullaniciPage = function () {

    var handleTable = function () {
        var table = $('#sample_editable_1');
        table.DataTable({
            "sPaginationType": "simple_numbers",
            "bProcessing": false,
            "ajax": {
                "url": "../kullanicilar/kullanicilist",
                "type": "POST",
                "data": {
                }
            },
            "aaSorting": [[1, "asc"]],
            "fnDrawCallback" : function() {
                $('.make-switch').bootstrapSwitch();
                $('.make-switch').on('switchChange.bootstrapSwitch', function (event, state) {
                    var id = $(this).attr('id');
                    var aktif;
                    if( state===true ){
                        $(this).attr('checked',true);
                        aktif = 1;
                    }else{
                        $(this).attr('checked',false);
                        aktif = 0;
                    }
                    $.getJSON("../kullanicilar/kullanicidurum/"+id+'/'+aktif,function(event){
                        toastr.options = {
                            closeButton: true,debug: false,positionClass: "toast-top-right",onclick: null,
                            showDuration: "1000",hideDuration: "1000",timeOut: "5000",extendedTimeOut: "1000",
                            showEasing: "swing",hideEasing: "linear",showMethod: "fadeIn",hideMethod: "fadeOut"
                        };
                        toastr[event.type](event.text, event.title);
                    });
                });
            },
            "bServerSide": true,
            "language": {
                "emptyTable": "Veri Bulunamadı",
                "info": "  _TOTAL_ Kayıttan _START_ - _END_ Arası Kayıtlar",
                "infoEmpty": "Kayıt Yok",
                "infoFiltered": "( _MAX_ Kayıt İçerisinden Bulunan)",
                "lengthMenu": "Sayfada _MENU_ Kayıt Göster",
                "paginate": {
                    "first": "İlk",
                    "last": "Son",
                    "previous": "Önceki",
                    "next": "Sonraki"
                },
                "search": "Bul:",
                "zeroRecords": "Eşleşen Kayıt Bulunmadı"

            },
            "columns": [
                {data: 'id', name: 'kullanici.id',"class":"id","orderable": true, "searchable": true},
                {data: 'adi_soyadi', name: 'kullanici.adi_soyadi',"orderable": true, "searchable": true},
                {data: 'girisadi', name: 'kullanici.girisadi',"orderable": true, "searchable": true},
                {data: 'grupadi', name: 'grup.grupadi',"orderable": true, "searchable": true},
                {data: 'aktifdurum', name: 'kullanici.aktifdurum',"orderable": true, "searchable": true},
                {data: 'islemler', name: 'islemler',"orderable": false, "searchable": false}
            ],
            "lengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "Hepsi"]
            ]
        });

    };



    return {

        //main function to initiate the module
        init: function () {
            handleTable();
        }

    };
}();