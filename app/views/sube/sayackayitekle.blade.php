@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Şube Sayaç Kayıt <small>Ekleme Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
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
<script src="{{ URL::to('assets/global/plugins/jquery.quicksearch.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>

@stop

@section('page-script')
<script src="{{ URL::to('pages/sube/form-validation-2.js') }}"></script>
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
    $(document).ready(function() {
        var flag=0;
        $(".kaydet").prop('disabled',true);
        var count=parseInt($("#count").val());
        $('.ekle').click(function(){
            //var adet=1;  // eklenecek sayı
            var newRow="";
            var cariadi = $('#cariadi').val();
            newRow += '<div class="panel panel-default sayaclar_ek"><input class="no hide" value="'+(count)+'"/><input class="adet hide" value="1"/><div class="panel-heading"><h4 class="panel-title">'+
            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_'+count+'">Yeni</a></h4></div>' +
            '<div id="collapse_'+count+'" class="panel-collapse in"><div class="panel-body"><div class="form-group col-sm-6 col-xs-12">'+
            '<label class="col-xs-4 col-sm-4 control-label">Seri No:<span class="required" aria-required="true"> * </span></label>'+
            '<div class="input-icon right col-xs-6 col-sm-8"><i class="fa"></i><input type="text" id="serino'+count+'" name="serino['+count+']" maxlength="15" class="form-control valid'+count+' serino"></div>' +
            '<label class="col-xs-1 hidden-sm hidden-md hidden-lg"><a class="btn red satirsil">Sil</a></label></div><div class="form-group col-sm-6 col-xs-12">'+
            '<label class="col-xs-4 col-sm-4 control-label">Geliş Yeri:<span class="required" aria-required="true"> * </span></label>'+
            '<div class="input-icon right col-xs-8 col-sm-6"><i class="fa"></i><select class="form-control select2me valid'+count+' uretimyer uretimyer'+count+'" id="uretimyer'+count+'" name="uretimyerleri['+count+']" tabindex="-1" title="">'+
            '<option value="">Seçiniz...</option>'+
            @foreach($uretimyerleri as $uretimyer)
            '<option value="{{ $uretimyer->id }}">{{ $uretimyer->yeradi }}</option>'+
            @endforeach
            '</select></div>'+
            '<label class="col-xs-1 hidden-xs"><a class="btn red satirsil">Sil</a></label></div>'+
            '<div class="form-group col-xs-12"><label class="col-sm-2 col-xs-4 control-label">İstek:</label><div class="col-sm-10 col-xs-8"><select class="form-control select2me serviskod serviskod'+count+'" id="serviskod'+count+'" name="serviskodlari[]" tabindex="-1" title="">'+
            '<option value="">Seçiniz...</option>'+
            @foreach($servisstokkodlari as $servisstokkod)
            '<option value="{{ $servisstokkod->id }}">{{ $servisstokkod->stokkodu.' '.$servisstokkod->stokadi }}</option>'+
            @endforeach
            '</select></div></div>'+
            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Adı:<span class="required" aria-required="true"> * </span></label><div class="input-icon right col-xs-8">' +
            '<i class="fa"></i><select class="form-control select2me valid'+count+' sayacadi sayacadi'+count+'" id="sayacadi'+count+'" name="sayacadlari['+count+']" tabindex="-1" title="">'+
            '<option value="">Seçiniz...</option>'+
            @foreach($sayacadlari as $sayacadi)
            '<option data-id="{{ $sayacadi->cap }}" value="{{ $sayacadi->id }}" >{{ $sayacadi->sayacadi }}</option>'+
            @endforeach
            '</select></div></div><div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Çapı:<span class="required" aria-required="true"> * </span></label>' +
            '<div class="input-icon right col-xs-8"><i class="fa"></i><select class="form-control select2me valid'+count+' sayaccap sayaccap'+count+'" id="sayaccap'+count+'" name="sayaccap['+count+']" tabindex="-1" title="">'+
            '<option value="">Seçiniz...</option>'+
            @foreach($sayaccaplari as $sayaccapi)
            '<option value="{{ $sayaccapi->id }}">{{ $sayaccapi->capadi }}</option>'+
            @endforeach
            '</select><input type="text" id="sayaccaplari'+count+'" name="sayaccaplari[]" class="sayaccaplari hide" value="1"></div></div>'+
            '<div class="form-group col-xs-12"><label class="col-sm-2 col-xs-4 control-label">Sökülme Nedeni:</label><div class="col-sm-10 col-xs-8">' +
            '<input type="text" id="neden'+count+'" name="neden[]" maxlength="200" class="form-control neden">'+
            '</div></div>'+
            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Servis Sayacı Takılma Tarihi:</label><div class="col-xs-8">' +
            '<div class="input-group input-medium date date-picker takilmatarihi" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">'+
            '<input type="text" id="takilmatarihi'+count+'" name="takilmatarihi[]" class="form-control" value="">'+
            '<span class="input-group-btn"><button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button></span>'+
            '</div></div></div><div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Servis Sayacı Takılma Endeksi:</label><div class="col-xs-8">' +
            '<input type="text" id="endeks'+count+'" name="endeks[]" class="form-control endeks">'+
            '</div></div>'+
            '</div></div></div>';
            count++;
            $('.count').html(count+' Adet');
            $('.sayaclar').append(newRow);
            $('select.valid'+(count-1)).each(function(){
                $(this).rules('remove');
                $(this).rules('add', {
                    required: true
                });
            });
            $('input.valid'+(count-1)).each(function(){
                $(this).rules('remove');
                $(this).rules('add', {
                    required: true
                });
            });
            if(cariadi!==""){
                $.blockUI();
                $.getJSON("{{ URL::to('sube/cariyer') }}",{netsiscariid:cariadi}, function (event) {
                    if(event.durum){
                        var uretimyeri = event.uretimyer;
                        var uretimyer =$("#uretimyer"+(count-1));
                        uretimyer.empty();
                        uretimyer.append('<option value="">Seçiniz...</option>');
                        $.each(uretimyeri, function (index) {
                            uretimyer.append('<option value="' + uretimyeri[index].id + '"> ' + uretimyeri[index].yeradi + '</option>');
                        });
                        uretimyer.select2("val", "");
                    }else{
                        toastr[event.type](event.text,event.title);
                    }
                    $.unblockUI();
                });
            }
            $("#uretimyer"+(count-1)).select2();
            $("#serviskod"+(count-1)).select2();
            $("#sayacadi"+(count-1)).select2();
            $("#sayaccap"+(count-1)).select2();
            $("#sayaccaplari"+(count-1)).val("");
            $('.sayacadi').on('change', function () {
                var id = $(this).val();
                var no = $(this).closest('.sayaclar_ek').children('.no').val();
                if(id!==""){
                    $.blockUI();
                    var capdurum = $(this).find("option:selected").data('id');
                    if (capdurum === 0) //cap kontrol edilmiyor
                    {
                        $("#sayaccap"+(no)).select2("val",1).trigger('change');
                        $("#sayaccaplari"+(no)).val(1);
                        $(".sayaccap"+(no)).prop("disabled", true);
                        $.getJSON("{{ URL::to('sube/serviskod') }}",{sayacadiid:id}, function (event) {
                            if(event.durum){
                                var serviskod = event.serviskod;
                                $("#serviskod" + (no)).select2("val", serviskod);
                            }else {
                                $("#serviskod" + (no)).select2("val", '');
                                toastr[event.type](event.text, event.title);
                            }
                            $.unblockUI();
                        });
                    } else {
                        $("#sayaccap"+(no)).select2("val","").trigger('change');
                        $("#sayaccaplari"+(no)).val("");
                        $(".sayaccap"+(no)).prop("disabled", false);
                        $("#serviskod" + (no)).select2("val", '');
                        $.unblockUI();
                    }
                }else{
                    $("#sayaccap"+(no)).select2("val","").trigger('change');
                    $("#sayaccaplari"+(no)).val("");
                    $(".sayaccap"+(no)).prop("disabled", true);
                    $("#serviskod"+(no)).select2("val", '');
                }
                $(this).valid();
            });
            $('.sayaccap').on('change', function () {
                var id = $(this).val();
                var no = $(this).closest('.sayaclar_ek').children('.no').val();
                $("#sayaccaplari"+(no)).val(id);
                var sayacadi = $('#sayacadi'+(no)).val();
                if(id!=="" && sayacadi!==""){
                    $.blockUI();
                    $.getJSON("{{ URL::to('sube/serviskod') }}",{sayacadiid:sayacadi,sayaccapid:id}, function (event) {
                        if(event.durum){
                            var serviskod = event.serviskod;
                            $("#serviskod"+(no)).select2('val', serviskod);
                        }else{
                            $("#serviskod" + (no)).select2('val', '');
                            toastr[event.type](event.text, event.title);
                        }
                        $.unblockUI();
                    });
                }else{
                    $("#serviskod"+(no)).select2('val', '');
                }
                $(this).valid();
            });
            $('.uretimyer').on('change', function () {
                $(this).valid();
            });
            $('.satirsil').click(function() {
                if ($('.sayaclar .sayaclar_ek').size() > 0) {
                    var sayac = $(this).closest('.sayaclar_ek');
                    var adet = sayac.children('.adet').val();
                    sayac.children('.adet').val(0);
                    sayac.remove();
                    count -= adet;
                    $("#count").val(count);
                    $('.count').html(count + ' Adet');
                    var j = 0;
                    $('.sayaclar .sayaclar_ek').each(function () {
                        var id = $(this).children('.no').val();
                        $(this).children('div').children('h4').children('.accordion-toggle').attr('href', '#collapse_' + j);
                        $(this).children('.panel-collapse').attr('id', 'collapse_' + j);
                        $(this).children('div').children('div').children('div').children('div').children('.serino').attr('id', 'serino' + j).attr('name', 'serino['+j+']');
                        $(this).children('div').children('div').children('div').children('div').children('.uretimyer').removeClass('uretimyer' + id).addClass('uretimyer' + j);
                        $(this).children('div').children('div').children('div').children('div').children('.uretimyer').attr('id', 'uretimyer' + j).attr('name', 'uretimyerleri['+j+']');
                        $(this).children('div').children('div').children('div').children('div').children('.serviskod').removeClass('serviskod' + id).addClass('serviskod' + j);
                        $(this).children('div').children('div').children('div').children('div').children('.serviskod').attr('id', 'serviskod' + j).attr('name', 'serviskodlari[]');
                        $(this).children('div').children('div').children('div').children('div').children('.sayacadi').attr('id', 'sayacadi' + j).attr('name', 'sayacadlari['+j+']');
                        $(this).children('div').children('div').children('div').children('div').children('.sayacadi').removeClass('sayacadi' + id).addClass('sayacadi' + j);
                        $(this).children('div').children('div').children('div').children('div').children('.sayaccap').attr('id', 'sayaccap' + j).attr('name', 'sayaccap['+j+']');
                        $(this).children('div').children('div').children('div').children('div').children('.sayaccap').removeClass('sayaccap' + id).addClass('sayaccap' + j);
                        $(this).children('div').children('div').children('div').children('div').children('.sayaccaplari').attr('id', 'sayaccaplari' + j).attr('name', 'sayaccaplari[]');
                        $(this).children('div').children('div').children('div').children('div').children('.neden').attr('id', 'neden' + j).attr('name', 'neden[]');
                        $(this).children('div').children('div').children('div').children('div').children('.takilmatarihi').attr('id', 'takilmatarihi' + j).attr('name', 'takilmatarihi[]');
                        $(this).children('div').children('div').children('div').children('div').children('.endeks').attr('id', 'endeks' + j).attr('name', 'endeks[]');
                        $(this).children('.no').val(j);
                        j++;
                    });
                    flag = 0;
                    $('input[name^="serino"]').css("background-color", "#FFFFFF");
                    $('input[name^="serino"]').each(function (i, el1) {
                        var current_val = jQuery(el1).val();
                        if (current_val !== "") {
                            $('input[name^="serino"]').each(function (i, el2) {
                                if (jQuery(el2).val() === current_val && jQuery(el1).attr("id") !== jQuery(el2).attr("id")) {
                                    jQuery(el2).css("background-color", "yellow");
                                    jQuery(el1).css("background-color", "yellow");
                                    flag = 1;
                                }
                            });
                        }
                    });
                    if (count > 0) {
                        if (flag === 0)
                            $(".kaydet").prop('disabled', false);
                        else
                            $(".kaydet").prop('disabled', true);
                    } else {
                        $(".kaydet").prop('disabled', true);
                    }
                }
            });
            $(".serino").inputmask("mask", {
                mask:"9",repeat:15,greedy:!1
            });
            $(".endeks").inputmask("decimal",{
                radixPoint:",",
                groupSeparator: "",
                digits: 3,
                autoGroup: true
            });
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "left",
                autoclose: true,
                language: 'tr'
            });
            $("#count").val(count);
            $('input[name^="serino"]').change(function () {
                $(".kaydet").prop('disabled', false);
                $('input[name^="serino"]').css("background-color", "#FFFFFF");
                $('input[name^="serino"]').each(function (i,el1) {
                    var current_val = jQuery(el1).val();
                    if (current_val !== "") {
                        $('input[name^="serino"]').each(function (i,el2) {
                            if (jQuery(el2).val() === current_val && jQuery(el1).attr("id") !== jQuery(el2).attr("id")) {
                                jQuery(el2).css("background-color", "yellow");
                                jQuery(el1).css("background-color", "yellow");
                                $(".kaydet").prop('disabled', true);
                            }
                        });
                    }
                });
            });
            if (count > 0){
                $(".kaydet").prop('disabled', false);
            }else{
                $(".kaydet").prop('disabled', true);
            }
            $('select').on("select2-close", function () { $(this).valid(); });
        });
        $('#cokluekle').click(function(){
            var adet=parseInt($("#adet").val());  // eklenecek sayı
            count=parseInt($("#count").val()); //ekli olan sayı
            var sayacadi = $('#sayacadi').select2('val');
            var sayaccap = $('#sayaccap').select2('val');
            var uretimyer = $('#uretimyer').select2('val');
            var serviskod = $('#istek').select2('val');
            var sayaccapdurum = $('#sayacadi').select2().find(":selected").data("id");
            var cariadi = $('#cariadi').val();
            while( adet>0 ) {
                var newRow = "";
                var sayi = 0;
                while (adet > 0 && sayi < 100) {
                    newRow += '<div class="panel panel-default sayaclar_ek"><input class="no hide" value="'+(count)+'"/><input class="adet hide" value="1"/><div class="panel-heading"><h4 class="panel-title">' +
                        '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_'+count+'">Yeni</a></h4></div>' +
                        '<div id="collapse_' + count + '" class="panel-collapse in"><div class="panel-body"><div class="form-group col-sm-6 col-xs-12">' +
                        '<label class="col-xs-4 col-sm-4 control-label">Seri No:<span class="required" aria-required="true"> * </span></label>' +
                        '<div class="input-icon right col-xs-6 col-sm-8"><i class="fa"></i><input type="text" id="serino'+count+'" name="serino['+count+']" maxlength="15" class="form-control valid'+count+' serino"></div>' +
                        '<label class="col-xs-1 hidden-sm hidden-md hidden-lg"><a class="btn red satirsil">Sil</a></label></div><div class="form-group col-sm-6 col-xs-12">' +
                        '<label class="col-xs-4 col-sm-4 control-label">Geliş Yeri:<span class="required" aria-required="true"> * </span></label>' +
                        '<div class="input-icon right col-xs-8 col-sm-6"><i class="fa"></i><select class="form-control select2me valid'+count+' uretimyer uretimyer'+count+'" id="uretimyer'+count+'" name="uretimyerleri['+count+']" tabindex="-1" title="">' +
                        '<option value="">Seçiniz...</option>' +
                            @foreach($uretimyerleri as $uretimyer)
                                '<option value="{{ $uretimyer->id }}">{{ $uretimyer->yeradi }}</option>' +
                            @endforeach
                                '</select></div>' +
                        '<label class="col-xs-1 hidden-xs"><a class="btn red satirsil">Sil</a></label></div>' +
                        '<div class="form-group col-xs-12"><label class="col-sm-2 col-xs-4 control-label">İstek:</label><div class="col-sm-10 col-xs-8"><select class="form-control select2me serviskod serviskod' + count + '" id="serviskod' + count + '" name="serviskodlari[]" tabindex="-1" title="">' +
                        '<option value="">Seçiniz...</option>' +
                            @foreach($servisstokkodlari as $servisstokkod)
                                '<option value="{{ $servisstokkod->id }}">{{ $servisstokkod->stokkodu.' '.$servisstokkod->stokadi }}</option>' +
                            @endforeach
                                '</select></div></div>' +
                        '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Adı:<span class="required" aria-required="true"> * </span></label><div class="input-icon right col-xs-8">' +
                        '<i class="fa"></i><select class="form-control select2me valid'+count+' sayacadi sayacadi'+count+'" id="sayacadi'+count+'" name="sayacadlari['+count+']" tabindex="-1" title="">' +
                        '<option value="">Seçiniz...</option>' +
                            @foreach($sayacadlari as $sayacadi)
                                '<option data-id="{{ $sayacadi->cap }}" value="{{ $sayacadi->id }}" >{{ $sayacadi->sayacadi }}</option>' +
                            @endforeach
                                '</select></div></div><div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Çapı:<span class="required" aria-required="true"> * </span></label>' +
                        '<div class="input-icon right col-xs-8"><i class="fa"></i><select class="form-control select2me valid'+count+' sayaccap sayaccap'+count+'" id="sayaccap'+count+'" name="sayaccap['+count+']" tabindex="-1" title="">' +
                        '<option value="">Seçiniz...</option>' +
                            @foreach($sayaccaplari as $sayaccapi)
                                '<option value="{{ $sayaccapi->id }}">{{ $sayaccapi->capadi }}</option>' +
                            @endforeach
                                '</select><input type="text" id="sayaccaplari'+count+'" name="sayaccaplari[]" class="sayaccaplari hide" value="1"></div></div>' +
                        '<div class="form-group col-xs-12"><label class="col-sm-2 col-xs-4 control-label">Sökülme Nedeni:</label><div class="col-sm-10 col-xs-8">' +
                        '<input type="text" id="neden' + count + '" name="neden[]" maxlength="200" class="form-control neden">' +
                        '</div></div>' +
                        '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Servis Sayacı Takılma Endeksi:</label><div class="col-xs-8">' +
                        '<input type="text" id="endeks' + count + '" name="endeks[]" class="form-control endeks">' +
                        '</div></div>' +
                        '</div></div></div>';
                    adet--;
                    count++;
                    sayi++;
                }
                $('.count').html(count + ' Adet');
                $('.sayaclar').append(newRow);
                const ilksayi = count - sayi;
                const sonsayi = count;
                if (cariadi !== "") {
                    $.blockUI();
                    $.getJSON("{{ URL::to('sube/cariyer') }}", {netsiscariid: cariadi}, function (event) {
                        if (event.durum) {
                            var uretimyerlist = event.uretimyer;
                            for (var i = ilksayi; i < sonsayi; i++) {
                                var uretimyeri = $("#uretimyer" + (ilksayi + i));
                                uretimyeri.empty();
                                uretimyeri.append('<option value="">Seçiniz...</option>');
                                $.each(uretimyerlist, function (index) {
                                    uretimyeri.append('<option value="' + uretimyerlist[index].id + '"> ' + uretimyerlist[index].yeradi + '</option>');
                                });
                                uretimyeri.select2("val", uretimyer);
                            }
                        } else {
                            toastr[event.type](event.text, event.title);
                        }
                        $.unblockUI();
                    });
                }
                $(".serino").inputmask("mask", {
                    mask: "9", repeat: 15, greedy: !1
                });
                $(".endeks").inputmask("decimal", {
                    radixPoint: ",",
                    groupSeparator: "",
                    digits: 3,
                    autoGroup: true
                });

                $('.sayacadi').on('change', function () {
                    var id = $(this).val();
                    var no = $(this).closest('.sayaclar_ek').children('.no').val();
                    if (id !== "") {
                        $.blockUI();
                        var capdurum = $(this).find("option:selected").data('id');
                        if (capdurum === 0) //cap kontrol edilmiyor
                        {
                            $("#sayaccap" + (no)).select2("val", 1).trigger('change');
                            $("#sayaccaplari" + (no)).val(1);
                            $(".sayaccap" + (no)).prop("disabled", true);
                            $.getJSON("{{ URL::to('sube/serviskod') }}", {sayacadiid: id}, function (event) {
                                if (event.durum) {
                                    var serviskod = event.serviskod;
                                    $("#serviskod" + (no)).select2('val', serviskod);
                                } else {
                                    $("#serviskod" + (no)).select2('val', '');
                                    toastr[event.type](event.text, event.title);
                                }
                                $.unblockUI();
                            });
                        } else {
                            $("#sayaccap" + (no)).select2("val", "").trigger('change');
                            $("#sayaccaplari" + (no)).val("");
                            $(".sayaccap" + (no)).prop("disabled", false);
                            $("#serviskod" + (no)).select2("val", '');
                            $.unblockUI();
                        }
                    } else {
                        $("#sayaccap" + (no)).select2("val", "").trigger('change');
                        $("#sayaccaplari" + (no)).val("");
                        $(".sayaccap" + (no)).prop("disabled", true);
                        $("#serviskod" + (no)).select2("val", '');
                    }
                    $(this).valid();
                });
                $('.sayaccap').on('change', function () {
                    var id = $(this).val();
                    var no = $(this).closest('.sayaclar_ek').children('.no').val();
                    $("#sayaccaplari" + (no)).val(id);
                    var sayacadi = $('#sayacadi' + (no)).val();
                    if (id !== "" && sayacadi !== "") {
                        $.blockUI();
                        $.getJSON("{{ URL::to('sube/serviskod') }}", {
                            sayacadiid: sayacadi,
                            sayaccapid: id
                        }, function (event) {
                            if (event.durum) {
                                var serviskod = event.serviskod;
                                $("#serviskod" + (no)).select2('val', serviskod);
                            } else {
                                toastr[event.type](event.text, event.title);
                            }
                            $.unblockUI();
                        });
                    } else {
                        $("#serviskod" + (no)).select2('val', '');
                    }
                    $(this).valid();
                });
                $('.uretimyer').on('change', function () {
                    $(this).valid();
                });
                $('.satirsil').click(function () {
                    if ($('.sayaclar .sayaclar_ek').size() > 0) {
                        var sayac = $(this).closest('.sayaclar_ek');
                        var adet = sayac.children('.adet').val();
                        sayac.children('.adet').val(0);
                        sayac.remove();
                        count -= adet;
                        $("#count").val(count);
                        $('.count').html(count + ' Adet');
                        var j = 0;
                        $('.sayaclar .sayaclar_ek').each(function () {
                            var id = $(this).children('.no').val();
                            $(this).children('div').children('h4').children('.accordion-toggle').attr('href', '#collapse_' + j);
                            $(this).children('.panel-collapse').attr('id', 'collapse_' + j);
                            $(this).children('div').children('div').children('div').children('div').children('.serino').attr('id', 'serino' + j).attr('name', 'serino['+j+']');
                            $(this).children('div').children('div').children('div').children('div').children('.uretimyer').removeClass('uretimyer' + id).addClass('uretimyer' + j);
                            $(this).children('div').children('div').children('div').children('div').children('.uretimyer').attr('id', 'uretimyer' + j).attr('name', 'uretimyerleri['+j+']');
                            $(this).children('div').children('div').children('div').children('div').children('.serviskod').attr('id', 'serviskod' + j).attr('name', 'serviskodlari[]');
                            $(this).children('div').children('div').children('div').children('div').children('.serviskod').removeClass('serviskod' + id).addClass('serviskod' + j);
                            $(this).children('div').children('div').children('div').children('div').children('.sayacadi').attr('id', 'sayacadi' + j).attr('name', 'sayacadlari['+j+']');
                            $(this).children('div').children('div').children('div').children('div').children('.sayacadi').removeClass('sayacadi' + id).addClass('sayacadi' + j);
                            $(this).children('div').children('div').children('div').children('div').children('.sayaccap').attr('id', 'sayaccap' + j).attr('name', 'sayaccap['+j+']');
                            $(this).children('div').children('div').children('div').children('div').children('.sayaccap').removeClass('sayaccap' + id).addClass('sayaccap' + j);
                            $(this).children('div').children('div').children('div').children('div').children('.sayaccaplari').attr('id', 'sayaccaplari' + j).attr('name', 'sayaccaplari[]');
                            $(this).children('div').children('div').children('div').children('div').children('.neden').attr('id', 'neden' + j).attr('name', 'neden[]');
                            $(this).children('div').children('div').children('div').children('div').children('.endeks').attr('id', 'endeks' + j).attr('name', 'endeks[]');
                            $(this).children('.no').val(j);
                            j++;
                        });
                        flag = 0;
                        $('input[name^="serino"]').css("background-color", "#FFFFFF");
                        $('input[name^="serino"]').each(function (i, el1) {
                            var current_val = jQuery(el1).val();
                            if (current_val !== "") {
                                $('input[name^="serino"]').each(function (i, el2) {
                                    if (jQuery(el2).val() === current_val && jQuery(el1).attr("id") !== jQuery(el2).attr("id")) {
                                        jQuery(el2).css("background-color", "yellow");
                                        jQuery(el1).css("background-color", "yellow");
                                        flag = 1;
                                    }
                                });
                            }
                        });
                        if (count > 0) {
                            if (flag === 0)
                                $(".kaydet").prop('disabled', false);
                            else
                                $(".kaydet").prop('disabled', true);
                        } else {
                            $(".kaydet").prop('disabled', true);
                        }
                    }
                });
                for (var i = ilksayi; i < sonsayi; i++) {
                    $('select.valid'+(i)).each(function(){
                        $(this).rules('remove');
                        $(this).rules('add', {
                            required: true
                        });
                    });
                    $('input.valid'+(i)).each(function(){
                        $(this).rules('remove');
                        $(this).rules('add', {
                            required: true
                        });
                    });
                    $("#uretimyer" + (i)).select2();
                    $("#serviskod" + (i)).select2();
                    $("#sayacadi" + (i)).select2();
                    $("#sayaccap" + (i)).select2();
                    $("#uretimyer" + (i)).select2("val", uretimyer).trigger('change');
                    $("#serviskod" + (i)).select2("val", serviskod).trigger('change');
                    $("#sayacadi" + (i)).select2("val", sayacadi).trigger('change');
                    $("#sayaccap" + (i)).select2("val", sayaccap).trigger('change');
                    $("#sayaccaplari" + (i)).val(sayaccap);
                    if (sayaccapdurum === 0) //cap kontrol edilmiyor
                    {
                        $(".sayaccap" + (i)).prop("disabled", true);
                    } else {
                        $(".sayaccap" + (i)).prop("disabled", false);
                    }
                }
                $("#count").val(count);
                $('input[name^="serino"]').change(function () {
                    $(".kaydet").prop('disabled', false);
                    $('input[name^="serino"]').css("background-color", "#FFFFFF");
                    $('input[name^="serino"]').each(function (i, el1) {
                        var current_val = jQuery(el1).val();
                        if (current_val !== "") {
                            $('input[name^="serino"]').each(function (i, el2) {
                                if (jQuery(el2).val() === current_val && jQuery(el1).attr("id") !== jQuery(el2).attr("id")) {
                                    jQuery(el2).css("background-color", "yellow");
                                    jQuery(el1).css("background-color", "yellow");
                                    $(".kaydet").prop('disabled', true);
                                }
                            });
                        }
                    });
                });
                if (count > 0) {
                    $(".kaydet").prop('disabled', false);
                } else {
                    $(".kaydet").prop('disabled', true);
                }
                $("select").on("select2-close", function () { $(this).valid(); });
            }
        });
        $('input[name^="serino"]').css("background-color", "#FFFFFF");
        $('input[name^="serino"]').each(function (i, el1) {
            var current_val = jQuery(el1).val();
            if (current_val !== "") {
                $('input[name^="serino"]').each(function (i, el2) {
                    if (jQuery(el2).val() === current_val && jQuery(el1).attr("id") !== jQuery(el2).attr("id")) {
                        jQuery(el2).css("background-color", "yellow");
                        jQuery(el1).css("background-color", "yellow");
                        flag=1;
                    }
                });
            }
        });
        $(".tumsil").click(function(){
            while($('.sayaclar .sayaclar_ek').size()>0){
                $('.sayaclar .sayaclar_ek:last').remove();
                count--;
            }
            $("#count").val(0);
            $('.count').html(0+' Adet');
            $(".kaydet").prop('disabled',true);
        });
        var capdurum;
        for (i = 0; i < count; i++) {
            capdurum = $('.sayacadi' + i).find("option:selected").data('id');
            if (capdurum === 0) //cap kontrol edilmiyor
            {
                $(".sayaccap"+i).prop("disabled", true);
            } else {
                $(".sayaccap"+i).prop("disabled", false);
            }
            $('select.valid'+i).each(function(){
                $(this).rules('remove');
                $(this).rules('add', {
                    required: true
                });
            });
            $('input.valid'+i).each(function(){
                $(this).rules('remove');
                $(this).rules('add', {
                    required: true
                });
            });
        }
        $('.sayacadi').on('change', function () {
            var id = $(this).val();
            var no = $(this).closest('.sayaclar_ek').children('.no').val();
            if(id!==""){
                $.blockUI();
                var capdurum = $(this).find("option:selected").data('id');
                if (capdurum === 0) //cap kontrol edilmiyor
                {
                    $("#sayaccap"+(no)).select2("val",1).trigger('change');
                    $("#sayaccaplari"+(no)).val(1);
                    $(".sayaccap"+(no)).prop("disabled", true);
                    $.getJSON("{{ URL::to('sube/serviskod') }}",{sayacadiid:id}, function (event) {
                        if(event.durum){
                            var serviskod = event.serviskod;
                            $("#serviskod" + (no)).select2("val", serviskod);
                        }else {
                            $("#serviskod" + (no)).select2("val", '');
                            toastr[event.type](event.text, event.title);
                        }
                        $.unblockUI();
                    });
                } else {
                    $("#sayaccap"+(no)).select2("val","").trigger('change');
                    $("#sayaccaplari"+(no)).val("");
                    $(".sayaccap"+(no)).prop("disabled", false);
                    $.unblockUI();
                }
            }else{
                $("#sayaccap"+(no)).select2("val","").trigger('change');
                $("#sayaccaplari"+(no)).val("");
                $(".sayaccap"+(no)).prop("disabled", true);
                $("#serviskod" + (no)).select2("val", '');
            }
            $(this).valid();
        });
        $('.sayaccap').on('change', function () {
            var id = $(this).val();
            var no = $(this).closest('.sayaclar_ek').children('.no').val();
            $("#sayaccaplari"+(no)).val(id);
            var sayacadi = $('#sayacadi'+(no)).val();
            if(id!=="" && sayacadi!==""){
                $.blockUI();
                $.getJSON("{{ URL::to('sube/serviskod') }}",{sayacadiid:sayacadi,sayaccapid:id}, function (event) {
                    if(event.durum){
                        var serviskod = event.serviskod;
                        $("#serviskod" + (no)).select2("val", serviskod);
                    }else{
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            }else{
                $("#serviskod" + (no)).select2("val", '');
            }
            $(this).valid();
        });
        $('.uretimyer').on('change', function () {
            $(this).valid();
        });
        $('.satirsil').click(function(){
            if($('.sayaclar .sayaclar_ek').size()>0){
                var sayac=$(this).closest('.sayaclar_ek');
                var adet = sayac.children('.adet').val();
                sayac.children('.adet').val(0);
                sayac.remove();
                count-=adet;
                $("#count").val(count);
                $('.count').html(count+' Adet');
                var j=0;
                $('.sayaclar .sayaclar_ek').each(function(){
                    var id=$( this ).children('.no').val();
                    $( this).children('div').children('h4').children('.accordion-toggle').attr('href','#collapse_'+j);
                    $( this).children('.panel-collapse').attr('id','collapse_'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.serino').attr('id','serino'+j).attr('name','serino['+j+']');
                    $( this).children('div').children('div').children('div').children('div').children('.uretimyer').removeClass('uretimyer'+id).addClass('uretimyer'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.uretimyer').attr('id','uretimyer'+j).attr('name','uretimyerleri['+j+']');
                    $( this).children('div').children('div').children('div').children('div').children('.serviskod').attr('id','serviskod'+j).attr('name','serviskodlari[]');
                    $( this).children('div').children('div').children('div').children('div').children('.serviskod').removeClass('serviskod'+id).addClass('serviskod'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.sayacadi').attr('id','sayacadi'+j).attr('name','sayacadlari['+j+']');
                    $( this).children('div').children('div').children('div').children('div').children('.sayacadi').removeClass('sayacadi'+id).addClass('sayacadi'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.sayaccap').attr('id','sayaccap'+j).attr('name','sayaccap['+j+']');
                    $( this).children('div').children('div').children('div').children('div').children('.sayaccap').removeClass('sayaccap'+id).addClass('sayaccap'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.sayaccaplari').attr('id', 'sayaccaplari' + j).attr('name', 'sayaccaplari[]');
                    $( this).children('div').children('div').children('div').children('div').children('.neden').attr('id','neden'+j).attr('name','neden[]');
                    $( this).children('div').children('div').children('div').children('div').children('.endeks').attr('id','endeks'+j).attr('name','endeks[]');
                    $( this ).children('.no').val(j);
                    j++;
                });
                flag=0;
                $('input[name^="serino"]').css("background-color", "#FFFFFF");
                $('input[name^="serino"]').each(function (i,el1) {
                    var current_val = jQuery(el1).val();
                    if (current_val !== "") {
                        $('input[name^="serino"]').each(function (i,el2) {
                            if (jQuery(el2).val() === current_val && jQuery(el1).attr("id") !== jQuery(el2).attr("id")) {
                                jQuery(el2).css("background-color", "yellow");
                                jQuery(el1).css("background-color", "yellow");
                                flag=1;
                            }
                        });
                    }
                });
                if (count > 0){
                    if(flag===0)
                        $(".kaydet").prop('disabled', false);
                    else
                        $(".kaydet").prop('disabled', true);
                }else{
                    $(".kaydet").prop('disabled', true);
                }
            }
        });
        $('#cariadi').on('change', function () {
            var id = $(this).val();
            if(id!=="") {
                $.blockUI();
                $.getJSON("{{ URL::to('sube/cariyer') }}",{netsiscariid:id}, function (event) {
                    var i,uretimyer;
                    if (event.durum) {
                        var uretimyeri = event.uretimyer;
                        for (i = 0; i < count; i++) {
                            uretimyer = $("#uretimyer" + i);
                            uretimyer.empty();
                            uretimyer.append('<option value="">Seçiniz...</option>');
                            $.each(uretimyeri, function (index) {
                                uretimyer.append('<option value="' + uretimyeri[index].id + '"> ' + uretimyeri[index].yeradi + '</option>');
                            });
                            uretimyer.select2("val", "").trigger('change');
                        }
                        uretimyer = $("#uretimyer");
                        uretimyer.empty();
                        uretimyer.append('<option value="">Seçiniz...</option>');
                        $.each(uretimyeri, function (index) {
                            uretimyer.append('<option value="' + uretimyeri[index].id + '"> ' + uretimyeri[index].yeradi + '</option>');
                        });
                        uretimyer.select2("val", "");
                    } else {
                        toastr[event.type](event.text, event.title);
                        for (i = 0; i < count; i++) {
                            uretimyer = $("#uretimyer" + i);
                            uretimyer.empty();
                            uretimyer.select2("val", "").trigger('change');
                        }
                        uretimyer = $("#uretimyer");
                        uretimyer.empty();
                        uretimyer.select2("val", "");
                    }
                    $.unblockUI();
                });
            }else{
                for (var i = 0; i < count; i++) {
                    var uretimyer = $("#uretimyer" + i);
                    uretimyer.empty();
                    uretimyer.select2("val", "").trigger('change');
                }
                uretimyer = $("#uretimyer");
                uretimyer.empty();
                uretimyer.select2("val", "");
            }
        });
        var cariadi=$('#cariadi').val();
        if(cariadi!==""){
            $.blockUI();
            $.getJSON("{{ URL::to('sube/cariyer') }}",{netsiscariid:cariadi}, function (event) {
                if(event.durum){
                    var uretimyeri = event.uretimyer;
                    for (var i = 0; i < count; i++){
                        var uretimyer =$("#uretimyer"+i);
                        var selected=uretimyer.val();
                        uretimyer.empty();
                        uretimyer.append('<option value="">Seçiniz...</option>');
                        $.each(uretimyeri, function (index) {
                            uretimyer.append('<option value="' + uretimyeri[index].id + '"> ' + uretimyeri[index].yeradi + '</option>');
                        });
                        uretimyer.select2("val", selected).trigger('change');
                    }
                    uretimyer =$("#uretimyer");
                    uretimyer.empty();
                    uretimyer.append('<option value="">Seçiniz...</option>');
                    $.each(uretimyeri, function (index) {
                        uretimyer.append('<option value="' + uretimyeri[index].id + '"> ' + uretimyeri[index].yeradi + '</option>');
                    });
                    uretimyer.select2("val", "");
                    $('#form_sample').valid();
                }else{
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
        }else{
            for (var i = 0; i < count; i++) {
                var uretimyer = $("#uretimyer" + i);
                uretimyer.empty();
                uretimyer.select2("val", "");
            }
            uretimyer = $("#uretimyer");
            uretimyer.empty();
            uretimyer.select2("val", "");
        }
        $('#sayacadi').on('change', function () {
            var id = $(this).val();
            if(id!==""){
                $.blockUI();
                var capdurum = $(this).find("option:selected").data('id');
                if (capdurum === 0) //cap kontrol edilmiyor
                {
                    $("#sayaccap").select2("val",1);
                    $("#sayaccap").prop("disabled", true);
                    $.getJSON("{{ URL::to('sube/serviskod') }}",{sayacadiid:id}, function (event) {
                        if(event.durum){
                            var serviskod = event.serviskod;
                            $("#istek").select2('val',serviskod);
                        }else {
                            $('#cokluekleme').modal('hide');
                            toastr[event.type](event.text, event.title);
                        }
                        $.unblockUI();
                    });
                } else {
                    $("#sayaccap").select2("val","");
                    $("#sayaccap").prop("disabled", false);
                    $.unblockUI();
                }
            }else{
                $("#sayaccap").select2("val","");
                $("#sayaccap").prop("disabled", true);
                $("#istek").select2('val','');
            }
        });
        $('#sayaccap').on('change', function () {
            var id = $(this).val();
            var sayacadi = $('#sayacadi').val();
            if(id!=="" && sayacadi!==""){
                $.blockUI();
                $.getJSON("{{ URL::to('sube/serviskod') }}",{sayacadiid:sayacadi,sayaccapid:id}, function (event) {
                    if(event.durum){
                        var serviskod = event.serviskod;
                        $("#istek").select2('val',serviskod);
                    }else {
                        $('#cokluekleme').modal('hide');
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            }else{
                $("#istek").select2('val','');
            }
        });
        $('input[name^="serino"]').change(function () {
            $(".kaydet").prop('disabled', false);
            $('input[name^="serino"]').css("background-color", "#FFFFFF");
            $('input[name^="serino"]').each(function (i, el1) {
                var current_val = jQuery(el1).val();
                if (current_val !== "") {
                    $('input[name^="serino"]').each(function (i, el2) {
                        if (jQuery(el2).val() === current_val && jQuery(el1).attr("id") !== jQuery(el2).attr("id")) {
                            jQuery(el2).css("background-color", "yellow");
                            jQuery(el1).css("background-color", "yellow");
                            $(".kaydet").prop('disabled', true);
                        }
                    });
                }
            });
        });
        $(".serino").inputmask("mask", {
            mask:"9",repeat:15,greedy:!1
        });
        $(".endeks").inputmask("decimal",{
            radixPoint:",",
            groupSeparator: "",
            digits: 3,
            autoGroup: true
        });
        if (count > 0) {
            if (flag === 0)
                $(".kaydet").prop('disabled', false);
            else
                $(".kaydet").prop('disabled', true);
        }else{
            $(".kaydet").prop('disabled', true);
        }
        $('.belgeli').on('change', function () {
            if ($(this).prop('checked')) {
                $('#belgeno').removeAttr('disabled');
            } else {
                $('#belgeno').attr('disabled', true);
            }
        });
        $('#formsubmit').click(function () {
            $('#form_sample').submit();
        });
        $("select").on("select2-close", function () { $(this).valid(); });
        $('#gelis').on('change', function() { $(this).valid(); });
    });
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Sayac Kayıdı Ekle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('sube/sayackayitekle') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
            <div class="form-body">
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    Girilen Bilgilerde Hata Var.
                </div>
                <div class="alert alert-success display-hide">
                    <button class="close" data-close="alert"></button>
                    Bilgiler Doğru!
                </div>
                <input class="hide" id="subekodu" name="subekodu" value="{{$sube ? $sube->subekodu : 1}}">
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4"><input type="checkbox" class="belgeli" id="belgeli" name="belgeli" @if(Input::old('belgeli')) checked="" @endif />Belgeli mi?</label>
                    <div class="col-xs-8 belgeno">
                        <input type="text" id="belgeno" name="belgeno" @if(!Input::old('belgeli')) disabled="" @endif value="{{Input::old('belgeno') ? Input::old('belgeno') : '000000000000000'}}" maxlength="15" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Geliş Tarihi: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><div class="input-group input-medium date date-picker" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                            <input id="gelis" type="text" name="gelis" class="form-control" value="{{Input::old('gelis') ? Input::old('gelis') : '' }}">
                            <span class="input-group-btn"><button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button></span>
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4">Cari İsim:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-10 col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="cariadi" name="cariadi" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @if(Input::old('cariadi')==$sube->netsiscari_id )
                                <option value="{{ $sube->netsiscari_id }}" selected>{{ $sube->netsiscari->carikod.' - '.$sube->netsiscari->cariadi }}</option>
                            @else
                                <option value="{{ $sube->netsiscari_id }}">{{ $sube->netsiscari->carikod.' - '. $sube->netsiscari->cariadi }}</option>
                            @endif
                            @foreach($netsiscariler as $netsiscari)
                                @if(Input::old('cariadi')==$netsiscari->id )
                                    <option value="{{ $netsiscari->id }}" selected>{{ $netsiscari->carikod.' - '.$netsiscari->cariadi }}</option>
                                @else
                                    <option value="{{ $netsiscari->id }}">{{ $netsiscari->carikod.' - '. $netsiscari->cariadi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-xs-2" > Eklenecek Sayaçlar: </label>
                    <label class="col-xs-2 count" style="padding-top: 9px">{{Input::old('count') ? Input::old('count') : 0 .' Adet'}}</label>
                    <input type="text" id="count" name="count" value="{{Input::old('count') ? Input::old('count') : 0}}" data-required="1" class="form-control hide">
                </div>
                <div class="panel-group accordion sayaclar col-xs-12" id="accordion1">
                    @if(Input::old('count')!="0")
                        @for($i=0;$i<(int)(Input::old('count'));$i++)
                        <div class="panel panel-default sayaclar_ek"><input class="no hide" value="{{$i}}"/><input class="adet hide" value="1"/>
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_{{$i}}">{{Input::old('serino.'.$i.'').' - '.Input::old('serviskodlari.'.$i.'').' - '.Input::old('sayacadlari.'.$i.'')}} </a>
                                </h4>
                            </div>
                            <div id="collapse_{{$i}}" class="panel-collapse in">
                                <div class="panel-body">
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <label class="col-xs-4 control-label">Seri No:<span class="required" aria-required="true"> * </span></label>
                                        <div class="input-icon right col-xs-6 col-sm-8"><i class="fa"></i><input type="text" id="serino{{$i}}" name="serino[{{$i}}]" maxlength="10" class="form-control serino" value="{{Input::old('serino.'.$i.'')}}" /></div>
                                        <label class="col-xs-1 hidden-sm hidden-md hidden-lg"><a class="btn red satirsil">Sil</a></label>
                                    </div>
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <label class="col-xs-4 control-label">Geliş Yeri:<span class="required" aria-required="true"> * </span></label>
                                        <div class="input-icon right col-xs-8 col-sm-6">
                                            <i class="fa"></i><select class="form-control valid{{$i}} select2me uretimyer uretimyer{{$i}}" id="uretimyer{{$i}}" name="uretimyerleri[{{$i}}]" tabindex="-1" title="">
                                                <option value="">Seçiniz...</option>
                                                @foreach($uretimyerleri as $uretimyer)
                                                    @if((Input::old('uretimyerleri.'.$i.''))==$uretimyer->id)
                                                        <option value="{{ $uretimyer->id }}" selected>{{ $uretimyer->yeradi }}</option>
                                                    @else
                                                        <option value="{{ $uretimyer->id }}">{{ $uretimyer->yeradi }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <label class="col-xs-1 hidden-xs"><a class="btn red satirsil">Sil</a></label>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="col-sm-2 col-xs-4 control-label">İstek:</label>
                                        <div class="col-sm-10 col-xs-8">
                                            <select class="form-control select2me serviskod serviskod{{$i}}" id="serviskod{{$i}}" name="serviskodlari[]" tabindex="-1" title="">
                                                <option value="">Seçiniz...</option>
                                                @foreach($servisstokkodlari as $servisstokkod)
                                                    @if((Input::old('serviskodlari.'.$i.''))==$servisstokkod->id)
                                                        <option value="{{ $servisstokkod->id }}" selected>{{ $servisstokkod->stokkodu.' '.$servisstokkod->stokadi }}</option>
                                                    @else
                                                        <option value="{{ $servisstokkod->id }}">{{ $servisstokkod->stokkodu .' '.$servisstokkod->stokadi }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <label class="col-xs-4 control-label">Sayaç Adı:<span class="required" aria-required="true"> * </span></label>
                                        <div class="input-icon right col-xs-8">
                                            <i class="fa"></i><select class="form-control select2me valid{{$i}} sayacadi sayacadi{{$i}}" id="sayacadi{{$i}}" name="sayacadlari[{{$i}}]" tabindex="-1" title="">
                                                <option value="">Seçiniz...</option>
                                                @foreach($sayacadlari as $sayacadi)
                                                    @if((Input::old('sayacadlari.'.$i.''))==$sayacadi->id)
                                                        <option data-id="{{ $sayacadi->cap }}" value="{{ $sayacadi->id }}" selected>{{ $sayacadi->sayacadi }}</option>
                                                    @else
                                                        <option data-id="{{ $sayacadi->cap }}" value="{{ $sayacadi->id }}" >{{ $sayacadi->sayacadi }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <label class="col-xs-4 control-label">Sayaç Çapı:<span class="required" aria-required="true"> * </span></label>
                                        <div class="col-xs-8">
                                            <i class="fa"></i><select class="form-control select2me valid{{$i}} sayaccap sayaccap{{$i}}" id="sayaccap{{$i}}" name="sayaccap[{{$i}}]" tabindex="-1" title="">
                                                <option value="">Seçiniz...</option>
                                                @foreach($sayaccaplari as $sayaccapi)
                                                    @if((Input::old('sayaccaplari.'.$i.''))==$sayaccapi->id)
                                                        <option value="{{ $sayaccapi->id }}" selected>{{ $sayaccapi->capadi }}</option>
                                                    @else
                                                        <option value="{{ $sayaccapi->id }}">{{ $sayaccapi->capadi }}</option>
                                                    @endif
                                                @endforeach
                                            </select><input type="text" id="sayaccaplari{{$i}}" name="sayaccaplari[]" class="sayaccaplari hide" value="{{Input::old('sayaccaplari.'.$i.'') ? Input::old('sayaccaplari.'.$i.'') : 1}}">
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="col-sm-2 col-xs-4 control-label">Sökülme Nedeni:</label>
                                        <div class="col-sm-10 col-xs-8"><input type="text" id="neden{{$i}}" name="neden[]" maxlength="200" class="form-control neden" value="{{Input::old('neden.'.$i.'')}}" /></div>
                                    </div>
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <label class="col-xs-4 control-label">Servis Sayacı Takılma Tarihi:</label>
                                        <div class="col-xs-8"><input type="text" id="takilmatarihi{{$i}}" name="takilmatarihi[]" maxlength="15" class="form-control takilmatarihi" value="{{Input::old('takilmatarihi.'.$i.'')}}" /></div>
                                    </div>
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <label class="col-xs-4 control-label">Servis Sayacı Takılma Endeksi:</label>
                                        <div class="col-xs-8"><input type="text" id="endeks{{$i}}" name="endeks[]" class="form-control endeks" value="{{Input::old('endeks.'.$i.'')}}" /></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endfor
                    @endif
                </div>
                <div class="form-group">
                    <div class="col-md-6 control-label" style="text-align: left;"><a class="btn green ekle">&nbsp Tek Tek Ekle &nbsp </a>
                        <a class="btn yellow cokluekle" data-toggle="modal" data-target="#cokluekleme">Çoklu Ekle</a>
                        <a class="btn red tumsil">&nbsp Tümünü Sil &nbsp </a></div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <button type="button" class="btn green kaydet" data-toggle="modal" data-target="#confirm">Kaydet</button>
                        <a href="{{ URL::to('sube/sayackayit')}}" class="btn default">Vazgeç</a>
                    </div>
                </div>
            </div>
        </form>
        <!-- END FORM-->
    </div>
<!-- END VALIDATION STATES-->
</div>
@stop

@section('modal')
    <div class="modal fade" id="confirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Sayaç Kayıdı Tamamlanacak</h4>
                </div>
                <div class="modal-body">
                    Girilen Sayaç Kayıdı Bilgileri Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="cokluekleme" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Gelen Bilgisi Sayaç Ekle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_1" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body col-xs-12">
                                        <h3 class="form-section col-xs-12">Yeni Sayaç Girişi</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Sayaç Adedi:</label>
                                            <div class="col-xs-4">
                                                <input type="text" id="adet" name="adet" value="{{Input::old('adet') ? Input::old('adet') : 0}}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Geliş Yeri:<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-xs-6">
                                                <i class="fa"></i><select class="form-control select2me select2-offscreen uretimyer" id="uretimyer" name="uretimyer" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                    @foreach($uretimyerleri as $uretimyer)
                                                        @if(Input::old('uretimyer')==$uretimyer->id )
                                                            <option value="{{ $uretimyer->id }}" selected>{{ $uretimyer->yeradi }}</option>
                                                        @else
                                                            <option value="{{ $uretimyer->id }}">{{ $uretimyer->yeradi }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">İstek:</label>
                                            <div class="col-sm-8 col-xs-6">
                                                <select class="form-control select2me select2-offscreen" id="istek" name="istek" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                    @foreach($servisstokkodlari as $servisstokkod)
                                                        @if(Input::old('istek')==$servisstokkod->id )
                                                            <option value="{{ $servisstokkod->id }}" selected>{{ $servisstokkod->stokkodu.' - '.$servisstokkod->stokadi }}</option>
                                                        @else
                                                            <option value="{{ $servisstokkod->id }}">{{ $servisstokkod->stokkodu.' - '.$servisstokkod->stokadi }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Sayaç Adı:<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-xs-6">
                                                <i class="fa"></i><select class="form-control select2me select2-offscreen" id="sayacadi" name="sayacadi" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                    @foreach($sayacadlari as $sayacadi)
                                                        @if(Input::old('sayacadi')==$sayacadi->id )
                                                            <option data-id="{{ $sayacadi->cap }}" value="{{ $sayacadi->id }}" selected>{{ $sayacadi->sayacadi }}</option>
                                                        @else
                                                            <option data-id="{{ $sayacadi->cap }}" value="{{ $sayacadi->id }}" >{{ $sayacadi->sayacadi }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Sayaç Çapı:<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-xs-6">
                                                <i class="fa"></i><select class="form-control select2me select2-offscreen" id="sayaccap" name="sayaccap" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                    @foreach($sayaccaplari as $sayaccapi)
                                                        @if(Input::old('sayaccap')==$sayaccapi->id )
                                                            <option value="{{ $sayaccapi->id }}">{{ $sayaccapi->capadi }}</option>
                                                        @else
                                                            <option value="{{ $sayaccapi->id }}">{{ $sayaccapi->capadi }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-actions col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-offset-3 col-xs-9">
                                                    <a id="cokluekle" href="#" type="button" data-dismiss="modal" class="btn green">Ekle</a>
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
