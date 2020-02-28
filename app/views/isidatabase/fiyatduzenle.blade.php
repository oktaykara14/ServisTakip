@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Isı Sayaç Fiyatı <small>Bilgi Düzenleme Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-maskmoney/jquery.maskMoney.min.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/isidatabase/form-validation-7.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar   
   FormValidationIsiDatabase.init();
});
$(document).ready(function(){
    $('#uretimyer').on('change', function () {
        var yerid = $(this).val();
        if(yerid!==""){
            $.blockUI();
            $.getJSON("{{ URL::to('backend/yerparabirimi') }}",{id:yerid},function(event){
                if(event.durum){
                    $('#fiyat').maskMoney({suffix: ' '+event.parabirimi,affixesStay:false, allowZero:true});
                }else{
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
        }else{
            $('#fiyat').maskMoney({suffix: ' €',affixesStay:false, allowZero:true});
        }
    });
    var yerid = $('#uretimyer').val();
    if(yerid!==""){
        $.blockUI();
        $.getJSON("{{ URL::to('backend/yerparabirimi') }}",{id:yerid},function(event){
            if(event.durum){
                $('#fiyat').maskMoney({suffix: ' '+event.parabirimi,affixesStay:false, allowZero:true});
            }else{
                toastr[event.type](event.text, event.title);
            }
            $.unblockUI();
        });
    }else{
        $('#fiyat').maskMoney({suffix: ' €',affixesStay:false, allowZero:true});
    }

    $('#sayacadi').on('change', function () {
        var adiid = $(this).val();
        if(adiid!==""){
            $.blockUI();
            $.getJSON("{{ URL::to('isidatabase/sayacadicap') }}",{id:adiid},function(event){
                if(event.durum){
                    if (event.capdurum === "1") {
                        $('.kontrol').removeClass("hide");
                    } else {
                        $('.kontrol').addClass("hide");
                        $('#sayaccap').val("");
                    }
                }else{
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
        }else{
            $('.kontrol').removeClass("hide");
        }
    });
    var sayacadi = $('#sayacadi').val();
    if(sayacadi!==""){
        $.blockUI();
        $.getJSON("{{ URL::to('isidatabase/sayacadicap') }}",{id:sayacadi},function(event){
            if(event.durum){
                if (event.capdurum === "1") {
                    $('.kontrol').removeClass("hide");
                } else {
                    $('.kontrol').addClass("hide");
                    $('#sayaccap').val("");
                }
            }else{
                toastr[event.type](event.text, event.title);
            }
            $.unblockUI();
        });
    }else{
        $('.kontrol').removeClass("hide");
    }
    $("select").on("select2-close", function () { $(this).valid(); });
    $('#form_sample').valid();
});
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-pencil"></i>Sayaç Fiyatı Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('isidatabase/fiyatduzenle/'.$sayacfiyat->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
            <div class="form-body">
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    Girilen Bilgilerde Hata Var.
                </div>
                <div class="alert alert-success display-hide">
                    <button class="close" data-close="alert"></button>
                    Bilgiler Doğru!
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Üretim Yeri: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-6">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="uretimyer" name="uretimyer" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($uretimyerleri as $uretimyer)
                                @if((Input::old('uretimyer') ? Input::old('uretimyer') :$sayacfiyat->uretimyer_id)==$uretimyer->id )
                            <option value="{{ $uretimyer->id }}" selected>{{ $uretimyer->yeradi }}</option>
                                @else
                            <option value="{{ $uretimyer->id }}">{{ $uretimyer->yeradi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Sayaç Adı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-6">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="sayacadi" name="sayacadi" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($sayacadlari as $sayacadi)
                                @if((Input::old('sayacadi') ? Input::old('sayacadi') :$sayacfiyat->sayacadi_id)==$sayacadi->id )
                            <option value="{{ $sayacadi->id }}" selected>{{ $sayacadi->sayacadi }}</option>
                                @else
                            <option value="{{ $sayacadi->id }}">{{ $sayacadi->sayacadi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group kontrol">
                    <label class="control-label col-sm-2 col-xs-4">Sayaç Çapı:</label>
                    <div class="col-xs-6">
                        <select class="form-control select2me select2-offscreen" id="sayaccap" name="sayaccap" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($sayaccaplari as $sayaccapi)
                                @if((Input::old('sayaccap') ? Input::old('sayaccap') :$sayacfiyat->sayaccap_id)==$sayaccapi->id )
                            <option value="{{ $sayaccapi->id }}">{{ $sayaccapi->capadi }}</option>
                                @else
                            <option value="{{ $sayaccapi->id }}">{{ $sayaccapi->capadi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Fiyatı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-6">
                        <i class="fa"></i><input type="tel" id="fiyat" name="fiyat" value="{{(Input::old('fiyat') ? Input::old('fiyat') :$sayacfiyat->fiyat) }}" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn green">Kaydet</button>
                    <a href="{{ URL::to('isidatabase/sayacfiyat')}}" class="btn default">Vazgeç</a>
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

@stop
