@extends('layout.master')

@section('page-title')
<!--suppress ALL -->
<div class="page-title">
    <h1>Edestek Personel <small>Bilgi Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/datatables/datatables.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ URL::to('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('page-styles')
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/datatables.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/moment-with-locales.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/edestek/page-info.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar   
   EdestekPage.init();
});
</script>
<script>
$('.make-switch').on('switchChange.bootstrapSwitch', function (event, state) {
    var id = $(this).attr('id');
    var aktif;
    if( state===true ){
        $(this).attr('checked',true);
        aktif = 1;
    }else{
        $(this).attr('checked',false);
        aktif = 0;
    }
    $.getJSON("{{ URL::to('edestek/personeldurum/')}}/"+id+'/'+aktif,function(event){
        toastr.options = {
            closeButton: true,debug: false,positionClass: "toast-top-right",onclick: null,
            showDuration: "1000",hideDuration: "1000",timeOut: "5000",extendedTimeOut: "1000",
            showEasing: "swing",hideEasing: "linear",showMethod: "fadeIn",hideMethod: "fadeOut"
        };
        toastr[event.type](event.text, event.title);
    });    
});
$(document).on("click", ".delete", function () {
     var Id = $(this).data('id');
     $(".modal-footer #sayacid").attr('href',"{{ URL::to('edestek/personelsil') }}/"+Id);
});
</script><script>
    $(document).ready(function() {
        moment.locale('tr');
        //noinspection JSNonASCIINames,JSNonASCIINames,JSNonASCIINames,JSNonASCIINames,JSNonASCIINames
        $('#tarih').daterangepicker({opens: (Metronic.isRTL() ? 'left' : 'right'), format: 'DD.MM.YYYY', separator: ' - ', startDate: moment().subtract(29,'days'), endDate: moment(), ranges: {'Bugün': [moment(), moment()],'Dün': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],'Son 7 Gün': [moment().subtract(6, 'days'), moment()],'Son 30 Gün': [moment().subtract(29, 'days'), moment()],'Bu Ay': [moment().startOf('month'), moment().endOf('month')],'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]}, minDate: '01/01/2010',maxDate: '31/12/2040',"locale": {"format": "DD.MM.YYYY","separator": " - ","applyLabel": "Tamam","cancelLabel": "İptal","fromLabel": "Başlangıç","toLabel": "Bitiş","customRangeLabel": "Yeni Tarih Gir","daysOfWeek": ["Pz", "Pt", "Sa", "Ça", "Pe", "Cu", "Ct"],"monthNames": ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"],"firstDay": 1}}, function (start, end) {$('#tarih').val(start.format('DD.MM.YYYY') + ' - ' + end.format('DD.MM.YYYY'));});
        $('#tarih').val(moment().subtract(29,'days').format('DD.MM.YYYY')+' - '+moment().format('DD.MM.YYYY'));
        $('#personel').select2();
        $('#tarihcheck').on('change', function () {
            if ($('#tarihcheck').attr('checked')) {
                $("#tarih").removeAttr('disabled');
            } else {
                $("#tarih").attr('disabled',1);
            }
        });
        $('#personelcheck').on('change', function () {
            if ($('#personelcheck').attr('checked')) {
                $("#personel").removeAttr('disabled');
            } else {
                $("#personel").attr('disabled',1);
            }
        });

        $('.performanscikar').click(function () {
            var tarih = $('#tarih').val();
            var tarihcheck = $('#tarihcheck').attr('checked') ? "1" : "0";
            var personel = $('#personel').val();
            var personelcheck = $('#personelcheck').attr('checked') ? "1" : "0";
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
            $.redirectPost(redirect, { tarihcheck: tarihcheck,tarih: tarih,personelcheck: personelcheck,personel: personel,ireport: '1' });
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
                    <i class="fa fa-user"></i>Personel Listesi
                </div>
                <div class="actions">
                    <a class="btn btn-default btn-sm performans" data-toggle="modal" data-target="#performans">
                        <i class="fa fa-print"></i> Performans Raporu</a>
                    <a href="{{ URL::to('edestek/personelekle') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-pencil"></i> Yeni Personel Ekle </a>
                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                    <thead>
                        <tr>
                            <th class="hide"></th>
                            <th>Adı Soyadı</th>
                            <th>İlgilendikleri</th>
                            <th>Son İşlemi</th>
                            <th>Son İşlem Tarihi</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($personel as $kisi)
                        <tr class="odd gradeX">
                            <td class="hide">{{ $kisi->id }}</td>
                            <td> {{ $kisi->adisoyadi }}</td>
                            <td> {{ $kisi->ilgiler($kisi->ilgilendikleri) }}</td>
                            <td> @if($kisi->sonislem_id) {{ $kisi->sonislem($kisi->sonislem_id)->yapilanislem }} @endif</td>
                            <td> @if($kisi->sonislemtarihi) {{date("d-m-Y", strtotime($kisi->sonislemtarihi))}} @endif</td>
                            <td><div class="bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-off bootstrap-switch-animate" style="width: 116px;">
                                    <div class="bootstrap-switch-container" style="width: 116px; margin-left: 0;">
                                    <input type="checkbox" id="{{$kisi->id}}" class="make-switch" data-on-color="success" data-off-color="warning" data-on-text="Aktif" data-off-text="Pasif" @if($kisi->durum) checked @else  @endif ></div>
                                </div></td>
                            <td >
                                <a class="btn btn-sm btn-warning" href="{{ URL::to('edestek/personelduzenle/'.$kisi->id.'') }}" > Düzenle </a>
                                <a href="#portlet-delete" data-toggle="modal" data-id="{{ $kisi->id }}" class="btn btn-sm btn-danger delete" data-original-title="" title="">Sil</a>
                            </td>    
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- END TABLE PORTLET-->
    </div>
</div>
@stop

@section('modal')
<div class="modal fade" id="portlet-delete" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Personel Silinecek</h4>
            </div>
            <div class="modal-body">
                 Seçilen Personeli Silmek İstediğinizden Emin Misiniz?
            </div>
            <div class="modal-footer">
                <a id="sayacid" href="" type="button" class="btn blue">Sil</a>
                <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="performans" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="portlet box">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-plus"></i>Performans Raporu
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form action="" target="" id="form_sample_2" class="form-horizontal" method="POST" novalidate="novalidate">
                                <div class="form-body col-xs-12">
                                    <h3 class="form-section col-xs-12">Personel Performans Rapor Ekranı</h3>
                                    <div class="alert alert-danger display-hide">
                                        <button class="close" data-close="alert"></button>
                                        Girilen Bilgilerde Hata Var.
                                    </div>
                                    <div class="alert alert-success display-hide">
                                        <button class="close" data-close="alert"></button>
                                        Bilgiler Doğru!
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-6">Personel:
                                            <input type="checkbox" id="personelcheck" name="personelcheck" /></label>
                                        <div class="col-sm-8 col-xs-6">
                                            <select class="form-control select2me select2-offscreen" id="personel" name="personel" tabindex="-1" title="" disabled>
                                                <option value="-1" selected>Hepsi</option>
                                                @foreach($personel as $kisi)
                                                    @if ($kisi->durum)
                                                        @if(Input::old('personel')==$kisi->id )
                                                            <option value="{{ $kisi->id }}" selected>{{ $kisi->adisoyadi }}</option>
                                                        @else
                                                            <option value="{{ $kisi->id }}">{{ $kisi->adisoyadi }}</option>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label class="control-label col-sm-2 col-xs-6">Tarih Aralığı:
                                            <input type="checkbox" id="tarihcheck" name="tarihcheck" /></label>
                                        <div class="col-sm-8 col-xs-6">
                                            <div class="input-group" id="daterangepick">
                                                <input type="text" id="tarih" name="tarih" class="form-control" disabled>
                                                <span class="input-group-btn">
                                                    <button class="btn default date-range-toggle" type="button">
                                                        <i class="fa fa-calendar"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions col-xs-12">
                                        <div class="row">
                                            <div class="col-xs-offset-3 col-xs-9">
                                                <a id="performanscikar" href="#" type="button" data-dismiss="modal" class="btn green performanscikar">Rapor Çıkar</a>
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
