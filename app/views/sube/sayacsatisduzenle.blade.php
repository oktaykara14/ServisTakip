@extends('layout.master')

@section('page-title')
<!--suppress JSValidateTypes -->
<div class="page-title">
    <h1>Şube Fatura <small>Düzenleme Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::to('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" >
<link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
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
<script src="{{ URL::to('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/datatables.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery-maskmoney/jquery.maskMoney.min.js') }}" type="text/javascript" ></script>

@stop

@section('page-script')
<script src="{{ URL::to('pages/sube/form-validation-5.js') }}"></script>
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
    var table = $('#sample_1');
    var oTable = table.DataTable({
        "sPaginationType": "simple_numbers",
        "searching": true,
        "ordering": false,
        "bProcessing": false,
        "sAjaxSource": "",
        "fnDrawCallback" : function() {
        },
        "bServerSide": false,
        "bInfo": true,
        "bPaginate": true,
        "language": {
            "emptyTable": "Veri Bulunamadı",
            "info": "  _TOTAL_ Kayıttan _START_ - _END_ Arası Kayıtlar",
            "infoEmpty": "Kayıt Yok",
            "infoFiltered": "( _MAX_ Kayıt İçerisinden Bulunan)",
            "lengthMenu": "Sayfada _MENU_ Kayıt Göster",
            "paginate": {
                "first": "İlk",
                "last": "Son",
                "previous": "Önceki",
                "next": "Sonraki"
            },
            "search": "Bul:",
            "zeroRecords": "Eşleşen Kayıt Bulunmadı"

        },
        "aoColumns": [{"sClass":"id"},null,null,null],
        "lengthMenu": [
            [5, 10],
            [5, 10]
        ]
    });
    var tableWrapper = jQuery('#sample_editable_1_wrapper');
    table.on('click', 'tr', function () {
        if(oTable.cell( $(this).children('.id')).data()!==undefined) {
            var bos=0;
            $(this).toggleClass("active");
            var secilen = "";
            if ($(this).hasClass('active')) {
                $("tbody tr").removeClass("active");
                $(this).addClass("active");
                secilen = oTable.cell($(this).children('.id')).data();
                $('#secilenabone').val(secilen);
            } else {
                $(this).removeClass("active");
                $('#secilenabone').val("");
                bos=1;
            }
            if(bos)
            {
                $('#listesec').addClass("hide");
            }else{
                $('#listesec').removeClass("hide");
            }
        }
    });
    tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
</script>
<script>
    $(document).ready(function() {
        $('#nakit').maskMoney({suffix: " ₺"});
        $('#kredikart').maskMoney({suffix: " ₺"});
        $('#kredikart1').maskMoney({suffix: " ₺"});
        $('#kredikart2').maskMoney({suffix: " ₺"});
        $(".kaydet").prop('disabled',true);
        var count=parseInt($("#count").val());
        var cnt = count+1;
        var satisid=$('#satisid').val();
        $('.ekle').click(function(){
            var abone=$('#abone').val();
            //var adet=1;  // eklenecek sayı
            var newRow="";
            newRow += '<div class="panel panel-default sayaclar_ek"><input class="no hide" value="'+(count)+'"/><input class="adet hide" value="1"/><div class="panel-heading"><h4 class="panel-title">'+
            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_'+count+'">Yeni</a></h4></div>' +
            '<div id="collapse_'+count+'" class="panel-collapse in"><div class="panel-body"><div class="form-group">'+
            '<label class="col-sm-2 col-xs-4 control-label">Ürün Adı:<span class="required" aria-required="true"> * </span></label>'+
            '<div class="input-icon right col-sm-7 col-xs-6"><i class="fa"></i><select class="form-control select2me urunadi urunadi'+count+'" id="urunadi'+count+'" name="urunadi[]" tabindex="-1" title="">'+
            '<option value="">Seçiniz...</option>'+
            @foreach($urunler as $urun)
            '<option data-id="{{ 0.00 }}" data-fiyat="{{ 0.00 }}" data-baglanti="{{ $urun->baglanti }}" data-birim="{{ $urun->parabirimi_id }}" data-value="{{ $urun->parabirimi->birimi }}" value="{{ $urun->id }}">{{ $urun->netsisstokkod->kodu.' - '.$urun->urunadi.' ('.intval($urun->stok->BAKIYE).')'}}</option>'+
            @endforeach
            '</select></div>' +
            '<label class="col-sm-1 col-xs-2"><a class="btn red satirsil">Sil</a></label></div>'+
            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Fiyatı:<span class="required" aria-required="true"> * </span></label><div class="input-icon right col-xs-8">' +
            '<i class="fa"></i><input type="tel" id="fiyat'+(count)+'" name="fiyat[]" class="form-control fiyat" value="0.00">' +
            '<div class="hide"><input type="text" id="birimfiyat'+(count)+'" name="birimfiyat[]" class="form-control birimfiyat" value="0.00">' +
            '<input type="text" id="gfiyat'+(count)+'" name="gfiyat[]" class="form-control gfiyat" value="0.00"></div></div></div>'+
            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Miktarı:</label><div class="col-xs-4">' +
            '<input type="text" id="miktar'+(count)+'" name="miktar[]" maxlength="3" class="form-control miktar" value="1"></div>'+
            '<div class="col-xs-4" style="margin-top: 5px;font-size: 15px"><input type="checkbox" id="ucretsiz'+(count)+'" value="'+count+'" name="ucretsiz[]" class="ucretsiz"/>Ücretsiz</div>'+
            '</div>'+
            '<div class="form-group baglanti baglantidurum'+(count)+' hide"><label class="col-sm-2 col-xs-4 control-label">Bağlantılı Sayaçlar:<span class="required" aria-required="true"> * </span></label>'+
            '<div class="input-icon right col-sm-9 col-xs-7"><i class="fa"></i><select class="form-control select2 select2-offscreen abonesayac abonesayac'+(count)+'" id="abonesayac'+(count)+'" name="abonesayac['+count+'][]" multiple="" tabindex="-1">'+
            '</select></div></div><div class="hide"><input type="text" id="baglantidurum'+(count)+'" name="baglantidurum[]" class="form-control baglantidurum" value="0"></div>'+
            '</div></div></div>';
            cnt++;
            count++;
            $('.count').html(count+' Adet');
            $('.sayaclar').append(newRow);
            $('select.valid'+(count-1)).each(function(){
                $(this).rules('remove');
                $(this).rules('add', {
                    required: true
                });
            });
            $('.sayaclar').find('input:checkbox').uniform();
            $.uniform.update();
            if(abone!==""){
                $.blockUI();
                $.getJSON("{{ URL::to('sube/urunler') }}",{id:abone},function(event){
                    if(event.durum) {
                        var urunler = event.urunler;
                        $("#urunadi" + (count - 1)).empty();
                        $("#urunadi" + (count - 1)).append('<option value="">Seçiniz...</option>');
                        $.each(urunler, function (index) {
                            $("#urunadi" + (count - 1)).append('<option data-id="' + 0.00 + '" data-fiyat="' + 0.00+ '" data-baglanti="' + urunler[index].baglanti + '" ' +
                                'data-birim="' + urunler[index].parabirimi_id + '" data-value="' + urunler[index].parabirimi.birimi + '" value="' + urunler[index].id + '">' +
                                urunler[index].netsisstokkod.kodu+' - '+urunler[index].urunadi + ' ('+parseInt(urunler[index].stok.BAKIYE)+')</option>');
                        });
                        $("#urunadi" + (count - 1)).select2();
                    }else{
                        toastr['' + event.type + '']('' + event.text + '', '' + event.title + '');
                    }
                    $.unblockUI();
                });
            }
            $(".miktar").inputmask("mask", {
                mask:"9",repeat:3,greedy:!1
            });
            $("#fiyat"+(count-1)).maskMoney({suffix: ' ₺',affixesStay:true, allowZero:true});
            $("#urunadi"+(count-1)).select2();
            $("#abonesayac"+(count-1)).select2();
            var ucretsizler=$('#ucretsizler').val();
            ucretsizler+=(ucretsizler==="" ? "" : ",")+0;
            $('#ucretsizler').val(ucretsizler);
            var baglantidurumlari=$('#baglantidurumlari').val();
            baglantidurumlari+=(baglantidurumlari==="" ? "" : ",")+0;
            $('#baglantidurumlari').val(baglantidurumlari);
            $('.urunadi').on('change', function () {
                var no = $(this).closest('.sayaclar_ek').children('.no').val();
                var abone=$('#abone').val();
                $(this).closest('.sayaclar_ek').children('div').children('h4').children('.accordion-toggle').text(($("#urunadi"+(no)).select2('data').text)+' - '+($("#miktar"+(no)).val())+' ADET');
                var baglantidurum=$(this).find("option:selected").data('baglanti');
                $("#baglantidurum"+(no)).val(baglantidurum).trigger('change');
                if(baglantidurum){
                    if(abone!==""){
                        $.blockUI();
                        $.getJSON("{{ URL::to('sube/abonesayac') }}",{id:abone,satisid:satisid},function(event){
                            if (event.durum) {
                                var sayaclar = event.abonesayaclari;
                                $("#abonesayac" + (no)).empty();
                                $.each(sayaclar, function (index) {
                                    $("#abonesayac" + (no)).append('<option value="' + sayaclar[index].id + '"> ' + sayaclar[index].serino + '</option>');
                                });
                                $("#abonesayac" + (no)).select2({
                                    placeholder: "Sayaç Seçin",
                                    allowClear: true
                                });
                            } else {
                                toastr['' + event.type + '']('' + event.text + '', '' + event.title + '');
                            }
                            $.unblockUI();
                        });
                    }
                    $('.baglantidurum'+(no)).removeClass('hide');
                }else{
                    $('.baglantidurum'+(no)).addClass('hide');
                }
                var fiyat = parseFloat($(this).find("option:selected").data('id')) || 0.00;
                var birimfiyat = parseFloat($(this).find("option:selected").data('fiyat')) || 0.00;
                var parabirimi = $(this).find("option:selected").data('value');
                var parabirimiid = $(this).find("option:selected").data('birim');
                if(parabirimi==null){
                    parabirimi='';
                }
                $("#fiyat"+(no)).maskMoney({suffix: ' '+parabirimi,affixesStay:true, allowZero:true});
                $('#fiyat'+(no)).maskMoney('mask',fiyat.toFixed(2)*100);
                $("#birimfiyat"+(no)).val(birimfiyat.toFixed(2));
                $("#gfiyat"+(no)).val(fiyat.toFixed(2));
                var toplamtutar = 0;
                $('select.urunadi').each(function(){
                    var urunno = $(this).closest('.sayaclar_ek').children('.no').val();
                    var urunfiyat = parseFloat($(this).find("option:selected").data('id')) || 0.00;
                    var urunmiktar = parseFloat($('#miktar'+(urunno)).val());
                    if($("#ucretsiz"+(urunno)).is(':checked')){
                        urunfiyat = 0;
                        $("#fiyat"+urunno).maskMoney('mask',urunfiyat);
                    }
                    toplamtutar +=(urunfiyat*urunmiktar);
                });
                var kur=1;
                if(parabirimiid!==undefined){
                    if(parabirimiid!==1){
                        if(parabirimiid===2)
                            kur=parseFloat($('#euro').val()).toFixed(4);
                        else if(parabirimiid===3)
                            kur=parseFloat($('#dolar').val()).toFixed(4);
                        else
                            kur=parseFloat($('#sterlin').val()).toFixed(4);
                    }
                }
                toplamtutar*=kur;
                var kdv=((toplamtutar*18)/118);
                var tutar=(toplamtutar-kdv);
                //toplamtutar=Math.round(toplamtutar*10)/10;
                //kdv=toplamtutar-tutar;
                var birim=$('#birim').val();
                $('.tutar').html('<b>'+tutar.toFixed(2)+' '+birim+'</b>');
                $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+birim+'</b>');
                $('.toplamtutar').html('<b>'+toplamtutar.toFixed(2)+' '+birim+'</b>');
                $('#tutar').val(tutar.toFixed(2));
                $('#kdvtutar').val(kdv.toFixed(2));
                $('#toplamtutar').val(toplamtutar.toFixed(2));
                $('#geneltutar').val(tutar.toFixed(2));
                $('#genelkdv').val(kdv.toFixed(2));
                $('#geneltoplam').val(toplamtutar.toFixed(2));
            });
            $('.fiyat').on('change', function () {
                var fiyat = parseFloat($(this).val());
                var birimfiyat = ((fiyat*100)/118);
                var no = $(this).closest('.sayaclar_ek').children('.no').val();
                $("#birimfiyat"+(no)).val(birimfiyat.toFixed(2));
                $("#gfiyat"+(no)).val(fiyat.toFixed(2));
                $("#urunadi"+(no)).find("option:selected").data('id',fiyat);
                $("#urunadi"+(no)).find("option:selected").data('fiyat',birimfiyat);
                var parabirimiid = $("#urunadi"+(no)).find("option:selected").data('birim');
                var toplamtutar = 0;
                $('select.urunadi').each(function(){
                    var urunno = $(this).closest('.sayaclar_ek').children('.no').val();
                    var urunfiyat = parseFloat($(this).find("option:selected").data('id')) || 0.00;
                    var urunmiktar = parseFloat($('#miktar'+(urunno)).val());
                    if($("#ucretsiz"+(urunno)).is(':checked')){
                        urunfiyat = 0;
                        $("#fiyat"+(urunno)).maskMoney('mask',urunfiyat.toFixed(2)*100);
                    }
                    toplamtutar +=(urunfiyat*urunmiktar);
                });
                var kur=1;
                if(parabirimiid!==undefined){
                    if(parabirimiid!==1){
                        if(parabirimiid===2)
                            kur=parseFloat($('#euro').val()).toFixed(4);
                        else if(parabirimiid===3)
                            kur=parseFloat($('#dolar').val()).toFixed(4);
                        else
                            kur=parseFloat($('#sterlin').val()).toFixed(4);
                    }
                }
                toplamtutar*=kur;
                var kdv=((toplamtutar*18)/118);
                var tutar=(toplamtutar-kdv);
                //toplamtutar=Math.round(toplamtutar*10)/10;
                //kdv=toplamtutar-tutar;
                var birim=$('#birim').val();
                $('.tutar').html('<b>'+tutar.toFixed(2)+' '+birim+'</b>');
                $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+birim+'</b>');
                $('.toplamtutar').html('<b>'+toplamtutar.toFixed(2)+' '+birim+'</b>');
                $('#tutar').val(tutar.toFixed(2));
                $('#kdvtutar').val(kdv.toFixed(2));
                $('#toplamtutar').val(toplamtutar.toFixed(2));
                $('#geneltutar').val(tutar.toFixed(2));
                $('#genelkdv').val(kdv.toFixed(2));
                $('#geneltoplam').val(toplamtutar.toFixed(2));
                $('#odemesekli').select2('val','').trigger('change');
            });
            $('.miktar').on('change', function () {
                var no = $(this).closest('.sayaclar_ek').children('.no').val();
                var parabirimiid = $("#urunadi"+(no)).find("option:selected").data('birim');
                $(this).closest('.sayaclar_ek').children('div').children('h4').children('.accordion-toggle').text(($("#urunadi"+(no)).select2('data').text)+' - '+($("#miktar"+(no)).val())+' ADET');
                var toplamtutar = 0;
                $('select.urunadi').each(function(){
                    var urunno = $(this).closest('.sayaclar_ek').children('.no').val();
                    var urunfiyat = parseFloat($(this).find("option:selected").data('id')) || 0.00;
                    var urunmiktar = parseFloat($('#miktar'+(urunno)).val());
                    if($("#ucretsiz"+(urunno)).is(':checked')){
                        urunfiyat = 0;
                        $("#ucretsiz"+(urunno)).maskMoney('mask',urunfiyat.toFixed(2)*100);
                    }
                    toplamtutar +=(urunfiyat*urunmiktar);
                });
                var kur=1;
                if(parabirimiid!==undefined){
                    if(parabirimiid!==1){
                        if(parabirimiid===2)
                            kur=parseFloat($('#euro').val()).toFixed(4);
                        else if(parabirimiid===3)
                            kur=parseFloat($('#dolar').val()).toFixed(4);
                        else
                            kur=parseFloat($('#sterlin').val()).toFixed(4);
                    }
                }
                toplamtutar*=kur;
                var kdv=((toplamtutar*18)/118);
                var tutar=(toplamtutar-kdv);
                //toplamtutar=Math.round(toplamtutar*10)/10;
                //kdv=toplamtutar-tutar;
                var birim=$('#birim').val();
                $('.tutar').html('<b>'+tutar.toFixed(2)+' '+birim+'</b>');
                $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+birim+'</b>');
                $('.toplamtutar').html('<b>'+toplamtutar.toFixed(2)+' '+birim+'</b>');
                $('#tutar').val(tutar.toFixed(2));
                $('#kdvtutar').val(kdv.toFixed(2));
                $('#toplamtutar').val(toplamtutar.toFixed(2));
                $('#geneltutar').val(tutar.toFixed(2));
                $('#genelkdv').val(kdv.toFixed(2));
                $('#geneltoplam').val(toplamtutar.toFixed(2));
                $('#odemesekli').select2('val','').trigger('change');
            });
            $('.ucretsiz').on('change', function () {
                var ucretsizler = "";
                var no = $(this).closest('.sayaclar_ek').children('.no').val();
                var parabirimiid = $("#urunadi"+(no)).find("option:selected").data('birim');
                var toplamtutar = 0;
                $('select.urunadi').each(function(){
                    var ucretsizdurum = 0;
                    var urunno = $(this).closest('.sayaclar_ek').children('.no').val();
                    var urunfiyat = parseFloat($(this).find("option:selected").data('id')) || 0.00;
                    var urunmiktar = parseFloat($('#miktar'+(urunno)).val());
                    if($("#ucretsiz"+urunno).is(':checked')){
                        urunfiyat = 0;
                        ucretsizdurum = 1;
                        $("#fiyat"+(urunno)).maskMoney('mask',urunfiyat.toFixed(2)*100);
                    }
                    ucretsizler+=(ucretsizler==="" ? "" : ",")+ucretsizdurum;
                    toplamtutar +=(urunfiyat*urunmiktar);
                });
                var kur=1;
                if(parabirimiid!==undefined){
                    if(parabirimiid!==1){
                        if(parabirimiid===2)
                            kur=parseFloat($('#euro').val()).toFixed(4);
                        else if(parabirimiid===3)
                            kur=parseFloat($('#dolar').val()).toFixed(4);
                        else
                            kur=parseFloat($('#sterlin').val()).toFixed(4);
                    }
                }
                toplamtutar*=kur;
                var kdv=((toplamtutar*18)/118);
                var tutar=(toplamtutar-kdv);
                //toplamtutar=Math.round(toplamtutar*10)/10;
                //kdv=toplamtutar-tutar;
                var birim=$('#birim').val();
                $('.tutar').html('<b>'+tutar.toFixed(2)+' '+birim+'</b>');
                $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+birim+'</b>');
                $('.toplamtutar').html('<b>'+toplamtutar.toFixed(2)+' '+birim+'</b>');
                $('#tutar').val(tutar.toFixed(2));
                $('#kdvtutar').val(kdv.toFixed(2));
                $('#toplamtutar').val(toplamtutar.toFixed(2));
                $('#ucretsizler').val(ucretsizler);
                $('#geneltutar').val(tutar.toFixed(2));
                $('#genelkdv').val(kdv.toFixed(2));
                $('#geneltoplam').val(toplamtutar.toFixed(2));
                $('#odemesekli').select2('val','').trigger('change');
            });
            $('.baglantidurum').on('change', function () {
                var baglantidurumlari = "";
                $('select.urunadi').each(function(){
                    var urunno = $(this).closest('.sayaclar_ek').children('.no').val();
                    var baglantidurum = $("#baglantidurum"+(urunno)).val();
                    baglantidurumlari+=(baglantidurumlari==="" ? "" : ",")+baglantidurum;
                    if(baglantidurum==='1'){
                        $('.baglantidurum'+(urunno)).removeClass('hide');
                    }else{
                        $('.baglantidurum'+(urunno)).addClass('hide');
                    }
                });
                $('#baglantidurumlari').val(baglantidurumlari);
            });
            $('.satirsil').click(function(){
                if($('.sayaclar .sayaclar_ek').size()>0){
                    var sayac=$(this).closest('.sayaclar_ek');
                    var adet = sayac.children('.adet').val();
                    sayac.children('.adet').val(0);
                    sayac.remove();
                    cnt-=adet;
                    count-=adet;
                    $("#count").val(count);
                    if(count===0)
                        $(".kaydet").prop('disabled',true);
                    var j=0;
                    $('.sayaclar .sayaclar_ek').each(function(){
                        var id=$( this ).children('.no').val();
                        $( this).children('div').children('h4').children('.accordion-toggle').attr('href','#collapse_'+j);
                        $( this).children('.panel-collapse').attr('id','collapse_'+j);
                        $( this).children('div').children('div').children('div').children('div').children('.urunadi').attr('id','urunadi'+j).attr('name','urunadi[]');
                        $( this).children('div').children('div').children('div').children('div').children('.urunadi').removeClass('urunadi'+id).addClass('urunadi'+j);
                        $( this).children('div').children('div').children('div').children('div').children('.fiyat').attr('id','fiyat'+j+'').attr('name','fiyat[]');
                        $( this).children('div').children('div').children('div').children('div').children('.birimfiyat').attr('id','birimfiyat'+j+'').attr('name','birimfiyat[]');
                        $( this).children('div').children('div').children('div').children('div').children('.gfiyat').attr('id','gfiyat'+j+'').attr('name','gfiyat[]');
                        $( this).children('div').children('div').children('div').children('div').children('.miktar').attr('id','miktar'+j+'').attr('name','miktar[]');
                        $( this).children('div').children('div').children('div').children('div').children('.ucretsiz').attr('id','ucretsiz'+j+'').attr('value',j).attr('name','ucretsiz[]');
                        $( this).children('div').children('div').children('.baglantidurum'+id).removeClass('baglantidurum'+id).addClass('baglantidurum'+j);
                        $( this).children('div').children('div').children('div').children('div').children('.abonesayac').removeClass('abonesayac'+id).addClass('abonesayac'+j).attr('id','abonesayac'+j+'').attr('name','abonesayac['+j+'][]');
                        $( this).children('div').children('div').children('div').children('.baglantidurum').attr('id','baglantidurum'+j+'').attr('name','baglantidurum[]');
                        $( this ).children('.no').val(j);
                        j++;
                    });
                    var birim = $("#birim").val();
                    var parabirimiid = 1;
                    var toplamtutar = 0;
                    var ucretsizler="";
                    var baglantidurumlari="";
                    $('select.urunadi').each(function(){
                        var ucretsizdurum = 0;
                        var urunno = $(this).closest('.sayaclar_ek').children('.no').val();
                        var baglantidurum = $("#baglantidurum"+(urunno)).val();
                        var urunfiyat = parseFloat($(this).find("option:selected").data('id')) || 0.00;
                        parabirimiid = $(this).find("option:selected").data('birim');
                        var urunmiktar = parseFloat($('#miktar'+(urunno)).val());
                        if($("#ucretsiz"+(urunno)).is(':checked')){
                            urunfiyat = 0;
                            ucretsizdurum = 1;
                            $("#fiyat"+(urunno)).maskMoney('mask',urunfiyat.toFixed(2)*100);
                        }
                        ucretsizler+=(ucretsizler==="" ? "" : ",")+ucretsizdurum;
                        baglantidurumlari+=(baglantidurumlari==="" ? "" : ",")+baglantidurum;
                        toplamtutar +=(urunfiyat*urunmiktar);
                    });
                    var kur=1;
                    if(parabirimiid!==undefined){
                        if(parabirimiid!==1){
                            if(parabirimiid===2)
                                kur=parseFloat($('#euro').val()).toFixed(4);
                            else if(parabirimiid===3)
                                kur=parseFloat($('#dolar').val()).toFixed(4);
                            else
                                kur=parseFloat($('#sterlin').val()).toFixed(4);
                        }
                    }
                    toplamtutar*=kur;
                    var kdv=((toplamtutar*18)/118);
                    var tutar=(toplamtutar-kdv);
                    //toplamtutar=Math.round(toplamtutar*10)/10;
                    //kdv=toplamtutar-tutar;
                    $('.tutar').html('<b>'+tutar.toFixed(2)+' '+birim+'</b>');
                    $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+birim+'</b>');
                    $('.toplamtutar').html('<b>'+toplamtutar.toFixed(2)+' '+birim+'</b>');
                    $('#tutar').val(tutar.toFixed(2));
                    $('#kdvtutar').val(kdv.toFixed(2));
                    $('#toplamtutar').val(toplamtutar.toFixed(2));
                    $('#ucretsizler').val(ucretsizler);
                    $('#baglantidurumlari').val(baglantidurumlari);
                    $('#geneltutar').val(tutar.toFixed(2));
                    $('#genelkdv').val(kdv.toFixed(2));
                    $('#geneltoplam').val(toplamtutar.toFixed(2));
                    $('#odemesekli').select2('val','').trigger('change');
                }
            });
            if(count>0){
                $(".kaydet").prop('disabled',false);
            }
            else{
                $(".kaydet").prop('disabled',true);
            }
            $("#count").val(count);
            $("select").on("select2-close", function () { $(this).valid(); });
        });
        $(".tumsil").click(function(){
            while($('.sayaclar .sayaclar_ek').size()>0){
                $('.sayaclar .sayaclar_ek:last').remove();
                cnt--;
                count--;
            }
            $("#count").val(0);
            $(".kaydet").prop('disabled',true);
            var parabirimi = $("#birim").val();
            $('.tutar').html('<b>0.00 '+parabirimi+'</b>');
            $('.kdvtutar').html('<b>0.00 '+parabirimi+'/b>');
            $('.toplamtutar').html('<b>0.00 '+parabirimi+'</b>');
            $('#tutar').val(0.00);
            $('#kdvtutar').val(0.00);
            $('#toplamtutar').val(0.00);
            $('#ucretsizler').val("");
            $('#baglantidurumlari').val("");
            $('#geneltutar').val(0.00);
            $('#genelkdv').val(0.00);
            $('#geneltoplam').val(0.00);
            $('#odemesekli').select2('val','').trigger('change');
        });
        $('.getir').click(function(){
            var kriter = $("#kriter").val();
            var kriterdeger = $("#kriterdeger").val();
            var subekodu = $("#subekodu").val();
            var cariadi = $("#cariadi").val();
            var faturano = $("#eskifaturano").val();
            var status = 0;
            if (kriterdeger !== "" && kriter !== "" ) {
                $.blockUI();
                $.getJSON("{{ URL::to('sube/satisabonebilgi') }}",{tip:kriter,kriter:kriterdeger,subekodu:subekodu,faturano:faturano}, function (event) {
                    if (event.durum)
                    {
                        if(event.count>1){
                            abonebilgi = event.abonebilgi;
                            oTable.clear().draw();
                            $.each(abonebilgi, function (index) {
                                oTable.row.add([abonebilgi[index].id,((abonebilgi[index].serino)==null ? '' : abonebilgi[index].serino),abonebilgi[index].adisoyadi,
                                    ((abonebilgi[index].faturaadresi)==null ? '' : abonebilgi[index].faturaadresi)])
                                    .draw();
                            });
                            $('#abonelistesi').modal('show');
                        }else {
                            var abonebilgi = event.abonebilgi[0];
                            if(cariadi!==abonebilgi.netsiscari_id)
                                $("#cariadi").select2('val',abonebilgi.netsiscari_id).trigger('change');
                            $('#abone').val(abonebilgi.id);
                            $('.abone').text(abonebilgi.adisoyadi);
                            $('#telefon').val(abonebilgi.telefon).trigger('input');
                            $('.uretimyer').text(abonebilgi.yeradi);
                            $('#adres').val(abonebilgi.faturaadresi).valid();
                            $('#tckimlikno').val(abonebilgi.tckimlikno);
                            $('#faturaadresi').val(abonebilgi.faturaadresi).valid();
                            $('#faturano').val(abonebilgi.faturano);
                            status = 1;
                        }
                    } else {
                        toastr[event.type](event.text,event.title);
                    }
                    $.unblockUI();
                    if(status)
                        $('#abone').trigger('change');
                });
            }else{
                toastr['warning']('Kriter Bilgisi Girilmemiş', 'Kriter Hatası');
            }
        });
        $('#listesec').click(function () {
            var aboneid = $('#secilenabone').val();
            var subekodu = $("#subekodu").val();
            var cariadi = $("#cariadi").val();
            var faturano = $("#eskifaturano").val();
            if ( aboneid !== "") {
                $.blockUI();
                $.getJSON("{{ URL::to('sube/satislistebilgigetir') }}", {id: aboneid,subekodu: subekodu,faturano:faturano}, function (event) {
                    if (event.durum) {
                        var abonebilgi = event.abonebilgi;
                        if(cariadi!==abonebilgi.netsiscari_id)
                            $("#cariadi").select2('val',abonebilgi.netsiscari_id).trigger('change');
                        $('#abone').val(abonebilgi.id);
                        $('.abone').text(abonebilgi.adisoyadi);
                        $('#telefon').val(abonebilgi.telefon).trigger('input');
                        $('.uretimyer').text(abonebilgi.yeradi);
                        $('#adres').val(abonebilgi.faturaadresi).valid();
                        $('#tckimlikno').val(abonebilgi.tckimlikno);
                        $('#faturaadresi').val(abonebilgi.faturaadresi).valid();
                        $('#faturano').val(abonebilgi.faturano);
                    } else {
                        $('#abone').val('');
                        $('.abone').text('');
                        $('#telefon').val('').trigger('input');
                        $('.uretimyer').text('');
                        $('#adres').val('').valid();
                        $('#faturaadresi').val('').valid();
                        $('#tckimlikno').val('');
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                    $('#abone').trigger('change');
                    $('#abonelistesi').modal('hide');
                });
            } else {
                toastr['warning']('Kriter Bilgisi Girilmemiş', 'Kriter Hatası');
            }
        });
        @if((int)Input::old('count')>0)
        var aboneid = $('#abone').val();
        if(aboneid!==""){
            $.blockUI();
            $.getJSON("{{ URL::to('sube/abonesayac') }}",{id:aboneid,satisid:satisid,urun:1},function(event){
                if(event.durum){
                    var abone=event.abone;
                    var sayaclar=event.abonesayaclari;
                    var urunler=event.urunler;
                    $('.abone').text(abone.adisoyadi);
                    $('#telefon').val(abone.telefon).trigger('input');
                    $('.uretimyer').text(abone.uretimyer.yeradi);
                    $('#adres').val(abone.faturaadresi).valid();
                    $('#tckimlikno').val(abone.tckimlikno);
                    $(".abonesayac").empty();
                    $('#faturaadresi').val(abone.faturaadresi).valid();
                    var ucretsizler=$('#ucretsizler').val();
                    ucretsizler=ucretsizler.split(',');
                    var baglantidurumlari=$('#baglantidurumlari').val();
                    baglantidurumlari=baglantidurumlari.split(',');
                    for(var i=0;i<count;i++){
                        $.each(sayaclar, function (index) {
                            $( "#abonesayac"+(i)).append('<option value="' + sayaclar[index].id + '"> ' + sayaclar[index].serino + '</option>');
                        });
                        $( "#abonesayac"+(i)).select2({
                            placeholder: "Sayaç Seçin",
                            allowClear: true
                        });
                        var urunadi=$("#urunadi"+(i)).val();
                        $("#urunadi" + (i)).empty();
                        $("#urunadi" + (i)).append('<option value="">Seçiniz...</option>');
                        $.each(urunler, function (index) {
                            if (urunadi === urunler[index].id) {
                                $("#urunadi" + (i)).append('<option data-id="' + $("#fiyat" + (i)).val() + '" data-fiyat="' + $("#birimfiyat" + (i)).val() + '" data-baglanti="' + urunler[index].baglanti + '" ' +
                                    'data-birim="' + urunler[index].parabirimi_id + '" data-value="' + urunler[index].parabirimi.birimi + '" value="' + urunler[index].id + '">' +
                                    urunler[index].netsisstokkod.kodu+' - '+urunler[index].urunadi + ' ('+parseInt(urunler[index].stok.BAKIYE)+')</option>');
                            } else {
                                $("#urunadi" + (i)).append('<option data-id="' + 0.00 + '" data-fiyat="' + 0.00 + '" data-baglanti="' + urunler[index].baglanti + '" ' +
                                    'data-birim="' + urunler[index].parabirimi_id + '" data-value="' + urunler[index].parabirimi.birimi + '" value="' + urunler[index].id + '">' +
                                    urunler[index].netsisstokkod.kodu+' - '+urunler[index].urunadi + ' ('+parseInt(urunler[index].stok.BAKIYE)+')</option>');
                            }
                        });
                        $("#urunadi"+(i)).select2("val",urunadi);
                        $("#ucretsiz"+(i)).attr('checked',ucretsizler[i]==="1");
                        $("#baglantidurum"+(i)).val(baglantidurumlari[i]).trigger('change');
                        var parabirimi = $("#urunadi"+(i)).find("option:selected").data('value');
                        $("#fiyat"+(i)).maskMoney({suffix: ' '+parabirimi,affixesStay:true, allowZero:true});

                        var abonesayaclar;
                        if($( "#abonesayaclar"+(i)).hasClass( "abonesayaclar"+(i) ))
                        {
                            abonesayaclar =document.getElementById("abonesayaclar"+(i)).innerHTML;
                            abonesayaclar = abonesayaclar.replace(/\s+/g,' ').trim();
                            abonesayaclar = abonesayaclar.split(",");
                            $('#abonesayac'+(i)).select2("val",abonesayaclar);
                        }
                        if($( "#abonesayaclarekli"+(i) ).hasClass( "abonesayaclarekli"+(i) ))
                        {
                            abonesayaclar =document.getElementById("abonesayaclarekli"+(i)).innerHTML;
                            abonesayaclar = abonesayaclar.split(",");
                            $('#abonesayac'+(i)).select2("val",abonesayaclar);
                        }
                    }
                    for(i=0;i<count;i++){
                        urunadi=$("#urunadi"+(i)).val();
                        for(var j=0;j<count;j++){
                            if(i!==j){
                                $.each(urunler, function (index) {
                                    if (urunadi === urunler[index].id) {
                                        $("#urunadi"+(j)).find("option[value="+urunadi+"]").data('id',$("#fiyat" + (i)).val());
                                        $("#urunadi"+(j)).find("option[value="+urunadi+"]").data('fiyat',$("#birimfiyat" + (i)).val());
                                    }
                                });
                            }
                        }
                    }
                }else{
                    toastr['' + event.type + '']('' + event.text + '', '' + event.title + '');
                }
                $('.sayaclar').find('input:checkbox').uniform();
                $.uniform.update();
                $.unblockUI();
            });
        }
        $('.urunadi').on('change', function () {
            //var id = $(this).val();
            var aboneid = $('#abone').val();
            var satisid = $('#satisid').val();
            var no = $(this).closest('.sayaclar_ek').children('.no').val();
            $(this).closest('.sayaclar_ek').children('div').children('h4').children('.accordion-toggle').text(($("#urunadi"+(no)).select2('data').text)+' - '+($("#miktar"+(no)).val())+' ADET');
            var baglantidurum=$(this).find("option:selected").data('baglanti');
            $("#baglantidurum"+(no)).val(baglantidurum).trigger('change');
            if(baglantidurum){
                if(aboneid!==""){
                    $.blockUI();
                    $.getJSON("{{ URL::to('sube/abonesayac') }}",{id:aboneid,satisid:satisid},function(event){
                        if (event.durum) {
                            var sayaclar = event.abonesayaclari;
                            $("#abonesayac" + (no)).empty();
                            $.each(sayaclar, function (index) {
                                $("#abonesayac" + (no)).append('<option value="' + sayaclar[index].id + '"> ' + sayaclar[index].serino + '</option>');
                            });
                            $("#abonesayac" + (no)).select2({
                                placeholder: "Sayaç Seçin",
                                allowClear: true
                            });
                        } else {
                            toastr['' + event.type + '']('' + event.text + '', '' + event.title + '');
                        }
                        $.unblockUI();
                    });
                }
                $('.baglantidurum'+(no)).removeClass('hide');
            }else{
                $('.baglantidurum'+(no)).addClass('hide');
            }
            var fiyat = parseFloat($(this).find("option:selected").data('id')) || 0.00;
            var birimfiyat = parseFloat($(this).find("option:selected").data('fiyat')) || 0.00;
            var parabirimi = $(this).find("option:selected").data('value');
            var parabirimiid = $(this).find("option:selected").data('birim');
            if(parabirimi==null){
                parabirimi='';
            }
            $("#fiyat"+(no)).maskMoney({suffix: ' '+parabirimi,affixesStay:true, allowZero:true});
            $('#fiyat'+(no)).maskMoney('mask',fiyat.toFixed(2)*100);
            $("#birimfiyat"+(no)).val(birimfiyat.toFixed(2));
            $("#gfiyat"+(no)).val(fiyat.toFixed(2));
            var toplamtutar = 0;
            $('select.urunadi').each(function(){
                var urunno = $(this).closest('.sayaclar_ek').children('.no').val();
                var urunfiyat = parseFloat($(this).find("option:selected").data('id')) || 0.00;
                var urunmiktar = parseFloat($('#miktar'+(urunno)).val());
                if($("#ucretsiz"+(urunno)).is(':checked')){
                    urunfiyat = 0;
                    $("#fiyat"+(urunno)).maskMoney('mask',urunfiyat.toFixed(2)*100);
                }
                toplamtutar +=(urunfiyat*urunmiktar);
            });
            var kur=1;
            if(parabirimiid!==undefined){
                if(parabirimiid!==1){
                    if(parabirimiid===2)
                        kur=parseFloat($('#euro').val()).toFixed(4);
                    else if(parabirimiid===3)
                        kur=parseFloat($('#dolar').val()).toFixed(4);
                    else
                        kur=parseFloat($('#sterlin').val()).toFixed(4);
                }
            }
            toplamtutar*=kur;
            var kdv=((toplamtutar*18)/118);
            var tutar=(toplamtutar-kdv);
            //toplamtutar=Math.round(toplamtutar*10)/10;
            //kdv=toplamtutar-tutar;
            var birim=$('#birim').val();
            $('.tutar').html('<b>'+tutar.toFixed(2)+' '+birim+'</b>');
            $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+birim+'</b>');
            $('.toplamtutar').html('<b>'+toplamtutar.toFixed(2)+' '+birim+'</b>');
            $('#tutar').val(tutar.toFixed(2));
            $('#kdvtutar').val(kdv.toFixed(2));
            $('#toplamtutar').val(toplamtutar.toFixed(2));
            $('#geneltutar').val(tutar.toFixed(2));
            $('#genelkdv').val(kdv.toFixed(2));
            $('#geneltoplam').val(toplamtutar.toFixed(2));

        });
        $('.fiyat').on('change', function () {
            var fiyat = parseFloat($(this).val());
            var birimfiyat = ((fiyat*100)/118);
            var no = $(this).closest('.sayaclar_ek').children('.no').val();
            $("#birimfiyat"+(no)).val(birimfiyat.toFixed(2));
            $("#gfiyat"+(no)).val(fiyat.toFixed(2));
            $("#urunadi"+(no)).find("option:selected").data('id',fiyat);
            $("#urunadi"+(no)).find("option:selected").data('fiyat',birimfiyat);
            var parabirimiid = $("#urunadi"+(no)).find("option:selected").data('birim');
            var toplamtutar = 0;
            $('select.urunadi').each(function(){
                var urunno = $(this).closest('.sayaclar_ek').children('.no').val();
                var urunfiyat = parseFloat($(this).find("option:selected").data('id')) || 0.00;
                var urunmiktar = parseFloat($('#miktar'+(urunno)).val());
                if($("#ucretsiz"+(urunno)).is(':checked')){
                    urunfiyat = 0;
                    $("#fiyat"+(urunno)).maskMoney('mask',urunfiyat.toFixed(2)*100);
                }
                toplamtutar +=(urunfiyat*urunmiktar);
            });
            var kur=1;
            if(parabirimiid!==undefined){
                if(parabirimiid!==1){
                    if(parabirimiid===2)
                        kur=parseFloat($('#euro').val()).toFixed(4);
                    else if(parabirimiid===3)
                        kur=parseFloat($('#dolar').val()).toFixed(4);
                    else
                        kur=parseFloat($('#sterlin').val()).toFixed(4);
                }
            }
            toplamtutar*=kur;
            var kdv=((toplamtutar*18)/118);
            var tutar=(toplamtutar-kdv);
            //toplamtutar=Math.round(toplamtutar*10)/10;
            //kdv=toplamtutar-tutar;
            var birim=$('#birim').val();
            $('.tutar').html('<b>'+tutar.toFixed(2)+' '+birim+'</b>');
            $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+birim+'</b>');
            $('.toplamtutar').html('<b>'+toplamtutar.toFixed(2)+' '+birim+'</b>');
            $('#tutar').val(tutar.toFixed(2));
            $('#kdvtutar').val(kdv.toFixed(2));
            $('#toplamtutar').val(toplamtutar.toFixed(2));
            $('#geneltutar').val(tutar.toFixed(2));
            $('#genelkdv').val(kdv.toFixed(2));
            $('#geneltoplam').val(toplamtutar.toFixed(2));
            $('#odemesekli').select2('val','').trigger('change');
        });
        $('.miktar').on('change', function () {
            var no = $(this).closest('.sayaclar_ek').children('.no').val();
            var parabirimiid = $("#urunadi"+(no)).find("option:selected").data('birim');
            $(this).closest('.sayaclar_ek').children('div').children('h4').children('.accordion-toggle').text(($("#urunadi"+(no)).select2('data').text)+' - '+($("#miktar"+(no)).val())+' ADET');
            var toplamtutar = 0;
            $('select.urunadi').each(function(){
                var urunno = $(this).closest('.sayaclar_ek').children('.no').val();
                var urunfiyat = parseFloat($(this).find("option:selected").data('id')) || 0.00;
                var urunmiktar = parseFloat($('#miktar'+(urunno)).val());
                if($("#ucretsiz"+(urunno)).is(':checked')){
                    urunfiyat = 0;
                    $("#fiyat"+(urunno)).maskMoney('mask',urunfiyat.toFixed(2)*100);
                }
                toplamtutar +=(urunfiyat*urunmiktar);
            });
            var kur=1;
            if(parabirimiid!==undefined){
                if(parabirimiid!==1){
                    if(parabirimiid===2)
                        kur=parseFloat($('#euro').val()).toFixed(4);
                    else if(parabirimiid===3)
                        kur=parseFloat($('#dolar').val()).toFixed(4);
                    else
                        kur=parseFloat($('#sterlin').val()).toFixed(4);
                }
            }
            toplamtutar*=kur;
            var kdv=((toplamtutar*18)/118);
            var tutar=(toplamtutar-kdv);
            //toplamtutar=Math.round(toplamtutar*10)/10;
            //kdv=toplamtutar-tutar;
            var birim=$('#birim').val();
            $('.tutar').html('<b>'+tutar.toFixed(2)+' '+birim+'</b>');
            $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+birim+'</b>');
            $('.toplamtutar').html('<b>'+toplamtutar.toFixed(2)+' '+birim+'</b>');
            $('#tutar').val(tutar.toFixed(2));
            $('#kdvtutar').val(kdv.toFixed(2));
            $('#toplamtutar').val(toplamtutar.toFixed(2));
            $('#geneltutar').val(tutar.toFixed(2));
            $('#genelkdv').val(kdv.toFixed(2));
            $('#geneltoplam').val(toplamtutar.toFixed(2));
            $('#odemesekli').select2('val','').trigger('change');
        });
        $('.ucretsiz').on('change', function () {
            var ucretsizler = "";
            var no = $(this).closest('.sayaclar_ek').children('.no').val();
            var parabirimiid = $("#urunadi"+(no)).find("option:selected").data('birim');
            var toplamtutar = 0;
            $('select.urunadi').each(function(){
                var ucretsizdurum = 0;
                var urunno = $(this).closest('.sayaclar_ek').children('.no').val();
                var urunfiyat = parseFloat($(this).find("option:selected").data('id')) || 0.00;
                var urunmiktar = parseFloat($('#miktar'+(urunno)).val());
                if($("#ucretsiz"+(urunno)).is(':checked')){
                    urunfiyat = 0;
                    ucretsizdurum = 1;
                    $("#fiyat"+(urunno)).maskMoney('mask',urunfiyat.toFixed(2)*100);
                }
                ucretsizler+=(ucretsizler==="" ? "" : ",")+ucretsizdurum;
                toplamtutar +=(urunfiyat*urunmiktar);
            });
            var kur=1;
            if(parabirimiid!==undefined){
                if(parabirimiid!==1){
                    if(parabirimiid===2)
                        kur=parseFloat($('#euro').val()).toFixed(4);
                    else if(parabirimiid===3)
                        kur=parseFloat($('#dolar').val()).toFixed(4);
                    else
                        kur=parseFloat($('#sterlin').val()).toFixed(4);
                }
            }
            toplamtutar*=kur;
            var kdv=((toplamtutar*18)/118);
            var tutar=(toplamtutar-kdv);
            //toplamtutar=Math.round(toplamtutar*10)/10;
            //kdv=toplamtutar-tutar;
            var birim=$('#birim').val();
            $('.tutar').html('<b>'+tutar.toFixed(2)+' '+birim+'</b>');
            $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+birim+'</b>');
            $('.toplamtutar').html('<b>'+toplamtutar.toFixed(2)+' '+birim+'</b>');
            $('#tutar').val(tutar.toFixed(2));
            $('#kdvtutar').val(kdv.toFixed(2));
            $('#toplamtutar').val(toplamtutar.toFixed(2));
            $('#ucretsizler').val(ucretsizler);
            $('#geneltutar').val(tutar.toFixed(2));
            $('#genelkdv').val(kdv.toFixed(2));
            $('#geneltoplam').val(toplamtutar.toFixed(2));
            $('#odemesekli').select2('val','').trigger('change');
        });
        $('.baglantidurum').on('change', function () {
            var baglantidurumlari = "";
            $('select.urunadi').each(function(){
                var urunno = $(this).closest('.sayaclar_ek').children('.no').val();
                var baglantidurum = $("#baglantidurum"+(urunno)).val();
                baglantidurumlari+=(baglantidurumlari==="" ? "" : ",")+baglantidurum;
                if(baglantidurum==='1'){
                    $('.baglantidurum'+(urunno)).removeClass('hide');
                }else{
                    $('.baglantidurum'+(urunno)).addClass('hide');
                }
            });
            $('#baglantidurumlari').val(baglantidurumlari);
        });
        $('.satirsil').click(function(){
            if($('.sayaclar .sayaclar_ek').size()>0){
                var sayac=$(this).closest('.sayaclar_ek');
                var adet = sayac.children('.adet').val();
                sayac.children('.adet').val(0);
                sayac.remove();
                cnt-=adet;
                count-=adet;
                $("#count").val(count);
                if(count===0)
                    $(".kaydet").prop('disabled',true);
                var j=0;
                $('.sayaclar .sayaclar_ek').each(function(){
                    var id=$( this ).children('.no').val();
                    $( this).children('div').children('h4').children('.accordion-toggle').attr('href','#collapse_'+j);
                    $( this).children('.panel-collapse').attr('id','collapse_'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.urunadi').attr('id','urunadi'+j).attr('name','urunadi[]');
                    $( this).children('div').children('div').children('div').children('div').children('.urunadi').removeClass('urunadi'+id).addClass('urunadi'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.fiyat').attr('id','fiyat'+j+'').attr('name','fiyat[]');
                    $( this).children('div').children('div').children('div').children('div').children('.birimfiyat').attr('id','birimfiyat'+j+'').attr('name','birimfiyat[]');
                    $( this).children('div').children('div').children('div').children('div').children('.gfiyat').attr('id','gfiyat'+j+'').attr('name','gfiyat[]');
                    $( this).children('div').children('div').children('div').children('div').children('.miktar').attr('id','miktar'+j+'').attr('name','miktar[]');
                    $( this).children('div').children('div').children('div').children('div').children('.ucretsiz').attr('id','ucretsiz'+j+'').attr('value',j).attr('name','ucretsiz[]');
                    $( this).children('div').children('div').children('.baglantidurum'+id).removeClass('baglantidurum'+id).addClass('baglantidurum'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.abonesayac').removeClass('abonesayac'+id).addClass('abonesayac'+j).attr('id','abonesayac'+j+'').attr('name','abonesayac['+j+'][]');
                    $( this).children('div').children('div').children('div').children('.baglantidurum').attr('id','baglantidurum'+j+'').attr('name','baglantidurum[]');
                    $( this ).children('.no').val(j);
                    j++;
                });
                var birim = $("#birim").val();
                var parabirimiid = 1;
                var toplamtutar = 0;
                var ucretsizler="";
                var baglantidurumlari="";
                $('select.urunadi').each(function(){
                    var ucretsizdurum = 0;
                    var urunno = $(this).closest('.sayaclar_ek').children('.no').val();
                    var baglantidurum = $("#baglantidurum"+(urunno)).val();
                    var urunfiyat = parseFloat($(this).find("option:selected").data('id')) || 0.00;
                    parabirimiid = $(this).find("option:selected").data('birim');
                    var urunmiktar = parseFloat($('#miktar'+(urunno)).val());
                    if($("#ucretsiz"+(urunno)).is(':checked')){
                        urunfiyat = 0;
                        ucretsizdurum = 1;
                        $("#fiyat"+(urunno)).maskMoney('mask',urunfiyat.toFixed(2)*100);
                    }
                    ucretsizler+=(ucretsizler==="" ? "" : ",")+ucretsizdurum;
                    baglantidurumlari+=(baglantidurumlari==="" ? "" : ",")+baglantidurum;
                    toplamtutar +=(urunfiyat*urunmiktar);
                });
                var kur=1;
                if(parabirimiid!==undefined){
                    if(parabirimiid!==1){
                        if(parabirimiid===2)
                            kur=parseFloat($('#euro').val()).toFixed(4);
                        else if(parabirimiid===3)
                            kur=parseFloat($('#dolar').val()).toFixed(4);
                        else
                            kur=parseFloat($('#sterlin').val()).toFixed(4);
                    }
                }
                toplamtutar*=kur;
                var kdv=((toplamtutar*18)/118);
                var tutar=(toplamtutar-kdv);
                //toplamtutar=Math.round(toplamtutar*10)/10;
                //kdv=toplamtutar-tutar;
                $('.tutar').html('<b>'+tutar.toFixed(2)+' '+birim+'</b>');
                $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+birim+'</b>');
                $('.toplamtutar').html('<b>'+toplamtutar.toFixed(2)+' '+birim+'</b>');
                $('#tutar').val(tutar.toFixed(2));
                $('#kdvtutar').val(kdv.toFixed(2));
                $('#toplamtutar').val(toplamtutar.toFixed(2));
                $('#ucretsizler').val(ucretsizler);
                $('#baglantidurumlari').val(baglantidurumlari);
                $('#geneltutar').val(tutar.toFixed(2));
                $('#genelkdv').val(kdv.toFixed(2));
                $('#geneltoplam').val(toplamtutar.toFixed(2));
                $('#odemesekli').select2('val','').trigger('change');
            }
        });
        $(".miktar").inputmask("mask", {
            mask:"9",repeat:3,greedy:!1
        });
        @elseif(count($sayacsatis->urunler)>0)
        $('.urunadi').on('change', function () {
            //var id = $(this).val();
            var aboneid = $('#abone').val();
            var satisid = $('#satisid').val();
            var no = $(this).closest('.sayaclar_ek').children('.no').val();
            $(this).closest('.sayaclar_ek').children('div').children('h4').children('.accordion-toggle').text(($("#urunadi"+(no)).select2('data').text)+' - '+($("#miktar"+(no)).val())+' ADET');
            var baglantidurum=$(this).find("option:selected").data('baglanti');
            $("#baglantidurum"+(no)).val(baglantidurum).trigger('change');
            if(baglantidurum){
                if(aboneid!==""){
                    $.blockUI();
                    $.getJSON("{{ URL::to('sube/abonesayac') }}",{id:aboneid,satisid:satisid},function(event){
                        if (event.durum) {
                            var sayaclar = event.abonesayaclari;
                            $("#abonesayac" + (no)).empty();
                            $.each(sayaclar, function (index) {
                                $("#abonesayac" + (no)).append('<option value="' + sayaclar[index].id + '"> ' + sayaclar[index].serino + '</option>');
                            });
                            $("#abonesayac" + (no)).select2({
                                placeholder: "Sayaç Seçin",
                                allowClear: true
                            });
                        } else {
                            toastr['' + event.type + '']('' + event.text + '', '' + event.title + '');
                        }
                        $.unblockUI();
                    });
                }
                $('.baglantidurum'+(no)).removeClass('hide');
            }else{
                $('.baglantidurum'+(no)).addClass('hide');
            }
            var fiyat = parseFloat($(this).find("option:selected").data('id')) || 0.00;
            var birimfiyat = parseFloat($(this).find("option:selected").data('fiyat')) || 0.00;
            var parabirimi = $(this).find("option:selected").data('value');
            var parabirimiid = $(this).find("option:selected").data('birim');
            if(parabirimi==null){
                parabirimi='';
            }
            $("#fiyat"+(no)).maskMoney({suffix: ' '+parabirimi,affixesStay:true, allowZero:true});
            $('#fiyat'+(no)).maskMoney('mask',fiyat.toFixed(2)*100);
            $("#birimfiyat"+(no)).val(birimfiyat.toFixed(2));
            $("#gfiyat"+(no)).val(fiyat.toFixed(2));
            var toplamtutar = 0;
            $('select.urunadi').each(function(){
                var urunno = $(this).closest('.sayaclar_ek').children('.no').val();
                var urunfiyat = parseFloat($(this).find("option:selected").data('id')) || 0.00;
                var urunmiktar = parseFloat($('#miktar'+(urunno)).val());
                if($("#ucretsiz"+(urunno)).is(':checked')){
                    urunfiyat = 0;
                    $("#fiyat"+(urunno)).maskMoney('mask',urunfiyat.toFixed(2)*100);
                }
                toplamtutar +=(urunfiyat*urunmiktar);
            });
            var kur=1;
            if(parabirimiid!==undefined){
                if(parabirimiid!==1){
                    if(parabirimiid===2)
                        kur=parseFloat($('#euro').val()).toFixed(4);
                    else if(parabirimiid===3)
                        kur=parseFloat($('#dolar').val()).toFixed(4);
                    else
                        kur=parseFloat($('#sterlin').val()).toFixed(4);
                }
            }
            toplamtutar*=kur;
            var kdv=((toplamtutar*18)/118);
            var tutar=(toplamtutar-kdv);
            //toplamtutar=Math.round(toplamtutar*10)/10;
            //kdv=toplamtutar-tutar;
            var birim=$('#birim').val();
            $('.tutar').html('<b>'+tutar.toFixed(2)+' '+birim+'</b>');
            $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+birim+'</b>');
            $('.toplamtutar').html('<b>'+toplamtutar.toFixed(2)+' '+birim+'</b>');
            $('#tutar').val(tutar.toFixed(2));
            $('#kdvtutar').val(kdv.toFixed(2));
            $('#toplamtutar').val(toplamtutar.toFixed(2));
            $('#geneltutar').val(tutar.toFixed(2));
            $('#genelkdv').val(kdv.toFixed(2));
            $('#geneltoplam').val(toplamtutar.toFixed(2));

        });
        $('.fiyat').on('change', function () {
            var fiyat = parseFloat($(this).val());
            var birimfiyat = ((fiyat*100)/118);
            var no = $(this).closest('.sayaclar_ek').children('.no').val();
            $("#birimfiyat"+(no)).val(birimfiyat.toFixed(2));
            $("#gfiyat"+(no)).val(fiyat.toFixed(2));
            $("#urunadi"+(no)).find("option:selected").data('id',fiyat);
            $("#urunadi"+(no)).find("option:selected").data('fiyat',birimfiyat);
            var parabirimiid = $("#urunadi"+(no)).find("option:selected").data('birim');
            var toplamtutar = 0;
            $('select.urunadi').each(function(){
                var urunno = $(this).closest('.sayaclar_ek').children('.no').val();
                var urunfiyat = parseFloat($(this).find("option:selected").data('id')) || 0.00;
                var urunmiktar = parseFloat($('#miktar'+(urunno)).val());
                if($("#ucretsiz"+(urunno)).is(':checked')){
                    urunfiyat = 0;
                    $("#fiyat"+(urunno)).maskMoney('mask',urunfiyat.toFixed(2)*100);
                }
                toplamtutar +=(urunfiyat*urunmiktar);
            });
            var kur=1;
            if(parabirimiid!==undefined){
                if(parabirimiid!==1){
                    if(parabirimiid===2)
                        kur=parseFloat($('#euro').val()).toFixed(4);
                    else if(parabirimiid===3)
                        kur=parseFloat($('#dolar').val()).toFixed(4);
                    else
                        kur=parseFloat($('#sterlin').val()).toFixed(4);
                }
            }
            toplamtutar*=kur;
            var kdv=((toplamtutar*18)/118);
            var tutar=(toplamtutar-kdv);
            var birim=$('#birim').val();
            $('.tutar').html('<b>'+tutar.toFixed(2)+' '+birim+'</b>');
            $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+birim+'</b>');
            $('.toplamtutar').html('<b>'+toplamtutar.toFixed(2)+' '+birim+'</b>');
            $('#tutar').val(tutar.toFixed(2));
            $('#kdvtutar').val(kdv.toFixed(2));
            $('#toplamtutar').val(toplamtutar.toFixed(2));
            $('#geneltutar').val(tutar.toFixed(2));
            $('#genelkdv').val(kdv.toFixed(2));
            $('#geneltoplam').val(toplamtutar.toFixed(2));
            $('#odemesekli').select2('val','').trigger('change');
        });
        $('.miktar').on('change', function () {
            var no = $(this).closest('.sayaclar_ek').children('.no').val();
            var parabirimiid = $("#urunadi"+(no)).find("option:selected").data('birim');
            $(this).closest('.sayaclar_ek').children('div').children('h4').children('.accordion-toggle').text(($("#urunadi"+(no)).select2('data').text)+' - '+($("#miktar"+(no)).val())+' ADET');
            var toplamtutar = 0;
            $('select.urunadi').each(function(){
                var urunno = $(this).closest('.sayaclar_ek').children('.no').val();
                var urunfiyat = parseFloat($(this).find("option:selected").data('id')) || 0.00;
                var urunmiktar = parseFloat($('#miktar'+(urunno)).val());
                if($("#ucretsiz"+(urunno)).is(':checked')){
                    urunfiyat = 0;
                    $("#fiyat"+(urunno)).maskMoney('mask',urunfiyat.toFixed(2)*100);
                }
                toplamtutar +=(urunfiyat*urunmiktar);
            });
            var kur=1;
            if(parabirimiid!==undefined){
                if(parabirimiid!==1){
                    if(parabirimiid===2)
                        kur=parseFloat($('#euro').val()).toFixed(4);
                    else if(parabirimiid===3)
                        kur=parseFloat($('#dolar').val()).toFixed(4);
                    else
                        kur=parseFloat($('#sterlin').val()).toFixed(4);
                }
            }
            toplamtutar*=kur;
            var kdv=((toplamtutar*18)/118);
            var tutar=(toplamtutar-kdv);
            //toplamtutar=Math.round(toplamtutar*10)/10;
            //kdv=toplamtutar-tutar;
            var birim=$('#birim').val();
            $('.tutar').html('<b>'+tutar.toFixed(2)+' '+birim+'</b>');
            $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+birim+'</b>');
            $('.toplamtutar').html('<b>'+toplamtutar.toFixed(2)+' '+birim+'</b>');
            $('#tutar').val(tutar.toFixed(2));
            $('#kdvtutar').val(kdv.toFixed(2));
            $('#toplamtutar').val(toplamtutar.toFixed(2));
            $('#geneltutar').val(tutar.toFixed(2));
            $('#genelkdv').val(kdv.toFixed(2));
            $('#geneltoplam').val(toplamtutar.toFixed(2));
            $('#odemesekli').select2('val','').trigger('change');
        });
        $('.ucretsiz').on('change', function () {
            var ucretsizler = "";
            var no = $(this).closest('.sayaclar_ek').children('.no').val();
            var parabirimiid = $("#urunadi"+(no)).find("option:selected").data('birim');
            var toplamtutar = 0;
            $('select.urunadi').each(function(){
                var ucretsizdurum = 0;
                var urunno = $(this).closest('.sayaclar_ek').children('.no').val();
                var urunfiyat = parseFloat($(this).find("option:selected").data('id')) || 0.00;
                var urunmiktar = parseFloat($('#miktar'+(urunno)).val());
                if($("#ucretsiz"+(urunno)).is(':checked')){
                    urunfiyat = 0;
                    ucretsizdurum = 1;
                    $("#fiyat"+(urunno)).maskMoney('mask',urunfiyat.toFixed(2)*100);
                }
                ucretsizler+=(ucretsizler==="" ? "" : ",")+ucretsizdurum;
                toplamtutar +=(urunfiyat*urunmiktar);
            });
            var kur=1;
            if(parabirimiid!==undefined){
                if(parabirimiid!==1){
                    if(parabirimiid===2)
                        kur=parseFloat($('#euro').val()).toFixed(4);
                    else if(parabirimiid===3)
                        kur=parseFloat($('#dolar').val()).toFixed(4);
                    else
                        kur=parseFloat($('#sterlin').val()).toFixed(4);
                }
            }
            toplamtutar*=kur;
            var kdv=((toplamtutar*18)/118);
            var tutar=(toplamtutar-kdv);
            //toplamtutar=Math.round(toplamtutar*10)/10;
            //kdv=toplamtutar-tutar;
            var birim=$('#birim').val();
            $('.tutar').html('<b>'+tutar.toFixed(2)+' '+birim+'</b>');
            $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+birim+'</b>');
            $('.toplamtutar').html('<b>'+toplamtutar.toFixed(2)+' '+birim+'</b>');
            $('#tutar').val(tutar.toFixed(2));
            $('#kdvtutar').val(kdv.toFixed(2));
            $('#toplamtutar').val(toplamtutar.toFixed(2));
            $('#ucretsizler').val(ucretsizler);
            $('#geneltutar').val(tutar.toFixed(2));
            $('#genelkdv').val(kdv.toFixed(2));
            $('#geneltoplam').val(toplamtutar.toFixed(2));
            $('#odemesekli').select2('val','').trigger('change');
        });
        $('.baglantidurum').on('change', function () {
            var baglantidurumlari = "";
            $('select.urunadi').each(function(){
                var urunno = $(this).closest('.sayaclar_ek').children('.no').val();
                var baglantidurum = $("#baglantidurum"+(urunno)).val();
                baglantidurumlari+=(baglantidurumlari==="" ? "" : ",")+baglantidurum;
                if(baglantidurum==='1'){
                    $('.baglantidurum'+(urunno)).removeClass('hide');
                }else{
                    $('.baglantidurum'+(urunno)).addClass('hide');
                }
            });
            $('#baglantidurumlari').val(baglantidurumlari);
        });
        $('.satirsil').click(function(){
            if($('.sayaclar .sayaclar_ek').size()>0){
                var sayac=$(this).closest('.sayaclar_ek');
                var adet = sayac.children('.adet').val();
                sayac.children('.adet').val(0);
                sayac.remove();
                cnt-=adet;
                count-=adet;
                $("#count").val(count);
                if(count===0)
                    $(".kaydet").prop('disabled',true);
                var j=0;
                $('.sayaclar .sayaclar_ek').each(function(){
                    var id=$( this ).children('.no').val();
                    $( this).children('div').children('h4').children('.accordion-toggle').attr('href','#collapse_'+j);
                    $( this).children('.panel-collapse').attr('id','collapse_'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.urunadi').attr('id','urunadi'+j).attr('name','urunadi[]');
                    $( this).children('div').children('div').children('div').children('div').children('.urunadi').removeClass('urunadi'+id).addClass('urunadi'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.fiyat').attr('id','fiyat'+j+'').attr('name','fiyat[]');
                    $( this).children('div').children('div').children('div').children('div').children('.birimfiyat').attr('id','birimfiyat'+j+'').attr('name','birimfiyat[]');
                    $( this).children('div').children('div').children('div').children('div').children('.gfiyat').attr('id','gfiyat'+j+'').attr('name','gfiyat[]');
                    $( this).children('div').children('div').children('div').children('div').children('.miktar').attr('id','miktar'+j+'').attr('name','miktar[]');
                    $( this).children('div').children('div').children('div').children('div').children('.ucretsiz').attr('id','ucretsiz'+j+'').attr('value',j).attr('name','ucretsiz[]');
                    $( this).children('div').children('div').children('.baglantidurum'+id).removeClass('baglantidurum'+id).addClass('baglantidurum'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.abonesayac').removeClass('abonesayac'+id).addClass('abonesayac'+j).attr('id','abonesayac'+j+'').attr('name','abonesayac['+j+'][]');
                    $( this).children('div').children('div').children('div').children('.baglantidurum').attr('id','baglantidurum'+j+'').attr('name','baglantidurum[]');
                    $( this ).children('.no').val(j);
                    j++;
                });
                var birim = $("#birim").val();
                var parabirimiid = 1;
                var toplamtutar = 0;
                var ucretsizler="";
                var baglantidurumlari="";
                $('select.urunadi').each(function(){
                    var ucretsizdurum = 0;
                    var urunno = $(this).closest('.sayaclar_ek').children('.no').val();
                    var baglantidurum = $("#baglantidurum"+(urunno)).val();
                    var urunfiyat = parseFloat($(this).find("option:selected").data('id')) || 0.00;
                    parabirimiid = $(this).find("option:selected").data('birim');
                    var urunmiktar = parseFloat($('#miktar'+(urunno)).val());
                    if($("#ucretsiz"+(urunno)).is(':checked')){
                        urunfiyat = 0;
                        ucretsizdurum = 1;
                        $("#fiyat"+(urunno)).maskMoney('mask',urunfiyat.toFixed(2)*100);
                    }
                    ucretsizler+=(ucretsizler==="" ? "" : ",")+ucretsizdurum;
                    baglantidurumlari+=(baglantidurumlari==="" ? "" : ",")+baglantidurum;
                    toplamtutar +=(urunfiyat*urunmiktar);
                });
                var kur=1;
                if(parabirimiid!==undefined){
                    if(parabirimiid!==1){
                        if(parabirimiid===2)
                            kur=parseFloat($('#euro').val()).toFixed(4);
                        else if(parabirimiid===3)
                            kur=parseFloat($('#dolar').val()).toFixed(4);
                        else
                            kur=parseFloat($('#sterlin').val()).toFixed(4);
                    }
                }
                toplamtutar*=kur;
                var kdv=((toplamtutar*18)/118);
                var tutar=(toplamtutar-kdv);
                //toplamtutar=Math.round(toplamtutar*10)/10;
                //kdv=toplamtutar-tutar;
                $('.tutar').html('<b>'+tutar.toFixed(2)+' '+birim+'</b>');
                $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+birim+'</b>');
                $('.toplamtutar').html('<b>'+toplamtutar.toFixed(2)+' '+birim+'</b>');
                $('#tutar').val(tutar.toFixed(2));
                $('#kdvtutar').val(kdv.toFixed(2));
                $('#toplamtutar').val(toplamtutar.toFixed(2));
                $('#ucretsizler').val(ucretsizler);
                $('#baglantidurumlari').val(baglantidurumlari);
                $('#geneltutar').val(tutar.toFixed(2));
                $('#genelkdv').val(kdv.toFixed(2));
                $('#geneltoplam').val(toplamtutar.toFixed(2));
                $('#odemesekli').select2('val','').trigger('change');
            }
        });
        $(".miktar").inputmask("mask", {
            mask:"9",repeat:3,greedy:!1
        });
        for(var i=0;i<count;i++){
            $( "#abonesayac"+(i)).select2({
                placeholder: "Sayaç Seçin",
                allowClear: true
            });
            var sayaclar;
            if($( "#abonesayaclar"+(i)).hasClass( "abonesayaclar"+(i) ))
            {
                sayaclar =document.getElementById("abonesayaclar"+(i)).innerHTML;
                sayaclar = sayaclar.replace(/\s+/g,' ').trim();
                sayaclar = sayaclar.split(" ");
                $('#abonesayac'+(i)).select2("val",sayaclar);
            }

            if($( "#abonesayaclarekli"+(i) ).hasClass( "abonesayaclarekli"+(i) ))
            {
                sayaclar =document.getElementById("abonesayaclarekli"+(i)).innerHTML;
                sayaclar = sayaclar.split(",");
                $('#abonesayac'+(i)).select2("val",sayaclar);
            }

            var parabirimi = $("#urunadi"+(i)).find("option:selected").data('value');
            $("#fiyat"+(i)).maskMoney({suffix: ' '+parabirimi,affixesStay:true, allowZero:true});
        }
        @endif
        if (count > 0)
            $(".kaydet").prop('disabled', false);
        else
            $(".kaydet").prop('disabled', true);
        $('#abone').on('change', function () {
            var aboneid = $(this).val();
            if(aboneid!==""){
                $.blockUI();
                $.getJSON("{{ URL::to('sube/abonesayac') }}",{id:aboneid,satisid:satisid,urun:1},function(event){
                    if(event.durum){
                        var sayaclar=event.abonesayaclari;
                        var urunler=event.urunler;
                        $(".abonesayac").empty();
                        $(".urunadi").empty();
                        $.each($(".abonesayac"), function (i) {
                            var abonesayac = $("#abonesayac" + i);
                            $.each(sayaclar, function (index) {
                                abonesayac.append('<option value="' + sayaclar[index].id + '"> ' + sayaclar[index].serino + '</option>');
                            });
                            abonesayac.select2({
                                placeholder: "Sayaç Seçin",
                                allowClear: true
                            });
                            $('.baglantidurum'+(i)).addClass('hide');
                        });
                        $.each($(".urunadi"), function (i) {
                            var urunadi = $("#urunadi" + i);
                            urunadi.append('<option value="">Seçiniz...</option>');
                            $.each(urunler, function (index) {
                                urunadi.append('<option data-id="' + 0.00 + '" data-fiyat="'+ 0.00 +'" data-baglanti="' + urunler[index].baglanti + '" ' +
                                    'data-birim="' + urunler[index].parabirimi_id + '" data-value="' + urunler[index].parabirimi.birimi + '" value="' + urunler[index].id + '">' +
                                    urunler[index].netsisstokkod.kodu+' - '+urunler[index].urunadi + ' ('+parseInt(urunler[index].stok.BAKIYE)+')</option>');
                            });
                            urunadi.select2();
                            $("#fiyat"+(i)).maskMoney({suffix: ' ₺',affixesStay:true, allowZero:true});
                            $('#fiyat'+(i)).maskMoney('mask',0);
                            $("#birimfiyat"+(i)).val(0.00);
                            $("#gfiyat"+(i)).val(0.00);
                            $("#ucretsiz"+(i)).attr('checked',false);
                        });
                        $('.tutar').html('<b>0.00 ₺</b>');
                        $('.kdvtutar').html('<b>0.00 ₺</b>');
                        $('.toplamtutar').html('<b>0.00 ₺</b>');
                        $('#tutar').val(0.00);
                        $('#kdvtutar').val(0.00);
                        $('#toplamtutar').val(0.00);
                        $('#geneltutar').val(0.00);
                        $('#genelkdv').val(0.00);
                        $('#geneltoplam').val(0.00);
                        $('#odemesekli').select2('val','').trigger('change');
                        $('.sayaclar .sayaclar_ek').each(function () {
                            $(this).children('div').children('h4').children('.accordion-toggle').text('YENİ');
                        });
                        $('.sayaclar').find('input:checkbox').uniform();
                        $.uniform.update();
                    }else{
                        toastr['' + event.type + '']('' + event.text + '', '' + event.title + '');
                    }
                    $.unblockUI();
                });
            }
        });
        $('#adres').on('change', function () {
            var adres = $(this).val();
            $('#faturaadresi').val(adres);
        });
        $('#faturaadresi').on('change', function () {
            var adres = $(this).val();
            $('#adres').val(adres);
        });
        $('#faturail').on('change', function (){
            var faturail = $(this).val();
            if(faturail!==""){
                $.blockUI();
                $.getJSON("{{ URL::to('sube/faturailceler') }}",{id:faturail},function(event){
                    if(event.durum){
                        var ilceler=event.ilceler;
                        $("#faturailce").empty();
                        if(ilceler.length>0){
                            $("#faturailce").append('<option value="">Seçiniz...</option>');
                            $.each(ilceler, function (index) {
                                $("#faturailce").append('<option value="' + ilceler[index].id + '">' + ilceler[index].adi+'</option>');
                            });
                            $("#faturailce").select2("val","");
                        }
                    }else{
                        toastr['' + event.type + '']('' + event.text + '', '' + event.title + '');
                    }
                    $.unblockUI();
                });
            }
        });
        var startDate =(new Date).getFullYear();
        $('#tarih').datepicker({ rtl: Metronic.isRTL(),orientation: "left",autoclose: true,language: 'tr',
            startDate: new Date(startDate,0,1),endDate: new Date(startDate+1,0,0)})
            .on('changeDate', function () {
            var tarih = $(this).val();
            if(tarih!=="") {
                $.blockUI();
                $.getJSON("{{ URL::to('backend/dovizkurgetir') }}", {tarih: tarih}, function (event) {
                    if (event.durum) {
                        var dovizkuru = event.dovizkuru;
                        var birim=$('#birim').val();
                        $.each(dovizkuru, function (index) {
                            if (dovizkuru[index].parabirimi_id === "2"){
                                $('.euro').text('Euro : '+dovizkuru[index].kurfiyati+' '+birim);
                                $('#euro').val(dovizkuru[index].kurfiyati);
                            } else if (dovizkuru[index].parabirimi_id === "3"){
                                $('.dolar').text('Dolar : '+dovizkuru[index].kurfiyati+' '+birim);
                                $('#dolar').val(dovizkuru[index].kurfiyati);
                            }else{
                                $('.sterlin').text('Sterlin : '+dovizkuru[index].kurfiyati+' '+birim);
                                $('#sterlin').val(dovizkuru[index].kurfiyati);
                            }
                        });
                        $('.kurtarihi').text("Kur Tarihi: "+tarih);
                        $('#kurtarih').val(tarih);
                        var parabirimiid=parseInt($('#parabirimi').val());
                        var toplamtutar =parseFloat($('#toplamtutar').val());
                        var kur=1;
                        if(parabirimiid!==undefined){
                            if(parabirimiid!==1){
                                if(parabirimiid===2)
                                    kur=parseFloat($('#euro').val()).toFixed(4);
                                else if(parabirimiid===3)
                                    kur=parseFloat($('#dolar').val()).toFixed(4);
                                else
                                    kur=parseFloat($('#sterlin').val()).toFixed(4);
                            }
                        }
                        toplamtutar*=kur;
                        var kdv=((toplamtutar*18)/118);
                        var tutar=(toplamtutar-kdv);
                        $('.tutar').html('<b>'+tutar.toFixed(2)+' '+birim+'</b>');
                        $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+birim+'</b>');
                        $('.toplamtutar').html('<b>'+toplamtutar.toFixed(2)+' '+birim+'</b>');
                        $('#tutar').val(tutar.toFixed(2));
                        $('#kdvtutar').val(kdv.toFixed(2));
                        $('#toplamtutar').val(toplamtutar.toFixed(2));
                        $('#geneltutar').val(tutar.toFixed(2));
                        $('#genelkdv').val(kdv.toFixed(2));
                        $('#geneltoplam').val(tutar.toFixed(2));
                        $('#odemesekli').select2('val','').trigger('change');
                    } else {
                        toastr['' + event.type + '']('' + event.text + '', '' + event.title + '');
                    }
                    $.unblockUI();
                });
            }
        });
        $('#formsubmit').click(function () {
            $('#form_sample').submit();
        });
        $("select").on("select2-close", function () { $(this).valid(); });
        $('#tarih').on('change', function() { $(this).valid(); });
        $('#form_sample').valid();
    });
