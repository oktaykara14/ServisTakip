@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Ürün <small>Bilgi Ekranı</small></h1>
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
            <i class="fa fa-plus"></i>Ürün Bilgisi
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-sm-2 col-xs-4">Ürün Adı:</label>
                    <label class="col-xs-6" style="padding-top: 7px">{{ $urun->urunadi }} </label>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Stok Kodu:</label>
                    <label class="col-xs-6" style="padding-top: 7px">{{ $urun->netsisstokkod->kodu.' '.$urun->netsisstokkod->adi }} </label>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-xs-offset-4 col-sm-8 col-xs-8" style="margin-top: 5px;font-size: 15px">
                        <input type="checkbox" id="kontrol" name="kontrol" class="kontrol" {{$urun->kontrol ? 'checked' : ''}} disabled/>
                        Stok Kontrolü Olacak (Eksiye Düşme Olmayacak)
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-xs-offset-4 col-sm-8 col-xs-8" style="margin-top: 5px;font-size: 15px">
                        <input type="checkbox" id="baglanti" name="baglanti" class="baglanti" {{$urun->baglanti ? 'checked' : ''}} disabled/>
                        Abone Sayacı ile Bağlantılı (Aboneye Satılan Sayaç ise Seçilecektir)
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-xs-offset-4 col-sm-8 col-xs-8" style="margin-top: 5px;font-size: 15px">
                        <input type="checkbox" id="ekstra" name="ekstra" class="ekstra" {{ $urun->ekstra ? 'checked' : ''}} disabled/>
                        Servis Kayıdında Kullanılacak (Ekstra Ücretlerde gözükecek)
                    </div>
                </div>
                <div class="form-group {{$urun->baglanti ? "" : "hide" }}">
                    <div class="form-group col-xs-12">
                        <label class="col-sm-2 col-xs-4 control-label">Sayaç Adı:</label>
                        <label class="col-xs-6" style="padding-top: 7px">{{ $urun->sayacadi ? $urun->sayacadi->sayacadi : '' }} </label>
                    </div>
                    <div class="form-group col-xs-12">
                        <label class="col-sm-2 col-xs-4 control-label">Sayaç Çapı:</label>
                        <label class="col-xs-6" style="padding-top: 7px">{{ $urun->sayaccapi ? $urun->sayaccapi->capadi : '' }} </label>
                    </div>
                </div>
                <div class="form-group {{$urun->ekstra ? "" : "hide" }}">
                    <label class="control-label col-sm-2 col-xs-4">Fiyatı:</label>
                    <div class="col-xs-6">
                        <label class="col-xs-6" style="padding-top: 7px">{{ $urun->fiyat.' '.$urun->parabirimi->birimi }} </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Para Birimi:</label>
                    <label class="col-xs-6" style="padding-top: 7px">{{ $urun->parabirimi->adi }} </label>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Netsis Depo:</label>
                    <label class="col-xs-6" style="padding-top: 7px">{{ $urun->netsisdepo->adi }} </label>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Durumu:</label>
                    <label class="col-xs-6" style="padding-top: 7px">{{ $urun->gdurum }} </label>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <a href="{{ URL::to('subedatabase/urunler')}}" class="btn default">Tamam</a>
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
