@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Netsis Fatura Bilgi <small>Görüntüleme Ekranı</small></h1>
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
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.tr.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/datatables.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery.quicksearch.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/fuelux/js/spinner.min.js') }}" type="text/javascript" ></script>

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
$(document).ready(function(){
    $("select").on("select2-close", function () { $(this).valid(); });
});
</script>
<script>
    var table = $('#sample_editable_1');
    var oTable = table.DataTable({
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
        "bPaginate": false,
        "searching": false,
        "ordering": false,
        bInfo: false
    });
    var tableWrapper = jQuery('#sample_editable_1_wrapper');
    table.on('click', 'tr', function () {
        if($(this).children('.id').text()!==undefined){
            $(this).toggleClass("active");
            var adet=parseInt($('.secilenadet').html());
            var secilenler=$('#secilenler').val();
            if($(this).hasClass('active'))
            {
                secilenler+=(secilenler==="" ? "" : ",")+$(this).children('.id').html();
                adet++;
                $('#secilenler').val(secilenler);
                $('.secilenadet').html(adet);
            }else{
                var secilen=$(this).children('.id').data();
                var secilenlist=secilenler.split(',');
                var yenilist="";
                $.each(secilenlist,function(index){
                    if(secilenlist[index]!==secilen)
                    {
                        yenilist+=(yenilist==="" ? "" : ",")+secilenlist[index];
                    }
                });
                adet--;
                $('#secilenler').val(yenilist);
                $('.secilenadet').html(adet);
            }
            if(adet>0)
                $('.aktar').removeClass('hide');
            else
                $('.aktar').addClass('hide');
        }
    });
    $(document).on("click", ".temizle", function () {
        var adet=parseInt($('.secilenadet').html());
        var secilenler=$('#secilenler').val();
        $("#sample_editable_1 tbody tr .id").each(function(){
            var secilen=$(this).html();
            $(this).parents('tr').removeClass("active");
            var secilenlist=secilenler.split(',');
            var yenilist="";
            $.each(secilenlist,function(index){
                if(secilenlist[index]!==secilen)
                {
                    yenilist+=(yenilist==="" ? "" : ",")+secilenlist[index];
                }else{
                    adet--;
                }
            });
            secilenler=yenilist;
        });
        $('#secilenler').val(secilenler);
        $('.secilenadet').html(adet);
        var secilenlist=secilenler.split(',');
        oTable.rows().every( function () {
            var data = this.data();
            var id=data[0];
            var durum=0;
            $.each(secilenlist,function(index){
                if(id===secilenlist[index])
                    durum=1;
            });
        });
        if(adet>0){
            $('.aktar').removeClass('hide');
        }else{
            $('.aktar').addClass('hide');
        }
    });
    $(document).on("click", ".tumunusec", function () {
        var adet=parseInt($('.secilenadet').html());
        var secilenler=$('#secilenler').val();
        $("#sample_editable_1 tbody tr .id").each(function(){
            var secilen=$(this).html();
            $(this).parents('tr').addClass("active");
            var secilenlist=secilenler.split(',');
            var flag=0;
            $.each(secilenlist,function(index){
                if(secilenlist[index]===secilen)
                {
                    flag=1;
                }
            });
            if(flag===0){
                secilenler+=(secilenler==="" ? "" : ",")+secilen;
                adet++;
            }
        });
        $('#secilenler').val(secilenler);
        $('.secilenadet').html(adet);
        var secilenlist=secilenler.split(',');
        oTable.rows().every( function () {
            var data = this.data();
            var id=data[0];
            var durum=0;
            $.each(secilenlist,function(index){
                if(id===secilenlist[index])
                    durum=1;
            });
        });
        if(adet>0){
            $('.aktar').removeClass('hide');
        }else{
            $('.aktar').addClass('hide');
        }
    });
    tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
