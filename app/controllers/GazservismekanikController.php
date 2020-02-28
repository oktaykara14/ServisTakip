<?php
//transaction işlemi tamamlandı
class GazservismekanikController extends BackendController {
    public $servisadi = 'mekanikgaz';
    public $servisid = 5;
    public $servisbilgi = 'Gaz';

    public function getSayackayit($hatirlatma_id=false) {
        if($hatirlatma_id)
            return View::make($this->servisadi.'.sayackayit',array('hatirlatma_id'=>$hatirlatma_id))->with(array('title'=>$this->servisbilgi.' Servis Mekanik Sayaç Kayıdı'));
        else
            return View::make($this->servisadi.'.sayackayit')->with(array('title'=>$this->servisbilgi.' Servis Mekanik Sayaç Kayıdı'));
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
                    "kullanici.nadi_soyadi","sayacgelen.arizakayit","sayacgelen.depolararasi","sayacgelen.kalibrasyon"))
                ->leftjoin("servistakip", "servistakip.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("uretimyer", "sayacgelen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("servisstokkod", "sayacgelen.stokkodu", "=", "servisstokkod.stokkodu")
                ->leftjoin("kullanici", "sayacgelen.kullanici_id", "=", "kullanici.id");

        }else{
            $query = SayacGelen::where('sayacgelen.servis_id',$this->servisid)
                ->select(array("sayacgelen.id","sayacgelen.serino","uretimyer.yeradi","servisstokkod.stokadi","sayacgelen.depotarihi","kullanici.adi_soyadi",
                    "sayacgelen.eklenmetarihi","servistakip.eskiserino","sayacgelen.gdepotarihi","sayacgelen.gdurumtarihi","uretimyer.nyeradi","servisstokkod.nstokadi",
                    "kullanici.nadi_soyadi","sayacgelen.arizakayit","sayacgelen.depolararasi","sayacgelen.kalibrasyon"))
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
                if(!$model->arizakayit && !$model->depolararasi && !$model->kalibrasyon )
                    return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/sayackayitduzenle/".$model->id."' > Düzenle </a>";
                else
                    return "<a class='btn btn-sm btn-info' href='".$root."/".$this->servisadi."/sayackayitgoster/".$model->id."' > Göster </a>";
            })
            ->make(true);
    }

    public function getSayackayitekle() {
        $uretimyerleri = UretimYer::where('mekanik',1)->get();
        $netsiscariler = NetsisCari::where('caridurum','A')->whereIn('caritipi',array('A','D'))
            ->whereIn('subekodu',array(-1,8))->whereNotIn('carikod',(function ($query){$query->select('carikod')->from('kodharichar')->where('subekodu', 8);}))
            ->orderBy('carikod','asc')->get();
        $sayacadlari = SayacAdi::where('sayactur_id',$this->servisid)->get();
        $servisstokkodlari = ServisStokKod::where('servisid',$this->servisid)->get();
        $yillar = array_combine(range(date("Y"), 1990), range(date("Y"), 1990));
        return View::make($this->servisadi.'.sayackayitekle',array('uretimyerleri'=>$uretimyerleri,'netsiscariler'=>$netsiscariler,'sayacadlari'=>$sayacadlari,
            'servisstokkodlari'=>$servisstokkodlari,'yillar'=>$yillar))->with(array('title'=>$this->servisbilgi.' Mekanik Sayaç Kayıdı Ekle'));
    }

    public function postSayackayitexcel(){
        try {
            If (Input::hasFile('file')) {
                $dosya = Input::file('file');
                $uzanti = $dosya->getClientOriginalExtension();
                $isim = Str::slug(str_random(5)) . '.' . $uzanti;
                $dosya->move('assets/temp/', $isim);
                $path ='assets/temp/'.$isim;
                $excelvalues = $errors = array();
                $data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                })->get();
                if(!empty($data) && $data->count()){
                    foreach ($data as $key => $value) {
                        if(count($value)>=7){
                            $marka=SayacMarka::where('marka',strtoupper($value->marka))->first();
                            if($marka){
                                $sayactip=SayacTip::where('sayacmarka_id',$marka->id)->where('kisaadi',$value->tip)->get(array('id'))->toArray();
                                if($sayactip){
                                    $sayacadi=SayacAdi::where('kisaadi',$value->model)->whereIn('sayactip_id',$sayactip)->where('sayactur_id',5)->first();
                                    if($sayacadi){
                                        $sayacparca = SayacParca::where('sayacadi_id', $sayacadi->id)->where('sayactur_id', 5)->first();
                                        if ($sayacparca) {
                                            $serviskod = $sayacparca->servisstokkod_id;
                                            $cellvalues = array("serino" => $value->sayac_no , "sayacadi" => $sayacadi->id, "serviskod" => $serviskod,
                                                "imalyili" => $value->imalat_yili,"endeks" => $value->endeks);
                                            array_push($excelvalues,$cellvalues);
                                        }else{
                                            array_push($errors,array("error"=>$sayacadi->sayacadi." için Sayaç Parçaları Ekli Değil."));
                                        }
                                    }else{
                                        array_push($errors,array("error"=>$value->model." için ".$marka->marka." Markasında Sayaç Adı Ekli Değil."));
                                    }
                                }else{
                                    array_push($errors,array("error"=>$value->tip." için ".$marka->marka." Markasında Sayaç Tipi Ekli Değil."));
                                }
                            }else{
                                array_push($errors,array("error"=>$value->marka." Markası Sistemde Ekli Değil."));
                            }
                        }
                    }
                }
                File::delete($path);
                return Response::json(array('durum' => true, 'degerler' => $excelvalues,'adet'=>count($excelvalues),'hatalar' => $errors));
            }else {
                return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Excelden bilgi alınırken hata oluştu!', 'text' => 'Excel Dosyası Bulunamadı'));
            }
        } catch (Exception $e) {
            return Response::json(array('durum' => false, 'type'=>'error','title'=>'Excelden bilgi alınırken hata oluştu!','text'=>str_replace("'","\'",$e->getMessage())));
        }
    }

    public function postSayackayitekle() {
        try {
            $rules = ['gelis' => 'required', 'uretimyerleri' => 'required', 'cariadi' => 'required', 'serino' => 'required', 'sayacadlari' => 'required', 'sayaccaplari' => 'required', 'serviskodlari' => 'required', 'endeks' => 'required' ];
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
            $uretimtarih = Input::get('uretimtarih');
            $endeks = Input::get('endeks');
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
                    if(isset($endeks[$i])) {
                        if ($endeks[$i] == "") {
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
                $sayaclar = BackendController::DepoGirisGrupla($serinolar, $uretimyerleri, $serviskodlari, $sayacadlari, $sayaccaplari, $uretimtarih, $endeks);
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
                        Input::flash();
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
                                    $sayacgruplari = $sayacgrup['sayac']; //eklenecek sayaçlar
                                    foreach ($sayacgruplari as $girilecek) {
                                        $serino = $girilecek['serino'];
                                        $sayacadi = $girilecek['sayacadi'];
                                        $sayaccap = $girilecek['sayaccap'];
                                        $uretimyeri = $girilecek['uretimyeri'];
                                        $uretimtarihi = $girilecek['uretimyili'];
                                        $endeks = str_replace(',', '.', $girilecek['endeks']);
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
                                                $sayacgelen->beyanname=-2;
                                                $sayacgelen->endeks = $endeks;
                                                $sayacgelen->save();
                                                $sayac = Sayac::where('serino', $serino)->where('sayacadi_id', $sayacadi)->where('sayaccap_id', 1)->where('uretimyer_id', $uretimyeri)
                                                    ->where('sayactur_id', $this->servisid)->first();
                                                if (!$sayac) // mekanik sayaç yoksa sayaç listesine eklenecek
                                                {
                                                    $sayac = new Sayac;
                                                    $sayac->serino = $serino;
                                                    $sayac->cihazno = $serino;
                                                    $sayac->sayactur_id = $this->servisid;
                                                    $sayac->sayacadi_id = $sayacadi;
                                                    $sayac->sayaccap_id = 1;
                                                    if($uretimtarihi!=""){
                                                        $utarih = date('Y-m-d', mktime(0, 0, 0, 1, 1, $uretimtarihi));
                                                        $sayac->uretimtarihi = $utarih;
                                                    }
                                                    $sayac->uretimyer_id = $uretimyeri;
                                                    $sayac->save();
                                                }
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
                                                Input::flash();
                                                Log::error($e);
                                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Servis Takip Kısmına Kayıt Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
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
                                            Input::flash();
                                            Log::error($e);
                                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Hatırlatma Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
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
                    Input::flash();
                    Log::error($e);
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
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
        $sayacgelen->sayac=Sayac::where('sayactur_id',$this->servisid)->where('serino',$sayacgelen->serino)->where('uretimyer_id',$sayacgelen->uretimyer_id)->first();
        $cariyerler=CariYer::where('netsiscari_id',$sayacgelen->netsiscari_id)->where('durum',1)->get(array('uretimyer_id'))->toArray();
        $uretimyerleri = Uretimyer::whereIn('id',$cariyerler)->get();
        $sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
        $sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
        $netsiscari = NetsisCari::find($sayacgelen->netsiscari_id);
        if($netsiscari->caridurum!="A")
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
        $servisstokkod = ServisStokKod::where('stokkodu',$sayacgelen->stokkodu)->first();
        $yillar = array_combine(range(date("Y"), 1990), range(date("Y"), 1990));
        return View::make($this->servisadi.'.sayackayitduzenle',array('sayacgelen'=>$sayacgelen,'uretimyerleri'=>$uretimyerleri,'netsiscari'=>$netsiscari,'sayacadi'=>$sayacadi,'sayaccap'=>$sayaccap,'servisstokkod'=>$servisstokkod,'yillar'=>$yillar))->with(array('title'=>$this->servisbilgi.' Mekanik Sayaç Kayıdı Düzenleme Ekranı'));
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
            $uretimtarihi = Input::get('uretimtarih');
            $subedurum = 0;
            if ($servistakip) {
                if ($servistakip->arizakayit_id == null)
                    if (BackendController::SayacDurum($serino,$uretimyer_id, $this->servisid, $subedurum, $servistakip->id)) {
                        Input::flash();
                        DB::rollBack();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => $serino . ' Nolu Sayacın Depo Çıkışı Yapılmamış.Önce Sayaca Ait Diğer İşlemleri Bitiriniz.', 'type' => 'error'));
                    }
                $sayac = Sayac::where('serino', $serino)->where('sayactur_id', $this->servisid)->first();
                if (!$sayac) {
                    $sayac = Sayac::where('serino', $sayacgelen->serino)->first();
                    $sayac->serino = $serino;
                    if ($uretimtarihi != "") {
                        $utarih = date('Y-m-d', mktime(0, 0, 0, 1, 1, $uretimtarihi));
                        $sayac->uretimtarihi = $utarih;
                    }
                    $sayac->save();
                } else {
                    if ($uretimtarihi != "") {
                        $utarih = date('Y-m-d', mktime(0, 0, 0, 1, 1, $uretimtarihi));
                        $sayac->uretimtarihi = $utarih;
                    }
                    $sayac->save();
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
        $sayacgelen->sayac=Sayac::where('sayactur_id',$this->servisid)->where('serino',$sayacgelen->serino)->where('uretimyer_id',$sayacgelen->uretimyer_id)->first();
        $sayacgelen->servistakip=ServisTakip::where('sayacgelen_id',$sayacgelen->id)->first();
        $uretimyerleri = UretimYer::where('mekanik',1)->get();
        $sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
        $sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
        $netsiscari = NetsisCari::find($sayacgelen->netsiscari_id);
        $servisstokkod = ServisStokKod::where('stokkodu',$sayacgelen->stokkodu)->first();
        return View::make($this->servisadi.'.sayackayitgoster',array('sayacgelen'=>$sayacgelen,'uretimyerleri'=>$uretimyerleri,'netsiscari'=>$netsiscari,'sayacadi'=>$sayacadi,'sayaccap'=>$sayaccap,'servisstokkod'=>$servisstokkod))->with(array('title'=>$this->servisbilgi.' Mekanik Sayaç Kayıdı Bilgi Ekranı'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Bilgilerinde Hata Var!', 'text' => str_replace("'","'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getArizakayit($hatirlatma_id=false) {
        $depogelenler = DepoGelen::leftJoin('sayacgelen','sayacgelen.depogelen_id','=','depogelen.id')->
        where('depogelen.durum',1)->where('depogelen.kayitdurum',1)->where('depogelen.servis_id',$this->servisid)->where('sayacgelen.arizakayit',0)
            ->groupBy(array("depogelen.id","depogelen.tarih","depogelen.adet","depogelen.carikod"))->get(array("depogelen.id","depogelen.tarih","depogelen.adet","depogelen.carikod"));
        foreach($depogelenler as $depogelen){
            $depogelen->netsiscari = NetsisCari::where('carikod',$depogelen->carikod)->first();
        }
        $arizakodlari = ArizaKod::where('sayactur_id',$this->servisid)->orderBy('kullanim','desc')->orderBy('tanim','asc')->get();
        $yapilanlar = Yapilanlar::where('sayactur_id',$this->servisid)->get();
        $degisenler = Degisenler::where('sayactur_id',$this->servisid)->get();
        $uyarilar = Uyarilar::where('sayactur_id',$this->servisid)->orderBy('kullanim','desc')->orderBy('tanim','asc')->get();
        if($hatirlatma_id)
            return View::make($this->servisadi.'.arizakayit',array('hatirlatma_id'=>$hatirlatma_id,'depogelenler'=>$depogelenler,'arizakodlari'=>$arizakodlari,'yapilanlar'=>$yapilanlar,'degisenler'=>$degisenler,'uyarilar'=>$uyarilar))->with(array('title'=>$this->servisbilgi.' Servis Mekanik Arıza Kayıdı'));
        else
            return View::make($this->servisadi.'.arizakayit',array('depogelenler'=>$depogelenler,'arizakodlari'=>$arizakodlari,'yapilanlar'=>$yapilanlar,'degisenler'=>$degisenler,'uyarilar'=>$uyarilar))->with(array('title'=>$this->servisbilgi.' Servis Mekanik Arıza Kayıdı'));
    }

    public function getDepogelensayaclar()
    {
        try {
            $depogelenid=Input::get('depogelenid');
            $sayacgelenler = SayacGelen::where('depogelen_id',$depogelenid)->where('arizakayit',0)->get();
            if ($sayacgelenler->count() > 0) {
                $sayacadi = SayacAdi::find($sayacgelenler->first()->sayacadi_id);
                $tipmodel = $sayacadi->kisaadi.' '.$sayacadi->sayactip->tipadi;
                if($sayacadi->sayactip->tipadi=="DİYAFRAM"){
                    $sayacadi->sayacozellik=SayacOzellik::where('sayacadi_id',$sayacadi->id)->first();
                }
                return Response::json(array('durum' => true, 'sayacgelenler' => $sayacgelenler,'sayacadi'=>$sayacadi,'tipmodel'=>$tipmodel));
            } else {
                return Response::json(array('durum' => false, 'title' => 'Seçilen Depo Bilgisine Ait Arıza Kayıdı Bekleyen Sayaç Bulunamadı!', 'text' => 'Seçilen Depo Bilgisine Ait Arıza Kayıdı Bekleyen Sayaç Bulunamadı!', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'title' => 'Depo Gelen Bilgisi Getirilirken Hata Oluştu!', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
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
                if(!$sayacgelen->kalibrasyon){ //kalibrasyonu yapılmadıysa sil
                    try {
                        $kalibrasyon = Kalibrasyon::where('sayacgelen_id',$sayacgelen->id)->where('durum',0)->first();
                        if($kalibrasyon){
                            $grup=KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
                            if(!$grup->kalibrasyondurum){
                                $grup->adet-=1;
                                if($grup->adet==$grup->biten)
                                    $grup->kalibrasyondurum=1;
                                $grup->save();
                            }
                            $servistakip->kalibrasyon_id = NULL;
                            $servistakip->save();
                            $kalibrasyon->delete();
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıt Hatası', 'text' => 'Kalibrasyona Gönderilen Sayaç Silinirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                    }
                    BackendController::HatirlatmaSil(8,$netsiscari->id,$sayacgelen->servis_id,1);
                }
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
            $sayacgelen->beyanname = -2;
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
                else if($model->gdurum=='Yedek Parça Bekliyor')
                    return "<a class='btn btn-sm btn-info' href='".$root."/".$this->servisadi."/arizakayitgoster/".$model->id."' > Göster </a>";
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
                'sayacgelen.serino', 'sayacgelen.depotarihi', 'sayacgelen.uretimyer_id', 'sayacgelen.sayacadi_id',
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
                $sayacgelen->sayactip = SayacTip::find($sayacgelen->sayacadi->sayactip_id);
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
                    'sayacgelen.serino', 'sayacgelen.depotarihi', 'sayacgelen.uretimyer_id', 'sayacgelen.sayacadi_id','sayacgelen.endeks',
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
                    $sayacgelen->sayactip = SayacTip::find($sayacgelen->sayacadi->sayactip_id);
                    if($sayacgelen->sayactip->tipadi=="DİYAFRAM"){
                        $sayacgelen->sayacozellik=SayacOzellik::where('sayacadi_id',$sayacgelen->sayacadi_id)->first();
                    }
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
                            'sayacgelen.serino', 'sayacgelen.depotarihi', 'sayacgelen.uretimyer_id', 'sayacgelen.sayacadi_id','sayacgelen.endeks',
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
                                $sayacgelen->sayactip = SayacTip::find($sayacgelen->sayacadi->sayactip_id);
                                if($sayacgelen->sayactip->tipadi=="DİYAFRAM"){
                                    $sayacgelen->sayacozellik=SayacOzellik::where('sayacadi_id',$sayacgelen->sayacadi_id)->first();
                                }
                                $sayacgelen->sayac = Sayac::find($sayacgelen->sayac_id);
                                if ($sayacgelen->sayac) { //sayac bilgisi belliyse
                                    $sayacgelen->sayac->sayacadi = SayacAdi::find($sayacgelen->sayac->sayacadi_id);
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
                                $sayacgelen->sayactip = SayacTip::find($sayacgelen->sayacadi->sayactip_id);
                                if($sayacgelen->sayactip->tipadi=="DİYAFRAM"){
                                    $sayacgelen->sayacozellik=SayacOzellik::where('sayacadi_id',$sayacgelen->sayacadi_id)->first();
                                }
                                if ($sayacgelen->sayac) { //sayac bilgisi belliyse
                                    $sayacgelen->sayac->sayacadi = SayacAdi::find($sayacgelen->sayac->sayacadi_id);
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
                                            if ($sayacgelen->sayacadi_id != $sayacgelen->sayac->sayacadi_id) {
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
            $uretimtarihi = date("Y-m-d", strtotime(Input::get('uretimtarihi')));
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
                                $sayac->uretimtarihi = $uretimtarihi;
                                $sayac->save();
                            }
                        }else{
                            $sayac=Sayac::find($sayacid);
                            $sayac->uretimyer_id=$uretimyer;
                            $sayac->sayactur_id=$sayactur;
                            $sayac->sayacadi_id=$sayacadi;
                            $sayac->sayaccap_id=$sayaccap;
                            $sayac->uretimtarihi = $uretimtarihi;
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
                            $sayac->uretimtarihi = $uretimtarihi;
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
                    $sayac=Sayac::where('serino',$serino)->where('uretimyer_id',$uretimyer)->where('sayactur_id', $this->servisid)->first();
                    if($sayac){
                        $sayac->sayactur_id=$sayactur;
                        $sayac->sayacadi_id=$sayacadi;
                        $sayac->sayaccap_id=$sayaccap;
                        $sayac->uretimtarihi = $uretimtarihi;
                        $sayac->save();
                    }else{
                        $sayac = new Sayac;
                        $sayac->serino = $serino;
                        $sayac->cihazno = $serino;
                        $sayac->sayactur_id = $this->servisid;
                        $sayac->sayacadi_id = $sayacadi;
                        $sayac->sayaccap_id = 1;
                        $sayac->uretimtarihi = $uretimtarihi;
                        $sayac->uretimyer_id = $uretimyer;
                        $sayac->save();
                        return Response::json(array('durum'=>true, 'title' => 'Yeni Seri Numarası Eklendi', 'text' => 'Yeni Seri Numarası Başarıyla Eklendi', 'type' => 'success'));
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
                    $arizakayit->sayactip = SayacTip::find($arizakayit->sayacadi->sayactip_id);
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
                    $arizakayit->aksesuarlar = BackendController::getAksesuar($arizakayit->aksesuar);
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
            $sayacadi=SayacAdi::find($sayacadiid);
            $sayactip=SayacTip::find($sayacadi->sayactip_id);
            if (Input::has('sayaccapid')) {
                $sayaccapid=Input::get('sayaccapid');
                $sayacparcalar = SayacParca::where('sayacadi_id', $sayacadiid)->where('sayaccap_id', $sayaccapid)->first();
                if ($sayacparcalar) {
                    $parcalar = explode(',', $sayacparcalar->parcalar);
                    $sayacparcalar->parca = Degisenler::whereIn('id', $parcalar)->get();
                    return Response::json(array('durum' => true, 'sayacparcalar' => $sayacparcalar,'sayactip'=>$sayactip));
                }
            } else {
                $sayacparcalar = SayacParca::where('sayacadi_id', $sayacadiid)->first();
                if ($sayacparcalar) {
                    $parcalar = explode(',', $sayacparcalar->parcalar);
                    $sayacparcalar->parca = Degisenler::whereIn('id', $parcalar)->get();
                    return Response::json(array('durum' => true, 'sayacparcalar' => $sayacparcalar,'sayactip'=>$sayactip));
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

    public function getSayacaksesuar(){
        try {
            $sayacadiid=Input::get('sayacadiid');
            $sayacadi = SayacAdi::find($sayacadiid);
            $aksesuarlar = Aksesuar::where('sayactip_id', $sayacadi->sayactip_id)->get();
            return Response::json(array('durum' => true, 'aksesuarlar' => $aksesuarlar));
        } catch (Exception $e) {
            return Response::json(array('durum' => false,'title'=>'Sayaç Aksesuarlarında Hata Var','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
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
        $aksesuarlar=array();
        if($hatirlatma_id)
        {
            $hatirlatma = Hatirlatma::find($hatirlatma_id);
            $netsiscari = NetsisCari::find($hatirlatma->netsiscari_id);
            if($netsiscari->caridurum!="A")
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
            $servisstokkod = ServisStokKod::where('stokkodu',$hatirlatma->servisstokkodu)->first();
            return View::make($this->servisadi.'.arizakayitekle',array('arizakodlari'=>$arizakodlari,'yapilanlar'=>$yapilanlar,'degisenler'=>$degisenler,'uyarilar'=>$uyarilar,'sayacadlari'=>$sayacadlari,'hatirlatmalar'=>$hatirlatma,'netsiscari'=>$netsiscari,'servisstokkod'=>$servisstokkod,'hurdanedenleri'=>$hurdanedenleri,'aksesuarlar'=>$aksesuarlar))->with(array('title'=>$this->servisbilgi.' Sayaç Arıza Kayıdı Ekle'));
        }else{
            return View::make($this->servisadi.'.arizakayitekle',array('arizakodlari'=>$arizakodlari,'yapilanlar'=>$yapilanlar,'degisenler'=>$degisenler,'uyarilar'=>$uyarilar,'sayacadlari'=>$sayacadlari,'hurdanedenleri'=>$hurdanedenleri,'aksesuarlar'=>$aksesuarlar))->with(array('title'=>$this->servisbilgi.' Sayaç Arıza Kayıdı Ekle'));
        }
    }

    public function postArizakayitekle() {
        try {
            $rules = ['cariadi' => 'required', 'istek' => 'required', 'uretimyer' => 'required', 'serino' => 'required',
                'uretim' => 'required', 'sayacadi' => 'required', 'garanti' => 'required', 'ilkmekanik' => 'required',
                'baglanticap' => 'required','pmax' => 'required','qmax' => 'required','qmin' => 'required',
                'arizalar' => 'required', 'yapilanlar' => 'required', 'degisenler' => 'required', 'uyarilar' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $ilkmekanik = str_replace(',', '.', Input::get('ilkmekanik'));
            $baglanticap = Input::get('baglanticap');
            $pmax = Input::get('pmax');
            $qmax = Input::get('qmax');
            $qmin = Input::get('qmin');
            if (Input::has('aksesuar')) {
                $aksesuarlist = "";
                $aksesuarlar = Input::get('aksesuar');
                foreach ($aksesuarlar as $aksesuar) {
                    $aksesuarlist .= ($aksesuarlist == "" ? "" : ",") . $aksesuar;
                }
                if($aksesuarlist=="") $aksesuarlist=null;
            }else{
                $aksesuarlist=null;
            }
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
            $sertifika = Input::get('sertifika');
            $hf2 = Input::get('hf2');
            $hf3 = Input::get('hf3');
            $hf32 = Input::get('hf32');
            if(Input::has('ozeldurum')) // 0 normal 3 yedek parça bekliyor 4 şikayetli muayene 5 müdahaleli sayaç 6 yeni sayaç verildi 7 geri iade 8 geri iade kalibrasyonsuz
                $ozeldurum=Input::get('sayacdurum');
            else
                $ozeldurum=0;
            $garanti = $ozeldurum==6 ? 1 : Input::get('garanti'); // yeni sayaç verilirse garanti içinde olacak
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
                if ($hurdadurum == 1 || $ozeldurum==6 || $ozeldurum==8) //hurdaya ayrıldıysa ya da yeni sayaç verilme durumu varsa yada kalibrasyonsuz geri iade ise
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
                                        $arizakayit->ilkmekanik = $ilkmekanik;
                                        $arizakayit->aksesuar = $aksesuarlist;
                                        $arizakayit->baglanticap=$baglanticap;
                                        $arizakayit->pmax=$pmax;
                                        $arizakayit->qmax=$qmax;
                                        $arizakayit->qmin=$qmin;
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
                                        $arizakayit->arizakayit_durum = $ozeldurum==8 ? 8 : 2;
                                        $arizakayit->arizanot = $arizanot;
                                        $arizakayit->serinodegisim=$serinodegisim;
                                        $arizakayit->yenisayac= $ozeldurum==6 ? 1 : 0;
                                        $arizakayit->sertifika=$sertifika=="" ? null : $sertifika;
                                        $arizakayit->hf2=$hf2=="" ? null : $hf2;
                                        $arizakayit->hf3=$hf3=="" ? null : $hf3;
                                        $arizakayit->hf32=$hf32=="" ? null : $hf32;
                                        $arizakayit->rapordurum = 0;
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
                                            $sayacgelen->sayacdurum = $ozeldurum==8 ? 1 : 3; // 1 arıza kayıt 2 kalibrasyon 3 hurda
                                            $sayacgelen->teslimdurum = $ozeldurum==8 ? 2 : 3; //0 bekliyor 1 teslimat 2 geri gönderim 3 hurda 4 depolararasi 5 periyodik bakım
                                            $sayacgelen->sayacadi_id = $sayacadiid;
                                            $sayacgelen->sayaccap_id = $sayaccapid;
                                            $sayacgelen->beyanname = -2; // -2 hurda geri gonderim sayackayit arizakayit -1 dahil değil 0 kalibrasyon 1 tamamlanan
                                            $sayacgelen->kalibrasyon = 1;
                                            $sayacgelen->save();
                                            $depogelen = DepoGelen::find($sayacgelen->depogelen_id);
                                            if (BackendController::getArizaKayitDurum($depogelen->id)) {
                                                $depogelen->kayitdurum = 1;
                                                $depogelen->save();
                                            }
                                            if($depogelen->periyodik)
                                                $periyodik=1;
                                            else
                                                $periyodik=0;
                                            $sayac->sayacadi_id = $sayacadiid;
                                            $sayac->sayaccap_id = $sayaccapid;
                                            $sayac->sayactur_id = $sayacadi->sayactur_id;
                                            $sayac->uretimtarihi = $uretimtarih;
                                            $sayac->kullanici_id = Auth::user()->id;
                                            $sayac->hurdadurum = $ozeldurum!=8 ? 1 : $sayac->hurdadurum;
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
                                                if($ozeldurum!=8){
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
                                                            ->where('depodurum', 0)->where('tipi', 3)->where('subegonderim', $subedurum)->where('periyodik',$periyodik)->first();
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
                                                            $depoteslim->periyodik=$periyodik;
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
                                                }else{
                                                    try {
                                                        $flag=0;
                                                        $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                            ->where('depodurum', 0)->where('tipi', 2)->where('subegonderim', $subedurum)->where('periyodik',$periyodik)->first();
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
                                                            $depoteslim->tipi = 2;
                                                            $depoteslim->periyodik=$periyodik;
                                                            $depoteslim->subegonderim = $subedurum;
                                                            $depoteslim->parabirimi_id = $uretimyer->parabirimi_id;
                                                        }
                                                        $depoteslim->save();
                                                        try {
                                                            $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                            $servistakip->arizakayit_id = $arizakayit->id;
                                                            $servistakip->arizafiyat_id = $arizafiyat->id;
                                                            $servistakip->durum = 2;
                                                            $servistakip->arizakayittarihi = $arizakayit->arizakayittarihi;
                                                            $servistakip->sonislemtarihi = $arizakayit->arizakayittarihi;
                                                            $servistakip->save();
                                                            if(!$flag){
                                                                BackendController::HatirlatmaIdGuncelle($hatirlatmaid,1);
                                                                BackendController::HatirlatmaEkle(9, $netsiscari->id,$sayacgelen->servis_id, 1);
                                                                BackendController::BildirimEkle(3, $netsiscari->id,$sayacgelen->servis_id, 1,$depogelen->id,$depogelen->servisstokkodu);
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
                                                    $arizakayit->ilkmekanik = $ilkmekanik;
                                                    $arizakayit->aksesuar = $aksesuarlist;
                                                    $arizakayit->baglanticap=$baglanticap;
                                                    $arizakayit->pmax=$pmax;
                                                    $arizakayit->qmax=$qmax;
                                                    $arizakayit->qmin=$qmin;
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
                                                    $arizakayit->arizakayit_durum = $ozeldurum!=0 ? $ozeldurum : 1;
                                                    $arizakayit->arizanot = $arizanot;
                                                    $arizakayit->serinodegisim=$serinodegisim;
                                                    $arizakayit->sertifika=$sertifika=="" ? null : $sertifika;
                                                    $arizakayit->hf2=$hf2=="" ? null : $hf2;
                                                    $arizakayit->hf3=$hf3=="" ? null : $hf3;
                                                    $arizakayit->hf32=$hf32=="" ? null : $hf32;
                                                    $arizakayit->rapordurum = -1;
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
                                                        if($ozeldurum==3 || $ozeldurum==5)
                                                            $sayacgelen->kalibrasyon=1;
                                                        else
                                                            $sayacgelen->kalibrasyon=0;
                                                        if($sayacadi->sayactip->tipadi=="QUANTOMETRE") //quntometreler beyannameye eklenmiyor
                                                        {
                                                            $sayacgelen->beyanname = -1;
                                                        }else{
                                                            $sayacgelen->beyanname = -2; //kalibrasyon sonrası aktif olacak
                                                        }
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
                                                            try {
                                                                if($ozeldurum!=3 && $ozeldurum!=5 ){ //yedek parça beklemiyorsa ve müdahaleli sayaç değilse
                                                                    $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('kalibrasyondurum', 0)->first();
                                                                    if (!$grup) {
                                                                        $grup = new KalibrasyonGrup;
                                                                        $grup->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                        $grup->kayittarihi = date('Y-m-d H:i:s');
                                                                        $grup->kalibrasyondurum = 0;
                                                                        $grup->save();
                                                                    }
                                                                    $kalibrasyon = new Kalibrasyon;
                                                                    $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                    $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                    $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                    $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                    $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                    $kalibrasyon->kalibrasyonsayisi = 1;
                                                                    $kalibrasyon->save();
                                                                    $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                                    $servistakip->save();
                                                                    $grup->adet += 1;
                                                                    $grup->save();
                                                                    BackendController::HatirlatmaEkle(8, $netsiscari->id, $sayacgelen->servis_id, 1);
                                                                }
                                                            } catch (Exception $e) {
                                                                DB::rollBack();
                                                                Log::error($e);
                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıt Hatası', 'text' => 'Kalibrasyona Gönderilen Sayaç Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                            }
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
                                            $arizakayit->ilkmekanik = $ilkmekanik;
                                            $arizakayit->aksesuar = $aksesuarlist;
                                            $arizakayit->baglanticap=$baglanticap;
                                            $arizakayit->pmax=$pmax;
                                            $arizakayit->qmax=$qmax;
                                            $arizakayit->qmin=$qmin;
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
                                            $arizakayit->arizakayit_durum = $ozeldurum!=0 ? $ozeldurum : 1;
                                            $arizakayit->arizanot = $arizanot;
                                            $arizakayit->serinodegisim=$serinodegisim;
                                            $arizakayit->sertifika=$sertifika=="" ? null : $sertifika;
                                            $arizakayit->hf2=$hf2=="" ? null : $hf2;
                                            $arizakayit->hf3=$hf3=="" ? null : $hf3;
                                            $arizakayit->hf32=$hf32=="" ? null : $hf32;
                                            $arizakayit->rapordurum = -1;
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
                                                if($ozeldurum==3 || $ozeldurum==5)
                                                    $sayacgelen->kalibrasyon=1;
                                                else
                                                    $sayacgelen->kalibrasyon=0;
                                                if($sayacadi->sayactip->tipadi=="QUANTOMETRE")
                                                {
                                                    $sayacgelen->beyanname = -1;
                                                }else{
                                                    $sayacgelen->beyanname = -2; //kalibrasyon sonrası aktif olacak
                                                }
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
                                                    try {
                                                        if($ozeldurum!=3 && $ozeldurum!=5){ //yedek parça beklemiyorsa ve müdahaleli sayaç değilse
                                                            $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('kalibrasyondurum', 0)->first();
                                                            if (!$grup) {
                                                                $grup = new KalibrasyonGrup;
                                                                $grup->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                $grup->kayittarihi = date('Y-m-d H:i:s');
                                                                $grup->kalibrasyondurum = 0;
                                                                $grup->save();
                                                            }
                                                            $kalibrasyon = new Kalibrasyon;
                                                            $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                            $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                            $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                            $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                            $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                            $kalibrasyon->kalibrasyonsayisi = 1;
                                                            $kalibrasyon->save();
                                                            $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                            $servistakip->save();
                                                            $grup->adet += 1;
                                                            $grup->save();
                                                            BackendController::HatirlatmaEkle(8, $netsiscari->id, $sayacgelen->servis_id, 1);
                                                        }
                                                    } catch (Exception $e) {
                                                        DB::rollBack();
                                                        Log::error($e);
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıt Hatası', 'text' => 'Kalibrasyona Gönderilen Sayaç Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                    }
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
        $arizakayit->sayactip = SayacTip::find($arizakayit->sayacadi->sayactip_id);
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
        $arizakayit->aksesuarlar = explode(',',$arizakayit->aksesuar);
        $sayacadlari = SayacAdi::where('sayactur_id',$this->servisid)->get();
        $arizakodlari = ArizaKod::where('sayactur_id',$this->servisid)->orderBy('kullanim','desc')->orderBy('tanim','asc')->get();
        $yapilanlar = Yapilanlar::where('sayactur_id',$this->servisid)->get();
        $degisenler = Degisenler::where('sayactur_id',$this->servisid)->whereIn('id',$arizakayit->sayacparcalar)->get();
        $uyarilar = Uyarilar::where('sayactur_id',$this->servisid)->orderBy('kullanim','desc')->orderBy('tanim','asc')->get();
        $hurdanedenleri=HurdaNedeni::where('sayactur_id',$this->servisid)->get();
        $aksesuarlar = Aksesuar::where('sayactip_id', $arizakayit->sayacadi->sayactip_id)->get();
        $arizakayit->sayacgelen->hurdadurum=($arizakayit->sayacgelen->sayacdurum==3 ? 1 : 0);
        if($arizakayit->sayacgelen->hurdadurum){
            $hurda=Hurda::where('sayacgelen_id',$arizakayit->sayacgelen_id)->first();
            $arizakayit->sayacgelen->hurdaneden=$hurda ? $hurda->hurdanedeni_id : '';
        }
        return View::make($this->servisadi.'.arizakayitduzenle',array('arizakayit'=>$arizakayit,'arizakodlari'=>$arizakodlari,'yapilanlar'=>$yapilanlar,'degisenler'=>$degisenler,'uyarilar'=>$uyarilar,'sayacadlari'=>$sayacadlari,'hurdanedenleri'=>$hurdanedenleri,'aksesuarlar'=>$aksesuarlar))->with(array('title'=>$this->servisbilgi.' Mekanik Arıza Kayıdı Düzenleme Ekranı'));
    }

    public function postArizakayitduzenle($id) {
        try {
            $rules = ['cariadi'=>'required','istek'=>'required','uretimyer'=>'required','serino'=>'required',
            'uretim'=>'required','sayacadi'=>'required','garanti'=>'required','ilkmekanik'=>'required',
            'baglanticap' => 'required','pmax' => 'required','qmax' => 'required','qmin' => 'required',
            'arizalar'=>'required','yapilanlar'=>'required','degisenler'=>'required','uyarilar'=>'required'];
            $validate = Validator::make(Input::all(),$rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $arizakayit = ArizaKayit::find($id);
            $bilgi=$arizakayit;
            $ilkmekanik=str_replace(',','.',Input::get('ilkmekanik'));
            $baglanticap = Input::get('baglanticap');
            $pmax = Input::get('pmax');
            $qmax = Input::get('qmax');
            $qmin = Input::get('qmin');
            $aksesuarlist = "";
            if (Input::has('aksesuar')) {
                $aksesuarlar = Input::get('aksesuar');
                foreach ($aksesuarlar as $aksesuar) {
                    $aksesuarlist .= ($aksesuarlist == "" ? "" : ",") . $aksesuar;
                }
                if($aksesuarlist=="") $aksesuarlist=null;
            }else{
                $aksesuarlist=null;
            }
            $musteribilgi=Input::get('musteribilgi');
            $arizaaciklama=Input::get('arizaaciklama');
            $uretimyerid = Input::get('uretimyer');
            $uretimyer=UretimYer::find($uretimyerid);
            $sayacgelenid=Input::get('sayacgelenid');
            $sayacadiid=Input::get('sayacadi');
            $sayaccapid=1;
            $yeniserino=Input::get('yeniserino');
            $sertifika = Input::get('sertifika');
            $hf2 = Input::get('hf2');
            $hf3 = Input::get('hf3');
            $hf32 = Input::get('hf32');
            if(Input::has('ozeldurum')) // 0 normal 3 yedek parça bekliyor 4 şikayetli muayene 5 müdahaleli sayaç 6 yeni sayaç verildi 7 geri iade 8 geri iade kalibrasyonsuz
                $ozeldurum=Input::get('sayacdurum');
            else
                $ozeldurum=0;
            $arizanot=Input::get('arizanot');
            $garanti= $ozeldurum==6 ? 1 : Input::get('garanti');
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
            $depogelen=DepoGelen::find($sayacgelen->depogelen_id);
            if($depogelen->periyodik)
                $periyodik=1;
            else
                $periyodik=0;
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
                if ($hurdadurum == 1 || $ozeldurum==6 || $ozeldurum==8 ) //hurdaya ayrıldıysa ya da yeni sayaç verildiyse ay da kalibrasyonsuz geri iade
                {
                    $sayacdegisen = SayacDegisen::find($arizakayit->sayacdegisen_id);
                    $eskidegisenler = $sayacdegisen->degisenler;
                    if($eskidurum!=2 && $eskidurum!=6 && $eskidurum!=8) //arızakayıttan hurdaya ayrıldıysa
                    {
                        if($sayacgelen->fiyatlandirma==1){ //arıza kayıtı ücretlendirildiyse silinmeyecek hata verecek
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Seçilen Arıza Tespiti Fiyatlandırılmış. Öncelikle Fiyatlandırmanın Silinmesi Gerekiyor.', 'type' => 'error'));
                        }
                        //hatırlatmalar düzeltilecek fiyatlandırma bekleyen silinecek
                        if(!$sayacgelen->kalibrasyon){ //kalibrasyonu yapılmadıysa sil
                            try {
                                $kalibrasyon = Kalibrasyon::where('sayacgelen_id',$sayacgelen->id)->where('durum',0)->first();
                                if($kalibrasyon){
                                    $grup=KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
                                    if(!$grup->kalibrasyondurum){
                                        if($grup->adet>1) {
                                            $grup->adet -= 1;
                                            if ($grup->adet == $grup->biten)
                                                $grup->kalibrasyondurum = 1;
                                            $grup->save();
                                        }else{
                                            $kalibrasyon->kalibrasyongrup_id=NULL;
                                            $kalibrasyon->save();
                                            $grup->delete();
                                        }
                                    }
                                    if($kalibrasyon->kalibrasyonsayisi==2){
                                        $eskikalibrasyon=Kalibrasyon::where('sayacgelen_id',$sayacgelen->id)->where('kalibrasyonsayisi',1)->first();
                                        $servistakip->kalibrasyon_id = $eskikalibrasyon->id;
                                    }else{
                                        $servistakip->kalibrasyon_id = NULL;
                                    }
                                    $servistakip->save();
                                    $kalibrasyon->delete();
                                }
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıt Hatası', 'text' => 'Kalibrasyon Bilgisi Silinirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                            }
                            BackendController::HatirlatmaSil(8,$netsiscari->id,$sayacgelen->servis_id,1);
                        }
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
                        if($ozeldurum!=8)
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
                                        $arizakayit->ilkmekanik = $ilkmekanik;
                                        $arizakayit->aksesuar = $aksesuarlist;
                                        $arizakayit->baglanticap=$baglanticap;
                                        $arizakayit->pmax=$pmax;
                                        $arizakayit->qmax=$qmax;
                                        $arizakayit->qmin=$qmin;
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
                                        $arizakayit->arizakayit_durum = $ozeldurum==8 ? 8 : 2;
                                        $arizakayit->arizanot = $arizanot;
                                        $arizakayit->serinodegisim=$serinodegisim;
                                        $arizakayit->yenisayac= $ozeldurum==6 ? 1 : 0;
                                        $arizakayit->sertifika= $sertifika=="" ? null : $sertifika;
                                        $arizakayit->hf2=$hf2=="" ? null : $hf2;
                                        $arizakayit->hf3=$hf3=="" ? null : $hf3;
                                        $arizakayit->hf32=$hf32=="" ? null : $hf32;
                                        $arizakayit->rapordurum = 0;
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
                                            $sayacgelen->sayacdurum = $ozeldurum==8 ? 1 : 3; // 1 arıza kayıt 2 kalibrasyon 3 hurda
                                            $sayacgelen->teslimdurum = $ozeldurum==8 ? 2 : 3; //0 bekliyor 1 teslimat 2 geri gönderim 3 hurda 4 depolararasi 5 periyodik bakım
                                            $sayacgelen->sayacadi_id = $sayacadiid;
                                            $sayacgelen->sayaccap_id = $sayaccapid;
                                            $sayacgelen->beyanname = -2; // -2 hurda geri gönderim sayackayıt -1 dahil değil 0 bekleyen 1 biten
                                            $sayacgelen->kalibrasyon = 1;
                                            $sayacgelen->save();
                                            $sayac->sayacadi_id = $sayacadiid;
                                            $sayac->sayaccap_id = $sayaccapid;
                                            $sayac->sayactur_id = $sayacadi->sayactur_id;
                                            $sayac->uretimtarihi = $uretimtarih;
                                            $sayac->kullanici_id = Auth::user()->id;
                                            $sayac->hurdadurum = $ozeldurum!=8 ? 1 : $sayac->hurdadurum;
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
                                                if($ozeldurum!=8){
                                                    $hurdakayit = Hurda::where('sayacgelen_id', $sayacgelenid)->first();
                                                    if (!$hurdakayit) {
                                                        $hurdakayit = new Hurda;
                                                    }
                                                    $hurdakayit->servis_id = $sayacgelen->servis_id;
                                                    $hurdakayit->netsiscari_id = $sayacgelen->netsiscari_id;
                                                    $hurdakayit->sayac_id = $sayac->id;
                                                    $hurdakayit->hurdanedeni_id = 1;
                                                    $hurdakayit->hurdatarihi = date('Y-m-d H:i:s');
                                                    $hurdakayit->sayacgelen_id = $sayacgelen->id;
                                                    $hurdakayit->arizakayit_id = $arizakayit->id;
                                                    $hurdakayit->arizafiyat_id = $arizafiyat->id;
                                                    $hurdakayit->kullanici_id = Auth::user()->id;
                                                    $hurdakayit->save();
                                                    try {
                                                        $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                            ->where('depodurum', 0)->where('tipi', 3)->where('subegonderim', $subedurum)->where('periyodik',$periyodik)->first();
                                                        if ($eskidurum != 2 && $eskidurum != 6) //arızakayıttan hurdaya ayrıldıysa
                                                        {
                                                            if($eskidurum==8){
                                                                $eskidepoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                                    ->where('depodurum', 0)->where('tipi', 2)->where('subegonderim', $subedurum)->where('periyodik',$periyodik)->first();
                                                                if($eskidepoteslim){
                                                                    if($eskidepoteslim->sayacsayisi>1){
                                                                        $secilenlist=explode(',',$eskidepoteslim->secilenler);
                                                                        $silinecek=array($sayacgelenid);
                                                                        $eskidepoteslim->secilenler=BackendController::getListeFark($secilenlist,$silinecek);
                                                                        $eskidepoteslim->sayacsayisi=count(explode(',',$eskidepoteslim->secilenler));
                                                                        $eskidepoteslim->save();
                                                                    }else{
                                                                        $eskidepoteslim->delete();
                                                                    }
                                                                }
                                                            }
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
                                                                $depoteslim->periyodik=$periyodik;
                                                                $depoteslim->subegonderim = $subedurum;
                                                                $depoteslim->parabirimi_id = $uretimyer->parabirimi_id;
                                                            }
                                                            $depoteslim->save();
                                                            if($eskidurum==8)
                                                                BackendController::HatirlatmaSil(9,$netsiscari->id,$sayacgelen->servis_id,1);
                                                        }
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
                                                            $hurdanedeni = HurdaNedeni::find(1);
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
                                                }else{
                                                    try {
                                                        $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                            ->where('depodurum', 0)->where('tipi', 2)->where('subegonderim', $subedurum)->where('periyodik',$periyodik)->first();
                                                        if ($eskidurum != 8) //eski hali geri gönderim değilse
                                                        {
                                                            if($eskidurum==2 || $eskidurum==6){ //hurda silinecek
                                                                $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelenid)->first();
                                                                $servistakip->hurda_id=NULL;
                                                                $servistakip->save();
                                                                $hurdakayit = Hurda::where('sayacgelen_id', $sayacgelenid)->first();
                                                                $hurdakayit->delete();
                                                                $eskidepoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                                    ->where('depodurum', 0)->where('tipi', 3)->where('subegonderim', $subedurum)->where('periyodik',$periyodik)->first();
                                                                if($eskidepoteslim){
                                                                    if($eskidepoteslim->sayacsayisi>1){
                                                                        $secilenlist=explode(',',$eskidepoteslim->secilenler);
                                                                        $silinecek=array($sayacgelenid);
                                                                        $eskidepoteslim->secilenler=BackendController::getListeFark($secilenlist,$silinecek);
                                                                        $eskidepoteslim->sayacsayisi=count(explode(',',$eskidepoteslim->secilenler));
                                                                        $eskidepoteslim->save();
                                                                    }else{
                                                                        $eskidepoteslim->delete();
                                                                    }
                                                                }
                                                            }
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
                                                                $depoteslim->tipi = 2;
                                                                $depoteslim->periyodik=$periyodik;
                                                                $depoteslim->subegonderim = $subedurum;
                                                                $depoteslim->parabirimi_id = $uretimyer->parabirimi_id;
                                                            }
                                                            $depoteslim->save();
                                                            if($eskidurum==2 || $eskidurum==6){
                                                                BackendController::HatirlatmaSil(9,$netsiscari->id,$sayacgelen->servis_id,1);
                                                                $hurdanedeni=HurdaNedeni::find($hurdaneden);
                                                                $hurdanedeni->kullanim-=($hurdanedeni->kullanim==0 ? 0 : 1);
                                                                $hurdanedeni->save();
                                                            }
                                                        }
                                                        try {
                                                            $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                            $servistakip->arizakayit_id = $arizakayit->id;
                                                            $servistakip->arizafiyat_id = $arizafiyat->id;
                                                            $servistakip->hurda_id = null;
                                                            $servistakip->hurdalamatarihi = NULL;
                                                            $servistakip->durum = 2;
                                                            $servistakip->arizakayittarihi = $arizakayit->arizakayittarihi;
                                                            $servistakip->sonislemtarihi = $arizakayit->arizakayittarihi;
                                                            $servistakip->save();
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
                    if($eskidurum==2 || $eskidurum==6 || $eskidurum==8) //hurdadan yada geri iadeden arıza kayıta değiştirildiyse
                    {
                        if($sayacgelen->depoteslim==1){ //depoya teslim edildiyse uyarı verecek
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Seçilen Sayaç Depoya Teslim Edilmiş. Arıza Kayıdı Tekrar oluşturulamaz.', 'type' => 'error'));
                        }
                        if($eskidurum==2 || $eskidurum==6){
                            $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                            $servistakip->hurda_id=NULL;
                            $servistakip->hurdalamatarihi = NULL;
                            $servistakip->save();
                            $hurda=Hurda::where('sayacgelen_id',$sayacgelenid)->first();
                            if($hurda){
                                $hurda->delete();
                            }
                            $depoteslim = DepoTeslim::where('servis_id',$sayacgelen->servis_id)->where('netsiscari_id',$sayacgelen->netsiscari_id)
                                ->where('depodurum',0)->where('tipi',3)->where('subegonderim',$subedurum)->where('periyodik',$periyodik)->first();
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
                        }else{
                            $depoteslim = DepoTeslim::where('servis_id',$sayacgelen->servis_id)->where('netsiscari_id',$sayacgelen->netsiscari_id)
                                ->where('depodurum',0)->where('tipi',2)->where('subegonderim',$subedurum)->where('periyodik',$periyodik)->first();
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
                            }
                            //hatırlatmalar düzeltilecek depoteslimden silinecek
                            BackendController::HatirlatmaSil(9,$netsiscari->id,$sayacgelen->servis_id,1);
                            BackendController::HatirlatmaEkle(4,$netsiscari->id,$sayacgelen->servis_id,1);
                        }
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
                                                    $arizakayit->ilkmekanik = $ilkmekanik;
                                                    $arizakayit->aksesuar = $aksesuarlist;
                                                    $arizakayit->baglanticap=$baglanticap;
                                                    $arizakayit->pmax=$pmax;
                                                    $arizakayit->qmax=$qmax;
                                                    $arizakayit->qmin=$qmin;
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
                                                    $arizakayit->arizakayit_durum = $ozeldurum!=0 ? $ozeldurum : 1;
                                                    $arizakayit->arizanot = $arizanot;
                                                    $arizakayit->serinodegisim=$serinodegisim;
                                                    $arizakayit->yenisayac = 0;
                                                    $arizakayit->sertifika=$sertifika=="" ? null : $sertifika;
                                                    $arizakayit->hf2=$hf2=="" ? null : $hf2;
                                                    $arizakayit->hf3=$hf3=="" ? null : $hf3;
                                                    $arizakayit->hf32=$hf32=="" ? null : $hf32;
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
                                                        if($sayacadi->sayactip->tipadi=="QUANTOMETRE")
                                                        {
                                                            $sayacgelen->beyanname = -1;
                                                        }else{
                                                            $sayacgelen->beyanname = -2; //kalibrasyon sonrası aktif olacak
                                                        }
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
                                                        try {
                                                            if($eskidurum==2 || $eskidurum==6 || $eskidurum==8) //hurdadan ya da geri iadeden arıza kayıta değiştirildiyse
                                                            {
                                                                if($ozeldurum!=3 && $ozeldurum!=5 ){ // parça beklemiyorsa ve müdahaleli değilse
                                                                    if($ozeldurum==7){ //geri iade durumunda kalibrasyonu tekrar istemesin
                                                                        $kalibrasyon = Kalibrasyon::where('sayacgelen_id',$sayacgelen->id)->get();
                                                                        if($kalibrasyon->count()==0){
                                                                            $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('kalibrasyondurum', 0)->first();
                                                                            if (!$grup) {
                                                                                $grup = new KalibrasyonGrup;
                                                                                $grup->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                                $grup->kayittarihi = date('Y-m-d H:i:s');
                                                                                $grup->kalibrasyondurum = 0;
                                                                                $grup->save();
                                                                            }
                                                                            $kalibrasyon = new Kalibrasyon;
                                                                            $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                            $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                            $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                            $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                            $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                            $kalibrasyon->kalibrasyonsayisi = 1;
                                                                            $kalibrasyon->save();
                                                                            $grup->adet += 1;
                                                                            $grup->save();
                                                                            BackendController::HatirlatmaEkle(8,$netsiscari->id,$sayacgelen->servis_id,1);
                                                                            $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                                            $servistakip->save();
                                                                            $arizakayit->rapordurum = -1;
                                                                            $arizakayit->save();
                                                                            $sayacgelen->kalibrasyon= 0;
                                                                            $sayacgelen->save();
                                                                        }
                                                                    }else{
                                                                        $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('kalibrasyondurum', 0)->first();
                                                                        if (!$grup) {
                                                                            $grup = new KalibrasyonGrup;
                                                                            $grup->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                            $grup->kayittarihi = date('Y-m-d H:i:s');
                                                                            $grup->kalibrasyondurum = 0;
                                                                            $grup->save();
                                                                        }
                                                                        $kalibrasyon = Kalibrasyon::where('sayacgelen_id',$sayacgelen->id)->where('durum',0)->first();
                                                                        if($kalibrasyon){
                                                                            if($kalibrasyon->kalibrasyonsayisi>1){
                                                                                $eskikalibrasyon=Kalibrasyon::where('sayacgelen_id',$sayacgelen->id)->where('kalibrasyonsayisi',1)->first();
                                                                                $servistakip->kalibrasyon_id=NULL;
                                                                                $servistakip->save();
                                                                                $eskigrup = KalibrasyonGrup::find($eskikalibrasyon->kalibrasyongrup_id);
                                                                                if(!$eskigrup->kalibrasyondurum){
                                                                                    if($grup->adet>1) {
                                                                                        $grup->adet -= 1;
                                                                                        if ($grup->adet == $grup->biten)
                                                                                            $grup->kalibrasyondurum = 1;
                                                                                        $grup->save();
                                                                                    }else{
                                                                                        $kalibrasyon->kalibrasyongrup_id=NULL;
                                                                                        $kalibrasyon->save();
                                                                                        $grup->delete();
                                                                                    }
                                                                                }
                                                                                $eskikalibrasyon->delete();
                                                                            }
                                                                            if($kalibrasyon->kalibrasyongrup_id!=$grup->id){
                                                                                $eskikalibrasyongrup = KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
                                                                                if(!$eskikalibrasyongrup->kalibrasyondurum){
                                                                                    if($eskikalibrasyongrup->adet>1) {
                                                                                        $eskikalibrasyongrup->adet -= 1;
                                                                                        if ($eskikalibrasyongrup->adet == $eskikalibrasyongrup->biten)
                                                                                            $eskikalibrasyongrup->kalibrasyondurum = 1;
                                                                                        $eskikalibrasyongrup->save();
                                                                                    }else{
                                                                                        $kalibrasyon->kalibrasyongrup_id=NULL;
                                                                                        $kalibrasyon->save();
                                                                                        $eskikalibrasyongrup->delete();
                                                                                    }
                                                                                }else{
                                                                                    if($eskikalibrasyongrup->adet>1) {
                                                                                        $eskikalibrasyongrup->adet -= 1;
                                                                                        $eskikalibrasyongrup->biten-= 1;
                                                                                        $eskikalibrasyongrup->save();
                                                                                    }else{
                                                                                        $kalibrasyon->kalibrasyongrup_id=NULL;
                                                                                        $kalibrasyon->save();
                                                                                        $eskikalibrasyongrup->delete();
                                                                                    }
                                                                                }
                                                                            }
                                                                            $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                            $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                            $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                            $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                            $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                            $kalibrasyon->kalibrasyonsayisi = 1;
                                                                            $kalibrasyon->save();
                                                                        }else{
                                                                            $kalibrasyon = new Kalibrasyon;
                                                                            $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                            $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                            $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                            $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                            $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                            $kalibrasyon->kalibrasyonsayisi = 1;
                                                                            $kalibrasyon->save();
                                                                            $grup->adet += 1;
                                                                            $grup->save();
                                                                            BackendController::HatirlatmaEkle(8,$netsiscari->id,$sayacgelen->servis_id,1);
                                                                        }
                                                                        $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                                        $servistakip->save();
                                                                        $arizakayit->rapordurum = -1;
                                                                        $arizakayit->save();
                                                                        $sayacgelen->kalibrasyon= 0;
                                                                        $sayacgelen->save();
                                                                    }
                                                                }
                                                            }else if($eskidurum==3 || $eskidurum==5) // ilk kayıtta parça bekliyorsa ya da müdahaleli
                                                            {
                                                                if($ozeldurum!=3 && $ozeldurum!=5){ // parça beklemiyorsa ve müdahaleli değilse
                                                                    if($ozeldurum==7){ //geri iade durumunda kalibrasyonu tekrar istemesin
                                                                        $kalibrasyon = Kalibrasyon::where('sayacgelen_id',$sayacgelen->id)->get();
                                                                        if($kalibrasyon->count()==0){
                                                                            $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('kalibrasyondurum', 0)->first();
                                                                            if (!$grup) {
                                                                                $grup = new KalibrasyonGrup;
                                                                                $grup->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                                $grup->kayittarihi = date('Y-m-d H:i:s');
                                                                                $grup->kalibrasyondurum = 0;
                                                                                $grup->save();
                                                                            }
                                                                            $kalibrasyon = new Kalibrasyon;
                                                                            $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                            $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                            $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                            $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                            $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                            $kalibrasyon->kalibrasyonsayisi = 1;
                                                                            $kalibrasyon->save();
                                                                            $grup->adet += 1;
                                                                            $grup->save();
                                                                            BackendController::HatirlatmaEkle(8,$netsiscari->id,$sayacgelen->servis_id,1);
                                                                            $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                                            $servistakip->save();
                                                                            $arizakayit->rapordurum = -1;
                                                                            $arizakayit->save();
                                                                            $sayacgelen->kalibrasyon= 0;
                                                                            $sayacgelen->save();
                                                                        }
                                                                    }else {
                                                                        $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('kalibrasyondurum', 0)->first();
                                                                        if (!$grup) {
                                                                            $grup = new KalibrasyonGrup;
                                                                            $grup->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                            $grup->kayittarihi = date('Y-m-d H:i:s');
                                                                            $grup->kalibrasyondurum = 0;
                                                                            $grup->save();
                                                                        }
                                                                        $kalibrasyon = Kalibrasyon::where('sayacgelen_id', $sayacgelen->id)->where('durum', 0)->first();
                                                                        if ($kalibrasyon) {
                                                                            if ($kalibrasyon->kalibrasyonsayisi > 1) {
                                                                                $eskikalibrasyon = Kalibrasyon::where('sayacgelen_id', $sayacgelen->id)->where('kalibrasyonsayisi', 1)->first();
                                                                                $servistakip->kalibrasyon_id = NULL;
                                                                                $servistakip->save();
                                                                                $eskigrup = KalibrasyonGrup::find($eskikalibrasyon->kalibrasyongrup_id);
                                                                                if (!$eskigrup->kalibrasyondurum) {
                                                                                    if ($grup->adet > 1) {
                                                                                        $grup->adet -= 1;
                                                                                        if ($grup->adet == $grup->biten)
                                                                                            $grup->kalibrasyondurum = 1;
                                                                                        $grup->save();
                                                                                    } else {
                                                                                        $kalibrasyon->kalibrasyongrup_id = NULL;
                                                                                        $kalibrasyon->save();
                                                                                        $grup->delete();
                                                                                    }
                                                                                }
                                                                                $eskikalibrasyon->delete();
                                                                            }
                                                                            if ($kalibrasyon->kalibrasyongrup_id != $grup->id) {
                                                                                $eskikalibrasyongrup = KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
                                                                                if (!$eskikalibrasyongrup->kalibrasyondurum) {
                                                                                    if ($eskikalibrasyongrup->adet > 1) {
                                                                                        $eskikalibrasyongrup->adet -= 1;
                                                                                        if ($eskikalibrasyongrup->adet == $eskikalibrasyongrup->biten)
                                                                                            $eskikalibrasyongrup->kalibrasyondurum = 1;
                                                                                        $eskikalibrasyongrup->save();
                                                                                    } else {
                                                                                        $kalibrasyon->kalibrasyongrup_id = NULL;
                                                                                        $kalibrasyon->save();
                                                                                        $eskikalibrasyongrup->delete();
                                                                                    }
                                                                                } else {
                                                                                    if ($eskikalibrasyongrup->adet > 1) {
                                                                                        $eskikalibrasyongrup->adet -= 1;
                                                                                        $eskikalibrasyongrup->biten -= 1;
                                                                                        $eskikalibrasyongrup->save();
                                                                                    } else {
                                                                                        $kalibrasyon->kalibrasyongrup_id = NULL;
                                                                                        $kalibrasyon->save();
                                                                                        $eskikalibrasyongrup->delete();
                                                                                    }
                                                                                }
                                                                            }
                                                                            $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                            $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                            $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                            $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                            $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                            $kalibrasyon->kalibrasyonsayisi = 1;
                                                                            $kalibrasyon->save();
                                                                        } else {
                                                                            $kalibrasyon = new Kalibrasyon;
                                                                            $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                            $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                            $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                            $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                            $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                            $kalibrasyon->kalibrasyonsayisi = 1;
                                                                            $kalibrasyon->save();
                                                                            $grup->adet += 1;
                                                                            $grup->save();
                                                                            BackendController::HatirlatmaEkle(8, $netsiscari->id, $sayacgelen->servis_id, 1);
                                                                        }
                                                                        $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                                        $servistakip->save();
                                                                        $arizakayit->rapordurum = -1;
                                                                        $arizakayit->save();
                                                                        $sayacgelen->kalibrasyon = 0;
                                                                        $sayacgelen->save();
                                                                    }
                                                                }
                                                            }else{ //normal arıza kayıt
                                                                if($ozeldurum!=3 && $ozeldurum!=5){
                                                                    if($ozeldurum==7){ //geri iade durumunda kalibrasyonu tekrar istemesin
                                                                        $kalibrasyon = Kalibrasyon::where('sayacgelen_id',$sayacgelen->id)->get();
                                                                        if($kalibrasyon->count()==0){
                                                                            $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('kalibrasyondurum', 0)->first();
                                                                            if (!$grup) {
                                                                                $grup = new KalibrasyonGrup;
                                                                                $grup->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                                $grup->kayittarihi = date('Y-m-d H:i:s');
                                                                                $grup->kalibrasyondurum = 0;
                                                                                $grup->save();
                                                                            }
                                                                            $kalibrasyon = new Kalibrasyon;
                                                                            $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                            $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                            $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                            $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                            $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                            $kalibrasyon->kalibrasyonsayisi = 1;
                                                                            $kalibrasyon->save();
                                                                            $grup->adet += 1;
                                                                            $grup->save();
                                                                            BackendController::HatirlatmaEkle(8,$netsiscari->id,$sayacgelen->servis_id,1);
                                                                            $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                                            $servistakip->save();
                                                                            $arizakayit->rapordurum = -1;
                                                                            $arizakayit->save();
                                                                            $sayacgelen->kalibrasyon= 0;
                                                                            $sayacgelen->save();
                                                                        }
                                                                    }else {
                                                                        $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('kalibrasyondurum', 0)->first();
                                                                        $kalibrasyon = Kalibrasyon::where('sayacgelen_id', $sayacgelen->id)->where('durum', 0)->first();
                                                                        if ($kalibrasyon) {
                                                                            if ($kalibrasyon->kalibrasyongrup_id != $grup->id) {
                                                                                $eskikalibrasyongrup = KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
                                                                                if (!$eskikalibrasyongrup->kalibrasyondurum) {
                                                                                    if ($eskikalibrasyongrup->adet > 1) {
                                                                                        $eskikalibrasyongrup->adet -= 1;
                                                                                        if ($eskikalibrasyongrup->adet == $eskikalibrasyongrup->biten)
                                                                                            $eskikalibrasyongrup->kalibrasyondurum = 1;
                                                                                        $eskikalibrasyongrup->save();
                                                                                    } else {
                                                                                        $kalibrasyon->kalibrasyongrup_id = NULL;
                                                                                        $kalibrasyon->save();
                                                                                        $eskikalibrasyongrup->delete();
                                                                                    }
                                                                                } else {
                                                                                    if ($eskikalibrasyongrup->adet > 1) {
                                                                                        $eskikalibrasyongrup->adet -= 1;
                                                                                        $eskikalibrasyongrup->biten -= 1;
                                                                                        $eskikalibrasyongrup->save();
                                                                                    } else {
                                                                                        $kalibrasyon->kalibrasyongrup_id = NULL;
                                                                                        $kalibrasyon->save();
                                                                                        $eskikalibrasyongrup->delete();
                                                                                    }
                                                                                }
                                                                            }
                                                                            $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                            $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                            $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                            $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                            $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                            $kalibrasyon->save();
                                                                            $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                                            $servistakip->save();
                                                                            $arizakayit->rapordurum = -1;
                                                                            $arizakayit->save();
                                                                            $sayacgelen->kalibrasyon = 0;
                                                                            $sayacgelen->save();
                                                                        } else {
                                                                            $arizakayit->rapordurum = 0;
                                                                            $arizakayit->save();
                                                                            $sayacgelen->kalibrasyon = 1;
                                                                            $sayacgelen->save();
                                                                        }
                                                                    }
                                                                }else{ // kalibrasyon silinecek
                                                                    if(!$sayacgelen->kalibrasyon){ //kalibrasyonu yapılmadıysa sil
                                                                        try {
                                                                            $kalibrasyon = Kalibrasyon::where('sayacgelen_id',$sayacgelen->id)->where('durum',0)->first();
                                                                            if($kalibrasyon){
                                                                                $grup=KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
                                                                                if(!$grup->kalibrasyondurum){
                                                                                    if($grup->adet>1) {
                                                                                        $grup->adet -= 1;
                                                                                        if ($grup->adet == $grup->biten)
                                                                                            $grup->kalibrasyondurum = 1;
                                                                                        $grup->save();
                                                                                    }else{
                                                                                        $kalibrasyon->kalibrasyongrup_id=NULL;
                                                                                        $kalibrasyon->save();
                                                                                        $grup->delete();
                                                                                    }
                                                                                }
                                                                                $servistakip->kalibrasyon_id = NULL;
                                                                                $servistakip->save();
                                                                                $kalibrasyon->delete();
                                                                            }
                                                                        } catch (Exception $e) {
                                                                            DB::rollBack();
                                                                            Log::error($e);
                                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıt Hatası', 'text' => 'Kalibrasyon Bilgisi Silinirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                                        }
                                                                        BackendController::HatirlatmaSil(8,$netsiscari->id,$sayacgelen->servis_id,1);
                                                                    }
                                                                    $arizakayit->rapordurum = -1;
                                                                    $arizakayit->save();
                                                                    $sayacgelen->kalibrasyon= 1;
                                                                    $sayacgelen->save();
                                                                }
                                                            }
                                                        } catch (Exception $e) {
                                                            DB::rollBack();
                                                            Log::error($e);
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıt Hatası', 'text' => 'Kalibrasyona Gönderilen Sayaç Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
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
                                            $arizakayit->ilkmekanik = $ilkmekanik;
                                            $arizakayit->aksesuar = $aksesuarlist;
                                            $arizakayit->baglanticap=$baglanticap;
                                            $arizakayit->pmax=$pmax;
                                            $arizakayit->qmax=$qmax;
                                            $arizakayit->qmin=$qmin;
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
                                            $arizakayit->arizakayit_durum = $ozeldurum!=0 ? $ozeldurum : 1;
                                            $arizakayit->arizanot = $arizanot;
                                            $arizakayit->serinodegisim=$serinodegisim;
                                            $arizakayit->yenisayac = 0;
                                            $arizakayit->sertifika=$sertifika=="" ? null : $sertifika;
                                            $arizakayit->hf2=$hf2=="" ? null : $hf2;
                                            $arizakayit->hf3=$hf3=="" ? null : $hf3;
                                            $arizakayit->hf32=$hf32=="" ? null : $hf32;
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
                                                if($sayacadi->sayactip->tipadi=="QUANTOMETRE")
                                                {
                                                    $sayacgelen->beyanname = -1;
                                                }else{
                                                    $sayacgelen->beyanname = -2; //kalibrasyon sonrası aktif olacak
                                                }
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
                                                try {
                                                    if($eskidurum==2 || $eskidurum==6 || $eskidurum==8) //hurdadan ya da geri iadeden arıza kayıta değiştirildiyse
                                                    {
                                                        if($ozeldurum!=3 && $ozeldurum!=5 ){ // parça beklemiyorsa ve müdahaleli değilse
                                                            if($ozeldurum==7){ //geri iade durumunda kalibrasyonu tekrar istemesin
                                                                $kalibrasyon = Kalibrasyon::where('sayacgelen_id',$sayacgelen->id)->get();
                                                                if($kalibrasyon->count()==0){
                                                                    $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('kalibrasyondurum', 0)->first();
                                                                    if (!$grup) {
                                                                        $grup = new KalibrasyonGrup;
                                                                        $grup->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                        $grup->kayittarihi = date('Y-m-d H:i:s');
                                                                        $grup->kalibrasyondurum = 0;
                                                                        $grup->save();
                                                                    }
                                                                    $kalibrasyon = new Kalibrasyon;
                                                                    $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                    $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                    $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                    $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                    $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                    $kalibrasyon->kalibrasyonsayisi = 1;
                                                                    $kalibrasyon->save();
                                                                    $grup->adet += 1;
                                                                    $grup->save();
                                                                    BackendController::HatirlatmaEkle(8, $netsiscari->id, $sayacgelen->servis_id, 1);
                                                                    $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                                    $servistakip->save();
                                                                    $arizakayit->rapordurum = -1;
                                                                    $arizakayit->save();
                                                                    $sayacgelen->kalibrasyon = 0;
                                                                    $sayacgelen->save();
                                                                }
                                                            }else {
                                                                $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('kalibrasyondurum', 0)->first();
                                                                if (!$grup) {
                                                                    $grup = new KalibrasyonGrup;
                                                                    $grup->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                    $grup->kayittarihi = date('Y-m-d H:i:s');
                                                                    $grup->kalibrasyondurum = 0;
                                                                    $grup->save();
                                                                }
                                                                $kalibrasyon = Kalibrasyon::where('sayacgelen_id', $sayacgelen->id)->where('durum', 0)->first();
                                                                if ($kalibrasyon) {
                                                                    if ($kalibrasyon->kalibrasyonsayisi > 1) {
                                                                        $eskikalibrasyon = Kalibrasyon::where('sayacgelen_id', $sayacgelen->id)->where('kalibrasyonsayisi', 1)->first();
                                                                        $servistakip->kalibrasyon_id = NULL;
                                                                        $servistakip->save();
                                                                        $eskigrup = KalibrasyonGrup::find($eskikalibrasyon->kalibrasyongrup_id);
                                                                        if (!$eskigrup->kalibrasyondurum) {
                                                                            if ($grup->adet > 1) {
                                                                                $grup->adet -= 1;
                                                                                if ($grup->adet == $grup->biten)
                                                                                    $grup->kalibrasyondurum = 1;
                                                                                $grup->save();
                                                                            } else {
                                                                                $kalibrasyon->kalibrasyongrup_id = NULL;
                                                                                $kalibrasyon->save();
                                                                                $grup->delete();
                                                                            }
                                                                        }
                                                                        $eskikalibrasyon->delete();
                                                                    }
                                                                    if ($kalibrasyon->kalibrasyongrup_id != $grup->id) {
                                                                        $eskikalibrasyongrup = KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
                                                                        if (!$eskikalibrasyongrup->kalibrasyondurum) {
                                                                            if ($eskikalibrasyongrup->adet > 1) {
                                                                                $eskikalibrasyongrup->adet -= 1;
                                                                                if ($eskikalibrasyongrup->adet == $eskikalibrasyongrup->biten)
                                                                                    $eskikalibrasyongrup->kalibrasyondurum = 1;
                                                                                $eskikalibrasyongrup->save();
                                                                            } else {
                                                                                $kalibrasyon->kalibrasyongrup_id = NULL;
                                                                                $kalibrasyon->save();
                                                                                $eskikalibrasyongrup->delete();
                                                                            }
                                                                        } else {
                                                                            if ($eskikalibrasyongrup->adet > 1) {
                                                                                $eskikalibrasyongrup->adet -= 1;
                                                                                $eskikalibrasyongrup->biten -= 1;
                                                                                $eskikalibrasyongrup->save();
                                                                            } else {
                                                                                $kalibrasyon->kalibrasyongrup_id = NULL;
                                                                                $kalibrasyon->save();
                                                                                $eskikalibrasyongrup->delete();
                                                                            }
                                                                        }
                                                                    }
                                                                    $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                    $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                    $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                    $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                    $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                    $kalibrasyon->kalibrasyonsayisi = 1;
                                                                    $kalibrasyon->save();
                                                                } else {
                                                                    $kalibrasyon = new Kalibrasyon;
                                                                    $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                    $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                    $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                    $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                    $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                    $kalibrasyon->kalibrasyonsayisi = 1;
                                                                    $kalibrasyon->save();
                                                                    $grup->adet += 1;
                                                                    $grup->save();
                                                                    BackendController::HatirlatmaEkle(8, $netsiscari->id, $sayacgelen->servis_id, 1);
                                                                }
                                                                $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                                $servistakip->save();
                                                                $arizakayit->rapordurum = -1;
                                                                $arizakayit->save();
                                                                $sayacgelen->kalibrasyon = 0;
                                                                $sayacgelen->save();
                                                            }
                                                        }
                                                    }else if($eskidurum==3 || $eskidurum==5) // ilk kayıtta parça bekliyorsa ya da müdahaleli
                                                    {
                                                        if($ozeldurum!=3 && $ozeldurum!=5){ // parça beklemiyorsa ve müdahaleli değilse
                                                            if($ozeldurum==7) { //geri iade durumunda kalibrasyonu tekrar istemesin
                                                                $kalibrasyon = Kalibrasyon::where('sayacgelen_id', $sayacgelen->id)->get();
                                                                if ($kalibrasyon->count() == 0) {
                                                                    $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('kalibrasyondurum', 0)->first();
                                                                    if (!$grup) {
                                                                        $grup = new KalibrasyonGrup;
                                                                        $grup->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                        $grup->kayittarihi = date('Y-m-d H:i:s');
                                                                        $grup->kalibrasyondurum = 0;
                                                                        $grup->save();
                                                                    }
                                                                    $kalibrasyon = new Kalibrasyon;
                                                                    $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                    $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                    $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                    $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                    $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                    $kalibrasyon->kalibrasyonsayisi = 1;
                                                                    $kalibrasyon->save();
                                                                    $grup->adet += 1;
                                                                    $grup->save();
                                                                    BackendController::HatirlatmaEkle(8, $netsiscari->id, $sayacgelen->servis_id, 1);
                                                                    $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                                    $servistakip->save();
                                                                    $arizakayit->rapordurum = -1;
                                                                    $arizakayit->save();
                                                                    $sayacgelen->kalibrasyon = 0;
                                                                    $sayacgelen->save();
                                                                }
                                                            }else {
                                                                $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('kalibrasyondurum', 0)->first();
                                                                if (!$grup) {
                                                                    $grup = new KalibrasyonGrup;
                                                                    $grup->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                    $grup->kayittarihi = date('Y-m-d H:i:s');
                                                                    $grup->kalibrasyondurum = 0;
                                                                    $grup->save();
                                                                }
                                                                $kalibrasyon = Kalibrasyon::where('sayacgelen_id', $sayacgelen->id)->where('durum', 0)->first();
                                                                if ($kalibrasyon) {
                                                                    if ($kalibrasyon->kalibrasyonsayisi > 1) {
                                                                        $eskikalibrasyon = Kalibrasyon::where('sayacgelen_id', $sayacgelen->id)->where('kalibrasyonsayisi', 1)->first();
                                                                        $servistakip->kalibrasyon_id = NULL;
                                                                        $servistakip->save();
                                                                        $eskigrup = KalibrasyonGrup::find($eskikalibrasyon->kalibrasyongrup_id);
                                                                        if (!$eskigrup->kalibrasyondurum) {
                                                                            if ($grup->adet > 1) {
                                                                                $grup->adet -= 1;
                                                                                if ($grup->adet == $grup->biten)
                                                                                    $grup->kalibrasyondurum = 1;
                                                                                $grup->save();
                                                                            } else {
                                                                                $kalibrasyon->kalibrasyongrup_id = NULL;
                                                                                $kalibrasyon->save();
                                                                                $grup->delete();
                                                                            }
                                                                        }
                                                                        $eskikalibrasyon->delete();
                                                                    }
                                                                    if ($kalibrasyon->kalibrasyongrup_id != $grup->id) {
                                                                        $eskikalibrasyongrup = KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
                                                                        if (!$eskikalibrasyongrup->kalibrasyondurum) {
                                                                            if ($eskikalibrasyongrup->adet > 1) {
                                                                                $eskikalibrasyongrup->adet -= 1;
                                                                                if ($eskikalibrasyongrup->adet == $eskikalibrasyongrup->biten)
                                                                                    $eskikalibrasyongrup->kalibrasyondurum = 1;
                                                                                $eskikalibrasyongrup->save();
                                                                            } else {
                                                                                $kalibrasyon->kalibrasyongrup_id = NULL;
                                                                                $kalibrasyon->save();
                                                                                $eskikalibrasyongrup->delete();
                                                                            }
                                                                        } else {
                                                                            if ($eskikalibrasyongrup->adet > 1) {
                                                                                $eskikalibrasyongrup->adet -= 1;
                                                                                $eskikalibrasyongrup->biten -= 1;
                                                                                $eskikalibrasyongrup->save();
                                                                            } else {
                                                                                $kalibrasyon->kalibrasyongrup_id = NULL;
                                                                                $kalibrasyon->save();
                                                                                $eskikalibrasyongrup->delete();
                                                                            }
                                                                        }
                                                                    }
                                                                    $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                    $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                    $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                    $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                    $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                    $kalibrasyon->kalibrasyonsayisi = 1;
                                                                    $kalibrasyon->save();
                                                                } else {
                                                                    $kalibrasyon = new Kalibrasyon;
                                                                    $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                    $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                    $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                    $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                    $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                    $kalibrasyon->kalibrasyonsayisi = 1;
                                                                    $kalibrasyon->save();
                                                                    $grup->adet += 1;
                                                                    $grup->save();
                                                                    BackendController::HatirlatmaEkle(8, $netsiscari->id, $sayacgelen->servis_id, 1);
                                                                }
                                                                $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                                $servistakip->save();
                                                                $arizakayit->rapordurum = -1;
                                                                $arizakayit->save();
                                                                $sayacgelen->kalibrasyon = 0;
                                                                $sayacgelen->save();
                                                            }
                                                        }
                                                    }else{ //normal arıza kayıt
                                                        if($ozeldurum!=3 && $ozeldurum!=5){
                                                            if($ozeldurum==7){ //geri iade durumunda kalibrasyonu tekrar istemesin
                                                                $kalibrasyon = Kalibrasyon::where('sayacgelen_id',$sayacgelen->id)->get();
                                                                if($kalibrasyon->count()==0){
                                                                    $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('kalibrasyondurum', 0)->first();
                                                                    if (!$grup) {
                                                                        $grup = new KalibrasyonGrup;
                                                                        $grup->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                        $grup->kayittarihi = date('Y-m-d H:i:s');
                                                                        $grup->kalibrasyondurum = 0;
                                                                        $grup->save();
                                                                    }
                                                                    $kalibrasyon = new Kalibrasyon;
                                                                    $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                    $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                    $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                    $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                    $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                    $kalibrasyon->kalibrasyonsayisi = 1;
                                                                    $kalibrasyon->save();
                                                                    $grup->adet += 1;
                                                                    $grup->save();
                                                                    BackendController::HatirlatmaEkle(8,$netsiscari->id,$sayacgelen->servis_id,1);
                                                                    $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                                    $servistakip->save();
                                                                    $arizakayit->rapordurum = -1;
                                                                    $arizakayit->save();
                                                                    $sayacgelen->kalibrasyon= 0;
                                                                    $sayacgelen->save();
                                                                }
                                                            }else {
                                                                $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('kalibrasyondurum', 0)->first();
                                                                $kalibrasyon = Kalibrasyon::where('sayacgelen_id', $sayacgelen->id)->where('durum', 0)->first();
                                                                if ($kalibrasyon) {
                                                                    if ($kalibrasyon->kalibrasyongrup_id != $grup->id) {
                                                                        $eskikalibrasyongrup = KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
                                                                        if (!$eskikalibrasyongrup->kalibrasyondurum) {
                                                                            if ($eskikalibrasyongrup->adet > 1) {
                                                                                $eskikalibrasyongrup->adet -= 1;
                                                                                if ($eskikalibrasyongrup->adet == $eskikalibrasyongrup->biten)
                                                                                    $eskikalibrasyongrup->kalibrasyondurum = 1;
                                                                                $eskikalibrasyongrup->save();
                                                                            } else {
                                                                                $kalibrasyon->kalibrasyongrup_id = NULL;
                                                                                $kalibrasyon->save();
                                                                                $eskikalibrasyongrup->delete();
                                                                            }
                                                                        } else {
                                                                            if ($eskikalibrasyongrup->adet > 1) {
                                                                                $eskikalibrasyongrup->adet -= 1;
                                                                                $eskikalibrasyongrup->biten -= 1;
                                                                                $eskikalibrasyongrup->save();
                                                                            } else {
                                                                                $kalibrasyon->kalibrasyongrup_id = NULL;
                                                                                $kalibrasyon->save();
                                                                                $eskikalibrasyongrup->delete();
                                                                            }
                                                                        }
                                                                    }
                                                                    $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                    $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                    $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                    $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                    $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                    $kalibrasyon->save();
                                                                    $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                                    $servistakip->save();
                                                                    $arizakayit->rapordurum = -1;
                                                                    $arizakayit->save();
                                                                    $sayacgelen->kalibrasyon = 0;
                                                                    $sayacgelen->save();
                                                                } else {
                                                                    $arizakayit->rapordurum = 0;
                                                                    $arizakayit->save();
                                                                    $sayacgelen->kalibrasyon = 1;
                                                                    $sayacgelen->save();
                                                                }
                                                            }
                                                        }else{ // kalibrasyon silinecek
                                                            if(!$sayacgelen->kalibrasyon){ //kalibrasyonu yapılmadıysa sil
                                                                try {
                                                                    $kalibrasyon = Kalibrasyon::where('sayacgelen_id',$sayacgelen->id)->where('durum',0)->first();
                                                                    if($kalibrasyon){
                                                                        $grup=KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
                                                                        if(!$grup->kalibrasyondurum){
                                                                            if($grup->adet>1) {
                                                                                $grup->adet -= 1;
                                                                                if ($grup->adet == $grup->biten)
                                                                                    $grup->kalibrasyondurum = 1;
                                                                                $grup->save();
                                                                            }else{
                                                                                $kalibrasyon->kalibrasyongrup_id=NULL;
                                                                                $kalibrasyon->save();
                                                                                $grup->delete();
                                                                            }
                                                                        }
                                                                        $servistakip->kalibrasyon_id = NULL;
                                                                        $servistakip->save();
                                                                        $kalibrasyon->delete();
                                                                    }
                                                                } catch (Exception $e) {
                                                                    DB::rollBack();
                                                                    Log::error($e);
                                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıt Hatası', 'text' => 'Kalibrasyon Bilgisi Silinirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                                }
                                                                BackendController::HatirlatmaSil(8,$netsiscari->id,$sayacgelen->servis_id,1);
                                                            }
                                                            $arizakayit->rapordurum = -1;
                                                            $arizakayit->save();
                                                            $sayacgelen->kalibrasyon= 1;
                                                            $sayacgelen->save();
                                                        }
                                                    }
                                                } catch (Exception $e) {
                                                    DB::rollBack();
                                                    Log::error($e);
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıt Hatası', 'text' => 'Kalibrasyona Gönderilen Sayaç Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
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
        $arizakayit->sayactip = SayacTip::find($arizakayit->sayacadi->sayactip_id);
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
        $aksesuarlar = Aksesuar::whereIn('id', explode(',', $arizakayit->aksesuar))->get();
        $arizakayit->aksesuarlar = "";
        foreach ($aksesuarlar as $aksesuar) {
            $arizakayit->aksesuarlar .= ($arizakayit->aksesuarlar == "" ? "" : ",") . $aksesuar->adi;
        }
        $arizakodlari = ArizaKod::where('sayactur_id',$this->servisid)->get();
        $yapilanlar = Yapilanlar::where('sayactur_id',$this->servisid)->get();
        $degisenler = Degisenler::where('sayactur_id',$this->servisid)->whereIn('id',$arizakayit->sayacparcalar)->get();
        $uyarilar = Uyarilar::where('sayactur_id',$this->servisid)->get();
        return View::make($this->servisadi.'.arizakayitgoster',array('arizakayit'=>$arizakayit,'arizakodlari'=>$arizakodlari,'yapilanlar'=>$yapilanlar,'degisenler'=>$degisenler,'uyarilar'=>$uyarilar))->with(array('title'=>$this->servisbilgi.' Mekanik Arıza Kayıdı Bilgi Ekranı'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Bilgisi Getirilemedi.', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function postArizakayitgoster($id) {
        try {
            DB::beginTransaction();
            $arizakayit = ArizaKayit::find($id);
            $netsiscari = NetsisCari::find($arizakayit->netsiscari_id);
            $bilgi=$arizakayit;
            $arizaaciklama=Input::get('arizaaciklama');
            $sertifika = Input::get('sertifika');
            $hf2 = Input::get('hf2');
            $hf3 = Input::get('hf3');
            $hf32 = Input::get('hf32');
            $arizanot=Input::get('arizanot');
            if(Input::has('ozeldurum')) // 0 normal 3 yedek parça bekliyor 4 şikayetli muayene 5 müdahaleli sayaç 6 yeni sayaç verildi 7 geri iade 8 geri iade kalibrasyonsuz
                $ozeldurum=Input::get('sayacdurum');
            else
                $ozeldurum=0;
            try {
                $arizakayit->arizaaciklama = $arizaaciklama;
                $arizakayit->sertifika = $sertifika == "" ? null : $sertifika;
                $arizakayit->hf2 = $hf2 == "" ? null : $hf2;
                $arizakayit->hf3 = $hf3 == "" ? null : $hf3;
                $arizakayit->hf32 = $hf32 == "" ? null : $hf32;
                $arizakayit->arizanot = $arizanot;
                $arizakayit->arizakayit_durum = $ozeldurum!=0 ? $ozeldurum : 1;
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

    public function postTopluarizakayitekle()
    {
        $depogelenid = Input::get('depogelen');
        $garanti = Input::get('garanti');
        $baglanticap = Input::get('baglanticap');
        $pmax = Input::get('pmax');
        $qmax = Input::get('qmax');
        $qmin = Input::get('qmin');
        $musteribilgi = Input::get('musteribilgi');
        $arizaaciklama = Input::get('arizaaciklama');
        $arizalist = Input::get('arizalist');
        $yapilanlist = Input::get('yapilanlist');
        $degisenlist = Input::get('degisenlist');
        $uyarilist = Input::get('uyarilist');
        $arizalar = Input::get('arizalar');
        $yapilanlar = Input::get('yapilanlar');
        $degisenler = Input::get('degisenler');
        $uyarilar = Input::get('uyarilar');
        $sayacgelenler = Input::get('sayaclar');
        $aksesuarlist=null;
        if(Input::has('ozeldurum')) // 0 normal 3 yedek parça bekliyor 4 şikayetli muayene 5 müdahaleli sayaç 6 yeni sayaç verildi 7 geri iade 8 geri iade kalibrasyonsuz
            $ozeldurum=Input::get('sayacdurum');
        else
            $ozeldurum=0;
        $garanti = $ozeldurum==6 ? 1 : $garanti; // yeni sayaç verilirse garanti içinde olacak
        if (count($sayacgelenler) > 0) {
            DB::beginTransaction();
            $depogelen = DepoGelen::find($depogelenid);
            if ($depogelen) {
                $subedurum = 0;
                $biten = 0;
                $eklenenler = "";
                $netsiscari = NetsisCari::where('carikod', $depogelen->carikod)->first();
                $depogelen->kod = ServisStokKod::where('stokkodu', $depogelen->servisstokkodu)->first();
                foreach ($sayacgelenler as $sayacgelenid) {
                    $sayacgelen = SayacGelen::find($sayacgelenid);
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
                    try {
                        foreach ($uyarilar as $uyari) {
                            $uyarisonuc = Uyarilar::find($uyari);
                            $uyarisonuc->kullanim = 1;
                            $uyarisonuc->save();
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Uyarı-Sonuç Hatası', 'text' => 'Uyarı-Sonuç Güncellenemedi.', 'type' => 'error'));
                    }
                    if ($sayacgelen) {
                        $biten++;
                        $arizakayit = ArizaKayit::where('sayacgelen_id', $sayacgelen->id)->first();
                        if ($arizakayit) {
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıt Hatası', 'text' => 'Aynı Sayacı 2.Kez kaydetmeye Çalıştınız.', 'type' => 'warning'));
                        }
                        $sayac = Sayac::where('serino', $sayacgelen->serino)->where('sayactur_id', $this->servisid)->where('uretimyer_id', $sayacgelen->uretimyer_id)->first();
                        $uretimyer = UretimYer::find($sayacgelen->uretimyer_id);
                        $eklenenler .= ($eklenenler == "" ? "" : ",") . $sayacgelen->serino;
                        if ($ozeldurum == 6 || $ozeldurum == 8) //hurdaya ayrıldıysa ya da yeni sayaç verilme durumu varsa yada kalibrasyonsuz geri iade ise
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
                                                $arizakayit->ilkmekanik = $sayacgelen->endeks;
                                                $arizakayit->aksesuar = $aksesuarlist;
                                                $arizakayit->baglanticap = $baglanticap;
                                                $arizakayit->pmax = $pmax;
                                                $arizakayit->qmax = $qmax;
                                                $arizakayit->qmin = $qmin;
                                                $arizakayit->depogelen_id = $sayacgelen->depogelen_id;
                                                $arizakayit->netsiscari_id = $sayacgelen->netsiscari_id;
                                                $arizakayit->sayacgelen_id = $sayacgelen->id;
                                                $arizakayit->sayac_id = $sayac->id;
                                                $arizakayit->sayacadi_id = $sayacgelen->sayacadi_id;
                                                $arizakayit->sayaccap_id = $sayacgelen->sayaccap_id;
                                                $arizakayit->garanti = $garanti;
                                                $arizakayit->musteriaciklama = $musteribilgi;
                                                $arizakayit->arizaaciklama = $arizaaciklama;
                                                $arizakayit->sayacariza_id = $sayacariza->id;
                                                $arizakayit->sayacyapilan_id = $sayacyapilan->id;
                                                $arizakayit->sayacdegisen_id = $sayacdegisen->id;
                                                $arizakayit->sayacuyari_id = $sayacuyari->id;
                                                $arizakayit->arizakayit_kullanici_id = Auth::user()->id;
                                                $arizakayit->arizakayittarihi = date('Y-m-d H:i:s');
                                                $arizakayit->arizakayit_durum = $ozeldurum == 8 ? 8 : 2;
                                                $arizakayit->arizanot = "";
                                                $arizakayit->yenisayac = $ozeldurum == 6 ? 1 : 0;
                                                $arizakayit->rapordurum = 0;
                                                $arizakayit->resimler = "";
                                                $arizakayit->save();
                                                try {
                                                    $sayacgelen->arizakayit = 1;
                                                    $sayacgelen->sayacdurum = $ozeldurum == 8 ? 1 : 3; // 1 arıza kayıt 2 kalibrasyon 3 hurda
                                                    $sayacgelen->teslimdurum = $ozeldurum == 8 ? 2 : 3; //0 bekliyor 1 teslimat 2 geri gönderim 3 hurda 4 depolararasi 5 periyodik bakım
                                                    $sayacgelen->beyanname = -2; // -2 hurda geri gonderim sayackayit arizakayit -1 dahil değil 0 kalibrasyon 1 tamamlanan
                                                    $sayacgelen->kalibrasyon = 1;
                                                    $sayacgelen->save();
                                                    if (BackendController::getArizaKayitDurum($depogelen->id)) {
                                                        DepoGelen::find($depogelen->id)->update(['kayitdurum' => 1]);
                                                    }
                                                    if ($depogelen->periyodik)
                                                        $periyodik = 1;
                                                    else
                                                        $periyodik = 0;
                                                    $sayac->kullanici_id = Auth::user()->id;
                                                    $sayac->hurdadurum = $ozeldurum != 8 ? 1 : $sayac->hurdadurum;
                                                    $sayac->save();
                                                } catch (Exception $e) {
                                                    DB::rollBack();
                                                    Log::error($e);
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

                                                    $fiyatlar['total'] = 0;
                                                    $fiyatlar['total2'] = 0;
                                                    $fiyatlar['parabirimi2'] = null;

                                                    $kdv = ($fiyatlar['total'] * 18) / 100;
                                                    $kdv2 = ($fiyatlar['total2'] * 18) / 100;
                                                    $arizafiyat = new ArizaFiyat;
                                                    $arizafiyat->ariza_serino = $sayac->serino;
                                                    $arizafiyat->sayac_id = $sayac->id;
                                                    $arizafiyat->sayacadi_id = $sayacgelen->sayacadi_id;
                                                    $arizafiyat->sayaccap_id = $sayacgelen->sayaccap_id;;
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
                                                        if ($ozeldurum != 8) {
                                                            $hurdakayit = new Hurda;
                                                            $hurdakayit->servis_id = $sayacgelen->servis_id;
                                                            $hurdakayit->netsiscari_id = $sayacgelen->netsiscari_id;
                                                            $hurdakayit->sayac_id = $sayac->id;
                                                            $hurdakayit->hurdanedeni_id = 1;
                                                            $hurdakayit->hurdatarihi = date('Y-m-d H:i:s');
                                                            $hurdakayit->sayacgelen_id = $sayacgelen->id;
                                                            $hurdakayit->arizakayit_id = $arizakayit->id;
                                                            $hurdakayit->arizafiyat_id = $arizafiyat->id;
                                                            $hurdakayit->kullanici_id = Auth::user()->id;
                                                            $hurdakayit->save();
                                                            try {
                                                                $flag=0;
                                                                $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                                    ->where('depodurum', 0)->where('tipi', 3)->where('subegonderim', $subedurum)->where('periyodik', $periyodik)->first();
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
                                                                    $depoteslim->periyodik = $periyodik;
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
                                                                    $hurdanedeni = HurdaNedeni::find(1);
                                                                    $hurdanedeni->kullanim++;
                                                                    $hurdanedeni->save();
                                                                    if(!$flag){
                                                                        BackendController::HatirlatmaGuncelle(3, $netsiscari->id, $sayacgelen->servis_id, 1);
                                                                        BackendController::HatirlatmaEkle(9, $netsiscari->id, $sayacgelen->servis_id, 1);
                                                                        BackendController::BildirimEkle(3, $netsiscari->id, $sayacgelen->servis_id, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                                        BackendController::BildirimEkle(10, $netsiscari->id, $sayacgelen->servis_id, 1);
                                                                    }
                                                                } catch (Exception $e) {
                                                                    DB::rollBack();
                                                                    Log::error($e);
                                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Hurdaya Ayrılan Sayaç için Servis Takip ve Hatırlatma Bilgisi Güncellenemedi.', 'type' => 'error'));
                                                                }
                                                            } catch (Exception $e) {
                                                                DB::rollBack();
                                                                Log::error($e);
                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Hurdaya Ayrılan Sayaç için Depo Teslimi Kaydedilemedi.', 'type' => 'error'));
                                                            }
                                                        } else {
                                                            try {
                                                                $flag=0;
                                                                $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                                    ->where('depodurum', 0)->where('tipi', 2)->where('subegonderim', $subedurum)->where('periyodik', $periyodik)->first();
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
                                                                    $depoteslim->tipi = 2;
                                                                    $depoteslim->periyodik = $periyodik;
                                                                    $depoteslim->subegonderim = $subedurum;
                                                                    $depoteslim->parabirimi_id = $uretimyer->parabirimi_id;
                                                                }
                                                                $depoteslim->save();
                                                                try {
                                                                    $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                                    $servistakip->arizakayit_id = $arizakayit->id;
                                                                    $servistakip->arizafiyat_id = $arizafiyat->id;
                                                                    $servistakip->durum = 2;
                                                                    $servistakip->arizakayittarihi = $arizakayit->arizakayittarihi;
                                                                    $servistakip->sonislemtarihi = $arizakayit->arizakayittarihi;
                                                                    $servistakip->save();
                                                                    if(!$flag)  {
                                                                        BackendController::HatirlatmaGuncelle(3, $netsiscari->id, $sayacgelen->servis_id, 1);
                                                                        BackendController::HatirlatmaEkle(9, $netsiscari->id, $sayacgelen->servis_id, 1);
                                                                        BackendController::BildirimEkle(3, $netsiscari->id, $sayacgelen->servis_id, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                                    }
                                                                } catch (Exception $e) {
                                                                    DB::rollBack();
                                                                    Log::error($e);
                                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Hurdaya Ayrılan Sayaç için Servis Takip ve Hatırlatma Bilgisi Güncellenemedi.', 'type' => 'error'));
                                                                }
                                                            } catch (Exception $e) {
                                                                DB::rollBack();
                                                                Log::error($e);
                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Hurdaya Ayrılan Sayaç için Depo Teslimi Kaydedilemedi.', 'type' => 'error'));
                                                            }
                                                        }
                                                    } catch (Exception $e) {
                                                        DB::rollBack();
                                                        Log::error($e);
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Hurda Kayıdı Sisteme Eklenemedi', 'type' => 'error'));
                                                    }
                                                } catch (Exception $e) {
                                                    DB::rollBack();
                                                    Log::error($e);
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Arıza Fiyatlandırması Sisteme Kaydedilemedi.', 'type' => 'error'));
                                                }
                                            } catch (Exception $e) {
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
                            // normal arıza kayıt ise
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
                                                            $arizakayit->ilkmekanik = $sayacgelen->endeks;
                                                            $arizakayit->aksesuar = $aksesuarlist;
                                                            $arizakayit->baglanticap = $baglanticap;
                                                            $arizakayit->pmax = $pmax;
                                                            $arizakayit->qmax = $qmax;
                                                            $arizakayit->qmin = $qmin;
                                                            $arizakayit->depogelen_id = $sayacgelen->depogelen_id;
                                                            $arizakayit->netsiscari_id = $sayacgelen->netsiscari_id;
                                                            $arizakayit->sayacgelen_id = $sayacgelen->id;
                                                            $arizakayit->sayac_id = $sayac->id;
                                                            $arizakayit->sayacadi_id = $sayacgelen->sayacadi_id;
                                                            $arizakayit->sayaccap_id = $sayacgelen->sayaccap_id;
                                                            $arizakayit->garanti = $garanti;
                                                            $arizakayit->musteriaciklama = $musteribilgi;
                                                            $arizakayit->arizaaciklama = $arizaaciklama;
                                                            $arizakayit->sayacariza_id = $sayacariza->id;
                                                            $arizakayit->sayacyapilan_id = $sayacyapilan->id;
                                                            $arizakayit->sayacdegisen_id = $sayacdegisen->id;
                                                            $arizakayit->sayacuyari_id = $sayacuyari->id;
                                                            $arizakayit->arizakayit_kullanici_id = Auth::user()->id;
                                                            $arizakayit->arizakayittarihi = date('Y-m-d H:i:s');
                                                            $arizakayit->arizakayit_durum = $ozeldurum != 0 ? $ozeldurum : 1;
                                                            $arizakayit->arizanot = "";
                                                            $arizakayit->rapordurum = -1;
                                                            $arizakayit->resimler = "";
                                                            $arizakayit->save();
                                                            try {
                                                                $sayacgelen->arizakayit = 1;
                                                                if ($ozeldurum == 3 || $ozeldurum == 5)
                                                                    $sayacgelen->kalibrasyon = 1;
                                                                else
                                                                    $sayacgelen->kalibrasyon = 0;

                                                                $gelensayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                                                                if ($gelensayacadi->sayactip->tipadi == "QUANTOMETRE") //quntometreler beyannameye eklenmiyor
                                                                {
                                                                    $sayacgelen->beyanname = -1;
                                                                } else {
                                                                    $sayacgelen->beyanname = -2; //kalibrasyon sonrası aktif olacak
                                                                }
                                                                $sayacgelen->save();
                                                                if (BackendController::getArizaKayitDurum($depogelen->id)) {
                                                                    DepoGelen::find($depogelen->id)->update(['kayitdurum' => 1]);
                                                                }
                                                                $sayac->kullanici_id = Auth::user()->id;
                                                                $sayac->hurdadurum = 0;
                                                                $sayac->save();
                                                            } catch (Exception $e) {
                                                                DB::rollBack();
                                                                Log::error($e);
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
                                                                $arizafiyat->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                $arizafiyat->sayaccap_id = $sayacgelen->sayaccap_id;
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
                                                                    $servistakip->arizakayit_id = $arizakayit->id;
                                                                    $servistakip->arizafiyat_id = $arizafiyat->id;
                                                                    $servistakip->durum = 2;
                                                                    $servistakip->arizakayittarihi = $arizakayit->arizakayittarihi;
                                                                    $servistakip->sonislemtarihi = $arizakayit->arizakayittarihi;
                                                                    $servistakip->save();
                                                                    try {
                                                                        if ($ozeldurum != 3 && $ozeldurum != 5) { //yedek parça beklemiyorsa ve müdahaleli sayaç değilse
                                                                            $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('kalibrasyondurum', 0)->first();
                                                                            if (!$grup) {
                                                                                $grup = new KalibrasyonGrup;
                                                                                $grup->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                                $grup->kayittarihi = date('Y-m-d H:i:s');
                                                                                $grup->kalibrasyondurum = 0;
                                                                                $grup->save();
                                                                            }
                                                                            $kalibrasyon = new Kalibrasyon;
                                                                            $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                            $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                            $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                            $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                            $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                            $kalibrasyon->kalibrasyonsayisi = 1;
                                                                            $kalibrasyon->save();
                                                                            $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                                            $servistakip->save();
                                                                            $grup->adet += 1;
                                                                            $grup->save();
                                                                            BackendController::HatirlatmaEkle(8, $netsiscari->id, $sayacgelen->servis_id, 1);
                                                                        }
                                                                    } catch (Exception $e) {
                                                                        DB::rollBack();
                                                                        Log::error($e);
                                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıt Hatası', 'text' => 'Kalibrasyona Gönderilen Sayaç Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                                    }
                                                                    BackendController::HatirlatmaGuncelle(3, $netsiscari->id, $depogelen->servis_id, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                                    BackendController::HatirlatmaEkle(4, $netsiscari->id, $depogelen->servis_id, 1);
                                                                    BackendController::BildirimEkle(3, $netsiscari->id, $depogelen->servis_id, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                                    BackendController::getStokKullan($degisenlist, $sayacgelen->servis_id);
                                                                } catch (Exception $e) {
                                                                    DB::rollBack();
                                                                    Log::error($e);
                                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Servis Takip ve Hatırlatma Bilgisi Güncellenemedi', 'type' => 'error'));
                                                                }
                                                            } catch (Exception $e) {
                                                                DB::rollBack();
                                                                Log::error($e);
                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Arıza Fiyatlandırması Sisteme Kaydedilemedi.', 'type' => 'error'));
                                                            }
                                                        } catch (Exception $e) {
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
                                                    $arizakayit->ilkmekanik = $sayacgelen->endeks;
                                                    $arizakayit->aksesuar = $aksesuarlist;
                                                    $arizakayit->baglanticap = $baglanticap;
                                                    $arizakayit->pmax = $pmax;
                                                    $arizakayit->qmax = $qmax;
                                                    $arizakayit->qmin = $qmin;
                                                    $arizakayit->depogelen_id = $sayacgelen->depogelen_id;
                                                    $arizakayit->netsiscari_id = $sayacgelen->netsiscari_id;
                                                    $arizakayit->sayacgelen_id = $sayacgelen->id;
                                                    $arizakayit->sayac_id = $sayac->id;
                                                    $arizakayit->sayacadi_id = $sayacgelen->sayacadi_id;
                                                    $arizakayit->sayaccap_id = $sayacgelen->sayaccap_id;
                                                    $arizakayit->garanti = $garanti;
                                                    $arizakayit->musteriaciklama = $musteribilgi;
                                                    $arizakayit->arizaaciklama = $arizaaciklama;
                                                    $arizakayit->sayacariza_id = $sayacariza->id;
                                                    $arizakayit->sayacyapilan_id = $sayacyapilan->id;
                                                    $arizakayit->sayacdegisen_id = $sayacdegisen->id;
                                                    $arizakayit->sayacuyari_id = $sayacuyari->id;
                                                    $arizakayit->arizakayit_kullanici_id = Auth::user()->id;
                                                    $arizakayit->arizakayittarihi = date('Y-m-d H:i:s');
                                                    $arizakayit->arizakayit_durum = $ozeldurum != 0 ? $ozeldurum : 1;
                                                    $arizakayit->arizanot = "";
                                                    $arizakayit->rapordurum = -1;
                                                    $arizakayit->resimler = "";
                                                    $arizakayit->save();
                                                    try {
                                                        $sayacgelen->arizakayit = 1;
                                                        if ($ozeldurum == 3 || $ozeldurum == 5)
                                                            $sayacgelen->kalibrasyon = 1;
                                                        else
                                                            $sayacgelen->kalibrasyon = 0;
                                                        $gelensayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                                                        if ($gelensayacadi->sayactip->tipadi == "QUANTOMETRE") {
                                                            $sayacgelen->beyanname = -1;
                                                        } else {
                                                            $sayacgelen->beyanname = -2; //kalibrasyon sonrası aktif olacak
                                                        }
                                                        $sayacgelen->save();
                                                        $depogelen = DepoGelen::find($sayacgelen->depogelen_id);
                                                        if (BackendController::getArizaKayitDurum($depogelen->id)) {
                                                            DepoGelen::find($depogelen->id)->update(['kayitdurum' => 1]);
                                                        }
                                                        $sayac->kullanici_id = Auth::user()->id;
                                                        $sayac->hurdadurum = 0;
                                                        $sayac->save();
                                                    } catch (Exception $e) {
                                                        DB::rollBack();
                                                        Log::error($e);
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
                                                        $arizafiyat->sayacadi_id = $sayacgelen->sayacadi_id;
                                                        $arizafiyat->sayaccap_id = $sayacgelen->sayaccap_id;
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
                                                            $servistakip->arizakayit_id = $arizakayit->id;
                                                            $servistakip->arizafiyat_id = $arizafiyat->id;
                                                            $servistakip->durum = 2;
                                                            $servistakip->arizakayittarihi = $arizakayit->arizakayittarihi;
                                                            $servistakip->sonislemtarihi = $arizakayit->arizakayittarihi;
                                                            $servistakip->save();
                                                            try {
                                                                if ($ozeldurum != 3 && $ozeldurum != 5) { //yedek parça beklemiyorsa ve müdahaleli sayaç değilse
                                                                    $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('kalibrasyondurum', 0)->first();
                                                                    if (!$grup) {
                                                                        $grup = new KalibrasyonGrup;
                                                                        $grup->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                        $grup->kayittarihi = date('Y-m-d H:i:s');
                                                                        $grup->kalibrasyondurum = 0;
                                                                        $grup->save();
                                                                    }
                                                                    $kalibrasyon = new Kalibrasyon;
                                                                    $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                                    $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                    $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                                    $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                                    $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                                    $kalibrasyon->kalibrasyonsayisi = 1;
                                                                    $kalibrasyon->save();
                                                                    $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                                    $servistakip->save();
                                                                    $grup->adet += 1;
                                                                    $grup->save();
                                                                    BackendController::HatirlatmaEkle(8, $netsiscari->id, $sayacgelen->servis_id, 1);
                                                                }
                                                            } catch (Exception $e) {
                                                                DB::rollBack();
                                                                Log::error($e);
                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıt Hatası', 'text' => 'Kalibrasyona Gönderilen Sayaç Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                            }
                                                            BackendController::HatirlatmaGuncelle(3, $netsiscari->id, $depogelen->servis_id, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                            BackendController::HatirlatmaEkle(4, $netsiscari->id, $depogelen->servis_id, 1);
                                                            BackendController::BildirimEkle(3, $netsiscari->id, $depogelen->servis_id, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                        } catch (Exception $e) {
                                                            DB::rollBack();
                                                            Log::error($e);
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Servis Takip ve Hatırlatma Bilgisi Güncellenemedi', 'type' => 'error'));
                                                        }
                                                    } catch (Exception $e) {
                                                        DB::rollBack();
                                                        Log::error($e);
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Arıza Fiyatlandırması Sisteme Kaydedilemedi.', 'type' => 'error'));
                                                    }
                                                } catch (Exception $e) {
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
                    } else {
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Sayaç Arızası Zaten Kaydedilmiş.', 'type' => 'error'));
                    }
                }
                try {
                    DB::commit();
                    BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-pencil', $netsiscari->cariadi . ' Yerine Ait ' . $biten . ' Adet Arıza Kayıdı Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Arıza Kayıt Seri Numaraları:' . $eklenenler);
                    return Redirect::to($this->servisadi . '/arizakayit')->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapıldı', 'text' => 'Arıza Kayıdı Başarıyla Yapıldı', 'type' => 'success'));
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Hatırlatma Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                }
            } else {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Kaydedilemedi', 'text' => 'Depo Gelen Bilgisi Bulunamadı', 'type' => 'error'));
            }
        }else{
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Kaydedilemedi', 'text' => 'Seri Numaraları Seçilmemiş!', 'type' => 'error'));
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
            $uretimyer = Uretimyer::whereIn('id', $cariyerler)->where('mekanik',1)->get();
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

    public function getBeyanname() {
            return View::make($this->servisadi.'.beyanname')->with(array('title'=>$this->servisbilgi.' Servis Beyannameleri'));
    }

    public function postBeyannamelist() {
        $query = Beyanname::where('beyanname.servis_id',$this->servisid)
            ->select(array("beyanname.id","beyanname.no","beyanname.adet","beyanname.tarih","kullanici.adi_soyadi","beyanname.gtarih","kullanici.nadi_soyadi","beyanname.durum"))
            ->leftjoin("kullanici", "beyanname.kullanici_id", "=", "kullanici.id");
        return Datatables::of($query)
            ->editColumn('tarih', function ($model) {
                $date = new DateTime($model->tarih);
                return $date->format('d-m-Y');})
            ->addColumn('islemler',function ($model) {
                $root = BackendController::getRootDizin();
                if(!$model->durum)
                    return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/beyannameduzenle/".$model->id."' > Düzenle </a>
                        <a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                else
                    return "<a class='btn btn-sm btn-info' href='".$root."/".$this->servisadi."/beyannamegoster/".$model->id."' > Göster </a>";
            })
            ->make(true);
    }

    public function postBeyanname()
    {
        try {
            $beyannameid = Input::get('beyanname');
            $raportip = Input::get('rapor');
            $beyanname = Beyanname::find($beyannameid);
            if ($beyanname) {
                if ($raportip == "1") // Beyanname
                {
                    $raporadi = "Beyanname-" . Str::slug($beyanname->no);
                    $export = "xls";
                    $kriterler = array();
                    $kriterler['id'] = $beyannameid;

                    JasperPHP::process(public_path('reports/beyanname/beyannamegaz.jasper'), public_path('reports/outputs/beyanname/' . $raporadi),
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
                    readfile("reports/outputs/beyanname/" . $raporadi . "." . $export . "");
                    File::delete("reports/outputs/beyanname/" . $raporadi . "." . $export . "");
                } else { // Defter Çıktısı
                    $raporadi = "Muhur-Defteri-" . Str::slug($beyanname->no);
                    $export = "xls";
                    $kriterler = array();
                    $kriterler['id'] = $beyannameid;

                    JasperPHP::process(public_path('reports/beyannamedefter/beyannamedeftergaz.jasper'), public_path('reports/outputs/beyannamedefter/' . $raporadi),
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
                    readfile("reports/outputs/beyannamedefter/" . $raporadi . "." . $export . "");
                    File::delete("reports/outputs/beyannamedefter/" . $raporadi . "." . $export . "");
                }
                return Redirect::back()->with(array('mesaj' => 'false'));
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Rapor alınacak beyanname seçilmedi', 'type' => 'warning', 'title' => 'Rapor Hatası'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning', 'title' => 'Rapor Hatası'));
        }
    }

    public function getBeyannameekle() {
        try {
            $no = BeyannameNo::where('servis_id', $this->servisid)->where('yil', date('Y'))->where('type', 'T')->first();
            if ($no) {
                $kayitno = BackendController::BeyannameNo($no->no, 1);
            } else {
                $beyannameno = new BeyannameNo;
                $beyannameno->servis_id = $this->servisid;
                $beyannameno->yil = date('Y');
                $beyannameno->type = 'T';
                $beyannameno->no = "T-".date('Y')."/".(0);
                $beyannameno->save();
                $kayitno = BackendController::BeyannameNo($beyannameno->no,1);
            }
            $sayacgelenlist="";
            $sayacgelenler = SayacGelen::where('servis_id', $this->servisid)->where('beyanname',0)->get();
            foreach ($sayacgelenler as $sayacgelen){
                $sayacgelen->sayacdurum=BackendController::ServisTakipDurum($sayacgelen->id);
                $sayacgelenlist.=($sayacgelenlist=="" ? "" : ",").$sayacgelen->id;
            }
            return View::make($this->servisadi.'.beyannameekle', array('sayacgelenlist'=>$sayacgelenlist,'adet'=>$sayacgelenler->count(), 'beyannameno' => $kayitno,'tarih'=>date('d-m-Y')))->with(array('title' => 'Beyanname Ekle'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning', 'title' => 'Yeni Beyanname Bilgi Bulunamadı!'));
        }
    }

    public function postBeyannamekayitlist() {
        if(Input::has('beyanname_id')){
            $beyanname_id=Input::get('beyanname_id');
            $beyanname=Beyanname::find($beyanname_id);
            $sayacgelenlist=explode(',',$beyanname->secilenler);
            $query = SayacGelen::whereIn('sayacgelen.id',$sayacgelenlist)
                ->orWhere(function($query){ $query->where('sayacgelen.servis_id',$this->servisid)->where('sayacgelen.beyanname',0);})
                ->select(array("sayacgelen.id","sayacgelen.serino","sayacadi.sayacadi","uretimyer.yeradi","sayaccap.capadi"))
                ->leftjoin("sayacadi", "sayacgelen.sayacadi_id", "=", "sayacadi.id")
                ->leftjoin("sayaccap", "sayacgelen.sayaccap_id", "=", "sayaccap.id")
                ->leftjoin("uretimyer", "sayacgelen.uretimyer_id", "=", "uretimyer.id");
            return Datatables::of($query)
                ->editColumn('sayacadi', function ($model) {
                    return $model->sayacadi." ".$model->capadi;
                })
                ->editColumn('sayacdurum', function ($model) {
                    return BackendController::ServisTakipDurum($model->id);
                })
                ->make(true);
        }else if(Input::has('beyannamegoster_id')){
            $beyannamegoster_id=Input::get('beyannamegoster_id');
            $beyanname=Beyanname::find($beyannamegoster_id);
            $sayacgelenlist=explode(',',$beyanname->secilenler);
            $query = SayacGelen::whereIn('sayacgelen.id',$sayacgelenlist)
                ->select(array("sayacgelen.id","sayacgelen.serino","sayacadi.sayacadi","uretimyer.yeradi","sayaccap.capadi"))
                ->leftjoin("sayacadi", "sayacgelen.sayacadi_id", "=", "sayacadi.id")
                ->leftjoin("sayaccap", "sayacgelen.sayaccap_id", "=", "sayaccap.id")
                ->leftjoin("uretimyer", "sayacgelen.uretimyer_id", "=", "uretimyer.id");
            return Datatables::of($query)
                ->editColumn('sayacadi', function ($model) {
                    return $model->sayacadi." ".$model->capadi;
                })
                ->editColumn('sayacdurum', function ($model) {
                    return BackendController::ServisTakipDurum($model->id);
                })
                ->make(true);
        }else{
            $query = SayacGelen::where('sayacgelen.servis_id',$this->servisid)->where('sayacgelen.beyanname',0)
                ->select(array("sayacgelen.id","sayacgelen.serino","sayacadi.sayacadi","uretimyer.yeradi","sayaccap.capadi"))
                ->leftjoin("sayacadi", "sayacgelen.sayacadi_id", "=", "sayacadi.id")
                ->leftjoin("sayaccap", "sayacgelen.sayaccap_id", "=", "sayaccap.id")
                ->leftjoin("uretimyer", "sayacgelen.uretimyer_id", "=", "uretimyer.id");
            return Datatables::of($query)
                ->editColumn('sayacadi', function ($model) {
                    return $model->sayacadi." ".$model->capadi;
                })
                ->editColumn('sayacdurum', function ($model) {
                    return BackendController::ServisTakipDurum($model->id);
                })
                ->make(true);
        }
    }

    public function postBeyannameekle() {
        try {
            $rules = ['tarih' => 'required', 'beyannameno' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $tarih = Input::get('tarih');
            $beyannametarihi = date("Y-m-d", strtotime($tarih));
            $beyannameno = Input::get('beyannameno');
            $secilenler = Input::get('secilenler');
            $secilenlist=explode(',',$secilenler);
            $adet = Input::get('beyannameadet');
            $beyanname=Beyanname::where('no',$beyannameno)->where('servis_id',$this->servisid)->first();
            if($beyanname){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Bu Beyanname Numarası Sistemde Mevcut', 'text' => 'Beyanname Numarası Sistemde Zaten Kayıtlı.', 'type' => 'error'));
            }
            DB::beginTransaction();
            try {
                $beyanname = new Beyanname;
                $beyanname->servis_id = $this->servisid;
                $beyanname->no = $beyannameno;
                $beyanname->adet = $adet;
                $beyanname->secilenler = $secilenler;
                $beyanname->tarih = $beyannametarihi;
                $beyanname->durum = 0;
                $beyanname->kullanici_id = Auth::user()->id;
                $beyanname->save();

                BeyannameNo::where('servis_id', $this->servisid)->where('yil', date('Y'))->where('type', 'T')->update(['no'=>$beyannameno]);
                Beyanname::where('id','<>',$beyanname->id)->where('servis_id',$this->servisid)->update(['durum'=>1]);

                if(count($secilenlist)>2000){
                    $list=array_chunk($secilenlist,2000);
                    foreach ($list as $secilen){
                        $sayacgelenler=SayacGelen::whereIn('id',$secilen)->get();
                        foreach ($sayacgelenler as $sayacgelen){
                            $sayacgelen->beyanname=1;
                            $sayacgelen->save();
                        }
                    }
                }else{
                    $sayacgelenler=SayacGelen::whereIn('id',$secilenlist)->get();
                    foreach ($sayacgelenler as $sayacgelen){
                        $sayacgelen->beyanname=1;
                        $sayacgelen->save();
                    }
                }
                DB::commit();
                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-pencil', $beyanname->no . ' Numaralı Beyanname Kayıdı Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Beyanname Kayıt Numarası:' . $beyanname->id);
                return Redirect::to($this->servisadi.'/beyanname')->with(array('mesaj' => 'true', 'title' => 'Beyanname Kayıdı Yapıldı', 'text' => 'Beyanname Kayıdı Başarıyla Yapıldı.', 'type' => 'success'));
            } catch (Exception $e) {
                Log::error($e);
                return Redirect::back()->with(array('mesaj'=>'true','title'=>'Beyanname Kayıdı Yapılamadı','text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Beyanname Kayıdı Yapılamadı', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getBeyannamesil($id){
        try {
            DB::beginTransaction();
            $beyanname = Beyanname::find($id);
            if($beyanname){
                $bilgi = clone $beyanname;
                $beyanname->delete();
                $beyannameno=BackendController::BeyannameNo($beyanname->no,-1);
                $secilenlist = explode(',',$beyanname->secilenler);
                $sayacgelenler=SayacGelen::whereIn('id',$secilenlist)->get();
                foreach ($sayacgelenler as $sayacgelen){
                    $sayacgelen->beyanname=0;
                    $sayacgelen->save();
                }
                BeyannameNo::where('servis_id', $this->servisid)->where('yil', date('Y'))->where('type', 'T')->update(['no'=>$beyannameno]);
                Beyanname::where('no',$beyannameno)->where('servis_id',$this->servisid)->update(['durum'=>0]);
            }else{
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Beyanname Kayıdı Silinemedi', 'text' => 'Beyanname Kayıdı Zaten Silinmiş.', 'type' => 'error'));
            }
            BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-pencil', $bilgi->no. ' Numaralı Beyanname Kayıdı Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Beyanname Kayıt Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Beyanname Kayıdı Silindi', 'text' => 'Beyanname Kayıdı Başarıyla Silindi.', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Beyanname Kayıdı Silinemedi', 'text' => 'Beyanname Kayıdı Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getBeyannameduzenle($id) {
        try {
            $beyanname = Beyanname::find($id);
            return View::make($this->servisadi.'.beyannameduzenle', array('beyanname' => $beyanname))->with(array('title' => 'Beyanname Düzenle'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning', 'title' => 'Kayıtlı Beyanname Bilgisi Bulunamadı!'));
        }
    }

    public function postBeyannameduzenle($id) {
        try {
            $rules = ['tarih' => 'required', 'beyannameno' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $tarih = Input::get('tarih');
            $beyannametarihi = date("Y-m-d", strtotime($tarih));
            $beyannameno = Input::get('beyannameno');
            $secilenler = Input::get('secilenler');
            $secilenlist=explode(',',$secilenler);
            $adet = Input::get('beyannameadet');
            $beyanname=Beyanname::where('id','<>',$id)->where('no',$beyannameno)->where('servis_id',$this->servisid)->first();
            if($beyanname){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Bu Beyanname Numarası Sistemde Mevcut', 'text' => 'Beyanname Numarası Sistemde Zaten Kayıtlı.', 'type' => 'error'));
            }
            DB::beginTransaction();
            $beyanname = Beyanname::find($id);
            $bilgi = clone $beyanname;
            try {
                $beyanname->no = $beyannameno;
                $beyanname->adet = $adet;
                $beyanname->secilenler = $secilenler;
                $beyanname->tarih = $beyannametarihi;
                $beyanname->kullanici_id = Auth::user()->id;
                $beyanname->save();

                BeyannameNo::where('servis_id', $this->servisid)->where('yil', date('Y'))->where('type', 'T')->update(['no'=>$beyannameno]);
                Beyanname::where('id','<>',$beyanname->id)->where('servis_id',$this->servisid)->update(['durum'=>1]);

                $sayacgelenler=SayacGelen::whereIn('id',$secilenlist)->get();
                foreach ($sayacgelenler as $sayacgelen){
                    $sayacgelen->beyanname=1;
                    $sayacgelen->save();
                }
                DB::commit();
                BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-pencil', $bilgi->no . ' Numaralı Beyanname Kayıdı Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Beyanname Kayıt Numarası:' . $bilgi->id);
                return Redirect::to($this->servisadi.'/beyanname')->with(array('mesaj' => 'true', 'title' => 'Beyanname Kayıdı Güncellendi', 'text' => 'Beyanname Kayıdı Başarıyla Güncellendi.', 'type' => 'success'));
            } catch (Exception $e) {
                Log::error($e);
                return Redirect::back()->with(array('mesaj'=>'true','title'=>'Beyanname Kayıdı Güncellenemedi','text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Beyanname Kayıdı Güncellenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getBeyannamegoster($id) {
        try {
            $beyanname = Beyanname::find($id);
            return View::make($this->servisadi.'.beyannamegoster', array('beyanname' => $beyanname))->with(array('title' => 'Beyanname Kayıdı Bilgi Ekranı'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning', 'title' => 'Yeni Beyanname Bilgi Bulunamadı!'));
        }
    }

    public function getPeriyodik() {
        return View::make($this->servisadi.'.periyodik')->with(array('title'=>'Periyodik Bakım'));
    }

    public function postPeriyodiklist() {
        $query = Periyodik::select(array("periyodik.id","depogelen.fisno","netsiscari.cariadi","servisstokkod.stokadi","periyodik.adet","periyodik.biten",
            "periyodik.gdurum","depogelen.tarih","depogelen.gtarih","netsiscari.ncariadi","servisstokkod.nstokadi","periyodik.ndurum"))
            ->leftjoin("depogelen", "depogelen.id", "=", "periyodik.depogelen_id")
            ->leftjoin("netsiscari", "depogelen.carikod", "=", "netsiscari.carikod")
            ->leftjoin("servisstokkod", "depogelen.servisstokkodu", "=", "servisstokkod.stokkodu");
        return Datatables::of($query)
            ->editColumn('tarih', function ($model) {
                $date = new DateTime($model->tarih);
                return $date->format('d-m-Y');})
            ->addColumn('islemler',function ($model) {
                $root = BackendController::getRootDizin();
                if(!$model->durum)
                    return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/periyodikduzenle/".$model->id."' > Düzenle </a>
                        <a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                else
                    return "<a class='btn btn-sm btn-info' href='".$root."/".$this->servisadi."/periyodikgoster/".$model->id."' > Göster </a>";
            })
            ->make(true);
    }

    public function getPeriyodikekle() {
        try {
            $netsiscariler = NetsisCari::where('caridurum','A')->whereIn('caritipi',array('A','D'))
                ->whereIn('subekodu',array(-1,8))->whereNotIn('carikod',(function ($query){$query->select('carikod')->from('kodharichar')->where('subekodu', 8);}))
                ->orderBy('carikod','asc')->get();
            $sayacadlari = SayacAdi::where('sayactur_id',$this->servisid)->get();
            $servisstokkodlari = ServisStokKod::where('servisid',$this->servisid)->get();
            $yillar = array_combine(range(date("Y"), 1990), range(date("Y"), 1990));
            $uretimyerleri = UretimYer::where('mekanik', 1)->where('id', '<>', 0)->get();
            return View::make($this->servisadi.'.periyodikekle', array('sayacadlari'=>$sayacadlari,'servisstokkodlari'=>$servisstokkodlari, 'netsiscariler' => $netsiscariler,'uretimyerleri'=>$uretimyerleri,'yillar'=>$yillar))->with(array('title' => 'Periyodik Bakım Ekle'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning', 'title' => 'Periyodik Bakım Eklerken Hata Oluştu!'));
        }
    }

    public function postPeriyodikexcel(){
        try {
            If (Input::hasFile('file')) {
                $dosya = Input::file('file');
                $uzanti = $dosya->getClientOriginalExtension();
                $isim = Str::slug(str_random(5)) . '.' . $uzanti;
                $dosya->move('assets/temp/', $isim);
                $path ='assets/temp/'.$isim;
                $excelvalues = $errors = array();
                $data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                })->get();
                if(!empty($data) && $data->count()){
                    foreach ($data as $key => $value) {
                        if(count($value)>=6){
                            $marka=SayacMarka::where('marka',strtoupper($value->marka))->first();
                            if($marka){
                                $sayactip=SayacTip::where('sayacmarka_id',$marka->id)->where('kisaadi',$value->tip)->get(array('id'))->toArray();
                                if($sayactip){
                                    $sayacadi=SayacAdi::where('kisaadi',$value->model)->whereIn('sayactip_id',$sayactip)->where('sayactur_id',5)->first();
                                    if($sayacadi){
                                        $sayacparca = SayacParca::where('sayacadi_id', $sayacadi->id)->where('sayactur_id', 5)->first();
                                        if ($sayacparca) {
                                            $serviskod = $sayacparca->servisstokkod_id;
                                            $cellvalues = array("serino" => $value->sayac_no , "sayacadi" => $sayacadi->id, "serviskod" => $serviskod, "imalyili" => $value->imalat_yili);
                                            array_push($excelvalues,$cellvalues);
                                        }else{
                                            array_push($errors,array("error"=>$sayacadi->sayacadi." için Sayaç Parçaları Ekli Değil."));
                                        }
                                    }else{
                                        array_push($errors,array("error"=>$value->model." için ".$marka->marka." Markasında Sayaç Adı Ekli Değil."));
                                    }
                                }else{
                                    array_push($errors,array("error"=>$value->tip." için ".$marka->marka." Markasında Sayaç Tipi Ekli Değil."));
                                }
                            }else{
                                array_push($errors,array("error"=>$value->marka." Markası Sistemde Ekli Değil."));
                            }
                        }
                    }
                }
                File::delete($path);
                return Response::json(array('durum' => true, 'degerler' => $excelvalues,'adet'=>count($excelvalues),'hatalar' => $errors));
            }else {
                return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Excelden bilgi alınırken hata oluştu!', 'text' => 'Excel Dosyası Bulunamadı'));
            }
        } catch (Exception $e) {
            return Response::json(array('durum' => false, 'type'=>'error','title'=>'Excelden bilgi alınırken hata oluştu!','text'=>str_replace("'","\'",$e->getMessage())));
        }
    }

    public function postPeriyodikekle(){
        try {
            $rules = ['gelis' => 'required', 'uretimyerleri' => 'required', 'cariadi' => 'required', 'serino' => 'required', 'sayacadlari' => 'required', 'serviskodlari' => 'required'];
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
            $netsiscari = NetsisCari::find($netsiscari_id);
            $uretimtarih = Input::get('uretimtarih');
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
                $sayaclar = BackendController::DepoGirisGrupla($serinolar, $uretimyerleri, $serviskodlari, $sayacadlari, $sayaccaplari, $uretimtarih);
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
                        Fatuno::where(['SUBE_KODU' => 8, 'SERI' => 'Z', 'TIP' => '9'])->update(['NUMARA' => $belgeno]);
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
                                    $secilenler="";
                                    $kullanici_id=Auth::user()->id;
                                    foreach ($sayac as $girilecek) {
                                        $serino = $girilecek['serino'];
                                        $sayacadi = $girilecek['sayacadi'];
                                        $sayaccap = $girilecek['sayaccap'];
                                        $uretimyeri = $girilecek['uretimyeri'];
                                        $uretimtarihi = $girilecek['uretimyili'];
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
                                                $sayacgelen->kullanici_id = $kullanici_id;
                                                $sayacgelen->beyanname=-2;
                                                $sayacgelen->periyodik=1;
                                                $sayacgelen->save();
                                                $secilenler.=($secilenler=="" ? "" : ",").$sayacgelen->id;
                                                $sayac = Sayac::where('serino', $serino)->where('sayacadi_id', $sayacadi)->where('sayaccap_id', $sayaccap)->where('uretimyer_id', $uretimyeri)
                                                    ->where('sayactur_id', $this->servisid)->first();
                                                if (!$sayac) // mekanik sayaç yoksa sayaç listesine eklenecek
                                                {
                                                    $sayac = new Sayac;
                                                    $sayac->serino = $serino;
                                                    $sayac->cihazno = $serino;
                                                    $sayac->sayactur_id = $this->servisid;
                                                    $sayac->sayacadi_id = $sayacadi;
                                                    $sayac->sayaccap_id = $sayaccap;
                                                    $utarih = date('Y-m-d', mktime(0, 0, 0, 1, 1, $uretimtarihi));
                                                    $sayac->uretimtarihi = $utarih;
                                                    $sayac->uretimyer_id = $uretimyeri;
                                                    $sayac->save();
                                                }
                                                $biten++;
                                                $adet++;
                                                $eklenenler .= ($eklenenler == "" ? "" : ",") . $serino;
                                                $servistakip = new ServisTakip;
                                                $servistakip->serino = $sayacgelen->serino;
                                                $servistakip->sayacadi_id = $sayacgelen->sayacadi_id;
                                                $servistakip->depogelen_id = $sayacgelen->depogelen_id;
                                                $servistakip->netsiscari_id = $sayacgelen->netsiscari_id;
                                                $servistakip->sayacgelen_id = $sayacgelen->id;
                                                $servistakip->servis_id = $sayacgelen->servis_id;
                                                $servistakip->uretimyer_id = $sayacgelen->uretimyer_id;
                                                $servistakip->durum = 1;
                                                $servistakip->depotarih = $sayacgelen->depotarihi;
                                                $servistakip->sayacgiristarihi = $sayacgelen->eklenmetarihi;
                                                $servistakip->kullanici_id = $kullanici_id;
                                                $servistakip->sonislemtarihi = $sayacgelen->eklenmetarihi;
                                                $servistakip->save();
                                                $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('periyodik',1)->where('kalibrasyondurum', 0)->first();
                                                if (!$grup) {
                                                    $grup = new KalibrasyonGrup;
                                                    $grup->periyodik=1;
                                                    $grup->netsiscari_id = $sayacgelen->netsiscari_id;
                                                    $grup->kayittarihi = date('Y-m-d H:i:s');
                                                    $grup->kalibrasyondurum = 0;
                                                    $grup->save();
                                                }
                                                $kalibrasyon = new Kalibrasyon;
                                                $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                $kalibrasyon->kalibrasyonsayisi = 1;
                                                $kalibrasyon->save();
                                                $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                $servistakip->save();
                                                $sayacgelen->sayacdurum = 2; // 1 arıza kayıt 2 kalibrasyon 3 hurda
                                                $sayacgelen->save();
                                                $grup->adet += 1;
                                                $grup->save();
                                            } catch (Exception $e) {
                                                DB::rollBack();
                                                Log::error($e);
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Servis Takip Kısmına Kayıt Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                            }
                                        }
                                    }
                                    if ($biten > 0) {
                                        try {
                                            BackendController::HatirlatmaGuncelle(2,$netsiscari_id,$servisid,$biten,$depogelen->id,$depogelen->servisstokkodu);
                                            BackendController::DepoDurumGuncelle($depogelen->id);
                                            BackendController::HatirlatmaEkle(8, $netsiscari_id, $servisid, $biten);
                                            BackendController::BildirimEkle(2, $netsiscari_id, $servisid, $biten, $depogelen->id,$depogelen->servisstokkodu);
                                            try {
                                                $periyodik = new Periyodik;
                                                $periyodik->depogelen_id = $depogelen->id;
                                                $periyodik->secilenler = $secilenler;
                                                $periyodik->adet = $biten;
                                                $periyodik->biten = 0;
                                                $periyodik->durum = 0;
                                                $periyodik->kullanici_id = $kullanici_id;
                                                $periyodik->save();
                                                DepoGelen::find($depogelen->id)->update(array('periyodik' => 1));
                                            } catch (Exception $e) {
                                                DB::rollBack();
                                                Log::error($e);
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Yapılamadı', 'text' => 'Periyodik Bilgisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                            }
                                        } catch (Exception $e) {
                                            DB::rollBack();
                                            Log::error($e);
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Yapılamadı', 'text' => 'Hatırlatma Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
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
                return Redirect::to($this->servisadi.'/periyodik')->with(array('mesaj' => 'true', 'title' => 'Periyodik Bakım Kayıdı Yapıldı', 'text' => 'Periyodik Bakım Kayıdı Başarıyla Yapıldı', 'type' => 'success'));
            }else{
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => 'Girilen Sayaçların Seri Numaraları yazılmamış', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Periyodik Bakım Kayıdı Yapılamadı', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getPeriyodiksil($id){
        try {
            $periyodik = Periyodik::find($id);
            if ($periyodik) {
                DB::beginTransaction();
                $bilgi = clone $periyodik;
                $depogelen=DepoGelen::find($periyodik->depogelen_id);
                if (!BackendController::getPeriyodikKayitDurum($depogelen->id)) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Kayıdı Silinemedi', 'text' => 'Kalibrasyon Kayıdı Yapılmış Sayaç Var!', 'type' => 'error'));
                }
                $dbname = $depogelen->db_name;
                $inckey = $depogelen->inckeyno;
                $fisno = $depogelen->fisno;
                $netsiscari=NetsisCari::where('carikod',$depogelen->carikod)->first();
                $kullanici = Kullanici::find(Auth::user()->id);
                $durum = 0;
                $diger = DepoGelen::where('fisno', $fisno)->where('db_name', $dbname)->get();
                if ($diger->count() > 1)
                    $durum = 1;
                $depogiris = BackendController::NetsisDepoGirisSil($inckey,$fisno,$durum);
                if ($depogiris['durum'] == '0') {
                    DB::rollBack();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Silinemedi', 'text' => $depogiris['text'], 'type' => 'error'));
                }
                $sayacgelenler = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                $servistakip = ServisTakip::where('depogelen_id', $depogelen->id)->get();
                $hatirlatmalar = Hatirlatma::where('depogelen_id', $depogelen->id)->get();
                BackendController::HatirlatmaSil(8,$netsiscari->id,$this->servisid,$depogelen->adet);
                $kalibrasyongrup= KalibrasyonGrup::where('netsiscari_id',$netsiscari->id)->where('periyodik',1)->where('kalibrasyondurum',0)->first();
                $kalibrasyonlar = Kalibrasyon::where('kalibrasyongrup_id',$kalibrasyongrup->id)->get();
                try {
                    foreach ($sayacgelenler as $sayacgelen) {
                        $sayacgelen->depogelen_id = NULL;
                        $sayacgelen->save();
                    }
                    foreach ($servistakip as $takip) {
                        $takip->depogelen_id = NULL;
                        $takip->kalibrasyon_id=NULL;
                        $takip->save();
                    }
                    foreach ($hatirlatmalar as $hatirlatma) {
                        $hatirlatma->depogelen_id = NULL;
                        $hatirlatma->save();
                    }
                    foreach ($kalibrasyonlar as $kalibrasyon) {
                        $kalibrasyon->delete();
                    }
                    $periyodik->delete();
                    $kalibrasyongrup->delete();
                    $depogelen->delete();
                    foreach ($servistakip as $takip) {
                        $takip->delete();
                    }
                    foreach ($sayacgelenler as $sayacgelen) {
                        $sayacgelen->delete();
                    }
                    foreach ($hatirlatmalar as $hatirlatma) {
                        $hatirlatma->delete();
                    }
                    BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-thumbs-o-up', $bilgi->id . ' Nolu Depo Girişi Silindi.', 'Güncelleme Yapan:' . $kullanici->adi_soyadi . ',Depo Giriş Numarası:' . $bilgi->id);
                    DB::commit();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Kayıdı Silindi', 'text' => 'Depo Giriş Kayıdı Başarıyla Silindi.', 'type' => 'success'));
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Kayıdı Silinemedi', 'text' => 'Depo Giriş Kayıdı Silinirken Sorun Oluştu.', 'type' => 'error'));
                }
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Kayıdı Silinemedi', 'text' => 'Depo Giriş Kayıdı Bulunamadı.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Kayıdı Silinemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getPeriyodikduzenle($id) {
        try {
            $periyodik=Periyodik::find($id);
            $depogelen=DepoGelen::find($periyodik->depogelen_id);
            $depogelenler=DepoGelen::where('fisno',$depogelen->fisno)->get(array('id'));
            $depogelenlist=$depogelenler->toArray();
            $sayacgelenler=SayacGelen::whereIn('depogelen_id',$depogelenlist)->get();
            $flag=0;
            foreach($sayacgelenler as $sayacgelen)
            {
                $sayacgelen->uretimyer=UretimYer::find($sayacgelen->uretimyer_id);
                $sayacgelen->stokkod=ServisStokKod::where('stokkodu',$sayacgelen->stokkodu)->first();
                $sayacgelen->sayacadi=SayacAdi::find($sayacgelen->sayacadi_id);
                $sayacgelen->sayac=Sayac::where('sayactur_id',$this->servisid)->where('serino',$sayacgelen->serino)->first();
                if($sayacgelen->arizakayit){
                    $flag=1;
                }
            }
            $depogelen->netsiscari=NetsisCari::where('carikod',$depogelen->carikod)->first();
            $sayacadlari = SayacAdi::where('sayactur_id',$this->servisid)->get();
            $servisstokkodlari = ServisStokKod::where('servisid',$this->servisid)->get();
            $yillar = array_combine(range(date("Y"), 1990), range(date("Y"), 1990));
            $uretimyerleri = UretimYer::where('mekanik', 1)->where('id', '<>', 0)->get();
            if($flag==0){
                $netsiscariler = NetsisCari::where('caridurum','A')->whereIn('caritipi',array('A','D'))
                    ->whereIn('subekodu',array(-1,8))->whereNotIn('carikod',(function ($query){$query->select('carikod')->from('kodharichar')->where('subekodu', 8);}))
                    ->orderBy('carikod','asc')->get();
            }else{
                $netsiscariler = NetsisCari::where('carikod',$depogelen->carikod)->get();
            }
            return View::make($this->servisadi.'.periyodikduzenle', array('periyodik'=>$periyodik,'depogelen'=>$depogelen,'depogelenler'=>$depogelenler,'sayacgelen'=>$sayacgelenler,'sayacadlari'=>$sayacadlari,'servisstokkodlari'=>$servisstokkodlari, 'netsiscariler' => $netsiscariler,'uretimyerleri'=>$uretimyerleri,'yillar'=>$yillar))->with(array('title' => 'Periyodik Bakım Ekle'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning', 'title' => 'Periyodik Bakım Düzenlerken Hata Oluştu!'));
        }
    }

    public function postPeriyodikduzenle($id){
        try {
            $rules = ['gelis' => 'required', 'uretimyerleri' => 'required', 'cariadi' => 'required', 'serino' => 'required', 'sayacadlari' => 'required', 'serviskodlari' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $periyodik=Periyodik::find($id);
            $depogelen=DepoGelen::find($periyodik->depogelen_id);
            $dbname=$depogelen->db_name;
            $tarih = Input::get('gelis');
            $gelistarih = date("Y-m-d", strtotime($tarih));
            $uretimyerleri = Input::get('uretimyerleri');
            $netsiscari_id = Input::get('cariadi');
            $serinolar = Input::get('serino');
            $serviskodlari = Input::get('serviskodlari');
            $sayacadlari = Input::get('sayacadlari');
            $sayaccaplari = Input::get('sayaccaplari');
            $netsiscari = NetsisCari::find($netsiscari_id);
            $belgeno=Input::get('belgeno');
            $uretimtarih = Input::get('uretimtarih');
            $subedurum = 0;
            if (count($serinolar) > 0) {
                for ($i = 0; $i < count($serinolar); $i++) {
                    $serino1 = $serinolar[$i];
                    $yeri = $uretimyerleri[$i];
                    if ($serino1 == "")
                        continue;
                    $servistakip = ServisTakip::where('serino', $serino1)->where('depogelen_id', $depogelen->id)->first();
                    if ($servistakip->arizakayit_id == null)
                        if (BackendController::SayacDurum($serino1,$yeri, $this->servisid, $subedurum, $servistakip->id)) {
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => $serino1 . ' Nolu Sayacın Depo Çıkışı Yapılmamış.Önce Sayaca Ait Diğer İşlemleri Bitiriniz.', 'type' => 'error'));
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
                $kullanici=Kullanici::find(Auth::user()->id);
                $sayaclar = BackendController::DepoGirisGrupla($serinolar, $uretimyerleri, $serviskodlari, $sayacadlari, $sayaccaplari, $uretimtarih);
                if (count($sayaclar) == 0) {
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Depo Sayaç Girişi Kaydedilemedi', 'text' => 'Girilen Sayaçların Seri Numaraları yazılmamış', 'type' => 'error'));
                }
                DB::beginTransaction();
                $depogiris = BackendController::NetsisDepoGirisDuzenle($dbname,$sayaclar,$gelistarih,$netsiscari_id,$belgeno);
                //Config::set('database.connections.sqlsrv2.database', 'MANAS'.date('Y'));
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
                        $depogelen = DepoGelen::where('db_name', $dbname)->where('inckeyno', $inckey)->first();
                        $now = time();
                        while ($now + 30 > time()) {
                            $depogelen = DepoGelen::where('db_name', $dbname)->where('inckeyno', $inckey)->first();
                            if ($depogelen)
                                break;
                        }
                        if ($depogelen) {
                            $servisid=$depogelen->servis_id;
                            $hatirlatmalar=Hatirlatma::where('depogelen_id',$depogelen->id)->get();
                            foreach($hatirlatmalar as $hatirlatma){
                                $hatirlatma->netsiscari_id=$netsiscari->id;
                                $hatirlatma->save();
                            }
                            $depogelen->kod = ServisStokKod::where('stokkodu', $depogelen->servisstokkodu)->first();
                            foreach ($sayaclar as $sayacgrup) {
                                if ($sayacgrup['kod'] == $depogelen->kod->id) {
                                    $biten = 0;
                                    $sayac = $sayacgrup['sayac']; //eklenecek sayaçlar
                                    $eskiler=SayacGelen::where('depogelen_id',$depogelen->id)->get();
                                    $eskisayaclar=SayacGelen::where('depogelen_id',$depogelen->id)->get();
                                    $eskisayi=$eskisayaclar->count();
                                    foreach($eskisayaclar as $eskisayac){
                                        $eskisayac->flag=0;
                                    }
                                    $yeniids=array();
                                    $allids=array();
                                    $secilenler="";
                                    foreach ($sayac as $girilecek) {
                                        $serino = $girilecek['serino'];
                                        $uretimyeri = $girilecek['uretimyeri'];
                                        $sayacadi = $girilecek['sayacadi'];
                                        $sayaccap = $girilecek['sayaccap'];
                                        $uretimtarihi = $girilecek['uretimyili'];
                                        if ($serino != "") {
                                            try {
                                                $flag = 0;
                                                for ($j = 0; $j < $eskiler->count(); $j++) {
                                                    $sayacgelen = $eskiler[$j];
                                                    $eskisayac = $eskisayaclar[$j];
                                                    if ($sayacgelen->serino == $serino && $eskisayac->flag == 0) {
                                                        try {
                                                            $flag = 1;
                                                            $eskisayac->flag = 1;
                                                            $sayacgelen->depogelen_id = $depogelen->id;
                                                            $sayacgelen->netsiscari_id = $netsiscari_id;
                                                            $sayacgelen->stokkodu = $depogelen->servisstokkodu;
                                                            $sayacgelen->serino = $serino;
                                                            $sayacgelen->depotarihi = $gelistarih;
                                                            $sayacgelen->sayacadi_id = $sayacadi;
                                                            $sayacgelen->sayaccap_id = $sayaccap;
                                                            $sayacgelen->uretimyer_id = $uretimyeri;
                                                            $sayacgelen->kullanici_id = Auth::user()->id;
                                                            $sayacgelen->save();
                                                            $biten++;
                                                            $adet++;
                                                            $eklenenler .= ($eklenenler == "" ? "" : ",") . $serino;
                                                            array_push($allids, $sayacgelen->id);
                                                            $secilenler.=($secilenler=="" ? "" : ",").$sayacgelen->id;
                                                            $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                            if ($servistakip) {
                                                                $servistakip->depogelen_id = $depogelen->id;
                                                                $servistakip->serino = $serino;
                                                                $servistakip->sayacadi_id = $sayacadi;
                                                                $servistakip->netsiscari_id = $netsiscari_id;
                                                                $servistakip->uretimyer_id = $uretimyeri;
                                                                $servistakip->save();
                                                            }
                                                            $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('periyodik',1)->where('kalibrasyondurum', 0)->first();
                                                            if (!$grup) {
                                                                $grup = new KalibrasyonGrup;
                                                                $grup->periyodik=1;
                                                                $grup->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                $grup->kayittarihi = date('Y-m-d H:i:s');
                                                                $grup->kalibrasyondurum = 0;
                                                                $grup->save();
                                                            }
                                                            $kalibrasyon =Kalibrasyon::where('sayacgelen_id',$sayacgelen->id)->first();
                                                            $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                            $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                            $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                            $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                            $kalibrasyon->kalibrasyonsayisi = 1;
                                                            $kalibrasyon->save();
                                                            $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                            $servistakip->save();
                                                            $sayacgelen->sayacdurum = 2; // 1 arıza kayıt 2 kalibrasyon 3 hurda
                                                            $sayacgelen->save();
                                                            break;
                                                        } catch (Exception $e) {
                                                            Log::error($e);
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Periyodik Bakım Kayıdı Güncellenemedi', 'text' => 'Sayaç Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                        }
                                                    } else {
                                                        continue;
                                                    }
                                                }
                                                if ($flag == 0) { //yeni eklemeyse
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
                                                    $sayacgelen->servis_id = $servisid;
                                                    $sayacgelen->kullanici_id = $kullanici->id;
                                                    $sayacgelen->beyanname=-2;
                                                    $sayacgelen->periyodik=1;
                                                    $sayacgelen->save();
                                                    array_push($yeniids, $sayacgelen->id);
                                                    array_push($allids, $sayacgelen->id);
                                                    $secilenler.=($secilenler=="" ? "" : ",").$sayacgelen->id;
                                                    $sayac = Sayac::where('serino', $serino)->where('sayacadi_id', $sayacadi)->where('sayaccap_id', $sayaccap)->where('uretimyer_id', $uretimyeri)
                                                        ->where('sayactur_id', $this->servisid)->first();
                                                    if (!$sayac) // mekanik sayaç yoksa sayaç listesine eklenecek
                                                    {
                                                        $sayac = new Sayac;
                                                        $sayac->serino = $serino;
                                                        $sayac->cihazno = $serino;
                                                        $sayac->sayactur_id = $this->servisid;
                                                        $sayac->sayacadi_id = $sayacadi;
                                                        $sayac->sayaccap_id = $sayaccap;
                                                        $utarih = date('Y-m-d', mktime(0, 0, 0, 1, 1, $uretimtarihi));
                                                        $sayac->uretimtarihi = $utarih;
                                                        $sayac->uretimyer_id = $uretimyeri;
                                                        $sayac->save();
                                                    }
                                                    $biten++;
                                                    $adet++;
                                                    $eklenenler .= ($eklenenler == "" ? "" : ",") . $serino;
                                                    $servistakip = new ServisTakip;
                                                    $servistakip->serino = $sayacgelen->serino;
                                                    $servistakip->sayacadi_id = $sayacgelen->sayacadi_id;
                                                    $servistakip->depogelen_id = $sayacgelen->depogelen_id;
                                                    $servistakip->netsiscari_id = $sayacgelen->netsiscari_id;
                                                    $servistakip->sayacgelen_id = $sayacgelen->id;
                                                    $servistakip->servis_id = $sayacgelen->servis_id;
                                                    $servistakip->uretimyer_id = $sayacgelen->uretimyer_id;
                                                    $servistakip->durum = 1;
                                                    $servistakip->depotarih = $sayacgelen->depotarihi;
                                                    $servistakip->sayacgiristarihi = $sayacgelen->eklenmetarihi;
                                                    $servistakip->kullanici_id = $kullanici->id;
                                                    $servistakip->sonislemtarihi = $sayacgelen->eklenmetarihi;
                                                    $servistakip->save();
                                                    $grup = KalibrasyonGrup::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('periyodik',1)->where('kalibrasyondurum', 0)->first();
                                                    if (!$grup) {
                                                        $grup = new KalibrasyonGrup;
                                                        $grup->periyodik=1;
                                                        $grup->netsiscari_id = $sayacgelen->netsiscari_id;
                                                        $grup->kayittarihi = date('Y-m-d H:i:s');
                                                        $grup->kalibrasyondurum = 0;
                                                        $grup->save();
                                                    }
                                                    $kalibrasyon = new Kalibrasyon;
                                                    $kalibrasyon->sayacgelen_id = $sayacgelen->id;
                                                    $kalibrasyon->sayacadi_id = $sayacgelen->sayacadi_id;
                                                    $kalibrasyon->imalyili = date('Y-m-d', strtotime($sayac->uretimtarihi));
                                                    $kalibrasyon->kalibrasyongrup_id = $grup->id;
                                                    $kalibrasyon->kalibrasyon_seri = $sayacgelen->serino;
                                                    $kalibrasyon->kalibrasyonsayisi = 1;
                                                    $kalibrasyon->save();
                                                    $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                                    $servistakip->save();
                                                    $sayacgelen->sayacdurum = 2; // 1 arıza kayıt 2 kalibrasyon 3 hurda
                                                    $sayacgelen->save();
                                                    $grup->adet += 1;
                                                    $grup->save();
                                                } catch (Exception $e) {
                                                    Log::error($e);
                                                    DB::rollBack();
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                }
                                              }

                                            } catch (Exception $e) {
                                                Log::error($e);
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                            }
                                        }
                                    }
                                    for ($j = 0; $j < $eskisayaclar->count(); $j++) {
                                        $sayacgelen = $eskisayaclar[$j];
                                        if ($sayacgelen->flag == 0) { // silinecek varsa
                                            try {
                                                $sayacgelen = SayacGelen::find($sayacgelen->id);
                                                $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                $kalibrasyon = Kalibrasyon::find($servistakip->kalibrasyon_id);
                                                BackendController::HatirlatmaSil(8,$netsiscari_id,$servisid,1);
                                                BackendController::BildirimGeriAl(2,$netsiscari_id,$servisid,1,$depogelen->id,$depogelen->servisstokkodu);
                                                $servistakip->delete();
                                                $kalibrasyon->delete();
                                                $sayacgelen->delete();
                                            } catch (Exception $e) {
                                                Log::error($e);
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Eski Sayaç Kayıdı Silinemedi', 'text' => 'Eski Sayaç Kayıdı Silinirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                            }
                                        }
                                    }
                                    try {
                                        if ($biten > 0 && $biten != $eskisayi) {
                                            $sayi = $biten - $eskisayi; //silinme durumu yukarda çözüldü ekleme varsa eklenecek
                                            if($sayi>0){ //ekleme olacak silme işlemi yukarda yapıldı
                                                BackendController::HatirlatmaEkle(8, $netsiscari_id, $servisid, $sayi);
                                                BackendController::BildirimEkle(2, $netsiscari_id, $servisid, $sayi, $depogelen->id, $depogelen->servisstokkodu);
                                            }
                                            try {
                                                $periyodik =Periyodik::where('depogelen_id',$depogelen->id)->first();
                                                $periyodik->secilenler = $secilenler;
                                                $periyodik->adet = $biten;
                                                $periyodik->durum = $sayi>0 ? 0 : $periyodik->durum;
                                                $periyodik->kullanici_id = $kullanici->id;
                                                $periyodik->save();
                                            } catch (Exception $e) {
                                                DB::rollBack();
                                                Log::error($e);
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Yapılamadı', 'text' => 'Periyodik Bilgisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                            }
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Yapılamadı', 'text' => 'Hatırlatma Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
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
                BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-thumbs-o-up', $netsiscari->cariadi . ' Yerine Ait ' . $adet . ' Adet Sayacın Sisteme Girişi Yapıldı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Seri Numaraları:' . $eklenenler);
                DB::commit();
                return Redirect::to($this->servisadi.'/periyodik')->with(array('mesaj' => 'true', 'title' => 'Periyodik Bakım Kayıdı Yapıldı', 'text' => 'Periyodik Bakım Kayıdı Başarıyla Yapıldı', 'type' => 'success'));
            }else{
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => 'Girilen Sayaçların Seri Numaraları yazılmamış', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            Input::flash();
            DB::rollBack();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Periyodik Bakım Kayıdı Yapılamadı', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getServisraporu(){
        $arizakayitlar=ArizaKayit::whereIn('rapordurum',array(0,1))->get();
        $yerler=array();
        $kalibrasyonyerler=array();
        foreach ($arizakayitlar as $arizakayit){
            $sayacgelen=SayacGelen::find($arizakayit->sayacgelen_id);
            if($arizakayit->rapordurum==0){
                array_push($yerler,$sayacgelen->uretimyer_id);
            }
            array_push($kalibrasyonyerler,$sayacgelen->uretimyer_id);
        }

        $uretimyerleri = UretimYer::where('mekanik', 1)->where('id', '<>', 0)->whereIn('id',$yerler)->get();
        $kalibrasyonyerleri = UretimYer::where('mekanik', 1)->where('id', '<>', 0)->whereIn('id',$kalibrasyonyerler)->get();
        return View::make($this->servisadi.'.servisraporu',array('uretimyerleri'=>$uretimyerleri,'kalibrasyonyerleri'=>$kalibrasyonyerleri))->with(array('title'=>$this->servisbilgi.' Servis - Servis Raporu Bekleyenler'));
    }

    public function postRaporlist() {
        $id=Input::get('remove_id');
        $uretimyerid=Input::get('uretimyer_id');
        if($id!="")
            $query = ArizaKayit::where('sayacgelen.servis_id',$this->servisid)->where('arizakayit.rapordurum',0)->where('arizakayit.id','<>',$id)
                ->select(array("arizakayit.id", "sayacgelen.serino", "sayacadi.sayacadi", "uretimyer.yeradi","arizakayit.gdurum", "kullanici.adi_soyadi",
                    "arizakayit.arizakayittarihi","servistakip.eskiserino","arizakayit.garizakayittarihi","sayacadi.nsayacadi","uretimyer.nyeradi",
                    "arizakayit.ndurum","kullanici.nadi_soyadi","sayacgelen.fiyatlandirma", "sayacgelen.depoteslim"))
                ->leftjoin("sayacgelen", "arizakayit.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("servistakip", "servistakip.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("uretimyer", "sayacgelen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("sayacadi", "arizakayit.sayacadi_id", "=", "sayacadi.id")
                ->leftjoin("kullanici", "arizakayit.arizakayit_kullanici_id", "=", "kullanici.id");
        else if($uretimyerid!="")
                $query = ArizaKayit::where('sayacgelen.servis_id',$this->servisid)->where('arizakayit.rapordurum',0)->where('uretimyer.id','<>',$uretimyerid)
                    ->select(array("arizakayit.id", "sayacgelen.serino", "sayacadi.sayacadi", "uretimyer.yeradi","arizakayit.gdurum", "kullanici.adi_soyadi",
                        "arizakayit.arizakayittarihi","servistakip.eskiserino","arizakayit.garizakayittarihi","sayacadi.nsayacadi","uretimyer.nyeradi",
                        "arizakayit.ndurum","kullanici.nadi_soyadi","sayacgelen.fiyatlandirma", "sayacgelen.depoteslim"))
                    ->leftjoin("sayacgelen", "arizakayit.sayacgelen_id", "=", "sayacgelen.id")
                    ->leftjoin("servistakip", "servistakip.sayacgelen_id", "=", "sayacgelen.id")
                    ->leftjoin("uretimyer", "sayacgelen.uretimyer_id", "=", "uretimyer.id")
                    ->leftjoin("sayacadi", "arizakayit.sayacadi_id", "=", "sayacadi.id")
                    ->leftjoin("kullanici", "arizakayit.arizakayit_kullanici_id", "=", "kullanici.id");
        else
            $query = ArizaKayit::where('sayacgelen.servis_id',$this->servisid)->where('arizakayit.rapordurum',0)
                ->select(array("arizakayit.id", "sayacgelen.serino", "sayacadi.sayacadi", "uretimyer.yeradi","arizakayit.gdurum", "kullanici.adi_soyadi",
                    "arizakayit.arizakayittarihi","servistakip.eskiserino","arizakayit.garizakayittarihi","sayacadi.nsayacadi","uretimyer.nyeradi",
                    "arizakayit.ndurum","kullanici.nadi_soyadi","sayacgelen.fiyatlandirma", "sayacgelen.depoteslim"))
                ->leftjoin("sayacgelen", "arizakayit.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("servistakip", "servistakip.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("uretimyer", "sayacgelen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("sayacadi", "arizakayit.sayacadi_id", "=", "sayacadi.id")
                ->leftjoin("kullanici", "arizakayit.arizakayit_kullanici_id", "=", "kullanici.id");
        return Datatables::of($query)
            ->editColumn('serino', function ($model) {
                return $model->serino.($model->eskiserino ? " (".$model->eskiserino.")" : "");
            })
            ->editColumn('arizakayittarihi', function ($model) {
                $date = new DateTime($model->arizakayittarihi);
                return $date->format('d-m-Y');})
            ->addColumn('islemler',function ($model) {
                return "<a data-id='".$model->id."' class='btn btn-sm btn-info rapor' >Rapor</a>";
            })
            ->make(true);
    }

    public function postEskiraporlist() {
        $query = ServisRaporu::select(array("servisraporu.id","uretimyer.yeradi","servisraporu.adet","servisraporu.raportarihi","servisraporu.graportarihi","servisraporu.durum"))
            ->leftjoin("uretimyer", "servisraporu.uretimyer_id", "=", "uretimyer.id");
        return Datatables::of($query)
            ->editColumn('raportarihi', function ($model) {
                $date = new DateTime($model->raportarihi);
                return $date->format('d-m-Y');})
            ->addColumn('islemler',function ($model) {
                if($model->durum==1) {
                    if ((time() - strtotime($model->raportarihi)) > (7 * 86400))
                        return "<a data-id='" . $model->id . "' class='btn btn-sm btn-success eskirapor' >Servis Raporu</a>";
                    else
                        return "<a data-id='" . $model->id . "' class='btn btn-sm btn-success eskirapor' >Servis Raporu</a>
                        <a data-id='" . $model->id . "' class='btn btn-sm btn-danger eskisil' >İşlemi Geri Al</a>";
                }else {
                    if ((time() - strtotime($model->raportarihi)) > (7 * 86400))
                        return "<a data-id='" . $model->id . "' class='btn btn-sm btn-success eskirapor' >Servis Raporu</a>
                        <a data-id='" . $model->id . "' class='btn btn-sm btn-warning eskikalibrasyon' >Kalibrasyon Raporu</a>";
                    else
                        return "<a data-id='" . $model->id . "' class='btn btn-sm btn-success eskirapor' >Servis Raporu</a>
                        <a data-id='" . $model->id . "' class='btn btn-sm btn-warning eskikalibrasyon' >Kalibrasyon Raporu</a>
                        <a data-id='" . $model->id . "' class='btn btn-sm btn-danger eskisil' >İşlemi Geri Al</a>";
                }
            })
            ->make(true);
    }

    public function postServisraporu(){
        try {
            DB::beginTransaction();
            $raportipi = Input::get('ireport');
            if($raportipi==1){
                $arizaid = Input::get('arizaid');
                $arizakayit = ArizaKayit::find($arizaid);
                if ($arizakayit) {
                    $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                } else {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Rapor alınacak sayaç seçilmedi', 'type' => 'warning', 'title' => 'Rapor Hatası'));
                }
                $raporadi = "ServisRaporu-" . Str::slug($sayacgelen->serino);
                $export = "pdf";
                $kriterler = array();
                $kriterler['id'] = $arizaid;
                JasperPHP::process(public_path('reports/servisraporu/servisraporugaz.jasper'), public_path('reports/outputs/servisraporu/' . $raporadi),
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
                $arizakayit->rapordurum=2;  //kalibrasyon raporu çıkmayacak
                $arizakayit->save();
                DB::commit();
                return Redirect::back()->with(array());
            }else if($raportipi==2) { //toplu rapor üretim yerine göre
                $uretimyerid = Input::get('uretimyerid');
                $uretimyer = Uretimyer::find($uretimyerid);
                if (!$uretimyer) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Rapor alınacak üretim yeri seçilmedi', 'type' => 'warning', 'title' => 'Rapor Hatası'));
                }
                $arizakayitlar = ArizaKayit::where('rapordurum', 0)->get();
                if($arizakayitlar->count()>0) {
                    $raporadi = "ServisRaporu-" . Str::slug($uretimyer->yeradi);
                    $export = "pdf";
                    $kriterler = array();
                    $kriterler['uretimyerid'] = $uretimyerid;
                    JasperPHP::process(public_path('reports/servisraporuliste/servisraporuliste.jasper'), public_path('reports/outputs/servisraporuliste/' . $raporadi),
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
                    readfile("reports/outputs/servisraporuliste/" . $raporadi . "." . $export . "");
                    File::delete("reports/outputs/servisraporuliste/" . $raporadi . "." . $export . "");

                    $servisraporu = new ServisRaporu;
                    $servisraporu->uretimyer_id = $uretimyerid;
                    $servisraporu->raportarihi = date('Y-m-d H:i:s');
                    $servisraporu->durum = 1;
                    $servisraporu->save();
                    $count = 0;
                    foreach ($arizakayitlar as $arizakayit) {
                        $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                        $sayacadi = SayacAdi::find($arizakayit->sayacadi_id);
                        $sayactip = SayacTip::find($sayacadi->sayactip_id);
                        if ($sayacgelen->uretimyer_id == $uretimyerid) {
                            if ($arizakayit->arizakayit_durum == 2) //hurda sayacın kalibrasyonu çıkmayacak
                                $arizakayit->rapordurum = 2;
                            else if ($arizakayit->arizakayit_durum == 8) //kalibrasyonsuz iade
                                $arizakayit->rapordurum = 2;
                            else if ($sayactip->tipadi != 'DİYAFRAM')
                                $arizakayit->rapordurum = 2;
                            else
                                $arizakayit->rapordurum = 1;
                            $arizakayit->servisraporu_id = $servisraporu->id;
                            $arizakayit->save();
                            $count++;
                        }
                    }
                    $servisraporu->adet = $count;
                    $servisraporu->save();
                    DB::commit();
                    return Redirect::back()->with(array());
                }else{
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Seçilen Yer için Toplu Servis Raporu Alınacak Sayaç Bulunamadı. Alınan Raporları Kontrol Ediniz.', 'type' => 'warning', 'title' => 'Rapor Hatası'));
                }
            }else if($raportipi==3){ //kalibrasyon raporu üretim yerine göre
                $uretimyerid = Input::get('uretimyerid');
                $uretimyer = Uretimyer::find($uretimyerid);
                if (!$uretimyer) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Rapor alınacak üretim yeri seçilmedi', 'type' => 'warning', 'title' => 'Rapor Hatası'));
                }
                $arizakayitlar = ArizaKayit::where('rapordurum', 1)->get();
                if($arizakayitlar->count()>0) {
                    $raporadi = "Kalibrasyon-" . Str::slug($uretimyer->yeradi);
                    $export = "pdf";
                    $kriterler = array();
                    $kriterler['uretimyerid'] = $uretimyerid;
                    JasperPHP::process(public_path('reports/kalibrasyonliste/kalibrasyonliste.jasper'), public_path('reports/outputs/kalibrasyonliste/' . $raporadi),
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
                    readfile("reports/outputs/kalibrasyonliste/" . $raporadi . "." . $export . "");
                    File::delete("reports/outputs/kalibrasyonliste/" . $raporadi . "." . $export . "");

                    foreach ($arizakayitlar as $arizakayit) {
                        $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                        if ($sayacgelen->uretimyer_id == $uretimyerid) {
                            $arizakayit->rapordurum = 2;
                            $arizakayit->save();
                            $servisraporu = ServisRaporu::find($arizakayit->servisraporu_id);
                            if ($servisraporu) {
                                $servisraporu->durum = 2;
                                $servisraporu->save();
                            }
                        }
                    }
                    DB::commit();
                    return Redirect::back()->with(array());
                }else{
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Seçilen Yer için Kalibrasyon Raporu Alınacak Sayaç Bulunamadı. Önce Toplu Servis Raporu almak gerekebilir.', 'type' => 'warning', 'title' => 'Rapor Hatası'));
                }
            }else if($raportipi==4){ //eski toplu servis raporunu çekicek
                $servisraporuid = Input::get('servisraporuid');
                $servisraporu = ServisRaporu::find($servisraporuid);
                $uretimyer = Uretimyer::find($servisraporu->uretimyer_id);
                if (!$uretimyer) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Rapor alınacak üretim yeri seçilmedi', 'type' => 'warning', 'title' => 'Rapor Hatası'));
                }
                $raporadi = "ServisRaporu-" . Str::slug($uretimyer->yeradi);
                $export = "pdf";
                $kriterler = array();
                $kriterler['servisraporuid'] = $servisraporu->id;
                JasperPHP::process(public_path('reports/eskiservisraporuliste/eskiservisraporuliste.jasper'), public_path('reports/outputs/eskiservisraporuliste/' . $raporadi),
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
                readfile("reports/outputs/eskiservisraporuliste/" . $raporadi . "." . $export . "");
                File::delete("reports/outputs/eskiservisraporuliste/" . $raporadi . "." . $export . "");
                return Redirect::back()->with(array());
            }else if($raportipi==5){ // eski toplu kalibrasyon raporunu çekicek
                $servisraporuid = Input::get('servisraporuid');
                $servisraporu = ServisRaporu::find($servisraporuid);
                $uretimyer = Uretimyer::find($servisraporu->uretimyer_id);
                if (!$uretimyer) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Rapor alınacak üretim yeri seçilmedi', 'type' => 'warning', 'title' => 'Rapor Hatası'));
                }
                $raporadi = "Kalibrasyon-" . Str::slug($uretimyer->yeradi);
                $export = "pdf";
                $kriterler = array();
                $kriterler['servisraporuid'] = $servisraporu->id;
                JasperPHP::process(public_path('reports/eskikalibrasyonliste/eskikalibrasyonliste.jasper'), public_path('reports/outputs/eskikalibrasyonliste/' . $raporadi),
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
                readfile("reports/outputs/eskikalibrasyonliste/" . $raporadi . "." . $export . "");
                File::delete("reports/outputs/eskikalibrasyonliste/" . $raporadi . "." . $export . "");
                return Redirect::back()->with(array());
            }else{ // servis raporu alınmamış gibi eski haline getirmek
                $servisraporuid = Input::get('servisraporuid');
                $servisraporu = ServisRaporu::find($servisraporuid);
                $arizakayitlar = ArizaKayit::where('servisraporu_id',$servisraporuid)->get();
                foreach ($arizakayitlar as $arizakayit){
                    $arizakayit->rapordurum=0;
                    $arizakayit->servisraporu_id=NULL;
                    $arizakayit->save();
                }
                $servisraporu->delete();
                DB::commit();
                return Redirect::back()->with(array());
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning', 'title' => 'Rapor Hatası'));
        }
    }
}
