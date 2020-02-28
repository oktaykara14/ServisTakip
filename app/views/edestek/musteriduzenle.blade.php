@extends('layout.master')

@section('page-title')
<!--suppress HtmlRequiredAltAttribute -->
<div class="page-title">
    <h1>Proje Bilgisi <small>Bilgi Düzenleme Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" >
<link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::to('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" >
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/tinymce/tinymce.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.tr.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/edestek/form-validation-3.js') }}"></script>
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
    $('#options4').on('change', function () {
        var program = $(this).val();
        $('.epic').addClass('hide');
        $('.epicsmart').addClass('hide');
        $('.forcom').addClass('hide');
        $('.entegrasyon').addClass('hide');
        $('.ipman').addClass('hide');
        if (program !== null) {
            for (var i = 0; i < program.length; i++) {
                switch (program[i]) {
                    case "1": //epic
                        $('.epic').removeClass('hide');
                        break;
                    case "2": //epicsmart
                        $('.epicsmart').removeClass('hide');
                        break;
                    case "3": //4com
                        $('.forcom').removeClass('hide');
                        break;
                    case "4": //entegrasyon
                        $('.entegrasyon').removeClass('hide');
                        break;
                    case "5": //entegrasyon
                        $('.ipman').removeClass('hide');
                        break;
                }
            }
        }
    });	
	var options4=$('#options4').val();
	var i;
	if(options4!=="" && options4!=null){
        for (i = 0; i < options4.length; i++) {
            switch (options4[i]) {
                case "1": //epic
                    $('.epic').removeClass('hide');
                    break;
                case "2": //epicsmart
                    $('.epicsmart').removeClass('hide');
                    break;
                case "3": //4com
                    $('.forcom').removeClass('hide');
                    break;
                case "4": //entegrasyon
                    $('.entegrasyon').removeClass('hide');
                    break;
                case "5": //entegrasyon
                    $('.ipman').removeClass('hide');
                    break;
            }
        }
    }
	
    $('#options3').on('change', function () {
        var urun = $(this).val();
        $('.susayac').addClass('hide');
        $('.elksayac').addClass('hide');
        $('.gazsayac').addClass('hide');
        $('.sicaksayac').addClass('hide');
        $('.isisayac').addClass('hide');
        $('.payolcer').addClass('hide');
        $('.elterminali').addClass('hide');
        $('.kiosk').addClass('hide');
        $('.klimakontrol').addClass('hide');
        $('.icunite').addClass('hide');
        $('.kartokuyucu').addClass('hide');
        $('.digerurun').addClass('hide');
        if (urun !== null) {
            for (var i = 0; i < urun.length; i++) {
                switch (urun[i]) {
                    case "1":
                        $('.susayac').removeClass('hide');
                        break;
                    case "2":
                        $('.sicaksayac').removeClass('hide');
                        break;
                    case "3":
                        $('.elksayac').removeClass('hide');
                        break;
                    case "4":
                        $('.gazsayac').removeClass('hide');
                        break;
                    case "5":
                        $('.isisayac').removeClass('hide');
                        break;
                    case "6":
                        $('.payolcer').removeClass('hide');
                        break;
                    case "7":
                        $('.elterminali').removeClass('hide');
                        break;
                    case "8":
                        $('.kiosk').removeClass('hide');
                        break;
                    case "9":
                        $('.klimakontrol').removeClass('hide');
                        break;
                    case "10":
                        $('.digerurun').removeClass('hide');
                        break;
                    case "11":
                        $('.icunite').removeClass('hide');
                        break;
                    case "12":
                        $('.kartokuyucu').removeClass('hide');
                        break;
                }
            }
        }
    });
	var options3=$('#options3').val();
	if(options3!=="" && options3!=null){
		for (i = 0; i < options3.length; i++) {
			switch (options3[i]) {
				case "1":
					$('.susayac').removeClass('hide');
					break;
				case "2":
					$('.sicaksayac').removeClass('hide');
					break;
				case "3":
					$('.elksayac').removeClass('hide');
					break;
				case "4":
					$('.gazsayac').removeClass('hide');
					break;
				case "5":
					$('.isisayac').removeClass('hide');
					break;
				case "6":
					$('.payolcer').removeClass('hide');
					break;
				case "7":
					$('.elterminali').removeClass('hide');
					break;
				case "8":
					$('.kiosk').removeClass('hide');
					break;
				case "9":
					$('.klimakontrol').removeClass('hide');
					break;
				case "10":
					$('.digerurun').removeClass('hide');
					break;
				case "11":
					$('.icunite').removeClass('hide');
					break;
				case "12":
					$('.kartokuyucu').removeClass('hide');
					break;
			}        
		}	
	}
	
    $('#options9').on('change', function () {
        var baskitur = $(this).val();
        $('.subaski').addClass('hide');
        $('.klmbaski').addClass('hide');
        $('.manasbaski').addClass('hide');
        $('.trifazebaski').addClass('hide');
        $('.monofazebaski').addClass('hide');
        $('.baskisiz').addClass('hide');
        $('.klimatikbaski').addClass('hide');
        $('.gazbaski').addClass('hide');
        $('.mifarebaski').addClass('hide');
        $('.mifaremanasbaski').addClass('hide');
        if (baskitur !== null) {
            for (var i = 0; i < baskitur.length; i++) {
                switch (baskitur[i]) {
                    case "1":
                        $('.subaski').removeClass('hide');
                        break;
                    case "2":
                        $('.klmbaski').removeClass('hide');
                        break;
                    case "3":
                        $('.manasbaski').removeClass('hide');
                        break;
                    case "4":
                        $('.trifazebaski').removeClass('hide');
                        break;
                    case "5":
                        $('.monofazebaski').removeClass('hide');
                        break;
                    case "6":
                        $('.baskisiz').removeClass('hide');
                        break;
                    case "7":
                        $('.klimatikbaski').removeClass('hide');
                        break;
                    case "8":
                        $('.gazbaski').removeClass('hide');
                        break;
                    case "9":
                        $('.mifarebaski').removeClass('hide');
                        break;
                    case "10":
                        $('.mifaremanasbaski').removeClass('hide');
                        break;
                }
            }
        }
    });
	var options9=$('#options9').val();
	if(options9!=="" && options9!=null){
        for (i = 0; i < options9.length; i++) {
            switch (options9[i]) {
                case "1":
                    $('.subaski').removeClass('hide');
                    break;
                case "2":
                    $('.klmbaski').removeClass('hide');
                    break;
                case "3":
                    $('.manasbaski').removeClass('hide');
                    break;
                case "4":
                    $('.trifazebaski').removeClass('hide');
                    break;
                case "5":
                    $('.monofazebaski').removeClass('hide');
                    break;
                case "6":
                    $('.baskisiz').removeClass('hide');
                    break;
                case "7":
                    $('.klimatikbaski').removeClass('hide');
                    break;
                case "8":
                    $('.gazbaski').removeClass('hide');
                    break;
                case "9":
                    $('.mifarebaski').removeClass('hide');
                    break;
                case "10":
                    $('.mifaremanasbaski').removeClass('hide');
                    break;
            }
        }
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
                if (editor.id === "projedetay") {
                    $("#projedetayid").val(editor.getContent());
                } else {
                    $("#projedetayyeniid").val(editor.getContent());
                }
            });
        }
    });
    $('#baslangic').on('change', function() { $(this).valid(); });
    $('#bitis').on('change', function() { $(this).valid(); });
});
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-pencil"></i>Proje Bilgisi Düzenle
        </div>
    </div>
    <div class="portlet-body form" style="display: block;">
        <div class="form-body">
            <div class="tabbable-line">
                <ul class="nav nav-tabs ">
                    <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true"> Proje Detayı </a></li>
                    <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">Müşteri Bilgisi </a></li>
                    <li class=""><a href="#tab_3" data-toggle="tab" aria-expanded="false">Bağlantı Bilgileri </a></li>
                    <li class=""><a href="#tab_4" data-toggle="tab" aria-expanded="false">Sistem Bilgisi </a></li>
                    <li class=""><a href="#tab_5" data-toggle="tab" aria-expanded="false">Ürün Bilgisi </a></li>
                    <li class=""><a href="#tab_6" data-toggle="tab" aria-expanded="false">Program Bilgisi </a></li>
                    <li class=""><a href="#tab_7" data-toggle="tab" aria-expanded="false">Kart Baskıları </a></li>
                </ul>
        <!-- BEGIN FORM-->
                <form action="{{ URL::to('edestek/musteriduzenle/'.$musteri->id.'') }}" id="form_sample" method="POST" enctype="multipart/form-data"  class="form-horizontal" novalidate="novalidate">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1">
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
                                    <label class="control-label col-md-2">Proje/Müşteri Adı <span class="required" aria-required="true"> * </span></label>
                                    <div class="input-icon right col-md-8">
                                        <i class="fa"></i><input type="text" name="adi" value="{{Input::old('adi') ? Input::old('adi') : $musteri->musteriadi }}" data-required="1" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Proje Başlangıç Tarihi</label>
                                    <div class="col-md-2">
                                        <div class="input-group input-medium date date-picker" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                                <input id="baslangic" type="text" name="baslangic" class="form-control" value="{{Input::old('baslangic') ? Input::old('baslangic') : $musteri->baslangictarihi ? date("d-m-Y", strtotime($musteri->baslangictarihi)) : '' }}">
                                                <span class="input-group-btn">
                                                <button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button>
                                                </span>
                                        </div>
                                    </div>
                                    <label class="control-label col-md-2">Bitiş Tarihi</label>
                                    <div class="col-md-3">
                                        <div class="input-group input-medium date date-picker" style="padding-left: 12px !important;"  data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                                <input id="bitis" type="text" name="bitis" class="form-control" value="{{Input::old('bitis') ? Input::old('bitis') : $musteri->bitistarihi ? date("d-m-Y", strtotime($musteri->bitistarihi)) : '' }}">
                                                <span class="input-group-btn">
                                                <button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button>
                                                </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Proje Resmi </label>
                                    <div class="col-md-2 fileinput fileinput-new" data-provides="fileinput">
                                        <div class="input-group input-small">
                                            <div class="form-control uneditable-input input-fixed" data-trigger="fileinput">
                                                    <i class="fa fa-image fileinput-exists"></i><span class="fileinput-filename"></span>
                                            </div>
                                            <span class="input-group-addon btn default btn-file">
                                            <span class="fileinput-new">
                                            Resim Seç </span>
                                            <span class="fileinput-exists">
                                            Değiştir </span>
                                            <input type="file" name="resim" accept="image/*" />
                                            </span>
                                            <a href="javascript:" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                            Sil </a>
                                        </div>
                                    </div>
                                    @if($musteri->projeresim!='')
                                    <label class="control-label col-md-offset-2 col-md-2">Geçerli Proje Resmi</label>
                                    <div class="col-md-3">
                                        <img src='{{ URL::to('assets/images/proje/'.$musteri->projeresim.'') }}' alt="proje resmi"/>
                                    </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-xs-12">Proje Detayı </label>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-offset-1 col-md-10 col-xs-12">
                                        <textarea2 id="projedetay" name="projedetay">{{Input::old('projedetayid') ? Input::old('projedetayid') : $musteri->projedetay }}</textarea2>
                                    </div>
                                </div>
                                <input id="projedetayid" name="projedetayid" class="hide" value='{{Input::old('projedetayid') ? Input::old('projedetayid') : $musteri->projedetay }}' />
                                <div class="form-actions">
                                <div class="row">
                                    <div class="col-xs-12" style="text-align: center">
                                        <button type="submit" class="btn green">Kaydet</button>
                                        <a href="{{ URL::to('edestek/projebilgisi')}}" class="btn default">Vazgeç</a>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_2">
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
                                    <label class="control-label col-md-2">Adresi </label>
                                    <div class="col-md-8">
                                            <input type="text" name="adresi" value="{{Input::old('adresi') ? Input::old('adresi') : $musteri->edestekmusteribilgi ? $musteri->edestekmusteribilgi->adresi : "" }}"  class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-xs-12">İli </label>
                                    <div class="col-md-8 col-xs-12">
                                        <select class="form-control select2me select2-offscreen" id="options1" name="options1" tabindex="-1" title="">
                                            <option value="">Seçiniz...</option>
                                            @foreach($iller as $il)
                                                @if((Input::old('options1') ? Input::old('options1') : $musteri->edestekmusteribilgi ? $musteri->edestekmusteribilgi->iller_id : '')==$il->id )
                                            <option value="{{ $il->id }}" selected>{{ $il->adi }}</option>
                                                @else
                                            <option value="{{ $il->id }}">{{ $il->adi }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Telefonu </label>
                                    <div class="col-md-3">
                                        <input class="form-control" id="mask_phone" name="telefon" value="{{Input::old('telefon') ? Input::old('telefon') : $musteri->edestekmusteribilgi ? $musteri->edestekmusteribilgi->telefon : '' }}"  type="text">
                                    </div>
                                    <label class="control-label col-md-2">Maili </label>
                                    <div class="col-md-3">
                                            <input type="text" name="mail" value="{{Input::old('mail') ? Input::old('mail') : $musteri->edestekmusteribilgi ? $musteri->edestekmusteribilgi->mail : '' }}"  class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Yetkili Kişi </label>
                                    <div class="col-md-3">
                                            <input type="text" name="yetkili" value="{{Input::old('yetkili') ? Input::old('yetkili') : $musteri->edestekmusteribilgi ? $musteri->edestekmusteribilgi->yetkiliadi : '' }}"  class="form-control">
                                    </div>
                                    <label class="control-label col-md-2">Yetkili Telefonu </label>
                                    <div class="col-md-3">
                                            <input type="text" id="mask_phone2" name="yetkilitel" value="{{Input::old('yetkilitel') ? Input::old('telefon') : $musteri->edestekmusteribilgi ? $musteri->edestekmusteribilgi->yetkilitel : '' }}"  class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_3">            
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        <img src="{{ URL::to('assets/images/Teamviewer.png')}}" style="width:30px;height: 30px;margin-right: 5px" alt="teamviewer"/>
                                        Teamviewer Bağlantı Bilgileri</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-2">Id </label>
                                        <div class="col-md-3">
                                                <input type="text" name="teamid" value="{{Input::old('teamid') ? Input::old('teamid') : $musteri->edestekmusteribilgi ? $musteri->edestekmusteribilgi->teamid : ''}}"  class="form-control">
                                        </div>
                                        <label class="control-label col-md-2">Parola </label>
                                        <div class="col-md-3">
                                                <input type="text" name="teampass" value="{{Input::old('teampass') ? Input::old('teampass') : $musteri->edestekmusteribilgi ? $musteri->edestekmusteribilgi->teampass :''}}"  class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        <img src="{{ URL::to('assets/images/Ammyy.png')}}" style="width:30px;height: 30px;margin-right: 5px" alt="ammyy"/>
                                        Ammyy Bağlantı Bilgileri</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-2">Id </label>
                                        <div class="col-md-8">
                                                <input type="text" name="ammyyid" value="{{Input::old('ammyyid') ? Input::old('ammyyid') : $musteri->edestekmusteribilgi ? $musteri->edestekmusteribilgi->ammyyid : '' }}"  class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        <img src="{{ URL::to('assets/images/Alpemix.png')}}" style="width:30px;height: 30px;margin-right: 5px" alt="alpemix"/>
                                        Alpemix Bağlantı Bilgileri</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-2">Id </label>
                                        <div class="col-md-3">
                                                <input type="text" name="alpemixid" value="{{Input::old('alpemixid') ? Input::old('alpemixid') : $musteri->edestekmusteribilgi ? $musteri->edestekmusteribilgi->alpemixid : '' }}"  class="form-control">
                                        </div>
                                        <label class="control-label col-md-2">Parola </label>
                                        <div class="col-md-3">
                                                <input type="text" name="alpemixpass" value="{{Input::old('alpemixpass') ? Input::old('alpemixpass') : $musteri->edestekmusteribilgi ? $musteri->edestekmusteribilgi->alpemixpass : '' }}"  class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        <img src="{{ URL::to('assets/images/UzakBaglanti.png')}}" style="width:30px;height: 30px;margin-right: 5px" alt="rdp"/>
                                        Uzak Bağlantı Bilgileri</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-2">Ip Adresi </label>
                                        <div class="col-md-8">
                                                <input type="text" name="uzakip" value="{{Input::old('uzakip') ? Input::old('uzakip') : $musteri->edestekmusteribilgi ? $musteri->edestekmusteribilgi->uzakip : ''}}"  class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-2">Kullanıcı Adı </label>
                                        <div class="col-md-3">
                                                <input type="text" name="uzakkullanici" value="{{Input::old('uzakkullanici') ? Input::old('uzakkullanici') : $musteri->edestekmusteribilgi ? $musteri->edestekmusteribilgi->uzakkullanici :'' }}"  class="form-control">
                                        </div>
                                        <label class="control-label col-md-2">Parola </label>
                                        <div class="col-md-3">
                                                <input type="text" name="uzakpass" value="{{Input::old('uzakpass') ? Input::old('uzakpass') : $musteri->edestekmusteribilgi ? $musteri->edestekmusteribilgi->uzakpass :''}}"  class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_4">
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
                                    <label class="control-label col-md-2 col-xs-12">Cari Adı </label>
									<div class="col-md-8 col-xs-12">
										<select class="form-control select2me select2-offscreen" id="cariadi" name="cariadi" tabindex="-1" title="">
                                            <option value="">Seçiniz...</option>
                                            @foreach($netsiscariler as $netsiscari)
                                                @if((Input::old('cariadi') ? Input::old('cariadi') : $musteri->edesteksistembilgi ? $musteri->edesteksistembilgi->netsiscari_id :'')==$netsiscari->id )
                                            <option value="{{ $netsiscari->id }}" selected>{{ $netsiscari->cariadi }}</option>
                                                @else
                                            <option value="{{ $netsiscari->id }}">{{ $netsiscari->cariadi }}</option>
                                                @endif
                                            @endforeach
                                        </select>
									</div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-xs-12">İlgili Satış Personeli </label>
                                    <div class="col-md-8 col-xs-12">
                                        <select class="form-control select2me select2-offscreen" id="options2" name="options2" tabindex="-1" title="">
                                            <option value="">Seçiniz...</option>
                                            @foreach($plasiyerler as $plasiyer)
                                                @if((Input::old('options2') ? Input::old('options2') : $musteri->edesteksistembilgi ? $musteri->edesteksistembilgi->plasiyer_id :'')==$plasiyer->id )
                                            <option value="{{ $plasiyer->id }}" selected>{{ $plasiyer->plasiyeradi }}</option>
                                                @else
                                            <option value="{{ $plasiyer->id }}">{{ $plasiyer->plasiyeradi }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Kullanılan Ürünler</label>
                                    <div class="col-md-8">
                                        <select class="form-control select2 select2-offscreen" multiple="" tabindex="-1" id="options3" name="options3[]">
                                            @foreach($urunler as $urun)
                                            <option value="{{ $urun->id }}">{{ $urun->adi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @if(Input::old('options3'))
                                <div id="urunler" class="hide urunler">
                                    @foreach(Input::old('options3') as $urun)
                                        {{ $urun }}
                                    @endforeach
                                </div>
                                @else
                                <div id="urunekli" class="hide urunekli">{{ $musteri->urunturleri }}</div>
                                @endif
                                
                                <div class="form-group">
                                    <label class="control-label col-md-2">Kullanılan Programlar</label>
                                    <div class="col-md-8">
                                        <select class="form-control select2 select2-offscreen" multiple="" tabindex="-1" id="options4" name="options4[]">
                                            @foreach($programlar as $program)
                                            <option value="{{ $program->id }}">{{ $program->adi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @if(Input::old('options4'))
                                <div id="programlar" class="hide programlar">
                                    @foreach(Input::old('options4') as $program)
                                        {{ $program }}
                                    @endforeach
                                </div>
                                @else
                                <div id="programekli" class="hide programekli">{{ $musteri->programturleri }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_5">
                            <div class="form-body">
                                <div class="alert alert-danger display-hide">
                                    <button class="close" data-close="alert"></button>
                                    Girilen Bilgilerde Hata Var.
                                </div>
                                <div class="alert alert-success display-hide">
                                    <button class="close" data-close="alert"></button>
                                    Bilgiler Doğru!
                                </div>
                                @if($ekliurunler)
                                @foreach($ekliurunler as $urun)
                                    @foreach($urunler as $anaurun)
                                        @if($urun->edestekurun_id==$anaurun->id && $anaurun->id==1)
                                <div class="panel susayac panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            <img src="{{ URL::to('assets/images/susayac.png')}}" style="width:30px;height: 30px;margin-right: 5px" alt="susayac"/>
                                            Su Sayacı Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="suadi" value="{{Input::old('suadi') ? Input::old('suadi') : $urun->adi  }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="suadet" value="{{Input::old('suadet') ? Input::old('suadet') : $urun->adet  }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="suissue" value="{{Input::old('suissue') ? Input::old('suissue') : $urun->issue  }}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="sudetay"  style="max-width:100%;">{{Input::old('sudetay') ? Input::old('sudetay') : $urun->detay }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($urun->edestekurun_id==$anaurun->id && $anaurun->id==2)
                                <div class="panel sicaksayac panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/susayac.png')}}" style="width:30px;height: 30px;margin-right: 5px" alt="ssusayac"/>
                                            Sıcak Su Sayacı Bilgileri</h3>
                                        
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="sicakadi" value="{{Input::old('sicakadi') ? Input::old('sicakadi') : $urun->adi  }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="sicakadet" value="{{Input::old('sicakadet') ? Input::old('sicakadet') : $urun->adet  }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="sicakissue" value="{{Input::old('sicakissue') ? Input::old('sicakissue') : $urun->issue  }}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="sicakdetay" style="max-width:100%;">{{Input::old('sicakdetay') ? Input::old('sicakdetay') : $urun->detay }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($urun->edestekurun_id==$anaurun->id && $anaurun->id==3)
                                <div class="panel elksayac panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/elektriksayac.png')}}" style="width:30px;height: 30px;margin-right: 5px" alt="elektriksayac"/>
                                            Elektrik Sayacı Bilgileri</h3> 
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="elkadi" value="{{Input::old('elkadi') ? Input::old('elkadi') : $urun->adi }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="elkadet" value="{{Input::old('elkadet') ? Input::old('elkadet') : $urun->adet }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="elkissue" value="{{Input::old('elkissue') ? Input::old('elkissue') : $urun->issue }}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="elkdetay" style="max-width:100%;">{{Input::old('elkdetay') ? Input::old('elkdetay') : $urun->detay }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($urun->edestekurun_id==$anaurun->id && $anaurun->id==4)
                                <div class="panel gazsayac panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/gazsayac.png')}}" style="width:30px;height: 30px;margin-right: 5px" alt="gazsayac"/>
                                            Gaz Sayacı Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="gazadi" value="{{Input::old('gazadi') ? Input::old('gazadi') : $urun->adi }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="gazadet" value="{{Input::old('gazadet') ? Input::old('gazadet') : $urun->adet }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="gazissue" value="{{Input::old('gazissue') ? Input::old('gazissue') : $urun->issue }}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="gazdetay" style="max-width:100%;">{{Input::old('gazdetay') ? Input::old('gazdetay') : $urun->detay }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($urun->edestekurun_id==$anaurun->id && $anaurun->id==5)
                                <div class="panel isisayac panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            <img src="{{ URL::to('assets/images/isisayac.png')}}" style="width:30px;height: 30px;margin-right: 5px" alt="isisayac"/>
                                            Isı Sayacı Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="isiadi" value="{{Input::old('isiadi') ? Input::old('isiadi') : $urun->adi}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="isiadet" value="{{Input::old('isiadet') ? Input::old('isiadet') : $urun->adet}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="isiissue" value="{{Input::old('isiissue') ? Input::old('isiissue') : $urun->issue}}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="isidetay" style="max-width:100%;">{{Input::old('isidetay') ? Input::old('isidetay') : $urun->detay}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($urun->edestekurun_id==$anaurun->id && $anaurun->id==6)
                                <div class="panel payolcer panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/payolcer.png')}}" style="width:30px;height: 30px;margin-right: 5px" alt="payolcer"/>
                                            Pay Ölçer Bilgileri</h3>
                                        
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="payolceradi" value="{{Input::old('payolceradi') ? Input::old('payolceradi') : $urun->adi }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="payolceradet" value="{{Input::old('payolceradet') ? Input::old('payolceradet') : $urun->adet }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="payolcerissue" value="{{Input::old('payolcerissue') ? Input::old('payolcerissue') : $urun->issue }}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="payolcerdetay" style="max-width:100%;">{{Input::old('payolcerdetay') ? Input::old('payolcerdetay') : $urun->detay }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($urun->edestekurun_id==$anaurun->id && $anaurun->id==7)
                                <div class="panel elterminali panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/elterminali.png')}}" style="width:30px;height: 30px;margin-right: 5px" alt="terminal"/>
                                            El Terminali Bilgileri</h3> 
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="terminaladi" value="{{Input::old('terminaladi') ? Input::old('terminaladi') : $urun->adi }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="terminaladet" value="{{Input::old('terminaladet') ? Input::old('terminaladet') : $urun->adet }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="terminalissue" value="{{Input::old('terminalissue') ? Input::old('terminalissue') : $urun->issue }}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="terminaldetay" style="max-width:100%;">{{Input::old('terminaldetay') ? Input::old('terminaldetay') : $urun->detay }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($urun->edestekurun_id==$anaurun->id && $anaurun->id==8)
                                <div class="panel kiosk panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/kiosk.png')}}" style="width:30px;height: 30px;margin-right: 5px" alt="kiosk"/>
                                            Kiosk Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="kioskadi" value="{{Input::old('kioskadi') ? Input::old('kioskadi') : $urun->adi }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="kioskadet" value="{{Input::old('kioskadet') ? Input::old('kioskadet') : $urun->adet }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="kioskissue" value="{{Input::old('kioskissue') ? Input::old('kioskissue') : $urun->issue }}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="kioskdetay" style="max-width:100%;">{{Input::old('kioskdetay') ? Input::old('kioskdetay') : $urun->detay }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($urun->edestekurun_id==$anaurun->id && $anaurun->id==9)
                                <div class="panel klimakontrol panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            <img src="{{ URL::to('assets/images/klimakontrol.png')}}" style="width:30px;height: 30px;margin-right: 5px" alt="klimatik"/>
                                            Klima Kontrol Cihazı Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="klimaadi" value="{{Input::old('klimaadi') ? Input::old('klimaadi') : $urun->adi }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="klimaadet" value="{{Input::old('klimaadet') ? Input::old('klimaadet') : $urun->adet }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="klimaissue" value="{{Input::old('klimaissue') ? Input::old('klimaissue') : $urun->issue }}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="klimadetay" style="max-width:100%;">{{Input::old('klimadetay') ? Input::old('klimadetay') : $urun->detay }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
									@elseif($urun->edestekurun_id==$anaurun->id && $anaurun->id==11)
                                <div class="panel icunite panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            <img src="{{ URL::to('assets/images/klimakontrol.png')}}" style="width:30px;height: 30px;margin-right: 5px" alt="klimakontrol"/>
                                            İç Ünite Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="icuniteadi" value="{{Input::old('icuniteadi') ? Input::old('icuniteadi') : $urun->adi }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="icuniteadet" value="{{Input::old('icuniteadet') ? Input::old('icuniteadet') : $urun->adet }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="icuniteissue" value="{{Input::old('icuniteissue') ? Input::old('icuniteissue') : $urun->issue }}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="icunitedetay" style="max-width:100%;">{{Input::old('icunitedetay') ? Input::old('icunitedetay') : $urun->detay }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
									@elseif($urun->edestekurun_id==$anaurun->id && $anaurun->id==12)
                                <div class="panel kartokuyucu panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            <img src="{{ URL::to('assets/images/kartokuyucu.jpg')}}" style="width:30px;height: 30px;margin-right: 5px" alt="reader"/>
                                            Kart Okuyucu Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="kartokuyucuadi" value="{{Input::old('kartokuyucuadi') ? Input::old('kartokuyucuadi') : $urun->adi }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="kartokuyucuadet" value="{{Input::old('kartokuyucuadet') ? Input::old('kartokuyucuadet') : $urun->adet }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Çeşidi </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="kartokuyucucesit" value="{{Input::old('kartokuyucucesit') ? Input::old('kartokuyucucesit') : $urun->issue }}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="kartokuyucudetay" style="max-width:100%;">{{Input::old('kartokuyucudetay') ? Input::old('kartokuyucudetay') : $urun->detay }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($urun->edestekurun_id==$anaurun->id && $anaurun->id==10)
                                <div class="panel digerurun panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/digerurun.png')}}" style="width:30px;height: 30px;margin-right: 5px" alt="diger"/>
                                            Diğer Ürün Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="digeradi" value="{{Input::old('digeradi') ? Input::old('digeradi') : $urun->adi}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="digeradet" value="{{Input::old('digeradet') ? Input::old('digeradet') : $urun->adet}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="digerissue" value="{{Input::old('digerissue') ? Input::old('digerissue') : $urun->issue}}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="digerdetay" style="max-width:100%;">{{Input::old('digerdetay') ? Input::old('digerdetay') : $urun->detay}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @endif
                                    @endforeach
                                @endforeach
                                @endif
                                @if($digerurunler)
                                    @foreach($digerurunler as $anaurun)
                                            @if($anaurun->id==1)
                                <div class="panel susayac panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            <img src="{{ URL::to('assets/images/susayac.png')}}" style="width:30px;height: 30px;margin-right: 5px" alt="susayaci"/>
                                            Su Sayacı Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="suadi" value="{{Input::old('suadi')}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="suadet" value="{{Input::old('suadet')}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="suissue" value="{{Input::old('suissue')}}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="sudetay" style="max-width:100%;">{{Input::old('sudetay')}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                            @elseif($anaurun->id==2)
                                <div class="panel sicaksayac panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/susayac.png')}}" style="width:30px;height: 30px;margin-right: 5px" alt="ssusayaci"/>
                                            Sıcak Su Sayacı Bilgileri</h3>
                                        
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="sicakadi" value="{{Input::old('sicakadi')}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="sicakadet" value="{{Input::old('sicakadet')}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="sicakissue" value="{{Input::old('sicakissue')}}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="sicakdetay" style="max-width:100%;">{{Input::old('sicakdetay')}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                            @elseif($anaurun->id==3)
                                <div class="panel elksayac panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/elektriksayac.png')}}" style="width:30px;height: 30px;margin-right: 5px" alt="elektriksayaci"/>
                                            Elektrik Sayacı Bilgileri</h3> 
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="elkadi" value="{{Input::old('elkadi')}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="elkadet" value="{{Input::old('elkadet')}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="elkissue" value="{{Input::old('elkissue')}}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="elkdetay" style="max-width:100%;">{{Input::old('elkdetay')}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                            @elseif($anaurun->id==4)
                                <div class="panel gazsayac panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/gazsayac.png')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            Gaz Sayacı Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="gazadi" value="{{Input::old('gazadi')}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="gazadet" value="{{Input::old('gazadet')}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="gazissue" value="{{Input::old('gazissue')}}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="gazdetay" style="max-width:100%;">{{Input::old('gazdetay')}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                            @elseif($anaurun->id==5)
                                <div class="panel isisayac panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            <img src="{{ URL::to('assets/images/isisayac.png')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            Isı Sayacı Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="isiadi" value="{{Input::old('isiadi')}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="isiadet" value="{{Input::old('isiadet')}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="isiissue" value="{{Input::old('isiissue')}}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="isidetay" style="max-width:100%;">{{Input::old('isidetay')}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                            @elseif($anaurun->id==6)
                                <div class="panel payolcer panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/payolcer.png')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            Pay Ölçer Bilgileri</h3>
                                        
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="payolceradi" value="{{Input::old('payolceradi')}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="payolceradet" value="{{Input::old('payolceradet')}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="payolcerissue" value="{{Input::old('payolcerissue')}}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="payolcerdetay" style="max-width:100%;">{{Input::old('payolcerdetay')}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                            @elseif($anaurun->id==7)
                                <div class="panel elterminali panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/elterminali.png')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            El Terminali Bilgileri</h3> 
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="terminaladi" value="{{Input::old('terminaladi')}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="terminaladet" value="{{Input::old('terminaladet')}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="terminalissue" value="{{Input::old('terminalissue')}}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="terminaldetay" style="max-width:100%;">{{Input::old('terminaldetay')}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                            @elseif($anaurun->id==8)
                                <div class="panel kiosk panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/kiosk.png')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            Kiosk Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="kioskadi" value="{{Input::old('kioskadi')}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="kioskadet" value="{{Input::old('kioskadet')}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="kioskissue" value="{{Input::old('kioskissue')}}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="kioskdetay" style="max-width:100%;">{{Input::old('kioskdetay')}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                            @elseif($anaurun->id==9)
                                <div class="panel klimakontrol panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            <img src="{{ URL::to('assets/images/klimakontrol.png')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            Klima Kontrol Cihazı Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="klimaadi" value="{{Input::old('klimaadi') }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="klimaadet" value="{{Input::old('klimaadet') }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="klimaissue" value="{{Input::old('klimaissue') }}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="klimadetay" style="max-width:100%;">{{Input::old('klimadetay') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								            @elseif($anaurun->id==11)
                                <div class="panel icunite panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            <img src="{{ URL::to('assets/images/klimakontrol.png')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            İç Ünite Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="icuniteadi" value="{{Input::old('icuniteadi') }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="icuniteadet" value="{{Input::old('icuniteadet') }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="icuniteissue" value="{{Input::old('icuniteissue') }}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="icunitedetay" style="max-width:100%;">{{Input::old('icunitedetay') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								            @elseif($anaurun->id==12)
                                <div class="panel kartokuyucu panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            <img src="{{ URL::to('assets/images/kartokuyucu.jpg')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            Kart Okuyucu Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="kartokuyucuadi" value="{{Input::old('kartokuyucuadi') }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="kartokuyucuadet" value="{{Input::old('kartokuyucuadet') }}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Çeşidi </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="kartokuyucucesit" value="{{Input::old('kartokuyucucesit') }}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="kartokuyucudetay" style="max-width:100%;">{{Input::old('kartokuyucudetay') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                            @elseif($anaurun->id==10)
                                <div class="panel digerurun panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/digerurun.png')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            Diğer Ürün Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ürün Adı </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="digeradi" value="{{Input::old('digeradi')}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Adet</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="digeradet" value="{{Input::old('digeradet')}}"  class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Issue </label>
                                                <div class="col-md-8">
                                                    <input type="text" name="digerissue" value="{{Input::old('digerissue')}}"  class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">Detayı </label>
                                            <div class="col-md-8">
                                                    <textarea class="form-control" rows="6"  name="digerdetay" style="max-width:100%;">{{Input::old('digerdetay')}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                            @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_6">
                            <div class="form-body">
                                <div class="alert alert-danger display-hide">
                                    <button class="close" data-close="alert"></button>
                                    Girilen Bilgilerde Hata Var.
                                </div>
                                <div class="alert alert-success display-hide">
                                    <button class="close" data-close="alert"></button>
                                    Bilgiler Doğru!
                                </div>
                                @if($ekliprogramlar)
                                @foreach($ekliprogramlar as $program)
                                    @foreach($programlar as $anaprogram)
                                        @if($program->edestekprogram_id==$anaprogram->id && $anaprogram->id==1)
                                <div class="panel epic panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            <img src="{{ URL::to('assets/images/epic.png')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            Entegre (Epic) Bilgileri    
                                            <img src="{{ URL::to('assets/images/oracle.png')}}" style="width:25px;height: 30px;margin-left:30% ;margin-right: 5px"/>
                                            Oracle Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Versiyon </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="epicversiyon" value="{{Input::old('epicversiyon') ? Input::old('epicversiyon') : $program->versiyon }}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Versiyon </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="oracleversiyon" value="{{Input::old('oracleversiyon') ? Input::old('oracleversiyon') : ($program->database ? $program->database->versiyon : '') }}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Kullanıcı Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="epickullanici" value="{{Input::old('epickullanici') ? Input::old('epickullanici') : $program->kullaniciadi }}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Veritabanı Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="oracleveritabani" value="{{Input::old('oracleveritabani') ? Input::old('oracleveritabani') : ($program->database ? $program->database->adi : '') }}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="epicsifre" value="{{Input::old('epicsifre') ? Input::old('epicsifre') : $program->sifre }}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Kullanıcı Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="oraclekullanici" value="{{Input::old('oraclekullanici') ? Input::old('oraclekullanici') : ($program->database ? $program->database->kullaniciadi : '') }}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Manas Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="epicmanas" value="{{Input::old('epicmanas') ? Input::old('epicmanas') : $program->yetkilisifre }}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="oraclesifre" value="{{Input::old('oraclesifre') ? Input::old('oraclesifre') : ($program->database ? $program->database->sifre : '') }}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Diğer Bilgiler </label>
                                            <div class="col-md-3">
                                                    <textarea class="form-control" rows="5"  name="epicdiger" style="max-width:100%;">{{Input::old('epicdiger') ? Input::old('epicdiger') : $program->diger }}</textarea>
                                            </div>
                                            <label class="control-label col-md-2">Diğer Bilgiler </label>
                                            <div class="col-md-3">
                                                    <textarea class="form-control" rows="5"  name="oraclediger" style="max-width:100%;">{{Input::old('oraclediger') ? Input::old('oraclediger') : ($program->database ? $program->database->diger : '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($program->edestekprogram_id==$anaprogram->id && $anaprogram->id==2)
                                <div class="panel epicsmart panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/epicsmart.png')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            EpicSmart Bilgileri   
                                            <img src="{{ URL::to('assets/images/sqlserver.png')}}" style="width:25px;height: 30px;margin-left:33% ;margin-right: 5px"/>
                                            SQL Server Bilgileri</h3>
                                        
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Versiyon </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="smartversiyon" value="{{Input::old('smartversiyon') ? Input::old('smartversiyon') : $program->versiyon }}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Versiyon </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="sqlversiyon" value="{{Input::old('sqlversiyon') ? Input::old('sqlversiyon') : ($program->database ? $program->database->versiyon : '') }}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Kullanıcı Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="smartkullanici" value="{{Input::old('smartkullanici') ? Input::old('smartkullanici') : $program->kullaniciadi }}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Server Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="sqlveritabani" value="{{Input::old('sqlveritabani') ? Input::old('sqlveritabani') : ($program->database ? $program->database->adi : '') }}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="smartsifre" value="{{Input::old('smartsifre') ? Input::old('smartsifre') : $program->sifre }}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Kullanıcı Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="sqlkullanici" value="{{Input::old('sqlkullanici') ? Input::old('sqlkullanici') : ($program->database ? $program->database->kullaniciadi : '') }}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Manas Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="smartmanas" value="{{Input::old('smartmanas') ? Input::old('smartmanas') : $program->yetkilisifre }}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="sqlsifre" value="{{Input::old('sqlsifre') ? Input::old('sqlsifre') : ($program->database ? $program->database->sifre : '') }}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Diğer Bilgiler </label>
                                            <div class="col-md-3">
                                                    <textarea class="form-control" rows="5"  name="smartdiger" style="max-width:100%;">{{Input::old('smartdiger') ? Input::old('smartdiger') : $program->diger }}</textarea>
                                            </div>
                                            <label class="control-label col-md-2">Diğer Bilgiler </label>
                                            <div class="col-md-3">
                                                    <textarea class="form-control" rows="5"  name="sqldiger" style="max-width:100%;">{{Input::old('sqldiger') ? Input::old('sqldiger') : ($program->database ? $program->database->diger : '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($program->edestekprogram_id==$anaprogram->id && $anaprogram->id==3)
                                <div class="panel forcom panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/4com.png')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            4com Bilgileri 
                                            <img src="{{ URL::to('assets/images/mysql.png')}}" style="width:25px;height: 30px;margin-left:37% ;margin-right: 5px"/>
                                            MySQL Bilgileri</h3> 
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Versiyon </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="4comversiyon" value="{{Input::old('4comversiyon') ? Input::old('4comversiyon') : $program->versiyon }}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Versiyon </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="mysqlversiyon" value="{{Input::old('mysqlversiyon') ? Input::old('mysqlversiyon') : ($program->database ? $program->database->versiyon : '') }}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Kullanıcı Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="4comkullanici" value="{{Input::old('4comkullanici') ? Input::old('4comkullanici') : $program->kullaniciadi }}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Server Adı/Portu </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="mysqlveritabani" value="{{Input::old('mysqlveritabani') ? Input::old('mysqlveritabani') : ($program->database ? $program->database->adi : '') }}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="4comsifre" value="{{Input::old('4comsifre') ? Input::old('4comsifre') : $program->sifre }}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Kullanıcı Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="mysqlkullanici" value="{{Input::old('mysqlkullanici') ? Input::old('mysqlkullanici') : ($program->database ? $program->database->kullaniciadi : '') }}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Power User Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="4compower" value="{{Input::old('4compower') ? Input::old('4compower') : $program->yetkilisifre }}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="mysqlsifre" value="{{Input::old('mysqlsifre') ? Input::old('mysqlsifre') : ($program->database ? $program->database->sifre : '') }}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Diğer Bilgiler </label>
                                            <div class="col-md-3">
                                                    <textarea class="form-control" rows="5"  name="4comdiger" style="max-width:100%;">{{Input::old('4comdiger') ? Input::old('4comdiger') : $program->diger }}</textarea>
                                            </div>
                                            <label class="control-label col-md-2">Diğer Bilgiler </label>
                                            <div class="col-md-3">
                                                    <textarea class="form-control" rows="5"  name="mysqldiger" style="max-width:100%;">{{Input::old('mysqldiger') ? Input::old('mysqldiger') : ($program->database ? $program->database->diger : '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($program->edestekprogram_id==$anaprogram->id && $anaprogram->id==4)
                                <div class="panel entegrasyon panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/entegrasyon.png')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            Entegrasyon Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Firma </label>
                                                <div class="col-md-8 col-xs-12">
                                                    <select class="form-control select2me select2-offscreen" id="options5" name="options5" tabindex="-1" title="">
                                                        <option value="">Seçiniz...</option>
                                                        @foreach($firmalar as $firma)
                                                            @if((Input::old('options5')? Input::old('options5') : $program->edestekentegrasyonfirma_id)==$firma->id )
                                                        <option value="{{ $firma->id }}" selected>{{ $firma->firma }}</option>
                                                            @else
                                                        <option value="{{ $firma->id }}">{{ $firma->firma }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Entegrasyon Tipi</label>
                                                <div class="col-md-8 col-xs-12">
                                                    <select class="form-control select2me select2-offscreen" id="options6" name="options6" tabindex="-1" title="">
                                                        <option value="">Seçiniz...</option>
                                                        @foreach($tipler as $tip)
                                                            @if((Input::old('options6') ? Input::old('options6') : $program->edestekentegrasyontip_id)==$tip->id )
                                                        <option value="{{ $tip->id }}" selected>{{ $tip->tipi }}</option>
                                                            @else
                                                        <option value="{{ $tip->id }}">{{ $tip->tipi }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Program </label>
                                                <div class="col-md-8 col-xs-12">
                                                    <select class="form-control select2me select2-offscreen" id="options7" name="options7" tabindex="-1" title="">
                                                        <option value="">Seçiniz...</option>
                                                        @foreach($entegrasyonprogramlar as $entegrasyonprogram)
                                                            @if((Input::old('options7') ? Input::old('options7') : $program->edestekentegrasyonprogram_id)==$entegrasyonprogram->id )
                                                        <option value="{{ $entegrasyonprogram->id }}" selected>{{ $entegrasyonprogram->program }}</option>
                                                            @else
                                                        <option value="{{ $entegrasyonprogram->id }}">{{ $entegrasyonprogram->program }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Versiyon </label>
                                                <div class="col-md-8 col-xs-12">
                                                    <select class="form-control select2me select2-offscreen" id="options8" name="options8" tabindex="-1" title="">
                                                        <option value="">Seçiniz...</option>
                                                        @foreach($versiyonlar as $versiyon)
                                                            @if((Input::old('options8')? Input::old('options8') : $program->edestekentegrasyonversiyon_id)==$versiyon->id )
                                                        <option value="{{ $versiyon->id }}" selected>{{ $versiyon->versiyon }}</option>
                                                            @else
                                                        <option value="{{ $versiyon->id }}">{{ $versiyon->versiyon }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-12" style="margin-bottom: 15px;text-align:left">Diğer Bilgiler </label>
                                            <div class="col-md-12">
                                                    <textarea class="form-control" rows="7"  name="entegrasyondiger" style="max-width:100%;">{{Input::old('entegrasyondiger') ? Input::old('entegrasyondiger') : $program->diger }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								        @elseif($program->edestekprogram_id==$anaprogram->id && $anaprogram->id==5)
                                <div class="panel ipman panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/ipman.png')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            Ipman Bilgileri   
                                            <img src="{{ URL::to('assets/images/sqlserver.png')}}" style="width:25px;height: 30px;margin-left:33% ;margin-right: 5px"/>
                                            SQL Server Bilgileri</h3>
                                        
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Versiyon </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="ipmanversiyon" value="{{Input::old('ipmanversiyon') ? Input::old('ipmanversiyon') : $program->versiyon }}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Versiyon </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="ipmansqlversiyon" value="{{Input::old('ipmansqlversiyon') ? Input::old('ipmansqlversiyon') : ($program->database ? $program->database->versiyon : '') }}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Kullanıcı Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="ipmankullanici" value="{{Input::old('ipmankullanici') ? Input::old('ipmankullanici') : $program->kullaniciadi }}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Server Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="ipmanveritabani" value="{{Input::old('ipmanveritabani') ? Input::old('ipmanveritabani') : ($program->database ? $program->database->adi : '') }}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="ipmansifre" value="{{Input::old('ipmansifre') ? Input::old('ipmansifre') : $program->sifre }}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Kullanıcı Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="ipmansqlkullanici" value="{{Input::old('ipmansqlkullanici') ? Input::old('ipmansqlkullanici') : ($program->database ? $program->database->kullaniciadi : '') }}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Manas Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="ipmanmanas" value="{{Input::old('ipmanmanas') ? Input::old('ipmanmanas') : $program->yetkilisifre }}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="ipmansqlsifre" value="{{Input::old('ipmansqlsifre') ? Input::old('ipmansqlsifre') : ($program->database ? $program->database->sifre : '') }}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Diğer Bilgiler </label>
                                            <div class="col-md-3">
                                                    <textarea class="form-control" rows="5"  name="ipmandiger" style="max-width:100%;">{{Input::old('ipmandiger') ? Input::old('ipmandiger') : $program->diger }}</textarea>
                                            </div>
                                            <label class="control-label col-md-2">Diğer Bilgiler </label>
                                            <div class="col-md-3">
                                                    <textarea class="form-control" rows="5"  name="ipmansqldiger" style="max-width:100%;">{{Input::old('ipmansqldiger') ? Input::old('ipmansqldiger') : ($program->database ? $program->database->diger : '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @endif
                                    @endforeach
                                @endforeach
                                @endif
                                @if($digerprogramlar)
                                    @foreach($digerprogramlar as $anaprogram)
                                        @if($anaprogram->id==1)
                                <div class="panel epic panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            <img src="{{ URL::to('assets/images/epic.png')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            Entegre (Epic) Bilgileri    
                                            <img src="{{ URL::to('assets/images/oracle.png')}}" style="width:25px;height: 30px;margin-left:30% ;margin-right: 5px"/>
                                            Oracle Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Versiyon </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="epicversiyon" value="{{Input::old('epicversiyon') }}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Versiyon </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="oracleversiyon" value="{{Input::old('oracleversiyon')}}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Kullanıcı Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="epickullanici" value="{{Input::old('epickullanici')}}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Veritabanı Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="oracleveritabani" value="{{Input::old('oracleveritabani')}}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="epicsifre" value="{{Input::old('epicsifre')}}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Kullanıcı Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="oraclekullanici" value="{{Input::old('oraclekullanici')}}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Manas Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="epicmanas" value="{{Input::old('epicmanas')}}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="oraclesifre" value="{{Input::old('oraclesifre')}}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Diğer Bilgiler </label>
                                            <div class="col-md-3">
                                                    <textarea class="form-control" rows="5"  name="epicdiger" style="max-width:100%;">{{Input::old('epicdiger')}}</textarea>
                                            </div>
                                            <label class="control-label col-md-2">Diğer Bilgiler </label>
                                            <div class="col-md-3">
                                                    <textarea class="form-control" rows="5"  name="oraclediger" style="max-width:100%;">{{Input::old('oraclediger')}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($anaprogram->id==2)
                                <div class="panel epicsmart panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/epicsmart.png')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            EpicSmart Bilgileri   
                                            <img src="{{ URL::to('assets/images/sqlserver.png')}}" style="width:25px;height: 30px;margin-left:33% ;margin-right: 5px"/>
                                            SQL Server Bilgileri</h3>
                                        
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Versiyon </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="smartversiyon" value="{{Input::old('smartversiyon')}}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Versiyon </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="sqlversiyon" value="{{Input::old('sqlversiyon')}}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Kullanıcı Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="smartkullanici" value="{{Input::old('smartkullanici')}}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Server Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="sqlveritabani" value="{{Input::old('sqlveritabani')}}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="smartsifre" value="{{Input::old('smartsifre')}}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Kullanıcı Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="sqlkullanici" value="{{Input::old('sqlkullanici')}}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Manas Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="smartmanas" value="{{Input::old('smartmanas')}}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="sqlsifre" value="{{Input::old('sqlsifre')}}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Diğer Bilgiler </label>
                                            <div class="col-md-3">
                                                    <textarea class="form-control" rows="5"  name="smartdiger" style="max-width:100%;">{{Input::old('smartdiger')}}</textarea>
                                            </div>
                                            <label class="control-label col-md-2">Diğer Bilgiler </label>
                                            <div class="col-md-3">
                                                    <textarea class="form-control" rows="5"  name="sqldiger" style="max-width:100%;">{{Input::old('sqldiger')}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($anaprogram->id==3)
                                <div class="panel 4com panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/4com.png')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            4com Bilgileri 
                                            <img src="{{ URL::to('assets/images/mysql.png')}}" style="width:25px;height: 30px;margin-left:37% ;margin-right: 5px"/>
                                            MySQL Bilgileri</h3> 
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Versiyon </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="4comversiyon" value="{{Input::old('4comversiyon')}}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Versiyon </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="mysqlversiyon" value="{{Input::old('mysqlversiyon')}}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Kullanıcı Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="4comkullanici" value="{{Input::old('4comkullanici')}}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Server Adı/Portu </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="mysqlveritabani" value="{{Input::old('mysqlveritabani')}}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="4comsifre" value="{{Input::old('4comsifre')}}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Kullanıcı Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="mysqlkullanici" value="{{Input::old('mysqlkullanici')}}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Power User Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="4compower" value="{{Input::old('4compower')}}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="mysqlsifre" value="{{Input::old('mysqlsifre')}}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Diğer Bilgiler </label>
                                            <div class="col-md-3">
                                                    <textarea class="form-control" rows="5"  name="4comdiger" style="max-width:100%;">{{Input::old('4comdiger')}}</textarea>
                                            </div>
                                            <label class="control-label col-md-2">Diğer Bilgiler </label>
                                            <div class="col-md-3">
                                                    <textarea class="form-control" rows="5"  name="mysqldiger" style="max-width:100%;">{{Input::old('mysqldiger')}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($anaprogram->id==4)
                                <div class="panel entegrasyon panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/entegrasyon.png')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            Entegrasyon Bilgileri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-5">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Firma </label>
                                                <div class="col-md-8 col-xs-12">
                                                    <select class="form-control select2me select2-offscreen" id="options5" name="options5" tabindex="-1" title="">
                                                        <option value="">Seçiniz...</option>
                                                        @foreach($firmalar as $firma)
                                                            @if(Input::old('options5')==$firma->id )
                                                        <option value="{{ $firma->id }}" selected>{{ $firma->firma }}</option>
                                                            @else
                                                        <option value="{{ $firma->id }}">{{ $firma->firma }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Entegrasyon Tipi</label>
                                                <div class="col-md-8 col-xs-12">
                                                    <select class="form-control select2me select2-offscreen" id="options6" name="options6" tabindex="-1" title="">
                                                        <option value="">Seçiniz...</option>
                                                        @foreach($tipler as $tip)
                                                            @if(Input::old('options6')==$tip->id )
                                                        <option value="{{ $tip->id }}" selected>{{ $tip->tipi }}</option>
                                                            @else
                                                        <option value="{{ $tip->id }}">{{ $tip->tipi }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Program </label>
                                                <div class="col-md-8 col-xs-12">
                                                    <select class="form-control select2me select2-offscreen" id="options7" name="options7" tabindex="-1" title="">
                                                        <option value="">Seçiniz...</option>
                                                        @foreach($entegrasyonprogramlar as $entegrasyonprogram)
                                                            @if(Input::old('options7')==$entegrasyonprogram->id )
                                                        <option value="{{ $entegrasyonprogram->id }}" selected>{{ $entegrasyonprogram->program }}</option>
                                                            @else
                                                        <option value="{{ $entegrasyonprogram->id }}">{{ $entegrasyonprogram->program }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Versiyon </label>
                                                <div class="col-md-8 col-xs-12">
                                                    <select class="form-control select2me select2-offscreen" id="options8" name="options8" tabindex="-1" title="">
                                                        <option value="">Seçiniz...</option>
                                                        @foreach($versiyonlar as $versiyon)
                                                            @if(Input::old('options8')==$versiyon->id )
                                                        <option value="{{ $versiyon->id }}" selected>{{ $versiyon->versiyon }}</option>
                                                            @else
                                                        <option value="{{ $versiyon->id }}">{{ $versiyon->versiyon }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-12" style="margin-bottom: 15px;text-align:left">Diğer Bilgiler </label>
                                            <div class="col-md-12">
                                                    <textarea class="form-control" rows="7"  name="entegrasyondiger" style="max-width:100%;">{{Input::old('entegrasyondiger')}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								        @elseif($anaprogram->id==5)
                                <div class="panel ipman panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <img src="{{ URL::to('assets/images/ipman.png')}}" style="width:30px;height: 30px;margin-right: 5px"/>
                                            Ipman Bilgileri   
                                            <img src="{{ URL::to('assets/images/sqlserver.png')}}" style="width:25px;height: 30px;margin-left:33% ;margin-right: 5px"/>
                                            SQL Server Bilgileri</h3>
                                        
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Versiyon </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="ipmanversiyon" value="{{Input::old('ipmanversiyon')}}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Versiyon </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="ipmansqlversiyon" value="{{Input::old('ipmansqlversiyon')}}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Kullanıcı Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="ipmankullanici" value="{{Input::old('ipmankullanici')}}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Server Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="ipmanveritabani" value="{{Input::old('ipmanveritabani')}}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="ipmansifre" value="{{Input::old('ipmansifre')}}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Kullanıcı Adı </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="ipmansqlkullanici" value="{{Input::old('ipmansqlkullanici')}}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Manas Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="ipmanmanas" value="{{Input::old('ipmanmanas')}}"  class="form-control">
                                            </div>
                                            <label class="control-label col-md-2">Şifresi </label>
                                            <div class="col-md-3">
                                                    <input type="text" name="ipmansqlsifre" value="{{Input::old('ipmansqlsifre')}}"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Diğer Bilgiler </label>
                                            <div class="col-md-3">
                                                    <textarea class="form-control" rows="5"  name="ipmandiger" style="max-width:100%;">{{Input::old('ipmandiger')}}</textarea>
                                            </div>
                                            <label class="control-label col-md-2">Diğer Bilgiler </label>
                                            <div class="col-md-3">
                                                    <textarea class="form-control" rows="5"  name="ipmansqldiger" style="max-width:100%;">{{Input::old('ipmansqldiger')}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_7">
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
                                    <label class="control-label col-md-2">Baskı Türleri</label>
                                    <div class="col-md-8">
                                        <select class="form-control select2 select2-offscreen" multiple="" tabindex="-1" id="options9" name="options9[]">
                                            @foreach($baskiturler as $baskitur)
                                            <option value="{{ $baskitur->id }}">{{ $baskitur->adi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @if(Input::old('options9'))
                                <div id="baskiturler" class="hide baskiturler">
                                    @foreach(Input::old('options9') as $baskitur)
                                        {{ $baskitur }}
                                    @endforeach
                                </div>
                                @else
                                <div id="baskiturekli" class="hide baskiturekli">{{ $musteri->baskiturleri }}</div>
                                @endif
                                @if($eklibaskilar)
                                @foreach($eklibaskilar as $baski)
                                    @foreach($baskiturler as $baskitur)
                                        @if($baski->edestekbaskitur_id==$baskitur->id && $baskitur->id==1)
                                <div class="panel subaski panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Su Kart Baskısı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="@if($baski->onresim){{ URL::to('assets/images/baski/'.$baski->onresim.'') }} @endif" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="suontaraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="@if($baski->arkaresim){{ URL::to('assets/images/baski/'.$baski->arkaresim.'') }} @endif" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="suarkataraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($baski->edestekbaskitur_id==$baskitur->id && $baskitur->id==2)
                                <div class="panel klmbaski panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Kalorimetre Kart Baskısı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="@if($baski->onresim){{ URL::to('assets/images/baski/'.$baski->onresim.'') }} @endif" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="klmontaraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="@if($baski->arkaresim){{ URL::to('assets/images/baski/'.$baski->arkaresim.'') }} @endif" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="klmarkataraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($baski->edestekbaskitur_id==$baskitur->id && $baskitur->id==3)
                                <div class="panel manasbaski panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Manas Kart Baskısı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4">
                                                <div class="thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="{{ URL::to('assets/images/manasbaskion.png') }}" alt=""/>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4">
                                                <div class="thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="{{ URL::to('assets/images/manasbaskiarka.png') }}" alt=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($baski->edestekbaskitur_id==$baskitur->id && $baskitur->id==4)
                                <div class="panel trifazebaski panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Trifaze Elektrik Kart Baskısı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="@if($baski->onresim){{ URL::to('assets/images/baski/'.$baski->onresim.'') }} @endif" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="trifazeontaraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="@if($baski->arkaresim){{ URL::to('assets/images/baski/'.$baski->arkaresim.'') }} @endif" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="trifazearkataraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($baski->edestekbaskitur_id==$baskitur->id && $baskitur->id==5)
                                <div class="panel monofazebaski panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Monofaze Elektrik Kart Baskısı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="@if($baski->onresim){{ URL::to('assets/images/baski/'.$baski->onresim.'') }} @endif" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="monoontaraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="@if($baski->arkaresim){{ URL::to('assets/images/baski/'.$baski->arkaresim.'') }} @endif" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="monoarkataraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($baski->edestekbaskitur_id==$baskitur->id && $baskitur->id==6)
                                <div class="panel baskisiz panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Baskısız Kart</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4">
                                                <div class="thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="" alt=""/>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4">
                                                <div class="thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="" alt=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($baski->edestekbaskitur_id==$baskitur->id && $baskitur->id==7)
                                <div class="panel klimatikbaski panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Klimatik Kart Baskısı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="@if($baski->onresim){{ URL::to('assets/images/baski/'.$baski->onresim.'') }} @endif" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="klimaontaraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="@if($baski->arkaresim){{ URL::to('assets/images/baski/'.$baski->arkaresim.'') }} @endif" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="klimaarkataraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($baski->edestekbaskitur_id==$baskitur->id && $baskitur->id==8)
                                <div class="panel gaz panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Gaz Kart Baskısı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="@if($baski->onresim){{ URL::to('assets/images/baski/'.$baski->onresim.'') }} @endif" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="gazontaraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="@if($baski->arkaresim){{ URL::to('assets/images/baski/'.$baski->arkaresim.'') }} @endif" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="gazarkataraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($baski->edestekbaskitur_id==$baskitur->id && $baskitur->id==9)
                                <div class="panel mifare panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Mifare Kart Baskısı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="@if($baski->onresim){{ URL::to('assets/images/baski/'.$baski->onresim.'') }} @endif" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="mifareontaraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="@if($baski->arkaresim){{ URL::to('assets/images/baski/'.$baski->arkaresim.'') }} @endif" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="mifarearkataraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($baski->edestekbaskitur_id==$baskitur->id && $baskitur->id==10)
                                <div class="panel mifaremanas panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Mifare Manas Kart Baskısı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4">
                                                <div class="thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="{{ URL::to('assets/images/mifaremanason.png') }}" alt=""/>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4">
                                                <div class="thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="{{ URL::to('assets/images/mifaremanasarka.png') }}" alt=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @endif
                                    @endforeach
                                @endforeach
                                @endif
                                @if($digerbaskilar)
                                    @foreach($digerbaskilar as $baskitur)
                                        @if($baskitur->id==1)
                                <div class="panel subaski panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Su Kart Baskısı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="suontaraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="suarkataraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($baskitur->id==2)
                                <div class="panel klmbaski panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Kalorimetre Kart Baskısı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="klmontaraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="klmarkataraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($baskitur->id==3)
                                <div class="panel manasbaski panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Manas Kart Baskısı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4">
                                                <div class="thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="{{ URL::to('assets/images/manasbaskion.png') }}" alt=""/>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4">
                                                <div class="thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="{{ URL::to('assets/images/manasbaskiarka.png') }}" alt=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($baskitur->id==4)
                                <div class="panel trifazebaski panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Trifaze Elektrik Kart Baskısı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="trifazeontaraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="trifazearkataraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($baskitur->id==5)
                                <div class="panel monofazebaski panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Monofaze Elektrik Kart Baskısı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="monoontaraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="monoarkataraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($baskitur->id==6)
                                <div class="panel baskisiz panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Baskısız Kart</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4">
                                                <div class="thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="" alt=""/>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4">
                                                <div class="thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="" alt=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($baskitur->id==7)
                                <div class="panel klimatikbaski panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Klimatik Kart Baskısı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="klimaontaraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="klimaarkataraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($baskitur->id==8)
                                <div class="panel gazbaski panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Klimatik Kart Baskısı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="klimaontaraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="klimaarkataraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($baskitur->id==9)
                                <div class="panel mifarebaski panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Mifare Kart Baskısı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="mifareontaraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 400px; max-height: 256px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="mifarearkataraf" accept="image/*" />
                                                    </span>
                                                    <a href="javascript:" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @elseif($baskitur->id==10)
                                <div class="panel mifaremanasbaski panel-default hide">
                                    <div class="panel-heading">
                                        <h3 class="panel-title ">
                                            Mifare Manas Kart Baskısı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-1">Ön Taraf </label>
                                            <div class="col-md-4">
                                                <div class="thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="{{ URL::to('assets/images/mifaremanason.png') }}" alt=""/>
                                                </div>
                                            </div>
                                            <label class="control-label col-md-1">Arka Taraf </label>
                                            <div class="col-md-4">
                                                <div class="thumbnail" style="width: 400px; height: 256px;">
                                                    <img src="{{ URL::to('assets/images/mifaremanasarka.png') }}" alt=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">{{ Form::token() }}</div>
                </form>
        <!-- END FORM-->
            </div>
        </div>
    </div>
<!-- END VALIDATION STATES-->
</div>
                
                
@stop

@section('modal')
@stop
