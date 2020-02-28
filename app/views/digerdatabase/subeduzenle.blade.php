@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Şube <small>Düzenleme Ekranı</small></h1>
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
<script src="{{ URL::to('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/digerdatabase/form-validation-8.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar   
   FormValidationDigerDatabase.init();
});
$(document).ready(function(){
    $('#linkedserver').on('change',function(){
        $('#linkeddurum').val(0);
        $('.linkeddurum').html('');
        $('.linkeddurum').css('color','white');
    });
    $('#linkedserverabone').on('change',function(){
        $('#linkedabonedurum').val(0);
        $('.linkedabonedurum').html('');
        $('.linkedabonedurum').css('color','white');
    });
    $('.test').click(function () {
        var linked = $("#linkedserver").val();
        if (linked !== "") {
            $.blockUI();
            $.getJSON("{{ URL::to('digerdatabase/linkedtest') }}", {linked: linked}, function (event) {
                if (event.durum === 0) // başarılı
                {
                    $('#linkeddurum').val(1);
                    $('.linkeddurum').html('<b>Başarılı</b>');
                    $('.linkeddurum').css('color','green');
                }else{ //başarısız
                    $('#linkeddurum').val(0);
                    $('.linkeddurum').html('<b>Başarısız</b>');
                    $('.linkeddurum').css('color','red');
                }
                $.unblockUI();
            });
        }
    });
    $('.testabone').click(function () {
        var linked = $("#linkedserverabone").val();
        if (linked !== "") {
            $.blockUI();
            $.getJSON("{{ URL::to('digerdatabase/linkedtest') }}", {linked: linked}, function (event) {
                if (event.durum === 0) // başarılı
                {
                    $('#linkedabonedurum').val(1);
                    $('.linkedabonedurum').html('<b>Başarılı</b>');
                    $('.linkedabonedurum').css('color','green');
                }else{ //başarısız
                    $('#linkedabonedurum').val(0);
                    $('.linkedabonedurum').html('<b>Başarısız</b>');
                    $('.linkedabonedurum').css('color','red');
                }
                $.unblockUI();
            });
        }
    });
    $("select").on("select2-close", function () {$(this).valid();});
    $('#form_sample').valid();
});
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-pencil"></i>Şube Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('digerdatabase/subeduzenle/'.$sube->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-sm-2 col-xs-4">Şube Adı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><input type="text" id="adi" name="adi" value="{{Input::old('adi') ? Input::old('adi') : $sube->adi }}" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Netsis Cari Adı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="netsiscari" name="netsiscari" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($netsiscariler as $netsiscari)
                                @if((Input::old('netsiscari') ? Input::old('netsiscari') : $sube->netsiscari_id )==$netsiscari->id )
                                    <option value="{{ $netsiscari->id }}" selected>{{ $netsiscari->cariadi }}</option>
                                @else
                                    <option value="{{ $netsiscari->id }}">{{ $netsiscari->cariadi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Şube Kodu: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="subekod" name="subekod" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($subekodlari as $subekod)
                                @if((Input::old('subekod') ? Input::old('subekod') : $sube->subekodu )==$subekod->SUBE_KODU )
                                    <option value="{{ $subekod->SUBE_KODU }}" selected>{{ $subekod->SUBE_KODU.'- '.$subekod->UNVAN }}</option>
                                @else
                                    <option value="{{ $subekod->SUBE_KODU }}">{{ $subekod->SUBE_KODU.'- '.$subekod->UNVAN }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Şube Deposu: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="depo" name="depo" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($netsisdepolar as $netsisdepo)
                                @if((Input::old('depo') ? Input::old('depo') : $sube->netsisdepolar_id )==$netsisdepo->id )
                                    <option value="{{ $netsisdepo->id }}" selected>{{ $netsisdepo->kodu.'- '.$netsisdepo->adi }}</option>
                                @else
                                    <option value="{{ $netsisdepo->id }}">{{ $netsisdepo->kodu.'- '.$netsisdepo->adi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Sayaç Türleri: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2 select2-offscreen" multiple="" tabindex="-1" id="sayactur" name="sayactur[]">
                            @foreach($sayacturleri as $sayactur)
                                <option value="{{ $sayactur->id }}">{{ $sayactur->tur }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if(Input::old('sayactur'))
                    <div id="sayacturleri" class="hide sayacturleri">
                        @foreach(Input::old('sayactur') as $sayactur)
                            {{$sayactur}}
                        @endforeach
                    </div>
                @else
                    <div id="sayacturekli" class="hide sayacturekli">{{ $sube->sayactur }}</div>
                @endif
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Sayaç Adları: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2 select2-offscreen" multiple="" tabindex="-1" id="sayacadi" name="sayacadi[]">
                            @foreach($sayacadlari as $sayacadi)
                                <option value="{{ $sayacadi->id }}">{{ $sayacadi->sayacadi }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if(Input::old('sayacadi'))
                    <div id="sayacadlari" class="hide sayacadlari">
                        @foreach(Input::old('sayacadi') as $sayacadi)
                            {{ $sayacadi }}
                        @endforeach
                    </div>
                @else
                    <div id="sayacadiekli" class="hide sayacadiekli">{{ $sube->sayacadlari }}</div>
                @endif
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">SQL Server Şube Bağlantı Adı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-6">
                        <i class="fa"></i><input type="text" id="linkedserver" name="linkedserver" value="{{Input::old('linkedserver') ? Input::old('linkedserver') : $sube->subelinked }}" data-required="1" class="form-control">
                    </div>
                    <div class="col-xs-2">
                        <a class="btn red test">Test Et</a><label class="linkeddurum" style="margin-left:5px;{{(Input::old('linkeddurum') ? Input::old('linkeddurum') : $sube->linkeddurum)==1 ? "color:green;" : "color:red;"}}">{{(Input::old('linkeddurum') ? Input::old('linkeddurum') : $sube->linkeddurum)==1 ? "<b>Başarılı</b>" : "<b>Başarısız</b>"}}</label>
                    </div>
                    <input id="linkeddurum" name="linkeddurum" class="hide" value="{{Input::old('linkeddurum') ? Input::old('linkeddurum') : $sube->linkeddurum}}">
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">SQL Server Belediye Bağlantı Adı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-6">
                        <i class="fa"></i><input type="text" id="linkedserverabone" name="linkedserverabone" value="{{Input::old('linkedserverabone') ? Input::old('linkedserverabone') : $sube->bellinked }}" data-required="1" class="form-control">
                    </div>
                    <div class="col-xs-2">
                        <a class="btn red testabone">Test Et</a><label class="linkedabonedurum" style="margin-left:5px;{{(Input::old('linkedabonedurum') ? Input::old('linkedabonedurum') : $sube->linkedabonedurum)==1 ? "color:green;" : "color:red;"}}">{{(Input::old('linkedabonedurum') ? Input::old('linkedabonedurum') : $sube->linkedabonedurum)==1 ? "<b>Başarılı</b>" : "<b>Başarısız</b>"}}</label>
                    </div>
                    <input id="linkedabonedurum" name="linkedabonedurum" class="hide" value="{{Input::old('linkedabonedurum') ? Input::old('linkedabonedurum') : $sube->linkedabonedurum}}">
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Durumu: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="aktif" name="aktif" tabindex="-1" title="">
                            <option value="0" {{(Input::old('aktif') ? Input::old('aktif') : $sube->aktif)==0 ? 'selected' : ''}}>Pasif</option>
                            <option value="1" {{(Input::old('aktif') ? Input::old('aktif') : $sube->aktif)==1 ? 'selected' : ''}}>Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn green">Kaydet</button>
                    <a href="{{ URL::to('digerdatabase/sube')}}" class="btn default">Vazgeç</a>
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
