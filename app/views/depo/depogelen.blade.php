@extends('layout.master')

@section('page-title')
    <div class="page-title">
        <h1>Depo <small> Gelen Sayaç Bilgi Ekranı</small></h1>
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
                "url": "{{ URL::to('depo/depogelenlist') }}",
                "type": "POST",
                "data": {
                    "hatirlatma_id" : "@if(isset($hatirlatma_id)){{$hatirlatma_id}}@endif" ,
                    "netsiscari_id" : "@if(isset(Auth::user()->netsiscari_id) && count(Auth::user()->netsiscari_id)>0){{Auth::user()->netsiscarilist}}@endif"
                }
            },
            "bServerSide": true,
            "fnDrawCallback" : function() {
                $(document).on("click", ".delete", function(){
                    var id = $(this).data('id');
                    $(".modal-footer #gelenid").attr('href',"{{ URL::to('depo/depogelensil') }}/"+id );
                });
            },
            "aaSorting": [[7,'desc']],
            "columnDefs": [ { targets: [ 2 ], orderData: [ 2, 0 ] },
                { targets: [ 3 ], orderData: [ 3, 0 ] },
                { targets: [ 4 ], orderData: [ 4, 0 ] },
                { targets: [ 5 ], orderData: [ 5, 0 ] },
                { targets: [ 6 ], orderData: [ 6, 0 ] },
                { targets: [ 7 ], orderData: [ 7, 0 ] }
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
                {data: 'id', name: 'depogelen.id',"class":"id","orderable": true, "searchable": true},
                {data: 'fisno', name: 'depogelen.fisno',"orderable": true, "searchable": true},
                {data: 'cariadi', name: 'netsiscari.cariadi',"orderable": true, "searchable": false},
                {data: 'stokadi', name: 'servisstokkod.stokadi',"orderable": true, "searchable": false},
                {data: 'adet', name: 'depogelen.adet',"orderable": true, "searchable": true},
                {data: 'servisadi', name: 'servis.servisadi',"orderable": true, "searchable": false},
                {data: 'kullanici', name: 'depogelen.kullanici',"orderable": true, "searchable": false},
                {data: 'tarih', name: 'depogelen.tarih',"orderable": true, "searchable": false},
                {data: 'gtarih', name: 'depogelen.gtarih',"visible": false, "searchable": true},
                {data: 'ncariadi', name: 'netsiscari.ncariadi',"visible": false, "searchable": true},
                {data: 'nstokadi', name: 'servisstokkod.nstokadi',"visible": false, "searchable": true},
                {data: 'nservisadi', name: 'servis.nservisadi',"visible": false, "searchable": true},
                {data: 'nkullanici', name: 'depogelen.nkullanici',"visible": false, "searchable": true},
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
            '<option value="1">Fiş No</option>'+
            '<option value="9">Cari Adı</option>'+
            '<option value="10">Stok Adı</option>'+
            '<option value="4">Adet</option>'+
            '<option value="11">Servis Adı</option>'+
            '<option value="12">Netsis Kullanıcı</option>'+
            '<option value="8">Giriş Tarihi</option>'+
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
        oTable.on('change', 'tbody tr .checkboxes', function () {
            if($(this).prop('checked'))
            {
                $("tbody tr .checkboxes").prop('checked', false);
                $("tbody tr").removeClass("active");
                $(this).prop('checked',true);
                $(this).parents('tr').addClass("active");
            }else{
                $(this).prop('checked',false);
                $(this).parents('tr').removeClass("active");
            }
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        var table2 = $('#sample_editable_2');
        var oTable2 = table2.DataTable({
            "sPaginationType": "simple_numbers",
            "searching": false,
            "ordering": false,
            "bProcessing": false,
            "sAjaxSource": "",
            "bServerSide": false,
            "bInfo": true,
            "bPaginate": true,
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
            "lengthMenu": [
                [5, 10],
                [5, 10]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_2_wrapper');
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        $(document).on("click", ".detay", function () {
            $.blockUI();
            var id = $(this).data('id');
            $.getJSON("{{ URL::to('depo/gelenbilgi') }}",{id:id},function(event){
                if(event.durum){
                    var depogelen = event.depogelen;
                    var sayacgelen = event.sayacgelen;
                    $(".cariadi").html(depogelen.netsiscari.cariadi);
                    $(".stokadi").html(depogelen.servisstokkod.stokadi);
                    $(".adet").html(depogelen.adet);
                    $(".giristarihi").html(depogelen.tarih);
                    oTable2.clear().draw();
                    $.each(sayacgelen,function(index) {
                        var servistakip = sayacgelen[index].servistakip;
                        var serino =  sayacgelen[index].serino+(servistakip.eskiserino ? " ("+servistakip.eskiserino+")" : "");
                        var uretimyer = sayacgelen[index].uretimyer.yeradi;
                        var sayacadi = sayacgelen[index].sayacadi.sayacadi;
                        var sayaccap = sayacgelen[index].sayaccap.capadi;
                        var tarih = sayacgelen[index].kayittarihi;
                        oTable2.row.add([sayacgelen[index].id,serino,uretimyer,sayacadi ,sayaccap,tarih])
                                .draw();
                    });
                }else{
                    $('#detay-goster').modal('hide');
                    toastr[event.type](event.text,event.title);
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
                        <i class="fa fa-check"></i>Depo Gelen Bilgisi
                    </div>
                    <div class="actions">
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Fiş No</th>
                            <th>Cari Adı</th>
                            <th>Stok Adı</th>
                            <th>Adet</th>
                            <th>Servis Adı</th>
                            <th>Netsis Kullanıcı</th>
                            <th>Giriş Tarihi</th>
                            <th></th><th></th><th></th><th></th><th></th>
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
    <div class="modal fade" id="detay-goster" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-check"></i>Eklenen Sayaçlar
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Eklenen Sayaçların Detayı</h3>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Cari Adı:</label>
                                            <label class="col-xs-8 cariadi" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Stok Adı:</label>
                                            <label class="col-xs-8 stokadi" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Adet:</label>
                                            <label class="col-xs-8 adet" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Depo Giriş Tarihi:</label>
                                            <label class="col-xs-8 giristarihi" style="padding-top: 7px"></label>
                                        </div>

                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_2">
                                                <thead>
                                                <tr>
                                                    <th class="">#</th>
                                                    <th>Seri No</th>
                                                    <th>Üretim Yeri</th>
                                                    <th>Sayaç Adı</th>
                                                    <th>Sayaç Çapı</th>
                                                    <th>Kayıt Tarihi</th>
                                                </tr>
                                                </thead>
                                            </table>
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
    <div class="modal fade" id="portlet-delete" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Depo Girilen Sayaçlar Silinecek</h4>
                </div>
                <div class="modal-body">
                    Seçilen Depo Giriş Kayıdını Silmek İstediğinizden Emin Misiniz?
                </div>
                <div class="modal-footer">
                    <a id="gelenid" href="" type="button" class="btn blue">Sil</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@stop
