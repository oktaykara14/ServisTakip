@extends('layout.master')

@section('page-title')
<!--suppress JSValidateTypes -->
<div class="page-title">
    <h1>Onaylanan <small> Sayaçlar Ekranı</small></h1>
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
                "url": "{{ URL::to('ucretlendirme/onaylananlist') }}",
                "type": "POST",
                "data": {
                    "hatirlatma_id" : "@if(isset($hatirlatma_id)){{$hatirlatma_id}}@endif" ,
                    "netsiscari_id" : "@if(isset(Auth::user()->netsiscari_id) && count(Auth::user()->netsiscari_id)>0){{Auth::user()->netsiscarilist}}@endif"
                }
            },
            "bServerSide": true,
            "aaSorting": [[6,'desc']],
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
                {data: 'id', name: 'onaylanan.id',"class":"id","orderable": true, "searchable": true},
                {data: 'cariadi', name: 'netsiscari.cariadi',"orderable": true, "searchable": false},
                {data: 'yeradi', name: 'uretimyer.yeradi',"orderable": true, "searchable": false},
                {data: 'sayacsayisi', name: 'ucretlendirilen.sayacsayisi',"orderable": true, "searchable": true},
                {data: 'adi_soyadi', name: 'kullanici.adi_soyadi',"orderable": true, "searchable": false},
                {data: 'gonaylamatipi', name: 'onaylanan.gonaylamatipi',"orderable": true, "searchable": false},
                {data: 'onaytarihi', name: 'onaylanan.onaytarihi',"orderable": true, "searchable": false},
                {data: 'gonaytarihi', name: 'onaylanan.gonaytarihi',"visible": false, "searchable": true},
                {data: 'ncariadi', name: 'netsiscari.ncariadi',"visible": false, "searchable": true},
                {data: 'nyeradi', name: 'uretimyer.nyeradi',"visible": false, "searchable": true},
                {data: 'nadi_soyadi', name: 'kullanici.nadi_soyadi',"visible": false, "searchable": true},
                {data: 'nonaylamatipi', name: 'onaylanan.nonaylamatipi',"visible": false, "searchable": true},
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
            '<option value="8">Cari Adı</option>'+
            '<option value="9">Üretim Yeri</option>'+
            '<option value="3">Adet</option>'+
            '<option value="10">Onaylayan</option>'+
            '<option value="11">Onaylama Tipi</option>'+
            '<option value="7">Onay Tarihi</option>'+
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
        var tableWrapper = jQuery('#sample_editable_3_wrapper');
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        $(document).on("click", ".detay", function () {
            $.blockUI();
            var id = $(this).data('id');
            $.getJSON("{{ URL::to('ucretlendirme/onaylananbilgi') }}",{id:id},function(event){
                if(event.durum){
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
                    var dovizkurutarih="";
                    $.each(dovizkuru,function(index){
                        dovizkurutarih=dovizkuru[index].tarih;
                        if(dovizkuru[index].parabirimi_id==="2")
                            euro = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                        else if(dovizkuru[index].parabirimi_id==="3")
                            dolar = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                        else
                            sterlin = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                    });
                    $('.onizlemeeuro').html('Euro : '+euro+' ₺');
                    $('.onizlemedolar').html('Dolar : '+dolar+' ₺');
                    $('.onizlemesterlin').html('Sterlin : '+sterlin+' ₺');
                    $('#onizlemedolar').val(dolar);
                    $('#onizlemeeuro').val(euro);
                    $('#onizlemesterlin').val(sterlin);
                    var parabirimi = ucretlendirilen.parabirimi;
                    var parabirimi2 = ucretlendirilen.parabirimi2;
                    $('#onizlemebirim').val(parabirimi.id);
                    $('#onizlemebirim2').val(parabirimi2==null ? "" : parabirimi2.id);
                    $('#onizlemebirimi').val(parabirimi.birimi);
                    $('#onizlemebirimi2').val(parabirimi2==null ? "" : parabirimi2.birimi);
                    $('#onizlemekurtarih').val(dovizkurutarih);
                    var arizafiyat=ucretlendirilen.arizafiyat;
                    var kurdurum=false;
                    var birimdurum = false;
                    $.each(arizafiyat,function(index) {
                        var garanti = arizafiyat[index].ariza_garanti==="0" ? 'Dışında' : 'İçinde';
                        var fiyatdurum = arizafiyat[index].fiyatdurum==="0" ? 'Genel' : 'Özel';
                        var sayacadi = arizafiyat[index].sayacadi.sayacadi;
                        var serino = arizafiyat[index].ariza_serino;
                        var birim = arizafiyat[index].parabirimi;
                        var birim2 = arizafiyat[index].parabirimi2;
                        var fiyat = arizafiyat[index].durum==="4" ? 0 : parseFloat(arizafiyat[index].fiyat);
                        var fiyat2 = arizafiyat[index].durum==="4" ? 0 : parseFloat(arizafiyat[index].fiyat2);
                        var indirimorani = parseFloat(arizafiyat[index].indirimorani);
                        var indirim =(fiyat*indirimorani)/100;
                        var indirim2 =(fiyat2*indirimorani)/100;
                        var kdvsiztutar = arizafiyat[index].durum==="4" ? 0 : parseFloat(arizafiyat[index].tutar);
                        var kdvsiztutar2 = arizafiyat[index].durum==="4" ? 0 : parseFloat(arizafiyat[index].tutar2);
                        var kdv = arizafiyat[index].durum==="4" ? 0 : parseFloat(arizafiyat[index].kdv);
                        var kdv2 = arizafiyat[index].durum==="4" ? 0 : parseFloat(arizafiyat[index].kdv2);
                        var toplamtutar = arizafiyat[index].durum==="4" ? 0 : parseFloat(arizafiyat[index].toplamtutar);
                        var toplamtutar2 = arizafiyat[index].durum==="4" ? 0 : parseFloat(arizafiyat[index].toplamtutar2);
                        if(toplamtutar2>0){
                            oTable2.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi+' + '+fiyat2.toFixed(2)+' '+birim2.birimi,
                                indirimorani.toFixed(2)+'%',kdvsiztutar.toFixed(2)+' '+birim.birimi+' + '+kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv.toFixed(2)+' '+birim.birimi+' + '+kdv2.toFixed(2)+' '+birim2.birimi,
                                toplamtutar.toFixed(2)+' '+birim.birimi+' + '+toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>']).draw();
                        }else{
                            oTable2.row.add([arizafiyat[index].id,serino,sayacadi ,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi,indirimorani.toFixed(2)+'%',
                                kdvsiztutar.toFixed(2)+' '+birim.birimi,kdv.toFixed(2)+' '+birim.birimi,toplamtutar.toFixed(2)+' '+birim.birimi,
                                '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>'])
                                .draw();
                        }
                        if(birim.id===parabirimi.id){
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            if(birim2!=null){
                                if(birim2.id===parabirimi2.id){
                                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                                }else{
                                    toplamtutar2=0;
                                    $('.onizlemewarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                }
                            }else{
                                toplamtutar2=0;
                            }
                        }else{
                            var kur = 1;
                            kurdurum=true;
                            if(birim.id!=="1" && parabirimi.id==="1")
                                birimdurum=true;
                            if(parabirimi.id==="1") // tl ise
                            {
                                if(birim.id==="2") //euro ise
                                    kur = euro;
                                else if(birim.id==="3") //dolar ise
                                    kur = dolar;
                                else
                                    kur = sterlin;
                            }else if(parabirimi.id==="2"){ //euro ise
                                if(birim.id==="1") //tl ise
                                    kur = 1/euro;
                                else if(birim.id==="3") //dolar ise
                                    kur = dolar/euro;
                                else
                                    kur = sterlin/euro;
                            }else if(parabirimi.id==="3"){ //dolar ise
                                if(birim.id==="1") //tl ise
                                    kur = 1/dolar;
                                else if(birim.id==="2") //euro ise
                                    kur = euro/dolar;
                                else
                                    kur = sterlin/dolar;
                            }else{ //sterlin ise
                                if(birim.id==="1") //euro ise
                                    kur = 1/sterlin;
                                else if(birim.id==="2") //dolar ise
                                    kur = euro/sterlin;
                                else
                                    kur = dolar/sterlin;
                            }
                            fiyat*=kur;
                            if(birim2!=null){
                                if(birim2.id===parabirimi.id){
                                    fiyat+=fiyat2;
                                    fiyat2=0;
                                }else if(birim2.id!==parabirimi2.id){
                                    fiyat2=0;
                                    $('.onizlemewarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                }
                            }else{
                                fiyat2=0;
                            }
                            indirim = ((fiyat*indirimorani)/100);
                            indirim2 = ((fiyat2*indirimorani)/100);
                            kdvsiztutar = (fiyat-indirim);
                            kdvsiztutar2 = (fiyat2-indirim2);
                            kdv=(kdvsiztutar*18)/100;
                            kdv2=(kdvsiztutar2*18)/100;
                            toplamtutar=Math.round((kdvsiztutar+kdv)*100)/100;
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            toplamtutar2=Math.round((kdvsiztutar2+kdv2)*100)/100;
                            toplamtutar2=Math.round(toplamtutar2*2)/2;
                        }
                        genelfiyat+=fiyat;
                        genelindirim+=indirim;
                        genelkdvsiztutar+=kdvsiztutar;
                        genelkdvtutar+=kdv;
                        geneltoplamtutar+=toplamtutar;
                        genelfiyat2+=fiyat2;
                        genelindirim2+=indirim2;
                        genelkdvsiztutar2+=kdvsiztutar2;
                        genelkdvtutar2+=kdv2;
                        geneltoplamtutar2+=toplamtutar2;
                    });
                    if(birimdurum) {
                        $('.onizlemewarning').html('<span style="color:red">Kur Tarihi: ' + dovizkurutarih + '</span>');
                    }else {
                        $('.onizlemewarning').html('<span style="color:red">Kur Tarihi: ' + dovizkurutarih + '. Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
                    }
                    if(kurdurum)
                        $('.onizlemekur').removeClass('hide');
                    else
                        $('.onizlemekur').addClass('hide');
                    if(geneltoplamtutar2===0){
                        $('.onizlemetutar').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi);
                        $('.onizlemeindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi);
                        $('.onizlemekdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.onizlemekdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.onizlemetoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi);
                    }else{
                        $('.onizlemetutar').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi+' + '+genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                        $('.onizlemeindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi+' + '+genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                        $('.onizlemekdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                        $('.onizlemekdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        $('.onizlemetoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
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
                }else{
                    $('#detay-goster').modal('hide');
                    toastr[event.type](event.text,event.title);
                }
                $.unblockUI();
            });
        });
        $(document).on("click", ".fiyatdetay", function () {
            $.blockUI();
            var id = $(this).data('id');
            $.getJSON("{{ URL::to('ucretlendirme/kayitdetay') }}",{id:id},function(event){
                if(event.durum){
                    var ucretlendirme = event.ucretlendirme;
                    $(".fiyatdurum").html(ucretlendirme.fiyatdurum==="0" ? 'Genel' : 'Özel');
                    $(".serino").html(ucretlendirme.ariza_serino);
                    $(".garanti").html(ucretlendirme.ariza_garanti==="0" ? 'Dışında' : 'İçinde');
                    if(ucretlendirme.sayac.sayaccap_id==="1")
                        $(".sayacadi").html(ucretlendirme.sayacadi.sayacadi);
                    else
                        $(".sayacadi").html(ucretlendirme.sayacadi.sayacadi+" - "+ucretlendirme.sayaccap.capadi );
                    oTable3.clear().draw();
                    var indirimdurum=ucretlendirme.indirim;
                    var degisenler=ucretlendirme.parcalar;
                    var genelfiyat=ucretlendirme.genelfiyatlar;
                    var ozelfiyat=ucretlendirme.ozelfiyatlar;
                    var genelbirimler=ucretlendirme.genelbirimler;
                    var ozelbirimler=ucretlendirme.ozelbirimler;
                    var genelbirim=JSON.stringify(ucretlendirme.genelbirimler);
                    var ozelbirim=JSON.stringify(ucretlendirme.ozelbirimler);
                    var ucretsizler=ucretlendirme.ucretsizler;
                    var genelbirimi=ucretlendirme.genelparabirimi.birimi;
                    var ozelbirimi=ucretlendirme.ozelparabirimi.birimi;
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
                    if(ucretlendirme.fiyatdurum==="0") //genel fiyatlar gözükecek
                    {
                        if (ucretlendirme.ariza_garanti === "1") {
                            oTable3.columns([4]).visible(true, false);
                            oTable3.columns([2, 3, 5, 6]).visible(false, false);
                        }else {
                            oTable3.columns([2, 6]).visible(true, false);
                            oTable3.columns([3, 4, 5]).visible(false, false);
                        }
                        $.each(degisenler,function(index) {
                            if (ucretsizler[index] === "1") {
                                oTable3.row.add([degisenler[index].id, degisenler[index].tanim, '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,
                                    '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,'Evet']).draw();
                            }else{
                                oTable3.row.add([degisenler[index].id, degisenler[index].tanim, genelfiyat[index] + ' ' + genelbirimler[index].birimi,ozelfiyat[index] + ' ' + ozelbirimler[index].birimi,
                                    '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,'Hayır']).draw();
                            }
                        });
                    }else {
                        if(ucretlendirme.ariza_garanti==="1") {
                            oTable3.columns([ 5 ]).visible(true, false);
                            oTable3.columns([2, 3, 4, 6]).visible(false, false);
                        }else{
                            oTable3.columns( [ 3,6 ] ).visible(true, false);
                            oTable3.columns( [ 2,4,5 ] ).visible( false,false );
                        }
                        $.each(degisenler, function (index) {
                            if (ucretsizler[index] === "1") {
                                oTable3.row.add([degisenler[index].id, degisenler[index].tanim, '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,
                                    '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,'Evet']).draw();
                            }else{
                                oTable3.row.add([degisenler[index].id, degisenler[index].tanim, genelfiyat[index] + ' ' + genelbirimler[index].birimi,ozelfiyat[index] + ' ' + ozelbirimler[index].birimi,
                                    '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,'Hayır']).draw();
                            }
                        });
                    }
                    var dolar = 0;
                    var euro = 0;
                    var sterlin = 0;
                    var dovizkuru = event.dovizkuru;
                    var dovizkurutarih="";
                    $.each(dovizkuru,function(index){
                        dovizkurutarih=dovizkuru[index].tarih;
                        if(dovizkuru[index].parabirimi_id==="2")
                            euro = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                        else if(dovizkuru[index].parabirimi_id==="3")
                            dolar = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                        else
                            sterlin = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                    });
                    if(ozelbirimid!=="1")
                        $('.warning').html('<span style="color:red">Kur Tarihi: '+dovizkurutarih+'. Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
                    else
                        $('.warning').html('<span style="color:red">Kur Tarihi: '+dovizkurutarih+'</span>');
                    $('.detayeuro').html('Euro : '+euro+' ₺');
                    $('.detaydolar').html('Dolar : '+dolar+' ₺');
                    $('.detaysterlin').html('Sterlin : '+sterlin+' ₺');
                    $('#detaydolar').val(dolar);
                    $('#detayeuro').val(euro);
                    $('#detaysterlin').val(sterlin);
                    $('#detaybirim').val(ozelbirimid);
                    $('#detaykurtarih').val(dovizkurutarih);
                    var kur = 1;
                    var kurdurum=false;
                    if(ozelbirimid!==genelbirimid) //parabirimi farklı kur ile çarpılacak
                    {
                        kurdurum=true;
                        if(ozelbirimid==="1") // tl ise
                        {
                            if(genelbirimid==="2") //euro ise
                                kur = euro;
                            else if(genelbirimid==="3") //dolar ise
                                kur = dolar;
                            else
                                kur = sterlin;
                        }else if(ozelbirimid==="2"){ //euro ise
                            if(genelbirimid==="1") //tl ise
                                kur = 1/euro;
                            else if(genelbirimid==="3") //dolar ise
                                kur = dolar/euro;
                            else
                                kur = sterlin/euro;
                        }else if(ozelbirimid==="3"){ //dolar ise
                            if(genelbirimid==="1") //tl ise
                                kur = 1/dolar;
                            else if(genelbirimid==="2") //euro ise
                                kur = euro/dolar;
                            else
                                kur = sterlin/dolar;
                        }else{ //sterlin ise
                            if(genelbirimid==="1") //euro ise
                                kur = 1/sterlin;
                            else if(genelbirimid==="2") //dolar ise
                                kur = euro/sterlin;
                            else
                                kur = dolar/sterlin;
                        }
                    }
                    var fiyat=0;
                    var indirim=0;
                    var kdvsiztutar=0;
                    var kdv=0;
                    var toplamtutar=0;
                    var fiyat2=0;
                    var indirim2=0;
                    var kdvsiztutar2=0;
                    var kdv2=0;
                    var toplamtutar2=0;
                    if(ucretlendirme.fiyatdurum==="0") //genel fiyatlar gözükecek
                    {
                        var parabirimi=genelbirimid;
                        var parabirimi2="";
                        var genel=ucretlendirme.genel;
                        genel=genel.split(';');
                        $.each(genel,function(index){
                            if(ucretsizler[index]!=='1'){
                                if(parabirimi===genelbirimler[index].id){
                                    fiyat+=parseFloat(genel[index]);
                                }else if(parabirimi2==="" || parabirimi2.id===genelbirimler[index].id){
                                    fiyat2+=parseFloat(genel[index]);
                                    parabirimi2 = genelbirimler[index];
                                }else{
                                    $('.warning').html('<span style="color:red">İki Para Biriminden Fazla Para Birimi Kullanılamaz!</span>');
                                }
                            }
                        });
                        if(ucretlendirme.ariza_garanti==="1") {
                            fiyat=0;
                            fiyat2=0;
                        }else{
                            fiyat*=kur;
                            if(parabirimi2.id===ozelbirimid){
                                fiyat+=fiyat2;
                                fiyat2=0;
                                parabirimi2="";
                            }
                        }
                        if(indirimdurum === '1') //indirim varsa
                        {
                            $('.indirim').text('Var');
                            $('.indirimorani').text('%'+parseFloat(ucretlendirme.indirimorani).toFixed(2));
                            indirim = ((fiyat*parseFloat(ucretlendirme.indirimorani))/100);
                            indirim2 = ((fiyat2*parseFloat(ucretlendirme.indirimorani))/100);
                            kdvsiztutar = (fiyat-indirim);
                            kdvsiztutar2 = (fiyat2-indirim2);
                        }else{
                            $('.indirim').text('Yok');
                            $('.indirimorani').text('%'+parseFloat(ucretlendirme.indirimorani).toFixed(2));
                            kdvsiztutar = fiyat;
                            kdvsiztutar2 = fiyat2;
                        }
                        kdv=(kdvsiztutar*18)/100;
                        kdv2=(kdvsiztutar2*18)/100;
                        toplamtutar=Math.round((kdvsiztutar+kdv)*100)/100;
                        toplamtutar=Math.round(toplamtutar*2)/2;
                        toplamtutar2=Math.round((kdvsiztutar2+kdv2)*100)/100;
                        toplamtutar2=Math.round(toplamtutar2*2)/2;
                        if(ucretlendirme.ariza_garanti==="1"){
                            $('.fiyattutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                            $('.indirimtutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                            $('.kdvsiztutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                            $('.kdvtutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                            $('.toplamtutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                        }else if(ucretlendirme.durum==="4"){
                            $('.fiyattutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                            $('.indirimtutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                            $('.kdvsiztutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                            $('.kdvtutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                            $('.toplamtutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                        }else{
                            if(toplamtutar2===0){
                                $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi);
                                $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi);
                                $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi);
                                $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi);
                                $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi);
                            }else{
                                $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi+' + '+fiyat2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi+' + '+indirim2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi+' + '+kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi+' + '+kdv2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi+' + '+toplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            }
                        }
                    }else{ //ozel fiyatlar
                        parabirimi=ozelbirimid;
                        parabirimi2="";
                        var ozel=ucretlendirme.ozel;
                        ozel=ozel.split(';');
                        $.each(ozel,function(index){
                            if(ucretsizler[index]!=='1'){
                                if(parabirimi===ozelbirimler[index].id){
                                    fiyat+=parseFloat(ozel[index]);
                                }else if(parabirimi2==="" || parabirimi2.id===ozelbirimler[index].id){
                                    fiyat2+=parseFloat(ozel[index]);
                                    parabirimi2 = ozelbirimler[index];
                                }else{
                                    $('.warning').html('<span style="color:red">İki Para Biriminden Fazla Para Birimi Kullanılamaz!</span>');
                                }
                            }
                        });
                        if(ucretlendirme.ariza_garanti==="1") {
                            fiyat=0;
                            fiyat2=0;
                        }
                        if(parabirimi2.id===ozelbirimid){
                            fiyat+=fiyat2;
                            fiyat2=0;
                            parabirimi2="";
                        }
                        if(indirimdurum === '1') //indirim varsa
                        {
                            $('.indirim').text('Var');
                            $('.indirimorani').text('%'+parseFloat(ucretlendirme.indirimorani).toFixed(2));
                            indirim = ((fiyat*parseFloat(ucretlendirme.indirimorani))/100);
                            indirim2 = ((fiyat2*parseFloat(ucretlendirme.indirimorani))/100);
                            kdvsiztutar = (fiyat-indirim);
                            kdvsiztutar2 = (fiyat2-indirim2);
                        }else{
                            $('.indirim').text('Yok');
                            $('.indirimorani').text('%'+parseFloat(ucretlendirme.indirimorani).toFixed(2));
                            kdvsiztutar = fiyat;
                            kdvsiztutar2 = fiyat2;
                        }
                        kdv=(kdvsiztutar*18)/100;
                        kdv2=(kdvsiztutar2*18)/100;
                        toplamtutar=Math.round((kdvsiztutar+kdv)*100)/100;
                        toplamtutar=Math.round(toplamtutar*2)/2;
                        toplamtutar2=Math.round((kdvsiztutar2+kdv2)*100)/100;
                        toplamtutar2=Math.round(toplamtutar2*2)/2;
                        if(ucretlendirme.ariza_garanti==="1"){
                            $('.fiyattutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                            $('.indirimtutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                            $('.kdvsiztutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                            $('.kdvtutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                            $('.toplamtutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                        }else if(ucretlendirme.durum==="4"){
                            $('.fiyattutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                            $('.indirimtutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                            $('.kdvsiztutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                            $('.kdvtutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                            $('.toplamtutar').text((0.00).toFixed(2)+' '+ozelbirimi);
                        }else {
                            if(toplamtutar2===0){
                                $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi);
                                $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi);
                                $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi);
                                $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi);
                                $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi);
                            }else{
                                $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi+' + '+fiyat2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi+' + '+indirim2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi+' + '+kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi+' + '+kdv2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi+' + '+toplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            }
                        }
                    }
                    if(genelbirimid!=="1")
                        kurdurum=true;
                    if(kurdurum)
                        $('.kur').removeClass('hide');
                    else
                        $('.kur').addClass('hide');
                }else{
                    toastr[event.type](event.text, event.type);
                    $('#fiyat-detay').modal('hide');
                }
                $.unblockUI();
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
                        <i class="fa fa-check"></i>Onaylanan Sayaçlar
                    </div>
                    <div class="actions">
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Cari Adı</th>
                            <th>Üretim Yeri</th>
                            <th>Adet</th>
                            <th>Onaylayan</th>
                            <th>Onaylama Tipi</th>
                            <th>Onay Tarihi</th>
                            <th></th><th></th><th></th>
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
    <div class="modal fade" id="detay-goster" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-check"></i>Onaylanan Sayaçların Detayı
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Onaylanan Sayaçların Detayı</h3>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Yeri:</label>
                                            <label class="col-xs-8 yer" style="padding-top: 7px"></label>
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
                                                    <th>İşlemler</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="col-xs-12 onizlemekur">
                                                    <label class="col-xs-12 onizlemeeuro" style="padding-top: 9px;margin-left:5px;text-align:center">Euro : 0.0000 ₺</label>
                                                    <label class="col-xs-12 onizlemedolar" style="padding-top: 9px;margin-left:3px;text-align:center">Dolar : 0.0000 ₺</label>
                                                    <label class="col-xs-12 onizlemesterlin" style="padding-top: 9px;text-align:center">Sterlin : 0.0000 ₺</label>
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
                                                <input class="hide" id="onizlemetutar2" name="onizlemetutar2"/>
                                                <input class="hide" id="onizlemeindirimtutar" name="onizlemeindirimtutar"/>
                                                <input class="hide" id="onizlemeindirimtutar2" name="onizlemeindirimtutar2"/>
                                                <input class="hide" id="onizlemekdvsiztutar" name="onizlemekdvsiztutar"/>
                                                <input class="hide" id="onizlemekdvsiztutar2" name="onizlemekdvsiztutar2"/>
                                                <input class="hide" id="onizlemekdvtutar" name="onizlemekdvtutar"/>
                                                <input class="hide" id="onizlemekdvtutar2" name="onizlemekdvtutar2"/>
                                                <input class="hide" id="onizlemetoplamtutar" name="onizlemetoplamtutar"/>
                                                <input class="hide" id="onizlemetoplamtutar2" name="onizlemetoplamtutar2"/>
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
                                <form action="" id="form_sample_3" class="form-horizontal" novalidate="novalidate">
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
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_3">
                                                <thead>
                                                <tr>
                                                    <th class="">#</th>
                                                    <th>Değişen Parça</th>
                                                    <th class="genelfiyat">Fiyatı</th>
                                                    <th class="ozelfiyat">Fiyatı</th>
                                                    <th class="genelgaranti">Fiyatı</th>
                                                    <th class="ozelgaranti">Fiyatı</th>
                                                    <th class="ucretsiz">Ücretsiz</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-7 col-xs-12 ">
                                                <div class="col-sm-6 col-xs-12 kur">
                                                    <label class="col-xs-12 detayeuro" style="padding-top: 9px;margin-left:5px;text-align:center">Euro : 0.0000 ₺</label>
                                                    <label class="col-xs-12 detaydolar" style="padding-top: 9px;margin-left:3px;text-align:center">Dolar : 0.0000 ₺</label>
                                                    <label class="col-xs-12 detaysterlin" style="padding-top: 9px;text-align:center">Sterlin : 0.0000 ₺</label>
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
@stop
