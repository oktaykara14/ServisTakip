@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Beyanname <small>Bilgi Ekranı</small></h1>
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
<script src="{{ URL::to('pages/mekanikgaz/form-validation-6.js') }}"></script>
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
jQuery(document).ready(function() {
    var table = $('#sample_editable_1');
    table.DataTable({
        "sPaginationType": "simple_numbers",
        "bProcessing": true,
        "ajax": {
            "url": "{{ URL::to('mekanikgaz/beyannamekayitlist') }}",
            "type": "POST",
            "data": {
                "beyannamegoster_id" : "@if(isset($beyanname)){{$beyanname->id}}@endif"
            }
        },
        "bServerSide": true,
        "fnDrawCallback" : function() {},
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
            {data: 'id', name: 'sayacgelen.id',"class":"id","orderable": true, "searchable": true},
            {data: 'serino', name: 'sayacgelen.serino',"orderable": true, "searchable": true},
            {data: 'sayacadi', name: 'sayacadi.sayacadi',"orderable": true, "searchable": true},
            {data: 'yeradi', name: 'uretimyer.yeradi',"orderable": true, "searchable": true},
            {data: 'sayacdurum', name: 'sayacdurum',"orderable": false, "searchable": true}
        ],
        "lengthMenu": [
            [10, 15, 20, 99999999],
            [10, 15, 20, "Hepsi"]
        ],
        searchDelay: 0
    });
    var tableWrapper = jQuery('#sample_editable_1_wrapper');
    tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
});
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Beyanname Bilgi Ekranı
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="" id="form_sample" class="form-horizontal" novalidate="novalidate">
            <div class="form-body">
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    Girilen Bilgilerde Hata Var.
                </div>
                <div class="alert alert-success display-hide">
                    <button class="close" data-close="alert"></button>
                    Bilgiler Doğru!
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Beyanname No:</label>
                    <label class="col-xs-8" style="padding-top: 7px">{{ $beyanname->no }} </label>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Tarihi:</label>
                    <label class="col-sm-3 col-xs-8" style="padding-top: 7px">{{ $beyanname->tarih ? date("d-m-Y", strtotime($beyanname->tarih)) : '' }}</label>
                </div>
                <div class="form-group col-xs-12">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                            <tr><th>#</th>
                                <th>Serino</th>
                                <th>Sayaç Adı</th>
                                <th>Üretim Yeri</th>
                                <th>Sayaç Durumu</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="form-group col-xs-12">
                    <label class="control-label col-sm-2 col-xs-6">Seçilen Sayaç Sayısı:</label>
                    <label class="col-sm-3 col-xs-6 beyannameadet" style="padding-top: 9px">{{ Input::old('beyannameadet') ? Input::old('beyannameadet') : $beyanname->adet }}</label>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <a href="{{ URL::to('mekanikgaz/beyanname')}}" class="btn default">Tamam</a>
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
@stop

