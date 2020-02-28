@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Gaz Kalibrasyon İstasyonu <small>Bilgi Düzenleme Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
    <link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::to('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" >
@stop

@section('page-js')
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/mekanikdatabase/form-validation-19.js') }}"></script>
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
$(document).ready(function() {
    $("select").on("select2-close", function () { $(this).valid(); });
    $('#form_sample').valid();
});
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-pencil"></i>Kalibrasyon İstasyonu Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('mekanikdatabase/istasyonduzenle/'.$istasyon->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-sm-2 col-xs-4">İstasyon Adı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><input type="text" id="istasyonadi" name="istasyonadi" value="{{Input::old('istasyonadi') ? Input::old('istasyonadi') :$istasyon->istasyonadi }}" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Test Edilen Sayaç Sayısı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><input type="text" id="sayacsayi" name="sayacsayi" value="{{Input::old('sayacsayi') ? Input::old('sayacsayi') :$istasyon->sayacsayi }}" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Sayaç Adları:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2 select2-offscreen" multiple="" tabindex="-1" id="sayacadi" name="sayacadi[]">
                            @foreach($sayacadlari as $sayacadi)
                                <option value="{{ $sayacadi->id }}">{{ $sayacadi->sayacadi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="sayacadlari" class="hide sayacadlari">
                        @if(Input::old('sayacadi'))
                            @foreach(Input::old('sayacadi') as $sayacadi)
                                {{$sayacadi}}
                            @endforeach
                        @else
                            <div id="sayacadiekli" class="hide sayacadiekli">{{ $istasyon->sayacadlari }}</div>
                        @endif
                    </div>
                </div>

                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn green">Kaydet</button>
                    <a href="{{ URL::to('mekanikdatabase/istasyon')}}" class="btn default">Vazgeç</a>
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
