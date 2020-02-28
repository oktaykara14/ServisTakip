<?php
//transaction tamamlandı
class ServistakipController extends BackendController {

    public function getServistakipkayit() {
        return View::make('servistakip.servistakipkayit')->with(array('title'=>'Servis Takip Bilgi Ekranı'));
    }

    public function postSayaclist() {
        $netsiscari_id=Input::get('netsiscari_id');
        $servisid=BackendController::getKullaniciServis();
        if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $query = ServisTakip::whereIn('servistakip.servis_id',$servisid)->whereIn('servistakip.netsiscari_id',$netsiscarilist)
                ->select(array("servistakip.id","servistakip.serino","servis.servisadi","sayacadi.sayacadi","uretimyer.yeradi","sayacdurum.durumadi",
                    "servistakip.depotarih","servistakip.sonislemtarihi","servistakip.gdepotarihi","servistakip.gdurumtarihi","servistakip.eskiserino",
                    "servis.nservisadi","sayacadi.nsayacadi","uretimyer.nyeradi","sayacdurum.ndurumadi","servistakip.durum"))
            ->leftjoin("uretimyer", "servistakip.uretimyer_id", "=", "uretimyer.id")
            ->leftjoin("servis", "servistakip.servis_id", "=", "servis.id")
            ->leftjoin("sayacadi", "servistakip.sayacadi_id", "=", "sayacadi.id")
            ->leftjoin("sayacdurum", "servistakip.durum", "=", "sayacdurum.id");
        }else{
            $query = ServisTakip::whereIn('servis_id',$servisid)
                ->select(array("servistakip.id","servistakip.serino","servis.servisadi","sayacadi.sayacadi","uretimyer.yeradi","sayacdurum.durumadi",
                    "servistakip.depotarih","servistakip.sonislemtarihi","servistakip.gdepotarihi","servistakip.gdurumtarihi","servistakip.eskiserino",
                    "servis.nservisadi","sayacadi.nsayacadi","uretimyer.nyeradi","sayacdurum.ndurumadi","servistakip.durum"))
            ->leftjoin("uretimyer", "servistakip.uretimyer_id", "=", "uretimyer.id")
            ->leftjoin("servis", "servistakip.servis_id", "=", "servis.id")
                ->leftjoin("sayacadi", "servistakip.sayacadi_id", "=", "sayacadi.id")
            ->leftjoin("sayacdurum", "servistakip.durum", "=", "sayacdurum.id");
        }
        return Datatables::of($query)
            ->editColumn('serino', function ($model) {
                return $model->serino.($model->eskiserino ? "(".$model->eskiserino.")" : "");
            })
            ->editColumn('depotarih', function ($model) { $date = new DateTime($model->depotarih);  return $date->format('d-m-Y'); })
            ->editColumn('sonislemtarihi', function ($model) { $date = new DateTime($model->sonislemtarihi);  return $date->format('d-m-Y'); })
            ->addColumn('islemler', function ($model) {
                $user=Auth::user();
                if($user->grup_id>15)
                    return "<a class='btn btn-sm btn-info goster' href='#goster' data-toggle='modal' data-id='".$model->id."'> Göster </a>";
                else
                    return "<a class='btn btn-sm btn-warning detay' href='#detay' data-toggle='modal' data-id='".$model->id."'> Detay </a>";
            })
            ->make(true);
    }

    public function getDurumdetay() {
        try{
            $id=Input::get('id');
            $servistakip = ServisTakip::find($id);
            $servistakip->uretimyer = Uretimyer::find($servistakip->uretimyer_id);
            $servistakip->servis = Servis::find($servistakip->servis_id);
            $servistakip->sayacadi = SayacAdi::find($servistakip->sayacadi_id);
            if($servistakip->durum==11 || $servistakip->durum==10 || $servistakip->durum==9) ///sayaç teslim edilmiştir durum değiştirilmez
                $durumlar=SayacDurum::where('id',$servistakip->durum)->get();
            else
                $durumlar=SayacDurum::where('ozel',1)->orWhere('id',$servistakip->durum)->get();
            $servistakip->sondurum=SayacDurum::where('id',$servistakip->durum)->first();
            $yapilanlar=array();
            $i=1;
            $depotarih=date('d-m-Y H:i:s',strtotime($servistakip->depotarih));
            array_push($yapilanlar,array('id'=>$i,'islem_id'=>$servistakip->depogelen_id,'islem'=>'Sayaç Depoya Geldi','kullanici'=>'Depo','tarih'=>$depotarih));
            $i++;
            if($servistakip->sayacgiristarihi){
                $sayackayit=date('d-m-Y H:i:s',strtotime($servistakip->sayacgiristarihi));
                $sayackayitekli=SayacGelen::find($servistakip->sayacgelen_id);
                $sayackayitekli->kullanici=Kullanici::find($sayackayitekli->kullanici_id);
                array_push($yapilanlar,array('id'=>$i,'islem_id'=>$servistakip->sayacgelen_id,'islem'=>'Servis Sayaç Kayıdı Yapıldı','kullanici'=>$sayackayitekli->kullanici->adi_soyadi,'tarih'=>$sayackayit));
                $i++;
            }
            if($servistakip->arizakayittarihi){
                $arizakayit=date('d-m-Y H:i:s',strtotime($servistakip->arizakayittarihi));
                $arizakayitekli=ArizaKayit::find($servistakip->arizakayit_id);
                $arizakayitekli->kullanici=Kullanici::find($arizakayitekli->arizakayit_kullanici_id);
                array_push($yapilanlar,array('id'=>$i,'islem_id'=>$servistakip->arizakayit_id,'islem'=>'Arıza Kayıdı Yapıldı','kullanici'=>$arizakayitekli->kullanici->adi_soyadi,'tarih'=>$arizakayit));
                $i++;
            }
            if($servistakip->depolararasitarihi){
                $depolararasi=date('d-m-Y H:i:s',strtotime($servistakip->depolararasitarihi));
                $depolararasiekli=Depolararasi::find($servistakip->depolararasi_id);
                $depolararasiekli->kullanici=Kullanici::find($depolararasiekli->kullanici_id);
                array_push($yapilanlar,array('id'=>$i,'islem_id'=>$servistakip->depoalrarasi_id,'islem'=>'Depolararası Sevk Yapıldı','kullanici'=>$depolararasiekli->kullanici->adi_soyadi,'tarih'=>$depolararasi));
                $i++;
            }
            if($servistakip->ucretlendirmetarihi){
                $ucretlendirme=date('d-m-Y H:i:s',strtotime($servistakip->ucretlendirmetarihi));
                $arizafiyatekli=ArizaFiyat::find($servistakip->arizafiyat_id);
                $arizafiyatekli->kullanici=Kullanici::find($arizafiyatekli->kullanici_id);
                array_push($yapilanlar,array('id'=>$i,'islem_id'=>$servistakip->arizafiyat_id,'islem'=>'Fiyatlandırma Yapıldı','kullanici'=>$arizafiyatekli->kullanici->adi_soyadi,'tarih'=>$ucretlendirme));
                $i++;
            }
            if($servistakip->gondermetarihi){
                $gonderme=date('d-m-Y H:i:s',strtotime($servistakip->gondermetarihi));
                $ucretlendirmeekli=Ucretlendirilen::find($servistakip->ucretlendirilen_id);
                $ucretlendirmeekli->kullanici=Kullanici::find($ucretlendirmeekli->kullanici_id);
                array_push($yapilanlar,array('id'=>$i,'islem_id'=>$servistakip->ucretlendirilen_id,'islem'=>'Onay Formu Gönderildi','kullanici'=>$ucretlendirmeekli->kullanici->adi_soyadi,'tarih'=>$gonderme));
                $i++;
            }
            if($servistakip->onaylanmatarihi){
                $onaylama=date('d-m-Y H:i:s',strtotime($servistakip->onaylanmatarihi));
                $onaylamaekli=Onaylanan::find($servistakip->onaylanan_id);
                $onaylamaekli->yetkili=Yetkili::find($onaylamaekli->yetkili_id);
                if($onaylamaekli->yetkili && $onaylamaekli->yetkili->kullanici_id!=null)
                {
                    $onaylamaekli->yetkili->kullanici=Kullanici::find($onaylamaekli->yetkili->kullanici_id);
                }
                $onaylayan = $onaylamaekli->onaylamatipi==0 ? 'Sistem' : ( $onaylamaekli->onaylamatipi==3 ? $onaylamaekli->yetkili->kullanici->adi_soyadi : 'Müşteri');
                array_push($yapilanlar,array('id'=>$i,'islem_id'=>$servistakip->onaylanan_id,'islem'=>'Müşteri Onayı Alındı','kullanici'=>$onaylayan,'tarih'=>$onaylama));
            }
            if($servistakip->reddetmetarihi){
                $reddetme=date('d-m-Y H:i:s',strtotime($servistakip->reddetmetarihi));
                $ucretlendirmeekli=Ucretlendirilen::find($servistakip->ucretlendirilen_id);
                $ucretlendirmeekli->kullanici=Kullanici::find($ucretlendirmeekli->kullanici_id);
                array_push($yapilanlar,array('id'=>$i,'islem_id'=>$servistakip->ucretlendirilen_id,'islem'=>'Fiyatlandırma Reddedildi','kullanici'=>'Müşteri','tarih'=>$reddetme));
                $i++;
            }
            if($servistakip->tekrarucrettarihi){
                $tekrarucret=date('d-m-Y H:i:s',strtotime($servistakip->tekrarucrettarihi));
                $arizafiyatekli=ArizaFiyat::find($servistakip->arizafiyat_id);
                $arizafiyatekli->kullanici=Kullanici::find($arizafiyatekli->kullanici_id);
                array_push($yapilanlar,array('id'=>$i,'islem_id'=>$servistakip->arizafiyat_id,'islem'=>'Tekrar Fiyatlandırıldı','kullanici'=>$arizafiyatekli->kullanici->adi_soyadi,'tarih'=>$tekrarucret));
                $i++;
            }
            if($servistakip->kalibrasyontarihi){
                $kalibrasyon=date('d-m-Y H:i:s',strtotime($servistakip->kalibrasyontarihi));
                array_push($yapilanlar,array('id'=>$i,'islem_id'=>$servistakip->kalibrasyon_id,'islem'=>'Kalibrasyonu Yapıldı','kullanici'=>'','tarih'=>$kalibrasyon));
                $i++;
            }
            if($servistakip->depoteslimtarihi){
                $depoteslim=date('d-m-Y H:i:s',strtotime($servistakip->depoteslimtarihi));
                $depoteslimekli=DepoTeslim::find($servistakip->depoteslim_id);
                $depoteslimekli->kullanici=Kullanici::find($depoteslimekli->kullanici_id);
                array_push($yapilanlar,array('id'=>$i,'islem_id'=>$servistakip->depoteslim_id,'islem'=>'Depoya Teslim Edildi','kullanici'=>isset($depoteslimekli->kullanici) ? $depoteslimekli->kullanici->adi_soyadi : '','tarih'=>$depoteslim));
                $i++;
            }
            if($servistakip->gerigonderimtarihi){
                $gerigonderim=date('d-m-Y H:i:s',strtotime($servistakip->gerigonderimtarihi));
                $depoteslimekli=DepoTeslim::find($servistakip->depoteslim_id);
                $depoteslimekli->kullanici=Kullanici::find($depoteslimekli->kullanici_id);
                array_push($yapilanlar,array('id'=>$i,'islem_id'=>$servistakip->depoteslim_id,'islem'=>'Geri Gönderildi','kullanici'=>isset($depoteslimekli->kullanici) ? $depoteslimekli->kullanici->adi_soyadi : '','tarih'=>$gerigonderim));
                $i++;
            }
            if($servistakip->hurdalamatarihi){
                $hurdalama=date('d-m-Y H:i:s',strtotime($servistakip->hurdalamatarihi));
                $depoteslimekli=Hurda::find($servistakip->hurda_id);
                $depoteslimekli->kullanici=Kullanici::find($depoteslimekli->kullanici_id);
                array_push($yapilanlar,array('id'=>$i,'islem_id'=>$servistakip->hurda_id,'islem'=>'Hurdaya Ayrıldı','kullanici'=>isset($depoteslimekli->kullanici) ? $depoteslimekli->kullanici->adi_soyadi : '','tarih'=>$hurdalama));
                $i++;
            }
            if($servistakip->aboneteslimtarihi){
                $aboneteslim=date('d-m-Y H:i:s',strtotime($servistakip->aboneteslimtarihi));
                $aboneteslimekli=AboneTeslim::find($servistakip->aboneteslim_id);
                $aboneteslimekli->kullanici=Kullanici::find($aboneteslimekli->kullanici_id);
                array_push($yapilanlar,array('id'=>$i,'islem_id'=>$servistakip->aboneteslim_id,'islem'=>'Sayaç Aboneye Teslim Edildi','kullanici'=>isset($aboneteslimekli->kullanici) ? $aboneteslimekli->kullanici->adi_soyadi : '','tarih'=>$aboneteslim));
            }

            $tarih=$islem=$idler=$islemidler=array();
            foreach ($yapilanlar as $key => $row) {
                $islem[$key]  = $row['islem'];
                $tarih[$key] = strtotime($row['tarih']);
                $idler[$key] = $row['id'];
                $islemidler[$key] = $row['islem_id'];
            }
            array_multisort( $tarih, SORT_ASC,$islemidler, SORT_ASC, $yapilanlar);
            return Response::json(array('durum' => true,'servistakip' => $servistakip,'yapilanlar'=>$yapilanlar,'durumlar'=>$durumlar ));
        }catch (Exception $e){
            Log::error($e);
            return Response::json(array('durum' => false,'hata'=>$e->getMessage()));
        }

    }

    public function postTakipduzenle($id){
        try {
            DB::beginTransaction();
            $durum = Input::get('durum');
            $servistakip = ServisTakip::find($id);
            $servistakip->durum = $durum;
            $servistakip->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $servistakip->id . ' Numaralı Sayaç Durumu Güncellendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Sayaç Durum Numarası:' . $servistakip->id);
            DB::commit();
            return Redirect::to('servistakip/servistakipkayit')->with(array('mesaj' => 'true', 'title' => 'Sayaç Durumu Güncellendi', 'text' => 'Sayaç Durumu Başarıyla Değiştirildi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Durumu Güncellenemedi', 'text' => 'Sayaç Durumu Güncellenirken Hata Oluştu.', 'type' => 'error'));
        }
    }

    public function getBildirimler($bildirimdurum=false) {
        if($bildirimdurum)
            return View::make('servistakip.bildirimler',array('bildirimdurum'=>$bildirimdurum))->with(array('title'=>'Servis Takip Bildirimler Ekranı'));
        else
            return View::make('servistakip.bildirimler')->with(array('title'=>'Servis Takip Bildirimler Ekranı'));
    }

    public function getHatirlatmalar() {
        return View::make('servistakip.hatirlatmalar')->with(array('title'=>'Servis Takip Hatırlatmalar Ekranı'));
    }

    public function postHatirlatmalist() {
        $netsiscari_id = Input::get('netsiscari_id');
        $servisid=BackendController::getKullaniciServis();
        $hatirlatmatipdurum = BackendController::getHatirlatmatipdurum();
        if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $query = Hatirlatma::where('tip',2)->whereIn('hatirlatma.servis_id',$servisid)->whereIn('hatirlatma.netsiscari_id',$netsiscarilist)
                ->whereIn('hatirlatma.hatirlatmatip_id',$hatirlatmatipdurum)
                ->select(array("hatirlatma.id","hatirlatmatip.tur","servis.servisadi","netsiscari.cariadi","servisstokkod.stokadi","hatirlatma.tarih",
                    "hatirlatma.adet","hatirlatma.kalan","hatirlatma.gdurum","hatirlatma.gtarih","hatirlatmatip.ntur","servis.nservisadi","netsiscari.ncariadi",
                    "servisstokkod.nstokadi","hatirlatma.ndurum","hatirlatma.hatirlatmatip_id","hatirlatma.servis_id"))
                ->leftjoin("hatirlatmatip", "hatirlatma.hatirlatmatip_id", "=", "hatirlatmatip.id")
                ->leftjoin("servis", "hatirlatma.servis_id", "=", "servis.id")
                ->leftjoin("netsiscari", "hatirlatma.netsiscari_id", "=", "netsiscari.id")
                ->leftjoin("servisstokkod", "hatirlatma.servisstokkodu", "=", "servisstokkod.stokkodu");
        }else{
            $query = Hatirlatma::where('tip',2)->whereIn('servis_id',$servisid)->whereIn('hatirlatma.hatirlatmatip_id',$hatirlatmatipdurum)
                ->select(array("hatirlatma.id","hatirlatmatip.tur","servis.servisadi","netsiscari.cariadi","servisstokkod.stokadi","hatirlatma.tarih",
                    "hatirlatma.adet","hatirlatma.kalan","hatirlatma.gdurum","hatirlatma.gtarih","hatirlatmatip.ntur","servis.nservisadi","netsiscari.ncariadi",
                    "servisstokkod.nstokadi","hatirlatma.ndurum","hatirlatma.hatirlatmatip_id","hatirlatma.servis_id"))
                ->leftjoin("hatirlatmatip", "hatirlatma.hatirlatmatip_id", "=", "hatirlatmatip.id")
                ->leftjoin("servis", "hatirlatma.servis_id", "=", "servis.id")
                ->leftjoin("netsiscari", "hatirlatma.netsiscari_id", "=", "netsiscari.id")
                ->leftjoin("servisstokkod", "hatirlatma.servisstokkodu", "=", "servisstokkod.stokkodu");
        }
        return Datatables::of($query)
            ->editColumn('stokadi', function ($model) {
                if($model->hatirlatmatip_id>3)  return '';
                else               return $model->stokadi;
            })
            ->editColumn('tarih', function ($model) { $date = new DateTime($model->tarih);  return $date->format('d-m-Y'); })
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                $servisadi="";
                switch($model->servis_id){
                    case 1: $servisadi="suservis";
                        break;
                    case 2: $servisadi="elkservis";
                        break;
                    case 3: $servisadi="gazservis";
                        break;
                    case 4: $servisadi="isiservis";
                        break;
                    case 5: $servisadi="mekanikgaz";
                        break;
                    case 6: $servisadi="sube";
                        break;
                }
                if($model->gdurum=="Tamamlandı") //tamamlananlara gidecek
                {
                    if ($model->hatirlatmatip_id == 1) { //Depo Kayıt Tamamlandı
                        return "<a class='btn btn-sm btn-success' href='".$root."/depo/depogelen/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 2) { //Sayaç Kayıt Tamamlandı
                        return "<a class='btn btn-sm btn-success' href='".$root."/".$servisadi."/sayackayit/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 3) { //Arıza Kayıt Tamamlandı
                        return "<a class='btn btn-sm btn-success' href='".$root."/".$servisadi."/arizakayit/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 4) { //Fiyatlandırma Yapıldı
                        return "<a class='btn btn-sm btn-success' href='".$root."/ucretlendirme/ucretlendirilenler/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 5) { //Form Gönderildi
                        return "<a class='btn btn-sm btn-success' href='".$root."/ucretlendirme/ucretlendirilenler/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 6) { //Müşteri Onayı Alındı
                        if(Auth::user()->grup_id==19)
                            return "<a class='btn btn-sm btn-success' href='".$root."/abone/musterionay/".$model->id."' > Git </a>";
                        else
                            return "<a class='btn btn-sm btn-success' href='".$root."/ucretlendirme/onaylananlar/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 7) { //Tekrar Fiyatlandırıldı
                        return "<a class='btn btn-sm btn-success' href='".$root."/ucretlendirme/ucretlendirilenler/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 8) { //Kalibrasyon Yapıldı
                        return "<a class='btn btn-sm btn-success' href='".$root."/kalibrasyon/kalibrasyon/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 9) { //Depo Teslim Edildi
                        return "<a class='btn btn-sm btn-success' href='".$root."/depo/depoteslim/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 10) { //Hurdaya Ayrıldı
                        return "<a class='btn btn-sm btn-success' href='".$root."/depo/hurda/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 11) { //Şubeye Gönderildi
                        return "<a class='btn btn-sm btn-success' href='".$root."/depo/depolararasi/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 12) { //Abone Teslim Edildi
                        return "<a class='btn btn-sm btn-success' href='".$root."/depo/aboneteslim/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 13) { //Servis Kayıdı Yapıldı
                        return "<a class='btn btn-sm btn-success' href='".$root."/sube/serviskayit/".$model->id."' > Git </a>";
                    } else {
                        return "";
                    }
                }else{ //bekleyenlere gidecek
                    if ($model->hatirlatmatip_id == 2) { //Sayaç Kayıt Bekliyor
                        return "<a class='btn btn-sm btn-warning' href='".$root."/".$servisadi."/sayackayitekle/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 3) { //Arıza Kayıt Bekliyor
                        return "<a class='btn btn-sm btn-warning' href='".$root."/".$servisadi."/arizakayitekle/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 4) { //Fiyatlandırma Bekliyor
                        return "<a class='btn btn-sm btn-warning' href='".$root."/ucretlendirme/ucretlendirmekayit/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 5) { //Form Gönderimi Bekliyor
                        return "<a class='btn btn-sm btn-warning' href='".$root."/ucretlendirme/ucretlendirilenler/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 6) { //Müşteri Onayı Bekliyor
                        if(Auth::user()->grup_id==19)
                            return "<a class='btn btn-sm btn-warning' href='".$root."/abone/musterionay/".$model->id."' > Git </a>";
                        else
                            return "<a class='btn btn-sm btn-warning' href='".$root."/ucretlendirme/ucretlendirilenler/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 7) { //Tekrar Ücretlendirme Bekliyor
                        return "<a class='btn btn-sm btn-warning' href='".$root."/ucretlendirme/ucretlendirmekayit/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 8) { //Kalibrasyon Bekliyor
                        return "<a class='btn btn-sm btn-warning' href='".$root."/kalibrasyon/kalibrasyon/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 9) { //Depo Teslim Bekliyor
                        return "<a class='btn btn-sm btn-warning' href='".$root."/depo/depoteslim/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 11) { //Depolararası Teslim Bekliyor
                        return "<a class='btn btn-sm btn-warning' href='".$root."/depo/depolararasi/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 12) { //Abone Teslim Bekliyor
                        return "<a class='btn btn-sm btn-warning' href='".$root."/depo/aboneteslim/".$model->id."' > Git </a>";
                    } else if ($model->hatirlatmatip_id == 13) { //Servis Kayıdı Bekliyor
                        return "<a class='btn btn-sm btn-warning' href='".$root."/sube/serviskayit/".$model->id."' > Git </a>";
                    } else {
                        return "";
                    }
                }
            })
            ->make(true);
    }

    public function postBildirimlist()
    {
        $bildirimdurum=Input::get('bildirimdurum');
        $netsiscari_id = Input::get('netsiscari_id');
        $servisid = BackendController::getKullaniciServis();
        $bildirimtipdurum = BackendController::getBildirimtipdurum();
        if($bildirimdurum!=""){
            $bildirimtipdurum = BackendController::getOnaybildirimtipdurum();
            $query = Hatirlatma::where('tip', 1)->whereIn('servis_id', $servisid)->whereIn('hatirlatma.hatirlatmatip_id',$bildirimtipdurum)
                ->select(array("hatirlatma.id", "hatirlatmatip.tur", "servis.servisadi", "netsiscari.cariadi", "servisstokkod.stokadi", "hatirlatma.tarih",
                    "hatirlatma.adet","hatirlatma.kalan","hatirlatma.gdurum","hatirlatma.gtarih","hatirlatmatip.ntur","servis.nservisadi","netsiscari.ncariadi",
                    "servisstokkod.nstokadi","hatirlatma.ndurum","hatirlatma.hatirlatmatip_id","hatirlatma.servis_id"))
                ->leftjoin("hatirlatmatip", "hatirlatma.hatirlatmatip_id", "=", "hatirlatmatip.id")
                ->leftjoin("servis", "hatirlatma.servis_id", "=", "servis.id")
                ->leftjoin("netsiscari", "hatirlatma.netsiscari_id", "=", "netsiscari.id")
                ->leftjoin("servisstokkod", "hatirlatma.servisstokkodu", "=", "servisstokkod.stokkodu");
        } else if ($netsiscari_id!="") {
            $netsiscarilist = explode(',', $netsiscari_id);
            $query = Hatirlatma::where('tip', 1)->whereIn('hatirlatma.servis_id', $servisid)->whereIn('hatirlatma.netsiscari_id', $netsiscarilist)
                ->whereIn('hatirlatma.hatirlatmatip_id',$bildirimtipdurum)
                ->select(array("hatirlatma.id", "hatirlatmatip.tur", "servis.servisadi", "netsiscari.cariadi", "servisstokkod.stokadi", "hatirlatma.tarih",
                    "hatirlatma.adet","hatirlatma.kalan","hatirlatma.gdurum","hatirlatma.gtarih","hatirlatmatip.ntur","servis.nservisadi","netsiscari.ncariadi",
                    "servisstokkod.nstokadi","hatirlatma.ndurum","hatirlatma.hatirlatmatip_id","hatirlatma.servis_id"))
                ->leftjoin("hatirlatmatip", "hatirlatma.hatirlatmatip_id", "=", "hatirlatmatip.id")
                ->leftjoin("servis", "hatirlatma.servis_id", "=", "servis.id")
                ->leftjoin("netsiscari", "hatirlatma.netsiscari_id", "=", "netsiscari.id")
                ->leftjoin("servisstokkod", "hatirlatma.servisstokkodu", "=", "servisstokkod.stokkodu");
        } else {
            $query = Hatirlatma::where('tip', 1)->whereIn('servis_id', $servisid)->whereIn('hatirlatma.hatirlatmatip_id',$bildirimtipdurum)
                ->select(array("hatirlatma.id", "hatirlatmatip.tur", "servis.servisadi", "netsiscari.cariadi", "servisstokkod.stokadi", "hatirlatma.tarih",
                    "hatirlatma.adet","hatirlatma.kalan","hatirlatma.gdurum","hatirlatma.gtarih","hatirlatmatip.ntur","servis.nservisadi","netsiscari.ncariadi",
                    "servisstokkod.nstokadi","hatirlatma.ndurum","hatirlatma.hatirlatmatip_id","hatirlatma.servis_id"))
                ->leftjoin("hatirlatmatip", "hatirlatma.hatirlatmatip_id", "=", "hatirlatmatip.id")
                ->leftjoin("servis", "hatirlatma.servis_id", "=", "servis.id")
                ->leftjoin("netsiscari", "hatirlatma.netsiscari_id", "=", "netsiscari.id")
                ->leftjoin("servisstokkod", "hatirlatma.servisstokkodu", "=", "servisstokkod.stokkodu");
        }
        return Datatables::of($query)
            ->editColumn('stokadi', function ($model) {
                if($model->hatirlatmatip_id>3)  return '';
                else              return $model->stokadi;
            })
            ->editColumn('tur', function ($model) {
                if ($model->hatirlatmatip_id==6)
                    return 'Müşteri Reddi';
                else if ($model->hatirlatmatip_id==7)
                    return 'Müşteri Onayı';
                else
                    return $model->tur;
            })
            ->editColumn('tarih', function ($model) { $date = new DateTime($model->tarih);  return $date->format('d-m-Y'); })
            ->addColumn('islemler', function ($model) {
                if ($model->gdurum =="Okunmadı")  return "<a data-id='" . $model->id . "' class='btn btn-sm btn-warning ok'> OK </a>";
                else return "";
            })
            ->make(true);
    }

    public function getBildirimdurum(){
        try {
            $id=Input::get('id');
            DB::beginTransaction();
            if ($id == 0) {
                try {
                    $bildirimtipdurum = BackendController::getBildirimtipdurum();
                    if (isset(Auth::user()->netsiscari_id) && count(Auth::user()->netsiscari_id) > 0) {
                        $netsiscariler = Auth::user()->netsiscari_id;
                        $bildirimler = Hatirlatma::where('tip', 1)->whereIn('netsiscari_id', $netsiscariler)
                            ->whereIn('hatirlatmatip_id', $bildirimtipdurum)->where('durum', 0)->get();
                        foreach ($bildirimler as $bildirim) {
                            $bildirim->durum = 1;
                            $bildirim->gdurum = 'Okundu';
                            $bildirim->save();
                        }
                    } else {
                        $bildirimler = Hatirlatma::where('tip', 1)->whereIn('hatirlatmatip_id', $bildirimtipdurum)->where('durum', 0)->get();
                        foreach ($bildirimler as $bildirim) {
                            $bildirim->durum = 1;
                            $bildirim->gdurum = 'Okundu';
                            $bildirim->save();
                        }
                    }
                    DB::commit();
                    return Response::json(array('mesaj' => 'true', 'title' => 'Bildirimler Okundu', 'text' => 'Bildirimler Başarıyla Okundu.', 'type' => 'success'));
                } catch (Exception $e) {
                    Log::error($e);
                    DB::rollBack();
                    return Response::json(array('mesaj' => 'true', 'title' => 'Bildirimler Okunamadı', 'text' => 'Bildirimler Okunurken Sorun Oluştu.', 'type' => 'error'));
                }
            } else {
                try {
                    $bildirim = Hatirlatma::find($id);
                    $bildirim->durum = 1;
                    $bildirim->gdurum = 'Okundu';
                    $bildirim->save();
                    DB::commit();
                    return Response::json(array('mesaj' => 'true', 'title' => 'Bildirim Okundu', 'text' => 'Bildirim Başarıyla Okundu.', 'type' => 'success'));
                } catch (Exception $e) {
                    Log::error($e);
                    DB::rollBack();
                    return Response::json(array('mesaj' => 'true', 'title' => 'Bildirim Okunamadı', 'text' => 'Bildirim Okunurken Sorun Oluştu.', 'type' => 'error'));
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('mesaj' => 'true', 'title' => 'Bildirim Okunamadı', 'text' => 'Bildirim Okunurken Sorun Oluştu.', 'type' => 'error'));
        }

    }

}
