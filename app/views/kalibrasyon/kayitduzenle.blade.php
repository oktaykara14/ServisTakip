@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Gaz Kalibrasyon Kayıt <small>Düzenleme Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::to('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" >
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.tr.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery.quicksearch.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/datatables.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/kalibrasyon/form-validation-1.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
   FormValidationKalibrasyon.init();
});
</script>
<script>
    $(document).ready(function() {
        $("#sira").on('change', function () {
            var sira = $(this).val();
            var noktasayi = $('#noktasayi').val();
            var fark1 = 0, fark2 = 0, fark3 = 0;
            var result,sapma;
            if (parseInt(noktasayi) === 3) //nokta sayısına göre sonuçları çekecez
           { //diyafram sayaçlar için
               var sonuc1 = $('.sonuc1');
               var sonuc2 = $('.sonuc2');
               var sonuc3 = $('.sonuc3');
               switch (sira) {
                   case 1:  fark1=0;   fark2=0;        fark3=0;        break;
                   case 2:  fark1=0.1; fark2=0.05;     fark3=0.03;     break;
                   case 3:  fark1=0.2; fark2=0.10;     fark3=0.06;     break;
                   case 4:  fark1=0.3; fark2=0.15;     fark3=0.09;     break;
                   case 5:  fark1=0.5; fark2=0.20;     fark3=0.12;     break;
                   case 6:  fark1=0.7; fark2=0.25;     fark3=0.15;     break;
                   case 7:  fark1=0.9; fark2=0.30;     fark3=0.18;     break;
                   case 8:  fark1=1.0; fark2=0.35;     fark3=0.21;     break;
                   case 9:  fark1=1.2; fark2=0.40;     fark3=0.24;     break;
                   case 10: fark1=1.3; fark2=0.50;     fark3=0.27;     break;
                   case 11: fark1=1.4; fark2=0.55;     fark3=0.30;     break;
                   case 12: fark1=1.5; fark2=0.60;     fark3=0.33;     break;
                }
               $(".sonuc1").attr('data-fark',fark1);
               $(".sonuc2").attr('data-fark',fark2);
               $(".sonuc3").attr('data-fark',fark3);
               if (sonuc1.val() !== "") {
                   result = parseFloat((sonuc1.val()).replace(',', '.'));
                   sapma = parseFloat(sonuc1.data('id'));
                   if (result.toFixed(3) > sapma || result.toFixed(3) < (sapma * -1)) {
                       sonuc1.css("background-color", "red");
                   } else {
                       sonuc1.css("background-color", "green");
                   }
               } else {
                   sonuc1.css("background-color", "red");
               }
               sonuc1.css("color", "white");
               if (sonuc2.val() !== "") {
                   result = parseFloat((sonuc2.val()).replace(',', '.'));
                   sapma = parseFloat(sonuc2.data('id'));
                   if (result.toFixed(3) > sapma || result.toFixed(3) < (sapma * -1)) {
                       sonuc2.css("background-color", "red");
                   } else {
                       sonuc2.css("background-color", "green");
                   }
               } else {
                   sonuc2.css("background-color", "red");
               }
               sonuc2.css("color", "white");
               if (sonuc3.val() !== "") {
                   result = parseFloat((sonuc3.val()).replace(',', '.'));
                   sapma = parseFloat(sonuc3.data('id'));
                   if (result.toFixed(3) > sapma || result.toFixed(3) < (sapma * -1)) {
                       sonuc3.css("background-color", "red");
                   } else {
                       sonuc3.css("background-color", "green");
                   }
               } else {
                   sonuc3.css("background-color", "red");
               }
               sonuc3.css("color", "white");
            }
        });
        $('#hf2').on('change', function () {
            if ($(this).attr('checked')) {
                $(".hf2").removeClass('hide');
            } else {
                $(".hf2").addClass('hide');
            }
        });
        $('#hf3').on('change', function () {
            if ($(this).attr('checked')) {
                $(".hf3").removeClass('hide');
            } else {
                $(".hf3").addClass('hide');
            }
        });
        $('#hf32').on('change', function () {
            if ($(this).attr('checked')) {
                $(".hf32").removeClass('hide');
            } else {
                $(".hf32").addClass('hide');
            }
        });

        $('#formsubmit').click(function () {
            $("#lf").removeAttr("disabled");
            $('#form_sample').submit();
        });
        $('input.valid0').each(function(){
            $(this).rules('remove');
            $(this).rules('add', {
                required: true
            });
        });
        $(".sonuc1").inputmask("decimal", {
            radixPoint: ",",
            groupSeparator: "",
            digits: 3,
            autoGroup: true
        });
        $(".sonuc2").inputmask("decimal", {
            radixPoint: ",",
            groupSeparator: "",
            digits: 3,
            autoGroup: true
        });
        $(".sonuc3").inputmask("decimal", {
            radixPoint: ",",
            groupSeparator: "",
            digits: 3,
            autoGroup: true
        });
        $(".sonuc4").inputmask("decimal", {
            radixPoint: ",",
            groupSeparator: "",
            digits: 3,
            autoGroup: true
        });
        $(".sonuc").inputmask("decimal", {
            radixPoint: ",",
            groupSeparator: "",
            digits: 3,
            autoGroup: true
        });
        $('.sonuc1').on('change', function () {
            var sonuc = $(this);
            if (sonuc.val() !== "") {
                var result = parseFloat((sonuc.val()).replace(',', '.'));
                var sapma = parseFloat(sonuc.data('id'));
                if (result.toFixed(3) > sapma || result.toFixed(3) < (sapma * -1)) {
                    sonuc.css("background-color", "red");
                } else {
                    sonuc.css("background-color", "green");
                }
            } else {
                sonuc.css("background-color", "red");
            }
            sonuc.css("color", "white");
        });
        $('.sonuc2').on('change', function () {
            var sonuc = $(this);
            if (sonuc.val() !== "") {
                var result = parseFloat((sonuc.val()).replace(',', '.'));
                var sapma = parseFloat(sonuc.data('id'));
                if (result.toFixed(3) > sapma || result.toFixed(3) < (sapma * -1)) {
                    sonuc.css("background-color", "red");
                } else {
                    sonuc.css("background-color", "green");
                }
            } else {
                sonuc.css("background-color", "red");
            }
            sonuc.css("color", "white");
        });
        $('.sonuc3').on('change', function () {
            var sonuc = $(this);
            if (sonuc.val() !== "") {
                var result = parseFloat((sonuc.val()).replace(',', '.'));
                var sapma = parseFloat(sonuc.data('id'));
                if (result.toFixed(3) > sapma || result.toFixed(3) < (sapma * -1)) {
                    sonuc.css("background-color", "red");
                } else {
                    sonuc.css("background-color", "green");
                }
            } else {
                sonuc.css("background-color", "red");
            }
            sonuc.css("color", "white");

        });
        $('.sonuc4').on('change', function () {
            var sonuc = $(this);
            if (sonuc.val() !== "") {
                var result = parseFloat((sonuc.val()).replace(',', '.'));
                var sapma = parseFloat(sonuc.data('id'));
                if (result.toFixed(3) > sapma || result.toFixed(3) < (sapma * -1)) {
                    sonuc.css("background-color", "red");
                } else {
                    sonuc.css("background-color", "green");
                }
            } else {
                sonuc.css("background-color", "red");
            }
            sonuc.css("color", "white");

        });
        $('.sonuc').on('change', function () {
            var sonuc = $(this);
            if (sonuc.val() !== "") {
                var result = parseFloat((sonuc.val()).replace(',', '.'));
                var sapma = parseFloat(sonuc.data('id'));
                if (result.toFixed(3) > sapma || result.toFixed(3) < (sapma * -1)) {
                    sonuc.css("background-color", "red");
                } else {
                    sonuc.css("background-color", "green");
                }
            } else {
                sonuc.css("background-color", "red");
            }
            sonuc.css("color", "white");
        });
        $('#form_sample').valid();
    });
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-pencil"></i>Kalibrasyon Kayıt Düzenleme
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('kalibrasyon/kayitduzenle/'.$kalibrasyon->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
            <div class="form-body">
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    Girilen Bilgilerde Hata Var.
                </div>
                <div class="alert alert-success display-hide">
                    <button class="close" data-close="alert"></button>
                    Bilgiler Doğru!
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4"><b>Cari Adı:</b></label>
                    <label class="col-sm-10 col-xs-8" style="padding-top: 9px">{{ $grup->netsiscari->cariadi}}</label>
                    <input class="hide" id="grupid" name="grupid" value="{{ $grup->id}}"/>
                    <input type="text" id="sayacsayi" name="sayacsayi" value="{{$kalibrasyon->istasyon->sayacsayi}}" data-required="1" class="form-control hide">
                    <input type="text" id="noktasayi" name="noktasayi" value="{{$kalibrasyonstandart->noktasayisi}}" data-required="1" class="form-control hide">
                    <input type="text" id="secilenler" name="secilenler" value="{{$kalibrasyon->id}}" data-required="1" class="form-control hide">
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">İstasyon Adı:</label>
                    <label class="col-xs-8" style="padding-top: 9px">{{ $kalibrasyon->istasyon->istasyonadi}} </label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Sayaç Adı:</label>
                    <label class="col-xs-8" style="padding-top: 9px">{{ $kalibrasyon->sayacadi->sayacadi}}</label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Ölçüm Hassasiyeti:</label>
                    <label class="col-xs-8" style="padding-top: 9px">{{ $kalibrasyonstandart->hassasiyet}} </label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Seri No:</label>
                    <label class="col-xs-8" style="padding-top: 9px">{{ $kalibrasyon->kalibrasyon_seri}} </label>
                </div>
                <div class="form-group @if($kalibrasyonstandart->id<>1) hide @endif">
                    <label class="control-label col-sm-2 col-xs-4">Kalibrasyon Sırası:</label>
                    <div class="col-sm-3 col-xs-8">
                        <select class="form-control select2me select2-offscreen" id="sira" name="sira" tabindex="-1" title="">
                            @for($i=1;$i<=$kalibrasyon->istasyon->sayacsayi;$i++)
                                @if($kalibrasyon->sira==$i)
                                    <option value="{{ $i }}" selected>{{ $i }}</option>
                                @else
                                    <option value="{{ $i }}" >{{ $i }}</option>
                                @endif
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="form-group noktalar hide">
                    <div class="form-group noktalar_ek"><input id="nokta1" name="nokta1" data-id="{{$kalibrasyonstandart->nokta1}}" value="{{$kalibrasyonstandart->sapma1}}"/></div>
                    <div class="form-group noktalar_ek"><input id="nokta2" name="nokta2" data-id="{{$kalibrasyonstandart->nokta2}}" value="{{$kalibrasyonstandart->sapma2}}"/></div>
                    <div class="form-group noktalar_ek"><input id="nokta3" name="nokta3" data-id="{{$kalibrasyonstandart->nokta3}}" value="{{$kalibrasyonstandart->sapma3}}"/></div>
                    <div class="form-group noktalar_ek"><input id="nokta4" name="nokta4" data-id="{{$kalibrasyonstandart->nokta4}}" value="{{$kalibrasyonstandart->sapma4}}"/></div>
                    <div class="form-group noktalar_ek"><input id="nokta5" name="nokta5" data-id="{{$kalibrasyonstandart->nokta5}}" value="{{$kalibrasyonstandart->sapma5}}"/></div>
                    <div class="form-group noktalar_ek"><input id="nokta6" name="nokta6" data-id="{{$kalibrasyonstandart->nokta6}}" value="{{$kalibrasyonstandart->sapma6}}"/></div>
                    <div class="form-group noktalar_ek"><input id="nokta7" name="nokta7" data-id="{{$kalibrasyonstandart->nokta7}}" value="{{$kalibrasyonstandart->sapma7}}"/></div>
                </div>
                <div class="form-group col-xs-12 lfhf @if($kalibrasyonstandart->id==1) hide @endif">
                    <h4 style="padding-left: 30px;">Test Frekansları</h4>
                    <div class="form-group lfhf_ek">
                        <label class="control-label col-sm-2 col-xs-4"><input type="checkbox" id=lf name="lf" checked disabled/> LF </label>
                        <label class="control-label col-sm-2 col-xs-4"><input type="checkbox" id=hf2 name="hf2" @if($kalibrasyon->hf2==1) checked @endif /> HF2 </label>
                        <label class="control-label col-sm-2 col-xs-4"><input type="checkbox" id=hf3 name="hf3" @if($kalibrasyon->hf3==1) checked @endif /> HF3-I </label>
                        <label class="control-label col-sm-2 col-xs-4"><input type="checkbox" id=hf32 name="hf32" @if($kalibrasyon->hf32==1) checked @endif /> HF3-II </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-6"> Kalibrasyon Bilgileri</label>
                </div>
                <div class="form-group sayaclar">
                @if($kalibrasyonstandart->noktasayisi==3)
                    <table class="col-sm-10 col-sm-offset-1 col-xs-11 sayaclar_ek">
                        <tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label{{$kalibrasyon->id}}" data-id="1">Seri No</b></td><td><b>Ölçüm</b></td><td><b>Sonuç</b></td></tr>
                        <tr class="rows row1" data-id="1"><td class="serino serino{{$kalibrasyon->id}}">{{$kalibrasyon->kalibrasyon_seri}}<input type="text" id="serino[0]" name="serino[0]" class="form-control hide" value="{{$kalibrasyon->kalibrasyon_seri}}"/><input type="text" id="kalibrasyonid[0]" name="kalibrasyonid[0]" class="form-control hide" value="{{$kalibrasyon->id}}"/></td><td>{{$kalibrasyonstandart->nokta1}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc[0][0]" name="sonuc[0][0]" data-id="{{$kalibrasyonstandart->sapma1}}" style="color:white;background-color:{{$kalibrasyon->sonuc1>$kalibrasyonstandart->sapma1 || $kalibrasyon->sonuc1<-($kalibrasyonstandart->sapma1) ? "red" : "green" }}" data-fark="{{$kalibrasyon->fark1}}" class="form-control valid0 sonuc sonuc1" tabindex="1" value="{{$kalibrasyon->sonuc1}}"></td>
                        </tr>
                        <tr class="rows row2" data-id="2" style="padding-top:10px;"><td></td><td>{{$kalibrasyonstandart->nokta2}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc[0][1]" name="sonuc[0][1]" data-id="{{$kalibrasyonstandart->sapma2}}" style="color:white;background-color:{{$kalibrasyon->sonuc2>$kalibrasyonstandart->sapma2 || $kalibrasyon->sonuc2<-($kalibrasyonstandart->sapma2) ? "red" : "green" }}" data-fark="{{$kalibrasyon->fark2}}" class="form-control valid0 sonuc sonuc2" tabindex="2" value="{{$kalibrasyon->sonuc2}}"></td>
                        </tr>
                        <tr class="rows row3" data-id="3" style="padding-top:10px;"><td></td><td>{{$kalibrasyonstandart->nokta3}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc[0][2]" name="sonuc[0][2]" data-id="{{$kalibrasyonstandart->sapma3}}" style="color:white;background-color:{{$kalibrasyon->sonuc3>$kalibrasyonstandart->sapma3 || $kalibrasyon->sonuc3<-($kalibrasyonstandart->sapma3) ? "red" : "green" }}" data-fark="{{$kalibrasyon->fark3}}" class="form-control valid0 sonuc sonuc3" tabindex="3" value="{{$kalibrasyon->sonuc3}}"></td>
                        </tr>
                    </table>
                    @elseif($kalibrasyonstandart->noktasayisi==5)
                    <table class="col-sm-10 col-sm-offset-1 col-xs-11 sayaclar_ek">
                        <tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label{{$kalibrasyon->id}}" data-id="1">Seri No</b></td>
                            <td><b>Ölçüm</b></td><td class="lf"><b>LF Sonuç</b></td><td class="hf2 {{$kalibrasyon->hf2 ? "" : "hide"}}"><b>HF2 Sonuç</b></td><td class="hf3 {{$kalibrasyon->hf3 ? "" : "hide"}}"><b>HF3-I Sonuç</b></td><td class="hf32 {{$kalibrasyon->hf32 ? "" : "hide"}}"><b>HF3-II Sonuç</b></td></tr>
                        <tr class="rows row1" data-id="1"><td class="serino serino{{$kalibrasyon->id}}">{{$kalibrasyon->kalibrasyon_seri}}<input type="text" id="serino[0]" name="serino[0]" class="form-control hide" value="{{$kalibrasyon->kalibrasyon_seri}}"/><input type="text" id="kalibrasyonid[0]" name="kalibrasyonid[0]" class="form-control hide" value="{{$kalibrasyon->id}}"/></td><td>{{$kalibrasyonstandart->nokta1}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[0][0]" name="sonuc1[0][0]" data-id="{{$kalibrasyonstandart->sapma1}}" style="color:white;background-color:{{$kalibrasyon->sonuc1>$kalibrasyonstandart->sapma1 || $kalibrasyon->sonuc1<-($kalibrasyonstandart->sapma1) ? "red" : "green" }}" class="form-control valid0 lf sonuc1" maxlength="9" tabindex="1" value="{{$kalibrasyon->sonuc1}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[0][0]" name="sonuc2[0][0]" data-id="{{$kalibrasyonstandart->sapma1}}" style="color:{{$kalibrasyon->hf2sonuc1==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf2sonuc1==''  ? "white" :($kalibrasyon->hf2sonuc1>$kalibrasyonstandart->sapma1  || $kalibrasyon->hf2sonuc1<-($kalibrasyonstandart->sapma1)  ? "red" : "green") }}" class="form-control valid0 hf2 sonuc2  {{$kalibrasyon->hf2  ? "" : "hide"}}" maxlength="9" tabindex="6" value="{{$kalibrasyon->hf2sonuc1}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[0][0]" name="sonuc3[0][0]" data-id="{{$kalibrasyonstandart->sapma1}}" style="color:{{$kalibrasyon->hf3sonuc1==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf3sonuc1==''  ? "white" :($kalibrasyon->hf3sonuc1>$kalibrasyonstandart->sapma1  || $kalibrasyon->hf3sonuc1<-($kalibrasyonstandart->sapma1)  ? "red" : "green") }}" class="form-control valid0 hf3 sonuc3  {{$kalibrasyon->hf3  ? "" : "hide"}}" maxlength="9" tabindex="11" value="{{$kalibrasyon->hf3sonuc1}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[0][0]" name="sonuc4[0][0]" data-id="{{$kalibrasyonstandart->sapma1}}" style="color:{{$kalibrasyon->hf32sonuc1=='' ? "black" : "white"}};background-color:{{$kalibrasyon->hf32sonuc1=='' ? "white" :($kalibrasyon->hf32sonuc1>$kalibrasyonstandart->sapma1 || $kalibrasyon->hf32sonuc1<-($kalibrasyonstandart->sapma1) ? "red" : "green") }}" class="form-control valid0 hf32 sonuc4 {{$kalibrasyon->hf32 ? "" : "hide"}}" maxlength="9" tabindex="16" value="{{$kalibrasyon->hf32sonuc1}}"></td></tr>
                        <tr class="rows row2" data-id="2"><td></td><td>{{$kalibrasyonstandart->nokta2}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[0][1]" name="sonuc1[0][1]" data-id="{{$kalibrasyonstandart->sapma2}}" style="color:white;background-color:{{$kalibrasyon->sonuc2>$kalibrasyonstandart->sapma2 || $kalibrasyon->sonuc2<-($kalibrasyonstandart->sapma2) ? "red" : "green" }}" class="form-control valid0 lf sonuc1" maxlength="9" tabindex="2" value="{{$kalibrasyon->sonuc2}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[0][1]" name="sonuc2[0][1]" data-id="{{$kalibrasyonstandart->sapma2}}" style="color:{{$kalibrasyon->hf2sonuc2==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf2sonuc2==''  ? "white" :($kalibrasyon->hf2sonuc2>$kalibrasyonstandart->sapma2  || $kalibrasyon->hf2sonuc2<-($kalibrasyonstandart->sapma2)  ? "red" : "green") }}" class="form-control valid0 hf2 sonuc2  {{$kalibrasyon->hf2  ? "" : "hide"}}" maxlength="9" tabindex="7" value="{{$kalibrasyon->hf2sonuc2}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[0][1]" name="sonuc3[0][1]" data-id="{{$kalibrasyonstandart->sapma2}}" style="color:{{$kalibrasyon->hf3sonuc2==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf3sonuc2==''  ? "white" :($kalibrasyon->hf3sonuc2>$kalibrasyonstandart->sapma2  || $kalibrasyon->hf3sonuc2<-($kalibrasyonstandart->sapma2)  ? "red" : "green") }}" class="form-control valid0 hf3 sonuc3  {{$kalibrasyon->hf3  ? "" : "hide"}}" maxlength="9" tabindex="12" value="{{$kalibrasyon->hf3sonuc2}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[0][1]" name="sonuc4[0][1]" data-id="{{$kalibrasyonstandart->sapma2}}" style="color:{{$kalibrasyon->hf32sonuc2=='' ? "black" : "white"}};background-color:{{$kalibrasyon->hf32sonuc2=='' ? "white" :($kalibrasyon->hf32sonuc2>$kalibrasyonstandart->sapma2 || $kalibrasyon->hf32sonuc2<-($kalibrasyonstandart->sapma2) ? "red" : "green") }}" class="form-control valid0 hf32 sonuc4 {{$kalibrasyon->hf32 ? "" : "hide"}}" maxlength="9" tabindex="17" value="{{$kalibrasyon->hf32sonuc2}}"></td></tr>
                        <tr class="rows row3" data-id="3"><td></td><td>{{$kalibrasyonstandart->nokta3}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[0][2]" name="sonuc1[0][2]" data-id="{{$kalibrasyonstandart->sapma3}}" style="color:white;background-color:{{$kalibrasyon->sonuc3>$kalibrasyonstandart->sapma3 || $kalibrasyon->sonuc3<-($kalibrasyonstandart->sapma3) ? "red" : "green" }}" class="form-control valid0 lf sonuc1" maxlength="9" tabindex="3" value="{{$kalibrasyon->sonuc3}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[0][2]" name="sonuc2[0][2]" data-id="{{$kalibrasyonstandart->sapma3}}" style="color:{{$kalibrasyon->hf2sonuc3==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf2sonuc3==''  ? "white" :($kalibrasyon->hf2sonuc3>$kalibrasyonstandart->sapma3  || $kalibrasyon->hf2sonuc3<-($kalibrasyonstandart->sapma3)  ? "red" : "green") }}" class="form-control valid0 hf2 sonuc2  {{$kalibrasyon->hf2  ? "" : "hide"}}" maxlength="9" tabindex="8" value="{{$kalibrasyon->hf2sonuc3}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[0][2]" name="sonuc3[0][2]" data-id="{{$kalibrasyonstandart->sapma3}}" style="color:{{$kalibrasyon->hf3sonuc3==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf3sonuc3==''  ? "white" :($kalibrasyon->hf3sonuc3>$kalibrasyonstandart->sapma3  || $kalibrasyon->hf3sonuc3<-($kalibrasyonstandart->sapma3)  ? "red" : "green") }}" class="form-control valid0 hf3 sonuc3  {{$kalibrasyon->hf3  ? "" : "hide"}}" maxlength="9" tabindex="13" value="{{$kalibrasyon->hf3sonuc3}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[0][2]" name="sonuc4[0][2]" data-id="{{$kalibrasyonstandart->sapma3}}" style="color:{{$kalibrasyon->hf32sonuc3=='' ? "black" : "white"}};background-color:{{$kalibrasyon->hf32sonuc3=='' ? "white" :($kalibrasyon->hf32sonuc3>$kalibrasyonstandart->sapma3 || $kalibrasyon->hf32sonuc3<-($kalibrasyonstandart->sapma3) ? "red" : "green") }}" class="form-control valid0 hf32 sonuc4 {{$kalibrasyon->hf32 ? "" : "hide"}}" maxlength="9" tabindex="18" value="{{$kalibrasyon->hf32sonuc3}}"></td></tr>
                        <tr class="rows row4" data-id="4"><td></td><td>{{$kalibrasyonstandart->nokta4}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[0][3]" name="sonuc1[0][3]" data-id="{{$kalibrasyonstandart->sapma4}}" style="color:white;background-color:{{$kalibrasyon->sonuc4>$kalibrasyonstandart->sapma4 || $kalibrasyon->sonuc4<-($kalibrasyonstandart->sapma4) ? "red" : "green" }}" class="form-control valid0 lf sonuc1" maxlength="9" tabindex="4" value="{{$kalibrasyon->sonuc4}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[0][3]" name="sonuc2[0][3]" data-id="{{$kalibrasyonstandart->sapma4}}" style="color:{{$kalibrasyon->hf2sonuc4==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf2sonuc4==''  ? "white" :($kalibrasyon->hf2sonuc4>$kalibrasyonstandart->sapma4  || $kalibrasyon->hf2sonuc4<-($kalibrasyonstandart->sapma4)  ? "red" : "green") }}" class="form-control valid0 hf2 sonuc2  {{$kalibrasyon->hf2  ? "" : "hide"}}" maxlength="9" tabindex="9" value="{{$kalibrasyon->hf2sonuc4}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[0][3]" name="sonuc3[0][3]" data-id="{{$kalibrasyonstandart->sapma4}}" style="color:{{$kalibrasyon->hf3sonuc4==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf3sonuc4==''  ? "white" :($kalibrasyon->hf3sonuc4>$kalibrasyonstandart->sapma4  || $kalibrasyon->hf3sonuc4<-($kalibrasyonstandart->sapma4)  ? "red" : "green") }}" class="form-control valid0 hf3 sonuc3  {{$kalibrasyon->hf3  ? "" : "hide"}}" maxlength="9" tabindex="14" value="{{$kalibrasyon->hf3sonuc4}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[0][3]" name="sonuc4[0][3]" data-id="{{$kalibrasyonstandart->sapma4}}" style="color:{{$kalibrasyon->hf32sonuc4=='' ? "black" : "white"}};background-color:{{$kalibrasyon->hf32sonuc4=='' ? "white" :($kalibrasyon->hf32sonuc4>$kalibrasyonstandart->sapma4 || $kalibrasyon->hf32sonuc4<-($kalibrasyonstandart->sapma4) ? "red" : "green") }}" class="form-control valid0 hf32 sonuc4 {{$kalibrasyon->hf32 ? "" : "hide"}}" maxlength="9" tabindex="19" value="{{$kalibrasyon->hf32sonuc4}}"></td></tr>
                        <tr class="rows row5" data-id="5"><td></td><td>{{$kalibrasyonstandart->nokta5}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[0][4]" name="sonuc1[0][4]" data-id="{{$kalibrasyonstandart->sapma5}}" style="color:white;background-color:{{$kalibrasyon->sonuc5>$kalibrasyonstandart->sapma5 || $kalibrasyon->sonuc5<-($kalibrasyonstandart->sapma5) ? "red" : "green" }}" class="form-control valid0 lf sonuc1" maxlength="9" tabindex="5" value="{{$kalibrasyon->sonuc5}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[0][4]" name="sonuc2[0][4]" data-id="{{$kalibrasyonstandart->sapma5}}" style="color:{{$kalibrasyon->hf2sonuc5==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf2sonuc5==''  ? "white" :($kalibrasyon->hf2sonuc5>$kalibrasyonstandart->sapma5  || $kalibrasyon->hf2sonuc5<-($kalibrasyonstandart->sapma5)  ? "red" : "green") }}" class="form-control valid0 hf2 sonuc2  {{$kalibrasyon->hf2  ? "" : "hide"}}" maxlength="9" tabindex="10" value="{{$kalibrasyon->hf2sonuc5}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[0][4]" name="sonuc3[0][4]" data-id="{{$kalibrasyonstandart->sapma5}}" style="color:{{$kalibrasyon->hf3sonuc5==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf3sonuc5==''  ? "white" :($kalibrasyon->hf3sonuc5>$kalibrasyonstandart->sapma5  || $kalibrasyon->hf3sonuc5<-($kalibrasyonstandart->sapma5)  ? "red" : "green") }}" class="form-control valid0 hf3 sonuc3  {{$kalibrasyon->hf3  ? "" : "hide"}}" maxlength="9" tabindex="15" value="{{$kalibrasyon->hf3sonuc5}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[0][4]" name="sonuc4[0][4]" data-id="{{$kalibrasyonstandart->sapma5}}" style="color:{{$kalibrasyon->hf32sonuc5=='' ? "black" : "white"}};background-color:{{$kalibrasyon->hf32sonuc5=='' ? "white" :($kalibrasyon->hf32sonuc5>$kalibrasyonstandart->sapma5 || $kalibrasyon->hf32sonuc5<-($kalibrasyonstandart->sapma5) ? "red" : "green") }}" class="form-control valid0 hf32 sonuc4 {{$kalibrasyon->hf32 ? "" : "hide"}}" maxlength="9" tabindex="20" value="{{$kalibrasyon->hf32sonuc5}}"></td></tr>
                    </table>
                    @elseif($kalibrasyonstandart->noktasayisi==6)
                    <table class="col-sm-10 col-sm-offset-1 col-xs-11 sayaclar_ek">
                        <tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label{{$kalibrasyon->id}}" data-id="1">Seri No</b></td>
                            <td><b>Ölçüm</b></td><td class="lf"><b>LF Sonuç</b></td><td class="hf2 {{$kalibrasyon->hf2 ? "" : "hide"}}"><b>HF2 Sonuç</b></td><td class="hf3 {{$kalibrasyon->hf3 ? "" : "hide"}}"><b>HF3-I Sonuç</b></td><td class="hf32 {{$kalibrasyon->hf32 ? "" : "hide"}}"><b>HF3-II Sonuç</b></td></tr>
                        <tr class="rows row1" data-id="1"><td class="serino serino{{$kalibrasyon->id}}">{{$kalibrasyon->kalibrasyon_seri}}<input type="text" id="serino[0]" name="serino[0]" class="form-control hide" value="{{$kalibrasyon->kalibrasyon_seri}}"/><input type="text" id="kalibrasyonid[0]" name="kalibrasyonid[0]" class="form-control hide" value="{{$kalibrasyon->id}}"/></td><td>{{$kalibrasyonstandart->nokta1}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[0][0]" name="sonuc1[0][0]" data-id="{{$kalibrasyonstandart->sapma1}}" style="color:white;background-color:{{$kalibrasyon->sonuc1>$kalibrasyonstandart->sapma1 || $kalibrasyon->sonuc1<-($kalibrasyonstandart->sapma1) ? "red" : "green" }}" class="form-control valid0 lf sonuc1" maxlength="9" tabindex="1" value="{{$kalibrasyon->sonuc1}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[0][0]" name="sonuc2[0][0]" data-id="{{$kalibrasyonstandart->sapma1}}" style="color:{{$kalibrasyon->hf2sonuc1==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf2sonuc1==''  ? "white" :($kalibrasyon->hf2sonuc1>$kalibrasyonstandart->sapma1  || $kalibrasyon->hf2sonuc1<-($kalibrasyonstandart->sapma1)  ? "red" : "green") }}" class="form-control valid0 hf2  sonuc2 {{$kalibrasyon->hf2  ? "" : "hide"}}" maxlength="9" tabindex="7"  value="{{$kalibrasyon->hf2sonuc1}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[0][0]" name="sonuc3[0][0]" data-id="{{$kalibrasyonstandart->sapma1}}" style="color:{{$kalibrasyon->hf3sonuc1==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf3sonuc1==''  ? "white" :($kalibrasyon->hf3sonuc1>$kalibrasyonstandart->sapma1  || $kalibrasyon->hf3sonuc1<-($kalibrasyonstandart->sapma1)  ? "red" : "green") }}" class="form-control valid0 hf3  sonuc3 {{$kalibrasyon->hf3  ? "" : "hide"}}" maxlength="9" tabindex="13" value="{{$kalibrasyon->hf3sonuc1}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[0][0]" name="sonuc4[0][0]" data-id="{{$kalibrasyonstandart->sapma1}}" style="color:{{$kalibrasyon->hf32sonuc1=='' ? "black" : "white"}};background-color:{{$kalibrasyon->hf32sonuc1=='' ? "white" :($kalibrasyon->hf32sonuc1>$kalibrasyonstandart->sapma1 || $kalibrasyon->hf32sonuc1<-($kalibrasyonstandart->sapma1) ? "red" : "green") }}" class="form-control valid0 hf32 sonuc4 {{$kalibrasyon->hf32 ? "" : "hide"}}" maxlength="9" tabindex="19" value="{{$kalibrasyon->hf32sonuc1}}"></td></tr>
                        <tr class="rows row2" data-id="2"><td></td><td>{{$kalibrasyonstandart->nokta2}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[0][1]" name="sonuc1[0][1]" data-id="{{$kalibrasyonstandart->sapma2}}" style="color:white;background-color:{{$kalibrasyon->sonuc2>$kalibrasyonstandart->sapma2 || $kalibrasyon->sonuc2<-($kalibrasyonstandart->sapma2) ? "red" : "green" }}" class="form-control valid0 lf sonuc1" maxlength="9" tabindex="2" value="{{$kalibrasyon->sonuc2}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[0][1]" name="sonuc2[0][1]" data-id="{{$kalibrasyonstandart->sapma2}}" style="color:{{$kalibrasyon->hf2sonuc2==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf2sonuc2==''  ? "white" :($kalibrasyon->hf2sonuc2>$kalibrasyonstandart->sapma2  || $kalibrasyon->hf2sonuc2<-($kalibrasyonstandart->sapma2)  ? "red" : "green") }}" class="form-control valid0 hf2  sonuc2 {{$kalibrasyon->hf2  ? "" : "hide"}}" maxlength="9" tabindex="8"  value="{{$kalibrasyon->hf2sonuc2}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[0][1]" name="sonuc3[0][1]" data-id="{{$kalibrasyonstandart->sapma2}}" style="color:{{$kalibrasyon->hf3sonuc2==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf3sonuc2==''  ? "white" :($kalibrasyon->hf3sonuc2>$kalibrasyonstandart->sapma2  || $kalibrasyon->hf3sonuc2<-($kalibrasyonstandart->sapma2)  ? "red" : "green") }}" class="form-control valid0 hf3  sonuc3 {{$kalibrasyon->hf3  ? "" : "hide"}}" maxlength="9" tabindex="14" value="{{$kalibrasyon->hf3sonuc2}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[0][1]" name="sonuc4[0][1]" data-id="{{$kalibrasyonstandart->sapma2}}" style="color:{{$kalibrasyon->hf32sonuc2=='' ? "black" : "white"}};background-color:{{$kalibrasyon->hf32sonuc2=='' ? "white" :($kalibrasyon->hf32sonuc2>$kalibrasyonstandart->sapma2 || $kalibrasyon->hf32sonuc2<-($kalibrasyonstandart->sapma2) ? "red" : "green") }}" class="form-control valid0 hf32 sonuc4 {{$kalibrasyon->hf32 ? "" : "hide"}}" maxlength="9" tabindex="20" value="{{$kalibrasyon->hf32sonuc2}}"></td></tr>
                        <tr class="rows row3" data-id="3"><td></td><td>{{$kalibrasyonstandart->nokta3}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[0][2]" name="sonuc1[0][2]" data-id="{{$kalibrasyonstandart->sapma3}}" style="color:white;background-color:{{$kalibrasyon->sonuc3>$kalibrasyonstandart->sapma3 || $kalibrasyon->sonuc3<-($kalibrasyonstandart->sapma3) ? "red" : "green" }}" class="form-control valid0 lf sonuc1" maxlength="9" tabindex="3" value="{{$kalibrasyon->sonuc3}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[0][2]" name="sonuc2[0][2]" data-id="{{$kalibrasyonstandart->sapma3}}" style="color:{{$kalibrasyon->hf2sonuc3==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf2sonuc3==''  ? "white" :($kalibrasyon->hf2sonuc3>$kalibrasyonstandart->sapma3  || $kalibrasyon->hf2sonuc3<-($kalibrasyonstandart->sapma3)  ? "red" : "green") }}" class="form-control valid0 hf2  sonuc2 {{$kalibrasyon->hf2  ? "" : "hide"}}" maxlength="9" tabindex="9"  value="{{$kalibrasyon->hf2sonuc3}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[0][2]" name="sonuc3[0][2]" data-id="{{$kalibrasyonstandart->sapma3}}" style="color:{{$kalibrasyon->hf3sonuc3==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf3sonuc3==''  ? "white" :($kalibrasyon->hf3sonuc3>$kalibrasyonstandart->sapma3  || $kalibrasyon->hf3sonuc3<-($kalibrasyonstandart->sapma3)  ? "red" : "green") }}" class="form-control valid0 hf3  sonuc3 {{$kalibrasyon->hf3  ? "" : "hide"}}" maxlength="9" tabindex="15" value="{{$kalibrasyon->hf3sonuc3}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[0][2]" name="sonuc4[0][2]" data-id="{{$kalibrasyonstandart->sapma3}}" style="color:{{$kalibrasyon->hf32sonuc3=='' ? "black" : "white"}};background-color:{{$kalibrasyon->hf32sonuc3=='' ? "white" :($kalibrasyon->hf32sonuc3>$kalibrasyonstandart->sapma3 || $kalibrasyon->hf32sonuc3<-($kalibrasyonstandart->sapma3) ? "red" : "green") }}" class="form-control valid0 hf32 sonuc4 {{$kalibrasyon->hf32 ? "" : "hide"}}" maxlength="9" tabindex="21" value="{{$kalibrasyon->hf32sonuc3}}"></td></tr>
                        <tr class="rows row4" data-id="4"><td></td><td>{{$kalibrasyonstandart->nokta4}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[0][3]" name="sonuc1[0][3]" data-id="{{$kalibrasyonstandart->sapma4}}" style="color:white;background-color:{{$kalibrasyon->sonuc4>$kalibrasyonstandart->sapma4 || $kalibrasyon->sonuc4<-($kalibrasyonstandart->sapma4) ? "red" : "green" }}" class="form-control valid0 lf sonuc1" maxlength="9" tabindex="4" value="{{$kalibrasyon->sonuc4}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[0][3]" name="sonuc2[0][3]" data-id="{{$kalibrasyonstandart->sapma4}}" style="color:{{$kalibrasyon->hf2sonuc4==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf2sonuc4==''  ? "white" :($kalibrasyon->hf2sonuc4>$kalibrasyonstandart->sapma4  || $kalibrasyon->hf2sonuc4<-($kalibrasyonstandart->sapma4)  ? "red" : "green") }}" class="form-control valid0 hf2  sonuc2 {{$kalibrasyon->hf2  ? "" : "hide"}}" maxlength="9" tabindex="10" value="{{$kalibrasyon->hf2sonuc4}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[0][3]" name="sonuc3[0][3]" data-id="{{$kalibrasyonstandart->sapma4}}" style="color:{{$kalibrasyon->hf3sonuc4==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf3sonuc4==''  ? "white" :($kalibrasyon->hf3sonuc4>$kalibrasyonstandart->sapma4  || $kalibrasyon->hf3sonuc4<-($kalibrasyonstandart->sapma4)  ? "red" : "green") }}" class="form-control valid0 hf3  sonuc3 {{$kalibrasyon->hf3  ? "" : "hide"}}" maxlength="9" tabindex="16" value="{{$kalibrasyon->hf3sonuc4}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[0][3]" name="sonuc4[0][3]" data-id="{{$kalibrasyonstandart->sapma4}}" style="color:{{$kalibrasyon->hf32sonuc4=='' ? "black" : "white"}};background-color:{{$kalibrasyon->hf32sonuc4=='' ? "white" :($kalibrasyon->hf32sonuc4>$kalibrasyonstandart->sapma4 || $kalibrasyon->hf32sonuc4<-($kalibrasyonstandart->sapma4) ? "red" : "green") }}" class="form-control valid0 hf32 sonuc4 {{$kalibrasyon->hf32 ? "" : "hide"}}" maxlength="9" tabindex="22" value="{{$kalibrasyon->hf32sonuc4}}"></td></tr>
                        <tr class="rows row5" data-id="5"><td></td><td>{{$kalibrasyonstandart->nokta5}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[0][4]" name="sonuc1[0][4]" data-id="{{$kalibrasyonstandart->sapma5}}" style="color:white;background-color:{{$kalibrasyon->sonuc5>$kalibrasyonstandart->sapma5 || $kalibrasyon->sonuc5<-($kalibrasyonstandart->sapma5) ? "red" : "green" }}" class="form-control valid0 lf sonuc1" maxlength="9" tabindex="5" value="{{$kalibrasyon->sonuc5}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[0][4]" name="sonuc2[0][4]" data-id="{{$kalibrasyonstandart->sapma5}}" style="color:{{$kalibrasyon->hf2sonuc5==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf2sonuc5==''  ? "white" :($kalibrasyon->hf2sonuc5>$kalibrasyonstandart->sapma5  || $kalibrasyon->hf2sonuc5<-($kalibrasyonstandart->sapma5)  ? "red" : "green") }}" class="form-control valid0 hf2  sonuc2 {{$kalibrasyon->hf2  ? "" : "hide"}}" maxlength="9" tabindex="11" value="{{$kalibrasyon->hf2sonuc5}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[0][4]" name="sonuc3[0][4]" data-id="{{$kalibrasyonstandart->sapma5}}" style="color:{{$kalibrasyon->hf3sonuc5==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf3sonuc5==''  ? "white" :($kalibrasyon->hf3sonuc5>$kalibrasyonstandart->sapma5  || $kalibrasyon->hf3sonuc5<-($kalibrasyonstandart->sapma5)  ? "red" : "green") }}" class="form-control valid0 hf3  sonuc3 {{$kalibrasyon->hf3  ? "" : "hide"}}" maxlength="9" tabindex="17" value="{{$kalibrasyon->hf3sonuc5}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[0][4]" name="sonuc4[0][4]" data-id="{{$kalibrasyonstandart->sapma5}}" style="color:{{$kalibrasyon->hf32sonuc5=='' ? "black" : "white"}};background-color:{{$kalibrasyon->hf32sonuc5=='' ? "white" :($kalibrasyon->hf32sonuc5>$kalibrasyonstandart->sapma5 || $kalibrasyon->hf32sonuc5<-($kalibrasyonstandart->sapma5) ? "red" : "green") }}" class="form-control valid0 hf32 sonuc4 {{$kalibrasyon->hf32 ? "" : "hide"}}" maxlength="9" tabindex="23" value="{{$kalibrasyon->hf32sonuc5}}"></td></tr>
                        <tr class="rows row6" data-id="6"><td></td><td>{{$kalibrasyonstandart->nokta6}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[0][5]" name="sonuc1[0][5]" data-id="{{$kalibrasyonstandart->sapma6}}" style="color:white;background-color:{{$kalibrasyon->sonuc6>$kalibrasyonstandart->sapma6 || $kalibrasyon->sonuc6<-($kalibrasyonstandart->sapma6) ? "red" : "green" }}" class="form-control valid0 lf sonuc1" maxlength="9" tabindex="6" value="{{$kalibrasyon->sonuc6}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[0][5]" name="sonuc2[0][5]" data-id="{{$kalibrasyonstandart->sapma6}}" style="color:{{$kalibrasyon->hf2sonuc6==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf2sonuc6==''  ? "white" :($kalibrasyon->hf2sonuc6>$kalibrasyonstandart->sapma6  || $kalibrasyon->hf2sonuc6<-($kalibrasyonstandart->sapma6)  ? "red" : "green") }}" class="form-control valid0 hf2  sonuc2 {{$kalibrasyon->hf2  ? "" : "hide"}}" maxlength="9" tabindex="12" value="{{$kalibrasyon->hf2sonuc6}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[0][5]" name="sonuc3[0][5]" data-id="{{$kalibrasyonstandart->sapma6}}" style="color:{{$kalibrasyon->hf3sonuc6==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf3sonuc6==''  ? "white" :($kalibrasyon->hf3sonuc6>$kalibrasyonstandart->sapma6  || $kalibrasyon->hf3sonuc6<-($kalibrasyonstandart->sapma6)  ? "red" : "green") }}" class="form-control valid0 hf3  sonuc3 {{$kalibrasyon->hf3  ? "" : "hide"}}" maxlength="9" tabindex="18" value="{{$kalibrasyon->hf3sonuc6}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[0][5]" name="sonuc4[0][5]" data-id="{{$kalibrasyonstandart->sapma6}}" style="color:{{$kalibrasyon->hf32sonuc6=='' ? "black" : "white"}};background-color:{{$kalibrasyon->hf32sonuc6=='' ? "white" :($kalibrasyon->hf32sonuc6>$kalibrasyonstandart->sapma6 || $kalibrasyon->hf32sonuc6<-($kalibrasyonstandart->sapma6) ? "red" : "green") }}" class="form-control valid0 hf32 sonuc4 {{$kalibrasyon->hf32 ? "" : "hide"}}" maxlength="9" tabindex="24" value="{{$kalibrasyon->hf32sonuc6}}"></td></tr>
                    </table>
                    @elseif($kalibrasyonstandart->noktasayisi==7)
                    <table class="col-sm-10 col-sm-offset-1 col-xs-11 sayaclar_ek">
                        <tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label{{$kalibrasyon->id}}" data-id="1">Seri No</b></td>
                            <td><b>Ölçüm</b></td><td class="lf"><b>LF Sonuç</b></td><td class="hf2 {{$kalibrasyon->hf2 ? "" : "hide"}}"><b>HF2 Sonuç</b></td><td class="hf3 {{$kalibrasyon->hf3 ? "" : "hide"}}"><b>HF3-I Sonuç</b></td><td class="hf32 {{$kalibrasyon->hf32 ? "" : "hide"}}"><b>HF3-II Sonuç</b></td></tr>
                        <tr class="rows row1" data-id="1"><td class="serino serino{{$kalibrasyon->id}}">{{$kalibrasyon->kalibrasyon_seri}}<input type="text" id="serino[0]" name="serino[0]" class="form-control hide" value="{{$kalibrasyon->kalibrasyon_seri}}"/><input type="text" id="kalibrasyonid[0]" name="kalibrasyonid[0]" class="form-control hide" value="{{$kalibrasyon->id}}"/></td><td>{{$kalibrasyonstandart->nokta1}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[0][0]" name="sonuc1[0][0]" data-id="{{$kalibrasyonstandart->sapma1}}" style="color:white;background-color:{{$kalibrasyon->sonuc1>$kalibrasyonstandart->sapma1 || $kalibrasyon->sonuc1<-($kalibrasyonstandart->sapma1) ? "red" : "green" }}" class="form-control valid0 lf sonuc1" maxlength="9" tabindex="1" value="{{$kalibrasyon->sonuc1}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[0][0]" name="sonuc2[0][0]" data-id="{{$kalibrasyonstandart->sapma1}}" style="color:{{$kalibrasyon->hf2sonuc1==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf2sonuc1==''  ? "white" :($kalibrasyon->hf2sonuc1>$kalibrasyonstandart->sapma1  || $kalibrasyon->hf2sonuc1<-($kalibrasyonstandart->sapma1)  ? "red" : "green") }}" class="form-control valid0 hf2  sonuc2 {{$kalibrasyon->hf2  ? "" : "hide"}}" maxlength="9" tabindex="8"  value="{{$kalibrasyon->hf2sonuc1}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[0][0]" name="sonuc3[0][0]" data-id="{{$kalibrasyonstandart->sapma1}}" style="color:{{$kalibrasyon->hf3sonuc1==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf3sonuc1==''  ? "white" :($kalibrasyon->hf3sonuc1>$kalibrasyonstandart->sapma1  || $kalibrasyon->hf3sonuc1<-($kalibrasyonstandart->sapma1)  ? "red" : "green") }}" class="form-control valid0 hf3  sonuc3 {{$kalibrasyon->hf3  ? "" : "hide"}}" maxlength="9" tabindex="15" value="{{$kalibrasyon->hf3sonuc1}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[0][0]" name="sonuc4[0][0]" data-id="{{$kalibrasyonstandart->sapma1}}" style="color:{{$kalibrasyon->hf32sonuc1=='' ? "black" : "white"}};background-color:{{$kalibrasyon->hf32sonuc1=='' ? "white" :($kalibrasyon->hf32sonuc1>$kalibrasyonstandart->sapma1 || $kalibrasyon->hf32sonuc1<-($kalibrasyonstandart->sapma1) ? "red" : "green") }}" class="form-control valid0 hf32 sonuc4 {{$kalibrasyon->hf32 ? "" : "hide"}}" maxlength="9" tabindex="22" value="{{$kalibrasyon->hf32sonuc1}}"></td></tr>
                        <tr class="rows row2" data-id="2"><td></td><td>{{$kalibrasyonstandart->nokta2}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[0][1]" name="sonuc1[0][1]" data-id="{{$kalibrasyonstandart->sapma2}}" style="color:white;background-color:{{$kalibrasyon->sonuc2>$kalibrasyonstandart->sapma2 || $kalibrasyon->sonuc2<-($kalibrasyonstandart->sapma2) ? "red" : "green" }}" class="form-control valid0 lf sonuc1" maxlength="9" tabindex="2" value="{{$kalibrasyon->sonuc2}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[0][1]" name="sonuc2[0][1]" data-id="{{$kalibrasyonstandart->sapma2}}" style="color:{{$kalibrasyon->hf2sonuc2==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf2sonuc2==''  ? "white" :($kalibrasyon->hf2sonuc2>$kalibrasyonstandart->sapma2  || $kalibrasyon->hf2sonuc2<-($kalibrasyonstandart->sapma2)  ? "red" : "green") }}" class="form-control valid0 hf2  sonuc2 {{$kalibrasyon->hf2  ? "" : "hide"}}" maxlength="9" tabindex="9"  value="{{$kalibrasyon->hf2sonuc2}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[0][1]" name="sonuc3[0][1]" data-id="{{$kalibrasyonstandart->sapma2}}" style="color:{{$kalibrasyon->hf3sonuc2==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf3sonuc2==''  ? "white" :($kalibrasyon->hf3sonuc2>$kalibrasyonstandart->sapma2  || $kalibrasyon->hf3sonuc2<-($kalibrasyonstandart->sapma2)  ? "red" : "green") }}" class="form-control valid0 hf3  sonuc3 {{$kalibrasyon->hf3  ? "" : "hide"}}" maxlength="9" tabindex="16" value="{{$kalibrasyon->hf3sonuc2}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[0][1]" name="sonuc4[0][1]" data-id="{{$kalibrasyonstandart->sapma2}}" style="color:{{$kalibrasyon->hf32sonuc2=='' ? "black" : "white"}};background-color:{{$kalibrasyon->hf32sonuc2=='' ? "white" :($kalibrasyon->hf32sonuc2>$kalibrasyonstandart->sapma2 || $kalibrasyon->hf32sonuc2<-($kalibrasyonstandart->sapma2) ? "red" : "green") }}" class="form-control valid0 hf32 sonuc4 {{$kalibrasyon->hf32 ? "" : "hide"}}" maxlength="9" tabindex="23" value="{{$kalibrasyon->hf32sonuc2}}"></td></tr>
                        <tr class="rows row3" data-id="3"><td></td><td>{{$kalibrasyonstandart->nokta3}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[0][2]" name="sonuc1[0][2]" data-id="{{$kalibrasyonstandart->sapma3}}" style="color:white;background-color:{{$kalibrasyon->sonuc3>$kalibrasyonstandart->sapma3 || $kalibrasyon->sonuc3<-($kalibrasyonstandart->sapma3) ? "red" : "green" }}" class="form-control valid0 lf sonuc1" maxlength="9" tabindex="3" value="{{$kalibrasyon->sonuc3}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[0][2]" name="sonuc2[0][2]" data-id="{{$kalibrasyonstandart->sapma3}}" style="color:{{$kalibrasyon->hf2sonuc3==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf2sonuc3==''  ? "white" :($kalibrasyon->hf2sonuc3>$kalibrasyonstandart->sapma3  || $kalibrasyon->hf2sonuc3<-($kalibrasyonstandart->sapma3)  ? "red" : "green") }}" class="form-control valid0 hf2  sonuc2 {{$kalibrasyon->hf2  ? "" : "hide"}}" maxlength="9" tabindex="10" value="{{$kalibrasyon->hf2sonuc3}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[0][2]" name="sonuc3[0][2]" data-id="{{$kalibrasyonstandart->sapma3}}" style="color:{{$kalibrasyon->hf3sonuc3==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf3sonuc3==''  ? "white" :($kalibrasyon->hf3sonuc3>$kalibrasyonstandart->sapma3  || $kalibrasyon->hf3sonuc3<-($kalibrasyonstandart->sapma3)  ? "red" : "green") }}" class="form-control valid0 hf3  sonuc3 {{$kalibrasyon->hf3  ? "" : "hide"}}" maxlength="9" tabindex="17" value="{{$kalibrasyon->hf3sonuc3}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[0][2]" name="sonuc4[0][2]" data-id="{{$kalibrasyonstandart->sapma3}}" style="color:{{$kalibrasyon->hf32sonuc3=='' ? "black" : "white"}};background-color:{{$kalibrasyon->hf32sonuc3=='' ? "white" :($kalibrasyon->hf32sonuc3>$kalibrasyonstandart->sapma3 || $kalibrasyon->hf32sonuc3<-($kalibrasyonstandart->sapma3) ? "red" : "green") }}" class="form-control valid0 hf32 sonuc4 {{$kalibrasyon->hf32 ? "" : "hide"}}" maxlength="9" tabindex="24" value="{{$kalibrasyon->hf32sonuc3}}"></td></tr>
                        <tr class="rows row4" data-id="4"><td></td><td>{{$kalibrasyonstandart->nokta4}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[0][3]" name="sonuc1[0][3]" data-id="{{$kalibrasyonstandart->sapma4}}" style="color:white;background-color:{{$kalibrasyon->sonuc4>$kalibrasyonstandart->sapma4 || $kalibrasyon->sonuc4<-($kalibrasyonstandart->sapma4) ? "red" : "green" }}" class="form-control valid0 lf sonuc1" maxlength="9" tabindex="4" value="{{$kalibrasyon->sonuc4}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[0][3]" name="sonuc2[0][3]" data-id="{{$kalibrasyonstandart->sapma4}}" style="color:{{$kalibrasyon->hf2sonuc4==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf2sonuc4==''  ? "white" :($kalibrasyon->hf2sonuc4>$kalibrasyonstandart->sapma4  || $kalibrasyon->hf2sonuc4<-($kalibrasyonstandart->sapma4)  ? "red" : "green") }}" class="form-control valid0 hf2  sonuc2 {{$kalibrasyon->hf2  ? "" : "hide"}}" maxlength="9" tabindex="11" value="{{$kalibrasyon->hf2sonuc4}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[0][3]" name="sonuc3[0][3]" data-id="{{$kalibrasyonstandart->sapma4}}" style="color:{{$kalibrasyon->hf3sonuc4==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf3sonuc4==''  ? "white" :($kalibrasyon->hf3sonuc4>$kalibrasyonstandart->sapma4  || $kalibrasyon->hf3sonuc4<-($kalibrasyonstandart->sapma4)  ? "red" : "green") }}" class="form-control valid0 hf3  sonuc3 {{$kalibrasyon->hf3  ? "" : "hide"}}" maxlength="9" tabindex="18" value="{{$kalibrasyon->hf3sonuc4}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[0][3]" name="sonuc4[0][3]" data-id="{{$kalibrasyonstandart->sapma4}}" style="color:{{$kalibrasyon->hf32sonuc4=='' ? "black" : "white"}};background-color:{{$kalibrasyon->hf32sonuc4=='' ? "white" :($kalibrasyon->hf32sonuc4>$kalibrasyonstandart->sapma4 || $kalibrasyon->hf32sonuc4<-($kalibrasyonstandart->sapma4) ? "red" : "green") }}" class="form-control valid0 hf32 sonuc4 {{$kalibrasyon->hf32 ? "" : "hide"}}" maxlength="9" tabindex="25" value="{{$kalibrasyon->hf32sonuc4}}"></td></tr>
                        <tr class="rows row5" data-id="5"><td></td><td>{{$kalibrasyonstandart->nokta5}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[0][4]" name="sonuc1[0][4]" data-id="{{$kalibrasyonstandart->sapma5}}" style="color:white;background-color:{{$kalibrasyon->sonuc5>$kalibrasyonstandart->sapma5 || $kalibrasyon->sonuc5<-($kalibrasyonstandart->sapma5) ? "red" : "green" }}" class="form-control valid0 lf sonuc1" maxlength="9" tabindex="5" value="{{$kalibrasyon->sonuc5}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[0][4]" name="sonuc2[0][4]" data-id="{{$kalibrasyonstandart->sapma5}}" style="color:{{$kalibrasyon->hf2sonuc5==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf2sonuc5==''  ? "white" :($kalibrasyon->hf2sonuc5>$kalibrasyonstandart->sapma5  || $kalibrasyon->hf2sonuc5<-($kalibrasyonstandart->sapma5)  ? "red" : "green") }}" class="form-control valid0 hf2  sonuc2 {{$kalibrasyon->hf2  ? "" : "hide"}}" maxlength="9" tabindex="12" value="{{$kalibrasyon->hf2sonuc5}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[0][4]" name="sonuc3[0][4]" data-id="{{$kalibrasyonstandart->sapma5}}" style="color:{{$kalibrasyon->hf3sonuc5==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf3sonuc5==''  ? "white" :($kalibrasyon->hf3sonuc5>$kalibrasyonstandart->sapma5  || $kalibrasyon->hf3sonuc5<-($kalibrasyonstandart->sapma5)  ? "red" : "green") }}" class="form-control valid0 hf3  sonuc3 {{$kalibrasyon->hf3  ? "" : "hide"}}" maxlength="9" tabindex="19" value="{{$kalibrasyon->hf3sonuc5}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[0][4]" name="sonuc4[0][4]" data-id="{{$kalibrasyonstandart->sapma5}}" style="color:{{$kalibrasyon->hf32sonuc5=='' ? "black" : "white"}};background-color:{{$kalibrasyon->hf32sonuc5=='' ? "white" :($kalibrasyon->hf32sonuc5>$kalibrasyonstandart->sapma5 || $kalibrasyon->hf32sonuc5<-($kalibrasyonstandart->sapma5) ? "red" : "green") }}" class="form-control valid0 hf32 sonuc4 {{$kalibrasyon->hf32 ? "" : "hide"}}" maxlength="9" tabindex="26" value="{{$kalibrasyon->hf32sonuc5}}"></td></tr>
                        <tr class="rows row6" data-id="6"><td></td><td>{{$kalibrasyonstandart->nokta6}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[0][5]" name="sonuc1[0][5]" data-id="{{$kalibrasyonstandart->sapma6}}" style="color:white;background-color:{{$kalibrasyon->sonuc6>$kalibrasyonstandart->sapma6 || $kalibrasyon->sonuc6<-($kalibrasyonstandart->sapma6) ? "red" : "green" }}" class="form-control valid0 lf sonuc1" maxlength="9" tabindex="6" value="{{$kalibrasyon->sonuc6}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[0][5]" name="sonuc2[0][5]" data-id="{{$kalibrasyonstandart->sapma6}}" style="color:{{$kalibrasyon->hf2sonuc6==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf2sonuc6==''  ? "white" :($kalibrasyon->hf2sonuc6>$kalibrasyonstandart->sapma6  || $kalibrasyon->hf2sonuc6<-($kalibrasyonstandart->sapma6)  ? "red" : "green") }}" class="form-control valid0 hf2  sonuc2 {{$kalibrasyon->hf2  ? "" : "hide"}}" maxlength="9" tabindex="13" value="{{$kalibrasyon->hf2sonuc6}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[0][5]" name="sonuc3[0][5]" data-id="{{$kalibrasyonstandart->sapma6}}" style="color:{{$kalibrasyon->hf3sonuc6==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf3sonuc6==''  ? "white" :($kalibrasyon->hf3sonuc6>$kalibrasyonstandart->sapma6  || $kalibrasyon->hf3sonuc6<-($kalibrasyonstandart->sapma6)  ? "red" : "green") }}" class="form-control valid0 hf3  sonuc3 {{$kalibrasyon->hf3  ? "" : "hide"}}" maxlength="9" tabindex="20" value="{{$kalibrasyon->hf3sonuc6}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[0][5]" name="sonuc4[0][5]" data-id="{{$kalibrasyonstandart->sapma6}}" style="color:{{$kalibrasyon->hf32sonuc6=='' ? "black" : "white"}};background-color:{{$kalibrasyon->hf32sonuc6=='' ? "white" :($kalibrasyon->hf32sonuc6>$kalibrasyonstandart->sapma6 || $kalibrasyon->hf32sonuc6<-($kalibrasyonstandart->sapma6) ? "red" : "green") }}" class="form-control valid0 hf32 sonuc4 {{$kalibrasyon->hf32 ? "" : "hide"}}" maxlength="9" tabindex="27" value="{{$kalibrasyon->hf32sonuc6}}"></td></tr>
                        <tr class="rows row7" data-id="7"><td></td><td>{{$kalibrasyonstandart->nokta7}}<span class="required" aria-required="true"> * </span></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[0][6]" name="sonuc1[0][6]" data-id="{{$kalibrasyonstandart->sapma7}}" style="color:white;background-color:{{$kalibrasyon->sonuc7>$kalibrasyonstandart->sapma7 || $kalibrasyon->sonuc7<-($kalibrasyonstandart->sapma7) ? "red" : "green" }}" class="form-control valid0 lf sonuc1" maxlength="9" tabindex="7" value="{{$kalibrasyon->sonuc7}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[0][6]" name="sonuc2[0][6]" data-id="{{$kalibrasyonstandart->sapma7}}" style="color:{{$kalibrasyon->hf2sonuc7==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf2sonuc7==''  ? "white" :($kalibrasyon->hf2sonuc7>$kalibrasyonstandart->sapma7  || $kalibrasyon->hf2sonuc7<-($kalibrasyonstandart->sapma7)  ? "red" : "green") }}" class="form-control valid0 hf2  sonuc2 {{$kalibrasyon->hf2  ? "" : "hide"}}" maxlength="9" tabindex="14" value="{{$kalibrasyon->hf2sonuc7}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[0][6]" name="sonuc3[0][6]" data-id="{{$kalibrasyonstandart->sapma7}}" style="color:{{$kalibrasyon->hf3sonuc7==''  ? "black" : "white"}};background-color:{{$kalibrasyon->hf3sonuc7==''  ? "white" :($kalibrasyon->hf3sonuc7>$kalibrasyonstandart->sapma7  || $kalibrasyon->hf3sonuc7<-($kalibrasyonstandart->sapma7)  ? "red" : "green") }}" class="form-control valid0 hf3  sonuc3 {{$kalibrasyon->hf3  ? "" : "hide"}}" maxlength="9" tabindex="21" value="{{$kalibrasyon->hf3sonuc7}}"></td>
                            <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[0][6]" name="sonuc4[0][6]" data-id="{{$kalibrasyonstandart->sapma7}}" style="color:{{$kalibrasyon->hf32sonuc7=='' ? "black" : "white"}};background-color:{{$kalibrasyon->hf32sonuc7=='' ? "white" :($kalibrasyon->hf32sonuc7>$kalibrasyonstandart->sapma7 || $kalibrasyon->hf32sonuc7<-($kalibrasyonstandart->sapma7) ? "red" : "green") }}" class="form-control valid0 hf32 sonuc4 {{$kalibrasyon->hf32 ? "" : "hide"}}" maxlength="9" tabindex="28" value="{{$kalibrasyon->hf32sonuc7}}"></td></tr>
                    </table>
                    @endif
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <button type="button" class="btn green" data-toggle="modal" data-target="#confirm">Kaydet</button>
                        <a href="{{ URL::to('kalibrasyon/kalibrasyondetay/'.$grup->id.'')}}" class="btn default">Vazgeç</a>
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
                    <h4 class="modal-title">Kalibrasyon Bilgisi Güncellenecek</h4>
                </div>
                <div class="modal-body">
                    Girilen Kalibrasyon Bilgileri Güncellenecektir?
                </div>
                <div class="modal-footer">
                    <a id="formsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
@stop

