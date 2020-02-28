@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Abone Kayıt <small>Ekleme Ekranı</small></h1>
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
<script src="{{ URL::to('assets/global/plugins/jquery-maskplugin/src/jquery.mask.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.tr.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery.quicksearch.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>

@stop

@section('page-script')
<script src="{{ URL::to('pages/sube/form-validation-1.js') }}"></script>
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
        var count=parseInt($("#count").val());
        $('.ekle').click(function(){
            var newRow="";
            newRow += '<div class="panel panel-default sayaclar_ek"><input class="no hide" value="'+(count)+'"/><input class="adet hide" value="1"/><div class="panel-heading"><h4 class="panel-title">'+
            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_'+count+'">Yeni</a></h4></div>' +
            '<div id="collapse_'+count+'" class="panel-collapse in"><div class="panel-body"><div class="form-group">'+
            '<label class="col-sm-2 col-xs-4 control-label">Seri No:<span class="required" aria-required="true"> * </span></label>'+
            '<div class="input-icon right col-sm-4 col-xs-4"><i class="fa"></i><input type="text" id="serino'+count+'" name="serino['+count+']" maxlength="15" class="form-control valid'+count+' serino"></div>' +
            '<label class="col-sm-3 col-xs-4"><a class="btn green getir">Bul</a><a class="btn red satirsil">Sil</a></label></div>'+
            '<div class="form-group"><label class="col-sm-2 col-xs-4 control-label">Sayaç Türü:<span class="required" aria-required="true"> * </span></label>' +
            '<div class="input-icon right col-sm-4 col-xs-8"><i class="fa"></i><select class="form-control select2me valid'+count+' sayactur sayactur'+count+'" id="sayactur'+count+'" name="sayacturleri['+count+']" tabindex="-1" title="">'+
            '<option value="">Seçiniz...</option>'+
            @foreach($sayacturleri as $sayactur)
            '<option value="{{ $sayactur->id }}">{{ $sayactur->tur}}</option>'+
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
            '<div class="form-group col-xs-12"><label class="col-sm-2 col-xs-4 control-label">Montaj Adresi:<span class="required" aria-required="true"> * </span></label><div class="input-icon right col-sm-10 col-xs-8">' +
            '<i class="fa"></i><input type="text" id="sayacadresi'+count+'" name="sayacadresi['+count+']" class="form-control valid'+count+' sayacadresi"></div></div>' +
            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Bilgi:</label><div class="col-xs-8">' +
            '<input type="text" id="sayacbilgi'+count+'" name="sayacbilgi['+count+']" class="form-control sayacbilgi"></div>' +
            '</div><div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">İletişim:</label><div class="col-xs-8">' +
            '<input type="text" id="sayaciletisim'+count+'" name="sayaciletisim['+count+']" class="form-control sayaciletisim"></div>' +
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
            $("#sayactur"+(count-1)).select2();
            $("#sayacadi"+(count-1)).select2();
            $("#sayaccap"+(count-1)).select2();
            $("#sayaccaplari"+(count-1)).val("");
            $('.sayactur').on('change', function () {
                var id = $(this).val();
                var no = $(this).closest('.sayaclar_ek').children('.no').val();
                var subekodu = $('#subekodu').val();
                if(id!==""){
                    $.blockUI();
                    $.getJSON("{{ URL::to('sube/subesayacadlari') }}",{sayacturid:id,subekodu:subekodu}, function (event) {
                        if(event.durum) // sayac adları varsa
                        {
                            var sayacadlari = event.sayacadlari;
                            var sayaccaplari = event.sayaccaplari;
                            var capdurum = event.capdurum;
                            if(capdurum===0){ //sayaccap gözükmeyecek
                                $("#sayaccap"+(no)).select2("val",1).valid();
                                $("#sayaccaplari"+(no)).val(1);
                                $(".sayaccap"+(no)).prop("disabled", true);
                            }else{
                                $("#sayaccap"+(no)).select2("val","").valid();
                                $("#sayaccaplari"+(no)).val("");
                                $(".sayaccap"+(no)).prop("disabled", false);
                            }
                            $("#sayacadi"+(no)).empty();
                            $("#sayacadi"+(no)).append('<option value="">Seçiniz...</option>');
                            $.each(sayacadlari, function (index) {
                                $("#sayacadi"+(no)).append('<option data-id="'+sayacadlari[index].cap+'" value="' + sayacadlari[index].id + '"> ' + sayacadlari[index].sayacadi + '</option>');
                            });
                            $("#sayacadi"+(no)).select2("val", "").valid();
                            $("#sayaccap"+(no)).empty();
                            $("#sayaccap"+(no)).append('<option value="">Seçiniz...</option>');
                            $.each(sayaccaplari, function (index) {
                                $("#sayaccap"+(no)).append('<option value="' + sayaccaplari[index].id + '"> ' + sayaccaplari[index].capadi + '</option>');
                            });
                            $("#sayaccap"+(no)).select2("val", "").valid();
                            $("#sayaccaplari"+(no)).val("");
                        } else { //bulunamadı hatasını ekrana bas
                            $("#sayacadi"+(no)).empty();
                            $("#sayaccap"+(no)).empty();
                            $("#sayacadi"+(no)).append('<option value="">Seçiniz...</option>');
                            $("#sayaccap"+(no)).append('<option value="">Seçiniz...</option>');
                            $("#sayacadi"+(no)).select2("val","").valid();
                            $("#sayaccap"+(no)).select2("val","").valid();
                            $("#sayaccaplari"+(no)).val("");
                            $(".sayaccap"+(no)).prop("disabled", false);
                            toastr[event.type](event.text,event.title);
                        }
                        $.unblockUI();
                    });
                }else{
                    $("#sayacadi"+(no)).empty();
                    $("#sayaccap"+(no)).empty();
                    $("#sayacadi"+(no)).append('<option value="">Seçiniz...</option>');
                    $("#sayaccap"+(no)).append('<option value="">Seçiniz...</option>');
                    $("#sayacadi"+(no)).select2("val","").valid();
                    $("#sayaccap"+(no)).select2("val","").valid();
                    $("#sayaccaplari"+(no)).val("");
                    $(".sayaccap"+(no)).prop("disabled", false);
                }
                $(this).valid();
            });
            $('.sayacadi').on('change', function () {
                var id = $(this).val();
                var no = $(this).closest('.sayaclar_ek').children('.no').val();
                if(id!==""){
                    var capdurum = $(this).find("option:selected").data('id');
                    if (capdurum === 0) //cap kontrol edilmiyor
                    {
                        $("#sayaccap"+(no)).select2("val",1).valid();
                        $("#sayaccaplari"+(no)).val(1);
                        $(".sayaccap"+(no)).prop("disabled", true);
                    } else {
                        $("#sayaccap"+(no)).select2("val","").valid();
                        $("#sayaccaplari"+(no)).val("");
                        $(".sayaccap"+(no)).prop("disabled", false);
                    }
                }else{
                    $("#sayaccap"+(no)).select2("val","").valid();
                    $("#sayaccaplari"+(no)).val("");
                    $(".sayaccap"+(no)).prop("disabled", false);
                }
                $(this).valid();
            });
            $('.sayaccap').on('change', function () {
                var id = $(this).val();
                var no = $(this).closest('.sayaclar_ek').children('.no').val();
                $("#sayaccaplari"+(no)).val(id);
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
                    $('.count').html(count + ' Adet');
                    var j=0;
                    $('.sayaclar .sayaclar_ek').each(function(){
                        var id=$(this).children('.no').val();
                        $(this).children('div').children('h4').children('.accordion-toggle').attr('href','#collapse_'+j);
                        $(this).children('.panel-collapse').attr('id','collapse_'+j);
                        $(this).children('div').children('div').children('div').children('div').children('.serino').attr('id','serino'+j).attr('name','serino['+j+']');
                        $(this).children('div').children('div').children('div').children('div').children('.sayactur').attr('id','sayactur'+j).attr('name','sayacturleri['+j+']');
                        $(this).children('div').children('div').children('div').children('div').children('.sayactur').removeClass('sayactur'+id).addClass('sayactur'+j);
                        $(this).children('div').children('div').children('div').children('div').children('.sayacadi').attr('id','sayacadi'+j).attr('name','sayacadlari['+j+']');
                        $(this).children('div').children('div').children('div').children('div').children('.sayacadi').removeClass('sayacadi'+id).addClass('sayacadi'+j);
                        $(this).children('div').children('div').children('div').children('div').children('.sayaccap').attr('id','sayaccap'+j).attr('name','sayaccap['+j+']');
                        $(this).children('div').children('div').children('div').children('div').children('.sayaccap').removeClass('sayaccap'+id).addClass('sayaccap'+j);
                        $(this).children('div').children('div').children('div').children('div').children('.sayaccaplari').attr('id', 'sayaccaplari' + j).attr('name', 'sayaccaplari[]');
                        $(this).children('div').children('div').children('div').children('div').children('.sayacadresi').attr('id','sayacadresi'+j).attr('name','sayacadresi['+j+']');
                        $(this).children('div').children('div').children('div').children('div').children('.sayacbilgi').attr('id','sayacbilgi'+j).attr('name','sayacbilgi['+j+']');
                        $(this).children('div').children('div').children('div').children('div').children('.sayaciletisim').attr('id','sayaciletisim'+j).attr('name','sayaciletisim['+j+']');
                        $(this).children('.no').val(j);
                        j++;
                    });
                }
            });
            $('.getir').click(function(){
                var sayac=$(this).closest('.sayaclar_ek');
                var no=sayac.children('.no').val();
                $('#secilen').val(no);
                var subekodu = $('#subekodu').val();
                var uretimyer = $('#uretimyer').val();
                var serino = sayac.children('div').children('div').children('div').children('div').children('.serino').val();
                if(serino!==""){
                    $.blockUI();
                    $.getJSON("{{ URL::to('sube/abonebilgi') }}",{serino:serino,subekodu:subekodu,uretimyer:uretimyer}, function (event) {
                        if(event.durum){
                            var sayac = event.sayac;
                            $("#sayactur"+no).select2("val",sayac.sayactur_id).valid();
                            $("#sayacadi"+no).select2("val",sayac.sayacadi_id).valid();
                            $("#sayaccap"+no).select2("val",sayac.sayaccap_id).valid();
                            $("#sayaccaplari"+no).val(sayac.sayaccap_id);
                            if(sayac.sayaccap_id === "1"){
                                $("#sayaccap"+no).prop("disabled", true);
                            }else{
                                $("#sayaccap"+no).prop("disabled", false);
                            }
                            $("#sayacadresi"+no).val(sayac.adres).valid();
                        }else{
                            $("#sayactur"+no).select2("val","").valid();
                            $("#sayacadi"+no).select2("val","").valid();
                            $("#sayaccap"+no).select2("val","").valid();
                            $("#sayaccaplari"+no).val("");
                            $("#sayaccap"+no).prop("disabled", false);
                            $("#sayacadresi"+no).val("");
                            toastr[event.type](event.text, event.title);
                        }
                        $.unblockUI();
                    });
                }
            });

            $(".serino").inputmask("mask", {
                mask:"9",repeat:15,greedy:!1
            });
            $("#count").val(count);
            $('input[name^="serino"]').change(function () {
                $(".kaydet").prop('disabled', false);
                $('input[name^="serino"]').css("background-color", "#FFFFFF");
                $('input[name^="serino"]').each(function (i, el1) {
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
            $("select").on("select2-close", function () { $(this).valid(); });
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
            //$(".kaydet").prop('disabled',true);
        });
        var i,capdurum;
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
        $('.getir').click(function(){
            var sayac=$(this).closest('.sayaclar_ek');
            var no=sayac.children('.no').val();
            $('#secilen').val(no);
            var subekodu = $('#subekodu').val();
            var uretimyer = $('#uretimyer').val();
            var serino = sayac.children('div').children('div').children('div').children('div').children('.serino').val();
            if(serino!==""){
                $.blockUI();
                $.getJSON("{{ URL::to('sube/abonebilgi') }}",{serino:serino,subekodu:subekodu,uretimyer:uretimyer}, function (event) {
                    if(event.durum){
                        var sayac = event.sayac;
                        $("#sayactur"+no).select2("val",sayac.sayactur_id).valid();
                        $("#sayacadi"+no).select2("val",sayac.sayacadi_id).valid();
                        $("#sayaccap"+no).select2("val",sayac.sayaccap_id).valid();
                        $("#sayaccaplari"+no).val(sayac.sayaccap_id);
                        if(sayac.sayaccap_id === "1"){
                            $("#sayaccap"+no).prop("disabled", true);
                        }else{
                            $("#sayaccap"+no).prop("disabled", false);
                        }
                        $("#sayacadresi"+no).val(sayac.adres).valid();
                    }else{
                        $("#sayactur"+no).select2("val","").valid();
                        $("#sayacadi"+no).select2("val","").valid();
                        $("#sayaccap"+no).select2("val","").valid();
                        $("#sayaccaplari"+no).val("");
                        $("#sayaccap"+no).prop("disabled", false);
                        $("#sayacadresi"+no).val("");
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            }
        });
        $('#adisoyadi').on('change',function(){
            var adisoyadi=$(this).val();
            if(adisoyadi!==""){
                $(".kaydet").prop('disabled',false);
            }else{
                $(".kaydet").prop('disabled',true);
            }
        });
        $('#uretimyer').on('change',function(){
            var id=$(this).val();
            if(id!==""){
                $('.tuslar').removeClass('hide');
            }else{
                while($('.sayaclar .sayaclar_ek').size()>0){
                    $('.sayaclar .sayaclar_ek:last').remove();
                    count--;
                }
                $("#count").val(0);
                $('.count').html(0+' Adet');
//                $(".kaydet").prop('disabled',true);
                $('.tuslar').addClass('hide');
            }
        });
        $('.sayactur').on('change', function () {
            var sayactur = $(this).val();
            var subekodu = $('#subekodu').val();
            var no = $(this).closest('.sayaclar_ek').children('.no').val();
            if(sayactur!=="") {
                $.blockUI();
                $.getJSON("{{ URL::to('sube/subesayacadlari') }}",{sayacturid:sayactur,subekodu:subekodu}, function (event) {
                    if (event.durum) // sayac adları varsa
                    {
                        var sayacadlari = event.sayacadlari;
                        var sayaccaplari = event.sayaccaplari;
                        var capdurum = event.capdurum;
                        if (capdurum === 0) { //sayaccap gözükmeyecek
                            $("#sayaccap" + (no)).select2("val", 1).valid();
                            $("#sayaccaplari"+(no)).val(1);
                            $(".sayaccap" + (no)).prop("disabled", true);
                        } else {
                            $("#sayaccap" + (no)).select2("val", "").valid();
                            $("#sayaccaplari"+(no)).val("");
                            $(".sayaccap" + (no)).prop("disabled", false);
                        }
                        $("#sayacadi" + (no)).empty();
                        $("#sayacadi" + (no)).append('<option value="">Seçiniz...</option>');
                        $.each(sayacadlari, function (index) {
                            $("#sayacadi" + (no)).append('<option data-id="' + sayacadlari[index].cap + '" value="' + sayacadlari[index].id + '"> ' + sayacadlari[index].sayacadi + '</option>');
                        });
                        $("#sayacadi" + (no)).select2("val", "").valid();
                        $("#sayaccap" + (no)).empty();
                        $("#sayaccap" + (no)).append('<option value="">Seçiniz...</option>');
                        $.each(sayaccaplari, function (index) {
                            $("#sayaccap" + (no)).append('<option value="' + sayaccaplari[index].id + '"> ' + sayaccaplari[index].capadi + '</option>');
                        });
                        $("#sayaccap" + (no)).select2("val", "").valid();
                        $("#sayaccaplari"+(no)).val("");
                    } else { //bulunamadı hatasını ekrana bas
                        $("#sayacadi" + (no)).empty();
                        $("#sayaccap" + (no)).empty();
                        $("#sayacadi" + (no)).append('<option value="">Seçiniz...</option>');
                        $("#sayaccap" + (no)).append('<option value="">Seçiniz...</option>');
                        $("#sayacadi" + (no)).select2("val", "").valid();
                        $("#sayaccap" + (no)).select2("val", "").valid();
                        $("#sayaccaplari"+(no)).val("");
                        $(".sayaccap" + (no)).prop("disabled", false);
                        toastr['' + event.type + '']('' + event.text + '', '' + event.title + '');
                    }
                    $.unblockUI();
                });
            }else{
                $("#sayacadi" + (no)).empty();
                $("#sayaccap" + (no)).empty();
                $("#sayacadi" + (no)).append('<option value="">Seçiniz...</option>');
                $("#sayaccap" + (no)).append('<option value="">Seçiniz...</option>');
                $("#sayacadi" + (no)).select2("val", "").valid();
                $("#sayaccap" + (no)).select2("val", "").valid();
                $("#sayaccaplari"+(no)).val("");
                $(".sayaccap" + (no)).prop("disabled", false);
            }
            $(this).valid();
        });
        $('.sayacadi').on('change', function () {
            var id = $(this).val();
            var no = $(this).closest('.sayaclar_ek').children('.no').val();
            if(id!==""){
                var capdurum = $(this).find("option:selected").data('id');
                if (capdurum === 0) //cap kontrol edilmiyor
                {
                    $("#sayaccap"+(no)).select2("val",1).valid();
                    $("#sayaccaplari"+(no)).val(1);
                    $(".sayaccap"+(no)).prop("disabled", true);
                } else {
                    $("#sayaccap"+(no)).select2("val","").valid();
                    $("#sayaccaplari"+(no)).val("");
                    $(".sayaccap"+(no)).prop("disabled", false);
                }
            }else{
                $("#sayaccap"+(no)).select2("val","").valid();
                $("#sayaccaplari"+(no)).val("");
                $(".sayaccap"+(no)).prop("disabled", false);
            }
            $(this).valid();
        });
        $('.sayaccap').on('change', function (){
            var id = $(this).val();
            var no = $(this).closest('.sayaclar_ek').children('.no').val();
            $("#sayaccaplari"+(no)).val(id);
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
                    $( this).children('div').children('div').children('div').children('div').children('.sayactur').attr('id','sayactur'+j).attr('name','sayacturleri['+j+']');
                    $( this).children('div').children('div').children('div').children('div').children('.sayactur').removeClass('sayactur'+id).addClass('sayactur'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.sayacadi').attr('id','sayacadi'+j).attr('name','sayacadlari['+j+']');
                    $( this).children('div').children('div').children('div').children('div').children('.sayacadi').removeClass('sayacadi'+id).addClass('sayacadi'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.sayaccap').attr('id','sayaccap'+j).attr('name','sayaccap['+j+']');
                    $( this).children('div').children('div').children('div').children('div').children('.sayaccap').removeClass('sayaccap'+id).addClass('sayaccap'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.sayaccaplari').attr('id', 'sayaccaplari' + j).attr('name', 'sayaccaplari[]');
                    $( this).children('div').children('div').children('div').children('div').children('.sayacadresi').attr('id','sayacadresi'+j).attr('name','sayacadresi['+j+']');
                    $( this).children('div').children('div').children('div').children('div').children('.sayacbilgi').attr('id','sayacbilgi'+j).attr('name','sayacbilgi['+j+']');
                    $( this).children('div').children('div').children('div').children('div').children('.sayaciletisim').attr('id','sayaciletisim'+j).attr('name','sayaciletisim['+j+']');
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
                    if(flag === 0)
                        $(".kaydet").prop('disabled', false);
                    else
                        $(".kaydet").prop('disabled', true);
                }
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
        $('#il').on('change', function (){
            var faturail = $(this).val();
            if(faturail!==""){
                $.blockUI();
                $.getJSON("{{ URL::to('sube/faturailceler') }}",{id:faturail},function(event){
                    if(event.durum){
                        var ilceler=event.ilceler;
                        $("#ilce").empty();
                        if(ilceler.length>0){
                            $("#ilce").append('<option value="">Seçiniz...</option>');
                            $.each(ilceler, function (index) {
                                $("#ilce").append('<option value="' + ilceler[index].id + '">' + ilceler[index].adi+'</option>');
                            });
                            $("#ilce").select2("val","");
                        }
                    }else{
                        toastr['' + event.type + '']('' + event.text + '', '' + event.title + '');
                    }
                    $.unblockUI();
                });
            }
        });
        if (count > 0) {
            if (flag === 0)
                $(".kaydet").prop('disabled', false);
            else
                $(".kaydet").prop('disabled', true);
        }
        $('#formsubmit').click(function () {
            $('#form_sample').submit();
        });
        $("select").on("select2-close", function () { $(this).valid(); });
    });
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Yeni Abone Ekle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('sube/abonekayitekle') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <input type="text" id="subekodu" name="subekodu" value="{{ $sube ? $sube->subekodu : 1 }}" data-required="1" class="form-control">
                    <input type="text" id="subelinked" name="subelinked" value="{{ $sube ? $sube->subelinked : '' }}" data-required="1" class="form-control">
                    <input type="text" id="bellinked" name="bellinked" value="{{ $sube ? $sube->bellinked : ''}}" data-required="1" class="form-control">
                    <input type="text" id="netsisdepo" name="netsisdepo" value="{{ $sube ? $sube->netsisdepolar_id : 1 }}" data-required="1" class="form-control">
                    <input type="text" id="netsiscari" name="netsiscari" value="{{ $sube ? $sube->netsiscari_id : 2631 }}" data-required="1" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Cari İsim:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
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
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Adı Soyadı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-9">
                        <i class="fa"></i><input type="text" id="adisoyadi" name="adisoyadi" value="{{ Input::old('adisoyadi') }}" maxlength="200" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Kayıt Yeri: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-9">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="uretimyer" name="uretimyer" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($uretimyerleri as $uretimyeri)
                                @if(Input::old('uretimyer')==$uretimyeri->id)
                                    <option value="{{ $uretimyeri->id }}" selected>{{ $uretimyeri->yeradi }}</option>
                                @else
                                    <option value="{{ $uretimyeri->id }}" >{{ $uretimyeri->yeradi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Vergi Dairesi:</label>
                    <div class="col-sm-8 col-xs-9">
                        <input type="text" id="vergidairesi" name="vergidairesi" value="{{ Input::old('vergidairesi') }}" maxlength="30" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">TC Kimlik / Vergi No:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-9">
                        <i class="fa"></i><input type="text" id="tckimlikno" name="tckimlikno" value="{{ Input::old('tckimlikno') }}" maxlength="11" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Abone No:</label>
                    <div class="col-sm-8 col-xs-9">
                        <input type="text" id="aboneno" name="aboneno" value="{{ Input::old('aboneno') }}" maxlength="10" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Telefonu:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-9">
                        <i class="fa"></i><input type="tel" id="telefon" name="telefon" value="{{ Input::old('telefon') }}" maxlength="17" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Fatura Adresi:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-9">
                        <i class="fa"></i><input type="text" id="adres" name="adres" value="{{ Input::old('adres') }}" maxlength="100" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Email Adresi:</label>
                    <div class="col-sm-8 col-xs-9">
                        <input type="text" id="email" name="email" value="{{ Input::old('email') }}" maxlength="100" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">İl:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="il" name="il" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($iller as $il)
                                @if(Input::old('il')==$il->id )
                                    <option value="{{ $il->id }}" selected>{{ $il->adi }}</option>
                                @else
                                    <option value="{{ $il->id }}">{{ $il->adi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">İlçe:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="ilce" name="ilce" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($ilceler as $ilce)
                                @if(Input::old('ilce')==$ilce->id )
                                    <option value="{{ $ilce->id }}" selected>{{ $ilce->adi }}</option>
                                @else
                                    <option value="{{ $ilce->id }}">{{ $ilce->adi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4" > Aboneye Ait Sayaçlar: </label>
                    <label class="col-xs-2 count" style="padding-top: 9px">{{Input::old('count') ? Input::old('count') : 0 .' Adet'}}</label>
                    <input type="text" id="count" name="count" value="{{Input::old('count') ? Input::old('count') : 0}}" data-required="1" class="form-control hide">
                    <input type="text" id="secilen" name="secilen" value="{{Input::old('secilen') ? Input::old('secilen') : -1}}" data-required="1" class="form-control hide">
                </div>
                <div class="panel-group accordion sayaclar col-xs-12" id="accordion1">
                    @if((int)(Input::old('count'))!=0)
                        @for($i=0;$i<(int)(Input::old('count'));$i++)
                        <div class="panel panel-default sayaclar_ek"><input class="no hide" value="{{$i}}"/><input class="adet hide" value="1"/>
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_{{$i}}">{{Input::old('serino.'.$i)}} </a>
                                </h4>
                            </div>
                            <div id="collapse_{{$i}}" class="panel-collapse in">
                                <div class="panel-body">
                                    <div class="form-group col-xs-12">
                                        <label class="col-sm-2 col-xs-4 control-label">Seri No: <span class="required" aria-required="true"> * </span></label>
                                        <div class="input-icon right col-sm-4 col-xs-6">
                                            <i class="fa"></i><input type="text" id="serino{{$i}}" name="serino[{{$i}}]" maxlength="15" class="form-control valid{{$i}} serino" value="{{Input::old('serino.'.$i.'')}}" />
                                        </div>
                                        <label class="col-sm-3 col-xs-4"><a class="btn green getir">Bul</a><a class="btn red satirsil">Sil</a></label>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2 col-xs-4">Sayaç Türü:<span class="required" aria-required="true"> * </span></label>
                                        <div class="input-icon right col-sm-4 col-xs-8">
                                            <i class="fa"></i><select class="form-control select2me valid{{$i}} sayactur sayactur{{$i}}" id="sayactur{{$i}}" name="sayacturleri[{{$i}}]" tabindex="-1" title="">
                                                <option value="">Seçiniz...</option>
                                                @foreach($sayacturleri as $sayactur)
                                                    @if(Input::old('sayacturleri.'.$i.'')==$sayactur->id)
                                                        <option value="{{ $sayactur->id }}" selected>{{ $sayactur->tur }}</option>
                                                    @else
                                                        <option value="{{ $sayactur->id }}">{{ $sayactur->tur }}</option>
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
                                        <div class="input-icon right col-xs-8">
                                            <i class="fa"></i><select class="form-control select2me valid{{$i}} sayaccap sayaccap{{$i}}" id="sayaccap{{$i}}" name="sayaccap[{{$i}}]" tabindex="-1" title="">
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
                                        <label class="col-sm-2 col-xs-4 control-label">Montaj Adresi:<span class="required" aria-required="true"> * </span></label>
                                        <div class="input-icon right col-sm-10 col-xs-8">
                                            <i class="fa"></i><input type="text" id="sayacadresi{{$i}}" name="sayacadresi[{{$i}}]" class="form-control valid{{$i}} sayacadresi" value="{{Input::old('sayacadresi.'.$i.'') ? Input::old('sayacadresi.'.$i) : ""}}">
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <label class="col-xs-4 control-label">Bilgi:</label>
                                        <div class="col-xs-8">
                                            <input type="text" id="sayacbilgi{{$i}}" name="sayacbilgi[{{$i}}]" class="form-control sayacbilgi" value="{{Input::old('sayacbilgi.'.$i.'')}}" >
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <label class="col-xs-4 control-label">İletişim:</label>
                                        <div class="col-xs-8">
                                            <input type="text" id="sayaciletisim{{$i}}" name="sayaciletisim[{{$i}}]" class="form-control sayaciletisim" value="{{Input::old('sayaciletisim.'.$i.'')}}" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endfor
                    @endif
                </div>
                <div class="form-group tuslar {{(int)(Input::old('count'))!=0 ? '' : 'hide'}}">
                    <div class="col-md-6 control-label" style="text-align: left;"><a class="btn green ekle">&nbsp Ekle &nbsp </a>
                        <a class="btn red tumsil">&nbsp Tümünü Sil &nbsp </a></div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <button type="button" class="btn green kaydet" data-toggle="modal" data-target="#confirm">Kaydet</button>
                        <a href="{{ URL::to('sube/abonekayit')}}" class="btn default">Vazgeç</a>
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
                    <h4 class="modal-title">Abone Bilgisi Kaydedilecek</h4>
                </div>
                <div class="modal-body">
                    Girilen Abone Bilgisi Kaydedilecektir?
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
@stop

