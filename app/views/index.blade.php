@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Ana Sayfa <small>İstatistikler</small></h1>
</div>
@stop

@section('page-plugins')
@stop

@section('page-styles')
@stop

@section('page-js')
<script src="{{ URL::to('assets/admin/pages/scripts/dashboard.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/moment.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/counterup/jquery.waypoints.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/counterup/jquery.counterup.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/amcharts/amcharts/amcharts.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/amcharts/amcharts/serial.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/amcharts/amcharts/pie.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/amcharts/amcharts/radar.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/amcharts/amcharts/themes/light.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/amcharts/amcharts/themes/patterns.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/amcharts/amcharts/themes/chalk.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/amcharts/ammap/ammap.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/amcharts/ammap/maps/js/worldLow.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/amcharts/amstockcharts/amstock.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/amcharts/amcharts/lang/tr.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/fullcalendar/fullcalendar.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/flot/jquery.flot.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/flot/jquery.flot.resize.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/flot/jquery.flot.categories.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery.sparkline.min.js')}}" type="text/javascript"></script>

<script src="{{ URL::to('assets/global/plugins/brainsocket/js/modernizr-2.6.2.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/brainsocket/js/brain-socket.min.js')}}" type="text/javascript"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
   Dashboard.init(); // init index page
});
</script>
<script>
$(document).ready(function() {
    var chartDatayapilan = [];
    var chartDatagelen = [];
    var chartgelen = AmCharts.makeChart("dashboard_amchart_1", {
        type: "serial",
        "language": "tr",
        fontSize: 12,
        fontFamily: "Open Sans",
        dataDateFormat: "YYYY-MM-DD",
        dataProvider: [
            {"date": '2016-01-01', "aralik": '1. Gun', "gelen": 0, "biten": 0, "townSize": 10}
        ],

        addClassNames: true,
        startDuration: 1,
        color: "#6c7b88",
        marginLeft: 0,

        categoryField: "date",
        categoryAxis: {
            parseDates: true,
            minPeriod: "DD",
            autoGridCount: false,
            gridCount: 10,
            gridAlpha: 0.1,
            gridColor: "#FFFFFF",
            axisColor: "#555555",
            dateFormats: [{
                period: 'DD',
                format: 'DD'
            }, {
                period: 'WW',
                format: 'MMM DD'
            }, {
                period: 'MM',
                format: 'MMM'
            }, {
                period: 'YYYY',
                format: 'YYYY'
            }]
        },

        valueAxes: [{
            id: "a1",
            title: "Gelen Sayaç",
            gridAlpha: 0,
            axisAlpha: 0
        }, {
            id: "a2",
            title: "Giden Sayaç",
            position: "right",
            gridAlpha: 0,
            axisAlpha: 0,
            labelsEnabled: true
        }],
        graphs: [{
            id: "g1",
            valueField: "gelen",
            title: "Gelen",
            type: "column",
            fillAlphas: 0.7,
            valueAxis: "a1",
            balloonText: "[[value]] Adet",
            legendValueText: "[[value]] Adet",
            legendPeriodValueText: "Toplam: [[value.sum]] Adet",
            lineColor: "#08a3cc",
            alphaField: "alpha"
        }, {
            id: "g2",
            valueField: "biten",
            classNameField: "bulletClass",
            title: "Biten",
            type: "line",
            valueAxis: "a2",
            lineColor: "#786c56",
            lineThickness: 1,
            legendValueText: "[[description]]:[[value]] Adet",
            descriptionField: "aralik",
            bullet: "round",
            bulletSizeField: "townSize",
            bulletBorderColor: "#02617a",
            bulletBorderAlpha: 1,
            bulletBorderThickness: 2,
            bulletColor: "#89c4f4",
            labelText: "[[townName2]]",
            labelPosition: "right",
            balloonText: "Biten:[[value]]",
            showBalloon: true,
            animationPlayed: true
        }],

        chartCursor: {
            zoomable: false,
            categoryBalloonDateFormat: "DD",
            cursorAlpha: 0,
            categoryBalloonColor: "#e26a6a",
            categoryBalloonAlpha: 0.8,
            valueBalloonsEnabled: false
        },
        legend: {
            bulletType: "round",
            equalWidths: true,
            valueWidth: 120,
            useGraphSettings: true,
            color: "#6c7b88"
        }
    });
    var chartyapilan = AmCharts.makeChart("dashboard_amchart_4", {
        "type": "pie",
        "language": "tr",
        "theme": "light",
        "path": "../assets/global/plugins/amcharts/ammap/images/",
        "dataProvider": [
            {durum: "Depo Gelen", "value": 0},
            {durum: "Sayaç Kayıt", "value": 0},
            {durum: "Arıza Kayıt", "value": 0},
            {durum: "Ucretlendirme", "value": 0},
            {durum: "Form Gonderimi", "value": 0},
            {durum: "Onaylama", "value": 0},
            {durum: "Reddetme", "value": 0},
            {durum: "Tekrar Ücretlendirme", "value": 0},
            {durum: "Kalibrasyon", "value": 0},
            {durum: "Depo Teslim", "value": 0},
            {durum: "Geri Gönderim", "value": 0},
            {durum: "Hurdaya Ayırma", "value": 0},
            {durum: "Diğer Durumlar", "value": 0}
        ],
        "valueField": "value",
        "titleField": "durum",
        "outlineAlpha": 0.4,
        "height": 400,
        "depth3D": 15,
        "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
        "angle": 5,
        "export": {
            "enabled": true
        }
    });
    jQuery('.chart-input').off().on('input change', function () {
        var property = jQuery(this).data('property');
        var target = chartyapilan;
        var value = Number(this.value);
        chartyapilan.startDuration = 0;
        var radius = "";
        if (property === 'innerRadius') {
            radius = value+"%";
        }

        target[property] = radius;
        chartyapilan.validateNow();
    });
    var legend = new AmCharts.AmLegend();
    legend.data = [{title: "Depo Gelen", color: "#67B7DC"},
        {title: "Sayaç Kayıt", color: "#FDD400"},
        {title: "Arıza Kayıt", color: "#84B761"},
        {title: "Ucretlendirme", color: "#CC4748"},
        {title: "Form Gonderimi", color: "#CD82AD"},
        {title: "Onaylama", color: "#2F4074"},
        {title: "Reddetme", color: "#448E4D"},
        {title: "Tekrar Ücretlendirme", color: "#B7B83F"},
        {title: "Kalibrasyon", color: "#B9783F"},
        {title: "Depo Teslim", color: "#B93E3D"},
        {title: "Geri Gönderim", color: "#913167"},
        {title: "Hurdaya Ayırma", color: "#C52BD4"},
        {title: "Diğer Durumlar", color: "#4250E7"}];
    chartyapilan.addLegend(legend);
    var tarih = $('.tarih').val();
    var servisler = $('.servisler').val();
    if (tarih !== '') {
        $.getJSON("{{ URL::to('main/chartbilgi') }}",{tarih:tarih}, function (event) {
            var data = event.data;
            var servisid=event.servisid;
            var icerik;
            if(servisid==="0"){
                $('.susayac').html(data[0]['toplamgelen']);
                $('.elksayac').html(data[1]['toplamgelen']);
                $('.gazsayac').html(data[2]['toplamgelen']);
                $('.isisayac').html(data[3]['toplamgelen']);
                $('.mekgazsayac').html(data[4]['toplamgelen']);
                $('.subesayac').html(data[5]['toplamgelen']);
                document.getElementById('suprogress1').style.width = data[0]['oran'] + '%';
                $('.suprogress2').html(data[0]['oran'] + '%');
                $('.suprogress3').html(data[0]['oran'] + '%');
                document.getElementById('elkprogress1').style.width = data[1]['oran'] + '%';
                $('.elkprogress2').html(data[1]['oran'] + '%');
                $('.elkprogress3').html(data[1]['oran'] + '%');
                document.getElementById('gazprogress1').style.width = data[2]['oran'] + '%';
                $('.gazprogress2').html(data[2]['oran'] + '%');
                $('.gazprogress3').html(data[2]['oran'] + '%');
                document.getElementById('isiprogress1').style.width = data[3]['oran'] + '%';
                $('.isiprogress2').html(data[3]['oran'] + '%');
                $('.isiprogress3').html(data[3]['oran'] + '%');
                document.getElementById('mekgazprogress1').style.width = data[4]['oran'] + '%';
                $('.mekgazprogress2').html(data[4]['oran'] + '%');
                $('.mekgazprogress3').html(data[4]['oran'] + '%');
                document.getElementById('subeprogress1').style.width = data[5]['oran'] + '%';
                $('.subeprogress2').html(data[5]['oran'] + '%');
                $('.subeprogress3').html(data[5]['oran'] + '%');
            }else if(servisid==="1" || servisid==="3" || servisid==="4"){
                $('.susayac').html(data[0]['toplamgelen']);
                $('.gazsayac').html(data[2]['toplamgelen']);
                $('.isisayac').html(data[3]['toplamgelen']);
                document.getElementById('suprogress1').style.width = data[0]['oran'] + '%';
                $('.suprogress2').html(data[0]['oran'] + '%');
                $('.suprogress3').html(data[0]['oran'] + '%');
                document.getElementById('gazprogress1').style.width = data[2]['oran'] + '%';
                $('.gazprogress2').html(data[2]['oran'] + '%');
                $('.gazprogress3').html(data[2]['oran'] + '%');
                document.getElementById('isiprogress1').style.width = data[3]['oran'] + '%';
                $('.isiprogress2').html(data[3]['oran'] + '%');
                $('.isiprogress3').html(data[3]['oran'] + '%');
            }else if(servisid==="2"){
                $('.elksayac').html(data[1]['toplamgelen']);
                document.getElementById('elkprogress1').style.width = data[1]['oran'] + '%';
                $('.elkprogress2').html(data[1]['oran'] + '%');
                $('.elkprogress3').html(data[1]['oran'] + '%');
            }else if(servisid==="5"){
                $('.mekgazsayac').html(data[4]['toplamgelen']);
                document.getElementById('mekgazprogress1').style.width = data[4]['oran'] + '%';
                $('.mekgazprogress2').html(data[4]['oran'] + '%');
                $('.mekgazprogress3').html(data[4]['oran'] + '%');
            }else{
                $('.subesayac').html(data[5]['toplamgelen']);
                document.getElementById('subeprogress1').style.width = data[5]['oran'] + '%';
                $('.subeprogress2').html(data[5]['oran'] + '%');
                $('.subeprogress3').html(data[5]['oran'] + '%');
            }
            $("[data-counter='counterup']").counterUp({});
            $.each(data, function (index) {
                var list = [];
                var list2 = [];
                var kalan = data[index].kalanlar;
                var gelenbiten = data[index].gelenbiten;
                list.push({durum: "Depo Gelen", "value": kalan[0]});
                list.push({durum: "Sayaç Kayıt", "value": kalan[1]});
                list.push({durum: "Arıza Kayıt", "value": kalan[2]});
                list.push({durum: "Ucretlendirme", "value": kalan[3]});
                list.push({durum: "Form Gonderimi", "value": kalan[4]});
                list.push({durum: "Onaylama", "value": kalan[5]});
                list.push({durum: "Reddetme", "value": kalan[6]});
                list.push({durum: "Tekrar Ücretlendirme", "value": kalan[7]});
                list.push({durum: "Kalibrasyon", "value": kalan[8]});
                list.push({durum: "Depo Teslim", "value": kalan[9]});
                list.push({durum: "Geri Gönderim", "value": kalan[10]});
                list.push({durum: "Hurdaya Ayırma", "value": kalan[11]});
                list.push({durum: "Diğer Durumlar", "value": kalan[12]});
                chartDatayapilan.push(list);
                $.each(gelenbiten, function (index) {
                    list2.push({
                        "date": gelenbiten[index].date,
                        "aralik": gelenbiten[index].deger + '. ' + gelenbiten[index].bolum,
                        "gelen": gelenbiten[index].depogelen,
                        "biten": gelenbiten[index].biten,
                        "townSize": 10
                    });
                });
                chartDatagelen.push(list2);
            });
            chartyapilan.dataProvider = chartDatayapilan[servisler];
            chartyapilan.validateData();
            chartgelen.dataProvider = chartDatagelen[servisler];
            chartgelen.dataDateFormat = "YYYY MM DD";
            chartgelen.categoryAxis = {minPeriod: "DD"};
            chartgelen.validateData();
            icerik = $(".servisler option[value='" + servisler + "']").text();
            $('.servisbilgi').html(icerik);
        });
    }
    $('.servisler').on('change', function () {
        var servis = $(this).val();
        var icerik = $(".servisler option[value='" + servis + "']").text();
        chartyapilan.dataProvider = chartDatayapilan[servis];
        chartyapilan.validateData();
        chartgelen.dataProvider = chartDatagelen[servis];
        switch (tarih) {
            case "0" : //bu yıl
                chartgelen.dataDateFormat = "YYYY MM DD";
                chartgelen.categoryAxis = {minPeriod: "DD"};
                break;
            case "1" : // bugün
                chartgelen.dataDateFormat = "DD HH";
                chartgelen.categoryAxis = {minPeriod: "DD"};
                break;
            case "2" : // son bir hafta
                chartgelen.dataDateFormat = "YYYY MM DD";
                chartgelen.categoryAxis = {minPeriod: "DD"};
                break;
            case "3" : // son 2 hafta
                chartgelen.dataDateFormat = "YYYY MM DD";
                chartgelen.categoryAxis = {minPeriod: "DD"};
                break;
            case "4" : // son 1 ay
                chartgelen.dataDateFormat = "YYYY MM DD";
                chartgelen.categoryAxis = {minPeriod: "WW"};
                break;
            case "5" : //son 3 ay
                chartgelen.dataDateFormat = "YYYY MM DD";
                chartgelen.categoryAxis = {minPeriod: "WW"};
                break;
            case "6" : // son 6 ay
                chartgelen.dataDateFormat = "YYYY MM";
                chartgelen.categoryAxis = {minPeriod: "MM"};
                break;
            case "7" : // son 1 yıl
                chartgelen.dataDateFormat = "YYYY MM";
                chartgelen.categoryAxis = {minPeriod: "MM"};
                break;
            case "8" : // tamamı
                chartgelen.dataDateFormat = "YYYY";
                chartgelen.categoryAxis = {minPeriod: "YYYY"};
                break;
            default:
                chartgelen.dataDateFormat = "YYYY";
                chartgelen.categoryAxis = {minPeriod: "YYYY"};
                break;
        }
        chartgelen.validateData();
        $('.servisbilgi').html(icerik);
    });

    $('.tarih').on('change', function () {
        var tarih = $(this).val();
        $.getJSON("{{ URL::to('main/chartbilgi') }}",{tarih:tarih}, function (event) {
            var data = event.data;
            var servisid=event.servisid;
            if(servisid==="0"){
                $('.susayac').html(data[0]['toplamgelen']);
                $('.elksayac').html(data[1]['toplamgelen']);
                $('.gazsayac').html(data[2]['toplamgelen']);
                $('.isisayac').html(data[3]['toplamgelen']);
                $('.mekgazsayac').html(data[4]['toplamgelen']);
                $('.subesayac').html(data[5]['toplamgelen']);
                document.getElementById('suprogress1').style.width = data[0]['oran'] + '%';
                $('.suprogress2').html(data[0]['oran'] + '%');
                $('.suprogress3').html(data[0]['oran'] + '%');
                document.getElementById('elkprogress1').style.width = data[1]['oran'] + '%';
                $('.elkprogress2').html(data[1]['oran'] + '%');
                $('.elkprogress3').html(data[1]['oran'] + '%');
                document.getElementById('gazprogress1').style.width = data[2]['oran'] + '%';
                $('.gazprogress2').html(data[2]['oran'] + '%');
                $('.gazprogress3').html(data[2]['oran'] + '%');
                document.getElementById('isiprogress1').style.width = data[3]['oran'] + '%';
                $('.isiprogress2').html(data[3]['oran'] + '%');
                $('.isiprogress3').html(data[3]['oran'] + '%');
                document.getElementById('mekgazprogress1').style.width = data[4]['oran'] + '%';
                $('.mekgazprogress2').html(data[4]['oran'] + '%');
                $('.mekgazprogress3').html(data[4]['oran'] + '%');
                document.getElementById('subeprogress1').style.width = data[5]['oran'] + '%';
                $('.subeprogress2').html(data[5]['oran'] + '%');
                $('.subeprogress3').html(data[5]['oran'] + '%');
            }else if(servisid==="1" || servisid==="3" || servisid==="4"){
                $('.susayac').html(data[0]['toplamgelen']);
                $('.gazsayac').html(data[2]['toplamgelen']);
                $('.isisayac').html(data[3]['toplamgelen']);
                document.getElementById('suprogress1').style.width = data[0]['oran'] + '%';
                $('.suprogress2').html(data[0]['oran'] + '%');
                $('.suprogress3').html(data[0]['oran'] + '%');
                document.getElementById('gazprogress1').style.width = data[2]['oran'] + '%';
                $('.gazprogress2').html(data[2]['oran'] + '%');
                $('.gazprogress3').html(data[2]['oran'] + '%');
                document.getElementById('isiprogress1').style.width = data[3]['oran'] + '%';
                $('.isiprogress2').html(data[3]['oran'] + '%');
                $('.isiprogress3').html(data[3]['oran'] + '%');
            }else if(servisid==="2"){
                $('.elksayac').html(data[1]['toplamgelen']);
                document.getElementById('elkprogress1').style.width = data[1]['oran'] + '%';
                $('.elkprogress2').html(data[1]['oran'] + '%');
                $('.elkprogress3').html(data[1]['oran'] + '%');
            }else if(servisid==="5"){
                $('.mekgazsayac').html(data[4]['toplamgelen']);
                document.getElementById('mekgazprogress1').style.width = data[4]['oran'] + '%';
                $('.mekgazprogress2').html(data[4]['oran'] + '%');
                $('.mekgazprogress3').html(data[4]['oran'] + '%');
            }else{
                $('.subesayac').html(data[5]['toplamgelen']);
                document.getElementById('subeprogress1').style.width = data[5]['oran'] + '%';
                $('.subeprogress2').html(data[5]['oran'] + '%');
                $('.subeprogress3').html(data[5]['oran'] + '%');
            }
            $("[data-counter='counterup']").counterUp({});
            chartDatayapilan = [];
            chartDatagelen = [];
            $.each(data, function (index) {
                var list = [];
                var list2 = [];
                var kalan = data[index].kalanlar;
                var gelenbiten = data[index].gelenbiten;
                list.push({durum: "Depo Gelen", "value": kalan[0]});
                list.push({durum: "Sayaç Kayıt", "value": kalan[1]});
                list.push({durum: "Arıza Kayıt", "value": kalan[2]});
                list.push({durum: "Ucretlendirme", "value": kalan[3]});
                list.push({durum: "Form Gonderimi", "value": kalan[4]});
                list.push({durum: "Onaylama", "value": kalan[5]});
                list.push({durum: "Reddetme", "value": kalan[6]});
                list.push({durum: "Tekrar Ücretlendirme", "value": kalan[7]});
                list.push({durum: "Kalibrasyon", "value": kalan[8]});
                list.push({durum: "Depo Teslim", "value": kalan[9]});
                list.push({durum: "Geri Gönderim", "value": kalan[10]});
                list.push({durum: "Hurdaya Ayırma", "value": kalan[11]});
                list.push({durum: "Diğer Durumlar", "value": kalan[12]});
                chartDatayapilan.push(list);
                $.each(gelenbiten, function (index) {
                    list2.push({
                        "date": gelenbiten[index].date,
                        "aralik": gelenbiten[index].deger + '. ' + gelenbiten[index].bolum,
                        "gelen": gelenbiten[index].depogelen,
                        "biten": gelenbiten[index].biten,
                        "townSize": 10
                    });
                });
                chartDatagelen.push(list2);
            });
            var servis = $('.servisler').val();
            chartyapilan.dataProvider = chartDatayapilan[servis];
            chartyapilan.validateData();
            chartgelen.dataProvider = chartDatagelen[servis];
            switch (servis) {
                case "0" : //bu yıl
                    chartgelen.dataDateFormat = "YYYY MM DD";
                    chartgelen.categoryAxis = {minPeriod: "DD"};
                    break;
                case "1" : // bugün
                    chartgelen.dataDateFormat = "DD HH";
                    chartgelen.categoryAxis = {minPeriod: "DD"};
                    break;
                case "2" : // son bir hafta
                    chartgelen.dataDateFormat = "YYYY MM DD";
                    chartgelen.categoryAxis = {minPeriod: "DD"};
                    break;
                case "3" : // son 2 hafta
                    chartgelen.dataDateFormat = "YYYY MM DD";
                    chartgelen.categoryAxis = {minPeriod: "DD"};
                    break;
                case "4" : // son 1 ay
                    chartgelen.dataDateFormat = "YYYY MM DD";
                    chartgelen.categoryAxis = {minPeriod: "WW"};
                    break;
                case "5" : //son 3 ay
                    chartgelen.dataDateFormat = "YYYY MM DD";
                    chartgelen.categoryAxis = {minPeriod: "WW"};
                    break;
                case "6" : // son 6 ay
                    chartgelen.dataDateFormat = "YYYY MM";
                    chartgelen.categoryAxis = {minPeriod: "MM"};
                    break;
                case "7" : // son 1 yıl
                    chartgelen.dataDateFormat = "YYYY MM";
                    chartgelen.categoryAxis = {minPeriod: "MM"};
                    break;
                case "8" : // tamamı
                    chartgelen.dataDateFormat = "YYYY";
                    chartgelen.categoryAxis = {minPeriod: "YYYY"};
                    break;
                default:
                    chartgelen.dataDateFormat = "YYYY";
                    chartgelen.categoryAxis = {minPeriod: "YYYY"};
                    break;
            }
            chartgelen.validateData();
        });
    });
});
</script>
{{--<script src="https://js.pusher.com/4.4/pusher.min.js"></script>
<script>

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = false;

    var pusher = new Pusher('3eb2d03eb0b2222b161f', {
        cluster: 'eu',
        forceTLS: false
    });

    var channel = pusher.subscribe('my-channel');
    channel.bind('my-event', function(data) {
        alert(JSON.stringify(data));
    });
</script>--}}
@stop

