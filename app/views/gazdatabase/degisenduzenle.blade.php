@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Gaz Sayaç Değişen Parça <small>Bilgi Düzenleme Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" >
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/gazdatabase/form-validation-5.js') }}"></script>
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
$(document).ready(function(){
    $("select").on("select2-close", function () { $(this).valid(); });
    $('#form_sample').valid();
});
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-pencil"></i>Sayaç Değişen Parça Tanımı Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('gazdatabase/degisenduzenle/'.$degisen->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-sm-2 col-xs-4">Parça Tanımı:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-6">
                        <i class="fa"></i><input type="text" id="tanim" name="tanim" value="{{Input::old('tanim') ? Input::old('tanim') : $degisen->tanim }}" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Parça Durumu:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-6">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="parcadurum" name="parcadurum" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            <option value="0" @if((Input::old('parcadurum') ? Input::old('parcadurum') :$degisen->parcadurum)==0) selected @endif >Parçası Yok</option>
                            <option value="1" @if((Input::old('parcadurum') ? Input::old('parcadurum') :$degisen->parcadurum)==1) selected @endif >Parçası Var</option>
                            <option value="2" @if((Input::old('parcadurum') ? Input::old('parcadurum') :$degisen->parcadurum)==2) selected @endif >Kendisi Parça</option>
                        </select>
                    </div>
                </div>
                <div class="form-group kontrol @if($degisen->parcadurum<>1) hide @endif ">
                    <label class="control-label col-sm-2 col-xs-4">Değişen Parçanın Parçaları:</label>
                    <div class="col-xs-6">
                        <select class="form-control select2 select2-offscreen" multiple="" tabindex="-1" id="parca" name="parca[]">
                            @foreach($parcalar as $parca)
                            <option value="{{ $parca->id }}">{{ $parca->tanim }}</option>  
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="parcaekli" class="hide parcaekli">{{ $degisen->parcalar }}</div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Stok Kontrolü: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-6">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="stokkontrol" name="stokkontrol" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            <option value="0" @if(!(Input::old('stokkontrol') ? Input::old('stokkontrol') :$degisen->stokkontrol)) selected @endif >Yok</option>
                            <option value="1" @if(Input::old('stokkontrol') ? Input::old('stokkontrol') :$degisen->stokkontrol) selected @endif >Var</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Durumu: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-6">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="durum" name="durum" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            <option value="0" @if(!(Input::old('durum')? Input::old('durum') :$degisen->sabit)) selected @endif>Normal</option>
                            <option value="1" @if((Input::old('durum')? Input::old('durum') :$degisen->sabit)) selected @endif>Sabit</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn green">Kaydet</button>
                    <a href="{{ URL::to('gazdatabase/degisenler')}}" class="btn default">Vazgeç</a>
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
