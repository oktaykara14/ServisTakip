<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>@if(isset($title)) Servis Takip | {{ $title }} @else Servis Takip @endif</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="{{ URL::to('assets/loginpage/styles/opensans.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/uniform/css/uniform.default.css') }}" rel="stylesheet" type="text/css"/>

<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ URL::to('assets/admin/pages/css/lock.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="{{ URL::to('assets/global/css/components-rounded.css') }}" id="style_components" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/css/plugins.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/admin/layout4/css/layout.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/admin/layout4/css/themes/light.css') }}" rel="stylesheet" type="text/css" id="style_color"/>
<link href="{{ URL::to('assets/admin/layout4/css/custom.css') }}" rel="stylesheet" type="text/css"/>

<!-- END THEME STYLES -->
<link rel="shortcut icon" href="{{ URL::to('favicon.ico') }}"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body>
<div class="page-lock" style="width: 60% !important;">
	<div class="page-logo">
		<a class="brand" href="">
            <img src="{{ URL::to('assets/images/logo/logo-white.png') }}" alt="logo"/>
		</a>
	</div>
	<div class="page-body">
		<div class="lock-head">
			 Servis Takip Sistemi - Şifre Sıfırlama
		</div>
		<div class="form-body">
            <div class="portlet box">
                <div class="portlet-body form">
                    <form action="{{ URL::to('reminder/reset') }}" id="form_sample" method="POST" class="form-horizontal" novalidate="novalidate">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button>
                            Girilen Bilgilerde Hata Var.
                        </div>
                        <div class="alert alert-success display-hide">
                            <button class="close" data-close="alert"></button>
                            Bilgiler Doğru!
                        </div>
                        <div class="form-body">
                            <div class="form-group hide">
                                <input type="hidden" id="token" name="token" value="{{Input::old('token') ? Input::old('token') : $token}}">
                            </div>
                            <div class="form-group" style="padding-top: 20px">
                                <label class="control-label col-md-3">Email Adresi:</label>
                                <div class="col-md-7">
                                    <input type="email" id="email" name="email" value="{{Input::old('email') ? Input::old('email') : $email}}" data-required="1" class="form-control" readonly="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Yeni Şifre:</label>
                                <div class="col-md-7">
                                    <input type="password" id="password" name="password" value="{{Input::old('password')}}" data-required="1" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Yeni Şifre(Tekrar):</label>
                                <div class="col-md-7">
                                    <input type="password" id="password_confirmation" name="password_confirmation" value="{{Input::old('email')}}" data-required="1" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-xs-12" style="text-align: center">
                                    <button type="submit" class="btn green">Değiştir</button>
                                    <a href="{{ URL::to('login')}}" class="btn default">Üye Girişi</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
	</div>
	<div class="page-footer-custom">
		 &copy; <?=date("Y")?> Manas Online Servis Sayaç Takip Sistemi
	</div>
</div>
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="{{ URL::to('assets/global/plugins/respond.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/excanvas.min.js') }}" type="text/javascript"></script>
<![endif]-->
<script src="{{ URL::to('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery-migrate.min.js') }}" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="{{ URL::to('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery.blockui.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery.cokie.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>

<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{ URL::to('assets/global/plugins/backstretch/jquery.backstretch.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-toastr/toastr.min.js') }}"></script>

<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ URL::to('assets/global/scripts/metronic.j') }}s" type="text/javascript"></script>
<script src="{{ URL::to('assets/admin/layout4/scripts/layout.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/admin/layout4/scripts/demo.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('pages/password/form-validation-1.js') }}"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function() {    
Metronic.init(); // init metronic core components
Layout.init(); // init current layout
Demo.init();
FormValidationPassword.init();
});
</script>
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
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>