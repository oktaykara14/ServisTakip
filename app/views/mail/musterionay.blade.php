<html lang="tr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        body { font-family: DejaVu Sans,sans-serif;
        font-size: 12px}
        table {
            border-collapse: collapse;
        }
    </style>
    <title></title>
</head>
<body>
<table style="width: 100%">
    <tr>
        <td colspan="10">
            <p>     {{$netsiscari->cariadi}} carisine ait {{$ucretlendirilen->id}} numaralı ücretlendirme müşteri tarafından
                {{$ucretlendirilen->durum==2 ? 'onaylanmıştır' : 'reddedilmiştir'}}.
            </p>
        </td>
    </tr>
    <tr>
        <td colspan="10">
            <p>Ücretlendirmeye ait bilgiler için
                <a style="font-size: 20px" target="_blank" href="https://servis.manas.com.tr/ucretlendirme/ucretlendirilenler/{{$hatirlatma->id}}">tıklayınız</a>
            </p>
        </td>
    </tr>
    <tr><td colspan="10" style="height:20px"></td></tr>
</table>
</body>
</html>