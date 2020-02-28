@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Gaz Servis Periyodik Bakım <small>Ekleme Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::to('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" >
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
<script src="{{ URL::to('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>

@stop

@section('page-script')
<script src="{{ URL::to('pages/mekanikgaz/form-validation-7.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
   FormValidationGazServis.init();
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
            '<div class="input-icon right col-xs-6 col-sm-8"><i class="fa"></i><input type="text" id="serino'+count+'" name="serino['+count+']" maxlength="20" class="form-control valid'+count+' serino"></div>' +
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
            '<option value="{{ $sayacadi->id }}" >{{ $sayacadi->sayacadi }}</option>'+
            @endforeach
            '</select></div></div><div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Üretim Tarihi:<span class="required" aria-required="true"> * </span></label>' +
            '<div class="input-icon right col-xs-8"><i class="fa"></i><select class="form-control select2me valid'+count+' uretimtarih uretimtarih'+count+'" id="uretimtarih'+count+'" name="uretimtarih['+count+']" tabindex="-1" title="">'+
            '<option value="">Seçiniz...</option>'+
            @foreach($yillar as $yil)
            '<option value="{{ $yil }}">{{ $yil }}</option>'+
            @endforeach
            '</select><input type="text" id="sayaccaplari'+count+'" name="sayaccaplari[]" class="sayaccaplari hide" value="1"></div></div>'+
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
                $.getJSON("{{ URL::to('mekanikgaz/cariyer') }}",{netsiscariid:cariadi}, function (event) {
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
            $("#uretimtarih"+(count-1)).select2();
            $('.sayacadi').on('change', function () {
                var id = $(this).val();
                var no = $(this).closest('.sayaclar_ek').children('.no').val();
                if(id!==""){
                    $.blockUI();
                    $.getJSON("{{ URL::to('mekanikgaz/serviskod') }}",{sayacadiid:id}, function (event) {
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
            $('.uretimtarih').on('change', function () {
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
                        $(this).children('div').children('div').children('div').children('div').children('.uretimtarih').attr('id', 'uretimtarih' + j).attr('name', 'uretimtarih['+j+']');
                        $(this).children('div').children('div').children('div').children('div').children('.uretimtarih').removeClass('uretimtarih' + id).addClass('uretimtarih' + j);
                        $(this).children('div').children('div').children('div').children('div').children('.sayaccaplari').attr('id', 'sayaccaplari' + j).attr('name', 'sayaccaplari[]');
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
                        $(".tuslar").removeClass('hide');
                        if (flag === 0)
                            $(".kaydet").prop('disabled', false);
                        else
                            $(".kaydet").prop('disabled', true);
                    } else {
                        $(".tuslar").addClass('hide');
                        $(".kaydet").prop('disabled', true);
                    }
                }
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
                $(".tuslar").removeClass('hide');
                $(".kaydet").prop('disabled', false);
            }else{
                $(".tuslar").addClass('hide');
                $(".kaydet").prop('disabled', true);
            }
            $("select").on("select2-close", function () { $(this).valid(); });
        });
        $('#cokluekle').click(function(){
            var adet=parseInt($("#adet").val());  // eklenecek sayı
            count=parseInt($("#count").val()); //ekli olan sayı
            var sayacadi = $('#sayacadi').select2('val');
            var uretimyer = $('#uretimyer').select2('val');
            var uretimtarih = $('#uretimtarih').select2('val');
            var serviskod = $('#istek').select2('val');
            var cariadi = $('#cariadi').val();
            while( adet>0 ) {
                var newRow = "";
                var sayi = 0;
                while (adet > 0 && sayi < 100) {
                    newRow += '<div class="panel panel-default sayaclar_ek"><input class="no hide" value="'+(count)+'"/><input class="adet hide" value="1"/><div class="panel-heading"><h4 class="panel-title">' +
                        '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_'+count+'">Yeni</a></h4></div>' +
                        '<div id="collapse_' + count + '" class="panel-collapse in"><div class="panel-body"><div class="form-group col-sm-6 col-xs-12">' +
                        '<label class="col-xs-4 col-sm-4 control-label">Seri No:<span class="required" aria-required="true"> * </span></label>' +
                        '<div class="input-icon right col-xs-6 col-sm-8"><i class="fa"></i><input type="text" id="serino'+count+'" name="serino['+count+']" maxlength="20" class="form-control valid'+count+' serino"></div>' +
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
                                '<option value="{{ $sayacadi->id }}" >{{ $sayacadi->sayacadi }}</option>' +
                            @endforeach
                                '</select></div></div><div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Üretim Tarihi:<span class="required" aria-required="true"> * </span></label>' +
                        '<div class="input-icon right col-xs-8"><i class="fa"></i><select class="form-control select2me valid'+count+' uretimtarih uretimtarih'+(count)+'" id="uretimtarih' + (count) + '" name="uretimtarih['+count+']" tabindex="-1" title="">' +
                        '<option value="">Seçiniz...</option>' +
                            @foreach($yillar as $yil)
                                '<option value="{{ $yil }}">{{ $yil }}</option>' +
                            @endforeach
                                '</select><input type="text" id="sayaccaplari'+count+'" name="sayaccaplari[]" class="sayaccaplari hide" value="1"></div></div>' +
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
                    $.getJSON("{{ URL::to('mekanikgaz/cariyer') }}", {netsiscariid: cariadi}, function (event) {
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
                $('.sayacadi').on('change', function () {
                    var id = $(this).val();
                    var no = $(this).closest('.sayaclar_ek').children('.no').val();
                    if (id !== "") {
                        $.blockUI();
                        $.getJSON("{{ URL::to('mekanikgaz/serviskod') }}", {sayacadiid: id}, function (event) {
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
                        $("#serviskod" + (no)).select2('val', '');
                    }
                    $(this).valid();
                });
                $('.uretimyer').on('change', function () {
                    $(this).valid();
                });
                $('.uretimtarih').on('change', function () {
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
                            $(this).children('div').children('div').children('div').children('div').children('.uretimtarih').attr('id', 'uretimtarih' + j).attr('name', 'uretimtarih['+j+']');
                            $(this).children('div').children('div').children('div').children('div').children('.uretimtarih').removeClass('uretimtarih' + id).addClass('uretimtarih' + j);
                            $(this).children('div').children('div').children('div').children('div').children('.sayaccaplari').attr('id', 'sayaccaplari' + j).attr('name', 'sayaccaplari[]');
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
                            $(".tuslar").removeClass('hide');
                            if (flag === 0)
                                $(".kaydet").prop('disabled', false);
                            else
                                $(".kaydet").prop('disabled', true);
                        } else {
                            $(".tuslar").addClass('hide');
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
                    $("#uretimtarih" + (i)).select2();
                    $("#uretimyer" + (i)).select2("val", uretimyer).trigger('change');
                    $("#serviskod" + (i)).select2("val", serviskod).trigger('change');
                    $("#sayacadi" + (i)).select2("val", sayacadi).trigger('change');
                    $("#uretimtarih" + (i)).select2("val", uretimtarih).trigger('change');
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
                    $(".tuslar").removeClass('hide');
                    $(".kaydet").prop('disabled', false);
                } else {
                    $(".tuslar").addClass('hide');
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
        for (i = 0; i < count; i++) {
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
                $.getJSON("{{ URL::to('mekanikgaz/serviskod') }}",{sayacadiid:id}, function (event) {
                    if(event.durum){
                        var serviskod = event.serviskod;
                        $("#serviskod" + (no)).select2("val", serviskod);
                    }else {
                        $("#serviskod" + (no)).select2("val", '');
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
                    $( this).children('div').children('div').children('div').children('div').children('.uretimtarih').attr('id','uretimtarih'+j).attr('name','uretimtarih['+j+']');
                    $( this).children('div').children('div').children('div').children('div').children('.uretimtarih').removeClass('uretimtarih'+id).addClass('uretimtarih'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.sayaccaplari').attr('id', 'sayaccaplari' + j).attr('name', 'sayaccaplari[]');
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
                $.getJSON("{{ URL::to('mekanikgaz/cariyer') }}",{netsiscariid:id}, function (event) {
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
                        uretimyer = $("#exceluretimyer");
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
                        uretimyer = $("#exceluretimyer");
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
                uretimyer = $("#exceluretimyer");
                uretimyer.empty();
                uretimyer.select2("val", "");
            }
        });
        $('#excelekle').click(function(){
            var fileControl = document.getElementById('excel');
            if(fileControl.files.length === 0){
                alert('Excel Dosyası Seçilmedi!');
                return false;
            }
            var formData = new FormData();
            formData.append('file',fileControl.files[0]);
            $.blockUI();
            $.ajax({
                url:'{{ URL::to('mekanikgaz/periyodikexcel') }}',
                type :'POST',
                dataType:'json',
                data: formData,
                async: true,
                cache: false,
                contentType: false,
                enctype: 'multipart/form-data',
                processData: false,
                success: function(event){
                    $.unblockUI();
                    if(!event.durum)
                        toastr[event.type](event.text,event.title);
                    else{
                        var degerler=event.degerler;
                        var hatalar=event.hatalar;
                        $('.hatalar').html('');
                        if(hatalar.length>0){
                            var hata="";
                            for(var j=0;j<hatalar.length && j<3;j++){
                                hata+='<label class="col-xs-12" style="padding-top: 9px">'+hatalar[j].error+'</label>';
                            }
                            $('.hatalar').append(hata);
                        }
                        var adet=degerler.length;  // eklenecek sayı
                        count=parseInt($("#count").val()); //ekli olan sayı
                        var uretimyer = $('#exceluretimyer').select2('val');
                        var inc=0;
                        var cariadi = $('#cariadi').val();
                        while( adet>0 ){
                            var newRow="";
                            var sayi=0;
                            while(adet>0 && sayi<100) {
                                newRow += '<div class="panel panel-default sayaclar_ek"><input class="no hide" value="'+(count)+'"/><input class="adet hide" value="1"/><div class="panel-heading"><h4 class="panel-title">'+
                                    '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_'+(count)+'">Yeni</a></h4></div>' +
                                    '<div id="collapse_'+count+'" class="panel-collapse in"><div class="panel-body"><div class="form-group col-sm-6 col-xs-12">'+
                                    '<label class="col-xs-4 col-sm-4 control-label">Seri No:<span class="required" aria-required="true"> * </span></label>'+
                                    '<div class="input-icon right col-xs-6 col-sm-8"><i class="fa"></i><input type="text" id="serino'+count+'" name="serino['+count+']" maxlength="20" class="form-control valid'+count+' serino" value="'+degerler[inc].serino+'"></div>' +
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
                                            '<option value="{{ $sayacadi->id }}" >{{ $sayacadi->sayacadi }}</option>'+
                                        @endforeach
                                            '</select></div></div><div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Üretim Tarihi:<span class="required" aria-required="true"> * </span></label>' +
                                    '<div class="input-icon right col-xs-8"><i class="fa"></i><select class="form-control select2me valid'+count+' uretimtarih uretimtarih'+(count)+'" id="uretimtarih'+(count)+'" name="uretimtarih['+count+']" tabindex="-1" title="">'+
                                    '<option value="">Seçiniz...</option>'+
                                        @foreach($yillar as $yil)
                                            '<option value="{{ $yil }}">{{ $yil }}</option>'+
                                        @endforeach
                                            '</select><input type="text" id="sayaccaplari'+count+'" name="sayaccaplari[]" class="sayaccaplari hide" value="1"></div></div>'+
                                    '</div></div></div>';
                                adet--;
                                count++;
                                sayi++;
                                inc++;
                            }
                            $('.count').html(count+' Adet');
                            $('.sayaclar').append(newRow);
                            const ilksayi = count-sayi;
                            const sonsayi = count;
                            if(cariadi!==""){
                                $.blockUI();
                                $.getJSON("{{ URL::to('mekanikgaz/cariyer') }}",{netsiscariid:cariadi}, function (event){
                                    if(event.durum){
                                        var uretimyerlist = event.uretimyer;
                                        for(var i=ilksayi;i<sonsayi;i++)
                                        {
                                            var uretimyeri =$("#uretimyer"+(ilksayi+i));
                                            uretimyeri.empty();
                                            uretimyeri.append('<option value="">Seçiniz...</option>');
                                            $.each(uretimyerlist, function (index) {
                                                uretimyeri.append('<option value="' + uretimyerlist[index].id + '"> ' + uretimyerlist[index].yeradi + '</option>');
                                            });
                                            uretimyeri.select2("val", uretimyer);
                                        }
                                    }else{
                                        toastr[event.type](event.text, event.title);
                                    }
                                    $.unblockUI();
                                });
                            }
                            $('.sayacadi').on('change', function () {
                                var id = $(this).val();
                                var no = $(this).closest('.sayaclar_ek').children('.no').val();
                                if(id!==""){
                                    $.blockUI();
                                    $.getJSON("{{ URL::to('mekanikgaz/serviskod') }}",{sayacadiid:id}, function (event) {
                                        if(event.durum){
                                            var serviskod = event.serviskod;
                                            $("#serviskod" + (no)).select2('val', serviskod);
                                        }else {
                                            $("#serviskod" + (no)).select2('val', '');
                                            toastr[event.type](event.text, event.title);
                                        }
                                        $.unblockUI();
                                    });
                                }else{
                                    $("#serviskod" + (no)).select2('val', '');
                                }
                                $(this).valid();
                            });
                            $('.uretimyer').on('change', function () {
                                $(this).valid();
                            });
                            $('.uretimtarih').on('change', function () {
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
                                        $( this).children('div').children('div').children('div').children('div').children('.uretimtarih').attr('id','uretimtarih'+j).attr('name','uretimtarih['+j+']');
                                        $( this).children('div').children('div').children('div').children('div').children('.uretimtarih').removeClass('uretimtarih'+id).addClass('uretimtarih'+j);
                                        $( this).children('div').children('div').children('div').children('div').children('.sayaccaplari').attr('id', 'sayaccaplari' + j).attr('name', 'sayaccaplari[]');
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
                                        $(".tuslar").removeClass('hide');
                                        if(flag===0)
                                            $(".kaydet").prop('disabled', false);
                                        else
                                            $(".kaydet").prop('disabled', true);
                                    }else{
                                        $(".tuslar").addClass('hide');
                                        $(".kaydet").prop('disabled', true);
                                    }
                                }
                            });
                            for(i=ilksayi;i<sonsayi;i++)
                            {
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
                                $("#uretimyer"+(i)).select2();
                                $("#serviskod"+(i)).select2();
                                $("#sayacadi"+(i)).select2();
                                $("#uretimtarih"+(i)).select2();
                                $("#uretimyer"+(i)).select2("val",uretimyer).trigger('change');
                                $("#serviskod"+(i)).select2("val",degerler[i].serviskod).trigger('change');
                                $("#sayacadi"+(i)).select2("val",degerler[i].sayacadi).trigger('change');
                                $("#uretimtarih"+(i)).select2("val",degerler[i].imalyili).trigger('change');
                            }
                            $("#count").val(count);
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
                                $(".tuslar").removeClass('hide');
                                if(flag===0)
                                    $(".kaydet").prop('disabled', false);
                                else
                                    $(".kaydet").prop('disabled', true);
                            }else{
                                $(".tuslar").addClass('hide');
                                $(".kaydet").prop('disabled', true);
                            }
                            $("select").on("select2-close", function () { $(this).valid(); });
                        }
                    }
                },
                error: function (request) {
                    $.unblockUI();
                    alert(request.responseText);
                }
            });
        });
        var cariadi=$('#cariadi').val();
        if(cariadi!==""){
            $.blockUI();
            $.getJSON("{{ URL::to('mekanikgaz/cariyer') }}",{netsiscariid:cariadi}, function (event) {
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
                    uretimyer =$("#exceluretimyer");
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
            uretimyer = $("#exceluretimyer");
            uretimyer.empty();
            uretimyer.select2("val", "");
        }
        $('#sayacadi').on('change', function () {
            var id = $(this).val();
            if(id!==""){
                $.blockUI();
                $.getJSON("{{ URL::to('mekanikgaz/serviskod') }}",{sayacadiid:id}, function (event) {
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
        if (count > 0) {
            $(".tuslar").removeClass('hide');
            if (flag === 0)
                $(".kaydet").prop('disabled', false);
            else
                $(".kaydet").prop('disabled', true);
        }else{
            $(".tuslar").addClass('hide');
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
            <i class="fa fa-plus"></i>Periyodik Bakım Kayıdı Ekle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('mekanikgaz/periyodikekle') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
            <div class="form-body">
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    Girilen Bilgilerde Hata Var.
                </div>
                <div class="alert alert-success display-hide">
                    <button class="close" data-close="alert"></button>
                    Bilgiler Doğru!
                </div>
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
                    <label class="control-label col-sm-2 col-xs-4">Cari İsim: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-10 col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="cariadi" name="cariadi" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($netsiscariler as $netsiscari)
                                @if(Input::old('cariadi')==$netsiscari->id )
                                    <option value="{{ $netsiscari->id }}" selected>{{ $netsiscari->carikod.' - '.$netsiscari->cariadi }}</option>
                                @else
                                    <option value="{{ $netsiscari->id }}">{{ $netsiscari->carikod.' - '.$netsiscari->cariadi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4" > Eklenecek Sayaçlar: </label>
                    <label class="col-sm-2 col-xs-4 count" style="padding-top: 9px">{{Input::old('count') ? Input::old('count') : 0 .' Adet'}}</label>
                    <input type="text" id="count" name="count" value="{{Input::old('count') ? Input::old('count') : 0}}" data-required="1" class="form-control hide">
                </div>
                <div class="form-group">
                    <div class="col-xs-6 control-label" style="text-align: left;"><a class="btn green ekle">&nbsp Tek Tek Ekle &nbsp </a>
                        <a class="btn yellow cokluekle" data-toggle="modal" data-target="#cokluekleme">Çoklu Ekle</a>
                        <a class="btn blue excelekleme" data-toggle="modal" data-target="#excelekleme">Excelden Aktar</a>
                        <a class="btn red tumsil">&nbsp Tümünü Sil &nbsp </a></div>
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
                                        <div class="input-icon right col-xs-6 col-sm-8"><i class="fa"></i><input type="text" id="serino{{$i}}" name="serino[{{$i}}]" maxlength="20" class="form-control valid{{$i}} serino" value="{{Input::old('serino.'.$i.'')}}" /></div>
                                        <label class="col-xs-2 hidden-sm hidden-md hidden-lg"><a class="btn red satirsil">Sil</a></label>
                                    </div>
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <label class="col-xs-4 control-label">Geliş Yeri:<span class="required" aria-required="true"> * </span></label>
                                        <div class="input-icon right col-xs-8 col-sm-6">
                                            <i class="fa"></i><select class="form-control select2me valid{{$i}} uretimyer uretimyer{{$i}}" id="uretimyer{{$i}}" name="uretimyerleri[{{$i}}]" tabindex="-1" title="">
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
                                                        <option value="{{ $sayacadi->id }}" selected>{{ $sayacadi->sayacadi }}</option>
                                                    @else
                                                        <option value="{{ $sayacadi->id }}" >{{ $sayacadi->sayacadi }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <label class="col-xs-4 control-label">Üretim Yılı:<span class="required" aria-required="true"> * </span></label>
                                        <div class="input-icon right col-xs-8">
                                            <i class="fa"></i><select class="form-control select2me valid{{$i}} uretimtarih uretimtarih{{$i}}" id="uretimtarih{{$i}}" name="uretimtarih[{{$i}}]" tabindex="-1" title="">
                                                <option value="">Seçiniz...</option>
                                                @foreach($yillar as $yil)
                                                    @if(Input::old('uretimtarih.'.$i.'')==$yil)
                                                        <option value="{{ $yil}}" selected>{{ $yil }}</option>
                                                    @else
                                                        <option value="{{ $yil }}" >{{ $yil }}</option>
                                                    @endif
                                                @endforeach
                                            </select><input type="text" id="sayaccaplari{{$i}}" name="sayaccaplari[]" class="sayaccaplari hide" value="{{Input::old('sayaccaplari.'.$i.'') ? Input::old('sayaccaplari.'.$i.'') : 1}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endfor
                    @endif
                </div>
                <div class="form-group hatalar" style="color: red;"></div>
                <div class="form-group tuslar">
                    <div class="col-xs-6 control-label" style="text-align: left;"><a class="btn green ekle">&nbsp Tek Tek Ekle &nbsp </a>
                        <a class="btn yellow cokluekle" data-toggle="modal" data-target="#cokluekleme">Çoklu Ekle</a>
                        <a class="btn blue excelekleme" data-toggle="modal" data-target="#excelekleme">Excelden Aktar</a>
                        <a class="btn red tumsil">&nbsp Tümünü Sil &nbsp </a></div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <button type="button" class="btn green kaydet" data-toggle="modal" data-target="#confirm">Kaydet</button>
                        <a href="{{ URL::to('mekanikgaz/periyodik')}}" class="btn default">Vazgeç</a>
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
                    <h4 class="modal-title">Periyodik Bakım Kayıdı Tamamlanacak</h4>
                </div>
                <div class="modal-body">
                    Girilen Periyodik Kayıt Bakım Bilgileri Kaydedilecektir?
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
                                            <div class="input-icon right col-xs-6">
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
                                            <div class="input-icon right col-xs-6">
                                                <i class="fa"></i><select class="form-control select2me select2-offscreen" id="sayacadi" name="sayacadi" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                    @foreach($sayacadlari as $sayacadi)
                                                        @if(Input::old('sayacadi')==$sayacadi->id )
                                                            <option value="{{ $sayacadi->id }}" selected>{{ $sayacadi->sayacadi }}</option>
                                                        @else
                                                            <option value="{{ $sayacadi->id }}" >{{ $sayacadi->sayacadi }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Üretim Tarihi:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-6">
                                                <i class="fa"></i><select class="form-control select2me select2-offscreen" id="uretimtarih" name="uretimtarih" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                    @foreach($yillar as $yil)
                                                        @if(Input::old('uretimtarih')==$yil)
                                                            <option value="{{ $yil}}" selected>{{ $yil }}</option>
                                                        @else
                                                            <option value="{{ $yil }}" >{{ $yil }}</option>
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
    <div class="modal fade" id="excelekleme" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Excelden aktarma
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate" enctype="multipart/form-data">
                                    <div class="form-body col-xs-12">
                                        <h3 class="form-section col-xs-12">Excel üzerinden Kayıt Girişi</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <h4>Excel İlk Satırında Şu Alanlar Olmalıdır:</h4>
                                        <h4>Sıra | Marka | Tip | Model | Sayaç No | İmalat Yılı (Örneğin; 1 | ITRON | DYF | G4 | 12054334 | 2014 )</h4>
                                        <div class="form-group">
                                            <label class="control-label col-xs-2">Excel</label>
                                            <div class="col-sm-8 col-xs-7 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="input-group input-large">
                                                    <div class="form-control uneditable-input input-fixed input-xlarge" data-trigger="fileinput">
                                                        <i class="fa fa-file-excel-o fileinput-exists"></i><span class="fileinput-filename" style="margin-left: 5px"></span>
                                                    </div>
                                                    <span class="input-group-addon btn default btn-file" style="border:1px solid #969696">
                                                    <span class="fileinput-new">
                                                    Excel Seç </span>
                                                    <span class="fileinput-exists">
                                                    Değiştir </span>
                                                    <input type="file" id="excel" name="excel" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                                                    </span>
                                                    <a href="" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Geliş Yeri:<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-xs-6">
                                                <i class="fa"></i><select class="form-control select2me select2-offscreen uretimyer" id="exceluretimyer" name="exceluretimyer" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                    @foreach($uretimyerleri as $uretimyer)
                                                        @if(Input::old('exceluretimyer')==$uretimyer->id )
                                                            <option value="{{ $uretimyer->id }}" selected>{{ $uretimyer->yeradi }}</option>
                                                        @else
                                                            <option value="{{ $uretimyer->id }}">{{ $uretimyer->yeradi }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-actions col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-offset-3 col-xs-9">
                                                    <a id="excelekle" href="#" type="button" data-dismiss="modal" class="btn green">Ekle</a>
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
