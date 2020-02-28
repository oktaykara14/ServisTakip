@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Gaz Kalibrasyon Kayıt <small>Giriş Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::to('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" />
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
        var grupid = $("#grupid").val();
        var count= 0;
        var cnt = count+1;
        $('#istasyonadi').on('change', function (){
            var id = $(this).val();
            if (id !== "") {
                $.blockUI();
                $.getJSON("{{ URL::to('kalibrasyon/istasyonbilgi') }}",{grupid:grupid,istasyonid:id}, function (event) {
                    if (event.durum) //istasyon bilgilerini getirir
                    {
                        var istasyon = event.istasyon;
                        var sayacadlari = event.sayacadlari;
                        //var kalibrasyonlar = event.kalibrasyonlar;
                        var sayacsayi=istasyon.sayacsayi;
                        $('#sayacsayi').val(sayacsayi);
                        $("#noktasayi").val(0);
                        $("#sayacadi").empty();
                        $("#sayacadi").append('<option value=""> Seçiniz... </option>');
                        $("#sayacadi").select2('val','').trigger('change');
                        $("#hassasiyet").empty();
                        $("#hassasiyet").select2('val','');
                        $("#kalibrasyon").empty();
                        $("#kalibrasyon").select2('val','');
                        while($('.sayaclar .sayaclar_ek').size()>0){
                            $('.sayaclar .sayaclar_ek:last').remove();
                            cnt--;
                        }
                        cnt=0;
                        $('#secilenler').val("");
                        $("#count").val(cnt++);
                        $('.lfhf').addClass('hide');
                        $.each(sayacadlari, function (index) {
                            $("#sayacadi").append('<option value="' + sayacadlari[index].id + '"> ' + sayacadlari[index].sayacadi + '</option>');
                        });
                    } else {
                        toastr['warning']('Seçilen İstasyonun Sayaç Tipi Bilgileri Mevcut Değil', 'Kalibrasyon Bilgi Hatası');
                    }
                    $.unblockUI();
                });
            }
            $(this).valid();
        });
        $('#sayacadi').on('change', function () {
            var id = $(this).val();
            if (id !== "") {
                $.blockUI();
                $.getJSON("{{ URL::to('kalibrasyon/kalibrasyonstandart') }}",{grupid:grupid,sayacadiid:id}, function (event) {
                    if (event.durum) //kalibrasyon standarlarını getirir
                    {
                        //var sayacadi = event.sayacadi;
                        var standart = event.kalibrasyonstandart;
                        var kalibrasyon = event.kalibrasyon;
                        $("#hassasiyet").empty();
                        $("#hassasiyet").select2('val','');
                        $("#kalibrasyon").empty();
                        $("#kalibrasyon").select2('val','');
                        while($('.sayaclar .sayaclar_ek').size()>0){
                            $('.sayaclar .sayaclar_ek:last').remove();
                            cnt--;
                        }
                        cnt=0;
                        $('.lfhf').addClass('hide');
                        $('#secilenler').val("");
                        $("#count").val(cnt++);
                        $.each(standart, function (index) {
                            $("#hassasiyet").append('<option value="' + standart[index].id + '"> ' + standart[index].hassasiyet + '</option>');
                            if(index===0){
                                $("#hassasiyet").select2('val',standart[index].id).trigger('change');
                                $("#noktasayi").val(standart[index].noktasayisi);
                                $('.noktalar_ek').remove();
                                var sayi=1;
                                while(parseInt(standart[index].noktasayisi)>=sayi)
                                {
                                    var durum="";
                                    switch(sayi) {
                                        case 1: durum = standart[index].nokta1+'" value="'+standart[index].sapma1+'"/>'; break;
                                        case 2: durum = standart[index].nokta2+'" value="'+standart[index].sapma2+'"/>'; break;
                                        case 3: durum = standart[index].nokta3+'" value="'+standart[index].sapma3+'"/>'; break;
                                        case 4: durum = standart[index].nokta4+'" value="'+standart[index].sapma4+'"/>'; break;
                                        case 5: durum = standart[index].nokta5+'" value="'+standart[index].sapma5+'"/>'; break;
                                        case 6: durum = standart[index].nokta6+'" value="'+standart[index].sapma6+'"/>'; break;
                                        case 7: durum = standart[index].nokta7+'" value="'+standart[index].sapma7+'"/>'; break;
                                    }
                                    var nokta='<div class="form-group noktalar_ek"><input id="nokta'+sayi+'" name="nokta'+sayi+'" data-id="'+durum+'</div>';
                                    $('.noktalar').append(nokta);
                                    sayi++;
                                }
                                if(parseInt(standart[index].noktasayisi)>3)
                                {
                                    $('.lfhf').removeClass('hide');
                                }else{
                                    $('.lfhf').addClass('hide');
                                }
                            }
                        });
                        $.each(kalibrasyon, function (index) {
                            $("#kalibrasyon").append('<option data-yil="'+kalibrasyon[index].imalyili+'" data-id="'+kalibrasyon[index].kalibrasyon_seri+'" data-hassasiyet="'+kalibrasyon[index].hassasiyet+'" value="' + kalibrasyon[index].id + '"> ' + kalibrasyon[index].kalibrasyon_seri + '</option>');
                        });
                    } else {
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            }
            $(this).valid();
        });
        $('#hassasiyet').on('change', function () {
            var id = $(this).val();
            if (id !== "") {
                $.blockUI();
                $.getJSON("{{ URL::to('kalibrasyon/kalibrasyonstandartbilgi') }}",{id:id}, function (event) {
                    if(event.durum){
                        var standart = event.standart;
                        $("#noktasayi").val(standart.noktasayisi);
                        $('.noktalar_ek').remove();
                        var sayi=1;
                        while(parseInt(standart.noktasayisi)>=sayi)
                        {
                            var durum="";
                            switch(sayi) {
                                case 1: durum = standart.nokta1+'" value="'+standart.sapma1+'"/>'; break;
                                case 2: durum = standart.nokta2+'" value="'+standart.sapma2+'"/>'; break;
                                case 3: durum = standart.nokta3+'" value="'+standart.sapma3+'"/>'; break;
                                case 4: durum = standart.nokta4+'" value="'+standart.sapma4+'"/>'; break;
                                case 5: durum = standart.nokta5+'" value="'+standart.sapma5+'"/>'; break;
                                case 6: durum = standart.nokta6+'" value="'+standart.sapma6+'"/>'; break;
                                case 7: durum = standart.nokta7+'" value="'+standart.sapma7+'"/>'; break;
                            }
                            var nokta='<div class="form-group noktalar_ek"><input id="nokta'+sayi+'" name="nokta'+sayi+'" data-id="'+durum+'</div>';
                            $('.noktalar').append(nokta);
                            sayi++;
                        }
                        if(parseInt(standart.noktasayisi)>3)
                        {
                            $('.lfhf').removeClass('hide');
                        }else{
                            $('.lfhf').addClass('hide');
                        }
                        while($('.sayaclar .sayaclar_ek').size()>0){
                            $('.sayaclar .sayaclar_ek:last').remove();
                            cnt--;
                        }
                        cnt=0;
                        $('#secilenler').val("");
                        $("#count").val(cnt++);
                        $("#kalibrasyon").select2('val','');
                    }else{
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            }
            $(this).valid();
        });
        $("#kalibrasyon").on('change', function() {
            var tumu = $(this).val();
            var sayacsayi = $('#sayacsayi').val();
            var noktasayi = $('#noktasayi').val();
            var oncekiler = $('#secilenler').val();
            var oncekilist = oncekiler.split(',');
            var yeniler="";
            var yeni="";
            var flag=0;
            var nokta1="",nokta2="",nokta3="",nokta4="",nokta5="",nokta6="",nokta7="";
            var hf2,hf3,hf32;
            var sayi;
            var row1,row2,row3,row4,row5,row6,row7;
            if(tumu!=null) //ekleme varsa
            {
                if( tumu.length>=oncekilist.length) //ekleme varsa
                {
                    $.each(tumu,function(index){
                        $.each(oncekilist,function(index2){
                            if(oncekilist[index2]===tumu[index])
                            {
                                flag=1;
                                return false;
                            }
                        });
                        if(flag===0)
                        {
                            yeni=tumu[index];
                            return false;
                        }else{
                            flag=0;
                            return true;
                        }
                    });
                    if(sayacsayi<tumu.length){
                        $('#kalibrasyon').select2("val",oncekilist);
                        $('#secilenler').val(oncekiler);
                        toastr['warning']('İstasyonda kalibrasyon için belirlenen sayaç sayısı maksimuma ulaştı.', 'Maksimum Sayaç Sayısı');
                    }else{
                        var seri=$('#kalibrasyon option[value='+ yeni +']').html();
                        seri= $.trim(seri);
                        var newSayac="";
                        var fark1=0,fark2=0,fark3=0;
                        if(parseInt(noktasayi)>3) //nokta sayısına göre sonuçları çekecez
                        {
                            hf2=$('#hf2');
                            hf3=$('#hf3');
                            hf32=$('#hf32');

                            switch(noktasayi) {
                                case "4":  nokta1=$('#nokta1'); nokta2=$('#nokta2'); nokta3=$('#nokta3'); nokta4=$('#nokta4');
                                    newSayac='<table class="col-md-10 col-md-offset-1 sayaclar_ek">'+
                                    '<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label'+yeni+'" data-id="'+cnt+'">'+cnt+'.Seri No</b></td>'+
                                    '<td><b>Ölçüm</b></td><td class="lf"><b>LF Sonuç</b></td><td class="hf2 '+(hf2.attr('checked') ? '' : 'hide')+'"><b>HF2 Sonuç</b></td><td class="hf3 '+(hf3.attr('checked') ? '' : 'hide')+'"><b>HF3-I Sonuç</b></td><td class="hf32 '+(hf32.attr('checked') ? '' : 'hide')+'"><b>HF3-II Sonuç</b></td></tr>'+
                                    '<tr class="rows row1" data-id="1"><td class="serino serino'+(yeni)+'">'+seri+'<input type="text" id="serino['+(cnt-1)+']" name="serino['+(cnt-1)+']" class="form-control hide" value="'+seri+'"/><input type="text" id="kalibrasyonid['+(cnt-1)+']" name="kalibrasyonid['+(cnt-1)+']" class="form-control hide" value="'+yeni+'"/></td><td>'+nokta1.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][0]" name="sonuc1['+(cnt-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(cnt-1))+1)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][0]" name="sonuc2['+(cnt-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(cnt-1))+5)+'"  class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][0]" name="sonuc3['+(cnt-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(cnt-1))+9)+'"  class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][0]" name="sonuc4['+(cnt-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(cnt-1))+13)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '<tr class="rows row2" data-id="2"><td></td><td>'+nokta2.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][1]" name="sonuc1['+(cnt-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(cnt-1))+2)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][1]" name="sonuc2['+(cnt-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(cnt-1))+6)+'"  class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'" ></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][1]" name="sonuc3['+(cnt-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(cnt-1))+10)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'" ></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][1]" name="sonuc4['+(cnt-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(cnt-1))+14)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'" ></td>' +
                                        '</tr>'+
                                    '<tr class="rows row3" data-id="3"><td></td><td>'+nokta3.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][2]" name="sonuc1['+(cnt-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(cnt-1))+3)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][2]" name="sonuc2['+(cnt-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(cnt-1))+7)+'"  class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][2]" name="sonuc3['+(cnt-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(cnt-1))+11)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][2]" name="sonuc4['+(cnt-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(cnt-1))+15)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '<tr class="rows row4" data-id="4"><td></td><td>'+nokta4.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][3]" name="sonuc1['+(cnt-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(cnt-1))+4)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][3]" name="sonuc2['+(cnt-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(cnt-1))+8)+'"  class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][3]" name="sonuc3['+(cnt-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(cnt-1))+12)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][3]" name="sonuc4['+(cnt-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(cnt-1))+16)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '</table>';
                                    break;
                                case "5":  nokta1=$('#nokta1'); nokta2=$('#nokta2'); nokta3=$('#nokta3'); nokta4=$('#nokta4'); nokta5=$('#nokta5');
                                    newSayac='<table class="col-md-10 col-md-offset-1 sayaclar_ek">'+
                                    '<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label'+yeni+'" data-id="'+cnt+'">'+cnt+'.Seri No</b></td>'+
                                    '<td><b>Ölçüm</b></td><td class="lf"><b>LF Sonuç</b></td><td class="hf2 '+(hf2.attr('checked') ? '' : 'hide')+'"><b>HF2 Sonuç</b></td><td class="hf3 '+(hf3.attr('checked') ? '' : 'hide')+'"><b>HF3-I Sonuç</b></td><td class="hf32 '+(hf32.attr('checked') ? '' : 'hide')+'"><b>HF3-II Sonuç</b></td></tr>'+
                                    '<tr class="rows row1" data-id="1"><td class="serino serino'+(yeni)+'">'+seri+'<input type="text" id="serino['+(cnt-1)+']" name="serino['+(cnt-1)+']" class="form-control hide" value="'+seri+'"/><input type="text" id="kalibrasyonid['+(cnt-1)+']" name="kalibrasyonid['+(cnt-1)+']" class="form-control hide" value="'+yeni+'"/></td><td>'+nokta1.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][0]" name="sonuc1['+(cnt-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+1)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][0]" name="sonuc2['+(cnt-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+6)+'"  class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][0]" name="sonuc3['+(cnt-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+11)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][0]" name="sonuc4['+(cnt-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+16)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '<tr class="rows row2" data-id="2"><td></td><td>'+nokta2.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][1]" name="sonuc1['+(cnt-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+2)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][1]" name="sonuc2['+(cnt-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+7)+'"  class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][1]" name="sonuc3['+(cnt-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+12)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][1]" name="sonuc4['+(cnt-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+17)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '<tr class="rows row3" data-id="3"><td></td><td>'+nokta3.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][2]" name="sonuc1['+(cnt-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+3)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][2]" name="sonuc2['+(cnt-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+8)+'"  class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][2]" name="sonuc3['+(cnt-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+13)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][2]" name="sonuc4['+(cnt-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+18)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '<tr class="rows row4" data-id="4"><td></td><td>'+nokta4.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][3]" name="sonuc1['+(cnt-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+4)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][3]" name="sonuc2['+(cnt-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+9)+'"  class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][3]" name="sonuc3['+(cnt-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+14)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][3]" name="sonuc4['+(cnt-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+19)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '<tr class="rows row5" data-id="5"><td></td><td>'+nokta5.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][4]" name="sonuc1['+(cnt-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+5)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][4]" name="sonuc2['+(cnt-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+10)+'" class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][4]" name="sonuc3['+(cnt-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+15)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][4]" name="sonuc4['+(cnt-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+20)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '</table>';
                                    break;
                                case "6":  nokta1=$('#nokta1'); nokta2=$('#nokta2'); nokta3=$('#nokta3'); nokta4=$('#nokta4'); nokta5=$('#nokta5'); nokta6=$('#nokta6');
                                    newSayac='<table class="col-md-10 col-md-offset-1 sayaclar_ek">'+
                                    '<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label'+yeni+'" data-id="'+cnt+'">'+cnt+'.Seri No</b></td>'+
                                    '<td><b>Ölçüm</b></td><td class="lf"><b>LF Sonuç</b></td><td class="hf2 '+(hf2.attr('checked') ? '' : 'hide')+'"><b>HF2 Sonuç</b></td><td class="hf3 '+(hf3.attr('checked') ? '' : 'hide')+'"><b>HF3-I Sonuç</b></td><td class="hf32 '+(hf32.attr('checked') ? '' : 'hide')+'"><b>HF3-II Sonuç</b></td></tr>'+
                                    '<tr class="rows row1" data-id="1"><td class="serino serino'+(yeni)+'">'+seri+'<input type="text" id="serino['+(cnt-1)+']" name="serino['+(cnt-1)+']" class="form-control hide" value="'+seri+'"/><input type="text" id="kalibrasyonid['+(cnt-1)+']" name="kalibrasyonid['+(cnt-1)+']" class="form-control hide" value="'+yeni+'"/></td><td>'+nokta1.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][0]" name="sonuc1['+(cnt-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+1)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][0]" name="sonuc2['+(cnt-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+7)+'"  class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][0]" name="sonuc3['+(cnt-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+13)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][0]" name="sonuc4['+(cnt-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+19)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '<tr class="rows row2" data-id="2"><td></td><td>'+nokta2.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][1]" name="sonuc1['+(cnt-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+2)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][1]" name="sonuc2['+(cnt-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+8)+'"  class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][1]" name="sonuc3['+(cnt-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+14)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][1]" name="sonuc4['+(cnt-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(cnt-1))+20)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '<tr class="rows row3" data-id="3"><td></td><td>'+nokta3.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][2]" name="sonuc1['+(cnt-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+3)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][2]" name="sonuc2['+(cnt-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+9)+'"  class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][2]" name="sonuc3['+(cnt-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+15)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][2]" name="sonuc4['+(cnt-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+21)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '<tr class="rows row4" data-id="4"><td></td><td>'+nokta4.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][3]" name="sonuc1['+(cnt-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+4)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][3]" name="sonuc2['+(cnt-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+10)+'" class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][3]" name="sonuc3['+(cnt-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+16)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][3]" name="sonuc4['+(cnt-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+22)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '<tr class="rows row5" data-id="5"><td></td><td>'+nokta5.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][4]" name="sonuc1['+(cnt-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+5)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][4]" name="sonuc2['+(cnt-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+11)+'" class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][4]" name="sonuc3['+(cnt-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+17)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][4]" name="sonuc4['+(cnt-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+23)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '<tr class="rows row6" data-id="5"><td></td><td>'+nokta6.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][5]" name="sonuc1['+(cnt-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+6)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][5]" name="sonuc2['+(cnt-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+12)+'" class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][5]" name="sonuc3['+(cnt-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+18)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][5]" name="sonuc4['+(cnt-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(cnt-1))+24)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '</table>';
                                    break;
                                case "7":  nokta1=$('#nokta1'); nokta2=$('#nokta2'); nokta3=$('#nokta3'); nokta4=$('#nokta4'); nokta5=$('#nokta5'); nokta6=$('#nokta6'); nokta7=$('#nokta7');
                                    newSayac='<table class="col-md-10 col-md-offset-1 sayaclar_ek">'+
                                    '<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label'+yeni+'" data-id="'+cnt+'">'+cnt+'.Seri No</b></td>'+
                                    '<td><b>Ölçüm</b></td><td class="lf"><b>LF Sonuç</b></td><td class="hf2 '+(hf2.attr('checked') ? '' : 'hide')+'"><b>HF2 Sonuç</b></td><td class="hf3 '+(hf3.attr('checked') ? '' : 'hide')+'"><b>HF3-I Sonuç</b></td><td class="hf32 '+(hf32.attr('checked') ? '' : 'hide')+'"><b>HF3-II Sonuç</b></td></tr>'+
                                    '<tr class="rows row1" data-id="1"><td class="serino serino'+(yeni)+'">'+seri+'<input type="text" id="serino['+(cnt-1)+']" name="serino['+(cnt-1)+']" class="form-control hide" value="'+seri+'"/><input type="text" id="kalibrasyonid['+(cnt-1)+']" name="kalibrasyonid['+(cnt-1)+']" class="form-control hide" value="'+yeni+'"/></td><td>'+nokta1.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][0]" name="sonuc1['+(cnt-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+1)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][0]" name="sonuc2['+(cnt-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+8)+'"  class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][0]" name="sonuc3['+(cnt-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+15)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][0]" name="sonuc4['+(cnt-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+22)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '<tr class="rows row2" data-id="2"><td></td><td>'+nokta2.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][1]" name="sonuc1['+(cnt-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+2)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][1]" name="sonuc2['+(cnt-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+9)+'"  class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][1]" name="sonuc3['+(cnt-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+16)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][1]" name="sonuc4['+(cnt-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+23)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '<tr class="rows row3" data-id="3"><td></td><td>'+nokta3.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][2]" name="sonuc1['+(cnt-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+3)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][2]" name="sonuc2['+(cnt-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+10)+'" class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][2]" name="sonuc3['+(cnt-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+17)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][2]" name="sonuc4['+(cnt-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+24)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '<tr class="rows row4" data-id="4"><td></td><td>'+nokta4.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][3]" name="sonuc1['+(cnt-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+4)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][3]" name="sonuc2['+(cnt-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+11)+'" class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][3]" name="sonuc3['+(cnt-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+18)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][3]" name="sonuc4['+(cnt-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+25)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '<tr class="rows row5" data-id="5"><td></td><td>'+nokta5.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][4]" name="sonuc1['+(cnt-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+5)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][4]" name="sonuc2['+(cnt-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+12)+'" class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][4]" name="sonuc3['+(cnt-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+19)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][4]" name="sonuc4['+(cnt-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+26)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '<tr class="rows row6" data-id="5"><td></td><td>'+nokta6.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][5]" name="sonuc1['+(cnt-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+6)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][5]" name="sonuc2['+(cnt-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+13)+'" class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][5]" name="sonuc3['+(cnt-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+20)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][5]" name="sonuc4['+(cnt-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+27)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '<tr class="rows row7" data-id="5"><td></td><td>'+nokta7.data('id')+'v</td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(cnt-1)+'][6]" name="sonuc1['+(cnt-1)+'][6]" data-id="'+nokta7.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+7)+'"  class="form-control valid'+(cnt-1)+' lf   sonuc1 "></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(cnt-1)+'][6]" name="sonuc2['+(cnt-1)+'][6]" data-id="'+nokta7.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+14)+'" class="form-control valid'+(cnt-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(cnt-1)+'][6]" name="sonuc3['+(cnt-1)+'][6]" data-id="'+nokta7.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+21)+'" class="form-control valid'+(cnt-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(cnt-1)+'][6]" name="sonuc4['+(cnt-1)+'][6]" data-id="'+nokta7.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(cnt-1))+28)+'" class="form-control valid'+(cnt-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                        '</tr>'+
                                    '</table>';
                                    break;
                            }
                            $('.sayaclar').append(newSayac);
                            $('input.valid'+(cnt-1)).each(function(){
                                $(this).rules('remove');
                                $(this).rules('add', {
                                    required: true
                                });
                            });
                            $(".sonuc1").inputmask("decimal",{
                                radixPoint:",",
                                groupSeparator: "",
                                digits: 3,
                                autoGroup: true
                            });
                            $(".sonuc2").inputmask("decimal",{
                                radixPoint:",",
                                groupSeparator: "",
                                digits: 3,
                                autoGroup: true
                            });
                            $(".sonuc3").inputmask("decimal",{
                                radixPoint:",",
                                groupSeparator: "",
                                digits: 3,
                                autoGroup: true
                            });
                            $(".sonuc4").inputmask("decimal",{
                                radixPoint:",",
                                groupSeparator: "",
                                digits: 3,
                                autoGroup: true
                            });
                            $('.sonuc1').on('change',function(){
                                var sonuc=$(this);
                                if(sonuc.val()!=="")
                                {
                                    var result = parseFloat((sonuc.val()).replace(',','.'));
                                    var sapma = parseFloat(sonuc.data('id'));
                                    if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                        sonuc.css("background-color", "red");
                                    }else{
                                        sonuc.css("background-color", "green");
                                    }
                                }else{
                                    sonuc.css("background-color", "red");
                                }
                                sonuc.css("color", "white");
                            });
                            $('.sonuc2').on('change',function(){
                                var sonuc=$(this);
                                if(sonuc.val()!=="")
                                {
                                    var result = parseFloat((sonuc.val()).replace(',','.'));
                                    var sapma = parseFloat(sonuc.data('id'));
                                    if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                        sonuc.css("background-color", "red");
                                    }else{
                                        sonuc.css("background-color", "green");
                                    }
                                }else{
                                    sonuc.css("background-color", "red");
                                }
                                sonuc.css("color", "white");
                            });
                            $('.sonuc3').on('change',function(){
                                var sonuc=$(this);
                                if(sonuc.val()!=="")
                                {
                                    var result = parseFloat((sonuc.val()).replace(',','.'));
                                    var sapma = parseFloat(sonuc.data('id'));
                                    if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                        sonuc.css("background-color", "red");
                                    }else{
                                        sonuc.css("background-color", "green");
                                    }
                                }else{
                                    sonuc.css("background-color", "red");
                                }
                                sonuc.css("color", "white");
                            });
                            $('.sonuc4').on('change',function(){
                                var sonuc=$(this);
                                if(sonuc.val()!=="")
                                {
                                    var result = parseFloat((sonuc.val()).replace(',','.'));
                                    var sapma = parseFloat(sonuc.data('id'));
                                    if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                        sonuc.css("background-color", "red");
                                    }else{
                                        sonuc.css("background-color", "green");
                                    }
                                }else{
                                    sonuc.css("background-color", "red");
                                }
                                sonuc.css("color", "white");
                            });
                        }else{ //diyafram sayaçlar için
                            nokta1=$('#nokta1');
                            nokta2=$('#nokta2');
                            nokta3=$('#nokta3');
                            switch(cnt) {
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
                            newSayac='<table class="col-md-10 col-md-offset-1 sayaclar_ek">'+
                                '<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label'+yeni+'" data-id="'+cnt+'">'+cnt+'.Seri No</b></td><td><b>Ölçüm</b></td><td><b>Sonuç</b></td></tr>'+
                                '<tr class="rows row1" data-id="1"><td class="serino serino'+(yeni)+'">'+seri+'<input type="text" id="serino['+(cnt-1)+']" name="serino['+(cnt-1)+']" class="form-control hide" value="'+seri+'"/><input type="text" id="kalibrasyonid['+(cnt-1)+']" name="kalibrasyonid['+(cnt-1)+']" class="form-control hide" value="'+yeni+'"/></td><td>'+nokta1.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                    '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc['+(cnt-1)+'][0]" name="sonuc['+(cnt-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" data-fark="'+fark1+'" class="form-control valid'+(cnt-1)+' sonuc sonuc1" tabindex="'+((3*(cnt-1))+1)+'"></td>' +
                                '</tr>'+
                                '<tr class="rows row2" data-id="2" style="padding-top:10px;"><td></td><td>'+nokta2.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                    '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc['+(cnt-1)+'][1]" name="sonuc['+(cnt-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" data-fark="'+fark2+'" class="form-control valid'+(cnt-1)+' sonuc sonuc2" tabindex="'+((3*(cnt-1))+2)+'"></td>' +
                                '</tr>'+
                                '<tr class="rows row3" data-id="3" style="padding-top:10px;"><td></td><td>'+nokta3.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                    '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc['+(cnt-1)+'][2]" name="sonuc['+(cnt-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" data-fark="'+fark3+'" class="form-control valid'+(cnt-1)+' sonuc sonuc3" tabindex="'+((3*(cnt-1))+3)+'"></td>' +
                                '</tr>'+
                                    '</table>';
                            $('.sayaclar').append(newSayac);
                            $('input.valid'+(cnt-1)).each(function(){
                                $(this).rules('remove');
                                $(this).rules('add', {
                                    required: true
                                });
                            });
                            $(".sonuc").inputmask("decimal",{
                                radixPoint:",",
                                groupSeparator: "",
                                digits: 3,
                                autoGroup: true
                            });
                            $('.sonuc').on('change',function(){
                                var sonuc=$(this);
                                if(sonuc.val()!=="")
                                {
                                    var result = parseFloat((sonuc.val()).replace(',','.'));
                                    var sapma = parseFloat(sonuc.data('id'));
                                    if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                        sonuc.css("background-color", "red");
                                    }else{
                                        sonuc.css("background-color", "green");
                                    }
                                }else{
                                    sonuc.css("background-color", "red");
                                }
                                sonuc.css("color", "white");
                            });
                        }
                        $("#count").val(cnt++);
                        $('#secilenler').val(oncekiler+(oncekiler==="" ? "" : ",")+yeni);
                    }
                }else{ //silinme varsa
                    if(tumu==='')
                    {
                        while($('.sayaclar .sayaclar_ek').size()>0){
                            $('.sayaclar .sayaclar_ek:last').remove();
                        }
                        count=0;
                        cnt=count+1;
                        $('#secilenler').val("");
                    }else{
                        $.each(oncekilist,function(index){
                            $.each(tumu,function(index2){
                                if(oncekilist[index]===tumu[index2])
                                {
                                    flag=1;
                                    return false;
                                }
                            });
                            if(flag===0)
                            {
                                yeni=oncekilist[index];
                                return false;
                            }else{
                                flag=0;
                                return true;
                            }
                        });
                        $('.serino'+yeni).closest('.sayaclar_ek').remove();
                        sayi=1;
                        //noinspection CommaExpressionJS
                        row1="",row2="",row3="",row4="",row5="",row6="",row7="";
                        if(parseInt(noktasayi)>3) //nokta sayısına göre sonuçları çekecez
                        {
                            hf2=$('#hf2');
                            hf3=$('#hf3');
                            hf32=$('#hf32');
                            $.each(oncekilist, function (index) {
                                if (yeni !== oncekilist[index]) {
                                    var label = $('.label' + oncekilist[index]);
                                    yeniler += (yeniler === "" ? "" : ",") + oncekilist[index];
                                    var parent = label.closest('.sayaclar_ek').children('tbody');
                                    var seri = parent.children('tr').children('td.serino'+oncekilist[index]).text();
                                    switch(noktasayi) {
                                        case "4":  nokta1=$('#nokta1'); nokta2=$('#nokta2'); nokta3=$('#nokta3'); nokta4=$('#nokta4');
                                            row1 = parent.children('tr.row1'); row2 = parent.children('tr.row2'); row3 = parent.children('tr.row3'); row4 = parent.children('tr.row4');
                                            newSayac='<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label'+ oncekilist[index] +'" data-id="'+sayi+'">'+sayi+'.Seri No</b></td>'+
                                            '<td><b>Ölçüm</b></td><td class="lf"><b>LF Sonuç</b></td><td class="hf2 '+(hf2.attr('checked') ? '' : 'hide')+'"><b>HF2 Sonuç</b></td><td class="hf3 '+(hf3.attr('checked') ? '' : 'hide')+'"><b>HF3-I Sonuç</b></td><td class="hf32 '+(hf32.attr('checked') ? '' : 'hide')+'"><b>HF3-II Sonuç</b></td></tr>'+
                                            '<tr class="rows row1" data-id="1"><td class="serino serino'+(oncekilist[index])+'">'+seri+'<input type="text" id="serino['+(sayi-1)+']" name="serino['+(sayi-1)+']" class="form-control hide" value="'+seri+'"/><input type="text" id="kalibrasyonid['+(sayi-1)+']" name="kalibrasyonid['+(sayi-1)+']" class="form-control hide" value="'+yeni+'"/></td><td>'+nokta1.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][0]" name="sonuc1['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+1)+'"  value="'+row1.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][0]" name="sonuc2['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+5)+'"  value="'+row1.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][0]" name="sonuc3['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+9)+'"  value="'+row1.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][0]" name="sonuc4['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+13)+'" value="'+row1.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>'+
                                            '<tr class="rows row2" data-id="2"><td></td><td>'+nokta2.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][1]" name="sonuc1['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+2)+'"  value="'+row2.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][1]" name="sonuc2['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+6)+'"  value="'+row2.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][1]" name="sonuc3['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+10)+'" value="'+row2.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][1]" name="sonuc4['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+14)+'" value="'+row2.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>'+
                                            '<tr class="rows row3" data-id="3"><td></td><td>'+nokta3.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][2]" name="sonuc1['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+3)+'"  value="'+row3.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][2]" name="sonuc2['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+7)+'"  value="'+row3.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][2]" name="sonuc3['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+11)+'" value="'+row3.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][2]" name="sonuc4['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+15)+'" value="'+row3.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>'+
                                            '<tr class="rows row4" data-id="4"><td></td><td>'+nokta4.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][3]" name="sonuc1['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+4)+'"  value="'+row4.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][3]" name="sonuc2['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+8)+'"  value="'+row4.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"</td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][3]" name="sonuc3['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+12)+'" value="'+row4.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][3]" name="sonuc4['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+16)+'" value="'+row4.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>';
                                            break;
                                        case "5":  nokta1=$('#nokta1'); nokta2=$('#nokta2'); nokta3=$('#nokta3'); nokta4=$('#nokta4'); nokta5=$('#nokta5');
                                            row1 = parent.children('tr.row1'); row2 = parent.children('tr.row2'); row3 = parent.children('tr.row3'); row4 = parent.children('tr.row4'); row5 = parent.children('tr.row5');
                                            newSayac='<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label'+ oncekilist[index] +'" data-id="'+sayi+'">'+sayi+'.Seri No</b></td>'+
                                            '<td><b>Ölçüm</b></td><td class="lf"><b>LF Sonuç</b></td><td class="hf2 '+(hf2.attr('checked') ? '' : 'hide')+'"><b>HF2 Sonuç</b></td><td class="hf3 '+(hf3.attr('checked') ? '' : 'hide')+'"><b>HF3-I Sonuç</b></td><td class="hf32 '+(hf32.attr('checked') ? '' : 'hide')+'"><b>HF3-II Sonuç</b></td></tr>'+
                                            '<tr class="rows row1" data-id="1"><td class="serino serino'+(oncekilist[index])+'">'+seri+'<input type="text" id="serino['+(sayi-1)+']" name="serino['+(sayi-1)+']" class="form-control hide" value="'+seri+'"/><input type="text" id="kalibrasyonid['+(sayi-1)+']" name="kalibrasyonid['+(sayi-1)+']" class="form-control hide" value="'+yeni+'"/></td><td>'+nokta1.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][0]" name="sonuc1['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+1)+'"  value="'+row1.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][0]" name="sonuc2['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+6)+'"  value="'+row1.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][0]" name="sonuc3['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+11)+'" value="'+row1.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][0]" name="sonuc4['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+16)+'" value="'+row1.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>'+
                                            '<tr class="rows row2" data-id="2"><td></td><td>'+nokta2.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][1]" name="sonuc1['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+2)+'"  value="'+row2.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][1]" name="sonuc2['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+7)+'"  value="'+row2.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked') ?  '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][1]" name="sonuc3['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+12)+'" value="'+row2.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked') ?  '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][1]" name="sonuc4['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+17)+'" value="'+row2.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>'+
                                            '<tr class="rows row3" data-id="3"><td></td><td>'+nokta3.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][2]" name="sonuc1['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+3)+'"  value="'+row3.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][2]" name="sonuc2['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+8)+'"  value="'+row3.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked') ?  '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][2]" name="sonuc3['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+13)+'" value="'+row3.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked') ?  '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][2]" name="sonuc4['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+18)+'" value="'+row3.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>'+
                                            '<tr class="rows row4" data-id="4"><td></td><td>'+nokta4.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][3]" name="sonuc1['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+4)+'"  value="'+row4.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][3]" name="sonuc2['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+9)+'"  value="'+row4.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked') ?  '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][3]" name="sonuc3['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+14)+'" value="'+row4.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked') ?  '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][3]" name="sonuc4['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+19)+'" value="'+row4.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>'+
                                            '<tr class="rows row5" data-id="5"><td></td><td>'+nokta5.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][4]" name="sonuc1['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+5)+'"  value="'+row5.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][4]" name="sonuc2['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+10)+'" value="'+row5.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked') ?  '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][4]" name="sonuc3['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+15)+'" value="'+row5.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked') ?  '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][4]" name="sonuc4['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+20)+'" value="'+row5.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>';
                                            break;
                                        case "6":  nokta1=$('#nokta1'); nokta2=$('#nokta2'); nokta3=$('#nokta3'); nokta4=$('#nokta4'); nokta5=$('#nokta5'); nokta6=$('#nokta6');
                                            row1 = parent.children('tr.row1'); row2 = parent.children('tr.row2'); row3 = parent.children('tr.row3'); row4 = parent.children('tr.row4'); row5 = parent.children('tr.row5'); row6 = parent.children('tr.row6');
                                            newSayac='<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label'+ oncekilist[index] +'" data-id="'+sayi+'">'+sayi+'.Seri No</b></td>'+
                                            '<td><b>Ölçüm</b></td><td class="lf"><b>LF Sonuç</b></td><td class="hf2 '+(hf2.attr('checked') ? '' : 'hide')+'"><b>HF2 Sonuç</b></td><td class="hf3 '+(hf3.attr('checked') ? '' : 'hide')+'"><b>HF3-I Sonuç</b></td><td class="hf32 '+(hf32.attr('checked') ? '' : 'hide')+'"><b>HF3-II Sonuç</b></td></tr>'+
                                            '<tr class="rows row1" data-id="1"><td class="serino serino'+(oncekilist[index])+'">'+seri+'<input type="text" id="serino['+(sayi-1)+']" name="serino['+(sayi-1)+']" class="form-control hide" value="'+seri+'"/><input type="text" id="kalibrasyonid['+(sayi-1)+']" name="kalibrasyonid['+(sayi-1)+']" class="form-control hide" value="'+yeni+'"/></td><td>'+nokta1.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][0]" name="sonuc1['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+1)+'"  value="'+row1.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][0]" name="sonuc2['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+7)+'"  value="'+row1.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][0]" name="sonuc3['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+13)+'" value="'+row1.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][0]" name="sonuc4['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+19)+'" value="'+row1.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>'+
                                            '<tr class="rows row2" data-id="2"><td></td><td>'+nokta2.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][1]" name="sonuc1['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+2)+'"  value="'+row2.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][1]" name="sonuc2['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+8)+'"  value="'+row2.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][1]" name="sonuc3['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+14)+'" value="'+row2.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][1]" name="sonuc4['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+20)+'" value="'+row1.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>'+
                                            '<tr class="rows row3" data-id="3"><td></td><td>'+nokta3.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][2]" name="sonuc1['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+3)+'"  value="'+row3.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][2]" name="sonuc2['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+9)+'"  value="'+row3.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][2]" name="sonuc3['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+15)+'" value="'+row3.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][2]" name="sonuc4['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+21)+'" value="'+row1.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>'+
                                            '<tr class="rows row4" data-id="4"><td></td><td>'+nokta4.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][3]" name="sonuc1['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+4)+'"  value="'+row4.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][3]" name="sonuc2['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+10)+'" value="'+row4.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][3]" name="sonuc3['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+16)+'" value="'+row4.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][3]" name="sonuc4['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+22)+'" value="'+row1.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>'+
                                            '<tr class="rows row5" data-id="5"><td></td><td>'+nokta5.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][4]" name="sonuc1['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+5)+'"  value="'+row5.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][4]" name="sonuc2['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+11)+'" value="'+row5.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][4]" name="sonuc3['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+17)+'" value="'+row5.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][4]" name="sonuc4['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+23)+'" value="'+row1.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>'+
                                            '<tr class="rows row6" data-id="6"><td></td><td>'+nokta6.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][5]" name="sonuc1['+(sayi-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+6)+'"  value="'+row6.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][5]" name="sonuc2['+(sayi-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+12)+'" value="'+row6.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][5]" name="sonuc3['+(sayi-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+18)+'" value="'+row6.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][5]" name="sonuc4['+(sayi-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+24)+'" value="'+row1.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>';
                                            break;
                                        case "7":  nokta1=$('#nokta1'); nokta2=$('#nokta2'); nokta3=$('#nokta3'); nokta4=$('#nokta4'); nokta5=$('#nokta5'); nokta6=$('#nokta6'); nokta7=$('#nokta7');
                                            row1 = parent.children('tr.row1'); row2 = parent.children('tr.row2'); row3 = parent.children('tr.row3'); row4 = parent.children('tr.row4'); row5 = parent.children('tr.row5'); row6 = parent.children('tr.row6'); row7 = parent.children('tr.row7');
                                            newSayac='<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label'+ oncekilist[index] +'" data-id="'+sayi+'">'+sayi+'.Seri No</b></td>'+
                                            '<td><b>Ölçüm</b></td><td class="lf"><b>LF Sonuç</b></td><td class="hf2 '+(hf2.attr('checked') ? '' : 'hide')+'"><b>HF2 Sonuç</b></td><td class="hf3 '+(hf3.attr('checked') ? '' : 'hide')+'"><b>HF3-I Sonuç</b></td><td class="hf32 '+(hf32.attr('checked') ? '' : 'hide')+'"><b>HF3-II Sonuç</b></td></tr>'+
                                            '<tr class="rows row1" data-id="1"><td class="serino serino'+(oncekilist[index])+'">'+seri+'<input type="text" id="serino['+(sayi-1)+']" name="serino['+(sayi-1)+']" class="form-control hide" value="'+seri+'"/><input type="text" id="kalibrasyonid['+(sayi-1)+']" name="kalibrasyonid['+(sayi-1)+']" class="form-control hide" value="'+yeni+'"/></td><td>'+nokta1.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][0]" name="sonuc1['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+1)+'"  value="'+row1.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][0]" name="sonuc2['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+8)+'"  value="'+row1.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][0]" name="sonuc3['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+15)+'" value="'+row1.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][0]" name="sonuc4['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+22)+'" value="'+row1.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>'+
                                            '<tr class="rows row2" data-id="2"><td></td><td>'+nokta2.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][1]" name="sonuc1['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+2)+'"  value="'+row2.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][1]" name="sonuc2['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+9)+'"  value="'+row2.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][1]" name="sonuc3['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+16)+'" value="'+row2.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][1]" name="sonuc4['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+23)+'" value="'+row2.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>'+
                                            '<tr class="rows row3" data-id="3"><td></td><td>'+nokta3.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][2]" name="sonuc1['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+3)+'"  value="'+row3.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][2]" name="sonuc2['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+10)+'" value="'+row3.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][2]" name="sonuc3['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+17)+'" value="'+row3.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][2]" name="sonuc4['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+24)+'" value="'+row3.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>'+
                                            '<tr class="rows row4" data-id="4"><td></td><td>'+nokta4.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][3]" name="sonuc1['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+4)+'"  value="'+row4.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][3]" name="sonuc2['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+11)+'" value="'+row4.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][3]" name="sonuc3['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+18)+'" value="'+row4.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][3]" name="sonuc4['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+25)+'" value="'+row4.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>'+
                                            '<tr class="rows row5" data-id="5"><td></td><td>'+nokta5.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][4]" name="sonuc1['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+5)+'"  value="'+row5.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][4]" name="sonuc2['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+12)+'" value="'+row5.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][4]" name="sonuc3['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+19)+'" value="'+row5.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][4]" name="sonuc4['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+26)+'" value="'+row5.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>'+
                                            '<tr class="rows row6" data-id="6"><td></td><td>'+nokta6.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][5]" name="sonuc1['+(sayi-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+6)+'"  value="'+row6.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][5]" name="sonuc2['+(sayi-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+13)+'" value="'+row6.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][5]" name="sonuc3['+(sayi-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+20)+'" value="'+row6.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][5]" name="sonuc4['+(sayi-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+27)+'" value="'+row6.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>'+
                                            '<tr class="rows row7" data-id="7"><td></td><td>'+nokta7.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][6]" name="sonuc1['+(sayi-1)+'][6]" data-id="'+nokta7.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+7)+'"  value="'+row7.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][6]" name="sonuc2['+(sayi-1)+'][6]" data-id="'+nokta7.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+14)+'" value="'+row7.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][6]" name="sonuc3['+(sayi-1)+'][6]" data-id="'+nokta7.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+21)+'" value="'+row7.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                                '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][6]" name="sonuc4['+(sayi-1)+'][6]" data-id="'+nokta7.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+28)+'" value="'+row7.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                                '</tr>';
                                            break;
                                    }
                                    parent.html(newSayac);
                                    $('input.valid'+(sayi-1)).each(function(){
                                        $(this).rules('remove');
                                        $(this).rules('add', {
                                            required: true
                                        });
                                    });
                                    var degerler=parent.children('tr.rows');
                                    $.each(degerler, function () {
                                        var sonuc1=$(this).children('td').children('.sonuc1');
                                        var sonuc2=$(this).children('td').children('.sonuc2');
                                        var sonuc3=$(this).children('td').children('.sonuc3');
                                        var sonuc4=$(this).children('td').children('.sonuc4');
                                        var result,sapma;
                                        if(sonuc1.val()!=="")
                                        {
                                            result = parseFloat((sonuc1.val()).replace(',','.'));
                                            sapma = parseFloat(sonuc1.data('id'));
                                            if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                                sonuc1.css("background-color", "red");
                                            }else{
                                                sonuc1.css("background-color", "green");
                                            }
                                        }else{
                                            sonuc1.css("background-color", "red");
                                        }
                                        sonuc1.css("color", "white");
                                        if(sonuc2.val()!=="")
                                        {
                                            result = parseFloat((sonuc2.val()).replace(',','.'));
                                            sapma = parseFloat(sonuc2.data('id'));
                                            if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                                sonuc2.css("background-color", "red");
                                            }else{
                                                sonuc2.css("background-color", "green");
                                            }
                                        }else{
                                            sonuc2.css("background-color", "red");
                                        }
                                        sonuc2.css("color", "white");
                                        if(sonuc3.val()!=="")
                                        {
                                            result = parseFloat((sonuc3.val()).replace(',','.'));
                                            sapma = parseFloat(sonuc3.data('id'));
                                            if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                                sonuc3.css("background-color", "red");
                                            }else{
                                                sonuc3.css("background-color", "green");
                                            }
                                        }else{
                                            sonuc3.css("background-color", "red");
                                        }
                                        sonuc3.css("color", "white");
                                        if(sonuc4.val()!=="")
                                        {
                                            result = parseFloat((sonuc4.val()).replace(',','.'));
                                            sapma = parseFloat(sonuc4.data('id'));
                                            if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                                sonuc4.css("background-color", "red");
                                            }else{
                                                sonuc4.css("background-color", "green");
                                            }
                                        }else{
                                            sonuc4.css("background-color", "red");
                                        }
                                        sonuc4.css("color", "white");
                                    });
                                    sayi++;
                                }
                            });
                            $(".sonuc1").inputmask("decimal",{
                                radixPoint:",",
                                groupSeparator: "",
                                digits: 3,
                                autoGroup: true
                            });
                            $(".sonuc2").inputmask("decimal",{
                                radixPoint:",",
                                groupSeparator: "",
                                digits: 3,
                                autoGroup: true
                            });
                            $(".sonuc3").inputmask("decimal",{
                                radixPoint:",",
                                groupSeparator: "",
                                digits: 3,
                                autoGroup: true
                            });
                            $(".sonuc4").inputmask("decimal",{
                                radixPoint:",",
                                groupSeparator: "",
                                digits: 3,
                                autoGroup: true
                            });
                            $('.sonuc1').on('change',function(){
                                var sonuc=$(this);
                                if(sonuc.val()!=="")
                                {
                                    var result = parseFloat((sonuc.val()).replace(',','.'));
                                    var sapma = parseFloat(sonuc.data('id'));
                                    if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                        sonuc.css("background-color", "red");
                                    }else{
                                        sonuc.css("background-color", "green");
                                    }
                                }else{
                                    sonuc.css("background-color", "red");
                                }
                                sonuc.css("color", "white");
                            });
                            $('.sonuc2').on('change',function(){
                                var sonuc=$(this);
                                if(sonuc.val()!=="")
                                {
                                    var result = parseFloat((sonuc.val()).replace(',','.'));
                                    var sapma = parseFloat(sonuc.data('id'));
                                    if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                        sonuc.css("background-color", "red");
                                    }else{
                                        sonuc.css("background-color", "green");
                                    }
                                }else{
                                    sonuc.css("background-color", "red");
                                }
                                sonuc.css("color", "white");
                            });
                            $('.sonuc3').on('change',function(){
                                var sonuc=$(this);
                                if(sonuc.val()!=="")
                                {
                                    var result = parseFloat((sonuc.val()).replace(',','.'));
                                    var sapma = parseFloat(sonuc.data('id'));
                                    if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                        sonuc.css("background-color", "red");
                                    }else{
                                        sonuc.css("background-color", "green");
                                    }
                                }else{
                                    sonuc.css("background-color", "red");
                                }
                                sonuc.css("color", "white");
                            });
                            $('.sonuc4').on('change',function(){
                                var sonuc=$(this);
                                if(sonuc.val()!=="")
                                {
                                    var result = parseFloat((sonuc.val()).replace(',','.'));
                                    var sapma = parseFloat(sonuc.data('id'));
                                    if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                        sonuc.css("background-color", "red");
                                    }else{
                                        sonuc.css("background-color", "green");
                                    }
                                }else{
                                    sonuc.css("background-color", "red");
                                }
                                sonuc.css("color", "white");
                            });
                            $('#secilenler').val(yeniler);
                        }else {
                            nokta1 = $('#nokta1');
                            nokta2 = $('#nokta2');
                            nokta3 = $('#nokta3');
                            $.each(oncekilist, function (index) {
                                if (yeni !== oncekilist[index]) {
                                    var fark1 = 0, fark2 = 0, fark3 = 0;
                                    switch (sayi) {
                                        case 1:  fark1 = 0;fark2 = 0;fark3 = 0;break;
                                        case 2:  fark1 = 0.1;fark2 = 0.05;fark3 = 0.03;break;
                                        case 3:  fark1 = 0.2;fark2 = 0.10;fark3 = 0.06;break;
                                        case 4:  fark1 = 0.3;fark2 = 0.15;fark3 = 0.09;break;
                                        case 5:  fark1 = 0.5;fark2 = 0.20;fark3 = 0.12;break;
                                        case 6:  fark1 = 0.7;fark2 = 0.25;fark3 = 0.15;break;
                                        case 7:  fark1 = 0.9;fark2 = 0.30;fark3 = 0.18;break;
                                        case 8:  fark1 = 1.0;fark2 = 0.35;fark3 = 0.21;break;
                                        case 9:  fark1 = 1.2;fark2 = 0.40;fark3 = 0.24;break;
                                        case 10: fark1 = 1.3;fark2 = 0.50;fark3 = 0.27;break;
                                        case 11: fark1 = 1.4;fark2 = 0.55;fark3 = 0.30;break;
                                        case 12: fark1 = 1.5;fark2 = 0.60;fark3 = 0.33;break;
                                    }
                                    var label = $('.label' + oncekilist[index]);
                                    yeniler += (yeniler === "" ? "" : ",") + oncekilist[index];
                                    var parent = label.closest('.sayaclar_ek').children('tbody');
                                    var seri = parent.children('tr').children('td.serino'+oncekilist[index]).text();
                                    var row1 = parent.children('tr.row1');
                                    var row2 = parent.children('tr.row2');
                                    var row3 = parent.children('tr.row3');
                                    newSayac = '<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label' + oncekilist[index] + '" data-id="' + sayi + '">' + sayi + '.Seri No</b></td><td><b>Ölçüm</b></td><td><b>Sonuç</b></td></tr>' +
                                    '<tr class="rows row1" data-id="1"><td class="serino serino' + (oncekilist[index]) + '">' + seri + '<input type="text" id="serino['+(sayi-1)+']" name="serino['+(sayi-1)+']" class="form-control hide" value="'+seri+'"/><input type="text" id="kalibrasyonid['+(sayi-1)+']" name="kalibrasyonid['+(sayi-1)+']" class="form-control hide" value="'+(oncekilist[index])+'"/></td><td>' + nokta1.data('id') + '<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc[' + (sayi - 1) + '][0]" name="sonuc[' + (sayi - 1) + '][0]" data-id="' + nokta1.val() + '" style="color:black;background-color:white" data-fark="' + fark1 + '" class="form-control valid'+(sayi-1)+' sonuc sonuc1" tabindex="'+((3*(sayi-1))+1)+'" value="'+row1.children('td').children('.sonuc').val()+'"></td>' +
                                        '</tr>' +
                                    '<tr class="rows row2" data-id="2" style="padding-top:10px;"><td></td><td>' + nokta2.data('id') + '<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc[' + (sayi - 1) + '][1]" name="sonuc[' + (sayi - 1) + '][1]" data-id="' + nokta2.val() + '" style="color:black;background-color:white" data-fark="' + fark2 + '" class="form-control valid'+(sayi-1)+' sonuc sonuc2" tabindex="'+((3*(sayi-1))+2)+'" value="'+row2.children('td').children('.sonuc').val()+'"></td>' +
                                        '</tr>' +
                                    '<tr class="rows row3" data-id="3" style="padding-top:10px;"><td></td><td>' + nokta3.data('id') + '<span class="required" aria-required="true"> * </span></td>' +
                                        '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc[' + (sayi - 1) + '][2]" name="sonuc[' + (sayi - 1) + '][2]" data-id="' + nokta3.val() + '" style="color:black;background-color:white" data-fark="' + fark3 + '" class="form-control valid'+(sayi-1)+' sonuc sonuc3" tabindex="'+((3*(sayi-1))+3)+'" value="'+row3.children('td').children('.sonuc').val()+'"></td>' +
                                        '</tr>';
                                    parent.html(newSayac);
                                    $('input.valid'+(sayi-1)).each(function(){
                                        $(this).rules('remove');
                                        $(this).rules('add', {
                                            required: true
                                        });
                                    });
                                    var degerler=parent.children('tr.rows');
                                    $.each(degerler, function () {
                                        var sonuc = $(this).children('td').children('.sonuc');
                                        var result,sapma;
                                        if(sonuc.val()!=="")
                                        {
                                            result = parseFloat((sonuc.val()).replace(',','.'));
                                            sapma = parseFloat(sonuc.data('id'));
                                            if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                                sonuc.css("background-color", "red");
                                            }else{
                                                sonuc.css("background-color", "green");
                                            }
                                        }else{
                                            sonuc.css("background-color", "red");
                                        }
                                        sonuc.css("color", "white");
                                    });
                                    sayi++;
                                }
                            });
                            $(".sonuc").inputmask("decimal",{
                                radixPoint:",",
                                groupSeparator: "",
                                digits: 3,
                                autoGroup: true
                            });
                            $('.sonuc').on('change',function(){
                                var sonuc=$(this);
                                if(sonuc.val()!=="")
                                {
                                    var result = parseFloat((sonuc.val()).replace(',','.'));
                                    var sapma = parseFloat(sonuc.data('id'));
                                    if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                        sonuc.css("background-color", "red");
                                    }else{
                                        sonuc.css("background-color", "green");
                                    }
                                }else{
                                        sonuc.css("background-color", "red");
                                }
                                sonuc.css("color", "white");
                            });
                            $('#secilenler').val(yeniler);
                        }
                    }
                    cnt--;
                    $("#count").val(cnt-1);
                }
            }else{ //silinme varsa
                if(tumu==null)
                {
                    while($('.sayaclar .sayaclar_ek').size()>0){
                        $('.sayaclar .sayaclar_ek:last').remove();
                        cnt--;
                    }
                    cnt++;
                    $('#secilenler').val("");
                }else{
                    $.each(oncekilist,function(index){
                        $.each(tumu,function(index2){
                            if(oncekilist[index]===tumu[index2])
                            {
                                flag=1;
                                return false;
                            }
                        });
                        if(flag===0)
                        {
                            yeni=oncekilist[index];
                            return false;
                        }else{
                            flag=0;
                            return true;
                        }
                    });
                    $('.serino'+yeni).closest('.sayaclar_ek').remove();
                    sayi=1;
                    if(parseInt(noktasayi)>3) //nokta sayısına göre sonuçları çekecez
                    {
                        hf2=$('#hf2');
                        hf3=$('#hf3');
                        hf32=$('#hf32');
                        //noinspection CommaExpressionJS
                        row1="",row2="",row3="",row4="",row5="",row6="",row7="";
                        $.each(oncekilist, function (index) {
                            if (yeni !== oncekilist[index]) {
                                var label = $('.label' + oncekilist[index]);
                                yeniler += (yeniler === "" ? "" : ",") + oncekilist[index];
                                var parent = label.closest('.sayaclar_ek').children('tbody');
                                var seri = parent.children('tr').children('td.serino'+oncekilist[index]).text();
                                switch(noktasayi) {
                                    case "4":  nokta1=$('#nokta1'); nokta2=$('#nokta2'); nokta3=$('#nokta3'); nokta4=$('#nokta4');
                                        row1 = parent.children('tr.row1'); row2 = parent.children('tr.row2'); row3 = parent.children('tr.row3'); row4 = parent.children('tr.row4');
                                        newSayac='<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label'+ oncekilist[index] +'" data-id="'+sayi+'">'+sayi+'.Seri No</b></td>'+
                                            '<td><b>Ölçüm</b></td><td class="lf"><b>LF Sonuç</b></td><td class="hf2 '+(hf2.attr('checked') ? '' : 'hide')+'"><b>HF2 Sonuç</b></td><td class="hf3 '+(hf3.attr('checked') ? '' : 'hide')+'"><b>HF3-I Sonuç</b></td><td class="hf32 '+(hf32.attr('checked') ? '' : 'hide')+'"><b>HF3-II Sonuç</b></td></tr>'+
                                            '<tr class="rows row1" data-id="1"><td class="serino serino'+(oncekilist[index])+'">'+seri+'<input type="text" id="serino['+(sayi-1)+']" name="serino['+(sayi-1)+']" class="form-control hide" value="'+seri+'"/><input type="text" id="kalibrasyonid['+(sayi-1)+']" name="kalibrasyonid['+(sayi-1)+']" class="form-control hide" value="'+yeni+'"/></td><td>'+nokta1.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][0]" name="sonuc1['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+1)+'"  value="'+row1.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][0]" name="sonuc2['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+5)+'"  value="'+row1.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][0]" name="sonuc3['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+9)+'"  value="'+row1.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][0]" name="sonuc4['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+13)+'" value="'+row1.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>'+
                                            '<tr class="rows row2" data-id="2"><td></td><td>'+nokta2.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][1]" name="sonuc1['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+2)+'"  value="'+row2.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][1]" name="sonuc2['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+6)+'"  value="'+row2.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][1]" name="sonuc3['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+10)+'" value="'+row2.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][1]" name="sonuc4['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+14)+'" value="'+row2.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>'+
                                            '<tr class="rows row3" data-id="3"><td></td><td>'+nokta3.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][2]" name="sonuc1['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+3)+'"  value="'+row3.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][2]" name="sonuc2['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+7)+'"  value="'+row3.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][2]" name="sonuc3['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+11)+'" value="'+row3.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][2]" name="sonuc4['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+15)+'" value="'+row3.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>'+
                                            '<tr class="rows row4" data-id="4"><td></td><td>'+nokta4.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][3]" name="sonuc1['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+4)+'"  value="'+row4.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][3]" name="sonuc2['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+8)+'"  value="'+row4.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"</td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][3]" name="sonuc3['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+12)+'" value="'+row4.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][3]" name="sonuc4['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((4*4*(sayi-1))+16)+'" value="'+row4.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>';
                                        break;
                                    case "5":  nokta1=$('#nokta1'); nokta2=$('#nokta2'); nokta3=$('#nokta3'); nokta4=$('#nokta4'); nokta5=$('#nokta5');
                                        row1 = parent.children('tr.row1'); row2 = parent.children('tr.row2'); row3 = parent.children('tr.row3'); row4 = parent.children('tr.row4'); row5 = parent.children('tr.row5');
                                        newSayac='<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label'+ oncekilist[index] +'" data-id="'+sayi+'">'+sayi+'.Seri No</b></td>'+
                                            '<td><b>Ölçüm</b></td><td class="lf"><b>LF Sonuç</b></td><td class="hf2 '+(hf2.attr('checked') ? '' : 'hide')+'"><b>HF2 Sonuç</b></td><td class="hf3 '+(hf3.attr('checked') ? '' : 'hide')+'"><b>HF3-I Sonuç</b></td><td class="hf32 '+(hf32.attr('checked') ? '' : 'hide')+'"><b>HF3-II Sonuç</b></td></tr>'+
                                            '<tr class="rows row1" data-id="1"><td class="serino serino'+(oncekilist[index])+'">'+seri+'<input type="text" id="serino['+(sayi-1)+']" name="serino['+(sayi-1)+']" class="form-control hide" value="'+seri+'"/><input type="text" id="kalibrasyonid['+(sayi-1)+']" name="kalibrasyonid['+(sayi-1)+']" class="form-control hide" value="'+yeni+'"/></td><td>'+nokta1.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][0]" name="sonuc1['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+1)+'"  value="'+row1.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][0]" name="sonuc2['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+6)+'"  value="'+row1.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][0]" name="sonuc3['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+11)+'" value="'+row1.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][0]" name="sonuc4['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+16)+'" value="'+row1.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>'+
                                            '<tr class="rows row2" data-id="2"><td></td><td>'+nokta2.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][1]" name="sonuc1['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+2)+'"  value="'+row2.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][1]" name="sonuc2['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+7)+'"  value="'+row2.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked') ?  '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][1]" name="sonuc3['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+12)+'" value="'+row2.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked') ?  '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][1]" name="sonuc4['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+17)+'" value="'+row2.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>'+
                                            '<tr class="rows row3" data-id="3"><td></td><td>'+nokta3.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][2]" name="sonuc1['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+3)+'"  value="'+row3.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][2]" name="sonuc2['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+8)+'"  value="'+row3.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked') ?  '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][2]" name="sonuc3['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+13)+'" value="'+row3.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked') ?  '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][2]" name="sonuc4['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+18)+'" value="'+row3.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>'+
                                            '<tr class="rows row4" data-id="4"><td></td><td>'+nokta4.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][3]" name="sonuc1['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+4)+'"  value="'+row4.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][3]" name="sonuc2['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+9)+'"  value="'+row4.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked') ?  '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][3]" name="sonuc3['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+14)+'" value="'+row4.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked') ?  '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][3]" name="sonuc4['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+19)+'" value="'+row4.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>'+
                                            '<tr class="rows row5" data-id="5"><td></td><td>'+nokta5.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][4]" name="sonuc1['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+5)+'"  value="'+row5.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][4]" name="sonuc2['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+10)+'" value="'+row5.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked') ?  '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][4]" name="sonuc3['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+15)+'" value="'+row5.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked') ?  '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][4]" name="sonuc4['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((5*4*(sayi-1))+20)+'" value="'+row5.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>';
                                        break;
                                    case "6":  nokta1=$('#nokta1'); nokta2=$('#nokta2'); nokta3=$('#nokta3'); nokta4=$('#nokta4'); nokta5=$('#nokta5'); nokta6=$('#nokta6');
                                        row1 = parent.children('tr.row1'); row2 = parent.children('tr.row2'); row3 = parent.children('tr.row3'); row4 = parent.children('tr.row4'); row5 = parent.children('tr.row5'); row6 = parent.children('tr.row6');
                                        newSayac='<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label'+ oncekilist[index] +'" data-id="'+sayi+'">'+sayi+'.Seri No</b></td>'+
                                            '<td><b>Ölçüm</b></td><td class="lf"><b>LF Sonuç</b></td><td class="hf2 '+(hf2.attr('checked') ? '' : 'hide')+'"><b>HF2 Sonuç</b></td><td class="hf3 '+(hf3.attr('checked') ? '' : 'hide')+'"><b>HF3-I Sonuç</b></td><td class="hf32 '+(hf32.attr('checked') ? '' : 'hide')+'"><b>HF3-II Sonuç</b></td></tr>'+
                                            '<tr class="rows row1" data-id="1"><td class="serino serino'+(oncekilist[index])+'">'+seri+'<input type="text" id="serino['+(sayi-1)+']" name="serino['+(sayi-1)+']" class="form-control hide" value="'+seri+'"/><input type="text" id="kalibrasyonid['+(sayi-1)+']" name="kalibrasyonid['+(sayi-1)+']" class="form-control hide" value="'+yeni+'"/></td><td>'+nokta1.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][0]" name="sonuc1['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+1)+'"  value="'+row1.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][0]" name="sonuc2['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+7)+'"  value="'+row1.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][0]" name="sonuc3['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+13)+'" value="'+row1.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][0]" name="sonuc4['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+19)+'" value="'+row1.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>'+
                                            '<tr class="rows row2" data-id="2"><td></td><td>'+nokta2.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][1]" name="sonuc1['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+2)+'"  value="'+row2.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][1]" name="sonuc2['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+8)+'"  value="'+row2.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][1]" name="sonuc3['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+14)+'" value="'+row2.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][1]" name="sonuc4['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+20)+'" value="'+row1.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>'+
                                            '<tr class="rows row3" data-id="3"><td></td><td>'+nokta3.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][2]" name="sonuc1['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+3)+'"  value="'+row3.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][2]" name="sonuc2['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+9)+'"  value="'+row3.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][2]" name="sonuc3['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+15)+'" value="'+row3.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][2]" name="sonuc4['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+21)+'" value="'+row1.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>'+
                                            '<tr class="rows row4" data-id="4"><td></td><td>'+nokta4.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][3]" name="sonuc1['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+4)+'"  value="'+row4.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][3]" name="sonuc2['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+10)+'" value="'+row4.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][3]" name="sonuc3['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+16)+'" value="'+row4.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][3]" name="sonuc4['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+22)+'" value="'+row1.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>'+
                                            '<tr class="rows row5" data-id="5"><td></td><td>'+nokta5.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][4]" name="sonuc1['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+5)+'"  value="'+row5.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][4]" name="sonuc2['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+11)+'" value="'+row5.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][4]" name="sonuc3['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+17)+'" value="'+row5.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][4]" name="sonuc4['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+23)+'" value="'+row1.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>'+
                                            '<tr class="rows row6" data-id="6"><td></td><td>'+nokta6.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][5]" name="sonuc1['+(sayi-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+6)+'"  value="'+row6.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][5]" name="sonuc2['+(sayi-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+12)+'" value="'+row6.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][5]" name="sonuc3['+(sayi-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+18)+'" value="'+row6.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][5]" name="sonuc4['+(sayi-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((6*4*(sayi-1))+24)+'" value="'+row1.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>';
                                        break;
                                    case "7":  nokta1=$('#nokta1'); nokta2=$('#nokta2'); nokta3=$('#nokta3'); nokta4=$('#nokta4'); nokta5=$('#nokta5'); nokta6=$('#nokta6'); nokta7=$('#nokta7');
                                        row1 = parent.children('tr.row1'); row2 = parent.children('tr.row2'); row3 = parent.children('tr.row3'); row4 = parent.children('tr.row4'); row5 = parent.children('tr.row5'); row6 = parent.children('tr.row6'); row7 = parent.children('tr.row7');
                                        newSayac='<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label'+ oncekilist[index] +'" data-id="'+sayi+'">'+sayi+'.Seri No</b></td>'+
                                            '<td><b>Ölçüm</b></td><td class="lf"><b>LF Sonuç</b></td><td class="hf2 '+(hf2.attr('checked') ? '' : 'hide')+'"><b>HF2 Sonuç</b></td><td class="hf3 '+(hf3.attr('checked') ? '' : 'hide')+'"><b>HF3-I Sonuç</b></td><td class="hf32 '+(hf32.attr('checked') ? '' : 'hide')+'"><b>HF3-II Sonuç</b></td></tr>'+
                                            '<tr class="rows row1" data-id="1"><td class="serino serino'+(oncekilist[index])+'">'+seri+'<input type="text" id="serino['+(sayi-1)+']" name="serino['+(sayi-1)+']" class="form-control hide" value="'+seri+'"/><input type="text" id="kalibrasyonid['+(sayi-1)+']" name="kalibrasyonid['+(sayi-1)+']" class="form-control hide" value="'+yeni+'"/></td><td>'+nokta1.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][0]" name="sonuc1['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+1)+'"  value="'+row1.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][0]" name="sonuc2['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+8)+'"  value="'+row1.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][0]" name="sonuc3['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+15)+'" value="'+row1.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][0]" name="sonuc4['+(sayi-1)+'][0]" data-id="'+nokta1.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+22)+'" value="'+row1.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>'+
                                            '<tr class="rows row2" data-id="2"><td></td><td>'+nokta2.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][1]" name="sonuc1['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+2)+'"  value="'+row2.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][1]" name="sonuc2['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+9)+'"  value="'+row2.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][1]" name="sonuc3['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+16)+'" value="'+row2.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][1]" name="sonuc4['+(sayi-1)+'][1]" data-id="'+nokta2.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+23)+'" value="'+row2.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>'+
                                            '<tr class="rows row3" data-id="3"><td></td><td>'+nokta3.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][2]" name="sonuc1['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+3)+'"  value="'+row3.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][2]" name="sonuc2['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+10)+'" value="'+row3.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][2]" name="sonuc3['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+17)+'" value="'+row3.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][2]" name="sonuc4['+(sayi-1)+'][2]" data-id="'+nokta3.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+24)+'" value="'+row3.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>'+
                                            '<tr class="rows row4" data-id="4"><td></td><td>'+nokta4.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][3]" name="sonuc1['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+4)+'"  value="'+row4.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][3]" name="sonuc2['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+11)+'" value="'+row4.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][3]" name="sonuc3['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+18)+'" value="'+row4.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][3]" name="sonuc4['+(sayi-1)+'][3]" data-id="'+nokta4.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+25)+'" value="'+row4.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>'+
                                            '<tr class="rows row5" data-id="5"><td></td><td>'+nokta5.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][4]" name="sonuc1['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+5)+'"  value="'+row5.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][4]" name="sonuc2['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+12)+'" value="'+row5.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][4]" name="sonuc3['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+19)+'" value="'+row5.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][4]" name="sonuc4['+(sayi-1)+'][4]" data-id="'+nokta5.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+26)+'" value="'+row5.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>'+
                                            '<tr class="rows row6" data-id="6"><td></td><td>'+nokta6.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][5]" name="sonuc1['+(sayi-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+6)+'"  value="'+row6.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][5]" name="sonuc2['+(sayi-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+13)+'" value="'+row6.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][5]" name="sonuc3['+(sayi-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+20)+'" value="'+row6.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][5]" name="sonuc4['+(sayi-1)+'][5]" data-id="'+nokta6.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+27)+'" value="'+row6.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>'+
                                            '<tr class="rows row7" data-id="7"><td></td><td>'+nokta7.data('id')+'<span class="required" aria-required="true"> * </span></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1['+(sayi-1)+'][6]" name="sonuc1['+(sayi-1)+'][6]" data-id="'+nokta7.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+7)+'"  value="'+row7.children('td').children('.sonuc1').val()+'" class="form-control valid'+(sayi-1)+' lf   sonuc1 "></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2['+(sayi-1)+'][6]" name="sonuc2['+(sayi-1)+'][6]" data-id="'+nokta7.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+14)+'" value="'+row7.children('td').children('.sonuc2').val()+'" class="form-control valid'+(sayi-1)+' hf2  sonuc2 '+(hf2.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3['+(sayi-1)+'][6]" name="sonuc3['+(sayi-1)+'][6]" data-id="'+nokta7.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+21)+'" value="'+row7.children('td').children('.sonuc3').val()+'" class="form-control valid'+(sayi-1)+' hf3  sonuc3 '+(hf3.attr('checked')  ? '' : 'hide')+'"></td>' +
                                            '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4['+(sayi-1)+'][6]" name="sonuc4['+(sayi-1)+'][6]" data-id="'+nokta7.val()+'" style="color:black;background-color:white" maxlength="9" tabindex="'+((7*4*(sayi-1))+28)+'" value="'+row7.children('td').children('.sonuc4').val()+'" class="form-control valid'+(sayi-1)+' hf32 sonuc4 '+(hf32.attr('checked') ? '' : 'hide')+'"></td>' +
                                            '</tr>';
                                        break;
                                }
                                parent.html(newSayac);
                                $('input.valid'+(sayi-1)).each(function(){
                                    $(this).rules('remove');
                                    $(this).rules('add', {
                                        required: true
                                    });
                                });
                                var degerler=parent.children('tr.rows');
                                $.each(degerler, function () {
                                    var sonuc1=$(this).children('td').children('.sonuc1');
                                    var sonuc2=$(this).children('td').children('.sonuc2');
                                    var sonuc3=$(this).children('td').children('.sonuc3');
                                    var sonuc4=$(this).children('td').children('.sonuc4');
                                    var result,sapma;
                                    if(sonuc1.val()!=="")
                                    {
                                        result = parseFloat((sonuc1.val()).replace(',','.'));
                                        sapma = parseFloat(sonuc1.data('id'));
                                        if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                            sonuc1.css("background-color", "red");
                                        }else{
                                            sonuc1.css("background-color", "green");
                                        }
                                    }else{
                                        sonuc1.css("background-color", "red");
                                    }
                                    sonuc1.css("color", "white");
                                    if(sonuc2.val()!=="")
                                    {
                                        result = parseFloat((sonuc2.val()).replace(',','.'));
                                        sapma = parseFloat(sonuc2.data('id'));
                                        if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                            sonuc2.css("background-color", "red");
                                        }else{
                                            sonuc2.css("background-color", "green");
                                        }
                                    }else{
                                        sonuc2.css("background-color", "red");
                                    }
                                    sonuc2.css("color", "white");
                                    if(sonuc3.val()!=="")
                                    {
                                        result = parseFloat((sonuc3.val()).replace(',','.'));
                                        sapma = parseFloat(sonuc3.data('id'));
                                        if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                            sonuc3.css("background-color", "red");
                                        }else{
                                            sonuc3.css("background-color", "green");
                                        }
                                    }else{
                                        sonuc3.css("background-color", "red");
                                    }
                                    sonuc3.css("color", "white");
                                    if(sonuc4.val()!=="")
                                    {
                                        result = parseFloat((sonuc4.val()).replace(',','.'));
                                        sapma = parseFloat(sonuc4.data('id'));
                                        if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                            sonuc4.css("background-color", "red");
                                        }else{
                                            sonuc4.css("background-color", "green");
                                        }
                                    }else{
                                        sonuc4.css("background-color", "red");
                                    }
                                    sonuc4.css("color", "white");
                                });
                                sayi++;
                            }
                        });
                        $(".sonuc1").inputmask("decimal",{
                            radixPoint:",",
                            groupSeparator: "",
                            digits: 3,
                            autoGroup: true
                        });
                        $(".sonuc2").inputmask("decimal",{
                            radixPoint:",",
                            groupSeparator: "",
                            digits: 3,
                            autoGroup: true
                        });
                        $(".sonuc3").inputmask("decimal",{
                            radixPoint:",",
                            groupSeparator: "",
                            digits: 3,
                            autoGroup: true
                        });
                        $(".sonuc4").inputmask("decimal",{
                            radixPoint:",",
                            groupSeparator: "",
                            digits: 3,
                            autoGroup: true
                        });
                        $('.sonuc1').on('change',function(){
                            var sonuc=$(this);
                            if(sonuc.val()!=="")
                            {
                                var result = parseFloat((sonuc.val()).replace(',','.'));
                                var sapma = parseFloat(sonuc.data('id'));
                                if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                    sonuc.css("background-color", "red");
                                }else{
                                    sonuc.css("background-color", "green");
                                }
                            }else{
                                sonuc.css("background-color", "red");
                            }
                            sonuc.css("color", "white");
                        });
                        $('.sonuc2').on('change',function(){
                            var sonuc=$(this);
                            if(sonuc.val()!=="")
                            {
                                var result = parseFloat((sonuc.val()).replace(',','.'));
                                var sapma = parseFloat(sonuc.data('id'));
                                if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                    sonuc.css("background-color", "red");
                                }else{
                                    sonuc.css("background-color", "green");
                                }
                            }else{
                                sonuc.css("background-color", "red");
                            }
                            sonuc.css("color", "white");
                        });
                        $('.sonuc3').on('change',function(){
                            var sonuc=$(this);
                            if(sonuc.val()!=="")
                            {
                                var result = parseFloat((sonuc.val()).replace(',','.'));
                                var sapma = parseFloat(sonuc.data('id'));
                                if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                    sonuc.css("background-color", "red");
                                }else{
                                    sonuc.css("background-color", "green");
                                }
                            }else{
                                sonuc.css("background-color", "red");
                            }
                            sonuc.css("color", "white");
                        });
                        $('.sonuc4').on('change',function(){
                            var sonuc=$(this);
                            if(sonuc.val()!=="")
                            {
                                var result = parseFloat((sonuc.val()).replace(',','.'));
                                var sapma = parseFloat(sonuc.data('id'));
                                if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                    sonuc.css("background-color", "red");
                                }else{
                                    sonuc.css("background-color", "green");
                                }
                            }else{
                                sonuc.css("background-color", "red");
                            }
                            sonuc.css("color", "white");
                        });
                        $('#secilenler').val(yeniler);
                    }else {
                        nokta1 = $('#nokta1');
                        nokta2 = $('#nokta2');
                        nokta3 = $('#nokta3');
                        $.each(oncekilist, function (index) {
                            if (yeni !== oncekilist[index]) {
                                var fark1 = 0, fark2 = 0, fark3 = 0;
                                switch (sayi) {
                                    case 1:  fark1 = 0;fark2 = 0;fark3 = 0;break;
                                    case 2:  fark1 = 0.1;fark2 = 0.05;fark3 = 0.03;break;
                                    case 3:  fark1 = 0.2;fark2 = 0.10;fark3 = 0.06;break;
                                    case 4:  fark1 = 0.3;fark2 = 0.15;fark3 = 0.09;break;
                                    case 5:  fark1 = 0.5;fark2 = 0.20;fark3 = 0.12;break;
                                    case 6:  fark1 = 0.7;fark2 = 0.25;fark3 = 0.15;break;
                                    case 7:  fark1 = 0.9;fark2 = 0.30;fark3 = 0.18;break;
                                    case 8:  fark1 = 1.0;fark2 = 0.35;fark3 = 0.21;break;
                                    case 9:  fark1 = 1.2;fark2 = 0.40;fark3 = 0.24;break;
                                    case 10: fark1 = 1.3;fark2 = 0.50;fark3 = 0.27;break;
                                    case 11: fark1 = 1.4;fark2 = 0.55;fark3 = 0.30;break;
                                    case 12: fark1 = 1.5;fark2 = 0.60;fark3 = 0.33;break;
                                }
                                var label = $('.label' + oncekilist[index]);
                                yeniler += (yeniler === "" ? "" : ",") + oncekilist[index];
                                var parent = label.closest('.sayaclar_ek').children('tbody');
                                var seri = parent.children('tr').children('td.serino'+oncekilist[index]).text();
                                var row1 = parent.children('tr.row1');
                                var row2 = parent.children('tr.row2');
                                var row3 = parent.children('tr.row3');
                                newSayac = '<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label' + oncekilist[index] + '" data-id="' + sayi + '">' + sayi + '.Seri No</b></td><td><b>Ölçüm</b></td><td><b>Sonuç</b></td></tr>' +
                                '<tr class="rows row1" data-id="1"><td class="serino serino' + (oncekilist[index]) + '"">' + seri + '<input type="text" id="serino['+(sayi-1)+']" name="serino['+(sayi-1)+']" class="form-control hide" value="'+seri+'"/><input type="text" id="kalibrasyonid['+(sayi-1)+']" name="kalibrasyonid['+(sayi-1)+']" class="form-control hide" value="'+(oncekilist[index])+'"/></td><td>' + nokta1.data('id') + '<span class="required" aria-required="true"> * </span></td>' +
                                    '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc[' + (sayi - 1) + '][0]" name="sonuc[' + (sayi - 1) + '][0]" data-id="' + nokta1.val() + '" style="color:black;background-color:white" data-fark="' + fark1 + '" class="form-control valid'+(sayi-1)+' sonuc sonuc1" tabindex="'+((3*(sayi-1))+1)+'" value="'+row1.children('td').children('.sonuc').val()+'"></td>' +
                                    '</tr>' +
                                '<tr class="rows row2" data-id="2" style="padding-top:10px;"><td></td><td>' + nokta2.data('id') + '<span class="required" aria-required="true"> * </span></td>' +
                                    '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc[' + (sayi - 1) + '][1]" name="sonuc[' + (sayi - 1) + '][1]" data-id="' + nokta2.val() + '" style="color:black;background-color:white" data-fark="' + fark2 + '" class="form-control valid'+(sayi-1)+' sonuc sonuc2" tabindex="'+((3*(sayi-1))+2)+'" value="'+row2.children('td').children('.sonuc').val()+'"></td>' +
                                    '</tr>' +
                                '<tr class="rows row3" data-id="3" style="padding-top:10px;"><td></td><td>' + nokta3.data('id') + '<span class="required" aria-required="true"> * </span></td>' +
                                    '<td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc[' + (sayi - 1) + '][2]" name="sonuc[' + (sayi - 1) + '][2]" data-id="' + nokta3.val() + '" style="color:black;background-color:white" data-fark="' + fark3 + '" class="form-control valid'+(sayi-1)+'  sonuc sonuc3" tabindex="'+((3*(sayi-1))+3)+'" value="'+row3.children('td').children('.sonuc').val()+'"></td>' +
                                    '</tr>';
                                parent.html(newSayac);
                                $('input.valid'+(sayi-1)).each(function(){
                                    $(this).rules('remove');
                                    $(this).rules('add', {
                                        required: true
                                    });
                                });
                                var degerler=parent.children('tr.rows');
                                $.each(degerler, function () {
                                    var sonuc = $(this).children('td').children('.sonuc');
                                    var result,sapma;
                                    if(sonuc.val()!=="")
                                    {
                                        result = parseFloat((sonuc.val()).replace(',','.'));
                                        sapma = parseFloat(sonuc.data('id'));
                                        if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                            sonuc.css("background-color", "red");
                                        }else{
                                            sonuc.css("background-color", "green");
                                        }
                                    }else{
                                        sonuc.css("background-color", "red");
                                    }
                                    sonuc.css("color", "white");
                                });
                                sayi++;
                            }
                        });
                        $(".sonuc").inputmask("decimal",{
                            radixPoint:",",
                            groupSeparator: "",
                            digits: 3,
                            autoGroup: true
                        });
                        $('.sonuc').on('change',function(){
                            var sonuc=$(this);
                            if(sonuc.val()!=="")
                            {
                                var result = parseFloat((sonuc.val()).replace(',','.'));
                                var sapma = parseFloat(sonuc.data('id'));
                                if(result.toFixed(3)>sapma || result.toFixed(3)<(sapma*-1)){
                                    sonuc.css("background-color", "red");
                                }else{
                                    sonuc.css("background-color", "green");
                                }
                            }else{
                                sonuc.css("background-color", "red");
                            }
                            sonuc.css("color", "white");
                        });
                        $('#secilenler').val(yeniler);
                    }
                }
                cnt--;
                $("#count").val(cnt-1);
            }
        });
        $('#hf2').on('change',function(){
            if ($(this).attr('checked')) {
                $(".hf2").removeClass('hide');
            } else {
                $(".hf2").addClass('hide');
            }
        });
        $('#hf3').on('change',function(){
            if ($(this).attr('checked')) {
                $(".hf3").removeClass('hide');
            } else {
                $(".hf3").addClass('hide');
            }
        });
        $('#hf32').on('change',function(){
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

        var istasyonadi=$('#istasyonadi').val();
        var sayacadi=$('#sayacadi').val();
        if(istasyonadi!=="" && sayacadi!=="")
        {
            $.blockUI();
            $.getJSON("{{ URL::to('kalibrasyon/istasyonbilgi') }}",{grupid:grupid,istasyonid:istasyonadi}, function (event) {
                if (event.durum) //istasyon bilgilerini getirir
                {
                    var istasyon = event.istasyon;
                    var sayacadlari = event.sayacadlari;
                    var sayacsayi=istasyon.sayacsayi;
                    $('#sayacsayi').val(sayacsayi);
                    $("#noktasayi").val(0);
                    $("#sayacadi").empty();
                    $("#sayacadi").append('<option value=""> Seçiniz... </option>');
                    $.each(sayacadlari, function (index) {
                        if(sayacadi===sayacadlari[index].id)
                            $("#sayacadi").append('<option value="' + sayacadlari[index].id + '" selected> ' + sayacadlari[index].sayacadi + '</option>');
                        else
                            $("#sayacadi").append('<option value="' + sayacadlari[index].id + '"> ' + sayacadlari[index].sayacadi + '</option>');
                    });
                    $("#sayacadi").select2('val',sayacadi);
                }else{
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
            $.blockUI();
            $.getJSON("{{ URL::to('kalibrasyon/kalibrasyonstandart') }}",{grupid:grupid,sayacadiid:sayacadi}, function (event) {
                if (event.durum) //kalibrasyon standarlarını getirir
                {
                    //var sayacadi = event.sayacadi;
                    var standart = event.kalibrasyonstandart;
                    var kalibrasyon = event.kalibrasyon;
                    $("#hassasiyet").empty();
                    $("#hassasiyet").select2('val','');
                    var secilenler=$("#kalibrasyon").val();
                    $("#kalibrasyon").empty();
                    $("#kalibrasyon").select2('val','');
                    while($('.sayaclar .sayaclar_ek').size()>0){
                        $('.sayaclar .sayaclar_ek:last').remove();
                        cnt--;
                    }
                    cnt=0;
                    $('.lfhf').addClass('hide');
                    $('#secilenler').val("");
                    $("#count").val(cnt++);
                    $.each(standart, function (index) {
                        $("#hassasiyet").append('<option value="' + standart[index].id + '"> ' + standart[index].hassasiyet + '</option>');
                        if(index===0){
                            $("#hassasiyet").select2('val',standart[index].id);
                            $("#noktasayi").val(standart[index].noktasayisi);
                            $('.noktalar_ek').remove();
                            var sayi=1;
                            while(parseInt(standart[index].noktasayisi)>=sayi)
                            {
                                var durum="";
                                switch(sayi) {
                                    case 1: durum = standart[index].nokta1+'" value="'+standart[index].sapma1+'"/>'; break;
                                    case 2: durum = standart[index].nokta2+'" value="'+standart[index].sapma2+'"/>'; break;
                                    case 3: durum = standart[index].nokta3+'" value="'+standart[index].sapma3+'"/>'; break;
                                    case 4: durum = standart[index].nokta4+'" value="'+standart[index].sapma4+'"/>'; break;
                                    case 5: durum = standart[index].nokta5+'" value="'+standart[index].sapma5+'"/>'; break;
                                    case 6: durum = standart[index].nokta6+'" value="'+standart[index].sapma6+'"/>'; break;
                                    case 7: durum = standart[index].nokta7+'" value="'+standart[index].sapma7+'"/>'; break;
                                }
                                var nokta='<div class="form-group noktalar_ek"><input id="nokta'+sayi+'" name="nokta'+sayi+'" data-id="'+durum+'</div>';
                                $('.noktalar').append(nokta);
                                sayi++;
                            }
                            if(parseInt(standart[index].noktasayisi)>3)
                            {
                                $('.lfhf').removeClass('hide');
                            }else{
                                $('.lfhf').addClass('hide');
                            }
                        }
                    });
                    $.each(kalibrasyon, function (index) {
                        $("#kalibrasyon").append('<option data-yil="'+kalibrasyon[index].imalyili+'" data-id="'+kalibrasyon[index].kalibrasyon_seri+'" data-hassasiyet="'+kalibrasyon[index].hassasiyet+'" value="' + kalibrasyon[index].id + '"> ' + kalibrasyon[index].kalibrasyon_seri + '</option>');
                    });
                    $("#kalibrasyon").select2('val',secilenler);
                } else {
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
        }
        $("select").on("select2-close", function () { $(this).valid(); });
    });
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Kalibrasyon Kayıt Girişi
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('kalibrasyon/kayitgirisi/'.$grup->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <input type="text" id="count" name="count" value="{{Input::old('count') ? Input::old('count') : 0}}" data-required="1" class="form-control hide">
                    <input type="text" id="sayacsayi" name="sayacsayi" value="{{Input::old('sayacsayi') ? Input::old('sayacsayi') : 0}}" data-required="1" class="form-control hide">
                    <input type="text" id="noktasayi" name="noktasayi" value="{{Input::old('noktasayi') ? Input::old('noktasayi') : 0}}" data-required="1" class="form-control hide">
                    <input type="text" id="secilenler" name="secilenler" value="{{Input::old('secilenler') ? Input::old('secilenler') : ''}}" data-required="1" class="form-control hide">
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">İstasyon Adı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="istasyonadi" name="istasyonadi" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($istasyonlar as $istasyon)
                                @if(Input::old('istasyonadi')==$istasyon->id)
                                    <option value="{{ $istasyon->id }}" selected>{{ $istasyon->istasyonadi }}</option>
                                @else
                                    <option value="{{ $istasyon->id }}" >{{ $istasyon->istasyonadi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Sayaç Adı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="sayacadi" name="sayacadi" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($sayacadlari as $sayacadi)
                                @if(Input::old('sayacadi')==$sayacadi->id)
                                    <option value="{{ $sayacadi->id }}" selected>{{ $sayacadi->sayacadi }}</option>
                                @else
                                    <option value="{{ $sayacadi->id }}" >{{ $sayacadi->sayacadi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Ölçüm Hassasiyeti:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="hassasiyet" name="hassasiyet" tabindex="-1" title="">
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label class="control-label col-xs-4">Seri No:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><select class="form-control select2 select2-offscreen" multiple="" tabindex="-1" id="kalibrasyon" name="kalibrasyon[]">
                                @foreach($kalibrasyonlar as $kalibrasyon)
                                    <option value="{{ $kalibrasyon->id }}" data-id="{{$kalibrasyon->hassasiyet}}">{{ $kalibrasyon->kalibrasyon_seri }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="kalibrasyonlar" class="hide kalibrasyonlar">
                        @if(Input::old('kalibrasyon'))
                            @foreach(Input::old('kalibrasyon') as $seri)
                                {{$seri}}
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="form-group noktalar hide">
                </div>
                <div class="form-group farklar hide">
                    <input type="text" id="fark1[0]"  name="fark1[0]"  value="0"><input   type="text" id="fark2[0]"  name="fark2[0]"  value="0"><input    type="text" id="fark3[0]"  name="fark3[0]"  value="0">
                    <input type="text" id="fark1[1]"  name="fark1[1]"  value="0.1"><input type="text" id="fark2[1]"  name="fark2[1]"  value="0.05"><input type="text" id="fark3[1]"  name="fark3[1]"  value="0.03">
                    <input type="text" id="fark1[2]"  name="fark1[2]"  value="0.2"><input type="text" id="fark2[2]"  name="fark2[2]"  value="0.10"><input type="text" id="fark3[2]"  name="fark3[2]"  value="0.06">
                    <input type="text" id="fark1[3]"  name="fark1[3]"  value="0.3"><input type="text" id="fark2[3]"  name="fark2[3]"  value="0.15"><input type="text" id="fark3[3]"  name="fark3[3]"  value="0.09">
                    <input type="text" id="fark1[4]"  name="fark1[4]"  value="0.5"><input type="text" id="fark2[4]"  name="fark2[4]"  value="0.20"><input type="text" id="fark3[4]"  name="fark3[4]"  value="0.12">
                    <input type="text" id="fark1[5]"  name="fark1[5]"  value="0.7"><input type="text" id="fark2[5]"  name="fark2[5]"  value="0.25"><input type="text" id="fark3[5]"  name="fark3[5]"  value="0.15">
                    <input type="text" id="fark1[6]"  name="fark1[6]"  value="0.9"><input type="text" id="fark2[6]"  name="fark2[6]"  value="0.30"><input type="text" id="fark3[6]"  name="fark3[6]"  value="0.18">
                    <input type="text" id="fark1[7]"  name="fark1[7]"  value="1.0"><input type="text" id="fark2[7]"  name="fark2[7]"  value="0.35"><input type="text" id="fark3[7]"  name="fark3[7]"  value="0.21">
                    <input type="text" id="fark1[8]"  name="fark1[8]"  value="1.2"><input type="text" id="fark2[8]"  name="fark2[8]"  value="0.40"><input type="text" id="fark3[8]"  name="fark3[8]"  value="0.24">
                    <input type="text" id="fark1[9]"  name="fark1[9]"  value="1.3"><input type="text" id="fark2[9]"  name="fark2[9]"  value="0.50"><input type="text" id="fark3[9]"  name="fark3[9]"  value="0.27">
                    <input type="text" id="fark1[10]" name="fark1[10]" value="1.4"><input type="text" id="fark2[10]" name="fark2[10]" value="0.55"><input type="text" id="fark3[10]" name="fark3[10]" value="0.30">
                    <input type="text" id="fark1[11]" name="fark1[11]" value="1.5"><input type="text" id="fark2[11]" name="fark2[11]" value="0.60"><input type="text" id="fark3[11]" name="fark3[11]" value="0.33">
                </div>
                <div class="form-group col-xs-12 lfhf hide">
                    <h4 style="padding-left: 30px;">Test Frekansları</h4>
                    <div class="form-group lfhf_ek">
                        <label class="control-label col-sm-2 col-xs-4"><input type="checkbox" id=lf name="lf" checked disabled/> LF </label>
                        <label class="control-label col-sm-2 col-xs-4"><input type="checkbox" id=hf2 name="hf2" /> HF2 </label>
                        <label class="control-label col-sm-2 col-xs-4"><input type="checkbox" id=hf3 name="hf3" /> HF3-I </label>
                        <label class="control-label col-sm-2 col-xs-4"><input type="checkbox" id=hf32 name="hf32" /> HF3-II </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-6"> Kalibrasyon Bilgileri</label>
                </div>
                <div class="form-group sayaclar">
                @if(Input::old('kalibrasyon'))
                    @if(Input::old('noktasayi')==3)
                       @for($i=0;$i<count(Input::old('kalibrasyon'));$i++)
                          <table class="col-sm-10 col-sm-offset-1 col-xs-11 sayaclar_ek">
                              <tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label{{Input::old('kalibrasyon.'.$i)}}" data-id="{{$i+1}}">{{$i+1}}.Seri No</b></td><td><b>Ölçüm</b></td><td><b>Sonuç</b></td></tr>
                              <tr class="rows row1" data-id="1"><td class="serino serino{{Input::old('kalibrasyon.'.$i)}}">{{Input::old('serino.'.$i)}}<input type="text" id="serino[{{$i}}]" name="serino[{{$i}}]" class="form-control hide" value="{{Input::old('serino.'.$i)}}"/><input type="text" id="kalibrasyonid[{{$i}}]" name="kalibrasyonid[{{$i}}]" class="form-control hide" value="{{Input::old('kalibrasyon.'.$i)}}"/></td><td>Qmax<span class="required" aria-required="true"> * </span></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc[{{$i}}][0]" name="sonuc[{{$i}}][0]" data-id="{{Input::old('nokta1')}}" style="color:black;background-color:white" data-fark="{{Input::old('fark1.'.$i)}}" class="form-control valid{{$i}} sonuc sonuc1" tabindex="{{(3*($i))+1}}" value="{{Input::old('sonuc.'.$i.'.0')}}"></td>
                              </tr>
                              <tr class="rows row2" data-id="2" style="padding-top:10px;"><td></td><td>0,20 Qmax<span class="required" aria-required="true"> * </span></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc[{{$i}}][1]" name="sonuc[{{$i}}][1]" data-id="{{Input::old('nokta2')}}" style="color:black;background-color:white" data-fark="{{Input::old('fark2.'.$i)}}" class="form-control valid{{$i}} sonuc sonuc2" tabindex="{{(3*($i))+2}}" value="{{Input::old('sonuc.'.$i.'.1')}}"></td>
                              </tr>
                              <tr class="rows row3" data-id="3" style="padding-top:10px;"><td></td><td>Qmin<span class="required" aria-required="true"> * </span></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc[{{$i}}][2]" name="sonuc[{{$i}}][2]" data-id="{{Input::old('nokta3')}}" style="color:black;background-color:white" data-fark="{{Input::old('fark3.'.$i)}}" class="form-control valid{{$i}} sonuc sonuc3" tabindex="{{(3*($i))+3}}" value="{{Input::old('sonuc.'.$i.'.2')}}"></td>
                              </tr>
                          </table>
                       @endfor
                    @elseif(Input::old('noktasayi')==5)
                       @for($i=0;$i<count(Input::old('kalibrasyon'));$i++)
                          <table class="col-sm-10 col-sm-offset-1 col-xs-11 sayaclar_ek">
                              <tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label{{Input::old('kalibrasyon.'.$i)}}" data-id="{{$i+1}}">{{$i+1}}.Seri No</b></td>
                                  <td><b>Ölçüm</b></td><td class="lf"><b>LF Sonuç</b></td><td class="hf2 {{Input::old('hf2') ? '' : 'hide'}}"><b>HF2 Sonuç</b></td><td class="hf3 {{Input::old('hf3') ? '' : 'hide'}}"><b>HF3-I Sonuç</b></td><td class="hf32 {{Input::old('hf32') ? '' : 'hide'}}"><b>HF3-II Sonuç</b></td></tr>
                              <tr class="rows row1" data-id="1"><td class="serino serino{{Input::old('kalibrasyon.'.$i)}}">{{Input::old('serino.'.$i)}}<input type="text" id="serino[{{$i}}]" name="serino[{{$i}}]" class="form-control hide" value="{{Input::old('serino.'.$i)}}"/><input type="text" id="kalibrasyonid[{{$i}}]" name="kalibrasyonid[{{$i}}]" class="form-control hide" value="{{Input::old('kalibrasyon.'.$i)}}"/></td><td>Qmax<span class="required" aria-required="true"> * </span></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[{{$i}}][0]" name="sonuc1[{{$i}}][0]" data-id="{{Input::old('nokta1')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+1}}"  class="form-control valid{{$i}} lf   sonuc1 " value="{{Input::old('sonuc1.'.$i.'.0')}}"></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[{{$i}}][0]" name="sonuc2[{{$i}}][0]" data-id="{{Input::old('nokta1')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+6}}"  class="form-control valid{{$i}} hf2  sonuc2 {{Input::old('hf2')  ? '' : 'hide'}}" value="{{Input::old('sonuc2.'.$i.'.0')}}"></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[{{$i}}][0]" name="sonuc3[{{$i}}][0]" data-id="{{Input::old('nokta1')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+11}}" class="form-control valid{{$i}} hf3  sonuc3 {{Input::old('hf3')  ? '' : 'hide'}}" value="{{Input::old('sonuc3.'.$i.'.0')}}"></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[{{$i}}][0]" name="sonuc4[{{$i}}][0]" data-id="{{Input::old('nokta1')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+16}}" class="form-control valid{{$i}} hf32 sonuc4 {{Input::old('hf32') ? '' : 'hide'}}" value="{{Input::old('sonuc4.'.$i.'.0')}}"></td>
                              </tr>
                              <tr class="rows row2" data-id="2"><td></td><td>0,70 Qmax<span class="required" aria-required="true"> * </span></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[{{$i}}][1]" name="sonuc1[{{$i}}][1]" data-id="{{Input::old('nokta2')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+2}}"  class="form-control valid{{$i}} lf   sonuc1 " value="{{Input::old('sonuc1.'.$i.'.1')}}"></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[{{$i}}][1]" name="sonuc2[{{$i}}][1]" data-id="{{Input::old('nokta2')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+7}}"  class="form-control valid{{$i}} hf2  sonuc2 {{Input::old('hf2')  ? '' : 'hide'}}" value="{{Input::old('sonuc2.'.$i.'.1')}}"></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[{{$i}}][1]" name="sonuc3[{{$i}}][1]" data-id="{{Input::old('nokta2')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+12}}" class="form-control valid{{$i}} hf3  sonuc3 {{Input::old('hf3')  ? '' : 'hide'}}" value="{{Input::old('sonuc3.'.$i.'.1')}}"></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[{{$i}}][1]" name="sonuc4[{{$i}}][1]" data-id="{{Input::old('nokta2')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+17}}" class="form-control valid{{$i}} hf32 sonuc4 {{Input::old('hf32') ? '' : 'hide'}}" value="{{Input::old('sonuc4.'.$i.'.1')}}"></td>
                              </tr>
                              <tr class="rows row3" data-id="3"><td></td><td>0,40 Qmax<span class="required" aria-required="true"> * </span></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[{{$i}}][2]" name="sonuc1[{{$i}}][2]" data-id="{{Input::old('nokta3')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+3}}"  class="form-control valid{{$i}} lf   sonuc1 " value="{{Input::old('sonuc1.'.$i.'.2')}}"></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[{{$i}}][2]" name="sonuc2[{{$i}}][2]" data-id="{{Input::old('nokta3')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+8}}"  class="form-control valid{{$i}} hf2  sonuc2 {{Input::old('hf2')  ? '' : 'hide'}}" value="{{Input::old('sonuc2.'.$i.'.2')}}"></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[{{$i}}][2]" name="sonuc3[{{$i}}][2]" data-id="{{Input::old('nokta3')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+13}}" class="form-control valid{{$i}} hf3  sonuc3 {{Input::old('hf3')  ? '' : 'hide'}}" value="{{Input::old('sonuc3.'.$i.'.2')}}"></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[{{$i}}][2]" name="sonuc4[{{$i}}][2]" data-id="{{Input::old('nokta3')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+18}}" class="form-control valid{{$i}} hf32 sonuc4 {{Input::old('hf32') ? '' : 'hide'}}" value="{{Input::old('sonuc4.'.$i.'.2')}}"></td>
                              </tr>
                              <tr class="rows row4" data-id="4"><td></td><td>0,25 Qmax<span class="required" aria-required="true"> * </span></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[{{$i}}][3]" name="sonuc1[{{$i}}][3]" data-id="{{Input::old('nokta4')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+4}}"  class="form-control valid{{$i}} lf   sonuc1 " value="{{Input::old('sonuc1.'.$i.'.3')}}"></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[{{$i}}][3]" name="sonuc2[{{$i}}][3]" data-id="{{Input::old('nokta4')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+9}}"  class="form-control valid{{$i}} hf2  sonuc2 {{Input::old('hf2')  ? '' : 'hide'}}" value="{{Input::old('sonuc2.'.$i.'.3')}}"></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[{{$i}}][3]" name="sonuc3[{{$i}}][3]" data-id="{{Input::old('nokta4')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+14}}" class="form-control valid{{$i}} hf3  sonuc3 {{Input::old('hf3')  ? '' : 'hide'}}" value="{{Input::old('sonuc3.'.$i.'.3')}}"></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[{{$i}}][3]" name="sonuc4[{{$i}}][3]" data-id="{{Input::old('nokta4')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+19}}" class="form-control valid{{$i}} hf32 sonuc4 {{Input::old('hf32') ? '' : 'hide'}}" value="{{Input::old('sonuc4.'.$i.'.3')}}"></td>
                              </tr>
                              <tr class="rows row5" data-id="5"><td></td><td>0,10 Qmax<span class="required" aria-required="true"> * </span></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[{{$i}}][4]" name="sonuc1[{{$i}}][4]" data-id="{{Input::old('nokta5')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+5}}"  class="form-control valid{{$i}} lf   sonuc1 " value="{{Input::old('sonuc1.'.$i.'.4')}}"></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[{{$i}}][4]" name="sonuc2[{{$i}}][4]" data-id="{{Input::old('nokta5')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+10}}" class="form-control valid{{$i}} hf2  sonuc2 {{Input::old('hf2')  ? '' : 'hide'}}" value="{{Input::old('sonuc2.'.$i.'.4')}}"></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[{{$i}}][4]" name="sonuc3[{{$i}}][4]" data-id="{{Input::old('nokta5')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+15}}" class="form-control valid{{$i}} hf3  sonuc3 {{Input::old('hf3')  ? '' : 'hide'}}" value="{{Input::old('sonuc3.'.$i.'.4')}}"></td>
                                  <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[{{$i}}][4]" name="sonuc4[{{$i}}][4]" data-id="{{Input::old('nokta5')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(5*4*($i))+20}}" class="form-control valid{{$i}} hf32 sonuc4 {{Input::old('hf32') ? '' : 'hide'}}" value="{{Input::old('sonuc4.'.$i.'.4')}}"></td>
                              </tr>
                              </table>
                             @endfor
                        @elseif(Input::old('noktasayi')==6)
                            @for($i=0;$i<count(Input::old('kalibrasyon'));$i++)
                                <table class="col-sm-10 col-sm-offset-1 col-xs-11 sayaclar_ek">
                                    <tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label{{Input::old('kalibrasyon.'.$i)}}" data-id="{{$i+1}}">{{$i+1}}.Seri No</b></td>
                                        <td><b>Ölçüm</b></td><td class="lf"><b>LF Sonuç</b></td><td class="hf2 {{Input::old('hf2') ? '' : 'hide'}}"><b>HF2 Sonuç</b></td><td class="hf3 {{Input::old('hf3') ? '' : 'hide'}}"><b>HF3-I Sonuç</b></td><td class="hf32 {{Input::old('hf32') ? '' : 'hide'}}"><b>HF3-II Sonuç</b></td></tr>
                                    <tr class="rows row1" data-id="1"><td class="serino serino{{Input::old('kalibrasyon.'.$i)}}">{{Input::old('serino.'.$i)}}<input type="text" id="serino[{{$i}}]" name="serino[{{$i}}]" class="form-control hide" value="{{Input::old('serino.'.$i)}}"/><input type="text" id="kalibrasyonid[{{$i}}]" name="kalibrasyonid[{{$i}}]" class="form-control hide" value="{{Input::old('kalibrasyon.'.$i)}}"/></td><td>Qmax<span class="required" aria-required="true"> * </span></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[{{$i}}][0]" name="sonuc1[{{$i}}][0]" data-id="{{Input::old('nokta1')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+1}}"  class="form-control valid{{$i}} lf   sonuc1 " value="{{Input::old('sonuc1.'.$i.'.0')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[{{$i}}][0]" name="sonuc2[{{$i}}][0]" data-id="{{Input::old('nokta1')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+7}}"  class="form-control valid{{$i}} hf2  sonuc2 {{Input::old('hf2')  ? '' : 'hide'}}" value="{{Input::old('sonuc2.'.$i.'.0')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[{{$i}}][0]" name="sonuc3[{{$i}}][0]" data-id="{{Input::old('nokta1')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+13}}" class="form-control valid{{$i}} hf3  sonuc3 {{Input::old('hf3')  ? '' : 'hide'}}" value="{{Input::old('sonuc3.'.$i.'.0')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[{{$i}}][0]" name="sonuc4[{{$i}}][0]" data-id="{{Input::old('nokta1')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+19}}" class="form-control valid{{$i}} hf32 sonuc4 {{Input::old('hf32') ? '' : 'hide'}}" value="{{Input::old('sonuc4.'.$i.'.0')}}"></td>
                                    </tr>
                                    <tr class="rows row2" data-id="2"><td></td><td>0,70 Qmax<span class="required" aria-required="true"> * </span></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[{{$i}}][1]" name="sonuc1[{{$i}}][1]" data-id="{{Input::old('nokta2')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+2}}"  class="form-control valid{{$i}} lf   sonuc1 " value="{{Input::old('sonuc1.'.$i.'.1')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[{{$i}}][1]" name="sonuc2[{{$i}}][1]" data-id="{{Input::old('nokta2')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+8}}"  class="form-control valid{{$i}} hf2  sonuc2 {{Input::old('hf2')  ? '' : 'hide'}}" value="{{Input::old('sonuc2.'.$i.'.1')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[{{$i}}][1]" name="sonuc3[{{$i}}][1]" data-id="{{Input::old('nokta2')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+14}}" class="form-control valid{{$i}} hf3  sonuc3 {{Input::old('hf3')  ? '' : 'hide'}}" value="{{Input::old('sonuc3.'.$i.'.1')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[{{$i}}][1]" name="sonuc4[{{$i}}][1]" data-id="{{Input::old('nokta2')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+20}}" class="form-control valid{{$i}} hf32 sonuc4 {{Input::old('hf32') ? '' : 'hide'}}" value="{{Input::old('sonuc4.'.$i.'.1')}}"></td>
                                    </tr>
                                    <tr class="rows row3" data-id="3"><td></td><td>0,40 Qmax<span class="required" aria-required="true"> * </span></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[{{$i}}][2]" name="sonuc1[{{$i}}][2]" data-id="{{Input::old('nokta3')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+3}}"  class="form-control valid{{$i}} lf   sonuc1 " value="{{Input::old('sonuc1.'.$i.'.2')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[{{$i}}][2]" name="sonuc2[{{$i}}][2]" data-id="{{Input::old('nokta3')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+9}}"  class="form-control valid{{$i}} hf2  sonuc2 {{Input::old('hf2')  ? '' : 'hide'}}" value="{{Input::old('sonuc2.'.$i.'.2')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[{{$i}}][2]" name="sonuc3[{{$i}}][2]" data-id="{{Input::old('nokta3')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+15}}" class="form-control valid{{$i}} hf3  sonuc3 {{Input::old('hf3')  ? '' : 'hide'}}" value="{{Input::old('sonuc3.'.$i.'.2')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[{{$i}}][2]" name="sonuc4[{{$i}}][2]" data-id="{{Input::old('nokta3')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+21}}" class="form-control valid{{$i}} hf32 sonuc4 {{Input::old('hf32') ? '' : 'hide'}}" value="{{Input::old('sonuc4.'.$i.'.2')}}"></td>
                                    </tr>
                                    <tr class="rows row4" data-id="4"><td></td><td>0,25 Qmax<span class="required" aria-required="true"> * </span></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[{{$i}}][3]" name="sonuc1[{{$i}}][3]" data-id="{{Input::old('nokta4')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+4}}"  class="form-control valid{{$i}} lf   sonuc1 " value="{{Input::old('sonuc1.'.$i.'.3')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[{{$i}}][3]" name="sonuc2[{{$i}}][3]" data-id="{{Input::old('nokta4')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+10}}" class="form-control valid{{$i}} hf2  sonuc2 {{Input::old('hf2')  ? '' : 'hide'}}" value="{{Input::old('sonuc2.'.$i.'.3')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[{{$i}}][3]" name="sonuc3[{{$i}}][3]" data-id="{{Input::old('nokta4')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+16}}" class="form-control valid{{$i}} hf3  sonuc3 {{Input::old('hf3')  ? '' : 'hide'}}" value="{{Input::old('sonuc3.'.$i.'.3')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[{{$i}}][3]" name="sonuc4[{{$i}}][3]" data-id="{{Input::old('nokta4')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+22}}" class="form-control valid{{$i}} hf32 sonuc4 {{Input::old('hf32') ? '' : 'hide'}}" value="{{Input::old('sonuc4.'.$i.'.3')}}"></td>
                                    </tr>
                                    <tr class="rows row5" data-id="5"><td></td><td>0,10 Qmax<span class="required" aria-required="true"> * </span></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[{{$i}}][4]" name="sonuc1[{{$i}}][4]" data-id="{{Input::old('nokta5')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+5}}"  class="form-control valid{{$i}} lf   sonuc1 " value="{{Input::old('sonuc1.'.$i.'.4')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[{{$i}}][4]" name="sonuc2[{{$i}}][4]" data-id="{{Input::old('nokta5')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+11}}" class="form-control valid{{$i}} hf2  sonuc2 {{Input::old('hf2')  ? '' : 'hide'}}" value="{{Input::old('sonuc2.'.$i.'.4')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[{{$i}}][4]" name="sonuc3[{{$i}}][4]" data-id="{{Input::old('nokta5')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+17}}" class="form-control valid{{$i}} hf3  sonuc3 {{Input::old('hf3')  ? '' : 'hide'}}" value="{{Input::old('sonuc3.'.$i.'.4')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[{{$i}}][4]" name="sonuc4[{{$i}}][4]" data-id="{{Input::old('nokta5')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+23}}" class="form-control valid{{$i}} hf32 sonuc4 {{Input::old('hf32') ? '' : 'hide'}}" value="{{Input::old('sonuc4.'.$i.'.4')}}"></td>
                                    </tr>
                                    <tr class="rows row6" data-id="6"><td></td><td>0,05 Qmax<span class="required" aria-required="true"> * </span></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[{{$i}}][5]" name="sonuc1[{{$i}}][5]" data-id="{{Input::old('nokta6')}}'" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+6}}"  class="form-control valid{{$i}} lf   sonuc1 " value="{{Input::old('sonuc1.'.$i.'.5')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[{{$i}}][5]" name="sonuc2[{{$i}}][5]" data-id="{{Input::old('nokta6')}}'" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+12}}" class="form-control valid{{$i}} hf2  sonuc2 {{Input::old('hf2')  ? '' : 'hide'}}" value="{{Input::old('sonuc2.'.$i.'.5')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[{{$i}}][5]" name="sonuc3[{{$i}}][5]" data-id="{{Input::old('nokta6')}}'" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+18}}" class="form-control valid{{$i}} hf3  sonuc3 {{Input::old('hf3')  ? '' : 'hide'}}" value="{{Input::old('sonuc3.'.$i.'.5')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[{{$i}}][5]" name="sonuc4[{{$i}}][5]" data-id="{{Input::old('nokta6')}}'" style="color:black;background-color:white" maxlength="9" tabindex="{{(6*4*($i))+24}}" class="form-control valid{{$i}} hf32 sonuc4 {{Input::old('hf32') ? '' : 'hide'}}" value="{{Input::old('sonuc4.'.$i.'.5')}}"></td>
                                    </tr>
                                </table>
                            @endfor
                        @else
                            @for($i=0;$i<count(Input::old('kalibrasyon'));$i++)
                                <table class="col-sm-10 col-sm-offset-1 col-xs-11 sayaclar_ek">
                                    <tr style="text-align: left;font-size:14px"><td style="width: 100px"><b class="label{{Input::old('kalibrasyon.'.$i)}}" data-id="{{$i+1}}">{{$i+1}}.Seri No</b></td>
                                        <td><b>Ölçüm</b></td><td class="lf"><b>LF Sonuç</b></td><td class="hf2 {{Input::old('hf2') ? '' : 'hide'}}"><b>HF2 Sonuç</b></td><td class="hf3 {{Input::old('hf3') ? '' : 'hide'}}"><b>HF3-I Sonuç</b></td><td class="hf32 {{Input::old('hf32') ? '' : 'hide'}}"><b>HF3-II Sonuç</b></td></tr>
                                    <tr class="rows row1" data-id="1"><td class="serino serino{{Input::old('kalibrasyon.'.$i)}}">{{Input::old('serino.'.$i)}}<input type="text" id="serino[{{$i}}]" name="serino[{{$i}}]" class="form-control hide" value="{{Input::old('serino.'.$i)}}"/><input type="text" id="kalibrasyonid[{{$i}}]" name="kalibrasyonid[{{$i}}]" class="form-control hide" value="{{Input::old('kalibrasyon.'.$i)}}"/></td><td>Qmax<span class="required" aria-required="true"> * </span></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[{{$i}}][0]" name="sonuc1[{{$i}}][0]" data-id="{{Input::old('nokta1')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+1}}"  class="form-control valid{{$i}} lf   sonuc1 " value="{{Input::old('sonuc1.'.$i.'.0')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[{{$i}}][0]" name="sonuc2[{{$i}}][0]" data-id="{{Input::old('nokta1')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+8}}"  class="form-control valid{{$i}} hf2  sonuc2 {{Input::old('hf2')  ? '' : 'hide'}}" value="{{Input::old('sonuc2.'.$i.'.0')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[{{$i}}][0]" name="sonuc3[{{$i}}][0]" data-id="{{Input::old('nokta1')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+15}}" class="form-control valid{{$i}} hf3  sonuc3 {{Input::old('hf3')  ? '' : 'hide'}}" value="{{Input::old('sonuc3.'.$i.'.0')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[{{$i}}][0]" name="sonuc4[{{$i}}][0]" data-id="{{Input::old('nokta1')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+22}}" class="form-control valid{{$i}} hf32 sonuc4 {{Input::old('hf32') ? '' : 'hide'}}" value="{{Input::old('sonuc4.'.$i.'.0')}}"></td>
                                    </tr>
                                    <tr class="rows row2" data-id="2"><td></td><td>0,70 Qmax<span class="required" aria-required="true"> * </span></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[{{$i}}][1]" name="sonuc1[{{$i}}][1]" data-id="{{Input::old('nokta2')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+2}}"  class="form-control valid{{$i}} lf   sonuc1 " value="{{Input::old('sonuc1.'.$i.'.1')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[{{$i}}][1]" name="sonuc2[{{$i}}][1]" data-id="{{Input::old('nokta2')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+9}}"  class="form-control valid{{$i}} hf2  sonuc2 {{Input::old('hf2')  ? '' : 'hide'}}" value="{{Input::old('sonuc2.'.$i.'.1')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[{{$i}}][1]" name="sonuc3[{{$i}}][1]" data-id="{{Input::old('nokta2')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+16}}" class="form-control valid{{$i}} hf3  sonuc3 {{Input::old('hf3')  ? '' : 'hide'}}" value="{{Input::old('sonuc3.'.$i.'.1')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[{{$i}}][1]" name="sonuc4[{{$i}}][1]" data-id="{{Input::old('nokta2')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+23}}" class="form-control valid{{$i}} hf32 sonuc4 {{Input::old('hf32') ? '' : 'hide'}}" value="{{Input::old('sonuc4.'.$i.'.1')}}"></td>
                                    </tr>
                                    <tr class="rows row3" data-id="3"><td></td><td>0,40 Qmax<span class="required" aria-required="true"> * </span></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[{{$i}}][2]" name="sonuc1[{{$i}}][2]" data-id="{{Input::old('nokta3')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+3}}"  class="form-control valid{{$i}} lf   sonuc1 " value="{{Input::old('sonuc1.'.$i.'.2')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[{{$i}}][2]" name="sonuc2[{{$i}}][2]" data-id="{{Input::old('nokta3')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+10}}" class="form-control valid{{$i}} hf2  sonuc2 {{Input::old('hf2')  ? '' : 'hide'}}" value="{{Input::old('sonuc2.'.$i.'.2')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[{{$i}}][2]" name="sonuc3[{{$i}}][2]" data-id="{{Input::old('nokta3')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+17}}" class="form-control valid{{$i}} hf3  sonuc3 {{Input::old('hf3')  ? '' : 'hide'}}" value="{{Input::old('sonuc3.'.$i.'.2')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[{{$i}}][2]" name="sonuc4[{{$i}}][2]" data-id="{{Input::old('nokta3')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+24}}" class="form-control valid{{$i}} hf32 sonuc4 {{Input::old('hf32') ? '' : 'hide'}}" value="{{Input::old('sonuc4.'.$i.'.2')}}"></td>
                                    </tr>
                                    <tr class="rows row4" data-id="4"><td></td><td>0,25 Qmax<span class="required" aria-required="true"> * </span></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[{{$i}}][3]" name="sonuc1[{{$i}}][3]" data-id="{{Input::old('nokta4')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+4}}"  class="form-control valid{{$i}} lf   sonuc1 " value="{{Input::old('sonuc1.'.$i.'.3')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[{{$i}}][3]" name="sonuc2[{{$i}}][3]" data-id="{{Input::old('nokta4')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+11}}" class="form-control valid{{$i}} hf2  sonuc2 {{Input::old('hf2')  ? '' : 'hide'}}" value="{{Input::old('sonuc2.'.$i.'.3')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[{{$i}}][3]" name="sonuc3[{{$i}}][3]" data-id="{{Input::old('nokta4')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+18}}" class="form-control valid{{$i}} hf3  sonuc3 {{Input::old('hf3')  ? '' : 'hide'}}" value="{{Input::old('sonuc3.'.$i.'.3')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[{{$i}}][3]" name="sonuc4[{{$i}}][3]" data-id="{{Input::old('nokta4')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+25}}" class="form-control valid{{$i}} hf32 sonuc4 {{Input::old('hf32') ? '' : 'hide'}}" value="{{Input::old('sonuc4.'.$i.'.3')}}"></td>
                                    </tr>
                                    <tr class="rows row5" data-id="5"><td></td><td>{{(Input::old('hassasiyet')==5 || Input::old('hassasiyet')==9 || Input::old('hassasiyet')==13) ? '0,15 Qmax' : '0,10 Qmax'}}<span class="required" aria-required="true"> * </span></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[{{$i}}][4]" name="sonuc1[{{$i}}][4]" data-id="{{Input::old('nokta5')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+5}}"  class="form-control valid{{$i}} lf   sonuc1 " value="{{Input::old('sonuc1.'.$i.'.4')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[{{$i}}][4]" name="sonuc2[{{$i}}][4]" data-id="{{Input::old('nokta5')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+12}}" class="form-control valid{{$i}} hf2  sonuc2 {{Input::old('hf2')  ? '' : 'hide'}}" value="{{Input::old('sonuc2.'.$i.'.4')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[{{$i}}][4]" name="sonuc3[{{$i}}][4]" data-id="{{Input::old('nokta5')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+19}}" class="form-control valid{{$i}} hf3  sonuc3 {{Input::old('hf3')  ? '' : 'hide'}}" value="{{Input::old('sonuc3.'.$i.'.4')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[{{$i}}][4]" name="sonuc4[{{$i}}][4]" data-id="{{Input::old('nokta5')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+26}}" class="form-control valid{{$i}} hf32 sonuc4 {{Input::old('hf32') ? '' : 'hide'}}" value="{{Input::old('sonuc4.'.$i.'.4')}}"></td>
                                    </tr>
                                    <tr class="rows row6" data-id="6"><td></td><td>0,05 Qmax<span class="required" aria-required="true"> * </span></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[{{$i}}][5]" name="sonuc1[{{$i}}][5]" data-id="{{Input::old('nokta6')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+6}}"  class="form-control valid{{$i}} lf   sonuc1 " value="{{Input::old('sonuc1.'.$i.'.5')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[{{$i}}][5]" name="sonuc2[{{$i}}][5]" data-id="{{Input::old('nokta6')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+13}}" class="form-control valid{{$i}} hf2  sonuc2 {{Input::old('hf2')  ? '' : 'hide'}}" value="{{Input::old('sonuc2.'.$i.'.5')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[{{$i}}][5]" name="sonuc3[{{$i}}][5]" data-id="{{Input::old('nokta6')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+20}}" class="form-control valid{{$i}} hf3  sonuc3 {{Input::old('hf3')  ? '' : 'hide'}}" value="{{Input::old('sonuc3.'.$i.'.5')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[{{$i}}][5]" name="sonuc4[{{$i}}][5]" data-id="{{Input::old('nokta6')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+27}}" class="form-control valid{{$i}} hf32 sonuc4 {{Input::old('hf32') ? '' : 'hide'}}" value="{{Input::old('sonuc4.'.$i.'.5')}}"></td>
                                    </tr>
                                    <tr class="rows row7" data-id="7"><td></td><td>Qmin</td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc1[{{$i}}][6]" name="sonuc1[{{$i}}][6]" data-id="'{{Input::old('nokta7')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+7}}"  class="form-control valid{{$i}} lf   sonuc1 " value="{{Input::old('sonuc1.'.$i.'.6')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc2[{{$i}}][6]" name="sonuc2[{{$i}}][6]" data-id="'{{Input::old('nokta7')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+14}}" class="form-control valid{{$i}} hf2  sonuc2 {{Input::old('hf2')  ? '' : 'hide'}}" value="{{Input::old('sonuc2.'.$i.'.6')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc3[{{$i}}][6]" name="sonuc3[{{$i}}][6]" data-id="'{{Input::old('nokta7')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+21}}" class="form-control valid{{$i}} hf3  sonuc3 {{Input::old('hf3')  ? '' : 'hide'}}" value="{{Input::old('sonuc3.'.$i.'.6')}}"></td>
                                        <td class="input-icon right"><i class="fa"></i><input type="text" id="sonuc4[{{$i}}][6]" name="sonuc4[{{$i}}][6]" data-id="'{{Input::old('nokta7')}}" style="color:black;background-color:white" maxlength="9" tabindex="{{(7*4*($i))+28}}" class="form-control valid{{$i}} hf32 sonuc4 {{Input::old('hf32') ? '' : 'hide'}}" value="{{Input::old('sonuc4.'.$i.'.6')}}"></td>
                                    </tr>
                                </table>
                            @endfor
                        @endif
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
                    <h4 class="modal-title">Kalibrasyon Bilgisi Kaydedilecek</h4>
                </div>
                <div class="modal-body">
                    Girilen Kalibrasyon Bilgileri Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
@stop

