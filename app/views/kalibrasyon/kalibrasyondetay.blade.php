@extends('layout.master')

@section('page-title')
    <div class="page-title">
        <h1>Kalibrasyon <small>Detay Ekranı</small></h1>
    </div>
@stop

@section('page-plugins')
    <link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::to('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::to('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" >
@stop

@section('page-styles')
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
    <script src="{{ URL::to('assets/global/plugins/jquery.quicksearch.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/datatables/datatables.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.js') }}" type="text/javascript"></script>
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
        jQuery.fn.DataTable.ext.type.search.string = function(data) {
            return !data ? '' : typeof data === 'string' ? data.replace(/Ç/g, 'c').replace(/İ/g, 'i').replace(/Ğ/g, 'g').replace(/Ö/g, 'o').replace(/Ş/g, 's').replace(/Ü/g, 'u').toLowerCase().replace(/ç/g, 'c').replace(/ı/g, 'i').replace(/ğ/g, 'g').replace(/ö/g, 'o').replace(/ş/g, 's').replace(/ü/g, 'u') : data;
        };
        var table = $('#sample_editable_1');
        var oTable = table.DataTable({
            "sPaginationType": "simple_numbers",
            "bProcessing": true,
            "ajax": {
                "url": "{{ URL::to('kalibrasyon/kalibrasyondetaylist') }}",
                "type": "POST",
                "data": {
                    "grup_id" : "@if(isset($grup)){{$grup->id}}@endif"
                }
            },
            "bServerSide": true,
            "fnDrawCallback" : function() {
            },
            "aaSorting": [[4,'asc']],
            "columnDefs": [ { targets: [ 2 ], orderData: [ 2, 4, 0 ] },
                { targets: [ 3 ], orderData: [ 3, 4, 0 ] },
                { targets: [ 4 ], orderData: [ 4, 0 ] },
                { targets: [ 5 ], orderData: [ 5, 4, 0 ] },
                { targets: [ 6 ], orderData: [ 6, 4, 0 ] }
            ],
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
                "zeroRecords": "Eşleşen Kayıt Bulunmadı",
                "processing": "<h1><i class='fa fa-spinner fa-spin icon-lg-processing fa-fw'></i>İşlem Devam Ediyor...</h1>"

            },
            "columns": [
                {data: 'id', name: 'kalibrasyon.id',"class":"id","orderable": true, "searchable": true},
                {data: 'kalibrasyon_seri', name: 'kalibrasyon.kalibrasyon_seri',"orderable": true, "searchable": true},
                {data: 'sayacadi', name: 'sayacadi.sayacadi',"orderable": true, "searchable": false},
                {data: 'imalyili', name: 'kalibrasyon.imalyili',"orderable": true, "searchable": true},
                {data: 'gdurum', name: 'kalibrasyon.gdurum',"orderable": true, "searchable": false},
                {data: 'adi_soyadi', name: 'kullanici.adi_soyadi',"orderable": true, "searchable": false},
                {data: 'kalibrasyontarih', name: 'kalibrasyon.kalibrasyontarih',"orderable": true, "searchable": false},
                {data: 'gkalibrasyontarih', name: 'kalibrasyon.gkalibrasyontarih',"visible": false, "searchable": true},
                {data: 'nsayacadi', name: 'sayacadi.nsayacadi',"visible": false, "searchable": true},
                {data: 'ndurum', name: 'kalibrasyon.ndurum',"visible": false, "searchable": true},
                {data: 'nadi_soyadi', name: 'kullanici.nadi_soyadi',"visible": false, "searchable": true},
                {data: 'islemler', name: 'islemler',"orderable": false, "searchable": false}
            ],
            "lengthMenu": [
                [10, 15, 20, 99999999],
                [10, 15, 20, "Hepsi"]
            ],
            "searchDelay": 0,
            "bFilter": true,
            "stateSave":true
        });
        $('<label>Kriter: </label><select style="height: 34px;margin-left: 5px;border-radius: 4px;padding-top:2px;padding-right: 10px" id="kriter" tabindex="-1" title="" class="select2me">'+
            '<option value="">Tamamı</option>'+
            '<option value="0">Id</option>'+
            '<option value="1">Seri Numarası</option>'+
            '<option value="8">Sayaç Adı</option>'+
            '<option value="3">İmalat Yılı</option>'+
            '<option value="9">Durum</option>'+
            '<option value="10">Kullanıcı Adı</option>'+
            '<option value="7">Kayıt Tarihi</option>'+
            '</select><input class="hide" id="search">').insertBefore('#sample_editable_1_filter label');
        $('#sample_editable_1_filter input[type=search]').unbind();
        $('#sample_editable_1_filter input[type=search]').bind('keyup', function(e) {
            if(e.keyCode === 13) {
                var kriter=$('#kriter').val();
                var search=jQuery.fn.DataTable.ext.type.search.string(this.value);
                $('#search').val(search);
                if(kriter!==""){
                    oTable.search( '' ).columns().search( '' );
                    oTable.column(kriter).search(search).draw();
                }
                else{
                    oTable.columns().search( '' );
                    oTable.search(search).draw();
                }
            }
        });
        var state = oTable.state.loaded();
        if (state) {
            var search=state.search;
            if(search.search){
                var globalSearch=search.search;
                $('#kriter').val('');
                $('#sample_editable_1_filter input[type=search]').val(globalSearch);
                $('#search').val(globalSearch);
            }else{
                oTable.columns().eq(0).each(function (colIdx) {
                    var colSearch = state.columns[colIdx].search;
                    if (colSearch.search) {
                        $('#kriter').val(colIdx);
                        $('#sample_editable_1_filter input[type=search]').val(colSearch.search);
                        $('#search').val(colSearch.search);
                    }
                });
            }
        }
        table.on('draw.dt', function() {
            $('#sample_editable_1_filter input[type=search]').val($('#search').val());
        });
        var tableWrapper = jQuery('#sample_editable_1_wrapper');
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
        oTable.draw();
    </script>
    <script>
        $(document).ready(function() {
            $(document).on("click", ".goster", function () {
                $.blockUI();
                var id = $(this).data('id');
                $.getJSON(" {{ URL::to('kalibrasyon/kalibrasyonbilgi') }}",{id:id}, function (event) {
                    if (event.durum) {
                        var kalibrasyon = event.kalibrasyon;
                        var istasyon = event.istasyon;
                        var kullanici = event.kullanici;
                        var kalibrasyonstandart = event.kalibrasyonstandart;
                        $('.serino').text(kalibrasyon.kalibrasyon_seri);
                        $('.istasyonadi').text(istasyon.istasyonadi);
                        $('.sayacadi').text(kalibrasyon.sayacadi.sayacadi);
                        $('.imalyili').text(kalibrasyon.imalyili);
                        $('.hassasiyet').text(kalibrasyonstandart.hassasiyet);
                        $('.kalibrasyonsayi').text(kalibrasyon.kalibrasyonsayisi);
                        $('.kullanici').text(kullanici.adi_soyadi);
                        $('.kayittarihi').text(kalibrasyon.kalibrasyontarih);
                        $('.durum').text(kalibrasyon.durum);
                        $('.sayaclar .sayaclar_ek').remove();
                        var Sayac = "";
                        switch (kalibrasyonstandart.noktasayisi) {
                            case "3" :
                                $('.lfhf').addClass('hide');
                                Sayac = '<table class="col-md-10 col-md-offset-1 sayaclar_ek">' +
                                    '<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b>Seri No</b></td><td><b>Ölçüm</b></td><td><b>LF Sonuç</b></td></tr>' +
                                    '<tr><td>' + kalibrasyon.kalibrasyon_seri + '</td><td>' + kalibrasyonstandart.nokta1 + '</td><td>' + kalibrasyon.sonuc1.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta2 + '</td><td>' + kalibrasyon.sonuc2.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta3 + '</td><td>' + kalibrasyon.sonuc3.replace('.', ',') + '</td></tr>' +
                                    '</table>';
                                break;
                            case "4" :
                                $('.lfhf').removeClass('hide');
                                $('#hf2').attr('checked', (kalibrasyon.hf2 === true));
                                $('#hf3').attr('checked', (kalibrasyon.hf3 === true));
                                $('#hf32').attr('checked', (kalibrasyon.hf32 === true));
                                Sayac = '<table class="col-md-10 col-md-offset-1 sayaclar_ek">' +
                                    '<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b>Seri No</b></td><td><b>Ölçüm</b></td><td><b>LF Sonuç</b></td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '"><b>HF2 Sonuç</b></td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '"><b>HF3-I Sonuç</b></td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '"><b>HF3-II Sonuç</b></td></tr>' +
                                    '<tr><td>' + kalibrasyon.kalibrasyon_seri + '</td><td>' + kalibrasyonstandart.nokta1 + '</td><td>' + kalibrasyon.sonuc1.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc1.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc1.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc1.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta2 + '</td><td>' + kalibrasyon.sonuc2.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc2.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc2.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc2.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta3 + '</td><td>' + kalibrasyon.sonuc3.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc3.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc3.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc3.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta4 + '</td><td>' + kalibrasyon.sonuc4.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc4.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc4.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc4.replace('.', ',') + '</td></tr>' +
                                    '</table>';
                                break;
                            case "5" :
                                $('.lfhf').removeClass('hide');
                                $('#hf2').attr('checked', (kalibrasyon.hf2 === true));
                                $('#hf3').attr('checked', (kalibrasyon.hf3 === true));
                                $('#hf32').attr('checked', (kalibrasyon.hf32 === true));
                                Sayac = '<table class="col-md-10 col-md-offset-1 sayaclar_ek">' +
                                    '<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b>Seri No</b></td><td><b>Ölçüm</b></td><td><b>LF Sonuç</b></td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '"><b>HF2 Sonuç</b></td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '"><b>HF3-I Sonuç</b></td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '"><b>HF3-II Sonuç</b></td></tr>' +
                                    '<tr><td>' + kalibrasyon.kalibrasyon_seri + '</td><td>' + kalibrasyonstandart.nokta1 + '</td><td>' + kalibrasyon.sonuc1.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc1.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc1.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc1.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta2 + '</td><td>' + kalibrasyon.sonuc2.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc2.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc2.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc2.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta3 + '</td><td>' + kalibrasyon.sonuc3.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc3.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc3.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc3.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta4 + '</td><td>' + kalibrasyon.sonuc4.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc4.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc4.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc4.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta5 + '</td><td>' + kalibrasyon.sonuc5.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc5.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc5.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc5.replace('.', ',') + '</td></tr>' +
                                    '</table>';
                                break;
                            case "6" :
                                $('.lfhf').removeClass('hide');
                                $('#hf2').attr('checked', (kalibrasyon.hf2 === true));
                                $('#hf3').attr('checked', (kalibrasyon.hf3 === true));
                                $('#hf32').attr('checked', (kalibrasyon.hf32 === true));
                                Sayac = '<table class="col-md-10 col-md-offset-1 sayaclar_ek">' +
                                    '<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b>Seri No</b></td><td><b>Ölçüm</b></td><td><b>LF Sonuç</b></td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '"><b>HF2 Sonuç</b></td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '"><b>HF3-I Sonuç</b></td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '"><b>HF3-II Sonuç</b></td></tr>' +
                                    '<tr><td>' + kalibrasyon.kalibrasyon_seri + '</td><td>' + kalibrasyonstandart.nokta1 + '</td><td>' + kalibrasyon.sonuc1.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc1.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc1.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc1.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta2 + '</td><td>' + kalibrasyon.sonuc2.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc2.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc2.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc2.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta3 + '</td><td>' + kalibrasyon.sonuc3.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc3.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc3.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc3.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta4 + '</td><td>' + kalibrasyon.sonuc4.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc4.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc4.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc4.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta5 + '</td><td>' + kalibrasyon.sonuc5.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc5.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc5.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc5.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta6 + '</td><td>' + kalibrasyon.sonuc6.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc6.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc6.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc6.replace('.', ',') + '</td></tr>' +
                                    '</table>';
                                break;
                            case "7" :
                                $('.lfhf').removeClass('hide');
                                $('#hf2').attr('checked', (kalibrasyon.hf2 === true));
                                $('#hf3').attr('checked', (kalibrasyon.hf3 === true));
                                $('#hf32').attr('checked', (kalibrasyon.hf32 === true));
                                Sayac = '<table class="col-md-10 col-md-offset-1 sayaclar_ek">' +
                                '<tr style="text-align: left;font-size:14px"><td style="width: 100px"><b>Seri No</b></td><td><b>Ölçüm</b></td><td><b>LF Sonuç</b></td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '"><b>HF2 Sonuç</b></td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '"><b>HF3-I Sonuç</b></td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '"><b>HF3-II Sonuç</b></td></tr>' +
                                    '<tr><td>' + kalibrasyon.kalibrasyon_seri + '</td><td>' + kalibrasyonstandart.nokta1 + '</td><td>' + kalibrasyon.sonuc1.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc1.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc1.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc1.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta2 + '</td><td>' + kalibrasyon.sonuc2.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc2.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc2.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc2.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta3 + '</td><td>' + kalibrasyon.sonuc3.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc3.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc3.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc3.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta4 + '</td><td>' + kalibrasyon.sonuc4.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc4.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc4.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc4.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta5 + '</td><td>' + kalibrasyon.sonuc5.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc5.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc5.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc5.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta6 + '</td><td>' + kalibrasyon.sonuc6.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc6.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc6.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc6.replace('.', ',') + '</td></tr>' +
                                    '<tr><td></td><td>' + kalibrasyonstandart.nokta7 + '</td><td>' + kalibrasyon.sonuc7.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf2 === false ? 'hide' : '') + '">' + kalibrasyon.hf2sonuc7.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf3 === false ? 'hide' : '') + '">' + kalibrasyon.hf3sonuc7.replace('.', ',') + '</td><td class="' + (kalibrasyon.hf32 === false ? 'hide' : '') + '">' + kalibrasyon.hf32sonuc7.replace('.', ',') + '</td></tr>' +
                                    '</table>';
                                break;
                        }
                        $('.lfhf').find('input:checkbox').uniform();
                        $.uniform.update();
                        $('.sayaclar').html(Sayac);
                    } else {
                        $('#detay-goster').modal('hide');
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            });
            $(document).on("click", ".hurdagoster", function () {
                $.blockUI();
                var id = $(this).data('id');
                $.getJSON("{{ URL::to('kalibrasyon/hurdabilgi') }}",{id:id}, function (event) {
                    if (event.durum) {
                        var kalibrasyon = event.kalibrasyon;
                        var kullanici = event.kullanici;
                        var hurdanedeni = event.hurdanedeni;
                        $('.serino').text(kalibrasyon.kalibrasyon_seri);
                        $('.hurdanedeni').text(hurdanedeni.nedeni);
                        $('.kullanici').text(kullanici.adi_soyadi);
                        $('.kayittarihi').text(kalibrasyon.kalibrasyontarih);
                    } else {
                        $('#hurda-goster').modal('hide');
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            });
            $('#kriter').select2();
        });
    </script>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN TABLE PORTLET-->
            <div class="portlet box">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cog"></i>Kalibrasyon Bilgileri
                    </div>
                    <div class="actions">
                        {{--<a class="btn btn-default btn-sm kayitgirisiexcel" data-toggle="modal" data-target="#kayitgirisiexcel">
                            <i class="fa fa-file-excel-o"></i> Kalibrasyon Girişi Excelden Aktar</a>
                        <a class="btn btn-default btn-sm hurdagirisiexcel" data-toggle="modal" data-target="#hurdagirisiexcel">
                            <i class="fa fa-file-excel-o"></i> Hurda Girişi Excelden Aktar </a>--}}
                        <a href="{{ URL::to('kalibrasyon/kayitgirisi/'.$grup->id.'') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-pencil"></i> Yeni Kalibrasyon Girişi </a>
                        <a href="{{ URL::to('kalibrasyon/hurdagirisi/'.$grup->id.'') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-pencil"></i> Hurda Girişi </a>
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form action="{{ URL::to('kalibrasyon/kalibrasyon') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
                        <div class="form-body">
                            <div class="form-group col-xs-12">
                                <label class="control-label col-sm-2 col-xs-4"><b>Cari Adı:</b></label>
                                <label class="col-sm-10 col-xs-8" style="padding-top: 9px">{{ $grup->netsiscari->cariadi}}</label>
                            </div>
                            <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Seri Numarası</th>
                                    <th>Sayaç Adı</th>
                                    <th>İmalat Yılı</th>
                                    <th>Durum</th>
                                    <th>Kullanıcı Adı</th>
                                    <th>Kayıt Tarihi</th>
                                    <th></th><th></th><th></th><th></th>
                                    <th>İşlemler</th>
                                </tr>
                                </thead>
                            </table>
                            <div class="form-group">
                                <div class="col-xs-offset-4 col-xs-4"><a href="{{ URL::to('kalibrasyon/kalibrasyon')}}" class="btn default">Tamam</a></div>
                            </div>
                        </div>
                    </form>
                 </div>
            </div>
            <!-- END TABLE PORTLET-->
        </div>
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
                                    <i class="fa fa-check"></i>Kalibrasyon Bilgisi Detaylı Açıklaması
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_2" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Kalibrasyon Bilgisi</h3>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Seri No:</label>
                                            <label class="col-xs-8 serino" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">İstasyon Adı:</label>
                                            <label class="col-xs-8 istasyonadi" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Sayaç Adı:</label>
                                            <label class="col-xs-8 sayacadi" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">İmal Yılı:</label>
                                            <label class="col-xs-8 imalyili" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Hassasiyet:</label>
                                            <label class="col-xs-8 hassasiyet" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Kalibrasyon Sayısı:</label>
                                            <label class="col-xs-8 kalibrasyonsayi" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Kayıt Yapan:</label>
                                            <label class="col-xs-8 kullanici" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Kayıt Tarihi:</label>
                                            <label class="col-xs-8 kayittarihi" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-4">Durum:</label>
                                            <label class="col-xs-8 durum" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group lfhf hide">
                                            <h4 style="padding-left: 30px;">Test Frekansları</h4>
                                            <div class="form-group lfhf_ek">
                                                <label class="control-label col-xs-2"><input type="checkbox" id=lf name="lf" checked disabled/> LF </label>
                                                <label class="control-label col-xs-2"><input type="checkbox" id=hf2 name="hf2" disabled/> HF2 </label>
                                                <label class="control-label col-xs-2"><input type="checkbox" id=hf3 name="hf3" disabled/> HF3-I </label>
                                                <label class="control-label col-xs-2"><input type="checkbox" id=hf32 name="hf32" disabled/> HF3-II </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-xs-6"> Kalibrasyon Bilgileri</label>
                                        </div>
                                        <div class="form-group sayaclar">

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
    <div class="modal fade" id="hurda-goster" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-check"></i>Hurda Sayaç Kayıdı Detaylı Bilgisi
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" id="form_sample_3" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h3 class="form-section">Kayıt Bilgisi</h3>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Seri No:</label>
                                            <label class="col-xs-8 serino" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Hurda Nedeni:</label>
                                            <label class="col-xs-8 hurdanedeni" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Kayıt Yapan:</label>
                                            <label class="col-xs-8 kullanici" style="padding-top: 7px"></label>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <label class="control-label col-xs-4">Kayıt Tarihi:</label>
                                            <label class="col-xs-8 kayittarihi" style="padding-top: 7px"></label>
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
@stop
