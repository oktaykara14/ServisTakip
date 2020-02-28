@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Şube Servis Kayıdı <small>Ekleme Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
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
<script src="{{ URL::to('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/datatables.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery-maskmoney/jquery.maskMoney.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript" ></script>

@stop

@section('page-script')
<script src="{{ URL::to('pages/sube/form-validation-7.js') }}"></script>
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
        $('.getir').click(function(){
            var kriter = $("#kriter").val();
            var kriterdeger = $("#kriterdeger").val();
            var subekodu = $("#subekodu").val();
            if (kriterdeger !== "" && kriter !== "" ) {
                $.blockUI();
                $.getJSON("{{ URL::to('sube/serviskayitbilgi') }}",{tip:kriter,kriter:kriterdeger,subekodu:subekodu}, function (event) {
                    if (event.durum)
                    {
                        if(event.count>1){
                            abonebilgi = event.abonebilgi;
                            oTable.clear().draw();
                            $.each(abonebilgi, function (index) {
                                oTable.row.add([abonebilgi[index].id,((abonebilgi[index].serino)==null ? '' : abonebilgi[index].serino),abonebilgi[index].adisoyadi,
                                    ((abonebilgi[index].adres)==null ? '' : abonebilgi[index].adres)])
                                    .draw();
                            });
                            $('#abonelistesi').modal('show');
                        }else {
                            var abonebilgi = event.abonebilgi[0];
                            $('#abone').val(abonebilgi.aboneid);
                            $('#abonesayac').val(abonebilgi.abonesayacid);
                            $('.abone').text(abonebilgi.adisoyadi);
                            $('.abonesayac').text(abonebilgi.serino);
                            $('#abonetelefon').val(abonebilgi.telefon).trigger('input').valid();
                            $('#telefon').val(abonebilgi.iletisim);
                            $('.uretimyer').text(abonebilgi.yeradi);
                            $('#sayacadres').val(abonebilgi.adres);
                            $('.tckimlikno').text(abonebilgi.tckimlikno);
                            if(abonebilgi.sayactur_id===2) {
                                birim = 'kWh';
                            }else {
                                birim = 'm³';
                            }
                             $('#ilkendes').maskMoney({suffix: " "+birim,precision:3});
                             $('#sonendeks').maskMoney({suffix: " "+birim,precision:3});
                             $('#sayacborcu').maskMoney({suffix: " "+birim,precision:3});
                             $('.toplamfark').html('0.000 '+birim);
                        }
                    } else {
                        toastr[event.type](event.text,event.title);
                    }
                    $.unblockUI();
                });
            }else{
                toastr['warning']('Kriter Bilgisi Girilmemiş', 'Kriter Hatası');
            }
        });
        $('#listesec').click(function () {
            var aboneid = $('#secilenabone').val();
            var subekodu = $("#subekodu").val();
            if ( aboneid !== "") {
                $.blockUI();
                $.getJSON("{{ URL::to('sube/listebilgigetir') }}", {id: aboneid,subekodu: subekodu}, function (event) {
                    if (event.durum) {
                        var abonebilgi = event.abonebilgi;
                        $('#abone').val(abonebilgi.aboneid);
                        $('#abonesayac').val(abonebilgi.abonesayacid);
                        $('.abone').text(abonebilgi.adisoyadi);
                        $('.abonesayac').text(abonebilgi.serino);
                        $('#abonetelefon').val(abonebilgi.telefon).trigger('input').valid();
                        $('#telefon').val(abonebilgi.iletisim);
                        $('.uretimyer').text(abonebilgi.yeradi);
                        $('#sayacadres').val(abonebilgi.adres);
                        $('.tckimlikno').text(abonebilgi.tckimlikno);
                        if(abonebilgi.sayactur_id===2) {
                            birim = 'kWh';
                        }else {
                            birim = 'm³';
                        }
                        $('#ilkendes').maskMoney({suffix: " "+birim,precision:3});
                        $('#sonendeks').maskMoney({suffix: " "+birim,precision:3});
                        $('#sayacborcu').maskMoney({suffix: " "+birim,precision:3});
                        $('.toplamfark').html('0.000 '+birim);
                    } else {
                        $('#abone').val('');
                        $('#abonesayac').val('');
                        $('.abone').text('');
                        $('.abonesayac').text('');
                        $('#abonetelefon').val('').trigger('input').valid();
                        $('#telefon').val('');
                        $('.uretimyer').text('');
                        $('#sayacadres').val('');
                        $('.tckimlikno').text('');
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                    $('#abonelistesi').modal('hide');
                });
            } else {
                toastr['warning']('Kriter Bilgisi Girilmemiş', 'Kriter Hatası');
            }
        });
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
        $('#formsubmit').click(function () {
            $('#form_sample').submit();
        });
        var abone = $('#abone').val();
        if(abone!==""){
            var abonesayac = $('#abonesayac').val();
            $.blockUI();
            $.getJSON("{{ URL::to('sube/serviskayitabonebilgi') }}",{id:abone,sayacid:abonesayac}, function (event) {
                if(event.durum){
                    var abonebilgi = event.abonebilgi;
                    $('.abone').text(abonebilgi.adisoyadi);
                    $('.abonesayac').text(abonebilgi.serino);
                    $('#abonetelefon').val(abonebilgi.telefon).trigger('input').valid();
                    $('#telefon').val(abonebilgi.iletisim);
                    $('.uretimyer').text(abonebilgi.yeradi);
                    $('#sayacadres').val(abonebilgi.adres);
                    $('.tckimlikno').text(abonebilgi.tckimlikno);
                    if(abonebilgi.sayactur_id===2) {
                        birim = 'kWh';
                    }else {
                        birim = 'm³';
                    }
                    $('#ilkendes').maskMoney({suffix: " "+birim,precision:3});
                    $('#sonendeks').maskMoney({suffix: " "+birim,precision:3});
                    $('#sayacborcu').maskMoney({suffix: " "+birim,precision:3});
                    $('.toplamfark').html('0.000 '+birim);
                }else {
                    $('.abone').text('');
                    $('.abonesayac').text('');
                    $('#abonetelefon').val('').trigger('input').valid();
                    $('#telefon').val('');
                    $('.uretimyer').text('');
                    $('#sayacadres').val('');
                    $('.tckimlikno').text('');
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
            var durum =$('#durum').val();
            var tipi = $('#tipi').val();
            if(durum==="1" && tipi!=="2"){
                $('.servissayacison').removeClass('hide');
            }else{
                $('.servissayacison').addClass('hide');
            }
            var ilkendeks=$('#ilkendeks').maskMoney('unmasked')[0];
            var sonendeks=$('#sonendeks').maskMoney('unmasked')[0];
            var sayacborcu=$('#sayacborcu').maskMoney('unmasked')[0];
            if(sonendeks!==0){
                var fark = sonendeks-ilkendeks+sayacborcu;
                $('.toplamfark').html(((fark.toFixed(3)).replace('.',','))+' '+birim);
            }else{
                $('.toplamfark').html('0,000 '+birim);
            }
        }
        $('#acilmatarihi').on('change', function() { $(this).valid(); });
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
            <i class="fa fa-pencil"></i>Servis Bilgisi Ekle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('sube/serviskayitekle') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <input type="text" id="abone" name="abone" value="{{ Input::old('abone')}}">
                    <input type="text" id="abonesayac" name="abonesayac" value="{{ Input::old('abonesayac')}}">
                    <input type="text" id="birim" name="birim" value="{{ Input::old('birim') ? Input::old('birim') : 'm³' }}">
                    <input type="text" id="smsgonder" name="smsgonder" value="{{ Input::old('smsgonder') ? Input::old('smsgonder') : 0 }}">
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-6">Kriter:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-3 col-xs-6">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="kriter" name="kriter" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            <option value="1">Seri Numarası</option>
                            <option value="2">Adı Soyadı</option>
                            <option value="3">TC Kimlik No/Vergi Numarası</option>
                            <option value="4">Telefon</option>
                        </select>
                    </div>
                    <div class="input-icon right col-sm-3 col-xs-6">
                        <i class="fa"></i><input type="text" id="kriterdeger" name="kriterdeger" data-required="1" class="form-control">
                    </div>
                    <div class="col-sm-2 col-xs-6"><a class="btn green getir">Bilgileri Getir</a></div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Abone:</label>
                        <label class="col-xs-8 abone" style="padding-top: 7px"></label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Kayıt Yeri:</label>
                        <label class="col-xs-8 uretimyer" style="padding-top: 7px"></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Abone Sayacı:</label>
                        <label class="col-xs-8 abonesayac" style="padding-top: 7px"></label>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">TC Kimlik No :</label>
                        <label class="col-xs-8 tckimlikno" style="padding-top: 7px"></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-4">Takılma Adresi :</label>
                    <div class="col-xs-8">
                        <input type="text" id="sayacadres" name="sayacadres" value="{{ Input::old('sayacadres') }}" maxlength="100" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Abone Telefon :<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><input type="tel" id="abonetelefon" name="abonetelefon" value="{{ Input::old('abonetelefon') }}" maxlength="17" data-required="1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Diğer Telefonlar :</label>
                        <div class="col-xs-8">
                            <input type="text" id="telefon" name="telefon" value="{{ Input::old('telefon') }}" maxlength="100" data-required="1" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Açıklama:</label>
                        <div class="col-xs-8">
                            <input type="text" id="aciklama" name="aciklama" value="{{ Input::old('aciklama')}}" data-required="1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Servis Nedeni:</label>
                        <div class="col-xs-8">
                            <select class="form-control select2me select2-offscreen" id="tipi" name="tipi" tabindex="-1" title="">
                                <option value="2" {{((Input::old('tipi') ? Input::old('tipi') : "2")=="2") ? 'selected' : '' }} >Arıza Kontrolü</option>
                                <option value="0" {{(Input::old('tipi')=="0") ? 'selected' : '' }} >Sayaç Montajı</option>
                                <option value="1" {{(Input::old('tipi')=="1") ? 'selected' : '' }} >Tamir Montajı</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Açılma Tarihi:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><div class="input-group input-medium date date-picker acilmatarihi" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                <input type="text" id="acilmatarihi" name="acilmatarihi" class="form-control" value="{{Input::old('acilmatarihi') ? Input::old('acilmatarihi') : '' }}">
                                <span class="input-group-btn"><button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Kapanma Tarihi:</label>
                        <div class="col-xs-8">
                            <div class="input-group input-medium date date-picker kapanmatarihi" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                <input type="text" id="kapanmatarihi" name="kapanmatarihi" class="form-control" value="{{Input::old('kapanmatarihi') ? Input::old('kapanmatarihi') : '' }}">
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
                                    @if((Input::old('personel'))==$personel->id)
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
                                <option value="0" {{Input::old('durum') === "0" ? "selected" : ""}}>Bekliyor</option>
                                <option value="1" {{Input::old('durum') === "1" ? "selected" : ""}}>Tamamlandı</option>
                                <option value="2" {{Input::old('durum') === "2" ? "selected" : ""}}>Şebeke Yok</option>
                                <option value="3" {{Input::old('durum') === "3" ? "selected" : ""}}>Tadilatlı Yer</option>
                                <option value="4" {{Input::old('durum') === "4" ? "selected" : ""}}>Beklemede</option>
                                <option value="5" {{Input::old('durum') === "5" ? "selected" : ""}}>Geçici Sayaç</option>
                                <option value="6" {{Input::old('durum') === "6" ? "selected" : ""}}>Büroda</option>
                                <option value="7" {{Input::old('durum') === "7" ? "selected" : ""}}>Kurumlar</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Servis Son Durumu:</label>
                        <div class="col-xs-8">
                            <input type="text" id="servisnot" name="servisnot" value="{{ Input::old('servisnot') }}" data-required="1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12 sokulmedurumu ">
                        <label class="control-label col-xs-4">Sökülme Durumu:</label>
                        <label class="" style="padding-top:7px;padding-left: 20px">
                            <input type="checkbox" id="sokulmedurumu" name="sokulmedurumu" {{Input::old('sokulmedurumu') ? "checked" : ""}}/><label>Söküldü</label>
                        </label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Servis Sayacı:</label>
                        <label class="" style="padding-top:7px;padding-left: 10px">
                            <input type="checkbox" id="servissayaci" name="servissayaci" {{Input::old('servissayaci') ? "checked" : ""}}/><label>Takıldı</label>
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
                        <div id="ekstralar" class="hide ekstralar">
                            @if(Input::old('ekstra'))
                                @foreach(Input::old('ekstra') as $ekstra)
                                    {{$ekstra}}
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-12 servissayaci hide">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">Takılma Tarihi:<span class="required" aria-required="true"> * </span></label>
                        <div class="input-icon right col-xs-8">
                            <i class="fa"></i><div class="input-group input-medium date date-picker takilmatarihi" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                <input type="text" id="takilmatarihi" name="takilmatarihi" class="form-control" value="{{Input::old('takilmatarihi') ? Input::old('takilmatarihi') : '' }}">
                                <span class="input-group-btn"><button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-xs-4">İlk Endeksi:</label>
                        <div class="col-xs-8">
                            <input type="tel" id="ilkendeks" name="ilkendeks" value="{{ Input::old('ilkendeksi') ? Input::old('ilkendeksi') : 0.000}} " maxlength="14" data-required="1" class="form-control">
                            <input type="text" id="ilkendeksi"  name="ilkendeksi" value="{{Input::old('ilkendeksi') ? Input::old('ilkendeksi') : 0.000}}" class="form-control hide">
                        </div>
                    </div>
                    <div class="form-group servissayacison col-sm-6 col-xs-12 hide">
                        <label class="control-label col-xs-4">Sayaç Borcu:</label>
                        <div class="col-xs-8">
                            <input type="tel" id="sayacborcu" name="sayacborcu" value="{{ Input::old('sayacborc') ? Input::old('sayacborc') : 0.000}}" maxlength="14" data-required="1" class="form-control">
                            <input type="text" id="sayacborc"  name="sayacborc" value="{{Input::old('sayacborc') ? Input::old('sayacborc') : 0.000}}" class="form-control hide">
                        </div>
                    </div>
                    <div class="form-group servissayacison col-sm-6 col-xs-12 hide">
                        <label class="control-label col-xs-4">Son Endeksi:</label>
                        <div class="col-xs-8">
                            <input type="tel" id="sonendeks" name="sonendeks" value="{{ Input::old('sonendeksi') ? Input::old('sonendeksi') : 0.000 }}" maxlength="14" data-required="1" class="form-control">
                            <input type="text" id="sonendeksi"  name="sonendeksi" value="{{Input::old('sonendeksi') ? Input::old('sonendeksi') : 0.000}}" class="form-control hide">
                        </div>
                    </div>
                    <div class="form-group servissayacison col-sm-6 col-xs-12 hide">
                        <label class="control-label col-xs-4">Toplam Fark:</label>
                        <label class="col-xs-8 toplamfark" style="padding-top: 7px"></label>
                    </div>
                </div>
                <div class="form-group col-xs-12 bilgilendirme hide">
                    <h4 style="padding-left: 10px">Sms Bilgilendirme <label><input type="checkbox" id="smsvar" name="smsvar" checked/> Sms Gönder </label></h4>
                    <div class="form-group col-xs-12">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="control-label col-xs-4">İlgili Telefonu:<span class="required" aria-required="true"> * </span></label>
                            <div class="input-icon right col-xs-8">
                                <i class="fa"></i><input type="tel" id="ilgilitel" name="ilgilitel" value="{{ Input::old('ilgilitel') }}" maxlength="17" autoComplete="off" data-required="1" class="form-control">
                                <input type="text" id="ilgilitelefonu"  name="ilgilitelefonu" value="{{Input::old('ilgilitelefonu')}}" class="form-control hide">
                            </div>
                        </div>
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="control-label col-xs-4">Sms Durumu:</label>
                            <label class="col-xs-8 smsdurum" style="padding-top: 7px"></label>
                        </div>
                    </div>
                    <div class="hide"></div>
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
@stop
