@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Mekanik Gaz Sayaç Garanti Süresi <small>Ekleme Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/fuelux/js/spinner.min.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/mekanikdatabase/form-validation-9.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar   
   FormValidationGazDatabase.init();
});
$(document).ready(function() {
    $('#sayacadi').on('change', function () {
        var adiid = $(this).val();
        if (adiid !== "") {
            $.blockUI();
            $.getJSON(" {{ URL::to('mekanikdatabase/sayacparcalari') }}/" + adiid + "/1", function (event) {
                if (event.durum) {
                    var sayacparca = event.parcalar;
                    var r = [],j = -1;
                    var key,size;
                    r[++j] = '<thead><tr><th> # </th><th> Parça Adı </th><th> Garanti Süresi (yıl) </th></tr></thead>';
                    for (key = 0, size = sayacparca.length; key < size; key++) {
                        var value = key + 1;
                        r[++j] = '<tr><td>';
                        r[++j] = value;
                        r[++j] = '</td><td>';
                        r[++j] = sayacparca[key]['tanim'];
                        r[++j] = '</td><td class="hide">';
                        r[++j] = '<input name="parca' + value + '" value="' + sayacparca[key]['id'] + '"/>';
                        r[++j] = '</td><td>';
                        r[++j] = '<div class="spinner" id="spinner' + value + '">' +
                        '<div class="input-group input-small">' +
                        '<input type="text" name="spinner' + value + '" class="spinner-input form-control" maxlength="3" readonly="">' +
                        '<div class="spinner-buttons input-group-btn btn-group-vertical">' +
                        '<button type="button" class="btn spinner-up btn-xs blue">' +
                        '<i class="fa fa-angle-up"></i></button>' +
                        '<button type="button" class="btn spinner-down btn-xs blue">' +
                        '<i class="fa fa-angle-down"></i></button>' +
                        '</div></div></div>';
                        r[++j] = '</td></tr>';
                    }
                    $("#parcasayi").val(sayacparca.length);
                    $('#dataTable').html(r.join(''));
                    var garanti = $('#spinner').spinner('value');
                    for (key = 1, size = sayacparca.length; key <= size; key++) {
                        $('#spinner' + key).spinner({value: garanti, min: 0, max: 5});
                    }
                } else {
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
        }
    });
    var sayacadi = $('#sayacadi').val();
    if (sayacadi !== "") {
        $.blockUI();
        $.getJSON(" {{ URL::to('mekanikdatabase/sayacparcalari') }}/" + sayacadi + "/1", function (event) {
            if (event.durum) {
                var sayacparca = event.parcalar;
                var r = [],j = -1;
                var key,size;
                r[++j] = '<thead><tr><th> # </th><th> Parça Adı </th><th> Garanti Süresi (yıl) </th></tr></thead>';
                for (key = 0,size = sayacparca.length; key < size; key++) {
                    var value = key + 1;
                    r[++j] = '<tr><td>';
                    r[++j] = value;
                    r[++j] = '</td><td>';
                    r[++j] = sayacparca[key]['tanim'];
                    r[++j] = '</td><td class="hide">';
                    r[++j] = '<input name="parca' + value + '" value="' + sayacparca[key]['id'] + '"/>';
                    r[++j] = '</td><td>';
                    r[++j] = '<div class="spinner" id="spinner' + value + '">' +
                    '<div class="input-group input-small">' +
                    '<input type="text" name="spinner' + value + '" class="spinner-input form-control" maxlength="3" readonly="">' +
                    '<div class="spinner-buttons input-group-btn btn-group-vertical">' +
                    '<button type="button" class="btn spinner-up btn-xs blue">' +
                    '<i class="fa fa-angle-up"></i></button>' +
                    '<button type="button" class="btn spinner-down btn-xs blue">' +
                    '<i class="fa fa-angle-down"></i></button>' +
                    '</div></div></div>';
                    r[++j] = '</td></tr>';
                }
                $("#parcasayi").val(sayacparca.length);
                $('#dataTable').html(r.join(''));
                var garanti = $('#spinner').spinner('value');
                for (key = 1, size = sayacparca.length; key <= size; key++) {
                    $('#spinner' + key).spinner({value: garanti, min: 0, max: 5});
                }
            } else {
                toastr[event.type](event.text, event.title);
            }
            $.unblockUI();
        });
    }
    $('#spinner').on('change', function () {
        var garanti = $(this).spinner('value');
        var parcasayi = $('#parcasayi').val();
        for (var i = 1; i <= parcasayi; i++) {
            $('#spinner' + i).spinner('value', garanti);
        }

    });
    $("select").on("select2-close", function () { $(this).valid(); });
});
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>Sayaç Garanti Süresi Ekle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('mekanikdatabase/garantiekle') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-sm-2 col-xs-4">Üretim Yeri: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="uretimyer" name="uretimyer" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($uretimyerleri as $uretimyeri)
                                @if(Input::old('uretimyer')==$uretimyeri->id )
                            <option value="{{ $uretimyeri->id }}" selected>{{ $uretimyeri->yeradi }}</option>
                                @else
                            <option value="{{ $uretimyeri->id }}">{{ $uretimyeri->yeradi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Sayaç Adı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="sayacadi" name="sayacadi" tabindex="-1" title="">
                            <option value="">Seçiniz...</option>
                            @foreach($sayacadlari as $sayacadi)
                                @if(Input::old('sayacadi')==$sayacadi->id )
                            <option value="{{ $sayacadi->id }}" selected>{{ $sayacadi->sayacadi }}</option>
                                @else
                            <option value="{{ $sayacadi->id }}">{{ $sayacadi->sayacadi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Garanti Süresi:</label>
                    <div id="spinner" class="col-xs-8">
                        <div class="input-group input-small">
                            <input type="text" name="spinner" value="{{ Input::old('spinner') }}" class="spinner-input form-control" maxlength="3" readonly="">
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
                <div class="form-group">
                    <label class="control-label col-sm-2 col-xs-4">Parçalar ve Garanti Süreleri</label>
                    <div class="col-xs-8">
                        <div class="table-scrollable">
                            <table id="dataTable" class="table table-hover">
                            <thead>
                            <tr>
                                <th> # </th>
                                <th> Parça Adı </th>
                                <th> Garanti Süresi (yıl) </th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="form-group hide">
                    <input id="parcasayi" name="parcasayi" value="{{ Input::old('parcasayi')}}"/>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn green">Kaydet</button>
                    <a href="{{ URL::to('mekanikdatabase/sayacgaranti')}}" class="btn default">Vazgeç</a>
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
