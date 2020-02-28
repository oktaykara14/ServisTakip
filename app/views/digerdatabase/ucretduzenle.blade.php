@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Toplu Sayaç Parça Ücreti <small>Bilgi Düzenleme Ekranı</small></h1>
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
<script src="{{ URL::to('assets/global/plugins/jquery-maskmoney/jquery.maskMoney.min.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/digerdatabase/form-validation-11.js') }}"></script>
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
$(document).ready(function(){
    $('#uretimyer').on('change', function () {
        var yerid = $(this).val();
        if(yerid!==""){
            $.blockUI();
            $.getJSON("{{ URL::to('backend/yerparabirimi') }}",{id:yerid},function(event){
                if(event.durum){
                    $('#ozelbirim').val(event.parabirimi);
                }else{
                    toastr[event.type](event.text, event.title);
                }
                $.unblockUI();
            });
        }else{
            $('#ozelbirim').val('€');
        }
    });

    $('#sayactur').on('change', function () {
        var turid = $(this).val();
        if (turid !== "") {
            $.blockUI();
            $.getJSON("{{ URL::to('digerdatabase/sayacadilist') }}", {id: turid}, function (event) {
                if (event.durum) {
                    var sayacadlari = event.sayacadlari;
                    $("#sayacadi").empty();
                    $('#sayacadi').append('<option value="">Seçiniz...</option>');
                    $('#sayacadi').append('<option data-id="0" value="0">Hepsi</option>');
                    $.each(sayacadlari, function (index) {
                        $("#sayacadi").append('<option data-id="'+sayacadlari[index].cap+'" value="' + sayacadlari[index].id + '"> ' + sayacadlari[index].sayacadi + '</option>');
                    });
                    $('#sayacadi').select2("val","");
                    $('#dataTable').html("<thead><tr><th> # </th><th> Parça Adı </th><th> Genel Fiyat </th><th> Özel Fiyat </th></tr></thead>");
                } else {
                    toastr[event.type](event.text, event.title);
                    $('#sayacadi').empty();
                    $('#sayacadi').append('<option value="">Seçiniz...</option>');
                    $('#sayacadi').select2("val","");
                    $('#dataTable').html("<thead><tr><th> # </th><th> Parça Adı </th><th> Genel Fiyat </th><th> Özel Fiyat </th></tr></thead>");
                }
                $.unblockUI();
            });
        }else{
            $('#sayacadi').empty();
            $('#sayacadi').append('<option value="">Seçiniz...</option>');
            $('#sayacadi').select2("val","");
            $('#dataTable').html("<thead><tr><th> # </th><th> Parça Adı </th><th> Genel Fiyat </th><th> Özel Fiyat </th></tr></thead>");
        }
    });

    $('#sayacadi').on('change', function () {
        var adiid = $(this).val();
        var uretimyerid = $('#uretimyer').val();
        //var genelbirim = $('#genelbirim').val();
        //var ozelbirim = $('#ozelbirim').val();
        var sayactur = $('#sayactur').val();
        if (adiid !== "" && uretimyerid!=="") {
            var capdurum = $(this).find("option:selected").data('id');
            if (capdurum === 0) //cap kontrol edilmiyor
            {
                $.blockUI();
                $("#sayaccap").select2("val", 1);
                $("#sayaccap").prop("disabled", true);
                $.getJSON("{{ URL::to('digerdatabase/sayacparcalari') }}", {turid: sayactur,adiid: adiid, capid: 1,uretimyerid : uretimyerid}, function (event) {
                    if (event.durum) {
                        var sayacparca = event.parcalar;
                        var r = [], j = -1;
                        var key, size;
                        r[++j] = '<thead><tr><th> # </th><th> Parça Adı </th><th> Genel Fiyat </th><th> Özel Fiyat </th></tr></thead>';
                        for (key = 0, size = sayacparca.length; key < size; key++) {
                            var value = key + 1;
                            r[++j] = '<tr><td>';
                            r[++j] = value;
                            r[++j] = '</td><td>';
                            r[++j] = sayacparca[key]['tanim'];
                            r[++j] = '</td><td class="hide">';
                            r[++j] = '<input id="parca'+value+'" name="parca[]" value="' + sayacparca[key]['id'] + '"/>';
                            r[++j] = '</td><td>';
                            r[++j] = '<input type="tel" id="genelbirim'+value+'" class="genelbirim" name="genel[]" value="' + (parseFloat(sayacparca[key]['genel']!== null ? sayacparca[key]['genel']['fiyat'] : 0.00).toFixed(2)) + '" readonly/>';
                            r[++j] = '</td><td>';
                            if(sayacparca[key]['ozel']!==null)
                                r[++j] = '<input type="tel" id="ozelbirim'+value+'" class="ozelbirim" name="ozel[]" value="' + (parseFloat(sayacparca[key]['ozel']['fiyat']).toFixed(2)) + '" />';
                            else
                                r[++j] = '<input type="tel" id="ozelbirim'+value+'" class="ozelbirim" name="ozel[]" value="' + (parseFloat('0.00').toFixed(2)) + '" style="background-color: red;color:white"/>';
                            r[++j] = '</td></tr>';
                        }
                        $("#parcasayi").val(sayacparca.length);
                        $('#dataTable').html(r.join(''));
                        for (key = 0, size = sayacparca.length; key < size; key++) {
                            value = key + 1;
                            $('#genelbirim'+value).maskMoney({suffix: ' ' + sayacparca[key]['genelparabirimi']['birimi'], affixesStay: true, allowZero: true});
                            $('#ozelbirim'+value).maskMoney({suffix: ' ' + sayacparca[key]['ozelparabirimi']['birimi'], affixesStay: true, allowZero: true});
                        }
                    } else {
                        toastr[event.type](event.text, event.title);
                        $("#sayaccap").select2("val", 1);
                        $("#sayaccap").prop("disabled", false);
                        $('#dataTable').html("<thead><tr><th> # </th><th> Parça Adı </th><th> Genel Fiyat </th><th> Özel Fiyat </th></tr></thead>");
                    }
                    $.unblockUI();
                });
            } else {
                $("#sayaccap").select2("val", 1);
                $("#sayaccap").prop("disabled", false);
                $('#dataTable').html("<thead><tr><th> # </th><th> Parça Adı </th><th> Genel Fiyat </th><th> Özel Fiyat </th></tr></thead>");
            }
        } else {
            $("#sayaccap").select2("val", 1);
            $("#sayaccap").prop("disabled", true);
            $('#dataTable').html("<thead><tr><th> # </th><th> Parça Adı </th><th> Genel Fiyat </th><th> Özel Fiyat </th></tr></thead>");

        }
    });

    $('#sayaccap').on('change', function () {
        var capid = $(this).val();
        var uretimyerid = $('#uretimyer').val();
        //var genelbirim = $('#genelbirim').val();
        //var ozelbirim = $('#ozelbirim').val();
        var sayacadi = $('#sayacadi').val();
        if (capid !== "") {
            $.blockUI();
            $.getJSON("{{ URL::to('digerdatabase/sayacparcalari') }}", {adiid: sayacadi, capid: capid,uretimyerid : uretimyerid}, function (event) {
                if (event.durum) {
                    var sayacparca = event.parcalar;
                    var r = [], j = -1;
                    var key, size;
                    r[++j] = '<thead><tr><th> # </th><th> Parça Adı </th><th> Genel Fiyat </th><th> Özel Fiyat </th></tr></thead>';
                    for (key = 0, size = sayacparca.length; key < size; key++) {
                        var value = key + 1;
                        r[++j] = '<tr><td>';
                        r[++j] = value;
                        r[++j] = '</td><td>';
                        r[++j] = sayacparca[key]['tanim'];
                        r[++j] = '</td><td class="hide">';
                        r[++j] = '<input id="parca'+value+'" name="parca[]" value="' + sayacparca[key]['id'] + '"/>';
                        r[++j] = '</td><td>';
                        r[++j] = '<input type="tel" id="genelbirim'+value+'" class="genelbirim" name="genel[]" value="' + (parseFloat(sayacparca[key]['genel']!== null ? sayacparca[key]['genel']['fiyat'] : 0.00).toFixed(2)) + '" readonly/>';
                        r[++j] = '</td><td>';
                        if(sayacparca[key]['ozel']!== null)
                            r[++j] = '<input type="tel" id="ozelbirim'+value+'" class="ozelbirim" name="ozel[]" value="' + (parseFloat(sayacparca[key]['ozel']['fiyat']).toFixed(2)) + '" />';
                        else
                            r[++j] = '<input type="tel" id="ozelbirim'+value+'" class="ozelbirim" name="ozel[]" value="' + (parseFloat('0.00').toFixed(2)) + '" style="background-color: red;color:white"/>';
                        r[++j] = '</td></tr>';
                    }
                    $("#parcasayi").val(sayacparca.length);
                    $('#dataTable').html(r.join(''));
                    for (key = 0, size = sayacparca.length; key < size; key++) {
                        value = key + 1;
                        $('#genelbirim'+value).maskMoney({suffix: ' ' + sayacparca[key]['genelparabirimi']['birimi'], affixesStay: true, allowZero: true});
                        $('#ozelbirim'+value).maskMoney({suffix: ' ' + sayacparca[key]['ozelparabirimi']['birimi'], affixesStay: true, allowZero: true});
                    }
                } else {
                    toastr[event.type](event.text, event.title);
                    $("#sayaccap").select2("val", 1);
                    $("#sayaccap").prop("disabled", false);
                    $('#dataTable').html("<thead><tr><th> # </th><th> Parça Adı </th><th> Genel Fiyat </th><th> Özel Fiyat </th></tr></thead>");
                }
                $.unblockUI();
            });
        }else{
            $('#dataTable').html("<thead><tr><th> # </th><th> Parça Adı </th><th> Genel Fiyat </th><th> Özel Fiyat </th></tr></thead>");
        }
    });


    var uretimyer = $('#uretimyer').val();
    if(uretimyer!==""){
        $.blockUI();
        $.getJSON("{{ URL::to('backend/yerparabirimi') }}",{id:uretimyer},function(event){
            if(event.durum){
                $('#ozelbirim').val(event.parabirimi);
            }else{
                toastr[event.type](event.text, event.title);
            }
            $.unblockUI();
        });
    }else {
        $('#ozelbirim').val('€');
    }
    var sayactur = $('#sayactur').val();
    if(sayactur!==""){
        $.blockUI();
        $.getJSON("{{ URL::to('digerdatabase/sayacadilist') }}", {id: sayactur}, function (event) {
            if (event.durum) {
                var sayacadlari = event.sayacadlari;
                $("#sayacadi").empty();
                $('#sayacadi').append('<option value="">Seçiniz...</option>');
                $('#sayacadi').append('<option data-id="0" value="0">Hepsi</option>');
                $.each(sayacadlari, function (index) {
                    $("#sayacadi").append('<option data-id="'+sayacadlari[index].cap+'" value="' + sayacadlari[index].id + '"> ' + sayacadlari[index].sayacadi + '</option>');
                });
                $('#sayacadi').select2("val",0);
                $("#sayaccap").select2("val", 1);
                $("#sayaccap").prop("disabled", true);
            } else {
                toastr[event.type](event.text, event.title);
            }
            $.unblockUI();
        });
    }else{
        $('#sayacadi').empty();
    }
    var sayacadi = $('#sayacadi').val();
    var sayaccap = $('#sayaccap').val();
    if (sayacadi !== "" &&  sayacadi!=null && uretimyer!=="") {
        var capdurum = $(this).find("option:selected").data('id');
        if (capdurum === 0) //cap kontrol edilmiyor
        {
            $.blockUI();
            $("#sayaccap").select2("val", 1);
            $("#sayaccap").prop("disabled", true);
            $.getJSON("{{ URL::to('digerdatabase/sayacparcalari') }}", { turid: sayactur,adiid: sayacadi,capid: 1,uretimyerid: uretimyer}, function (event) {
                if (event.durum) {
                    var sayacparca = event.parcalar;
                    var r = [], j = -1;
                    var key, size;
                    r[++j] = '<thead><tr><th> # </th><th> Parça Adı </th><th> Genel Fiyat </th><th> Özel Fiyat </th></tr></thead>';
                    for (key = 0, size = sayacparca.length; key < size; key++) {
                        var value = key + 1;
                        r[++j] = '<tr><td>';
                        r[++j] = value;
                        r[++j] = '</td><td>';
                        r[++j] = sayacparca[key]['tanim'];
                        r[++j] = '</td><td class="hide">';
                        r[++j] = '<input id="parca'+value+'" name="parca[]" value="' + sayacparca[key]['id'] + '"/>';
                        r[++j] = '</td><td>';
                        r[++j] = '<input type="tel" id="genelbirim'+value+'" class="genelbirim" name="genel[]" value="' + (parseFloat(sayacparca[key]['genel']!== null ? sayacparca[key]['genel']['fiyat'] : 0.00).toFixed(2)) + '" readonly/>';
                        r[++j] = '</td><td>';
                        if(sayacparca[key]['ozel']!== null)
                            r[++j] = '<input type="tel" id="ozelbirim'+value+'" class="ozelbirim" name="ozel[]" value="' + (parseFloat(sayacparca[key]['ozel']['fiyat']).toFixed(2)) + '" />';
                        else
                            r[++j] = '<input type="tel" id="ozelbirim'+value+'" class="ozelbirim" name="ozel[]" value="' + (parseFloat('0.00').toFixed(2)) + '" style="background-color: red;color:white"/>';
                        r[++j] = '</td></tr>';
                    }
                    $("#parcasayi").val(sayacparca.length);
                    $('#dataTable').html(r.join(''));
                    for (key = 0, size = sayacparca.length; key < size; key++) {
                        value = key + 1;
                        $('#genelbirim'+value).maskMoney({suffix: ' ' + sayacparca[key]['genelparabirimi']['birimi'], affixesStay: true, allowZero: true});
                        $('#ozelbirim'+value).maskMoney({suffix: ' ' + sayacparca[key]['ozelparabirimi']['birimi'], affixesStay: true, allowZero: true});
                    }
                } else {
                    toastr[event.type](event.text, event.title);
                    $("#sayaccap").select2("val", 1);
                    $("#sayaccap").prop("disabled", false);
                    $('#dataTable').html("<thead><tr><th> # </th><th> Parça Adı </th><th> Genel Fiyat </th><th> Özel Fiyat </th></tr></thead>");
                }
                $.unblockUI();
            });
        } else {
            $.blockUI();
            $("#sayaccap").prop("disabled", false);
            $.getJSON("{{ URL::to('digerdatabase/sayacparcalari') }}", { turid: sayactur,adiid: sayacadi,capid: sayaccap,uretimyerid: uretimyer}, function (event) {
                if (event.durum) {
                    var sayacparca = event.parcalar;
                    var r = [], j = -1;
                    var key, size;
                    r[++j] = '<thead><tr><th> # </th><th> Parça Adı </th><th> Genel Fiyat </th><th> Özel Fiyat </th></tr></thead>';
                    for (key = 0, size = sayacparca.length; key < size; key++) {
                        var value = key + 1;
                        r[++j] = '<tr><td>';
                        r[++j] = value;
                        r[++j] = '</td><td>';
                        r[++j] = sayacparca[key]['tanim'];
                        r[++j] = '</td><td class="hide">';
                        r[++j] = '<input id="parca'+value+'" name="parca[]" value="' + sayacparca[key]['id'] + '"/>';
                        r[++j] = '</td><td>';
                        r[++j] = '<input type="tel" id="genelbirim'+value+'" class="genelbirim" name="genel[]" value="' + (parseFloat(sayacparca[key]['genel']!== null ? sayacparca[key]['genel']['fiyat'] : 0.00).toFixed(2)) + '" readonly/>';
                        r[++j] = '</td><td>';
                        if(sayacparca[key]['ozel']!== null)
                            r[++j] = '<input type="tel" id="ozelbirim'+value+'" class="ozelbirim" name="ozel[]" value="' + (parseFloat(sayacparca[key]['ozel']['fiyat']).toFixed(2)) + '" />';
                        else
                            r[++j] = '<input type="tel" id="ozelbirim'+value+'" class="ozelbirim" name="ozel[]" value="' + (parseFloat('0.00').toFixed(2)) + '" style="background-color: red;color:white"/>';
                        r[++j] = '</td></tr>';
                    }
                    $("#parcasayi").val(sayacparca.length);
                    $('#dataTable').html(r.join(''));
                    for (key = 0, size = sayacparca.length; key < size; key++) {
                        value = key + 1;
                        $('#genelbirim'+value).maskMoney({suffix: ' ' + sayacparca[key]['genelparabirimi']['birimi'], affixesStay: true, allowZero: true});
                        $('#ozelbirim'+value).maskMoney({suffix: ' ' + sayacparca[key]['ozelparabirimi']['birimi'], affixesStay: true, allowZero: true});
                    }
                } else {
                    toastr[event.type](event.text, event.title);
                    $("#sayaccap").select2("val", 1);
                    $("#sayaccap").prop("disabled", false);
                    $('#dataTable').html("<thead><tr><th> # </th><th> Parça Adı </th><th> Genel Fiyat </th><th> Özel Fiyat </th></tr></thead>");
                }
                $.unblockUI();
            });
        }
    } else {
        $("#sayaccap").select2("val", 1);
        $("#sayaccap").prop("disabled", true);
        $('#dataTable').html("<thead><tr><th> # </th><th> Parça Adı </th><th> Genel Fiyat </th><th> Özel Fiyat </th></tr></thead>");
    }

    $("select").on("select2-close", function () { $(this).valid(); });
    $('#form_sample').valid();
});
</script>
@stop

