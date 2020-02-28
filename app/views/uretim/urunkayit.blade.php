@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Üretim <small>Ürün Kayıt Ekranı</small></h1>
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
    <script src="{{ URL::to('assets/global/plugins/qz-tray/js/dependencies/rsvp-3.1.0.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/qz-tray/js/dependencies/sha-256.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::to('assets/global/plugins/qz-tray/js/qz-tray.js') }}" type="text/javascript" ></script>
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
    @if(!$mobile)
    <script>
        /// Authentication setup ///
        qz.security.setCertificatePromise(function (resolve) {
            //Preferred method - from server
//        $.ajax({ url: "assets/signing/digital-certificate.txt", cache: false, dataType: "text" }).then(resolve, reject);

            //Alternate method 1 - anonymous
            //resolve();

            //Alternate method 2 - direct
            resolve(
                "-----BEGIN CERTIFICATE-----\n" +
                "MIIEMTCCAxmgAwIBAgIUYiq4RORWHaWGOXu8trITTQKjulMwDQYJKoZIhvcNAQEL\n" +
                "BQAwgaYxCzAJBgNVBAYTAlRSMQ8wDQYDVQQIDAZTaW5jYW4xDzANBgNVBAcMBkFu\n" +
                "a2FyYTEhMB8GA1UECgwYTWFuYXMgRW5lcmppIFlvbmV0aW1pIEFTMQswCQYDVQQL\n" +
                "DAJJVDEcMBoGA1UEAwwTc2VydmlzLm1hbmFzLmNvbS50cjEnMCUGCSqGSIb3DQEJ\n" +
                "ARYYc2VydmlzdGFraXBAbWFuYXMuY29tLnRyMCAXDTE5MDkwNDEyMDMxOFoYDzIw\n" +
                "NTEwMjI3MTIwMzE4WjCBpjELMAkGA1UEBhMCVFIxDzANBgNVBAgMBlNpbmNhbjEP\n" +
                "MA0GA1UEBwwGQW5rYXJhMSEwHwYDVQQKDBhNYW5hcyBFbmVyamkgWW9uZXRpbWkg\n" +
                "QVMxCzAJBgNVBAsMAklUMRwwGgYDVQQDDBNzZXJ2aXMubWFuYXMuY29tLnRyMScw\n" +
                "JQYJKoZIhvcNAQkBFhhzZXJ2aXN0YWtpcEBtYW5hcy5jb20udHIwggEiMA0GCSqG\n" +
                "SIb3DQEBAQUAA4IBDwAwggEKAoIBAQDTdhmLeeV6UVl7m6B6ABkZkpIIYlbkNDuS\n" +
                "oPwRhzYzd0jKLbOlJUcVU1IQSfahIV5qRxPmNM/+HZlzxSb/u7KppeXeX9nknwCe\n" +
                "SvIgvrUkvT54lU12Rsq0EswfEnYDPd/amQHAGFEFVRebc5IjjchmRvyrluQm8q4N\n" +
                "ApRiB/BLvvrtkRmjO66IHeHSJ5BL67RyZAIEp0G31QmSfuv56KOXi2E69MMTy/FR\n" +
                "J8jtEaCiBHmQ0WuqMkuToFjdZdqL1WpRKYFj3Kc38KGThv7dOx9KzJdNuACjIqJF\n" +
                "LcLSznj0chWENVtw/229kuCfU0wZ2Si4UmINRR/iAsdjeu7l9N+VAgMBAAGjUzBR\n" +
                "MB0GA1UdDgQWBBSxpVqwDAydrDcMuwfyAdM58jwxGzAfBgNVHSMEGDAWgBSxpVqw\n" +
                "DAydrDcMuwfyAdM58jwxGzAPBgNVHRMBAf8EBTADAQH/MA0GCSqGSIb3DQEBCwUA\n" +
                "A4IBAQBYZFf8gD0dlkBhiNxQoMKYs2zSjzy7FKtcccVB6Hhyh5yThh4cQePPZ7ax\n" +
                "6zknxnJaOfrfMokDywF/OaET58Zrt57GQk/Z/fWvMiKhfvdR48JntVj0BDoqeemT\n" +
                "WzVP15GqMezB8U32YK7wzS/SSUvnSeL5efcXKkqO+Lbpdr1Z4RSZ7+CDsh68eBTE\n" +
                "iN6NhlkbTB+9F8WrHY0fAT9dqvwRkx+IkqSX2NcrifJ5yt8TLe9mlD6ipvWoQBap\n" +
                "znZXGv3Y33YYjAmXILBxAh4jlN6B5HDv9CNWntt4/Aih2oIh4Z/5YLQkILnA3hd0\n" +
                "vv38ztQotAgUWyaYw/NYBZnKccCJ\n" +
                "-----END CERTIFICATE-----"


            /*"-----BEGIN CERTIFICATE-----\n" +
               "MIIFAzCCAuugAwIBAgICEAIwDQYJKoZIhvcNAQEFBQAwgZgxCzAJBgNVBAYTAlVT\n" +
               "MQswCQYDVQQIDAJOWTEbMBkGA1UECgwSUVogSW5kdXN0cmllcywgTExDMRswGQYD\n" +
               "VQQLDBJRWiBJbmR1c3RyaWVzLCBMTEMxGTAXBgNVBAMMEHF6aW5kdXN0cmllcy5j\n" +
               "b20xJzAlBgkqhkiG9w0BCQEWGHN1cHBvcnRAcXppbmR1c3RyaWVzLmNvbTAeFw0x\n" +
               "NTAzMTkwMjM4NDVaFw0yNTAzMTkwMjM4NDVaMHMxCzAJBgNVBAYTAkFBMRMwEQYD\n" +
               "VQQIDApTb21lIFN0YXRlMQ0wCwYDVQQKDAREZW1vMQ0wCwYDVQQLDAREZW1vMRIw\n" +
               "EAYDVQQDDAlsb2NhbGhvc3QxHTAbBgkqhkiG9w0BCQEWDnJvb3RAbG9jYWxob3N0\n" +
               "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtFzbBDRTDHHmlSVQLqjY\n" +
               "aoGax7ql3XgRGdhZlNEJPZDs5482ty34J4sI2ZK2yC8YkZ/x+WCSveUgDQIVJ8oK\n" +
               "D4jtAPxqHnfSr9RAbvB1GQoiYLxhfxEp/+zfB9dBKDTRZR2nJm/mMsavY2DnSzLp\n" +
               "t7PJOjt3BdtISRtGMRsWmRHRfy882msBxsYug22odnT1OdaJQ54bWJT5iJnceBV2\n" +
               "1oOqWSg5hU1MupZRxxHbzI61EpTLlxXJQ7YNSwwiDzjaxGrufxc4eZnzGQ1A8h1u\n" +
               "jTaG84S1MWvG7BfcPLW+sya+PkrQWMOCIgXrQnAsUgqQrgxQ8Ocq3G4X9UvBy5VR\n" +
               "CwIDAQABo3sweTAJBgNVHRMEAjAAMCwGCWCGSAGG+EIBDQQfFh1PcGVuU1NMIEdl\n" +
               "bmVyYXRlZCBDZXJ0aWZpY2F0ZTAdBgNVHQ4EFgQUpG420UhvfwAFMr+8vf3pJunQ\n" +
               "gH4wHwYDVR0jBBgwFoAUkKZQt4TUuepf8gWEE3hF6Kl1VFwwDQYJKoZIhvcNAQEF\n" +
               "BQADggIBAFXr6G1g7yYVHg6uGfh1nK2jhpKBAOA+OtZQLNHYlBgoAuRRNWdE9/v4\n" +
               "J/3Jeid2DAyihm2j92qsQJXkyxBgdTLG+ncILlRElXvG7IrOh3tq/TttdzLcMjaR\n" +
               "8w/AkVDLNL0z35shNXih2F9JlbNRGqbVhC7qZl+V1BITfx6mGc4ayke7C9Hm57X0\n" +
               "ak/NerAC/QXNs/bF17b+zsUt2ja5NVS8dDSC4JAkM1dD64Y26leYbPybB+FgOxFu\n" +
               "wou9gFxzwbdGLCGboi0lNLjEysHJBi90KjPUETbzMmoilHNJXw7egIo8yS5eq8RH\n" +
               "i2lS0GsQjYFMvplNVMATDXUPm9MKpCbZ7IlJ5eekhWqvErddcHbzCuUBkDZ7wX/j\n" +
               "unk/3DyXdTsSGuZk3/fLEsc4/YTujpAjVXiA1LCooQJ7SmNOpUa66TPz9O7Ufkng\n" +
               "+CoTSACmnlHdP7U9WLr5TYnmL9eoHwtb0hwENe1oFC5zClJoSX/7DRexSJfB7YBf\n" +
               "vn6JA2xy4C6PqximyCPisErNp85GUcZfo33Np1aywFv9H+a83rSUcV6kpE/jAZio\n" +
               "5qLpgIOisArj1HTM6goDWzKhLiR/AeG3IJvgbpr9Gr7uZmfFyQzUjvkJ9cybZRd+\n" +
               "G8azmpBBotmKsbtbAU/I/LVk8saeXznshOVVpDRYtVnjZeAneso7\n" +
               "-----END CERTIFICATE-----\n" +
               "--START INTERMEDIATE CERT--\n" +
               "-----BEGIN CERTIFICATE-----\n" +
               "MIIFEjCCA/qgAwIBAgICEAAwDQYJKoZIhvcNAQELBQAwgawxCzAJBgNVBAYTAlVT\n" +
               "MQswCQYDVQQIDAJOWTESMBAGA1UEBwwJQ2FuYXN0b3RhMRswGQYDVQQKDBJRWiBJ\n" +
               "bmR1c3RyaWVzLCBMTEMxGzAZBgNVBAsMElFaIEluZHVzdHJpZXMsIExMQzEZMBcG\n" +
               "A1UEAwwQcXppbmR1c3RyaWVzLmNvbTEnMCUGCSqGSIb3DQEJARYYc3VwcG9ydEBx\n" +
               "emluZHVzdHJpZXMuY29tMB4XDTE1MDMwMjAwNTAxOFoXDTM1MDMwMjAwNTAxOFow\n" +
               "gZgxCzAJBgNVBAYTAlVTMQswCQYDVQQIDAJOWTEbMBkGA1UECgwSUVogSW5kdXN0\n" +
               "cmllcywgTExDMRswGQYDVQQLDBJRWiBJbmR1c3RyaWVzLCBMTEMxGTAXBgNVBAMM\n" +
               "EHF6aW5kdXN0cmllcy5jb20xJzAlBgkqhkiG9w0BCQEWGHN1cHBvcnRAcXppbmR1\n" +
               "c3RyaWVzLmNvbTCCAiIwDQYJKoZIhvcNAQEBBQADggIPADCCAgoCggIBANTDgNLU\n" +
               "iohl/rQoZ2bTMHVEk1mA020LYhgfWjO0+GsLlbg5SvWVFWkv4ZgffuVRXLHrwz1H\n" +
               "YpMyo+Zh8ksJF9ssJWCwQGO5ciM6dmoryyB0VZHGY1blewdMuxieXP7Kr6XD3GRM\n" +
               "GAhEwTxjUzI3ksuRunX4IcnRXKYkg5pjs4nLEhXtIZWDLiXPUsyUAEq1U1qdL1AH\n" +
               "EtdK/L3zLATnhPB6ZiM+HzNG4aAPynSA38fpeeZ4R0tINMpFThwNgGUsxYKsP9kh\n" +
               "0gxGl8YHL6ZzC7BC8FXIB/0Wteng0+XLAVto56Pyxt7BdxtNVuVNNXgkCi9tMqVX\n" +
               "xOk3oIvODDt0UoQUZ/umUuoMuOLekYUpZVk4utCqXXlB4mVfS5/zWB6nVxFX8Io1\n" +
               "9FOiDLTwZVtBmzmeikzb6o1QLp9F2TAvlf8+DIGDOo0DpPQUtOUyLPCh5hBaDGFE\n" +
               "ZhE56qPCBiQIc4T2klWX/80C5NZnd/tJNxjyUyk7bjdDzhzT10CGRAsqxAnsjvMD\n" +
               "2KcMf3oXN4PNgyfpbfq2ipxJ1u777Gpbzyf0xoKwH9FYigmqfRH2N2pEdiYawKrX\n" +
               "6pyXzGM4cvQ5X1Yxf2x/+xdTLdVaLnZgwrdqwFYmDejGAldXlYDl3jbBHVM1v+uY\n" +
               "5ItGTjk+3vLrxmvGy5XFVG+8fF/xaVfo5TW5AgMBAAGjUDBOMB0GA1UdDgQWBBSQ\n" +
               "plC3hNS56l/yBYQTeEXoqXVUXDAfBgNVHSMEGDAWgBQDRcZNwPqOqQvagw9BpW0S\n" +
               "BkOpXjAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBCwUAA4IBAQAJIO8SiNr9jpLQ\n" +
               "eUsFUmbueoxyI5L+P5eV92ceVOJ2tAlBA13vzF1NWlpSlrMmQcVUE/K4D01qtr0k\n" +
               "gDs6LUHvj2XXLpyEogitbBgipkQpwCTJVfC9bWYBwEotC7Y8mVjjEV7uXAT71GKT\n" +
               "x8XlB9maf+BTZGgyoulA5pTYJ++7s/xX9gzSWCa+eXGcjguBtYYXaAjjAqFGRAvu\n" +
               "pz1yrDWcA6H94HeErJKUXBakS0Jm/V33JDuVXY+aZ8EQi2kV82aZbNdXll/R6iGw\n" +
               "2ur4rDErnHsiphBgZB71C5FD4cdfSONTsYxmPmyUb5T+KLUouxZ9B0Wh28ucc1Lp\n" +
               "rbO7BnjW\n" +
               "-----END CERTIFICATE-----\n"*/
            );
        });

        qz.security.setSignaturePromise(function () {
            return function (resolve) {
                //Preferred method - from server
//            $.ajax("/secure/url/for/sign-message?request=" + toSign).then(resolve, reject);

                //Alternate method - unsigned
                resolve();
            };
        });

        /// Connection ///
        function launchQZ() {
            if (!qz.websocket.isActive()) {
                window.location.assign("qz:launch");
                //Retry 5 times, pausing 1 second between each attempt
                startConnection({retries: 5, delay: 1});
            }
        }

        function startConnection(config) {
            if (!qz.websocket.isActive()) {
                updateState('Waiting', 'default');

                qz.websocket.connect(config).then(function () {
                    updateState('Active', 'success');
                    findVersion();
                }).catch(handleConnectionError);
            } else {
                displayMessage('QZ ile aktif bir bağlantı mevcut.', 'alert-warning');
            }
        }

        function endConnection() {
            if (qz.websocket.isActive()) {
                qz.websocket.disconnect().then(function () {
                    updateState('Inactive', 'default');
                }).catch(handleConnectionError);
            } else {
                displayMessage('QZ ile aktif bir bağlantı yok.', 'alert-warning');
            }
        }

        function listNetworkInfo() {
            qz.websocket.getNetworkInfo().then(function (data) {
                if (data.macAddress == null) {
                    data.macAddress = 'UNKNOWN';
                }
                if (data.ipAddress == null) {
                    data.ipAddress = "UNKNOWN";
                }

                var macFormatted = '';
                for (var i = 0; i < data.macAddress.length; i++) {
                    macFormatted += data.macAddress[i];
                    if (i % 2 === 1 && i < data.macAddress.length - 1) {
                        macFormatted += ":";
                    }
                }

                displayMessage("<strong>IP:</strong> " + data.ipAddress + "<br/><strong>Physical Address:</strong> " + macFormatted);
            }).catch(displayError);
        }

        /// Detection ///
        function findPrinter(query, set) {
            $("#printerSearch").val(query);
            qz.printers.find(query).then(function (data) {
                displayMessage("<strong>Found:</strong> " + data);
                if (set) {
                    setPrinter(data);
                }
            }).catch(displayError);
        }

        function findDefaultPrinter(set) {
            qz.printers.getDefault().then(function (data) {
                displayMessage("<strong>Found:</strong> " + data);
                if (set) {
                    setPrinter(data);
                }
            }).catch(displayError);
        }

        function findPrinters() {
            qz.printers.find().then(function (data) {
                var list = '';
                for (var i = 0; i < data.length; i++) {
                    list += "&nbsp; " + data[i] + "<br/>";
                }

                displayMessage("<strong>Available printers:</strong><br/>" + list);
            }).catch(displayError);
        }

        /// Raw Printers ///
        function printEPL() {
            var config = getUpdatedConfig();

            var printData = [
                '\nN\n',
                'q609\n',
                'Q203,26\n',
                'B5,26,0,1A,3,7,152,B,"1234"\n',
                'A310,26,0,3,1,1,N,"SKU 00000 MFG 0000"\n',
                'A310,56,0,3,1,1,N,"QZ PRINT APPLET"\n',
                'A310,86,0,3,1,1,N,"TEST PRINT SUCCESSFUL"\n',
                'A310,116,0,3,1,1,N,"FROM SAMPLE.HTML"\n',
                'A310,146,0,3,1,1,N,"QZ.IO"\n',
                {
                    type: 'raw',
                    format: 'image',
                    data: 'assets/img/image_sample_bw.png',
                    options: {language: 'EPL', x: 150, y: 300}
                },
                '\nP1,1\n'
            ];

            qz.print(config, printData).catch(displayError);
        }

        function printZPL(data) {
            var config = getUpdatedConfig();
            var printData = [
                '^XA ^FT215,150^BY2 ^A0N,40,40 ^BC,100,Y,N,N,A^FD' + data + ' ^FS ^XZ'
                //'^XA ^FT215,150^BY2 ^A0N,40,40 ^BC,100,Y,N,N,A^FDD008501M0621E1 ^FS ^XZ'
                //'^XA ^FT250,100^BY1 ^A0N,40,20 ^BC,80,Y,N,N,A^FDD0008501M06208081901000 ^FS ^XZ'

                //FT Başlangıç noktası
                //BY kod yükseklik
                //AON Text Boyutu
                //BC Code 128
            ];
            qz.print(config, printData).catch(displayError);
        }

        function printFile(file) {
            var config = getUpdatedConfig();

            var printData = [
                {type: 'raw', format: 'file', data: 'assets/' + file}
            ];

            qz.print(config, printData).catch(displayError);
        }

        function printPDF() {
            var config = getUpdatedConfig();

            var printData = [
                {type: 'pdf', data: 'assets/UrunKayidi-1003.pdf'}
            ];

            qz.print(config, printData).catch(displayError);
        }

        function printImage() {
            var config = getUpdatedConfig();

            var printData = [
                {type: 'image', data: 'assets/img/image_sample.png'}
            ];

            qz.print(config, printData).catch(displayError);
        }

        /// Resets ///
        function resetRawOptions() {
            $("#rawPerSpool").val(1);
            $("#rawEncoding").val(null);
            $("#rawEndOfDoc").val(null);
            $("#rawAltPrinting").prop('checked', false);
            $("#rawCopies").val(1);
        }

        function resetPixelOptions() {
            $("#pxlColorType").val("color");
            $("#pxlCopies").val(1);
            $("#pxlDensity").val('');
            $("#pxlDuplex").prop('checked', false);
            $("#pxlInterpolation").val("");
            $("#pxlJobName").val("");
            $("#pxlLegacy").prop('checked', false);
            $("#pxlOrientation").val("");
            $("#pxlPaperThickness").val(null);
            $("#pxlPrinterTray").val(null);
            $("#pxlRasterize").prop('checked', true);
            $("#pxlRotation").val(0);
            $("#pxlScale").prop('checked', true);
            $("#pxlUnitsIN").prop('checked', true);

            $("#pxlMargins").val(0).css('display', '');
            $("#pxlMarginsTop").val(0);
            $("#pxlMarginsRight").val(0);
            $("#pxlMarginsBottom").val(0);
            $("#pxlMarginsLeft").val(0);
            $("#pxlMarginsActive").prop('checked', false);
            $("#pxlMarginsGroup").css('display', 'none');

            $("#pxlSizeWidth").val('');
            $("#pxlSizeHeight").val('');
            $("#pxlSizeActive").prop('checked', false);
            $("#pxlSizeGroup").css('display', 'none');
        }

        function checkSizeActive() {
            if ($("#pxlSizeActive").prop('checked')) {
                $("#pxlSizeGroup").css('display', '');
            } else {
                $("#pxlSizeGroup").css('display', 'none');
            }
        }

        function checkMarginsActive() {
            if ($("#pxlMarginsActive").prop('checked')) {
                $("#pxlMarginsGroup").css('display', '');
                $("#pxlMargins").css('display', 'none');
            } else {
                $("#pxlMarginsGroup").css('display', 'none');
                $("#pxlMargins").css('display', '');
            }
        }

        /// Page load ///
        $(document).ready(function () {
            window.readingWeight = false;

            resetRawOptions();
            resetPixelOptions();
            startConnection();

            $("#printerSearch").on('keyup', function (e) {
                if (e.which === 13 || e.keyCode === 13) {
                    findPrinter($('#printerSearch').val(), true);
                    return false;
                }
            });

            $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
                if (window.readingWeight) {
                    $("#usbWeightRadio").click();
                    $("#hidWeightRadio").click();
                } else {
                    $("#usbRawRadio").click();
                    $("#hidRawRadio").click();
                }
            });

            $("[data-toggle='tooltip']").tooltip();
        });

        qz.websocket.setClosedCallbacks(function (evt) {
            updateState('Inactive', 'default');
            console.log(evt);

            if (evt.reason) {
                displayMessage("<strong>Connection closed:</strong> " + evt.reason, 'alert-warning');
            }
        });

        qz.websocket.setErrorCallbacks(handleConnectionError);

        var qzVersion = 0;

        function findVersion() {
            qz.api.getVersion().then(function (data) {
                $("#qz-version").html(data);
                qzVersion = data;
            }).catch(displayError);
        }

        /// Helpers ///
        function handleConnectionError(err) {
            updateState('Error', 'danger');

            if (err.target !== undefined) {
                if (err.target.readyState >= 2) { //if CLOSING or CLOSED
                    displayError("QZ Tray Bağlantısı Kapalı");
                } else {
                    displayError("QZ Tray Aktif Değil");
                    //console.error(err);
                }
            } else {
                displayError(err);
            }
        }

        function displayError(err) {

            toastr['error'](err, 'Barkod Hatası');
        }

        function displayMessage(msg, css) {
            if (css === undefined) {
                css = 'alert-info';
            }

            var timeout = setTimeout(function () {
                $('#' + timeout).alert('close');
            }, 5000);

            var alert = $("<div/>").addClass('alert alert-dismissible fade in ' + css)
                .css('max-height', '20em').css('overflow', 'auto')
                .attr('id', timeout).attr('role', 'alert');
            alert.html("<button type='button' class='close' data-dismiss='alert'>&times;</button>" + msg);

            $("#qz-alert").append(alert);
        }

        function pinMessage(msg, id, css) {
            if (css === undefined) {
                css = 'alert-info';
            }

            var alert = $("<div/>").addClass('alert alert-dismissible fade in ' + css)
                .css('max-height', '20em').css('overflow', 'auto').attr('role', 'alert')
                .html("<button type='button' class='close' data-dismiss='alert'>&times;</button>");

            var text = $("<div/>").html(msg);
            if (id !== undefined) {
                text.attr('id', id);
            }

            alert.append(text);

            $("#qz-pin").append(alert);
        }

        function updateState(text, css) {
            $("#qz-status").html(text);
            $("#qz-connection").removeClass().addClass('panel panel-' + css);

            if (text === "Inactive" || text === "Error") {
                $("#launch").show();
            } else {
                $("#launch").hide();
            }
        }

        function getPath() {
            var path = window.location.href;
            return path.substring(0, path.lastIndexOf("/"));
        }

        function formatHexInput(inputId) {
            var $input = $('#' + inputId);
            var val = $input.val();

            if (val.length > 0 && val.substring(0, 2) !== '0x') {
                val = '0x' + val;
            }

            $input.val(val.toLowerCase());
        }

        /** Attempts to parse scale reading from USB raw output */
        function readScaleData(data) {
            // Filter erroneous data
            if (data.length < 4 || data.slice(2, 8).join('') === "000000000000") {
                return null;
            }

            // Get status
            var status = parseInt(data[1], 16);
            switch (status) {
                case 1: // fault
                case 5: // underweight
                case 6: // overweight
                case 7: // calibrate
                case 8: // re-zero
                    status = 'Error';
                    break;
                case 3: // busy
                    status = 'Busy';
                    break;
                case 2: // stable at zero
                case 4: // stable non-zero
                default:
                    status = 'Stable';
            }

            // Get precision
            var precision = parseInt(data[3], 16);
            precision = precision ^ -256; //unsigned to signed

            // xor on 0 causes issues
            if (precision === -256) {
                precision = 0;
            }

            // Get units
            var units = parseInt(data[2], 16);
            switch (units) {
                case 2:
                    units = 'g';
                    break;
                case 3:
                    units = 'kg';
                    break;
                case 11:
                    units = 'oz';
                    break;
                case 12:
                default:
                    units = 'lbs';
            }

            // Get weight
            data.splice(0, 4);
            data.reverse();
            var weight = parseInt(data.join(''), 16);

            weight *= Math.pow(10, precision);
            weight = weight.toFixed(Math.abs(precision));

            return weight + units + ' - ' + status;
        }


        /// QZ Config ///
        var cfg = null;

        function getUpdatedConfig() {
            if (cfg == null) {
                cfg = qz.configs.create(null);
            }

            updateConfig();
            return cfg
        }

        function updateConfig() {
            var pxlSize = null;
            if ($("#pxlSizeActive").prop('checked')) {
                pxlSize = {
                    width: $("#pxlSizeWidth").val(),
                    height: $("#pxlSizeHeight").val()
                };
            }

            var pxlMargins = $("#pxlMargins").val();
            if ($("#pxlMarginsActive").prop('checked')) {
                pxlMargins = {
                    top: $("#pxlMarginsTop").val(),
                    right: $("#pxlMarginsRight").val(),
                    bottom: $("#pxlMarginsBottom").val(),
                    left: $("#pxlMarginsLeft").val()
                };
            }

            var jobName = null;
            if ($("#rawTab").hasClass("active")) {
                //copies = $("#rawCopies").val();
                jobName = $("#rawJobName").val();
            } else {
                //copies = $("#pxlCopies").val();
                jobName = $("#pxlJobName").val();
            }

            var copies = $("#barkodadet").val();
            if (copies === "" || parseInt(copies) <= 0) {
                copies = 1;
            }

            cfg.reconfigure({
                altPrinting: $("#rawAltPrinting").prop('checked'),
                encoding: $("#rawEncoding").val(),
                endOfDoc: $("#rawEndOfDoc").val(),
                perSpool: $("#rawPerSpool").val(),
                colorType: $("#pxlColorType").val(),
                copies: copies,
                density: $("#pxlDensity").val(),
                duplex: $("#pxlDuplex").prop('checked'),
                interpolation: $("#pxlInterpolation").val(),
                jobName: jobName,
                legacy: $("#pxlLegacy").prop('checked'),
                margins: pxlMargins,
                orientation: $("#pxlOrientation").val(),
                paperThickness: $("#pxlPaperThickness").val(),
                printerTray: $("#pxlPrinterTray").val(),
                rasterize: $("#pxlRasterize").prop('checked'),
                rotation: $("#pxlRotation").val(),
                scaleContent: $("#pxlScale").prop('checked'),
                size: pxlSize,
                units: $("input[name='pxlUnits']:checked").val()
            });
        }

        function setPrintFile() {
            setPrinter({file: $("#askFile").val()});
            $("#askFileModal").modal('hide');
        }

        function setPrintHost() {
            setPrinter({host: $("#askHost").val(), port: $("#askPort").val()});
            $("#askHostModal").modal('hide');
        }

        function setPrinter(printer) {
            var cf = getUpdatedConfig();
            cf.setPrinter(printer);

            if (printer && typeof printer === 'object' && printer.name === undefined) {
                var shown;
                if (printer.file !== undefined) {
                    shown = "<em>FILE:</em> " + printer.file;
                }
                if (printer.host !== undefined) {
                    shown = "<em>HOST:</em> " + printer.host + ":" + printer.port;
                }

                $("#configPrinter").html(shown);
            } else {
                if (printer && printer.name !== undefined) {
                    printer = printer.name;
                }

                if (printer === undefined) {
                    printer = 'NONE';
                }
                $("#configPrinter").html(printer);
            }
        }

    </script>
    @endif
    <script>
        jQuery.fn.DataTable.ext.type.search.string = function(data) {
            return !data ? '' : typeof data === 'string' ? data.replace(/Ç/g, 'c').replace(/İ/g, 'i').replace(/Ğ/g, 'g').replace(/Ö/g, 'o').replace(/Ş/g, 's').replace(/Ü/g, 'u').toLowerCase().replace(/ç/g, 'c').replace(/ı/g, 'i').replace(/ğ/g, 'g').replace(/ö/g, 'o').replace(/ş/g, 's').replace(/ü/g, 'u') : data;
        };
        var table = $('#sample_editable_1');
        var oTable = table.DataTable({
            "sPaginationType": "simple_numbers",
            "bProcessing": true,
            "ajax": {
                "url": "{{ URL::to('uretim/urunkayitlist') }}",
                "type": "POST",
                "data": {
                }
            },
            "bServerSide": true,
            "fnDrawCallback" : function() {
                $(document).on("click", ".delete", function () {
                    var id = $(this).data('id');
                    $(".modal-footer #sayacid").attr('href',"{{ URL::to('uretim/urunkayitsil') }}/"+id );
                });
            },
            "aaSorting": [[7,'desc']],
            "columnDefs": [ { targets: [ 1 ], orderData: [ 1, 0 ] },
                { targets: [ 2 ], orderData: [ 2, 0 ] },
                { targets: [ 3 ], orderData: [ 3, 0 ] },
                { targets: [ 4 ], orderData: [ 4, 0 ] },
                { targets: [ 5 ], orderData: [ 5, 0 ] },
                { targets: [ 6 ], orderData: [ 6, 0 ] }
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
                {data: 'id', name: 'uretimurun.id',"class":"id","orderable": true, "searchable": true},
                {data: 'urunadi', name: 'uretimurun.urunadi',"orderable": true, "searchable": false},
                {data: 'kodu', name: 'netsisstokkod.kodu',"orderable": true, "searchable": false},
                {data: 'ureticiadi', name: 'uretimuretici.ureticiadi',"orderable": true, "searchable": false},
                {data: 'markaadi', name: 'uretimurunmarka.markaadi',"orderable": true, "searchable": false},
                {data: 'adet', name: 'uretimurun.adet',"orderable": true, "searchable": true},
                {data: 'depotarihi', name: 'uretimurun.depotarihi',"orderable": true, "searchable": false},
                {data: 'eklenmetarihi', name: 'uretimurun.eklenmetarihi',"orderable": true, "searchable": false},
                {data: 'gdepotarihi', name: 'uretimurun.gdepotarihi',"visible": false, "searchable": true},
                {data: 'geklenmetarihi', name: 'uretimurun.geklenmetarihi',"visible": false, "searchable": true},
                {data: 'nurunadi', name: 'uretimurun.nurunadi',"visible": false, "searchable": true},
                {data: 'nkodu', name: 'netsisstokkod.nkodu',"visible": false, "searchable": true},
                {data: 'nureticiadi', name: 'uretimuretici.nureticiadi',"visible": false, "searchable": true},
                {data: 'nmarkaadi', name: 'uretimurunmarka.nmarkaadi',"visible": false, "searchable": true},
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
            '<option value="10">Ürün Adı</option>'+
            '<option value="11">Stok Kodu</option>'+
            '<option value="12">Üretici</option>'+
            '<option value="13">Marka</option>'+
            '<option value="5">Adet</option>'+
            '<option value="8">Depo Tarihi</option>'+
            '<option value="9">Kayıt Tarihi</option>'+
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
        table.on('click', 'tr', function () {
            if(oTable.cell( $(this).children('.id')).data()!==undefined){
                $(this).toggleClass("active");
                if($(this).hasClass('active'))
                {
                    $("tbody tr").removeClass("active");
                    $(this).addClass("active");
                }else{
                    $(this).removeClass("active");
                }
            }
        });
        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    </script>
    <script>
        $(document).ready(function() {
            $('#kriter').select2();
            $('.barkodbas').click(function () {
                @if(!$mobile)
                var id = $('#sample_editable_1 .active .id').text();
                if (id === "") {
                    toastr['warning']('Ürün Seçilmedi', 'Ürün Seçme Hatası');
                } else{
                    $.blockUI();
                    $.getJSON(" {{ URL::to('uretim/urunbilgi') }}", {id: id}, function (event) {
                        if (event.durum) {
                            var uretimurun = event.uretimurun;
                            $('.urunadi').text(uretimurun.netsisstokkod.kodu+' - '+uretimurun.urunadi);
                            $('.uruncari').text(uretimurun.netsiscari ? uretimurun.netsiscari.carikod+' - '+uretimurun.netsiscari.cariadi : '');
                            $('.urunuretici').text(uretimurun.uretici ? uretimurun.uretici.ureticiadi : '');
                            $('.urunmarka').text(uretimurun.marka ? uretimurun.marka.markaadi : '');
                            $('#barkod').val(uretimurun.barkod);
                            $('#barkodadet').val(1);
                            $('#barkodbas').modal('show');
                        }else{
                            toastr[event.type](event.text, event.title);
                        }
                        $.unblockUI();
                    });
                }
                @else
                    toastr['warning']('Barkod Basma İşlemi Mobil üzerinde yapılamaz', 'Barkod Basma Aktif Değil');
                @endif
            });

            $('.barkodcikti').click(function () {
                @if(!$mobile)
                var adet = $('#barkodadet').val();
                var barkod = $('#barkod').val();
                if (adet !== "" && parseInt(adet)>0) {
                    setPrinter('zebra');
                    printZPL(barkod);
                }
                @endif
                $('#barkodbas').modal('hide');
            });
            @if(!$mobile)
                @if(Session::has('barkodvar'))
                    @if(Session::get('barkodvar'))
                        setTimeout(function() {
                            setPrinter('zebra');
                            @foreach(Session::get('barkodlar') as $barkod)
                            printZPL('{{$barkod}}');
                            @endforeach
                         }, 2000);
                    @endif
                @endif
            @endif
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
                        <i class="fa fa-tag"></i>Sisteme Girilen Ürün Kayıtları
                    </div>
                    <div class="actions">
                        <a class="btn btn-default btn-sm barkodbas">
                            <i class="fa fa-print"></i> Barkod Çıktısı Al</a>
                        <a href="{{ URL::to('uretim/urunkayitekle') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-pencil"></i> Yeni Ürün Kayıdı Ekle </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ürün Adı</th>
                                <th>Stok Kodu</th>
                                <th>Üretici</th>
                                <th>Marka</th>
                                <th>Adet</th>
                                <th>Depo Tarihi</th>
                                <th>Kayıt Tarihi</th>
                                <th></th><th></th><th></th><th></th><th></th><th></th>
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
    <div class="modal fade" id="portlet-delete" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Ürün Kayıdı Silinecek</h4>
                </div>
                <div class="modal-body">
                    Seçilen ürün Kayıdını Silmek İstediğinizden Emin Misiniz?
                </div>
                <div class="modal-footer">
                    <a id="sayacid" href="" type="button" class="btn blue">Sil</a>
                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="barkodbas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Barkod Çıkart</h4>
                </div>
                <div class="modal-body">
                    <form id="form_sample_abone" class="form-horizontal" novalidate="novalidate">
                        <div class="form-body">
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button>
                                Girilen Bilgilerde Hata Var.
                            </div>
                            <div class="alert alert-success display-hide">
                                <button class="close" data-close="alert"></button>
                                Bilgiler Doğru!
                            </div>

                            <div class="form-group col-xs-12">
                                <div class="form-group col-xs-12">
                                    <label class="control-label col-sm-2 col-xs-4">Ürün Adı:</label>
                                    <label class="col-xs-8 urunadi" style="padding-top: 7px"></label>
                                </div>
                                <div class="form-group col-xs-12">
                                    <label class="control-label col-sm-2 col-xs-4">Cari Bilgisi :</label>
                                    <label class="col-xs-8 uruncari" style="padding-top: 7px"></label>
                                </div>
                            </div>
                            <div class="form-group col-xs-12">
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Üretici :</label>
                                    <label class="col-xs-8 urunuretici" style="padding-top: 7px"></label>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Marka :</label>
                                    <label class="col-xs-8 urunmarka" style="padding-top: 7px"></label>
                                </div>
                            </div>
                            <div class="form-group col-xs-12">
                                <div class="form-group col-sm-6 col-xs-12">
                                    <label class="control-label col-xs-4">Barkod Sayısı :</label>
                                    <div class="col-xs-8">
                                        <input type="text" id="barkod" name="barkod" value="" data-required="1" class="form-control hide">
                                        <input type="text" id="barkodadet" name="barkodadet" value="1" maxlength="3" data-required="1" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-xs-12" style="text-align: center">
                                    <button type="button" class="btn green barkodcikti">Çıkart</button>
                                    <button type="button" class="btn default" data-dismiss="modal">Vazgeç</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
