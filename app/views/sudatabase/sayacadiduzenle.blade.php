@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Su Sayaç Adı <small>Bilgi Düzenleme Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css">    
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}" type="text/javascript"></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/sudatabase/form-validation-2.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar   
   FormValidationSuDatabase.init();
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
            <i class="fa fa-pencil"></i>Sayaç Adı Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('sudatabase/sayacadiduzenle/'.$sayacadi->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-sm-2 col-xs-4">Sayaç Adı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-6">
                        <i class="fa"></i><input type="text" id="adi" name="adi" value="{{Input::old('adi') ? Input::old('adi') : $sayacadi->sayacadi }}" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Sayaç Tipi: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-6">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="sayactip" name="sayactip" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($sayactipleri as $sayactip)
                                @if((Input::old('sayactip') ? Input::old('sayactip') :$sayacadi->sayactip_id)==$sayactip->id)
                            <option value="{{ $sayactip->id }}" selected>{{ $sayactip->sayacmarka->marka.' '.$sayactip->tipadi }}</option>
                                @else
                            <option value="{{ $sayactip->id }}">{{ $sayactip->sayacmarka->marka.' '.$sayactip->tipadi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                        <label class="control-label col-sm-2 col-xs-4">Çapı Önemli mi?</label>
                        <div class="col-xs-6">
                                <div class="bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-off bootstrap-switch-animate" style="width: 116px;">
                                    <div class="bootstrap-switch-container" style="width: 116px; margin-left: 0;">
                                    <input type="checkbox" id="cap" name="cap" class="make-switch" data-on-color="success" data-off-color="warning" data-on-text="Evet" data-off-text="Hayır" @if(Input::old('cap') ? Input::old('cap') :$sayacadi->cap) checked @else  @endif></div>
                                </div>
                        </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Beyannamede Olacak mı?</label>
                    <div class="col-xs-6">
                        <div class="bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-off bootstrap-switch-animate" style="width: 116px;">
                            <div class="bootstrap-switch-container" style="width: 116px; margin-left: 0;">
                                <input type="checkbox" id="beyanname" name="beyanname" class="make-switch" data-on-color="success" data-off-color="warning" data-on-text="Evet" data-off-text="Hayır" @if(Input::old('beyanname') ? Input::old('beyanname') :$sayacadi->beyanname) checked @endif></div>
                        </div>
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn green">Kaydet</button>
                    <a href="{{ URL::to('sudatabase/sayacadi')}}" class="btn default">Vazgeç</a>
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
