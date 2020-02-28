@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Gaz Servis Mekanik <small>Servis Raporu Bekleyenler Ekranı</small></h1>
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
                "url": "{{ URL::to('mekanikgaz/raporlist') }}",
                "type": "POST",
                "data": {
                    "remove_id" : $('#id').val() ,
                    "uretimyer_id" : $('#uretimyerid').val()
                }
            },
            "bServerSide": true,
            "fnDrawCallback" : function() {
                $('#id').val('');
                $('#uretimyerid').val('');
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
                {data: 'arizakayittarihi', name: 'arizakayit.arizakayittarihi',"orderable": true, "searchable": false},
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
        var table2 = $('#sample_editable_2');
        table2.DataTable({
            "sPaginationType": "simple_numbers",
            "bProcessing": true,
            "ajax": {
                "url": "{{ URL::to('mekanikgaz/eskiraporlist') }}",
                "type": "POST",
                "data": {
                }
            },
            "bServerSide": true,
            "fnDrawCallback" : function() {
            },
            "aaSorting": [[3,'desc']],
            "columnDefs": [ { targets: [ 1 ], orderData: [ 1, 0 ] },
                { targets: [ 2 ], orderData: [ 2, 0 ] },
                { targets: [ 3 ], orderData: [ 3, 0 ] }
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
                {data: 'id', name: 'servisraporu.id',"class":"id","orderable": true, "searchable": true},
                {data: 'yeradi', name: 'uretimyer.yeradi',"orderable": true, "searchable": true},
                {data: 'adet', name: 'servisraporu.adet',"orderable": true, "searchable": true},
                {data: 'raportarihi', name: 'servisraporu.raportarihi',"orderable": true, "searchable": false},
                {data: 'graportarihi', name: 'servisraporu.graportarihi',"visible": false, "searchable": true},
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
        var tableWrapper = jQuery('#sample_editable_2_wrapper');
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        $(document).ready(function() {
            $('#kriter').select2();
            $('#uretimyer').on('change',function() {
                var uretimyer = $(this).val();
                $('#uretimyerid').val(uretimyer);
            });
            $('#uretimyer2').on('change',function() {
                var uretimyer = $(this).val();
                $('#uretimyerid').val(uretimyer);
            });
            $(document).on("click", ".rapor", function () {
                var id = $(this).data('id');
                if(id !=null){
                    $.extend({
                        redirectPost: function(location,callback, args)
                        {
                            var form = '';
                            $.each( args, function( key, value ) {
                                form += '<input type="hidden" name="'+key+'" value="'+value+'">';
                            });
                            $('<form action="' + location + '" method="POST" target="_blank">' + form + '</form>').appendTo($(document.body)).submit();
                            if(typeof callback=='function'){
                                callback.call(this);
                            }
                        }
                    });
                    var redirect = "";
                    $.redirectPost(redirect,function () { $('#id').val(id);$('#uretimyerid').val(''); window.setTimeout(function(){oTable.draw();}, 3000)},{arizaid: id,ireport:'1'});
                }
            });
            $('#topluraporal').click(function () {
                var uretimyerid = $('#uretimyerid').val();
                if(uretimyerid !=null && uretimyerid !==""){
                    $.extend({
                        redirectPost: function(location,callback, args)
                        {
                            var form = '';
                            $.each( args, function( key, value ) {
                                form += '<input type="hidden" name="'+key+'" value="'+value+'">';
                            });
                            $('<form action="' + location + '" method="POST" target="_blank">' + form + '</form>').appendTo($(document.body)).submit();
                            if(typeof callback=='function'){
                                callback.call(this);
                            }
                        }
                    });
                    var redirect = "";
                    $.redirectPost(redirect,function () { $('#id').val('');window.setTimeout(function(){oTable.draw();}, 3000)},{uretimyerid: uretimyerid,ireport:'2'});
                }
            });
            $('#kalibrasyonraporal').click(function () {
                var uretimyerid = $('#uretimyerid').val();
                if(uretimyerid !=null && uretimyerid !==""){
                    $.extend({
                        redirectPost: function(location,callback, args)
                        {
                            var form = '';
                            $.each( args, function( key, value ) {
                                form += '<input type="hidden" name="'+key+'" value="'+value+'">';
                            });
                            $('<form action="' + location + '" method="POST" target="_blank">' + form + '</form>').appendTo($(document.body)).submit();
                            if(typeof callback=='function'){
                                callback.call(this);
                            }
                        }
                    });
                    var redirect = "";
                    $.redirectPost(redirect,function () { $('#id').val('');window.setTimeout(function(){oTable.draw();}, 3000)},{uretimyerid: uretimyerid,ireport:'3'});
                }
            });
            $(document).on("click", ".eskirapor", function () {
                var id = $(this).data('id');
                if(id !=null){
                    $.extend({
                        redirectPost: function(location,callback, args)
                        {
                            var form = '';
                            $.each( args, function( key, value ) {
                                form += '<input type="hidden" name="'+key+'" value="'+value+'">';
                            });
                            $('<form action="' + location + '" method="POST" target="_blank">' + form + '</form>').appendTo($(document.body)).submit();
                            if(typeof callback=='function'){
                                callback.call(this);
                            }
                        }
                    });
                    var redirect = "";
                    $.redirectPost(redirect,function () { $('#id').val('');$('#uretimyerid').val('');window.setTimeout(function(){oTable.draw();}, 3000)},{servisraporuid: id,ireport:'4'});
                }
            });
            $(document).on("click", ".eskikalibrasyon", function () {
                var id = $(this).data('id');
                if(id !=null){
                    $.extend({
                        redirectPost: function(location,callback, args)
                        {
                            var form = '';
                            $.each( args, function( key, value ) {
                                form += '<input type="hidden" name="'+key+'" value="'+value+'">';
                            });
                            $('<form action="' + location + '" method="POST" target="_blank">' + form + '</form>').appendTo($(document.body)).submit();
                            if(typeof callback=='function'){
                                callback.call(this);
                            }
                        }
                    });
                    var redirect = "";
                    $.redirectPost(redirect,function () { $('#id').val('');$('#uretimyerid').val('');window.setTimeout(function(){oTable.draw();}, 3000) },{servisraporuid: id,ireport:'5'});
                }
            });
            $(document).on("click", ".eskisil", function () {
                var id = $(this).data('id');
                if(id !=null){
                    $.extend({
                        redirectPost: function(location,callback, args)
                        {
                            var form = '';
                            $.each( args, function( key, value ) {
                                form += '<input type="hidden" name="'+key+'" value="'+value+'">';
                            });
                            $('<form action="' + location + '" method="POST" >' + form + '</form>').appendTo($(document.body)).submit();
                            if(typeof callback=='function'){
                                callback.call(this);
                            }
                        }
                    });
                    var redirect = "";
                    $.redirectPost(redirect,function () { $('#id').val('');$('#uretimyerid').val('');window.setTimeout(function(){oTable.draw();}, 3000)},{servisraporuid: id,ireport:'6'});
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
                        <i class="fa fa-tag"></i>Servis Raporu Bekleyenler
                    </div>
                    <div class="actions">
                        <a class="btn btn-default btn-sm" data-toggle="modal" data-target="#oncekirapor">
                            <i class="fa fa-search"></i> Alınan Raporlar </a>
                        <a class="btn btn-default btn-sm" data-toggle="modal" data-target="#toplurapor">
                            <i class="fa fa-search"></i> Toplu Servis Raporu </a>
                        <a class="btn btn-default btn-sm" data-toggle="modal" data-target="#kalibrasyonrapor">
                            <i class="fa fa-search"></i> Toplu Kalibrasyon Sonuçları</a>
                    </div>
                    <input id="id" class="hide" value="">
                    <input id="uretimyerid" class="hide" value="">
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
    <div class="modal fade" id="oncekirapor" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="portlet box">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-plus"></i>Alınan Önceki Toplu Raporlar
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-hover table-bordered" id="sample_editable_2">
                                        <thead>
                                        <tr><th>#</th>
                                            <th>Üretim Yeri</th>
                                            <th>Adet</th>
                                            <th>Rapor Tarihi</th>
                                            <th></th>
                                            <th>İşlemler</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="modal fade" id="toplurapor" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Toplu Servis Raporu Ekranı
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" target="frame" id="form_sample_3" class="form-horizontal" method="POST" novalidate="novalidate" enctype="multipart/form-data">
                                    <div class="form-body col-xs-12">
                                        <h3 class="form-section col-xs-12">Üretim Yerine Göre Toplu Servis Raporu</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Üretim Yeri:</label>
                                            <div class="col-xs-8">
                                                <select class="form-control select2me select2-offscreen uretimyer" id="uretimyer" name="uretimyer" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                    @foreach($uretimyerleri as $uretimyer)
                                                        <option value="{{ $uretimyer->id }}">{{ $uretimyer->yeradi }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-actions col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-offset-3 col-xs-9">
                                                    <a id="topluraporal" href="#" type="button" data-dismiss="modal" class="btn green">Rapor Al</a>
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
    <div class="modal fade" id="kalibrasyonrapor" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Toplu Kalibrasyon Raporu Ekranı
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" target="frame" id="form_sample_4" class="form-horizontal" method="POST" novalidate="novalidate" enctype="multipart/form-data">
                                    <div class="form-body col-xs-12">
                                        <h3 class="form-section col-xs-12">Üretim Yerine Göre Toplu Kalibrasyon Raporu</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Üretim Yeri:</label>
                                            <div class="col-xs-8">
                                                <select class="form-control select2me select2-offscreen uretimyer2" id="uretimyer2" name="uretimyer2" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                    @foreach($kalibrasyonyerleri as $uretimyer)
                                                        <option value="{{ $uretimyer->id }}">{{ $uretimyer->yeradi }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-actions col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-offset-3 col-xs-9">
                                                    <a id="kalibrasyonraporal" href="#" type="button" data-dismiss="modal" class="btn green">Rapor Al</a>
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
@stop
