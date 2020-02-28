@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Abone Bilgi<small> Kayıt Ekranı</small></h1>
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
                "url": "{{ URL::to('sube/abonekayitlist') }}",
                "type": "POST",
                "data": {
                    "netsiscari_id" : "@if(isset(Auth::user()->netsiscari_id) && count(Auth::user()->netsiscari_id)>0){{Auth::user()->netsiscarilist}}@endif"
                }
            },
            "bServerSide": true,
            "fnDrawCallback" : function() {
                $(document).on("click", ".delete", function () {
                    var id = $(this).data('id');
                    $(".modal-footer #sayacid").attr('href',"{{ URL::to('sube/abonekayitsil') }}/"+id );
                });
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
            "columns": [
                {data: 'id', name: 'abone.id',"class":"id","orderable": true, "searchable": true},
                {data: 'adisoyadi', name: 'abone.adisoyadi',"orderable": true, "searchable": false},
                {data: 'tckimlikno', name: 'abone.tckimlikno',"orderable": true, "searchable": true},
                {data: 'yeradi', name: 'uretimyer.yeradi',"orderable": true, "searchable": false},
                {data: 'telefon', name: 'abone.telefon',"orderable": true, "searchable": true},
                {data: 'faturaadresi', name: 'abone.faturaadresi',"orderable": true, "searchable": false},
                {data: 'nadisoyadi', name: 'abone.nadisoyadi',"visible": false, "searchable": true},
                {data: 'nyeradi', name: 'uretimyer.nyeradi',"visible": false, "searchable": true},
                {data: 'nfaturaadresi', name: 'abone.nfaturaadresi',"visible": false, "searchable": true},
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
            '<option value="6">Adı Soyadı</option>'+
            '<option value="2">TC No</option>'+
            '<option value="7">Yeri</option>'+
            '<option value="4">Telefon</option>'+
            '<option value="8">Adresi</option>'+
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
        table.on('click', 'tr', function () {
            if(oTable.cell( $(this).children('.id')).data()!==undefined){
                $(this).toggleClass("active");
                if($(this).hasClass('active'))
                {
                    $("tbody tr").removeClass("active");
                    $(this).addClass("active");
                }else{
                    $(this).removeClass("active");
                }
            }
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        $(document).ready(function() {
            $('#kriter').select2();
            $('.serinosorgula').click(function () {
                var serino = $('#serino').val();
                if (serino !== "") {
                    $.blockUI();
                    $.getJSON("{{ URL::to('sube/abonesorgula') }}",{serino:serino}, function (event) {
                        if (event.durum)
                        {
                            var abone = event.abone;
                            var search=jQuery.fn.DataTable.ext.type.search.string(abone.adisoyadi);
                            $('#kriter').select2('val','6');
                            $('#sample_editable_1_filter input[type=search]').val(search);
                            $('#search').val(search);
                            oTable.search( '' ).columns().search( '' );
                            oTable.column("0").search(abone.id)
                                .column("6").search(search).draw();
                        } else {
                            $('#kriter').select2('val','');
                            $('#sample_editable_1_filter input[type=search]').val('');
                            $('#search').val('');
                            oTable.columns().search( '' );
                            oTable.search('').draw();
                            toastr[event.type](event.text,event.title);
                        }
                        $.unblockUI();
                    });
                }else{
                    toastr["warning"]("Seri No Alanı Boş Geçilmiş!","Alan Boş Geçilmiş");
                }
            });
            $('.getir').click(function () {
                var serino = $('#sayacserino').val();
                if (serino !== "") {
                    $.blockUI();
                    $.getJSON("{{ URL::to('sube/abonesorgula') }}",{serino:serino}, function (event) {
                        if (event.durum)
                        {
                            var abone = event.abone;
                            var abonetahsis = event.abonetahsis;
                            var newRow="";
                            newRow+= '<div class="form-group">' +

                        '<div class="form-group col-xs-12">'+
                        '<label class="control-label col-sm-2 col-xs-4">Cari İsim:</label>'+
                        '<label class="col-xs-8" style="padding-top: 9px">'+abone.netsiscari.carikod+' - '+abone.netsiscari.cariadi+'</label>'+
                        '</div>'+
                        '<div class="form-group col-sm-6 col-xs-12">'+
                        '<label class="control-label col-sm-4 col-xs-3">Adı Soyadı:</label>'+
                        '<label class="col-xs-8" style="padding-top: 9px">'+abone.adisoyadi+'</label>'+
                        '</div>'+
                        '<div class="form-group col-sm-6 col-xs-12">'+
                        '<label class="control-label col-sm-4 col-xs-3">Kayıt Yeri:</label>'+
                        '<label class="col-xs-8" style="padding-top: 9px">'+abone.uretimyer.yeradi+'</label>'+
                        '</div>'+
                        '<div class="form-group col-sm-6 col-xs-12">'+
                        '<label class="control-label col-sm-4 col-xs-3">Vergi Dairesi:</label>'+
                        '<label class="col-xs-8" style="padding-top: 9px">'+abone.vergidairesi+'</label>'+
                        '</div>'+
                        '<div class="form-group col-sm-6 col-xs-12">' +
                        '<label class="control-label col-sm-4 col-xs-3">TC Kimlik / Vergi No:</label>'+
                        '<label class="col-xs-8" style="padding-top: 9px">'+abone.tckimlikno+'</label>'+
                        '</div>'+
                        '<div class="form-group col-sm-6 col-xs-12">'+
                        '<label class="control-label col-sm-4 col-xs-3">Abone No:</label>'+
                        '<label class="col-xs-8" style="padding-top: 9px">'+abone.abone_no+'</label>'+
                        '</div>'+
                        '<div class="form-group col-sm-6 col-xs-12">'+
                        '<label class="control-label col-sm-4 col-xs-3">Telefonu:</label>'+
                        '<label class="col-xs-8" style="padding-top: 9px">'+abone.telefon+'</label>'+
                        '</div>'+
                        '<div class="form-group col-xs-12">'+
                        '<label class="control-label col-sm-2 col-xs-4">Fatura Adresi:</label>'+
                        '<label class="col-xs-8" style="padding-top: 9px">'+abone.faturaadresi+'</label>'+
                        '</div>' +
                        '<div class="form-group col-sm-6 col-xs-12">'+
                        '<label class="control-label col-sm-4 col-xs-3">Fatura İl:</label>'+
                        '<label class="col-xs-8" style="padding-top: 9px">'+abone.il.adi+'</label>'+
                        '</div>' +
                        '<div class="form-group col-sm-6 col-xs-12">'+
                        '<label class="control-label col-sm-4 col-xs-3">Fatura İlçe:</label>'+
                        '<label class="col-xs-8" style="padding-top: 9px">'+abone.ilce.adi+'</label>'+
                        '</div>';
                            $('.abonebilgileri').append(newRow);
                            $(".abonedegisikligi").attr('href',"{{ URL::to('sube/abonedegisikligi') }}/"+abonetahsis.id );
                            $(".abonedegisikligi").removeClass("hide");
                        } else {
                            $('.abonebilgileri').html('');
                            $(".abonedegisikligi").addClass("hide");
                            toastr[event.type](event.text,event.title);
                        }
                        $.unblockUI();
                    });
                }else{
                    $('.abonebilgileri').html('');
                    $(".abonedegisikligi").addClass("hide");
                    toastr["warning"]("Seri No Alanı Boş Geçilmiş!","Alan Boş Geçilmiş");
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
                        <i class="fa fa-tag"></i>Kayıtlı Abone Bilgileri
                    </div>
                    <div class="actions">
                        <a class="btn btn-default btn-sm" data-toggle="modal" data-target="#serinosorgula">
                            <i class="fa fa-search"></i> Seri No Sorgula</a>
                        <a class="btn btn-default btn-sm" data-toggle="modal" data-target="#abonedegisikligi">
                            <i class="fa fa-pencil-square-o"></i> Abone Değişikliği</a>
                        <a href="{{ URL::to('sube/abonekayitekle') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-pencil"></i> Yeni Abone Kaydet </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Adı Soyadı</th>
                                <th>TC No</th>
                                <th>Yeri</th>
                                <th>Telefon</th>
                                <th>Fatura Adresi</th>
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
    <div class="modal fade" id="serinosorgula" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Seri Numarası Sorgulama Ekranı
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" method="POST" novalidate="novalidate">
                                    <div class="form-body col-xs-12">
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <h3 class="form-section col-xs-12">Seri Numarası Sorgulama Ekranı</h3>
                                        <div class="form-group col-xs-12">
                                            <label class="col-sm-2 col-xs-4 control-label">Seri Numarası:<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-xs-8">
                                                <input type="text" id="serino" name="serino" value="{{ Input::old('serino')}}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-offset-3 col-xs-9">
                                                    <button type="button" class="btn green serinosorgula" data-dismiss="modal">Bul</button>
                                                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
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
    <div class="modal fade" id="abonedegisikligi" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Abone Değişikliği Ekranı
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_3" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body col-xs-12">
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <h3 class="form-section col-xs-12">Abone Değişikliği Ekranı</h3>
                                        <div class="form-group col-xs-12">
                                            <label class="col-sm-2 col-xs-4 control-label">Seri Numarası:<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-xs-3">
                                                <input type="text" id="sayacserino" name="sayacserino" value="{{ Input::old('sayacserino')}}" data-required="1" class="form-control">
                                            </div>
                                            <div class="col-xs-2"><a class="btn green getir">Bilgileri Getir</a></div>
                                        </div>
                                        <div class="form-group abonebilgileri col-xs-12">

                                        </div>
                                        <div class="form-actions col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-offset-3 col-xs-9">
                                                    <a href="{{ URL::to('sube/abonedegisikligi') }}" class="btn green abonedegisikligi hide">Abone Değişikliği</a>
                                                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
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
                    <h4 class="modal-title">Abone Kayıdı Silinecek</h4>
                </div>
                <div class="modal-body">
                    Seçilen Abone Kayıdını Silmek İstediğinizden Emin Misiniz?
                </div>
                <div class="modal-footer">
                    <a id="sayacid" href="" type="button" class="btn blue">Sil</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
@stop
