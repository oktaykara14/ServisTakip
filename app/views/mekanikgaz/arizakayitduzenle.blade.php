@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Mekanik Gaz Arıza Kayıt <small>Düzenleme Ekranı</small></h1>
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
<script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>

@stop

@section('page-script')
<script src="{{ URL::to('pages/mekanikgaz/form-validation-4.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
   FormValidationGazServis.init();
});
</script>
<script>
var table = $('#sample_editable_1');
var oTable = table.DataTable({
    "sPaginationType": "simple_numbers",
    "searching": false,
    "ordering": false,
    "bProcessing": false,
    "sAjaxSource": "",
    "bServerSide": false,
    "fnDrawCallback" : function() {
        $('.goster').click(function(){
            var depoteslimid = $(this).data('id');
            var sayacid = $('#sayacid').val();
            if(sayacid!=="") {
                $.blockUI();
                $.getJSON("{{ URL::to('mekanikgaz/gelisbilgidetay') }}",{sayacid:sayacid,depoteslimid:depoteslimid}, function (event) {
                    if(event.durum){
                        var arizakayit = event.arizakayit;
                        $('.eskiserino').html(arizakayit.sayacgelen.serino);
                        $('.eskigelis').html(arizakayit.depotarihi);
                        $('.eskicari').html(arizakayit.netsiscari.cariadi);
                        $('.eskiistek').html(arizakayit.istek.stokadi);
                        $('.eskiyer').html(arizakayit.uretimyer.yeradi);
                        $('.eskiuretim').html(arizakayit.uretimtarihi);
                        $('.eskisayacadi').html(arizakayit.sayacadi.sayacadi);
                        $('.eskigaranti').html(arizakayit.garantidurum);
                        $('.eskiaksesuar').html(arizakayit.aksesuarlar);
                        $('.eskimekanik').html(arizakayit.ilkmekanik);
                        $('.eskibaglanticap').html(arizakayit.baglanticap);
                        $('.eskipmax').html(arizakayit.pmax);
                        $('.eskiqmax').html(arizakayit.qmax);
                        $('.eskiqmin').html(arizakayit.qmin);
                        $('.eskiaciklama').html(arizakayit.musteriaciklama);
                        $('.eskiariza').html(arizakayit.problemler);
                        $('.eskiyapilan').html(arizakayit.yapilanlar);
                        $('.eskidegisen').html(arizakayit.degisenler);
                        $('.eskiuyari').html(arizakayit.uyarilar);
                        $('.eskikullanici').html(arizakayit.kullanici.adi_soyadi);
                        var root = event.root;
                        var eskiresimler = arizakayit.resimler;
                        var resimler = '';
                        var resim = "";
                        if (eskiresimler !== "") {
                            eskiresimler = eskiresimler.split(',');
                            $.each(eskiresimler, function (index) {
                                resim = "<div class='col-md-10'><span>- " + eskiresimler[index] + "</span><a href='" + root + "assets/arizaresim/" + eskiresimler[index] + "' type='button' class='btn btn-warning' target='_blank'>Göster</a></div>";
                                resimler += resim;
                            });
                        }
                        $('.eskiresimler').append(resimler);
                        $('.eskinot').html(arizakayit.arizanot);
                        if (arizakayit.sayactip.tipadi === "ROTARY" || arizakayit.sayactip.tipadi === "TURBIN") {
                            $('.eskisertifikaek').removeClass('hide');
                            $('.eskisertifika').html(arizakayit.sertifika);
                            $('.eskihf2').html(arizakayit.hf2);
                            $('.eskihf3').html(arizakayit.hf3);
                            $('.eskihf32').html(arizakayit.hf32);
                        } else {
                            $('.eskisertifikaek').addClass('hide');
                        }
                    }else{
                        $('#detaygoster').modal('hide');
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            }else{
                toastr['warning']('Sayaç Bilgisi Getirilemedi', 'Bilgi Getirme Hatası');
                $.unblockUI();
            }
        });
    },
    "bInfo": false,
    "bPaginate": false,
    "aaSorting": [],
    "columnDefs": [{ "targets": 0, "orderable": false }],
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
    "aoColumns": [null,null,null,null,null],
    "lengthMenu": [
        [5],
        [5]
    ]
});
var tableWrapper = jQuery('#sample_editable_1_wrapper');
tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
</script>
<script>
    $(document).ready(function() {
        var degisenler;
        $('.gelisbilgigetir').click(function () {
            var sayacid = $('#sayacid').val();
            var cariid = $("#cariid").val();
            if (sayacid !== "") {
                $.blockUI();
                $.getJSON("{{ URL::to('mekanikgaz/gelisbilgi') }}",{sayacid:sayacid,cariid:cariid}, function (event) {
                    if(event.durum){
                        oTable.clear().draw();
                        var depoteslim = event.depoteslim;
                        $.each(depoteslim, function (index) {
                            var link = '<a class="btn btn-sm btn-warning goster" href="#detaygoster" data-toggle="modal" data-id="' + depoteslim[index].id + '"> Detay </a>';
                            oTable.row.add([depoteslim[index].id, $.datepicker.formatDate('dd-mm-yy', new Date(depoteslim[index].gelistarihi)),$.datepicker.formatDate('dd-mm-yy', new Date(depoteslim[index].kayittarihi)),$.datepicker.formatDate('dd-mm-yy', new Date(depoteslim[index].teslimtarihi)),
                                link]).draw();
                        });
                    }else{
                        $('#bilgigetir').modal('hide');
                        toastr[event.type](event.text,event.title);
                    }
                    $.unblockUI();
                });
            } else {
                toastr['warning']('Sayaç Bilgisi Getirilemedi', 'Bilgi Getirme Hatası');
            }
        });
        $('#istek').on('change', function () {
            $(this).valid();
        });
        $('#cariadi').on('change', function () {
            $(this).valid();
        });
        $('#degisenler').on('change', function () {
            $(this).valid();
        });
        $('#yapilanlar').on('change', function () {
            $(this).valid();
        });
        $('#uyarilar').on('change', function () {
            $(this).valid();
        });
        var selected_val = $("#arizalist").val().split(',');
        $("#arizalist").val('');
        for (var q=0; q<selected_val.length; q++) {
            $('#arizalar').multiSelect('select', selected_val[q]);
        }
        selected_val = $("#yapilanlist").val().split(',');
        $("#yapilanlist").val('');
        for (q=0; q<selected_val.length; q++) {
            $('#yapilanlar').multiSelect('select', selected_val[q]);
        }
        selected_val = $("#degisenlist").val().split(',');
        $("#degisenlist").val('');
        for (q=0; q<selected_val.length; q++) {
            $('#degisenler').multiSelect('select', selected_val[q]);
        }
        selected_val = $("#uyarilist").val().split(',');
        $("#uyarilist").val('');
        for (q=0; q<selected_val.length; q++) {
            $('#uyarilar').multiSelect('select', selected_val[q]);
        }
        $('#arizalar').on('change', function () {
            var secilenler = $(this).val();
            var garanti = $('#garanti').val();
            var durum = -1;
            if (secilenler === null) {
                $('#garanti').select2('val', garanti);
            } else {
                $.each(secilenler, function (index) {
                    var garantidurum = $('#arizalar option[value="' + secilenler[index] + '"]').data('id');
                    if (garantidurum === 0) //garanti dışıysa
                    {
                        durum = 0;
                    } else if (garantidurum === 1 && durum !== 0) {
                        durum = 1;
                    }
                });
                if (durum === -1) {
                    $('#garanti').select2('val', garanti);
                } else {
                    $('#garanti').select2('val', durum);
                }
            }
            $(this).valid();
        });
        $('#sayacadi').on('change', function () {
            var id = $(this).val();
            if (id !== "") {
                $.blockUI();
                $.getJSON("{{ URL::to('mekanikgaz/sayacparca') }}",{sayacadiid:id}, function (event) {
                    if (event.durum) //sayaç parçalarını listede göster
                    {
                        var parcalar = event.sayacparcalar.parca;
                        var sayactip = event.sayactip;
                        $("#degisenler").empty();
                        $.each(parcalar, function (index) {
                            if (parcalar[index].sabit === '1') {
                                $("#degisenler").append('<option value="' + parcalar[index].id + '" selected> ' + parcalar[index].tanim + '</option>');
                            } else {
                                $("#degisenler").append('<option value="' + parcalar[index].id + '"> ' + parcalar[index].tanim + '</option>');
                            }
                        });
                        $("#degisenler").multiSelect("refresh");
                        degisenler = $("#degisenler").val();
                        $("#degisenlist").val(degisenler);
                        if(sayactip.tipadi==="ROTARY" || sayactip.tipadi==="TURBIN"){
                            $('.sertifika').removeClass('hide');
                        }else{
                            $('.sertifika').addClass('hide');
                        }
                    } else {
                        $("#degisenler").empty();
                        $("#degisenler").multiSelect("refresh");
                        degisenler = $("#degisenler").val();
                        $("#degisenlist").val(degisenler);
                        toastr[event.type](event.text,event.title);
                    }
                    $("#degisenler").valid();
                    $.unblockUI();
                });
                $.blockUI();
                $.getJSON("{{ URL::to('mekanikgaz/sayacaksesuar') }}",{sayacadiid:id}, function (event) {
                    if (event.durum) //sayaç aksesuarlarını getir
                    {
                        var aksesuarlar = event.aksesuarlar;
                        $("#aksesuar").empty();
                        $.each(aksesuarlar, function (index) {
                            $("#aksesuar").append('<option value="' + aksesuarlar[index].id + '"> ' + aksesuarlar[index].adi + '</option>');
                        });
                        $('#aksesuar').select2({
                            placeholder: "Aksesuar Seçin",
                            allowClear: true
                        });
                    } else {
                        toastr[event.type](event.text,event.title);
                    }
                    $.unblockUI();
                });
            }else{
                $("#degisenler").empty();
                $("#degisenler").multiSelect("refresh");
                degisenler = $("#degisenler").val();
                $("#degisenlist").val(degisenler);
                $("#degisenler").valid();
            }
            $(this).valid();
        });

        $('.arizaekle').click(function () {
            var ariza = $('#arizayeni').val();
            if (ariza === "") {
                toastr['warning']('Arıza Tespiti Boş Geçildi', 'Arıza Ekleme Hatası');
            } else {
                $.blockUI();
                $.getJSON(" {{ URL::to('mekanikgaz/arizaekle') }}", {ariza: ariza}, function (event) {
                    toastr[event.type](event.text, event.title);
                    $('#arizaekle').modal('hide');
                    if (event.durum) {
                        var arizalar = event.arizalar;
                        var secilenler = $("#arizalar").val();
                        $("#arizalar").empty();
                        $.each(arizalar, function (index) {
                            $("#arizalar").append('<option value="' + arizalar[index].id + '"> ' + arizalar[index].tanim + '</option>');
                        });
                        $("#arizalar").val(secilenler);
                        $("#arizalar").multiSelect("refresh");
                        $("#arizalist").val(secilenler);
                    }
                    $.unblockUI();
                });
            }
        });

        $('.yapilanekle').click(function () {
            var yapilan = $('#yapilanyeni').val();
            if (yapilan === "") {
                toastr['warning']('Yapılan İşlem Boş Geçildi', 'Yapılan İşlem Ekleme Hatası');
            } else {
                $.blockUI();
                $.getJSON(" {{ URL::to('mekanikgaz/yapilanekle') }}", {yapilan: yapilan}, function (event) {
                    toastr[event.type](event.text, event.title);
                    $('#yapilanekle').modal('hide');
                    if (event.durum) {
                        var yapilanlar = event.yapilanlar;
                        var secilenler = $("#yapilanlar").val();
                        $("#yapilanlar").empty();
                        $.each(yapilanlar, function (index) {
                            $("#yapilanlar").append('<option value="' + yapilanlar[index].id + '"> ' + yapilanlar[index].tanim + '</option>');
                        });
                        $("#yapilanlar").val(secilenler);
                        $("#yapilanlar").multiSelect("refresh");
                        $("#yapilanlist").val(secilenler);
                    }
                    $.unblockUI();
                });
            }
        });

        $('.uyariekle').click(function () {
            var uyari = $('#uyariyeni').val();
            if (uyari === "") {
                toastr['warning']('Uyarı-Sonuç Boş Geçildi', 'Uyarı-Sonuç Ekleme Hatası');
            } else {
                $.blockUI();
                $.getJSON(" {{ URL::to('mekanikgaz/uyariekle') }}", {uyari: uyari}, function (event) {
                    toastr[event.type](event.text, event.title);
                    $('#uyariekle').modal('hide');
                    if (event.durum) {
                        var uyarilar = event.uyarilar;
                        var secilenler = $("#uyarilar").val();
                        $("#uyarilar").empty();
                        $.each(uyarilar, function (index) {
                            $("#uyarilar").append('<option value="' + uyarilar[index].id + '"> ' + uyarilar[index].tanim + '</option>');
                        });
                        $("#uyarilar").val(secilenler);
                        $("#uyarilar").multiSelect("refresh");
                        $("#uyarilist").val(secilenler);
                    }
                    $.unblockUI();
                });
            }
        });
        $('.hurdanedeniekle').click(function () {
            var hurdaneden = $('#hurdanedenyeni').val();
            if (hurdaneden === "") {
                toastr['warning']('Hurda Nedeni Boş Geçildi', 'Hurda Nedeni Ekleme Hatası');
            } else {
                $.blockUI();
                $.getJSON(" {{ URL::to('mekanikgaz/hurdanedeniekle') }}", {hurdaneden: hurdaneden}, function (event) {
                    toastr[event.type](event.text, event.title);
                    $('#hurdanedeniekle').modal('hide');
                    if (event.durum) {
                        var hurdanedenleri = event.hurdanedenleri;
                        var id = event.id;
                        $("#hurdaneden").empty();
                        $.each(hurdanedenleri, function (index) {
                            $("#hurdaneden").append('<option value="' + hurdanedenleri[index].id + '"> ' + hurdanedenleri[index].nedeni + '</option>');
                        });
                        $("#hurdaneden").select2('val',id);
                    }
                    $.unblockUI();
                });
            }
        });

        $('.resimsil').click(function () {
            var yenilist = "";
            var resim = $(this).data('id');
            $(this).closest('div').remove();
            var resimler = $('#resimler').val();
            resimler = resimler.split(',');
            $.each(resimler, function (index) {
                if (resimler[index] !== resim) {
                    yenilist += (yenilist === "" ? "" : ",") + resimler[index];
                }
            });
            $('#resimler').val(yenilist);
        });
        $('.serinoekle').click(function () {
            var yeniserino = $('#serinoyeni').val();
            var serino = $('#serino').val();
            var uretimyer = $('#uretimyer').val();
            var sayacadi = $('#sayacadi').val();
            var sayaccap = $('#sayaccap').val();
            var sayacgelen = $('#sayacgelenid').val();
            if (yeniserino === "") {
                toastr['warning']('Seri Numarası Boş Geçildi', 'Seri Numarası Ekleme Hatası');
            } else if (yeniserino === serino) {
                toastr['warning']('Eski Seri Numarası ile Aynı Numara Girildi', 'Seri Numarası Ekleme Hatası');
            } else {
                $.blockUI();
                $.getJSON("{{ URL::to('mekanikgaz/serinoekle') }}", {yeniserino: yeniserino,uretimyer:uretimyer,sayactur:5,sayacadi:sayacadi,sayaccap:sayaccap,sayacgelen:sayacgelen}, function (event) {
                    toastr[event.type](event.text, event.title);
                    $('#serinoekle').modal('hide');
                    if (event.durum) {
                        $('.yeniserino').removeClass('hide').html(yeniserino);
                        $('#yeniserino').val(yeniserino);
                        $('.eklebutton').addClass('hide');
                        $('.silbutton').removeClass('hide');
                    }
                    $.unblockUI();
                });
                $('#serinoyeni').val('');
            }
        });

        $('#formserisil').click(function () {
            $('#serinosil').modal('hide');
            $('.yeniserino').addClass('hide').html("");
            $('#yeniserino').val("");
            $('.eklebutton').removeClass('hide');
            $('.silbutton').addClass('hide');
            toastr["success"]("Eklenen Yeni Seri Numarası Başarıyla Silindi", "Silme İşlemi Başarılı");
        });
        $('#formsubmit').click(function () {
            $('#form_sample').submit();
        });
        $('#hurdakaydet').click(function () {
            var neden=$("#hurdaneden").select2('val');
            $("#hurdadurum").val(1);
            $("#hurdanedeni").val(neden);
            $('#form_sample').submit();
        });
        $('#hurdacikarkaydet').click(function () {
            var neden=$("#hurdaneden").select2('val');
            $("#hurdadurum").val(0);
            $("#hurdanedeni").val(neden);
            $('#form_sample').submit();
        });
        $('input[type="checkbox"]').on('click', function() {
            var $box=$(this);
            $("#sayacdurum").val($box.val());
            if($box.parent().hasClass('checked')){
                $('input[name="' + this.name + '"]').parent().removeClass('checked');
                $box.parent().addClass('checked');
            }
        });
        $("select").on("select2-close", function () { $(this).valid(); });
        $('#uretim').on('change', function() { $(this).valid(); });
        $('#form_sample').valid();
    });
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Arıza Kayıdı Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('mekanikgaz/arizakayitduzenle/'.$arizakayit->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate" enctype="multipart/form-data">
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
                    <label class="control-label col-sm-2 col-xs-3">Seri No:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-3 col-xs-9">
                        <i class="fa"></i><input type="text" id="serino" name="serino" value="{{ Input::old('serino') ? Input::old('serino') : ($arizakayit->serinodegisim ? $arizakayit->servistakip->eskiserino : $arizakayit->servistakip->serino)}}" data-required="1" class="form-control" readonly="" tabindex="1">
                    </div>
                    <label class="control-label col-sm-2 col-xs-3">Yeni Seri Numarası:</label>
                    <label class="col-sm-2 col-xs-3 yeniserino {{Input::old('yeniserino') ?  "" : ($arizakayit->serinodegisim ? "" : "hide")}}" style="margin-top: 9px">{{ Input::old('yeniserino') ? Input::old('yeniserino') : ($arizakayit->serinodegisim ? $arizakayit->servistakip->serino : '')}}</label><input id="yeniserino" name="yeniserino" value="{{ Input::old('yeniserino') ? Input::old('yeniserino') : ($arizakayit->serinodegisim ? $arizakayit->servistakip->serino : '')}}" class="hide"/>
                    <label class="col-sm-2 col-xs-3 eklebutton {{Input::old('yeniserino') ?  "hide" : ($arizakayit->serinodegisim ? "hide" : "")}}"><a class="btn yellow" href='#serinoekle' data-toggle='modal' data-id=''>Yeni Seri Numarası Ekle</a></label>
                    <label class="col-sm-2 col-xs-2 silbutton {{Input::old('yeniserino') ?  "" : ($arizakayit->serinodegisim ? "" : "hide")}}" style="margin-top: 5px;"><a class="btn red" href='#serinosil' data-toggle='modal' data-id=''>Yeni Seri Numarasını Sil</a></label>
                    <input type="text" id="sayacgelenid" name="sayacgelenid" value="{{ Input::old('sayacgelenid') ? Input::old('sayacgelenid') : $arizakayit->sayacgelen->id }}" class="form-control hide"/>
                    <input type="text" id="sayacid" name="sayacid" value="{{ Input::old('sayacid') ? Input::old('sayacid') : $arizakayit->sayac->id }}" class="form-control hide"/>
                    <input type="text" id="garantiilk" name="garantiilk" value="{{ Input::old('garanti') ? Input::old('garanti') : $arizakayit->garanti }}" class="form-control hide"/>
                    <input type="text" id="hurdadurum" name="hurdadurum" value="{{Input::old('hurdadurum') ? Input::old('hurdadurum') : $arizakayit->sayacgelen->hurdadurum}}" class="form-control hide"/>
                    <input type="text" id="hurdanedeni" name="hurdanedeni" value="{{Input::old('hurdanedeni') ? Input::old('hurdanedeni') : ($arizakayit->sayacgelen->hurdadurum==1 ? $arizakayit->sayacgelen->hurdaneden : 0) }}" class="form-control hide"/>
                    <input type="text" id="sayacdurum" name="sayacdurum" value="{{Input::old('sayacdurum') ? Input::old('sayacdurum')  : ($arizakayit->arizakayit_durum==2 && $arizakayit->yenisayac) ? "6" : $arizakayit->arizakayit_durum }}" class="form-control hide">
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Geliş Tarihi:</label>
                    <div class="col-sm-8 col-xs-9">
                        <input type="text" id="gelis" name="gelis" class="form-control"  value="{{Input::old('gelis') ? Input::old('gelis') : $arizakayit->sayacgelen->depotarihi ? date("d-m-Y", strtotime($arizakayit->sayacgelen->depotarihi)) : '' }}" readonly="">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Önceki Geliş Tarihi:</label>
                    <div class="col-sm-6 col-xs-7">
                        <input type="text" id="oncekigelis" name="oncekigelis" class="form-control" value="{{Input::old('oncekigelis') ? Input::old('oncekigelis') : $arizakayit->sayac->songelistarihi ? date("d-m-Y", strtotime($arizakayit->sayac->songelistarihi)) : ''  }}" readonly="">
                    </div>
                    <div class="col-sm-2 col-xs-2">
                        <a class="btn green gelisbilgigetir {{ $arizakayit->sayac->songelistarihi ? '' : 'disabled'}}" href='#bilgigetir' data-toggle='modal' data-id=''>Tümü</a>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-3">Cari İsim: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-9 col-xs-8">
                        <i class="fa"></i><input type="text" id="cariadi" name="cariadi" class="form-control" value="{{Input::old('cariadi') ? Input::old('cariadi') : $arizakayit->netsiscari->cariadi }}" readonly="">
                        <input type="text" id="cariid" name="cariid" class="form-control hide" value="{{Input::old('cariid') ? Input::old('cariid') : $arizakayit->netsiscari->id }}" readonly="">
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-3">İstek: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-9 col-xs-8">
                        <i class="fa"></i><input type="text" id="istek" name="istek" class="form-control" value="{{Input::old('istek') ? Input::old('istek') : $arizakayit->servisstokkod->stokadi }}" readonly="">
                        <input type="text" id="istekid" name="istekid" class="form-control hide" value="{{Input::old('istekid') ? Input::old('istekid') : $arizakayit->servisstokkod->id }}" readonly="">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Geliş Yeri: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-9">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="uretimyer" name="uretimyer" tabindex="2" title="">
                            <option value="{{ $arizakayit->uretimyer->id }}" selected> {{ $arizakayit->uretimyer->yeradi }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Üretim Tarihi: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-9">
                        <i class="fa"></i><div class="input-group input-medium date date-picker uretim" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                            <input type="text" id="uretim" name="uretim" class="form-control" value="{{Input::old('uretim') ? Input::old('uretim') : $arizakayit->sayac->uretimtarihi ? date("d-m-Y", strtotime($arizakayit->sayac->uretimtarihi)) : '' }}" tabindex="3">
                            <span class="input-group-btn"><button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button></span>
                        </div>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Sayaç Adı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-9">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="sayacadi" name="sayacadi" tabindex="4" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($sayacadlari as $sayacadi)
                                @if((Input::old('sayacadi') ? Input::old('sayacadi') : $arizakayit->sayacadi_id)==$sayacadi->id)
                                    <option value="{{ $sayacadi->id }}" selected>{{ $sayacadi->sayacadi }}</option>
                                @else
                                    <option value="{{ $sayacadi->id }}" >{{ $sayacadi->sayacadi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Garanti Durum: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-9">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="garanti" name="garanti" tabindex="5" title="">
                            <option value="0" @if((Input::old('garanti') ? Input::old('garanti') :$arizakayit->garanti)==0) selected @endif>Dışında</option>
                            <option value="1" @if((Input::old('garanti') ? Input::old('garanti') :$arizakayit->garanti)==1) selected @endif>İçinde</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Aksesuar:</label>
                    <div class="col-sm-8 col-xs-9">
                        <select class="form-control select2 select2-offscreen" multiple="" tabindex="6" id="aksesuar" name="aksesuar[]">
                            @foreach($aksesuarlar as $aksesuar)
                                <option value="{{ $aksesuar->id }}">{{ $aksesuar->adi }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if(Input::old('aksesuar'))
                    <div id="aksesuarlar" class="hide aksesuarlar">
                        @foreach(Input::old('aksesuar') as $aksesuar)
                            {{$aksesuar}}
                        @endforeach
                    </div>
                @else
                    <div id="aksesuarekli" class="hide aksesuarekli">{{ $arizakayit->aksesuar }}</div>
                @endif
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Endeks:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-9">
                        <i class="fa"></i><input type="text" id="ilkmekanik" name="ilkmekanik"  value="{{ Input::old('ilkmekanik') ? Input::old('ilkmekanik') : round($arizakayit->ilkmekanik,3) }}" maxlength="14" data-required="1" class="form-control" tabindex="7">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Bağlantı Çapı:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-9">
                        <i class="fa"></i><input type="text" id="baglanticap" name="baglanticap" value="{{ Input::old('baglanticap') ? Input::old('baglanticap') : $arizakayit->baglanticap }}" maxlength="6" data-required="1" class="form-control" tabindex="8">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Pmax:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-9">
                        <i class="fa"></i><input type="text" id="pmax" name="pmax" value="{{ Input::old('pmax') ? Input::old('pmax') : $arizakayit->pmax }}" maxlength="6" data-required="1" class="form-control" tabindex="9">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Qmax:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-9">
                        <i class="fa"></i><input type="text" id="qmax" name="qmax" value="{{ Input::old('qmax') ? Input::old('qmax') : $arizakayit->qmax }}" maxlength="6" data-required="1" class="form-control" tabindex="10">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-sm-4 col-xs-3">Qmin:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-9">
                        <i class="fa"></i><input type="text" id="qmin" name="qmin" value="{{ Input::old('qmin') ? Input::old('qmin') : $arizakayit->qmin }}" maxlength="6" data-required="1" class="form-control" tabindex="11">
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-3">Müşteri Açıklaması:</label>
                    <div class="col-sm-9 col-xs-8">
                        <input type="text" id="musteribilgi" name="musteribilgi" value="{{ Input::old('musteribilgi') ? Input::old('musteribilgi') : $arizakayit->musteriaciklama }}" data-required="1" class="form-control" tabindex="12">
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-3">Arıza Açıklaması:</label>
                    <div class="col-sm-9 col-xs-8">
                        <input type="text" id="arizaaciklama" name="arizaaciklama" value="{{ Input::old('arizaaciklama') ? Input::old('arizaaciklama') : $arizakayit->arizaaciklama }}"
                               placeholder="Müşterinin göreceği arıza açıklaması" data-required="1" class="form-control" tabindex="13">
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-3">Arıza Tespiti:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-7">
                        <i class="fa"></i><select multiple="multiple" class="multi-select" id="arizalar" name="arizalar[]">
                            @foreach($arizakodlari as $arizakod)
                                <option data-id="{{ $arizakod->garanti }}" value="{{ $arizakod->id }}">{{ $arizakod->tanim }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input id="arizalist" name="arizalist" class="hide" value="{{ Input::old('arizalist') ? Input::old('arizalist') : $arizakayit->sayacariza ? $arizakayit->sayacariza->problemler : ''}}">
                    <label class="col-sm-1 col-xs-2"><a class="btn red" href='#arizaekle' data-toggle='modal' data-id=''>+</a></label>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-3">Değişen Parçalar:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-7">
                        <i class="fa"></i><select multiple="multiple" class="multi-select" id="degisenler" name="degisenler[]">
                            @foreach($degisenler as $degisen)
                                <option value="{{ $degisen->id }}">{{ $degisen->tanim }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input id="degisenlist" name="degisenlist" class="hide" value="{{ Input::old('degisenlist') ? Input::old('degisenlist') : $arizakayit->sayacdegisen ? $arizakayit->sayacdegisen->degisenler : ''}}">
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-3">Yapılan İşlemler:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-7">
                        <i class="fa"></i><select multiple="multiple" class="multi-select" id="yapilanlar" name="yapilanlar[]">
                            @foreach($yapilanlar as $yapilan)
                                <option value="{{ $yapilan->id }}">{{ $yapilan->tanim }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input id="yapilanlist" name="yapilanlist" class="hide" value="{{ Input::old('yapilanlist') ? Input::old('yapilanlist') : $arizakayit->sayacyapilan ? $arizakayit->sayacyapilan->yapilanlar : ''}}">
                    <label class="col-sm-1 col-xs-2"><a class="btn red" href='#yapilanekle' data-toggle='modal' data-id=''>+</a></label>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-3">Sonuç ve Uyarılar:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-sm-8 col-xs-7">
                        <i class="fa"></i><select multiple="multiple" class="multi-select" id="uyarilar" name="uyarilar[]">
                            @foreach($uyarilar as $uyari)
                                <option value="{{ $uyari->id }}">{{ $uyari->tanim }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input id="uyarilist" name="uyarilist" class="hide" value="{{ Input::old('uyarilist') ? Input::old('uyarilist') : $arizakayit->sayacuyari ? $arizakayit->sayacuyari->uyarilar : ''}}">
                    <label class="col-sm-1 col-xs-2"><a class="btn red" href='#uyariekle' data-toggle='modal' data-id=''>+</a></label>
                </div>
                <div class="form-group  col-xs-12 sertifika {{($arizakayit->sayactip->tipadi=="ROTARY" || $arizakayit->sayactip->tipadi=="TURBIN") ? "" : "hide"}}">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-sm-4 col-xs-3">Sertifika:</label>
                        <div class="input-icon col-sm-8 col-xs-7">
                            <input type="text" id="sertifika" name="sertifika" value="{{ Input::old('sertifika') ? Input::old('sertifika') : $arizakayit->sertifika }}" maxlength="20" data-required="1" class="form-control" tabindex="22">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-sm-4 col-xs-3">HF2:</label>
                        <div class="input-icon col-sm-8 col-xs-7">
                            <input type="text" id="hf2" name="hf2" value="{{ Input::old('hf2') ? Input::old('hf2') : $arizakayit->hf2 }}" maxlength="20" data-required="1" class="form-control" tabindex="23">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-sm-4 col-xs-3">HF3-I:</label>
                        <div class="input-icon col-sm-8 col-xs-7">
                            <input type="text" id="hf3" name="hf3" value="{{ Input::old('hf3') ? Input::old('hf3') : $arizakayit->hf3 }}" maxlength="20" data-required="1" class="form-control" tabindex="24">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label class="control-label col-sm-4 col-xs-3">HF3-II:</label>
                        <div class="input-icon col-sm-8 col-xs-7">
                            <input type="text" id="hf32" name="hf32" value="{{ Input::old('hf32') ? Input::old('hf32') : $arizakayit->hf32 }}" maxlength="20" data-required="1" class="form-control" tabindex="25">
                        </div>
                    </div>
                </div>
                <h4 class="form-section col-xs-12">Arıza Tespit Resimler <span style="font-size: 12px">Sayacın Geldiği Halinin Görüntüsü</span></h4>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-3">Resim</label>
                    <div class="col-sm-8 col-xs-7 fileinput fileinput-new" data-provides="fileinput">
                        <div class="input-group input-large">
                            <div class="form-control uneditable-input input-fixed input-xlarge" data-trigger="fileinput">
                                <i class="fa fa-image fileinput-exists"></i><span class="fileinput-filename"></span>
                            </div>
                            <span class="input-group-addon btn default btn-file" style="border:1px solid #969696">
                            <span class="fileinput-new">
                            Resim Seç </span>
                            <span class="fileinput-exists">
                            Değiştir </span>
                            <input type="file" name="resim[]" accept="image/*" multiple="" />
                            </span>
                            <a href="" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                Sil </a>
                        </div>
                    </div>
                </div>
                @if($arizakayit->resimler!=="")
                    <h4 class="form-section col-xs-12">Ekli Resimler <span style="font-size: 12px">Kayıt Sırasında Eklenen Resimler</span></h4>
                    @foreach($arizakayit->resimlist as $resim)
                        <div class="form-group">
                            <div class="col-xs-10">
                                <span>- {{$resim}}</span>
                                <a href="{{ URL::to('assets/arizaresim/'.$resim) }}" type="button" class="btn btn-warning" target="_blank">Göster</a>
                                <button data-id="{{$resim}}" class="btn red resimsil">Sil</button>
                            </div>
                        </div>
                    @endforeach
                @endif
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-3">Not:</label>
                    <div class="col-sm-8 col-xs-7">
                        <input type="text" id="arizanot" name="arizanot" value="{{ Input::old('arizanot') ? Input::old('arizanot') : $arizakayit->arizanot }}" data-required="1" class="form-control" tabindex="26">
                    </div>
                </div>
                <div class="form-group col-xs-12" style="margin:0;text-align:center">
                        <label><input value="3" name="ozeldurum[]" type="checkbox" {{ (Input::old('ozeldurum') ? Input::old('ozeldurum') : $arizakayit->arizakayit_durum)==3 ? 'checked' : '' }}> Yedek Parça Bekliyor </label>
                        <label><input value="4" name="ozeldurum[]" type="checkbox" {{ (Input::old('ozeldurum') ? Input::old('ozeldurum') : $arizakayit->arizakayit_durum)==4 ? 'checked' : '' }} > Şikayetli Muayene </label>
                        <label><input value="5" name="ozeldurum[]" type="checkbox" {{ (Input::old('ozeldurum') ? Input::old('ozeldurum') : $arizakayit->arizakayit_durum)==5 ? 'checked' : '' }} > Müdahaleli Sayaç </label>
                        <label><input value="6" name="ozeldurum[]" type="checkbox" {{ (Input::old('ozeldurum')==6 || ($arizakayit->arizakayit_durum==2 && $arizakayit->yenisayac)) ? 'checked' : '' }} > Yeni Sayaç Verildi </label>
                        <label><input value="7" name="ozeldurum[]" type="checkbox" {{ (Input::old('ozeldurum') ? Input::old('ozeldurum') : $arizakayit->arizakayit_durum)==7 ? 'checked' : '' }} > Geri İade </label>
                        <label><input value="8" name="ozeldurum[]" type="checkbox" {{ (Input::old('ozeldurum') ? Input::old('ozeldurum') : $arizakayit->arizakayit_durum)==8 ? 'checked' : '' }} > Geri İade(Kalibrasyonsuz) </label>
                </div>
                <div class=" form-group hide"><input class="col-md-10" id="resimler" name="resimler" value="{{$arizakayit->resimler}}"/></div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <button type="button" class="btn green" data-toggle="modal" data-target="#confirm">Kaydet</button>
                        @if($arizakayit->sayacgelen->hurdadurum==0)
                            <button type="button" class="btn yellow" data-toggle="modal" data-target="#hurdaayir">Hurdaya Ayır</button>
                        @else
                            <button type="button" class="btn blue" data-toggle="modal" data-target="#hurdacikar">Hurdadan Çıkar</button>
                        @endif
                        <a href="{{ URL::to('mekanikgaz/arizakayit')}}" class="btn default">Vazgeç</a>
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
                    <h4 class="modal-title">Arıza Kayıdı Kaydedilecek</h4>
                </div>
                <div class="modal-body">
                    Girilen Arıza Kayıdı Bilgileri Kaydedilecektir?
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
    <div class="modal fade" id="hurdaayir" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Girilen Arıza Kayıdı Bilgileri Kaydedilecek ve Sayaç Hurdaya Ayrılacaktır?</h4>
                </div>
                <div class="modal-body" style="height:70px">
                    <label class="control-label col-md-2">Hurda Nedeni<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-md-6" style="">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="hurdaneden" name="hurdaneden" tabindex="-1" title="">
                            @foreach($hurdanedenleri as $hurdaneden)
                                @if(Input::old('hurdaneden')==$hurdaneden->id)
                                    <option value="{{ $hurdaneden->id }}" selected>{{ $hurdaneden->nedeni }}</option>
                                @else
                                    <option value="{{ $hurdaneden->id }}">{{ $hurdaneden->nedeni }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <label class="col-md-1"><a class="btn red" href='#hurdanedeniekle' data-toggle='modal' data-id=''>+</a></label>
                </div>
                <div class="modal-footer">
                    <a id="hurdakaydet" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="hurdacikar" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Girilen Arıza Kayıdı Bilgileri Kaydedilecek ve Sayaç Hurdadan Çıkarılacaktır?</h4>
                </div>
                <div class="modal-footer">
                    <a id="hurdacikarkaydet" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="bilgigetir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Önceki Geliş Tarihleri
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_1" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Geliş Tarihi</th>
                                                    <th>Arıza Kayıt Tarihi</th>
                                                    <th>Depo Teslim Tarihi</th>
                                                    <th>Detay</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green" data-dismiss="modal">Tamam</button>
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
    <div class="modal fade" id="detaygoster" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Önceki Arıza Kayıdının Detayı
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section col-xs-12">Arıza Kayıdının Detayı</h3>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Seri No:</label>
                                            <label class="col-xs-8 eskiserino" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Geliş Tarihi:</label>
                                            <label class="col-xs-8 eskigelis" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Cari İsim:</label>
                                            <label class="col-xs-8 eskicari" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">İstek:</label>
                                            <label class="col-xs-8 eskiistek" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Geliş Yeri:</label>
                                            <label class="col-xs-8 eskiyer" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Üretim Tarihi:</label>
                                            <label class="col-xs-8 eskiuretim" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Sayaç Adı:</label>
                                            <label class="col-xs-8 eskisayacadi" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Garanti Durum:</label>
                                            <label class="col-xs-8 eskigaranti" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Aksesuar:</label>
                                            <label class="col-xs-8 eskiaksesuar" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Endeks:</label>
                                            <label class="col-xs-8 eskimekanik" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Bağlantı Çapı:</label>
                                            <label class="col-xs-8 eskibaglanticap" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Pmax:</label>
                                            <label class="col-xs-8 eskipmax" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Qmax:</label>
                                            <label class="col-xs-8 eskiqmax" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Qmin:</label>
                                            <label class="col-xs-8 eskiqmin" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Müşteri Açıklaması:</label>
                                            <label class="col-xs-8 eskiaciklama" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Arıza Tespiti:</label>
                                            <label class="col-xs-8 eskiariza" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Yapılan İşlemler:</label>
                                            <label class="col-xs-8 eskiyapilan" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Değişen Parçalar:</label>
                                            <label class="col-xs-8 eskidegisen" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Sonuç ve Uyarılar:</label>
                                            <label class="col-xs-8 eskiuyari" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group col-xs-12 eskisertifikaek hide">
                                            <div class="form-group col-sm-4 col-xs-12">
                                                <label class="control-label col-sm-4 col-xs-3">Sertifika:</label>
                                                <label class="col-sm-8 col-xs-7 eskisertifika" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-4 col-xs-12">
                                                <label class="control-label col-sm-4 col-xs-3">HF2:</label>
                                                <label class="col-sm-8 col-xs-7 eskihf2" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-4 col-xs-12">
                                                <label class="control-label col-sm-4 col-xs-3">HF3-I:</label>
                                                <label class="col-sm-8 col-xs-7 eskihf3" style="padding-top: 9px"></label>
                                            </div>
                                            <div class="form-group col-sm-4 col-xs-12">
                                                <label class="control-label col-sm-4 col-xs-3">HF3-II:</label>
                                                <label class="col-sm-8 col-xs-7 eskihf32" style="padding-top: 9px"></label>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Kayıt Yapan:</label>
                                            <label class="col-xs-8 eskikullanici" style="padding-top: 9px"></label>
                                        </div>
                                        <h4 class="form-section col-xs-12">Ekli Resimler <span style="font-size: 12px">Kayıt Sırasında Eklenen Resimler</span></h4>
                                        <div class="form-group eskiresimler col-xs-12">
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Not:</label>
                                            <label class="col-xs-8 eskinot" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn default" data-dismiss="modal">Tamam</button>
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
    <div class="modal fade" id="arizaekle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Arıza Tespiti Ekle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_3" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Yeni Arıza Tespiti Ekle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Arıza Tespiti<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-6">
                                                <i class="fa"></i><input type="text" id="arizayeni" name="arizayeni" value="{{Input::old('arizayeni')}}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green arizaekle">Kaydet</button>
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
    <div class="modal fade" id="yapilanekle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                                <form action="" id="form_sample_4" class="form-horizontal" novalidate="novalidate">
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
                                            <label class="control-label col-md-3">Yapılan İşlem<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-6">
                                                <i class="fa"></i><input type="text" id="yapilanyeni" name="yapilanyeni" value="{{Input::old('yapilanyeni')}}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green yapilanekle">Kaydet</button>
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
    <div class="modal fade" id="hurdanedeniekle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Hurda Nedeni Ekle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_5" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Yeni Hurda Nedeni Ekle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Hurda Nedeni<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-6">
                                                <i class="fa"></i><input type="text" id="hurdanedenyeni" name="hurdanedenyeni" value="{{Input::old('hurdanedenyeni')}}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green hurdanedeniekle">Kaydet</button>
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
    <div class="modal fade" id="uyariekle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Sonuç - Uyarı Ekle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_6" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Yeni Sonuç - Uyarı Ekle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Tanımı<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-6">
                                                <i class="fa"></i><input type="text" id="uyariyeni" name="uyariyeni" value="{{Input::old('uyariyeni')}}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green uyariekle">Kaydet</button>
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
    <div class="modal fade" id="serinoekle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Yeni Seri Numarası Ekle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_7" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Yeni Seri Numarası Ekle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Seri Numarası<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-6">
                                                <i class="fa"></i><input type="text" id="serinoyeni" name="serinoyeni" value="{{Input::old('serinoyeni')}}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green serinoekle">Kaydet</button>
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
    <div class="modal fade" id="serinosil" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Yeni Seri Numarası Arıza Kayıdından Silinecek</h4>
                </div>
                <div class="modal-body">
                    Girilen Yeni Seri Numarası Silinecek?
                </div>
                <div class="modal-footer">
                    <a id="formserisil" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@stop

