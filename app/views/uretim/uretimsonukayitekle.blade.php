@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Üretim Sonu Kayıt <small>Ekleme Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" >
<link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::to('assets/global/plugins/quagga/css/styles.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.tr.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/fuelux/js/spinner.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-scanner-detection/jquery.scannerdetection.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/quagga/adapter-latest.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/quagga/quagga.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/quagga/live_w_locator.js') }}" type="text/javascript" ></script>

@stop

@section('page-script')
<script src="{{ URL::to('pages/uretim/form-validation-2.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
   FormValidationUretim.init();
});
</script>
<script>
    $(document).scannerDetection({
        timeBeforeScanTest: 100, // wait for the next character for upto 200ms
        avgTimeByChar: 50, // it's not a barcode if a character takes longer than 100ms
        onComplete: function(barcode){
            barcode=barcode.trim();
            $('#eklibarkod').append('<option value="' +barcode+ '"> ' + barcode + '</option>');
            $("#eklibarkod").select2("val", $("#eklibarkod").select2("val").concat(barcode));
            $('#tumbarkod').append('<option value="' +barcode+ '"> ' + barcode + '</option>');
            $("#tumbarkod").select2("val", $("#tumbarkod").select2("val").concat(barcode));
        } // main callback function
    });
    $(document).ready(function() {
        var flag=0;
        $(".kaydet").prop('disabled',true);
        $(".serigir").prop('disabled',true);
        var count=parseInt($("#count").val());
        $('#isemri').on('change', function () {
            var isemri = $(this).val();
            if (isemri !== "") {
                $.blockUI();
                while($('.urunler .urunler_ek').size()>0){
                    $('.urunler .urunler_ek:last').remove();
                    count--;
                }
                $("#eklibarkod").empty();
                var barkodlar = $("#tumbarkod").select2("val");
                $.each(barkodlar, function (i) {
                    $('#eklibarkod').append('<option value="' +barkodlar[i]+ '"> ' + barkodlar[i] + '</option>');
                    $("#eklibarkod").select2("val", $("#eklibarkod").select2("val").concat(barkodlar[i]));
                });
                $.getJSON("{{ URL::to('uretim/urunrecete') }}", {isemri: isemri}, function (event) {
                    if (event.durum) {
                        var recete = event.recete;
                        var uretilenserino = event.uretilenserino;
                        var isemribilgi=event.isemri;
                        $('.tarih').text(isemribilgi.TARIH);
                        $('.teslimtarihi').text(isemribilgi.TESLIM_TARIHI);
                        $('.stokadi').text(isemribilgi.STOK_KODU+' - '+isemribilgi.STOK_ADI);
                        $('.miktar').text(isemribilgi.MIKTAR);
                        $('.uretilen').text(isemribilgi.URETILENMIKTAR);
                        $('.kalan').text(isemribilgi.KALANMIKTAR);
                        $('.siparisno').text(isemribilgi.SIPARIS_NO==null ? '' : isemribilgi.SIPARIS_NO );
                        $('#miktar').val(isemribilgi.MIKTAR);
                        $('#uretilen').val(isemribilgi.URETILENMIKTAR);
                        $('#kalan').val(isemribilgi.KALANMIKTAR);
                        $('#sonuretilen').val(uretilenserino);
                        $('.serinouret').removeClass('hide');
                        var uretilecek = parseInt($('#uretilecek').val());
                        var newRow = "";
                        var depokodu = $('#cikisdepo').val();
                        for(var i=0;i<recete.length;i++){
                            var id=count+i;
                            newRow = '<div class="panel panel-default urunler_ek"><input class="no hide" value="' + id + '"/><div class="panel-heading">' +
                                '<h4 class="panel-title"><a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_' + id + '">'+
                                recete[i].HAM_KODU+' - '+recete[i].HAMMADDE_ADI+'</a>' +
                                '</h4><input name="stokkodu[]" class="stokkodu'+id+' hide stokkodu" value="'+recete[i].HAM_KODU+'"/><input name="stokadi[]" class="stokadi'+id+' hide" value="'+recete[i].HAMMADDE_ADI+'"/>' +
                                '<input name="alturun[]" class="alturun'+id+' hide" value="'+(recete[i].kalem.length>0 ? 1 : 0)+'"/></div><div id="collapse_' + id + '" class="panel-collapse in"><div class="panel-body"><div class="form-group col-sm-6 col-xs-12">' +
                                '<label class="col-xs-4 col-sm-4 control-label">Depo Kodu:<span class="required" aria-required="true"> * </span></label>' +
                                '<div class="input-icon right col-xs-8 col-sm-6"><i class="fa"></i>' +
                                '<select class="form-control select2me valid'+id+' depokodu depokodu' + id + '" id="depokodu' + id + '" name="depokodu[]" tabindex="-1" title="">' +
                                '<option value="">Seçiniz...</option>' +
                                @foreach($netsisdepolar as $depo)
                                    '<option value="{{ $depo->kodu }}">{{ $depo->kodu . ' - ' . $depo->adi }}</option>'+
                                @endforeach
                                '</select></div>' +
                                '<label class="col-xs-1 hidden-sm hidden-md hidden-lg hide"><a class="btn red satirsil">Sil</a></label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 col-sm-4 control-label">Adet:<span class="required" aria-required="true"> * </span></label>' +
                                '<div class="input-icon right col-xs-8 col-sm-6"><i class="fa"></i><input type="text" id="birimadet' + id + '" name="birimadet[]" class="form-control birimadet hide" value="'+(recete[i].MIKTAR)+'">' +
                                '<input type="text" id="adet' + id + '" name="adet[]" class="form-control adet valid'+id+'" value="'+((uretilecek>0 ? uretilecek : isemribilgi.KALANMIKTAR)*recete[i].MIKTAR)+'"></div>' +
                                '<label class="col-xs-1 hidden-xs hide"><a class="btn red satirsil">Sil</a></label></div>' +
                                '<div class="form-group col-xs-12 barkod_ek'+id+'"><input type="text" id="barkodcount'+id+'" name="barkodcount[]" class="form-control barkodcount'+id+' barkodcount hide" value="1"/><div class="form-group col-sm-6 col-xs-12">' +
                                '<label class="col-xs-4 control-label">Barkod:<span class="required" aria-required="true"> * </span></label>' +
                                '<div class="input-icon right col-xs-8"><i class="fa"></i><label style="padding-top: 6px" class="barkod'+id+'_0"></label>' +
                                '<select class="form-control select2me barkod hide valid'+id+'" id="barkod' + id + '" multiple=""  name="barkod['+id+'][]" tabindex="-1" title="">' +
                                '</select>' +
                                '<select class="form-control select2me olanbarkod olanbarkod' + id + ' hide" id="olanbarkod' + id + '" multiple=""  name="olanbarkod['+id+'][]" tabindex="-1" title="">' +
                                '</select></div></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">'+(recete[i].kalem.length>0 ? 'Eşleşen' : 'Ürün Kalan')+':</label><div class="col-xs-8"><label style="padding-top: 6px" class="barkodkalan'+id+'_0"></label></div></div></div>' +
                                '</div></div></div>';
                            $('.urunler').append(newRow);
                            var barkodlar = recete[i].barkodlar;
                            var muadiller = recete[i].muadiller;
                            $("#barkod" + (id)).select2();
                            $("#olanbarkod" + (id)).select2();
                            $("#depokodu" + (id)).select2();
                            var olanbarkod=$("#olanbarkod" + (id));
                            $("#adet" + (id)).inputmask("mask", { mask:"9",repeat:5,greedy:!1 });
                            if(barkodlar!=="" && barkodlar!==undefined ){
                                $.each(barkodlar, function (j) {
                                    olanbarkod.append('<option value="' +barkodlar[j].barkod+ '" data-id="'+barkodlar[j].id+'"  data-kalan="'+barkodlar[j].kalan+'"> ' + barkodlar[j].barkod + '</option>');
                                    olanbarkod.select2("val", olanbarkod.select2("val").concat(barkodlar[j].barkod));
                                });
                            }
                            if(muadiller!=="" && muadiller!==undefined ){
                                $.each(muadiller, function (j) {
                                    olanbarkod.append('<option value="' +muadiller[j].barkod+ '" data-id="'+muadiller[j].id+'"  data-kalan="'+muadiller[j].kalan+'"> ' + muadiller[j].barkod + '</option>');
                                    olanbarkod.select2("val", olanbarkod.select2("val").concat(muadiller[j].barkod));
                                });
                            }
                            $('select.valid' + (id)).each(function () {
                                $(this).rules('remove');
                                $(this).rules('add', {
                                    required: true
                                });
                            });
                            $('input.valid' + (id)).each(function () {
                                $(this).rules('remove');
                                $(this).rules('add', {
                                    required: true
                                });
                                $(this).valid();
                            });
                            $("#depokodu" + (id)).select2('val',depokodu).valid();
                        }
                        count+=recete.length;
                        $("#count").val(count);
                        $(".kaydet").prop('disabled', false);
                        $('select').on("select2-close", function () {
                            $(this).valid();
                        });
                    } else {
                        $('.serinouret').addClass('hide');
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            } else {
                while($('.urunler .urunler_ek').size()>0){
                    $('.urunler .urunler_ek:last').remove();
                    count--;
                }
                $("#eklibarkod").empty();
                barkodlar = $("#tumbarkod").select2("val");
                $.each(barkodlar, function (i) {
                    $('#eklibarkod').append('<option value="' +barkodlar[i]+ '"> ' + barkodlar[i] + '</option>');
                    $("#eklibarkod").select2("val", $("#eklibarkod").select2("val").concat(barkodlar[i]));
                });
            }
            $(this).valid();
        });

        $('#baslangic').on('change',function(){
            var baslangic=parseInt($(this).val());
            var bitis=parseInt($('#bitis').val());
            if(baslangic && bitis)
            {
                if(bitis<baslangic)
                {
                    $(".serisayi").html("Hesaplanamıyor");
                    $(".serigir").prop('disabled',true);
                }else{
                    var artis = parseInt($('#spinner').spinner('value'));
                    var sayi=((bitis-baslangic)/artis)+1;
                    $(".serisayi").html(sayi+' adet - '+baslangic+' ile '+bitis+' arasındaki serinolar');
                    $(".serigir").prop('disabled',false);
                }
            }
        });

        $('#bitis').on('change',function(){
            var baslangic=parseInt($('#baslangic').val());
            var bitis=parseInt($(this).val());
            if(baslangic && bitis)
            {
                if(bitis<baslangic)
                {
                    $(".serisayi").html("Hesaplanamıyor");
                    $(".serigir").prop('disabled',true);
                }else{
                    var artis = $('#spinner').spinner('value');
                    var sayi=((bitis-baslangic)/artis)+1;
                    $(".serisayi").html(sayi+' adet - '+baslangic+' ile '+bitis+' arasındaki serinolar');
                    $(".serigir").prop('disabled',false);
                }
            }
        });

        $('#spinner').on('change',function(){
            var artis = $(this).spinner('value');
            var baslangic=parseInt($('#baslangic').val());
            var bitis=parseInt($('#bitis').val());
            if(baslangic && bitis)
            {
                if(bitis<baslangic)
                {
                    $(".serisayi").html("Hesaplanamıyor");
                    $(".serigir").prop('disabled',true);
                }else{
                    var sayi=((bitis-baslangic)/artis)+1;
                    $(".serisayi").html(sayi+' adet - '+baslangic+' ile '+bitis+' arasındaki serinolar');
                    $(".serigir").prop('disabled',false);
                }
            }
        });

        $('.serigir').click(function(){
            var artis = $('#spinner').spinner('value');
            var baslangic=parseInt($('#baslangic').val());
            var bitis=parseInt($('#bitis').val());
            var sayi=((bitis-baslangic)/artis)+1;
            var serino = $("#serino");
            var uretilecek=parseInt($("#uretilecek").val());
            count=parseInt($("#count").val());
            var ayni = 0;
            for(var i = 0;i<sayi;i++){
                var eklenecek =  baslangic+(artis*i);
                var item = $("#serino").find("option[value="+eklenecek+"]");
                if(item.length===0){
                    serino.append('<option value="' +eklenecek + '"> ' + eklenecek + '</option>');
                    serino.select2("val", $("#serino").select2("val").concat(eklenecek)).valid();
                }else{
                    ayni++;
                }
            }
            uretilecek = uretilecek+sayi-ayni;
            $('.uretilecek').text(uretilecek+' Adet');
            $('#uretilecek').val(uretilecek);
            $(".seritemizle").removeClass('hide');
            $(".seriesle").removeClass('hide');
            $('#baslangic').val('');
            $('#bitis').val('');
            $('#spinner').val(1);
            $(".serisayi").html("");
            for (i=0;i<count;i++){
                var birimadet = parseInt($('#birimadet'+i).val());
                $('#adet'+i).val(birimadet*uretilecek);
            }
        });

        $('.seriuret').click(function(){
            var adet=parseInt($('#adet').val());
            var serino = $("#serino");
            var uretilecek=parseInt($("#uretilecek").val());
            var uretilen=$("#sonuretilen").val();
            var lastcount = uretilen.substr(6,5);
            count=parseInt($("#count").val());
            var ayni = 0;
            for(var i = 0;i<adet;i++){
                var eklenecek =  uretilen.substr(0,6)+('00000'+(parseInt(lastcount)+i)).slice(-5);
                var item = $("#serino").find("option[value="+eklenecek+"]");
                if(item.length===0){
                    serino.append('<option value="' +eklenecek + '">' + eklenecek + '</option>');
                    serino.select2("val", $("#serino").select2("val").concat(eklenecek)).valid();
                }else{
                    ayni++;
                }
            }
            uretilecek = uretilecek+adet-ayni;
            $('.uretilecek').text(uretilecek+' Adet');
            $('#uretilecek').val(uretilecek);
            $(".seritemizle").removeClass('hide');
            $(".seriesle").removeClass('hide');
            $('#adet').val('');
            for (i=0;i<count;i++){
                var birimadet = parseInt($('#birimadet'+i).val());
                $('#adet'+i).val(birimadet*uretilecek);
            }
        });

        $('.seritemizle').click(function(){
            var serino = $("#serino");
            serino.empty();
            serino.select2("val", "").valid();
            count=parseInt($("#count").val());
            var kalan=parseInt($('.kalan').text());
            $('.uretilecek').text('0 Adet');
            $('#uretilecek').val(0);
            $(".seritemizle").addClass('hide');
            $(".seriesle").addClass('hide');
            for (var i=0;i<count;i++){
                var birimadet = parseInt($('#birimadet'+i).val());
                $('#adet'+i).val(birimadet*kalan);
            }
        });

        $('.serinoekle').click(function(){
            $("#serigir").prop('disabled',true);
        });

        $('#cikisdepo').on('change', function () {
            var depokodu = $(this).val();
            count=parseInt($("#count").val());
            for(var i=0;i<count;i++){
                $('#depokodu'+i).select2('val',depokodu).valid();
            }
        });

        $('.seriesle').click(function () {
            var serino = $("#serino").select2("val");
            var urunler = $(".no");
            $.each(urunler, function () {
                var no = $(this).closest('.urunler_ek').children('.no').val();
                var olanbarkod = $('#olanbarkod'+no);
                var olanlar = olanbarkod.select2("val");
                $.each(olanlar, function (j) {
                    var barkod = $('#barkod'+no);
                    var barkodlar = barkod.select2("val");
                    if(!barkodlar.includes(olanlar[j])){
                        if(serino.includes(olanlar[j])){
                            var kalan = $('#olanbarkod'+no).find("option[value="+olanlar[j]+"]").data('kalan');
                            if(barkodlar.length!==0){
                                var adet = parseInt($('.barkodcount'+no).val());
                                if(kalan!=="undefined" && kalan!==""){
                                    var newBarkod = '<div class="form-group col-xs-6">' +
                                        '<label class="col-xs-4 control-label">Barkod:<span class="required" aria-required="true"> * </span></label>' +
                                        '<div class="input-icon right col-xs-8"><i class="fa"></i><label style="padding-top: 6px" class="barkod'+no+'_'+adet+'"></label></div></div>' +
                                        '<div class="form-group col-xs-6"><label class="col-xs-4 control-label">Ürün Kalan:</label><div class="col-xs-8"><label style="padding-top: 6px" class="barkodkalan'+no+'_'+adet+'"></label></div></div>';
                                    $('.barkod_ek'+no).append(newBarkod);
                                    $('.barkod'+no+'_'+adet).text(olanlar[j]);
                                    $('.barkodkalan'+no+'_'+adet).text(kalan);
                                }else{
                                    $('.barkod'+no+'_0').text($('.barkod'+no+'_0').text()+($('.barkod'+no+'_0').text()==="" ? "" : ", ")+olanlar[j]);
                                    $('.barkodkalan'+no+'_0').text(adet+1);
                                }
                                $('.barkodcount'+no).val(adet+1);
                            }else{
                                if(kalan!=="undefined" && kalan!==""){
                                    $('.barkod'+no+'_0').text(olanlar[j]);
                                    $('.barkodkalan'+no+'_0').text(1);
                                }else{
                                    $('.barkod'+no+'_0').text(olanlar[j]);
                                    $('.barkodkalan'+no+'_0').text(kalan);
                                }
                            }
                            barkod.append('<option value="' +olanlar[j]+ '"> ' + olanlar[j] + '</option>');
                            barkod.select2("val", barkod.select2("val").concat(olanlar[j])).valid();
                            $('#eklibarkod option[value='+olanlar[j]+']').remove();
                            $("#eklibarkod").select2("val");
                            $('#tumbarkod').append('<option value="' +olanlar[j]+ '"> ' + olanlar[j] + '</option>');
                            $("#tumbarkod").select2("val", $("#tumbarkod").select2("val").concat(olanlar[j]));
                        }
                    }
                });
            });
            $("#eklibarkod").select2();
        });

        $('#serino').on('change', function () {
            var serino = $(this).select2("val");
            var adet = serino.length;
            count=parseInt($("#count").val());
            $('.uretilecek').text(adet+' Adet');
            $('#uretilecek').val(adet);
            for(var i=0;i<count;i++){
                var birimadet = parseInt($('#birimadet'+i).val());
                $('#adet'+i).val(birimadet*adet);
            }
        });

        $('.barkodekle').click(function(){
            $(".barkodeklediv").addClass('hide');
            $(".barkodbilgi").removeClass('hide');
        });

        $('.eslestir').click(function () {
            var barkodlar = $("#eklibarkod").select2("val");
            var eklibarkod=$('.barkod');
            $.each(eklibarkod, function () {
                var no = $(this).closest('.urunler_ek').children('.no').val();
                var olanbarkod = $('#olanbarkod'+no);
                var olanlar = olanbarkod.select2("val");
                $.each(olanlar, function (j) {
                    if(barkodlar.includes(olanlar[j])){
                        var kalan = $('#olanbarkod'+no).find("option[value="+olanlar[j]+"]").data('kalan');

                        var barkod = $('#barkod'+no);
                        var urunbarkodlar = barkod.select2("val");
                        var item,uretilecek;
                        if(urunbarkodlar.length!==0){
                            var adet = parseInt($('.barkodcount'+no).val());
                            if(kalan!=="undefined" && kalan!==""){
                                var newBarkod = '<div class="form-group col-xs-6">' +
                                    '<label class="col-xs-4 control-label">Barkod:<span class="required" aria-required="true"> * </span></label>' +
                                    '<div class="input-icon right col-xs-8"><i class="fa"></i><label style="padding-top: 6px" class="barkod'+no+'_'+adet+'"></label></div></div>' +
                                    '<div class="form-group col-xs-6"><label class="col-xs-4 control-label">Ürün Kalan:</label><div class="col-xs-8"><label style="padding-top: 6px" class="barkodkalan'+no+'_'+adet+'"></label></div></div>';
                                $('.barkod_ek'+no).append(newBarkod);
                                $('.barkod'+no+'_'+adet).text(olanlar[j]);
                                $('.barkodkalan'+no+'_'+adet).text(kalan);
                            }else{
                                if(!barkodlar.includes(olanlar[j])) {
                                    $('.barkod' + no + '_0').text($('.barkod' + no + '_0').text() + ($('.barkod' + no + '_0').text() === "" ? "" : ", ") + olanlar[j]);
                                    $('.barkodkalan' + no + '_0').text(adet + 1);
                                }
                                item = $("#serino").find("option[value="+olanlar[j]+"]");
                                uretilecek = parseInt($('#uretilecek').val());
                                if(item.length===0){
                                    $("#serino").append('<option value="' +olanlar[j] + '"> ' + olanlar[j] + '</option>');
                                    $("#serino").select2("val", $("#serino").select2("val").concat(olanlar[j])).valid();
                                    $('.uretilecek').text((uretilecek+1)+' Adet');
                                    $('#uretilecek').val(uretilecek+1);
                                }
                            }
                            $('.barkodcount'+no).val(adet+1);
                        }else{
                            if(kalan!=="undefined" && kalan!==""){
                                $('.barkod'+no+'_0').text(olanlar[j]);
                                $('.barkodkalan'+no+'_0').text(1);
                            }else{
                                if(!barkodlar.includes(olanlar[j])) {
                                    $('.barkod' + no + '_0').text(olanlar[j]);
                                    $('.barkodkalan' + no + '_0').text(kalan);
                                }
                                item = $("#serino").find("option[value="+olanlar[j]+"]");
                                uretilecek = parseInt($('#uretilecek').val());
                                if(item.length===0){
                                    $("#serino").append('<option value="' +olanlar[j] + '"> ' + olanlar[j] + '</option>');
                                    $("#serino").select2("val", $("#serino").select2("val").concat(olanlar[j])).valid();
                                    $('.uretilecek').text((uretilecek+1)+' Adet');
                                    $('#uretilecek').val(uretilecek+1);
                                }
                            }
                        }
                        barkod.append('<option value="' +olanlar[j]+ '"> ' + olanlar[j] + '</option>');
                        barkod.select2("val", barkod.select2("val").concat(olanlar[j])).valid();
                        $('#eklibarkod option[value='+olanlar[j]+']').remove();
                        barkodlar = $("#eklibarkod").select2("val");

                    }
                });
            });
            $("#eklibarkod").select2();
        });

        $('.barkodguncelle').click(function () {
            var isemri = $('#isemri').val();
            $.blockUI();
            $.getJSON("{{ URL::to('uretim/urunrecete') }}", {isemri: isemri}, function (event) {
                if (event.durum) {
                    var recete = event.recete;
                    for(var i=0;i<recete.length;i++){
                        var barkodlar = recete[i].barkodlar;
                        var muadiller = recete[i].muadiller;
                        var olanbarkod=$("#olanbarkod" + (i));
                        olanbarkod.empty();
                        if(barkodlar!=="" && barkodlar!==undefined ){
                            $.each(barkodlar, function (j) {
                                olanbarkod.append('<option value="' +barkodlar[j].barkod+ '" data-id="'+barkodlar[j].id+'"  data-kalan="'+barkodlar[j].kalan+'"> ' + barkodlar[j].barkod + '</option>');
                                olanbarkod.select2("val", olanbarkod.select2("val").concat(barkodlar[j].barkod));
                            });
                        }
                        if(muadiller!=="" && muadiller!==undefined ){
                            $.each(muadiller, function (j) {
                                olanbarkod.append('<option value="' +muadiller[j].barkod+ '" data-id="'+muadiller[j].id+'"  data-kalan="'+muadiller[j].kalan+'"> ' + muadiller[j].barkod + '</option>');
                                olanbarkod.select2("val", olanbarkod.select2("val").concat(muadiller[j].barkod));
                            });
                        }
                    }
                } else {
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
        });

        var isemri = $('#isemri').val();
        if (isemri !== "") {
            $.blockUI();
            $.getJSON("{{ URL::to('uretim/urunrecete') }}", {isemri: isemri}, function (event) {
                if (event.durum) {
                    var recete = event.recete;
                    var isemribilgi=event.isemri;
                    $('.tarih').text(isemribilgi.TARIH);
                    $('.teslimtarihi').text(isemribilgi.TESLIM_TARIHI);
                    $('.stokadi').text(isemribilgi.STOK_KODU+' - '+isemribilgi.STOK_ADI);
                    $('.miktar').text(isemribilgi.MIKTAR);
                    $('.uretilen').text(isemribilgi.URETILENMIKTAR);
                    $('.kalan').text(isemribilgi.KALANMIKTAR);
                    $('.siparisno').text(isemribilgi.SIPARIS_NO==null ? '' : isemribilgi.SIPARIS_NO );
                    $('#miktar').val(isemribilgi.MIKTAR);
                    $('#uretilen').val(isemribilgi.URETILENMIKTAR);
                    $('#kalan').val(isemribilgi.KALANMIKTAR);
                    for(var i=0;i<recete.length;i++){
                        var id=i;
                        var barkodlar = recete[i].barkodlar;
                        var muadiller = recete[i].muadiller;
                        $("#barkod" + (id)).select2();
                        $("#olanbarkod" + (id)).select2();
                        $("#depokodu" + (id)).select2();
                        var olanbarkod=$("#olanbarkod" + (id));
                        $("#adet" + (id)).inputmask("mask", { mask:"9",repeat:5,greedy:!1 });
                        if(barkodlar!=="" && barkodlar!==undefined ){
                            $.each(barkodlar, function (j) {
                                olanbarkod.append('<option value="' +barkodlar[j].barkod+ '" data-id="'+barkodlar[j].id+'"  data-kalan="'+barkodlar[j].kalan+'"> ' + barkodlar[j].barkod + '</option>');
                                olanbarkod.select2("val", olanbarkod.select2("val").concat(barkodlar[j].barkod));
                            });
                        }
                        if(muadiller!=="" && muadiller!==undefined ){
                            $.each(muadiller, function (j) {
                                olanbarkod.append('<option value="' +muadiller[j].barkod+ '" data-id="'+muadiller[j].id+'"  data-kalan="'+muadiller[j].kalan+'"> ' + muadiller[j].barkod + '</option>');
                                olanbarkod.select2("val", olanbarkod.select2("val").concat(muadiller[j].barkod));
                            });
                        }
                        var eklibarkodlar;
                        if($( "#barkodlar"+(id)).hasClass( "barkodlar" ))
                        {
                            eklibarkodlar =document.getElementById("barkodlar"+(id)).innerHTML;
                            eklibarkodlar = eklibarkodlar.replace(/\s+/g,' ').trim();
                            eklibarkodlar = eklibarkodlar.split(" ");
                            var k;
                            if(recete[i].kalem.length>0){
                                if(eklibarkodlar[0]!==""){
                                    k = 0;
                                    $.each(eklibarkodlar, function (j) {
                                        if(eklibarkodlar!=="") {
                                            $('.barkod'+id+'_0').text($('.barkod'+id+'_0').text()+($('.barkod'+id+'_0').text()==="" ? "" : ", ")+eklibarkodlar[j]);
                                        }
                                    });
                                    var adet = parseInt($('.barkodcount'+id).val());
                                    $('.barkodkalan'+id+'_0').text(adet);
                                    $("#barkod" + (id)).select2("val",eklibarkodlar);
                                }
                            }else{
                                if(eklibarkodlar[0]!==""){
                                    k = 0;
                                    $.each(eklibarkodlar, function (j) {
                                        if(eklibarkodlar!=="") {
                                            $("#barkod" + (id)).append('<option value="' + eklibarkodlar[j] + '"> ' + eklibarkodlar[j] + '</option>');
                                            var kalan = $('#olanbarkod' + id).find("option[value=" + eklibarkodlar[j] + "]").data('kalan');
                                            $('.barkod' + id + '_' + k).text(eklibarkodlar[j]);
                                            $('.barkodkalan' + id + '_' + k).text(kalan);
                                            k++;
                                        }
                                    });
                                    $("#barkod" + (id)).select2("val",eklibarkodlar);
                                }
                            }
                        }
                        $('select.valid' + (id)).each(function () {
                            $(this).rules('remove');
                            $(this).rules('add', {
                                required: true
                            });
                        });
                        $('input.valid' + (id)).each(function () {
                            $(this).rules('remove');
                            $(this).rules('add', {
                                required: true
                            });
                            $(this).valid();
                        });
                    }
                    $('select').on("select2-close", function () {
                        $(this).valid();
                    });
                } else {
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
        }
        if (count > 0) {
            if (flag === 0)
                $(".kaydet").prop('disabled', false);
            else
                $(".kaydet").prop('disabled', true);
        }else{
            $(".kaydet").prop('disabled', true);
        }

        $('#kaydet').click(function () {
            $.blockUI();
            var depokodu = $('select.depokodu');
            var stokkodu = $('.stokkodu');
            var adet = $('.adet');
            var depokodlari = [];
            var stokkodlari = [];
            var adetler = [];
            $.each(depokodu, function () {
                depokodlari.push($(this).select2('val'));
            });
            $.each(stokkodu, function () {
                stokkodlari.push($(this).val());
            });
            $.each(adet, function () {
                adetler.push($(this).val());
            });
            $.getJSON("{{ URL::to('uretim/recetestokkontrol') }}", {stokkodlari: stokkodlari,depokodlari:depokodlari,adetler:adetler}, function (event) {
                if (event.durum) {
                    $('confirm_body').html('Girilen Üretim Sonu Kayıdı Bilgileri Kaydedilecektir?');
                    $('.confirm_header').html('Üretim Sonu Kayıdı Tamamlanacak');
                    $("#formsubmit").prop('disabled', false);
                } else {
                    toastr[event.type](event.text, event.title);
                    var eksikkodlar = event.eksikkodlar;
                    var eksikadetler = event.eksikadetler;
                    var eksik = "";
                    $.each(eksikkodlar,function (i) {
                        eksik += '<div class="form-group col-xs-12"><div class="col-sm-6 col-xs-12">' +
                            '<label class="col-xs-4 control-label">Stok Kodu:</label>' +
                            '<label class="col-xs-8" style="padding-top: 6px">'+eksikkodlar[i]+'</label></div>' +
                            '<div class="col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Eksik:</label>' +
                            '<label class="col-xs-8" style="padding-top: 6px">'+eksikadetler[i]+'</label></div></div>';
                    });
                    $('.confirm_body').html(eksik);
                    $('.confirm_header').html('Depoya Ait Bakiyelerde Üretim Sonu Kaydı için Eksik Var');
                    $("#formsubmit").prop('disabled', true);
                }
                $('#confirm').modal('show');
                $.unblockUI();
            });
        });

        $('.kameraac').click(function () {
            QuaggaApp.init();
            $('.kamerakapat').removeClass('hide');
            $('.kameraac').addClass('hide');
            $('#container').removeClass('hide');
            $('.controls').removeClass('hide');
        });

        $('.kamerakapat').click(function () {
            Quagga.stop();
            $('.kamerakapat').addClass('hide');
            $('.kameraac').removeClass('hide');
            $('#container').addClass('hide');
            $('.controls').addClass('hide');
        });

        $('#formsubmit').click(function () {
            $('#form_sample').submit();
        });
        $("select").on("select2-close", function () { $(this).valid(); });
    });
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Üretim Sonu Kayıdı Ekle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('uretim/uretimsonukayitekle') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-sm-2 col-xs-4">İş Emri Numarası:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="isemri" name="isemri" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($acikisemirleri as $isemri)
                                @if(Input::old('isemri')==$isemri->ISEMRINO )
                                    <option value="{{ $isemri->ISEMRINO }}" selected>{{ $isemri->ISEMRINO . ' - ' . $isemri->TARIH . ' Tarihli - ' . $isemri->MIKTAR .' Adet - ' .$isemri->STOK_KODU . ' - ' .$isemri->STOK_ADI . ' - ' . $isemri->CARI_ISIM }}</option>
                                @else
                                    <option value="{{ $isemri->ISEMRINO }}">{{ $isemri->ISEMRINO . ' - ' . $isemri->TARIH . ' Tarihli - ' . $isemri->MIKTAR .' Adet - ' .$isemri->STOK_KODU . ' - ' . $isemri->STOK_ADI . ' - ' . $isemri->CARI_ISIM }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-xs-12">
                        <label class="control-label col-sm-2 col-xs-4">Stok Adı:</label>
                        <label class="col-xs-8 stokadi" style="padding-top: 7px"></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Miktar:</label>
                        <label class="col-xs-8 miktar" style="padding-top: 7px"></label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Sipariş No:</label>
                        <label class="col-xs-8 siparisno" style="padding-top: 7px"></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Üretilen:</label>
                        <label class="col-xs-8 uretilen" style="padding-top: 7px"></label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Kalan:</label>
                        <label class="col-xs-8 kalan" style="padding-top: 7px"></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Açılma Tarihi:</label>
                        <label class="col-xs-8 tarih" style="padding-top: 7px"></label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Teslim Tarihi :</label>
                        <label class="col-xs-8 teslimtarihi" style="padding-top: 7px"></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="col-xs-4 control-label">Çıkış Depo:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i>
                            <select class="form-control select2me cikisdepo" id="cikisdepo" name="cikisdepo" tabindex="-1" title="">
                                <option value="">Seçiniz...</option>
                                @foreach($netsisdepolar as $depo)
                                    @if(Input::old('cikisdepo')==$depo->kodu )
                                        <option value="{{ $depo->kodu }}" selected>{{ $depo->kodu . ' - ' . $depo->adi }}</option>
                                    @else
                                        <option value="{{ $depo->kodu }}">{{ $depo->kodu . ' - ' . $depo->adi }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="col-xs-4 control-label">Giriş Depo:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i>
                            <select class="form-control select2me girisdepo" id="girisdepo" name="girisdepo" tabindex="-1" title="">
                                <option value="">Seçiniz...</option>
                                @foreach($netsisdepolar as $depo)
                                    @if(Input::old('girisdepo')==$depo->kodu )
                                        <option value="{{ $depo->kodu }}" selected>{{ $depo->kodu . ' - ' . $depo->adi }}</option>
                                    @else
                                        <option value="{{ $depo->kodu }}">{{ $depo->kodu . ' - ' . $depo->adi }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4">Seri Numaraları:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-8">
                        <i class="fa"></i>
                        <select class="form-control select2 select2-offscreen" multiple="" tabindex="-1" id="serino" name="serino[]">
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-2 control-label" style="text-align: left;padding-top:0">
                        <a class="btn green serinoekle" data-toggle="modal" data-target="#serinoekle">SeriNo Ekle </a>
                        <a class="btn blue serinouret hide" data-toggle="modal" data-target="#serinouretim">SeriNo Üret </a>
                        <a class="btn red seritemizle hide" >SeriNo Temizle </a>
                        <a class="btn yellow seriesle hide" >SeriNo Eşleştir </a>
                    </div>
                </div>
                <div id="serinolar" class="hide serinolar">
                    @if(Input::old('serino'))
                        @foreach(Input::old('serino') as $serino)
                            {{$serino}}
                        @endforeach
                    @endif
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Üretilecek Miktar:</label>
                        <label class="col-xs-8 uretilecek" style="padding-top: 7px">{{Input::old('uretilecek') ? Input::old('uretilecek') : 0}} Adet</label>
                    </div>
                </div>
                <h4 class="form-section col-sm-12 ">Reçete Bilgisi</h4>

                <div class="form-group col-xs-12 hide">
                    <input type="text" id="count" name="count" value="{{Input::old('count') ? Input::old('count') : 0}}" data-required="1" class="form-control">
                    <input type="text" id="miktar" name="miktar" value="{{Input::old('miktar') ? Input::old('miktar') : 0}}" data-required="1" class="form-control">
                    <input type="text" id="uretilen" name="uretilen" value="{{Input::old('uretilen') ? Input::old('uretilen') : 0}}" data-required="1" class="form-control">
                    <input type="text" id="kalan" name="kalan" value="{{Input::old('kalan') ? Input::old('kalan') : 0}}" data-required="1" class="form-control">
                    <input type="text" id="uretilecek" name="uretilecek" class="form-control" value="{{Input::old('uretilecek') ? Input::old('uretilecek') : 0}}">
                    <input type="text" id="sonuretilen" name="sonuretilen" class="form-control" value="{{Input::old('sonuretilen') ? Input::old('sonuretilen') : 0}}">
                </div>
                <div class="panel-group accordion urunler col-xs-12" id="accordion1">
                    @if(Input::old('count')!="0")
                        @for($i=0;$i<(int)(Input::old('count'));$i++)
                            <div class="panel panel-default urunler_ek">
                                <input class="no hide" value="{{$i}}"/>
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_{{$i}}">
                                            {{Input::old('stokkodu.'.$i)}} -  {{Input::old('stokadi.'.$i)}}
                                        </a>
                                    </h4>
                                    <input name="stokkodu[]" class="stokkodu{{$i}} stokkodu hide" value="{{Input::old('stokkodu.'.$i)}}"/>
                                    <input name="stokadi[]" class="stokadi{{$i}} hide" value="{{Input::old('stokadi.'.$i)}}"/>
                                    <input name="alturun[]" class="alturun{{$i}} hide" value="{{Input::old('alturun.'.$i)}}"/>
                                </div>
                                <div id="collapse_{{$i}}" class="panel-collapse in">
                                    <div class="panel-body">
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="col-xs-4 col-sm-4 control-label">Depo Kodu:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8 col-sm-6">
                                                <i class="fa"></i>
                                                <select class="form-control select2me valid{{$i}} depokodu depokodu{{$i}}" id="depokodu{{$i}}" name="depokodu[]" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                    @foreach($netsisdepolar as $depo)
                                                        @if(Input::old('depokodu.'.$i)==$depo->kodu)
                                                            <option value="{{ $depo->kodu }}" selected>{{ $depo->kodu . ' - ' . $depo->adi }}</option>
                                                        @else
                                                            <option value="{{ $depo->kodu }}">{{ $depo->kodu . ' - ' . $depo->adi }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <label class="col-xs-1 hidden-sm hidden-md hidden-lg hide"><a class="btn red satirsil">Sil</a></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="col-xs-4 col-sm-4 control-label">Adet:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8 col-sm-6">
                                                <i class="fa"></i>
                                                <input type="text" id="birimadet{{$i}}" name="birimadet[]" class="form-control birimadet hide" value="{{Input::old('birimadet.'.$i)}}">
                                                <input type="text" id="adet{{$i}}" name="adet[]" class="form-control adet valid{{$i}}" value="{{Input::old('adet.'.$i)}}">
                                            </div>
                                            <label class="col-xs-1 hidden-xs hide"><a class="btn red satirsil">Sil</a></label>
                                        </div>
                                        <div class="form-group col-xs-12 barkod_ek{{$i}}">
                                            <input type="text" id="barkodcount{{$i}}" name="barkodcount[]" class="form-control barkodcount{{$i}} barkodcount hide" value="{{Input::old('barkodcount.'.$i)}}"/>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="col-xs-4 control-label">Barkod:<span class="required" aria-required="true"> * </span></label>
                                                <div class="input-icon right col-xs-8">
                                                    <i class="fa"></i>
                                                    <label style="padding-top: 6px" class="barkod{{$i}}_0"></label>
                                                    <select class="form-control select2me barkod valid{{$i}} hide" id="barkod{{$i}}" multiple=""  name="barkod[{{$i}}][]" tabindex="-1" title="">
                                                    </select>
                                                    <select class="form-control select2me olanbarkod olanbarkod{{$i}} hide" id="olanbarkod{{$i}}" multiple=""  name="olanbarkod[{{$i}}][]" tabindex="-1" title="">
                                                    </select>
                                                </div>
                                                <div id="barkodlar{{$i}}" class="hide barkodlar">
                                                    @if(Input::old('barkod.'.$i))
                                                        @foreach(Input::old('barkod.'.$i) as $barkod)
                                                            {{$barkod}}
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="col-xs-4 control-label">{{intval(Input::old('alturun.'.$i))==1 ? 'Eşleşen' : 'Ürün Kalan'}}:</label>
                                                <div class="col-xs-8">
                                                    <label style="padding-top: 6px" class="barkodkalan{{$i}}_0"></label>
                                                </div>
                                            </div>
                                            @if(intval(Input::old('alturun.'.$i))==0 && Input::old('barkodcount.'.$i)>1)
                                                @for($j=1;$j<intval(Input::old('barkodcount.'.$i));$j++)
                                                    <div class="form-group col-sm-6 col-xs-12">
                                                        <label class="col-xs-4 control-label">Barkod:<span class="required" aria-required="true"> * </span></label>
                                                        <div class="input-icon right col-xs-8">
                                                            <i class="fa"></i>
                                                            <label style="padding-top: 6px" class="barkod{{$i}}_{{$j}}"></label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-6 col-xs-12">
                                                        <label class="col-xs-4 control-label">Ürün Kalan:</label>
                                                        <div class="col-xs-8">
                                                            <label style="padding-top: 6px" class="barkodkalan{{$i}}_{{$j}}"></label>
                                                        </div>
                                                    </div>
                                                @endfor
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    @endif
                </div>
                <h4 class="form-section col-sm-12 ">Barkod Bilgisi</h4>
                <div class="form-group">
                    <div class="col-sm-6 col-xs-12 control-label barkodeklediv {{Input::old('tumbarkod')!="" ? 'hide' : ''}}" style="text-align: left;">
                        <a class="btn green barkodekle">Barkod Ekle </a>
                    </div>
                    <div class="form-group barkodbilgi {{Input::old('tumbarkod')!="" ? '' : 'hide'}}">
                        <div class="form-group col-xs-12">
                            <label class="control-label col-sm-2 col-xs-4">Barkodlar:</label>
                            <div class="col-sm-6 col-xs-8">
                                <select class="form-control select2 select2-offscreen" multiple="" tabindex="-1" id="eklibarkod" name="eklibarkod[]">
                                </select>
                            </div>
                            <div class="col-xs-6 hide">
                                <select class="form-control select2 select2-offscreen" multiple="" tabindex="-1" id="tumbarkod" name="tumbarkod[]">
                                </select>
                            </div>
                            <div class="col-sm-4 col-xs-12 control-label" style="text-align: left;padding-top:0;padding-left:30px">
                                <a class="btn btn-lg yellow eslestir" >Barkod Eşleştir</a>
                                <a class="btn btn-lg blue barkodguncelle" >Ürün Barkod Güncelle</a>
                            </div>
                            <div class="col-xs-4 control-label hidden-sm hidden-lg hidden-md" style="text-align: left;padding-top:0;padding-left:30px">
                                <a class="btn btn-lg red kameraac" >Kamera Aç</a>
                                <button class="btn btn-lg black kamerakapat hide">Kamera Kapat</button>
                            </div>
                            <div class="form-group controls col-sm-6 col-xs-12 hide">
                                <fieldset class="reader-config-group">
                                    <label class="hide">
                                        <span>Barcode-Type</span>
                                        <select name="decoder_readers">
                                            <option value="code_128" selected="selected">Code 128</option>
                                            <option value="code_39">Code 39</option>
                                            <option value="code_39_vin">Code 39 VIN</option>
                                            <option value="ean">EAN</option>
                                            <option value="ean_extended">EAN-extended</option>
                                            <option value="ean_8">EAN-8</option>
                                            <option value="upc">UPC</option>
                                            <option value="upc_e">UPC-E</option>
                                            <option value="codabar">Codabar</option>
                                            <option value="i2of5">Interleaved 2 of 5</option>
                                            <option value="2of5">Standard 2 of 5</option>
                                            <option value="code_93">Code 93</option>
                                        </select>
                                    </label>
                                    <label class="hide">
                                        <span>Resolution (width)</span>
                                        <select name="input-stream_constraints">
                                            <option value="320x240">320px</option>
                                            <option selected="selected" value="640x480">640px</option>
                                            <option value="800x600">800px</option>
                                            <option value="1280x720">1280px</option>
                                            <option value="1600x960">1600px</option>
                                            <option value="1920x1080">1920px</option>
                                        </select>
                                    </label>
                                    <label class="hide">
                                        <span>Patch-Size</span>
                                        <select name="locator_patch-size">
                                            <option value="x-small">x-small</option>
                                            <option value="small">small</option>
                                            <option selected="selected" value="medium">medium</option>
                                            <option value="large">large</option>
                                            <option value="x-large">x-large</option>
                                        </select>
                                    </label>
                                    <label class="hide">
                                        <span>Half-Sample</span>
                                        <input type="checkbox" checked="checked" name="locator_half-sample" />
                                    </label>
                                    <label class="hide">
                                        <span>Workers</span>
                                        <select name="numOfWorkers">
                                            <option value="0">0</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option selected="selected" value="4">4</option>
                                            <option value="8">8</option>
                                        </select>
                                    </label>
                                    <div class="form-group col-xs-12">
                                        <label class="col-xs-4 control-label">Kamera:</label>
                                        <div class="col-xs-8">
                                            <select class="form-control select2me" id="deviceSelection" name="input-stream_constraints" tabindex="-1" title="">
                                            </select>
                                        </div>
                                    </div>
                                    <label style="display: none" class="hide">
                                        <span>Zoom</span>
                                        <select name="settings_zoom"></select>
                                    </label>
                                    <label style="display: none" class="hide">
                                        <span>Torch</span>
                                        <input type="checkbox" name="settings_torch" />
                                    </label>
                                </fieldset>
                            </div>
                        </div>
                        <div id="container" class="form-group col-xs-12 hide" style="padding-top:0;padding-left:30px">
                            <div id="interactive" class="viewport" style="display: block;margin-left: auto;margin-right: auto"></div>

                            <div id="result_strip hide">
                                <ul class="thumbnails"></ul>
                                <ul class="collector"></ul>
                            </div>
                        </div>
                        <div id="eklibarkodlar" class="hide eklibarkodlar">
                            @if(Input::old('eklibarkod'))
                                @foreach(Input::old('eklibarkod') as $barkod)
                                    {{$barkod}}
                                @endforeach
                            @endif
                        </div>
                        <div id="tumbarkodlar" class="hide tumbarkodlar">
                            @if(Input::old('tumbarkod'))
                                @foreach(Input::old('tumbarkod') as $barkod)
                                    {{$barkod}}
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <button id="kaydet" type="button" class="btn green kaydet">Kaydet</button>
                        <a href="{{ URL::to('uretim/uretimsonukayit')}}" class="btn default">Vazgeç</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('modal')
    <div class="modal fade" id="confirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Üretim Sonu Kayıdı Tamamlanacak
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body col-xs-12">
                                        <h3 class="form-section confirm_header col-xs-12">Üretim Sonu Kayıdı Tamamlanacak</h3>
                                        <div class="form-group confirm_body col-xs-12">
                                            Girilen Üretim Sonu Kayıdı Bilgileri Kaydedilecektir?
                                        </div>
                                        <div class="form-actions col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-offset-4 col-xs-8">
                                                    <button id="formsubmit" type="button" class="btn green kaydet">Kaydet</button>
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
    <div class="modal fade" id="serinoekle" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Ürün Seri Numarası Ekle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_1" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body col-xs-12">
                                        <h3 class="form-section col-xs-12">Ürün Seri Numarası Girişi</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="col-xs-12">
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Seri No Başlangıç: <span class="required" aria-required="true"> * </span></label>
                                                <div class="input-icon right col-xs-8">
                                                    <i class="fa"></i><input type="text" id="baslangic" name="baslangic" value="" data-required="1" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Bitiş: <span class="required" aria-required="true"> * </span></label>
                                                <div class="input-icon right col-xs-8">
                                                    <i class="fa"></i><input type="text" id="bitis" name="bitis" value="" data-required="1" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12">
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Artış Miktarı: <span class="required" aria-required="true"> * </span></label>
                                                <div id="spinner" class="input-icon right col-xs-6">
                                                    <i class="fa"></i><div class="input-group input-small">
                                                        <input type="text" name="artis" value="{{ (Input::old('artis') ? Input::old('artis') : 1) }}" class="spinner-input form-control" maxlength="3" readonly="">
                                                        <div class="spinner-buttons input-group-btn btn-group-vertical">
                                                            <button type="button" class="btn spinner-up btn-xs blue">
                                                                <i class="fa fa-angle-up"></i>
                                                            </button>
                                                            <button type="button" class="btn spinner-down btn-xs blue">
                                                                <i class="fa fa-angle-down"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Seri Numarası Sayısı:</label>
                                                <label class="col-xs-8 serisayi" style="margin-top: 9px;color: red">0</label>
                                            </div>
                                        </div>
                                        <div class="form-actions col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-offset-3 col-xs-9">
                                                    <button type="button" class="btn green serigir" data-dismiss="modal" disabled>Ekle</button>
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
    <div class="modal fade" id="serinouretim" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Ürün Seri Numarası Üretme
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_1" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body col-xs-12">
                                        <h3 class="form-section col-xs-12">Ürün Seri Numarası Üretme</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="col-xs-12">
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Adet: <span class="required" aria-required="true"> * </span></label>
                                                <div class="input-icon right col-xs-8">
                                                    <i class="fa"></i><input type="text" id="adet" name="adet" value="" data-required="1" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-offset-3 col-xs-9">
                                                    <button type="button" class="btn green seriuret" data-dismiss="modal">Üret</button>
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
