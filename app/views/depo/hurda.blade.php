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
                "url": "{{ URL::to('depo/hurdalist') }}",
                "type": "POST",
                "data": {
                    "hatirlatma_id" : "@if(isset($hatirlatma_id)){{$hatirlatma_id}}@endif" ,
                    "netsiscari_id" : "@if(isset(Auth::user()->netsiscari_id) && count(Auth::user()->netsiscari_id)>0){{Auth::user()->netsiscarilist}}@endif"
                }
            },
            "bServerSide": true,
            "aaSorting": [[5,'desc']],
            "columnDefs": [ { targets: [ 2 ], orderData: [ 2, 0 ] },
                { targets: [ 3 ], orderData: [ 3, 0 ] },
                { targets: [ 4 ], orderData: [ 4, 0 ] },
                { targets: [ 5 ], orderData: [ 5, 0 ] }
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
                {data: 'id', name: 'hurda.id',"class":"id","orderable": true, "searchable": true},
                {data: 'serino', name: 'sayac.serino',"orderable": true, "searchable": true},
                {data: 'sayacadi', name: 'sayacadi.sayacadi',"orderable": true, "searchable": false},
                {data: 'cariadi', name: 'netsiscari.cariadi',"orderable": true, "searchable": false},
                {data: 'nedeni', name: 'hurdanedeni.nedeni',"orderable": true, "searchable": false},
                {data: 'hurdatarihi', name: 'hurda.hurdatarihi',"orderable": true, "searchable": false},
                {data: 'ghurdatarihi', name: 'hurda.ghurdatarihi',"visible": false, "searchable": true},
                {data: 'nsayacadi', name: 'sayacadi.nsayacadi',"visible": false, "searchable": true},
                {data: 'ncariadi', name: 'netsiscari.ncariadi',"visible": false, "searchable": true},
                {data: 'nnedeni', name: 'hurdanedeni.nnedeni',"visible": false, "searchable": true},
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
            '<option value="1">Seri No</option>'+
            '<option value="7">Sayaç Adı</option>'+
            '<option value="8">Cari Adı</option>'+
            '<option value="9">Hurda Nedeni</option>'+
            '<option value="6">Hurda Tarihi</option>'+
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
        $(document).on("click", ".detay", function () {
            $.blockUI();
            var id = $(this).data('id');
            $.getJSON("{{ URL::to('depo/hurdabilgi') }}",{id:id},function(event){
                if(event.durum){
                    var hurda = event.hurda;
                    var sayacgelen = event.sayacgelen;
                    $(".serino").html(hurda.sayac.serino);
                    $(".sayacadi").html(hurda.sayacadi.sayacadi);
                    $(".hurdatarih").html(hurda.hurdatarihi);
                    $(".depotarih").html(sayacgelen.depotarihi);
                    $(".hurdaneden").html(hurda.hurdanedeni.nedeni);
                    if((hurda.kalibrasyon2_id)!=null)
                        $(".hurdazaman").html("2. KALİBRASYON SONRASI HURDAYA AYRILMIŞ");
                    else if((hurda.kalibrasyon_id)!=null)
                        $(".hurdazaman").html("1. KALİBRASYON SONRASI HURDAYA AYRILMIŞ");
                    else if((hurda.ucretlendirilen_id)!=null)
                        $(".hurdazaman").html("MÜŞTERİ ONAYI SONRASI HURDAYA AYRILMIŞ");
                    else if((hurda.arizafiyat_id)!=null)
                        $(".hurdazaman").html("ÜCRETLENDİRME SONRASI HURDAYA AYRILMIŞ");
                    else if((hurda.arizakayit_id)!=null)
                        $(".hurdazaman").html("ARIZA KAYIDI SONRASI HURDAYA AYRILMIŞ");
                    else
                        $(".hurdazaman").html("SAYAÇ KAYIDI SONRASI HURDAYA AYRILMIŞ");
                }else{
                    $('#detay-goster').modal('hide');
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
                        <i class="fa fa-trash-o"></i>Hurda Sayaç Bilgisi
                    </div>
                    <div class="actions">
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                        <tr><th>#</th>
                            <th>Seri No</th>
                            <th>Sayaç Adı</th>
                            <th>Üretim Yeri</th>
                            <th>Hurda Nedeni</th>
                            <th>Hurda Tarihi</th>
                            <th></th><th></th><th></th><th></th>
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
                                    <i class="fa fa-trash-o"></i>Hurda Sayaç Detayı
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Ne Zaman Hurdaya Ayrıldığının Detayı</h3>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Seri No:</label>
                                            <label class="col-xs-8 serino" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Sayaç Adı:</label>
                                            <label class="col-xs-8 sayacadi" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Depo Geliş Tarihi:</label>
                                            <label class="col-xs-8 depotarih" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Hurda Tarihi:</label>
                                            <label class="col-xs-8 hurdatarih" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Hurda Nedeni:</label>
                                            <label class="col-xs-8 hurdaneden" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Ne Zaman Hurdaya Ayrıldı?:</label>
                                            <label class="col-xs-8 hurdazaman" style="padding-top: 7px"></label>
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
