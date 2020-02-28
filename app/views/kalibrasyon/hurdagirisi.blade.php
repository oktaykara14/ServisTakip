@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Gaz Kalibrasyon Hurda Kayıt <small>Giriş Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::to('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" />
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
<script src="{{ URL::to('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery.quicksearch.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/kalibrasyon/form-validation-2.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
   FormValidationKalibrasyon.init();
});
</script>
<script>
    $(document).ready(function() {
        $(".kaydet").prop('disabled',true);
        var count= 0;
        $("#kalibrasyon").on('change', function() {
            var tumu = $(this).val();
            var oncekiler = $('#secilenler').val();
            var oncekilist = oncekiler.split(',');
            var yeni="";
            var flag=0;
            if(oncekiler==="" || tumu!=null) //ekleme varsa
            {
                if( tumu.length>=oncekilist.length) //ekleme varsa
                {
                    $.each(tumu,function(index){
                        $.each(oncekilist,function(index2){
                            if(oncekilist[index2]===tumu[index])
                            {
                                flag=1;
                                return false;
                            }
                        });
                        if(flag===0)
                        {
                            yeni=tumu[index];
                            return false;
                        }else{
                            flag=0;
                            return true;
                        }
                    });
                    var seri=$('#kalibrasyon option[value='+ yeni +']').html();
                    seri= $.trim(seri);
                    var newSayac='<div class="form-group sayaclar_ek col-xs-12"><input class="no hide" value="'+count+'"/>'+
                        '<label class="col-sm-2 col-xs-4 control-label">Seri No:</label><label class="col-sm-2 col-xs-8 serino serino'+count+'" style="margin-top: 9px;">'+seri+'</label>'+
                        '<input type="text" id="serino'+count+'" name="serino['+count+']" class="form-control hide" value="'+seri+'"/><input type="text" id="kalibrasyonid'+count+'" name="kalibrasyonid[]" class="form-control hide" value="'+yeni+'"/>' +
                        '<label class="col-sm-2 col-xs-4 control-label">Hurda Nedeni:<span class="required" aria-required="true"> * </span></label>' +
                        '<div class="input-icon right col-sm-6 col-xs-8"><i class="fa"></i>'+
                        '<select class="form-control select2me valid'+count+' hurdaneden hurdaneden'+count+'" id="hurdaneden'+count+'" name="hurdanedenleri['+count+']" tabindex="-1" title="">'+
                        '<option value="">Seçiniz...</option>'+
                            @foreach($nedenler as $neden)
                                '<option value="{{ $neden->id }}">{{ $neden->nedeni }}</option>'+
                            @endforeach
                                '</select></div>'+
                        '</div></div>';
                    $('.sayaclar').append(newSayac);
                    $('select.valid'+(count)).each(function(){
                        $(this).rules('remove');
                        $(this).rules('add', {
                            required: true
                        });
                    });
                    $('input.valid'+(count)).each(function(){
                        $(this).rules('remove');
                        $(this).rules('add', {
                            required: true
                        });
                    });
                    $("#hurdaneden"+(count)).select2();
                    $("#count").val(++count);
                    $('#secilenler').val(oncekiler+(oncekiler==="" ? "" : ",")+yeni);
                    $(".kaydet").prop('disabled',false);
                    $(".hurdaneden").on('change',function(){ $(this).valid();});

                }else{ //silinme varsa
                    $.each(oncekilist, function (index) {
                        $.each(tumu, function (index2) {
                            if (oncekilist[index] === tumu[index2]) {
                                flag = 1;
                                return false;
                            }
                        });
                        if (flag === 0) {
                            yeni = oncekilist[index];
                            return false;
                        } else {
                            flag = 0;
                            return true;
                        }
                    });
                    $('.serino' + yeni).closest('.sayaclar_ek').remove();
                    var j = 0;
                    $('.sayaclar .sayaclar_ek').each(function () {
                        var id = $(this).children('.no').val();
                        $(this).children('.serino').removeClass('serino' + id).addClass('serino' + j);
                        $(this).children('#serino' + id).attr('id', 'serino' + j).attr('name', 'serino[' + j + ']');
                        $(this).children('#kalibrasyonid' + id).attr('id', 'kalibrasyonid' + j).attr('name', 'kalibrasyonid[]');
                        $(this).children('div').children('.hurdaneden').removeClass('hurdaneden' + id).addClass('hurdaneden' + j);
                        $(this).children('div').children('.hurdaneden').attr('id', 'hurdaneden' + j).attr('name', 'hurdanedenleri[' + j + ']');
                        $(this).children('.no').val(j);
                        j++;
                    });
                    count--;
                    $("#count").val(count);
                }

            }else{ //silinme varsa
                if(tumu==null)
                {
                    while($('.sayaclar .sayaclar_ek').size()>0){
                        $('.sayaclar .sayaclar_ek:last').remove();
                    }
                    count=0;
                    $('#secilenler').val("");
                    $(".kaydet").prop('disabled',true);
                }else{
                    $.each(oncekilist,function(index){
                        $.each(tumu,function(index2){
                            if(oncekilist[index]===tumu[index2])
                            {
                                flag=1;
                                return false;
                            }
                        });
                        if(flag===0)
                        {
                            yeni=oncekilist[index];
                            return false;
                        }else{
                            flag=0;
                            return true;
                        }
                    });
                    $('.serino'+yeni).closest('.sayaclar_ek').remove();
                    j = 0;
                    $('.sayaclar .sayaclar_ek').each(function () {
                        var id = $(this).children('.no').val();
                        $(this).children('.serino').removeClass('serino' + id).addClass('serino'+j);
                        $(this).children('#serino'+id).attr('id', 'serino' + j).attr('name', 'serino['+j+']');
                        $(this).children('#kalibrasyonid'+id).attr('id', 'kalibrasyonid' + j).attr('name', 'kalibrasyonid[]');
                        $(this).children('div').children('.hurdaneden').removeClass('hurdaneden' + id).addClass('hurdaneden' + j);
                        $(this).children('div').children('.hurdaneden').attr('id', 'hurdaneden' + j).attr('name', 'hurdanedenleri['+j+']');
                        $(this).children('.no').val(j);
                        j++;
                    });
                    count--;
                }
                $("#count").val(count);
            }
        });
        $(".hurdaneden").on('change',function(){ $(this).valid();});
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
            <i class="fa fa-plus"></i>Kalibrasyon Hurda Kayıt Girişi
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('kalibrasyon/hurdagirisi/'.$grup->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-sm-2 col-xs-4"><b>Cari Adı:</b></label>
                    <label class="col-sm-10 col-xs-8" style="padding-top: 9px">{{ $grup->netsiscari->cariadi}}</label>
                    <input class="hide" id="grupid" name="grupid" value="{{ $grup->id}}"/>
                    <input type="text" id="count" name="count" value="{{Input::old('count') ? Input::old('count') : 0}}" data-required="1" class="form-control hide">
                    <input type="text" id="secilenler" name="secilenler" value="{{Input::old('secilenler') ? Input::old('secilenler') : ''}}" data-required="1" class="form-control hide">
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group">
                        <label class="control-label col-sm-2 col-xs-4">Seri No:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-sm-10 col-xs-8">
                            <i class="fa"></i><select class="form-control select2 select2-offscreen" multiple="" tabindex="-1" id="kalibrasyon" name="kalibrasyon[]">
                                @foreach($kalibrasyonlar as $kalibrasyon)
                                    <option value="{{ $kalibrasyon->id }}">{{ $kalibrasyon->kalibrasyon_seri }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="kalibrasyonlar" class="hide kalibrasyonlar">
                        @if(Input::old('kalibrasyon'))
                            @foreach(Input::old('kalibrasyon') as $seri)
                                {{$seri}}
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-6"> Hurda Sayaç Bilgileri</label>
                </div>
                <div class="form-group sayaclar">

                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <button type="button" class="btn green kaydet" data-toggle="modal" data-target="#confirm">Kaydet</button>
                        <a href="{{ URL::to('kalibrasyon/kalibrasyondetay/'.$grup->id.'')}}" class="btn default">Vazgeç</a>
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
                    <h4 class="modal-title">Kalibrasyon Hurda Kayıt Bilgisi Kaydedilecek</h4>
                </div>
                <div class="modal-body">
                    Girilen Hurda Sayaç Bilgileri Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
@stop

