@extends('layout.master')

@section('page-title')
    <!--suppress JSValidateTypes -->
    <div class="page-title">
        <h1>Depo <small> Teslimat Bilgi Ekranı</small></h1>
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
    <script src="{{ URL::to('pages/depo/form-validation-1.js') }}"></script>
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
                "url": "{{ URL::to('depo/depoteslimlist') }}",
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
                    $(".modal-footer #sayacid").attr('href',"{{ URL::to('depo/depoteslimsil') }}/"+id );
                });
                var secilen=$('#secilen').val();
                $("#sample_editable_1  tr .id").each(function(){
                    if(secilen===$(this).html()){
                        $(this).parents('tr').addClass("active");
                    }
                });
            },
            "aaSorting": [[5,'asc'],[0,'desc']],
            "columnDefs": [ { targets: [ 1 ], orderData: [ 1, 5, 4, 0 ] },
                          { targets: [ 2 ], orderData: [ 2, 5, 4, 0 ] },
                          { targets: [ 4 ], orderData: [ 4, 5, 0 ] },
                          { targets: [ 5 ], orderData: [ 5 ] },
                          { targets: [ 7 ], orderData: [ 7, 5, 4, 0 ] }
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
                {data: 'id', name: 'depoteslim.id',"class":"id","orderable": true, "searchable": true},
                {data: 'cariadi', name: 'netsiscari.cariadi',"orderable": true, "searchable": false},
                {data: 'servisadi', name: 'servis.servisadi',"orderable": true, "searchable": false},
                {data: 'sayacsayisi', name: 'depoteslim.sayacsayisi',"orderable": true, "searchable": true},
                {data: 'gtipi', name: 'depoteslim.gtipi',"class":"tipi","orderable": true, "searchable": false},
                {data: 'gdepodurum', name: 'depoteslim.gdepodurum',"class":"durum","orderable": true, "searchable": false},
                {data: 'adi_soyadi', name: 'kullanici.adi_soyadi',"orderable": true, "searchable": false},
                {data: 'teslimtarihi', name: 'depoteslim.teslimtarihi',"orderable": true, "searchable": false},
                {data: 'gteslimtarihi', name: 'depoteslim.gteslimtarihi',"class":"hide","visible": false, "searchable": true},
                {data: 'ncariadi', name: 'netsiscari.ncariadi',"visible": false, "searchable": true},
                {data: 'nservisadi', name: 'servis.nservisadi',"visible": false, "searchable": true},
                {data: 'ntipi', name: 'depoteslim.ntipi',"visible": false, "searchable": true},
                {data: 'ndepodurum', name: 'depoteslim.ndepodurum',"visible": false, "searchable": true},
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
            '<option value="9">Cari Adı</option>'+
            '<option value="10">Servis</option>'+
            '<option value="3">Adet</option>'+
            '<option value="11">Tipi</option>'+
            '<option value="12">Durum</option>'+
            '<option value="13">Teslim Eden</option>'+
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
                    @if(Auth::user()->grup_id<16 && Auth::user()->grup_id!=6)
                    var durum = $(this).children('.durum').text();
                    var tipi = $(this).children('.tipi').text();
                    if(durum==='Bekliyor')
                    {
                        if(tipi==='Depolararası'){
                            $('.depolararasi').removeClass("hide");
                            $('.gonder').addClass("hide");
                            $('.depocikis').addClass("hide");
                        }else if(tipi==='Hurda'){
                            $('.gonder').removeClass("hide");
                            $('.depocikis').removeClass("hide");
                            $('.depolararasi').addClass("hide");
                        }else{
                            $('.gonder').removeClass("hide");
                            $('.depocikis').addClass("hide");
                            $('.depolararasi').addClass("hide");
                        }
                    }else{
                        $('.gonder').addClass("hide");
                        $('.depocikis').addClass("hide");
                        $('.depolararasi').addClass("hide");
                    }
                    @else
                        $('.gonder').addClass("hide");
                        $('.depocikis').addClass("hide");
                        $('.depolararasi').addClass("hide");
                    @endif
                } else {
                    $(this).removeClass("active");
                    $('#secilen').val("");
                    $('.gonder').addClass("hide");
                    $('.depocikis').addClass("hide");
                    $('.depolararasi').addClass("hide");
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
            "fnDrawCallback" : function() {
                $(document).on("click", ".kayitsil", function () {
                    var id = $(this).data('id');
                    $(".modal-footer #kayitid").attr('href',"{{ URL::to('depo/depoteslimkayitsil') }}/"+id );
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
            "aoColumns": [{"sClass":"id"},null,null,null,{"sClass":"teslimgaranti"},{"sClass":"teslimindirimorani"},
                {"sClass":"teslimkdvsiztutar"},{"sClass":"teslimkdvtutar"},{"sClass":"teslimtoplamtutar"},null],
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
                var geneltoplamtutar = 0;
                var geneltoplamtutar2 = 0;
                var parabirimi = $('#teslimbirimi').val();
                var parabirimi2 =$('#teslimbirimi2').val();
                var secilenlist;
                var birim,birim2,fiyat,fiyat2;
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
                    var fiyatlar = (data[8]).split('+');
                    if((fiyatlar.length)>1){ //2 birim varsa
                        birim = fiyatlar[0].split(' ')[1];
                        birim2 = fiyatlar[1].split(' ')[2];
                        fiyat = parseFloat(fiyatlar[0].split(' ')[0]);
                        fiyat2 = parseFloat(fiyatlar[1].split(' ')[1]);
                    }else{
                        birim = fiyatlar[0].split(' ')[1];
                        birim2 = "";
                        fiyat = parseFloat(fiyatlar[0].split(' ')[0]);
                        fiyat2 = 0;
                    }
                    if(durum===1){
                        geneltoplamtutar += fiyat;
                        geneltoplamtutar2 += fiyat2;
                    }
                });
                if(geneltoplamtutar2===0){
                    $('.teslimtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi);
                }else{
                    $('.teslimtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2);
                }
                $('#teslimtutar').val(geneltoplamtutar.toFixed(2));
                $('#teslimtutar2').val(geneltoplamtutar2.toFixed(2));
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
            var geneltoplamtutar = 0;
            var geneltoplamtutar2 = 0;
            var parabirimi = $('#teslimbirimi').val();
            var parabirimi2 = $('#teslimbirimi2').val();
            var secilenlist = secilenler.split(',');
            var birim, birim2, fiyat, fiyat2;
            oTable3.rows().every(function () {
                var data = this.data();
                var id = data[0];
                var durum = 0;
                $.each(secilenlist, function (index) {
                    if (id === secilenlist[index])
                        durum = 1;
                });
                var fiyatlar = (data[8]).split('+');
                if ((fiyatlar.length) > 1) { //2 birim varsa
                    birim = fiyatlar[0].split(' ')[1];
                    birim2 = fiyatlar[1].split(' ')[2];
                    fiyat = parseFloat(fiyatlar[0].split(' ')[0]);
                    fiyat2 = parseFloat(fiyatlar[1].split(' ')[1]);
                } else {
                    birim = fiyatlar[0].split(' ')[1];
                    birim2 = "";
                    fiyat = parseFloat(fiyatlar[0].split(' ')[0]);
                    fiyat2 = 0;
                }
                if (durum === 1) {
                    geneltoplamtutar += fiyat;
                    geneltoplamtutar2 += fiyat2;
                }
            });
            if (geneltoplamtutar2 === 0) {
                $('.teslimtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi);
            } else {
                $('.teslimtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi + ' + ' + geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
            }
            $('#teslimtutar').val(geneltoplamtutar.toFixed(2));
            $('#teslimtutar2').val(geneltoplamtutar2.toFixed(2));
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
            var geneltoplamtutar = 0;
            var geneltoplamtutar2 = 0;
            var parabirimi = $('#teslimbirimi').val();
            var parabirimi2 = $('#teslimbirimi2').val();
            var secilenlist = secilenler.split(',');
            var birim, birim2, fiyat, fiyat2;
            oTable3.rows().every(function () {
                var data = this.data();
                var id = data[0];
                var durum = 0;
                $.each(secilenlist, function (index) {
                    if (id === secilenlist[index])
                        durum = 1;
                });
                var fiyatlar = (data[8]).split('+');
                if ((fiyatlar.length) > 1) { //2 birim varsa
                    birim = fiyatlar[0].split(' ')[1];
                    birim2 = fiyatlar[1].split(' ')[2];
                    fiyat = parseFloat(fiyatlar[0].split(' ')[0]);
                    fiyat2 = parseFloat(fiyatlar[1].split(' ')[1]);
                } else {
                    birim = fiyatlar[0].split(' ')[1];
                    birim2 = "";
                    fiyat = parseFloat(fiyatlar[0].split(' ')[0]);
                    fiyat2 = 0;
                }
                if (durum === 1) {
                    geneltoplamtutar += fiyat;
                    geneltoplamtutar2 += fiyat2;
                }
            });
            if (geneltoplamtutar2 === 0) {
                $('.teslimtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi);
            } else {
                $('.teslimtutar').text(geneltoplamtutar.toFixed(2) + ' ' + parabirimi + ' + ' + geneltoplamtutar2.toFixed(2) + ' ' + parabirimi2);
            }
            $('#teslimtutar').val(geneltoplamtutar.toFixed(2));
            $('#teslimtutar2').val(geneltoplamtutar2.toFixed(2));
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
            "searching": true,
            "ordering": false,
            "bProcessing": false,
            "sAjaxSource": "",
            "bServerSide": false,
            "bInfo": true,
            "bPaginate": true,
            "bLengthChange": true,
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
            "lengthMenu": [
                [5, 10, 20, 99999999],
                [5, 10, 20, "Hepsi"]
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
            "bFilter" : false,
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
            ]
        });
        var tableWrapper = jQuery('#sample_editable_6_wrapper');
        table6.on('click', 'tr', function () {
            if(oTable6.cell( $(this).children('.id')).data()!==undefined){
                $(this).toggleClass("active");
                var adet=parseInt($('#depolararasiadet').val());
                var secilenler=$('#depolararasisecilenler').val();
                if($(this).hasClass('active'))
                {
                    secilenler+=(secilenler==="" ? "" : ",")+oTable6.cell( $(this).children('.id')).data();
                    adet++;
                    $('#depolararasisecilenler').val(secilenler);
                    $('#depolararasiadet').val(adet);
                    $('.depolararasiadet').text(adet);
                }else{
                    var secilen=oTable6.cell( $(this).children('.id')).data();
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
                    $('#depolararasisecilenler').val(yenilist);
                    $('#depolararasiadet').val(adet);
                    $('.depolararasiadet').text(adet);
                }
                if(adet>0){
                    $('.depolararasigonder').removeClass('hide');
                }else{
                    $('.depolararasigonder').addClass('hide');
                }
                $('#depolararasisecilenler').val(secilenler);
            }
        });
        $(document).on("click", ".temizle6", function () {
            var adet=parseInt($('#depolararasiadet').val());
            var secilenler=$('#depolararasisecilenler').val();
            $("#sample_editable_6 tbody tr .id").each(function(){
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
            $('#depolararasiadet').val(adet);
            $('#depolararasisecilenler').val(secilenler);
            $('.depolararasiadet').text(adet);
            if(adet>0){
                $('.depolararasigonder').removeClass('hide');
            }else{
                $('.depolararasigonder').addClass('hide');
            }
        });
        $(document).on("click", ".tumunusec6", function () {
            var adet=parseInt($('#depolararasiadet').val());
            var secilenler=$('#depolararasisecilenler').val();
            $("#sample_editable_6 tbody tr .id").each(function(){
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
            $('#depolararasiadet').val(adet);
            $('#depolararasisecilenler').val(secilenler);
            $('.depolararasiadet').text(adet);
            if(adet>0){
                $('.depolararasigonder').removeClass('hide');
            }else{
                $('.depolararasigonder').addClass('hide');
            }
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        var table7 = $('#sample_editable_7');
        var oTable7 = table7.DataTable({
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
        var tableWrapper = jQuery('#sample_editable_7_wrapper');
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
        table7.on('click', 'tr', function () {
            if(oTable7.cell( $(this).children('.adres')).data()!==undefined){
                $(this).toggleClass("active");
                var secilen= "";
                if($(this).hasClass("active"))
                    secilen=oTable7.cell( $(this).children('.adres')).data();
                var flag = 0;
                $('#secilenadres').val("");
                $("#sample_editable_7  tr .adres").each(function(){
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
        var table8 = $('#sample_editable_8');
        var oTable8 = table8.DataTable({
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
        var tableWrapper = jQuery('#sample_editable_8_wrapper');
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
        table8.on('click', 'tr', function () {
            if(oTable8.cell( $(this).children('.adres')).data()!==undefined){
                $(this).toggleClass("active");
                var secilen = "";
                if($(this).hasClass("active"))
                    secilen=oTable8.cell( $(this).children('.adres')).data();
                var flag = 0;
                $('#depolararasisecilenadres').val("");
                $("#sample_editable_8  tr .adres").each(function(){
                    if(secilen===$(this).html()){
                        $('#depolararasisecilenadres').val(secilen);
                        flag = 1;
                    }else{
                        $(this).parents('tr').removeClass("active");
                    }
                });
                if(!flag){
                    $('#depolararasisecilenadres').val("");
                }
            }
        });
    </script>
    <script>
        $(document).on("click", ".detay", function () {
            $.blockUI();
            var id = $(this).data('id');
            $.getJSON("{{ URL::to('depo/teslimatbilgi') }}",{id:id},function(event){
                if(event.durum){
                    var depoteslim = event.depoteslim;
                    var sayacgelen = depoteslim.sayacgelen;
                    $(".teslimtarihi").html(depoteslim.teslimtarihi);
                    $(".cariadi").html(depoteslim.netsiscari.cariadi);
                    $(".faturano").html(depoteslim.faturano);
                    $(".carikod").html(depoteslim.carikod);
                    $(".ozelkod").html(depoteslim.ozelkod);
                    $(".plasiyerkod").html(depoteslim.plasiyerkod);
                    $(".depokod").html(depoteslim.depokodu);
                    $(".aciklama").html(depoteslim.aciklama);
                    $(".faturaadres").html(depoteslim.faturaadres);
                    $(".teslimatadres").html(depoteslim.teslimadres);
                    $(".aciklama1").html(depoteslim.belge1);
                    $(".aciklama2").html(depoteslim.belge2);
                    $(".aciklama3").html(depoteslim.belge3);
                    $(".faturasizaciklama").html(depoteslim.belge1);
                    if(depoteslim.faturano==null) {
                        $('.faturadetaykisim').addClass('hide');
                        $('.faturasizdetaykisim').removeClass('hide');
                    }else{
                        $('.faturadetaykisim').removeClass('hide');
                        $('.faturasizdetaykisim').addClass('hide');
                    }
                    oTable2.clear().draw();
                    $.each(sayacgelen,function(index) {
                        var sayacadi = sayacgelen[index].sayacadi.sayacadi;
                        var serino = sayacgelen[index].serino;
                        var sayaccap = sayacgelen[index].sayaccap.capadi;
                        var uretimyeradi = sayacgelen[index].uretimyer.yeradi;
                        var durum = sayacgelen[index].durum;
                        if(depoteslim.depodurum==="0"){
                            oTable2.row.add([sayacgelen[index].id,uretimyeradi,serino,sayacadi+' '+sayaccap,durum,
                                '<a href="#kayit-sil" data-toggle="modal" data-id="'+sayacgelen[index].id+'" class="btn btn-sm btn-danger kayitsil '+durum+'">Sil</a>'])
                                .draw();
                        }else{
                            oTable2.row.add([sayacgelen[index].id,uretimyeradi,serino,sayacadi+' '+sayaccap,durum,''])
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
        $(document).on("click", ".gonder", function () {
            var id = $('#secilen').val();
            if(id!==""){
                $.blockUI();
                var action = $('#form_sample_3').data('action');
                $('#form_sample_3').prop('action',action+'/'+id);
                $.getJSON("{{ URL::to('depo/teslimatucretlibilgi') }}",{id:id},function(event){
                    if(event.durum) {
                        var depoteslim = event.depoteslim;
                        var teslimadres = event.teslimadres;
                        var arizafiyat = depoteslim.arizafiyat;
                        var geneltoplamtutar = 0;
                        var geneltoplamtutar2 = 0;
                        var parabirimi = depoteslim.parabirimi;
                        var parabirimi2 = depoteslim.parabirimi2;
                        $(".teslimcariadi").html(depoteslim.netsiscari.cariadi);
                        $(".teslimfaturano").html('Teslim Edildiğinde Oluşacaktır');
                        $(".teslimcarikod").html(depoteslim.netsiscari.carikod);
                        $(".teslimozelkod").html(depoteslim.yetkili.ozelkod);
                        $(".teslimplasiyerkod").html(depoteslim.yetkili.plasiyerkod);
                        $(".teslimdepokod").html(depoteslim.yetkili.depokodu);
                        $(".teslimaciklama").html(depoteslim.servis.servisadi);
                        $(".teslimfaturaadres").html(depoteslim.netsiscari.adres+' '+depoteslim.netsiscari.il+' '+depoteslim.netsiscari.ilce);
                        $("#teslimadres").val('');
                        $('#teslimsecilenler').val(depoteslim.secilenler);
                        $('#teslimtumu').val(depoteslim.secilenler);
                        $('#teslimadet').val(depoteslim.sayacsayisi);
                        $('#teslimbirim').val(parabirimi.id);
                        $('#teslimbirim2').val(parabirimi2==null ? "" : parabirimi2.id);
                        $('#teslimbirimi').val(parabirimi.birimi);
                        $('#teslimbirimi2').val(parabirimi2==null ? "" : parabirimi2.birimi);
                        $('.teslimadet').text(depoteslim.sayacsayisi);
                        $("#teslimaciklama1").select2('val',depoteslim.tipi);
                        $('.faturasizkismi').addClass('hide');
                        //$("#teslimaciklama2").val('SAYAÇ LİSTESİ EKTEDİR.');
                        $("#yetkilimail").val(depoteslim.netsiscari.depoyetkili!=null ? depoteslim.netsiscari.depoyetkili : '');
                        oTable3.clear().draw();
                        if(arizafiyat.length>0){
                            $.each(arizafiyat,function(index) {
                                var garanti = arizafiyat[index].ariza_garanti==="0" ? 'Dışında' : 'İçinde';
                                var sayacadi = arizafiyat[index].sayacadi.sayacadi;
                                var yeradi = arizafiyat[index].uretimyer.yeradi;
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
                                    if(birim2!=null){
                                        if(birim2.id===parabirimi2.id){
                                            toplamtutar2=Math.round(toplamtutar2*2)/2;
                                        }else{
                                            toplamtutar2=0;
                                            $('.teslimet').prop('disabled', true);
                                        }
                                    }else{
                                        toplamtutar2=0;
                                        birim2=[];
                                        birim2.birimi="";
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
                                oTable3.row.add([arizafiyat[index].sayacgelen_id,serino,sayacadi,yeradi,garanti,indirimorani.toFixed(2)+'%',kdvsiztutar.toFixed(2)+' '+birim.birimi+' + '+kdvsiztutar2.toFixed(2)+' '+birim2.birimi,kdv.toFixed(2)+' '+birim.birimi+' + '+kdv2.toFixed(2)+' '+birim2.birimi,toplamtutar.toFixed(2)+' '+birim.birimi+' + '+toplamtutar2.toFixed(2)+' '+birim2.birimi,
                                        '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>'])
                                            .draw().nodes().to$().addClass( 'active' );
                                }else{
                                    oTable3.row.add([arizafiyat[index].sayacgelen_id,serino,sayacadi,yeradi,garanti,indirimorani.toFixed(2)+'%',kdvsiztutar.toFixed(2)+' '+birim.birimi,kdv.toFixed(2)+' '+birim.birimi,toplamtutar.toFixed(2)+' '+birim.birimi,
                                        '<a class="btn btn-sm btn-warning fiyatdetay" href="#fiyat-detay" data-toggle="modal" data-id="' + arizafiyat[index].id + '"> Detay </a>'])
                                            .draw().nodes().to$().addClass( 'active' );
                                }
                                geneltoplamtutar+=toplamtutar;
                                geneltoplamtutar2+=toplamtutar2;
                            });
                        }else{
                            var sayacgelen=depoteslim.sayacgelen;
                            $.each(sayacgelen, function (index) {
                                var garanti = 'Periyodik';
                                var sayacadi = sayacgelen[index].sayacadi.sayacadi;
                                var yeradi = sayacgelen[index].uretimyer.yeradi;
                                var serino = sayacgelen[index].serino;
                                var indirimorani = 0;
                                var kdvsiztutar = 0.00;
                                var kdv = 0.00;
                                var toplamtutar = 0.00;
                                oTable3.row.add([sayacgelen[index].id, serino, sayacadi,yeradi,garanti,indirimorani.toFixed(2)+'%', kdvsiztutar.toFixed(2)+' '+parabirimi.birimi,
                                    kdv.toFixed(2)+' '+parabirimi.birimi, toplamtutar.toFixed(2)+' '+parabirimi.birimi,'']).draw().nodes().to$().addClass( 'active' );
                            });
                        }
                        if(geneltoplamtutar2===0){
                            $('.teslimtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi);
                        }else{
                            $('.teslimtutar').text(geneltoplamtutar.toFixed(2)+' '+parabirimi.birimi+' + '+geneltoplamtutar2.toFixed(2)+' '+parabirimi2.birimi);
                        }
                        $('#teslimtutar').val(geneltoplamtutar.toFixed(2));
                        $('#teslimtutar2').val(geneltoplamtutar2.toFixed(2));
                        oTable7.clear().draw();
                        $.each(teslimadres, function (index) {
                            oTable7.row.add([teslimadres[index].teslimadres]).draw();
                        });
                        $('#secilenadres').val("");
                    }else{
                        $('#gonder').modal('hide');
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
                var action = $('#form_sample_5').data('action');
                $('#form_sample_5').prop('action',action+'/'+id);
                $.getJSON("{{ URL::to('depo/teslimatbilgi') }}",{id:id},function(event){
                    if(event.durum){
                        var depoteslim = event.depoteslim;
                        $(".cikiscariadi").html(depoteslim.netsiscari.cariadi);
                        $('#cikissecilenler').val(depoteslim.secilenler);
                        $('#cikisadet').val(depoteslim.sayacsayisi);
                        $('#cikisid').val(depoteslim.id);
                        oTable5.clear().draw();
                        var sayacgelen=depoteslim.sayacgelen;
                        $.each(sayacgelen, function (index) {
                            var sayacadi = sayacgelen[index].sayacadi.sayacadi;
                            var yeradi = sayacgelen[index].uretimyer.yeradi;
                            var serino = sayacgelen[index].serino;
                            oTable5.row.add([sayacgelen[index].id, serino, sayacadi,yeradi]).draw();
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
        $(document).on("click", ".depolararasi", function () {
            var id = $('#secilen').val();
            if(id!==""){
                $.blockUI();
                var action = $('#form_sample_6').data('action');
                $('#form_sample_6').prop('action',action+'/'+id);
                $.getJSON("{{ URL::to('depo/teslimatbilgi') }}",{id:id},function(event){
                    if(event.durum){
                        var depoteslim = event.depoteslim;
                        var teslimadres = event.teslimadres;
                        $(".depolararasicariadi").html(depoteslim.netsiscari.cariadi);
                        $('#depolararasisecilenler').val(depoteslim.secilenler);
                        $('#depolararasitumu').val(depoteslim.secilenler);
                        $('#depolararasiadet').val(depoteslim.sayacsayisi);
                        $('.depolararasiadet').text(depoteslim.sayacsayisi);
                        $('#depolararasiid').val(depoteslim.id);
                        $(".depolararasifaturano").html('Teslim Edildiğinde Oluşacaktır');
                        $(".depolararasifaturaadres").html(depoteslim.netsiscari.adres+' '+depoteslim.netsiscari.il+' '+depoteslim.netsiscari.ilce);
                        $("#depolararasiadres").val('');
                        var sayacdurum;
                        if(depoteslim.tipi==="3"){
                            $("#depolararasiaciklama1").select2('val',1);
                            sayacdurum="Hurda";
                        }else if(depoteslim.tipi==="2"){
                            $("#depolararasiaciklama1").select2('val',2);
                            sayacdurum="Geri Gönderim";
                        }else{
                            $("#depolararasiaciklama1").select2('val',0);
                            sayacdurum="Teslimat";
                        }
                        $("#depolararasiaciklama2").val('SAYAÇ LİSTESİ EKTEDİR.');
                        oTable6.clear().draw();
                        var sayacgelen=depoteslim.sayacgelen;
                        $.each(sayacgelen, function (index) {
                            var sayacadi = sayacgelen[index].sayacadi.sayacadi;
                            var yeradi = sayacgelen[index].uretimyer.yeradi;
                            var serino = sayacgelen[index].serino;
                            oTable6.row.add([sayacgelen[index].id, serino, sayacadi,yeradi,sayacdurum]).draw().nodes().to$().addClass( 'active' );
                        });
                        oTable8.clear().draw();
                        $.each(teslimadres, function (index) {
                            oTable8.row.add([teslimadres[index].teslimadres]).draw();
                        });
                        $('#secilenadres').val("");
                    }else{
                        $('#depolararasi').modal('hide');
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            }else{
                toastr["warning"]('Teslim Edilecek Seçilmedi', 'Teslimat Hatası');
            }
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
                    oTable4.clear().draw();
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
                            oTable4.columns([4]).visible(true, false);
                            oTable4.columns([2, 3, 5, 6]).visible(false, false);
                        }else {
                            oTable4.columns([2, 6]).visible(true, false);
                            oTable4.columns([3, 4, 5]).visible(false, false);
                        }
                        $.each(degisenler,function(index) {
                            if (ucretsizler[index] === "1") {
                                oTable4.row.add([degisenler[index].id, degisenler[index].tanim, '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,
                                    '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,'Evet']).draw();
                            }else{
                                oTable4.row.add([degisenler[index].id, degisenler[index].tanim, genelfiyat[index] + ' ' + genelbirimler[index].birimi,ozelfiyat[index] + ' ' + ozelbirimler[index].birimi,
                                    '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,'Hayır']).draw();
                            }
                        });
                    }else {
                        if(ucretlendirme.ariza_garanti==="1") {
                            oTable4.columns([ 5 ]).visible(true, false);
                            oTable4.columns([2, 3, 4, 6]).visible(false, false);
                        }else{
                            oTable4.columns( [ 3,6 ] ).visible(true, false);
                            oTable4.columns( [ 2,4,5 ] ).visible( false,false );
                        }
                        $.each(degisenler, function (index) {
                            if (ucretsizler[index] === "1") {
                                oTable4.row.add([degisenler[index].id, degisenler[index].tanim, '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,
                                    '0.00 ' + genelbirimi, '0.00 ' + ozelbirimi,'Evet']).draw();
                            }else{
                                oTable4.row.add([degisenler[index].id, degisenler[index].tanim, genelfiyat[index] + ' ' + genelbirimler[index].birimi,ozelfiyat[index] + ' ' + ozelbirimler[index].birimi,
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
        $(document).on("click", ".teslimadresisec", function (){
            var secilen = $('#secilenadres').val();
            $('#teslimadres').val(secilen);
        });
        $(document).on("click", ".depolararasiteslimadresisec", function (){
            var secilen = $('#depolararasisecilenadres').val();
            $('#depolararasiadres').val(secilen);
        });
        $(document).ready(function() {
            $('#kriter').select2();
            $('#faturavar').on('change', function () {
                if ($('#faturavar').attr('checked')) {
                    $(".faturakismi").removeClass('hide');
                    $(".faturasizkismi").addClass('hide');
                } else {
                    $(".faturakismi").addClass('hide');
                    $(".faturasizkismi").removeClass('hide');
                }
            });
            $('#mailvar').on('change', function () {
                if ($('#mailvar').attr('checked')) {
                    $(".mailvar").removeClass('hide');
                } else {
                    $(".mailvar").addClass('hide');
                }
            });
            $('#depolararasifaturavar').on('change', function () {
                if ($('#depolararasifaturavar').attr('checked')) {
                    $(".depolararasifaturakismi").removeClass('hide');
                } else {
                    $(".depolararasifaturakismi").addClass('hide');
                }
            });
            $('.tutanak').click(function () {
                var teslim = $('#sample_editable_1 .active .id').text();
                if (teslim !== null) {
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
                    $.redirectPost(redirect, {teslim: teslim,ireport:'1'});
                }
            });
            $('.irsaliyeek').click(function () {
                var irsaliye = $('#sample_editable_1 .active .id').text();
                if (irsaliye !== null) {
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

                }
            });
        });
        $('#formsubmit').click(function () {
            $('#form_sample_3').submit();
            $.blockUI();
        });
        $('#cikissubmit').click(function () {
            $('#form_sample_5').submit();
            $.blockUI();
        });
        $('#depolararasisubmit').click(function () {
            $('#form_sample_6').submit();
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
                        <i class="fa fa-tag"></i>Depo Teslimat Bilgisi
                    </div>
                    <div class="actions">
                        <a class="btn btn-default btn-sm irsaliyeek">
                            <i class="fa fa-list"></i> Sayaç Listesi </a>
                        <a class="btn btn-default btn-sm tutanak">
                            <i class="fa fa-check"></i> Teslim Tutanağı </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                        <tr><th>#</th>
                            <th>Cari Adı</th>
                            <th>Servis</th>
                            <th>Adet</th>
                            <th>Tipi</th>
                            <th>Durum</th>
                            <th>Teslim Eden</th>
                            <th>Teslim Tarihi</th>
                            <th></th><th></th><th></th>
                            <th></th><th></th><th></th>
                            <th>Detay</th>
                        </tr>
                        </thead>
                    </table>
                    <form action="" id="form_sample" class="form-horizontal" novalidate="novalidate">
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-xs-offset-2 col-xs-10">
                                    <a class='btn yellow depocikis hide' href='#depocikis' data-toggle='modal' data-id=''>Çıkış Yap</a>
                                    <a class='btn green gonder hide' href='#gonder' data-toggle='modal' data-id=''>Depo Teslim Et</a>
                                    <a class='btn green depolararasi hide' href='#depolararasi' data-toggle='modal' data-id=''>Depolararası Gönder</a>
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
                    <h4 class="modal-title">Depo Teslimi Silinecek</h4>
                </div>
                <div class="modal-body">
                    Seçilen Depo Teslimini Silmek İstediğinizden Emin Misiniz?
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
                                    <i class="fa fa-pencil"></i>Depo Teslimat Detayı
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Depo Teslimat Detayı</h3>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Cari Adı:</label>
                                            <label class="col-xs-8 cariadi" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Teslim Tarihi:</label>
                                            <label class="col-xs-8 teslimtarihi" style="padding-top: 7px"></label>
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
                                                    <th>İşlem</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <h4 class="form-section">Fatura Bilgisi  <span style="font-size: 12px">Teslimat sonrası Netsis tarafında oluşan faturayı temsil eder</span></h4>
                                        <div class="form-group faturadetaykisim">
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Fatura No:</label>
                                                <label class="col-xs-8 faturano" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Cari Kod:</label>
                                                <label class="col-xs-8 carikod" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-2 col-xs-4">Fatura Adresi:</label>
                                                <label class="col-sm-10 col-xs-8 faturaadres" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Özel Kod:</label>
                                                <label class="col-xs-8 ozelkod" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Plasiyer Kodu:</label>
                                                <label class="col-xs-8 plasiyerkod" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Depo Kodu:</label>
                                                <label class="col-xs-8 depokod" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Açıklama:</label>
                                                <label class="col-xs-8 aciklama" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-2 col-xs-4">Teslimat Adresi:</label>
                                                <label class="col-sm-10 col-xs-8 teslimatadres" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Belge1:</label>
                                                <label class="col-xs-8 aciklama1" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Belge2:</label>
                                                <label class="col-xs-8 aciklama2" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Diğer Açıklama:</label>
                                                <label class="col-xs-8 aciklama3" style="padding-top: 9px"></label>
                                            </div>
                                        </div>
                                        <div class="form-group faturasizdetaykisim">
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Açıklama:</label>
                                                <label class="col-xs-8 faturasizaciklama" style="padding-top: 9px"></label>
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
    <div class="modal fade" id="gonder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-truck"></i>Depo Teslimat Sayfası
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{ URL::to('depo/teslimet') }}" data-action="{{URL::to('depo/teslimet')}}" id="form_sample_3" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h4 class="form-section">Müşteri Bilgisi</h4>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-sm-10 col-xs-8 teslimcariadi" style="padding-top: 9px"></label>
                                        </div>
                                        <h4 class="form-section col-xs-12">Bilgilendirme Maili <span style="font-size: 12px">Depo Teslim Listesini Belirtilen Mail Adresine Gönderir.</span>
                                            <label><input type="checkbox" id=mailvar name="mailvar" /> Bilgilendirme Maili Gitsin mi? </label></h4>
                                        <div class="form-group mailvar hide col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Mail Adresi:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><input type="text" id="yetkilimail" name="yetkilimail" value="{{ Input::old('yetkilimail') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group mailvar hide col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Diğer Mail Adresleri:</label>
                                            <div class="col-xs-8">
                                                <input type="text" id="mailcc" name="mailcc" value="{{ Input::old('mailcc') }}" data-required="1" class="form-control">
                                            </div>
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
                                                    <th>Garanti</th>
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
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Seçilen Sayaç Sayısı:</label>
                                                <label class="col-xs-8 teslimadet" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">TOPLAM TUTAR:</label>
                                                <label class="col-xs-8 teslimtutar" style="padding-top: 9px">0.00</label>
                                                <input class="hide" id="teslimtutar" name="teslimtutar"/>
                                                <input class="hide" id="teslimtutar2" name="teslimtutar2"/>
                                                <input class="hide" id="teslimsecilenler" name="teslimsecilenler"/>
                                                <input class="hide" id="teslimadet" name="teslimadet"/>
                                                <input class="hide" id="teslimtumu" name="teslimtumu"/>
                                                <input class="hide" id="teslimbirim" name="teslimbirim"/>
                                                <input class="hide" id="teslimbirim2" name="teslimbirim2"/>
                                                <input class="hide" id="teslimbirimi" name="teslimbirimi"/>
                                                <input class="hide" id="teslimbirimi2" name="teslimbirimi2"/>
                                            </div>
                                        </div>
                                        <h4 class="form-section">Fatura Bilgisi  <span style="font-size: 12px">Teslimat sonrası Netsis tarafında oluşacak faturayı temsil edecek</span>
                                            <label><input type="checkbox" id="faturavar" name="faturavar" checked/> Fatura Çıkacak mı? </label></h4>
                                        <div class="form-group faturakismi">
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Fatura No:</label>
                                                <label class="col-xs-8 teslimfaturano" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Cari Kod:</label>
                                                <label class="col-xs-8 teslimcarikod" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-2 col-xs-4">Fatura Adresi:</label>
                                                <label class="col-sm-10 col-xs-8 teslimfaturaadres" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Özel Kod:</label>
                                                <label class="col-xs-8 teslimozelkod" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Plasiyer Kodu:</label>
                                                <label class="col-xs-8 teslimplasiyerkod" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Depo Kodu:</label>
                                                <label class="col-xs-8 teslimdepokod" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Açıklama:</label>
                                                <label class="col-xs-8 teslimaciklama" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-2 col-xs-4">Teslimat Adresi:</label>
                                                <div class="col-sm-8 col-xs-6">
                                                    <input type="text" id="teslimadres" name="teslimadres" data-required="1" class="form-control" maxlength="100"
                                                            placeholder="Fatura Adresinden Farklı Olduğu Durumlarda Girilecektir">
                                                </div>
                                                <div class="col-xs-2" style="text-align: center">
                                                    <button type="button" class="btn green adressec" data-toggle="modal" data-target="#adressec">Seç</button>
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Belge1:</label>
                                                <div class="col-xs-8">
                                                    <select class="form-control select2me select2-offscreen" id="teslimaciklama1" name="teslimaciklama1" tabindex="-1" title="" disabled>
                                                        <option value="0">TAMİR BAKIM</option>
                                                        <option value="1">GARANTİ KAPSAMINDA YAPILMIŞTIR.FATURA EDİLMEYECEKTİR.</option>
                                                        <option value="2">GERİ GÖNDERİMDİR. FATURA EDİLMEYECEKTİR.</option>
                                                        <option value="3">HURDA SAYAÇTIR. FATURA EDİLMEYECEKTİR.</option>
                                                        <option value="4">DEPOLAR ARASI SEVKTİR. FATURA EDİLMEYECEKTİR.</option>
                                                        {{--<option value="5">ŞİKAYETLİ MUAYENE KAPSAMINDA DEĞERLENDİRİLMİŞTİR. FATURA EDİLMEYECEKTİR.</option>--}}
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Belge2:</label>
                                                <div class="col-xs-8">
                                                    <input type="text" id="teslimaciklama2" name="teslimaciklama2" data-required="1" class="form-control" maxlength="100">
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Diğer Açıklama:</label>
                                                <div class="col-xs-8">
                                                    <input type="text" id="teslimaciklama3" name="teslimaciklama3" data-required="1" class="form-control" maxlength="100">
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12" style="font-size: 12px;margin-top:10px">
                                                <div class=""><span >Yetkili Kişi, Telefonu, vb...</span></div>
                                            </div>
                                        </div>
                                        <div class="form-group faturasizkismi">
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Açıklama:</label>
                                                <div class="col-xs-8">
                                                    <input type="text" id="teslimaciklama4" name="teslimaciklama4" data-required="1" class="form-control" maxlength="100">
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
                                <form action="" id="form_sample_4" class="form-horizontal" novalidate="novalidate">
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
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_4">
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
                                <form action="{{URL::to('depo/cikisyap')}}" data-action="{{URL::to('depo/cikisyap')}}" id="form_sample_5" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Aşağıdaki Sayaçların Depodan Çıkışı Yapılacaktır</h3>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-xs-8 cikiscariadi" style="padding-top: 9px"></label>
                                        </div>
                                        <input class="hide" id="cikissecilenler" name="cikissecilenler"/>
                                        <input class="hide" id="cikisadet" name="cikisadet"/>
                                        <input class="hide" id="cikisid" name="cikisid"/>
                                        <h4 class="form-section col-xs-12">Sayaç Listesi</h4>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_5">
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
    <div class="modal fade" id="depolararasi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Sayaçlar Depolararası Aktarılacak
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{URL::to('depo/depolararasigonder')}}" data-action="{{URL::to('depo/depolararasigonder')}}" id="form_sample_6" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Aşağıdaki Sayaçların Depolararası Aktarımı Yapılacaktır</h3>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-xs-8 depolararasicariadi" style="padding-top: 9px"></label>
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
                                                    <th>Seri No</th>
                                                    <th>Sayaç Adı</th>
                                                    <th>Üretim Yeri</th>
                                                    <th>Durumu</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-6">Seçilen Sayaç Sayısı:</label>
                                                <label class="col-xs-6 depolararasiadet" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <input class="hide" id="depolararasisecilenler" name="depolararasisecilenler"/>
                                                <input class="hide" id="depolararasitumu" name="depolararasitumu"/>
                                                <input class="hide" id="depolararasiadet" name="depolararasiadet"/>
                                                <input class="hide" id="depolararasiid" name="depolararasiid"/>
                                            </div>
                                        </div>
                                        <h4 class="form-section">Fatura Bilgisi  <span style="font-size: 12px">Teslimat sonrası Netsis tarafında oluşacak faturayı temsil edecek</span>
                                            <label><input type="checkbox" id="depolararasifaturavar" name="depolararasifaturavar" checked/> Fatura Çıkacak mı? </label></h4>
                                        <div class="form-group depolararasifaturakismi">
                                            <div class="form-group col-xs-12">
                                                <label class="control-label col-sm-2 col-xs-4">Fatura No:</label>
                                                <label class="col-sm-10 col-xs-8 depolararasifaturano" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-2 col-xs-4">Fatura Adresi:</label>
                                                <label class="col-sm-10 col-xs-8 depolararasifaturaadres" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-2 col-xs-4">Teslimat Adresi:</label>
                                                <div class="col-sm-8 col-xs-6">
                                                    <input type="text" id="depolararasiadres" name="depolararasiadres" data-required="1" class="form-control" maxlength="100"
                                                           placeholder="Fatura Adresinden Farklı Olduğu Durumlarda Girilecektir">
                                                </div>
                                                <div class="col-xs-2" style="text-align: center">
                                                    <button type="button" class="btn green depolararasiadressec" data-toggle="modal" data-target="#depolararasiadressec">Seç</button>
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Belge1:</label>
                                                <div class="col-xs-8">
                                                    <select class="form-control select2me select2-offscreen" id="depolararasiaciklama1" name="depolararasiaciklama1" tabindex="-1" title="">
                                                        <option value="0">DEPOLAR ARASI SEVKTİR. FATURA EDİLMEYECEKTİR.</option>
                                                        <option value="1">HURDA SAYAÇTIR. FATURA EDİLMEYECEKTİR.</option>
                                                        <option value="2">GERİ GÖNDERİMDİR. FATURA EDİLMEYECEKTİR.</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Belge2:</label>
                                                <div class="col-xs-8">
                                                    <input type="text" id="depolararasiaciklama2" name="depolararasiaciklama2" data-required="1" class="form-control" maxlength="100">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green depolararasigonder" data-toggle="modal" data-target="#depolararasiconfirm">Depolararası Aktar</button>
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
                                <form action="" id="form_sample_7" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Önceki Teslimat Adresleri</h3>
                                        <input class="hide" id="secilenadres" name="secilenadres"/>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_7">
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
    <div class="modal fade" id="depolararasiadressec" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                                <form action="" id="form_sample_8" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Önceki Teslimat Adresleri</h3>
                                        <input class="hide" id="depolararasisecilenadres" name="depolararasisecilenadres"/>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_8">
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
                                                    <button type="button" class="btn green depolararasiteslimadresisec" data-dismiss="modal">Tamam</button>
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
    <div class="modal fade" id="kayit-sil" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Sayaç Ücretlendirme Ekranına Geri Gönderilecektir</h4>
                </div>
                <div class="modal-body">
                    Seçilen Depo Teslimini Silmek İstediğinizden Emin Misiniz?
                </div>
                <div class="modal-footer">
                    <a id="kayitid" href="" type="button" class="btn blue">Sil</a>
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
                    <h4 class="modal-title">Sayaçlar Teslim Edilecek</h4>
                </div>
                <div class="modal-body">
                    Seçilen Sayaçlar Depoya Teslim Edilecektir?
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
    <div class="modal fade" id="depolararasiconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Sayaçların Depo Çıkışı Yapılacak</h4>
                </div>
                <div class="modal-body">
                    Seçilen Sayaçlar Depolararası Olarak Aktarılacaktır?
                </div>
                <div class="modal-footer">
                    <a id="depolararasisubmit" href="#" type="button" data-dismiss="modal" class="btn green">Onayla</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
@stop
