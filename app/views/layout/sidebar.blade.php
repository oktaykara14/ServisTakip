@if(Auth::user())
<div class="page-sidebar-wrapper">
    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
    <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
    <div class="page-sidebar navbar-collapse collapse">
        <!-- BEGIN SIDEBAR MENU -->
        <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
        <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
        <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
        <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <ul class="page-sidebar-menu " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
        @if(Auth::user()->grup_id !=10 && Auth::user()->grup_id !=6 && Auth::user()->grup_id !=14 && Auth::user()->grup_id !=15 && Auth::user()->grup_id <18 )
            @if(Request::segment(1)=='index') <li class="active"> @else <li> @endif
                 <a href="{{ URL::to('index') }}">
                    <i class="icon-home"></i>
                    <span class="title">Ana Sayfa</span>
                </a>
            </li>
        @endif
        @if(Auth::user()->grup_id <7 || Auth::user()->grup_id==7 || Auth::user()->grup_id==11 || Auth::user()->grup_id==16 )
            @if(Request::segment(1)=='suservis') <li class="active"> @else <li> @endif
                <a href="">
                    <i class="icon-drop"></i>
                    <span class="title">Su Servis</span>
                    <span class="arrow @if(Request::segment(1)=='suservis') open @endif"></span>
                </a>
                <ul class="sub-menu">
                    @if(Auth::user()->grup_id!=6 )
                        @if(Request::segment(1)=='suservis' && (Request::segment(2)=='sayackayit' || Request::segment(2)=='sayackayitekle' || Request::segment(2)=='sayackayitduzenle')) <li class="active"> @else <li> @endif
                             <a href="{{ URL::to('suservis/sayackayit') }}">
                                <i class="icon-pencil"></i>
                                Sayaç Kayıt</a>
                        </li>
                    @endif
                    @if(Auth::user()->grup_id!=16 )
                        @if(Request::segment(1)=='suservis' && (Request::segment(2)=='arizakayit' || Request::segment(2)=='arizakayitekle' || Request::segment(2)=='arizakayitduzenle')) <li class="active"> @else <li> @endif
                         <a href="{{ URL::to('suservis/arizakayit') }}">
                            <i class="icon-wrench"></i>
                            Arıza Kayıt</a>
                        </li>
                        @if(Auth::user()->grup_id!=6 )
                            @if(Request::segment(1)=='suservis' && (Request::segment(2)=='beyanname' || Request::segment(2)=='beyannameekle' || Request::segment(2)=='beyannameduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('suservis/beyanname') }}">
                                <i class="icon-pin"></i>
                                Beyannameler</a>
                            </li>
                        @endif
                    @endif
                </ul>
            </li>
        @endif
        @if(Auth::user()->grup_id <7 || Auth::user()->grup_id==8 || Auth::user()->grup_id==12 || Auth::user()->grup_id==16 )
            @if(Request::segment(1)=='elkservis') <li class="active"> @else <li> @endif
                <a href="javascript:">
                    <i class="icon-energy"></i>
                    <span class="title">Elektrik Servis</span>
                    <span class="arrow @if(Request::segment(1)=='elkservis') open @endif"></span>
                </a>
                <ul class="sub-menu">
                    @if(Auth::user()->grup_id!=6 )
                        @if(Request::segment(1)=='elkservis' && (Request::segment(2)=='sayackayit' || Request::segment(2)=='sayackayitekle' || Request::segment(2)=='sayackayitduzenle')) <li class="active"> @else <li> @endif
                             <a href="{{ URL::to('elkservis/sayackayit') }}">
                                <i class="icon-pencil"></i>
                                Sayaç Kayıt</a>
                        </li>
                    @endif
                    @if(Auth::user()->grup_id!=16 )
                        @if(Request::segment(1)=='elkservis' && (Request::segment(2)=='arizakayit' || Request::segment(2)=='arizakayitekle' || Request::segment(2)=='arizakayitduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('elkservis/arizakayit') }}">
                            <i class="icon-wrench"></i>
                            Arıza Kayıt</a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        @if(Auth::user()->grup_id <7 || Auth::user()->grup_id==7 || Auth::user()->grup_id==11 || Auth::user()->grup_id==16 )
            @if(Request::segment(1)=='gazservis') <li class="active"> @else <li> @endif
                <a href="javascript:">
                    <i class="icon-fire"></i>
                    <span class="title">Gaz Servis</span>
                    <span class="arrow @if(Request::segment(1)=='gazservis') open @endif"></span>
                </a>
                <ul class="sub-menu">
                    @if(Auth::user()->grup_id!=6 )
                        @if(Request::segment(1)=='gazservis' && (Request::segment(2)=='sayackayit' || Request::segment(2)=='sayackayitekle' || Request::segment(2)=='sayackayitduzenle')) <li class="active"> @else <li> @endif
                             <a href="{{ URL::to('gazservis/sayackayit') }}">
                                <i class="icon-pencil"></i>
                                Sayaç Kayıt</a>
                        </li>
                    @endif
                    @if(Auth::user()->grup_id!=16 )
                        @if(Request::segment(1)=='gazservis' && (Request::segment(2)=='arizakayit' || Request::segment(2)=='arizakayitekle' || Request::segment(2)=='arizakayitduzenle')) <li class="active"> @else <li> @endif
                             <a href="{{ URL::to('gazservis/arizakayit') }}">
                                <i class="icon-wrench"></i>
                                Arıza Kayıt</a>
                        </li>
                        @if(Auth::user()->grup_id!=6 )
                            @if(Request::segment(1)=='gazservis' && (Request::segment(2)=='beyanname' || Request::segment(2)=='beyannameekle' || Request::segment(2)=='beyannameduzenle')) <li class="active"> @else <li> @endif
                                <a href="{{ URL::to('gazservis/beyanname') }}">
                                    <i class="icon-pin"></i>
                                    Beyannameler</a>
                            </li>
                        @endif
                    @endif
                </ul>
            </li>
        @endif
        @if(Auth::user()->grup_id <7 || Auth::user()->grup_id==9 || Auth::user()->grup_id==16)
            @if(Request::segment(1)=='mekanikgaz') <li class="active"> @else <li> @endif
                <a href="javascript:">
                    <i class="icon-reload"></i>
                    <span class="title">Mekanik Gaz S.</span>
                    <span class="arrow @if(Request::segment(1)=='mekanikgaz') open @endif"></span>
                </a>
                <ul class="sub-menu">
                    @if(Auth::user()->grup_id!=6 )
                        @if(Request::segment(1)=='mekanikgaz' && (Request::segment(2)=='sayackayit' || Request::segment(2)=='sayackayitekle' || Request::segment(2)=='sayackayitduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('mekanikgaz/sayackayit') }}">
                                <i class="icon-pencil"></i>
                                Sayaç Kayıt</a>
                        </li>
                    @endif
                    @if(Auth::user()->grup_id!=16 )
                        @if(Request::segment(1)=='mekanikgaz' && (Request::segment(2)=='arizakayit' || Request::segment(2)=='arizakayitekle' || Request::segment(2)=='arizakayitduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('mekanikgaz/arizakayit') }}">
                                <i class="icon-wrench"></i>
                                Arıza Kayıt</a>
                        </li>
                        @if(Auth::user()->grup_id!=6 )
                            @if(Request::segment(1)=='mekanikgaz' && (Request::segment(2)=='beyanname' || Request::segment(2)=='beyannameekle' || Request::segment(2)=='beyannameduzenle')) <li class="active"> @else <li> @endif
                                <a href="{{ URL::to('mekanikgaz/beyanname') }}">
                                    <i class="icon-pin"></i>
                                    Beyannameler</a>
                            </li>
                        @endif
                        @if(Request::segment(1)=='mekanikgaz' && (Request::segment(2)=='servisraporu')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('mekanikgaz/servisraporu') }}">
                                <i class="icon-list"></i>
                                Servis Raporu</a>
                        </li>
                    @endif
                    @if(Auth::user()->grup_id!=6 )
                        @if(Request::segment(1)=='mekanikgaz' && (Request::segment(2)=='periyodik' || Request::segment(2)=='periyodikekle' || Request::segment(2)=='periyodikduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('mekanikgaz/periyodik') }}">
                                <i class="icon-refresh"></i>
                                Periyodik Bakım</a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        @if(Auth::user()->grup_id <7 || Auth::user()->grup_id==7  || Auth::user()->grup_id==11 || Auth::user()->grup_id==16)
            @if(Request::segment(1)=='isiservis') <li class="active"> @else <li> @endif
                <a href="javascript:">
                    <i class="icon-pointer"></i>
                    <span class="title">Isı Servis</span>
                    <span class="arrow @if(Request::segment(1)=='isiservis') open @endif"></span>
                </a>
                <ul class="sub-menu">
                    @if(Auth::user()->grup_id!=6 )
                        @if(Request::segment(1)=='isiservis' && (Request::segment(2)=='sayackayit' || Request::segment(2)=='sayackayitekle' || Request::segment(2)=='sayackayitduzenle')) <li class="active"> @else <li> @endif
                             <a href="{{ URL::to('isiservis/sayackayit') }}">
                                <i class="icon-pencil"></i>
                                Sayaç Kayıt</a>
                        </li>
                    @endif
                    @if(Auth::user()->grup_id!=16 )
                        @if(Request::segment(1)=='isiservis' && (Request::segment(2)=='arizakayit' || Request::segment(2)=='arizakayitekle' || Request::segment(2)=='arizakayitduzenle')) <li class="active"> @else <li> @endif
                             <a href="{{ URL::to('isiservis/arizakayit') }}">
                                <i class="icon-wrench"></i>
                                Arıza Kayıt</a>
                        </li>
                        @if(Auth::user()->grup_id!=6 )
                            @if(Request::segment(1)=='isiservis' && (Request::segment(2)=='beyanname' || Request::segment(2)=='beyannameekle' || Request::segment(2)=='beyannameduzenle')) <li class="active"> @else <li> @endif
                                <a href="{{ URL::to('isiservis/beyanname') }}">
                                    <i class="icon-pin"></i>
                                    Beyannameler</a>
                            </li>
                        @endif
                    @endif
                </ul>
            </li>
        @endif
        @if(Auth::user()->grup_id <5 || Auth::user()->grup_id==10)
            @if(Request::segment(1)=='edestek') <li class="active"> @else <li> @endif 
                <a href="javascript:">
                    <i class="icon-graduation"></i>
                    <span class="title">Yazılım Destek</span>
                    <span class="arrow @if(Request::segment(1)=='edestek') open @endif"></span>
                </a>
                <ul class="sub-menu">
                    @if(Request::segment(2)=='edestekkayit' || Request::segment(2)=='kayitekle' || Request::segment(2)=='kayitduzenle') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('edestek/edestekkayit') }}">
                            <i class="icon-home"></i>
                            Ana Sayfa</a>
                    </li>
                    @if(Request::segment(2)=='projebilgisi' || Request::segment(2)=='musteriekle' || Request::segment(2)=='musteriduzenle') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('edestek/projebilgisi') }}">
                            <i class="icon-docs"></i>
                            Proje Bilgileri</a>
                    </li>
                    @if(Request::segment(2)=='hatacozumleri' || Request::segment(2)=='cozumekle' || Request::segment(2)=='cozumduzenle') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('edestek/hatacozumleri') }}">
                            <i class="icon-directions"></i>
                            Hata Çözümleri</a>
                    </li>
                    @if(Request::segment(2)=='islemler' || Request::segment(2)=='islemekle' || Request::segment(2)=='islemduzenle') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('edestek/islemler') }}">
                            <i class="icon-puzzle"></i>
                            Düzenli İşlemler</a>
                    </li>
                    @if(Request::segment(2)=='personel' || Request::segment(2)=='personelekle' || Request::segment(2)=='personelduzenle') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('edestek/personel') }}">
                            <i class="icon-users"></i>
                            Personel Bilgisi</a>
                    </li>
                </ul>
            </li>
        @endif
        {{--@if(Auth::user()->grup_id <5 || Auth::user()->grup_id==10)
            @if(Request::segment(1)=='destek') <li class="active"> @else <li> @endif
                <a href="javascript:">
                    <i class="icon-graduation"></i>
                    <span class="title">Yazılım Destek</span>
                    <span class="arrow @if(Request::segment(1)=='destek') open @endif"></span>
                </a>
                <ul class="sub-menu">
                    @if(Request::segment(2)=='kayit' || Request::segment(2)=='kayitekle' || Request::segment(2)=='kayitduzenle') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('destek/kayit') }}">
                            <i class="icon-home"></i>
                            Ana Sayfa</a>
                    </li>
                </ul>
            </li>
        @endif--}}

        @if(Auth::user()->grup_id <5 || Auth::user()->grup_id==6 || Auth::user()->grup_id==17 || Auth::user()->grup_id==18)
            @if(Request::segment(1)=='sube') <li class="active"> @else <li> @endif
                <a href="javascript:">
                    <i class="icon-share"></i>
                    <span class="title">Şube</span>
                    <span class="arrow @if(Request::segment(1)=='sube') open @endif"></span>
                </a>
                <ul class="sub-menu">
                @if(Auth::user()->grup_id!=18)
                    @if(Request::segment(2)=='abonekayit' || Request::segment(2)=='abonekayitekle' || Request::segment(2)=='abonekayitduzenle') <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('sube/abonekayit') }}">
                            <i class="icon-magnifier"></i>
                            Abone Bilgileri</a>
                    </li>
                    @if(Request::segment(2)=='sayacsatis' || Request::segment(2)=='sayacsatisekle' || Request::segment(2)=='sayacsatisduzenle' || Request::segment(2)=='sayacsatisgoster') <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('sube/sayacsatis') }}">
                            <i class="icon-bag"></i>
                            Satış Ekranı</a>
                    </li>
                    @if( Auth::user()->grup_id!=6 )
                        @if(Request::segment(2)=='sayackayit' || Request::segment(2)=='sayackayitekle' || Request::segment(2)=='sayackayitduzenle' || Request::segment(2)=='sayackayitgoster') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('sube/sayackayit') }}">
                                <i class="icon-pencil"></i>
                                Arızalı Sayaç Kayıdı</a>
                        </li>
                        @if(Request::segment(2)=='arizakayit' || Request::segment(2)=='arizakayitekle' || Request::segment(2)=='arizakayitduzenle' || Request::segment(2)=='arizakayitgoster') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('sube/arizakayit') }}">
                                <i class="icon-wrench"></i>
                                Tamir Ekranı</a>
                        </li>
                    @endif
                @endif
                @if(Auth::user()->grup_id!=6  )
                    @if(Request::segment(2)=='serviskayit' || Request::segment(2)=='serviskayitekle' || Request::segment(2)=='serviskayitduzenle' || Request::segment(2)=='serviskayitgoster') <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('sube/serviskayit') }}">
                            <i class="icon-check"></i>
                            Servis Bilgisi</a>
                    </li>
                @endif
                @if(Auth::user()->grup_id!=18 && Auth::user()->grup_id!=6  )
                    @if(Request::segment(1)=='sube' && (Request::segment(2)=='beyanname' || Request::segment(2)=='beyannameekle' || Request::segment(2)=='beyannameduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('sube/beyanname') }}">
                            <i class="icon-pin"></i>
                            Beyannameler</a>
                    </li>
                @endif
                </ul>
            </li>
        @endif
        @if(Auth::user()->grup_id <5 || Auth::user()->grup_id==14 || Auth::user()->grup_id==15  || Auth::user()->grup_id==16 )
             @if(Request::segment(1)=='uretim') <li class="active"> @else <li> @endif
                 <a href="">
                     <i class="icon-settings"></i>
                     <span class="title">Üretim</span>
                     <span class="arrow @if(Request::segment(1)=='uretim') open @endif"></span>
                 </a>
                 <ul class="sub-menu">
                     @if(Request::segment(1)=='uretim' && (Request::segment(2)=='urunkayit' || Request::segment(2)=='urunkayitekle' || Request::segment(2)=='urunkayitduzenle')) <li class="active"> @else <li> @endif
                         <a href="{{ URL::to('uretim/urunkayit') }}">
                             <i class="icon-pencil"></i>
                             Ürün Kayıt</a>
                     </li>
                     @if(Request::segment(1)=='uretim' && (Request::segment(2)=='acikisemri')) <li class="active"> @else <li> @endif
                         <a href="{{ URL::to('uretim/acikisemri') }}">
                             <i class="icon-eye"></i>
                             Açık İş Emri</a>
                     </li>
                     @if(Request::segment(1)=='uretim' && (Request::segment(2)=='uretimsonukayit' || Request::segment(2)=='uretimsonukayitekle' || Request::segment(2)=='uretimsonukayitduzenle' || Request::segment(2)=='uretimsonukaydigoster')) <li class="active"> @else <li> @endif
                         <a href="{{ URL::to('uretim/uretimsonukayit') }}">
                             <i class="icon-calendar"></i>
                             Üretim Sonu Kaydı</a>
                     </li>
                     @if(Request::segment(1)=='uretim' && (Request::segment(2)=='urunsorgulama' || Request::segment(2)=='urungoster')) <li class="active"> @else <li> @endif
                         <a href="{{ URL::to('uretim/urunsorgulama') }}">
                             <i class="icon-pin"></i>
                             Ürün Sorgulama</a>
                     </li>
                 </ul>
             </li>
             @endif
        @if(Auth::user()->grup_id <5 || Auth::user()->grup_id==19)
             @if(Request::segment(1)=='abone') <li class="active"> @else <li> @endif
                 <a href="javascript:">
                     <i class="icon-speech"></i>
                     <span class="title">Abone</span>
                     <span class="arrow @if(Request::segment(1)=='abone') open @endif"></span>
                 </a>
                 <ul class="sub-menu">
                     @if(Request::segment(1)=='abone' && (Request::segment(2)=='sayackayit' || Request::segment(2)=='sayackayitekle' || Request::segment(2)=='sayackayitduzenle')) <li class="active"> @else <li> @endif
                         <a href="{{ URL::to('abone/sayackayit') }}">
                             <i class="icon-pencil"></i>
                             Sayaç Kayıt</a>
                     </li>
                     @if(Request::segment(1)=='abone' && (Request::segment(2)=='arizakayit')) <li class="active"> @else <li> @endif
                         <a href="{{ URL::to('abone/arizakayit') }}">
                             <i class="icon-wrench"></i>
                             Arıza Kayıt</a>
                     </li>
                     @if(Request::segment(2)=='musterionay') <li class="active"> @else <li> @endif
                         <a href="{{ URL::to('abone/musterionay') }}">
                             <i class="icon-like"></i>
                             Onay Bilgisi</a>
                     </li>
                 </ul>
             </li>
        @endif
        @if(Auth::user()->grup_id !=10 && Auth::user()->grup_id !=14 && Auth::user()->grup_id !=15 && Auth::user()->grup_id !=18)
            @if(Request::segment(1)=='servistakip') <li class="active"> @else <li> @endif 
                <a href="javascript:">
                    <i class="icon-check"></i>
                    <span class="title">Servis Takip</span>
                    <span class="arrow @if(Request::segment(1)=='servistakip') open @endif"></span>
                </a>
                <ul class="sub-menu">
                    @if(Auth::user()->grup_id!=16 )
                        @if(Request::segment(2)=='servistakipkayit') <li class="active"> @else <li> @endif
                                <a href="{{ URL::to('servistakip/servistakipkayit') }}">
                                <i class="icon-magnifier"></i>
                                Sayaç Servis Bilgileri</a>
                        </li>
                    @endif
                    @if(Request::segment(2)=='hatirlatmalar') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('servistakip/hatirlatmalar') }}">
                            <i class="icon-info"></i>
                            Hatırlatmalar</a>
                    </li>
                    @if(Auth::user()->grup_id!=16 )
                        @if(Request::segment(2)=='bildirimler') <li class="active"> @else <li> @endif
                                <a href="{{ URL::to('servistakip/bildirimler') }}">
                                <i class="icon-info"></i>
                                Bildirimler</a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        @if((Auth::user()->grup_id !=10 && Auth::user()->grup_id<14) || Auth::user()->grup_id==17)
            @if(Request::segment(1)=='ucretlendirme') <li class="active"> @else <li> @endif
                <a href="javascript:">
                    <i class="icon-wallet"></i>
                    <span class="title">Ücretlendirme</span>
                    <span class="arrow @if(Request::segment(1)=='ucretlendirme') open @endif"></span>
                </a>
                <ul class="sub-menu">
                    @if(Request::segment(2)=='ucretlendirmekayit') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('ucretlendirme/ucretlendirmekayit') }}">
                            <i class="icon-clock"></i>
                            Bekleyenler</a>
                    </li>
                    @if(Request::segment(2)=='ucretlendirilenler') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('ucretlendirme/ucretlendirilenler') }}">
                            <i class="icon-tag"></i>
                            Ücretlendirilenler</a>
                    </li>
                @if(Auth::user()->grup_id!=17)
                    @if(Request::segment(2)=='onaylananlar') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('ucretlendirme/onaylananlar') }}">
                            <i class="icon-check"></i>
                            Onaylananlar</a>
                    </li>
                    @if(Request::segment(2)=='reddedilenler') <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('ucretlendirme/reddedilenler') }}">
                            <i class="icon-close"></i>
                            Reddedilenler</a>
                    </li>
                @endif
                </ul>
            </li>
        @endif
        @if(Auth::user()->grup_id <6 || Auth::user()->grup_id==9 || Auth::user()->grup_id==13)
            @if(Request::segment(1)=='kalibrasyon') <li class="active"> @else <li> @endif
                <a href="javascript:">
                    <i class="icon-speedometer"></i>
                    <span class="title">Kalibrasyon</span>
                    <span class="arrow @if(Request::segment(1)=='kalibrasyon') open @endif"></span>
                </a>
                <ul class="sub-menu">
                    @if(Request::segment(1)=='kalibrasyon' && (Request::segment(2)=='kalibrasyon' || Request::segment(2)=='kalibrasyondetay')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('kalibrasyon/kalibrasyon') }}">
                            <i class="icon-target"></i>
                            Kalibrasyon Bilgileri</a>
                    </li>
                </ul>
            </li>
        @endif
        @if(Auth::user()->grup_id !=10 && Auth::user()->grup_id!=13 && Auth::user()->grup_id!=6 && Auth::user()->grup_id!=14 && Auth::user()->grup_id!=15 && Auth::user()->grup_id!=18 && Auth::user()->grup_id!=19)
            @if(Request::segment(1)=='depo') <li class="active"> @else <li> @endif
                <a href="javascript:">
                    <i class="icon-like"></i>
                    <span class="title">Depo</span>
                    <span class="arrow @if(Request::segment(1)=='depo') open @endif"></span>
                </a>
                <ul class="sub-menu">
                    @if(Request::segment(2)=='depogelen' || Request::segment(2)=='depogelenduzenle') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('depo/depogelen') }}">
                            <i class="icon-action-redo"></i>
                            Gelen Sayaçlar</a>
                    </li>
                @if(Auth::user()->grup_id!=16 && Auth::user()->grup_id!=17)
                    @if(Request::segment(2)=='depoteslim') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('depo/depoteslim') }}">
                            <i class="icon-action-undo"></i>
                            Teslimat Bilgisi</a>
                    </li>
                @endif
                @if(Auth::user()->grup_id<5 || Auth::user()->grup_id==17)
                    @if(Request::segment(2)=='depolararasi') <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('depo/depolararasi') }}">
                            <i class="icon-directions "></i>
                            Depolar Arası Transfer</a>
                    </li>
                @endif
                @if(Auth::user()->grup_id!=16)
                    @if(Request::segment(2)=='hurda') <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('depo/hurda') }}">
                            <i class="icon-trash"></i>
                            Hurda Sayaçlar</a>
                    </li>
                @endif
                </ul>
            </li>
        @endif
        @if(Request::segment(1)=='profil') <li class="active"> @else <li> @endif
                <a href="{{ URL::to('profil') }}">
                <i class="icon-user"></i>
                <span class="title">Kullanıcı Profili</span>
            </a>
        </li>
        @if(Auth::user()->grup_id <5)
            @if(Request::segment(1)=='kullanicilar') <li class="active"> @else <li> @endif
                    <a href="javascript:">
                        <i class="icon-users"></i>
                        <span class="title">Kullanıcılar</span>
                        <span class="arrow @if(Request::segment(1)=='kullanicilar') open @endif"></span>
                    </a>
                <ul class="sub-menu">
                    @if(Request::segment(2)=='kullanicilistesi') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('kullanicilar/kullanicilistesi') }}">
                            <i class="icon-users"></i>
                            Kullanıcı Listesi</a>
                    </li>
                </ul>
            </li>
        @endif
        @if(Auth::user()->grup_id <=10 || Auth::user()->grup_id ==17 )
            @if(Request::segment(1)=='rapor') <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('rapor') }}">
                    <i class="icon-list"></i>
                    <span class="title">Rapor</span>
                </a>
            </li>
        @endif
        @if(Auth::user()->grup_id <6 || Auth::user()->grup_id ==7)
            @if(Request::segment(1)=='sudatabase') <li class="active"> @else <li> @endif
                <a href="javascript:">
                    <i class="icon-lock"></i>
                    <span class="title">Su Veritabanı</span>
                    <span class="arrow @if(Request::segment(1)=='sudatabase') open @endif"></span>
                </a>
                <ul class="sub-menu">
                    @if(Request::segment(1)=='sudatabase' && (Request::segment(2)=='sayactip' || Request::segment(2)=='sayactipekle' || Request::segment(2)=='sayactipduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('sudatabase/sayactip') }}">
                            Sayaç Tipleri</a>
                    </li>
                    @if(Request::segment(1)=='sudatabase' && (Request::segment(2)=='sayacadi' || Request::segment(2)=='sayactipekle' || Request::segment(2)=='sayactipduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('sudatabase/sayacadi') }}">
                            Sayaç Adları</a>
                    </li>
                    @if(Request::segment(1)=='sudatabase' && (Request::segment(2)=='sayaclar' || Request::segment(2)=='sayacekle' || Request::segment(2)=='sayacduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('sudatabase/sayaclar') }}">
                            Sayaç Listesi</a>
                    </li>
                    @if( Request::segment(1)=='sudatabase' && (Request::segment(2)=='uretimyeri' || Request::segment(2)=='uretimyeriekle' || Request::segment(2)=='uretimyeriduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('sudatabase/uretimyeri') }}">
                            Üretim Yerleri</a>
                    </li>
                    @if(Request::segment(1)=='sudatabase' && (Request::segment(2)=='sayacfiyat' || Request::segment(2)=='fiyatekle' || Request::segment(2)=='fiyatduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('sudatabase/sayacfiyat') }}">
                            Sayaç Fiyatları</a>
                    </li>
                    @if(Request::segment(1)=='sudatabase' && (Request::segment(2)=='sayacparca' || Request::segment(2)=='parcaekle' || Request::segment(2)=='parcaduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('sudatabase/sayacparca') }}">
                            Sayaç Parçaları</a>
                    </li>
                    @if(Request::segment(1)=='sudatabase' && (Request::segment(2)=='sayacgaranti' || Request::segment(2)=='garantiekle' || Request::segment(2)=='garantiduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('sudatabase/sayacgaranti') }}">
                            Sayaç Garanti Süreleri</a>
                    </li>
                    @if(Request::segment(1)=='sudatabase' && (Request::segment(2)=='arizalar' || Request::segment(2)=='arizaekle' || Request::segment(2)=='arizaduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('sudatabase/arizalar') }}">
                            Arıza Nedenleri</a>
                    </li>
                    @if(Request::segment(1)=='sudatabase' && (Request::segment(2)=='yapilanlar' || Request::segment(2)=='yapilanekle' || Request::segment(2)=='yapilanduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('sudatabase/yapilanlar') }}">
                            Yapılan İşlemler</a>
                    </li>
                    @if(Request::segment(1)=='sudatabase' && (Request::segment(2)=='degisenler' || Request::segment(2)=='degisenekle' || Request::segment(2)=='degisenduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('sudatabase/degisenler') }}">
                            Değişen Parçalar</a>
                    </li>
                    @if(Request::segment(1)=='sudatabase' && (Request::segment(2)=='parcaucret' || Request::segment(2)=='ucretekle' || Request::segment(2)=='ucretduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('sudatabase/parcaucret') }}">
                            Parça Ücretleri</a>
                    </li>
                    @if(Request::segment(1)=='sudatabase' && (Request::segment(2)=='uyarilar' || Request::segment(2)=='uyarilar' || Request::segment(2)=='uyariduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('sudatabase/uyarilar') }}">
                            Uyarılar & Sonuçlar</a>
                    </li>
                    @if(Request::segment(1)=='sudatabase' && (Request::segment(2)=='hurdaneden' || Request::segment(2)=='hurdanedeniekle' || Request::segment(2)=='hurdanedeniduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('sudatabase/hurdaneden') }}">
                            Hurda Nedenleri</a>
                    </li>
                    @if(Request::segment(1)=='sudatabase' && (Request::segment(2)=='stokdurum' || Request::segment(2)=='stokdurumekle' || Request::segment(2)=='stokdurumduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('sudatabase/stokdurum') }}">
                            Stok Durumu</a>
                    </li>
                    {{--@if(Request::segment(1)=='sudatabase' && (Request::segment(2)=='stokgirisi' || Request::segment(2)=='stokhareketekle' || Request::segment(2)=='stokhareketduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('sudatabase/stokgirisi') }}">
                            Stok Hareketleri</a>
                    </li>--}}
                    @if(Request::segment(1)=='sudatabase' && (Request::segment(2)=='yetkilikisi' || Request::segment(2)=='yetkiliekle' || Request::segment(2)=='yetkiliduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('sudatabase/yetkilikisi') }}">
                        Netsis Cari Yetkili Kişiler</a>
                    </li>
                </ul>
            </li>
        @endif
        @if(Auth::user()->grup_id <6 || Auth::user()->grup_id ==8)
            @if(Request::segment(1)=='elkdatabase') <li class="active"> @else <li> @endif
                <a href="javascript:">
                    <i class="icon-lock"></i>
                    <span class="title">Elektrik Veritabanı</span>
                    <span class="arrow @if(Request::segment(1)=='elkdatabase') open @endif"></span>
                </a>
                <ul class="sub-menu">
                    @if(Request::segment(1)=='elkdatabase' && (Request::segment(2)=='sayactip' || Request::segment(2)=='sayactipekle' || Request::segment(2)=='sayactipduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('elkdatabase/sayactip') }}">
                            Sayaç Tipleri</a>
                    </li>
                    @if(Request::segment(1)=='elkdatabase' && (Request::segment(2)=='sayacadi' || Request::segment(2)=='sayactipekle' || Request::segment(2)=='sayactipduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('elkdatabase/sayacadi') }}">
                            Sayaç Adları</a>
                    </li>
                    @if(Request::segment(1)=='elkdatabase' && (Request::segment(2)=='sayaclar' || Request::segment(2)=='sayacekle' || Request::segment(2)=='sayacduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('elkdatabase/sayaclar') }}">
                            Sayaç Listesi</a>
                    </li>
                    @if( Request::segment(1)=='elkdatabase' && (Request::segment(2)=='uretimyeri' || Request::segment(2)=='uretimyeriekle' || Request::segment(2)=='uretimyeriduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('elkdatabase/uretimyeri') }}">
                            Üretim Yerleri</a>
                    </li>
                    @if(Request::segment(1)=='elkdatabase' && (Request::segment(2)=='sayacfiyat' || Request::segment(2)=='fiyatekle' || Request::segment(2)=='fiyatduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('elkdatabase/sayacfiyat') }}">
                            Sayaç Fiyatları</a>
                    </li>
                    @if(Request::segment(1)=='elkdatabase' && (Request::segment(2)=='sayacparca' || Request::segment(2)=='parcaekle' || Request::segment(2)=='parcaduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('elkdatabase/sayacparca') }}">
                            Sayaç Parçaları</a>
                    </li>
                    @if(Request::segment(1)=='elkdatabase' && (Request::segment(2)=='sayacgaranti' || Request::segment(2)=='garantiekle' || Request::segment(2)=='garantiduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('elkdatabase/sayacgaranti') }}">
                            Sayaç Garanti Süreleri</a>
                    </li>
                    @if(Request::segment(1)=='elkdatabase' && (Request::segment(2)=='arizalar' || Request::segment(2)=='arizaekle' || Request::segment(2)=='arizaduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('elkdatabase/arizalar') }}">
                            Arıza Nedenleri</a>
                    </li>
                    @if(Request::segment(1)=='elkdatabase' && (Request::segment(2)=='yapilanlar' || Request::segment(2)=='yapilanekle' || Request::segment(2)=='yapilanduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('elkdatabase/yapilanlar') }}">
                            Yapılan İşlemler</a>
                    </li>
                    @if(Request::segment(1)=='elkdatabase' && (Request::segment(2)=='degisenler' || Request::segment(2)=='degisenekle' || Request::segment(2)=='degisenduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('elkdatabase/degisenler') }}">
                            Değişen Parçalar</a>
                    </li>
                    @if(Request::segment(1)=='elkdatabase' && (Request::segment(2)=='parcaucret' || Request::segment(2)=='ucretekle' || Request::segment(2)=='ucretduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('elkdatabase/parcaucret') }}">
                            Parça Ücretleri</a>
                    </li>
                    @if(Request::segment(1)=='elkdatabase' && (Request::segment(2)=='uyarilar' || Request::segment(2)=='uyarilar' || Request::segment(2)=='uyariduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('elkdatabase/uyarilar') }}">
                            Uyarılar & Sonuçlar</a>
                    </li>
                    @if(Request::segment(1)=='elkdatabase' && (Request::segment(2)=='hurdaneden' || Request::segment(2)=='hurdanedeniekle' || Request::segment(2)=='hurdanedeniduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('elkdatabase/hurdaneden') }}">
                            Hurda Nedenleri</a>
                    </li>
                    @if(Request::segment(1)=='elkdatabase' && (Request::segment(2)=='stokdurum' || Request::segment(2)=='stokparcaekle' || Request::segment(2)=='stokparcaduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('elkdatabase/stokdurum') }}">
                            Stok Durumu</a>
                    </li>
                    {{--@if(Request::segment(1)=='elkdatabase' && (Request::segment(2)=='stokgirisi' || Request::segment(2)=='stokekle' || Request::segment(2)=='stokduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('elkdatabase/stokgirisi') }}">
                            Stok Hareketleri</a>
                    </li>--}}
                    @if(Request::segment(1)=='elkdatabase' && (Request::segment(2)=='yetkilikisi' || Request::segment(2)=='yetkiliekle' || Request::segment(2)=='yetkiliduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('elkdatabase/yetkilikisi') }}">
                        Netsis Cari Yetkili Kişiler</a>
                    </li>
                </ul>
            </li>
        @endif
        @if(Auth::user()->grup_id <6 || Auth::user()->grup_id ==7)
            @if(Request::segment(1)=='gazdatabase') <li class="active"> @else <li> @endif
            <a href="javascript:">
                <i class="icon-lock"></i>
                <span class="title">Gaz Veritabanı</span>
                <span class="arrow @if(Request::segment(1)=='gazdatabase') open @endif"></span>
            </a>
            <ul class="sub-menu">
                @if(Request::segment(1)=='gazdatabase' && (Request::segment(2)=='sayactip' || Request::segment(2)=='sayactipekle' || Request::segment(2)=='sayactipduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('gazdatabase/sayactip') }}">
                        Sayaç Tipleri</a>
                </li>
                @if(Request::segment(1)=='gazdatabase' && (Request::segment(2)=='sayacadi' || Request::segment(2)=='sayactipekle' || Request::segment(2)=='sayactipduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('gazdatabase/sayacadi') }}">
                        Sayaç Adları</a>
                </li>
                @if(Request::segment(1)=='gazdatabase' && (Request::segment(2)=='sayaclar' || Request::segment(2)=='sayacekle' || Request::segment(2)=='sayacduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('gazdatabase/sayaclar') }}">
                        Sayaç Listesi</a>
                </li>
                @if( Request::segment(1)=='gazdatabase' && (Request::segment(2)=='uretimyeri' || Request::segment(2)=='uretimyeriekle' || Request::segment(2)=='uretimyeriduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('gazdatabase/uretimyeri') }}">
                        Üretim Yerleri</a>
                </li>
                @if(Request::segment(1)=='gazdatabase' && (Request::segment(2)=='sayacfiyat' || Request::segment(2)=='fiyatekle' || Request::segment(2)=='fiyatduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('gazdatabase/sayacfiyat') }}">
                        Sayaç Fiyatları</a>
                </li>
                @if(Request::segment(1)=='gazdatabase' && (Request::segment(2)=='sayacparca' || Request::segment(2)=='parcaekle' || Request::segment(2)=='parcaduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('gazdatabase/sayacparca') }}">
                        Sayaç Parçaları</a>
                </li>
                @if(Request::segment(1)=='gazdatabase' && (Request::segment(2)=='sayacgaranti' || Request::segment(2)=='garantiekle' || Request::segment(2)=='garantiduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('gazdatabase/sayacgaranti') }}">
                        Sayaç Garanti Süreleri</a>
                </li>
                @if(Request::segment(1)=='gazdatabase' && (Request::segment(2)=='arizalar' || Request::segment(2)=='arizaekle' || Request::segment(2)=='arizaduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('gazdatabase/arizalar') }}">
                        Arıza Nedenleri</a>
                </li>
                @if(Request::segment(1)=='gazdatabase' && (Request::segment(2)=='yapilanlar' || Request::segment(2)=='yapilanekle' || Request::segment(2)=='yapilanduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('gazdatabase/yapilanlar') }}">
                        Yapılan İşlemler</a>
                </li>
                @if(Request::segment(1)=='gazdatabase' && (Request::segment(2)=='degisenler' || Request::segment(2)=='degisenekle' || Request::segment(2)=='degisenduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('gazdatabase/degisenler') }}">
                        Değişen Parçalar</a>
                </li>
                @if(Request::segment(1)=='gazdatabase' && (Request::segment(2)=='uyarilar' || Request::segment(2)=='uyarilar' || Request::segment(2)=='uyariduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('gazdatabase/uyarilar') }}">
                        Uyarılar & Sonuçlar</a>
                </li>
                @if(Request::segment(1)=='gazdatabase' && (Request::segment(2)=='parcaucret' || Request::segment(2)=='ucretekle' || Request::segment(2)=='ucretduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('gazdatabase/parcaucret') }}">
                        Parça Ücretleri</a>
                </li>
                @if(Request::segment(1)=='gazdatabase' && (Request::segment(2)=='hurdaneden' || Request::segment(2)=='hurdanedeniekle' || Request::segment(2)=='hurdanedeniduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('gazdatabase/hurdaneden') }}">
                        Hurda Nedenleri</a>
                </li>
                @if(Request::segment(1)=='gazdatabase' && (Request::segment(2)=='stokdurum' || Request::segment(2)=='stokparcaekle' || Request::segment(2)=='stokparcaduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('gazdatabase/stokdurum') }}">
                        Stok Durumu</a>
                </li>
                {{--@if(Request::segment(1)=='gazdatabase' && (Request::segment(2)=='stokgirisi' || Request::segment(2)=='stokekle' || Request::segment(2)=='stokduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('gazdatabase/stokgirisi') }}">
                        Stok Hareketleri</a>
                </li>--}}
                @if(Request::segment(1)=='gazdatabase' && (Request::segment(2)=='yetkilikisi' || Request::segment(2)=='yetkiliekle' || Request::segment(2)=='yetkiliduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('gazdatabase/yetkilikisi') }}">
                    Netsis Cari Yetkili Kişiler</a>
                </li>
            </ul>
        </li>
        @endif
        @if(Auth::user()->grup_id <6 || Auth::user()->grup_id ==7)
            @if(Request::segment(1)=='isidatabase') <li class="active"> @else <li> @endif
                <a href="javascript:">
                    <i class="icon-lock"></i>
                    <span class="title">Isı Veritabanı</span>
                    <span class="arrow @if(Request::segment(1)=='isidatabase') open @endif"></span>
                </a>
                <ul class="sub-menu">
                    @if(Request::segment(1)=='isidatabase' && (Request::segment(2)=='sayactip' || Request::segment(2)=='sayactipekle' || Request::segment(2)=='sayactipduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('isidatabase/sayactip') }}">
                            Sayaç Tipleri</a>
                    </li>
                    @if(Request::segment(1)=='isidatabase' && (Request::segment(2)=='sayacadi' || Request::segment(2)=='sayactipekle' || Request::segment(2)=='sayactipduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('isidatabase/sayacadi') }}">
                            Sayaç Adları</a>
                    </li>
                    @if(Request::segment(1)=='isidatabase' && (Request::segment(2)=='sayaclar' || Request::segment(2)=='sayacekle' || Request::segment(2)=='sayacduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('isidatabase/sayaclar') }}">
                            Sayaç Listesi</a>
                    </li>
                    @if( Request::segment(1)=='isidatabase' && (Request::segment(2)=='uretimyeri' || Request::segment(2)=='uretimyeriekle' || Request::segment(2)=='uretimyeriduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('isidatabase/uretimyeri') }}">
                            Üretim Yerleri</a>
                    </li>
                    @if(Request::segment(1)=='isidatabase' && (Request::segment(2)=='sayacfiyat' || Request::segment(2)=='fiyatekle' || Request::segment(2)=='fiyatduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('isidatabase/sayacfiyat') }}">
                            Sayaç Fiyatları</a>
                    </li>
                    @if(Request::segment(1)=='isidatabase' && (Request::segment(2)=='sayacparca' || Request::segment(2)=='parcaekle' || Request::segment(2)=='parcaduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('isidatabase/sayacparca') }}">
                            Sayaç Parçaları</a>
                    </li>
                    @if(Request::segment(1)=='isidatabase' && (Request::segment(2)=='sayacgaranti' || Request::segment(2)=='garantiekle' || Request::segment(2)=='garantiduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('isidatabase/sayacgaranti') }}">
                            Sayaç Garanti Süreleri</a>
                    </li>
                    @if(Request::segment(1)=='isidatabase' && (Request::segment(2)=='arizalar' || Request::segment(2)=='arizaekle' || Request::segment(2)=='arizaduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('isidatabase/arizalar') }}">
                            Arıza Nedenleri</a>
                    </li>
                    @if(Request::segment(1)=='isidatabase' && (Request::segment(2)=='yapilanlar' || Request::segment(2)=='yapilanekle' || Request::segment(2)=='yapilanduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('isidatabase/yapilanlar') }}">
                            Yapılan İşlemler</a>
                    </li>
                    @if(Request::segment(1)=='isidatabase' && (Request::segment(2)=='degisenler' || Request::segment(2)=='degisenekle' || Request::segment(2)=='degisenduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('isidatabase/degisenler') }}">
                            Değişen Parçalar</a>
                    </li>
                    @if(Request::segment(1)=='isidatabase' && (Request::segment(2)=='parcaucret' || Request::segment(2)=='ucretekle' || Request::segment(2)=='ucretduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('isidatabase/parcaucret') }}">
                            Parça Ücretleri</a>
                    </li>
                    @if(Request::segment(1)=='isidatabase' && (Request::segment(2)=='uyarilar' || Request::segment(2)=='uyarilar' || Request::segment(2)=='uyariduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('isidatabase/uyarilar') }}">
                            Uyarılar & Sonuçlar</a>
                    </li>
                    @if(Request::segment(1)=='isidatabase' && (Request::segment(2)=='hurdaneden' || Request::segment(2)=='hurdanedeniekle' || Request::segment(2)=='hurdanedeniduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('isidatabase/hurdaneden') }}">
                            Hurda Nedenleri</a>
                    </li>
                    @if(Request::segment(1)=='isidatabase' && (Request::segment(2)=='stokdurum' || Request::segment(2)=='stokparcaekle' || Request::segment(2)=='stokparcaduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('isidatabase/stokdurum') }}">
                            Stok Durumu</a>
                    </li>
                    {{--@if(Request::segment(1)=='isidatabase' && (Request::segment(2)=='stokgirisi' || Request::segment(2)=='stokekle' || Request::segment(2)=='stokduzenle')) <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('isidatabase/stokgirisi') }}">
                            Stok Hareketleri</a>
                    </li>--}}
                    @if(Request::segment(1)=='isidatabase' && (Request::segment(2)=='yetkilikisi' || Request::segment(2)=='yetkiliekle' || Request::segment(2)=='yetkiliduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('isidatabase/yetkilikisi') }}">
                            Netsis Cari Yetkili Kişiler</a>
                    </li>
                </ul>
            </li>
        @endif
        @if(Auth::user()->grup_id <6 || Auth::user()->grup_id ==9 )
            @if(Request::segment(1)=='mekanikdatabase') <li class="active"> @else <li> @endif
            <a href="javascript:">
                <i class="icon-lock"></i>
                <span class="title">Mekanik Veritabanı</span>
                <span class="arrow @if(Request::segment(1)=='mekanikdatabase') open @endif"></span>
            </a>
            <ul class="sub-menu">
                @if(Request::segment(1)=='mekanikdatabase' && (Request::segment(2)=='sayactip' || Request::segment(2)=='sayactipekle' || Request::segment(2)=='sayactipduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('mekanikdatabase/sayactip') }}">
                        Sayaç Tipleri</a>
                </li>
                @if(Request::segment(1)=='mekanikdatabase' && (Request::segment(2)=='sayacadi' || Request::segment(2)=='sayactipekle' || Request::segment(2)=='sayactipduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('mekanikdatabase/sayacadi') }}">
                        Sayaç Adları</a>
                </li>
                @if(Request::segment(1)=='mekanikdatabase' && (Request::segment(2)=='sayaclar' || Request::segment(2)=='sayacekle' || Request::segment(2)=='sayacduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('mekanikdatabase/sayaclar') }}">
                        Sayaç Listesi</a>
                </li>
                @if( Request::segment(1)=='mekanikdatabase' && (Request::segment(2)=='uretimyeri' || Request::segment(2)=='uretimyeriekle' || Request::segment(2)=='uretimyeriduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('mekanikdatabase/uretimyeri') }}">
                        Üretim Yerleri</a>
                </li>
                @if(Request::segment(1)=='mekanikdatabase' && (Request::segment(2)=='sayacfiyat' || Request::segment(2)=='fiyatekle' || Request::segment(2)=='fiyatduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('mekanikdatabase/sayacfiyat') }}">
                        Sayaç Fiyatları</a>
                </li>
                @if(Request::segment(1)=='mekanikdatabase' && (Request::segment(2)=='sayacparca' || Request::segment(2)=='parcaekle' || Request::segment(2)=='parcaduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('mekanikdatabase/sayacparca') }}">
                        Sayaç Parçaları</a>
                </li>
                @if(Request::segment(1)=='mekanikdatabase' && (Request::segment(2)=='sayacgaranti' || Request::segment(2)=='garantiekle' || Request::segment(2)=='garantiduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('mekanikdatabase/sayacgaranti') }}">
                        Sayaç Garanti Süreleri</a>
                </li>
                @if(Request::segment(1)=='mekanikdatabase' && (Request::segment(2)=='arizalar' || Request::segment(2)=='arizaekle' || Request::segment(2)=='arizaduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('mekanikdatabase/arizalar') }}">
                        Arıza Nedenleri</a>
                </li>
                @if(Request::segment(1)=='mekanikdatabase' && (Request::segment(2)=='yapilanlar' || Request::segment(2)=='yapilanekle' || Request::segment(2)=='yapilanduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('mekanikdatabase/yapilanlar') }}">
                        Yapılan İşlemler</a>
                </li>
                @if(Request::segment(1)=='mekanikdatabase' && (Request::segment(2)=='degisenler' || Request::segment(2)=='degisenekle' || Request::segment(2)=='degisenduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('mekanikdatabase/degisenler') }}">
                        Değişen Parçalar</a>
                </li>
                @if(Request::segment(1)=='mekanikdatabase' && (Request::segment(2)=='parcaucret' || Request::segment(2)=='ucretekle' || Request::segment(2)=='ucretduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('mekanikdatabase/parcaucret') }}">
                        Parça Ücretleri</a>
                </li>
                @if(Request::segment(1)=='mekanikdatabase' && (Request::segment(2)=='uyarilar' || Request::segment(2)=='uyarilar' || Request::segment(2)=='uyariduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('mekanikdatabase/uyarilar') }}">
                        Uyarılar & Sonuçlar</a>
                </li>
                @if(Request::segment(1)=='mekanikdatabase' && (Request::segment(2)=='hurdaneden' || Request::segment(2)=='hurdanedeniekle' || Request::segment(2)=='hurdanedeniduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('mekanikdatabase/hurdaneden') }}">
                        Hurda Nedenleri</a>
                </li>
                @if(Request::segment(1)=='mekanikdatabase' && (Request::segment(2)=='stokdurum' || Request::segment(2)=='stokparcaekle' || Request::segment(2)=='stokparcaduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('mekanikdatabase/stokdurum') }}">
                        Stok Durumu</a>
                </li>
                {{--@if(Request::segment(1)=='mekanikdatabase' && (Request::segment(2)=='stokgirisi' || Request::segment(2)=='stokekle' || Request::segment(2)=='stokduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('mekanikdatabase/stokgirisi') }}">
                        Stok Hareketleri</a>
                </li>--}}
                @if(Request::segment(1)=='mekanikdatabase' && (Request::segment(2)=='yetkilikisi' || Request::segment(2)=='yetkiliekle' || Request::segment(2)=='yetkiliduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('mekanikdatabase/yetkilikisi') }}">
                        Netsis Cari Yetkili Kişiler</a>
                </li>
                @if(Request::segment(1)=='mekanikdatabase' && (Request::segment(2)=='istasyon' || Request::segment(2)=='istasyonekle' || Request::segment(2)=='istasyonduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('mekanikdatabase/istasyon') }}">
                        Kalibrasyon İstasyonları</a>
                </li>
            </ul>
        </li>
        @endif
        @if(Auth::user()->grup_id <6)
            @if(Request::segment(1)=='digerdatabase') <li class="active"> @else <li> @endif
                <a href="javascript:">
                    <i class="icon-lock"></i>
                    <span class="title">Veritabanı Diğerleri</span>
                    <span class="arrow @if(Request::segment(1)=='digerdatabase') open @endif"></span>
                </a>
                <ul class="sub-menu">
                    @if(Request::segment(2)=='sayacmarka' || Request::segment(2)=='markaekle' || Request::segment(2)=='markaduzenle') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('digerdatabase/sayacmarka') }}">
                            Sistemdeki Markalar</a>
                    </li>
                    @if(Request::segment(2)=='sayactur' || Request::segment(2)=='turekle' || Request::segment(2)=='turduzenle') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('digerdatabase/sayactur') }}">
                            Servis-Sayaç Türü</a>
                    </li>
                    @if(Request::segment(1)=='digerdatabase' && (Request::segment(2)=='sayaclar' || Request::segment(2)=='sayacduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('digerdatabase/sayaclar') }}">
                            Sayaç Listesi (Türü Belli Olmayanlar)</a>
                    </li>
                    @if(Request::segment(1)=='digerdatabase' && Request::segment(2)=='netsiscari') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('digerdatabase/netsiscari') }}">
                            Netsis Cari İsimleri</a>
                    </li>
                    @if(Request::segment(2)=='cariyer' || Request::segment(2)=='cariyerekle' || Request::segment(2)=='cariyerduzenle') <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('digerdatabase/cariyer') }}">
                            Netsis Cari ve Üretim Yeri Eşleştirme</a>
                    </li>
                    @if(Request::segment(2)=='kasakod' || Request::segment(2)=='kasakodduzenle') <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('digerdatabase/kasakod') }}">
                            Netsis Kasa Bilgileri</a>
                    </li>
                    @if(Request::segment(2)=='parcaucret' || Request::segment(2)=='ucretekle' || Request::segment(2)=='ucretduzenle') <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('digerdatabase/parcaucret') }}">
                            Parça Ücretleri</a>
                    </li>
                    @if(Request::segment(2)=='netsisstokkod') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('digerdatabase/netsisstokkod') }}">
                            Netsis Servis Stok Kodları</a>
                    </li>
                    @if(Request::segment(2)=='servisdurum' || Request::segment(2)=='durumekle' || Request::segment(2)=='durumduzenle') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('digerdatabase/servisdurum') }}">
                            Servis Durumları</a>
                    </li>
                    @if(Request::segment(2)=='servisyetkili') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('digerdatabase/servisyetkili') }}">
                            Servis Yetkili Kişileri</a>
                    </li>
                    @if(Request::segment(2)=='sube' || Request::segment(2)=='subeekle' || Request::segment(2)=='subeduzenle') <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('digerdatabase/sube') }}">
                            Şubeler</a>
                    </li>
                    @if(Request::segment(2)=='subeyetkili' || Request::segment(2)=='subeyetkiliekle' || Request::segment(2)=='subeyetkiliduzenle') <li class="active"> @else <li> @endif
                            <a href="{{ URL::to('digerdatabase/subeyetkili') }}">
                            Şube Yetkili Elemanlar</a>
                    </li>
                    @if(Request::segment(2)=='islem') <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('digerdatabase/islem') }}">
                            Tüm Yapılan İşlemler</a>
                    </li>
                    @if(Request::segment(2)=='islemduzenle') <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('digerdatabase/islemduzenle') }}">
                            İşlem Düzenleme</a>
                    </li>
                </ul>
            </li>
        @endif
        @if(Auth::user()->grup_id <5 || Auth::user()->grup_id ==17)
            @if(Request::segment(1)=='subedatabase') <li class="active"> @else <li> @endif
                <a href="javascript:">
                    <i class="icon-lock"></i>
                    <span class="title">Şube Veritabanı</span>
                    <span class="arrow @if(Request::segment(1)=='subedatabase') open @endif"></span>
                </a>
                <ul class="sub-menu">
                    @if(Request::segment(2)=='urunler' || Request::segment(2)=='urunekle' || Request::segment(2)=='urunduzenle') <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('subedatabase/urunler') }}">
                            Ürün Listesi</a>
                    </li>
                    @if(Request::segment(1)=='subedatabase' && (Request::segment(2)=='sayaclar' || Request::segment(2)=='sayacekle' || Request::segment(2)=='sayacduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('subedatabase/sayaclar') }}">
                            Sayaç Listesi</a>
                    </li>
                    @if(Request::segment(1)=='subedatabase' && Request::segment(2)=='netsiscari') <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('subedatabase/netsiscari') }}">
                            Netsis Cari İsimleri</a>
                    </li>
                    @if(Request::segment(2)=='personel' || Request::segment(2)=='personelekle' || Request::segment(2)=='personelduzenle') <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('subedatabase/personel') }}">
                            Personel Listesi</a>
                    </li>
                    @if(Request::segment(1)=='subedatabase' && (Request::segment(2)=='stokhareket' || Request::segment(2)=='stokhareketekle' || Request::segment(2)=='stokhareketduzenle')) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('subedatabase/stokhareket') }}">
                            Stok Hareketleri</a>
                    </li>
                    @if(Request::segment(2)=='abonebilgi' ) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('subedatabase/abonebilgi') }}">
                            Abone Bilgileri</a>
                    </li>
                    @if(Request::segment(2)=='faturabilgi' ) <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('subedatabase/faturabilgi') }}">
                            Fatura Bilgileri</a>
                    </li>
                    @if(Request::segment(2)=='servisbilgi') <li class="active"> @else <li> @endif
                        <a href="{{ URL::to('subedatabase/servisbilgi') }}">
                            Eski Servis Kayıtları</a>
                    </li>
                </ul>
            </li>
        @endif
        @if(Auth::user()->grup_id <5)
            @if(Request::segment(1)=='destekdatabase') <li class="active"> @else <li> @endif
            <a href="javascript:">
                <i class="icon-lock"></i>
                <span class="title">Y.Destek Veritabanı</span>
                <span class="arrow @if(Request::segment(1)=='destekdatabase') open @endif"></span>
            </a>
            <ul class="sub-menu">
               {{-- @if(Request::segment(1)=='destekdatabase' && (Request::segment(2)=='kategori' || Request::segment(2)=='kategoriekle' || Request::segment(2)=='kategoriduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('destekdatabase/kategori') }}">
                        Kategoriler</a>
                </li>
                @if(Request::segment(1)=='destekdatabase' && (Request::segment(2)=='urun' || Request::segment(2)=='urunekle' || Request::segment(2)=='urunduzenle')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('destekdatabase/urun') }}">
                        Ürünler</a>
                </li>--}}
                @if(Request::segment(1)=='destekdatabase' && (Request::segment(2)=='urunler')) <li class="active"> @else <li> @endif
                    <a href="{{ URL::to('destekdatabase/urunler') }}">
                        Ürünler</a>
                </li>
            </ul>
        </li>
        @endif
        </ul>
        <!-- END SIDEBAR MENU -->
    </div>
</div>
@endif