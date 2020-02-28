<!DOCTYPE html>
<html lang="tr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="">
    <meta name="description" content="Manas Servis Sayaç Takip Sistemi">
<!-- Favicon -->
    <link rel="shortcut icon" href="{{ URL::to('favicon.ico') }}" />
<!-- Sitil -->
    <link href="{{ URL::to('assets/loginpage/styles/main.css') }}" rel="stylesheet" />	
<!-- Eklentiler -->
    <link href="{{ URL::to('assets/loginpage/plugins/flexslider/flexslider.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('assets/loginpage/plugins/flexslider/skin.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('assets/loginpage/plugins/colorbox/colorbox.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('assets/loginpage/plugins/colorbox/skin.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('assets/loginpage/plugins/mediaelement/mediaelementplayer.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('assets/loginpage/plugins/mediaelement/skin.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('assets/global/plugins/bootstrap-toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Explorer Uyumluluk -->
        <!--[if lt IE 9]>
                <script src="{{ URL::to('assets/loginpage/scripts/ie/html5.js') }}"></script>
                <link href="{{ URL::to('assets/loginpage/styles/ie/ie.css') }}" rel="stylesheet" />
        <![endif]-->
<!-- Başlık -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-147452027-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-147452027-1');
    </script>
    <title>Manas Online Sayaç Servis Takip Sistemi</title>
</head>
<body>

<!-- MAIN WRAP -->
    <div id="main-wrap"><!-- MAIN WRAP -->
        <div id="page" class="fullscreen no-scroll">
            <div id="home-slider">
                <ul class="slides">
                    <li>
                        <img src="{{ URL::to('assets/loginpage/assets/resized/8.jpg') }}" data-large="{{ URL::to('assets/loginpage/assets/full/8.jpg') }}" data-thumb="{{ URL::to('assets/loginpage/assets/thumb/8.jpg') }}" alt="Image" />
                        <div class="fullscreen-caption">
                            <h1>Kaynaklar Azalıyor...</h1>
                            <h4>
                                <span class="slide">Manas EKS-3.20 Sayaçlar İle Verİmlİ Kaynak Kullanımı.</span>
                                <span class="slide">Damlayı Dahİ Sayar Sİzİn İçİn Tasarruf Yapar.</span>
                            </h4>
                            <h4>EKS Sistemin Avantajları</h4>
							<p>EKS sistem sağladığı pek çok avantajın yanı sıra su idarelerine yüksek bir maliyet getirmeksizin gelirlerini artırma imkanı
							sunmaktadır. İdarelerin gelir kayıpları genellikle yüksek oranda su kullanma eğiliminde olan ve fatura tahsilatında güçlük
							çeken resmi ve özel abonelerden kaynaklanır. EKS sistem ile birlikte faturaların ödenmemesi problemi tamamen ortadan
							kalkmaktadır. </p>
                            <button class="color ajax" data-href="">
                                <i class="small briefcase icon l"></i>
                                <span class="inline-block">Detay</span>
                            </button>
                        </div>
                    </li>
                    <li>
                        <img src="{{ URL::to('assets/loginpage/assets/resized/9.jpg') }}" data-large="{{ URL::to('assets/loginpage/assets/full/9.jpg') }}" data-thumb="{{ URL::to('assets/loginpage/assets/thumb/9.jpg') }}" alt="Image" />
                        <div class="fullscreen-caption">
                            <h2>Doğalgaz ve Su Sayacında Devrim...</h2>
                            <h4>
                                <span class="slide">Artık Kredİ Alabİlmek İçİn Saatlerce Uğraşmayın.</span>
                                <span class="slide"> Gprs Entegrelİ Su ve Gaz Sayacı </span>
								<h3> Kredi Kartı İle Kredi Satın Alma Devri Başlıyor.</h3>
								<p>Kurulan yüksek güvenlikli web sitesinden satın alacağınız kredi sayacınıza otomatik olarak sistem tarafından yüklecektir. 
								Arıza durumlarında dahi uzakdan müdahele edilebilir ve problem merkez tarafından saniyeler içerisinde giderilebilir. </p>
                            </h4>
                            <button class="color" onclick="">
                                <i class="small check icon l"></i>
                                <span class="inline-block">Detay</span>
                            </button>
                        </div>
                    </li>
                    <li>
                        <img src="{{ URL::to('assets/loginpage/assets/resized/10.jpg') }}" data-large="{{ URL::to('assets/loginpage/assets/full/10.jpg') }}" data-thumb="{{ URL::to('assets/loginpage/assets/thumb/10.jpg') }}" alt="Image" />
                        <div class="fullscreen-caption">
                            <h2>Su sağlıktır ve ekonomİk bİr değerdİr.</h2>
                            <h4>
                                <span class="slide">Suyumuzu korumanın Tam Zamanı...</span>
                                <span class="slide"></span>
                            </h4>
                        </div>
                    </li>
                </ul>
            </div>
        </div><!-- #page -->
        <div id="background">
        </div><!-- #background -->
        <div class="container" id="dock">
            <header id="header">
                <div id="site-logo">
            <!-- LOGİN PENCERESİ -->
                <div class="carousel-caption width-form">
                    <div id="infoMessage"></div>
                    <form action="{{ URL::to('login') }}" class="form-signin" method="post" accept-charset="utf-8">
                        <div class="form-body">
                            <div class="form-group">
                                <h5 class="form-hed">MANAS SERVİS SİSTEMİNE GİRİŞ <span class="block"></span></h5>
                            </div>
                            <div class="form-group">
                                <span>
                                    <input type="text" name="girisadi" id="girisadi" class="form-control" placeholder="Kullanıcı Adı" required="required"  />
                                    <input type="password" name="sifre" id="sifre" class="form-control" placeholder="Şifre" required="required"  />
                                </span>
                                <span>
                                    <label class="checkbox" style="color:white"><input type="checkbox" name="remember" id="remember" class="uniform" />Hatırla</label>
                                    <button type="submit" class="btn butt">GİRİŞ</button>
                                    <a href="{{ URL::to('reminder/remind') }}" class="btn butt2">ŞİFREMİ UNUTTUM!</a>
                                </span>
                            </div>
                        <div class="form-group">{{ Form::token() }}</div>
                        </div>
                    </form>
                </div>

            <!-- LOGİN PENCERESİ OFF -->
            </div><!-- #site-logo -->
            <nav id="main-nav">
            <ul>
                <li class="current">
                    <a href="{{ URL::to('login') }}">Ana Sayfa</a>
                </li>
                <li>
                    <a href="#">Onlİne Sayaç Takİbİ</a>
                </li>
                <li>
                    <a href="#">İletİşİm</a>
                </li>
            </ul>
            </nav> <!-- #main-nav -->
            </header><!-- #header -->
            <a href="#" id="toggle-dock"><span><span>
            <span class="active-text">Gizle</span>
            <span class="inactive-text">Göster</span>
            </span></span></a>
            <a href="#" id="page-top-link"><span><span>En yukarı</span></span></a>
        </div><!-- #dock -->
        <div id="preloader">
                <span><span>Yükleniyor</span></span>
        </div><!-- #preloader -->
    </div><!-- #main-wrap -->
