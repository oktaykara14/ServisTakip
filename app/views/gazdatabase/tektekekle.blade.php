@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Gaz Sayaç <small>Ekleme Ekranı</small></h1>
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
<script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>

@stop

@section('page-script')
<script src="{{ URL::to('pages/gazdatabase/form-validation-12.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
   FormValidationGazDatabase.init();
});
</script>
<script>
    $(document).ready(function() {
        var flag=0;
        $(".kaydet").prop('disabled',true);
        var count=parseInt($("#count").val());
        $('.ekle').click(function(){
            var adet=parseInt($("#adet").val());  // eklenecek sayı
            count=parseInt($("#count").val()); //ekli olan sayı
            var sayacadi = $('#genelsayacadi').select2('val');
            while( adet>0 ) {
                var newRow = "";
                var sayi = 0;
                while (adet > 0 && sayi < 100) {
                    newRow += '<div class="panel panel-default sayaclar_ek"><input class="no hide" value="'+(count)+'"/><input class="adet hide" value="1"/><div class="panel-heading"><h4 class="panel-title">' +
                        '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_'+count+'">Yeni</a></h4></div>' +
                        '<div id="collapse_' + count + '" class="panel-collapse in"><div class="panel-body"><div class="form-group col-xs-12">' +
                        '<label class="col-xs-4 col-sm-2 control-label">Seri No:<span class="required" aria-required="true"> * </span></label>' +
                        '<div class="input-icon right col-xs-6 col-sm-8"><i class="fa"></i><input type="text" id="serino'+count+'" name="serino['+count+']" maxlength="15" class="form-control valid'+count+' serino" tabindex="'+(count+1)+'"></div>' +
                        '<label class="col-xs-2"><a class="btn red satirsil">Sil</a></label></div>' +
                        '<div class="form-group col-xs-12"><label class="col-sm-2 col-xs-4 control-label">Sayaç Adı:<span class="required" aria-required="true"> * </span></label><div class="input-icon right col-xs-8">' +
                        '<i class="fa"></i><select class="form-control select2me valid'+count+' sayacadi sayacadi'+count+'" id="sayacadi'+count+'" name="sayacadlari['+count+']" tabindex="-1" title="">' +
                        '<option value="">Seçiniz...</option>' +
                        @foreach($sayacadlari as $sayacadi)
                        '<option value="{{ $sayacadi->id }}" >{{ $sayacadi->sayacadi }}</option>' +
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
                $(".serino").inputmask("mask", {
                    mask: "9", repeat: 15, greedy: !1
                });
                $('.sayacadi').on('change', function () {
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
                            $(this).children('div').children('div').children('div').children('div').children('.sayacadi').attr('id', 'sayacadi' + j).attr('name', 'sayacadlari['+j+']');
                            $(this).children('div').children('div').children('div').children('div').children('.sayacadi').removeClass('sayacadi' + id).addClass('sayacadi' + j);
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
                    $("#sayacadi" + (i)).select2();
                    $("#sayacadi" + (i)).select2("val", sayacadi).trigger('change');
                    $("#sayaccaplari" + (i)).val(1);
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
        for (var i = 0; i < count; i++) {
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
                    $( this).children('div').children('div').children('div').children('div').children('.sayacadi').attr('id','sayacadi'+j).attr('name','sayacadlari['+j+']');
                    $( this).children('div').children('div').children('div').children('div').children('.sayacadi').removeClass('sayacadi'+id).addClass('sayacadi'+j);
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
        $('#genelsayacadi').on('change', function () {
            var id = $(this).val();
            $(".sayacadi").select2("val",id).trigger('change');
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
            mask:"9",repeat: 15,greedy:!1
        });
        if (count > 0) {
            if (flag === 0)
                $(".kaydet").prop('disabled', false);
            else
                $(".kaydet").prop('disabled', true);
        }else{
            $(".kaydet").prop('disabled', true);
        }
        $('#formsubmit').click(function () {
            $('#form_sample').submit();
        });
        $("select").on("select2-close", function () { $(this).valid(); });
        $('#uretimtarihi').on('change', function() { $(this).valid(); });
    });
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Tek Tek Sayaç Ekle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('gazdatabase/tektekekle') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
            <div class="form-body">
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    Girilen Bilgilerde Hata Var.
                </div>
                <div class="alert alert-success display-hide">
                    <button class="close" data-close="alert"></button>
                    Bilgiler Doğru!
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Sayaç Adedi: <span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-sm-4 col-xs-8">
                            <i class="fa"></i><input type="text" id="adet" name="adet" value="" data-required="1" class="form-control">
                            <input type="text" id="count" name="count" value="{{Input::old('count') ? Input::old('count') : 0}}" data-required="1" class="form-control hide">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <div class="col-xs-3"><a class="btn green ekle">&nbsp Ekle &nbsp </a></div>
                        <div class="col-xs-3"><a class="btn red tumsil">Tümünü Sil</a></div>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Üretim Tarihi: <span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><div class="input-group input-medium date date-picker" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                <input id="uretimtarihi" type="text" name="uretimtarihi" class="form-control" value="{{Input::old('uretimtarihi') ? Input::old('uretimtarihi') : '' }}">
                                <span class="input-group-btn"><button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Üretim Yeri:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><select class="form-control select2me uretimyer" id="uretimyer" name="uretimyer" tabindex="-1" title="">
                                <option value="">Seçiniz...</option>
                                @foreach($uretimyerleri as $uretimyer)
                                    @if(Input::old('uretimyer')==$uretimyer->id)
                                        <option value="{{ $uretimyer->id }}" selected>{{ $uretimyer->yeradi }}</option>
                                    @else
                                        <option value="{{ $uretimyer->id }}">{{ $uretimyer->yeradi }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="col-xs-4 control-label">Sayaç Adı:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><select class="form-control select2me" id="genelsayacadi" name="genelsayacadi" tabindex="-1" title="">
                                <option value="">Seçiniz...</option>
                                @foreach($sayacadlari as $sayacadi)
                                    @if(Input::old('genelsayacadi')==$sayacadi->id)
                                        <option value="{{ $sayacadi->id }}" selected>{{ $sayacadi->sayacadi }}</option>
                                    @else
                                        <option value="{{ $sayacadi->id }}" >{{ $sayacadi->sayacadi }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-xs-2" > Eklenecek Sayaçlar: </label>
                    <label class="col-xs-2 count" style="padding-top: 9px">{{Input::old('count') ? Input::old('count') : 0 .' Adet'}}</label>
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
                                    <div class="form-group col-xs-12">
                                        <label class="col-sm-2 col-xs-4 control-label">Seri No:<span class="required" aria-required="true"> * </span></label>
                                        <div class="input-icon right col-xs-6 col-sm-8"><i class="fa"></i><input type="text" id="serino{{$i}}" name="serino[{{$i}}]" maxlength="15" class="form-control serino" value="{{Input::old('serino.'.$i.'')}}" tabindex="{{$i+1}}"/></div>
                                        <label class="col-xs-2"><a class="btn red satirsil">Sil</a></label>
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
                                        </div><input type="text" id="sayaccaplari{{$i}}" name="sayaccaplari[]" class="sayaccaplari hide" value="1">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endfor
                    @endif
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <button type="button" class="btn green kaydet" data-toggle="modal" data-target="#confirm">Kaydet</button>
                        <a href="{{ URL::to('gazdatabase/sayaclar')}}" class="btn default">Vazgeç</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
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
@stop
