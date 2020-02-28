@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Kullanıcı <small>Profil Bilgileri</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/admin/pages/css/profile.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-toastr/toastr.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" >
@stop

@section('page-styles')
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/jquery.sparkline.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/admin/pages/scripts/profile.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-toastr/toastr.min.js') }}"></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/jquery-validation/js/localization/messages_tr.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript" ></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript" ></script>
@stop

@section('page-script')
<script src="{{ URL::to('pages/kullanicilar/form-validation.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
   Profile.init();  
   FormValidationKullanici.init();
});
</script>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        <div class="profile-sidebar" style="width: 250px;">
            <!-- PORTLET MAIN -->
            <div class="portlet light profile-sidebar-portlet">
                <!-- SIDEBAR USERPIC -->
                <div class="profile-userpic">
                    <img src="@if(Auth::user()->avatar!=' ' && Auth::user()->avatar!=null ) {{ URL::to('assets/images/profilresim/'.Auth::user()->avatar.'') }} @else {{ URL::to('assets/images/profilresim/test.png') }} @endif" class="img-responsive" alt="">
                </div>
                <!-- END SIDEBAR USERPIC -->
                <!-- SIDEBAR USER TITLE -->
                <div class="profile-usertitle">
                    <div class="profile-usertitle-name">
                        {{ Auth::user()->adi_soyadi }}
                    </div>
                    <div class="profile-usertitle-job">
                        {{ Auth::user()->grup->grupadi}}
                    </div>
                </div>
                <!-- END SIDEBAR USER TITLE -->
                <!-- SIDEBAR BUTTONS -->
                <div class="profile-userbuttons">
                   {{-- <button type="button" class="btn btn-circle green-haze btn-sm">Follow</button>
                    <button type="button" class="btn btn-circle btn-danger btn-sm">Message</button>--}}
                </div>
                <!-- END SIDEBAR BUTTONS -->
                <!-- SIDEBAR MENU -->
                <div class="profile-usermenu">
                    <ul class="nav">
                        <li>
                            <a href="{{ URL::to('profil') }}">
                                <i class="icon-home"></i>
                                Profil Ana Sayfa </a>
                        </li>
                        <li  class="active">
                            <a href="{{ URL::to('profil/profilbilgi') }}">
                                <i class="icon-settings"></i>
                                Profil Bilgileri </a>
                        </li>

                    </ul>
                </div>
                <!-- END MENU -->
            </div>
            <!-- END PORTLET MAIN -->
        </div>
        <!-- END BEGIN PROFILE SIDEBAR -->
        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="portlet-title tabbable-line">
                            <div class="caption caption-md">
                                <i class="icon-globe theme-font hide"></i>
                                <span class="caption-subject font-blue-madison bold uppercase">Profil Bilgileri</span>
                            </div>
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#tab_1_1" data-toggle="tab">Kişisel Bilgiler</a>
                                </li>
                                <li>
                                    <a href="#tab_1_2" data-toggle="tab">Avatar Değiştir</a>
                                </li>
                                <li>
                                    <a href="#tab_1_3" data-toggle="tab">Şifre Değiştir</a>
                                </li>
                            </ul>
                        </div>
                        <div class="portlet-body">
                            <div class="tab-content">
                                <!-- PERSONAL INFO TAB -->
                                <div class="tab-pane active" id="tab_1_1">
                                    <form role="form" action="{{ URL::to('profil/profilbilgi/'.Auth::user()->id.'/1') }}" id="form_sample_3" method="POST" class="form-horizontal" novalidate="novalidate">
                                        <div class="form-group">
                                            <label class="control-label">Adı Soyadı<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right"><i class="fa"></i><input type="text" id="adisoyadi" name='adisoyadi' value="{{Auth::user()->adi_soyadi}}" data-required="1" placeholder="Adınız" class="form-control"/></div>

                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Giriş Adı<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right"><i class="fa"></i><input type="text" id="girisadi" name='girisadi' value="{{Auth::user()->girisadi}}" data-required="1" placeholder="Giriş Adınız" class="form-control"/></div>

                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Email Adresi</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-envelope"></i>
                                                </span>
                                                <input type="email" id="email" name="email" value="{{Auth::user()->email}}" data-required="0" placeholder="Mail Adresiniz" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Telefon</label>
                                            <input type="text" id="telefon" name="telefon" value="{{Auth::user()->telefon}}" data-required="0" placeholder="Telefon Numarası" class="form-control"/>
                                        </div>
                                        <div class="margiv-top-10">
                                            <button type="submit" class="btn green-haze">
                                                Değişiklikleri kaydet </button>
                                            <a href="{{ URL::to('profil') }}" class="btn default">
                                                Vazgeç </a>
                                        </div>
                                    </form>
                                </div>
                                <!-- END PERSONAL INFO TAB -->
                                <!-- CHANGE AVATAR TAB -->
                                <div class="tab-pane" id="tab_1_2">
                                    <p>
                                         Profil resminizi bu kısımdan değiştirebilirsiniz
                                    </p>
                                    <form role="form" action="{{ URL::to('profil/profilbilgi/'.Auth::user()->id.'/2') }}" id="form_sample_4" method="POST" enctype="multipart/form-data" class="form-horizontal" novalidate="novalidate">
                                        <div class="form-group">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 200px; height: 200px;">
                                                    @if(Auth::user()->avatar)
                                                        <img src="{{ URL::to('assets/images/profilresim/'.Auth::user()->avatar) }}" alt=""/>
                                                    @else
                                                        <img src="" alt=""/>
                                                    @endif
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 200px;">
                                                </div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new">
                                                            Resim Seç </span>
                                                        <span class="fileinput-exists">
                                                            Değiştir </span>
                                                        <input type="file" name="avatar">
                                                    </span>
                                                    <a href="" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                        Sil </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="margin-top-10">
                                            <button type="submit" class="btn green-haze"> Kaydet </button>
                                            <a href="{{ URL::to('profil') }}" class="btn default"> Vazgeç </a>
                                        </div>
                                    </form>
                                </div>
                                <!-- END CHANGE AVATAR TAB -->
                                <!-- CHANGE PASSWORD TAB -->
                                <div class="tab-pane" id="tab_1_3">
                                    <form role="form" action="{{ URL::to('profil/profilbilgi/'.Auth::user()->id.'/3') }}" id="form_sample_5" method="POST" class="form-horizontal" novalidate="novalidate">
                                        <div class="form-group">
                                            <label class="control-label">Şuanki Şifre<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right"><i class="fa"></i><input type="password" id="oldpassword" name="oldpassword" data-required="1" class="form-control"/></div>

                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Yeni Şifre<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right"><i class="fa"></i><input type="password" id="password" name="password" data-required="1" class="form-control"/></div>

                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Yeni Şifre Tekrar<span class="required" aria-required="true"> * </span></label>
                                            <div class="input-icon right"><i class="fa"></i><input type="password" id="password_confirmation" name="password_confirmation" data-required="1" class="form-control"/></div>

                                        </div>
                                        <div class="margin-top-10">
                                            <button type="submit" class="btn green-haze">
                                                Şifre Değiştir </button>
                                            <a href="{{ URL::to('profil') }}" class="btn default">
                                                Vazgeç </a>
                                        </div>
                                    </form>
                                </div>
                                <!-- END CHANGE PASSWORD TAB -->
                                <!-- PRIVACY SETTINGS TAB -->
                                <div class="tab-pane" id="tab_1_4">
                                    <form action="#">
                                        <table class="table table-light table-hover">
                                            <tr>
                                                <td>
                                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus..
                                                </td>
                                                <td>
                                                    <label class="uniform-inline">
                                                        <input type="radio" name="optionsRadios1" value="option1"/>
                                                        Yes </label>
                                                    <label class="uniform-inline">
                                                        <input type="radio" name="optionsRadios1" value="option2" checked/>
                                                        No </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Enim eiusmod high life accusamus terry richardson ad squid wolf moon
                                                </td>
                                                <td>
                                                    <label class="uniform-inline">
                                                        <input type="checkbox" value=""/> Yes </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Enim eiusmod high life accusamus terry richardson ad squid wolf moon
                                                </td>
                                                <td>
                                                    <label class="uniform-inline">
                                                        <input type="checkbox" value=""/> Yes </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Enim eiusmod high life accusamus terry richardson ad squid wolf moon
                                                </td>
                                                <td>
                                                    <label class="uniform-inline">
                                                        <input type="checkbox" value=""/> Yes </label>
                                                </td>
                                            </tr>
                                        </table>
                                        <!--end profile-settings-->
                                        <div class="margin-top-10">
                                            <a href="" class="btn green-haze">
                                                Save Changes </a>
                                            <a href="" class="btn default">
                                                Cancel </a>
                                        </div>
                                    </form>
                                </div>
                                <!-- END PRIVACY SETTINGS TAB -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PROFILE CONTENT -->
    </div>
</div>

@stop
