@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Servis <small>Bilgi Ekranı</small></h1>
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
            <i class="fa fa-plus"></i>Servis Kayıdı Bilgisi
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
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Abone:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $abone->adisoyadi }}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Kayıt Yeri:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $uretimyer->yeradi }}</label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Abone Sayacı:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $abonesayac->serino }}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">TC Kimlik No :</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $abone->tckimlikno }}</label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4">Takılma Adresi :</label>
                    <label class="col-xs-8" style="padding-top: 7px">{{ $kayit->kayitadres }} </label>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Abone Telefon :</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $abone->telefon }}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Diğer Telefonlar :</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $abonesayac->iletisim }}</label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Açıklama:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $kayit->aciklama}}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Servis Nedeni:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $kayit->gtipi}}</label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Açılma Tarihi:</label>
                        <label class="col-xs-8" style="padding-top: 7px">@if(isset($kayit->acilmatarihi)){{ $kayit->acilmatarihi ? date("d-m-Y", strtotime($kayit->acilmatarihi)) : '' }}@endif</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Kapanma Tarihi:</label>
                        <label class="col-xs-8" style="padding-top: 7px">@if(isset($kayit->kapanmatarihi)){{ $kayit->kapanmatarihi ? date("d-m-Y", strtotime($kayit->kapanmatarihi)) : '' }}@endif</label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Servis Personeli:</label>
                        <label class="col-xs-8" style="padding-top: 7px">@if(isset($subepersonel)) {{$subepersonel->kullanici->adi_soyadi}} @endif </label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Servis Durumu:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $kayit->gdurum}}</label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Servis Son Durumu:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $kayit->servisnotu}}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12 {{$kayit->tipi==2 ? "" : "hide"}}">
                        <label class="control-label col-xs-4">Sökülme Durumu:</label>
                        <label class="col-xs-8" style="padding-top: 7px"><input type="checkbox" id="sayacsokuldu" name="sayacsokuldu" {{$kayit->sokulmedurumu ? "checked" : ""}} disabled/> Sayaç Söküldü</label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Servis Sayacı:</label>
                        <label class="col-xs-8" style="padding-top: 7px"><input type="checkbox" id="servissayaci" name="servissayaci" {{$kayit->servissayaci ? "checked" : ""}} disabled/> Takıldı </label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Diğer Ücretler:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{$ekstra}}</label>
                    </div>
                </div>
                <div class="form-group col-xs-12 {{($kayit->servissayaci) ? "" : "hide"}}">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Takılma Tarihi:</label>
                        <label class="col-xs-8" style="padding-top: 7px">@if(isset($kayit->takilmatarihi)){{ $kayit->takilmatarihi ? date("d-m-Y", strtotime($kayit->takilmatarihi)) : '' }}@endif</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">İlk Endeksi:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ number_format($kayit->ilkendeks,3,',','.').' '.$kayit->birim}}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12 {{$kayit->durum==1 && $kayit->tipi!=2 ? "" : "hide"}}">
                        <label class="control-label col-xs-4">Sayaç Borcu:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ number_format($kayit->sayacborcu,3,',','.').' '.$kayit->birim}}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12 {{$kayit->durum==1 && $kayit->tipi!=2 ? "" : "hide"}}">
                        <label class="control-label col-xs-4">Son Endeksi:</label>
                        <label class="col-md-3" style="padding-top: 7px">{{ number_format($kayit->sonendeks,3,',','.').' '.$kayit->birim}}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12 {{$kayit->durum==1 && $kayit->tipi!=2 ? "" : "hide"}}">
                        <label class="control-label col-xs-4">Toplam Fark:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ number_format($kayit->sonendeks-$kayit->ilkendeks+$kayit->sayacborcu,3,',','.').' '.$kayit->birim}}</label>
                    </div>
                </div>
                <div class="form-group col-xs-12 {{($kayit->smsdurum==-1 ? 0 : 1)==1 ? (($kayit->tipi)==2 ? (($kayit->sokulmedurumu) ? '' : (($kayit->durum)=="1"  ? "" : "hide")) : (($kayit->durum)=="1"  ? "" : "hide")) : "hide"}}">
                    <h4>Sms Bilgilendirme</h4>
                    <div class="form-group col-xs-12">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="control-label col-xs-4">İlgili Telefonu:</label>
                            <label class="col-xs-8" style="padding-top: 7px">{{$kayit->ilgilitel}}</label>
                        </div>
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="control-label col-xs-4">Sms Durumu:</label>
                            <label class="col-xs-8 smsdurum" style="padding-top: 7px">{{$kayit->gsmsdurum}}</label>
                        </div>
                    </div>
                    <div class="form-group col-xs-12">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="control-label col-xs-4">Sms İçeriği:</label>
                            <label class="col-xs-8" style="padding-top: 7px">@if(isset($smslog)) {{$smslog->mesaj}} @endif </label>
                        </div>
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="control-label col-xs-4">Sms Zamanı:</label>
                            <label class="col-xs-8" style="padding-top: 7px">@if(isset($smslog)) {{date('H:i',strtotime($smslog->tarih))}} @endif </label>
                        </div>
                    </div>
                    <div class="hide"></div>
                </div>
                <div class="form-group col-xs-12 serviskayitbilgi {{$servisbilgi ? "" : "hide"}}">
                    <div class="panel-group accordion col-xs-12" id="accordion1">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion1" href="#collapse_1">Eski Servis Kayıdı Detayı</a>
                            </h4>
                        </div>
                        <div id="collapse_1" class="panel-collapse collapse">
                            <div class="panel-body">
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Abone:</label>
                                    <label class="col-xs-8 abone" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->adisoyadi : ''}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Abone Sayacı:</label>
                                    <label class="col-xs-8 abonesayac" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->serino : ''}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Telefonu:</label>
                                    <label class="col-xs-8 telefon" style="padding-top: 7px">{{$servisbilgi ? (is_null($servisbilgi->telefon) ? '' : $servisbilgi->telefon) : ''}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Abone No:</label>
                                    <label class="col-xs-8 aboneno" style="padding-top: 7px">{{$servisbilgi ? (is_null($servisbilgi->aboneno) ? '' : $servisbilgi->aboneno) : ''}}</label>
                                </div>
                                <div class="form-group col-xs-12">
                                    <label class="control-label col-sm-2 col-xs-4">Adresi:</label>
                                    <label class="col-xs-8 adresi" style="padding-top: 7px">{{$servisbilgi ? (is_null($servisbilgi->faturaadresi) ? '' : $servisbilgi->faturaadresi) : ''}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Servis Nedeni:</label>
                                    <label class="col-xs-8 servisnedeni" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->isemritipi : ''}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Servis durumu:</label>
                                    <label class="col-xs-8 servisdurum" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->durum : ''}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Açılma Tarihi:</label>
                                    <label class="col-xs-8 acilmatarihi" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->acilmatarihi : ''}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Kapanma Tarihi:</label>
                                    <label class="col-xs-8 kapanmatarihi" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->kapanmatarihi : ''}}</label>
                                </div>
                                <div class="form-group col-xs-12">
                                    <label class="control-label col-sm-2 col-xs-4">Açıklama:</label>
                                    <label class="col-xs-8 aciklama" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->aciklama : ''}}</label>
                                </div>
                                <div class="form-group col-xs-12">
                                    <label class="control-label col-sm-2 col-xs-4">Sonuç:</label>
                                    <label class="col-xs-8 sonuc" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->sonuc : ''}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Servis Sayacı:</label>
                                    <label class="col-xs-8 servissayac" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->servissayaci : ''}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Personel:</label>
                                    <label class="col-xs-8 personel" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->personeladi : ''}}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <a href="{{ URL::to('sube/serviskayit')}}" class="btn default">Tamam</a>
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
