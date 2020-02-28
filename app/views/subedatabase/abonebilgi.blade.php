@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Abone Bilgi <small>Görüntüleme Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.tr.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/datatables.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery.quicksearch.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>

@stop

@section('page-script')
    <script src="{{ URL::to('pages/subedatabase/form-validation-4.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
   FormValidationSubeDatabase.init();
});
$(document).ready(function(){
    $("select").on("select2-close", function () { $(this).valid(); });
});
</script>
<script>
    $(document).ready(function() {
        $('.getir').click(function(){
            var kriter = $("#kriter").val();
            var kriterdeger = $("#kriterdeger").val();
            var subekodu = $("#subekodu").val();
            if (kriterdeger !== "" && kriter !== "" ) {
                $.blockUI();
                $('.abonebilgi').html('');
                var newRow;
                $.getJSON("{{ URL::to('subedatabase/bilgigetir') }}",{tip:kriter,kriter:kriterdeger,subekodu:subekodu}, function (event) {
                    if (event.durum)
                    {
                        if(event.count>1){
                            switch (kriter) {
                                case "1":
                                    abonebilgi = event.abonebilgi;
                                    $('#sample_2 tbody tr').remove();
                                    $.each(abonebilgi, function (index) {
                                        newRow = '<tr><td><input type="checkbox" class="checkboxes"/></td>' +
                                            '<td class="hide id">' + abonebilgi[index].id + '</td><td>' + abonebilgi[index].serino + '</td><td>' + abonebilgi[index].cihazno + '</td>' +
                                            '<td>' + $.datepicker.formatDate('dd-mm-yy', new Date(abonebilgi[index].uretimtarihi)) + '</td><td>'+abonebilgi[index].gdurum+'</td></tr>';
                                        $('#sample_2 tbody').append(newRow);
                                    });
                                    $('#sayaclistesi').modal('show');
                                    break;
                                case "2":
                                case "3":
                                case "4":
                                case "5":
                                    abonebilgi = event.abonebilgi;
                                    $('#sample_1 tbody tr').remove();
                                    $.each(abonebilgi, function (index) {
                                        newRow = '<tr><td><input type="checkbox" class="checkboxes"/></td>' +
                                            '<td class="hide id">' + abonebilgi[index].id + '</td><td>' + abonebilgi[index].aboneno + '</td><td>' + abonebilgi[index].adisoyadi + '</td>' +
                                            '<td>' + abonebilgi[index].faturaadresi + '</td><td>'+abonebilgi[index].gdurum+'</td></tr>';
                                        $('#sample_1 tbody').append(newRow);
                                    });
                                    $('#abonelistesi').modal('show');
                                    break;
                                default:
                                    abonebilgi = event.abonebilgi;
                                    $('#sample_2 tbody tr').remove();
                                    $.each(abonebilgi, function (index) {
                                        newRow = '<tr><td><input type="checkbox" class="checkboxes"/></td>' +
                                            '<td class="hide id">' + abonebilgi[index].id + '</td><td>' + abonebilgi[index].serino + '</td><td>' + abonebilgi[index].cihazno + '</td>' +
                                            '<td>' + $.datepicker.formatDate('dd-mm-yy', new Date(abonebilgi[index].uretimtarihi)) + '</td><td>'+abonebilgi[index].gdurum+'</td></tr>';
                                        $('#sample_2 tbody').append(newRow);
                                    });
                                    $('#sayaclistesi').modal('show');
                                    break;
                            }
                        }else {
                            var abonebilgi = event.abonebilgi[0];
                            newRow = '<div class="panel-group accordion col-xs-12" id="accordion">' +
                                '<div class="panel panel-default servistakip_ek"><div class="panel-heading"><h4 class="panel-title">' +
                                '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_abone">Abone Bilgisi</a></h4></div>' +
                                '<div id="collapse_abone" class="panel-collapse in"><div class="panel-body">' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Adı Soyadı:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.adisoyadi + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Abone No:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.aboneno + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Başvuru No:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.basvuruno + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Başvuru Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + $.datepicker.formatDate('dd-mm-yy', new Date(abonebilgi.basvurutarihi)) + '</label></div>' +
                                '<div class="form-group col-xs-12"><label class="col-sm-2 col-xs-4 control-label">Fatura Adresi:</label><label class="col-sm-10 col-xs-8" style="padding-top: 8px">' + (abonebilgi.faturaadresi !== null ? abonebilgi.faturaadresi : '') + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">TC No / Vergi No:</label><label class="col-xs-8" style="padding-top: 8px">' + (abonebilgi.vno !== null ? abonebilgi.vno : '') + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Telefon 1:</label><label class="col-xs-8" style="padding-top: 8px">' + (abonebilgi.tlf1 !== null ? abonebilgi.tlf1 : '') + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Telefon 2:</label><label class="col-xs-8" style="padding-top: 8px">' + (abonebilgi.tlf2 !== null ? abonebilgi.tlf2 : '') + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">GSM:</label><label class="col-xs-8" style="padding-top: 8px">' + (abonebilgi.gsm !== null ? abonebilgi.gsm : '') + '</label></div>' +
                                '</div></div></div>';
                            $('.abonebilgi').append(newRow);
                            $("select").on("select2-close", function () {$(this).valid();});
                            newRow = '<div class="panel-group accordion col-xs-12" id="accordion">' +
                                '<div class="panel panel-default servistakip_ek"><div class="panel-heading"><h4 class="panel-title">' +
                                '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_sayac">Sayaç Bilgisi</a></h4></div>' +
                                '<div id="collapse_sayac" class="panel-collapse in"><div class="panel-body">' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Seri No:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.serino + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Üretim Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + $.datepicker.formatDate('dd-mm-yy', new Date(abonebilgi.uretimtarihi)) + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Harcanan Kredi:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.sayac_harcanan + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Kalan Kredi:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.sayac_kalan + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Güncelleme Zamanı:</label><label class="col-xs-8" style="padding-top: 8px">' + (abonebilgi.sayacguncellemetarihi !== null ? $.datepicker.formatDate('dd-mm-yy', new Date(abonebilgi.sayacguncellemetarihi)) : '') + '</label></div>' +
                                '</div></div></div>';
                            $('.abonebilgi').append(newRow);
                            $("select").on("select2-close", function () {$(this).valid();});
                            newRow = '<div class="panel-group accordion col-xs-12" id="accordion">' +
                                '<div class="panel panel-default servistakip_ek"><div class="panel-heading"><h4 class="panel-title">' +
                                '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_tahsis">Tahsis Bilgisi ('+abonebilgi.gdurum+')</a></h4></div>' +
                                '<div id="collapse_tahsis" class="panel-collapse in"><div class="panel-body">' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Tahsis Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + $.datepicker.formatDate('dd-mm-yy', new Date(abonebilgi.tahsistarihi)) + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">T. Satılan Kredi:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.toplamsatilan + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Son K.Satış Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + (abonebilgi.sonsatistarihi !== null ? $.datepicker.formatDate('dd-mm-yy', new Date(abonebilgi.sonsatistarihi)) : '') + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Kart Ana Kredi:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.kart_anakredi + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Kart Yedek Kredi:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.kart_yedekkredi + '</label></div>' +
                                '</div></div></div>';
                            $('.abonebilgi').append(newRow);
                            $("select").on("select2-close", function () {$(this).valid();});
                        }
                    } else {
                        toastr[event.type](event.text,event.title);
                    }
                    $.unblockUI();
                });
            }else{
                toastr['warning']('Kriter Bilgisi Girilmemiş', 'Kriter Hatası');
            }
        });
        $('#listekapat').click(function () {
            $('#abonelistesi').modal('hide');
        });
        $('#listesec').click(function () {
            var abonebilgiid = $('#sample_1 .active .id').text();
            var kriter = $("#kriter").val();
            var subekodu = $("#subekodu").val();
            if ( kriter !== "") {
                $.blockUI();
                $('.abonebilgi').html('');
                var newRow;
                $.getJSON("{{ URL::to('subedatabase/listebilgigetir') }}", {tip: kriter,id: abonebilgiid,subekodu: subekodu}, function (event) {
                    if (event.durum) {
                        var abonebilgi = event.abonebilgi;
                        newRow = '<div class="panel-group accordion col-xs-12" id="accordion">' +
                            '<div class="panel panel-default servistakip_ek"><div class="panel-heading"><h4 class="panel-title">' +
                            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_abone">Abone Bilgisi</a></h4></div>' +
                            '<div id="collapse_abone" class="panel-collapse in"><div class="panel-body">' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Adı Soyadı:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.adisoyadi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Abone No:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.aboneno + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Başvuru No:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.basvuruno + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Başvuru Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + $.datepicker.formatDate('dd-mm-yy', new Date(abonebilgi.basvurutarihi)) + '</label></div>' +
                            '<div class="form-group col-xs-12"><label class="col-sm-2 col-xs-4 control-label">Fatura Adresi:</label><label class="col-sm-10 col-xs-8" style="padding-top: 8px">' + (abonebilgi.faturaadresi !== null ? abonebilgi.faturaadresi : '') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">TC No / Vergi No:</label><label class="col-xs-8" style="padding-top: 8px">' + (abonebilgi.vno !== null ? abonebilgi.vno : '') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Telefon 1:</label><label class="col-xs-8" style="padding-top: 8px">' + (abonebilgi.tlf1 !== null ? abonebilgi.tlf1 : '') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Telefon 2:</label><label class="col-xs-8" style="padding-top: 8px">' + (abonebilgi.tlf2 !== null ? abonebilgi.tlf2 : '') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">GSM:</label><label class="col-xs-8" style="padding-top: 8px">' + (abonebilgi.gsm !== null ? abonebilgi.gsm : '') + '</label></div>' +
                            '</div></div></div>';
                        $('.abonebilgi').append(newRow);
                        $("select").on("select2-close", function () {$(this).valid();});
                        newRow = '<div class="panel-group accordion col-xs-12" id="accordion">' +
                            '<div class="panel panel-default servistakip_ek"><div class="panel-heading"><h4 class="panel-title">' +
                            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_sayac">Sayaç Bilgisi</a></h4></div>' +
                            '<div id="collapse_sayac" class="panel-collapse in"><div class="panel-body">' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Seri No:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.serino + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Üretim Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + $.datepicker.formatDate('dd-mm-yy', new Date(abonebilgi.uretimtarihi)) + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Harcanan Kredi:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.sayac_harcanan + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Kalan Kredi:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.sayac_kalan + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Güncelleme Zamanı:</label><label class="col-xs-8" style="padding-top: 8px">' + (abonebilgi.sayacguncellemetarihi !== null ? $.datepicker.formatDate('dd-mm-yy', new Date(abonebilgi.sayacguncellemetarihi)) : '') + '</label></div>' +
                            '</div></div></div>';
                        $('.abonebilgi').append(newRow);
                        $("select").on("select2-close", function () {$(this).valid();});
                        newRow = '<div class="panel-group accordion col-xs-12" id="accordion">' +
                            '<div class="panel panel-default servistakip_ek"><div class="panel-heading"><h4 class="panel-title">' +
                            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_tahsis">Tahsis Bilgisi ('+abonebilgi.gdurum+')</a></h4></div>' +
                            '<div id="collapse_tahsis" class="panel-collapse in"><div class="panel-body">' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Tahsis Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + $.datepicker.formatDate('dd-mm-yy', new Date(abonebilgi.tahsistarihi)) + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">T. Satılan Kredi:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.toplamsatilan + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Son K.Satış Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + (abonebilgi.sonsatistarihi !== null ? $.datepicker.formatDate('dd-mm-yy', new Date(abonebilgi.sonsatistarihi)) : '') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Kart Ana Kredi:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.kart_anakredi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Kart Yedek Kredi:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.kart_yedekkredi + '</label></div>' +
                            '</div></div></div>';
                        $('.abonebilgi').append(newRow);
                        $("select").on("select2-close", function () {$(this).valid();});
                    } else {
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                    $('#abonelistesi').modal('hide');
                });
            } else {
                toastr['warning']('Kriter Bilgisi Girilmemiş', 'Kriter Hatası');
            }
        });
        $('#sayaclistekapat').click(function () {
            $('#sayaclistesi').modal('hide');
        });
        $('#sayaclistesec').click(function () {
            var abonebilgiid = $('#sample_2 .active .id').text();
            var kriter = $("#kriter").val();
            var subekodu = $("#subekodu").val();
            if ( kriter !== "") {
                $.blockUI();
                $('.abonebilgi').html('');
                var newRow;
                $.getJSON("{{ URL::to('subedatabase/listebilgigetir') }}", {tip: kriter,id: abonebilgiid,subekodu: subekodu}, function (event) {
                    if (event.durum) {
                        var abonebilgi = event.abonebilgi;
                        newRow = '<div class="panel-group accordion col-xs-12" id="accordion">' +
                            '<div class="panel panel-default servistakip_ek"><div class="panel-heading"><h4 class="panel-title">' +
                            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_abone">Abone Bilgisi</a></h4></div>' +
                            '<div id="collapse_abone" class="panel-collapse in"><div class="panel-body">' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Adı Soyadı:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.adisoyadi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Abone No:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.aboneno + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Başvuru No:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.basvuruno + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Başvuru Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + $.datepicker.formatDate('dd-mm-yy', new Date(abonebilgi.basvurutarihi)) + '</label></div>' +
                            '<div class="form-group col-xs-12"><label class="col-sm-2 col-xs-4 control-label">Fatura Adresi:</label><label class="col-sm-10 col-xs-8" style="padding-top: 8px">' + (abonebilgi.faturaadresi !== null ? abonebilgi.faturaadresi : '') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">TC No / Vergi No:</label><label class="col-xs-8" style="padding-top: 8px">' + (abonebilgi.vno !== null ? abonebilgi.vno : '') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Telefon 1:</label><label class="col-xs-8" style="padding-top: 8px">' + (abonebilgi.tlf1 !== null ? abonebilgi.tlf1 : '') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Telefon 2:</label><label class="col-xs-8" style="padding-top: 8px">' + (abonebilgi.tlf2 !== null ? abonebilgi.tlf2 : '') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">GSM:</label><label class="col-xs-8" style="padding-top: 8px">' + (abonebilgi.gsm !== null ? abonebilgi.gsm : '') + '</label></div>' +
                            '</div></div></div>';
                        $('.abonebilgi').append(newRow);
                        $("select").on("select2-close", function () {$(this).valid();});
                        newRow = '<div class="panel-group accordion col-xs-12" id="accordion">' +
                            '<div class="panel panel-default servistakip_ek"><div class="panel-heading"><h4 class="panel-title">' +
                            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_sayac">Sayaç Bilgisi</a></h4></div>' +
                            '<div id="collapse_sayac" class="panel-collapse in"><div class="panel-body">' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Seri No:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.serino + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Üretim Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + $.datepicker.formatDate('dd-mm-yy', new Date(abonebilgi.uretimtarihi)) + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Harcanan Kredi:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.sayac_harcanan + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Kalan Kredi:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.sayac_kalan + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Güncelleme Zamanı:</label><label class="col-xs-8" style="padding-top: 8px">' + (abonebilgi.sayacguncellemetarihi !== null ? $.datepicker.formatDate('dd-mm-yy', new Date(abonebilgi.sayacguncellemetarihi)) : '') + '</label></div>' +
                            '</div></div></div>';
                        $('.abonebilgi').append(newRow);
                        $("select").on("select2-close", function () {$(this).valid();});
                        newRow = '<div class="panel-group accordion col-xs-12" id="accordion">' +
                            '<div class="panel panel-default servistakip_ek"><div class="panel-heading"><h4 class="panel-title">' +
                            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_tahsis">Tahsis Bilgisi ('+abonebilgi.gdurum+')</a></h4></div>' +
                            '<div id="collapse_tahsis" class="panel-collapse in"><div class="panel-body">' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Tahsis Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + $.datepicker.formatDate('dd-mm-yy', new Date(abonebilgi.tahsistarihi)) + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">T. Satılan Kredi:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.toplamsatilan + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Son K.Satış Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + (abonebilgi.sonsatistarihi !== null ? $.datepicker.formatDate('dd-mm-yy', new Date(abonebilgi.sonsatistarihi)) : '') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Kart Ana Kredi:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.kart_anakredi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Kart Yedek Kredi:</label><label class="col-xs-8" style="padding-top: 8px">' + abonebilgi.kart_yedekkredi + '</label></div>' +
                            '</div></div></div>';
                        $('.abonebilgi').append(newRow);
                        $("select").on("select2-close", function () {$(this).valid();});
                    } else {
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                    $('#sayaclistesi').modal('hide');
                });
            } else {
                toastr['warning']('Kriter Bilgisi Girilmemiş', 'Kriter Hatası');
            }
        });
    });
</script>

@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Abone Bilgi Görüntüleme Ekranı
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form id="form_sample" class="form-horizontal" novalidate="novalidate">
            <div class="form-body">
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    Girilen Bilgilerde Hata Var.
                </div>
                <div class="alert alert-success display-hide">
                    <button class="close" data-close="alert"></button>
                    Bilgiler Doğru!
                </div>
                <div class="form-group col-xs-12 hide">
                    <input type="text" id="subekodu" name="subekodu" value="{{ $sube ? $sube->subekodu : '1'}}" data-required="1" class="form-control">
                    <input type="text" id="subelinked" name="subelinked" value="{{ $sube ? $sube->subelinked : ''}}" data-required="1" class="form-control">
                    <input type="text" id="bellinked" name="bellinked" value="{{ $sube ? $sube->bellinked : ''}}" data-required="1" class="form-control">
                    <input type="text" id="netsisdepo" name="netsisdepo" value="{{ $sube ? $sube->netsisdepolar_id : 1 }}" data-required="1" class="form-control">
                    <input type="text" id="netsiscari" name="netsiscari" value="{{ $sube ? $sube->netsiscari_id : 2631 }}" data-required="1" class="form-control">
                </div>
                <div class="form-group {{$sube ? ($sube->bellinked!=="" ? "" : "hide") : "hide"}}">
                    <label class="control-label col-xs-2">Kriter:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-3">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="kriter" name="kriter" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            <option value="1">Seri Numarası</option>
                            <option value="2">Abone No</option>
                            <option value="3">TC Kimlik No/Vergi Numarası</option>
                            <option value="4">Telefon</option>
                            <option value="5">Adı Soyadı</option>
                        </select>
                    </div>
                    <div class="input-icon right col-xs-3">
                        <i class="fa"></i><input type="text" id="kriterdeger" name="kriterdeger" data-required="1" class="form-control">
                    </div>
                    <div class="col-xs-2"><a class="btn green getir">Bilgileri Getir</a></div>
                </div>
                <div class="form-group {{$sube ? ($sube->bellinked=="" ? "" : "hide") : ""}}">
                    <label class="col-xs-12" style="color: red">{{$sube ? ($sube->bellinked=="" ? "Bağlantı Mevcut Değil.Önce Bağlantı Bilgilerini Kontrol Ediniz!" : "") : "Kullanıcının Bilgi Getirme Yetkisi Yok!"}}</label>
                </div>
                <h3 class="form-section">Arama Sonuçları</h3>
                <div class="abonebilgi col-xs-12" id="abonebilgi">

                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
            </div>
            </div>
        </form>
        <!-- END FORM-->
    </div>
<!-- END VALIDATION STATES-->
</div>
@stop

@section('modal')
    <div class="modal fade" id="abonelistesi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-search"></i>Abone Listesi
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <table class="table table-hover" id="sample_1">
                                    <thead>
                                    <tr><th class="table-checkbox"></th>
                                        <th class="abonebilgiid hide">Id</th>
                                        <th>Abone No</th>
                                        <th>Adı Soyadı</th>
                                        <th>Fatura Adresi</th>
                                        <th>Tahsis Durumu</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-xs-12" style="text-align: center">
                                            <button type="button" id="listesec" class="btn green">Seç</button>
                                            <button type="button" id="listekapat" class="btn default">Vazgeç</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="sayaclistesi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-search"></i>Sayaç Listesi
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <table class="table table-hover" id="sample_2">
                                    <thead>
                                    <tr><th class="table-checkbox"></th>
                                        <th class="abonebilgiid hide">Id</th>
                                        <th>Seri No</th>
                                        <th>Cihaz No</th>
                                        <th>Üretim Tarihi</th>
                                        <th>Tahsis Durumu</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-xs-12" style="text-align: center">
                                            <button type="button" id="sayaclistesec" class="btn green">Seç</button>
                                            <button type="button" id="sayaclistekapat" class="btn default">Vazgeç</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

