@extends('layout.master')

@section('page-title')
<!--suppress ALL -->
<div class="page-title">
    <h1>Servis Bilgisi<small> Kayıt Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::to('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" >
<link href="{{ URL::to('assets/global/plugins/icheck/skins/all.css') }}" rel="stylesheet" type="text/css" >
@stop

@section('page-styles')
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/datatables.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/moment-with-locales.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.tr.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/icheck/icheck.min.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
    <script src="{{ URL::to('pages/sube/form-validation-12.js') }}"></script>
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
            "url": "{{ URL::to('sube/serviskayitlist') }}",
            "type": "POST",
            "data": {
                "netsiscari_id" : "@if(isset(Auth::user()->netsiscari_id) && count(Auth::user()->netsiscari_id)>0){{Auth::user()->netsiscarilist}}@endif"
            }
        },
        "bServerSide": true,
        "fnDrawCallback" : function() {
            $(document).on("click", ".delete", function () {
                var id = $(this).data('id');
                $(".modal-footer #sayacid").attr('href',"{{ URL::to('sube/serviskayitsil') }}/"+id );
            });
        },
        "aaSorting": [[5,'asc']],
        "columnDefs": [ { targets: [ 1 ], orderData: [ 1, 0 ] },
            { targets: [ 2 ], orderData: [ 2, 0 ] },
            { targets: [ 3 ], orderData: [ 3, 6, 0 ] },
            { targets: [ 4 ], orderData: [ 4, 6, 0 ] },
            { targets: [ 5 ], orderData: [ 5, 6, 0 ] },
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
            {data: 'id', name: 'serviskayit.id',"class":"id","orderable": true, "searchable": true},
            {data: 'adisoyadi', name: 'abone.adisoyadi',"orderable": true, "searchable": false},
            {data: 'serino', name: 'abonesayac.serino',"orderable": true, "searchable": true},
            {data: 'kayitadres', name: 'serviskayit.kayitadres',"orderable": true, "searchable": false},
            {data: 'gtipi', name: 'serviskayit.gtipi',"orderable": true, "searchable": false},
            {data: 'gdurum', name: 'serviskayit.gdurum',"orderable": true, "searchable": false},
            {data: 'acilmatarihi', name: 'serviskayit.acilmatarihi',"orderable": true, "searchable": false},
            {data: 'kapanmatarihi', name: 'serviskayit.kapanmatarihi',"orderable": true, "searchable": false},
            {data: 'gacilmatarihi', name: 'serviskayit.gacilmatarihi',"visible": false, "searchable": true},
            {data: 'gkapanmatarihi', name: 'serviskayit.gkapanmatarihi',"visible": false, "searchable": true},
            {data: 'nadisoyadi', name: 'abone.nadisoyadi',"visible": false, "searchable": true},
            {data: 'nkayitadres', name: 'serviskayit.nkayitadres',"visible": false, "searchable": true},
            {data: 'ntipi', name: 'serviskayit.ntipi',"visible": false, "searchable": true},
            {data: 'ndurum', name: 'serviskayit.ndurum',"visible": false, "searchable": true},
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
        '<option value="10">Adı Soyadı</option>'+
        '<option value="2">Seri No</option>'+
        '<option value="8">Açılma Tarihi</option>'+
        '<option value="9">Kapanma Tarihi</option>'+
        '<option value="11">Adresi</option>'+
        '<option value="12">Tipi</option>'+
        '<option value="13">Durum</option>'+
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
<!--suppress NonAsciiCharacters -->
<script>
    $(document).ready(function() {
        moment.locale('tr');
        //noinspection JSNonASCIINames,JSNonASCIINames,JSNonASCIINames,JSNonASCIINames,JSNonASCIINames
        $('#tarih').daterangepicker({opens: (Metronic.isRTL() ? 'left' : 'right'), format: 'DD.MM.YYYY', separator: ' - ', startDate: moment().subtract(29,'days'), endDate: moment(), ranges: {'Bugün': [moment(), moment()],'Dün': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],'Son 7 Gün': [moment().subtract(6, 'days'), moment()],'Son 30 Gün': [moment().subtract(29, 'days'), moment()],'Bu Ay': [moment().startOf('month'), moment().endOf('month')],'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]}, minDate: '01/01/2010',maxDate: '31/12/2040',"locale": {"format": "DD.MM.YYYY","separator": " - ","applyLabel": "Tamam","cancelLabel": "İptal","fromLabel": "Başlangıç","toLabel": "Bitiş","customRangeLabel": "Yeni Tarih Gir","daysOfWeek": ["Pz", "Pt", "Sa", "Ça", "Pe", "Cu", "Ct"],"monthNames": ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"],"firstDay": 1}}, function (start, end) {$('#tarih').val(start.format('DD.MM.YYYY') + ' - ' + end.format('DD.MM.YYYY'));});
        $('#tarih').val(moment().subtract(29,'days').format('DD.MM.YYYY')+' - '+moment().format('DD.MM.YYYY'));
        $('#tamamlanantarih').daterangepicker({opens: (Metronic.isRTL() ? 'left' : 'right'), format: 'DD.MM.YYYY', separator: ' - ', startDate: moment().startOf('month'), endDate: moment().endOf('month'), ranges: {'Bugün': [moment(), moment()],'Dün': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],'Son 7 Gün': [moment().subtract(6, 'days'), moment()],'Son 30 Gün': [moment().subtract(29, 'days'), moment()],'Bu Ay': [moment().startOf('month'), moment().endOf('month')],'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]}, minDate: '01/01/2010',maxDate: '31/12/2040',"locale": {"format": "DD.MM.YYYY","separator": " - ","applyLabel": "Tamam","cancelLabel": "İptal","fromLabel": "Başlangıç","toLabel": "Bitiş","customRangeLabel": "Yeni Tarih Gir","daysOfWeek": ["Pz", "Pt", "Sa", "Ça", "Pe", "Cu", "Ct"],"monthNames": ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"],"firstDay": 1}}, function (start, end) {$('#tarih').val(start.format('DD.MM.YYYY') + ' - ' + end.format('DD.MM.YYYY'));});
        $('#tamamlanantarih').val(moment().startOf('month').format('DD.MM.YYYY')+' - '+moment().endOf('month').format('DD.MM.YYYY'));
        $('#gunsonutarih').daterangepicker({opens: (Metronic.isRTL() ? 'left' : 'right'), format: 'DD.MM.YYYY', separator: ' - ', startDate: moment().startOf('month'), endDate: moment().endOf('month'), ranges: {'Bugün': [moment(), moment()],'Dün': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],'Son 7 Gün': [moment().subtract(6, 'days'), moment()],'Son 30 Gün': [moment().subtract(29, 'days'), moment()],'Bu Ay': [moment().startOf('month'), moment().endOf('month')],'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]}, minDate: '01/01/2010',maxDate: '31/12/2040',"locale": {"format": "DD.MM.YYYY","separator": " - ","applyLabel": "Tamam","cancelLabel": "İptal","fromLabel": "Başlangıç","toLabel": "Bitiş","customRangeLabel": "Yeni Tarih Gir","daysOfWeek": ["Pz", "Pt", "Sa", "Ça", "Pe", "Cu", "Ct"],"monthNames": ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"],"firstDay": 1}}, function (start, end) {$('#tarih').val(start.format('DD.MM.YYYY') + ' - ' + end.format('DD.MM.YYYY'));});
        $('#gunsonutarih').val(moment().startOf('month').format('DD.MM.YYYY')+' - '+moment().endOf('month').format('DD.MM.YYYY'));
        $("#serino1").inputmask("mask", { mask:"9",repeat:10,greedy:!1 });
        $("#serino2").inputmask("mask", { mask:"9",repeat:10,greedy:!1 });
        $("#tamamlananserino1").inputmask("mask", { mask:"9",repeat:10,greedy:!1 });
        $("#tamamlananserino2").inputmask("mask", { mask:"9",repeat:10,greedy:!1 });
        $("#gunsonuserino1").inputmask("mask", { mask:"9",repeat:10,greedy:!1 });
        $("#gunsonuserino2").inputmask("mask", { mask:"9",repeat:10,greedy:!1 });
        $('#kriter').select2();
        $('#tarihcheck').on('change', function () {
            if ($('#tarihcheck').attr('checked')) {
                $("#tarih").removeAttr('disabled');
                $('.tarihtipi').iCheck('enable');
            } else {
                $("#tarih").attr('disabled',1);
                $('.tarihtipi').iCheck('disable');
                $('.tarihtipi').iCheck('uncheck');
                $('#tarihtipi1').iCheck('check');
            }
        });
        $('#adrescheck').on('change', function () {
            if ($('#adrescheck').attr('checked')) {
                $("#adres").removeAttr('readonly');
            } else {
                $("#adres").attr('readonly',1);
            }
        });
        $('#tipcheck').on('change', function () {
            if ($('#tipcheck').attr('checked')) {
                $("#kayittipi").removeAttr('disabled');
            } else {
                $("#kayittipi").attr('disabled',1);
            }
        });
        $('#durumcheck').on('change', function () {
            if ($('#durumcheck').attr('checked')) {
                $("#sondurum").removeAttr('disabled');
            } else {
                $("#sondurum").attr('disabled',1);
            }
        });
        $('#aciklamacheck').on('change', function () {
            if ($('#aciklamacheck').attr('checked')) {
                $("#aciklama").removeAttr('readonly');
            } else {
                $("#aciklama").attr('readonly',1);
            }
        });
        $('#topluaciklamacheck').on('change', function () {
            if ($('#topluaciklamacheck').attr('checked')) {
                $("#topluaciklama").removeAttr('readonly');
            } else {
                $("#topluaciklama").attr('readonly',1);
            }
        });
        $('#sericheck').on('change', function () {
            if ($('#sericheck').attr('checked')) {
                $("#serino1").removeAttr('readonly');
                $("#serino2").removeAttr('readonly');
            } else {
                $("#serino1").attr('readonly',1);
                $("#serino2").attr('readonly',1);
            }
        });
        $('#tamamlanantarihcheck').on('change', function () {
            if ($('#tamamlanantarihcheck').attr('checked')) {
                $("#tamamlanantarih").removeAttr('disabled');
            } else {
                $("#tamamlanantarih").attr('disabled',1);
            }
        });
        $('#tamamlanantipcheck').on('change', function () {
            if ($('#tamamlanantipcheck').attr('checked')) {
                $("#tamamlanankayittipi").removeAttr('disabled');
            } else {
                $("#tamamlanankayittipi").attr('disabled',1);
            }
        });
        $('#tamamlanansericheck').on('change', function () {
            if ($('#tamamlanansericheck').attr('checked')) {
                $("#tamamlananserino1").removeAttr('readonly');
                $("#tamamlananserino2").removeAttr('readonly');
            } else {
                $("#tamamlananserino1").attr('readonly',1);
                $("#tamamlananserino2").attr('readonly',1);
            }
        });
        $('#gunsonusericheck').on('change', function () {
            if ($('#gunsonusericheck').attr('checked')) {
                $("#gunsonuserino1").removeAttr('readonly');
                $("#gunsonuserino2").removeAttr('readonly');
            } else {
                $("#gunsonuserino1").attr('readonly',1);
                $("#gunsonuserino2").attr('readonly',1);
            }
        });
        $('.bekleyencikar').click(function () {
            var netsiscari = $('#netsiscari').val();
            var tarihtipi = $('#tarihtipi1').attr('checked') ? "1" : "2";
            var tarih = $('#tarih').val();
            var tarihcheck = $('#tarihcheck').attr('checked') ? "1" : "0";
            var adres = $('#adres').val();
            var adrescheck = $('#adrescheck').attr('checked') ? "1" : "0";
            var aciklama = $('#aciklama').val();
            var aciklamacheck = $('#aciklamacheck').attr('checked') ? "1" : "0";
            var kayittipi = $('#kayittipi').val();
            var tipcheck = $('#tipcheck').attr('checked') ? "1" : "0";
            var sondurum = $('#sondurum').select2('val');
            var list = "";
            if(sondurum.length>0){
                $.each(sondurum,function (index) {
                    list += (list=="" ? "" : ",")+sondurum[index];
                });
            }else{
                list="-1";
            }
            sondurum=list;
            var durumcheck = $('#durumcheck').attr('checked') ? "1" : "0";
            var sericheck = $('#sericheck').attr('checked') ? "1" : "0";
            var serino1 = $('#serino1').val();
            var serino2 = $('#serino2').val();
            var exp = $('#export').val();
            if (netsiscari !== "") {
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
                $.redirectPost(redirect, {netsiscari: netsiscari,tarihtipi:tarihtipi,tarihcheck: tarihcheck,tarih: tarih,adrescheck: adrescheck,adres: adres,
                    tipcheck: tipcheck,kayittipi: kayittipi,durumcheck: durumcheck,sondurum: sondurum,sericheck: sericheck,serino1: serino1,
                    serino2: serino2,aciklamacheck: aciklamacheck,aciklama: aciklama,export:exp,ireport:'1'});
            }else{
                toastr["warning"]('Bekleyen Listesi Çıkarma Yetkiniz Yok!', 'Rapor Hatası');
            }
        });
        $('.tamamlanancikar').click(function () {
            var netsiscari = $('#netsiscari').val();
            var kayittipi = $('#tamamlanankayittipi').val();
            var tipcheck = $('#tamamlanantipcheck').attr('checked') ? "1" : "0";
            var tarih = $('#tamamlanantarih').val();
            var tarihcheck = $('#tamamlanantarihcheck').attr('checked') ? "1" : "0";
            var sericheck = $('#tamamlanansericheck').attr('checked') ? "1" : "0";
            var serino1 = $('#tamamlananserino1').val();
            var serino2 = $('#tamamlananserino2').val();
            var exp = $('#tamamlananexport').val();
            if (netsiscari !== "") {
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
                $.redirectPost(redirect, {netsiscari: netsiscari,tipcheck: tipcheck,kayittipi: kayittipi,tarihcheck: tarihcheck,tarih: tarih,sericheck: sericheck,
                    serino1: serino1,serino2: serino2,export:exp,ireport:'2'});
            }else{
                toastr["warning"]('Bekleyen Listesi Çıkarma Yetkiniz Yok!', 'Rapor Hatası');
            }
        });
        $('.gunsonucikar').click(function () {
            var netsiscari = $('#netsiscari').val();
            var tarih = $('#gunsonutarih').val();
            var exp = $('#gunsonuexport').val();
            if (netsiscari !== "") {
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
                $.redirectPost(redirect, {netsiscari: netsiscari,tarih: tarih,export:exp,ireport:'3'});
            }else{
                toastr["warning"]('Bekleyen Listesi Çıkarma Yetkiniz Yok!', 'Rapor Hatası');
            }
        });
        $('#formsubmit').click(function () {
            $('#form_sample_3').submit();
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
                    <i class="fa fa-tag"></i>Kayıtlı Servis Bilgileri
                </div>
                <div class="actions">
                    <a class="btn btn-default btn-sm topluguncelleme" data-toggle="modal" data-target="#topluguncelleme">
                        <i class="fa fa-print fa-pencil-square-o"></i> Toplu Güncelle</a>
                    <a class="btn btn-default btn-sm gunsonu" data-toggle="modal" data-target="#gunsonu">
                        <i class="fa fa-calendar-check-o"></i> Gün Sonu Raporu</a>
                    <a class="btn btn-default btn-sm tamamlanan" data-toggle="modal" data-target="#tamamlanan">
                        <i class="fa fa-check"></i> Belediye Endeks Raporu</a>
                    <a class="btn btn-default btn-sm bekleyen" data-toggle="modal" data-target="#bekleyen">
                        <i class="fa fa-print"></i> Rapor</a>
                    <a href="{{ URL::to('sube/serviskayitekle') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-pencil"></i> Yeni Servis Kayıdı </a>
                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                    <thead>
                        <tr><th>#</th>
                            <th>Adı Soyadı</th>
                            <th>Seri No</th>
                            <th>Adresi</th>
                            <th>Tipi</th>
                            <th>Durum</th>
                            <th>Açılma Tarihi</th>
                            <th>Kapanma Tarihi</th>
                            <th></th><th></th><th></th>
                            <th></th><th></th><th></th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <input id="netsiscari" class="hide" value="@if(isset(Auth::user()->netsiscari_id) && count(Auth::user()->netsiscari_id)>0){{Auth::user()->netsiscarilist}}@endif">
        </div>
        <!-- END TABLE PORTLET-->
    </div>
</div>
@stop

@section('modal')
<div class="modal fade" id="bekleyen" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="portlet box">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-plus"></i>Servis Kayıt Listesi
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form action="" target="" id="form_sample_2" class="form-horizontal" method="POST" novalidate="novalidate">
                                <div class="form-body col-xs-12">
                                    <div class="alert alert-danger display-hide">
                                        <button class="close" data-close="alert"></button>
                                        Girilen Bilgilerde Hata Var.
                                    </div>
                                    <div class="alert alert-success display-hide">
                                        <button class="close" data-close="alert"></button>
                                        Bilgiler Doğru!
                                    </div>
                                    <h3 class="form-section col-xs-12">Servis Kayıt Listesi Rapor Ekranı</h3>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-6">Son Durumu:</label>
                                        <div class="col-sm-8 col-xs-6">
                                            <select class="form-control select2me select2-offscreen" id="sondurum" name="sondurum[]"  multiple="" tabindex="-1" >
                                                <option value="0" selected>Bekliyor</option>
                                                <option value="1" selected>Tamamlandı</option>
                                                <option value="2" selected>Şebeke Yok</option>
                                                <option value="3" selected>Tadilatlı Yer</option>
                                                <option value="4" selected>Beklemede</option>
                                                <option value="5" selected>Geçici Sayaç</option>
                                                <option value="6" selected>Büroda</option>
                                                <option value="7" selected>Kurumlar</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-6">Tarih Tipi:
                                            <input type="checkbox" id="tarihcheck" name="tarihcheck" /></label>
                                        <div class="col-sm-8 col-xs-6">
                                            <div class="input-group">
                                                <div class="icheck-inline">
                                                    <label>
                                                        <div class="iradio_square-green" style="position: relative;"><input type="radio" id="tarihtipi1" name="tarihtipi" class="icheck tarihtipi" checked="" data-radio="iradio_square-green" value="1" style="position: absolute; opacity: 0;" disabled></div> Açılma Tarihi </label>
                                                    <label>
                                                        <div class="iradio_square-green" style="position: relative;"><input type="radio" id="tarihtipi2" name="tarihtipi" class="icheck tarihtipi" data-radio="iradio_square-green" value="2" style="position: absolute; opacity: 0;" disabled></div> Kapanma Tarihi </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-6">Tarih Aralığı:</label>
                                        <div class="col-sm-8 col-xs-6">
                                            <div class="input-group" id="daterangepick">
                                                <input type="text" id="tarih" name="tarih" class="form-control" disabled>
                                                <span class="input-group-btn">
                                                    <button class="btn default date-range-toggle" type="button">
                                                        <i class="fa fa-calendar"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-6">Adres:
                                            <input type="checkbox" id="adrescheck" name="adrescheck" /></label>
                                        <div class="col-sm-8 col-xs-6">
                                            <input type="text" id="adres" name="adres" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-6">Kayıt Tipi:
                                            <input type="checkbox" id="tipcheck" name="tipcheck" /></label>
                                        <div class="col-sm-8 col-xs-6">
                                            <select class="form-control select2me select2-offscreen" id="kayittipi" name="kayittipi" tabindex="-1" title="" disabled>
                                                <option value="-1" selected>Hepsi</option>
                                                <option value="0">Sayaç Montaj</option>
                                                <option value="1">Tamir Montaj</option>
                                                <option value="2">Arıza Kontrol</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-6">SeriNo Aralığı:
                                            <input type="checkbox" id="sericheck" name="sericheck" /></label>
                                        <div class="col-sm-4 col-xs-3">
                                            <input type="text" id="serino1" name="serino1" class="form-control" readonly>
                                        </div>
                                        <div class="col-sm-4 col-xs-3">
                                            <input type="text" id="serino2" name="serino2" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-6">Açıklama:
                                            <input type="checkbox" id="aciklamacheck" name="aciklamacheck" /></label>
                                        <div class="col-sm-8 col-xs-6">
                                            <input type="text" id="aciklama" name="aciklama" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-6">Çıktı Tipi:</label>
                                        <div class="col-sm-8 col-xs-6">
                                            <select class="form-control select2me select2-offscreen" id="export" name="export" tabindex="-1" title="">
                                                <option value="pdf" selected>Pdf</option>
                                                <option value="xls">Excel</option>
                                                <option value="docx">Word</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-actions col-xs-12">
                                        <div class="row">
                                            <div class="col-xs-offset-3 col-xs-9">
                                                <a id="bekleyencikar" href="#" type="button" data-dismiss="modal" class="btn green bekleyencikar">Rapor Çıkar</a>
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
<div class="modal fade" id="tamamlanan" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="portlet box">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-plus"></i>Tamir Bakım Listesi
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form action="" target="" id="form_sample_4" class="form-horizontal" method="POST" novalidate="novalidate">
                                <div class="form-body col-xs-12">
                                    <div class="alert alert-danger display-hide">
                                        <button class="close" data-close="alert"></button>
                                        Girilen Bilgilerde Hata Var.
                                    </div>
                                    <div class="alert alert-success display-hide">
                                        <button class="close" data-close="alert"></button>
                                        Bilgiler Doğru!
                                    </div>
                                    <h3 class="form-section col-xs-12">Tamir Bakım Listesi Rapor Ekranı
                                        <h4>Belediyeye verilecek arıza kayıdı tamamlanmış sayaçların servis sayacının endeks bilgilerini ve varsa sayaç borç bilgisini içerir</h4>
                                    </h3>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-6">Tarih Aralığı:
                                        <input type="checkbox" id="tamamlanantarihcheck" name="tamamlanantarihcheck" /></label>
                                        <div class="col-sm-8 col-xs-6">
                                            <div class="input-group" id="daterangepick">
                                                <input type="text" id="tamamlanantarih" name="tamamlanantarih" class="form-control" disabled>
                                                <span class="input-group-btn">
                                                    <button class="btn default date-range-toggle" type="button">
                                                        <i class="fa fa-calendar"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-6">Kayıt Tipi:
                                            <input type="checkbox" id="tamamlanantipcheck" name="tamamlanantipcheck" /></label>
                                        <div class="col-sm-8 col-xs-6">
                                            <select class="form-control select2me select2-offscreen" id="tamamlanankayittipi" name="tamamlanankayittipi" tabindex="-1" title="" disabled>
                                                <option value="-1" selected>Hepsi</option>
                                                <option value="0">Sayaç Montaj</option>
                                                <option value="1">Tamir Montaj</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-6">SeriNo Aralığı:
                                            <input type="checkbox" id="tamamlanansericheck" name="tamamlanansericheck" /></label>
                                        <div class="col-sm-4 col-xs-3">
                                            <input type="text" id="tamamlananserino1" name="tamamlananserino1" class="form-control" readonly>
                                        </div>
                                        <div class="col-sm-4 col-xs-3">
                                            <input type="text" id="tamamlananserino2" name="tamamlananserino2" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-6">Çıktı Tipi:</label>
                                        <div class="col-sm-8 col-xs-6">
                                            <select class="form-control select2me select2-offscreen" id="tamamlananexport" name="tamamlananexport" tabindex="-1" title="">
                                                <option value="pdf" selected>Pdf</option>
                                                <option value="xls">Excel</option>
                                                <option value="docx">Word</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-actions col-xs-12">
                                        <div class="row">
                                            <div class="col-xs-offset-3 col-xs-9">
                                                <a id="tamamlanancikar" href="#" type="button" data-dismiss="modal" class="btn green tamamlanancikar">Rapor Çıkar</a>
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
<div class="modal fade" id="gunsonu" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="portlet box">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-plus"></i>Gün Sonu Raporu
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form action="" target="" id="form_sample_5" class="form-horizontal" method="POST" novalidate="novalidate">
                                <div class="form-body col-xs-12">
                                    <div class="alert alert-danger display-hide">
                                        <button class="close" data-close="alert"></button>
                                        Girilen Bilgilerde Hata Var.
                                    </div>
                                    <div class="alert alert-success display-hide">
                                        <button class="close" data-close="alert"></button>
                                        Bilgiler Doğru!
                                    </div>
                                    <h3 class="form-section col-xs-12">Servis Gün Sonu Rapor Ekranı</h3>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-6">Tarih Aralığı:</label>
                                        <div class="col-sm-8 col-xs-6">
                                            <div class="input-group" id="daterangepick">
                                                <input type="text" id="gunsonutarih" name="gunsonutarih" class="form-control">
                                                <span class="input-group-btn">
                                                    <button class="btn default date-range-toggle" type="button">
                                                        <i class="fa fa-calendar"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-6">Çıktı Tipi:</label>
                                        <div class="col-sm-8 col-xs-6">
                                            <select class="form-control select2me select2-offscreen" id="gunsonuexport" name="gunsonuexport" tabindex="-1" title="">
                                                <option value="pdf" selected>Pdf</option>
                                                <option value="xls">Excel</option>
                                                <option value="docx">Word</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-actions col-xs-12">
                                        <div class="row">
                                            <div class="col-xs-offset-3 col-xs-9">
                                                <a id="gunsonucikar" href="#" type="button" data-dismiss="modal" class="btn green gunsonucikar">Rapor Çıkar</a>
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
<div class="modal fade" id="topluguncelleme" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="portlet box">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-plus"></i>Servis Bekleyen Sayaçları Toplu Güncelleme Ekranı
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form action="{{ URL::to('sube/toplukayitguncelle') }}" id="form_sample_3" class="form-horizontal" method="POST" novalidate="novalidate">
                                <div class="form-body col-xs-12">
                                    <div class="alert alert-danger display-hide">
                                        <button class="close" data-close="alert"></button>
                                        Girilen Bilgilerde Hata Var.
                                    </div>
                                    <div class="alert alert-success display-hide">
                                        <button class="close" data-close="alert"></button>
                                        Bilgiler Doğru!
                                    </div>
                                    <h3 class="form-section col-xs-12">Servis Bekleyen Sayaçları Toplu Güncelleme Ekranı</h3>
                                    <div class="form-group col-xs-12">
                                        <label class="col-sm-2 col-xs-4 control-label">Sayaçlar:<span class="required" aria-required="true"> * </span></label>
                                        <div class="input-icon right col-xs-8">
                                            <i class="fa"></i><select class="form-control select2 select2-offscreen sayaclar" id="sayaclar" name="sayaclar[]" multiple="" tabindex="-1" placeholder="Sayaç Seçin...">
                                                @foreach($serviskayit as $kayit)
                                                    @if((Input::old('sayaclar'))==$kayit->id)
                                                        <option value="{{ $kayit->id }}" selected>{{ $kayit->serino }}</option>
                                                    @else
                                                        <option value="{{ $kayit->id }}" >{{ $kayit->serino }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-4">Kapanma Tarihi:</label>
                                        <div class="col-xs-8">
                                            <div class="input-group input-medium date date-picker kapanmatarihi" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                                <input type="text" id="kapanmatarihi" name="kapanmatarihi" class="form-control" value="{{Input::old('kapanmatarihi') ? Input::old('kapanmatarihi') : '' }}">
                                                <span class="input-group-btn"><button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-4">Servis Personeli:<span class="required" aria-required="true"> * </span></label>
                                        <div class="input-icon right col-xs-8">
                                            <i class="fa"></i><select class="form-control select2me select2-offscreen" id="personel" name="personel" tabindex="-1" title="">
                                                <option value="">Seçiniz...</option>
                                                @foreach($subepersonel as $personel)
                                                    @if((Input::old('personel'))==$personel->id)
                                                        <option value="{{ $personel->id }}" selected>{{ $personel->kullanici->adi_soyadi }}</option>
                                                    @else
                                                        <option value="{{ $personel->id }}" >{{ $personel->kullanici->adi_soyadi }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-4">Servis Durumu:<span class="required" aria-required="true"> * </span></label>
                                        <div class="input-icon right col-xs-8">
                                            <i class="fa"></i><select class="form-control select2me select2-offscreen" id="durum" name="durum" tabindex="-1" title="">
                                                <option value="0" {{Input::old('durum') === "0" ? "selected" : ""}}>Bekliyor</option>
                                                <option value="1" {{Input::old('durum') === "1" ? "selected" : ""}}>Tamamlandı</option>
                                                <option value="2" {{Input::old('durum') === "2" ? "selected" : ""}}>Şebeke Yok</option>
                                                <option value="3" {{Input::old('durum') === "3" ? "selected" : ""}}>Tadilat Yapılacak</option>
                                                <option value="4" {{Input::old('durum') === "4" ? "selected" : ""}}>Beklemede</option>
                                                <option value="5" {{Input::old('durum') === "5" ? "selected" : ""}}>Geçici Sayaç</option>
                                                <option value="6" {{Input::old('durum') === "6" ? "selected" : ""}}>Büroda</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-4">Açıklama:
                                            <input type="checkbox" id="topluaciklamacheck" name="topluaciklamacheck" /></label>
                                        <div class="col-xs-8">
                                            <input type="text" id="topluaciklama" name="topluaciklama" value="{{ Input::old('topluaciklama')}}" data-required="1" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="form-actions col-xs-12">
                                        <div class="row">
                                            <div class="col-xs-offset-3 col-xs-9">
                                                <button type="button" class="btn green topluguncelle" data-toggle="modal" data-target="#confirm">Güncelle</button>
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
                <h4 class="modal-title">Servis Bilgisi Silinecek</h4>
            </div>
            <div class="modal-body">
                Seçilen Servis Bilgisini Silmek İstediğinizden Emin Misiniz?
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
                <h4 class="modal-title">Servis Bilgisi Güncellenecek?</h4>
            </div>
            <div class="modal-body">
                Seçilen Sayaçlar için Servis Kayıtları Girilen Bilgilerle Kaydedilecektir?
            </div>
            <div class="modal-footer">
                <a id="formsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
            </div>
        </div>
    </div>
</div>
@stop
