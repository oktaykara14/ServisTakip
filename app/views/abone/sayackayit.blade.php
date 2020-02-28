@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Müşteri <small>Sayaç Kayıt Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
    <link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
@stop

@section('page-styles')
@stop

@section('page-js')
    <script src="{{ URL::to('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/datatables/datatables.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
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
                "url": "{{ URL::to('abone/sayackayitlist') }}",
                "type": "POST",
                "data": {
                    "netsiscari_id" : "@if(isset(Auth::user()->netsiscari_id) && count(Auth::user()->netsiscari_id)>0){{Auth::user()->netsiscarilist}}@endif"
                }
            },
            "bServerSide": true,
            "fnDrawCallback" : function() {
                $(document).on("click", ".delete", function () {
                    var id = $(this).data('id');
                    $(".modal-footer #sayacid").attr('href',"{{ URL::to('abone/sayackayitsil') }}/"+id);
                });
            },
            "aaSorting": [[3,'desc']],
            "columnDefs": [ { targets: [ 1 ], orderData: [ 1, 0 ] },
                { targets: [ 2 ], orderData: [ 2, 0 ] },
                { targets: [ 3 ], orderData: [ 3, 0 ] },
                { targets: [ 4 ], orderData: [ 4, 0 ] },
                { targets: [ 5 ], orderData: [ 5, 0 ] },
                { targets: [ 6 ], orderData: [ 6, 0 ] },
                { targets: [ 7 ], orderData: [ 7, 0 ] }
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
                {data: 'id', name: 'abonesayackayit.id',"class":"id","orderable": true, "searchable": true},
                {data: 'belgeno', name: 'abonesayackayit.belgeno',"orderable": true, "searchable": true},
                {data: 'kargoadi', name: 'kargofirma.kargoadi',"orderable": true, "searchable": false},
                {data: 'gondermetarihi', name: 'abonesayackayit.gondermetarihi',"orderable": true, "searchable": false},
                {data: 'tur', name: 'sayactur.tur',"orderable": true, "searchable": false},
                {data: 'adet', name: 'abonesayackayit.adet',"orderable": true, "searchable": true},
                {data: 'gdurum', name: 'abonesayackayit.gdurum',"orderable": true, "searchable": false},
                {data: 'adi_soyadi', name: 'kullanici.adi_soyadi',"orderable": true, "searchable": false},
                {data: 'kabultarihi', name: 'abonesayackayit.kabultarihi',"orderable": true, "searchable": false},
                {data: 'ggondermetarihi', name: 'abonesayackayit.ggondermetarihi',"visible": false, "searchable": true},
                {data: 'gkabultarihi', name: 'abonesayackayit.gkabultarihi',"visible": false, "searchable": true},
                {data: 'ndurum', name: 'abonesayackayit.ndurum',"visible": false, "searchable": true},
                {data: 'nkargoadi', name: 'kargofirma.nkargoadi',"visible": false, "searchable": true},
                {data: 'ntur', name: 'sayactur.ntur',"visible": false, "searchable": true},
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
            '<option value="1">BelgeNo</option>'+
            '<option value="12">Kargo</option>'+
            '<option value="9">Gönderme T.</option>'+
            '<option value="5">Adet</option>'+
            '<option value="13">Sayaç Tipi</option>'+
            '<option value="11">Durum</option>'+
            '<option value="14">Kabul Eden</option>'+
            '<option value="10">Kabul T.</option>'+
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
        table.on('click', 'tr', function () {
            if(oTable.cell( $(this).children('.id')).data()!==undefined) {
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
                }
            }
        });
        $(document).ready(function() {
            $('#kriter').select2();
            $('.sayacliste').click(function () {
                var liste = $('#sample_editable_1 .active .id').text();
                if (liste !== null) {
                    $.extend({
                        redirectPost: function (location, args) {
                            var form = '';
                            $.each(args, function (key, value) {
                                value = value.split('"').join('\"');
                                form += '<input type="hidden" name="' + key + '" value="' + value + '">';
                            });
                            $('<form action="' + location + '" method="POST" target="_blank">' + form + '</form>').appendTo($(document.body)).submit();
                        }
                    });
                    var redirect = "";
                    $.redirectPost(redirect, {liste: liste});

                }
            });
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
                        <i class="fa fa-tag"></i>Sisteme Girilen Sayaçlar
                    </div>
                    <div class="actions">
                        <a class="btn btn-default btn-sm sayacliste">
                            <i class="fa fa-list"></i> Sayaç Listesi </a>
                        <a href="{{ URL::to('abone/sayackayitekle') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-pencil"></i> Sayaç Kayıdı Ekle </a>
                        <div class="hide"><input id="secilen" name="secilen"/></div>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Takip No</th>
                                <th>Kargo Firma</th>
                                <th>Gönderme Tarihi</th>
                                <th>Sayaç Tipi</th>
                                <th>Adet</th>
                                <th>Durum</th>
                                <th>Kabul Eden</th>
                                <th>Kabul Tarihi</th>
                                <th></th><th></th><th></th>
                                <th></th><th></th><th></th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('modal')
    <div class="modal fade" id="portlet-delete" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Sayaç Kayıdı Silinecek</h4>
                </div>
                <div class="modal-body">
                    Seçilen Sayaç Kayıdını Silmek İstediğinizden Emin Misiniz?
                </div>
                <div class="modal-footer">
                    <a id="sayacid" href="" type="button" class="btn blue">Sil</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
@stop