@section('content')
<div class="portlet box">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-pencil"></i>Toplu Sayaç Parça Ücreti Düzenle
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('digerdatabase/ucretduzenle/'.$sayactur->id.'/'.$uretimyer->id) }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
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
                    <label class="control-label col-xs-4">Üretim Yeri: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="uretimyer" name="uretimyer" tabindex="-1" title="">
                            <option value="{{ $uretimyer->id }}" selected>{{ $uretimyer->yeradi }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Sayaç Türü: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="sayactur" name="sayactur" tabindex="-1" title="">
                            <option value="{{ $sayactur->id }}" selected>{{ $sayactur->tur }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12">
                    <label class="control-label col-xs-4">Sayaç Adı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="sayacadi" name="sayacadi" tabindex="-1" title="">
                            <option data-id="0"  value="0" selected>Hepsi</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-6 col-xs-12 kontrol">
                    <label class="control-label col-xs-4">Sayaç Çapı: <span class="required" aria-required="true"> * </span></label>
                    <div class="input-icon right col-xs-8">
                        <i class="fa"></i><select class="form-control select2me select2-offscreen" id="sayaccap" name="sayaccap" tabindex="-1" title="" disabled>
                            @foreach($sayaccaplari as $sayaccapi)
                                @if((Input::old('sayaccap') ? Input::old('sayaccap') : 1 )==$sayaccapi->id)
                                    <option value="{{ $sayaccapi->id }}" selected>{{ $sayaccapi->capadi }}</option>
                                @else
                                    <option value="{{ $sayaccapi->id }}">{{ $sayaccapi->capadi }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <h3 class="col-xs-12">Parçalar ve Ücretleri</h3>
                    <div class="col-xs-12">
                        <div class="table-scrollable">
                            <table id="dataTable" class="table table-hover">
                                <thead>
                                <tr>
                                    <th> # </th>
                                    <th> Parça Adı </th>
                                    <th> Genel Fiyatı </th>
                                    <th> Özel Fiyatı </th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="form-group hide">
                    <input id="genelbirim" name="genelbirim" value="{{ Input::old('genelbirim') ? Input::old('genelbirim') : $genel->parabirimi->birimi }}"/>
                    <input id="ozelbirim" name="ozelbirim" value="{{ Input::old('ozelbirim') ? Input::old('ozelbirim') : $uretimyer->parabirimi->birimi }}"/>
                    <input id="parcasayi" name="parcasayi" value="{{ Input::old('parcasayi') ? Input::old('parcasayi') : $parcalar->count()}} "/>
                </div>
                <div class="form-group">{{ Form::token() }}</div>
            </div>
            <div class="form-actions">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn green">Kaydet</button>
                    <a href="{{ URL::to('digerdatabase/parcaucret')}}" class="btn default">Vazgeç</a>
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
