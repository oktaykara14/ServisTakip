@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Satış Fatura<small> Kayıt Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
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
<script src="{{ URL::to('assets/global/plugins/jquery-maskmoney/jquery.maskMoney.min.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
    <script src="{{ URL::to('pages/sube/form-validation-6.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
   FormValidationSube.init();
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
            "url": "{{ URL::to('sube/sayacsatislist') }}",
            "type": "POST",
            "data": {
                "netsiscari_id" : "@if(isset(Auth::user()->netsiscari_id) && count(Auth::user()->netsiscari_id)>0){{Auth::user()->netsiscarilist}}@endif"
            }
        },
        "bServerSide": true,
        "fnDrawCallback" : function() {
            $(document).on("click", ".delete", function () {
                var id = $(this).data('id');
                $(".modal-footer #id").attr('href',"{{ URL::to('sube/sayacsatissil') }}/"+id );
            });
            var secilen=$('#secilen').val();
            $("#sample_editable_1  tr .id").each(function(){
                if(secilen===$(this).html()){
                    $(this).parents('tr').addClass("active");
                }
            });
        },
        "aaSorting": [[5,'asc']],
        "columnDefs": [ { targets: [ 1 ], orderData: [ 1, 0 ] },
            { targets: [ 2 ], orderData: [ 2, 5, 0 ] },
            { targets: [ 3 ], orderData: [ 3, 5, 0 ] },
            { targets: [ 4 ], orderData: [ 4, 5, 0 ] },
            { targets: [ 5 ], orderData: [ 5, 0 ] },
            { targets: [ 6 ], orderData: [ 6, 5, 0 ] }
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
            {data: 'id', name: 'subesayacsatis.id',"class":"id","orderable": true, "searchable": true},
            {data: 'adisoyadi', name: 'abone.adisoyadi',"orderable": true, "searchable": false},
            {data: 'tckimlikno', name: 'abone.tckimlikno',"orderable": true, "searchable": true},
            {data: 'yeradi', name: 'uretimyer.yeradi',"orderable": true, "searchable": false},
            {data: 'toplamtutar', name: 'subesayacsatis.toplamtutar',"orderable": true, "searchable": true},
            {data: 'gdurum', name: 'subesayacsatis.gdurum',"class":"durum","orderable": true, "searchable": false},
            {data: 'faturatarihi', name: 'subesayacsatis.faturatarihi',"orderable": true, "searchable": false},
            {data: 'gfaturatarihi', name: 'subesayacsatis.gfaturatarihi',"visible": false, "searchable": true},
            {data: 'nadisoyadi', name: 'abone.nadisoyadi',"visible": false, "searchable": true},
            {data: 'nyeradi', name: 'uretimyer.nyeradi',"visible": false, "searchable": true},
            {data: 'ndurum', name: 'subesayacsatis.ndurum',"visible": false, "searchable": true},
            {data: 'faturano', name: 'subesayacsatis.faturano',"visible": false, "searchable": true},
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
        '<option value="8">Adı Soyadı</option>'+
        '<option value="2">TC No</option>'+
        '<option value="9">Yeri</option>'+
        '<option value="4">Tutar</option>'+
        '<option value="10">Durum</option>'+
        '<option value="11">FaturaNo</option>'+
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
        if(oTable.cell( $(this).children('.id')).data()!==undefined){
            $(this).toggleClass("active");
            var secilen = "";
            if($(this).hasClass('active')){
                $("tbody tr").removeClass("active");
                $(this).addClass("active");
                secilen = oTable.cell($(this).children('.id')).data();
                $('#secilen').val(secilen);
            }else{
                $(this).removeClass("active");
                $('#secilen').val("");
            }
        }
    });
    $('.fatura').click(function () {
        var fatura = $('#sample_editable_1 .active .id').text();
        if (fatura !== "") {
            var durum =$('#sample_editable_1 .active .durum').text();
            if(durum!=="Bekliyor" && durum!==""){
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
                $.redirectPost(redirect, {fatura: fatura,ireport:'1'});
            }else{
                toastr["warning"]('Fatura Oluşturmadan Satış Faturası Çıkarılamaz!', 'Fatura Hatası');
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
                $.getJSON("{{ URL::to('sube/satissorgula') }}",{serino:serino}, function (event) {
                    if (event.durum)                    {
                        var satisfatura = event.satisfatura;
                        var search=jQuery.fn.DataTable.ext.type.search.string(satisfatura.faturano);
                        $('#kriter').select2('val','11');
                        $('#sample_editable_1_filter input[type=search]').val(search);
                        $('#search').val(search);
                        oTable.search( '' ).columns().search( '' );
                        oTable.column("11").search(search)
                            .column("0").search(satisfatura.id)
                            .draw();
                    } else {
                        $('#kriter').select2('val','');
                        $('#sample_editable_1_filter input[type=search]').val("");
                        $('#search').val("");
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
                    <i class="fa fa-tag"></i>Satış Fatura Bilgileri
                </div>
                <div class="actions">
                    <a class="btn btn-default btn-sm" data-toggle="modal" data-target="#serinosorgula">
                        <i class="fa fa-search"></i> Seri No Sorgula</a>
                    <a class="btn btn-default btn-sm fatura">
                        <i class="fa fa-print"></i> Satış Faturası </a>
                    <a href="{{ URL::to('sube/sayacsatisekle') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-pencil"></i> Yeni Fatura Kaydet </a>
                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                    <thead>
                        <tr><th>#</th>
                            <th>Adı Soyadı</th>
                            <th>TC No</th>
                            <th>Yeri</th>
                            <th>Tutar</th>
                            <th>Durum</th>
                            <th>Tarih</th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                </table>
                <form action="" id="form_sample" class="form-horizontal" novalidate="novalidate">
                    <div class="form-body">
                        <div class="form-group">
                            <div class="col-xs-offset-2 col-xs-2"><a class='btn green cikar hide' href='#cikar' data-toggle='modal' data-id=''>Fatura Oluştur</a></div>
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
                <h4 class="modal-title">Fatura Silinecek</h4>
            </div>
            <div class="modal-body">
                Seçilen Faturayı Silmek İstediğinizden Emin Misiniz?
            </div>
            <div class="modal-footer">
                <a id="id" href="" type="button" class="btn blue">Sil</a>
                <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="serinosorgula" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="portlet box">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-plus"></i>Seri Numarası ile Fatura Sorgulama Ekranı
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
                                    <h3 class="form-section col-xs-12">Seri Numarası ile Fatura Sorgulama Ekranı</h3>
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
@stop
