@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Sayaç <small>Bilgi Düzenleme Ekranı</small></h1>
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
@stop

@section('page-script')
<script src="{{ URL::to('pages/digerdatabase/form-validation-3.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar   
   FormValidationDigerDatabase.init();
});

$(document).ready(function() {
    $('#sayactur').on('change', function () {
        var turid = $(this).val();
        if (turid !== "") {
            $.blockUI();
            $.getJSON("{{ URL::to('digerdatabase/sayacadi') }}",{id:turid}, function (event) {
                if(event.durum){
                    var sayacadi = event.sayacadi;
                    $("#sayacadi").empty();
                    $("#sayacadi").append('<option value="">Seçiniz...</option>');
                    $("#sayacadi").select2("val", "");
                    $.each(sayacadi, function (index) {
                        $("#sayacadi").append('<option value="' + sayacadi[index].id + '"> ' + sayacadi[index].sayacadi + '</option>');
                    });
                }else{
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
        } else {
            $("#sayacadi").empty();
            $("#sayacadi").append('<option value="">Seçiniz...</option>');
            $("#sayacadi").select2("val", "");
        }
    });

    $('#sayacadi').on('change', function () {
        var adiid = $(this).val();
        if (adiid !== "") {
            $.blockUI();
            $.getJSON("{{ URL::to('digerdatabase/sayacadicap') }}",{id:adiid}, function (event) {
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
        } else {
            $('.kontrol').removeClass("hide");
        }
    });

    $("select").on("select2-close", function () { $(this).valid(); });
    $('#uretimtarihi').on('change', function() { $(this).valid(); });
    $('#form_sample').valid();
});
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-pencil"></i>Sayaç Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('digerdatabase/sayacduzenle/'.$sayac->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-md-2">Seri No</label>
                    <label class="col-md-3" style="margin-top: 9px">{{$sayac->serino}}</label>
                    <label class="control-label col-md-2">Sayaç Türü <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-3">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="sayactur" name="sayactur" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($sayacturleri as $sayactur)
                                @if(Input::old('sayactur')==$sayactur->id )
                                    <option value="{{ $sayactur->id }}" selected>{{ $sayactur->tur }}</option>
                                @else
                                    <option value="{{ $sayactur->id }}">{{ $sayactur->tur }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <input class="hide" name="serino" value="{{$sayac->serino}}"/>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">Üretim Tarihi <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-3">
                        <i class="fa"></i><div class="input-group input-medium date date-picker uretimtarihi" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                            <input id="uretimtarihi" type="text" name="uretimtarihi" class="form-control" value="{{Input::old('uretimtarihi') ? Input::old('uretimtarihi') :  $sayac->uretimtarihi ? date("d-m-Y", strtotime($sayac->uretimtarihi)) : '' }}">
                            <span class="input-group-btn"><button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button></span>
                        </div>
                    </div>
                    <label class="control-label col-md-2">Üretim Yeri<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-3">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="uretimyer" name="uretimyer" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($uretimyerleri as $uretimyer)
                                @if((Input::old('uretimyer') ? Input::old('uretimyer') :$sayac->uretimyer_id)==$uretimyer->id )
                                    <option value="{{ $uretimyer->id }}" selected>{{ $uretimyer->yeradi }}</option>
                                @else
                                    <option value="{{ $uretimyer->id }}">{{ $uretimyer->yeradi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">Sayaç Adı <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="sayacadi" name="sayacadi" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($sayacadlari as $sayacadi)
                                @if((Input::old('sayacadi') ? Input::old('sayacadi') :$sayac->sayacadi_id)==$sayacadi->id )
                            <option value="{{ $sayacadi->id }}" selected>{{ $sayacadi->sayacadi }}</option>
                                @else
                            <option value="{{ $sayacadi->id }}">{{ $sayacadi->sayacadi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group kontrol @if($sayac->sayaccap_id==1) hide @endif ">
                    <label class="control-label col-md-2">Sayaç Çapı</label>
                    <div class="col-md-8">
                        <select class="form-control select2me select2-offscreen" id="sayaccap" name="sayaccap" tabindex="-1" title="" >
                            <option value="">Seçiniz...</option>
                            @foreach($sayaccaplari as $sayaccapi)
                                @if((Input::old('sayaccap') ? Input::old('sayaccap') :$sayac->sayaccap_id)==$sayaccapi->id )
                            <option value="{{ $sayaccapi->id }}">{{ $sayaccapi->capadi }}</option>
                                @else
                            <option value="{{ $sayaccapi->id }}">{{ $sayaccapi->capadi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn green">Kaydet</button>
                    <a href="{{ URL::to('digerdatabase/sayaclar')}}" class="btn default">Vazgeç</a>
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
