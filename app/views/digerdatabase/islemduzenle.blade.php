@extends('layout.master')

@section('page-title')
<!--suppress JSCheckFunctionSignatures -->
<div class="page-title">
    <h1>İşlem <small>Düzenleme - Geri Alma Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/datatables.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/digerdatabase/form-validation-10.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar   
   FormValidationDigerDatabase.init();
});
</script>
<script>
    $(document).ready(function() {
        $('.tekgetir').click(function(){
            var kriter = $("#tekkriter").val();
            if (kriter !== "") {
                $.blockUI();
                $('.bilgi').html('');
                $('.buttons').html('');
                var servistakip;
                $.getJSON("{{ URL::to('digerdatabase/islembilgi') }}",{kriter:kriter}, function (event) {
                    if (event.durum)
                    {
                        if(event.count>1){
                            servistakip = event.servistakip;
                            $('#servistakipid').val(0);
                            $('#sample_1 tbody tr').remove();
                            $.each(servistakip, function (index) {
                                var newRow = '<tr><td><input type="checkbox" class="checkboxes"/></td>' +
                                    '<td class="hide id">' + servistakip[index].id + '</td><td>' + servistakip[index].serino + '</td><td>' + servistakip[index].uretimyer.yeradi + '</td>' +
                                    '<td>' + servistakip[index].sayacadi.sayacadi + '</td><td>' + servistakip[index].sayacdurum.durumadi + '</td>' +
                                    '<td>' + $.datepicker.formatDate('dd-mm-yy', new Date(servistakip[index].depogelen.tarih)) + '</td></tr>';
                                $('#sample_1 tbody').append(newRow);
                            });
                            $('#sayaclistesi').modal('show');
                        }else {
                            servistakip = event.servistakip;
                            $('#servistakipid').val(servistakip.id);
                            var sayacdurum = servistakip.sayacdurum;
                            var durum = "";
                            switch (servistakip.durum) {
                                case "1":
                                    durum = sayacdurum.durumadi + ". Arıza Kayıdı ya da Kalibrasyon Bekliyor.";
                                    break;
                                case "2":
                                    durum = sayacdurum.durumadi + ". Fiyatlandırma Bekliyor.";
                                    break;
                                case "3":
                                    durum = sayacdurum.durumadi + ". Onay Formu Gönderimi Bekliyor.";
                                    break;
                                case "4":
                                    durum = sayacdurum.durumadi + ". Müşteri Onayı Bekliyor.";
                                    break;
                                case "5":
                                    durum = sayacdurum.durumadi + ". Kalibrasyon ya da Depo Teslimi Bekliyor.";
                                    break;
                                case "6":
                                    durum = sayacdurum.durumadi + ". Tekrar Fiyatlandırma Bekliyor.";
                                    break;
                                case "7":
                                    durum = sayacdurum.durumadi + ". Onay Formu Gönderimi Bekliyor.";
                                    break;
                                case "8":
                                    durum = sayacdurum.durumadi + ". Depo Teslimi Bekliyor";
                                    break;
                                case "9":
                                    durum = sayacdurum.durumadi + ".";
                                    break;
                                case "10":
                                    durum = sayacdurum.durumadi + ".";
                                    break;
                                case "11":
                                    durum = sayacdurum.durumadi + ".";
                                    break;
                                case "12":
                                    durum = sayacdurum.durumadi + ".";
                                    break;
                                default:
                                    durum = sayacdurum.durumadi + ".";
                                    break;
                            }
                            var sonislemtarihi =  new Date(servistakip.sonislemtarihi);
                            sonislemtarihi.setDate(sonislemtarihi.getDate() + 7);
                            if((servistakip.depoteslim_id !=null || servistakip.aboneteslim_id !=null || servistakip.depolararasi_id !=null )&& (sonislemtarihi< new Date())){ // depo teslimi abone teslimi gibi işlemler 7 güne kadar iptal edilebilir.
                                $('.buttons').append('<h3 style="color:red;margin-top:5px"><b>7 Gün Öncesine Kadar Depo Teslimi, Depolararası ve Abone Teslimi İşlemi <br>Yapılan Sayaçlar' +
                                    ' için İşlemler Geri Alınabilir.</br></h3>');
                            }else{
                                var buttons = '<div class="form-group col-xs-12" style="text-align:center">' +
                                    '<a class="btn btn-sm btn-info cariduzenle" href="#cariduzenle" data-toggle="modal"> Cari Bilgi Değiştir </a>' +
                                    '<a class="btn btn-sm btn-info uretimyerduzenle" href="#uretimyerduzenle" data-toggle="modal"> Geliş Yeri Değiştir </a>' +
                                    '<a class="btn btn-sm btn-info serinoduzenle" href="#serinoduzenle" data-toggle="modal"> Seri No Değiştir </a>' +
                                    '<a class="btn btn-sm btn-info sayacadiduzenle" href="#sayacadiduzenle" data-toggle="modal"> Sayaç Adı Değiştir </a>' +
                                    '</div>';
                                if (servistakip.durum>1)
                                buttons += '<div class="form-group col-xs-12" style="text-align:center">' +
                                    '<a class="btn btn-sm btn-warning islemgerial" href="#islemgerial" data-toggle="modal"> İşlem Geri Al</a>'+
                                    '</div>';
                                $('.buttons').append(buttons);
                            }
                            var newRow = '<div class="panel-group accordion col-xs-12" id="accordion">' +
                                '<div class="panel panel-default servistakip_ek"><div class="panel-heading"><h4 class="panel-title">' +
                                '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_servistakip">Servis Takip Bilgisi ' + servistakip.id + '</a></h4></div>' +
                                '<div id="collapse_servistakip" class="panel-collapse in"><div class="panel-body">' +
                                '<div class="form-group col-xs-12"><label class="col-xs-4 col-sm-2 control-label">Cari Bilgisi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.netsiscari.cariadi + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Geliş Yeri:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.uretimyer.yeradi + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Son Durumu:</label><label class="col-xs-8" style="padding-top: 8px">' + durum + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Seri No:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.serino + (servistakip.eskiserino !== null ? '(' + servistakip.eskiserino + ')' : '') + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Adı:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.sayacadi.sayacadi + '</label></div>' +
                                '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Servis:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.servis.servisadi + '</label></div>' +
                                '</div></div></div>';
                            if (servistakip.depogelen)
                                newRow += '<div class="panel panel-default depogelen_ek"><div class="panel-heading"><h4 class="panel-title">' +
                                    '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_depogelen">Depo Gelen Bilgisi ' + servistakip.depogelen.id + '</a></h4></div>' +
                                    '<div id="collapse_depogelen" class="panel-collapse in"><div class="panel-body">' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Belge No:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depogelen.fisno + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Geliş Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + $.datepicker.formatDate('dd-mm-yy', new Date(servistakip.depogelen.tarih)) + '</label></div>' +
                                    '<div class="form-group col-xs-12"><label class="col-xs-4 col-sm-2 control-label">Stok Kodu:</label><label class="col-xs-8 col-sm-10" style="padding-top: 8px">' + servistakip.depogelen.servisstokkodu + ' - ' + servistakip.depogelen.servisstokkod.stokadi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Cari Kodu:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depogelen.carikod + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Adedi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depogelen.adet + '</label></div>' +
                                    '<div class="form-group col-xs-12"><label class="col-xs-4 col-sm-2 control-label">Sayaçlar:</label><label class="col-xs-8 col-sm-10" style="padding-top: 8px">' + servistakip.depogelen.sayaclar + '</label></div>' +
                                    '</div></div></div>';
                            if (servistakip.sayacgelen)
                                newRow += '<div class="panel panel-default sayacgelen_ek"><div class="panel-heading"><h4 class="panel-title">' +
                                    '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_sayacgelen">Sayaç Gelen Bilgisi ' + servistakip.sayacgelen.id + '</a></h4></div>' +
                                    '<div id="collapse_sayacgelen" class="panel-collapse in"><div class="panel-body">' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Seri No:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.sayacgelen.serino + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Adı:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.sayacgelen.sayacadi.sayacadi + (servistakip.sayacgelen.sayaccap_id !== "1" ? ' ' + servistakip.sayacgelen.sayaccap.capadi : '') + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Geliş Yeri:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.uretimyer.yeradi + '</label></div>' +
                                    '</div></div></div>';
                            if (servistakip.arizakayit) {
                                newRow += '<div class="panel panel-default sayac_ek"><div class="panel-heading"><h4 class="panel-title">' +
                                    '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_sayac">Sayaç Bilgisi ' + servistakip.sayac.id + '</a></h4></div>' +
                                    '<div id="collapse_sayac" class="panel-collapse in"><div class="panel-body">' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Seri No:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.sayac.serino + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Adı:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.sayac.sayacadi_id !== null ? servistakip.sayacadi.sayacadi + (servistakip.sayac.sayaccap_id !== "1" ? ' ' + servistakip.sayac.sayaccap.capadi : '') : '') + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Üretim Yeri:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.sayac.uretimyer.yeradi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Üretim Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.sayac.uretimtarihi !== null ? $.datepicker.formatDate('dd-mm-yy', new Date(servistakip.sayac.uretimtarihi)) : '') + '</label></div>' +
                                    '</div></div></div>';
                                newRow += '<div class="panel panel-default arizakayit_ek"><div class="panel-heading"><h4 class="panel-title">' +
                                    '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_arizakayit">Arıza Kayıt Bilgisi ' + servistakip.arizakayit.id + '</a></h4></div>' +
                                    '<div id="collapse_arizakayit" class="panel-collapse in"><div class="panel-body">' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Garanti Durumu:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.arizakayit.garanti === "1" ? 'İÇİNDE' : 'DIŞINDA') + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Seri No Değişimi:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.arizakayit.serinodegisim === "1" ? 'VAR' : 'YOK' ) + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Kayıt Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + $.datepicker.formatDate('dd-mm-yy', new Date(servistakip.arizakayit.arizakayittarihi)) + '</label></div>' +
                                    '</div></div></div>';
                            }
                            if (servistakip.arizafiyat)
                                newRow += '<div class="panel panel-default arizafiyat_ek"><div class="panel-heading"><h4 class="panel-title">' +
                                    '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_arizafiyat">Arıza Fiyat Bilgisi ' + servistakip.arizafiyat.id + '</a></h4></div>' +
                                    '<div id="collapse_arizafiyat" class="panel-collapse in"><div class="panel-body">' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Seri No:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.arizafiyat.ariza_serino + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Adı:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.arizafiyat.sayacadi.sayacadi + (servistakip.arizafiyat.sayaccap_id !== "1" ? ' ' + servistakip.arizafiyat.sayaccap.capadi : '') + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Garanti Durumu:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.arizafiyat.ariza_garanti === "1" ? 'İÇİNDE' : 'DIŞINDA') + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Fiyat Durumu:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.arizafiyat.fiyatdurum === "1" ? 'ÖZEL' : 'GENEL') + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Geliş Yeri:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.arizafiyat.uretimyer.yeradi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Toplam Tutarı:</label><label class="col-xs-8" style="padding-top: 8px">' + Number(servistakip.arizafiyat.toplamtutar).toFixed(2) + ' ' + servistakip.arizafiyat.parabirimi.birimi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">İndirim:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.arizafiyat.indirim === "1" ? 'VAR' : 'YOK') + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">İndirim Oranı:</label><label class="col-xs-8" style="padding-top: 8px">' + '%' + Number(servistakip.arizafiyat.indirimorani).toFixed(2) + '</label></div>' +
                                    '<div class="form-group col-xs-12"><label class="col-xs-4 col-sm-2 control-label">Değişen Parçalar:</label><label class="col-xs-8 col-sm-10" style="padding-top: 8px">' + servistakip.arizafiyat.parcalar + '</label></div>' +
                                    '</div></div></div>';
                            if (servistakip.ucretlendirilen)
                                newRow += '<div class="panel panel-default ucretlendirilen_ek"><div class="panel-heading"><h4 class="panel-title">' +
                                    '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_ucretlendirilen">Ücretlendirilen Bilgisi ' + servistakip.ucretlendirilen.id + '</a></h4></div>' +
                                    '<div id="collapse_ucretlendirilen" class="panel-collapse in"><div class="panel-body">' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Cari Bilgisi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.ucretlendirilen.netsiscari.cariadi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Geliş Yeri:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.ucretlendirilen.uretimyer.yeradi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Sayısı:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.ucretlendirilen.sayacsayisi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Durum:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.ucretlendirilen.gdurum + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Toplam Tutarı:</label><label class="col-xs-8" style="padding-top: 8px">' + Number(servistakip.ucretlendirilen.fiyat).toFixed(2) + ' ' + servistakip.ucretlendirilen.parabirimi.birimi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Mail Durumu:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.ucretlendirilen.gmail + '</label></div>' +
                                    '<div class="form-group col-xs-12"><label class="col-xs-4 col-sm-2 control-label">Sayaçlar:</label><label class="col-xs-8 col-sm-10" style="padding-top: 8px">' + servistakip.ucretlendirilen.sayaclar + '</label></div>' +
                                    '</div></div></div>';
                            if (servistakip.onaylanan)
                                newRow += '<div class="panel panel-default onaylanan_ek"><div class="panel-heading"><h4 class="panel-title">' +
                                    '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_onaylanan">Onaylanan Bilgisi ' + servistakip.onaylanan.id + '</a></h4></div>' +
                                    '<div id="collapse_onaylanan" class="panel-collapse in"><div class="panel-body">' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Onaylayan:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.onaylanan.yetkili.kullanici ? servistakip.onaylanan.yetkili.kullanici.adi_soyadi : '') + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Onay Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + $.datepicker.formatDate('dd-mm-yy', new Date(servistakip.onaylanan.onaytarihi)) + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Onaylama Tipi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.onaylanan.gonaylamatipi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Onay Formu:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.onaylanan.onayformu !== null ? servistakip.onaylanan.onayformu : 'YOK') + '</label></div>' +
                                    '</div></div></div>';
                            if (servistakip.kalibrasyon)
                                newRow += '<div class="panel panel-default kalibrasyon_ek"><div class="panel-heading"><h4 class="panel-title">' +
                                    '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_kalibrasyon">Kalibrasyon Bilgisi ' + servistakip.kalibrasyon.id + '</a></h4></div>' +
                                    '<div id="collapse_kalibrasyon" class="panel-collapse in"><div class="panel-body">' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Seri No:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.kalibrasyon.kalibrasyon_seri + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Adı:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.kalibrasyon.sayacadi.sayacadi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">İmal Yılı:</label><label class="col-xs-8" style="padding-top: 8px">' + $.datepicker.formatDate('yy', new Date(servistakip.kalibrasyon.imalyili)) + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">İstasyon:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.kalibrasyon.istasyon ? servistakip.kalibrasyon.istasyon.istasyonadi : '') + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Kalibrasyon Sayısı:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.kalibrasyon.kalibrasyonsayisi + '. Kalibrasyon' + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Durum:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.kalibrasyon.gdurum + '</label></div>' +
                                    '</div></div></div>';
                            if (servistakip.depoteslim)
                                newRow += '<div class="panel panel-default depoteslim_ek"><div class="panel-heading"><h4 class="panel-title">' +
                                    '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_depoteslim">Depo Teslim Bilgisi ' + servistakip.depoteslim.id + '</a></h4></div>' +
                                    '<div id="collapse_depoteslim" class="panel-collapse in"><div class="panel-body">' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Cari Bilgisi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depoteslim.netsiscari.cariadi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Sayısı:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depoteslim.sayacsayisi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Durum:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depoteslim.gdepodurum + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Teslim Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depoteslim.gteslimtarihi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Fatura No:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.depoteslim.faturano !== null ? servistakip.depoteslim.faturano : '' ) + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Fatura Adresi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depoteslim.faturaadres + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Teslim Adresi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depoteslim.teslimadres + '</label></div>' +
                                    '<div class="form-group col-xs-12"><label class="col-xs-4 col-sm-2 control-label">Sayaçlar:</label><label class="col-xs-8 col-sm-10" style="padding-top: 8px">' + servistakip.depoteslim.sayaclar + '</label></div>' +
                                    '</div></div></div>';
                            if (servistakip.hurda)
                                newRow += '<div class="panel panel-default hurda_ek"><div class="panel-heading"><h4 class="panel-title">' +
                                    '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_hurda">Hurda Bilgisi ' + servistakip.hurda.id + '</a></h4></div>' +
                                    '<div id="collapse_hurda" class="panel-collapse in"><div class="panel-body">' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Hurda Nedeni:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.hurda.hurdanedeni.nedeni + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Hurda Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.hurda.ghurdatarihi + '</label></div>' +
                                    '</div></div></div>';
                            if (servistakip.depolararasi)
                                newRow += '<div class="panel panel-default depolararasi_ek"><div class="panel-heading"><h4 class="panel-title">' +
                                    '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_depolararasi">Depolararası Bilgisi ' + servistakip.depolararasi.id + '</a></h4></div>' +
                                    '<div id="collapse_depolararasi" class="panel-collapse in"><div class="panel-body">' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Cari Bilgisi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depolararasi.netsiscari.cariadi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Sayısı:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depolararasi.sayacsayisi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Durum:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depolararasi.gdurum + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Teslim Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depolararasi.gteslimtarihi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Fatura No:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.depolararasi.faturano !== null ? servistakip.depolararasi.faturano : '' ) + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Fatura Adresi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depolararasi.faturaadres + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Teslim Adresi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depolararasi.teslimadres + '</label></div>' +
                                    '<div class="form-group col-xs-12"><label class="col-xs-4 col-sm-2 control-label">Sayaçlar:</label><label class="col-xs-8 col-sm-10" style="padding-top: 8px">' + servistakip.depolararasi.sayaclar + '</label></div>' +
                                    '</div></div></div>';
                            if (servistakip.aboneteslim)
                                newRow += '<div class="panel panel-default aboneteslim_ek"><div class="panel-heading"><h4 class="panel-title">' +
                                    '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_aboneteslim">Abone Teslim Bilgisi ' + servistakip.aboneteslim.id + '</a></h4></div>' +
                                    '<div id="collapse_aboneteslim" class="panel-collapse in"><div class="panel-body">' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Cari Bilgisi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.aboneteslim.netsiscari.cariadi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Sayısı:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.aboneteslim.sayacsayisi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Durum:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.aboneteslim.gdurum + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Teslim Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.aboneteslim.gteslimtarihi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Fatura No:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.aboneteslim.faturano !== null ? servistakip.aboneteslim.faturano : '' ) + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Toplam Tutarı:</label><label class="col-xs-8" style="padding-top: 8px">' + Number(servistakip.aboneteslim.toplamtutar).toFixed(2) + ' ' + servistakip.aboneteslim.parabirimi.birimi + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Fatura Adresi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.aboneteslim.faturaadres + '</label></div>' +
                                    '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Teslim Adresi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.aboneteslim.teslimadres + '</label></div>' +
                                    '<div class="form-group col-xs-12"><label class="col-xs-4 col-sm-2 control-label">Sayaçlar:</label><label class="col-xs-8 col-sm-10" style="padding-top: 8px">' + servistakip.aboneteslim.sayaclar + '</label></div>' +
                                    '</div></div></div>';
                            newRow += '</div>';
                            $('.bilgi').append(newRow);
                            $('.cariduzenle').click(function() {
                                $.blockUI();
                                var id = $('#servistakipid').val();
                                $('#cariduzenleservistakipid').val(id);
                                $.getJSON("{{ URL::to('digerdatabase/genelbilgi') }}", {id: id}, function (event) {
                                    if (event.durum) {
                                        var servistakip = event.servistakip;
                                        var netsiscariler = event.netsiscariler;
                                        $('.cariduzenlecariadi').html(servistakip.netsiscari.carikod+' - '+servistakip.netsiscari.cariadi);
                                        $("#cariduzenleyenicariadi").empty();
                                        $("#cariduzenleyenicariadi").append('<option value="">Seçiniz...</option>');
                                        $.each(netsiscariler, function (index) {
                                            $("#cariduzenleyenicariadi").append('<option value="' + netsiscariler[index].id + '"> ' + netsiscariler[index].carikod + ' - ' + netsiscariler[index].cariadi + '</option>');
                                        });
                                        $("#cariduzenleyenicariadi").select2();
                                    } else {
                                        $('#cariduzenle').modal('hide');
                                        toastr[event.type](event.text, event.title);
                                    }
                                    $.unblockUI();
                                });
                            });
                            $('.sayacadiduzenle').click(function(){
                                $.blockUI();
                                var id = $('#servistakipid').val();
                                $('#sayacadiduzenleservistakipid').val(id);
                                $.getJSON("{{ URL::to('digerdatabase/genelbilgi') }}", {id: id}, function (event) {
                                    if (event.durum) {
                                        var servistakip = event.servistakip;
                                        var sayacadlari = event.sayacadlari;
                                        var sayaccaplari = event.sayaccaplari;
                                        $('.sayacadiduzenlesayacadi').html(servistakip.sayacadi.sayacadi + (servistakip.sayaccap.capadi !== " " ? ' - ' + servistakip.sayaccap.capadi : ''));
                                        $("#sayacadiduzenleyenisayacadi").empty();
                                        $("#sayacadiduzenleyenisayacadi").append('<option value="">Seçiniz...</option>');
                                        $.each(sayacadlari, function (index) {
                                            $("#sayacadiduzenleyenisayacadi").append('<option data-id="'+  sayacadlari[index].cap +'" value="' + sayacadlari[index].id + '"> ' + sayacadlari[index].sayacadi+ '</option>');
                                        });
                                        $("#sayacadiduzenleyenisayacadi").select2();
                                        if (servistakip.servis_id === '1' || servistakip.servis_id === '4' || servistakip.servis_id === '6') {
                                            $('.sayaccapdurum').removeClass('hide');
                                            $.each(sayaccaplari, function (index) {
                                                $("#sayacadiduzenleyenisayaccap").append('<option value="' + sayaccaplari[index].id + '"> ' + sayaccaplari[index].capadi + '</option>');
                                            });
                                            $("#sayacadiduzenleyenisayaccap").select2();
                                            $('#sayacadiduzenleyenisayacadi').on('change', function () {
                                                var id = $(this).val();
                                                if(id!==""){
                                                    var capdurum = $(this).find("option:selected").data('id');
                                                    if (capdurum === 0) //cap kontrol edilmiyor
                                                    {
                                                        $("#sayacadiduzenleyenisayaccap").select2("val",1);
                                                        $("#sayacadiduzenleyenisayaccap").prop("disabled", true);
                                                    } else {
                                                        $("#sayacadiduzenleyenisayaccap").select2("val","");
                                                        $("#sayacadiduzenleyenisayaccap").prop("disabled", false);
                                                    }
                                                }else{
                                                    $("#sayacadiduzenleyenisayaccap").select2("val","");
                                                    $("#sayacadiduzenleyenisayaccap").prop("disabled", true);
                                                }
                                                $(this).valid();
                                            });
                                        }
                                    } else {
                                        $('#sayacadiduzenle').modal('hide');
                                        toastr[event.type](event.text, event.title);
                                    }
                                    $.unblockUI();
                                });
                            });
                            $('.serinoduzenle').click(function(){
                                $.blockUI();
                                var id = $('#servistakipid').val();
                                $('#serinoduzenleservistakipid').val(id);
                                var serino = $('#tekkriter').val();
                                $('.serinoduzenleserino').html(serino);
                                $.unblockUI();
                            });
                            $('.uretimyerduzenle').click(function(){
                                $.blockUI();
                                var id = $('#servistakipid').val();
                                $('#uretimyeriduzenleservistakipid').val(id);
                                $.getJSON("{{ URL::to('digerdatabase/genelbilgi') }}", {id: id}, function (event) {
                                    if (event.durum) {
                                        var servistakip = event.servistakip;
                                        var uretimyerleri = event.uretimyerleri;
                                        $('.uretimyeriduzenleyeradi').html(servistakip.uretimyer.yeradi);
                                        $("#uretimyeriduzenleyeniyeradi").empty();
                                        $("#uretimyeriduzenleyeniyeradi").append('<option value="">Seçiniz...</option>');
                                        $.each(uretimyerleri, function (index) {
                                            $("#uretimyeriduzenleyeniyeradi").append('<option value="' + uretimyerleri[index].id + '"> ' + uretimyerleri[index].yeradi+ '</option>');
                                        });
                                        $("#uretimyeriduzenleyeniyeradi").select2();
                                    } else {
                                        $('#uretimyeriduzenle').modal('hide');
                                        toastr[event.type](event.text, event.title);
                                    }
                                    $.unblockUI();
                                });
                            });
                            $('.islemgerial').click(function() {
                                $.blockUI();
                                var id = $('#servistakipid').val();
                                $('#islemgerialservistakipid').val(id);
                                $.getJSON("{{ URL::to('digerdatabase/genelbilgi') }}", {id: id}, function (event) {
                                    if (event.durum) {
                                        var servistakip = event.servistakip;
                                        $('.islemgerialsondurum').html(durum);
                                        $("#islemgerialislemadi").empty();
                                        $("#islemgerialislemadi").append('<option value="">Seçiniz...</option>');
                                        if(servistakip.durum>1){
                                            $("#islemgerialislemadi").append('<option value="1"> Arıza Kayıdı Bekliyor </option>');
                                        }
                                        if(servistakip.durum>2){
                                            $("#islemgerialislemadi").append('<option value="2"> Fiyatlandırma Bekliyor </option>');
                                        }
                                        if(servistakip.durum>3){
                                            $("#islemgerialislemadi").append('<option value="3"> Ücretlendirme Gönderilmeyi Bekliyor </option>');
                                        }
                                        if(servistakip.durum>4){
                                            $("#islemgerialislemadi").append('<option value="4"> Müşteri Onayı Bekliyor </option>');
                                        }
                                        if(servistakip.durum>7 && servistakip.servis_id==="5"){
                                            $("#islemgerialislemadi").append('<option value="5"> Kalibrasyon Bekliyor </option>');
                                        }
                                        if(servistakip.durum>8 && servistakip.durum<11){
                                            $("#islemgerialislemadi").append('<option value="6"> Depo Teslimi Bekliyor </option>');
                                        }
                                        $("#islemgerialislemadi").select2();
                                    } else {
                                        $('#islemgerial').modal('hide');
                                        toastr[event.type](event.text, event.title);
                                    }
                                    $.unblockUI();
                                });
                            });

                            $("select").on("select2-close", function () {
                                $(this).valid();
                            });
                        }
                    } else {
                        $('#servistakipid').val(0);
                        toastr[event.type](event.text,event.title);
                    }
                    $.unblockUI();
                });
            }else{
                $('#servistakipid').val(0);
                toastr['warning']('Kriter Bilgisi Girilmemiş', 'Kriter Hatası');
            }
        });
        $('#listekapat').click(function () {
            $('#sayaclistesi').modal('hide');
        });
        $('#listesec').click(function () {
            var servistakipid = $('#sample_1 .active .id').text();
            $.blockUI();
            $.getJSON("{{ URL::to('digerdatabase/islemgelenbilgi') }}",{servistakipid:servistakipid}, function (event) {
                if (event.durum) {
                    var servistakip = event.servistakip;
                    var sayacdurum = servistakip.sayacdurum;
                    $('#servistakipid').val(servistakip.id);
                    var durum = "";
                    switch (servistakip.durum) {
                        case "1":
                            durum = sayacdurum.durumadi + ". Arıza Kayıdı ya da Kalibrasyon Bekliyor.";
                            break;
                        case "2":
                            durum = sayacdurum.durumadi + ". Fiyatlandırma Bekliyor.";
                            break;
                        case "3":
                            durum = sayacdurum.durumadi + ". Onay Formu Gönderimi Bekliyor.";
                            break;
                        case "4":
                            durum = sayacdurum.durumadi + ". Müşteri Onayı Bekliyor.";
                            break;
                        case "5":
                            durum = sayacdurum.durumadi + ". Kalibrasyon ya da Depo Teslimi Bekliyor.";
                            break;
                        case "6":
                            durum = sayacdurum.durumadi + ". Tekrar Fiyatlandırma Bekliyor.";
                            break;
                        case "7":
                            durum = sayacdurum.durumadi + ". Onay Formu Gönderimi Bekliyor.";
                            break;
                        case "8":
                            durum = sayacdurum.durumadi + ". Depo Teslimi Bekliyor";
                            break;
                        case "9":
                            durum = sayacdurum.durumadi + ".";
                            break;
                        case "10":
                            durum = sayacdurum.durumadi + ".";
                            break;
                        case "11":
                            durum = sayacdurum.durumadi + ".";
                            break;
                        case "12":
                            durum = sayacdurum.durumadi + ".";
                            break;
                        default:
                            durum = sayacdurum.durumadi + ".";
                            break;
                    }
                    var sonislemtarihi =  new Date(servistakip.sonislemtarihi);
                    sonislemtarihi.setDate(sonislemtarihi.getDate() + 7);
                    if((servistakip.depoteslim_id !=null || servistakip.aboneteslim_id !=null || servistakip.depolararasi_id !=null )&& (sonislemtarihi< new Date())){ // depo teslimi abone teslimi gibi işlemler 7 güne kadar iptal edilebilir.
                        $('.buttons').append('<h3 style="color:red;margin-top:5px"><b>7 Gün Öncesine Kadar Depo Teslimi, Depolararası ve Abone Teslimi İşlemi <br>Yapılan Sayaçlar' +
                            ' için İşlemler Geri Alınabilir.</br></h3>');
                    }else {
                        var buttons = '<div class="form-group col-xs-12" style="text-align:center">' +
                            '<a class="btn btn-sm btn-info cariduzenle" href="#cariduzenle" data-toggle="modal"> Cari Bilgi Değiştir </a>' +
                            '<a class="btn btn-sm btn-info uretimyerduzenle" href="#uretimyerduzenle" data-toggle="modal"> Geliş Yeri Değiştir </a>' +
                            '<a class="btn btn-sm btn-info serinoduzenle" href="#serinoduzenle" data-toggle="modal"> Seri No Değiştir </a>' +
                            '<a class="btn btn-sm btn-info sayacadiduzenle" href="#sayacadiduzenle" data-toggle="modal"> Sayaç Adı Değiştir </a>' +
                            '</div>';
                        if (servistakip.durum>1)
                        buttons += '<div class="form-group col-xs-12" style="text-align:center">' +
                            '<a class="btn btn-sm btn-warning islemgerial" href="#islemgerial" data-toggle="modal"> İşlem Geri Al</a>'+
                            '</div>';
                        $('.buttons').append(buttons);
                    }
                    var newRow = '<div class="panel-group accordion col-xs-12" id="accordion">' +
                        '<div class="panel panel-default servistakip_ek"><div class="panel-heading"><h4 class="panel-title">' +
                        '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_servistakip">Servis Takip Bilgisi ' + servistakip.id + '</a></h4></div>' +
                        '<div id="collapse_servistakip" class="panel-collapse in"><div class="panel-body">' +
                        '<div class="form-group col-xs-12"><label class="col-xs-4 col-sm-2 control-label">Cari Bilgisi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.netsiscari.cariadi + '</label></div>' +
                        '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Geliş Yeri:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.uretimyer.yeradi + '</label></div>' +
                        '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Son Durumu:</label><label class="col-xs-8" style="padding-top: 8px">' + durum + '</label></div>' +
                        '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Seri No:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.serino + (servistakip.eskiserino !== null ? '(' + servistakip.eskiserino + ')' : '') + '</label></div>' +
                        '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Adı:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.sayacadi.sayacadi + '</label></div>' +
                        '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Servis:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.servis.servisadi + '</label></div>' +
                        '</div></div></div>';
                    if (servistakip.depogelen)
                        newRow += '<div class="panel panel-default depogelen_ek"><div class="panel-heading"><h4 class="panel-title">' +
                            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_depogelen">Depo Gelen Bilgisi ' + servistakip.depogelen.id + '</a></h4></div>' +
                            '<div id="collapse_depogelen" class="panel-collapse in"><div class="panel-body">' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Belge No:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depogelen.fisno + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Geliş Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + $.datepicker.formatDate('dd-mm-yy', new Date(servistakip.depogelen.tarih)) + '</label></div>' +
                            '<div class="form-group col-xs-12"><label class="col-xs-4 col-sm-2 control-label">Stok Kodu:</label><label class="col-xs-8 col-sm-10" style="padding-top: 8px">' + servistakip.depogelen.servisstokkodu + ' - ' + servistakip.depogelen.servisstokkod.stokadi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Cari Kodu:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depogelen.carikod + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Adedi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depogelen.adet + '</label></div>' +
                            '<div class="form-group col-xs-12"><label class="col-xs-4 col-sm-2 control-label">Sayaçlar:</label><label class="col-xs-8 col-sm-10" style="padding-top: 8px">' + servistakip.depogelen.sayaclar + '</label></div>' +
                            '</div></div></div>';
                    if (servistakip.sayacgelen)
                        newRow += '<div class="panel panel-default sayacgelen_ek"><div class="panel-heading"><h4 class="panel-title">' +
                            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_sayacgelen">Sayaç Gelen Bilgisi ' + servistakip.sayacgelen.id + '</a></h4></div>' +
                            '<div id="collapse_sayacgelen" class="panel-collapse in"><div class="panel-body">' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Seri No:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.sayacgelen.serino + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Adı:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.sayacgelen.sayacadi.sayacadi + (servistakip.sayacgelen.sayaccap_id !== "1" ? ' ' + servistakip.sayacgelen.sayaccap.capadi : '') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Geliş Yeri:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.uretimyer.yeradi + '</label></div>' +
                            '</div></div></div>';
                    if (servistakip.arizakayit) {
                        newRow += '<div class="panel panel-default sayac_ek"><div class="panel-heading"><h4 class="panel-title">' +
                            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_sayac">Sayaç Bilgisi ' + servistakip.sayac.id + '</a></h4></div>' +
                            '<div id="collapse_sayac" class="panel-collapse in"><div class="panel-body">' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Seri No:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.sayac.serino + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Adı:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.sayac.sayacadi_id !== null ? servistakip.sayacadi.sayacadi + (servistakip.sayac.sayaccap_id !== "1" ? ' ' + servistakip.sayac.sayaccap.capadi : '') : '') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Üretim Yeri:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.sayac.uretimyer.yeradi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Üretim Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.sayac.uretimtarihi !== null ? $.datepicker.formatDate('dd-mm-yy', new Date(servistakip.sayac.uretimtarihi)) : '') + '</label></div>' +
                            '</div></div></div>';
                        newRow += '<div class="panel panel-default arizakayit_ek"><div class="panel-heading"><h4 class="panel-title">' +
                            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_arizakayit">Arıza Kayıt Bilgisi ' + servistakip.arizakayit.id + '</a></h4></div>' +
                            '<div id="collapse_arizakayit" class="panel-collapse in"><div class="panel-body">' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Garanti Durumu:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.arizakayit.garanti === "1" ? 'İÇİNDE' : 'DIŞINDA') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Seri No Değişimi:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.arizakayit.serinodegisim === "1" ? 'VAR' : 'YOK' ) + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Kayıt Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + $.datepicker.formatDate('dd-mm-yy', new Date(servistakip.arizakayit.arizakayittarihi)) + '</label></div>' +
                            '</div></div></div>';
                    }
                    if (servistakip.arizafiyat)
                        newRow += '<div class="panel panel-default arizafiyat_ek"><div class="panel-heading"><h4 class="panel-title">' +
                            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_arizafiyat">Arıza Fiyat Bilgisi ' + servistakip.arizafiyat.id + '</a></h4></div>' +
                            '<div id="collapse_arizafiyat" class="panel-collapse in"><div class="panel-body">' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Seri No:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.arizafiyat.ariza_serino + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Adı:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.arizafiyat.sayacadi.sayacadi + (servistakip.arizafiyat.sayaccap_id !== "1" ? ' ' + servistakip.arizafiyat.sayaccap.capadi : '') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Garanti Durumu:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.arizafiyat.ariza_garanti === "1" ? 'İÇİNDE' : 'DIŞINDA') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Fiyat Durumu:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.arizafiyat.fiyatdurum === "1" ? 'ÖZEL' : 'GENEL') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Geliş Yeri:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.arizafiyat.uretimyer.yeradi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Toplam Tutarı:</label><label class="col-xs-8" style="padding-top: 8px">' + Number(servistakip.arizafiyat.toplamtutar).toFixed(2) + ' ' + servistakip.arizafiyat.parabirimi.birimi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">İndirim:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.arizafiyat.indirim === "1" ? 'VAR' : 'YOK') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">İndirim Oranı:</label><label class="col-xs-8" style="padding-top: 8px">' + '%' + Number(servistakip.arizafiyat.indirimorani).toFixed(2) + '</label></div>' +
                            '<div class="form-group col-xs-12"><label class="col-xs-4 col-sm-2 control-label">Değişen Parçalar:</label><label class="col-xs-8 col-sm-10" style="padding-top: 8px">' + servistakip.arizafiyat.parcalar + '</label></div>' +
                            '</div></div></div>';
                    if (servistakip.ucretlendirilen)
                        newRow += '<div class="panel panel-default ucretlendirilen_ek"><div class="panel-heading"><h4 class="panel-title">' +
                            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_ucretlendirilen">Ücretlendirilen Bilgisi ' + servistakip.ucretlendirilen.id + '</a></h4></div>' +
                            '<div id="collapse_ucretlendirilen" class="panel-collapse in"><div class="panel-body">' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Cari Bilgisi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.ucretlendirilen.netsiscari.cariadi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Geliş Yeri:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.ucretlendirilen.uretimyer.yeradi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Sayısı:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.ucretlendirilen.sayacsayisi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Durum:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.ucretlendirilen.gdurum + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Toplam Tutarı:</label><label class="col-xs-8" style="padding-top: 8px">' + Number(servistakip.ucretlendirilen.fiyat).toFixed(2) + ' ' + servistakip.ucretlendirilen.parabirimi.birimi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Mail Durumu:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.ucretlendirilen.gmail + '</label></div>' +
                            '<div class="form-group col-xs-12"><label class="col-xs-4 col-sm-2 control-label">Sayaçlar:</label><label class="col-xs-8 col-sm-10" style="padding-top: 8px">' + servistakip.ucretlendirilen.sayaclar + '</label></div>' +
                            '</div></div></div>';
                    if (servistakip.onaylanan)
                        newRow += '<div class="panel panel-default onaylanan_ek"><div class="panel-heading"><h4 class="panel-title">' +
                            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_onaylanan">Onaylanan Bilgisi ' + servistakip.onaylanan.id + '</a></h4></div>' +
                            '<div id="collapse_onaylanan" class="panel-collapse in"><div class="panel-body">' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Onaylayan:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.onaylanan.yetkili.kullanici ? servistakip.onaylanan.yetkili.kullanici.adi_soyadi : '') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Onay Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + $.datepicker.formatDate('dd-mm-yy', new Date(servistakip.onaylanan.onaytarihi)) + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Onaylama Tipi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.onaylanan.gonaylamatipi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Onay Formu:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.onaylanan.onayformu !== null ? servistakip.onaylanan.onayformu : 'YOK') + '</label></div>' +
                            '</div></div></div>';
                    if (servistakip.kalibrasyon)
                        newRow += '<div class="panel panel-default kalibrasyon_ek"><div class="panel-heading"><h4 class="panel-title">' +
                            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_kalibrasyon">Kalibrasyon Bilgisi ' + servistakip.kalibrasyon.id + '</a></h4></div>' +
                            '<div id="collapse_kalibrasyon" class="panel-collapse in"><div class="panel-body">' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Seri No:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.kalibrasyon.kalibrasyon_seri + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Adı:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.kalibrasyon.sayacadi.sayacadi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">İmal Yılı:</label><label class="col-xs-8" style="padding-top: 8px">' + $.datepicker.formatDate('yy', new Date(servistakip.kalibrasyon.imalyili)) + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">İstasyon:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.kalibrasyon.istasyon ? servistakip.kalibrasyon.istasyon.istasyonadi : '') + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Kalibrasyon Sayısı:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.kalibrasyon.kalibrasyonsayisi + '. Kalibrasyon' + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Durum:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.kalibrasyon.gdurum + '</label></div>' +
                            '</div></div></div>';
                    if (servistakip.depoteslim)
                        newRow += '<div class="panel panel-default depoteslim_ek"><div class="panel-heading"><h4 class="panel-title">' +
                            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_depoteslim">Depo Teslim Bilgisi ' + servistakip.depoteslim.id + '</a></h4></div>' +
                            '<div id="collapse_depoteslim" class="panel-collapse in"><div class="panel-body">' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Cari Bilgisi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depoteslim.netsiscari.cariadi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Sayısı:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depoteslim.sayacsayisi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Durum:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depoteslim.gdepodurum + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Teslim Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depoteslim.gteslimtarihi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Fatura No:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.depoteslim.faturano !== null ? servistakip.depoteslim.faturano : '' ) + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Fatura Adresi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depoteslim.faturaadres + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Teslim Adresi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depoteslim.teslimadres + '</label></div>' +
                            '<div class="form-group col-xs-12"><label class="col-xs-4 col-sm-2 control-label">Sayaçlar:</label><label class="col-xs-8 col-sm-10" style="padding-top: 8px">' + servistakip.depoteslim.sayaclar + '</label></div>' +
                            '</div></div></div>';
                    if (servistakip.hurda)
                        newRow += '<div class="panel panel-default hurda_ek"><div class="panel-heading"><h4 class="panel-title">' +
                            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_hurda">Hurda Bilgisi ' + servistakip.hurda.id + '</a></h4></div>' +
                            '<div id="collapse_hurda" class="panel-collapse in"><div class="panel-body">' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Hurda Nedeni:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.hurda.hurdanedeni.nedeni + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Hurda Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.hurda.ghurdatarihi + '</label></div>' +
                            '</div></div></div>';
                    if (servistakip.depolararasi)
                        newRow += '<div class="panel panel-default depolararasi_ek"><div class="panel-heading"><h4 class="panel-title">' +
                            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_depolararasi">Depolararası Bilgisi ' + servistakip.depolararasi.id + '</a></h4></div>' +
                            '<div id="collapse_depolararasi" class="panel-collapse in"><div class="panel-body">' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Cari Bilgisi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depolararasi.netsiscari.cariadi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Sayısı:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depolararasi.sayacsayisi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Durum:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depolararasi.gdurum + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Teslim Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depolararasi.gteslimtarihi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Fatura No:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.depolararasi.faturano !== null ? servistakip.depolararasi.faturano : '' ) + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Fatura Adresi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depolararasi.faturaadres + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Teslim Adresi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.depolararasi.teslimadres + '</label></div>' +
                            '<div class="form-group col-xs-12"><label class="col-xs-4 col-sm-2 control-label">Sayaçlar:</label><label class="col-xs-8 col-sm-10" style="padding-top: 8px">' + servistakip.depolararasi.sayaclar + '</label></div>' +
                            '</div></div></div>';
                    if (servistakip.aboneteslim)
                        newRow += '<div class="panel panel-default aboneteslim_ek"><div class="panel-heading"><h4 class="panel-title">' +
                            '<a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapse_aboneteslim">Abone Teslim Bilgisi ' + servistakip.aboneteslim.id + '</a></h4></div>' +
                            '<div id="collapse_aboneteslim" class="panel-collapse in"><div class="panel-body">' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Cari Bilgisi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.aboneteslim.netsiscari.cariadi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Sayaç Sayısı:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.aboneteslim.sayacsayisi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Durum:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.aboneteslim.gdurum + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Teslim Tarihi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.aboneteslim.gteslimtarihi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Fatura No:</label><label class="col-xs-8" style="padding-top: 8px">' + (servistakip.aboneteslim.faturano !== null ? servistakip.aboneteslim.faturano : '' ) + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Toplam Tutarı:</label><label class="col-xs-8" style="padding-top: 8px">' + Number(servistakip.aboneteslim.toplamtutar).toFixed(2) + ' ' + servistakip.aboneteslim.parabirimi.birimi + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Fatura Adresi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.aboneteslim.faturaadres + '</label></div>' +
                            '<div class="form-group col-sm-6 col-xs-12"><label class="col-xs-4 control-label">Teslim Adresi:</label><label class="col-xs-8" style="padding-top: 8px">' + servistakip.aboneteslim.teslimadres + '</label></div>' +
                            '<div class="form-group col-xs-12"><label class="col-xs-4 col-sm-2 control-label">Sayaçlar:</label><label class="col-xs-8 col-sm-10" style="padding-top: 8px">' + servistakip.aboneteslim.sayaclar + '</label></div>' +
                            '</div></div></div>';
                    newRow += '</div>';
                    $('.bilgi').append(newRow);
                    $('.cariduzenle').click(function() {
                        $.blockUI();
                        var id = $('#servistakipid').val();
                        $('#cariduzenleservistakipid').val(id);
                        $.getJSON("{{ URL::to('digerdatabase/genelbilgi') }}", {id: id}, function (event) {
                            if (event.durum) {
                                var servistakip = event.servistakip;
                                var netsiscariler = event.netsiscariler;
                                $('.cariduzenlecariadi').html(servistakip.netsiscari.carikod+' - '+servistakip.netsiscari.cariadi);
                                $("#cariduzenleyenicariadi").empty();
                                $("#cariduzenleyenicariadi").append('<option value="">Seçiniz...</option>');
                                $.each(netsiscariler, function (index) {
                                    $("#cariduzenleyenicariadi").append('<option value="' + netsiscariler[index].id + '"> ' + netsiscariler[index].carikod + ' - ' + netsiscariler[index].cariadi + '</option>');
                                });
                                $("#cariduzenleyenicariadi").select2();
                            } else {
                                $('#cariduzenle').modal('hide');
                                toastr[event.type](event.text, event.title);
                            }
                            $.unblockUI();
                        });
                    });
                    $('.sayacadiduzenle').click(function(){
                        $.blockUI();
                        var id = $('#servistakipid').val();
                        $('#sayacadiduzenleservistakipid').val(id);
                        $.getJSON("{{ URL::to('digerdatabase/genelbilgi') }}", {id: id}, function (event) {
                            if (event.durum) {
                                var servistakip = event.servistakip;
                                var sayacadlari = event.sayacadlari;
                                var sayaccaplari = event.sayaccaplari;
                                $('.sayacadiduzenlesayacadi').html(servistakip.sayacadi.sayacadi+(servistakip.sayaccap.capadi!==" " ? ' - '+servistakip.sayaccap.capadi : ''));
                                $("#sayacadiduzenleyenisayacadi").empty();
                                $("#sayacadiduzenleyenisayacadi").append('<option value="">Seçiniz...</option>');
                                $.each(sayacadlari, function (index) {
                                    $("#sayacadiduzenleyenisayacadi").append('<option data-id="'+  sayacadlari[index].cap +'" value="' + sayacadlari[index].id + '"> ' + sayacadlari[index].sayacadi+ '</option>');
                                });
                                $("#sayacadiduzenleyenisayacadi").select2();
                                if (servistakip.servis_id === '1' || servistakip.servis_id === '4' || servistakip.servis_id === '6') {
                                    $('.sayaccapdurum').removeClass('hide');
                                    $.each(sayaccaplari, function (index) {
                                        $("#sayacadiduzenleyenisayaccap").append('<option value="' + sayaccaplari[index].id + '"> ' + sayaccaplari[index].capadi + '</option>');
                                    });
                                    $("#sayacadiduzenleyenisayaccap").select2();
                                    $('#sayacadiduzenleyenisayacadi').on('change', function () {
                                        var id = $(this).val();
                                        if(id!==""){
                                            var capdurum = $(this).find("option:selected").data('id');
                                            if (capdurum === 0) //cap kontrol edilmiyor
                                            {
                                                $("#sayacadiduzenleyenisayaccap").select2("val",1);
                                                $("#sayacadiduzenleyenisayaccap").prop("disabled", true);
                                            } else {
                                                $("#sayacadiduzenleyenisayaccap").select2("val","");
                                                $("#sayacadiduzenleyenisayaccap").prop("disabled", false);
                                            }
                                        }else{
                                            $("#sayacadiduzenleyenisayaccap").select2("val","");
                                            $("#sayacadiduzenleyenisayaccap").prop("disabled", true);
                                        }
                                        $(this).valid();
                                    });
                                }
                            } else {
                                $('#sayacadiduzenle').modal('hide');
                                toastr[event.type](event.text, event.title);
                            }
                            $.unblockUI();
                        });
                    });
                    $('.serinoduzenle').click(function(){
                        $.blockUI();
                        var id = $('#servistakipid').val();
                        $('#serinoduzenleservistakipid').val(id);
                        var serino = $('#tekkriter').val();
                        $('.serinoduzenleserino').html(serino);
                        $.unblockUI();
                    });
                    $('.uretimyerduzenle').click(function(){
                        $.blockUI();
                        var id = $('#servistakipid').val();
                        $('#uretimyeriduzenleservistakipid').val(id);
                        $.getJSON("{{ URL::to('digerdatabase/genelbilgi') }}", {id: id}, function (event) {
                            if (event.durum) {
                                var servistakip = event.servistakip;
                                var uretimyerleri = event.uretimyerleri;
                                $('.uretimyeriduzenleyeradi').html(servistakip.uretimyer.yeradi);
                                $("#uretimyeriduzenleyeniyeradi").empty();
                                $("#uretimyeriduzenleyeniyeradi").append('<option value="">Seçiniz...</option>');
                                $.each(uretimyerleri, function (index) {
                                    $("#uretimyeriduzenleyeniyeradi").append('<option value="' + uretimyerleri[index].id + '"> ' + uretimyerleri[index].yeradi+ '</option>');
                                });
                                $("#uretimyeriduzenleyeniyeradi").select2();
                            } else {
                                $('#uretimyeriduzenle').modal('hide');
                                toastr[event.type](event.text, event.title);
                            }
                            $.unblockUI();
                        });
                    });
                    $('.islemgerial').click(function() {
                        $.blockUI();
                        var id = $('#servistakipid').val();
                        $('#islemgerialservistakipid').val(id);
                        $.getJSON("{{ URL::to('digerdatabase/genelbilgi') }}", {id: id}, function (event) {
                            if (event.durum) {
                                var servistakip = event.servistakip;
                                $('.islemgerialsondurum').html(durum);
                                $("#islemgerialislemadi").empty();
                                $("#islemgerialislemadi").append('<option value="">Seçiniz...</option>');
                                if(servistakip.durum>1){
                                    $("#islemgerialislemadi").append('<option value="1"> Arıza Kayıdı Bekliyor </option>');
                                }
                                if(servistakip.durum>2){
                                    $("#islemgerialislemadi").append('<option value="2"> Fiyatlandırma Bekliyor </option>');
                                }
                                if(servistakip.durum>3){
                                    $("#islemgerialislemadi").append('<option value="3"> Ücretlendirme Gönderilmeyi Bekliyor </option>');
                                }
                                if(servistakip.durum>4){
                                    $("#islemgerialislemadi").append('<option value="4"> Müşteri Onayı Bekliyor </option>');
                                }
                                if(servistakip.durum>7 && servistakip.servis_id==="5"){
                                    $("#islemgerialislemadi").append('<option value="5"> Kalibrasyon Bekliyor </option>');
                                }
                                if(servistakip.durum>8 && servistakip.durum<11){
                                    $("#islemgerialislemadi").append('<option value="6"> Depo Teslimi Bekliyor </option>');
                                }
                                $("#islemgerialislemadi").select2();
                            } else {
                                $('#islemgerial').modal('hide');
                                toastr[event.type](event.text, event.title);
                            }
                            $.unblockUI();
                        });
                    });


                    $("select").on("select2-close", function () {
                        $(this).valid();
                    });

                } else {
                    $('#servistakipid').val(0);
                    toastr[event.type](event.text,event.title);
                }
                $.unblockUI();
            });
            $('#sayaclistesi').modal('hide');
        });
        $('#caridegistir').click(function(){
            $('#form_sample_1').submit();
        });
        $('#sayacadidegistir').click(function(){
            $('#form_sample_2').submit();
        });
        $('#serinodegistir').click(function(){
            $('#form_sample_3').submit();
        });
        $('#uretimyeridegistir').click(function(){
            $('#form_sample_4').submit();
        });
        $('#islemigerial').click(function(){
            $('#form_sample_5').submit();
        });
    });
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-edit"></i>İşlem Düzenle - Geri Al - Sil
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form id="form_sample" class="form-horizontal" novalidate="novalidate">
            <div class="form-body">
                <h3 class="form-section">Tek Sayaç Üzerinden Yapılacak İşlemler</h3>
                <div class="form-group">
                    <label class="control-label col-xs-2">Seri Numarası:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-3">
                        <i class="fa"></i><input type="text" id="tekkriter" name="tekkriter" data-required="1" class="form-control">
                    </div>
                    <div class="col-xs-4"><a class="btn green tekgetir">Bilgileri Getir</a></div>
                </div>
                <div class="form-group coksayacsecim hide">
                    <label class="control-label col-xs-2">Kriter:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-3">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="cokkriter" name="cokkriter" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            <option value="1">DepoGelen Id</option>
                            <option value="2">Ucretlendirilen Id</option>
                            <option value="3">DepoTeslim Id</option>
                        </select>
                    </div>
                    <div class="input-icon right col-xs-3">
                        <i class="fa"></i><input type="text" id="cokkriterdeger" name="cokkriterdeger" data-required="1" class="form-control">
                    </div>
                    <div class="col-xs-4"><a class="btn green cokgetir">Bilgileri Getir</a></div>
                </div>
                <div class="buttons col-xs-12" id="buttons">
                </div>
                <input class="hide" id="servistakipid" name="servistakipid"/>
                <input class="hide" id="infotype" name="infotype"/>
                <input class="hide" id="infovalue" name="infovalue"/>
                <div class="bilgi col-xs-12" id="bilgi">
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
        </form>
    </div>
