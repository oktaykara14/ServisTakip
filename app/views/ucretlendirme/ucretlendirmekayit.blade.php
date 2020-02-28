@extends('layout.master')

@section('page-title')
<!--suppress JSValidateTypes -->
<div class="page-title">
    <h1>Ücretlendirme <small>Bekleyenler Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
    <link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::to('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('page-styles')
@stop

@section('page-js')
    <script src="{{ URL::to('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/datatables/datatables.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/fuelux/js/spinner.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-maskmoney/jquery.maskMoney.min.js') }}" type="text/javascript" ></script>

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
                "url": "{{ URL::to('ucretlendirme/ucretlendirmekayitlist') }}",
                "type": "POST",
                "data": {
                    "hatirlatma_id" : "@if(isset($hatirlatma_id)){{$hatirlatma_id}}@endif" ,
                    "netsiscari_id" : "@if(isset(Auth::user()->netsiscari_id) && count(Auth::user()->netsiscari_id)>0){{Auth::user()->netsiscarilist}}@endif"
                }
            },
            "bServerSide": true,
            "fnDrawCallback" : function() {
                var secilenler=$('#secilenler').val();
                var secilenlist=secilenler.split(',');
                $.each(secilenlist,function(index) {
                    $("#sample_editable_1  tr .id").each(function(){
                        if(secilenlist[index]===$(this).html()){
                            $(this).parents('tr').addClass("active");
                        }
                    });
                });
            },
            "aaSorting": [[0,'desc']],
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
                {data: 'id', name: 'arizafiyat.id',"class":"id","orderable": true, "searchable": true},
                {data: 'ariza_serino', name: 'arizafiyat.ariza_serino',"orderable": true, "searchable": true},
                {data: 'cariadi', name: 'netsiscari.cariadi',"orderable": true, "searchable": false},
                {data: 'yeradi', name: 'uretimyer.yeradi',"orderable": true, "searchable": false},
                {data: 'sayacadi', name: 'sayacadi.sayacadi',"orderable": true, "searchable": false},
                {data: 'ggaranti', name: 'arizafiyat.ggaranti',"orderable": true, "searchable": false},
                {data: 'toplamtutar', name: 'arizafiyat.toplamtutar',"class":"tutar","orderable": true, "searchable": true},
                {data: 'toplamtutar2', name: 'arizafiyat.toplamtutar2',"class":"tutar2","visible": false, "searchable": true},
                {data: 'depotarihi', name: 'sayacgelen.depotarihi',"orderable": true, "searchable": false},
                {data: 'gdepotarihi', name: 'sayacgelen.gdepotarihi',"visible": false, "searchable": true},
                {data: 'ncariadi', name: 'netsiscari.ncariadi',"visible": false, "searchable": true},
                {data: 'nyeradi', name: 'uretimyer.nyeradi',"visible": false, "searchable": true},
                {data: 'nsayacadi', name: 'sayacadi.nsayacadi',"visible": false, "searchable": true},
                {data: 'ngaranti', name: 'arizafiyat.ngaranti',"visible": false, "searchable": true},
                {data: 'islemler', name: 'islemler',"orderable": false, "searchable": false},
                {data: 'subedurum', name: 'arizafiyat.subedurum',"class":"sube hide","orderable": false, "searchable": false},
                {data: 'durum', name: 'arizafiyat.durum',"class":"durum hide","orderable": false, "searchable": false}
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
            '<option value="10">Cari Adı</option>'+
            '<option value="11">Üretim Yeri</option>'+
            '<option value="12">Sayaç Adı</option>'+
            '<option value="13">Garanti</option>'+
            '<option value="6">Tutar</option>'+
            '<option value="9">Geliş Tarihi</option>'+
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
                var adet=parseInt($('.secilenadet').html());
                var secilenler=$('#secilenler').val();
                var subesecilenler=$('#subesecilenler').val();
                var subedurum= $('.subegonder').hasClass('hide') ? "0" : "1";
                if($(this).hasClass('active'))
                {
                    subedurum = subedurum==="1" ? subedurum : oTable.cell( $(this).children('.sube')).data();
                    subesecilenler+=(subesecilenler==="" ? "" : ",")+oTable.cell( $(this).children('.sube')).data();
                    secilenler+=(secilenler==="" ? "" : ",")+oTable.cell( $(this).children('.id')).data();
                    adet++;
                    $('#secilenler').val(secilenler);
                    $('#subesecilenler').val(subesecilenler);
                    $('.secilenadet').html(adet);
                    //$(this).parents('tr').addClass("active");
                }else{
                    subedurum = 0;
                    var secilen=oTable.cell( $(this).children('.id')).data();
                    var secilenlist=secilenler.split(',');
                    var subesecilenlist=subesecilenler.split(',');
                    var yenilist="",subeyenilist="";
                    $.each(secilenlist,function(index){
                        if(secilenlist[index]!==secilen)
                        {
                            yenilist+=(yenilist==="" ? "" : ",")+secilenlist[index];
                            subeyenilist+=(subeyenilist==="" ? "" : ",")+subesecilenlist[index];
                            subedurum = subedurum==="1" ? subedurum : subesecilenlist[index];
                        }
                    });
                    adet--;
                    $('#secilenler').val(yenilist);
                    $('#subesecilenler').val(subeyenilist);
                    $('.secilenadet').html(adet);
                    //$(this).parents('tr').removeClass("active");
                }
                if(subedurum==="1"){
                    @if(Auth::user()->grup_id<16 && Auth::user()->grup_id!=6)
                        $('.subegonder').removeClass('hide');
                    @else
                        $('.subegonder').addClass('hide');
                    @endif
                }else{
                    $('.subegonder').addClass('hide');
                }
                if(adet>0)
                    $('.getir').removeClass('hide');
                else
                    $('.getir').addClass('hide');
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
            "bAutoWidth": false,
            "fnDrawCallback" : function() {
                var id,garanti,fiyat,indirim,kdvsiztutar,kdv,toplamtutar,fiyat2,indirim2,kdvsiztutar2,kdv2,toplamtutar2,indirimdurum,indirimorani,ucretsizler,genelbirimid,ozelbirimid,
                    dolar,euro,sterlin,genelbirimi,ozelbirimi,kur,birim,index,parca,ucret,parabirimi,parabirimi2,fiyatindex,fiyatbirim,fiyatbirimid,ozel,yeni,genelbirimler,ozelbirimler;

                $('#fiyatdurum').on('change', function () {
                    id = $(this).val();
                    garanti = $('#garanti').val();
                    fiyat=0;
                    indirim=0;
                    kdvsiztutar=0;
                    kdv=0;
                    toplamtutar=0;
                    fiyat2=0;
                    indirim2=0;
                    kdvsiztutar2=0;
                    kdv2=0;
                    toplamtutar2=0;
                    indirimorani=0.00;
                    ucretsizler=$('#ucretsiz').val();
                    ucretsizler=ucretsizler.split(',');
                    dolar = $('#detaydolar').val();
                    euro = $('#detayeuro').val();
                    sterlin = $('#detaysterlin').val();
                    genelbirimi = $('#genelbirim').val();
                    ozelbirimi= $('#ozelbirim').val();
                    genelbirimid = $('#genelbirimid').val();
                    ozelbirimid= $('#ozelbirimid').val();
                    genelbirimler=JSON.parse($('#genelbirimler').val());
                    ozelbirimler=JSON.parse($('#ozelbirimler').val());
                    kur = 1;
                    if(ozelbirimi!==genelbirimi) //parabirimi farklı kur ile çarpılacak
                    {
                        if(ozelbirimi==='₺') // tl ise
                        {
                            if(genelbirimi==='€') //euro ise
                                kur = euro;
                            else if(genelbirimi==='$') //dolar ise
                                kur = dolar;
                            else
                                kur = sterlin;
                        }else if(ozelbirimi==='€'){ //euro ise
                            if(genelbirimi==='₺') //tl ise
                                kur = 1/euro;
                            else if(genelbirimi==='$') //dolar ise
                                kur = dolar/euro;
                            else
                                kur = sterlin/euro;
                        }else if(ozelbirimi==='$'){ //dolar ise
                            if(genelbirimi==='₺') //tl ise
                                kur = 1/dolar;
                            else if(genelbirimi==='€') //euro ise
                                kur = euro/dolar;
                            else
                                kur = sterlin/dolar;
                        }else{ //sterlin ise
                            if(genelbirimi==='€') //euro ise
                                kur = 1/sterlin;
                            else if(genelbirimi==='$') //dolar ise
                                kur = euro/sterlin;
                            else
                                kur = dolar/sterlin;
                        }
                    }
                    if(id==="0") //genel fiyatlar gözükecek
                    {
                        parabirimi=genelbirimid;
                        parabirimi2="";
                        if(garanti==="0") //garanti dışı
                        {
                            oTable2.columns( [ 2,6 ] ).visible( true,false );
                            oTable2.columns( [ 3,4,5,7 ] ).visible( false,false );
                            var genel=$('#genel').val();
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
                            fiyat*=kur;
                            if(parabirimi2.id===ozelbirimid){
                                fiyat+=fiyat2;
                                fiyat2=0;
                                parabirimi2="";
                            }
                            indirimdurum = $('#indirim').bootstrapSwitch('state');
                            if(indirimdurum === true) //indirim varsa
                            {
                                indirimorani= $('#indirimorani').maskMoney('unmasked')[0];
                                indirim = ((fiyat*indirimorani)/100);
                                indirim2 = ((fiyat2*indirimorani)/100);
                                kdvsiztutar = (fiyat-indirim);
                                kdvsiztutar2 = (fiyat2-indirim2);
                            }else{
                                kdvsiztutar=fiyat;
                                kdvsiztutar2 = fiyat2;
                            }
                            kdv=(kdvsiztutar*18)/100;
                            kdv2=(kdvsiztutar2*18)/100;
                            toplamtutar=Math.round((kdvsiztutar+kdv)*100)/100;
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            toplamtutar2=Math.round((kdvsiztutar2+kdv2)*100)/100;
                            toplamtutar2=Math.round(toplamtutar2*2)/2;
                            $('#detaybirim2').val(parabirimi2==="" ? "" : parabirimi2.id);
                        }else{ //garanti içi
                            oTable2.columns( [ 4 ] ).visible( true,false );
                            oTable2.columns( [ 2,3,5,6,7 ] ).visible( false,false );
                        }
                        if(toplamtutar2 === 0){
                            $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi);
                            $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi);
                            $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi);
                            $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi);
                            $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi);
                            $('.sontoplam2').addClass('hide');
                        }else{
                            if(toplamtutar === 0){
                                $('.fiyattutar').text(fiyat2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.indirimtutar').text(indirim2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvsiztutar').text(kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvtutar').text(kdv2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.toplamtutar').text(toplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            }else{
                                $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi+' + '+fiyat2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi+' + '+indirim2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi+' + '+kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi+' + '+kdv2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi+' + '+toplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            }
                            $('.sontoplam2').removeClass('hide');
                        }
                        $('#sontoplam').maskMoney({suffix: " "+ozelbirimi});
                        $('#sontoplam').maskMoney('mask',toplamtutar*100);
                        $('#sontoplam2').maskMoney({suffix: " "+parabirimi2.birimi});
                        $('#sontoplam2').maskMoney('mask',toplamtutar2*100);
                        $('#fiyattutar').val(fiyat.toFixed(2));
                        $('#fiyattutar2').val(fiyat2.toFixed(2));
                        $('#indirimtutar').val(indirim.toFixed(2));
                        $('#indirimtutar2').val(indirim2.toFixed(2));
                        $('#kdvsiztutar').val(kdvsiztutar.toFixed(2));
                        $('#kdvsiztutar2').val(kdvsiztutar2.toFixed(2));
                        $('#kdvtutar').val(kdv.toFixed(2));
                        $('#kdvtutar2').val(kdv2.toFixed(2));
                        $('#toplamtutar').val(toplamtutar.toFixed(2));
                        $('#toplamtutar2').val(toplamtutar2.toFixed(2));
                    }else{ //ozel fiyatlar
                        parabirimi=ozelbirimid;
                        parabirimi2="";
                        if(garanti==="0") //garanti dışı
                        {
                            oTable2.columns( [ 3,6,7 ] ).visible( true,false );
                            oTable2.columns( [ 2,4,5 ] ).visible( false,false );
                            var ozel=$('#ozel').val();
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
                            if(parabirimi2.id===ozelbirimid){
                                fiyat+=fiyat2;
                                fiyat2=0;
                                parabirimi2="";
                            }
                            indirimdurum = $('#indirim').bootstrapSwitch('state');
                            if(indirimdurum === true) //indirim varsa
                            {
                                indirimorani= $('#indirimorani').maskMoney('unmasked')[0];
                                indirim = ((fiyat*indirimorani)/100);
                                indirim2 = ((fiyat2*indirimorani)/100);
                                kdvsiztutar = (fiyat-indirim);
                                kdvsiztutar2 = (fiyat2-indirim2);
                            }else{
                                kdvsiztutar=fiyat;
                                kdvsiztutar2=fiyat2;
                            }
                            kdv=(kdvsiztutar*18)/100;
                            kdv2=(kdvsiztutar2*18)/100;
                            toplamtutar=Math.round((kdvsiztutar+kdv)*100)/100;
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            toplamtutar2=Math.round((kdvsiztutar2+kdv2)*100)/100;
                            toplamtutar2=Math.round(toplamtutar2*2)/2;
                            $('#detaybirim2').val(parabirimi2==="" ? "" : parabirimi2.id);
                        }else{ //garanti içi
                            oTable2.columns( [ 5 ] ).visible( true,false );
                            oTable2.columns( [ 2,3,4,6,7 ] ).visible( false,false );
                        }
                        if(toplamtutar2 === 0){
                            $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi);
                            $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi);
                            $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi);
                            $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi);
                            $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi);
                            $('.sontoplam2').addClass('hide');
                        }else{
                            if(toplamtutar === 0){
                                $('.fiyattutar').text(fiyat2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.indirimtutar').text(indirim2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvsiztutar').text(kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvtutar').text(kdv2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.toplamtutar').text(toplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            }else{
                                $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi+' + '+fiyat2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi+' + '+indirim2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi+' + '+kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi+' + '+kdv2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi+' + '+toplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            }
                            $('.sontoplam2').removeClass('hide');
                        }
                        $('#sontoplam').maskMoney({suffix: " "+ozelbirimi});
                        $('#sontoplam').maskMoney('mask',toplamtutar*100);
                        $('#sontoplam2').maskMoney({suffix: " "+parabirimi2.birimi});
                        $('#sontoplam2').maskMoney('mask',toplamtutar2*100);
                        $('#fiyattutar').val(fiyat.toFixed(2));
                        $('#fiyattutar2').val(fiyat2.toFixed(2));
                        $('#indirimtutar').val(indirim.toFixed(2));
                        $('#indirimtutar2').val(indirim2.toFixed(2));
                        $('#kdvsiztutar').val(kdvsiztutar.toFixed(2));
                        $('#kdvsiztutar2').val(kdvsiztutar2.toFixed(2));
                        $('#kdvtutar').val(kdv.toFixed(2));
                        $('#kdvtutar2').val(kdv2.toFixed(2));
                        $('#toplamtutar').val(toplamtutar.toFixed(2));
                        $('#toplamtutar2').val(toplamtutar2.toFixed(2));
                    }
                });
                $('#garanti').on('change', function () {
                    garanti = $(this).val();
                    id = $('#fiyatdurum').val();
                    fiyat=0;
                    indirim=0;
                    kdvsiztutar=0;
                    kdv=0;
                    toplamtutar=0;
                    fiyat2=0;
                    indirim2=0;
                    kdvsiztutar2=0;
                    kdv2=0;
                    toplamtutar2=0;
                    indirimorani=0.00;
                    ucretsizler=$('#ucretsiz').val();
                    ucretsizler=ucretsizler.split(',');
                    dolar = $('#detaydolar').val();
                    euro = $('#detayeuro').val();
                    sterlin = $('#detaysterlin').val();
                    genelbirimi = $('#genelbirim').val();
                    ozelbirimi= $('#ozelbirim').val();
                    genelbirimid = $('#genelbirimid').val();
                    ozelbirimid= $('#ozelbirimid').val();
                    genelbirimler=JSON.parse($('#genelbirimler').val());
                    ozelbirimler=JSON.parse($('#ozelbirimler').val());
                    kur = 1;
                    if(ozelbirimi!==genelbirimi) //parabirimi farklı kur ile çarpılacak
                    {
                        if(ozelbirimi==='₺') // tl ise
                        {
                            if(genelbirimi==='€') //euro ise
                                kur = euro;
                            else if(genelbirimi==='$') //dolar ise
                                kur = dolar;
                            else
                                kur = sterlin;
                        }else if(ozelbirimi==='€'){ //euro ise
                            if(genelbirimi==='₺') //tl ise
                                kur = 1/euro;
                            else if(genelbirimi==='$') //dolar ise
                                kur = dolar/euro;
                            else
                                kur = sterlin/euro;
                        }else if(ozelbirimi==='$'){ //dolar ise
                            if(genelbirimi==='₺') //tl ise
                                kur = 1/dolar;
                            else if(genelbirimi==='€') //euro ise
                                kur = euro/dolar;
                            else
                                kur = sterlin/dolar;
                        }else{ //sterlin ise
                            if(genelbirimi==='€') //euro ise
                                kur = 1/sterlin;
                            else if(genelbirimi==='$') //dolar ise
                                kur = euro/sterlin;
                            else
                                kur = dolar/sterlin;
                        }
                    }
                    if(id==="0") //genel fiyatlar gözükecek
                    {
                        parabirimi=genelbirimid;
                        parabirimi2="";
                        if(garanti==="0") //garanti dışı
                        {
                            oTable2.columns( [ 2,6 ] ).visible( true,false );
                            oTable2.columns( [ 3,4,5,7 ] ).visible( false,false );
                            $('.indirimkismi').removeClass('hide');
                            $('.indirimkismi2').removeClass('hide');
                            var genel=$('#genel').val();
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
                            fiyat*=kur;
                            if(parabirimi2.id===ozelbirimid){
                                fiyat+=fiyat2;
                                fiyat2=0;
                                parabirimi2="";
                            }
                            indirimdurum = $('#indirim').bootstrapSwitch('state');
                            if(indirimdurum === true) //indirim varsa
                            {
                                indirimorani= $('#indirimorani').maskMoney('unmasked')[0];
                                indirim = ((fiyat*indirimorani)/100);
                                indirim2 = ((fiyat2*indirimorani)/100);
                                kdvsiztutar = (fiyat-indirim);
                                kdvsiztutar2 = (fiyat2-indirim2);
                            }else{
                                kdvsiztutar=fiyat;
                                kdvsiztutar2 = fiyat2;
                            }
                            kdv=(kdvsiztutar*18)/100;
                            kdv2=(kdvsiztutar2*18)/100;
                            toplamtutar=Math.round((kdvsiztutar+kdv)*100)/100;
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            toplamtutar2=Math.round((kdvsiztutar2+kdv2)*100)/100;
                            toplamtutar2=Math.round(toplamtutar2*2)/2;
                            $('#detaybirim2').val(parabirimi2==="" ? "" : parabirimi2.id);
                        }else{ //garanti içi
                            oTable2.columns( [ 4 ] ).visible( true,false );
                            oTable2.columns( [ 2,3,5,6,7 ] ).visible( false,false );
                            $('#indirim').bootstrapSwitch('state',false);
                            $('.indirimkismi').addClass('hide');
                            $('.indirimkismi2').addClass('hide');
                        }
                        if(toplamtutar2 === 0){
                            $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi);
                            $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi);
                            $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi);
                            $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi);
                            $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi);
                            $('.sontoplam2').addClass('hide');
                        }else{
                            if(toplamtutar === 0){
                                $('.fiyattutar').text(fiyat2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.indirimtutar').text(indirim2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvsiztutar').text(kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvtutar').text(kdv2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.toplamtutar').text(toplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            }else{
                                $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi+' + '+fiyat2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi+' + '+indirim2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi+' + '+kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi+' + '+kdv2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi+' + '+toplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            }
                            $('.sontoplam2').removeClass('hide');
                        }
                        $('#sontoplam').maskMoney({suffix: " "+ozelbirimi});
                        $('#sontoplam').maskMoney('mask',toplamtutar*100);
                        $('#sontoplam2').maskMoney({suffix: " "+parabirimi2.birimi});
                        $('#sontoplam2').maskMoney('mask',toplamtutar2*100);
                        $('#fiyattutar').val(fiyat.toFixed(2));
                        $('#fiyattutar2').val(fiyat2.toFixed(2));
                        $('#indirimtutar').val(indirim.toFixed(2));
                        $('#indirimtutar2').val(indirim2.toFixed(2));
                        $('#kdvsiztutar').val(kdvsiztutar.toFixed(2));
                        $('#kdvsiztutar2').val(kdvsiztutar2.toFixed(2));
                        $('#kdvtutar').val(kdv.toFixed(2));
                        $('#kdvtutar2').val(kdv2.toFixed(2));
                        $('#toplamtutar').val(toplamtutar.toFixed(2));
                        $('#toplamtutar2').val(toplamtutar2.toFixed(2));
                    }else{ //ozel fiyatlar
                        parabirimi=ozelbirimid;
                        parabirimi2="";
                        if(garanti==="0") //garanti dışı
                        {
                            oTable2.columns( [ 3,6,7 ] ).visible( true,false );
                            oTable2.columns( [ 2,4,5 ] ).visible( false,false );
                            $('.indirimkismi').removeClass('hide');
                            $('.indirimkismi2').removeClass('hide');
                            var ozel=$('#ozel').val();
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
                            if(parabirimi2.id===ozelbirimid){
                                fiyat+=fiyat2;
                                fiyat2=0;
                                parabirimi2="";
                            }
                            indirimdurum = $('#indirim').bootstrapSwitch('state');
                            if(indirimdurum === true) //indirim varsa
                            {
                                indirimorani= $('#indirimorani').maskMoney('unmasked')[0];
                                indirim = ((fiyat*indirimorani)/100);
                                indirim2 = ((fiyat2*indirimorani)/100);
                                kdvsiztutar = (fiyat-indirim);
                                kdvsiztutar2 = (fiyat2-indirim2);
                            }else{
                                kdvsiztutar=fiyat;
                                kdvsiztutar2=fiyat2;
                            }
                            kdv=(kdvsiztutar*18)/100;
                            kdv2=(kdvsiztutar2*18)/100;
                            toplamtutar=Math.round((kdvsiztutar+kdv)*100)/100;
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            toplamtutar2=Math.round((kdvsiztutar2+kdv2)*100)/100;
                            toplamtutar2=Math.round(toplamtutar2*2)/2;
                            $('#detaybirim2').val(parabirimi2==="" ? "" : parabirimi2.id);
                        }else{ //garanti içi
                            oTable2.columns( [ 5 ] ).visible( true,false );
                            oTable2.columns( [ 2,3,4,6,7 ] ).visible( false,false );
                            $('#indirim').bootstrapSwitch('state',false);
                            $('.indirimkismi').addClass('hide');
                            $('.indirimkismi2').addClass('hide');
                        }
                        if(toplamtutar2 === 0){
                            $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi);
                            $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi);
                            $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi);
                            $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi);
                            $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi);
                            $('.sontoplam2').addClass('hide');
                        }else{
                            if(toplamtutar === 0){
                                $('.fiyattutar').text(fiyat2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.indirimtutar').text(indirim2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvsiztutar').text(kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvtutar').text(kdv2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.toplamtutar').text(toplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            }else{
                                $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi+' + '+fiyat2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi+' + '+indirim2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi+' + '+kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi+' + '+kdv2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi+' + '+toplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            }
                            $('.sontoplam2').removeClass('hide');
                        }
                        $('#sontoplam').maskMoney({suffix: " "+ozelbirimi});
                        $('#sontoplam').maskMoney('mask',toplamtutar*100);
                        $('#sontoplam2').maskMoney({suffix: " "+parabirimi2.birimi});
                        $('#sontoplam2').maskMoney('mask',toplamtutar2*100);
                        $('#fiyattutar').val(fiyat.toFixed(2));
                        $('#fiyattutar2').val(fiyat2.toFixed(2));
                        $('#indirimtutar').val(indirim.toFixed(2));
                        $('#indirimtutar2').val(indirim2.toFixed(2));
                        $('#kdvsiztutar').val(kdvsiztutar.toFixed(2));
                        $('#kdvsiztutar2').val(kdvsiztutar2.toFixed(2));
                        $('#kdvtutar').val(kdv.toFixed(2));
                        $('#kdvtutar2').val(kdv2.toFixed(2));
                        $('#toplamtutar').val(toplamtutar.toFixed(2));
                        $('#toplamtutar2').val(toplamtutar2.toFixed(2));
                    }
                });
                $('#indirimorani').on('change',function(){
                    indirimorani = $(this).maskMoney('unmasked')[0];
                    if(indirimorani>40)
                        indirimorani=40;
                    fiyat = parseFloat($('#fiyattutar').val());
                    fiyat2 = parseFloat($('#fiyattutar2').val());
                    indirim = ((fiyat*indirimorani)/100);
                    indirim2 = ((fiyat2*indirimorani)/100);
                    kdvsiztutar = (fiyat-indirim);
                    kdvsiztutar2 = (fiyat2-indirim2);
                    kdv = ((kdvsiztutar*18)/100);
                    kdv2 = ((kdvsiztutar2*18)/100);
                    toplamtutar = (kdvsiztutar+kdv);
                    toplamtutar=Math.round(toplamtutar*2)/2;
                    toplamtutar2 = (kdvsiztutar2+kdv2);
                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                    birim=($('.fiyattutar').text()).split(' ');
                    $('#indirimoran').val(indirimorani);
                    if(toplamtutar2 === 0){
                        $('.fiyattutar').text(fiyat.toFixed(2)+' '+birim[1]);
                        $('.indirimtutar').text(indirim.toFixed(2)+' '+birim[1]);
                        $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+birim[1]);
                        $('.kdvtutar').text(kdv.toFixed(2)+' '+birim[1]);
                        $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+birim[1]);
                        $('.sontoplam2').addClass('hide');
                    }else{
                        if(toplamtutar === 0){
                            $('.fiyattutar').text(fiyat2.toFixed(2)+' '+birim[4]);
                            $('.indirimtutar').text(indirim2.toFixed(2)+' '+birim[4]);
                            $('.kdvsiztutar').text(kdvsiztutar2.toFixed(2)+' '+birim[4]);
                            $('.kdvtutar').text(kdv2.toFixed(2)+' '+birim[4]);
                            $('.toplamtutar').text(toplamtutar2.toFixed(2)+' '+birim[4]);
                        }else{
                            $('.fiyattutar').text(fiyat.toFixed(2)+' '+birim[1]+' + '+fiyat2.toFixed(2)+' '+birim[4]);
                            $('.indirimtutar').text(indirim.toFixed(2)+' '+birim[1]+' + '+indirim2.toFixed(2)+' '+birim[4]);
                            $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+birim[1]+' + '+kdvsiztutar2.toFixed(2)+' '+birim[4]);
                            $('.kdvtutar').text(kdv.toFixed(2)+' '+birim[1]+' + '+kdv2.toFixed(2)+' '+birim[4]);
                            $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+birim[1]+' + '+toplamtutar2.toFixed(2)+' '+birim[4]);
                        }
                        $('.sontoplam2').removeClass('hide');
                    }
                    $('#sontoplam').maskMoney({suffix: " "+birim[1]});
                    $('#sontoplam').maskMoney('mask',toplamtutar*100);
                    $('#sontoplam2').maskMoney({suffix: " "+birim[4]});
                    $('#sontoplam2').maskMoney('mask',toplamtutar2*100);
                    $('#fiyattutar').val(fiyat.toFixed(2));
                    $('#fiyattutar2').val(fiyat2.toFixed(2));
                    $('#indirimtutar').val(indirim.toFixed(2));
                    $('#indirimtutar2').val(indirim2.toFixed(2));
                    $('#kdvsiztutar').val(kdvsiztutar.toFixed(2));
                    $('#kdvsiztutar2').val(kdvsiztutar2.toFixed(2));
                    $('#kdvtutar').val(kdv.toFixed(2));
                    $('#kdvtutar2').val(kdv2.toFixed(2));
                    $('#toplamtutar').val(toplamtutar.toFixed(2));
                    $('#toplamtutar2').val(toplamtutar2.toFixed(2));
                });
                $('#sontoplam').on('change',function(){
                    toplamtutar = $(this).maskMoney('unmasked')[0];
                    fiyat = parseFloat($('#fiyattutar').val());
                    if((Math.round((fiyat*118/100)*2)/2)===toplamtutar){
                        indirim = 0;
                        indirimorani = 0;
                        kdvsiztutar = (fiyat-indirim);
                        kdv = ((kdvsiztutar*18)/100);
                    }else{
                        kdvsiztutar=(toplamtutar*100)/118;
                        kdv=toplamtutar-kdvsiztutar;
                        indirim = fiyat - kdvsiztutar;
                        indirimorani = ((indirim*100)/fiyat);
                    }
                    toplamtutar = (kdvsiztutar+kdv);
                    toplamtutar=Math.round(toplamtutar*2)/2;
                    if(indirimorani>40){
                        indirimorani=40;
                        indirim = ((fiyat*indirimorani)/100);
                        kdvsiztutar = (fiyat-indirim);
                        kdv = ((kdvsiztutar*18)/100);
                        toplamtutar = (kdvsiztutar+kdv);
                        toplamtutar=Math.round(toplamtutar*2)/2;
                    }else if(indirimorani<0){
                        indirimorani=0;
                        indirim = ((fiyat*indirimorani)/100);
                        kdvsiztutar = (fiyat-indirim);
                        kdv = ((kdvsiztutar*18)/100);
                        toplamtutar = (kdvsiztutar+kdv);
                        toplamtutar=Math.round(toplamtutar*2)/2;
                    }
                    fiyat2 = parseFloat($('#fiyattutar2').val());
                    indirim2 = ((fiyat2*indirimorani)/100);
                    kdvsiztutar2 = (fiyat2-indirim2);
                    kdv2 = ((kdvsiztutar2*18)/100);
                    toplamtutar2 = (kdvsiztutar2+kdv2);
                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                    birim=($('.fiyattutar').text()).split(' ');
                    $('#indirimorani').maskMoney('mask',indirimorani);
                    $('#indirimoran').val(indirimorani);
                    if(toplamtutar2 === 0){
                        $('.fiyattutar').text(fiyat.toFixed(2)+' '+birim[1]);
                        $('.indirimtutar').text(indirim.toFixed(2)+' '+birim[1]);
                        $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+birim[1]);
                        $('.kdvtutar').text(kdv.toFixed(2)+' '+birim[1]);
                        $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+birim[1]);
                        $('.sontoplam2').addClass('hide');
                    }else{
                        if(toplamtutar === 0){
                            $('.fiyattutar').text(fiyat2.toFixed(2)+' '+birim[4]);
                            $('.indirimtutar').text(indirim2.toFixed(2)+' '+birim[4]);
                            $('.kdvsiztutar').text(kdvsiztutar2.toFixed(2)+' '+birim[4]);
                            $('.kdvtutar').text(kdv2.toFixed(2)+' '+birim[4]);
                            $('.toplamtutar').text(toplamtutar2.toFixed(2)+' '+birim[4]);
                        }else{
                            $('.fiyattutar').text(fiyat.toFixed(2)+' '+birim[1]+' + '+fiyat2.toFixed(2)+' '+birim[4]);
                            $('.indirimtutar').text(indirim.toFixed(2)+' '+birim[1]+' + '+indirim2.toFixed(2)+' '+birim[4]);
                            $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+birim[1]+' + '+kdvsiztutar2.toFixed(2)+' '+birim[4]);
                            $('.kdvtutar').text(kdv.toFixed(2)+' '+birim[1]+' + '+kdv2.toFixed(2)+' '+birim[4]);
                            $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+birim[1]+' + '+toplamtutar2.toFixed(2)+' '+birim[4]);
                        }
                        $('.sontoplam2').removeClass('hide');
                    }
                    $('#sontoplam').maskMoney({suffix: " "+birim[1]});
                    $('#sontoplam').maskMoney('mask',toplamtutar*100);
                    $('#sontoplam2').maskMoney({suffix: " "+birim[4]});
                    $('#sontoplam2').maskMoney('mask',toplamtutar2*100);
                    $('#fiyattutar').val(fiyat.toFixed(2));
                    $('#fiyattutar2').val(fiyat2.toFixed(2));
                    $('#indirimtutar').val(indirim.toFixed(2));
                    $('#indirimtutar2').val(indirim2.toFixed(2));
                    $('#kdvsiztutar').val(kdvsiztutar.toFixed(2));
                    $('#kdvsiztutar2').val(kdvsiztutar2.toFixed(2));
                    $('#kdvtutar').val(kdv.toFixed(2));
                    $('#kdvtutar2').val(kdv2.toFixed(2));
                    $('#toplamtutar').val(toplamtutar.toFixed(2));
                    $('#toplamtutar2').val(toplamtutar2.toFixed(2));
                });
                $('#sontoplam2').on('change',function(){
                    toplamtutar2 = $(this).maskMoney('unmasked')[0];
                    fiyat2 = parseFloat($('#fiyattutar2').val());
                    if((Math.round((fiyat2*118/100)*2)/2)===toplamtutar2){
                        indirim2 = 0;
                        indirimorani = 0;
                        kdvsiztutar2 = (fiyat2-indirim2);
                        kdv2 = ((kdvsiztutar2*18)/100);
                    }else{
                        kdvsiztutar2=(toplamtutar2*100)/118;
                        kdv2=toplamtutar2-kdvsiztutar2;
                        indirim2 = fiyat2 - kdvsiztutar2;
                        indirimorani = ((indirim2*100)/fiyat2);
                    }
                    toplamtutar2 = (kdvsiztutar2+kdv2);
                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                    if(indirimorani>40){
                        indirimorani=40;
                        indirim2 = ((fiyat2*indirimorani)/100);
                        kdvsiztutar2 = (fiyat2-indirim2);
                        kdv2 = ((kdvsiztutar2*18)/100);
                        toplamtutar2 = (kdvsiztutar2+kdv2);
                        toplamtutar2=Math.round(toplamtutar2*2)/2;
                    }else if(indirimorani<0){
                        indirimorani=0;
                        indirim2 = ((fiyat2*indirimorani)/100);
                        kdvsiztutar2 = (fiyat2-indirim2);
                        kdv2 = ((kdvsiztutar2*18)/100);
                        toplamtutar2 = (kdvsiztutar2+kdv2);
                        toplamtutar2=Math.round(toplamtutar2*2)/2;
                    }
                    fiyat = parseFloat($('#fiyattutar').val());
                    indirim = ((fiyat*indirimorani)/100);
                    kdvsiztutar = (fiyat-indirim);
                    kdv = ((kdvsiztutar*18)/100);
                    toplamtutar = (kdvsiztutar+kdv);
                    toplamtutar=Math.round(toplamtutar*2)/2;
                    birim=($('.fiyattutar').text()).split(' ');
                    $('#indirimorani').maskMoney('mask',indirimorani);
                    $('#indirimoran').val(indirimorani);
                    if(toplamtutar2 === 0){
                        $('.fiyattutar').text(fiyat.toFixed(2)+' '+birim[1]);
                        $('.indirimtutar').text(indirim.toFixed(2)+' '+birim[1]);
                        $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+birim[1]);
                        $('.kdvtutar').text(kdv.toFixed(2)+' '+birim[1]);
                        $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+birim[1]);
                        $('.sontoplam2').addClass('hide');
                    }else{
                        if(toplamtutar === 0){
                            $('.fiyattutar').text(fiyat2.toFixed(2)+' '+birim[4]);
                            $('.indirimtutar').text(indirim2.toFixed(2)+' '+birim[4]);
                            $('.kdvsiztutar').text(kdvsiztutar2.toFixed(2)+' '+birim[4]);
                            $('.kdvtutar').text(kdv2.toFixed(2)+' '+birim[4]);
                            $('.toplamtutar').text(toplamtutar2.toFixed(2)+' '+birim[4]);
                        }else{
                            $('.fiyattutar').text(fiyat.toFixed(2)+' '+birim[1]+' + '+fiyat2.toFixed(2)+' '+birim[4]);
                            $('.indirimtutar').text(indirim.toFixed(2)+' '+birim[1]+' + '+indirim2.toFixed(2)+' '+birim[4]);
                            $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+birim[1]+' + '+kdvsiztutar2.toFixed(2)+' '+birim[4]);
                            $('.kdvtutar').text(kdv.toFixed(2)+' '+birim[1]+' + '+kdv2.toFixed(2)+' '+birim[4]);
                            $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+birim[1]+' + '+toplamtutar2.toFixed(2)+' '+birim[4]);
                        }
                        $('.sontoplam2').removeClass('hide');
                    }
                    $('#sontoplam').maskMoney({suffix: " "+birim[1]});
                    $('#sontoplam').maskMoney('mask',toplamtutar*100);
                    $('#sontoplam2').maskMoney({suffix: " "+birim[4]});
                    $('#sontoplam2').maskMoney('mask',toplamtutar2*100);
                    $('#fiyattutar').val(fiyat.toFixed(2));
                    $('#fiyattutar2').val(fiyat2.toFixed(2));
                    $('#indirimtutar').val(indirim.toFixed(2));
                    $('#indirimtutar2').val(indirim2.toFixed(2));
                    $('#kdvsiztutar').val(kdvsiztutar.toFixed(2));
                    $('#kdvsiztutar2').val(kdvsiztutar2.toFixed(2));
                    $('#kdvtutar').val(kdv.toFixed(2));
                    $('#kdvtutar2').val(kdv2.toFixed(2));
                    $('#toplamtutar').val(toplamtutar.toFixed(2));
                    $('#toplamtutar2').val(toplamtutar2.toFixed(2));
                });
                $(document).on("click", ".fiyatduzenle", function (){
                    id = $(this).data('id');
                    ozelbirimid = $(this).data('birim');
                    index = oTable2.row($(this).parents('tr')).index();
                    parca = $(this).parents('tr').children('.parca').text();
                    fiyat = ($(this).parents('tr').children('.ozel').text()).split(' ');
                    ucret = fiyat[0];
                    parabirimi = fiyat[1];
                    $('.parcaduzenle').html(parca);
                    $('#ucretduzenle').maskMoney({suffix: ' '+parabirimi,affixesStay:false, allowZero:true});
                    $('#ucretduzenle').val(ucret);
                    $('#parabirimiduzenle').select2("val",ozelbirimid);

                    $('#fiyatindex').val(index);
                    $('#fiyatbirim').val(parabirimi);
                    $('#fiyatid').val(id);
                });
                $(document).on("click", ".fiyatdegistir", function () {
                    fiyatindex = parseInt($('#fiyatindex').val());
                    fiyatbirim = $('#fiyatbirim').val();
                    id = $('#fiyatid').val();
                    ucret = $('#ucretduzenle').val();
                    fiyatbirimid=$('#parabirimiduzenle').val();
                    genelbirimi = $('#genelbirim').val();
                    ozelbirimi= $('#ozelbirim').val();
                    genelbirimid = $('#genelbirimid').val();
                    ozelbirimid= $('#ozelbirimid').val();
                    ozel = $('#ozel').val();
                    ozel=ozel.split(';');
                    ozelbirimler=JSON.parse($('#ozelbirimler').val());
                    yeni="";
                    fiyat=0;
                    indirim=0;
                    kdvsiztutar=0;
                    fiyat2=0;
                    indirim2=0;
                    kdvsiztutar2=0;
                    indirimorani=0.00;
                    parabirimi=ozelbirimid;
                    parabirimi2="";
                    ucretsizler=$('#ucretsiz').val();
                    ucretsizler=ucretsizler.split(',');
                    $.each(ozel,function(index){
                        if(index===fiyatindex)
                        {
                            yeni+=(yeni==="" ? "" : ";")+ucret;
                            ozelbirimler[index].id=fiyatbirimid;
                            ozelbirimler[index].birimi=fiyatbirim;
                            var ozelbirim=JSON.stringify(ozelbirimler);
                            $('#ozelbirimler').val(ozelbirim);
                        }else{
                            yeni+=(yeni==="" ? "" : ";")+ozel[index];
                        }
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
                    if(parabirimi2.id===ozelbirimid){
                        fiyat+=fiyat2;
                        fiyat2=0;
                        parabirimi2="";
                    }
                    $('#ozel').val(yeni);
                    var pagelen=oTable2.page.len();
                    if(fiyatindex+1>pagelen){
                        $('#sample_editable_2 tr:nth-child('+((fiyatindex%pagelen)+1)+') td:nth-child(3)').html(ucret+' '+fiyatbirim);
                        $('#sample_editable_2 tr:nth-child('+((fiyatindex%pagelen)+1)+') td:nth-child(5)').html('<a class="btn btn-sm btn-warning fiyatduzenle" href="#fiyat-duzenle" data-toggle="modal" data-id="'+id+'" data-birim="'+fiyatbirimid+'"> Değiştir </a></td>');
                    }else{
                        $('#sample_editable_2 tr:nth-child('+(fiyatindex+1)+') td:nth-child(3)').html(ucret+' '+fiyatbirim);
                        $('#sample_editable_2 tr:nth-child('+(fiyatindex+1)+') td:nth-child(5)').html('<a class="btn btn-sm btn-warning fiyatduzenle" href="#fiyat-duzenle" data-toggle="modal" data-id="'+id+'" data-birim="'+fiyatbirimid+'"> Değiştir </a></td>');
                    }
                    indirimdurum = $('#indirim').bootstrapSwitch('state');
                    if(indirimdurum === true) //indirim varsa
                    {
                        indirimorani= $('#indirimorani').maskMoney('unmasked')[0];
                        indirim = ((fiyat*indirimorani)/100);
                        indirim2 = ((fiyat2*indirimorani)/100);
                        kdvsiztutar = (fiyat-indirim);
                        kdvsiztutar2 = (fiyat2-indirim2);
                    }else{
                        kdvsiztutar=fiyat;
                        kdvsiztutar2=fiyat2;
                    }
                    kdv=(kdvsiztutar*18)/100;
                    kdv2=(kdvsiztutar2*18)/100;
                    toplamtutar=Math.round((kdvsiztutar+kdv)*100)/100;
                    toplamtutar=Math.round(toplamtutar*2)/2;
                    toplamtutar2=Math.round((kdvsiztutar2+kdv2)*100)/100;
                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                    $('#detaybirim2').val(parabirimi2==="" ? "" : parabirimi2.id);
                    if(toplamtutar2 === 0){
                        $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi);
                        $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi);
                        $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi);
                        $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi);
                        $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi);
                        $('.sontoplam2').addClass('hide');
                    }else{
                        if(toplamtutar === 0){
                            $('.fiyattutar').text(fiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.indirimtutar').text(indirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.kdvsiztutar').text(kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.kdvtutar').text(kdv2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.toplamtutar').text(toplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }else{
                            $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi+' + '+fiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi+' + '+indirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi+' + '+kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi+' + '+kdv2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi+' + '+toplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }
                        $('.sontoplam2').removeClass('hide');
                    }
                    $('#sontoplam').maskMoney({suffix: " "+ozelbirimi});
                    $('#sontoplam').maskMoney('mask',toplamtutar*100);
                    $('#sontoplam2').maskMoney({suffix: " "+parabirimi2.birimi});
                    $('#sontoplam2').maskMoney('mask',toplamtutar2*100);
                    $('#fiyattutar').val(fiyat.toFixed(2));
                    $('#fiyattutar2').val(fiyat2.toFixed(2));
                    $('#indirimtutar').val(indirim.toFixed(2));
                    $('#indirimtutar2').val(indirim2.toFixed(2));
                    $('#kdvsiztutar').val(kdvsiztutar.toFixed(2));
                    $('#kdvsiztutar2').val(kdvsiztutar2.toFixed(2));
                    $('#kdvtutar').val(kdv.toFixed(2));
                    $('#kdvtutar2').val(kdv2.toFixed(2));
                    $('#toplamtutar').val(toplamtutar.toFixed(2));
                    $('#toplamtutar2').val(toplamtutar2.toFixed(2));
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
                "zeroRecords": "Eşleşen Kayıt Bulunmadı"

            },
            "aoColumns": [{"sClass":"id","width": "10%"},{"sClass":"parca","width": "50%"},{"sClass":"genel","width": "30%"},{"sClass":"ozel","width": "20%"},
                    {"sClass":"garantigenel","width": "40%"},{"sClass":"garantiozel","width": "40%"},{"sClass":"free","width": "10%"},{"sClass":"islem","width": "10%"}],
            "lengthMenu": [
                [5, 10],
                [5, 10]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_2_wrapper');
        table2.on('change', 'tbody tr .freecheck', function () {
            var id,fiyatdurum,fiyat,indirim,kdvsiztutar,kdv,toplamtutar,fiyat2,indirim2,kdvsiztutar2,kdv2,toplamtutar2,indirimdurum,indirimorani,ucretsizler,
                genelbirimid,ozelbirimid,dolar,euro,sterlin,genelbirimi,ozelbirimi,kur,parabirimi,parabirimi2,genel,ozel,yeni,genelbirimler,ozelbirimler;

            id = oTable2.row($(this).parents('tr')).index();
            fiyatdurum=$('#fiyatdurum').val();
            fiyat = 0;
            fiyat2 = 0;
            indirim = 0;
            indirim2 = 0;
            kdvsiztutar = 0;
            kdvsiztutar2 = 0;
            kdv = 0;
            kdv2 = 0;
            toplamtutar = 0;
            toplamtutar2 = 0;
            ucretsizler=$('#ucretsiz').val();
            ucretsizler=ucretsizler.split(',');
            dolar = $('#detaydolar').val();
            euro = $('#detayeuro').val();
            sterlin = $('#detaysterlin').val();
            genelbirimi = $('#genelbirim').val();
            ozelbirimi= $('#ozelbirim').val();
            genelbirimid = $('#genelbirimid').val();
            ozelbirimid= $('#ozelbirimid').val();
            genelbirimler=JSON.parse($('#genelbirimler').val());
            ozelbirimler=JSON.parse($('#ozelbirimler').val());
            kur = 1;
            if(ozelbirimi!==genelbirimi) //parabirimi farklı kur ile çarpılacak
            {
                if(ozelbirimi==='₺') // tl ise
                {
                    if(genelbirimi==='€') //euro ise
                        kur = euro;
                    else if(genelbirimi==='$') //dolar ise
                        kur = dolar;
                    else
                        kur = sterlin;
                }else if(ozelbirimi==='€'){ //euro ise
                    if(genelbirimi==='₺') //tl ise
                        kur = 1/euro;
                    else if(genelbirimi==='$') //dolar ise
                        kur = dolar/euro;
                    else
                        kur = sterlin/euro;
                }else if(ozelbirimi==='$'){ //dolar ise
                    if(genelbirimi==='₺') //tl ise
                        kur = 1/dolar;
                    else if(genelbirimi==='€') //euro ise
                        kur = euro/dolar;
                    else
                        kur = sterlin/dolar;
                }else{ //sterlin ise
                    if(genelbirimi==='€') //euro ise
                        kur = 1/sterlin;
                    else if(genelbirimi==='$') //dolar ise
                        kur = euro/sterlin;
                    else
                        kur = dolar/sterlin;
                }
            }
            if($(this).prop('checked')) //ucretsiz olacak
            {
                $(this).parents('tr').children('.islem').children('.fiyatduzenle').addClass('hide');
                $(this).parents('tr').children('.genel').text(0.00.toFixed(2)+' '+genelbirimi);
                $(this).parents('tr').children('.ozel').text(0.00.toFixed(2)+' '+ozelbirimi);

                genel=$('#genel').val();
                genel=genel.split(';');
                yeni="";
                $.each(genel,function(index){
                    if(index===id){
                        yeni+=(yeni==="" ? "" : ",")+"1";
                    }else{
                        yeni+=(yeni==="" ? "" : ",")+ucretsizler[index];
                    }
                });
                $('#ucretsiz').val(yeni);
                ozel=$('#ozel').val();
                ozel=ozel.split(';');
                yeni="";
                $.each(ozel,function(index){
                    if(index===id){
                        yeni+=(yeni==="" ? "" : ",")+"1";
                    }else{
                        yeni+=(yeni==="" ? "" : ",")+ucretsizler[index];
                    }
                });
                $('#ucretsiz').val(yeni);
            }else{ //eski fiyatı çekilecek
                $(this).parents('tr').children('.islem').children('.fiyatduzenle').removeClass('hide');
                var geneldegisecek=$(this).parents('tr').children('.genel');
                var ozeldegisecek=$(this).parents('tr').children('.ozel');
                genel=$('#genel').val();
                genel=genel.split(';');
                yeni="";
                $.each(genel,function(index){
                    if(index===id){
                        yeni+=(yeni==="" ? "" : ",")+"0";
                        geneldegisecek.text(parseFloat(genel[index]).toFixed(2)+' '+genelbirimler[index].birimi);
                    }else{
                        yeni+=(yeni==="" ? "" : ",")+ucretsizler[index];
                    }
                });
                $('#ucretsiz').val(yeni);
                ozel=$('#ozel').val();
                ozel=ozel.split(';');
                yeni="";
                $.each(ozel,function(index){
                    if(index===id){
                        yeni+=(yeni==="" ? "" : ",")+"0";
                        ozeldegisecek.text(parseFloat(ozel[index]).toFixed(2)+' '+ozelbirimler[index].birimi);
                    }else{
                        yeni+=(yeni==="" ? "" : ",")+ucretsizler[index];
                    }
                });
                $('#ucretsiz').val(yeni);
            }
            ucretsizler=$('#ucretsiz').val();
            ucretsizler=ucretsizler.split(',');
            if(fiyatdurum==="0") //genelfiyat
            {
                parabirimi = genelbirimid;
                parabirimi2 = "";
                genel = $('#genel').val();
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
                fiyat *= kur;
                if (parabirimi2.id === ozelbirimid) {
                    fiyat += fiyat2;
                    fiyat2 = 0;
                    parabirimi2 = "";
                }
                indirimdurum = $('#indirim').bootstrapSwitch('state');
                if (indirimdurum === true) //indirim varsa
                {
                    indirimorani = $('#indirimorani').maskMoney('unmasked')[0];
                    indirim = ((fiyat * indirimorani) / 100);
                    indirim2 = ((fiyat2 * indirimorani) / 100);
                    kdvsiztutar = (fiyat - indirim);
                    kdvsiztutar2 = (fiyat2 - indirim2);
                } else {
                    kdvsiztutar = fiyat;
                    kdvsiztutar2 = fiyat2;
                }
                kdv = (kdvsiztutar * 18) / 100;
                kdv2 = (kdvsiztutar2 * 18) / 100;
                toplamtutar = kdvsiztutar + kdv;
                toplamtutar = Math.round(toplamtutar * 2) / 2;
                toplamtutar2 = kdvsiztutar2 + kdv2;
                toplamtutar2 = Math.round(toplamtutar2 * 2) / 2;
                $('#detaybirim2').val(parabirimi2 === "" ? "" : parabirimi2.id);

                if (toplamtutar2 === 0) {
                    $('.fiyattutar').text(fiyat.toFixed(2) + ' ' + ozelbirimi);
                    $('.indirimtutar').text(indirim.toFixed(2) + ' ' + ozelbirimi);
                    $('.kdvsiztutar').text(kdvsiztutar.toFixed(2) + ' ' + ozelbirimi);
                    $('.kdvtutar').text(kdv.toFixed(2) + ' ' + ozelbirimi);
                    $('.toplamtutar').text(toplamtutar.toFixed(2) + ' ' + ozelbirimi);
                    $('.sontoplam2').addClass('hide');
                } else {
                    if(toplamtutar === 0){
                        $('.fiyattutar').text(fiyat2.toFixed(2) + ' ' + parabirimi2.birimi);
                        $('.indirimtutar').text(indirim2.toFixed(2) + ' ' + parabirimi2.birimi);
                        $('.kdvsiztutar').text(kdvsiztutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                        $('.kdvtutar').text(kdv2.toFixed(2) + ' ' + parabirimi2.birimi);
                        $('.toplamtutar').text(toplamtutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                    }else{
                        $('.fiyattutar').text(fiyat.toFixed(2) + ' ' + ozelbirimi + ' + ' + fiyat2.toFixed(2) + ' ' + parabirimi2.birimi);
                        $('.indirimtutar').text(indirim.toFixed(2) + ' ' + ozelbirimi + ' + ' + indirim2.toFixed(2) + ' ' + parabirimi2.birimi);
                        $('.kdvsiztutar').text(kdvsiztutar.toFixed(2) + ' ' + ozelbirimi + ' + ' + kdvsiztutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                        $('.kdvtutar').text(kdv.toFixed(2) + ' ' + ozelbirimi + ' + ' + kdv2.toFixed(2) + ' ' + parabirimi2.birimi);
                        $('.toplamtutar').text(toplamtutar.toFixed(2) + ' ' + ozelbirimi + ' + ' + toplamtutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                    }
                    $('.sontoplam2').removeClass('hide');
                }
                $('#sontoplam').maskMoney({suffix: " " + ozelbirimi});
                $('#sontoplam').maskMoney('mask', toplamtutar * 100);
                $('#sontoplam2').maskMoney({suffix: " " + parabirimi2.birimi});
                $('#sontoplam2').maskMoney('mask', toplamtutar2 * 100);
                $('#fiyattutar').val(fiyat.toFixed(2));
                $('#fiyattutar2').val(fiyat2.toFixed(2));
                $('#indirimtutar').val(indirim.toFixed(2));
                $('#indirimtutar2').val(indirim2.toFixed(2));
                $('#kdvsiztutar').val(kdvsiztutar.toFixed(2));
                $('#kdvsiztutar2').val(kdvsiztutar2.toFixed(2));
                $('#kdvtutar').val(kdv.toFixed(2));
                $('#kdvtutar2').val(kdv2.toFixed(2));
                $('#toplamtutar').val(toplamtutar.toFixed(2));
                $('#toplamtutar2').val(toplamtutar2.toFixed(2));
            }else {
                parabirimi = ozelbirimid;
                parabirimi2 = "";
                ozel = $('#ozel').val();
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
                if (parabirimi2.id === ozelbirimid) {
                    fiyat += fiyat2;
                    fiyat2 = 0;
                    parabirimi2 = "";
                }
                indirimdurum = $('#indirim').bootstrapSwitch('state');
                if (indirimdurum === true) //indirim varsa
                {
                    indirimorani = $('#indirimorani').maskMoney('unmasked')[0];
                    indirim = ((fiyat * indirimorani) / 100);
                    indirim2 = ((fiyat2 * indirimorani) / 100);
                    kdvsiztutar = (fiyat - indirim);
                    kdvsiztutar2 = (fiyat2 - indirim2);
                } else {
                    kdvsiztutar = fiyat;
                    kdvsiztutar2 = fiyat2;
                }
                kdv = (kdvsiztutar * 18) / 100;
                kdv2 = (kdvsiztutar2 * 18) / 100;
                toplamtutar = kdvsiztutar + kdv;
                toplamtutar = Math.round(toplamtutar * 2) / 2;
                toplamtutar2 = kdvsiztutar2 + kdv2;
                toplamtutar2 = Math.round(toplamtutar2 * 2) / 2;
                $('#detaybirim2').val(parabirimi2 === "" ? "" : parabirimi2.id);

                if (toplamtutar2 === 0) {
                    $('.fiyattutar').text(fiyat.toFixed(2) + ' ' + ozelbirimi);
                    $('.indirimtutar').text(indirim.toFixed(2) + ' ' + ozelbirimi);
                    $('.kdvsiztutar').text(kdvsiztutar.toFixed(2) + ' ' + ozelbirimi);
                    $('.kdvtutar').text(kdv.toFixed(2) + ' ' + ozelbirimi);
                    $('.toplamtutar').text(toplamtutar.toFixed(2) + ' ' + ozelbirimi);
                    $('.sontoplam2').addClass('hide');
                } else {
                    if(toplamtutar === 0){
                        $('.fiyattutar').text(fiyat2.toFixed(2) + ' ' + parabirimi2.birimi);
                        $('.indirimtutar').text(indirim2.toFixed(2) + ' ' + parabirimi2.birimi);
                        $('.kdvsiztutar').text(kdvsiztutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                        $('.kdvtutar').text(kdv2.toFixed(2) + ' ' + parabirimi2.birimi);
                        $('.toplamtutar').text(toplamtutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                    }else{
                        $('.fiyattutar').text(fiyat.toFixed(2) + ' ' + ozelbirimi + ' + ' + fiyat2.toFixed(2) + ' ' + parabirimi2.birimi);
                        $('.indirimtutar').text(indirim.toFixed(2) + ' ' + ozelbirimi + ' + ' + indirim2.toFixed(2) + ' ' + parabirimi2.birimi);
                        $('.kdvsiztutar').text(kdvsiztutar.toFixed(2) + ' ' + ozelbirimi + ' + ' + kdvsiztutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                        $('.kdvtutar').text(kdv.toFixed(2) + ' ' + ozelbirimi + ' + ' + kdv2.toFixed(2) + ' ' + parabirimi2.birimi);
                        $('.toplamtutar').text(toplamtutar.toFixed(2) + ' ' + ozelbirimi + ' + ' + toplamtutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                    }
                    $('.sontoplam2').removeClass('hide');
                }
                $('#sontoplam').maskMoney({suffix: " " + ozelbirimi});
                $('#sontoplam').maskMoney('mask', toplamtutar * 100);
                $('#sontoplam2').maskMoney({suffix: " " + parabirimi2.birimi});
                $('#sontoplam2').maskMoney('mask', toplamtutar2 * 100);
                $('#fiyattutar').val(fiyat.toFixed(2));
                $('#fiyattutar2').val(fiyat2.toFixed(2));
                $('#indirimtutar').val(indirim.toFixed(2));
                $('#indirimtutar2').val(indirim2.toFixed(2));
                $('#kdvsiztutar').val(kdvsiztutar.toFixed(2));
                $('#kdvsiztutar2').val(kdvsiztutar2.toFixed(2));
                $('#kdvtutar').val(kdv.toFixed(2));
                $('#kdvtutar2').val(kdv2.toFixed(2));
                $('#toplamtutar').val(toplamtutar.toFixed(2));
                $('#toplamtutar2').val(toplamtutar2.toFixed(2));
            }
        });
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
            "aoColumns": [{"sClass":"id"},null,null,null],
            "lengthMenu": [
                [5, 10],
                [5, 10]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_4_wrapper');
        table4.on('click', 'tr', function () {
            if(oTable4.cell( $(this).children('.id')).data()!==undefined){
                $(this).toggleClass("active");
                var adet=parseInt($('#teslimadet').val());
                var secilenler=$('#teslimsecilenler').val();
                var secilenlist;
                if($(this).hasClass('active'))
                {
                    secilenler+=(secilenler==="" ? "" : ",")+oTable4.cell( $(this).children('.id')).data();
                    adet++;
                    $('#teslimsecilenler').val(secilenler);
                    $('#teslimadet').val(adet);
                    $('.teslimadet').text(adet);
                }else{
                    var secilen=oTable4.cell( $(this).children('.id')).data();
                    secilenlist=secilenler.split(',');
                    var yenilist="";
                    $.each(secilenlist,function(index){
                        if(secilenlist[index]!==secilen)
                        {
                            yenilist+=(yenilist==="" ? "" : ",")+secilenlist[index];
                        }
                    });
                    adet--;
                    secilenler=yenilist;
                    $('#teslimsecilenler').val(yenilist);
                    $('#teslimadet').val(adet);
                    $('.teslimadet').text(adet);
                }
                secilenlist=secilenler.split(',');
                oTable4.rows().every( function () {
                    var data = this.data();
                    var id=data[0];
                    var durum=0;
                    $.each(secilenlist,function(index){
                        if(id===secilenlist[index])
                            durum=1;
                    });
                });
                if(adet>0){
                    $('.teslimet').removeClass('hide');
                }else{
                    $('.teslimet').addClass('hide');
                }
                $('#teslimsecilenler').val(secilenler);
            }
        });
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
            "bFilter" : false,
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
            "aoColumns": [{"sClass":"id"},null,null,null,{"sClass":"garanti"},{"sClass":"fiyatdurum"},
                {"sClass":"tutar"},{"sClass":"indirimorani"},{"sClass":"ksiztutar"},{"sClass":"klitutar"},{"sClass":"ttutar"}
                ,null,null,null,null],
            "lengthMenu": [
                [5, 10, 20, 99999999],
                [5, 10, 20, "Hepsi"]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_6_wrapper');
        table6.on('click', 'tr', function () {
            if(oTable6.cell( $(this).children('.id')).data()!==undefined){
                $(this).toggleClass("active");
                var adet=parseInt($('#topluonizlemeadet').val());
                var secilenler=$('#topluonizlemesecilenler').val();
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
                var dolar = $('#topluonizlemedolar').val();
                var euro = $('#topluonizlemeeuro').val();
                var sterlin = $('#topluonizlemesterlin').val();
                var parabirimi = $('#topluonizlemebirimi').val();
                var parabirimi2 =$('#topluonizlemebirimi2').val();
                var genelgaranti = "1";
                var secilenlist;
                var birim,birim2,fiyat,fiyat2,indirim,kdvsiztutar,kdv,toplamtutar,indirim2,kdvsiztutar2,kdv2,toplamtutar2;
                if($(this).hasClass('active'))
                {
                    secilenler+=(secilenler==="" ? "" : ",")+oTable6.cell( $(this).children('.id')).data();
                    adet++;
                    $('#topluonizlemesecilenler').val(secilenler);
                    $('#topluonizlemeadet').val(adet);
                }else{
                    var secilen=oTable6.cell( $(this).children('.id')).data();
                    secilenlist=secilenler.split(',');
                    var yenilist="";
                    $.each(secilenlist,function(index){
                        if(secilenlist[index]!==secilen)
                        {
                            yenilist+=(yenilist==="" ? "" : ",")+secilenlist[index];
                        }
                    });
                    adet--;
                    secilenler=yenilist;
                    $('#topluonizlemesecilenler').val(yenilist);
                    $('#topluonizlemeadet').val(adet);
                }
                secilenlist=secilenler.split(',');
                oTable6.rows().every( function () {
                    var data = this.data();
                    var id=data[0];
                    var durum=0;
                    $.each(secilenlist,function(index){
                        if(id===secilenlist[index])
                            durum=1;
                    });
                    var garanti = data[4];
                    var indirimorani = parseFloat(data[7]);
                    fiyat = parseFloat(data[11]);
                    fiyat2 = parseFloat(data[13]);
                    birim = data[12];
                    birim2 = data[14];
                    if(durum===1){
                        if (birim === parabirimi) {
                            if (birim2 !== "") {
                                if (birim2 !== parabirimi2) {
                                    fiyat2 = 0;
                                    $('.topluonizlemewarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.topluonizlemeonayla').prop('disabled', true);
                                }
                            } else {
                                fiyat2 = 0;
                            }
                        } else {
                            var kur = 1;
                            if (parabirimi === '₺') // tl ise
                            {
                                if (birim === '€') //euro ise
                                    kur = euro;
                                else if (birim === '$') //dolar ise
                                    kur = dolar;
                                else
                                    kur = sterlin;
                            } else if (parabirimi === '€') { //euro ise
                                if (birim === '₺') //tl ise
                                    kur = 1 / euro;
                                else if (birim === '$') //dolar ise
                                    kur = dolar / euro;
                                else
                                    kur = sterlin / euro;
                            } else if (parabirimi === '$') { //dolar ise
                                if (birim === '₺') //tl ise
                                    kur = 1 / dolar;
                                else if (birim === '€') //euro ise
                                    kur = euro / dolar;
                                else
                                    kur = sterlin / dolar;
                            } else { //sterlin ise
                                if (birim === '€') //euro ise
                                    kur = 1 / sterlin;
                                else if (birim === '$') //dolar ise
                                    kur = euro / sterlin;
                                else
                                    kur = dolar / sterlin;
                            }
                            fiyat *= kur;
                            if (birim2 !== "") {
                                if (birim2 === parabirimi) {
                                    fiyat += fiyat2;
                                    fiyat2 = 0;
                                } else if (birim2 !== parabirimi2) {
                                    fiyat2 = 0;
                                    $('.topluonizlemewarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.topluonizlemeonayla').prop('disabled', true);
                                }
                            } else {
                                fiyat2 = 0;
                            }
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
                    }
                    genelgaranti = genelgaranti==="0" ? "0" : (garanti==='Dışında' ? '0' : '1');
                });
                if(geneltoplamtutar2 === 0){
                    $('.topluonizlemefiyat').text(genelfiyat.toFixed(2)+' '+parabirimi);
                    $('.topluonizlemeindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi);
                    $('.topluonizlemekdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi);
                    $('.topluonizlemekdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi);
                    $('.topluonizlemetoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi);
                }else{
                    if(geneltoplamtutar === 0){
                        $('.topluonizlemefiyat').text(genelfiyat2.toFixed(2)+' '+parabirimi2);
                        $('.topluonizlemeindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2);
                        $('.topluonizlemekdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2);
                        $('.topluonizlemekdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2);
                        $('.topluonizlemetoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2);
                    }else{
                        $('.topluonizlemefiyat').text(genelfiyat.toFixed(2)+' '+parabirimi+' + '+genelfiyat2.toFixed(2)+' '+parabirimi2);
                        $('.topluonizlemeindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi+' + '+genelindirim2.toFixed(2)+' '+parabirimi2);
                        $('.topluonizlemekdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi+' + '+genelkdvsiztutar2.toFixed(2)+' '+parabirimi2);
                        $('.topluonizlemekdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi+' + '+genelkdvtutar2.toFixed(2)+' '+parabirimi2);
                        $('.topluonizlemetoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2);
                    }
                }
                $('#topluonizlemefiyat').val(genelfiyat.toFixed(2));
                $('#topluonizlemeindirimtutar').val(genelindirim.toFixed(2));
                $('#topluonizlemekdvsiztutar').val(genelkdvsiztutar.toFixed(2));
                $('#topluonizlemekdvtutar').val(genelkdvtutar.toFixed(2));
                $('#topluonizlemetoplamtutar').val(geneltoplamtutar.toFixed(2));
                $('#topluonizlemefiyat2').val(genelfiyat2.toFixed(2));
                $('#topluonizlemeindirimtutar2').val(genelindirim2.toFixed(2));
                $('#topluonizlemekdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
                $('#topluonizlemekdvtutar2').val(genelkdvtutar2.toFixed(2));
                $('#topluonizlemetoplamtutar2').val(geneltoplamtutar2.toFixed(2));
                if(adet>0){
                    $('.topluucretlendir').removeClass('hide');
                }else{
                    $('.topluucretlendir').addClass('hide');
                }
                $('#topluonizlemesecilenler').val(secilenler);
                $('#topluonizlemegaranti').val(genelgaranti);
            }
        });
        $(document).on("click", ".temizle6", function () {
            var adet = parseInt($('#topluonizlemeadet').val());
            var secilenler = $('#topluonizlemesecilenler').val();
            $("#sample_editable_6 tbody tr .id").each(function () {
                var secilen = $(this).html();
                $(this).parents('tr').removeClass("active");
                var secilenlist = secilenler.split(',');
                var yenilist = "";
                $.each(secilenlist, function (index) {
                    if (secilenlist[index] !== secilen) {
                        yenilist += (yenilist === "" ? "" : ",") + secilenlist[index];
                    } else {
                        adet--;
                    }
                });
                secilenler = yenilist;
            });
            $('#topluonizlemesecilenler').val(secilenler);
            $('#topluonizlemeadet').val(adet);
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
            var dolar = $('#topluonizlemedolar').val();
            var euro = $('#topluonizlemeeuro').val();
            var sterlin = $('#topluonizlemesterlin').val();
            var parabirimi = $('#topluonizlemebirimi').val();
            var parabirimi2 = $('#topluonizlemebirimi2').val();
            var genelgaranti = "1";
            var secilenlist = secilenler.split(',');
            var birim, birim2, fiyat, fiyat2, indirim, kdvsiztutar, kdv, toplamtutar, indirim2, kdvsiztutar2, kdv2, toplamtutar2;
            oTable6.rows().every(function () {
                var data = this.data();
                var id = data[0];
                var durum = 0;
                $.each(secilenlist, function (index) {
                    if (id === secilenlist[index])
                        durum = 1;
                });
                var garanti = data[4];
                var indirimorani = parseFloat(data[7]);
                fiyat = parseFloat(data[11]);
                fiyat2 = parseFloat(data[13]);
                birim = data[12];
                birim2 = data[14];
                if (durum === 1) {
                    if (birim === parabirimi) {
                        if (birim2 !== "") {
                            if (birim2 !== parabirimi2) {
                                fiyat2 = 0;
                                $('.topluonizlemewarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.topluonizlemeonayla').prop('disabled', true);
                            }
                        } else {
                            fiyat2 = 0;
                        }
                    } else {
                        var kur = 1;
                        if (parabirimi === '₺') // tl ise
                        {
                            if (birim === '€') //euro ise
                                kur = euro;
                            else if (birim === '$') //dolar ise
                                kur = dolar;
                            else
                                kur = sterlin;
                        } else if (parabirimi === '€') { //euro ise
                            if (birim === '₺') //tl ise
                                kur = 1 / euro;
                            else if (birim === '$') //dolar ise
                                kur = dolar / euro;
                            else
                                kur = sterlin / euro;
                        } else if (parabirimi === '$') { //dolar ise
                            if (birim === '₺') //tl ise
                                kur = 1 / dolar;
                            else if (birim === '€') //euro ise
                                kur = euro / dolar;
                            else
                                kur = sterlin / dolar;
                        } else { //sterlin ise
                            if (birim === '€') //euro ise
                                kur = 1 / sterlin;
                            else if (birim === '$') //dolar ise
                                kur = euro / sterlin;
                            else
                                kur = dolar / sterlin;
                        }
                        fiyat *= kur;
                        if (birim2 !== "") {
                            if (birim2 === parabirimi) {
                                fiyat += fiyat2;
                                fiyat2 = 0;
                            } else if (birim2 !== parabirimi2) {
                                fiyat2 = 0;
                                $('.topluonizlemewarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.topluonizlemeonayla').prop('disabled', true);
                            }
                        } else {
                            fiyat2 = 0;
                        }
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
                }
                genelgaranti = genelgaranti === "0" ? "0" : (garanti === 'Dışında' ? '0' : '1');
            });
            if (geneltoplamtutar2 === 0) {
                $('.topluonizlemefiyat').text(genelfiyat.toFixed(2) + ' ' + parabirimi);
                $('.topluonizlemeindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi);
                $('.topluonizlemekdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi);
                $('.topluonizlemekdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi);
                $('.topluonizlemetoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi);
            } else {
                if(geneltoplamtutar === 0) {
                    $('.topluonizlemefiyat').text(genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.topluonizlemeindirimtutar').text(genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.topluonizlemekdvsiztutar').text(genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.topluonizlemekdvtutar').text(genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.topluonizlemetoplamtutar').text(geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }else{
                    $('.topluonizlemefiyat').text(genelfiyat.toFixed(2) + ' ' + parabirimi + ' + ' + genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.topluonizlemeindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi + ' + ' + genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.topluonizlemekdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.topluonizlemekdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.topluonizlemetoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi + ' + ' + geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }
            }
            $('#topluonizlemefiyat').val(genelfiyat.toFixed(2));
            $('#topluonizlemeindirimtutar').val(genelindirim.toFixed(2));
            $('#topluonizlemekdvsiztutar').val(genelkdvsiztutar.toFixed(2));
            $('#topluonizlemekdvtutar').val(genelkdvtutar.toFixed(2));
            $('#topluonizlemetoplamtutar').val(geneltoplamtutar.toFixed(2));
            $('#topluonizlemefiyat2').val(genelfiyat2.toFixed(2));
            $('#topluonizlemeindirimtutar2').val(genelindirim2.toFixed(2));
            $('#topluonizlemekdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
            $('#topluonizlemekdvtutar2').val(genelkdvtutar2.toFixed(2));
            $('#topluonizlemetoplamtutar2').val(geneltoplamtutar2.toFixed(2));
            if (adet > 0) {
                $('.topluucretlendir').removeClass('hide');
            } else {
                $('.topluucretlendir').addClass('hide');
            }
            $('#topluonizlemegaranti').val(genelgaranti);
        } );
        $(document).on("click", ".tumunusec6", function () {
            var adet=parseInt($('#topluonizlemeadet').val());
            var secilenler=$('#topluonizlemesecilenler').val();
            $("#sample_editable_6 tbody tr .id").each(function(){
                var secilen=$(this).html();
                $(this).parents('tr').addClass("active");
                var secilenlist=secilenler.split(',');
                var flag=0;
                $.each(secilenlist,function(index){
                    if(secilenlist[index]===secilen){
                        flag=1;
                    }
                });
                if(flag===0){
                    secilenler+=(secilenler==="" ? "" : ",")+secilen;
                    adet++;
                }
            });
            $('#topluonizlemesecilenler').val(secilenler);
            $('#topluonizlemeadet').val(adet);
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
            var dolar = $('#topluonizlemedolar').val();
            var euro = $('#topluonizlemeeuro').val();
            var sterlin = $('#topluonizlemesterlin').val();
            var parabirimi = $('#topluonizlemebirimi').val();
            var parabirimi2 = $('#topluonizlemebirimi2').val();
            var genelgaranti = "1";
            var secilenlist = secilenler.split(',');
            var birim, birim2, fiyat, fiyat2, indirim, kdvsiztutar, kdv, toplamtutar, indirim2, kdvsiztutar2, kdv2, toplamtutar2;
            oTable6.rows().every(function () {
                var data = this.data();
                var id = data[0];
                var durum = 0;
                $.each(secilenlist, function (index) {
                    if (id === secilenlist[index])
                        durum = 1;
                });
                var garanti = data[4];
                var indirimorani = parseFloat(data[7]);
                fiyat = parseFloat(data[11]);
                fiyat2 = parseFloat(data[13]);
                birim = data[12];
                birim2 = data[14];
                if (durum === 1) {
                    if (birim === parabirimi) {
                        if (birim2 !== "") {
                            if (birim2 !== parabirimi2) {
                                fiyat2 = 0;
                                $('.topluonizlemewarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.topluonizlemeonayla').prop('disabled', true);
                            }
                        } else {
                            fiyat2 = 0;
                        }
                    } else {
                        var kur = 1;
                        if (parabirimi === '₺') // tl ise
                        {
                            if (birim === '€') //euro ise
                                kur = euro;
                            else if (birim === '$') //dolar ise
                                kur = dolar;
                            else
                                kur = sterlin;
                        } else if (parabirimi === '€') { //euro ise
                            if (birim === '₺') //tl ise
                                kur = 1 / euro;
                            else if (birim === '$') //dolar ise
                                kur = dolar / euro;
                            else
                                kur = sterlin / euro;
                        } else if (parabirimi === '$') { //dolar ise
                            if (birim === '₺') //tl ise
                                kur = 1 / dolar;
                            else if (birim === '€') //euro ise
                                kur = euro / dolar;
                            else
                                kur = sterlin / dolar;
                        } else { //sterlin ise
                            if (birim === '€') //euro ise
                                kur = 1 / sterlin;
                            else if (birim === '$') //dolar ise
                                kur = euro / sterlin;
                            else
                                kur = dolar / sterlin;
                        }
                        fiyat *= kur;
                        if (birim2 !== "") {
                            if (birim2 === parabirimi) {
                                fiyat += fiyat2;
                                fiyat2 = 0;
                            } else if (birim2 !== parabirimi2) {
                                fiyat2 = 0;
                                $('.topluonizlemewarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.topluonizlemeonayla').prop('disabled', true);
                            }
                        } else {
                            fiyat2 = 0;
                        }
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
                }
                genelgaranti = genelgaranti === "0" ? "0" : (garanti === 'Dışında' ? '0' : '1');
            });
            if (geneltoplamtutar2 === 0) {
                $('.topluonizlemefiyat').text(genelfiyat.toFixed(2) + ' ' + parabirimi);
                $('.topluonizlemeindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi);
                $('.topluonizlemekdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi);
                $('.topluonizlemekdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi);
                $('.topluonizlemetoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi);
            } else {
                if(geneltoplamtutar === 0){
                    $('.topluonizlemefiyat').text(genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.topluonizlemeindirimtutar').text(genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.topluonizlemekdvsiztutar').text(genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.topluonizlemekdvtutar').text(genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.topluonizlemetoplamtutar').text(geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }else{
                    $('.topluonizlemefiyat').text(genelfiyat.toFixed(2) + ' ' + parabirimi + ' + ' + genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.topluonizlemeindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi + ' + ' + genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.topluonizlemekdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.topluonizlemekdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.topluonizlemetoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi + ' + ' + geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }
            }
            $('#topluonizlemefiyat').val(genelfiyat.toFixed(2));
            $('#topluonizlemeindirimtutar').val(genelindirim.toFixed(2));
            $('#topluonizlemekdvsiztutar').val(genelkdvsiztutar.toFixed(2));
            $('#topluonizlemekdvtutar').val(genelkdvtutar.toFixed(2));
            $('#topluonizlemetoplamtutar').val(geneltoplamtutar.toFixed(2));
            $('#topluonizlemefiyat2').val(genelfiyat2.toFixed(2));
            $('#topluonizlemeindirimtutar2').val(genelindirim2.toFixed(2));
            $('#topluonizlemekdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
            $('#topluonizlemekdvtutar2').val(genelkdvtutar2.toFixed(2));
            $('#topluonizlemetoplamtutar2').val(geneltoplamtutar2.toFixed(2));
            if (adet > 0) {
                $('.topluucretlendir').removeClass('hide');
            } else {
                $('.topluucretlendir').addClass('hide');
            }
            $('#topluonizlemegaranti').val(genelgaranti);
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        $('#sontoplam').maskMoney({suffix: ' ₺',affixesStay:true,allowNegative: false, allowZero:true});
        $('#sontoplam2').maskMoney({suffix: ' €',affixesStay:true,allowNegative: false, allowZero:true});
        $('#indirimorani').maskMoney({prefix: '%',affixesStay:false,allowNegative: false, allowZero:true});
        $("#sontoplam[type='text']").on("click", function () {
            $(this).select();
            var indirimorani=$('#indirimorani').maskMoney('unmasked')[0];
            $('#indirimoran').val(indirimorani);
        });
        $("#sontoplam2[type='text']").on("click", function () {
            $(this).select();
            var indirimorani=$('#indirimorani').maskMoney('unmasked')[0];
            $('#indirimoran').val(indirimorani);
        });
        $("#indirimorani[type='text']").on("click", function () {
            $(this).select();
            var indirimorani=$('#indirimorani').maskMoney('unmasked')[0];
            $('#indirimoran').val(indirimorani);
        });
        $('#parabirimiduzenle').on('change', function () {
            var birimid = $(this).val();
            if(birimid!=="") {
                var birim = $(this).find("option:selected").data('id');
                $('#fiyatbirim').val(birim);
                $('#ucretduzenle').maskMoney({suffix: ' '+birim,affixesStay:false, allowZero:true});
            }else{
                $('#fiyatbirim').val('€');
                $('#ucretduzenle').maskMoney({suffix: ' €',affixesStay:false, allowZero:true});
            }
        });
        $('#indirim').on('switchChange.bootstrapSwitch', function (event, state) {
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
            var birim="";
            if( state===true){
                $('#indirim').attr('checked',true);
                $('#sontoplam').attr("disabled", false);
                $('#sontoplam2').attr("disabled", false);
                $('#indirimorani').attr("disabled", false);
                var indirimorani=$('#indirimorani').maskMoney('unmasked')[0];
                fiyat = parseFloat($('#fiyattutar').val());
                fiyat2 = parseFloat($('#fiyattutar2').val());
                indirim = ((fiyat*indirimorani)/100);
                indirim2 = ((fiyat2*indirimorani)/100);
                kdvsiztutar = (fiyat-indirim);
                kdvsiztutar2 = (fiyat2-indirim2);
                kdv = ((kdvsiztutar*18)/100);
                kdv2 = ((kdvsiztutar2*18)/100);
                toplamtutar = (kdvsiztutar+kdv);
                toplamtutar=Math.round(toplamtutar*2)/2;
                toplamtutar2 = (kdvsiztutar2+kdv2);
                toplamtutar2=Math.round(toplamtutar2*2)/2;
                birim=($('.fiyattutar').text()).split(' ');
                if(toplamtutar2 === 0){
                    $('.fiyattutar').text(fiyat.toFixed(2)+' '+birim[1]);
                    $('.indirimtutar').text(indirim.toFixed(2)+' '+birim[1]);
                    $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+birim[1]);
                    $('.kdvtutar').text(kdv.toFixed(2)+' '+birim[1]);
                    $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+birim[1]);
                    $('.sontoplam2').addClass('hide');
                }else{
                    if(toplamtutar === 0){
                        $('.fiyattutar').text(fiyat2.toFixed(2)+' '+birim[4]);
                        $('.indirimtutar').text(indirim2.toFixed(2)+' '+birim[4]);
                        $('.kdvsiztutar').text(kdvsiztutar2.toFixed(2)+' '+birim[4]);
                        $('.kdvtutar').text(kdv2.toFixed(2)+' '+birim[4]);
                        $('.toplamtutar').text(toplamtutar2.toFixed(2)+' '+birim[4]);
                    }else{
                        $('.fiyattutar').text(fiyat.toFixed(2)+' '+birim[1]+' + '+fiyat2.toFixed(2)+' '+birim[4]);
                        $('.indirimtutar').text(indirim.toFixed(2)+' '+birim[1]+' + '+indirim2.toFixed(2)+' '+birim[4]);
                        $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+birim[1]+' + '+kdvsiztutar2.toFixed(2)+' '+birim[4]);
                        $('.kdvtutar').text(kdv.toFixed(2)+' '+birim[1]+' + '+kdv2.toFixed(2)+' '+birim[4]);
                        $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+birim[1]+' + '+toplamtutar2.toFixed(2)+' '+birim[4]);
                    }
                    $('.sontoplam2').removeClass('hide');
                }
                $('#sontoplam').maskMoney({suffix: " "+birim[1]});
                $('#sontoplam').maskMoney('mask',toplamtutar*100);
                $('#sontoplam2').maskMoney({suffix: " "+birim[4]});
                $('#sontoplam2').maskMoney('mask',toplamtutar2*100);
                $('#fiyattutar').val(fiyat.toFixed(2));
                $('#fiyattutar2').val(fiyat2.toFixed(2));
                $('#indirimtutar').val(indirim.toFixed(2));
                $('#indirimtutar2').val(indirim2.toFixed(2));
                $('#kdvsiztutar').val(kdvsiztutar.toFixed(2));
                $('#kdvsiztutar2').val(kdvsiztutar2.toFixed(2));
                $('#kdvtutar').val(kdv.toFixed(2));
                $('#kdvtutar2').val(kdv2.toFixed(2));
                $('#toplamtutar').val(toplamtutar.toFixed(2));
                $('#toplamtutar2').val(toplamtutar2.toFixed(2));
            }else{
                $('#indirim').attr('checked',false);
                $('#sontoplam').attr("disabled", true);
                $('#sontoplam2').attr("disabled", true);
                $('#indirimorani').attr("disabled", true);
                fiyat = parseFloat($('#fiyattutar').val());
                fiyat2 = parseFloat($('#fiyattutar2').val());
                indirim = (0.00);
                indirim2 = (0.00);
                kdvsiztutar = (fiyat-indirim);
                kdvsiztutar2 = (fiyat2-indirim2);
                kdv = ((kdvsiztutar*18)/100);
                kdv2 = ((kdvsiztutar2*18)/100);
                toplamtutar = (kdvsiztutar+kdv);
                toplamtutar=Math.round(toplamtutar*2)/2;
                toplamtutar2 = (kdvsiztutar2+kdv2);
                toplamtutar2=Math.round(toplamtutar2*2)/2;
                birim=($('.fiyattutar').text()).split(' ');
                if(toplamtutar2 === 0){
                    $('.fiyattutar').text(fiyat.toFixed(2)+' '+birim[1]);
                    $('.indirimtutar').text(indirim.toFixed(2)+' '+birim[1]);
                    $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+birim[1]);
                    $('.kdvtutar').text(kdv.toFixed(2)+' '+birim[1]);
                    $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+birim[1]);
                    $('.sontoplam2').addClass('hide');
                }else{
                    if(toplamtutar === 0){
                        $('.fiyattutar').text(fiyat2.toFixed(2)+' '+birim[4]);
                        $('.indirimtutar').text(indirim2.toFixed(2)+' '+birim[4]);
                        $('.kdvsiztutar').text(kdvsiztutar2.toFixed(2)+' '+birim[4]);
                        $('.kdvtutar').text(kdv2.toFixed(2)+' '+birim[4]);
                        $('.toplamtutar').text(toplamtutar2.toFixed(2)+' '+birim[4]);
                    }else{
                        $('.fiyattutar').text(fiyat.toFixed(2)+' '+birim[1]+' + '+fiyat2.toFixed(2)+' '+birim[4]);
                        $('.indirimtutar').text(indirim.toFixed(2)+' '+birim[1]+' + '+indirim2.toFixed(2)+' '+birim[4]);
                        $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+birim[1]+' + '+kdvsiztutar2.toFixed(2)+' '+birim[4]);
                        $('.kdvtutar').text(kdv.toFixed(2)+' '+birim[1]+' + '+kdv2.toFixed(2)+' '+birim[4]);
                        $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+birim[1]+' + '+toplamtutar2.toFixed(2)+' '+birim[4]);
                    }
                    $('.sontoplam2').removeClass('hide');
                }
                $('#sontoplam').maskMoney({suffix: " "+birim[1]});
                $('#sontoplam').maskMoney('mask',toplamtutar*100);
                $('#sontoplam2').maskMoney({suffix: " "+birim[4]});
                $('#sontoplam2').maskMoney('mask',toplamtutar2*100);
                $('#fiyattutar').val(fiyat.toFixed(2));
                $('#fiyattutar2').val(fiyat2.toFixed(2));
                $('#indirimtutar').val(indirim.toFixed(2));
                $('#indirimtutar2').val(indirim2.toFixed(2));
                $('#kdvsiztutar').val(kdvsiztutar.toFixed(2));
                $('#kdvsiztutar2').val(kdvsiztutar2.toFixed(2));
                $('#kdvtutar').val(kdv.toFixed(2));
                $('#kdvtutar2').val(kdv2.toFixed(2));
                $('#toplamtutar').val(toplamtutar.toFixed(2));
                $('#toplamtutar2').val(toplamtutar2.toFixed(2));
            }
        });
        $('#kayittipi').on('change', function () {
            var tip = $(this).val();
            if (tip !== "") {
                $.blockUI();
                $.getJSON("{{ URL::to('ucretlendirme/kayittipi') }}",{tip:tip}, function (event) {
                    if (event.durum)
                    {
                        var kriter = event.kriter;
                        $("#kayitkriteri").empty();
                        $("#kayitkriteri").append('<option value="">Seçiniz...</option>');
                        $.each(kriter, function (index) {
                            if(tip==="1")
                                $("#kayitkriteri").append('<option value="' + kriter[index].id + '"> ' + kriter[index].cariadi + '</option>');
                            else
                                $("#kayitkriteri").append('<option value="' + kriter[index].id + '"> ' + kriter[index].yeradi + '</option>');
                        });
                        $("#kayitkriteri").select2("val", "");
                    } else {
                        $("#kayitkriteri").empty();
                        $("#kayitkriteri").append('<option value="">Seçiniz...</option>');
                        $("#kayitkriteri").select2("val", "");
                        toastr[event.type](event.text,event.title);
                    }
                    $.unblockUI();
                });
            }else{
                $("#kayitkriteri").empty();
                $("#kayitkriteri").append('<option value="">Seçiniz...</option>');
                $("#kayitkriteri").select2("val", "");
            }
        });

        $(document).on("click", ".duzenle", function () {
            $.blockUI();
            var id = $(this).data('id');
            var action = $('#form_sample_1').data('action');
            $('#form_sample_1').prop('action',action+'/'+id);
            $.getJSON("{{ URL::to('ucretlendirme/kayitdetay') }}",{id:id},function(event){
                if(event.durum){
                    var ucretlendirme = event.ucretlendirme;
                    $(".yer").html(ucretlendirme.uretimyer.yeradi);
                    $("#fiyatdurum").select2("val", ucretlendirme.fiyatdurum);
                    $(".serino").html(ucretlendirme.ariza_serino);
                    $("#garanti").select2("val", ucretlendirme.ariza_garanti);
                    if(ucretlendirme.sayac.sayaccap_id==="1")
                        $(".sayacadi").html(ucretlendirme.sayacadi.sayacadi);
                    else
                        $(".sayacadi").html(ucretlendirme.sayacadi.sayacadi+" - "+ucretlendirme.sayaccap.capadi );
                    oTable2.clear().draw();
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
                            oTable2.columns([4]).visible(true, false);
                            oTable2.columns([2, 3, 5, 6, 7]).visible(false, false);
                        }else {
                            oTable2.columns([2, 6]).visible(true, false);
                            oTable2.columns([3, 4, 5, 7]).visible(false, false);
                        }
                        $.each(degisenler,function(index) {
                            if (ucretsizler[index] === "1") {
                                oTable2.row.add([degisenler[index].id, degisenler[index].tanim, '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,
                                    '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,
                                    '<input type="checkbox" class="freecheck" checked=""/>',
                                    '<a class="btn btn-sm btn-warning fiyatduzenle hide" href="#fiyat-duzenle" data-toggle="modal" data-id="' + degisenler[index].id + '" data-birim="'+ozelbirimid+'"> Değiştir </a>'])
                                        .draw();
                            }else{
                                oTable2.row.add([degisenler[index].id, degisenler[index].tanim, genelfiyat[index] + ' ' + genelbirimler[index].birimi,ozelfiyat[index] + ' ' + ozelbirimler[index].birimi,
                                    '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,
                                    '<input type="checkbox" class="freecheck"/>',
                                    '<a class="btn btn-sm btn-warning fiyatduzenle" href="#fiyat-duzenle" data-toggle="modal" data-id="' + degisenler[index].id + '" data-birim="'+ozelbirimler[index].id+'"> Değiştir </a>'])
                                        .draw();
                            }
                        });
                    }else {
                        if(ucretlendirme.ariza_garanti==="1") {
                            oTable2.columns([ 5 ]).visible(true, false);
                            oTable2.columns([2, 3, 4, 6, 7]).visible(false, false);
                        }else{
                            oTable2.columns( [ 3,6,7 ] ).visible(true, false);
                            oTable2.columns( [ 2,4,5 ] ).visible( false,false );
                        }
                        $.each(degisenler, function (index) {
                            if (ucretsizler[index] === "1") {
                                oTable2.row.add([degisenler[index].id, degisenler[index].tanim, '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,
                                    '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,
                                    '<input type="checkbox" class="freecheck" checked=""/>',
                                    '<a class="btn btn-sm btn-warning fiyatduzenle hide" href="#fiyat-duzenle" data-toggle="modal" data-id="' + degisenler[index].id + '" data-birim="'+ozelbirimid+'"> Değiştir </a>'])
                                        .draw();
                            }else{
                                oTable2.row.add([degisenler[index].id, degisenler[index].tanim, genelfiyat[index] + ' ' + genelbirimler[index].birimi,ozelfiyat[index] + ' ' + ozelbirimler[index].birimi,
                                    '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,
                                    '<input type="checkbox" class="freecheck"/>',
                                    '<a class="btn btn-sm btn-warning fiyatduzenle" href="#fiyat-duzenle" data-toggle="modal" data-id="' + degisenler[index].id + '" data-birim="'+ozelbirimler[index].id+'"> Değiştir </a>'])
                                        .draw();
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
                            $('#indirim').bootstrapSwitch('state', true);
                            $('#sontoplam').attr("disabled", false);
                            $('#sontoplam2').attr("disabled", false);
                            $('#indirimorani').attr("disabled", false);
                            $('#indirimorani').maskMoney('mask',parseFloat(ucretlendirme.indirimorani));
                            $('#indirimoran').val(parseFloat(ucretlendirme.indirimorani));
                            indirim = ((fiyat*parseFloat(ucretlendirme.indirimorani))/100);
                            indirim2 = ((fiyat2*parseFloat(ucretlendirme.indirimorani))/100);
                            kdvsiztutar = (fiyat-indirim);
                            kdvsiztutar2 = (fiyat2-indirim2);
                        }else{
                            $('#indirim').bootstrapSwitch('state', false);
                            $('#sontoplam').attr("disabled", true);
                            $('#sontoplam2').attr("disabled", true);
                            $('#indirimorani').attr("disabled", true);
                            $('#indirimorani').maskMoney('mask',parseFloat(ucretlendirme.indirimorani));
                            $('#indirimoran').val(parseFloat(ucretlendirme.indirimorani));
                            kdvsiztutar = fiyat;
                            kdvsiztutar2 = fiyat2;
                        }
                        kdv=(kdvsiztutar*18)/100;
                        kdv2=(kdvsiztutar2*18)/100;
                        toplamtutar=Math.round((kdvsiztutar+kdv)*100)/100;
                        toplamtutar=Math.round(toplamtutar*2)/2;
                        toplamtutar2=Math.round((kdvsiztutar2+kdv2)*100)/100;
                        toplamtutar2=Math.round(toplamtutar2*2)/2;
                        $('#detaybirim2').val(parabirimi2==="" ? "" : parabirimi2.id);
                        if(toplamtutar2 === 0){
                            $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi);
                            $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi);
                            $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi);
                            $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi);
                            $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi);
                            $('.sontoplam2').addClass('hide');
                        }else{
                            if(toplamtutar === 0){
                                $('.fiyattutar').text(fiyat2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.indirimtutar').text(indirim2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvsiztutar').text(kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvtutar').text(kdv2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.toplamtutar').text(toplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            }else{
                                $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi+' + '+fiyat2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi+' + '+indirim2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi+' + '+kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi+' + '+kdv2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi+' + '+toplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            }
                            $('.sontoplam2').removeClass('hide');
                        }
                        $('#sontoplam').maskMoney({suffix: " "+ozelbirimi});
                        $('#sontoplam').maskMoney('mask',toplamtutar*100);
                        $('#sontoplam2').maskMoney({suffix: " "+parabirimi2.birimi});
                        $('#sontoplam2').maskMoney('mask',toplamtutar2*100);
                        $('#fiyattutar').val(fiyat.toFixed(2));
                        $('#fiyattutar2').val(fiyat2.toFixed(2));
                        $('#indirimtutar').val(indirim.toFixed(2));
                        $('#indirimtutar2').val(indirim2.toFixed(2));
                        $('#kdvsiztutar').val(kdvsiztutar.toFixed(2));
                        $('#kdvsiztutar2').val(kdvsiztutar2.toFixed(2));
                        $('#kdvtutar').val(kdv.toFixed(2));
                        $('#kdvtutar2').val(kdv2.toFixed(2));
                        $('#toplamtutar').val(toplamtutar.toFixed(2));
                        $('#toplamtutar2').val(toplamtutar2.toFixed(2));
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
                            $('#indirim').bootstrapSwitch('state', true);
                            $('#sontoplam').attr("disabled", false);
                            $('#sontoplam2').attr("disabled", false);
                            $('#indirimorani').attr("disabled", false);
                            $('#indirimorani').maskMoney('mask',parseFloat(ucretlendirme.indirimorani));
                            $('#indirimoran').val(parseFloat(ucretlendirme.indirimorani));
                            indirim = ((fiyat*parseFloat(ucretlendirme.indirimorani))/100);
                            indirim2 = ((fiyat2*parseFloat(ucretlendirme.indirimorani))/100);
                            kdvsiztutar = (fiyat-indirim);
                            kdvsiztutar2 = (fiyat2-indirim2);
                        }else{
                            $('#indirim').bootstrapSwitch('state', false);
                            $('#sontoplam').attr("disabled", true);
                            $('#sontoplam2').attr("disabled", true);
                            $('#indirimorani').attr("disabled", true);
                            $('#indirimorani').maskMoney('mask',parseFloat(ucretlendirme.indirimorani));
                            $('#indirimoran').val(parseFloat(ucretlendirme.indirimorani));
                            kdvsiztutar = fiyat;
                            kdvsiztutar2 = fiyat2;
                        }
                        kdv=(kdvsiztutar*18)/100;
                        kdv2=(kdvsiztutar2*18)/100;
                        toplamtutar=Math.round((kdvsiztutar+kdv)*100)/100;
                        toplamtutar=Math.round(toplamtutar*2)/2;
                        toplamtutar2=Math.round((kdvsiztutar2+kdv2)*100)/100;
                        toplamtutar2=Math.round(toplamtutar2*2)/2;
                        $('#detaybirim2').val(parabirimi2==="" ? "" : parabirimi2.id);
                        if(toplamtutar2 === 0){
                            $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi);
                            $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi);
                            $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi);
                            $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi);
                            $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi);
                            $('.sontoplam2').addClass('hide');
                        }else{
                            if(toplamtutar === 0){
                                $('.fiyattutar').text(fiyat2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.indirimtutar').text(indirim2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvsiztutar').text(kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvtutar').text(kdv2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.toplamtutar').text(toplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            }else{
                                $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi+' + '+fiyat2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi+' + '+indirim2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi+' + '+kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi+' + '+kdv2.toFixed(2)+' '+parabirimi2.birimi);
                                $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi+' + '+toplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            }
                            $('.sontoplam2').removeClass('hide');
                        }
                        $('#sontoplam').maskMoney({suffix: " "+ozelbirimi});
                        $('#sontoplam').maskMoney('mask',toplamtutar*100);
                        $('#sontoplam2').maskMoney({suffix: " "+parabirimi2.birimi});
                        $('#sontoplam2').maskMoney('mask',toplamtutar2*100);
                        $('#fiyattutar').val(fiyat.toFixed(2));
                        $('#fiyattutar2').val(fiyat2.toFixed(2));
                        $('#indirimtutar').val(indirim.toFixed(2));
                        $('#indirimtutar2').val(indirim2.toFixed(2));
                        $('#kdvsiztutar').val(kdvsiztutar.toFixed(2));
                        $('#kdvsiztutar2').val(kdvsiztutar2.toFixed(2));
                        $('#kdvtutar').val(kdv.toFixed(2));
                        $('#kdvtutar2').val(kdv2.toFixed(2));
                        $('#toplamtutar').val(toplamtutar.toFixed(2));
                        $('#toplamtutar2').val(toplamtutar2.toFixed(2));
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
        $(document).on("click", ".getir", function () {
            $.blockUI();
            var secilenler =$('#secilenler').val();
            $('#onizlemesecilenler').val(secilenler);
            $.getJSON("{{ URL::to('ucretlendirme/ucretlendirmelistesi') }}",{secilenler:secilenler},function(event){
                if(event.durum) //farklı yerlere ait sayaclar seçilmiş
                {
                    oTable3.clear().draw();
                    oTable3.column(0).visible(false); //id
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
                    var ucretlendirme =event.ucretlendirme;
                    var parabirimi =event.parabirimi;
                    var parabirimi2 =event.parabirimi2;
                    $('#onizlemebirim').val(parabirimi.id);
                    $('#onizlemebirim2').val(parabirimi2 === null ? "" : parabirimi2.id);
                    $('#onizlemekurtarih').val(dovizkurutarih);
                    var genelgaranti = "1";
                    var kurdurum = false;
                    var birimdurum = false;
                    $.each(ucretlendirme,function(index){
                        var serino = ucretlendirme[index].ariza_serino;
                        var sayacadi = ucretlendirme[index].sayacadi.sayacadi;
                        var gelistarihi = ucretlendirme[index].sayacgelen.gdepotarihi;
                        var garanti = (ucretlendirme[index].ariza_garanti==="0" ? 'Dışında' : 'İçinde');
                        genelgaranti = genelgaranti==="1" ? ucretlendirme[index].ariza_garanti : genelgaranti;
                        var fiyatdurum = (ucretlendirme[index].fiyatdurum==="0" ? 'Genel' : 'Özel');
                        var indirimorani =parseFloat(ucretlendirme[index].indirimorani);
                        var fiyat= parseFloat(ucretlendirme[index].fiyat);
                        var fiyat2= parseFloat(ucretlendirme[index].fiyat2);
                        var indirim =(fiyat*indirimorani)/100;
                        var indirim2 =(fiyat2*indirimorani)/100;
                        var kdvsiztutar =parseFloat(ucretlendirme[index].tutar);
                        var kdvsiztutar2 =parseFloat(ucretlendirme[index].tutar2);
                        var kdv =parseFloat(ucretlendirme[index].kdv);
                        var kdv2 =parseFloat(ucretlendirme[index].kdv2);
                        var toplamtutar =parseFloat(ucretlendirme[index].toplamtutar);
                        var toplamtutar2 =parseFloat(ucretlendirme[index].toplamtutar2);
                        var birim =ucretlendirme[index].parabirimi;
                        var birim2 =ucretlendirme[index].parabirimi2;
                        if(toplamtutar2>0){
                            if(toplamtutar>0){
                                oTable3.row.add([ucretlendirme[index].id,serino,sayacadi,gelistarihi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi+' + '+fiyat2.toFixed(2)+' '+birim2.birimi,
                                    indirimorani+' %',kdvsiztutar.toFixed(2)+' '+birim.birimi+' + '+kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv.toFixed(2)+' '+birim.birimi+' + '+kdv2.toFixed(2)+' '+birim2.birimi,
                                    toplamtutar.toFixed(2)+' '+birim.birimi+' + '+toplamtutar2.toFixed(2)+' '+birim2.birimi]).draw();
                            }else{
                                oTable3.row.add([ucretlendirme[index].id,serino,sayacadi,gelistarihi,garanti,fiyatdurum,fiyat2.toFixed(2)+' '+birim2.birimi,indirimorani+' %',
                                    kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv2.toFixed(2)+' '+birim2.birimi,toplamtutar2.toFixed(2)+' '+birim2.birimi]).draw();
                            }
                        }else{
                            oTable3.row.add([ucretlendirme[index].id,serino,sayacadi,gelistarihi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi,indirimorani+' %',
                                kdvsiztutar.toFixed(2)+' '+birim.birimi,kdv.toFixed(2)+' '+birim.birimi,toplamtutar.toFixed(2)+' '+birim.birimi]).draw();
                        }

                        if(birim.id===parabirimi.id){
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            if(birim2!=null){
                                if(birim2.id===parabirimi2.id){
                                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                                }else{
                                    toplamtutar2=0;
                                    $('.onizlemewarning').html('<span style="color:red">İki Parabiriminden Fazla.</span>');
                                    $('.ucretlendir').prop('disabled', true);
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
                                    $('.onizlemewarning').html('<span style="color:red">İki Parabiriminden Fazla.</span>');
                                    $('.ucretlendir').prop('disabled', true);
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
                    if(euro==="0") {
                        $('.onizlemewarning').html('<span style="color:red">Bugünün Kur Bilgisi Netsise Girilmemiş! Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
                        $('.ucretlendir').prop('disabled', true);
                    }else if(birimdurum) {
                        $('.onizlemewarning').html('<span style="color:red">Kur Tarihi: ' + dovizkurutarih + '</span>');
                        $('.ucretlendir').prop('disabled', false);
                    }else {
                        $('.onizlemewarning').html('<span style="color:red">Kur Tarihi: ' + dovizkurutarih + '. Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
                        $('.ucretlendir').prop('disabled', false);
                    }
                    if(kurdurum)
                        $('.onizlemekur').removeClass('hide');
                    else
                        $('.onizlemekur').addClass('hide');
                    $('.onizlemeyer').html(ucretlendirme[0].uretimyer.yeradi);
                    if(ucretlendirme[0].sayacgelen.teslimdurum==="4")
                        $('#onizlemeservis').val(6);
                    else
                        $('#onizlemeservis').val(ucretlendirme[0].sayacgelen.servis_id);
                    $('#onizlemeuretimyer').val(ucretlendirme[0].uretimyer_id);
                    $('#onizlemegaranti').val(genelgaranti);
                    $('#onizlemenetsiscari').val(ucretlendirme[0].netsiscari_id);
                    $('#onizlemeadet').val(ucretlendirme.length);

                    if(geneltoplamtutar2 === 0){
                        $('.onizlemefiyat').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi);
                        $('.onizlemeindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi);
                        $('.onizlemekdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.onizlemekdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.onizlemetoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi);
                    }else{
                        if(geneltoplamtutar === 0){
                            $('.onizlemefiyat').text(genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onizlemeindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onizlemekdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onizlemekdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onizlemetoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }else{
                            $('.onizlemefiyat').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi+' + '+genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onizlemeindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi+' + '+genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onizlemekdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onizlemekdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onizlemetoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }
                    }
                    $('#onizlemefiyat').val(genelfiyat.toFixed(2));
                    $('#onizlemeindirimtutar').val(genelindirim.toFixed(2));
                    $('#onizlemekdvsiztutar').val(genelkdvsiztutar.toFixed(2));
                    $('#onizlemekdvtutar').val(genelkdvtutar.toFixed(2));
                    $('#onizlemetoplamtutar').val(geneltoplamtutar.toFixed(2));
                    $('#onizlemefiyat2').val(genelfiyat2.toFixed(2));
                    $('#onizlemeindirimtutar2').val(genelindirim2.toFixed(2));
                    $('#onizlemekdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
                    $('#onizlemekdvtutar2').val(genelkdvtutar2.toFixed(2));
                    $('#onizlemetoplamtutar2').val(geneltoplamtutar2.toFixed(2));
                }else{
                    toastr[event.type](event.text,event.title);
                    $('#getir').modal('hide');
                }
                $.unblockUI();
            });
        });
        $(document).on("click", ".subegonder", function () {
            $.blockUI();
            var secilenler =$('#secilenler').val();
            $.getJSON("{{ URL::to('ucretlendirme/aktarmabilgi') }}",{secilenler:secilenler},function(event){
                if(event.durum)
                {
                    var arizafiyat = event.arizafiyat;
                    var sayacgelen = event.sayacgelen;
                    var secilen= event.secilen;
                    var secilensayi=event.secilensayi;
                    $(".teslimyer").html(arizafiyat.uretimyer.yeradi);
                    $(".teslimcariadi").html(arizafiyat.netsiscari.cariadi);
                    $('#teslimsecilenler').val(secilen);
                    $('#teslimadet').val(secilensayi);
                    $('.teslimadet').text(secilensayi);
                    oTable4.clear().draw();
                    $.each(sayacgelen,function(index) {
                        var sayacadi = sayacgelen[index].sayacadi.sayacadi;
                        var serino = sayacgelen[index].serino;
                        var sayaccap = sayacgelen[index].sayaccap.capadi;
                        var tarih = sayacgelen[index].tarih;
                        oTable4.row.add([sayacgelen[index].id,serino,sayacadi+' '+sayaccap,tarih])
                                .draw().nodes().to$().addClass( 'active' );
                    });
                }else{
                    toastr[event.type](event.text, event.type);
                    $('#subegonder').modal('hide');
                }
                $.unblockUI();
            });
        });
        $(document).on("click", ".kayitgetir", function () {
            $.blockUI();
            $('#topluucretlendir').modal('hide');
            var kayittipi =$('#kayittipi').val();
            var kayitkriteri =$('#kayitkriteri').val();
            var kayitid =$('#kayitid').val();
            $.getJSON("{{ URL::to('ucretlendirme/topluucretlendirmelistesi') }}",{kayittipi:kayittipi,kayitkriteri:kayitkriteri,kayitid:kayitid},function(event){
                if(event.durum)
                {
                    $('#toplugetir').modal('show');
                    oTable6.clear().draw();
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
                    $('.topluonizlemeeuro').html('Euro : '+euro+' ₺');
                    $('.topluonizlemedolar').html('Dolar : '+dolar+' ₺');
                    $('.topluonizlemesterlin').html('Sterlin : '+sterlin+' ₺');
                    $('#topluonizlemedolar').val(dolar);
                    $('#topluonizlemeeuro').val(euro);
                    $('#topluonizlemesterlin').val(sterlin);
                    var ucretlendirme =event.ucretlendirme;
                    var parabirimi =event.parabirimi;
                    var parabirimi2 =event.parabirimi2;
                    $('#topluonizlemebirim').val(parabirimi.id);
                    $('#topluonizlemebirim2').val(parabirimi2 === null ? "" : parabirimi2.id);
                    $('#topluonizlemebirimi').val(parabirimi.birimi);
                    $('#topluonizlemebirimi2').val(parabirimi2==null ? "" : parabirimi2.birimi);
                    $('#topluonizlemekurtarih').val(dovizkurutarih);
                    var genelgaranti = "1";
                    var kurdurum = false;
                    var birimdurum = false;
                    var secilenlist="";
                    $.each(ucretlendirme,function(index){
                        secilenlist += (secilenlist==="" ? "" : ",")+ucretlendirme[index].id;
                        var serino = ucretlendirme[index].ariza_serino;
                        var sayacadi = ucretlendirme[index].sayacadi.sayacadi;
                        var gelistarihi = ucretlendirme[index].sayacgelen.gdepotarihi;
                        var garanti = (ucretlendirme[index].ariza_garanti==="0" ? 'Dışında' : 'İçinde');
                        genelgaranti = genelgaranti==="1" ? ucretlendirme[index].ariza_garanti : genelgaranti;
                        var fiyatdurum = (ucretlendirme[index].fiyatdurum==="0" ? 'Genel' : 'Özel');
                        var indirimorani =parseFloat(ucretlendirme[index].indirimorani);
                        var fiyat= parseFloat(ucretlendirme[index].fiyat);
                        var fiyat2= parseFloat(ucretlendirme[index].fiyat2);
                        var indirim =(fiyat*indirimorani)/100;
                        var indirim2 =(fiyat2*indirimorani)/100;
                        var kdvsiztutar =parseFloat(ucretlendirme[index].tutar);
                        var kdvsiztutar2 =parseFloat(ucretlendirme[index].tutar2);
                        var kdv =parseFloat(ucretlendirme[index].kdv);
                        var kdv2 =parseFloat(ucretlendirme[index].kdv2);
                        var toplamtutar =parseFloat(ucretlendirme[index].toplamtutar);
                        var toplamtutar2 =parseFloat(ucretlendirme[index].toplamtutar2);
                        var birim =ucretlendirme[index].parabirimi;
                        var birim2 =ucretlendirme[index].parabirimi2;
                        oTable6.columns( [ 11,12,13,14 ] ).visible( false,false );
                        if(toplamtutar2>0){
                            if(toplamtutar>0){
                                oTable6.row.add([ucretlendirme[index].id,serino,sayacadi,gelistarihi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi+' + '+fiyat2.toFixed(2)+' '+birim2.birimi,
                                    indirimorani+' %',kdvsiztutar.toFixed(2)+' '+birim.birimi+' + '+kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv.toFixed(2)+' '+birim.birimi+' + '+kdv2.toFixed(2)+' '+birim2.birimi,
                                    toplamtutar.toFixed(2)+' '+birim.birimi+' + '+toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                    fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),birim2.birimi]).draw()
                                    .nodes().to$().addClass( 'active' );
                            }else{
                                oTable6.row.add([ucretlendirme[index].id,serino,sayacadi,gelistarihi,garanti,fiyatdurum,fiyat2.toFixed(2)+' '+birim2.birimi,indirimorani+' %',
                                    kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv2.toFixed(2)+' '+birim2.birimi,toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                    fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),birim2.birimi]).draw()
                                    .nodes().to$().addClass( 'active' );
                            }
                        }else{
                            oTable6.row.add([ucretlendirme[index].id,serino,sayacadi,gelistarihi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi,indirimorani+' %',
                                kdvsiztutar.toFixed(2)+' '+birim.birimi,kdv.toFixed(2)+' '+birim.birimi,toplamtutar.toFixed(2)+' '+birim.birimi,
                                fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),'']).draw()
                                .nodes().to$().addClass( 'active' );
                        }

                        if(birim.id===parabirimi.id){
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            if(birim2!=null){
                                if(birim2.id===parabirimi2.id){
                                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                                }else{
                                    toplamtutar2=0;
                                    $('.topluonizlemewarning').html('<span style="color:red">İki Parabiriminden Fazla.</span>');
                                    $('.topluucretlendir').prop('disabled', true);
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
                                    $('.topluonizlemewarning').html('<span style="color:red">İki Parabiriminden Fazla.</span>');
                                    $('.topluucretlendir').prop('disabled', true);
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
                    if(euro==="0") {
                        $('.topluonizlemewarning').html('<span style="color:red">Bugünün Kur Bilgisi Netsise Girilmemiş! Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
                        $('.topluucretlendir').prop('disabled', true);
                    }else if(birimdurum) {
                        $('.topluonizlemewarning').html('<span style="color:red">Kur Tarihi: ' + dovizkurutarih + '</span>');
                        $('.topluucretlendir').prop('disabled', false);
                    }else {
                        $('.topluonizlemewarning').html('<span style="color:red">Kur Tarihi: ' + dovizkurutarih + '. Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
                        $('.topluucretlendir').prop('disabled', false);
                    }
                    if(kurdurum)
                        $('.topluonizlemekur').removeClass('hide');
                    else
                        $('.topluonizlemekur').addClass('hide');
                    $('.topluonizlemeyer').html(ucretlendirme[0].uretimyer.yeradi);
                    if(ucretlendirme[0].sayacgelen.teslimdurum==="4")
                        $('#topluonizlemeservis').val(6);
                    else
                        $('#topluonizlemeservis').val(ucretlendirme[0].sayacgelen.servis_id);
                    $('#topluonizlemeuretimyer').val(ucretlendirme[0].uretimyer_id);
                    $('#topluonizlemegaranti').val(genelgaranti);
                    $('#topluonizlemenetsiscari').val(ucretlendirme[0].netsiscari_id);
                    $('#topluonizlemeadet').val(ucretlendirme.length);
                    $('#topluonizlemesecilenler').val(secilenlist);
                    if(geneltoplamtutar2 === 0){
                        $('.topluonizlemefiyat').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi);
                        $('.topluonizlemeindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi);
                        $('.topluonizlemekdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.topluonizlemekdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.topluonizlemetoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi);
                    }else{
                        if(geneltoplamtutar === 0){
                            $('.topluonizlemefiyat').text(genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.topluonizlemeindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.topluonizlemekdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.topluonizlemekdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.topluonizlemetoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }else{
                            $('.topluonizlemefiyat').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi+' + '+genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.topluonizlemeindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi+' + '+genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.topluonizlemekdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.topluonizlemekdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.topluonizlemetoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }
                    }
                    $('#topluonizlemefiyat').val(genelfiyat.toFixed(2));
                    $('#topluonizlemeindirimtutar').val(genelindirim.toFixed(2));
                    $('#topluonizlemekdvsiztutar').val(genelkdvsiztutar.toFixed(2));
                    $('#topluonizlemekdvtutar').val(genelkdvtutar.toFixed(2));
                    $('#topluonizlemetoplamtutar').val(geneltoplamtutar.toFixed(2));
                    $('#topluonizlemefiyat2').val(genelfiyat2.toFixed(2));
                    $('#topluonizlemeindirimtutar2').val(genelindirim2.toFixed(2));
                    $('#topluonizlemekdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
                    $('#topluonizlemekdvtutar2').val(genelkdvtutar2.toFixed(2));
                    $('#topluonizlemetoplamtutar2').val(geneltoplamtutar2.toFixed(2));
                }else{
                    toastr[event.type](event.text,event.title);
                    $('#toplugetir').modal('hide');
                }
                $.unblockUI();
            });
        });
        $('#formsubmit').click(function () {
            $('#form_sample_3').submit();
            $.blockUI();
        });
        $('#formtoplusubmit').click(function () {
            $('#form_sample_6').submit();
            $.blockUI();
        });
        $('#formsube').click(function () {
            $('#form_sample_4').submit();
            $.blockUI();
        });
        $('#formupdate').click(function () {
            $('#form_sample_1').submit();
            $.blockUI();
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
                        <i class="fa fa-tag"></i>Ücretlendirme Bekleyen Sayaçlar ve Fiyatlandırması
                    </div>
                    <div class="actions">
                        <a class="btn btn-default btn-sm ucretlendir" data-toggle="modal" data-target="#topluucretlendir">
                            <i class="fa fa-pencil"></i> Toplu Ücretlendirme</a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Serino</th>
                            <th>Cari Adı</th>
                            <th>Üretim Yeri</th>
                            <th>Sayaç Adı</th>
                            <th>Garanti</th>
                            <th>Tutar</th>
                            <th class="hide"></th>
                            <th>Geliş Tarihi</th>
                            <th class="hide"></th>
                            <th></th><th></th><th></th><th></th>
                            <th>İşlemler</th>
                            <th class="hide"></th>
                            <th class="hide"></th>
                        </tr>
                        </thead>
                    </table>
                    <form action="{{ URL::to('ucretlendirme/ucretlendir') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-sm-2 col-xs-6">Seçilen Sayaç Sayısı:</label>
                                <label class="col-sm-3 col-xs-6 secilenadet" style="padding-top: 9px">0</label>
                                <div class="col-sm-6 col-xs-6">
                                    <a class='btn green subegonder hide' href='#subegonder' data-toggle='modal' data-id=''> Şubeye Gönder</a>
                                    <a class='btn green getir hide' href='#getir' data-toggle='modal' data-id=''> Ücretlendir</a>
                                </div>
                                <input type="text" id="secilenler" name="secilenler" value="{{ Input::old('secilenler') }}" class="form-control hide"/>
                                <input type="text" id="subesecilenler" name="subesecilenler" value="{{ Input::old('subesecilenler') }}" class="form-control hide"/>
                                <input type="text" id="hatirlatmaid" name="hatirlatmaid" value="{{ Input::old('hatirlatmaid') ? Input::old('hatirlatmaid') : isset($hatirlatma_id) ? $hatirlatma_id : "" }}" class="form-control hide"/>
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
    <div class="modal fade" id="detay-duzenle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                                <form action="{{ URL::to('ucretlendirme/detayduzenle') }}" data-action="{{URL::to('ucretlendirme/detayduzenle')}}" id="form_sample_1" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Ücretlendirme Düzenle</h3>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Yeri:</label>
                                            <label class="col-xs-8 yer" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Fiyat Durumu:</label>
                                            <div class="col-xs-8">
                                                <select class="form-control select2me select2-offscreen" id="fiyatdurum" name="fiyatdurum" tabindex="-1" title="">
                                                    <option value="0">Genel</option>
                                                    <option value="1">Özel</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">SeriNo:</label>
                                            <label class="col-xs-8 serino" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Garanti:</label>
                                            <div class="col-xs-8">
                                                <select class="form-control select2me select2-offscreen" id="garanti" name="garanti" tabindex="-1" title="">
                                                    <option value="0">Dışında</option>
                                                    <option value="1">İçinde</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Sayaç Adı:</label>
                                            <label class="col-xs-8 sayacadi" style="padding-top: 7px"></label>
                                            <input class="hide" id="genel" name="genel"/>
                                            <input class="hide" id="ozel" name="ozel"/>
                                            <input class="hide" id="ucretsiz" name="ucretsiz"/>
                                            <input class="hide" id="genelbirim" name="genelbirim"/>
                                            <input class="hide" id="ozelbirim" name="ozelbirim"/>
                                            <input class="hide" id="genelbirimid" name="genelbirimid"/>
                                            <input class="hide" id="ozelbirimid" name="ozelbirimid"/>
                                            <input class="hide" id="genelbirimler" name="genelbirimler"/>
                                            <input class="hide" id="ozelbirimler" name="ozelbirimler"/>

                                        </div>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_2">
                                                <thead>
                                                <tr>
                                                    <th class="" style='width: 10%;'>#</th>
                                                    <th style='width: 50%;'>Değişen Parça</th>
                                                    <th class="genelfiyat" style='width: 30%;'>Fiyatı</th>
                                                    <th class="ozelfiyat " style='width: 20%;'>Fiyatı</th>
                                                    <th class="genelgaranti " style='width: 40%;'>Fiyatı</th>
                                                    <th class="ozelgaranti " style='width: 40%;'>Fiyatı</th>
                                                    <th class="ucretsiz " style='width: 10%;'>Ücretsiz</th>
                                                    <th class="islemduzenle " style='width: 10%;'>İşlemler</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-7 col-xs-12">
                                                <div class="col-sm-5 col-xs-12">
                                                    <div class="col-xs-12 kur">
                                                        <label class="col-xs-12 detayeuro" style="padding-top: 9px;margin-left:5px;text-align:center">Euro : 0.0000 ₺</label>
                                                        <label class="col-xs-12 detaydolar" style="padding-top: 9px;margin-left:3px;text-align:center">Dolar : 0.0000 ₺</label>
                                                        <label class="col-xs-12 detaysterlin" style="padding-top: 9px;text-align:center">Sterlin : 0.0000 ₺</label>
                                                        <input id="detayeuro" class="hide">
                                                        <input id="detaydolar" class="hide">
                                                        <input id="detaysterlin" class="hide">
                                                    </div>
                                                </div>
                                                <div class="col-sm-7 col-xs-12">
                                                    <div class="indirimkismi col-xs-12">
                                                        <label class="control-label col-xs-6" style="padding-top: 0;">İndirim Uygulanacak?</label>
                                                        <div class="col-xs-6">
                                                            <div class="bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-animate bootstrap-switch-off" style="width: 78px;">
                                                                <div class="bootstrap-switch-container" style="width: 114px; margin-left: 0;">
                                                                    <input type="checkbox" id="indirim" name="indirim" class="make-switch  switch-large" data-on-color="success" data-off-color="warning" data-label-icon="fa fa-fullscreen" data-on-text="<i class='fa fa-check'></i>" data-off-text="<i class='fa fa-times'></i>" @if(Input::old('indirim')) checked @endif>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="indirimkismi2 col-xs-12">
                                                        <label class="control-label col-xs-6">İndirim Oranı:</label>
                                                        <div class="col-xs-6">
                                                            <input type="tel" id="indirimorani"  name="indirimorani" value="{{Input::old('indirimorani')}}" data-required="1" class="form-control" maxlength="6" disabled="disabled">
                                                            <input type="text" id="indirimoran"  name="indirimoran" value="{{Input::old('indirimoran')}}" class="form-control hide">

                                                        </div>
                                                    </div>
                                                    <div class="indirimkismi2 col-xs-12" style="margin-top: 5px;">
                                                        <label class="control-label col-xs-6" style="padding-top: 0;">İndirim Sonrası Toplam Tutar:</label>
                                                        <div class="col-xs-6">
                                                            <input type="tel" id="sontoplam"  name="sontoplam" value="{{Input::old('sontoplam')}}" data-required="1" class="form-control" disabled="disabled">
                                                        </div>
                                                        <div class="col-xs-6 sontoplam2">
                                                            <input type="tel" id="sontoplam2"  name="sontoplam2" value="{{Input::old('sontoplam2')}}" data-required="1" class="form-control" disabled="disabled">
                                                        </div>
                                                    </div>
                                                </div>
                                                <label class="control-label col-xs-12 warning" style="text-align: left"></label>
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
                                                <input class="hide" id="fiyattutar" name="fiyattutar"/>
                                                <input class="hide" id="fiyattutar2" name="fiyattutar2"/>
                                                <input class="hide" id="indirimtutar" name="indirimtutar"/>
                                                <input class="hide" id="indirimtutar2" name="indirimtutar2"/>
                                                <input class="hide" id="kdvsiztutar" name="kdvsiztutar"/>
                                                <input class="hide" id="kdvsiztutar2" name="kdvsiztutar2"/>
                                                <input class="hide" id="kdvtutar" name="kdvtutar"/>
                                                <input class="hide" id="kdvtutar2" name="kdvtutar2"/>
                                                <input class="hide" id="toplamtutar" name="toplamtutar"/>
                                                <input class="hide" id="toplamtutar2" name="toplamtutar2"/>
                                                <input class="hide" id="detaybirim" name="detaybirim"/>
                                                <input class="hide" id="detaybirim2" name="detaybirim2"/>
                                                <input class="hide" id="detaykurtarih" name="detaykurtarih"/>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green" data-toggle="modal" data-target="#updateconfirm">Kaydet</button>
                                                    <button type="button" class="btn default vazgec" data-dismiss="modal">Vazgeç</button>
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
    <div class="modal fade" id="fiyat-duzenle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Fiyat Düzenle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Parça Fiyatını Güncelle</h3>
                                        <input class="hide" id="fiyatindex" name="fiyatindex"/>
                                        <input class="hide" id="fiyatbirim" name="fiyatbirim"/>
                                        <input class="hide" id="fiyatid" name="fiyatid"/>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Değişen Parça:</label>
                                            <label class="col-xs-8 parcaduzenle" style="padding-top: 7px;margin-bottom:0"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Fiyatı:</label>
                                            <div class="col-xs-8">
                                                <input type="tel" id="ucretduzenle"  name="ucretduzenle" value="{{Input::old('ucretduzenle')}}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Para Birimi: <span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><select class="form-control select2me select2-offscreen" id="parabirimiduzenle" name="parabirimiduzenle" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                    @foreach($parabirimleri as $parabirimi)
                                                        @if(Input::old('parabirimiduzenle')==$parabirimi->id )
                                                            <option data-id="{{$parabirimi->birimi}}" value="{{ $parabirimi->id }}" selected>{{ $parabirimi->adi }}</option>
                                                        @else
                                                            <option data-id="{{$parabirimi->birimi}}" value="{{ $parabirimi->id }}">{{ $parabirimi->adi }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green fiyatdegistir" data-dismiss="modal">Değiştir</button>
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
    <div class="modal fade" id="getir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Ücretlendirme Önizleme
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{ URL::to('ucretlendirme/ucretlendir') }}" id="form_sample_3" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Ücretlendirme Önizleme</h3>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Yeri:</label>
                                            <label class="col-xs-8 onizlemeyer" style="padding-top: 7px;margin-bottom:0"></label>
                                            <input class="hide" id="onizlemesecilenler" name="onizlemesecilenler"/>
                                            <input class="hide" id="onizlemeservis" name="onizlemeservis"/>
                                            <input class="hide" id="onizlemeuretimyer" name="onizlemeuretimyer"/>
                                            <input class="hide" id="onizlemegaranti" name="onizlemegaranti"/>
                                            <input class="hide" id="onizlemenetsiscari" name="onizlemenetsiscari"/>
                                            <input class="hide" id="onizlemeadet" name="onizlemeadet"/>
                                            <input class="hide" id="onizlemehatirlatmaid" name="onizlemehatirlatmaid" value="{{ Input::old('onizlemehatirlatmaid') ? Input::old('onizlemehatirlatmaid') : isset($hatirlatma_id) ? $hatirlatma_id : "" }}"/>
                                        </div>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_3">
                                                <thead>
                                                <tr>
                                                    <th class="hide">#</th>
                                                    <th>Seri No</th>
                                                    <th class="">Sayac Adı</th>
                                                    <th class="">Geliş Tarihi</th>
                                                    <th class="">Garanti</th>
                                                    <th class="">Fiyat Durumu</th>
                                                    <th class="">&nbsp;Fiyatı&nbsp;</th>
                                                    <th class="">İndirim</th>
                                                    <th class="">Kdvsiz Tutar</th>
                                                    <th class="">Kdv Tutarı</th>
                                                    <th class="">Toplam Tutar</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-6 col-xs-12 ">
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
                                                    <label class="col-xs-6 onizlemefiyat" style="padding-top: 9px">0.00</label>
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
                                                <input class="hide" id="onizlemefiyat" name="onizlemefiyat"/>
                                                <input class="hide" id="onizlemeindirimtutar" name="onizlemeindirimtutar"/>
                                                <input class="hide" id="onizlemekdvsiztutar" name="onizlemekdvsiztutar"/>
                                                <input class="hide" id="onizlemekdvtutar" name="onizlemekdvtutar"/>
                                                <input class="hide" id="onizlemetoplamtutar" name="onizlemetoplamtutar"/>
                                                <input class="hide" id="onizlemebirim" name="onizlemebirim"/>
                                                <input class="hide" id="onizlemefiyat2" name="onizlemefiyat2"/>
                                                <input class="hide" id="onizlemeindirimtutar2" name="onizlemeindirimtutar2"/>
                                                <input class="hide" id="onizlemekdvsiztutar2" name="onizlemekdvsiztutar2"/>
                                                <input class="hide" id="onizlemekdvtutar2" name="onizlemekdvtutar2"/>
                                                <input class="hide" id="onizlemetoplamtutar2" name="onizlemetoplamtutar2"/>
                                                <input class="hide" id="onizlemebirim2" name="onizlemebirim2"/>
                                                <input class="hide" id="onizlemekurtarih" name="onizlemekurtarih"/>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green ucretlendir" data-toggle="modal" data-target="#confirm">Ücretlendir</button>
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
    <div class="modal fade" id="subegonder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Depolararası Transfer
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{ URL::to('ucretlendirme/subegonder') }}" id="form_sample_4" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h4 class="form-section col-xs-12">Müşteri Bilgisi</h4>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Yeri:</label>
                                            <label class="col-xs-8 teslimyer" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-xs-8 teslimcariadi" style="padding-top: 9px"></label>
                                        </div>
                                        <h4 class="form-section col-xs-12">Sayaç Listesi</h4>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_4">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Seri No</th>
                                                    <th>Sayaç Adı</th>
                                                    <th>Geliş Tarihi</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-6">Seçilen Sayaç Sayısı:</label>
                                                <label class="col-xs-6 teslimadet" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <input class="hide" id="teslimsecilenler" name="teslimsecilenler"/>
                                                <input class="hide" id="teslimadet" name="teslimadet"/>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green teslimet" data-toggle="modal" data-target="#subeconfirm">Aktar</button>
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
    <div class="modal fade" id="topluucretlendir" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Toplu Fiyatlandırma Ekranı
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" target="frame" id="form_sample_5" class="form-horizontal" method="POST" novalidate="novalidate" enctype="multipart/form-data">
                                    <div class="form-body col-xs-12">
                                        <h3 class="form-section col-xs-12">Toplu Fiyat Seçme Ekranı</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-6">Kriter Tipi:</label>
                                            <div class="col-sm-8 col-xs-6">
                                                <select class="form-control select2me select2-offscreen kayittipi" id="kayittipi" name="kayittipi" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                    <option value="1">Cari Bilgisi</option>
                                                    <option value="2">Üretim Yeri</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-6">Kriter:</label>
                                            <div class="col-sm-8 col-xs-6">
                                                <select class="form-control select2me select2-offscreen kayitkriteri" id="kayitkriteri" name="kayitkriteri" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-6">Servis Raporu Id (Gerekliyse):</label>
                                            <div class="col-xs-8">
                                                <input type="text" id="kayitid"  name="kayitid" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-offset-3 col-xs-9">
                                                    <a id="kayitgetir" href="#" type="button" data-dismiss="modal" class="btn green kayitgetir">Bilgileri Getir</a>
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
    <div class="modal fade" id="toplugetir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Ücretlendirme Önizleme
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{ URL::to('ucretlendirme/topluucretlendir') }}" id="form_sample_6" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Ücretlendirme Önizleme</h3>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Yeri:</label>
                                            <label class="col-xs-8 topluonizlemeyer" style="padding-top: 7px;margin-bottom:0"></label>
                                            <input class="hide" id="topluonizlemesecilenler" name="topluonizlemesecilenler"/>
                                            <input class="hide" id="topluonizlemeservis" name="topluonizlemeservis"/>
                                            <input class="hide" id="topluonizlemeuretimyer" name="topluonizlemeuretimyer"/>
                                            <input class="hide" id="topluonizlemegaranti" name="topluonizlemegaranti"/>
                                            <input class="hide" id="topluonizlemenetsiscari" name="topluonizlemenetsiscari"/>
                                            <input class="hide" id="topluonizlemeadet" name="topluonizlemeadet"/>
                                        </div>
                                        <h4 class="form-section col-xs-12">Sayaç Listesi
                                            <span style="padding-left:100px"><button type="button" class="btn green tumunusec6">Tümünü Seç</button>
                                                <button type="button" class="btn red temizle6">Temizle</button></span>
                                        </h4>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_6">
                                                <thead>
                                                <tr>
                                                    <th class="">#</th>
                                                    <th class="">Seri No</th>
                                                    <th class="">Sayac Adı</th>
                                                    <th class="">Geliş Tarihi</th>
                                                    <th class="">Garanti</th>
                                                    <th class="">Fiyat Durumu</th>
                                                    <th class="">&nbsp;Fiyatı&nbsp;</th>
                                                    <th class="">İndirim</th>
                                                    <th class="">Kdvsiz Tutar</th>
                                                    <th class="">Kdv Tutarı</th>
                                                    <th class="">Toplam Tutar</th>
                                                    <th class="hide">Tutar</th>
                                                    <th class="hide">Para Birimi</th>
                                                    <th class="hide">Tutar 2</th>
                                                    <th class="hide">Para Birimi 2</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-6 col-xs-12 ">
                                                <div class="col-xs-12 topluonizlemekur">
                                                    <label class="col-xs-12 topluonizlemeeuro" style="padding-top: 9px;margin-left:5px;text-align:center">Euro : 0.0000 ₺</label>
                                                    <label class="col-xs-12 topluonizlemedolar" style="padding-top: 9px;margin-left:3px;text-align:center">Dolar : 0.0000 ₺</label>
                                                    <label class="col-xs-12 topluonizlemesterlin" style="padding-top: 9px;text-align:center">Sterlin : 0.0000 ₺</label>
                                                    <label class="control-label col-xs-12 topluonizlemewarning" style="text-align: center"></label>
                                                    <input id="topluonizlemeeuro" class="hide">
                                                    <input id="topluonizlemedolar" class="hide">
                                                    <input id="topluonizlemesterlin" class="hide">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TUTAR:</label>
                                                    <label class="col-xs-6 topluonizlemefiyat" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">İNDİRİM MİKTARI:</label>
                                                    <label class="col-xs-6 topluonizlemeindirimtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDVSİZ TUTAR:</label>
                                                    <label class="col-xs-6 topluonizlemekdvsiztutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDV TUTARI:</label>
                                                    <label class="col-xs-6 topluonizlemekdvtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TOPLAM TUTAR:</label>
                                                    <label class="col-xs-6 topluonizlemetoplamtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <input class="hide" id="topluonizlemefiyat" name="topluonizlemefiyat"/>
                                                <input class="hide" id="topluonizlemeindirimtutar" name="topluonizlemeindirimtutar"/>
                                                <input class="hide" id="topluonizlemekdvsiztutar" name="topluonizlemekdvsiztutar"/>
                                                <input class="hide" id="topluonizlemekdvtutar" name="topluonizlemekdvtutar"/>
                                                <input class="hide" id="topluonizlemetoplamtutar" name="topluonizlemetoplamtutar"/>
                                                <input class="hide" id="topluonizlemebirim" name="topluonizlemebirim"/>
                                                <input class="hide" id="topluonizlemefiyat2" name="topluonizlemefiyat2"/>
                                                <input class="hide" id="topluonizlemeindirimtutar2" name="topluonizlemeindirimtutar2"/>
                                                <input class="hide" id="topluonizlemekdvsiztutar2" name="topluonizlemekdvsiztutar2"/>
                                                <input class="hide" id="topluonizlemekdvtutar2" name="topluonizlemekdvtutar2"/>
                                                <input class="hide" id="topluonizlemetoplamtutar2" name="topluonizlemetoplamtutar2"/>
                                                <input class="hide" id="topluonizlemebirim2" name="topluonizlemebirim2"/>
                                                <input class="hide" id="topluonizlemekurtarih" name="topluonizlemekurtarih"/>
                                                <input class="hide" id="topluonizlemebirimi" name="topluonizlemebirimi"/>
                                                <input class="hide" id="topluonizlemebirimi2" name="topluonizlemebirimi2"/>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green topluucretlendir" data-toggle="modal" data-target="#topluconfirm">Ücretlendir</button>
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
                    <h4 class="modal-title">Fiyatlandırma Kaydedilecek</h4>
                </div>
                <div class="modal-body">
                    Seçilen Sayaçlara Ait Servis Fiyatları Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="topluconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Fiyatlandırma Kaydedilecek</h4>
                </div>
                <div class="modal-body">
                    Seçilen Sayaçlara Ait Servis Fiyatları Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formtoplusubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="updateconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Fiyatlandırma Güncellenecek</h4>
                </div>
                <div class="modal-body">
                    Seçilen Sayaça Ait Servis Fiyatı Güncellenecektir?
                </div>
                <div class="modal-footer">
                    <a id="formupdate" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="subeconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Sayaçlar Depolararası Olarak Depo Teslimine Aktarılacak?</h4>
                </div>
                <div class="modal-body">
                    Seçilen Sayaçlar Depolararası Olarak Depo Teslimine Aktarılacaktır?
                </div>
                <div class="modal-footer">
                    <a id="formsube" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
@stop