<!-- SCRIPTS -->
        <script src="{{ URL::to('assets/loginpage/scripts/jquery.1.7.2.js') }}"></script>
        <script src="{{ URL::to('assets/loginpage/scripts/jquery.easing.js') }}"></script>
        <script src="{{ URL::to('assets/loginpage/scripts/jquery.cookie.js') }}"></script>
        <script src="{{ URL::to('assets/loginpage/scripts/main.js') }}"></script> 
<!-- Plugins -->
        <script src="{{ URL::to('assets/loginpage/plugins/modernizr/modernizr.min.js') }}"></script>
        <script src="{{ URL::to('assets/loginpage/plugins/flexslider/jquery.flexslider.min.js') }}"></script>
        <script src="{{ URL::to('assets/loginpage/plugins/colorbox/jquery.colorbox.min.js') }}"></script>
        <script src="{{ URL::to('assets/loginpage/plugins/mediaelement/mediaelement-and-player.min.js') }}"></script>
        <script src="{{ URL::to('assets/loginpage/plugins/social/jquery.social.js') }}"></script> 
        <script src="{{ URL::to('assets/global/plugins/bootstrap-toastr/toastr.min.js') }}"></script>
        @if(Session::has('mesaj'))
        <script>
            toastr.options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-top-right",
                onclick: null,
                showDuration: "1000",
                hideDuration: "1000",
                timeOut: "5000",
                extendedTimeOut: "1000",
                showEasing: "swing",
                hideEasing: "linear",
                showMethod: "fadeIn",
                hideMethod: "fadeOut"
            };
            toastr['{{Session::get("type")}}']('{{Session::get("text")}}', '{{Session::get("title")}}');
        </script>
        @endif
<!-- IE -->
        <!--[if lt IE 9]>
        <script src="{{ URL::to('assets/loginpage/scripts/ie/ie.js') }}" type="text/javascript"></script>
        <![endif]-->


</body> 
</html>