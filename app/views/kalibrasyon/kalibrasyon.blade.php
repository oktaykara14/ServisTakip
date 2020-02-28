@extends('layout.master')

@section('page-title')
    <!--suppress JSCheckFunctionSignatures -->
    <div class="page-title">
        <h1>Kalibrasyon <small>Bilgi Ekranı</small></h1>
    </div>
@stop

@section('page-plugins')
    <link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::to('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" >
@stop

@section('page-styles')
@stop

@section('page-js')
    <script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
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
                "url": "{{ URL::to('kalibrasyon/kalibrasyonlist') }}",
                "type": "POST",
                "data": {
                    "hatirlatma_id" : "@if(isset($hatirlatma_id)){{$hatirlatma_id}}@endif"
                }
            },
            "bServerSide": true,
            "aaSorting": [[5,'desc']],
            "columnDefs": [ { targets: [ 1 ], orderData: [ 1, 0 ] },
                { targets: [ 2 ], orderData: [ 2, 0 ] },
                { targets: [ 3 ], orderData: [ 3, 0 ] },
                { targets: [ 4 ], orderData: [ 4, 0 ] },
                { targets: [ 5 ], orderData: [ 5, 0 ] }
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
                {data: 'id', name: 'kalibrasyongrup.id',"class":"id","orderable": true, "searchable": true},
                {data: 'cariadi', name: 'netsiscari.cariadi',"orderable": true, "searchable": false},
                {data: 'adet', name: 'kalibrasyongrup.adet',"orderable": true, "searchable": true},
                {data: 'biten', name: 'kalibrasyongrup.biten',"orderable": true, "searchable": true},
                {data: 'gdurum', name: 'kalibrasyongrup.gdurum',"orderable": true, "searchable": false},
                {data: 'kayittarihi', name: 'kalibrasyongrup.kayittarihi',"orderable": true, "searchable": false},
                {data: 'gkayittarihi', name: 'kalibrasyongrup.gkayittarihi',"visible": false, "searchable": true},
                {data: 'ncariadi', name: 'netsiscari.ncariadi',"visible": false, "searchable": true},
                {data: 'ndurum', name: 'kalibrasyongrup.ndurum',"visible": false, "searchable": true},
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
            '<option value="7">Cari Adı</option>'+
            '<option value="2">Adet</option>'+
            '<option value="3">Biten</option>'+
            '<option value="8">Durum</option>'+
            '<option value="6">Kayıt Tarihi</option>'+
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

    </script>
    <script>
        $('#kayitnoktasayi').on('change',function() {
            var noktasayi = $(this).val();
            var text="";
            var hf2=$('#kayithf3').attr('checked') ? 1 : 0;
            var hf3=$('#kayithf3').attr('checked') ? 1 : 0;
            var hf32=$('#kayithf32').attr('checked') ? 1 : 0;
            if(noktasayi!==""){
                switch(noktasayi){
                    case '3':  $('.kayitinfo').text('Sayaç No | Nokta1 | Nokta2 | Nokta3 ');
                        break;
                    case '5':
                        text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 ";
                        if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 ";
                        if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 ";
                        if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 ";
                        $('.kayitinfo').text(text);
                        break;
                    case '6':
                        text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 | Nokta6 ";
                        if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 | Hf2Nokta6 ";
                        if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 | Hf3Nokta6 ";
                        if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 | Hf32Nokta6 ";
                        $('.kayitinfo').text(text);
                        break;
                    case '7':
                        text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 | Nokta6 | Nokta7 ";
                        if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 | Hf2Nokta6 | Hf2Nokta7 ";
                        if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 | Hf3Nokta6 | Hf3Nokta7 ";
                        if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 | Hf32Nokta6 | Hf32Nokta7 ";
                        $('.kayitinfo').text(text);
                        break;
                }
                if(noktasayi>3)
                {
                    $('.kayitlfhf').removeClass('hide');
                }else{
                    $('.kayitlfhf').addClass('hide');
                }
                $('.kayitexceldiv').removeClass('hide');
            }else{
                $('.kayitexceldiv').addClass('hide');
            }
        });
        $('#kayithf2').on('change',function() {
            var hf2=$(this).attr('checked') ? 1 : 0;
            var hf3=$('#kayithf3').attr('checked') ? 1 : 0;
            var hf32=$('#kayithf32').attr('checked') ? 1 : 0;
            var noktasayi = $('#kayitnoktasayi').val();
            var text="";
            switch(noktasayi){
                case '5':
                    text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 ";
                    if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 ";
                    if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 ";
                    if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 ";
                    $('.kayitinfo').text(text);
                    break;
                case '6':
                    text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 | Nokta6 ";
                    if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 | Hf2Nokta6 ";
                    if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 | Hf3Nokta6 ";
                    if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 | Hf32Nokta6 ";
                    $('.kayitinfo').text(text);
                    break;
                case '7':
                    text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 | Nokta6 | Nokta7 ";
                    if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 | Hf2Nokta6 | Hf2Nokta7 ";
                    if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 | Hf3Nokta6 | Hf3Nokta7 ";
                    if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 | Hf32Nokta6 | Hf32Nokta7 ";
                    $('.kayitinfo').text(text);
                    break;
            }
        });
        $('#kayithf3').on('change',function() {
            var hf3=$(this).attr('checked') ? 1 : 0;
            var hf2=$('#kayithf2').attr('checked') ? 1 : 0;
            var hf32=$('#kayithf32').attr('checked') ? 1 : 0;
            var noktasayi = $('#kayitnoktasayi').val();
            var text="";
            switch(noktasayi){
                case '5':
                    text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 ";
                    if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 ";
                    if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 ";
                    if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 ";
                    $('.kayitinfo').text(text);
                    break;
                case '6':
                    text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 | Nokta6 ";
                    if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 | Hf2Nokta6 ";
                    if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 | Hf3Nokta6 ";
                    if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 | Hf32Nokta6 ";
                    $('.kayitinfo').text(text);
                    break;
                case '7':
                    text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 | Nokta6 | Nokta7 ";
                    if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 | Hf2Nokta6 | Hf2Nokta7 ";
                    if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 | Hf3Nokta6 | Hf3Nokta7 ";
                    if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 | Hf32Nokta6 | Hf32Nokta7 ";
                    $('.kayitinfo').text(text);
                    break;
            }
        });
        $('#kayithf32').on('change',function() {
            var hf32=$(this).attr('checked') ? 1 : 0;
            var hf2=$('#kayithf2').attr('checked') ? 1 : 0;
            var hf3=$('#kayithf3').attr('checked') ? 1 : 0;
            var noktasayi = $('#kayitnoktasayi').val();
            var text="";
            switch(noktasayi){
                case '5':
                    text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 ";
                    if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 ";
                    if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 ";
                    if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 ";
                    $('.kayitinfo').text(text);
                    break;
                case '6':
                    text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 | Nokta6 ";
                    if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 | Hf2Nokta6 ";
                    if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 | Hf3Nokta6 ";
                    if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 | Hf32Nokta6 ";
                    $('.kayitinfo').text(text);
                    break;
                case '7':
                    text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 | Nokta6 | Nokta7 ";
                    if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 | Hf2Nokta6 | Hf2Nokta7 ";
                    if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 | Hf3Nokta6 | Hf3Nokta7 ";
                    if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 | Hf32Nokta6 | Hf32Nokta7 ";
                    $('.kayitinfo').text(text);
                    break;
            }
        });
        $('#kayitexcelekle').click(function () {
            var fileControl = document.getElementById('kayitexcel');
            if(fileControl.files.length === 0){
                alert('Excel Dosyası Seçilmedi!');
                return false;
            }
            var formData = new FormData();
            formData.append('file',fileControl.files[0]);
            formData.append('kayitnoktasayi',$('#kayitnoktasayi').val());
            formData.append('kayitgrupid',$('#kayitgrupid').val());
            formData.append('kayithf2',$('#kayithf2').attr('checked') ? 1 : 0);
            formData.append('kayithf3',$('#kayithf3').attr('checked') ? 1 : 0);
            formData.append('kayithf32',$('#kayithf32').attr('checked') ? 1 : 0);
            $.blockUI();
            $.ajax({
                url: '{{ URL::to('kalibrasyon/kalibrasyonexcel') }}',
                type: 'POST',
                dataType: 'json',
                data: formData,
                async: true,
                cache: false,
                contentType: false,
                enctype: 'multipart/form-data',
                processData: false,
                success: function (event) {
                    $.unblockUI();
                    if (!event.durum)
                        toastr[event.type](event.text, event.title);
                    else {
                        toastr[event.type](event.text, event.title);
                        oTable.ajax.reload();
                        var errors=event.errors;
                        if(errors.length>0)
                            alert('Hatalı Kayıtlar : '+ errors.toString());
                    }
                },
                error: function (request) {
                    $.unblockUI();
                    alert(request.responseText);
                }
            });
        });
        $('#hurdanoktasayi').on('change',function() {
            var noktasayi = $(this).val();
            var hf2=$('#hurdahf3').attr('checked') ? 1 : 0;
            var hf3=$('#hurdahf3').attr('checked') ? 1 : 0;
            var hf32=$('#hurdahf32').attr('checked') ? 1 : 0;
            var text="";
            if(noktasayi!==""){
                switch(noktasayi){
                    case '3':  $('.hurdainfo').text('Sayaç No | Nokta1 | Nokta2 | Nokta3 | Açıklama ');
                        break;
                    case '5':
                        text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 ";
                        if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 ";
                        if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 ";
                        if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 ";
                        text +='| Açıklama';
                        $('.hurdainfo').text(text);
                        break;
                    case '6':
                        text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 | Nokta6 ";
                        if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 | Hf2Nokta6 ";
                        if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 | Hf3Nokta6 ";
                        if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 | Hf32Nokta6 ";
                        text +='| Açıklama';
                        $('.hurdainfo').text(text);
                        break;
                    case '7':
                        text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 | Nokta6 | Nokta7 ";
                        if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 | Hf2Nokta6 | Hf2Nokta7 ";
                        if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 | Hf3Nokta6 | Hf3Nokta7 ";
                        if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 | Hf32Nokta6 | Hf32Nokta7 ";
                        text +='| Açıklama';
                        $('.hurdainfo').text(text);
                        break;
                }
                if(noktasayi>3)
                {
                    $('.hurdalfhf').removeClass('hide');
                }else{
                    $('.hurdalfhf').addClass('hide');
                }
                $('.hurdaexceldiv').removeClass('hide');
            }else{
                $('.hurdaexceldiv').addClass('hide');
            }
        });
        $('#hurdahf2').on('change',function() {
            var hf2=$(this).attr('checked') ? 1 : 0;
            var hf3=$('#hurdahf3').attr('checked') ? 1 : 0;
            var hf32=$('#hurdahf32').attr('checked') ? 1 : 0;
            var noktasayi = $('#hurdanoktasayi').val();
            var text="";
            switch(noktasayi){
                case '5':
                    text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 ";
                    if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 ";
                    if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 ";
                    if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 ";
                    text +='| Açıklama';
                    $('.hurdainfo').text(text);
                    break;
                case '6':
                    text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 | Nokta6 ";
                    if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 | Hf2Nokta6 ";
                    if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 | Hf3Nokta6 ";
                    if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 | Hf32Nokta6 ";
                    text +='| Açıklama';
                    $('.hurdainfo').text(text);
                    break;
                case '7':
                    text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 | Nokta6 | Nokta7 ";
                    if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 | Hf2Nokta6 | Hf2Nokta7 ";
                    if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 | Hf3Nokta6 | Hf3Nokta7 ";
                    if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 | Hf32Nokta6 | Hf32Nokta7 ";
                    text +='| Açıklama';
                    $('.hurdainfo').text(text);
                    break;
            }
        });
        $('#hurdahf3').on('change',function() {
            var hf3=$(this).attr('checked') ? 1 : 0;
            var hf2=$('#hurdahf2').attr('checked') ? 1 : 0;
            var hf32=$('#hurdahf32').attr('checked') ? 1 : 0;
            var noktasayi = $('#hurdanoktasayi').val();
            var text="";
            switch(noktasayi){
                case '5':
                    text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 ";
                    if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 ";
                    if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 ";
                    if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 ";
                    text +='| Açıklama';
                    $('.hurdainfo').text(text);
                    break;
                case '6':
                    text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 | Nokta6 ";
                    if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 | Hf2Nokta6 ";
                    if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 | Hf3Nokta6 ";
                    if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 | Hf32Nokta6 ";
                    text +='| Açıklama';
                    $('.hurdainfo').text(text);
                    break;
                case '7':
                    text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 | Nokta6 | Nokta7 ";
                    if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 | Hf2Nokta6 | Hf2Nokta7 ";
                    if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 | Hf3Nokta6 | Hf3Nokta7 ";
                    if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 | Hf32Nokta6 | Hf32Nokta7 ";
                    text +='| Açıklama';
                    $('.hurdainfo').text(text);
                    break;
            }
        });
        $('#hurdahf32').on('change',function() {
            var hf32=$(this).attr('checked') ? 1 : 0;
            var hf2=$('#hurdahf2').attr('checked') ? 1 : 0;
            var hf3=$('#hurdahf3').attr('checked') ? 1 : 0;
            var noktasayi = $('#hurdanoktasayi').val();
            var text="";
            switch(noktasayi){
                case '5':
                    text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 ";
                    if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 ";
                    if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 ";
                    if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 ";
                    text +='| Açıklama';
                    $('.hurdainfo').text(text);
                    break;
                case '6':
                    text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 | Nokta6 ";
                    if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 | Hf2Nokta6 ";
                    if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 | Hf3Nokta6 ";
                    if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 | Hf32Nokta6 ";
                    text +='| Açıklama';
                    $('.hurdainfo').text(text);
                    break;
                case '7':
                    text="Sayaç No | Nokta1 | Nokta2 | Nokta3 | Nokta4 | Nokta5 | Nokta6 | Nokta7 ";
                    if(hf2)  text +="| Hf2Nokta1 | Hf2Nokta2 | Hf2Nokta3 | Hf2Nokta4 | Hf2Nokta5 | Hf2Nokta6 | Hf2Nokta7 ";
                    if(hf3)  text +="| Hf3Nokta1 | Hf3Nokta2 | Hf3Nokta3 | Hf3Nokta4 | Hf3Nokta5 | Hf3Nokta6 | Hf3Nokta7 ";
                    if(hf32) text +="| Hf32Nokta1 | Hf32Nokta2 | Hf32Nokta3 | Hf32Nokta4 | Hf32Nokta5 | Hf32Nokta6 | Hf32Nokta7 ";
                    text +='| Açıklama';
                    $('.hurdainfo').text(text);
                    break;
            }
        });
        $('#hurdaexcelekle').click(function () {
            var fileControl = document.getElementById('hurdaexcel');
            if(fileControl.files.length === 0){
                alert('Excel Dosyası Seçilmedi!');
                return false;
            }
            var formData = new FormData();
            formData.append('file',fileControl.files[0]);
            formData.append('hurdanoktasayi',$('#hurdanoktasayi').val());
            formData.append('hurdagrupid',$('#hurdagrupid').val());
            formData.append('hurdahf2',$('#hurdahf2').attr('checked') ? 1 : 0);
            formData.append('hurdahf3',$('#hurdahf3').attr('checked') ? 1 : 0);
            formData.append('hurdahf32',$('#hurdahf32').attr('checked') ? 1 : 0);
            $.blockUI();
            $.ajax({
                url: '{{ URL::to('kalibrasyon/hurdaexcel') }}',
                type: 'POST',
                dataType: 'json',
                data: formData,
                async: true,
                cache: false,
                contentType: false,
                enctype: 'multipart/form-data',
                processData: false,
                success: function (event) {
                    $.unblockUI();
                    if (!event.durum)
                        toastr[event.type](event.text, event.title);
                    else {
                        toastr[event.type](event.text, event.title);
                        oTable.ajax.reload();
                        var errors=event.errors;
                        if(errors.length>0)
                            alert('Hatalı Kayıtlar : '+ errors.toString());
                    }
                },
                error: function (request) {
                    $.unblockUI();
                    alert(request.responseText);
                }
            });
        });
        $(document).ready(function() {
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
                        <a class="btn btn-default btn-sm kayitgirisiexcel" data-toggle="modal" data-target="#kayitgirisiexcel">
                            <i class="fa fa-file-excel-o"></i> Kalibrasyon Girişi Excelden Aktar</a>
                        <a class="btn btn-default btn-sm hurdagirisiexcel" data-toggle="modal" data-target="#hurdagirisiexcel">
                            <i class="fa fa-file-excel-o"></i> Hurda Girişi Excelden Aktar </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Cari Adı</th>
                            <th>Adet</th>
                            <th>Biten</th>
                            <th>Durum</th>
                            <th>Kayıt Tarihi</th>
                            <th></th><th></th><th></th>
                            <th>İşlemler</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- END TABLE PORTLET-->
        </div>
    </div>
@stop

@section('modal')
    <div class="modal fade" id="kayitgirisiexcel" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Excelden Kayıt Girişi
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" target="frame" id="form_sample_4" class="form-horizontal" method="POST" novalidate="novalidate" enctype="multipart/form-data">
                                    <div class="form-body col-xs-12">
                                        <h3 class="form-section col-xs-12">Excel üzerinden Kayıt Girişi</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group col-sm-4 col-xs-12">
                                            <label class="control-label col-xs-6">Ölçüm Nokta Sayısı:</label>
                                            <div class="col-xs-6">
                                                <select class="form-control select2me select2-offscreen kayitnoktasayi" id="kayitnoktasayi" name="kayitnoktasayi" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                    <option value="3">3</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-8 col-xs-12 kayitlfhf hide">
                                            <label class="control-label col-xs-4">Test Frekansları:</label>
                                            <div class="form-group col-xs-8">
                                                <label class="control-label col-xs-3"><input type="checkbox" id=kayitlf name="kayitlf" checked disabled/> LF </label>
                                                <label class="control-label col-xs-3"><input type="checkbox" id=kayithf2 name="kayithf2" /> HF2 </label>
                                                <label class="control-label col-xs-3"><input type="checkbox" id=kayithf3 name="kayithf3" /> HF3-I </label>
                                                <label class="control-label col-xs-3"><input type="checkbox" id=kayithf32 name="kayithf32" /> HF3-II </label>
                                            </div>
                                        </div>
                                        <div class="form-group col-xs-12 hide kayitexceldiv">
                                            <h4>Excel İlk Satırında Şu Alanlar Olmalıdır:</h4>
                                            <h4 class="kayitinfo" style="line-height: 25px">Sayaç No | Nokta1 | Nokta2 | Nokta3 </h4>
                                            <div class="form-group">
                                                <label class="control-label col-xs-2">Excel</label>
                                                <div class="col-sm-8 col-xs-7 fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="input-group input-large">
                                                        <div class="form-control uneditable-input input-fixed input-xlarge" data-trigger="fileinput">
                                                            <i class="fa fa-file-excel-o fileinput-exists"></i><span class="fileinput-filename" style="margin-left: 5px"></span>
                                                        </div>
                                                        <span class="input-group-addon btn default btn-file" style="border:1px solid #969696">
                                                    <span class="fileinput-new">
                                                    Excel Seç </span>
                                                    <span class="fileinput-exists">
                                                    Değiştir </span>
                                                    <input type="file" id="kayitexcel" name="kayitexcel" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                                                    </span>
                                                        <a href="" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                            Sil </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-offset-3 col-xs-9">
                                                    <a id="kayitexcelekle" href="#" type="button" data-dismiss="modal" class="btn green">Ekle</a>
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
    <div class="modal fade" id="hurdagirisiexcel" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="portlet box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-plus"></i>Excelden Hurda Girişi
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="" target="frame" id="form_sample_5" class="form-horizontal" method="POST" novalidate="novalidate" enctype="multipart/form-data">
                                    <div class="form-body col-xs-12">
                                        <h3 class="form-section col-xs-12">Excel üzerinden Hurda Girişi</h3>
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Girilen Bilgilerde Hata Var.
                                        </div>
                                        <div class="alert alert-success display-hide">
                                            <button class="close" data-close="alert"></button>
                                            Bilgiler Doğru!
                                        </div>
                                        <div class="form-group col-sm-4 col-xs-12">
                                            <label class="control-label col-xs-8">Ölçüm Nokta Sayısı:</label>
                                            <div class="col-xs-4">
                                                <select class="form-control select2me select2-offscreen hurdanoktasayi" id="hurdanoktasayi" name="hurdanoktasayi" tabindex="-1" title="">
                                                    <option value="">Seçiniz...</option>
                                                    <option value="3">3</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-8 col-xs-12 hurdalfhf hide">
                                            <label class="control-label col-xs-4">Test Frekansları:</label>
                                            <div class="form-group col-xs-8">
                                                <label class="control-label col-xs-3"><input type="checkbox" id=hurdalf name="hurdalf" checked disabled/> LF </label>
                                                <label class="control-label col-xs-3"><input type="checkbox" id=hurdahf2 name="hurdahf2" /> HF2 </label>
                                                <label class="control-label col-xs-3"><input type="checkbox" id=hurdahf3 name="hurdahf3" /> HF3-I </label>
                                                <label class="control-label col-xs-3"><input type="checkbox" id=hurdahf32 name="hurdahf32" /> HF3-II </label>
                                            </div>
                                        </div>
                                        <div class="form-group col-xs-12 hide hurdaexceldiv">
                                            <h4>Excel İlk Satırında Şu Alanlar Olmalıdır:</h4>
                                            <h4 class="hurdainfo" style="line-height: 25px">Sayaç No | Nokta1 | Nokta2 | Nokta3 | Açıklama </h4>
                                            <div class="form-group">
                                                <label class="control-label col-xs-2">Excel</label>
                                                <div class="col-sm-8 col-xs-7 fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="input-group input-large">
                                                        <div class="form-control uneditable-input input-fixed input-xlarge" data-trigger="fileinput">
                                                            <i class="fa fa-file-excel-o fileinput-exists"></i><span class="fileinput-filename" style="margin-left: 5px"></span>
                                                        </div>
                                                        <span class="input-group-addon btn default btn-file" style="border:1px solid #969696">
                                                    <span class="fileinput-new">
                                                    Excel Seç </span>
                                                    <span class="fileinput-exists">
                                                    Değiştir </span>
                                                    <input type="file" id="hurdaexcel" name="hurdaexcel" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                                                    </span>
                                                        <a href="" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                            Sil </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-offset-3 col-xs-9">
                                                    <a id="hurdaexcelekle" href="#" type="button" data-dismiss="modal" class="btn green">Ekle</a>
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
