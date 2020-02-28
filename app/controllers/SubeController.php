<?php
//transaction işlemi tamamlandı
class SubeController extends BackendController {
    public $servisadi = 'sube';
    public $servisid = 6;
    public $servisbilgi = 'Şube';

    public function getAbonekayit(){
        return View::make($this->servisadi.'.abonekayit')->with(array('title'=>'Abone Kayıdı'));
    }

    public function postAbonekayitlist() {
        $netsiscari_id=Input::get('netsiscari_id');
        $sube = null;
        if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $sube=Sube::whereIn('netsiscari_id',$netsiscarilist)->where('aktif',1)->first();
            if($sube) {
                $query = Abone::where('abone.subekodu', $sube->subekodu)
                    ->select(array("abone.id", "abone.adisoyadi", "abone.tckimlikno", "uretimyer.yeradi", "abone.telefon", "abone.faturaadresi", "abone.nadisoyadi",
                        "uretimyer.nyeradi", "abone.nfaturaadresi"))
                    ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id");
            }else{
                $query = Abone::select(array("abone.id", "abone.adisoyadi", "abone.tckimlikno","uretimyer.yeradi", "abone.telefon", "abone.faturaadresi", "abone.nadisoyadi",
                    "uretimyer.nyeradi","abone.nfaturaadresi"))
                    ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id");
            }
        }else{
            $query = Abone::select(array("abone.id", "abone.adisoyadi", "abone.tckimlikno","uretimyer.yeradi", "abone.telefon", "abone.faturaadresi", "abone.nadisoyadi",
                "uretimyer.nyeradi","abone.nfaturaadresi"))
                ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id");
        }
        return Datatables::of($query)
            ->addColumn('islemler',function ($model)use($netsiscari_id,$sube) {
                $root = BackendController::getRootDizin();
                if($netsiscari_id && $sube)
                    return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/abonekayitduzenle/".$model->id."' > Düzenle </a>
                        <a href='#portlet-delete' data-toggle='modal' data-id='{$model->id}' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                else
                    return "";
            })
            ->make(true);
    }

    public function getAbonekayitekle() {
        $netsiscariid=Auth::user()->netsiscari_id;
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        $netsiscariler=NetsisCari::whereIn('id',Auth::user()->netsiscari_id)->get(array('id'))->toArray();
        $uretimyerid=CariYer::whereIn('netsiscari_id',$netsiscariler)->where('durum',1)->get(array('uretimyer_id'))->toArray();
        $uretimyerleri = UretimYer::whereIn('id',$uretimyerid)->get();
        if($sube){
            $sube->netsiscari = NetsisCari::find($sube->netsiscari_id);
            $iller = Iller::all();
            $ilceler = Ilceler::where('iller_id',$sube->iller_id)->get();
            $subeyetkili=SubeYetkili::where('kullanici_id',Auth::user()->id)->where('aktif',1)->get(array('netsiscari_id'))->toArray();
            $netsiscariler = NetsisCari::where('caridurum','A')->whereIn('caritipi',array('A','D'))
                ->where(function($query)use($subeyetkili,$sube){$query->whereIn('id',$subeyetkili)->orwhereIn('subekodu',array(-1,$sube->subekodu));})
                ->whereNotIn('carikod',(function ($query) use ($sube) {$query->select('carikod')->from('kodharichar')->where('subekodu', $sube->subekodu);}))
                ->whereNotIn('id',array($sube->netsiscari_id))
                ->orderBy('cariadi','asc')->get();
            $sayacturlist=explode(',',$sube->sayactur);
            $sayacadilist=explode(',',$sube->sayacadlari);
            $sayacturleri = SayacTur::whereIn('id',$sayacturlist)->get();
            $sayacadlari = SayacAdi::whereIn('sayactur_id',$sayacturlist)->whereIn('id',$sayacadilist)->get();
            $sayaccaplari = SayacCap::all();
            return View::make($this->servisadi.'.abonekayitekle',array('sayacturleri'=>$sayacturleri,'sayacadlari'=>$sayacadlari,'sayaccaplari'=>$sayaccaplari,'uretimyerleri'=>$uretimyerleri,'netsiscariler'=> $netsiscariler,'sube'=>$sube,'iller'=>$iller,'ilceler'=>$ilceler))->with(array('title'=>'Abone Kayıdı Ekle'));
        }else{
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkisiz', 'text' => 'Abone Ekleme Yetkiniz Yok', 'type' => 'error'));
        }
    }

    public function getSubesayacadlari()
    {
        $sayacturid=Input::get('sayacturid');
        $subekodu=Input::get('subekodu');
        $sube=Sube::where('subekodu',$subekodu)->where('aktif',1)->first();
        if($sube){
            $sayacadilist=explode(',',$sube->sayacadlari);
            $sayacadlari = SayacAdi::where('sayactur_id',$sayacturid)->whereIn('id',$sayacadilist)->get();
            $sayaccaplari=array();
            $capdurum=0;
            if($sayacadlari->count()>0){
                switch($sayacturid){
                    case 1:
                    case 4:
                        $capdurum=1;
                        $sayaccaplari=SayacCap::all();
                        break;
                    case 2:
                    case 3:
                    case 5:
                        $sayaccaplari=array();
                        $capdurum=0;
                        break;
                }
                return Response::json(array('durum' => true, 'sayacadlari' => $sayacadlari, 'sayaccaplari' => $sayaccaplari, 'capdurum' => $capdurum));
            }else{
                return Response::json(array('durum' => false, 'title' => 'Sayaç Adı Bulunamadı', 'text' => 'Girilen Sayaç Türüne Ait Sayac Adı Girilmemiş.', 'type' => 'error'));
            }
        }else{
            return Response::json(array('durum' => false, 'title' => 'Sayaç Adı Bulunamadı', 'text' => 'Aktif Şube Bilgisi Mevcut Değil!', 'type' => 'error'));
        }
    }

    public function postAbonekayitekle() {
        try{
            $rules = ['cariadi'=>'required','adisoyadi'=>'required','tckimlikno'=>'required','telefon'=>'required|min:10','uretimyer'=>'required','adres'=>'required','il'=>'required','ilce'=>'required'
               // 'serino'=>'required','sayacadlari'=>'required','sayaccaplari'=>'required','sayacturleri'=>'required','sayacadresi'=>'required'
            ];
            $validate = Validator::make(Input::all(),$rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $adisoyadi = Input::get('adisoyadi');
            $uretimyer = Input::get('uretimyer');
            $netsiscari = Input::get('cariadi');
            $vergidairesi = Input::get('vergidairesi');
            $tckimlikno = Input::get('tckimlikno');
            $aboneno = Input::get('aboneno');
            $telefon = Input::get('telefon');
            $email = Input::get('email');
            $adres = Input::get('adres');
            $il = Input::get('il');
            $ilce = Input::get('ilce');
            $serinolar = Input::get('serino');
            $sayacturleri = Input::get('sayacturleri');
            $sayacadlari = Input::get('sayacadlari');
            $sayaccaplari = Input::get('sayaccaplari');
            $sayacadresi = Input::get('sayacadresi');
            $sayacbilgi = Input::get('sayacbilgi');
            $sayaciletisim = Input::get('sayaciletisim');
            $subekodu = Input::get('subekodu');
            $telefon = preg_replace('/\D/','',$telefon);
            $telefon = mb_substr($telefon, 0, 4)." ".mb_substr($telefon, 4, 3)." ".mb_substr($telefon, 7, 2)." ".mb_substr($telefon, 9, 2);
            $yetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
            if (!$yetkili) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkisiz', 'text' => 'Abone Kaydetme Yetkiniz Yok', 'type' => 'error'));
            }
            $abonesayaclist = array();
            $adet = 0;
            DB::beginTransaction();
            for ($i = 0; $i < count($serinolar); $i++) {
                $serino1 = $serinolar[$i];
                if (isset($sayaccaplari[$i]))
                    $sayaccap = $sayaccaplari[$i];
                else
                    $sayaccap = '1';
                if ($serino1 == "")
                    continue;
                $adet++;
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
                $sayaclar = Sayac::where('serino', $serino1)->where('uretimyer_id', $uretimyer)->get();
                $kayitlisayac = "";
                if($sayaclar->count()>1){ //birden fazla sayaç varsa
                    $flag=1;
                    foreach($sayaclar as $sayac){
                        if($sayac->sayacadi_id){
                            if($sayac->sayacadi_id==$sayacadlari[$i]){
                                $flag=1;
                            }else{
                                $kayitlisayac=SayacAdi::find($sayac->sayacadi_id);
                                $flag=0;
                            }
                        }
                    }
                    if($flag!=1){
                        DB::rollBack();
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hatası', 'text' => 'Girilen Sayacın Sistemdeki Adı Farklı: '.$kayitlisayac->sayacadi, 'type' => 'error'));
                    }
                }elseif($sayaclar->count()>0){ //tek sayaç varsa
                    $sayac=$sayaclar[0];
                    if($sayac->sayacadi_id)
                        if($sayac->sayacadi_id!=$sayacadlari[$i]){
                            $sayac->sayacadi=SayacAdi::find($sayac->sayacadi_id);
                            DB::rollBack();
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hatası', 'text' => 'Girilen Sayacın Sistemdeki Adı Farklı: '.$sayac->sayacadi->sayacadi, 'type' => 'error'));
                        }
                } else { //sayaç başka yere ait ya da yoksa
                    $sayaclar = Sayac::where('serino', $serino1)->get();
                    $kayitliyer = "";
                    foreach ($sayaclar as $sayac) {
                        $kayitliyer = Uretimyer::find($sayac->uretimyer_id);
                    }
                    if ($kayitliyer == "") { //sayaç yoksa sayaç kaydedilecek
                        try{
                            $sayac = new Sayac;
                            $sayac->serino = $serino1;
                            $sayac->cihazno = $serino1;
                            $sayac->sayactur_id = $sayacturleri[$i];
                            $sayac->sayacadi_id = $sayacadlari[$i];
                            $sayac->sayaccap_id = $sayaccap;
                            $sayac->uretimtarihi = date('Y-m-d H:i:s');
                            $sayac->uretimyer_id = $uretimyer;
                            $sayac->kullanici_id = Auth::user()->id;
                            $sayac->save();
                        }catch(Exception $e){
                            Log::error($e);
                            DB::rollBack();
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hatası', 'text' => 'Girilen Sayaç Sisteme Kaydedilemedi', 'type' => 'error'));
                        }
                    } else {
                        DB::rollBack();
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hatası', 'text' => 'Girilen Sayacın Sistemdeki Yeri Farklı: ' . $serino1 . '->' . $kayitliyer->yeradi, 'type' => 'error'));
                    }
                }
                $abonesayac = AboneSayac::where('serino', $serino1)->first();
                if ($abonesayac) {
                    $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                    if ($abonetahsis) {
                        $abone = Abone::find($abonetahsis->abone_id);
                        DB::rollBack();
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: ' . $abone->abone_no, 'type' => 'error'));
                    } else {
                        $abonesayac->uretimyer_id=$uretimyer;
                        $abonesayac->sayactur_id=$sayacturleri[$i];
                        $abonesayac->sayacadi_id=$sayacadlari[$i];
                        $abonesayac->sayaccap_id=$sayaccap;
                        $abonesayac->adres=BackendController::StrtoUpper($sayacadresi[$i]);
                        $abonesayac->bilgi=$sayacbilgi[$i];
                        $abonesayac->iletisim=$sayaciletisim[$i];
                        $abonesayac->save();
                        array_push($abonesayaclist, $abonesayac->id);
                    }
                } else {
                    try{
                        $abonesayac = new AboneSayac;
                        $abonesayac->serino = $serino1;
                        $abonesayac->uretimyer_id = $uretimyer;
                        $abonesayac->sayactur_id = $sayacturleri[$i];
                        $abonesayac->sayacadi_id = $sayacadlari[$i];
                        $abonesayac->sayaccap_id = $sayaccap;
                        $abonesayac->adres = BackendController::StrtoUpper($sayacadresi[$i]);
                        $abonesayac->bilgi=$sayacbilgi[$i];
                        $abonesayac->iletisim=$sayaciletisim[$i];
                        $abonesayac->save();
                        array_push($abonesayaclist, $abonesayac->id);
                    }catch (Exception $e){
                        Log::error($e);
                        DB::rollBack();
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Abone Sayacı Kaydedilemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                    }
                }
            }
            /*if ($adet == 0) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Abone Bilgisi Kaydedilemedi', 'text' => 'Girilen Sayaçların Seri Numaraları yazılmamış', 'type' => 'error'));
            }*/

            try{
                $abone = new Abone;
                $abone->subekodu = $subekodu;
                $abone->netsiscari_id = $netsiscari;
                $abone->adisoyadi = BackendController::StrtoUpper($adisoyadi);
                $abone->vergidairesi = BackendController::StrtoUpper($vergidairesi);
                $abone->tckimlikno = $tckimlikno;
                $abone->abone_no = $aboneno;
                $abone->telefon = $telefon;
                $abone->email = $email=="" ? null : $email;
                $abone->faturaadresi = BackendController::StrtoUpper($adres);
                $abone->iller_id = $il;
                $abone->ilceler_id = $ilce;
                $abone->uretimyer_id = $uretimyer;
                $abone->netsiscari_id = $netsiscari;
                $abone->kullanici_id = Auth::user()->id;
                $abone->save();
            }catch(Exception $e){
                Log::error($e);
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Abone Bilgisi Kaydedilemedi', 'text' => 'Abone Bilgisi Eksik Ya da Hatalı', 'type' => 'error'));
            }

            try {
                for ($i = 0; $i < count($abonesayaclist); $i++) {
                    $abonetahsis = new AboneTahsis;
                    $abonetahsis->abone_id = $abone->id;
                    $abonetahsis->abonesayac_id = $abonesayaclist[$i];
                    $abonetahsis->tahsistarihi = date('Y-m-d H:i:s');
                    $abonetahsis->save();
                }
            }catch(Exception $e) {
                Log::error($e);
                DB::rollBack();
                Input::flash();
                return Redirect::to($this->servisadi.'/abonekayit')->with(array('mesaj' => 'true', 'title' => 'Abone Kayıdı Yapılamadı', 'text' => 'Abonenin Tahsisi Kaydedilemedi', 'type' => 'error'));
            }
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $adisoyadi . ' İsimli Abone Kayıdı Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Abone Numarası:' . $abone->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/abonekayit')->with(array('mesaj' => 'true', 'title' => 'Abone Kayıdı Yapıldı', 'text' => 'Abone Kayıdı Başarıyla Yapıldı', 'type' => 'success'));
        }catch (Exception $e){
            Log::error($e);
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Hata Oluştu', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getAbonekayitduzenle($id) {
        $abone = Abone::find($id);
        $abonetahsisleri = AboneTahsis::where('abone_id',$abone->id)->get();
        $abonesayaclist=array();
        foreach($abonetahsisleri as $abonetahsis)
        {
            array_push($abonesayaclist,$abonetahsis->abonesayac_id);
        }
        $abonesayaclari = AboneSayac::whereIn('id',$abonesayaclist)->get();
        foreach($abonesayaclari as $abonesayac){
            $abonesayac->sayacadi=SayacAdi::find($abonesayac->sayacadi_id);
            $abonesayac->sayaccap=SayacCap::find($abonesayac->sayaccap_id);
            $abonesayac->sayactur=SayacTur::find($abonesayac->sayactur_id);
        }
        $netsiscariid=Auth::user()->netsiscari_id;
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        $netsiscariler=NetsisCari::whereIn('id',Auth::user()->netsiscari_id)->get(array('id'))->toArray();
        $uretimyerid=CariYer::whereIn('netsiscari_id',$netsiscariler)->where('durum',1)->get(array('uretimyer_id'))->toArray();
        $uretimyerleri = UretimYer::whereIn('id',$uretimyerid)->get();
        if($sube){
            $sube->netsiscari = NetsisCari::find($sube->netsiscari_id);
            $iller = Iller::all();
            $ilceler = Ilceler::where('iller_id',$abone->iller_id)->get();
            $subeyetkili=SubeYetkili::where('kullanici_id',Auth::user()->id)->where('aktif',1)->get(array('netsiscari_id'))->toArray();
            $netsiscariler = NetsisCari::where('caridurum','A')->whereIn('caritipi',array('A','D'))
                ->where(function($query)use($subeyetkili,$sube){$query->whereIn('id',$subeyetkili)->orwhereIn('subekodu',array(-1,$sube->subekodu));})
                ->whereNotIn('carikod',(function ($query) use ($sube) {$query->select('carikod')->from('kodharichar')->where('subekodu', $sube->subekodu);}))
                ->whereNotIn('id',array($sube->netsiscari_id))
                ->orderBy('cariadi','asc')->get();
            $sayacturlist=explode(',',$sube->sayactur);
            $sayacadilist=explode(',',$sube->sayacadlari);
            $sayacturleri = SayacTur::whereIn('id',$sayacturlist)->get();
            $sayacadlari = SayacAdi::whereIn('sayactur_id',$sayacturlist)->whereIn('id',$sayacadilist)->get();
            $sayaccaplari = SayacCap::all();
            return View::make($this->servisadi.'.abonekayitduzenle',array('abone'=>$abone,'abonesayaclari'=>$abonesayaclari,'uretimyerleri'=>$uretimyerleri,'sayacturleri'=>$sayacturleri,'sayacadlari'=>$sayacadlari,'sayaccaplari'=>$sayaccaplari,'netsiscariler'=> $netsiscariler,'sube'=>$sube,'iller'=>$iller,'ilceler'=>$ilceler))->with(array('title'=>'Abone Kayıdı Düzenleme Ekranı'));
        }else{
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkisiz', 'text' => 'Abone Ekleme Yetkiniz Yok', 'type' => 'error'));
        }
    }

    public function postAbonekayitduzenle($id) {
        try{
            $rules = ['cariadi'=>'required','adisoyadi'=>'required','tckimlikno'=>'required','telefon'=>'required|min:10','uretimyer'=>'required','adres'=>'required','il'=>'required','ilce'=>'required'
               // ,'serino'=>'required','sayacadlari'=>'required','sayaccaplari'=>'required','sayacturleri'=>'required','sayacadresi'=>'required'
            ];
            $validate = Validator::make(Input::all(),$rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $adisoyadi=Input::get('adisoyadi');
            $uretimyer=Input::get('uretimyer');
            $netsiscari = Input::get('cariadi');
            $vergidairesi=Input::get('vergidairesi');
            $tckimlikno=Input::get('tckimlikno');
            $aboneno=Input::get('aboneno');
            $telefon=Input::get('telefon');
            $email=Input::get('email');
            $adres=Input::get('adres');
            $il = Input::get('il');
            $ilce = Input::get('ilce');
            $serinolar=Input::get('serino');
            $sayacturleri=Input::get('sayacturleri');
            $sayacadlari=Input::get('sayacadlari');
            $sayaccaplari = Input::get('sayaccaplari');
            $sayacadresi = Input::get('sayacadresi');
            $sayacbilgi = Input::get('sayacbilgi');
            $sayaciletisim = Input::get('sayaciletisim');
            $abonesayaclist=array();
            $guncelabone=Abone::find($id);
            $telefon = preg_replace('/\D/','',$telefon);
            $telefon = mb_substr($telefon, 0, 4)." ".mb_substr($telefon, 4, 3)." ".mb_substr($telefon, 7, 2)." ".mb_substr($telefon, 9, 2);
            $bilgi=$guncelabone;
            $abonetahsisler=AboneTahsis::where('abone_id',$id)->get();
            $yetkili=SubeYetkili::where('kullanici_id',Auth::user()->id)->where('aktif',1)->first();
            if(!$yetkili){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkisiz', 'text' => 'Abone Kaydetme Yetkiniz Yok', 'type' => 'error'));
            }
            DB::beginTransaction();
            $eskiabonesayaclist=array();
            foreach($abonetahsisler as $abonetahsis){
                array_push($eskiabonesayaclist,$abonetahsis->abonesayac_id);
            }
            $adet=0;
            for($i=0;$i<count($serinolar);$i++)
            {
                $serino1=$serinolar[$i];
                if(isset($sayaccaplari[$i]))
                    $sayaccap=$sayaccaplari[$i];
                else
                    $sayaccap='1';
                if($serino1=="")
                    continue;
                $adet++;
                if(count($serinolar)>1) {
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
                $sayaclar=Sayac::where('serino',$serino1)->where('uretimyer_id',$uretimyer)->get();
                $kayitlisayac="";
                if($sayaclar->count()>1){ //birden fazla sayaç varsa
                    $flag=1;
                    foreach($sayaclar as $sayac){
                        if($sayac->sayacadi_id){
                            if($sayac->sayacadi_id==$sayacadlari[$i]){
                                $flag=1;
                            }else{
                                $kayitlisayac=SayacAdi::find($sayac->sayacadi_id);
                                $flag=0;
                            }
                        }
                    }
                    if($flag!=1){
                        DB::rollBack();
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hatası', 'text' => 'Girilen Sayacın Sistemdeki Adı Farklı: '.$kayitlisayac->sayacadi, 'type' => 'error'));
                    }
                }elseif($sayaclar->count()>0){ //tek sayaç varsa
                    $sayac=$sayaclar[0];
                    if($sayac->sayacadi_id)
                        if($sayac->sayacadi_id!=$sayacadlari[$i]){
                            $sayac->sayacadi=SayacAdi::find($sayac->sayacadi_id);
                            DB::rollBack();
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hatası', 'text' => 'Girilen Sayacın Sistemdeki Adı Farklı: '.$sayac->sayacadi->sayacadi, 'type' => 'error'));
                        }
                }else { //sayaç başka yere ait ya da yoksa
                    $sayaclar = Sayac::where('serino', $serino1)->get();
                    $kayitliyer = "";
                    foreach ($sayaclar as $sayac) {
                        $kayitliyer=Uretimyer::find($sayac->uretimyer_id);
                    }
                    if($kayitliyer=="") { //sayaç yoksa sayaç kaydedilecek
                        try{
                            $sayac=new Sayac;
                            $sayac->serino=$serino1;
                            $sayac->cihazno=$serino1;
                            $sayac->sayactur_id=$sayacturleri[$i];
                            $sayac->sayacadi_id=$sayacadlari[$i];
                            $sayac->sayaccap_id=$sayaccap;
                            $sayac->uretimtarihi=date('Y-m-d H:i:s');
                            $sayac->uretimyer_id=$uretimyer;
                            $sayac->kullanici_id=Auth::user()->id;
                            $sayac->save();
                        }catch(Exception $e){
                            Log::error($e);
                            DB::rollBack();
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hatası', 'text' => 'Girilen Sayacın Sistemdeki Yeri Farklı: '.$serino1.'->'. $kayitliyer->yeradi, 'type' => 'error'));
                        }
                    }else{
                        DB::rollBack();
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hatası', 'text' => 'Girilen Sayacın Sistemdeki Yeri Farklı: '.$serino1.'->'. $kayitliyer->yeradi, 'type' => 'error'));
                    }
                }
                $abonesayac= AboneSayac::where('serino',$serino1)->first();
                if($abonesayac){
                    $abonetahsis=AboneTahsis::where('abonesayac_id',$abonesayac->id)->where('abone_id','<>',$guncelabone->id)->first();
                    if($abonetahsis){
                        $abone=Abone::find($abonetahsis->abone_id);
                        DB::rollBack();
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: '.$abone->abone_no, 'type' => 'error'));
                    }else{
                        $abonesayac->uretimyer_id=$uretimyer;
                        $abonesayac->sayactur_id=$sayacturleri[$i];
                        $abonesayac->sayacadi_id=$sayacadlari[$i];
                        $abonesayac->sayaccap_id=$sayaccap;
                        $abonesayac->adres=BackendController::StrtoUpper($sayacadresi[$i]);
                        $abonesayac->bilgi=$sayacbilgi[$i];
                        $abonesayac->iletisim=$sayaciletisim[$i];
                        $abonesayac->save();
                        array_push($abonesayaclist,$abonesayac->id);
                    }
                }else{
                    try{
                        $abonesayac= new AboneSayac;
                        $abonesayac->serino=$serino1;
                        $abonesayac->uretimyer_id=$uretimyer;
                        $abonesayac->sayactur_id=$sayacturleri[$i];
                        $abonesayac->sayacadi_id=$sayacadlari[$i];
                        $abonesayac->sayaccap_id=$sayaccap;
                        $abonesayac->adres=BackendController::StrtoUpper($sayacadresi[$i]);
                        $abonesayac->bilgi=$sayacbilgi[$i];
                        $abonesayac->iletisim=$sayaciletisim[$i];
                        $abonesayac->save();
                        array_push($abonesayaclist,$abonesayac->id);
                    }catch(Exception $e){
                        Log::error($e);
                        DB::rollBack();
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Abone Bilgisi Kaydedilemedi', 'text' => 'Girilen Sayaçların Seri Numaraları yazılmamış', 'type' => 'error'));
                    }
                }
            }
            try{
                $guncelabone->adisoyadi=BackendController::StrtoUpper($adisoyadi);
                $guncelabone->netsiscari_id = $netsiscari;
                $guncelabone->vergidairesi=BackendController::StrtoUpper($vergidairesi);
                $guncelabone->tckimlikno=$tckimlikno;
                $guncelabone->abone_no=$aboneno;
                $guncelabone->telefon=$telefon;
                $guncelabone->email = $email=="" ? null : $email;
                $guncelabone->faturaadresi=BackendController::StrtoUpper($adres);
                $guncelabone->iller_id = $il;
                $guncelabone->ilceler_id = $ilce;
                $guncelabone->uretimyer_id=$uretimyer;
                $guncelabone->netsiscari_id=$netsiscari;
                $guncelabone->kullanici_id=Auth::user()->id;
                $guncelabone->save();
            }catch (Exception $e){
                Log::error($e);
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Abone Kayıdı Yapılamadı', 'text' => 'Abonenin Bilgileri Kaydedilemedi', 'type' => 'error'));
            }
            try{
                $silinecekler=BackendController::getListeFark($eskiabonesayaclist,$abonesayaclist);
                $silinecekler=explode(',',$silinecekler);
                foreach($silinecekler as $silinecek){
                    $abonetahsis=AboneTahsis::where('abonesayac_id',$silinecek)->first();
                    if($abonetahsis){
                        ServisKayit::where('abonetahsis_id',$abonetahsis->id)->where('durum',0)->delete();
                        $abonetahsis->delete();
                        AboneSayac::find($silinecek)->update(['satisdurum'=>0]);
                    }
                }
                for($i=0;$i<count($abonesayaclist);$i++)
                {
                    $abonetahsis=AboneTahsis::where('abonesayac_id',$abonesayaclist[$i])->first();
                    if(!$abonetahsis){
                        $abonetahsis = new AboneTahsis;
                        $abonetahsis->abone_id=$guncelabone->id;
                        $abonetahsis->abonesayac_id=$abonesayaclist[$i];
                        $abonetahsis->tahsistarihi=date('Y-m-d H:i:s');
                        $abonetahsis->save();
                    }else{
                        $abonesayac = AboneSayac::find($abonetahsis->abonesayac_id);
                        $serviskayit = ServisKayit::where('abonetahsis_id',$abonetahsis->id)->where('durum','<>',1)->get();
                        foreach ($serviskayit as $kayit){
                            $kayit->kayitadres = $abonesayac->adres;
                            $kayit->save();
                        }
                    }
                }
            }catch(Exception  $e){
                Log::error($e);
                DB::rollBack();
                Input::flash();
                return Redirect::to($this->servisadi.'/abonekayit')->with(array('mesaj' => 'true', 'title' => 'Abone Kayıdı Yapılamadı', 'text' => 'Abonenin Tahsisi Kaydedilemedi', 'type' => 'error'));
            }
            BackendController::IslemEkle(2,Auth::user()->id,'label-warning','fa-edit',$bilgi->adisoyadi.' İsimli Abone Kayıdı Güncellendi.','Güncelleyen:'.Auth::user()->adi_soyadi.',Abone Numarası:'.$bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/abonekayit')->with(array('mesaj' => 'true', 'title' => 'Abone Kayıdı Güncellendi', 'text' => 'Abone Kayıdı Başarıyla Güncellendi', 'type' => 'success'));
        }catch(Exception $e){
            Log::error($e);
            DB::rollBack();
            Input::flash();
            return Redirect::to($this->servisadi.'/abonekayit')->with(array('mesaj' => 'true', 'title' => 'Abone Kayıdı Güncellenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getAbonekayitsil($id){
        try{
            DB::beginTransaction();
            $abone = Abone::find($id);
            if($abone){
                $bilgi=$abone;
                $sayacsatis=SubeSayacSatis::where('abone_id',$abone->id)->get();
                if($sayacsatis->count()>0){
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Kayıdı Silinemez', 'text' => 'Aboneye Ait Satış Kayıdı Var!.', 'type' => 'error'));
                }
                $abonetahsisler=AboneTahsis::where('abone_id',$abone->id)->get();
                foreach($abonetahsisler as $abonetahsis){
                    ServisKayit::where('abonetahsis_id',$abonetahsis->id)->where('durum',0)->delete();
                    $abonetahsis->delete();
                }
                $abone->delete();
                BackendController::IslemEkle(3,Auth::user()->id,'label-danger','fa-close',$bilgi->adisoyadi.' İsimli Abone Kayıdı Silindi.','Silen:'.Auth::user()->adi_soyadi.',Abone Numarası:'.$bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Kayıdı Silindi', 'text' => 'Abone Kayıdı Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Kayıdı Silinemedi', 'text' => 'Abone Kayıdı Zaten Silinmiş.', 'type' => 'error'));
            }
        }catch(Exception $e){
            Log::error($e);
            DB::rollBack();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Kayıdı Silinemedi', 'text' => 'Abone Kayıdı Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function postHizliaboneekle() {
        try{
            $rules = ['netsiscari'=>'required','adisoyadi'=>'required','tckimlikno'=>'required','telefon'=>'required|min:10','uretimyer'=>'required','adres'=>'required','il'=>'required','ilce'=>'required'];
            $validate = Validator::make(Input::all(),$rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                return Response::json(array('durum' => 0,'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $adisoyadi = Input::get('adisoyadi');
            $uretimyer = Input::get('uretimyer');
            $netsiscari = Input::get('netsiscari');
            $vergidairesi = Input::get('vergidairesi');
            $tckimlikno = Input::get('tckimlikno');
            $aboneno = Input::get('aboneno');
            $telefon = Input::get('telefon');
            $email = Input::get('email');
            $adres = Input::get('adres');
            $il = Input::get('il');
            $ilce = Input::get('ilce');
            $subekodu = Input::get('subekodu');
            $eklisayaclar = Input::get('sayaclar');
            $telefon = preg_replace('/\D/','',$telefon);
            $telefon = mb_substr($telefon, 0, 4)." ".mb_substr($telefon, 4, 3)." ".mb_substr($telefon, 7, 2)." ".mb_substr($telefon, 9, 2);
            $yetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
            if (!$yetkili) {
                return Response::json(array('durum' => 0,'title' => 'Kullanıcı Yetkisiz', 'text' => 'Abone Kaydetme Yetkiniz Yok', 'type' => 'error'));
            }
            $abonesayaclist = array();
            $adet = 0;
            DB::beginTransaction();
            for ($i = 0; $i < count($eklisayaclar); $i++) {
                $serino1 = $eklisayaclar[$i]['serino'];
                if (isset($eklisayaclar[$i]['sayaccapi']))
                    $sayaccap = $eklisayaclar[$i]['sayaccapi'];
                else
                    $sayaccap = '1';
                if ($serino1 == "")
                    continue;
                $adet++;
                if (count($eklisayaclar) > 1) {
                    for ($j = $i + 1; $j < count($eklisayaclar); $j++) {
                        $serino2 = $eklisayaclar[$j]['serino'];
                        if ($serino2 == "")
                            continue;
                        if ($serino1 == $serino2) {
                            return Response::json(array('durum' => 0,'title' => 'Seri No Hatası', 'text' => 'Aynı Seri Numarası Girilmiş', 'type' => 'error'));
                        }
                    }
                }
                $sayaclar = Sayac::where('serino', $serino1)->where('uretimyer_id', $uretimyer)->get();
                $kayitlisayac = "";
                if($sayaclar->count()>1){ //birden fazla sayaç varsa
                    $flag=1;
                    foreach($sayaclar as $sayac){
                        if($sayac->sayacadi_id){
                            if($sayac->sayacadi_id==$eklisayaclar[$i]['sayacadi']){
                                $flag=1;
                            }else{
                                $kayitlisayac=SayacAdi::find($sayac->sayacadi_id);
                                $flag=0;
                            }
                        }
                    }
                    if($flag!=1){
                        DB::rollBack();
                        return Response::json(array('durum' => 0,'title' => 'Sayaç Hatası', 'text' => 'Girilen Sayacın Sistemdeki Adı Farklı: '.$kayitlisayac->sayacadi, 'type' => 'error'));
                    }
                }elseif($sayaclar->count()>0){ //tek sayaç varsa
                    $sayac=$sayaclar[0];
                    if($sayac->sayacadi_id)
                        if($sayac->sayacadi_id!=$eklisayaclar[$i]['sayacadi']){
                            $sayac->sayacadi=SayacAdi::find($sayac->sayacadi_id);
                            DB::rollBack();
                            return Response::json(array('durum' => 0,'title' => 'Sayaç Hatası', 'text' => 'Girilen Sayacın Sistemdeki Adı Farklı: '.$sayac->sayacadi->sayacadi, 'type' => 'error'));
                        }
                } else { //sayaç başka yere ait ya da yoksa
                    $sayaclar = Sayac::where('serino', $serino1)->get();
                    $kayitliyer = "";
                    foreach ($sayaclar as $sayac) {
                        $kayitliyer = Uretimyer::find($sayac->uretimyer_id);
                    }
                    if ($kayitliyer == "") { //sayaç yoksa sayaç kaydedilecek
                        try{
                            $sayac = new Sayac;
                            $sayac->serino = $serino1;
                            $sayac->cihazno = $serino1;
                            $sayac->sayactur_id = $eklisayaclar[$i]['sayactur'];
                            $sayac->sayacadi_id = $eklisayaclar[$i]['sayacadi'];
                            $sayac->sayaccap_id = $sayaccap;
                            $sayac->uretimtarihi = date('Y-m-d H:i:s');
                            $sayac->uretimyer_id = $uretimyer;
                            $sayac->kullanici_id = Auth::user()->id;
                            $sayac->save();
                        }catch(Exception $e){
                            Log::error($e);
                            DB::rollBack();
                            return Response::json(array('durum' => 0,'title' => 'Sayaç Hatası', 'text' => 'Girilen Sayaç Sisteme Kaydedilemedi', 'type' => 'error'));
                        }
                    } else {
                        DB::rollBack();
                        return Response::json(array('durum' => 0,'title' => 'Sayaç Hatası', 'text' => 'Girilen Sayacın Sistemdeki Yeri Farklı: ' . $serino1 . '->' . $kayitliyer->yeradi, 'type' => 'error'));
                    }
                }
                $abonesayac = AboneSayac::where('serino', $serino1)->first();
                if ($abonesayac) {
                    $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                    if ($abonetahsis) {
                        $abone = Abone::find($abonetahsis->abone_id);
                        DB::rollBack();
                        return Response::json(array('durum' => 0, 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: ' . $abone->abone_no, 'type' => 'error'));
                    } else {
                        $abonesayac->uretimyer_id=$uretimyer;
                        $abonesayac->sayactur_id=$eklisayaclar[$i]['sayactur'];
                        $abonesayac->sayacadi_id=$eklisayaclar[$i]['sayacadi'];
                        $abonesayac->sayaccap_id=$sayaccap;
                        $abonesayac->adres=BackendController::StrtoUpper($eklisayaclar[$i]['sayacadresi']);
                        $abonesayac->bilgi=$eklisayaclar[$i]['sayacbilgi'];
                        $abonesayac->iletisim=$eklisayaclar[$i]['sayaciletisim'];
                        $abonesayac->save();
                        array_push($abonesayaclist, $abonesayac->id);
                    }
                } else {
                    try{
                        $abonesayac = new AboneSayac;
                        $abonesayac->serino = $serino1;
                        $abonesayac->uretimyer_id = $uretimyer;
                        $abonesayac->sayactur_id = $eklisayaclar[$i]['sayactur'];
                        $abonesayac->sayacadi_id = $eklisayaclar[$i]['sayacadi'];
                        $abonesayac->sayaccap_id = $sayaccap;
                        $abonesayac->adres = BackendController::StrtoUpper($eklisayaclar[$i]['sayacadresi']);
                        $abonesayac->bilgi=$eklisayaclar[$i]['sayacbilgi'];
                        $abonesayac->iletisim=$eklisayaclar[$i]['sayaciletisim'];
                        $abonesayac->save();
                        array_push($abonesayaclist, $abonesayac->id);
                    }catch (Exception $e){
                        Log::error($e);
                        DB::rollBack();
                        return Response::json(array('durum' => 0,'title' => 'Abone Sayacı Kaydedilemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                    }
                }
            }
            try{
                $abone = new Abone;
                $abone->subekodu = $subekodu;
                $abone->netsiscari_id = $netsiscari;
                $abone->adisoyadi = BackendController::StrtoUpper($adisoyadi);
                $abone->vergidairesi = BackendController::StrtoUpper($vergidairesi);
                $abone->tckimlikno = $tckimlikno;
                $abone->abone_no = $aboneno;
                $abone->telefon = $telefon;
                $abone->email = $email=="" ? null : $email;
                $abone->faturaadresi = BackendController::StrtoUpper($adres);
                $abone->iller_id = $il;
                $abone->ilceler_id = $ilce;
                $abone->uretimyer_id = $uretimyer;
                $abone->netsiscari_id = $netsiscari;
                $abone->kullanici_id = Auth::user()->id;
                $abone->save();
            }catch(Exception $e){
                Log::error($e);
                DB::rollBack();
                return Response::json(array('durum' => 0,'title' => 'Abone Bilgisi Kaydedilemedi', 'text' => 'Abone Bilgisi Eksik Ya da Hatalı', 'type' => 'error'));
            }

            try {
                for ($i = 0; $i < count($abonesayaclist); $i++) {
                    $abonetahsis = new AboneTahsis;
                    $abonetahsis->abone_id = $abone->id;
                    $abonetahsis->abonesayac_id = $abonesayaclist[$i];
                    $abonetahsis->tahsistarihi = date('Y-m-d H:i:s');;
                    $abonetahsis->save();
                }
            }catch(Exception $e) {
                Log::error($e);
                DB::rollBack();
                return Response::json(array('durum' => 0,'title' => 'Abone Kayıdı Yapılamadı', 'text' => 'Abonenin Tahsisi Kaydedilemedi', 'type' => 'error'));
            }
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $adisoyadi . ' İsimli Abone Kayıdı Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Abone Numarası:' . $abone->id);
            DB::commit();
            $dbname = 'MANAS' . date('Y');
            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
            $efatura=BackendController::Efatura($dbname,$abone->netsiscari_id);
            If(Input::has('faturano')) {
                $eskifaturano = Input::get('faturano');
                if($efatura){ //efatura müşterisi ise
                    $param = EfaturaParam::where('SIRKET',$dbname)->where('SUBE_KODU',$subeyetkili->subekodu)->first();
                    if($param){
                        $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE',$param->TERCIH_EDILEN_SERI_NO.'%')->orderBy('FATIRS_NO','desc')->first();
                        if($fatura){
                            $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                        }else{
                            $fatirsno = BackendController::FaturaNo($param->TERCIH_EDILEN_SERI_NO.'0',0);
                        }
                    }else{
                        $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE','ME0%')->orderBy('FATIRS_NO','desc')->first();
                        if($fatura){
                            $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                        }else{
                            $fatirsno = BackendController::FaturaNo('ME0'.'0',0);
                        }
                    }
                    $faturaseri = mb_substr($fatirsno,0,1);
                    $faturano = Fatuno::where('SUBE_KODU',$subeyetkili->subekodu)->where('SERI',$faturaseri)->where('TIP','1')->first();
                    if(!$faturano){
                        $faturano = new Fatuno;
                        $faturano->SUBE_KODU = $subeyetkili->subekodu;
                        $faturano->SERI = $faturaseri;
                        $faturano->TIP = 1;
                        $faturano->NUMARA = $fatirsno;
                        $faturano->save();
                    }
                    if(mb_substr($eskifaturano, 0, 1)!=$faturaseri)
                        $fatirsno = BackendController::FaturaNo($fatirsno,1);
                    else
                        $fatirsno = $eskifaturano;
                }else{
                    $param = EarsivParam::where('SIRKET',$dbname)->where('SUBE_KODU',$subeyetkili->subekodu)->first();
                    if($param){
                        $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE',$param->TERCIH_EDILEN_SERI_NO.'%')->orderBy('FATIRS_NO','desc')->first();
                        if($fatura){
                            $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                        }else{
                            $fatirsno = BackendController::FaturaNo($param->TERCIH_EDILEN_SERI_NO.'0',0);
                        }
                    }else{
                        $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE','EA'.$subeyetkili->subekodu.'%')->orderBy('FATIRS_NO','desc')->first();
                        if($fatura){
                            $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                        }else{
                            $fatirsno = BackendController::FaturaNo('EA'.$subeyetkili->subekodu.'0',0);
                        }
                    }
                    $faturaseri = mb_substr($fatirsno,0,1);
                    $faturano = Fatuno::where('SUBE_KODU', $subeyetkili->subekodu)->where('SERI', $faturaseri)->where('TIP', '1')->first();
                    if (!$faturano) {
                        $faturano = new Fatuno;
                        $faturano->SUBE_KODU = $subeyetkili->subekodu;
                        $faturano->SERI = $faturaseri;
                        $faturano->TIP = 1;
                        $faturano->NUMARA = $fatirsno;
                        $faturano->save();
                    }
                    if(mb_substr($eskifaturano, 0, 1)!=$faturaseri)
                        $fatirsno = BackendController::FaturaNo($faturano->NUMARA,1);
                    else
                        $fatirsno = $eskifaturano;
                }
            }else{
                if($efatura){ //efatura müşterisi ise
                    $param = EfaturaParam::where('SIRKET',$dbname)->where('SUBE_KODU',$subeyetkili->subekodu)->first();
                    if($param){
                        $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE',$param->TERCIH_EDILEN_SERI_NO.'%')->orderBy('FATIRS_NO','desc')->first();
                        if($fatura){
                            $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                        }else{
                            $fatirsno = BackendController::FaturaNo($param->TERCIH_EDILEN_SERI_NO.'0',0);
                        }
                    }else{
                        $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE','ME0%')->orderBy('FATIRS_NO','desc')->first();
                        if($fatura){
                            $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                        }else{
                            $fatirsno = BackendController::FaturaNo('ME0'.'0',0);
                        }
                    }
                    $faturaseri = mb_substr($fatirsno,0,1);
                    $faturano = Fatuno::where('SUBE_KODU',$subeyetkili->subekodu)->where('SERI',$faturaseri)->where('TIP','1')->first();
                    if(!$faturano){
                        $faturano = new Fatuno;
                        $faturano->SUBE_KODU = $subeyetkili->subekodu;
                        $faturano->SERI = $faturaseri;
                        $faturano->TIP = 1;
                        $faturano->NUMARA = $fatirsno;
                        $faturano->save();
                    }
                    $fatirsno = BackendController::FaturaNo($fatirsno,1);
                }else{
                    $param = EarsivParam::where('SIRKET',$dbname)->where('SUBE_KODU',$subeyetkili->subekodu)->first();
                    if($param){
                        $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE',$param->TERCIH_EDILEN_SERI_NO.'%')->orderBy('FATIRS_NO','desc')->first();
                        if($fatura){
                            $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                        }else{
                            $fatirsno = BackendController::FaturaNo($param->TERCIH_EDILEN_SERI_NO.'0',0);
                        }
                    }else{
                        $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE','EA'.$subeyetkili->subekodu.'%')->orderBy('FATIRS_NO','desc')->first();
                        if($fatura){
                            $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                        }else{
                            $fatirsno = BackendController::FaturaNo('EA'.$subeyetkili->subekodu.'0',0);
                        }
                    }
                    $faturaseri = mb_substr($fatirsno,0,1);
                    $faturano = Fatuno::where('SUBE_KODU', $subeyetkili->subekodu)->where('SERI', $faturaseri)->where('TIP', '1')->first();
                    if (!$faturano) {
                        $faturano = new Fatuno;
                        $faturano->SUBE_KODU = $subeyetkili->subekodu;
                        $faturano->SERI = $faturaseri;
                        $faturano->TIP = 1;
                        $faturano->NUMARA = $fatirsno;
                        $faturano->save();
                    }
                    $fatirsno = BackendController::FaturaNo($faturano->NUMARA,1);
                }
            }
            $uretimyeri = UretimYer::find($uretimyer);
            return Response::json(array('durum' => 1,'abone'=>$abone,'faturano'=>$fatirsno,'uretimyer'=>$uretimyeri,'title' => 'Abone Kayıdı Yapıldı', 'text' => 'Abone Kayıdı Başarıyla Yapıldı', 'type' => 'success'));
        }catch(Exception $e){
            Db::rollBack();
            Log::error($e);
            return Response::json(array('durum' => 0,'title' => 'Abone Eklenirken Hata Oluştu', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getSatisabonebilgi(){
        try {
            $tip = Input::get('tip');
            $kriter = Input::get('kriter');
            $subekodu = Input::get('subekodu');
            If(Input::has('faturano')){
                $eskifaturano = Input::get('faturano');
                if ($subekodu != 1) {
                    switch ($tip) {
                        case 1: // seri numarası
                            $abonebilgi = Abone::where('abonesayac.serino', 'LIKE', '%' . $kriter . '%')->where('abone.subekodu', $subekodu)->whereNull('abonetahsis.deleted_at')
                                ->select(array("abone.id","abonetahsis.id as tahsisid","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                                    "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abone.netsiscari_id"))
                                ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")
                                ->leftjoin("abonetahsis", "abonetahsis.abone_id", "=", "abone.id")
                                ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")->get();
                            break;
                        case 2: // Adı Soyadı
                            $kriter = BackendController::StrNormalized($kriter);
                            $abonebilgi = Abone::where('abone.nadisoyadi', 'LIKE', '%' . $kriter . '%')->where('abone.subekodu', $subekodu)->whereNull('abonetahsis.deleted_at')
                                ->select(array("abone.id","abonetahsis.id as tahsisid","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                                    "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abone.netsiscari_id"))
                                ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")
                                ->leftjoin("abonetahsis", "abonetahsis.abone_id", "=", "abone.id")
                                ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")->get();
                            break;
                        case 3: // TC No
                            $abonebilgi = Abone::where('abone.tckimlikno', 'LIKE', '%' . $kriter . '%')->where('abone.subekodu', $subekodu)->whereNull('abonetahsis.deleted_at')
                                ->select(array("abone.id","abonetahsis.id as tahsisid","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                                    "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abone.netsiscari_id"))
                                ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")
                                ->leftjoin("abonetahsis", "abonetahsis.abone_id", "=", "abone.id")
                                ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")->get();
                            break;
                        case 4: // Telefon
                            $abonebilgi = Abone::where('abone.telefon', 'LIKE', '%' . $kriter . '%')->where('abone.subekodu', $subekodu)->whereNull('abonetahsis.deleted_at')
                                ->select(array("abone.id","abonetahsis.id as tahsisid","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                                    "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abone.netsiscari_id"))
                                ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")
                                ->leftjoin("abonetahsis", "abonetahsis.abone_id", "=", "abone.id")
                                ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")->get();
                            break;
                        default: // seri numarası
                            $abonebilgi = Abone::where('abonesayac.serino', 'LIKE', '%' . $kriter . '%')->where('abone.subekodu', $subekodu)->whereNull('abonetahsis.deleted_at')
                                ->select(array("abone.id","abonetahsis.id as tahsisid","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                                    "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abone.netsiscari_id"))
                                ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")
                                ->leftjoin("abonetahsis", "abonetahsis.abone_id", "=", "abone.id")
                                ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")->get();
                            break;
                    }
                    if ($abonebilgi->count() > 0) {
                        $dbname = 'MANAS' . date('Y');
                        $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
                        $efatura=BackendController::Efatura($dbname,$abonebilgi[0]->netsiscari_id);
                        if($efatura){ //efatura müşterisi ise
                            $param = EfaturaParam::where('SIRKET',$dbname)->where('SUBE_KODU',$subeyetkili->subekodu)->first();
                            if($param){
                                $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE',$param->TERCIH_EDILEN_SERI_NO.'%')->orderBy('FATIRS_NO','desc')->first();
                                if($fatura){
                                    $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                                }else{
                                    $fatirsno = BackendController::FaturaNo($param->TERCIH_EDILEN_SERI_NO.'0',0);
                                }
                            }else{
                                $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE','ME0%')->orderBy('FATIRS_NO','desc')->first();
                                if($fatura){
                                    $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                                }else{
                                    $fatirsno = BackendController::FaturaNo('ME0'.'0',0);
                                }
                            }
                            $faturaseri = mb_substr($fatirsno, 0, 1);
                            $faturano = Fatuno::where('SUBE_KODU',$subeyetkili->subekodu)->where('SERI',$faturaseri)->where('TIP','1')->first();
                            if(!$faturano){
                                $faturano = new Fatuno;
                                $faturano->SUBE_KODU = $subeyetkili->subekodu;
                                $faturano->SERI = $faturaseri;
                                $faturano->TIP = 1;
                                $faturano->NUMARA = $fatirsno;
                                $faturano->save();
                            }
                            if(mb_substr($eskifaturano, 0, 1)!= $faturaseri)
                                $fatirsno = BackendController::FaturaNo($fatirsno,1);
                            else
                                $fatirsno = $eskifaturano;
                        }else{
                            $param = EarsivParam::where('SIRKET', $dbname)->where('SUBE_KODU', $subeyetkili->subekodu)->first();
                            if($param){
                                $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE',$param->TERCIH_EDILEN_SERI_NO . '%')->orderBy('FATIRS_NO', 'desc')->first();
                                if ($fatura) {
                                    $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO, 0);
                                } else {
                                    $fatirsno = BackendController::FaturaNo($param->TERCIH_EDILEN_SERI_NO . '0', 0);
                                }
                            } else {
                                $fatura = Fatuirs::where('FTIRSIP', 1)->where('FATIRS_NO', 'LIKE', 'EA' . $subeyetkili->subekodu . '%')->orderBy('FATIRS_NO', 'desc')->first();
                                if ($fatura) {
                                    $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO, 0);
                                } else {
                                    $fatirsno = BackendController::FaturaNo('EA' . $subeyetkili->subekodu . '0', 0);
                                }
                            }
                            $faturaseri = mb_substr($fatirsno, 0, 1);
                            $faturano = Fatuno::where('SUBE_KODU', $subeyetkili->subekodu)->where('SERI', $faturaseri)->where('TIP', '1')->first();
                            if (!$faturano) {
                                $faturano = new Fatuno;
                                $faturano->SUBE_KODU = $subeyetkili->subekodu;
                                $faturano->SERI = $faturaseri;
                                $faturano->TIP = 1;
                                $faturano->NUMARA = $fatirsno;
                                $faturano->save();
                            }
                            if(mb_substr($eskifaturano, 0, 1)!= $faturaseri)
                                $fatirsno = BackendController::FaturaNo($faturano->NUMARA,1);
                            else
                                $fatirsno = $eskifaturano;
                        }
                        $abonebilgi[0]->faturano=$fatirsno;
                        return array("durum" => true, "count" => $abonebilgi->count(), "abonebilgi" => $abonebilgi);
                    } else {
                        return array("durum" => false, "type" => "error", "title" => "Abone Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Abone Bilgisi Bulunamadı.");
                    }
                } else {
                    return array("durum" => false, "type" => "error", "title" => "Şube Bilgisi Bulunamadı", "text" => "Şubeye Ait Abone Bilgisi Mevcut Değil!");
                }
            }else{
                if ($subekodu != 1) {
                    switch ($tip) {
                        case 1: // seri numarası
                            $abonebilgi = Abone::where('abonesayac.serino', 'LIKE', '%' . $kriter . '%')->where('abone.subekodu', $subekodu)->whereNull('abonetahsis.deleted_at')
                                ->select(array("abone.id","abonetahsis.id as tahsisid","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                                    "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abone.netsiscari_id"))
                                ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")
                                ->leftjoin("abonetahsis", "abonetahsis.abone_id", "=", "abone.id")
                                ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")->get();
                            break;
                        case 2: // Adı Soyadı
                            $kriter = BackendController::StrNormalized($kriter);
                            $abonebilgi = Abone::where('abone.nadisoyadi', 'LIKE', '%' . $kriter . '%')->where('abone.subekodu', $subekodu)->whereNull('abonetahsis.deleted_at')
                                ->select(array("abone.id","abonetahsis.id as tahsisid","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                                    "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abone.netsiscari_id"))
                                ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")
                                ->leftjoin("abonetahsis", "abonetahsis.abone_id", "=", "abone.id")
                                ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")->get();
                            break;
                        case 3: // TC No
                            $abonebilgi = Abone::where('abone.tckimlikno', 'LIKE', '%' . $kriter . '%')->where('abone.subekodu', $subekodu)->whereNull('abonetahsis.deleted_at')
                                ->select(array("abone.id","abonetahsis.id as tahsisid","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                                    "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abone.netsiscari_id"))
                                ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")
                                ->leftjoin("abonetahsis", "abonetahsis.abone_id", "=", "abone.id")
                                ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")->get();
                            break;
                        case 4: // Telefon
                            $abonebilgi = Abone::where('abone.telefon', 'LIKE', '%' . $kriter . '%')->where('abone.subekodu', $subekodu)->whereNull('abonetahsis.deleted_at')
                                ->select(array("abone.id","abonetahsis.id as tahsisid","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                                    "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abone.netsiscari_id"))
                                ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")
                                ->leftjoin("abonetahsis", "abonetahsis.abone_id", "=", "abone.id")
                                ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")->get();
                            break;
                        default: // seri numarası
                            $abonebilgi = Abone::where('abonesayac.serino', 'LIKE', '%' . $kriter . '%')->where('abone.subekodu', $subekodu)->whereNull('abonetahsis.deleted_at')
                                ->select(array("abone.id","abonetahsis.id as tahsisid","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                                    "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abone.netsiscari_id"))
                                ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")
                                ->leftjoin("abonetahsis", "abonetahsis.abone_id", "=", "abone.id")
                                ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")->get();
                            break;
                    }
                    if ($abonebilgi->count() > 0) {
                        $dbname = 'MANAS' . date('Y');
                        $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
                        $efatura=BackendController::Efatura($dbname,$abonebilgi[0]->netsiscari_id);
                        if($efatura){ //efatura müşterisi ise
                            $param = EfaturaParam::where('SIRKET',$dbname)->where('SUBE_KODU',$subeyetkili->subekodu)->first();
                            if($param){
                                $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE',$param->TERCIH_EDILEN_SERI_NO.'%')->orderBy('FATIRS_NO','desc')->first();
                                if($fatura){
                                    $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                                }else{
                                    $fatirsno = BackendController::FaturaNo($param->TERCIH_EDILEN_SERI_NO.'0',0);
                                }
                            }else{
                                $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE','ME0%')->orderBy('FATIRS_NO','desc')->first();
                                if($fatura){
                                    $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                                }else{
                                    $fatirsno = BackendController::FaturaNo('ME0'.'0',0);
                                }
                            }
                            $faturaseri = mb_substr($fatirsno, 0, 1);
                            $faturano = Fatuno::where('SUBE_KODU',$subeyetkili->subekodu)->where('SERI',$faturaseri)->where('TIP','1')->first();
                            if(!$faturano){
                                $faturano = new Fatuno;
                                $faturano->SUBE_KODU = $subeyetkili->subekodu;
                                $faturano->SERI = $faturaseri;
                                $faturano->TIP = 1;
                                $faturano->NUMARA = $fatirsno;
                                $faturano->save();
                            }
                            $fatirsno = BackendController::FaturaNo($fatirsno,1);
                        }else{
                            $param = EarsivParam::where('SIRKET',$dbname)->where('SUBE_KODU',$subeyetkili->subekodu)->first();
                            if($param){
                                $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE',$param->TERCIH_EDILEN_SERI_NO.'%')->orderBy('FATIRS_NO','desc')->first();
                                if($fatura){
                                    $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                                } else {
                                    $fatirsno = BackendController::FaturaNo($param->TERCIH_EDILEN_SERI_NO.'0',0);
                                }
                            }else{
                                $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE','EA'.$subeyetkili->subekodu.'%')->orderBy('FATIRS_NO','desc')->first();
                                if($fatura){
                                    $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                                }else{
                                    $fatirsno = BackendController::FaturaNo('EA'.$subeyetkili->subekodu.'0',0);
                                }
                            }
                            $faturaseri = mb_substr($fatirsno, 0, 1);
                            $faturano = Fatuno::where('SUBE_KODU', $subeyetkili->subekodu)->where('SERI', $faturaseri)->where('TIP', '1')->first();
                            if (!$faturano) {
                                $faturano = new Fatuno;
                                $faturano->SUBE_KODU = $subeyetkili->subekodu;
                                $faturano->SERI = $faturaseri;
                                $faturano->TIP = 1;
                                $faturano->NUMARA = $fatirsno;
                                $faturano->save();
                            }
                            $fatirsno = BackendController::FaturaNo($faturano->NUMARA,1);
                        }
                        $abonebilgi[0]->faturano=$fatirsno;
                        return array("durum" => true, "count" => $abonebilgi->count(), "abonebilgi" => $abonebilgi);
                    } else {
                        return array("durum" => false, "type" => "error", "title" => "Abone Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Abone Bilgisi Bulunamadı.");
                    }
                } else {
                    return array("durum" => false, "type" => "error", "title" => "Şube Bilgisi Bulunamadı", "text" => "Şubeye Ait Abone Bilgisi Mevcut Değil!");
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title' => 'Abone Sayaç Bilgilerinde Hata Var!', 'text' => str_replace("'","\'",$e->getMessage()), 'type'=>'warning'));
        }
    }

    public function getSatislistebilgigetir()
    {
        $id = Input::get('id');
        $subekodu = Input::get('subekodu');
        If(Input::has('faturano')) {
            $eskifaturano = Input::get('faturano');
            if($subekodu!=1){
                $abonebilgi = Abone::where('abone.id', $id)->where('abone.subekodu', $subekodu)
                    ->select(array("abone.id","abonetahsis.id as tahsis","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                        "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abone.netsiscari_id"))
                    ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")
                    ->leftjoin("abonetahsis", "abonetahsis.abone_id", "=", "abone.id")
                    ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")->first();
                if($abonebilgi){
                    $dbname = 'MANAS' . date('Y');
                    $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
                    $efatura=BackendController::Efatura($dbname,$abonebilgi->netsiscari_id);
                    if($efatura){ //efatura müşterisi ise
                        $param = EfaturaParam::where('SIRKET',$dbname)->where('SUBE_KODU',$subeyetkili->subekodu)->first();
                        if($param){
                            $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE',$param->TERCIH_EDILEN_SERI_NO.'%')->orderBy('FATIRS_NO','desc')->first();
                            if($fatura){
                                $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                            }else{
                                $fatirsno = BackendController::FaturaNo($param->TERCIH_EDILEN_SERI_NO.'0',0);
                            }
                        }else{
                            $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE','ME0%')->orderBy('FATIRS_NO','desc')->first();
                            if($fatura){
                                $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                            }else{
                                $fatirsno = BackendController::FaturaNo('ME0'.'0',0);
                            }
                        }
                        $faturaseri = mb_substr($fatirsno, 0, 1);
                        $faturano = Fatuno::where('SUBE_KODU',$subeyetkili->subekodu)->where('SERI',$faturaseri)->where('TIP','1')->first();
                        if(!$faturano){
                            $faturano = new Fatuno;
                            $faturano->SUBE_KODU = $subeyetkili->subekodu;
                            $faturano->SERI = $faturaseri;
                            $faturano->TIP = 1;
                            $faturano->NUMARA = $fatirsno;
                            $faturano->save();
                        }
                        if(mb_substr($eskifaturano, 0, 1)!= $faturaseri)
                            $fatirsno = BackendController::FaturaNo($fatirsno,1);
                        else
                            $fatirsno = $eskifaturano;
                    }else{
                        $param = EarsivParam::where('SIRKET', $dbname)->where('SUBE_KODU', $subeyetkili->subekodu)->first();
                        if($param){
                            $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE',$param->TERCIH_EDILEN_SERI_NO . '%')->orderBy('FATIRS_NO', 'desc')->first();
                            if ($fatura) {
                                $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO, 0);
                            } else {
                                $fatirsno = BackendController::FaturaNo($param->TERCIH_EDILEN_SERI_NO . '0', 0);
                            }
                        } else {
                            $fatura = Fatuirs::where('FTIRSIP', 1)->where('FATIRS_NO', 'LIKE', 'EA' . $subeyetkili->subekodu . '%')->orderBy('FATIRS_NO', 'desc')->first();
                            if ($fatura) {
                                $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO, 0);
                            } else {
                                $fatirsno = BackendController::FaturaNo('EA' . $subeyetkili->subekodu . '0', 0);
                            }
                        }
                        $faturaseri = mb_substr($fatirsno, 0, 1);
                        $faturano = Fatuno::where('SUBE_KODU', $subeyetkili->subekodu)->where('SERI', $faturaseri)->where('TIP', '1')->first();
                        if (!$faturano) {
                            $faturano = new Fatuno;
                            $faturano->SUBE_KODU = $subeyetkili->subekodu;
                            $faturano->SERI = $faturaseri;
                            $faturano->TIP = 1;
                            $faturano->NUMARA = $fatirsno;
                            $faturano->save();
                        }
                        if(mb_substr($eskifaturano, 0, 1)!= $faturaseri)
                            $fatirsno = BackendController::FaturaNo($faturano->NUMARA,1);
                        else
                            $fatirsno = $eskifaturano;
                    }
                    $abonebilgi->faturano = $fatirsno;
                    return array("durum" => true,"abonebilgi"=>$abonebilgi);
                }else{
                    return array("durum" => false, "type" => "error", "title" => "Abone Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Abone Bilgisi Bulunamadı.");
                }
            }else {
                return array("durum" => false, "type" => "error", "title" => "Şube Bilgisi Bulunamadı", "text" => "Şubeye Ait Abone Bilgisi Mevcut Değil!");
            }
        }else{
            if($subekodu!=1){
                $abonebilgi = Abone::where('abone.id', $id)->where('abone.subekodu', $subekodu)
                    ->select(array("abone.id","abonetahsis.id as tahsis","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                        "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abone.netsiscari_id"))
                    ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")
                    ->leftjoin("abonetahsis", "abonetahsis.abone_id", "=", "abone.id")
                    ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")->first();
                if($abonebilgi){
                    $dbname = 'MANAS' . date('Y');
                    $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
                    $efatura=BackendController::Efatura($dbname,$abonebilgi->netsiscari_id);
                    if($efatura){ //efatura müşterisi ise
                        $param = EfaturaParam::where('SIRKET',$dbname)->where('SUBE_KODU',$subeyetkili->subekodu)->first();
                        if($param){
                            $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE',$param->TERCIH_EDILEN_SERI_NO.'%')->orderBy('FATIRS_NO','desc')->first();
                            if($fatura){
                                $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                            }else{
                                $fatirsno = BackendController::FaturaNo($param->TERCIH_EDILEN_SERI_NO.'0',0);
                            }
                        }else{
                            $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE','ME0%')->orderBy('FATIRS_NO','desc')->first();
                            if($fatura){
                                $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                            }else{
                                $fatirsno = BackendController::FaturaNo('ME0'.'0',0);
                            }
                        }
                        $faturaseri = mb_substr($fatirsno, 0, 1);
                        $faturano = Fatuno::where('SUBE_KODU',$subeyetkili->subekodu)->where('SERI',$faturaseri)->where('TIP','1')->first();
                        if(!$faturano){
                            $faturano = new Fatuno;
                            $faturano->SUBE_KODU = $subeyetkili->subekodu;
                            $faturano->SERI = $faturaseri;
                            $faturano->TIP = 1;
                            $faturano->NUMARA = $fatirsno;
                            $faturano->save();
                        }
                        $fatirsno = BackendController::FaturaNo($fatirsno,1);
                    }else{
                        $param = EarsivParam::where('SIRKET',$dbname)->where('SUBE_KODU',$subeyetkili->subekodu)->first();
                        if($param){
                            $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE',$param->TERCIH_EDILEN_SERI_NO.'%')->orderBy('FATIRS_NO','desc')->first();
                            if($fatura){
                                $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                            } else {
                                $fatirsno = BackendController::FaturaNo($param->TERCIH_EDILEN_SERI_NO.'0',0);
                            }
                        }else{
                            $fatura = Fatuirs::where('FTIRSIP',1)->where('FATIRS_NO','LIKE','EA'.$subeyetkili->subekodu.'%')->orderBy('FATIRS_NO','desc')->first();
                            if($fatura){
                                $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO,0);
                            }else{
                                $fatirsno = BackendController::FaturaNo('EA'.$subeyetkili->subekodu.'0',0);
                            }
                        }
                        $faturaseri = mb_substr($fatirsno, 0, 1);
                        $faturano = Fatuno::where('SUBE_KODU', $subeyetkili->subekodu)->where('SERI', $faturaseri)->where('TIP', '1')->first();
                        if(!$faturano){
                            $faturano = new Fatuno;
                            $faturano->SUBE_KODU = $subeyetkili->subekodu;
                            $faturano->SERI = $faturaseri;
                            $faturano->TIP = 1;
                            $faturano->NUMARA = $fatirsno;
                            $faturano->save();
                        }
                        $fatirsno = BackendController::FaturaNo($faturano->NUMARA,1);
                    }
                    $abonebilgi->faturano = $fatirsno;
                    return array("durum" => true,"abonebilgi"=>$abonebilgi);
                }else{
                    return array("durum" => false, "type" => "error", "title" => "Abone Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Abone Bilgisi Bulunamadı.");
                }
            }else {
                return array("durum" => false, "type" => "error", "title" => "Şube Bilgisi Bulunamadı", "text" => "Şubeye Ait Abone Bilgisi Mevcut Değil!");
            }
        }
    }

    public function getSayacsatis() {
        return View::make($this->servisadi.'.sayacsatis')->with(array('title'=>'Satış Kayıdı'));
    }

    public function postSayacsatislist() {
        $netsiscari_id=Input::get('netsiscari_id');
        $sube = null;
        if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $sube=Sube::whereIn('netsiscari_id',$netsiscarilist)->where('aktif',1)->first();
            if($sube){
                $query = SubeSayacSatis::where('subesayacsatis.subekodu',$sube->subekodu)
                    ->select(array("subesayacsatis.id","abone.adisoyadi","abone.tckimlikno","uretimyer.yeradi","subesayacsatis.toplamtutar","subesayacsatis.gdurum",
                        "subesayacsatis.faturatarihi","subesayacsatis.gfaturatarihi","abone.nadisoyadi","uretimyer.nyeradi","subesayacsatis.ndurum","subesayacsatis.faturano","parabirimi.birimi"))
                    ->leftjoin("uretimyer", "subesayacsatis.uretimyer_id", "=", "uretimyer.id")
                    ->leftjoin("abone", "subesayacsatis.abone_id", "=", "abone.id")
                    ->leftjoin("parabirimi", "subesayacsatis.parabirimi_id", "=", "parabirimi.id");
            }else{
                $query = SubeSayacSatis::select(array("subesayacsatis.id","abone.adisoyadi","abone.tckimlikno","uretimyer.yeradi","subesayacsatis.toplamtutar","subesayacsatis.gdurum",
                        "subesayacsatis.faturatarihi","subesayacsatis.gfaturatarihi","abone.nadisoyadi","uretimyer.nyeradi","subesayacsatis.ndurum","subesayacsatis.faturano","parabirimi.birimi"))
                    ->leftjoin("uretimyer", "subesayacsatis.uretimyer_id", "=", "uretimyer.id")
                    ->leftjoin("abone", "subesayacsatis.abone_id", "=", "abone.id")
                    ->leftjoin("parabirimi", "subesayacsatis.parabirimi_id", "=", "parabirimi.id");
            }
        }else{
            $query = SubeSayacSatis::select(array("subesayacsatis.id","abone.adisoyadi","abone.tckimlikno","uretimyer.yeradi","subesayacsatis.toplamtutar","subesayacsatis.gdurum",
                "subesayacsatis.faturatarihi","subesayacsatis.gfaturatarihi","abone.nadisoyadi","uretimyer.nyeradi","subesayacsatis.ndurum","subesayacsatis.faturano","parabirimi.birimi"))
                ->leftjoin("uretimyer", "subesayacsatis.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("abone", "subesayacsatis.abone_id", "=", "abone.id")
                ->leftjoin("parabirimi", "subesayacsatis.parabirimi_id", "=", "parabirimi.id");
        }

        return Datatables::of($query)
            ->editColumn('toplamtutar', function ($model) {
                return ($model->toplamtutar==0 ? '0.00' : $model->toplamtutar) .' '.$model->birimi;
            })
            ->editColumn('faturatarihi', function ($model) {
                if($model->faturatarihi){
                    $date = new DateTime($model->faturatarihi);
                    return $date->format('d-m-Y');
                }else{
                    return '';
                }
            })
            ->addColumn('islemler',function ($model) use($netsiscari_id,$sube) {
                $root = BackendController::getRootDizin();
                if($netsiscari_id && $sube)
                    if($model->gdurum=="Bekliyor")
                        return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/sayacsatisduzenle/".$model->id."' > Düzenle </a>
                        <a href='#portlet-delete' data-toggle='modal' data-id='{$model->id}' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                    else if($model->gdurum=="Teslim Edildi" && (time()-strtotime($model->faturatarihi))<432000 )
                        return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/sayacsatisduzenle/".$model->id."' > Düzenle </a>
                        <a href='#portlet-delete' data-toggle='modal' data-id='{$model->id}' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                    else
                        return "<a class='btn btn-sm btn-info' href='".$root."/".$this->servisadi."/sayacsatisgoster/".$model->id."' > Göster </a>";
                else
                    return "<a class='btn btn-sm btn-info' href='".$root."/".$this->servisadi."/sayacsatisgoster/".$model->id."' > Göster </a>";
            })
            ->make(true);
    }

    public function getSayacsatisekle() {
        $netsiscariid=Auth::user()->netsiscari_id;
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        $netsiscariler=NetsisCari::whereIn('id',Auth::user()->netsiscari_id)->get(array('id'))->toArray();
        $uretimyerid=CariYer::whereIn('netsiscari_id',$netsiscariler)->where('durum',1)->get(array('uretimyer_id'))->toArray();
        $uretimyerleri = UretimYer::whereIn('id',$uretimyerid)->get();
        if($sube){
            $sube->netsiscari = NetsisCari::find($sube->netsiscari_id);
            $sube->depo = NetsisDepolar::find($sube->netsisdepolar_id);
            $iller = Iller::all();
            $ilceler = Ilceler::where('iller_id',$sube->iller_id)->get();
            $subeyetkili=SubeYetkili::where('kullanici_id',Auth::user()->id)->where('aktif',1)->get(array('netsiscari_id'))->toArray();
            $netsiscariler = NetsisCari::where('caridurum','A')->whereIn('caritipi',array('A','D'))
                ->where(function($query)use($subeyetkili,$sube){$query->whereIn('id',$subeyetkili)->orwhereIn('subekodu',array(-1,$sube->subekodu));})
                ->whereNotIn('carikod',(function ($query) use ($sube) {$query->select('carikod')->from('kodharichar')->where('subekodu', $sube->subekodu);}))
                ->whereNotIn('id',array($sube->netsiscari_id))
                ->orderBy('cariadi','asc')->get();
            foreach ($netsiscariler as $netsiscari){
                $netsiscari->efatura=BackendController::Efatura('MANAS' . date('Y'),$netsiscari->id);
            }
            $urunler=SubeUrun::where('subekodu',$sube->subekodu)->where('durum',1)->get();
            foreach($urunler as $urun){
                $urun->netsisstokkod = NetsisStokKod::find($urun->netsisstokkod_id);
                $urun->parabirimi=Parabirimi::find($urun->parabirimi_id);
                $urun->stok=StBakiye::where('STOK_KODU',$urun->netsisstokkod->kodu)->where('DEPO_KODU',$urun->depokodu)->first();
                if(!$urun->stok)
                    $urun->stok=new StBakiye(array('BAKIYE'=>0));
            }
            $parabirimi = ParaBirimi::find(1);
            $dovizkuru = DovizKuru::orderBy('tarih','desc')->orderBy('parabirimi_id','asc')->take(3)->get();
            foreach($dovizkuru as $doviz){
                $doviz->tarih=date("d-m-Y",strtotime ($doviz->tarih));
            }
            $sayacturlist=explode(',',$sube->sayactur);
            $sayacadilist=explode(',',$sube->sayacadlari);
            $sayacturleri = SayacTur::whereIn('id',$sayacturlist)->get();
            $sayacadlari = SayacAdi::whereIn('sayactur_id',$sayacturlist)->whereIn('id',$sayacadilist)->get();
            $sayaccaplari = SayacCap::all();
            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
            $kasakodlar = KasaKod::where('subekodu', $subeyetkili->subekodu)->get();
            return View::make($this->servisadi.'.sayacsatisekle',array('netsiscariler'=>$netsiscariler,'urunler'=>$urunler,'kasakodlar'=>$kasakodlar,'iller'=>$iller,'ilceler'=>$ilceler,'parabirimi'=>$parabirimi,'dovizkuru'=>$dovizkuru,'sube'=>$sube,'sayacturleri'=>$sayacturleri,'sayacadlari'=>$sayacadlari,'sayaccaplari'=>$sayaccaplari,'uretimyerleri'=>$uretimyerleri))->with(array('title'=>'Satış Kayıdı Ekle'));
        }else{
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkisiz', 'text' => 'Fatura Ekleme Yetkiniz Yok', 'type' => 'error'));
        }
    }

    public function postSayacsatisekle(){
        try{
            if (Input::has('faturavar')) {
                if(Input::get('odemetipi')==5 || Input::get('odemetipi')==7)
                    $rules = ['cariadi'=>'required','abone'=>'required','tckimlikno'=>'required','telefon'=>'required|min:10','tarih'=>'required','adres'=>'required','faturail'=>'required','faturailce'=>'required','faturano' => 'required', 'aciklama' => 'required', 'odemesekli' => 'required'];
                else
                    $rules = ['cariadi'=>'required','abone'=>'required','tckimlikno'=>'required','telefon'=>'required|min:10','tarih'=>'required','adres'=>'required','faturail'=>'required','faturailce'=>'required','faturano' => 'required', 'aciklama' => 'required', 'odemesekli' => 'required', 'kasakod' => 'required'];
                $validate = Validator::make(Input::all(), $rules);
                $messages = $validate->messages();
                if ($validate->fails()) {
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
                }
                $fatura = 1;
            } else {
                $fatura = 0;
            }
            $dbname = 'MANAS' . date('Y');
            $aboneid = Input::get('abone');
            $subekodu = Input::get('subekodu');
            if($subekodu==""){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Şube Hatası', 'text' => 'Kullanıcıya Ait Aktif Bir Şube Tanımlı Değil!', 'type' => 'error'));
            }
            $sube = Sube::where('subekodu',$subekodu)->where('aktif',1)->first();
            if($sube) {
                $sube->depo = NetsisDepolar::find($sube->netsisdepolar_id);
            }else{
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Şube Hatası', 'text' => 'Şube Bilgisi Bulunamadı!', 'type' => 'error'));
            }
            $netsiscariid = Input::get('cariadi');
            $adres = Input::get('adres');
            $tckimlikno = Input::get('tckimlikno');
            $telefon = Input::get('telefon');
            $kalemadet = Input::get('count');
            $urun = Input::get('urunadi');
            $birimfiyat = Input::get('fiyat');
            $gfiyat = Input::get('gfiyat');
            $miktar = Input::get('miktar');
            $ucretsizler = Input::get('ucretsizler');
            $tutar = Input::get('tutar');
            $kdv = Input::get('kdvtutar');
            $toplamtutar = Input::get('toplamtutar');
            $parabirimi = Input::get('parabirimi');
            $kurtarih = date('Y-m-d',strtotime(Input::get('kurtarih')));
            $faturatarihi = date('Y-m-d',strtotime(Input::get('tarih')));
            $baglantidurum=Input::get('baglantidurum');
            $baglanti=0;
            $kdv_orani=SistemSabitleri::where('adi','KdvOrani')->first();
            $faturaadresi=Input::get('faturaadresi');
            $faturail=Input::get('faturail');
            $faturailce=Input::get('faturailce');
            $odemesekli=Input::get('odemesekli');
            $odemetipi=Input::get('odemetipi');
            $taksit=Input::get('taksit');
            $taksit2=Input::get('taksit2');
            $nakit = floatval(Input::get('nakit'));
            $kredikart = floatval(Input::get('kredikart'));
            $kredikart1 = floatval(Input::get('kredikart1'));
            $kredikart2 = floatval(Input::get('kredikart2'));
            $aciklama=Input::get('aciklama');
            $odemeyapan=Input::get('odemeyapan');
            $telefon = preg_replace('/\D/','',$telefon);
            $telefon = mb_substr($telefon, 0, 4)." ".mb_substr($telefon, 4, 3)." ".mb_substr($telefon, 7, 2)." ".mb_substr($telefon, 9, 2);
            $tumsayac=array();
            if(Input::has('abonesayac'))
                $abonesayaclari=Input::get('abonesayac');
            else
                $abonesayaclari=array();
            $abonesayaci="";
            $ucretsizlist = explode(',',$ucretsizler);
            for($i=0;$i<count($birimfiyat);$i++){
                if($ucretsizlist[$i]==0){
                    if(floatval($birimfiyat[$i]==0)){
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Birim Fiyat Sıfır Girilemez! Ücretsiz olacaksa ücretsiz kısmı işaretlenmelidir. ', 'type' => 'error'));
                    }
                }
            }
            $abone = Abone::find($aboneid);
            $netsiscari = NetsisCari::find($netsiscariid);
            $faturano = Input::get('faturano');

            //$fatirsno = BackendController::FaturaNo($faturano, 0);
            if ($netsiscari->caridurum != "A")
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
            if (!$subeyetkili) {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube Yetkili Uyarısı', 'text' => 'Bu Yere Ait Şube Yetkilisi Yok.', 'type' => 'warning'));
            }
            $yetkili = ServisYetkili::where('kullanici_id', $subeyetkili->kullanici_id)->first();
            if (!$yetkili) {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetki Uyarısı', 'text' => 'Bu Kullanıcının Yetkisi Yok.', 'type' => 'warning'));
            }
            $fatirsno = BackendController::FaturaNo($faturano,0);
            if(Input::has('kasakod')){
                $kasakodu = Input::get('kasakod');
            }else{
                $kasakod = KasaKod::where('subekodu', $subeyetkili->subekodu)->orderBy('kasaadi','asc')->first();
                $kasakodu = $kasakod->kasakod;
            }
            $kasakodu2 = Input::get('kasakod2');
            DB::beginTransaction();
            try {
                for ($i = 0; $i < $kalemadet; $i++) {
                    if ($baglantidurum[$i]) {
                        $baglanti = 1;
                        if (isset($abonesayaclari[$i])) {
                            if(AboneSayac::where('id',$abonesayaclari[$i])->where('satisdurum',1)->first())
                            {
                                DB::rollBack();
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' =>'Satış Kayıdı Hatalı!', 'text' => 'Ekteki Sayaçların Zaten Satışı Yapılmış!', 'type' => 'error'));
                            }
                            $ek = "";
                            foreach ($abonesayaclari[$i] as $sayaci) {
                                if (in_array($sayaci, $tumsayac)) {
                                    foreach ($tumsayac as $sayac) {
                                        AboneSayac::where('id', $sayac)->update(['satisdurum' => 0]);
                                    }
                                    DB::rollBack();
                                    Input::flash();
                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Bağlantılı Sayaçlar Hatalı!', 'text' => 'Aynı Seri Numarası Farklı kalemlerde Mevcut!', 'type' => 'error'));
                                } else {
                                    array_push($tumsayac, $sayaci);
                                }
                                $ek .= ($ek == "" ? "" : ",") . $sayaci;
                                AboneSayac::where('id', $sayaci)->update(['satisdurum' => 1]);
                            }
                            $abonesayaci .= ($abonesayaci == "" ? "" : ";") . $ek;
                        } else {
                            DB::rollBack();
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Bağlantılı Sayaçlar Eksik!', 'text' => 'Kalem Olarak Eklenen Sayaçların Seri Numaraları Girilmemiş ya da Eksik!', 'type' => 'error'));
                        }
                    } else {
                        $abonesayaci .= ($abonesayaci == "" ? "" : ";") . '0';
                    }
                }
            } catch (Exception $e) {
                Log::error($e);
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Bağlantılı Sayaçlar Eksik!', 'text' => 'Kalem Olarak Eklenen Sayaçlarda Hata Var!', 'type' => 'error'));
            }
            $secilenler="";
            $miktarlar="";
            $birimfiyatlar="";
            $gfiyatlar="";
            $baglantililar=0;
            try {
                for ($i = 0; $i < $kalemadet; $i++) {
                    if ($urun[$i] != "") {
                        $subeurun = SubeUrun::find($urun[$i]);
                        if ($subeurun->baglanti) {
                            $baglantililar += $miktar[$i];
                        }
                        if($subeurun->kontrol){
                            $subeurun->netsisstokkod = NetsisStokKod::find($subeurun->netsisstokkod_id);
                            $stok=StBakiye::where('STOK_KODU',$subeurun->netsisstokkod->kodu)->where('DEPO_KODU',$sube->depo->kodu)->first();
                            if(!$stok)
                                $stok=new StBakiye(array('BAKIYE'=>0));
                            if(intval($stok->BAKIYE)<$miktar[$i]){
                                DB::rollBack();
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Stok Bakiyesi Yetersiz!', 'text' => $subeurun->urunadi.' İsimli Ürünün  Bakiyesi Yetersiz!', 'type' => 'error'));
                            }
                        }
                        $secilenler .= ($secilenler == "" ? "" : ",") . $urun[$i];
                        $miktarlar .= ($miktarlar == "" ? "" : ",") . $miktar[$i];
                        $birimfiyatlar .= ($birimfiyatlar == "" ? "" : ",") . number_format(floatval($birimfiyat[$i]),2,'.','');
                        $gfiyatlar .= ($gfiyatlar == "" ? "" : ",") . number_format(floatval($gfiyat[$i]),2,'.','');
                        SubeUrun::where('id', $urun[$i])->update(['urundurum' => 1]);
                    }
                }
            } catch (Exception $e) {
                Log::error($e);
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ürün Eklemede Hata Oluştu', 'text' => 'Kalem Olarak Eklenen Ürünlerin Bilgilerinde Hata Var!', 'type' => 'error'));
            }

            try {
                if ($baglanti && $baglantililar != count($tumsayac)) {
                    DB::rollBack();
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Bağlantılı Sayaçların Sayısı Uyuşmuyor', 'text' => 'Sayaçlarla Bağlantılı Seçilen Ürünlerin Miktarı ile Seçilen Sayaçların Sayısı Farklı', 'type' => 'error'));
                }
            } catch (Exception $e) {
                Log::error($e);
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Bağlantılı Sayaçların Sayısı Uyuşmuyor', 'text' => 'Sayaçlarla Bağlantılı Seçilen Ürünlerin Miktarı ile Seçilen Sayaçların Sayısı Farklı', 'type' => 'error'));
            }

            try {
                $abone->faturaadresi = $adres;
                $abone->iller_id = $faturail;
                $abone->ilceler_id = $faturailce;
                $abone->netsiscari_id = $netsiscariid;
                $abone->tckimlikno = $tckimlikno;
                $abone->telefon = $telefon;
                $abone->save();
                $odeme  = 0;
                $odeme2 = 0;
                switch ($odemetipi) {
                    case 1 : //NAKİT
                        $odeme = $toplamtutar;
                        break;
                    case 2 : //KREDİ KARTI
                        $odeme2 = $toplamtutar;
                        break;
                    case 3 : //SENET
                        break;
                    case 4 : //NAKİT+KREDİ KARTI
                        $odeme = $nakit;
                        $odeme2 = $kredikart;
                        break;
                    case 5 : //BANKA HAVALESİ
                        break;
                    case 6 : //2 KREDİ KARTI
                        $odeme = $kredikart1;
                        $odeme2 = $kredikart2;
                        break;
                    case 7 : //BELLİ DEĞİL
                        break;
                }
                $sayacsatis = new SubeSayacSatis;
                $sayacsatis->subekodu = $subekodu ;
                $sayacsatis->abone_id = $aboneid;
                $sayacsatis->uretimyer_id = $abone->uretimyer_id;
                $sayacsatis->netsiscari_id = $netsiscariid;
                $sayacsatis->faturatarihi = $faturatarihi;
                $sayacsatis->secilenler = $secilenler;
                $sayacsatis->adet = $miktarlar;
                $sayacsatis->birimfiyat = $birimfiyatlar;
                $sayacsatis->gfiyat = $gfiyatlar;
                $sayacsatis->ucretsiz = $ucretsizler;
                $sayacsatis->sayaclar = $abonesayaci;
                $sayacsatis->tutar = $tutar;
                $sayacsatis->kdv = $kdv;
                $sayacsatis->toplamtutar = $toplamtutar;
                $sayacsatis->parabirimi_id = $parabirimi;
                $sayacsatis->kullanici_id = Auth::user()->id;
                $sayacsatis->kayittarihi = date('Y-m-d H:i:s');
                $sayacsatis->kdvorani=$kdv_orani->deger;
                $sayacsatis->yazitutar=BackendController::YaziTutar($toplamtutar,$parabirimi);
                $sayacsatis->efatura = BackendController::Efatura($dbname,$netsiscariid);
                $sayacsatis->kurtarihi = $kurtarih;
                $sayacsatis->durum = 1;
                $sayacsatis->db_name = $dbname;
                $sayacsatis->faturano = $fatirsno;
                $sayacsatis->faturaadres = $faturaadresi;
                $sayacsatis->iller_id = $faturail;
                $sayacsatis->ilceler_id = $faturailce;
                $sayacsatis->carikod = $netsiscari->carikod;
                $sayacsatis->ozelkod = $yetkili->ozelkod;
                $sayacsatis->projekodu = $subeyetkili->projekodu;
                $sayacsatis->plasiyerkod = $yetkili->plasiyerkod;
                $sayacsatis->depokodu = $yetkili->depokodu;
                $sayacsatis->aciklama = $aciklama;
                $sayacsatis->odemetipi = $odemetipi;
                $sayacsatis->odemesekli = $odemesekli;
                $sayacsatis->kasakodu = $kasakodu;
                $sayacsatis->kasakodu2 = $kasakodu2;
                $sayacsatis->odeme = $odeme;
                $sayacsatis->odeme2 = $odeme2;
                $sayacsatis->taksit = $taksit;
                $sayacsatis->taksit2 = $taksit2;
                $sayacsatis->odemeyapan = trim($odemeyapan)!="" ? BackendController::StrtoUpper(trim($odemeyapan)) : null;
                $sayacsatis->netsiskullanici = $yetkili->netsiskullanici;
                $sayacsatis->netsiskullanici_id = $yetkili->netsiskullanici_id;
                $sayacsatis->save();

                if ($fatura == 0) //fatura basılmayacaksa
                {
                    $servisdurum = BackendController::ServisBilgisiEkle($sayacsatis, 0); // Servis Bilgisi Eklenecek 0 Satış 1 Tamir sonrası servis 2 arıza kontrolü
                    if ($servisdurum) {
                        $durum = 8;
                    } else if ($servisdurum == 2) {
                        $durum = 9;
                    } else {
                        $durum = 1;
                    }
                } else {
                    if ($sayacsatis->efatura) { //E-fatura varsa
                        $faturaonek = BackendController::FaturaOnEk($sayacsatis);
                        $fatura = Fatuirs::where('FTIRSIP', 1)->where('FATIRS_NO', 'LIKE', $faturaonek . '%')->orderBy('FATIRS_NO', 'desc')->first();
                        if ($fatura) {
                            $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO, 1);
                        } else {
                            $fatirsno = BackendController::FaturaNo($faturaonek . '0', 1);
                        }
                        $sayacsatis->faturano = $fatirsno;
                        $sayacsatis->save();
                        Fatuno::where(['SUBE_KODU' => $subeyetkili->subekodu, 'SERI' => 'M', 'TIP' => '1'])->update(['NUMARA' => $fatirsno]);
                    } else {
                        Fatuno::where(['SUBE_KODU' => $subeyetkili->subekodu, 'SERI' => $subeyetkili->belgeonek, 'TIP' => '1'])->update(['NUMARA' => $fatirsno]);
                    }
                    $durum = BackendController::SatisFaturasi($sayacsatis->id);
                }
                if ($durum == 0) {
                    BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $sayacsatis->id . ' Numaralı Satış Kayıdı Yapıldı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Sayaç Satış Numarası:' . $sayacsatis->id);
                    DB::commit();
                    return Redirect::to($this->servisadi . '/sayacsatis')->with(array('mesaj' => 'true', 'title' => 'Satış Kayıdı Başarıyla Yapıldı', 'text' => 'Faturası oluşturulmadan işlem yapıldı', 'type' => 'success'));
                } else if ($durum == 1) {
                    BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $sayacsatis->id . ' Numaralı Satış Kayıdı Yapıldı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Sayaç Satış Numarası:' . $sayacsatis->id);
                    DB::commit();
                    return Redirect::to($this->servisadi . '/sayacsatis')->with(array('mesaj' => 'true', 'title' => 'Satış Kayıdı Başarıyla Yapıldı', 'text' => 'Faturası oluşturularak işlem yapıldı', 'type' => 'success'));
                } else {
                    $silmedurum = BackendController::SatisFaturasiTemizle($sayacsatis->id);
                    DB::rollBack();
                    Input::flash();
                    if ($durum == 2) {
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Netsise Kaydedilemedi.'.($silmedurum['durum']=="0" ? $silmedurum['text'] : ''), 'type' => 'error'));
                    } else if ($durum == 3) {
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Açıklaması Kaydedilemedi.'.($silmedurum['durum']=="0" ? $silmedurum['text'] : ''), 'type' => 'error'));
                    } else if ($durum == 4) {
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Kalemleri Kaydedilemedi.'.($silmedurum['durum']=="0" ? $silmedurum['text'] : ''), 'type' => 'error'));
                    } else if ($durum == 5) {
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Bu Fatura Numarası Sistemde Kayıtlı.'.($silmedurum['durum']=="0" ? $silmedurum['text'] : ''), 'type' => 'error'));
                    } else if ($durum == 6) {
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Ücret Kasaya Kaydedilemedi.'.($silmedurum['durum']=="0" ? $silmedurum['text'] : ''), 'type' => 'error'));
                    } else if ($durum == 7) {
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Kalemleri Kaydedilemedi.'.($silmedurum['durum']=="0" ? $silmedurum['text'] : ''), 'type' => 'error'));
                    } else if ($durum == 8) {
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Servis Bilgisi Kaydedilemedi.'.($silmedurum['durum']=="0" ? $silmedurum['text'] : ''), 'type' => 'error'));
                    } else if ($durum == 9) {
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Sayaç Aboneye Ait Değil!'.($silmedurum['durum']=="0" ? $silmedurum['text'] : ''), 'type' => 'error'));
                    } else if ($durum == 10) {
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Seri Numaralar Faturaya Kaydedilemedi!'.($silmedurum['durum']=="0" ? $silmedurum['text'] : ''), 'type' => 'error'));
                    } else if ($durum == 11) {
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Muhasebe Fişi Kaydedilemedi!'.($silmedurum['durum']=="0" ? $silmedurum['text'] : ''), 'type' => 'error'));
                    } else if ($durum == 12) {
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Cari Hareket Kaydedilemedi!'.($silmedurum['durum']=="0" ? $silmedurum['text'] : ''), 'type' => 'error'));
                    } else {
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => $durum.($silmedurum['durum']=="0" ? $silmedurum['text'] : ''), 'type' => 'error'));
                    }
                }
            } catch (Exception $e) {
                Log::error($e);
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Satış Bilgisi Kaydedilemedi', 'text' => 'Satış Bilgisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }
        }catch(Exception $e){
            Log::error($e);
            DB::rollBack();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Satış Bilgisi Kaydedilemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getSayacsatisduzenle($id) {
        $sayacsatis=SubeSayacSatis::find($id);
        $sayacsatis->uretimyer=UretimYer::find($sayacsatis->uretimyer_id);
        $sayacsatis->abone=Abone::find($sayacsatis->abone_id);
        $netsiscariid=Auth::user()->netsiscari_id;
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        $netsiscariler=NetsisCari::whereIn('id',Auth::user()->netsiscari_id)->get(array('id'))->toArray();
        $uretimyerid=CariYer::whereIn('netsiscari_id',$netsiscariler)->where('durum',1)->get(array('uretimyer_id'))->toArray();
        $uretimyerleri = UretimYer::whereIn('id',$uretimyerid)->get();
        if($sube) {
            $sube->netsiscari = NetsisCari::find($sube->netsiscari_id);
            $sube->depo = NetsisDepolar::find($sube->netsisdepolar_id);
            $iller = Iller::all();
            if($sayacsatis->netsiscari_id!=$sube->netsiscari_id){
                $ilceler = Ilceler::where('iller_id',$sayacsatis->iller_id)->get();
            }else{
                $ilceler = Ilceler::where('iller_id',$sube->iller_id)->get();
            }
            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->get(array('netsiscari_id'))->toArray();
            $netsiscariler = NetsisCari::where('caridurum', 'A')->whereIn('caritipi', array('A', 'D'))
                ->where(function ($query) use ($subeyetkili, $sube) {
                    $query->whereIn('id', $subeyetkili)->orwhereIn('subekodu', array(-1, $sube->subekodu));
                })
                ->whereNotIn('carikod', (function ($query) use ($sube) {
                    $query->select('carikod')->from('kodharichar')->where('subekodu', $sube->subekodu);
                }))
                ->whereNotIn('id', array($sube->netsiscari_id))
                ->orderBy('cariadi', 'asc')->get();
            foreach ($netsiscariler as $netsiscari) {
                $netsiscari->efatura = BackendController::Efatura('MANAS' . date('Y'), $netsiscari->id);
            }
            $urunler = SubeUrun::where('subekodu', $sube->subekodu)->where('durum', 1)->get();
            foreach ($urunler as $urun) {
                $urun->netsisstokkod = NetsisStokKod::find($urun->netsisstokkod_id);
                $urun->parabirimi = Parabirimi::find($urun->parabirimi_id);
                $urun->stok = StBakiye::where('STOK_KODU', $urun->netsisstokkod->kodu)->where('DEPO_KODU', $urun->depokodu)->first();
                if (!$urun->stok)
                    $urun->stok = new StBakiye(array('BAKIYE' => 0));
            }
            $baglantidurum = array();
            $abonesayaclar = array();
            $abonesayaclist = array();
            $sayaclar = explode(';', $sayacsatis->sayaclar);
            for ($i = 0; $i < count($sayaclar); $i++) {
                $sayaclist = explode(',', $sayaclar[$i]);
                array_push($abonesayaclar, $sayaclar[$i]);
                $abonesayaclist = array_merge($abonesayaclist, $sayaclist);
            }
            $sayacsatis->abonesayaclar = $abonesayaclar;
            $abonesayacidleri = AboneTahsis::where('abone_id', $sayacsatis->abone_id)->get(array('abonesayac_id'))->toArray();
            $abonesayaclari = AboneSayac::whereIn('id', $abonesayacidleri)->where(function ($query) use ($abonesayaclist) {
                $query->where('satisdurum', 0)->orWhereIn('id', $abonesayaclist);
            })->get();
            $urunlist = explode(',', $sayacsatis->secilenler);
            $secilenler = array();
            $baglantidurumlari="";
            for ($i = 0; $i < count($urunlist); $i++) {
                $subeurun = SubeUrun::find($urunlist[$i]);
                array_push($secilenler, $subeurun);
                array_push($baglantidurum, $subeurun->baglanti);
                $baglantidurumlari.=($baglantidurumlari=="" ? "" : ",").$subeurun->baglanti;
            }
            $sayacsatis->baglantidurum = $baglantidurum;
            $sayacsatis->urunler = $secilenler;
            $sayacsatis->parabirimi = ParaBirimi::find($sayacsatis->parabirimi_id);
            $sayacsatis->adetler = explode(',', $sayacsatis->adet);
            $sayacsatis->fiyatlar = explode(',', $sayacsatis->birimfiyat);
            $sayacsatis->gfiyatlar = explode(',', $sayacsatis->gfiyat);
            $sayacsatis->ucretsizler = explode(',', $sayacsatis->ucretsiz);
            $sayacsatis->baglantidurumlari = $baglantidurumlari;
            $parabirimi = ParaBirimi::find(1);
            if (is_null($sayacsatis->kurtarihi))
                $dovizkuru = DovizKuru::orderBy('tarih', 'desc')->orderBy('parabirimi_id', 'asc')->take(3)->get();
            else
                $dovizkuru = DovizKuru::where('tarih', $sayacsatis->kurtarihi)->orderBy('tarih', 'desc')->orderBy('parabirimi_id', 'asc')->take(3)->get();
            foreach ($dovizkuru as $doviz) {
                $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
            }
            $sayacsatis->kurtarihi = date("d-m-Y", strtotime($sayacsatis->kurtarihi));
            $geneltoplam = 0;
            for ($i=0;$i<count($sayacsatis->gfiyatlar);$i++){
                $ucretsiz=intval($sayacsatis->ucretsizler[$i]);
                $adet=intval($sayacsatis->adetler[$i]);
                $gfiyat=floatval($sayacsatis->gfiyatlar[$i]);
                if(!$ucretsiz){
                    $geneltoplam+=($gfiyat*$adet);
                }
            }
            $genelkdv=$geneltoplam*18/118;
            $geneltutar=$geneltoplam-$genelkdv;
            $sayacsatis->geneltutar=$geneltutar;
            $sayacsatis->genelkdv=$genelkdv;
            $sayacsatis->geneltoplam=$geneltoplam;
            $sayacturlist = explode(',', $sube->sayactur);
            $sayacadilist = explode(',', $sube->sayacadlari);
            $sayacturleri = SayacTur::whereIn('id', $sayacturlist)->get();
            $sayacadlari = SayacAdi::whereIn('sayactur_id', $sayacturlist)->whereIn('id', $sayacadilist)->get();
            $sayaccaplari = SayacCap::all();
            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
            $kasakodlar = KasaKod::where('subekodu', $subeyetkili->subekodu)->get();
            return View::make($this->servisadi.'.sayacsatisduzenle',array('sayacsatis'=>$sayacsatis,'netsiscariler'=>$netsiscariler,'abonesayaclari'=>$abonesayaclari,'urunler'=>$urunler,'iller'=>$iller,'ilceler'=>$ilceler,'parabirimi'=>$parabirimi,'dovizkuru'=>$dovizkuru,'sube'=>$sube,'kasakodlar' => $kasakodlar, 'sayacturleri' => $sayacturleri, 'sayacadlari' => $sayacadlari, 'sayaccaplari' => $sayaccaplari, 'uretimyerleri' => $uretimyerleri))->with(array('title'=>'Satış Kayıdı Düzenle'));
        }else{
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkisiz', 'text' => 'Fatura Düzenleme Yetkiniz Yok', 'type' => 'error'));
        }
    }

    public function postSayacsatisduzenle($id){
        try{
            if (Input::has('faturavar')) {
                if(Input::get('odemetipi')==5 || Input::get('odemetipi')==7)
                    $rules = ['cariadi'=>'required','abone'=>'required','tckimlikno'=>'required','telefon'=>'required|min:10','tarih'=>'required','adres'=>'required','faturail'=>'required','faturailce'=>'required','faturano' => 'required', 'aciklama' => 'required', 'odemesekli' => 'required'];
                else
                    $rules = ['cariadi'=>'required','abone'=>'required','tckimlikno'=>'required','telefon'=>'required|min:10','tarih'=>'required','adres'=>'required','faturail'=>'required','faturailce'=>'required','faturano' => 'required', 'aciklama' => 'required', 'odemesekli' => 'required', 'kasakod' => 'required'];
                $validate = Validator::make(Input::all(), $rules);
                $messages = $validate->messages();
                if ($validate->fails()) {
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
                }
                $fatura = 1;
            } else {
                $fatura = 0;
            }
            $dbname = 'MANAS' . date('Y');
            $sayacsatis=SubeSayacSatis::find($id);
            $eskibilgi=clone $sayacsatis;
            $aboneid = Input::get('abone');
            $subekodu = Input::get('subekodu');
            if($subekodu==""){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Şube Hatası', 'text' => 'Kullanıcıya Ait Aktif Bir Şube Tanımlı Değil!', 'type' => 'error'));
            }
            $sube = Sube::where('subekodu',$subekodu)->where('aktif',1)->first();
            if($sube) {
                $sube->depo = NetsisDepolar::find($sube->netsisdepolar_id);
            }else{
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Şube Hatası', 'text' => 'Şube Bilgisi Bulunamadı!', 'type' => 'error'));
            }
            if($sayacsatis->db_name!=$dbname){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Fatura Düzenleme Hatası', 'text' => 'Eski Yıla Ait Faturalar Üzerinde Düzenleme Yapılamaz!', 'type' => 'error'));
            }
            $netsiscariid = Input::get('cariadi');
            $adres = Input::get('adres');
            $tckimlikno = Input::get('tckimlikno');
            $telefon = Input::get('telefon');
            $kalemadet = Input::get('count');
            $urun = Input::get('urunadi');
            $birimfiyat = Input::get('fiyat');
            $gfiyat = Input::get('gfiyat');
            $miktar = Input::get('miktar');
            $ucretsizler = Input::get('ucretsizler');
            $tutar = Input::get('tutar');
            $kdv = Input::get('kdvtutar');
            $toplamtutar = Input::get('toplamtutar');
            $parabirimi = Input::get('parabirimi');
            $kurtarih = date('Y-m-d',strtotime(Input::get('kurtarih')));
            $faturatarihi = date('Y-m-d',strtotime(Input::get('tarih')));
            $baglantidurum=Input::get('baglantidurum');
            $baglanti=0;
            $kdv_orani=SistemSabitleri::where('adi','KdvOrani')->first();
            $faturaadresi=Input::get('faturaadresi');
            $faturail=Input::get('faturail');
            $faturailce=Input::get('faturailce');
            $odemesekli=Input::get('odemesekli');
            $odemetipi=Input::get('odemetipi');
            $taksit=Input::get('taksit');
            $taksit2=Input::get('taksit2');
            $nakit = floatval(Input::get('nakit'));
            $kredikart = floatval(Input::get('kredikart'));
            $kredikart1 = floatval(Input::get('kredikart1'));
            $kredikart2 = floatval(Input::get('kredikart2'));
            $aciklama=Input::get('aciklama');
            $odemeyapan=Input::get('odemeyapan');
            $telefon = preg_replace('/\D/','',$telefon);
            $telefon = mb_substr($telefon, 0, 4)." ".mb_substr($telefon, 4, 3)." ".mb_substr($telefon, 7, 2)." ".mb_substr($telefon, 9, 2);
            $tumsayac=array();
            if(Input::has('abonesayac'))
                $abonesayaclari=Input::get('abonesayac');
            else
                $abonesayaclari=array();
            $abonesayaci="";
            $ucretsizlist = explode(',',$ucretsizler);
            for($i=0;$i<count($birimfiyat);$i++){
                if($ucretsizlist[$i]==0){
                    if(floatval($birimfiyat[$i]==0)){
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Birim Fiyat Sıfır Girilemez! Ücretsiz olacaksa ücretsiz kısmı işaretlenmelidir. ', 'type' => 'error'));
                    }
                }
            }
            $abone = Abone::find($aboneid);
            $netsiscari = NetsisCari::find($netsiscariid);
            $faturano = Input::get('faturano');
            if ($netsiscari->caridurum != "A")
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
            if (!$subeyetkili) {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube Yetkili Uyarısı', 'text' => 'Bu Yere Ait Şube Yetkilisi Yok.', 'type' => 'warning'));
            }
            $yetkili = ServisYetkili::where('kullanici_id', $subeyetkili->kullanici_id)->first();
            if (!$yetkili) {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetki Uyarısı', 'text' => 'Bu Kullanıcının Yetkisi Yok.', 'type' => 'warning'));
            }
            $fatirsno = BackendController::FaturaNo($faturano,0);
            if(Input::has('kasakod')){
                $kasakodu = Input::get('kasakod');
            }else{
                $kasakod = KasaKod::where('subekodu', $subeyetkili->subekodu)->orderBy('kasaadi','asc')->first();
                $kasakodu = $kasakod->kasakod;
            }
            $kasakodu2 = Input::get('kasakod2');
            DB::beginTransaction();
            try {
                for ($i = 0; $i < $kalemadet; $i++) {
                    if ($baglantidurum[$i]) {
                        $baglanti = 1;
                        if (isset($abonesayaclari[$i])) {
                            foreach ($abonesayaclari[$i] as $sayaci) {
                                if (SubeSayacSatis::where('id','<>',$id)->where(function($query) use($sayaci){
                                    $query->where('sayaclar', 'LIKE', $sayaci.';%')->orWhere('sayaclar', 'LIKE', $sayaci.',%')
                                        ->orWhere('sayaclar', 'LIKE', '%,'.$sayaci.',%');})->first()) {
                                    DB::rollBack();
                                    Input::flash();
                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Satış Kayıdı Hatalı!', 'text' => 'Ekteki Sayaçlardan Bazılarının Zaten Satışı Yapılmış!', 'type' => 'error'));
                                }
                            }
                            $ek = "";
                            foreach ($abonesayaclari[$i] as $sayaci) {
                                if (in_array($sayaci, $tumsayac)) {
                                    foreach ($tumsayac as $sayac) {
                                        AboneSayac::where('id', $sayac)->update(['satisdurum' => 0]);
                                    }
                                    DB::rollBack();
                                    Input::flash();
                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Bağlantılı Sayaçlar Hatalı!', 'text' => 'Aynı Seri Numarası Farklı kalemlerde Mevcut!', 'type' => 'error'));
                                } else {
                                    array_push($tumsayac, $sayaci);
                                }
                                $ek .= ($ek == "" ? "" : ",") . $sayaci;
                                AboneSayac::where('id', $sayaci)->update(['satisdurum' => 1]);
                            }
                            $abonesayaci .= ($abonesayaci == "" ? "" : ";") . $ek;
                        } else {
                            DB::rollBack();
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Bağlantılı Sayaçlar Eksik!', 'text' => 'Kalem Olarak Eklenen Sayaçların Seri Numaraları Girilmemiş ya da Eksik!', 'type' => 'error'));
                        }
                    } else {
                        $abonesayaci .= ($abonesayaci == "" ? "" : ";") . '0';
                    }
                }
                $eskisayacgruplar=explode(';',$eskibilgi->sayaclar);
                foreach ($eskisayacgruplar as $eskisayacgrup){
                    $eskisayaclar=explode(',',$eskisayacgrup);
                    foreach ($eskisayaclar as $eskisayac){
                        if(!in_array($eskisayac,$tumsayac)){
                            AboneSayac::where('id', $eskisayac)->update(['satisdurum' => 0]);
                        }
                    }
                }
            } catch (Exception $e) {
                Log::error($e);
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Bağlantılı Sayaçlar Eksik!', 'text' => 'Kalem Olarak Eklenen Sayaçlarda Hata Var!', 'type' => 'error'));
            }
            $secilenler="";
            $miktarlar="";
            $birimfiyatlar="";
            $gfiyatlar="";
            $baglantililar=0;
            try {
                for ($i = 0; $i < $kalemadet; $i++) {
                    if ($urun[$i] != "") {
                        $subeurun = SubeUrun::find($urun[$i]);
                        if ($subeurun->baglanti) {
                            $baglantililar += $miktar[$i];
                        }
                        if($subeurun->kontrol){
                            $subeurun->netsisstokkod = NetsisStokKod::find($subeurun->netsisstokkod_id);
                            $stok=StBakiye::where('STOK_KODU',$subeurun->netsisstokkod->kodu)->where('DEPO_KODU',$sube->depo->kodu)->first();
                            if(!$stok)
                                $stok=new StBakiye(array('BAKIYE'=>0));
                            $eskihareket = Sthar::where('SUBE_KODU', $sayacsatis->subekodu)->where('STHAR_FTIRSIP', 1)->where('FISNO', $eskibilgi->faturano)
                                ->where('STOK_KODU',$subeurun->netsisstokkod->kodu)->first();
                            if($eskihareket){
                                if((intval($stok->BAKIYE)-intval($eskihareket->STHAR_GCMIK))<$miktar[$i]){
                                    DB::rollBack();
                                    Input::flash();
                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Stok Bakiyesi Yetersiz!', 'text' => $subeurun->urunadi.' İsimli Ürünün  Bakiyesi Yetersiz!', 'type' => 'error'));
                                }
                            }else{
                                if((intval($stok->BAKIYE))<$miktar[$i]){
                                    DB::rollBack();
                                    Input::flash();
                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Stok Bakiyesi Yetersiz!', 'text' => $subeurun->urunadi.' İsimli Ürünün  Bakiyesi Yetersiz!', 'type' => 'error'));
                                }
                            }

                        }
                        $secilenler .= ($secilenler == "" ? "" : ",") . $urun[$i];
                        $miktarlar .= ($miktarlar == "" ? "" : ",") . $miktar[$i];
                        $birimfiyatlar .= ($birimfiyatlar == "" ? "" : ",") . number_format(floatval($birimfiyat[$i]),2,'.','');
                        $gfiyatlar .= ($gfiyatlar == "" ? "" : ",") . number_format(floatval($gfiyat[$i]),2,'.','');
                        SubeUrun::where('id', $urun[$i])->update(['urundurum' => 1]);
                    }
                }
            } catch (Exception $e) {
                Log::error($e);
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ürün Eklemede Hata Oluştu', 'text' => 'Kalem Olarak Eklenen Ürünlerin Bilgilerinde Hata Var!', 'type' => 'error'));
            }

            try {
                if ($baglanti && $baglantililar != count($tumsayac)) {
                    DB::rollBack();
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Bağlantılı Sayaçların Sayısı Uyuşmuyor', 'text' => 'Sayaçlarla Bağlantılı Seçilen Ürünlerin Miktarı ile Seçilen Sayaçların Sayısı Farklı', 'type' => 'error'));
                }
            } catch (Exception $e) {
                Log::error($e);
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Bağlantılı Sayaçların Sayısı Uyuşmuyor', 'text' => 'Sayaçlarla Bağlantılı Seçilen Ürünlerin Miktarı ile Seçilen Sayaçların Sayısı Farklı', 'type' => 'error'));
            }

            try {
                $abone->faturaadresi = $adres;
                $abone->iller_id = $faturail;
                $abone->ilceler_id = $faturailce;
                $abone->netsiscari_id = $netsiscariid;
                $abone->tckimlikno = $tckimlikno;
                $abone->telefon = $telefon;
                $abone->save();
                $odeme  = 0;
                $odeme2 = 0;
                switch ($odemetipi) {
                    case 1 : //NAKİT
                        $odeme = $toplamtutar;
                        break;
                    case 2 : //KREDİ KARTI
                        $odeme2 = $toplamtutar;
                        break;
                    case 3 : //SENET
                        break;
                    case 4 : //NAKİT+KREDİ KARTI
                        $odeme = $nakit;
                        $odeme2 = $kredikart;
                        break;
                    case 5 : //BANKA HAVALESİ
                        break;
                    case 6 : //2 KREDİ KARTI
                        $odeme = $kredikart1;
                        $odeme2 = $kredikart2;
                        break;
                }
                $sayacsatis->subekodu = $subekodu;
                $sayacsatis->abone_id = $aboneid;
                $sayacsatis->uretimyer_id = $abone->uretimyer_id;
                $sayacsatis->netsiscari_id = $netsiscariid;
                $sayacsatis->faturatarihi = $faturatarihi;
                $sayacsatis->secilenler = $secilenler;
                $sayacsatis->adet = $miktarlar;
                $sayacsatis->birimfiyat = $birimfiyatlar;
                $sayacsatis->gfiyat = $gfiyatlar;
                $sayacsatis->ucretsiz = $ucretsizler;
                $sayacsatis->sayaclar = $abonesayaci;
                $sayacsatis->tutar = $tutar;
                $sayacsatis->kdv = $kdv;
                $sayacsatis->toplamtutar = $toplamtutar;
                $sayacsatis->parabirimi_id = $parabirimi;
                $sayacsatis->kullanici_id = Auth::user()->id;
                $sayacsatis->kayittarihi = date('Y-m-d H:i:s');
                $sayacsatis->kdvorani=$kdv_orani->deger;
                $sayacsatis->yazitutar=BackendController::YaziTutar($toplamtutar,$parabirimi);
                $sayacsatis->efatura = BackendController::Efatura($dbname,$netsiscariid);
                $sayacsatis->kurtarihi = $kurtarih;
                $sayacsatis->durum = 1;
                $sayacsatis->db_name = $dbname;
                $sayacsatis->faturano = $fatirsno;
                $sayacsatis->faturaadres = $faturaadresi;
                $sayacsatis->iller_id = $faturail;
                $sayacsatis->ilceler_id = $faturailce;
                $sayacsatis->carikod = $netsiscari->carikod;
                $sayacsatis->ozelkod = $yetkili->ozelkod;
                $sayacsatis->projekodu = $subeyetkili->projekodu;
                $sayacsatis->plasiyerkod = $yetkili->plasiyerkod;
                $sayacsatis->depokodu = $yetkili->depokodu;
                $sayacsatis->aciklama = $aciklama;
                $sayacsatis->odemetipi = $odemetipi;
                $sayacsatis->odemesekli = $odemesekli;
                $sayacsatis->kasakodu = $kasakodu;
                $sayacsatis->kasakodu2 = $kasakodu2;
                $sayacsatis->odeme = $odeme;
                $sayacsatis->odeme2 = $odeme2;
                $sayacsatis->taksit = $taksit;
                $sayacsatis->taksit2 = $taksit2;
                $sayacsatis->odemeyapan = trim($odemeyapan)!="" ? BackendController::StrtoUpper(trim($odemeyapan)) : null;
                $sayacsatis->netsiskullanici = $yetkili->netsiskullanici;
                $sayacsatis->netsiskullanici_id = $yetkili->netsiskullanici_id;
                $sayacsatis->save();

                if ($fatura == 0) //fatura basılmayacaksa
                {
                    $servisdurum = BackendController::ServisBilgisiDuzenle($sayacsatis->id, 0,$eskibilgi->sayaclar); // Servis Bilgisi Eklenecek 0 Satış 1 Tamir sonrası servis 2 arıza kontrolü
                    if ($servisdurum) {
                        $durum = 8;
                    } else if ($servisdurum == 2) {
                        $durum = 9;
                    } else {
                        $durum = 1;
                    }
                } else {
                    if ($sayacsatis->efatura) { //E-fatura varsa
                        $faturaonek = BackendController::FaturaOnEk($sayacsatis);
                        $fatura = Fatuirs::where('FTIRSIP', 1)->where('FATIRS_NO', 'LIKE', $faturaonek . '%')->orderBy('FATIRS_NO', 'desc')->first();
                        if ($fatura) {
                            $fatirsno = BackendController::FaturaNo($fatura->FATIRS_NO, 1);
                        } else {
                            $fatirsno = BackendController::FaturaNo($faturaonek . '0', 1);
                        }
                        $faturaseri = mb_substr($fatirsno, 0, 1);
                        if(mb_substr($eskibilgi->faturano, 0, 1)==$faturaseri){
                            $fatirsno = $eskibilgi->faturano;
                        }
                        $sayacsatis->faturano = $fatirsno;
                        $sayacsatis->save();
                        Fatuno::where(['SUBE_KODU' => $subeyetkili->subekodu, 'SERI' => $faturaseri, 'TIP' => '1'])->update(['NUMARA' => $fatirsno]);
                    } else {
                        $faturaseri =mb_substr($fatirsno, 0, 1);
                        Fatuno::where(['SUBE_KODU' => $subeyetkili->subekodu, 'SERI' => $faturaseri, 'TIP' => '1'])->update(['NUMARA' => $fatirsno]);
                    }
                    $durum = BackendController::SatisFaturasiDuzenle($sayacsatis->id,$eskibilgi);
                }
                if ($durum == 0) {
                    BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $eskibilgi->id . ' Numaralı Satış Kayıdı Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Sayaç Satış Numarası:' . $eskibilgi->id);
                    DB::commit();
                    return Redirect::to($this->servisadi . '/sayacsatis')->with(array('mesaj' => 'true', 'title' => 'Satış Kayıdı Başarıyla Güncellendi', 'text' => 'Faturası oluşturulmadan işlem yapıldı', 'type' => 'success'));
                } else if ($durum == 1) {
                    BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $eskibilgi->id . ' Numaralı Satış Kayıdı Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Sayaç Satış Numarası:' . $eskibilgi->id);
                    DB::commit();
                    return Redirect::to($this->servisadi . '/sayacsatis')->with(array('mesaj' => 'true', 'title' => 'Satış Kayıdı Güncellendi', 'text' => 'Satış Kayıdı Başarıyla Güncellendi', 'type' => 'success'));
                } else {
                    $text = "";
                    BackendController::SatisFaturasiTemizle($sayacsatis->id);
                    DB::rollBack();
                    Input::flash();
                    $eklemedurum = BackendController::SatisFaturasi($sayacsatis->id);
                    if ($eklemedurum != 1 && $eklemedurum != 0) {
                        if ($durum == 2) {
                            $eklemedurum ='Fatura Netsise Kaydedilemedi.';
                        } else if ($durum == 3) {
                            $eklemedurum ='Fatura Açıklaması Kaydedilemedi.';
                        } else if ($durum == 4) {
                            $eklemedurum ='Fatura Kalemleri Kaydedilemedi.';
                        } else if ($durum == 5) {
                            $eklemedurum ='Bu Fatura Numarası Sistemde Kayıtlı.';
                        } else if ($durum == 6) {
                            $eklemedurum ='Ücret Kasaya Kaydedilemedi.';
                        } else if ($durum == 7) {
                            $eklemedurum ='Fatura Kalemleri Kaydedilemedi.';
                        } else if ($durum == 8) {
                            $eklemedurum ='Servis Bilgisi Kaydedilemedi.';
                        } else if ($durum == 9) {
                            $eklemedurum ='Sayaç Aboneye Ait Değil!';
                        } else if ($durum == 10) {
                            $eklemedurum ='Seri Numaralar Faturaya Kaydedilemedi!';
                        } else if ($durum == 11) {
                            $eklemedurum ='Muhasebe Fişi Kaydedilemedi!';
                        } else if ($durum == 12) {
                            $eklemedurum ='Cari Hareket Kaydedilemedi!';
                        }
                        $text .=$eklemedurum;
                    }
                    if ($durum == 2) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Netsise Kaydedilemedi.'.$text, 'type' => 'error'));
                    } else if ($durum == 3) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Açıklaması Kaydedilemedi.'.$text, 'type' => 'error'));
                    } else if ($durum == 4) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Kalemleri Kaydedilemedi.'.$text, 'type' => 'error'));
                    } else if ($durum == 5) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Bu Fatura Numarası Sistemde Kayıtlı.'.$text, 'type' => 'error'));
                    } else if ($durum == 6) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Ücret Kasaya Kaydedilemedi.'.$text, 'type' => 'error'));
                    } else if ($durum == 7) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Kalemleri Kaydedilemedi.'.$text, 'type' => 'error'));
                    } else if ($durum == 8) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Servis Bilgisi Kaydedilemedi.'.$text, 'type' => 'error'));
                    } else if ($durum == 9) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Sayaç Aboneye Ait Değil!'.$text, 'type' => 'error'));
                    } else if ($durum == 10) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Seri Numaralar Faturaya Kaydedilemedi!'.$text, 'type' => 'error'));
                    } else if ($durum == 11) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Muhasebe Fişi Kaydedilemedi!'.$text, 'type' => 'error'));
                    } else if ($durum == 12) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Cari Hareket Kaydedilemedi!'.$text, 'type' => 'error'));
                    } else if ($durum == 13) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Eski Fatura Bulunamadı.'.$text, 'type' => 'error'));
                    } else if ($durum == 14) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Eski Fatura Açıklaması Bulunamadı.'.$text, 'type' => 'error'));
                    } else if ($durum == 15) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Eski Fatura Kalemleri Bulunamadı.'.$text, 'type' => 'error'));
                    } else if ($durum == 16) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Eski Faturaya Ait Seri No Bilgileri Bulunamadı.'.$text, 'type' => 'error'));
                    } else if ($durum == 17) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Eski Faturaya Ait Kasa Kaydı Bulunamadı.'.$text, 'type' => 'error'));
                    } else if ($durum == 18) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Eski Faturaya Ait Cari Hareket Bilgisi Bulunamadı.'.$text, 'type' => 'error'));
                    } else if ($durum == 19) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Eski Faturaya Ait Muhasebe Fişi Bulunamadı'.$text, 'type' => 'error'));
                    } else if ($durum == 20) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Eski Faturaya Ait Servis Kaydı Bulunamadı!'.$text, 'type' => 'error'));
                    } else if ($durum == 21) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Eski Fatura Silinemedi!'.$text, 'type' => 'error'));
                    } else {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => $durum.$text, 'type' => 'error'));
                    }
                }
            } catch (Exception $e) {
                Log::error($e);
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Satış Bilgisi Güncellenemedi', 'text' => 'Satış Bilgisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }}catch(Exception $e){
            Log::error($e);
            DB::rollBack();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Satış Bilgisi Güncellenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getSayacsatisgoster($id){
        try {
            $sayacsatis = SubeSayacSatis::find($id);
            $sayacsatis->abone = Abone::withTrashed()->where('id',$sayacsatis->abone_id)->first();
            $sayacsatis->uretimyeri = UretimYer::find($sayacsatis->uretimyer_id);
            $sayacsatis->netsiscari = NetsisCari::find($sayacsatis->netsiscari_id);
            $sayacsatis->il = Iller::find($sayacsatis->iller_id);
            $sayacsatis->ilce = Ilceler::find($sayacsatis->ilceler_id);
            $urunlist = explode(',', $sayacsatis->secilenler);
            $birimfiyatlist = explode(',', $sayacsatis->birimfiyat);
            $secilenler = array();
            $baglantidurum = array();
            for ($i = 0; $i < count($urunlist); $i++) {
                $subeurun = SubeUrun::find($urunlist[$i]);
                $subeurun->netsisstokkod=NetsisStokKod::find($subeurun->netsisstokkod_id);
                array_push($secilenler, $subeurun);
                $birimfiyatlist[$i] = number_format($birimfiyatlist[$i], 2,'.','');
                array_push($baglantidurum, $subeurun->baglanti);
            }
            $sayacsatis->odemesekli .= $sayacsatis->taksit>1 ? ' - '.$sayacsatis->taksit.' TAKSİT' : '';
            $sayacsatis->odemesekli .= $sayacsatis->taksit2>1 ? ' - '.$sayacsatis->taksit2.' TAKSİT' : '';
            $sayacsatis->baglanti = $baglantidurum;
            $sayacsatis->urunler = $secilenler;
            $sayacsatis->parabirimi = ParaBirimi::find($sayacsatis->parabirimi_id);
            $sayacsatis->adetler = explode(',', $sayacsatis->adet);
            $sayacsatis->fiyatlar = $birimfiyatlist;
            $sayacsatis->ucretsizler = explode(',', $sayacsatis->ucretsiz);
            $sayaclar = explode(';', $sayacsatis->sayaclar);
            $abonesayaclar = array();
            for ($i = 0; $i < count($sayaclar); $i++) {
                $sayaclist = explode(',', $sayaclar[$i]);
                $serinolar = "";
                foreach ($sayaclist as $sayac) {
                    if ($sayac != 0) {
                        $abonesayac = AboneSayac::find($sayac);
                        $serinolar .= ($serinolar == "" ? "" : ", ") . $abonesayac->serino;
                    }
                }
                array_push($abonesayaclar, $serinolar);
            }
            $sayacsatis->abonesayaclar = $abonesayaclar;
            $parabirimi = ParaBirimi::find(1);
            if (is_null($sayacsatis->kurtarihi))
                $dovizkuru = DovizKuru::orderBy('tarih', 'desc')->orderBy('parabirimi_id','asc')->take(3)->get();
            else
                $dovizkuru = DovizKuru::where('tarih', $sayacsatis->kurtarihi)->orderBy('tarih', 'desc')->orderBy('parabirimi_id','asc')->take(3)->get();
            foreach ($dovizkuru as $doviz) {
                $sayacsatis->kurtarihi=$doviz->tarih;
            }
            $sayacsatis->kurtarihi = date("d-m-Y", strtotime($sayacsatis->kurtarihi));
            return View::make($this->servisadi.'.sayacsatisgoster',array('sayacsatis'=>$sayacsatis,'parabirimi'=>$parabirimi,'dovizkuru'=>$dovizkuru))->with(array('title'=>'Satış Bilgisi'));

        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Satış Bilgisi Bulunamadı', 'text' =>'Satış Bilgisinde eksik ya da hatalı bilgi mevcut!', 'type' => 'error'));
        }
    }

    public function getAbonesayac(){
        try {
            $id=Input::get('id');
            $abone = Abone::find($id);
            $ilceler = array();
            $abone->uretimyer = UretimYer::find($abone->uretimyer_id);
            $sube=Sube::where('subekodu',$abone->subekodu)->where('aktif',1)->first();
            $sube->netsiscari = NetsisCari::find($sube->netsiscari_id);
            $netsiscari = $sube->netsiscari;
            $sube->depo = NetsisDepolar::find($sube->netsisdepolar_id);
            if (Input::has('satisid')) {
                $satisid=Input::get('satisid');
                $sayacsatis = SubeSayacSatis::find($satisid);
                $abonesayacidleri = AboneTahsis::where('abone_id', $id)->get(array('abonesayac_id'))->toArray();
                if ($sayacsatis && $sayacsatis->abone_id == $id) {
                    $sayaclar = explode(';', $sayacsatis->sayaclar);
                    $abonesayaclar = array();
                    for ($i = 0; $i < count($sayaclar); $i++) {
                        $sayaclist = explode(',', $sayaclar[$i]);
                        $abonesayaclar = array_merge($abonesayaclar, $sayaclist);
                    }
                    $sayacsatis->abonesayaclar = $abonesayaclar;
                    $abonesayaclari = AboneSayac::whereIn('id', $abonesayacidleri)->where(function ($query) use ($sayacsatis) {
                        $query->where('satisdurum', 0)->orWhereIn('id', $sayacsatis->abonesayaclar);
                    })->get(array("id","serino"))->toArray();

                } else {
                    $abonesayaclari = AboneSayac::whereIn('id', $abonesayacidleri)->where('satisdurum', 0)->get(array("id","serino"))->toArray();
                }
            } else {
                if (is_null($abone->iller_id) || is_null($abone->ilceler_id)) {
                    $netsiscari->il = Iller::where('nadi',$netsiscari->nil)->first();
                    if($netsiscari->il){
                        $abone->iller_id=$netsiscari->il->id;
                        $netsiscari->ilce = Ilceler::where('iller_id',$netsiscari->il->id)->where('nadi',$netsiscari->nilce)->first();
                        if($netsiscari->ilce)
                            $abone->ilceler_id=$netsiscari->ilce->id;
                        $ilceler = Ilceler::where('iller_id', $netsiscari->il->id)->get();
                    }
                }else{
                    $ilceler = Ilceler::where('iller_id', $abone->iller_id)->get();
                }
                $abonesayacidleri = AboneTahsis::where('abone_id', $id)->get(array('abonesayac_id'))->toArray();
                $abonesayaclari = AboneSayac::whereIn('id', $abonesayacidleri)->where('satisdurum', 0)->get(array("id","serino"))->toArray();
            }
            $urunler=array();
            if(Input::has('urun')) {
                $urunler = SubeUrun::where('subekodu', $sube->subekodu)->where('durum', 1)->get();
                foreach($urunler as $urun){
                    $urun->netsisstokkod = NetsisStokKod::find($urun->netsisstokkod_id);
                    $urun->parabirimi=Parabirimi::find($urun->parabirimi_id);
                    $urun->stok=StBakiye::where('STOK_KODU',$urun->netsisstokkod->kodu)->where('DEPO_KODU',$urun->depokodu)->first();
                    if(!$urun->stok)
                        $urun->stok=new StBakiye(array('BAKIYE'=>0));
                }
            }
            return Response::json(array('durum'=>true, 'abone'=>$abone, 'abonesayaclari' => $abonesayaclari,'urunler'=>$urunler,'ilceler'=>$ilceler));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title' => 'Abone Sayaç Bilgilerinde Hata Var!', 'text' => str_replace("'","\'",$e->getMessage()), 'type'=>'warning'));
        }
    }

    public function getFaturailceler(){
        try {
            $id=Input::get('id');
            $ilceler = Ilceler::where('iller_id', $id)->get();
            return Response::json(array('durum'=>true,'ilceler'=>$ilceler));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title' => 'Netsis Bilgisinde Hata Var!', 'text' => str_replace("'","\'",$e->getMessage()), 'type'=>'warning'));
        }
    }

    public function getUrunler(){
        try {
            $id=Input::get('id');
            $abone = Abone::find($id);
            $abone->uretimyer = UretimYer::find($abone->uretimyer_id);
            $urunler=SubeUrun::where('subekodu',$abone->subekodu)->where('durum',1)->get();
            $sube=Sube::where('subekodu',$abone->subekodu)->where('aktif',1)->first();
            $sube->netsiscari = NetsisCari::find($sube->netsiscari_id);
            $sube->depo = NetsisDepolar::find($sube->netsisdepolar_id);
            foreach($urunler as $urun){
                $urun->netsisstokkod = NetsisStokKod::find($urun->netsisstokkod_id);
                $urun->parabirimi=Parabirimi::find($urun->parabirimi_id);
                $urun->stok=StBakiye::where('STOK_KODU',$urun->netsisstokkod->kodu)->where('DEPO_KODU',$urun->depokodu)->first();
                if(!$urun->stok)
                    $urun->stok=new StBakiye(array('BAKIYE'=>0));
            }
            return Response::json(array('durum'=>true,'urunler'=>$urunler));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title' => 'Abone Sayaç Bilgilerinde Hata Var!', 'text' => str_replace("'","\'",$e->getMessage()), 'type'=>'warning'));
        }
    }

    public function getSayacsatissil($id){
        try {
            DB::beginTransaction();
            $sayacsatis = SubeSayacSatis::find($id);
            $bilgi = clone $sayacsatis;
            $sayaclar = explode(';', $sayacsatis->sayaclar);
            foreach ($sayaclar as $kalemsayac) {
                $sayaclist = explode(',', $kalemsayac);
                foreach ($sayaclist as $sayac) {
                    AboneSayac::where('id', $sayac)->update(['satisdurum' => 0]);
                }
            }
            $durum = BackendController::SatisFaturasiSil($sayacsatis);
            if ($durum == 1) {
                $sayacsatis->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $bilgi->id . ' Numaralı Satış Bilgisi Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Sayaç Satış Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Satış Kayıdı Silindi', 'text' => 'Satış Kayıdı Başarıyla Silindi.', 'type' => 'success'));
            } else {
                DB::rollBack();
                if ($durum == 13) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Satış Kayıdı Silinemedi', 'text' => 'Fatura Bulunamadı', 'type' => 'error'));
                } else if ($durum == 14) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Satış Kayıdı Silinemedi', 'text' => 'Fatura Açıklaması Bulunamadı', 'type' => 'error'));
                } else if ($durum == 15) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Satış Kayıdı Silinemedi', 'text' => 'Fatura Kalemleriinden Birisi Bulunamadı', 'type' => 'error'));
                } else if ($durum == 16) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Satış Kayıdı Silinemedi', 'text' => 'Faturaya Ait Seri Numaralar Silinemedi', 'type' => 'error'));
                } else if ($durum == 17) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Satış Kayıdı Silinemedi', 'text' => 'Faturaya Ait Kasa Kaydı Silinemedi', 'type' => 'error'));
                } else if ($durum == 18) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Satış Kayıdı Silinemedi', 'text' => 'Faturaya Ait Cari Hareket Silinemedi', 'type' => 'error'));
                } else if ($durum == 19) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Satış Kayıdı Silinemedi', 'text' => 'Faturaya Ait Muhasabe Fişleri Silinemedi', 'type' => 'error'));
                } else if ($durum == 20) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Satış Kayıdı Silinemedi', 'text' => 'Faturaya Ait Servis Kaydı Bilgisi Silinemedi', 'type' => 'error'));
                } else if ($durum == 21) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Satış Kayıdı Silinemedi', 'text' => 'Eski Fatura Silinemedi!', 'type' => 'error'));
                } else {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Satış Kayıdı Silinemedi', 'text' => $durum, 'type' => 'error'));
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Satış Kayıdı Silinemedi', 'text' => 'Satış Kayıdı Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function postSayacsatis()
    {
        try {
            $id = Input::get('fatura');
            $sayacsatis = SubeSayacSatis::find($id);
            if ($sayacsatis->faturano == "") {
                return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Satış Faturası Kaydedilmeden Çıkış Yapılmış', 'type' => 'warning', 'title' => 'Fatura Hatası'));
            }else if($sayacsatis->efatura){
                return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'E-Fatura Olarak Kaydedilen Fatura Çıktı Olarak Alınamaz!', 'type' => 'warning', 'title' => 'E-Fatura Hatası'));
            }
            $earsivfatura = EarsivFatura::where('SIRKET',$sayacsatis->db_name)->where('SUBE_KODU',$sayacsatis->subekodu)->where('FATURA_NO',$sayacsatis->faturano)->first();
            if($earsivfatura){
                return Redirect::to($earsivfatura->FATURA_URL);
            }
            $sayacsatis->abone = Abone::find($sayacsatis->abone_id);
            $raporadi = "SatisFaturasi-" . Str::slug($sayacsatis->abone->adisoyadi);
            $export = "pdf";
            $kriterler = array();
            $kriterler['id'] = $id;

            JasperPHP::process(public_path('reports/satisfaturasi/satisfaturasi.jasper'), public_path('reports/outputs/satisfaturasi/' . $raporadi),
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
            readfile("reports/outputs/satisfaturasi/" . $raporadi . "." . $export . "");
            File::delete("reports/outputs/satisfaturasi/" . $raporadi . "." . $export . "");
            BackendController::BasimLogEkle($sayacsatis->id);
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Satış Faturası Bulunamadı', 'type' => 'warning', 'title' => 'Fatura Hatası'));
        }
    }

    public function postSayacsatisgoster()
    {
        try {
            $id = Input::get('fatura');
            $sayacsatis = SubeSayacSatis::find($id);
            if ($sayacsatis->faturano == "") {
                return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Satış Faturası Kaydedilmeden Çıkış Yapılmış', 'type' => 'warning', 'title' => 'Fatura Hatası'));
            }else if($sayacsatis->efatura){
                return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'E-Fatura Olarak Kaydedilen Fatura Çıktı Olarak Alınamaz!', 'type' => 'warning', 'title' => 'E-Fatura Hatası'));
            }
            $earsivfatura = EarsivFatura::where('SIRKET',$sayacsatis->db_name)->where('SUBE_KODU',$sayacsatis->subekodu)->where('FATURA_NO',$sayacsatis->faturano)->first();
            if($earsivfatura){
                return Redirect::to($earsivfatura->FATURA_URL);
            }
            $sayacsatis->abone = Abone::find($sayacsatis->abone_id);
            $raporadi = "SatisFaturasiBaski-" . Str::slug($sayacsatis->abone->adisoyadi);
            $export = "pdf";
            $kriterler = array();
            $kriterler['id'] = $id;
            JasperPHP::process(public_path('reports/satisfaturasibaski/satisfaturasibaski.jasper'), public_path('reports/outputs/satisfaturasibaski/' . $raporadi),
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
            readfile("reports/outputs/satisfaturasibaski/" . $raporadi . "." . $export . "");
            File::delete("reports/outputs/satisfaturasibaski/" . $raporadi . "." . $export . "");
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Satış Faturası Bulunamadı', 'type' => 'warning', 'title' => 'Fatura Hatası'));
        }
    }

    public function getSayackayit() {
        return View::make($this->servisadi.'.sayackayit')->with(array('title'=>'Arızalı Sayaç Kayıdı'));
    }

    public function postSayackayitlist() {
        $netsiscari_id=Input::get('netsiscari_id');
        $hatirlatma_id=Input::get('hatirlatma_id');
        $sube = null;
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

        }else if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $sube=Sube::whereIn('netsiscari_id',$netsiscarilist)->where('aktif',1)->first();
            if($sube) {
                $query = SayacGelen::where('sayacgelen.subekodu', $sube->subekodu)
                    ->select(array("sayacgelen.id", "sayacgelen.serino", "uretimyer.yeradi", "servisstokkod.stokadi", "sayacgelen.depotarihi", "kullanici.adi_soyadi",
                        "sayacgelen.eklenmetarihi", "servistakip.eskiserino", "sayacgelen.gdepotarihi", "sayacgelen.gdurumtarihi", "uretimyer.nyeradi", "servisstokkod.nstokadi",
                        "kullanici.nadi_soyadi", "sayacgelen.arizakayit", "sayacgelen.depolararasi"))
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
            ->addColumn('islemler',function ($model) use($netsiscari_id) {
                $root = BackendController::getRootDizin();
                if(!$model->arizakayit && !$model->depolararasi and $netsiscari_id)
                    return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/sayackayitduzenle/".$model->id."' > Düzenle </a>";
                else
                    return "<a class='btn btn-sm btn-info' href='".$root."/".$this->servisadi."/sayackayitgoster/".$model->id."' > Göster </a>";
            })
            ->make(true);
    }

    public function getSayackayitekle() {
        $netsiscariid=Auth::user()->netsiscari_id;
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        if($sube) {
            $sube->netsiscari = NetsisCari::find($sube->netsiscari_id);
            $uretimyerid=CariYer::whereIn('netsiscari_id',$netsiscariid)->where('durum',1)->get(array('uretimyer_id'))->toArray();
            $uretimyerleri = UretimYer::whereIn('id',$uretimyerid)->get();
            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->get(array('netsiscari_id'))->toArray();
            $netsiscariler = NetsisCari::where('caridurum', 'A')->whereIn('caritipi', array('A', 'D'))
                ->where(function ($query) use ($subeyetkili, $sube) {$query->whereIn('id', $subeyetkili)->orwhereIn('subekodu', array(-1, $sube->subekodu));})
                ->whereNotIn('carikod', (function ($query) use ($sube) {$query->select('carikod')->from('kodharichar')->where('subekodu', $sube->subekodu);}))
                ->whereNotIn('id', array($sube->netsiscari_id))
                ->orderBy('cariadi', 'asc')->get();
            $sayacturlist=explode(',',$sube->sayactur);
            $sayacadilist=explode(',',$sube->sayacadlari);
            $sayacturleri = SayacTur::whereIn('id',$sayacturlist)->get();
            $sayacadlari = SayacAdi::whereIn('sayactur_id',$sayacturlist)->whereIn('id',$sayacadilist)->get();
            $sayaccaplari = SayacCap::all();
            $sayacadilist=SayacAdi::whereIn('sayactur_id',$sayacturlist)->whereIn('id',$sayacadilist)->get(array('id'))->toArray();
            $sayacparcalari = SayacParca::whereIn('sayacadi_id',$sayacadilist)->get(array('servisstokkod_id'))->toArray();
            $servisstokkodlari = ServisStokKod::where('servisid','<>',0)->whereIn('servisid',$sayacturlist)->whereIn('id',$sayacparcalari)->where('koddurum',true)->get();
            return View::make($this->servisadi.'.sayackayitekle',array('sube'=>$sube,'uretimyerleri'=>$uretimyerleri,'netsiscariler'=>$netsiscariler,'sayacturleri'=>$sayacturleri,'sayacadlari'=>$sayacadlari,'sayaccaplari'=>$sayaccaplari,'servisstokkodlari'=>$servisstokkodlari))->with(array('title'=>'Arızalı Sayaç Kayıdı Ekle'));
        }else {
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkisiz', 'text' => 'Sayaç Kayıdı Ekleme Yetkiniz Yok', 'type' => 'error'));
        }
 }

    public function getAbonebilgi()
    {
        try {
            $serino=Input::get('serino');
            $uretimyer=Input::get('uretimyer');
            $subekodu=Input::get('subekodu');
            $abone=Input::get('abone');
            $sube = Sube::where('subekodu',$subekodu)->where('aktif',1)->first();
            if(!$sube){
                return Response::json(array('durum' => false, 'title' => 'Şube Aktif Değil!', 'text' => 'Kullanıcının bulunduğu şube aktif değil. Yetkili ile görüşüp şubenin aktifliği düzeltilmeli!', 'type' => 'error'));
            }
            $sayacturleri = explode(',',$sube->sayactur);
            if($abone){
                $abonesayac = AboneSayac::where('serino', $serino)->first();
                if ($abonesayac) {
                    $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                    if ($abonetahsis) {
                        if($abone!=$abonetahsis->abone_id){
                            return Response::json(array('durum' => false, 'title' => 'Sayaç Başka Aboneye Kayıtlı', 'text' => 'Sistemde bu seri numarası başka abone üzerinde kayıtlı.Önce diğer tahsisi kaldırın!', 'type' => 'error'));
                        }else{
                            $abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$serino)->where('durum',2)->first();
                            $abonesayac->adres=($abonebilgi && $abonebilgi->faturaadresi!=null) ? $abonebilgi->faturaadresi : '';
                        }
                        return Response::json(array('durum' => true, 'sayac' => $abonesayac));
                    } else {
                        return Response::json(array('durum' => true, 'sayac' => $abonesayac));
                    }
                }else{
                    $sayaclar = Sayac::where('serino', $serino)->where('uretimyer_id', $uretimyer)->get();
                    if ($sayaclar->count() > 0) {
                        if($sayaclar->count()> 1){
                            $sayac=$sayaclar[0];
                            foreach ($sayaclar as $sayacc){
                                if($sayacc->sayactur_id!=null){
                                    if(in_array($sayacc->sayactur_id,$sayacturleri)){
                                        $sayac=$sayacc;
                                        break;
                                    }
                                }
                            }
                            if($sayac->sayactur_id!=null) {
                                if (!in_array($sayac->sayactur_id, $sayacturleri)) {
                                    return Response::json(array('durum' => false, 'title' => 'Sayaç Türü Şubeye Kayıtlı Değil', 'text' => 'Sistemde şubeye kayıtlı bu serino için sayaç türü bulunamadı.', 'type' => 'error'));
                                }
                            }
                            $abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$serino)->where('durum',2)->first();
                            $sayac->adres=($abonebilgi && $abonebilgi->faturaadresi!=null) ? $abonebilgi->faturaadresi : '';
                            return Response::json(array('durum' => true, 'sayac'=>$sayac));
                        }else{
                            $sayac=$sayaclar[0];
                            if($sayac->sayactur_id!=null) {
                                if (!in_array($sayac->sayactur_id, $sayacturleri)) {
                                    return Response::json(array('durum' => false, 'title' => 'Sayaç Türü Şubeye Kayıtlı Değil', 'text' => 'Sistemde şubeye kayıtlı bu serino için sayaç türü bulunamadı.', 'type' => 'error'));
                                }
                            }
                            $abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$serino)->where('durum',2)->first();
                            $sayac->adres=($abonebilgi && $abonebilgi->faturaadresi!=null) ? $abonebilgi->faturaadresi : '';
                            return Response::json(array('durum' => true, 'sayac'=>$sayac));
                        }
                    } else {
                        return Response::json(array('durum' => false, 'title' => 'Sayaç Bilgisi Bulunamadı', 'text' => 'Sistemde bu seri no kayıtlı değil ya da bu şubeye bağlı yerlere ait değil.', 'type' => 'error'));
                    }
                }
            }else{
                $abonesayac = AboneSayac::where('serino', $serino)->first();
                if ($abonesayac) {
                    $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                    if ($abonetahsis) {
                        return Response::json(array('durum' => false, 'title' => 'Sayaç Başka Aboneye Kayıtlı', 'text' => 'Sistemde bu seri numarası başka abone üzerinde kayıtlı.Önce diğer tahsisi kaldırın!', 'type' => 'error'));
                    } else {
                        return Response::json(array('durum' => true, 'sayac'=>$abonesayac));
                    }
                } else {
                    $sayaclar = Sayac::where('serino', $serino)->where('uretimyer_id', $uretimyer)->get();
                    if ($sayaclar->count() > 0) {
                        if($sayaclar->count()> 1){
                            $sayac=$sayaclar[0];
                            foreach ($sayaclar as $sayacc){
                                if($sayacc->sayactur_id!=null){
                                    if(in_array($sayacc->sayactur_id,$sayacturleri)){
                                        $sayac=$sayacc;
                                        break;
                                    }
                                }
                            }
                            if($sayac->sayactur_id!=null) {
                                if (!in_array($sayac->sayactur_id, $sayacturleri)) {
                                    return Response::json(array('durum' => false, 'title' => 'Sayaç Türü Şubeye Kayıtlı Değil', 'text' => 'Sistemde şubeye kayıtlı bu serino için sayaç türü bulunamadı.', 'type' => 'error'));
                                }
                            }
                            $abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$serino)->where('durum',2)->first();
                            $sayac->adres=($abonebilgi && $abonebilgi->faturaadresi!=null) ? $abonebilgi->faturaadresi : '';
                            return Response::json(array('durum' => true, 'sayac'=>$sayac));
                        }else{
                            $sayac=$sayaclar[0];
                            if($sayac->sayactur_id!=null) {
                                if (!in_array($sayac->sayactur_id, $sayacturleri)) {
                                    return Response::json(array('durum' => false, 'title' => 'Sayaç Türü Şubeye Kayıtlı Değil', 'text' => 'Sistemde şubeye kayıtlı bu serino için sayaç türü bulunamadı.', 'type' => 'error'));
                                }
                            }
                            $abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$serino)->where('durum',2)->first();
                            $sayac->adres=($abonebilgi && $abonebilgi->faturaadresi!=null) ? $abonebilgi->faturaadresi : '';
                            return Response::json(array('durum' => true, 'sayac'=>$sayac));
                        }
                    } else {
                        return Response::json(array('durum' => false, 'title' => 'Sayaç Bilgisi Bulunamadı', 'text' => 'Sistemde bu seri no kayıtlı değil.', 'type' => 'error'));
                    }
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'title' => 'Sayaç Bilgisi Getirilirken Hata Oluştu', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
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
            $subekodu = Input::get('subekodu');
            $uretimyerleri = Input::get('uretimyerleri');
            $netsiscari_id = Input::get('cariadi');
            $serinolar = Input::get('serino');
            $serviskodlari = Input::get('serviskodlari');
            $sayacadlari = Input::get('sayacadlari');
            $sayaccaplari = Input::get('sayaccaplari');
            $nedenler = Input::get('neden');
            $takilmatarihleri = Input::get('takilmatarihi');
            $endeksler = Input::get('endeks');
            $netsiscari = NetsisCari::find($netsiscari_id);
            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif',1)->first();
            if (!$subeyetkili){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkili Değil', 'text' => 'Kullanıcı Şube için Yetkili Olarak Eklenmemiş!', 'type' => 'error'));
            }
            if (count($serinolar) > 0) {
                for ($i = 0; $i < count($serinolar); $i++) {
                    $serino1 = $serinolar[$i];
                    $sayacadi=$sayacadlari[$i];
                    $yeri = $uretimyerleri[$i];
                    if ($serino1 == "")
                        continue;
                    if(BackendController::SayacDurum($serino1,$yeri,$this->servisid,false,false,$sayacadi)){
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
                $sayaclar = BackendController::SubeDepoGirisGrupla($serinolar, $uretimyerleri, $serviskodlari, $sayacadlari, $sayaccaplari, $nedenler, $takilmatarihleri, $endeksler);
                if (count($sayaclar) == 0) {
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Depo Sayaç Girişi Kaydedilemedi', 'text' => 'Girilen Sayaçların Seri Numaraları yazılmamış', 'type' => 'error'));
                }
                DB::beginTransaction();
                if (Input::has('belgeli')) {
                    $belgeno = BackendController::FaturaNo(Input::get('belgeno'), 0);
                } else {
                    try {
                        $faturano = Fatuno::where('SUBE_KODU', $subeyetkili->subekodu)->where('SERI', 'Z')->where('TIP', '9')->first();
                        if (!$faturano) {
                            $faturano = new Fatuno;
                            $faturano->SUBE_KODU = $subeyetkili->subekodu;
                            $faturano->SERI = 'Z';
                            $faturano->TIP = 9;
                            $faturano->NUMARA = 'Z' . '0';
                            $faturano->save();
                        }
                        $belgeno = BackendController::FaturaNo($faturano->NUMARA, 1);
                        Fatuno::where(['SUBE_KODU'=>$subeyetkili->subekodu,'SERI' => 'Z','TIP' => '9'])->update(['NUMARA' => $belgeno]);
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Fatura Numarası Kaydedilemedi', 'text' => 'Girilen Sayaçlar için Fatura Numarası Alınamadı', 'type' => 'error'));
                    }
                }
                $depogiris = BackendController::NetsisSubeDepoGiris($sayaclar,$gelistarih,$netsiscari_id,$belgeno);
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
                                    $secilenler = "";
                                    $sayac = $sayacgrup['sayac']; //eklenecek sayaçlar
                                    foreach ($sayac as $girilecek) {
                                        $serino = $girilecek['serino'];
                                        $sayacadi = $girilecek['sayacadi'];
                                        $sayaccap = $girilecek['sayaccap'];
                                        $uretimyeri = $girilecek['uretimyeri'];
                                        $sokulmenedeni = $girilecek['neden'];
                                        $takilmatarih = $girilecek['takilmatarihi'];
                                        $endeks = $girilecek['endeks'];
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
                                                $sayacgelen->subekodu=$subekodu;
                                                $sayacgelen->kullanici_id = Auth::user()->id;
                                                $sayacgelen->beyanname=-2;
                                                $sayacgelen->sokulmenedeni = $sokulmenedeni;
                                                $sayacgelen->takilmatarihi = $takilmatarih=="" ? NULL : date("Y-m-d", strtotime($takilmatarih));
                                                $sayacgelen->endeks = $endeks;
                                                $sayacgelen->save();
                                                $biten++;
                                                $adet++;
                                                $eklenenler .= ($eklenenler == "" ? "" : ",") . $serino;
                                                $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelen->id;
                                                $servistakip = new ServisTakip;
                                                $servistakip->serino = $sayacgelen->serino;
                                                $servistakip->sayacadi_id = $sayacgelen->sayacadi_id;
                                                $servistakip->depogelen_id = $sayacgelen->depogelen_id;
                                                $servistakip->netsiscari_id = $sayacgelen->netsiscari_id;
                                                $servistakip->sayacgelen_id = $sayacgelen->id;
                                                $servistakip->servis_id = $sayacgelen->servis_id;
                                                $servistakip->uretimyer_id = $sayacgelen->uretimyer_id;
                                                $servistakip->durum = 1;
                                                $servistakip->subedurum = 1;
                                                $servistakip->subekodu=$subekodu;
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
                                            $depolararasi = Depolararasi::where('servis_id', $this->servisid)->where('netsiscari_id', $netsiscari_id)->where('depokodu', $depogelen->depokodu)
                                                ->where('tipi',0)->where('depodurum',0)->first();
                                            if ($depolararasi) {
                                                $depolararasi->secilenler .= ($depolararasi->secilenler == "" ? "" : ",") . $secilenler; //bu bilgiler yok
                                                $depolararasi->sayacsayisi += $biten;
                                            } else {
                                                $depolararasi = new Depolararasi;
                                                $depolararasi->servis_id = $this->servisid;
                                                $depolararasi->netsiscari_id = $netsiscari_id;
                                                $depolararasi->secilenler = $secilenler;
                                                $depolararasi->sayacsayisi = $biten;
                                                $depolararasi->depodurum = 0;
                                                $depolararasi->depokodu = $depogelen->depokodu;
                                            }
                                            $depolararasi->save();
                                        } catch (Exception $e) {
                                            Log::error($e);
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Yapılamadı', 'text' => 'Depolararası Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                        }
                                        try {
                                            BackendController::HatirlatmaGuncelle(2,$netsiscari_id,$servisid,$biten,$depogelen->id,$depogelen->servisstokkodu);
                                            BackendController::DepoDurumGuncelle($depogelen->id);
                                            BackendController::HatirlatmaEkle(3, $netsiscari_id, $servisid, $biten,$depogelen->id,$depogelen->servisstokkodu);
                                            BackendController::HatirlatmaEkle(11, $netsiscari_id, $servisid, $biten,$depogelen->id,$depogelen->servisstokkodu);
                                            BackendController::BildirimEkle(2, $netsiscari_id, $servisid, $biten,$depogelen->id,$depogelen->servisstokkodu);
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
                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $netsiscari->cariadi . ' Yerine Ait ' . $adet . ' Adet Sayacın Şube Sayaç Kayıdı Yapıldı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Seri Numaraları:' . $eklenenler);
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

    public function getSayackayitduzenle($id) {
        $sayacgelen = SayacGelen::find($id);
        if ($sayacgelen->arizakayit) {
            return Redirect::to($this->servisadi.'/sayackayit')->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Güncellenemez', 'text' => 'Sayaç İçin Arıza Kayıdı Var!', 'type' => 'error'));
        }
        $netsiscariid=NetsisCari::whereIn('id',Auth::user()->netsiscari_id)->get(array('id'))->toArray();
        $uretimyerid=CariYer::whereIn('netsiscari_id',$netsiscariid)->where('durum',1)->get(array('uretimyer_id'))->toArray();
        $uretimyerleri = UretimYer::whereIn('id',$uretimyerid)->get();
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
            $neden = Input::get('neden');
            $takilmatarihi = Input::get('takilmatarihi');
            $endeks = str_replace(',', '.', Input::get('endeks'));
            if ($servistakip) {
                if ($servistakip->arizakayit_id == null)
                    if (BackendController::SayacDurum($serino,$uretimyer_id, $sayacgelen->servis_id, 0, $servistakip->id)) {
                        Input::flash();
                        DB::rollBack();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => $serino . ' Nolu Sayacın Depo Çıkışı Yapılmamış.Önce Sayaca Ait Diğer İşlemleri Bitiriniz.', 'type' => 'error'));
                    }
                $abonesayac = AboneSayac::where('serino',$sayacgelen->serino)->where('uretimyer_id',$sayacgelen->uretimyer_id)->first();
                if($abonesayac){
                    $abonetahsis = AboneTahsis::where('abonesayac_id',$abonesayac->id)->first();
                    if($abonetahsis){
                        $serviskayit = ServisKayit::where('depogelen_id',$sayacgelen->depogelen_id)->where('abonetahsis_id',$abonetahsis->id)->first();
                        if($serviskayit){
                            $serviskayit->aciklama = $neden;
                            $serviskayit->takilmatarihi = $takilmatarihi=="" ? NULL : date("Y-m-d", strtotime($takilmatarihi));
                            $serviskayit->ilkendeks = $endeks;
                            $serviskayit->save();
                        }
                    }
                }

                $sayacgelen->serino = $serino;
                $sayacgelen->uretimyer_id = $uretimyer_id;
                $sayacgelen->kullanici_id = Auth::user()->id;
                $sayacgelen->sokulmenedeni = $neden;
                $sayacgelen->takilmatarihi = $takilmatarihi=="" ? NULL : date("Y-m-d", strtotime($takilmatarihi));
                $sayacgelen->endeks = $endeks;
                $servistakip->serino = $serino;
                $servistakip->uretimyer_id = $uretimyer_id;
                $servistakip->save();
                $sayacgelen->save();

                BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $netsiscari->cariadi . ' Yerine Ait ' . $bilgi->id . ' Numaralı Şube Sayaç Kayıdı Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Şube Sayaç Kayıt Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::to($this->servisadi . '/sayackayit')->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Güncellendi', 'text' => 'Sayaç Kayıdı Başarıyla Güncellendi', 'type' => 'success'));
            } else {
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Güncellenemedi', 'text' => 'Sayacın Servis Bilgisi Düzgün Kaydedilmemiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Güncellenemedi', 'text' => str_replace("'","'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getSayackayitgoster($id) {
        try {
        $sayacgelen = SayacGelen::find($id);
        $sayacgelen->servistakip=ServisTakip::where('sayacgelen_id',$sayacgelen->id)->first();
        $netsiscariid = NetsisCari::whereIn('id', Auth::user()->netsiscari_id)->get(array('id'))->toArray();
        $uretimyerid = CariYer::whereIn('netsiscari_id', $netsiscariid)->get(array('uretimyer_id'))->toArray();
        $uretimyerleri = UretimYer::whereIn('id', $uretimyerid)->get();
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
            return View::make($this->servisadi.'.arizakayit',array('hatirlatma_id'=>$hatirlatma_id))->with(array('title'=>$this->servisbilgi.' Arıza Kayıdı'));
        else
            return View::make($this->servisadi.'.arizakayit')->with(array('title'=>$this->servisbilgi.' Arıza Kayıdı'));
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
            if ($arizakayit->arizakayit_durum == 2) { //hurda ise
                BackendController::HatirlatmaSil(11,$netsiscari->id,$sayacgelen->servis_id,1,$depogelen->id,$depogelen->servisstokkodu);
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
            try {
                $depolararasi = Depolararasi::where('servis_id', $this->servisid)->where('netsiscari_id', $netsiscari->id)->where('depokodu', $depogelen->depokodu)
                    ->where('tipi',0)->where('depodurum',0)->first();
                if ($depolararasi) {
                    $depolararasi->secilenler .= ($depolararasi->secilenler == "" ? "" : ",") . $sayacgelen->id; //bu bilgiler yok
                    $depolararasi->sayacsayisi += 1;
                } else {
                    $depolararasi = new Depolararasi;
                    $depolararasi->servis_id = $this->servisid;
                    $depolararasi->netsiscari_id = $netsiscari->id;
                    $depolararasi->secilenler = $sayacgelen->id;
                    $depolararasi->sayacsayisi = 1;
                    $depolararasi->depodurum = 0;
                    $depolararasi->depokodu = $depogelen->depokodu;
                }
                $depolararasi->save();
            } catch (Exception $e) {
                Log::error($e);
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıt Silinemedi', 'text' => 'Depolararası Kayıdı Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }
            BackendController::HatirlatmaGeriAl(3,$netsiscari->id,$sayacgelen->servis_id,1,$depogelen->id,$depogelen->servisstokkodu);
            BackendController::HatirlatmaEkle(11,$netsiscari->id,$sayacgelen->servis_id,1,$depogelen->id,$depogelen->servisstokkodu);
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
            $query = ArizaKayit::where('sayacgelen.netsiscari_id',$netsiscariid)
                ->select(array("arizakayit.id", "sayacgelen.serino", "sayacadi.sayacadi", "uretimyer.yeradi","arizakayit.gdurum","kullanici.adi_soyadi",
                    "arizakayit.arizakayittarihi","servistakip.eskiserino","arizakayit.garizakayittarihi","sayacadi.nsayacadi","uretimyer.nyeradi",
                    "arizakayit.ndurum","kullanici.nadi_soyadi","sayacgelen.fiyatlandirma", "sayacgelen.depoteslim","sayacgelen.servis_id"))
                ->leftjoin("sayacgelen", "arizakayit.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("servistakip", "servistakip.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("uretimyer", "sayacgelen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("sayacadi", "arizakayit.sayacadi_id", "=", "sayacadi.id")
                ->leftjoin("kullanici", "arizakayit.arizakayit_kullanici_id", "=", "kullanici.id");
        }else if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $query = ArizaKayit::whereIn('arizakayit.netsiscari_id',$netsiscarilist)
                ->select(array("arizakayit.id", "sayacgelen.serino", "sayacadi.sayacadi", "uretimyer.yeradi","arizakayit.gdurum","kullanici.adi_soyadi",
                    "arizakayit.arizakayittarihi","servistakip.eskiserino","arizakayit.garizakayittarihi","sayacadi.nsayacadi","uretimyer.nyeradi",
                    "arizakayit.ndurum","kullanici.nadi_soyadi","sayacgelen.fiyatlandirma", "sayacgelen.depoteslim","sayacgelen.servis_id"))
                ->leftjoin("sayacgelen", "arizakayit.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("servistakip", "servistakip.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("uretimyer", "sayacgelen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("sayacadi", "arizakayit.sayacadi_id", "=", "sayacadi.id")
                ->leftjoin("kullanici", "arizakayit.arizakayit_kullanici_id", "=", "kullanici.id");
        }else{
            $query = ArizaKayit::where('sayacgelen.servis_id',$this->servisid)
                ->select(array("arizakayit.id", "sayacgelen.serino", "sayacadi.sayacadi", "uretimyer.yeradi","arizakayit.gdurum","kullanici.adi_soyadi",
                    "arizakayit.arizakayittarihi","servistakip.eskiserino","arizakayit.garizakayittarihi","sayacadi.nsayacadi","uretimyer.nyeradi",
                    "arizakayit.ndurum","kullanici.nadi_soyadi","sayacgelen.fiyatlandirma", "sayacgelen.depoteslim","sayacgelen.servis_id"))
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
            ->addColumn('islemler',function ($model) {
                $root = BackendController::getRootDizin();
                if($model->servis_id!=$this->servisid)
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
                'sayacgelen.servis_id', 'sayacgelen.arizakayit', 'sayacgelen.fiyatlandirma','sayacgelen.sokulmenedeni',
                'sayacgelen.musterionay', 'sayac.id as sayac_id'))
                ->leftjoin('uretimyer', 'sayacgelen.uretimyer_id', '=', 'uretimyer.id')
                ->leftjoin('sayac', 'sayacgelen.serino', '=', 'sayac.serino')
                ->where('sayacgelen.servis_id', $this->servisid)->where('sayacgelen.serino', $serino)->where('sayacgelen.id', $sayacgelenid)->where('sayacgelen.arizakayit', 0)
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
                    'sayacgelen.servis_id', 'sayacgelen.arizakayit', 'sayacgelen.fiyatlandirma','sayacgelen.sokulmenedeni',
                    'sayacgelen.musterionay', 'sayac.id as sayac_id'))
                    ->leftjoin('uretimyer', 'sayacgelen.uretimyer_id', '=', 'uretimyer.id')
                    ->leftjoin('sayac', 'sayacgelen.serino', '=', 'sayac.serino')
                    ->where('sayacgelen.servis_id', $this->servisid)->where('sayacgelen.serino', $serino)
                    ->where('sayac.id', $sayacid)->where('sayacgelen.arizakayit', 0)
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
                                        $sayacgelen->garantidurum = BackendController::getGarantiDurum($sayacgelen->depotarihi, $sayacgelen->sayac->songelistarihi,1);
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
                            'sayacgelen.servis_id', 'sayacgelen.arizakayit', 'sayacgelen.fiyatlandirma','sayacgelen.sokulmenedeni',
                            'sayacgelen.musterionay', 'sayac.id as sayac_id'))
                            ->leftjoin('uretimyer', 'sayacgelen.uretimyer_id', '=', 'uretimyer.id')
                            ->leftjoin('sayac', 'sayacgelen.serino', '=', 'sayac.serino')
                            ->where('sayacgelen.servis_id', $this->servisid)->where('sayacgelen.serino', $serino)->where('sayacgelen.arizakayit', 0)
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

    public function getSerinoekle() {
        try {
            $serino = Input::get('yeniserino');
            $uretimyer = Input::get('uretimyer');
            $sayacadi=Input::get('sayacadi');
            $sayacadii=SayacAdi::find($sayacadi);
            $sayactur=$sayacadii->sayactur_id;
            $sayaccap=Input::get('sayaccap');
            $sayacgelen=Input::get('sayacgelen');
            $sayaclar=Sayac::where('serino',$serino)->get();
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
                            if(BackendController::SayacDurum($serino,$uretimyer,$sayactur,false,$servistakip->id)){
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
                        if(BackendController::SayacDurum($serino,$uretimyer,$sayactur,false)){
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
        $netsiscariid=Auth::user()->netsiscari_id;
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->first();
        if($sube){
            $sayacturlist=explode(',',$sube->sayactur);
            $sayacadlari = SayacAdi::whereIn('sayactur_id',$sayacturlist)->get();
            $sayaccaplari = SayacCap::all();
            $arizakodlari = ArizaKod::whereIn('sayactur_id',$sayacturlist)->orderBy('kullanim','desc')->orderBy('tanim','asc')->get();
            $yapilanlar = Yapilanlar::whereIn('sayactur_id',$sayacturlist)->get();
            $degisenler = Degisenler::whereIn('sayactur_id',$sayacturlist)->get();
            $uyarilar = Uyarilar::whereIn('sayactur_id',$sayacturlist)->orderBy('kullanim','desc')->orderBy('tanim','asc')->get();
            $hurdanedenleri=HurdaNedeni::whereIn('sayactur_id',$sayacturlist)->get();
        }else{
            $sayacadlari = SayacAdi::all();
            $sayaccaplari = SayacCap::all();
            $arizakodlari = ArizaKod::orderBy('kullanim','desc')->orderBy('tanim','asc')->get();
            $yapilanlar = Yapilanlar::all();
            $degisenler = Degisenler::all();
            $hurdanedenleri=HurdaNedeni::all();
            $uyarilar=Uyarilar::all();
        }
        if($hatirlatma_id)
        {
            $hatirlatma = Hatirlatma::find($hatirlatma_id);
            $netsiscari = NetsisCari::find($hatirlatma->netsiscari_id);
            if($netsiscari->caridurum!="A")
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
            $servisstokkod = ServisStokKod::where('stokkodu',$hatirlatma->servisstokkodu)->first();
            return View::make($this->servisadi.'.arizakayitekle',array('arizakodlari'=>$arizakodlari,'yapilanlar'=>$yapilanlar,'degisenler'=>$degisenler,'uyarilar'=>$uyarilar,'sayacadlari'=>$sayacadlari,'sayaccaplari'=>$sayaccaplari,'hatirlatmalar'=>$hatirlatma,'netsiscari'=>$netsiscari,'servisstokkod'=>$servisstokkod,'hurdanedenleri'=>$hurdanedenleri))->with(array('title'=>$this->servisbilgi.' Sayaç Arıza Kayıdı Ekle'));
        }else{
            return View::make($this->servisadi.'.arizakayitekle',array('arizakodlari'=>$arizakodlari,'yapilanlar'=>$yapilanlar,'degisenler'=>$degisenler,'uyarilar'=>$uyarilar,'sayacadlari'=>$sayacadlari,'sayaccaplari'=>$sayaccaplari,'hurdanedenleri'=>$hurdanedenleri))->with(array('title'=>$this->servisbilgi.' Sayaç Arıza Kayıdı Ekle'));
        }
    }

    public function postArizakayitekle() {
        try {
            $rules = ['cariadi' => 'required', 'istek' => 'required', 'uretimyer' => 'required', 'serino' => 'required',
                'uretim' => 'required', 'sayacadi' => 'required', 'garanti' => 'required', 'ilkkredi' => 'required',
                'ilkharcanan' => 'required', 'ilkmekanik' => 'required', 'kalan' => 'required',
                'harcanan' => 'required', 'mekanik' => 'required', 'arizalar' => 'required', 'yapilanlar' => 'required', 'degisenler' => 'required', 'uyarilar' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $ilkkredi = str_replace(',', '.', Input::get('ilkkredi'));
            $ilkharcanan = str_replace(',', '.', Input::get('ilkharcanan'));
            $ilkmekanik = str_replace(',', '.', Input::get('ilkmekanik'));
            $kalan = str_replace(',', '.', Input::get('kalan'));
            $harcanan = str_replace(',', '.', Input::get('harcanan'));
            $mekanik = str_replace(',', '.', Input::get('mekanik'));
            $musteribilgi = Input::get('musteribilgi');
            $arizaaciklama = Input::get('arizaaciklama');
            $uretimyerid = Input::get('uretimyer');
            $sayacid = Input::get('sayacid');
            $hatirlatmaid = Input::get('hatirlatmaid');
            $sayacadiid = Input::get('sayacadi');
            if (Input::has('sayaccap'))
                $sayaccapid = Input::get('sayaccap');
            else
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
            $sube = Sube::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('aktif',1)->first();
            if (!$sube){
                DB::rollBack();
                return Redirect::to($this->servisadi.'/arizakayit')->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Şube Bilgisi Bulunamadı!', 'type' => 'error'));
            }
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
                                            $sayacgelen->beyanname = -2; // -2 hurda geri gonderim sayackayit -1 dahil değil 0 arizakayit kalibrasyon 1 tamamlanan
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
                                            $arizafiyat->toplamtutar = round(($fiyatlar['total'] + $kdv)*2)/2;
                                            $arizafiyat->toplamtutar2 = round(($fiyatlar['total2'] + $kdv2)*2)/2;
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
                                                    $depolararasi = Depolararasi::where('servis_id', $this->servisid)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                        ->where('tipi',1)->where('depodurum',0)->first();
                                                    if ($depolararasi) {
                                                        $depolararasi->secilenler .= ($depolararasi->secilenler == "" ? "" : ",") . $sayacgelen->id;
                                                        $depolararasi->sayacsayisi += 1;
                                                    } else {
                                                        $depolararasi = new Depolararasi;
                                                        $depolararasi->servis_id = $sayacgelen->servis_id;
                                                        $depolararasi->netsiscari_id = $sayacgelen->netsiscari_id;
                                                        $depolararasi->secilenler = $sayacgelen->id;
                                                        $depolararasi->sayacsayisi = 1;
                                                        $depolararasi->depodurum = 0;
                                                        $depolararasi->depokodu = $depogelen->depokodu;
                                                        $depolararasi->tipi = 1;
                                                    }
                                                    $depolararasi->save();
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
                                                        $hurdakayit->depolararasi_id = $depolararasi->id;
                                                        $hurdakayit->save();
                                                        $hurdanedeni = HurdaNedeni::find($hurdaneden);
                                                        $hurdanedeni->kullanim++;
                                                        $hurdanedeni->save();
                                                        BackendController::HatirlatmaIdGuncelle($hatirlatmaid,1);
                                                        BackendController::HatirlatmaEkle(11, $netsiscari->id,$sayacgelen->servis_id, 1,$depogelen->id,$depogelen->servisstokkodu);
                                                        BackendController::BildirimEkle(3, $netsiscari->id,$sayacgelen->servis_id, 1,$depogelen->id,$depogelen->servisstokkodu);
                                                        BackendController::BildirimEkle(10, $netsiscari->id,$sayacgelen->servis_id, 1);
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
                                                try {
                                                    $depolararasi = Depolararasi::where('servis_id', $this->servisid)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                        ->where('tipi',0)->where('depodurum',0)->first();
                                                    if ($depolararasi) {
                                                        $secilenler = explode(',', $depolararasi->secilenler);
                                                        $durum = BackendController::Listedemi($sayacgelen->id, $secilenler);
                                                        if ($durum) {
                                                            if($depolararasi->sayacsayisi>1){
                                                                $depolararasi->secilenler = BackendController::getListeFark($secilenler, array($sayacgelen->id));
                                                                $depolararasi->sayacsayisi--;
                                                                $depolararasi->save();
                                                            }else{
                                                                $depolararasi->delete();
                                                            }
                                                            BackendController::HatirlatmaSil(11,$netsiscari->id,$sayacgelen->servis_id,1,$depogelen->id,$depogelen->servisstokkodu);
                                                        }
                                                    }
                                                } catch (Exception $e) {
                                                    Log::error($e);
                                                    DB::rollBack();
                                                    $eklenenresimler = explode(',', $eklenenler);
                                                    foreach ($eklenenresimler as $resim) {
                                                        File::delete('assets/arizaresim/' . $resim . '');
                                                    }
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Depolararası Bilgisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
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
                                                        $sayacgelen->beyanname = 0;
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
                                                        $arizafiyat->toplamtutar = round(($fiyatlar['total'] + $kdv)*2)/2;
                                                        $arizafiyat->toplamtutar2 = round(($fiyatlar['total2'] + $kdv2)*2)/2;
                                                        $arizafiyat->parabirimi_id = $fiyatlar['parabirimi'];
                                                        $arizafiyat->parabirimi2_id = $fiyatlar['parabirimi2'];
                                                        $arizafiyat->subedurum = $subedurum;
                                                        $arizafiyat->durum = 0;
                                                        $arizafiyat->kullanici_id = Auth::user()->id;
                                                        $arizafiyat->kayittarihi = date('Y-m-d H:i:s');
                                                        $arizafiyat->save();
                                                        try {
                                                            $depolararasi = Depolararasi::where('servis_id', $this->servisid)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                                ->where('tipi',0)->where('depodurum',0)->first();
                                                            if ($depolararasi) {
                                                                $secilenler = explode(',', $depolararasi->secilenler);
                                                                $durum = BackendController::Listedemi($sayacgelen->id, $secilenler);
                                                                if ($durum) {
                                                                    if($depolararasi->sayacsayisi>1){
                                                                        $depolararasi->secilenler = BackendController::getListeFark($secilenler, array($sayacgelen->id));
                                                                        $depolararasi->sayacsayisi--;
                                                                        $depolararasi->save();
                                                                    }else{
                                                                        $depolararasi->delete();
                                                                    }
                                                                    BackendController::HatirlatmaSil(11,$netsiscari->id,$sayacgelen->servis_id,1,$depogelen->id,$depogelen->servisstokkodu);
                                                                }
                                                            }
                                                        } catch (Exception $e) {
                                                            Log::error($e);
                                                            DB::rollBack();
                                                            $eklenenresimler = explode(',', $eklenenler);
                                                            foreach ($eklenenresimler as $resim) {
                                                                File::delete('assets/arizaresim/' . $resim . '');
                                                            }
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Depolararası Bilgisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                        }
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
                                                $sayacgelen->beyanname = 0;
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
                                                $arizafiyat->toplamtutar = round(($fiyatlar['total'] + $kdv)*2)/2;
                                                $arizafiyat->toplamtutar2 = round(($fiyatlar['total2'] + $kdv2)*2)/2;
                                                $arizafiyat->parabirimi_id = $fiyatlar['parabirimi'];
                                                $arizafiyat->parabirimi2_id = $fiyatlar['parabirimi2'];
                                                $arizafiyat->subedurum = $subedurum;
                                                $arizafiyat->durum = 0;
                                                $arizafiyat->kullanici_id = Auth::user()->id;
                                                $arizafiyat->kayittarihi = date('Y-m-d H:i:s');
                                                $arizafiyat->save();
                                                try {
                                                    $depolararasi = Depolararasi::where('servis_id', $this->servisid)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                        ->where('tipi',0)->where('depodurum',0)->first();
                                                    if ($depolararasi) {
                                                        $secilenler = explode(',', $depolararasi->secilenler);
                                                        $durum = BackendController::Listedemi($sayacgelen->id, $secilenler);
                                                        if ($durum) {
                                                            if($depolararasi->sayacsayisi>1){
                                                                $depolararasi->secilenler = BackendController::getListeFark($secilenler, array($sayacgelen->id));
                                                                $depolararasi->sayacsayisi--;
                                                                $depolararasi->save();
                                                            }else{
                                                                $depolararasi->delete();
                                                            }
                                                            BackendController::HatirlatmaSil(11,$netsiscari->id,$sayacgelen->servis_id,1,$depogelen->id,$depogelen->servisstokkodu);
                                                        }
                                                    }
                                                } catch (Exception $e) {
                                                    Log::error($e);
                                                    DB::rollBack();
                                                    $eklenenresimler = explode(',', $eklenenler);
                                                    foreach ($eklenenresimler as $resim) {
                                                        File::delete('assets/arizaresim/' . $resim . '');
                                                    }
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Yapılamadı', 'text' => 'Depolararası Bilgisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                }
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
        $arizakayit->hatirlatma_id = BackendController::getHatirlatmaId(3, $arizakayit->sayacgelen->servis_id, $arizakayit->sayacgelen->depogelen_id, $arizakayit->sayacgelen->netsiscari_id);
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
        $netsiscariid=Auth::user()->netsiscari_id;
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->first();
        if($sube){
            $sayacturlist=explode(',',$sube->sayactur);
            $sayacadlari = SayacAdi::whereIn('sayactur_id',$sayacturlist)->get();
            $sayaccaplari = SayacCap::all();
            $arizakodlari = ArizaKod::whereIn('sayactur_id',$sayacturlist)->orderBy('kullanim','desc')->orderBy('tanim','asc')->get();
            $yapilanlar = Yapilanlar::whereIn('sayactur_id',$sayacturlist)->get();
            $degisenler = Degisenler::whereIn('sayactur_id',$sayacturlist)->get();
            $uyarilar = Uyarilar::whereIn('sayactur_id',$sayacturlist)->orderBy('kullanim','desc')->orderBy('tanim','asc')->get();
            $hurdanedenleri=HurdaNedeni::whereIn('sayactur_id',$sayacturlist)->get();
        }else{
            $sayacadlari = SayacAdi::all();
            $sayaccaplari = SayacCap::all();
            $arizakodlari = ArizaKod::orderBy('kullanim','desc')->orderBy('tanim','asc')->get();
            $yapilanlar = Yapilanlar::all();
            $degisenler = Degisenler::all();
            $uyarilar = Uyarilar::all();
            $hurdanedenleri=HurdaNedeni::all();
        }
        $arizakayit->sayacgelen->hurdadurum=($arizakayit->sayacgelen->sayacdurum==3 ? 1 : 0);
        if($arizakayit->sayacgelen->hurdadurum){
            $hurda=Hurda::where('sayacgelen_id',$arizakayit->sayacgelen_id)->first();
            $arizakayit->sayacgelen->hurdaneden=$hurda ? $hurda->hurdanedeni_id : '';
        }
        return View::make($this->servisadi.'.arizakayitduzenle',array('arizakayit'=>$arizakayit,'arizakodlari'=>$arizakodlari,'yapilanlar'=>$yapilanlar,'degisenler'=>$degisenler,'uyarilar'=>$uyarilar,'sayacadlari'=>$sayacadlari,'sayaccaplari'=>$sayaccaplari,'hurdanedenleri'=>$hurdanedenleri))->with(array('title'=>$this->servisbilgi.' Arıza Kayıdı Düzenleme Ekranı'));
    }

    public function postArizakayitduzenle($id) {
        try {
            $rules = ['cariadi'=>'required','istek'=>'required','uretimyer'=>'required','serino'=>'required',
            'uretim'=>'required','sayacadi'=>'required','garanti'=>'required','ilkkredi'=>'required',
            'ilkharcanan'=>'required','ilkmekanik'=>'required','kalan'=>'required',
            'harcanan'=>'required','mekanik'=>'required','arizalar'=>'required','yapilanlar'=>'required','degisenler'=>'required','uyarilar'=>'required'];
            $validate = Validator::make(Input::all(),$rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $arizakayit = ArizaKayit::find($id);
            $bilgi=$arizakayit;
            $ilkkredi=str_replace(',','.',Input::get('ilkkredi'));
            $ilkharcanan=str_replace(',','.',Input::get('ilkharcanan'));
            $ilkmekanik=str_replace(',','.',Input::get('ilkmekanik'));
            $kalan=str_replace(',','.',Input::get('kalan'));
            $harcanan=str_replace(',','.',Input::get('harcanan'));
            $mekanik=str_replace(',','.',Input::get('mekanik'));
            $musteribilgi=Input::get('musteribilgi');
            $arizaaciklama=Input::get('arizaaciklama');
            $uretimyerid = Input::get('uretimyer');
            $sayacgelenid=Input::get('sayacgelenid');
            $sayacadiid=Input::get('sayacadi');
            if(Input::has('sayaccap'))
                $sayaccapid=Input::get('sayaccap');
            else
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
            $depogelen = DepoGelen::find($servistakip->depogelen_id);
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
            $sube=Sube::where('netsiscari_id',$sayacgelen->netsiscari_id)->where('aktif',1)->first();
            if(!$sube){
                DB::rollBack();
                return Redirect::to($this->servisadi.'/arizakayit')->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Güncellenemedi', 'text' => 'Şube Bilgisi Bulunamadı!', 'type' => 'error'));
            }
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
                        BackendController::HatirlatmaEkle(11,$netsiscari->id,$sayacgelen->servis_id,1,$depogelen->id,$depogelen->servisstokkodu);
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
                                            $sayacgelen->beyanname = -2; // -2 hurda geri gönderim sayackayıt -1 dahil değil 0 bekleyen 1 biten
                                            $sayacgelen->save();
                                            $depogelen = DepoGelen::find($sayacgelen->depogelen_id);
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
                                            $arizafiyat->toplamtutar = round(($fiyatlar['total'] + $kdv)*2)/2;
                                            $arizafiyat->toplamtutar2 = round(($fiyatlar['total2'] + $kdv2)*2)/2;
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
                                                    $depolararasi = Depolararasi::where('servis_id', $this->servisid)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                        ->where('tipi',1)->where('depodurum',0)->first();
                                                    if ($eskidurum != 2) //arızakayıttan hurdaya ayrıldıysa
                                                    {
                                                        if ($depolararasi) {
                                                            $depolararasi->secilenler .= ($depolararasi->secilenler == "" ? "" : ",") . $sayacgelen->id;
                                                            $depolararasi->sayacsayisi += 1;
                                                        } else {
                                                            $depolararasi = new Depolararasi;
                                                            $depolararasi->servis_id = $sayacgelen->servis_id;
                                                            $depolararasi->netsiscari_id = $sayacgelen->netsiscari_id;
                                                            $depolararasi->secilenler = $sayacgelen->id;
                                                            $depolararasi->sayacsayisi = 1;
                                                            $depolararasi->depodurum = 0;
                                                            $depolararasi->depokodu = $depogelen->depokodu;
                                                            $depolararasi->tipi = 1;
                                                        }
                                                    }
                                                    $depolararasi->save();
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
                                                        $hurdakayit->depolararasi_id = $depolararasi->id;
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
                        if($sayacgelen->depolararasi==1){ //hurdalar depoya teslim edildiyse uyarı verecek
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
                        $depolararasi = Depolararasi::where('servis_id',$this->servisid)->where('netsiscari_id',$sayacgelen->netsiscari_id)
                            ->where('tipi',1)->where('depodurum',0)->first();
                        if($depolararasi){
                            if($depolararasi->sayacsayisi>1){
                                $secilenlist=explode(',',$depolararasi->secilenler);
                                $silinecek=array($sayacgelenid);
                                $depolararasi->secilenler=BackendController::getListeFark($secilenlist,$silinecek);
                                $depolararasi->sayacsayisi--;
                                $depolararasi->save();
                            }else{
                                $depolararasi->delete();
                            }
                            $hurdanedeni=HurdaNedeni::find($hurdaneden);
                            $hurdanedeni->kullanim-=($hurdanedeni->kullanim==0 ? 0 : 1);
                            $hurdanedeni->save();
                        }
                        //hatırlatmalar düzeltilecek hurda sayaç silinecek depoteslimden silinecek
                        BackendController::HatirlatmaSil(11,$netsiscari->id,$sayacgelen->servis_id,1,$depogelen->id,$depogelen->servisstokkodu);
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
                                                        $sayacgelen->beyanname = 0;
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
                                                        $arizafiyat->toplamtutar = round(($fiyatlar['total'] + $kdv)*2)/2;
                                                        $arizafiyat->toplamtutar2 = round(($fiyatlar['total2'] + $kdv2)*2)/2;
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
                                                $sayacgelen->beyanname = 0;
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
                                                $arizafiyat->toplamtutar = round(($fiyatlar['total'] + $kdv)*2)/2;
                                                $arizafiyat->toplamtutar2 = round(($fiyatlar['total2'] + $kdv2)*2)/2;
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
        $netsiscariid = Auth::user()->netsiscari_id;
        $sube = Sube::whereIn('netsiscari_id', $netsiscariid)->first();
        if ($sube) {
            $sayacturlist = explode(',', $sube->sayactur);
            $arizakodlari = ArizaKod::whereIn('sayactur_id', $sayacturlist)->orderBy('kullanim', 'desc')->orderBy('tanim', 'asc')->get();
            $yapilanlar = Yapilanlar::whereIn('sayactur_id', $sayacturlist)->get();
            $degisenler = Degisenler::whereIn('sayactur_id', $sayacturlist)->get();
            $uyarilar = Uyarilar::whereIn('sayactur_id', $sayacturlist)->orderBy('kullanim','desc')->orderBy('tanim','asc')->get();
        } else {
            $arizakodlari = ArizaKod::orderBy('kullanim', 'desc')->orderBy('tanim', 'asc')->get();
            $yapilanlar = Yapilanlar::all();
            $degisenler = Degisenler::all();
            $uyarilar = Uyarilar::all();
        }
        return View::make($this->servisadi.'.arizakayitgoster',array('arizakayit'=>$arizakayit,'arizakodlari'=>$arizakodlari,'yapilanlar'=>$yapilanlar,'degisenler'=>$degisenler,'uyarilar'=>$uyarilar))->with(array('title'=>$this->servisbilgi.' Arıza Kayıdı Bilgi Ekranı'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Kayıdı Bilgisi Getirilemedi.', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function postArizakayitgoster($id) {
        try {
            $rules = ['ilkkredi'=>'required','ilkharcanan'=>'required','ilkmekanik'=>'required','kalan'=>'required','harcanan'=>'required','mekanik'=>'required'];
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
            $ilkkredi=str_replace(',','.',Input::get('ilkkredi'));
            $ilkharcanan=str_replace(',','.',Input::get('ilkharcanan'));
            $ilkmekanik=str_replace(',','.',Input::get('ilkmekanik'));
            $kalan=str_replace(',','.',Input::get('kalan'));
            $harcanan=str_replace(',','.',Input::get('harcanan'));
            $mekanik=str_replace(',','.',Input::get('mekanik'));
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
                $sayacparca = SayacParca::where('sayacadi_id', $sayacadiid)->where('sayaccap_id', $sayaccapid)->first();
            } else {
                $sayacparca = SayacParca::where('sayacadi_id', $sayacadiid)->first();
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
            if ($uretimyer->count() > 0) {
                return Response::json(array('durum' => true, 'uretimyer' => $uretimyer));
            } else {
                return Response::json(array('durum' => false, 'title' => 'Seçilen Cari ile Bir Üretim Yeri Eşleştirilmemiş!', 'text' => 'Önce Cari Bilgisi ile bir Üretim Yeri Eşleştirin!', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'title' => 'Cari ve Yer Bilgisi Getirilirken Hata Oluştu!', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getServiskayit() {
        $netsiscariid=NetsisCari::whereIn('id',Auth::user()->netsiscari_id)->get(array('id'))->toArray();
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        $subepersonel = array();
        $serviskayit = array();
        if($sube){
            $subepersonel=SubePersonel::where('subekodu',$sube->subekodu)->get();
            $serviskayit = ServisKayit::leftJoin('abonetahsis','abonetahsis.id','=','serviskayit.abonetahsis_id')
                ->leftJoin('abonesayac','abonesayac.id','=','abonetahsis.abonesayac_id')->where('durum','<>',1)->get(array('serviskayit.id','abonesayac.serino'));
        }
        return View::make($this->servisadi.'.serviskayit',array('subepersonel'=>$subepersonel,'serviskayit'=>$serviskayit))->with(array('title'=>'Servis Bilgisi'));
    }

    public function postServiskayitlist() {
        $netsiscari_id=Input::get('netsiscari_id');
        if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $sube=Sube::whereIn('netsiscari_id',$netsiscarilist)->where('aktif',1)->first();
            if($sube) {
                $query = ServisKayit::where('serviskayit.subekodu', $sube->subekodu)
                    ->select(array("serviskayit.id", "abone.adisoyadi", "abonesayac.serino", "serviskayit.kayitadres", "serviskayit.gtipi", "serviskayit.gdurum",
                        "serviskayit.acilmatarihi", "serviskayit.kapanmatarihi", "serviskayit.gacilmatarihi", "serviskayit.gkapanmatarihi", "abone.nadisoyadi",
                        "serviskayit.nkayitadres","serviskayit.ntipi", "serviskayit.ndurum"))
                    ->leftjoin("abonetahsis", "serviskayit.abonetahsis_id", "=", "abonetahsis.id")
                    ->leftjoin("abone", "abonetahsis.abone_id", "=", "abone.id")
                    ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")
                    ->leftjoin("uretimyer", "serviskayit.uretimyer_id", "=", "uretimyer.id");
            }else{
                $query = ServisKayit::select(array("serviskayit.id", "abone.adisoyadi", "abonesayac.serino","serviskayit.kayitadres","serviskayit.gtipi","serviskayit.gdurum",
                    "serviskayit.acilmatarihi","serviskayit.kapanmatarihi","serviskayit.gacilmatarihi","serviskayit.gkapanmatarihi","abone.nadisoyadi",
                    "serviskayit.nkayitadres","serviskayit.ntipi","serviskayit.ndurum"))
                    ->leftjoin("abonetahsis", "serviskayit.abonetahsis_id", "=", "abonetahsis.id")
                    ->leftjoin("abone", "abonetahsis.abone_id", "=", "abone.id")
                    ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")
                    ->leftjoin("uretimyer", "serviskayit.uretimyer_id", "=", "uretimyer.id");
            }
        }else{
            $query = ServisKayit::select(array("serviskayit.id", "abone.adisoyadi", "abonesayac.serino","serviskayit.kayitadres","serviskayit.gtipi","serviskayit.gdurum",
                "serviskayit.acilmatarihi","serviskayit.kapanmatarihi","serviskayit.gacilmatarihi","serviskayit.gkapanmatarihi","abone.nadisoyadi",
                "serviskayit.nkayitadres","serviskayit.ntipi","serviskayit.ndurum"))
                ->leftjoin("abonetahsis", "serviskayit.abonetahsis_id", "=", "abonetahsis.id")
                ->leftjoin("abone", "abonetahsis.abone_id", "=", "abone.id")
                ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")
                ->leftjoin("uretimyer", "serviskayit.uretimyer_id", "=", "uretimyer.id");
        }
        return Datatables::of($query)
            ->editColumn('acilmatarihi', function ($model) {
                $date = new DateTime($model->acilmatarihi);
                return $date->format('d-m-Y');})
            ->editColumn('kapanmatarihi', function ($model) {
                if($model->kapanmatarihi){
                    $date = new DateTime($model->kapanmatarihi);
                    return $date->format('d-m-Y');
                }else{
                    return '';
                }})
            ->addColumn('islemler',function ($model)use($netsiscari_id) {
                $root = BackendController::getRootDizin();
                if($netsiscari_id)
                    if($model->kapanmatarihi!=null && (time()-strtotime($model->kapanmatarihi))>2592000)
                        return "<a class='btn btn-sm btn-info' href='".$root."/".$this->servisadi."/serviskayitgoster/".$model->id."' > Göster </a>";
                    else if($model->gtipi == "Arıza Kontrolü" && $model->gdurum != "Tamamlandı")
                        return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/serviskayitduzenle/".$model->id."' > Düzenle </a>
                        <a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                    else
                        return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/serviskayitduzenle/".$model->id."' > Düzenle </a>";
                else
                    return "";
            })
            ->make(true);
    }

    public function postServiskayit()
    {
        try {
            $kriterler = array();
            $export = Input::get('export');
            if(Input::get('ireport')==1){ //bekleyen raporu
                if(Input::get('tarihcheck')){
                    $tarihtipi = Input::get('tarihtipi');
                    $tarih = Input::get('tarih');
                    $tarih = explode(' - ', $tarih);
                    $date = explode('.', $tarih[0]);
                    $ilktarih = date('Y-m-d', mktime(0, 0, 0, $date[1], $date[0], $date[2]));
                    $nilktarih = $date[1].'-'.$date[0].'-'.$date[2];
                    $date = explode('.', $tarih[1]);
                    $sontarih = date('Y-m-d', mktime(0, 0, 0, $date[1], $date[0], $date[2]));
                    $date = explode('-', $sontarih);
                    $nsontarih = $date[1].'-'.$date[2].'-'.$date[0];
                    $date = explode('.', $tarih[1]);
                    $sontarih = date('Y-m-d', mktime(0, 0, 0, $date[1], $date[0], $date[2]));
                    $kriterler['tarihtipi'] = $tarihtipi;
                    $kriterler['ilktarih'] = $ilktarih;
                    $kriterler['sontarih'] = $sontarih;
                    $kriterler['nilktarih'] = $nilktarih;
                    $kriterler['nsontarih'] = $nsontarih;
                }else{
                    $kriterler['tarihtipi'] = "1";
                }
                $kriterler['tarihcheck'] = Input::get('tarihcheck');
                if(Input::get('adrescheck')){
                    $adres = Input::get('adres');
                    $adres = BackendController::StrNormalized($adres);
                    $kriterler['adres'] = '"'.$adres.'"';
                }
                $kriterler['adrescheck'] = Input::get('adrescheck');
                if(Input::get('aciklamacheck')){
                    $aciklama = Input::get('aciklama');
                    $aciklama = BackendController::StrNormalized($aciklama);
                    $kriterler['aciklama'] = '"'.$aciklama.'"';
                }
                $kriterler['aciklamacheck'] = Input::get('aciklamacheck');
                if(Input::get('tipcheck')){
                    $kayittipi = Input::get('kayittipi');
                    $kriterler['kayittipi'] = $kayittipi ;
                }else{
                    $kriterler['kayittipi'] = -1 ;
                }
                $sondurum = Input::get('sondurum');
                $kriterler['sondurum'] = $sondurum;
                if(Input::get('sericheck')){
                    $sericheck = Input::get('sericheck');
                    $kriterler['sericheck'] = $sericheck ;
                    $serino1 = Input::get('serino1');
                    $serino2 = Input::get('serino2');
                    $kriterler['serino1'] = $serino1;
                    $kriterler['serino2'] = $serino2;
                }else{
                    $kriterler['sericheck'] = Input::get('sericheck') ;
                    $kriterler['serino1'] = '-1';
                    $kriterler['serino2'] = '-1';
                }
                $netsiscarilist = Input::get('netsiscari');
                $netsiscarilist=explode(',',$netsiscarilist);
                $netsiscariler = NetsisCari::whereIn('id', $netsiscarilist)->get(array('id'))->toArray();
                if (count($netsiscariler) > 0) {
                    $sube = Sube::whereIn('netsiscari_id', $netsiscariler)->where('aktif',1)->first();
                    if (!$sube) {
                        return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Servis Bekleyen Listesini Çıkarma Yetkiniz Yok!', 'type' => 'warning', 'title' => 'Rapor Yetki Hatası'));
                    } else {
                        $raporadi = "servisbekleyen";
                        $kriterler['subekodu'] = $sube->subekodu;
                        JasperPHP::process(public_path('reports/servisbekleyen/servisbekleyen.jasper'), public_path('reports/outputs/servisbekleyen/' . $raporadi),
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
                        readfile("reports/outputs/servisbekleyen/" . $raporadi . "." . $export . "");
                        File::delete("reports/outputs/servisbekleyen/" . $raporadi . "." . $export . "");
                        return Redirect::back()->with(array('mesaj' => 'false'));
                    }
                } else {
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Servis Bekleyen Listesini Çıkarma Yetkiniz Yok!', 'type' => 'warning', 'title' => 'Rapor Yetki Hatası'));
                }
            }else if(Input::get('ireport')==2){ //tamamlanan raporu
                if(Input::get('tarihcheck')){
                    $tarih = Input::get('tarih');
                    $tarih = explode(' - ', $tarih);
                    $date = explode('.', $tarih[0]);
                    $ilktarih = date('Y-m-d', mktime(0, 0, 0, $date[1], $date[0], $date[2]));
                    $nilktarih = $date[1].'-'.$date[0].'-'.$date[2];
                    $date = explode('.', $tarih[1]);
                    $sontarih = date('Y-m-d', mktime(0, 0, 0, $date[1], $date[0], $date[2]));
                    $date = explode('-', $sontarih);
                    $nsontarih = $date[1].'-'.$date[2].'-'.$date[0];
                    $date = explode('.', $tarih[1]);
                    $sontarih = date('Y-m-d', mktime(0, 0, 0, $date[1], $date[0], $date[2]));
                    $kriterler['ilktarih'] = $ilktarih;
                    $kriterler['sontarih'] = $sontarih;
                    $kriterler['nilktarih'] = $nilktarih;
                    $kriterler['nsontarih'] = $nsontarih;
                }
                $kriterler['tarihcheck'] = Input::get('tarihcheck');
                if(Input::get('sericheck')){
                    $sericheck = Input::get('sericheck');
                    $kriterler['sericheck'] = $sericheck ;
                    $serino1 = Input::get('serino1');
                    $serino2 = Input::get('serino2');
                    $kriterler['serino1'] = $serino1;
                    $kriterler['serino2'] = $serino2;
                }else{
                    $kriterler['sericheck'] = Input::get('sericheck') ;
                    $kriterler['serino1'] = '-1';
                    $kriterler['serino2'] = '-1';
                }
                if(Input::get('tipcheck')){
                    $kayittipi = Input::get('kayittipi');
                    $kriterler['kayittipi'] = $kayittipi ;
                }else{
                    $kriterler['kayittipi'] = -1 ;
                }
                $netsiscarilist = Input::get('netsiscari');
                $netsiscarilist=explode(',',$netsiscarilist);
                $netsiscariler = NetsisCari::whereIn('id', $netsiscarilist)->get(array('id'))->toArray();
                if (count($netsiscariler) > 0) {
                    $sube = Sube::whereIn('netsiscari_id', $netsiscariler)->where('aktif',1)->first();
                    if (!$sube) {
                        return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Servis Tamamlanan Listesini Çıkarma Yetkiniz Yok!', 'type' => 'warning', 'title' => 'Rapor Yetki Hatası'));
                    } else {
                        $raporadi = "servistamamlanan";
                        $kriterler['subekodu'] = $sube->subekodu;
                        JasperPHP::process(public_path('reports/servistamamlanan/servistamamlanan.jasper'), public_path('reports/outputs/servistamamlanan/' . $raporadi),
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
                        readfile("reports/outputs/servistamamlanan/" . $raporadi . "." . $export . "");
                        File::delete("reports/outputs/servistamamlanan/" . $raporadi . "." . $export . "");
                        return Redirect::back()->with(array('mesaj' => 'false'));
                    }
                } else {
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Servis Tamamlanan Listesini Çıkarma Yetkiniz Yok!', 'type' => 'warning', 'title' => 'Rapor Yetki Hatası'));
                }
            }else{ //gunsonu raporu
                $tarih = Input::get('tarih');
                $tarih = explode(' - ', $tarih);
                $date = explode('.', $tarih[0]);
                $ilktarih = date('Y-m-d', mktime(0, 0, 0, $date[1], $date[0], $date[2]));
                $nilktarih = $date[1].'-'.$date[0].'-'.$date[2];
                $date = explode('.', $tarih[1]);
                $sontarih = date('Y-m-d', mktime(0, 0, 0, $date[1], $date[0], $date[2]));
                $date = explode('-', $sontarih);
                $nsontarih = $date[1].'-'.$date[2].'-'.$date[0];
                $date = explode('.', $tarih[1]);
                $sontarih = date('Y-m-d', mktime(0, 0, 0, $date[1], $date[0], $date[2]));
                $kriterler['ilktarih'] = $ilktarih;
                $kriterler['sontarih'] = $sontarih;
                $kriterler['nilktarih'] = $nilktarih;
                $kriterler['nsontarih'] = $nsontarih;
                $netsiscarilist = Input::get('netsiscari');
                $netsiscarilist=explode(',',$netsiscarilist);
                $netsiscariler = NetsisCari::whereIn('id', $netsiscarilist)->get(array('id'))->toArray();
                if (count($netsiscariler) > 0) {
                    $sube = Sube::whereIn('netsiscari_id', $netsiscariler)->where('aktif',1)->first();
                    if (!$sube) {
                        return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Gün Sonu Raporu Çıkarma Yetkiniz Yok!', 'type' => 'warning', 'title' => 'Rapor Yetki Hatası'));
                    } else {
                        $raporadi = "subegunsonu";
                        $kriterler['subekodu'] = $sube->subekodu;
                        JasperPHP::process(public_path('reports/subegunsonu/subegunsonu.jasper'), public_path('reports/outputs/subegunsonu/' . $raporadi),
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
                        readfile("reports/outputs/subegunsonu/" . $raporadi . "." . $export . "");
                        File::delete("reports/outputs/subegunsonu/" . $raporadi . "." . $export . "");
                        return Redirect::back()->with(array('mesaj' => 'false'));
                    }
                } else {
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Gün Sonu Raporu Çıkarma Yetkiniz Yok!', 'type' => 'warning', 'title' => 'Rapor Yetki Hatası'));
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error', 'title' => 'Rapor Çıkarma Hatası'));
        }
    }

    public function getServiskayitgoster($id) {
        try {
            $kayit = ServisKayit::find($id);
            $abonetahsis = AboneTahsis::withTrashed()->find($kayit->abonetahsis_id);
            $abone = Abone::withTrashed()->find($abonetahsis->abone_id);
            $abonesayac = AboneSayac::find($abonetahsis->abonesayac_id);
            $uretimyer = UretimYer::find($abone->uretimyer_id);
            $subepersonel = SubePersonel::find($kayit->subepersonel_id);
            if($abonesayac->sayactur_id==2){
                $kayit->birim = 'kWh';
            }else {
                $kayit->birim = 'm³';
            }
            $servisbilgi=null;
            if($kayit->servisbilgi_id){
                $servisbilgi = ServisBilgi::find($kayit->servisbilgi_id);
                $servisbilgi->durum = $servisbilgi->durum==1 ? 'Bekliyor' : 'Tamamlandı';
                if ($servisbilgi->acilmatarihi)
                    $servisbilgi->acilmatarihi = date('d-m-Y', strtotime($servisbilgi->acilmatarihi));
                else
                    $servisbilgi->acilmatarihi = "";
                if ($servisbilgi->kapanmatarihi)
                    $servisbilgi->kapanmatarihi = date('d-m-Y', strtotime($servisbilgi->kapanmatarihi));
                else
                    $servisbilgi->kapanmatarihi = "";
            }
            $servisfiyat = ServisFiyat::find($kayit->servisfiyat_id);
            $ekstra="";
            if($servisfiyat){
                $secilenler=explode(',',$servisfiyat->secilenler);
                $subeurun = SubeUrun::where('subekodu',$kayit->subekodu)->whereIn('id',$secilenler)->get();
                foreach ($subeurun as $urun){
                    $ekstra .= ($ekstra=="" ? "" : ",").$urun->urunadi;
                }
            }
            $smslog = SmsLog::where('islem_id',$kayit->id)->where('islemtipi',1)->orderBy('id','desc')->first();
            return View::make($this->servisadi.'.serviskayitgoster', array('kayit' => $kayit,'servisbilgi'=>$servisbilgi, 'abonetahsis' => $abonetahsis, 'abone' => $abone, 'abonesayac' => $abonesayac, 'uretimyer' => $uretimyer,'subepersonel'=>$subepersonel,'ekstra'=>$ekstra,'smslog'=>$smslog))->with(array('title' => 'Servis Bilgi Ekranı'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Bilgisi Getirilirken Hata Oluştu.', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getServiskayitekle(){
        $netsiscariid=NetsisCari::whereIn('id',Auth::user()->netsiscari_id)->get(array('id'))->toArray();
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        if(!$sube)
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube Kayıt Uyarısı', 'text' => 'Bu kullanıcıya ait şube bilgisi bulunamadı.', 'type' => 'warning'));
        $subepersonel=SubePersonel::where('subekodu',$sube->subekodu)->get();
        $subeurun= SubeUrun::where('subekodu',$sube->subekodu)->where('ekstra',1)->where('durum',1)->get();
        return View::make($this->servisadi.'.serviskayitekle',array('sube'=>$sube,'subepersonel'=>$subepersonel,'subeurun'=>$subeurun))->with(array('title'=>'Servis Kayıdı Ekle'));
    }

    public function getServiskayitabonebilgi(){
        try {
            $abone = Input::get('id');
            $abonesayac = Input::get('sayacid');
            $abonebilgi = AboneTahsis::where('abone.id',$abone)->where('abonesayac.id',$abonesayac)
                ->select(array("abonetahsis.id","abone.id as aboneid","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                    "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abonesayac.iletisim"))
                ->leftjoin("abone", "abonetahsis.abone_id", "=", "abone.id")
                ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")
                ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")->first();
            return Response::json(array('durum'=>true,'abonebilgi' => $abonebilgi));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title' => 'Abone Sayaç Bilgilerinde Hata Var!', 'text' => str_replace("'","\'",$e->getMessage()), 'type'=>'warning'));
        }
    }

    public function getServiskayitbilgi(){
        try {
            $tip = Input::get('tip');
            $kriter = Input::get('kriter');
            $subekodu = Input::get('subekodu');
            if ($subekodu != 1) {
                switch ($tip) {
                    case 1: // seri numarası
                        $abonebilgi = AboneTahsis::where('abonesayac.serino', 'LIKE', '%' . $kriter . '%')->where('abone.subekodu', $subekodu)->whereNull('abonetahsis.deleted_at')
                            ->select(array("abonetahsis.id","abone.id as aboneid","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                                "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abonesayac.sayactur_id","abonesayac.iletisim"))
                            ->leftjoin("abone", "abonetahsis.abone_id", "=", "abone.id")
                            ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")
                            ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")->get();
                            break;
                    case 2: // Adı Soyadı
                        $kriter = BackendController::StrNormalized($kriter);
                        $abonebilgi = AboneTahsis::where('abone.nadisoyadi', 'LIKE', '%' . $kriter . '%')->where('abone.subekodu', $subekodu)->whereNull('abonetahsis.deleted_at')
                            ->select(array("abonetahsis.id","abone.id as aboneid","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                                "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abonesayac.sayactur_id","abonesayac.iletisim"))
                            ->leftjoin("abone", "abonetahsis.abone_id", "=", "abone.id")
                            ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")
                            ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")->get();
                        break;
                    case 3: // TC No
                        $abonebilgi = AboneTahsis::where('abone.tckimlikno', 'LIKE', '%' . $kriter . '%')->where('abone.subekodu', $subekodu)->whereNull('abonetahsis.deleted_at')
                            ->select(array("abonetahsis.id","abone.id as aboneid","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                                "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abonesayac.sayactur_id","abonesayac.iletisim"))
                            ->leftjoin("abone", "abonetahsis.abone_id", "=", "abone.id")
                            ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")
                            ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")->get();
                        break;
                    case 4: // Telefon
                        $abonebilgi = AboneTahsis::where(function ($query) use ($kriter) {$query->where('abone.telefon', 'LIKE', '%' . $kriter . '%')
                            ->orwhere('abonesayac.iletisim', 'LIKE', '%' . $kriter . '%');})->where('abone.subekodu', $subekodu)->whereNull('abonetahsis.deleted_at')
                            ->select(array("abonetahsis.id","abone.id as aboneid","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                                "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abonesayac.sayactur_id","abonesayac.iletisim"))
                            ->leftjoin("abone", "abonetahsis.abone_id", "=", "abone.id")
                            ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")
                            ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")->get();
                        break;
                    default: // seri numarası
                        $abonebilgi = AboneTahsis::where('abonesayac.serino', 'LIKE', '%' . $kriter . '%')->where('abone.subekodu', $subekodu)->whereNull('abonetahsis.deleted_at')
                            ->select(array("abonetahsis.id","abone.id as aboneid","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                                "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abonesayac.sayactur_id","abonesayac.iletisim"))
                            ->leftjoin("abone", "abonetahsis.abone_id", "=", "abone.id")
                            ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")
                            ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")->get();
                        break;
                }
                if ($abonebilgi->count() > 0) {
                    return array("durum" => true, "count" => $abonebilgi->count(), "abonebilgi" => $abonebilgi);
                } else {
                    return array("durum" => false, "type" => "error", "title" => "Abone Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Abone Bilgisi Bulunamadı.");
                }
            } else {
                return array("durum" => false, "type" => "error", "title" => "Şube Bilgisi Bulunamadı", "text" => "Şubeye Ait Abone Bilgisi Mevcut Değil!");
            }
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title' => 'Abone Sayaç Bilgilerinde Hata Var!', 'text' => str_replace("'","\'",$e->getMessage()), 'type'=>'warning'));
        }
    }

    public function getListebilgigetir()
    {
        $id = Input::get('id');
        $subekodu = Input::get('subekodu');
        $netsiscari = Sube::where('subekodu', $subekodu)->get(array('netsiscari_id'))->toArray();
        if($subekodu!=1){
            $abonebilgi = AboneTahsis::where('abonetahsis.id', $id)->whereIn('abone.netsiscari_id', $netsiscari)
                ->select(array("abonetahsis.id","abone.id as aboneid","abonesayac.id as abonesayacid", "abone.adisoyadi", "abonesayac.serino", "uretimyer.yeradi", "abonesayac.adres",
                    "abone.telefon", "abone.tckimlikno", "abone.faturaadresi", "abone.tckimlikno","abonesayac.sayactur_id","abonesayac.iletisim"))
                ->leftjoin("abone", "abonetahsis.abone_id", "=", "abone.id")
                ->leftjoin("abonesayac", "abonetahsis.abonesayac_id", "=", "abonesayac.id")
                ->leftjoin("uretimyer", "abone.uretimyer_id", "=", "uretimyer.id")->first();
            if($abonebilgi){
                return array("durum" => true,"abonebilgi"=>$abonebilgi);
            }else{
                return array("durum" => false, "type" => "error", "title" => "Abone Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Abone Bilgisi Bulunamadı.");
            }
        }else {
            return array("durum" => false, "type" => "error", "title" => "Şube Bilgisi Bulunamadı", "text" => "Şubeye Ait Abone Bilgisi Mevcut Değil!");
        }
    }

    public function postServiskayitekle(){
        try {
            if (Input::get('smsgonder'))
                $rules = ['abone' => 'required', 'abonesayac' => 'required','acilmatarihi' => 'required', 'durum' => 'required','ilgilitel' => 'required|min:11'];
            else
                $rules = ['abone' => 'required', 'abonesayac' => 'required','acilmatarihi' => 'required', 'durum' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $subekodu= Input::get('subekodu');
            $tipi = Input::get('tipi');
            $aboneid = Input::get('abone');
            $abonesayacid = Input::get('abonesayac');
            $acilmatarihi = Input::get('acilmatarihi');
            $sayacadresi = Input::get('sayacadres');
            $abonetelefon = Input::get('abonetelefon');
            $telefon = Input::get('telefon');
            $aciklama = Input::get('aciklama');
            $kapanmatarihi = Input::get('kapanmatarihi');
            $personel = Input::get('personel');
            $durum = Input::get('durum');
            $servisnot = Input::get('servisnot');
            $sokulmedurumu = Input::has('sokulmedurumu') ? 1 : 0;
            $servissayaci = Input::has('servissayaci') ? 1 : 0;
            $takilmatarihi = Input::get('takilmatarihi');
            $ilkendeks = Input::get('ilkendeksi');
            $sonendeks = Input::get('sonendeksi');
            $sayacborcu = Input::get('sayacborc');
            $ilgilitel = Input::get('ilgilitelefonu');
            $smsgonder = Input::get('smsgonder');
            $abonetelefon = preg_replace('/\D/','',$abonetelefon);
            $abonetelefon = mb_substr($abonetelefon, 0, 4)." ".mb_substr($abonetelefon, 4, 3)." ".mb_substr($abonetelefon, 7, 2)." ".mb_substr($abonetelefon, 9, 2);
            $abone=Abone::find($aboneid);
            $abonetahsis = AboneTahsis::where('abone_id',$aboneid)->where('abonesayac_id',$abonesayacid)->first();
            $abonesayac = AboneSayac::find($abonetahsis->abonesayac_id);
            $netsiscari = NetsisCari::find($abone->netsiscari_id);
            $ekstralar = "";
            $ekstrafiyat = 0;
            $kurtarihi = null;
            if (Input::has('ekstra')) {
                $ekstra = Input::get('ekstra');
                foreach ($ekstra as $urun) {
                    $ekstralar .= ($ekstralar == "" ? "" : ",") . $urun;
                    $subeurun = SubeUrun::find($urun);
                    if($subeurun->parabirimi_id!=1){
                        $dovizkuru = DovizKuru::where('parabirimi_id',$subeurun->parabirimi_id)->orderBy('tarih','desc')->first();
                        if($dovizkuru){
                            $kurtarihi=$dovizkuru->tarih;
                            $urunfiyat = $subeurun->fiyat*$dovizkuru->kurfiyati;
                        }else{
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kur Hatası', 'text' => 'Seçilen Ekstra Ücretlerden Birisine Ait Kur Fiyatı Alınamadı!', 'type' => 'error'));
                        }
                    }else{
                        $urunfiyat = $subeurun->fiyat;
                    }
                    $ekstrafiyat += $urunfiyat;
                }
                $ekstraucret = 1;
            }else{
                $ekstralar = array();
                $ekstraucret = 0;
            }
            if($durum==1){
                if($kapanmatarihi==""){
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Tamamlanan Servis Kayıdı için Kapatma Tarihi Girilmelidir!', 'type' => 'error'));
                }
            }else{
                if($kapanmatarihi!=""){
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Kapatma Tarihi girilen Servis Kayıdının Servis Durumu Tamamlandı Seçilmelidir!', 'type' => 'error'));
                }
            }
            if(ServisKayit::where('abonetahsis_id',$abonetahsis->id)->where('durum',0)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Abone Tahsisi için Servis Kayıdı Zaten Mevcut!', 'type' => 'error'));
            }
            DB::beginTransaction();
            try {
                $abone->faturaadresi = $sayacadresi!="" ? $sayacadresi : $abone->faturaadresi;
                $abone->telefon = $abonetelefon!="" ? $abonetelefon : $abone->telefon;
                $abone->save();
                $abonesayac->adres = $sayacadresi!="" ? $sayacadresi : $abonesayac->adres;
                $abonesayac->iletisim = $telefon!="" ? $telefon : $abonesayac->iletisim;
                $abonesayac->tamirdurum=0;
                $abonesayac->save();
                $kayit = new ServisKayit;
                $kayit->subekodu=$subekodu;
                $kayit->abonetahsis_id=$abonetahsis->id;
                $kayit->netsiscari_id=$abone->netsiscari_id;
                $kayit->uretimyer_id=$abone->uretimyer_id;
                $kayit->kayitadres = $sayacadresi!="" ? $sayacadresi : $kayit->kayitadres;
                $kayit->durum = $durum;
                $kayit->tipi=$tipi;
                $kayit->subepersonel_id=$personel=="" ? NULL : $personel;
                if ($acilmatarihi != "")
                    $kayit->acilmatarihi = date("Y-m-d", strtotime($acilmatarihi));
                if ($kapanmatarihi != "")
                    $kayit->kapanmatarihi = date("Y-m-d", strtotime($kapanmatarihi));
                $kayit->servisnotu=$servisnot;
                $kayit->aciklama=$aciklama;
                $kayit->servissayaci=$servissayaci;
                if($servissayaci){
                    $kayit->ilkendeks = $ilkendeks;
                    if ($durum==1) {
                        $kayit->sonendeks = $sonendeks;
                        $kayit->sayacborcu = $sayacborcu;
                    }
                    $kayit->takilmatarihi = date("Y-m-d", strtotime($takilmatarihi));
                }else{
                    $kayit->ilkendeks = 0;
                    $kayit->sonendeks = 0;
                    $kayit->sayacborcu = 0;
                    $kayit->takilmatarihi = null;
                }
                $kayit->sokulmedurumu = $sokulmedurumu;
                $kayit->ilgilitel = $ilgilitel;
                $kayit->smsdurum = $smsgonder ? 0 : -1;
                $kayit->ekstraucret = $ekstraucret;
                $kayit->kullanici_id = Auth::user()->id;
                $kayit->save();
                if($ekstraucret){
                    $servisfiyat = new ServisFiyat;
                    $servisfiyat->secilenler = $ekstralar;
                    $servisfiyat->tutar = $ekstrafiyat;
                    $servisfiyat->parabirimi_id = 1;
                    $servisfiyat->kurtarihi = $kurtarihi;
                    $servisfiyat->save();
                    $kayit->servisfiyat_id=$servisfiyat->id;
                    $kayit->save();
                }
                if($sokulmedurumu){ //sayaç söküldüyse
                    try {
                        $gelistarih = is_null($kayit->takilmatarihi) ? $kayit->acilmatarihi : $kayit->takilmatarihi;
                        $kayitserino = $abonesayac->serino;
                        $sayacparca = SayacParca::where('sayacadi_id', $abonesayac->sayacadi_id)->where('sayaccap_id', $abonesayac->sayaccap_id)->first();
                        if (!$sayacparca) {
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaca Ait Stok Kodu Bulunamadı.', 'text' => 'Sayacın Adının Sistemde Tanımlı Olduğundan Emin Olun!', 'type' => 'error'));
                        }
                        $serviskod = ServisStokKod::find($sayacparca->servisstokkod_id);
                        $sokulmenedeni = $kayit->aciklama;
                        $ilkendeks = $kayit->ilkendeks;
                        $tarih = $kayit->takilmatarihi;
                        $servisyetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
                        if (!$servisyetkili) {
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkili Değil', 'text' => 'Kullanıcı Şube için Yetkili Olarak Eklenmemiş!', 'type' => 'error'));
                        }
                        $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
                        if (!$subeyetkili) {
                            $subeyetkili = SubeYetkili::where('subekodu', $kayit->subekodu)->where('aktif', 1)->first();
                            if (!$subeyetkili) {
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkili Değil', 'text' => 'Kullanıcı Şube için Yetkili Olarak Eklenmemiş!', 'type' => 'error'));
                            }
                        }
                        if (BackendController::SayacDurum($kayitserino, $abone->uretimyer_id, $this->servisid, false, false, $abonesayac->sayacadi_id)) {
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => $kayitserino . ' Nolu Sayacın Depo Çıkışı Yapılmamış.Önce Sayaca Ait Diğer İşlemleri Bitiriniz.', 'type' => 'error'));
                        }
                        $sayaclar = BackendController::SubeDepoGirisGrupla(array($kayitserino), array($abone->uretimyer_id), array($sayacparca->servisstokkod_id), array($abonesayac->sayacadi_id), array($abonesayac->sayaccap_id), array($sokulmenedeni),array($tarih), array($ilkendeks));
                        if (count($sayaclar) == 0) {
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Depo Sayaç Girişi Kaydedilemedi', 'text' => 'Girilen Sayaçların Seri Numaraları yazılmamış', 'type' => 'error'));
                        }
                        $depogelen = DepoGelen::where('db_name','MANAS' . date('Y'))->where('tarih', $gelistarih)->where('servis_id', 6)
                            ->where('carikod',$netsiscari->carikod)
                            ->where('depokodu', $servisyetkili->depokodu)->first();
                        $flag = 0;
                        if($depogelen){
                            $depogelenler = DepoGelen::where('db_name','MANAS' . date('Y'))->where('fisno', $depogelen->fisno)->get();
                            foreach ($depogelenler as $depogelenn) {
                                if ($depogelenn->servisstokkodu == $serviskod->stokkodu) {
                                    $depogelen = $depogelenn;
                                    $flag = 1;
                                    break;
                                }
                            }
                        }
                        if ($flag) { // stokkodu eşleşen depo girişi varsa o güne ait
                            $dbname = $depogelen->db_name;
                            $fisno = $depogelen->fisno;
                        }else{
                            $fisno = NULL;
                            $dbname = 'MANAS' . date('Y');
                        }
                        $depogiris = BackendController::SubeDepoGiris($dbname,$sayaclar, $gelistarih, $abone->netsiscari_id,$subekodu, $fisno);
                        //Config::set('database.connections.sqlsrv2.database', 'MANAS' . date('Y'));
                        if ($depogiris['durum'] == '0') {
                            DB::rollBack();
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => $depogiris['text'], 'type' => 'error'));
                        }
                        $adet = 0;
                        $secilenler = "";
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
                                    $servisid = $depogelen->servis_id;
                                    $depogelen->kod = ServisStokKod::where('stokkodu', $depogelen->servisstokkodu)->first();
                                    foreach ($sayaclar as $sayacgrup) {
                                        if ($sayacgrup['kod'] == $depogelen->kod->id) {
                                            $sayac = $sayacgrup['sayac']; //eklenecek sayaçlar
                                            foreach ($sayac as $girilecek) {
                                                $serino = $girilecek['serino'];
                                                $sayacadi = $girilecek['sayacadi'];
                                                $sayaccap = $girilecek['sayaccap'];
                                                $uretimyeri = $girilecek['uretimyeri'];
                                                $sokulmenedeni = $girilecek['neden'];
                                                $takilmatarih = $girilecek['takilmatarihi'];
                                                $endeks = $girilecek['endeks'];
                                                if ($serino != "") {
                                                    try {
                                                        if ($serino == $kayitserino) {
                                                            $kayit->depogelen_id = $depogelen->id;
                                                            $kayit->save();
                                                        }
                                                        $sayacgelen = new SayacGelen;
                                                        $sayacgelen->depogelen_id = $depogelen->id;
                                                        $sayacgelen->netsiscari_id = $abone->netsiscari_id;
                                                        $sayacgelen->stokkodu = $depogelen->servisstokkodu;
                                                        $sayacgelen->serino = $serino;
                                                        $sayacgelen->depotarihi = $gelistarih;
                                                        $sayacgelen->sayacadi_id = $sayacadi;
                                                        $sayacgelen->sayaccap_id = $sayaccap;
                                                        $sayacgelen->uretimyer_id = $uretimyeri;
                                                        $sayacgelen->servis_id = $depogelen->servis_id;
                                                        $sayacgelen->subekodu = $subekodu;
                                                        $sayacgelen->kullanici_id = Auth::user()->id;
                                                        $sayacgelen->beyanname = -2;
                                                        $sayacgelen->sokulmenedeni = $sokulmenedeni;
                                                        $sayacgelen->takilmatarihi = $takilmatarih=="" ? NULL : date("Y-m-d", strtotime($takilmatarih));
                                                        $sayacgelen->endeks = $endeks;
                                                        $sayacgelen->save();
                                                        $adet++;
                                                        $eklenenler .= ($eklenenler == "" ? "" : ",") . $serino;
                                                        $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelen->id;
                                                        $servistakip = new ServisTakip;
                                                        $servistakip->serino = $sayacgelen->serino;
                                                        $servistakip->sayacadi_id = $sayacgelen->sayacadi_id;
                                                        $servistakip->depogelen_id = $sayacgelen->depogelen_id;
                                                        $servistakip->netsiscari_id = $sayacgelen->netsiscari_id;
                                                        $servistakip->sayacgelen_id = $sayacgelen->id;
                                                        $servistakip->servis_id = $sayacgelen->servis_id;
                                                        $servistakip->uretimyer_id = $sayacgelen->uretimyer_id;
                                                        $servistakip->durum = 1;
                                                        $servistakip->subedurum = 1;
                                                        $servistakip->subekodu = $subekodu;
                                                        $servistakip->depotarih = $sayacgelen->depotarihi;
                                                        $servistakip->sayacgiristarihi = $sayacgelen->eklenmetarihi;
                                                        $servistakip->kullanici_id = $sayacgelen->kullanici_id;
                                                        $servistakip->sonislemtarihi = $sayacgelen->eklenmetarihi;
                                                        $servistakip->save();
                                                        $abonesayac->tamirdurum=1;
                                                        $abonesayac->save();
                                                    } catch (Exception $e) {
                                                        DB::rollBack();
                                                        Log::error($e);
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Servis Takip Kısmına Kayıt Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                    }
                                                }
                                            }
                                            if ($adet > 0) {
                                                try {
                                                    $depolararasi = Depolararasi::where('servis_id', $this->servisid)->where('netsiscari_id', $abone->netsiscari_id)->where('depokodu', $depogelen->depokodu)
                                                        ->where('tipi', 0)->where('depodurum', 0)->first();
                                                    if ($depolararasi) {
                                                        $depolararasi->secilenler .= ($depolararasi->secilenler == "" ? "" : ",") . $secilenler; //bu bilgiler yok
                                                        $depolararasi->sayacsayisi += $adet;
                                                    } else {
                                                        $depolararasi = new Depolararasi;
                                                        $depolararasi->subekodu = $subekodu;
                                                        $depolararasi->servis_id = $this->servisid;
                                                        $depolararasi->netsiscari_id = $abone->netsiscari_id;
                                                        $depolararasi->secilenler = $secilenler;
                                                        $depolararasi->sayacsayisi = $adet;
                                                        $depolararasi->depodurum = 0;
                                                        $depolararasi->depokodu = $depogelen->depokodu;
                                                    }
                                                    $depolararasi->save();
                                                } catch (Exception $e) {
                                                    Log::error($e);
                                                    DB::rollBack();
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Yapılamadı', 'text' => 'Depolararası Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                }
                                                try {
                                                    BackendController::HatirlatmaGuncelle(2, $abone->netsiscari_id, $servisid, $adet, $depogelen->id, $depogelen->servisstokkodu);
                                                    BackendController::DepoDurumGuncelle($depogelen->id);
                                                    BackendController::HatirlatmaEkle(3, $abone->netsiscari_id, $servisid, $adet, $depogelen->id, $depogelen->servisstokkodu);
                                                    BackendController::HatirlatmaEkle(11, $abone->netsiscari_id, $servisid, $adet, $depogelen->id, $depogelen->servisstokkodu);
                                                    BackendController::BildirimEkle(2, $abone->netsiscari_id, $servisid, $adet, $depogelen->id, $depogelen->servisstokkodu);
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
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => str_replace("'", "\'", $e->getMessage()), 'type' => 'error'));
                        }
                        BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $netsiscari->cariadi . ' Yerine Ait ' . $adet . ' Adet Sayacın Şube Sayaç Kayıdı Yapıldı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Seri Numaraları:' . $eklenenler);

                    } catch (Exception $e) {
                        Log::error($e);
                        DB::rollBack();
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Arızalı Sayaç Kayıdı Yapılamadı', 'text' => str_replace("'", "\'", $e->getMessage()), 'type' => 'error'));
                    }
                }
                if($smsgonder){
                    $smsdurum=BackendController::SmsGonder($kayit);
                    $kayit->smsdurum=$smsdurum;
                    $kayit->save();
                }
                BackendController::HatirlatmaEkle(13, $abone->netsiscari_id, $this->servisid, 1);
                BackendController::BildirimEkle(13, $abone->netsiscari_id, $this->servisid, 1);
                if($durum==1){
                    BackendController::HatirlatmaGuncelle(13,$abone->netsiscari_id, $this->servisid, 1);
                }
            } catch (Exception $e) {
                Log::error($e);
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Bilgisi Kaydedilemedi', 'text' => 'Servis Bilgisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-pencil', $kayit->id . ' Numaralı Servis Bilgisi Eklendi.', 'Kayıt Eden:' . Auth::user()->adi_soyadi . ',Servis Kayıt Numarası:' . $kayit->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/serviskayit')->with(array('mesaj' => 'true', 'title' => 'Servis Bilgisi Kaydedildi', 'text' => 'Servis Bilgisi Kaydedildi.'.($smsgonder ? 'Sms '.$kayit->gsmsdurum : ''), 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Bilgisi Kaydedilirken Hata Oluştu.', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getServiskayitduzenle($id){
        $kayit=ServisKayit::find($id);
        $kayit->abonetahsis=AboneTahsis::find($kayit->abonetahsis_id);
        if(!$kayit->abonetahsis){
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Bilgisi Getirilirken Hata Oluştu.', 'text' => "Aboneye Ait Bu Sayaca Ait Tahsis Bilgisi Yok", 'type' => 'warning'));
        }
        $kayit->abone=Abone::find($kayit->abonetahsis->abone_id);
        $kayit->abonesayac=AboneSayac::find($kayit->abonetahsis->abonesayac_id);
        if($kayit->abonesayac->sayactur_id==2){
            $kayit->birim = 'kWh';
        }else {
            $kayit->birim = 'm³';
        }
        $kayit->uretimyer=Uretimyer::find($kayit->uretimyer_id);
        $netsiscariler=isset(Auth::user()->netsiscari_id) && count(Auth::user()->netsiscari_id)>0 ? Auth::user()->netsiscarilist : array();
        $netsiscarilist=explode(',',$netsiscariler);
        $sube=Sube::whereIn('netsiscari_id',$netsiscarilist)->where('aktif',1)->first();
        if(!$sube){
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Bilgisi Getirilirken Hata Oluştu.', 'text' => "Servis Bilgisi Düzenleme Yetkiniz Yok!", 'type' => 'warning'));
        }
        $subepersonel=SubePersonel::where('subekodu',$sube->subekodu)->get();
        $subeurun = SubeUrun::where('subekodu',$sube->subekodu)->where('ekstra',1)->where('durum',1)->get();
        $servisfiyat = ServisFiyat::find($kayit->servisfiyat_id);
        $smslog = SmsLog::where('islem_id',$kayit->id)->where('islemtipi',1)->orderBy('id','desc')->first();
        $servisbilgi=null;
        if($kayit->servisbilgi_id){
            $servisbilgi = ServisBilgi::find($kayit->servisbilgi_id);
            $servisbilgi->durum = $servisbilgi->durum==1 ? 'Bekliyor' : 'Tamamlandı';
            if ($servisbilgi->acilmatarihi)
                $servisbilgi->acilmatarihi = date('d-m-Y', strtotime($servisbilgi->acilmatarihi));
            else
                $servisbilgi->acilmatarihi = "";
            if ($servisbilgi->kapanmatarihi)
                $servisbilgi->kapanmatarihi = date('d-m-Y', strtotime($servisbilgi->kapanmatarihi));
            else
                $servisbilgi->kapanmatarihi = "";
        }
        return View::make($this->servisadi.'.serviskayitduzenle',array('kayit'=>$kayit,'sube'=>$sube,'subepersonel'=>$subepersonel,'subeurun'=>$subeurun,'servisfiyat'=>$servisfiyat,'servisbilgi'=>$servisbilgi,'smslog'=>$smslog))->with(array('title'=>'Servis Bilgisi Düzenle'));
    }

    public function postServiskayitduzenle($id)
    {
        try {
            if (Input::get('smsgonder'))
                $rules = ['durum' => 'required','ilgilitel' => 'required|min:11'];
            else
                $rules = ['durum' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $subekodu= Input::get('subekodu');
            $tipi = Input::get('tipi');
            $aboneid = Input::get('abone');
            $abonesayacid = Input::get('abonesayac');
            $sayacadresi = Input::get('sayacadres');
            $abonetelefon = Input::get('abonetelefon');
            $telefon = Input::get('telefon');
            $aciklama = Input::get('aciklama');
            $kapanmatarihi = Input::get('kapanmatarihi');
            $personel = Input::get('personel');
            $durum = Input::get('durum');
            $servisnot = Input::get('servisnot');
            $sokulmedurumu = Input::has('sokulmedurumu') ? 1 : 0;
            $servissayaci = Input::has('servissayaci') ? 1 : 0;
            $takilmatarihi = Input::get('takilmatarihi');
            $ilkendeks = Input::get('ilkendeksi');
            $sonendeks = Input::get('sonendeksi');
            $sayacborcu = Input::get('sayacborc');
            $ilgilitel = Input::get('ilgilitelefonu');
            $smsgonder = Input::get('smsgonder');
            $abonetelefon = preg_replace('/\D/','',$abonetelefon);
            $abonetelefon = mb_substr($abonetelefon, 0, 4)." ".mb_substr($abonetelefon, 4, 3)." ".mb_substr($abonetelefon, 7, 2)." ".mb_substr($abonetelefon, 9, 2);
            $abone=Abone::find($aboneid);
            $abonetahsis = AboneTahsis::where('abone_id',$aboneid)->where('abonesayac_id',$abonesayacid)->first();
            $abonesayac = AboneSayac::find($abonetahsis->abonesayac_id);
            $netsiscari = NetsisCari::find($abone->netsiscari_id);
            $kayitserino = $abonesayac->serino;
            $ekstralar = "";
            $ekstrafiyat = 0;
            $kurtarihi = null;
            if (Input::has('ekstra')) {
                $ekstra = Input::get('ekstra');
                foreach ($ekstra as $urun) {
                    $ekstralar .= ($ekstralar == "" ? "" : ",") . $urun;
                    $subeurun = SubeUrun::find($urun);
                    if($subeurun->parabirimi_id!=1){
                        $dovizkuru = DovizKuru::where('parabirimi_id',$subeurun->parabirimi_id)->orderBy('tarih','desc')->first();
                        if($dovizkuru){
                            $kurtarihi=$dovizkuru->tarih;
                            $urunfiyat = $subeurun->fiyat*$dovizkuru->kurfiyati;
                        }else{
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kur Hatası', 'text' => 'Seçilen Ekstra Ücretlerden Birisine Ait Kur Fiyatı Alınamadı!', 'type' => 'error'));
                        }
                    }else{
                        $urunfiyat = $subeurun->fiyat;
                    }
                    $ekstrafiyat += $urunfiyat;
                }
                $ekstraucret = 1;
            }else{
                $ekstralar = array();
                $ekstraucret = 0;
            }
            if($durum==1){
                if($kapanmatarihi==""){
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Tamamlanan Servis Kayıdı için Kapatma Tarihi Girilmelidir!', 'type' => 'error'));
                }
            }else{
                if($kapanmatarihi!=""){
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Kapatma Tarihi girilen Servis Kayıdının Servis Durumu Tamamlandı Seçilmelidir!', 'type' => 'error'));
                }
            }
            DB::beginTransaction();
            try {
                $abone->faturaadresi = $sayacadresi!="" ? $sayacadresi : $abone->faturaadresi;
                $abone->telefon = $abonetelefon!="" ? $abonetelefon : $abone->telefon;
                $abone->save();
                $abonesayac->adres = $sayacadresi!="" ? $sayacadresi : $abonesayac->adres;
                $abonesayac->iletisim = $telefon!="" ? $telefon : $abonesayac->iletisim;
                $abonesayac->save();
                $kayit = ServisKayit::find($id);
                $bilgi = clone $kayit;
                $eskidurum = $kayit->sokulmedurumu;
                $eskikayit = $kayit->durum;
                $kayit->kayitadres = $sayacadresi!="" ? $sayacadresi : $kayit->kayitadres;
                $kayit->durum = $durum;
                $kayit->tipi=$tipi;
                $kayit->subepersonel_id=$personel=="" ? NULL : $personel;
                if ($kapanmatarihi != "")
                    $kayit->kapanmatarihi = date("Y-m-d", strtotime($kapanmatarihi));
                else
                    $kayit->kapanmatarihi = null;
                $kayit->servisnotu=$servisnot;
                $kayit->aciklama=$aciklama;
                $kayit->servissayaci=$servissayaci;
                if($servissayaci){
                    $kayit->ilkendeks = $ilkendeks;
                    if ($durum==1) {
                        $kayit->sonendeks = $sonendeks;
                        $kayit->sayacborcu = $sayacborcu;
                    }
                    $kayit->takilmatarihi = date("Y-m-d", strtotime($takilmatarihi));
                }else{
                    $kayit->ilkendeks = 0;
                    $kayit->sonendeks = 0;
                    $kayit->sayacborcu = 0;
                    $kayit->takilmatarihi = null;
                }
                $kayit->sokulmedurumu = $sokulmedurumu;
                $kayit->ilgilitel = $ilgilitel;
                $kayit->smsdurum = $kayit->smsdurum==-1 ? ($smsgonder ? 0 : -1) : $kayit->smsdurum;
                $kayit->ekstraucret = $ekstraucret;
                $kayit->kullanici_id = Auth::user()->id;
                $kayit->save();
                if($ekstraucret){
                    if($kayit->servisfiyat_id){
                        $servisfiyat = ServisFiyat::find($kayit->servisfiyat_id);
                    }else{
                        $servisfiyat = new ServisFiyat;
                    }
                    $servisfiyat->secilenler = $ekstralar;
                    $servisfiyat->tutar = $ekstrafiyat;
                    $servisfiyat->parabirimi_id = 1;
                    $servisfiyat->kurtarihi = $kurtarihi;
                    $servisfiyat->save();
                    $kayit->servisfiyat_id=$servisfiyat->id;
                    $kayit->save();
                }
                $sayacparca = SayacParca::where('sayacadi_id', $abonesayac->sayacadi_id)->where('sayaccap_id', $abonesayac->sayaccap_id)->first();
                if (!$sayacparca) {
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaca Ait Stok Kodu Bulunamadı.', 'text' => 'Sayacın Adının Sistemde Tanımlı Olduğundan Emin Olun!', 'type' => 'error'));
                }
                $serviskod = ServisStokKod::find($sayacparca->servisstokkod_id);
                $sokulmenedeni = $kayit->aciklama;
                $ilkendeks = $kayit->ilkendeks;
                $tarih = $kayit->takilmatarihi;
                $sayaclar = BackendController::SubeDepoGirisGrupla(array($kayitserino), array($abone->uretimyer_id), array($sayacparca->servisstokkod_id), array($abonesayac->sayacadi_id), array($abonesayac->sayaccap_id), array($sokulmenedeni), array($tarih), array($ilkendeks));
                if (count($sayaclar) == 0) {
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Depo Sayaç Girişi Kaydedilemedi', 'text' => 'Girilen Sayaçların Seri Numaraları yazılmamış', 'type' => 'error'));
                }
                $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
                if (!$subeyetkili) {
                    $subeyetkili = SubeYetkili::where('subekodu', $kayit->subekodu)->where('aktif', 1)->first();
                    if (!$subeyetkili) {
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkili Değil', 'text' => 'Kullanıcı Şube için Yetkili Olarak Eklenmemiş!', 'type' => 'error'));
                    }
                }
                if ($sokulmedurumu) { //sayaç söküldüyse
                    $gelistarih = is_null($kayit->takilmatarihi) ? $kayit->acilmatarihi : $kayit->takilmatarihi;
                    if (!($eskidurum)) {
                        try {
                            $servisyetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
                            if (!$servisyetkili) {
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkili Değil', 'text' => 'Kullanıcı Şube için Yetkili Olarak Eklenmemiş!', 'type' => 'error'));
                            }
                            if (BackendController::SayacDurum($kayitserino, $abone->uretimyer_id, $this->servisid, false, false, $abonesayac->sayacadi_id)) {
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => $kayitserino . ' Nolu Sayacın Depo Çıkışı Yapılmamış.Önce Sayaca Ait Diğer İşlemleri Bitiriniz.', 'type' => 'error'));
                            }

                            $depogelen = DepoGelen::where('tarih', $gelistarih)->where('servis_id', 6)
                                ->where('carikod',$netsiscari->carikod)
                                ->where('depokodu', $servisyetkili->depokodu)->first();
                            $flag = 0;
                            if($depogelen){
                                $depogelenler = DepoGelen::where('fisno', $depogelen->fisno)->get();
                                foreach ($depogelenler as $depogelenn) {
                                    if ($depogelenn->servisstokkodu == $serviskod->stokkodu) {
                                        $depogelen = $depogelenn;
                                        $flag = 1;
                                        break;
                                    }
                                }
                            }
                            if ($flag) { // stokkodu eşleşen depo girişi varsa o güne ait
                                $dbname = $depogelen->db_name;
                                $fisno = $depogelen->fisno;
                            }else{
                                $fisno = NULL;
                                $dbname = 'MANAS' . date('Y');
                            }
                            $depogiris = BackendController::SubeDepoGiris($dbname,$sayaclar, $gelistarih, $abone->netsiscari_id,$kayit->subekodu, $fisno);
                            if ($depogiris['durum'] == '0') {
                                DB::rollBack();
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => $depogiris['text'], 'type' => 'error'));
                            }
                            $inckeys = $depogiris['faturakalemler'];
                            $dbnames = $depogiris['dbnames'];
                            if ($dbname != 'MANAS' . date('Y')) { //eski depo girişi guncelleniyorsa
                                $dbname = 'MANAS' . date('Y');
                            }
                            $adet = 0;
                            $secilenler = "";
                            $eklenenler = "";
                            try {
                                for ($i = 0; $i < count($inckeys); $i++) {
                                    if ($dbname != $dbnames[$i]) { //eski depo girişi
                                        $depogelen = DepoGelen::where('db_name', $dbnames[$i])->where('inckeyno', $inckeys[$i])->first();
                                        if ($depogelen) {
                                            // eksilen depo kayıdı
                                            // düzenlenen depo kayıdı
                                            // yeni yok
                                            $depogelen->kod = ServisStokKod::where('stokkodu', $depogelen->servisstokkodu)->first();
                                            foreach ($sayaclar as $sayacgrup) {
                                                if ($sayacgrup['kod'] == $depogelen->kod->id) {
                                                    $sayac = $sayacgrup['sayac']; //eklenecek sayaçlar
                                                    $eskiler = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                                                    $eskisayaclar = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                                                    foreach ($eskisayaclar as $eskisayac) {
                                                        $eskisayac->flag = 0;
                                                    }
                                                    foreach ($sayac as $girilecek) {
                                                        $serino = $girilecek['serino'];
                                                        $sayacadi = $girilecek['sayacadi'];
                                                        $sayaccap = $girilecek['sayaccap'];
                                                        $uretimyeri = $girilecek['uretimyeri'];
                                                        $sokulmenedeni = $girilecek['neden'];
                                                        $takilmatarih = $girilecek['takilmatarihi'];
                                                        $endeks = $girilecek['endeks'];
                                                        if ($serino != "") {
                                                            try {
                                                                for ($j = 0; $j < $eskiler->count(); $j++) {
                                                                    $sayacgelen = $eskiler[$j];
                                                                    $eskisayac = $eskisayaclar[$j];
                                                                    if ($sayacgelen->serino == $serino && $eskisayac->flag == 0) {
                                                                        try {
                                                                            $eskisayac->flag = 1;
                                                                            if (!$sayacgelen->arizakayit && !$sayacgelen->depolararasi) {
                                                                                $sayacgelen->depogelen_id = $depogelen->id;
                                                                                $sayacgelen->netsiscari_id = $abone->netsiscari_id;
                                                                                $sayacgelen->stokkodu = $depogelen->servisstokkodu;
                                                                                $sayacgelen->serino = $serino;
                                                                                $sayacgelen->depotarihi = $depogelen->tarih;
                                                                                $sayacgelen->sayacadi_id = $sayacadi;
                                                                                $sayacgelen->sayaccap_id = $sayaccap;
                                                                                $sayacgelen->uretimyer_id = $uretimyeri;
                                                                                $sayacgelen->kullanici_id = Auth::user()->id;
                                                                                $sayacgelen->sokulmenedeni = $sokulmenedeni;
                                                                                $sayacgelen->takilmatarihi = $takilmatarih == "" ? NULL : date("Y-m-d", strtotime($takilmatarih));
                                                                                $sayacgelen->endeks = $endeks;
                                                                                $sayacgelen->save();
                                                                                $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                                                if ($servistakip) {
                                                                                    $servistakip->depogelen_id = $depogelen->id;
                                                                                    $servistakip->serino = $serino;
                                                                                    $servistakip->sayacadi_id = $sayacadi;
                                                                                    $servistakip->netsiscari_id = $abone->netsiscari_id;
                                                                                    $servistakip->uretimyer_id = $uretimyeri;
                                                                                    $servistakip->save();
                                                                                }
                                                                                $abonesayac = AboneSayac::where('serino', $serino)->first();
                                                                                if ($abonesayac) {
                                                                                    $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                                                                                    if ($abonetahsis) {
                                                                                        $serviskayit = ServisKayit::where('depogelen_id', $depogelen->id)->where('abonetahsis_id', $abonetahsis->id)->first();
                                                                                        if ($serviskayit) {
                                                                                            $serviskayit->netsiscari_id = $abone->netsiscari_id;
                                                                                            $serviskayit->uretimyer_id = $uretimyeri;
                                                                                            $serviskayit->sokulmedurumu = 1;
                                                                                            $serviskayit->aciklama = $sayacgelen->sokulmenedeni;
                                                                                            $serviskayit->servissayaci = $sayacgelen->takilmatarihi == NULL ? 0 : 1;
                                                                                            $serviskayit->takilmatarihi = $sayacgelen->takilmatarihi;
                                                                                            $serviskayit->ilkendeks = $sayacgelen->endeks;
                                                                                            $serviskayit->save();
                                                                                        }
                                                                                        $abonesayac->tamirdurum=1;
                                                                                        $abonesayac->save();
                                                                                    }
                                                                                }

                                                                                $depolararasi = Depolararasi::where('netsiscari_id', $eskisayac->netsiscari_id)->where('secilenler', 'LIKE', '%' . $sayacgelen->id . '%')
                                                                                    ->where('servis_id', 6)->where('tipi', 0)->where('depodurum', 0)->get();
                                                                                foreach ($depolararasi as $depoislem) {
                                                                                    $secilenlist = explode(',', $depoislem->secilenler);
                                                                                    if (in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listedeyse
                                                                                        if (count($secilenlist) > 1) { // sayaç bu listeden ayrılacak
                                                                                            $secilenler = "";
                                                                                            foreach ($secilenlist as $secilen) {
                                                                                                if ($secilen != $sayacgelen->id)
                                                                                                    $secilenler = ($secilenler == "" ? "" : ",") . $secilen;
                                                                                            }
                                                                                            $depoislem->secilenler = $secilenler;
                                                                                            $depoislem->save();


                                                                                            $yenidepoislem = Depolararasi::where('servis_id', 6)->where('netsiscari_id', $abone->netsiscari_id)->where('depokodu', $depogelen->depokodu)
                                                                                                ->where('tipi', 0)->where('depodurum', 0)->first();
                                                                                            if ($yenidepoislem) {
                                                                                                $yenidepoislem->secilenler .= ($yenidepoislem->secilenler == "" ? "" : ",") . $sayacgelen->id; //bu bilgiler yok
                                                                                                $yenidepoislem->sayacsayisi += 1;
                                                                                            } else {
                                                                                                $yenidepoislem = new Depolararasi;
                                                                                                $yenidepoislem->subekodu = $subekodu;
                                                                                                $yenidepoislem->servis_id = 6;
                                                                                                $yenidepoislem->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                                                $yenidepoislem->secilenler = $sayacgelen->id;
                                                                                                $yenidepoislem->sayacsayisi = 1;
                                                                                                $yenidepoislem->depodurum = 0;
                                                                                                $yenidepoislem->depokodu = $depogelen->depokodu;
                                                                                                $yenidepoislem->save();
                                                                                            }
                                                                                        } else {
                                                                                            $depoislem->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                                            $depoislem->secilenler = $sayacgelen->id;
                                                                                            $depoislem->sayacsayisi = 1;
                                                                                            $depoislem->depodurum = 0;
                                                                                            $depoislem->depokodu = $depogelen->depokodu;
                                                                                            $depoislem->save();
                                                                                        }
                                                                                        break;
                                                                                    }
                                                                                }
                                                                                array_push($allids, $sayacgelen->id);
                                                                            }
                                                                            break;
                                                                        } catch (Exception $e) {
                                                                            Log::error($e);
                                                                            DB::rollBack();
                                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Kayıdı Güncellenemedi', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                                        }
                                                                    } else {
                                                                        continue;
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
                                                                if ($sayacgelen->servis_id == 5) {
                                                                    $sayac = Sayac::where('serino', $sayacgelen->serino)->where('uretimyer_id', $sayacgelen->uretimyeri_id)
                                                                        ->where('sayactur_id', 5)->first();
                                                                    if ($sayac) { // mekanik sayaç varsa ve başka kayıdı yoksa silinecek
                                                                        $eskisayacgelen = SayacGelen::where('serino', $sayacgelen->serino)->where('sayactur_id', 5)->where('id', '<>', $sayacgelen->id)->first();
                                                                        if (!$eskisayacgelen)
                                                                            $sayac->delete();
                                                                    }
                                                                }
                                                                $depolararasi = Depolararasi::where('netsiscari_id', $abone->netsiscari_id)->where('secilenler', 'LIKE', '%' . $sayacgelen->id . '%')
                                                                    ->where('servis_id', 6)->where('tipi', 0)->where('depodurum', 0)->get();
                                                                foreach ($depolararasi as $depoislem) {
                                                                    $secilenlist = explode(',', $depoislem->secilenler);
                                                                    if (in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listedeyse
                                                                        $secilenler = "";
                                                                        foreach ($secilenlist as $secilen) {
                                                                            if ($secilen != $sayacgelen->id)
                                                                                $secilenler = ($secilenler == "" ? "" : ",") . $secilen;
                                                                        }
                                                                        if ($secilenler != "") {
                                                                            $depoislem->secilenler = $secilenler;
                                                                            $depoislem->save();
                                                                        } else {
                                                                            $depoislem->delete();
                                                                        }
                                                                        break;
                                                                    }
                                                                }
                                                                $abonesayac = AboneSayac::where('serino', $sayacgelen->serino)->first();
                                                                if ($abonesayac) {
                                                                    $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                                                                    if ($abonetahsis) {
                                                                        $serviskayit = ServisKayit::where('depogelen_id', $depogelen->id)->where('abonetahsis_id', $abonetahsis->id)->first();
                                                                        if ($serviskayit) {
                                                                            $serviskayit->depogelen_id = null;
                                                                            $serviskayit->sokulmedurumu = 0;
                                                                            $serviskayit->aciklama = '';
                                                                            $serviskayit->servissayaci = $sayacgelen->takilmatarihi == NULL ? 0 : 1;
                                                                            $serviskayit->takilmatarihi = $sayacgelen->takilmatarihi;
                                                                            $serviskayit->ilkendeks = $sayacgelen->endeks;
                                                                            $serviskayit->save();
                                                                        }
                                                                    }
                                                                }
                                                                $servistakip->delete();
                                                                $sayacgelen->delete();
                                                                BackendController::HatirlatmaSil(3, $abone->netsiscari_id, 6, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                                BackendController::HatirlatmaSil(11, $abone->netsiscari_id, 6, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                                BackendController::BildirimGeriAl(2, $abone->netsiscari_id, 6, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                            } catch (Exception $e) {
                                                                Log::error($e);
                                                                DB::rollBack();
                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Eski Sayaç Kayıdı Silinemedi', 'text' => 'Eski Sayaç Kayıdı Silinirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            DB::rollBack();
                                            Input::flash();
                                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Bilgisi Güncellenemedi', 'text' => 'Depo Gelen Bilgisi Kaydedilmemiş', 'type' => 'error'));
                                        }
                                    } else {
                                        $depogelen = DepoGelen::where('db_name', $dbname)->where('inckeyno', $inckeys[$i])->first();
                                        $now = time();
                                        while ($now + 30 > time()) {
                                            $depogelen = DepoGelen::where('db_name', $dbname)->where('inckeyno', $inckeys[$i])->first();
                                            if ($depogelen)
                                                break;
                                        }
                                        if ($depogelen) {
                                            $servisid = $depogelen->servis_id;
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
                                                        $sokulmenedeni = $girilecek['neden'];
                                                        $takilmatarih = $girilecek['takilmatarihi'];
                                                        $endeks = $girilecek['endeks'];
                                                        if ($serino != "") {
                                                            try {
                                                                if ($serino == $kayitserino) {
                                                                    $kayit->depogelen_id = $depogelen->id;
                                                                    $kayit->save();
                                                                }
                                                                $sayacgelen = new SayacGelen;
                                                                $sayacgelen->depogelen_id = $depogelen->id;
                                                                $sayacgelen->netsiscari_id = $abone->netsiscari_id;
                                                                $sayacgelen->stokkodu = $depogelen->servisstokkodu;
                                                                $sayacgelen->serino = $serino;
                                                                $sayacgelen->depotarihi = $gelistarih;
                                                                $sayacgelen->sayacadi_id = $sayacadi;
                                                                $sayacgelen->sayaccap_id = $sayaccap;
                                                                $sayacgelen->uretimyer_id = $uretimyeri;
                                                                $sayacgelen->servis_id = $depogelen->servis_id;
                                                                $sayacgelen->subekodu = $subekodu;
                                                                $sayacgelen->kullanici_id = Auth::user()->id;
                                                                $sayacgelen->beyanname = -2;
                                                                $sayacgelen->sokulmenedeni = $sokulmenedeni;
                                                                $sayacgelen->takilmatarihi = $takilmatarih == "" ? NULL : date("Y-m-d", strtotime($takilmatarih));
                                                                $sayacgelen->endeks = $endeks;
                                                                $sayacgelen->save();
                                                                $biten++;
                                                                $adet++;
                                                                $eklenenler .= ($eklenenler == "" ? "" : ",") . $serino;
                                                                $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelen->id;
                                                                $servistakip = new ServisTakip;
                                                                $servistakip->serino = $sayacgelen->serino;
                                                                $servistakip->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                $servistakip->depogelen_id = $sayacgelen->depogelen_id;
                                                                $servistakip->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                $servistakip->sayacgelen_id = $sayacgelen->id;
                                                                $servistakip->servis_id = $sayacgelen->servis_id;
                                                                $servistakip->uretimyer_id = $sayacgelen->uretimyer_id;
                                                                $servistakip->durum = 1;
                                                                $servistakip->subedurum = 1;
                                                                $servistakip->subekodu = $subekodu;
                                                                $servistakip->depotarih = $sayacgelen->depotarihi;
                                                                $servistakip->sayacgiristarihi = $sayacgelen->eklenmetarihi;
                                                                $servistakip->kullanici_id = $sayacgelen->kullanici_id;
                                                                $servistakip->sonislemtarihi = $sayacgelen->eklenmetarihi;
                                                                $servistakip->save();
                                                                $abonesayac->tamirdurum=1;
                                                                $abonesayac->save();
                                                            } catch (Exception $e) {
                                                                DB::rollBack();
                                                                Log::error($e);
                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Servis Takip Kısmına Kayıt Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                            }
                                                        }
                                                    }
                                                    if ($biten > 0) {
                                                        try {
                                                            $depolararasi = Depolararasi::where('servis_id', $this->servisid)->where('netsiscari_id', $abone->netsiscari_id)->where('depokodu', $depogelen->depokodu)
                                                                ->where('tipi', 0)->where('depodurum', 0)->first();
                                                            if ($depolararasi) {
                                                                $depolararasi->secilenler .= ($depolararasi->secilenler == "" ? "" : ",") . $secilenler; //bu bilgiler yok
                                                                $depolararasi->sayacsayisi += $biten;
                                                            } else {
                                                                $depolararasi = new Depolararasi;
                                                                $depolararasi->subekodu = $subekodu;
                                                                $depolararasi->servis_id = $this->servisid;
                                                                $depolararasi->netsiscari_id = $abone->netsiscari_id;
                                                                $depolararasi->secilenler = $secilenler;
                                                                $depolararasi->sayacsayisi = $biten;
                                                                $depolararasi->depodurum = 0;
                                                                $depolararasi->depokodu = $depogelen->depokodu;
                                                            }
                                                            $depolararasi->save();
                                                        } catch (Exception $e) {
                                                            Log::error($e);
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Yapılamadı', 'text' => 'Depolararası Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                        }
                                                        try {
                                                            BackendController::HatirlatmaGuncelle(2, $abone->netsiscari_id, $servisid, $biten, $depogelen->id, $depogelen->servisstokkodu);
                                                            BackendController::DepoDurumGuncelle($depogelen->id);
                                                            BackendController::HatirlatmaEkle(3, $abone->netsiscari_id, $servisid, $biten, $depogelen->id, $depogelen->servisstokkodu);
                                                            BackendController::HatirlatmaEkle(11, $abone->netsiscari_id, $servisid, $biten, $depogelen->id, $depogelen->servisstokkodu);
                                                            BackendController::BildirimEkle(2, $abone->netsiscari_id, $servisid, $biten, $depogelen->id, $depogelen->servisstokkodu);
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
                                }
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => str_replace("'", "\'", $e->getMessage()), 'type' => 'error'));
                            }
                        } catch (Exception $e) {
                            Log::error($e);
                            DB::rollBack();
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Arızalı Sayaç Kayıdı Yapılamadı', 'text' => str_replace("'", "\'", $e->getMessage()), 'type' => 'error'));
                        }
                    } else {
                        $depogelen = DepoGelen::find($kayit->depogelen_id);
                        if ($depogelen) {
                            $depogelen->kod = ServisStokKod::where('stokkodu', $depogelen->servisstokkodu)->first();
                            foreach ($sayaclar as $sayacgrup) {
                                if ($sayacgrup['kod'] == $depogelen->kod->id) {
                                    $sayac = $sayacgrup['sayac']; //eklenecek sayaçlar
                                    foreach ($sayac as $girilecek) {
                                        $serino = $girilecek['serino'];
                                        $sayacadi = $girilecek['sayacadi'];
                                        $sayaccap = $girilecek['sayaccap'];
                                        $uretimyeri = $girilecek['uretimyeri'];
                                        $sokulmenedeni = $girilecek['neden'];
                                        $takilmatarih = $girilecek['takilmatarihi'];
                                        $endeks = $girilecek['endeks'];
                                        if ($serino != "") {
                                            try {
                                                if ($serino == $kayitserino) {
                                                    $kayit->depogelen_id = $depogelen->id;
                                                    $kayit->save();
                                                }
                                                $sayacgelen = SayacGelen::where('depogelen_id', $depogelen->id)->where('serino', $serino)->first();
                                                $sayacgelen->netsiscari_id = $abone->netsiscari_id;
                                                $sayacgelen->stokkodu = $depogelen->servisstokkodu;
                                                $sayacgelen->serino = $serino;
                                                $sayacgelen->depotarihi = $gelistarih;
                                                $sayacgelen->sayacadi_id = $sayacadi;
                                                $sayacgelen->sayaccap_id = $sayaccap;
                                                $sayacgelen->uretimyer_id = $uretimyeri;
                                                $sayacgelen->sokulmenedeni = $sokulmenedeni;
                                                $sayacgelen->takilmatarihi = $takilmatarih == "" ? NULL : date("Y-m-d", strtotime($takilmatarih));
                                                $sayacgelen->endeks = $endeks;
                                                $sayacgelen->save();

                                                $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                $servistakip->serino = $sayacgelen->serino;
                                                $servistakip->sayacadi_id = $sayacgelen->sayacadi_id;
                                                $servistakip->depogelen_id = $sayacgelen->depogelen_id;
                                                $servistakip->netsiscari_id = $sayacgelen->netsiscari_id;
                                                $servistakip->sayacgelen_id = $sayacgelen->id;
                                                $servistakip->servis_id = $sayacgelen->servis_id;
                                                $servistakip->uretimyer_id = $sayacgelen->uretimyer_id;
                                                $servistakip->durum = 1;
                                                $servistakip->subekodu = $subekodu;
                                                $servistakip->subedurum = 1;
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
                                }
                            }
                        } else {
                            DB::rollBack();
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => 'Depo Gelen Bilgisi Kaydedilmemiş', 'type' => 'error'));
                        }
                    }
                } else {
                    if ($eskidurum) {
                        try {
                            $depogelen = DepoGelen::find($kayit->depogelen_id);
                            if ($depogelen) {
                                if (!BackendController::getDepoKayitDurum($depogelen->id)) {
                                    DB::rollBack();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Kayıdı Silinemedi', 'text' => 'Arıza Kayıdı Yapılmış Sayaç Var!', 'type' => 'error'));
                                }
                                $dbname = $depogelen->db_name;
                                $inckey = $depogelen->inckeyno;
                                $fisno = $depogelen->fisno;
                                $servisid = $depogelen->servis_id;
                                $durum = 0;
                                $diger = DepoGelen::where('fisno', $fisno)->where('db_name', $dbname)->get();
                                if (intval($depogelen->adet) > 1)
                                    $durum = 1;
                                else if ($diger->count() > 1)
                                    $durum = 2;

                                if ($depogelen->servis_id == 6) {
                                    $depogiris = BackendController::SubeDepoGirisSil($kayit->subekodu, $inckey, $fisno, $durum);
                                } else {
                                    $depogiris = BackendController::NetsisDepoGirisSil($inckey, $fisno, $durum);
                                }
                                if ($depogiris['durum'] == '0') {
                                    DB::rollBack();
                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Silinemedi', 'text' => $depogiris['text'], 'type' => 'error'));
                                }
                                try {
                                    $sayacgelenler = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                                    foreach ($sayacgelenler as $sayacgelen) {
                                        if ($sayacgelen->serino == $kayitserino) {
                                            $sayacgelen->depogelen_id = NULL;
                                            $sayacgelen->save();
                                            $kayit->depogelen_id = NULL;
                                            $kayit->save();
                                            $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                            if ($sayacgelen->servis_id == 6) {
                                                $depolararasi = Depolararasi::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('servis_id', 6)->where('tipi', 0)
                                                    ->where('depodurum', 0)->get();
                                                foreach ($depolararasi as $depoislem) {
                                                    $secilenlist = explode(',', $depoislem->secilenler);
                                                    if (in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listedeyse
                                                        $secilenler = "";
                                                        foreach ($secilenlist as $secilen) {
                                                            if ($secilen != $sayacgelen->id)
                                                                $secilenler = ($secilenler == "" ? "" : ",") . $secilen;
                                                        }
                                                        if ($secilenler != "") {
                                                            $depoislem->secilenler = $secilenler;
                                                            $depoislem->sayacsayisi--;
                                                            $depoislem->save();
                                                        } else {
                                                            $depoislem->delete();
                                                        }
                                                        break;
                                                    }
                                                }
                                            }
                                            $abonesayac->tamirdurum=0;
                                            $abonesayac->save();
                                            BackendController::HatirlatmaSil(2, $abone->netsiscari_id, $servisid, 1, $depogelen->id, $depogelen->servisstokkodu);
                                            BackendController::HatirlatmaSil(3, $abone->netsiscari_id, $servisid, 1, $depogelen->id, $depogelen->servisstokkodu);
                                            BackendController::HatirlatmaSil(11, $abone->netsiscari_id, $servisid, 1, $depogelen->id, $depogelen->servisstokkodu);
                                            BackendController::BildirimGeriAl(2, $abone->netsiscari_id, $servisid, 1, $depogelen->id, $depogelen->servisstokkodu);
                                            if($durum==1){ // adet silinecek
                                                $servistakip->delete();
                                                $sayacgelen->delete();
                                            } else { // depogelen ve ona ait bilgiler silinecek
                                                $servistakip->delete();
                                                $sayacgelen->delete();
                                            }
                                        }
                                    }
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Kayıdı Silinemedi', 'text' => 'Depo Giriş Kayıdı Silinirken Sorun Oluştu.', 'type' => 'error'));
                                }
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Kayıdı Silinemedi', 'text' => str_replace("'", "\'", $e->getMessage()), 'type' => 'error'));
                        }
                    }
                }
                if($kayit->smsdurum==0){
                    $smsdurum=BackendController::SmsGonder($kayit);
                    $kayit->smsdurum=$smsdurum;
                    $kayit->save();
                }
                if ($eskikayit != 1 && $durum == 1) {
                    BackendController::HatirlatmaGuncelle(13,$abone->netsiscari_id, $this->servisid, 1);
                }
            } catch (Exception $e) {
                Log::error($e);
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Bilgisi Güncellenemedi', 'text' => 'Servis Bilgisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $bilgi->id . ' Numaralı Servis Bilgisi Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Servis Kayıt Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/serviskayit')->with(array('mesaj' => 'true', 'title' => 'Servis Bilgisi Güncellendi', 'text' => 'Servis Bilgisi Güncellendi.', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Bilgisi Düzenlenirken Hata Oluştu.', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function postToplukayitguncelle(){
        try {
            $rules = ['sayaclar'=>'required','personel'=>'required','durum' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $serviskayitlar = Input::get('sayaclar');
            $kapanmatarihi = Input::get('kapanmatarihi');
            $personel = Input::get('personel');
            $durum = Input::get('durum');
            if($durum==1){
                if($kapanmatarihi==""){
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Tamamlanan Servis Kayıdı için Kapatma Tarihi Girilmelidir!', 'type' => 'error'));
                }
            }else{
                if($kapanmatarihi!=""){
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Kapatma Tarihi girilen Servis Kayıdının Servis Durumu Tamamlandı Seçilmelidir!', 'type' => 'error'));
                }
            }
            $aciklama = Input::has('topluaciklamacheck') ? Input::get('topluaciklama') : null;
            DB::beginTransaction();
            try {
                foreach($serviskayitlar as $serviskayitid){
                    $kayit = ServisKayit::find($serviskayitid);
                    $eskikayit = $kayit->durum;
                    if ($kapanmatarihi != "")
                        $kayit->kapanmatarihi = date("Y-m-d", strtotime($kapanmatarihi));
                    else
                        $kayit->kapanmatarihi = null;
                    $kayit->durum = $durum;
                    $kayit->subepersonel_id=$personel=="" ? NULL : $personel;
                    if($aciklama)
                        $kayit->aciklama = $aciklama;
                    $kayit->save();
                    if($eskikayit!=1 && $durum==1){
                        BackendController::HatirlatmaGuncelle(13,$kayit->netsiscari_id, $this->servisid, 1);
                    }
                }
            } catch (Exception $e) {
                Log::error($e);
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Bilgisi Güncellenemedi', 'text' => 'Servis Bilgisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', count($serviskayitlar) . ' Adet Servis Kayıdı Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Servis Kayıt Numarası:' . implode(", ", $serviskayitlar));
            DB::commit();
            return Redirect::to($this->servisadi.'/serviskayit')->with(array('mesaj' => 'true', 'title' => 'Servis Bilgisi Güncellendi', 'text' => 'Servis Bilgisi Güncellendi.', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Bilgisi Düzenlenirken Hata Oluştu.', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getServiskayitsil($id){
        try {
            DB::beginTransaction();
            $serviskayit = ServisKayit::find($id);
            $abonetahsis = AboneTahsis::find($serviskayit->abonetahsis_id);
            $abonesayac = AboneSayac::find($abonetahsis->abonesayac_id);
            $abone=Abone::find($abonetahsis->abone_id);
            $kayitserino = $abonesayac->serino;
            $bilgi = clone $serviskayit;
            $netsiscari = NetsisCari::find($serviskayit->netsiscari_id);
            if($serviskayit->depogelen_id!=NULL){
                $depogelen = DepoGelen::find($serviskayit->depogelen_id);
                if ($depogelen) {
                    if (!BackendController::getDepoKayitDurum($depogelen->id)) {
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Kayıdı Silinemedi', 'text' => 'Arıza Kayıdı Yapılmış Sayaç Var!', 'type' => 'error'));
                    }
                    $dbname = $depogelen->db_name;
                    $inckey = $depogelen->inckeyno;
                    $fisno = $depogelen->fisno;
                    $servisid = $depogelen->servis_id;
                    $durum = 0;
                    $diger = DepoGelen::where('fisno', $fisno)->where('db_name', $dbname)->get();
                    if($depogelen->adet>1)
                        $durum = 1;
                    else if ($diger->count() > 1)
                        $durum = 2;

                    if($depogelen->servis_id==6){
                        $depogiris = BackendController::SubeDepoGirisSil($serviskayit->subekodu,$inckey,$fisno,$durum);
                    }else{
                        $depogiris = BackendController::NetsisDepoGirisSil($inckey,$fisno,$durum);
                    }
                    if ($depogiris['durum'] == '0') {
                        DB::rollBack();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Silinemedi', 'text' => $depogiris['text'], 'type' => 'error'));
                    }
                    try {
                        $sayacgelenler = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                        foreach ($sayacgelenler as $sayacgelen) {
                            if ($sayacgelen->serino == $kayitserino) {
                                $sayacgelen->depogelen_id = NULL;
                                $sayacgelen->save();
                                $serviskayit->depogelen_id = NULL;
                                $serviskayit->save();
                                $abonesayac->tamirdurum=0;
                                $abonesayac->save();
                                $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                if ($sayacgelen->servis_id == 5) {
                                    $sayac = Sayac::where('serino', $sayacgelen->serino)->where('uretimyer_id', $sayacgelen->uretimyeri_id)
                                        ->where('sayactur_id', 5)->first();
                                    if ($sayac) { // mekanik sayaç varsa ve başka kayıdı yoksa silinecek
                                        $eskisayacgelen = SayacGelen::where('serino', $sayacgelen->serino)->where('sayactur_id', 5)->where('id', '<>', $sayacgelen->id)->first();
                                        if (!$eskisayacgelen)
                                            $sayac->delete();
                                    }
                                } else if ($sayacgelen->servis_id == 6) {
                                    $depolararasi = Depolararasi::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('servis_id', 6)->where('tipi', 0)
                                        ->where('depodurum', 0)->get();
                                    foreach ($depolararasi as $depoislem) {
                                        $secilenlist = explode(',', $depoislem->secilenler);
                                        if (in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listedeyse
                                            $secilenler = "";
                                            foreach ($secilenlist as $secilen) {
                                                if ($secilen != $sayacgelen->id)
                                                    $secilenler = ($secilenler == "" ? "" : ",") . $secilen;
                                            }
                                            if ($secilenler != "") {
                                                $depoislem->secilenler = $secilenler;
                                                $depoislem->save();
                                            } else {
                                                $depoislem->delete();
                                            }
                                            break;
                                        }
                                    }
                                }
                                BackendController::HatirlatmaSil(2, $abone->netsiscari_id, $servisid, 1, $depogelen->id, $depogelen->servisstokkodu);
                                BackendController::HatirlatmaSil(3, $abone->netsiscari_id, $servisid, 1, $depogelen->id, $depogelen->servisstokkodu);
                                BackendController::HatirlatmaSil(11, $abone->netsiscari_id, $servisid, 1, $depogelen->id, $depogelen->servisstokkodu);
                                BackendController::BildirimGeriAl(2, $abone->netsiscari_id, $servisid, 1, $depogelen->id, $depogelen->servisstokkodu);
                                if($durum==1){ // adet silinecek
                                    $servistakip->delete();
                                    $sayacgelen->delete();
                                    $depogelen->adet -=1;
                                    $depogelen->save();
                                }else {// depogelen ve ona ait bilgiler silinecek
                                    $servistakip->delete();
                                    $sayacgelen->delete();
                                    $depogelen->delete();
                                }
                            }
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Kayıdı Silinemedi', 'text' => 'Depo Giriş Kayıdı Silinirken Sorun Oluştu.', 'type' => 'error'));
                    }
                }
            }
            $serviskayit->delete();
            BackendController::HatirlatmaSil(13, $abone->netsiscari_id, $this->servisid, 1);
            BackendController::BildirimGeriAl(13, $abone->netsiscari_id, $this->servisid, 1);
            BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-pencil', $netsiscari->cariadi . ' Yerine Ait ' . $bilgi->id . ' Numaralı Servis Kayıdı Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Servis Kayıt Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Kayıdı Silindi', 'text' => 'Servis Kayıdı Başarıyla Silindi.', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Kayıdı Silinemedi', 'text' => 'Servis Kayıdı Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getBeyanname() {
            return View::make($this->servisadi.'.beyanname')->with(array('title'=>$this->servisbilgi.' Servis Beyannameleri'));
    }

    public function postBeyannamelist() {
        $flag=0;
        $netsiscari_id=Input::get('netsiscari_id');
        if($netsiscari_id!="") {
            $flag=1;
            $netsiscarilist=explode(',',$netsiscari_id);
            $query = Beyanname::whereIn('beyanname.netsiscari_id',$netsiscarilist)->where('beyanname.servis_id',$this->servisid)
                ->select(array("beyanname.id","beyanname.no","beyanname.adet","beyanname.tarih","kullanici.adi_soyadi","beyanname.gtarih","kullanici.nadi_soyadi","beyanname.durum"))
                ->leftjoin("kullanici", "beyanname.kullanici_id", "=", "kullanici.id");
        }else{
            $query = Beyanname::where('beyanname.servis_id',$this->servisid)
                ->select(array("beyanname.id","beyanname.no","beyanname.adet","beyanname.tarih","kullanici.adi_soyadi","beyanname.gtarih","kullanici.nadi_soyadi","beyanname.durum"))
                ->leftjoin("kullanici", "beyanname.kullanici_id", "=", "kullanici.id");
        }
        return Datatables::of($query)
            ->editColumn('tarih', function ($model) {
                $date = new DateTime($model->tarih);
                return $date->format('d-m-Y');})
            ->addColumn('islemler',function ($model) use($flag) {
                $root = BackendController::getRootDizin();
                if(!$model->durum && $flag)
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

                    JasperPHP::process(public_path('reports/beyanname/beyanname.jasper'), public_path('reports/outputs/beyanname/' . $raporadi),
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

                    JasperPHP::process(public_path('reports/beyannamedefter/beyannamedefter.jasper'), public_path('reports/outputs/beyannamedefter/' . $raporadi),
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
            $netsiscari_id=Auth::user()->netsiscari_id;
            $netsiscarilist=Auth::user()->netsiscarilist;
            $sube = Sube::whereIn('netsiscari_id',$netsiscari_id)->where('aktif',1)->first();
            if($netsiscarilist!="" && $sube ) {
                $no = BeyannameNo::where('netsiscari_id',$sube->netsiscari_id)->where('servis_id', $this->servisid)->where('yil', date('Y'))->where('type', 'T')->first();
                if ($no) {
                    $kayitno = BackendController::BeyannameNo($no->no, 1);
                } else {
                    $beyannameno = new BeyannameNo;
                    $beyannameno->servis_id = $this->servisid;
                    $beyannameno->netsiscari_id = $sube->netsiscari_id;
                    $beyannameno->yil = date('Y');
                    $beyannameno->type = 'T';
                    $beyannameno->no = "T-".date('Y')."/".(0);
                    $beyannameno->save();
                    $kayitno = BackendController::BeyannameNo($beyannameno->no,1);
                }
                $sayacgelenlist="";
                $sayacgelenler = SayacGelen::whereIn('netsiscari_id',$netsiscari_id)->where('servis_id', $this->servisid)->where('beyanname',0)->get();
                foreach ($sayacgelenler as $sayacgelen){
                    $sayacgelen->sayacdurum=BackendController::ServisTakipDurum($sayacgelen->id);
                    $sayacgelenlist.=($sayacgelenlist=="" ? "" : ",").$sayacgelen->id;
                }
                return View::make($this->servisadi.'.beyannameekle', array('sayacgelenlist'=>$sayacgelenlist,'adet'=>$sayacgelenler->count(), 'beyannameno' => $kayitno,'tarih'=>date('d-m-Y')))->with(array('title' => 'Beyanname Ekle'));
            }else{
                return Redirect::back()->with(array('mesaj'=>'true','title'=>'Yetki Hatası','text'=>'Bu Şube İçin Beyanname Ekleme Yetkiniz Yok','type'=>'warning'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning', 'title' => 'Beyanname Bilgisi Bulunamadı!'));
        }
    }

    public function postBeyannamekayitlist() {
        if(Input::has('beyanname_id')){
            $beyanname_id=Input::get('beyanname_id');
            $beyanname=Beyanname::find($beyanname_id);
            $sayacgelenlist=explode(',',$beyanname->secilenler);
            $netsiscari_id=Input::get('netsiscari_id');
            if($netsiscari_id!="") {
                $netsiscarilist=explode(',',$netsiscari_id);
                $query = SayacGelen::whereIn('sayacgelen.id', $sayacgelenlist)
                    ->orWhere(function($query)use($netsiscarilist) {
                        $query->whereIn('sayacgelen.netsiscari_id',$netsiscarilist)->where('sayacgelen.servis_id',$this->servisid)->where('sayacgelen.beyanname',0);
                    })
                    ->select(array("sayacgelen.id","sayacgelen.serino","sayacadi.sayacadi","uretimyer.yeradi","sayaccap.capadi"))
                    ->leftjoin("sayacadi", "sayacgelen.sayacadi_id", "=", "sayacadi.id")
                    ->leftjoin("sayaccap", "sayacgelen.sayaccap_id", "=", "sayaccap.id")
                    ->leftjoin("uretimyer", "sayacgelen.uretimyer_id", "=", "uretimyer.id");
            }else{
                $query = SayacGelen::whereIn('sayacgelen.id', $sayacgelenlist)
                    ->orWhere(function ($query) {
                        $query->where('sayacgelen.servis_id', $this->servisid)->where('sayacgelen.beyanname', 0);
                    })
                    ->select(array("sayacgelen.id", "sayacgelen.serino", "sayacadi.sayacadi", "uretimyer.yeradi", "sayaccap.capadi"))
                    ->leftjoin("sayacadi", "sayacgelen.sayacadi_id", "=", "sayacadi.id")
                    ->leftjoin("sayaccap", "sayacgelen.sayaccap_id", "=", "sayaccap.id")
                    ->leftjoin("uretimyer", "sayacgelen.uretimyer_id", "=", "uretimyer.id");
            }
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
            $netsiscari_id=Input::get('netsiscari_id');
            if($netsiscari_id!="") {
                $netsiscarilist=explode(',',$netsiscari_id);
                $query = SayacGelen::whereIn('netsiscari_id',$netsiscarilist)->where('sayacgelen.servis_id',$this->servisid)->where('sayacgelen.beyanname',0)
                    ->select(array("sayacgelen.id","sayacgelen.serino","sayacadi.sayacadi","uretimyer.yeradi","sayaccap.capadi"))
                    ->leftjoin("sayacadi", "sayacgelen.sayacadi_id", "=", "sayacadi.id")
                    ->leftjoin("sayaccap", "sayacgelen.sayaccap_id", "=", "sayaccap.id")
                    ->leftjoin("uretimyer", "sayacgelen.uretimyer_id", "=", "uretimyer.id");
            }else{
                $query = SayacGelen::where('sayacgelen.servis_id',$this->servisid)->where('sayacgelen.beyanname', 0)
                    ->select(array("sayacgelen.id", "sayacgelen.serino", "sayacadi.sayacadi", "uretimyer.yeradi", "sayaccap.capadi"))
                    ->leftjoin("sayacadi", "sayacgelen.sayacadi_id", "=", "sayacadi.id")
                    ->leftjoin("sayaccap", "sayacgelen.sayaccap_id", "=", "sayaccap.id")
                    ->leftjoin("uretimyer", "sayacgelen.uretimyer_id", "=", "uretimyer.id");
            }
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
            $netsiscari_id=Auth::user()->netsiscari_id;
            //$netsiscarilist=Auth::user()->netsiscarilist;
            $sube = Sube::whereIn('netsiscari_id',$netsiscari_id)->where('aktif',1)->first();
            $tarih = Input::get('tarih');
            $beyannametarihi = date("Y-m-d", strtotime($tarih));
            $beyannameno = Input::get('beyannameno');
            $secilenler = Input::get('secilenler');
            $secilenlist=explode(',',$secilenler);
            $adet = Input::get('beyannameadet');
            $beyanname=Beyanname::whereIn('netsiscari_id',$sube->netsiscari_id)->where('no',$beyannameno)->where('servis_id',$this->servisid)->first();
            if($beyanname){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Bu Beyanname Numarası Sistemde Mevcut', 'text' => 'Beyanname Numarası Sistemde Zaten Kayıtlı.', 'type' => 'error'));
            }
            DB::beginTransaction();
            try {
                $beyanname = new Beyanname;
                $beyanname->servis_id = $this->servisid;
                $beyanname->netsiscari_id=$sube->netsiscari_id;
                $beyanname->no = $beyannameno;
                $beyanname->adet = $adet;
                $beyanname->secilenler = $secilenler;
                $beyanname->tarih = $beyannametarihi;
                $beyanname->durum = 0;
                $beyanname->kullanici_id = Auth::user()->id;
                $beyanname->save();

                BeyannameNo::whereIn('netsiscari_id',$sube->netsiscari_id)->where('servis_id', $this->servisid)->where('yil', date('Y'))->where('type', 'T')->update(['no'=>$beyannameno]);
                Beyanname::whereIn('netsiscari_id',$sube->netsiscari_id)->where('id','<>',$beyanname->id)->where('servis_id',$this->servisid)->update(['durum'=>1]);

                $sayacgelenler=SayacGelen::whereIn('id',$secilenlist)->get();
                foreach ($sayacgelenler as $sayacgelen){
                    $sayacgelen->beyanname=1;
                    $sayacgelen->save();
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
            $netsiscari_id=Auth::user()->netsiscari_id;
            $netsiscarilist=Auth::user()->netsiscarilist;
            if($netsiscarilist!="" && count($netsiscari_id)==1 ) {
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
                    BeyannameNo::whereIn('netsiscari_id',$netsiscari_id)->where('servis_id', $this->servisid)->where('yil', date('Y'))->where('type', 'T')->update(['no'=>$beyannameno]);
                    Beyanname::whereIn('netsiscari_id',$netsiscari_id)->where('no',$beyannameno)->where('servis_id',$this->servisid)->update(['durum'=>0]);
                }else{
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Beyanname Kayıdı Silinemedi', 'text' => 'Beyanname Kayıdı Silinirken Sorun Oluştu.', 'type' => 'error'));
                }
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-pencil', $bilgi->no. ' Numaralı Beyanname Kayıdı Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Beyanname Kayıt Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Beyanname Kayıdı Silindi', 'text' => 'Beyanname Kayıdı Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj'=>'true','title'=>'Yetki Hatası','text'=>'Bu Şube İçin Beyanname Silme Yetkiniz Yok','type'=>'warning'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Beyanname Kayıdı Silinemedi', 'text' => 'Beyanname Kayıdı Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getBeyannameduzenle($id) {
        try {
            $netsiscari_id=Auth::user()->netsiscari_id;
            $netsiscarilist=Auth::user()->netsiscarilist;
            if($netsiscarilist!="" && count($netsiscari_id)==1) {
                $netsiscarilist=explode(',',$netsiscarilist);
                $beyanname = Beyanname::find($id);
                if(BackendController::Listedemi($beyanname->netsiscari_id,$netsiscarilist))
                    return View::make($this->servisadi.'.beyannameduzenle', array('beyanname' => $beyanname))->with(array('title' => 'Beyanname Düzenle'));
                else
                    return Redirect::back()->with(array('mesaj'=>'true','title'=>'Yetki Hatası','text'=>'Bu Beyannameyi Düzenleme Yetkiniz Yok','type'=>'warning'));
            }else{
                return Redirect::back()->with(array('mesaj'=>'true','title'=>'Yetki Hatası','text'=>'Bu Şube İçin Beyanname Düzenleme Yetkiniz Yok','type'=>'warning'));
            }
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
            $netsiscari_id=Auth::user()->netsiscari_id;
            $tarih = Input::get('tarih');
            $beyannametarihi = date("Y-m-d", strtotime($tarih));
            $beyannameno = Input::get('beyannameno');
            $secilenler = Input::get('secilenler');
            $secilenlist=explode(',',$secilenler);
            $adet = Input::get('beyannameadet');
            $beyanname=Beyanname::whereIn('netsiscari_id',$netsiscari_id)->where('id','<>',$id)->where('no',$beyannameno)->where('servis_id',$this->servisid)->first();
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

                BeyannameNo::whereIn('netsiscari_id',$netsiscari_id)->where('servis_id', $this->servisid)->where('yil', date('Y'))->where('type', 'T')->update(['no'=>$beyannameno]);
                Beyanname::whereIn('netsiscari_id',$netsiscari_id)->where('id','<>',$beyanname->id)->where('servis_id',$this->servisid)->update(['durum'=>1]);

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

    public function getAbonesorgula(){
        try {
            if(Input::has('serino')){
                $serino=trim(Input::get('serino'));

                $abonesayac = AboneSayac::where('serino',$serino)->first();
                if($abonesayac){
                    $abonetahsis = AboneTahsis::where('abonesayac_id',$abonesayac->id)->first();
                    if($abonetahsis){
                        $abone = Abone::find($abonetahsis->abone_id);
                        $abone->netsiscari = NetsisCari::find(($abone->netsiscari_id));
                        $abone->uretimyer = UretimYer::find(($abone->uretimyer_id));
                        $abone->il = Iller::find(($abone->iller_id));
                        $abone->ilce = Ilceler::find(($abone->ilceler_id));
                        if(BackendController::SayacDurum($serino,$abone->uretimyer_id,$this->servisid,1,false,$abonesayac->sayacadi_id)){
                            $abone->tamirdurum=1;
                        }else{
                            $abone->tamirdurum=0;
                        }
                        return Response::json(array('durum' => 1, 'abone' => $abone,'abonetahsis'=>$abonetahsis));
                    }else{
                        return Response::json(array('durum' => 0, 'title' => 'Sayaç Bilgisi Bulunamadı', 'text' =>'Seri Numarası Hiç Bir Aboneye Kayıtlı Değil!', 'type' => 'error'));
                    }
                }else{
                    return Response::json(array('durum' => 0, 'title' => 'Sayaç Bilgisi Bulunamadı', 'text' =>'Seri Numarası Abone Sayaçları Arasında Bulunamadı!', 'type' => 'error'));
                }
            }else if(Input::has('adisoyadi')){
                $adisoyadi=Input::get('adisoyadi');
                $subekodu=Input::get('subekodu');
                $aboneler = Abone::where('adisoyadi','LIKE','%'.$adisoyadi.'%')->where('subekodu',$subekodu)->get();
                $adet = $aboneler->count();
                if($adet>1) {
                    return Response::json(array('durum' => 1, 'aboneler' => $aboneler,'adet'=>$adet));
                }else if($adet>0){
                    $abone = $aboneler->first();
                    $abone->netsiscari = NetsisCari::find(($abone->netsiscari_id));
                    $abone->uretimyer = UretimYer::find(($abone->uretimyer_id));
                    $abone->il = Iller::find(($abone->iller_id));
                    $abone->ilce = Ilceler::find(($abone->ilceler_id));
                    return Response::json(array('durum' => 1, 'abone' => $abone,'adet'=>$adet));
                }else{
                    return Response::json(array('durum' => 0, 'title' => 'Abone Bilgisi Bulunamadı', 'text' =>'Girilen Bilgiler Aboneler Arasında Bulunamadı!', 'type' => 'error'));
                }
            }else if(Input::has('aboneid')){
                $aboneid=Input::get('aboneid');
                $abone = Abone::find($aboneid);
                if($abone){
                    $abone->netsiscari = NetsisCari::find(($abone->netsiscari_id));
                    $abone->uretimyer = UretimYer::find(($abone->uretimyer_id));
                    $abone->il = Iller::find(($abone->iller_id));
                    $abone->ilce = Ilceler::find(($abone->ilceler_id));
                    return Response::json(array('durum' => 1, 'abone' => $abone));
                }else{
                    return Response::json(array('durum' => 0, 'title' => 'Abone Bilgisi Bulunamadı', 'text' =>'Girilen Bilgiler Aboneler Arasında Bulunamadı!', 'type' => 'error'));
                }
            }else{
                return Response::json(array('durum' => 0, 'title' => 'Abone Bilgisi Bulunamadı', 'text' =>'abone Bilgisi Bulunamadı!', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => 0, 'title' => 'Sayaç Bilgisi Bulunamadı', 'text' =>str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getAbonedegisikligi($tahsisid){
        $abonetahsis = AboneTahsis::find($tahsisid);
        $abone = Abone::find($abonetahsis->abone_id);
        $abone->netsiscari = NetsisCari::find($abone->netsiscari_id);
        $abone->uretimyer = UretimYer::find($abone->uretimyer_id);
        $abone->il = Iller::find($abone->iller_id);
        $abone->ilce = Ilceler::find($abone->ilceler_id);
        $abonesayac = AboneSayac::find($abonetahsis->abonesayac_id);
        $abonesayac->sayacadi=SayacAdi::find($abonesayac->sayacadi_id);
        $abonesayac->sayaccap=SayacCap::find($abonesayac->sayaccap_id);
        $abonesayac->sayactur=SayacTur::find($abonesayac->sayactur_id);
        $netsiscariid=Auth::user()->netsiscari_id;
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        $netsiscariler=NetsisCari::whereIn('id',Auth::user()->netsiscari_id)->get(array('id'))->toArray();
        $uretimyerid=CariYer::whereIn('netsiscari_id',$netsiscariler)->where('durum',1)->get(array('uretimyer_id'))->toArray();
        $uretimyerleri = UretimYer::whereIn('id',$uretimyerid)->get();
        if($sube){
            $sube->netsiscari = NetsisCari::find($sube->netsiscari_id);
            $iller = Iller::all();
            if($abone->netsiscari_id!=$sube->netsiscari_id){
                $ilceler = Ilceler::where('iller_id',$abone->iller_id)->get();
            }else{
                $ilceler = Ilceler::where('iller_id',$sube->iller_id)->get();
            }
            $subeyetkili=SubeYetkili::where('kullanici_id',Auth::user()->id)->where('aktif',1)->get(array('netsiscari_id'))->toArray();
            $netsiscariler = NetsisCari::where('caridurum','A')->whereIn('caritipi',array('A','D'))
                ->where(function($query)use($subeyetkili,$sube){$query->whereIn('id',$subeyetkili)->orwhereIn('subekodu',array(-1,$sube->subekodu));})
                ->whereNotIn('carikod',(function ($query) use ($sube) {$query->select('carikod')->from('kodharichar')->where('subekodu', $sube->subekodu);}))
                ->whereNotIn('id',array($sube->netsiscari_id))
                ->orderBy('cariadi','asc')->get();
            $sayacturlist=explode(',',$sube->sayactur);
            $sayacadilist=explode(',',$sube->sayacadlari);
            $sayacturleri = SayacTur::whereIn('id',$sayacturlist)->get();
            $sayacadlari = SayacAdi::whereIn('sayactur_id',$sayacturlist)->whereIn('id',$sayacadilist)->get();
            $sayaccaplari = SayacCap::all();
            return View::make($this->servisadi.'.abonedegisikligi',array('abonetahsis'=>$abonetahsis,'abone'=>$abone,'abonesayac'=>$abonesayac,'uretimyerleri'=>$uretimyerleri,'sayacturleri'=>$sayacturleri,'sayacadlari'=>$sayacadlari,'sayaccaplari'=>$sayaccaplari,'netsiscariler'=> $netsiscariler,'sube'=>$sube,'iller'=>$iller,'ilceler'=>$ilceler))->with(array('title'=>'Abone Değişikliği Ekranı'));
        }else{
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkisiz', 'text' => 'Abone Değişikliği Yetkiniz Yok', 'type' => 'error'));
        }
    }

    public function postAbonedegistir(){
        try{

            $kayittipi = Input::get('kayittipi');
            $eskiaboneid = Input::get('eskiabone');
            $eskitahsisid = Input::get('eskitahsis');
            $sayacadresi = Input::get('sayacadresi');
            $sayacbilgi = Input::get('sayacbilgi');
            $sayaciletisim = Input::get('sayaciletisim');
            if($kayittipi=="1"){ // olan aboneye aktarılacak
                $rules = ['kayittipi'=>'required','secilenabone'=>'required'];
                $validate = Validator::make(Input::all(),$rules);
                $messages = $validate->messages();
                if ($validate->fails()) {
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
                }
                $aboneid=Input::get('secilenabone');
                $eskiabone = Abone::find($eskiaboneid);
                $bilgi=$eskiabone;
                $eskitahsis = AboneTahsis::find($eskitahsisid);
                $abonesayac = AboneSayac::find($eskitahsis->abonesayac_id);
                $guncelabone = Abone::find($aboneid);
                $yetkili=SubeYetkili::where('kullanici_id',Auth::user()->id)->where('aktif',1)->first();
                if(!$yetkili){
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkisiz', 'text' => 'Abone Değişiklik Yetkiniz Yok', 'type' => 'error'));
                }
                DB::beginTransaction();
                try{
                    $abonesayac->uretimyer_id=$guncelabone->uretimyer_id;
                    $abonesayac->adres=BackendController::StrtoUpper($sayacadresi);
                    $abonesayac->bilgi=$sayacbilgi;
                    $abonesayac->iletisim=$sayaciletisim;
                    $abonesayac->save();

                    $sayac = Sayac::where('serino',$abonesayac->serino)->where('uretimyer_id',$eskiabone->uretimyer_id)->first();
                    if(!$sayac){
                        DB::rollBack();
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Abone Kayıdı Değiştirilemedi', 'text' => 'Seri Numarası Sayaç Listesinde Bu Eski Aboneye Ait Değil!', 'type' => 'error'));
                    }
                    $sayac->uretimyer_id=$guncelabone->uretimyer_id;
                    $sayac->save();

                    $eskitahsis->delete();

                    $abonetahsis = new AboneTahsis;
                    $abonetahsis->abone_id=$guncelabone->id;
                    $abonetahsis->abonesayac_id=$abonesayac->id;
                    $abonetahsis->tahsistarihi=date('Y-m-d H:i:s');
                    $abonetahsis->save();

                    $serviskayit = ServisKayit::where('abonetahsis_id',$eskitahsisid)->where('durum',0)->get();
                    if($serviskayit->count()>0){
                        foreach ($serviskayit as $kayit){
                            $kayit->abonetahsis_id=$abonetahsis->id;
                            $kayit->netsiscari_id=$guncelabone->netsiscari_id;
                            $kayit->uretimyer_id=$guncelabone->uretimyer_id;
                            $kayit->save();
                        }
                    }
                }catch (Exception $e){
                    Log::error($e);
                    DB::rollBack();
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Abone Kayıdı Değiştirilemedi', 'text' => 'Abonenin Bilgileri Değiştirilemedi', 'type' => 'error'));
                }
            }else{ //yeni aboneye aktarılacak
                $rules = ['subekodu'=>'required','kayittipi'=>'required','cariadi'=>'required','adisoyadi'=>'required','tckimlikno'=>'required','telefon'=>'required','uretimyer'=>'required','adres'=>'required','il'=>'required','ilce'=>'required'];
                $validate = Validator::make(Input::all(),$rules);
                $messages = $validate->messages();
                if ($validate->fails()) {
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
                }
                $adisoyadi=Input::get('adisoyadi');
                $uretimyer=Input::get('uretimyer');
                $netsiscari = Input::get('cariadi');
                $vergidairesi=Input::get('vergidairesi');
                $tckimlikno=Input::get('tckimlikno');
                $aboneno=Input::get('aboneno');
                $telefon=Input::get('telefon');
                $adres=Input::get('adres');
                $il=Input::get('il');
                $ilce=Input::get('ilce');
                $telefon = preg_replace('/\D/','',$telefon);
                $telefon = mb_substr($telefon, 0, 4)." ".mb_substr($telefon, 4, 3)." ".mb_substr($telefon, 7, 2)." ".mb_substr($telefon, 9, 2);
                $eskiabone = Abone::find($eskiaboneid);
                $bilgi=$eskiabone;
                $eskitahsis = AboneTahsis::find($eskitahsisid);
                $abonesayac = AboneSayac::find($eskitahsis->abonesayac_id);
                $yetkili=SubeYetkili::where('kullanici_id',Auth::user()->id)->where('aktif',1)->first();
                if(!$yetkili){
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkisiz', 'text' => 'Abone Kaydetme Yetkiniz Yok', 'type' => 'error'));
                }
                DB::beginTransaction();
                try{
                    $abonesayac->uretimyer_id=$uretimyer;
                    $abonesayac->adres=BackendController::StrtoUpper($sayacadresi);
                    $abonesayac->bilgi=$sayacbilgi;
                    $abonesayac->iletisim=$sayaciletisim;
                    $abonesayac->save();

                    $sayac = Sayac::where('serino',$abonesayac->serino)->where('uretimyer_id',$eskiabone->uretimyer_id)->first();
                    if(!$sayac){
                        DB::rollBack();
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Abone Kayıdı Değiştirilemedi', 'text' => 'Seri Numarası Sayaç Listesinde Bu Eski Aboneye Ait Değil!', 'type' => 'error'));
                    }
                    $sayac->uretimyer_id=$uretimyer;
                    $sayac->save();

                    $eskitahsis->delete();

                    $guncelabone = new Abone;
                    $guncelabone->subekodu = $eskiabone->subekodu;
                    $guncelabone->adisoyadi=BackendController::StrtoUpper($adisoyadi);
                    $guncelabone->netsiscari_id = $netsiscari;
                    $guncelabone->vergidairesi=BackendController::StrtoUpper($vergidairesi);
                    $guncelabone->tckimlikno=$tckimlikno;
                    $guncelabone->abone_no=$aboneno;
                    $guncelabone->telefon=$telefon;
                    $guncelabone->faturaadresi=BackendController::StrtoUpper($adres);
                    $guncelabone->iller_id=$il;
                    $guncelabone->ilceler_id=$ilce;
                    $guncelabone->uretimyer_id=$uretimyer;
                    $guncelabone->netsiscari_id=$netsiscari;
                    $guncelabone->kullanici_id=Auth::user()->id;
                    $guncelabone->save();

                    $abonetahsis = new AboneTahsis;
                    $abonetahsis->abone_id=$guncelabone->id;
                    $abonetahsis->abonesayac_id=$abonesayac->id;
                    $abonetahsis->tahsistarihi=date('Y-m-d H:i:s');
                    $abonetahsis->save();

                    $serviskayit = ServisKayit::where('abonetahsis_id',$eskitahsisid)->where('durum',0)->get();
                    if($serviskayit->count()>0){
                        foreach ($serviskayit as $kayit){
                            $kayit->abonetahsis_id=$abonetahsis->id;
                            $kayit->netsiscari_id=$guncelabone->netsiscari_id;
                            $kayit->uretimyer_id=$guncelabone->uretimyer_id;
                            $kayit->save();
                        }
                    }
                }catch (Exception $e){
                    Log::error($e);
                    DB::rollBack();
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Abone Kayıdı Yapılamadı', 'text' => 'Abonenin Bilgileri Kaydedilemedi', 'type' => 'error'));
                }
            }
            BackendController::IslemEkle(2,Auth::user()->id,'label-warning','fa-edit',$bilgi->adisoyadi.' İsimli Abone Kayıdı Değiştirildi.','Değişiklik Yapan:'.Auth::user()->adi_soyadi.',Abone Numarası:'.$bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/abonekayit')->with(array('mesaj' => 'true', 'title' => 'Abone Kayıdı Değiştirildi', 'text' => 'Abone Kayıdı Başarıyla Değiştirildi', 'type' => 'success'));
        }catch(Exception $e){
            Log::error($e);
            DB::rollBack();
            Input::flash();
            return Redirect::to($this->servisadi.'/abonekayit')->with(array('mesaj' => 'true', 'title' => 'Abone Kayıdı Değiştirilemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getSatissorgula(){
        try {
            if(Input::has('serino')){
                $serino=trim(Input::get('serino'));

                $abonesayac = AboneSayac::where('serino',$serino)->first();
                if($abonesayac){
                        $satisfatura = SubeSayacSatis::where('sayaclar','LIKE',$abonesayac->id.';%')
                            ->orWhere('sayaclar','LIKE','%;'.$abonesayac->id.';%')
                            ->orWhere('sayaclar','LIKE','%;'.$abonesayac->id)
                            ->orWhere('sayaclar','LIKE',$abonesayac->id)->first();
                        if($satisfatura)
                            return Response::json(array('durum' => 1, 'satisfatura' => $satisfatura));
                        else
                            return Response::json(array('durum' => 0, 'title' => 'Fatura Bilgisi Bulunamadı', 'text' =>'Seri Numarası Abone Sayaçları Arasında Bulunamadı!', 'type' => 'error'));
                }else{
                    return Response::json(array('durum' => 0, 'title' => 'Fatura Bilgisi Bulunamadı', 'text' =>'Seri Numarası Abone Sayaçları Arasında Bulunamadı!', 'type' => 'error'));
                }
            }else{
                return Response::json(array('durum' => 0, 'title' => 'Fatura Bilgisi Bulunamadı', 'text' =>'Seri No Boş Geçilmiş!', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => 0, 'title' => 'Sayaç Bilgisi Bulunamadı', 'text' =>str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }
}
