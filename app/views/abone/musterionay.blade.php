@extends('layout.master')

@section('page-title')
    <!--suppress JSValidateTypes -->
    <div class="page-title">
        <h1>Müşteri Onay <small> Takip Ekranı</small></h1>
    </div>
@stop

@section('page-plugins')
    <link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::to('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" >
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
    <script src="{{ URL::to('assets/global/plugins/jquery-maskmoney/jquery.maskMoney.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/tinymce/tinymce.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript" ></script>

@stop

@section('page-script')
    <script src="{{ URL::to('pages/abone/form-validation-1.js') }}"></script>
@stop

@section('scripts')
    <script>
        jQuery(document).ready(function() {
           Metronic.init(); // init metronic core componets
           Layout.init(); // init layout
           Demo.init(); // init demo features
           QuickSidebar.init(); // init quick sidebar
           FormValidationAbone.init();
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
                "url": "{{ URL::to('abone/ucretlendirmeonaylist') }}",
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
            "aaSorting": [[3,'asc']],
            "columnDefs": [ { targets: [ 1 ], orderData: [ 1, 3, 0 ] },
                { targets: [ 2 ], orderData: [ 2, 3, 0 ] },
                { targets: [ 3 ], orderData: [ 3, 0 ] },
                { targets: [ 4 ], orderData: [ 4, 3, 0 ] },
                { targets: [ 5 ], orderData: [ 5, 3, 0 ] },
                { targets: [ 6 ], orderData: [ 6, 3, 0 ] }
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
                {data: 'id', name: 'ucretlendirilen.id',"class":"id","orderable": true, "searchable": true},
                {data: 'yeradi', name: 'uretimyer.yeradi',"orderable": true, "searchable": false},
                {data: 'sayacsayisi', name: 'ucretlendirilen.sayacsayisi',"orderable": true, "searchable": true},
                {data: 'gabonedurum', name: 'ucretlendirilen.gabonedurum',"class":"durum","orderable": true, "searchable": false},
                {data: 'fiyat', name: 'ucretlendirilen.fiyat',"orderable": true, "searchable": true},
                {data: 'adi_soyadi', name: 'kullanici.adi_soyadi',"orderable": true, "searchable": false},
                {data: 'durumtarihi', name: 'ucretlendirilen.durumtarihi',"class":"tarih","orderable": true, "searchable": false},
                {data: 'gdurumtarihi', name: 'ucretlendirilen.gdurumtarihi',"visible": false, "searchable": true},
                {data: 'nyeradi', name: 'uretimyer.nyeradi',"visible": false, "searchable": true},
                {data: 'nabonedurum', name: 'ucretlendirilen.nabonedurum',"visible": false, "searchable": true},
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
            '<option value="8">Üretim Yeri</option>'+
            '<option value="2">Sayaç Sayısı</option>'+
            '<option value="9">Durum</option>'+
            '<option value="4">Fiyat</option>'+
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
                var bos=0;
                $(this).toggleClass("active");
                var secilen = "";
                if ($(this).hasClass('active')) {
                    $("tbody tr").removeClass("active");
                    $(this).addClass("active");
                    secilen = oTable.cell($(this).children('.id')).data();
                    $('#secilen').val(secilen);
                } else {
                    $(this).removeClass("active");
                    $('#secilen').val("");
                    bos=1;
                }
                if(bos)
                {
                    $('.onayla').addClass("hide");
                    $('.reddet').addClass("hide");
                }else{
                    @if(Auth::user()->grup_id==19)
                    var durum = $(this).children('.durum').text();
                    if(durum==='Bekliyor')
                    {
                        $('.onayla').removeClass("hide");
                        $('.reddet').removeClass("hide");
                    }else{
                        $('.onayla').addClass("hide");
                        $('.reddet').addClass("hide");
                    }
                    @else
                        $('.onayla').addClass("hide");
                        $('.reddet').addClass("hide");
                    @endif
                }
            }
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown*/
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
            "bInfo": true,
            "bPaginate": true,
            "bAutoWidth": false,
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
            "lengthMenu": [
                [5, 10],
                [5, 10]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_4_wrapper');
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        var table5 = $('#sample_editable_5');
        var oTable5 = table5.DataTable({
            "sPaginationType": "simple_numbers",
            "searching": false,
            "ordering": false,
            "bProcessing": false,
            "sAjaxSource": "",
            "bServerSide": false,
            "bInfo": true,
            "bPaginate": true,
            "bAutoWidth": false,
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
        var tableWrapper = jQuery('#sample_editable_5_wrapper');
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        var table6 = $('#sample_editable_6');
        var oTable6 = table6.DataTable({
            "sPaginationType": "simple_numbers",
            "searching": true,
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
                [5],
                [5]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_6_wrapper');
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        $(document).ready(function() {
            $('#kriter').select2();
            $(document).on("click", ".goster", function () {
                $.blockUI();
                var id = $(this).data('id');
                $.getJSON("{{ URL::to('ucretlendirme/ucretlendirilenbilgi') }}", {id: id}, function (event) {
                    if (event.durum) {
                        var ucretlendirilen = event.ucretlendirilen;
                        $(".yer").html(ucretlendirilen.uretimyer.yeradi);
                        oTable2.clear().draw();
                        var genelfiyat = 0;
                        var genelindirim = 0;
                        var genelkdvsiztutar = 0;
                        var genelkdvtutar = 0;
                        var geneltoplamtutar = 0;
                        var genelfiyat2 = 0;
                        var genelindirim2 = 0;
                        var genelkdvsiztutar2 = 0;
                        var genelkdvtutar2 = 0;
                        var geneltoplamtutar2 = 0;
                        var dolar = 0;
                        var euro = 0;
                        var sterlin = 0;
                        var dovizkuru = event.dovizkuru;
                        var dovizkurutarih = "";
                        $.each(dovizkuru, function (index) {
                            dovizkurutarih = dovizkuru[index].tarih;
                            if (dovizkuru[index].parabirimi_id === "2")
                                euro = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                            else if (dovizkuru[index].parabirimi_id === "3")
                                dolar = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                            else
                                sterlin = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                        });
                        $('#onizlemedolar').val(dolar);
                        $('#onizlemeeuro').val(euro);
                        $('#onizlemesterlin').val(sterlin);
                        var parabirimi = ucretlendirilen.parabirimi;
                        var parabirimi2 = ucretlendirilen.parabirimi2;
                        $('#onizlemebirim').val(parabirimi.id);
                        $('#onizlemebirim2').val(parabirimi2 === null ? "" : parabirimi2.id);
                        $('#onizlemebirimi').val(parabirimi.birimi);
                        $('#onizlemebirimi2').val(parabirimi2 === null ? "" : parabirimi2.birimi);
                        $('#onizlemekurtarih').val(dovizkurutarih);
                        var arizafiyat = ucretlendirilen.arizafiyat;
                        var kurdurum = false;
                        $.each(arizafiyat, function (index) {
                            var garanti = arizafiyat[index].ariza_garanti === "0" ? 'Dışında' : 'İçinde';
                            var fiyatdurum = arizafiyat[index].fiyatdurum === "0" ? 'Genel' : 'Özel';
                            var sayacadi = arizafiyat[index].sayacadi.sayacadi;
                            var serino = arizafiyat[index].ariza_serino;
                            var birim = arizafiyat[index].parabirimi;
                            var birim2 = arizafiyat[index].parabirimi2;
                            var fiyat = arizafiyat[index].durum === "4" ? 0 : parseFloat(arizafiyat[index].fiyat);
                            var fiyat2 = arizafiyat[index].durum === "4" ? 0 : parseFloat(arizafiyat[index].fiyat2);
                            var indirimorani = parseFloat(arizafiyat[index].indirimorani);
                            var indirim = (fiyat * indirimorani) / 100;
                            var indirim2 = (fiyat2 * indirimorani) / 100;
                            var kdvsiztutar = arizafiyat[index].durum === "4" ? 0 : parseFloat(arizafiyat[index].tutar);
                            var kdvsiztutar2 = arizafiyat[index].durum === "4" ? 0 : parseFloat(arizafiyat[index].tutar2);
                            var kdv = arizafiyat[index].durum === "4" ? 0 : parseFloat(arizafiyat[index].kdv);
                            var kdv2 = arizafiyat[index].durum === "4" ? 0 : parseFloat(arizafiyat[index].kdv2);
                            var toplamtutar = arizafiyat[index].durum === "4" ? 0 : parseFloat(arizafiyat[index].toplamtutar);
                            var toplamtutar2 = arizafiyat[index].durum === "4" ? 0 : parseFloat(arizafiyat[index].toplamtutar2);
                            if (toplamtutar2 > 0) {
                                oTable2.row.add([arizafiyat[index].id, serino, sayacadi, garanti, fiyatdurum, fiyat.toFixed(2) + ' ' + birim.birimi + ' + ' + fiyat2.toFixed(2) + ' ' + birim2.birimi,
                                    indirimorani.toFixed(2) + '%', kdvsiztutar.toFixed(2) + ' ' + birim.birimi + ' + ' + kdvsiztutar2.toFixed(2) + ' ' + birim2.birimi, kdv.toFixed(2) + ' ' + birim.birimi + ' + ' + kdv2.toFixed(2) + ' ' + birim2.birimi,
                                    toplamtutar.toFixed(2) + ' ' + birim.birimi + ' + ' + toplamtutar2.toFixed(2) + ' ' + birim2.birimi,
                                    '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>']).draw();
                            } else {
                                oTable2.row.add([arizafiyat[index].id, serino, sayacadi, garanti, fiyatdurum, fiyat.toFixed(2) + ' ' + birim.birimi, indirimorani.toFixed(2) + '%',
                                    kdvsiztutar.toFixed(2) + ' ' + birim.birimi, kdv.toFixed(2) + ' ' + birim.birimi, toplamtutar.toFixed(2) + ' ' + birim.birimi,
                                    '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>'])
                                    .draw();
                            }
                            if (birim.id === parabirimi.id) {
                                toplamtutar = Math.round(toplamtutar * 2) / 2;
                                if (birim2 !== null) {
                                    if (birim2.id === parabirimi2.id) {
                                        toplamtutar2 = Math.round(toplamtutar2 * 2) / 2;
                                    } else {
                                        toplamtutar2 = 0;
                                        $('.onizlemewarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                        $('.ucretlendir').prop('disabled', true);
                                    }
                                } else {
                                    toplamtutar2 = 0;
                                }
                            } else {
                                var kur = 1;
                                kurdurum = true;
                                if (parabirimi.id === "1") // tl ise
                                {
                                    if (birim.id === "2") //euro ise
                                        kur = euro;
                                    else if (birim.id === "3") //dolar ise
                                        kur = dolar;
                                    else
                                        kur = sterlin;
                                } else if (parabirimi.id === "2") { //euro ise
                                    if (birim.id === "1") //tl ise
                                        kur = 1 / euro;
                                    else if (birim.id === "3") //dolar ise
                                        kur = dolar / euro;
                                    else
                                        kur = sterlin / euro;
                                } else if (parabirimi.id === "3") { //dolar ise
                                    if (birim.id === "1") //tl ise
                                        kur = 1 / dolar;
                                    else if (birim.id === "2") //euro ise
                                        kur = euro / dolar;
                                    else
                                        kur = sterlin / dolar;
                                } else { //sterlin ise
                                    if (birim.id === "1") //euro ise
                                        kur = 1 / sterlin;
                                    else if (birim.id === "2") //dolar ise
                                        kur = euro / sterlin;
                                    else
                                        kur = dolar / sterlin;
                                }
                                fiyat *= kur;
                                if (birim2 !== null) {
                                    if (birim2.id === parabirimi.id) {
                                        fiyat += fiyat2;
                                        fiyat2 = 0;
                                    } else if (birim2.id !== parabirimi2.id) {
                                        fiyat2 = 0;
                                        $('.onizlemewarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    }
                                } else {
                                    fiyat2 = 0;
                                }
                                indirim = ((fiyat * indirimorani) / 100);
                                indirim2 = ((fiyat2 * indirimorani) / 100);
                                kdvsiztutar = (fiyat - indirim);
                                kdvsiztutar2 = (fiyat2 - indirim2);
                                kdv = (kdvsiztutar * 18) / 100;
                                kdv2 = (kdvsiztutar2 * 18) / 100;
                                toplamtutar = kdvsiztutar + kdv;
                                toplamtutar = Math.round(toplamtutar * 2) / 2;
                                toplamtutar2 = kdvsiztutar2 + kdv2;
                                toplamtutar2 = Math.round(toplamtutar2 * 2) / 2;
                            }
                            genelfiyat += fiyat;
                            genelindirim += indirim;
                            genelkdvsiztutar += kdvsiztutar;
                            genelkdvtutar += kdv;
                            geneltoplamtutar += toplamtutar;
                            genelfiyat2 += fiyat2;
                            genelindirim2 += indirim2;
                            genelkdvsiztutar2 += kdvsiztutar2;
                            genelkdvtutar2 += kdv2;
                            geneltoplamtutar2 += toplamtutar2;
                        });
                        $('.onizlemewarning').html('<span style="color:red">Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
                        if (kurdurum)
                            $('.onizlemekur').removeClass('hide');
                        else
                            $('.onizlemekur').addClass('hide');
                        if (geneltoplamtutar2 === 0) {
                            $('.onizlemetutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi.birimi);
                            $('.onizlemeindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi.birimi);
                            $('.onizlemekdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi.birimi);
                            $('.onizlemekdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi.birimi);
                            $('.onizlemetoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi.birimi);
                        } else {
                            $('.onizlemetutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi.birimi + ' + ' + genelfiyat2.toFixed(2) + ' ' + parabirimi2.birimi);
                            $('.onizlemeindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi.birimi + ' + ' + genelindirim2.toFixed(2) + ' ' + parabirimi2.birimi);
                            $('.onizlemekdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi.birimi + ' + ' + genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                            $('.onizlemekdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi.birimi + ' + ' + genelkdvtutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                            $('.onizlemetoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi.birimi + ' + ' + geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                        }
                        $('#onizlemetutar').val(genelfiyat.toFixed(2));
                        $('#onizlemeindirimtutar').val(genelindirim.toFixed(2));
                        $('#onizlemekdvsiztutar').val(genelkdvsiztutar.toFixed(2));
                        $('#onizlemekdvtutar').val(genelkdvtutar.toFixed(2));
                        $('#onizlemetoplamtutar').val(geneltoplamtutar.toFixed(2));
                        $('#onizlemetutar2').val(genelfiyat2.toFixed(2));
                        $('#onizlemeindirimtutar2').val(genelindirim2.toFixed(2));
                        $('#onizlemekdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
                        $('#onizlemekdvtutar2').val(genelkdvtutar2.toFixed(2));
                        $('#onizlemetoplamtutar2').val(geneltoplamtutar2.toFixed(2));
                    } else {
                        $('#detay-goster').modal('hide');
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            });
            $(document).on("click", ".fiyatdetay", function () {
                $.blockUI();
                var id = $(this).data('id');
                $.getJSON("{{ URL::to('ucretlendirme/kayitdetay') }}", {id: id}, function (event) {
                    if (event.durum) {
                        var ucretlendirme = event.ucretlendirme;
                        $(".yer").html(ucretlendirme.uretimyer.yeradi);
                        $(".fiyatdurum").html(ucretlendirme.fiyatdurum === "0" ? 'Genel' : 'Özel');
                        $(".serino").html(ucretlendirme.ariza_serino);
                        $(".garanti").html(ucretlendirme.ariza_garanti === "0" ? 'Dışında' : 'İçinde');
                        if (ucretlendirme.sayac.sayaccap_id === "1")
                            $(".sayacadi").html(ucretlendirme.sayacadi.sayacadi);
                        else
                            $(".sayacadi").html(ucretlendirme.sayacadi.sayacadi + " - " + ucretlendirme.sayaccap.capadi);
                        oTable6.clear().draw();
                        var indirimdurum = ucretlendirme.indirim;
                        var degisenler = ucretlendirme.parcalar;
                        var genelbirimler = ucretlendirme.genelbirimler;
                        var ozelbirimler = ucretlendirme.ozelbirimler;
                        var genelbirim = JSON.stringify(ucretlendirme.genelbirimler);
                        var ozelbirim = JSON.stringify(ucretlendirme.ozelbirimler);
                        var ucretsizler = ucretlendirme.ucretsizler;
                        var genelbirimi = ucretlendirme.genelparabirimi.birimi;
                        var ozelbirimi = ucretlendirme.ozelparabirimi.birimi;
                        var ozelbirimid = ucretlendirme.ozelparabirimi.id;
                        var genelbirimid = ucretlendirme.genelparabirimi.id;
                        $('#genel').val(ucretlendirme.genel);
                        $('#ozel').val(ucretlendirme.ozel);
                        $('#ucretsiz').val(ucretlendirme.ucretsiz);
                        $('#genelbirim').val(genelbirimi);
                        $('#ozelbirim').val(ozelbirimi);
                        $('#genelbirimid').val(genelbirimid);
                        $('#ozelbirimid').val(ozelbirimid);
                        $('#genelbirimler').val(genelbirim);
                        $('#ozelbirimler').val(ozelbirim);
                        $.each(degisenler, function (index) {
                            if (ucretsizler[index] === "1") {
                                oTable6.row.add([degisenler[index].id, degisenler[index].tanim, 'Evet']).draw();
                            } else {
                                oTable6.row.add([degisenler[index].id, degisenler[index].tanim, 'Hayır']).draw();
                            }
                        });

                        var dolar = 0;
                        var euro = 0;
                        var sterlin = 0;
                        var dovizkuru = event.dovizkuru;
                        var dovizkurutarih = "";
                        $.each(dovizkuru, function (index) {
                            dovizkurutarih = dovizkuru[index].tarih;
                            if (dovizkuru[index].parabirimi_id === "2")
                                euro = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                            else if (dovizkuru[index].parabirimi_id === "3")
                                dolar = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                            else
                                sterlin = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                        });
                        $('.warning').html('<span style="color:red">Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
                        $('#detaydolar').val(dolar);
                        $('#detayeuro').val(euro);
                        $('#detaysterlin').val(sterlin);
                        $('#detaybirim').val(ozelbirimid);
                        $('#detaykurtarih').val(dovizkurutarih);
                        var kur = 1;
                        var kurdurum = false;
                        if (ozelbirimid !== genelbirimid) //parabirimi farklı kur ile çarpılacak
                        {
                            kurdurum = true;
                            if (ozelbirimid === "1") // tl ise
                            {
                                if (genelbirimid === "2") //euro ise
                                    kur = euro;
                                else if (genelbirimid === "3") //dolar ise
                                    kur = dolar;
                                else
                                    kur = sterlin;
                            } else if (ozelbirimid === "2") { //euro ise
                                if (genelbirimid === "1") //tl ise
                                    kur = 1 / euro;
                                else if (genelbirimid === "3") //dolar ise
                                    kur = dolar / euro;
                                else
                                    kur = sterlin / euro;
                            } else if (ozelbirimid === "3") { //dolar ise
                                if (genelbirimid === "1") //tl ise
                                    kur = 1 / dolar;
                                else if (genelbirimid === "2") //euro ise
                                    kur = euro / dolar;
                                else
                                    kur = sterlin / dolar;
                            } else { //sterlin ise
                                if (genelbirimid === "1") //euro ise
                                    kur = 1 / sterlin;
                                else if (genelbirimid === "2") //dolar ise
                                    kur = euro / sterlin;
                                else
                                    kur = dolar / sterlin;
                            }
                        }
                        var fiyat = 0;
                        var indirim = 0;
                        var kdvsiztutar = 0;
                        var kdv = 0;
                        var toplamtutar = 0;
                        var fiyat2 = 0;
                        var indirim2 = 0;
                        var kdvsiztutar2 = 0;
                        var kdv2 = 0;
                        var toplamtutar2 = 0;
                        if (ucretlendirme.fiyatdurum === false) //genel fiyatlar gözükecek
                        {
                            var parabirimi = genelbirimid;
                            var parabirimi2 = "";
                            var genel = ucretlendirme.genel;
                            genel = genel.split(';');
                            $.each(genel, function (index) {
                                if (ucretsizler[index] !== '1') {
                                    if (parabirimi === genelbirimler[index].id) {
                                        fiyat += parseFloat(genel[index]);
                                    } else if (parabirimi2 === "" || parabirimi2.id === genelbirimler[index].id) {
                                        fiyat2 += parseFloat(genel[index]);
                                        parabirimi2 = genelbirimler[index];
                                    } else {
                                        $('.warning').html('<span style="color:red">İki Para Biriminden Fazla Para Birimi Kullanılamaz!</span>');
                                    }
                                }
                            });
                            if (ucretlendirme.ariza_garanti === "1") {
                                fiyat = 0;
                                fiyat2 = 0;
                            } else {
                                fiyat *= kur;
                                if (parabirimi2.id === ozelbirimid) {
                                    fiyat += fiyat2;
                                    fiyat2 = 0;
                                    parabirimi2 = "";
                                }
                            }
                            if (indirimdurum === '1') //indirim varsa
                            {
                                $('.indirim').text('Var');
                                $('.indirimorani').text('%' + parseFloat(ucretlendirme.indirimorani).toFixed(2));
                                indirim = ((fiyat * parseFloat(ucretlendirme.indirimorani)) / 100);
                                indirim2 = ((fiyat2 * parseFloat(ucretlendirme.indirimorani)) / 100);
                                kdvsiztutar = (fiyat - indirim);
                                kdvsiztutar2 = (fiyat2 - indirim2);
                            } else {
                                $('.indirim').text('Yok');
                                $('.indirimorani').text('%' + parseFloat(ucretlendirme.indirimorani).toFixed(2));
                                kdvsiztutar = fiyat;
                                kdvsiztutar2 = fiyat2;
                            }
                            kdv = (kdvsiztutar * 18) / 100;
                            kdv2 = (kdvsiztutar2 * 18) / 100;
                            toplamtutar = kdvsiztutar + kdv;
                            toplamtutar = Math.round(toplamtutar * 2) / 2;
                            toplamtutar2 = kdvsiztutar2 + kdv2;
                            toplamtutar2 = Math.round(toplamtutar2 * 2) / 2;
                            if (ucretlendirme.ariza_garanti === "1") {
                                $('.fiyattutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                                $('.indirimtutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                                $('.kdvsiztutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                                $('.kdvtutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                                $('.toplamtutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                            } else if (ucretlendirme.durum === "4") {
                                $('.fiyattutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                                $('.indirimtutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                                $('.kdvsiztutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                                $('.kdvtutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                                $('.toplamtutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                            } else {
                                if (toplamtutar2 === 0) {
                                    $('.fiyattutar').text(fiyat.toFixed(2) + ' ' + ozelbirimi);
                                    $('.indirimtutar').text(indirim.toFixed(2) + ' ' + ozelbirimi);
                                    $('.kdvsiztutar').text(kdvsiztutar.toFixed(2) + ' ' + ozelbirimi);
                                    $('.kdvtutar').text(kdv.toFixed(2) + ' ' + ozelbirimi);
                                    $('.toplamtutar').text(toplamtutar.toFixed(2) + ' ' + ozelbirimi);
                                } else {
                                    $('.fiyattutar').text(fiyat.toFixed(2) + ' ' + ozelbirimi + ' + ' + fiyat2.toFixed(2) + ' ' + parabirimi2.birimi);
                                    $('.indirimtutar').text(indirim.toFixed(2) + ' ' + ozelbirimi + ' + ' + indirim2.toFixed(2) + ' ' + parabirimi2.birimi);
                                    $('.kdvsiztutar').text(kdvsiztutar.toFixed(2) + ' ' + ozelbirimi + ' + ' + kdvsiztutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                                    $('.kdvtutar').text(kdv.toFixed(2) + ' ' + ozelbirimi + ' + ' + kdv2.toFixed(2) + ' ' + parabirimi2.birimi);
                                    $('.toplamtutar').text(toplamtutar.toFixed(2) + ' ' + ozelbirimi + ' + ' + toplamtutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                                }
                            }
                        } else { //ozel fiyatlar
                            parabirimi = ozelbirimid;
                            parabirimi2 = "";
                            var ozel = ucretlendirme.ozel;
                            ozel = ozel.split(';');
                            $.each(ozel, function (index) {
                                if (ucretsizler[index] !== '1') {
                                    if (parabirimi === ozelbirimler[index].id) {
                                        fiyat += parseFloat(ozel[index]);
                                    } else if (parabirimi2 === "" || parabirimi2.id === ozelbirimler[index].id) {
                                        fiyat2 += parseFloat(ozel[index]);
                                        parabirimi2 = ozelbirimler[index];
                                    } else {
                                        $('.warning').html('<span style="color:red">İki Para Biriminden Fazla Para Birimi Kullanılamaz!</span>');
                                    }
                                }
                            });
                            if (ucretlendirme.ariza_garanti === "1") {
                                fiyat = 0;
                                fiyat2 = 0;
                            }
                            if (parabirimi2.id === ozelbirimid) {
                                fiyat += fiyat2;
                                fiyat2 = 0;
                                parabirimi2 = "";
                            }
                            if (indirimdurum === '1') //indirim varsa
                            {
                                $('.indirim').text('Var');
                                $('.indirimorani').text('%' + parseFloat(ucretlendirme.indirimorani).toFixed(2));
                                indirim = ((fiyat * parseFloat(ucretlendirme.indirimorani)) / 100);
                                indirim2 = ((fiyat2 * parseFloat(ucretlendirme.indirimorani)) / 100);
                                kdvsiztutar = (fiyat - indirim);
                                kdvsiztutar2 = (fiyat2 - indirim2);
                            } else {
                                $('.indirim').text('Yok');
                                $('.indirimorani').text('%' + parseFloat(ucretlendirme.indirimorani).toFixed(2));
                                kdvsiztutar = fiyat;
                                kdvsiztutar2 = fiyat2;
                            }
                            kdv = (kdvsiztutar * 18) / 100;
                            kdv2 = (kdvsiztutar2 * 18) / 100;
                            toplamtutar = kdvsiztutar + kdv;
                            toplamtutar = Math.round(toplamtutar * 2) / 2;
                            toplamtutar2 = kdvsiztutar2 + kdv2;
                            toplamtutar2 = Math.round(toplamtutar2 * 2) / 2;
                            if (ucretlendirme.ariza_garanti === "1") {
                                $('.fiyattutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                                $('.indirimtutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                                $('.kdvsiztutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                                $('.kdvtutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                                $('.toplamtutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                            } else if (ucretlendirme.durum === "4") {
                                $('.fiyattutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                                $('.indirimtutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                                $('.kdvsiztutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                                $('.kdvtutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                                $('.toplamtutar').text((0.00).toFixed(2) + ' ' + ozelbirimi);
                            } else {
                                if (toplamtutar2 === 0) {
                                    $('.fiyattutar').text(fiyat.toFixed(2) + ' ' + ozelbirimi);
                                    $('.indirimtutar').text(indirim.toFixed(2) + ' ' + ozelbirimi);
                                    $('.kdvsiztutar').text(kdvsiztutar.toFixed(2) + ' ' + ozelbirimi);
                                    $('.kdvtutar').text(kdv.toFixed(2) + ' ' + ozelbirimi);
                                    $('.toplamtutar').text(toplamtutar.toFixed(2) + ' ' + ozelbirimi);
                                } else {
                                    $('.fiyattutar').text(fiyat.toFixed(2) + ' ' + ozelbirimi + ' + ' + fiyat2.toFixed(2) + ' ' + parabirimi2.birimi);
                                    $('.indirimtutar').text(indirim.toFixed(2) + ' ' + ozelbirimi + ' + ' + indirim2.toFixed(2) + ' ' + parabirimi2.birimi);
                                    $('.kdvsiztutar').text(kdvsiztutar.toFixed(2) + ' ' + ozelbirimi + ' + ' + kdvsiztutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                                    $('.kdvtutar').text(kdv.toFixed(2) + ' ' + ozelbirimi + ' + ' + kdv2.toFixed(2) + ' ' + parabirimi2.birimi);
                                    $('.toplamtutar').text(toplamtutar.toFixed(2) + ' ' + ozelbirimi + ' + ' + toplamtutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                                }
                            }
                        }
                        if (kurdurum)
                            $('.detaykur').removeClass('hide');
                        else
                            $('.detaykur').addClass('hide');
                    } else {
                        toastr[event.type](event.text, event.type);
                        $('#fiyat-detay').modal('hide');
                    }
                    $.unblockUI();
                });
            });
            $(document).on("click", ".onayla", function () {
                $.blockUI();
                var id = $('#secilen').val();
                var action = $('#form_sample_3').data('action');
                $('#form_sample_3').prop('action', action + '/' + id);
                $.getJSON("{{ URL::to('abone/onaybilgi') }}", {id: id}, function (event) {
                    if (event.durum) {
                        var ucretlendirilen = event.ucretlendirilen;
                        $(".onayyer").html(ucretlendirilen.uretimyer.yeradi);
                        $(".onaycariadi").html(ucretlendirilen.netsiscari.cariadi);
                        $('#onayid').val(ucretlendirilen.id);
                        if (ucretlendirilen.durum === "1") {
                            var secilenler = ucretlendirilen.secilenler;
                            var secilenlist = secilenler.split(',');
                            $('#onayadet').val(secilenlist.length);
                        } else {
                            var reddedilenler = ucretlendirilen.reddedilenler;
                            var redlist = reddedilenler.split(',');
                            $('#onayadet').val(redlist.length);
                        }
                        oTable3.clear().draw();
                        var genelkdvsiztutar = 0;
                        var genelkdvtutar = 0;
                        var geneltoplamtutar = 0;
                        var genelkdvsiztutar2 = 0;
                        var genelkdvtutar2 = 0;
                        var geneltoplamtutar2 = 0;
                        var dolar = 0;
                        var euro = 0;
                        var sterlin = 0;
                        var dovizkuru = event.dovizkuru;
                        var dovizkurutarih = "";
                        $.each(dovizkuru, function (index) {
                            dovizkurutarih = dovizkuru[index].tarih;
                            if (dovizkuru[index].parabirimi_id === "2")
                                euro = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                            else if (dovizkuru[index].parabirimi_id === "3")
                                dolar = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                            else
                                sterlin = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                        });
                        $('#onaydolar').val(dolar);
                        $('#onayeuro').val(euro);
                        $('#onaysterlin').val(sterlin);
                        var parabirimi = ucretlendirilen.parabirimi;
                        var parabirimi2 = ucretlendirilen.parabirimi2;
                        $('#onaybirim').val(parabirimi.id);
                        $('#onaybirim2').val(parabirimi2 === null ? "" : parabirimi2.id);
                        $('#onaybirimi').val(parabirimi.birimi);
                        $('#onaybirimi2').val(parabirimi2 === null ? "" : parabirimi2.birimi);
                        $('#onaykurtarih').val(dovizkurutarih);
                        var arizafiyat = ucretlendirilen.arizafiyat;
                        $.each(arizafiyat, function (index) {
                            var garanti = arizafiyat[index].ariza_garanti === "0" ? 'Dışında' : 'İçinde';
                            var sayacadi = arizafiyat[index].sayacadi.sayacadi;
                            var serino = arizafiyat[index].ariza_serino;
                            var birim = arizafiyat[index].parabirimi;
                            var birim2 = arizafiyat[index].parabirimi2;
                            var fiyat = parseFloat(arizafiyat[index].fiyat);
                            var fiyat2 = parseFloat(arizafiyat[index].fiyat2);
                            var indirimorani = parseFloat(arizafiyat[index].indirimorani);
                            var indirim = (fiyat * indirimorani) / 100;
                            var indirim2 = (fiyat2 * indirimorani) / 100;
                            var kdvsiztutar = parseFloat(arizafiyat[index].tutar);
                            var kdvsiztutar2 = parseFloat(arizafiyat[index].tutar2);
                            var kdv = parseFloat(arizafiyat[index].kdv);
                            var kdv2 = parseFloat(arizafiyat[index].kdv2);
                            var toplamtutar = parseFloat(arizafiyat[index].toplamtutar);
                            var toplamtutar2 = parseFloat(arizafiyat[index].toplamtutar2);
                            if (toplamtutar2 > 0) {
                                oTable3.row.add([arizafiyat[index].id, serino, sayacadi, garanti, kdvsiztutar.toFixed(2) + ' ' + birim.birimi + ' + ' + kdvsiztutar2.toFixed(2) + ' ' + birim2.birimi,
                                    kdv.toFixed(2) + ' ' + birim.birimi + ' + ' + kdv2.toFixed(2) + ' ' + birim2.birimi, toplamtutar.toFixed(2) + ' ' + birim.birimi + ' + ' + toplamtutar2.toFixed(2) + ' ' + birim2.birimi]).draw();
                            } else {
                                oTable3.row.add([arizafiyat[index].id, serino, sayacadi, garanti, kdvsiztutar.toFixed(2) + ' ' + birim.birimi,
                                    kdv.toFixed(2) + ' ' + birim.birimi, toplamtutar.toFixed(2) + ' ' + birim.birimi]).draw();
                            }
                            if (birim.id === parabirimi.id) {
                                toplamtutar = Math.round(toplamtutar * 2) / 2;
                                if (birim2 !== null) {
                                    if (birim2.id === parabirimi2.id) {
                                        toplamtutar2 = Math.round(toplamtutar2 * 2) / 2;
                                    } else {
                                        toplamtutar2 = 0;
                                        $('.onaywarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                        $('.onay').prop('disabled', true);
                                    }
                                } else {
                                    toplamtutar2 = 0;
                                }
                            } else {
                                var kur = 1;
                                $('.onaywarning').html('<span style="color:red">Kur Tarihi: ' + dovizkurutarih + '. Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
                                if (parabirimi.id === "1") // tl ise
                                {
                                    if (birim.id === "2") //euro ise
                                        kur = euro;
                                    else if (birim.id === "3") //dolar ise
                                        kur = dolar;
                                    else
                                        kur = sterlin;
                                } else if (parabirimi.id === "2") { //euro ise
                                    if (birim.id === "1") //tl ise
                                        kur = 1 / euro;
                                    else if (birim.id === "3") //dolar ise
                                        kur = dolar / euro;
                                    else
                                        kur = sterlin / euro;
                                } else if (parabirimi.id === "3") { //dolar ise
                                    if (birim.id === "1") //tl ise
                                        kur = 1 / dolar;
                                    else if (birim.id === "2") //euro ise
                                        kur = euro / dolar;
                                    else
                                        kur = sterlin / dolar;
                                } else { //sterlin ise
                                    if (birim.id === "1") //euro ise
                                        kur = 1 / sterlin;
                                    else if (birim.id === "2") //dolar ise
                                        kur = euro / sterlin;
                                    else
                                        kur = dolar / sterlin;
                                }
                                fiyat *= kur;
                                if (birim2 !== null) {
                                    if (birim2.id === parabirimi.id) {
                                        fiyat += fiyat2;
                                        fiyat2 = 0;
                                    } else if (birim2.id !== parabirimi2.id) {
                                        fiyat2 = 0;
                                        $('.onaywarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                        $('.onay').prop('disabled', true);
                                    }
                                } else {
                                    fiyat2 = 0;
                                }
                                indirim = ((fiyat * indirimorani) / 100);
                                indirim2 = ((fiyat2 * indirimorani) / 100);
                                kdvsiztutar = (fiyat - indirim);
                                kdvsiztutar2 = (fiyat2 - indirim2);
                                kdv = (kdvsiztutar * 18) / 100;
                                kdv2 = (kdvsiztutar2 * 18) / 100;
                                toplamtutar = kdvsiztutar + kdv;
                                toplamtutar = Math.round(toplamtutar * 2) / 2;
                                toplamtutar2 = kdvsiztutar2 + kdv2;
                                toplamtutar2 = Math.round(toplamtutar2 * 2) / 2;
                            }
                            genelkdvsiztutar += kdvsiztutar;
                            genelkdvtutar += kdv;
                            geneltoplamtutar += toplamtutar;
                            genelkdvsiztutar2 += kdvsiztutar2;
                            genelkdvtutar2 += kdv2;
                            geneltoplamtutar2 += toplamtutar2;
                        });
                        $('.onaykur').removeClass('hide');
                        if (geneltoplamtutar2 === 0) {
                            $('.onaytutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi.birimi);
                            $('.onaykdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi.birimi);
                            $('.onaytoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi.birimi);
                        } else {
                            $('.onaytutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi.birimi + ' + ' + genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                            $('.onaykdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi.birimi + ' + ' + genelkdvtutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                            $('.onaytoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi.birimi + ' + ' + geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                        }
                        $('#onaytutar').val(genelkdvsiztutar.toFixed(2));
                        $('#onaykdvtutar').val(genelkdvtutar.toFixed(2));
                        $('#onaytoplamtutar').val(geneltoplamtutar.toFixed(2));
                        $('#onaytutar2').val(genelkdvsiztutar2.toFixed(2));
                        $('#onaykdvtutar2').val(genelkdvtutar2.toFixed(2));
                        $('#onaytoplamtutar2').val(geneltoplamtutar2.toFixed(2));
                    } else {
                        $('#onayla').modal('hide');
                        toastr["warning"](event.durum_mesaj, 'Onaylama Bilgi Hatası');
                    }
                    $.unblockUI();
                });
            });
            $(document).on("click", ".reddet", function () {
                $.blockUI();
                var id = $('#secilen').val();
                var action = $('#form_sample_5').data('action');
                $('#form_sample_5').prop('action', action + '/' + id);
                $.getJSON("{{ URL::to('abone/onaybilgi') }}", {id: id}, function (event) {
                    if (event.durum) {
                        var ucretlendirilen = event.ucretlendirilen;
                        $(".redyer").html(ucretlendirilen.uretimyer.yeradi);
                        $(".redcariadi").html(ucretlendirilen.netsiscari.cariadi);
                        $('#redid').val(ucretlendirilen.id);
                        if (ucretlendirilen.durum === "1") {
                            var secilenler = ucretlendirilen.secilenler;
                            var secilenlist = secilenler.split(',');
                            $('#redadet').val(secilenlist.length);
                        } else {
                            var reddedilenler = ucretlendirilen.reddedilenler;
                            var redlist = reddedilenler.split(',');
                            $('#redadet').val(redlist.length);
                        }
                        oTable5.clear().draw();
                        var dolar = 0;
                        var euro = 0;
                        var sterlin = 0;
                        var dovizkuru = event.dovizkuru;
                        var dovizkurutarih = "";
                        $.each(dovizkuru, function (index) {
                            dovizkurutarih = dovizkuru[index].tarih;
                            if (dovizkuru[index].parabirimi_id === "2")
                                euro = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                            else if (dovizkuru[index].parabirimi_id === "3")
                                dolar = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                            else
                                sterlin = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                        });
                        var arizafiyat = ucretlendirilen.arizafiyat;
                        $.each(arizafiyat, function (index) {
                            var garanti = arizafiyat[index].ariza_garanti === "0" ? 'Dışında' : 'İçinde';
                            var sayacadi = arizafiyat[index].sayacadi.sayacadi;
                            var serino = arizafiyat[index].ariza_serino;
                            var birim = arizafiyat[index].parabirimi;
                            var birim2 = arizafiyat[index].parabirimi2;
                            var kdvsiztutar = parseFloat(arizafiyat[index].tutar);
                            var kdvsiztutar2 = parseFloat(arizafiyat[index].tutar2);
                            var kdv = parseFloat(arizafiyat[index].kdv);
                            var kdv2 = parseFloat(arizafiyat[index].kdv2);
                            var toplamtutar = parseFloat(arizafiyat[index].toplamtutar);
                            var toplamtutar2 = parseFloat(arizafiyat[index].toplamtutar2);
                            if (toplamtutar2 > 0) {
                                oTable5.row.add([arizafiyat[index].id, serino, sayacadi, garanti, kdvsiztutar.toFixed(2) + ' ' + birim.birimi + ' + ' + kdvsiztutar2.toFixed(2) + ' ' + birim2.birimi,
                                    kdv.toFixed(2) + ' ' + birim.birimi + ' + ' + kdv2.toFixed(2) + ' ' + birim2.birimi, toplamtutar.toFixed(2) + ' ' + birim.birimi + ' + ' + toplamtutar2.toFixed(2) + ' ' + birim2.birimi]).draw();
                            } else {
                                oTable5.row.add([arizafiyat[index].id, serino, sayacadi, garanti, kdvsiztutar.toFixed(2) + ' ' + birim.birimi,
                                    kdv.toFixed(2) + ' ' + birim.birimi, toplamtutar.toFixed(2) + ' ' + birim.birimi]).draw();
                            }
                        });
                    } else {
                        $('#reddet').modal('hide');
                        toastr["warning"]('Kullanıcı Yetkili Değil', 'Kullanıcı Hatası');
                    }
                    $.unblockUI();
                });
            });
            $(document).on("click", ".neden", function () {
                $.blockUI();
                var id = $(this).data('id');
                $.getJSON("{{ URL::to('ucretlendirme/reddedilenbilgi') }}", {id: id}, function (event) {
                    if (event.durum) {
                        var ucretlendirilen = event.ucretlendirilen;
                        $(".yer").html(ucretlendirilen.uretimyer.yeradi);
                        $(".redtarihi").html(ucretlendirilen.reddetmetarihi);
                        $(".redneden").html(ucretlendirilen.musterinotu);
                        oTable4.clear().draw();
                        var genelfiyat = 0;
                        var genelindirim = 0;
                        var genelkdvsiztutar = 0;
                        var genelkdvtutar = 0;
                        var geneltoplamtutar = 0;
                        var genelfiyat2 = 0;
                        var genelindirim2 = 0;
                        var genelkdvsiztutar2 = 0;
                        var genelkdvtutar2 = 0;
                        var geneltoplamtutar2 = 0;
                        var dolar = 0;
                        var euro = 0;
                        var sterlin = 0;
                        var dovizkuru = event.dovizkuru;
                        var dovizkurutarih = "";
                        $.each(dovizkuru, function (index) {
                            dovizkurutarih = dovizkuru[index].tarih;
                            if (dovizkuru[index].parabirimi_id === "2")
                                euro = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                            else if (dovizkuru[index].parabirimi_id === "3")
                                dolar = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                            else
                                sterlin = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                        });
                        $('.redwarning').html('<span style="color:red">Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
                        $('#reddolar').val(dolar);
                        $('#redeuro').val(euro);
                        $('#redsterlin').val(sterlin);
                        var parabirimi = ucretlendirilen.parabirimi;
                        var parabirimi2 = ucretlendirilen.parabirimi2;
                        $('#redkurtarih').val(dovizkurutarih);
                        var arizafiyat = ucretlendirilen.arizafiyat;
                        var kurdurum = false;
                        $.each(arizafiyat, function (index) {
                            var garanti = arizafiyat[index].ariza_garanti === "0" ? 'Dışında' : 'İçinde';
                            var sayacadi = arizafiyat[index].sayacadi.sayacadi;
                            var serino = arizafiyat[index].ariza_serino;
                            var birim = arizafiyat[index].parabirimi;
                            var birim2 = arizafiyat[index].parabirimi2;
                            var fiyat = parseFloat(arizafiyat[index].fiyat);
                            var fiyat2 = parseFloat(arizafiyat[index].fiyat2);
                            var indirimorani = parseFloat(arizafiyat[index].indirimorani);
                            var indirim = (fiyat * indirimorani) / 100;
                            var indirim2 = (fiyat2 * indirimorani) / 100;
                            var kdvsiztutar = parseFloat(arizafiyat[index].tutar);
                            var kdvsiztutar2 = parseFloat(arizafiyat[index].tutar2);
                            var kdv = parseFloat(arizafiyat[index].kdv);
                            var kdv2 = parseFloat(arizafiyat[index].kdv2);
                            var toplamtutar = parseFloat(arizafiyat[index].toplamtutar);
                            var toplamtutar2 = parseFloat(arizafiyat[index].toplamtutar2);
                            var durum = '';
                            switch (arizafiyat[index].durum) {
                                case '2':
                                    durum = 'Fiyatlandırma Bekliyor';
                                    break;
                                case '3':
                                    durum = 'Tekrar Ücretlendirildi';
                                    break;
                                case '4':
                                    durum = 'Geri Gönderildi';
                                    break;
                                case '5':
                                    durum = 'Garanti İçi Gönderildi';
                                    break;
                            }
                            if (toplamtutar2 > 0) {
                                oTable4.row.add([arizafiyat[index].id, serino, sayacadi, garanti, fiyat.toFixed(2) + ' ' + birim.birimi + ' + ' + fiyat2.toFixed(2) + ' ' + birim2.birimi,
                                    indirimorani.toFixed(2) + '%', kdvsiztutar.toFixed(2) + ' ' + birim.birimi + ' + ' + kdvsiztutar2.toFixed(2) + ' ' + birim2.birimi, kdv.toFixed(2) + ' ' + birim.birimi + ' + ' + kdv2.toFixed(2) + ' ' + birim2.birimi,
                                    toplamtutar.toFixed(2) + ' ' + birim.birimi + ' + ' + toplamtutar2.toFixed(2) + ' ' + birim2.birimi, durum]).draw();
                            } else {
                                oTable4.row.add([arizafiyat[index].id, serino, sayacadi, garanti, fiyat.toFixed(2) + ' ' + birim.birimi, indirimorani.toFixed(2) + '%',
                                    kdvsiztutar.toFixed(2) + ' ' + birim.birimi, kdv.toFixed(2) + ' ' + birim.birimi, toplamtutar.toFixed(2) + ' ' + birim.birimi, durum]).draw();
                            }
                            if (birim.id === parabirimi.id) {
                                toplamtutar = Math.round(toplamtutar * 2) / 2;
                                if (birim2 !== null) {
                                    if (birim2.id === parabirimi2.id) {
                                        toplamtutar2 = Math.round(toplamtutar2 * 2) / 2;
                                    } else {
                                        toplamtutar2 = 0;
                                        $('.redwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    }
                                } else {
                                    toplamtutar2 = 0;
                                }
                            } else {
                                var kur = 1;
                                kurdurum = true;
                                if (parabirimi.id === "1") // tl ise
                                {
                                    if (birim.id === "2") //euro ise
                                        kur = euro;
                                    else if (birim.id === "3") //dolar ise
                                        kur = dolar;
                                    else
                                        kur = sterlin;
                                } else if (parabirimi.id === "2") { //euro ise
                                    if (birim.id === "1") //tl ise
                                        kur = 1 / euro;
                                    else if (birim.id === "3") //dolar ise
                                        kur = dolar / euro;
                                    else
                                        kur = sterlin / euro;
                                } else if (parabirimi.id === "3") { //dolar ise
                                    if (birim.id === "1") //tl ise
                                        kur = 1 / dolar;
                                    else if (birim.id === "2") //euro ise
                                        kur = euro / dolar;
                                    else
                                        kur = sterlin / dolar;
                                } else { //sterlin ise
                                    if (birim.id === "1") //euro ise
                                        kur = 1 / sterlin;
                                    else if (birim.id === "2") //dolar ise
                                        kur = euro / sterlin;
                                    else
                                        kur = dolar / sterlin;
                                }
                                fiyat *= kur;
                                if (birim2 !== null) {
                                    if (birim2.id === parabirimi.id) {
                                        fiyat += fiyat2;
                                        fiyat2 = 0;
                                    } else if (birim2.id !== parabirimi2.id) {
                                        fiyat2 = 0;
                                        $('.redwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    }
                                } else {
                                    fiyat2 = 0;
                                }
                                indirim = ((fiyat * indirimorani) / 100);
                                indirim2 = ((fiyat2 * indirimorani) / 100);
                                kdvsiztutar = (fiyat - indirim);
                                kdvsiztutar2 = (fiyat2 - indirim2);
                                kdv = (kdvsiztutar * 18) / 100;
                                kdv2 = (kdvsiztutar2 * 18) / 100;
                                toplamtutar = kdvsiztutar + kdv;
                                toplamtutar = Math.round(toplamtutar * 2) / 2;
                                toplamtutar2 = kdvsiztutar2 + kdv2;
                                toplamtutar2 = Math.round(toplamtutar2 * 2) / 2;
                            }
                            genelfiyat += fiyat;
                            genelindirim += indirim;
                            genelkdvsiztutar += kdvsiztutar;
                            genelkdvtutar += kdv;
                            geneltoplamtutar += toplamtutar;
                            genelfiyat2 += fiyat2;
                            genelindirim2 += indirim2;
                            genelkdvsiztutar2 += kdvsiztutar2;
                            genelkdvtutar2 += kdv2;
                            geneltoplamtutar2 += toplamtutar2;
                        });
                        if (kurdurum)
                            $('.redkur').removeClass('hide');
                        else
                            $('.redkur').addClass('hide');
                        if (geneltoplamtutar2 === 0) {
                            $('.redtutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi.birimi);
                            $('.redindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi.birimi);
                            $('.redkdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi.birimi);
                            $('.redkdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi.birimi);
                            $('.redtoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi.birimi);
                        } else {
                            $('.redtutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi.birimi + ' + ' + genelfiyat2.toFixed(2) + ' ' + parabirimi2.birimi);
                            $('.redindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi.birimi + ' + ' + genelindirim2.toFixed(2) + ' ' + parabirimi2.birimi);
                            $('.redkdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi.birimi + ' + ' + genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                            $('.redkdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi.birimi + ' + ' + genelkdvtutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                            $('.redtoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi.birimi + ' + ' + geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                        }
                    } else {
                        $('#redneden').modal('hide');
                        toastr[event.type](event.text, event.title);
                    }

                    $.unblockUI();
                });
            });

            $('.fiyatlandirma').click(function () {
                var ucretlendirilen = $('#sample_editable_1 .active .id').text();
                if (ucretlendirilen !== null) {
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
                    $.redirectPost(redirect, {ucretlendirilen: ucretlendirilen, ireport: '1'});
                }
            });

            $('#formonay').click(function () {
                $('#form_sample_3').submit();
            });
            $('#formred').click(function () {
                $('#form_sample_5').submit();
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
                        <i class="fa fa-tag"></i>Ücretlendirmesi Belirlenen Sayaçlar
                    </div>
                    <div class="actions">
                        <a class="btn btn-default btn-sm fiyatlandirma">
                            <i class="fa fa-try"></i> Fiyatlandırma </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Üretim Yeri</th>
                                <th>Sayaç Sayısı</th>
                                <th>Durum</th>
                                <th>Fiyat</th>
                                <th>Kullanıcı</th>
                                <th>Tarih</th>
                                <th></th><th></th><th></th><th></th>
                                <th>Detay</th>
                            </tr>
                        </thead>
                    </table>
                    <form action="" id="form_sample" class="form-horizontal" novalidate="novalidate">
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-xs-offset-2 col-xs-10">
                                    <a class='btn green onayla hide' href='#onayla' data-toggle='modal' data-id=''>Onayla</a>
                                    <a class='btn green reddet hide' href='#reddet' data-toggle='modal' data-id=''>Reddet</a>
                                </div>
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
                                    <i class="fa fa-pencil"></i>Fiyatlandırma Detayı
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Fiyatlandırma Detayı</h3>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Yeri:</label>
                                            <label class="col-sm-5 col-xs-8 yer" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_2">
                                                <thead>
                                                <tr>
                                                    <th class="">#</th>
                                                    <th>Seri No</th>
                                                    <th>Sayaç Adı</th>
                                                    <th>Garanti</th>
                                                    <th>Fiyat Durum</th>
                                                    <th>&nbsp;Fiyatı&nbsp;</th>
                                                    <th>İndirim</th>
                                                    <th>Tutar</th>
                                                    <th>Kdv Tutarı</th>
                                                    <th>Toplam Tutar</th>
                                                    <th>Detay</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="col-xs-12 onizlemekur">
                                                    <label class="control-label col-xs-12 onizlemewarning" style="text-align: center"></label>
                                                    <input id="onizlemeeuro" class="hide">
                                                    <input id="onizlemedolar" class="hide">
                                                    <input id="onizlemesterlin" class="hide">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TUTAR:</label>
                                                    <label class="col-xs-6 onizlemetutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">İNDİRİM MİKTARI:</label>
                                                    <label class="col-xs-6 onizlemeindirimtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDVSİZ TUTAR:</label>
                                                    <label class="col-xs-6 onizlemekdvsiztutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDV TUTARI:</label>
                                                    <label class="col-xs-6 onizlemekdvtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TOPLAM TUTAR:</label>
                                                    <label class="col-xs-6 onizlemetoplamtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <input class="hide" id="onizlemetutar" name="onizlemetutar"/>
                                                <input class="hide" id="onizlemeindirimtutar" name="onizlemeindirimtutar"/>
                                                <input class="hide" id="onizlemekdvsiztutar" name="onizlemekdvsiztutar"/>
                                                <input class="hide" id="onizlemekdvtutar" name="onizlemekdvtutar"/>
                                                <input class="hide" id="onizlemetoplamtutar" name="onizlemetoplamtutar"/>
                                                <input class="hide" id="onizlemebirim" name="onizlemebirim"/>
                                                <input class="hide" id="onizlemebirim2" name="onizlemebirim2"/>
                                                <input class="hide" id="onizlemebirimi" name="onizlemebirimi"/>
                                                <input class="hide" id="onizlemebirimi2" name="onizlemebirimi2"/>
                                                <input class="hide" id="onizlemekurtarih" name="onizlemekurtarih"/>
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
    <div class="modal fade" id="redneden" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-close"></i>Reddedilen Fiyatlandırma Detayı
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_4" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Reddedilen Fiyatlandırma Detayı</h3>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Yeri:</label>
                                            <label class="col-xs-8 yer" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Reddetme Tarihi:</label>
                                            <label class="col-xs-8 redtarihi" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Nedeni:</label>
                                            <label class="col-xs-8 redneden" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_4">
                                                <thead>
                                                <tr>
                                                    <th class="">#</th>
                                                    <th>Seri No</th>
                                                    <th>Sayaç Adı</th>
                                                    <th>Garanti</th>
                                                    <th>&nbsp;Fiyatı&nbsp;</th>
                                                    <th>İndirim</th>
                                                    <th>Tutar</th>
                                                    <th>Kdv Tutarı</th>
                                                    <th>Toplam Tutar</th>
                                                    <th>Durum</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-6 col-xs-12 ">
                                                <div class="col-xs-12 redkur">
                                                    <label class="control-label col-xs-12 redwarning" style="text-align:center"></label>
                                                    <input id="redeuro" class="hide">
                                                    <input id="reddolar" class="hide">
                                                    <input id="redsterlin" class="hide">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TUTAR:</label>
                                                    <label class="col-xs-6 redtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">İNDİRİM MİKTARI:</label>
                                                    <label class="col-xs-6 redindirimtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDVSİZ TUTAR:</label>
                                                    <label class="col-xs-6 redkdvsiztutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDV TUTARI:</label>
                                                    <label class="col-xs-6 redkdvtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TOPLAM TUTAR:</label>
                                                    <label class="col-xs-6 redtoplamtutar" style="padding-top: 9px">0.00</label>
                                                </div>
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
    <div class="modal fade" id="onayla" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Sayaçların Fiyatlandırma Onaylanacak
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{URL::to('abone/onayla')}}" data-action="{{URL::to('abone/onayla')}}" id="form_sample_3" method="POST" class="form-horizontal" novalidate="novalidate" enctype="multipart/form-data" >
                                    <div class="form-body">
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <h3 class="form-section">Aşağıdaki Sayaçlar Onayınız ile Beraber Size Tamiri Yapılarak Gönderilecektir</h3>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Yeri:</label>
                                            <label class="col-xs-8 onayyer" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-xs-8 onaycariadi" style="padding-top: 9px"></label>
                                        </div>
                                        <input class="hide" id="onayadet" name="onayadet"/>
                                        <input class="hide" id="onayid" name="onayid"/>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_3">
                                                <thead>
                                                <tr>
                                                    <th class="">#</th>
                                                    <th>Seri No</th>
                                                    <th>Sayaç Adı</th>
                                                    <th>Garanti</th>
                                                    <th>Tutar</th>
                                                    <th>Kdv Tutarı</th>
                                                    <th>Toplam Tutar</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="col-xs-12 onaykur">
                                                    <label class="control-label col-xs-12 onaywarning" style="text-align:center"></label>
                                                    <input id="onayeuro" class="hide">
                                                    <input id="onaydolar" class="hide">
                                                    <input id="onaysterlin" class="hide">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6" style="color:red">TUTAR:</label>
                                                    <label class="col-xs-6 onaytutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6" style="color:red">KDV TUTARI:</label>
                                                    <label class="col-xs-6 onaykdvtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6" style="color:red">TOPLAM TUTAR:</label>
                                                    <label class="col-xs-6 onaytoplamtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <input class="hide" id="onaytutar" name="onaytutar"/>
                                                <input class="hide" id="onaytutar2" name="onaytutar2"/>
                                                <input class="hide" id="onaykdvtutar" name="onaykdvtutar"/>
                                                <input class="hide" id="onaykdvtutar2" name="onaykdvtutar2"/>
                                                <input class="hide" id="onaytoplamtutar" name="onaytoplamtutar"/>
                                                <input class="hide" id="onaytoplamtutar2" name="onaytoplamtutar2"/>
                                                <input class="hide" id="onaybirim" name="onaybirim"/>
                                                <input class="hide" id="onaybirim2" name="onaybirim2"/>
                                                <input class="hide" id="onaybirimi" name="onaybirimi"/>
                                                <input class="hide" id="onaybirimi2" name="onaybirimi2"/>
                                                <input class="hide" id="onaykurtarih" name="onaykurtarih"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-xs-12" style="font-size: 16px;text-indent: 30px">
                                                Size gönderilen Onay Formu'nu imzalayıp buradan yüklediğinizde tarafımıza onayladığınıza dair bilgilendirme gelecektir.
                                                Bilgilendirmeyi aldığımızda sayacınıza ait ücretin yatırıldığı kontrol edilip sayacınız paketlenecektir ve size gönderilecektir.
                                            </div>
                                        </div>
                                        <h4 class="form-section" style="padding-left:20px">İmzalanan formu buradan yükleyebilirsiniz</h4>
                                        <div class="form-group">
                                            <div class="col-xs-9" style="padding-left: 40px"><span class="required" aria-required="true" style="color: red"> * </span>
                                                <div id="file" class="fileinput fileinput-new" data-provides="fileinput">
                                                    <span class="btn green btn-file">
                                                        <span class="fileinput-new"> Dosya Seç</span>
                                                        <span class="fileinput-exists"> Değiştir </span>
                                                        <input type="file" id="eklenendosya" name="eklenendosya" accept="image/jpeg,image/gif,image/png,application/pdf">
                                                    </span>
                                                    <span class="fileinput-filename"> </span> &nbsp;
                                                    <a href="" class="close fileinput-exists" data-dismiss="fileinput"> </a>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="form-section" style="padding-left:20px;color: red">!!İkinci bir onaylama seçeneği olarak online ödeme sistemi yakında sisteme eklenecektir!!</h4>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green onay" data-toggle="modal" data-target="#onayconfirm">Onayla</button>
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
    <div class="modal fade" id="reddet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Sayaçlara ait Fiyatlar Reddedilecek
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{URL::to('abone/reddet')}}" data-action="{{URL::to('abone/reddet')}}" id="form_sample_5" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <h3 class="form-section">Aşağıdaki Sayaçların Fiyatlandırması Reddedilecektir?</h3>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Yeri:</label>
                                            <label class="col-xs-8 redyer" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-xs-8 redcariadi" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-xs-4">Reddetme Nedeni:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><input type="text" id="redneden" name="redneden" value="{{ Input::old('redneden') }}"
                                                       placeholder="Neden Fiyatı Reddettiğinizi Belirtiniz!" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <input class="hide" id="redadet" name="redadet"/>
                                        <input class="hide" id="redid" name="redid"/>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_5">
                                                <thead>
                                                <tr>
                                                    <th class="">#</th>
                                                    <th>Seri No</th>
                                                    <th>Sayaç Adı</th>
                                                    <th>Garanti</th>
                                                    <th>Tutar</th>
                                                    <th>Kdv Tutarı</th>
                                                    <th>Toplam Tutar</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green redd" data-toggle="modal" data-target="#redconfirm">Reddet</button>
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
    <div class="modal fade" id="fiyat-detay" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Ücretlendirme Detayı
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_6" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Ücretlendirme Detayı</h3>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Yeri:</label>
                                            <label class="col-xs-8 yer" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Fiyat Durumu:</label>
                                            <label class="col-xs-8 fiyatdurum" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">SeriNo:</label>
                                            <label class="col-xs-8 serino" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Garanti:</label>
                                            <label class="col-xs-8 garanti" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Sayaç Adı:</label>
                                            <label class="col-xs-8 sayacadi" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_6">
                                                <thead>
                                                <tr>
                                                    <th class="">#</th>
                                                    <th>Değişen Parça</th>
                                                    <th class="ucretsiz">Ücretsiz</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-7 col-xs-12 ">
                                                <div class="col-sm-6 col-xs-12 detaykur">
                                                    <input id="detayeuro" class="hide">
                                                    <input id="detaydolar" class="hide">
                                                    <input id="detaysterlin" class="hide">
                                                </div>
                                                <div class="col-sm-6 col-xs-12">
                                                    <label class="control-label col-xs-6">İndirim:</label>
                                                    <label class="col-xs-6 indirim" style="padding-top: 7px"></label>
                                                    <label class="control-label col-xs-6">İndirim Oranı:</label>
                                                    <label class="col-xs-6 indirimorani" style="padding-top: 7px"></label>
                                                </div>
                                                <label class="control-label col-xs-12 warning" style="text-align:left"></label>
                                            </div>
                                            <div class="col-sm-5 col-xs-12">
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TUTAR:</label>
                                                    <label class="col-xs-6 fiyattutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">İNDİRİM MİKTARI:</label>
                                                    <label class="col-xs-6 indirimtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDVSIZ TUTAR:</label>
                                                    <label class="col-xs-6 kdvsiztutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDV TUTARI:</label>
                                                    <label class="col-xs-6 kdvtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TOPLAM TUTAR:</label>
                                                    <label class="col-xs-6 toplamtutar" style="padding-top: 9px">0.00</label>
                                                </div>
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
    <div class="modal fade" id="onayconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Fiyatlandırma Onaylanacak</h4>
                </div>
                <div class="modal-body">
                    Fiyatlandırmaya Ait Sayaçlar Onaylanacaktır?
                </div>
                <div class="modal-footer">
                    <a id="formonay" href="#" type="button" data-dismiss="modal" class="btn green">Onayla</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="redconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Fiyatlandırma Reddedilecek</h4>
                </div>
                <div class="modal-body">
                    Sayaçlara ait Fiyatlandırma Reddedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formred" href="#" type="button" data-dismiss="modal" class="btn green">Reddet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
@stop
