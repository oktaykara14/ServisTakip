@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Kart Baskı Bilgisi <small>Ekleme Ekranı</small></h1>
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
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.tr.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/edestek/form-validation-6.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar   
   FormValidationEdestek.init();
});

$(document).ready(function() {
    $('#options1').on('change', function () {
        var id = $(this).val();
        $("#options3").select2("val", "");
        if (id !== "") {
            $.getJSON(" {{ URL::to('edestek/musteribaski') }}/" + id, function (event) {
                $("#options3").empty();
                var baski = event.baski;
                if (baski.length > 0) {
                    $("#options3").append('<option value="">Seçiniz...</option>');
                    $.each(baski, function (index) {
                        switch (baski[index].edestekbaskitur_id) {
                            case '1':
                                $("#options3").append('<option value="' + baski[index].id + '"> Su Kart Baskısı </option>');
                                break;
                            case '2':
                                $("#options3").append('<option value="' + baski[index].id + '"> Kalorimetre Kart Baskısı </option>');
                                break;
                            case '3':
                                $("#options3").append('<option value="' + baski[index].id + '"> Manas Kart Baskısı </option>');
                                break;
                            case '4':
                                $("#options3").append('<option value="' + baski[index].id + '"> Trifaze Elektrik Kart Baskısı </option>');
                                break;
                            case '5':
                                $("#options3").append('<option value="' + baski[index].id + '"> Monofaze Elektrik Kart Baskısı </option>');
                                break;
                            case '6':
                                $("#options3").append('<option value="' + baski[index].id + '"> Baskısız </option>');
                                break;
                            case '7':
                                $("#options3").append('<option value="' + baski[index].id + '"> Klimatik Kart Baskısı </option>');
                                break;
                            case '8':
                                $("#options3").append('<option value="' + baski[index].id + '"> Gaz Kart Baskısı </option>');
                                break;
							case '9':
                                $("#options3").append('<option value="' + baski[index].id + '"> Mifare Kart Baskısı </option>');
                                break;
                            case '10':
                                $("#options3").append('<option value="' + baski[index].id + '"> Mifare Manas Kart Baskısı </option>');
                                break;	
                        }
                    });
                } else {
                    $("#options3").empty();
                }
            });
        } else {
            $("#options3").empty();
        }
    });

    $('#options5').on('change', function () {
        $('.aciklama').removeClass('hide');
        var id = $(this).val();
        if(id==="1"){
            $('.aciklama').addClass('hide');
            $('#aciklama').val('');
        }
    });
    var options1 = $('#options1').val();
    if (options1 !== "") {
        $.getJSON(" {{ URL::to('edestek/musteribaski') }}/" + options1, function (event) {
            $("#options3").empty();
            var baski = event.baski;
            if (baski.length > 0) {
                $("#options3").append('<option value="">Seçiniz...</option>');
                $.each(baski, function (index) {
                    switch (baski[index].edestekbaskitur_id) {
                        case '1':
                            $("#options3").append('<option value="' + baski[index].id + '"> Su Kart Baskısı </option>');
                            break;
                        case '2':
                            $("#options3").append('<option value="' + baski[index].id + '"> Kalorimetre Kart Baskısı </option>');
                            break;
                        case '3':
                            $("#options3").append('<option value="' + baski[index].id + '"> Manas Kart Baskısı </option>');
                            break;
                        case '4':
                            $("#options3").append('<option value="' + baski[index].id + '"> Trifaze Elektrik Kart Baskısı </option>');
                            break;
                        case '5':
                            $("#options3").append('<option value="' + baski[index].id + '"> Monofaze Elektrik Kart Baskısı </option>');
                            break;
                        case '6':
                            $("#options3").append('<option value="' + baski[index].id + '"> Baskısız </option>');
                            break;
                        case '7':
                            $("#options3").append('<option value="' + baski[index].id + '"> Klimatik Kart Baskısı </option>');
                            break;
                        case '8':
                            $("#options3").append('<option value="' + baski[index].id + '"> Gaz Kart Baskısı </option>');
                            break;
						case '9':
                            $("#options3").append('<option value="' + baski[index].id + '"> Mifare Kart Baskısı </option>');
                            break;
                        case '10':
                            $("#options3").append('<option value="' + baski[index].id + '"> Mifare Manas Kart Baskısı </option>');
                            break;
                    }
                });
            } else {
                $("#options3").empty();
            }
        });
    } else {
        $("#options3").empty();
    }

    $('#musteriduzenle').click(function () {
        var musteriid = $("#options1").val();
        if (musteriid !== "") {
            $.getJSON(" {{ URL::to('edestek/musteri') }}/" + musteriid, function (event) {
                $("#musteriguncel").val(event.musteri.musteriadi);
                $("#musteriid").val(musteriid);
            });
            $('#musteri-duzenle').modal('show');
        } else {
            toastr['warning']('Firma Seçilmedi', 'Firma Güncelleme Hatası');
        }
    });
    $('#baskigoster').click(function () {
        var baskiid = $("#options3").val();
        if (baskiid !== "" && baskiid !== null) {
            $.getJSON(" {{ URL::to('edestek/kartbaski') }}/" + baskiid, function (event) {
                switch (event.kartbaski.edestekbaskitur_id) {
                    case '1':
                    case '2':
                    case '4':
                    case '5':
                    case '7':
                    case '8':
                    case '9':
                        document.getElementById("baskion").src = "{{URL::to('assets/images/baski')}}/" + event.kartbaski.onresim;
                        document.getElementById("baskiarka").src = "{{URL::to('assets/images/baski')}}/" + event.kartbaski.arkaresim;
                        break;
                    case '3':
                        document.getElementById("baskion").src = "{{URL::to('assets/images/manasbaskion.png')}}";
                        document.getElementById("baskiarka").src = "{{URL::to('assets/images/manasbaskiarka.png')}}";
                        break;
                    case '6':
                        document.getElementById("baskion").src = "";
                        document.getElementById("baskiarka").src = "";
                        break;
					case '10':
                        document.getElementById("baskion").src = "{{URL::to('assets/images/mifaremanason.png')}}";
                        document.getElementById("baskiarka").src = "{{URL::to('assets/images/mifaremanasarka.png')}}";
                        break;
                }
            });
            $('#baski-goster').modal('show');
        } else {

            toastr['warning']('Baskı Adı Seçilmedi', 'Kart Baskısı Gösterme Hatası');
        }
    });
    $('.musteriekle').click(function () {
        var musteri = $('#musteriyeni').val();
        if (musteri === "") {
            toastr['warning']('Müşteri Boş Geçildi', 'Müşteri Ekleme Hatası');
        } else {
            $.getJSON(" {{ URL::to('edestek/hizlimusteriekle') }}", {musteri: musteri}, function (event) {
                toastr[event.type](event.text, event.title);
                $('#musteri-ekle').modal('hide');
                if (event.durum === 1 || event.durum === 2) {
                    $('#options1').empty();
                    var musteriler = event.musteriler;
                    var musteriid = 0;
                    $.each(musteriler, function (index) {
                        if (musteriler[index].musteriadi === musteri)
                            musteriid = musteriler[index].id;
                        $('#options1').append('<option value="' + musteriler[index].id + '" >' + musteriler[index].musteriadi + '</option>');
                    });
                    $('#options1').select2('val', musteriid);
                }
            });
        }
    });
    $('.musteriduzenle').click(function () {
        var musteri = $('#musteriguncel').val();
        var musteriid = $('#musteriid').val();
        if (musteri === "") {
            toastr['warning']('Müşteri Boş Geçildi', 'Müşteri Ekleme Hatası');
        } else {
            $.getJSON(" {{ URL::to('edestek/hizlimusteriduzenle') }}", {
                musteriid: musteriid,
                musteri: musteri
            }, function (event) {
                toastr[event.type](event.text, event.title);
                $('#musteri-duzenle').modal('hide');
                if (event.durum === 1) {
                    $('#options1').empty();
                    var musteriler = event.musteriler;
                    $.each(musteriler, function (index) {
                        $('#options1').append('<option value="' + musteriler[index].id + '" >' + musteriler[index].musteriadi + '</option>');
                    });
                    $('#options1').select2('val', musteriid);
                }
            });
        }
    });
    $("select").on("select2-close", function () { $(this).valid(); });
    $('#siparistarih').on('change', function() { $(this).valid(); });
    $('#teslimtarih').on('change', function() { $(this).valid(); });
});
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Kart Baskı Bilgisi Ekle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('edestek/baskiekle') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-md-2 col-xs-12">Firma Adı <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-5 col-xs-12">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="options1" name="options1" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($musteriler as $musteri)
                                @if(Input::old('options1')==$musteri->id )
                            <option value="{{ $musteri->id }}" selected>{{ $musteri->musteriadi }}</option>
                                @else
                            <option value="{{ $musteri->id }}">{{ $musteri->musteriadi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-xs-12">
                    <a href="#musteri-ekle" data-toggle="modal" type="button" class="btn btn-success ">Ekle</a>
                    <a href="" id="musteriduzenle" data-toggle="modal" type="button" class="btn btn-warning ">Düzenle</a>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-xs-12">Siparişi Oluşturan <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-7 col-xs-12">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="options2" name="options2" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($plasiyerler as $plasiyer)
                                @if(Input::old('options2')==$plasiyer->id )
                            <option value="{{ $plasiyer->id }}" selected>{{ $plasiyer->plasiyeradi }}</option>
                                @else
                            <option value="{{ $plasiyer->id }}">{{ $plasiyer->plasiyeradi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">Sipariş Tarihi<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-8">
                        <i class="fa"></i><div class="input-group input-large date date-picker" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                             <input id="siparistarih" type="text" name="siparistarih" class="form-control" value="{{Input::old('siparistarih') }}">
                             <span class="input-group-btn">
                             <button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button>
                             </span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-2 col-xs-12">Baskı Adı <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-7 col-xs-12">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="options3" name="options3" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($baskilar as $baski)
                                @if(Input::old('options3')==$baski->id )
                            <option value="{{ $baski->id }}" selected>{{ $baski->edestekbaskitur->adi }}</option>
                                @else
                            <option value="{{ $baski->id }}">{{ $baski->edestekbaskitur->adi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">Baskı Örneği</label>
                    <div class="col-md-7">
                        <a href="" id="baskigoster" data-toggle="modal" type="button" class="btn btn-info "><i class="fa fa-eye" style="padding-right: 2px"></i>Göster</a>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-2">Miktar<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-7">
                        <i class="fa"></i><input type="text" id="mask_number" name="miktar" value="{{Input::old('miktar')}}" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">Teslim Tarihi</label>
                    <div class="col-md-8">
                        <div class="input-group input-large date date-picker" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                <input id="teslimtarih" type="text" name="teslimtarih" class="form-control" value="{{Input::old('teslimtarih') }}">
                                <span class="input-group-btn">
                                <button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button>
                                </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-xs-12">Personel <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-7 col-xs-12">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="options4" name="options4" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($personeller as $personel)
                                @if(Input::old('options4')==$personel->id )
                            <option value="{{ $personel->id }}" selected>{{ $personel->adisoyadi }}</option>
                                @else
                            <option value="{{ $personel->id }}">{{ $personel->adisoyadi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-xs-12">Durum</label>
                    <div class="col-md-4 col-xs-12">
                        <select class="form-control select2me select2-offscreen" id="options5" name="options5" tabindex="-1" title="">
                            <option value="0" @if(Input::old('options5')=='0') selected @endif >Bekliyor</option>
                            <option value="1" @if(Input::old('options5')=='1') selected @endif >Tamamlandı</option>
                        </select>
                    </div>
                    <label class="control-label col-md-2">Harcanan Süre <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-2">
                        <i class="fa"></i><input type="text" id="sure" name="sure" value="{{Input::old('sure')}}" class="form-control">
                    </div>
                    <label class="control-label">Dakika</label>
                </div>
                <div class="form-group aciklama">
                    <label class="control-label col-md-2">Durum Açıklaması</label>
                    <div class="col-md-9 col-xs-12">
                        <input type="text" id="aciklama" name="aciklama" value="{{Input::old('aciklama')}}" class="form-control">
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn green">Kaydet</button>
                    <a href="{{ URL::to('edestek/edestekkayit')}}" class="btn default">Vazgeç</a>
                </div>
            </div>
            </div>
        </form>
        <!-- END FORM-->
    </div>
</div>
@stop

@section('modal')
    <div class="modal fade" id="musteri-ekle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Müşteri Ekle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_1" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Yeni Müşteri / Firma Ekle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Müşteri Adı <span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-6">
                                                <i class="fa"></i><input type="text" id="musteriyeni" name="musteriyeni" value="{{Input::old('musteriyeni')}}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green musteriekle">Kaydet</button>
                                                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="musteri-duzenle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Müşteri / Firma Düzenle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Konu Düzenle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Müşteri Adı <span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-6">
                                                <i class="fa"></i><input type="text" id="musteriguncel" name="musteriguncel" value="{{Input::old('musteriguncel')}}" data-required="1" class="form-control">
                                                <input type="text" id="musteriid" name="musteriid" value="{{Input::old('musteriid')}}" data-required="1" class="form-control hide">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green musteriduzenle">Kaydet</button>
                                                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="modal fade" id="baski-goster" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet box">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-image"></i> Kart Baskı Örneği
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form action="" id="form_sample_3" class="form-horizontal" novalidate="novalidate">
                                <div class="form-body">
                                    <div class="form-group">
                                        <div class="col-md-5 col-md-offset-1">
                                            <div class="thumbnail" style="width: 400px; height: 256px;">
                                                <img id="baskion" src="" alt=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class=" thumbnail" style="width: 400px; height: 256px;">
                                                <img id="baskiarka" src="" alt=""/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-xs-12" style="text-align: center">
                                                <button type="button" class="btn default" data-dismiss="modal">Kapat</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
