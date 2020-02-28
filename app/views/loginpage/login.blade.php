<!DOCTYPE html>
<html lang="tr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="">
    <meta name="description" content="Manas Servis Sayaç Takip Sistemi">
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ URL::to('favicon.ico') }}" />
    <title>Manas Online Sayaç Servis Takip Sistemi</title>
    <style type="text/css">
        @font-face {
            font-family: Quicksand-Bold;
            src: url('{{ URL::to('assets/loginpage/fonts/Quicksand-Bold.ttf') }}');
        }
    </style><!--Menu için -->
    <link href="{{ URL::to('assets/loginpage/css/loginstyle.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('assets/loginpage/css/search.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('assets/loginpage/fonts/material-icon/css/material-design-iconic-font.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('assets/loginpage/css/jquery.mb.YTPlayer.min.css') }}" rel="stylesheet" /><!--video için -->
    <link href="{{ URL::to('assets/global/plugins/bootstrap-toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />

    <script src="{{ URL::to('assets/loginpage/js/jquery.min.js') }}"></script>
<!--[if IE]>
    <link href="{{ URL::to('assets/loginpage/css/all-ie-only.css') }}" rel="stylesheet" />
	<![endif]-->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-147452027-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-147452027-1');
    </script>
<script>

    var myPlayer;
    jQuery(function () {
      myPlayer = jQuery("#bgndVideo").YTPlayer({useOnMobile:true, mobileFallbackImage:"{{ URL::to('assets/loginpage/images/bg2.jpg') }}"});
      var YTPConsole = jQuery("#eventListener");
      myPlayer.on("YTPReady YTPStart YTPEnd YTPPlay YTPLoop YTPPause YTPBuffering YTPMuted YTPUnmuted YTPChangeVideo", function (e) {
        YTPConsole.append("event: " + e.type + " (" + jQuery("#bgndVideo").YTPGetPlayer().getPlayerState() + ") > time: " + e.time);
        YTPConsole.append("<br>");
      });
      myPlayer.on("YTPChanged", function () {
        YTPConsole.html("");
      });
      myPlayer.on("YTPChangeVideo", function(e){
        console.debug("YTPChangeVideo", e);
      });

      myPlayer.on("YTPData", function (e) {
        $(".dida").html(e.prop.title + "<br>@" + e.prop.channelTitle);
        $("#videoData").show();

        YTPConsole.append("******************************");
        YTPConsole.append("<br>");
        YTPConsole.append(e.type);
        YTPConsole.append("<br>");
        YTPConsole.append(e.prop.title);
        YTPConsole.append("<br>");
        YTPConsole.append(e.prop.description.replace(/\n/g, "<br/>"));
        YTPConsole.append("<br>");
        YTPConsole.append("******************************");
        YTPConsole.append("<br>");
      });

      myPlayer.on("YTPTime", function (e) {
        var currentTime = e.time;
        var traceLog = currentTime / 5 === Math.floor(currentTime / 5);

        if (traceLog && YTPConsole.is(":hidden")) {
          YTPConsole.append(myPlayer.attr("id")+ " > " + e.type + " > actual time is: " + currentTime);
          YTPConsole.append("<br>");

          if(myPlayer.YTPGetFilters())
            console.debug("filters: ", myPlayer.YTPGetFilters());
        }
      });

      $(".slider").each(function(){
        var $slider = $(this);
        $slider.simpleSlider({
          initialval: 0, //function (el) {return Math.random() * el.opt.scale},
          scale     : 100,
          callback  : function (el) {
            var filter = $(el).data("filter");
            myPlayer.YTPApplyFilter(filter, +(el.value).toFixed(0));
            $("span",el).html(filter + "       (" + (+(el.value).toFixed(0)) + ")");
            var desc = "$(selector).YTPApplyFilters({})";
            $("#filterScript").html(desc);
          }
        });
      });

      myPlayer.on("YTPPlay", function(){
        $("#togglePlay").removeClass("play pause");
        $("#togglePlay").addClass("pause");
      });

      myPlayer.on("YTPPause", function(){
        $("#togglePlay").removeClass("play pause");
        $("#togglePlay").addClass("play");
      });
    });

    function checkForVal(val){
      return val || 0;
    }
    function gosearch() {
        $('#serialno').focus();
        $('#captcha').focus();
    }
    function goiletisim() {
        $('#iletisim').modal('show');
    }
