@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Üretim Yeri <small>Ekleme Ekranı</small></h1>
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
<script src="{{ URL::to('pages/gazdatabase/form-validation-16.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar   
   FormValidationGazDatabase.init();
});
$(document).ready(function(){
    $("select").on("select2-close", function () { $(this).valid(); });
});
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Üretim Yeri Ekle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('gazdatabase/uretimyeriekle') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-sm-2 col-xs-4">Üretim Yeri: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><input type="text" id="yer" name="yer" value="{{Input::old('yer') }}" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Issue:</label>
                    <div class="col-xs-8">
                        <input type="text" id="issue" name="issue" value="{{Input::old('issue') }}"  maxlength="3" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Para Birimi: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="birim" name="birim" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($birimler as $birim)
                                @if(Input::old('birim')==$birim->id )
                            <option value="{{ $birim->id }}" selected>{{ $birim->birimi.' '.$birim->adi }}</option>
                                @else
                            <option value="{{ $birim->id }}">{{ $birim->birimi.' '.$birim->adi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn green">Kaydet</button>
                    <a href="{{ URL::to('gazdatabase/uretimyeri')}}" class="btn default">Vazgeç</a>
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