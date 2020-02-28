@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Netsis Cari <small> Bilgi Detayı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
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
            <i class="fa fa-pencil"></i>Netsis Cari Bilgi Detayı
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="" id="form_sample" class="form-horizontal" novalidate="novalidate">
            <div class="form-body">
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Cari Kodu:</label>
                    <label class="col-sm-9 col-xs-8 carikod" style="padding-top: 9px">{{$netsiscari->carikod}}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Cari Adı:</label>
                    <label class="col-sm-9 col-xs-8 cariadi" style="padding-top: 9px">{{$netsiscari->cariadi}} <label style="color: red">{{ $netsiscari->aciklama }}</label></label>
                </div>
                <div class="form-group">
                    <label class="control-label col-xs-2">Telefon:</label>
                    <label class="col-xs-4 telefon" style="padding-top: 9px">{{$netsiscari->telefon}}</label>
                    <label class="control-label col-xs-2">Email:</label>
                    <label class="col-xs-4 email" style="padding-top: 9px">{{$netsiscari->email}}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-xs-2">Vergi Dairesi:</label>
                    <label class="col-xs-4 vergidairesi" style="padding-top: 9px">{{$netsiscari->vergidairesi}}</label>
                    <label class="control-label col-xs-2">Vergi No:</label>
                    <label class="col-xs-4 vergino" style="padding-top: 9px">{{$netsiscari->vergino}}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Adresi:</label>
                    <label class="col-sm-9 col-xs-8 adres" style="padding-top: 9px">{{$netsiscari->adres}}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-xs-2">İl:</label>
                    <label class="col-xs-4 il" style="padding-top: 9px">{{$netsiscari->il}}</label>
                    <label class="control-label col-xs-2">İlçe:</label>
                    <label class="col-xs-4 ilce" style="padding-top: 9px">{{$netsiscari->ilce}}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-xs-2">Posta Kodu:</label>
                    <label class="col-xs-4 vergidairesi" style="padding-top: 9px">{{$netsiscari->postakodu}}</label>
                    <label class="control-label col-xs-2">Vade Günü:</label>
                    <label class="col-xs-4 vergino" style="padding-top: 9px">{{$netsiscari->vadegunu}}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-xs-2">Yetkili Adı:</label>
                    <label class="col-xs-4 vergidairesi" style="padding-top: 9px">{{$netsiscari->yetkiliadi}}</label>
                    <label class="control-label col-xs-2">Yetkili Telefonu:</label>
                    <label class="col-xs-4 vergino" style="padding-top: 9px">{{$netsiscari->yetkilitel}}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-xs-2">Cari Tipi:</label>
                    <label class="col-xs-4 caritipi" style="padding-top: 9px">{{$netsiscari->gcaritipi}}</label>
                    <label class="control-label col-xs-2">Cari Durumu:</label>
                    <label class="col-xs-4 durum" style="padding-top: 9px">{{$netsiscari->gdurum}}</label>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <a href="{{ URL::to('digerdatabase/netsiscari')}}" class="btn green">Tamam</a>
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
