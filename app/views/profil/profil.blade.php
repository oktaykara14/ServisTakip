@extends('layout.master')

@section('page-title')
<div class="page-title">
    <h1>Kullanıcı <small>Profil Detayları</small></h1>
</div>
@stop

@section('page-plugins')
<link href="{{ URL::to('assets/admin/pages/css/profile.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::to('assets/admin/pages/css/tasks.css') }}" rel="stylesheet" type="text/css">
<link href="{{ URL::to('assets/global/plugins/bootstrap-toastr/toastr.min.css') }}" rel="stylesheet" type="text/css"/>
@stop

@section('page-styles')
@stop

@section('page-js')
<script src="{{ URL::to('assets/global/plugins/jquery.sparkline.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/admin/pages/scripts/profile.js') }}" type="text/javascript"></script>
<script src="{{ URL::to('assets/global/plugins/bootstrap-toastr/toastr.min.js') }}"></script>
@stop

@section('scripts')
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
   Profile.init(); 
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
                </div>
                <!-- END SIDEBAR BUTTONS -->
                <!-- SIDEBAR MENU -->
                <div class="profile-usermenu">
                    <ul class="nav">
                        <li class="active">
                            <a href="{{ URL::to('profil') }}">
                                <i class="icon-home"></i>
                                Profil Ana Sayfa </a>
                        </li>
                        <li>
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
                       <!-- BEGIN PORTLET -->
                       <div class="portlet light">
                           <div class="portlet-title">
                               <div class="caption caption-md">
                                   <i class="icon-globe theme-font hide"></i>
                                   <span class="caption-subject font-blue-madison bold uppercase">Son Yapılan İşlemler</span>
                               </div>
                           </div>
                           <div class="portlet-body">
                               <!--BEGIN TABS-->
                               <div class="scroller" style="height: 320px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                                           <ul class="feeds">
                                               @foreach($islemler as $islem)
                                               <li>
                                                   <div class="col1">
                                                       <div class="cont">
                                                           <div class="cont-col1">
                                                               <div class="label label-sm {{$islem->label}}">
                                                                   <i class="fa {{$islem->icon}}"></i>
                                                               </div>
                                                           </div>
                                                           <div class="cont-col2">
                                                               <div class="desc">
                                                                   {{$islem->aciklama}}
                                                               </div>
                                                           </div>
                                                       </div>
                                                   </div>
                                                   <div class="col2">
                                                       <div class="date">
                                                           {{$islem->tarih}}
                                                       </div>
                                                   </div>
                                               </li>
                                               @endforeach
                                           </ul>
                                       </div>
                                <!--END TABS-->
                           </div>
                       </div>
                       <!-- END PORTLET -->
                 </div>
            </div>
        </div>
        <!-- END PROFILE CONTENT -->
    </div>
</div>

@stop
