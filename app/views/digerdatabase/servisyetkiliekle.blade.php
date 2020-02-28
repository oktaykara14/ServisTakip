@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Servis Yetkili <small>Ekleme Ekranı</small></h1>
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
<script src="{{ URL::to('pages/digerdatabase/form-validation-5.js') }}"></script>
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
$(document).ready(function(){
    $("select").on("select2-close", function () { $(this).valid(); });
});
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Servis Yetkili Ekle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('digerdatabase/servisyetkiliekle') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-sm-2 col-xs-4">Yetkili Kişi:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="kullanici" name="kullanici" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($kullanicilar as $kullanici)
                                @if(Input::old('kullanici')==$kullanici->id )
                                    <option value="{{ $kullanici->id }}" selected>{{ $kullanici->adi_soyadi }}</option>
                                @else
                                    <option value="{{ $kullanici->id }}">{{ $kullanici->adi_soyadi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Plasiyer Bilgisi:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="plasiyer" name="plasiyer" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($plasiyerler as $plasiyer)
                                @if(Input::old('plasiyer')==$plasiyer->kodu )
                                    <option value="{{ $plasiyer->kodu }}" selected>{{ $plasiyer->kodu .' - '. $plasiyer->plasiyeradi }}</option>
                                @else
                                    <option value="{{ $plasiyer->kodu }}">{{ $plasiyer->kodu .' - '. $plasiyer->plasiyeradi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Netsis Ozel Kodu:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="ozelkod" name="ozelkod" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($ozelkodlar as $ozelkod)
                                @if(Input::old('ozelkod')==$ozelkod->OZELKOD )
                                    <option value="{{ $ozelkod->OZELKOD }}" selected>{{ $ozelkod->OZELKOD.' - '.$ozelkod->ACIKLAMA }}</option>
                                @else
                                    <option value="{{ $ozelkod->OZELKOD }}">{{ $ozelkod->OZELKOD.' - '.$ozelkod->ACIKLAMA }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Depo Kodu:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="depo" name="depo" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($depolar as $depo)
                                @if(Input::old('depo')==$depo->kodu )
                                    <option value="{{ $depo->kodu }}" selected>{{ $depo->kodu.'-'.$depo->adi }}</option>
                                @else
                                    <option value="{{ $depo->kodu }}">{{ $depo->kodu.'-'.$depo->adi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Netsis Kullanıcı Adı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><input type="text" id="netsiskullanici" name="netsiskullanici" value="{{Input::old('netsiskullanici') }}" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Netsis Kullanıcı No: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><input type="text" id="netsiskullanicino" name="netsiskullanicino" value="{{Input::old('netsiskullanicino') }}" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn green">Kaydet</button>
                    <a href="{{ URL::to('digerdatabase/servisyetkili')}}" class="btn default">Vazgeç</a>
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