@section('content')
    <div class="hide">{{$servisid=Auth::user()->servis_id}}</div>
<div class="portlet light bordered col-xs-12" style="margin-bottom: 10px">
    <div class="portlet-title">
        <div class="caption caption-md">
            <i class="icon-bar-chart font-red"></i>
            <span class="caption-subject font-red bold uppercase">DEPOLARA GELEN SAYAÇ BİLGİSİ</span>
            <span class="caption-helper"></span>
        </div>
        <div class="actions">
            <div class="btn-group btn-group-devided" data-toggle="buttons">
                <label class="btn btn-transparent green btn-outline btn-circle btn-sm active">
                    <input type="radio" name="tarih" class="toggle tarih" value="1" id="option1">Bu Yıl</label>
                <label class="btn btn-transparent green btn-outline btn-circle btn-sm">
                    <input type="radio" name="tarih" class="toggle tarih" value="2" id="option2">Bugün</label>
                <label class="btn btn-transparent green btn-outline btn-circle btn-sm">
                    <input type="radio" name="tarih" class="toggle tarih" value="3" id="option3">Son 1 Hafta</label>
                <label class="btn btn-transparent green btn-outline btn-circle btn-sm">
                    <input type="radio" name="tarih" class="toggle tarih" value="4" id="option4">Son 2 Hafta</label>
                <label class="btn btn-transparent green btn-outline btn-circle btn-sm">
                    <input type="radio" name="tarih" class="toggle tarih" value="5" id="option5">Son 1 Ay</label>
                <label class="btn btn-transparent green btn-outline btn-circle btn-sm">
                    <input type="radio" name="tarih" class="toggle tarih" value="6" id="option6">Son 3 Ay</label>
                <label class="btn btn-transparent green btn-outline btn-circle btn-sm">
                    <input type="radio" name="tarih" class="toggle tarih" value="7" id="option7">Son 6 Ay</label>
                <label class="btn btn-transparent green btn-outline btn-circle btn-sm">
                    <input type="radio" name="tarih" class="toggle tarih" value="8" id="option8">Son 1 Yıl</label>
                <label class="btn btn-transparent green btn-outline btn-circle btn-sm">
                    <input type="radio" name="tarih" class="toggle tarih" value="9" id="option9">Tamamı</label>
            </div>
        </div>
    </div>
    @if($servisid==0 || $servisid==1 || $servisid==3 || $servisid==4)
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-blue-sharp">
                        <span data-counter="counterup" class="susayac" data-value="0">0</span>
                        <small class="font-blue-sharp"></small>
                    </h3>
                    <small>SU SERVİS</small>
                </div>
                <div class="icon">
                    <i class="icon-drop"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                        <span style="width: 0;"  id="suprogress1" class="progress-bar progress-bar-success blue-sharp">
                            <span class="sr-only suprogress2">0%</span>
                        </span>
                </div>
                <div class="status">
                    <div class="status-title"> BİTEN </div>
                    <div class="status-number suprogress3"> 0% </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($servisid==0 || $servisid==2)
        @if($servisid==2)
    <div class="col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-red-haze">
                        <span data-counter="counterup" class="elksayac" data-value="0">0</span>
                    </h3>
                    <small>ELEKTRİK SERVİS</small>
                </div>
                <div class="icon">
                    <i class="icon-energy"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                        <span style="width: 0;" id="elkprogress1" class="progress-bar progress-bar-success red-haze">
                            <span class="sr-only elkprogress2">0%</span>
                        </span>
                </div>
                <div class="status">
                    <div class="status-title"> BİTEN </div>
                    <div class="status-number elkprogress3"> 0% </div>
                </div>
            </div>
        </div>
    </div>
        @else
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-red-haze">
                        <span data-counter="counterup" class="elksayac" data-value="0">0</span>
                    </h3>
                    <small>ELEKTRİK SERVİS</small>
                </div>
                <div class="icon">
                    <i class="icon-energy"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                        <span style="width: 0;" id="elkprogress1" class="progress-bar progress-bar-success red-haze">
                            <span class="sr-only elkprogress2">0%</span>
                        </span>
                </div>
                <div class="status">
                    <div class="status-title"> BİTEN </div>
                    <div class="status-number elkprogress3"> 0% </div>
                </div>
            </div>
        </div>
    </div>
        @endif
    @endif
    @if($servisid==0 || $servisid==1 || $servisid==3 || $servisid==4)
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-yellow-gold">
                        <span data-counter="counterup" class="gazsayac" data-value="0">0</span>
                    </h3>
                    <small>GAZ SERVİS</small>
                </div>
                <div class="icon">
                    <i class="icon-fire"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                        <span style="width: 0;" id="gazprogress1" class="progress-bar progress-bar-success yellow-gold">
                            <span class="sr-only gazprogress2">0%</span>
                        </span>
                </div>
                <div class="status">
                    <div class="status-title"> BİTEN </div>
                    <div class="status-number gazprogress3"> 0% </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($servisid==0 || $servisid==1 || $servisid==3 || $servisid==4)
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-yellow-lemon">
                        <span id="isisayac" data-counter="counterup" class="isisayac" data-value="0">0</span>
                    </h3>
                    <small>ISI SERVİS</small>
                </div>
                <div class="icon">
                    <i class="icon-pointer"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                        <span style="width: 0;" id="isiprogress1" class="progress-bar progress-bar-success yellow-lemon">
                            <span class="sr-only isiprogress2">0%</span>
                        </span>
                </div>
                <div class="status">
                    <div class="status-title"> BİTEN </div>
                    <div class="status-number isiprogress3"> 0% </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($servisid==0 || $servisid==5)
        @if($servisid==5)
    <div class="col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-purple-soft">
                        <span data-counter="counterup" class="mekgazsayac" data-value="0">0</span>
                    </h3>
                    <small>GAZ SERVİS MEKANİK</small>
                </div>
                <div class="icon">
                    <i class="icon-reload"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                        <span style="width: 0;" id="mekgazprogress1" class="progress-bar progress-bar-success purple-soft">
                            <span class="sr-only mekgazprogress2">0%</span>
                        </span>
                </div>
                <div class="status">
                    <div class="status-title"> BİTEN </div>
                    <div class="status-number mekgazprogress3"> 0% </div>
                </div>
            </div>
        </div>
    </div>
        @else
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-purple-soft">
                        <span data-counter="counterup" class="mekgazsayac" data-value="0">0</span>
                    </h3>
                    <small>GAZ SERVİS MEKANİK</small>
                </div>
                <div class="icon">
                    <i class="icon-reload"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                        <span style="width: 0;" id="mekgazprogress1" class="progress-bar progress-bar-success purple-soft">
                            <span class="sr-only mekgazprogress2">0%</span>
                        </span>
                </div>
                <div class="status">
                    <div class="status-title"> BİTEN </div>
                    <div class="status-number mekgazprogress3"> 0% </div>
                </div>
            </div>
        </div>
    </div>
        @endif
    @endif
    @if($servisid==0 || $servisid==6)
        @if($servisid==6)
    <div class="col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-green-sharp">
                        <span data-counter="counterup" class="subesayac" data-value="0">0</span>
                    </h3>
                    <small>ŞUBELER</small>
                </div>
                <div class="icon">
                    <i class="icon-share"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                        <span style="width: 0;" id="subeprogress1" class="progress-bar progress-bar-success green-sharp">
                            <span class="sr-only subeprogress2">0%</span>
                        </span>
                </div>
                <div class="status">
                    <div class="status-title"> BİTEN </div>
                    <div class="status-number subeprogress3"> 0% </div>
                </div>
            </div>
        </div>
    </div>
        @else
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-green-sharp">
                        <span data-counter="counterup" class="subesayac" data-value="0">0</span>
                    </h3>
                    <small>ŞUBELER</small>
                </div>
                <div class="icon">
                    <i class="icon-share"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                        <span style="width: 0;" id="subeprogress1" class="progress-bar progress-bar-success green-sharp">
                            <span class="sr-only subeprogress2">0%</span>
                        </span>
                </div>
                <div class="status">
                    <div class="status-title"> BİTEN </div>
                    <div class="status-number subeprogress3"> 0% </div>
                </div>
            </div>
        </div>
    </div>
        @endif
    @endif
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green">
                    <span class="caption-subject bold uppercase">SAYAÇ GELEN GİDEN BİLGİSİ</span>
                    <span class="caption-helper servisbilgi"></span>
                </div>
                <div class="actions">
                    <div class="btn-group">
                        <select class="servisler">
                            @if($servisid==0)
                                <option value="0" selected >Su Servis</option>
                                <option value="1" >Elektrik Servis</option>
                                <option value="2" >Gaz Servis</option>
                                <option value="3" >Isı Servis</option>
                                <option value="4" >Gaz Servis Mekanik</option>
                                <option value="5" >Şube</option>
                            @elseif($servisid==1)
                                <option value="0" selected>Su Servis</option>
                                <option value="2" >Gaz Servis</option>
                                <option value="3" >Isı Servis</option>
                            @elseif($servisid==2)
                                <option value="1" selected>Elektrik Servis</option>
                            @elseif($servisid==3)
                                <option value="0" >Su Servis</option>
                                <option value="2" selected>Gaz Servis</option>
                                <option value="3" >Isı Servis</option>
                            @elseif($servisid==4)
                                <option value="0" >Su Servis</option>
                                <option value="2" >Gaz Servis</option>
                                <option value="3" selected>Isı Servis</option>
                            @elseif($servisid==5)
                                <option value="4" selected>Gaz Servis Mekanik</option>
                            @elseif($servisid==6)
                                <option value="5" selected>Şube</option>
                            @endif
                        </select>
                    </div>
                    <a class="btn btn-circle btn-icon-only btn-default hide" href="#">
                        <i class="icon-cloud-upload"></i>
                    </a>
                    <a class="btn btn-circle btn-icon-only btn-default hide" href="#">
                        <i class="icon-wrench"></i>
                    </a>
                    <a class="btn btn-circle btn-icon-only btn-default hide" href="#">
                        <i class="icon-trash"></i>
                    </a>
                    <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div id="dashboard_amchart_1" class="CSSAnimationChart"></div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green">
                    <span class="caption-subject bold uppercase">SERVİS BİLGİSİ</span>
                    <span class="caption-helper servisbilgi"></span>
                </div>
                <div class="actions">
                </div>
            </div>
            <div class="portlet-body">
                <div id="dashboard_amchart_4" class="CSSAnimationChart"></div>
            </div>
        </div>
    </div>
</div>
@stop
