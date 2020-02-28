@extends('layout.master')

@section('page-title')
    <div class="page-title">
        <h1>Kullanıcıların İşlem Bilgileri<small> Bilgi Ekranı</small></h1>
    </div>
@stop

@section('page-plugins')
    <link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
@stop

@section('page-styles')
@stop

@section('page-js')
    <script src="{{ URL::to('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/datatables/datatables.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
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
                "url": "{{ URL::to('digerdatabase/islemlist') }}",
                "type": "POST",
                "data": {
                }
            },
            "bServerSide": true,
            "fnDrawCallback" : function() {
            },
            "aaSorting": [[0,'desc']],
            "columnDefs": [ { targets: [ 1 ], orderData: [ 1, 0 ] },
                { targets: [ 2 ], orderData: [ 2, 0 ] },
                { targets: [ 3 ], orderData: [ 3, 0 ] }
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
                {data: 'id', name: 'islem.id',"class":"id","orderable": true, "searchable": true},
                {data: 'adi_soyadi', name: 'kullanici.adi_soyadi',"orderable": true, "searchable": false},
                {data: 'aciklama', name: 'islem.aciklama',"orderable": true, "searchable": false},
                {data: 'updated_at', name: 'islem.updated_at',"orderable": true, "searchable": false},
                {data: 'gdurumtarihi', name: 'islem.gdurumtarihi',"visible": false, "searchable": true},
                {data: 'nadi_soyadi', name: 'kullanici.nadi_soyadi',"visible": false, "searchable": true},
                {data: 'naciklama', name: 'islem.naciklama',"visible": false, "searchable": true},
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
            '<option value="5">Kullanıcı</option>'+
            '<option value="6">Yapılan İşlem</option>'+
            '<option value="4">Tarih</option>'+
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
        var tableWrapper = jQuery('#sample_editable_1_wrapper');
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        $(document).on("click", ".detay", function () {
            var id = $(this).data('id');
            $.blockUI();
            $.getJSON("{{ URL::to('digerdatabase/islemdetay') }}",{id:id},function(event){
                if(event.durum){
                    var islem = event.islem;
                    $(".kullanici").html(islem.kullanici.adi_soyadi);
                    $(".tarih").html(islem.tarih);
                    $(".islem").html(islem.aciklama);
                    $(".islemdetay").html(islem.ayrinti);
                }else{
                    $('#detay').modal('hide');
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
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
            <div class="portlet box">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-tag"></i>Kullanıcı İşlem Bilgileri
                    </div>
                    <div class="actions">
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Kullanıcı</th>
                            <th>Yapılan İşlem</th>
                            <th>Tarih</th>
                            <th></th><th></th><th></th>
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
    <div class="modal fade" id="detay" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>İşlem Detayı
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" data-action="" id="form_sample_2" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">İşlem Detayı</h3>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Kullanıcı:</label>
                                            <label class="col-md-3 col-xs-12 kullanici" style="padding-top: 9px"></label>
                                            <label class="control-label col-md-2 col-xs-12">Kayıt Tarihi:</label>
                                            <label class="col-md-3 col-xs-12 tarih" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Yapılan İşlem:</label>
                                            <label class="col-md-10 col-xs-12 islem" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Ayrıntılı Bilgi:</label>
                                            <label class="col-md-10 col-xs-12 islemdetay" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn default" data-dismiss="modal">Tamam</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop