@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Üretim Sonu Kayıt <small>Bilgi Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" >
<link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.tr.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/fuelux/js/spinner.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-scanner-detection/jquery.scannerdetection.js') }}" type="text/javascript" ></script>

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
            <i class="fa fa-plus"></i>Üretim Sonu Kayıt Bilgisi
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
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">İş Emri Numarası:</label>
                    <label class="col-xs-8" style="padding-top: 7px">{{ $isemri->ISEMRINO . ' - ' . $isemri->TARIH . ' Tarihli - ' . $isemri->MIKTAR .' Adet - ' .$isemri->STOK_KODU . ' - ' .$isemri->STOK_ADI . ($isemri->CARI_ISIM ? ' - ' . $isemri->CARI_ISIM : '') }}</label>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-xs-12">
                        <label class="control-label col-sm-2 col-xs-4">Stok Adı:</label>
                        <label class="col-xs-8 stokadi" style="padding-top: 7px">{{$isemri->STOK_ADI}}</label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Miktar:</label>
                        <label class="col-xs-8 miktar" style="padding-top: 7px">{{$isemri->MIKTAR}}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Sipariş No:</label>
                        <label class="col-xs-8 siparisno" style="padding-top: 7px">{{$isemri->SIPARIS_NO}}</label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Üretilen:</label>
                        <label class="col-xs-8 uretilen" style="padding-top: 7px">{{$isemri->URETILENMIKTAR}}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Kalan:</label>
                        <label class="col-xs-8 kalan" style="padding-top: 7px">{{$isemri->KALANMIKTAR}}</label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Açılma Tarihi:</label>
                        <label class="col-xs-8 tarih" style="padding-top: 7px">{{$isemri->TARIH}}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Teslim Tarihi :</label>
                        <label class="col-xs-8 teslimtarihi" style="padding-top: 7px">{{$isemri->TESLIM_TARIHI}}</label>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="col-xs-4 control-label">Çıkış Depo:</label>
                    <label class="col-xs-8" style="padding-top: 7px">{{$uretimsonukayit->cikisdepobilgi->kodu.' - '.$uretimsonukayit->cikisdepobilgi->adi}}</label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="col-xs-4 control-label">Giriş Depo:</label>
                    <label class="col-xs-8" style="padding-top: 7px">{{$uretimsonukayit->girisdepobilgi->kodu.' - '.$uretimsonukayit->girisdepobilgi->adi}}</label>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4">Seri Numaraları:</label>
                    <label class="col-xs-8" style="padding-top: 7px">{{$uretimsonukayit->serinolar}}</label>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Üretilen Miktar:</label>
                        <label class="col-xs-8 uretilecek" style="padding-top: 7px">{{$uretimsonukayit->adet}} Adet</label>
                    </div>
                </div>
                <h4 class="form-section col-sm-12 ">Reçete Bilgisi</h4>
                <div class="panel-group accordion urunler col-xs-12" id="accordion1">
                    @for($i=0;$i<($recete->count());$i++)
                        <div class="panel panel-default urunler_ek">
                            <input class="no hide" value="{{$i}}"/>
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_{{$i}}">
                                        {{Input::old('stokkodu.'.$i) ? Input::old('stokkodu.'.$i) : $recete[$i]->HAM_KODU}} -  {{Input::old('stokadi.'.$i) ? Input::old('stokadi.'.$i) : $recete[$i]->HAMMADDE_ADI}}
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse_{{$i}}" class="panel-collapse in">
                                <div class="panel-body">
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <label class="col-xs-4 control-label">Depo Kodu:</label>
                                        <label class="col-xs-8" style="padding-top: 7px">{{$recete[$i]->uretimsonukullanilan->count()>0 ? $recete[$i]->uretimsonukullanilan->first()->depokodu : $uretimsonukayit->cikisdepo}}</label>
                                    </div>
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <label class="col-xs-4 control-label">Adet:</label>
                                        <label class="col-xs-8" style="padding-top: 7px">{{$recete[$i]->kullanilanadet}}</label>
                                    </div>
                                    <div class="form-group col-xs-12 barkod_ek{{$i}}">
                                        <input type="text" id="barkodcount{{$i}}" name="barkodcount[]" class="form-control barkodcount{{$i}} barkodcount hide" value="{{Input::old('barkodcount.'.$i) ? Input::old('barkodcount.'.$i) : $recete[$i]->uretimsonukullanilan->count()}}"/>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="col-xs-4 control-label">Barkod:</label>
                                            <label class="col-xs-8" style="padding-top: 7px">{{$recete[$i]->secilenbarkodlar}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <a href="{{ URL::to('uretim/uretimsonukayit')}}" class="btn default">Tamam</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('modal')
@stop
