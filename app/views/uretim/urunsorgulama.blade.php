@extends('layout.master')

@section('page-title')
<!--suppress JSValidateTypes -->
<div class="page-title">
    <h1>Ürün - Parça <small>Sorgulama Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::to('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css">
<link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
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
<script src="{{ URL::to('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/datatables.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery-maskmoney/jquery.maskMoney.min.js') }}" type="text/javascript" ></script>

@stop

@section('page-script')
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
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
                $('#secilen').val(secilen);
            } else {
                $(this).removeClass("active");
                $('#secilen').val("");
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
        $('.getir').click(function(){
            var kriter = $("#kriter").val();
            var kriterdeger = $("#kriterdeger").val();
            if (kriterdeger !== "" && kriter !== "" ) {
                $.blockUI();
                $.getJSON("{{ URL::to('uretim/urunsorgulamabilgi') }}",{tip:kriter,kriter:kriterdeger}, function (event) {
                    if (event.durum)
                    {
                        if(event.count>1){
                            urunbilgi = event.urunbilgi;
                            oTable.clear().draw();
                            $.each(urunbilgi, function (index) {
                                if(urunbilgi[index].tipi==="1"){
                                    oTable.row.add([urunbilgi[index].id,urunbilgi[index].serino,urunbilgi[index].kodu,urunbilgi[index].stokadi ])
                                        .draw();
                                }else{
                                    oTable.row.add([urunbilgi[index].id,urunbilgi[index].barkod,urunbilgi[index].kodu,urunbilgi[index].stokadi ])
                                        .draw();
                                }
                            });
                            $("#secilenkriter").val(kriter);
                            $('#urunlistesi').modal('show');
                        }else {
                            var urunbilgi = event.urunbilgi;
                            if(urunbilgi.tipi==="1"){
                                $('.header').text('Ürün Bilgisi');
                                $('.parcainfo').addClass('hide');
                                $('.uruninfo').removeClass('hide');
                                $('.urunserino').text(urunbilgi.serino);
                                $('.urunuretimtarihi').text(urunbilgi.geklenmetarihi);
                                $('.urunstokadi').text(urunbilgi.stokadi);
                                $('.urunstokkodu').text(urunbilgi.kodu);
                                $('.urunisemrino').text(urunbilgi.isemrino);
                                $('.usturuninfo').addClass('hide');
                                $('.alturuninfo').addClass('hide');
                                $('.usturunheader').addClass('hide');
                                $('.alturunheader').addClass('hide');
                                $('.receteheader').addClass('hide');
                                $('.recetebilgisi').addClass('hide');
                                $('.urunheader').addClass('hide');
                                $('.uretilenurunbilgisi').addClass('hide');
                                if(urunbilgi.usturun!=null){
                                    $('.usturunheader').removeClass('hide');
                                    $('.usturuninfo').removeClass('hide');
                                    var usturun = urunbilgi.usturun;
                                    $('.usturunserino').text(usturun.serino);
                                    $('.usturunuretimtarihi').text(usturun.uretimsonu.geklenmetarihi);
                                    $('.usturunstokadi').text(usturun.netsisstokkod.adi);
                                    $('.usturunstokkodu').text(usturun.netsisstokkod.kodu);
                                    $('.usturunisemrino').text(usturun.uretimsonu.isemrino);
                                }
                                if(urunbilgi.alturun!=null){
                                    $('.alturunheader').removeClass('hide');
                                    $('.alturuninfo').removeClass('hide');
                                    var alturun = urunbilgi.alturun;
                                    $('.alturunserino').text(alturun.serino);
                                    $('.alturunuretimtarihi').text(alturun.uretimsonu.geklenmetarihi);
                                    $('.alturunstokadi').text(alturun.netsisstokkod.adi);
                                    $('.alturunstokkodu').text(alturun.netsisstokkod.kodu);
                                    $('.alturunisemrino').text(alturun.uretimsonu.isemrino);
                                }
                                if(urunbilgi.uretimsonukullanilan!=null){
                                    $('.receteheader').removeClass('hide');
                                    $('.recetebilgisi').removeClass('hide');
                                    var parcalar=urunbilgi.uretimsonukullanilan;
                                    var newRow;
                                    for(var i=0;i<parcalar.length;i++) {
                                        var id = i;
                                        newRow = '<div class="panel panel-default urunler_ek"><input class="no hide" value="' + id + '"/><div class="panel-heading">' +
                                            '<h4 class="panel-title"><a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_' + id + '">' +
                                            parcalar[i].netsisstokkod.kodu + ' - ' + parcalar[i].netsisstokkod.adi + '</a>' +
                                            '</h4></div><div id="collapse_' + id + '" class="panel-collapse in"><div class="panel-body">' +
                                            '<div class="form-group col-xs-12"><div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Depo Kodu:</label><label class="col-xs-8" style="padding-top: 7px">'+parcalar[i].depokodu+'</label></div>' +
                                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Adet:</label><label class="col-xs-8" style="padding-top: 7px">'+parcalar[i].urunadet+'</label></div></div>' +
                                            '<div class="form-group col-xs-12"><div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Üretici:</label><label class="col-xs-8" style="padding-top: 7px">'+(parcalar[i].uretimuretici ? parcalar[i].uretimuretici.ureticiadi : '')+'</label></div>' +
                                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Marka:</label><label class="col-xs-8" style="padding-top: 7px">'+(parcalar[i].uretimmarka ? parcalar[i].uretimmarka.markaadi : '')+'</label></div></div>' +
                                            '<div class="form-group col-xs-12"><div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Üretim Yılı:</label><label class="col-xs-8" style="padding-top: 7px">'+parcalar[i].uretimurun.guretimtarihi+'</label></div>' +
                                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Barkod:</label><label class="col-xs-8" style="padding-top: 7px">'+parcalar[i].uretimurun.barkod+'</label></div></div>' +
                                            '</div></div></div>';
                                        $('.recetebilgisi').append(newRow);
                                    }
                                }
                            }else{
                                $('.header').text('Parça Bilgisi');
                                $('.uruninfo').addClass('hide');
                                $('.parcainfo').removeClass('hide');
                                $('.usturuninfo').addClass('hide');
                                $('.alturuninfo').addClass('hide');
                                $('.usturunheader').addClass('hide');
                                $('.alturunheader').addClass('hide');
                                $('.receteheader').addClass('hide');
                                $('.recetebilgisi').addClass('hide');
                                $('.urunheader').addClass('hide');
                                $('.uretilenurunbilgisi').addClass('hide');
                                $('.parcabarkod').text(urunbilgi.barkod);
                                $('.parcastokadi').text(urunbilgi.stokadi);
                                $('.parcastokkodu').text(urunbilgi.kodu);
                                $('.parcaadet').text(urunbilgi.adet);
                                $('.parcakalan').text(urunbilgi.kalan);
                                $('.parcadepokayidi').text(urunbilgi.depokayitbilgi);
                                $('.parcauretici').text(urunbilgi.ureticiadi);
                                $('.parcamarka').text(urunbilgi.markaadi);
                                $('.parcauretimyili').text(urunbilgi.guretimtarihi);
                                $('.parcabarkod1').text(urunbilgi.urunbarkod1==null ? "" : urunbilgi.urunbarkod1);
                                $('.parcabarkod2').text(urunbilgi.urunbarkod2==null ? "" : urunbilgi.urunbarkod2);
                                $('.parcabarkod3').text(urunbilgi.urunbarkod3==null ? "" : urunbilgi.urunbarkod3);
                                if(urunbilgi.uretimsonukullanilan!=null){
                                    $('.urunheader').removeClass('hide');
                                    $('.uretilenurunbilgisi').removeClass('hide');
                                    var uretimsonukullanilan=urunbilgi.uretimsonukullanilan;
                                    for (var j=0;j<uretimsonukullanilan.length;j++){
                                            newRow = '<div class="panel panel-default urunler_ek"><input class="no hide" value="' + j + '"/><div class="panel-heading">' +
                                                '<h4 class="panel-title"><a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion2" href="#collapse2_' + j + '">' +
                                                uretimsonukullanilan[j].uretimsonu.netsisstokkod.kodu + ' - ' + uretimsonukullanilan[j].uretimsonu.netsisstokkod.adi + '</a>' +
                                                '</h4></div><div id="collapse2_' + j + '" class="panel-collapse in"><div class="panel-body">' +
                                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 col-sm-4 control-label">Seri No:</label><label class="col-xs-8" style="padding-top: 7px">'+uretimsonukullanilan[j].serinolar+'</label></div>' +
                                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 col-sm-4 control-label">Adet:</label><label class="col-xs-8" style="padding-top: 7px">'+uretimsonukullanilan[j].uretimsonu.adet+'</label></div>' +
                                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 col-sm-4 control-label">Üretim Tarihi:</label><label class="col-xs-8" style="padding-top: 7px">'+uretimsonukullanilan[j].uretimsonu.geklenmetarihi+'</label></div>' +
                                                '</div></div></div>';
                                            $('.uretilenurunbilgisi').append(newRow);
                                    }
                                }
                            }
                            $('.header').removeClass('hide');
                        }
                    } else {
                        $('.header').addClass('hide');
                        $('.uruninfo').addClass('hide');
                        $('.parcainfo').addClass('hide');
                        $('.usturuninfo').addClass('hide');
                        $('.alturuninfo').addClass('hide');
                        $('.usturunheader').addClass('hide');
                        $('.alturunheader').addClass('hide');
                        $('.receteheader').addClass('hide');
                        $('.recetebilgisi').addClass('hide');
                        $('.urunheader').addClass('hide');
                        $('.uretilenurunbilgisi').addClass('hide');
                        toastr[event.type](event.text,event.title);
                    }
                    $.unblockUI();
                });
            }else{
                toastr['warning']('Kriter Bilgisi Girilmemiş', 'Kriter Hatası');
            }
        });
        $('#listesec').click(function () {
            var secilen = $('#secilen').val();
            var secilenkriter = $('#secilenkriter').val();
            if ( secilen !== "") {
                $.blockUI();
                $.getJSON("{{ URL::to('uretim/urunlistebilgigetir') }}", {id: secilen,kriter:secilenkriter}, function (event) {
                    if (event.durum) {
                        var urunbilgi = event.urunbilgi;
                        if(cariadi!==urunbilgi.netsiscari_id)
                            $("#cariadi").select2('val',urunbilgi.netsiscari_id).trigger('change');
                        $('#abone').val(urunbilgi.id);
                        $('.abone').text(urunbilgi.adisoyadi);
                        $('.telefon').text(urunbilgi.telefon);
                        $('.uretimyer').text(urunbilgi.yeradi);
                        $('#adres').val(urunbilgi.faturaadresi);
                        $('.tckimlikno').text(urunbilgi.tckimlikno);
                        $('#faturaadresi').val(urunbilgi.faturaadresi);
                        $('#faturano').val(urunbilgi.faturano);
                    } else {
                        $('#abone').val('');
                        $('.abone').text('');
                        $('.telefon').text('');
                        $('.uretimyer').text('');
                        $('#adres').val('');
                        $('.tckimlikno').text('');
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                    $('#urunlistesi').modal('hide');
                });
            } else {
                toastr['warning']('Kriter Bilgisi Girilmemiş', 'Kriter Hatası');
            }
        });
    });
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-search-plus"></i>Ürün - Parça Sorgulama Ekranı
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form id="form_sample" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-xs-2">Kriter:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-3">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="kriter" name="kriter" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            <option value="1">Ürün Numarası</option>
                            <option value="2">Parça Barkod Numarası</option>
                        </select>
                    </div>
                    <div class="input-icon right col-xs-3">
                        <i class="fa"></i><input type="text" id="kriterdeger" name="kriterdeger" data-required="1" class="form-control">
                    </div>
                    <div class="col-xs-4"><a class="btn green getir">Bilgileri Getir</a></div>
                </div>

                <h4 class="form-section header hide"></h4>
                <div class="form-group uruninfo hide">
                    <div class="form-group">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Seri No:</label>
                            <label class=" col-xs-8 urunserino" style="padding-top: 7px"></label>
                        </div>
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Üretim Tarihi:</label>
                            <label class="col-xs-8 urunuretimtarihi" style="padding-top: 7px"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Stok Adı:</label>
                            <label class="col-xs-8 urunstokadi" style="padding-top: 7px"></label>
                        </div>
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Stok Kodu:</label>
                            <label class="col-xs-8 urunstokkodu" style="padding-top: 7px"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 col-sm-2 control-label">İş Emri:</label>
                        <label class="col-xs-8 col-sm-10 urunisemrino" style="padding-top: 7px"></label>
                    </div>
                </div>
                <div class="form-group parcainfo hide">
                    <div class="form-group">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Stok Adı:</label>
                            <label class="col-xs-8 parcastokadi" style="padding-top: 7px"></label>
                        </div>
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Stok Kodu:</label>
                            <label class="col-xs-8 parcastokkodu" style="padding-top: 7px"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Barkod:</label>
                            <label class=" col-xs-8 parcabarkod" style="padding-top: 7px"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 col-sm-2 control-label">Depo Kayıdı:</label>
                        <label class="col-xs-8 col-sm-10 parcadepokayidi" style="padding-top: 7px"></label>
                    </div>
                    <div class="form-group">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Adet:</label>
                            <label class=" col-xs-8 parcaadet" style="padding-top: 7px"></label>
                        </div>
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Kalan:</label>
                            <label class="col-xs-8 parcakalan" style="padding-top: 7px"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Üretici:</label>
                            <label class="col-xs-8 parcauretici" style="padding-top: 7px"></label>
                        </div>
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Marka:</label>
                            <label class="col-xs-8 parcamarka" style="padding-top: 7px"></label>
                        </div>
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="control-label col-xs-4">Üretim Yılı: </label>
                            <label class="col-xs-8 parcauretimyili" style="padding-top: 7px"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2 col-xs-6">Diğer Bilgiler (Barkod vb.):</label>
                        <label class="col-sm-3 col-xs-6 parcabarkod1" style="padding-top: 7px"></label>
                        <label class="col-sm-3 col-xs-6 parcabarkod2" style="padding-top: 7px"></label>
                        <label class="col-sm-3 col-xs-6 parcabarkod3" style="padding-top: 7px"></label>
                    </div>
                </div>

                <h4 class="form-section usturunheader hide">Üst Ürün Bilgisi</h4>
                <div class="form-group usturuninfo hide">
                    <div class="form-group">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Seri No:</label>
                            <label class=" col-xs-8 usturunserino" style="padding-top: 7px"></label>
                        </div>
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Üretim Tarihi:</label>
                            <label class="col-xs-8 usturunuretimtarihi" style="padding-top: 7px"></label>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Stok Adı:</label>
                            <label class="col-xs-8 usturunstokadi" style="padding-top: 7px"></label>
                        </div>
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Stok Kodu:</label>
                            <label class="col-xs-8 usturunstokkodu" style="padding-top: 7px"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 col-sm-2 control-label">İş Emri:</label>
                        <label class="col-xs-8 col-sm-10 usturunisemrino" style="padding-top: 7px"></label>
                    </div>
                </div>

                <h4 class="form-section alturunheader hide">Alt Ürün Bilgisi</h4>
                <div class="form-group alturuninfo hide">
                    <div class="form-group">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Seri No:</label>
                            <label class=" col-xs-8 alturunserino" style="padding-top: 7px"></label>
                        </div>
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Üretim Tarihi:</label>
                            <label class="col-xs-8 alturunuretimtarihi" style="padding-top: 7px"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Stok Adı:</label>
                            <label class="col-xs-8 alturunstokadi" style="padding-top: 7px"></label>
                        </div>
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="col-xs-4 control-label">Stok Kodu:</label>
                            <label class="col-xs-8 alturunstokkodu" style="padding-top: 7px"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 col-sm-2 control-label">İş Emri:</label>
                        <label class="col-xs-8 col-sm-10 alturunisemrino" style="padding-top: 7px"></label>
                    </div>
                </div>

                <h4 class="form-section receteheader hide">Reçete Bilgisi</h4>
                <div class="panel-group accordion recetebilgisi hide" id="accordion1">
                </div>

                <h4 class="form-section urunheader hide">Üretilen Ürünler</h4>
                <div class="panel-group accordion uretilenurunbilgisi hide" id="accordion2">
                </div>

            </div>
            <div class="form-actions">
                <div class="row">
                </div>
            </div>
        </form>
        <!-- END FORM-->
    </div>
<!-- END VALIDATION STATES-->
</div>              
@stop

@section('modal')
    <div class="modal fade" id="urunlistesi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-search"></i>Ürün Listesi
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Ürün Listesi</h3>
                                        <div class="portlet-body">
                                            <input type="text" id="secilen" name="secilen" class="hide" >
                                            <input type="text" id="secilenkriter" name="secilenkriter" class="hide" >
                                            <table class="table table-striped table-hover table-bordered" id="sample_1">
                                                <thead>
                                                <tr>
                                                    <th class="id">#</th>
                                                    <th>Seri No / Barkod</th>
                                                    <th>Stok Kodu</th>
                                                    <th>Stok Adı</th>
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
