@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Netsis <small>Cari Bilgiler Ekranı</small></h1>
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
    <script src="{{ URL::to('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript" ></script>
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
                "url": "{{ URL::to('subedatabase/carilist') }}",
                "type": "POST",
                "data": {
                    "netsiscari_id" : "@if(isset(Auth::user()->netsiscari_id) && count(Auth::user()->netsiscari_id)>0){{Auth::user()->netsiscarilist}}@endif"
                }
            },
            "bServerSide": true,
            "fnDrawCallback" : function() {
            },
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
            "aaSorting": [[2,'asc']],
            "columns": [
                {data: 'id', name: 'netsiscari.id',"class":"id","orderable": true, "searchable": true},
                {data: 'carikod', name: 'netsiscari.carikod',"orderable": true, "searchable": true},
                {data: 'cariadi', name: 'netsiscari.cariadi',"orderable": true, "searchable": false},
                {data: 'adres', name: 'netsiscari.adres',"orderable": true, "searchable": false},
                {data: 'vergidairesi', name: 'netsiscari.vergidairesi',"orderable": true, "searchable": false},
                {data: 'vergino', name: 'netsiscari.vergino',"orderable": true, "searchable": false},
                {data: 'gcaritipi', name: 'netsiscari.gcaritipi',"orderable": true, "searchable": false},
                {data: 'gdurum', name: 'netsiscari.gdurum',"orderable": true, "searchable": false},
                {data: 'ncariadi', name: 'netsiscari.ncariadi',"visible": false, "searchable": true},
                {data: 'nadres', name: 'netsiscari.nadres',"visible": false, "searchable": true},
                {data: 'nvergidairesi', name: 'netsiscari.nvergidairesi',"visible": false, "searchable": true},
                {data: 'nvergino', name: 'netsiscari.nvergino',"visible": false, "searchable": true},
                {data: 'ncaritipi', name: 'netsiscari.ncaritipi',"visible": false, "searchable": true},
                {data: 'ndurum', name: 'netsiscari.ndurum',"visible": false, "searchable": true},
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
            '<option value="1">Cari Kodu</option>'+
            '<option value="8">Cari Adı</option>'+
            '<option value="9">Adres</option>'+
            '<option value="10">İl</option>'+
            '<option value="11">İlçe</option>'+
            '<option value="12">Cari Tipi</option>'+
            '<option value="13">Durum</option>'+
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
        $(document).ready(function() {
            $('#kriter').select2();
            $("#vergino").inputmask("mask", {mask:"9",repeat:11,greedy:!1});
            $('.sorgula').click(function () {
                var vergino = $("#vergino").val();
                if (vergino !== "") {
                    $.blockUI();
                    $.getJSON("{{ URL::to('subedatabase/carisorgula') }}", {vergino: vergino}, function (event) {
                        if (event.durum) {
                            var efatura = event.efatura;
                            var netsiscari = event.netsiscari;
                            if(efatura===1){
                                $('.durum').text("E-FATURA MUKELLEFİ");
                                $('.durum').css("color","green");
                                $('.caribilgi').removeClass("hide");
                                $('.unvan').text(netsiscari.unvan);
                                $('.cariadi').text(netsiscari.cariadi);
                                $('.carikod').text(netsiscari.carikod);
                                $('.caridurum').text(netsiscari.gdurum);
                                $('.tarih').text(netsiscari.guncellenmetarihi);
                            }else{
                                $('.durum').text("E-FATURA MUKELLEFİ DEĞİL");
                                $('.durum').css("color","red");
                                if(netsiscari===null){
                                    $('.caribilgi').addClass("hide");
                                    $('.unvan').text("");
                                    $('.cariadi').text("");
                                    $('.carikod').text("");
                                    $('.caridurum').text("Kayıtlı Değil");
                                    $('.tarih').text("");
                                }else{
                                    $('.caribilgi').removeClass("hide");
                                    $('.unvan').text(netsiscari.unvan);
                                    $('.cariadi').text(netsiscari.cariadi);
                                    $('.carikod').text(netsiscari.carikod);
                                    $('.caridurum').text(netsiscari.gdurum);
                                    $('.tarih').text(netsiscari.guncellenmetarihi);
                                }
                            }
                        } else {
                            $('.durum').text('');
                            $('.caribilgi').addClass("hide");
                            $('.unvan').text("");
                            $('.cariadi').text("");
                            $('.carikod').text("");
                            $('.caridurum').text("");
                            $('.tarih').text("");
                            toastr[event.type](event.text, event.title);
                        }
                        $.unblockUI();
                    });
                } else {
                    toastr['warning']('Vergi Numarası Girilmemiş', 'Kriter Hatası');
                }
            });
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
                        <i class="fa fa-tag"></i>Netsis Cari Bilgileri
                    </div>
                    <div class="actions">
                        <a class="btn btn-default btn-sm carisorgu" data-toggle="modal" data-target="#carisorgu">
                            <i class="fa fa-search"></i> Cari Sorgula</a>
                        <a href="{{ URL::to('subedatabase/cariekle') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-pencil"></i> Yeni Cari Ekle </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Cari Kodu</th>
                            <th>Cari Adı</th>
                            <th>Adres</th>
                            <th>Vergi Dairesi</th>
                            <th>Vergi No</th>
                            <th>Cari Tipi</th>
                            <th>Durum</th>
                            <th></th><th></th><th></th><th></th><th></th><th></th>
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
    <div class="modal fade" id="carisorgu" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Netsis Cari Sorgulama
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" target="" id="form_sample_2" class="form-horizontal" method="POST" novalidate="novalidate">
                                    <div class="form-body col-xs-12">
                                        <h3 class="form-section col-xs-12">E-Fatura Sorgu Ekranı</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-xs-4">Vergi Numarası:</label>
                                            <div class="col-xs-4">
                                                <input type="text" id="vergino" name="vergino" value="" maxlength="11" data-required="1" class="form-control">
                                            </div>
                                            <a id="sorgula" href="#" type="button" class="btn green sorgula">Sorgula</a>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-xs-4">Durum:</label>
                                            <label class="col-xs-8 durum" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group caribilgi hide">
                                            <div class="form-group col-xs-12">
                                                <label class="control-label col-xs-4">ÜNVANI:</label>
                                                <label class="col-xs-8 unvan" style="padding-top: 7px"></label>
                                            </div>
                                            <div class="form-group col-xs-12">
                                                <label class="control-label col-xs-4">CARİ ADI:</label>
                                                <label class="col-xs-8 cariadi" style="padding-top: 7px"></label>
                                            </div>
                                            <div class="form-group col-xs-12">
                                                <label class="control-label col-xs-4">CARİ KODU:</label>
                                                <label class="col-xs-8 carikod" style="padding-top: 7px"></label>
                                            </div>
                                            <div class="form-group col-xs-12">
                                                <label class="control-label col-xs-4">CARİ DURUM:</label>
                                                <label class="col-xs-8 caridurum" style="padding-top: 7px"></label>
                                            </div>
                                            <div class="form-group col-xs-12">
                                                <label class="control-label col-xs-4">GÜNCELLENME TARİHİ:</label>
                                                <label class="col-xs-8 tarih" style="padding-top: 7px"></label>
                                            </div>
                                        </div>
                                        <div class="form-actions col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-offset-4 col-xs-8">
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