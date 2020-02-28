@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Şube Yetkili Eleman <small>Ekleme Ekranı</small></h1>
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
@stop

@section('page-script')
<script src="{{ URL::to('pages/digerdatabase/form-validation-7.js') }}"></script>
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
</script>
<script>
$(document).ready(function(){
    $('#kullanici').on('change', function () {
        //var id=$(this).val();
        var mail = $(this).find("option:selected").data('id');
        var telefon = $(this).find("option:selected").data('value');
        $('#email').val(mail);
        $('#telefon').val(telefon);
    });
    $('#belgeonek').on('change', function(){
        var ek=$(this).val();
        $(this).val(ek.toUpperCase());
    });
    $("select").on("select2-close", function () { $(this).valid(); });
});
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Şube Yetkili Eleman Ekle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('digerdatabase/subeyetkiliekle') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-sm-2 col-xs-4">Adı Soyadı:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-9 col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="kullanici" name="kullanici" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($kullanicilar as $kullanici)
                                @if(Input::old('kullanici')==$kullanici->id )
                                    <option data-id="{{$kullanici->email}}" data-value="{{$kullanici->telefon}}" value="{{ $kullanici->id }}" selected>{{ $kullanici->adi_soyadi.' ('.$kullanici->email.')' }}</option>
                                @else
                                    <option data-id="{{$kullanici->email}}" data-value="{{$kullanici->telefon}}" value="{{ $kullanici->id }}">{{ $kullanici->adi_soyadi.' ('.$kullanici->email.')' }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Email:</label>
                    <div class="col-xs-8">
                        <input type="text" id="email" name="email" value="{{Input::old('email') }}" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Telefon:</label>
                    <div class="col-xs-8">
                        <input type="text" id="telefon" name="telefon" value="{{Input::old('telefon') }}" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4">Cari Bilgisi:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-9 col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="netsiscari" name="netsiscari" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($netsiscariler as $netsiscari)
                                @if(Input::old('netsiscari')==$netsiscari->id )
                            <option value="{{ $netsiscari->id }}" selected>{{ $netsiscari->cariadi }}</option>
                                @else
                            <option value="{{ $netsiscari->id }}">{{ $netsiscari->cariadi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Şube Kodu: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="subekod" name="subekod" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($subekodlari as $subekod)
                                @if(Input::old('subekod')==$subekod->SUBE_KODU )
                                    <option value="{{ $subekod->SUBE_KODU }}" selected>{{ $subekod->SUBE_KODU.'- '.$subekod->UNVAN }}</option>
                                @else
                                    <option value="{{ $subekod->SUBE_KODU }}">{{ $subekod->SUBE_KODU.'- '.$subekod->UNVAN }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Proje Kodu:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="projekodu" name="projekodu" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($projekodlari as $projekodu)
                                @if(Input::old('projekodu')==$projekodu->PROJE_KODU )
                                    <option value="{{ $projekodu->PROJE_KODU }}" selected>{{ $projekodu->PROJE_KODU.'-'.$projekodu->PROJE_ACIKLAMA }}</option>
                                @else
                                    <option value="{{ $projekodu->PROJE_KODU }}">{{ $projekodu->PROJE_KODU.'-'.$projekodu->PROJE_ACIKLAMA }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Belge Ön Eki:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><input type="text" id="belgeonek" name="belgeonek" value="{{Input::old('belgeonek') }}" maxlength="1" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Durumu:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="durum" name="durum" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            <option value="0" {{ Input::old('durum')==0 ? 'selected' : '' }}>Pasif</option>
                            <option value="1" {{ Input::old('durum')==1 ? 'selected' : '' }}>Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn green">Kaydet</button>
                    <a href="{{ URL::to('digerdatabase/subeyetkili')}}" class="btn default">Vazgeç</a>
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
