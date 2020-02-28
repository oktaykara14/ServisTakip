@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Netsis Servis Stok Kodu <small>Bilgi Düzenleme Ekranı</small></h1>
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
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
    $('#form_sample').valid();
});
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-pencil"></i>Servis Stok Kodu Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('digerdatabase/stokkodduzenle/'.$stokkod->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-sm-2 col-xs-4">Stok Kodu:</label>
                    <label class="col-xs-8 stokkod" style="padding-top: 9px">{{$stokkod->stokkodu}}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Stok Adı:</label>
                    <label class="col-xs-8 stokadi" style="padding-top: 9px">{{$stokkod->stokadi}}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Servis Kodu:</label>
                    <label class="col-xs-8 serviskod" style="padding-top: 9px">{{$stokkod->servisbirimi}}</label>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Servis Birimi:</label>
                    <div class="col-xs-8">
                        <select class="form-control select2me select2-offscreen" id="servis" name="servis" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($servisler as $servis)
                                @if($servis->id==$stokkod->servisid)
                                    <option value="{{$servis->id}}" selected>{{$servis->servisadi}}</option>
                                @else
                                    <option value="{{$servis->id}}" >{{$servis->servisadi}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Durumu:</label>
                    <div class="col-xs-8">
                        <select class="form-control select2me select2-offscreen" id="durum" name="durum" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            <option value="0" {{ $stokkod->koddurum==0 ? 'selected' : '' }}>Pasif</option>
                            <option value="1" {{ $stokkod->koddurum==1 ? 'selected' : '' }}>Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn green">Kaydet</button>
                    <a href="{{ URL::to('digerdatabase/netsisstokkod')}}" class="btn default">Vazgeç</a>
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
