@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Şube Sayaç Satışı <small>Bilgi Ekranı</small></h1>
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
<script>
    $(document).ready(function() {
        $('.fatura').click(function () {
            var fatura = $('#satisid').val();
            if (fatura !== "") {
                $.extend({
                    redirectPost: function (location, args) {
                        var form = '';
                        $.each(args, function (key, value) {
                            value = value.split('"').join('\"');
                            form += '<input type="hidden" name="' + key + '" value="' + value + '">';
                        });
                        $('<form action="' + location + '" method="POST" target="_blank">' + form + '</form>').appendTo($(document.body)).submit();
                    }
                });
                var redirect = "";
                $.redirectPost(redirect, {fatura: fatura});
            }
        });
    });
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-pencil"></i>Fatura Bilgi
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="" id="form_sample" class="form-horizontal" novalidate="novalidate">
            <div class="form-body">
                <input type="text" id="satisid" name="satisid" value="{{ $sayacsatis->id }}" class="form-control hide">
                <input type="text" id="earsiv" name="earsiv" value="{{ $sayacsatis->earsiv }}" class="form-control hide">
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    Girilen Bilgilerde Hata Var.
                </div>
                <div class="alert alert-success display-hide">
                    <button class="close" data-close="alert"></button>
                    Bilgiler Doğru!
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-3">Cari İsim:</label>
                    <label class="col-xs-8" style="padding-top: 7px">{{ $sayacsatis->netsiscari->carikod.' - '.$sayacsatis->netsiscari->cariadi }}
                        <a class="btn btn-warning btn-sm fatura">
                            <i class="fa fa-print"></i> Satış Faturası </a>
                    </label>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Abone:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $sayacsatis->abone->adisoyadi.' - '.$sayacsatis->abone->abone_no }} </label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">TC Kimlik / Vergi No:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $sayacsatis->abone->tckimlikno }}</label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Kayıt Yeri:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $sayacsatis->uretimyeri->yeradi }}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Telefonu:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $sayacsatis->abone->telefon }} </label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Fatura Numarası:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $sayacsatis->faturano }} </label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Ödeme Şekli:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $sayacsatis->odemesekli }} </label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Fatura Tarihi:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $sayacsatis->faturatarihi ? date("d-m-Y", strtotime($sayacsatis->faturatarihi)) : '' }}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Kayıt Yapan:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $sayacsatis->netsiskullanici }} </label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-xs-2">Fatura Adresi:</label>
                    <label class="col-xs-10" style="padding-top: 7px">{{ $sayacsatis->faturaadres }} </label>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">İl:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $sayacsatis->il ? $sayacsatis->il->adi : '' }} </label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">İlçe:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $sayacsatis->ilce ? $sayacsatis->ilce->adi : '' }} </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-3" > Ürünler </label>
                </div>
                <div class="panel-group accordion sayaclar" id="accordion1">
                        @for($i=0;$i<count($sayacsatis->urunler);$i++)
                            <div class="panel panel-default sayaclar_ek"><input class="no hide" value="{{$i}}"/><input class="adet hide" value="1"/>
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_{{$i}}">{{($sayacsatis->urunler[$i]->urunadi) .' - '.($sayacsatis->adetler[$i]) . ' ADET' }} </a>
                                    </h4>
                                </div>
                                <div id="collapse_{{$i}}" class="panel-collapse in">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-2 col-xs-3 control-label">Ürün Adı:</label>
                                            <label class="col-xs-8" style="padding-top: 7px">{{  $sayacsatis->urunler[$i]->netsisstokkod->kodu.' - '.$sayacsatis->urunler[$i]->urunadi }} </label>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="col-sm-2 col-xs-3 control-label">Birim Fiyatı:</label>
                                            <label class="col-sm-2 col-xs-3" style="padding-top: 7px">{{ $sayacsatis->fiyatlar[$i].' '.$sayacsatis->parabirimi->birimi }} </label>
                                            <label class="col-sm-2 col-xs-3 control-label">Miktarı:</label>
                                            <label class="col-sm-1 col-xs-3" style="padding-top: 7px">{{ $sayacsatis->adetler[$i] }} </label>
                                            <label class="col-sm-2 col-xs-3 control-label">Ücretsiz:</label>
                                            <label class="col-sm-2 col-xs-3" style="padding-top: 7px">{{($sayacsatis->ucretsizler[$i] ) ? 'Evet' : 'Hayır'}}</label>
                                        </div>
                                        <div class="form-group col-xs-12 baglantidurum {{ ($sayacsatis->baglanti[$i]) ? '' : 'hide'}}">
                                            <label class="col-sm-2 col-xs-3 control-label">Bağlantılı Sayaçlar:</label>
                                            <label class="col-xs-8" style="padding-top: 7px;white-space: pre-wrap">{{ $sayacsatis->abonesayaclar[$i] }} </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                </div>
                <div class="form-group">
                    <div class="col-sm-3 col-xs-12 control-label" style="text-align: center;">
                        <label class="control-label col-xs-10" style="text-align: center;"><span style="color:red">Kur Tarihi: {{$sayacsatis->kurtarihi}}</span></label>
                    </div>
                    <div class="col-sm-3 col-xs-12 control-label">
                        <label class="control-label col-xs-12 euro" style="padding-top: 9px;margin-left:5px;text-align:center;">Euro : {{isset($dovizkuru[0]) ? $dovizkuru[0]->kurfiyati.' '.$parabirimi->birimi : ""}}</label>
                        <label class="control-label col-xs-12 dolar" style="padding-top: 9px;margin-left:3px;text-align:center;">Dolar : {{isset($dovizkuru[1]) ? $dovizkuru[1]->kurfiyati.' '.$parabirimi->birimi : ""}}</label>
                        <label class="control-label col-xs-12 sterlin" style="padding-top: 9px;text-align:center;">Sterlin : {{isset($dovizkuru[2]) ? $dovizkuru[2]->kurfiyati.' '.$parabirimi->birimi : ""}}</label>
                        <input id="euro" class="hide" value="{{isset($dovizkuru[0]) ? $dovizkuru[0]->kurfiyati : ""}}">
                        <input id="dolar" class="hide" value="{{isset($dovizkuru[1]) ? $dovizkuru[1]->kurfiyati : ""}}">
                        <input id="sterlin" class="hide" value="{{isset($dovizkuru[2]) ? $dovizkuru[2]->kurfiyati : ""}}">
                    </div>
                    <div class="col-sm-5 col-xs-12 control-label">
                        <div class="col-xs-12">
                            <label class="control-label col-xs-6">TUTAR:</label>
                            <label class="col-xs-4 tutar" style="padding-top: 9px">{{ $sayacsatis->tutar.' '.$sayacsatis->parabirimi->birimi }}</label>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label col-xs-6">KDV TUTARI:</label>
                            <label class="col-xs-4 kdvtutar" style="padding-top: 9px">{{ $sayacsatis->kdv.' '.$sayacsatis->parabirimi->birimi }}</label>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label col-xs-6">TOPLAM TUTAR:</label>
                            <label class="col-xs-4 toplamtutar" style="padding-top: 9px">{{ $sayacsatis->toplamtutar.' '.$sayacsatis->parabirimi->birimi }}</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <a href="{{ URL::to('sube/sayacsatis')}}" class="btn default">Tamam</a>
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
