@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Gaz Servis Mekanik <small>Arıza Kayıt Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
    <link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
@stop

@section('page-styles')
@stop

@section('page-js')
    <script src="{{ URL::to('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/datatables/datatables.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.tr.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/jquery.quicksearch.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>
@stop

@section('page-script')
    <script src="{{ URL::to('pages/mekanikgaz/form-validation-8.js') }}"></script>
@stop

@section('scripts')
    <script>
        jQuery(document).ready(function() {
           Metronic.init(); // init metronic core componets
           Layout.init(); // init layout
           Demo.init(); // init demo features
           QuickSidebar.init(); // init quick sidebar
           FormValidationGazServis.init();
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
                "url": "{{ URL::to('mekanikgaz/arizakayitlist') }}",
                "type": "POST",
                "data": {
                    "hatirlatma_id" : "@if(isset($hatirlatma_id)){{$hatirlatma_id}}@endif" ,
                    "netsiscari_id" : "@if(isset(Auth::user()->netsiscari_id) && count(Auth::user()->netsiscari_id)>0){{Auth::user()->netsiscarilist}}@endif"
                }
            },
            "bServerSide": true,
            "fnDrawCallback" : function() {
                $(document).on("click", ".delete", function () {
                    var id = $(this).data('id');
                    $(".modal-footer #sayacid").attr('href',"{{ URL::to('mekanikgaz/arizakayitsil') }}/"+id );
                });
            },
            "aaSorting": [[6,'desc']],
            "columnDefs": [ { targets: [ 2 ], orderData: [ 2, 0 ] },
                { targets: [ 3 ], orderData: [ 3, 0 ] },
                { targets: [ 4 ], orderData: [ 4, 0 ] },
                { targets: [ 5 ], orderData: [ 5, 0 ] },
                { targets: [ 6 ], orderData: [ 6, 0 ] }
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
                {data: 'id', name: 'arizakayit.id',"class":"id","orderable": true, "searchable": true},
                {data: 'serino', name: 'sayacgelen.serino',"orderable": true, "searchable": true},
                {data: 'sayacadi', name: 'sayacadi.sayacadi',"orderable": true, "searchable": false},
                {data: 'yeradi', name: 'uretimyer.yeradi',"orderable": true, "searchable": false},
                {data: 'gdurum', name: 'arizakayit.gdurum',"orderable": true, "searchable": false},
                {data: 'adi_soyadi', name: 'kullanici.adi_soyadi',"orderable": true, "searchable": false},
                {data: 'arizakayittarihi', name: 'arizakayit.arizakayittarihi',"orderable": true, "searchable": true},
                {data: 'eskiserino', name: 'servistakip.eskiserino',"visible": false, "searchable": true},
                {data: 'garizakayittarihi', name: 'arizakayit.garizakayittarihi',"visible": false, "searchable": true},
                {data: 'nsayacadi', name: 'sayacadi.nsayacadi',"visible": false, "searchable": true},
                {data: 'nyeradi', name: 'uretimyer.nyeradi',"visible": false, "searchable": true},
                {data: 'ndurum', name: 'arizakayit.ndurum',"visible": false, "searchable": true},
                {data: 'nadi_soyadi', name: 'kullanici.nadi_soyadi',"visible": false, "searchable": true},
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
            '<option value="1">SeriNo</option>'+
            '<option value="9">Sayaç Adı</option>'+
            '<option value="10">Üretim Yeri</option>'+
            '<option value="11">Durum</option>'+
            '<option value="12">Kayıt Eden</option>'+
            '<option value="8">Kayıt Tarihi</option>'+
            '<option value="7">Eski SeriNo</option>'+
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
        var arizalar,yapilanlar,degisenler,uyarilar;
        $('.rapor').click(function() {
            var arizaid = $('#sample_editable_1 .active .id').text();
            if(arizaid !=null){
                $.extend({
                    redirectPost: function(location, args)
                    {
                        var form = '';
                        $.each( args, function( key, value ) {
                            value = value.split('"').join('\"');
                            form += '<input type="hidden" name="'+key+'" value="'+value+'">';
                        });
                        $('<form action="' + location + '" method="POST" target="_blank">' + form + '</form>').appendTo($(document.body)).submit();
                    }
                });
                var redirect = "";
                $.redirectPost(redirect, {arizaid: arizaid,ireport:'1'});
            }
        });
        $('#depogelen').on('change',function () {
            var id = $(this).val();
            $("#sayaclar").empty();
            if (id !== "") {
                $.blockUI();
                $.getJSON("{{ URL::to('mekanikgaz/depogelensayaclar') }}", {depogelenid: id}, function (event) {
                    if (event.durum) {
                        var sayacgelenler = event.sayacgelenler;
                        var sayacadi = event.sayacadi;
                        var tipmodel = event.tipmodel;
                        $(".sayacadi").text(tipmodel);
                        $("#sayacadi").val(sayacadi.id).trigger("change");
                        $.each(sayacgelenler, function (index) {
                            $("#sayaclar").append('<option value="' + sayacgelenler[index].id + '"> ' + sayacgelenler[index].serino + '</option>');
                        });
                        if (sayacadi.sayacozellik) {
                            $("#baglanticap").val(sayacadi.sayacozellik.baglanticapi);
                            $("#pmax").val(sayacadi.sayacozellik.pmax);
                            $("#qmax").val(sayacadi.sayacozellik.qmax);
                            $("#qmin").val(sayacadi.sayacozellik.qmin);
                        }else{
                            $("#baglanticap").val('');
                            $("#pmax").val('');
                            $("#qmax").val('');
                            $("#qmin").val('');
                        }
                        $("#sayaclar > option").prop("selected","selected");
                        $("#sayaclar").trigger("change");
                        $("#arizalar").multiSelect("refresh");
                        $("#degisenler").multiSelect("refresh");
                        $("#yapilanlar").multiSelect("refresh");
                        $("#uyarilar").multiSelect("refresh");
                    } else {
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            }
            $("#sayaclar").select2();
        });
        $('#degisenler').on('change', function () {
            $(this).valid();
        });
        $('#yapilanlar').on('change', function () {
            $(this).valid();
        });
        $('#uyarilar').on('change', function () {
            $(this).valid();
        });
        $('#arizalar').on('change', function () {
            var secilenler = $(this).val();
            var garanti = $('#garanti').val();
            var durum = -1;
            if (secilenler === null) {
                $('#garanti').select2('val', garanti);
            } else {
                $.each(secilenler, function (index) {
                    var garantidurum = $('#arizalar option[value="' + secilenler[index] + '"]').data('id');
                    if (garantidurum === 0) //garanti dışıysa
                    {
                        durum = 0;
                    } else if (garantidurum === 1 && durum !== 0) {
                        durum = 1;
                    }
                });
                if (durum === -1) {
                    $('#garanti').select2('val', garanti);
                } else {
                    $('#garanti').select2('val', durum);
                }
            }
            $(this).valid();
        });

        $('#sayacadi').on('change', function () {
            var id = $(this).val();
            if (id !== "") {
                $.blockUI();
                $.getJSON("{{ URL::to('mekanikgaz/sayacparca') }}",{sayacadiid:id}, function (event) {
                    if (event.durum) //sayaç parçalarını listede göster
                    {
                        var parcalar = event.sayacparcalar.parca;
                        var sayactip = event.sayactip;
                        $("#degisenler").empty();
                        $.each(parcalar, function (index) {
                            if (parcalar[index].sabit === '1') {
                                $("#degisenler").append('<option value="' + parcalar[index].id + '" selected> ' + parcalar[index].tanim + '</option>');
                            } else {
                                $("#degisenler").append('<option value="' + parcalar[index].id + '"> ' + parcalar[index].tanim + '</option>');
                            }
                        });
                        $("#degisenler").multiSelect("refresh");
                        degisenler = $("#degisenler").val();
                        $("#degisenlist").val(degisenler);
                        if(sayactip.tipadi==="ROTARY" || sayactip.tipadi==="TURBIN"){
                            $('.sertifika').removeClass('hide');
                        }else{
                            $('.sertifika').addClass('hide');
                        }
                    } else {
                        $("#degisenler").empty();
                        $("#degisenler").multiSelect("refresh");
                        degisenler = $("#degisenler").val();
                        $("#degisenlist").val(degisenler);
                        toastr[event.type](event.text,event.title);
                    }
                    $("#degisenler").valid();
                    $.unblockUI();
                });
                $.blockUI();
            }else{
                $("#degisenler").empty();
                $("#degisenler").multiSelect("refresh");
                degisenler = $("#degisenler").val();
                $("#degisenlist").val(degisenler);
                $("#degisenler").valid();
            }
            $(this).valid();
        });
        $('#formsubmit').click(function () {
            $('#form_sample_2').submit();
        });
        $('input[type="checkbox"]').on('click', function() {
            var $box=$(this);
            $("#sayacdurum").val($box.val());
            if($box.parent().hasClass('checked')){
                $('input[name="' + this.name + '"]').parent().removeClass('checked');
                $box.parent().addClass('checked');
            }
        });
        $('#kriter').select2();
        $("#arizalar").multiSelect("refresh");
        $("#degisenler").multiSelect("refresh");
        $("#yapilanlar").multiSelect("refresh");
        $("#uyarilar").multiSelect("refresh");
        $("select").on("select2-close", function () { $(this).valid(); });
        arizalar=$('#arizalar').val();
        $('#arizalist').val(arizalar);
        degisenler=$('#degisenler').val();
        $('#degisenlist').val(degisenler);
        yapilanlar=$('#yapilanlar').val();
        $('#yapilanlist').val(yapilanlar);
        uyarilar=$('#uyarilar').val();
        $('#uyarilist').val(uyarilar);
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
                        <i class="fa fa-tag"></i>Sisteme Girilen Gaz Arıza Kayıtları
                    </div>
                    <div class="actions">
                        <a class="btn btn-default btn-sm topluekle" data-toggle="modal" data-target="#topluekle">
                            <i class="fa fa-print fa-pencil-square-o"></i> Toplu Arıza Kayıdı Ekle</a>
                        <a class="btn btn-default btn-sm rapor">
                            <i class="fa fa-search"></i> Servis Raporu </a>
                        <a href="{{ URL::to('mekanikgaz/arizakayitekle') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-pencil"></i> Yeni Arıza Kayıdı Ekle </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>SeriNo</th>
                                <th>Sayaç Adı</th>
                                <th>Üretim Yeri</th>
                                <th>Durum</th>
                                <th>Kayıt Eden</th>
                                <th>Kayıt Tarihi</th>
                                <th></th><th></th><th></th>
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
    <div class="modal fade" id="topluekle" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Toplu Arıza Kayıdı Ekleme Ekranı
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{ URL::to('mekanikgaz/topluarizakayitekle') }}" id="form_sample_2" class="form-horizontal" method="POST" novalidate="novalidate">
                                    <div class="form-body col-xs-12">
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <h3 class="form-section col-xs-12">Toplu Arıza Kayıdı Ekleme Ekranı</h3>
                                        <div class="form-group col-xs-12">
                                            <label class="col-sm-2 col-xs-4 control-label">Depo Gelen Bilgisi:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><select class="form-control select2 select2-offscreen depogelen" id="depogelen" name="depogelen" tabindex="-1">
                                                    <option value="">Seçiniz...</option>
                                                    @foreach($depogelenler as $depogelen)
                                                        @if((Input::old('depogelen'))==$depogelen->id)
                                                            <option value="{{ $depogelen->id }}" selected>{{ date("d-m-Y", strtotime($depogelen->tarih)).' Tarihli- '.$depogelen->adet.' Adet - '.$depogelen->netsiscari->cariadi }}</option>
                                                        @else
                                                            <option value="{{ $depogelen->id }}" >{{ date("d-m-Y", strtotime($depogelen->tarih)).' Tarihli - '.$depogelen->adet.' Adet - '.$depogelen->netsiscari->cariadi }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="col-sm-2 col-xs-4 control-label">Sayaç Tipi-Modeli:</label>
                                            <label class="col-xs-8 sayacadi" style="padding-top: 7px;padding-left:30px"></label>
                                            <input id="sayacadi" name="sayacadi" class="hide">
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="col-sm-2 col-xs-4 control-label">Sayaçlar:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-sm-10 col-xs-8">
                                                <i class="fa"></i><select class="form-control select2 select2-offscreen sayaclar" id="sayaclar" name="sayaclar[]"  multiple="" tabindex="-1">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <h4 class="col-xs-12">Seçilen Tüm Sayaçlar İçin Bu Bilgiler Arıza Kayıdına Eklenecektir!</h4>
                                            <div class="col-xs-12">
                                                <div class="form-group col-sm-6 col-xs-12">
                                                    <label class="control-label col-sm-4 col-xs-3">Garanti Durum: <span class="required" aria-required="true"> * </span></label>
                                                    <div class="input-icon right col-sm-8 col-xs-9" style="padding-left: 0">
                                                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="garanti" name="garanti" tabindex="5" title="">
                                                            <option value="0" {{Input::old('garanti')=="0" ? 'selected' : ''}}>Dışında</option>
                                                            <option value="1" {{Input::old('garanti')=="0" ? '' : 'selected'}}>İçinde</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-sm-4 col-xs-3">Bağlantı Çapı:<span class="required" aria-required="true"> * </span></label>
                                                <div class="input-icon right col-sm-8 col-xs-9">
                                                    <i class="fa"></i><input type="text" id="baglanticap" name="baglanticap" value="{{ Input::old('baglanticap') }}" maxlength="6" data-required="1" class="form-control" tabindex="8">
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-sm-4 col-xs-3">Pmax:<span class="required" aria-required="true"> * </span></label>
                                                <div class="input-icon right col-sm-8 col-xs-9">
                                                    <i class="fa"></i><input type="text" id="pmax" name="pmax" value="{{ Input::old('pmax') }}" maxlength="6" data-required="1" class="form-control" tabindex="9">
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-sm-4 col-xs-3">Qmax:<span class="required" aria-required="true"> * </span></label>
                                                <div class="input-icon right col-sm-8 col-xs-9">
                                                    <i class="fa"></i><input type="text" id="qmax" name="qmax" value="{{ Input::old('qmax') }}" maxlength="6" data-required="1" class="form-control" tabindex="10">
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-sm-4 col-xs-3">Qmin:<span class="required" aria-required="true"> * </span></label>
                                                <div class="input-icon right col-sm-8 col-xs-9">
                                                    <i class="fa"></i><input type="text" id="qmin" name="qmin" value="{{ Input::old('qmin') }}" maxlength="6" data-required="1" class="form-control" tabindex="11">
                                                </div>
                                            </div>
                                            <div class="form-group col-xs-12">
                                                <label class="control-label col-sm-2 col-xs-3">Müşteri Açıklaması:</label>
                                                <div class="col-sm-9 col-xs-8">
                                                    <input type="text" id="musteribilgi" name="musteribilgi" value="{{ Input::old('musteribilgi') }}" data-required="1" class="form-control" tabindex="12">
                                                </div>
                                            </div>
                                            <div class="form-group col-xs-12">
                                                <label class="control-label col-sm-2 col-xs-3">Arıza Açıklaması:</label>
                                                <div class="col-sm-9 col-xs-8">
                                                    <input type="text" id="arizaaciklama" name="arizaaciklama" value="{{ Input::old('arizaaciklama') }}"
                                                           placeholder="Müşterinin göreceği arıza açıklaması" data-required="1" class="form-control" tabindex="13">
                                                </div>
                                            </div>
                                            <div class="form-group col-xs-12">
                                                <label class="control-label col-sm-2 col-xs-3">Arıza Tespiti:<span class="required" aria-required="true"> * </span></label>
                                                <div class="input-icon right col-sm-10 col-xs-9">
                                                    <i class="fa"></i><select multiple="multiple" class="multi-select" id="arizalar" name="arizalar[]">
                                                        @foreach($arizakodlari as $arizakod)
                                                            <option data-id="{{ $arizakod->garanti }}" value="{{ $arizakod->id }}">{{ $arizakod->tanim }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <input id="arizalist" name="arizalist" class="hide" value="{{ Input::old('arizalist')}}">
                                            </div>
                                            <div class="form-group col-xs-12">
                                                <label class="control-label col-sm-2 col-xs-3">Değişen Parçalar:<span class="required" aria-required="true"> * </span></label>
                                                <div class="input-icon right col-sm-10 col-xs-9">
                                                    <i class="fa"></i><select multiple="multiple" class="multi-select" id="degisenler" name="degisenler[]">
                                                        @foreach($degisenler as $degisen)
                                                            <option value="{{ $degisen->id }}">{{ $degisen->tanim }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <input id="degisenlist" name="degisenlist" class="hide" value="{{ Input::old('degisenlist')}}">
                                            </div>
                                            <div class="form-group col-xs-12">
                                                <label class="control-label col-sm-2 col-xs-3">Yapılan İşlemler:<span class="required" aria-required="true"> * </span></label>
                                                <div class="input-icon right col-sm-10 col-xs-9">
                                                    <i class="fa"></i><select multiple="multiple" class="multi-select" id="yapilanlar" name="yapilanlar[]">
                                                        @foreach($yapilanlar as $yapilan)
                                                            <option value="{{ $yapilan->id }}">{{ $yapilan->tanim }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <input id="yapilanlist" name="yapilanlist" class="hide" value="{{ Input::old('yapilanlist')}}">
                                            </div>
                                            <div class="form-group col-xs-12">
                                                <label class="control-label col-sm-2 col-xs-3">Sonuç ve Uyarılar:<span class="required" aria-required="true"> * </span></label>
                                                <div class="input-icon right col-sm-10 col-xs-9">
                                                    <i class="fa"></i><select multiple="multiple" class="multi-select" id="uyarilar" name="uyarilar[]">
                                                        @foreach($uyarilar as $uyari)
                                                            <option value="{{ $uyari->id }}">{{ $uyari->tanim }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <input id="uyarilist" name="uyarilist" class="hide" value="{{ Input::old('uyarilist')}}">
                                            </div>
                                            <div class="form-group col-xs-12" style="margin:0;text-align:center">
                                                <input type="text" id="sayacdurum" name="sayacdurum" value="{{ Input::old('sayacdurum') ? Input::old('sayacdurum') : "0" }}" class="form-control hide"/>
                                                <label><input value="3" id="ozeldurum3" name="ozeldurum[]" type="checkbox" > Yedek Parça Bekliyor </label>
                                                <label><input value="4" id="ozeldurum4" name="ozeldurum[]" type="checkbox" > Şikayetli Muayene </label>
                                                <label><input value="5" id="ozeldurum5" name="ozeldurum[]" type="checkbox" > Müdahaleli Sayaç </label>
                                                <label><input value="6" id="ozeldurum6" name="ozeldurum[]" type="checkbox" > Yeni Sayaç Verildi </label>
                                                <label><input value="7" id="ozeldurum7" name="ozeldurum[]" type="checkbox" > Geri İade </label>
                                                <label><input value="8" id="ozeldurum8" name="ozeldurum[]" type="checkbox" > Geri İade(Kalibrasyonsuz) </label>
                                            </div>
                                        </div>
                                        <div class="form-actions col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-offset-3 col-xs-9">
                                                    <button type="button" class="btn green topluekleme" data-toggle="modal" data-target="#confirm">Kaydet</button>
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
                    <h4 class="modal-title">Arıza Kayıdı Silinecek</h4>
                </div>
                <div class="modal-body">
                    Seçilen Arıza Kayıdını Silmek İstediğinizden Emin Misiniz?
                </div>
                <div class="modal-footer">
                    <a id="sayacid" href="" type="button" class="btn blue">Sil</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Arıza Kayıtları Eklenecek?</h4>
                </div>
                <div class="modal-body">
                    Seçilen Depo Girişi İçin Girilen Bilgilerle Arıza Kayıtları Eklenecektir?
                </div>
                <div class="modal-footer">
                    <a id="formsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
@stop
