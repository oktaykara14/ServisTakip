@extends('layout.master')

@section('page-title')
    <div class="page-title">
        <h1>Kullanıcı <small>Bilgi Ekranı</small></h1>
    </div>
@stop

@section('page-plugins')
    <link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::to('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css">
@stop

@section('page-styles')
@stop

@section('page-js')
    <script src="{{ URL::to('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/datatables/datatables.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
@stop

@section('scripts')
    <script>
        jQuery(document).ready(function() {
           Metronic.init(); // init metronic core componets
           Layout.init(); // init layout
           Demo.init(); // init demo features
           QuickSidebar.init(); // init quick sidebar
        });
    </script>
    <script>
        jQuery.fn.DataTable.ext.type.search.string = function(data) {
            return !data ? '' : typeof data === 'string' ? data.replace(/Ç/g, 'c').replace(/İ/g, 'i').replace(/Ğ/g, 'g').replace(/Ö/g, 'o').replace(/Ş/g, 's').replace(/Ü/g, 'u').toLowerCase().replace(/ç/g, 'c').replace(/ı/g, 'i').replace(/ğ/g, 'g').replace(/ö/g, 'o').replace(/ş/g, 's').replace(/ü/g, 'u') : data;
        };
        var table = $('#sample_editable_1');
        var oTable = table.DataTable({
            "sPaginationType": "simple_numbers",
            "bProcessing": true,
            "ajax": {
                "url": "{{ URL::to('kullanicilar/kullanicilist') }}",
                "type": "POST",
                "data": {
                }
            },
            "bServerSide": true,
            "fnDrawCallback" : function() {
                $('.make-switch').bootstrapSwitch();
                $('.make-switch').on('switchChange.bootstrapSwitch', function(event, state) {
                    var id = $(this).attr('id');
                    var aktif;
                    if( state===true ){
                        $(this).attr('checked',true);
                        aktif = 1;
                    }else{
                        $(this).attr('checked',false);
                        aktif = 0;
                    }
                    $.getJSON("{{ URL::to('kullanicilar/kullanicidurum') }}",{id:id,durum:aktif},function(event){
                        toastr.options = {
                            closeButton: true,debug: false,positionClass: "toast-top-right",onclick: null,
                            showDuration: "1000",hideDuration: "1000",timeOut: "5000",extendedTimeOut: "1000",
                            showEasing: "swing",hideEasing: "linear",showMethod: "fadeIn",hideMethod: "fadeOut"
                        };
                        toastr[event.type](event.text, event.title);
                    });
                });
            },
            "aaSorting": [[1,'asc']],
            "columnDefs": [ { targets: [ 1 ], orderData: [ 1, 0 ] },
                { targets: [ 2 ], orderData: [ 2, 0 ] },
                { targets: [ 3 ], orderData: [ 3, 0 ] },
                { targets: [ 4 ], orderData: [ 4, 0 ] }
            ],
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
                "zeroRecords": "Eşleşen Kayıt Bulunmadı",
                "processing": "<h1><i class='fa fa-spinner fa-spin icon-lg-processing fa-fw'></i>İşlem Devam Ediyor...</h1>"

            },
            "columns": [
                {data: 'id', name: 'kullanici.id',"class":"id","orderable": true, "searchable": true},
                {data: 'adi_soyadi', name: 'kullanici.adi_soyadi',"orderable": true, "searchable": false},
                {data: 'girisadi', name: 'kullanici.girisadi',"orderable": true, "searchable": true},
                {data: 'grupadi', name: 'grup.grupadi',"orderable": true, "searchable": false},
                {data: 'aktifdurum', name: 'kullanici.aktifdurum',"orderable": true, "searchable": true},
                {data: 'nadi_soyadi', name: 'kullanici.nadi_soyadi',"visible": false, "searchable": true},
                {data: 'ngrupadi', name: 'grup.ngrupadi',"visible": false, "searchable": true},
                {data: 'islemler', name: 'islemler',"orderable": false, "searchable": false}
            ],
            "lengthMenu": [
                [10, 15, 20, 99999999],
                [10, 15, 20, "Hepsi"]
            ],
            "searchDelay": 0,
            "bFilter": true,
            "stateSave":true
        });
        $('<label>Kriter: </label><select style="height: 34px;margin-left: 5px;border-radius: 4px;padding-top:2px;padding-right: 10px" id="kriter" tabindex="-1" title="" class="select2me">'+
            '<option value="">Tamamı</option>'+
            '<option value="0">Id</option>'+
            '<option value="5">Adı Soyadı</option>'+
            '<option value="2">Giriş Adı</option>'+
            '<option value="6">Grubu</option>'+
            '<option value="4">Durum</option>'+
            '</select><input class="hide" id="search">').insertBefore('#sample_editable_1_filter label');
        $('#sample_editable_1_filter input[type=search]').unbind();
        $('#sample_editable_1_filter input[type=search]').bind('keyup', function(e) {
            if(e.keyCode === 13) {
                var kriter=$('#kriter').val();
                var search=jQuery.fn.DataTable.ext.type.search.string(this.value);
                $('#search').val(search);
                if(kriter!==""){
                    oTable.search( '' ).columns().search( '' );
                    oTable.column(kriter).search(search).draw();
                }
                else{
                    oTable.columns().search( '' );
                    oTable.search(search).draw();
                }
            }
        });
        var state = oTable.state.loaded();
        if (state) {
            var search=state.search;
            if(search.search){
                var globalSearch=search.search;
                $('#kriter').val('');
                $('#sample_editable_1_filter input[type=search]').val(globalSearch);
                $('#search').val(globalSearch);
            }else{
                oTable.columns().eq(0).each(function (colIdx) {
                    var colSearch = state.columns[colIdx].search;
                    if (colSearch.search) {
                        $('#kriter').val(colIdx);
                        $('#sample_editable_1_filter input[type=search]').val(colSearch.search);
                        $('#search').val(colSearch.search);
                    }
                });
            }
        }
        table.on('draw.dt', function() {
            $('#sample_editable_1_filter input[type=search]').val($('#search').val());
        });
    </script>
    <script>
    $(document).on("click", ".delete", function () {
         var id = $(this).data('id');
         $(".modal-footer #sayacid").attr('href',"{{ URL::to('kullanicilar/kullanicisil') }}/"+id);
    });
    $(document).ready(function() {
        $('#kriter').select2();
    });
    </script>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN TABLE PORTLET-->
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-user"></i>Kullanıcılar
                    </div>
                    <div class="actions">
                        <a href="{{ URL::to('kullanicilar/kullaniciekle') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-pencil"></i> Yeni Kullanıcı Ekle </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Adı Soyadı</th>
                                <th>Giriş Adı</th>
                                <th>Grubu</th>
                                <th>Durum</th>
                                <th></th><th></th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- END TABLE PORTLET-->
        </div>
    </div>
@stop

@section('modal')
    <div class="modal fade" id="portlet-delete" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
                <div class="modal-content">
                        <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h4 class="modal-title">Kullanıcı Silinecek</h4>
                        </div>
                        <div class="modal-body">
                                 Seçilen Kullanıcıyı Silmek İstediğinizden Emin Misiniz?
                        </div>
                        <div class="modal-footer">
                                <a id="sayacid" href="" type="button" class="btn blue">Sil</a>
                                <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                        </div>
                </div>
                <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@stop
