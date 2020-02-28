@extends('layout.master')

@section('page-title')
    <div class="page-title">
        <h1>Şube Depolararası <small> Transfer Ekranı</small></h1>
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
    <script src="{{ URL::to('assets/global/plugins/jquery-maskmoney/jquery.maskMoney.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/tinymce/tinymce.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>

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
                "url": "{{ URL::to('depo/depolararasilist') }}",
                "type": "POST",
                "data": {
                    "netsiscari_id" : "@if(isset(Auth::user()->netsiscari_id) && count(Auth::user()->netsiscari_id)>0){{Auth::user()->netsiscarilist}}@endif"
                }
            },
            "bServerSide": true,
            "fnDrawCallback" : function(  ) {
                var secilen=$('#secilen').val();
                $("#sample_editable_1  tr .id").each(function(){
                    if(secilen===$(this).html()){
                        $(this).parents('tr').addClass("active");
                    }
                });
            },
            "aaSorting": [[3,'desc']],
            "columnDefs": [ { targets: [ 1 ], orderData: [ 1, 0 ] },
                          { targets: [ 2 ], orderData: [ 2, 0 ] },
                          { targets: [ 3 ], orderData: [ 3, 0 ] },
                          { targets: [ 4 ], orderData: [ 4, 0 ] },
                          { targets: [ 5 ], orderData: [ 5, 0 ] },
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
                {data: 'id', name: 'depolararasi.id',"class":"id","orderable": true, "searchable": true},
                {data: 'cariadi', name: 'netsiscari.cariadi',"orderable": true, "searchable": false},
                {data: 'sayacsayisi', name: 'depolararasi.sayacsayisi',"orderable": true, "searchable": true},
                {data: 'gtipi', name: 'depolararasi.gtipi',"class":"tipi","orderable": true, "searchable": false},
                {data: 'gdepodurum', name: 'depolararasi.gdepodurum',"class":"durum","orderable": true, "searchable": false},
                {data: 'adi', name: 'netsisdepolar.adi',"orderable": true, "searchable": true},
                {data: 'adi_soyadi', name: 'kullanici.adi_soyadi',"orderable": true, "searchable": true},
                {data: 'teslimtarihi', name: 'depolararasi.teslimtarihi',"class":"tarih","orderable": true, "searchable": true},
                {data: 'gteslimtarihi', name: 'depolararasi.gteslimtarihi',"visible": false, "searchable": true},
                {data: 'ncariadi', name: 'netsiscari.ncariadi',"visible": false, "searchable": true},
                {data: 'ntipi', name: 'depolararasi.ntipi',"visible": false, "searchable": true},
                {data: 'ndepodurum', name: 'depolararasi.ndepodurum',"visible": false, "searchable": true},
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
            '<option value="9">Cari Adı</option>'+
            '<option value="2">Adet</option>'+
            '<option value="10">Tipi</option>'+
            '<option value="11">Durum</option>'+
            '<option value="5">Aktarılan Depo</option>'+
            '<option value="6">Teslim Eden</option>'+
            '<option value="8">Teslim Tarihi</option>'+
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
                $(this).toggleClass("active");
                var secilen = "";
                if ($(this).hasClass('active')) {
                    $("tbody tr").removeClass("active");
                    $(this).addClass("active");
                    secilen = oTable.cell($(this).children('.id')).data();
                    $('#secilen').val(secilen);
                    var durum = $(this).children('.durum').text();
                    var tipi = $(this).children('.tipi').text();
                    if(durum==='Bekliyor')
                    {
                        if(tipi==='Hurda'){
                            $('.aktar').removeClass("hide");
                            $('.depocikis').removeClass("hide");
                        }else{
                            $('.aktar').removeClass("hide");
                            $('.depocikis').addClass("hide");
                        }
                    }else{
                        $('.aktar').addClass("hide");
                        $('.depocikis').addClass("hide");
                    }
                }else{
                    $(this).removeClass("active");
                    $('#secilen').val("");
                    $('.aktar').addClass("hide");
                    $('.depocikis').addClass("hide");
                }
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
            "searching": true,
            "ordering": false,
            "bProcessing": false,
            "sAjaxSource": "",
            "bServerSide": false,
            "bFilter" : true,
            "bLengthChange": true,
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
            "aoColumns": [{"sClass":"id"},null,null,null,null],
            "lengthMenu": [
                [5, 10, 20, 99999999],
                [5, 10, 20, "Hepsi"]
            ],
            "searchDelay": 0
        });
        var tableWrapper = jQuery('#sample_editable_3_wrapper');
        table3.on('click', 'tr', function () {
            if(oTable3.cell( $(this).children('.id')).data()!==undefined){
                $(this).toggleClass("active");
                var adet=parseInt($('#teslimadet').val());
                var secilenler=$('#teslimsecilenler').val();
                var secilenlist;
                if($(this).hasClass('active'))
                {
                    secilenler+=(secilenler==="" ? "" : ",")+oTable3.cell( $(this).children('.id')).data();
                    adet++;
                    $('#teslimsecilenler').val(secilenler);
                    $('#teslimadet').val(adet);
                    $('.teslimadet').text(adet);
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
                    $('#teslimsecilenler').val(yenilist);
                    $('#teslimadet').val(adet);
                    $('.teslimadet').text(adet);
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
                    $('.teslimet').removeClass('hide');
                }else{
                    $('.teslimet').addClass('hide');
                }
                $('#teslimsecilenler').val(secilenler);
            }
        });
        $(document).on("click", ".temizle3", function () {
            var adet=parseInt($('#teslimadet').val());
            var secilenler=$('#teslimsecilenler').val();
            $("#sample_editable_3 tbody tr .id").each(function(){
                var secilen=$(this).html();
                $(this).parents('tr').removeClass("active");
                var secilenlist=secilenler.split(',');
                var yenilist="";
                $.each(secilenlist,function(index){
                    if(secilenlist[index]!==secilen){
                        yenilist+=(yenilist==="" ? "" : ",")+secilenlist[index];
                    }else{
                        adet--;
                    }
                });
                secilenler=yenilist;
            });
            $('#teslimsecilenler').val(secilenler);
            $('#teslimadet').val(adet);
            $('.teslimadet').text(adet);
            if (adet > 0) {
                $('.teslimet').removeClass('hide');
            } else {
                $('.teslimet').addClass('hide');
            }
        });
        $(document).on("click", ".tumunusec3", function () {
            var adet=parseInt($('#teslimadet').val());
            var secilenler=$('#teslimsecilenler').val();
            $("#sample_editable_3 tbody tr .id").each(function(){
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
            $('#teslimsecilenler').val(secilenler);
            $('#teslimadet').val(adet);
            $('.teslimadet').text(adet);
            if (adet > 0) {
                $('.teslimet').removeClass('hide');
            } else {
                $('.teslimet').addClass('hide');
            }
        });
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
                [5],
                [5]
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
        var tableWrapper = jQuery('#sample_editable_5_wrapper');
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
        table5.on('click', 'tr', function () {
            if(oTable5.cell( $(this).children('.adres')).data()!==undefined){
                $(this).toggleClass("active");
                var secilen= "";
                if($(this).hasClass("active"))
                    secilen=oTable5.cell( $(this).children('.adres')).data();
                var flag = 0;
                $('#secilenadres').val("");
                $("#sample_editable_5  tr .adres").each(function(){
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
        $('#teslimaktarilan').on('change', function () {
            var id = $(this).val();
            if(id!==""){
                var adres = $(this).find("option:selected").data('id');
                $(".teslimfaturaadres").html(adres);
            }else{
                $(".teslimfaturaadres").html('');
            }
        });
        $(document).on("click", ".detay", function () {
            $.blockUI();
            var id = $(this).data('id');
            $.getJSON("{{ URL::to('depo/depolararasibilgi') }}",{id:id},function(event){
                if(event.durum){
                    var depolararasi = event.depolararasi;
                    var sayacgelen = depolararasi.sayacgelen;
                    $(".teslimtarihi").html(depolararasi.teslimtarihi);
                    $(".cariadi").html(depolararasi.netsiscari.cariadi);
                    $(".carikod").html(depolararasi.carikod);
                    $(".depokod").html(depolararasi.depokodu);
                    $(".faturano").html(depolararasi.faturano);
                    $(".aktarilan").html(depolararasi.netsisdepo ? depolararasi.netsisdepo.adi : '');
                    $(".faturaadres").html(depolararasi.faturaadres);
                    $(".teslimatadres").html(depolararasi.teslimadres);
                    if(depolararasi.faturano==null)
                        $('.faturadetaykisim').addClass('hide');
                    else
                        $('.faturadetaykisim').removeClass('hide');
                    oTable2.clear().draw();
                    $.each(sayacgelen,function(index) {
                        var sayacadi = sayacgelen[index].sayacadi.sayacadi;
                        var serino = sayacgelen[index].serino;
                        var sayaccap = sayacgelen[index].sayaccap.capadi;
                        var uretimyeradi = sayacgelen[index].uretimyer.yeradi;
                        var durum = sayacgelen[index].durum;
                        oTable2.row.add([sayacgelen[index].id,uretimyeradi,serino,sayacadi+' '+sayaccap,durum])
                                .draw();
                    });
                }else{
                    $('#detay-goster').modal('hide');
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
        });
        $(document).on("click", ".aktar", function () {
            var id = $('#secilen').val();
            if(id!==""){
                $.blockUI();
                var action = $('#form_sample_3').data('action');
                $('#form_sample_3').prop('action',action+'/'+id);
                $.getJSON("{{ URL::to('depo/aktarmabilgi') }}",{id:id},function(event){
                    if(event.durum) {
                        var depolararasi = event.depolararasi;
                        var teslimadres = event.teslimadres;
                        var sayacgelen = depolararasi.sayacgelen;
                        $(".teslimcariadi").html(depolararasi.netsiscari.cariadi);
                        $(".teslimcarikod").html(depolararasi.netsiscari.carikod);
                        $(".teslimdepokod").html(depolararasi.yetkili.depokodu);
                        $(".teslimfaturaadres").html('');
                        $("#teslimadres").val('');
                        $('#teslimsecilenler').val(depolararasi.secilenler);
                        $('#teslimtumu').val(depolararasi.secilenler);
                        $('#teslimadet').val(depolararasi.sayacsayisi);
                        $('.teslimadet').text(depolararasi.sayacsayisi);
                        oTable3.clear().draw();
                        $.each(sayacgelen,function(index) {
                            var uretimyeri = sayacgelen[index].uretimyer.yeradi;
                            var sayacadi = sayacgelen[index].sayacadi.sayacadi;
                            var serino = sayacgelen[index].serino;
                            var sayaccap = sayacgelen[index].sayaccap.capadi;
                            var tarih = sayacgelen[index].tarih;
                            oTable3.row.add([sayacgelen[index].id,serino,sayacadi+' '+sayaccap,uretimyeri,tarih])
                                    .draw().nodes().to$().addClass( 'active' );
                        });
                        oTable5.clear().draw();
                        $.each(teslimadres, function (index) {
                            oTable5.row.add([teslimadres[index].teslimadres]).draw();
                        });
                        $('#secilenadres').val("");
                    }else{
                        $('#aktar').modal('hide');
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            }else{
                toastr["warning"]('Teslim Edilecek Seçilmedi', 'Teslimat Hatası');
            }
        });
        $(document).on("click", ".depocikis", function () {
            var id = $('#secilen').val();
            if(id!==""){
                $.blockUI();
                var action = $('#form_sample_4').data('action');
                $('#form_sample_4').prop('action',action+'/'+id);
                $.getJSON("{{ URL::to('depo/aktarmabilgi') }}",{id:id},function(event){
                    if(event.durum){
                        var depolararasi = event.depolararasi;
                        $(".cikiscariadi").html(depolararasi.netsiscari.cariadi);
                        $(".cikisdepokod").html(depolararasi.yetkili.depokodu);
                        $(".cikiscarikod").html(depolararasi.netsiscari.carikod);
                        $('#cikissecilenler').val(depolararasi.secilenler);
                        $('#cikisadet').val(depolararasi.sayacsayisi);
                        $('#cikisid').val(depolararasi.id);
                        oTable4.clear().draw();
                        var sayacgelen=depolararasi.sayacgelen;
                        $.each(sayacgelen, function (index) {
                            var sayacadi = sayacgelen[index].sayacadi.sayacadi;
                            var yeradi = sayacgelen[index].uretimyer.yeradi;
                            var serino = sayacgelen[index].serino;
                            var sayaccap = sayacgelen[index].sayaccap.capadi;
                            oTable4.row.add([sayacgelen[index].id, serino, sayacadi + ' ' + sayaccap,yeradi]).draw();
                        });
                    }else{
                        $('#depocikis').modal('hide');
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
                        toastr["warning"]('Aktarma Tamamlanmadan İrsaliye Çıkarılamaz!', 'İrsaliye Hatası');
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
                        toastr["warning"]('Aktarma Tamamlanmadan İrsaliye Eki Çıkarılamaz!', 'İrsaliye Hatası');
                    }
                }
            });
        });
        $('#formsubmit').click(function () {
            $('#form_sample_3').submit();
            $.blockUI();
        });
        $('#cikissubmit').click(function () {
            $('#form_sample_4').submit();
            $.blockUI();
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
                        <i class="fa fa-tag"></i>Depolararası Transfer Bilgisi
                    </div>
                    <div class="actions">
                        <a class="btn btn-default btn-sm irsaliyeek">
                            <i class="fa fa-list"></i> İrsaliye Sayaç Listesi </a>
                        <a class="btn btn-default btn-sm irsaliye">
                            <i class="fa fa-print"></i> Depolararası İrsaliyesi </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Cari Bilgisi</th>
                            <th>Adet</th>
                            <th>Tipi</th>
                            <th>Durum</th>
                            <th>Aktarılan Depo</th>
                            <th>Teslim Eden</th>
                            <th>Teslim Tarihi</th>
                            <th></th><th></th><th></th><th></th>
                            <th>Detay</th>
                        </tr>
                        </thead>
                    </table>
                    <form action="" id="form_sample" class="form-horizontal" novalidate="novalidate">
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-xs-offset-2 col-xs-10">
                                    <a class='btn green aktar hide' href='#aktar' data-toggle='modal' data-id=''>Aktar</a>
                                    <a class='btn yellow depocikis hide' href='#depocikis' data-toggle='modal' data-id=''>Çıkış Yap</a>
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
    <div class="modal fade" id="portlet-delete" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Depolararası Bekleyen İşlemi Silinecek</h4>
                </div>
                <div class="modal-body">
                    Seçilen Depolararası Kayıdını Silmek İstediğinizden Emin Misiniz?
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
                                    <i class="fa fa-pencil"></i>Depolararası Transfer Detayı
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Depolararası Transfer Detayı</h3>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Cari Adı:</label>
                                            <label class="col-md-8 col-xs-12 cariadi" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Depo Kodu:</label>
                                            <label class="col-md-3 col-xs-12 depokod" style="padding-top: 9px"></label>
                                            <label class="control-label col-md-2 col-xs-12">Teslim Tarihi:</label>
                                            <label class="col-md-3 col-xs-12 teslimtarihi" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_2">
                                                <thead>
                                                <tr>
                                                    <th class="">#</th>
                                                    <th>Üretim Yeri</th>
                                                    <th>Seri No</th>
                                                    <th>Sayaç Adı</th>
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
                                                <label class="control-label col-md-2 col-xs-12">Aktarılan Depo:</label>
                                                <label class="col-md-3 col-xs-12 aktarilan" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-2 col-xs-12">Fatura Adresi:</label>
                                                <label class="col-md-8 col-xs-12 faturaadres" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-2 col-xs-12">Teslimat Adresi:</label>
                                                <label class="col-md-8 col-xs-12 teslimatadres" style="padding-top: 9px"></label>
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
    <div class="modal fade" id="aktar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-truck"></i>Depolararası Transfer Sayfası
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{ URL::to('depo/aktar') }}" data-action="{{URL::to('depo/aktar')}}" id="form_sample_3" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h4 class="form-section">Gönderen Bilgisi</h4>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Netsis Cari Adı:</label>
                                            <label class="col-md-8 col-xs-12 teslimcariadi" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Depo Kodu:</label>
                                            <label class="col-md-3 col-xs-12 teslimdepokod" style="padding-top: 9px"></label>
                                            <label class="control-label col-md-2 col-xs-12">Cari Kod:</label>
                                            <label class="col-md-3 col-xs-12 teslimcarikod" style="padding-top: 9px"></label>
                                        </div>
                                        <h4 class="form-section col-xs-12">Sayaç Listesi
                                            <span style="padding-left:100px"><button type="button" class="btn green tumunusec3">Tümünü Seç</button>
                                                <button type="button" class="btn red temizle3">Temizle</button></span>
                                        </h4>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_3">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Seri No</th>
                                                    <th>Sayaç Adı</th>
                                                    <th>Üretim Yeri</th>
                                                    <th>Kayıt Tarihi</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-6 col-xs-12">
                                                <label class="control-label col-md-6 col-xs-12">Seçilen Sayaç Sayısı:</label>
                                                <label class="col-md-6 col-xs-12 teslimadet" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="col-md-6 col-xs-12">
                                                <input class="hide" id="teslimsecilenler" name="teslimsecilenler"/>
                                                <input class="hide" id="teslimadet" name="teslimadet"/>
                                                <input class="hide" id="teslimtumu" name="teslimtumu"/>
                                            </div>
                                        </div>
                                        <h4 class="form-section">İrsaliye Bilgisi  <span style="font-size: 12px">Teslimat sonrası Netsis tarafında oluşacak faturayı temsil edecek</span>
                                            <label><input type="checkbox" id="faturavar" name="faturavar" checked/> Fatura Çıkacak mı? </label></h4>
                                        <div class="form-group faturakismi">
                                            <div class="form-group">
                                                <label class="control-label col-md-2 col-xs-12">Fatura No:<span class="required" aria-required="true"> * </span></label>
                                                <div class="input-icon right col-md-3 col-xs-12">
                                                    <i class="fa"></i><input type="text" id="teslimfaturano" name="teslimfaturano" data-required="1" class="form-control" maxlength="15">
                                                </div>
                                                <label class="control-label col-md-2 col-xs-12">Aktarılacak Depo:</label>
                                                <div class="col-md-3">
                                                    <select class="form-control select2me select2-offscreen" id="teslimaktarilan" name="teslimaktarilan" tabindex="-1" title="">
                                                        <option data-id="" value="">Seçiniz...</option>
                                                        @foreach($netsisdepolar as $netsisdepo)
                                                            <option data-id="{{$netsisdepo->netsiscari->adres.' '.$netsisdepo->netsiscari->ilce.' '.$netsisdepo->netsiscari->il}}" value="{{$netsisdepo->kodu}}">{{$netsisdepo->adi}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-2 col-xs-12">Fatura Adresi:</label>
                                                <label class="col-md-8 col-xs-12 teslimfaturaadres" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-2 col-xs-4">Teslimat Adresi:</label>
                                                <div class="col-md-8 col-xs-6">
                                                    <input type="text" id="teslimadres" name="teslimadres" data-required="1" class="form-control" maxlength="100"
                                                           placeholder="Fatura Adresinden Farklı Olduğu Durumlarda Girilecektir">
                                                </div>
                                                <div class="col-xs-2" style="text-align: center">
                                                    <button type="button" class="btn green adressec" data-toggle="modal" data-target="#adressec">Seç</button>
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
    <div class="modal fade" id="depocikis" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Sayaçlar Depodan Faturasız Çıkış Yapılacak
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{URL::to('depo/subecikisyap')}}" data-action="{{URL::to('depo/subecikisyap')}}" id="form_sample_4" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Aşağıdaki Sayaçların Depodan Çıkışı Yapılacaktır</h3>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-xs-8 cikiscariadi" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Depo Kodu:</label>
                                            <label class="col-md-3 col-xs-12 cikisdepokod" style="padding-top: 9px"></label>
                                            <label class="control-label col-md-2 col-xs-12">Cari Kod:</label>
                                            <label class="col-md-3 col-xs-12 cikiscarikod" style="padding-top: 9px"></label>
                                        </div>
                                        <input class="hide" id="cikissecilenler" name="cikissecilenler"/>
                                        <input class="hide" id="cikisadet" name="cikisadet"/>
                                        <input class="hide" id="cikisid" name="cikisid"/>
                                        <h4 class="form-section col-xs-12">Sayaç Listesi</h4>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_4">
                                                <thead>
                                                <tr>
                                                    <th class="">#</th>
                                                    <th>Seri No</th>
                                                    <th>Sayaç Adı</th>
                                                    <th>Üretim Yeri</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green cikisyap" data-toggle="modal" data-target="#cikisconfirm">Çıkış Yap</button>
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
                                    <i class="fa fa-pencil"></i>Teslimat Adresi Listesi
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_5" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Önceki Teslimat Adresleri</h3>
                                        <input class="hide" id="secilenadres" name="secilenadres"/>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_5">
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
                    <h4 class="modal-title">Sayaçlar Depolararası Aktarılacak</h4>
                </div>
                <div class="modal-body">
                    Seçilen Sayaçlar Depolararası Aktarılacaktır?
                </div>
                <div class="modal-footer">
                    <a id="formsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Onayla</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="cikisconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Sayaçların Depo Çıkışı Yapılacak</h4>
                </div>
                <div class="modal-body">
                    Seçilen Sayaçların Depo Çıkış Yapılacak?
                </div>
                <div class="modal-footer">
                    <a id="cikissubmit" href="#" type="button" data-dismiss="modal" class="btn green">Onayla</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
@stop
