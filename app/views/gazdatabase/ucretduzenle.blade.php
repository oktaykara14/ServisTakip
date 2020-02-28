@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Gaz Sayaç Parça Ücreti <small>Bilgi Düzenleme Ekranı</small></h1>
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
<script src="{{ URL::to('assets/global/plugins/jquery-maskmoney/jquery.maskMoney.min.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/gazdatabase/form-validation-10.js') }}"></script>
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
    $('#parabirimi').on('change', function () {
        var birimid = $(this).val();
        var yerid = $('#uretimyer').val();
        if(birimid!=="") {
            var birim = $(this).find("option:selected").data('id');
            $('#ucret').maskMoney({suffix: ' '+birim,affixesStay:false, allowZero:true});
        }else if(yerid!==""){
            $.blockUI();
            $.getJSON("{{ URL::to('backend/yerparabirimi') }}",{id:yerid},function(event){
                if(event.durum){
                    $('#ucret').maskMoney({suffix: ' '+event.parabirimi,affixesStay:false, allowZero:true});
                }else{
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
        }else{
            $('#ucret').maskMoney({suffix: ' €',affixesStay:false, allowZero:true});
        }
    });
    $('#uretimyer').on('change', function () {
        var yerid = $(this).val();
        var birimid = $('#parabirimi').val();
        if(birimid!=="") {
            var birim = $('#parabirimi').find("option:selected").data('id');
            $('#ucret').maskMoney({suffix: ' '+birim,affixesStay:false, allowZero:true});
        }else if(yerid!==""){
            $.blockUI();
            $.getJSON("{{ URL::to('backend/yerparabirimi') }}",{id:yerid},function(event){
                if(event.durum){
                    $('#ucret').maskMoney({suffix: ' '+event.parabirimi,affixesStay:false, allowZero:true});
                }else{
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
        }else{
            $('#ucret').maskMoney({suffix: ' €',affixesStay:false, allowZero:true});
        }
    });

    var uretimyer = $('#uretimyer').val();
    var parabirimi = $('#parabirimi').val();
    if(parabirimi!==""){
        var birim = $('#parabirimi').find("option:selected").data('id');
        $('#ucret').maskMoney({suffix: ' '+birim,affixesStay:false, allowZero:true});
    }else if(uretimyer!==""){
        $.blockUI();
        $.getJSON("{{ URL::to('backend/yerparabirimi') }}",{id:uretimyer},function(event){
            if(event.durum){
                $('#ucret').maskMoney({suffix: ' '+event.parabirimi,affixesStay:false, allowZero:true});
            }else{
                toastr[event.type](event.text, event.title);
            }
            $.unblockUI();
        });
    }else {
        $('#ucret').maskMoney({suffix: ' €', affixesStay: false, allowZero:true});
    }
    $("select").on("select2-close", function () { $(this).valid(); });
    $('#form_sample').valid();
});
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-pencil"></i>Sayaç Parça Ücreti Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('gazdatabase/ucretduzenle/'.$parcaucret->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="uretimyer" name="uretimyer" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($uretimyerleri as $uretimyer)
                                @if((Input::old('uretimyer') ? Input::old('uretimyer') :$parcaucret->uretimyer_id)==$uretimyer->id )
                            <option value="{{ $uretimyer->id }}" selected>{{ $uretimyer->yeradi }}</option>
                                @else
                            <option value="{{ $uretimyer->id }}">{{ $uretimyer->yeradi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Değişen Parça: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="parca" name="parca" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($parcalar as $parca)
                                @if((Input::old('parca') ? Input::old('parca') :$parcaucret->degisenler_id)==$parca->id )
                            <option value="{{ $parca->id }}" selected>{{ $parca->tanim }}</option>
                                @else
                            <option value="{{ $parca->id }}">{{ $parca->tanim }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Para Birimi: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="parabirimi" name="parabirimi" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($parabirimleri as $parabirimi)
                                @if((Input::old('parabirimi') ? Input::old('parabirimi') :$parcaucret->parabirimi_id)==$parabirimi->id )
                                    <option data-id="{{$parabirimi->birimi}}" value="{{ $parabirimi->id }}" selected>{{ $parabirimi->adi }}</option>
                                @else
                                    <option data-id="{{$parabirimi->birimi}}" value="{{ $parabirimi->id }}">{{ $parabirimi->adi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Ücreti:</label>
                    <div class="col-xs-8">
                        <input type="tel" id="ucret" name="ucret" value="{{(Input::old('ucret') ? Input::old('ucret') :number_format($parcaucret->fiyat,2,'.','')) }}" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn green">Kaydet</button>
                    <a href="{{ URL::to('gazdatabase/parcaucret')}}" class="btn default">Vazgeç</a>
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