</script>
</head>
<body>
<div id="wrapper" style="position:relative; padding:10px 0;z-index:10">
<div class='nav'>
  <ul>
    <li>
      <a class='logo' href='{{URL::to('login')}}'>
        <img alt="" src='{{ URL::to('assets/loginpage/images/Manaslogo.png') }}'>
      </a>
    </li>
    <li>
      <a href='#' onclick='return gosearch()'>Ürün Takibi</a>
    </li>
    <li>
      <a href='#login'>Sisteme Giriş</a>
    </li>
	<li>
      <a href='http://www.manas.com.tr/tarihce.html'>
        Hakkında
      </a>
    </li>
    <li>
        <a href='http://www.manas.com.tr/iletisim/'>İletişim</a>
    </li>
    <li>
      <a href='#'>
        <div class='fa fa-phone fa-lg'></div>
		444 7 667
      </a>
    </li>
  </ul>
</div>

<div id="bgndVideo" class="player" data-property="{videoURL:'mqA_2cPV2Sg',  showControls:true, autoPlay:true, loop:true, stopMovieOnBlur: false, vol:0, mute:true, startAt:0, opacity:1, addRaster:true, quality:'highres', optimizeDisplay:true, playOnlyIfVisible: true, useOnMobile: false, mobileFallbackImage: '{{ URL::to('assets/loginpage/images/bg2.jpg') }}'}"></div>

</div>

<div id="login" class="overlay">
	<div class="popup">
		<div class="content">
			<form class='login-form' action="{{ URL::to('login') }}" method="POST" accept-charset="utf-8">
                <div class="flex-row">
                    <label class="lf--label" for="username">
                        <div class='fa fa-user fa-lg'></div>
	                </label>
                    <input id="girisadi" name="girisadi" class='lf--input2' placeholder='Kullanıcı Adı' type='text'>
	                <label class="lf--label" for="password">
                        <div class='fa fa-lock fa-lg'></div>
                    </label>
                    <input id="sifre" name="sifre" class='lf--input2' placeholder='Şifre' type='password'>
	                <input class='lf--submit' type='submit' value='Giriş'>
                </div>
                <a class='lf--forgot' href="{{ URL::to('reminder/remind') }}">ŞİFREMİ UNUTTUM!</a>
                <a class="close" href="#"><i class="fa fa-close"></i></a>
                <div class="form-group">{{ Form::token() }}</div>
            </form>
		</div>
	</div>
</div>
<div class="loginorta">
    <div class="container">
      <form method="POST" id="search-form" class="signup-form" action="{{ URL::to('uruntakip') }}">
          <div>
              <h3></h3>
              <fieldset>
                  <input class="arama {{Input::old('takipno') ? 'valid' : ''}}" type="text" name="takipno" id="takipno" maxlength="20" autocomplete="off" value="{{Input::old('takipno')}}"/>
                  <label for="takipno" class="form-label arama">Takip numaranızı giriniz</label>
              </fieldset>
              <h3></h3>
              <fieldset>
                  <input class="arama {{Input::old('serino') ? 'valid' : ''}}" type="text" name="serino" id="serino" maxlength="20" autocomplete="off" value="{{Input::old('serino')}}"/>
                  <label for="serino" class="form-label arama">Sayaç numaranızı giriniz</label>
              </fieldset>
              <h3></h3>
              <fieldset>
                  <input class="arama" type="text" name="captcha" id="captcha" maxlength="6" autocomplete="off" />
                  <label for="captcha" class="form-label arama">Aşağıdaki Kodu Giriniz</label>
                  {{ HTML::image(Captcha::img(),'Güvenlik Resmi',array( 'style' => 'margin-top:5px' )) }}
              </fieldset>
            <p></p>
          </div>
      </form>
    </div>
</div>

<script src="{{ URL::to('assets/loginpage/js/menuscript.js') }}"></script>
<script src="{{ URL::to('assets/loginpage/js/jquery.mb.YTPlayer.js') }}"></script>

<!-- <script src="vendor/jquery/jquery.min.js"></script> -->
<script src="{{ URL::to('assets/loginpage/vendor/jquery-validation/dist/jquery.validate.min.js') }}"></script>
<script src="{{ URL::to('assets/loginpage/vendor/jquery-validation/dist/additional-methods.min.js') }}"></script>
<script src="{{ URL::to('assets/loginpage/vendor/jquery-steps/jquery.steps.min.js') }}"></script>
<script src="{{ URL::to('assets/loginpage/js/main.js') }}"></script>
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
</body>
</html>
