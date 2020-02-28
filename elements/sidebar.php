
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
                    <li class="start <?=$active['anasayfa']?> <?=$hide['anasayfa']?>">
                            <a href="index.php">
                            <i class="icon-home"></i>
                            <span class="title">Ana Sayfa</span>
                            </a>
                    </li>
                    <li class="<?=$active['suservis']?> <?=$hide['suservis']?>">
                            <a href="javascript:;">
                            <i class="icon-drop"></i>
                            <span class="title">Su Servis</span>
                            <span class="arrow <?=$arrow['suservis']?>"></span>
                            </a>
                            <ul class="sub-menu">
                                    <li class="<?=$active['susayackayit']?>">
                                            <a href="susayackayit.php">
                                            <i class="icon-pencil"></i>
                                            Sayaç Kayıt</a>
                                    </li>
                                    <li class="<?=$active['suarizakayit']?>">
                                            <a href="suarizakayit.php">
                                            <i class="icon-wrench"></i>
                                            Arıza Kayıt</a>
                                    </li>
                            </ul>
                    </li>
                    <li class="<?=$active['elkservis']?> <?=$hide['elkservis']?>">
                            <a href="javascript:;">
                            <i class="icon-energy"></i>
                            <span class="title">Elektrik Servis</span>
                            <span class="arrow <?=$arrow['elkservis']?>"></span>
                            </a>
                            <ul class="sub-menu">
                                    <li class="<?=$active['elksayackayit']?>">
                                            <a href="elksayackayit.php">
                                            <i class="icon-pencil"></i>
                                            Sayaç Kayıt</a>
                                    </li>
                                    <li class="<?=$active['elkarizakayit']?>">
                                            <a href="elkarizakayit.php">
                                            <i class="icon-wrench"></i>
                                            Arıza Kayıt</a>
                                    </li>
                            </ul>
                    </li>
                    <li class="<?=$active['gazservis']?> <?=$hide['gazservis']?>">
                            <a href="javascript:;">
                            <i class="icon-fire"></i>
                            <span class="title">Gaz Servis</span>
                            <span class="arrow <?=$arrow['gazservis']?>"></span>
                            </a>
                            <ul class="sub-menu">
                                    <li class="<?=$active['gazsayackayit']?>">
                                            <a href="gazsayackayit.php">
                                            <i class="icon-pencil"></i>
                                            Sayaç Kayıt</a>
                                    </li>
                                    <li class="<?=$active['gazarizakayit']?>">
                                            <a href="gazarizakayit.php">
                                            <i class="icon-wrench"></i>
                                            Arıza Kayıt</a>
                                    </li>
                            </ul>
                    </li>
                    <li class="<?=$active['isiservis']?> <?=$hide['isiservis']?>">
                            <a href="javascript:;">
                            <i class="icon-pointer"></i>
                            <span class="title">Isı Servis</span>
                            <span class="arrow <?=$arrow['isiservis']?>"></span>
                            </a>
                            <ul class="sub-menu">
                                    <li class="<?=$active['isisayackayit']?>">
                                            <a href="isisayackayit.php">
                                            <i class="icon-pencil"></i>
                                            Sayaç Kayıt</a>
                                    </li>
                                    <li class="<?=$active['isiarizakayit']?>">
                                            <a href="isiarizakayit.php">
                                            <i class="icon-wrench"></i>
                                            Arıza Kayıt</a>
                                    </li>
                            </ul>
                    </li>
                    <li class="<?=$active['istservis']?> <?=$hide['istservis']?>">
                            <a href="javascript:;">
                            <i class="icon-pointer"></i>
                            <span class="title">İstanbul Servis</span>
                            <span class="arrow <?=$arrow['istservis']?>"></span>
                            </a>
                            <ul class="sub-menu">
                                    <li class="<?=$active['istsayackayit']?>">
                                            <a href="istsayackayit.php">
                                            <i class="icon-pencil"></i>
                                            Sayaç Kayıt</a>
                                    </li>
                                    <li class="<?=$active['istarizakayit']?>">
                                            <a href="istarizakayit.php">
                                            <i class="icon-wrench"></i>
                                            Arıza Kayıt</a>
                                    </li>
                            </ul>
                    </li>
                    <li class="<?=$active['boluservis']?> <?=$hide['boluservis']?>">
                            <a href="javascript:;">
                            <i class="icon-pointer"></i>
                            <span class="title">Bolu Servis</span>
                            <span class="arrow <?=$arrow['boluservis']?>"></span>
                            </a>
                            <ul class="sub-menu">
                                    <li class="<?=$active['bolusayackayit']?>">
                                            <a href="bolusayackayit.php">
                                            <i class="icon-pencil"></i>
                                            Sayaç Kayıt</a>
                                    </li>
                                    <li class="<?=$active['boluarizakayit']?>">
                                            <a href="boluarizakayit.php">
                                            <i class="icon-wrench"></i>
                                            Arıza Kayıt</a>
                                    </li>
                            </ul>
                    </li>
                    <li class="<?=$active['edestek']?> <?=$hide['edestek']?>">
                            <a href="javascript:;">
                            <i class="icon-graduation"></i>
                            <span class="title">Eğitim Destek</span>
                            <span class="arrow <?=$arrow['edestek']?>"></span>
                            </a>
                            <ul class="sub-menu">
                                    <li class="<?=$active['edestekkayit']?>">
                                            <a href="edestek.php">
                                            <i class="icon-home"></i>
                                            Ana Sayfa</a>
                                    </li>
                                    <li class="<?=$active['projebilgisi']?>">
                                            <a href="projebilgisi.php">
                                            <i class="icon-docs"></i>
                                            Proje Bilgileri</a>
                                    </li>
                                    <li class="<?=$active['hatacozumleri']?>">
                                            <a href="hatacozumleri.php">
                                            <i class="icon-directions"></i>
                                            Hata Çözümleri</a>
                                    </li>
                                    <li class="<?=$active['islemler']?>">
                                            <a href="islemler.php">
                                            <i class="icon-puzzle"></i>
                                            Düzenli İşlemler</a>
                                    </li>
                                    <li class="<?=$active['personel']?>">
                                            <a href="personel.php">
                                            <i class="icon-users"></i>
                                            Personel Bilgisi</a>
                                    </li>
                            </ul>
                    </li>
                    <li class="<?=$active['servistakip']?> <?=$hide['servistakip']?>">
                            <a href="javascript:;">
                            <i class="icon-check"></i>
                            <span class="title">Servis Takip</span>
                            <span class="arrow <?=$arrow['servistakip']?>"></span>
                            </a>
                            <ul class="sub-menu">
                                    <li class="<?=$active['servistakipkayit']?>">
                                            <a href="servistakip.php">
                                            <i class="icon-magnifier"></i>
                                            Sayaç Servis Bilgileri</a>
                                    </li>
                                    <li class="<?=$active['hatirlatmalar']?>">
                                            <a href="hatirlatmalar.php">
                                            <i class="icon-info"></i>
                                            Hatırlatmalar</a>
                                    </li>
                                    <li class="<?=$active['bildirimler']?>">
                                            <a href="bildirimler.php">
                                            <i class="icon-info"></i>
                                            Bildirimler</a>
                                    </li>
                            </ul>
                    </li>
                    <li class="<?=$active['ucretlendirme']?> <?=$hide['ucretlendirme']?>">
                            <a href="javascript:;">
                            <i class="icon-wallet"></i>
                            <span class="title">Ücretlendirme</span>
                            <span class="arrow <?=$arrow['ucretlendirme']?>"></span>
                            </a>
                            <ul class="sub-menu">
                                    <li class="<?=$active['ucretlendirmekayit']?>">
                                            <a href="ucretlendirme.php">
                                            <i class="icon-clock"></i>
                                            Bekleyenler</a>
                                    </li>
                                    <li class="<?=$active['ucretlendirilenler']?>">
                                            <a href="ucretlendirilenler.php">
                                            <i class="icon-tag"></i>
                                            Ücretlendirilenler</a>
                                    </li>
                                    <li class="<?=$active['onaylananlar']?>">
                                            <a href="onaylananlar.php">
                                            <i class="icon-check"></i>
                                            Onaylananlar</a>
                                    </li>
                            </ul>
                    </li>
                    <li class="<?=$active['depo']?> <?=$hide['depo']?>">
                            <a href="javascript:;">
                            <i class="icon-like"></i>
                            <span class="title">Depo</span>
                            <span class="arrow <?=$arrow['depo']?>"></span>
                            </a>
                            <ul class="sub-menu">
                                    <li class="<?=$active['depogelen']?>">
                                            <a href="depogelen.php">
                                            <i class="icon-action-redo"></i>
                                            Gelen Sayaçlar</a>
                                    </li>
                                    <li class="<?=$active['depoteslim']?>">
                                            <a href="depoteslim.php">
                                            <i class="icon-action-undo"></i>
                                            Teslimat Bilgisi</a>
                                    </li>
                            </ul>
                    </li>
                    <li class="<?=$active['kullanicilar']?> <?=$hide['kullanicilar']?>">
                            <a href="kullanicilar.php">
                            <i class="icon-user"></i>
                            <span class="title">Kullanıcı Profilleri</span>
                            </a>
                    </li>
                    <li class="<?=$active['sudatabase']?> <?=$hide['sudatabase']?>">
                            <a href="javascript:;">
                            <i class="icon-lock"></i>
                            <span class="title">Su Veritabanı</span>
                            <span class="arrow <?=$arrow['sudatabase']?>"></span>
                            </a>
                            <ul class="sub-menu">
                                    <li class="<?=$active['sayactipsu']?>">
                                            <a href="sayactipsu.php">
                                            Sayaç Tipleri</a>
                                    </li>
                                    <li class="<?=$active['sayacadisu']?>">
                                            <a href="sayacadisu.php">
                                            Sayaç Adları</a>
                                    </li>
                                    <li class="<?=$active['sayacfiyatsu']?>">
                                            <a href="sayacfiyatsu.php">
                                            Sayaç Fiyatları</a>
                                    </li>
                                    <li class="<?=$active['sayacparcasu']?>">
                                            <a href="sayacparcasu.php">
                                            Sayaç Parçaları</a>
                                    </li>
                                    <li class="<?=$active['sayacgarantisu']?>">
                                            <a href="sayacgarantisu.php">
                                            Sayaç Garanti Süreleri</a>
                                    </li>
                                    <li class="<?=$active['uretimyerisu']?>">
                                            <a href="uretimyerisu.php">
                                            Üretim Yerleri</a>
                                    </li>
                                    <li class="<?=$active['arizalarsu']?>">
                                            <a href="arizalarsu.php">
                                            Arıza Nedenleri</a>
                                    </li>
                                    <li class="<?=$active['yapilanlarsu']?>">
                                            <a href="yapilanlarsu.php">
                                            Yapılan İşlemler</a>
                                    </li>
                                    <li class="<?=$active['degisenlersu']?>">
                                            <a href="degisenlersu.php">
                                            Değişen Parçalar</a>
                                    </li>
                                    <li class="<?=$active['parcaucretsu']?>">
                                            <a href="parcaucretsu.php">
                                            Parça Ücretleri</a>
                                    </li>
                                    <li class="<?=$active['stokdurumsu']?>">
                                            <a href="stokdurumsu.php">
                                            Stok Durumu</a>
                                    </li>
                                    <li class="<?=$active['stokgirisisu']?>">
                                            <a href="stokgirisisu.php">
                                            Stok Girişi Manuel</a>
                                    </li>
                            </ul>
                    </li>
                    <li class="<?=$active['elkdatabase']?> <?=$hide['elkdatabase']?>">
                            <a href="javascript:;">
                            <i class="icon-lock"></i>
                            <span class="title">Elektrik Veritabanı</span>
                            <span class="arrow <?=$arrow['elkdatabase']?>"></span>
                            </a>
                            <ul class="sub-menu">
                                    <li class="<?=$active['sayactipelk']?>">
                                            <a href="sayactipelk.php">
                                            Sayaç Tipleri</a>
                                    </li>
                                    <li class="<?=$active['sayacadielk']?>">
                                            <a href="sayacadielk.php">
                                            Sayaç Adları</a>
                                    </li>
                                    <li class="<?=$active['sayacfiyatelk']?>">
                                            <a href="sayacfiyatelk.php">
                                            Sayaç Fiyatları</a>
                                    </li>
                                    <li class="<?=$active['sayacparcaelk']?>">
                                            <a href="sayacparcaelk.php">
                                            Sayaç Parçaları</a>
                                    </li>
                                    <li class="<?=$active['sayacgarantielk']?>">
                                            <a href="sayacgarantielk.php">
                                            Sayaç Garanti Süreleri</a>
                                    </li>
                                    <li class="<?=$active['uretimyerielk']?>">
                                            <a href="uretimyerielk.php">
                                            Üretim Yerleri</a>
                                    </li>
                                    <li class="<?=$active['arizalarelk']?>">
                                            <a href="arizalarelk.php">
                                            Arıza Nedenleri</a>
                                    </li>
                                    <li class="<?=$active['yapilanlarelk']?>">
                                            <a href="yapilanlarelk.php">
                                            Yapılan İşlemler</a>
                                    </li>
                                    <li class="<?=$active['degisenlerelk']?>">
                                            <a href="degisenlerelk.php">
                                            Değişen Parçalar</a>
                                    </li>
                                    <li class="<?=$active['parcaucretelk']?>">
                                            <a href="parcaucretelk.php">
                                            Parça Ücretleri</a>
                                    </li>
                                    <li class="<?=$active['stokdurumelk']?>">
                                            <a href="stokdurumelk.php">
                                            Stok Durumu</a>
                                    </li>
                                    <li class="<?=$active['stokgirisielk']?>">
                                            <a href="stokgirisielk.php">
                                            Stok Girişi Manuel</a>
                                    </li>
                            </ul>
                    </li>
                    <li class="<?=$active['gazdatabase']?> <?=$hide['gazdatabase']?>">
                            <a href="javascript:;">
                            <i class="icon-lock"></i>
                            <span class="title">Gaz Veritabanı</span>
                            <span class="arrow <?=$arrow['gazdatabase']?>"></span>
                            </a>
                            <ul class="sub-menu">
                                    <li class="<?=$active['sayactipgaz']?>">
                                            <a href="sayactipgaz.php">
                                            Sayaç Tipleri</a>
                                    </li>
                                    <li class="<?=$active['sayacadigaz']?>">
                                            <a href="sayacadigaz.php">
                                            Sayaç Adları</a>
                                    </li>
                                    <li class="<?=$active['sayacfiyatgaz']?>">
                                            <a href="sayacfiyatgaz.php">
                                            Sayaç Fiyatları</a>
                                    </li>
                                    <li class="<?=$active['sayacparcagaz']?>">
                                            <a href="sayacparcagaz.php">
                                            Sayaç Parçaları</a>
                                    </li>
                                    <li class="<?=$active['sayacgarantigaz']?>">
                                            <a href="sayacgarantigaz.php">
                                            Sayaç Garanti Süreleri</a>
                                    </li>
                                    <li class="<?=$active['uretimyerigaz']?>">
                                            <a href="uretimyerigaz.php">
                                            Üretim Yerleri</a>
                                    </li>
                                    <li class="<?=$active['arizalargaz']?>">
                                            <a href="arizalargaz.php">
                                            Arıza Nedenleri</a>
                                    </li>
                                    <li class="<?=$active['yapilanlargaz']?>">
                                            <a href="yapilanlargaz.php">
                                            Yapılan İşlemler</a>
                                    </li>
                                    <li class="<?=$active['degisenlergaz']?>">
                                            <a href="degisenlergaz.php">
                                            Değişen Parçalar</a>
                                    </li>
                                    <li class="<?=$active['parcaucretgaz']?>">
                                            <a href="parcaucretgaz.php">
                                            Parça Ücretleri</a>
                                    </li>
                                    <li class="<?=$active['stokdurumgaz']?>">
                                            <a href="stokdurumgaz.php">
                                            Stok Durumu</a>
                                    </li>
                                    <li class="<?=$active['stokgirisigaz']?>">
                                            <a href="stokgirisigaz.php">
                                            Stok Girişi Manuel</a>
                                    </li>
                            </ul>
                    </li>
                    <li class="<?=$active['isidatabase']?> <?=$hide['isidatabase']?>">
                            <a href="javascript:;">
                            <i class="icon-lock"></i>
                            <span class="title">Isı Veritabanı</span>
                            <span class="arrow <?=$arrow['isidatabase']?>"></span>
                            </a>
                            <ul class="sub-menu">
                                    <li class="<?=$active['sayactipisi']?>">
                                            <a href="sayactipisi.php">
                                            Sayaç Tipleri</a>
                                    </li>
                                    <li class="<?=$active['sayacadiisi']?>">
                                            <a href="sayacadiisi.php">
                                            Sayaç Adları</a>
                                    </li>
                                    <li class="<?=$active['sayacfiyatisi']?>">
                                            <a href="sayacfiyatisi.php">
                                            Sayaç Fiyatları</a>
                                    </li>
                                    <li class="<?=$active['sayacparcaisi']?>">
                                            <a href="sayacparcaisi.php">
                                            Sayaç Parçaları</a>
                                    </li>
                                    <li class="<?=$active['sayacgarantiisi']?>">
                                            <a href="sayacgarantiisi.php">
                                            Sayaç Garanti Süreleri</a>
                                    </li>
                                    <li class="<?=$active['uretimyeriisi']?>">
                                            <a href="uretimyeriisi.php">
                                            Üretim Yerleri</a>
                                    </li>
                                    <li class="<?=$active['arizalarisi']?>">
                                            <a href="arizalarisi.php">
                                            Arıza Nedenleri</a>
                                    </li>
                                    <li class="<?=$active['yapilanlarisi']?>">
                                            <a href="yapilanlarisi.php">
                                            Yapılan İşlemler</a>
                                    </li>
                                    <li class="<?=$active['degisenlerisi']?>">
                                            <a href="degisenlerisi.php">
                                            Değişen Parçalar</a>
                                    </li>
                                    <li class="<?=$active['parcaucretisi']?>">
                                            <a href="parcaucretisi.php">
                                            Parça Ücretleri</a>
                                    </li>
                                    <li class="<?=$active['stokdurumisi']?>">
                                            <a href="stokdurumisi.php">
                                            Stok Durumu</a>
                                    </li>
                                    <li class="<?=$active['stokgirisiisi']?>">
                                            <a href="stokgirisiisi.php">
                                            Stok Girişi Manuel</a>
                                    </li>
                            </ul>
                    </li>
                    <li class="<?=$active['digerdatabase']?> <?=$hide['digerdatabase']?>">
                            <a href="javascript:;">
                            <i class="icon-lock"></i>
                            <span class="title">Veritabanı Diğerleri</span>
                            <span class="arrow <?=$arrow['digerdatabase']?>"></span>
                            </a>
                            <ul class="sub-menu">
                                    <li class="<?=$active['sayacmarka']?>">
                                            <a href="sayacmarka.php">
                                            Sistemdeki Markalar</a>
                                    </li>
                                    <li class="<?=$active['sayactur']?>">
                                            <a href="sayactur.php">
                                            Servis-Sayaç Türü</a>
                                    </li>
                                    <li class="<?=$active['netsiscari']?>">
                                            <a href="netsiscari.php">
                                            Netsis Cari İsimleri</a>
                                    </li>
                                    <li class="<?=$active['netsisstokkod']?>">
                                            <a href="netsisstokkod.php">
                                            Netsis Servis Stok Kodları</a>
                                    </li>
                                    <li class="<?=$active['yetkilikisi']?>">
                                            <a href="yetkilikisi.php">
                                            Netsis Cari Yetkili Kişiler</a>
                                    </li>
                                    <li class="<?=$active['servisdurum']?>">
                                            <a href="servisdurum.php">
                                            Servis Durumları</a>
                                    </li>
                                    <li class="<?=$active['servisdepo']?>">
                                            <a href="servisdepo.php">
                                            Servis Depoları</a>
                                    </li>
                                    <li class="<?=$active['servisyetkili']?>">
                                            <a href="servisyetkili.php">
                                            Servis Yetkili Kişileri</a>
                                    </li>
                                    <li class="<?=$active['servisbirim']?>">
                                            <a href="servisbirim.php">
                                            Servis Birimleri</a>
                                    </li>
                            </ul>
                    </li>
                    
            </ul>
            <!-- END SIDEBAR MENU -->
    </div>
</div>