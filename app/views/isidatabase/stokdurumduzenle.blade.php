@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Isı Stok-Parça Grubu <small>Bilgi Düzenleme Ekranı</small></h1>
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
<script src="{{ URL::to('pages/isidatabase/form-validation-6.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar   
   FormValidationIsiDatabase.init();
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
            <i class="fa fa-pencil"></i>Stok-Parça Grubu Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('isidatabase/stokdurumduzenle/'.$stokdurum->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-sm-2 col-xs-4">Parça Adı:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="parca" name="parca" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($parcalar as $parca)
                                @if((Input::old('parca') ? Input::old('parca') :$stokdurum->degisenler_id)==$parca->id )
                                    <option value="{{ $parca->id }}" selected>{{ $parca->tanim }}</option>
                                @else
                                    <option value="{{ $parca->id }}">{{ $parca->tanim }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Stok Adı:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="stokadi" name="stokadi" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($netsisstokkodlari as $netsisstokkod)
                                @if((Input::old('stokadi') ? Input::old('stokadi') :$stokdurum->netsisstokkod_id)==$netsisstokkod->id )
                                    <option value="{{ $netsisstokkod->id }}" selected>{{ $netsisstokkod->kodu.' - '.$netsisstokkod->adi }}</option>
                                @else
                                    <option value="{{ $netsisstokkod->id }}">{{ $netsisstokkod->kodu.' - '.$netsisstokkod->adi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Depo Kodu:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="depokodu" name="depokodu" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($netsisdepolar as $netsisdepo)
                                @if((Input::old('depokodu') ? Input::old('depokodu') :$stokdurum->depokodu)==$netsisdepo->kodu )
                                    <option value="{{ $netsisdepo->kodu }}" selected>{{ $netsisdepo->kodu.' - '.$netsisdepo->adi }}</option>
                                @else
                                    <option value="{{ $netsisdepo->kodu }}">{{ $netsisdepo->kodu.' - '.$netsisdepo->adi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Arıza Kayıdında Düşülecek Adet:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><input type="text" id="adet" name="adet" data-required="1" class="form-control" value="{{$stokdurum->adet}}"/>
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn green">Kaydet</button>
                    <a href="{{ URL::to('isidatabase/stokdurum')}}" class="btn default">Vazgeç</a>
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
