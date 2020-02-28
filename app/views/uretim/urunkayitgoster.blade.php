@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Ürün Kayıt <small>Bilgi Ekranı</small></h1>
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
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
});
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Ürün Kayıt Bilgisi
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="" id="form_sample" class="form-horizontal" novalidate="novalidate">
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
                    <label class="col-xs-4 control-label">Stok Kodu:</label>
                    <label class="col-xs-8" style="padding-top: 7px">{{ $uretimurun->netsisstokkod->kodu }}</label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="col-xs-4 control-label">Adet:</label>
                    <label class=" col-xs-8" style="padding-top: 7px">{{ $uretimurun->adet }}</label>
                </div>
                <div class="form-group col-xs-12">
                    <label class="col-xs-4 col-sm-2 control-label">Depo Kayıdı:</label>
                    <label class="col-xs-8 col-sm-10" style="padding-top: 7px">{{ $uretimurun->depokayitbilgi }}</label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="col-xs-4 control-label">Üretici:</label>
                    <label class="col-xs-8" style="padding-top: 7px">{{ $uretimurun->uretici->ureticiadi }}</label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Üretim Yılı: </label>
                    <label class="col-xs-8" style="padding-top: 7px">{{ $uretimurun->uretimtarihi ? date("d-m-Y", strtotime($uretimurun->uretimtarihi)) : '' }}</label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="col-xs-4 control-label">Kullanılan:</label>
                    <label class="col-xs-8" style="padding-top: 7px">{{ $uretimurun->kullanilan }}</label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="col-xs-4 control-label">Kalan:</label>
                    <label class="col-xs-8" style="padding-top: 7px">{{ $uretimurun->adet-$uretimurun->kullanilan }}</label>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-6">Diğer Bilgiler (Barkod vb.):</label>
                    <label class="col-sm-3 col-xs-6" style="padding-top: 7px">{{ $uretimurun->urunbarkod1==null ? "" : $uretimurun->urunbarkod1 }}</label>
                    <label class="col-sm-3 col-xs-6" style="padding-top: 7px">{{ $uretimurun->urunbarkod2==null ? "" : $uretimurun->urunbarkod2 }}</label>
                    <label class="col-sm-3 col-xs-6" style="padding-top: 7px">{{ $uretimurun->urunbarkod3==null ? "" : $uretimurun->urunbarkod3 }}</label>
                </div>
                <div class="form-group col-xs-6 col-xs-12">
                    <label class="col-xs-offset-4 col-xs-8" style="padding-top: 10px;">
                        <input type="checkbox" {{$uretimurun->muadil==null ? "" : "checked"}} disabled/> Muadil Olarak Kullanılacak </label>
                </div>
                <div class="form-group col-xs-6 col-xs-12 {{($uretimurun->muadil==null ? "" : $uretimurun->muadil)!="" ? "" : "hide"}}">
                    <label class="col-xs-4 control-label">Hangi ürün için?:</label>
                    <label class="col-xs-8" style="padding-top: 7px">{{ $uretimurun->muadilkodu->kodu }}</label>

                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <a href="{{ URL::to('uretim/urunkayit')}}" class="btn default">Tamam</a>
                </div>
            </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('modal')

@stop
