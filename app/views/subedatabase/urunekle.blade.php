@extends('layout.master')

@section('page-title')
<!--suppress JSValidateTypes -->
<div class="page-title">
    <h1>Ürün <small>Ekleme Ekranı</small></h1>
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
<script src="{{ URL::to('pages/subedatabase/form-validation-1.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar   
    FormValidationSubeDatabase.init();
});
$(document).ready(function(){
    $('#baglanti').on('change', function () {
        if ($('#baglanti').attr('checked')) {
            $(".baglantisayac").removeClass('hide');
        } else {
            $(".baglantisayac").addClass('hide');
        }
    });
    $('#sayacadi').on('change', function () {
        var id = $(this).val();
        if(id!==""){
            var capdurum = $(this).find("option:selected").data('id');
            if (capdurum === 0) //cap kontrol edilmiyor
            {
                $("#sayaccapi").select2("val",1).valid();
                $(".sayaccapi").prop("disabled", true);
            } else {
                $("#sayaccapi").select2("val","").valid();
                $(".sayaccapi").prop("disabled", false);
            }
        }else{
            $("#sayaccapi").select2("val","").valid();
            $(".sayaccapi").prop("disabled", true);
        }
        $(this).valid();
    });
    $('#ekstra').on('change', function () {
        if ($('#ekstra').attr('checked')) {
            $(".ekstra").removeClass('hide');
        } else {
            $(".ekstra").addClass('hide');
        }
    });
    $('#parabirimi').on('change', function () {
        var birimid = $(this).val();
        if(birimid!=="") {
            var birim = $(this).find("option:selected").data('id');
            $('#fiyat').maskMoney({suffix: ' '+birim,affixesStay:false, allowZero:true});
        }else{
            $('#fiyat').maskMoney({suffix: ' €',affixesStay:false, allowZero:true});
        }
    });
    $("select").on("select2-close", function () { $(this).valid(); });
});
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Ürün Ekle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('subedatabase/urunekle') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <input class="hide" id="subekodu" name="subekodu" value="{{$sube ? $sube->subekodu : 1}}">
                    <label class="control-label col-sm-2 col-xs-4">Ürün Adı:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-6">
                        <i class="fa"></i><input type="text" id="urunadi" name="urunadi" value="{{Input::old('urunadi') }}" maxlength="100" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Stok Kodu: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-6">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="stokkod" name="stokkod" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($netsisstokkodlari as $stokkod)
                                @if(Input::old('stokkod')==$stokkod->id )
                            <option value="{{ $stokkod->id }}" selected>{{ $stokkod->kodu.' '.$stokkod->adi }}</option>
                                @else
                            <option value="{{ $stokkod->id }}">{{ $stokkod->kodu.' '.$stokkod->adi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-xs-offset-4 col-sm-8 col-xs-8" style="margin-top: 5px;font-size: 15px">
                        <input type="checkbox" id="kontrol" name="kontrol" class="kontrol" {{Input::old('kontrol') ? 'checked' : ''}}/>
                        Stok Kontrolü Olacak (Eksiye Düşme Olmayacak)
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-xs-offset-4 col-sm-8 col-xs-8" style="margin-top: 5px;font-size: 15px">
                        <input type="checkbox" id="baglanti" name="baglanti" class="baglanti" {{Input::old('baglanti') ? 'checked' : ''}}/>
                        Abone Sayacı ile Bağlantılı (Aboneye Satılan Sayaç ise Seçilecektir)
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-xs-offset-4 col-sm-8 col-xs-8" style="margin-top: 5px;font-size: 15px">
                        <input type="checkbox" id="ekstra" name="ekstra" class="ekstra" {{Input::old('ekstra') ? 'checked' : ''}}/>
                        Servis Kayıdında Kullanılacak (Ekstra Ücretlerde gözükecek)
                    </div>
                </div>
                <div class="form-group baglantisayac hide">
                    <div class="form-group col-xs-12">
                        <label class="col-sm-2 col-xs-4 control-label">Sayaç Adı: <span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-6">
                            <i class="fa"></i><select class="form-control select2me sayacadi " id="sayacadi" name="sayacadi" tabindex="-1" title="">
                                <option value="">Seçiniz...</option>
                                @foreach($sayacadlari as $sayacadi)
                                    @if((Input::old('sayacadi'))==$sayacadi->id)
                                        <option data-id="{{ $sayacadi->cap }}" value="{{ $sayacadi->id }}" selected>{{ $sayacadi->sayacadi }}</option>
                                    @else
                                        <option data-id="{{ $sayacadi->cap }}" value="{{ $sayacadi->id }}" >{{ $sayacadi->sayacadi }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-12">
                        <label class="col-sm-2 col-xs-4 control-label">Sayaç Çapı: <span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-6">
                            <i class="fa"></i><select class="form-control select2me sayaccapi" id="sayaccapi" name="sayaccapi" tabindex="-1" title="">
                                <option value="">Seçiniz...</option>
                                @foreach($sayaccaplari as $sayaccapi)
                                    @if((Input::old('sayaccapi'))==$sayaccapi->id)
                                        <option value="{{ $sayaccapi->id }}" selected>{{ $sayaccapi->capadi }}</option>
                                    @else
                                        <option value="{{ $sayaccapi->id }}">{{ $sayaccapi->capadi }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group ekstra hide">
                    <label class="control-label col-sm-2 col-xs-4">Fiyatı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-6">
                        <i class="fa"></i><input type="tel" id="fiyat" name="fiyat" value="{{Input::old('fiyat') }}" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Para Birimi: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-6">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="parabirimi" name="parabirimi" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($parabirimleri as $parabirimi)
                                @if(Input::old('parabirimi')==$parabirimi->id )
                                    <option data-id="{{$parabirimi->birimi}}" value="{{ $parabirimi->id }}" selected>{{ $parabirimi->adi }}</option>
                                @else
                                    <option data-id="{{$parabirimi->birimi}}" value="{{ $parabirimi->id }}">{{ $parabirimi->adi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Netsis Depo: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-6">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="netsisdepo" name="netsisdepo" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($netsisdepolar as $netsisdepo)
                                @if(Input::old('netsisdepo')==$netsisdepo->kodu )
                                    <option value="{{ $netsisdepo->kodu }}" selected>{{ $netsisdepo->kodu.' - '.$netsisdepo->adi }}</option>
                                @else
                                    <option value="{{ $netsisdepo->kodu }}">{{ $netsisdepo->kodu.' - '.$netsisdepo->adi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="col-sm-2 col-xs-4 control-label">Durumu:</label>
                    <div class="col-xs-6">
                        <select class="form-control select2me durum" id="durum" name="durum" tabindex="-1" title="">
                            <option value="1" {{(Input::old('durum'))==1 ? 'selected' : ''}}>Aktif</option>
                            <option value="0" {{(Input::old('durum'))==0 ? 'selected' : ''}}>Pasif</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn green">Kaydet</button>
                    <a href="{{ URL::to('subedatabase/urunler')}}" class="btn default">Vazgeç</a>
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
