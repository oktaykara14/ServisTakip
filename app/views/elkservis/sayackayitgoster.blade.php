@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Elektrik Sayaç Kayıt <small>Bilgi Ekranı</small></h1>
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
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Geliş Tarihi:</label>
                    <label class="col-sm-3 col-xs-8" style="padding-top: 7px">{{ $sayacgelen->depotarihi ? date("d-m-Y", strtotime($sayacgelen->depotarihi)) : '' }}</label>
                    <label class="control-label col-sm-2 col-xs-4">Geliş Yeri:</label>
                    <label class="col-sm-3 col-xs-8" style="padding-top: 7px">
                            @foreach($uretimyerleri as $uretimyer) @if( $sayacgelen->uretimyer_id ==$uretimyer->id ) {{ $uretimyer->yeradi }} @endif @endforeach
                    </label>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Cari İsim:</label>
                    <label class="col-sm-8 col-xs-8" style="padding-top: 7px">{{ $netsiscari->carikod.' - '.$netsiscari->cariadi }} </label>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">İstek:</label>
                    <label class="col-sm-8 col-xs-8" style="padding-top: 7px">{{ $servisstokkod->stokadi }}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Sayaç Adı:</label>
                    <label class="col-sm-3 col-xs-8" style="padding-top: 7px">{{ $sayacadi->sayacadi }}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Seri No:</label>
                    <label class="col-sm-3 col-xs-8" style="padding-top: 7px">{{ $sayacgelen->serino }}</label>
                    @if($sayacgelen->servistakip->eskiserino)
                        <label class="control-label col-sm-2 col-xs-4">Eski Seri No:</label>
                        <label class="col-sm-3 col-xs-8" style="padding-top: 7px">{{ $sayacgelen->servistakip->eskiserino }}</label>
                    @endif
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <a href="{{ URL::to('elkservis/sayackayit')}}" class="btn default">Tamam</a>
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
