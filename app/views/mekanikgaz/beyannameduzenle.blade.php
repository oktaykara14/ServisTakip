@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Beyanname <small>Düzenleme Ekranı</small></h1>
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
    var oTable = table.DataTable({
        "sPaginationType": "simple_numbers",
        "bProcessing": true,
        "ajax": {
            "url": "{{ URL::to('mekanikgaz/beyannamekayitlist') }}",
            "type": "POST",
            "data": {
                "beyanname_id" : "@if(isset($beyanname)){{$beyanname->id}}@endif"
            }
        },
        "bServerSide": true,
        "fnDrawCallback" : function() {
            var secilenler=$('#secilenler').val();
            var secilenlist=secilenler.split(',');
            $.each(secilenlist,function(index) {
                $("#sample_editable_1  tr .id").each(function(){
                    if(secilenlist[index]===$(this).html()){
                        $(this).parents('tr').addClass("active");
                    }
                });
            });
        },
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
    table.on('click', 'tr', function () {
        if (oTable.cell($(this).children('.id')).data() !== undefined) {
            $(this).toggleClass("active");
            var adet = parseInt($('#beyannameadet').val());
            var secilenler = $('#secilenler').val();
            var secilenlist;
            if ($(this).hasClass('active')) {
                secilenler += (secilenler === "" ? "" : ",") + oTable.cell($(this).children('.id')).data();
                adet++;
                $('#secilenler').val(secilenler);
                $('#beyannameadet').val(adet);
                $('.beyannameadet').text(adet);
            } else {
                var secilen = oTable.cell($(this).children('.id')).data();
                secilenlist = secilenler.split(',');
                var yenilist = "";
                $.each(secilenlist, function (index) {
                    if (secilenlist[index] !== secilen) {
                        yenilist += (yenilist === "" ? "" : ",") + secilenlist[index];
                    }
                });
                adet--;
                secilenler = yenilist;
                $('#secilenler').val(yenilist);
                $('#beyannameadet').val(adet);
                $('.beyannameadet').text(adet);
            }
            secilenlist = secilenler.split(',');
            oTable.rows().every(function () {
                var data = this.data();
                var id = data[0];
                var durum = 0;
                $.each(secilenlist, function (index) {
                    if (id === secilenlist[index])
                        durum = 1;
                });
            });
            if (adet > 0) {
                $('.confirm').removeClass('hide');
            } else {
                $('.confirm').addClass('hide');
            }
            $('#secilenler').val(secilenler);
        }
    });
    $(document).on("click", ".tumunusec", function () {
        var adet=parseInt($('#beyannameadet').val());
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
        $('#beyannameadet').val(adet);
        $('.beyannameadet').text(adet);
        if(adet>0){
            $('.confirm').removeClass('hide');
        }else{
            $('.confirm').addClass('hide');
        }
    });
    $(document).on("click", ".temizle", function () {
        var adet=parseInt($('#beyannameadet').val());
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
        $('#beyannameadet').val(adet);
        $('.beyannameadet').text(adet);
        if(adet>0){
            $('.confirm').removeClass('hide');
        }else{
            $('.confirm').addClass('hide');
        }
    });
    tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
});
</script>
<script>
    $(document).ready(function() {
        $('#formsubmit').click(function () {
            $('#form_sample').submit();
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
            <i class="fa fa-plus"></i>Beyanname Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('mekanikgaz/beyannameduzenle/'.$beyanname->id.'') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate" enctype="multipart/form-data">
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
                    <label class="control-label col-xs-4">Beyanname No:<span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><input type="text" id="beyannameno" name="beyannameno" value="{{ Input::old('beyannameno') ? Input::old('beyannameno') : $beyanname->no }}" data-required="1" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Tarihi: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><div class="input-group input-medium date date-picker uretim" style="padding-left: 12px !important;" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                            <input type="text" id="tarih" name="tarih" class="form-control" value="{{Input::old('tarih') ? Input::old('tarih') : date("d-m-Y", strtotime($beyanname->tarih)) }}">
                            <span class="input-group-btn"><button class="btn default" type="button" style="border:1px solid #969696;padding-bottom: 5px;"><i class="fa fa-calendar"></i></button></span>
                        </div>
                    </div>
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
                    <span style="padding-left:100px"><button type="button" class="btn green tumunusec">Tümünü Seç</button>
                                                <button type="button" class="btn red temizle">Temizle</button></span>
                    <label class="col-sm-3 col-xs-6 beyannameadet" style="padding-top: 9px">{{ Input::old('beyannameadet') ? Input::old('beyannameadet') : $beyanname->adet }}</label>
                    <input type="text" id="secilenler" name="secilenler" value="{{ Input::old('secilenler') ? Input::old('secilenler') : $beyanname->secilenler }}" class="form-control hide"/>
                    <input type="text" id="beyannameadet" name="beyannameadet" value="{{ Input::old('beyannameadet') ? Input::old('beyannameadet') : $beyanname->adet }}" class="form-control hide"/>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <button type="button" class="btn green confirm" data-toggle="modal" data-target="#confirm">Kaydet</button>
                        <a href="{{ URL::to('mekanikgaz/beyanname')}}" class="btn default">Vazgeç</a>
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
                    <h4 class="modal-title">Beyanname Kaydedilecek</h4>
                </div>
                <div class="modal-body">
                    Girilen Bilgilere Göre Beyanname Kaydedilecektir?
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
@stop

