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
    <tr><td colspan="10">{{$mailicerik}}</td></tr>
    <tr><td colspan="10"><a style="font-size: 20px" id="onaylink" target="_blank" href="{{$onaylink}}">Onaylama sayfası için buraya tıklayınız.</a></td></tr>
    <tr><td colspan="10" style="height:20px"></td></tr>
    <tr><td colspan="10">Giriş Sayfası: <a style="font-size: 20px" id="girissayfa" target="_blank" href="https://servis.manas.com.tr">servis.manas.com.tr</a></td></tr>
    <tr><td colspan="10">Kullanıcı Bilgileri:</td></tr>
    @if(!$ilkmail)
        <tr><td colspan="10" style="font-size: 20px;color:black">Adı: {{$girisadi}}</td></tr>
        <tr><td colspan="10" style="padding-bottom: 5px;font-size: 20px;color:black">Şifresi: manas123</td></tr>
        <tr><td colspan="10" style="padding-bottom: 5px;font-size: 20px;color:black">Ürün Takip Numarası: {{$takipno}}</td></tr>
        <tr><td colspan="10" style="color: #D94A38;font-size: 18px">NOT: Şifrenizi ilk giriş sonrası kullanıcı profili sayfasından değiştiriniz! İlk mesaj sonrası şifre bir daha gönderilmeyecektir.</td></tr>
    @else
        <tr><td colspan="10" style="font-size: 20px;color:black">Adı: {{$girisadi}}</td></tr>
        <tr><td colspan="10" style="padding-bottom: 5px;font-size: 20px;color:black">Ürün Takip Numarası: {{$takipno}}</td></tr>
        <tr><td colspan="10" style="color: #D94A38;font-size: 18px">NOT: Şifrenizi unuttuysanız Giriş Sayfasındaki Şifremi Unuttum linki ile şifrenizi sıfırlayabilirsiniz!</td></tr>
    @endif
    <tr><td colspan="10" style="color: #D94A38;font-size: 18px">NOT: Bu ileti sistem tarafından gönderilen bir otomatik mesajdır.Onay için link üzerinden işlem yapınız!</td></tr>
    <tr><td colspan="10" style="height:10px"></td></tr>
    <tr><td colspan="10" style="color: #D94A38;font-size: 18px">NOT: Ödeme dekontlarınızı info@manas.com.tr mail adresine gönderebilirsiniz. İletiniz bu mail üzerinden yetkili kişilere yönlendirilecektir.</td></tr>
    <tr><td colspan="10" style="height:10px"></td></tr>
    <tr><td colspan="10" style="color: #D94A38;font-size: 18px">NOT: Outlook gibi programlar, mailin içeriğinde link ve ek olduğu için ekteki dosyaları açmanızı engelleyebilir. İçerikteki link ve dosyalara erişebilmek için servistakip@manas.com.tr mail adresini güvenli gönderenler listesine eklemeniz gerekiyor.</td></tr>

</table>
</body>
</html>