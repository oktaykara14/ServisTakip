<html lang="tr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        body { font-family: DejaVu Sans,sans-serif;
        font-size: 12px
        }
        table {
            border-collapse: collapse;
        }
        td {
            padding-bottom: 10px;
        }
    </style>
    <title></title>
</head>
<body>
<table style="width: 100%">
    <tr><td colspan="10" style="">Sayın {{$user->adi_soyadi}},</td></tr>
    <tr><td colspan="10" style="">Bu mesaj parola sıfırlama talebiniz üzerine gönderilmiştir.İşlemi tamamlamak için lütfen aşağıdaki bağlantıya tıklayınız.</td></tr>
    <tr><td colspan="10" style="">Bağlantıya tıkladığınızda yeni parolanızı belirleyeceğiniz sayfaya yönlendirileceksiniz.</td></tr>
    <tr><td colspan="10" style=""><a href="http://servis.manas.com.tr/reminder/reset/{{$token}}">Şimdi Sıfırlayın ></a></td></tr>
    <tr><td colspan="10" style="">Saygılarımızla,</td></tr>
    <tr><td colspan="10" style="">Servis Takip Destek</td></tr>
</table>
</body>
</html>