</script>
<script>
    $(document).ready(function() {
        var aboneflag=0;
        var sayaccount=parseInt($("#sayaccount").val());
        $('.sayacekle').click(function(){
            //var adet=1;  // eklenecek sayı
            var newRow="";
            newRow += '<div class="panel panel-default abonesayaclar_ek"><input class="no hide" value="'+(sayaccount)+'"/><input class="aboneadet hide" value="1"/><div class="panel-heading"><h4 class="panel-title">'+
                '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#aboneaccordion1" href="#abonecollapse_'+sayaccount+'">Yeni</a></h4></div>' +
                '<div id="abonecollapse_'+sayaccount+'" class="panel-collapse in"><div class="panel-body"><div class="form-group">'+
                '<label class="col-sm-2 col-xs-4 control-label">Seri No:<span class="required" aria-required="true"> * </span></label>'+
                '<div class="input-icon right col-sm-4 col-xs-4"><i class="fa"></i><input type="text" id="aboneserino'+sayaccount+'" name="aboneserino['+sayaccount+']" maxlength="15" class="form-control abonevalid'+sayaccount+' aboneserino"></div>' +
                '<label class="col-sm-3 col-xs-4"><a class="btn green abonegetir">Bul</a><a class="btn red abonesatirsil">Sil</a></label></div>'+
                '<div class="form-group"><label class="col-sm-2 col-xs-4 control-label">Sayaç Türü:<span class="required" aria-required="true"> * </span></label>' +
                '<div class="input-icon right col-sm-4 col-xs-8"><i class="fa"></i><select class="form-control select2me abonevalid'+sayaccount+' abonesayactur abonesayactur'+sayaccount+'" id="abonesayactur'+sayaccount+'" name="abonesayacturleri['+sayaccount+']" tabindex="-1" title="">'+
                '<option value="">Seçiniz...</option>'+
                    @foreach($sayacturleri as $sayactur)
                        '<option value="{{ $sayactur->id }}">{{ $sayactur->tur}}</option>'+
                    @endforeach
                        '</select></div></div>'+
                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Adı:<span class="required" aria-required="true"> * </span></label><div class="input-icon right col-xs-8">' +
                '<i class="fa"></i><select class="form-control select2me abonevalid'+sayaccount+' abonesayacadi abonesayacadi'+sayaccount+'" id="abonesayacadi'+sayaccount+'" name="abonesayacadlari['+sayaccount+']" tabindex="-1" title="">'+
                '<option value="">Seçiniz...</option>'+
                    @foreach($sayacadlari as $sayacadi)
                        '<option data-id="{{ $sayacadi->cap }}" value="{{ $sayacadi->id }}" >{{ $sayacadi->sayacadi }}</option>'+
                    @endforeach
                        '</select></div></div><div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Çapı:<span class="required" aria-required="true"> * </span></label>' +
                '<div class="input-icon right col-xs-8"><i class="fa"></i><select class="form-control select2me abonevalid'+sayaccount+' abonesayaccap abonesayaccap'+sayaccount+'" id="abonesayaccap'+sayaccount+'" name="abonesayaccap['+sayaccount+']" tabindex="-1" title="">'+
                '<option value="">Seçiniz...</option>'+
                    @foreach($sayaccaplari as $sayaccapi)
                        '<option value="{{ $sayaccapi->id }}">{{ $sayaccapi->capadi }}</option>'+
                    @endforeach
                        '</select><input type="text" id="abonesayaccaplari'+sayaccount+'" name="abonesayaccaplari[]" class="abonesayaccaplari hide" value="1"></div></div>'+
                '<div class="form-group col-xs-12"><label class="col-sm-2 col-xs-4 control-label">Montaj Adresi:<span class="required" aria-required="true"> * </span></label><div class="input-icon right col-sm-10 col-xs-8">' +
                '<i class="fa"></i><input type="text" id="abonesayacadresi'+sayaccount+'" name="abonesayacadresi['+sayaccount+']" class="form-control abonevalid'+sayaccount+' abonesayacadresi"></div></div>' +
                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Bilgi:</label><div class="col-xs-8">' +
                '<input type="text" id="abonesayacbilgi'+sayaccount+'" name="abonesayacbilgi['+sayaccount+']" class="form-control abonesayacbilgi"></div>' +
                '</div><div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">İletişim:</label><div class="col-xs-8">' +
                '<input type="text" id="abonesayaciletisim'+sayaccount+'" name="abonesayaciletisim['+sayaccount+']" class="form-control abonesayaciletisim"></div>' +
                '</div></div></div>';
            sayaccount++;
            $('.sayaccount').html(sayaccount+' Adet');
            $('.abonesayaclar').append(newRow);
            $('select.abonevalid'+(sayaccount-1)).each(function(){
                $(this).rules('remove');
                $(this).rules('add', {
                    required: true
                });
            });
            $('input.abonevalid'+(sayaccount-1)).each(function(){
                $(this).rules('remove');
                $(this).rules('add', {
                    required: true
                });
            });
            $("#abonesayactur"+(sayaccount-1)).select2();
            $("#abonesayacadi"+(sayaccount-1)).select2();
            $("#abonesayaccap"+(sayaccount-1)).select2();
            $("#abonesayaccaplari"+(sayaccount-1)).val("");
            $('.abonesayactur').on('change', function () {
                var id = $(this).val();
                var no = $(this).closest('.abonesayaclar_ek').children('.no').val();
                var subekodu = $('#abonesubekodu').val();
                if(id!==""){
                    $.blockUI();
                    $.getJSON("{{ URL::to('sube/subesayacadlari') }}",{sayacturid:id,subekodu:subekodu}, function (event) {
                        if(event.durum) // sayac adları varsa
                        {
                            var sayacadlari = event.sayacadlari;
                            var sayaccaplari = event.sayaccaplari;
                            var capdurum = event.capdurum;
                            if(capdurum===0){ //sayaccap gözükmeyecek
                                $("#abonesayaccap"+(no)).select2("val",1).valid();
                                $("#abonesayaccaplari"+(no)).val(1);
                                $(".abonesayaccap"+(no)).prop("disabled", true);
                            }else{
                                $("#abonesayaccap"+(no)).select2("val","").valid();
                                $("#abonesayaccaplari"+(no)).val("");
                                $(".abonesayaccap"+(no)).prop("disabled", false);
                            }
                            $("#abonesayacadi"+(no)).empty();
                            $("#abonesayacadi"+(no)).append('<option value="">Seçiniz...</option>');
                            $.each(sayacadlari, function (index) {
                                $("#abonesayacadi"+(no)).append('<option data-id="'+sayacadlari[index].cap+'" value="' + sayacadlari[index].id + '"> ' + sayacadlari[index].sayacadi + '</option>');
                            });
                            $("#abonesayacadi"+(no)).select2("val", "").valid();
                            $("#abonesayaccap"+(no)).empty();
                            $("#abonesayaccap"+(no)).append('<option value="">Seçiniz...</option>');
                            $.each(sayaccaplari, function (index) {
                                $("#abonesayaccap"+(no)).append('<option value="' + sayaccaplari[index].id + '"> ' + sayaccaplari[index].capadi + '</option>');
                            });
                            $("#abonesayaccap"+(no)).select2("val", "").valid();
                            $("#abonesayaccaplari"+(no)).val("");
                        } else { //bulunamadı hatasını ekrana bas
                            $("#abonesayacadi"+(no)).empty();
                            $("#abonesayaccap"+(no)).empty();
                            $("#abonesayacadi"+(no)).append('<option value="">Seçiniz...</option>');
                            $("#abonesayaccap"+(no)).append('<option value="">Seçiniz...</option>');
                            $("#abonesayacadi"+(no)).select2("val","").valid();
                            $("#abonesayaccap"+(no)).select2("val","").valid();
                            $("#abonesayaccaplari"+(no)).val("");
                            $(".abonesayaccap"+(no)).prop("disabled", false);
                            toastr[event.type](event.text,event.title);
                        }
                        $.unblockUI();
                    });
                }else{
                    $("#abonesayacadi"+(no)).empty();
                    $("#abonesayaccap"+(no)).empty();
                    $("#abonesayacadi"+(no)).append('<option value="">Seçiniz...</option>');
                    $("#abonesayaccap"+(no)).append('<option value="">Seçiniz...</option>');
                    $("#abonesayacadi"+(no)).select2("val","").valid();
                    $("#abonesayaccap"+(no)).select2("val","").valid();
                    $("#abonesayaccaplari"+(no)).val("");
                    $(".abonesayaccap"+(no)).prop("disabled", false);
                }
                $(this).valid();
            });
            $('.abonesayacadi').on('change', function () {
                var id = $(this).val();
                var no = $(this).closest('.abonesayaclar_ek').children('.no').val();
                if(id!==""){
                    var capdurum = $(this).find("option:selected").data('id');
                    if (capdurum === 0) //cap kontrol edilmiyor
                    {
                        $("#abonesayaccap"+(no)).select2("val",1).valid();
                        $("#abonesayaccaplari"+(no)).val(1);
                        $(".abonesayaccap"+(no)).prop("disabled", true);
                    } else {
                        $("#abonesayaccap"+(no)).select2("val","").valid();
                        $("#abonesayaccaplari"+(no)).val("");
                        $(".abonesayaccap"+(no)).prop("disabled", false);
                    }
                }else{
                    $("#abonesayaccap"+(no)).select2("val","").valid();
                    $("#abonesayaccaplari"+(no)).val("");
                    $(".abonesayaccap"+(no)).prop("disabled", false);
                }
                $(this).valid();
            });
            $('.abonesayaccap').on('change', function () {
                var id = $(this).val();
                var no = $(this).closest('.abonesayaclar_ek').children('.no').val();
                $("#abonesayaccaplari"+(no)).val(id);
                $(this).valid();
            });
            $('.abonesatirsil').click(function(){
                if($('.abonesayaclar .abonesayaclar_ek').size()>0){
                    var sayac=$(this).closest('.abonesayaclar_ek');
                    var adet = sayac.children('.aboneadet').val();
                    sayac.children('.aboneadet').val(0);
                    sayac.remove();
                    sayaccount-=adet;
                    $("#sayaccount").val(sayaccount);
                    $('.sayaccount').html(sayaccount + ' Adet');
                    /*if(sayaccount==0)
                        $(".kaydet").prop('disabled',true);*/
                    var j=0;
                    $('.abonesayaclar .abonesayaclar_ek').each(function(){
                        var id=$(this).children('.no').val();
                        $(this).children('div').children('h4').children('.accordion-toggle').attr('href','#abonecollapse_'+j);
                        $(this).children('.panel-collapse').attr('id','abonecollapse_'+j);
                        $(this).children('div').children('div').children('div').children('div').children('.aboneserino').attr('id','aboneserino'+j).attr('name','aboneserino['+j+']');
                        $(this).children('div').children('div').children('div').children('div').children('.abonesayactur').attr('id','abonesayactur'+j).attr('name','abonesayacturleri['+j+']');
                        $(this).children('div').children('div').children('div').children('div').children('.abonesayactur').removeClass('abonesayactur'+id).addClass('abonesayactur'+j);
                        $(this).children('div').children('div').children('div').children('div').children('.abonesayacadi').attr('id','abonesayacadi'+j).attr('name','abonesayacadlari['+j+']');
                        $(this).children('div').children('div').children('div').children('div').children('.abonesayacadi').removeClass('abonesayacadi'+id).addClass('abonesayacadi'+j);
                        $(this).children('div').children('div').children('div').children('div').children('.abonesayaccap').attr('id','abonesayaccap'+j).attr('name','abonesayaccap['+j+']');
                        $(this).children('div').children('div').children('div').children('div').children('.abonesayaccap').removeClass('abonesayaccap'+id).addClass('abonesayaccap'+j);
                        $(this).children('div').children('div').children('div').children('div').children('.abonesayaccaplari').attr('id', 'abonesayaccaplari' + j).attr('name', 'abonesayaccaplari[]');
                        $(this).children('div').children('div').children('div').children('div').children('.abonesayacadresi').attr('id','abonesayacadresi'+j).attr('name','abonesayacadresi['+j+']');
                        $(this).children('div').children('div').children('div').children('div').children('.abonesayacbilgi').attr('id','abonesayacbilgi'+j).attr('name','abonesayacbilgi['+j+']');
                        $(this).children('div').children('div').children('div').children('div').children('.abonesayaciletisim').attr('id','abonesayaciletisim'+j).attr('name','abonesayaciletisim['+j+']');
                        $(this).children('.no').val(j);
                        j++;
                    });
                }
            });
            $('.abonegetir').click(function(){
                var sayac=$(this).closest('.abonesayaclar_ek');
                var no=sayac.children('.no').val();
                $('#abonesecilen').val(no);
                var subekodu = $('#abonesubekodu').val();
                var uretimyer = $('#aboneuretimyer').val();
                var serino = sayac.children('div').children('div').children('div').children('div').children('.aboneserino').val();
                if(serino!==""){
                    $.blockUI();
                    $.getJSON("{{ URL::to('sube/abonebilgi') }}",{serino:serino,subekodu:subekodu,uretimyer:uretimyer}, function (event) {
                        if(event.durum){
                            var sayac = event.sayac;
                            $("#abonesayactur"+no).select2("val",sayac.sayactur_id).valid();
                            $("#abonesayacadi"+no).select2("val",sayac.sayacadi_id).valid();
                            $("#abonesayaccap"+no).select2("val",sayac.sayaccap_id).valid();
                            $("#abonesayaccaplari"+no).val(sayac.sayaccap_id);
                            if(sayac.sayaccap_id === "1"){
                                $("#abonesayaccap"+no).prop("disabled", true);
                            }else{
                                $("#abonesayaccap"+no).prop("disabled", false);
                            }
                            $("#abonesayacadresi"+no).val(sayac.adres).valid();
                            $("#abonesayacbilgi"+no).val(sayac.bilgi);
                            $("#abonesayaciletisim"+no).val(sayac.iletisim);
                        }else{
                            $("#abonesayactur"+no).select2("val","").valid();
                            $("#abonesayacadi"+no).select2("val","").valid();
                            $("#abonesayaccap"+no).select2("val","").valid();
                            $("#abonesayaccaplari"+no).val("");
                            $("#abonesayaccap"+no).prop("disabled", false);
                            $("#abonesayacadresi"+no).val("");
                            $("#abonesayacbilgi"+no).val("");
                            $("#abonesayaciletisim"+no).val("");
                            toastr[event.type](event.text, event.title);
                        }
                        $.unblockUI();
                    });
                }
            });

            $(".aboneserino").inputmask("mask", {
                mask:"9",repeat:15,greedy:!1
            });
            $("#sayaccount").val(sayaccount);
            $('input[name^="aboneserino"]').change(function () {
                $(".abonekaydet").prop('disabled', false);
                $('input[name^="aboneserino"]').css("background-color", "#FFFFFF");
                $('input[name^="aboneserino"]').each(function (i, el1) {
                    var current_val = jQuery(el1).val();
                    if (current_val !== "") {
                        $('input[name^="aboneserino"]').each(function (i,el2) {
                            if (jQuery(el2).val() === current_val && jQuery(el1).attr("id") !== jQuery(el2).attr("id")) {
                                jQuery(el2).css("background-color", "yellow");
                                jQuery(el1).css("background-color", "yellow");
                                $(".abonekaydet").prop('disabled', true);
                            }
                        });
                    }
                });
            });
            /* if (sayaccount > 0) {
                 $(".kaydet").prop('disabled', false);
             } else {
                 $(".kaydet").prop('disabled', true);
             }*/
            $("select").on("select2-close", function () { $(this).valid(); });
        });
        $('input[name^="aboneserino"]').css("background-color", "#FFFFFF");
        $('input[name^="aboneserino"]').each(function (i, el1) {
            var current_val = jQuery(el1).val();
            if (current_val !== "") {
                $('input[name^="aboneserino"]').each(function (i, el2) {
                    if (jQuery(el2).val() === current_val && jQuery(el1).attr("id") !== jQuery(el2).attr("id")) {
                        jQuery(el2).css("background-color", "yellow");
                        jQuery(el1).css("background-color", "yellow");
                        aboneflag=1;
                    }
                });
            }
        });
        $(".sayactumsil").click(function(){
            while($('.abonesayaclar .abonesayaclar_ek').size()>0){
                $('.abonesayaclar .abonesayaclar_ek:last').remove();
                sayaccount--;
            }
            $("#sayaccount").val(0);
            $('.sayaccount').html(0+' Adet');
            //$(".kaydet").prop('disabled',true);
        });
        var i,capdurum;
        for (i = 0; i < sayaccount; i++) {
            capdurum = $('.abonesayacadi' + i).find("option:selected").data('id');
            if (capdurum === 0) //cap kontrol edilmiyor
            {
                $(".abonesayaccap"+i).prop("disabled", true);
            } else {
                $(".abonesayaccap"+i).prop("disabled", false);
            }
            $('select.abonevalid'+i).each(function(){
                $(this).rules('remove');
                $(this).rules('add', {
                    required: true
                });
            });
            $('input.abonevalid'+i).each(function(){
                $(this).rules('remove');
                $(this).rules('add', {
                    required: true
                });
            });
        }
        $('.abonegetir').click(function(){
            var sayac=$(this).closest('.abonesayaclar_ek');
            var no=sayac.children('.no').val();
            $('#abonesecilen').val(no);
            var subekodu = $('#abonesubekodu').val();
            var uretimyer = $('#aboneuretimyer').val();
            var serino = sayac.children('div').children('div').children('div').children('div').children('.aboneserino').val();
            if(serino!==""){
                $.blockUI();
                $.getJSON("{{ URL::to('sube/abonebilgi') }}",{serino:serino,subekodu:subekodu,uretimyer:uretimyer}, function (event) {
                    if(event.durum){
                        var sayac = event.sayac;
                        $("#abonesayactur"+no).select2("val",sayac.sayactur_id).valid();
                        $("#abonesayacadi"+no).select2("val",sayac.sayacadi_id).valid();
                        $("#abonesayaccap"+no).select2("val",sayac.sayaccap_id).valid();
                        $("#abonesayaccaplari"+no).val(sayac.sayaccap_id);
                        if(sayac.sayaccap_id === "1"){
                            $("#abonesayaccap"+no).prop("disabled", true);
                        }else{
                            $("#abonesayaccap"+no).prop("disabled", false);
                        }
                        $("#abonesayacadresi"+no).val(sayac.adres).valid();
                        $("#abonesayacbilgi"+no).val(sayac.bilgi).valid();
                        $("#abonesayaciletisim"+no).val(sayac.bilgi).valid();
                    }else{
                        $("#abonesayactur"+no).select2("val","").valid();
                        $("#abonesayacadi"+no).select2("val","").valid();
                        $("#abonesayaccap"+no).select2("val","").valid();
                        $("#abonesayaccaplari"+no).val("");
                        $("#abonesayaccap"+no).prop("disabled", false);
                        $("#abonesayacadresi"+no).val("");
                        $("#abonesayacbilgi"+no).val("");
                        $("#abonesayaciletisim"+no).val("");
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            }
        });
        $('#aboneadisoyadi').on('change',function(){
            var adisoyadi=$(this).val();
            if(adisoyadi!==""){
                $(".abonekaydet").prop('disabled',false);
            }else{
                $(".abonekaydet").prop('disabled',true);
            }
        });
        $('#aboneuretimyer').on('change',function(){
            var id=$(this).val();
            if(id!==""){
                $('.tuslar').removeClass('hide');
            }else{
                while($('.abonesayaclar .abonesayaclar_ek').size()>0){
                    $('.abonesayaclar .abonesayaclar_ek:last').remove();
                    sayaccount--;
                }
                $("#sayaccount").val(0);
                $('.sayaccount').html(0+' Adet');
//                $(".kaydet").prop('disabled',true);
                $('.tuslar').addClass('hide');
            }
        });
        $('.abonesayactur').on('change', function () {
            var sayactur = $(this).val();
            var subekodu = $('#abonesubekodu').val();
            var no = $(this).closest('.abonesayaclar_ek').children('.no').val();
            if(sayactur!=="") {
                $.blockUI();
                $.getJSON("{{ URL::to('sube/subesayacadlari') }}",{sayacturid:sayactur,subekodu:subekodu}, function (event) {
                    if (event.durum) // sayac adları varsa
                    {
                        var sayacadlari = event.sayacadlari;
                        var sayaccaplari = event.sayaccaplari;
                        var capdurum = event.capdurum;
                        if (capdurum === 0) { //sayaccap gözükmeyecek
                            $("#abonesayaccap" + (no)).select2("val", 1).valid();
                            $("#abonesayaccaplari"+(no)).val(1);
                            $(".abonesayaccap" + (no)).prop("disabled", true);
                        } else {
                            $("#abonesayaccap" + (no)).select2("val", "").valid();
                            $("#abonesayaccaplari"+(no)).val("");
                            $(".abonesayaccap" + (no)).prop("disabled", false);
                        }
                        $("#abonesayacadi" + (no)).empty();
                        $("#abonesayacadi" + (no)).append('<option value="">Seçiniz...</option>');
                        $.each(sayacadlari, function (index) {
                            $("#abonesayacadi" + (no)).append('<option data-id="' + sayacadlari[index].cap + '" value="' + sayacadlari[index].id + '"> ' + sayacadlari[index].sayacadi + '</option>');
                        });
                        $("#abonesayacadi" + (no)).select2("val", "").valid();
                        $("#abonesayaccap" + (no)).empty();
                        $("#abonesayaccap" + (no)).append('<option value="">Seçiniz...</option>');
                        $.each(sayaccaplari, function (index) {
                            $("#abonesayaccap" + (no)).append('<option value="' + sayaccaplari[index].id + '"> ' + sayaccaplari[index].capadi + '</option>');
                        });
                        $("#abonesayaccap" + (no)).select2("val", "").valid();
                        $("#abonesayaccaplari"+(no)).val("");
                    } else { //bulunamadı hatasını ekrana bas
                        $("#abonesayacadi" + (no)).empty();
                        $("#abonesayaccap" + (no)).empty();
                        $("#abonesayacadi" + (no)).append('<option value="">Seçiniz...</option>');
                        $("#abonesayaccap" + (no)).append('<option value="">Seçiniz...</option>');
                        $("#abonesayacadi" + (no)).select2("val", "").valid();
                        $("#abonesayaccap" + (no)).select2("val", "").valid();
                        $("#abonesayaccaplari"+(no)).val("");
                        $(".abonesayaccap" + (no)).prop("disabled", false);
                        toastr['' + event.type + '']('' + event.text + '', '' + event.title + '');
                    }
                    $.unblockUI();
                });
            }else{
                $("#abonesayacadi" + (no)).empty();
                $("#abonesayaccap" + (no)).empty();
                $("#abonesayacadi" + (no)).append('<option value="">Seçiniz...</option>');
                $("#abonesayaccap" + (no)).append('<option value="">Seçiniz...</option>');
                $("#abonesayacadi" + (no)).select2("val", "").valid();
                $("#abonesayaccap" + (no)).select2("val", "").valid();
                $("#abonesayaccaplari"+(no)).val("");
                $(".abonesayaccap" + (no)).prop("disabled", false);
            }
            $(this).valid();
        });
        $('.abonesayacadi').on('change', function () {
            var id = $(this).val();
            var no = $(this).closest('.abonesayaclar_ek').children('.no').val();
            if(id!==""){
                var capdurum = $(this).find("option:selected").data('id');
                if (capdurum === 0) //cap kontrol edilmiyor
                {
                    $("#abonesayaccap"+(no)).select2("val",1).valid();
                    $("#abonesayaccaplari"+(no)).val(1);
                    $(".abonesayaccap"+(no)).prop("disabled", true);
                } else {
                    $("#abonesayaccap"+(no)).select2("val","").valid();
                    $("#abonesayaccaplari"+(no)).val("");
                    $(".abonesayaccap"+(no)).prop("disabled", false);
                }
            }else{
                $("#abonesayaccap"+(no)).select2("val","").valid();
                $("#abonesayaccaplari"+(no)).val("");
                $(".abonesayaccap"+(no)).prop("disabled", false);
            }
            $(this).valid();
        });
        $('.abonesayaccap').on('change', function (){
            var id = $(this).val();
            var no = $(this).closest('.abonesayaclar_ek').children('.no').val();
            $("#abonesayaccaplari"+(no)).val(id);
            $(this).valid();
        });
        $('.abonesatirsil').click(function(){
            if($('.abonesayaclar .abonesayaclar_ek').size()>0){
                var sayac=$(this).closest('.abonesayaclar_ek');
                var adet = sayac.children('.aboneadet').val();
                sayac.children('.aboneadet').val(0);
                sayac.remove();
                sayaccount-=adet;
                $("#sayaccount").val(sayaccount);
                $('.sayaccount').html(sayaccount+' Adet');
                /*if(sayaccount==0)
                    $(".kaydet").prop('disabled',true);*/
                var j=0;
                $('.abonesayaclar .abonesayaclar_ek').each(function(){
                    var id=$( this ).children('.no').val();
                    $( this).children('div').children('h4').children('.accordion-toggle').attr('href','#abonecollapse_'+j);
                    $( this).children('.panel-collapse').attr('id','abonecollapse_'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.aboneserino').attr('id','aboneserino'+j).attr('name','aboneserino['+j+']');
                    $( this).children('div').children('div').children('div').children('div').children('.abonesayactur').attr('id','abonesayactur'+j).attr('name','abonesayacturleri['+j+']');
                    $( this).children('div').children('div').children('div').children('div').children('.abonesayactur').removeClass('abonesayactur'+id).addClass('abonesayactur'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.abonesayacadi').attr('id','abonesayacadi'+j).attr('name','abonesayacadlari['+j+']');
                    $( this).children('div').children('div').children('div').children('div').children('.abonesayacadi').removeClass('abonesayacadi'+id).addClass('abonesayacadi'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.abonesayaccap').attr('id','abonesayaccap'+j).attr('name','abonesayaccap['+j+']');
                    $( this).children('div').children('div').children('div').children('div').children('.abonesayaccap').removeClass('abonesayaccap'+id).addClass('abonesayaccap'+j);
                    $( this).children('div').children('div').children('div').children('div').children('.abonesayaccaplari').attr('id', 'abonesayaccaplari' + j).attr('name', 'abonesayaccaplari[]');
                    $( this).children('div').children('div').children('div').children('div').children('.abonesayacadresi').attr('id','abonesayacadresi'+j).attr('name','abonesayacadresi['+j+']');
                    $( this).children('div').children('div').children('div').children('div').children('.abonesayacbilgi').attr('id','abonesayacbilgi'+j).attr('name','abonesayacbilgi['+j+']');
                    $( this).children('div').children('div').children('div').children('div').children('.abonesayaciletisim').attr('id','abonesayaciletisim'+j).attr('name','abonesayaciletisim['+j+']');
                    $( this ).children('.no').val(j);
                    j++;
                });
                aboneflag=0;
                $('input[name^="aboneserino"]').css("background-color", "#FFFFFF");
                $('input[name^="aboneserino"]').each(function (i,el1) {
                    var current_val = jQuery(el1).val();
                    if (current_val !== "") {
                        $('input[name^="aboneserino"]').each(function (i,el2) {
                            if (jQuery(el2).val() === current_val && jQuery(el1).attr("id") !== jQuery(el2).attr("id")) {
                                jQuery(el2).css("background-color", "yellow");
                                jQuery(el1).css("background-color", "yellow");
                                aboneflag=1;
                            }
                        });
                    }
                });
                if (sayaccount > 0){
                    if(aboneflag === 0)
                        $(".abonekaydet").prop('disabled', false);
                    else
                        $(".abonekaydet").prop('disabled', true);
                }else{
//                    $(".kaydet").prop('disabled', true);
                }
            }
        });
        $('input[name^="aboneserino"]').change(function () {
            $(".kaydet").prop('disabled', false);
            $('input[name^="aboneserino"]').css("background-color", "#FFFFFF");
            $('input[name^="aboneserino"]').each(function (i, el1) {
                var current_val = jQuery(el1).val();
                if (current_val !== "") {
                    $('input[name^="aboneserino"]').each(function (i, el2) {
                        if (jQuery(el2).val() === current_val && jQuery(el1).attr("id") !== jQuery(el2).attr("id")) {
                            jQuery(el2).css("background-color", "yellow");
                            jQuery(el1).css("background-color", "yellow");
                            $(".abonekaydet").prop('disabled', true);
                        }
                    });
                }
            });
        });
        $(".aboneserino").inputmask("mask", {
            mask:"9",repeat:15,greedy:!1
        });
        if (sayaccount > 0) {
            if (aboneflag === 0)
                $(".abonekaydet").prop('disabled', false);
            else
                $(".abonekaydet").prop('disabled', true);
        }else{
//            $(".kaydet").prop('disabled', true);
        }
        $('#aboneformsubmit').click(function () {
            var adisoyadi = $('#aboneadisoyadi').val();
            var uretimyer = $('#aboneuretimyer').val();
            var netsiscari = $('#abonecariadi').val();
            var vergidairesi = $('#abonevergidairesi').val();
            var tckimlikno = $('#abonetckimlikno').val();
            var aboneno = $('#aboneaboneno').val();
            var telefon = $('#abonetelefon').val();
            var adres = $('#aboneadres').val();
            var il = $('#aboneil').val();
            var ilce = $('#aboneilce').val();
            var subekodu = $('#abonesubekodu').val();
            var sayaccount = $('#sayaccount').val();
            var faturano = $('#eskifaturano').val();
            var sayaclar = [];
            for(var i=0;i<sayaccount;i++){
                var sayac = {};
                sayac["serino"] = $('#aboneserino'+i).val();
                sayac["sayactur"] = $('#abonesayactur'+i).val();
                sayac["sayacadi"] = $('#abonesayacadi'+i).val();
                sayac["sayaccapi"] = $('#abonesayaccap'+i).val();
                sayac["sayacadresi"] = $('#abonesayacadresi'+i).val();
                sayac["sayacbilgi"] = $('#abonesayacbilgi'+i).val();
                sayac["sayaciletisim"] = $('#abonesayaciletisim'+i).val();
                sayaclar.push(sayac);
            }
            $.blockUI();
            $.post("{{ URL::to('sube/hizliaboneekle') }}", {adisoyadi: adisoyadi, uretimyer:uretimyer, netsiscari:netsiscari, vergidairesi:vergidairesi,
                tckimlikno:tckimlikno, aboneno:aboneno, telefon:telefon, adres:adres, il:il, ilce:ilce, subekodu:subekodu, sayaclar:sayaclar,faturano:faturano }, function (event) {
                toastr[event.type](event.text, event.title);
                if(event.durum){
                    var abone = event.abone;
                    var uretimyer = event.uretimyer;
                    $('#yeniabone').modal('hide');
                    $("#cariadi").select2('val',netsiscari);
                    $('#abone').val(abone.id);
                    $('#abone').trigger('change');
                    $("#adres").val(adres).valid();
                    $("#faturaadresi").val(adres).valid();
                    $('#faturano').val(event.faturano);
                    $('#telefon').val(telefon).trigger('input');
                    $('.uretimyer').text(uretimyer.yeradi);
                    $('#tckimlikno').val(tckimlikno);
                    $('#odemesekli').select2('val','').trigger('change');
                }
                $.unblockUI();
            });
        });
        $("select").on("select2-close", function () { $(this).valid(); });
    });
</script>
<script>
    $(document).ready(function() {
        $('#nakit').maskMoney({suffix: ' ₺',affixesStay:true,allowNegative: false, allowZero:true});
        $('#kredikart1').maskMoney({suffix: ' ₺',affixesStay:true,allowNegative: false, allowZero:true});
        $('#faturavar').on('change', function () {
            if ($('#faturavar').attr('checked')) {
                $(".faturakismi").removeClass('hide');
            } else {
                $(".faturakismi").addClass('hide');
            }
        });
        $('#odemesekli').on('change',function(){
            var odemetipi = $(this).val();
            var count=parseInt($("#count").val());
            var tip = $(this).find("option:selected").data('id');
            $('#odemetipi').val(tip);
            $('#taksit').prop('disabled',false);
            $('#taksit2').prop('disabled',false);
            $("#kasakod>option").prop('disabled',true);
            $("#kasakod>option[data-id='0']").prop('disabled',false);
            $('.kasakod').removeClass('hide');
            $("#kasakod2>option").prop('disabled',true);
            $("#kasakod2>option[data-id='0']").prop('disabled',false);
            $('.kasakod2').removeClass('hide');
            var toplam = parseFloat($('#geneltoplam').val());
            var kdv = parseFloat($('#genelkdv').val());
            var tutar = parseFloat($('#geneltutar').val());
            var parabirimi = $('#birim').val();
            switch(odemetipi){
                case 'NAKİT' :
                    $('.taksit').addClass('hide');
                    $('.nakitkredikart').addClass('hide');
                    $('.ikikredikarti').addClass('hide');
                    $("#kasakod>option[data-id='1']").prop('disabled',false);
                    break;
                case 'KREDİ KARTI' :
                    $('.taksit').removeClass('hide');
                    $('.nakitkredikart').addClass('hide');
                    $('.ikikredikarti').addClass('hide');
                    $("#kasakod>option[data-id='2']").prop('disabled',false);
                    $('#taksit').select2('val',1);
                    break;
                case 'SENET' :
                    $('.taksit').addClass('hide');
                    $('.nakitkredikart').addClass('hide');
                    $('.ikikredikarti').addClass('hide');
                    $("#kasakod>option[data-id='3']").prop('disabled',false);
                    break;
                case 'NAKİT+KREDİ KARTI' :
                    $('.taksit').removeClass('hide');
                    $('.nakitkredikart').removeClass('hide');
                    $('.ikikredikarti').addClass('hide');
                    $("#kasakod>option[data-id='2']").prop('disabled',false);
                    $('#taksit').select2('val',1);
                    $('#nakit').maskMoney('mask',toplam*100);
                    $('#kredikart').maskMoney('mask',0);
                    $('.kredikart').text('0.00 '+parabirimi);
                    break;
                case 'BANKA HAVALESİ' :
                    $('.taksit').addClass('hide');
                    $('.nakitkredikart').addClass('hide');
                    $('.ikikredikarti').addClass('hide');
                    $('.kasakod').addClass('hide');
                    break;
                case '2 KREDİ KARTI' :
                    $('.taksit').removeClass('hide');
                    $('.nakitkredikart').addClass('hide');
                    $('.ikikredikarti').removeClass('hide');
                    $("#kasakod>option[data-id='2']").prop('disabled',false);
                    $("#kasakod2>option[data-id='2']").prop('disabled',false);
                    $('#taksit').select2('val',1);
                    $('#taksit2').select2('val',1);
                    $('#kredikart1').maskMoney('mask',toplam*100);
                    $('#kredikart2').maskMoney('mask',0);
                    $('.kredikart2').text('0.00 '+parabirimi);
                    $('#kredikartilk').val(toplam);
                    $('#kredikartilk2').val(0);
                    $('#taksit').prop('disabled',true);
                    $('#taksit2').prop('disabled',true);
                    break;
                case 'BELLİ DEĞİL' :
                    $('.taksit').addClass('hide');
                    $('.nakitkredikart').addClass('hide');
                    $('.ikikredikarti').addClass('hide');
                    $('.kasakod').addClass('hide');
                    break;
                case '' :
                    $('.taksit').addClass('hide');
                    $('.nakitkredikart').addClass('hide');
                    $('.ikikredikarti').addClass('hide');
                    break;
            }
            $('#tutar').val(tutar.toFixed(2));
            $('#kdvtutar').val(kdv.toFixed(2));
            $('#toplamtutar').val(toplam.toFixed(2));
            $('.tutar').html('<b>'+tutar.toFixed(2)+' '+parabirimi+'</b>');
            $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+parabirimi+'</b>');
            $('.toplamtutar').html('<b>'+toplam.toFixed(2)+' '+parabirimi+'</b>');
            $("#kasakod").select2("val","").valid();
            $("#kasakod2").select2("val","");
            for(var i=0;i<count;i++){
                var gfiyat = $('#gfiyat'+(i)).val();
                if($("#ucretsiz"+(i)).is(':checked'))
                    gfiyat=0;
                $('#fiyat'+(i)).maskMoney('mask',gfiyat*100);
            }
            $('#kredikart1').prop('readonly', false);
            $('#kredikart2').prop('readonly', false);
        });
        $('#nakit').on('change',function(){
            var count=parseInt($("#count").val());
            var nakit = parseFloat($(this).maskMoney('unmasked')[0]);
            var geneltoplam = parseFloat($('#geneltoplam').val());
            var parabirimi = $('#birim').val();
            var komisyon =parseFloat($('#taksit').find("option:selected").data('id'));
            nakit = nakit>=geneltoplam ? geneltoplam : nakit;
            var kalan = nakit ;
            var kredikart = 0;
            for(var i=0;i<count;i++){
                var tutar = $('#gfiyat'+(i)).val();
                var adet = $('#miktar'+(i)).val();
                if($("#ucretsiz"+(i)).is(':checked'))
                    tutar=0;
                if(tutar>0){
                    if( komisyon > 0 ){
                        if( kalan > 0){
                            if( kalan > (tutar*adet)){
                                kalan -= (tutar*adet);
                            }else{
                                var fark = tutar*adet - kalan;
                                fark = (fark*(100+komisyon)/100);
                                tutar = Math.round(((kalan+fark)/adet)*100)/100;
                                kredikart += ((tutar*adet)-kalan);
                                kalan = 0;
                            }
                        }else{
                            tutar = Math.round((tutar*(100+komisyon)/100)*100)/100;
                            kredikart +=(tutar*adet);
                        }
                    }
                }
                $('#fiyat'+(i)).maskMoney('mask',tutar*100);
            }
            if( komisyon===0 ){
                kredikart = (geneltoplam-nakit);
            }
            var toplam = nakit+kredikart;
            var kdv = Math.round(((toplam*18)/118)*100)/100;
            var fiyat = toplam - kdv;
            $('#nakit').maskMoney('mask',nakit*100);
            $('#kredikart').maskMoney('mask',kredikart*100);
            $('.kredikart').text(kredikart.toFixed(2)+' '+parabirimi);
            $('#tutar').val(fiyat.toFixed(2));
            $('#kdvtutar').val(kdv.toFixed(2));
            $('#toplamtutar').val(toplam.toFixed(2));
            $('.tutar').html('<b>'+fiyat.toFixed(2)+' '+parabirimi+'</b>');
            $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+parabirimi+'</b>');
            $('.toplamtutar').html('<b>'+toplam.toFixed(2)+' '+parabirimi+'</b>');
        });

        $('#kredikart1').on('change',function(){
            var kredikart = parseFloat($('#kredikart1').maskMoney('unmasked')[0]);
            var kredikart2 = parseFloat($('#kredikart2').maskMoney('unmasked')[0]);
            var geneltoplam = parseFloat($('#geneltoplam').val());
            var parabirimi = $('#birim').val();
            var komisyon =parseFloat($('#taksit').find("option:selected").data('id'));
            var komisyon2 =parseFloat($('#taksit2').find("option:selected").data('id'));
            if($('#taksit').prop( "disabled" ) || (komisyon===0 && komisyon2===0)){
                kredikart = kredikart >= geneltoplam ? geneltoplam : kredikart;
                kredikart2 = geneltoplam - kredikart;
            }
            var toplam = kredikart+kredikart2;
            var kdv = Math.round(((toplam*18)/118)*100)/100;
            var fiyat = toplam - kdv;
            $('#kredikart1').maskMoney('mask',kredikart*100);
            $('#kredikart2').maskMoney('mask',kredikart2*100);
            $('#kredikartilk').val(kredikart);
            $('#kredikartilk2').val(kredikart2);
            $('#tutar').val(fiyat.toFixed(2));
            $('#kdvtutar').val(kdv.toFixed(2));
            $('#toplamtutar').val(toplam.toFixed(2));
            $('.tutar').html('<b>'+fiyat.toFixed(2)+' '+parabirimi+'</b>');
            $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+parabirimi+'</b>');
            $('.toplamtutar').html('<b>'+toplam.toFixed(2)+' '+parabirimi+'</b>');
            if(kredikart>0 && kredikart2>0){
                $('#taksit').prop('disabled',false);
                $('#taksit2').prop('disabled',false);
            }else{
                $('#taksit').prop('disabled',true);
                $('#taksit2').prop('disabled',true);
            }
        });
        $('#kredikart2').on('change',function(){
            var kredikart = parseFloat($('#kredikart1').maskMoney('unmasked')[0]);
            var kredikart2 = parseFloat($('#kredikart2').maskMoney('unmasked')[0]);
            var geneltoplam = parseFloat($('#geneltoplam').val());
            var parabirimi = $('#birim').val();
            var komisyon =parseFloat($('#taksit').find("option:selected").data('id'));
            var komisyon2 =parseFloat($('#taksit2').find("option:selected").data('id'));
            if($('#taksit').prop( "disabled" ) || (komisyon===0 && komisyon2===0)){
                kredikart2 = kredikart2 >= geneltoplam ? geneltoplam : kredikart2;
                kredikart = geneltoplam - kredikart2;
            }
            var toplam = kredikart+kredikart2;
            var kdv = Math.round(((toplam*18)/118)*100)/100;
            var fiyat = toplam - kdv;
            $('#kredikart1').maskMoney('mask',kredikart*100);
            $('#kredikart2').maskMoney('mask',kredikart2*100);
            $('#kredikartilk').val(kredikart);
            $('#kredikartilk2').val(kredikart2);
            $('#tutar').val(fiyat.toFixed(2));
            $('#kdvtutar').val(kdv.toFixed(2));
            $('#toplamtutar').val(toplam.toFixed(2));
            $('.tutar').html('<b>'+fiyat.toFixed(2)+' '+parabirimi+'</b>');
            $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+parabirimi+'</b>');
            $('.toplamtutar').html('<b>'+toplam.toFixed(2)+' '+parabirimi+'</b>');
            if(kredikart>0 && kredikart2>0){
                $('#taksit').prop('disabled',false);
                $('#taksit2').prop('disabled',false);
            }else{
                $('#taksit').prop('disabled',true);
                $('#taksit2').prop('disabled',true);
            }
        });

        $('#taksit').on('change',function(){
            var count=parseInt($("#count").val());
            var komisyon = parseFloat($(this).find("option:selected").data('id'));
            var geneltoplam = parseFloat($('#geneltoplam').val());
            var parabirimi = $('#birim').val();
            var odemesekli = $('#odemesekli').val();
            var toplam = 0;
            var nakit,kredikart,kalan,kalemtoplam,fark,i,tutar,adet;
            if(odemesekli === 'KREDİ KARTI'){
                for(i=0;i<count;i++){
                    tutar = $('#gfiyat'+(i)).val();
                    adet = $('#miktar'+(i)).val();
                    if($("#ucretsiz"+(i)).is(':checked'))
                        tutar=0;
                    if(tutar>0){
                        if( komisyon > 0 ){
                            tutar = Math.round((tutar*(100+komisyon)/100)*100)/100;
                        }
                        toplam +=(tutar*adet);
                    }
                    $('#fiyat'+(i)).maskMoney('mask',tutar*100);
                }
            }else if(odemesekli === 'NAKİT+KREDİ KARTI'){
                nakit = parseFloat($('#nakit').maskMoney('unmasked')[0]);
                nakit = nakit>=geneltoplam ? geneltoplam : nakit;
                kalan = nakit ;
                kredikart = 0;
                for(i=0;i<count;i++){
                    tutar = $('#gfiyat'+(i)).val();
                    adet = $('#miktar'+(i)).val();
                    if($("#ucretsiz"+(i)).is(':checked'))
                        tutar=0;
                    if(tutar>0){
                        if( komisyon > 0 ){
                            if( kalan > 0){
                                if( kalan >= tutar*adet){
                                    kalan -= (tutar*adet);
                                }else{
                                    fark = (tutar*adet) - kalan;
                                    fark = (fark*(100+komisyon)/100);
                                    tutar = Math.round(((kalan+fark)/adet)*100)/100;
                                    kredikart += ((tutar*adet)-kalan);
                                    kalan = 0;
                                }
                            }else{
                                tutar = Math.round((tutar*(100+komisyon)/100)*100)/100;
                                kredikart +=(tutar*adet);
                            }
                        }
                    }
                    $('#fiyat'+(i)).maskMoney('mask',tutar*100);
                }
                if( komisyon===0 ){
                    kredikart = (geneltoplam-nakit);
                }
                toplam = nakit+kredikart;
                $('#kredikart').maskMoney('mask',kredikart*100);
                $('.kredikart').text(kredikart.toFixed(2)+' '+parabirimi);
            }else if(odemesekli === '2 KREDİ KARTI'){
                var komisyon2 =parseFloat($('#taksit2').find("option:selected").data('id'));
                kredikart = 0;
                var kredikart2 = 0;
                var kredikartilk = parseFloat($('#kredikartilk').val());
                var kredikartilk2 = parseFloat($('#kredikartilk2').val());
                if(komisyon>0 || komisyon2>0){
                    $('#kredikart1').prop('readonly',true);
                    $('#kredikart2').prop('readonly',true);
                }else{
                    $('#kredikart1').prop('readonly',false);
                    $('#kredikart2').prop('readonly',false);
                }
                toplam = Math.round((kredikartilk*(100+komisyon)/100)*100)/100 +  Math.round((kredikartilk2*(100+komisyon2)/100)*100)/100;
                kalan=kredikartilk;
                var kalan2=kredikartilk2;
                for(i=0;i<count;i++){
                    tutar = $('#gfiyat'+(i)).val();
                    adet = $('#miktar'+(i)).val();
                    kalemtoplam=tutar*adet;
                    if($("#ucretsiz"+(i)).is(':checked'))
                        tutar=0;
                    if(tutar>0) {
                        if (kalan > 0) {
                            if (kalan >= tutar * adet) {
                                kalan -= (tutar * adet);
                                tutar = (kalemtoplam * (100 + komisyon) / 100);
                                kredikart += Math.round((tutar) * 100) / 100;
                                tutar = Math.round(((tutar) / adet) * 100) / 100;
                            } else {
                                fark = (tutar * adet) - kalan;
                                tutar = (kalan * (100 + komisyon) / 100);
                                kredikart += Math.round((tutar) * 100) / 100;
                                kalan = 0;
                                if (fark > 0) {
                                    if (kalan2 > 0) {
                                        kalan2 -= (fark);
                                        var farktutar = (fark * (100 + komisyon2) / 100);
                                        kredikart2 += Math.round((farktutar) * 100) / 100;
                                        tutar += farktutar;
                                    }
                                }
                                tutar = Math.round(((tutar) / adet) * 100) / 100;
                            }
                        } else {
                            if (kalan2 > 0) {
                                if (kalan2 > tutar * adet) {
                                    kalan2 -= (tutar * adet);
                                    tutar = (kalemtoplam * (100 + komisyon2) / 100);
                                    kredikart2 += Math.round((tutar) * 100) / 100;
                                    tutar = Math.round(((tutar) / adet) * 100) / 100;
                                }else{
                                    fark = (tutar * adet) - kalan2;
                                    tutar = (kalan2 * (100 + komisyon2) / 100);
                                    kredikart2 += Math.round((tutar) * 100) / 100;
                                    kalan2 = 0;
                                }
                            }
                        }
                    }
                    $('#fiyat'+(i)).maskMoney('mask',tutar*100);
                }
            }
            var kdv = Math.round(((toplam*18)/118)*100)/100;
            var fiyat = toplam - kdv;
            $('#tutar').val(fiyat.toFixed(2));
            $('#kdvtutar').val(kdv.toFixed(2));
            $('#toplamtutar').val(toplam.toFixed(2));
            $('.tutar').html('<b>'+fiyat.toFixed(2)+' '+parabirimi+'</b>');
            $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+parabirimi+'</b>');
            $('.toplamtutar').html('<b>'+toplam.toFixed(2)+' '+parabirimi+'</b>');
            $('#kredikart1').maskMoney('mask',kredikart*100);
            $('#kredikart2').maskMoney('mask',kredikart2*100);
        });
        $('#taksit2').on('change',function() {
            var count=parseInt($("#count").val());
            var komisyon2 = parseFloat($(this).find("option:selected").data('id'));
            var komisyon = parseFloat($('#taksit').find("option:selected").data('id'));
            var parabirimi = $('#birim').val();
            var toplam, kredikart, kalan, kalemtoplam, fark,i,tutar,adet;
            kredikart = 0;
            var kredikart2 = 0;
            var kredikartilk = parseFloat($('#kredikartilk').val());
            var kredikartilk2 = parseFloat($('#kredikartilk2').val());
            if (komisyon > 0 || komisyon2 > 0) {
                $('#kredikart1').prop('readonly', true);
                $('#kredikart2').prop('readonly', true);
            } else {
                $('#kredikart1').prop('readonly', false);
                $('#kredikart2').prop('readonly', false);
            }
            toplam = Math.round((kredikartilk * (100 + komisyon) / 100) * 100) / 100 + Math.round((kredikartilk2 * (100 + komisyon2) / 100) * 100) / 100;
            kalan = kredikartilk;
            var kalan2 = kredikartilk2;
            for(i=0;i<count;i++){
                tutar = $('#gfiyat'+(i)).val();
                adet = $('#miktar'+(i)).val();
                kalemtoplam=tutar*adet;
                if($("#ucretsiz"+(i)).is(':checked'))
                    tutar=0;
                if (tutar > 0) {
                    if (kalan > 0) {
                        if (kalan > tutar * adet) {
                            kalan -= (tutar * adet);
                            tutar = (kalemtoplam * (100 + komisyon) / 100);
                            kredikart += Math.round((tutar) * 100) / 100;
                            tutar = Math.round(((tutar) / adet) * 100) / 100;
                        } else {
                            fark = (tutar * adet) - kalan;
                            tutar = (kalan * (100 + komisyon) / 100);
                            kredikart += Math.round((tutar) * 100) / 100;
                            kalan = 0;
                            if (fark > 0) {
                                if (kalan2 > 0) {
                                    kalan2 -= (fark);
                                    var farktutar = (fark * (100 + komisyon2) / 100);
                                    kredikart2 += Math.round((farktutar) * 100) / 100;
                                    tutar += farktutar;
                                }
                            }
                            tutar = Math.round(((tutar) / adet) * 100) / 100;
                        }
                    } else {
                        if (kalan2 > 0) {
                            if (kalan2 > tutar * adet) {
                                kalan2 -= (tutar * adet);
                                tutar = (kalemtoplam * (100 + komisyon2) / 100);
                                kredikart2 += Math.round((tutar) * 100) / 100;
                                tutar = Math.round(((tutar) / adet) * 100) / 100;
                            } else {
                                fark = (tutar * adet) - kalan2;
                                tutar = (kalan2 * (100 + komisyon2) / 100);
                                kredikart2 += Math.round((tutar) * 100) / 100;
                                kalan2 = 0;
                            }
                        }
                    }
                }
                $('#fiyat'+(i)).maskMoney('mask',tutar*100);
            }
            var kdv = Math.round(((toplam * 18) / 118) * 100) / 100;
            var fiyat = toplam - kdv;
            $('#tutar').val(fiyat.toFixed(2));
            $('#kdvtutar').val(kdv.toFixed(2));
            $('#toplamtutar').val(toplam.toFixed(2));
            $('.tutar').html('<b>'+fiyat.toFixed(2)+' '+parabirimi+'</b>');
            $('.kdvtutar').html('<b>'+kdv.toFixed(2)+' '+parabirimi+'</b>');
            $('.toplamtutar').html('<b>'+toplam.toFixed(2)+' '+parabirimi+'</b>');
            $('#kredikart1').maskMoney('mask', kredikart * 100);
            $('#kredikart2').maskMoney('mask', kredikart2 * 100);
        });

        $('#kasakod').on('change',function () {
            var kasakod = $(this).val();
            var kasakod2 = $('#kasakod2').val();
            var odemesekli = $('#odemesekli').val();
            if(odemesekli === '2 KREDİ KARTI') {
                if (kasakod === kasakod2) {
                    $("#kasakod2").select2("val", "");
                }
                $("#kasakod2>option[data-id='2']").prop('disabled',false);
                $("#kasakod2>option[value='" + kasakod + "']").prop('disabled', true);
            }
        });
        $('#kasakod2').on('change',function () {
            var kasakod2 = $(this).val();
            var kasakod = $('#kasakod').val();
            var odemesekli = $('#odemesekli').val();
            if(odemesekli === '2 KREDİ KARTI') {
                if (kasakod === kasakod2) {
                    $("#kasakod").select2("val", "");
                }
                $("#kasakod>option[data-id='2']").prop('disabled',false);
                $("#kasakod>option[value='"+kasakod2+"']").prop('disabled',true);
            }
        });
    });
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-pencil"></i>Fatura Bilgi Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('sube/sayacsatisduzenle/'.$sayacsatis->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <input type="text" class="hide" id="abone" name="abone" value="{{ Input::old('abone') ? Input::old('abone') : $sayacsatis->abone_id }}">
                    <label class="control-label col-sm-2 col-xs-4">Cari İsim:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="cariadi" name="cariadi" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @if((Input::old('cariadi') ? Input::old('cariadi') : $sayacsatis->netsiscari_id )==$sube->netsiscari_id )
                                <option value="{{ $sube->netsiscari_id }}" selected>{{ $sube->netsiscari->carikod.' - '.$sube->netsiscari->cariadi }}</option>
                            @else
                                <option value="{{ $sube->netsiscari_id }}">{{ $sube->netsiscari->carikod.' - '. $sube->netsiscari->cariadi }}</option>
                            @endif
                            @foreach($netsiscariler as $netsiscari)
                                @if((Input::old('cariadi') ? Input::old('cariadi') : $sayacsatis->netsiscari_id )==$netsiscari->id )
                                    <option value="{{ $netsiscari->id }}" selected>{{ $netsiscari->carikod.' - '.$netsiscari->cariadi.($netsiscari->efatura ? ' ( E-Fatura Müşterisi )' : '') }}</option>
                                @else
                                    <option value="{{ $netsiscari->id }}">{{ $netsiscari->carikod.' - '. $netsiscari->cariadi.($netsiscari->efatura ? ' ( E-Fatura Müşterisi )' : '') }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-xs-2">Kriter:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-3">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="kriter" name="kriter" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            <option value="1">Seri Numarası</option>
                            <option value="2">Adı Soyadı</option>
                            <option value="3">TC Kimlik No/Vergi Numarası</option>
                            <option value="4">Telefon</option>
                        </select>
                    </div>
                    <div class="input-icon right col-xs-3">
                        <i class="fa"></i><input type="text" id="kriterdeger" name="kriterdeger" data-required="1" class="form-control">
                    </div>
                    <div class="col-xs-4"><a class="btn green getir">Bilgileri Getir</a><a class="btn blue yeni" data-toggle="modal" data-target="#yeniabone">Yeni Abone</a></div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Abone:</label>
                        <label class="col-xs-8 abone" style="padding-top: 7px">{{$sayacsatis->abone->adisoyadi}}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-sm-4 col-xs-3">TC Kimlik / Vergi No:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-sm-8 col-xs-9">
                            <i class="fa"></i><input type="text" id="tckimlikno" name="tckimlikno" value="{{ Input::old('tckimlikno') ? Input::old('tckimlikno') :$sayacsatis->abone->tckimlikno }}" maxlength="11" data-required="1" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Kayıt Yeri:</label>
                        <label class="col-xs-8 uretimyer" style="padding-top: 7px">{{$sayacsatis->uretimyer->yeradi}}</label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-sm-4 col-xs-3">Telefonu:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-sm-8 col-xs-9">
                            <i class="fa"></i><input type="tel" id="telefon" name="telefon" value="{{ Input::old('telefon') ? Input::old('telefon') : $sayacsatis->abone->telefon }}" data-required="1" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Fatura Adresi :<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><input type="text" id="adres" name="adres" value="{{ Input::old('adres') ? Input::old('adres') : $sayacsatis->abone->faturaadresi }}" maxlength="100" data-required="1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Fatura Tarihi: <span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><div class="input-group input-medium date date-picker" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                <input id="tarih" type="text" name="tarih" class="form-control" value="{{Input::old('tarih') ? Input::old('tarih') : date("d-m-Y", strtotime($sayacsatis->faturatarihi)) }}">
                                <span class="input-group-btn"><button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-12 {{$sayacsatis->faturano!=NULL ? "" : "hide"}}">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Fatura Yılı :</label>
                        <div class="col-xs-8">
                            <input type="text" id="db_name" name="db_name" value="{{ Input::old('db_name') ? Input::old('db_name') : $sayacsatis->db_name }}" maxlength="100" data-required="1" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Fatura Numarası:</label>
                        <div class="col-xs-8">
                            <input type="text" id="eskifaturano" name="eskifaturano" value="{{ Input::old('eskifaturano') ? Input::old('eskifaturano') : $sayacsatis->faturano }}" maxlength="100" data-required="1" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4"> Eklenecek Ürünler </label>
                    <input type="text" id="count" name="count" value="{{Input::old('count') ? Input::old('count') : count($sayacsatis->urunler)}}" data-required="1" class="form-control hide">
                    <input class="hide" id="ucretsizler" name="ucretsizler" value="{{Input::old('ucretsizler') ? Input::old('ucretsizler') : $sayacsatis->ucretsiz }}"/>
                    <input class="hide" id="baglantidurumlari" name="baglantidurumlari" value="{{Input::old('baglantidurumlari') ? Input::old('baglantidurumlari') : $sayacsatis->baglantidurumlari }}"/>
                </div>
                <div class="panel-group accordion sayaclar col-xs-12" id="accordion1">
                        @for($i=0;$i<(Input::old('count') ? (int)(Input::old('count')) : count($sayacsatis->urunler));$i++)
                            <div class="panel panel-default sayaclar_ek"><input class="no hide" value="{{$i}}"/><input class="adet hide" value="1"/>
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_{{$i}}">{{(Input::old('urunadi.'.$i.'') ? Input::old('urunadi.'.$i.'') : $sayacsatis->urunler[$i]->urunadi) .' - '. (Input::old('miktar.'.$i.'') ? Input::old('miktar.'.$i.'') : $sayacsatis->adetler[$i]) . ' ADET' }} </a>
                                    </h4>
                                </div>
                                <div id="collapse_{{$i}}" class="panel-collapse in">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-2 col-xs-4 control-label">Ürün Adı:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-sm-7 col-xs-6">
                                                <i class="fa"></i><select class="form-control select2me urunadi urunadi{{$i}}" id="urunadi{{$i}}" name="urunadi[]" tabindex="-1" title="">
                                                    @foreach($urunler as $urun)
                                                        @if((Input::old('urunadi.'.$i.'') ? Input::old('urunadi.'.$i.'') : $sayacsatis->urunler[$i]->id)==$urun->id)
                                                            <option data-id="{{ Input::old('gfiyat.'.$i.'') ? Input::old('gfiyat.'.$i.'') :  number_format($sayacsatis->fiyatlar[$i],2,'.','') }}" data-fiyat="{{ Input::old('birimfiyat.'.$i.'') ? Input::old('birimfiyat.'.$i.'') : number_format((($sayacsatis->fiyatlar[$i]*100)/118),2,'.','') }}" data-baglanti="{{ $urun->baglanti }}" data-birim="{{ $urun->parabirimi_id }}" data-value="{{ $urun->parabirimi->birimi }}" value="{{ $urun->id }}" selected>{{ $urun->netsisstokkod->kodu.' - '.$urun->urunadi.'('.intval($urun->stok->BAKIYE).')'}}</option>
                                                        @else
                                                            <option data-id="{{ 0.00 }}" data-fiyat="{{ 0.00 }}" data-baglanti="{{ $urun->baglanti }}" data-birim="{{ $urun->parabirimi_id }}" data-value="{{ $urun->parabirimi->birimi }}" value="{{ $urun->id }}" >{{ $urun->netsisstokkod->kodu.' - '.$urun->urunadi.'('.intval($urun->stok->BAKIYE).')' }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <label class="col-sm-1 col-xs-2"><a class="btn red satirsil">Sil</a></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="col-xs-4 control-label">Fiyatı:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><input type="tel" id="fiyat{{$i}}" name="fiyat[]" class="form-control fiyat" value="{{Input::old('fiyat.'.$i.'') ? Input::old('fiyat.'.$i.'') : number_format($sayacsatis->fiyatlar[$i],2,'.','').' '.$sayacsatis->parabirimi->birimi}}">
                                                <div class="hide">
                                                    <input type="text" id="birimfiyat{{$i}}" name="birimfiyat[]" class="form-control birimfiyat" value="{{Input::old('birimfiyat.'.$i.'')? Input::old('birimfiyat.'.$i.'') : number_format((($sayacsatis->fiyatlar[$i]*100)/118),2,'.','')}}">
                                                    <input type="text" id="gfiyat{{$i}}" name="gfiyat[]" class="form-control gfiyat" value="{{Input::old('gfiyat.'.$i.'') ? Input::old('gfiyat.'.$i.'') : number_format(($sayacsatis->gfiyatlar[$i]),2,'.','')}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="col-xs-4 control-label">Miktarı:</label>
                                            <div class="col-xs-4">
                                                <input type="text" id="miktar{{$i}}" name="miktar[]" maxlength="3" class="form-control miktar" value="{{ Input::old('miktar.'.$i.'') ? Input::old('miktar.'.$i.'') : $sayacsatis->adetler[$i]}}">
                                            </div>
                                            <div class="col-xs-4" style="margin-top: 5px;font-size: 15px">
                                                <input type="checkbox" id="ucretsiz{{$i}}" value="{{$i}}" name="ucretsiz[]" class="ucretsiz" {{(Input::old('ucretsizler') ? explode(',',Input::old('ucretsizler'))[$i] : $sayacsatis->ucretsizler[$i] ) ? 'checked' : ''}}/>Ücretsiz
                                            </div>
                                        </div>
                                        <div class="form-group baglanti baglantidurum{{$i}} {{(Input::old('baglantidurumlari') ? explode(',',Input::old('baglantidurumlari'))[$i] : $sayacsatis->baglantidurum[$i])=="1" ? "" : "hide"}}">
                                            <label class="col-sm-2 col-xs-4 control-label">Bağlantılı Sayaçlar:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-sm-9 col-xs-7">
                                                <i class="fa"></i><select class="form-control select2 select2-offscreen abonesayac abonesayac{{$i}}" id="abonesayac{{$i}}" name="abonesayac[{{$i}}][]" multiple="" tabindex="-1">
                                                    @foreach($abonesayaclari as $abonesayac)
                                                        <option value="{{$abonesayac->id}}">{{$abonesayac->serino}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @if(Input::old('abonesayac.'.$i.''))
                                                <div id="abonesayaclar{{$i}}" class="hide abonesayaclar{{$i}}">
                                                    @foreach(Input::old('abonesayac.'.$i.'') as $abonesayac)
                                                        {{$abonesayac}}
                                                    @endforeach
                                                </div>
                                            @else
                                                <div id="abonesayaclarekli{{$i}}" class="hide abonesayaclarekli{{$i}}">{{ count($sayacsatis->abonesayaclar)>$i ? $sayacsatis->abonesayaclar[$i] : '' }}</div>
                                            @endif
                                            <div class="hide">
                                                <input type="text" id="baglantidurum{{$i}}" name="baglantidurum[]" class="form-control baglantidurum" value="{{Input::old('baglantidurumlari') ? explode(',',Input::old('baglantidurumlari'))[$i] : $sayacsatis->baglantidurum[$i] }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                </div>
                <div class="form-group">
                    <div class="col-sm-3 col-xs-12 control-label" style="text-align: center;">
                        <a class="btn green ekle">&nbsp Ekle &nbsp </a>
                        <a class="btn red tumsil">&nbsp Tümünü Sil &nbsp </a>
                        <label class="control-label col-xs-10" style="text-align: center;"><span style="color:red" class="kurtarihi">Kur Tarihi: {{Input::old('kurtarih') ? Input::old('kurtarih') : $dovizkuru[0]->tarih}}</span></label>
                    </div>
                    <div class="col-sm-3 col-xs-12 control-label">
                        <label class="control-label col-xs-12 euro" style="padding-top: 9px;margin-left:5px;text-align:center;">Euro : {{$dovizkuru[0]->kurfiyati.' '.$parabirimi->birimi}}</label>
                        <label class="control-label col-xs-12 dolar" style="padding-top: 9px;margin-left:3px;text-align:center;">Dolar : {{$dovizkuru[1]->kurfiyati.' '.$parabirimi->birimi}}</label>
                        <label class="control-label col-xs-12 sterlin" style="padding-top: 9px;text-align:center;">Sterlin : {{$dovizkuru[2]->kurfiyati.' '.$parabirimi->birimi}}</label>
                        <input id="euro" class="hide" value="{{$dovizkuru[0]->kurfiyati}}">
                        <input id="dolar" class="hide" value="{{$dovizkuru[1]->kurfiyati}}">
                        <input id="sterlin" class="hide" value="{{$dovizkuru[2]->kurfiyati}}">
                    </div>
                    <div class="col-sm-5 col-xs-12 control-label">
                        <div class="col-xs-12">
                            <label class="control-label col-xs-6"><b>TUTAR:</b></label>
                            <label class="col-xs-4 tutar" style="padding-top: 9px"><b>{{ Input::old('tutar') ? Input::old('tutar').' '.Input::old('birim') : $sayacsatis->tutar.' '.$sayacsatis->parabirimi->birimi }}</b></label>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label col-xs-6"><b>KDV TUTARI:</b></label>
                            <label class="col-xs-4 kdvtutar" style="padding-top: 9px"><b>{{ Input::old('kdvtutar') ? Input::old('kdvtutar').' '.Input::old('birim') : $sayacsatis->kdv.' '.$sayacsatis->parabirimi->birimi }}</b></label>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label col-xs-6"><b>TOPLAM TUTAR:</b></label>
                            <label class="col-xs-4 toplamtutar" style="padding-top: 9px"><b>{{ Input::old('toplamtutar') ? Input::old('toplamtutar').' '.Input::old('birim') : $sayacsatis->toplamtutar.' '.$sayacsatis->parabirimi->birimi }}</b></label>
                        </div>
                        <input type="text" id="tutar" name="tutar" class="form-control hide" value="{{ Input::old('tutar') ? Input::old('tutar') : $sayacsatis->tutar }}">
                        <input type="text" id="kdvtutar" name="kdvtutar" class="form-control hide" value="{{ Input::old('kdvtutar') ? Input::old('kdvtutar') : $sayacsatis->kdv }}">
                        <input type="text" id="toplamtutar" name="toplamtutar" class="form-control hide" value="{{ Input::old('toplamtutar') ? Input::old('toplamtutar') : $sayacsatis->toplamtutar }}">
                        <input type="text" id="parabirimi" name="parabirimi" class="form-control hide" value="{{ Input::old('parabirimi') ? Input::old('parabirimi') : $sayacsatis->parabirimi_id }}">
                        <input type="text" id="birim" name="birim" class="form-control hide" value="{{ Input::old('birim') ? Input::old('birim') : $sayacsatis->parabirimi->birimi}}">
                        <input type="text" id="kurtarih" name="kurtarih" class="hide" value="{{Input::old('kurtarih') ? Input::old('kurtarih') : $dovizkuru[0]->tarih}}">
                        <input type="text" id="geneltutar" name="geneltutar" class="form-control hide" value="{{ Input::old('geneltutar') ? Input::old('geneltutar') : $sayacsatis->geneltutar  }}" >
                        <input type="text" id="genelkdv" name="genelkdv" class="form-control hide" value="{{ Input::old('genelkdv') ? Input::old('genelkdv') : $sayacsatis->genelkdv  }}" >
                        <input type="text" id="geneltoplam" name="geneltoplam" class="form-control hide" value="{{ Input::old('geneltoplam') ? Input::old('geneltoplam') : $sayacsatis->geneltoplam  }}" >
                    </div>
                </div>
                <h4 class="form-section">Fatura Bilgisi  <span style="font-size: 12px">Satış Faturası oluşturma sonrası Netsis tarafında oluşacak faturayı temsil edecek</span>
                    <label><input type="checkbox" id="faturavar" name="faturavar" {{$sayacsatis->faturano!=NULL ? "checked" : ""}}/> Fatura Çıkacak mı? </label></h4>
                <div class="form-group faturakismi">
                    <div class="form-group col-xs-12">
                        <label class="control-label col-xs-2">Fatura Adresi:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-sm-10 col-xs-8">
                            <i class="fa"></i><input type="text" id="faturaadresi" name="faturaadresi" value="{{ Input::old('faturaadresi')  ? Input::old('faturaadresi') : $sayacsatis->abone->faturaadresi}}" maxlength="100" data-required="1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">İl:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><select class="form-control select2me select2-offscreen" id="faturail" name="faturail" tabindex="-1" title="">
                                <option value="">Seçiniz...</option>
                                @foreach($iller as $il)
                                    @if((Input::old('faturail') ? Input::old('faturail') : $sayacsatis->iller_id)==$il->id )
                                        <option value="{{ $il->id }}" selected>{{ $il->adi }}</option>
                                    @else
                                        <option value="{{ $il->id }}">{{ $il->adi }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">İlçe:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><select class="form-control select2me select2-offscreen" id="faturailce" name="faturailce" tabindex="-1" title="">
                                <option value="">Seçiniz...</option>
                                @foreach($ilceler as $ilce)
                                    @if((Input::old('faturailce') ? Input::old('faturailce') : $sayacsatis->ilceler_id)==$ilce->id )
                                        <option value="{{ $ilce->id }}" selected>{{ $ilce->adi }}</option>
                                    @else
                                        <option value="{{ $ilce->id }}">{{ $ilce->adi }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Fatura No:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><input type="text" id="faturano" name="faturano" data-required="1" class="form-control" maxlength="15" placeholder="İrsaliye Sıra No" value="{{ Input::old('faturano') ? Input::old('faturano') : $sayacsatis->faturano }}">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Ödeme Şekli:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><select class="form-control select2me" id="odemesekli" name="odemesekli" tabindex="-1" title="">
                                <option data-id="0" {{(Input::old('odemetipi') ? Input::old('odemetipi') : $sayacsatis->odemetipi)=="0" ? "selected" : ""}} value="">Seçiniz...</option>
                                <option data-id="1" {{(Input::old('odemetipi') ? Input::old('odemetipi') : $sayacsatis->odemetipi)=="1" ? "selected" : ""}} value="NAKİT">NAKİT</option>
                                <option data-id="2" {{(Input::old('odemetipi') ? Input::old('odemetipi') : $sayacsatis->odemetipi)=="2" ? "selected" : ""}} value="KREDİ KARTI">KREDİ KARTI</option>
                                <option data-id="3" {{(Input::old('odemetipi') ? Input::old('odemetipi') : $sayacsatis->odemetipi)=="3" ? "selected" : ""}} value="SENET">SENET</option>
                                <option data-id="4" {{(Input::old('odemetipi') ? Input::old('odemetipi') : $sayacsatis->odemetipi)=="4" ? "selected" : ""}} value="NAKİT+KREDİ KARTI">NAKİT + KREDİ KARTI</option>
                                <option data-id="6" {{(Input::old('odemetipi') ? Input::old('odemetipi') : $sayacsatis->odemetipi)=="6" ? "selected" : ""}} value="2 KREDİ KARTI">2 FARKLI KREDİ KARTI</option>
                                <option data-id="5" {{(Input::old('odemetipi') ? Input::old('odemetipi') : $sayacsatis->odemetipi)=="5" ? "selected" : ""}} value="BANKA HAVALESİ">BANKA HAVALESİ</option>
                                <option data-id="7" {{(Input::old('odemetipi') ? Input::old('odemetipi') : $sayacsatis->odemetipi)=="7" ? "selected" : ""}} value="BELLİ DEĞİL">BELLİ DEĞİL (AÇIK FATURA)</option>
                            </select>
                        </div>
                        <div class="col-xs-4 hide">
                            <input type="text" id="odemetipi" name="odemetipi" class="form-control" value="{{Input::old('odemetipi') ? Input::old('odemetipi') : $sayacsatis->odemetipi}}">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12 kasakod">
                        <label class="control-label col-xs-4">Kasa:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><select class="form-control select2me" id="kasakod" name="kasakod" tabindex="-1" title="">
                                <option data-id="0" value="">Seçiniz...</option>
                                @foreach($kasakodlar as $kasakod)
                                    @if((Input::old('kasakod') ? Input::old('kasakod') : $sayacsatis->kasakodu)==$kasakod->kasakod )
                                        <option data-id="{{$kasakod->odemetipi}}" value="{{ $kasakod->kasakod }}" selected>{{ $kasakod->kasakod.' - '.$kasakod->kasaadi }}</option>
                                    @else
                                        <option data-id="{{$kasakod->odemetipi}}" value="{{ $kasakod->kasakod }}" disabled>{{ $kasakod->kasakod.' - '. $kasakod->kasaadi }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12 taksit hide">
                        <label class="control-label col-xs-4">Taksit:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><select class="form-control select2me" id="taksit" name="taksit" tabindex="-1" title="">
                                <option data-id="0"     {{(Input::old('taksit') ? Input::old('taksit') : $sayacsatis->taksit)=="1" ? "selected" : ""}} value="1">Tek Çekim</option>
                                <option data-id="6.15"  {{(Input::old('taksit') ? Input::old('taksit') : $sayacsatis->taksit)=="2" ? "selected" : ""}} value="2">2 Taksit</option>
                                <option data-id="7.25"  {{(Input::old('taksit') ? Input::old('taksit') : $sayacsatis->taksit)=="3" ? "selected" : ""}} value="3">3 Taksit</option>
                                <option data-id="8.35"  {{(Input::old('taksit') ? Input::old('taksit') : $sayacsatis->taksit)=="4" ? "selected" : ""}} value="4">4 Taksit</option>
                                <option data-id="9.20"  {{(Input::old('taksit') ? Input::old('taksit') : $sayacsatis->taksit)=="5" ? "selected" : ""}} value="5">5 Taksit</option>
                                <option data-id="10.10" {{(Input::old('taksit') ? Input::old('taksit') : $sayacsatis->taksit)=="6" ? "selected" : ""}} value="6">6 Taksit</option>
                                <option data-id="11.20" {{(Input::old('taksit') ? Input::old('taksit') : $sayacsatis->taksit)=="7" ? "selected" : ""}} value="7">7 Taksit</option>
                                <option data-id="11.90" {{(Input::old('taksit') ? Input::old('taksit') : $sayacsatis->taksit)=="8" ? "selected" : ""}} value="8">8 Taksit</option>
                                <option data-id="12.55" {{(Input::old('taksit') ? Input::old('taksit') : $sayacsatis->taksit)=="9" ? "selected" : ""}} value="9">9 Taksit</option>
                                <option data-id="13.55" {{(Input::old('taksit') ? Input::old('taksit') : $sayacsatis->taksit)=="10" ? "selected" : ""}} value="10">10 Taksit</option>
                                <option data-id="14.55" {{(Input::old('taksit') ? Input::old('taksit') : $sayacsatis->taksit)=="11" ? "selected" : ""}} value="11">11 Taksit</option>
                                <option data-id="15.25" {{(Input::old('taksit') ? Input::old('taksit') : $sayacsatis->taksit)=="12" ? "selected" : ""}} value="12">12 Taksit</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12 ikikredikarti hide">
                        <label class="control-label col-xs-4">Diğer Kart Kasa:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><select class="form-control select2me" id="kasakod2" name="kasakod2" tabindex="-1" title="">
                                <option data-id="0" value="">Seçiniz...</option>
                                @foreach($kasakodlar as $kasakod)
                                    @if((Input::old('kasakod2') ? Input::old('kasakod2') : $sayacsatis->kasakodu2)==$kasakod->kasakod )
                                        <option data-id="{{$kasakod->odemetipi}}" value="{{ $kasakod->kasakod }}" selected>{{ $kasakod->carikod.' - '.$kasakod->kasaadi }}</option>
                                    @else
                                        <option data-id="{{$kasakod->odemetipi}}" value="{{ $kasakod->kasakod }}" disabled>{{ $kasakod->carikod.' - '. $kasakod->kasaadi }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12 ikikredikarti hide">
                        <label class="control-label col-xs-4">Diğer Kart Taksit:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><select class="form-control select2me" id="taksit2" name="taksit2" tabindex="-1" title="">
                                <option data-id="0"     {{(Input::old('taksit2') ? Input::old('taksit2') : $sayacsatis->taksit2)=="1" ? "selected" : ""}} value="1">Tek Çekim</option>
                                <option data-id="6.15"  {{(Input::old('taksit2') ? Input::old('taksit2') : $sayacsatis->taksit2)=="2" ? "selected" : ""}} value="2">2 Taksit</option>
                                <option data-id="7.25"  {{(Input::old('taksit2') ? Input::old('taksit2') : $sayacsatis->taksit2)=="3" ? "selected" : ""}} value="3">3 Taksit</option>
                                <option data-id="8.35"  {{(Input::old('taksit2') ? Input::old('taksit2') : $sayacsatis->taksit2)=="4" ? "selected" : ""}} value="4">4 Taksit</option>
                                <option data-id="9.20"  {{(Input::old('taksit2') ? Input::old('taksit2') : $sayacsatis->taksit2)=="5" ? "selected" : ""}} value="5">5 Taksit</option>
                                <option data-id="10.10" {{(Input::old('taksit2') ? Input::old('taksit2') : $sayacsatis->taksit2)=="6" ? "selected" : ""}} value="6">6 Taksit</option>
                                <option data-id="11.20" {{(Input::old('taksit2') ? Input::old('taksit2') : $sayacsatis->taksit2)=="7" ? "selected" : ""}} value="7">7 Taksit</option>
                                <option data-id="11.90" {{(Input::old('taksit2') ? Input::old('taksit2') : $sayacsatis->taksit2)=="8" ? "selected" : ""}} value="8">8 Taksit</option>
                                <option data-id="12.55" {{(Input::old('taksit2') ? Input::old('taksit2') : $sayacsatis->taksit2)=="9" ? "selected" : ""}} value="9">9 Taksit</option>
                                <option data-id="13.55" {{(Input::old('taksit2') ? Input::old('taksit2') : $sayacsatis->taksit2)=="10" ? "selected" : ""}} value="10">10 Taksit</option>
                                <option data-id="14.55" {{(Input::old('taksit2') ? Input::old('taksit2') : $sayacsatis->taksit2)=="11" ? "selected" : ""}} value="11">11 Taksit</option>
                                <option data-id="15.25" {{(Input::old('taksit2') ? Input::old('taksit2') : $sayacsatis->taksit2)=="12" ? "selected" : ""}} value="12">12 Taksit</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 nakitkredikart hide">
                        <label class="control-label col-xs-2">Nakit:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-4">
                            <i class="fa"></i><input type="tel" id="nakit" name="nakit" data-required="1" class="form-control" maxlength="15" value="{{Input::old('nakit') ? Input::old('nakit').' '.Input::old('birim') : $sayacsatis->odeme.' '.$sayacsatis->parabirimi->birimi}}" placeholder="Ne Kadar Nakit Geçilecek">
                        </div>
                        <label class="control-label col-xs-2">Kredi Kartı:<span class="required" aria-required="true"> * </span></label>
                        <label class="col-xs-4 kredikart" style="padding-top: 9px">{{Input::old('kredikart') ? Input::old('kredikart').' '.Input::old('birim') : $sayacsatis->odeme2.' '.$sayacsatis->parabirimi->birimi}}</label>
                        <div class="input-icon right col-xs-4 hide">
                            <i class="fa"></i><input type="tel" id="kredikart" name="kredikart" data-required="1" class="form-control" maxlength="15" value="{{Input::old('kredikart') ? Input::old('kredikart') : $sayacsatis->odeme2}}" placeholder="Ne Kadar Kredi Kartından Çekilecek">
                        </div>
                    </div>
                    <div class="form-group col-xs-12 ikikredikarti hide">
                        <label class="control-label col-xs-2">1. Kredi Kartı:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-4">
                            <i class="fa"></i><input type="tel" id="kredikart1" name="kredikart1" data-required="1" class="form-control" maxlength="15"  value="{{Input::old('kredikart1') ? Input::old('kredikart1').' '.Input::old('birim') : $sayacsatis->odeme.' '.$sayacsatis->parabirimi->birimi}}" placeholder="Ne Kadar İlk Karttan Çekilecek">
                        </div>
                        <label class="control-label col-xs-2">2. Kredi Kartı:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-4">
                            <i class="fa"></i><input type="tel" id="kredikart2" name="kredikart2" data-required="1" class="form-control" maxlength="15"  value="{{Input::old('kredikart2') ? Input::old('kredikart2').' '.Input::old('birim') : $sayacsatis->odeme2.' '.$sayacsatis->parabirimi->birimi}}" placeholder="Ne Kadar Diğer Karttan Çekilecek">
                        </div>
                        <input type="text" id="kredikartilk" name="kredikartilk" data-required="1" class="form-control hide" maxlength="15"  value="{{Input::old('kredikartilk') ? Input::old('kredikartilk') : $sayacsatis->odeme}}">
                        <input type="text" id="kredikartilk2" name="kredikartilk2" data-required="1" class="form-control hide" maxlength="15" value="{{Input::old('kredikartilk2') ? Input::old('kredikartilk2') : $sayacsatis->odeme2}}">
                    </div>
                    <div class="form-group col-xs-12">
                        <label class="control-label col-xs-2">Açıklama:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-10">
                            <i class="fa"></i><input type="text" id="aciklama" name="aciklama" data-required="1" class="form-control" maxlength="100" value="{{Input::old('aciklama') ? Input::old('aciklama') : $sayacsatis->aciklama}}" placeholder="Seri Numaraları ya da Ücret Açıklaması( Tamir Bakım Ücreti,Smart Kart Bedeli vb.)">
                        </div>
                    </div>
                    <div class="form-group col-xs-12">
                        <label class="control-label col-xs-2">Ödemeyi Yapan:</label>
                        <div class="col-xs-6">
                            <input type="text" id="odemeyapan" name="odemeyapan" data-required="1" class="form-control" maxlength="100" value="{{Input::old('odemeyapan') ? Input::old('odemeyapan') : $sayacsatis->odemeyapan ? $sayacsatis->odemeyapan : ''}}" placeholder="Ödemeyi Yapan ile Fatura Sahibi Farklı ise Bilgi Amaçlı Eklenir">
                        </div>
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <input type="text" id="satisid" name="satisid" value="{{Input::old('satisid') ? Input::old('satisid') : $sayacsatis->id}}" data-required="1" class="form-control hide">
                        <button type="button" class="btn green kaydet" data-toggle="modal" data-target="#confirm">Kaydet</button>
                        <a href="{{ URL::to('sube/sayacsatis')}}" class="btn default">Vazgeç</a>
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
                    <h4 class="modal-title">Satış Kayıdı Tamamlanacak</h4>
                </div>
                <div class="modal-body">
                    Girilen Satış Kayıdı Bilgileri Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="abonelistesi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-search"></i>Abone Listesi
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Abone Listesi</h3>
                                        <div class="portlet-body">
                                            <input type="text" id="secilenabone" name="secilenabone" class="hide" >
                                            <table class="table table-striped table-hover table-bordered" id="sample_1">
                                                <thead>
                                                <tr>
                                                    <th class="id">#</th>
                                                    <th>Seri No</th>
                                                    <th>Adı Soyadı</th>
                                                    <th>Adresi</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" id="listesec" class="btn green">Seç</button>
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
    <div class="modal fade" id="yeniabone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Yeni Abone</h4>
                </div>
                <div class="modal-body">
                    <form id="form_sample_abone" class="form-horizontal" novalidate="novalidate">
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
                                <input type="text" id="abonesubekodu" name="abonesubekodu" value="{{ $sube ? $sube->subekodu : 1 }}" data-required="1" class="form-control">
                                <input type="text" id="abonesubelinked" name="abonesubelinked" value="{{ $sube ? $sube->subelinked : '' }}" data-required="1" class="form-control">
                                <input type="text" id="abonebellinked" name="abonebellinked" value="{{ $sube ? $sube->bellinked : ''}}" data-required="1" class="form-control">
                                <input type="text" id="abonenetsisdepo" name="abonenetsisdepo" value="{{ $sube ? $sube->netsisdepolar_id : 1 }}" data-required="1" class="form-control">
                                <input type="text" id="abonenetsiscari" name="abonenetsiscari" value="{{ $sube ? $sube->netsiscari_id : 2631 }}" data-required="1" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2 col-xs-4">Cari İsim:<span class="required" aria-required="true"> * </span></label>
                                <div class="input-icon right col-xs-8">
                                    <i class="fa"></i><select class="form-control select2me select2-offscreen" id="abonecariadi" name="abonecariadi" tabindex="-1" title="">
                                        <option value="">Seçiniz...</option>
                                        @if(Input::old('abonecariadi')==$sube->netsiscari_id )
                                            <option value="{{ $sube->netsiscari_id }}" selected>{{ $sube->netsiscari->carikod.' - '.$sube->netsiscari->cariadi }}</option>
                                        @else
                                            <option value="{{ $sube->netsiscari_id }}">{{ $sube->netsiscari->carikod.' - '. $sube->netsiscari->cariadi }}</option>
                                        @endif
                                        @foreach($netsiscariler as $netsiscari)
                                            @if(Input::old('abonecariadi')==$netsiscari->id )
                                                <option value="{{ $netsiscari->id }}" selected>{{ $netsiscari->carikod.' - '.$netsiscari->cariadi }}</option>
                                            @else
                                                <option value="{{ $netsiscari->id }}">{{ $netsiscari->carikod.' - '. $netsiscari->cariadi }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-6 col-xs-12">
                                <label class="control-label col-sm-4 col-xs-3">Adı Soyadı: <span class="required" aria-required="true"> * </span></label>
                                <div class="input-icon right col-sm-8 col-xs-9">
                                    <i class="fa"></i><input type="text" id="aboneadisoyadi" name="aboneadisoyadi" value="{{ Input::old('aboneadisoyadi') }}" maxlength="200" data-required="1" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-sm-6 col-xs-12">
                                <label class="control-label col-sm-4 col-xs-3">Kayıt Yeri: <span class="required" aria-required="true"> * </span></label>
                                <div class="input-icon right col-sm-8 col-xs-9">
                                    <i class="fa"></i><select class="form-control select2me select2-offscreen" id="aboneuretimyer" name="aboneuretimyer" tabindex="-1" title="">
                                        <option value="">Seçiniz...</option>
                                        @foreach($uretimyerleri as $uretimyeri)
                                            @if(Input::old('aboneuretimyer')==$uretimyeri->id)
                                                <option value="{{ $uretimyeri->id }}" selected>{{ $uretimyeri->yeradi }}</option>
                                            @else
                                                <option value="{{ $uretimyeri->id }}" >{{ $uretimyeri->yeradi }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-6 col-xs-12">
                                <label class="control-label col-sm-4 col-xs-3">Vergi Dairesi:</label>
                                <div class="col-sm-8 col-xs-9">
                                    <input type="text" id="abonevergidairesi" name="abonevergidairesi" value="{{ Input::old('abonevergidairesi') }}" maxlength="30" data-required="1" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-sm-6 col-xs-12">
                                <label class="control-label col-sm-4 col-xs-3">TC Kimlik / Vergi No:</label>
                                <div class="input-icon right col-sm-8 col-xs-9">
                                    <i class="fa"></i><input type="text" id="abonetckimlikno" name="abonetckimlikno" value="{{ Input::old('abonetckimlikno') }}" maxlength="11" data-required="1" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-sm-6 col-xs-12">
                                <label class="control-label col-sm-4 col-xs-3">Abone No:</label>
                                <div class="col-sm-8 col-xs-9">
                                    <input type="text" id="aboneaboneno" name="aboneaboneno" value="{{ Input::old('aboneaboneno') }}" maxlength="10" data-required="1" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-sm-6 col-xs-12">
                                <label class="control-label col-sm-4 col-xs-3">Telefonu:</label>
                                <div class="col-sm-8 col-xs-9">
                                    <input type="tel" id="abonetelefon" name="abonetelefon" value="{{ Input::old('abonetelefon') }}" data-required="1" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-xs-12">
                                <label class="control-label col-sm-2 col-xs-4">Fatura Adresi:<span class="required" aria-required="true"> * </span></label>
                                <div class="input-icon right col-sm-10 col-xs-8">
                                    <i class="fa"></i><input type="text" id="aboneadres" name="aboneadres" value="{{ Input::old('aboneadres') }}" maxlength="100" data-required="1" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-sm-6 col-xs-12">
                                <label class="control-label col-xs-4">İl:<span class="required" aria-required="true"> * </span></label>
                                <div class="input-icon right col-xs-8">
                                    <i class="fa"></i><select class="form-control select2me select2-offscreen" id="aboneil" name="aboneil" tabindex="-1" title="">
                                        <option value="">Seçiniz...</option>
                                        @foreach($iller as $il)
                                            @if(Input::old('aboneil')==$il->id )
                                                <option value="{{ $il->id }}" selected>{{ $il->adi }}</option>
                                            @else
                                                <option value="{{ $il->id }}">{{ $il->adi }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-6 col-xs-12">
                                <label class="control-label col-xs-4">İlçe:<span class="required" aria-required="true"> * </span></label>
                                <div class="input-icon right col-xs-8">
                                    <i class="fa"></i><select class="form-control select2me select2-offscreen" id="aboneilce" name="aboneilce" tabindex="-1" title="">
                                        <option value="">Seçiniz...</option>
                                        @foreach($ilceler as $ilce)
                                            @if(Input::old('aboneilce')==$ilce->id )
                                                <option value="{{ $ilce->id }}" selected>{{ $ilce->adi }}</option>
                                            @else
                                                <option value="{{ $ilce->id }}">{{ $ilce->adi }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2 col-xs-4" > Aboneye Ait Sayaçlar: </label>
                                <label class="col-xs-2 sayaccount" style="padding-top: 9px">{{Input::old('sayaccount') ? Input::old('sayaccount') : 0 .' Adet'}}</label>
                                <input type="text" id="sayaccount" name="sayaccount" value="{{Input::old('sayaccount') ? Input::old('sayaccount') : 0}}" data-required="1" class="form-control hide">
                                <input type="text" id="abonesecilen" name="abonesecilen" value="{{Input::old('abonesecilen') ? Input::old('abonesecilen') : -1}}" data-required="1" class="form-control hide">
                            </div>
                            <div class="panel-group accordion abonesayaclar col-xs-12" id="aboneaccordion1">
                                @if(Input::old('sayaccount')!="0")
                                    @for($i=0;$i<(int)(Input::old('sayaccount'));$i++)
                                        <div class="panel panel-default abonesayaclar_ek"><input class="no hide" value="{{$i}}"/><input class="aboneadet hide" value="1"/>
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#aboneaccordion1" href="#abonecollapse_{{$i}}">{{Input::old('aboneserino.'.$i)}} </a>
                                                </h4>
                                            </div>
                                            <div id="abonecollapse_{{$i}}" class="panel-collapse in">
                                                <div class="panel-body">
                                                    <div class="form-group col-xs-12">
                                                        <label class="col-sm-2 col-xs-4 control-label">Seri No: <span class="required" aria-required="true"> * </span></label>
                                                        <div class="input-icon right col-sm-4 col-xs-6">
                                                            <i class="fa"></i><input type="text" id="aboneserino{{$i}}" name="aboneserino[{{$i}}]" maxlength="15" class="form-control abonevalid{{$i}} aboneserino" value="{{Input::old('aboneserino.'.$i.'')}}" />
                                                        </div>
                                                        <label class="col-sm-3 col-xs-4"><a class="btn green abonegetir">Bul</a><a class="btn red abonesatirsil">Sil</a></label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-2 col-xs-4">Sayaç Türü:<span class="required" aria-required="true"> * </span></label>
                                                        <div class="input-icon right col-sm-4 col-xs-8">
                                                            <i class="fa"></i><select class="form-control select2me abonevalid{{$i}} abonesayactur abonesayactur{{$i}}" id="abonesayactur{{$i}}" name="abonesayacturleri[{{$i}}]" tabindex="-1" title="">
                                                                <option value="">Seçiniz...</option>
                                                                @foreach($sayacturleri as $sayactur)
                                                                    @if(Input::old('abonesayacturleri.'.$i.'')==$sayactur->id)
                                                                        <option value="{{ $sayactur->id }}" selected>{{ $sayactur->tur }}</option>
                                                                    @else
                                                                        <option value="{{ $sayactur->id }}">{{ $sayactur->tur }}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-6 col-xs-12">
                                                        <label class="col-xs-4 control-label">Sayaç Adı:<span class="required" aria-required="true"> * </span></label>
                                                        <div class="input-icon right col-xs-8">
                                                            <i class="fa"></i><select class="form-control select2me abonevalid{{$i}} abonesayacadi abonesayacadi{{$i}}" id="abonesayacadi{{$i}}" name="abonesayacadlari[{{$i}}]" tabindex="-1" title="">
                                                                <option value="">Seçiniz...</option>
                                                                @foreach($sayacadlari as $sayacadi)
                                                                    @if((Input::old('abonesayacadlari.'.$i.''))==$sayacadi->id)
                                                                        <option data-id="{{ $sayacadi->cap }}" value="{{ $sayacadi->id }}" selected>{{ $sayacadi->sayacadi }}</option>
                                                                    @else
                                                                        <option data-id="{{ $sayacadi->cap }}" value="{{ $sayacadi->id }}" >{{ $sayacadi->sayacadi }}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-6 col-xs-12">
                                                        <label class="col-xs-4 control-label">Sayaç Çapı:<span class="required" aria-required="true"> * </span></label>
                                                        <div class="input-icon right col-xs-8">
                                                            <i class="fa"></i><select class="form-control select2me abonevalid{{$i}} abonesayaccap abonesayaccap{{$i}}" id="abonesayaccap{{$i}}" name="abonesayaccap[{{$i}}]" tabindex="-1" title="">
                                                                @foreach($sayaccaplari as $sayaccapi)
                                                                    @if((Input::old('abonesayaccaplari.'.$i.''))==$sayaccapi->id)
                                                                        <option value="{{ $sayaccapi->id }}" selected>{{ $sayaccapi->capadi }}</option>
                                                                    @else
                                                                        <option value="{{ $sayaccapi->id }}">{{ $sayaccapi->capadi }}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select><input type="text" id="abonesayaccaplari{{$i}}" name="abonesayaccaplari[]" class="abonesayaccaplari hide" value="{{Input::old('abonesayaccaplari.'.$i.'') ? Input::old('abonesayaccaplari.'.$i.'') : 1}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-xs-12">
                                                        <label class="col-sm-2 col-xs-4 control-label">Montaj Adresi:<span class="required" aria-required="true"> * </span></label>
                                                        <div class="input-icon right col-sm-10 col-xs-8">
                                                            <i class="fa"></i><input type="text" id="abonesayacadresi{{$i}}" name="abonesayacadresi[{{$i}}]" class="form-control abonevalid{{$i}} abonesayacadresi">
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-6 col-xs-12">
                                                        <label class="col-xs-4 control-label">Bilgi:</label>
                                                        <div class="col-xs-8">
                                                            <input type="text" id="abonesayacbilgi{{$i}}" name="abonesayacbilgi[{{$i}}]" class="form-control abonesayacbilgi" value="{{Input::old('abonesayacbilgi.'.$i.'')}}" >
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-6 col-xs-12">
                                                        <label class="col-xs-4 control-label">İletişim:</label>
                                                        <div class="col-xs-8">
                                                            <input type="text" id="abonesayaciletisim{{$i}}" name="abonesayaciletisim[{{$i}}]" class="form-control abonesayaciletisim" value="{{Input::old('abonesayaciletisim.'.$i.'')}}" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                @endif
                            </div>
                            <div class="form-group tuslar {{(int)(Input::old('sayaccount'))!=0 ? '' : 'hide'}}">
                                <div class="col-md-6 control-label" style="text-align: left;"><a class="btn green sayacekle">&nbsp Ekle &nbsp </a>
                                    <a class="btn red sayactumsil">&nbsp Tümünü Sil &nbsp </a></div>
                            </div>
                            <div class="form-group">{{ Form::token() }}</div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-xs-12" style="text-align: center">
                                    <button type="button" class="btn green abonekaydet" data-toggle="modal" data-target="#aboneconfirm">Kaydet</button>
                                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="aboneconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Abone Kayıdı Tamamlanacak</h4>
                </div>
                <div class="modal-body">
                    Girilen Abone Kayıdı Bilgileri Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="aboneformsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
@stop
