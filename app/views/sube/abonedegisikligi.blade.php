@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Abone Kayıt <small>Değişikliği Ekranı</small></h1>
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
<script src="{{ URL::to('assets/global/plugins/jquery.quicksearch.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>

@stop

@section('page-script')
<script src="{{ URL::to('pages/sube/form-validation-13.js') }}"></script>
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
        "aoColumns": [{"sClass":"id"},null,null],
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
        var capdurum = $('.sayacadi').find("option:selected").data('id');
        if (capdurum === 0) //cap kontrol edilmiyor
        {
            $(".sayaccap").prop("disabled", true);
        } else {
            $(".sayaccap").prop("disabled", false);
        }
        $('#uretimyer').on('change',function(){
            var id=$(this).val();
            if(id!==""){
                $('.tuslar').removeClass('hide');
                $(".kaydet").prop('disabled',false);
            }else{
                $('.tuslar').addClass('hide');
                $(".kaydet").prop('disabled',true);
            }
        });
        $('.sayactur').on('change', function () {
            var sayactur = $(this).val();
            var subekodu = $('#subekodu').val();
            if(sayactur!=="") {
                $.blockUI();
                $.getJSON("{{ URL::to('sube/subesayacadlari') }}",{sayacturid:sayactur,subekodu:subekodu}, function (event) {
                    if (event.durum) // sayac adları varsa
                    {
                        var sayacadlari = event.sayacadlari;
                        var sayaccaplari = event.sayaccaplari;
                        var capdurum = event.capdurum;
                        if (capdurum === 0) { //sayaccap gözükmeyecek
                            $("#sayaccap").select2("val", 1).valid();
                            $("#sayaccaplari").val(1);
                            $(".sayaccap").prop("disabled", true);
                        } else {
                            $("#sayaccap").select2("val", "").valid();
                            $("#sayaccaplari").val("");
                            $(".sayaccap").prop("disabled", false);
                        }
                        $("#sayacadi").empty();
                        $("#sayacadi").append('<option value="">Seçiniz...</option>');
                        $.each(sayacadlari, function (index) {
                            $("#sayacadi").append('<option data-id="' + sayacadlari[index].cap + '" value="' + sayacadlari[index].id + '"> ' + sayacadlari[index].sayacadi + '</option>');
                        });
                        $("#sayacadi").select2("val", "").valid();
                        $("#sayaccap").empty();
                        $("#sayaccap").append('<option value="">Seçiniz...</option>');
                        $.each(sayaccaplari, function (index) {
                            $("#sayaccap").append('<option value="' + sayaccaplari[index].id + '"> ' + sayaccaplari[index].capadi + '</option>');
                        });
                        $("#sayaccap").select2("val", "").valid();
                        $("#sayaccaplari").val("");
                    } else { //bulunamadı hatasını ekrana bas
                        $("#sayacadi").empty();
                        $("#sayaccap").empty();
                        $("#sayacadi").append('<option value="">Seçiniz...</option>');
                        $("#sayaccap").append('<option value="">Seçiniz...</option>');
                        $("#sayacadi").select2("val", "").valid();
                        $("#sayaccap").select2("val", "").valid();
                        $("#sayaccaplari").val("");
                        $(".sayaccap").prop("disabled", false);
                        toastr['' + event.type + '']('' + event.text + '', '' + event.title + '');
                    }
                    $.unblockUI();
                });
            }else{
                $("#sayacadi").empty();
                $("#sayaccap").empty();
                $("#sayacadi").append('<option value="">Seçiniz...</option>');
                $("#sayaccap").append('<option value="">Seçiniz...</option>');
                $("#sayacadi").select2("val", "").valid();
                $("#sayaccap").select2("val", "").valid();
                $("#sayaccaplari").val("");
                $(".sayaccap").prop("disabled", false);
            }
            $(this).valid();
        });
        $('.sayacadi').on('change', function () {
            var id = $(this).val();
            if(id!==""){
                var capdurum = $(this).find("option:selected").data('id');
                if (capdurum === 0) //cap kontrol edilmiyor
                {
                    $("#sayaccap").select2("val",1).valid();
                    $("#sayaccaplari").val(1);
                    $(".sayaccap").prop("disabled", true);
                } else {
                    $("#sayaccap").select2("val","").valid();
                    $("#sayaccaplari").val("");
                    $(".sayaccap").prop("disabled", false);
                }
            }else{
                $("#sayaccap").select2("val","").valid();
                $("#sayaccaplari").val("");
                $(".sayaccap").prop("disabled", false);
            }
            $(this).valid();
        });
        $('.sayaccap').on('change', function (){
            var id = $(this).val();
            $("#sayaccaplari").val(id);
            $(this).valid();
        });
        $(".serino").inputmask("mask", {
            mask:"9",repeat:15,greedy:!1
        });
        $('#il').on('change', function (){
            var faturail = $(this).val();
            if(faturail!==""){
                $.blockUI();
                $.getJSON("{{ URL::to('sube/faturailceler') }}",{id:faturail},function(event){
                    if(event.durum){
                        var ilceler=event.ilceler;
                        $("#ilce").empty();
                        if(ilceler.length>0){
                            $("#ilce").append('<option value="">Seçiniz...</option>');
                            $.each(ilceler, function (index) {
                                $("#ilce").append('<option value="' + ilceler[index].id + '">' + ilceler[index].adi+'</option>');
                            });
                            $("#ilce").select2("val","");
                        }
                    }else{
                        toastr['' + event.type + '']('' + event.text + '', '' + event.title + '');
                    }
                    $.unblockUI();
                });
            }
        });
        $('#formsubmit').click(function () {
            $('#form_sample').submit();
        });
        $('#kayittipi').on('change', function () {
            var id = $(this).val();
            if (id === "1") {
                $('.olanabone').removeClass('hide');
                $('.yeniabone').addClass('hide');
                $('select.valid').each(function(){
                    $(this).rules('remove');
                });
                $('input.valid').each(function(){
                    $(this).rules('remove');
                });

            }else if(id === "2"){
               $('.olanabone').addClass('hide');
               $('.yeniabone').removeClass('hide');
                $('select.valid').each(function(){
                    $(this).rules('remove');
                    $(this).rules('add', {
                        required: true
                    });
                });
                $('input.valid').each(function(){
                    $(this).rules('remove');
                    $(this).rules('add', {
                        required: true
                    });
                });
            }else{
                $('.olanabone').addClass('hide');
                $('.yeniabone').addClass('hide');
            }
        });
        $('.olanabonebul').click(function(){
            var subekodu = $('#subekodu').val();
            var adisoyadi = $('#olanadisoyadi').val();
            if(adisoyadi!==""){
                $.blockUI();
                $.getJSON("{{ URL::to('sube/abonesorgula') }}",{adisoyadi:adisoyadi,subekodu:subekodu}, function (event) {
                    if (event.durum)
                    {
                        if(event.adet>1){
                            var aboneler = event.aboneler;
                            oTable.clear().draw();
                            $.each(aboneler, function (index) {
                                oTable.row.add([aboneler[index].id,aboneler[index].adisoyadi,((aboneler[index].faturaadresi)==null ? '' : aboneler[index].faturaadresi)])
                                .draw();
                            });
                            $('#abonelistesi').modal('show');
                        }else{
                            var abone = event.abone;
                            var newRow="";
                            newRow+= '<div class="form-group">' +
                                '<div class="form-group col-xs-12">'+
                                '<label class="control-label col-sm-2 col-xs-4">Cari İsim:</label>'+
                                '<label class="col-xs-8" style="padding-top: 9px">'+abone.netsiscari.carikod+' - '+abone.netsiscari.cariadi+'</label>'+
                                '</div>'+
                                '<div class="form-group col-sm-6 col-xs-12">'+
                                '<label class="control-label col-sm-4 col-xs-3">Adı Soyadı:</label>'+
                                '<label class="col-xs-8" style="padding-top: 9px">'+abone.adisoyadi+'</label>'+
                                '</div>'+
                                '<div class="form-group col-sm-6 col-xs-12">'+
                                '<label class="control-label col-sm-4 col-xs-3">Kayıt Yeri:</label>'+
                                '<label class="col-xs-8" style="padding-top: 9px">'+abone.uretimyer.yeradi+'</label>'+
                                '</div>'+
                                '<div class="form-group col-sm-6 col-xs-12">'+
                                '<label class="control-label col-sm-4 col-xs-3">Vergi Dairesi:</label>'+
                                '<label class="col-xs-8" style="padding-top: 9px">'+abone.vergidairesi+'</label>'+
                                '</div>'+
                                '<div class="form-group col-sm-6 col-xs-12">' +
                                '<label class="control-label col-sm-4 col-xs-3">TC Kimlik / Vergi No:</label>'+
                                '<label class="col-xs-8" style="padding-top: 9px">'+abone.tckimlikno+'</label>'+
                                '</div>'+
                                '<div class="form-group col-sm-6 col-xs-12">'+
                                '<label class="control-label col-sm-4 col-xs-3">Abone No:</label>'+
                                '<label class="col-xs-8" style="padding-top: 9px">'+abone.abone_no+'</label>'+
                                '</div>'+
                                '<div class="form-group col-sm-6 col-xs-12">'+
                                '<label class="control-label col-sm-4 col-xs-3">Telefonu:</label>'+
                                '<label class="col-xs-8" style="padding-top: 9px">'+abone.telefon+'</label>'+
                                '</div>'+
                                '<div class="form-group col-sm-6 col-xs-12">'+
                                '<label class="control-label col-sm-4 col-xs-3">Fatura Adresi:</label>'+
                                '<label class="col-xs-8" style="padding-top: 9px">'+abone.faturaadresi+'</label>'+
                                '</div>'+
                                '<div class="form-group col-sm-6 col-xs-12">'+
                                '<label class="control-label col-sm-4 col-xs-3">Email Adresi:</label>'+
                                '<label class="col-xs-8" style="padding-top: 9px">'+abone.email+'</label>'+
                                '</div>'+
                                '<div class="form-group col-sm-6 col-xs-12">'+
                                '<label class="control-label col-sm-4 col-xs-3">Fatura İl:</label>'+
                                '<label class="col-xs-8" style="padding-top: 9px">'+abone.il.adi+'</label>'+
                                '</div>'+
                                '<div class="form-group col-sm-6 col-xs-12">'+
                                '<label class="control-label col-sm-4 col-xs-3">Fatura İlçe:</label>'+
                                '<label class="col-xs-8" style="padding-top: 9px">'+abone.ilce.adi+'</label>'+
                                '</div>';
                            $('.olanabonebilgi').append(newRow);
                            $('#secilenabone').val(abone.id);
                        }
                    } else {
                        $('#secilenabone').val('');
                        $('.olanabonebilgi').html('');
                        toastr[event.type](event.text,event.title);
                    }
                    $.unblockUI();
                });
            }
        });
        $('#listesec').click(function () {
            var aboneid = $("#secilenabone").val();
            if ( aboneid !== "") {
                $.blockUI();
                $.getJSON("{{ URL::to('sube/abonesorgula') }}", {aboneid: aboneid}, function (event) {
                    if (event.durum) {
                        var abone = event.abone;
                        var newRow="";
                        newRow+= '<div class="form-group">' +
                            '<div class="form-group col-xs-12">'+
                            '<label class="control-label col-sm-2 col-xs-4">Cari İsim:</label>'+
                            '<label class="col-xs-8" style="padding-top: 9px">'+abone.netsiscari.carikod+' - '+abone.netsiscari.cariadi+'</label>'+
                            '</div>'+
                            '<div class="form-group col-sm-6 col-xs-12">'+
                            '<label class="control-label col-sm-4 col-xs-3">Adı Soyadı:</label>'+
                            '<label class="col-xs-8" style="padding-top: 9px">'+abone.adisoyadi+'</label>'+
                            '</div>'+
                            '<div class="form-group col-sm-6 col-xs-12">'+
                            '<label class="control-label col-sm-4 col-xs-3">Kayıt Yeri:</label>'+
                            '<label class="col-xs-8" style="padding-top: 9px">'+abone.uretimyer.yeradi+'</label>'+
                            '</div>'+
                            '<div class="form-group col-sm-6 col-xs-12">'+
                            '<label class="control-label col-sm-4 col-xs-3">Vergi Dairesi:</label>'+
                            '<label class="col-xs-8" style="padding-top: 9px">'+abone.vergidairesi+'</label>'+
                            '</div>'+
                            '<div class="form-group col-sm-6 col-xs-12">' +
                            '<label class="control-label col-sm-4 col-xs-3">TC Kimlik / Vergi No:</label>'+
                            '<label class="col-xs-8" style="padding-top: 9px">'+abone.tckimlikno+'</label>'+
                            '</div>'+
                            '<div class="form-group col-sm-6 col-xs-12">'+
                            '<label class="control-label col-sm-4 col-xs-3">Abone No:</label>'+
                            '<label class="col-xs-8" style="padding-top: 9px">'+abone.abone_no+'</label>'+
                            '</div>'+
                            '<div class="form-group col-sm-6 col-xs-12">'+
                            '<label class="control-label col-sm-4 col-xs-3">Telefonu:</label>'+
                            '<label class="col-xs-8" style="padding-top: 9px">'+abone.telefon+'</label>'+
                            '</div>'+
                            '<div class="form-group col-sm-6 col-xs-12">'+
                            '<label class="control-label col-sm-4 col-xs-3">Fatura Adresi:</label>'+
                            '<label class="col-xs-8" style="padding-top: 9px">'+abone.faturaadresi+'</label>'+
                            '</div>'+
                            '<div class="form-group col-sm-6 col-xs-12">'+
                            '<label class="control-label col-sm-4 col-xs-3">Email Adresi:</label>'+
                            '<label class="col-xs-8" style="padding-top: 9px">'+abone.email+'</label>'+
                            '</div>'+
                            '<div class="form-group col-sm-6 col-xs-12">'+
                            '<label class="control-label col-sm-4 col-xs-3">Fatura İl:</label>'+
                            '<label class="col-xs-8" style="padding-top: 9px">'+abone.il.adi+'</label>'+
                            '</div>' +
                            '<div class="form-group col-sm-6 col-xs-12">'+
                            '<label class="control-label col-sm-4 col-xs-3">Fatura İlçe:</label>'+
                            '<label class="col-xs-8" style="padding-top: 9px">'+abone.ilce.adi+'</label>'+
                            '</div>';
                        $('.olanabonebilgi').append(newRow);
                    } else {
                        $('#secilenabone').val('');
                        $('.olanabonebilgi').html('');
                        toastr[event.type](event.text,event.title);
                    }
                    $.unblockUI();
                    $('#abonelistesi').modal('hide');
                });
            } else {
                $('#secilenabone').val('');
                toastr['warning']('Abone Seçilmemiş', 'Abone Seçim Hatası');
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
            <i class="fa fa-pencil"></i>Abone Değişikliği
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('sube/abonedegistir') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <input type="text" id="eskiabone" name="eskiabone" value="{{ $abone->id }}" data-required="1" class="form-control">
                    <input type="text" id="eskitahsis" name="eskitahsis" value="{{ $abonetahsis->id }}" data-required="1" class="form-control">
                    <input type="text" id="subekodu" name="subekodu" value="{{ $sube ? $sube->subekodu : 1 }}" data-required="1" class="form-control">
                    <input type="text" id="netsisdepo" name="netsisdepo" value="{{ $sube ? $sube->netsisdepolar_id : 1 }}" data-required="1" class="form-control">
                    <input type="text" id="netsiscari" name="netsiscari" value="{{ $sube ? $sube->netsiscari_id : 2631 }}" data-required="1" class="form-control">
                </div>
                <div class="panel-group accordion eskiabone col-xs-12" id="accordion1">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_1">Eski Abone Bilgileri </a>
                            </h4>
                        </div>
                        <div id="collapse_1" class="panel-collapse in">
                            <div class="panel-body">
                                <div class="form-group col-xs-12">
                                    <label class="control-label col-sm-2 col-xs-4">Cari İsim:</label>
                                    <label class="col-xs-8" style="padding-top: 9px">{{$abone->netsiscari->carikod.' - '.$abone->netsiscari->cariadi}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-sm-4 col-xs-3">Adı Soyadı:</label>
                                    <label class="col-xs-8" style="padding-top: 9px">{{$abone->adisoyadi}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-sm-4 col-xs-3">Kayıt Yeri:</label>
                                    <label class="col-xs-8" style="padding-top: 9px">{{$abone->uretimyer->yeradi}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-sm-4 col-xs-3">Vergi Dairesi:</label>
                                    <label class="col-xs-8" style="padding-top: 9px">{{$abone->vergidairesi}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-sm-4 col-xs-3">TC Kimlik / Vergi No:</label>
                                    <label class="col-xs-8" style="padding-top: 9px">{{$abone->tckimlikno}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-sm-4 col-xs-3">Abone No:</label>
                                    <label class="col-xs-8" style="padding-top: 9px">{{$abone->abone_no}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-sm-4 col-xs-3">Telefonu:</label>
                                    <label class="col-xs-8" style="padding-top: 9px">{{$abone->telefon}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-sm-4 col-xs-3">Fatura Adresi:</label>
                                    <label class="col-xs-8" style="padding-top: 9px">{{$abone->faturaadresi}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-sm-4 col-xs-3">Email Adresi:</label>
                                    <label class="col-xs-8" style="padding-top: 9px">{{$abone->email}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-sm-4 col-xs-3">Fatura İl:</label>
                                    <label class="col-xs-8" style="padding-top: 9px">{{$abone->il->adi}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-sm-4 col-xs-3">Fatura İlçe:</label>
                                    <label class="col-xs-8" style="padding-top: 9px">{{$abone->ilce->adi}}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <h4>Yeni Abone Bilgileri</h4>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Kayıt Tipi:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="kayittipi" name="kayittipi" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            <option value="1">Olan Abone Üzerine</option>
                            <option value="2">Yeni Aboneye</option>
                        </select>
                    </div>
                </div>
                <div class="panel-group accordion hide olanabone col-xs-12" id="accordion2">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion2" href="#collapse_2">Olan Abone Bilgileri </a>
                            </h4>
                        </div>
                        <div id="collapse_2" class="panel-collapse in">
                            <div class="panel-body">
                                <div class="form-group">
                                    <input type="text" id="secilenabone" name="secilenabone" data-required="1" class="form-control hide">
                                    <label class="control-label col-sm-2 col-xs-4">Adı Soyadı:<span class="required" aria-required="true"> * </span></label>
                                    <div class="col-xs-6">
                                        <input type="text" id="olanadisoyadi" name="olanadisoyadi" value="{{ Input::old('olanadisoyadi')}}" data-required="1" class="form-control">
                                    </div>
                                    <label class="col-xs-2"><a class="btn green olanabonebul">Bul</a></label>

                                </div>
                                <div class="form-group olanabonebilgi">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-group accordion hide yeniabone col-xs-12" id="accordion3">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3">Yeni Abone Bilgileri </a>
                            </h4>
                        </div>
                        <div id="collapse_3" class="panel-collapse in">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="control-label col-sm-2 col-xs-4">Cari İsim:<span class="required" aria-required="true"> * </span></label>
                                    <div class="input-icon right col-xs-8">
                                        <i class="fa"></i><select class="form-control select2me select2-offscreen valid" id="cariadi" name="cariadi" tabindex="-1" title="">
                                            <option value="">Seçiniz...</option>
                                            @if(Input::old('cariadi')==$sube->netsiscari_id )
                                                <option value="{{ $sube->netsiscari_id }}" selected>{{ $sube->netsiscari->carikod.' - '.$sube->netsiscari->cariadi }}</option>
                                            @else
                                                <option value="{{ $sube->netsiscari_id }}">{{ $sube->netsiscari->carikod.' - '. $sube->netsiscari->cariadi }}</option>
                                            @endif
                                            @foreach($netsiscariler as $netsiscari)
                                                @if(Input::old('cariadi')==$netsiscari->id )
                                                    <option value="{{ $netsiscari->id }}" selected>{{ $netsiscari->carikod.' - '.$netsiscari->cariadi }}</option>
                                                @else
                                                    <option value="{{ $netsiscari->id }}">{{ $netsiscari->carikod.' - '.$netsiscari->cariadi }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-sm-4 col-xs-3">Adı Soyadı: <span class="required" aria-required="true"> * </span></label>
                                    <div class="input-icon right col-sm-8 col-xs-9">
                                        <i class="fa"></i><input type="text" id="adisoyadi" name="adisoyadi" value="{{ Input::old('adisoyadi') }}" maxlength="200" data-required="1" class="form-control valid">
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-sm-4 col-xs-3">Kayıt Yeri: <span class="required" aria-required="true"> * </span></label>
                                    <div class="input-icon right col-sm-8 col-xs-9">
                                        <i class="fa"></i><select class="form-control select2me select2-offscreen valid" id="uretimyer" name="uretimyer" tabindex="-1" title="">
                                            <option value="">Seçiniz...</option>
                                            @foreach($uretimyerleri as $uretimyeri)
                                                @if(Input::old('uretimyer')==$uretimyeri->id)
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
                                        <input type="text" id="vergidairesi" name="vergidairesi" value="{{ Input::old('vergidairesi') }}" maxlength="30" data-required="1" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-sm-4 col-xs-3">TC Kimlik / Vergi No:<span class="required" aria-required="true"> * </span></label>
                                    <div class="input-icon right col-sm-8 col-xs-9">
                                        <i class="fa"></i><input type="text" id="tckimlikno" name="tckimlikno" value="{{ Input::old('tckimlikno') }}" maxlength="11" data-required="1" class="form-control valid">
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-sm-4 col-xs-3">Abone No:</label>
                                    <div class="col-sm-8 col-xs-9">
                                        <input type="text" id="aboneno" name="aboneno" value="{{ Input::old('aboneno') }}" maxlength="10" data-required="1" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-sm-4 col-xs-3">Telefonu:<span class="required" aria-required="true"> * </span></label>
                                    <div class="input-icon right col-sm-8 col-xs-9">
                                        <i class="fa"></i><input type="tel" id="telefon" name="telefon" value="{{ Input::old('telefon') }}" maxlength="17" data-required="1" class="form-control valid">
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-sm-4 col-xs-3">Fatura Adresi:<span class="required" aria-required="true"> * </span></label>
                                    <div class="input-icon right col-sm-8 col-xs-9">
                                        <i class="fa"></i><input type="text" id="adres" name="adres" value="{{ Input::old('adres') }}" maxlength="100" data-required="1" class="form-control valid">
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-sm-4 col-xs-3">Email Adresi:</label>
                                    <div class="col-sm-8 col-xs-9">
                                        <input type="text" id="email" name="email" value="{{ Input::old('email') }}" maxlength="100" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">İl:<span class="required" aria-required="true"> * </span></label>
                                    <div class="input-icon right col-xs-8">
                                        <i class="fa"></i><select class="form-control select2me select2-offscreen valid" id="il" name="il" tabindex="-1" title="">
                                            <option value="">Seçiniz...</option>
                                            @foreach($iller as $il)
                                                @if(Input::old('il')==$il->id )
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
                                        <i class="fa"></i><select class="form-control select2me select2-offscreen valid" id="ilce" name="ilce" tabindex="-1" title="">
                                            <option value="">Seçiniz...</option>
                                            @foreach($ilceler as $ilce)
                                                @if(Input::old('ilce')==$ilce->id )
                                                    <option value="{{ $ilce->id }}" selected>{{ $ilce->adi }}</option>
                                                @else
                                                    <option value="{{ $ilce->id }}">{{ $ilce->adi }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-group accordion sayaclar col-xs-12" id="accordion4">
                    <div class="panel panel-default sayaclar_ek">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion4" href="#collapse_4">Sayaç Bilgisi {{$abonesayac->serino}} </a>
                            </h4>
                        </div>
                        <div id="collapse_4" class="panel-collapse in">
                            <div class="panel-body">
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="col-xs-4 control-label">Seri No:</label>
                                    <label class="col-xs-8" style="padding-top: 9px">{{$abonesayac->serino}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Sayaç Türü:</label>
                                    <label class="col-xs-8" style="padding-top: 9px">{{$abonesayac->sayactur->tur}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="col-xs-4 control-label">Sayaç Adı:</label>
                                    <label class="col-xs-8" style="padding-top: 9px">{{$abonesayac->sayacadi->sayacadi}}</label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="col-xs-4 control-label">Sayaç Çapı:</label>
                                    <label class="col-xs-8" style="padding-top: 9px">{{$abonesayac->sayaccap->capadi}}</label>
                                </div>
                                <div class="form-group col-xs-12">
                                    <label class="col-sm-2 col-xs-4 control-label">Montaj Adresi:<span class="required" aria-required="true"> * </span></label>
                                    <div class="input-icon right col-sm-10 col-xs-8">
                                        <i class="fa"></i><input type="text" id="sayacadresi" name="sayacadresi" class="form-control  sayacadresi" value="{{Input::old('sayacadresi') ? Input::old('sayacadresi') : $abonesayac->adres}}" >
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="col-xs-4 control-label">Bilgi:</label>
                                    <div class="col-xs-8">
                                        <input type="text" id="sayacbilgi" name="sayacbilgi" class="form-control sayacbilgi" value="{{Input::old('sayacbilgi') ? Input::old('sayacbilgi') : $abonesayac->bilgi}}" >
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="col-xs-4 control-label">İletişim:</label>
                                    <div class="col-xs-8">
                                        <input type="text" id="sayaciletisim" name="sayaciletisim" class="form-control sayaciletisim" value="{{Input::old('sayaciletisim') ? Input::old('sayaciletisim') : $abonesayac->iletisim}}" >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <button type="button" class="btn green kaydet" data-toggle="modal" data-target="#confirm">Değiştir</button>
                        <a href="{{ URL::to('sube/abonekayit')}}" class="btn default">Vazgeç</a>
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
                    <h4 class="modal-title">Abone Bilgisi Değiştirilecek</h4>
                </div>
                <div class="modal-body">
                    Girilen Abone Bilgisi Güncellenecektir?
                </div>
                <div class="modal-footer">
                    <a id="formsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="abonelistesi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Abone Listesi
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Abone Listesi</h3>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_1">
                                                <thead>
                                                <tr>
                                                    <th class="">#</th>
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

