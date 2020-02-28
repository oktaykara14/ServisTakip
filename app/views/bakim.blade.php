
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Language" content="de">
    <meta charset="utf-8">
    <title>Bakım Yapılıyor...</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Coming soon page responsive">
    <meta name="author" content="PixelGreco">

    <!-- Styles -->
    <link href="{{ URL::to('assets/bakim/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ URL::to('assets/bakim/css/bootstrap-responsive.min.css') }}" rel="stylesheet">
      <link rel="shortcut icon" href="{{ URL::to('favicon.ico') }}"/>
    <link href="{{ URL::to('assets/bakim/css/nanoscroller.css') }}" rel="stylesheet">
    <link href="{{ URL::to('assets/bakim/css/colorbox.css') }}" rel="stylesheet">
	
	<!--<link href='../../fonts.googleapis.com/css@family=Play_3A700' rel='stylesheet' type='text/css'>-->
	<link href='{{ URL::to('assets/bakim/css/font-awesome.min.css') }}' rel='stylesheet'>
	
	<link href="{{ URL::to('assets/bakim/css/style-blue.css') }}" rel="stylesheet">

    <!-- CSS3 for IE8-9 support -->
    <!--[if lte IE 8]><link rel="stylesheet" href="{{ URL::to('assets/bakim/css/ie8.css') }}" /><![endif]-->
	<!--[if IE 9]><link rel="stylesheet" href="{{ URL::to('assets/bakim/css/ie9.css') }}" /><![endif]-->
	
	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]><script src="{{ URL::to('assets/bakim/js/html5.js') }}"></script><![endif]-->
  </head>

  <body>
	<!-- Left Shadow Background starts -->
    <div class="bg-container-left">
	<!-- Right Shadow Background starts -->
    <div class="bg-container-right">
	<!-- Wrapper container starts -->
    <div class="container-narrow">
	  
	  <!-- Header starts -->
      <div class="masthead row-fluid">
	  
	    <!-- Logo starts -->
		<div class="span4 pull-left">
			<h1 class="logo"><a href="http://www.manas.com.tr"><img src="{{ URL::to('assets/bakim/img/logo.png')}}" alt=""/></a></h1>
			<!-- OR text -->
			<!-- <h1 class="logo"><a href="#">netrino*</a></h1> -->
		</div>
		<!-- Logo ends -->
		
		<!-- Social Icons starts -->
		<div class="span7 pull-right">
			<ul class="social">
				
				<!-- Enabled icons starts -->
				<li><a href="#" class="facebook"></a></li>
				<li><a href="#" class="twitter"></a></li>
				<li><a href="#" class="tumblr"></a></li>
				<li><a href="#" class="dribbble"></a></li>
				<li><a href="#" class="behance"></a></li>
				<li><a href="#" class="linkedin"></a></li>
				<li><a href="#" class="rss"></a></li>
				<li><a href="#" class="delicious"></a></li>
				<li><a href="#" class="deviantart"></a></li>
				<li><a href="#" class="digg"></a></li>
				<!-- Enabled icons ends -->
				
				<!-- Disabled icons starts --><!--
				<li><a href="#" class="flickr"></a></li>
				<li><a href="#" class="lastfm"></a></li>
				<li><a href="#" class="myspace"></a></li>
				<li><a href="#" class="pinterest"></a></li>
				<li><a href="#" class="sharethis"></a></li>
				<li><a href="#" class="skype"></a></li>
				<li><a href="#" class="youtube"></a></li>
				<li><a href="#" class="vimeo"></a></li>
				<li><a href="#" class="wordpress"></a></li>
				<li><a href="#" class="blogger"></a></li>
				<li><a href="#" class="email"></a></li>
				<li><a href="#" class="github"></a></li>
				<li><a href="#" class="stumbleupon"></a></li>
				<li><a href="#" class="instagram"></a></li>
				<li><a href="#" class="spotify"></a></li>
				<li><a href="#" class="reddit"></a></li>
				<li><a href="#" class="picasa"></a></li>
				<li><a href="#" class="yahoo"></a></li>
				<li><a href="#" class="googleplus"></a></li>
				<li><a href="#" class="paypal"></a></li>
				--><!--Disabled icons ends -->
	        </ul>
		</div>
		<!-- Social Icons ends -->
      </div>
	  <!-- Header ends -->
	  
	  <!-- Main content starts -->
      <div class="main">
	    
		<!-- Countdown starts -->
		<div class="countdown-split">
	        <div class="btn btn-large btn-custom">
				<span class="cname">GUN</span>
				<span id="cdays" class="cnumber">00</span>
			</div>
			<div class="btn btn-large btn-custom">
				<span class="cname">SAAT</span>
				<span id="chours" class="cnumber">00</span>
			</div>
		</div>
		<div class="countdown-split">
			<div class="btn btn-large btn-custom">
				<span class="cname">DAKİKA</span>
				<span id="cminutes" class="cnumber">00</span>
			</div>
			<div class="btn btn-large btn-custom">
				<span class="cname">SANİYE</span>
				<span id="cseconds" class="cnumber">00</span>
			</div>
		</div>
		<!-- Countdown ends -->
		
		<!-- Heading starts -->
		<h1>Bakım Yapılıyor!</h1>
		<p class="lead"><strong>Bize E-mail'inizi Birakin!</strong> Bakım Bittiginde size Haber Verelim...</p>
		<!-- Heading ends -->
		
		<!-- Subscription Form starts -->
		<form class="form-inline subscribe-frm" id="subscribe-frm" method="post">
			<div class="input-prepend subscribe-input">
				<span class="add-on">@</span>
				<input class="input-xxlarge" id="email" name="email" type="text" placeholder="E-Mail Adresiniz!">
			</div>
			<button class="btn btn-inverse input-small subscribe-btn" type="submit">Gonder</button>
		</form>
		<div id="success"><h2>Teşekkürler!</h2></div>
		<div id="error"><h4>*Doğru Bir Mail Adresi Giriniz!</h4></div>
		<!-- Subscription Form ends -->
      </div>
	  <!-- Main content ends -->
	  
	  <!-- Services starts -->
	  <div class="row-fluid services">
        <div class="span4">
		  <!-- Font Awesome Icon -->
		  <i class="icon-desktop icon-4x"></i>
          <h3>Tasarimimiz Degisiyor</h3>
          <p>Tasarimimiz Bastan Asagiya degisiyor.! Tum uzman personel ve ekiplerimiz sizin icin en iyi goruntulenmeyi sunacaklardir..</p>
        </div>
        <div class="span4">
		  <!-- Font Awesome Icon -->
		  <i class="icon-check icon-4x"></i>
		  <h3>Bilgileriniz Korumada</h3>
          <p>Tum giris Bilgileriniz Koruma altindadir. Hicbir üyelik silinmeyecek ve kaldiginiz yerden devam edebileceksiniz..</p>
        </div>
		<div class="span4">
		  <!-- Font Awesome Icon -->
		  <i class="icon-beaker icon-4x"></i>
		  <h3>Mucize Gibi Cozumler</h3>
          <p>Sizlere Mucize gibi cozumlerimiz var.! Bizi izlemeye devam edin mucizeleri kacirmayin.!..</p>
        </div>
      </div>
	  <!-- Services ends -->
	  
    </div>
	<!-- Wrapper container ends -->
    </div>
	<!-- Right Shadow Background ends -->
    </div>
	<!-- Left Shadow Background ends -->
	
	<!-- Footer starts -->
	<div id="footer" class="">
	<!-- OR use this for STATIC footer:  <div id="footer" class="static"> -->
		
		<!-- Footer handle starts -->
	    <div class="handle">
			<a href="#">
		        <div class="custom-icon">
					<span class="custom-icon-bar"></span>
					<span class="custom-icon-bar"></span>
					<span class="custom-icon-bar"></span>
			    </div>
				<span class="handle-text">Tikla Ac.!</span>
			</a>
	    </div>
		<!-- Footer handle ends -->
		
		<!-- Nanoscroller starts -->
	    <div class="nano">
			<div class="content overthrow">
				<div class="row-fluid">
					<div class="row-fluid">
							
					 <!-- Twitter Feed -->
						{{--<div class="span4">
							<h3 class="tweet-name">Twitlerimiz</h3>
							<div class="tweet"></div>
						</div>--}}
						<!-- Twitter Feed ends -->
						
						<!-- About starts -->
						<div class="span7">
							<h3>Hakkimizda</h3>
							<p>1996 yılında tamamen yerel sermaye ve teknoloji ile kurulan Manas, akıllı sayaç sistemlerine dayalı olarak geliştirdiği ürünler ve çözümler ile sektörde yenilikçi bir firma konumuna gelmiştir.</p>
							<address>
								Twitter.com/manasenerji<br>
								A.S.O. 1.Organize Sanayi Bölgesi Ahi Evran Mah. Anadolu Cad.No:25 Sincan / ANKARA<br>
								<abbr title="Phone">Tel:</abbr> 444 76 67
							</address>
							<address>
								<strong>Manas Enerji Yönetimi AŞ</strong><br>
								<a href="mailto:#">edestek@manas.com.tr</a>
							</address>
						</div>
						<!-- About ends -->
					
					</div>
					<div class="row-fluid">
						
						<!-- Copyright starts -->
						<div class="span12 copyright">
							<p>Manas Enerji Yönetimi A.Ş. &copy; Company {{date('Y')}}</p>
						</div>
						<!-- Copyright ends -->
						
					</div>
				</div>
			</div>
	    </div>
		<!-- Nanoscroller ends -->
		
	</div>
	<!-- Foοter ends -->

    <!-- Javascript -->
    <script src="{{ URL::to('assets/bakim/js/jquery-1.9.0.min.js')}}"></script>
    <script src="{{ URL::to('assets/bakim/js/bootstrap.min.js')}}"></script>
	
	<!-- Javascript for Countdown -->
	<script src="{{ URL::to('assets/bakim/js/jquery.countdown.min.js')}}"></script>
	<!--suppress JSCheckFunctionSignatures -->
	<script type="text/javascript">
		$(document).ready(function () {

			var launchDate = "{{$tarih}}";  //Your launch date in: "YYYY/MM/DD, HH:MM"

			var splitDate = launchDate.split(" ");
			var d = splitDate[0].split("-");
            var ms = splitDate[1].split(".");
			var h = ms[0].split(":");

			var countdown = new Date(d[0], d[1]-1, d[2], h[0], h[1]);
			$('#cdays').countdown({until: countdown, format: 'DHMS', 
    layout: '{dnn}'});
			$('#chours').countdown({until: countdown, format: 'DHMS', 
		    layout: '{hnn}'});
			$('#cminutes').countdown({until: countdown, format: 'DHMS', 
		    layout: '{mnn}'});
			$('#cseconds').countdown({until: countdown, format: 'DHMS', 
		    layout: '{snn}'});
		});
	</script>
	
	<!-- Javascript for Subscription Form -->
	<script type="text/javascript">
		$(document).ready(function () {
			
			$('#subscribe-frm').submit(function(){
				$.ajax({
				url:'email.php',
				type :'POST',
				dataType:'json',
				data: $(this).serialize(),
					success: function(data){
						if(data.error){
							$('#error').fadeIn();
							$('#email').addClass('error-input');
						}else{
							$('#success').fadeIn();
							$("#error").hide();
							$('#email').removeClass('error-input');
							$("#subscribe-frm").hide();
						}
					}
				});
				return false;
			});
			
			$('#email').focus(function(){
				$('#error').fadeOut();
				$('#email').removeClass('error-input');
			});
			$('#email').keydown(function(){
				$('#error').fadeOut();
				$('#email').removeClass('error-input');
			});
			
		});
	</script>
	
	<!-- Javascript for Footer Slider -->
	<script src="{{ URL::to('assets/bakim/js/jquery.easing.1.3.js')}}"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			
			var contentHeight = 300 + 20;  //Nanoscroller height + padding
			
		    $('#footer').css('bottom',-contentHeight);
		    var hidden = true,animating = false;
		    
		    $('#footer .handle a').click(function(e) {
		        e.preventDefault();
		        animating = true;
		        if(hidden) {
		            $('#footer').animate({
		                    bottom:0
		                },600,function() {
		                    animating = false;
		                    hidden = false;
		                    $('#footer .handle-text').html("Tikla Kapat.!");
		            });
		        } else {
		            $('#footer').animate({
		                    bottom:-contentHeight
		                },1200,'easeOutBounce',function() {
		                    animating = false;
		                    hidden = true;
		                    $('#footer .handle-text').html("Tikla Ac.!");
		            });
		        }
		    });
		});
	</script>
	
	<!-- Javascript for Nanoscroller -->
	<script src="{{ URL::to('assets/bakim/js/jquery.nanoscroller.min.js')}}"></script>
    <script src="{{ URL::to('assets/bakim/js/overthrow.min.js')}}"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			$(window).load(function () {
				$(".nano").nanoScroller({ preventPageScrolling: true });
			});
		});
	</script>
	
	<!-- Javascript for ColorBox -->
	<script src="{{ URL::to('assets/bakim/js/jquery.colorbox-min.js')}}"></script>
	<script type="text/javascript">
		jQuery(document).ready(function () {
                jQuery('a.group1').colorbox({
					opacity:0.6,
					rel: 'group1',
					maxWidth:'95%',
					maxHeight:'95%'
				});
            });
	</script>
	
	<!-- Javascript for Twitter Feed -->
	<script src="{{ URL::to('assets/bakim/js/jquery.tweet.js')}}"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			
			var twittername = "manasenerji";  //Your twitter username
			
			$(".tweet").tweet({
			  username: twittername,
	          count: 3,
			  template: "{avatar}{join} {text}<br />{time}",
			  //avatar_size: 48,
			  //join_text: "auto",
	          //auto_join_text_default: "We said, ",
	          //auto_join_text_ed: "We ",
	          //auto_join_text_ing: "We were ",
	          //auto_join_text_reply: "We replied ",
	          //auto_join_text_url: "We were checking out ",
	          loading_text: "Tweetler Yükleniyor..."
	        });
			
			//var twitterheading = "Son Tweetler";
			//$(".tweet-name").html(twitterheading + " <a href='../../www.twitter.com/" + twittername + "'>@" + twittername + "</a>");
		});
	</script>

  </body>
</html>
