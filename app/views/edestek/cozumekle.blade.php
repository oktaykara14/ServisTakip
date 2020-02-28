@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Hata Çözümü <small>Ekleme Ekranı</small></h1>
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
<script src="{{ URL::to('assets/global/plugins/tinymce/tinymce.min.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/edestek/form-validation-2.js') }}"></script>
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
        $("#options2").select2("val", "");
        if (id !== "") {
            $.getJSON(" {{ URL::to('edestek/detaylar') }}/" + id, function (event) {
                $("#options2").empty();
                var detay = event.detay;
                if (detay.length > 0) {
                    $("#options2").append('<option value="">Seçiniz...</option>');
                    $.each(detay, function (index) {
                        $("#options2").append('<option value="' + detay[index].id + '"> ' + detay[index].detay + '</option>');
                    });
                } else {
                    $("#options2").empty();
                }
            });
        } else {
            $("#options2").empty();
        }
    });
    var options1 = $('#options1').val();
    if (options1 !== "") {
        $.getJSON(" {{ URL::to('edestek/detaylar') }}/" + options1, function (event) {
            $("#options2").empty();
            var detay = event.detay;
            if (detay.length > 0) {
                $("#options2").append('<option value="">Seçiniz...</option>');
                $.each(detay, function (index) {
                    $("#options2").append('<option value="' + detay[index].id + '"> ' + detay[index].detay + '</option>');
                });
            } else {
                $("#options2").empty();
            }
        });
    } else {
        $("#options2").empty();
    }

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
                if (editor.id === "cozum") {
                    $("#cozumid").val(editor.getContent());
                } else {
                    $("#cozumyeniid").val(editor.getContent());
                }
            });
        }
    });

    $('#konuduzenle').click(function () {
        var konuid = $("#options1").val();
        if (konuid !== "") {
            $.getJSON(" {{ URL::to('edestek/konu') }}/" + konuid, function (event) {
                $("#konuguncel").val(event.konu.adi);
                $("#konuid").val(konuid);
            });
            $('#konu-duzenle').modal('show');
        } else {
            toastr['warning']('Konu Seçilmedi', 'Konu Güncelleme Hatası');
        }
    });
    $('#detayekle').click(function () {
        var konuid = $("#options1").val();
        if (konuid !== "") {
            $.getJSON(" {{ URL::to('edestek/konu') }}/" + konuid, function (event) {
                $(".konudetayyeni").text(event.konu.adi);
            });
        } else {
            $('#detay-ekle').modal('hide');
            toastr['warning']('Detay Seçilmedi', 'Detay Ekleme Hatası');
        }
    });
    $('#detayduzenle').click(function () {
        var detayid = $("#options2").val();
        if (detayid !== "" && detayid !== null) {
            $.getJSON(" {{ URL::to('edestek/konudetay') }}/" + detayid, function (event) {
                $("#detayguncel").val(event.detay.detay);
                $(".konudetayguncel").text(event.detay.konu.adi);
            });
            $('#detay-duzenle').modal('show');
        } else {
            toastr['warning']('Detay Seçilmedi', 'Detay Güncelleme Hatası');
        }
    });
    $('.konuekle').click(function () {
        var konu = $('#konuyeni').val();
        if (konu === "") {
            toastr['warning']('Konu Boş Geçildi', 'Konu Ekleme Hatası');
        } else {
            $.getJSON(" {{ URL::to('edestek/konuekle') }}", {konu: konu}, function (event) {
                toastr[event.type](event.text, event.title);
                $('#konu-ekle').modal('hide');
                if (event.durum === 1 || event.durum === 2) {
                    $('#options1').empty();
                    var konular = event.konular;
                    var konuid = 0;
                    $.each(konular, function (index) {
                        if (konular[index].adi === konu)
                            konuid = konular[index].id;
                        $('#options1').append('<option value="' + konular[index].id + '" >' + konular[index].adi + '</option>');
                    });
                    $('#options1').select2('val', konuid);
                }
            });
        }
    });
    $('.konuduzenle').click(function () {
        var konu = $('#konuguncel').val();
        var konuid = $('#konuid').val();
        if (konu === "") {
            toastr['warning']('Konu Boş Geçildi', 'Konu Ekleme Hatası');
        } else {
            $.getJSON(" {{ URL::to('edestek/konuduzenle') }}", {konuid: konuid, konu: konu}, function (event) {
                toastr[event.type](event.text, event.title);
                $('#konu-duzenle').modal('hide');
                if (event.durum === 1) {
                    $('#options1').empty();
                    var konular = event.konular;
                    $.each(konular, function (index) {
                        $('#options1').append('<option value="' + konular[index].id + '" >' + konular[index].adi + '</option>');
                    });
                    $('#options1').select2('val', konuid);
                }
            });
        }
    });
    $('.detayekle').click(function () {
        var konu = $('#options1').val();
        var detay = $('#detayyeni').val();
        if (detay === "") {
            toastr['warning']('Detay Boş Geçildi', 'Detay Ekleme Hatası');
        } else {
            $.getJSON(" {{ URL::to('edestek/detayekle') }}", {konu: konu, detay: detay}, function (event) {
                toastr[event.type](event.text, event.title);
                $('#detay-ekle').modal('hide');
                if (event.durum === 1 || event.durum === 2) {
                    $('#options2').empty();
                    var detaylar = event.detaylar;
                    var detayid = 0;
                    $.each(detaylar, function (index) {
                        if (detaylar[index].detay === detay)
                            detayid = detaylar[index].id;
                        $('#options2').append('<option value="' + detaylar[index].id + '" >' + detaylar[index].detay + '</option>');
                    });
                    $('#options2').select2('val', detayid);
                }
            });
        }
    });
    $('.detayduzenle').click(function () {
        var konu = $('#options1').val();
        var detayid = $('#options2').val();
        var detay = $('#detayguncel').val();
        if (detay === "") {
            toastr['warning']('Detay Boş Geçildi', 'Detay Ekleme Hatası');
        } else {
            $.getJSON(" {{ URL::to('edestek/detayduzenle') }}", {
                konu: konu,
                detayid: detayid,
                detay: detay
            }, function (event) {
                toastr[event.type](event.text, event.title);
                $('#detay-duzenle').modal('hide');
                if (event.durum === 1) {
                    $('#options2').empty();
                    var detaylar = event.detaylar;
                    $.each(detaylar, function (index) {
                        $('#options2').append('<option value="' + detaylar[index].id + '" >' + detaylar[index].detay + '</option>');
                    });
                    $('#options2').select2('val', detayid);
                }
            });
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
            <i class="fa fa-plus"></i>Hata Çözümü Ekle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('edestek/cozumekle') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-md-2 col-xs-12">Konu <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-5 col-xs-12">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="options1" name="options1" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($konular as $konu)
                                @if(Input::old('options1')==$konu->id )
                            <option value="{{ $konu->id }}" selected>{{ $konu->adi }}</option>
                                @else
                            <option value="{{ $konu->id }}">{{ $konu->adi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-xs-12">
                    <a href="#konu-ekle" data-toggle="modal" type="button" class="btn btn-success ">Ekle</a>
                    <a href="" id="konuduzenle" data-toggle="modal" type="button" class="btn btn-warning ">Düzenle</a>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-xs-12">Detayı <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-5 col-xs-12">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="options2" name="options2" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($detaylar as $detay)
                                @if(Input::old('options2')==$detay->id )
                            <option value="{{ $detay->id }}" selected>{{ $detay->detay }}</option>
                                @else
                            <option value="{{ $detay->id }}">{{ $detay->detay }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-xs-12">
                    <a href="#detay-ekle" id="detayekle" data-toggle="modal" type="button" class="btn btn-success ">Ekle</a>
                    <a href="" id="detayduzenle" data-toggle="modal" type="button" class="btn btn-warning ">Düzenle</a>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-xs-12">Problem <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-7 col-xs-12">
                        <i class="fa"></i><input type="text" name="problem" value="{{Input::old('problem') }}" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-xs-12">Çözümü </label>
                    <div class="col-md-7 col-xs-12">
                        <textarea2 id="cozumyeni" name="cozumyeni">{{Input::old('cozumyeniid') }}</textarea2>
                    </div>
                </div>
                <input id="cozumyeniid" name="cozumyeniid" class="hide" />
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn green">Kaydet</button>
                    <a href="{{ URL::to('edestek/hatacozumleri')}}" class="btn default">Vazgeç</a>
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
    <div class="modal fade" id="konu-ekle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Konu Ekle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_1" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Yeni Konu Ekle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Konu <span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-7">
                                                <i class="fa"></i><input type="text" id="konuyeni" name="konuyeni" value="{{Input::old('konuyeni')}}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green konuekle">Kaydet</button>
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
    <div class="modal fade" id="konu-duzenle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Konu Düzenle
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
                                            <label class="control-label col-md-2">Konu <span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-7">
                                                <i class="fa"></i><input type="text" id="konuguncel" name="konuguncel" value="{{Input::old('konuguncel')}}" data-required="1" class="form-control">
                                                <input type="text" id="konuid" name="konuid" value="{{Input::old('konuid')}}" data-required="1" class="form-control hide">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green konuduzenle">Kaydet</button>
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
    <div class="modal fade" id="detay-ekle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Konu Detayı Ekle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_3" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Yeni Konu Detayı Ekle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Konu</label>
                                            <label class="col-md-7 col-xs-12 konudetayyeni" style="margin-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Detay <span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-7">
                                                <i class="fa"></i><input type="text" id="detayyeni" name="detayyeni" value="{{Input::old('detayyeni')}}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green detayekle">Kaydet</button>
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
    <div class="modal fade" id="detay-duzenle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Konu Detayı Düzenle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_4" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Konu Detayı Düzenle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Konu</label>
                                            <label class="col-md-7 col-xs-12 konudetayguncel" style="margin-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Detay<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-7">
                                                <i class="fa"></i><input type="text" id="detayguncel" name="detayguncel" value="{{Input::old('detayguncel')}}" data-required="1" class="form-control">
                                                <input type="text" id="detayid" name="detayid" value="{{Input::old('detayid')}}" data-required="1" class="form-control hide">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green detayduzenle">Kaydet</button>
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
