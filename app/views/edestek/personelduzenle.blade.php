@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Kullanıcı <small>Bilgi Düzenleme Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css">    
<link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::to('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" >
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.tr.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/edestek/form-validation-1.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar   
   FormValidationEdestek.init();
});
$(document).ready(function() {
    $('#options1').on('change', function () {
        var id = $(this).val();
        if (id !== "") {
            $.getJSON(" {{ URL::to('edestek/kullanici') }}/" + id, function (event) {
                if (event.kullanici.mail === null) {
                    $('#email').val("");
                } else {
                    $('#email').val(event.kullanici.mail);
                }
            });
        } else {
            $('#email').val("");
        }
    });
    var options1 = $('#options1').val();
    if (options1 !== "") {
        $.getJSON(" {{ URL::to('edestek/kullanici') }}/" + options1, function (event) {
            if (event.mail === null) {
                $('#email').val("");
            } else {
                $('#email').val(event.kullanici.mail);
            }
        });
    } else {
        $('#email').val("");
    }
    $("select").on("select2-close", function () { $(this).valid(); });
    $('#giristarihi').on('change', function() { $(this).valid(); });
});
</script>
@stop

@section('content')
<div class="portlet box green">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-user"></i>Personel Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('edestek/personelduzenle/'.$personel->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-md-3">Adı Soyadı <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-7">
                        <i class="fa"></i><input type="text" name="adisoyadi" value="{{Input::old('adisoyadi') ? Input::old('adisoyadi') : $personel->adisoyadi }}" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">İşe Giriş Tarihi</label>
                    <div class="col-md-7">
                        <div class="input-group input-medium date date-picker" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                <input id="giristarihi" type="text" name="giristarihi" class="form-control" value="{{Input::old('giristarihi') ? Input::old('giristarihi') : date("d-m-Y", strtotime($personel->giristarihi)) }}">
                                <span class="input-group-btn">
                                <button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button>
                                </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Kullanıcı Adı <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-7">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="options1" name="options1" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($kullanicilar as $kullanici)
                                @if((Input::old('options1') ? Input::old('options1') : $personel->kullanici_id)==$kullanici->id )
                            <option value="{{ $kullanici->id }}" selected>{{ $kullanici->adi.' '.$kullanici->soyadi }}</option>
                                @else
                            <option value="{{ $kullanici->id }}">{{ $kullanici->adi.' '.$kullanici->soyadi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">Email Adresi</label>
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-envelope"></i>
                            </span>
                            <input type="email" id="email" name="email" value="{{ Input::old('email') ? Input::old('email') : $personel->mail }}" class="form-control" readonly placeholder="Email Adresi">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Mesleği </label>
                    <div class="col-md-7">
                        <input type="text" name="meslek" value="{{Input::old('meslek') ? Input::old('meslek') : $personel->meslek }}" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">İlgilendiği Konular</label>
                    <div class="col-md-7">
                        <select class="form-control select2 select2-offscreen" multiple="" tabindex="-1" id="options2" name="options2[]">
                            @foreach($ilgiler as $ilgi)
                            <option value="{{ $ilgi->id }}">{{ $ilgi->adi }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if(Input::old('options2'))
                <div id="ilgiler" class="hide ilgiler">
                    @foreach(Input::old('options2') as $ilgi)
                        {{ $ilgi }}
                    @endforeach
                </div>
                @else
                <div id="ilgiekli" class="hide ilgiekli">{{ $personel->ilgilendikleri }}</div>
                @endif
                <div class="form-group">
                        <label class="control-label col-md-3">Durumu</label>
                        <div class="col-md-9">
                                <div class="bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-off bootstrap-switch-animate" style="width: 116px;">
                                    <div class="bootstrap-switch-container" style="width: 116px; margin-left: 0;">
                                    <input type="checkbox" id="durum" name="durum" class="make-switch" data-on-color="success" data-off-color="warning" data-on-text="Aktif" data-off-text="Pasif" @if(Input::old('durum') ? Input::old('durum') : $personel->durum) checked @endif></div>
                                </div>
                        </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn green">Kaydet</button>
                    <a href="{{ URL::to('edestek/personel')}}" class="btn default">Vazgeç</a>
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