</div>              
@stop

@section('modal')
    <div class="modal fade" id="sayaclistesi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-search"></i>Sistemdeki Sayaç Listesi
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <table class="table table-hover" id="sample_1">
                                    <thead>
                                    <tr><th class="table-checkbox"></th>
                                        <th class="hide">Id</th>
                                        <th>Seri No</th>
                                        <th>Üretim Yeri</th>
                                        <th>Sayaç Adı</th>
                                        <th>Sayaç Durumu</th>
                                        <th>Geliş Tarihi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-xs-12" style="text-align: center">
                                            <button type="button" id="listesec" class="btn green">Seç</button>
                                            <button type="button" id="listekapat" class="btn default">Vazgeç</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="cariduzenle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Cari Bilgisi Değiştirilecek?
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{URL::to('digerdatabase/caridegistir')}}" id="form_sample_1" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <div class="hide"><input type="text" name="cariduzenleservistakipid" id="cariduzenleservistakipid"/></div>
                                        <h3 class="form-section">Cari Bilgisi Seçilen Cari Bilgisi ile Değiştirilecek?</h3>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Netsis Cari Adı:</label>
                                            <label class="col-xs-8 cariduzenlecariadi" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Yeni Cari Adı:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><select class="form-control select2me select2-offscreen" id="cariduzenleyenicariadi" name="cariduzenleyenicariadi" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green" data-toggle="modal" data-target="#cariduzenleconfirm">Değiştir</button>
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
    <div class="modal fade" id="sayacadiduzenle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Sayaç Adı Değiştirilecek?
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{URL::to('digerdatabase/sayacadidegistir')}}" id="form_sample_2" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <div class="hide"><input type="text" name="sayacadiduzenleservistakipid" id="sayacadiduzenleservistakipid"/></div>
                                        <h3 class="form-section">Sayaç Adı Seçilen Sayaç Adı ile Değiştirilecek?</h3>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Sayaç Adı:</label>
                                            <label class="col-xs-8 sayacadiduzenlesayacadi" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Yeni Sayaç Adı:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><select class="form-control select2me select2-offscreen" id="sayacadiduzenleyenisayacadi" name="sayacadiduzenleyenisayacadi" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group sayaccapdurum hide">
                                            <label class="control-label col-sm-2 col-xs-4">Yeni Sayaç Çapı:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><select class="form-control select2me select2-offscreen" id="sayacadiduzenleyenisayaccap" name="sayacadiduzenleyenisayaccap" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green" data-toggle="modal" data-target="#sayacadiduzenleconfirm">Değiştir</button>
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
    <div class="modal fade" id="serinoduzenle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Seri Numarası Değiştirilecek?
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{URL::to('digerdatabase/serinodegistir')}}" id="form_sample_3" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <div class="hide"><input type="text" name="serinoduzenleservistakipid" id="serinoduzenleservistakipid"/></div>
                                        <h3 class="form-section">Seri Numarası Girilen Yeni Seri Numarası ile Değiştirilecek?</h3>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Seri Numarası:</label>
                                            <label class="col-xs-8 serinoduzenleserino" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Yeni Seri Numarası:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><input type="text" id="serinoduzenleyeniserino" name="serinoduzenleyeniserino" maxlength="20"  data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green" data-toggle="modal" data-target="#serinoduzenleconfirm">Değiştir</button>
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
    <div class="modal fade" id="uretimyerduzenle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Geliş Yeri Bilgisi Değiştirilecek?
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{URL::to('digerdatabase/uretimyeridegistir')}}" id="form_sample_4" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <div class="hide"><input type="text" name="uretimyeriduzenleservistakipid" id="uretimyeriduzenleservistakipid"/></div>
                                        <h3 class="form-section">Geliş Yeri Bilgisi Seçilen Geliş Yeri Bilgisi ile Değiştirilecek?</h3>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Geliş Yeri:</label>
                                            <label class="col-xs-8 uretimyeriduzenleyeradi" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Yeni Geliş Yeri:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><select class="form-control select2me select2-offscreen" id="uretimyeriduzenleyeniyeradi" name="uretimyeriduzenleyeniyeradi" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green" data-toggle="modal" data-target="#uretimyerduzenleconfirm">Değiştir</button>
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
    <div class="modal fade" id="islemgerial" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>İşlem Bilgisi Geri Alınacak?
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{URL::to('digerdatabase/islemgerial')}}" id="form_sample_5" method="POST" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <div class="hide"><input type="text" name="islemgerialservistakipid" id="islemgerialservistakipid"/></div>
                                        <h3 class="form-section">İşlem Bilgisi seçilen Kademeye Kadar Geri Alınacak?</h3>
                                        <div class="form-group col-xs-12">
                                            <label class="control-label col-sm-2 col-xs-4">Son Durum:</label>
                                            <label class="col-xs-8 islemgerialsondurum" style="padding-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Hangi İşleme Kadar Geri Alınacak:<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-xs-8">
                                                <i class="fa"></i><select class="form-control select2me select2-offscreen" id="islemgerialislemadi" name="islemgerialislemadi" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green" data-toggle="modal" data-target="#islemgerialconfirm">Değiştir</button>
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

    <div class="modal fade" id="cariduzenleconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Cari Bilgisi Değiştirilecek</h4>
                </div>
                <div class="modal-body">
                    Eski Cari Bilgisi Seçilen Yeni Cari ile Değiştirlecek?
                </div>
                <div class="modal-footer">
                    <a id="caridegistir" href="#" type="button" data-dismiss="modal" class="btn green">Tamam</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="sayacadiduzenleconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Sayaç Adı Değiştirilecek</h4>
                </div>
                <div class="modal-body">
                    Eski Sayaç Adı Seçilen Yeni Sayaç Adı ile Değiştirlecek?
                </div>
                <div class="modal-footer">
                    <a id="sayacadidegistir" href="#" type="button" data-dismiss="modal" class="btn green">Tamam</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="serinoduzenleconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Seri Numarası Değiştirilecek</h4>
                </div>
                <div class="modal-body">
                    Eski Seri Numarası Girilen Yeni Seri Numarası ile Değiştirlecek?
                </div>
                <div class="modal-footer">
                    <a id="serinodegistir" href="#" type="button" data-dismiss="modal" class="btn green">Tamam</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="uretimyerduzenleconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Geliş Yeri Bilgisi Değiştirilecek</h4>
                </div>
                <div class="modal-body">
                    Eski Geliş Yeri Bilgisi Seçilen Yeni Geliş Yeri ile Değiştirlecek?
                </div>
                <div class="modal-footer">
                    <a id="uretimyeridegistir" href="#" type="button" data-dismiss="modal" class="btn green">Tamam</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="islemgerialconfirm" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">İşlem Bilgisi Geri Alınacak</h4>
                </div>
                <div class="modal-body">
                    İşlem Bilgisi Seçilen Kademeye Kadar Geri Alınacak?
                </div>
                <div class="modal-footer">
                    <a id="islemigerial" href="#" type="button" data-dismiss="modal" class="btn green">Tamam</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
@stop
