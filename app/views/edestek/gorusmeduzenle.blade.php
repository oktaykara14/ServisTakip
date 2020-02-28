@extends('layout.master')

@section('page-title')
    <div class="page-title">
        <h1>Telefon Görüşmesi Bilgi <small>Düzenleme Ekranı</small></h1>
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
    <script src="{{ URL::to('assets/global/plugins/tinymce/tinymce.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.tr.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
    <script src="{{ URL::to('pages/edestek/form-validation-7.js') }}"></script>
@stop

@section('scripts')
<script>
    jQuery(document).ready(function() {
        Metronic.init(); // init metronic core componets
        Layout.init(); // init layout
        Demo.init(); // init demo features
        QuickSidebar.init(); // init quick sidebar
        FormValidationEdestek.init();
    });

    $(document).ready(function() {
        $('#hata_ara').on('change', function () {
            var hata = $(this).val();
            $.getJSON(" {{ URL::to('edestek/hatacozumlistesi') }}", {hata: hata}, function (event) {

                $('.hatalistesi').html('');
                $.each(event.cozum, function (index) {
                    $('.hatalistesi').append('<a href="#cozum-goster" data-toggle="modal" data-id="' + event.cozum[index].id + '" class="list-group-item goster" >' + event.cozum[index].problem + '</a>');
                });
            });
        });
        $('#problem').autocomplete({
            source: " {{ URL::to('edestek/problemcomplete') }}",
            minLength: 2,
            delay: 100
        });
        var options1 = $('#options1').val();
        if (options1 !== "") {
            $.getJSON(" {{ URL::to('edestek/musteritum') }}/" + options1, function (event) {
                $('#collapse_1 div').html('');
                $('#collapse_2 div').html('');
                $('#collapse_3 div').html('');
                $('#collapse_4 div').html('');
                $('#collapse_5 div').html('');
                var musteribilgi = event.musteribilgi;
                var sistembilgi = event.sistembilgi;
                var gorusmeler = event.gorusmeler;
                var programlar = event.programlar;
                var veritabanlari = event.veritabanlari;
                var urunler = event.urunler;
                var urunturleri = event.urunturleri;
                if (sistembilgi && musteribilgi) {
                    $('#collapse_1 div').append('<p><b>Cari Adı</b>: ' + (sistembilgi.cariadi ? sistembilgi.cariadi : '') + '</p>');
                }
                if (musteribilgi) {
                    $('#collapse_1 div').append('<p><b>İletişim Adresi</b>: ' + (musteribilgi.adresi ? musteribilgi.adresi : '') + '</p>');
                    $('#collapse_1 div').append('<p><b>Bulunduğu İl</b>: ' + (musteribilgi.il ? musteribilgi.il : '') + '</p>');
                    $('#collapse_1 div').append('<p><b>Telefonu</b>: ' + (musteribilgi.telefon ? musteribilgi.telefon : '') + '</p>');
                    $('#collapse_1 div').append('<p><b>Maili</b>: ' + (musteribilgi.mail ? musteribilgi.mail : '' ) + '</p>');
                    $('#collapse_1 div').append('<p><b>Yetkili Kişi</b>: ' + (musteribilgi.yetkiliadi ? musteribilgi.yetkiliadi : '') + '</p>');
                    $('#collapse_1 div').append('<p><b>Yetkili Telefonu</b>: ' + (musteribilgi.yetkilitel ? musteribilgi.yetkilitel : '') + '</p>');

                }
                if (sistembilgi && sistembilgi.plasiyer) {
                    $('#collapse_2 div').append('<p><b>İlgili Personel</b>: ' + (sistembilgi.plasiyer.plasiyeradi ? sistembilgi.plasiyer.plasiyeradi : '') + '</p>');
                }
                if (urunturleri) {
                    var uruntur = '';
                    $.each(urunturleri, function (index) {
                        uruntur += (uruntur === '' ? '' : ',') + urunturleri[index].adi;
                    });
                    $('#collapse_2 div').append('<p><b>Ürünler</b>: ' + uruntur + '</p>');
                }
                if (programlar) {
                    $.each(programlar, function (index) {
                        switch (programlar[index].edestekprogram_id) {
                            case '1':
                                $('#collapse_2 div').append('<p><b>Program</b>: Entegre (Epic)</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Versiyon</i> : ' + (programlar[index].versiyon ? programlar[index].versiyon : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Kullanıcı</i> : ' + (programlar[index].kullaniciadi ? programlar[index].kullaniciadi : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Şifresi</i> : ' + (programlar[index].sifre ? programlar[index].sifre : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Yetkili Şifre</i> : ' + (programlar[index].yetkilisifre ? programlar[index].yetkilisifre : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Diğer Bilgi</i> : ' + (programlar[index].diger ? programlar[index].diger : '') + '</p>');
                                break;
                            case '2':
                                $('#collapse_2 div').append('<p><b>Program</b>: EpicSmart </p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Versiyon</i> : ' + (programlar[index].versiyon ? programlar[index].versiyon : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Kullanıcı</i> : ' + (programlar[index].kullaniciadi ? programlar[index].kullaniciadi : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Şifresi</i> : ' + (programlar[index].sifre ? programlar[index].sifre : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Yetkili Şifre</i> : ' + (programlar[index].yetkilisifre ? programlar[index].yetkilisifre : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Diğer Bilgi</i> : ' + (programlar[index].diger ? programlar[index].diger : '') + '</p>');
                                break;
                            case '3':
                                $('#collapse_2 div').append('<p><b>Program</b>: 4com </p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Versiyon</i> : ' + (programlar[index].versiyon ? programlar[index].versiyon : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Kullanıcı</i> : ' + (programlar[index].kullaniciadi ? programlar[index].kullaniciadi : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Şifresi</i> : ' + (programlar[index].sifre ? programlar[index].sifre : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Yetkili Şifre</i> : ' + (programlar[index].yetkilisifre ? programlar[index].yetkilisifre : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Diğer Bilgi</i> : ' + (programlar[index].diger ? programlar[index].diger : '') + '</p>');
                                break;
                            case '4':
                                $('#collapse_2 div').append('<p><b>Program</b>: Entegrasyon </p>');

                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Firma</i> : ' + (programlar[index].firma.firma ? programlar[index].firma.firma : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Entegraston Tipi</i> : ' + (programlar[index].tip.tipi ? programlar[index].tip.tipi : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Program</i> : ' + (programlar[index].program.program ? programlar[index].program.program : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Versiyon</i> : ' + (programlar[index].versiyon.versiyon ? programlar[index].versiyon.versiyon : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Diğer Bilgi</i> : ' + (programlar[index].diger ? programlar[index].diger : '') + '</p>');
                                break;
                        }

                    });
                }
                if (veritabanlari) {
                    $.each(veritabanlari, function (index) {
                        switch (veritabanlari[index].edestekdatabase_id) {
                            case '1':
                                $('#collapse_2 div').append('<p><b>Veritabanı</b>: Oracle</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Versiyon</i> : ' + (veritabanlari[index].versiyon ? veritabanlari[index].versiyon : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Adı</i> : ' + (veritabanlari[index].adi ? veritabanlari[index].adi : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Kullanıcı</i> : ' + (veritabanlari[index].kullaniciadi ? veritabanlari[index].kullaniciadi : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Şifresi</i> : ' + (veritabanlari[index].sifre ? veritabanlari[index].sifre : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Diğer Bilgi</i> : ' + (veritabanlari[index].diger ? veritabanlari[index].diger : '') + '</p>');
                                break;
                            case '2':
                                $('#collapse_2 div').append('<p><b>Program</b>: Microsoft SQL Server </p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Versiyon</i> : ' + (veritabanlari[index].versiyon ? veritabanlari[index].versiyon : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Adı</i> : ' + (veritabanlari[index].adi ? veritabanlari[index].adi : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Kullanıcı</i> : ' + (veritabanlari[index].kullaniciadi ? veritabanlari[index].kullaniciadi : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Şifresi</i> : ' + (veritabanlari[index].sifre ? veritabanlari[index].sifre : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Diğer Bilgi</i> : ' + (veritabanlari[index].diger ? veritabanlari[index].diger : '') + '</p>');
                                break;
                            case '3':
                                $('#collapse_2 div').append('<p><b>Program</b>: MySQL </p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Versiyon</i> : ' + (veritabanlari[index].versiyon ? veritabanlari[index].versiyon : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Adı</i> : ' + (veritabanlari[index].adi ? veritabanlari[index].adi : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Kullanıcı</i> : ' + (veritabanlari[index].kullaniciadi ? veritabanlari[index].kullaniciadi : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Şifresi</i> : ' + (veritabanlari[index].sifre ? veritabanlari[index].sifre : '') + '</p>');
                                $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Diğer Bilgi</i> : ' + (veritabanlari[index].diger ? veritabanlari[index].diger : '') + '</p>');
                                break;
                        }

                    });
                }
                if (musteribilgi) {
                    if (musteribilgi.teamid) {
                        $('#collapse_3 div').append('<p><b>Teamviewer</b></p>');
                        $('#collapse_3 div').append('<p style="margin-left: 10px"><i>Id</i> : ' + musteribilgi.teamid + '</p>');
                        $('#collapse_3 div').append('<p style="margin-left: 10px"><i>Şifre</i> : ' + (musteribilgi.teampass ? musteribilgi.teampass : '') + '</p>');
                    }
                    if (musteribilgi.ammyyid) {
                        $('#collapse_3 div').append('<p><b>Ammyy</b></p>');
                        $('#collapse_3 div').append('<p style="margin-left: 10px"><i>Id</i> : ' + musteribilgi.ammyyid + '</p>');
                    }
                    if (musteribilgi.alpemixid) {
                        $('#collapse_3 div').append('<p><b>Alpemix</b></p>');
                        $('#collapse_3 div').append('<p style="margin-left: 10px"><i>Id</i> : ' + musteribilgi.alpemixid + '</p>');
                        $('#collapse_3 div').append('<p style="margin-left: 10px"><i>Şifre</i> : ' + (musteribilgi.alpemixpass ? musteribilgi.alpemixpass : '') + '</p>');
                    }
                    if (musteribilgi.uzakip) {
                        $('#collapse_3 div').append('<p><b>Uzak Bağlantı</b></p>');
                        $('#collapse_3 div').append('<p style="margin-left: 10px"><i>IP Adresi</i> : ' + musteribilgi.uzakip + '</p>');
                        $('#collapse_3 div').append('<p style="margin-left: 10px"><i>Kullanıcı</i> : ' + (musteribilgi.uzakkullanici ? musteribilgi.uzakkullanici : '') + '</p>');
                        $('#collapse_3 div').append('<p style="margin-left: 10px"><i>Şifre</i> : ' + (musteribilgi.uzakpass ? musteribilgi.uzakpass : '') + '</p>');
                    }

                }
                if (urunler) {
                    $.each(urunler, function (index) {
                        $('#collapse_4 div').append('<p><b>Ürün Türü</b>: ' + urunler[index].uruntur.adi + '</p>');
                        $('#collapse_4 div').append('<p style="margin-left: 10px"><i>Adı</i> : ' + (urunler[index].adi ? urunler[index].adi : '') + '</p>');
                        $('#collapse_4 div').append('<p style="margin-left: 10px"><i>Adet</i> : ' + (urunler[index].adet ? urunler[index].adet : '') + '</p>');
                        $('#collapse_4 div').append('<p style="margin-left: 10px"><i>Issue</i> : ' + (urunler[index].issue ? urunler[index].issue : '') + '</p>');
                        $('#collapse_4 div').append('<p style="margin-left: 10px"><i>Detay</i> : ' + (urunler[index].detay ? urunler[index].detay : '') + '</p>');
                    });
                }
                if (gorusmeler) {
                    $.each(gorusmeler, function (index) {
                        $('#collapse_5 div').append('<p><b>Konu</b>: ' + gorusmeler[index].kayitkonu + '</p>');
                        $('#collapse_5 div').append('<p style="margin-left: 10px"><i>İşlem</i> : ' + gorusmeler[index].yapilanislem + '</p>');
                        $('#collapse_5 div').append('<p style="margin-left: 10px"><i>Personel</i> : ' + gorusmeler[index].personel + '</p>');
                        $('#collapse_5 div').append('<p style="margin-left: 10px"><i>Tarih</i> : ' + gorusmeler[index].tarih + '</p>');
                        $('#collapse_5 div').append('<p style="margin-left: 10px"><i>Durum</i> : ' + gorusmeler[index].durum + '</p>');
                    });
                }
                if (musteribilgi) {
                    $("#yetkiliadi").val(musteribilgi.yetkiliadi);
                    $("#mask_phone").val(musteribilgi.yetkilitel);
                }
            });
        }
        $('#options1').on('change', function () {
            var id = $(this).val();
            if (id !== "") {
                $.getJSON(" {{ URL::to('edestek/musteritum') }}/" + id, function (event) {
                    $('#collapse_1 div').html('');
                    $('#collapse_2 div').html('');
                    $('#collapse_3 div').html('');
                    $('#collapse_4 div').html('');
                    $('#collapse_5 div').html('');
                    var musteribilgi = event.musteribilgi;
                    var sistembilgi = event.sistembilgi;
                    var gorusmeler = event.gorusmeler;
                    var programlar = event.programlar;
                    var veritabanlari = event.veritabanlari;
                    var urunler = event.urunler;
                    var urunturleri = event.urunturleri;
                    if (sistembilgi && musteribilgi) {
                        $('#collapse_1 div').append('<p><b>Cari Adı</b>: ' + sistembilgi.cariadi + '</p>');
                    }
                    if (musteribilgi) {
                        $('#collapse_1 div').append('<p><b>İletişim Adresi</b>: ' + musteribilgi.adresi + '</p>');
                        $('#collapse_1 div').append('<p><b>Bulunduğu İl</b>: ' + musteribilgi.il + '</p>');
                        $('#collapse_1 div').append('<p><b>Telefonu</b>: ' + musteribilgi.telefon + '</p>');
                        $('#collapse_1 div').append('<p><b>Maili</b>: ' + musteribilgi.mail + '</p>');
                        $('#collapse_1 div').append('<p><b>Yetkili Kişi</b>: ' + musteribilgi.yetkiliadi + '</p>');
                        $('#collapse_1 div').append('<p><b>Yetkili Telefonu</b>: ' + musteribilgi.yetkilitel + '</p>');

                    }
                    if (sistembilgi && sistembilgi.plasiyer) {
                        $('#collapse_2 div').append('<p><b>İlgili Personel</b>: ' + sistembilgi.plasiyer.plasiyeradi + '</p>');
                    }
                    if (urunturleri) {
                        var uruntur = '';
                        $.each(urunturleri, function (index) {
                            uruntur += (uruntur === '' ? '' : ',') + urunturleri[index].adi;
                        });
                        $('#collapse_2 div').append('<p><b>Ürünler</b>: ' + uruntur + '</p>');
                    }
                    if (programlar) {
                        $.each(programlar, function (index) {
                            switch (programlar[index].edestekprogram_id) {
                                case '1':
                                    $('#collapse_2 div').append('<p><b>Program</b>: Entegre (Epic)</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Versiyon</i> : ' + programlar[index].versiyon + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Kullanıcı</i> : ' + programlar[index].kullaniciadi + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Şifresi</i> : ' + programlar[index].sifre + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Yetkili Şifre</i> : ' + programlar[index].yetkilisifre + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Diğer Bilgi</i> : ' + programlar[index].diger + '</p>');
                                    break;
                                case '2':
                                    $('#collapse_2 div').append('<p><b>Program</b>: EpicSmart </p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Versiyon</i> : ' + programlar[index].versiyon + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Kullanıcı</i> : ' + programlar[index].kullaniciadi + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Şifresi</i> : ' + programlar[index].sifre + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Yetkili Şifre</i> : ' + programlar[index].yetkilisifre + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Diğer Bilgi</i> : ' + programlar[index].diger + '</p>');
                                    break;
                                case '3':
                                    $('#collapse_2 div').append('<p><b>Program</b>: 4com </p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Versiyon</i> : ' + programlar[index].versiyon + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Kullanıcı</i> : ' + programlar[index].kullaniciadi + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Şifresi</i> : ' + programlar[index].sifre + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Yetkili Şifre</i> : ' + programlar[index].yetkilisifre + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Diğer Bilgi</i> : ' + programlar[index].diger + '</p>');
                                    break;
                                case '4':
                                    $('#collapse_2 div').append('<p><b>Program</b>: Entegrasyon </p>');

                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Firma</i> : ' + programlar[index].firma.firma + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Entegraston Tipi</i> : ' + programlar[index].tip.tipi + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Program</i> : ' + programlar[index].program.program + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Versiyon</i> : ' + programlar[index].versiyon.versiyon + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Diğer Bilgi</i> : ' + programlar[index].diger + '</p>');
                                    break;
                            }

                        });
                    }
                    if (veritabanlari) {
                        $.each(veritabanlari, function (index) {
                            switch (veritabanlari[index].edestekdatabase_id) {
                                case '1':
                                    $('#collapse_2 div').append('<p><b>Veritabanı</b>: Oracle</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Versiyon</i> : ' + veritabanlari[index].versiyon + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Adı</i> : ' + veritabanlari[index].adi + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Kullanıcı</i> : ' + veritabanlari[index].kullaniciadi + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Şifresi</i> : ' + veritabanlari[index].sifre + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Diğer Bilgi</i> : ' + veritabanlari[index].diger + '</p>');
                                    break;
                                case '2':
                                    $('#collapse_2 div').append('<p><b>Program</b>: Microsoft SQL Server </p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Versiyon</i> : ' + veritabanlari[index].versiyon + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Adı</i> : ' + veritabanlari[index].adi + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Kullanıcı</i> : ' + veritabanlari[index].kullaniciadi + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Şifresi</i> : ' + veritabanlari[index].sifre + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Diğer Bilgi</i> : ' + veritabanlari[index].diger + '</p>');
                                    break;
                                case '3':
                                    $('#collapse_2 div').append('<p><b>Program</b>: MySQL </p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Versiyon</i> : ' + veritabanlari[index].versiyon + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Adı</i> : ' + veritabanlari[index].adi + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Kullanıcı</i> : ' + veritabanlari[index].kullaniciadi + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Şifresi</i> : ' + veritabanlari[index].sifre + '</p>');
                                    $('#collapse_2 div').append('<p style="margin-left: 10px"><i>Diğer Bilgi</i> : ' + veritabanlari[index].diger + '</p>');
                                    break;
                            }

                        });
                    }
                    if (musteribilgi) {
                        if (musteribilgi.teamid) {
                            $('#collapse_3 div').append('<p><b>Teamviewer</b></p>');
                            $('#collapse_3 div').append('<p style="margin-left: 10px"><i>Id</i> : ' + musteribilgi.teamid + '</p>');
                            $('#collapse_3 div').append('<p style="margin-left: 10px"><i>Şifre</i> : ' + musteribilgi.teampass + '</p>');
                        }
                        if (musteribilgi.ammyyid) {
                            $('#collapse_3 div').append('<p><b>Ammyy</b></p>');
                            $('#collapse_3 div').append('<p style="margin-left: 10px"><i>Id</i> : ' + musteribilgi.ammyyid + '</p>');
                        }
                        if (musteribilgi.alpemixid) {
                            $('#collapse_3 div').append('<p><b>Alpemix</b></p>');
                            $('#collapse_3 div').append('<p style="margin-left: 10px"><i>Id</i> : ' + musteribilgi.alpemixid + '</p>');
                            $('#collapse_3 div').append('<p style="margin-left: 10px"><i>Şifre</i> : ' + musteribilgi.alpemixpass + '</p>');
                        }
                        if (musteribilgi.uzakip) {
                            $('#collapse_3 div').append('<p><b>Uzak Bağlantı</b></p>');
                            $('#collapse_3 div').append('<p style="margin-left: 10px"><i>IP Adresi</i> : ' + musteribilgi.uzakip + '</p>');
                            $('#collapse_3 div').append('<p style="margin-left: 10px"><i>Kullanıcı</i> : ' + musteribilgi.uzakkullanici + '</p>');
                            $('#collapse_3 div').append('<p style="margin-left: 10px"><i>Şifre</i> : ' + musteribilgi.uzakpass + '</p>');
                        }

                    }
                    if (urunler) {
                        $.each(urunler, function (index) {
                            $('#collapse_4 div').append('<p><b>Urun Türü</b>: ' + urunler[index].uruntur.adi + '</p>');
                            $('#collapse_4 div').append('<p style="margin-left: 10px"><i>Adı</i> : ' + urunler[index].adi + '</p>');
                            $('#collapse_4 div').append('<p style="margin-left: 10px"><i>Adet</i> : ' + urunler[index].adet + '</p>');
                            $('#collapse_4 div').append('<p style="margin-left: 10px"><i>Issue</i> : ' + urunler[index].issue + '</p>');
                            $('#collapse_4 div').append('<p style="margin-left: 10px"><i>Detay</i> : ' + urunler[index].detay + '</p>');
                        });
                    }
                    if (gorusmeler) {
                        $.each(gorusmeler, function (index) {
                            $('#collapse_5 div').append('<p><b>Konu</b>: ' + gorusmeler[index].kayitkonu + '</p>');
                            $('#collapse_5 div').append('<p style="margin-left: 10px"><i>İşlem</i> : ' + gorusmeler[index].yapilanislem + '</p>');
                            $('#collapse_5 div').append('<p style="margin-left: 10px"><i>Personel</i> : ' + gorusmeler[index].personel + '</p>');
                            $('#collapse_5 div').append('<p style="margin-left: 10px"><i>Tarih</i> : ' + gorusmeler[index].tarih + '</p>');
                            $('#collapse_5 div').append('<p style="margin-left: 10px"><i>Durum</i> : ' + gorusmeler[index].durum + '</p>');
                        });
                    }
                    if (musteribilgi) {
                        $("#yetkiliadi").val(musteribilgi.yetkiliadi);
                        $("#mask_phone").val(musteribilgi.yetkilitel);
                    }
                });
            }
        });

        $('#options2').on('change', function () {
            var id = $(this).val();
            $("#options3").select2("val", "");
            if (id !== "") {
                $.getJSON(" {{ URL::to('edestek/detaylar') }}/" + id, function (event) {
                    $("#options3").empty();
                    var detay = event.detay;
                    if (detay.length > 0) {
                        $("#options3").append('<option value="">Seçiniz...</option>');
                        $.each(detay, function (index) {
                            $("#options3").append('<option value="' + detay[index].id + '"> ' + detay[index].detay + '</option>');
                        });
                    } else {
                        $("#options3").empty();
                    }
                });
            } else {
                $("#options3").empty();
            }
        });
        var options2 = $('#options2').val();
        var options3 = $('#options3').val();
        if (options2 !== "") {
            $.getJSON(" {{ URL::to('edestek/detaylar') }}/" + options2, function (event) {
                $("#options3").empty();
                var detay = event.detay;
                if (detay.length > 0) {
                    $("#options3").append('<option value="">Seçiniz...</option>');
                    $.each(detay, function (index) {
                        if (options3 === detay[index].id)
                            $("#options3").append('<option value="' + detay[index].id + '" selected> ' + detay[index].detay + '</option>');
                        else
                            $("#options3").append('<option value="' + detay[index].id + '"> ' + detay[index].detay + '</option>');
                    });
                    $("#options3").select2("val", options3);
                } else {
                    $("#options3").empty();
                }
            });
        } else {
            $("#options3").empty();
        }

        $('#options5').on('change', function () {
            var id = $(this).val();
            $('.aciklama').removeClass('hide');
            if (id === "2") {
                var personel = $('#options4').val();
                $.getJSON(" {{ URL::to('edestek/devredilecekler') }}/" + personel, function (event) {
                    var devredilen = event.devredilen;
                    $("#options8").empty();
                    if (devredilen.length > 0) {
                        $("#options8").append('<option value="">Seçiniz...</option>');
                        $.each(devredilen, function (index) {
                            $("#options8").append('<option value="' + devredilen[index].id + '"> ' + devredilen[index].adisoyadi + '</option>');
                        });
                    } else {
                        $("#options8").empty();
                    }
                });
                $('#konu-devret').modal('show');

            }else if(id==="1"){
                $('.aciklama').addClass('hide');
                $('#aciklama').val('');
            }
        });

        tinymce.init({
            selector: "textarea2", theme: "modern", format: 'text',
            language: "tr", height: 250, resize: false, entity_encoding: "raw",
            entities: '160,nbsp,161,iexcl,162,cent,163,pound,164,curren,165,yen,166,brvbar,167,sect,168,uml,169,copy,170,ordf,171,laquo,172,not,173,shy,174,reg,175,macr,176,deg,177,plusmn,178,sup2,179,sup3,180,acute,181,micro,182,para,183,middot,184,cedil,185,sup1,186,ordm,187,raquo,188,frac14,189,frac12,190,frac34,191,iquest,192,Agrave,193,Aacute,194,Acirc,195,Atilde,196,Auml,197,Aring,198,AElig,199,Ccedil,200,Egrave,201,Eacute,202,Ecirc,203,Euml,204,Igrave,205,Iacute,206,Icirc,207,Iuml,208,ETH,209,Ntilde,210,Ograve,211,Oacute,212,Ocirc,213,Otilde,214,Ouml,215,times,216,Oslash,217,Ugrave,218,Uacute,219,Ucirc,220,Uuml,221,Yacute,222,THORN,223,szlig,224,agrave,225,aacute,226,acirc,227,atilde,228,auml,229,aring,230,aelig,231,ccedil,232,egrave,233,eacute,234,ecirc,235,euml,236,igrave,237,iacute,238,icirc,239,iuml,240,eth,241,ntilde,242,ograve,243,oacute,244,ocirc,245,otilde,246,ouml,247,divide,248,oslash,249,ugrave,250,uacute,251,ucirc,252,uuml,253,yacute,254,thorn,255,yuml,402,fnof,913,Alpha,914,Beta,915,Gamma,916,Delta,917,Epsilon,918,Zeta,919,Eta,920,Theta,921,Iota,922,Kappa,923,Lambda,924,Mu,925,Nu,926,Xi,927,Omicron,928,Pi,929,Rho,931,Sigma,932,Tau,933,Upsilon,934,Phi,935,Chi,936,Psi,937,Omega,945,alpha,946,beta,947,gamma,948,delta,949,epsilon,950,zeta,951,eta,952,theta,953,iota,954,kappa,955,lambda,956,mu,957,nu,958,xi,959,omicron,960,pi,961,rho,962,sigmaf,963,sigma,964,tau,965,upsilon,966,phi,967,chi,968,psi,969,omega,977,thetasym,978,upsih,982,piv,8226,bull,8230,hellip,8242,prime,8243,Prime,8254,oline,8260,frasl,8472,weierp,8465,image,8476,real,8482,trade,8501,alefsym,8592,larr,8593,uarr,8594,rarr,8595,darr,8596,harr,8629,crarr,8656,lArr,8657,uArr,8658,rArr,8659,dArr,8660,hArr,8704,forall,8706,part,8707,exist,8709,empty,8711,nabla,8712,isin,8713,notin,8715,ni,8719,prod,8721,sum,8722,minus,8727,lowast,8730,radic,8733,prop,8734,infin,8736,ang,8743,and,8744,or,8745,cap,8746,cup,8747,int,8756,there4,8764,sim,8773,cong,8776,asymp,8800,ne,8801,equiv,8804,le,8805,ge,8834,sub,8835,sup,8836,nsub,8838,sube,8839,supe,8853,oplus,8855,otimes,8869,perp,8901,sdot,8968,lceil,8969,rceil,8970,lfloor,8971,rfloor,9001,lang,9002,rang,9674,loz,9824,spades,9827,clubs,9829,hearts,9830,diams,338,OElig,339,oelig,352,Scaron,353,scaron,376,Yuml,710,circ,732,tilde,8194,ensp,8195,emsp,8201,thinsp,8204,zwnj,8205,zwj,8206,lrm,8207,rlm,8211,ndash,8212,mdash,8216,lsquo,8217,rsquo,8218,sbquo,8220,ldquo,8221,rdquo,8222,bdquo,8224,dagger,8225,Dagger,8240,permil,8249,lsaquo,8250,rsaquo,8364,euro',
            plugins: ["moxiemanager autolink lists link image charmap preview hr", "wordcount code media save table directionality paste imagetools"],
            toolbar: "undo redo |  bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
            //    toolbar_items_size : 'small',
            //    menubar: false,
            relative_urls: false,
            setup: function (editor) {
                editor.on('change', function () {
                    if (editor.id === "gorusmedetay") {
                        $("#gorusmedetayid").val(editor.getContent());
                    } else {
                        $("#gorusmedetayyeniid").val(editor.getContent());
                    }
                });
            }
        });
        $('#closeButton7').click(function () {
            $("#options5").select2("val", "0");
            $('#konu-devret').modal('hide');
        });

        $(document).on("click", ".goster", function () {
            var Id = $(this).data('id');
            $.getJSON(" {{ URL::to('edestek/hatacozum') }}/" + Id, function (event) {
                $(".cozumkonu").html(event.cozum.konu.adi);
                $(".cozumkonudetay").html(event.cozum.konudetay.detay);
                $(".cozumproblem").html(event.cozum.problem);
                $(".cozumdetay").html(event.cozum.cozum);

            });
        });
        $('#musteriduzenle').click(function () {
            var musteriid = $("#options1").val();
            if (musteriid !== "") {
                $.getJSON(" {{ URL::to('edestek/musteri') }}/" + musteriid, function (event) {
                    $("#musteriguncel").val(event.musteri.musteriadi);
                    $("#musteriid").val(musteriid);
                });
                $('#musteri-duzenle').modal('show');
            } else {
                toastr['warning']('Firma Seçilmedi', 'Firma Güncelleme Hatası');
            }
        });
        $('#konuduzenle').click(function () {
            var konuid = $("#options2").val();
            if (konuid !== "") {
                $.getJSON(" {{ URL::to('edestek/konu') }}/" + konuid, function (event) {
                    $("#konuguncel").val(event.konu.adi);
                    $("#konuid").val(konuid);
                });
                $('#konu-duzenle').modal('show');
            } else {
                toastr['warning']('Konu Seçilmedi', 'Konu Güncelleme Hatası');
            }
        });
        $('#detayekle').click(function () {
            var konuid = $("#options2").val();
            if (konuid !== "") {
                $.getJSON(" {{ URL::to('edestek/konu') }}/" + konuid, function (event) {
                    $(".konudetayyeni").text(event.konu.adi);
                });
            } else {
                $('#detay-ekle').modal('hide');
                toastr['warning']('Detay Seçilmedi', 'Detay Ekleme Hatası');
            }
        });
        $('#detayduzenle').click(function () {
            //var konuid = $("#options2").val();
            var detayid = $("#options3").val();
            if (detayid !== "" && detayid !== null) {
                $.getJSON(" {{ URL::to('edestek/konudetay') }}/" + detayid, function (event) {
                    $("#detayguncel").val(event.detay.detay);
                    $(".konudetayguncel").text(event.detay.konu.adi);
                });
                $('#detay-duzenle').modal('show');
            } else {
                toastr['warning']('Detay Seçilmedi', 'Detay Güncelleme Hatası');
            }
        });
        $('#konudevret').click(function () {
            var devredilenid = $("#options8").val();
            if (devredilenid !== "") {
                $("#devreden").val(devredilenid);
                $('#konu-devret').modal('hide');
            } else {
                toastr['warning']('Devredilecek Kişi Seçilmedi', 'Seçim Yapılmadı');
            }
        });
        $('.musteriekle').click(function () {
            var musteri = $('#musteriyeni').val();
            if (musteri === "") {
                toastr['warning']('Müşteri Boş Geçildi', 'Müşteri Ekleme Hatası');
            } else {
                $.getJSON(" {{ URL::to('edestek/hizlimusteriekle') }}", {musteri: musteri}, function (event) {
                    toastr[event.type](event.text, event.title);
                    $('#musteri-ekle').modal('hide');
                    if (event.durum === 1 || event.durum === 2) {
                        $('#options1').empty();
                        var musteriler = event.musteriler;
                        var musteriid = 0;
                        $.each(musteriler, function (index) {
                            if (musteriler[index].musteriadi === musteri)
                                musteriid = musteriler[index].id;
                            $('#options1').append('<option value="' + musteriler[index].id + '" >' + musteriler[index].musteriadi + '</option>');
                        });
                        $('#options1').select2('val', musteriid);
                    }
                });
            }
        });
        $('.musteriduzenle').click(function () {
            var musteri = $('#musteriguncel').val();
            var musteriid = $('#musteriid').val();
            if (musteri === "") {
                toastr['warning']('Müşteri Boş Geçildi', 'Müşteri Ekleme Hatası');
            } else {
                $.getJSON(" {{ URL::to('edestek/hizlimusteriduzenle') }}", {
                    musteriid: musteriid,
                    musteri: musteri
                }, function (event) {
                    toastr[event.type](event.text, event.title);
                    $('#musteri-duzenle').modal('hide');
                    if (event.durum === 1) {
                        $('#options1').empty();
                        var musteriler = event.musteriler;
                        $.each(musteriler, function (index) {
                            $('#options1').append('<option value="' + musteriler[index].id + '" >' + musteriler[index].musteriadi + '</option>');
                        });
                        $('#options1').select2('val', musteriid);
                    }
                });
            }
        });
        $('.konuekle').click(function () {
            var konu = $('#konuyeni').val();
            if (konu === "") {
                toastr['warning']('Konu Boş Geçildi', 'Konu Ekleme Hatası');
            } else {
                $.getJSON(" {{ URL::to('edestek/konuekle') }}", {konu: konu}, function (event) {
                    toastr[event.type](event.text, event.title);
                    $('#konu-ekle').modal('hide');
                    if (event.durum === 1 || event.durum === 2) {
                        $('#options2').empty();
                        var konular = event.konular;
                        var konuid = 0;
                        $.each(konular, function (index) {
                            if (konular[index].adi === konu)
                                konuid = konular[index].id;
                            $('#options2').append('<option value="' + konular[index].id + '" >' + konular[index].adi + '</option>');
                        });
                        $('#options2').select2('val', konuid);
                    }
                });
            }
        });
        $('.konuduzenle').click(function () {
            var konu = $('#konuguncel').val();
            var konuid = $('#konuid').val();
            if (konu === "") {
                toastr['warning']('Konu Boş Geçildi', 'Konu Ekleme Hatası');
            } else {
                $.getJSON(" {{ URL::to('edestek/konuduzenle') }}", {konuid: konuid, konu: konu}, function (event) {
                    toastr[event.type](event.text, event.title);
                    $('#konu-duzenle').modal('hide');
                    if (event.durum === 1) {
                        $('#options2').empty();
                        var konular = event.konular;
                        $.each(konular, function (index) {
                            $('#options2').append('<option value="' + konular[index].id + '" >' + konular[index].adi + '</option>');
                        });
                        $('#options2').select2('val', konuid);
                    }
                });
            }
        });
        $('.detayekle').click(function () {
            var konu = $('#options2').val();
            var detay = $('#detayyeni').val();
            if (detay === "") {
                toastr['warning']('Detay Boş Geçildi', 'Detay Ekleme Hatası');
            } else {
                $.getJSON(" {{ URL::to('edestek/detayekle') }}", {konu: konu, detay: detay}, function (event) {
                    toastr[event.type](event.text, event.title);
                    $('#detay-ekle').modal('hide');
                    if (event.durum === 1 || event.durum === 2) {
                        $('#options3').empty();
                        var detaylar = event.detaylar;
                        var detayid = 0;
                        $.each(detaylar, function (index) {
                            if (detaylar[index].detay === detay)
                                detayid = detaylar[index].id;
                            $('#options3').append('<option value="' + detaylar[index].id + '" >' + detaylar[index].detay + '</option>');
                        });
                        $('#options3').select2('val', detayid);
                    }
                });
            }
        });
        $('.detayduzenle').click(function () {
            var konu = $('#options2').val();
            var detayid = $('#options3').val();
            var detay = $('#detayguncel').val();
            if (detay === "") {
                toastr['warning']('Detay Boş Geçildi', 'Detay Ekleme Hatası');
            } else {
                $.getJSON(" {{ URL::to('edestek/detayduzenle') }}", {
                    konu: konu,
                    detayid: detayid,
                    detay: detay
                }, function (event) {
                    toastr[event.type](event.text, event.title);
                    $('#detay-duzenle').modal('hide');
                    if (event.durum === 1) {
                        $('#options3').empty();
                        var detaylar = event.detaylar;
                        $.each(detaylar, function (index) {
                            $('#options3').append('<option value="' + detaylar[index].id + '" >' + detaylar[index].detay + '</option>');
                        });
                        $('#options3').select2('val', detayid);
                    }
                });
            }
        });
        $("select").on("select2-close", function () { $(this).valid(); });
        $('#tarih').on('change', function() { $(this).valid(); });
    });
</script>
@stop

@section('content')
    <div class="col-md-8">
        <div class="portlet box">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-pencil"></i>Telefon Görüşmesi Bilgi Düzenle
                </div>
            </div>
            <div class="portlet-body form">
                <!-- BEGIN FORM-->
                <form action="{{ URL::to('edestek/gorusmeduzenle/'.$kayit->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                            <label class="control-label col-md-2 col-xs-12">Firma Adı <span class="required" aria-required="true"> * </span></label>
                            <div class="input-icon right col-md-6 col-xs-12">
                                <i class="fa"></i><select class="form-control select2me select2-offscreen" id="options1" name="options1" tabindex="-1" title="">
                                    <option value="">Seçiniz...</option>
                                    @foreach($musteriler as $musteri)
                                        @if((Input::old('options1')? Input::old('options1') : $kayit->edestekmusteri_id)==$musteri->id )
                                            <option value="{{ $musteri->id }}" selected>{{ $musteri->musteriadi }}</option>
                                        @else
                                            <option value="{{ $musteri->id }}">{{ $musteri->musteriadi }}</option>
                                        @endif
                                    @endforeach

                                </select>
                            </div>
                            <div class="col-md-4 col-xs-12">
                                <a href="#musteri-ekle" data-toggle="modal" type="button" class="btn btn-success ">Ekle</a>
                                <a href="" id="musteriduzenle" data-toggle="modal" type="button" class="btn btn-warning ">Düzenle</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">Yetkili Adı</label>
                            <div class="col-md-9">
                                <input type="text" id="yetkiliadi" name="yetkiliadi" value="{{Input::old('yetkiliadi') ? Input::old('yetkiliadi') : $gorusme->yetkiliadi }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">Yetkili Telefonu</label>
                            <div class="col-md-9">
                                <input type="text" id="mask_phone" name="telefon" value="{{Input::old('telefon') ? Input::old('telefon') : $gorusme->yetkilitel }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">Tarih<span class="required" aria-required="true"> * </span></label>
                            <div class="input-icon right col-md-9">
                                <i class="fa"></i><div class="input-group input-large date date-picker" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                    <input id="tarih" type="text" name="tarih" class="form-control" value="{{Input::old('tarih')? Input::old('tarih') : date("d-m-Y", strtotime($kayit->tarih)) }}">
                                <span class="input-group-btn">
                                <button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button>
                                </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-xs-12">Konu <span class="required" aria-required="true"> * </span></label>
                            <div class="input-icon right col-md-6 col-xs-12">
                                <i class="fa"></i><select class="form-control select2me select2-offscreen" id="options2" name="options2" tabindex="-1" title="">
                                    <option value="">Seçiniz...</option>
                                    @foreach($konular as $konu)
                                        @if((Input::old('options2')? Input::old('options2') : $gorusme->edestekkonu_id)==$konu->id )
                                            <option value="{{ $konu->id }}" selected>{{ $konu->adi }}</option>
                                        @else
                                            <option value="{{ $konu->id }}">{{ $konu->adi }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-xs-12">
                                <a href="#konu-ekle" data-toggle="modal" type="button" class="btn btn-success ">Ekle</a>
                                <a href="" id="konuduzenle" data-toggle="modal" type="button" class="btn btn-warning ">Düzenle</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-xs-12">Detay<span class="required" aria-required="true"> * </span></label>
                            <div class="input-icon right col-md-6 col-xs-12">
                                <i class="fa"></i><select class="form-control select2me select2-offscreen" id="options3" name="options3" tabindex="-1" title="">
                                    <option value="">Seçiniz...</option>
                                    @foreach($detaylar as $detay)
                                        @if((Input::old('options3')? Input::old('options3') : $gorusme->edestekkonudetay_id)==$detay->id )
                                            <option value="{{ $detay->id }}" selected>{{ $detay->detay }}</option>
                                        @else
                                            <option value="{{ $detay->id }}">{{ $detay->detay }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-xs-12">
                                <a href="#detay-ekle" id="detayekle" data-toggle="modal" type="button" class="btn btn-success ">Ekle</a>
                                <a href="" id="detayduzenle" data-toggle="modal" type="button" class="btn btn-warning ">Düzenle</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">Problem <span class="required" aria-required="true"> * </span></label>
                            <div class="input-icon right col-md-9">
                                <i class="fa"></i><input type="text" id="problem" name="problem" value="{{Input::old('problem') ? Input::old('problem') : $gorusme->problem}}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-xs-12">Yapılan İşlem </label>
                            <div class="col-md-9 col-xs-12">
                                <textarea2 id="gorusmedetay" name="gorusmedetay">{{Input::old('gorusmedetayid') ? Input::old('gorusmedetayid') : $gorusme->cozum }}</textarea2>
                            </div>
                        </div>
                        <input id="gorusmedetayid" name="gorusmedetayid" class="hide" value="{{Input::old('gorusmedetayid') ? Input::old('gorusmedetayid') : $gorusme->cozum }}" />
                        <div class="form-group">
                            <label class="control-label col-md-2 col-xs-12">Personel <span class="required" aria-required="true"> * </span></label>
                            <div class="input-icon right col-md-9 col-xs-12">
                                <i class="fa"></i><select class="form-control select2me select2-offscreen" id="options4" name="options4" tabindex="-1" title="">
                                    <option value="">Seçiniz...</option>
                                    @foreach($personeller as $personel)
                                        @if((Input::old('options4')? Input::old('options4') : $kayit->edestekpersonel_id)==$personel->id )
                                            <option value="{{ $personel->id }}" selected>{{ $personel->adisoyadi }}</option>
                                        @else
                                            <option value="{{ $personel->id }}">{{ $personel->adisoyadi }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-xs-12">Durum</label>
                            <div class="col-md-4 col-xs-12">
                                <select class="form-control select2me select2-offscreen" id="options5" name="options5" tabindex="-1" title="">
                                    <option value="0" @if((Input::old('options5')? Input::old('options5') : $kayit->durum)=='0') selected @endif >Bekliyor</option>
                                    <option value="1" @if((Input::old('options5')? Input::old('options5') : $kayit->durum)=='1') selected @endif >Tamamlandı</option>
                                    <option value="2" @if((Input::old('options5')? Input::old('options5') : $kayit->durum)=='2') selected @endif >Devredildi</option>
                                    <option value="3" @if((Input::old('options5')? Input::old('options5') : $kayit->durum)=='3') selected @endif >İptal Edildi</option>
                                </select>
                            </div>
                            <label class="control-label col-md-2">Harcanan Süre <span class="required" aria-required="true"> * </span></label>
                            <div class="input-icon right col-md-2">
                                <i class="fa"></i><input type="text" id="sure" name="sure" value="{{Input::old('sure') ? Input::old('sure') : $kayit->sure}}" class="form-control">
                            </div>
                            <label class="control-label">Dakika</label>
                        </div>
                        <div class="form-group aciklama">
                            <label class="control-label col-md-2">Durum Açıklaması</label>
                            <div class="col-md-9 col-xs-12">
                                <input type="text" id="aciklama" name="aciklama" value="{{Input::old('aciklama') ? Input::old('aciklama') : $kayit->durum_aciklama}}" class="form-control">
                            </div>
                        </div>
                        <input id="devreden" name="devreden" class="hide" value="" />
                        <input type="checkbox" name="hatacozumu" class="hide"/>
                        <div class="form-group">{{ Form::token() }}</div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-xs-12" style="text-align: center">
                                <button type="submit" class="btn green">Kaydet</button>
                                <a href="{{ URL::to('edestek/edestekkayit')}}" class="btn default">Vazgeç</a>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
            <!-- END VALIDATION STATES-->
        </div>
    </div>
    <div class="col-md-4">
        <div class="portlet box">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-info"></i>Diğer Bilgiler
                </div>
            </div>
            <div class="portlet-body form">
                <div class="tabbable-line">
                    <ul class="nav nav-tabs ">
                        <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true"> Müşteri Bİlgisi </a></li>
                        <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">Hata Çözümleri </a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1">
                            <div class="portlet-body">
                                <div class="panel-group accordion" id="accordion1">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#collapse_1">
                                                    <i class="fa fa-phone" style="padding-right: 2px"></i>İletişim Bilgileri </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_1" class="panel-collapse in">
                                            <div class="panel-body">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#collapse_2">
                                                    <i class="fa fa-database" style="padding-right: 2px"></i>Sistem Bilgisi </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_2" class="panel-collapse collapse">
                                            <div class="panel-body" style="overflow-y:auto;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#collapse_3">
                                                    <i class="fa fa-desktop" style="padding-right: 2px"></i>Bağlantı Bilgileri </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_3" class="panel-collapse collapse">
                                            <div class="panel-body">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#collapse_4">
                                                    <i class="fa fa-shopping-cart" style="padding-right: 2px"></i>Ürün Bilgileri </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_4" class="panel-collapse collapse">
                                            <div class="panel-body">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#collapse_5">
                                                    <i class="fa fa-comment" style="padding-right: 2px"></i>Son Görüşmeler </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_5" class="panel-collapse collapse">
                                            <div class="panel-body">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_2">
                            <div class="input-group">
                                <span class="input-group-addon">
                                <i class="fa fa-search"></i>
                                </span>
                                <input type="text" id="hata_ara" placeholder="Aramak için Buraya Yazın" class="form-control">
                            </div>
                            <div class="hatalistesi list-group">
                                @foreach($hatacozumleri as $hatacozum)
                                    <a href="#cozum-goster" data-toggle="modal" data-id="{{ $hatacozum->id }}" class="list-group-item goster" >{{$hatacozum->problem}}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('modal')
    <div class="modal fade" id="musteri-ekle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Müşteri Ekle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_1" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Yeni Müşteri / Firma Ekle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Müşteri Adı <span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-6">
                                                <i class="fa"></i><input type="text" id="musteriyeni" name="musteriyeni" value="{{Input::old('musteriyeni')}}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green musteriekle">Kaydet</button>
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
    <div class="modal fade" id="musteri-duzenle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Müşteri / Firma Düzenle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Konu Düzenle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Müşteri Adı <span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-6">
                                                <i class="fa"></i><input type="text" id="musteriguncel" name="musteriguncel" value="{{Input::old('musteriguncel')}}" data-required="1" class="form-control">
                                                <input type="text" id="musteriid" name="musteriid" value="{{Input::old('musteriid')}}" data-required="1" class="form-control hide">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green musteriduzenle">Kaydet</button>
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
    <div class="modal fade" id="konu-ekle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Konu Ekle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_3" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Yeni Konu Ekle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Konu <span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-7">
                                                <i class="fa"></i><input type="text" id="konuyeni" name="konuyeni" value="{{Input::old('konuyeni')}}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green konuekle">Kaydet</button>
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
    <div class="modal fade" id="konu-duzenle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Konu Düzenle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_4" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Konu Düzenle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Konu <span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-7">
                                                <i class="fa"></i><input type="text" id="konuguncel" name="konuguncel" value="{{Input::old('konuguncel')}}" data-required="1" class="form-control">
                                                <input type="text" id="konuid" name="konuid" value="{{Input::old('konuid')}}" data-required="1" class="form-control hide">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green konuduzenle">Kaydet</button>
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
    <div class="modal fade" id="detay-ekle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Konu Detayı Ekle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_5" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Yeni Konu Detayı Ekle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Konu</label>
                                            <label class="col-md-7 col-xs-12 konudetayyeni" style="margin-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Detay <span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-7">
                                                <i class="fa"></i><input type="text" id="detayyeni" name="detayyeni" value="{{Input::old('detayyeni')}}" data-required="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green detayekle">Kaydet</button>
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
    <div class="modal fade" id="detay-duzenle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Konu Detayı Düzenle
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_6" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Konu Detayı Düzenle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Konu</label>
                                            <label class="col-md-7 col-xs-12 konudetayguncel" style="margin-top: 9px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Detay<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-7">
                                                <i class="fa"></i><input type="text" id="detayguncel" name="detayguncel" value="{{Input::old('detayguncel')}}" data-required="1" class="form-control">
                                                <input type="text" id="detayid" name="detayid" value="{{Input::old('detayid')}}" data-required="1" class="form-control hide">
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-xs-12" style="text-align: center">
                                                    <button type="button" class="btn green detayduzenle">Kaydet</button>
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
    <div class="modal fade" id="cozum-goster" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Hata Çözümü Detayı</h4>
                </div>
                <div class="modal-body">
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        <form action="" id="form_sample_7" class="form-horizontal" novalidate="novalidate">
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-xs-12">Konu</label>
                                    <label class="cozumkonu col-md-7 col-xs-12" style="padding-top: 7px"></label>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Detayı</label>
                                    <label class="cozumkonudetay col-md-7 col-xs-12" style="padding-top: 7px"></label>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-xs-12">Problem</label>
                                    <label class="cozumproblem col-md-7 col-xs-12" style="padding-top: 7px"></label>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-xs-12">Çözümü</label>
                                    <label class="cozumdetay col-md-7 col-xs-12" style="padding-top: 7px"></label>
                                </div>
                            </div>
                        </form>
                        <!-- END FORM-->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Tamam</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="konu-devret" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-pencil"></i>Konu Kime Devredilecek?
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_8" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Yapılan İşlem Düzenle</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-xs-12">Devredilecek Kişi <span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right col-md-7 col-xs-12">
                                                <select class="form-control select2me select2-offscreen" id="options8" name="options8" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-xs-12" style="text-align: center">
                                            <button id="konudevret" type="button" class="btn green">Devret</button>
                                            <button type="button" id="closeButton7" class="btn default">Vazgeç</button>
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
@stop
