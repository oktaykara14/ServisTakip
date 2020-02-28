<?php
//tansaction işlemi tamamlandı abone onayı test edilecek
class AboneController extends BackendController {

    public function getMusterionay($hatirlatma_id=false){
        if($hatirlatma_id)
            return View::make('abone.musterionay',array('hatirlatma_id'=>$hatirlatma_id))->with(array('title'=>'Ucretlendirme Onay Ekranı'));
        else
            return View::make('abone.musterionay')->with(array('title'=>'Ucretlendirme Onay Ekranı'));
    }

    public function postUcretlendirmeonaylist() {
        $hatirlatma_id=Input::get('hatirlatma_id');
        $netsiscari_id=Input::get('netsiscari_id');
        $servisid=BackendController::getKullaniciServis();
        $hatirlatma = false;
        if($hatirlatma_id!="") {
            $hatirlatma = Hatirlatma::find($hatirlatma_id);
        }
        if($hatirlatma){
            $query = Ucretlendirilen::whereIn('ucretlendirilen.durum',array(1,2,3,4))
                ->where('ucretlendirilen.netsiscari_id',$hatirlatma->netsiscari_id)->whereIn('ucretlendirilen.servis_id',$servisid)
                ->select(array("ucretlendirilen.id","uretimyer.yeradi","ucretlendirilen.sayacsayisi","ucretlendirilen.gabonedurum","ucretlendirilen.fiyat",
                    "kullanici.adi_soyadi","ucretlendirilen.durumtarihi","ucretlendirilen.gdurumtarihi","uretimyer.nyeradi","ucretlendirilen.nabonedurum",
                    "kullanici.nadi_soyadi","ucretlendirilen.mail","ucretlendirilen.fiyat2","parabirimi.birimi","parabirimi2.birimi2"))
                ->leftjoin("uretimyer", "ucretlendirilen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("kullanici", "ucretlendirilen.kullanici_id", "=", "kullanici.id")
                ->leftjoin("parabirimi", "ucretlendirilen.parabirimi_id", "=", "parabirimi.id")
                ->leftjoin("parabirimi2", "ucretlendirilen.parabirimi2_id", "=", "parabirimi2.id");
        }else if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $query = Ucretlendirilen::whereIn('ucretlendirilen.durum',array(1,2,3,4))
                ->whereIn('ucretlendirilen.netsiscari_id',$netsiscarilist)->whereIn('ucretlendirilen.servis_id',$servisid)
                ->select(array("ucretlendirilen.id","uretimyer.yeradi","ucretlendirilen.sayacsayisi","ucretlendirilen.gabonedurum","ucretlendirilen.fiyat",
                    "kullanici.adi_soyadi","ucretlendirilen.durumtarihi","ucretlendirilen.gdurumtarihi","uretimyer.nyeradi","ucretlendirilen.nabonedurum",
                    "kullanici.nadi_soyadi","ucretlendirilen.mail","ucretlendirilen.fiyat2","parabirimi.birimi","parabirimi2.birimi2"))
                ->leftjoin("uretimyer", "ucretlendirilen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("kullanici", "ucretlendirilen.kullanici_id", "=", "kullanici.id")
                ->leftjoin("parabirimi", "ucretlendirilen.parabirimi_id", "=", "parabirimi.id")
                ->leftjoin("parabirimi2", "ucretlendirilen.parabirimi2_id", "=", "parabirimi2.id");
        }else{
            $query = Ucretlendirilen::whereIn('ucretlendirilen.durum',array(1,2,3,4))
                ->whereIn('ucretlendirilen.servis_id',$servisid)->where('ucretlendirilen.netsiscari_id',0)
                ->select(array("ucretlendirilen.id","uretimyer.yeradi","ucretlendirilen.sayacsayisi","ucretlendirilen.gabonedurum","ucretlendirilen.fiyat",
                    "kullanici.adi_soyadi","ucretlendirilen.durumtarihi","ucretlendirilen.gdurumtarihi","uretimyer.nyeradi","ucretlendirilen.nabonedurum",
                    "kullanici.nadi_soyadi","ucretlendirilen.mail","ucretlendirilen.fiyat2","parabirimi.birimi","parabirimi2.birimi2"))
                ->leftjoin("uretimyer", "ucretlendirilen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("kullanici", "ucretlendirilen.kullanici_id", "=", "kullanici.id")
                ->leftjoin("parabirimi", "ucretlendirilen.parabirimi_id", "=", "parabirimi.id")
                ->leftjoin("parabirimi2", "ucretlendirilen.parabirimi2_id", "=", "parabirimi2.id");

        }
        return Datatables::of($query)
            ->editColumn('fiyat', function ($model) {
                if($model->garanti)
                    return '0.00 '.$model->birimi;
                else
                    if($model->fiyat2==0){
                        if($model->fiyat==0)
                            return "0.00 ".$model->birimi;
                        else
                            return $model->fiyat." ".$model->birimi;
                    }else{
                        if($model->fiyat==0)
                            return "0.00 ".$model->birimi." + ".$model->fiyat2." ".$model->birimi2;
                        else
                            return $model->fiyat." ".$model->birimi." + ".$model->fiyat2." ".$model->birimi2;
                    }
            })
            ->editColumn('durumtarihi',function($model) {
                $date = new DateTime($model->durumtarihi);
                return $date->format('d-m-Y');
            })
            ->addColumn('islemler',function ($model) {
                if($model->gabonedurum=="Reddedildi")
                    return "<a href='#redneden' data-toggle='modal' data-id='{$model->id}' class='btn btn-sm btn-danger neden'>Detay</a>";
                else
                    return "<a href='#detay-goster' data-toggle='modal' data-id='{$model->id}' class='btn btn-sm btn-warning goster'> Detay </a>";
            })
            ->make(true);
    }

    public function getOnaybilgi(){
        try {
            $id=Input::get('id');
            $ucretlendirilen = Ucretlendirilen::find($id);
            $ucretlendirilen->uretimyer = Uretimyer::find($ucretlendirilen->uretimyer_id);
            $ucretlendirilen->netsiscari = Netsiscari::find($ucretlendirilen->netsiscari_id);
            $ucretlendirilen->parabirimi = ParaBirimi::find($ucretlendirilen->parabirimi_id);
            $ucretlendirilen->parabirimi2 = ParaBirimi::find($ucretlendirilen->parabirimi2_id);
            $ucretlendirilen->servis = Servis::find($ucretlendirilen->servis_id);
            if ($ucretlendirilen->durum == 1)
                $secilenler = explode(',', $ucretlendirilen->secilenler);
            else
                $secilenler = explode(',', $ucretlendirilen->reddedilenler);
            $ucretlendirilen->arizafiyat = ArizaFiyat::whereIn('id', $secilenler)->get();
            foreach ($ucretlendirilen->arizafiyat as $arizafiyat) {
                $arizafiyat->sayacadi = SayacAdi::find($arizafiyat->sayacadi_id);
                $arizafiyat->sayaccap = SayacCap::find($arizafiyat->sayaccap_id);
                $arizafiyat->parabirimi = ParaBirimi::find($arizafiyat->parabirimi_id);
                $arizafiyat->parabirimi2 = ParaBirimi::find($arizafiyat->parabirimi2_id);
            }
            $dovizkuru = DovizKuru::where('tarih', $ucretlendirilen->kurtarihi)->take(3)->get();
            foreach ($dovizkuru as $doviz) {
                $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
            }
            return Response::json(array('durum' => true, 'ucretlendirilen' => $ucretlendirilen, 'dovizkuru' => $dovizkuru ));
        } catch (Exception $e) {
            return Response::json(array('durum' => false, 'durum_mesaj' => str_replace("'","\'",$e->getMessage()) ));
        }
    }

    public function postOnayla(){
        $isim = "";
        try {
            DB::beginTransaction();
            $ucretlendirilenid = Input::get('onayid');
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            if($ucretlendirilen->durum!=1){
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylama Tamamlanamadı!', 'text' => 'Ücretlendirme Zaten Onaylanmış ya da Reddedilmiş Olabilir!', 'type' => 'warning'));
            }
            $servisid = $ucretlendirilen->servis_id;
            $uretimyerid = $ucretlendirilen->uretimyer_id;
            $uretimyer = UretimYer::find($uretimyerid);
            $netsiscariid = $ucretlendirilen->netsiscari_id;
            $secilenler = $ucretlendirilen->secilenler;
            $secilenlist = explode(',', $secilenler);
            $sayacsayisi = count($secilenlist);
            $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenlist)->get();
            $netsiscari = NetsisCari::find($netsiscariid);
            $kullanici=Kullanici::find(Auth::user()->id);
            $yetkili = Yetkili::where('netsiscari_id', $netsiscariid)->where('kullanici_id',$kullanici->id)->first();
            $birimyonetici = Kullanici::where('grup_id',5)->where('aktifdurum',1)->first();
            $servisyetkili = Kullanici::where('id',$ucretlendirilen->kullanici_id)->first();
            $mailcc = null;
            if($birimyonetici){
                if($servisyetkili){
                    if($birimyonetici->id!=$servisyetkili->id){
                        $mailcc = $birimyonetici->email;
                    }
                }else{
                    $servisyetkili = $birimyonetici;
                }
            }
            $onaylanan = new Onaylanan;
            try{
                If (Input::hasFile('eklenendosya')) {
                    $dosya = Input::file('eklenendosya');
                    $uzanti = $dosya->getClientOriginalExtension();
                    $isim = Str::slug($uretimyer->yeradi) . '_' . Str::slug(str_random(5)) . '.' . $uzanti;
                    $dosya->move('assets/onayformu/', $isim);
                    $onaylanan->onayformu = $isim;
                }
            }catch (Exception $e){
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Eklenen Dosya Yüklenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
            }
            try{
                $ucretlendirilen->durum = 2;
                $ucretlendirilen->onaytarihi = date('Y-m-d H:i:s');
                $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                $ucretlendirilen->save();
                $birim = $ucretlendirilen->parabirimi_id;
                $birim2 = $ucretlendirilen->parabirimi2_id;
                $onaylanan->servis_id = $servisid;
                $onaylanan->uretimyer_id = $uretimyerid;
                $onaylanan->netsiscari_id = $netsiscariid;
                $onaylanan->ucretlendirilen_id = $ucretlendirilen->id;
                $onaylanan->yetkili_id = $yetkili->id;
                $onaylanan->onaytarihi = date('Y-m-d H:i:s');
                $onaylanan->onaylamatipi = 2;
                $onaylanan->userip = BackendController::getRealIP();
                $onaylanan->save();
            }catch (Exception $e){
                Log::error($e);
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylama onaylananlara kaydedilemedi', 'type' => 'error'));
            }
            $secilensayaclar = "";
            $secilensayaclist = array();
            $garantisayaclar = "";
            $garantisayaclist = array();
            $iadesayaclar = "";
            $iadesayaclist = array();
            try {
                foreach ($arizafiyatlar as $arizafiyat) {
                    $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                    $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                    if($arizakayit->arizakayit_durum==7){
                        array_push($iadesayaclist, $sayacgelen->id);
                        $iadesayaclar .= ($iadesayaclar == "" ? "" : ",") . $sayacgelen->id;
                    }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0) {
                        array_push($secilensayaclist, $sayacgelen->id);
                        $secilensayaclar .= ($secilensayaclar == "" ? "" : ",") . $sayacgelen->id;
                    }else{
                        array_push($garantisayaclist, $sayacgelen->id);
                        $garantisayaclar .= ($garantisayaclar == "" ? "" : ",") . $sayacgelen->id;
                    }
                    $sayacgelen->musterionay = 1;
                    $sayacgelen->teslimdurum = 1; //0 bekliyor 1 teslimat 2 geri gönderim 3 hurda 4 depolararasi 5 periyodik bakım
                    $sayacgelen->save();
                    $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                    $servistakip->onaylanan_id = $onaylanan->id;
                    $servistakip->durum = 5;
                    $servistakip->onaylanmatarihi = date('Y-m-d H:i:s');
                    $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                    $servistakip->save();
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylama onaylananlara kaydedilemedi', 'type' => 'error'));
            }
            if ($servisid == 5) //kalibrasyon tamamlanmışsa depo teslim yoksa bekleyecek
            {
                try {
                    $kalibrasyonsayi=0;
                    $kalibrasyonsayaclist="";
                    $garantisayi=0;
                    $garantisayaclist="";
                    $geriiadesayi=0;
                    $geriiadesayaclist="";
                    $periyodiksayi=0;
                    $periyodiklist="";
                    foreach ($arizafiyatlar as $arizafiyat) {
                        $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                        $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                        $depogelen=DepoGelen::find($sayacgelen->depogelen_id);
                        if ($depogelen->periyodik) {
                            if ($sayacgelen->kalibrasyon) {
                                $periyodiksayi++;
                                $periyodiklist .= ($periyodiklist == "" ? "" : ",") . $sayacgelen->id;
                            }
                        } else {
                            if($arizakayit->arizakayit_durum==7){
                                $geriiadesayi++;
                                $geriiadesayaclist .= ($geriiadesayaclist == "" ? "" : ",") . $sayacgelen->id;
                            }else if ($sayacgelen->kalibrasyon) {
                                if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0){
                                    $kalibrasyonsayi++;
                                    $kalibrasyonsayaclist .= ($kalibrasyonsayaclist == "" ? "" : ",") . $sayacgelen->id;
                                }else{
                                    $garantisayi++;
                                    $garantisayaclist .= ($garantisayaclist == "" ? "" : ",") . $sayacgelen->id;
                                }
                            }
                        }
                    }
                    if($kalibrasyonsayi>0){
                        $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                            ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',0)->first();
                        if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                        {
                            $secilenlist = explode(',', $depoteslim->secilenler);
                            $kalibrasyonsayaclar = explode(',', $kalibrasyonsayaclist);
                            $secilenler="";
                            $adet = 0;
                            foreach($kalibrasyonsayaclar as $sayacgelenid){
                                if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                    $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                    $adet++;
                                }
                            }
                            $kalibrasyonsayi = $adet;
                            if ($adet>0) {
                                $depoteslim->secilenler .= ',' . $secilenler;
                                $depoteslim->sayacsayisi += $adet;
                                $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                $depoteslim->save();
                            }
                        } else { //yeni depo teslimatı yapılacak
                            $depoteslim = new DepoTeslim;
                            $depoteslim->servis_id = $servisid;
                            $depoteslim->netsiscari_id = $netsiscariid;
                            $depoteslim->secilenler = $kalibrasyonsayaclist;
                            $depoteslim->sayacsayisi = $kalibrasyonsayi;
                            $depoteslim->depodurum = 0;
                            $depoteslim->parabirimi_id=$birim;
                            $depoteslim->parabirimi2_id=$birim2;
                            $depoteslim->save();
                        }
                    }
                    if($garantisayi>0){
                        $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                            ->where('depodurum',0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',0)->first();
                        if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                        {
                            $secilenlist = explode(',', $depoteslim->secilenler);
                            $garantisayaclar = explode(',', $garantisayaclist);
                            $secilenler="";
                            $adet = 0;
                            foreach($garantisayaclar as $sayacgelenid){
                                if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                    $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                    $adet++;
                                }
                            }
                            $garantisayi = $adet;
                            if ($adet>0) {
                                $depoteslim->secilenler .= ',' . $secilenler;
                                $depoteslim->sayacsayisi += $adet;
                                $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                $depoteslim->save();
                            }
                        } else { //yeni depo teslimatı yapılacak
                            $depoteslim = new DepoTeslim;
                            $depoteslim->servis_id = $servisid;
                            $depoteslim->netsiscari_id = $netsiscariid;
                            $depoteslim->secilenler = $garantisayaclist;
                            $depoteslim->sayacsayisi = $garantisayi;
                            $depoteslim->depodurum = 0;
                            $depoteslim->tipi = 1;
                            $depoteslim->parabirimi_id=$birim;
                            $depoteslim->parabirimi2_id=$birim2;
                            $depoteslim->save();
                        }
                    }
                    if($geriiadesayi>0){
                        $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                            ->where('depodurum',0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',0)->first();
                        if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                        {
                            $secilenlist = explode(',', $depoteslim->secilenler);
                            $geriiadesayaclar = explode(',', $geriiadesayaclist);
                            $secilenler="";
                            $adet = 0;
                            foreach($geriiadesayaclar as $sayacgelenid){
                                if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                    $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                    $adet++;
                                }
                            }
                            $geriiadesayi = $adet;
                            if ($adet>0) {
                                $depoteslim->secilenler .= ',' . $secilenler;
                                $depoteslim->sayacsayisi += $adet;
                                $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                $depoteslim->save();
                            }
                        } else { //yeni depo teslimatı yapılacak
                            $depoteslim = new DepoTeslim;
                            $depoteslim->servis_id = $servisid;
                            $depoteslim->netsiscari_id = $netsiscariid;
                            $depoteslim->secilenler = $geriiadesayaclist;
                            $depoteslim->sayacsayisi = $geriiadesayi;
                            $depoteslim->depodurum = 0;
                            $depoteslim->tipi = 2;
                            $depoteslim->parabirimi_id=$birim;
                            $depoteslim->parabirimi2_id=$birim2;
                            $depoteslim->save();
                        }
                    }
                    if($periyodiksayi>0){
                        $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                            ->where('depodurum',0)->where('tipi',0)->where('periyodik',1)->where('subegonderim',0)->first();
                        if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                        {
                            $secilenlist = explode(',', $depoteslim->secilenler);
                            $periyodiksayaclar = explode(',', $periyodiklist);
                            $secilenler="";
                            $adet = 0;
                            foreach($periyodiksayaclar as $sayacgelenid){
                                if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                    $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                    $adet++;
                                }
                            }
                            $periyodiksayi = $adet;
                            if ($adet>0) {
                                $depoteslim->secilenler .= ',' . $secilenler;
                                $depoteslim->sayacsayisi += $adet;
                                $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                $depoteslim->save();
                            }
                        } else { //yeni depo teslimatı yapılacak
                            $depoteslim = new DepoTeslim;
                            $depoteslim->servis_id = $servisid;
                            $depoteslim->netsiscari_id = $netsiscariid;
                            $depoteslim->secilenler = $periyodiklist;
                            $depoteslim->sayacsayisi = $periyodiksayi;
                            $depoteslim->depodurum = 0;
                            $depoteslim->periyodik = 1;
                            $depoteslim->parabirimi_id=$birim;
                            $depoteslim->parabirimi2_id=$birim2;
                            $depoteslim->save();
                        }
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    if(File::exists('assets/onayformu/' . $isim . ''))
                        File::delete('assets/onayformu/' . $isim . '');
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Kalibrasyona Gönderilen Sayaç Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                }
                BackendController::HatirlatmaGuncelle(6, $netsiscariid, $servisid, $sayacsayisi);
                BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                if(($kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi)>0)
                    BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi);
                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilenid . ' Nolu Ücretlendirme Abone Tarafından Onaylandı.', 'Onaylayan:' . $kullanici->adi_soyadi . ',Onay Numarası:' . $onaylanan->id . ',Ücretlendirme Numarası:' . $ucretlendirilenid);
                DB::commit();
                try {
                    $hatirlatma = Hatirlatma::where('hatirlatmatip_id',6)->where('servis_id',$servisid)
                        ->where('netsiscari_id',$netsiscariid)->orderBy('id', 'desc')->first();
                    Mail::send('mail.musterionay', array('ucretlendirilen' => $ucretlendirilen,'netsiscari'=>$netsiscari,'hatirlatma'=>$hatirlatma),
                        function ($message) use ($servisyetkili, $ucretlendirilen,$mailcc,$netsiscari) {
                            if(!is_null($mailcc))
                                $message->cc($mailcc);
                            $message->to($servisyetkili->email, $servisyetkili->adi_soyadi)->subject('Servis Fiyatlandirma Müşteri Onayı / '.$netsiscari->cariadi);
                        });
                } catch (Exception $e) {
                    Log::error($e);
                }
                return Redirect::to('abone/musterionay')->with(array('mesaj' => 'true', 'title' => 'Fiyatlandırma Onaylandı', 'text' => 'Sayaçların Fiyatlandırması Onaylandı.Firma ile irtibata geçebilirsiniz.', 'type' => 'success'));
            } else {
                try{
                    if(count($secilensayaclist)>0){
                        $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                            ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',0)->first();
                        if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                        {
                            $secilenlist = explode(',', $depoteslim->secilenler);
                            $secilenler="";
                            $adet = 0;
                            foreach($secilensayaclist as $sayacgelenid){
                                if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                    $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                    $adet++;
                                }
                            }
                            if ($adet>0) {
                                $depoteslim->secilenler .= ',' . $secilenler;
                                $depoteslim->sayacsayisi += $adet;
                                $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                $depoteslim->save();
                            }
                        } else { //yeni depo teslimatı yapılacak
                            $depoteslim = new DepoTeslim;
                            $depoteslim->servis_id = $servisid;
                            $depoteslim->netsiscari_id = $netsiscariid;
                            $depoteslim->secilenler = $secilensayaclar;
                            $depoteslim->sayacsayisi = count($secilensayaclist);
                            $depoteslim->depodurum = 0;
                            $depoteslim->parabirimi_id=$birim;
                            $depoteslim->parabirimi2_id=$birim2;
                            $depoteslim->save();
                        }
                    }
                    if(count($garantisayaclist)>0){
                        $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                            ->where('depodurum', 0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',0)->first();
                        if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                        {
                            $secilenlist = explode(',', $depoteslim->secilenler);
                            $secilenler="";
                            $adet = 0;
                            foreach($garantisayaclist as $sayacgelenid){
                                if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                    $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                    $adet++;
                                }
                            }
                            if ($adet>0) {
                                $depoteslim->secilenler .= ',' . $secilenler;
                                $depoteslim->sayacsayisi += $adet;
                                $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                $depoteslim->save();
                            }
                        } else { //yeni depo teslimatı yapılacak
                            $depoteslim = new DepoTeslim;
                            $depoteslim->servis_id = $servisid;
                            $depoteslim->netsiscari_id = $netsiscariid;
                            $depoteslim->secilenler = $garantisayaclar;
                            $depoteslim->sayacsayisi = count($garantisayaclist);
                            $depoteslim->depodurum = 0;
                            $depoteslim->tipi = 1;
                            $depoteslim->parabirimi_id=$birim;
                            $depoteslim->parabirimi2_id=$birim2;
                            $depoteslim->save();
                        }
                    }
                    if(count($iadesayaclist)>0){
                        $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                            ->where('depodurum', 0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',0)->first();
                        if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                        {
                            $secilenlist = explode(',', $depoteslim->secilenler);
                            $secilenler="";
                            $adet = 0;
                            foreach($iadesayaclist as $sayacgelenid){
                                if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                    $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                    $adet++;
                                }
                            }
                            if ($adet>0) {
                                $depoteslim->secilenler .= ',' . $secilenler;
                                $depoteslim->sayacsayisi += $adet;
                                $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                $depoteslim->save();
                            }
                        } else { //yeni depo teslimatı yapılacak
                            $depoteslim = new DepoTeslim;
                            $depoteslim->servis_id = $servisid;
                            $depoteslim->netsiscari_id = $netsiscariid;
                            $depoteslim->secilenler = $iadesayaclar;
                            $depoteslim->sayacsayisi = count($iadesayaclist);
                            $depoteslim->depodurum = 0;
                            $depoteslim->tipi = 2;
                            $depoteslim->parabirimi_id=$birim;
                            $depoteslim->parabirimi2_id=$birim2;
                            $depoteslim->save();
                        }
                    }
                    BackendController::HatirlatmaGuncelle(6, $netsiscariid, $servisid, $sayacsayisi);
                    BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $sayacsayisi);
                    BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                    BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilenid . ' Nolu Ücretlendirme Abone Tarafından Onaylandı.', 'Onaylayan:' . $kullanici->adi_soyadi . ',Onay Numarası:' . $onaylanan->id . ',Ücretlendirme Numarası:' . $ucretlendirilenid);
                    DB::commit();
                    try {
                        $hatirlatma = Hatirlatma::where('hatirlatmatip_id',6)->where('servis_id',$servisid)
                            ->where('netsiscari_id',$netsiscariid)->orderBy('id', 'desc')->first();
                        Mail::send('mail.musterionay', array('ucretlendirilen' => $ucretlendirilen,'netsiscari'=>$netsiscari,'hatirlatma'=>$hatirlatma),
                            function ($message) use ($servisyetkili, $ucretlendirilen,$mailcc,$netsiscari) {
                                if(!is_null($mailcc))
                                    $message->cc($mailcc);
                                $message->to($servisyetkili->email, $servisyetkili->adi_soyadi)->subject('Servis Fiyatlandirma Müşteri Onayı / '.$netsiscari->cariadi);
                            });
                    } catch (Exception $e) {
                        Log::error($e);
                    }
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fiyatlandırma Onaylandı', 'text' => 'Sayaçların Fiyatlandırması Onaylandı.Firma ile irtibata geçebilirsiniz.', 'type' => 'success', 'durum' => 1));
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi', 'type' => 'error'));
                }
            }
        } catch (Exception $e) {
            if(File::exists('assets/onayformu/'.$isim.''))
                File::delete('assets/onayformu/'.$isim.'');
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylama onaylananlara kaydedilemedi', 'type' => 'error'));
        }
    }

    public function postReddet(){
        $rules = ['redneden' => 'required'];
        $validate = Validator::make(Input::all(), $rules);
        $messages = $validate->messages();
        if ($validate->fails()) {
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
        }
        try{
            DB::beginTransaction();
            $redneden = Input::get('redneden');
            $ucretlendirilenid = Input::get('redid');
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            if($ucretlendirilen->durum!=1){
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylama Tamamlanamadı!', 'text' => 'Ücretlendirme Zaten Onaylanmış ya da Reddedilmiş Olabilir!', 'type' => 'warning'));
            }

            $servisid = $ucretlendirilen->servis_id;
            $netsiscariid = $ucretlendirilen->netsiscari_id;
            $secilenler = $ucretlendirilen->secilenler;
            $secilenlist = explode(',', $secilenler);
            $sayacsayisi = count($secilenlist);
            $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenlist)->get();
            $netsiscari = NetsisCari::find($netsiscariid);
            $birimyonetici = Kullanici::where('grup_id',5)->where('aktifdurum',1)->first();
            $servisyetkili = Kullanici::where('id',$ucretlendirilen->kullanici_id)->first();
            $mailcc = null;
            if($birimyonetici){
                if($servisyetkili){
                    if($birimyonetici->id!=$servisyetkili->id){
                        $mailcc = $birimyonetici->email;
                    }
                }else{
                    $servisyetkili = $birimyonetici;
                }
            }
            $ucretlendirilen->durum = 3;
            $ucretlendirilen->reddetmetarihi = date('Y-m-d H:i:s');
            $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
            $ucretlendirilen->tekrarkayittarihi = NULL;
            $ucretlendirilen->gerigonderimtarihi = NULL;
            $ucretlendirilen->musterinotu = $redneden;
            $ucretlendirilen->reddedilenler = $secilenler;
            $ucretlendirilen->save();
            foreach ($arizafiyatlar as $arizafiyat) {
                $arizafiyateski=new ArizaFiyatEski;
                $arizafiyateski->arizafiyat_id=$arizafiyat->id;
                $arizafiyateski->ariza_serino=$arizafiyat->ariza_serino;
                $arizafiyateski->sayac_id=$arizafiyat->sayac_id;
                $arizafiyateski->sayacadi_id=$arizafiyat->sayacadi_id;
                $arizafiyateski->sayaccap_id=$arizafiyat->sayaccap_id;
                $arizafiyateski->ariza_garanti=$arizafiyat->ariza_garanti;
                $arizafiyateski->fiyatdurum=$arizafiyat->fiyatdurum;
                $arizafiyateski->uretimyer_id=$arizafiyat->uretimyer_id;
                $arizafiyateski->arizakayit_id=$arizafiyat->arizakayit_id;
                $arizafiyateski->depogelen_id=$arizafiyat->depogelen_id;
                $arizafiyateski->sayacgelen_id=$arizafiyat->sayacgelen_id;
                $arizafiyateski->netsiscari_id=$arizafiyat->netsiscari_id;
                $arizafiyateski->degisenler=$arizafiyat->degisenler;
                $arizafiyateski->genel=$arizafiyat->genel;
                $arizafiyateski->ozel=$arizafiyat->ozel;
                $arizafiyateski->ucretsiz=$arizafiyat->ucretsiz;
                $arizafiyateski->fiyat=$arizafiyat->fiyat;
                $arizafiyateski->indirim=$arizafiyat->indirim;
                $arizafiyateski->indirimorani=$arizafiyat->indirimorani;
                $arizafiyateski->tutar=$arizafiyat->tutar;
                $arizafiyateski->kdv=$arizafiyat->kdv;
                $arizafiyateski->toplamtutar=$arizafiyat->toplamtutar;
                $arizafiyateski->parabirimi_id=$arizafiyat->parabirimi_id;
                $arizafiyateski->durum=$arizafiyat->durum;
                $arizafiyateski->kullanici_id=$arizafiyat->kullanici_id;
                $arizafiyateski->kayittarihi=$arizafiyat->kayittarihi;
                $arizafiyateski->kurtarihi=$arizafiyat->kurtarihi;
                $arizafiyateski->tekrarkayittarihi=$arizafiyat->tekrarkayittarihi;
                $arizafiyateski->save();
                $arizafiyat->durum = 2;
                $arizafiyat->save();
                $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                $sayacgelen->fiyatlandirma = 0;
                $sayacgelen->save();
                $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                $servistakip->durum = 6;
                $servistakip->reddetmetarihi= date('Y-m-d H:i:s');
                $servistakip->save();
            }
            BackendController::HatirlatmaGuncelle(6,$netsiscariid,$servisid,$sayacsayisi);
            BackendController::HatirlatmaEkle(7,$netsiscariid,$servisid,$sayacsayisi);
            BackendController::BildirimEkle(6,$netsiscariid,$servisid,$sayacsayisi);
            BackendController::IslemEkle(3,Auth::user()->id,'label-danger','fa-user',$ucretlendirilenid.' Nolu Ücretlendirme Abone Tarafından Reddedildi.','Reddeden:'.Auth::user()->adi_soyadi.',Reddetme Nedeni:'.$redneden.',Ücretlendirme Numarası:'.$ucretlendirilenid);
            DB::commit();
            try {
                $hatirlatma = Hatirlatma::where('hatirlatmatip_id',6)->where('servis_id',$servisid)
                    ->where('netsiscari_id',$netsiscariid)->orderBy('id', 'desc')->first();
                Mail::send('mail.musterionay', array('ucretlendirilen' => $ucretlendirilen,'netsiscari'=>$netsiscari,'hatirlatma'=>$hatirlatma),
                    function ($message) use ($servisyetkili, $ucretlendirilen,$mailcc,$netsiscari) {
                        if(!is_null($mailcc))
                            $message->cc($mailcc);
                        $message->to($servisyetkili->email, $servisyetkili->adi_soyadi)->subject('Servis Fiyatlandirma Müşteri Reddi / '.$netsiscari->cariadi);
                    });
            } catch (Exception $e) {
                Log::error($e);
            }
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fiyatlandırma Reddedildi', 'text' => 'Sayaçların Fiyatlandırması Reddedildi.Firma ile irtibata geçebilirsiniz.', 'type' => 'success', 'durum' => 1));
        }catch (Exception $e){
            Log::error($e);
            DB::rollBack();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Reddetme Hatası', 'text' => 'Reddetme kaydedilemedi', 'type' => 'error'));
        }
    }

    public function postMusterionay(){
        try {
            $ucretlendirilenid = Input::get('ucretlendirilen');
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            if ($ucretlendirilen) {
                $ucretlendirilen->uretimyer = UretimYer::find($ucretlendirilen->uretimyer_id);
                $ucretlendirilen->yetkili = Yetkili::where('netsiscari_id', $ucretlendirilen->netsiscari_id)->where('aktif', 1)->first();
                if (!$ucretlendirilen->yetkili) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Seçilen yerin yetkili kişisi ekli değil', 'type' => 'warning', 'title' => 'Rapor Hatası'));
                }

                $raporadi = "Fiyatlandirma-" . Str::slug($ucretlendirilen->uretimyer->yeradi);
                $export = "pdf";
                $kriterler = array();
                $kriterler['id'] = $ucretlendirilenid;

                JasperPHP::process(public_path( 'reports/fiyatlandirma/fiyatlandirma.jasper'), public_path('reports/outputs/fiyatlandirma/' . $raporadi),
                    array($export), $kriterler,
                    Config::get('database.connections.report'))->execute();
                if ($export == 'pdf') {
                    header("Content-type:application/pdf");
                    header("Content-Disposition:inline;filename=" . $raporadi . "." . $export . "");
                } else if ($export == 'xls') {
                    header("Content-Type:   application/vnd.ms-excel");
                    header("Content-Disposition: inline; filename=" . $raporadi . "." . $export . "");
                } else {
                    //header('Content-Type: application/octet-stream');
                    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                    header("Content-Disposition: inline; filename=" . $raporadi . "." . $export . "");
                }
                readfile("reports/outputs/fiyatlandirma/" . $raporadi . "." . $export);
                File::delete("reports/outputs/fiyatlandirma/" . $raporadi . "." . $export);
                return Redirect::back()->with(array('mesaj' => 'false'));
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Rapor alınacak ucretlendirme seçilmedi', 'type' => 'warning', 'title' => 'Rapor Hatası'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning', 'title' => 'Rapor Hatası'));
        }
    }

    public function getSayackayit() {
        return View::make('abone.sayackayit')->with(array('title'=>'Sayaç Kayıdı'));
    }

    public function postSayackayitlist() {
        $netsiscari_id=Input::get('netsiscari_id');
        $sube = null;
        if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $query = AboneSayacKayit::whereIn('abonesayackayit.netsiscari_id',$netsiscarilist)
                ->select(array("abonesayackayit.id","abonesayackayit.belgeno","kargofirma.kargoadi","abonesayackayit.gondermetarihi","sayactur.tur","abonesayackayit.adet",
                    "abonesayackayit.gdurum","kullanici.adi_soyadi","abonesayackayit.kabultarihi","abonesayackayit.ggondermetarihi","abonesayackayit.gkabultarihi",
                    "abonesayackayit.ndurum","kargofirma.nkargoadi","sayactur.ntur","kullanici.nadi_soyadi"))
                ->leftjoin("kullanici", "abonesayackayit.kullanici_id", "=", "kullanici.id")
                ->leftjoin("sayactur", "abonesayackayit.sayactur_id", "=", "sayactur.id")
                ->leftjoin("kargofirma", "abonesayackayit.kargofirma_id", "=", "kargofirma.id");
        }else{
            $query = AboneSayacKayit::select(array("abonesayackayit.id","abonesayackayit.belgeno","kargofirma.kargoadi","abonesayackayit.gondermetarihi","sayactur.tur",
                    "abonesayackayit.adet","abonesayackayit.gdurum","kullanici.adi_soyadi","abonesayackayit.kabultarihi","abonesayackayit.ggondermetarihi",
                    "abonesayackayit.gkabultarihi","abonesayackayit.ndurum","kargofirma.nkargoadi","sayactur.ntur","kullanici.nadi_soyadi"))
                ->leftjoin("kullanici", "abonesayackayit.kullanici_id", "=", "kullanici.id")
                ->leftjoin("sayactur", "abonesayackayit.sayactur_id", "=", "sayactur.id")
                ->leftjoin("kargofirma", "abonesayackayit.kargofirma_id", "=", "kargofirma.id");
        }

        return Datatables::of($query)
            ->editColumn('gondermetarihi', function ($model) {
                $date = new DateTime($model->gondermetarihi);
                return $date->format('d-m-Y');})
            ->editColumn('kabultarihi', function ($model) {
                if($model->kabultarihi){
                    $date = new DateTime($model->kabultarihi);
                    return $date->format('d-m-Y');
                }else{
                    return '';
                }
            })
            ->addColumn('islemler',function ($model) {
                $root = BackendController::getRootDizin();
                if($model->gdurum=="Bekliyor")
                    return "<a class='btn btn-sm btn-warning' href='".$root."/abone/sayackayitduzenle/".$model->id."' > Düzenle </a>
                    <a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                else
                    return "<a class='btn btn-sm btn-info' href='".$root."/abone/sayackayitgoster/".$model->id."' > Göster </a>";
            })
            ->make(true);
    }

    public function getSayackayitekle() {
        $netsiscariid=Auth::user()->netsiscari_id;
        $kargofirmalari = KargoFirma::orderBy('kargoadi','asc')->get();
        $netsiscariler = NetsisCari::whereIn('id',$netsiscariid)->where('caridurum','A')->whereIn('caritipi',array('A','D'))
            ->orderBy('carikod','asc')->get();

        return View::make('abone.sayackayitekle',array('kargofirmalari'=>$kargofirmalari,'netsiscariler'=>$netsiscariler,'netsiscariid'=>$netsiscariid))->with(array('title'=>' Sayaç Kayıdı Ekle'));
    }

    public function postSayackayitekle() {
        try {
            $rules = ['gondermetarihi' => 'required', 'kargoadi' => 'required', 'belgeno' => 'required', 'serino' => 'required', 'cariadi' => 'required', 'arizaneden' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }

            $tarih = Input::get('gondermetarihi');
            $gondermetarihi = date("Y-m-d", strtotime($tarih));
            $kargoadi = Input::get('kargoadi');
            $belgeno = Input::get('belgeno');
            $netsiscariid = Input::get('cariadi');
            $sayacturid = Input::get('sayactur');
            $serinolar = Input::get('serino');
            $arizanedenleri = Input::get('arizaneden');
            if (count($serinolar) > 0) {
                for ($i = 0; $i < count($serinolar); $i++) {
                    $serino1 = $serinolar[$i];
                    if ($serino1 == "")
                        continue;

                    if(isset($arizanedenleri[$i])){
                        if($arizanedenleri[$i]==""){
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ariza Nedeni Hatası', 'text' => 'Seri Numarası Girilen Sayacın Arıza Nedeni Boş Geçilmiş!', 'type' => 'error'));
                        }
                    }
                    if (count($serinolar) > 1) {
                        for ($j = $i + 1; $j < count($serinolar); $j++) {
                            $serino2 = $serinolar[$j];
                            if ($serino2 == "")
                                continue;
                            if ($serino1 == $serino2) {
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Aynı Seri Numarası Girilmiş', 'type' => 'error'));
                            }
                        }
                    }
                }
                DB::beginTransaction();
                try {
                    $adet = 0;
                    $abonesayackayit = new AboneSayacKayit;
                    $abonesayackayit->belgeno = $belgeno;
                    $abonesayackayit->kargofirma_id = $kargoadi;
                    $abonesayackayit->gondermetarihi = $gondermetarihi;
                    $abonesayackayit->durum = 0;
                    $abonesayackayit->netsiscari_id = $netsiscariid;
                    $abonesayackayit->sayactur_id = $sayacturid;
                    $abonesayackayit->save();

                    for($i=0;$i<count($serinolar);$i++){
                        if($serinolar[$i]!=""){
                            $kayitbilgi = new AboneSayacKayitBilgi;
                            $kayitbilgi->abonesayackayit_id=$abonesayackayit->id;
                            $kayitbilgi->serino = $serinolar[$i];
                            $kayitbilgi->arizanedeni = $arizanedenleri[$i];
                            $kayitbilgi->save();
                            $adet++;
                        }
                    }
                    $abonesayackayit->adet = $adet;
                    $abonesayackayit->save();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Girişi Kaydedilemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                }
                DB::commit();
                return Redirect::to('abone/sayackayit')->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Yapıldı', 'text' => 'Sayac Kayıdı Başarıyla Yapıldı', 'type' => 'success'));
            }else{
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaç Girişi Kaydedilemedi', 'text' => 'Girilen Sayaçların Seri Numaraları Yazılmamış', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Arızalı Sayaç Kayıdı Yapılamadı', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getSayackayitsil($id){
        try {
            DB::beginTransaction();
            $sayackayit = AboneSayacKayit::find($id);
            if ($sayackayit->durum==1) {
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Silinemez', 'text' => 'Sayaçlar İçin Merkez tarafından onay işlemi var!', 'type' => 'error'));
            }
            AboneSayacKayitBilgi::where('abonesayackayit_id',$sayackayit->id)->delete();
            $sayackayit->delete();
            DB::commit();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Silindi', 'text' => 'Sayaç Kayıdı Başarıyla Silindi.', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Silinemedi', 'text' => 'Sayaç Kayıdı Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getSayackayitduzenle($id) {
        $sayackayit = AboneSayacKayit::find($id);
        if ($sayackayit->durum) {
            return Redirect::to('abone/sayackayit')->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Güncellenemez', 'text' => 'Sayaçlar İçin Merkez tarafından onay işlemi var!', 'type' => 'error'));
        }
        $sayackayitbilgi = AboneSayacKayitBilgi::where('abonesayackayit_id',$sayackayit->id)->get();
        $netsiscariid=Auth::user()->netsiscari_id;
        $kargofirmalari = KargoFirma::orderBy('kargoadi','asc')->get();
        $netsiscariler = NetsisCari::whereIn('id',$netsiscariid)->where('caridurum','A')->whereIn('caritipi',array('A','D'))
            ->orderBy('carikod','asc')->get();
        $netsiscari = NetsisCari::find($sayackayit->netsiscari_id);
        if($netsiscari->caridurum!="A")
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
        return View::make('abone.sayackayitduzenle',array('sayackayit'=>$sayackayit,'sayackayitbilgi'=>$sayackayitbilgi,'kargofirmalari'=>$kargofirmalari,'netsiscari'=>$netsiscari,'netsiscariler'=>$netsiscariler))->with(array('title'=>'Sayaç Kayıdı Düzenleme Ekranı'));
    }

    public function postSayackayitduzenle($id) {
        try {
            $rules = ['gondermetarihi' => 'required', 'kargoadi' => 'required', 'belgeno' => 'required', 'serino' => 'required', 'cariadi' => 'required', 'arizaneden' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }

            DB::beginTransaction();
            $abonesayackayit = AboneSayacKayit::find($id);
            $tarih = Input::get('gondermetarihi');
            $gondermetarihi = date("Y-m-d", strtotime($tarih));
            $kargoadi = Input::get('kargoadi');
            $belgeno = Input::get('belgeno');
            $netsiscariid = Input::get('cariadi');
            $sayacturid = Input::get('sayactur');
            $serinolar = Input::get('serino');
            $arizanedenleri = Input::get('arizaneden');
            if (count($serinolar) > 0) {
                $durumlar = array();
                for ($i = 0; $i < count($serinolar); $i++) {
                    $serino1 = $serinolar[$i];
                    array_push($durumlar,0);
                    if ($serino1 == "")
                        continue;

                    if(isset($arizanedenleri[$i])){
                        if($arizanedenleri[$i]==""){
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ariza Nedeni Hatası', 'text' => 'Seri Numarası Girilen Sayacın Arıza Nedeni Boş Geçilmiş!', 'type' => 'error'));
                        }
                    }
                    if (count($serinolar) > 1) {
                        for ($j = $i + 1; $j < count($serinolar); $j++) {
                            $serino2 = $serinolar[$j];
                            if ($serino2 == "")
                                continue;
                            if ($serino1 == $serino2) {
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Aynı Seri Numarası Girilmiş', 'type' => 'error'));
                            }
                        }
                    }
                }
                try {
                    $adet = 0;
                    $abonesayackayit->belgeno = $belgeno;
                    $abonesayackayit->kargofirma_id = $kargoadi;
                    $abonesayackayit->gondermetarihi = $gondermetarihi;
                    $abonesayackayit->durum = 0;
                    $abonesayackayit->netsiscari_id = $netsiscariid;
                    $abonesayackayit->sayactur_id = $sayacturid;
                    $abonesayackayit->save();

                    $kayitbilgiler = AboneSayacKayitBilgi::where('abonesayackayit_id',$abonesayackayit->id)->get();
                    foreach($kayitbilgiler as $kayitbilgi){
                        $flag = 0;
                        for($i=0;$i<count($serinolar);$i++){ //serino aynı kalanlar
                            if(!$durumlar[$i]){
                                if($serinolar[$i]!=""){
                                    if($serinolar[$i]==$kayitbilgi->serino){
                                        $durumlar[$i] = 1;
                                        $flag = 1;
                                        $kayitbilgi->abonesayackayit_id=$abonesayackayit->id;
                                        $kayitbilgi->serino = $serinolar[$i];
                                        $kayitbilgi->arizanedeni = $arizanedenleri[$i];
                                        $kayitbilgi->save();
                                        $adet++;
                                        break;
                                    }
                                }
                            }
                        }
                        if(!$flag) { //serino değişenler
                            $kayitbilgi->delete();
                        }
                    }
                    for($i=0;$i<count($serinolar);$i++){ //yeni eklemeler
                        if(!$durumlar[$i]){
                            if($serinolar[$i]!=""){
                                $kayitbilgi = new AboneSayacKayitBilgi;
                                $kayitbilgi->abonesayackayit_id=$abonesayackayit->id;
                                $kayitbilgi->serino = $serinolar[$i];
                                $kayitbilgi->arizanedeni = $arizanedenleri[$i];
                                $kayitbilgi->save();
                                $adet++;
                            }
                        }
                    }
                    $abonesayackayit->adet = $adet;
                    $abonesayackayit->save();

                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Girişi Güncellenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                }
                DB::commit();
                return Redirect::to('abone/sayackayit')->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Güncellendi', 'text' => 'Sayac Kayıdı Başarıyla Güncellendi', 'type' => 'success'));
            }else{
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaç Girişi Güncellenemedi', 'text' => 'Girilen Sayaçların Seri Numaraları Yazılmamış', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Arızalı Sayaç Kayıdı Güncellenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function postSayackayit()
    {
        try{
                $sayackayitid=Input::get('liste');
                $sayackayit = AboneSayacKayit::find($sayackayitid);
                if ($sayackayit) {

                    $sayackayit->netsiscari=NetsisCari::find($sayackayit->netsiscari_id);
                    $raporadi="SayacListesi-".Str::slug($sayackayit->netsiscari->cariadi);
                    $export="pdf";
                    $kriterler=array();
                    $kriterler['id']=$sayackayitid;

                    JasperPHP::process( public_path('reports/abonesayackayitlistesi/abonesayackayitlistesi.jasper'), public_path('reports/outputs/abonesayackayitlistesi/'.$raporadi),
                        array($export), $kriterler,
                        Config::get('database.connections.report') )->execute();
                    if($export=='pdf'){
                        header("Content-type:application/pdf");
                        header("Content-Disposition:inline;filename=".$raporadi.".".$export."");
                    }else if($export=='xls'){
                        header("Content-Type:   application/vnd.ms-excel");
                        header("Content-Disposition: inline; filename=".$raporadi.".".$export."");
                    }else{
                        //header('Content-Type: application/octet-stream');
                        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                        header("Content-Disposition: inline; filename=".$raporadi.".".$export."");
                    }
                    readfile("reports/outputs/abonesayackayitlistesi/".$raporadi.".".$export."");
                    File::delete("reports/outputs/abonesayackayitlistesi/".$raporadi.".".$export."");
                    return Redirect::back()->with(array('mesaj' => 'false'));
                } else {
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' =>'Sayaç Listesi alınacak kayıt seçilmedi', 'type' => 'warning','title' => 'Rapor Hatası'));
                }
        }catch (Exception $e){
            return Redirect::back()->with(array('mesaj' => 'true', 'text' =>'Rapor Alınırken Hata ile karşılaşıldı'.str_replace("'","\'",$e->getMessage()), 'type' => 'error','title' => 'Rapor Hatası'));
        }
    }

    public function getSayackayitgoster($id) {
        try {
            $sayackayit = AboneSayacKayit::find($id);
            $sayackayitbilgi = AboneSayacKayitBilgi::where('abonesayackayit_id',$sayackayit->id)->get();
            $sayackayit->kargofirma = KargoFirma::find($sayackayit->kargofirma_id);
            $sayackayit->netsiscari = NetsisCari::find($sayackayit->netsiscari_id);
            switch ($sayackayit->sayactur_id){
                case 1 : $sayackayit->sayactur = 'Ön Ödemeli Su Sayaçları';
                        break;
                case 2 : $sayackayit->sayactur = 'Ön Ödemeli Elektrik Sayaçları';
                    break;
                case 3 : $sayackayit->sayactur = 'Ön Ödemeli Gaz Sayaçları';
                    break;
                case 4 : $sayackayit->sayactur = 'Ön Ödemeli Isı Sayaçları';
                    break;
                case 5 : $sayackayit->sayactur = 'Mekanik Gaz Sayaçları';
                    break;
                default: $sayackayit->sayactur = '';
            }
            return View::make('abone.sayackayitgoster',array('sayackayit'=>$sayackayit,'sayackayitbilgi'=>$sayackayitbilgi))->with(array('title'=>'Sayaç Kayıdı Bilgi Ekranı'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Bilgilerinde Hata Var!', 'text' => str_replace("'","'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getArizakayit() {
            return View::make('abone.arizakayit')->with(array('title'=>'Servis Arıza Kayıdı'));
    }

    public function postArizakayit()
    {
        try {
            $arizaid = Input::get('arizaid');
            $arizakayit = ArizaKayit::find($arizaid);
            if ($arizakayit) {
                $arizakayit->sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Rapor alınacak sayaç seçilmedi', 'type' => 'warning', 'title' => 'Rapor Hatası'));
            }
            $raporadi = "ServisRaporu-" . Str::slug($arizakayit->sayacgelen->serino);
            $export = "pdf";
            $kriterler = array();
            $kriterler['id'] = $arizaid;
            if($arizakayit->sayacgelen->servis_id==5){
                JasperPHP::process(public_path('reports/servisraporu/servisraporugaz.jasper'), public_path('reports/outputs/servisraporu/' . $raporadi),
                    array($export), $kriterler,
                    Config::get('database.connections.report'))->execute();
                if ($export == 'pdf') {
                    header("Content-type:application/pdf");
                    header("Content-Disposition:inline;filename=" . $raporadi . "." . $export . "");
                } else if ($export == 'xls') {
                    header("Content-Type:   application/vnd.ms-excel");
                    header("Content-Disposition: inline; filename=" . $raporadi . "." . $export . "");
                } else {
                    //header('Content-Type: application/octet-stream');
                    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                    header("Content-Disposition: inline; filename=" . $raporadi . "." . $export . "");
                }
                readfile("reports/outputs/servisraporu/" . $raporadi . "." . $export . "");
                File::delete("reports/outputs/servisraporu/" . $raporadi . "." . $export . "");
            }else{
                JasperPHP::process(public_path('reports/servisraporu/servisraporu.jasper'), public_path('reports/outputs/servisraporu/' . $raporadi),
                    array($export), $kriterler,
                    Config::get('database.connections.report'))->execute();
                if ($export == 'pdf') {
                    header("Content-type:application/pdf");
                    header("Content-Disposition:inline;filename=".$raporadi.".".$export."");
                } else if ($export == 'xls') {
                    header("Content-Type:   application/vnd.ms-excel");
                    header("Content-Disposition: inline; filename=".$raporadi.".".$export."");
                } else {
                    //header('Content-Type: application/octet-stream');
                    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                    header("Content-Disposition: inline; filename=".$raporadi.".".$export."");
                }
                readfile("reports/outputs/servisraporu/" . $raporadi . "." . $export . "");
                File::delete("reports/outputs/servisraporu/" . $raporadi . "." . $export . "");
            }
            return Redirect::back()->with(array('mesaj' => 'false'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning', 'title' => 'Rapor Hatası'));
        }
    }

    public function postArizakayitlist() {
        $netsiscari_id=Input::get('netsiscari_id');
        if($netsiscari_id){
            $netsiscarilist=explode(',',$netsiscari_id);
            $query = ArizaKayit::whereIn('sayacgelen.netsiscari_id',$netsiscarilist)
                ->select(array("arizakayit.id", "sayacgelen.serino", "sayacadi.sayacadi", "uretimyer.yeradi","arizakayit.gdurum","kullanici.adi_soyadi",
                    "arizakayit.arizakayittarihi","servistakip.eskiserino","arizakayit.garizakayittarihi","sayacadi.nsayacadi","uretimyer.nyeradi",
                    "arizakayit.ndurum","kullanici.nadi_soyadi","sayacgelen.fiyatlandirma", "sayacgelen.depoteslim"))
                ->leftjoin("sayacgelen", "arizakayit.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("servistakip", "servistakip.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("uretimyer", "sayacgelen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("sayacadi", "arizakayit.sayacadi_id", "=", "sayacadi.id")
                ->leftjoin("kullanici", "arizakayit.arizakayit_kullanici_id", "=", "kullanici.id");
        }else{
            $query = ArizaKayit::where('sayacgelen.servis_id',0)
                ->select(array("arizakayit.id", "sayacgelen.serino", "sayacadi.sayacadi", "uretimyer.yeradi","arizakayit.gdurum","kullanici.adi_soyadi",
                    "arizakayit.arizakayittarihi","servistakip.eskiserino","arizakayit.garizakayittarihi","sayacadi.nsayacadi","uretimyer.nyeradi",
                    "arizakayit.ndurum","kullanici.nadi_soyadi","sayacgelen.fiyatlandirma", "sayacgelen.depoteslim"))
                ->leftjoin("sayacgelen", "arizakayit.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("servistakip", "servistakip.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("uretimyer", "sayacgelen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("sayacadi", "arizakayit.sayacadi_id", "=", "sayacadi.id")
                ->leftjoin("kullanici", "arizakayit.arizakayit_kullanici_id", "=", "kullanici.id");
        }
        return Datatables::of($query)
            ->editColumn('serino', function ($model) {
                return $model->serino.($model->eskiserino ? " (".$model->eskiserino.")" : "");
            })
            ->editColumn('arizakayittarihi', function ($model) {
                $date = new DateTime($model->arizakayittarihi);
                return $date->format('d-m-Y');})
            ->addColumn('islemler',function ($model)use($netsiscari_id) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-info' href='".$root."/abone/arizakayitgoster/".$model->id."' > Göster </a>";
            })
            ->make(true);
    }

    public function getArizakayitgoster($id) {
        try {
            $arizakayit = ArizaKayit::find($id);
            $arizakayit->servistakip=ServisTakip::where('arizakayit_id',$arizakayit->id)->first();
            $arizakayit->sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
            $arizakayit->uretimyer = Uretimyer::find($arizakayit->sayacgelen->uretimyer_id);
            $arizakayit->sayac = Sayac::find($arizakayit->sayac_id);
            $arizakayit->sayacadi = SayacAdi::find($arizakayit->sayacadi_id);
            $arizakayit->sayaccap = SayacCap::find($arizakayit->sayaccap_id);
            $arizakayit->sayacparca = SayacParca::where('sayacadi_id',$arizakayit->sayacadi_id)->where('sayaccap_id',$arizakayit->sayaccap_id)->first();
            $arizakayit->sayacparcalar = explode(',',$arizakayit->sayacparca->parcalar);
            $arizakayit->netsiscari = NetsisCari::find($arizakayit->netsiscari_id);
            $arizakayit->servisstokkod = ServisStokKod::where('stokkodu',$arizakayit->sayacgelen->stokkodu)->first();
            $arizakayit->sayacariza = SayacAriza::find($arizakayit->sayacariza_id);
            $arizakayit->sayacyapilan = SayacYapilan::find($arizakayit->sayacyapilan_id);
            $arizakayit->sayacdegisen = SayacDegisen::find($arizakayit->sayacdegisen_id);
            $arizakayit->sayacuyari = SayacUyari::find($arizakayit->sayacuyari_id);
            $arizakayit->problemler = explode(',',$arizakayit->sayacariza ? $arizakayit->sayacariza->problemler : '');
            $arizakayit->yapilanlar = explode(',',$arizakayit->sayacyapilan ? $arizakayit->sayacyapilan->yapilanlar : '');
            $arizakayit->degisenler = explode(',',$arizakayit->sayacdegisen ? $arizakayit->sayacdegisen->degisenler : '');
            $arizakayit->uyarilar = explode(',',$arizakayit->sayacuyari ? $arizakayit->sayacuyari->uyarilar: '');
            $arizakayit->resimlist = explode(',',$arizakayit->resimler);
            $arizakodlari = ArizaKod::where('sayactur_id',$arizakayit->servistakip->servis_id)->get();
            $yapilanlar = Yapilanlar::where('sayactur_id',$arizakayit->servistakip->servis_id)->get();
            $degisenler = Degisenler::where('sayactur_id',$arizakayit->servistakip->servis_id)->whereIn('id',$arizakayit->sayacparcalar)->get();
            $uyarilar = Uyarilar::where('sayactur_id',$arizakayit->servistakip->servis_id)->orderBy('kullanim','desc')->orderBy('tanim','asc')->get();
            return View::make('abone.arizakayitgoster',array('arizakayit'=>$arizakayit,'arizakodlari'=>$arizakodlari,'yapilanlar'=>$yapilanlar,'degisenler'=>$degisenler,'uyarilar'=>$uyarilar))->with(array('title'=>' Arıza Kayıdı Bilgi Ekranı'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Bilgisi Getirilemedi.', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }
}
