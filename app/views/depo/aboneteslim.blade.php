@extends('layout.master')

@section('page-title')
    <div class="page-title">
        <h1>Abone <small> Sayaç Teslimat Bilgi Ekranı</small></h1>
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
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>

@stop

@section('page-script')
    <script src="{{ URL::to('pages/depo/form-validation-3.js') }}"></script>
@stop

@section('scripts')
    <script>
        jQuery(document).ready(function() {
            Metronic.init(); // init metronic core componets
            Layout.init(); // init layout
            Demo.init(); // init demo features
            QuickSidebar.init(); // init quick sidebar
            FormValidationDepo.init();
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
                "url": "{{ URL::to('depo/aboneteslimlist') }}",
                "type": "POST",
                "data": {
                    "hatirlatma_id" : "@if(isset($hatirlatma_id)){{$hatirlatma_id}}@endif" ,
                    "netsiscari_id" : "@if(isset(Auth::user()->netsiscari_id) && count(Auth::user()->netsiscari_id)>0){{Auth::user()->netsiscarilist}}@endif"
                }
            },
            "bServerSide": true,
            "fnDrawCallback" : function() {
                var secilen=$('#secilen').val();
                $("#sample_editable_1  tr .id").each(function(){
                    if(secilen===$(this).html()){
                        $(this).parents('tr').addClass("active");
                    }
                });
            },
            "aaSorting": [[4,'desc']],
            "columnDefs": [ { targets: [ 1 ], orderData: [ 1, 0 ] },
                { targets: [ 2 ], orderData: [ 2, 0 ] },
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
                {data: 'id', name: 'aboneteslim.id',"class":"id","orderable": true, "searchable": true},
                {data: 'adisoyadi', name: 'abone.adisoyadi',"orderable": true, "searchable": false},
                {data: 'sayacsayisi', name: 'aboneteslim.sayacsayisi',"orderable": true, "searchable": true},
                {data: 'yeradi', name: 'uretimyer.yeradi',"orderable": true, "searchable": false},
                {data: 'gdurum', name: 'aboneteslim.gdurum',"class":"durum","orderable": true, "searchable": false},
                {data: 'adi_soyadi', name: 'kullanici.adi_soyadi',"orderable": true, "searchable": false},
                {data: 'teslimtarihi', name: 'aboneteslim.teslimtarihi',"class":"tarih","orderable": true, "searchable": false},
                {data: 'gteslimtarihi', name: 'aboneteslim.gteslimtarihi',"visible": false, "searchable": true},
                {data: 'nadisoyadi', name: 'abone.nadisoyadi',"visible": false, "searchable": true},
                {data: 'nyeradi', name: 'uretimyer.nyeradi',"visible": false, "searchable": true},
                {data: 'ndurum', name: 'aboneteslim.ndurum',"visible": false, "searchable": true},
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
            '<option value="8">Abone</option>'+
            '<option value="2">Sayaç Sayısı</option>'+
            '<option value="9">Üretim Yeri</option>'+
            '<option value="4">Durum</option>'+
            '<option value="10">Kullanıcı</option>'+
            '<option value="7">Tarih</option>'+
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
            if(oTable.cell( $(this).children('.id')).data()!==undefined) {
                var adet=0;
                $(this).toggleClass("active");
                var secilen = "";
                if ($(this).hasClass('active')) {
                    $("tbody tr").removeClass("active");
                    $(this).addClass("active");
                    secilen = oTable.cell($(this).children('.id')).data();
                    $('#secilen').val(secilen);
                    var durum = $(this).children('.durum').text();
                    if(durum==='Bekliyor')
                    {
                        adet++;
                    }
                } else {
                    $(this).removeClass("active");
                    $('#secilen').val("");
                }
                @if(Auth::user()->grup_id==17)
                if(adet>0) {
                    $('.teslim').removeClass("hide");
                }else{
                    $('.teslim').addClass("hide");
                }
                @else
                    $('.teslim').addClass("hide");
                @endif

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
        var table3 = $('#sample_editable_3');
        var oTable3 = table3.DataTable({
            "sPaginationType": "simple_numbers",
            "searching": false,
            "ordering": false,
            "bProcessing": false,
            "sAjaxSource": "",
            "bServerSide": false,
            "bFilter" : false,
            "bLengthChange": false,
            "iDisplayLength": 5,
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
            "aoColumns": [{"sClass":"id"},null,null,null,null,null,null],
            "lengthMenu": [
                [5, 10],
                [5, 10]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_3_wrapper');
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        var table4 = $('#sample_editable_4');
        var oTable4 = table4.DataTable({
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
            "aoColumns": [{"sClass":"adres"}],
            "lengthMenu": [
                [5],
                [5]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_4_wrapper');
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
        table4.on('click', 'tr', function () {
            if(oTable4.cell( $(this).children('.adres')).data()!==undefined){
                $(this).toggleClass("active");
                var secilen=oTable4.cell( $(this).children('.adres')).data();
                var flag = 0;
                $('#secilenadres').val("");
                $("#sample_editable_4  tr .adres").each(function(){
                    if(secilen===$(this).html()){
                        $('#secilenadres').val(secilen);
                        flag = 1;
                    }else{
                        $(this).parents('tr').removeClass("active");
                    }
                });
                if(!flag){
                    $('#secilenadres').val("");
                }
            }
        });
    </script>
    <script>
        $(document).on("click", ".detay", function () {
            $.blockUI();
            var id = $(this).data('id');
            $.getJSON("{{ URL::to('depo/aboneteslimbilgi') }}",{id:id},function(event){
                if(event.durum){
                    var aboneteslim = event.aboneteslim;
                    var abone = aboneteslim.abone;
                    $(".adisoyadi").html(abone.adisoyadi);
                    $(".aboneno").html(abone.abone_no);
                    $(".tckimlikno").html(abone.tckimlikno);
                    $(".telefon").html(abone.telefon);
                    $(".faturano").html(aboneteslim.faturano);
                    $(".faturaadres").html(aboneteslim.faturaadres);
                    $(".odemesekli").html(aboneteslim.odemesekli);
                    $(".aciklama").html(aboneteslim.aciklama);
                    $(".kasakod").html(aboneteslim.kasakod ? aboneteslim.kasakod.kasaadi : '');
                    if(aboneteslim.faturano==="" || aboneteslim.faturano==null)
                        $('.faturadetaykisim').addClass('hide');
                    else
                        $('.faturadetaykisim').removeClass('hide');
                    oTable2.clear().draw();
                    $.each(sayacgelen,function(index) {
                        var sayacadi = sayacgelen[index].sayacadi.sayacadi;
                        var serino = sayacgelen[index].serino;
                        var sayaccap = sayacgelen[index].sayaccap.capadi;
                        var uretimyer = sayacgelen[index].uretimyer.yeradi;
                        var durum = sayacgelen[index].durum;
                        var arizafiyat = sayacgelen[index].arizafiyat;
                        var parabirimi = sayacgelen[index].parabirimi;
                        var parabirimi2 = sayacgelen[index].parabirimi2;
                        var garanti = arizafiyat.ariza_garanti==="1" ? 'İçinde' : 'Dışında';
                        var fiyat =  parseFloat(arizafiyat.toplamtutar);
                        var fiyat2 =  parseFloat(arizafiyat.toplamtutar2);
                        if(fiyat2===0){
                            oTable2.row.add([sayacgelen[index].id,serino,uretimyer,sayacadi+' '+sayaccap,garanti,fiyat+' '+parabirimi.birimi+' + '+fiyat2+' '+parabirimi2.birimi,durum])
                                .draw();
                        }else{
                            oTable2.row.add([sayacgelen[index].id,serino,uretimyer,sayacadi+' '+sayaccap,garanti,fiyat+' '+parabirimi.birimi,durum])
                                .draw();
                        }
                    });
                }else{
                    $('#detay-goster').modal('hide');
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
        });
        $(document).on("click", ".teslim", function () {
            var id = $('#secilen').val();
            if(id!==""){
                $.blockUI();
                var action = $('#form_sample_3').data('action');
                $('#form_sample_3').prop('action',action+'/'+id);
                $.getJSON("{{ URL::to('depo/teslimbilgi') }}",{id:id},function(event){
                    if(event.durum) {
                        var aboneteslim = event.aboneteslim;
                        var teslimadres = event.teslimadres;
                        var sayacgelen = aboneteslim.sayacgelen;
                        var abone = aboneteslim.abone;
                        var kasakod = aboneteslim.kasakod;
                        $(".teslimadisoyadi").html(abone.adisoyadi);
                        $(".teslimaboneno").html(abone.abone_no);
                        $(".teslimtckimlikno").html(abone.tckimlikno);
                        $(".teslimtelefon").html(abone.telefon);
                        $("#teslimadres").val(abone.faturaadresi);
                        $('#teslimsecilenler').val(aboneteslim.secilenler);
                        $('#teslimadet').val(aboneteslim.sayacsayisi);
                        $('#teslimfaturano').val(aboneteslim.belgeno);
                        $('#teslimaciklama').val('TAMİR BAKIM ÜCRETİ. AYRINTILI BİLGİ EKTEDİR.');
                        $("#teslimkasakod").empty();
                        $.each(kasakod,function(index){
                            $("#teslimkasakod").append('<option data-id="'+kasakod[index].odemetipi+'" value="' + kasakod[index].kasakod + '" disabled> ' + kasakod[index].kasaadi + '</option>');
                        });
                        $("#teslimkasakod").select2();
                        oTable3.clear().draw();
                        $.each(sayacgelen,function(index) {
                            var sayacadi = sayacgelen[index].sayacadi.sayacadi;
                            var serino = sayacgelen[index].serino;
                            var sayaccap = sayacgelen[index].sayaccap.capadi;
                            var uretimyer = sayacgelen[index].uretimyer.yeradi;
                            var durum = sayacgelen[index].durum;
                            var arizafiyat = sayacgelen[index].arizafiyat;
                            var parabirimi = sayacgelen[index].parabirimi;
                            var parabirimi2 = sayacgelen[index].parabirimi2;
                            var garanti = arizafiyat.ariza_garanti==="1" ? 'İçinde' : 'Dışında';
                            var fiyat = parseFloat(arizafiyat.toplamtutar);
                            var fiyat2 = parseFloat(arizafiyat.toplamtutar2);
                            if(fiyat2===0){
                                oTable3.row.add([sayacgelen[index].id,serino,uretimyer,sayacadi+' '+sayaccap,garanti,fiyat+' '+parabirimi.birimi+' + '+fiyat2+' '+parabirimi2.birimi,durum])
                                    .draw();
                            }else{
                                oTable3.row.add([sayacgelen[index].id,serino,uretimyer,sayacadi+' '+sayaccap,garanti,fiyat+' '+parabirimi.birimi,durum])
                                    .draw();
                            }
                        });
                        oTable4.clear().draw();
                        $.each(teslimadres, function (index) {
                            oTable4.row.add([teslimadres[index].faturaadres]).draw();
                        });
                        $('#secilenadres').val("");
                    }else{
                        $('#teslim').modal('hide');
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            }else{
                toastr["warning"]('Teslim Edilecek Seçilmedi', 'Teslimat Hatası');
            }
        });
        $(document).on("click", ".teslimadresisec", function (){
            var secilen = $('#secilenadres').val();
            $('#teslimadres').val(secilen);
        });
        $(document).ready(function() {
            $('#kriter').select2();
            $('#teslimodemesekli').on('change',function(){

                var odemetipi = $(this).val();
                $("#teslimkasakod>option").prop('disabled',true);
                switch(odemetipi){
                    case 'NAKİT' :
                        $("#teslimkasakod>option[data-id='1']").prop('disabled',false);
                        break;
                    case 'KREDİ KARTI' :
                        $("#teslimkasakod>option[data-id='2']").prop('disabled',false);
                        break;
                    case 'SENET' :
                        $("#teslimkasakod>option[data-id='3']").prop('disabled',false);
                        break;
                    default :
                        break;
                }
                $("#teslimkasakod").select2();
            });
            $('#faturavar').on('change', function () {
                if ($('#faturavar').attr('checked')) {
                    $(".faturakismi").removeClass('hide');
                } else {
                    $(".faturakismi").addClass('hide');
                }
            });
            $('.irsaliye').click(function () {
                var irsaliye = $('#sample_editable_1 .active .id').text();
                if (irsaliye !== null) {
                    var durum =$('#sample_editable_1 .active .durum').text();
                    if(durum!=="Bekliyor"){
                        $.extend({
                            redirectPost: function (location, args) {
                                var form = '';
                                $.each(args, function (key, value) {
                                    value = value.split('"').join('\"');
                                    form += '<input type="hidden" name="' + key + '" value="' + value + '">';
                                });
                                $('<form action="' + location + '" method="POST" target="_blank">' + form + '</form>').appendTo($(document.body)).submit();
                            }
                        });
                        var redirect = "";
                        $.redirectPost(redirect, {irsaliye: irsaliye,ireport:'1'});
                    }else{
                        toastr["warning"]('Teslim Edilmeden İrsaliye Çıkarılamaz!', 'İrsaliye Hatası');
                    }
                }
            });
            $('.irsaliyeek').click(function () {
                var irsaliye = $('#sample_editable_1 .active .id').text();
                if (irsaliye !== null) {
                    var durum =$('#sample_editable_1 .active .durum').text();
                    if(durum!=="Bekliyor"){
                        $.extend({
                            redirectPost: function (location, args) {
                                var form = '';
                                $.each(args, function (key, value) {
                                    value = value.split('"').join('\"');
                                    form += '<input type="hidden" name="' + key + '" value="' + value + '">';
                                });
                                $('<form action="' + location + '" method="POST" target="_blank">' + form + '</form>').appendTo($(document.body)).submit();
                            }
                        });
                        var redirect = "";
                        $.redirectPost(redirect, {irsaliyeek: irsaliye,ireport:'1'});
                    }else{
                        toastr["warning"]('Teslim Edilmeden İrsaliye Eki Çıkarılamaz!', 'İrsaliye Hatası');
                    }
                }
            });
        });
        $('#formsubmit').click(function () {
            $('#form_sample_3').submit();
        });
        $(document).ready(function(){
            $("select").on("select2-close", function () { $(this).valid(); });
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
                        <i class="fa fa-tag"></i>Abone Sayaç Teslim Bilgisi
                    </div>
                    <div class="actions">
                        <a class="btn btn-default btn-sm irsaliyeek">
                            <i class="fa fa-list"></i> İrsaliye Sayaç Listesi </a>
                        <a class="btn btn-default btn-sm irsaliye">
                            <i class="fa fa-print"></i> Teslimat İrsaliyesi </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Abone</th>
                            <th>Sayaç Sayısı</th>
                            <th>Üretim Yeri</th>
                            <th>Durum</th>
                            <th>Kullanıcı</th>
                            <th>Tarih</th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th>Detay</th>
                        </tr>
                        </thead>
                    </table>
                    <form action="" id="form_sample" class="form-horizontal" novalidate="novalidate">
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-md-offset-1 col-md-2"><a class='btn green teslim hide' href='#teslim' data-toggle='modal' data-id=''>Teslim Et</a></div>
                                <div class="hide"><input id="secilen" name="secilen"/></div>
                            </div>
                        </div>
                    </form>
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
                                    <i class="fa fa-pencil"></i>Abone Sayaç Teslimatı Detayı
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h4 class="form-section">Abone Bilgisi</h4>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Adı Soyadı:</label>
                                            <label class="col-md-3 col-xs-12 adisoyadi" style="padding-top: 9px"></label>
                                            <label class="control-label col-md-2 col-xs-12">Abone No:</label>
                                            <label class="col-md-5 col-xs-12 aboneno" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Tc Kimlik No / Vergi Numarası:</label>
                                            <label class="col-md-3 col-xs-12 tckimlikno" style="padding-top: 9px"></label>
                                            <label class="control-label col-md-2 col-xs-12">Telefonu:</label>
                                            <label class="col-md-3 col-xs-12 telefon" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_2">
                                                <thead>
                                                <tr>
                                                    <th class="">#</th>
                                                    <th>Seri No</th>
                                                    <th>Üretim Yeri</th>
                                                    <th>Sayaç Adı</th>
                                                    <th>Garanti</th>
                                                    <th>Fiyatı</th>
                                                    <th>Teslim Durumu</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <h4 class="form-section">İrsaliye Bilgisi  <span style="font-size: 12px">Teslimat sonrası Netsis tarafında oluşan faturayı temsil eder</span></h4>
                                        <div class="form-group faturadetaykisim">
                                            <div class="form-group">
                                                <label class="control-label col-md-2 col-xs-12">Fatura No:</label>
                                                <label class="col-md-3 col-xs-12 faturano" style="padding-top: 9px"></label>
                                                <label class="control-label col-md-2 col-xs-12">Ödeme Şekli:</label>
                                                <label class="col-md-3 col-xs-12 odemesekli" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-2 col-xs-12">Fatura Adresi:</label>
                                                <label class="col-md-8 col-xs-12 faturaadres" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-2 col-xs-12">Açıklama:</label>
                                                <label class="col-md-3 col-xs-12 aciklama" style="padding-top: 9px"></label>
                                                <label class="control-label col-md-2 col-xs-12">Kasa:</label>
                                                <label class="col-md-3 col-xs-12 kasakod" style="padding-top: 9px"></label>
                                            </div>
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
    <div class="modal fade" id="teslim" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-truck"></i>Sayaç Teslimat Sayfası
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{ URL::to('depo/aboneteslimet') }}" data-action="{{URL::to('depo/aboneteslimet')}}" id="form_sample_3" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h4 class="form-section">Abone Bilgisi</h4>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Adı Soyadı:</label>
                                            <label class="col-md-3 col-xs-12 teslimadisoyadi" style="padding-top: 9px"></label>
                                            <label class="control-label col-md-2 col-xs-12">Abone No:</label>
                                            <label class="col-md-5 col-xs-12 teslimaboneno" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Tc Kimlik No / Vergi Numarası:</label>
                                            <label class="col-md-3 col-xs-12 teslimtckimlikno" style="padding-top: 9px"></label>
                                            <label class="control-label col-md-2 col-xs-12">Telefonu:</label>
                                            <label class="col-md-3 col-xs-12 teslimtelefon" style="padding-top: 9px"></label>
                                        </div>
                                        <h4 class="form-section">Sayaç Listesi</h4>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_3">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Seri No</th>
                                                    <th>Üretim Yeri</th>
                                                    <th>Sayaç Adı</th>
                                                    <th>Garanti</th>
                                                    <th>Fiyatı</th>
                                                    <th>Teslim Durumu</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-6 col-xs-12">
                                                <input class="hide" id="teslimsecilenler" name="teslimsecilenler"/>
                                                <input class="hide" id="teslimadet" name="teslimadet"/>
                                            </div>
                                        </div>
                                        <h4 class="form-section">İrsaliye Bilgisi  <span style="font-size: 12px">Teslimat sonrası Netsis tarafında oluşacak faturayı temsil edecek</span>
                                            <label><input type="checkbox" id="faturavar" name="faturavar" checked/> Fatura Çıkacak mı? </label></h4>
                                        <div class="form-group faturakismi">
                                            <div class="form-group">
                                                <label class="control-label col-md-2 col-xs-12">Fatura No:<span class="required" aria-required="true"> * </span></label>
                                                <div class="input-icon right col-md-3 col-xs-12">
                                                    <i class="fa"></i><input type="text" id="teslimfaturano" name="teslimfaturano" data-required="1" class="form-control" maxlength="15" placeholder="İrsaliye Sıra No">
                                                </div>
                                                <label class="control-label col-md-2 col-xs-12">Ödeme Şekli:<span class="required" aria-required="true"> * </span></label>
                                                <div class="input-icon right col-md-3 col-xs-12">
                                                    <i class="fa"></i><select class="form-control select2me" id="teslimodemesekli" name="teslimodemesekli" tabindex="-1" title="">
                                                        <option data-id="0" value="">Seçiniz...</option>
                                                        <option data-id="1" value="NAKİT">NAKİT</option>
                                                        <option data-id="2" value="KREDİ KARTI">KREDİ KARTI</option>
                                                        <option data-id="3" value="SENET">SENET</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-2 col-xs-4">Fatura Adresi:<span class="required" aria-required="true"> * </span></label>
                                                <div class="input-icon right col-md-8 col-xs-6">
                                                    <i class="fa"></i><input type="text" id="teslimadres" name="teslimadres" data-required="1" class="form-control" maxlength="100">
                                                </div>
                                                <div class="col-xs-2" style="text-align: center">
                                                    <button type="button" class="btn green adressec" data-toggle="modal" data-target="#adressec">Seç</button>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-2 col-xs-12">Açıklama:<span class="required" aria-required="true"> * </span></label>
                                                <div class="input-icon right col-md-3 col-xs-12">
                                                    <i class="fa"></i><input type="text" id="teslimaciklama" name="teslimaciklama" data-required="1" class="form-control" maxlength="100">
                                                </div>
                                                <label class="control-label col-md-2 col-xs-12">Kasa:<span class="required" aria-required="true"> * </span></label>
                                                <div class="input-icon right col-md-3 col-xs-12">
                                                    <i class="fa"></i><select class="form-control select2me" id="teslimkasakod" name="teslimkasakod" tabindex="-1" title="">
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green teslimet" data-toggle="modal" data-target="#confirm">Teslim Et</button>
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
    <div class="modal fade" id="adressec" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Fatura Adresi Listesi
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_4" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Önceki Fatura Adresleri</h3>
                                        <input class="hide" id="secilenadres" name="secilenadres"/>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_4">
                                                <thead>
                                                <tr>
                                                    <th>Fatura Adresi</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green teslimadresisec" data-dismiss="modal">Tamam</button>
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
    <div class="modal fade" id="confirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Sayaç Teslim Edilecek</h4>
                </div>
                <div class="modal-body">
                    Seçilen Sayaç Aboneye Teslim Edilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Onayla</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@stop
