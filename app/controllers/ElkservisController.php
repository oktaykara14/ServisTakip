<?php
//transaction işlemi tamamlandı
class ElkservisController extends BackendController {
    public $servisadi = 'elkservis';
    public $servisid = 2;
    public $servisbilgi = 'Elektrik';

    public function getSayackayit($hatirlatma_id=false) {
        if($hatirlatma_id)
            return View::make($this->servisadi.'.sayackayit',array('hatirlatma_id'=>$hatirlatma_id))->with(array('title'=>$this->servisbilgi.' Servis Sayaç Kayıdı'));
        else
            return View::make($this->servisadi.'.sayackayit')->with(array('title'=>$this->servisbilgi.' Servis Sayaç Kayıdı'));
    }

    public function postSayackayitlist() {
        $hatirlatma_id=Input::get('hatirlatma_id');
        $hatirlatma = false;
        if($hatirlatma_id!="") {
            $hatirlatma = Hatirlatma::find($hatirlatma_id);
        }
        if($hatirlatma){
            $depogelenid = array($hatirlatma->depogelen_id);
            $query = SayacGelen::where('sayacgelen.servis_id',$this->servisid)->whereIn('sayacgelen.depogelen_id',$depogelenid)
                ->select(array("sayacgelen.id","sayacgelen.serino","uretimyer.yeradi","servisstokkod.stokadi","sayacgelen.depotarihi","kullanici.adi_soyadi",
                    "sayacgelen.eklenmetarihi","servistakip.eskiserino","sayacgelen.gdepotarihi","sayacgelen.gdurumtarihi","uretimyer.nyeradi","servisstokkod.nstokadi",
                    "kullanici.nadi_soyadi","sayacgelen.arizakayit","sayacgelen.depolararasi"))
                ->leftjoin("servistakip", "servistakip.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("uretimyer", "sayacgelen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("servisstokkod", "sayacgelen.stokkodu", "=", "servisstokkod.stokkodu")
                ->leftjoin("kullanici", "sayacgelen.kullanici_id", "=", "kullanici.id");

        }else{
            $query = SayacGelen::where('sayacgelen.servis_id',$this->servisid)
                ->select(array("sayacgelen.id","sayacgelen.serino","uretimyer.yeradi","servisstokkod.stokadi","sayacgelen.depotarihi","kullanici.adi_soyadi",
                    "sayacgelen.eklenmetarihi","servistakip.eskiserino","sayacgelen.gdepotarihi","sayacgelen.gdurumtarihi","uretimyer.nyeradi","servisstokkod.nstokadi",
                    "kullanici.nadi_soyadi","sayacgelen.arizakayit","sayacgelen.depolararasi"))
                ->leftjoin("servistakip", "servistakip.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("uretimyer", "sayacgelen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("servisstokkod", "sayacgelen.stokkodu", "=", "servisstokkod.stokkodu")
                ->leftjoin("kullanici", "sayacgelen.kullanici_id", "=", "kullanici.id");
        }
        return Datatables::of($query)
            ->editColumn('serino', function ($model) {
                return $model->serino.($model->eskiserino ? "(".$model->eskiserino.")" : "");
            })
            ->editColumn('depotarihi', function ($model) {
                $date = new DateTime($model->depotarihi);
                return $date->format('d-m-Y');})
            ->editColumn('eklenmetarihi', function ($model) {
                $date = new DateTime($model->eklenmetarihi);
                return $date->format('d-m-Y');})
            ->addColumn('islemler',function ($model) {
                $root = BackendController::getRootDizin();
                if(!$model->arizakayit && !$model->depolararasi)
                    return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/sayackayitduzenle/".$model->id."' > Düzenle </a>";
                else
                    return "<a class='btn btn-sm btn-info' href='".$root."/".$this->servisadi."/sayackayitgoster/".$model->id."' > Göster </a>";
            })
            ->make(true);
    }

    public function getSayackayitekle() {
        $uretimyerleri = UretimYer::where('mekanik',0)->where('id','<>',0)->get();
        $netsiscariler = NetsisCari::where('caridurum','A')->whereIn('caritipi',array('A','D'))
            ->whereIn('subekodu',array(-1,8))->whereNotIn('carikod',(function ($query){$query->select('carikod')->from('kodharichar')->where('subekodu', 8);}))
            ->orderBy('carikod','asc')->get();
        $sayacadlari = SayacAdi::where('sayactur_id',$this->servisid)->get();
        $servisstokkodlari = ServisStokKod::where('servisid',$this->servisid)->get();
        return View::make($this->servisadi.'.sayackayitekle',array('uretimyerleri'=>$uretimyerleri,'netsiscariler'=>$netsiscariler,'sayacadlari'=>$sayacadlari,'servisstokkodlari'=>$servisstokkodlari))->with(array('title'=>$this->servisbilgi.' Sayaç Kayıdı Ekle'));
    }

    public function postSayackayitekle() {
        try {
            $rules = ['gelis' => 'required', 'uretimyerleri' => 'required', 'cariadi' => 'required', 'serino' => 'required', 'sayacadlari' => 'required', 'sayaccaplari' => 'required', 'serviskodlari' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $tarih = Input::get('gelis');
            $gelistarih = date("Y-m-d", strtotime($tarih));
            $uretimyerleri = Input::get('uretimyerleri');
            $netsiscari_id = Input::get('cariadi');
            $serinolar = Input::get('serino');
            $serviskodlari = Input::get('serviskodlari');
            $sayacadlari = Input::get('sayacadlari');
            $sayaccaplari = Input::get('sayaccaplari');
            $eklenenkayit = Input::get('eklenenkayit');
            $netsiscari = NetsisCari::find($netsiscari_id);
            $abonesayackayit = null;
            $eklenenadet = 0;
            if($eklenenkayit!=""){
                $abonesayackayit = AboneSayacKayit::find($eklenenkayit);
            }
            $subedurum = 0;
            if (count($serinolar) > 0) {
                for ($i = 0; $i < count($serinolar); $i++) {
                    $serino1 = $serinolar[$i];
                    $yeri = $uretimyerleri[$i];
                    if ($serino1 == "")
                        continue;
                    if(BackendController::SayacDurum($serino1,$yeri,$this->servisid,$subedurum)){
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => $serino1.' Nolu Sayacın Depo Çıkışı Yapılmamış.Önce Sayaca Ait Diğer İşlemleri Bitiriniz.', 'type' => 'error'));
                    }
                    if(isset($uretimyerleri[$i])){
                        if($uretimyerleri[$i]==""){
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Hatası', 'text' => 'Seri Numarası Girilen Sayacın Geliş Yeri Boş Geçilmiş!', 'type' => 'error'));
                        }
                    }
                    if(isset($sayacadlari[$i])){
                        if($sayacadlari[$i]==""){
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Hatası', 'text' => 'Seri Numarası Girilen Sayacın Sayaç Adı Boş Geçilmiş!', 'type' => 'error'));
                        }
                    }
                    if(isset($sayaccaplari[$i])){
                        if($sayaccaplari[$i]==""){
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaç Çapı Hatası', 'text' => 'Seri Numarası Girilen Sayacın Sayaç Çapı Boş Geçilmiş!', 'type' => 'error'));
                        }
                    }
                    if(isset($serviskodlari[$i])){
                        if($serviskodlari[$i]==""){
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Servis Kodu Hatası', 'text' => 'Seri Numarası Girilen Sayacın İstek Kısmı Boş Geçilmiş!', 'type' => 'error'));
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
                $sayaclar = BackendController::DepoGirisGrupla($serinolar, $uretimyerleri, $serviskodlari, $sayacadlari, $sayaccaplari);
                if (count($sayaclar) == 0) {
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Depo Sayaç Girişi Kaydedilemedi', 'text' => 'Girilen Sayaçların Seri Numaraları yazılmamış', 'type' => 'error'));
                }
                DB::beginTransaction();
                if (Input::has('belgeli')) {
                    $belgeno = BackendController::FaturaNo(Input::get('belgeno'), 0);
                } else {
                    try {
                        $faturano = Fatuno::where('SUBE_KODU', 8)->where('SERI', 'Z')->where('TIP', '9')->first();
                        if (!$faturano) {
                            $faturano = new Fatuno;
                            $faturano->SUBE_KODU = 8;
                            $faturano->SERI = 'Z';
                            $faturano->TIP = 9;
                            $faturano->NUMARA = 'Z' . '0';
                            $faturano->save();
                        }
                        $belgeno = BackendController::FaturaNo($faturano->NUMARA, 1);
                        Fatuno::where(['SUBE_KODU'=> 8,'SERI' => 'Z','TIP' => '9'])->update(['NUMARA' => $belgeno]);
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Fatura Numarası Kaydedilemedi', 'text' => 'Girilen Sayaçlar için Fatura Numarası Alınamadı', 'type' => 'error'));
                    }
                }
                $depogiris = BackendController::NetsisDepoGiris($sayaclar,$gelistarih,$netsiscari_id,$belgeno);
                if ($depogiris['durum'] == '0') {
                    DB::rollBack();
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => $depogiris['text'], 'type' => 'error'));
                }
                $adet = 0;
                $eklenenler = "";
                $faturakalemler = $depogiris['faturakalemler'];
                try {
                    foreach ($faturakalemler as $inckey) {
                        $dbname = 'MANAS' . date('Y');
                        $depogelen = DepoGelen::where('db_name', $dbname)->where('inckeyno', $inckey)->first();
                        $now = time();
                        while ($now + 30 > time()) {
                            $depogelen = DepoGelen::where('db_name', $dbname)->where('inckeyno', $inckey)->first();
                            if ($depogelen)
                                break;
                        }
                        if ($depogelen) {
                            $servisid=$depogelen->servis_id;
                            $depogelen->kod = ServisStokKod::where('stokkodu', $depogelen->servisstokkodu)->first();
                            foreach ($sayaclar as $sayacgrup) {
                                if ($sayacgrup['kod'] == $depogelen->kod->id) {
                                    $biten = 0;
                                    $sayac = $sayacgrup['sayac']; //eklenecek sayaçlar
                                    foreach ($sayac as $girilecek) {
                                        $serino = $girilecek['serino'];
                                        $sayacadi = $girilecek['sayacadi'];
                                        $sayaccap = $girilecek['sayaccap'];
                                        $uretimyeri = $girilecek['uretimyeri'];
                                        if ($serino != "") {
                                            try {
                                                $sayacgelen = new SayacGelen;
                                                $sayacgelen->depogelen_id = $depogelen->id;
                                                $sayacgelen->netsiscari_id = $netsiscari_id;
                                                $sayacgelen->stokkodu = $depogelen->servisstokkodu;
                                                $sayacgelen->serino = $serino;
                                                $sayacgelen->depotarihi = $gelistarih;
                                                $sayacgelen->sayacadi_id = $sayacadi;
                                                $sayacgelen->sayaccap_id = $sayaccap;
                                                $sayacgelen->uretimyer_id = $uretimyeri;
                                                $sayacgelen->servis_id = $depogelen->servis_id;
                                                $sayacgelen->kullanici_id = Auth::user()->id;
                                                $sayacgelen->beyanname=-1; // elektrikte beyanname yok
                                                $sayacgelen->save();
                                                $biten++;
                                                $adet++;
                                                $eklenenler .= ($eklenenler == "" ? "" : ",") . $serino;
                                                $abonesayackayitbilgiid = null;
                                                if($eklenenkayit!=""){
                                                    $abonesayackayitbilgi = AboneSayacKayitBilgi::where('abonesayackayit_id',$eklenenkayit)->where('serino',$serino)->first();
                                                    if($abonesayackayitbilgi){
                                                        $abonesayackayitbilgiid = $abonesayackayitbilgi->id;
                                                        $abonesayackayitbilgi->durum=1;
                                                        $abonesayackayitbilgi->save();
                                                        $eklenenadet++;
                                                    }
                                                }
                                                $servistakip = new ServisTakip;
                                                $servistakip->serino = $sayacgelen->serino;
                                                $servistakip->sayacadi_id = $sayacgelen->sayacadi_id;
                                                $servistakip->abonesayackayitbilgi_id = $abonesayackayitbilgiid;
                                                $servistakip->depogelen_id = $sayacgelen->depogelen_id;
                                                $servistakip->netsiscari_id = $sayacgelen->netsiscari_id;
                                                $servistakip->sayacgelen_id = $sayacgelen->id;
                                                $servistakip->servis_id = $sayacgelen->servis_id;
                                                $servistakip->uretimyer_id = $sayacgelen->uretimyer_id;
                                                $servistakip->durum = 1;
                                                $servistakip->depotarih = $sayacgelen->depotarihi;
                                                $servistakip->sayacgiristarihi = $sayacgelen->eklenmetarihi;
                                                $servistakip->kullanici_id = $sayacgelen->kullanici_id;
                                                $servistakip->sonislemtarihi = $sayacgelen->eklenmetarihi;
                                                $servistakip->save();
                                            } catch (Exception $e) {
                                                DB::rollBack();
                                                Log::error($e);
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Servis Takip Kısmına Kayıt Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                            }
                                        }
                                    }
                                    if ($biten > 0) {
                                        try {
                                            if($eklenenadet>0)
                                                if($abonesayackayit){
                                                    if($abonesayackayit->adet==$eklenenadet){
                                                        $abonesayackayit->durum=1;
                                                    }else{
                                                        $abonesayackayit->durum=2;
                                                    }
                                                    $abonesayackayit->kullanici_id=Auth::user()->id;
                                                    $abonesayackayit->kabultarihi=$gelistarih;
                                                    $abonesayackayit->save();
                                                }
                                            BackendController::HatirlatmaGuncelle(2,$netsiscari_id,$servisid,$biten,$depogelen->id,$depogelen->servisstokkodu);
                                            BackendController::DepoDurumGuncelle($depogelen->id);
                                            BackendController::HatirlatmaEkle(3, $netsiscari_id, $servisid, $biten, $depogelen->id,$depogelen->servisstokkodu);
                                            BackendController::BildirimEkle(2, $netsiscari_id, $servisid, $biten, $depogelen->id,$depogelen->servisstokkodu);
                                        } catch (Exception $e) {
                                            DB::rollBack();
                                            Log::error($e);
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Hatırlatma Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                        }
                                    }
                                }
                            }
                        } else {
                            DB::rollBack();
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => 'Depo Gelen Bilgisi Kaydedilmemiş', 'type' => 'error'));
                        }
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                }
                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-thumbs-o-up', $netsiscari->cariadi . ' Yerine Ait ' . $adet . ' Adet Sayacın Sisteme Girişi Yapıldı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Seri Numaraları:' . $eklenenler);
                DB::commit();
                return Redirect::to($this->servisadi.'/sayackayit')->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Yapıldı', 'text' => 'Sayac Kayıdı Başarıyla Yapıldı', 'type' => 'success'));
            }else{
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => 'Girilen Sayaçların Seri Numaraları yazılmamış', 'type' => 'error'));
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
            $sayacgelen = SayacGelen::find($id);
            $bilgi = clone $sayacgelen;
            if ($sayacgelen->arizakayit) {
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Silinemez', 'text' => 'Sayaç İçin Arıza Kayıdı Var!', 'type' => 'error'));
            }
            $depogelen = DepoGelen::find($sayacgelen->depogelen_id);
            $netsiscari = NetsisCari::find($sayacgelen->netsiscari_id);
            if ($depogelen->adet == 1) {
                $depogelen->durum = 2;
                $depogelen->save();
            } else {
                $depogelen->adet--;
                $depogelen->save();
            }
            $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
            $servistakip->sayacgelen_id = NULL;
            $servistakip->save();
            $sayacgelen->delete();
            $servistakip->delete();
            BackendController::HatirlatmaSil(3,$netsiscari->id,$depogelen->servis_id,1,$depogelen->id,$depogelen->servisstokkodu);
            BackendController::BildirimGeriAl(2,$netsiscari->id,$depogelen->servis_id,1,$depogelen->id,$depogelen->servisstokkodu);
            BackendController::HatirlatmaGeriAl(2,$netsiscari->id,$depogelen->servis_id,1,$depogelen->id,$depogelen->servisstokkodu);
            BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-thumbs-o-up', $netsiscari->cariadi . ' Yerine Ait ' . $bilgi->id . ' Numaralı Sayaç Kayıdı Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Sayaç Kayıt Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Silindi', 'text' => 'Sayaç Kayıdı Başarıyla Silindi.', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Silinemedi', 'text' => 'Sayaç Kayıdı Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getSayackayitduzenle($id) {
        $sayacgelen = SayacGelen::find($id);
        if ($sayacgelen->arizakayit) {
            return Redirect::to($this->servisadi.'/sayackayit')->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Güncellenemez', 'text' => 'Sayaç İçin Arıza Kayıdı Var!', 'type' => 'error'));
        }
        $cariyerler=CariYer::where('netsiscari_id',$sayacgelen->netsiscari_id)->where('durum',1)->get(array('uretimyer_id'))->toArray();
        $uretimyerleri = Uretimyer::whereIn('id',$cariyerler)->get();
        $sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
        $sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
        $netsiscari = NetsisCari::find($sayacgelen->netsiscari_id);
        if($netsiscari->caridurum!="A")
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
        $servisstokkod = ServisStokKod::where('stokkodu',$sayacgelen->stokkodu)->first();
        return View::make($this->servisadi.'.sayackayitduzenle',array('sayacgelen'=>$sayacgelen,'uretimyerleri'=>$uretimyerleri,'netsiscari'=>$netsiscari,'sayacadi'=>$sayacadi,'sayaccap'=>$sayaccap,'servisstokkod'=>$servisstokkod))->with(array('title'=>$this->servisbilgi.' Sayaç Kayıdı Düzenleme Ekranı'));
    }

    public function postSayackayitduzenle($id) {
        try {
            $rules = ['uretimyer' => 'required', 'serino' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $sayacgelen = SayacGelen::find($id);
            $bilgi = clone $sayacgelen;
            if ($sayacgelen->arizakayit) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Güncellenemez', 'text' => 'Sayaç İçin Arıza Kayıdı Var!', 'type' => 'error'));
            }
            $netsiscari = NetsisCari::find($sayacgelen->netsiscari_id);
            $servistakip = ServisTakip::where('serino', $sayacgelen->serino)->where('depogelen_id', $sayacgelen->depogelen_id)->first();
            $uretimyer_id = Input::get('uretimyer');
            $serino = Input::get('serino');
            $subedurum = 0;
            if ($servistakip) {
                if ($servistakip->arizakayit_id == null)
                    if (BackendController::SayacDurum($serino,$uretimyer_id, $this->servisid, $subedurum, $servistakip->id)) {
                        Input::flash();
                        DB::rollBack();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => $serino . ' Nolu Sayacın Depo Çıkışı Yapılmamış.Önce Sayaca Ait Diğer İşlemleri Bitiriniz.', 'type' => 'error'));
                    }
                if($servistakip->abonesayackayitbilgi_id != null){
                    $abonesayackayitbilgi = AboneSayacKayitBilgi::find($servistakip->abonesayackayitbilgi_id);
                    if(!$abonesayackayitbilgi){
                        Input::flash();
                        DB::rollBack();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Müşteri Kayıdı Hatalı', 'text' => ' Müşteri Kayıdı Bulunamadı!', 'type' => 'error'));
                    }
                    if($serino != $abonesayackayitbilgi->serino){
                        $abonesayackayit = AboneSayacKayit::find($abonesayackayitbilgi->abonesayackayit_id);
                        $eklisayackayitbilgi = AboneSayacKayitBilgi::where('abonesayackayit_id',$abonesayackayitbilgi->abonesayackayit_id)->where('serino',$serino)->where('id','<>',$servistakip->abonesayackayitbilgi_id)->first();
                        if($eklisayackayitbilgi){
                            if($eklisayackayitbilgi->durum==0){
                                $eklisayackayitbilgi->durum = 1;
                                $eklisayackayitbilgi->save();
                                $abonesayackayitbilgi->durum = 0;
                                $abonesayackayitbilgi->save();
                                $servistakip->abonesayackayitbilgi_id=$eklisayackayitbilgi->id;
                                $servistakip->save();
                            }else{
                                Input::flash();
                                DB::rollBack();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Müşteri Kayıdı Hatalı', 'text' => $serino . ' Nolu Sayaç için zaten kayıt mevcut!', 'type' => 'error'));
                            }
                        }else{
                            $abonesayackayitbilgi->durum=0;
                            $abonesayackayitbilgi->save();
                            $abonesayackayit->durum = 2;
                            $abonesayackayit->save();
                            $servistakip->abonesayackayitbilgi_id=null;
                            $servistakip->save();
                        }
                    }
                }else{
                    $bilgi = AboneSayacKayit::where('abonesayackayit.netsiscari_id',$netsiscari->id)->where('abonesayackayit.durum',2)
                        ->where('abonesayackayitbilgi.serino',$serino)
                        ->leftJoin('abonesayackayitbilgi','abonesayackayitbilgi.abonesayackayit_id','=','abonesayackayit.id')->first(array('abonesayackayitbilgi.id'));
                    if($bilgi){
                        $abonesayackayitbilgi = AboneSayacKayitBilgi::find($bilgi->id);
                        $abonesayackayitbilgi->durum = 1;
                        $abonesayackayitbilgi->save();
                        $servistakip->abonesayackayitbilgi_id=$abonesayackayitbilgi->id;
                        $servistakip->save();
                        $abonesayackayit = AboneSayacKayit::find($abonesayackayitbilgi->abonesayackayit_id);
                        $hatalikayitlar = AboneSayacKayitBilgi::where('abonesayackayit_id',$abonesayackayit->id)->where('durum',0)->get();
                        if($hatalikayitlar->count()>0){
                            $abonesayackayit->durum=2;
                            $abonesayackayit->save();
                        }else{
                            $abonesayackayit->durum=1;
                            $abonesayackayit->save();
                        }
                    }
                }
                $sayacgelen->serino = $serino;
                $sayacgelen->uretimyer_id = $uretimyer_id;
                $sayacgelen->kullanici_id = Auth::user()->id;
                $servistakip->serino = $serino;
                $servistakip->uretimyer_id = $uretimyer_id;
                $servistakip->save();
                $sayacgelen->save();
                BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-thumbs-o-up', $netsiscari->cariadi . ' Yerine Ait ' . $bilgi->id . ' Numaralı Sayaç Kayıdı Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Sayaç Kayıt Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::to($this->servisadi . '/sayackayit')->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Güncellendi', 'text' => 'Sayaç Kayıdı Başarıyla Güncellendi', 'type' => 'success'));
            } else {
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Güncellenemedi', 'text' => 'Sayacın Servis Bilgisi Düzgün Kaydedilmemiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Güncellenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getSayackayitgoster($id) {
        try {
        $sayacgelen = SayacGelen::find($id);
        $sayacgelen->servistakip=ServisTakip::where('sayacgelen_id',$sayacgelen->id)->first();
        $uretimyerleri = UretimYer::where('mekanik',0)->get();
        $sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
        $sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
        $netsiscari = NetsisCari::find($sayacgelen->netsiscari_id);
        $servisstokkod = ServisStokKod::where('stokkodu',$sayacgelen->stokkodu)->first();
        return View::make($this->servisadi.'.sayackayitgoster',array('sayacgelen'=>$sayacgelen,'uretimyerleri'=>$uretimyerleri,'netsiscari'=>$netsiscari,'sayacadi'=>$sayacadi,'sayaccap'=>$sayaccap,'servisstokkod'=>$servisstokkod))->with(array('title'=>$this->servisbilgi.' Sayaç Kayıdı Bilgi Ekranı'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Bilgilerinde Hata Var!', 'text' => str_replace("'","'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getArizakayit($hatirlatma_id=false) {
        if($hatirlatma_id)
            return View::make($this->servisadi.'.arizakayit',array('hatirlatma_id'=>$hatirlatma_id))->with(array('title'=>$this->servisbilgi.' Servis Arıza Kayıdı'));
        else
            return View::make($this->servisadi.'.arizakayit')->with(array('title'=>$this->servisbilgi.' Servis Arıza Kayıdı'));
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
            return Redirect::back()->with(array('mesaj' => 'false'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning', 'title' => 'Rapor Hatası'));
        }
    }

    public function getArizakayitsil($id){
        try {
            DB::beginTransaction();
            $arizakayit = ArizaKayit::find($id);
            $bilgi = clone $arizakayit;
            if(!$arizakayit)
                return Redirect::to($this->servisadi.'/arizakayit')->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Silinemedi', 'text' => 'Sayaç İçin Arıza Kayıdı Zaten Silinmiş Olabilir!', 'type' => 'error'));
            $netsiscari = NetsisCari::find($arizakayit->netsiscari_id);
            $arizafiyat = ArizaFiyat::where('arizakayit_id', $arizakayit->id)->first();
            $arizafiyateski = ArizaFiyatEski::where('arizakayit_id', $arizakayit->id)->first();
            $servistakip = ServisTakip::where('arizakayit_id', $arizakayit->id)->first();
            $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
            if ($sayacgelen->fiyatlandirma) {
                return Redirect::to($this->servisadi.'/arizakayit')->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Silinemez', 'text' => 'Sayaç Ücretlendirilmiş!', 'type' => 'error'));
            }
            $depogelen = DepoGelen::find($sayacgelen->depogelen_id);
            $sayacariza = SayacAriza::find($arizakayit->sayacariza_id);
            $sayacyapilan = SayacYapilan::find($arizakayit->sayacyapilan_id);
            $sayacdegisen = SayacDegisen::find($arizakayit->sayacdegisen_id);
            $depoteslim = DepoTeslim::find($servistakip->depoteslim_id);
            if ($arizakayit->arizakayit_durum == 2) { //hurda ise
                BackendController::HatirlatmaSil(9,$netsiscari->id,$sayacgelen->servis_id,1);
                BackendController::BildirimGeriAl(10,$netsiscari->id,$sayacgelen->servis_id,1);
                BackendController::BildirimGeriAl(3,$netsiscari->id,$sayacgelen->servis_id,1,$depogelen->id,$depogelen->servisstokkodu);
                $servistakip->depoteslim_id = NULL;
                $hurda = Hurda::find($servistakip->hurda_id);
                $servistakip->hurda_id = NULL;
                $servistakip->save();
                if ($hurda){
                    $hurdanedeni = HurdaNedeni::find($hurda->hurdanedeni_id);
                    $hurdanedeni->kullanim-=($hurdanedeni->kullanim==0 ? 0 : 1);
                    $hurdanedeni->save();
                    $hurda->delete();
                }
            } else {
                BackendController::HatirlatmaSil(4,$netsiscari->id,$sayacgelen->servis_id,1);
                BackendController::BildirimGeriAl(3,$netsiscari->id,$sayacgelen->servis_id,1,$depogelen->id,$depogelen->servisstokkodu);
            }
            if($arizakayit->serinodegisim){
                $servistakip->serino=$servistakip->eskiserino;
                $servistakip->eskiserino=null;
                $sayacgelen->serino=$servistakip->eskiserino;
            }
            $servistakip->arizakayit_id = NULL;
            $servistakip->arizafiyat_id = NULL;
            $servistakip->save();

            if(isset($servistakip->subetakip_id)){ //şubeden gelen sayaçlar için bilgileri aktar
                $subeservistakip = ServisTakip::find($servistakip->subetakip_id);
                $subearizakayit = ArizaKayit::find($subeservistakip->arizakayit_id);
                $subearizafiyat = ArizaFiyat::find($subeservistakip->arizafiyat_id);
                $subesayacgelen = SayacGelen::find($subeservistakip->sayacgelen_id);
                BackendController::ArizaKayitSil($subeservistakip,$subesayacgelen,$subearizakayit,$subearizafiyat,null);
            }
            $arizafiyat->arizakayit_id = NULL;
            $arizafiyat->save();

            $arizakayit->sayacariza_id = NULL;
            $arizakayit->sayacyapilan_id = NULL;
            $arizakayit->sayacdegisen_id = NULL;
            $arizakayit->save();

            if ($arizafiyateski) {
                $arizafiyateski->arizakayit_id = NULL;
                $arizafiyateski->arizafiyat_id = NULL;
                $arizafiyateski->save();
                $arizafiyateski->delete();
            }
            if ($depoteslim) {
                if ($depoteslim->sayacsayisi > 1) {
                    $secilenlist = explode(',', $depoteslim->secilenler);
                    $silinecek = array($sayacgelen->id);
                    $depoteslim->secilenler = BackendController::getListeFark($secilenlist, $silinecek);
                    $depoteslim->sayacsayisi = count(explode(',', $depoteslim->secilenler));
                    $depoteslim->save();
                } else {
                    $depoteslim->delete();
                }
            }

            $arizakayit->delete();
            $sayacariza->delete();
            $sayacdegisen->delete();
            $sayacyapilan->delete();
            $arizafiyat->delete();

            $servistakip->durum = 1;
            $servistakip->arizakayittarihi = NULL;
            $servistakip->hurdalamatarihi = NULL;
            $servistakip->sonislemtarihi = $servistakip->sayacgiristarihi;
            $servistakip->kullanici_id = $sayacgelen->kullanici_id;
            $servistakip->save();

            $sayacgelen->arizakayit = 0;
            $sayacgelen->beyanname = -1; // elektrikte beyanname yok
            $sayacgelen->save();

            $depogelen->kayitdurum = 0;
            $depogelen->save();

            BackendController::HatirlatmaGeriAl(3,$netsiscari->id,$sayacgelen->servis_id,1,$depogelen->id,$depogelen->servisstokkodu);
            BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-pencil', $netsiscari->cariadi . ' Yerine Ait ' . $bilgi->id . ' Numaralı Arıza Kayıdı Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Arıza Kayıt Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıt Silindi', 'text' => 'Arıza Kayıt Başarıyla Silindi.', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıt Silinemedi', 'text' => 'Arıza Kayıt Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function postArizakayitlist() {
        $hatirlatma_id=Input::get('hatirlatma_id');
        $netsiscari_id=Input::get('netsiscari_id');
        $hatirlatma = false;
        if($hatirlatma_id!="") {
            $hatirlatma = Hatirlatma::find($hatirlatma_id);
        }
        if($hatirlatma){
            $netsiscariid = $hatirlatma->netsiscari_id;
            $query = ArizaKayit::where('sayacgelen.netsiscari_id',$netsiscariid)->where('sayacgelen.servis_id',$this->servisid)
                ->select(array("arizakayit.id", "sayacgelen.serino", "sayacadi.sayacadi", "uretimyer.yeradi","arizakayit.gdurum","kullanici.adi_soyadi",
                    "arizakayit.arizakayittarihi","servistakip.eskiserino","arizakayit.garizakayittarihi","sayacadi.nsayacadi","uretimyer.nyeradi",
                    "arizakayit.ndurum","kullanici.nadi_soyadi","sayacgelen.fiyatlandirma", "sayacgelen.depoteslim"))
                ->leftjoin("sayacgelen", "arizakayit.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("servistakip", "servistakip.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("uretimyer", "sayacgelen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("sayacadi", "arizakayit.sayacadi_id", "=", "sayacadi.id")
                ->leftjoin("kullanici", "arizakayit.arizakayit_kullanici_id", "=", "kullanici.id");
        }else{
            $query = ArizaKayit::where('sayacgelen.servis_id',$this->servisid)
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
                if($netsiscari_id!="")
                    return "<a class='btn btn-sm btn-info' href='".$root."/".$this->servisadi."/arizakayitgoster/".$model->id."' > Göster </a>";
                else if(!$model->fiyatlandirma && !$model->depoteslim)
                    return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/arizakayitduzenle/".$model->id."' > Düzenle </a>
                        <a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                else
                    return "<a class='btn btn-sm btn-info' href='".$root."/".$this->servisadi."/arizakayitgoster/".$model->id."' > Göster </a>";
            })
            ->make(true);
    }

    public function getSayacgelenbilgi(){
        try {
            $serino=Input::get('serino');
            $sayacgelenid=Input::get('sayacgelenid');
            $sayacgelenler = SayacGelen::select(array('sayacgelen.id', 'sayacgelen.depogelen_id', 'sayacgelen.netsiscari_id', 'sayacgelen.stokkodu',
                'sayacgelen.serino', 'sayacgelen.depotarihi', 'sayacgelen.uretimyer_id', 'sayacgelen.sayacadi_id', 'sayacgelen.sayaccap_id',
                'sayacgelen.servis_id', 'sayacgelen.arizakayit', 'sayacgelen.fiyatlandirma','sayacgelen.depolararasi', 'sayacgelen.sokulmenedeni',
                'sayacgelen.musterionay', 'sayac.id as sayac_id'))
                ->leftjoin('uretimyer', 'sayacgelen.uretimyer_id', '=', 'uretimyer.id')
                ->leftjoin('sayac', 'sayacgelen.serino', '=', 'sayac.serino')
                ->where('sayacgelen.servis_id', $this->servisid)->where(function ($query) {
                    $query->where('sayac.sayactur_id', $this->servisid);
                })
                ->where('sayacgelen.serino', $serino)->where('sayacgelen.id', $sayacgelenid)->where('sayacgelen.arizakayit', 0)->where('sayacgelen.depolararasi', 0)
                ->orderBy('sayacgelen.depotarihi', 'asc')->get();
            foreach ($sayacgelenler as $sayacgelen) {
                $sayacgelen->netsiscari = NetsisCari::find($sayacgelen->netsiscari_id);
                $sayacgelen->servisstokkodu = ServisStokKod::where('stokkodu', $sayacgelen->stokkodu)->first();
                $sayacgelen->hatirlatma_id = BackendController::getHatirlatmaId(3, $this->servisid, $sayacgelen->depogelen_id, $sayacgelen->netsiscari_id);
                if ($sayacgelen->hatirlatma_id) {
                    $sayacgelen->hatirlatma = Hatirlatma::find($sayacgelen->hatirlatma_id);
                }
                $sayacgelen->uretimyer = UretimYer::find($sayacgelen->uretimyer_id);
                $sayacgelen->parabirimi = ParaBirimi::find($sayacgelen->uretimyer->parabirimi_id);
                $sayacgelen->sayac = Sayac::find($sayacgelen->sayac_id);
                $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                if ($sayacgelen->sayac) { //sayac bilgisi belliyse
                    $sayacgelen->sayac->sayacadi = SayacAdi::find($sayacgelen->sayac->sayacadi_id);
                    $sayacgelen->sayac->uretimyer = UretimYer::find($sayacgelen->sayac->uretimyer_id);
                    if ($sayacgelen->sayac->songelistarihi) { // onceden servise gelmişse
                        if ($sayacgelen->sayac->uretimtarihi) { // uretim tarihi belliyse
                            $sayacgaranti = SayacGaranti::where('sayacadi_id', $sayacgelen->sayacadi_id)
                                ->where('uretimyer_id', $sayacgelen->uretimyer_id)->where('sayaccap_id', $sayacgelen->sayac->sayaccap_id)->first();
                            if ($sayacgaranti) { //özel garanti süresi varsa
                                $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->songelistarihi, $sayacgaranti->garanti);
                            } else { //yoksa genel garanti süresine bakılır
                                $sayacgaranti = SayacGaranti::where('sayacadi_id', $sayacgelen->sayacadi_id)
                                    ->where('uretimyer_id', 0)->where('sayaccap_id', $sayacgelen->sayac->sayaccap_id)->first();
                                if ($sayacgaranti) {
                                    $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->songelistarihi, $sayacgaranti->garanti);
                                } else {
                                    $sayacgelen->garantidurum = 1; //garanti kontrolü yoksa garanti içi yapılır
                                }
                            }
                        } else { //uretim tarihi belli değilse
                            $sayacgelen->garantidurum = 1;
                        }
                    } else { // önceden servise gelmemişse
                        if ($sayacgelen->sayac->uretimtarihi) { // uretim tarihi belliyse
                            $sayacgaranti = SayacGaranti::where('sayacadi_id', $sayacgelen->sayacadi_id)
                                ->where('uretimyer_id', $sayacgelen->uretimyer_id)->where('sayaccap_id', $sayacgelen->sayac->sayaccap_id)->first();
                            if ($sayacgaranti) { //özel garanti süresi varsa
                                $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->uretimtarihi, $sayacgaranti->garanti);
                            } else { //yoksa genel garanti süresine bakılır
                                $sayacgaranti = SayacGaranti::where('sayacadi_id', $sayacgelen->sayacadi_id)
                                    ->where('uretimyer_id', 0)->where('sayaccap_id', $sayacgelen->sayac->sayaccap_id)->first();
                                if ($sayacgaranti) {
                                    $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->uretimtarihi, $sayacgaranti->garanti);
                                } else {
                                    $sayacgelen->garantidurum = 1; //garanti kontrolü yoksa garanti içi yapılır
                                }
                            }
                        } else { //uretim tarihi belli değilse
                            $sayacgelen->garantidurum = 1;
                        }
                    }
                }
            }
            return Response::json(array('durum' => true, 'sayacgelenler' => $sayacgelenler));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'title' => 'Sayaç Gelen Bilgisi Alınamadı', 'text' => 'Sayaç Gelen Bilgisinde Eksik ', 'type' => 'error'));
        }
    }

    public function getSayacbilgi(){
        try {
            $serino=Input::get('serino');
            if (Input::has('sayacid')) {
                $sayacid=Input::get('sayacid');
                $sayacgelenler = SayacGelen::select(array('sayacgelen.id', 'sayacgelen.depogelen_id', 'sayacgelen.netsiscari_id', 'sayacgelen.stokkodu',
                    'sayacgelen.serino', 'sayacgelen.depotarihi', 'sayacgelen.uretimyer_id', 'sayacgelen.sayacadi_id', 'sayacgelen.sayaccap_id',
                    'sayacgelen.servis_id', 'sayacgelen.arizakayit', 'sayacgelen.fiyatlandirma','sayacgelen.depolararasi', 'sayacgelen.sokulmenedeni',
                    'sayacgelen.musterionay', 'sayac.id as sayac_id'))
                    ->leftjoin('uretimyer', 'sayacgelen.uretimyer_id', '=', 'uretimyer.id')
                    ->leftjoin('sayac', 'sayacgelen.serino', '=', 'sayac.serino')
                    ->where('sayacgelen.servis_id', $this->servisid)->where('sayacgelen.serino', $serino)
                    ->where('sayac.id', $sayacid)->where('sayacgelen.arizakayit', 0)->where('sayacgelen.depolararasi', 0)
                    ->orderBy('sayacgelen.depotarihi', 'asc')->get();
                foreach ($sayacgelenler as $sayacgelen) {
                    $sayacgelen->netsiscari = NetsisCari::find($sayacgelen->netsiscari_id);
                    $sayacgelen->servisstokkodu = ServisStokKod::where('stokkodu', $sayacgelen->stokkodu)->first();
                    $sayacgelen->hatirlatma_id = BackendController::getHatirlatmaId(3, $this->servisid, $sayacgelen->depogelen_id, $sayacgelen->netsiscari_id);
                    $sayacgelen->hatirlatma = Hatirlatma::find($sayacgelen->hatirlatma_id);
                    $sayacgelen->uretimyer = UretimYer::find($sayacgelen->uretimyer_id);
                    $sayacgelen->parabirimi = ParaBirimi::find($sayacgelen->uretimyer->parabirimi_id);
                    $sayacgelen->sayac = Sayac::find($sayacgelen->sayac_id);
                    $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                    $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                    if ($sayacgelen->sayac) { //sayac bilgisi belliyse
                        $sayacgelen->sayac->uretimyer = UretimYer::find($sayacgelen->sayac->uretimyer_id);
                        if ($sayacgelen->sayac->songelistarihi) { // onceden servise gelmişse
                            if ($sayacgelen->sayac->uretimtarihi) { // uretim tarihi belliyse
                                $sayacgaranti = SayacGaranti::where('sayacadi_id', $sayacgelen->sayacadi_id)
                                    ->where('uretimyer_id', $sayacgelen->uretimyer_id)->where('sayaccap_id', $sayacgelen->sayaccap_id)->first();
                                if ($sayacgaranti) { //özel garanti süresi varsa
                                    $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->songelistarihi, $sayacgaranti->garanti);
                                } else { //yoksa genel garanti süresine bakılır
                                    $sayacgaranti = SayacGaranti::where('sayacadi_id', $sayacgelen->sayacadi_id)
                                        ->where('uretimyer_id', 0)->where('sayaccap_id', $sayacgelen->sayaccap_id)->first();
                                    if ($sayacgaranti) {
                                        $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->songelistarihi, $sayacgaranti->garanti);
                                    } else {
                                        $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->songelistarihi, 1);
                                    }
                                }
                            } else { //uretim tarihi belli değilse
                                $sayacgelen->garantidurum = 1;
                            }
                        } else { // önceden servise gelmemişse
                            if ($sayacgelen->sayac->uretimtarihi) { // uretim tarihi belliyse
                                $sayacgaranti = SayacGaranti::where('sayacadi_id', $sayacgelen->sayacadi_id)
                                    ->where('uretimyer_id', $sayacgelen->uretimyer_id)->where('sayaccap_id', $sayacgelen->sayaccap_id)->first();
                                if ($sayacgaranti) { //özel garanti süresi varsa
                                    $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->uretimtarihi, $sayacgaranti->garanti);
                                } else { //yoksa genel garanti süresine bakılır
                                    $sayacgaranti = SayacGaranti::where('sayacadi_id', $sayacgelen->sayacadi_id)
                                        ->where('uretimyer_id', 0)->where('sayaccap_id', $sayacgelen->sayaccap_id)->first();
                                    if ($sayacgaranti) {
                                        $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->uretimtarihi, $sayacgaranti->garanti);
                                    } else {
                                        $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->uretimtarihi, 2);
                                    }
                                }
                            } else { //uretim tarihi belli değilse
                                $sayacgelen->garantidurum = 1;
                            }
                        }
                    }
                }
                return Response::json(array('durum' => 0, 'sayacgelenler' => $sayacgelenler));
            } else {
                $hatirlatma_durum = BackendController::getHatirlatmaSayac(3, $serino);
                if ($hatirlatma_durum == 1) //hatirlatmaya ait kalan miktar varsa
                {
                    $kayitdurum = BackendController::getKayitDurum($serino, $this->servisid);
                    if ($kayitdurum == 1)//arizakayidi girilmemiş sayaç var
                    {
                        $sayacgelenler = SayacGelen::select(array('sayacgelen.id', 'sayacgelen.depogelen_id', 'sayacgelen.netsiscari_id', 'sayacgelen.stokkodu',
                            'sayacgelen.serino', 'sayacgelen.depotarihi', 'sayacgelen.uretimyer_id', 'sayacgelen.sayacadi_id', 'sayacgelen.sayaccap_id',
                            'sayacgelen.servis_id', 'sayacgelen.arizakayit', 'sayacgelen.fiyatlandirma','sayacgelen.depolararasi', 'sayacgelen.sokulmenedeni',
                            'sayacgelen.musterionay', 'sayac.id as sayac_id'))
                            ->leftjoin('uretimyer', 'sayacgelen.uretimyer_id', '=', 'uretimyer.id')
                            ->leftjoin('sayac', 'sayacgelen.serino', '=', 'sayac.serino')
                            ->where('sayacgelen.servis_id', $this->servisid)->where('sayacgelen.serino', $serino)->where('sayacgelen.arizakayit', 0)->where('sayacgelen.depolararasi', 0)
                            ->where(function ($query) {
                                $query->where('sayac.sayactur_id', $this->servisid);
                            })
                            ->orderBy('sayacgelen.depotarihi', 'asc')->get();
                        if ($sayacgelenler->count() > 1) {
                            foreach ($sayacgelenler as $sayacgelen) {
                                $sayacgelen->netsiscari = NetsisCari::find($sayacgelen->netsiscari_id);
                                $sayacgelen->servisstokkodu = ServisStokKod::where('stokkodu', $sayacgelen->stokkodu)->first();
                                $sayacgelen->hatirlatma_id = BackendController::getHatirlatmaId(3, $this->servisid, $sayacgelen->depogelen_id, $sayacgelen->netsiscari_id);
                                if ($sayacgelen->hatirlatma_id) {
                                    $sayacgelen->hatirlatma = Hatirlatma::find($sayacgelen->hatirlatma_id);
                                }
                                $sayacgelen->uretimyer = UretimYer::find($sayacgelen->uretimyer_id);
                                $sayacgelen->parabirimi = ParaBirimi::find($sayacgelen->uretimyer->parabirimi_id);
                                $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                                $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                                $sayacgelen->sayac = Sayac::find($sayacgelen->sayac_id);
                                if ($sayacgelen->sayac) { //sayac bilgisi belliyse
                                    $sayacgelen->sayac->sayacadi = SayacAdi::find($sayacgelen->sayac->sayacadi_id);
                                    $sayacgelen->sayac->sayaccap = SayacCap::find($sayacgelen->sayac->sayaccap_id);
                                    $sayacgelen->sayac->uretimyer = UretimYer::find($sayacgelen->sayac->uretimyer_id);
                                    if ($sayacgelen->sayac->songelistarihi) { // onceden servise gelmişse
                                        if ($sayacgelen->sayac->uretimtarihi) { // uretim tarihi belliyse
                                            $sayacgaranti = SayacGaranti::where('sayacadi_id', $sayacgelen->sayacadi_id)
                                                ->where('uretimyer_id', $sayacgelen->uretimyer_id)->where('sayaccap_id', $sayacgelen->sayaccap_id)->first();
                                            if ($sayacgaranti) { //özel garanti süresi varsa
                                                $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->songelistarihi, $sayacgaranti->garanti);
                                            } else { //yoksa genel garanti süresine bakılır
                                                $sayacgaranti = SayacGaranti::where('sayacadi_id', $sayacgelen->sayacadi_id)
                                                    ->where('uretimyer_id', 0)->where('sayaccap_id', $sayacgelen->sayaccap_id)->first();
                                                if ($sayacgaranti) {
                                                    $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->songelistarihi, $sayacgaranti->garanti);
                                                } else {
                                                    $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->songelistarihi, 2);
                                                }
                                            }
                                        } else { //uretim tarihi belli değilse
                                            $sayacgelen->garantidurum = 1;
                                        }
                                    } else { // önceden servise gelmemişse
                                        if ($sayacgelen->sayac->uretimtarihi) { // uretim tarihi belliyse
                                            $sayacgaranti = SayacGaranti::where('sayacadi_id', $sayacgelen->sayacadi_id)
                                                ->where('uretimyer_id', $sayacgelen->uretimyer_id)->where('sayaccap_id', $sayacgelen->sayaccap_id)->first();
                                            if ($sayacgaranti) { //özel garanti süresi varsa
                                                $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->uretimtarihi, $sayacgaranti->garanti);
                                            } else { //yoksa genel garanti süresine bakılır
                                                $sayacgaranti = SayacGaranti::where('sayacadi_id', $sayacgelen->sayacadi_id)
                                                    ->where('uretimyer_id', 0)->where('sayaccap_id', $sayacgelen->sayaccap_id)->first();
                                                if ($sayacgaranti) {
                                                    $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->uretimtarihi, $sayacgaranti->garanti);
                                                } else {
                                                    $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->uretimtarihi, 2);
                                                }
                                            }
                                        } else { //uretim tarihi belli değilse
                                            $sayacgelen->garantidurum = 1;
                                        }
                                    }
                                }
                            }
                            return Response::json(array('durum' => 1, 'sayacgelenler' => $sayacgelenler));
                        } else if ($sayacgelenler->count() > 0) {
                            foreach ($sayacgelenler as $sayacgelen) {
                                $sayacgelen->netsiscari = NetsisCari::find($sayacgelen->netsiscari_id);
                                $sayacgelen->servisstokkodu = ServisStokKod::where('stokkodu', $sayacgelen->stokkodu)->first();
                                $sayacgelen->hatirlatma_id = BackendController::getHatirlatmaId(3, $this->servisid, $sayacgelen->depogelen_id, $sayacgelen->netsiscari_id);
                                $sayacgelen->hatirlatma = Hatirlatma::find($sayacgelen->hatirlatma_id);
                                $sayacgelen->uretimyer = UretimYer::find($sayacgelen->uretimyer_id);
                                $sayacgelen->parabirimi = ParaBirimi::find($sayacgelen->uretimyer->parabirimi_id);
                                $sayacgelen->sayac = Sayac::find($sayacgelen->sayac_id);
                                $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                                $sayacgelen->sayactip = SayacTip::findOrFail($sayacgelen->sayacadi->sayactip_id);
                                $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                                if ($sayacgelen->sayac) { //sayac bilgisi belliyse
                                    $sayacgelen->sayac->sayacadi = SayacAdi::find($sayacgelen->sayac->sayacadi_id);
                                    $sayacgelen->sayac->sayaccap = SayacCap::find($sayacgelen->sayac->sayaccap_id);
                                    $sayacgelen->sayac->uretimyer = UretimYer::find($sayacgelen->sayac->uretimyer_id);
                                    if($sayacgelen->sayac->sayacadi){
                                        $sayacgelen->sayac->sayactip = SayacTip::findOrFail($sayacgelen->sayac->sayacadi->sayactip_id);
                                        if($sayacgelen->sayacadi_id!=$sayacgelen->sayac->sayacadi_id &&
                                            ($sayacgelen->sayactip->tipadi=="MEKANİK" || $sayacgelen->sayac->sayactip->tipadi=="MEKANİK")){
                                            DB::beginTransaction();
                                            $sayac = new Sayac;
                                            $sayac->serino = $sayacgelen->sayac->serino;
                                            $sayac->cihazno = $sayacgelen->sayac->serino;
                                            $sayac->sayactur_id = $sayacgelen->sayac->sayactur_id;
                                            $sayac->sayacadi_id = $sayacgelen->sayacadi_id;
                                            $sayac->sayaccap_id = $sayacgelen->sayaccap_id;
                                            $sayac->uretimtarihi = $sayacgelen->sayac->uretimtarihi;
                                            $sayac->uretimyer_id = $sayacgelen->sayac->uretimyer_id;
                                            $sayac->save();
                                            DB::commit();
                                            $sayacgelen->sayac = $sayac;
                                        }else{
                                            if (($sayacgelen->sayacadi_id != $sayacgelen->sayac->sayacadi_id || $sayacgelen->sayaccap_id != $sayacgelen->sayac->sayaccap_id)) {
                                                return Response::json(array('durum' => 3, 'title' => 'Sayaç Bilgisi Farklı', 'text' => 'Girilen Seri Numarasının Sayaç Adı Sisteme Girilen Veri ile farklı.', 'type' => 'error'));
                                            }
                                        }
                                    }
                                    if ($sayacgelen->sayac->songelistarihi) { // onceden servise gelmişse
                                        if ($sayacgelen->sayac->uretimtarihi) { // uretim tarihi belliyse
                                            $sayacgaranti = SayacGaranti::where('sayacadi_id', $sayacgelen->sayacadi_id)
                                                ->where('uretimyer_id', $sayacgelen->uretimyer_id)->where('sayaccap_id', $sayacgelen->sayaccap_id)->first();
                                            if ($sayacgaranti) { //özel garanti süresi varsa
                                                $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->songelistarihi, $sayacgaranti->garanti);
                                            } else { //yoksa genel garanti süresine bakılır
                                                $sayacgaranti = SayacGaranti::where('sayacadi_id', $sayacgelen->sayacadi_id)
                                                    ->where('uretimyer_id', 0)->where('sayaccap_id', $sayacgelen->sayaccap_id)->first();
                                                if ($sayacgaranti) {
                                                    $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->songelistarihi, $sayacgaranti->garanti);
                                                } else {
                                                    $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->songelistarihi, 2);
                                                }
                                            }
                                        } else { //uretim tarihi belli değilse
                                            $sayacgelen->garantidurum = 1;
                                        }
                                    } else { // önceden servise gelmemişse
                                        if ($sayacgelen->sayac->uretimtarihi) { // uretim tarihi belliyse
                                            $sayacgaranti = SayacGaranti::where('sayacadi_id', $sayacgelen->sayacadi_id)
                                                ->where('uretimyer_id', $sayacgelen->uretimyer_id)->where('sayaccap_id', $sayacgelen->sayaccap_id)->first();
                                            if ($sayacgaranti) { //özel garanti süresi varsa
                                                $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->uretimtarihi, $sayacgaranti->garanti);
                                            } else { //yoksa genel garanti süresine bakılır
                                                $sayacgaranti = SayacGaranti::where('sayacadi_id', $sayacgelen->sayacadi_id)
                                                    ->where('uretimyer_id', 0)->where('sayaccap_id', $sayacgelen->sayaccap_id)->first();
                                                if ($sayacgaranti) {
                                                    $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->uretimtarihi, $sayacgaranti->garanti);
                                                } else {
                                                    $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->uretimtarihi, 2);
                                                }
                                            }
                                        } else { //uretim tarihi belli değilse
                                            $sayacgelen->garantidurum = 1;
                                        }
                                    }
                                }
                            }
                            return Response::json(array('durum' => 0, 'sayacgelenler' => $sayacgelenler));
                        } else {
                            return Response::json(array('durum' => 3, 'title' => 'Sayaç Bilgisi Bulunamadı', 'text' => 'Girilen Seri Numarası Gelen Sayaçlara Girilmemiş.', 'type' => 'error'));
                        }

                    } else if ($kayitdurum == 2) { //birden fazla aynı seriden bekleyen sayaç varsa öncelikle hangisi girilecek o seçilir
                        $sayacgelenler = SayacGelen::where('serino', $serino)->where('arizakayit', 0)->orderBy('depotarihi', 'asc')->get();
                        if ($sayacgelenler) {
                            foreach ($sayacgelenler as $sayacgelen) {
                                $sayacgelen->netsiscari = NetsisCari::find($sayacgelen->netsiscari_id);
                                $sayacgelen->servisstokkodu = ServisStokKod::where('stokkodu', $sayacgelen->stokkodu)->first();
                                $sayacgelen->uretimyer = UretimYer::find($sayacgelen->uretimyer_id);
                            }
                            return Response::json(array('durum' => 2, 'sayacgelenler' => $sayacgelenler));
                        } else {
                            return Response::json(array('durum' => 3, 'title' => 'Sayaç Bilgisi Bulunamadı', 'text' => 'Arıza kayıdı yapılacak sayaç bulunamadı.Sayacın gelen sayaçlara kaydedildiğinden emin olun.', 'type' => 'error'));
                        }
                    } else { //arıza kayıdı bekleyen sayaç yoksa
                        return Response::json(array('durum' => 3, 'title' => 'Sayaç Bilgisi Bulunamadı', 'text' => 'Arıza kayıdı yapılacak sayaç bulunamadı.Sayacın gelen sayaçlara kaydedildiğinden emin olun.', 'type' => 'error'));
                    }
                } else if ($hatirlatma_durum == 2) {
                    return Response::json(array('durum' => 3, 'title' => 'Hatırlatma Bilgisi Uyarı', 'text' => 'Arıza kayıdı yapılacak sayaç için hatırlatma bilgisi hatalı.', 'type' => 'error'));
                } else {
                    return Response::json(array('durum' => 3, 'title' => 'Sayaç Bilgisi Bulunamadı', 'text' => 'Arıza kayıdı yapılacak sayaç bulunamadı.Sayacın gelen sayaçlara kaydedildiğinden emin olun.', 'type' => 'error'));
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => 3, 'title' => 'Sayaç Bilgisi Bulunamadı', 'text' =>str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getArizaekle() {
        try {
            $ariza = Input::get('ariza');
            If (ArizaKod::where('tanim', $ariza)->where('sayactur_id', $this->servisid)->first()) {
                return Response::json(array('durum' => false, 'title' => 'Doğrulama Hatası', 'text' => 'Bu Arıza tespiti mevcut.', 'type' => 'warning'));
            }
            DB::beginTransaction();
            $arizakod = new ArizaKod;
            $arizakod->kod = "DGR";
            $arizakod->tanim = $ariza;
            $arizakod->sayactur_id = $this->servisid;
            $arizakod->garanti = 2;
            $arizakod->kullanim = 0;
            $arizakod->save();
            $arizalar = ArizaKod::where('sayactur_id', $this->servisid)->orderBy('kullanim', 'desc')->orderBy('tanim', 'asc')->get();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $ariza . ' Tanımlı Arıza Nedeni Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Arıza Nedeni Numarası:' . $arizakod->id);
            DB::commit();
            return Response::json(array('durum' => true, 'arizalar' => $arizalar, 'title' => 'Arıza Tespiti Eklendi', 'text' => 'Arıza Tespiti Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Response::json(array('durum' => false, 'title' => 'Arıza Tespiti Eklenemedi', 'text' => 'Arıza Tespiti Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getYapilanekle() {
        try {
            $yapilan = Input::get('yapilan');
            If (Yapilanlar::where('tanim', $yapilan)->where('sayactur_id', $this->servisid)->first()) {
                return Response::json(array('durum' =>false, 'title' => 'Doğrulama Hatası', 'text' => 'Bu Yapılan İşlem mevcut.', 'type' => 'warning'));
            }
            DB::beginTransaction();
            $yapilanislem = new Yapilanlar;
            $yapilanislem->tanim = $yapilan;
            $yapilanislem->sayactur_id = $this->servisid;
            $yapilanislem->durum = 0;
            $yapilanislem->kullanim = 0;
            $yapilanislem->save();
            $yapilanlar = Yapilanlar::where('sayactur_id', $this->servisid)->get();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $yapilan . ' Tanımlı Yapılan İşlem Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Yapılan İşlem Numarası:' . $yapilanislem->id);
            DB::commit();
            return Response::json(array('durum' => true, 'yapilanlar' => $yapilanlar, 'title' => 'Yapılan İşlem Eklendi', 'text' => 'Yapılan İşlem Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Response::json(array('durum' =>false, 'title' => 'Yapılan İşlem Eklenemedi', 'text' => 'Yapılan İşlem Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getUyariekle() {
        try {
            $uyari = Input::get('uyari');
            If (Uyarilar::where('tanim', $uyari)->where('sayactur_id', $this->servisid)->first()) {
                return Response::json(array('durum' =>false, 'title' => 'Doğrulama Hatası', 'text' => 'Bu Uyarı-Sonuç mevcut.', 'type' => 'warning'));
            }
            DB::beginTransaction();
            $sonucuyari = new Uyarilar;
            $sonucuyari->tanim = $uyari;
            $sonucuyari->sayactur_id = $this->servisid;
            $sonucuyari->durum = 0;
            $sonucuyari->kullanim = 0;
            $sonucuyari->save();
            $uyarilar = Uyarilar::where('sayactur_id', $this->servisid)->get();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $uyari . ' Tanımlı Uyarı-Sonuç Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Uyarı-Sonuç Numarası:' . $sonucuyari->id);
            DB::commit();
            return Response::json(array('durum' => true, 'uyarilar' => $uyarilar, 'title' => 'Uyarı-Sonuç Eklendi', 'text' => 'Uyarı-Sonuç Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Response::json(array('durum' =>false, 'title' => 'Uyarı-Sonuç Eklenemedi', 'text' => 'Uyarı-Sonuç Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getSerinoekle() {
        try {
            $serino = Input::get('yeniserino');
            $uretimyer = Input::get('uretimyer');
            $sayactur=Input::get('sayactur');
            $sayacadi=Input::get('sayacadi');
            $sayaccap=Input::get('sayaccap');
            $sayacgelen=Input::get('sayacgelen');
            $sayaclar=Sayac::where('serino',$serino)->where('sayactur_id',$this->servisid)->get();
            $flag=0;
            $sayacid=0;
            if($sayaclar->count()>0){
                foreach ($sayaclar as $sayac){
                    if($sayac->hurdadurum){
                        $flag=1;
                        $sayacid=$sayac->id;
                        break;
                    }
                    if($sayac->uretimyer_id==$uretimyer){
                        $flag=1;
                        $sayacid=$sayac->id;
                        break;
                    }
                }
                if($flag){
                    if($sayacgelen){
                        $servistakip=ServisTakip::where('sayacgelen_id',$sayacgelen)->first();
                        if($servistakip->arizakayit_id==null){
                            if(BackendController::SayacDurum($serino,$uretimyer,$this->servisid,false,$servistakip->id)){
                                return Response::json(array('durum'=>false, 'title' => 'Seri Numarası Hatası', 'text' => $serino.' Nolu Sayacın Depo Çıkışı Yapılmamış.Önce Sayaca Ait Diğer İşlemleri Bitiriniz.', 'type' => 'error'));
                            }else{
                                $sayac=Sayac::find($sayacid);
                                $sayac->uretimyer_id=$uretimyer;
                                $sayac->sayactur_id=$sayactur;
                                $sayac->sayacadi_id=$sayacadi;
                                $sayac->sayaccap_id=$sayaccap;
                                $sayac->save();
                            }
                        }else{
                            $sayac=Sayac::find($sayacid);
                            $sayac->uretimyer_id=$uretimyer;
                            $sayac->sayactur_id=$sayactur;
                            $sayac->sayacadi_id=$sayacadi;
                            $sayac->sayaccap_id=$sayaccap;
                            $sayac->save();
                        }
                    }else{
                        if(BackendController::SayacDurum($serino,$uretimyer,$this->servisid,false)){
                            return Response::json(array('durum'=>false, 'title' => 'Seri Numarası Hatası', 'text' => $serino.' Nolu Sayacın Depo Çıkışı Yapılmamış.Önce Sayaca Ait Diğer İşlemleri Bitiriniz.', 'type' => 'error'));
                        }else{
                            $sayac=Sayac::find($sayacid);
                            $sayac->uretimyer_id=$uretimyer;
                            $sayac->sayactur_id=$sayactur;
                            $sayac->sayacadi_id=$sayacadi;
                            $sayac->sayaccap_id=$sayaccap;
                            $sayac->save();
                        }
                    }
                }else{
                    $sayac=$sayaclar->first();
                    $sayacyer=UretimYer::find($sayac->uretimyer_id);
                    if ($sayacyer)
                        $yeradi=$sayacyer->yeradi;
                    else
                        $yeradi="";
                    return Response::json(array('durum'=>false, 'title' => 'Seri Numarası Hatası', 'text' => $serino.' Nolu Sayacın Üretim Yeri Farklı: '.$yeradi, 'type' => 'error'));
                }
            }else{
                if(BackendController::getSayacbilgiguncelle())
                {
                    $sayac=Sayac::where('serino',$serino)->where('uretimyer_id',$uretimyer)->first();
                    if($sayac){
                        $sayac->sayactur_id=$sayactur;
                        $sayac->sayacadi_id=$sayacadi;
                        $sayac->sayaccap_id=$sayaccap;
                        $sayac->save();
                    }else{
                        return Response::json(array('durum'=>false, 'title' => 'Seri Numarası Hatası', 'text' => $serino.' Nolu Seri Numarası Sistemde Kayıtlı Değil.', 'type' => 'error'));
                    }
                }else{
                    return Response::json(array('durum'=>false, 'title' => 'Seri Numarası Hatası', 'text' => 'Üretim Tarafından Gelen Sayaç Bilgileri Güncellenemedi.', 'type' => 'error'));
                }
            }
            return Response::json(array('durum'=>true, 'title' => 'Yeni Seri Numarası Eklendi', 'text' => 'Yeni Seri Numarası Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Response::json(array('durum' =>false, 'title' => 'Seri Numarası Eklenemedi', 'text' => 'Seri Numarası Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getGelisbilgi(){
        try {
            $sayacid=Input::get('sayacid');
            $netsiscariid=Input::get('cariid');
            $arizafiyatlar = ArizaFiyat::where('sayac_id', $sayacid)->orderBy('kayittarihi', 'desc')->take(10)->get();
            $list = array();
            foreach ($arizafiyatlar as $arizafiyat) {
                array_push($list, $arizafiyat->sayacgelen_id);
            }
            $depoteslimler = DepoTeslim::where('netsiscari_id', $netsiscariid)->orderBy('teslimtarihi')->get();
            $olmayanlar = array();
            foreach ($depoteslimler as $depoteslim) {
                $secilenler = explode(',', $depoteslim->secilenler);
                if (!BackendController::searchArray($secilenler, $list)) {
                    array_push($olmayanlar, $depoteslim->id);
                }
            }
            $depoteslim = $depoteslimler->except($olmayanlar);
            $sayac = Sayac::find($sayacid);
            foreach ($depoteslim as $teslim) {
                $secilenler = explode(',', $teslim->secilenler);
                $sayacgelen = SayacGelen::where('serino', $sayac->serino)->whereIN('id', $secilenler)->first();
                $arizakayit = ArizaKayit::where('sayac_id', $sayacid)->where('sayacgelen_id', $sayacgelen->id)->first();
                $teslim->gelistarihi = $sayacgelen->depotarihi;
                $teslim->kayittarihi = $arizakayit->arizakayittarihi;
            }
            return Response::json(array('durum'=>true,'depoteslim' => $depoteslim));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title'=>'Gelen Bilgisinde Hata Var','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

    public function getGelisbilgidetay(){
        try {
            $sayacid=Input::get('sayacid');
            $depoteslimid=Input::get('depoteslimid');
            $depoteslim = DepoTeslim::find($depoteslimid);
            $secilenler = explode(',', $depoteslim->secilenler);
            $root = BackendController::getRootDizin();
            foreach ($secilenler as $secilen) {
                $arizafiyat = ArizaFiyat::where('sayacgelen_id', $secilen)->first();
                if ($arizafiyat->sayac_id == $sayacid) {
                    $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                    $arizakayit->sayac = Sayac::find($arizakayit->sayac_id);
                    $arizakayit->sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                    $arizakayit->uretimyer = Uretimyer::find($arizakayit->sayacgelen->uretimyer_id);
                    $arizakayit->sayacadi = SayacAdi::find($arizakayit->sayacadi_id);
                    $arizakayit->sayaccap = SayacCap::find($arizakayit->sayaccap_id);
                    $arizakayit->netsiscari = NetsisCari::find($arizakayit->netsiscari_id);
                    $arizakayit->depogelen = DepoGelen::find($arizakayit->depogelen_id);
                    $arizakayit->istek = ServisStokKod::where('stokkodu', $arizakayit->depogelen->servisstokkodu)->first();
                    $arizakayit->sayacariza = SayacAriza::find($arizakayit->sayacariza_id);
                    $arizakayit->sayacyapilan = SayacYapilan::find($arizakayit->sayacyapilan_id);
                    $arizakayit->sayacdegisen = SayacDegisen::find($arizakayit->sayacdegisen_id);
                    $arizakayit->kullanici = Kullanici::find($arizakayit->arizakayit_kullanici_id);
                    $arizakayit->problemler = BackendController::getProblemler($arizakayit->sayacariza_id);
                    $arizakayit->yapilanlar = BackendController::getYapilanIslemler($arizakayit->sayacyapilan_id);
                    $arizakayit->degisenler = BackendController::getDegisenlerParca($arizakayit->sayacdegisen_id);
                    $arizakayit->uyarilar = BackendController::getSonucUyarilar($arizakayit->sayacuyari_id);
                    $arizakayit->garantidurum = ($arizakayit->garanti == 1 ? 'İçinde' : 'Dışında');
                    $depotarih = new DateTime($arizakayit->sayacgelen->depotarihi);
                    $arizakayit->depotarihi = $depotarih->format('d-m-Y');
                    $arizakayittarihi = new DateTime($arizakayit->arizakayittarihi);
                    $arizakayit->arizatarihi = $arizakayittarihi->format('d-m-Y');
                    $uretimtarihi = new DateTime($arizakayit->sayac->uretimtarihi);
                    $arizakayit->uretimtarihi = $uretimtarihi->format('d-m-Y');
                    $arizakayit->ilkkredi = number_format($arizakayit->ilkkredi, 3,'.','');
                    $arizakayit->ilkharcanan = number_format($arizakayit->ilkharcanan, 3,'.','');
                    $arizakayit->ilkmekanik = number_format($arizakayit->ilkmekanik, 3,'.','');
                    $arizakayit->kalankredi = number_format($arizakayit->kalankredi, 3,'.','');
                    $arizakayit->harcanankredi = number_format($arizakayit->harcanankredi, 3,'.','');
                    $arizakayit->mekanik = number_format($arizakayit->mekanik, 3,'.','');
                    return Response::json(array('durum'=>true,'arizakayit' => $arizakayit, 'root' => $root));
                }
            }
            return Response::json(array('durum'=>true,'arizakayit'=>'','root'=>$root));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title'=>'Gelen Bilgisinin Detayında Hata Var','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

    public function getSayacparca()
    {
        try {
            $sayacadiid=Input::get('sayacadiid');
            if (Input::has('sayaccapid')) {
                $sayaccapid=Input::get('sayaccapid');
                $sayacparcalar = SayacParca::where('sayacadi_id', $sayacadiid)->where('sayaccap_id', $sayaccapid)->first();
                if ($sayacparcalar) {
                    $parcalar = explode(',', $sayacparcalar->parcalar);
                    $sayacparcalar->parca = Degisenler::whereIn('id', $parcalar)->get();
                    return Response::json(array('durum' => true, 'sayacparcalar' => $sayacparcalar));
                }
            } else {
                $sayacparcalar = SayacParca::where('sayacadi_id', $sayacadiid)->first();
                if ($sayacparcalar) {
                    $parcalar = explode(',', $sayacparcalar->parcalar);
                    $sayacparcalar->parca = Degisenler::whereIn('id', $parcalar)->get();
                    return Response::json(array('durum' => true, 'sayacparcalar' => $sayacparcalar));
                }
            }
            return Response::json(array('durum' => false,'title'=>'Sayaç Parçalarında Hata Var','text'=>'Sayaç Parçaları Kaydedilmemiş','type'=>'error'));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false,'title'=>'Sayaç Parçalarında Hata Var','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

    public function getSerinokontrol(){
        try {
            $sayacid=Input::get('sayacid');
            $serino=Input::get('serino');
            $uretimyerid=Input::get('uretimyerid');
            $sayacyerid=Input::get('sayacyerid');
            $sayaclar=Saybil::where('seri_no',$serino)->get();
            if($sayaclar->count()>0){
                if ($sayaclar->count() > 1) {
                    foreach ($sayaclar as $mnssayac) {
                        $uretimyer = Uretimyer::where('oracle_id', $mnssayac->urtyer_id)->first();
                        if ($uretimyer) {
                            if ($uretimyer->id == $uretimyerid) {
                                $sayac = Sayac::find($sayacid);
                                $sayac->uretimyer_id = $uretimyer->id;
                                if ($mnssayac->uretim_tarihi) {
                                    $tarih = date("Y-m-d", strtotime($mnssayac->uretim_tarihi));
                                    $sayac->uretimtarihi = $tarih;
                                }
                                $sayac->save();
                                return Response::json(array('durum' => 1));
                            }
                        }
                    }
                    return Response::json(array('durum' => 2));
                } else {
                    $mnssayac=$sayaclar->first();
                    $uretimyer = Uretimyer::where('oracle_id', $mnssayac->urtyer_id)->first();
                    if ($uretimyer) {
                        if ($uretimyer->id == $sayacyerid) {
                            return Response::json(array('durum' => 2));
                        } else if ($uretimyer->id == $uretimyerid) {
                            $sayac = Sayac::find($sayacid);
                            $sayac->uretimyer_id = $uretimyer->id;
                            if ($sayaclar[0]->uretim_tarihi) {
                                $tarih = date("Y-m-d", strtotime($mnssayac->uretim_tarihi));
                                $sayac->uretimtarihi = $tarih;
                            }
                            $sayac->save();
                            return Response::json(array('durum' => 1));
                        } else {
                            return Response::json(array('durum' => 0));
                        }
                    } else {
                        return Response::json(array('durum' => 2));
                    }
                }
            }else{
                return Response::json(array('durum' => 0));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => 0));
        }
    }

    public function getArizakayitekle($hatirlatma_id=false) {
        if(Auth::user()->netsiscarilist!=""){
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkisiz', 'text' => 'Kayıt Ekleme Yetkiniz Yok', 'type' => 'error'));
        }
        $sayacadlari = SayacAdi::where('sayactur_id',$this->servisid)->get();
        $arizakodlari = ArizaKod::where('sayactur_id',$this->servisid)->orderBy('kullanim','desc')->orderBy('tanim','asc')->get();
        $yapilanlar = Yapilanlar::where('sayactur_id',$this->servisid)->get();
        $degisenler = Degisenler::where('sayactur_id',$this->servisid)->get();
        $uyarilar = Uyarilar::where('sayactur_id',$this->servisid)->orderBy('kullanim','desc')->orderBy('tanim','asc')->get();
        $hurdanedenleri=HurdaNedeni::where('sayactur_id',$this->servisid)->get();
        if($hatirlatma_id)
        {
            $hatirlatma = Hatirlatma::find($hatirlatma_id);
            $netsiscari = NetsisCari::find($hatirlatma->netsiscari_id);
            if($netsiscari->caridurum!="A")
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
            $servisstokkod = ServisStokKod::where('stokkodu',$hatirlatma->servisstokkodu)->first();
            return View::make($this->servisadi.'.arizakayitekle',array('arizakodlari'=>$arizakodlari,'yapilanlar'=>$yapilanlar,'degisenler'=>$degisenler,'uyarilar'=>$uyarilar,'sayacadlari'=>$sayacadlari,'hatirlatmalar'=>$hatirlatma,'netsiscari'=>$netsiscari,'servisstokkod'=>$servisstokkod,'hurdanedenleri'=>$hurdanedenleri))->with(array('title'=>$this->servisbilgi.' Sayaç Arıza Kayıdı Ekle'));
        }else{
            return View::make($this->servisadi.'.arizakayitekle',array('arizakodlari'=>$arizakodlari,'yapilanlar'=>$yapilanlar,'degisenler'=>$degisenler,'uyarilar'=>$uyarilar,'sayacadlari'=>$sayacadlari,'hurdanedenleri'=>$hurdanedenleri))->with(array('title'=>$this->servisbilgi.' Sayaç Arıza Kayıdı Ekle'));
        }
    }

    public function postArizakayitekle() {
        try {
            $rules = ['cariadi' => 'required', 'istek' => 'required', 'uretimyer' => 'required', 'serino' => 'required',
                'uretim' => 'required', 'sayacadi' => 'required', 'garanti' => 'required', 't1deger' => 'required',
                't2deger' => 'required', 't3deger' => 'required', 't4deger' => 'required',
                'ttoplam' => 'required', 'kalankredi' => 'required', 'arizalar' => 'required', 'yapilanlar' => 'required', 'degisenler' => 'required', 'uyarilar' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $ilkkredi = str_replace(',', '.', Input::get('t1deger'));
            $ilkharcanan = str_replace(',', '.', Input::get('t2deger'));
            $ilkmekanik = str_replace(',', '.', Input::get('t3deger'));
            $kalan = str_replace(',', '.', Input::get('t4deger'));
            $harcanan = str_replace(',', '.', Input::get('ttoplam'));
            $mekanik = str_replace(',', '.', Input::get('kalankredi'));
            $musteribilgi = Input::get('musteribilgi');
            $arizaaciklama = Input::get('arizaaciklama');
            $uretimyerid = Input::get('uretimyer');
            $uretimyer=UretimYer::find($uretimyerid);
            $sayacid = Input::get('sayacid');
            $hatirlatmaid = Input::get('hatirlatmaid');
            $sayacadiid = Input::get('sayacadi');
            $sayaccapid = 1;
            $yeniserino=Input::get('yeniserino');
            $arizanot = Input::get('arizanot');
            $garanti = Input::get('garanti');
            $arizalar = Input::get('arizalar');
            $uretimtarih = date("Y-m-d H:i:s", strtotime(Input::get('uretim')));
            $eklenenler = "";
            $hurdadurum = Input::get('hurdadurum');
            $hurdaneden = Input::get('hurdanedeni');
            $serinodegisim=($yeniserino=="") ? 0 : ($hurdadurum ? 0 : 1);
            $arizalist = Input::get('arizalist');
            try {
                foreach ($arizalar as $ariza) {
                    $arizakod = ArizaKod::find($ariza);
                    $arizakod->kullanim += 1;
                    $arizakod->save();
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kodu Hatası', 'text' => 'Arıza Kodu Güncellenemedi.', 'type' => 'error'));
            }
            $yapilanlar = Input::get('yapilanlar');
            $yapilanlist = Input::get('yapilanlist');
            try {
                foreach ($yapilanlar as $yapilan) {
                    $yapilanis = Yapilanlar::find($yapilan);
                    $yapilanis->kullanim = 1;
                    $yapilanis->save();
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Yapılan İşlem Hatası', 'text' => 'Yapılan İşlemler Güncellenemedi.', 'type' => 'error'));
            }
            $degisenler = Input::get('degisenler');
            $degisenlist = Input::get('degisenlist');
            try {
                foreach ($degisenler as $degisen) {
                    $degisenparca = Degisenler::find($degisen);
                    $degisenparca->kullanim = 1;
                    $degisenparca->save();
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Değişen Parça Hatası', 'text' => 'Değişen Parçalar Güncellenemedi.', 'type' => 'error'));
            }
            $uyarilar = Input::get('uyarilar');
            $uyarilist = Input::get('uyarilist');
            try {
                foreach ($uyarilar as $uyari) {
                    $uyarisonuc = Uyarilar::find($uyari);
                    $uyarisonuc->kullanim += 1;
                    $uyarisonuc->save();
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Uyarı-Sonuç Hatası', 'text' => 'Uyarı-Sonuç Güncellenemedi.', 'type' => 'error'));
            }
            $sayacgelen = SayacGelen::find(Input::get('sayacgelenid'));
            $arizakayit = ArizaKayit::where('sayacgelen_id',$sayacgelen->id)->first();
            if($arizakayit){
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıt Hatası', 'text' => 'Aynı Sayacı 2.Kez kaydetmeye Çalıştınız.', 'type' => 'warning'));
            }
            $netsiscari = NetsisCari::find($sayacgelen->netsiscari_id);
            if($serinodegisim){
                $sayac=Sayac::where('serino',$yeniserino)->where('uretimyer_id',$uretimyerid)->first();
                $eskisayac = Sayac::find($sayacid);
            }else{
                $sayac = Sayac::find($sayacid);
                $eskisayac = null;
            }
            $sayacadi = SayacAdi::find($sayacadiid);
            $subedurum = 0;
            if ($sayacgelen->count() > 0) {
                if ($hurdadurum == 1) //hurdaya ayrıldıysa
                {
                    try {
                        $sayacariza = new SayacAriza;
                        $sayacariza->problemler = $arizalist;
                        $sayacariza->kullanici_id = Auth::user()->id;
                        $sayacariza->tarih = date('Y-m-d H:i:s');
                        $sayacariza->durum = 0;
                        $sayacariza->save();
                        try {
                            $sayacyapilan = new SayacYapilan;
                            $sayacyapilan->yapilanlar = $yapilanlist;
                            $sayacyapilan->kullanici_id = Auth::user()->id;
                            $sayacyapilan->tarih = date('Y-m-d H:i:s');
                            $sayacyapilan->durum = 0;
                            $sayacyapilan->save();
                            try {
                                $sayacdegisen = new SayacDegisen;
                                $sayacdegisen->degisenler = $degisenlist;
                                $sayacdegisen->kullanici_id = Auth::user()->id;
                                $sayacdegisen->tarih = date('Y-m-d H:i:s');
                                $sayacdegisen->durum = 0;
                                $sayacdegisen->save();
                                try {
                                    $sayacuyari = new SayacUyari;
                                    $sayacuyari->uyarilar = $uyarilist;
                                    $sayacuyari->kullanici_id = Auth::user()->id;
                                    $sayacuyari->tarih = date('Y-m-d H:i:s');
                                    $sayacuyari->durum = 0;
                                    $sayacuyari->save();
                                    try {
                                        $arizakayit = new ArizaKayit;
                                        $arizakayit->ilkkredi = $ilkkredi;
                                        $arizakayit->ilkharcanan = $ilkharcanan;
                                        $arizakayit->ilkmekanik = $ilkmekanik;
                                        $arizakayit->kalankredi = $kalan;
                                        $arizakayit->harcanankredi = $harcanan;
                                        $arizakayit->mekanik = $mekanik;
                                        $arizakayit->depogelen_id = $sayacgelen->depogelen_id;
                                        $arizakayit->netsiscari_id = $sayacgelen->netsiscari_id;
                                        $arizakayit->sayacgelen_id = $sayacgelen->id;
                                        $arizakayit->sayac_id = $sayac->id;
                                        $arizakayit->sayacadi_id = $sayacadiid;
                                        $arizakayit->sayaccap_id = $sayaccapid;
                                        $arizakayit->musteriaciklama = $musteribilgi;
                                        $arizakayit->arizaaciklama = $arizaaciklama;
                                        $arizakayit->garanti = $garanti;
                                        $arizakayit->sayacariza_id = $sayacariza->id;
                                        $arizakayit->sayacyapilan_id = $sayacyapilan->id;
                                        $arizakayit->sayacdegisen_id = $sayacdegisen->id;
                                        $arizakayit->sayacuyari_id = $sayacuyari->id;
                                        $arizakayit->arizakayit_kullanici_id = Auth::user()->id;
                                        $arizakayit->arizakayittarihi = date('Y-m-d H:i:s');
                                        $arizakayit->arizakayit_durum = 2;
                                        $arizakayit->arizanot = $arizanot;
                                        $arizakayit->serinodegisim=$serinodegisim;
                                        If (Input::hasFile('resim')) {
                                            $resimler = Input::file('resim');
                                            foreach ($resimler as $resim) {
                                                $dosyaadi = $resim->getClientOriginalName();
                                                $dosyaadi = pathinfo($dosyaadi, PATHINFO_FILENAME);
                                                $uzanti = $resim->getClientOriginalExtension();
                                                $isim = Str::slug($dosyaadi) . Str::slug(str_random(5)) . '.' . $uzanti;
                                                $resim->move('assets/arizaresim/', $isim);
                                                //$image = Image::make('assets/arizaresim/' . $isim);
                                                //$image->save();
                                                $eklenenler .= ($eklenenler == "" ? "" : ",") . $isim;
                                            }
                                        }
                                        $arizakayit->resimler = $eklenenler;
                                        $arizakayit->save();
                                        try {
                                            $sayacgelen->arizakayit = 1;
                                            $sayacgelen->sayacdurum = 3; // 1 arıza kayıt 2 kalibrasyon 3 hurda
                                            $sayacgelen->teslimdurum = 3; //0 bekliyor 1 teslimat 2 geri gönderim 3 hurda 4 depolararasi 5 periyodik bakım
                                            $sayacgelen->sayacadi_id = $sayacadiid;
                                            $sayacgelen->sayaccap_id = $sayaccapid;
                                            $sayacgelen->beyanname = -1; // -2 hurda geri gonderim sayackayit -1 dahil değil 0 arizakayit 1 tamamlanan
                                            $sayacgelen->save();
                                            $depogelen = DepoGelen::find($sayacgelen->depogelen_id);
                                            if (BackendController::getArizaKayitDurum($depogelen->id)) {
                                                $depogelen->kayitdurum = 1;
                                                $depogelen->save();
                                            }
                                            $sayac->sayacadi_id = $sayacadiid;
                                            $sayac->sayaccap_id = $sayaccapid;
                                            $sayac->sayactur_id = $sayacadi->sayactur_id;
                                            $sayac->uretimtarihi = $uretimtarih;
                                            $sayac->kullanici_id = Auth::user()->id;
                                            $sayac->hurdadurum = 1;
                                            $sayac->save();
                                        } catch (Exception $e) {
                                            $eklenenresimler = explode(',', $eklenenler);
                                            foreach ($eklenenresimler as $resim) {
                                                File::delete('assets/arizaresim/' . $resim . '');
                                            }
                                            DB::rollBack();
                                            Log::error($e);
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Sayaç Gelen Bilgisi Güncellenemedi.', 'type' => 'error'));
                                        }
                                        try {
                                            //fiyatlandırma burada yapılır
                                            $ucretsiz = '';
                                            $fiyatlar = BackendController::Fiyatlandir($degisenler, $sayacgelen->uretimyer_id);
                                            $flag = 0;
                                            for ($i = 0; $i < count($degisenler); $i++) {
                                                $ucretsiz .= ($flag > 0 ? ',' : '') . '0';
                                                $flag = 1;
                                            }

                                            $fiyatlar['total'] = 0;
                                            $fiyatlar['total2'] = 0;
                                            $fiyatlar['parabirimi2'] = null;

                                            $kdv = ($fiyatlar['total'] * 18) / 100;
                                            $kdv2 = ($fiyatlar['total2'] * 18) / 100;
                                            $arizafiyat = new ArizaFiyat;
                                            $arizafiyat->ariza_serino = $sayac->serino;
                                            $arizafiyat->sayac_id = $sayac->id;
                                            $arizafiyat->sayacadi_id = $sayacadiid;
                                            $arizafiyat->sayaccap_id = $sayaccapid;
                                            $arizafiyat->ariza_garanti = $garanti;
                                            $arizafiyat->fiyatdurum = $fiyatlar['durum'];
                                            $arizafiyat->uretimyer_id = $sayacgelen->uretimyer_id;
                                            $arizafiyat->arizakayit_id = $arizakayit->id;
                                            $arizafiyat->depogelen_id = $sayacgelen->depogelen_id;
                                            $arizafiyat->sayacgelen_id = $sayacgelen->id;
                                            $arizafiyat->netsiscari_id = $sayacgelen->netsiscari_id;
                                            $arizafiyat->degisenler = BackendController::Sort($degisenlist);
                                            $arizafiyat->genel = $fiyatlar['genel'];
                                            $arizafiyat->ozel = $fiyatlar['ozel'];
                                            $arizafiyat->genelbirim = $fiyatlar['genelbirimler'];
                                            $arizafiyat->ozelbirim = $fiyatlar['ozelbirimler'];
                                            $arizafiyat->ucretsiz = $ucretsiz;
                                            $arizafiyat->fiyat = $fiyatlar['total'];
                                            $arizafiyat->fiyat2 = $fiyatlar['total2'];
                                            $arizafiyat->indirim = 0;
                                            $arizafiyat->indirimorani = 0;
                                            $arizafiyat->tutar = $fiyatlar['total'];
                                            $arizafiyat->tutar2 = $fiyatlar['total2'];
                                            $arizafiyat->kdv = $kdv;
                                            $arizafiyat->kdv2 = $kdv2;
                                            $arizafiyat->toplamtutar = $fiyatlar['total'] + $kdv;
                                            $arizafiyat->toplamtutar2 = $fiyatlar['total2'] + $kdv2;
                                            $arizafiyat->parabirimi_id = $fiyatlar['parabirimi'];
                                            $arizafiyat->parabirimi2_id = $fiyatlar['parabirimi2'];
                                            $arizafiyat->subedurum = $subedurum;
                                            $arizafiyat->durum = 1;
                                            $arizafiyat->kullanici_id = Auth::user()->id;
                                            $arizafiyat->kayittarihi = date('Y-m-d H:i:s');
                                            $arizafiyat->kurtarihi = date('Y-m-d');
                                            $arizafiyat->save();
                                            try {
                                                $hurdakayit = new Hurda;
                                                $hurdakayit->servis_id = $sayacgelen->servis_id;
                                                $hurdakayit->netsiscari_id = $sayacgelen->netsiscari_id;
                                                $hurdakayit->sayac_id = $sayac->id;
                                                $hurdakayit->hurdanedeni_id = $hurdaneden;
                                                $hurdakayit->hurdatarihi = date('Y-m-d H:i:s');
                                                $hurdakayit->sayacgelen_id = $sayacgelen->id;
                                                $hurdakayit->arizakayit_id = $arizakayit->id;
                                                $hurdakayit->arizafiyat_id = $arizafiyat->id;
                                                $hurdakayit->kullanici_id = Auth::user()->id;
                                                $hurdakayit->save();
                                                try {
                                                    $flag=0;
                                                    $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                        ->where('depodurum', 0)->where('tipi', 3)->where('subegonderim', $subedurum)->where('periyodik',0)->first();
                                                    if ($depoteslim) {
                                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                                        if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                            $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                            $depoteslim->sayacsayisi += 1;
                                                        }else{
                                                            $flag=1;
                                                        }
                                                    } else {
                                                        $depoteslim = new DepoTeslim;
                                                        $depoteslim->servis_id = $sayacgelen->servis_id;
                                                        $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                        $depoteslim->secilenler = $sayacgelen->id;
                                                        $depoteslim->sayacsayisi = 1;
                                                        $depoteslim->depodurum = 0;
                                                        $depoteslim->tipi = 3;
                                                        $depoteslim->subegonderim = $subedurum;
                                                        $depoteslim->parabirimi_id = $uretimyer->parabirimi_id;
                                                    }
                                                    $depoteslim->save();
                                                    try {
                                                        $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                        $servistakip->arizakayit_id = $arizakayit->id;
                                                        $servistakip->arizafiyat_id = $arizafiyat->id;
                                                        $servistakip->hurda_id = $hurdakayit->id;
                                                        $servistakip->durum = 11;
                                                        $servistakip->arizakayittarihi = $arizakayit->arizakayittarihi;
                                                        $servistakip->hurdalamatarihi = $hurdakayit->hurdatarihi;
                                                        $servistakip->sonislemtarihi = $arizakayit->arizakayittarihi;
                                                        $servistakip->save();
                                                        $hurdakayit->depoteslim_id = $depoteslim->id;
                                                        $hurdakayit->save();
                                                        $hurdanedeni = HurdaNedeni::find($hurdaneden);
                                                        $hurdanedeni->kullanim++;
                                                        $hurdanedeni->save();
                                                        if(!$flag){
                                                            BackendController::HatirlatmaIdGuncelle($hatirlatmaid,1);
                                                            BackendController::HatirlatmaEkle(9, $netsiscari->id,$sayacgelen->servis_id, 1);
                                                            BackendController::BildirimEkle(3, $netsiscari->id,$sayacgelen->servis_id, 1,$depogelen->id,$depogelen->servisstokkodu);
                                                            BackendController::BildirimEkle(10, $netsiscari->id,$sayacgelen->servis_id, 1);
                                                        }
                                                    } catch (Exception $e) {
                                                        $eklenenresimler = explode(',', $eklenenler);
                                                        foreach ($eklenenresimler as $resim) {
                                                            File::delete('assets/arizaresim/' . $resim . '');
                                                        }
                                                        DB::rollBack();
                                                        Log::error($e);
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Hurdaya Ayrılan Sayaç için Servis Takip ve Hatırlatma Bilgisi Güncellenemedi.', 'type' => 'error'));
                                                    }
                                                } catch (Exception $e) {
                                                    $eklenenresimler = explode(',', $eklenenler);
                                                    foreach ($eklenenresimler as $resim) {
                                                        File::delete('assets/arizaresim/' . $resim . '');
                                                    }
                                                    DB::rollBack();
                                                    Log::error($e);
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Hurdaya Ayrılan Sayaç için Depo Teslimi Kaydedilemedi.', 'type' => 'error'));
                                                }
                                                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-pencil', $netsiscari->cariadi . ' Yerine Ait ' . $arizakayit->id . ' Numaralı Arıza Kayıdı Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Arıza Kayıt Numarası:' . $arizakayit->id);
                                                DB::commit();
                                                return Redirect::to($this->servisadi.'/arizakayitekle')->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapıldı', 'text' => 'Arıza Kayıdı Başarıyla Yapıldı.Sayaç hurdaya ayrıldı.', 'type' => 'success'));
                                            } catch (Exception $e) {
                                                $eklenenresimler = explode(',', $eklenenler);
                                                foreach ($eklenenresimler as $resim) {
                                                    File::delete('assets/arizaresim/' . $resim . '');
                                                }
                                                DB::rollBack();
                                                Log::error($e);
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Hurda Kayıdı Sisteme Eklenemedi', 'type' => 'error'));
                                            }
                                        } catch (Exception $e) {
                                            $eklenenresimler = explode(',', $eklenenler);
                                            foreach ($eklenenresimler as $resim) {
                                                File::delete('assets/arizaresim/' . $resim . '');
                                            }
                                            DB::rollBack();
                                            Log::error($e);
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Arıza Fiyatlandırması Sisteme Kaydedilemedi.', 'type' => 'error'));
                                        }
                                    } catch (Exception $e) {
                                        $eklenenresimler = explode(',', $eklenenler);
                                        foreach ($eklenenresimler as $resim) {
                                            File::delete('assets/arizaresim/' . $resim . '');
                                        }
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Arıza Kayıdı Sisteme Kaydedilemedi.', 'type' => 'error'));
                                    }
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Uyarı-Sonuçlar Sisteme Kaydedilemedi.', 'type' => 'error'));
                                }

                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Değişen Parçalar Sisteme Kaydedilemedi.', 'type' => 'error'));
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Yapılan İşlemler Sisteme Kaydedilemedi.', 'type' => 'error'));
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Seçilen Arıza Tespiti Sisteme Kaydedilemedi.', 'type' => 'error'));
                    }
                } else {  // normal arıza kayıt ise
                    if (BackendController::getStokKodDurum()) // stok durumu aktifse
                    {
                        if (BackendController::getStokEslesme($degisenlist)) // stoklarda eşleşme sağlandıysa
                        {
                            $stokkontrol = BackendController::getStokKontrol($degisenlist);
                            if ($stokkontrol['durum'])  // malzemeler yeterliyse
                            {
                                try {
                                    $sayacariza = new SayacAriza;
                                    $sayacariza->problemler = $arizalist;
                                    $sayacariza->kullanici_id = Auth::user()->id;
                                    $sayacariza->tarih = date('Y-m-d H:i:s');
                                    $sayacariza->durum = 0;
                                    $sayacariza->save();
                                    try {
                                        $sayacyapilan = new SayacYapilan;
                                        $sayacyapilan->yapilanlar = $yapilanlist;
                                        $sayacyapilan->kullanici_id = Auth::user()->id;
                                        $sayacyapilan->tarih = date('Y-m-d H:i:s');
                                        $sayacyapilan->durum = 0;
                                        $sayacyapilan->save();
                                        try {
                                            $sayacdegisen = new SayacDegisen;
                                            $sayacdegisen->degisenler = $degisenlist;
                                            $sayacdegisen->kullanici_id = Auth::user()->id;
                                            $sayacdegisen->tarih = date('Y-m-d H:i:s');
                                            $sayacdegisen->durum = 0;
                                            $sayacdegisen->save();
                                            try {
                                                $sayacuyari = new SayacUyari;
                                                $sayacuyari->uyarilar = $uyarilist;
                                                $sayacuyari->kullanici_id = Auth::user()->id;
                                                $sayacuyari->tarih = date('Y-m-d H:i:s');
                                                $sayacuyari->durum = 0;
                                                $sayacuyari->save();
                                                try {
                                                    $arizakayit = new ArizaKayit;
                                                    $arizakayit->ilkkredi = $ilkkredi;
                                                    $arizakayit->ilkharcanan = $ilkharcanan;
                                                    $arizakayit->ilkmekanik = $ilkmekanik;
                                                    $arizakayit->kalankredi = $kalan;
                                                    $arizakayit->harcanankredi = $harcanan;
                                                    $arizakayit->mekanik = $mekanik;
                                                    $arizakayit->depogelen_id = $sayacgelen->depogelen_id;
                                                    $arizakayit->netsiscari_id = $sayacgelen->netsiscari_id;
                                                    $arizakayit->sayacgelen_id = $sayacgelen->id;
                                                    $arizakayit->sayac_id = $sayac->id;
                                                    $arizakayit->sayacadi_id = $sayacadiid;
                                                    $arizakayit->sayaccap_id = $sayaccapid;
                                                    $arizakayit->garanti = $garanti;
                                                    $arizakayit->musteriaciklama = $musteribilgi;
                                                    $arizakayit->arizaaciklama = $arizaaciklama;
                                                    $arizakayit->sayacariza_id = $sayacariza->id;
                                                    $arizakayit->sayacyapilan_id = $sayacyapilan->id;
                                                    $arizakayit->sayacdegisen_id = $sayacdegisen->id;
                                                    $arizakayit->sayacuyari_id = $sayacuyari->id;
                                                    $arizakayit->arizakayit_kullanici_id = Auth::user()->id;
                                                    $arizakayit->arizakayittarihi = date('Y-m-d H:i:s');
                                                    $arizakayit->arizakayit_durum = 1;
                                                    $arizakayit->arizanot = $arizanot;
                                                    $arizakayit->serinodegisim=$serinodegisim;
                                                    If (Input::hasFile('resim')) {
                                                        $resimler = Input::file('resim');
                                                        foreach ($resimler as $resim) {
                                                            $dosyaadi = $resim->getClientOriginalName();
                                                            $dosyaadi = pathinfo($dosyaadi, PATHINFO_FILENAME);
                                                            $uzanti = $resim->getClientOriginalExtension();
                                                            $isim = Str::slug($dosyaadi) . Str::slug(str_random(5)) . '.' . $uzanti;
                                                            $resim->move('assets/arizaresim/', $isim);
                                                            //$image = Image::make('assets/arizaresim/' . $isim);
                                                            //$image->save();
                                                            $eklenenler .= ($eklenenler == "" ? "" : ",") . $isim;
                                                        }
                                                    }
                                                    $arizakayit->resimler = $eklenenler;
                                                    $arizakayit->save();
                                                    try {
                                                        $sayacgelen->serino=$sayac->serino;
                                                        $sayacgelen->arizakayit = 1;
                                                        $sayacgelen->sayacadi_id = $sayacadiid;
                                                        $sayacgelen->sayaccap_id = $sayaccapid;
                                                        $sayacgelen->beyanname = -1; //elektrikte beyanname yok
                                                        $sayacgelen->save();
                                                        $depogelen = DepoGelen::find($sayacgelen->depogelen_id);
                                                        if (BackendController::getArizaKayitDurum($depogelen->id)) {
                                                            $depogelen->kayitdurum = 1;
                                                            $depogelen->save();
                                                        }
                                                        $sayac->sayacadi_id = $sayacadiid;
                                                        $sayac->sayaccap_id = $sayaccapid;
                                                        $sayac->sayactur_id = $sayacadi->sayactur_id;
                                                        $sayac->uretimtarihi = $uretimtarih;
                                                        $sayac->kullanici_id = Auth::user()->id;
                                                        $sayac->hurdadurum = 0;
                                                        $sayac->save();

                                                        if($serinodegisim){
                                                            $eskisayac->hurdadurum=1;
                                                            $eskisayac->save();
                                                        }
                                                    } catch (Exception $e) {
                                                        DB::rollBack();
                                                        Log::error($e);
                                                        $eklenenresimler = explode(',', $eklenenler);
                                                        foreach ($eklenenresimler as $resim) {
                                                            File::delete('assets/arizaresim/' . $resim . '');
                                                        }
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Sayaç Gelen Bilgisi Güncellenemedi.', 'type' => 'error'));
                                                    }
                                                    try {//fiyatlandırma burada yapılır
                                                        $ucretsiz = '';
                                                        $fiyatlar = BackendController::Fiyatlandir($degisenler, $sayacgelen->uretimyer_id);
                                                        $flag = 0;
                                                        for ($i = 0; $i < count($degisenler); $i++) {
                                                            $ucretsiz .= ($flag > 0 ? ',' : '') . '0';
                                                            $flag = 1;
                                                        }
                                                        if ($garanti == 1) //garanti içi ise
                                                        {
                                                            $fiyatlar['total'] = 0;
                                                            $fiyatlar['total2'] = 0;
                                                            $fiyatlar['parabirimi2'] = null;
                                                        }
                                                        $kdv = ($fiyatlar['total'] * 18) / 100;
                                                        $kdv2 = ($fiyatlar['total2'] * 18) / 100;
                                                        $arizafiyat = new ArizaFiyat;
                                                        $arizafiyat->ariza_serino = $sayac->serino;
                                                        $arizafiyat->sayac_id = $sayac->id;
                                                        $arizafiyat->sayacadi_id = $sayacadiid;
                                                        $arizafiyat->sayaccap_id = $sayaccapid;
                                                        $arizafiyat->ariza_garanti = $garanti;
                                                        $arizafiyat->fiyatdurum = $fiyatlar['durum'];
                                                        $arizafiyat->uretimyer_id = $sayacgelen->uretimyer_id;
                                                        $arizafiyat->arizakayit_id = $arizakayit->id;
                                                        $arizafiyat->depogelen_id = $sayacgelen->depogelen_id;
                                                        $arizafiyat->sayacgelen_id = $sayacgelen->id;
                                                        $arizafiyat->netsiscari_id = $sayacgelen->netsiscari_id;
                                                        $arizafiyat->degisenler = BackendController::Sort($degisenlist);
                                                        $arizafiyat->genel = $fiyatlar['genel'];
                                                        $arizafiyat->ozel = $fiyatlar['ozel'];
                                                        $arizafiyat->genelbirim = $fiyatlar['genelbirimler'];
                                                        $arizafiyat->ozelbirim = $fiyatlar['ozelbirimler'];
                                                        $arizafiyat->ucretsiz = $ucretsiz;
                                                        $arizafiyat->fiyat = $fiyatlar['total'];
                                                        $arizafiyat->fiyat2 = $fiyatlar['total2'];
                                                        $arizafiyat->indirim = 0;
                                                        $arizafiyat->indirimorani = 0;
                                                        $arizafiyat->tutar = $fiyatlar['total'];
                                                        $arizafiyat->tutar2 = $fiyatlar['total2'];
                                                        $arizafiyat->kdv = $kdv;
                                                        $arizafiyat->kdv2 = $kdv2;
                                                        $arizafiyat->toplamtutar = $fiyatlar['total'] + $kdv;
                                                        $arizafiyat->toplamtutar2 = $fiyatlar['total2'] + $kdv2;
                                                        $arizafiyat->parabirimi_id = $fiyatlar['parabirimi'];
                                                        $arizafiyat->parabirimi2_id = $fiyatlar['parabirimi2'];
                                                        $arizafiyat->subedurum = $subedurum;
                                                        $arizafiyat->durum = 0;
                                                        $arizafiyat->kullanici_id = Auth::user()->id;
                                                        $arizafiyat->kayittarihi = date('Y-m-d H:i:s');
                                                        $arizafiyat->save();
                                                        try {
                                                            $servistakip=ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                            if($serinodegisim){
                                                                $servistakip->serino=$sayac->serino;
                                                                $servistakip->eskiserino=$eskisayac->serino;
                                                            }
                                                            $servistakip->arizakayit_id = $arizakayit->id;
                                                            $servistakip->arizafiyat_id = $arizafiyat->id;
                                                            $servistakip->durum = 2;
                                                            $servistakip->arizakayittarihi = $arizakayit->arizakayittarihi;
                                                            $servistakip->sonislemtarihi = $arizakayit->arizakayittarihi;
                                                            $servistakip->save();
                                                            BackendController::HatirlatmaIdGuncelle($hatirlatmaid,1);
                                                            BackendController::HatirlatmaEkle(4, $netsiscari->id, $sayacgelen->servis_id, 1);
                                                            BackendController::BildirimEkle(3, $netsiscari->id, $sayacgelen->servis_id, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                            BackendController::getStokKullan($degisenlist, $sayacgelen->servis_id);
                                                            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-pencil', $netsiscari->cariadi . ' Yerine Ait ' . $arizakayit->id . ' Numaralı Arıza Kayıdı Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Arıza Kayıt Numarası:' . $arizakayit->id);
                                                            DB::commit();
                                                            return Redirect::to($this->servisadi.'/arizakayitekle')->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapıldı', 'text' => 'Arıza Kayıdı Başarıyla Yapıldı', 'type' => 'success'));
                                                        } catch (Exception $e) {
                                                            $eklenenresimler = explode(',', $eklenenler);
                                                            foreach ($eklenenresimler as $resim) {
                                                                File::delete('assets/arizaresim/' . $resim . '');
                                                            }
                                                            DB::rollBack();
                                                            Log::error($e);
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Servis Takip ve Hatırlatma Bilgisi Güncellenemedi', 'type' => 'error'));
                                                        }
                                                    } catch (Exception $e) {
                                                        $eklenenresimler = explode(',', $eklenenler);
                                                        foreach ($eklenenresimler as $resim) {
                                                            File::delete('assets/arizaresim/' . $resim . '');
                                                        }
                                                        DB::rollBack();
                                                        Log::error($e);
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Arıza Fiyatlandırması Sisteme Kaydedilemedi.', 'type' => 'error'));
                                                    }
                                                } catch (Exception $e) {
                                                    $eklenenresimler = explode(',', $eklenenler);
                                                    foreach ($eklenenresimler as $resim) {
                                                        File::delete('assets/arizaresim/' . $resim . '');
                                                    }
                                                    DB::rollBack();
                                                    Log::error($e);
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Arıza Kayıdı Sisteme Kaydedilemedi.', 'type' => 'error'));
                                                }
                                            } catch (Exception $e) {
                                                DB::rollBack();
                                                Log::error($e);
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Uyarı-Sonuçlar Sisteme Kaydedilemedi.', 'type' => 'error'));
                                            }

                                        } catch (Exception $e) {
                                            DB::rollBack();
                                            Log::error($e);
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Değişen Parçalar Sisteme Kaydedilemedi.', 'type' => 'error'));
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Yapılan İşlemler Sisteme Kaydedilemedi.', 'type' => 'error'));
                                    }
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Seçilen Arıza Tespiti Sisteme Kaydedilemedi.', 'type' => 'error'));
                                }
                            } else {
                                $eksikler = "";
                                foreach ($stokkontrol['eksik'] as $eksik) {
                                    $eksikler .= ($eksikler == "" ? "" : ",") . $eksik->tanim;
                                }
                                DB::rollBack();
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Stokta eksik malzeme var : ' . $eksikler, 'type' => 'error'));
                            }
                        } else {
                            Input::flash();
                            DB::rollBack();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Stok Durumunda Eşleştirilmeyen Malzeme Seçilmiş.', 'type' => 'error'));
                        }
                    } else { //stok kontrolü yok
                        try {
                            $sayacariza = new SayacAriza;
                            $sayacariza->problemler = $arizalist;
                            $sayacariza->kullanici_id = Auth::user()->id;
                            $sayacariza->tarih = date('Y-m-d H:i:s');
                            $sayacariza->durum = 0;
                            $sayacariza->save();
                            try {
                                $sayacyapilan = new SayacYapilan;
                                $sayacyapilan->yapilanlar = $yapilanlist;
                                $sayacyapilan->kullanici_id = Auth::user()->id;
                                $sayacyapilan->tarih = date('Y-m-d H:i:s');
                                $sayacyapilan->durum = 0;
                                $sayacyapilan->save();
                                try {
                                    $sayacdegisen = new SayacDegisen;
                                    $sayacdegisen->degisenler = $degisenlist;
                                    $sayacdegisen->kullanici_id = Auth::user()->id;
                                    $sayacdegisen->tarih = date('Y-m-d H:i:s');
                                    $sayacdegisen->durum = 0;
                                    $sayacdegisen->save();
                                    try {
                                        $sayacuyari = new SayacUyari;
                                        $sayacuyari->uyarilar = $uyarilist;
                                        $sayacuyari->kullanici_id = Auth::user()->id;
                                        $sayacuyari->tarih = date('Y-m-d H:i:s');
                                        $sayacuyari->durum = 0;
                                        $sayacuyari->save();
                                        try {
                                            $arizakayit = new ArizaKayit;
                                            $arizakayit->ilkkredi = $ilkkredi;
                                            $arizakayit->ilkharcanan = $ilkharcanan;
                                            $arizakayit->ilkmekanik = $ilkmekanik;
                                            $arizakayit->kalankredi = $kalan;
                                            $arizakayit->harcanankredi = $harcanan;
                                            $arizakayit->mekanik = $mekanik;
                                            $arizakayit->depogelen_id = $sayacgelen->depogelen_id;
                                            $arizakayit->netsiscari_id = $sayacgelen->netsiscari_id;
                                            $arizakayit->sayacgelen_id = $sayacgelen->id;
                                            $arizakayit->sayac_id = $sayac->id;
                                            $arizakayit->sayacadi_id = $sayacadiid;
                                            $arizakayit->sayaccap_id = $sayaccapid;
                                            $arizakayit->garanti = $garanti;
                                            $arizakayit->musteriaciklama = $musteribilgi;
                                            $arizakayit->arizaaciklama = $arizaaciklama;
                                            $arizakayit->sayacariza_id = $sayacariza->id;
                                            $arizakayit->sayacyapilan_id = $sayacyapilan->id;
                                            $arizakayit->sayacdegisen_id = $sayacdegisen->id;
                                            $arizakayit->sayacuyari_id = $sayacuyari->id;
                                            $arizakayit->arizakayit_kullanici_id = Auth::user()->id;
                                            $arizakayit->arizakayittarihi = date('Y-m-d H:i:s');
                                            $arizakayit->arizakayit_durum = 1;
                                            $arizakayit->arizanot = $arizanot;
                                            $arizakayit->serinodegisim=$serinodegisim;
                                            If (Input::hasFile('resim')) {
                                                $resimler = Input::file('resim');
                                                foreach ($resimler as $resim) {
                                                    $dosyaadi = $resim->getClientOriginalName();
                                                    $dosyaadi = pathinfo($dosyaadi, PATHINFO_FILENAME);
                                                    $uzanti = $resim->getClientOriginalExtension();
                                                    $isim = Str::slug($dosyaadi) . Str::slug(str_random(5)) . '.' . $uzanti;
                                                    $resim->move('assets/arizaresim/', $isim);
                                                    //$image = Image::make('assets/arizaresim/' . $isim);
                                                    //$image->save();
                                                    $eklenenler .= ($eklenenler == "" ? "" : ",") . $isim;
                                                }
                                            }
                                            $arizakayit->resimler = $eklenenler;
                                            $arizakayit->save();
                                            try {
                                                $sayacgelen->serino=$sayac->serino;
                                                $sayacgelen->arizakayit = 1;
                                                $sayacgelen->sayacadi_id = $sayacadiid;
                                                $sayacgelen->sayaccap_id = $sayaccapid;
                                                $sayacgelen->beyanname = -1; //elektrikte beyanname yok
                                                $sayacgelen->save();
                                                $depogelen = DepoGelen::find($sayacgelen->depogelen_id);
                                                if (BackendController::getArizaKayitDurum($depogelen->id)) {
                                                    $depogelen->kayitdurum = 1;
                                                    $depogelen->save();
                                                }
                                                $sayac->sayacadi_id = $sayacadiid;
                                                $sayac->sayaccap_id = $sayaccapid;
                                                $sayac->sayactur_id = $sayacadi->sayactur_id;
                                                $sayac->uretimtarihi = $uretimtarih;
                                                $sayac->kullanici_id = Auth::user()->id;
                                                $sayac->hurdadurum = 0;
                                                $sayac->save();

                                                if($serinodegisim){
                                                    $eskisayac->hurdadurum=1;
                                                    $eskisayac->save();
                                                }
                                            } catch (Exception $e) {
                                                DB::rollBack();
                                                Log::error($e);
                                                $eklenenresimler = explode(',', $eklenenler);
                                                foreach ($eklenenresimler as $resim) {
                                                    File::delete('assets/arizaresim/' . $resim . '');
                                                }
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Sayaç Gelen Bilgisi Güncellenemedi.', 'type' => 'error'));
                                            }
                                            try {//fiyatlandırma burada yapılır
                                                $ucretsiz = '';
                                                $fiyatlar = BackendController::Fiyatlandir($degisenler, $sayacgelen->uretimyer_id);
                                                $flag = 0;
                                                for ($i = 0; $i < count($degisenler); $i++) {
                                                    $ucretsiz .= ($flag > 0 ? ',' : '') . '0';
                                                    $flag = 1;
                                                }
                                                if ($garanti == 1) //garanti içi ise
                                                {
                                                    $fiyatlar['total'] = 0;
                                                    $fiyatlar['total2'] = 0;
                                                    $fiyatlar['parabirimi2'] = null;
                                                }
                                                $kdv = ($fiyatlar['total'] * 18) / 100;
                                                $kdv2 = ($fiyatlar['total2'] * 18) / 100;
                                                $arizafiyat = new ArizaFiyat;
                                                $arizafiyat->ariza_serino = $sayac->serino;
                                                $arizafiyat->sayac_id = $sayac->id;
                                                $arizafiyat->sayacadi_id = $sayacadiid;
                                                $arizafiyat->sayaccap_id = $sayaccapid;
                                                $arizafiyat->ariza_garanti = $garanti;
                                                $arizafiyat->fiyatdurum = $fiyatlar['durum'];
                                                $arizafiyat->uretimyer_id = $sayacgelen->uretimyer_id;
                                                $arizafiyat->arizakayit_id = $arizakayit->id;
                                                $arizafiyat->depogelen_id = $sayacgelen->depogelen_id;
                                                $arizafiyat->sayacgelen_id = $sayacgelen->id;
                                                $arizafiyat->netsiscari_id = $sayacgelen->netsiscari_id;
                                                $arizafiyat->degisenler = BackendController::Sort($degisenlist);
                                                $arizafiyat->genel = $fiyatlar['genel'];
                                                $arizafiyat->ozel = $fiyatlar['ozel'];
                                                $arizafiyat->genelbirim = $fiyatlar['genelbirimler'];
                                                $arizafiyat->ozelbirim = $fiyatlar['ozelbirimler'];
                                                $arizafiyat->ucretsiz = $ucretsiz;
                                                $arizafiyat->fiyat = $fiyatlar['total'];
                                                $arizafiyat->fiyat2 = $fiyatlar['total2'];
                                                $arizafiyat->indirim = 0;
                                                $arizafiyat->indirimorani = 0;
                                                $arizafiyat->tutar = $fiyatlar['total'];
                                                $arizafiyat->tutar2 = $fiyatlar['total2'];
                                                $arizafiyat->kdv = $kdv;
                                                $arizafiyat->kdv2 = $kdv2;
                                                $arizafiyat->toplamtutar = $fiyatlar['total'] + $kdv;
                                                $arizafiyat->toplamtutar2 = $fiyatlar['total2'] + $kdv2;
                                                $arizafiyat->parabirimi_id = $fiyatlar['parabirimi'];
                                                $arizafiyat->parabirimi2_id = $fiyatlar['parabirimi2'];
                                                $arizafiyat->subedurum = $subedurum;
                                                $arizafiyat->durum = 0;
                                                $arizafiyat->kullanici_id = Auth::user()->id;
                                                $arizafiyat->kayittarihi = date('Y-m-d H:i:s');
                                                $arizafiyat->save();
                                                try {
                                                    $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                    if($serinodegisim){
                                                        $servistakip->serino=$sayac->serino;
                                                        $servistakip->eskiserino=$eskisayac->serino;
                                                    }
                                                    $servistakip->arizakayit_id = $arizakayit->id;
                                                    $servistakip->arizafiyat_id = $arizafiyat->id;
                                                    $servistakip->durum = 2;
                                                    $servistakip->arizakayittarihi = $arizakayit->arizakayittarihi;
                                                    $servistakip->sonislemtarihi = $arizakayit->arizakayittarihi;
                                                    $servistakip->save();
                                                    BackendController::HatirlatmaIdGuncelle($hatirlatmaid,1);
                                                    BackendController::HatirlatmaEkle(4, $netsiscari->id, $sayacgelen->servis_id, 1);
                                                    BackendController::BildirimEkle(3, $netsiscari->id, $sayacgelen->servis_id, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                    BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-pencil', $netsiscari->cariadi . ' Yerine Ait ' . $arizakayit->id . ' Numaralı Arıza Kayıdı Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Arıza Kayıt Numarası:' . $arizakayit->id);
                                                    DB::commit();
                                                    return Redirect::to($this->servisadi.'/arizakayitekle')->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapıldı', 'text' => 'Arıza Kayıdı Başarıyla Yapıldı', 'type' => 'success'));
                                                } catch (Exception $e) {
                                                    $eklenenresimler = explode(',', $eklenenler);
                                                    foreach ($eklenenresimler as $resim) {
                                                        File::delete('assets/arizaresim/' . $resim . '');
                                                    }
                                                    DB::rollBack();
                                                    Log::error($e);
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Servis Takip ve Hatırlatma Bilgisi Güncellenemedi', 'type' => 'error'));
                                                }
                                            } catch (Exception $e) {
                                                $eklenenresimler = explode(',', $eklenenler);
                                                foreach ($eklenenresimler as $resim) {
                                                    File::delete('assets/arizaresim/' . $resim . '');
                                                }
                                                DB::rollBack();
                                                Log::error($e);
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Arıza Fiyatlandırması Sisteme Kaydedilemedi.', 'type' => 'error'));
                                            }
                                        } catch (Exception $e) {
                                            $eklenenresimler = explode(',', $eklenenler);
                                            foreach ($eklenenresimler as $resim) {
                                                File::delete('assets/arizaresim/' . $resim . '');
                                            }
                                            DB::rollBack();
                                            Log::error($e);
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Arıza Kayıdı Sisteme Kaydedilemedi.', 'type' => 'error'));
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Uyarı-Sonuçlar Sisteme Kaydedilemedi.', 'type' => 'error'));
                                    }

                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Değişen Parçalar Sisteme Kaydedilemedi.', 'type' => 'error'));
                                }
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Yapılan İşlemler Sisteme Kaydedilemedi.', 'type' => 'error'));
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Seçilen Arıza Tespiti Sisteme Kaydedilemedi.', 'type' => 'error'));
                        }
                    }
                }
            }else{
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Sayaç Arızası Zaten Kaydedilmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getArizakayitduzenle($id) {
        $arizakayit = ArizaKayit::find($id);
        $arizakayit->sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
        if ($arizakayit->sayacgelen->fiyatlandirma) {
            return Redirect::to($this->servisadi.'/arizakayit')->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemez', 'text' => 'Sayaç Ücretlendirilmiş!', 'type' => 'error'));
        }
        $arizakayit->servistakip = ServisTakip::where('sayacgelen_id',$arizakayit->sayacgelen_id)->first();
        $arizakayit->hatirlatma_id = BackendController::getHatirlatmaId(3, $this->servisid, $arizakayit->sayacgelen->depogelen_id, $arizakayit->sayacgelen->netsiscari_id);
        $arizakayit->uretimyer = Uretimyer::find($arizakayit->sayacgelen->uretimyer_id);
        $arizakayit->sayac = Sayac::find($arizakayit->sayac_id);
        $arizakayit->sayacadi = SayacAdi::find($arizakayit->sayacadi_id);
        $arizakayit->sayacparca = SayacParca::where('sayacadi_id',$arizakayit->sayacadi_id)->where('sayaccap_id',$arizakayit->sayaccap_id)->first();
        $arizakayit->sayacparcalar = explode(',',$arizakayit->sayacparca ? $arizakayit->sayacparca->parcalar : '');
        $arizakayit->netsiscari = NetsisCari::find($arizakayit->netsiscari_id);
        if($arizakayit->netsiscari->caridurum!="A")
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
        $arizakayit->servisstokkod = ServisStokKod::where('stokkodu',$arizakayit->sayacgelen->stokkodu)->first();
        $arizakayit->sayacariza = SayacAriza::find($arizakayit->sayacariza_id);
        $arizakayit->sayacyapilan = SayacYapilan::find($arizakayit->sayacyapilan_id);
        $arizakayit->sayacdegisen = SayacDegisen::find($arizakayit->sayacdegisen_id);
        $arizakayit->sayacuyari = SayacUyari::find($arizakayit->sayacuyari_id);
        $arizakayit->problemler = explode(',',$arizakayit->sayacariza ? $arizakayit->sayacariza->problemler : '');
        $arizakayit->yapilanlar = explode(',',$arizakayit->sayacyapilan ? $arizakayit->sayacyapilan->yapilanlar : '');
        $arizakayit->degisenler = explode(',',$arizakayit->sayacdegisen ? $arizakayit->sayacdegisen->degisenler : '');
        $arizakayit->uyarilar = explode(',',$arizakayit->sayacuyari ? $arizakayit->sayacuyari->uyarilar : '');
        $arizakayit->resimlist = explode(',',$arizakayit->resimler);
        $sayacadlari = SayacAdi::where('sayactur_id',$this->servisid)->get();
        $arizakodlari = ArizaKod::where('sayactur_id',$this->servisid)->orderBy('kullanim','desc')->orderBy('tanim','asc')->get();
        $yapilanlar = Yapilanlar::where('sayactur_id',$this->servisid)->get();
        $degisenler = Degisenler::where('sayactur_id',$this->servisid)->whereIn('id',$arizakayit->sayacparcalar)->get();
        $uyarilar = Uyarilar::where('sayactur_id',$this->servisid)->orderBy('kullanim','desc')->orderBy('tanim','asc')->get();
        $hurdanedenleri=HurdaNedeni::where('sayactur_id',$this->servisid)->get();
        $arizakayit->sayacgelen->hurdadurum=($arizakayit->sayacgelen->sayacdurum==3 ? 1 : 0);
        if($arizakayit->sayacgelen->hurdadurum){
            $hurda=Hurda::where('sayacgelen_id',$arizakayit->sayacgelen_id)->first();
            $arizakayit->sayacgelen->hurdaneden=$hurda ? $hurda->hurdanedeni_id : '';
        }
        return View::make($this->servisadi.'.arizakayitduzenle',array('arizakayit'=>$arizakayit,'arizakodlari'=>$arizakodlari,'yapilanlar'=>$yapilanlar,'degisenler'=>$degisenler,'uyarilar'=>$uyarilar,'sayacadlari'=>$sayacadlari,'hurdanedenleri'=>$hurdanedenleri))->with(array('title'=>$this->servisbilgi.' Arıza Kayıdı Düzenleme Ekranı'));
    }

    public function postArizakayitduzenle($id) {
        try {
            $rules = ['cariadi'=>'required','istek'=>'required','uretimyer'=>'required','serino'=>'required',
            'uretim'=>'required','sayacadi'=>'required','garanti'=>'required','t1deger'=>'required',
            't2deger'=>'required','t3deger'=>'required','t4deger'=>'required',
            'ttoplam'=>'required','kalankredi'=>'required','arizalar'=>'required','yapilanlar'=>'required','degisenler'=>'required','uyarilar'=>'required'];
            $validate = Validator::make(Input::all(),$rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $arizakayit = ArizaKayit::find($id);
            $bilgi=$arizakayit;
            $ilkkredi=str_replace(',','.',Input::get('t1deger'));
            $ilkharcanan=str_replace(',','.',Input::get('t2deger'));
            $ilkmekanik=str_replace(',','.',Input::get('t3deger'));
            $kalan=str_replace(',','.',Input::get('t4deger'));
            $harcanan=str_replace(',','.',Input::get('ttoplam'));
            $mekanik=str_replace(',','.',Input::get('kalankredi'));
            $musteribilgi=Input::get('musteribilgi');
            $arizaaciklama=Input::get('arizaaciklama');
            $uretimyerid = Input::get('uretimyer');
            $uretimyer=UretimYer::find($uretimyerid);
            $sayacgelenid=Input::get('sayacgelenid');
            $sayacadiid=Input::get('sayacadi');
            $sayaccapid=1;
            $yeniserino=Input::get('yeniserino');
            $arizanot=Input::get('arizanot');
            $garanti=Input::get('garanti');
            $arizalar = Input::get('arizalar');
            $uretimtarih = date("Y-m-d H:i:s", strtotime(Input::get('uretim')));
            $hurdadurum = Input::get('hurdadurum');
            $hurdaneden = Input::get('hurdanedeni');
            $serinodegisim=($yeniserino=="") ? 0 : ($hurdadurum ? 0 : 1);
            $eskidurum=$arizakayit->arizakayit_durum;
            $eskiresimler=explode(',',$arizakayit->resimler);
            $ekliler=explode(',',Input::get('resimler'));
            $eklenenler="";
            try {
                foreach ($eskiresimler as $eskiresim) {
                    $flag = 0;
                    foreach ($ekliler as $ekli) {
                        if ($ekli == $eskiresim) {
                            $flag = 1;
                            $eklenenler .= ($eklenenler == "" ? "" : ",") . $ekli;
                            break;
                        }
                    }
                    if ($flag == 0) {
                        File::delete('assets/arizaresim/' . $eskiresim . '');
                    }
                }
                $arizakayit->resimler = $eklenenler;
                $arizakayit->save();
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Arıza Kayıdına Ait resimler Eklenemedi', 'type' => 'error'));
            }
            $arizalist = Input::get('arizalist');
            foreach($arizalar as $ariza)
            {
                $arizakod=ArizaKod::find($ariza);
                $arizakod->kullanim+=1;
                $arizakod->save();
            }
            $yapilanlar = Input::get('yapilanlar');
            $yapilanlist = Input::get('yapilanlist');
            foreach($yapilanlar as $yapilan)
            {
                $yapilanis=Yapilanlar::find($yapilan);
                $yapilanis->kullanim=1;
                $yapilanis->save();
            }
            $degisenler = Input::get('degisenler');
            $degisenlist = Input::get('degisenlist');
            foreach($degisenler as $degisen)
            {
                $degisenparca=Degisenler::find($degisen);
                $degisenparca->kullanim=1;
                $degisenparca->save();
            }
            $uyarilar = Input::get('uyarilar');
            $uyarilist = Input::get('uyarilist');
            foreach($uyarilar as $uyari)
            {
                $uyarisonuc=Uyarilar::find($uyari);
                $uyarisonuc->kullanim+=1;
                $uyarisonuc->save();
            }
            $sayacgelen = SayacGelen::find($sayacgelenid);
            if ($sayacgelen->fiyatlandirma) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemez', 'text' => 'Sayaç Ücretlendirilmiş!', 'type' => 'error'));
            }
            $servistakip= ServisTakip::where('sayacgelen_id',$sayacgelenid)->first();
            if($serinodegisim){
                if($servistakip->eskiserino!=null){
                    $sayac=Sayac::where('serino',$yeniserino)->where('uretimyer_id',$uretimyerid)->first();
                    $eskisayac = Sayac::where('serino',$servistakip->eskiserino)->where('uretimyer_id',$uretimyerid)->first();
                }else {
                    $sayac = Sayac::where('serino', $yeniserino)->where('uretimyer_id', $uretimyerid)->first();
                    $eskisayac = Sayac::where('serino', $sayacgelen->serino)->where('uretimyer_id', $uretimyerid)->first();
                }
            }else{
                if($servistakip->eskiserino!=null) {
                    $sayac = Sayac::where('serino', $servistakip->eskiserino)->where('uretimyer_id', $uretimyerid)->first();
                    $eskisayac = null;
                }else{
                    $sayac = Sayac::where('serino', $sayacgelen->serino)->where('uretimyer_id', $uretimyerid)->first();
                    $eskisayac = null;
                }
            }
            $sayacadi = SayacAdi::find($sayacadiid);
            $netsiscari=NetsisCari::find($arizakayit->netsiscari_id);
            $subedurum=0;
            if($sayacgelen->count()>0)
            {
                if ($hurdadurum == 1) //hurdaya ayrıldıysa
                {
                    $sayacdegisen = SayacDegisen::find($arizakayit->sayacdegisen_id);
                    $eskidegisenler = $sayacdegisen->degisenler;
                    if($eskidurum!=2) //arızakayıttan hurdaya ayrıldıysa
                    {
                        if($sayacgelen->fiyatlandirma==1){ //arıza kayıtı ücretlendirildiyse silinmeyecek hata verecek
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Seçilen Arıza Tespiti Fiyatlandırılmış. Hurdaya Ayrılamaz. Öncelikle Fiyatlandırmanın Silinmesi Gerekiyor.', 'type' => 'error'));
                        }
                        //hatırlatmalar düzeltilecek fiyatlandırma bekleyen silinecek
                        $ucretred = Ucretlendirilen::where('servis_id', $this->servisid)->where('uretimyer_id', $uretimyerid)->where('netsiscari_id', $netsiscari->id)
                            ->where('durum', 3)->whereNull('tekrarkayittarihi')->whereNull('gerigonderimtarihi')->orderBy('kayittarihi', 'desc')->first();
                        $flag=0;
                        if ($ucretred) //reddedilmiş o yere ait ucretlendirme varsa kontrol edilecek
                        {
                            $reddedilenler = $ucretred->reddedilenler;
                            $reddedilenlist = explode(',', $reddedilenler);
                            $yenireddedilenler = "";
                            foreach ($reddedilenlist as $reddedilen) {
                                if ($reddedilen == $servistakip->arizafiyat_id) {
                                    $flag=1;
                                }else{
                                    $yenireddedilenler .= ($yenireddedilenler == "" ? "" : ",") . $reddedilen;
                                }
                            }
                            if ($yenireddedilenler == "") //reddedilenlerin tamamı tekrar ucretlendirilmiş
                            {
                                $ucretred->tekrarkayittarihi = date('Y-m-d H:i:s');
                                $ucretred->durumtarihi = date('Y-m-d H:i:s');
                            }
                            $ucretred->reddedilenler = $yenireddedilenler;
                            $ucretred->save();
                        }
                        if($flag){
                            BackendController::HatirlatmaSil(7,$netsiscari->id,$sayacgelen->servis_id,1);
                        }else{
                            BackendController::HatirlatmaSil(4,$netsiscari->id,$sayacgelen->servis_id,1);
                        }
                        BackendController::HatirlatmaEkle(9,$netsiscari->id,$sayacgelen->servis_id,1);
                        BackendController::BildirimEkle(10,$netsiscari->id,$sayacgelen->servis_id,1);
                        if(BackendController::getStokKodDurum()) // stok durumu aktifse
                        {
                            if(!BackendController::getStokGeriAl($eskidegisenler, $sayacgelen->servis_id)){
                                DB::rollBack();
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Seçilen Arıza Kayıdına Ait Kullanılan Parçaların Stokları Güncellenemedi.', 'type' => 'error'));
                            }
                        }
                    }
                    try {
                        $sayacariza = SayacAriza::find($arizakayit->sayacariza_id);
                        $sayacariza->problemler = $arizalist;
                        $sayacariza->kullanici_id = Auth::user()->id;
                        $sayacariza->tarih = date('Y-m-d H:i:s');
                        $sayacariza->durum = 0;
                        $sayacariza->save();
                        try {
                            $sayacyapilan = SayacYapilan::find($arizakayit->sayacyapilan_id);
                            $sayacyapilan->yapilanlar = $yapilanlist;
                            $sayacyapilan->kullanici_id = Auth::user()->id;
                            $sayacyapilan->tarih = date('Y-m-d H:i:s');
                            $sayacyapilan->durum = 0;
                            $sayacyapilan->save();
                            try {
                                $sayacdegisen = SayacDegisen::find($arizakayit->sayacdegisen_id);
                                $sayacdegisen->degisenler = $degisenlist;
                                $sayacdegisen->kullanici_id = Auth::user()->id;
                                $sayacdegisen->tarih = date('Y-m-d H:i:s');
                                $sayacdegisen->durum = 0;
                                $sayacdegisen->save();
                                try {
                                    $sayacuyari = SayacUyari::find($arizakayit->sayacuyari_id);
                                    $sayacuyari->uyarilar = $uyarilist;
                                    $sayacuyari->kullanici_id = Auth::user()->id;
                                    $sayacuyari->tarih = date('Y-m-d H:i:s');
                                    $sayacuyari->durum = 0;
                                    $sayacuyari->save();
                                    try {
                                        $arizakayit->ilkkredi = $ilkkredi;
                                        $arizakayit->ilkharcanan = $ilkharcanan;
                                        $arizakayit->ilkmekanik = $ilkmekanik;
                                        $arizakayit->kalankredi = $kalan;
                                        $arizakayit->harcanankredi = $harcanan;
                                        $arizakayit->mekanik = $mekanik;
                                        $arizakayit->depogelen_id = $sayacgelen->depogelen_id;
                                        $arizakayit->netsiscari_id = $sayacgelen->netsiscari_id;
                                        $arizakayit->sayacgelen_id = $sayacgelen->id;
                                        $arizakayit->sayac_id = $sayac->id;
                                        $arizakayit->sayacadi_id = $sayacadiid;
                                        $arizakayit->sayaccap_id = $sayaccapid;
                                        $arizakayit->musteriaciklama = $musteribilgi;
                                        $arizakayit->arizaaciklama = $arizaaciklama;
                                        $arizakayit->garanti = $garanti;
                                        $arizakayit->sayacariza_id = $sayacariza->id;
                                        $arizakayit->sayacyapilan_id = $sayacyapilan->id;
                                        $arizakayit->sayacdegisen_id = $sayacdegisen->id;
                                        $arizakayit->sayacuyari_id = $sayacuyari->id;
                                        $arizakayit->arizakayit_kullanici_id = Auth::user()->id;
                                        $arizakayit->arizakayittarihi = date('Y-m-d H:i:s');
                                        $arizakayit->arizakayit_durum = 2;
                                        $arizakayit->arizanot = $arizanot;
                                        $arizakayit->serinodegisim=$serinodegisim;
                                        If (Input::hasFile('resim')) {
                                            $resimler = Input::file('resim');
                                            foreach ($resimler as $resim) {
                                                //File::delete('assets/images/proje/'.$edestekmusteri->projeresim.'');
                                                $dosyaadi = $resim->getClientOriginalName();
                                                $dosyaadi = pathinfo($dosyaadi, PATHINFO_FILENAME);
                                                $uzanti = $resim->getClientOriginalExtension();
                                                $isim = Str::slug($dosyaadi) . Str::slug(str_random(5)) . '.' . $uzanti;
                                                $resim->move('assets/arizaresim/', $isim);
                                                //$image = Image::make('assets/arizaresim/' . $isim);
                                                //$image->save();
                                                $eklenenler .= ($eklenenler == "" ? "" : ",") . $isim;
                                            }
                                        }
                                        $arizakayit->resimler = $eklenenler;
                                        $arizakayit->save();
                                        try {
                                            $sayacgelen->serino = $sayac->serino;
                                            $sayacgelen->arizakayit = 1;
                                            $sayacgelen->sayacdurum = 3; // 1 arıza kayıt 2 kalibrasyon 3 hurda
                                            $sayacgelen->teslimdurum = 3; //0 bekliyor 1 teslimat 2 geri gönderim 3 hurda 4 depolararasi 5 periyodik bakım
                                            $sayacgelen->sayacadi_id = $sayacadiid;
                                            $sayacgelen->sayaccap_id = $sayaccapid;
                                            $sayacgelen->beyanname = -1; // -2 hurda geri gönderim sayackayıt -1 dahil değil 0 bekleyen 1 biten
                                            $sayacgelen->save();
                                            $sayac->sayacadi_id = $sayacadiid;
                                            $sayac->sayaccap_id = $sayaccapid;
                                            $sayac->sayactur_id = $sayacadi->sayactur_id;
                                            $sayac->uretimtarihi = $uretimtarih;
                                            $sayac->kullanici_id = Auth::user()->id;
                                            $sayac->hurdadurum = 1;
                                            $sayac->save();
                                        } catch (Exception $e) {
                                            DB::rollBack();
                                            Log::error($e);
                                            $eklenenresimler = explode(',', $eklenenler);
                                            foreach ($eklenenresimler as $resim) {
                                                File::delete('assets/arizaresim/' . $resim . '');
                                            }
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Sayaç Gelen Bilgisi Sistemde Güncellenemedi.', 'type' => 'error'));
                                        }
                                        try {//fiyatlandırma burada yapılır
                                            $ucretsiz = '';
                                            $fiyatlar = BackendController::Fiyatlandir($degisenler, $sayacgelen->uretimyer_id);
                                            $flag = 0;
                                            for ($i = 0; $i < count($degisenler); $i++) {
                                                $ucretsiz .= ($flag > 0 ? ',' : '') . '0';
                                                $flag = 1;
                                            }
                                            $fiyatlar['total'] = 0;
                                            $fiyatlar['total2'] = 0;
                                            $fiyatlar['parabirimi2'] = null;
                                            $kdv = ($fiyatlar['total'] * 18) / 100;
                                            $kdv2 = ($fiyatlar['total2'] * 18) / 100;
                                            $arizafiyat = ArizaFiyat::where('arizakayit_id', $arizakayit->id)->first();
                                            $arizafiyat->ariza_serino = $sayac->serino;
                                            $arizafiyat->sayac_id = $sayac->id;
                                            $arizafiyat->sayacadi_id = $sayacadiid;
                                            $arizafiyat->sayaccap_id = $sayaccapid;
                                            $arizafiyat->ariza_garanti = $garanti;
                                            $arizafiyat->fiyatdurum = $fiyatlar['durum'];
                                            $arizafiyat->uretimyer_id = $sayacgelen->uretimyer_id;
                                            $arizafiyat->arizakayit_id = $arizakayit->id;
                                            $arizafiyat->depogelen_id = $sayacgelen->depogelen_id;
                                            $arizafiyat->sayacgelen_id = $sayacgelen->id;
                                            $arizafiyat->netsiscari_id = $sayacgelen->netsiscari_id;
                                            $arizafiyat->degisenler = BackendController::Sort($degisenlist);
                                            $arizafiyat->genel = $fiyatlar['genel'];
                                            $arizafiyat->ozel = $fiyatlar['ozel'];
                                            $arizafiyat->genelbirim = $fiyatlar['genelbirimler'];
                                            $arizafiyat->ozelbirim = $fiyatlar['ozelbirimler'];
                                            $arizafiyat->ucretsiz = $ucretsiz;
                                            $arizafiyat->fiyat = $fiyatlar['total'];
                                            $arizafiyat->fiyat2 = $fiyatlar['total2'];
                                            $arizafiyat->indirim = 0;
                                            $arizafiyat->indirimorani = 0;
                                            $arizafiyat->tutar = $fiyatlar['total'];
                                            $arizafiyat->tutar2 = $fiyatlar['total2'];
                                            $arizafiyat->kdv = $kdv;
                                            $arizafiyat->kdv2 = $kdv2;
                                            $arizafiyat->toplamtutar = $fiyatlar['total'] + $kdv;
                                            $arizafiyat->toplamtutar2 = $fiyatlar['total2'] + $kdv2;
                                            $arizafiyat->parabirimi_id = $fiyatlar['parabirimi'];
                                            $arizafiyat->parabirimi2_id = $fiyatlar['parabirimi2'];
                                            $arizafiyat->subedurum = $subedurum;
                                            $arizafiyat->durum = 1;
                                            $arizafiyat->kullanici_id = Auth::user()->id;
                                            $arizafiyat->kayittarihi = date('Y-m-d H:i:s');
                                            $arizafiyat->kurtarihi = date('Y-m-d');
                                            $arizafiyat->save();
                                            try {
                                                $hurdakayit = Hurda::where('sayacgelen_id', $sayacgelenid)->first();
                                                if (!$hurdakayit) {
                                                    $hurdakayit = new Hurda;
                                                }
                                                $hurdakayit->servis_id = $sayacgelen->servis_id;
                                                $hurdakayit->netsiscari_id = $sayacgelen->netsiscari_id;
                                                $hurdakayit->sayac_id = $sayac->id;
                                                $hurdakayit->hurdanedeni_id = $hurdaneden;
                                                $hurdakayit->hurdatarihi = date('Y-m-d H:i:s');
                                                $hurdakayit->sayacgelen_id = $sayacgelen->id;
                                                $hurdakayit->arizakayit_id = $arizakayit->id;
                                                $hurdakayit->arizafiyat_id = $arizafiyat->id;
                                                $hurdakayit->kullanici_id = Auth::user()->id;
                                                $hurdakayit->save();
                                                try {
                                                    $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                        ->where('depodurum', 0)->where('tipi', 3)->where('subegonderim', $subedurum)->where('periyodik',0)->first();
                                                    if ($eskidurum != 2) //arızakayıttan hurdaya ayrıldıysa
                                                    {
                                                        if ($depoteslim) {
                                                            $secilenlist = explode(',', $depoteslim->secilenler);
                                                            if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                                $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                                $depoteslim->sayacsayisi += 1;
                                                            }
                                                        } else {
                                                            $depoteslim = new DepoTeslim;
                                                            $depoteslim->servis_id = $sayacgelen->servis_id;
                                                            $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                            $depoteslim->secilenler = $sayacgelen->id;
                                                            $depoteslim->sayacsayisi = 1;
                                                            $depoteslim->depodurum = 0;
                                                            $depoteslim->tipi = 3;
                                                            $depoteslim->subegonderim = $subedurum;
                                                            $depoteslim->parabirimi_id = $uretimyer->parabirimi_id;
                                                        }
                                                    }
                                                    $depoteslim->save();
                                                    try {
                                                        $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                        $servistakip->serino=$sayac->serino;
                                                        $servistakip->eskiserino=NULL;
                                                        $servistakip->arizakayit_id = $arizakayit->id;
                                                        $servistakip->arizafiyat_id = $arizafiyat->id;
                                                        $servistakip->hurda_id = $hurdakayit->id;
                                                        $servistakip->durum = 11;
                                                        $servistakip->arizakayittarihi = $arizakayit->arizakayittarihi;
                                                        $servistakip->hurdalamatarihi = $hurdakayit->hurdatarihi;
                                                        $servistakip->sonislemtarihi = $arizakayit->arizakayittarihi;
                                                        $servistakip->save();
                                                        $hurdakayit->depoteslim_id = $depoteslim->id;
                                                        $hurdakayit->save();
                                                        $hurdanedeni = HurdaNedeni::find($hurdaneden);
                                                        $hurdanedeni->kullanim++;
                                                        $hurdanedeni->save();
                                                    } catch (Exception $e) {
                                                        $eklenenresimler = explode(',', $eklenenler);
                                                        foreach ($eklenenresimler as $resim) {
                                                            File::delete('assets/arizaresim/' . $resim . '');
                                                        }
                                                        DB::rollBack();
                                                        Log::error($e);
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi.', 'text' => 'Servis Takip Bilgisi Güncellenemedi.', 'type' => 'error'));
                                                    }
                                                } catch (Exception $e) {
                                                    $eklenenresimler = explode(',', $eklenenler);
                                                    foreach ($eklenenresimler as $resim) {
                                                        File::delete('assets/arizaresim/' . $resim . '');
                                                    }
                                                    DB::rollBack();
                                                    Log::error($e);
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Hurdaya Ayrılan Sayaç için Depo Teslimi Kaydedilemedi.', 'type' => 'error'));
                                                }
                                                BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-pencil', $netsiscari->cariadi . ' Yerine Ait ' . $bilgi->id . ' Numaralı Arıza Kayıdı Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Arıza Kayıt Numarası:' . $bilgi->id);
                                                DB::commit();
                                                return Redirect::to($this->servisadi.'/arizakayit')->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellendi', 'text' => 'Arıza Kayıdı Başarıyla Güncellendi', 'type' => 'success'));
                                            } catch (Exception $e) {
                                                $eklenenresimler = explode(',', $eklenenler);
                                                foreach ($eklenenresimler as $resim) {
                                                    File::delete('assets/arizaresim/' . $resim . '');
                                                }
                                                DB::rollBack();
                                                Log::error($e);
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Hurda Kayıdı Yapılamadı', 'type' => 'error'));
                                            }
                                        } catch (Exception $e) {
                                            $eklenenresimler = explode(',', $eklenenler);
                                            foreach ($eklenenresimler as $resim) {
                                                File::delete('assets/arizaresim/' . $resim . '');
                                            }
                                            DB::rollBack();
                                            Log::error($e);
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Arıza Fiyatlandırması Sistemde Güncellenemedi.', 'type' => 'error'));
                                        }
                                    } catch (Exception $e) {
                                        $eklenenresimler = explode(',', $eklenenler);
                                        foreach ($eklenenresimler as $resim) {
                                            File::delete('assets/arizaresim/' . $resim . '');
                                        }
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Arıza Kayıdı Sistemde Güncellenemedi.', 'type' => 'error'));
                                    }
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Uyarı-Sonuç Sistemde Güncellenemedi.', 'type' => 'error'));
                                }
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Değişen Parçalar Sistemde Güncellenemedi.', 'type' => 'error'));
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Yapılan İşlemler Sistemde Güncellenemedi.', 'type' => 'error'));
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Seçilen Arıza Tespiti Sistemde Güncellenemedi.', 'type' => 'error'));
                    }
                }else{ //arıza kayıt yapıldıysa
                    if($eskidurum==2) //hurdadan arıza kayıta değiştirildiyse
                    {
                        if($sayacgelen->depoteslim==1){ //hurdalar depoya teslim edildiyse uyarı verecek
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Seçilen Sayaç Depoya Teslim Edilmiş. Arıza Kayıdı Tekrar oluşturulamaz.', 'type' => 'error'));
                        }
                        $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                        $servistakip->hurda_id=NULL;
                        $servistakip->hurdalamatarihi = NULL;
                        $servistakip->save();
                        $hurda=Hurda::where('sayacgelen_id',$sayacgelenid)->first();
                        if($hurda){
                            $hurda->delete();
                        }
                        $depoteslim = DepoTeslim::where('servis_id',$sayacgelen->servis_id)->where('netsiscari_id',$sayacgelen->netsiscari_id)
                            ->where('depodurum',0)->where('tipi',3)->where('subegonderim',$subedurum)->where('periyodik',0)->first();
                        if($depoteslim){
                            if($depoteslim->sayacsayisi>1){
                                $secilenlist=explode(',',$depoteslim->secilenler);
                                $silinecek=array($sayacgelenid);
                                $depoteslim->secilenler=BackendController::getListeFark($secilenlist,$silinecek);
                                $depoteslim->sayacsayisi= count(explode(',',$depoteslim->secilenler));
                                $depoteslim->save();
                            }else{
                                $depoteslim->delete();
                            }
                            $hurdanedeni=HurdaNedeni::find($hurdaneden);
                            $hurdanedeni->kullanim-=($hurdanedeni->kullanim==0 ? 0 : 1);
                            $hurdanedeni->save();
                        }
                        //hatırlatmalar düzeltilecek hurda sayaç silinecek depoteslimden silinecek
                        BackendController::HatirlatmaSil(9,$netsiscari->id,$sayacgelen->servis_id,1);
                        BackendController::HatirlatmaEkle(4,$netsiscari->id,$sayacgelen->servis_id,1);
                        BackendController::BildirimGeriAl(10,$netsiscari->id,$sayacgelen->servis_id,1);
                    }
                    if(BackendController::getStokKodDurum()) // stok durumu aktifse
                    {
                        if(BackendController::getStokEslesme($degisenlist)) // stoklarda eşleşme sağlandıysa
                        {
                            $sayacdegisen = SayacDegisen::find($arizakayit->sayacdegisen_id);
                            $eskidegisenler = $sayacdegisen->degisenler;
                            $stokkontrol=BackendController::getStokKontrolUpdate($degisenlist,$eskidegisenler);
                            if($stokkontrol['durum'])  // malzemeler yeterliyse
                            {
                                try {
                                    $sayacariza = SayacAriza::find($arizakayit->sayacariza_id);
                                    $sayacariza->problemler = $arizalist;
                                    $sayacariza->kullanici_id = Auth::user()->id;
                                    $sayacariza->tarih = date('Y-m-d H:i:s');
                                    $sayacariza->durum = 0;
                                    $sayacariza->save();
                                    try {
                                        $sayacyapilan = SayacYapilan::find($arizakayit->sayacyapilan_id);
                                        $sayacyapilan->yapilanlar = $yapilanlist;
                                        $sayacyapilan->kullanici_id = Auth::user()->id;
                                        $sayacyapilan->tarih = date('Y-m-d H:i:s');
                                        $sayacyapilan->durum = 0;
                                        $sayacyapilan->save();
                                        try {
                                            $sayacdegisen = SayacDegisen::find($arizakayit->sayacdegisen_id);
                                            $sayacdegisen->degisenler = $degisenlist;
                                            $sayacdegisen->kullanici_id = Auth::user()->id;
                                            $sayacdegisen->tarih = date('Y-m-d H:i:s');
                                            $sayacdegisen->durum = 0;
                                            $sayacdegisen->save();
                                            try {
                                                $sayacuyari = SayacUyari::find($arizakayit->sayacuyari_id);
                                                $sayacuyari->uyarilar = $uyarilist;
                                                $sayacuyari->kullanici_id = Auth::user()->id;
                                                $sayacuyari->tarih = date('Y-m-d H:i:s');
                                                $sayacuyari->durum = 0;
                                                $sayacuyari->save();
                                                try {
                                                    $arizakayit->ilkkredi = $ilkkredi;
                                                    $arizakayit->ilkharcanan = $ilkharcanan;
                                                    $arizakayit->ilkmekanik = $ilkmekanik;
                                                    $arizakayit->kalankredi = $kalan;
                                                    $arizakayit->harcanankredi = $harcanan;
                                                    $arizakayit->mekanik = $mekanik;
                                                    $arizakayit->depogelen_id = $sayacgelen->depogelen_id;
                                                    $arizakayit->netsiscari_id = $sayacgelen->netsiscari_id;
                                                    $arizakayit->sayacgelen_id = $sayacgelen->id;
                                                    $arizakayit->sayac_id = $sayac->id;
                                                    $arizakayit->sayacadi_id = $sayacadiid;
                                                    $arizakayit->sayaccap_id = $sayaccapid;
                                                    $arizakayit->musteriaciklama = $musteribilgi;
                                                    $arizakayit->arizaaciklama = $arizaaciklama;
                                                    $arizakayit->garanti = $garanti;
                                                    $arizakayit->sayacariza_id = $sayacariza->id;
                                                    $arizakayit->sayacyapilan_id = $sayacyapilan->id;
                                                    $arizakayit->sayacdegisen_id = $sayacdegisen->id;
                                                    $arizakayit->sayacuyari_id = $sayacuyari->id;
                                                    $arizakayit->arizakayit_kullanici_id = Auth::user()->id;
                                                    $arizakayit->arizakayittarihi = date('Y-m-d H:i:s');
                                                    $arizakayit->arizakayit_durum = 1;
                                                    $arizakayit->arizanot = $arizanot;
                                                    $arizakayit->serinodegisim=$serinodegisim;
                                                    If (Input::hasFile('resim')) {
                                                        $resimler = Input::file('resim');
                                                        foreach ($resimler as $resim) {
                                                            //File::delete('assets/images/proje/'.$edestekmusteri->projeresim.'');
                                                            $dosyaadi = $resim->getClientOriginalName();
                                                            $dosyaadi = pathinfo($dosyaadi, PATHINFO_FILENAME);
                                                            $uzanti = $resim->getClientOriginalExtension();
                                                            $isim = Str::slug($dosyaadi) . Str::slug(str_random(5)) . '.' . $uzanti;
                                                            $resim->move('assets/arizaresim/', $isim);
                                                            //$image = Image::make('assets/arizaresim/' . $isim);
                                                            //$image->save();
                                                            $eklenenler .= ($eklenenler == "" ? "" : ",") . $isim;
                                                        }
                                                    }
                                                    $arizakayit->resimler = $eklenenler;
                                                    $arizakayit->save();
                                                    try {
                                                        $sayacgelen->serino=$sayac->serino;
                                                        $sayacgelen->arizakayit = 1;
                                                        $sayacgelen->sayacdurum = 1;
                                                        $sayacgelen->teslimdurum = 0;
                                                        $sayacgelen->sayacadi_id = $sayacadiid;
                                                        $sayacgelen->sayaccap_id = $sayaccapid;
                                                        $sayacgelen->beyanname = -1; //elektrikte beyanname yok
                                                        $sayacgelen->save();
                                                        $sayac->sayacadi_id = $sayacadiid;
                                                        $sayac->sayaccap_id = $sayaccapid;
                                                        $sayac->sayactur_id = $sayacadi->sayactur_id;
                                                        $sayac->uretimtarihi = $uretimtarih;
                                                        $sayac->kullanici_id = Auth::user()->id;
                                                        $sayac->hurdadurum = 0;
                                                        $sayac->save();

                                                        if($serinodegisim){
                                                            $eskisayac->hurdadurum=1;
                                                            $eskisayac->save();
                                                        }
                                                        //fiyatlandırma burada yapılır
                                                        $ucretsiz = '';
                                                        $fiyatlar = BackendController::Fiyatlandir($degisenler, $sayacgelen->uretimyer_id);
                                                        $flag = 0;
                                                        for ($i = 0; $i < count($degisenler); $i++) {
                                                            $ucretsiz .= ($flag > 0 ? ',' : '') . '0';
                                                            $flag = 1;
                                                        }

                                                        if ($garanti == 1) //garanti içi ise
                                                        {
                                                            $fiyatlar['total'] = 0;
                                                            $fiyatlar['total2'] = 0;
                                                            $fiyatlar['parabirimi2'] = null;
                                                        }
                                                        $kdv = ($fiyatlar['total'] * 18) / 100;
                                                        $kdv2 = ($fiyatlar['total2'] * 18) / 100;
                                                        $arizafiyat = ArizaFiyat::where('arizakayit_id', $arizakayit->id)->first();
                                                        $arizafiyat->ariza_serino = $sayac->serino;
                                                        $arizafiyat->sayac_id = $sayac->id;
                                                        $arizafiyat->sayacadi_id = $sayacadiid;
                                                        $arizafiyat->sayaccap_id = $sayaccapid;
                                                        $arizafiyat->ariza_garanti = $garanti;
                                                        $arizafiyat->fiyatdurum = $fiyatlar['durum'];
                                                        $arizafiyat->uretimyer_id = $sayacgelen->uretimyer_id;
                                                        $arizafiyat->arizakayit_id = $arizakayit->id;
                                                        $arizafiyat->depogelen_id = $sayacgelen->depogelen_id;
                                                        $arizafiyat->sayacgelen_id = $sayacgelen->id;
                                                        $arizafiyat->netsiscari_id = $sayacgelen->netsiscari_id;
                                                        $arizafiyat->degisenler = BackendController::Sort($degisenlist);
                                                        $arizafiyat->genel = $fiyatlar['genel'];
                                                        $arizafiyat->ozel = $fiyatlar['ozel'];
                                                        $arizafiyat->genelbirim = $fiyatlar['genelbirimler'];
                                                        $arizafiyat->ozelbirim = $fiyatlar['ozelbirimler'];
                                                        $arizafiyat->ucretsiz = $ucretsiz;
                                                        $arizafiyat->fiyat = $fiyatlar['total'];
                                                        $arizafiyat->fiyat2 = $fiyatlar['total2'];
                                                        $arizafiyat->indirim = 0;
                                                        $arizafiyat->indirimorani = 0;
                                                        $arizafiyat->tutar = $fiyatlar['total'];
                                                        $arizafiyat->tutar2 = $fiyatlar['total2'];
                                                        $arizafiyat->kdv = $kdv;
                                                        $arizafiyat->kdv2 = $kdv2;
                                                        $arizafiyat->toplamtutar = $fiyatlar['total'] + $kdv;
                                                        $arizafiyat->toplamtutar2 = $fiyatlar['total2'] + $kdv2;
                                                        $arizafiyat->parabirimi_id = $fiyatlar['parabirimi'] ;
                                                        $arizafiyat->parabirimi2_id = $fiyatlar['parabirimi2'] ;
                                                        $arizafiyat->subedurum = $subedurum;
                                                        $arizafiyat->durum = 0;
                                                        $arizafiyat->kullanici_id = Auth::user()->id;
                                                        $arizafiyat->kayittarihi = date('Y-m-d H:i:s');
                                                        $arizafiyat->kurtarihi=NULL;
                                                        $arizafiyat->save();
                                                        try {
                                                            $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                            if($serinodegisim){
                                                                $servistakip->serino=$sayac->serino;
                                                                $servistakip->eskiserino=$eskisayac->serino;
                                                            }else{
                                                                $servistakip->serino=$sayac->serino;
                                                                $servistakip->eskiserino=NULL;
                                                            }
                                                            $servistakip->arizakayittarihi = $arizakayit->arizakayittarihi;
                                                            $servistakip->sonislemtarihi = $arizakayit->arizakayittarihi;
                                                            $servistakip->durum = 2;
                                                            $servistakip->save();
                                                        } catch (Exception $e) {
                                                            $eklenenresimler = explode(',', $eklenenler);
                                                            foreach ($eklenenresimler as $resim) {
                                                                File::delete('assets/arizaresim/' . $resim . '');
                                                            }
                                                            DB::rollBack();
                                                            Log::error($e);
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi.', 'text' => 'Servis Takip Bilgisi Güncellenemedi.', 'type' => 'error'));
                                                        }
                                                        BackendController::getStokGeriAl($eskidegisenler, $sayacgelen->servis_id);
                                                        BackendController::getStokKullan($degisenlist, $sayacgelen->servis_id);
                                                        BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-pencil', $netsiscari->cariadi . ' Yerine Ait ' . $bilgi->id . ' Numaralı Arıza Kayıdı Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Arıza Kayıt Numarası:' . $bilgi->id);
                                                        DB::commit();
                                                        return Redirect::to($this->servisadi.'/arizakayit')->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellendi', 'text' => 'Arıza Kayıdı Başarıyla Güncellendi', 'type' => 'success'));
                                                    } catch (Exception $e) {
                                                        $eklenenresimler = explode(',', $eklenenler);
                                                        foreach ($eklenenresimler as $resim) {
                                                            File::delete('assets/arizaresim/' . $resim . '');
                                                        }
                                                        DB::rollBack();
                                                        Log::error($e);
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Arıza Fiyatlandırması Sistemde Güncellenemedi.', 'type' => 'error'));
                                                    }
                                                } catch (Exception $e) {
                                                    $eklenenresimler = explode(',', $eklenenler);
                                                    foreach ($eklenenresimler as $resim) {
                                                        File::delete('assets/arizaresim/' . $resim . '');
                                                    }
                                                    DB::rollBack();
                                                    Log::error($e);
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Arıza Kayıdı Sistemde Güncellenemedi.', 'type' => 'error'));
                                                }
                                            } catch (Exception $e) {
                                                DB::rollBack();
                                                Log::error($e);
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Uyarı-Sonuçlar Sistemde Güncellenemedi.', 'type' => 'error'));
                                            }
                                        } catch (Exception $e) {
                                            DB::rollBack();
                                            Log::error($e);
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Değişen Parçalar Sistemde Güncellenemedi.', 'type' => 'error'));
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Yapılan İşlemler Sistemde Güncellenemedi.', 'type' => 'error'));
                                    }
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Seçilen Arıza Tespiti Sistemde Güncellenemedi.', 'type' => 'error'));
                                }
                            }else{
                                $eksikler="";
                                foreach($stokkontrol['eksik'] as $eksik) {
                                    $eksikler.=($eksikler=="" ? "" : ",").$eksik->tanim;
                                }
                                DB::rollBack();
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Stokta eksik malzeme var : '.$eksikler, 'type' => 'error'));
                            }
                        }else{
                            Input::flash();
                            DB::rollBack();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Stok Durumunda Eşleştirilmeyen Malzeme Seçilmiş.', 'type' => 'error'));
                        }
                    }else{ //stok aktif değil
                        try {
                            $sayacariza = SayacAriza::find($arizakayit->sayacariza_id);
                            $sayacariza->problemler = $arizalist;
                            $sayacariza->kullanici_id = Auth::user()->id;
                            $sayacariza->tarih = date('Y-m-d H:i:s');
                            $sayacariza->durum = 0;
                            $sayacariza->save();
                            try {
                                $sayacyapilan = SayacYapilan::find($arizakayit->sayacyapilan_id);
                                $sayacyapilan->yapilanlar = $yapilanlist;
                                $sayacyapilan->kullanici_id = Auth::user()->id;
                                $sayacyapilan->tarih = date('Y-m-d H:i:s');
                                $sayacyapilan->durum = 0;
                                $sayacyapilan->save();
                                try {
                                    $sayacdegisen = SayacDegisen::find($arizakayit->sayacdegisen_id);
                                    $sayacdegisen->degisenler = $degisenlist;
                                    $sayacdegisen->kullanici_id = Auth::user()->id;
                                    $sayacdegisen->tarih = date('Y-m-d H:i:s');
                                    $sayacdegisen->durum = 0;
                                    $sayacdegisen->save();
                                    try {
                                        $sayacuyari = SayacUyari::find($arizakayit->sayacuyari_id);
                                        $sayacuyari->uyarilar = $uyarilist;
                                        $sayacuyari->kullanici_id = Auth::user()->id;
                                        $sayacuyari->tarih = date('Y-m-d H:i:s');
                                        $sayacuyari->durum = 0;
                                        $sayacuyari->save();
                                        try {
                                            $arizakayit->ilkkredi = $ilkkredi;
                                            $arizakayit->ilkharcanan = $ilkharcanan;
                                            $arizakayit->ilkmekanik = $ilkmekanik;
                                            $arizakayit->kalankredi = $kalan;
                                            $arizakayit->harcanankredi = $harcanan;
                                            $arizakayit->mekanik = $mekanik;
                                            $arizakayit->depogelen_id = $sayacgelen->depogelen_id;
                                            $arizakayit->netsiscari_id = $sayacgelen->netsiscari_id;
                                            $arizakayit->sayacgelen_id = $sayacgelen->id;
                                            $arizakayit->sayac_id = $sayac->id;
                                            $arizakayit->sayacadi_id = $sayacadiid;
                                            $arizakayit->sayaccap_id = $sayaccapid;
                                            $arizakayit->musteriaciklama = $musteribilgi;
                                            $arizakayit->arizaaciklama = $arizaaciklama;
                                            $arizakayit->garanti = $garanti;
                                            $arizakayit->sayacariza_id = $sayacariza->id;
                                            $arizakayit->sayacyapilan_id = $sayacyapilan->id;
                                            $arizakayit->sayacdegisen_id = $sayacdegisen->id;
                                            $arizakayit->sayacuyari_id = $sayacuyari->id;
                                            $arizakayit->arizakayit_kullanici_id = Auth::user()->id;
                                            $arizakayit->arizakayittarihi = date('Y-m-d H:i:s');
                                            $arizakayit->arizakayit_durum = 1;
                                            $arizakayit->arizanot = $arizanot;
                                            $arizakayit->serinodegisim=$serinodegisim;
                                            If (Input::hasFile('resim')) {
                                                $resimler = Input::file('resim');
                                                foreach ($resimler as $resim) {
                                                    //File::delete('assets/images/proje/'.$edestekmusteri->projeresim.'');
                                                    $dosyaadi = $resim->getClientOriginalName();
                                                    $dosyaadi = pathinfo($dosyaadi, PATHINFO_FILENAME);
                                                    $uzanti = $resim->getClientOriginalExtension();
                                                    $isim = Str::slug($dosyaadi) . Str::slug(str_random(5)) . '.' . $uzanti;
                                                    $resim->move('assets/arizaresim/', $isim);
                                                    //$image = Image::make('assets/arizaresim/' . $isim);
                                                    //$image->save();
                                                    $eklenenler .= ($eklenenler == "" ? "" : ",") . $isim;
                                                }
                                            }
                                            $arizakayit->resimler = $eklenenler;
                                            $arizakayit->save();
                                            try {
                                                $sayacgelen->serino=$sayac->serino;
                                                $sayacgelen->arizakayit = 1;
                                                $sayacgelen->sayacdurum = 1;
                                                $sayacgelen->teslimdurum = 0;
                                                $sayacgelen->sayacadi_id = $sayacadiid;
                                                $sayacgelen->sayaccap_id = $sayaccapid;
                                                $sayacgelen->beyanname = -1; // elektrik te beyanname yok
                                                $sayacgelen->save();
                                                $sayac->sayacadi_id = $sayacadiid;
                                                $sayac->sayaccap_id = $sayaccapid;
                                                $sayac->sayactur_id = $sayacadi->sayactur_id;
                                                $sayac->uretimtarihi = $uretimtarih;
                                                $sayac->kullanici_id = Auth::user()->id;
                                                $sayac->hurdadurum = 0;
                                                $sayac->save();

                                                if($serinodegisim){
                                                    $eskisayac->hurdadurum=1;
                                                    $eskisayac->save();
                                                }
                                                //fiyatlandırma burada yapılır
                                                $ucretsiz = '';
                                                $fiyatlar = BackendController::Fiyatlandir($degisenler, $sayacgelen->uretimyer_id);
                                                $flag = 0;
                                                for ($i = 0; $i < count($degisenler); $i++) {
                                                    $ucretsiz .= ($flag > 0 ? ',' : '') . '0';
                                                    $flag = 1;
                                                }

                                                if ($garanti == 1) //garanti içi ise
                                                {
                                                    $fiyatlar['total'] = 0;
                                                    $fiyatlar['total2'] = 0;
                                                    $fiyatlar['parabirimi2'] = null;
                                                }
                                                $kdv = ($fiyatlar['total'] * 18) / 100;
                                                $kdv2 = ($fiyatlar['total2'] * 18) / 100;
                                                $arizafiyat = ArizaFiyat::where('arizakayit_id', $arizakayit->id)->first();
                                                $arizafiyat->ariza_serino = $sayac->serino;
                                                $arizafiyat->sayac_id = $sayac->id;
                                                $arizafiyat->sayacadi_id = $sayacadiid;
                                                $arizafiyat->sayaccap_id = $sayaccapid;
                                                $arizafiyat->ariza_garanti = $garanti;
                                                $arizafiyat->fiyatdurum = $fiyatlar['durum'];
                                                $arizafiyat->uretimyer_id = $sayacgelen->uretimyer_id;
                                                $arizafiyat->arizakayit_id = $arizakayit->id;
                                                $arizafiyat->depogelen_id = $sayacgelen->depogelen_id;
                                                $arizafiyat->sayacgelen_id = $sayacgelen->id;
                                                $arizafiyat->netsiscari_id = $sayacgelen->netsiscari_id;
                                                $arizafiyat->degisenler = BackendController::Sort($degisenlist);
                                                $arizafiyat->genel = $fiyatlar['genel'];
                                                $arizafiyat->ozel = $fiyatlar['ozel'];
                                                $arizafiyat->genelbirim = $fiyatlar['genelbirimler'];
                                                $arizafiyat->ozelbirim = $fiyatlar['ozelbirimler'];
                                                $arizafiyat->ucretsiz = $ucretsiz;
                                                $arizafiyat->fiyat = $fiyatlar['total'];
                                                $arizafiyat->fiyat2 = $fiyatlar['total2'];
                                                $arizafiyat->indirim = 0;
                                                $arizafiyat->indirimorani = 0;
                                                $arizafiyat->tutar = $fiyatlar['total'];
                                                $arizafiyat->tutar2 = $fiyatlar['total2'];
                                                $arizafiyat->kdv = $kdv;
                                                $arizafiyat->kdv2 = $kdv2;
                                                $arizafiyat->toplamtutar = $fiyatlar['total'] + $kdv;
                                                $arizafiyat->toplamtutar2 = $fiyatlar['total2'] + $kdv2;
                                                $arizafiyat->parabirimi_id = $fiyatlar['parabirimi'];
                                                $arizafiyat->parabirimi2_id = $fiyatlar['parabirimi2'];
                                                $arizafiyat->subedurum = $subedurum;
                                                $arizafiyat->durum = 0;
                                                $arizafiyat->kullanici_id = Auth::user()->id;
                                                $arizafiyat->kayittarihi = date('Y-m-d H:i:s');
                                                $arizafiyat->kurtarihi = NULL;
                                                $arizafiyat->save();
                                                try {
                                                    $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                    if($serinodegisim){
                                                        $servistakip->serino=$sayac->serino;
                                                        $servistakip->eskiserino=$eskisayac->serino;
                                                    }else{
                                                        $servistakip->serino=$sayac->serino;
                                                        $servistakip->eskiserino=NULL;
                                                    }
                                                    $servistakip->arizakayittarihi = $arizakayit->arizakayittarihi;
                                                    $servistakip->sonislemtarihi = $arizakayit->arizakayittarihi;
                                                    $servistakip->durum = 2;
                                                    $servistakip->save();
                                                } catch (Exception $e) {
                                                    $eklenenresimler = explode(',', $eklenenler);
                                                    foreach ($eklenenresimler as $resim) {
                                                        File::delete('assets/arizaresim/' . $resim . '');
                                                    }
                                                    DB::rollBack();
                                                    Log::error($e);
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi.', 'text' => 'Servis Takip Bilgisi Güncellenemedi.', 'type' => 'error'));
                                                }
                                                BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-pencil', $netsiscari->cariadi . ' Yerine Ait ' . $bilgi->id . ' Numaralı Arıza Kayıdı Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Arıza Kayıt Numarası:' . $bilgi->id);
                                                DB::commit();
                                                return Redirect::to($this->servisadi.'/arizakayit')->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellendi', 'text' => 'Arıza Kayıdı Başarıyla Güncellendi', 'type' => 'success'));
                                            } catch (Exception $e) {
                                                $eklenenresimler = explode(',', $eklenenler);
                                                foreach ($eklenenresimler as $resim) {
                                                    File::delete('assets/arizaresim/' . $resim . '');
                                                }
                                                DB::rollBack();
                                                Log::error($e);
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Arıza Fiyatlandırması Sistemde Güncellenemedi.', 'type' => 'error'));
                                            }
                                        } catch (Exception $e) {
                                            $eklenenresimler = explode(',', $eklenenler);
                                            foreach ($eklenenresimler as $resim) {
                                                File::delete('assets/arizaresim/' . $resim . '');
                                            }
                                            DB::rollBack();
                                            Log::error($e);
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Arıza Kayıdı Sistemde Güncellenemedi.', 'type' => 'error'));
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Uyarı-Sonuçlar Sistemde Güncellenemedi.', 'type' => 'error'));
                                    }
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Değişen Parçalar Sistemde Güncellenemedi.', 'type' => 'error'));
                                }
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Yapılan İşlemler Sistemde Güncellenemedi.', 'type' => 'error'));
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Seçilen Arıza Tespiti Sistemde Güncellenemedi.', 'type' => 'error'));
                        }
                    }
                }
            }else{
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Sayaç Arızası Sistemde Bulunamadı.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getArizakayitgoster($id) {
        try {
        $arizakayit = ArizaKayit::find($id);
        $arizakayit->servistakip=ServisTakip::where('arizakayit_id',$arizakayit->id)->first();
        $arizakayit->sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
        $arizakayit->hatirlatma_id = BackendController::getHatirlatmaId(3, $this->servisid, $arizakayit->sayacgelen->depogelen_id, $arizakayit->sayacgelen->netsiscari_id);
        $arizakayit->uretimyer = Uretimyer::find($arizakayit->sayacgelen->uretimyer_id);
        $arizakayit->sayac = Sayac::find($arizakayit->sayac_id);
        $arizakayit->sayacadi = SayacAdi::find($arizakayit->sayacadi_id);
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
        $arizakodlari = ArizaKod::where('sayactur_id',$this->servisid)->get();
        $yapilanlar = Yapilanlar::where('sayactur_id',$this->servisid)->get();
        $degisenler = Degisenler::where('sayactur_id',$this->servisid)->whereIn('id',$arizakayit->sayacparcalar)->get();
        $uyarilar = Uyarilar::where('sayactur_id',$this->servisid)->orderBy('kullanim','desc')->orderBy('tanim','asc')->get();
        return View::make($this->servisadi.'.arizakayitgoster',array('arizakayit'=>$arizakayit,'arizakodlari'=>$arizakodlari,'yapilanlar'=>$yapilanlar,'degisenler'=>$degisenler,'uyarilar'=>$uyarilar))->with(array('title'=>$this->servisbilgi.' Arıza Kayıdı Bilgi Ekranı'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Bilgisi Getirilemedi.', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function postArizakayitgoster($id) {
        try {
            $rules = ['t1deger'=>'required','t2deger'=>'required','t3deger'=>'required','t4deger'=>'required','ttoplam'=>'required','kalankredi'=>'required'];
            $validate = Validator::make(Input::all(),$rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $arizakayit = ArizaKayit::find($id);
            $netsiscari = NetsisCari::find($arizakayit->netsiscari_id);
            $bilgi=$arizakayit;
            $ilkkredi=str_replace(',','.',Input::get('t1deger'));
            $ilkharcanan=str_replace(',','.',Input::get('t2deger'));
            $ilkmekanik=str_replace(',','.',Input::get('t3deger'));
            $kalan=str_replace(',','.',Input::get('t4deger'));
            $harcanan=str_replace(',','.',Input::get('ttoplam'));
            $mekanik=str_replace(',','.',Input::get('kalankredi'));
            $arizaaciklama=Input::get('arizaaciklama');
            $arizanot=Input::get('arizanot');
            try {
                $yapilanlar = Input::get('yapilanlar');
                $yapilanlist = Input::get('yapilanlist');
                foreach($yapilanlar as $yapilan)
                {
                    $yapilanis=Yapilanlar::find($yapilan);
                    $yapilanis->kullanim=1;
                    $yapilanis->save();
                }
                $uyarilar = Input::get('uyarilar');
                $uyarilist = Input::get('uyarilist');
                foreach($uyarilar as $uyari)
                {
                    $uyarisonuc=Uyarilar::find($uyari);
                    $uyarisonuc->kullanim+=1;
                    $uyarisonuc->save();
                }
                $sayacyapilan = SayacYapilan::find($arizakayit->sayacyapilan_id);
                $sayacyapilan->yapilanlar = $yapilanlist;
                $sayacyapilan->kullanici_id = Auth::user()->id;
                $sayacyapilan->tarih = date('Y-m-d H:i:s');
                $sayacyapilan->durum = 0;
                $sayacyapilan->save();
                $sayacuyari = SayacUyari::find($arizakayit->sayacuyari_id);
                $sayacuyari->uyarilar = $uyarilist;
                $sayacuyari->kullanici_id = Auth::user()->id;
                $sayacuyari->tarih = date('Y-m-d H:i:s');
                $sayacuyari->durum = 0;
                $sayacuyari->save();

                $arizakayit->ilkkredi = $ilkkredi;
                $arizakayit->ilkharcanan = $ilkharcanan;
                $arizakayit->ilkmekanik = $ilkmekanik;
                $arizakayit->kalankredi = $kalan;
                $arizakayit->harcanankredi = $harcanan;
                $arizakayit->mekanik = $mekanik;
                $arizakayit->arizaaciklama = $arizaaciklama;
                $arizakayit->arizanot = $arizanot;
                $arizakayit->save();
                BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-pencil', $netsiscari->cariadi . ' Yerine Ait ' . $bilgi->id . ' Numaralı Arıza Kayıdı Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Arıza Kayıt Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::to($this->servisadi . '/arizakayit')->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellendi', 'text' => 'Arıza Kayıdı Başarıyla Güncellendi', 'type' => 'success'));
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Arıza Kayıdı Güncellenemedi', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getServiskod()
    {
        try {
            $sayacadiid=Input::get('sayacadiid');
            if (Input::has('sayaccapid')) {
                $sayaccapid=Input::get('sayaccapid');
                $sayacparca = SayacParca::where('sayacadi_id', $sayacadiid)->where('sayaccap_id', $sayaccapid)->where('sayactur_id', $this->servisid)->first();
            } else {
                $sayacparca = SayacParca::where('sayacadi_id', $sayacadiid)->where('sayactur_id', $this->servisid)->first();
            }
            if ($sayacparca) {
                $serviskod = $sayacparca->servisstokkod_id;
                return Response::json(array('durum' => true, 'serviskod' => $serviskod));
            } else {
                return Response::json(array('durum' => false, 'title' => 'Servis Kodu Bulunamadı', 'text' => 'Seçilen Sayaç Adı Servis Kodu ile uyumlu değil.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            return Response::json(array('durum' => false, 'title' => 'Servis Kodu Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getCariyer()
    {
        try {
            $netsiscariid=Input::get('netsiscariid');
            $cariyerler = CariYer::where('netsiscari_id', $netsiscariid)->where('durum',1)->get(array('uretimyer_id'))->toArray();
            $uretimyer = Uretimyer::whereIn('id', $cariyerler)->where('mekanik',0)->get();
            $abonesayackayit = AboneSayacKayit::where('netsiscari_id',$netsiscariid)->where('sayactur_id',$this->servisid)->where('durum',0)->get();
            foreach ($abonesayackayit as $kayit){
                $kayit->kargofirma = KargoFirma::find($kayit->kargofirma_id);
            }
            if ($uretimyer->count() > 0) {
                return Response::json(array('durum' => true, 'uretimyer' => $uretimyer,'abonesayackayit'=>$abonesayackayit));
            } else {
                return Response::json(array('durum' => false, 'title' => 'Seçilen Cari ile Bir Üretim Yeri Eşleştirilmemiş!', 'text' => 'Önce Cari Bilgisi ile bir Üretim Yeri Eşleştirin!', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'title' => 'Cari ve Yer Bilgisi Getirilirken Hata Oluştu!', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getAbonekayitdetay()
    {
        try {
            $kayitid=Input::get('kayitid');
            $abonesayackayit = AboneSayacKayit::find($kayitid);
            if(!$abonesayackayit)
                return Response::json(array('durum' => false, 'title' => 'Müşteri Kayıt Bilgisi Getirilemedi!', 'text' => 'Müşteri Kayıdı Bulunamadı', 'type' => 'error'));
            $abonesayackayit->kargofirma = KargoFirma::find($abonesayackayit->kargofirma_id);
            $abonesayackayit->netsiscari = NetsisCari::find($abonesayackayit->netsiscari_id);
            $abonesayackayitbilgi = AboneSayacKayitBilgi::where('abonesayackayit_id',$abonesayackayit->id)->get();
                return Response::json(array('durum' => true, 'abonesayackayitbilgi' => $abonesayackayitbilgi,'abonesayackayit'=>$abonesayackayit));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'title' => 'Müşteri Kayıt Bilgisi Getirilirken Hata Oluştu!', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getHurdanedeniekle() {
        try {
            $hurdaneden = Input::get('hurdaneden');
            If (HurdaNedeni::where('nedeni', $hurdaneden)->where('sayactur_id', $this->servisid)->first()) {
                return Response::json(array('durum' => false, 'title' => 'Doğrulama Hatası', 'text' => 'Bu Hurda Nedeni mevcut.', 'type' => 'warning'));
            }
            DB::beginTransaction();
            $hurdanedeni = new HurdaNedeni;
            $hurdanedeni->sayactur_id = $this->servisid;
            $hurdanedeni->nedeni = $hurdaneden;
            $hurdanedeni->save();
            $hurdanedenleri = HurdaNedeni::where('sayactur_id', $this->servisid)->get();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $hurdaneden . ' Tanımlı Hurda Nedeni Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Hurda Neden Numarası:' . $hurdanedeni->id);
            DB::commit();
            return Response::json(array('durum' => true, 'hurdanedenleri' => $hurdanedenleri,'id'=>$hurdanedeni->id, 'title' => 'Hurda Nedeni Eklendi', 'text' => 'Hurda Nedeni Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Response::json(array('durum' =>false, 'title' => 'Hurda Nedeni Eklenemedi', 'text' => 'Hurda Nedeni Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }
}
