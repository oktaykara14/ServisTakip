@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Şube Servis Kayıdı <small>Düzenleme Ekranı</small></h1>
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
<script src="{{ URL::to('assets/global/plugins/jquery-maskplugin/src/jquery.mask.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.tr.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery-maskmoney/jquery.maskMoney.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript" ></script>

@stop

@section('page-script')
<script src="{{ URL::to('pages/sube/form-validation-8.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
   FormValidationSube.init();
});
</script>
<script>
    $(document).ready(function() {
        var birim = $('#birim').val();
        $('#ilkendeks').maskMoney({suffix: ' '+birim,affixesStay:true,allowNegative: true, allowZero:true,precision:3});
        $('#sonendeks').maskMoney({suffix: ' '+birim,affixesStay:true,allowNegative: true, allowZero:true,precision:3});
        $('#sayacborcu').maskMoney({suffix: ' '+birim,affixesStay:true,allowNegative: true, allowZero:true,precision:3});
        $('#smsvar').on('change', function () {
            if ($('#smsvar').attr('checked')) {
                $('.bilgilendirme').removeClass('hide');
                $('#ilgilitel').rules("add", {required:true,minlength:16});
                $('#smsgonder').val(1);
            }else{
                $('.bilgilendirme').addClass('hide');
                $('#ilgilitel').rules("remove");
                $('#smsgonder').val(0);
            }
        });
        $('.toplamfark').html('0.000 '+birim);

        $('#servissayaci').on('change', function () {
            if ($('#servissayaci').attr('checked')) {
                $(".servissayaci").removeClass('hide');
                $('#takilmatarihi').rules("add", "required");
            } else {
                $(".servissayaci").addClass('hide');
                $('#takilmatarihi').rules("remove");
            }
        });
        $('#ilkendeks').on('change', function () {
            var birim = $('#birim').val();
            var ilkendeks=$('#ilkendeks').maskMoney('unmasked')[0];
            var sonendeks=$('#sonendeks').maskMoney('unmasked')[0];
            var sayacborcu=$('#sayacborcu').maskMoney('unmasked')[0];
            $('#ilkendeksi').val(parseFloat(ilkendeks));
            if(sonendeks!==0){
                var fark = sonendeks-ilkendeks+sayacborcu;
                $('.toplamfark').html(((fark.toFixed(3)).replace('.',','))+' '+birim);
            }else{
                $('.toplamfark').html('0,000 '+birim);
            }
        });
        $('#sonendeks').on('change', function () {
            var birim = $('#birim').val();
            var ilkendeks=$('#ilkendeks').maskMoney('unmasked')[0];
            var sonendeks=$('#sonendeks').maskMoney('unmasked')[0];
            var sayacborcu=$('#sayacborcu').maskMoney('unmasked')[0];
            $('#sonendeksi').val(parseFloat(sonendeks));
            if(sonendeks!==0){
                var fark = sonendeks-ilkendeks+sayacborcu;
                $('.toplamfark').html(((fark.toFixed(3)).replace('.',','))+' '+birim);
            }else{
                $('.toplamfark').html('0,000 '+birim);
            }
        });
        $('#sayacborcu').on('change', function () {
            var birim = $('#birim').val();
            var ilkendeks=$('#ilkendeks').maskMoney('unmasked')[0];
            var sonendeks=$('#sonendeks').maskMoney('unmasked')[0];
            var sayacborcu=$('#sayacborcu').maskMoney('unmasked')[0];
            $('#sayacborc').val(parseFloat(sayacborcu));
            if(sonendeks!==0){
                var fark = sonendeks-ilkendeks+sayacborcu;
                $('.toplamfark').html(((fark.toFixed(3)).replace('.',','))+' '+birim);
            }else{
                $('.toplamfark').html('0,000 '+birim);
            }
        });
        $('#ilgilitel').on('change', function () {
            var ilgilitel=$('#ilgilitel').val().replace(/\D+/g, '');
            $('#ilgilitelefonu').val(ilgilitel);
        });
        $('#tipi').on('change',function () {
            var tipi = $(this).val();
            var durum = $('#durum').val();
            if(durum==="1" && tipi!=="2"){
                $('.servissayacison').removeClass('hide');
            } else {
                $('.servissayacison').addClass('hide');
            }
            if(tipi==="2"){
                $('.sokulmedurumu').removeClass('hide');
                if($('#sokulmedurumu').attr('checked')){
                    $('.bilgilendirme').removeClass('hide');
                    $('#ilgilitel').rules("add", {required:true,minlength:16});
                    $('#smsgonder').val(1);
                    $('#smsvar').prop('checked',true);
                    $.uniform.update();
                }else{
                    if(durum==="1"){
                        $('.bilgilendirme').removeClass('hide');
                        $('#ilgilitel').rules("add", {required:true,minlength:16});
                        $('#smsgonder').val(1);
                        $('#smsvar').prop('checked',true);
                        $.uniform.update();
                    }else{
                        $('.bilgilendirme').addClass('hide');
                        $('#ilgilitel').rules("remove");
                        $('#smsgonder').val(0);
                    }
                }
            }else{
                $('.sokulmedurumu').addClass('hide');
                $('#sokulmedurumu').prop('checked',false);
                $.uniform.update();
                if(durum==="1"){
                    $('.bilgilendirme').removeClass('hide');
                    $('#ilgilitel').rules("add", {required:true,minlength:16});
                    $('#smsgonder').val(1);
                    $('#smsvar').prop('checked',true);
                    $.uniform.update();
                }else{
                    $('.bilgilendirme').addClass('hide');
                    $('#ilgilitel').rules("remove");
                    $('#smsgonder').val(0);
                }
            }
        });
        $('#durum').on('change',function () {
            var durum = $(this).val();
            var tipi = $('#tipi').val();
            if(durum==="1" && tipi!=="2"){
                $('.servissayacison').removeClass('hide');
            } else {
                $('.servissayacison').addClass('hide');
            }
            if(tipi==="2"){
                $('.sokulmedurumu').removeClass('hide');
                if($('#sokulmedurumu').attr('checked')){
                    $('.bilgilendirme').removeClass('hide');
                    $('#ilgilitel').rules("add", {required:true,minlength:16});
                    $('#smsgonder').val(1);
                    $('#smsvar').prop('checked',true);
                    $.uniform.update();
                }else{
                    if(durum==="1"){
                        $('.bilgilendirme').removeClass('hide');
                        $('#ilgilitel').rules("add", {required:true,minlength:16});
                        $('#smsgonder').val(1);
                        $('#smsvar').prop('checked',true);
                        $.uniform.update();
                    }else{
                        $('.bilgilendirme').addClass('hide');
                        $('#ilgilitel').rules("remove");
                        $('#smsgonder').val(0);
                    }
                }
            }else{
                $('.sokulmedurumu').addClass('hide');
                $('#sokulmedurumu').prop('checked',false);
                $.uniform.update();
                if(durum==="1"){
                    $('.bilgilendirme').removeClass('hide');
                    $('#ilgilitel').rules("add", {required:true,minlength:16});
                    $('#smsgonder').val(1);
                    $('#smsvar').prop('checked',true);
                    $.uniform.update();
                }else{
                    $('.bilgilendirme').addClass('hide');
                    $('#ilgilitel').rules("remove");
                    $('#smsgonder').val(0);
                }
            }
            $(this).valid();
        });
        $('#sokulmedurumu').on('change', function () {
            var durum = $('#durum').val();
            var tipi = $('#tipi').val();
            if(tipi==="2"){
                if($('#sokulmedurumu').attr('checked')){
                    $('.bilgilendirme').removeClass('hide');
                    $('#ilgilitel').rules("add", {required:true,minlength:16});
                    $('#smsgonder').val(1);
                    $('#smsvar').prop('checked',true);
                    $.uniform.update();
                }else{
                    if(durum==="1"){
                        $('.bilgilendirme').removeClass('hide');
                        $('#ilgilitel').rules("add", {required:true,minlength:16});
                        $('#smsgonder').val(1);
                        $('#smsvar').prop('checked',true);
                        $.uniform.update();
                    }else{
                        $('.bilgilendirme').addClass('hide');
                        $('#ilgilitel').rules("remove");
                        $('#smsgonder').val(0);
                    }
                }
            }else{
                if(durum==="1"){
                    $('.bilgilendirme').removeClass('hide');
                    $('#ilgilitel').rules("add", {required:true,minlength:16});
                    $('#smsgonder').val(1);
                    $('#smsvar').prop('checked',true);
                    $.uniform.update();
                }else{
                    $('.bilgilendirme').addClass('hide');
                    $('#ilgilitel').rules("remove");
                    $('#smsgonder').val(0);
                }
            }
        });
        if ($('#servissayaci').attr('checked')) {
            $(".servissayaci").removeClass('hide');
            $('#takilmatarihi').rules("add", "required");
        } else {
            $(".servissayaci").addClass('hide');
            $('#takilmatarihi').rules("remove");
        }
        $('#formsubmit').click(function () {
            $('#form_sample').submit();
        });
        var durum =$('#durum').val();
        var tipi = $('#tipi').val();
        if(durum==="1" && tipi!=="2"){
            $('.servissayacison').removeClass('hide');
        }else{
            $('.servissayacison').addClass('hide');
        }

        var ilkendeks = parseFloat($('#ilkendeksi').val());
        var sonendeks = parseFloat($('#sonendeksi').val());
        var sayacborcu = parseFloat($('#sayacborc').val());
        $('#ilkendeks').maskMoney('mask',parseFloat(ilkendeks)*1000);
        $('#sonendeks').maskMoney('mask',parseFloat(sonendeks)*1000);
        $('#sayacborcu').maskMoney('mask',parseFloat(sayacborcu)*1000);

        ilkendeks=$('#ilkendeks').maskMoney('unmasked')[0];
        sonendeks=$('#sonendeks').maskMoney('unmasked')[0];
        sayacborcu=$('#sayacborcu').maskMoney('unmasked')[0];
        if(sonendeks!==0){
            var fark = sonendeks-ilkendeks+sayacborcu;
            $('.toplamfark').html(((fark.toFixed(3)).replace('.',','))+' '+birim);
        }else{
            $('.toplamfark').html('0,000 '+birim);
        }
        $('#kapanmatarihi').on('change', function() { $(this).valid(); });
        $('#takilmatarihi').on('change', function() { $(this).valid(); });
        $("select").on("select2-close", function () { $(this).valid(); });
    });
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-pencil"></i>Servis Bilgisi Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('sube/serviskayitduzenle/'.$kayit->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
            <div class="form-body">
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    Girilen Bilgilerde Hata Var.
                </div>
                <div class="alert alert-success display-hide">
                    <button class="close" data-close="alert"></button>
                    Bilgiler Doğru!
                </div>
                <div class="form-group col-xs-12 hide">
                    <input type="text" id="subekodu" name="subekodu" value="{{ $sube ? $sube->subekodu : '1'}}" data-required="1" class="form-control">
                    <input type="text" id="abone" name="abone" value="{{ $kayit->abone->id}}">
                    <input type="text" id="abonesayac" name="abonesayac" value="{{ $kayit->abonesayac->id }}">
                    <input type="text" id="birim" name="birim" value="{{ Input::old('birim') ? Input::old('birim') : $kayit->birim }}">
                    <input type="text" id="smsgonder" name="smsgonder" value="{{ Input::old('smsgonder') ? Input::old('smsgonder') : ($kayit->smsdurum==-1 ? 0 : 1) }}">
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Abone:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $kayit->abone->adisoyadi }}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Kayıt Yeri:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $kayit->uretimyer->yeradi }}</label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Abone Sayacı:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $kayit->abonesayac->serino }}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">TC Kimlik No :</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $kayit->abone->tckimlikno }}</label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4">Takılma Adresi :</label>
                    <div class="col-xs-8">
                        <input type="text" id="sayacadres" name="sayacadres" value="{{ Input::old('sayacadres') ? Input::old('sayacadres') : $kayit->kayitadres}}" maxlength="100" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Abone Telefon :<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><input type="tel" id="abonetelefon" name="abonetelefon" value="{{ Input::old('abonetelefon') ? Input::old('abonetelefon') : $kayit->abone->telefon }}" maxlength="17" data-required="1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Diğer Telefonlar :</label>
                        <div class="col-xs-8">
                            <input type="text" id="telefon" name="telefon" value="{{ Input::old('telefon') ? Input::old('telefon') : $kayit->abonesayac->iletisim }}" maxlength="100" data-required="1" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Açıklama:</label>
                        <div class="col-xs-8">
                            <input type="text" id="aciklama" name="aciklama" value="{{ Input::old('aciklama') ? Input::old('aciklama') : $kayit->aciklama}}" data-required="1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Servis Nedeni:</label>
                        <label class="col-xs-8" style="padding-top: 7px">{{ $kayit->gtipi}}</label>
                        <input class="hide" id="tipi" name="tipi" value="{{$kayit->tipi}}">
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Açılma Tarihi:</label>
                        <label class="col-xs-8" style="padding-top: 7px">@if(isset($kayit->acilmatarihi)){{ $kayit->acilmatarihi ? date("d-m-Y", strtotime($kayit->acilmatarihi)) : '' }}@endif</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Kapanma Tarihi:</label>
                        <div class="col-xs-8">
                            <div class="input-group input-medium date date-picker kapanmatarihi" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                <input type="text" id="kapanmatarihi" name="kapanmatarihi" class="form-control" value="{{Input::old('kapanmatarihi') ? Input::old('kapanmatarihi') : ($kayit->kapanmatarihi ? date("d-m-Y", strtotime($kayit->kapanmatarihi)) : '') }}">
                                <span class="input-group-btn"><button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Servis Personeli:</label>
                        <div class="col-xs-8">
                            <select class="form-control select2me select2-offscreen" id="personel" name="personel" tabindex="-1" title="">
                                <option value="">Seçiniz...</option>
                                @foreach($subepersonel as $personel)
                                    @if((Input::old('personel') ? Input::old('personel') : $kayit->subepersonel_id)==$personel->id)
                                        <option value="{{ $personel->id }}" selected>{{ $personel->kullanici->adi_soyadi }}</option>
                                    @else
                                        <option value="{{ $personel->id }}" >{{ $personel->kullanici->adi_soyadi }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Servis Durumu:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><select class="form-control select2me select2-offscreen" id="durum" name="durum" tabindex="-1" title="">
                                <option value="0" {{(Input::old('durum') ? Input::old('durum') : $kayit->durum) === "0" ? "selected" : ""}}>Bekliyor</option>
                                <option value="1" {{(Input::old('durum') ? Input::old('durum') : $kayit->durum) === "1" ? "selected" : ""}}>Tamamlandı</option>
                                <option value="2" {{(Input::old('durum') ? Input::old('durum') : $kayit->durum) === "2" ? "selected" : ""}}>Şebeke Yok</option>
                                <option value="3" {{(Input::old('durum') ? Input::old('durum') : $kayit->durum) === "3" ? "selected" : ""}}>Tadilatlı Yer</option>
                                <option value="4" {{(Input::old('durum') ? Input::old('durum') : $kayit->durum) === "4" ? "selected" : ""}}>Beklemede</option>
                                <option value="5" {{(Input::old('durum') ? Input::old('durum') : $kayit->durum) === "5" ? "selected" : ""}}>Geçici Sayaç</option>
                                <option value="6" {{(Input::old('durum') ? Input::old('durum') : $kayit->durum) === "6" ? "selected" : ""}}>Büroda</option>
                                <option value="7" {{(Input::old('durum') ? Input::old('durum') : $kayit->durum) === "7" ? "selected" : ""}}>Kurumlar</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Servis Son Durumu:</label>
                        <div class="col-xs-8">
                            <input type="text" id="servisnot" name="servisnot" value="{{ Input::old('servisnot') ? Input::old('servisnot') : $kayit->servisnotu }}" data-required="1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12 {{$kayit->tipi==2 ? "" : "hide"}}">
                        <label class="control-label col-xs-4">Sökülme Durumu:</label>
                        <label class="" style="padding-top:7px;padding-left: 20px">
                            <input type="checkbox" id="sokulmedurumu" name="sokulmedurumu" {{(Input::old('sokulmedurumu') ? Input::old('sokulmedurumu') : $kayit->sokulmedurumu) ? "checked" : ""}}/><label>Söküldü</label>
                        </label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Servis Sayacı:</label>
                        <label class="" style="padding-top:7px;padding-left: 10px">
                            @if($kayit->tipi==1)
                                <input type="checkbox" {{(Input::old('servissayaci') ? Input::old('servissayaci') : $kayit->servissayaci) ? "checked" : ""}} {{$kayit->tipi==1 ? "disabled" : ""}} class="{{$kayit->tipi==1 ? "" : "hide"}}"/><label></label>
                                <input type="checkbox" id="servissayaci" name="servissayaci" {{(Input::old('servissayaci') ? Input::old('servissayaci') : $kayit->servissayaci) ? "checked" : ""}} class="{{$kayit->tipi==1 ? "hide" : ""}}"/><label>Takıldı</label>
                            @else
                                <input type="checkbox" id="servissayaci" name="servissayaci" {{(Input::old('servissayaci') ? Input::old('servissayaci') : $kayit->servissayaci) ? "checked" : ""}}/><label>Takıldı</label>
                            @endif
                        </label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Diğer Ücretler:</label>
                        <div class="col-xs-8">
                            <select class="form-control select2me select2-offscreen" id="ekstra" name="ekstra[]" tabindex="-1" title="" multiple="">
                                @foreach($subeurun as $urun)
                                    <option value="{{ $urun->id }}" >{{ $urun->urunadi }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if(Input::old('ekstra'))
                            <div id="ekstralar" class="hide ekstralar">
                                @foreach(Input::old('ekstra') as $ekstra)
                                    {{$ekstra}}
                                @endforeach
                            </div>
                        @else
                            <div id="ekstraekli" class="hide ekstraekli">{{ $servisfiyat ? $servisfiyat->secilenler : '' }}</div>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-12 servissayaci {{(Input::old('servissayaci') ? Input::old('servissayaci') : $kayit->servissayaci) ? "" : "hide"}}">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Takılma Tarihi:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><div class="input-group input-medium date date-picker takilmatarihi" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                <input type="text" id="takilmatarihi" name="takilmatarihi" class="form-control" value="{{Input::old('takilmatarihi') ? Input::old('takilmatarihi') : ($kayit->takilmatarihi ? date("d-m-Y", strtotime($kayit->takilmatarihi)) : '') }}">
                                <span class="input-group-btn"><button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">İlk Endeksi:</label>
                        <div class="col-xs-8">
                            <input type="tel" id="ilkendeks" name="ilkendeks" value="{{ Input::old('ilkendeksi') ? Input::old('ilkendeksi') : round($kayit->ilkendeks,3) }}" maxlength="14" data-required="1" class="form-control">
                            <input type="text" id="ilkendeksi"  name="ilkendeksi" value="{{Input::old('ilkendeksi') ? Input::old('ilkendeksi') : round($kayit->ilkendeks,3)}}" class="form-control hide">
                        </div>
                    </div>
                    <div class="form-group servissayacison col-sm-6 col-xs-12 {{$kayit->durum==1 && $kayit->tipi!=2 ? "" : "hide"}}">
                        <label class="control-label col-xs-4">Sayaç Borcu:</label>
                        <div class="col-xs-8">
                            <input type="tel" id="sayacborcu" name="sayacborcu" value="{{ Input::old('sayacborc') ? Input::old('sayacborc') : round($kayit->sayacborcu,3)}}" maxlength="14" data-required="1" class="form-control">
                            <input type="text" id="sayacborc"  name="sayacborc" value="{{Input::old('sayacborc') ? Input::old('sayacborc') : round($kayit->sayacborcu,3)}}" class="form-control hide">
                        </div>
                    </div>
                    <div class="form-group servissayacison col-sm-6 col-xs-12 {{$kayit->durum==1 && $kayit->tipi!=2 ? "" : "hide"}}">
                        <label class="control-label col-xs-4">Son Endeksi:</label>
                        <div class="col-xs-8">
                            <input type="tel" id="sonendeks" name="sonendeks" value="{{ Input::old('sonendeksi') ? Input::old('sonendeksi') : round($kayit->sonendeks,3)}}" maxlength="14" data-required="1" class="form-control">
                            <input type="text" id="sonendeksi"  name="sonendeksi" value="{{Input::old('sonendeksi') ? Input::old('sonendeksi') : round($kayit->sonendeks,3)}}" class="form-control hide">
                        </div>
                    </div>
                    <div class="form-group servissayacison col-sm-6 col-xs-12 {{$kayit->durum==1 && $kayit->tipi!=2 ? "" : "hide"}}">
                        <label class="control-label col-xs-4">Toplam Fark:</label>
                        <label class="col-xs-8 toplamfark" style="padding-top: 7px">{{ round($kayit->sonendeks-$kayit->ilkendeks+$kayit->sayacborcu,3)." ".$kayit->birim}}</label>
                    </div>
                </div>
                <div class="form-group col-xs-12 bilgilendirme {{(Input::old('smsgonder') ? Input::old('smsgonder') : ($kayit->smsdurum==-1 ? 0 : 1))==1 ? ((Input::old('tipi') ? Input::old('tipi') : $kayit->tipi)==2 ? ((Input::old('sokulmedurumu') ? Input::old('sokulmedurumu') : $kayit->sokulmedurumu) ? '' : ((Input::old('durum') ? Input::old('durum') : $kayit->durum)=="1" ? "" : "hide")) : ((Input::old('durum') ? Input::old('durum') : $kayit->durum)=="1"  ? "" : "hide")) : "hide"}}">
                    <h4>Sms Bilgilendirme <label><input type="checkbox" id="smsvar" name="smsvar" checked/> Sms Gönder </label></h4>
                    <div class="form-group col-xs-12">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="control-label col-xs-4">İlgili Telefonu:<span class="required" aria-required="true"> * </span></label>
                            <div class="input-icon right col-xs-8">
                                <i class="fa"></i><input type="tel" id="ilgilitel" name="ilgilitel" value="{{ Input::old('ilgilitel') ? Input::old('ilgilitel') : $kayit->ilgilitel }}" maxlength="17" autoComplete="off" data-required="1" class="form-control">
                                <input type="text" id="ilgilitelefonu"  name="ilgilitelefonu" value="{{Input::old('ilgilitelefonu') ? Input::old('ilgilitelefonu') : $kayit->ilgilitel}}" class="form-control hide">
                            </div>
                        </div>
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="control-label col-xs-4">Sms Durumu:</label>
                            <label class="col-xs-8 smsdurum" style="padding-top: 7px">{{$kayit->gsmsdurum}}</label>
                        </div>
                    </div>
                    <div class="form-group col-xs-12">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="control-label col-xs-4">Sms İçeriği:</label>
                            <label class="col-xs-8" style="padding-top: 7px">@if(isset($smslog)) {{$smslog->mesaj}} @endif </label>
                        </div>
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="control-label col-xs-4">Sms Zamanı:</label>
                            <label class="col-xs-8" style="padding-top: 7px">@if(isset($smslog)) {{date('H:i',strtotime($smslog->tarih))}} @endif </label>
                        </div>
                    </div>
                    <div class="hide"></div>
                </div>
                <div class="form-group col-xs-12 serviskayitbilgi {{$servisbilgi ? "" : "hide"}}">
                    <div class="panel-group accordion col-xs-12" id="accordion1">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion1" href="#collapse_1">Eski Servis Kayıdı Detayı</a>
                            </h4>
                        </div>
                        <div id="collapse_1" class="panel-collapse collapse">
                            <div class="panel-body">
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Abone:</label>
                                    <label class="col-xs-8 abone" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->adisoyadi : ''}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Abone Sayacı:</label>
                                    <label class="col-xs-8 abonesayac" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->serino : ''}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Telefonu:</label>
                                    <label class="col-xs-8 telefon" style="padding-top: 7px">{{$servisbilgi ? (is_null($servisbilgi->telefon) ? '' : $servisbilgi->telefon) : ''}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Abone No:</label>
                                    <label class="col-xs-8 aboneno" style="padding-top: 7px">{{$servisbilgi ? (is_null($servisbilgi->aboneno) ? '' : $servisbilgi->aboneno) : ''}}</label>
                                </div>
                                <div class="form-group col-xs-12">
                                    <label class="control-label col-sm-2 col-xs-4">Adresi:</label>
                                    <label class="col-xs-8 adresi" style="padding-top: 7px">{{$servisbilgi ? (is_null($servisbilgi->faturaadresi) ? '' : $servisbilgi->faturaadresi) : ''}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Servis Nedeni:</label>
                                    <label class="col-xs-8 servisnedeni" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->isemritipi : ''}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Servis durumu:</label>
                                    <label class="col-xs-8 servisdurum" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->durum : ''}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Açılma Tarihi:</label>
                                    <label class="col-xs-8 acilmatarihi" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->acilmatarihi : ''}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Kapanma Tarihi:</label>
                                    <label class="col-xs-8 kapanmatarihi" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->kapanmatarihi : ''}}</label>
                                </div>
                                <div class="form-group col-xs-12">
                                    <label class="control-label col-sm-2 col-xs-4">Açıklama:</label>
                                    <label class="col-xs-8 aciklama" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->aciklama : ''}}</label>
                                </div>
                                <div class="form-group col-xs-12">
                                    <label class="control-label col-sm-2 col-xs-4">Sonuç:</label>
                                    <label class="col-xs-8 sonuc" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->sonuc : ''}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Servis Sayacı:</label>
                                    <label class="col-xs-8 servissayac" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->servissayaci : ''}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Personel:</label>
                                    <label class="col-xs-8 personel" style="padding-top: 7px">{{$servisbilgi ? $servisbilgi->personeladi : ''}}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <button type="button" class="btn green kaydet" data-toggle="modal" data-target="#confirm">Kaydet</button>
                        <a href="{{ URL::to('sube/serviskayit')}}" class="btn default">Vazgeç</a>
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
    <div class="modal fade" id="confirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Servis Bilgisi Güncellenecek?</h4>
                </div>
                <div class="modal-body">
                    Seçilen Servis Kayıdı Girilen Bilgilerle Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
@stop