</script>
<script>
    $(document).ready(function() {
        $('.listegetir').click(function(){
            var faturayil = $("#faturayil").val();
            var subekodu = $('#subekodu').val();
            if (subekodu !=="1" && faturayil!=="") {
                $.blockUI();
                var newRow;
                $.getJSON("{{ URL::to('subedatabase/faturalistegetir') }}",{subekodu:subekodu,faturayil:faturayil}, function (event) {
                    if (event.durum) {
                        var fatura = event.fatura;
                        var parabirimi = event.parabirimi;
                        if (event.count > 0) {
                            $('#sample_editable_1 tbody tr').remove();
                            $.each(fatura, function (index) {
                                newRow = '<tr>' +
                                    '<td class="id">' + fatura[index].FATIRS_NO + '</td><td>' + fatura[index].CARI_KODU + '</td><td>' + $.datepicker.formatDate('dd-mm-yy', new Date(fatura[index].TARIH)) + '</td>' +
                                    '<td>' + (fatura[index].ACIKLAMA==null ? '' : fatura[index].ACIKLAMA ) + '</td><td>' + parseFloat(fatura[index].GENELTOPLAM).toFixed(2) +' '+parabirimi.birimi + '</td>' +
                                    '<td><a class="btn btn-sm btn-warning goster" href="#detay-goster" data-toggle="modal" data-id="'+fatura[index].FATIRS_NO+'"> Detay </a></td></tr>';
                                $('#sample_editable_1 tbody').append(newRow);
                            });
                        }
                        $("#dbname").val(event.dbname);
                    } else {
                        toastr[event.type](event.text,event.title);
                    }
                    $.unblockUI();
                });
            }else{
                toastr['warning']('Kriter Bilgisi Girilmemiş', 'Kriter Hatası');
            }
        });
        $(document).on("click", ".goster", function () {
            var faturayil = $("#faturayil").val();
            var subekodu = $('#subekodu').val();
            var faturano = $(this).data('id');
            if (subekodu !=="1" && faturayil!=="" && faturano!=="") {
                $.blockUI();
                $.getJSON("{{ URL::to('subedatabase/faturabilgigetir') }}", {subekodu: subekodu,faturayil: faturayil,faturano: faturano}, function (event) {
                    if (event.durum) {
                        var fatura = event.fatura;
                        var faturaek = event.faturaek;
                        var faturakalem = event.faturakalem;
                        var seritra = event.seritra;
                        var parabirimi = event.parabirimi;
                        var abonebilgi = event.abonebilgi;
                        var serinolar = event.serinolar;
                        if(fatura){
                            $('.faturano').text(fatura.FATIRS_NO);
                            $('#faturano').val(fatura.FATIRS_NO);
                            $('#faturasubekodu').val(subekodu);
                            $('#uretimyer').val(event.uretimyer);
                            $('.tarih').text($.datepicker.formatDate('dd-mm-yy', new Date(fatura.TARIH)));
                            $('.carikod').text(fatura.CARI_KODU+' ('+fatura.cari.cariadi+')' );
                            $('.projekod').text(fatura.PROJE_KODU);
                            $('.plasiyerkod').text(fatura.PLA_KODU+' ('+fatura.plasiyer.plasiyeradi+')');
                            $('.kasakod').text(fatura.KS_KODU+(fatura.kasakod==null ? "" : ' ('+fatura.kasakod.kasaadi+')'));
                            $('.bruttutar').text(parseFloat(fatura.BRUTTUTAR).toFixed(2)+' '+parabirimi.birimi);
                            $('.kdv').text(parseFloat(fatura.KDV).toFixed(2)+' '+parabirimi.birimi);
                            $('.geneltoplam').text(parseFloat(fatura.GENELTOPLAM).toFixed(2)+' '+parabirimi.birimi);
                            $('.kayityapan').text(fatura.KAYITYAPANKUL);
                            $('.kayittarihi').text($.datepicker.formatDate('dd-mm-yy', new Date(fatura.KAYITTARIHI)));
                        }
                        if(faturaek){
                            $('#acik1').val(faturaek.ACIK1!=null ? $.trim(faturaek.ACIK1) : '');
                            $('#acik2').val(faturaek.ACIK2!=null ? $.trim(faturaek.ACIK2) : (abonebilgi ? abonebilgi.adisoyadi : ''));
                            $('#acik3').val(faturaek.ACIK3!=null ? $.trim(faturaek.ACIK3) : '');
                            $('#acik4').val(faturaek.ACIK4!=null ? $.trim(faturaek.ACIK4) : (abonebilgi ? abonebilgi.faturaadresi : ''));
                            $('#acik5').val(faturaek.ACIK5!=null ? $.trim(faturaek.ACIK5) : '');
                            $('#acik6').val(faturaek.ACIK6!=null ? $.trim(faturaek.ACIK6) : '');
                            $('#acik7').val(faturaek.ACIK7!=null ? $.trim(faturaek.ACIK7) : '');
                            $('#acik8').val(faturaek.ACIK8!=null ? $.trim(faturaek.ACIK8) : serinolar);
                            $('#acik9').val(faturaek.ACIK9!=null ? $.trim(faturaek.ACIK9) : '');
                            $('#acik10').val(faturaek.ACIK10!=null ? $.trim(faturaek.ACIK10) : '');
                        }else{
                            $('#acik1').val('');
                            $('#acik2').val(abonebilgi ? abonebilgi.adisoyadi : '');
                            $('#acik3').val('');
                            $('#acik4').val(abonebilgi ? abonebilgi.faturaadresi : '');
                            $('#acik5').val('');
                            $('#acik6').val('');
                            $('#acik7').val('');
                            $('#acik8').val(serinolar);
                            $('#acik9').val('');
                            $('#acik10').val('');
                        }
                        var kalemrow;
                        $('#sample_2 tbody tr').remove();
                        if(faturakalem) {
                            $.each(faturakalem, function (index) {
                                kalemrow = '<tr><td>' + faturakalem[index].STOK_KODU + '</td>' +
                                    '<td>' + (faturakalem[index].subeurun ? faturakalem[index].subeurun.urunadi : '') + '</td>' +
                                    '<td>' + parseFloat(faturakalem[index].STHAR_GCMIK).toFixed(0) + '</td>' +
                                    '<td style="text-align: right">' + parseFloat(faturakalem[index].STHAR_NF).toFixed(2)+' '+parabirimi.birimi + '</td><td>' + parseFloat(faturakalem[index].STHAR_KDV).toFixed(2) + ' %</td>' +
                                    '<td style="text-align: right">' + parseFloat(faturakalem[index].STHAR_BF).toFixed(2)+' '+parabirimi.birimi + '</td></tr>';
                                $('#sample_2 tbody').append(kalemrow);
                            });
                        }
                        var seriler;
                        $('.seritra').html('');
                        if(seritra.length>0){
                            $.each(seritra, function (index) {
                                seriler='<div class="col-xs-3" style="padding-bottom: 5px"><input type="text" id="seri"'+index+' name="seri[]" value="'+seritra[index].SERI_NO+'" data-required="1" class="form-control"></div>';
                                $('.seritra').append(seriler);
                            });
                            $('.seridurum').addClass('hide');
                        }else{
                            $('.seridurum').removeClass('hide');
                        }
                        $('.seriadet').val(seritra.length);
                    } else {
                        toastr[event.type](event.text,event.title);
                    }
                    $.unblockUI();
                });
            }else{
                toastr['warning']('Seçilen Faturaya Ait Bilgi Yok', 'Fatura Bilgi Hatası');
            }
        });

        $('#baslangic').on('change',function(){
            var baslangic=parseInt($(this).val());
            var bitis=parseInt($('#bitis').val());
            if(baslangic && bitis)
            {
                if(bitis<baslangic)
                {
                    $(".sayacsayi").html("Hesaplanamıyor");
                    $("#seriekle").prop('disabled',true);
                }else{
                    var artis = $('#spinner').spinner('value');
                    var sayi=parseInt((bitis-baslangic)/artis)+1;
                    $(".sayacsayi").html(sayi+' adet - '+baslangic+' ile '+bitis+' arasındaki serinolar');
                    $("#seriekle").prop('disabled',false);
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
                    $(".sayacsayi").html("Hesaplanamıyor");
                    $("#seriekle").prop('disabled',true);
                }else{
                    var artis = $('#spinner').spinner('value');
                    var sayi=parseInt((bitis-baslangic)/artis)+1;
                    $(".sayacsayi").html(sayi+' adet - '+baslangic+' ile '+bitis+' arasındaki serinolar');
                    $("#seriekle").prop('disabled',false);
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
                    $(".sayacsayi").html("Hesaplanamıyor");
                    $("#seriekle").prop('disabled',true);
                }else{
                    var sayi=parseInt((bitis-baslangic)/artis)+1;
                    $(".sayacsayi").html(sayi+' adet - '+baslangic+' ile '+bitis+' arasındaki serinolar');
                    $("#seriekle").prop('disabled',false);
                }
            }
        });

        $('#seriekle').on('click',function () {
                var artis = $('#spinner').spinner('value');
                var baslangic=parseInt($('#baslangic').val());
                var bitis=parseInt($('#bitis').val());
                var sayi=((bitis-baslangic)/artis)+1;
                var adet = parseInt($('.seriadet').val());
                if(!isNaN(sayi)){
                    for(var i = 0;i<sayi;i++){
                        var index = adet+i+1;
                        var eklenecek = baslangic+(artis*i);
                        var serino='<div class="col-xs-3" style="padding-bottom: 5px"><input type="text" id="seri"'+index+' name="seri[]" value="'+eklenecek+'" data-required="1" class="form-control"></div>';
                        $('.seritra').append(serino);
                    }
                    adet = adet+sayi;
                }
                $('.seriadet').val(adet);
                $('#baslangic').val('');
                $('#bitis').val('');
                $('#spinner').val(1);
                $('#seriekleme').modal('hide');
        });
        $('.tekseriekle').on('click',function () {
            var adet = parseInt($('.seriadet').val());
            adet = adet+1;
            var serino='<div class="col-xs-3" style="padding-bottom: 5px"><input type="text" id="seri"'+adet+' name="seri[]" value="" data-required="1" class="form-control"></div>';
            $('.seritra').append(serino);
            $('.seriadet').val(adet);
        });

        $('.seritemizle').on('click',function () {
            $('.seritra').html('');
            $('.seriadet').val(0);
        });

        $('#formsubmit').click(function () {
            $('#form_sample_2').submit();
            $.blockUI();
        });
        $('#aktarsubmit').click(function () {
            $('#form_sample').submit();
            $.blockUI();
        });
    });
