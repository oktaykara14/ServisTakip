@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Ürün Kayıt <small>Düzenleme Ekranı</small></h1>
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
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.tr.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>

@stop

@section('page-script')
<script src="{{ URL::to('pages/uretim/form-validation-1.js') }}"></script>
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
    $(document).ready(function() {
        var flag=0;
        $(".kaydet").prop('disabled',true);
        var count=parseInt($("#count").val());
        $('.ekle').click(function() {
            var newRow = "";
            newRow += '<div class="panel panel-default urunler_ek"><input class="no hide" value="' + (count) + '"/><input class="adet hide" value="1"/><div class="panel-heading">' +
                '<h4 class="panel-title"><a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_' + count + '">Yeni</a>' +
                '</h4></div><div id="collapse_' + count + '" class="panel-collapse in"><div class="panel-body"><div class="form-group col-sm-6 col-xs-12">' +
                '<label class="col-xs-4 col-sm-4 control-label">Stok Kodu:<span class="required" aria-required="true"> * </span></label>' +
                '<div class="input-icon right col-xs-8 col-sm-6"><i class="fa"></i>' +
                '<select class="form-control select2me valid'+count+' stokkodu stokkodu' + count + '" id="stokkodu' + count + '" name="stokkodu[]" tabindex="-1" title="">' +
                '<option value="">Seçiniz...</option>' +
                '</select></div>' +
                '<label class="col-xs-1 hidden-xs"><a class="btn red satirsil">Sil</a></label></div>' +
                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 col-sm-4 control-label">Adet:<span class="required" aria-required="true"> * </span></label>' +
                '<div class="input-icon right col-xs-8 col-sm-6"><i class="fa"></i><input type="text" id="adet' + count + '" name="adet[]" class="form-control adet" ></div>' +
                '<label class="col-xs-1 hidden-sm hidden-lg hidden-md"><a class="btn red satirsil">Sil</a></label></div>' +
                '<div class="form-group col-xs-12">' +
                '<label class="col-sm-2 col-xs-4 control-label">Depo Kayıdı:<span class="required" aria-required="true"> * </span></label>' +
                '<div class="input-icon right col-sm-10 col-xs-8"><i class="fa"></i><select class="form-control select2me valid' + count + ' depokayidi depokayidi' + count + '" id="depokayidi' + count + '" name="depokayidi[]" tabindex="-1" title="">' +
                '<option value="">Seçiniz...</option></select></div></div>' +
                '<div class="form-group col-sm-6 col-xs-12">' +
                '<label class="col-xs-4 col-sm-4 control-label">Üretici:<span class="required" aria-required="true"> * </span></label>' +
                '<div class="input-icon right col-xs-8"><i class="fa"></i><select class="form-control select2me valid' + count + ' uretici uretici' + count + '" id="uretici' + count + '" name="uretici[]" tabindex="-1" title="">' +
                '<option value="">Seçiniz...</option></select></div></div>' +
                '<div class="form-group col-sm-6 col-xs-12">'+
                '<label class="col-xs-4 col-sm-4 control-label">Marka:<span class="required" aria-required="true"> * </span></label>'+
                '<div class="input-icon right col-xs-8"><i class="fa"></i><select class="form-control select2me valid' + count + ' marka marka' + count + '" id="marka' + count + '" name="marka[]" tabindex="-1" title="">' +
                '<option value="">Seçiniz...</option></select></div></div>'+
                '<div class="form-group col-sm-6 col-xs-12"><label class="control-label col-xs-4">Üretim Yılı: <span class="required" aria-required="true"> * </span></label>' +
                '<div class="input-icon right col-xs-8"><i class="fa"></i><div class="input-group input-medium date date-picker" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">' +
                '<input id="uretimtarih' + count + '" type="text" name="uretimtarih[]" class="form-control" value="">' +
                '<span class="input-group-btn"><button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button></span></div>' +
                '</div></div>' +
                '<div class="form-group col-xs-12">' +
                '<label class="control-label col-sm-2 col-xs-6">Diğer Bilgiler (Barkod vb.):</label>' +
                '<div class="col-sm-3 col-xs-6">' +
                '<input type="text" id="barkod1' + count + '" name="barkod1[]" class="form-control barkod1" value="">' +
                '</div><div class="col-sm-3 col-xs-6">' +
                '<input type="text" id="barkod2' + count + '" name="barkod2[]" class="form-control barkod2" value="">' +
                '</div><div class="col-sm-3 col-xs-6">' +
                '<input type="text" id="barkod3' + count + '" name="barkod3[]" class="form-control barkod3" value="">' +
                '</div></div>' +
                '<div class="form-group col-xs-6 col-xs-12">' +
                '<label class="col-xs-offset-4 col-xs-8" style="padding-top: 10px;"><input type="checkbox" id="muadil'+count+'" name="muadil[]" class="muadil muadil'+count+'" /> Muadil Olarak Kullanılacak </label></div>' +
                '<div class="form-group col-xs-6 col-xs-12 muadildurum'+count+' hide">' +
                '<label class="col-xs-4 col-sm-4 control-label">Hangi ürün için?:<span class="required" aria-required="true"> * </span></label>' +
                '<div class="input-icon right col-xs-8 col-sm-6"><i class="fa"></i>' +
                '<select class="form-control select2me muadilkodu muadilkodu' + count + '" id="muadilkodu' + count + '" name="muadilkodu[]" tabindex="-1" title="">' +
                '<option value="">Seçiniz...</option>' +
                '</select><input type="text" id="muadilkod'+count+'" name="muadilkod[]" class="form-control muadilkod hide" value="">' +
                '</div></div></div>';
            count++;
            $('.count').html(count + ' Adet');
            $('.urunler').append(newRow);
            $('select.valid' + (count - 1)).each(function () {
                $(this).rules('remove');
                $(this).rules('add', {
                    required: true
                });
            });
            $('input.valid' + (count - 1)).each(function () {
                $(this).rules('remove');
                $(this).rules('add', {
                    required: true
                });
            });
            $("#stokkodu" + (count - 1)).select2();
            $("#muadilkodu" + (count - 1)).select2();
            $("#uretici" + (count - 1)).select2();
            $("#marka" + (count - 1)).select2();
            $("#depokayidi" + (count - 1)).select2();
            $('#muadil' + (count - 1)).prop('checked',false);
            $.uniform.update();
            $("#adet" + (count - 1)).inputmask("mask", {mask: "9", repeat: 8, greedy: !1});
            $("#uretimtarih" + (count - 1)).datepicker({
                rtl: Metronic.isRTL(),
                orientation: "left",
                autoclose: true,
                language: 'tr'
            });
            $("#uretimtarih" + (count - 1)).inputmask("d-m-y",{ "placeholder": "*" ,removeMaskOnSubmit: false});
            $.blockUI();
            $.getJSON("{{ URL::to('uretim/urunkayitliste') }}", {}, function (event) {
                if (event.durum) {
                    var netsisstokkodlari = event.netsisstokkodlari;
                    var ureticiler = event.ureticiler;
                    var markalar = event.markalar;
                    var stokkodu = $("#stokkodu" + (count - 1));
                    stokkodu.empty();
                    stokkodu.append('<option value="">Seçiniz...</option>');
                    $.each(netsisstokkodlari, function (index) {
                        stokkodu.append('<option value="' + netsisstokkodlari[index].id + '"> ' + netsisstokkodlari[index].kodu + ' - ' + netsisstokkodlari[index].adi + '</option>');
                    });
                    stokkodu.select2("val", "");
                    var muadilkodu = $("#muadilkodu" + (count - 1));
                    muadilkodu.empty();
                    muadilkodu.append('<option value="">Seçiniz...</option>');
                    $.each(netsisstokkodlari, function (index) {
                        muadilkodu.append('<option value="' + netsisstokkodlari[index].id + '"> ' + netsisstokkodlari[index].kodu + ' - ' + netsisstokkodlari[index].adi + '</option>');
                    });
                    muadilkodu.select2("val", "");
                    var uretici = $("#uretici" + (count - 1));
                    uretici.empty();
                    uretici.append('<option value="">Seçiniz...</option>');
                    $.each(ureticiler, function (index) {
                        uretici.append('<option value="' + ureticiler[index].id + '"> ' + ureticiler[index].ureticiadi + '</option>');
                    });
                    uretici.select2("val", "");
                    var marka = $("#marka" + (count - 1));
                    marka.empty();
                    marka.append('<option value="">Seçiniz...</option>');
                    $.each(markalar, function (index) {
                        marka.append('<option value="' + markalar[index].id + '"> ' + markalar[index].markaadi + '</option>');
                    });
                    marka.select2("val", "");
                } else {
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
            $('.stokkodu').on('change', function () {
                var id = $(this).val();
                var no = $(this).closest('.urunler_ek').children('.no').val();
                var uretici = $("#uretici" + (no));
                var marka = $("#marka" + (no));
                var depokayidi = $("#depokayidi" + (no));
                if (id !== "") {
                    $.blockUI();
                    $.getJSON("{{ URL::to('uretim/ureticistokkod') }}", {stokkod: id}, function (event) {
                        if (event.durum) {
                            var depokayitlari = event.depokayitlari;
                            depokayidi.empty();
                            depokayidi.append('<option value="">Seçiniz...</option>');
                            $.each(depokayitlari, function (index) {
                                if((depokayitlari[index].FISNO)==null){
                                    depokayidi.append('<option data-sipnum="' + depokayitlari[index].STHAR_SIPNUM + '" data-fisno="' + depokayitlari[index].FISNO + '" ' +
                                        'data-adet="' + depokayitlari[index].STHAR_GCMIK + '" data-cari="' + depokayitlari[index].STHAR_CARIKOD + '" ' +
                                        'value="' + depokayitlari[index].INCKEYNO + '"> ' + depokayitlari[index].STHAR_ACIKLAMA + ' - ' + depokayitlari[index].STHAR_TARIH + ' Tarihli - ' +
                                        depokayitlari[index].STHAR_GCMIK + ' Adet </option>');
                                }else{
                                    depokayidi.append('<option data-sipnum="' + depokayitlari[index].STHAR_SIPNUM + '" data-fisno="' + depokayitlari[index].FISNO + '" ' +
                                        'data-adet="' + depokayitlari[index].STHAR_GCMIK + '" data-cari="' + depokayitlari[index].STHAR_CARIKOD + '" ' +
                                        'value="' + depokayitlari[index].INCKEYNO + '"> ' + depokayitlari[index].FISNO + ' - ' + depokayitlari[index].STHAR_TARIH + ' Tarihli - ' +
                                        depokayitlari[index].STHAR_GCMIK + ' Adet - ' + depokayitlari[index].STHAR_CARIKOD + ' - ' + depokayitlari[index].CARI_ISIM + ' </option>');
                                }
                            });
                            depokayidi.select2("val", "");
                            $("#muadilkodu"+no+">option").prop('disabled',false);
                            $("#muadilkodu"+no+">option[value='"+id+"']").prop('disabled',true);
                        } else {
                            toastr[event.type](event.text, event.title);
                        }
                        $.unblockUI();
                    });
                } else {
                    uretici.select2("val", "");
                    marka.select2("val", "");
                }
                $(this).valid();
            });
            $('.muadilkodu').on('change', function () {
                var id = $(this).val();
                var no = $(this).closest('.urunler_ek').children('.no').val();
                $('#muadilkod'+no).val(id);
            });
            $('.depokayidi').on('change', function () {
                var id=$(this).val();
                var no = $(this).closest('.urunler_ek').children('.no').val();
                var adet = $(this).find("option:selected").data('adet');
                $("#adet" + (no)).val(adet);
                var secilenler = $('#secilenler').val();
                secilenler+=(secilenler==="" ? "" : ",")+id;
                $('#secilenler').val(secilenler);
            });
            $('.muadil').on('change', function () {
                var no = $(this).closest('.urunler_ek').children('.no').val();
                if ($('.muadil'+no).attr('checked')) {
                    $('.muadildurum'+no).removeClass('hide');
                    $('#muadilkodu'+no).rules("add", "required");
                }else{
                    $('.muadildurum'+no).addClass('hide');
                    $('#muadilkodu'+no).rules("remove");
                }
            });
            $('.satirsil').click(function () {
                if ($('.urunler .urunler_ek').size() > 0) {
                    var urun = $(this).closest('.urunler_ek');
                    var adet = urun.children('.adet').val();
                    urun.children('.adet').val(0);
                    urun.remove();
                    count -= adet;
                    $("#count").val(count);
                    $('.count').html(count + ' Adet');
                    var j = 0;
                    $('.urunler .urunler_ek').each(function () {
                        var id = $(this).children('.no').val();
                        $(this).children('div').children('h4').children('.accordion-toggle').attr('href', '#collapse_' + j);
                        $(this).children('.panel-collapse').attr('id', 'collapse_' + j);
                        $(this).children('div').children('div').children('div').children('div').children('.stokkodu').removeClass('stokkodu' + id).addClass('stokkodu' + j);
                        $(this).children('div').children('div').children('div').children('div').children('.stokkodu').attr('id', 'stokkodu' + j).attr('name', 'stokkodu[]');
                        $(this).children('div').children('div').children('div').children('div').children('.adet').attr('id', 'adet' + j).attr('name', 'adet[]');
                        $(this).children('div').children('div').children('div').children('div').children('.depokayidi').removeClass('depokayidi' + id).addClass('depokayidi' + j);
                        $(this).children('div').children('div').children('div').children('div').children('.depokayidi').attr('id', depokayidi + j).attr('name', 'depokayidi[]');
                        $(this).children('div').children('div').children('div').children('div').children('.uretici').removeClass('uretici' + id).addClass('uretici' + j);
                        $(this).children('div').children('div').children('div').children('div').children('.uretici').attr('id', 'uretici' + j).attr('name', 'uretici[]');
                        $(this).children('div').children('div').children('div').children('div').children('.marka').removeClass('marka' + id).addClass('marka' + j);
                        $(this).children('div').children('div').children('div').children('div').children('.marka').attr('id', 'marka' + j).attr('name', 'marka[]');
                        $(this).children('div').children('div').children('div').children('div').children('.uretimtarih').attr('id', 'uretimtarih' + j).attr('name', 'uretimtarih[]');
                        $(this).children('div').children('div').children('div').children('div').children('.barkod1').attr('id', 'barkod1' + j).attr('name', 'barkod1[]');
                        $(this).children('div').children('div').children('div').children('div').children('.barkod2').attr('id', 'barkod2' + j).attr('name', 'barkod2[]');
                        $(this).children('div').children('div').children('div').children('div').children('.barkod3').attr('id', 'barkod3' + j).attr('name', 'barkod3[]');
                        $(this).children('div').children('div').children('div').children('div').children('.muadil').attr('id', 'muadil' + j).attr('name', 'muadil[]');
                        $(this).children('div').children('div').children('div').children('div').children('.muadil').removeClass('muadil' + id).addClass('muadil' + j);
                        $(this).children('div').children('div').children('div').children('div').children('.muadildurum'+id).removeClass('muadildurum' + id).addClass('muadildurum' + j);
                        $(this).children('div').children('div').children('div').children('div').children('.muadilkod').attr('id', 'muadilkod' + j).attr('name', 'muadilkod[]');
                        $(this).children('div').children('div').children('div').children('div').children('.muadilkodu').removeClass('muadilkodu' + id).addClass('muadilkodu' + j);
                        $(this).children('div').children('div').children('div').children('div').children('.muadilkodu').attr('id', 'muadilkodu' + j).attr('name', 'muadilkodu[]');
                        $(this).children('.no').val(j);
                        j++;
                    });
                    flag = 0;
                    if (count > 0) {
                        if (flag === 0)
                            $(".kaydet").prop('disabled', false);
                        else
                            $(".kaydet").prop('disabled', true);
                    } else {
                        $(".kaydet").prop('disabled', true);
                    }
                }
            });

            $("#count").val(count);
            if (count > 0) {
                $(".kaydet").prop('disabled', false);
            } else {
                $(".kaydet").prop('disabled', true);
            }
            $('select').on("select2-close", function () {
                $(this).valid();
            });
        });

        @if((int)Input::old('count')>0)
        var secilenler = $('#secilenler').val();
        var urunid = $('#urunid').val();
        $('.ekle').removeClass('hide');
        if(secilenler!==""){
            $.blockUI();
            $.getJSON("{{ URL::to('uretim/urunkayitbilgi') }}",{secilenler:secilenler,urunid:urunid},function(event){
                if(event.durum){
                    var bilgi=event.bilgi;
                    var eski = event.eski;
                    var stokkodlari=event.netsisstokkodlari;
                    for(var i=0;i<count;i++){
                        $("#stokkodu" + (i)).select2();
                        $("#muadilkodu" + (i)).select2();
                        $("#uretici" + (i)).select2();
                        $("#marka" + (i)).select2();
                        $("#depokayidi" + (i)).select2();
                        $("#adet" + (i)).inputmask("mask", {mask: "9", repeat: 8, greedy: !1});
                        $("#uretimtarih" + (i)).datepicker({
                            rtl: Metronic.isRTL(),
                            orientation: "left",
                            autoclose: true,
                            language: 'tr'
                        });
                        $("#stokkodu" + (i)).empty();
                        if(!eski)
                            $("#stokkodu" + (i)).append('<option value="">Seçiniz...</option>');
                        $.each(stokkodlari, function (index) {
                            if(!eski || stokkodlari[index].id===bilgi[i]['kayit'].netsisstokkod.id)
                                $("#stokkodu" + (i)).append('<option value="' + stokkodlari[index].id + '"> ' + stokkodlari[index].kodu + ' - ' + stokkodlari[index].adi + '</option>');
                        });
                        $("#stokkodu"+(i)).select2("val",bilgi[i]['kayit'].netsisstokkod.id);

                        $("#muadilkodu" + (i)).empty();
                        $("#muadilkodu" + (i)).append('<option value="">Seçiniz...</option>');
                        $.each(stokkodlari, function (index) {
                            $("#muadilkodu" + (i)).append('<option value="' + stokkodlari[index].id + '"> ' + stokkodlari[index].kodu + ' - ' + stokkodlari[index].adi + '</option>');
                        });
                        var muadilkod =$('#muadilkod'+(i)).val();
                        $("#muadilkodu"+(i)).select2("val",muadilkod);
                        $('#muadil'+(i)).prop('checked',(muadilkod !== ""));
                        $.uniform.update();
                        var ureticiler =bilgi[i]['ureticiler'];
                        var uretici=$("#uretici"+(i)).val();
                        $("#uretici" + (i)).empty();
                        $("#uretici" + (i)).append('<option value="">Seçiniz...</option>');
                        $.each(ureticiler, function (index) {
                            $("#uretici" + (i)).append('<option value="' + ureticiler[index].id + '"> ' + ureticiler[index].ureticiadi + '</option>');
                        });
                        $("#uretici"+(i)).select2("val",uretici);
                        var markalar =bilgi[i]['markalar'];
                        var marka=$("#marka"+(i)).val();
                        $("#marka" + (i)).empty();
                        $("#marka" + (i)).append('<option value="">Seçiniz...</option>');
                        $.each(markalar, function (index) {
                            $("#marka" + (i)).append('<option value="' + markalar[index].id + '"> ' + markalar[index].markaadi + '</option>');
                        });
                        $("#marka"+(i)).select2("val",marka);
                        var depokayitlari =bilgi[i]['depokayitlari'];
                        $("#depokayidi" + (i)).empty();
                        if(!eski)
                            $("#depokayidi" + (i)).append('<option value="">Seçiniz...</option>');
                        $.each(depokayitlari, function (index) {
                            if(!eski || depokayitlari[index].INCKEYNO===bilgi[i]['kayit'].INCKEYNO)
                                if((depokayitlari[index].FISNO)==null){
                                    $("#depokayidi" + (i)).append('<option data-sipnum="' + depokayitlari[index].STHAR_SIPNUM + '" data-fisno="' + depokayitlari[index].FISNO + '" ' +
                                        'data-adet="' + depokayitlari[index].STHAR_GCMIK + '" data-cari="' + depokayitlari[index].STHAR_CARIKOD + '" ' +
                                        'value="' + depokayitlari[index].INCKEYNO + '"> ' + depokayitlari[index].STHAR_ACIKLAMA + ' - ' + depokayitlari[index].STHAR_TARIH + ' Tarihli - ' +
                                        depokayitlari[index].STHAR_GCMIK + ' Adet </option>');
                                }else{
                                    $("#depokayidi" + (i)).append('<option data-sipnum="' + depokayitlari[index].STHAR_SIPNUM + '" data-fisno="' + depokayitlari[index].FISNO + '" ' +
                                    'data-adet="' + depokayitlari[index].STHAR_GCMIK + '" data-cari="' + depokayitlari[index].STHAR_CARIKOD + '" ' +
                                    'value="' + depokayitlari[index].INCKEYNO + '"> ' + depokayitlari[index].FISNO + ' - ' + depokayitlari[index].STHAR_TARIH + ' Tarihli - ' +
                                    depokayitlari[index].STHAR_GCMIK + ' Adet - ' + depokayitlari[index].STHAR_CARIKOD + ' - ' + depokayitlari[index].CARI_ISIM + ' </option>');
                                }
                        });
                        $("#depokayidi"+(i)).select2("val",bilgi[i]['kayit'].INCKEYNO);
                    }
                    if(eski){
                        $('.ekle').addClass('hide');
                    }
                }else{
                    toastr['' + event.type + '']('' + event.text + '', '' + event.title + '');
                }
                $.unblockUI();
            });
        }
        $('.stokkodu').on('change', function () {
            var id = $(this).val();
            var no = $(this).closest('.urunler_ek').children('.no').val();
            var uretici = $("#uretici" + (no));
            var marka = $("#marka" + (no));
            var depokayidi = $("#depokayidi" + (no));
            if (id !== "") {
                $.blockUI();
                $.getJSON("{{ URL::to('uretim/ureticistokkod') }}", {stokkod: id}, function (event) {
                    if (event.durum) {
                        var depokayitlari = event.depokayitlari;
                        depokayidi.empty();
                        depokayidi.append('<option value="">Seçiniz...</option>');
                        $.each(depokayitlari, function (index) {
                            if((depokayitlari[index].FISNO)==null){
                                depokayidi.append('<option data-sipnum="' + depokayitlari[index].STHAR_SIPNUM + '" data-fisno="' + depokayitlari[index].FISNO + '" ' +
                                    'data-adet="' + depokayitlari[index].STHAR_GCMIK + '" data-cari="' + depokayitlari[index].STHAR_CARIKOD + '" ' +
                                    'value="' + depokayitlari[index].INCKEYNO + '"> ' + depokayitlari[index].STHAR_ACIKLAMA + ' - ' + depokayitlari[index].STHAR_TARIH + ' Tarihli - ' +
                                    depokayitlari[index].STHAR_GCMIK + ' Adet </option>');
                            }else{
                                depokayidi.append('<option data-sipnum="' + depokayitlari[index].STHAR_SIPNUM + '" data-fisno="' + depokayitlari[index].FISNO + '" ' +
                                    'data-adet="' + depokayitlari[index].STHAR_GCMIK + '" data-cari="' + depokayitlari[index].STHAR_CARIKOD + '" ' +
                                    'value="' + depokayitlari[index].INCKEYNO + '"> ' + depokayitlari[index].FISNO + ' - ' + depokayitlari[index].STHAR_TARIH + ' Tarihli - ' +
                                    depokayitlari[index].STHAR_GCMIK + ' Adet - ' + depokayitlari[index].STHAR_CARIKOD + ' - ' + depokayitlari[index].CARI_ISIM + ' </option>');
                            }
                        });
                        depokayidi.select2("val", "");
                        $("#muadilkodu"+no+">option").prop('disabled',false);
                        $("#muadilkodu"+no+">option[value='"+id+"']").prop('disabled',true);
                    } else {
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            } else {
                uretici.select2("val", "");
                marka.select2("val", "");
            }
            $(this).valid();
        });
        $('.muadilkodu').on('change', function () {
            var id = $(this).val();
            var no = $(this).closest('.urunler_ek').children('.no').val();
            $('#muadilkod'+no).val(id);
        });
        $('.depokayidi').on('change', function () {
            var no = $(this).closest('.urunler_ek').children('.no').val();
            var adet = $(this).find("option:selected").data('adet');
            $("#adet" + (no)).val(adet);
            var secilenler = $('#secilenler').val();
            secilenler+=(secilenler==="" ? "" : ",")+id;
            $('#secilenler').val(secilenler);
        });
        $('.muadil').on('change', function () {
            var no = $(this).closest('.urunler_ek').children('.no').val();
            if ($('.muadil'+no).attr('checked')) {
                $('.muadildurum'+no).removeClass('hide');
                $('#muadilkodu'+no).rules("add", "required");
            }else{
                $('.muadildurum'+no).addClass('hide');
                $('#muadilkodu'+no).rules("remove");
            }
        });
        $('.satirsil').click(function () {
            if ($('.urunler .urunler_ek').size() > 0) {
                var urun = $(this).closest('.urunler_ek');
                var adet = urun.children('.adet').val();
                urun.children('.adet').val(0);
                urun.remove();
                count -= adet;
                $("#count").val(count);
                $('.count').html(count + ' Adet');
                var j = 0;
                $('.urunler .urunler_ek').each(function () {
                    var id = $(this).children('.no').val();
                    $(this).children('div').children('h4').children('.accordion-toggle').attr('href', '#collapse_' + j);
                    $(this).children('.panel-collapse').attr('id', 'collapse_' + j);
                    $(this).children('div').children('div').children('div').children('div').children('.stokkodu').removeClass('stokkodu' + id).addClass('stokkodu' + j);
                    $(this).children('div').children('div').children('div').children('div').children('.stokkodu').attr('id', 'stokkodu' + j).attr('name', 'stokkodu[]');
                    $(this).children('div').children('div').children('div').children('div').children('.adet').attr('id', 'adet' + j).attr('name', 'adet[]');
                    $(this).children('div').children('div').children('div').children('div').children('.depokayidi').removeClass('depokayidi' + id).addClass('depokayidi' + j);
                    $(this).children('div').children('div').children('div').children('div').children('.depokayidi').attr('id', depokayidi + j).attr('name', 'depokayidi[]');
                    $(this).children('div').children('div').children('div').children('div').children('.uretici').removeClass('uretici' + id).addClass('uretici' + j);
                    $(this).children('div').children('div').children('div').children('div').children('.uretici').attr('id', 'uretici' + j).attr('name', 'uretici[]');
                    $(this).children('div').children('div').children('div').children('div').children('.marka').removeClass('marka' + id).addClass('marka' + j);
                    $(this).children('div').children('div').children('div').children('div').children('.marka').attr('id', 'marka' + j).attr('name', 'marka[]');
                    $(this).children('div').children('div').children('div').children('div').children('.uretimtarih').attr('id', 'uretimtarih' + j).attr('name', 'uretimtarih[]');
                    $(this).children('div').children('div').children('div').children('div').children('.barkod1').attr('id', 'barkod1' + j).attr('name', 'barkod1[]');
                    $(this).children('div').children('div').children('div').children('div').children('.barkod2').attr('id', 'barkod2' + j).attr('name', 'barkod2[]');
                    $(this).children('div').children('div').children('div').children('div').children('.barkod3').attr('id', 'barkod3' + j).attr('name', 'barkod3[]');
                    $(this).children('div').children('div').children('div').children('div').children('.muadil').attr('id', 'muadil' + j).attr('name', 'muadil[]');
                    $(this).children('div').children('div').children('div').children('div').children('.muadil').removeClass('muadil' + id).addClass('muadil' + j);
                    $(this).children('div').children('div').children('div').children('div').children('.muadildurum'+id).removeClass('muadildurum' + id).addClass('muadildurum' + j);
                    $(this).children('div').children('div').children('div').children('div').children('.muadilkod').attr('id', 'muadilkod' + j).attr('name', 'muadilkod[]');
                    $(this).children('div').children('div').children('div').children('div').children('.muadilkodu').removeClass('muadilkodu' + id).addClass('muadilkodu' + j);
                    $(this).children('div').children('div').children('div').children('div').children('.muadilkodu').attr('id', 'muadilkodu' + j).attr('name', 'muadilkodu[]');
                    $(this).children('.no').val(j);
                    j++;
                });
                flag = 0;
                if (count > 0) {
                    if (flag === 0)
                        $(".kaydet").prop('disabled', false);
                    else
                        $(".kaydet").prop('disabled', true);
                } else {
                    $(".kaydet").prop('disabled', true);
                }
            }
        });
        $('.adet').on('change', function () {
            var adet = parseInt($(this).val());
            var no = $(this).closest('.urunler_ek').children('.no').val();
            var kullanilan = parseInt($(".kullanilan" + (no)).text());
            if(adet<kullanilan){
                toastr['warning']('Girilen Adet Kullanılan Miktardan Az', 'Ürün Miktar Hatası');
            }else{
                $(".kalan" + (no)).text(adet-kullanilan);
            }
        });
        @elseif(count($uretimurun->inckeyno)>0)
        secilenler = $('#secilenler').val();
        urunid = $('#urunid').val();
        $('.ekle').removeClass('hide');
        if(secilenler!==""){
            $.blockUI();
            $.getJSON("{{ URL::to('uretim/urunkayitbilgi') }}",{secilenler:secilenler,urunid:urunid},function(event){
                if(event.durum){
                    var bilgi=event.bilgi;
                    var eski = event.eski;
                    var stokkodlari=event.netsisstokkodlari;
                    for(var i=0;i<count;i++){
                        $("#stokkodu" + (i)).select2();
                        $("#muadilkodu" + (i)).select2();
                        $("#uretici" + (i)).select2();
                        $("#marka" + (i)).select2();
                        $("#depokayidi" + (i)).select2();
                        $("#adet" + (i)).inputmask("mask", {mask: "9", repeat: 8, greedy: !1});
                        $("#uretimtarih" + (i)).datepicker({
                            rtl: Metronic.isRTL(),
                            orientation: "left",
                            autoclose: true,
                            language: 'tr'
                        });
                        $("#stokkodu" + (i)).empty();
                        if(!eski)
                            $("#stokkodu" + (i)).append('<option value="">Seçiniz...</option>');
                        $.each(stokkodlari, function (index) {
                            if(!eski || stokkodlari[index].id===bilgi[i]['kayit'].netsisstokkod.id)
                                $("#stokkodu" + (i)).append('<option value="' + stokkodlari[index].id + '"> ' + stokkodlari[index].kodu + ' - ' + stokkodlari[index].adi + '</option>');
                        });
                        $("#stokkodu"+(i)).select2("val",bilgi[i]['kayit'].netsisstokkod.id);

                        $("#muadilkodu" + (i)).empty();
                        $("#muadilkodu" + (i)).append('<option value="">Seçiniz...</option>');
                        $.each(stokkodlari, function (index) {
                            $("#muadilkodu" + (i)).append('<option value="' + stokkodlari[index].id + '"> ' + stokkodlari[index].kodu + ' - ' + stokkodlari[index].adi + '</option>');
                        });
                        var muadilkod =$('#muadilkod'+(i)).val();
                        $("#muadilkodu"+(i)).select2("val",muadilkod);
                        $('#muadil'+(i)).prop('checked',(muadilkod !== ""));
                        $.uniform.update();
                        var ureticiler =bilgi[i]['ureticiler'];
                        var uretici=$("#uretici"+(i)).val();
                        $("#uretici" + (i)).empty();
                        $("#uretici" + (i)).append('<option value="">Seçiniz...</option>');
                        $.each(ureticiler, function (index) {
                            $("#uretici" + (i)).append('<option value="' + ureticiler[index].id + '"> ' + ureticiler[index].ureticiadi + '</option>');
                        });
                        $("#uretici"+(i)).select2("val",uretici);
                        var markalar =bilgi[i]['markalar'];
                        var marka=$("#marka"+(i)).val();
                        $("#marka" + (i)).empty();
                        $("#marka" + (i)).append('<option value="">Seçiniz...</option>');
                        $.each(markalar, function (index) {
                            $("#marka" + (i)).append('<option value="' + markalar[index].id + '"> ' + markalar[index].markaadi + '</option>');
                        });
                        $("#marka"+(i)).select2("val",marka);
                        var depokayitlari =bilgi[i]['depokayitlari'];
                        $("#depokayidi" + (i)).empty();
                        if(!eski)
                            $("#depokayidi" + (i)).append('<option value="">Seçiniz...</option>');
                        $.each(depokayitlari, function (index) {
                            if(!eski || depokayitlari[index].INCKEYNO===bilgi[i]['kayit'].INCKEYNO)
                                if((depokayitlari[index].FISNO)==null){
                                    $("#depokayidi" + (i)).append('<option data-sipnum="' + depokayitlari[index].STHAR_SIPNUM + '" data-fisno="' + depokayitlari[index].FISNO + '" ' +
                                        'data-adet="' + depokayitlari[index].STHAR_GCMIK + '" data-cari="' + depokayitlari[index].STHAR_CARIKOD + '" ' +
                                        'value="' + depokayitlari[index].INCKEYNO + '"> ' + depokayitlari[index].STHAR_ACIKLAMA + ' - ' + depokayitlari[index].STHAR_TARIH + ' Tarihli - ' +
                                        depokayitlari[index].STHAR_GCMIK + ' Adet </option>');
                                }else{
                                    $("#depokayidi" + (i)).append('<option data-sipnum="' + depokayitlari[index].STHAR_SIPNUM + '" data-fisno="' + depokayitlari[index].FISNO + '" ' +
                                    'data-adet="' + depokayitlari[index].STHAR_GCMIK + '" data-cari="' + depokayitlari[index].STHAR_CARIKOD + '" ' +
                                    'value="' + depokayitlari[index].INCKEYNO + '"> ' + depokayitlari[index].FISNO + ' - ' + depokayitlari[index].STHAR_TARIH + ' Tarihli - ' +
                                    depokayitlari[index].STHAR_GCMIK + ' Adet - ' + depokayitlari[index].STHAR_CARIKOD + ' - ' + depokayitlari[index].CARI_ISIM + ' </option>');
                                }
                        });
                        $("#depokayidi"+(i)).select2("val",bilgi[i]['kayit'].INCKEYNO);
                    }
                    if(eski){
                        $('.ekle').addClass('hide');
                    }
                }else{
                    toastr['' + event.type + '']('' + event.text + '', '' + event.title + '');
                }
                $.unblockUI();
            });
        }
        $('.stokkodu').on('change', function () {
            var id = $(this).val();
            var no = $(this).closest('.urunler_ek').children('.no').val();
            var uretici = $("#uretici" + (no));
            var marka = $("#marka" + (no));
            var depokayidi = $("#depokayidi" + (no));
            if (id !== "") {
                $.blockUI();
                $.getJSON("{{ URL::to('uretim/ureticistokkod') }}", {stokkod: id}, function (event) {
                    if (event.durum) {
                        var depokayitlari = event.depokayitlari;
                        depokayidi.empty();
                        depokayidi.append('<option value="">Seçiniz...</option>');
                        $.each(depokayitlari, function (index) {
                            if((depokayitlari[index].FISNO)==null){
                                depokayidi.append('<option data-sipnum="' + depokayitlari[index].STHAR_SIPNUM + '" data-fisno="' + depokayitlari[index].FISNO + '" ' +
                                    'data-adet="' + depokayitlari[index].STHAR_GCMIK + '" data-cari="' + depokayitlari[index].STHAR_CARIKOD + '" ' +
                                    'value="' + depokayitlari[index].INCKEYNO + '"> ' + depokayitlari[index].STHAR_ACIKLAMA + ' - ' + depokayitlari[index].STHAR_TARIH + ' Tarihli - ' +
                                    depokayitlari[index].STHAR_GCMIK + ' Adet </option>');
                            }else{
                                depokayidi.append('<option data-sipnum="' + depokayitlari[index].STHAR_SIPNUM + '" data-fisno="' + depokayitlari[index].FISNO + '" ' +
                                    'data-adet="' + depokayitlari[index].STHAR_GCMIK + '" data-cari="' + depokayitlari[index].STHAR_CARIKOD + '" ' +
                                    'value="' + depokayitlari[index].INCKEYNO + '"> ' + depokayitlari[index].FISNO + ' - ' + depokayitlari[index].STHAR_TARIH + ' Tarihli - ' +
                                    depokayitlari[index].STHAR_GCMIK + ' Adet - ' + depokayitlari[index].STHAR_CARIKOD + ' - ' + depokayitlari[index].CARI_ISIM + ' </option>');
                            }
                        });
                        depokayidi.select2("val", "");
                        $("#muadilkodu"+no+">option").prop('disabled',false);
                        $("#muadilkodu"+no+">option[value='"+id+"']").prop('disabled',true);
                    } else {
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            } else {
                uretici.select2("val", "");
                marka.select2("val", "");
            }
            $(this).valid();
        });
        $('.muadilkodu').on('change', function () {
            var id = $(this).val();
            var no = $(this).closest('.urunler_ek').children('.no').val();
            $('#muadilkod'+no).val(id);
        });
        $('.depokayidi').on('change', function () {
            var no = $(this).closest('.urunler_ek').children('.no').val();
            var adet = $(this).find("option:selected").data('adet');
            $("#adet" + (no)).val(adet);
            var secilenler = $('#secilenler').val();
            secilenler+=(secilenler==="" ? "" : ",")+id;
            $('#secilenler').val(secilenler);
        });
        $('.muadil').on('change', function () {
            var no = $(this).closest('.urunler_ek').children('.no').val();
            if ($('.muadil'+no).attr('checked')) {
                $('.muadildurum'+no).removeClass('hide');
                $('#muadilkodu'+no).rules("add", "required");
            }else{
                $('.muadildurum'+no).addClass('hide');
                $('#muadilkodu'+no).rules("remove");
            }
        });
        $('.satirsil').click(function () {
            if ($('.urunler .urunler_ek').size() > 0) {
                var urun = $(this).closest('.urunler_ek');
                var adet = urun.children('.adet').val();
                urun.children('.adet').val(0);
                urun.remove();
                count -= adet;
                $("#count").val(count);
                $('.count').html(count + ' Adet');
                var j = 0;
                $('.urunler .urunler_ek').each(function () {
                    var id = $(this).children('.no').val();
                    $(this).children('div').children('h4').children('.accordion-toggle').attr('href', '#collapse_' + j);
                    $(this).children('.panel-collapse').attr('id', 'collapse_' + j);
                    $(this).children('div').children('div').children('div').children('div').children('.stokkodu').removeClass('stokkodu' + id).addClass('stokkodu' + j);
                    $(this).children('div').children('div').children('div').children('div').children('.stokkodu').attr('id', 'stokkodu' + j).attr('name', 'stokkodu[]');
                    $(this).children('div').children('div').children('div').children('div').children('.adet').attr('id', 'adet' + j).attr('name', 'adet[]');
                    $(this).children('div').children('div').children('div').children('div').children('.depokayidi').removeClass('depokayidi' + id).addClass('depokayidi' + j);
                    $(this).children('div').children('div').children('div').children('div').children('.depokayidi').attr('id', depokayidi + j).attr('name', 'depokayidi[]');
                    $(this).children('div').children('div').children('div').children('div').children('.uretici').removeClass('uretici' + id).addClass('uretici' + j);
                    $(this).children('div').children('div').children('div').children('div').children('.uretici').attr('id', 'uretici' + j).attr('name', 'uretici[]');
                    $(this).children('div').children('div').children('div').children('div').children('.marka').removeClass('marka' + id).addClass('marka' + j);
                    $(this).children('div').children('div').children('div').children('div').children('.marka').attr('id', 'marka' + j).attr('name', 'marka[]');
                    $(this).children('div').children('div').children('div').children('div').children('.uretimtarih').attr('id', 'uretimtarih' + j).attr('name', 'uretimtarih[]');
                    $(this).children('div').children('div').children('div').children('div').children('.barkod1').attr('id', 'barkod1' + j).attr('name', 'barkod1[]');
                    $(this).children('div').children('div').children('div').children('div').children('.barkod2').attr('id', 'barkod2' + j).attr('name', 'barkod2[]');
                    $(this).children('div').children('div').children('div').children('div').children('.barkod3').attr('id', 'barkod3' + j).attr('name', 'barkod3[]');
                    $(this).children('div').children('div').children('div').children('div').children('.muadil').attr('id', 'muadil' + j).attr('name', 'muadil[]');
                    $(this).children('div').children('div').children('div').children('div').children('.muadil').removeClass('muadil' + id).addClass('muadil' + j);
                    $(this).children('div').children('div').children('div').children('div').children('.muadildurum'+id).removeClass('muadildurum' + id).addClass('muadildurum' + j);
                    $(this).children('div').children('div').children('div').children('div').children('.muadilkod').attr('id', 'muadilkod' + j).attr('name', 'muadilkod[]');
                    $(this).children('div').children('div').children('div').children('div').children('.muadilkodu').removeClass('muadilkodu' + id).addClass('muadilkodu' + j);
                    $(this).children('div').children('div').children('div').children('div').children('.muadilkodu').attr('id', 'muadilkodu' + j).attr('name', 'muadilkodu[]');
                    $(this).children('.no').val(j);
                    j++;
                });
                flag = 0;
                if (count > 0) {
                    if (flag === 0)
                        $(".kaydet").prop('disabled', false);
                    else
                        $(".kaydet").prop('disabled', true);
                } else {
                    $(".kaydet").prop('disabled', true);
                }
            }
        });
        $('.adet').on('change', function () {
            var adet = parseInt($(this).val());
            var no = $(this).closest('.urunler_ek').children('.no').val();
            var kullanilan = parseInt($(".kullanilan" + (no)).text());
            if(adet<kullanilan){
                toastr['warning']('Girilen Adet Kullanılan Miktardan Az', 'Ürün Miktar Hatası');
            }else{
                $(".kalan" + (no)).text(adet-kullanilan);
            }
        });
        @endif

        $("#ureticiadiekle").click(function () {
            var uretici = $('#ureticiadi').val();
            if (uretici === "") {
                toastr['warning']('Üretici Bilgisi Boş Geçildi', 'Üretici Ekleme Hatası');
            }else{
                $.blockUI();
                $.getJSON(" {{ URL::to('uretim/ureticiekle') }}", {uretici: uretici}, function (event) {
                    toastr[event.type](event.text, event.title);
                    $('#ureticiekle').modal('hide');
                    if (event.durum) {
                        var ureticiler = event.ureticiler;
                        var mevcutureticiler = $(".uretici");
                        $.each(mevcutureticiler, function () {
                            var no = $(this).closest('.urunler_ek').children('.no').val();
                            var uretici = $("#uretici" + (no));
                            uretici.empty();
                            uretici.append('<option value="">Seçiniz...</option>');
                            $.each(ureticiler, function (index) {
                                uretici.append('<option value="' + ureticiler[index].id + '"> ' + ureticiler[index].ureticiadi + '</option>');
                            });
                            uretici.select2("val", "");
                        });
                    }
                    $('#ureticiadi').val('');
                    $.unblockUI();
                });
            }
        });
        $("#markaadiekle").click(function () {
            var marka = $('#markaadi').val();
            if (marka === "") {
                toastr['warning']('Marka Bilgisi Boş Geçildi', 'Marka Ekleme Hatası');
            }else{
                $.blockUI();
                $.getJSON(" {{ URL::to('uretim/markaekle') }}", {marka: marka}, function (event) {
                    toastr[event.type](event.text, event.title);
                    $('#markaekle').modal('hide');
                    if (event.durum) {
                        var markalar = event.markalar;
                        var mevcutmarkalar = $(".marka");
                        $.each(mevcutmarkalar, function () {
                            var no = $(this).closest('.urunler_ek').children('.no').val();
                            var marka = $("#marka" + (no));
                            marka.empty();
                            marka.append('<option value="">Seçiniz...</option>');
                            $.each(markalar, function (index) {
                                marka.append('<option value="' + markalar[index].id + '"> ' + markalar[index].markaadi + '</option>');
                            });
                            marka.select2("val", "");
                        });
                    }
                    $('#markaadi').val('');
                    $.unblockUI();
                });
            }
        });

        if (count > 0) {
            if (flag === 0)
                $(".kaydet").prop('disabled', false);
            else
                $(".kaydet").prop('disabled', true);
        }else{
            $(".kaydet").prop('disabled', true);
        }

        $('#formsubmit').click(function () {
            $('#form_sample').submit();
        });
        $("select").on("select2-close", function () { $(this).valid(); });
        $('#gelis').on('change', function() { $(this).valid(); });
    });
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Ürün Kayıdı Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('uretim/urunkayitduzenle/'.$uretimurun->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-sm-2 col-xs-4" > Eklenecek Ürünler: </label>
                    <label class="col-sm-2 col-xs-4 count" style="padding-top: 9px">{{Input::old('count') ? Input::old('count') : count($uretimurun->inckeyno) .' Adet'}}</label>
                    <input type="text" id="urunid" name="urunid" value="{{Input::old('urunid') ? Input::old('urunid') : $uretimurun->id}}" class="form-control hide">
                    <input type="text" id="count" name="count" value="{{Input::old('count') ? Input::old('count') : count($uretimurun->inckeyno)}}" data-required="1" class="form-control hide">
                    <input type="text" id="secilenler" name="secilenler" value="{{Input::old('secilenler') ? Input::old('secilenler') : $uretimurun->inckeyno }}" data-required="1" class="form-control hide">
                    <label class="col-sm-3 col-xs-4" style="padding-top: 10px;"><input type="checkbox" id="barkodvar" name="barkodvar" checked/> Barkod Basılacak </label>
                </div>
                <div class="panel-group accordion urunler col-xs-12" id="accordion1">
                        @for($i=0;$i<(Input::old('count') ? (int)(Input::old('count')) : count($uretimurun->inckeyno));$i++)
                            <div class="panel panel-default urunler_ek">
                                <input class="no hide" value="{{$i}}"/>
                                <input class="adet hide" value="1"/>
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_{{$i}}">Yeni</a>
                                    </h4>
                                </div>
                                <div id="collapse_{{$i}}" class="panel-collapse in">
                                    <div class="panel-body">
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="col-xs-4 col-sm-4 control-label">Stok Kodu:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8 col-sm-6">
                                                <i class="fa"></i>
                                                <select class="form-control select2me validcount stokkodu stokkodu{{$i}}" id="stokkodu{{$i}}" name="stokkodu[]" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                </select>
                                            </div>
                                            <label class="col-xs-1 hidden-xs {{$uretimurun->kullanilan>0 ? 'hide' : ''}}"><a class="btn red satirsil">Sil</a></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="col-xs-4 col-sm-4 control-label">Adet:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8 col-sm-6">
                                                <i class="fa"></i>
                                                <input type="text" id="adet{{$i}}" name="adet[]" class="form-control adet" value="{{Input::old('adet.'.$i) ? Input::old('adet.'.$i) : $uretimurun->adet}}">
                                            </div>
                                            <label class="col-xs-1 hidden-sm hidden-md hidden-lg {{$uretimurun->kullanilan>0 ? 'hide' : ''}}"><a class="btn red satirsil">Sil</a></label>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="col-sm-2 col-xs-4 control-label">Depo Kayıdı:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-sm-10 col-xs-8">
                                                <i class="fa"></i>
                                                <select class="form-control select2me valid{{$i}} depokayidi depokayidi{{$i}}" id="depokayidi{{$i}}" name="depokayidi[]" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="col-xs-4 col-sm-4 control-label">Üretici:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i>
                                                <select class="form-control select2me valid{{$i}} uretici uretici{{$i}}" id="uretici{{$i}}" name="uretici[]" tabindex="-1" title="">' +
                                                    <option value="">Seçiniz...</option>
                                                    @foreach($ureticiler as $uretici)
                                                        @if((Input::old('uretici.'.$i) ? Input::old('uretici.'.$i) : $uretimurun->uretimuretici_id)==$uretici->id)
                                                            <option value="{{ $uretici->id }}" selected>{{ $uretici->ureticiadi }}</option>
                                                        @else
                                                            <option value="{{ $uretici->id }}" >{{ $uretici->ureticiadi }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="col-xs-4 col-sm-4 control-label">Marka:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i>
                                                <select class="form-control select2me valid{{$i}} marka marka{{$i}}" id="marka{{$i}}" name="marka[]" tabindex="-1" title="">' +
                                                    <option value="">Seçiniz...</option>
                                                    @foreach($markalar as $marka)
                                                        @if((Input::old('marka.'.$i)? Input::old('marka.'.$i) : $uretimurun->uretimurunmarka_id)==$marka->id)
                                                            <option value="{{ $marka->id }}" selected>{{ $marka->markaadi }}</option>
                                                        @else
                                                            <option value="{{ $marka->id }}" >{{ $marka->markaadi }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Üretim Yılı: <span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i>
                                                <div class="input-group input-medium date date-picker" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                                    <input id="uretimtarih{{$i}}" type="text" name="uretimtarih[]" class="form-control"  value="{{Input::old('uretimtarih') ? Input::old('uretimtarih.'.$i) : date("d-m-Y", strtotime($uretimurun->uretimtarihi)) }}">
                                                    <span class="input-group-btn">
                                                        <button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;">
                                                            <i class="fa fa-calendar"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="col-xs-4 col-sm-4 control-label">Kullanılan:</label>
                                            <label class="col-xs-8 col-sm-6 kullanilan{{$i}}" style="padding-top: 7px">{{ $uretimurun->kullanilan }}</label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="col-xs-4 col-sm-4 control-label">Kalan:</label>
                                            <label class="col-xs-8 col-sm-6 kalan{{$i}}" style="padding-top: 7px">{{ Input::old('adet.'.$i) ? Input::old('adet.'.$i)-$uretimurun->kullanilan : $uretimurun->adet-$uretimurun->kullanilan }}</label>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-6">Diğer Bilgiler (Barkod vb.):</label>
                                            <div class="col-sm-3 col-xs-6">
                                                <input type="text" id="barkod1{{$i}}" name="barkod1[]" class="form-control barkod1" value="{{Input::old('barkod1.'.$i) ? Input::old('barkod1.'.$i) : ($uretimurun->urunbarkod1==null ? "" : $uretimurun->urunbarkod1)}}">
                                            </div>
                                            <div class="col-sm-3 col-xs-6">
                                                <input type="text" id="barkod2{{$i}}" name="barkod2[]" class="form-control barkod2" value="{{Input::old('barkod2.'.$i) ? Input::old('barkod2.'.$i) : ($uretimurun->urunbarkod2==null ? "" : $uretimurun->urunbarkod2)}}">
                                            </div>
                                            <div class="col-sm-3 col-xs-6">
                                                <input type="text" id="barkod3{{$i}}" name="barkod3[]" class="form-control barkod3" value="{{Input::old('barkod3.'.$i) ? Input::old('barkod3.'.$i) : ($uretimurun->urunbarkod3==null ? "" : $uretimurun->urunbarkod3)}}">
                                            </div>
                                        </div>
                                        <div class="form-group col-xs-6 col-xs-12">
                                            <label class="col-xs-offset-4 col-xs-8" style="padding-top: 10px;">
                                                <input type="checkbox" id="muadil{{$i}}" name="muadil[]" class="muadil muadil{{$i}}" /> Muadil Olarak Kullanılacak </label>
                                        </div>
                                        <div class="form-group col-xs-6 col-xs-12 muadildurum{{$i}} {{(Input::old('muadilkod.'.$i) ? Input::old('muadilkod.'.$i) : ($uretimurun->muadil==null ? "" : $uretimurun->muadil))!="" ? "" : "hide"}}">
                                            <label class="col-xs-4 col-sm-4 control-label">Hangi ürün için?:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8 col-sm-6">
                                                <i class="fa"></i><select class="form-control select2me muadilkodu muadilkodu{{$i}}" id="muadilkodu{{$i}}" name="muadilkodu[]" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                </select>
                                                <input type="text" id="muadilkod{{$i}}" name="muadilkod[]" class="form-control muadilkod hide" value="{{Input::old('muadilkod.'.$i) ? Input::old('muadilkod.'.$i) : ($uretimurun->muadil==null ? "" : $uretimurun->muadil)}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                </div>
                <div class="form-group">
                    <div class="col-sm-6 col-xs-12 control-label" style="text-align: left;"><a class="btn green ekle">Ürün Ekle </a>
                        <a class="btn yellow ureticiekle" data-toggle="modal" data-target="#ureticiekle">Üretici Ekle</a>
                        <a class="btn blue markaekle" data-toggle="modal" data-target="#markaekle">Marka Ekle</a></div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <button type="button" class="btn green kaydet" data-toggle="modal" data-target="#confirm">Kaydet</button>
                        <a href="{{ URL::to('uretim/urunkayit')}}" class="btn default">Vazgeç</a>
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
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Ürün Kayıdı Tamamlanacak</h4>
                </div>
                <div class="modal-body">
                    Girilen Ürün Kayıdı Bilgileri Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="ureticiekle" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Üretici Ekle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_1" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body col-xs-12">
                                        <h3 class="form-section col-xs-12">Yeni Üretici Girişi</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Üretici Adı:</label>
                                            <div class="col-xs-8">
                                                <input type="text" id="ureticiadi" name="ureticiadi" value="{{Input::old('ureticiadi')}}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-offset-3 col-xs-9">
                                                    <a id="ureticiadiekle" href="#" type="button" data-dismiss="modal" class="btn green">Ekle</a>
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
    <div class="modal fade" id="markaekle" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Marka Ekle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body col-xs-12">
                                        <h3 class="form-section col-xs-12">Yeni Marka Girişi</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Marka Adı:</label>
                                            <div class="col-xs-8">
                                                <input type="text" id="markaadi" name="markaadi" value="{{Input::old('markaadi')}}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-offset-3 col-xs-9">
                                                    <a id="markaadiekle" href="#" type="button" data-dismiss="modal" class="btn green">Ekle</a>
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
