@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Sayaç Kayıt <small>Bilgi Ekranı</small></h1>
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
            <i class="fa fa-plus"></i>Sayac Kayıdı Bilgisi
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
                    <label class="control-label col-xs-4">Kargo Firması:</label>
                    <label class="col-sm-8 col-xs-8" style="padding-top: 7px">{{ $sayackayit->kargofirma->kargoadi }} </label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Takip Numarası:</label>
                    <label class="col-sm-8 col-xs-8" style="padding-top: 7px">{{ $sayackayit->belgeno }} </label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Cari Bilgisi:</label>
                    <label class="col-sm-8 col-xs-8" style="padding-top: 7px">{{ $sayackayit->netsiscari->cariadi }} </label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Gönderme Tarihi:</label>
                    <label class="col-sm-3 col-xs-8" style="padding-top: 7px">{{ $sayackayit->gondermetarihi ? date("d-m-Y", strtotime($sayackayit->gondermetarihi)) : '' }}</label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4" > Ekli Olan Sayaçlar: </label>
                    <label class="col-xs-8" style="padding-top: 9px">{{$sayackayit->adet .' Adet'}}</label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Sayaç Türü:</label>
                    <label class="col-xs-8" style="padding-top: 9px">{{$sayackayit->sayactur}}</label>
                </div>
                <div class="panel-group accordion sayaclar col-xs-12" id="accordion1">
                    @for($i=0;$i<(int)($sayackayitbilgi->count());$i++)
                        <div class="panel panel-default sayaclar_ek"><input class="no hide" value="{{$i}}"/><input class="adet hide" value="1"/>
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_{{$i}}">{{$sayackayitbilgi[$i]->serino}} </a>
                                </h4>
                            </div>
                            <div id="collapse_{{$i}}" class="panel-collapse in">
                                <div class="panel-body">
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <label class="col-xs-4 control-label">Seri No:</label>
                                        <label class="col-sm-8 col-xs-8" style="padding-top: 7px">{{ $sayackayitbilgi[$i]->serino }} </label>
                                    </div>
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <label class="col-xs-4 control-label">Arıza Nedeni:</label>
                                        <label class="col-sm-8 col-xs-8" style="padding-top: 7px">{{ $sayackayitbilgi[$i]->arizanedeni }} </label>
                                    </div>
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <label class="col-xs-4 control-label">Kabul Durumu:</label>
                                        <label class="col-sm-8 col-xs-8" style="padding-top: 7px">{{ $sayackayitbilgi[$i]->gdurum }} </label>
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
                    <a href="{{ URL::to('abone/sayackayit')}}" class="btn default">Tamam</a>
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
