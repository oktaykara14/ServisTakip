<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            @if(Auth::user())
                @if( Auth::user()->grup_id==10 )
                    <a href="{{ URL::to('edestek/edestekkayit') }}"><img src="{{ URL::to('assets/images/logo/logo-default.png') }}" alt="logo" class="logo-default"/></a>
                @elseif( Auth::user()->grup_id==6 )
                    <a href="{{ URL::to('ucretlendirme/ucretlendirmekayit') }}"><img src="{{ URL::to('assets/images/logo/logo-default.png') }}" alt="logo" class="logo-default"/></a>
                @elseif( Auth::user()->grup_id==18 )
                    <a href="{{ URL::to('sube/serviskayit') }}"><img src="{{ URL::to('assets/images/logo/logo-default.png') }}" alt="logo" class="logo-default"/></a>
                @elseif( Auth::user()->grup_id==19 )
                    <a href="{{ URL::to('abone/musterionay') }}"><img src="{{ URL::to('assets/images/logo/logo-default.png') }}" alt="logo" class="logo-default"/></a>
                @elseif( Auth::user()->grup_id==14 ||  Auth::user()->grup_id==15  )
                    <a href="{{ URL::to('uretim/urunkayit') }}"><img src="{{ URL::to('assets/images/logo/logo-default.png') }}" alt="logo" class="logo-default"/></a>
                @else
                    <a href="{{ URL::to('index') }}"> <img src="{{ URL::to('assets/images/logo/logo-default.png') }}" alt="logo" class="logo-default"/> </a>
                @endif
            <div class="menu-toggler sidebar-toggler">
                <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
            </div>
            @endif
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
        </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN PAGE ACTIONS -->
        <!-- DOC: Remove "hide" class to enable the page header actions -->
        <div class="page-actions hide">
            <div class="btn-group">
                <button type="button" class="btn red-haze btn-sm dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                    <span class="hidden-sm hidden-xs">Actions&nbsp;</span><i class="fa fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li>
                        <a href="">
                            <i class="icon-docs"></i> New Post </a>
                    </li>
                    <li>
                        <a href="">
                            <i class="icon-tag"></i> New Comment </a>
                    </li>
                    <li>
                        <a href="">
                            <i class="icon-share"></i> Share </a>
                    </li>
                    <li class="divider">
                    </li>
                    <li>
                        <a href="">
                            <i class="icon-flag"></i> Comments <span class="badge badge-success">4</span>
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <i class="icon-users"></i> Feedbacks <span class="badge badge-danger">2</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- END PAGE ACTIONS -->
        <!-- BEGIN PAGE TOP -->
        <div class="page-top">
            <!-- BEGIN HEADER SEARCH BOX -->
            <!-- DOC: Apply "search-form-expanded" right after the "search-form" class to have half expanded search box -->
            <form class="search-form hide" action="#" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control input-sm" placeholder="Search..." name="query">
                        <span class="input-group-btn">
                            <a href="" class="btn submit"><i class="icon-magnifier"></i></a>
                        </span>
                </div>
            </form>
            <!-- END HEADER SEARCH BOX -->
            <!-- BEGIN TOP NAVIGATION MENU -->
            @if(Auth::user())
            <div class="top-menu">
                <ul class="nav navbar-nav pull-right">
                    <li class="separator hide">
                    </li>
                        @if( Auth::user()->grup_id<17 && Auth::user()->grup_id!=10 && Auth::user()->grup_id!=14 && Auth::user()->grup_id!=15)
                        <!-- BEGIN NOTIFICATION DROPDOWN -->
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                        <li class="dropdown dropdown-extended dropdown-notification " id="header_notification_bar">
                                    <a href="" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                        <i class="icon-bag"></i>
                                        <span class="badge badge-warning">
                                    {{ isset(Auth::user()->onaybildirim) ? Auth::user()->onaybildirim->sayi : 0  }} </span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class="external">
                                            <h3><span class="bold">{{ isset(Auth::user()->onaybildirim) ? Auth::user()->onaybildirim->sayi : 0  }} Onay</span> İşlemi Var</h3>
                                            <a href="{{ URL::to('servistakip/bildirimler/1') }}">Göster</a>
                                        </li>
                                        <li>
                                            <ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
                                                @if(isset(Auth::user()->onaybildirim))
                                                    @foreach( Auth::user()->onaybildirim as $bildirim_row)
                                                        <li>
                                                            <a href="{{$bildirim_row->link}}">
                                                                <span class="time">{{$bildirim_row->time}}</span>
                                                                <span class="details">
                                                        <span class="label label-sm label-icon {{$bildirim_row->label}}">
                                                            <i class="fa {{$bildirim_row->icon}}"></i>
                                                        </span>
                                                        {{$bildirim_row->notify}}</span>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </li>
                                    </ul>
                        </li>
                        <!-- END NOTIFICATION DROPDOWN -->
                        <li class="separator hide">
                        </li>
                        @endif
                        @if( Auth::user()->grup_id<20 && Auth::user()->grup_id!=10 && Auth::user()->grup_id!=18 && Auth::user()->grup_id!=14 && Auth::user()->grup_id!=15)
                                <!-- BEGIN NOTIFICATION DROPDOWN -->
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                        <li class="dropdown dropdown-extended dropdown-notification " id="header_notification_bar">
                            <a href="" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="icon-bell"></i>
                                <span class="badge badge-danger">
                                    {{ isset(Auth::user()->hatirlatma) ? Auth::user()->hatirlatma->sayi : 0  }} </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="external">
                                    <h3><span class="bold">{{ isset(Auth::user()->hatirlatma) ? Auth::user()->hatirlatma->sayi : 0  }} Bekleyen</span> İşlem Var</h3>
                                    <a href="{{ URL::to('servistakip/hatirlatmalar/') }}">Göster</a>
                                </li>
                                <li>
                                    <ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
                                        @if(isset(Auth::user()->hatirlatma))
                                            @foreach( Auth::user()->hatirlatma as $hatirlatma_row)
                                                <li>
                                                    <a href="{{$hatirlatma_row->link}}">
                                                        <span class="time">{{$hatirlatma_row->time}}</span>
                                                    <span class="details">
                                                        <span class="label label-sm label-icon {{$hatirlatma_row->label}}">
                                                            <i class="fa {{$hatirlatma_row->icon}}"></i>
                                                        </span>
                                                        {{$hatirlatma_row->notify}}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                         @endif
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <!-- END NOTIFICATION DROPDOWN -->
                        <li class="separator hide">
                        </li>
                        <!-- BEGIN TODODROPDOWN -->
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                        <li class="dropdown dropdown-extended dropdown-notification" id="header_task_bar">
                            <a href="" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="icon-check"></i>
                        <span class="badge badge-success">
                            {{ isset(Auth::user()->bildirim) ? Auth::user()->bildirim->sayi : 0  }} </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="external">
                                    <h3><span class="bold">{{ isset(Auth::user()->bildirim) ? Auth::user()->bildirim->sayi : 0  }} Okunmamış</span> Bildirim Var</h3>
                                    <a href="{{ URL::to('servistakip/bildirimler/') }}">Göster</a>
                                </li>
                                <li>
                                    <ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
                                        @if(isset(Auth::user()->bildirim))
                                            @foreach( Auth::user()->bildirim as $bildirim_row)
                                                <li>
                                                    <a href="{{$bildirim_row->link}}">
                                                        <span class="time">{{$bildirim_row->time}}</span>
                                                    <span class="details">
                                                        <span class="label label-sm label-icon {{$bildirim_row->label}}">
                                                            <i class="fa {{$bildirim_row->icon}}"></i>
                                                        </span>
                                                        {{$bildirim_row->notify}}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <!-- END TODODROPDOWN -->
                        <li class="separator hide">
                        </li>
                        @endif
                    <!-- BEGIN INBOX DROPDOWN -->
                    <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                    <li class="dropdown dropdown-extended dropdown-inbox hide" id="header_inbox_bar">
                        <a href="" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <i class="icon-envelope-open"></i>
                            <span class="badge badge-danger">
                                4 </span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="external">
                                <h3><span class="bold">7</span> Yeni Mesajınız var </h3>
                                <a href="{{ URL::to('profil/gelen/') }}">Göster</a>
                            </li>
                            <li>
                                <ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
                                    <li>
                                        <a href="#">
                                            <span class="photo">
                                                <img src="{{ URL::to('assets/admin/layout3/img/avatar2.jpg') }}" class="img-circle" alt="">
                                            </span>
                                            <span class="subject">
                                                <span class="from">
                                                    Lisa Wong </span>
                                                <span class="time">Just Now </span>
                                            </span>
                                            <span class="message">
                                                Vivamus sed auctor nibh congue nibh. auctor nibh auctor nibh... </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <!-- END INBOX DROPDOWN -->
                    <!-- BEGIN USER LOGIN DROPDOWN -->
                    <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                    <li class="dropdown dropdown-user dropdown-extended">
                        <a href="" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            @if(Auth::user())
                            <span class="username username-hide-on-mobile">{{ Auth::user()->adi_soyadi }}</span>
                            <input class="hide userid" value="{{Auth::user()->id}}" />
                            <input class="hide useravatar" value="{{Auth::user()->avatar}}" />
                            <input class="hide root" value="{{URL::to('/')}}" />
                            <!-- DOC: Do not remove below empty space(&nbsp;) as its purposely used -->
                            <img alt="" class="img-circle" src="@if(Auth::user()->avatar!=' ' && Auth::user()->avatar!=null  ) {{ URL::to('assets/images/profilresim/'.Auth::user()->avatar.'') }} @else {{ URL::to('assets/images/profilresim/test.png') }} @endif"/>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-default">
                            <li>
                                <a href="{{ URL::to('profil') }}">
                                    <i class="icon-user"></i> Profilim </a>
                            </li>
                            <li>
                                <a href="{{ URL::to('mesaj') }}">
                                    <i class="icon-envelope-open"></i> Gelen Kutusu <span class="badge badge-danger">
                                        {{Auth::user()->newMessagesCount()}} </span>
                                </a>
                            </li>
                            <li class="divider">
                            </li>
                            <li>
                                <a href="{{ URL::to('lock') }}">
                                    <i class="icon-lock"></i> Ekranı Kilitle </a>
                            </li>
                            <li>
                                <a href="{{ URL::to('logout') }}">
                                    <i class="icon-key"></i> Çıkış </a>
                            </li>
                        </ul>
                    </li>
                    <!-- END USER LOGIN DROPDOWN -->
                    <!-- BEGIN USER LOGIN DROPDOWN -->
                    <li class="dropdown dropdown-extended quick-sidebar-toggler hide">
                        <span class="sr-only">Toggle Quick Sidebar</span>
                        <i id="mesajlasma" class="icon-logout"></i>
                    </li>
                    <!-- END USER LOGIN DROPDOWN -->
                </ul>
            </div>
            @endif
            <!-- END TOP NAVIGATION MENU -->
        </div>
        <!-- END PAGE TOP -->
    </div>
    <!-- END HEADER INNER -->
</div>