</script>

@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Fatura Bilgi Görüntüleme Ekranı
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('subedatabase/faturaaktar') }}" method="POST" id="form_sample" class="form-horizontal" novalidate="novalidate">
            <div class="form-body">
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    Girilen Bilgilerde Hata Var.
                </div>
                <div class="alert alert-success display-hide">
                    <button class="close" data-close="alert"></button>
                    Bilgiler Doğru!
                </div>
                <div class="form-group col-xs-12 {{$sube ? ($sube->subekodu!="1" ? "" : "hide") : "hide"}}">
                    <label class="control-label col-sm-2 col-xs-4">Fatura Dönemi: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-4"><i class="fa"></i>
                        <select class="form-control select2me faturayil" id="faturayil" name="faturayil" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($yillar as $yil)
                                <option value="{{ $yil }}">{{ $yil }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xs-2"><a class="btn green listegetir">Bilgileri Getir</a></div>
                </div>
                <div class="form-group {{$sube ? ($sube->subekodu=="1" ? "" : "hide") : ""}}">
                    <label class="col-xs-12" style="color: red">{{$sube ? ($sube->subekodu=="1" ? "Merkeze Bağlı Faturalar Gösterilemez!" : "") : "Kullanıcının Bilgi Getirme Yetkisi Yok!"}}</label>
                </div>
                <h4 class="form-section col-xs-12">Arama Sonuçları
                    <span style="padding-left:100px">
                        <button type="button" class="btn green tumunusec">Tümünü Seç</button>
                        <button type="button" class="btn red temizle">Temizle</button>
                        <a class='btn green aktar hide' href='#aktar' data-toggle='modal' data-id=''> Aktar</a>
                    </span>
                </h4>
                <div class="faturabilgi col-xs-12" id="faturabilgi">
                    <table class="table table-hover" id="sample_editable_1">
                        <thead>
                        <tr><th class="hide"></th>
                            <th>Fatura No</th>
                            <th>Cari Kodu</th>
                            <th>Tarih</th>
                            <th>Açıklama</th>
                            <th>Tutarı</th>
                            <th>Detay</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-6">Seçilen Fatura Sayısı:</label>
                    <label class="col-sm-3 col-xs-6 secilenadet" style="padding-top: 9px">0</label>
                    <div class="col-sm-6 col-xs-6">
                        <a class='btn green aktar hide' href='#aktar' data-toggle='modal' data-id=''> Aktar</a>
                    </div>
                    <div class="form-group col-xs-12 hide">
                        <input type="text" id="subekodu" name="subekodu" value="{{ $sube ? $sube->subekodu : '1'}}" data-required="1" class="form-control">
                        <input type="text" id="subelinked" name="subelinked" value="{{ $sube ? $sube->subelinked : ''}}" data-required="1" class="form-control">
                        <input type="text" id="bellinked" name="bellinked" value="{{ $sube ? $sube->bellinked : ''}}" data-required="1" class="form-control">
                        <input type="text" id="netsisdepo" name="netsisdepo" value="{{ $sube ? $sube->netsisdepolar_id : 1 }}" data-required="1" class="form-control">
                        <input type="text" id="netsiscari" name="netsiscari" value="{{ $sube ? $sube->netsiscari_id : 2631 }}" data-required="1" class="form-control">
                        <input type="text" id="secilenler" name="secilenler" value="{{ Input::old('secilenler') }}" class="form-control"/>
                    </div>
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
    <div class="modal fade" id="detay-goster" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Fatura Detayı
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{ URL::to('subedatabase/faturaekle') }}" id="form_sample_2" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body col-xs-12">
                                        <h3 class="form-section col-xs-12">Fatura Detayı</h3>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Fatura No:</label>
                                            <label class="col-xs-8 faturano" style="padding-top: 7px"></label>
                                            <input id="faturano" name="faturano" class="hide">
                                            <input id="dbname" name="dbname" class="hide">
                                            <input id="faturasubekodu" name="faturasubekodu" class="hide">
                                            <input id="uretimyer" name="uretimyer" class="hide">
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Tarih:</label>
                                            <label class="col-xs-8 tarih" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Cari Kodu:</label>
                                            <label class="col-xs-8 carikod" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Proje Kodu:</label>
                                            <label class="col-xs-8 projekod" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Plasiyer Kodu:</label>
                                            <label class="col-xs-8 plasiyerkod" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Kasa Kodu:</label>
                                            <label class="col-xs-8 kasakod" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Kayıt Yapan:</label>
                                            <label class="col-xs-8 kayityapan" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Kayıt Tarihi:</label>
                                            <label class="col-xs-8 kayittarihi" style="padding-top: 7px"></label>
                                        </div>
                                        <h3 class="form-section col-xs-12">Fatura Açıklamaları</h3>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">TC NO:</label>
                                            <div class="col-xs-8">
                                            <input type="text" id="acik1" name="acik1" value="{{ Input::old('acik1') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Müşteri Adı:</label>
                                            <div class="col-xs-8">
                                            <input type="text" id="acik2" name="acik2" value="{{ Input::old('acik2') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Telefonu:</label>
                                            <div class="col-xs-8">
                                            <input type="text" id="acik3" name="acik3" value="{{ Input::old('acik3') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Adresi:</label>
                                            <div class="col-xs-8">
                                            <input type="text" id="acik4" name="acik4" value="{{ Input::old('acik4') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Vergi Numarası:</label>
                                            <div class="col-xs-8">
                                            <input type="text" id="acik5" name="acik5" value="{{ Input::old('acik5') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Vergi Dairesi:</label>
                                            <div class="col-xs-8">
                                            <input type="text" id="acik6" name="acik6" value="{{ Input::old('acik6') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Genel Açıklama:</label>
                                            <div class="col-xs-8">
                                            <input type="text" id="acik7" name="acik7" value="{{ Input::old('acik7') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Genel Açıklama:</label>
                                            <div class="col-xs-8">
                                            <input type="text" id="acik8" name="acik8" value="{{ Input::old('acik8') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Ödeme Şekli:</label>
                                            <div class="col-xs-8">
                                            <input type="text" id="acik9" name="acik9" value="{{ Input::old('acik9') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Mahalle:</label>
                                            <div class="col-xs-8">
                                            <input type="text" id="acik10" name="acik10" value="{{ Input::old('acik10') }}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <h3 class="form-section col-xs-12">Fatura Kalemleri</h3>
                                        <div class="form-group col-xs-12 faturakalem" id="faturakalem">
                                            <table class="table table-hover" id="sample_2">
                                                <thead>
                                                <tr>
                                                    <th style="width: 100px">Stok Kodu</th>
                                                    <th>Stok Adı</th>
                                                    <th style="width: 50px">Miktarı</th>
                                                    <th style="width: 100px">Tutar</th>
                                                    <th style="width: 100px">Kdv</th>
                                                    <th style="width: 130px">Toplam Tutar</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                        </div>
                                        <div class="form-group col-sm-offset-6 col-sm-6 col-xs-12">
                                            <div class="col-xs-12">
                                                <label class="control-label col-xs-6">BRÜT TUTAR:</label>
                                                <label class="col-xs-6 bruttutar" style="padding-top: 9px;text-align: right">0.00</label>
                                            </div>
                                            <div class="col-xs-12">
                                                <label class="control-label col-xs-6">KDV TUTARI:</label>
                                                <label class="col-xs-6 kdv" style="padding-top: 9px;text-align: right">0.00</label>
                                            </div>
                                            <div class="col-xs-12">
                                                <label class="control-label col-xs-6">GENEL TOPLAM:</label>
                                                <label class="col-xs-6 geneltoplam" style="padding-top: 9px;text-align: right">0.00</label>
                                            </div>
                                        </div>
                                        <h3 class="form-section col-xs-12">Faturaya Ait Seri Numaraları</h3>
                                        <div class="col-xs-12 seridurum hide"><a class="btn green tekseriekle" >Tek Seri Ekle</a>
                                        <a class="btn yellow seriekle" data-toggle="modal" data-target="#seriekleme">Çoklu Seri Ekle</a>
                                            <a class="btn red seritemizle">Seri Temizle</a>
                                            <input class="hide seriadet" value="0"></div>
                                        <div class="form-group col-xs-12 seritra">
                                        </div>
                                        <div class="form-actions col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green kaydet" data-toggle="modal" data-target="#confirm">Kaydet</button>
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
    <div class="modal fade" id="seriekleme" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Seri Numarası Ekle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Abone Listesi</h3>
                                        <div class="portlet-body">
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Seri No Başlangıç:</label>
                                                <div class="col-xs-8">
                                                    <input type="text" id="baslangic" name="baslangic" value="" data-required="1" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Bitiş:</label>
                                                <div class="col-xs-8">
                                                    <input type="text" id="bitis" name="bitis" value="" data-required="1" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6 col-xs-12">
                                                <label class="control-label col-xs-4">Artış Miktarı:</label>
                                                <div id="spinner" class="col-xs-6">
                                                    <div class="input-group input-small">
                                                        <input type="text" name="artis" value="{{ 1 }}" class="spinner-input form-control" maxlength="3" readonly="">
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
                                                <label class="control-label col-xs-4">Sayaç Sayısı:</label>
                                                <label class="col-xs-8 sayacsayi" style="margin-top: 9px;color: red">0</label>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" id="seriekle" class="btn green">Seç</button>
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
    <div class="modal fade" id="confirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Fatura Bilgisi Kaydedilecek</h4>
                </div>
                <div class="modal-body">
                    Gelen Fatura Bilgisi Sisteme Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="formsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="aktar" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Fatura Bilgisi Kaydedilecek</h4>
                </div>
                <div class="modal-body">
                   Seçilen Faturalar Sisteme Kaydedilecektir?
                </div>
                <div class="modal-footer">
                    <a id="aktarsubmit" href="#" type="button" data-dismiss="modal" class="btn green">Kaydet</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
@stop

