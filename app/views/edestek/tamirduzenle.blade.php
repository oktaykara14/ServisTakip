@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Tamir Bakım Bilgisi <small>Düzenleme Ekranı</small></h1>
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
<script src="{{ URL::to('assets/global/plugins/tinymce/tinymce.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.tr.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/edestek/form-validation-5.js') }}"></script>
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
    $('#options2').on('change', function () {
        var id = $(this).val();
        $("#options3").select2("val", "");
        if (id !== "") {
            $.getJSON(" {{ URL::to('edestek/urunislemler') }}/" + id, function (event) {
                $("#options3").empty();
                var islem = event.islem;
                if (islem.length > 0) {
                    $("#options3").append('<option value="">Seçiniz...</option>');
                    $.each(islem, function (index) {
                        $("#options3").append('<option value="' + islem[index].id + '"> ' + islem[index].adi + '</option>');
                    });
                } else {
                    $("#options3").empty();
                }
            });
        } else {
            $("#options3").empty();
        }
    });
    var options2 = $('#options2').val();
    var options3 = $('#options3').val();
    if (options2 !== "") {
        $.getJSON(" {{ URL::to('edestek/urunislemler') }}/" + options2, function (event) {
            $("#options3").empty();
            var islem = event.islem;
            if (islem.length > 0) {
                $("#options3").append('<option value="">Seçiniz...</option>');
                $.each(islem, function (index) {
                    if (options3 === islem[index].id)
                        $("#options3").append('<option value="' + islem[index].id + '" selected> ' + islem[index].adi + '</option>');
                    else
                        $("#options3").append('<option value="' + islem[index].id + '"> ' + islem[index].adi + '</option>');
                });
            } else {
                $("#options3").empty();
            }
        });
    } else {
        $("#options3").empty();
    }
    $('#options5').on('change', function () {
        $('.aciklama').removeClass('hide');
        var id = $(this).val();
        if(id==="1"){
            $('.aciklama').addClass('hide');
            $('#aciklama').val('');
        }
    });
    tinymce.init({
        selector: "textarea2", theme: "modern", format: 'text',
        language: "tr", height: 250, resize: false, entity_encoding: "raw",
        entities: '160,nbsp,161,iexcl,162,cent,163,pound,164,curren,165,yen,166,brvbar,167,sect,168,uml,169,copy,170,ordf,171,laquo,172,not,173,shy,174,reg,175,macr,176,deg,177,plusmn,178,sup2,179,sup3,180,acute,181,micro,182,para,183,middot,184,cedil,185,sup1,186,ordm,187,raquo,188,frac14,189,frac12,190,frac34,191,iquest,192,Agrave,193,Aacute,194,Acirc,195,Atilde,196,Auml,197,Aring,198,AElig,199,Ccedil,200,Egrave,201,Eacute,202,Ecirc,203,Euml,204,Igrave,205,Iacute,206,Icirc,207,Iuml,208,ETH,209,Ntilde,210,Ograve,211,Oacute,212,Ocirc,213,Otilde,214,Ouml,215,times,216,Oslash,217,Ugrave,218,Uacute,219,Ucirc,220,Uuml,221,Yacute,222,THORN,223,szlig,224,agrave,225,aacute,226,acirc,227,atilde,228,auml,229,aring,230,aelig,231,ccedil,232,egrave,233,eacute,234,ecirc,235,euml,236,igrave,237,iacute,238,icirc,239,iuml,240,eth,241,ntilde,242,ograve,243,oacute,244,ocirc,245,otilde,246,ouml,247,divide,248,oslash,249,ugrave,250,uacute,251,ucirc,252,uuml,253,yacute,254,thorn,255,yuml,402,fnof,913,Alpha,914,Beta,915,Gamma,916,Delta,917,Epsilon,918,Zeta,919,Eta,920,Theta,921,Iota,922,Kappa,923,Lambda,924,Mu,925,Nu,926,Xi,927,Omicron,928,Pi,929,Rho,931,Sigma,932,Tau,933,Upsilon,934,Phi,935,Chi,936,Psi,937,Omega,945,alpha,946,beta,947,gamma,948,delta,949,epsilon,950,zeta,951,eta,952,theta,953,iota,954,kappa,955,lambda,956,mu,957,nu,958,xi,959,omicron,960,pi,961,rho,962,sigmaf,963,sigma,964,tau,965,upsilon,966,phi,967,chi,968,psi,969,omega,977,thetasym,978,upsih,982,piv,8226,bull,8230,hellip,8242,prime,8243,Prime,8254,oline,8260,frasl,8472,weierp,8465,image,8476,real,8482,trade,8501,alefsym,8592,larr,8593,uarr,8594,rarr,8595,darr,8596,harr,8629,crarr,8656,lArr,8657,uArr,8658,rArr,8659,dArr,8660,hArr,8704,forall,8706,part,8707,exist,8709,empty,8711,nabla,8712,isin,8713,notin,8715,ni,8719,prod,8721,sum,8722,minus,8727,lowast,8730,radic,8733,prop,8734,infin,8736,ang,8743,and,8744,or,8745,cap,8746,cup,8747,int,8756,there4,8764,sim,8773,cong,8776,asymp,8800,ne,8801,equiv,8804,le,8805,ge,8834,sub,8835,sup,8836,nsub,8838,sube,8839,supe,8853,oplus,8855,otimes,8869,perp,8901,sdot,8968,lceil,8969,rceil,8970,lfloor,8971,rfloor,9001,lang,9002,rang,9674,loz,9824,spades,9827,clubs,9829,hearts,9830,diams,338,OElig,339,oelig,352,Scaron,353,scaron,376,Yuml,710,circ,732,tilde,8194,ensp,8195,emsp,8201,thinsp,8204,zwnj,8205,zwj,8206,lrm,8207,rlm,8211,ndash,8212,mdash,8216,lsquo,8217,rsquo,8218,sbquo,8220,ldquo,8221,rdquo,8222,bdquo,8224,dagger,8225,Dagger,8240,permil,8249,lsaquo,8250,rsaquo,8364,euro',
        plugins: ["moxiemanager autolink lists link image charmap preview hr", "wordcount code media save table directionality paste imagetools"],
        toolbar: "undo redo |  bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        //    toolbar_items_size : 'small',
        //    menubar: false,
        relative_urls: false,
        setup: function (editor) {
            editor.on('change', function () {
                if (editor.id === "tamirdetay") {
                    $("#tamirdetayid").val(editor.getContent());
                } else {
                    $("#tamirdetayyeniid").val(editor.getContent());
                }
            });
        }
    });

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
    $('#cinsduzenle').click(function () {
        var cinsid = $("#options2").val();
        if (cinsid !== "") {
            $.getJSON(" {{ URL::to('edestek/uruncinsi') }}/" + cinsid, function (event) {
                $("#cinsguncel").val(event.tamirurun.adi);
                $("#cinsid").val(cinsid);
            });
            $('#cins-duzenle').modal('show');
        } else {
            toastr['warning']('Ürün Cinsi Seçilmedi', 'Ürün Cinsi Güncelleme Hatası');
        }
    });
    $('#islemekle').click(function () {
        var cinsid = $("#options2").val();
        if (cinsid !== "") {
            $.getJSON(" {{ URL::to('edestek/uruncinsi') }}/" + cinsid, function (event) {
                $(".cinsislemyeni").text(event.tamirurun.adi);
            });
        } else {
            $('#islem-ekle').modal('hide');
            toastr['warning']('Ürün Cinsi Seçilmedi', 'İşlem Ekleme Hatası');
        }
    });
    $('#islemduzenle').click(function () {
        //var cinsid = $("#options2").val();
        var islemid = $("#options3").val();
        if (islemid !== "" && islemid !== null) {
            $.getJSON(" {{ URL::to('edestek/urunislem') }}/" + islemid, function (event) {
                $("#islemguncel").val(event.islem.adi);
                $(".cinsislemguncel").text(event.islem.tamirurun.adi);
            });
            $('#islem-duzenle').modal('show');
        } else {
            toastr['warning']('İşlem Seçilmedi', 'İşlem Güncelleme Hatası');
        }
    });
    $('.musteriekle').click(function () {
        var musteri = $('#musteriyeni').val();
        if (musteri === "") {
            toastr['warning']('Müşteri Boş Geçildi', 'Müşteri Ekleme Hatası');
        } else {
            $.getJSON(" {{ URL::to('edestek/hizlimusteriekle') }}/" + musteri, function (event) {
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
            $.getJSON(" {{ URL::to('edestek/hizlimusteriduzenle') }}/" + musteriid + '/' + musteri, function (event) {
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
    $('.cinsekle').click(function () {
        var cins = $('#cinsyeni').val();
        if (cins === "") {
            toastr['warning']('Ürün Cinsi Boş Geçildi', 'Ürün Cinsi Ekleme Hatası');
        } else {
            $.getJSON(" {{ URL::to('edestek/uruncinsiekle') }}", {cins: cins}, function (event) {
                toastr[event.type](event.text, event.title);
                $('#cins-ekle').modal('hide');
                if (event.durum === 1 || event.durum === 2) {
                    $('#options2').empty();
                    var urunler = event.urunler;
                    var cinsid = 0;
                    $.each(urunler, function (index) {
                        if (urunler[index].adi === cins)
                            cinsid = urunler[index].id;
                        $('#options2').append('<option value="' + urunler[index].id + '" >' + urunler[index].adi + '</option>');
                    });
                    $('#options2').select2('val', cinsid);
                }
            });
        }
    });
    $('.cinsduzenle').click(function () {
        var cins = $('#cinsguncel').val();
        var cinsid = $('#cinsid').val();
        if (cins === "") {
            toastr['warning']('Ürün Cinsi Boş Geçildi', 'Ürün Cinsi Ekleme Hatası');
        } else {
            $.getJSON(" {{ URL::to('edestek/uruncinsiduzenle') }}", {cins: cins, cinsid: cinsid}, function (event) {
                toastr[event.type](event.text, event.title);
                $('#cins-duzenle').modal('hide');
                if (event.durum === 1) {
                    $('#options2').empty();
                    var urunler = event.urunler;
                    $.each(urunler, function (index) {
                        $('#options2').append('<option value="' + urunler[index].id + '" >' + urunler[index].adi + '</option>');
                    });
                    $('#options2').select2('val', cinsid);
                }
            });
        }
    });
    $('.islemekle').click(function () {
        var cins = $('#options2').val();
        var islem = $('#islemyeni').val();
        if (islem === "") {
            toastr['warning']('İşlem Boş Geçildi', 'İşlem Ekleme Hatası');
        } else {
            $.getJSON(" {{ URL::to('edestek/urunislemekle') }}", {cins: cins, islem: islem}, function (event) {
                toastr[event.type](event.text, event.title);
                $('#islem-ekle').modal('hide');
                if (event.durum === 1 || event.durum === 2) {
                    $('#options3').empty();
                    var islemler = event.islemler;
                    var islemid = 0;
                    $.each(islemler, function (index) {
                        if (islemler[index].adi === islem)
                            islemid = islemler[index].id;
                        $('#options3').append('<option value="' + islemler[index].id + '" >' + islemler[index].adi + '</option>');
                    });
                    $('#options3').select2('val', islemid);
                }
            });
        }
    });
    $('.islemduzenle').click(function () {
        var cins = $('#options2').val();
        var islemid = $('#options3').val();
        var islem = $('#islemguncel').val();
        if (islem === "") {
            toastr['warning']('İşlem Boş Geçildi', 'İşlem Ekleme Hatası');
        } else {
            $.getJSON(" {{ URL::to('edestek/urunislemduzenle') }}", {
                cins: cins,
                islemid: islemid,
                islem: islem
            }, function (event) {
                toastr[event.type](event.text, event.title);
                $('#islem-duzenle').modal('hide');
                if (event.durum === 1) {
                    $('#options3').empty();
                    var islemler = event.islemler;
                    $.each(islemler, function (index) {
                        $('#options3').append('<option value="' + islemler[index].id + '" >' + islemler[index].adi + '</option>');
                    });
                    $('#options3').select2('val', islemid);
                }
            });
        }
    });
    $("select").on("select2-close", function () { $(this).valid(); });
    $('#gelistarih').on('change', function() { $(this).valid(); });
    $('#sevktarih').on('change', function() { $(this).valid(); });
});
                    
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-pencil"></i>Tamir Bakım Bilgisi Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('edestek/tamirduzenle/'.$kayit->id.'') }}" id="form_sample" method="POST" enctype="multipart/form-data" class="form-horizontal" novalidate="novalidate">
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
                                @if((Input::old('options1')? Input::old('options1') : $kayit->edestekmusteri_id)==$musteri->id )
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
                    <label class="control-label col-md-2 col-xs-12">Ürün Cinsi <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-5 col-xs-12">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="options2" name="options2" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($tamirurunler as $tamirurun)
                                @if((Input::old('options2')? Input::old('options2') : $tamir->edestektamirurun_id)==$tamirurun->id )
                            <option value="{{ $tamirurun->id }}" selected>{{ $tamirurun->adi }}</option>
                                @else
                            <option value="{{ $tamirurun->id }}">{{ $tamirurun->adi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-xs-12">
                    <a href="#cins-ekle" data-toggle="modal" type="button" class="btn btn-success ">Ekle</a>
                    <a href="" id="cinsduzenle" data-toggle="modal" type="button" class="btn btn-warning ">Düzenle</a>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">Ürün Adı</label>
                    <div class="col-md-7">
                            <input type="text" name="urunadi" value="{{Input::old('urunadi')? Input::old('urunadi') : $tamir->urunadi}}" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">Geliş Tarihi</label>
                    <div class="col-md-8">
                        <div class="input-group input-large date date-picker" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                <input id="gelistarih" type="text" name="gelistarih" class="form-control" value="{{Input::old('gelistarih')? Input::old('gelistarih') : date("d-m-Y", strtotime($tamir->gelistarihi)) }}">
                                <span class="input-group-btn">
                                <button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button>
                                </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-xs-12">Yapılan İşlem <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-5 col-xs-12">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="options3" name="options3" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($tamirislemler as $tamirislem)
                                @if((Input::old('options3')? Input::old('options3') : $tamir->edestektamirislem_id)==$tamirislem->id )
                            <option value="{{ $tamirislem->id }}" selected>{{ $tamirislem->adi }}</option>
                                @else
                            <option value="{{ $tamirislem->id }}">{{ $tamirislem->adi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-xs-12">
                    <a href="#islem-ekle" data-toggle="modal" type="button" class="btn btn-success ">Ekle</a>
                    <a href="" id="islemduzenle" data-toggle="modal" type="button" class="btn btn-warning ">Düzenle</a>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-xs-12">Detayı </label>
                    <div class="col-md-7 col-xs-12">
                        <textarea2 id="tamirdetay" name="tamirdetay">{{Input::old('tamirdetayid')? Input::old('tamirdetayid') : $tamir->detay }}</textarea2>
                    </div>
                </div>
                <input id="tamirdetayid" name="tamirdetayid" class="hide" value="{{Input::old('tamirdetayid') ? Input::old('tamirdetayid') : $tamir->detay}}" />
                <div class="form-group">
                    <label class="control-label col-md-2">Sevk Tarihi</label>
                    <div class="col-md-8">
                        <div class="input-group input-large date date-picker" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                <input id="sevktarih" type="text" name="sevktarih" class="form-control" value="{{Input::old('sevktarih')? Input::old('sevktarih') : date("d-m-Y", strtotime($tamir->sevktarihi))  }}">
                                <span class="input-group-btn">
                                <button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button>
                                </span>
                        </div>
                    </div>
                </div>
				<div class="form-group">
                    <label class="control-label col-md-2">Tutanak </label>
                    <div class="col-md-3 fileinput fileinput-new" data-provides="fileinput">
                        <div class="input-group input-large">
                            <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                    <i class="fa fa-image fileinput-exists"></i><span class="fileinput-filename"></span>
                            </div>
                            <span class="input-group-addon btn default btn-file">
                            <span class="fileinput-new">
                            Dosya Seç </span>
                            <span class="fileinput-exists">
                            Değiştir </span>
                            <input type="file" name="tutanak" accept="*" />
                            </span>
                            <a href="" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                            Sil </a>
                        </div>
                    </div>
                    @if($tamir->tutanak)
                    <label class="control-label col-md-2">Geçerli Tutanak</label>
                    <div class="col-md-3" style="padding-top: 7px">
                        <a href='{{ URL::to('assets/tutanak/'.$tamir->tutanak.'') }}' style="color: red">{{ $tamir->tutanak }}</a>
                    </div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-xs-12">Personel <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-7 col-xs-12">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="options4" name="options4" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($personeller as $personel)
                                @if((Input::old('options4')? Input::old('options4') : $kayit->edestekpersonel_id)==$personel->id )
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
                            <option value="0" @if((Input::old('options5')? Input::old('options5') : $kayit->durum)=='0') selected @endif >Bekliyor</option>
                            <option value="1" @if((Input::old('options5')? Input::old('options5') : $kayit->durum)=='1') selected @endif >Tamamlandı</option>
                        </select>
                    </div>
                    <label class="control-label col-md-2">Harcanan Süre <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-2">
                        <i class="fa"></i><input type="text" id="sure" name="sure" value="{{Input::old('sure') ? Input::old('sure') : $kayit->sure}}" class="form-control">
                    </div>
                    <label class="control-label">Dakika</label>
                </div>
                <div class="form-group aciklama">
                    <label class="control-label col-md-2">Durum Açıklaması</label>
                    <div class="col-md-9 col-xs-12">
                        <input type="text" id="aciklama" name="aciklama" value="{{Input::old('aciklama') ? Input::old('aciklama') : $kayit->durum_aciklama}}" class="form-control">
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
<!-- END VALIDATION STATES-->
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
    <div class="modal fade" id="cins-ekle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Ürün Cinsi Ekle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_3" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Yeni Ürün Cinsi Ekle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Ürün Cinsi <span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-7">
                                                <i class="fa"></i><input type="text" id="cinsyeni" name="cinsyeni" value="{{Input::old('cinsyeni')}}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green cinsekle">Kaydet</button>
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
    <div class="modal fade" id="cins-duzenle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Ürün Cinsi Düzenle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_4" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Ürün Cinsi Düzenle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Ürün Cinsi <span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-7">
                                                <i class="fa"></i><input type="text" id="cinsguncel" name="cinsguncel" value="{{Input::old('cinsguncel')}}" data-required="1" class="form-control">
                                                <input type="text" id="cinsid" name="cinsid" value="{{Input::old('cinsid')}}" data-required="1" class="form-control hide">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green cinsduzenle">Kaydet</button>
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
    <div class="modal fade" id="islem-ekle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Yapılan İşlem Ekle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_5" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Yeni Yapılan İşlem Ekle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Ürün Cinsi</label>
                                            <label class="col-md-7 col-xs-12 cinsislemyeni" style="margin-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Yapılan İşlem <span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-7">
                                                <i class="fa"></i><input type="text" id="islemyeni" name="islemyeni" value="{{Input::old('islemyeni')}}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green islemekle">Kaydet</button>
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
    <div class="modal fade" id="islem-duzenle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Yapılan İşlem Düzenle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_6" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Yapılan İşlem Düzenle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Ürün Cinsi</label>
                                            <label class="col-md-7 col-xs-12 cinsislemguncel" style="margin-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Yapılan İşlem<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-7">
                                                <i class="fa"></i><input type="text" id="islemguncel" name="islemguncel" value="{{Input::old('islemguncel')}}" data-required="1" class="form-control">
                                                <input type="text" id="islemid" name="islemid" value="{{Input::old('islemid')}}" data-required="1" class="form-control hide">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green islemduzenle">Kaydet</button>
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
@stop
