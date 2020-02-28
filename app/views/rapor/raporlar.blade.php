@extends('layout.master')

@section('page-title')
<!--suppress NonAsciiCharacters -->
<div class="page-title">
    <h1>Rapor <small>Ekranı</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::to('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2_locale_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/moment-with-locales.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery.quicksearch.js') }}" type="text/javascript" ></script>
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
    $(document).ready(function() {
        $('#rootwizard').bootstrapWizard({
            'tabClass': 'nav nav-pills',
            onTabClick: function() {
                return false;
            },
            onNext: function(tab, navigation, index) {
                if($('#raporadi').val()===""){
                    toastr['warning']('Rapor Seçilmedi!', 'Rapor Hatası');
                    return false;
                }else{
                    var raporadi=$('#raporadi').select2('data').text;
                    $('.raporadi').html(raporadi);
                }
                if(index===2){
                    var kriterler="";
                    var kriter=$('.kriterlist').val();
                    var kriteraciklama=$('.kriteraciklamalist').val();
                    var kriterlist=kriter.split(',');
                    var aciklamalist=kriteraciklama.split(',');
                    $.each(kriterlist,function(index){
                       var bilgi=$('#'+kriterlist[index]).val();
                        kriterler+=(kriterler==="" ? "" : ";")+aciklamalist[index]+':'+bilgi;
                    });
                    $('.kriterler').html(kriterler);
                }
            }
        });

        $('#raporadi').on('change', function () {
            var id = $(this).val();
            if (id !== "") {
                $.blockUI();
                $.getJSON("{{ URL::to('rapor/raporkriter') }}",{id:id}, function (event) {
                    if (event.durum) //rapor kriterlerini çeker
                    {
                        var kriterler=event.kriterler;
                        var kritertab='';
                        var kriterlist='';
                        var kriteraciklamalist='';
                        $('#tab2').empty();
                        $.each(kriterler, function (index) {
                            kriterlist+=(kriterlist==="" ? "" : ",")+kriterler[index].kriteradi;
                            kriteraciklamalist+=(kriteraciklamalist==="" ? "" : ",")+kriterler[index].aciklama;
                            var kriter="#"+(kriterler[index].kriteradi);
                            var data=kriterler[index].data;
                            var onchange="#"+(kriterler[index].onchange);
                            var sorgu=kriterler[index].sorgu;
                            switch(kriterler[index].tipi){
                                case 'select': //select eklenecek kriter adıyla
                                    kritertab+='<div class="form-group"><label class="control-label col-sm-2 col-xs-4">'+kriterler[index].aciklama+':</label>'
                                    +'<div class="col-sm-6 col-xs-8"><select class="form-control select2me select2-offscreen" id="'+kriterler[index].kriteradi+'" name="'+kriterler[index].kriteradi+'" tabindex="-1" title="">'
                                    +'<option value="">Seçiniz...</option>';
                                    $.each(data, function (index2) {
                                    kritertab+='<option value="'+data[index2].id+'">'+data[index2].value+'</option>'
                                    });
                                    kritertab+='</select></div></div>';
                                    $('#tab2').append(kritertab);
                                    kritertab='';
                                    //$('#adi_soyadi').select2();
                                    $(kriter).select2();
                                    if(kriterler[index].onchange!=null || kriterler[index].onchange!==""){
                                        $(onchange).on('change', function () {
                                            var id = $(this).val();
                                            if (id !== "") {
                                                $.getJSON(" {{ URL::to('rapor') }}/"+sorgu+'/'+ id, function (event) {
                                                    if (event.durum) //degisecek veriyi listede göster
                                                    {
                                                        var degisecek = event.degisecek;
                                                        $(kriter).empty();
                                                        $.each(degisecek, function (i) {
                                                            $(kriter).append('<option value="' + degisecek[i].id + '"> ' + degisecek[i].value + '</option>');
                                                        });
                                                        $(kriter).select2();
                                                    } else {
                                                        toastr['warning']('Seçilen Bilgiye Göre Veri Çekilemedi!', 'Veri Bulunamadı!');
                                                    }
                                                });
                                            }
                                        });
                                    }
                                    break;
                                case 'select2': //select eklenecek kriter adıyla
                                    kritertab+='<div class="form-group"><label class="control-label col-sm-2 col-xs-4">'+kriterler[index].aciklama+':</label>'
                                        +'<div class="col-sm-6 col-xs-8"><select class="form-control select2me select2-offscreen" id="'+kriterler[index].kriteradi+'" name="'+kriterler[index].kriteradi+'" tabindex="-1" title="">'
                                        +'<option value="">Seçiniz...</option>'
                                        +'<option value="0">Hepsi</option>';
                                    $.each(data, function (index2) {
                                        kritertab+='<option value="'+data[index2].id+'">'+data[index2].value+'</option>'
                                    });
                                    kritertab+='</select></div></div>';
                                    $('#tab2').append(kritertab);
                                    kritertab='';
                                    //$('#adi_soyadi').select2();
                                    $(kriter).select2();
                                    if(kriterler[index].onchange!=null || kriterler[index].onchange!==""){
                                        $(onchange).on('change', function () {
                                            var id = $(this).val();
                                            if (id !== "") {
                                                $.getJSON(" {{ URL::to('rapor') }}/"+sorgu+'/'+ id, function (event) {
                                                    if (event.durum) //degisecek veriyi listede göster
                                                    {
                                                        var degisecek = event.degisecek;
                                                        $(kriter).empty();
                                                        $.each(degisecek, function (i) {
                                                            $(kriter).append('<option value="' + degisecek[i].id + '"> ' + degisecek[i].value + '</option>');
                                                        });
                                                        $(kriter).select2();
                                                    } else {
                                                        toastr['warning']('Seçilen Bilgiye Göre Veri Çekilemedi!', 'Veri Bulunamadı!');
                                                    }
                                                });
                                            }
                                        });
                                    }
                                    break;
                                case 'daterange': //daterange açılacak kriter adıyla
                                    kritertab+='<div class="form-group"><label class="control-label col-sm-2 col-xs-4">'+kriterler[index].aciklama+':</label>'
                                    +'<div class="col-sm-6 col-xs-8"><div class="input-group" id="daterangepick"><input type="text" id="'+kriterler[index].kriteradi+'" name="'+kriterler[index].kriteradi+'" class="form-control">'
                                    +'<span class="input-group-btn"><button class="btn default date-range-toggle" type="button"><i class="fa fa-calendar"></i></button></span></div></div></div>';
                                    $('#tab2').append(kritertab);
                                    kritertab='';
                                    moment.locale('tr');
                                    //noinspection JSNonASCIINames,JSNonASCIINames,JSNonASCIINames,JSNonASCIINames,JSNonASCIINames
                                    $('#daterangepick').daterangepicker({opens: (Metronic.isRTL() ? 'left' : 'right'), format: 'DD/MM/YYYY', separator: ' - ', startDate: moment().subtract(29,'days'), endDate: moment(), ranges: {'Bugün': [moment(), moment()],'Dün': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],'Son 7 Gün': [moment().subtract(6, 'days'), moment()],'Son 30 Gün': [moment().subtract(29, 'days'), moment()],'Bu Ay': [moment().startOf('month'), moment().endOf('month')],'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]}, minDate: '01/01/2012',maxDate: '31/12/2030',"locale": {"format": "DD/MM/YYYY","separator": " - ","applyLabel": "Tamam","cancelLabel": "İptal","fromLabel": "Başlangıç","toLabel": "Bitiş","customRangeLabel": "Yeni Tarih Gir","daysOfWeek": ["Pz", "Pt", "Sa", "Ça", "Pe", "Cu", "Ct"],"monthNames": ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"],"firstDay": 1}}, function (start, end) {$(kriter).val(start.format('DD.MM.YYYY') + ' - ' + end.format('DD.MM.YYYY'));});
                                    $(kriter).val(moment().subtract(29,'days').format('DD.MM.YYYY')+' - '+moment().format('DD.MM.YYYY'));
                                    break;
                                case 'multiselect': //multiselect açılacak  kriter adıyla
                                    kritertab+='<div class="form-group"><label class="control-label col-sm-2 col-xs-4">'+kriterler[index].aciklama+'</label>'
                                    +'<div class="col-sm-6 col-xs-6"><select multiple="multiple" class="multi-select" id="'+kriterler[index].kriteradi+'" name="'+kriterler[index].kriteradi+'[]" tabindex="-1" title="">'
                                    +'<option value="0">HEPSİ</option>';
                                    $.each(data, function (index2) {
                                        kritertab+='<option value="'+data[index2].id+'">'+data[index2].value+'</option>'
                                    });
                                    kritertab+='</select></div>'+
                                    '<div><button style="margin-top: 5px;margin-left:0 !important;" id="'+kriterler[index].kriteradi+'temizle" type="button" class="btn red">Temizle</button></div></div>';
                                    $('#tab2').append(kritertab);
                                    kritertab='';
                                    var kritertemizle=$("#"+(kriterler[index].kriteradi+"temizle"));
                                    $(kriter).multiSelect({
                                        selectableHeader: "<input type='text' style='width:100%' class='search-input' autocomplete='off' placeholder='Aramak için giriniz'>",
                                        selectionHeader: "<input type='text' style='width:100%' class='search-input' autocomplete='off' placeholder='Aramak için giriniz'>",
                                        afterInit: function(){ var that = this,$selectableSearch = that.$selectableUl.prev(),$selectionSearch = that.$selectionUl.prev(), selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)', selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';
                                            that.qs1 = $selectableSearch.quicksearch(selectableSearchString).on('keydown', function(e){ if (e.which === 40){ that.$selectableUl.focus();return false;}});
                                            that.qs2 = $selectionSearch.quicksearch(selectionSearchString).on('keydown', function(e){if (e.which === 40){ that.$selectionUl.focus();return false;}});
                                        },
                                        afterSelect: function(){ this.qs1.cache();this.qs2.cache();this.refresh();},
                                        afterDeselect: function(){this.qs1.cache(); this.qs2.cache();this.refresh();}
                                    });
                                    if(kriterler[index].onchange!=null || kriterler[index].onchange!==""){
                                        $(onchange).on('change', function () {
                                            var id = $(this).val();
                                            if (id !== "") {
                                                $.getJSON(" {{ URL::to('rapor') }}/"+sorgu+'/'+ id, function (event) {
                                                    if (event.durum) //degisecek veriyi listede göster
                                                    {
                                                        var degisecek = event.degisecek;
                                                        $(kriter).empty();
                                                        $(kriter).append('<option value="0">HEPSİ</option>');
                                                        $.each(degisecek, function (i) {
                                                            $(kriter).append('<option value="' + degisecek[i].id + '"> ' + degisecek[i].value + '</option>');
                                                        });
                                                        $(kriter).multiSelect("refresh");
                                                    } else {
                                                        toastr['warning']('Seçilen Bilgiye Göre Veri Çekilemedi!', 'Veri Bulunamadı!');
                                                    }
                                                });
                                            }
                                        });
                                    }
                                    kritertemizle.on("click", function () {
                                        $(kriter).multiSelect("deselect_all");
                                    });
                                    break;
                                default:
                                    break;
                            }
                        });
                        $('.kriterlist').val(kriterlist);
                        $('.kriteraciklamalist').val(kriteraciklamalist);
                        $('#rootwizard').bootstrapWizard('next');
                        //$('#tab1').removeClass('active');
                        //$('#tab2').addClass('active');
                        //$('#link1').removeClass('active');
                        //$('#link2').addClass('active');
                    } else {
                        toastr[event.type](event.text, event.title);
                    }
                    $.unblockUI();
                });
            }else{
                $('#tab2').empty();
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
            <i class="fa fa-pencil"></i>Rapor Sihirbazı
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ URL::to('rapor') }}" target="_blank" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
            <div class="form-body">
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    Girilen Bilgilerde Hata Var.
                </div>
                <div class="alert alert-success display-hide">
                    <button class="close" data-close="alert"></button>
                    Bilgiler Doğru!
                </div>
                <div id="rootwizard">
                    <ul>
                        <li id="link1"><a href="#tab1" data-toggle="tab">Rapor Seç</a></li>
                        <li id="link2"><a href="#tab2" data-toggle="tab">Kriterleri Gir</a></li>
                        <li id="link3"><a href="#tab3" data-toggle="tab">Çıktı Al</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane" id="tab1">
                            <div class="form-group">
                                <label class="control-label col-sm-2 col-xs-4" for="raporadi">Rapor Adı:</label>
                                <div class="col-sm-6 col-xs-8">
                                    <select class="form-control select2me select2-offscreen" id="raporadi" name="raporadi" tabindex="-1" title="">
                                        <option value="">Seçiniz...</option>
                                        @foreach($raporlar as $rapor)
                                            @if(Input::old('raporadi')==$rapor->id )
                                                <option data-id="{{$rapor->adi}}" value="{{ $rapor->id }}" selected>{{ $rapor->adi }}</option>
                                            @else
                                                <option data-id="{{$rapor->adi}}"  value="{{ $rapor->id }}">{{ $rapor->adi }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab2">
                            <div class="form-group">
                                <label class="control-label col-md-6">Rapor Kriterleri Getiriliyor...</label>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab3">
                            <div class="form-group">
                                <label class="control-label col-sm-2 col-xs-4">Rapor Adı:</label>
                                <label class="col-sm-6 col-xs-8 raporadi" style="padding-top: 7px"></label>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2 col-xs-4">Kriterler:</label>
                                <label class="col-sm-6 col-xs-8 kriterler" style="padding-top: 7px"></label>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2 col-xs-4">Çıktı Tipi:</label>
                                <div class="col-sm-6 col-xs-8">
                                    <select class="form-control select2me select2-offscreen" id="export" name="export" tabindex="-1" title="">
                                        <option value="pdf" selected>Pdf</option>
                                        <option value="xls">Excel</option>
                                        <option value="docx">Word</option>
                                    </select>
                                </div>
                            </div>
                            <input type="text" class="hide kriterlist"/>
                            <input type="text" class="hide kriteraciklamalist"/>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-xs-12" style="text-align: center">
                                        <button type="submit" class="btn green"><i class="fa fa-search" style="margin-right: 3px"></i>Rapor Hazırla</button>
                                        <a href="{{ URL::to('rapor')}}" class="btn default">Vazgeç</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <ul class="pager wizard">
                            <li class="previous"><a href="#">Önceki</a></li>
                            <li class="next"><a href="#">Sonraki</a></li>
                        </ul>
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
