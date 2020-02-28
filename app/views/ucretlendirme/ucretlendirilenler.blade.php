@extends('layout.master')

@section('page-title')
<!--suppress JSValidateTypes -->
<div class="page-title">
    <h1>Ücretlendirilen <small> Sayaçlar Ekranı</small></h1>
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
    <script src="{{ URL::to('pages/ucretlendirme/form-validation-2.js') }}"></script>
@stop

@section('scripts')
    <script>
        jQuery(document).ready(function() {
            Metronic.init(); // init metronic core componets
            Layout.init(); // init layout
            Demo.init(); // init demo features
            QuickSidebar.init(); // init quick sidebar
            FormValidationUcretlendirme.init();
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
                "url": "{{ URL::to('ucretlendirme/ucretlendirilenkayitlist') }}",
                "type": "POST",
                "data": {
                    "hatirlatma_id" : "@if(isset($hatirlatma_id)){{$hatirlatma_id}}@endif" ,
                    "netsiscari_id" : "@if(isset(Auth::user()->netsiscari_id) && count(Auth::user()->netsiscari_id)>0){{Auth::user()->netsiscarilist}}@endif"
                }
            },
            "bServerSide": true,
            "regex":false,
            "fnDrawCallback" : function() {
                $(document).on("click", ".delete", function () {
                    var id = $(this).data('id');
                    $(".modal-footer #sayacid").attr('href',"{{ URL::to('ucretlendirme/ucretlendirilensil') }}/"+id );
                });
                var secilen=$('#secilen').val();
                $("#sample_editable_1  tr .id").each(function(){
                    if(secilen===$(this).html()){
                        $(this).parents('tr').addClass("active");
                    }
                });
            },
            "aaSorting": [[9,'desc']],
            "columnDefs": [ { targets: [ 1 ], orderData: [ 1, 0 ] },
                { targets: [ 2 ], orderData: [ 2, 0 ] },
                { targets: [ 3 ], orderData: [ 3, 0 ] },
                { targets: [ 4 ], orderData: [ 4, 0 ] },
                { targets: [ 5 ], orderData: [ 5, 0 ] },
                { targets: [ 6 ], orderData: [ 6, 0 ] },
                { targets: [ 7 ], orderData: [ 7, 0 ] },
                { targets: [ 8 ], orderData: [ 8, 0 ] },
                { targets: [ 9 ], orderData: [ 9, 0 ] }
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
                {data: 'id', name: 'ucretlendirilen.id',"class":"id","orderable": true, "searchable": true },
                {data: 'cariadi', name: 'netsiscari.cariadi',"orderable": true, "searchable": false},
                {data: 'yeradi', name: 'uretimyer.yeradi',"orderable": true, "searchable": false},
                {data: 'servisadi', name: 'servis.servisadi',"class":"servis","orderable": true, "searchable": false},
                {data: 'sayacsayisi', name: 'ucretlendirilen.sayacsayisi',"orderable": true, "searchable": true},
                {data: 'gdurum', name: 'ucretlendirilen.gdurum',"class":"durum","orderable": true, "searchable": false},
                {data: 'fiyat', name: 'ucretlendirilen.fiyat',"orderable": true, "searchable": true},
                {data: 'gmail', name: 'ucretlendirilen.gmail',"orderable": true, "searchable": false},
                {data: 'adi_soyadi', name: 'kullanici.adi_soyadi',"orderable": true, "searchable": false},
                {data: 'durumtarihi', name: 'ucretlendirilen.durumtarihi',"class":"tarih","orderable": true, "searchable": false},
                {data: 'gdurumtarihi', name: 'ucretlendirilen.gdurumtarihi',"class":"tarih","visible": false, "searchable": true},
                {data: 'ncariadi', name: 'netsiscari.ncariadi',"visible": false, "searchable": true},
                {data: 'nyeradi', name: 'uretimyer.nyeradi',"visible": false, "searchable": true},
                {data: 'nservisadi', name: 'servis.nservisadi',"visible": false, "searchable": true},
                {data: 'ndurum', name: 'ucretlendirilen.ndurum',"visible": false, "searchable": true},
                {data: 'nmail', name: 'ucretlendirilen.nmail',"visible": false, "searchable": true},
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
            '<option value="">Tamamı</option>' +
            '<option value="0">Id</option>' +
            '<option value="11">Cari Adı</option>' +
            '<option value="12">Üretim Yeri</option>' +
            '<option value="13">Servis Adı</option>' +
            '<option value="4">Adet</option>' +
            '<option value="14">Durum</option>' +
            '<option value="6">Fiyat</option>' +
            '<option value="15">Mail</option>' +
            '<option value="16">Kullanıcı</option>' +
            '<option value="10">Tarih</option>' +
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
                var servis = "";
                var kullaniciservis = $('#servis').val();
                var durum;
                if ($(this).hasClass('active')) {
                    $("tbody tr").removeClass("active");
                    $(this).addClass("active");
                    secilen = oTable.cell($(this).children('.id')).data();
                    servis = oTable.cell($(this).children('.servis')).data();
                    $('#secilen').val(secilen);
                } else {
                    $(this).removeClass("active");
                    $('#secilen').val("");
                    bos=1;
                }
                if(bos)
                {
                    $('.telefononay').addClass("hide");
                    $('.mailonay').addClass("hide");
                    $('.gerigonder').addClass("hide");
                    $('.garantigonder').addClass("hide");
                    $('.yetkilionay').addClass("hide");
                    $('.yetkilired').addClass("hide");
                    $('.subeyetkilionay').addClass("hide");
                    $('.subeyetkilired').addClass("hide");
                    $('.subeaktar').addClass("hide");
                    $('.mailtekrar').addClass("hide");
                    $('.mailtelefon').addClass("hide");
                }else{
                    @if(Auth::user()->grup_id<16 && Auth::user()->grup_id!=6)
                    $('.yetkilionay').addClass("hide");
                    $('.yetkilired').addClass("hide");
                    $('.subeyetkilionay').addClass("hide");
                    $('.subeyetkilired').addClass("hide");
                    $('.subeaktar').addClass("hide");
                    durum = $(this).children('.durum').text();
                    var gondermetarihi=oTable.cell( $(this).children('.tarih')).data();
                    gondermetarihi=gondermetarihi.substr(6,4)+'-'+gondermetarihi.substr(3,2)+'-'+gondermetarihi.substr(0,2);
                    var date2=new Date();
                    var date1=new Date(gondermetarihi);
                    var diff = new Date(date2-date1);
                    var date_reaming = diff/1000/60/60/24;
                    if(durum==='Bekliyor'){
                        $('.telefononay').removeClass("hide");
                        $('.mailonay').removeClass("hide");
                        $('.garantigonder').addClass("hide");
                        $('.mailtekrar').addClass("hide");
                        $('.mailtelefon').addClass("hide");
                        if(date_reaming>7){
                            $('.gerigonder').removeClass("hide");
                        }else{
                            $('.gerigonder').addClass("hide");
                        }
                    }else if(durum==='Reddedildi'){
                        $('.telefononay').addClass("hide");
                        $('.mailonay').addClass("hide");
                        $('.gerigonder').removeClass("hide");
                        $('.garantigonder').removeClass("hide");
                        $('.mailtekrar').addClass("hide");
                        $('.mailtelefon').addClass("hide");
                    }else if(durum==='Gönderildi'){
                        $('.telefononay').addClass("hide");
                        $('.mailonay').addClass("hide");
                        $('.garantigonder').addClass("hide");
                        $('.mailtekrar').removeClass("hide");
                        $('.mailtelefon').removeClass("hide");
                        if(date_reaming>7){
                            $('.gerigonder').removeClass("hide");
                        }else{
                            $('.gerigonder').addClass("hide");
                        }
                    }else{
                        $('.telefononay').addClass("hide");
                        $('.mailonay').addClass("hide");
                        $('.gerigonder').addClass("hide");
                        $('.garantigonder').addClass("hide");
                        $('.mailtekrar').addClass("hide");
                        $('.mailtelefon').addClass("hide");
                    }
                    @elseif(Auth::user()->grup_id==6)
                    $('.telefononay').addClass("hide");
                    $('.mailonay').addClass("hide");
                    $('.gerigonder').addClass("hide");
                    $('.garantigonder').addClass("hide");
                    $('.mailtekrar').addClass("hide");
                    $('.mailtelefon').addClass("hide");
                    $('.subeyetkilionay').addClass("hide");
                    $('.subeyetkilired').addClass("hide");
                    $('.subeaktar').addClass("hide");
                    durum = $(this).children('.durum').text();
                    if(durum==='Bekliyor'){
                        $('.yetkilionay').removeClass("hide");
                        $('.yetkilired').addClass("hide");
                    }else if(durum==='Gönderildi'){
                        $('.yetkilionay').removeClass("hide");
                        $('.yetkilired').removeClass("hide");
                    }else{
                        $('.yetkilionay').addClass("hide");
                        $('.yetkilired').addClass("hide");
                    }
                    @elseif(Auth::user()->grup_id==17)
                    $('.telefononay').addClass("hide");
                    $('.mailonay').addClass("hide");
                    $('.gerigonder').addClass("hide");
                    $('.garantigonder').addClass("hide");
                    $('.mailtekrar').addClass("hide");
                    $('.mailtelefon').addClass("hide");
                    $('.yetkilionay').addClass("hide");
                    $('.yetkilired').addClass("hide");
                    durum = $(this).children('.durum').text();
                    if(kullaniciservis!==servis){
                        if(durum==='Bekliyor'){
                            $('.subeyetkilionay').removeClass("hide");
                            $('.subeyetkilired').addClass("hide");
                        }else if(durum==='Gönderildi'){
                            $('.subeyetkilionay').removeClass("hide");
                            $('.subeyetkilired').removeClass("hide");
                        }else{
                            $('.subeyetkilionay').addClass("hide");
                            $('.subeyetkilired').addClass("hide");
                        }
                        $('.subeaktar').addClass("hide");
                    }else{
                        if(durum==='Bekliyor'){
                            $('.subeaktar').removeClass("hide");
                        }else{
                            $('.subeaktar').addClass("hide");
                        }
                        $('.subeyetkilionay').addClass("hide");
                        $('.subeyetkilired').addClass("hide");
                    }
                    @else
                    $('.telefononay').addClass("hide");
                    $('.mailonay').addClass("hide");
                    $('.gerigonder').addClass("hide");
                    $('.garantigonder').addClass("hide");
                    $('.yetkilionay').addClass("hide");
                    $('.yetkilired').addClass("hide");
                    $('.subeyetkilionay').addClass("hide");
                    $('.subeyetkilired').addClass("hide");
                    $('.subeaktar').addClass("hide");
                    $('.mailtekrar').addClass("hide");
                    $('.mailtelefon').addClass("hide");
                    @endif
                }
            }
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        var table2 = $('#sample_editable_2');
        var oTable2 = table2.DataTable({
            "sPaginationType": "simple_numbers",
            "searching": true,
            "ordering": false,
            "bProcessing": false,
            "sAjaxSource": "",
            "fnDrawCallback" : function() {
                $(document).on("click", ".fiyatsil", function () {
                    var id = $(this).data('id');
                    $(".modal-footer #fiyatid").attr('href',"{{ URL::to('ucretlendirme/ucretlendirilenkayitsil') }}/"+id );
                });
            },
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
            "aoColumns": [{"sClass":"id"},null,null,null,null,null,null,null,null,null,null],
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
            "searching": true,
            "ordering": false,
            "bProcessing": false,
            "sAjaxSource": "",
            "bServerSide": false,
            "bInfo": true,
            "bPaginate": true,
            "bAutoWidth": false,
            "iDisplayLength": 5,
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
            "aoColumns": [{"sClass":"id","width": "6%"},
                {"width": "8%"},{"width": "24%"},{"width": "7%"},{"width": "7%"},{"width": "8%"},{"width": "8%"},{"width": "8%"},
                {"width": "8%"},{"width": "8%"},{"width": "8%"}],
            "lengthMenu": [
                [5, 10, 20, 99999999],
                [5, 10, 20, "Hepsi"]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_3_wrapper');
        table3.on('click', 'tr', function () {
            if(oTable3.cell( $(this).children('.id')).data()!==undefined){
                $(this).toggleClass("active");
                var adet=parseInt($('#geriadet').val());
                var secilenler=$('#gerisecilenler').val();
                var secilenlist;
                if($(this).hasClass('active'))
                {
                    secilenler+=(secilenler==="" ? "" : ",")+oTable3.cell( $(this).children('.id')).data();
                    adet++;
                    $('#gerisecilenler').val(secilenler);
                    $('#geriadet').val(adet);
                }else{
                    var secilen=oTable3.cell( $(this).children('.id')).data();
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
                    $('#gerisecilenler').val(yenilist);
                    $('#geriadet').val(adet);
                }
                secilenlist=secilenler.split(',');
                oTable3.rows().every( function () {
                    var data = this.data();
                    var id=data[0];
                    var durum=0;
                    $.each(secilenlist,function(index){
                        if(id===secilenlist[index])
                            durum=1;
                    });
                });
                if(adet>0){
                    $('.gerionayla').removeClass('hide');
                }else{
                    $('.gerionayla').addClass('hide');
                }
                $('#gerisecilenler').val(secilenler);
            }
        });
        $(document).on("click", ".temizle3", function () {
            var adet=parseInt($('#geriadet').val());
            var secilenler=$('#gerisecilenler').val();
            $("#sample_editable_3 tbody tr .id").each(function(){
                var secilen=$(this).html();
                $(this).parents('tr').removeClass("active");
                var secilenlist=secilenler.split(',');
                var yenilist="";
                $.each(secilenlist,function(index){
                    if(secilenlist[index]!==secilen)
                    {
                        yenilist+=(yenilist==="" ? "" : ",")+secilenlist[index];
                    }else{
                        adet--;
                    }
                });
                secilenler=yenilist;
            });
            $('#gerisecilenler').val(secilenler);
            $('#geriadet').val(adet);
            if(adet>0){
                $('.gerionayla').removeClass('hide');
            }else{
                $('.gerionayla').addClass('hide');
            }
        });
        $(document).on("click", ".tumunusec3", function () {
            var adet=parseInt($('#geriadet').val());
            var secilenler=$('#gerisecilenler').val();
            $("#sample_editable_3 tbody tr .id").each(function(){
                var secilen=$(this).html();
                $(this).parents('tr').addClass("active");
                var secilenlist=secilenler.split(',');
                var flag=0;
                $.each(secilenlist,function(index){
                    if(secilenlist[index]===secilen)
                    {
                        flag=1;
                    }
                });
                if(flag===0){
                    secilenler+=(secilenler==="" ? "" : ",")+secilen;
                    adet++;
                }
            });
            $('#gerisecilenler').val(secilenler);
            $('#geriadet').val(adet);
            if(adet>0){
                $('.gerionayla').removeClass('hide');
            }else{
                $('.gerionayla').addClass('hide');
            }
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        var table4 = $('#sample_editable_4');
        var oTable4 = table4.DataTable({
            "sPaginationType": "simple_numbers",
            "searching": true,
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
            "aoColumns": [{"width": "5%"},{"width": "8%"},{"width": "22%"},{"width": "7%"},{"width": "8%"},{"width": "8%"},
                {"width": "8%"},{"width": "8%"},{"width": "8%"},{"width": "10%"},{"width": "8%"}],
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
            "searching": true,
            "ordering": false,
            "bProcessing": false,
            "sAjaxSource": "",
            "bServerSide": false,
            "bInfo": true,
            "bPaginate": true,
            "bAutoWidth": false,
            "iDisplayLength": 5,
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
            "aoColumns": [{"sClass":"id","width": "6%"},
                {"width": "8%"},{"width": "24%"},{"width": "7%"},{"width": "7%"},{"width": "8%"},{"width": "8%"},{"width": "8%"},
                {"width": "8%"},{"width": "8%"},{"width": "8%"}],
            "lengthMenu": [
                [5, 10, 20, 99999999],
                [5, 10, 20, "Hepsi"]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_5_wrapper');
        table5.on('click', 'tr', function () {
            if(oTable5.cell( $(this).children('.id')).data()!==undefined){
                $(this).toggleClass("active");
                var adet=parseInt($('#garantiadet').val());
                var secilenler=$('#garantisecilenler').val();
                if($(this).hasClass('active'))
                {
                    secilenler+=(secilenler==="" ? "" : ",")+oTable5.cell( $(this).children('.id')).data();
                    adet++;
                    $('#garantisecilenler').val(secilenler);
                    $('#garantiadet').val(adet);
                }else{
                    var secilen=oTable5.cell( $(this).children('.id')).data();
                    var secilenlist=secilenler.split(',');
                    var yenilist="";
                    $.each(secilenlist,function(index){
                        if(secilenlist[index]!==secilen)
                        {
                            yenilist+=(yenilist==="" ? "" : ",")+secilenlist[index];
                        }
                    });
                    adet--;
                    secilenler=yenilist;
                    $('#garantisecilenler').val(yenilist);
                    $('#garantiadet').val(adet);
                }
                if(adet>0){
                    $('.garantionayla').removeClass('hide');
                }else{
                    $('.garantionayla').addClass('hide');
                }
                $('#garantisecilenler').val(secilenler);
            }
        });
        $(document).on("click", ".temizle5", function () {
            var adet=parseInt($('#garantiadet').val());
            var secilenler=$('#garantisecilenler').val();
            $("#sample_editable_5 tbody tr .id").each(function(){
                var secilen=$(this).html();
                $(this).parents('tr').removeClass("active");
                var secilenlist=secilenler.split(',');
                var yenilist="";
                $.each(secilenlist,function(index){
                    if(secilenlist[index]!==secilen)
                    {
                        yenilist+=(yenilist==="" ? "" : ",")+secilenlist[index];
                    }else{
                        adet--;
                    }
                });
                secilenler=yenilist;
            });
            $('#garantisecilenler').val(secilenler);
            $('#garantiadet').val(adet);
            if(adet>0){
                $('.garantionayla').removeClass('hide');
            }else{
                $('.garantionayla').addClass('hide');
            }
        });
        $(document).on("click", ".tumunusec5", function () {
            var adet=parseInt($('#garantiadet').val());
            var secilenler=$('#garantisecilenler').val();
            $("#sample_editable_5 tbody tr .id").each(function(){
                var secilen=$(this).html();
                $(this).parents('tr').addClass("active");
                var secilenlist=secilenler.split(',');
                var flag=0;
                $.each(secilenlist,function(index){
                    if(secilenlist[index]===secilen)
                    {
                        flag=1;
                    }
                });
                if(flag===0){
                    secilenler+=(secilenler==="" ? "" : ",")+secilen;
                    adet++;
                }
            });
            $('#garantisecilenler').val(secilenler);
            $('#garantiadet').val(adet);
            if(adet>0){
                $('.garantionayla').removeClass('hide');
            }else{
                $('.garantionayla').addClass('hide');
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
        var table7 = $('#sample_editable_7');
        var oTable7 = table7.DataTable({
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
            "aoColumns": [{"sClass":"id"},null,null,{"sClass":"garantitel"},{"sClass":"fiyatdurumtel"},
                {"sClass":"tutartel"},{"sClass":"indirimoranitel"},{"sClass":"kdvsiztutartel"},{"sClass":"kdvtutartel"},{"sClass":"toplamtutartel"}
                ,null,null,null,null],
            "lengthMenu": [
                [5, 10, 20, 99999999],
                [5, 10, 20, "Hepsi"]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_7_wrapper');
        table7.on('click', 'tr', function () {
            if(oTable7.cell( $(this).children('.id')).data()!==undefined){
                $(this).toggleClass("active");
                var adet=parseInt($('#teladet').val());
                var secilenler=$('#telsecilenler').val();
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
                var dolar = $('#teldolar').val();
                var euro = $('#teleuro').val();
                var sterlin = $('#telsterlin').val();
                var parabirimi = $('#telbirimi').val();
                var parabirimi2 =$('#telbirimi2').val();
                var genelgaranti = "1";
                var secilenlist;
                var birim,birim2,fiyat,fiyat2,indirim,kdvsiztutar,kdv,toplamtutar,indirim2,kdvsiztutar2,kdv2,toplamtutar2;
                if($(this).hasClass('active'))
                {
                    secilenler+=(secilenler==="" ? "" : ",")+oTable7.cell( $(this).children('.id')).data();
                    adet++;
                    $('#telsecilenler').val(secilenler);
                    $('#teladet').val(adet);
                }else{
                    var secilen=oTable7.cell( $(this).children('.id')).data();
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
                    $('#telsecilenler').val(yenilist);
                    $('#teladet').val(adet);
                }
                secilenlist=secilenler.split(',');
                oTable7.rows().every( function () {
                    var data = this.data();
                    var id=data[0];
                    var durum=0;
                    $.each(secilenlist,function(index){
                        if(id===secilenlist[index])
                            durum=1;
                    });
                    var garanti = data[3];
                    var indirimorani = parseFloat(data[6]);
                    fiyat = parseFloat(data[10]);
                    fiyat2 = parseFloat(data[12]);
                    birim = data[11];
                    birim2 = data[13];
                    if(durum===1){
                        if (birim === parabirimi) {
                            if (birim2 !== "") {
                                if (birim2 !== parabirimi2) {
                                    fiyat2 = 0;
                                    $('.telwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.telonayla').prop('disabled', true);
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
                                    $('.telwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.telonayla').prop('disabled', true);
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
                    $('.teltutar').text(genelfiyat.toFixed(2)+' '+parabirimi);
                    $('.telindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi);
                    $('.telkdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi);
                    $('.telkdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi);
                    $('.teltoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi);
                }else{
                    if(geneltoplamtutar === 0){
                        $('.teltutar').text(genelfiyat2.toFixed(2)+' '+parabirimi2);
                        $('.telindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2);
                        $('.telkdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2);
                        $('.telkdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2);
                        $('.teltoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2);
                    }else{
                        $('.teltutar').text(genelfiyat.toFixed(2)+' '+parabirimi+' + '+genelfiyat2.toFixed(2)+' '+parabirimi2);
                        $('.telindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi+' + '+genelindirim2.toFixed(2)+' '+parabirimi2);
                        $('.telkdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi+' + '+genelkdvsiztutar2.toFixed(2)+' '+parabirimi2);
                        $('.telkdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi+' + '+genelkdvtutar2.toFixed(2)+' '+parabirimi2);
                        $('.teltoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2);
                    }
                }
                $('#teltutar').val(genelfiyat.toFixed(2));
                $('#telindirimtutar').val(genelindirim.toFixed(2));
                $('#telkdvsiztutar').val(genelkdvsiztutar.toFixed(2));
                $('#telkdvtutar').val(genelkdvtutar.toFixed(2));
                $('#teltoplamtutar').val(geneltoplamtutar.toFixed(2));
                $('#teltutar2').val(genelfiyat2.toFixed(2));
                $('#telindirimtutar2').val(genelindirim2.toFixed(2));
                $('#telkdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
                $('#telkdvtutar2').val(genelkdvtutar2.toFixed(2));
                $('#teltoplamtutar2').val(geneltoplamtutar2.toFixed(2));
                if(adet>0){
                    $('.telonayla').removeClass('hide');
                }else{
                    $('.telonayla').addClass('hide');
                }
                $('#telsecilenler').val(secilenler);
                $('#telgaranti').val(genelgaranti);
            }
        });
        $(document).on("click", ".temizle7", function () {
            var adet = parseInt($('#teladet').val());
            var secilenler = $('#telsecilenler').val();
            $("#sample_editable_7 tbody tr .id").each(function () {
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
            $('#telsecilenler').val(secilenler);
            $('#teladet').val(adet);
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
            var dolar = $('#teldolar').val();
            var euro = $('#teleuro').val();
            var sterlin = $('#telsterlin').val();
            var parabirimi = $('#telbirimi').val();
            var parabirimi2 = $('#telbirimi2').val();
            var genelgaranti = "1";
            var secilenlist = secilenler.split(',');
            var birim, birim2, fiyat, fiyat2, indirim, kdvsiztutar, kdv, toplamtutar, indirim2, kdvsiztutar2, kdv2, toplamtutar2;
            oTable7.rows().every(function () {
                var data = this.data();
                var id = data[0];
                var durum = 0;
                $.each(secilenlist, function (index) {
                    if (id === secilenlist[index])
                        durum = 1;
                });
                var garanti = data[3];
                var indirimorani = parseFloat(data[6]);
                fiyat = parseFloat(data[10]);
                fiyat2 = parseFloat(data[12]);
                birim = data[11];
                birim2 = data[13];
                if (durum === 1) {
                    if (birim === parabirimi) {
                        if (birim2 !== "") {
                            if (birim2 !== parabirimi2) {
                                fiyat2 = 0;
                                $('.telwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.telonayla').prop('disabled', true);
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
                                $('.telwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.telonayla').prop('disabled', true);
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
                $('.teltutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi);
                $('.telindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi);
                $('.telkdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi);
                $('.telkdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi);
                $('.teltoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi);
            } else {
                if(geneltoplamtutar === 0) {
                    $('.teltutar').text(genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.telindirimtutar').text(genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.telkdvsiztutar').text(genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.telkdvtutar').text(genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.teltoplamtutar').text(geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }else{
                    $('.teltutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi + ' + ' + genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.telindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi + ' + ' + genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.telkdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.telkdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.teltoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi + ' + ' + geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }
            }
            $('#teltutar').val(genelfiyat.toFixed(2));
            $('#telindirimtutar').val(genelindirim.toFixed(2));
            $('#telkdvsiztutar').val(genelkdvsiztutar.toFixed(2));
            $('#telkdvtutar').val(genelkdvtutar.toFixed(2));
            $('#teltoplamtutar').val(geneltoplamtutar.toFixed(2));
            $('#teltutar2').val(genelfiyat2.toFixed(2));
            $('#telindirimtutar2').val(genelindirim2.toFixed(2));
            $('#telkdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
            $('#telkdvtutar2').val(genelkdvtutar2.toFixed(2));
            $('#teltoplamtutar2').val(geneltoplamtutar2.toFixed(2));
            if (adet > 0) {
                $('.telonayla').removeClass('hide');
            } else {
                $('.telonayla').addClass('hide');
            }
            $('#telgaranti').val(genelgaranti);
        } );
        $(document).on("click", ".tumunusec7", function () {
            var adet=parseInt($('#teladet').val());
            var secilenler=$('#telsecilenler').val();
            $("#sample_editable_7 tbody tr .id").each(function(){
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
            $('#telsecilenler').val(secilenler);
            $('#teladet').val(adet);
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
            var dolar = $('#teldolar').val();
            var euro = $('#teleuro').val();
            var sterlin = $('#telsterlin').val();
            var parabirimi = $('#telbirimi').val();
            var parabirimi2 = $('#telbirimi2').val();
            var genelgaranti = "1";
            var secilenlist = secilenler.split(',');
            var birim, birim2, fiyat, fiyat2, indirim, kdvsiztutar, kdv, toplamtutar, indirim2, kdvsiztutar2, kdv2, toplamtutar2;
            oTable7.rows().every(function () {
                var data = this.data();
                var id = data[0];
                var durum = 0;
                $.each(secilenlist, function (index) {
                    if (id === secilenlist[index])
                        durum = 1;
                });
                var garanti = data[3];
                var indirimorani = parseFloat(data[6]);
                fiyat = parseFloat(data[10]);
                fiyat2 = parseFloat(data[12]);
                birim = data[11];
                birim2 = data[13];
                if (durum === 1) {
                    if (birim === parabirimi) {
                        if (birim2 !== "") {
                            if (birim2 !== parabirimi2) {
                                fiyat2 = 0;
                                $('.telwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.telonayla').prop('disabled', true);
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
                                $('.telwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.telonayla').prop('disabled', true);
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
                $('.teltutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi);
                $('.telindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi);
                $('.telkdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi);
                $('.telkdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi);
                $('.teltoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi);
            } else {
                if(geneltoplamtutar === 0){
                    $('.teltutar').text(genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.telindirimtutar').text(genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.telkdvsiztutar').text(genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.telkdvtutar').text(genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.teltoplamtutar').text(geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }else{
                    $('.teltutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi + ' + ' + genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.telindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi + ' + ' + genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.telkdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.telkdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.teltoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi + ' + ' + geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }
            }
            $('#teltutar').val(genelfiyat.toFixed(2));
            $('#telindirimtutar').val(genelindirim.toFixed(2));
            $('#telkdvsiztutar').val(genelkdvsiztutar.toFixed(2));
            $('#telkdvtutar').val(genelkdvtutar.toFixed(2));
            $('#teltoplamtutar').val(geneltoplamtutar.toFixed(2));
            $('#teltutar2').val(genelfiyat2.toFixed(2));
            $('#telindirimtutar2').val(genelindirim2.toFixed(2));
            $('#telkdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
            $('#telkdvtutar2').val(genelkdvtutar2.toFixed(2));
            $('#teltoplamtutar2').val(geneltoplamtutar2.toFixed(2));
            if (adet > 0) {
                $('.telonayla').removeClass('hide');
            } else {
                $('.telonayla').addClass('hide');
            }
            $('#telgaranti').val(genelgaranti);
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        var table8 = $('#sample_editable_8');
        var oTable8 = table8.DataTable({
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
            "aoColumns": [{"sClass":"id"},null,null,{"sClass":"garantimail"},{"sClass":"fiyatdurummail"},
                {"sClass":"tutarmail"},{"sClass":"indirimoranimail"},{"sClass":"kdvsiztutarmail"},{"sClass":"kdvtutarmail"},{"sClass":"toplamtutarmail"},
                null,null,null,null],
            "lengthMenu": [
                [5, 10, 20, 99999999],
                [5, 10, 20, "Hepsi"]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_8_wrapper');
        table8.on('click', 'tr', function () {
            if(oTable8.cell( $(this).children('.id')).data()!==undefined){
                $(this).toggleClass("active");
                var adet=parseInt($('#mailadet').val());
                var secilenler=$('#mailsecilenler').val();
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
                var dolar = $('#maildolar').val();
                var euro = $('#maileuro').val();
                var sterlin = $('#mailsterlin').val();
                var parabirimi = $('#mailbirimi').val();
                var parabirimi2 =$('#mailbirimi2').val();
                var genelgaranti = "1";
                var secilenlist;
                var birim,birim2,fiyat,fiyat2,indirim,kdvsiztutar,kdv,toplamtutar,indirim2,kdvsiztutar2,kdv2,toplamtutar2;
                if($(this).hasClass('active'))
                {
                    secilenler+=(secilenler==="" ? "" : ",")+oTable8.cell( $(this).children('.id')).data();
                    adet++;
                    $('#mailsecilenler').val(secilenler);
                    $('#mailadet').val(adet);
                }else{
                    var secilen=oTable8.cell( $(this).children('.id')).data();
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
                    $('#mailsecilenler').val(yenilist);
                    $('#mailadet').val(adet);
                }
                secilenlist=secilenler.split(',');
                oTable8.rows().every( function () {
                    var data = this.data();
                    var id=data[0];
                    var durum=0;
                    $.each(secilenlist,function(index){
                        if(id===secilenlist[index])
                            durum=1;
                    });
                    var garanti = data[3];
                    var indirimorani = parseFloat(data[6]);
                    fiyat = parseFloat(data[10]);
                    fiyat2 = parseFloat(data[12]);
                    birim = data[11];
                    birim2 = data[13];
                    if(durum===1){
                        if (birim === parabirimi) {
                            if (birim2 !== "") {
                                if (birim2 !== parabirimi2) {
                                    fiyat2 = 0;
                                    $('.mailwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.mailonayla').prop('disabled', true);
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
                                    $('.mailwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.mailonayla').prop('disabled', true);
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
                if(geneltoplamtutar2===0){
                    $('.mailtutar').text(genelfiyat.toFixed(2)+' '+parabirimi);
                    $('.mailindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi);
                    $('.mailkdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi);
                    $('.mailkdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi);
                    $('.mailtoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi);
                }else{
                    if(geneltoplamtutar === 0){
                        $('.mailtutar').text(genelfiyat2.toFixed(2)+' '+parabirimi2);
                        $('.mailindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2);
                        $('.mailkdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2);
                        $('.mailkdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2);
                        $('.mailtoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2);
                    }else{
                        $('.mailtutar').text(genelfiyat.toFixed(2)+' '+parabirimi+' + '+genelfiyat2.toFixed(2)+' '+parabirimi2);
                        $('.mailindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi+' + '+genelindirim2.toFixed(2)+' '+parabirimi2);
                        $('.mailkdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi+' + '+genelkdvsiztutar2.toFixed(2)+' '+parabirimi2);
                        $('.mailkdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi+' + '+genelkdvtutar2.toFixed(2)+' '+parabirimi2);
                        $('.mailtoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2);
                    }
                }
                $('#mailtutar').val(genelfiyat.toFixed(2));
                $('#mailindirimtutar').val(genelindirim.toFixed(2));
                $('#mailkdvsiztutar').val(genelkdvsiztutar.toFixed(2));
                $('#mailkdvtutar').val(genelkdvtutar.toFixed(2));
                $('#mailtoplamtutar').val(geneltoplamtutar.toFixed(2));
                $('#mailtutar2').val(genelfiyat2.toFixed(2));
                $('#mailindirimtutar2').val(genelindirim2.toFixed(2));
                $('#mailkdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
                $('#mailkdvtutar2').val(genelkdvtutar2.toFixed(2));
                $('#mailtoplamtutar2').val(geneltoplamtutar2.toFixed(2));
                if(adet>0){
                    $('#maillink').removeClass('hide');
                    $('#mailek1').removeClass('hide');
                    $('#mailek2').removeClass('hide');
                    $('#caribilgi').removeClass('hide');
                    $('.mailonayla').removeClass('hide');
                }else{
                    $('#maillink').addClass('hide');
                    $('#mailek1').addClass('hide');
                    $('#mailek2').addClass('hide');
                    $('#caribilgi').addClass('hide');
                    $('.mailonayla').addClass('hide');
                }
                $('#mailsecilenler').val(secilenler);
                var id = $('#secilen').val();
                var root= $('.root').val();
                var serverdis = $('.mailroot').val();
                var onaysayfa = root+'/musterionay/'+id;
                var onaysayfadis = serverdis+'/musterionay/'+id;
                var fiyatlandirma = root+'/ucretlendirme/fiyatlandirmatablo/'+id+'/'+secilenler;
                var onayform = root+'/ucretlendirme/onayform/'+id+'/'+secilenler;
                $('#maillink').prop('href',onaysayfa);
                $('.maillink').val(onaysayfadis);
                $('#mailek1').prop('href',fiyatlandirma);
                $('#mailek2').prop('href',onayform);
                $('#mailgaranti').val(genelgaranti);
            }
        });
        $(document).on("click", ".temizle8", function () {
            var adet = parseInt($('#mailadet').val());
            var secilenler = $('#mailsecilenler').val();
            $("#sample_editable_8 tbody tr .id").each(function () {
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
            $('#mailsecilenler').val(secilenler);
            $('#mailadet').val(adet);
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
            var dolar = $('#maildolar').val();
            var euro = $('#maileuro').val();
            var sterlin = $('#mailsterlin').val();
            var parabirimi = $('#telbirimi').val();
            var parabirimi2 = $('#telbirimi2').val();
            var genelgaranti = "1";
            var secilenlist = secilenler.split(',');
            var birim, birim2, fiyat, fiyat2, indirim, kdvsiztutar, kdv, toplamtutar, indirim2, kdvsiztutar2, kdv2, toplamtutar2;
            oTable8.rows().every(function () {
                var data = this.data();
                var id = data[0];
                var durum = 0;
                $.each(secilenlist, function (index) {
                    if (id === secilenlist[index])
                        durum = 1;
                });
                var garanti = data[3];
                var indirimorani = parseFloat(data[6]);
                fiyat = parseFloat(data[10]);
                fiyat2 = parseFloat(data[12]);
                birim = data[11];
                birim2 = data[13];
                if (durum === 1) {
                    if (birim === parabirimi) {
                        if (birim2 !== "") {
                            if (birim2 !== parabirimi2) {
                                fiyat2 = 0;
                                $('.mailwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.mailonayla').prop('disabled', true);
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
                                $('.mailwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.mailonayla').prop('disabled', true);
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
                $('.mailtutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi);
                $('.mailindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi);
                $('.mailkdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi);
                $('.mailkdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi);
                $('.mailtoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi);
            } else {
                if(geneltoplamtutar === 0){
                    $('.mailtutar').text(genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.mailindirimtutar').text(genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.mailkdvsiztutar').text(genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.mailkdvtutar').text(genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.mailtoplamtutar').text(geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }else{
                    $('.mailtutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi + ' + ' + genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.mailindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi + ' + ' + genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.mailkdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.mailkdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.mailtoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi + ' + ' + geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }
            }
            $('#mailtutar').val(genelfiyat.toFixed(2));
            $('#mailindirimtutar').val(genelindirim.toFixed(2));
            $('#mailkdvsiztutar').val(genelkdvsiztutar.toFixed(2));
            $('#mailkdvtutar').val(genelkdvtutar.toFixed(2));
            $('#mailtoplamtutar').val(geneltoplamtutar.toFixed(2));
            $('#mailtutar2').val(genelfiyat2.toFixed(2));
            $('#mailindirimtutar2').val(genelindirim2.toFixed(2));
            $('#mailkdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
            $('#mailkdvtutar2').val(genelkdvtutar2.toFixed(2));
            $('#mailtoplamtutar2').val(geneltoplamtutar2.toFixed(2));
            if (adet > 0) {
                $('#maillink').removeClass('hide');
                $('#mailek1').removeClass('hide');
                $('#mailek2').removeClass('hide');
                $('#caribilgi').removeClass('hide');
                $('.mailonayla').removeClass('hide');
            } else {
                $('#maillink').addClass('hide');
                $('#mailek1').addClass('hide');
                $('#mailek2').addClass('hide');
                $('#caribilgi').addClass('hide');
                $('.mailonayla').addClass('hide');
            }
            var id = $('#secilen').val();
            var root = $('.root').val();
            var serverdis = $('.mailroot').val();
            var onaysayfa = root + '/musterionay/' + id;
            var onaysayfadis = serverdis + '/musterionay/' + id;
            var fiyatlandirma = root + '/ucretlendirme/fiyatlandirmatablo/' + id + '/' + secilenler;
            var onayform = root + '/ucretlendirme/onayform/' + id + '/' + secilenler;
            $('#maillink').prop('href', onaysayfa);
            $('.maillink').val(onaysayfadis);
            $('#mailek1').prop('href', fiyatlandirma);
            $('#mailek2').prop('href', onayform);
            $('#mailgaranti').val(genelgaranti);
        });
        $(document).on("click", ".tumunusec8", function () {
            var adet=parseInt($('#mailadet').val());
            var secilenler=$('#mailsecilenler').val();
            $("#sample_editable_8 tbody tr .id").each(function(){
                var secilen=$(this).html();
                $(this).parents('tr').addClass("active");
                var secilenlist=secilenler.split(',');
                var flag=0;
                $.each(secilenlist,function(index){
                    if(secilenlist[index]===secilen)
                    {
                        flag=1;
                    }
                });
                if(flag===0){
                    secilenler+=(secilenler==="" ? "" : ",")+secilen;
                    adet++;
                }
            });
            $('#mailsecilenler').val(secilenler);
            $('#mailadet').val(adet);
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
            var dolar = $('#maildolar').val();
            var euro = $('#maileuro').val();
            var sterlin = $('#mailsterlin').val();
            var parabirimi = $('#telbirimi').val();
            var parabirimi2 = $('#telbirimi2').val();
            var genelgaranti = "1";
            var secilenlist = secilenler.split(',');
            var birim, birim2, fiyat, fiyat2, indirim, kdvsiztutar, kdv, toplamtutar, indirim2, kdvsiztutar2, kdv2, toplamtutar2;
            oTable8.rows().every(function () {
                var data = this.data();
                var id = data[0];
                var durum = 0;
                $.each(secilenlist, function (index) {
                    if (id === secilenlist[index])
                        durum = 1;
                });
                var garanti = data[3];
                var indirimorani = parseFloat(data[6]);
                fiyat = parseFloat(data[10]);
                fiyat2 = parseFloat(data[12]);
                birim = data[11];
                birim2 = data[13];
                if (durum === 1) {
                    if (birim === parabirimi) {
                        if (birim2 !== "") {
                            if (birim2 !== parabirimi2) {
                                fiyat2 = 0;
                                $('.mailwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.mailonayla').prop('disabled', true);
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
                                $('.mailwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.mailonayla').prop('disabled', true);
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
                $('.mailtutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi);
                $('.mailindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi);
                $('.mailkdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi);
                $('.mailkdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi);
                $('.mailtoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi);
            } else {
                if(geneltoplamtutar === 0){
                    $('.mailtutar').text(genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.mailindirimtutar').text(genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.mailkdvsiztutar').text(genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.mailkdvtutar').text(genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.mailtoplamtutar').text(geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }else{
                    $('.mailtutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi + ' + ' + genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.mailindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi + ' + ' + genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.mailkdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.mailkdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.mailtoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi + ' + ' + geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }
            }
            $('#mailtutar').val(genelfiyat.toFixed(2));
            $('#mailindirimtutar').val(genelindirim.toFixed(2));
            $('#mailkdvsiztutar').val(genelkdvsiztutar.toFixed(2));
            $('#mailkdvtutar').val(genelkdvtutar.toFixed(2));
            $('#mailtoplamtutar').val(geneltoplamtutar.toFixed(2));
            $('#mailtutar2').val(genelfiyat2.toFixed(2));
            $('#mailindirimtutar2').val(genelindirim2.toFixed(2));
            $('#mailkdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
            $('#mailkdvtutar2').val(genelkdvtutar2.toFixed(2));
            $('#mailtoplamtutar2').val(geneltoplamtutar2.toFixed(2));
            if (adet > 0) {
                $('#maillink').removeClass('hide');
                $('#mailek1').removeClass('hide');
                $('#mailek2').removeClass('hide');
                $('#caribilgi').removeClass('hide');
                $('.mailonayla').removeClass('hide');
            }else{
                $('#maillink').addClass('hide');
                $('#mailek1').addClass('hide');
                $('#mailek2').addClass('hide');
                $('#caribilgi').addClass('hide');
                $('.mailonayla').addClass('hide');
            }
            var id = $('#secilen').val();
            var root= $('.root').val();
            var serverdis = $('.mailroot').val();
            var onaysayfa = root+'/musterionay/'+id;
            var onaysayfadis = serverdis+'/musterionay/'+id;
            var fiyatlandirma = root+'/ucretlendirme/fiyatlandirmatablo/'+id+'/'+secilenler;
            var onayform = root+'/ucretlendirme/onayform/'+id+'/'+secilenler;
            $('#maillink').prop('href',onaysayfa);
            $('.maillink').val(onaysayfadis);
            $('#mailek1').prop('href',fiyatlandirma);
            $('#mailek2').prop('href',onayform);
            $('#mailgaranti').val(genelgaranti);
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        var table9 = $('#sample_editable_9');
        var oTable9 = table9.DataTable({
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
            "aoColumns": [{"sClass":"id"},{"sClass":"adi"},{"sClass":"tel"},{"sClass":"mail"}],
            "lengthMenu": [
                [5, 10],
                [5, 10]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_9_wrapper');
        table9.on('click', 'tr', function () {
            if(oTable9.cell( $(this).children('.id')).data()!==undefined) {
                $(this).toggleClass("active");
                var bos=0;
                if ($(this).hasClass('active')) {
                    $("#sample_editable_9 tbody tr").removeClass("active");
                    $(this).addClass("active");
                    $('#yetkilisecilen').val(oTable9.cell( $(this).children('.id')).data());
                    $('#yetkilisecilenadi').val(oTable9.cell( $(this).children('.adi')).data());
                    $('#yetkilisecilentel').val(oTable9.cell( $(this).children('.tel')).data());
                    $('#yetkilisecilenemail').val(oTable9.cell( $(this).children('.mail')).data());
                } else {
                    $(this).removeClass("active");
                    $('#yetkilisecilen').val('');
                    $('#yetkilisecilenadi').val('');
                    $('#yetkilisecilentel').val('');
                    $('#yetkilisecilenemail').val('');
                    bos=1;
                }
                if(bos)
                {
                    $('.yetkililistesi').addClass("hide");
                }else{
                    $('.yetkililistesi').removeClass("hide");
                }
            }
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        var table10 = $('#sample_editable_10');
        var oTable10 = table10.DataTable({
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
            "aoColumns": [{"sClass":"id"},{"sClass":"adi"},{"sClass":"tel"},{"sClass":"mail"}],
            "lengthMenu": [
                [5, 10],
                [5, 10]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_10_wrapper');
        table10.on('click', 'tr', function () {
            if(oTable10.cell( $(this).children('.id')).data()!==undefined) {
                $(this).toggleClass("active");
                var bos=0;
                if ($(this).hasClass('active')) {
                    $("#sample_editable_10 tbody tr").removeClass("active");
                    $(this).addClass("active");
                    $('#yetkilibulsecilen').val(oTable10.cell( $(this).children('.id')).data());
                    $('#yetkilibulsecilenadi').val(oTable10.cell( $(this).children('.adi')).data());
                    $('#yetkilibulsecilentel').val(oTable10.cell( $(this).children('.tel')).data());
                    $('#yetkilibulsecilenemail').val(oTable10.cell( $(this).children('.mail')).data());
                } else {
                    $(this).removeClass("active");
                    $('#yetkilibulsecilen').val('');
                    $('#yetkilibulsecilenadi').val('');
                    $('#yetkilibulsecilentel').val('');
                    $('#yetkilibulsecilenemail').val('');
                    bos=1;
                }
                if(bos)
                {
                    $('.yetkilibul').addClass("hide");
                }else{
                    $('.yetkilibul').removeClass("hide");
                }
            }
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        var table12 = $('#sample_editable_12');
        var oTable12 = table12.DataTable({
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
            "aoColumns": [{"sClass":"id"},null,null,{"sClass":"garantionay"},{"sClass":"fiyatdurumonay"},
                {"sClass":"tutaronay"},{"sClass":"indirimoranionay"},{"sClass":"kdvsiztutaronay"},{"sClass":"kdvtutaronay"},{"sClass":"toplamtutaronay"},
                null,null,null,null],
            "lengthMenu": [
                [5, 10, 20, 99999999],
                [5, 10, 20, "Hepsi"]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_12_wrapper');
        table12.on('click', 'tr', function () {
            if(oTable12.cell( $(this).children('.id')).data()!==undefined){
                $(this).toggleClass("active");
                var adet=parseInt($('#onayadet').val());
                var secilenler=$('#onaysecilenler').val();
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
                var dolar = $('#onaydolar').val();
                var euro = $('#onayeuro').val();
                var sterlin = $('#onaysterlin').val();
                var parabirimi = $('#onaybirimi').val();
                var parabirimi2 =$('#onaybirimi2').val();
                var genelgaranti = "1";
                var secilenlist;
                var birim,birim2,fiyat,fiyat2,indirim,kdvsiztutar,kdv,toplamtutar,indirim2,kdvsiztutar2,kdv2,toplamtutar2;
                if($(this).hasClass('active'))
                {
                    secilenler+=(secilenler==="" ? "" : ",")+oTable12.cell( $(this).children('.id')).data();
                    adet++;
                    $('#onaysecilenler').val(secilenler);
                    $('#onayadet').val(adet);
                }else{
                    var secilen=oTable12.cell( $(this).children('.id')).data();
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
                    $('#onaysecilenler').val(yenilist);
                    $('#onayadet').val(adet);
                }
                secilenlist=secilenler.split(',');
                oTable12.rows().every( function () {
                    var data = this.data();
                    var id=data[0];
                    var durum=0;
                    $.each(secilenlist,function(index){
                        if(id===secilenlist[index])
                            durum=1;
                    });
                    var garanti = data[3];
                    var indirimorani = parseFloat(data[6]);
                    fiyat = parseFloat(data[10]);
                    fiyat2 = parseFloat(data[12]);
                    birim = data[11];
                    birim2 = data[13];
                    if(durum===1){
                        if (birim === parabirimi) {
                            if (birim2 !== "") {
                                if (birim2 !== parabirimi2) {
                                    fiyat2 = 0;
                                    $('.telwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.telonayla').prop('disabled', true);
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
                                    $('.onaywarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.yetkilionayla').prop('disabled', true);
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
                if(geneltoplamtutar2===0){
                    $('.onaytutar').text(genelfiyat.toFixed(2)+' '+parabirimi);
                    $('.onayindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi);
                    $('.onaykdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi);
                    $('.onaykdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi);
                    $('.onaytoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi);
                }else{
                    if(geneltoplamtutar === 0){
                        $('.onaytutar').text(genelfiyat2.toFixed(2)+' '+parabirimi2);
                        $('.onayindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2);
                        $('.onaykdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2);
                        $('.onaykdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2);
                        $('.onaytoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2);
                    }else{
                        $('.onaytutar').text(genelfiyat.toFixed(2)+' '+parabirimi+' + '+genelfiyat2.toFixed(2)+' '+parabirimi2);
                        $('.onayindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi+' + '+genelindirim2.toFixed(2)+' '+parabirimi2);
                        $('.onaykdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi+' + '+genelkdvsiztutar2.toFixed(2)+' '+parabirimi2);
                        $('.onaykdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi+' + '+genelkdvtutar2.toFixed(2)+' '+parabirimi2);
                        $('.onaytoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2);
                    }
                }
                $('#onaytutar').val(genelfiyat.toFixed(2));
                $('#onayindirimtutar').val(genelindirim.toFixed(2));
                $('#onaykdvsiztutar').val(genelkdvsiztutar.toFixed(2));
                $('#onaykdvtutar').val(genelkdvtutar.toFixed(2));
                $('#onaytoplamtutar').val(geneltoplamtutar.toFixed(2));
                $('#onaytutar2').val(genelfiyat2.toFixed(2));
                $('#onayindirimtutar2').val(genelindirim2.toFixed(2));
                $('#onaykdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
                $('#onaykdvtutar2').val(genelkdvtutar2.toFixed(2));
                $('#onaytoplamtutar2').val(geneltoplamtutar2.toFixed(2));
                if(adet>0){
                    $('.yetkilionayla').removeClass('hide');
                }else{
                    $('.yetkilionayla').addClass('hide');
                }
                $('#onaysecilenler').val(secilenler);
                $('#onaygaranti').val(genelgaranti);
            }
        });
        $(document).on("click", ".temizle12", function () {
            var adet = parseInt($('#onayadet').val());
            var secilenler = $('#onaysecilenler').val();
            $("#sample_editable_12 tbody tr .id").each(function () {
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
            $('#onaysecilenler').val(secilenler);
            $('#onayadet').val(adet);
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
            var dolar = $('#onaydolar').val();
            var euro = $('#onayeuro').val();
            var sterlin = $('#onaysterlin').val();
            var parabirimi = $('#onaybirimi').val();
            var parabirimi2 = $('#onaybirimi2').val();
            var genelgaranti = "1";
            var secilenlist = secilenler.split(',');
            var birim, birim2, fiyat, fiyat2, indirim, kdvsiztutar, kdv, toplamtutar, indirim2, kdvsiztutar2, kdv2, toplamtutar2;
            oTable12.rows().every(function () {
                var data = this.data();
                var id = data[0];
                var durum = 0;
                $.each(secilenlist, function (index) {
                    if (id === secilenlist[index])
                        durum = 1;
                });
                var garanti = data[3];
                var indirimorani = parseFloat(data[6]);
                fiyat = parseFloat(data[10]);
                fiyat2 = parseFloat(data[12]);
                birim = data[11];
                birim2 = data[13];
                if (durum === 1) {
                    if (birim === parabirimi) {
                        if (birim2 !== "") {
                            if (birim2 !== parabirimi2) {
                                fiyat2 = 0;
                                $('.onaywarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.yetkilionayla').prop('disabled', true);
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
                                $('.onaywarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.yetkilionayla').prop('disabled', true);
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
                $('.onaytutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi);
                $('.onayindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi);
                $('.onaykdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi);
                $('.onaykdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi);
                $('.onaytoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi);
            } else {
                if(geneltoplamtutar === 0){
                    $('.onaytutar').text(genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.onayindirimtutar').text(genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.onaykdvsiztutar').text(genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.onaykdvtutar').text(genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.onaytoplamtutar').text(geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }else{
                    $('.onaytutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi + ' + ' + genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.onayindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi + ' + ' + genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.onaykdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.onaykdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.onaytoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi + ' + ' + geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }
            }
            $('#onaytutar').val(genelfiyat.toFixed(2));
            $('#onayindirimtutar').val(genelindirim.toFixed(2));
            $('#onaykdvsiztutar').val(genelkdvsiztutar.toFixed(2));
            $('#onaykdvtutar').val(genelkdvtutar.toFixed(2));
            $('#onaytoplamtutar').val(geneltoplamtutar.toFixed(2));
            $('#onaytutar2').val(genelfiyat2.toFixed(2));
            $('#onayindirimtutar2').val(genelindirim2.toFixed(2));
            $('#onaykdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
            $('#onaykdvtutar2').val(genelkdvtutar2.toFixed(2));
            $('#onaytoplamtutar2').val(geneltoplamtutar2.toFixed(2));
            if (adet > 0) {
                $('.yetkilionayla').removeClass('hide');
            } else {
                $('.yetkilionayla').addClass('hide');
            }
            $('#onaygaranti').val(genelgaranti);
        } );
        $(document).on("click", ".tumunusec12", function () {
            var adet=parseInt($('#onayadet').val());
            var secilenler=$('#onaysecilenler').val();
            $("#sample_editable_12 tbody tr .id").each(function(){
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
            $('#onaysecilenler').val(secilenler);
            $('#onayadet').val(adet);
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
            var dolar = $('#onaydolar').val();
            var euro = $('#onayeuro').val();
            var sterlin = $('#onaysterlin').val();
            var parabirimi = $('#onaybirimi').val();
            var parabirimi2 = $('#onaybirimi2').val();
            var genelgaranti = "1";
            var secilenlist = secilenler.split(',');
            var birim, birim2, fiyat, fiyat2, indirim, kdvsiztutar, kdv, toplamtutar, indirim2, kdvsiztutar2, kdv2, toplamtutar2;
            oTable12.rows().every(function () {
                var data = this.data();
                var id = data[0];
                var durum = 0;
                $.each(secilenlist, function (index) {
                    if (id === secilenlist[index])
                        durum = 1;
                });
                var garanti = data[3];
                var indirimorani = parseFloat(data[6]);
                fiyat = parseFloat(data[10]);
                fiyat2 = parseFloat(data[12]);
                birim = data[11];
                birim2 = data[13];
                if (durum === 1) {
                    if (birim === parabirimi) {
                        if (birim2 !== "") {
                            if (birim2 !== parabirimi2) {
                                fiyat2 = 0;
                                $('.onaywarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.yetkilionayla').prop('disabled', true);
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
                                $('.onaywarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.yetkilionayla').prop('disabled', true);
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
                $('.onaytutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi);
                $('.onayindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi);
                $('.onaykdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi);
                $('.onaykdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi);
                $('.onaytoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi);
            } else {
                if(geneltoplamtutar === 0){
                    $('.onaytutar').text(genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.onayindirimtutar').text(genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.onaykdvsiztutar').text(genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.onaykdvtutar').text(genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.onaytoplamtutar').text(geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }else{
                    $('.onaytutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi + ' + ' + genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.onayindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi + ' + ' + genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.onaykdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.onaykdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.onaytoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi + ' + ' + geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }
            }
            $('#onaytutar').val(genelfiyat.toFixed(2));
            $('#onayindirimtutar').val(genelindirim.toFixed(2));
            $('#onaykdvsiztutar').val(genelkdvsiztutar.toFixed(2));
            $('#onaykdvtutar').val(genelkdvtutar.toFixed(2));
            $('#onaytoplamtutar').val(geneltoplamtutar.toFixed(2));
            $('#onaytutar2').val(genelfiyat2.toFixed(2));
            $('#onayindirimtutar2').val(genelindirim2.toFixed(2));
            $('#onaykdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
            $('#onaykdvtutar2').val(genelkdvtutar2.toFixed(2));
            $('#onaytoplamtutar2').val(geneltoplamtutar2.toFixed(2));
            if (adet > 0) {
                $('.yetkilionayla').removeClass('hide');
            } else {
                $('.yetkilionayla').addClass('hide');
            }
            $('#onaygaranti').val(genelgaranti);
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        var table13 = $('#sample_editable_13');
        var oTable13 = table13.DataTable({
            "sPaginationType": "simple_numbers",
            "searching": true,
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
            "aoColumns": [{"sClass":"id","width": "6%"},
                {"width": "8%"},{"width": "24%"},{"width": "7%"},{"width": "7%"},{"width": "8%"},{"width": "8%"},{"width": "8%"},
                {"width": "8%"},{"width": "8%"},{"width": "8%"}],
            "lengthMenu": [
                [5, 10],
                [5, 10]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_13_wrapper');
        table13.on('click', 'tr', function () {
            if(oTable13.cell( $(this).children('.id')).data()!==undefined){
                $(this).toggleClass("active");
                var adet=parseInt($('#yetkiliredadet').val());
                var secilenler=$('#yetkiliredsecilenler').val();
                var secilenlist;
                if($(this).hasClass('active'))
                {
                    secilenler+=(secilenler==="" ? "" : ",")+oTable13.cell( $(this).children('.id')).data();
                    adet++;
                    $('#yetkiliredsecilenler').val(secilenler);
                    $('#yetkiliredadet').val(adet);
                }else{
                    var secilen=oTable13.cell( $(this).children('.id')).data();
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
                    $('#yetkiliredsecilenler').val(yenilist);
                    $('#yetkiliredadet').val(adet);
                }
                secilenlist=secilenler.split(',');
                oTable13.rows().every( function () {
                    var data = this.data();
                    var id=data[0];
                    var durum=0;
                    $.each(secilenlist,function(index){
                        if(id===secilenlist[index])
                            durum=1;
                    });
                });
                if(adet>0){
                    $('.yetkiliredonayla').removeClass('hide');
                }else{
                    $('.yetkiliredonayla').addClass('hide');
                }
                $('#yetkiliredsecilenler').val(secilenler);
            }
        });
        $(document).on("click", ".temizle13", function () {
            var adet=parseInt($('#yetkiliredadet').val());
            var secilenler=$('#yetkiliredsecilenler').val();
            $("#sample_editable_13 tbody tr .id").each(function(){
                var secilen=$(this).html();
                $(this).parents('tr').removeClass("active");
                var secilenlist=secilenler.split(',');
                var yenilist="";
                $.each(secilenlist,function(index){
                    if(secilenlist[index]!==secilen)
                    {
                        yenilist+=(yenilist==="" ? "" : ",")+secilenlist[index];
                    }else{
                        adet--;
                    }
                });
                secilenler=yenilist;
            });
            $('#yetkiliredsecilenler').val(secilenler);
            $('#yetkiliredadet').val(adet);
            if(adet>0){
                $('.yetkiliredonayla').removeClass('hide');
            }else{
                $('.yetkiliredonayla').addClass('hide');
            }
        });
        $(document).on("click", ".tumunusec13", function () {
            var adet=parseInt($('#yetkiliredadet').val());
            var secilenler=$('#yetkiliredsecilenler').val();
            $("#sample_editable_13 tbody tr .id").each(function(){
                var secilen=$(this).html();
                $(this).parents('tr').addClass("active");
                var secilenlist=secilenler.split(',');
                var flag=0;
                $.each(secilenlist,function(index){
                    if(secilenlist[index]===secilen)
                    {
                        flag=1;
                    }
                });
                if(flag===0){
                    secilenler+=(secilenler==="" ? "" : ",")+secilen;
                    adet++;
                }
            });
            $('#yetkiliredsecilenler').val(secilenler);
            $('#yetkiliredadet').val(adet);
            if(adet>0){
                $('.yetkiliredonayla').removeClass('hide');
            }else{
                $('.yetkiliredonayla').addClass('hide');
            }
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        var table14 = $('#sample_editable_14');
        var oTable14 = table14.DataTable({
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
            "aoColumns": [{"sClass":"id"},null,null,{"sClass":"garantimail"},{"sClass":"fiyatdurummail"},
                {"sClass":"tutarmail"},{"sClass":"indirimoranimail"},{"sClass":"kdvsiztutarmail"},{"sClass":"kdvtutarmail"},{"sClass":"toplamtutarmail"},
                null,null,null,null],
            "lengthMenu": [
                [5, 10, 20, 99999999],
                [5, 10, 20, "Hepsi"]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_14_wrapper');
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        var table15 = $('#sample_editable_15');
        var oTable15 = table15.DataTable({
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
            "aoColumns": [{"sClass":"id"},null,null,{"sClass":"garantimail"},{"sClass":"fiyatdurummail"},
                {"sClass":"tutarmail"},{"sClass":"indirimoranimail"},{"sClass":"kdvsiztutarmail"},{"sClass":"kdvtutarmail"},{"sClass":"toplamtutarmail"},
                null,null,null,null],
            "lengthMenu": [
                [5, 10, 20, 99999999],
                [5, 10, 20, "Hepsi"]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_15_wrapper');
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        var table16 = $('#sample_editable_16');
        var oTable16 = table16.DataTable({
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
            "aoColumns": [{"sClass":"id"},null,null,{"sClass":"subegarantionay"},{"sClass":"subefiyatdurumonay"},
                {"sClass":"subetutaronay"},{"sClass":"indirimoranionay"},{"sClass":"subekdvsiztutaronay"},{"sClass":"subekdvtutaronay"},{"sClass":"subetoplamtutaronay"},
                null,null,null,null],
            "lengthMenu": [
                [5, 10, 20, 99999999],
                [5, 10, 20, "Hepsi"]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_16_wrapper');
        table16.on('click', 'tr', function () {
            if(oTable16.cell( $(this).children('.id')).data()!==undefined){
                $(this).toggleClass("active");
                var adet=parseInt($('#subeonayadet').val());
                var secilenler=$('#subeonaysecilenler').val();
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
                var dolar = $('#subeonaydolar').val();
                var euro = $('#subeonayeuro').val();
                var sterlin = $('#subeonaysterlin').val();
                var parabirimi = $('#subeonaybirimi').val();
                var parabirimi2 =$('#subeonaybirimi2').val();
                var genelgaranti = "1";
                var secilenlist;
                var birim,birim2,fiyat,fiyat2,indirim,kdvsiztutar,kdv,toplamtutar,indirim2,kdvsiztutar2,kdv2,toplamtutar2;
                if($(this).hasClass('active'))
                {
                    secilenler+=(secilenler==="" ? "" : ",")+oTable16.cell( $(this).children('.id')).data();
                    adet++;
                    $('#subeonaysecilenler').val(secilenler);
                    $('#subeonayadet').val(adet);
                }else{
                    var secilen=oTable16.cell( $(this).children('.id')).data();
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
                    $('#subeonaysecilenler').val(yenilist);
                    $('#subeonayadet').val(adet);
                }
                secilenlist=secilenler.split(',');
                oTable16.rows().every( function () {
                    var data = this.data();
                    var id=data[0];
                    var durum=0;
                    $.each(secilenlist,function(index){
                        if(id===secilenlist[index])
                            durum=1;
                    });
                    var garanti = data[3];
                    var indirimorani = parseFloat(data[6]);
                    fiyat = parseFloat(data[10]);
                    fiyat2 = parseFloat(data[12]);
                    birim = data[11];
                    birim2 = data[13];
                    if(durum===1){
                        if (birim === parabirimi) {
                            if (birim2 !== "") {
                                if (birim2 !== parabirimi2) {
                                    fiyat2 = 0;
                                    $('.subeonaywarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.subeyetkilionayla').prop('disabled', true);
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
                                    $('.subeonaywarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.subeyetkilionayla').prop('disabled', true);
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
                    $('.subeonaytutar').text(genelfiyat.toFixed(2)+' '+parabirimi);
                    $('.subeonayindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi);
                    $('.subeonaykdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi);
                    $('.subeonaykdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi);
                    $('.subeonaytoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi);
                }else{
                    if(geneltoplamtutar === 0){
                        $('.subeonaytutar').text(genelfiyat2.toFixed(2)+' '+parabirimi2);
                        $('.subeonayindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2);
                        $('.subeonaykdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2);
                        $('.subeonaykdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2);
                        $('.subeonaytoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2);
                    }else{
                        $('.subeonaytutar').text(genelfiyat.toFixed(2)+' '+parabirimi+' + '+genelfiyat2.toFixed(2)+' '+parabirimi2);
                        $('.subeonayindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi+' + '+genelindirim2.toFixed(2)+' '+parabirimi2);
                        $('.subeonaykdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi+' + '+genelkdvsiztutar2.toFixed(2)+' '+parabirimi2);
                        $('.subeonaykdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi+' + '+genelkdvtutar2.toFixed(2)+' '+parabirimi2);
                        $('.subeonaytoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2);
                    }
                }
                $('#subeonaytutar').val(genelfiyat.toFixed(2));
                $('#subeonayindirimtutar').val(genelindirim.toFixed(2));
                $('#subeonaykdvsiztutar').val(genelkdvsiztutar.toFixed(2));
                $('#subeonaykdvtutar').val(genelkdvtutar.toFixed(2));
                $('#subeonaytoplamtutar').val(geneltoplamtutar.toFixed(2));
                $('#subeonaytutar2').val(genelfiyat2.toFixed(2));
                $('#subeonayindirimtutar2').val(genelindirim2.toFixed(2));
                $('#subeonaykdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
                $('#subeonaykdvtutar2').val(genelkdvtutar2.toFixed(2));
                $('#subeonaytoplamtutar2').val(geneltoplamtutar2.toFixed(2));
                if(adet>0){
                    $('.subeyetkilionayla').removeClass('hide');
                }else{
                    $('.subeyetkilionayla').addClass('hide');
                }
                $('#subeonaysecilenler').val(secilenler);
                $('#subeonaygaranti').val(genelgaranti);
            }
        });
        $(document).on("click", ".temizle16", function () {
            var adet = parseInt($('#subeonayadet').val());
            var secilenler = $('#subeonaysecilenler').val();
            $("#sample_editable_16 tbody tr .id").each(function () {
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
                secilenler=yenilist;
            });
            $('#subeonaysecilenler').val(secilenler);
            $('#subeonayadet').val(adet);
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
            var dolar = $('#subeonaydolar').val();
            var euro = $('#subeonayeuro').val();
            var sterlin = $('#subeonaysterlin').val();
            var parabirimi = $('#subeonaybirimi').val();
            var parabirimi2 = $('#subeonaybirimi2').val();
            var genelgaranti = "1";
            var secilenlist = secilenler.split(',');
            var birim,birim2,fiyat,fiyat2,indirim,indirim2,kdvsiztutar,kdvsiztutar2,kdv,kdv2,toplamtutar,toplamtutar2;
            oTable16.rows().every(function () {
                var data = this.data();
                var id = data[0];
                var durum = 0;
                $.each(secilenlist, function (index) {
                    if (id === secilenlist[index])
                        durum = 1;
                });
                var garanti = data[3];
                var indirimorani = parseFloat(data[6]);
                fiyat = parseFloat(data[10]);
                fiyat2 = parseFloat(data[12]);
                birim = data[11];
                birim2 = data[13];
                if (durum === 1) {
                    if (birim === parabirimi) {
                        if (birim2 !== "") {
                            if (birim2 !== parabirimi2) {
                                fiyat2 = 0;
                                $('.subeonaywarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.subeyetkilionayla').prop('disabled', true);
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
                                $('.subeonaywarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.subeyetkilionayla').prop('disabled', true);
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
                $('.subeonaytutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi);
                $('.subeonayindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi);
                $('.subeonaykdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi);
                $('.subeonaykdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi);
                $('.subeonaytoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi);
            } else {
                if(geneltoplamtutar === 0){
                    $('.subeonaytutar').text(genelfiyat2.toFixed(2)+' '+parabirimi2);
                    $('.subeonayindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2);
                    $('.subeonaykdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2);
                    $('.subeonaykdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2);
                    $('.subeonaytoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2);
                }else {
                    $('.subeonaytutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi + ' + ' + genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.subeonayindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi + ' + ' + genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.subeonaykdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.subeonaykdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.subeonaytoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi + ' + ' + geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }
            }
            $('#subeonaytutar').val(genelfiyat.toFixed(2));
            $('#subeonayindirimtutar').val(genelindirim.toFixed(2));
            $('#subeonaykdvsiztutar').val(genelkdvsiztutar.toFixed(2));
            $('#subeonaykdvtutar').val(genelkdvtutar.toFixed(2));
            $('#subeonaytoplamtutar').val(geneltoplamtutar.toFixed(2));
            $('#subeonaytutar2').val(genelfiyat2.toFixed(2));
            $('#subeonayindirimtutar2').val(genelindirim2.toFixed(2));
            $('#subeonaykdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
            $('#subeonaykdvtutar2').val(genelkdvtutar2.toFixed(2));
            $('#subeonaytoplamtutar2').val(geneltoplamtutar2.toFixed(2));
            if (adet > 0) {
                $('.subeyetkilionayla').removeClass('hide');
            } else {
                $('.subeyetkilionayla').addClass('hide');
            }
            $('#subeonaygaranti').val(genelgaranti);
        } );
        $(document).on("click", ".tumunusec16", function () {
            var adet=parseInt($('#subeonayadet').val());
            var secilenler=$('#subeonaysecilenler').val();
            $("#sample_editable_16 tbody tr .id").each(function(){
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
            $('#subeonaysecilenler').val(secilenler);
            $('#subeonayadet').val(adet);
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
            var dolar = $('#subeonaydolar').val();
            var euro = $('#subeonayeuro').val();
            var sterlin = $('#subeonaysterlin').val();
            var parabirimi = $('#subeonaybirimi').val();
            var parabirimi2 = $('#subeonaybirimi2').val();
            var genelgaranti = "1";
            var secilenlist = secilenler.split(',');
            var birim, birim2, fiyat, fiyat2, indirim, kdvsiztutar, kdv, toplamtutar, indirim2, kdvsiztutar2, kdv2, toplamtutar2;
            oTable16.rows().every(function () {
                var data = this.data();
                var id = data[0];
                var durum = 0;
                $.each(secilenlist, function (index) {
                    if (id === secilenlist[index])
                        durum = 1;
                });
                var garanti = data[3];
                var indirimorani = parseFloat(data[6]);
                fiyat = parseFloat(data[10]);
                fiyat2 = parseFloat(data[12]);
                birim = data[11];
                birim2 = data[13];
                if (durum === 1) {
                    if (birim === parabirimi) {
                        if (birim2 !== "") {
                            if (birim2 !== parabirimi2) {
                                fiyat2 = 0;
                                $('.subeonaywarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.subeyetkilionayla').prop('disabled', true);
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
                                $('.subeonaywarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.subeyetkilionayla').prop('disabled', true);
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
                $('.subeonaytutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi);
                $('.subeonayindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi);
                $('.subeonaykdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi);
                $('.subeonaykdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi);
                $('.subeonaytoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi);
            } else {
                if(geneltoplamtutar === 0){
                    $('.subeonaytutar').text(genelfiyat2.toFixed(2)+' '+parabirimi2);
                    $('.subeonayindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2);
                    $('.subeonaykdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2);
                    $('.subeonaykdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2);
                    $('.subeonaytoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2);
                }else {
                    $('.subeonaytutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi + ' + ' + genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.subeonayindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi + ' + ' + genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.subeonaykdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.subeonaykdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.subeonaytoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi + ' + ' + geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }
            }
            $('#subeonaytutar').val(genelfiyat.toFixed(2));
            $('#subeonayindirimtutar').val(genelindirim.toFixed(2));
            $('#subeonaykdvsiztutar').val(genelkdvsiztutar.toFixed(2));
            $('#subeonaykdvtutar').val(genelkdvtutar.toFixed(2));
            $('#subeonaytoplamtutar').val(geneltoplamtutar.toFixed(2));
            $('#subeonaytutar2').val(genelfiyat2.toFixed(2));
            $('#subeonayindirimtutar2').val(genelindirim2.toFixed(2));
            $('#subeonaykdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
            $('#subeonaykdvtutar2').val(genelkdvtutar2.toFixed(2));
            $('#subeonaytoplamtutar2').val(geneltoplamtutar2.toFixed(2));
            if (adet > 0) {
                $('.subeyetkilionayla').removeClass('hide');
            } else {
                $('.subeyetkilionayla').addClass('hide');
            }
            $('#subeonaygaranti').val(genelgaranti);
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        var table17 = $('#sample_editable_17');
        var oTable17 = table17.DataTable({
            "sPaginationType": "simple_numbers",
            "searching": true,
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
            "aoColumns": [{"sClass":"id","width": "6%"},
                {"width": "8%"},{"width": "24%"},{"width": "7%"},{"width": "7%"},{"width": "8%"},{"width": "8%"},{"width": "8%"},
                {"width": "8%"},{"width": "8%"},{"width": "8%"}],
            "lengthMenu": [
                [5, 10],
                [5, 10]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_17_wrapper');
        table17.on('click', 'tr', function () {
            if(oTable17.cell( $(this).children('.id')).data()!==undefined){
                $(this).toggleClass("active");
                var adet=parseInt($('#subeyetkiliredadet').val());
                var secilenler=$('#subeyetkiliredsecilenler').val();
                var secilenlist;
                if($(this).hasClass('active'))
                {
                    secilenler+=(secilenler==="" ? "" : ",")+oTable17.cell( $(this).children('.id')).data();
                    adet++;
                    $('#subeyetkiliredsecilenler').val(secilenler);
                    $('#subeyetkiliredadet').val(adet);
                }else{
                    var secilen=oTable17.cell( $(this).children('.id')).data();
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
                    $('#subeyetkiliredsecilenler').val(yenilist);
                    $('#subeyetkiliredadet').val(adet);
                }
                secilenlist=secilenler.split(',');
                oTable17.rows().every( function () {
                    var data = this.data();
                    var id=data[0];
                    var durum=0;
                    $.each(secilenlist,function(index){
                        if(id===secilenlist[index])
                            durum=1;
                    });
                });
                if(adet>0){
                    $('.subeyetkiliredonayla').removeClass('hide');
                }else{
                    $('.subeyetkiliredonayla').addClass('hide');
                }
                $('#subeyetkiliredsecilenler').val(secilenler);
            }
        });
        $(document).on("click", ".temizle17", function () {
            var adet=parseInt($('#subeyetkiliredadet').val());
            var secilenler=$('#subeyetkiliredsecilenler').val();
            $("#sample_editable_17 tbody tr .id").each(function(){
                var secilen=$(this).html();
                $(this).parents('tr').removeClass("active");
                var secilenlist=secilenler.split(',');
                var yenilist="";
                $.each(secilenlist,function(index){
                    if(secilenlist[index]!==secilen)
                    {
                        yenilist+=(yenilist==="" ? "" : ",")+secilenlist[index];
                    }else{
                        adet--;
                    }
                });
                secilenler=yenilist;
            });
            $('#subeyetkiliredsecilenler').val(secilenler);
            $('#subeyetkiliredadet').val(adet);
            if(adet>0){
                $('.subeyetkiliredonayla').removeClass('hide');
            }else{
                $('.subeyetkiliredonayla').addClass('hide');
            }
        });
        $(document).on("click", ".tumunusec17", function () {
            var adet=parseInt($('#subeyetkiliredadet').val());
            var secilenler=$('#subeyetkiliredsecilenler').val();
            $("#sample_editable_17 tbody tr .id").each(function(){
                var secilen=$(this).html();
                $(this).parents('tr').addClass("active");
                var secilenlist=secilenler.split(',');
                var flag=0;
                $.each(secilenlist,function(index){
                    if(secilenlist[index]===secilen)
                    {
                        flag=1;
                    }
                });
                if(flag===0){
                    secilenler+=(secilenler==="" ? "" : ",")+secilen;
                    adet++;
                }
            });
            $('#subeyetkiliredsecilenler').val(secilenler);
            $('#subeyetkiliredadet').val(adet);
            if(adet>0){
                $('.subeyetkiliredonayla').removeClass('hide');
            }else{
                $('.subeyetkiliredonayla').addClass('hide');
            }
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        var table18 = $('#sample_editable_18');
        var oTable18 = table18.DataTable({
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
        var tableWrapper = jQuery('#sample_editable_18_wrapper');
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
        table18.on('click', 'tr', function () {
            if(oTable18.cell( $(this).children('.adres')).data()!==undefined){
                $(this).toggleClass("active");
                var secilen = "";
                if($(this).hasClass("active"))
                    secilen=oTable18.cell( $(this).children('.adres')).data();
                var flag = 0;
                $('#gerisecilenadres').val("");
                $("#sample_editable_18  tr .adres").each(function(){
                    if(secilen===$(this).html()){
                        $('#gerisecilenadres').val(secilen);
                        flag = 1;
                    }else{
                        $(this).parents('tr').removeClass("active");
                    }
                });
                if(!flag){
                    $('#gerisecilenadres').val("");
                }
            }
        });
    </script>
    <script>
        var table19 = $('#sample_editable_19');
        var oTable19 = table19.DataTable({
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
            "aoColumns": [{"sClass":"id"},null,null,{"sClass":"subegarantiaktar"},{"sClass":"subefiyatdurumaktar"},
                {"sClass":"subetutaraktar"},{"sClass":"indirimoraniaktar"},{"sClass":"subekdvsiztutaraktar"},{"sClass":"subekdvtutaraktar"},{"sClass":"subetoplamtutaraktar"},
                null,null,null,null],
            "lengthMenu": [
                [5, 10, 20, 99999999],
                [5, 10, 20, "Hepsi"]
            ]
        });
        var tableWrapper = jQuery('#sample_editable_19_wrapper');
        table19.on('click', 'tr', function () {
            if(oTable19.cell( $(this).children('.id')).data()!==undefined){
                $(this).toggleClass("active");
                var adet=parseInt($('#subeaktaradet').val());
                var secilenler=$('#subeaktarsecilenler').val();
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
                var dolar = $('#subeaktardolar').val();
                var euro = $('#subeaktareuro').val();
                var sterlin = $('#subeaktarsterlin').val();
                var parabirimi = $('#subeaktarbirimi').val();
                var parabirimi2 =$('#subeaktarbirimi2').val();
                var genelgaranti = "1";
                var secilenlist;
                var birim,birim2,fiyat,fiyat2,indirim,kdvsiztutar,kdv,toplamtutar,indirim2,kdvsiztutar2,kdv2,toplamtutar2;
                if($(this).hasClass('active'))
                {
                    secilenler+=(secilenler==="" ? "" : ",")+oTable19.cell( $(this).children('.id')).data();
                    adet++;
                    $('#subeaktarsecilenler').val(secilenler);
                    $('#subeaktaradet').val(adet);
                }else{
                    var secilen=oTable19.cell( $(this).children('.id')).data();
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
                    $('#subeaktarsecilenler').val(yenilist);
                    $('#subeaktaradet').val(adet);
                }
                secilenlist=secilenler.split(',');
                oTable19.rows().every( function () {
                    var data = this.data();
                    var id=data[0];
                    var durum=0;
                    $.each(secilenlist,function(index){
                        if(id===secilenlist[index])
                            durum=1;
                    });
                    var garanti = data[3];
                    var indirimorani = parseFloat(data[6]);
                    fiyat = parseFloat(data[10]);
                    fiyat2 = parseFloat(data[12]);
                    birim = data[11];
                    birim2 = data[13];
                    if(durum===1){
                        if (birim === parabirimi) {
                            if (birim2 !== "") {
                                if (birim2 !== parabirimi2) {
                                    fiyat2 = 0;
                                    $('.subeaktarwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.subeaktaronay').prop('disabled', true);
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
                                    $('.subeaktarwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.subeaktaronay').prop('disabled', true);
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
                    $('.subeaktartutar').text(genelfiyat.toFixed(2)+' '+parabirimi);
                    $('.subeaktarindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi);
                    $('.subeaktarkdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi);
                    $('.subeaktarkdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi);
                    $('.subeaktartoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi);
                }else{
                    if(geneltoplamtutar === 0){
                        $('.subeaktartutar').text(genelfiyat2.toFixed(2)+' '+parabirimi2);
                        $('.subeaktarindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2);
                        $('.subeaktarkdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2);
                        $('.subeaktarkdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2);
                        $('.subeaktartoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2);
                    }else{
                        $('.subeaktartutar').text(genelfiyat.toFixed(2)+' '+parabirimi+' + '+genelfiyat2.toFixed(2)+' '+parabirimi2);
                        $('.subeaktarindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi+' + '+genelindirim2.toFixed(2)+' '+parabirimi2);
                        $('.subeaktarkdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi+' + '+genelkdvsiztutar2.toFixed(2)+' '+parabirimi2);
                        $('.subeaktarkdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi+' + '+genelkdvtutar2.toFixed(2)+' '+parabirimi2);
                        $('.subeaktartoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2);
                    }
                }
                $('#subeaktartutar').val(genelfiyat.toFixed(2));
                $('#subeaktarindirimtutar').val(genelindirim.toFixed(2));
                $('#subeaktarkdvsiztutar').val(genelkdvsiztutar.toFixed(2));
                $('#subeaktarkdvtutar').val(genelkdvtutar.toFixed(2));
                $('#subeaktartoplamtutar').val(geneltoplamtutar.toFixed(2));
                $('#subeaktartutar2').val(genelfiyat2.toFixed(2));
                $('#subeaktarindirimtutar2').val(genelindirim2.toFixed(2));
                $('#subeaktarkdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
                $('#subeaktarkdvtutar2').val(genelkdvtutar2.toFixed(2));
                $('#subeaktartoplamtutar2').val(geneltoplamtutar2.toFixed(2));
                if(adet>0){
                    $('.subeaktaronay').removeClass('hide');
                }else{
                    $('.subeaktaronay').addClass('hide');
                }
                $('#subeaktarsecilenler').val(secilenler);
                $('#subeaktargaranti').val(genelgaranti);
            }
        });
        $(document).on("click", ".temizle19", function () {
            var adet = parseInt($('#subeaktaradet').val());
            var secilenler = $('#subeaktarsecilenler').val();
            $("#sample_editable_19 tbody tr .id").each(function () {
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
                secilenler=yenilist;
            });
            $('#subeaktarsecilenler').val(secilenler);
            $('#subeaktaradet').val(adet);
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
            var dolar = $('#subeaktardolar').val();
            var euro = $('#subeaktareuro').val();
            var sterlin = $('#subeaktarsterlin').val();
            var parabirimi = $('#subeaktarbirimi').val();
            var parabirimi2 = $('#subeaktarbirimi2').val();
            var genelgaranti = "1";
            var secilenlist = secilenler.split(',');
            var birim,birim2,fiyat,fiyat2,indirim,indirim2,kdvsiztutar,kdvsiztutar2,kdv,kdv2,toplamtutar,toplamtutar2;
            oTable19.rows().every(function () {
                var data = this.data();
                var id = data[0];
                var durum = 0;
                $.each(secilenlist, function (index) {
                    if (id === secilenlist[index])
                        durum = 1;
                });
                var garanti = data[3];
                var indirimorani = parseFloat(data[6]);
                fiyat = parseFloat(data[10]);
                fiyat2 = parseFloat(data[12]);
                birim = data[11];
                birim2 = data[13];
                if (durum === 1) {
                    if (birim === parabirimi) {
                        if (birim2 !== "") {
                            if (birim2 !== parabirimi2) {
                                fiyat2 = 0;
                                $('.subeaktarwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.subeaktaronay').prop('disabled', true);
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
                                $('.subeaktarwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.subeaktaronay').prop('disabled', true);
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
                $('.subeaktartutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi);
                $('.subeaktarindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi);
                $('.subeaktarkdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi);
                $('.subeaktarkdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi);
                $('.subeaktartoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi);
            } else {
                if(geneltoplamtutar === 0){
                    $('.subeaktartutar').text(genelfiyat2.toFixed(2)+' '+parabirimi2);
                    $('.subeaktarindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2);
                    $('.subeaktarkdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2);
                    $('.subeaktarkdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2);
                    $('.subeaktartoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2);
                }else {
                    $('.subeaktartutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi + ' + ' + genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.subeaktarindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi + ' + ' + genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.subeaktarkdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.subeaktarkdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.subeaktartoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi + ' + ' + geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }
            }
            $('#subeaktartutar').val(genelfiyat.toFixed(2));
            $('#subeaktarindirimtutar').val(genelindirim.toFixed(2));
            $('#subeaktarkdvsiztutar').val(genelkdvsiztutar.toFixed(2));
            $('#subeaktarkdvtutar').val(genelkdvtutar.toFixed(2));
            $('#subeaktartoplamtutar').val(geneltoplamtutar.toFixed(2));
            $('#subeaktartutar2').val(genelfiyat2.toFixed(2));
            $('#subeaktarindirimtutar2').val(genelindirim2.toFixed(2));
            $('#subeaktarkdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
            $('#subeaktarkdvtutar2').val(genelkdvtutar2.toFixed(2));
            $('#subeaktartoplamtutar2').val(geneltoplamtutar2.toFixed(2));
            if (adet > 0) {
                $('.subeaktaronay').removeClass('hide');
            } else {
                $('.subeaktaronay').addClass('hide');
            }
            $('#subeaktargaranti').val(genelgaranti);
        } );
        $(document).on("click", ".tumunusec19", function () {
            var adet=parseInt($('#subeaktaradet').val());
            var secilenler=$('#subeaktarsecilenler').val();
            $("#sample_editable_19 tbody tr .id").each(function(){
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
            $('#subeaktarsecilenler').val(secilenler);
            $('#subeaktaradet').val(adet);
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
            var dolar = $('#subeaktardolar').val();
            var euro = $('#subeaktareuro').val();
            var sterlin = $('#subeaktarsterlin').val();
            var parabirimi = $('#subeaktarbirimi').val();
            var parabirimi2 = $('#subeaktarbirimi2').val();
            var genelgaranti = "1";
            var secilenlist = secilenler.split(',');
            var birim, birim2, fiyat, fiyat2, indirim, kdvsiztutar, kdv, toplamtutar, indirim2, kdvsiztutar2, kdv2, toplamtutar2;
            oTable19.rows().every(function () {
                var data = this.data();
                var id = data[0];
                var durum = 0;
                $.each(secilenlist, function (index) {
                    if (id === secilenlist[index])
                        durum = 1;
                });
                var garanti = data[3];
                var indirimorani = parseFloat(data[6]);
                fiyat = parseFloat(data[10]);
                fiyat2 = parseFloat(data[12]);
                birim = data[11];
                birim2 = data[13];
                if (durum === 1) {
                    if (birim === parabirimi) {
                        if (birim2 !== "") {
                            if (birim2 !== parabirimi2) {
                                fiyat2 = 0;
                                $('.subeaktarwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.subeaktaronay').prop('disabled', true);
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
                                $('.subeaktarwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                $('.subeaktaronay').prop('disabled', true);
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
                $('.subeaktartutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi);
                $('.subeaktarindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi);
                $('.subeaktarkdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi);
                $('.subeaktarkdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi);
                $('.subeaktartoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi);
            } else {
                if(geneltoplamtutar === 0){
                    $('.subeaktartutar').text(genelfiyat2.toFixed(2)+' '+parabirimi2);
                    $('.subeaktarindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2);
                    $('.subeaktarkdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2);
                    $('.subeaktarkdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2);
                    $('.subeaktartoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2);
                }else {
                    $('.subeaktartutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi + ' + ' + genelfiyat2.toFixed(2) + ' ' + parabirimi2);
                    $('.subeaktarindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi + ' + ' + genelindirim2.toFixed(2) + ' ' + parabirimi2);
                    $('.subeaktarkdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.subeaktarkdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi + ' + ' + genelkdvtutar2.toFixed(2) + ' ' + parabirimi2);
                    $('.subeaktartoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi + ' + ' + geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
                }
            }
            $('#subeaktartutar').val(genelfiyat.toFixed(2));
            $('#subeaktarindirimtutar').val(genelindirim.toFixed(2));
            $('#subeaktarkdvsiztutar').val(genelkdvsiztutar.toFixed(2));
            $('#subeaktarkdvtutar').val(genelkdvtutar.toFixed(2));
            $('#subeaktartoplamtutar').val(geneltoplamtutar.toFixed(2));
            $('#subeaktartutar2').val(genelfiyat2.toFixed(2));
            $('#subeaktarindirimtutar2').val(genelindirim2.toFixed(2));
            $('#subeaktarkdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
            $('#subeaktarkdvtutar2').val(genelkdvtutar2.toFixed(2));
            $('#subeaktartoplamtutar2').val(geneltoplamtutar2.toFixed(2));
            if (adet > 0) {
                $('.subeaktaronay').removeClass('hide');
            } else {
                $('.subeaktaronay').addClass('hide');
            }
            $('#subeaktargaranti').val(genelgaranti);
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        $(document).on("click", ".goster", function () {
            $.blockUI();
            var id = $(this).data('id');
            $.getJSON("{{ URL::to('ucretlendirme/ucretlendirilenbilgi') }}",{id:id},function(event){
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
                    $('.onizlemewarning').html('<span style="color:red">Kur Tarihi: '+dovizkurutarih+'. Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
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
                    $.each(arizafiyat,function(index) {
                        var garanti = arizafiyat[index].ariza_garanti==="0" ? 'Dışında' : 'İçinde';
                        var fiyatdurum = arizafiyat[index].fiyatdurum==="0" ? 'Genel' : 'Özel';
                        var durum = arizafiyat[index].depoteslimdurum ? '' : 'hide';
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
                            if(toplamtutar>0){
                                oTable2.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi+' + '+fiyat2.toFixed(2)+' '+birim2.birimi,
                                    indirimorani.toFixed(2)+'%',kdvsiztutar.toFixed(2)+' '+birim.birimi+' + '+kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv.toFixed(2)+' '+birim.birimi+' + '+kdv2.toFixed(2)+' '+birim2.birimi,
                                    toplamtutar.toFixed(2)+' '+birim.birimi+' + '+toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                    '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>' +
                                    '<a href="#fiyat-sil" data-toggle="modal" data-id="'+arizafiyat[index].id+'" class="btn btn-sm btn-danger fiyatsil '+durum+'">Sil</a>']).draw();
                            }else{
                                oTable2.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat2.toFixed(2)+' '+birim2.birimi,indirimorani.toFixed(2)+'%',
                                    kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv2.toFixed(2)+' '+birim2.birimi,toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                    '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>' +
                                    '<a href="#fiyat-sil" data-toggle="modal" data-id="'+arizafiyat[index].id+'" class="btn btn-sm btn-danger fiyatsil '+durum+'">Sil</a>']).draw();
                            }
                        }else{
                            oTable2.row.add([arizafiyat[index].id,serino,sayacadi ,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi,indirimorani.toFixed(2)+'%',
                                kdvsiztutar.toFixed(2)+' '+birim.birimi,kdv.toFixed(2)+' '+birim.birimi,toplamtutar.toFixed(2)+' '+birim.birimi,
                                '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>' +
                                '<a href="#fiyat-sil" data-toggle="modal" data-id="'+arizafiyat[index].id+'" class="btn btn-sm btn-danger fiyatsil '+durum+'">Sil</a>'])
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
                                    $('.ucretlendir').prop('disabled', true);
                                }
                            }else{
                                toplamtutar2=0;
                            }
                        }else{
                            var kur = 1;
                            kurdurum=true;
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
                        if(geneltoplamtutar === 0){
                            $('.onizlemetutar').text(genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onizlemeindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onizlemekdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onizlemekdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onizlemetoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }else {
                            $('.onizlemetutar').text(genelfiyat.toFixed(2) + ' ' + parabirimi.birimi + ' + ' + genelfiyat2.toFixed(2) + ' ' + parabirimi2.birimi);
                            $('.onizlemeindirimtutar').text(genelindirim.toFixed(2) + ' ' + parabirimi.birimi + ' + ' + genelindirim2.toFixed(2) + ' ' + parabirimi2.birimi);
                            $('.onizlemekdvsiztutar').text(genelkdvsiztutar.toFixed(2) + ' ' + parabirimi.birimi + ' + ' + genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                            $('.onizlemekdvtutar').text(genelkdvtutar.toFixed(2) + ' ' + parabirimi.birimi + ' + ' + genelkdvtutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                            $('.onizlemetoplamtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi.birimi + ' + ' + geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                        }
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
                    $(".yer").html(ucretlendirme.uretimyer.yeradi);
                    $(".fiyatdurum").html(ucretlendirme.fiyatdurum==="0" ? 'Genel' : 'Özel');
                    $(".serino").html(ucretlendirme.ariza_serino);
                    $(".garanti").html(ucretlendirme.ariza_garanti==="0" ? 'Dışında' : 'İçinde');
                    if(ucretlendirme.sayac.sayaccap_id==="1")
                        $(".sayacadi").html(ucretlendirme.sayacadi.sayacadi);
                    else
                        $(".sayacadi").html(ucretlendirme.sayacadi.sayacadi+" - "+ucretlendirme.sayaccap.capadi );
                    oTable6.clear().draw();
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
                            oTable6.columns([4]).visible(true, false);
                            oTable6.columns([2, 3, 5, 6]).visible(false, false);
                        }else {
                            oTable6.columns([2, 6]).visible(true, false);
                            oTable6.columns([3, 4, 5]).visible(false, false);
                        }
                        $.each(degisenler,function(index) {
                            if (ucretsizler[index] === "1") {
                                oTable6.row.add([degisenler[index].id, degisenler[index].tanim, '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,
                                    '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,'Evet']).draw();
                            }else{
                                oTable6.row.add([degisenler[index].id, degisenler[index].tanim, genelfiyat[index] + ' ' + genelbirimler[index].birimi,ozelfiyat[index] + ' ' + ozelbirimler[index].birimi,
                                    '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,'Hayır']).draw();
                            }
                        });
                    }else {
                        if(ucretlendirme.ariza_garanti==="1") {
                            oTable6.columns([ 5 ]).visible(true, false);
                            oTable6.columns([2, 3, 4, 6]).visible(false, false);
                        }else{
                            oTable6.columns( [ 3,6 ] ).visible(true, false);
                            oTable6.columns( [ 2,4,5 ] ).visible( false,false );
                        }
                        $.each(degisenler, function (index) {
                            if (ucretsizler[index] === "1") {
                                oTable6.row.add([degisenler[index].id, degisenler[index].tanim, '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,
                                    '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,'Evet']).draw();
                            }else{
                                oTable6.row.add([degisenler[index].id, degisenler[index].tanim, genelfiyat[index] + ' ' + genelbirimler[index].birimi,ozelfiyat[index] + ' ' + ozelbirimler[index].birimi,
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
                                if(toplamtutar===0) {
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
                                if(toplamtutar===0) {
                                    $('.fiyattutar').text(fiyat2.toFixed(2)+' '+parabirimi2.birimi);
                                    $('.indirimtutar').text(indirim2.toFixed(2)+' '+parabirimi2.birimi);
                                    $('.kdvsiztutar').text(kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                                    $('.kdvtutar').text(kdv2.toFixed(2)+' '+parabirimi2.birimi);
                                    $('.toplamtutar').text(toplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                                }else {
                                    $('.fiyattutar').text(fiyat.toFixed(2)+' '+ozelbirimi+' + '+fiyat2.toFixed(2)+' '+parabirimi2.birimi);
                                    $('.indirimtutar').text(indirim.toFixed(2)+' '+ozelbirimi+' + '+indirim2.toFixed(2)+' '+parabirimi2.birimi);
                                    $('.kdvsiztutar').text(kdvsiztutar.toFixed(2)+' '+ozelbirimi+' + '+kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                                    $('.kdvtutar').text(kdv.toFixed(2)+' '+ozelbirimi+' + '+kdv2.toFixed(2)+' '+parabirimi2.birimi);
                                    $('.toplamtutar').text(toplamtutar.toFixed(2)+' '+ozelbirimi+' + '+toplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                                }
                            }
                        }
                    }
                    if(genelbirimid!=="1")
                        kurdurum=true;
                    if(kurdurum)
                        $('.detaykur').removeClass('hide');
                    else
                        $('.detaykur').addClass('hide');
                }else{
                    toastr[event.type](event.text, event.type);
                    $('#fiyat-detay').modal('hide');
                }
                $.unblockUI();
            });
        });
        $(document).on("click", ".telefononay", function () {
            $.blockUI();
            var id = $('#secilen').val();
            var action = $('#form_sample_7').data('action');
            $('#form_sample_7').prop('action',action+'/'+id);
            $.getJSON("{{ URL::to('ucretlendirme/onaybilgi') }}",{id:id},function(event){
                if(event.durum){
                    var ucretlendirilen = event.ucretlendirilen;
                    var yetkili = event.yetkili;
                    var kullanici = event.kullanici;
                    var yetkilisayi = event.yetkilisayi;
                    if(yetkilisayi>1) //yetkili listesini getir
                    {
                        oTable9.clear().draw();
                        $.each(yetkili,function(index){
                            if(yetkili[index].kullanici) {
                                oTable9.row.add([yetkili[index].id, yetkili[index].kullanici.adi_soyadi, yetkili[index].telefon, yetkili[index].email]).draw();
                            }else{
                                oTable9.row.add([yetkili[index].id, "", yetkili[index].telefon, yetkili[index].email]).draw();
                            }
                        });
                        $("#yetkilisecilen").val('');
                        $("#yetkilisecilenadi").val('');
                        $("#yetkilisecilentel").val('');
                        $("#yetkilisecilenemail").val('');
                        $("#yetkililistesi").modal('show');

                    }else if(yetkilisayi===1){
                        if(yetkili[0].kullanici){
                            $("#teladisoyadi").val(yetkili[0].kullanici.adi_soyadi);
                            $("#telyetkilitel").val(yetkili[0].telefon);
                            $("#telyetkilimail").val(yetkili[0].email);
                            $("#telyetkiliid").val(yetkili[0].id);
                        }else{
                            $("#teladisoyadi").val('');
                            $("#telyetkilitel").val('');
                            $("#telyetkilimail").val('');
                            $("#telyetkiliid").val('');
                            toastr["warning"]('Bu Yetkilinin kullanıcı bilgisi mevcut değil.Bu bilgilerin Admin tarafından girilmesi gerekli!', 'Yetkili Hatası');
                        }
                    }else{
                        $("#teladisoyadi").val('');
                        $("#telyetkilitel").val('');
                        $("#telyetkilimail").val('');
                        $("#telyetkiliid").val('');
                        toastr["warning"]('Bu Yere ait yetkili kayıdı mevcut değil.Bu bilgilerin girilmesi gerekli!', 'Yetkili Hatası');
                    }
                    $(".telcariadi").html(ucretlendirilen.netsiscari.cariadi);
                    $('#teltumu').val(ucretlendirilen.secilenler);
                    $('#telgaranti').val(ucretlendirilen.garanti);
                    $('#telid').val(ucretlendirilen.id);
                    $('#telsecilenler').val(ucretlendirilen.secilenler);
                    $('#teladet').val(ucretlendirilen.sayacsayisi);
                    oTable10.clear().draw();
                    var yetkiliid=$("#telyetkiliid").val();
                    $("#yetkilibulsecilen").val('');
                    $("#yetkilibulsecilenadi").val('');
                    $("#yetkilibulsecilentel").val('');
                    $("#yetkilibulsecilenemail").val('');
                    $.each(yetkili,function(index){
                        if(yetkili[index].kullanici.id===yetkiliid){
                            oTable10.row.add([yetkili[index].id,yetkili[index].kullanici.adi_soyadi,yetkili[index].telefon,yetkili[index].email]).draw().nodes().to$().addClass( 'active' );
                            $("#yetkilibulsecilen").val(yetkili[index].kullanici.id);
                            $("#yetkilibulsecilenadi").val(yetkili[index].kullanici.adi_soyadi);
                            $("#yetkilibulsecilentel").val(yetkili[index].telefon);
                            $("#yetkilibulsecilenemail").val(yetkili[index].email);
                        }else{
                            oTable10.row.add([yetkili[index].id,yetkili[index].kullanici.adi_soyadi,yetkili[index].telefon,yetkili[index].email]).draw();
                        }
                    });
                    $.each(kullanici,function(index){
                        oTable10.row.add(['0_'+kullanici[index].id,kullanici[index].adi_soyadi,kullanici[index].telefon,kullanici[index].email]).draw();
                    });
                    oTable7.clear().draw();
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
                    $('.telwarning').html('<span style="color:red">Kur Tarihi: '+dovizkurutarih+'. Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
                    $('.teleuro').html('Euro : '+euro+' ₺');
                    $('.teldolar').html('Dolar : '+dolar+' ₺');
                    $('.telsterlin').html('Sterlin : '+sterlin+' ₺');
                    $('#teldolar').val(dolar);
                    $('#teleuro').val(euro);
                    $('#telsterlin').val(sterlin);
                    var parabirimi = ucretlendirilen.parabirimi;
                    var parabirimi2 = ucretlendirilen.parabirimi2;
                    $('#telbirim').val(parabirimi.id);
                    $('#telbirim2').val(parabirimi2==null ? "" : parabirimi2.id);
                    $('#telbirimi').val(parabirimi.birimi);
                    $('#telbirimi2').val(parabirimi2==null ? "" : parabirimi2.birimi);
                    $('#telkurtarih').val(dovizkurutarih);
                    var arizafiyat=ucretlendirilen.arizafiyat;
                    var kurdurum=false;
                    $.each(arizafiyat,function(index) {
                        var garanti = arizafiyat[index].ariza_garanti==="0" ? 'Dışında' : 'İçinde';
                        var fiyatdurum = arizafiyat[index].fiyatdurum==="0" ? 'Genel' : 'Özel';
                        var sayacadi = arizafiyat[index].sayacadi.sayacadi;
                        var serino = arizafiyat[index].ariza_serino;
                        var birim = arizafiyat[index].parabirimi;
                        var birim2 = arizafiyat[index].parabirimi2;
                        var fiyat = parseFloat(arizafiyat[index].fiyat);
                        var fiyat2 = parseFloat(arizafiyat[index].fiyat2);
                        var indirimorani = parseFloat(arizafiyat[index].indirimorani);
                        var indirim =(fiyat*indirimorani)/100;
                        var indirim2 =(fiyat2*indirimorani)/100;
                        var kdvsiztutar = parseFloat(arizafiyat[index].tutar);
                        var kdvsiztutar2 = parseFloat(arizafiyat[index].tutar2);
                        var kdv = parseFloat(arizafiyat[index].kdv);
                        var kdv2 = parseFloat(arizafiyat[index].kdv2);
                        var toplamtutar = parseFloat(arizafiyat[index].toplamtutar);
                        var toplamtutar2 = parseFloat(arizafiyat[index].toplamtutar2);
                        oTable7.columns( [ 10,11,12,13 ] ).visible( false,false );
                        if(toplamtutar2>0){
                            if(toplamtutar>0){
                                oTable7.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi+' + '+fiyat2.toFixed(2)+' '+birim2.birimi,
                                    indirimorani.toFixed(2)+'%',kdvsiztutar.toFixed(2)+' '+birim.birimi+' + '+kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv.toFixed(2)+' '+birim.birimi+' + '+kdv2.toFixed(2)+' '+birim2.birimi,
                                    toplamtutar.toFixed(2)+' '+birim.birimi+' + '+toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                    fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),birim2.birimi ]).draw()
                                    .nodes().to$().addClass( 'active' );
                            }else{
                                oTable7.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat2.toFixed(2)+' '+birim2.birimi,indirimorani.toFixed(2)+'%',
                                    kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv2.toFixed(2)+' '+birim2.birimi,toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                    fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),birim2.birimi ]).draw()
                                    .nodes().to$().addClass( 'active' );
                            }
                        }else{
                            oTable7.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi,indirimorani.toFixed(2)+'%',
                                kdvsiztutar.toFixed(2)+' '+birim.birimi,kdv.toFixed(2)+' '+birim.birimi,toplamtutar.toFixed(2)+' '+birim.birimi,
                                fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),'' ]).draw()
                                .nodes().to$().addClass( 'active' );
                        }
                        if(birim.id===parabirimi.id){
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            if(birim2!==null){
                                if(birim2.id===parabirimi2.id){
                                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                                }else{
                                    toplamtutar2=0;
                                    $('.telwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.telonayla').prop('disabled', true);
                                }
                            }else{
                                toplamtutar2=0;
                            }
                        }else{
                            var kur = 1;
                            kurdurum=true;
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
                            if(birim2!==null){
                                if(birim2.id===parabirimi.id){
                                    fiyat+=fiyat2;
                                    fiyat2=0;
                                }else if(birim2.id!==parabirimi2.id){
                                    fiyat2=0;
                                    $('.telwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.telonayla').prop('disabled', true);
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
                    if(kurdurum)
                        $('.telkur').removeClass('hide');
                    else
                        $('.telkur').addClass('hide');
                    if(geneltoplamtutar2===0){
                        $('.teltutar').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi);
                        $('.telindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi);
                        $('.telkdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.telkdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.teltoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi);
                    }else{
                        if(geneltoplamtutar === 0){
                            $('.teltutar').text(genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.telindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.telkdvsiztutar').text(+genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.telkdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.teltoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }else{
                            $('.teltutar').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi+' + '+genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.telindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi+' + '+genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.telkdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.telkdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.teltoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }
                    }
                    $('#teltutar').val(genelfiyat.toFixed(2));
                    $('#telindirimtutar').val(genelindirim.toFixed(2));
                    $('#telkdvsiztutar').val(genelkdvsiztutar.toFixed(2));
                    $('#telkdvtutar').val(genelkdvtutar.toFixed(2));
                    $('#teltoplamtutar').val(geneltoplamtutar.toFixed(2));
                    $('#teltutar2').val(genelfiyat2.toFixed(2));
                    $('#telindirimtutar2').val(genelindirim2.toFixed(2));
                    $('#telkdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
                    $('#telkdvtutar2').val(genelkdvtutar2.toFixed(2));
                    $('#teltoplamtutar2').val(geneltoplamtutar2.toFixed(2));
                }else{
                    $('#telefononay').modal('hide');
                    toastr[event.type](event.text,event.title);
                }
                $.unblockUI();
            });
        });
        $(document).on("click", ".mailonay", function () {
            $.blockUI();
            var id = $('#secilen').val();
            var action = $('#form_sample_8').data('action');
            $('#form_sample_8').prop('action',action+'/'+id);
            $.getJSON("{{ URL::to('ucretlendirme/onaybilgi') }}",{id:id},function(event){
                if(event.durum){
                    var ucretlendirilen = event.ucretlendirilen;
                    var yetkili = event.yetkili;
                    var kullanici = event.kullanici;
                    var yetkilisayi = event.yetkilisayi;
                    var mailroot = event.root;
                    var root = $('.root').val();
                    $('.mailroot').val(mailroot);
                    if(yetkilisayi>1) //yetkili listesini getir
                    {
                        oTable9.clear().draw();
                        $.each(yetkili,function(index){
                            if(yetkili[index].kullanici) {
                                oTable9.row.add([yetkili[index].id, yetkili[index].kullanici.adi_soyadi, yetkili[index].telefon, yetkili[index].email]).draw();
                            }else{
                                oTable9.row.add([yetkili[index].id, "", yetkili[index].telefon, yetkili[index].email]).draw();
                            }
                        });
                        $("#yetkilisecilen").val('');
                        $("#yetkilisecilenadi").val('');
                        $("#yetkilisecilentel").val('');
                        $("#yetkilisecilenemail").val('');
                        $("#yetkililistesi").modal('show');

                    }else if(yetkilisayi===1){
                        if(yetkili[0].kullanici){
                            $("#mailadisoyadi").val(yetkili[0].kullanici.adi_soyadi);
                            $("#mailyetkilimail").val(yetkili[0].email);
                            $("#mailyetkiliid").val(yetkili[0].id);
                        }else{
                            $("#mailadisoyadi").val('');
                            $("#mailyetkilimail").val('');
                            $("#mailyetkiliid").val('');
                            toastr["warning"]('Bu Yetkilinin kullanıcı bilgisi mevcut değil.Bu bilgilerin Admin tarafından girilmesi gerekli!', 'Yetkili Hatası');
                        }
                    }else{
                        $("#mailadisoyadi").val('');
                        $("#mailyetkilimail").val('');
                        $("#mailyetkiliid").val('');
                        toastr["warning"]('Bu Yere ait yetkili kayıdı mevcut değil.Bu bilgilerin girilmesi gerekli!', 'Yetkili Hatası');
                    }
                    $(".mailcariadi").html(ucretlendirilen.netsiscari.cariadi);
                    $("#mailkonu").val('Servis Fiyatlandirma ve Onay Formu / '+ucretlendirilen.netsiscari.cariadi);
                    //var serverdis = "http://195.142.123.154:801/ServisTakip/";
                    //var serverdis = "http://servis.manas.com.tr/";
                    var onaysayfa = root+'/musterionay/'+id;
                    var onaysayfadis = mailroot+'/musterionay/'+id;
                    var fiyatlandirma = root+'/ucretlendirme/fiyatlandirmatablo/'+id+'/'+ucretlendirilen.secilenler;
                    var onayform = root+'/ucretlendirme/onayform/'+id+'/'+ucretlendirilen.secilenler;
                    var caribilgi = root+'/pages/storage/cari.pdf';
                    var icerik="<p style='padding-left:30px'>Merhabalar,</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bu eposta tarafı​m​ıza göndermiş olduğunuz saya​cınızın / sayaçlarınızın tamir ve bakımı için gönderilmiştir."
                            + " Ekte bulunan Fiyatlandırma Tablosu ve Onay Formuna bakarak saya​cın / sayaçlarınızın tamir ve bakımını onaylayarak bize <a style='font-size:18px' href='"+onaysayfadis+"'>link</a> üzerinden ya da size gönderilen"
                            + " kullanıcı adı ve şifresi ile <a style='font-size:18px' href='"+mailroot+"'>servis.manas.com.tr</a> adresi üzerinden ​7 gün içerisinde ​dönüş yap​manız gerekmektedir. "
                            + " 7 gün içerisinde dönüş yapmadığınız taktirde sayacınız / sayaçlarınız tamir edilmeden , kargo bedeli tarafınızdan ödenecek şekilde tarafınıza sevk edilecektir​.</p>"
                            + " <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;​O​nayınızla beraber tamir bedelini banka hesap numaramıza yatırmanız gerekmektedir . Onayınızı takip eden 10 iş günü içerisinde ( Bayram tatili ve olağanüstü durumlar hariç ,"
                            + " sayacınız / sayaçlarınız ile ilgili yedek parça ve malzeme sorunu olmadığı taktirde )  ​tamiratı yapılarak , sayaçınız / sayaçlarınız ​tarafınıza gönderilecektir.​</p><p style='padding-left:30px'>Saygılarımızla.</p>";
                    tinyMCE.get('mailicerik').setContent(icerik);
                    $('#icerik').val(icerik);
                    $('#maillink').prop('href',onaysayfa);
                    $('.maillink').val(onaysayfadis);
                    $('#mailek1').prop('href',fiyatlandirma);
                    $('#mailek2').prop('href',onayform);
                    $('#caribilgi').prop('href',caribilgi);
                    $('#mailtumu').val(ucretlendirilen.secilenler);
                    $('#mailgaranti').val(ucretlendirilen.garanti);
                    $('#mailid').val(ucretlendirilen.id);
                    $('#mailsecilenler').val(ucretlendirilen.secilenler);
                    $('#mailadet').val(ucretlendirilen.sayacsayisi);
                    oTable10.clear().draw();
                    var yetkiliid=$("#mailyetkiliid").val();
                    $("#yetkilibulsecilen").val('');
                    $("#yetkilibulsecilenadi").val('');
                    $("#yetkilibulsecilentel").val('');
                    $("#yetkilibulsecilenemail").val('');
                    $.each(yetkili,function(index){
                        if(yetkili[index].kullanici.id===yetkiliid){
                            oTable10.row.add([yetkili[index].id,yetkili[index].kullanici.adi_soyadi,yetkili[index].telefon,yetkili[index].email]).draw().nodes().to$().addClass( 'active' );
                            $("#yetkilibulsecilen").val(yetkili[index].kullanici.id);
                            $("#yetkilibulsecilenadi").val(yetkili[index].kullanici.adi_soyadi);
                            $("#yetkilibulsecilentel").val(yetkili[index].telefon);
                            $("#yetkilibulsecilenemail").val(yetkili[index].email);
                        }else{
                            oTable10.row.add([yetkili[index].id,yetkili[index].kullanici.adi_soyadi,yetkili[index].telefon,yetkili[index].email]).draw();
                        }
                    });
                    $.each(kullanici,function(index){
                        oTable10.row.add(['0_'+kullanici[index].id,kullanici[index].adi_soyadi,kullanici[index].telefon,kullanici[index].email]).draw();
                    });
                    oTable8.clear().draw();
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
                    $('.mailwarning').html('<span style="color:red">Kur Tarihi: '+dovizkurutarih+'. Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
                    $('.maileuro').html('Euro : '+euro+' ₺');
                    $('.maildolar').html('Dolar : '+dolar+' ₺');
                    $('.mailsterlin').html('Sterlin : '+sterlin+' ₺');
                    $('#maildolar').val(dolar);
                    $('#maileuro').val(euro);
                    $('#mailsterlin').val(sterlin);
                    var parabirimi = ucretlendirilen.parabirimi;
                    var parabirimi2 = ucretlendirilen.parabirimi2;
                    $('#mailbirim').val(parabirimi.id);
                    $('#mailbirim2').val(parabirimi2==null ? "" : parabirimi2.id);
                    $('#mailbirimi').val(parabirimi.birimi);
                    $('#mailbirimi2').val(parabirimi2==null ? "" : parabirimi2.birimi);
                    $('#mailkurtarih').val(dovizkurutarih);
                    var arizafiyat=ucretlendirilen.arizafiyat;
                    var kurdurum=false;
                    $.each(arizafiyat,function(index) {
                        var garanti = arizafiyat[index].ariza_garanti==="0" ? 'Dışında' : 'İçinde';
                        var fiyatdurum = arizafiyat[index].fiyatdurum==="0" ? 'Genel' : 'Özel';
                        var sayacadi = arizafiyat[index].sayacadi.sayacadi;
                        var serino = arizafiyat[index].ariza_serino;
                        var birim = arizafiyat[index].parabirimi;
                        var birim2 = arizafiyat[index].parabirimi2;
                        var fiyat = parseFloat(arizafiyat[index].fiyat);
                        var fiyat2 = parseFloat(arizafiyat[index].fiyat2);
                        var indirimorani = parseFloat(arizafiyat[index].indirimorani);
                        var indirim =(fiyat*indirimorani)/100;
                        var indirim2 =(fiyat2*indirimorani)/100;
                        var kdvsiztutar = parseFloat(arizafiyat[index].tutar);
                        var kdvsiztutar2 = parseFloat(arizafiyat[index].tutar2);
                        var kdv = parseFloat(arizafiyat[index].kdv);
                        var kdv2 = parseFloat(arizafiyat[index].kdv2);
                        var toplamtutar = parseFloat(arizafiyat[index].toplamtutar);
                        var toplamtutar2 = parseFloat(arizafiyat[index].toplamtutar2);
                        oTable8.columns( [ 10,11,12,13 ] ).visible( false,false );
                        if(toplamtutar2>0){
                            if(toplamtutar>0){
                                oTable8.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi+' + '+fiyat2.toFixed(2)+' '+birim2.birimi,
                                    indirimorani.toFixed(2)+'%',kdvsiztutar.toFixed(2)+' '+birim.birimi+' + '+kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv.toFixed(2)+' '+birim.birimi+' + '+kdv2.toFixed(2)+' '+birim2.birimi,
                                    toplamtutar.toFixed(2)+' '+birim.birimi+' + '+toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                    fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),birim2.birimi]).draw()
                                    .nodes().to$().addClass( 'active' );
                            }else{
                                oTable8.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat2.toFixed(2)+' '+birim2.birimi,indirimorani.toFixed(2)+'%',
                                    kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv2.toFixed(2)+' '+birim2.birimi,toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                    fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),birim2.birimi ]).draw()
                                    .nodes().to$().addClass( 'active' );
                            }
                        }else{
                            oTable8.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi,indirimorani.toFixed(2)+'%',
                                kdvsiztutar.toFixed(2)+' '+birim.birimi,kdv.toFixed(2)+' '+birim.birimi,toplamtutar.toFixed(2)+' '+birim.birimi,
                                fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),'' ]).draw()
                                .nodes().to$().addClass( 'active' );
                        }
                        if(birim.id===parabirimi.id){
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            if(birim2!==null){
                                if(birim2.id===parabirimi2.id){
                                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                                }else{
                                    toplamtutar2=0;
                                    $('.mailwarning').html('<span style="color:red">İki Parabiriminden Fazla.</span>');
                                    $('.mailonayla').prop('disabled', true);
                                }
                            }else{
                                toplamtutar2=0;
                            }
                        }else{
                            var kur = 1;
                            kurdurum=true;
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
                            if(birim2!==null){
                                if(birim2.id===parabirimi.id){
                                    fiyat+=fiyat2;
                                    fiyat2=0;
                                }else if(birim2.id!==parabirimi2.id){
                                    fiyat2=0;
                                    $('.mailwarning').html('<span style="color:red">İki Parabiriminden Fazla.</span>');
                                    $('.mailonayla').prop('disabled', true);
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
                    if(kurdurum)
                        $('.mailkur').removeClass('hide');
                    else
                        $('.mailkur').addClass('hide');
                    if(geneltoplamtutar2 === 0){
                        $('.mailtutar').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi);
                        $('.mailindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi);
                        $('.mailkdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.mailkdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.mailtoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi);
                    }else{
                        if(geneltoplamtutar === 0) {
                            $('.mailtutar').text(genelfiyat2.toFixed(2) + ' ' + parabirimi2.birimi);
                            $('.mailindirimtutar').text(genelindirim2.toFixed(2) + ' ' + parabirimi2.birimi);
                            $('.mailkdvsiztutar').text(genelkdvsiztutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                            $('.mailkdvtutar').text(genelkdvtutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                            $('.mailtoplamtutar').text(geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2.birimi);
                        }else{
                            $('.mailtutar').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi+' + '+genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.mailindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi+' + '+genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.mailkdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.mailkdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.mailtoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }
                    }
                    $('#mailtutar').val(genelfiyat.toFixed(2));
                    $('#mailindirimtutar').val(genelindirim.toFixed(2));
                    $('#mailkdvsiztutar').val(genelkdvsiztutar.toFixed(2));
                    $('#mailkdvtutar').val(genelkdvtutar.toFixed(2));
                    $('#mailtoplamtutar').val(geneltoplamtutar.toFixed(2));
                    $('#mailtutar2').val(genelfiyat2.toFixed(2));
                    $('#mailindirimtutar2').val(genelindirim2.toFixed(2));
                    $('#mailkdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
                    $('#mailkdvtutar2').val(genelkdvtutar2.toFixed(2));
                    $('#mailtoplamtutar2').val(geneltoplamtutar2.toFixed(2));
                }else{
                    $('#mailonay').modal('hide');
                    toastr[event.type](event.text,event.title);
                }
                $.unblockUI();
            });
        });
        $(document).on("click", ".mailtekrar", function () {
            $.blockUI();
            var id = $('#secilen').val();
            var action = $('#form_sample_14').data('action');
            $('#form_sample_14').prop('action',action+'/'+id);
            $.getJSON("{{ URL::to('ucretlendirme/onaybilgi') }}",{id:id},function(event){
                if(event.durum){
                    var ucretlendirilen = event.ucretlendirilen;
                    var yetkili = event.yetkili;
                    var kullanici = event.kullanici;
                    var yetkilisayi = event.yetkilisayi;
                    var mailroot = event.root;
                    var root = $('.root').val();
                    $('.tekrarmailroot').val(mailroot);
                    if(yetkilisayi>1) //yetkili listesini getir
                    {
                        oTable9.clear().draw();
                        $.each(yetkili,function(index){
                            if(yetkili[index].kullanici) {
                                oTable9.row.add([yetkili[index].id, yetkili[index].kullanici.adi_soyadi, yetkili[index].telefon, yetkili[index].email]).draw();
                            }else{
                                oTable9.row.add([yetkili[index].id, "", yetkili[index].telefon, yetkili[index].email]).draw();
                            }
                        });
                        $("#yetkilisecilen").val('');
                        $("#yetkilisecilenadi").val('');
                        $("#yetkilisecilentel").val('');
                        $("#yetkilisecilenemail").val('');
                        $("#yetkililistesi").modal('show');

                    }else if(yetkilisayi===1){
                        if(yetkili[0].kullanici){
                            $("#tekrarmailadisoyadi").val(yetkili[0].kullanici.adi_soyadi);
                            $("#tekrarmailyetkilimail").val(yetkili[0].email);
                            $("#tekrarmailyetkiliid").val(yetkili[0].id);
                        }else{
                            $("#tekrarmailadisoyadi").val('');
                            $("#tekrarmailyetkilimail").val('');
                            $("#tekrarmailyetkiliid").val('');
                            toastr["warning"]('Bu Yetkilinin kullanıcı bilgisi mevcut değil.Bu bilgilerin Admin tarafından girilmesi gerekli!', 'Yetkili Hatası');
                        }
                    }else{
                        $("#tekrarmailadisoyadi").val('');
                        $("#tekrarmailyetkilimail").val('');
                        $("#tekrarmailyetkiliid").val('');
                        toastr["warning"]('Bu Yere ait yetkili kayıdı mevcut değil.Bu bilgilerin girilmesi gerekli!', 'Yetkili Hatası');
                    }
                    $(".tekrarmailcariadi").html(ucretlendirilen.netsiscari.cariadi);
                    $("#tekrarmailkonu").val('Servis Fiyatlandirma ve Onay Formu / '+ucretlendirilen.netsiscari.cariadi);
                    //var serverdis = "http://195.142.123.154:801/ServisTakip/";
                    //var serverdis = "http://servis.manas.com.tr/";
                    var onaysayfa = root+'/musterionay/'+id;
                    var onaysayfadis = mailroot+'/musterionay/'+id;
                    var fiyatlandirma = root+'/ucretlendirme/fiyatlandirmatablo/'+id+'/'+ucretlendirilen.secilenler;
                    var onayform = root+'/ucretlendirme/onayform/'+id+'/'+ucretlendirilen.secilenler;
                    var caribilgi = root+'/pages/storage/cari.pdf';
                    var icerik="<p style='padding-left:30px'>Merhabalar,</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bu eposta tarafı​m​ıza göndermiş olduğunuz saya​cınızın / sayaçlarınızın tamir ve bakımı için gönderilmiştir."
                        + " Ekte bulunan Fiyatlandırma Tablosu ve Onay Formuna bakarak saya​cın / sayaçlarınızın tamir ve bakımını onaylayarak bize <a style='font-size:18px' href='"+onaysayfadis+"'>link</a> üzerinden ya da size gönderilen"
                        + " kullanıcı adı ve şifresi ile <a style='font-size:18px' href='//servis.manas.com.tr'>servis.manas.com.tr</a> adresi üzerinden ​7 gün içerisinde ​dönüş yap​manız gerekmektedir. "
                        + " 7 gün içerisinde dönüş yapmadığınız taktirde sayacınız / sayaçlarınız tamir edilmeden , kargo bedeli tarafınızdan ödenecek şekilde tarafınıza sevk edilecektir​.</p>"
                        + " <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;​O​nayınızla beraber tamir bedelini banka hesap numaramıza yatırmanız gerekmektedir . Onayınızı takip eden 10 iş günü içerisinde ( Bayram tatili ve olağanüstü durumlar hariç ,"
                        + " sayacınız / sayaçlarınız ile ilgili yedek parça ve malzeme sorunu olmadığı taktirde )  ​tamiratı yapılarak , sayaçınız / sayaçlarınız ​tarafınıza gönderilecektir.​</p><p style='padding-left:30px'>Saygılarımızla.</p>";
                    tinyMCE.get('tekrarmailicerik').setContent(icerik);
                    $('#tekraricerik').val(icerik);
                    $('#tekrarmaillink').prop('href',onaysayfa);
                    $('.tekrarmaillink').val(onaysayfadis);
                    $('#tekrarmailek1').prop('href',fiyatlandirma);
                    $('#tekrarmailek2').prop('href',onayform);
                    $('#tekrarcaribilgi').prop('href',caribilgi);
                    $('#tekrarmailtumu').val(ucretlendirilen.secilenler);
                    $('#tekrarmailgaranti').val(ucretlendirilen.garanti);
                    $('#tekrarmailid').val(ucretlendirilen.id);
                    $('#tekrarmailsecilenler').val(ucretlendirilen.secilenler);
                    $('#tekrarmailadet').val(ucretlendirilen.sayacsayisi);
                    oTable10.clear().draw();
                    var yetkiliid=$("#tekrarmailyetkiliid").val();
                    $("#yetkilibulsecilen").val('');
                    $("#yetkilibulsecilenadi").val('');
                    $("#yetkilibulsecilentel").val('');
                    $("#yetkilibulsecilenemail").val('');
                    $.each(yetkili,function(index){
                        if(yetkili[index].kullanici.id===yetkiliid){
                            oTable10.row.add([yetkili[index].id,yetkili[index].kullanici.adi_soyadi,yetkili[index].telefon,yetkili[index].email]).draw().nodes().to$().addClass( 'active' );
                            $("#yetkilibulsecilen").val(yetkili[index].kullanici.id);
                            $("#yetkilibulsecilenadi").val(yetkili[index].kullanici.adi_soyadi);
                            $("#yetkilibulsecilentel").val(yetkili[index].telefon);
                            $("#yetkilibulsecilenemail").val(yetkili[index].email);
                        }else{
                            oTable10.row.add([yetkili[index].id,yetkili[index].kullanici.adi_soyadi,yetkili[index].telefon,yetkili[index].email]).draw();
                        }
                    });
                    $.each(kullanici,function(index){
                        oTable10.row.add(['0_'+kullanici[index].id,kullanici[index].adi_soyadi,kullanici[index].telefon,kullanici[index].email]).draw();
                    });
                    oTable14.clear().draw();
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
                    $('.tekrarmailwarning').html('<span style="color:red">Kur Tarihi: '+dovizkurutarih+'. Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
                    $('.tekrarmaileuro').html('Euro : '+euro+' ₺');
                    $('.tekrarmaildolar').html('Dolar : '+dolar+' ₺');
                    $('.tekrarmailsterlin').html('Sterlin : '+sterlin+' ₺');
                    $('#tekrarmaildolar').val(dolar);
                    $('#tekrarmaileuro').val(euro);
                    $('#tekrarmailsterlin').val(sterlin);
                    var parabirimi = ucretlendirilen.parabirimi;
                    var parabirimi2 = ucretlendirilen.parabirimi2;
                    $('#tekrarmailbirim').val(parabirimi.id);
                    $('#tekrarmailbirim2').val(parabirimi2==null ? "" : parabirimi2.id);
                    $('#tekrarmailbirimi').val(parabirimi.birimi);
                    $('#tekrarmailbirimi2').val(parabirimi2==null ? "" : parabirimi2.birimi);
                    $('#tekrarmailkurtarih').val(dovizkurutarih);
                    var arizafiyat=ucretlendirilen.arizafiyat;
                    var kurdurum=false;
                    $.each(arizafiyat,function(index) {
                        var garanti = arizafiyat[index].ariza_garanti==="0" ? 'Dışında' : 'İçinde';
                        var fiyatdurum = arizafiyat[index].fiyatdurum==="0" ? 'Genel' : 'Özel';
                        var sayacadi = arizafiyat[index].sayacadi.sayacadi;
                        var serino = arizafiyat[index].ariza_serino;
                        var birim = arizafiyat[index].parabirimi;
                        var birim2 = arizafiyat[index].parabirimi2;
                        var fiyat = parseFloat(arizafiyat[index].fiyat);
                        var fiyat2 = parseFloat(arizafiyat[index].fiyat2);
                        var indirimorani = parseFloat(arizafiyat[index].indirimorani);
                        var indirim =(fiyat*indirimorani)/100;
                        var indirim2 =(fiyat2*indirimorani)/100;
                        var kdvsiztutar = parseFloat(arizafiyat[index].tutar);
                        var kdvsiztutar2 = parseFloat(arizafiyat[index].tutar2);
                        var kdv = parseFloat(arizafiyat[index].kdv);
                        var kdv2 = parseFloat(arizafiyat[index].kdv2);
                        var toplamtutar = parseFloat(arizafiyat[index].toplamtutar);
                        var toplamtutar2 = parseFloat(arizafiyat[index].toplamtutar2);
                        oTable14.columns( [ 10,11,12,13 ] ).visible( false,false );
                        if(toplamtutar2>0){
                            if(toplamtutar>0){
                                oTable14.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi+' + '+fiyat2.toFixed(2)+' '+birim2.birimi,
                                    indirimorani.toFixed(2)+'%',kdvsiztutar.toFixed(2)+' '+birim.birimi+' + '+kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv.toFixed(2)+' '+birim.birimi+' + '+kdv2.toFixed(2)+' '+birim2.birimi,
                                    toplamtutar.toFixed(2)+' '+birim.birimi+' + '+toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                    fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),birim2.birimi]).draw()
                                    .nodes().to$().addClass( 'active' );
                            }else{
                                oTable14.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat2.toFixed(2)+' '+birim2.birimi,indirimorani.toFixed(2)+'%',
                                    kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv2.toFixed(2)+' '+birim2.birimi,toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                    fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),birim2.birimi ]).draw()
                                    .nodes().to$().addClass( 'active' );
                            }
                        }else{
                            oTable14.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi,indirimorani.toFixed(2)+'%',
                                kdvsiztutar.toFixed(2)+' '+birim.birimi,kdv.toFixed(2)+' '+birim.birimi,toplamtutar.toFixed(2)+' '+birim.birimi,
                                fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),'' ]).draw()
                                .nodes().to$().addClass( 'active' );
                        }
                        if(birim.id===parabirimi.id){
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            if(birim2!==null){
                                if(birim2.id===parabirimi2.id){
                                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                                }else{
                                    toplamtutar2=0;
                                    $('.tekrarmailwarning').html('<span style="color:red">İki Parabiriminden Fazla.</span>');
                                    $('.tekrarmailgonder').prop('disabled', true);
                                }
                            }else{
                                toplamtutar2=0;
                            }
                        }else{
                            var kur = 1;
                            kurdurum=true;
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
                            if(birim2!==null){
                                if(birim2.id===parabirimi.id){
                                    fiyat+=fiyat2;
                                    fiyat2=0;
                                }else if(birim2.id!==parabirimi2.id){
                                    fiyat2=0;
                                    $('.mailwarning').html('<span style="color:red">İki Parabiriminden Fazla.</span>');
                                    $('.mailonayla').prop('disabled', true);
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
                    if(kurdurum)
                        $('.tekrarmailkur').removeClass('hide');
                    else
                        $('.tekrarmailkur').addClass('hide');
                    if(geneltoplamtutar2===0){
                        $('.tekrarmailtutar').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi);
                        $('.tekrarmailindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi);
                        $('.tekrarmailkdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.tekrarmailkdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.tekrarmailtoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi);
                    }else{
                        if(geneltoplamtutar === 0){
                            $('.tekrarmailtutar').text(genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.tekrarmailindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.tekrarmailkdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.tekrarmailkdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.tekrarmailtoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }else{
                            $('.tekrarmailtutar').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi+' + '+genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.tekrarmailindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi+' + '+genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.tekrarmailkdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.tekrarmailkdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.tekrarmailtoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }
                    }
                    $('#tekrarmailtutar').val(genelfiyat.toFixed(2));
                    $('#tekrarmailindirimtutar').val(genelindirim.toFixed(2));
                    $('#tekrarmailkdvsiztutar').val(genelkdvsiztutar.toFixed(2));
                    $('#tekrarmailkdvtutar').val(genelkdvtutar.toFixed(2));
                    $('#tekrarmailtoplamtutar').val(geneltoplamtutar.toFixed(2));
                    $('#tekrarmailtutar2').val(genelfiyat2.toFixed(2));
                    $('#tekrarmailindirimtutar2').val(genelindirim2.toFixed(2));
                    $('#tekrarmailkdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
                    $('#tekrarmailkdvtutar2').val(genelkdvtutar2.toFixed(2));
                    $('#tekrarmailtoplamtutar2').val(geneltoplamtutar2.toFixed(2));
                }else{
                    $('#mailtekrar').modal('hide');
                    toastr[event.type](event.text,event.title);
                }
                $.unblockUI();
            });
        });
        $(document).on("click", ".mailtelefon", function () {
            $.blockUI();
            var id = $('#secilen').val();
            var action = $('#form_sample_15').data('action');
            $('#form_sample_15').prop('action',action+'/'+id);
            $.getJSON("{{ URL::to('ucretlendirme/onaybilgi') }}",{id:id},function(event){
                if(event.durum){
                    var ucretlendirilen = event.ucretlendirilen;
                    var yetkili = event.yetkili;
                    var kullanici = event.kullanici;
                    var yetkilisayi = event.yetkilisayi;
                    if(yetkilisayi>1) //yetkili listesini getir
                    {
                        oTable9.clear().draw();
                        $.each(yetkili,function(index){
                            if(yetkili[index].kullanici) {
                                oTable9.row.add([yetkili[index].id, yetkili[index].kullanici.adi_soyadi, yetkili[index].telefon, yetkili[index].email]).draw();
                            }else{
                                oTable9.row.add([yetkili[index].id, "", yetkili[index].telefon, yetkili[index].email]).draw();
                            }
                        });
                        $("#yetkilisecilen").val('');
                        $("#yetkilisecilenadi").val('');
                        $("#yetkilisecilentel").val('');
                        $("#yetkilisecilenemail").val('');
                        $("#yetkililistesi").modal('show');

                    }else if(yetkilisayi===1){
                        if(yetkili[0].kullanici){
                            $("#mailteladisoyadi").val(yetkili[0].kullanici.adi_soyadi);
                            $("#mailtelyetkilitel").val(yetkili[0].telefon);
                            $("#mailtelyetkilimail").val(yetkili[0].email);
                            $("#mailtelyetkiliid").val(yetkili[0].id);
                        }else{
                            $("#mailteladisoyadi").val('');
                            $("#mailtelyetkilitel").val('');
                            $("#mailtelyetkilimail").val('');
                            $("#mailtelyetkiliid").val('');
                            toastr["warning"]('Bu Yetkilinin kullanıcı bilgisi mevcut değil.Bu bilgilerin Admin tarafından girilmesi gerekli!', 'Yetkili Hatası');
                        }
                    }else{
                        $("#mailteladisoyadi").val('');
                        $("#mailtelyetkilitel").val('');
                        $("#mailtelyetkilimail").val('');
                        $("#mailtelyetkiliid").val('');
                        toastr["warning"]('Bu Yere ait yetkili kayıdı mevcut değil.Bu bilgilerin girilmesi gerekli!', 'Yetkili Hatası');
                    }
                    $(".mailtelcariadi").html(ucretlendirilen.netsiscari.cariadi);
                    $('#mailteltumu').val(ucretlendirilen.secilenler);
                    $('#mailtelgaranti').val(ucretlendirilen.garanti);
                    $('#mailtelid').val(ucretlendirilen.id);
                    $('#mailtelsecilenler').val(ucretlendirilen.secilenler);
                    $('#mailteladet').val(ucretlendirilen.sayacsayisi);
                    oTable10.clear().draw();
                    var yetkiliid=$("#mailtelyetkiliid").val();
                    $("#yetkilibulsecilen").val('');
                    $("#yetkilibulsecilenadi").val('');
                    $("#yetkilibulsecilentel").val('');
                    $("#yetkilibulsecilenemail").val('');
                    $.each(yetkili,function(index){
                        if(yetkili[index].kullanici.id===yetkiliid){
                            oTable10.row.add([yetkili[index].id,yetkili[index].kullanici.adi_soyadi,yetkili[index].telefon,yetkili[index].email]).draw().nodes().to$().addClass( 'active' );
                            $("#yetkilibulsecilen").val(yetkili[index].kullanici.id);
                            $("#yetkilibulsecilenadi").val(yetkili[index].kullanici.adi_soyadi);
                            $("#yetkilibulsecilentel").val(yetkili[index].telefon);
                            $("#yetkilibulsecilenemail").val(yetkili[index].email);
                        }else{
                            oTable10.row.add([yetkili[index].id,yetkili[index].kullanici.adi_soyadi,yetkili[index].telefon,yetkili[index].email]).draw();
                        }
                    });
                    $.each(kullanici,function(index){
                        oTable10.row.add(['0_'+kullanici[index].id,kullanici[index].adi_soyadi,kullanici[index].telefon,kullanici[index].email]).draw();
                    });
                    oTable15.clear().draw();
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
                    $('.mailtelwarning').html('<span style="color:red">Kur Tarihi: '+dovizkurutarih+'. Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
                    $('.mailteleuro').html('Euro : '+euro+' ₺');
                    $('.mailteldolar').html('Dolar : '+dolar+' ₺');
                    $('.mailtelsterlin').html('Sterlin : '+sterlin+' ₺');
                    $('#mailteldolar').val(dolar);
                    $('#mailteleuro').val(euro);
                    $('#mailtelsterlin').val(sterlin);
                    var parabirimi = ucretlendirilen.parabirimi;
                    var parabirimi2 = ucretlendirilen.parabirimi2;
                    $('#mailtelbirim').val(parabirimi.id);
                    $('#mailtelbirim2').val(parabirimi2==null ? "" : parabirimi2.id);
                    $('#mailtelbirimi').val(parabirimi.birimi);
                    $('#mailtelbirimi2').val(parabirimi2==null ? "" : parabirimi2.birimi);
                    $('#mailtelkurtarih').val(dovizkurutarih);
                    var arizafiyat=ucretlendirilen.arizafiyat;
                    var kurdurum=false;
                    $.each(arizafiyat,function(index) {
                        var garanti = arizafiyat[index].ariza_garanti==="0" ? 'Dışında' : 'İçinde';
                        var fiyatdurum = arizafiyat[index].fiyatdurum==="0" ? 'Genel' : 'Özel';
                        var sayacadi = arizafiyat[index].sayacadi.sayacadi;
                        var serino = arizafiyat[index].ariza_serino;
                        var birim = arizafiyat[index].parabirimi;
                        var birim2 = arizafiyat[index].parabirimi2;
                        var fiyat = parseFloat(arizafiyat[index].fiyat);
                        var fiyat2 = parseFloat(arizafiyat[index].fiyat2);
                        var indirimorani = parseFloat(arizafiyat[index].indirimorani);
                        var indirim =(fiyat*indirimorani)/100;
                        var indirim2 =(fiyat2*indirimorani)/100;
                        var kdvsiztutar = parseFloat(arizafiyat[index].tutar);
                        var kdvsiztutar2 = parseFloat(arizafiyat[index].tutar2);
                        var kdv = parseFloat(arizafiyat[index].kdv);
                        var kdv2 = parseFloat(arizafiyat[index].kdv2);
                        var toplamtutar = parseFloat(arizafiyat[index].toplamtutar);
                        var toplamtutar2 = parseFloat(arizafiyat[index].toplamtutar2);
                        oTable15.columns( [ 10,11,12,13 ] ).visible( false,false );
                        if(toplamtutar2>0){
                            if(toplamtutar>0){
                                oTable15.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi+' + '+fiyat2.toFixed(2)+' '+birim2.birimi,
                                    indirimorani.toFixed(2)+'%',kdvsiztutar.toFixed(2)+' '+birim.birimi+' + '+kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv.toFixed(2)+' '+birim.birimi+' + '+kdv2.toFixed(2)+' '+birim2.birimi,
                                    toplamtutar.toFixed(2)+' '+birim.birimi+' + '+toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                    fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),birim2.birimi ]).draw()
                                    .nodes().to$().addClass( 'active' );
                            }else{
                                oTable15.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat2.toFixed(2)+' '+birim2.birimi,indirimorani.toFixed(2)+'%',
                                    kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv2.toFixed(2)+' '+birim2.birimi,toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                    fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),birim2.birimi ]).draw()
                                    .nodes().to$().addClass( 'active' );
                            }
                        }else{
                            oTable15.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi,indirimorani.toFixed(2)+'%',
                                kdvsiztutar.toFixed(2)+' '+birim.birimi,kdv.toFixed(2)+' '+birim.birimi,toplamtutar.toFixed(2)+' '+birim.birimi,
                                fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),'' ]).draw()
                                .nodes().to$().addClass( 'active' );
                        }
                        if(birim.id===parabirimi.id){
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            if(birim2!==null){
                                if(birim2.id===parabirimi2.id){
                                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                                }else{
                                    toplamtutar2=0;
                                    $('.mailtelwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.mailtelonayla').prop('disabled', true);
                                }
                            }else{
                                toplamtutar2=0;
                            }
                        }else{
                            var kur = 1;
                            kurdurum=true;
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
                            if(birim2!==null){
                                if(birim2.id===parabirimi.id){
                                    fiyat+=fiyat2;
                                    fiyat2=0;
                                }else if(birim2.id!==parabirimi2.id){
                                    fiyat2=0;
                                    $('.mailtelwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.mailtelonayla').prop('disabled', true);
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
                    if(kurdurum)
                        $('.mailtelkur').removeClass('hide');
                    else
                        $('.mailtelkur').addClass('hide');
                    if(geneltoplamtutar2===0){
                        $('.mailteltutar').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi);
                        $('.mailtelindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi);
                        $('.mailtelkdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.mailtelkdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.mailteltoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi);
                    }else{
                        if(geneltoplamtutar===0){
                            $('.mailteltutar').text(genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.mailtelindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.mailtelkdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.mailtelkdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.mailteltoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }else{
                            $('.mailteltutar').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi+' + '+genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.mailtelindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi+' + '+genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.mailtelkdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.mailtelkdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.mailteltoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }
                    }
                    $('#mailteltutar').val(genelfiyat.toFixed(2));
                    $('#mailtelindirimtutar').val(genelindirim.toFixed(2));
                    $('#mailtelkdvsiztutar').val(genelkdvsiztutar.toFixed(2));
                    $('#mailtelkdvtutar').val(genelkdvtutar.toFixed(2));
                    $('#mailteltoplamtutar').val(geneltoplamtutar.toFixed(2));
                    $('#mailteltutar2').val(genelfiyat2.toFixed(2));
                    $('#mailtelindirimtutar2').val(genelindirim2.toFixed(2));
                    $('#mailtelkdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
                    $('#mailtelkdvtutar2').val(genelkdvtutar2.toFixed(2));
                    $('#mailteltoplamtutar2').val(geneltoplamtutar2.toFixed(2));
                }else{
                    $('#mailtelefononay').modal('hide');
                    toastr[event.type](event.text,event.title);
                }
                $.unblockUI();
            });
        });
        $(document).on("click", ".yetkilionay", function () {
            $.blockUI();
            var id = $('#secilen').val();
            var action = $('#form_sample_12').data('action');
            $('#form_sample_12').prop('action',action+'/'+id);
            $.getJSON("{{ URL::to('ucretlendirme/yetkilibilgi') }}",{id:id},function(event){
                if(event.durum){
                    var ucretlendirilen = event.ucretlendirilen;
                    var yetkili = event.yetkili;
                    $("#yetkiliid").val(yetkili.id);
                    $(".onaycariadi").html(ucretlendirilen.netsiscari.cariadi);
                    $('#onaytumu').val(ucretlendirilen.secilenler);
                    $('#onaygaranti').val(ucretlendirilen.garanti);
                    $('#onayid').val(ucretlendirilen.id);
                    $('#onaysecilenler').val(ucretlendirilen.secilenler);
                    $('#onayadet').val(ucretlendirilen.sayacsayisi);
                    oTable12.clear().draw();
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
                    $('.onaywarning').html('<span style="color:red">Kur Tarihi: '+dovizkurutarih+'. Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
                    $('.onayeuro').html('Euro : '+euro+' ₺');
                    $('.onaydolar').html('Dolar : '+dolar+' ₺');
                    $('.onaysterlin').html('Sterlin : '+sterlin+' ₺');
                    $('#onaydolar').val(dolar);
                    $('#onayeuro').val(euro);
                    $('#onaysterlin').val(sterlin);
                    var parabirimi = ucretlendirilen.parabirimi;
                    var parabirimi2 = ucretlendirilen.parabirimi2;
                    $('#onaybirim').val(parabirimi.id);
                    $('#onaybirim2').val(parabirimi2==null ? "" : parabirimi2.id);
                    $('#onaybirimi').val(parabirimi.birimi);
                    $('#onaybirimi2').val(parabirimi2==null ? "" : parabirimi2.birimi);
                    $('#onaykurtarih').val(dovizkurutarih);
                    var arizafiyat=ucretlendirilen.arizafiyat;
                    var kurdurum=false;
                    $.each(arizafiyat,function(index) {
                        var garanti = arizafiyat[index].ariza_garanti==="0" ? 'Dışında' : 'İçinde';
                        var fiyatdurum = arizafiyat[index].fiyatdurum==="0" ? 'Genel' : 'Özel';
                        var sayacadi = arizafiyat[index].sayacadi.sayacadi;
                        var serino = arizafiyat[index].ariza_serino;
                        var birim = arizafiyat[index].parabirimi;
                        var birim2 = arizafiyat[index].parabirimi2;
                        var fiyat = parseFloat(arizafiyat[index].fiyat);
                        var fiyat2 = parseFloat(arizafiyat[index].fiyat2);
                        var indirimorani = parseFloat(arizafiyat[index].indirimorani);
                        var indirim =(fiyat*indirimorani)/100;
                        var indirim2 =(fiyat2*indirimorani)/100;
                        var kdvsiztutar = parseFloat(arizafiyat[index].tutar);
                        var kdvsiztutar2 = parseFloat(arizafiyat[index].tutar2);
                        var kdv = parseFloat(arizafiyat[index].kdv);
                        var kdv2 = parseFloat(arizafiyat[index].kdv2);
                        var toplamtutar = parseFloat(arizafiyat[index].toplamtutar);
                        var toplamtutar2 = parseFloat(arizafiyat[index].toplamtutar2);
                        oTable12.columns( [ 10,11,12,13 ] ).visible( false,false );
                        if(toplamtutar2>0){
                            if(toplamtutar>0){
                                oTable12.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi+' + '+fiyat2.toFixed(2)+' '+birim2.birimi,
                                    indirimorani.toFixed(2)+'%',kdvsiztutar.toFixed(2)+' '+birim.birimi+' + '+kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv.toFixed(2)+' '+birim.birimi+' + '+kdv2.toFixed(2)+' '+birim2.birimi,
                                    toplamtutar.toFixed(2)+' '+birim.birimi+' + '+toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                    fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),birim2.birimi ]).draw()
                                    .nodes().to$().addClass( 'active' );
                            }else{
                                oTable12.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat2.toFixed(2)+' '+birim2.birimi,indirimorani.toFixed(2)+'%',
                                    kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv2.toFixed(2)+' '+birim2.birimi,toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                    fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),birim2.birimi ]).draw()
                                    .nodes().to$().addClass( 'active' );
                            }
                        }else{
                            oTable12.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi,indirimorani.toFixed(2)+'%',
                                kdvsiztutar.toFixed(2)+' '+birim.birimi,kdv.toFixed(2)+' '+birim.birimi,toplamtutar.toFixed(2)+' '+birim.birimi,
                                fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),'' ]).draw()
                                .nodes().to$().addClass( 'active' );
                        }
                        if(birim.id===parabirimi.id){
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            if(birim2!==null){
                                if(birim2.id===parabirimi2.id){
                                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                                }else{
                                    toplamtutar2=0;
                                    $('.onaywarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.yetkilionayla').prop('disabled', true);
                                }
                            }else{
                                toplamtutar2=0;
                            }
                        }else{
                            var kur = 1;
                            kurdurum=true;
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
                            if(birim2!==null){
                                if(birim2.id===parabirimi.id){
                                    fiyat+=fiyat2;
                                    fiyat2=0;
                                }else if(birim2.id!==parabirimi2.id){
                                    fiyat2=0;
                                    $('.onaywarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.yetkilionayla').prop('disabled', true);
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
                    if(kurdurum)
                        $('.onaykur').removeClass('hide');
                    else
                        $('.onaykur').addClass('hide');
                    if(geneltoplamtutar2 === 0){
                        $('.onaytutar').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi);
                        $('.onayindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi);
                        $('.onaykdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.onaykdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.onaytoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi);
                    }else{
                        if(geneltoplamtutar === 0){
                            $('.onaytutar').text(genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onayindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onaykdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onaykdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onaytoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }else{
                            $('.onaytutar').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi+' + '+genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onayindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi+' + '+genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onaykdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onaykdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.onaytoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }
                    }
                    $('#onaytutar').val(genelfiyat.toFixed(2));
                    $('#onayindirimtutar').val(genelindirim.toFixed(2));
                    $('#onaykdvsiztutar').val(genelkdvsiztutar.toFixed(2));
                    $('#onaykdvtutar').val(genelkdvtutar.toFixed(2));
                    $('#onaytoplamtutar').val(geneltoplamtutar.toFixed(2));
                    $('#onaytutar2').val(genelfiyat2.toFixed(2));
                    $('#onayindirimtutar2').val(genelindirim2.toFixed(2));
                    $('#onaykdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
                    $('#onaykdvtutar2').val(genelkdvtutar2.toFixed(2));
                    $('#onaytoplamtutar2').val(geneltoplamtutar2.toFixed(2));
                }else{
                    $('#yetkilionay').modal('hide');
                    toastr[event.type](event.text,event.title);
                }
                $.unblockUI();
            });
        });
        $(document).on("click", ".yetkilired", function () {
            $.blockUI();
            var id = $('#secilen').val();
            var action = $('#form_sample_13').data('action');
            $('#form_sample_13').prop('action',action+'/'+id);
            $.getJSON("{{ URL::to('ucretlendirme/yetkiliredbilgi') }}",{id:id},function(event){
                if(event.durum) {
                    var ucretlendirilen = event.ucretlendirilen;
                    var parabirimi = ucretlendirilen.parabirimi;
                    var parabirimi2 = ucretlendirilen.parabirimi2;
                    var arizafiyat = ucretlendirilen.arizafiyat;
                    $(".yetkiliredcariadi").html(ucretlendirilen.netsiscari.cariadi);
                    $('#yetkiliredid').val(ucretlendirilen.id);
                    var reddedilenler,redlist;
                    if(ucretlendirilen.durum<2){
                        $('#yetkiliredsecilenler').val(ucretlendirilen.secilenler);
                        reddedilenler = ucretlendirilen.secilenler;
                        redlist=reddedilenler.split(',');
                        $('#yetkiliredadet').val(redlist.length);
                    }else{
                        $('#yetkiliredsecilenler').val(ucretlendirilen.reddedilenler);
                        reddedilenler = ucretlendirilen.reddedilenler;
                        redlist=reddedilenler.split(',');
                        $('#yetkiliredadet').val(redlist.length);
                    }
                    oTable13.clear().draw();
                    $.each(arizafiyat,function(index) {
                        var garanti = arizafiyat[index].ariza_garanti==="0" ? 'Dışında' : 'İçinde';
                        var fiyatdurum = arizafiyat[index].fiyatdurum==="0" ? 'Genel' : 'Özel';
                        var sayacadi = arizafiyat[index].sayacadi.sayacadi;
                        var serino = arizafiyat[index].ariza_serino;
                        var birim = arizafiyat[index].parabirimi;
                        var birim2 = arizafiyat[index].parabirimi2;
                        var fiyat = parseFloat(arizafiyat[index].fiyat);
                        var fiyat2 = parseFloat(arizafiyat[index].fiyat2);
                        var indirimorani = parseFloat(arizafiyat[index].indirimorani);
                        var indirim =(fiyat*indirimorani)/100;
                        var indirim2 =(fiyat2*indirimorani)/100;
                        var kdvsiztutar = parseFloat(arizafiyat[index].tutar);
                        var kdvsiztutar2 = parseFloat(arizafiyat[index].tutar2);
                        var kdv = parseFloat(arizafiyat[index].kdv);
                        var kdv2 = parseFloat(arizafiyat[index].kdv2);
                        var toplamtutar = parseFloat(arizafiyat[index].toplamtutar);
                        var toplamtutar2 = parseFloat(arizafiyat[index].toplamtutar2);
                        var dovizkuru = arizafiyat[index].dovizkuru;
                        var euro = 0;
                        var dolar = 0;
                        var sterlin = 0;
                        $.each(dovizkuru, function (index) {
                            if (dovizkuru[index].parabirimi_id === "2")
                                euro = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                            else if (dovizkuru[index].parabirimi_id === "3")
                                dolar = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                            else
                                sterlin = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                        });
                        if(birim.id===parabirimi.id){
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            if(birim2!==null){
                                if(birim2.id===parabirimi2.id){
                                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                                }else{
                                    toplamtutar2=0;
                                }
                            }else{
                                toplamtutar2=0;
                            }
                        }else{
                            var kur = 1;
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
                            if(birim2!==null){
                                if(birim2.id===parabirimi.id){
                                    fiyat+=fiyat2;
                                    fiyat2=0;
                                }else if(birim2.id!==parabirimi2.id){
                                    fiyat2=0;
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
                        if(toplamtutar2>0){
                            if(toplamtutar>0){
                                oTable13.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+parabirimi.birimi+' + '+fiyat2.toFixed(2)+' '+parabirimi2.birimi,
                                    indirimorani.toFixed(2)+'%',kdvsiztutar.toFixed(2)+' '+parabirimi.birimi+' + '+kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi,kdv.toFixed(2)+' '+parabirimi.birimi+' + '+kdv2.toFixed(2)+' '+parabirimi2.birimi,
                                    toplamtutar.toFixed(2)+' '+parabirimi.birimi+' + '+toplamtutar2.toFixed(2)+' '+parabirimi2.birimi,
                                    '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>'])
                                    .draw().nodes().to$().addClass( 'active' );
                            }else{
                                oTable13.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat2.toFixed(2)+' '+parabirimi2.birimi,indirimorani.toFixed(2)+'%',
                                    kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi,kdv2.toFixed(2)+' '+parabirimi2.birimi,toplamtutar2.toFixed(2)+' '+parabirimi2.birimi,
                                    '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>'])
                                    .draw().nodes().to$().addClass( 'active' );
                            }
                        }else{
                            oTable13.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+parabirimi.birimi,indirimorani.toFixed(2)+'%',
                                kdvsiztutar.toFixed(2)+' '+parabirimi.birimi,kdv.toFixed(2)+' '+parabirimi.birimi,toplamtutar.toFixed(2)+' '+parabirimi.birimi,
                                '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>'])
                                .draw().nodes().to$().addClass( 'active' );
                        }
                    });
                }else{
                    $('#yetkilired').modal('hide');
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
        });
        $(document).on("click", ".subeyetkilionay", function () {
            $.blockUI();
            var id = $('#secilen').val();
            var action = $('#form_sample_16').data('action');
            $('#form_sample_16').prop('action',action+'/'+id);
            $.getJSON("{{ URL::to('ucretlendirme/yetkilibilgi') }}",{id:id},function(event){
                if(event.durum){
                    var ucretlendirilen = event.ucretlendirilen;
                    var yetkili = event.yetkili;
                    $("#subeyetkiliid").val(yetkili.id);
                    $(".subeonaycariadi").html(ucretlendirilen.netsiscari.cariadi);
                    $('#subeonaytumu').val(ucretlendirilen.secilenler);
                    $('#subeonaygaranti').val(ucretlendirilen.garanti);
                    $('#subeonayid').val(ucretlendirilen.id);
                    $('#subeonaysecilenler').val(ucretlendirilen.secilenler);
                    $('#subeonayadet').val(ucretlendirilen.sayacsayisi);
                    oTable16.clear().draw();
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
                    $('.subeonaywarning').html('<span style="color:red">Kur Tarihi: '+dovizkurutarih+'. Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
                    $('.subeonayeuro').html('Euro : '+euro+' ₺');
                    $('.subeonaydolar').html('Dolar : '+dolar+' ₺');
                    $('.subeonaysterlin').html('Sterlin : '+sterlin+' ₺');
                    $('#subeonaydolar').val(dolar);
                    $('#subeonayeuro').val(euro);
                    $('#subeonaysterlin').val(sterlin);
                    var parabirimi = ucretlendirilen.parabirimi;
                    var parabirimi2 = ucretlendirilen.parabirimi2;
                    $('#subeonaybirim').val(parabirimi.id);
                    $('#subeonaybirim2').val(parabirimi2==null ? "" : parabirimi2.id);
                    $('#subeonaybirimi').val(parabirimi.birimi);
                    $('#subeonaybirimi2').val(parabirimi2==null ? "" : parabirimi2.birimi);
                    $('#subeonaykurtarih').val(dovizkurutarih);
                    var arizafiyat=ucretlendirilen.arizafiyat;
                    var kurdurum=false;
                    $.each(arizafiyat,function(index) {
                        var garanti = arizafiyat[index].ariza_garanti==="0" ? 'Dışında' : 'İçinde';
                        var fiyatdurum = arizafiyat[index].fiyatdurum==="0" ? 'Genel' : 'Özel';
                        var sayacadi = arizafiyat[index].sayacadi.sayacadi;
                        var serino = arizafiyat[index].ariza_serino;
                        var birim = arizafiyat[index].parabirimi;
                        var birim2 = arizafiyat[index].parabirimi2;
                        var fiyat = parseFloat(arizafiyat[index].fiyat);
                        var fiyat2 = parseFloat(arizafiyat[index].fiyat2);
                        var indirimorani = parseFloat(arizafiyat[index].indirimorani);
                        var indirim =(fiyat*indirimorani)/100;
                        var indirim2 =(fiyat2*indirimorani)/100;
                        var kdvsiztutar = parseFloat(arizafiyat[index].tutar);
                        var kdvsiztutar2 = parseFloat(arizafiyat[index].tutar2);
                        var kdv = parseFloat(arizafiyat[index].kdv);
                        var kdv2 = parseFloat(arizafiyat[index].kdv2);
                        var toplamtutar = parseFloat(arizafiyat[index].toplamtutar);
                        var toplamtutar2 = parseFloat(arizafiyat[index].toplamtutar2);
                        oTable16.columns( [ 10,11,12,13 ] ).visible( false,false );
                        if(toplamtutar2>0){
                            if(toplamtutar>0){
                                oTable16.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi+' + '+fiyat2.toFixed(2)+' '+birim2.birimi,
                                    indirimorani.toFixed(2)+'%',kdvsiztutar.toFixed(2)+' '+birim.birimi+' + '+kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv.toFixed(2)+' '+birim.birimi+' + '+kdv2.toFixed(2)+' '+birim2.birimi,
                                    toplamtutar.toFixed(2)+' '+birim.birimi+' + '+toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                    fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),birim2.birimi ]).draw()
                                    .nodes().to$().addClass( 'active' );
                            }else{
                                oTable16.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat2.toFixed(2)+' '+birim2.birimi,indirimorani.toFixed(2)+'%',
                                    kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv2.toFixed(2)+' '+birim2.birimi,toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                    fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),birim2.birimi ]).draw()
                                    .nodes().to$().addClass( 'active' );
                            }
                        }else{
                            oTable16.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi,indirimorani.toFixed(2)+'%',
                                kdvsiztutar.toFixed(2)+' '+birim.birimi,kdv.toFixed(2)+' '+birim.birimi,toplamtutar.toFixed(2)+' '+birim.birimi,
                                fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),'' ]).draw()
                                .nodes().to$().addClass( 'active' );
                        }
                        if(birim.id===parabirimi.id){
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            if(birim2!==null){
                                if(birim2.id===parabirimi2.id){
                                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                                }else{
                                    toplamtutar2=0;
                                    $('.subeonaywarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.subeyetkilionayla').prop('disabled', true);
                                }
                            }else{
                                toplamtutar2=0;
                            }
                        }else{
                            var kur = 1;
                            kurdurum=true;
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
                            if(birim2!==null){
                                if(birim2.id===parabirimi.id){
                                    fiyat+=fiyat2;
                                    fiyat2=0;
                                }else if(birim2.id!==parabirimi2.id){
                                    fiyat2=0;
                                    $('.subeonaywarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.subeyetkilionayla').prop('disabled', true);
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
                    if(kurdurum)
                        $('.subeonaykur').removeClass('hide');
                    else
                        $('.subeonaykur').addClass('hide');
                    if(geneltoplamtutar2 === 0){
                        $('.subeonaytutar').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi);
                        $('.subeonayindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi);
                        $('.subeonaykdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.subeonaykdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.subeonaytoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi);
                    }else{
                        if(geneltoplamtutar === 0){
                            $('.subeonaytutar').text(genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.subeonayindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.subeonaykdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.subeonaykdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.subeonaytoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }else{
                            $('.subeonaytutar').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi+' + '+genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.subeonayindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi+' + '+genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.subeonaykdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.subeonaykdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.subeonaytoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }
                    }
                    $('#subeonaytutar').val(genelfiyat.toFixed(2));
                    $('#subeonayindirimtutar').val(genelindirim.toFixed(2));
                    $('#subeonaykdvsiztutar').val(genelkdvsiztutar.toFixed(2));
                    $('#subeonaykdvtutar').val(genelkdvtutar.toFixed(2));
                    $('#subeonaytoplamtutar').val(geneltoplamtutar.toFixed(2));
                    $('#subeonaytutar2').val(genelfiyat2.toFixed(2));
                    $('#subeonayindirimtutar2').val(genelindirim2.toFixed(2));
                    $('#subeonaykdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
                    $('#subeonaykdvtutar2').val(genelkdvtutar2.toFixed(2));
                    $('#subeonaytoplamtutar2').val(geneltoplamtutar2.toFixed(2));
                }else{
                    $('#subeyetkilionay').modal('hide');
                    toastr[event.type](event.text,event.title);
                }
                $.unblockUI();
            });
        });
        $(document).on("click", ".subeyetkilired", function () {
            $.blockUI();
            var id = $('#secilen').val();
            var action = $('#form_sample_17').data('action');
            $('#form_sample_17').prop('action',action+'/'+id);
            $.getJSON("{{ URL::to('ucretlendirme/yetkiliredbilgi') }}",{id:id},function(event){
                if(event.durum) {
                    var ucretlendirilen = event.ucretlendirilen;
                    var parabirimi = ucretlendirilen.parabirimi;
                    var parabirimi2 = ucretlendirilen.parabirimi2;
                    var arizafiyat = ucretlendirilen.arizafiyat;
                    $(".subeyetkiliredcariadi").html(ucretlendirilen.netsiscari.cariadi);
                    $('#subeyetkiliredid').val(ucretlendirilen.id);
                    var reddedilenler,redlist;
                    if(ucretlendirilen.durum<2){
                        $('#subeyetkiliredsecilenler').val(ucretlendirilen.secilenler);
                        reddedilenler = ucretlendirilen.secilenler;
                        redlist=reddedilenler.split(',');
                        $('#subeyetkiliredadet').val(redlist.length);
                    }else{
                        $('#subeyetkiliredsecilenler').val(ucretlendirilen.reddedilenler);
                        reddedilenler = ucretlendirilen.reddedilenler;
                        redlist=reddedilenler.split(',');
                        $('#subeyetkiliredadet').val(redlist.length);
                    }

                    oTable17.clear().draw();
                    $.each(arizafiyat,function(index) {
                        var garanti = arizafiyat[index].ariza_garanti==="0" ? 'Dışında' : 'İçinde';
                        var fiyatdurum = arizafiyat[index].fiyatdurum==="0" ? 'Genel' : 'Özel';
                        var sayacadi = arizafiyat[index].sayacadi.sayacadi;
                        var serino = arizafiyat[index].ariza_serino;
                        var birim = arizafiyat[index].parabirimi;
                        var birim2 = arizafiyat[index].parabirimi2;
                        var fiyat = parseFloat(arizafiyat[index].fiyat);
                        var fiyat2 = parseFloat(arizafiyat[index].fiyat2);
                        var indirimorani = parseFloat(arizafiyat[index].indirimorani);
                        var indirim =(fiyat*indirimorani)/100;
                        var indirim2 =(fiyat2*indirimorani)/100;
                        var kdvsiztutar = parseFloat(arizafiyat[index].tutar);
                        var kdvsiztutar2 = parseFloat(arizafiyat[index].tutar2);
                        var kdv = parseFloat(arizafiyat[index].kdv);
                        var kdv2 = parseFloat(arizafiyat[index].kdv2);
                        var toplamtutar = parseFloat(arizafiyat[index].toplamtutar);
                        var toplamtutar2 = parseFloat(arizafiyat[index].toplamtutar2);
                        var dovizkuru = arizafiyat[index].dovizkuru;
                        var euro = 0;
                        var dolar = 0;
                        var sterlin = 0;
                        $.each(dovizkuru, function (index) {
                            if (dovizkuru[index].parabirimi_id === "2")
                                euro = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                            else if (dovizkuru[index].parabirimi_id === "3")
                                dolar = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                            else
                                sterlin = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                        });
                        if(birim.id===parabirimi.id){
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            if(birim2!==null){
                                if(birim2.id===parabirimi2.id){
                                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                                }else{
                                    toplamtutar2=0;
                                }
                            }else{
                                toplamtutar2=0;
                            }
                        }else{
                            var kur = 1;
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
                            if(birim2!==null){
                                if(birim2.id===parabirimi.id){
                                    fiyat+=fiyat2;
                                    fiyat2=0;
                                }else if(birim2.id!==parabirimi2.id){
                                    fiyat2=0;
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
                        if(toplamtutar2>0){
                            if(toplamtutar>0){
                                oTable17.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+parabirimi.birimi+' + '+fiyat2.toFixed(2)+' '+parabirimi2.birimi,
                                    indirimorani.toFixed(2)+'%',kdvsiztutar.toFixed(2)+' '+parabirimi.birimi+' + '+kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi,kdv.toFixed(2)+' '+parabirimi.birimi+' + '+kdv2.toFixed(2)+' '+parabirimi2.birimi,
                                    toplamtutar.toFixed(2)+' '+parabirimi.birimi+' + '+toplamtutar2.toFixed(2)+' '+parabirimi2.birimi,
                                    '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>'])
                                    .draw().nodes().to$().addClass( 'active' );
                            }else{
                                oTable17.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat2.toFixed(2)+' '+parabirimi2.birimi,indirimorani.toFixed(2)+'%',
                                    kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi,kdv2.toFixed(2)+' '+parabirimi2.birimi,toplamtutar2.toFixed(2)+' '+parabirimi2.birimi,
                                    '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>'])
                                    .draw().nodes().to$().addClass( 'active' );
                            }
                        }else{
                            oTable17.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+parabirimi.birimi,indirimorani.toFixed(2)+'%',
                                kdvsiztutar.toFixed(2)+' '+parabirimi.birimi,kdv.toFixed(2)+' '+parabirimi.birimi,toplamtutar.toFixed(2)+' '+parabirimi.birimi,
                                '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>'])
                                .draw().nodes().to$().addClass( 'active' );
                        }
                    });
                }else{
                    $('#subeyetkilired').modal('hide');
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
        });
        $(document).on("click", ".gerigonder", function () {
            $.blockUI();
            var id = $('#secilen').val();
            var action = $('#form_sample_3').data('action');
            $('#form_sample_3').prop('action',action+'/'+id);
            $.getJSON("{{ URL::to('ucretlendirme/geriteslimbilgi') }}",{id:id},function(event){
                if(event.durum) {
                    var ucretlendirilen = event.ucretlendirilen;
                    var teslimadres = event.teslimadres;
                    var parabirimi = ucretlendirilen.parabirimi;
                    var parabirimi2 = ucretlendirilen.parabirimi2;
                    var arizafiyat = ucretlendirilen.arizafiyat;
                    $(".redyer").html(ucretlendirilen.uretimyer.yeradi);
                    $(".redcariadi").html(ucretlendirilen.netsiscari.cariadi);
                    $(".rednedeni").html(ucretlendirilen.musterinotu);
                    $(".redfaturano").html('Teslim Edildiğinde Oluşacaktır');
                    $(".redcarikod").html(ucretlendirilen.netsiscari.carikod);
                    $(".redozelkod").html(ucretlendirilen.yetkili.ozelkod);
                    $(".redplasiyerkod").html(ucretlendirilen.yetkili.plasiyerkod);
                    $(".reddepokod").html(ucretlendirilen.yetkili.depokodu);
                    $(".redaciklama").html(ucretlendirilen.servis.servisadi);
                    $(".redfaturaadres").html(ucretlendirilen.netsiscari.adres+' '+ucretlendirilen.netsiscari.il+' '+ucretlendirilen.netsiscari.ilce);
                    $("#redteslimadres").val('');
                    $(".redaciklama1").html('GERİ GÖNDERİMDİR. FATURA EDİLMEYECEKTİR.');
                    $('#geriid').val(ucretlendirilen.id);
                    var reddedilenler,redlist;
                    if(ucretlendirilen.durum==="0" || ucretlendirilen.durum==="1"){
                        $('#gerisecilenler').val(ucretlendirilen.secilenler);
                        reddedilenler = ucretlendirilen.secilenler;
                        redlist=reddedilenler.split(',');
                        $('#geriadet').val(redlist.length);
                    }else{
                        $('#gerisecilenler').val(ucretlendirilen.reddedilenler);
                        reddedilenler = ucretlendirilen.reddedilenler;
                        redlist=reddedilenler.split(',');
                        $('#geriadet').val(redlist.length);
                    }

                    oTable3.clear().draw();
                    $.each(arizafiyat,function(index) {
                        var garanti = arizafiyat[index].ariza_garanti==="0" ? 'Dışında' : 'İçinde';
                        var fiyatdurum = arizafiyat[index].fiyatdurum==="0" ? 'Genel' : 'Özel';
                        var sayacadi = arizafiyat[index].sayacadi.sayacadi;
                        var serino = arizafiyat[index].ariza_serino;
                        var birim = arizafiyat[index].parabirimi;
                        var birim2 = arizafiyat[index].parabirimi2;
                        var fiyat = parseFloat(arizafiyat[index].fiyat);
                        var fiyat2 = parseFloat(arizafiyat[index].fiyat2);
                        var indirimorani = parseFloat(arizafiyat[index].indirimorani);
                        var indirim =(fiyat*indirimorani)/100;
                        var indirim2 =(fiyat2*indirimorani)/100;
                        var kdvsiztutar = parseFloat(arizafiyat[index].tutar);
                        var kdvsiztutar2 = parseFloat(arizafiyat[index].tutar2);
                        var kdv = parseFloat(arizafiyat[index].kdv);
                        var kdv2 = parseFloat(arizafiyat[index].kdv2);
                        var toplamtutar = parseFloat(arizafiyat[index].toplamtutar);
                        var toplamtutar2 = parseFloat(arizafiyat[index].toplamtutar2);
                        var dovizkuru = arizafiyat[index].dovizkuru;
                        var euro = 0;
                        var dolar = 0;
                        var sterlin = 0;
                        $.each(dovizkuru, function (index) {
                            if (dovizkuru[index].parabirimi_id === "2")
                                euro = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                            else if (dovizkuru[index].parabirimi_id === "3")
                                dolar = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                            else
                                sterlin = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                        });
                        if(birim.id===parabirimi.id){
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            if(birim2!==null){
                                if(birim2.id===parabirimi2.id){
                                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                                }else{
                                    toplamtutar2=0;
                                }
                            }else{
                                toplamtutar2=0;
                            }
                        }else{
                            var kur = 1;
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
                            if(birim2!==null){
                                if(birim2.id===parabirimi.id){
                                    fiyat+=fiyat2;
                                    fiyat2=0;
                                }else if(birim2.id!==parabirimi2.id){
                                    fiyat2=0;
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
                        if(toplamtutar2>0){
                            if(toplamtutar>0) {oTable3.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+parabirimi.birimi+' + '+fiyat2.toFixed(2)+' '+parabirimi2.birimi,
                                indirimorani.toFixed(2)+'%',kdvsiztutar.toFixed(2)+' '+parabirimi.birimi+' + '+kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi,kdv.toFixed(2)+' '+parabirimi.birimi+' + '+kdv2.toFixed(2)+' '+parabirimi2.birimi,
                                toplamtutar.toFixed(2)+' '+parabirimi.birimi+' + '+toplamtutar2.toFixed(2)+' '+parabirimi2.birimi,
                                '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>'])
                                .draw().nodes().to$().addClass( 'active' );
                            }else{oTable3.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat2.toFixed(2)+' '+parabirimi2.birimi,indirimorani.toFixed(2)+'%',
                                kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi,kdv2.toFixed(2)+' '+parabirimi2.birimi,toplamtutar2.toFixed(2)+' '+parabirimi2.birimi,
                                '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>'])
                                .draw().nodes().to$().addClass( 'active' );
                            }
                        }else{
                            oTable3.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+parabirimi.birimi,indirimorani.toFixed(2)+'%',
                                kdvsiztutar.toFixed(2)+' '+parabirimi.birimi,kdv.toFixed(2)+' '+parabirimi.birimi,toplamtutar.toFixed(2)+' '+parabirimi.birimi,
                                '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>'])
                                .draw().nodes().to$().addClass( 'active' );
                        }
                    });
                    oTable18.clear().draw();
                    $.each(teslimadres, function (index) {
                        oTable18.row.add([teslimadres[index].teslimadres]).draw();
                    });
                    $('#gerisecilenadres').val("");
                }else{
                    $('#gerigonder').modal('hide');
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
        });
        $(document).on("click", ".garantigonder", function () {
            $.blockUI();
            var id = $('#secilen').val();
            var action = $('#form_sample_5').data('action');
            $('#form_sample_5').prop('action',action+'/'+id);
            $.getJSON("{{ URL::to('ucretlendirme/garantiteslimbilgi') }}",{id:id},function(event){
                if(event.durum) {
                    var ucretlendirilen = event.ucretlendirilen;
                    var teslimadres = event.teslimadres;
                    var parabirimi = ucretlendirilen.parabirimi;
                    var parabirimi2 = ucretlendirilen.parabirimi2;
                    var arizafiyat = ucretlendirilen.arizafiyat;
                    $(".garantiyer").html(ucretlendirilen.uretimyer.yeradi);
                    $(".garanticariadi").html(ucretlendirilen.netsiscari.cariadi);
                    $(".garantinedeni").html(ucretlendirilen.musterinotu);
                    $(".garantifaturano").html('Teslim Edildiğinde Oluşacaktır');
                    $(".garanticarikod").html(ucretlendirilen.netsiscari.carikod);
                    $(".garantiozelkod").html(ucretlendirilen.yetkili.ozelkod);
                    $(".garantiplasiyerkod").html(ucretlendirilen.yetkili.plasiyerkod);
                    $(".garantidepokod").html(ucretlendirilen.yetkili.depokodu);
                    $(".garantiaciklama").html(ucretlendirilen.servis.servisadi);
                    $(".garantifaturaadres").html(ucretlendirilen.netsiscari.adres+' '+ucretlendirilen.netsiscari.il+' '+ucretlendirilen.netsiscari.ilce);
                    $("#garantiteslimadres").val('');
                    $(".garantiaciklama1").html('GARANTİ İÇİNDEDİR. FATURA EDİLMEYECEKTİR.');
                    $('#garantiid').val(ucretlendirilen.id);
                    $('#garantisecilenler').val(ucretlendirilen.reddedilenler);
                    var reddedilenler = ucretlendirilen.reddedilenler;
                    var redlist=reddedilenler.split(',');
                    $('#garantiadet').val(redlist.length);
                    oTable5.clear().draw();
                    $.each(arizafiyat,function(index) {
                        var garanti = arizafiyat[index].ariza_garanti==="0" ? 'Dışında' : 'İçinde';
                        var fiyatdurum = arizafiyat[index].fiyatdurum==="0" ? 'Genel' : 'Özel';
                        var sayacadi = arizafiyat[index].sayacadi.sayacadi;
                        var serino = arizafiyat[index].ariza_serino;
                        var birim = arizafiyat[index].parabirimi;
                        var birim2 = arizafiyat[index].parabirimi2;
                        var fiyat = parseFloat(arizafiyat[index].fiyat);
                        var fiyat2 = parseFloat(arizafiyat[index].fiyat2);
                        var indirimorani = parseFloat(arizafiyat[index].indirimorani);
                        var indirim =(fiyat*indirimorani)/100;
                        var indirim2 =(fiyat2*indirimorani)/100;
                        var kdvsiztutar = parseFloat(arizafiyat[index].tutar);
                        var kdvsiztutar2 = parseFloat(arizafiyat[index].tutar2);
                        var kdv = parseFloat(arizafiyat[index].kdv);
                        var kdv2 = parseFloat(arizafiyat[index].kdv2);
                        var toplamtutar = parseFloat(arizafiyat[index].toplamtutar);
                        var toplamtutar2 = parseFloat(arizafiyat[index].toplamtutar2);
                        var dovizkuru = arizafiyat[index].dovizkuru;
                        var euro = 0;
                        var dolar = 0;
                        var sterlin = 0;
                        $.each(dovizkuru, function (index) {
                            if (dovizkuru[index].parabirimi_id === "2")
                                euro = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                            else if (dovizkuru[index].parabirimi_id === "3")
                                dolar = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                            else
                                sterlin = parseFloat(dovizkuru[index].kurfiyati).toFixed(4);
                        });
                        if(birim.id===parabirimi.id){
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            if(birim2!==null){
                                if(birim2.id===parabirimi2.id){
                                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                                }else{
                                    toplamtutar2=0;
                                }
                            }else{
                                toplamtutar2=0;
                            }
                        }else{
                            var kur = 1;
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
                            if(birim2!==null){
                                if(birim2.id===parabirimi.id){
                                    fiyat+=fiyat2;
                                    fiyat2=0;
                                }else if(birim2.id!==parabirimi2.id){
                                    fiyat2=0;
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
                        if(toplamtutar2>0){
                            if(toplamtutar>0){
                                oTable5.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+parabirimi.birimi+' + '+fiyat2.toFixed(2)+' '+parabirimi2.birimi,
                                    indirimorani.toFixed(2)+'%',kdvsiztutar.toFixed(2)+' '+parabirimi.birimi+' + '+kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi,kdv.toFixed(2)+' '+parabirimi.birimi+' + '+kdv2.toFixed(2)+' '+parabirimi2.birimi,
                                    toplamtutar.toFixed(2)+' '+parabirimi.birimi+' + '+toplamtutar2.toFixed(2)+' '+parabirimi2.birimi,
                                    '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>'])
                                    .draw().nodes().to$().addClass( 'active' );
                            }else{
                                oTable5.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat2.toFixed(2)+' '+parabirimi2.birimi,indirimorani.toFixed(2)+'%',
                                    kdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi,kdv2.toFixed(2)+' '+parabirimi2.birimi,toplamtutar2.toFixed(2)+' '+parabirimi2.birimi,
                                    '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>'])
                                    .draw().nodes().to$().addClass( 'active' );
                            }
                        }else{
                            oTable5.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+parabirimi.birimi,indirimorani.toFixed(2)+'%',
                                kdvsiztutar.toFixed(2)+' '+parabirimi.birimi,kdv.toFixed(2)+' '+parabirimi.birimi,toplamtutar.toFixed(2)+' '+parabirimi.birimi,
                                '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>'])
                                .draw().nodes().to$().addClass( 'active' );
                        }
                    });
                    oTable19.clear().draw();
                    $.each(teslimadres, function (index) {
                        oTable19.row.add([teslimadres[index].teslimadres]).draw();
                    });
                    $('#garantisecilenadres').val("");
                }else{
                    $('#garantigonder').modal('hide');
                    toastr[event.type](event.text,event.title);
                }
                $.unblockUI();
            });
        });
        $(document).on("click", ".neden", function () {
            $.blockUI();
            var id = $(this).data('id');
            $.getJSON("{{ URL::to('ucretlendirme/reddedilenbilgi') }}",{id:id},function(event){
                if(event.durum){
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
                    $('.redwarning').html('<span style="color:red">Kur Tarihi: '+dovizkurutarih+'. Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
                    $('.redeuro').html('Euro : '+euro+' ₺');
                    $('.reddolar').html('Dolar : '+dolar+' ₺');
                    $('.redsterlin').html('Sterlin : '+sterlin+' ₺');
                    var parabirimi = ucretlendirilen.parabirimi;
                    var parabirimi2 = ucretlendirilen.parabirimi2;
                    var arizafiyat=ucretlendirilen.arizafiyat;
                    var kurdurum=false;
                    $.each(arizafiyat,function(index) {
                        var garanti = arizafiyat[index].ariza_garanti==="0" ? 'Dışında' : 'İçinde';
                        var sayacadi = arizafiyat[index].sayacadi.sayacadi;
                        var serino = arizafiyat[index].ariza_serino;
                        var birim = arizafiyat[index].parabirimi;
                        var birim2 = arizafiyat[index].parabirimi2;
                        var fiyat = parseFloat(arizafiyat[index].fiyat);
                        var fiyat2 = parseFloat(arizafiyat[index].fiyat2);
                        var indirimorani = parseFloat(arizafiyat[index].indirimorani);
                        var indirim =(fiyat*indirimorani)/100;
                        var indirim2 =(fiyat2*indirimorani)/100;
                        var kdvsiztutar = parseFloat(arizafiyat[index].tutar);
                        var kdvsiztutar2 = parseFloat(arizafiyat[index].tutar2);
                        var kdv = parseFloat(arizafiyat[index].kdv);
                        var kdv2 = parseFloat(arizafiyat[index].kdv2);
                        var toplamtutar = parseFloat(arizafiyat[index].toplamtutar);
                        var toplamtutar2 = parseFloat(arizafiyat[index].toplamtutar2);
                        var durum= '';
                        switch(arizafiyat[index].durum){
                            case '2': durum='Fiyatlandırma Bekliyor'; break;
                            case '3': durum='Tekrar Ücretlendirildi';break;
                            case '4': durum='Geri Gönderildi';break;
                            case '5': durum='Garanti İçi Gönderildi';break;
                        }
                        if(toplamtutar2>0){
                            if(toplamtutar>0){
                                oTable4.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyat.toFixed(2)+' '+birim.birimi+' + '+fiyat2.toFixed(2)+' '+birim2.birimi,
                                    indirimorani.toFixed(2)+'%',kdvsiztutar.toFixed(2)+' '+birim.birimi+' + '+kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv.toFixed(2)+' '+birim.birimi+' + '+kdv2.toFixed(2)+' '+birim2.birimi,
                                    toplamtutar.toFixed(2)+' '+birim.birimi+' + '+toplamtutar2.toFixed(2)+' '+birim2.birimi,durum,
                                    '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>']).draw();
                            }else{
                                oTable4.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyat2.toFixed(2)+' '+birim2.birimi,indirimorani.toFixed(2)+'%',
                                    kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv2.toFixed(2)+' '+birim2.birimi,toplamtutar2.toFixed(2)+' '+birim2.birimi,durum,
                                    '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>']).draw();
                            }
                        }else{
                            oTable4.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyat.toFixed(2)+' '+birim.birimi,indirimorani.toFixed(2)+'%',
                            kdvsiztutar.toFixed(2)+' '+birim.birimi,kdv.toFixed(2)+' '+birim.birimi,toplamtutar.toFixed(2)+' '+birim.birimi,durum,
                            '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>']).draw();
                        }
                        if(birim.id===parabirimi.id){
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            if(birim2!==null){
                                if(birim2.id===parabirimi2.id){
                                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                                }else{
                                    toplamtutar2=0;
                                    $('.redwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                }
                            }else{
                                toplamtutar2=0;
                            }
                        }else{
                            var kur = 1;
                            kurdurum=true;
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
                            if(birim2!==null){
                                if(birim2.id===parabirimi.id){
                                    fiyat+=fiyat2;
                                    fiyat2=0;
                                }else if(birim2.id!==parabirimi2.id){
                                    fiyat2=0;
                                    $('.redwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
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
                    if(kurdurum)
                        $('.redkur').removeClass('hide');
                    else
                        $('.redkur').addClass('hide');
                    if(geneltoplamtutar2===0){
                        $('.redtutar').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi);
                        $('.redindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi);
                        $('.redkdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.redkdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.redtoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi);
                    }else{
                        if(geneltoplamtutar === 0){
                            $('.redtutar').text(genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.redindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.redkdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.redkdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.redtoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }else{
                            $('.redtutar').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi+' + '+genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.redindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi+' + '+genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.redkdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.redkdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.redtoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }
                    }
                }else{
                    $('#redneden').modal('hide');
                    toastr[event.type](event.text,event.title);
                }

                $.unblockUI();
            });
        });
        $(document).on("click", ".yetkilisec", function (){
            var adi = $('#yetkilisecilenadi').val();
            var tel = $('#yetkilisecilentel').val();
            var mail = $('#yetkilisecilenemail').val();
            var id = $('#yetkilisecilen').val();
            $("#teladisoyadi").val(adi);
            $("#telyetkilitel").val(tel);
            $("#telyetkilimail").val(mail);
            $("#telyetkiliid").val(id);
            $("#mailadisoyadi").val(adi);
            $("#mailyetkilimail").val(mail);
            $("#mailyetkiliid").val(id);
            $("#tekrarmailadisoyadi").val(adi);
            $("#tekrarmailyetkilimail").val(mail);
            $("#tekrarmailyetkiliid").val(id);
            $("#mailteladisoyadi").val(adi);
            $("#mailtelyetkilitel").val(tel);
            $("#mailtelyetkilimail").val(mail);
            $("#mailtelyetkiliid").val(id);
        });
        $(document).on("click", ".yetkilibul", function (){
            var adi = $('#yetkilibulsecilenadi').val();
            var tel = $('#yetkilibulsecilentel').val();
            var mail = $('#yetkilibulsecilenemail').val();
            var id = $('#yetkilibulsecilen').val();
            $("#teladisoyadi").val(adi);
            $("#telyetkilitel").val(tel);
            $("#telyetkilimail").val(mail);
            $("#telyetkiliid").val(id);
            $("#mailadisoyadi").val(adi);
            $("#mailyetkilimail").val(mail);
            $("#mailyetkiliid").val(id);
            $("#tekrarmailadisoyadi").val(adi);
            $("#tekrarmailyetkilimail").val(mail);
            $("#tekrarmailyetkiliid").val(id);
            $("#mailteladisoyadi").val(adi);
            $("#mailtelyetkilitel").val(tel);
            $("#mailtelyetkilimail").val(mail);
            $("#mailtelyetkiliid").val(id);
        });
        $(document).on("click", ".yeniyetkiliekle", function (){
            var adi = $('#yeniadi').val();
            var soyadi = $('#yenisoyadi').val();
            var tel = $('#yenitel').val();
            var mail = $('#yenimail').val();
            var id = -1;
            $.blockUI();
            $.getJSON(" {{ URL::to('ucretlendirme/yetkilikontrol') }}",{mail:mail,tel:tel,adi:adi,soyadi:soyadi},function(event) {
                if (event.durum) {
                    $("#teladisoyadi").val(adi + ' ' + soyadi);
                    $("#telyetkilitel").val(tel);
                    $("#telyetkilimail").val(mail);
                    $("#telyetkiliid").val(id);
                    $("#mailadisoyadi").val(adi + ' ' + soyadi);
                    $("#mailyetkilimail").val(mail);
                    $("#mailyetkiliid").val(id);
                    $("#tekrarmailadisoyadi").val(adi + ' ' + soyadi);
                    $("#tekrarmailyetkilimail").val(mail);
                    $("#tekrarmailyetkiliid").val(id);
                    $("#mailteladisoyadi").val(adi + ' ' + soyadi);
                    $("#mailtelyetkilitel").val(tel);
                    $("#mailtelyetkilimail").val(mail);
                    $("#mailtelyetkiliid").val(id);
                }else{
                    toastr[event.type](event.text,event.title);
                }
                $.unblockUI();
            });
        });
        $(document).on("click", ".geriteslimadresisec", function (){
            var secilen = $('#gerisecilenadres').val();
            $('#redteslimadres').val(secilen);
        });
        $(document).on("click", ".subeaktar", function () {
            $.blockUI();
            var id = $('#secilen').val();
            var action = $('#form_sample_19').data('action');
            $('#form_sample_19').prop('action',action+'/'+id);
            $.getJSON("{{ URL::to('ucretlendirme/yetkilibilgi') }}",{id:id},function(event){
                if(event.durum){
                    var ucretlendirilen = event.ucretlendirilen;
                    var yetkili = event.yetkili;
                    $("#subeaktaryetkiliid").val(yetkili.id);
                    $(".subeaktarcariadi").html(ucretlendirilen.netsiscari.cariadi);
                    $('#subeaktartumu').val(ucretlendirilen.secilenler);
                    $('#subeaktargaranti').val(ucretlendirilen.garanti);
                    $('#subeaktarid').val(ucretlendirilen.id);
                    $('#subeaktarsecilenler').val(ucretlendirilen.secilenler);
                    $('#subeaktaradet').val(ucretlendirilen.sayacsayisi);
                    oTable19.clear().draw();
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
                    $('.subeaktarwarning').html('<span style="color:red">Kur Tarihi: '+dovizkurutarih+'. Faturanın Kesileceği Tarihteki Kur Fiyatı Dikkate Alınacaktır.</span>');
                    $('.subeaktareuro').html('Euro : '+euro+' ₺');
                    $('.subeaktardolar').html('Dolar : '+dolar+' ₺');
                    $('.subeaktarsterlin').html('Sterlin : '+sterlin+' ₺');
                    $('#subeaktardolar').val(dolar);
                    $('#subeaktareuro').val(euro);
                    $('#subeaktarsterlin').val(sterlin);
                    var parabirimi = ucretlendirilen.parabirimi;
                    var parabirimi2 = ucretlendirilen.parabirimi2;
                    $('#subeaktarbirim').val(parabirimi.id);
                    $('#subeaktarbirim2').val(parabirimi2==null ? "" : parabirimi2.id);
                    $('#subeaktarbirimi').val(parabirimi.birimi);
                    $('#subeaktarbirimi2').val(parabirimi2==null ? "" : parabirimi2.birimi);
                    $('#subeaktarkurtarih').val(dovizkurutarih);
                    var arizafiyat=ucretlendirilen.arizafiyat;
                    var kurdurum=false;
                    $.each(arizafiyat,function(index) {
                        var garanti = arizafiyat[index].ariza_garanti==="0" ? 'Dışında' : 'İçinde';
                        var fiyatdurum = arizafiyat[index].fiyatdurum==="0" ? 'Genel' : 'Özel';
                        var sayacadi = arizafiyat[index].sayacadi.sayacadi;
                        var serino = arizafiyat[index].ariza_serino;
                        var birim = arizafiyat[index].parabirimi;
                        var birim2 = arizafiyat[index].parabirimi2;
                        var fiyat = parseFloat(arizafiyat[index].fiyat);
                        var fiyat2 = parseFloat(arizafiyat[index].fiyat2);
                        var indirimorani = parseFloat(arizafiyat[index].indirimorani);
                        var indirim =(fiyat*indirimorani)/100;
                        var indirim2 =(fiyat2*indirimorani)/100;
                        var kdvsiztutar = parseFloat(arizafiyat[index].tutar);
                        var kdvsiztutar2 = parseFloat(arizafiyat[index].tutar2);
                        var kdv = parseFloat(arizafiyat[index].kdv);
                        var kdv2 = parseFloat(arizafiyat[index].kdv2);
                        var toplamtutar = parseFloat(arizafiyat[index].toplamtutar);
                        var toplamtutar2 = parseFloat(arizafiyat[index].toplamtutar2);
                        oTable19.columns( [ 10,11,12,13 ] ).visible( false,false );
                        if(toplamtutar2>0){
                            if(toplamtutar>0){
                                oTable19.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi+' + '+fiyat2.toFixed(2)+' '+birim2.birimi,
                                    indirimorani.toFixed(2)+'%',kdvsiztutar.toFixed(2)+' '+birim.birimi+' + '+kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv.toFixed(2)+' '+birim.birimi+' + '+kdv2.toFixed(2)+' '+birim2.birimi,
                                    toplamtutar.toFixed(2)+' '+birim.birimi+' + '+toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                    fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),birim2.birimi ]).draw()
                                    .nodes().to$().addClass( 'active' );
                            }else{
                                oTable19.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat2.toFixed(2)+' '+birim2.birimi,indirimorani.toFixed(2)+'%',
                                    kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv2.toFixed(2)+' '+birim2.birimi,toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                    fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),birim2.birimi ]).draw()
                                    .nodes().to$().addClass( 'active' );
                            }
                        }else{
                            oTable19.row.add([arizafiyat[index].id,serino,sayacadi,garanti,fiyatdurum,fiyat.toFixed(2)+' '+birim.birimi,indirimorani.toFixed(2)+'%',
                                kdvsiztutar.toFixed(2)+' '+birim.birimi,kdv.toFixed(2)+' '+birim.birimi,toplamtutar.toFixed(2)+' '+birim.birimi,
                                fiyat.toFixed(2),birim.birimi,fiyat2.toFixed(2),'' ]).draw()
                                .nodes().to$().addClass( 'active' );
                        }
                        if(birim.id===parabirimi.id){
                            toplamtutar=Math.round(toplamtutar*2)/2;
                            if(birim2!==null){
                                if(birim2.id===parabirimi2.id){
                                    toplamtutar2=Math.round(toplamtutar2*2)/2;
                                }else{
                                    toplamtutar2=0;
                                    $('.subeaktarwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.subeaktaronay').prop('disabled', true);
                                }
                            }else{
                                toplamtutar2=0;
                            }
                        }else{
                            var kur = 1;
                            kurdurum=true;
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
                            if(birim2!==null){
                                if(birim2.id===parabirimi.id){
                                    fiyat+=fiyat2;
                                    fiyat2=0;
                                }else if(birim2.id!==parabirimi2.id){
                                    fiyat2=0;
                                    $('.subeaktarwarning').html('<span style="color:red">İki Parabiriminden Fazla Kullanılamaz.</span>');
                                    $('.subeaktaronay').prop('disabled', true);
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
                    if(kurdurum)
                        $('.subeaktarkur').removeClass('hide');
                    else
                        $('.subeaktarkur').addClass('hide');
                    if(geneltoplamtutar2 === 0){
                        $('.subeaktartutar').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi);
                        $('.subeaktarindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi);
                        $('.subeaktarkdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.subeaktarkdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi);
                        $('.subeaktartoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi);
                    }else{
                        if(geneltoplamtutar === 0){
                            $('.subeaktartutar').text(genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.subeaktarindirimtutar').text(genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.subeaktarkdvsiztutar').text(genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.subeaktarkdvtutar').text(genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.subeaktartoplamtutar').text(geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }else{
                            $('.subeaktartutar').text(genelfiyat.toFixed(2)+' '+parabirimi.birimi+' + '+genelfiyat2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.subeaktarindirimtutar').text(genelindirim.toFixed(2)+' '+parabirimi.birimi+' + '+genelindirim2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.subeaktarkdvsiztutar').text(genelkdvsiztutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvsiztutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.subeaktarkdvtutar').text(genelkdvtutar.toFixed(2)+' '+parabirimi.birimi+' + '+genelkdvtutar2.toFixed(2)+' '+parabirimi2.birimi);
                            $('.subeaktartoplamtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }
                    }
                    $('#subeaktartutar').val(genelfiyat.toFixed(2));
                    $('#subeaktarindirimtutar').val(genelindirim.toFixed(2));
                    $('#subeaktarkdvsiztutar').val(genelkdvsiztutar.toFixed(2));
                    $('#subeaktarkdvtutar').val(genelkdvtutar.toFixed(2));
                    $('#subeaktartoplamtutar').val(geneltoplamtutar.toFixed(2));
                    $('#subeaktartutar2').val(genelfiyat2.toFixed(2));
                    $('#subeaktarindirimtutar2').val(genelindirim2.toFixed(2));
                    $('#subeaktarkdvsiztutar2').val(genelkdvsiztutar2.toFixed(2));
                    $('#subeaktarkdvtutar2').val(genelkdvtutar2.toFixed(2));
                    $('#subeaktartoplamtutar2').val(geneltoplamtutar2.toFixed(2));
                }else{
                    $('#subeaktar').modal('hide');
                    toastr[event.type](event.text,event.title);
                }
                $.unblockUI();
            });
        });
        $('.onay').click(function() {
            var ucretlendirilen = $('#sample_editable_1 .active .id').text();
            if(ucretlendirilen !==null){
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
                $.redirectPost(redirect, {ucretlendirilen: ucretlendirilen,rapor:'1',ireport:'1'});
            }
        });
        $('.fiyatlandirma').click(function() {
            var ucretlendirilen = $('#sample_editable_1 .active .id').text();
            if(ucretlendirilen !==null){
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
                $.redirectPost(redirect, {ucretlendirilen: ucretlendirilen,rapor:'2',ireport:'1'});
            }
        });
        $('.liste').click(function() {
            var ucretlendirilen = $('#sample_editable_1 .active .id').text();
            if(ucretlendirilen !==null){
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
                $.redirectPost(redirect, {ucretlendirilen: ucretlendirilen,rapor:'3',ireport:'1'});
            }
        });
        $('#gerifaturavar').on('change',function(){
            if($('#gerifaturavar').attr('checked')) {
                $(".gerifaturakismi").removeClass('hide');
            } else {
                $(".gerifaturakismi").addClass('hide');
            }
        });
        $('#mailvar').on('change', function () {
            if ($('#mailvar').attr('checked')) {
                $(".mailvar").removeClass('hide');
                $('.detay').removeClass('hide');
            } else {
                $(".mailvar").addClass('hide');
                $('.detay').addClass('hide');
            }
        });
        $('#telmailvar').on('change', function () {
            if ($('#telmailvar').attr('checked')) {
                $(".mailvar").removeClass('hide');
                $('.detay').removeClass('hide');
            } else {
                $(".mailvar").addClass('hide');
                $('.detay').addClass('hide');
            }
        });
        $('#tekrarmailvar').on('change', function () {
            if ($('#tekrarmailvar').attr('checked')) {
                $(".mailvar").removeClass('hide');
                $('.detay').removeClass('hide');
            } else {
                $(".mailvar").addClass('hide');
                $('.detay').addClass('hide');
            }
        });
        $('#onaymailvar').on('change', function () {
            if ($('#onaymailvar').attr('checked')) {
                $(".onaymailvar").removeClass('hide');
                $('.onaydetay').removeClass('hide');
            } else {
                $(".onaymailvar").addClass('hide');
                $('.onaydetay').addClass('hide');
            }
        });
        $('#subeonaymailvar').on('change', function () {
            if ($('#subeonaymailvar').attr('checked')) {
                $(".subeonaymailvar").removeClass('hide');
                $('.subeonaydetay').removeClass('hide');
            } else {
                $(".subeonaymailvar").addClass('hide');
                $('.subeonaydetay').addClass('hide');
            }
        });
        tinymce.init({ selector: "textarea2",theme: "modern",format:'text',
            language : "tr",height : 200,resize: false,entity_encoding : "raw",
            entities : '160,nbsp,161,iexcl,162,cent,163,pound,164,curren,165,yen,166,brvbar,167,sect,168,uml,169,copy,170,ordf,171,laquo,172,not,173,shy,174,reg,175,macr,176,deg,177,plusmn,178,sup2,179,sup3,180,acute,181,micro,182,para,183,middot,184,cedil,185,sup1,186,ordm,187,raquo,188,frac14,189,frac12,190,frac34,191,iquest,192,Agrave,193,Aacute,194,Acirc,195,Atilde,196,Auml,197,Aring,198,AElig,199,Ccedil,200,Egrave,201,Eacute,202,Ecirc,203,Euml,204,Igrave,205,Iacute,206,Icirc,207,Iuml,208,ETH,209,Ntilde,210,Ograve,211,Oacute,212,Ocirc,213,Otilde,214,Ouml,215,times,216,Oslash,217,Ugrave,218,Uacute,219,Ucirc,220,Uuml,221,Yacute,222,THORN,223,szlig,224,agrave,225,aacute,226,acirc,227,atilde,228,auml,229,aring,230,aelig,231,ccedil,232,egrave,233,eacute,234,ecirc,235,euml,236,igrave,237,iacute,238,icirc,239,iuml,240,eth,241,ntilde,242,ograve,243,oacute,244,ocirc,245,otilde,246,ouml,247,divide,248,oslash,249,ugrave,250,uacute,251,ucirc,252,uuml,253,yacute,254,thorn,255,yuml,402,fnof,913,Alpha,914,Beta,915,Gamma,916,Delta,917,Epsilon,918,Zeta,919,Eta,920,Theta,921,Iota,922,Kappa,923,Lambda,924,Mu,925,Nu,926,Xi,927,Omicron,928,Pi,929,Rho,931,Sigma,932,Tau,933,Upsilon,934,Phi,935,Chi,936,Psi,937,Omega,945,alpha,946,beta,947,gamma,948,delta,949,epsilon,950,zeta,951,eta,952,theta,953,iota,954,kappa,955,lambda,956,mu,957,nu,958,xi,959,omicron,960,pi,961,rho,962,sigmaf,963,sigma,964,tau,965,upsilon,966,phi,967,chi,968,psi,969,omega,977,thetasym,978,upsih,982,piv,8226,bull,8230,hellip,8242,prime,8243,Prime,8254,oline,8260,frasl,8472,weierp,8465,image,8476,real,8482,trade,8501,alefsym,8592,larr,8593,uarr,8594,rarr,8595,darr,8596,harr,8629,crarr,8656,lArr,8657,uArr,8658,rArr,8659,dArr,8660,hArr,8704,forall,8706,part,8707,exist,8709,empty,8711,nabla,8712,isin,8713,notin,8715,ni,8719,prod,8721,sum,8722,minus,8727,lowast,8730,radic,8733,prop,8734,infin,8736,ang,8743,and,8744,or,8745,cap,8746,cup,8747,int,8756,there4,8764,sim,8773,cong,8776,asymp,8800,ne,8801,equiv,8804,le,8805,ge,8834,sub,8835,sup,8836,nsub,8838,sube,8839,supe,8853,oplus,8855,otimes,8869,perp,8901,sdot,8968,lceil,8969,rceil,8970,lfloor,8971,rfloor,9001,lang,9002,rang,9674,loz,9824,spades,9827,clubs,9829,hearts,9830,diams,338,OElig,339,oelig,352,Scaron,353,scaron,376,Yuml,710,circ,732,tilde,8194,ensp,8195,emsp,8201,thinsp,8204,zwnj,8205,zwj,8206,lrm,8207,rlm,8211,ndash,8212,mdash,8216,lsquo,8217,rsquo,8218,sbquo,8220,ldquo,8221,rdquo,8222,bdquo,8224,dagger,8225,Dagger,8240,permil,8249,lsaquo,8250,rsaquo,8364,euro',
            plugins: [ "moxiemanager autolink lists link image charmap preview hr","wordcount code media save table directionality paste imagetools"],
            toolbar: "undo redo |  bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
            //    toolbar_items_size : 'small',
            //    menubar: false,
            relative_urls: false,
            setup: function(editor) {
                editor.on('change', function() {
                    if(editor.id==="mailicerik"){
                        $("#icerik").val(editor.getContent());
                    }else{
                        $("#icerik").val(editor.getContent());
                    }
                });
            }
        });

        $('#formgeri').click(function () {
            $('#form_sample_3').submit();
            $.blockUI();
        });
        $('#formgaranti').click(function () {
            $('#form_sample_5').submit();
            $.blockUI();
        });
        $('#formtelefon').click(function () {
            $('#form_sample_7').submit();
        });
        $('#formmail').click(function () {
            $('#form_sample_8').submit();
        });
        $('#formyetkili').click(function () {
            $('#form_sample_12').submit();
        });
        $('#formyetkilireddet').click(function () {
            $('#form_sample_13').submit();
        });
        $('#formtekrarmail').click(function () {
            $('#form_sample_14').submit();
        });
        $('#formmailtel').click(function () {
            $('#form_sample_15').submit();
        });
        $('#formsubeyetkili').click(function () {
            $('#form_sample_16').submit();
        });
        $('#formsubeyetkilireddet').click(function () {
            $('#form_sample_17').submit();
        });
        $('#formsubeaktar').click(function () {
            $('#form_sample_19').submit();
        });
        $('#fiyatid').click(function () {
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
                        <i class="fa fa-tag"></i>Ücretlendirmesi Belirlenen Sayaçlar
                    </div>
                    <div class="actions">
                        @if($servis_id<>6)
                        <a class="btn btn-default btn-sm onay">
                            <i class="fa fa-check"></i> Onay Formu </a>
                        @endif
                        <a class="btn btn-default btn-sm fiyatlandirma">
                            <i class="fa fa-try"></i> Fiyatlandırma </a>
                        <a class="btn btn-default btn-sm liste">
                            <i class="fa fa-list"></i> Sayaç Listesi </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Cari Adı</th>
                            <th>Üretim Yeri</th>
                            <th>Servis</th>
                            <th>Adet</th>
                            <th>Durum</th>
                            <th>Fiyat</th>
                            <th>Mail</th>
                            <th>Kullanıcı</th>
                            <th>Tarih</th>
                            <th></th><th></th><th></th>
                            <th></th><th></th><th></th>
                            <th></th>
                            <th>Detay</th>
                        </tr>
                        </thead>
                    </table>
                    <form action="" id="form_sample" class="form-horizontal" novalidate="novalidate">
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-xs-offset-2 col-xs-10">
                                    <a class='btn green gerigonder hide' href='#gerigonder' data-toggle='modal' data-id=''>Geri Gönder</a>
                                    <a class='btn green garantigonder hide' href='#garantigonder' data-toggle='modal' data-id=''>Garanti İçi Gönder</a>
                                    <a class='btn green telefononay hide' href='#telefononay' data-toggle='modal' data-id=''>Telefon ile Onay</a>
                                    <a class='btn green mailonay hide' href='#mailonay' data-toggle='modal' data-id=''>Mail ile Onay</a>
                                    <a class='btn green mailtekrar hide' href='#mailtekrar' data-toggle='modal' data-id=''>Tekrar Mail Gönder</a>
                                    <a class='btn green mailtelefon hide' href='#mailtelefon' data-toggle='modal' data-id=''>Telefon ile Onay</a>
                                    <a class='btn green yetkilionay hide' href='#yetkilionay' data-toggle='modal' data-id=''>Onayla</a>
                                    <a class='btn green yetkilired hide' href='#yetkilired' data-toggle='modal' data-id=''>Reddet</a>
                                    <a class='btn green subeyetkilionay hide' href='#subeyetkilionay' data-toggle='modal' data-id=''>Onayla</a>
                                    <a class='btn green subeyetkilired hide' href='#subeyetkilired' data-toggle='modal' data-id=''>Reddet</a>
                                    <a class='btn green subeaktar hide' href='#subeaktar' data-toggle='modal' data-id=''>Aktar</a>
                                </div>
                                <div class="hide">
                                    <input id="secilen" name="secilen"/>
                                    <input id="servis" name="servis" value="{{Auth::user()->servis->servisadi}}"/>
                                </div>
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
    <div class="modal fade" id="portlet-delete" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Ücretlendirme Bekleyen İşlemi Silinecek</h4>
                </div>
                <div class="modal-body">
                    Seçilen Ücretlendirme Kayıdını Silmek İstediğinizden Emin Misiniz?
                </div>
                <div class="modal-footer">
                    <a id="sayacid" href="" type="button" class="btn blue">Sil</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="detay-goster" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Ücretlendirilen Sayaçların Detayı
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Ücretlendirilen Sayaçların Detayı</h3>
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
                                            <div class="col-sm-6 col-xs-12 ">
                                                <div class="col-xs-12 onizlemekur">
                                                    <label class="col-xs-12 onizlemeeuro" style="padding-top: 9px;margin-left:5px;text-align:center">Euro : 0.0000 ₺</label>
                                                    <label class="col-xs-12 onizlemedolar" style="padding-top: 9px;margin-left:3px;text-align:center">Dolar : 0.0000 ₺</label>
                                                    <label class="col-xs-12 onizlemesterlin" style="padding-top: 9px;text-align:center">Sterlin : 0.0000 ₺</label>
                                                    <label class="control-label col-xs-12 warning" style="text-align:center"></label>
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
    <div class="modal fade" id="gerigonder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Sayaçlar Geri Gönderilecek
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{URL::to('ucretlendirme/gerigonder')}}" data-action="{{URL::to('ucretlendirme/gerigonder')}}" id="form_sample_3" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Aşağıdaki Sayaçlar İşlem Yapılmamış gibi Depoya Aktarılacaktır</h3>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Yeri:</label>
                                            <label class="col-xs-8 redyer" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-xs-8 redcariadi" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Nedeni:</label>
                                            <label class="col-xs-8 rednedeni" style="padding-top: 9px"></label>
                                        </div>
                                        <input class="hide" id="gerisecilenler" name="gerisecilenler"/>
                                        <input class="hide" id="geriadet" name="geriadet"/>
                                        <input class="hide" id="geriid" name="geriid"/>
                                        <h4 class="form-section col-xs-12">Sayaç Listesi
                                            <span style="padding-left:100px"><button type="button" class="btn green tumunusec3">Tümünü Seç</button>
                                                <button type="button" class="btn red temizle3">Temizle</button></span>
                                        </h4>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_3">
                                                <thead>
                                                <tr>
                                                    <th class="">#</th>
                                                    <th>Seri No</th>
                                                    <th>Sayaç Adı</th>
                                                    <th>Garanti</th>
                                                    <th>Fiyat Durum</th>
                                                    <th>&nbsp;&nbsp;Fiyatı&nbsp;&nbsp;</th>
                                                    <th>İndirim</th>
                                                    <th>Tutar</th>
                                                    <th>Kdv Tutarı</th>
                                                    <th>Toplam Tutar</th>
                                                    <th>İşlemler</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <h4 class="form-section">Fatura Bilgisi  <span style="font-size: 12px">Teslimat sonrası Netsis tarafında oluşacak faturayı temsil edecek</span>
                                            <label><input type="checkbox" id="gerifaturavar" name="gerifaturavar" checked/> Fatura Çıkacak mı? </label></h4>
                                        <div class="form-group gerifaturakismi">
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Fatura No:</label>
                                                <label class="col-xs-8 redfaturano" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Cari Kod:</label>
                                                <label class="col-xs-8 redcarikod" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-2 col-xs-4">Fatura Adresi:</label>
                                                <label class="col-xs-8 redfaturaadres" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-2 col-xs-4">Teslimat Adresi:</label>
                                                <div class="col-sm-8 col-xs-6">
                                                    <input type="text" id="redteslimadres" name="redteslimadres" data-required="1" class="form-control" maxlength="100"
                                                           placeholder="Fatura Adresinden Farklı Olduğu Durumlarda Girilecektir">
                                                </div>
                                                <div class="col-xs-2" style="text-align: center">
                                                    <button type="button" class="btn green redadressec" data-toggle="modal" data-target="#redadressec">Seç</button>
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Belge1:</label>
                                                <label class="col-xs-8 redaciklama1" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Belge2:</label>
                                                <div class="col-xs-8">
                                                    <input type="text" id="redaciklama2" name="redaciklama2" data-required="1" class="form-control" maxlength="100">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green gerionayla" data-toggle="modal" data-target="#gericonfirm">Geri Gönder</button>
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
                                                    <th>İşlemler</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-6 col-xs-12 ">
                                                <div class="col-xs-12 redkur">
                                                    <label class="col-xs-12 redeuro" style="padding-top: 9px;margin-left:5px;text-align:center">Euro : 0.0000 ₺</label>
                                                    <label class="col-xs-12 reddolar" style="padding-top: 9px;margin-left:3px;text-align:center">Dolar : 0.0000 ₺</label>
                                                    <label class="col-xs-12 redsterlin" style="padding-top: 9px;text-align:center">Sterlin : 0.0000 ₺</label>
                                                    <label class="control-label col-xs-12 redwarning" style="text-align:center"></label>
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
    <div class="modal fade" id="garantigonder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Sayaçlar Garanti İçinde Gönderilecek
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{URL::to('ucretlendirme/garantigonder')}}" data-action="{{URL::to('ucretlendirme/garantigonder')}}" id="form_sample_5" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Aşağıdaki Sayaçlar Garanti İçinde Depoya Aktarılacaktır</h3>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Yeri:</label>
                                            <label class="col-xs-8 garantiyer" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-xs-8 garanticariadi" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Nedeni:</label>
                                            <label class="col-xs-8 garantinedeni" style="padding-top: 9px"></label>
                                        </div>
                                        <input class="hide" id="garantisecilenler" name="garantisecilenler"/>
                                        <input class="hide" id="garantiadet" name="garantiadet"/>
                                        <input class="hide" id="garantiid" name="garantiid"/>
                                        <h4 class="form-section col-xs-12">Sayaç Listesi
                                            <span style="padding-left:100px"><button type="button" class="btn green tumunusec5">Tümünü Seç</button>
                                                <button type="button" class="btn red temizle5">Temizle</button></span>
                                        </h4>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_5">
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
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green garantionayla" data-toggle="modal" data-target="#garanticonfirm">Garanti İçi Gönder</button>
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
    <div class="modal fade" id="yetkilired" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Sayaçların Fiyatlandırması Reddedilecektir
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{URL::to('ucretlendirme/yetkilireddet')}}" data-action="{{URL::to('ucretlendirme/yetkilireddet')}}" id="form_sample_13" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Aşağıdaki Sayaçların Fiyatlandırması Reddedilecektir</h3>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-xs-8 yetkiliredcariadi" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Reddetme Nedeni:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><input type="text" id="yetkiliredneden" name="yetkiliredneden" value="{{ Input::old('yetkiliredneden') }}" data-required="1" class="form-control" maxlength="150">
                                            </div>
                                        </div>
                                        <input class="hide" id="yetkiliredsecilenler" name="yetkiliredsecilenler"/>
                                        <input class="hide" id="yetkiliredadet" name="yetkiliredadet"/>
                                        <input class="hide" id="yetkiliredid" name="yetkiliredid"/>
                                        <h4 class="form-section col-xs-12">Sayaç Listesi
                                            <span style="padding-left:100px"><button type="button" class="btn green tumunusec13">Tümünü Seç</button>
                                                <button type="button" class="btn red temizle13">Temizle</button></span>
                                        </h4>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_13">
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
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green yetkiliredonayla" data-toggle="modal" data-target="#yetkiliredconfirm">Reddet</button>
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
    <div class="modal fade" id="subeyetkilired" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Sayaçların Fiyatlandırması Reddedilecektir
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{URL::to('ucretlendirme/subeyetkilireddet')}}" data-action="{{URL::to('ucretlendirme/subeyetkilireddet')}}" id="form_sample_17" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Aşağıdaki Sayaçların Fiyatlandırması Reddedilecektir</h3>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-xs-8 subeyetkiliredcariadi" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Reddetme Nedeni:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><input type="text" id="subeyetkiliredneden" name="subeyetkiliredneden" value="{{ Input::old('subeyetkiliredneden') }}" data-required="1" class="form-control" maxlength="150">
                                            </div>
                                        </div>
                                        <input class="hide" id="subeyetkiliredsecilenler" name="subeyetkiliredsecilenler"/>
                                        <input class="hide" id="subeyetkiliredadet" name="subeyetkiliredadet"/>
                                        <input class="hide" id="subeyetkiliredid" name="subeyetkiliredid"/>
                                        <h4 class="form-section col-xs-12">Sayaç Listesi
                                            <span style="padding-left:100px"><button type="button" class="btn green tumunusec17">Tümünü Seç</button>
                                                <button type="button" class="btn red temizle17">Temizle</button></span>
                                        </h4>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_17">
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
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green subeyetkiliredonayla" data-toggle="modal" data-target="#subeyetkiliredconfirm">Reddet</button>
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
                                                <div class="col-sm-6 col-xs-12 detaykur">
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
    <div class="modal fade" id="telefononay" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Onaylama Sayfası
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{ URL::to('ucretlendirme/telefonileonayla') }}" data-action="{{URL::to('ucretlendirme/telefonileonayla')}}" id="form_sample_7" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <h3 class="form-section">Telefon ile Onaylama</h3>
                                        <h4 class="form-section">Cari Yetkili Bilgisi</h4>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Adı Soyadı:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><input type="text" id="teladisoyadi" name="teladisoyadi" value="{{ Input::old('teladisoyadi') }}" data-required="1" class="form-control" readonly="">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Telefon:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-4">
                                                <i class="fa"></i><input type="text" id="telyetkilitel" name="telyetkilitel" value="{{ Input::old('telyetkilitel') }}" data-required="1" class="form-control">
                                            </div>
                                            <div class="col-xs-2"><button type="button" class="btn blue yetkililistesi" data-toggle="modal" data-target="#yetkilibul">Seç</button></div>
                                            <div class="col-xs-2"><button type="button" class="btn green yeniyetkili" data-toggle="modal" data-target="#yeniyetkili">Yeni</button></div>
                                            <div class="hide"><input type="text" name="telyetkiliid" id="telyetkiliid"/></div>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-xs-8 telcariadi" style="padding-top: 9px">{{ Input::old('telcariadi') }}</label>
                                        </div>
                                        <h4 class="form-section col-xs-12">Bilgilendirme Maili <span style="font-size: 12px">Onaylanan Fiyatları Belirtilen Mail Adresine Gönderir.</span>
                                            <label><input type="checkbox" id="telmailvar" name="telmailvar" /> Bilgilendirme Maili Gitsin mi? </label>
                                            <label class="detay hide"><input type="checkbox" id=detaylifiyatlandirma name="detaylifiyatlandirma" /> Detaylı Fiyatlandırma Gitsin mi? </label>
                                        </h4>
                                        <div class="form-group mailvar hide col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Mail Adresi:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><input type="text" id="telyetkilimail" name="telyetkilimail" value="{{ Input::old('telyetkilimail') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group mailvar hide col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Diğer Mail Adresleri:</label>
                                            <div class="col-xs-8">
                                                <input type="text" id="telmailcc" name="telmailcc" value="{{ Input::old('telmailcc') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <h4 class="form-section col-xs-12">Sayaç Listesi
                                            <span style="padding-left:100px"><button type="button" class="btn green tumunusec7">Tümünü Seç</button>
                                                <button type="button" class="btn red temizle7">Temizle</button></span>
                                        </h4>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_7">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Seri No</th>
                                                    <th>Sayac Adı</th>
                                                    <th>Garanti</th>
                                                    <th>Fiyat Durumu</th>
                                                    <th>&nbsp;Fiyatı&nbsp;</th>
                                                    <th>İndirim</th>
                                                    <th>Tutar</th>
                                                    <th>Kdv Tutarı</th>
                                                    <th>Toplam Tutar</th>
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
                                                <div class="col-xs-12 telkur">
                                                    <label class="col-xs-12 teleuro" style="padding-top: 9px;margin-left:5px;text-align:center">0.0000 ₺</label>
                                                    <label class="col-xs-12 teldolar" style="padding-top: 9px;margin-left:3px;text-align:center">0.0000 ₺</label>
                                                    <label class="col-xs-12 telsterlin" style="padding-top: 9px;text-align:center">0.0000 ₺</label>
                                                    <label class="control-label col-xs-12 telwarning" style="text-align:center"></label>
                                                    <input id="teleuro" class="hide">
                                                    <input id="teldolar" class="hide">
                                                    <input id="telsterlin" class="hide">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TUTAR:</label>
                                                    <label class="col-xs-6 teltutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">İNDİRİM MİKTARI:</label>
                                                    <label class="col-xs-6 telindirimtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDVSIZ TUTAR:</label>
                                                    <label class="col-xs-6 telkdvsiztutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDV TUTARI:</label>
                                                    <label class="col-xs-6 telkdvtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TOPLAM TUTAR:</label>
                                                    <label class="col-xs-6 teltoplamtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <input class="hide" id="teltutar" name="teltutar"/>
                                                <input class="hide" id="teltutar2" name="teltutar2"/>
                                                <input class="hide" id="telindirimtutar" name="telindirimtutar"/>
                                                <input class="hide" id="telindirimtutar2" name="telindirimtutar2"/>
                                                <input class="hide" id="telkdvsiztutar" name="telkdvsiztutar"/>
                                                <input class="hide" id="telkdvsiztutar2" name="telkdvsiztutar2"/>
                                                <input class="hide" id="telkdvtutar" name="telkdvtutar"/>
                                                <input class="hide" id="telkdvtutar2" name="telkdvtutar2"/>
                                                <input class="hide" id="teltoplamtutar" name="teltoplamtutar"/>
                                                <input class="hide" id="teltoplamtutar2" name="teltoplamtutar2"/>
                                                <input class="hide" id="telbirim" name="telbirim"/>
                                                <input class="hide" id="telbirim2" name="telbirim2"/>
                                                <input class="hide" id="telbirimi" name="telbirimi"/>
                                                <input class="hide" id="telbirimi2" name="telbirimi2"/>
                                                <input class="hide" id="telkurtarih" name="telkurtarih"/>
                                                <input class="hide" id="telsecilenler" name="telsecilenler"/>
                                                <input class="hide" id="teladet" name="teladet"/>
                                                <input class="hide" id="teltumu" name="teltumu"/>
                                                <input class="hide" id="telgaranti" name="telgaranti"/>
                                                <input class="hide" id="telid" name="telid"/>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green telonayla" data-toggle="modal" data-target="#telefonconfirm">Onayla</button>
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
    <div class="modal fade" id="mailonay" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Onaylama Sayfası
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{ URL::to('ucretlendirme/mailileonayla') }}" data-action="{{URL::to('ucretlendirme/mailileonayla')}}" id="form_sample_8" method="POST" class="form-horizontal" novalidate="novalidate"  enctype="multipart/form-data">
                                    <div class="form-body">
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <h3 class="form-section">Mail ile Onaylama</h3>
                                        <h4 class="form-section">Cari Yetkili Bilgisi</h4>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Adı Soyadı:</label>
                                            <div class="col-xs-8">
                                                <input type="text" id="mailadisoyadi" name="mailadisoyadi" value="{{ Input::old('mailadisoyadi') }}" data-required="1" class="form-control" readonly="">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Maili:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-4">
                                                <i class="fa"></i><input type="email" id="mailyetkilimail" name="mailyetkilimail" value="{{Input::old('mailyetkilimail')}}" data-required="1" class="form-control" >
                                            </div>
                                            <div class="col-xs-2"><button type="button" class="btn blue yetkililistesi" data-toggle="modal" data-target="#yetkilibul">Seç</button></div>
                                            <div class="col-xs-2"><button type="button" class="btn green yeniyetkili" data-toggle="modal" data-target="#yeniyetkili">Yeni</button></div>
                                            <div class="hide"><input type="text" name="mailyetkiliid" id="mailyetkiliid"/></div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Diğer Mail Adresleri:</label>
                                            <div class="col-xs-8">
                                                <input type="text" id="mailcc" name="mailcc" value="{{ Input::old('mailcc') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-xs-8 mailcariadi" style="padding-top: 9px">{{ Input::old('mailcariadi') }}</label>
                                        </div>
                                        <h4 class="form-section">Mail İçeriği</h4>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Konusu:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><input type="text" id="mailkonu" name="mailkonu" value="{{ Input::old('mailkonu') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-offset-1 col-sm-10 col-xs-12">
                                                <textarea2 id="mailicerik" name="mailicerik">{{Input::old('mailicerik') ? Input::old('mailicerik') :'' }}</textarea2>
                                                <input type="text" id="icerik" name="icerik" value="{{ Input::old('icerik') }}" data-required="1" class="form-control hide"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Onay Linki:</label>
                                            <div class="col-xs-8" style="margin-top:9px">
                                                <a id="maillink" target="_blank" href="">Onaylama sayfası için buraya tıklayınız.</a>
                                                <input name="maillink" class="maillink hide"/>
                                                <input name="mailroot" class="mailroot hide"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Ekteki Dosyalar:</label>
                                            <div class="col-xs-8" style="margin-top:9px">
                                                <a id="mailek1" target="_blank" href="">Fiyatlandırma Tablosu</a>;
                                                <a id="mailek2" target="_blank" href="">Onay Formu</a>;
                                                <a id="caribilgi" target="_blank" href="">Manas Cari Bilgileri</a>
                                                <label><input type="checkbox" id=detaylifiyatlandirma name="detaylifiyatlandirma" /> Detaylı Fiyatlandırma Gitsin mi? </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Eklenecek Diğer Dosyalar:</label>
                                            <div class="col-xs-6 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="input-group input-large">
                                                    <div class="form-control uneditable-input input-fixed input-xlarge" data-trigger="fileinput">
                                                        <i class="fa fa-file fileinput-exists"></i><span class="fileinput-filename"></span>
                                                    </div>
                                                    <span class="input-group-addon btn default btn-file">
                                                    <span class="fileinput-new">
                                                    Dosya Seç </span>
                                                    <span class="fileinput-exists">
                                                    Değiştir </span>
                                                    <input type="file" name="dosya[]" accept="*" multiple="" />
                                                    </span>
                                                    <a href="" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="form-section col-xs-12">Sayaç Listesi
                                            <span style="padding-left:100px"><button type="button" class="btn green tumunusec8">Tümünü Seç</button>
                                                <button type="button" class="btn red temizle8">Temizle</button></span>
                                        </h4>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_8">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Seri No</th>
                                                    <th>Sayac Adı</th>
                                                    <th>Garanti</th>
                                                    <th>Fiyat Durumu</th>
                                                    <th>&nbsp;Fiyatı&nbsp;</th>
                                                    <th>İndirim</th>
                                                    <th>Tutar</th>
                                                    <th>Kdv Tutarı</th>
                                                    <th>Toplam Tutar</th>
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
                                                <div class="col-xs-12 mailkur">
                                                    <label class="col-xs-12 maileuro" style="padding-top: 9px;margin-left:5px;text-align:center">Euro : 0.0000 ₺</label>
                                                    <label class="col-xs-12 maildolar" style="padding-top: 9px;margin-left:3px;text-align:center">Dolar 0.0000 ₺</label>
                                                    <label class="col-xs-12 mailsterlin" style="padding-top: 9px;text-align:center">Sterlin 0.0000 ₺</label>
                                                    <label class="control-label col-xs-12 mailwarning" style="text-align:center"></label>
                                                    <input id="maileuro" class="hide">
                                                    <input id="maildolar" class="hide">
                                                    <input id="mailsterlin" class="hide">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TUTAR:</label>
                                                    <label class="col-xs-6 mailtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">İNDİRİM MİKTARI:</label>
                                                    <label class="col-xs-6 mailindirimtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDVSIZ TUTAR:</label>
                                                    <label class="col-xs-6 mailkdvsiztutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDV TUTARI:</label>
                                                    <label class="col-xs-6 mailkdvtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TOPLAM TUTAR:</label>
                                                    <label class="col-xs-6 mailtoplamtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <input class="hide" id="mailtutar" name="mailtutar"/>
                                                <input class="hide" id="mailtutar2" name="mailtutar2"/>
                                                <input class="hide" id="mailindirimtutar" name="mailindirimtutar"/>
                                                <input class="hide" id="mailindirimtutar2" name="mailindirimtutar2"/>
                                                <input class="hide" id="mailkdvsiztutar" name="mailkdvsiztutar"/>
                                                <input class="hide" id="mailkdvsiztutar2" name="mailkdvsiztutar2"/>
                                                <input class="hide" id="mailkdvtutar" name="mailkdvtutar"/>
                                                <input class="hide" id="mailkdvtutar2" name="mailkdvtutar2"/>
                                                <input class="hide" id="mailtoplamtutar" name="mailtoplamtutar"/>
                                                <input class="hide" id="mailtoplamtutar2" name="mailtoplamtutar2"/>
                                                <input class="hide" id="mailbirim" name="mailbirim"/>
                                                <input class="hide" id="mailbirim2" name="mailbirim2"/>
                                                <input class="hide" id="mailbirimi" name="mailbirimi"/>
                                                <input class="hide" id="mailbirimi2" name="mailbirimi2"/>
                                                <input class="hide" id="mailkurtarih" name="mailkurtarih"/>
                                                <input class="hide" id="mailsecilenler" name="mailsecilenler"/>
                                                <input class="hide" id="mailadet" name="mailadet"/>
                                                <input class="hide" id="mailtumu" name="mailtumu"/>
                                                <input class="hide" id="mailgaranti" name="mailgaranti"/>
                                                <input class="hide" id="mailid" name="mailid"/>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green mailonayla" data-toggle="modal" data-target="#mailconfirm">Gönder</button>
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
    <div class="modal fade" id="mailtekrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Onaylama Sayfası
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{ URL::to('ucretlendirme/tekrarmailgonder') }}" data-action="{{URL::to('ucretlendirme/tekrarmailgonder')}}" id="form_sample_14" method="POST" class="form-horizontal" novalidate="novalidate"  enctype="multipart/form-data">
                                    <div class="form-body">
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <h3 class="form-section">Tekrar Mail Gönderimi</h3>
                                        <h4 class="form-section">Cari Yetkili Bilgisi</h4>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Adı Soyadı:</label>
                                            <div class="col-xs-8">
                                                <input type="text" id="tekrarmailadisoyadi" name="tekrarmailadisoyadi" value="{{ Input::old('tekrarmailadisoyadi') }}" data-required="1" class="form-control" readonly="">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Maili:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-4">
                                                <i class="fa"></i><input type="email" id="tekrarmailyetkilimail" name="tekrarmailyetkilimail" value="{{Input::old('tekrarmailyetkilimail')}}" data-required="1" class="form-control" >
                                            </div>
                                            <div class="col-xs-2"><button type="button" class="btn blue yetkililistesi" data-toggle="modal" data-target="#yetkilibul">Seç</button></div>
                                            <div class="col-xs-2"><button type="button" class="btn green yeniyetkili" data-toggle="modal" data-target="#yeniyetkili">Yeni</button></div>
                                            <div class="hide"><input type="text" name="tekrarmailyetkiliid" id="tekrarmailyetkiliid"/></div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Diğer Mail Adresleri:</label>
                                            <div class="col-xs-8">
                                                <input type="text" id="tekrarmailcc" name="tekrarmailcc" value="{{ Input::old('tekrarmailcc') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-xs-8 tekrarmailcariadi" style="padding-top: 9px">{{ Input::old('tekrarmailcariadi') }}</label>
                                        </div>
                                        <h4 class="form-section">Mail İçeriği</h4>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Konusu:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><input type="text" id="tekrarmailkonu" name="tekrarmailkonu" value="{{ Input::old('tekrarmailkonu') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-offset-1 col-sm-10 col-xs-12">
                                                <textarea2 id="tekrarmailicerik" name="tekrarmailicerik">{{Input::old('tekrarmailicerik') ? Input::old('tekrarmailicerik') :'' }}</textarea2>
                                                <input type="text" id="tekraricerik" name="tekraricerik" value="{{ Input::old('tekraricerik') }}" data-required="1" class="form-control hide"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Onay Linki:</label>
                                            <div class="col-xs-8" style="margin-top:9px">
                                                <a id="tekrarmaillink" target="_blank" href="">Onaylama sayfası için buraya tıklayınız.</a>
                                                <input name="tekrarmaillink" class="tekrarmaillink hide"/>
                                                <input name="tekrarmailroot" class="tekrarmailroot hide"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Ekteki Dosyalar:</label>
                                            <div class="col-xs-8" style="margin-top:9px">
                                                <a id="tekrarmailek1" target="_blank" href="">Fiyatlandırma Tablosu</a>;
                                                <a id="tekrarmailek2" target="_blank" href="">Onay Formu</a>;
                                                <a id="tekrarcaribilgi" target="_blank" href="">Manas Cari Bilgileri</a>
                                                <label><input type="checkbox" id=detaylifiyatlandirma name="detaylifiyatlandirma" /> Detaylı Fiyatlandırma Gitsin mi? </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Eklenecek Diğer Dosyalar:</label>
                                            <div class="col-xs-6 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="input-group input-large">
                                                    <div class="form-control uneditable-input input-fixed input-xlarge" data-trigger="fileinput">
                                                        <i class="fa fa-file fileinput-exists"></i><span class="fileinput-filename"></span>
                                                    </div>
                                                    <span class="input-group-addon btn default btn-file">
                                                    <span class="fileinput-new">
                                                    Dosya Seç </span>
                                                    <span class="fileinput-exists">
                                                    Değiştir </span>
                                                    <input type="file" name="tekrardosya[]" accept="*" multiple="" />
                                                    </span>
                                                    <a href="" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="form-section col-xs-12">Sayaç Listesi
                                        </h4>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_14">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Seri No</th>
                                                    <th>Sayac Adı</th>
                                                    <th>Garanti</th>
                                                    <th>Fiyat Durumu</th>
                                                    <th>&nbsp;Fiyatı&nbsp;</th>
                                                    <th>İndirim</th>
                                                    <th>Tutar</th>
                                                    <th>Kdv Tutarı</th>
                                                    <th>Toplam Tutar</th>
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
                                                <div class="col-xs-12 tekrarmailkur">
                                                    <label class="col-xs-12 tekrarmaileuro" style="padding-top: 9px;margin-left:5px;text-align:center">Euro : 0.0000 ₺</label>
                                                    <label class="col-xs-12 tekrarmaildolar" style="padding-top: 9px;margin-left:3px;text-align:center">Dolar 0.0000 ₺</label>
                                                    <label class="col-xs-12 tekrarmailsterlin" style="padding-top: 9px;text-align:center">Sterlin 0.0000 ₺</label>
                                                    <label class="control-label col-xs-12 tekrarmailwarning" style="text-align:center"></label>
                                                    <input id="tekrarmaileuro" class="hide">
                                                    <input id="tekrarmaildolar" class="hide">
                                                    <input id="tekrarmailsterlin" class="hide">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TUTAR:</label>
                                                    <label class="col-xs-6 tekrarmailtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">İNDİRİM MİKTARI:</label>
                                                    <label class="col-xs-6 tekrarmailindirimtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDVSIZ TUTAR:</label>
                                                    <label class="col-xs-6 tekrarmailkdvsiztutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDV TUTARI:</label>
                                                    <label class="col-xs-6 tekrarmailkdvtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TOPLAM TUTAR:</label>
                                                    <label class="col-xs-6 tekrarmailtoplamtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <input class="hide" id="tekrarmailtutar" name="mailtutar"/>
                                                <input class="hide" id="tekrarmailtutar2" name="mailtutar2"/>
                                                <input class="hide" id="tekrarmailindirimtutar" name="tekrarmailindirimtutar"/>
                                                <input class="hide" id="tekrarmailindirimtutar2" name="tekrarmailindirimtutar2"/>
                                                <input class="hide" id="tekrarmailkdvsiztutar" name="tekrarmailkdvsiztutar"/>
                                                <input class="hide" id="tekrarmailkdvsiztutar2" name="tekrarmailkdvsiztutar2"/>
                                                <input class="hide" id="tekrarmailkdvtutar" name="tekrarmailkdvtutar"/>
                                                <input class="hide" id="tekrarmailkdvtutar2" name="tekrarmailkdvtutar2"/>
                                                <input class="hide" id="tekrarmailtoplamtutar" name=tekrar"mailtoplamtutar"/>
                                                <input class="hide" id="tekrarmailtoplamtutar2" name=tekrar"mailtoplamtutar2"/>
                                                <input class="hide" id="tekrarmailbirim" name="tekrarmailbirim"/>
                                                <input class="hide" id="tekrarmailbirim2" name="tekrarmailbirim2"/>
                                                <input class="hide" id="tekrarmailbirimi" name="tekrarmailbirimi"/>
                                                <input class="hide" id="tekrarmailbirimi2" name="tekrarmailbirimi2"/>
                                                <input class="hide" id="tekrarmailkurtarih" name="tekrarmailkurtarih"/>
                                                <input class="hide" id="tekrarmailsecilenler" name="tekrarmailsecilenler"/>
                                                <input class="hide" id="tekrarmailadet" name=tekrar"mailadet"/>
                                                <input class="hide" id="tekrarmailtumu" name="tekrarmailtumu"/>
                                                <input class="hide" id="tekrarmailgaranti" name="tekrarmailgaranti"/>
                                                <input class="hide" id="tekrarmailid" name="tekrarmailid"/>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green tekrarmailgonder" data-toggle="modal" data-target="#tekrarmailconfirm">Gönder</button>
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
    <div class="modal fade" id="mailtelefon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Onaylama Sayfası
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{ URL::to('ucretlendirme/mailtelefonileonayla') }}" data-action="{{URL::to('ucretlendirme/mailtelefonileonayla')}}" id="form_sample_15" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <h3 class="form-section">Mail Sonrası Telefon ile Onaylama</h3>
                                        <h4 class="form-section">Cari Yetkili Bilgisi</h4>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Adı Soyadı:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><input type="text" id="mailteladisoyadi" name="mailteladisoyadi" value="{{ Input::old('mailteladisoyadi') }}" data-required="1" class="form-control" readonly="">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Telefon:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-4">
                                                <i class="fa"></i><input type="text" id="mailtelyetkilitel" name="mailtelyetkilitel" value="{{ Input::old('mailtelyetkilitel') }}" data-required="1" class="form-control">
                                            </div>
                                            <div class="col-xs-2"><button type="button" class="btn blue yetkililistesi" data-toggle="modal" data-target="#yetkilibul">Seç</button></div>
                                            <div class="col-xs-2"><button type="button" class="btn green yeniyetkili" data-toggle="modal" data-target="#yeniyetkili">Yeni</button></div>
                                            <div class="hide"><input type="text" name="mailtelyetkiliid" id="mailtelyetkiliid"/></div>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-xs-8 mailtelcariadi" style="padding-top: 9px">{{ Input::old('mailtelcariadi') }}</label>
                                        </div>
                                        <h4 class="form-section col-xs-12">Bilgilendirme Maili <span style="font-size: 12px">Onaylanan Fiyatları Belirtilen Mail Adresine Gönderir.</span>
                                            <label><input type="checkbox" id="tekrarmailvar" name="tekrarmailvar" /> Bilgilendirme Maili Gitsin mi? </label>
                                            <label class="detay hide"><input type="checkbox" id=detaylifiyatlandirma name="detaylifiyatlandirma" /> Detaylı Fiyatlandırma Gitsin mi? </label>
                                        </h4>
                                        <div class="form-group mailvar hide col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Mail Adresi:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><input type="text" id="mailtelyetkilimail" name="mailtelyetkilimail" value="{{ Input::old('mailtelyetkilimail') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group mailvar hide col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Diğer Mail Adresleri:</label>
                                            <div class="col-xs-8">
                                                <input type="text" id="mailtelmailcc" name="mailtelmailcc" value="{{ Input::old('mailtelmailcc') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <h4 class="form-section col-xs-12">Sayaç Listesi
                                        </h4>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_15">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Seri No</th>
                                                    <th>Sayac Adı</th>
                                                    <th>Garanti</th>
                                                    <th>Fiyat Durumu</th>
                                                    <th>&nbsp;Fiyatı&nbsp;</th>
                                                    <th>İndirim</th>
                                                    <th>Tutar</th>
                                                    <th>Kdv Tutarı</th>
                                                    <th>Toplam Tutar</th>
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
                                                <div class="col-xs-12 mailtelkur">
                                                    <label class="col-xs-12 mailteleuro" style="padding-top: 9px;margin-left:5px;text-align:center">0.0000 ₺</label>
                                                    <label class="col-xs-12 mailteldolar" style="padding-top: 9px;margin-left:3px;text-align:center">0.0000 ₺</label>
                                                    <label class="col-xs-12 mailtelsterlin" style="padding-top: 9px;text-align:center">0.0000 ₺</label>
                                                    <label class="control-label col-xs-12 mailtelwarning" style="text-align:center"></label>
                                                    <input id="mailteleuro" class="hide">
                                                    <input id="mailteldolar" class="hide">
                                                    <input id="mailtelsterlin" class="hide">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TUTAR:</label>
                                                    <label class="col-xs-6 mailteltutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">İNDİRİM MİKTARI:</label>
                                                    <label class="col-xs-6 mailtelindirimtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDVSIZ TUTAR:</label>
                                                    <label class="col-xs-6 mailtelkdvsiztutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDV TUTARI:</label>
                                                    <label class="col-xs-6 mailtelkdvtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TOPLAM TUTAR:</label>
                                                    <label class="col-xs-6 mailteltoplamtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <input class="hide" id="mailteltutar" name="mailteltutar"/>
                                                <input class="hide" id="mailteltutar2" name="mailteltutar2"/>
                                                <input class="hide" id="mailtelindirimtutar" name="mailtelindirimtutar"/>
                                                <input class="hide" id="mailtelindirimtutar2" name="mailtelindirimtutar2"/>
                                                <input class="hide" id="mailtelkdvsiztutar" name="mailtelkdvsiztutar"/>
                                                <input class="hide" id="mailtelkdvsiztutar2" name="mailtelkdvsiztutar2"/>
                                                <input class="hide" id="mailtelkdvtutar" name="mailtelkdvtutar"/>
                                                <input class="hide" id="mailtelkdvtutar2" name="mailtelkdvtutar2"/>
                                                <input class="hide" id="mailteltoplamtutar" name="mailteltoplamtutar"/>
                                                <input class="hide" id="mailteltoplamtutar2" name="mailteltoplamtutar2"/>
                                                <input class="hide" id="mailtelbirim" name="mailtelbirim"/>
                                                <input class="hide" id="mailtelbirim2" name="mailtelbirim2"/>
                                                <input class="hide" id="mailtelbirimi" name="mailtelbirimi"/>
                                                <input class="hide" id="mailtelbirimi2" name="mailtelbirimi2"/>
                                                <input class="hide" id="mailtelkurtarih" name="mailtelkurtarih"/>
                                                <input class="hide" id="mailtelsecilenler" name="mailtelsecilenler"/>
                                                <input class="hide" id="mailteladet" name="mailteladet"/>
                                                <input class="hide" id="mailteltumu" name="mailteltumu"/>
                                                <input class="hide" id="mailtelgaranti" name="mailtelgaranti"/>
                                                <input class="hide" id="mailtelid" name="mailtelid"/>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green mailtelonayla" data-toggle="modal" data-target="#mailtelconfirm">Onayla</button>
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
    <div class="modal fade" id="yetkilionay" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Onaylama Sayfası
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{ URL::to('ucretlendirme/yetkilionayla') }}" data-action="{{URL::to('ucretlendirme/yetkilionayla')}}" id="form_sample_12" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <h3 class="form-section">Yetkili Onaylama</h3>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-xs-8 onaycariadi" style="padding-top: 9px">{{ Input::old('onaycariadi') }}</label>
                                        </div>
                                        <h4 class="form-section col-xs-12">Bilgilendirme Maili <span style="font-size: 12px">Onaylanan Fiyatları Belirtilen Mail Adresine Gönderir.</span>
                                            <label><input type="checkbox" id="onaymailvar" name="onaymailvar" /> Bilgilendirme Maili Gitsin mi? </label>
                                            <label class="onaydetay hide"><input type="checkbox" id=detaylifiyatlandirma name="detaylifiyatlandirma" /> Detaylı Fiyatlandırma Gitsin mi? </label>
                                        </h4>
                                        <div class="form-group onaymailvar hide col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Mail Adresi:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><input type="text" id="onayyetkilimail" name="onayyetkilimail" value="{{ Input::old('onayyetkilimail') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group onaymailvar hide col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Diğer Mail Adresleri:</label>
                                            <div class="col-xs-8">
                                                <input type="text" id="onaymailcc" name="onaymailcc" value="{{ Input::old('onaymailcc') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <h4 class="form-section col-xs-12">Sayaç Listesi
                                            <span style="padding-left:100px"><button type="button" class="btn green tumunusec12">Tümünü Seç</button>
                                                <button type="button" class="btn red temizle12">Temizle</button></span>
                                        </h4>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_12">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Seri No</th>
                                                    <th>Sayac Adı</th>
                                                    <th>Garanti</th>
                                                    <th>Fiyat Durumu</th>
                                                    <th>&nbsp;Fiyatı&nbsp;</th>
                                                    <th>İndirim</th>
                                                    <th>Tutar</th>
                                                    <th>Kdv Tutarı</th>
                                                    <th>Toplam Tutar</th>
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
                                                <div class="col-xs-12 onaykur">
                                                    <label class="col-xs-12 onayeuro" style="padding-top: 9px;margin-left:5px;text-align:center">Euro : 0.0000 ₺</label>
                                                    <label class="col-xs-12 onaydolar" style="padding-top: 9px;margin-left:3px;text-align:center">Dolar : 0.0000 ₺</label>
                                                    <label class="col-xs-12 onaysterlin" style="padding-top: 9px;text-align:center">Sterlin : 0.0000 ₺</label>
                                                    <label class="control-label col-xs-12 onaywarning" style="text-align:center"></label>
                                                    <input id="onayeuro" class="hide">
                                                    <input id="onaydolar" class="hide">
                                                    <input id="onaysterlin" class="hide">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TUTAR:</label>
                                                    <label class="col-xs-6 onaytutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">İNDİRİM MİKTARI:</label>
                                                    <label class="col-xs-6 onayindirimtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDVSIZ TUTAR:</label>
                                                    <label class="col-xs-6 onaykdvsiztutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDV TUTARI:</label>
                                                    <label class="col-xs-6 onaykdvtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TOPLAM TUTAR:</label>
                                                    <label class="col-xs-6 onaytoplamtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <input class="hide" id="onaytutar" name="onaytutar"/>
                                                <input class="hide" id="onaytutar2" name="onaytutar2"/>
                                                <input class="hide" id="onayindirimtutar" name="onayindirimtutar"/>
                                                <input class="hide" id="onayindirimtutar2" name="onayindirimtutar2"/>
                                                <input class="hide" id="onaykdvsiztutar" name="onaykdvsiztutar"/>
                                                <input class="hide" id="onaykdvsiztutar2" name="onaykdvsiztutar2"/>
                                                <input class="hide" id="onaykdvtutar" name="onaykdvtutar"/>
                                                <input class="hide" id="onaykdvtutar2" name="onaykdvtutar2"/>
                                                <input class="hide" id="onaytoplamtutar" name="onaytoplamtutar"/>
                                                <input class="hide" id="onaytoplamtutar2" name="onaytoplamtutar2"/>
                                                <input class="hide" id="onaybirim" name="onaybirim"/>
                                                <input class="hide" id="onaybirim2" name="onaybirim2"/>
                                                <input class="hide" id="onaybirimi" name="onaybirimi"/>
                                                <input class="hide" id="onaybirimi2" name="onaybirimi2"/>
                                                <input class="hide" id="onaykurtarih" name="onaykurtarih"/>
                                                <input class="hide" id="onaysecilenler" name="onaysecilenler"/>
                                                <input class="hide" id="onayadet" name="onayadet"/>
                                                <input class="hide" id="onaytumu" name="onaytumu"/>
                                                <input class="hide" id="onaygaranti" name="onaygaranti"/>
                                                <input class="hide" id="onayid" name="onayid"/>
                                                <input class="hide" id="yetkiliid" name="yetkiliid"/>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green yetkilionayla" data-toggle="modal" data-target="#yetkiliconfirm">Onayla</button>
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
    <div class="modal fade" id="subeyetkilionay" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Onaylama Sayfası
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{ URL::to('ucretlendirme/subeyetkilionayla') }}" data-action="{{URL::to('ucretlendirme/subeyetkilionayla')}}" id="form_sample_16" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <h3 class="form-section">Şube Yetkili Onaylama</h3>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-xs-8 subeonaycariadi" style="padding-top: 9px">{{ Input::old('subeonaycariadi') }}</label>
                                        </div>
                                        <h4 class="form-section col-xs-12">Bilgilendirme Maili <span style="font-size: 12px">Onaylanan Fiyatları Belirtilen Mail Adresine Gönderir.</span>
                                            <label><input type="checkbox" id="subeonaymailvar" name="subeonaymailvar" /> Bilgilendirme Maili Gitsin mi? </label>
                                            <label class="subeonaydetay hide"><input type="checkbox" id=detaylifiyatlandirma name="detaylifiyatlandirma" /> Detaylı Fiyatlandırma Gitsin mi? </label>
                                        </h4>
                                        <div class="form-group subeonaymailvar hide col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Mail Adresi:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><input type="text" id="onaysubeyetkilimail" name="onaysubeyetkilimail" value="{{ Input::old('onaysubeyetkilimail') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group subeonaymailvar hide col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Diğer Mail Adresleri:</label>
                                            <div class="col-xs-8">
                                                <input type="text" id="onaymailcc" name="onaymailcc" value="{{ Input::old('onaymailcc') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <h4 class="form-section col-xs-12">Sayaç Listesi
                                            <span style="padding-left:100px"><button type="button" class="btn green tumunusec16">Tümünü Seç</button>
                                                <button type="button" class="btn red temizle16">Temizle</button></span>
                                        </h4>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_16">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Seri No</th>
                                                    <th>Sayac Adı</th>
                                                    <th>Garanti</th>
                                                    <th>Fiyat Durumu</th>
                                                    <th>&nbsp;Fiyatı&nbsp;</th>
                                                    <th>İndirim</th>
                                                    <th>Tutar</th>
                                                    <th>Kdv Tutarı</th>
                                                    <th>Toplam Tutar</th>
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
                                                <div class="col-xs-12 subeonaykur">
                                                    <label class="col-xs-12 subeonayeuro" style="padding-top: 9px;margin-left:5px;text-align:center">Euro : 0.0000 ₺</label>
                                                    <label class="col-xs-12 subeonaydolar" style="padding-top: 9px;margin-left:3px;text-align:center">Dolar : 0.0000 ₺</label>
                                                    <label class="col-xs-12 subeonaysterlin" style="padding-top: 9px;text-align:center">Sterlin : 0.0000 ₺</label>
                                                    <label class="control-label col-xs-12 subeonaywarning" style="text-align:center"></label>
                                                    <input id="subeonayeuro" class="hide">
                                                    <input id="subeonaydolar" class="hide">
                                                    <input id="subeonaysterlin" class="hide">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TUTAR:</label>
                                                    <label class="col-xs-6 subeonaytutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">İNDİRİM MİKTARI:</label>
                                                    <label class="col-xs-6 subeonayindirimtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDVSIZ TUTAR:</label>
                                                    <label class="col-xs-6 subeonaykdvsiztutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDV TUTARI:</label>
                                                    <label class="col-xs-6 subeonaykdvtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TOPLAM TUTAR:</label>
                                                    <label class="col-xs-6 subeonaytoplamtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <input class="hide" id="subeonaytutar" name="subeonaytutar"/>
                                                <input class="hide" id="subeonaytutar2" name="subeonaytutar2"/>
                                                <input class="hide" id="subeonayindirimtutar" name="subeonayindirimtutar"/>
                                                <input class="hide" id="subeonayindirimtutar2" name="subeonayindirimtutar2"/>
                                                <input class="hide" id="subeonaykdvsiztutar" name="subeonaykdvsiztutar"/>
                                                <input class="hide" id="subeonaykdvsiztutar2" name="subeonaykdvsiztutar2"/>
                                                <input class="hide" id="subeonaykdvtutar" name="subeonaykdvtutar"/>
                                                <input class="hide" id="subeonaykdvtutar2" name="subeonaykdvtutar2"/>
                                                <input class="hide" id="subeonaytoplamtutar" name="subeonaytoplamtutar"/>
                                                <input class="hide" id="subeonaytoplamtutar2" name="subeonaytoplamtutar2"/>
                                                <input class="hide" id="subeonaybirim" name="subeonaybirim"/>
                                                <input class="hide" id="subeonaybirim2" name="subeonaybirim2"/>
                                                <input class="hide" id="subeonaybirimi" name="subeonaybirimi"/>
                                                <input class="hide" id="subeonaybirimi2" name="subeonaybirimi2"/>
                                                <input class="hide" id="subeonaykurtarih" name="subeonaykurtarih"/>
                                                <input class="hide" id="subeonaysecilenler" name="subeonaysecilenler"/>
                                                <input class="hide" id="subeonayadet" name="subeonayadet"/>
                                                <input class="hide" id="subeonaytumu" name="subeonaytumu"/>
                                                <input class="hide" id="subeonaygaranti" name="subeonaygaranti"/>
                                                <input class="hide" id="subeonayid" name="subeonayid"/>
                                                <input class="hide" id="subeyetkiliid" name="subeyetkiliid"/>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green subeyetkilionayla" data-toggle="modal" data-target="#subeyetkiliconfirm">Onayla</button>
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
    <div class="modal fade" id="yetkililistesi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Yetkili Listesi
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_9" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Yetkili Listesi</h3>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_9">
                                                <thead>
                                                <tr>
                                                    <th class="">#</th>
                                                    <th>Adı Soyadı</th>
                                                    <th>Telefon</th>
                                                    <th>Email</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="hide"><input type="text" id="yetkilisecilen" name="yetkilisecilen"/></div>
                                        <div class="hide"><input type="text" id="yetkilisecilenadi" name="yetkilisecilenadi"/></div>
                                        <div class="hide"><input type="text" id="yetkilisecilentel" name="yetkilisecilentel"/></div>
                                        <div class="hide"><input type="text" id="yetkilisecilenemail" name="yetkilisecilenemail"/></div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn default yetkilisec" data-dismiss="modal">Tamam</button>
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
    <div class="modal fade" id="yetkilibul" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Yetkili Listesi
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_10" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Yetkili Listesi</h3>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_10">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Adı Soyadı</th>
                                                    <th>Telefon</th>
                                                    <th>Email</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="hide"><input type="text" id="yetkilibulsecilen" name="yetkilibulsecilen"/></div>
                                        <div class="hide"><input type="text" id="yetkilibulsecilenadi" name="yetkilibulsecilenadi"/></div>
                                        <div class="hide"><input type="text" id="yetkilibulsecilentel" name="yetkilibulsecilentel"/></div>
                                        <div class="hide"><input type="text" id="yetkilibulsecilenemail" name="yetkilibulsecilenemail"/></div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn default yetkilibul" data-dismiss="modal">Tamam</button>
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
    <div class="modal fade" id="yeniyetkili" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Yeni Yetkili
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_11" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body col-xs-12">
                                        <h3 class="form-section col-xs-12">Yetkili Ekle</h3>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Adı:</label>
                                            <div class="col-xs-8">
                                                <input type="text" id="yeniadi" name="yeniadi" value="{{ Input::old('yeniadi') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Soyadı:</label>
                                            <div class="col-xs-8">
                                                <input type="text" id="yenisoyadi" name="yenisoyadi" value="{{ Input::old('yenisoyadi') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Telefon:</label>
                                            <div class="col-xs-8">
                                                <input type="text" id="yenitel" name="yenitel" value="{{ Input::old('yenitel') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Maili:</label>
                                            <div class="col-xs-8">
                                                <input type="text" id="yenimail" name="yenimail" value="{{ Input::old('yenimail') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-offset-4 col-xs-4">
                                                    <button type="button" class="btn green yeniyetkiliekle" data-dismiss="modal">Ekle</button>
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
    <div class="modal fade" id="redadressec" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Teslimat Adresi Listesi
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_18" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Önceki Teslimat Adresleri</h3>
                                        <input class="hide" id="gerisecilenadres" name="gerisecilenadres"/>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_18">
                                                <thead>
                                                <tr>
                                                    <th>Teslimat Adresi</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green geriteslimadresisec" data-dismiss="modal">Tamam</button>
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
    <div class="modal fade" id="fiyat-sil" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Fiyat Ücretlendirme Bekleyenlere Geri Gönderilecektir</h4>
                </div>
                <div class="modal-body">
                    Seçilen Fiyatı Silmek İstediğinizden Emin Misiniz?
                </div>
                <div class="modal-footer">
                    <a id="fiyatid" href="" type="button" class="btn blue">Sil</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="gericonfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Sayaçlar Geri Gönderilece</h4>
                </div>
                <div class="modal-body">
                    Fiyatlandırmaya Ait Sayaçlar İşlem Yapılmadan Geri Gönderilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formgeri" href="#" type="button" data-dismiss="modal" class="btn green">Onayla</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="garanticonfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Garanti İçinde Gönderilecek</h4>
                </div>
                <div class="modal-body">
                    Reddedilen Fiyatlandırma Garanti İçinde Gönderilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formgaranti" href="#" type="button" data-dismiss="modal" class="btn green">Onayla</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="telefonconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Telefon ile Onaylanacak</h4>
                </div>
                <div class="modal-body">
                    Girilen Bilgilere Göre Fiyatlandırma Telefonla Onaylanmış Şekilde Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formtelefon" href="#" type="button" data-dismiss="modal" class="btn green">Onayla</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mailconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Mail ile Onaylanacak?</h4>
                </div>
                <div class="modal-body">
                    Girilen Bilgilere Göre Fiyatlandırma Onay için Müşteriye Gönderilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formmail" href="#" type="button" data-dismiss="modal" class="btn green">Onayla</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="tekrarmailconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Tekrar Mail Gönderilecek?</h4>
                </div>
                <div class="modal-body">
                    Girilen Bilgilere Göre Fiyatlandırma onay için Müşteriye Tekrar Gönderilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formtekrarmail" href="#" type="button" data-dismiss="modal" class="btn green">Onayla</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mailtelconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Mail Sonrası Telefon ile Onaylanacak</h4>
                </div>
                <div class="modal-body">
                    Girilen Bilgilere Göre Fiyatlandırma Telefonla Onaylanmış Şekilde Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formmailtel" href="#" type="button" data-dismiss="modal" class="btn green">Onayla</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="yetkiliconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Seçilen Sayaçlar Onaylanacak</h4>
                </div>
                <div class="modal-body">
                    Girilen Bilgilere Göre Fiyatlandırma Onaylanmış Şekilde Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formyetkili" href="#" type="button" data-dismiss="modal" class="btn green">Onayla</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="yetkiliredconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Seçilen Sayaçlara Ait Fiyatlandırma Reddedilecektir</h4>
                </div>
                <div class="modal-body">
                    Girilen Bilgilere Göre Fiyatlandırma Reddedilmiş Şekilde Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formyetkilireddet" href="#" type="button" data-dismiss="modal" class="btn green">Onayla</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="subeyetkiliconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Seçilen Sayaçlar Onaylanacak</h4>
                </div>
                <div class="modal-body">
                    Girilen Bilgilere Göre Fiyatlandırma Onaylanmış Şekilde Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formsubeyetkili" href="#" type="button" data-dismiss="modal" class="btn green">Onayla</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="subeyetkiliredconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Seçilen Sayaçlara Ait Fiyatlandırma Reddedilecektir</h4>
                </div>
                <div class="modal-body">
                    Girilen Bilgilere Göre Fiyatlandırma Reddedilmiş Şekilde Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formsubeyetkilireddet" href="#" type="button" data-dismiss="modal" class="btn green">Onayla</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="subeaktar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Aktarma Sayfası
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{ URL::to('ucretlendirme/subeaktar') }}" data-action="{{URL::to('ucretlendirme/subeaktar')}}" id="form_sample_19" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <h3 class="form-section">Şube Sayaç Bilgileri Aktarma</h3>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-xs-8 subeaktarcariadi" style="padding-top: 9px">{{ Input::old('subeaktarcariadi') }}</label>
                                        </div>
                                        <h4 class="form-section col-xs-12">Sayaç Listesi
                                            <span style="padding-left:100px"><button type="button" class="btn green tumunusec19">Tümünü Seç</button>
                                                <button type="button" class="btn red temizle19">Temizle</button></span>
                                        </h4>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_19">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Seri No</th>
                                                    <th>Sayac Adı</th>
                                                    <th>Garanti</th>
                                                    <th>Fiyat Durumu</th>
                                                    <th>&nbsp;Fiyatı&nbsp;</th>
                                                    <th>İndirim</th>
                                                    <th>Tutar</th>
                                                    <th>Kdv Tutarı</th>
                                                    <th>Toplam Tutar</th>
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
                                                <div class="col-xs-12 subeaktarkur">
                                                    <label class="col-xs-12 subeaktareuro" style="padding-top: 9px;margin-left:5px;text-align:center">Euro : 0.0000 ₺</label>
                                                    <label class="col-xs-12 subeaktardolar" style="padding-top: 9px;margin-left:3px;text-align:center">Dolar : 0.0000 ₺</label>
                                                    <label class="col-xs-12 subeaktarsterlin" style="padding-top: 9px;text-align:center">Sterlin : 0.0000 ₺</label>
                                                    <label class="control-label col-xs-12 subeaktarwarning" style="text-align:center"></label>
                                                    <input id="subeaktareuro" class="hide">
                                                    <input id="subeaktardolar" class="hide">
                                                    <input id="subeaktarsterlin" class="hide">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TUTAR:</label>
                                                    <label class="col-xs-6 subeaktartutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">İNDİRİM MİKTARI:</label>
                                                    <label class="col-xs-6 subeaktarindirimtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDVSIZ TUTAR:</label>
                                                    <label class="col-xs-6 subeaktarkdvsiztutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">KDV TUTARI:</label>
                                                    <label class="col-xs-6 subeaktarkdvtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <div class="col-xs-12">
                                                    <label class="control-label col-xs-6">TOPLAM TUTAR:</label>
                                                    <label class="col-xs-6 subeaktartoplamtutar" style="padding-top: 9px">0.00</label>
                                                </div>
                                                <input class="hide" id="subeaktartutar" name="subeaktartutar"/>
                                                <input class="hide" id="subeaktartutar2" name="subeaktartutar2"/>
                                                <input class="hide" id="subeaktarindirimtutar" name="subeaktarindirimtutar"/>
                                                <input class="hide" id="subeaktarindirimtutar2" name="subeaktarindirimtutar2"/>
                                                <input class="hide" id="subeaktarkdvsiztutar" name="subeaktarkdvsiztutar"/>
                                                <input class="hide" id="subeaktarkdvsiztutar2" name="subeaktarkdvsiztutar2"/>
                                                <input class="hide" id="subeaktarkdvtutar" name="subeaktarkdvtutar"/>
                                                <input class="hide" id="subeaktarkdvtutar2" name="subeaktarkdvtutar2"/>
                                                <input class="hide" id="subeaktartoplamtutar" name="subeaktartoplamtutar"/>
                                                <input class="hide" id="subeaktartoplamtutar2" name="subeaktartoplamtutar2"/>
                                                <input class="hide" id="subeaktarbirim" name="subeaktarbirim"/>
                                                <input class="hide" id="subeaktarbirim2" name="subeaktarbirim2"/>
                                                <input class="hide" id="subeaktarbirimi" name="subeaktarbirimi"/>
                                                <input class="hide" id="subeaktarbirimi2" name="subeaktarbirimi2"/>
                                                <input class="hide" id="subeaktarkurtarih" name="subeaktarkurtarih"/>
                                                <input class="hide" id="subeaktarsecilenler" name="subeaktarsecilenler"/>
                                                <input class="hide" id="subeaktaradet" name="subeaktaradet"/>
                                                <input class="hide" id="subeaktartumu" name="subeaktartumu"/>
                                                <input class="hide" id="subeaktargaranti" name="subeaktargaranti"/>
                                                <input class="hide" id="subeaktarid" name="subeaktarid"/>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green subeaktaronay" data-toggle="modal" data-target="#subeaktarconfirm">Aktar</button>
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
    <div class="modal fade" id="subeaktarconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Seçilen Sayaçlar Servis Bilgisine Aktarılacak</h4>
                </div>
                <div class="modal-body">
                    Seçilen Sayaçlar için Servis Bilgisi Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formsubeaktar" href="#" type="button" data-dismiss="modal" class="btn green">Aktar</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
@stop
