<?php
//transaction işlemi tamamlandı
class SubedatabaseController extends BackendController {
    public $servisadi = 'subedatabase';
    public $servisid = 6;
    public $servisbilgi = 'Şube';

    public function getUrunler() {
        return View::make($this->servisadi.'.urunler')->with(array('title'=>'Ürün Listesi'));
    }

    public function postUrunlist() {
        $netsiscari_id=Input::get('netsiscari_id');
        $sube = null;
        if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $sube=Sube::whereIn('netsiscari_id',$netsiscarilist)->where('aktif',1)->first();
            if($sube){
                $sube->depo = NetsisDepolar::withTrashed()->find($sube->netsisdepolar_id);
            $query = SubeUrun::where('subeurun.subekodu',$sube->subekodu)->where('sube.aktif',1)
                ->select(array("subeurun.id","subeurun.urunadi","sube.adi","netsisstokkod.kodu",DB::raw("0 as adet"),"subeurun.gdurum",
                    "subeurun.nurunadi","sube.nadi","netsisstokkod.nkodu","subeurun.ndurum","subeurun.urundurum","subeurun.depokodu"))
                ->leftjoin("sube", "subeurun.subekodu", "=", "sube.subekodu")
                ->leftjoin("netsisstokkod", "subeurun.netsisstokkod_id", "=", "netsisstokkod.id");
            }else{
                $query = SubeUrun::where('sube.aktif',1)
                    ->select(array("subeurun.id","subeurun.urunadi","sube.adi","netsisstokkod.kodu",DB::raw("0 as adet"),"subeurun.gdurum",
                    "subeurun.nurunadi","sube.nadi","netsisstokkod.nkodu","subeurun.ndurum","subeurun.urundurum","subeurun.depokodu"))
                    ->leftjoin("sube", "subeurun.subekodu", "=", "sube.subekodu")
                    ->leftjoin("netsisstokkod", "subeurun.netsisstokkod_id", "=", "netsisstokkod.id");
            }
        }else{
            $query = SubeUrun::where('sube.aktif',1)
                ->select(array("subeurun.id","subeurun.urunadi","sube.adi","netsisstokkod.kodu",DB::raw("0 as adet"),"subeurun.gdurum",
                "subeurun.nurunadi","sube.nadi","netsisstokkod.nkodu","subeurun.ndurum","subeurun.urundurum","subeurun.depokodu"))
                ->leftjoin("sube", "subeurun.subekodu", "=", "sube.subekodu")
                ->leftjoin("netsisstokkod", "subeurun.netsisstokkod_id", "=", "netsisstokkod.id");
        }
        return Datatables::of($query)
            ->editColumn('adet', function ($model) use($sube) {
                if($sube){
                    $stok=StBakiye::where('STOK_KODU',$model->kodu)->where('DEPO_KODU',$model->depokodu)->first();
                    if(!$stok) $stok=new StBakiye(array('BAKIYE'=>0));
                    return intval($stok->BAKIYE);
                }else{
                    return 0;
                }
            })
            ->addColumn('islemler', function ($model) use($netsiscari_id,$sube) {
                $root = BackendController::getRootDizin();
                if($netsiscari_id && $sube)
                    if(!$model->urundurum)
                       return "<a class='btn btn-sm btn-warning' href='".$root."/subedatabase/urunduzenle/".$model->id."' > Düzenle </a>
                       <a href='#portlet-delete' data-toggle='modal' data-id='{$model->id}' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                    else
                       return "<a class='btn btn-sm btn-warning' href='".$root."/subedatabase/urunduzenle/".$model->id."' > Düzenle </a>";
                else
                    return "<a class='btn btn-sm btn-info' href='".$root."/subedatabase/urungoster/".$model->id."' > Göster </a>";
            })
            ->make(true);
    }

    public function getUrunekle() {
        $netsiscariid=NetsisCari::whereIn('id',Auth::user()->netsiscari_id)->get(array('id'))->toArray();
        $netsisstokkodlari = NetsisStokKod::all();
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        if($sube){
            $sayacturlist=explode(',',$sube->sayactur);
            $sayacadlari = SayacAdi::whereIn('sayactur_id',$sayacturlist)->get();
            $sayaccaplari = SayacCap::all();
        }else{
            $sayacadlari = SayacAdi::all();
            $sayaccaplari = SayacCap::all();
        }
        $parabirimleri = ParaBirimi::all();
        $netsisdepolar = NetsisDepolar::where('subekodu',$sube->subekodu)->get();
        return View::make($this->servisadi.'.urunekle',array('parabirimleri'=>$parabirimleri,'netsisstokkodlari'=>$netsisstokkodlari,'sayacadlari'=>$sayacadlari,'sayaccaplari'=>$sayaccaplari,'sube'=>$sube,'netsisdepolar'=>$netsisdepolar))->with(array('title'=>'Ürün Ekle'));
    }

    public function postUrunekle(){
        try {
            $rules = ['urunadi'=>'required','parabirimi'=>'required','stokkod'=>'required','netsisdepo'=>'required'];
            $validate = Validator::make(Input::all(),$rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $urunadi = Input::get('urunadi');
            $subekodu = Input::get('subekodu');
            $stokkod = Input::get('stokkod');
            $sayacadi = Input::get('sayacadi');
            $sayaccapi = Input::get('sayaccapi');
            $durum = Input::get('durum');
            $parabirimi = Input::get('parabirimi');
            $netsisdepo = Input::get('netsisdepo');
            if (Input::has('baglanti')) {
                $baglanti = 1;
            } else {
                $baglanti = 0;
            }
            if (Input::has('kontrol')) {
                $kontrol = 1;
            } else {
                $kontrol = 0;
            }
            if (Input::has('ekstra')) {
                $ekstra = 1;
            } else {
                $ekstra = 0;
            }
            $fiyat = doubleval(Input::get('fiyat'));
            DB::beginTransaction();
            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif',1)->first();
            if (!$subeyetkili) {
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Yetkili Hatası', 'text' => 'Bu kullanıcı ürün kaydetmek için yetkili değil!', 'type' => 'error'));
            }
            $netsiscari = NetsisCari::find($subeyetkili->netsiscari_id);
            if (SubeUrun::where('netsisstokkod_id', $stokkod)->where('subekodu', $subekodu)->first()) {
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Kaydedilemedi', 'text' => 'Bu Ürün Kodu Şube için Sistemde Mevcut!.', 'type' => 'error'));
            }
            $subeurun = new SubeUrun;
            $subeurun->subekodu = $subekodu;
            $subeurun->urunadi = $urunadi;
            $subeurun->netsiscari_id = $netsiscari->id;
            $subeurun->netsisstokkod_id = $stokkod;
            $subeurun->baglanti = $baglanti;
            $subeurun->kontrol = $kontrol;
            $subeurun->ekstra = $ekstra;
            $subeurun->fiyat = $fiyat;
            $subeurun->parabirimi_id = $parabirimi;
            $subeurun->depokodu = $netsisdepo;
            if($baglanti){
                $subeurun->sayacadi_id=$sayacadi;
                $subeurun->sayaccap_id=$sayaccapi ? $sayaccapi : 1;
            }
            $subeurun->durum=$durum;
            $subeurun->kullanici_id = Auth::user()->id;
            $subeurun->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $urunadi . ' İsimli Ürün Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ürün Numarası:' . $subeurun->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/urunler')->with(array('mesaj' => 'true', 'title' => 'Ürün Kaydedildi', 'text' => 'Ürün Kayıdı Başarıyla Yapıldı', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Kaydedilemedi', 'text' => 'Ürün Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getUrunduzenle($id) {
        $urun=SubeUrun::find($id);
        $netsiscariid=NetsisCari::whereIn('id',Auth::user()->netsiscari_id)->get(array('id'))->toArray();
        $netsisstokkodlari = NetsisStokKod::all();
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        if($sube){
            $sayacturlist=explode(',',$sube->sayactur);
            $sayacadlari = SayacAdi::whereIn('sayactur_id',$sayacturlist)->get();
            $sayaccaplari = SayacCap::all();
        }else{
            $sayacadlari = SayacAdi::all();
            $sayaccaplari = SayacCap::all();
        }
        $parabirimleri = ParaBirimi::all();
        $netsisdepolar = NetsisDepolar::where('subekodu',$sube->subekodu)->get();
        return View::make($this->servisadi.'.urunduzenle',array('urun'=>$urun,'parabirimleri'=>$parabirimleri,'netsisstokkodlari'=>$netsisstokkodlari,'sayacadlari'=>$sayacadlari,'sayaccaplari'=>$sayaccaplari,'sube'=>$sube,'netsisdepolar'=>$netsisdepolar))->with(array('title'=>'Ürün Bilgi Düzenle'));
    }

    public function postUrunduzenle($id){
        try {
            $rules = ['urunadi'=>'required','parabirimi'=>'required','stokkod'=>'required','netsisdepo'=>'required'];
            $validate = Validator::make(Input::all(),$rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $urunadi = Input::get('urunadi');
            $subekodu = Input::get('subekodu');
            $stokkod = Input::get('stokkod');
            $sayacadi = Input::get('sayacadi');
            $sayaccapi = Input::get('sayaccapi');
            $durum = Input::get('durum');
            $parabirimi = Input::get('parabirimi');
            $netsisdepo = Input::get('netsisdepo');
            if (Input::has('baglanti')) {
                $baglanti = 1;
            } else {
                $baglanti = 0;
            }
            if (Input::has('kontrol')) {
                $kontrol = 1;
            } else {
                $kontrol = 0;
            }
            if (Input::has('ekstra')) {
                $ekstra = 1;
            } else {
                $ekstra = 0;
            }
            $fiyat = doubleval(Input::get('fiyat'));
            DB::beginTransaction();
            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif',1)->first();
            if (!$subeyetkili) {
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Yetkili Hatası', 'text' => 'Bu kullanıcı ürün kaydetmek için yetkili değil!', 'type' => 'error'));
            }
            $netsiscari = NetsisCari::find($subeyetkili->netsiscari_id);
            if (SubeUrun::where('netsisstokkod_id', $stokkod)->where('subekodu', $subekodu)->where('id', '<>', $id)->first()) {
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Kaydedilemedi', 'text' => 'Bu Ürün Adı Sistemde Mevcut!.', 'type' => 'error'));
            }
            $subeurun = SubeUrun::find($id);
            $bilgi = clone $subeurun;
            $subeurun->subekodu = $subekodu;
            $subeurun->urunadi = $urunadi;
            $subeurun->netsiscari_id = $netsiscari->id;
            $subeurun->netsisstokkod_id = $stokkod;
            $subeurun->baglanti = $baglanti;
            $subeurun->kontrol = $kontrol;
            $subeurun->ekstra = $ekstra;
            $subeurun->fiyat = $fiyat;
            $subeurun->parabirimi_id = $parabirimi;
            $subeurun->depokodu = $netsisdepo;
            if($baglanti){
                $subeurun->sayacadi_id=$sayacadi;
                $subeurun->sayaccap_id=$sayaccapi ? $sayaccapi : 1;
            }else{
                $subeurun->sayacadi_id=NULL;
                $subeurun->sayaccap_id=NULL;
            }
            $subeurun->durum = $durum;
            $subeurun->kullanici_id = Auth::user()->id;
            $subeurun->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $bilgi->urunadi . ' İsimli Ürün Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Ürün Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/urunler')->with(array('mesaj' => 'true', 'title' => 'Ürün Bilgisi Güncellendi', 'text' => 'Ürün Bilgisi Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Bilgisi Güncellenemedi', 'text' => 'Ürün Bilgisi GÜncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getUrunsil($id){
        try {
            DB::beginTransaction();
            $urun = SubeUrun::find($id);
            if($urun){
                $bilgi = clone $urun;
                if ($urun->urundurum == 1) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Kayıdı Silinemez', 'text' => 'Ürün Sayaç Satışında Kullanılmış.', 'type' => 'error'));
                }else if($urun->urundurum == 2 ){
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Kayıdı Silinemez', 'text' => 'Ürün Servis Kayıdında Kullanılmış.', 'type' => 'error'));
                }
                $urun->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $bilgi->urunadi . ' İsimli Ürün Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Ürün Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Silindi', 'text' => 'Ürün Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Silinemedi', 'text' => 'Ürün Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Silinemedi', 'text' => 'Ürün Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getUrungoster($id) {
        $urun=SubeUrun::find($id);
        $urun->netsisstokkod=NetsisStokKod::find($urun->netsisstokkod_id);
        $urun->netsisdepo=NetsisDepolar::withTrashed()->where('kodu',$urun->depokodu)->first();
        $urun->parabirimi = ParaBirimi::find($urun->parabirimi_id);
        if($urun->baglanti){
            $urun->sayacadi=SayacAdi::find($urun->sayacadi_id);
            $urun->sayaccap=SayacCap::find($urun->sayaccap_id);
        }
        return View::make($this->servisadi.'.urungoster',array('urun'=>$urun))->with(array('title'=>'Ürün Bilgisi'));
    }

    public function getPersonel() {
        return View::make($this->servisadi.'.personel')->with(array('title'=>'Personel Bilgisi'));
    }

    public function postPersonellist() {
        $netsiscari_id=Input::get('netsiscari_id');
        if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $query = SubePersonel::whereIn('sube.netsiscari_id',$netsiscarilist)->where('sube.aktif','1')
                ->select(array("subepersonel.id", "kullanici.adi_soyadi", "sube.adi", "kullanici.nadi_soyadi", "sube.nadi"))
                ->leftjoin("sube", "subepersonel.subekodu", "=", "sube.subekodu")
                ->leftjoin("kullanici", "subepersonel.kullanici_id", "=", "kullanici.id");
        }else{
            $query = SubePersonel::where('sube.aktif','1')
                ->select(array("subepersonel.id", "kullanici.adi_soyadi", "sube.adi", "kullanici.nadi_soyadi", "sube.nadi"))
                ->leftjoin("sube", "subepersonel.subekodu", "=", "sube.subekodu")
                ->leftjoin("kullanici", "subepersonel.kullanici_id", "=", "kullanici.id");
        }
        return Datatables::of($query)
            ->addColumn('islemler',function ($model)use($netsiscari_id) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/subedatabase/personelduzenle/".$model->id."' > Düzenle </a>
                        <a href='#portlet-delete' data-toggle='modal' data-id='{$model->id}' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getPersonelekle() {
        $netsiscariler=NetsisCari::whereIn('id',Auth::user()->netsiscari_id)->get(array('id'))->toArray();
        $personeller=SubePersonel::all(array('kullanici_id'))->toArray();
        $kullanicilar=Kullanici::whereNotIn('id',$personeller)->whereIn('grup_id',array(17,18))->where('aktifdurum',1)->get();
        if(count($netsiscariler)>0)
            $subeler=Sube::whereIn('netsiscari_id',$netsiscariler)->where('aktif',1)->get();
        else{
            $subeler=Sube::where('aktif',1)->get();
        }
        return View::make($this->servisadi.'.personelekle',array('subeler'=>$subeler,'kullanicilar'=>$kullanicilar))->with(array('title'=>'Personel Kayıdı Ekle'));
    }

    public function postPersonelekle() {
        try {
            $rules = ['kullanici' => 'required', 'sube' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $kullaniciid = Input::get('kullanici');
            $subekodu = Input::get('sube');
            DB::beginTransaction();
            $kullanici=Kullanici::find($kullaniciid);
            $sube = Sube::where('subekodu',$subekodu)->where('aktif',1)->first();
            if (!$sube){
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkisiz', 'text' => 'Seçilen Şubenin Cari Bilgisi Ekli Değil!', 'type' => 'error'));
            }
            if(SubePersonel::where('subekodu',$sube->subekodu)->where('kullanici_id',$kullaniciid)->first()){
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Personel Kayıdı Hatası', 'text' => 'Personel Zaten Kayıtlı!', 'type' => 'error'));
            }
            $personel = new SubePersonel;
            $personel->subekodu = $sube->subekodu;
            $personel->kullanici_id = $kullaniciid;
            $personel->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle',$kullanici->adi_soyadi . ' İsimli Personel Kayıdı Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Personel Numarası:' . $personel->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/personel')->with(array('mesaj' => 'true', 'title' => 'Personel Kayıdı Yapıldı', 'text' => 'Personel Kayıdı Başarıyla Yapıldı', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::to($this->servisadi.'/personel')->with(array('mesaj' => 'true', 'title' => 'Personel Kayıdı Yapılamadı', 'text' => 'Personel Bilgileri Kaydedilemedi', 'type' => 'error'));
        }
    }

    public function getPersonelduzenle($id) {
        $personel = SubePersonel::find($id);
        $netsiscariler=NetsisCari::whereIn('id',Auth::user()->netsiscari_id)->get(array('id'))->toArray();
        $personeller=SubePersonel::where('id','<>',$id)->get(array('kullanici_id'))->toArray();
        $kullanicilar=Kullanici::whereNotIn('id',$personeller)->whereIn('grup_id',array(17,18))->where('aktifdurum',1)->get();
        if(count($netsiscariler)>0)
            $subeler=Sube::whereIn('netsiscari_id',$netsiscariler)->where('aktif',1)->get();
        else{
            $subeler=Sube::where('aktif',1)->get();
        }
        return View::make($this->servisadi.'.personelduzenle',array('personel'=>$personel,'subeler'=>$subeler,'kullanicilar'=>$kullanicilar))->with(array('title'=>'Personel Kayıdı Düzenleme Ekranı'));
    }

    public function postPersonelduzenle($id) {
        try {
            $rules = ['kullanici' => 'required', 'sube' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();

            $kullaniciid = Input::get('kullanici');
            $subekodu = Input::get('sube');
            $kullanici=Kullanici::find($kullaniciid);
            $sube = Sube::where('subekodu',$subekodu)->where('aktif',1)->first();
            if (!$sube){
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkisiz', 'text' => 'Seçilen Şubenin Cari Bilgisi Ekli Değil!', 'type' => 'error'));
            }
            if(SubePersonel::where('subekodu',$sube->subekodu)->where('kullanici_id',$kullaniciid)->where('id','<>',$id)->first()){
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Personel Kayıdı Hatası', 'text' => 'Personel Zaten Kayıtlı!', 'type' => 'error'));
            }
            $personel = SubePersonel::find($id);
            $bilgi = clone $personel;
            $personel->subekodu = $sube->subekodu;
            $personel->kullanici_id = $kullaniciid;
            $personel->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $kullanici->adi_soyadi . ' İsimli Personel Kayıdı Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Personel Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/personel')->with(array('mesaj' => 'true', 'title' => 'Personel Kayıdı Yapıldı', 'text' => 'Personel Kayıdı Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::to($this->servisadi.'/personel')->with(array('mesaj' => 'true', 'title' => 'Personel Kayıdı Yapılamadı', 'text' => 'Personel Bilgileri Güncellenemedi', 'type' => 'error'));
        }
    }

    public function getPersonelsil($id){
        try {
            DB::beginTransaction();
            $personel = SubePersonel::find($id);
            $kullanici=Kullanici::find($personel->kullanici_id);
            $bilgi = clone $personel;
            $personel->delete();
            BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $kullanici->adi_soyadi . ' İsimli Personel Kayıdı Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Personel Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Personel Kayıdı Silindi', 'text' => 'Personel Kayıdı Başarıyla Silindi.', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Personel Kayıdı Silinemedi', 'text' => 'Personel Kayıdı Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getAbonebilgi() {
        $netsiscariid=Auth::user()->netsiscari_id;
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        return View::make($this->servisadi.'.abonebilgi',array('sube'=>$sube))->with(array('title'=>'Abone Bilgi Görüntüleme Ekranı'));
    }

    public function getBilgigetir()
    {
        $tip = Input::get('tip');
        $kriter = Input::get('kriter');
        $subekodu = Input::get('subekodu');
        if($subekodu!=1){
            switch ($tip) {
                case 1: // seri numarası
                    $abonebilgi = AboneBilgi::where('serino',$kriter)->where('subekodu',$subekodu)->get();
                    if($abonebilgi->count()>0){
                        return array("durum" => true, "count" => $abonebilgi->count(),"abonebilgi"=>$abonebilgi);
                    }else{
                        return array("durum" => false, "type" => "error", "title" => "Abone Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Abone Bilgisi Bulunamadı.");
                    }
                    break;
                case 2: // Abone no
                    $abonebilgi = AboneBilgi::where('aboneno',$kriter)->where('subekodu',$subekodu)->get();
                    if($abonebilgi->count()>0){
                        return array("durum" => true, "count" => $abonebilgi->count(),"abonebilgi"=>$abonebilgi);
                    }else{
                        return array("durum" => false, "type" => "error", "title" => "Abone Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Abone Bilgisi Bulunamadı.");
                    }
                    break;
                case 3: // TC No
                    $abonebilgi = AboneBilgi::where('vno',$kriter)->where('subekodu',$subekodu)->get();
                    if($abonebilgi->count()>0){
                        return array("durum" => true, "count" => $abonebilgi->count(),"abonebilgi"=>$abonebilgi);
                    }else{
                        return array("durum" => false, "type" => "error", "title" => "Abone Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Abone Bilgisi Bulunamadı.");
                    }
                    break;
                case 4: // Telefon
                    $abonebilgi = AboneBilgi::where('subekodu',$subekodu)
                        ->where(function($query) use($kriter){ $query->where('telefon',$kriter)->orWhere('telefon2',$kriter)->orWhere('gsm',$kriter);})->get();
                    if($abonebilgi->count()>0){
                        return array("durum" => true, "count" => $abonebilgi->count(),"abonebilgi"=>$abonebilgi);
                    }else{
                        return array("durum" => false, "type" => "error", "title" => "Abone Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Abone Bilgisi Bulunamadı.");
                    }
                    break;
                case 5: // Adı Soyadı
                    $kriter = BackendController::StrNormalized($kriter);
                    $abonebilgi = AboneBilgi::where('nadisoyadi','LIKE','%'.$kriter.'%')->where('subekodu',$subekodu)->get();
                    if($abonebilgi->count()>0){
                        return array("durum" => true, "count" => $abonebilgi->count(),"abonebilgi"=>$abonebilgi);
                    }else{
                        return array("durum" => false, "type" => "error", "title" => "Abone Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Abone Bilgisi Bulunamadı.");
                    }
                    break;
                default: // seri numarası
                    $abonebilgi = AboneBilgi::where('serino',$kriter)->where('subekodu',$subekodu)->get();
                    if($abonebilgi->count()>0){
                        return array("durum" => true, "count" => $abonebilgi->count(),"abonebilgi"=>$abonebilgi);
                    }else{
                        return array("durum" => false, "type" => "error", "title" => "Abone Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Abone Bilgisi Bulunamadı.");
                    }
                    break;
            }
        }else{
            return array("durum" => false, "type" => "error", "title" => "Şube Bilgisi Bulunamadı", "text" => "Şubeye Ait Abone Bilgisi Mevcut Değil!");
        }
    }

    public function getListebilgigetir()
    {
        $id = Input::get('id');
        $subekodu = Input::get('subekodu');
        if($subekodu!=1){
            $abonebilgi = AboneBilgi::find($id);
            if($abonebilgi){
                return array("durum" => true,"abonebilgi"=>$abonebilgi);
            }else{
                return array("durum" => false, "type" => "error", "title" => "Abone Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Abone Bilgisi Bulunamadı.");
            }
        }else {
            return array("durum" => false, "type" => "error", "title" => "Şube Bilgisi Bulunamadı", "text" => "Şubeye Ait Abone Bilgisi Mevcut Değil!");
        }
    }

    public function getFaturabilgi() {
        $netsiscariid=Auth::user()->netsiscari_id;
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        $yillar = array_combine(range(date("Y"), 2011), range(date("Y"), 2011));
        return View::make($this->servisadi.'.faturabilgi',array('sube'=>$sube,'yillar'=>$yillar))->with(array('title'=>'Fatura Bilgi Görüntüleme Ekranı'));
    }

    public function getFaturalistegetir()
    {
        $subekodu = Input::get('subekodu');
        $sube = Sube::where('subekodu',$subekodu)->get(array('netsiscari_id'))->toArray();
        $netsiscari = NetsisCari::whereIn('id',$sube)->get(array(BackendController::ReverseTrk('carikod')))->toArray();
        $yil = Input::get('faturayil');
        if($yil!="") {
            $parabirimi = ParaBirimi::find(1);
            if ($yil != date('Y')) { //eski kayıtlar çekilecekse
                $dbname = 'MANAS'.$yil;
                $connection = BackendController::AddNewConnection('MANAS'.$yil);
                if($yil<2017) {
                    $faturakalem = Sthar::on($connection)->where('SUBE_KODU', 0)->where('STHAR_FTIRSIP', '1')->whereIn('STHAR_CARIKOD', $netsiscari)->where('STOK_KODU', 'NOT LIKE', 'SRV%')
                        ->whereNotExists(function ($query) use ($subekodu, $sube, $dbname) {
                            $query->select(DB::raw(1))->from('ServisTakip.dbo.subesayacsatis')
                                ->where('subekodu', $subekodu)->where('db_name', $dbname)->where('durum', 1)
                                ->whereRaw('subesayacsatis.faturano COLLATE TURKISH_CI_AS=TBLSTHAR.FISNO');
                        })->orderBy('STHAR_TARIH', 'asc')->get(array('FISNO'))->take(500)->toArray();
                    $fatura = Fatuirs::where('SUBE_KODU', 0)->where('FTIRSIP', '1')->WhereIn('FATIRS_NO', $faturakalem)->orderBy('TARIH', 'asc')->orderBy('FATIRS_NO', 'asc')->get(array('FATIRS_NO', 'CARI_KODU', 'TARIH', 'GENELTOPLAM', DB::raw('dbo.TRK(ACIKLAMA) as ACIKLAMA')));
                }else{
                    $faturakalem = Sthar::on($connection)->where('SUBE_KODU', $subekodu)->where('STHAR_FTIRSIP', '1')->where('STOK_KODU', 'NOT LIKE', 'SRV%')
                        ->whereNotExists(function ($query) use ($subekodu, $sube, $dbname) {
                            $query->select(DB::raw(1))->from('ServisTakip.dbo.subesayacsatis')
                                ->where('subekodu', $subekodu)->where('db_name', $dbname)->where('durum', 1)
                                ->whereRaw('subesayacsatis.faturano COLLATE TURKISH_CI_AS=TBLSTHAR.FISNO');
                        })->orderBy('STHAR_TARIH', 'asc')->get(array('FISNO'))->take(500)->toArray();
                    $fatura = Fatuirs::where('SUBE_KODU',$subekodu)->where('FTIRSIP','1')->WhereIn('FATIRS_NO',$faturakalem)->orderBy('TARIH','asc')->orderBy('FATIRS_NO','asc')->get(array('FATIRS_NO','CARI_KODU','TARIH','GENELTOPLAM',DB::raw('dbo.TRK(ACIKLAMA) as ACIKLAMA')));
                }
                BackendController::DeleteConnection($connection);
            }else{
                $dbname = 'MANAS'.date('Y');
                $faturakalem = Sthar::where('SUBE_KODU', $subekodu)->where('STHAR_FTIRSIP', '1')->where('STOK_KODU', 'NOT LIKE', 'SRV%')
                    ->whereNotExists(function ($query) use ($subekodu, $sube, $dbname) {
                        $query->select(DB::raw(1))->from('ServisTakip.dbo.subesayacsatis')
                            ->where('subekodu', $subekodu)->where('db_name', $dbname)->where('durum', 1)
                            ->whereRaw('subesayacsatis.faturano COLLATE TURKISH_CI_AS=TBLSTHAR.FISNO');
                    })->orderBy('STHAR_TARIH', 'asc')->get(array('FISNO'))->take(500)->toArray();
                $fatura = Fatuirs::where('SUBE_KODU',$subekodu)->where('FTIRSIP','1')->WhereIn('FATIRS_NO',$faturakalem)->orderBy('TARIH','asc')->orderBy('FATIRS_NO','asc')->get(array('FATIRS_NO','CARI_KODU','TARIH','GENELTOPLAM',DB::raw('dbo.TRK(ACIKLAMA) as ACIKLAMA')));
            }
            if($fatura->count()>0){
                return array("durum" => true,"count"=>$fatura->count(),"fatura" => $fatura,"parabirimi"=>$parabirimi,"dbname"=>$dbname);
            } else{
                return array("durum" => false, "type" => "error", "title" => "Fatura Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Fatura Bilgisi Bulunamadı.");
            }
        }else{
            return array("durum" => false, "type" => "error", "title" => "Fatura Bilgisi Getirilemedi", "text" => "Fatura Dönemi Boş Geçilmiş");
        }
    }

    public function getFaturabilgigetir()
    {
        try {
            $faturano = Input::get('faturano');
            $subekodu = Input::get('subekodu');
            $sube = Sube::where('subekodu', $subekodu)->where('aktif', 1)->first();
            $cariyerler = CariYer::where('netsiscari_id', $sube->netsiscari_id)->get();
            $serino = null;
            $serinolar = "";
            $uretimyerid = 0;
            if ($cariyerler->count() > 0)
                foreach ($cariyerler as $cariyer) {
                    $uretimyerid = $cariyer->uretimyer_id;
                    break;
                }
            $yil = Input::get('faturayil');
            $parabirimi = ParaBirimi::find(1);
            if ($faturano != "") {
                if ($yil != date('Y')) { //eski kayıtlar çekilecekse
                    $connection = BackendController::AddNewConnection('MANAS'.$yil);
                    if ($yil < 2017) {
                        $faturakalem = Sthar::on($connection)->where('SUBE_KODU', 0)->where('STHAR_FTIRSIP', '1')->where('FISNO', $faturano)->get(array('STOK_KODU', 'FISNO', 'STHAR_GCMIK', 'STHAR_GCKOD', 'STHAR_TARIH', 'STHAR_NF', 'STHAR_BF', 'STHAR_KDV', 'DEPO_KODU', 'STHAR_CARIKOD', 'PLASIYER_KODU', 'SIRA', 'SUBE_KODU', 'PROJE_KODU'));
                        $fatura = Fatuirs::on($connection)->where('SUBE_KODU', 0)->where('FTIRSIP', '1')->where('FATIRS_NO', $faturano)->first(array('FATIRS_NO', 'CARI_KODU', 'TARIH', 'BRUTTUTAR', 'KDV', 'GENELTOPLAM', 'FATKALEM_ADEDI', 'PROJE_KODU', 'PLA_KODU', DB::raw('dbo.TRK(KS_KODU) as KS_KODU'), 'KAYITYAPANKUL', 'KAYITTARIHI', DB::raw('dbo.TRK(ACIKLAMA) as ACIKLAMA')));
                        $faturaek = Fatuek::on($connection)->where('SUBE_KODU', 0)->where('FATIRSNO', $faturano)->where('FKOD', '1')->first(array('SUBE_KODU', 'FKOD', 'FATIRSNO', 'CKOD', DB::raw('dbo.TRK(ACIK1) as ACIK1'), DB::raw('dbo.TRK(ACIK2) as ACIK2'), DB::raw('dbo.TRK(ACIK3) as ACIK3'), DB::raw('dbo.TRK(ACIK4) as ACIK4'),
                            DB::raw('dbo.TRK(ACIK5) as ACIK5'), DB::raw('dbo.TRK(ACIK6) as ACIK6'), DB::raw('dbo.TRK(ACIK7) as ACIK7'), DB::raw('dbo.TRK(ACIK8) as ACIK8'), DB::raw('dbo.TRK(ACIK9) as ACIK9'), DB::raw('dbo.TRK(ACIK10) as ACIK10')));
                        $seritra = Seritra::on($connection)->where('SUBE_KODU', 0)->where('BELGENO', $faturano)->get(array('SERI_NO', 'STOK_KODU', 'GCKOD', 'BELGENO', 'HARACIK', 'SUBE_KODU', 'DEPOKOD'));
                        $fatura->cari = NetsisCari::where('carikod', BackendController::Trk($fatura->CARI_KODU))->first();
                        $fatura->plasiyer = Plasiyer::where('kodu', $fatura->PLA_KODU)->first();
                        $fatura->kasakod = KasaKod::where('kasakod', $fatura->KS_KODU)->first();
                        if ($seritra->count() > 0) {
                            foreach ($seritra as $seri) {
                                $sayaclar = Sayac::where('serino', $seri->SERI_NO)->get();
                                $serino = $serino == null ? $seri->SERI_NO : $serino;
                                $serinolar .= ($serinolar == "" ? "" : ",") . $seri->SERI_NO;
                                if ($sayaclar->count() > 0) {

                                    foreach ($sayaclar as $sayac) {
                                        if ($cariyerler->count() > 0)
                                            foreach ($cariyerler as $cariyer) {
                                                if ($sayac->uretimyer_id == $cariyer->uretimyer_id) {
                                                    $uretimyerid = $sayac->uretimyer_id;
                                                    break;
                                                }
                                            }
                                    }
                                }
                            }
                        } else {
                            $aciklama = trim($faturaek->ACIK8);
                            if (is_numeric($aciklama)) {
                                $sayaclar = Sayac::where('serino', trim($aciklama))->get();
                                if ($sayaclar->count() > 0) {
                                    $serinolar .= ($serinolar == "" ? "" : ",") . $aciklama;
                                    foreach ($sayaclar as $sayac) {
                                        if ($cariyerler->count() > 0)
                                            foreach ($cariyerler as $cariyer) {
                                                if ($sayac->uretimyer_id == $cariyer->uretimyer_id) {
                                                    $uretimyerid = $sayac->uretimyer_id;
                                                    break;
                                                }
                                            }
                                    }

                                }
                            }
                        }
                        foreach ($faturakalem as $kalem) {
                            $kalem->netsisstokkod = NetsisStokKod::where('kodu', $kalem->STOK_KODU)->first();
                            if ($kalem->netsisstokkod)
                                $kalem->subeurun = SubeUrun::where('netsisstokkod_id', $kalem->netsisstokkod->id)->where('subekodu', $sube->subekodu)->first();
                        }
                    } else {
                        $faturakalem = Sthar::on($connection)->where('SUBE_KODU', $subekodu)->where('STHAR_FTIRSIP', '1')->where('FISNO', $faturano)->get(array('STOK_KODU', 'FISNO', 'STHAR_GCMIK', 'STHAR_GCKOD', 'STHAR_TARIH', 'STHAR_NF', 'STHAR_BF', 'STHAR_KDV', 'DEPO_KODU', 'STHAR_CARIKOD', 'PLASIYER_KODU', 'SIRA', 'SUBE_KODU', 'PROJE_KODU'));
                        $fatura = Fatuirs::on($connection)->where('SUBE_KODU', $subekodu)->where('FTIRSIP', '1')->where('FATIRS_NO', $faturano)->first(array('FATIRS_NO', 'CARI_KODU', 'TARIH', 'BRUTTUTAR', 'KDV', 'GENELTOPLAM', 'FATKALEM_ADEDI', 'PROJE_KODU', 'PLA_KODU', DB::raw('dbo.TRK(KS_KODU) as KS_KODU'), 'KAYITYAPANKUL', 'KAYITTARIHI', DB::raw('dbo.TRK(ACIKLAMA) as ACIKLAMA')));
                        $faturaek = Fatuek::on($connection)->where('SUBE_KODU', $subekodu)->where('FATIRSNO', $faturano)->where('FKOD', '1')->first(array('SUBE_KODU', 'FKOD', 'FATIRSNO', 'CKOD', DB::raw('dbo.TRK(ACIK1) as ACIK1'), DB::raw('dbo.TRK(ACIK2) as ACIK2'), DB::raw('dbo.TRK(ACIK3) as ACIK3'), DB::raw('dbo.TRK(ACIK4) as ACIK4'),
                            DB::raw('dbo.TRK(ACIK5) as ACIK5'), DB::raw('dbo.TRK(ACIK6) as ACIK6'), DB::raw('dbo.TRK(ACIK7) as ACIK7'), DB::raw('dbo.TRK(ACIK8) as ACIK8'), DB::raw('dbo.TRK(ACIK9) as ACIK9'), DB::raw('dbo.TRK(ACIK10) as ACIK10')));
                        $seritra = Seritra::on($connection)->where('SUBE_KODU', $subekodu)->where('BELGENO', $faturano)->get(array('SERI_NO', 'STOK_KODU', 'GCKOD', 'BELGENO', 'HARACIK', 'SUBE_KODU', 'DEPOKOD'));
                        $fatura->cari = NetsisCari::where('carikod', BackendController::Trk($fatura->CARI_KODU))->first();
                        $fatura->plasiyer = Plasiyer::where('kodu', $fatura->PLA_KODU)->first();
                        $fatura->kasakod = KasaKod::where('kasakod', $fatura->KS_KODU)->first();
                        foreach ($seritra as $seri) {
                            $sayaclar = Sayac::where('serino', $seri->SERI_NO)->get();
                            $serino = $serino == null ? $seri->SERI_NO : $serino;
                            $serinolar .= ($serinolar == "" ? "" : ",") . $seri->SERI_NO;
                            if ($sayaclar->count() > 0) {
                                {
                                    foreach ($sayaclar as $sayac) {
                                        if ($cariyerler->count() > 0)
                                            foreach ($cariyerler as $cariyer) {
                                                if ($sayac->uretimyer_id == $cariyer->uretimyer_id) {
                                                    $uretimyerid = $sayac->uretimyer_id;
                                                    break;
                                                }
                                            }
                                    }
                                }
                            }
                        }
                        foreach ($faturakalem as $kalem) {
                            $kalem->netsisstokkod = NetsisStokKod::where('kodu', $kalem->STOK_KODU)->first();
                            if ($kalem->netsisstokkod)
                                $kalem->subeurun = SubeUrun::where('netsisstokkod_id', $kalem->netsisstokkod->id)->where('subekodu', $sube->subekodu)->first();
                        }
                    }
                }else {
                    $faturakalem = Sthar::where('SUBE_KODU', $subekodu)->where('STHAR_FTIRSIP', '1')->where('FISNO', $faturano)->get(array('STOK_KODU', 'FISNO', 'STHAR_GCMIK', 'STHAR_GCKOD', 'STHAR_TARIH', 'STHAR_NF', 'STHAR_BF', 'STHAR_KDV', 'DEPO_KODU', 'STHAR_CARIKOD', 'PLASIYER_KODU', 'SIRA', 'SUBE_KODU', 'PROJE_KODU'));
                    $fatura = Fatuirs::where('SUBE_KODU', $subekodu)->where('FTIRSIP', '1')->where('FATIRS_NO', $faturano)->first(array('FATIRS_NO', 'CARI_KODU', 'TARIH', 'BRUTTUTAR', 'KDV', 'GENELTOPLAM', 'FATKALEM_ADEDI', 'PROJE_KODU', 'PLA_KODU', DB::raw('dbo.TRK(KS_KODU) as KS_KODU'), 'KAYITYAPANKUL', 'KAYITTARIHI', DB::raw('dbo.TRK(ACIKLAMA) as ACIKLAMA')));
                    $faturaek = Fatuek::where('SUBE_KODU', $subekodu)->where('FATIRSNO', $faturano)->where('FKOD', '1')->first(array('SUBE_KODU', 'FKOD', 'FATIRSNO', 'CKOD', DB::raw('dbo.TRK(ACIK1) as ACIK1'), DB::raw('dbo.TRK(ACIK2) as ACIK2'), DB::raw('dbo.TRK(ACIK3) as ACIK3'), DB::raw('dbo.TRK(ACIK4) as ACIK4'),
                        DB::raw('dbo.TRK(ACIK5) as ACIK5'), DB::raw('dbo.TRK(ACIK6) as ACIK6'), DB::raw('dbo.TRK(ACIK7) as ACIK7'), DB::raw('dbo.TRK(ACIK8) as ACIK8'), DB::raw('dbo.TRK(ACIK9) as ACIK9'), DB::raw('dbo.TRK(ACIK10) as ACIK10')));
                    $seritra = Seritra::where('SUBE_KODU', $subekodu)->where('BELGENO', $faturano)->get(array('SERI_NO', 'STOK_KODU', 'GCKOD', 'BELGENO', 'HARACIK', 'SUBE_KODU', 'DEPOKOD'));
                    $fatura->cari = NetsisCari::where('carikod', BackendController::Trk($fatura->CARI_KODU))->first();
                    $fatura->plasiyer = Plasiyer::where('kodu', $fatura->PLA_KODU)->first();
                    $fatura->kasakod = KasaKod::where('kasakod', $fatura->KS_KODU)->first();
                    foreach ($seritra as $seri) {
                        $sayaclar = Sayac::where('serino', $seri->SERI_NO)->get();
                        $serino = $serino == null ? $seri->SERI_NO : $serino;
                        $serinolar .= ($serinolar == "" ? "" : ",") . $seri->SERI_NO;
                        if ($sayaclar->count() > 0) {
                            {
                                foreach ($sayaclar as $sayac) {
                                    if ($cariyerler->count() > 0)
                                        foreach ($cariyerler as $cariyer) {
                                            if ($sayac->uretimyer_id == $cariyer->uretimyer_id) {
                                                $uretimyerid = $sayac->uretimyer_id;
                                                break;
                                            }
                                        }
                                }
                            }
                        }
                    }
                    foreach ($faturakalem as $kalem) {
                        $kalem->netsisstokkod = NetsisStokKod::where('kodu', $kalem->STOK_KODU)->first();
                        if ($kalem->netsisstokkod)
                            $kalem->subeurun = SubeUrun::where('netsisstokkod_id', $kalem->netsisstokkod->id)->where('subekodu', $sube->subekodu)->first();
                    }
                }
                $abonebilgi = AboneBilgi::where('subekodu', $subekodu)->where('serino', $serino)->first();
                if ($fatura->count() > 0) {
                    return array("durum" => true, "fatura" => $fatura, "faturaek" => $faturaek, "faturakalem" => $faturakalem, "seritra" => $seritra, "parabirimi" => $parabirimi, "uretimyer" => $uretimyerid, "abonebilgi" => $abonebilgi, "serinolar" => $serinolar);
                } else {
                    return array("durum" => false, "type" => "error", "title" => "Fatura Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Fatura Bilgisi Bulunamadı.");
                }
            } else {
                return array("durum" => false, "type" => "error", "title" => "Fatura Bilgisi Getirilemedi", "text" => "Fatura Numarası Boş Geçilmiş");
            }
        } catch (Exception $e) {
            Log::error($e);
            return array("durum" => false, "type" => "error", "title" => "Fatura Bilgisi Getirilemedi", "text" => "Fatura Bilgisi Getirilirken Hata Oluştu!");
        }
    }

    public function postFaturaekle()
    {
        $connection = null;
        try {
            DB::beginTransaction();
            $faturano = Input::get('faturano');
            $dbname = Input::get('dbname');
            $subekodu = Input::get('faturasubekodu');
            $uretimyer = Input::get('uretimyer');
            $tckimlikno = trim(Input::get('acik1'));
            $adisoyadi = BackendController::StrtoUpper(trim(Input::get('acik2')));
            $nadisoyadi = BackendController::StrNormalized($adisoyadi);
            $telefon = trim(Input::get('acik3'));
            $adres = BackendController::StrtoUpper(trim(Input::get('acik4')));
            $vergino = trim(Input::get('acik5'));
            $vergidairesi = trim(Input::get('acik6'));
            $aciklama = trim(Input::get('acik8'));
            $odemesekli = trim(Input::get('acik9'));
            $mahalle = trim(Input::get('acik10'));
            $serinolar = Input::get('seri');
            $sube = Sube::where('subekodu', $subekodu)->where('aktif', 1)->first();
            $parabirimi = ParaBirimi::find(1);
            $kalanserinolar = $abonesayaclist = $tahsisliabone = $tahsislilist = $secilenserinolar = array();
            $abonesayaci = $secilenler = $miktarlar = $birimfiyatlar = $ucretsizler = $serinolist = "";
            $tahsisliabone = null;
            $baglisayac=0;
            $adet = 0;
            $subesayacsatis = SubeSayacSatis::where('faturano', $faturano)->where('db_name', $dbname)->first();
            if ($subesayacsatis) { return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fatura Uyarısı', 'text' => 'Bu Fatura Satışlar İçinde Zaten Kayıtlı!', 'type' => 'warning'));}
            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
            if (!$subeyetkili) { return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube Yetkili Uyarısı', 'text' => 'Bu Yere Ait Şube Yetkilisi Yok.', 'type' => 'warning'));}
            $yetkili = ServisYetkili::where('kullanici_id', $subeyetkili->kullanici_id)->first();
            if (!$yetkili) { return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetki Uyarısı', 'text' => 'Bu Kullanıcının Yetkisi Yok.', 'type' => 'warning'));}
            for ($i = 0; $i < count($serinolar); $i++) {
                $serino1 = $serinolar[$i];
                $serinolist .= ($serinolist == "" ? "" : ",") . $serino1;
                if ($serino1 == "")
                    continue;
                $adet++;
                array_push($kalanserinolar,$serino1);
                if (count($serinolar) > 1) {
                    for ($j = $i + 1; $j < count($serinolar); $j++) {
                        $serino2 = $serinolar[$j];
                        if ($serino2 == "")
                            continue;
                        if ($serino1 == $serino2) {
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Aynı Seri Numarası Girilmiş', 'type' => 'error'));
                        }
                    }
                }
            }
            $serinolar=$kalanserinolar;
            if ($faturano != "") {
                if ($dbname != 'MANAS' . date('Y')) { //eski kayıtlar kaydedilecekse
                    $connection = BackendController::AddNewConnection($dbname);
                    $yil = filter_var($dbname, FILTER_SANITIZE_NUMBER_INT);
                    if ($yil < 2017) {
                        $faturakalem = Sthar::on($connection)->where('SUBE_KODU', 0)->where('STHAR_FTIRSIP', '1')->where('FISNO', $faturano)->get(array('STOK_KODU', 'FISNO', 'STHAR_GCMIK', 'STHAR_GCKOD', 'STHAR_TARIH', 'STHAR_NF', 'STHAR_BF', 'STHAR_KDV', 'DEPO_KODU', 'STHAR_CARIKOD', 'PLASIYER_KODU', 'SIRA', 'SUBE_KODU', 'PROJE_KODU'));
                        $fatura = Fatuirs::on($connection)->where('SUBE_KODU', 0)->where('FTIRSIP', '1')->where('FATIRS_NO', $faturano)->first(array('FATIRS_NO', 'CARI_KODU', 'TARIH', 'BRUTTUTAR', 'KDV', 'GENELTOPLAM', 'FATKALEM_ADEDI', 'PROJE_KODU', 'PLA_KODU', DB::raw('dbo.TRK(KS_KODU) as KS_KODU'), 'KAYITYAPANKUL', 'KAYITTARIHI', DB::raw('dbo.TRK(ACIKLAMA) as ACIKLAMA')));
                    } else {
                        $faturakalem = Sthar::on($connection)->where('SUBE_KODU', $subekodu)->where('STHAR_FTIRSIP', '1')->where('FISNO', $faturano)->get(array('STOK_KODU', 'FISNO', 'STHAR_GCMIK', 'STHAR_GCKOD', 'STHAR_TARIH', 'STHAR_NF', 'STHAR_BF', 'STHAR_KDV', 'DEPO_KODU', 'STHAR_CARIKOD', 'PLASIYER_KODU', 'SIRA', 'SUBE_KODU', 'PROJE_KODU'));
                        $fatura = Fatuirs::on($connection)->where('SUBE_KODU', $subekodu)->where('FTIRSIP', '1')->where('FATIRS_NO', $faturano)->first(array('FATIRS_NO', 'CARI_KODU', 'TARIH', 'BRUTTUTAR', 'KDV', 'GENELTOPLAM', 'FATKALEM_ADEDI', 'PROJE_KODU', 'PLA_KODU', DB::raw('dbo.TRK(KS_KODU) as KS_KODU'), 'KAYITYAPANKUL', 'KAYITTARIHI', DB::raw('dbo.TRK(ACIKLAMA) as ACIKLAMA')));
                    }
                }else {
                    $faturakalem = Sthar::where('SUBE_KODU', $subekodu)->where('STHAR_FTIRSIP', '1')->where('FISNO', $faturano)->get(array('STOK_KODU', 'FISNO', 'STHAR_GCMIK', 'STHAR_GCKOD', 'STHAR_TARIH', 'STHAR_NF', 'STHAR_BF', 'STHAR_KDV', 'DEPO_KODU', 'STHAR_CARIKOD', 'PLASIYER_KODU', 'SIRA', 'SUBE_KODU', 'PROJE_KODU'));
                    $fatura = Fatuirs::where('SUBE_KODU', $subekodu)->where('FTIRSIP', '1')->where('FATIRS_NO', $faturano)->first(array('FATIRS_NO', 'CARI_KODU', 'TARIH', 'BRUTTUTAR', 'KDV', 'GENELTOPLAM', 'FATKALEM_ADEDI', 'PROJE_KODU', 'PLA_KODU', DB::raw('dbo.TRK(KS_KODU) as KS_KODU'), 'KAYITYAPANKUL', 'KAYITTARIHI', DB::raw('dbo.TRK(ACIKLAMA) as ACIKLAMA')));
                }
                $fatura->cari = NetsisCari::where('carikod', BackendController::Trk($fatura->CARI_KODU))->first();
                $fatura->plasiyer = Plasiyer::where('kodu', $fatura->PLA_KODU)->first();
                $fatura->kasakod = KasaKod::where('kasakod', $fatura->KS_KODU)->first();
                foreach ($faturakalem as $kalem) {
                    $kalem->netsisstokkod = NetsisStokKod::where('kodu', $kalem->STOK_KODU)->first();
                    if ($kalem->netsisstokkod){
                        $kalem->subeurun=SubeUrun::where('netsisstokkod_id',$kalem->netsisstokkod->id)->where('subekodu',$sube->subekodu)->first();
                        if(!$kalem->subeurun){
                            if($connection) BackendController::DeleteConnection($connection);
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fatura Bilgisi Kaydedilemedi', 'text' => 'Faturaya Ait Ürünler Sistemde Tanımlı Değil.', 'type' => 'error'));
                        }else{
                            $secilenler .= ($secilenler == "" ? "" : ",") . $kalem->subeurun->id;
                            $miktarlar .= ($miktarlar == "" ? "" : ",") . intval($kalem->STHAR_GCMIK);
                            $birimfiyatlar .= ($birimfiyatlar == "" ? "" : ",") . number_format(floatval($kalem->STHAR_BF),2,'.','');
                            $ucretsizler .= ($ucretsizler == "" ? "" : ",") . (number_format(floatval($kalem->STHAR_BF),2,'.','')==0 ? 1 : 0 );
                            SubeUrun::where('id', $kalem->subeurun->id)->update(['urundurum' => 1]);
                            if($kalem->subeurun->sayacadi_id!=null) {
                                if (($baglisayac+$kalem->STHAR_GCMIK) <= count($serinolar)) {
                                    $ek="";
                                    for($i=0;$i<$kalem->STHAR_GCMIK;$i++){
                                        $flag=0;
                                        for ($k=0;$k<count($kalanserinolar);$k++){
                                            $sayaclar = Sayac::where('serino', $kalanserinolar[$k])->where('uretimyer_id', $uretimyer)->get();
                                            if ($sayaclar->count() > 1) { //birden fazla sayaç varsa
                                                foreach ($sayaclar as $sayac) {
                                                    if ($sayac->sayacadi_id != "") {
                                                        if ($sayac->sayacadi_id == $kalem->subeurun->sayacadi_id && $sayac->sayaccap_id == $kalem->subeurun->sayaccap_id) {
                                                            $flag = 1;
                                                            $abonesayac = AboneSayac::where('serino', $kalanserinolar[$k])->first();
                                                            if ($abonesayac) {
                                                                $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                                                                if ($abonetahsis) {
                                                                    $abone = Abone::find($abonetahsis->abone_id);
                                                                    if(is_null($tahsisliabone)){
                                                                        if($tckimlikno!="" && $abone->tckimlikno==$tckimlikno){
                                                                            array_push($tahsislilist,$abonesayac->id);
                                                                        }else if($vergino!="" && $abone->tckimlikno==$vergino){
                                                                            array_push($tahsislilist,$abonesayac->id);
                                                                        }else if($nadisoyadi!="" && trim($abone->nadisoyadi)==$nadisoyadi){
                                                                            array_push($tahsislilist,$abonesayac->id);
                                                                        }else{
                                                                            if($connection) BackendController::DeleteConnection($connection);
                                                                            DB::rollBack();
                                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: ' . $abone->abone_no, 'type' => 'error'));
                                                                        }
                                                                        $tahsisliabone = $abone;
                                                                    }else if($tahsisliabone->id===$abone->id){
                                                                        array_push($tahsislilist,$abonesayac->id);
                                                                    }else{
                                                                        if($connection) BackendController::DeleteConnection($connection);
                                                                        DB::rollBack();
                                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: ' . $abone->abone_no, 'type' => 'error'));
                                                                    }
                                                                } else {
                                                                    //$abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$kalanserinolar[$k])->first();
                                                                    $abonesayac->adres=/*($abonebilgi && $abonebilgi->faturaadresi!=null) ? BackendController::StrtoUpper($abonebilgi->faturaadresi) :*/ $adres;
                                                                    $abonesayac->bilgi=$telefon;
                                                                    $abonesayac->save();
                                                                }
                                                            } else {
                                                                try{
                                                                    $abonesayac = new AboneSayac;
                                                                    $abonesayac->serino = $kalanserinolar[$k];
                                                                    $abonesayac->uretimyer_id = $uretimyer;
                                                                    $sayacadi = SayacAdi::find($kalem->subeurun->sayacadi_id);
                                                                    $abonesayac->sayactur_id = $sayacadi->sayactur_id;
                                                                    $abonesayac->sayacadi_id = $kalem->subeurun->sayacadi_id;
                                                                    $abonesayac->sayaccap_id = $kalem->subeurun->sayaccap_id;
                                                                    //$abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$kalanserinolar[$k])->first();
                                                                    $abonesayac->adres=/*($abonebilgi && $abonebilgi->faturaadresi!=null) ? BackendController::StrtoUpper($abonebilgi->faturaadresi) :*/ $adres;
                                                                    $abonesayac->bilgi=$telefon;
                                                                    $abonesayac->save();
                                                                }catch (Exception $e){
                                                                    Log::error($e);
                                                                    if($connection) BackendController::DeleteConnection($connection);
                                                                    DB::rollBack();
                                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Sayacı Kaydedilemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                                                                }
                                                            }
                                                            $ek .= ($ek == "" ? "" : ",") . $abonesayac->id;
                                                            array_push($abonesayaclist,$abonesayac->id);
                                                            array_push($secilenserinolar,$kalanserinolar[$k]);
                                                            array_splice($kalanserinolar, $k, 1);
                                                            break;
                                                        }
                                                    }
                                                }
                                            } elseif ($sayaclar->count() > 0) { //tek sayaç varsa
                                                $sayac = $sayaclar[0];
                                                if ($sayac->sayacadi_id != ""){
                                                    if ($sayac->sayacadi_id == $kalem->subeurun->sayacadi_id && $sayac->sayaccap_id == $kalem->subeurun->sayaccap_id) {
                                                        $flag = 1;
                                                        $abonesayac = AboneSayac::where('serino', $kalanserinolar[$k])->first();
                                                        if ($abonesayac) {
                                                            $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                                                            if ($abonetahsis) {
                                                                $abone = Abone::find($abonetahsis->abone_id);
                                                                if(is_null($tahsisliabone)){
                                                                    if($tckimlikno!="" && $abone->tckimlikno==$tckimlikno){
                                                                        array_push($tahsislilist,$abonesayac->id);
                                                                    }else if($vergino!="" && $abone->tckimlikno==$vergino){
                                                                        array_push($tahsislilist,$abonesayac->id);
                                                                    }else if($nadisoyadi!="" && trim($abone->nadisoyadi)==$nadisoyadi){
                                                                        array_push($tahsislilist,$abonesayac->id);
                                                                    }else{
                                                                        if($connection) BackendController::DeleteConnection($connection);
                                                                        DB::rollBack();
                                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: ' . $abone->abone_no, 'type' => 'error'));
                                                                    }
                                                                    $tahsisliabone = $abone;
                                                                }else if($tahsisliabone->id===$abone->id){
                                                                    array_push($tahsislilist,$abonesayac->id);
                                                                }else{
                                                                    if($connection) BackendController::DeleteConnection($connection);
                                                                    DB::rollBack();
                                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: ' . $abone->abone_no, 'type' => 'error'));
                                                                }
                                                            } else {
                                                                //$abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$kalanserinolar[$k])->first();
                                                                $abonesayac->adres=/*($abonebilgi && $abonebilgi->faturaadresi!=null) ? BackendController::StrtoUpper($abonebilgi->faturaadresi) :*/ $adres;
                                                                $abonesayac->bilgi=$telefon;
                                                                $abonesayac->save();
                                                            }
                                                        } else {
                                                            try{
                                                                $abonesayac = new AboneSayac;
                                                                $abonesayac->serino = $kalanserinolar[$k];
                                                                $abonesayac->uretimyer_id = $uretimyer;
                                                                $sayacadi = SayacAdi::find($kalem->subeurun->sayacadi_id);
                                                                $abonesayac->sayactur_id = $sayacadi->sayactur_id;
                                                                $abonesayac->sayacadi_id = $kalem->subeurun->sayacadi_id;
                                                                $abonesayac->sayaccap_id = $kalem->subeurun->sayaccap_id;
                                                                //$abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$kalanserinolar[$k])->first();
                                                                $abonesayac->adres=/*($abonebilgi && $abonebilgi->faturaadresi!=null) ? BackendController::StrtoUpper($abonebilgi->faturaadresi) :*/ $adres;
                                                                $abonesayac->bilgi=$telefon;
                                                                $abonesayac->save();
                                                            }catch (Exception $e){
                                                                Log::error($e);
                                                                if($connection) BackendController::DeleteConnection($connection);
                                                                DB::rollBack();
                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Sayacı Kaydedilemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                                                            }
                                                        }
                                                        $ek .= ($ek == "" ? "" : ",") . $abonesayac->id;
                                                        array_push($abonesayaclist,$abonesayac->id);
                                                        array_push($secilenserinolar,$kalanserinolar[$k]);
                                                        array_splice($kalanserinolar, $k, 1);
                                                    }
                                                }
                                            }
                                            if($flag){
                                                break;
                                            }
                                        }
                                        if(!$flag){ //sayaç bulunamadı ise yeni eklenecek
                                            if(count($kalanserinolar)>0){
                                                $sayaclar = Sayac::where('serino', $kalanserinolar[0])->get();
                                                $kayitliyer = "";
                                                $sayacdurum=0;
                                                foreach ($sayaclar as $sayac) {
                                                    if($sayac->uretimyer_id==$uretimyer){
                                                        if($sayac->sayacadi_id==""){
                                                            $sayacdurum=1;
                                                            $sayacadi = SayacAdi::find($kalem->subeurun->sayacadi_id);
                                                            $sayac->sayactur_id = $sayacadi->sayactur_id;
                                                            $sayac->sayacadi_id = $kalem->subeurun->sayacadi_id;
                                                            $sayac->sayaccap_id = $kalem->subeurun->sayaccap_id;
                                                            $sayac->uretimtarihi = date('Y-m-d H:i:s');
                                                            $sayac->uretimyer_id = $uretimyer;
                                                            $sayac->kullanici_id = Auth::user()->id;
                                                            $sayac->save();
                                                            $abonesayac = AboneSayac::where('serino', $kalanserinolar[0])->first();
                                                            if ($abonesayac) {
                                                                $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                                                                if ($abonetahsis) {
                                                                    $abone = Abone::find($abonetahsis->abone_id);
                                                                    if(is_null($tahsisliabone)){
                                                                        if($tckimlikno!="" && $abone->tckimlikno==$tckimlikno){
                                                                            array_push($tahsislilist,$abonesayac->id);
                                                                        }else if($vergino!="" && $abone->tckimlikno==$vergino){
                                                                            array_push($tahsislilist,$abonesayac->id);
                                                                        }else if($nadisoyadi!="" && trim($abone->nadisoyadi)==$nadisoyadi){
                                                                            array_push($tahsislilist,$abonesayac->id);
                                                                        }else{
                                                                            if($connection) BackendController::DeleteConnection($connection);
                                                                            DB::rollBack();
                                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: ' . $abone->abone_no, 'type' => 'error'));
                                                                        }
                                                                        $tahsisliabone = $abone;
                                                                    }else if($tahsisliabone->id===$abone->id){
                                                                        array_push($tahsislilist,$abonesayac->id);
                                                                    }else{
                                                                        if($connection) BackendController::DeleteConnection($connection);
                                                                        DB::rollBack();
                                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: ' . $abone->abone_no, 'type' => 'error'));
                                                                    }
                                                                } else {
                                                                    //$abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$kalanserinolar[0])->first();
                                                                    $abonesayac->adres=/*($abonebilgi && $abonebilgi->faturaadresi!=null) ? BackendController::StrtoUpper($abonebilgi->faturaadresi) : */$adres;
                                                                    $abonesayac->bilgi=$telefon;
                                                                    $abonesayac->save();
                                                                }
                                                            } else {
                                                                try{
                                                                    $abonesayac = new AboneSayac;
                                                                    $abonesayac->serino = $kalanserinolar[0];
                                                                    $abonesayac->uretimyer_id = $uretimyer;
                                                                    $sayacadi = SayacAdi::find($kalem->subeurun->sayacadi_id);
                                                                    $abonesayac->sayactur_id = $sayacadi->sayactur_id;
                                                                    $abonesayac->sayacadi_id = $kalem->subeurun->sayacadi_id;
                                                                    $abonesayac->sayaccap_id = $kalem->subeurun->sayaccap_id;
                                                                    //$abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$kalanserinolar[0])->first();
                                                                    $abonesayac->adres=/*($abonebilgi && $abonebilgi->faturaadresi!=null) ? BackendController::StrtoUpper($abonebilgi->faturaadresi) : */$adres;
                                                                    $abonesayac->bilgi=$telefon;
                                                                    $abonesayac->save();
                                                                }catch (Exception $e){
                                                                    Log::error($e);
                                                                    if($connection) BackendController::DeleteConnection($connection);
                                                                    DB::rollBack();
                                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Sayacı Kaydedilemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                                                                }
                                                            }
                                                            $ek .= ($ek == "" ? "" : ",") . $abonesayac->id;
                                                            array_push($abonesayaclist,$abonesayac->id);
                                                            array_push($secilenserinolar,$kalanserinolar[0]);
                                                            array_splice($kalanserinolar, 0, 1);
                                                            break;
                                                        }
                                                    }else{
                                                        $kayitliyer = Uretimyer::find($sayac->uretimyer_id);
                                                    }
                                                }
                                                if(!$sayacdurum){
                                                    if ($kayitliyer == "") { //sayaç yoksa sayaç kaydedilecek
                                                        try {
                                                            $sayac = new Sayac;
                                                            $sayac->serino = $kalanserinolar[0];
                                                            $sayac->cihazno = $kalanserinolar[0];
                                                            $sayacadi = SayacAdi::find($kalem->subeurun->sayacadi_id);
                                                            $sayac->sayactur_id = $sayacadi->sayactur_id;
                                                            $sayac->sayacadi_id = $kalem->subeurun->sayacadi_id;
                                                            $sayac->sayaccap_id = $kalem->subeurun->sayaccap_id;
                                                            $sayac->uretimtarihi = date('Y-m-d H:i:s');
                                                            $sayac->uretimyer_id = $uretimyer;
                                                            $sayac->kullanici_id = Auth::user()->id;
                                                            $sayac->save();
                                                            $abonesayac = AboneSayac::where('serino', $kalanserinolar[0])->first();
                                                            if ($abonesayac) {
                                                                $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                                                                if ($abonetahsis) {
                                                                    $abone = Abone::find($abonetahsis->abone_id);
                                                                    if(is_null($tahsisliabone)){
                                                                        if($tckimlikno!="" && $abone->tckimlikno==$tckimlikno){
                                                                            array_push($tahsislilist,$abonesayac->id);
                                                                        }else if($vergino!="" && $abone->tckimlikno==$vergino){
                                                                            array_push($tahsislilist,$abonesayac->id);
                                                                        }else if($nadisoyadi!="" && trim($abone->nadisoyadi)==$nadisoyadi){
                                                                            array_push($tahsislilist,$abonesayac->id);
                                                                        }else{
                                                                            if($connection) BackendController::DeleteConnection($connection);
                                                                            DB::rollBack();
                                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: ' . $abone->abone_no, 'type' => 'error'));
                                                                        }
                                                                        $tahsisliabone = $abone;
                                                                    }else if($tahsisliabone->id===$abone->id){
                                                                        array_push($tahsislilist,$abonesayac->id);
                                                                    }else{
                                                                        if($connection) BackendController::DeleteConnection($connection);
                                                                        DB::rollBack();
                                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: ' . $abone->abone_no, 'type' => 'error'));
                                                                    }
                                                                } else {
                                                                    //$abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$kalanserinolar[0])->first();
                                                                    $abonesayac->adres=/*($abonebilgi && $abonebilgi->faturaadresi!=null) ? BackendController::StrtoUpper($abonebilgi->faturaadresi) : */$adres;
                                                                    $abonesayac->bilgi=$telefon;
                                                                    $abonesayac->save();
                                                                }
                                                            } else {
                                                                try{
                                                                    $abonesayac = new AboneSayac;
                                                                    $abonesayac->serino = $kalanserinolar[0];
                                                                    $abonesayac->uretimyer_id = $uretimyer;
                                                                    $sayacadi = SayacAdi::find($kalem->subeurun->sayacadi_id);
                                                                    $abonesayac->sayactur_id = $sayacadi->sayactur_id;
                                                                    $abonesayac->sayacadi_id = $kalem->subeurun->sayacadi_id;
                                                                    $abonesayac->sayaccap_id = $kalem->subeurun->sayaccap_id;
                                                                    //$abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$kalanserinolar[0])->first();
                                                                    $abonesayac->adres=/*($abonebilgi && $abonebilgi->faturaadresi!=null) ? BackendController::StrtoUpper($abonebilgi->faturaadresi) : */$adres;
                                                                    $abonesayac->bilgi=$telefon;
                                                                    $abonesayac->save();
                                                                }catch (Exception $e){
                                                                    Log::error($e);
                                                                    if($connection) BackendController::DeleteConnection($connection);
                                                                    DB::rollBack();
                                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Sayacı Kaydedilemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                                                                }
                                                            }
                                                            $ek .= ($ek == "" ? "" : ",") . $abonesayac->id;
                                                            array_push($abonesayaclist,$abonesayac->id);
                                                            array_push($secilenserinolar,$kalanserinolar[0]);
                                                            array_splice($kalanserinolar, 0, 1);
                                                        } catch (Exception $e) {
                                                            Log::error($e);
                                                            if($connection) BackendController::DeleteConnection($connection);
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hatası', 'text' => 'Girilen Sayaç Sisteme Kaydedilemedi', 'type' => 'error'));
                                                        }
                                                    } else {
                                                        if($connection) BackendController::DeleteConnection($connection);
                                                        DB::rollBack();
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hatası', 'text' => 'Girilen Sayacın Sistemdeki Yeri Farklı: ' . $kalanserinolar[0] . '->' . $kayitliyer->yeradi, 'type' => 'error'));
                                                    }
                                                }
                                            }else{ //serino kalmadıysa
                                                if($connection) BackendController::DeleteConnection($connection);
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hatası', 'text' => 'Sayaç Sayısı ile Seri Numara Sayısı Uyuşmuyor', 'type' => 'error'));
                                            }
                                        }
                                    }
                                    $abonesayaci .= ($abonesayaci == "" ? "" : ";") . $ek;
                                    $baglisayac+=$kalem->STHAR_GCMIK;
                                }else{
                                    if($connection) BackendController::DeleteConnection($connection);
                                    DB::rollBack();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fatura Bilgisi Kaydedilemedi', 'text' => 'Sayaç Sayısı ile Seri Numara Sayısı Uyuşmuyor', 'type' => 'error'));
                                }
                            }else{
                                $abonesayaci .= ($abonesayaci == "" ? "" : ";") . '0';
                            }
                        }
                    }
                }
                if($connection) BackendController::DeleteConnection($connection);
                if ($adet == 0 && $baglisayac>0) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Bilgisi Kaydedilemedi', 'text' => 'Girilen Sayaçların Seri Numaraları yazılmamış', 'type' => 'error'));
                }else if($adet>0 && $baglisayac<$adet){
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Bilgisi Kaydedilemedi', 'text' => 'Girilen Ürünler İçin Bağlı Sayaç Tipi Yok', 'type' => 'error'));
                }
                if ($fatura->count() > 0) {
                    try{
                        $subeabone = BackendController::AboneNoBul($sube,$adisoyadi,$tckimlikno=="" ? $vergino : $tckimlikno,$serinolar);
                        if($subeabone){
                            $aboneno=$subeabone->aboneno;
                            $basvurutarihi = date("Y-m-d H:i:s", strtotime($subeabone->basvurutarihi));
                        }else{
                            $aboneno='';
                            $basvurutarihi=date('Y-m-d H:i:s');
                        }
                        if(is_null($tahsisliabone)){
                            $abone = Abone::where('uretimyer_id',$uretimyer)->where('nadisoyadi',$nadisoyadi)->where('tckimlikno',$tckimlikno)->first();
                            if(!$abone){
                                $abone = new Abone;
                                $abone->subekodu = $subekodu;
                                $abone->adisoyadi = $adisoyadi;
                                $abone->vergidairesi = $vergidairesi;
                                $abone->tckimlikno = $tckimlikno=="" ? $vergino : $tckimlikno;
                                $abone->abone_no = $aboneno;
                                $abone->telefon = $telefon;
                                $abone->faturaadresi = $adres;
                                $abone->mahalle = $mahalle;
                                $abone->uretimyer_id = $uretimyer;
                                $abone->netsiscari_id = $fatura->cari->id;
                                $abone->kullanici_id = Auth::user()->id;
                                $abone->save();
                            }
                        }else{
                            $abone = $tahsisliabone;
                        }
                    }catch(Exception $e){
                        Log::error($e);
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Bilgisi Kaydedilemedi', 'text' => 'Abone Bilgisi Eksik Ya da Hatalı', 'type' => 'error'));
                    }
                    try {
                        for ($i = 0; $i < count($abonesayaclist); $i++) {
                            $flag = 0;
                            for($j = 0; $j < count($tahsislilist); $j++){
                                if($abonesayaclist[$i]==$tahsislilist[$j]){
                                    $flag=1;
                                }
                            }
                            if(!$flag){
                                $abonetahsis = new AboneTahsis;
                                $abonetahsis->abone_id = $abone->id;
                                $abonetahsis->abonesayac_id = $abonesayaclist[$i];
                                $abonetahsis->tahsistarihi = $basvurutarihi;
                                $abonetahsis->save();
                            }
                            AboneSayac::where('id', $abonesayaclist[$i])->update(['satisdurum' => 1]);
                        }
                    }catch(Exception $e) {
                        Log::error($e);
                        DB::rollBack();
                        Input::flash();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Kayıdı Yapılamadı', 'text' => 'Abonenin Tahsisi Kaydedilemedi', 'type' => 'error'));
                    }
                    try{
                        $netsiscari = $fatura->cari;
                        $kdv_orani=SistemSabitleri::where('adi','KdvOrani')->first();
                        try {
                            $sayacsatis = new SubeSayacSatis;
                            $sayacsatis->subekodu = $subekodu;
                            $sayacsatis->abone_id = $abone->id;
                            $sayacsatis->uretimyer_id = $abone->uretimyer_id;
                            $sayacsatis->netsiscari_id = $netsiscari->id;
                            $sayacsatis->secilenler = $secilenler;
                            $sayacsatis->adet = $miktarlar;
                            $sayacsatis->birimfiyat = $birimfiyatlar;
                            $sayacsatis->ucretsiz = $ucretsizler;
                            $sayacsatis->sayaclar = $abonesayaci;
                            $sayacsatis->tutar = number_format(floatval($fatura->BRUTTUTAR),2,'.','');
                            $sayacsatis->kdv = number_format(floatval($fatura->KDV),2,'.','');
                            $sayacsatis->toplamtutar = number_format(floatval($fatura->GENELTOPLAM),2,'.','');
                            $sayacsatis->parabirimi_id = $parabirimi->id;
                            $sayacsatis->kullanici_id = Auth::user()->id;
                            $sayacsatis->kayittarihi = date('Y-m-d H:i:s');
                            $sayacsatis->kdvorani=$kdv_orani->deger;
                            $sayacsatis->yazitutar=BackendController::YaziTutar(number_format(floatval($fatura->GENELTOPLAM),2,'.',''),$parabirimi->id);
                            $sayacsatis->efatura = BackendController::Efatura($dbname,$netsiscari->id);
                            $sayacsatis->kurtarihi = $fatura->TARIH;
                            $sayacsatis->faturatarihi = $fatura->TARIH;
                            $sayacsatis->durum = 1;
                            $sayacsatis->db_name = $dbname;
                            $sayacsatis->faturano = $faturano;
                            $sayacsatis->faturaadres = $adres;
                            $sayacsatis->carikod = $netsiscari->carikod;
                            $sayacsatis->ozelkod = $yetkili->ozelkod;
                            $sayacsatis->projekodu = $fatura->PROJE_KODU;
                            $sayacsatis->plasiyerkod = $fatura->PLA_KODU;
                            $sayacsatis->depokodu = $yetkili->depokodu;
                            $sayacsatis->aciklama = $aciklama;
                            $sayacsatis->odemetipi = $fatura->kasakod->odemetipi;
                            $sayacsatis->odemesekli = $odemesekli;
                            $sayacsatis->kasakodu = $fatura->KS_KODU;
                            $sayacsatis->kasakodu2 = '';
                            $sayacsatis->netsiskullanici = $fatura->KAYITYAPANKUL;
                            $sayacsatis->netsiskullanici_id = $yetkili->netsiskullanici_id;
                            $sayacsatis->gfiyat = $birimfiyatlar;
                            $sayacsatis->save();

                            $abone = Abone::find($sayacsatis->abone_id);
                            $sayaclar = explode(';', $sayacsatis->sayaclar);
                            foreach ($sayaclar as $sayac) {
                                $sayaclist = explode(',', $sayac);
                                foreach ($sayaclist as $sayacid) {
                                    if ($sayacid != 0) {
                                        $abonesayac = AboneSayac::find($sayacid);
                                        $abonetahsis = AboneTahsis::where('abone_id', $abone->id)->where('abonesayac_id', $abonesayac->id)->first();
                                        if ($abonetahsis) {
                                            $serviskayit = ServisKayit::where('abonetahsis_id',$abonetahsis->id)->where('acilmatarihi',$sayacsatis->faturatarihi)
                                                ->where('tipi',0)->first();
                                            if(!$serviskayit){
                                                $serviskayit = new ServisKayit;
                                                $serviskayit->subekodu=$sayacsatis->subekodu;
                                                $serviskayit->kayitadres = $abonesayac->adres;
                                                $serviskayit->abonetahsis_id = $abonetahsis->id;
                                                $serviskayit->netsiscari_id = $sayacsatis->netsiscari_id;
                                                $serviskayit->uretimyer_id = $sayacsatis->uretimyer_id;
                                                $serviskayit->kullanici_id = Auth::user()->id;
                                                $serviskayit->tipi = 0;
                                                $serviskayit->aciklama = $abonesayac->bilgi;
                                                $serviskayit->acilmatarihi = $sayacsatis->faturatarihi;
                                                $serviskayit->save();
                                                BackendController::HatirlatmaEkle(13, $abone->netsiscari_id, 6, 1);
                                                BackendController::BildirimEkle(13, $abone->netsiscari_id, 6, 1);
                                            }
                                        }
                                    }
                                }
                            }
                        } catch (Exception $e) {
                            Log::error($e);
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fatura Bilgisi Kaydedilemedi', 'text' => 'Fatura Bilgisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                        }
                    }catch(Exception $e){
                        Log::error($e);
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fatura Bilgisi Kaydedilemedi', 'text' => 'Fatura Bilgisi Kaydedilemedi.', 'type' => 'error'));
                    }
                    DB::commit();
                    return Redirect::to($this->servisadi . '/faturabilgi')->with(array('mesaj' => 'true', 'title' => 'Fatura Bilgisi Aktarıldı', 'text' => 'Fatura Bilgisi Sube Satışlarına Başarıyla Aktarıldı.', 'type' => 'success'));
                } else {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fatura Bilgisi Kaydedilemedi', 'text' => 'Fatura Bilgisi Bulunamadı.', 'type' => 'error'));
                }
            } else {
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fatura Bilgisi Kaydedilemedi', 'text' => 'Fatura Numarası Boş Geçilmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            if($connection) BackendController::DeleteConnection($connection);
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fatura Bilgisi Kaydedilemedi', 'text' => 'Fatura Bilgisi Aktarılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function postFaturaaktar(){
        $connection = null;
        try {
            $secilenler = Input::get('secilenler');
            $secilenlist=explode(",",$secilenler);
            $yil = Input::get('faturayil');
            $subekodu = Input::get('subekodu');
            $sube = Sube::where('subekodu', $subekodu)->where('aktif', 1)->first();
            $uretimyer=NULL;
            $uretimyerlist=NULL;
            $cariyer = CariYer::where('netsiscari_id', $sube->netsiscari_id)->where('durum', 1)->get(array('uretimyer_id'))->toArray();
            if (count($cariyer) > 1)
                $uretimyerlist = $cariyer;
            else if (count($cariyer) > 0)
                $uretimyer = $cariyer[0]['uretimyer_id'];
            else
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Üretim Yeri Hatası', 'text' => 'Şube için Üretim Yeri Belirlenemedi', 'type' => 'error'));

            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
            if (!$subeyetkili) { return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube Yetkili Uyarısı', 'text' => 'Bu Yere Ait Şube Yetkilisi Yok.', 'type' => 'warning'));}
            $yetkili = ServisYetkili::where('kullanici_id', $subeyetkili->kullanici_id)->first();
            if (!$yetkili) { return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetki Uyarısı', 'text' => 'Bu Kullanıcının Yetkisi Yok.', 'type' => 'warning'));}
            if($yil!="") {
                $parabirimi = ParaBirimi::find(1);
                $satisflag=0;
                if ($yil != date('Y')) { //eski kayıtlar çekilecekse
                    $dbname = 'MANAS' . $yil;
                    $connection = BackendController::AddNewConnection($dbname);
                    if ($yil < 2014) {
                        $yilsubekodu = 0;
                    } else if ($yil < 2017) {
                        $yilsubekodu = 0;
                    } else {
                        $yilsubekodu = $subekodu;
                    }
                }else {
                    $dbname = 'MANAS' . date('Y');
                    $yilsubekodu = $subekodu;
                }
                foreach ($secilenlist as $faturano) {
                    $baglisayac = 0; $adet = 0;
                    $serinolar = $kalanserinolar = $abonesayaclist = $tahsisliabone = $tahsislilist = $secilenserinolar = array();
                    $abonesayaci = $secilenler = $miktarlar = $birimfiyatlar = $ucretsizler = "";
                    DB::beginTransaction();
                    $subesayacsatis = SubeSayacSatis::where('faturano', $faturano)->where('db_name', $dbname)->first();
                    if ($subesayacsatis) {
                        $satisflag=1;
                        continue;
                    }
                    if ($yil != date('Y')) {
                        $faturakalem = Sthar::on($connection)->where('SUBE_KODU', $yilsubekodu)->where('STHAR_FTIRSIP', '1')->where('FISNO', $faturano)->get(array('STOK_KODU', 'FISNO', 'STHAR_GCMIK', 'STHAR_GCKOD', 'STHAR_TARIH', 'STHAR_NF', 'STHAR_BF', 'STHAR_KDV', 'DEPO_KODU', 'STHAR_CARIKOD', 'PLASIYER_KODU', 'SIRA', 'SUBE_KODU', 'PROJE_KODU'));
                        $fatura = Fatuirs::on($connection)->where('SUBE_KODU', $yilsubekodu)->where('FTIRSIP', '1')->where('FATIRS_NO', $faturano)->first(array('FATIRS_NO', 'CARI_KODU', 'TARIH', 'BRUTTUTAR', 'KDV', 'GENELTOPLAM', 'FATKALEM_ADEDI', 'PROJE_KODU', 'PLA_KODU', DB::raw('dbo.TRK(KS_KODU) as KS_KODU'), 'KAYITYAPANKUL', 'KAYITTARIHI', DB::raw('dbo.TRK(ACIKLAMA) as ACIKLAMA')));
                        $faturaek = Fatuek::on($connection)->where('SUBE_KODU', $yilsubekodu)->where('FATIRSNO', $faturano)->where('FKOD', '1')->first(array('SUBE_KODU', 'FKOD', 'FATIRSNO', 'CKOD', DB::raw('dbo.TRK(ACIK1) as ACIK1'), DB::raw('dbo.TRK(ACIK2) as ACIK2'), DB::raw('dbo.TRK(ACIK3) as ACIK3'), DB::raw('dbo.TRK(ACIK4) as ACIK4'),
                            DB::raw('dbo.TRK(ACIK5) as ACIK5'), DB::raw('dbo.TRK(ACIK6) as ACIK6'), DB::raw('dbo.TRK(ACIK7) as ACIK7'), DB::raw('dbo.TRK(ACIK8) as ACIK8'), DB::raw('dbo.TRK(ACIK9) as ACIK9'), DB::raw('dbo.TRK(ACIK10) as ACIK10')));
                        $seritra = Seritra::on($connection)->where('SUBE_KODU', $yilsubekodu)->where('BELGENO', $faturano)->get(array('SERI_NO', 'STOK_KODU', 'GCKOD', 'BELGENO', 'HARACIK', 'SUBE_KODU', 'DEPOKOD'));
                    }else{
                        $faturakalem = Sthar::where('SUBE_KODU', $yilsubekodu)->where('STHAR_FTIRSIP', '1')->where('FISNO', $faturano)->get(array('STOK_KODU', 'FISNO', 'STHAR_GCMIK', 'STHAR_GCKOD', 'STHAR_TARIH', 'STHAR_NF', 'STHAR_BF', 'STHAR_KDV', 'DEPO_KODU', 'STHAR_CARIKOD', 'PLASIYER_KODU', 'SIRA', 'SUBE_KODU', 'PROJE_KODU'));
                        $fatura = Fatuirs::where('SUBE_KODU', $yilsubekodu)->where('FTIRSIP', '1')->where('FATIRS_NO', $faturano)->first(array('FATIRS_NO', 'CARI_KODU', 'TARIH', 'BRUTTUTAR', 'KDV', 'GENELTOPLAM', 'FATKALEM_ADEDI', 'PROJE_KODU', 'PLA_KODU', DB::raw('dbo.TRK(KS_KODU) as KS_KODU'), 'KAYITYAPANKUL', 'KAYITTARIHI', DB::raw('dbo.TRK(ACIKLAMA) as ACIKLAMA')));
                        $faturaek = Fatuek::where('SUBE_KODU', $yilsubekodu)->where('FATIRSNO', $faturano)->where('FKOD', '1')->first(array('SUBE_KODU', 'FKOD', 'FATIRSNO', 'CKOD', DB::raw('dbo.TRK(ACIK1) as ACIK1'), DB::raw('dbo.TRK(ACIK2) as ACIK2'), DB::raw('dbo.TRK(ACIK3) as ACIK3'), DB::raw('dbo.TRK(ACIK4) as ACIK4'),
                            DB::raw('dbo.TRK(ACIK5) as ACIK5'), DB::raw('dbo.TRK(ACIK6) as ACIK6'), DB::raw('dbo.TRK(ACIK7) as ACIK7'), DB::raw('dbo.TRK(ACIK8) as ACIK8'), DB::raw('dbo.TRK(ACIK9) as ACIK9'), DB::raw('dbo.TRK(ACIK10) as ACIK10')));
                        $seritra = Seritra::where('SUBE_KODU', $yilsubekodu)->where('BELGENO', $faturano)->get(array('SERI_NO', 'STOK_KODU', 'GCKOD', 'BELGENO', 'HARACIK', 'SUBE_KODU', 'DEPOKOD'));
                    }
                    $fatura->cari = NetsisCari::where('carikod', BackendController::Trk($fatura->CARI_KODU))->first();
                    $fatura->plasiyer = Plasiyer::where('kodu', $fatura->PLA_KODU)->first();
                    $fatura->kasakod = KasaKod::where('kasakod', $fatura->KS_KODU)->first();
                    $fatura->kasakod->odeme = $fatura->kasakod->odemetipi == 1 ? 'NAKİT' : ($fatura->kasakod->odemetipi == 2 ? 'KREDİ KARTI' : 'SENET');
                    $tckimlikno = trim($faturaek->ACIK1);
                    $adisoyadi = BackendController::StrtoUpper(trim($faturaek->ACIK2));
                    $nadisoyadi = BackendController::StrNormalized(trim($faturaek->ACIK2));
                    $telefon = trim($faturaek->ACIK3);
                    $adres = BackendController::StrtoUpper(trim($faturaek->ACIK4));
                    $vergino = trim($faturaek->ACIK5);
                    $vergidairesi = trim($faturaek->ACIK6);
                    $aciklama = trim($faturaek->ACIK8);
                    $mahalle = trim($faturaek->ACIK10);
                    if($seritra->count()>0){
                        foreach ($seritra as $seri) {
                            array_push($serinolar, $seri->SERI_NO);
                            $sayaclar = Sayac::where('serino', $seri->SERI_NO)->get();
                            if($sayaclar->count()>0) {
                                foreach ($sayaclar as $sayac) {
                                    if (count($cariyer) > 1) {
                                        foreach ($cariyer as $cari_yer) {
                                            if ($sayac->uretimyer_id == $cari_yer['uretimyer_id']) {
                                                $uretimyer = $sayac->uretimyer_id;
                                                break;
                                            }
                                        }
                                    } else if (count($cariyer) > 0) {
                                        if ($sayac->uretimyer_id == $cariyer[0]['uretimyer_id']) {
                                            $uretimyer = $sayac->uretimyer_id;
                                            break;
                                        }
                                    }
                                }
                            }else{
                                if($connection) BackendController::DeleteConnection($connection);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Üretim Yeri Hatası', 'text' => 'Sayaç Seri Numarası Sistemde Kayıtlı Değil -> '.$seri->SERI_NO, 'type' => 'error'));
                            }
                        }
                    }else if ($yil < 2014) {
                        if(is_numeric(trim($aciklama))){
                            array_push($serinolar, trim($aciklama));
                            $sayaclar = Sayac::where('serino', trim($aciklama))->get();
                            if($sayaclar->count()>0) {
                                foreach ($sayaclar as $sayac) {
                                    if (count($cariyer) > 1) {
                                        foreach ($cariyer as $cari_yer) {
                                            if ($sayac->uretimyer_id == $cari_yer['uretimyer_id']) {
                                                $uretimyer = $sayac->uretimyer_id;
                                                break;
                                            }
                                        }
                                    } else if (count($cariyer) > 0) {
                                        if ($sayac->uretimyer_id == $cariyer[0]['uretimyer_id']) {
                                            $uretimyer = $sayac->uretimyer_id;
                                            break;
                                        }
                                    }
                                }
                            }else{
                                if($connection) BackendController::DeleteConnection($connection);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Üretim Yeri Hatası', 'text' => 'Sayaç Seri Numarası Sistemde Kayıtlı Değil -> '.$aciklama, 'type' => 'error'));
                            }
                        }
                    }
                    for ($i = 0; $i < count($serinolar); $i++) {
                        $serino1 = $serinolar[$i];
                        if ($serino1 == "")
                            continue;
                        $adet++;
                        array_push($kalanserinolar, $serino1);
                        if (count($serinolar) > 1) {
                            for ($j = $i + 1; $j < count($serinolar); $j++) {
                                $serino2 = $serinolar[$j];
                                if ($serino2 == "")
                                    continue;
                                if ($serino1 == $serino2) {
                                    if($connection) BackendController::DeleteConnection($connection);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Aynı Seri Numarası Girilmiş', 'type' => 'error'));
                                }
                            }
                        }
                    }
                    $serinolar = $kalanserinolar;
                    foreach ($faturakalem as $kalem) {
                        $kalem->netsisstokkod = NetsisStokKod::where('kodu', $kalem->STOK_KODU)->first();
                        if ($kalem->netsisstokkod) {
                            $kalem->subeurun = SubeUrun::where('netsisstokkod_id', $kalem->netsisstokkod->id)->where('subekodu', $sube->subekodu)->first();
                            if (!$kalem->subeurun) {
                                if($connection) BackendController::DeleteConnection($connection);
                                DB::rollBack();
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fatura Bilgisi Kaydedilemedi', 'text' => 'Faturaya Ait Ürünler Sistemde Tanımlı Değil.', 'type' => 'error'));
                            } else {
                                $secilenler .= ($secilenler == "" ? "" : ",") . $kalem->subeurun->id;
                                $miktarlar .= ($miktarlar == "" ? "" : ",") . intval($kalem->STHAR_GCMIK);
                                $birimfiyatlar .= ($birimfiyatlar == "" ? "" : ",") . number_format(floatval($kalem->STHAR_BF), 2, '.', '');
                                $ucretsizler .= ($ucretsizler == "" ? "" : ",") . (number_format(floatval($kalem->STHAR_BF), 2, '.', '') == 0 ? 1 : 0);
                                SubeUrun::where('id', $kalem->subeurun->id)->update(['urundurum' => 1]);
                                if ($kalem->subeurun->sayacadi_id != null) {
                                    if (($baglisayac + $kalem->STHAR_GCMIK) <= count($serinolar)) {
                                        $ek = "";
                                        for ($i = 0; $i < $kalem->STHAR_GCMIK; $i++) {
                                            $flag = 0;
                                            for ($k = 0; $k < count($kalanserinolar); $k++) {
                                                $sayaclar = Sayac::where('serino', $kalanserinolar[$k])->where('uretimyer_id', $uretimyer)->get();
                                                if ($sayaclar->count() > 1) { //birden fazla sayaç varsa
                                                    foreach ($sayaclar as $sayac) {
                                                        if ($sayac->sayacadi_id != "") {
                                                            if ($sayac->sayacadi_id == $kalem->subeurun->sayacadi_id && $sayac->sayaccap_id == $kalem->subeurun->sayaccap_id) {
                                                                $flag = 1;
                                                                $abonesayac = AboneSayac::where('serino', $kalanserinolar[$k])->first();
                                                                if ($abonesayac) {
                                                                    $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                                                                    if ($abonetahsis) {
                                                                        $abone = Abone::find($abonetahsis->abone_id);
                                                                        if(is_null($tahsisliabone)){
                                                                            if($tckimlikno!="" && $abone->tckimlikno==$tckimlikno){
                                                                                array_push($tahsislilist,$abonesayac->id);
                                                                            }else if($vergino!="" && $abone->tckimlikno==$vergino){
                                                                                array_push($tahsislilist,$abonesayac->id);
                                                                            }else if($nadisoyadi!="" && trim($abone->nadisoyadi)==$nadisoyadi){
                                                                                array_push($tahsislilist,$abonesayac->id);
                                                                            }else{
                                                                                if($connection) BackendController::DeleteConnection($connection);
                                                                                DB::rollBack();
                                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: ' . $abone->abone_no, 'type' => 'error'));
                                                                            }
                                                                            $tahsisliabone = $abone;
                                                                        }else if($tahsisliabone->id===$abone->id){
                                                                            array_push($tahsislilist,$abonesayac->id);
                                                                        }else{
                                                                            if($connection) BackendController::DeleteConnection($connection);
                                                                            DB::rollBack();
                                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: ' . $abone->abone_no, 'type' => 'error'));
                                                                        }
                                                                    } else {
                                                                        //$abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$kalanserinolar[$k])->first();
                                                                        $abonesayac->adres=/*($abonebilgi && $abonebilgi->faturaadresi!=null) ? BackendController::StrtoUpper($abonebilgi->faturaadresi) :*/ $adres;
                                                                        $abonesayac->bilgi=$telefon;
                                                                        $abonesayac->save();
                                                                    }
                                                                } else {
                                                                    try {
                                                                        $abonesayac = new AboneSayac;
                                                                        $abonesayac->serino = $kalanserinolar[$k];
                                                                        $abonesayac->uretimyer_id = $uretimyer;
                                                                        $sayacadi = SayacAdi::find($kalem->subeurun->sayacadi_id);
                                                                        $abonesayac->sayactur_id = $sayacadi->sayactur_id;
                                                                        $abonesayac->sayacadi_id = $kalem->subeurun->sayacadi_id;
                                                                        $abonesayac->sayaccap_id = $kalem->subeurun->sayaccap_id;
                                                                        //$abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$kalanserinolar[$k])->first();
                                                                        $abonesayac->adres=/*($abonebilgi && $abonebilgi->faturaadresi!=null) ? BackendController::StrtoUpper($abonebilgi->faturaadresi) :*/ $adres;
                                                                        $abonesayac->bilgi=$telefon;
                                                                        $abonesayac->save();
                                                                    } catch (Exception $e) {
                                                                        Log::error($e);
                                                                        if($connection) BackendController::DeleteConnection($connection);
                                                                        DB::rollBack();
                                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Sayacı Kaydedilemedi', 'text' => str_replace("'", "\'", $e->getMessage()), 'type' => 'error'));
                                                                    }
                                                                }
                                                                $ek .= ($ek == "" ? "" : ",") . $abonesayac->id;
                                                                array_push($abonesayaclist, $abonesayac->id);
                                                                array_push($secilenserinolar, $kalanserinolar[$k]);
                                                                array_splice($kalanserinolar, $k, 1);
                                                                break;
                                                            }
                                                        }
                                                    }
                                                } elseif ($sayaclar->count() > 0) { //tek sayaç varsa
                                                    $sayac = $sayaclar[0];
                                                    if ($sayac->sayacadi_id != "") {
                                                        if ($sayac->sayacadi_id == $kalem->subeurun->sayacadi_id && $sayac->sayaccap_id == $kalem->subeurun->sayaccap_id) {
                                                            $flag = 1;
                                                            $abonesayac = AboneSayac::where('serino', $kalanserinolar[$k])->first();
                                                            if ($abonesayac) {
                                                                $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                                                                if ($abonetahsis) {
                                                                    $abone = Abone::find($abonetahsis->abone_id);
                                                                    if(is_null($tahsisliabone)){
                                                                        if($tckimlikno!="" && $abone->tckimlikno==$tckimlikno){
                                                                            array_push($tahsislilist,$abonesayac->id);
                                                                        }else if($vergino!="" && $abone->tckimlikno==$vergino){
                                                                            array_push($tahsislilist,$abonesayac->id);
                                                                        }else if($nadisoyadi!="" && trim($abone->nadisoyadi)==$nadisoyadi){
                                                                            array_push($tahsislilist,$abonesayac->id);
                                                                        }else{
                                                                            if($connection) BackendController::DeleteConnection($connection);
                                                                            DB::rollBack();
                                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: ' . $abone->abone_no, 'type' => 'error'));
                                                                        }
                                                                        $tahsisliabone = $abone;
                                                                    }else if($tahsisliabone->id===$abone->id){
                                                                        array_push($tahsislilist,$abonesayac->id);
                                                                    }else{
                                                                        if($connection) BackendController::DeleteConnection($connection);
                                                                        DB::rollBack();
                                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: ' . $abone->abone_no, 'type' => 'error'));
                                                                    }
                                                                } else {
                                                                    //$abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$kalanserinolar[$k])->first();
                                                                    $abonesayac->adres=/*($abonebilgi && $abonebilgi->faturaadresi!=null) ? BackendController::StrtoUpper($abonebilgi->faturaadresi) :*/ $adres;
                                                                    $abonesayac->bilgi=$telefon;
                                                                    $abonesayac->save();
                                                                }
                                                            } else {
                                                                try {
                                                                    $abonesayac = new AboneSayac;
                                                                    $abonesayac->serino = $kalanserinolar[$k];
                                                                    $abonesayac->uretimyer_id = $uretimyer;
                                                                    $sayacadi = SayacAdi::find($kalem->subeurun->sayacadi_id);
                                                                    $abonesayac->sayactur_id = $sayacadi->sayactur_id;
                                                                    $abonesayac->sayacadi_id = $kalem->subeurun->sayacadi_id;
                                                                    $abonesayac->sayaccap_id = $kalem->subeurun->sayaccap_id;
                                                                    //$abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$kalanserinolar[$k])->first();
                                                                    $abonesayac->adres=/*($abonebilgi && $abonebilgi->faturaadresi!=null) ? BackendController::StrtoUpper($abonebilgi->faturaadresi) :*/ $adres;
                                                                    $abonesayac->bilgi=$telefon;
                                                                    $abonesayac->save();
                                                                } catch (Exception $e) {
                                                                    Log::error($e);
                                                                    if($connection) BackendController::DeleteConnection($connection);
                                                                    DB::rollBack();
                                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Sayacı Kaydedilemedi', 'text' => str_replace("'", "\'", $e->getMessage()), 'type' => 'error'));
                                                                }
                                                            }
                                                            $ek .= ($ek == "" ? "" : ",") . $abonesayac->id;
                                                            array_push($abonesayaclist, $abonesayac->id);
                                                            array_push($secilenserinolar, $kalanserinolar[$k]);
                                                            array_splice($kalanserinolar, $k, 1);
                                                        }
                                                    }
                                                }
                                                if ($flag) {
                                                    break;
                                                }
                                            }
                                            if (!$flag) { //sayaç bulunamadı ise yeni eklenecek
                                                if (count($kalanserinolar) > 0) {
                                                    $sayaclar = Sayac::where('serino', $kalanserinolar[0])->get();
                                                    $kayitliyer = "";
                                                    $sayacdurum = 0;
                                                    foreach ($sayaclar as $sayac) {
                                                        if ($sayac->uretimyer_id == $uretimyer) {
                                                            if ($sayac->sayacadi_id == "") {
                                                                $sayacdurum = 1;
                                                                $sayacadi = SayacAdi::find($kalem->subeurun->sayacadi_id);
                                                                $sayac->sayactur_id = $sayacadi->sayactur_id;
                                                                $sayac->sayacadi_id = $kalem->subeurun->sayacadi_id;
                                                                $sayac->sayaccap_id = $kalem->subeurun->sayaccap_id;
                                                                $sayac->uretimtarihi = date('Y-m-d H:i:s');
                                                                $sayac->uretimyer_id = $uretimyer;
                                                                $sayac->kullanici_id = Auth::user()->id;
                                                                $sayac->save();
                                                                $abonesayac = AboneSayac::where('serino', $kalanserinolar[0])->first();
                                                                if ($abonesayac) {
                                                                    $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                                                                    if ($abonetahsis) {
                                                                        $abone = Abone::find($abonetahsis->abone_id);
                                                                        if(is_null($tahsisliabone)){
                                                                            if($tckimlikno!="" && $abone->tckimlikno==$tckimlikno){
                                                                                array_push($tahsislilist,$abonesayac->id);
                                                                            }else if($vergino!="" && $abone->tckimlikno==$vergino){
                                                                                array_push($tahsislilist,$abonesayac->id);
                                                                            }else if($nadisoyadi!="" && trim($abone->nadisoyadi)==$nadisoyadi){
                                                                                array_push($tahsislilist,$abonesayac->id);
                                                                            }else{
                                                                                if($connection) BackendController::DeleteConnection($connection);
                                                                                DB::rollBack();
                                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: ' . $abone->abone_no, 'type' => 'error'));
                                                                            }
                                                                            $tahsisliabone = $abone;
                                                                        }else if($tahsisliabone->id===$abone->id){
                                                                            array_push($tahsislilist,$abonesayac->id);
                                                                        }else{
                                                                            if($connection) BackendController::DeleteConnection($connection);
                                                                            DB::rollBack();
                                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: ' . $abone->abone_no, 'type' => 'error'));
                                                                        }
                                                                    } else {
                                                                        //$abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$kalanserinolar[0])->first();
                                                                        $abonesayac->adres=/*($abonebilgi && $abonebilgi->faturaadresi!=null) ? BackendController::StrtoUpper($abonebilgi->faturaadresi) : */$adres;
                                                                        $abonesayac->bilgi=$telefon;
                                                                        $abonesayac->save();
                                                                    }
                                                                } else {
                                                                    try {
                                                                        $abonesayac = new AboneSayac;
                                                                        $abonesayac->serino = $kalanserinolar[0];
                                                                        $abonesayac->uretimyer_id = $uretimyer;
                                                                        $sayacadi = SayacAdi::find($kalem->subeurun->sayacadi_id);
                                                                        $abonesayac->sayactur_id = $sayacadi->sayactur_id;
                                                                        $abonesayac->sayacadi_id = $kalem->subeurun->sayacadi_id;
                                                                        $abonesayac->sayaccap_id = $kalem->subeurun->sayaccap_id;
                                                                        $abonesayac->bilgi=$telefon;
                                                                        //$abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$kalanserinolar[0])->first();
                                                                        $abonesayac->adres=/*($abonebilgi && $abonebilgi->faturaadresi!=null) ? BackendController::StrtoUpper($abonebilgi->faturaadresi) : */$adres;
                                                                        $abonesayac->save();
                                                                    } catch (Exception $e) {
                                                                        Log::error($e);
                                                                        if($connection) BackendController::DeleteConnection($connection);
                                                                        DB::rollBack();
                                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Sayacı Kaydedilemedi', 'text' => str_replace("'", "\'", $e->getMessage()), 'type' => 'error'));
                                                                    }
                                                                }
                                                                $ek .= ($ek == "" ? "" : ",") . $abonesayac->id;
                                                                array_push($abonesayaclist, $abonesayac->id);
                                                                array_push($secilenserinolar, $kalanserinolar[0]);
                                                                array_splice($kalanserinolar, 0, 1);
                                                                break;
                                                            }
                                                        } else {
                                                            $kayitliyer = Uretimyer::find($sayac->uretimyer_id);
                                                        }
                                                    }
                                                    if (!$sayacdurum) {
                                                        if ($kayitliyer == "") { //sayaç yoksa sayaç kaydedilecek
                                                            try {
                                                                $sayac = new Sayac;
                                                                $sayac->serino = $kalanserinolar[0];
                                                                $sayac->cihazno = $kalanserinolar[0];
                                                                $sayacadi = SayacAdi::find($kalem->subeurun->sayacadi_id);
                                                                $sayac->sayactur_id = $sayacadi->sayactur_id;
                                                                $sayac->sayacadi_id = $kalem->subeurun->sayacadi_id;
                                                                $sayac->sayaccap_id = $kalem->subeurun->sayaccap_id;
                                                                $sayac->uretimtarihi = date('Y-m-d H:i:s');
                                                                $sayac->uretimyer_id = $uretimyer;
                                                                $sayac->kullanici_id = Auth::user()->id;
                                                                $sayac->save();
                                                                $abonesayac = AboneSayac::where('serino', $kalanserinolar[0])->first();
                                                                if ($abonesayac) {
                                                                    $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                                                                    if ($abonetahsis) {
                                                                        $abone = Abone::find($abonetahsis->abone_id);
                                                                        if(is_null($tahsisliabone)){
                                                                            if($tckimlikno!="" && $abone->tckimlikno==$tckimlikno){
                                                                                array_push($tahsislilist,$abonesayac->id);
                                                                            }else if($vergino!="" && $abone->tckimlikno==$vergino){
                                                                                array_push($tahsislilist,$abonesayac->id);
                                                                            }else if($nadisoyadi!="" && trim($abone->nadisoyadi)==$nadisoyadi){
                                                                                array_push($tahsislilist,$abonesayac->id);
                                                                            }else{
                                                                                if($connection) BackendController::DeleteConnection($connection);
                                                                                DB::rollBack();
                                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: ' . $abone->abone_no, 'type' => 'error'));
                                                                            }
                                                                            $tahsisliabone = $abone;
                                                                        }else if($tahsisliabone->id===$abone->id){
                                                                            array_push($tahsislilist,$abonesayac->id);
                                                                        }else{
                                                                            if($connection) BackendController::DeleteConnection($connection);
                                                                            DB::rollBack();
                                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => 'Girilen Seri No Başka Abonede Kayıtlı: ' . $abone->adisoyadi . ' Abone No: ' . $abone->abone_no, 'type' => 'error'));
                                                                        }
                                                                    } else {
                                                                        //$abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$kalanserinolar[0])->first();
                                                                        $abonesayac->adres=/*($abonebilgi && $abonebilgi->faturaadresi!=null) ? BackendController::StrtoUpper($abonebilgi->faturaadresi) : */$adres;
                                                                        $abonesayac->bilgi=$telefon;
                                                                        $abonesayac->save();
                                                                    }
                                                                } else {
                                                                    try {
                                                                        $abonesayac = new AboneSayac;
                                                                        $abonesayac->serino = $kalanserinolar[0];
                                                                        $abonesayac->uretimyer_id = $uretimyer;
                                                                        $sayacadi = SayacAdi::find($kalem->subeurun->sayacadi_id);
                                                                        $abonesayac->sayactur_id = $sayacadi->sayactur_id;
                                                                        $abonesayac->sayacadi_id = $kalem->subeurun->sayacadi_id;
                                                                        $abonesayac->sayaccap_id = $kalem->subeurun->sayaccap_id;
                                                                        //$abonebilgi=AboneBilgi::where('subekodu',$subekodu)->where('serino',$kalanserinolar[0])->first();
                                                                        $abonesayac->adres=/*($abonebilgi && $abonebilgi->faturaadresi!=null) ? BackendController::StrtoUpper($abonebilgi->faturaadresi) : */$adres;
                                                                        $abonesayac->bilgi=$telefon;
                                                                        $abonesayac->save();
                                                                    } catch (Exception $e) {
                                                                        Log::error($e);
                                                                        if($connection) BackendController::DeleteConnection($connection);
                                                                        DB::rollBack();
                                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Sayacı Kaydedilemedi', 'text' => str_replace("'", "\'", $e->getMessage()), 'type' => 'error'));
                                                                    }
                                                                }
                                                                $ek .= ($ek == "" ? "" : ",") . $abonesayac->id;
                                                                array_push($abonesayaclist, $abonesayac->id);
                                                                array_push($secilenserinolar, $kalanserinolar[0]);
                                                                array_splice($kalanserinolar, 0, 1);
                                                            } catch (Exception $e) {
                                                                Log::error($e);
                                                                if($connection) BackendController::DeleteConnection($connection);
                                                                DB::rollBack();
                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hatası', 'text' => 'Girilen Sayaç Sisteme Kaydedilemedi', 'type' => 'error'));
                                                            }
                                                        } else {
                                                            if($connection) BackendController::DeleteConnection($connection);
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hatası', 'text' => 'Girilen Sayacın Sistemdeki Yeri Farklı: ' . $kalanserinolar[0] . '->' . $kayitliyer->yeradi, 'type' => 'error'));
                                                        }
                                                    }
                                                } else { //serino kalmadıysa
                                                    if($connection) BackendController::DeleteConnection($connection);
                                                    DB::rollBack();
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hatası', 'text' => 'Sayaç Sayısı ile Seri Numara Sayısı Uyuşmuyor', 'type' => 'error'));
                                                }
                                            }
                                        }
                                        $abonesayaci .= ($abonesayaci == "" ? "" : ";") . $ek;
                                        $baglisayac += $kalem->STHAR_GCMIK;
                                    } else {
                                        if($connection) BackendController::DeleteConnection($connection);
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fatura Bilgisi Kaydedilemedi', 'text' => 'Sayaç Sayısı ile Seri Numara Sayısı Uyuşmuyor', 'type' => 'error'));
                                    }
                                } else {
                                    $abonesayaci .= ($abonesayaci == "" ? "" : ";") . '0';
                                }
                            }
                        }
                    }
                    if ($adet == 0 && $baglisayac > 0) {
                        if($connection) BackendController::DeleteConnection($connection);
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Bilgisi Kaydedilemedi', 'text' => 'Girilen Sayaçların Seri Numaraları yazılmamış', 'type' => 'error'));
                    }else if($adet>0 && $baglisayac<$adet){
                        if($connection) BackendController::DeleteConnection($connection);
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Bilgisi Kaydedilemedi', 'text' => 'Girilen Ürünler İçin Bağlı Sayaç Tipi Yok', 'type' => 'error'));
                    }
                    if ($fatura->count() > 0) {
                        try {
                            $subeabone = BackendController::AboneNoBul($sube, $adisoyadi, $tckimlikno == "" ? $vergino : $tckimlikno, $serinolar);
                            if ($subeabone) {
                                $aboneno = $subeabone->aboneno;
                                $basvurutarihi = date("Y-m-d H:i:s", strtotime($subeabone->basvurutarihi));
                            } else {
                                $aboneno = '';
                                $basvurutarihi = date('Y-m-d H:i:s');
                            }
                            if(is_null($tahsisliabone)){
                                if($uretimyer!=null){
                                    $abone = Abone::where('uretimyer_id',$uretimyer)->where('nadisoyadi',$nadisoyadi)->where('tckimlikno',$tckimlikno)->first();
                                }else if($uretimyerlist!=null){
                                    $abone = Abone::whereIn('uretimyer_id',$uretimyerlist)->where('nadisoyadi',$nadisoyadi)->where('tckimlikno',$tckimlikno)->first();
                                    $uretimyer = $uretimyerlist[0]['uretimyer_id'];
                                }else{
                                    if($connection) BackendController::DeleteConnection($connection);
                                    DB::rollBack();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Bilgisi Kaydedilemedi', 'text' => 'Girilen Ürünler İçin Bağlı Sayaç Tipi Yok', 'type' => 'error'));
                                }
                                if(!$abone){
                                    $abone = new Abone;
                                    $abone->subekodu = $subekodu;
                                    $abone->adisoyadi = $adisoyadi;
                                    $abone->vergidairesi = $vergidairesi;
                                    $abone->tckimlikno = $tckimlikno=="" ? $vergino : $tckimlikno;
                                    $abone->abone_no = $aboneno;
                                    $abone->telefon = $telefon;
                                    $abone->faturaadresi = $adres;
                                    $abone->mahalle = $mahalle;
                                    $abone->uretimyer_id = $uretimyer;
                                    $abone->netsiscari_id = $fatura->cari->id;
                                    $abone->kullanici_id = Auth::user()->id;
                                    $abone->save();
                                }
                            }else{
                                $abone = $tahsisliabone;
                            }
                        } catch (Exception $e) {
                            Log::error($e);
                            if($connection) BackendController::DeleteConnection($connection);
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Bilgisi Kaydedilemedi', 'text' => 'Abone Bilgisi Eksik Ya da Hatalı', 'type' => 'error'));
                        }
                        try {
                            for ($i = 0; $i < count($abonesayaclist); $i++) {
                                $flag = 0;
                                for($j = 0; $j < count($tahsislilist); $j++){
                                    if($abonesayaclist[$i]==$tahsislilist[$j]){
                                        $flag=1;
                                        break;
                                    }
                                }
                                if(!$flag){
                                    $abonetahsis = new AboneTahsis;
                                    $abonetahsis->abone_id = $abone->id;
                                    $abonetahsis->abonesayac_id = $abonesayaclist[$i];
                                    $abonetahsis->tahsistarihi = $basvurutarihi;
                                    $abonetahsis->save();
                                }
                                AboneSayac::where('id', $abonesayaclist[$i])->update(['satisdurum' => 1]);
                            }
                        } catch (Exception $e) {
                            Log::error($e);
                            if($connection) BackendController::DeleteConnection($connection);
                            DB::rollBack();
                            Input::flash();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Kayıdı Yapılamadı', 'text' => 'Abonenin Tahsisi Kaydedilemedi', 'type' => 'error'));
                        }
                        try {
                            $netsiscari = $fatura->cari;
                            $kdv_orani = SistemSabitleri::where('adi', 'KdvOrani')->first();
                            try {
                                $sayacsatis = new SubeSayacSatis;
                                $sayacsatis->subekodu = $subekodu;
                                $sayacsatis->abone_id = $abone->id;
                                $sayacsatis->uretimyer_id = $abone->uretimyer_id;
                                $sayacsatis->netsiscari_id = $netsiscari->id;
                                $sayacsatis->secilenler = $secilenler;
                                $sayacsatis->adet = $miktarlar;
                                $sayacsatis->birimfiyat = $birimfiyatlar;
                                $sayacsatis->ucretsiz = $ucretsizler;
                                $sayacsatis->sayaclar = $abonesayaci;
                                $sayacsatis->tutar = number_format(floatval($fatura->BRUTTUTAR), 2, '.', '');
                                $sayacsatis->kdv = number_format(floatval($fatura->KDV), 2, '.', '');
                                $sayacsatis->toplamtutar = number_format(floatval($fatura->GENELTOPLAM), 2, '.', '');
                                $sayacsatis->parabirimi_id = $parabirimi->id;
                                $sayacsatis->kullanici_id = Auth::user()->id;
                                $sayacsatis->kayittarihi = date('Y-m-d H:i:s');
                                $sayacsatis->kdvorani = $kdv_orani->deger;
                                $sayacsatis->yazitutar = BackendController::YaziTutar(number_format(floatval($fatura->GENELTOPLAM), 2, '.', ''), $parabirimi->id);
                                $sayacsatis->efatura = BackendController::Efatura($dbname, $netsiscari->id);
                                $sayacsatis->kurtarihi = $fatura->TARIH;
                                $sayacsatis->faturatarihi = $fatura->TARIH;
                                $sayacsatis->durum = 1;
                                $sayacsatis->db_name = $dbname;
                                $sayacsatis->faturano = $faturano;
                                $sayacsatis->faturaadres = $adres;
                                $sayacsatis->carikod = $netsiscari->carikod;
                                $sayacsatis->ozelkod = $yetkili->ozelkod;
                                $sayacsatis->projekodu = $fatura->PROJE_KODU;
                                $sayacsatis->plasiyerkod = $fatura->PLA_KODU;
                                $sayacsatis->depokodu = $yetkili->depokodu;
                                $sayacsatis->aciklama = $aciklama;
                                $sayacsatis->odemetipi = $fatura->kasakod->odemetipi;
                                $sayacsatis->odemesekli = $fatura->kasakod->odeme;
                                $sayacsatis->kasakodu = $fatura->KS_KODU;
                                $sayacsatis->kasakodu2 = '';
                                $sayacsatis->netsiskullanici = $fatura->KAYITYAPANKUL;
                                $sayacsatis->netsiskullanici_id = $yetkili->netsiskullanici_id;
                                $sayacsatis->gfiyat = $birimfiyatlar;
                                $sayacsatis->save();
                                $abone = Abone::find($sayacsatis->abone_id);
                                $sayaclar = explode(';', $sayacsatis->sayaclar);
                                foreach ($sayaclar as $sayac) {
                                    $sayaclist = explode(',', $sayac);
                                    foreach ($sayaclist as $sayacid) {
                                        if ($sayacid != 0) {
                                            $abonesayac = AboneSayac::find($sayacid);
                                            $abonetahsis = AboneTahsis::where('abone_id', $abone->id)->where('abonesayac_id', $abonesayac->id)->first();
                                            if ($abonetahsis) {
                                                $serviskayit = ServisKayit::where('abonetahsis_id',$abonetahsis->id)->where('acilmatarihi',$sayacsatis->faturatarihi)
                                                    ->where('tipi',0)->first();
                                                if(!$serviskayit) {
                                                    $serviskayit = new ServisKayit;
                                                    $serviskayit->subekodu = $sayacsatis->subekodu;
                                                    $serviskayit->kayitadres = $abonesayac->adres;
                                                    $serviskayit->abonetahsis_id = $abonetahsis->id;
                                                    $serviskayit->netsiscari_id = $sayacsatis->netsiscari_id;
                                                    $serviskayit->uretimyer_id = $sayacsatis->uretimyer_id;
                                                    $serviskayit->kullanici_id = Auth::user()->id;
                                                    $serviskayit->tipi = 0;
                                                    $serviskayit->aciklama = $abonesayac->bilgi;
                                                    $serviskayit->acilmatarihi = $sayacsatis->faturatarihi;
                                                    $serviskayit->save();
                                                    BackendController::HatirlatmaEkle(13, $abone->netsiscari_id, 6, 1);
                                                    BackendController::BildirimEkle(13, $abone->netsiscari_id, 6, 1);
                                                }
                                            }
                                        }
                                    }
                                }
                            } catch (Exception $e) {
                                Log::error($e);
                                if($connection) BackendController::DeleteConnection($connection);
                                DB::rollBack();
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fatura Bilgisi Kaydedilemedi', 'text' => 'Fatura Bilgisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                            }
                        } catch (Exception $e) {
                            Log::error($e);
                            if($connection) BackendController::DeleteConnection($connection);
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fatura Bilgisi Kaydedilemedi', 'text' => 'Fatura Bilgisi Kaydedilemedi.', 'type' => 'error'));
                        }
                        DB::commit();
                    }
                }
                if($connection) BackendController::DeleteConnection($connection);
                if($satisflag){
                    return Redirect::to($this->servisadi . '/faturabilgi')->with(array('mesaj' => 'true', 'title' => 'Fatura Bilgisi Aktarıldı', 'text' => 'Fatura Bilgisi Sube Satışlarına Başarıyla Aktarıldı. Bazı Faturalar Zaten Satış Kısmında mevcut Olduğu İçin Aktarılmadı.', 'type' => 'success'));
                }else{
                    return Redirect::to($this->servisadi . '/faturabilgi')->with(array('mesaj' => 'true', 'title' => 'Fatura Bilgisi Aktarıldı', 'text' => 'Fatura Bilgisi Sube Satışlarına Başarıyla Aktarıldı.', 'type' => 'success'));
                }
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fatura Aktarma Hatası', 'text' => 'Fatura Yıl Bilgisi Alınamadı', 'type' => 'error'));
            }
        } catch (Exception $e) {
            if($connection) BackendController::DeleteConnection($connection);
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fatura Bilgisi Kaydedilemedi', 'text' => 'Fatura Bilgisi Aktarılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getServisbilgi() {
        return View::make($this->servisadi.'.servisbilgi')->with(array('title'=>'Eski Servis Kayıtları Görüntüleme Ekranı'));
    }

    public function postServisbilgilist() {
        $netsiscari_id=Input::get('netsiscari_id');
        if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $sube=Sube::whereIn('netsiscari_id',$netsiscarilist)->where('aktif',1)->first();
            if($sube){
                $query = ServisBilgi::where('subekodu',$sube->subekodu)
                    ->select(array("servisbilgi.id", "servisbilgi.adisoyadi", "servisbilgi.serino","servisbilgi.isemritipi", "servisbilgi.acilmatarihi", "servisbilgi.kapanmatarihi", "servisbilgi.nadisoyadi"));
            }else{
                $query = ServisBilgi::where('subekodu',0)
                    ->select(array("servisbilgi.id", "servisbilgi.adisoyadi", "servisbilgi.serino","servisbilgi.isemritipi", "servisbilgi.acilmatarihi", "servisbilgi.kapanmatarihi", "servisbilgi.nadisoyadi"));
            }
        }else{
            $query = ServisBilgi::select(array("servisbilgi.id", "servisbilgi.adisoyadi", "servisbilgi.serino","servisbilgi.isemritipi", "servisbilgi.acilmatarihi", "servisbilgi.kapanmatarihi", "servisbilgi.nadisoyadi"));
        }
        return Datatables::of($query)
            ->editColumn('acilmatarihi', function ($model) {
                if($model->acilmatarihi!=NULL){
                    $date = new DateTime($model->acilmatarihi);
                    return $date->format('d-m-Y');
                }else{
                    return '';
                }
            })
            ->editColumn('kapanmatarihi', function ($model) {
                if($model->kapanmatarihi!=NULL){
                    $date = new DateTime($model->kapanmatarihi);
                    return $date->format('d-m-Y');
                }else{
                    return '';
                }
            })
            ->addColumn('islemler',function ($model){
                return "<a class='btn btn-sm btn-warning goster' href='#detay-goster' data-toggle='modal' data-id='{$model->id}'> Detay </a>";
            })
            ->make(true);
    }

    public function getServisbilgidetay(){
        try {
            $id=Input::get('id');
            $servisbilgi = ServisBilgi::find($id);
            $servisbilgi->durum = $servisbilgi->durum==1 ? 'Bekliyor' : 'Tamamlandı';
            if ($servisbilgi->acilmatarihi)
                $servisbilgi->acilmatarihi = date('d-m-Y', strtotime($servisbilgi->acilmatarihi));
            else
                $servisbilgi->acilmatarihi = "";
            if ($servisbilgi->kapanmatarihi)
                $servisbilgi->kapanmatarihi = date('d-m-Y', strtotime($servisbilgi->kapanmatarihi));
            else
                $servisbilgi->kapanmatarihi = "";
            return Response::json(array('durum'=>true,'servisbilgi' => $servisbilgi));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title'=>'Servis Bilgisi Hatalı','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

    public function getSayaclar() {
        return View::make($this->servisadi.'.sayaclar')->with(array('title'=>$this->servisbilgi.' Sayaç Listesi'));
    }

    public function postSayaclist() {
        $netsiscari_id=Input::get('netsiscari_id');
        if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $query = Sayac::whereIn('cariyer.netsiscari_id',$netsiscarilist)
                ->select(array("sayac.id","sayac.serino","sayac.cihazno","sayacadi.sayacadi","sayaccap.capadi","uretimyer.yeradi","sayac.uretimtarihi","sayac.guretimtarihi","sayacadi.nsayacadi",
                    "sayaccap.ncapadi","uretimyer.nyeradi"))
                ->leftjoin("uretimyer","sayac.uretimyer_id","=","uretimyer.id")
                ->leftjoin("cariyer","cariyer.uretimyer_id","=","uretimyer.id")
                ->leftjoin("sayacadi","sayac.sayacadi_id","=","sayacadi.id")
                ->leftjoin("sayaccap","sayac.sayaccap_id","=","sayaccap.id");
        }else{
            $query = Sayac::where('sayac.sayactur_id',$this->servisid)
                ->select(array("sayac.id","sayac.serino","sayac.cihazno","sayacadi.sayacadi","sayaccap.capadi","uretimyer.yeradi","sayac.uretimtarihi","sayac.guretimtarihi","sayacadi.nsayacadi",
                    "sayaccap.ncapadi","uretimyer.nyeradi"))
                ->leftjoin("uretimyer","sayac.uretimyer_id","=","uretimyer.id")
                ->leftjoin("sayacadi","sayac.sayacadi_id","=","sayacadi.id")
                ->leftjoin("sayaccap","sayac.sayaccap_id","=","sayaccap.id");
        }
        return Datatables::of($query)
            ->editColumn('uretimtarihi', function ($model) {
                if($model->uretimtarihi){
                    $date = new DateTime($model->uretimtarihi);
                    return $date->format('d-m-Y');
                }else{
                    return '';
                }
            })
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/sayacduzenle/".$model->id."'> Düzenle </a>".
                    "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getTektekekle() {
        $netsiscariid=Auth::user()->netsiscari_id;
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        $netsiscariler=NetsisCari::whereIn('id',Auth::user()->netsiscari_id)->get(array('id'))->toArray();
        $uretimyerid=CariYer::whereIn('netsiscari_id',$netsiscariler)->where('durum',1)->get(array('uretimyer_id'))->toArray();
        $uretimyerleri = UretimYer::whereIn('id',$uretimyerid)->get();
        if($sube){
            $sayacturlist=explode(',',$sube->sayactur);
            $sayacadilist=explode(',',$sube->sayacadlari);
            $sayacadlari = SayacAdi::whereIn('sayactur_id',$sayacturlist)->whereIn('id',$sayacadilist)->get();
            $sayaccaplari = SayacCap::all();
        }else{
            $sayacadlari = SayacAdi::all();
            $sayaccaplari = SayacCap::all();
        }
        return View::make($this->servisadi.'.tektekekle',array('uretimyerleri'=>$uretimyerleri,'sayacadlari'=>$sayacadlari,'sayaccaplari'=>$sayaccaplari))->with(array('title'=>$this->servisbilgi.' Sayaç Ekle'));
    }

    public function postTektekekle() {
        try {
            $rules = ['uretimtarihi' => 'required', 'uretimyer' => 'required', 'serino' => 'required', 'sayacadlari' => 'required', 'sayaccaplari' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $tarih = Input::get('uretimtarihi');
            $uretimtarih = date("Y-m-d", strtotime($tarih));
            $uretimyer_id = Input::get('uretimyer');
            $serinolar = Input::get('serino');
            $sayacadlari = Input::get('sayacadlari');
            $sayaccaplari = Input::get('sayaccaplari');
            $eklenenler = "";
            $adet = 0;
            for ($i = 0; $i < count($serinolar); $i++) {
                $serino1 = $serinolar[$i];
                if ($serino1 == "")
                    continue;
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
            $i = 0;
            $flag = 0;
            DB::beginTransaction();
            try {
                foreach ($serinolar as $serino) {
                    $sayac = Sayac::where('uretimyer_id', $uretimyer_id)->where('serino', $serino)->first();
                    if ($sayac) {
                        if ($sayac->sayacadi_id != null) //sayaç zaten kayıtlı
                        {
                            $flag = 1;
                        } else {
                            if ($serino != "") {
                                $sayac->sayactur_id = BackendController::getSayacTurId($sayacadlari[$i]);
                                $sayac->sayacadi_id = $sayacadlari[$i];
                                $sayac->sayaccap_id = $sayaccaplari[$i];
                                $sayac->uretimyer_id = $uretimyer_id;
                                $sayac->uretimtarihi = $uretimtarih;
                                $sayac->save();
                            } else {
                                $flag = ($flag != 1) ? 2 : $flag;
                            }
                        }
                    } else {
                        if ($serino != "") {
                            $sayac = new Sayac;
                            $sayac->serino = $serino;
                            $sayac->cihazno = $serino;
                            $sayac->sayactur_id = BackendController::getSayacTurId($sayacadlari[$i]);
                            $sayac->sayacadi_id = $sayacadlari[$i];
                            $sayac->sayaccap_id = $sayaccaplari[$i];
                            $sayac->uretimyer_id = $uretimyer_id;
                            $sayac->uretimtarihi = $uretimtarih;
                            $eklenenler .= ($eklenenler == "" ? "" : ",") . $serino;
                            $sayac->save();
                            $adet++;
                        }
                    }
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Eklenemedi', 'text' => 'Sayaç Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $this->servisbilgi.' İçin Yeni Sayaç Seri Noları Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Seri Nolar:' . $adet>10 ? $adet.' Adet' : $eklenenler);
            DB::commit();
            if ($flag == 1) {
                return Redirect::to($this->servisadi.'/sayaclar')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Bazıları Eklendi', 'text' => 'Sistemde Kayıtlı Olan Sayaçlar Tekrar Eklenmedi', 'type' => 'success'));
            } else if ($flag == 2) {
                return Redirect::to($this->servisadi.'/sayaclar')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Bazıları Eklenmedi', 'text' => 'Sayaç Serinosu Boş Geçilenler Eklenmedi', 'type' => 'success'));
            } else {
                return Redirect::to($this->servisadi.'/sayaclar')->with(array('mesaj' => 'true', 'title' => 'Sayaçlar Eklendi', 'text' => 'Sayaçlar Başarıyle Eklendi.', 'type' => 'success'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::to($this->servisadi.'/sayaclar')->with(array('mesaj' => 'true', 'title' => 'Sayaçlar Eklenirken Hata Oluştu', 'text' => str_replace("'","'",$e->getMessage()), 'type' => 'success'));
        }
    }

    public function getSiraliekle() {
        $netsiscariid=Auth::user()->netsiscari_id;
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        $netsiscariler=NetsisCari::whereIn('id',Auth::user()->netsiscari_id)->get(array('id'))->toArray();
        $uretimyerid=CariYer::whereIn('netsiscari_id',$netsiscariler)->where('durum',1)->get(array('uretimyer_id'))->toArray();
        $uretimyerleri = UretimYer::whereIn('id',$uretimyerid)->get();
        if($sube){
            $sayacturlist=explode(',',$sube->sayactur);
            $sayacadilist=explode(',',$sube->sayacadlari);
            $sayacadlari = SayacAdi::whereIn('sayactur_id',$sayacturlist)->whereIn('id',$sayacadilist)->get();
            $sayaccaplari = SayacCap::all();
        }else{
            $sayacadlari = SayacAdi::all();
            $sayaccaplari = SayacCap::all();
        }
        return View::make($this->servisadi.'.siraliekle',array('uretimyerleri'=>$uretimyerleri,'sayacadlari'=>$sayacadlari,'sayaccaplari'=>$sayaccaplari))->with(array('title'=>$this->servisbilgi.' Sayaç Ekle'));
    }

    public function postSiraliekle() {
        try {
            $rules = ['uretimtarihi' => 'required', 'uretimyer' => 'required', 'sayacadi' => 'required', 'baslangic' => 'required', 'bitis' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }

            $tarih = Input::get('uretimtarihi');
            $uretimtarih = date("Y-m-d", strtotime($tarih));
            $uretimyer_id = Input::get('uretimyer');
            $baslangic = Input::get('baslangic');
            $bitis = Input::get('bitis');
            $artis = Input::get('artis');
            $sayacadi_id = Input::get('sayacadi');
            if (Input::has('sayaccap')) {
                $sayaccap_id = Input::get('sayaccap');
            } else {
                $sayaccap_id = 1;
            }
            $adet = 0;
            $flag = 0;
            DB::beginTransaction();
            try {
                $eklenenler="";
                for ($i = $baslangic; $i <= $bitis; $i = $i + $artis) {
                    $sayac = Sayac::where('uretimyer_id', $uretimyer_id)->where('serino', $i)->first();
                    if ($sayac) {
                        if ($sayac->sayacadi_id != null) //sayaç zaten kayıtlı
                        {
                            $flag = 1;
                        } else {
                            $sayac->sayactur_id = BackendController::getSayacTurId($sayacadi_id);
                            $sayac->sayacadi_id = $sayacadi_id;
                            $sayac->sayaccap_id = $sayaccap_id;
                            $sayac->uretimyer_id = $uretimyer_id;
                            $sayac->uretimtarihi = $uretimtarih;
                            $eklenenler .= ($eklenenler == "" ? "" : ",") . $i;
                            $sayac->save();
                        }
                    } else {
                        $sayac = new Sayac;
                        $sayac->serino = $i;
                        $sayac->cihazno = $i;
                        $sayac->sayactur_id = BackendController::getSayacTurId($sayacadi_id);;
                        $sayac->sayacadi_id = $sayacadi_id;
                        $sayac->sayaccap_id = $sayaccap_id;
                        $sayac->uretimyer_id = $uretimyer_id;
                        $sayac->uretimtarihi = $uretimtarih;
                        $eklenenler .= ($eklenenler == "" ? "" : ",") . $i;
                        $sayac->save();
                        $adet++;
                    }
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Eklenemedi', 'text' => 'Sayaç Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $this->servisbilgi.' İçin Yeni Sayaç Seri Noları Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Seri Nolar:' . $adet>10 ? $adet.' Adet' : $eklenenler);
            DB::commit();
            if ($flag == 1) {
                return Redirect::to($this->servisadi.'/sayaclar')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Bazıları Eklendi', 'text' => 'Sistemde Kayıtlı Olan Sayaçlar Tekrar Eklenmedi', 'type' => 'success'));
            } else if ($flag == 2) {
                return Redirect::to($this->servisadi.'/sayaclar')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Bazıları Eklendi', 'text' => 'Sayaç Türü Belirsiz Olan Sayaçlar Güncellendi', 'type' => 'success'));
            } else {
                return Redirect::to($this->servisadi.'/sayaclar')->with(array('mesaj' => 'true', 'title' => 'Sayaçlar Eklendi', 'text' => 'Sayaçlar Başarıyla Eklendi.', 'type' => 'success'));
            }
        } catch (Exception $e) {
            return Redirect::to($this->servisadi.'/sayaclar')->with(array('mesaj' => 'true', 'title' => 'Sayaçlar Eklenirken Hata Oluştu', 'text' => str_replace("'","'",$e->getMessage()), 'type' => 'success'));
        }
    }

    public function getSayacduzenle($id) {
        $netsiscariid=Auth::user()->netsiscari_id;
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        $netsiscariler=NetsisCari::whereIn('id',Auth::user()->netsiscari_id)->get(array('id'))->toArray();
        $uretimyerid=CariYer::whereIn('netsiscari_id',$netsiscariler)->where('durum',1)->get(array('uretimyer_id'))->toArray();
        $uretimyerleri = UretimYer::whereIn('id',$uretimyerid)->get();
        if($sube){
            $sayacturlist=explode(',',$sube->sayactur);
            $sayacadilist=explode(',',$sube->sayacadlari);
            $sayacadlari = SayacAdi::whereIn('sayactur_id',$sayacturlist)->whereIn('id',$sayacadilist)->get();
            $sayaccaplari = SayacCap::all();
        }else{
            $sayacadlari = SayacAdi::all();
            $sayaccaplari = SayacCap::all();
        }
        $sayac = Sayac::find($id);
        return View::make($this->servisadi.'.sayacduzenle',array('sayac'=>$sayac,'uretimyerleri'=>$uretimyerleri,'sayacadlari'=>$sayacadlari,'sayaccaplari'=>$sayaccaplari))->with(array('title'=>$this->servisbilgi.' Sayaç Düzenleme Ekranı'));
    }

    public function postSayacduzenle($id) {
        try {
            $rules = ['uretimtarihi' => 'required', 'uretimyer' => 'required', 'sayacadi' => 'required', 'serino' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }

            $tarih = Input::get('uretimtarihi');
            $uretimtarih = date("Y-m-d", strtotime($tarih));
            $uretimyer_id = Input::get('uretimyer');
            $serino = Input::get('serino');
            $sayacadi_id = Input::get('sayacadi');
            if (Input::has('sayaccap')) {
                $sayaccap_id = Input::get('sayaccap');
            } else {
                $sayaccap_id = 1;
            }
            DB::beginTransaction();
            $sayac = Sayac::where('serino', $serino)->where('uretimyer_id', $uretimyer_id)->where('id', '<>', $id)->first();
            if ($sayac) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaç Güncellenemedi', 'text' => 'Seri No zaten bu yere ait mevcut', 'type' => 'error'));
            } else {
                $sayac = Sayac::find($id);
                $bilgi = clone $sayac;
                $sayac->serino = $serino;
                $sayac->uretimyer_id = $uretimyer_id;
                $sayac->uretimtarihi = $uretimtarih;
                $sayac->sayacadi_id = $sayacadi_id;
                $sayac->sayaccap_id = $sayaccap_id;
                $sayac->save();
                BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $this->servisbilgi.' İçin ' . $serino . ' Seri Nolu Sayacın Bilgileri Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Sayaç Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::to($this->servisadi.'/sayaclar')->with(array('mesaj' => 'true', 'title' => 'Sayac Güncellendi', 'text' => 'Sayac Başarıyla Güncellendi', 'type' => 'success'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Güncellenemedi', 'text' => 'Sayac Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getSayacsil($id){
        try {
            DB::beginTransaction();
            $sayac = Sayac::find($id);
            if($sayac){
                $bilgi = clone $sayac;
                $arizakayit = ArizaKayit::where('sayac_id', $id)->first();
                if ($arizakayit) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Silinemez', 'text' => 'Sayaç Arıza Kayıdında Kullanılmış.', 'type' => 'error'));
                }
                $sayac->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $this->servisbilgi.' İçin ' . $bilgi->serino . ' Seri Nolu Sayacın Bilgileri Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Sayaç Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Silindi', 'text' => 'Sayaç Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Silinemedi', 'text' => 'Sayaç Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Silinemedi', 'text' => 'Sayaç Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getNetsiscari() {
        return View::make($this->servisadi.'.netsiscari')->with(array('title'=>'Cari Bilgiler'));
    }

    public function getCarisorgula()
    {
        $vergino = Input::get('vergino');
        $dbname = 'MANAS' . date('Y');
        if($vergino!="") {
            $efaturakullanici = EfaturaKullanici::where('VTCKN',$vergino)->first();
            $efaturacari = EfaturaCari::where('SIRKET',$dbname)->where('VTCKN',$vergino)->first();
            $netsiscari = NetsisCari::where('vergino',$vergino)->first();
            $efatura = new NetsisCari;
            if($efaturakullanici){
                $efatura->unvan=BackendController::Trk($efaturakullanici->UNVAN);
                try {
                    $date = new DateTime($efaturakullanici->GUNCELLEME_TARIHI);
                    $efatura->guncellenmetarihi = $date->format('d-m-Y');
                } catch (Exception $e) {
                    $efatura->guncellenmetarihi = '';
                }
                if($efaturacari){
                    $efatura->carikod=BackendController::Trk($efaturacari->CARI_KODU);
                    $enetsiscari = NetsisCari::where('carikod',$efatura->carikod)->first();
                    $efatura->cariadi=$enetsiscari ? $enetsiscari->cariadi : '';
                }
                if($netsiscari)
                    $efatura->gdurum = $netsiscari->gdurum;
                else
                    $efatura->gdurum = "Kayıtlı Değil";
                return array("durum" => true,"efatura" => 1,"netsiscari"=>$efatura);
            }else{
                if($netsiscari){
                    $netsiscari->unvan = '';
                    $netsiscari->guncellenmetarihi = '';
                    if($netsiscari->aciklama === 'E-FATURA MUKELLEFI'){
                        return array("durum" => true,"efatura" =>1,"netsiscari"=>$netsiscari );
                    }else{
                        return array("durum" => true,"efatura" =>0,"netsiscari"=>$netsiscari );
                    }
                }else{
                    return array("durum" => true,"efatura" => 0,"netsiscari"=>null);
                }
            }
        }else{
            return array("durum" => false, "type" => "error", "title" => "Cari Bilgisi Getirilemedi", "text" => "Vergi Numarası Boş Geçilmiş!");
        }
    }

    public function postCarilist() {
        $netsiscari_id=Input::get('netsiscari_id');
        if($netsiscari_id!="") {
            $netsiscariid = explode(',', $netsiscari_id);
            $sube = Sube::whereIn('netsiscari_id', $netsiscariid)->where('aktif', 1)->first();
            $sube->netsiscari = NetsisCari::find($sube->netsiscari_id);
            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->get(array('netsiscari_id'))->toArray();
            $query = NetsisCari::where('caridurum', 'A')->whereIn('caritipi', array('A', 'D'))
                ->where(function ($query) use ($subeyetkili, $sube) {$query->whereIn('id', $subeyetkili)->orwhereIn('subekodu', array(-1, $sube->subekodu));})
                ->whereNotIn('carikod', (function ($query) use ($sube) {$query->select(BackendController::Trk('carikod'))->from('kodharichar')->where('subekodu', $sube->subekodu);}))
                ->whereNotIn('id', array($sube->netsiscari_id))->
                select(array("netsiscari.id", "netsiscari.carikod", "netsiscari.cariadi", "netsiscari.adres", "netsiscari.vergidairesi", "netsiscari.vergino",
                    "netsiscari.gcaritipi", "netsiscari.gdurum", "netsiscari.ncariadi", "netsiscari.nadres", "netsiscari.nvergidairesi", "netsiscari.nvergino", "netsiscari.ncaritipi",
                    "netsiscari.ndurum","netsiscari.subekodu"));
            return Datatables::of($query)
                ->addColumn('islemler', function ($model) use($netsiscari_id,$sube) {
                    $root = BackendController::getRootDizin();
                    if($netsiscari_id)
                        if($model->subekodu==$sube->subekodu)
                            return "<a class='btn btn-sm btn-warning' href='".$root."/subedatabase/cariduzenle/".$model->id."' > Düzenle </a>
                        <a href='#portlet-delete' data-toggle='modal' data-id='{$model->id}' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                        else
                            return "<a class='btn btn-sm btn-info' href='".$root."/subedatabase/carigoster/".$model->id."' > Detay </a>";
                    else
                        return "<a class='btn btn-sm btn-info' href='".$root."/subedatabase/carigoster/".$model->id."'> Detay </a>";
                })
                ->make(true);
        }else{
            $query = NetsisCari::select(array("netsiscari.id","netsiscari.carikod","netsiscari.cariadi","netsiscari.adres","netsiscari.vergidairesi","netsiscari.vergino",
                    "netsiscari.gcaritipi","netsiscari.gdurum","netsiscari.ncariadi","netsiscari.nadres","netsiscari.nvergidairesi","netsiscari.nvergino","netsiscari.ncaritipi",
                    "netsiscari.ndurum"));
            return Datatables::of($query)
                ->addColumn('islemler', function ($model) {
                    $root = BackendController::getRootDizin();
                    return "<a class='btn btn-sm btn-warning' href='".$root."/subedatabase/carigoster/".$model->id."'> Detay </a>";
                })
                ->make(true);
        }
    }

    public function getCarigoster($id) {
        $netsiscari = NetsisCari::find($id);
        return View::make($this->servisadi.'.netsiscarigoster',array('netsiscari'=>$netsiscari))->with(array('title'=>'Netsis Cari Bilgisi Detayı'));
    }

    public function getCariekle() {
        $netsiscariid=Auth::user()->netsiscari_id;
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        $sehirler = Sehirsabit::orderBy('SEHIRADI','asc')->get();
        return View::make($this->servisadi.'.cariekle',array('sube'=>$sube,'sehirler'=>$sehirler))->with(array('title'=>'Netsis Cari Bilgi Ekleme Ekranı'));
    }

    public function postCariekle() {
        try {
            if( Input::get('tckimlikno')!="" ){
                $rules = ['carikod'=>'required','cariadi' => 'required', 'vergidairesi' => 'required', 'tckimlikno' => 'required', 'adres' => 'required', 'il' => 'required', 'ilce' => 'required',
                    'caritipi' => 'required','durum' => 'required'];
            }else{
                $rules = ['carikod'=>'required','cariadi' => 'required', 'vergidairesi' => 'required', 'vergino' => 'required', 'adres' => 'required', 'il' => 'required', 'ilce' => 'required',
                    'caritipi' => 'required','durum' => 'required'];
            }
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $carikod = Input::get('carikod');
            $cariadi = Input::get('cariadi');
            $subekodu = Input::get('subekodu');
            $vergidairesi = Input::get('vergidairesi');
            $vergino = Input::get('vergino');
            $tckimlikno = Input::get('tckimlikno');
            $adres = Input::get('adres');
            $il = Input::get('il');
            $ilce = Input::get('ilce');
            $tel = Input::get('telefon');
            $email = Input::get('email');
            $postakodu = Input::get('postakodu');
            $caritipi = Input::get('caritipi');
            $caridurum = Input::get('durum');
            $yetkiliadi = Input::get('yetkili');
            $yetkilitel = Input::get('yetkilitel');
            $vadegunu = Input::get('vadegunu');
            $efatura = Input::get('EfaturaCari');
            DB::beginTransaction();
            $netsiscari = NetsisCari::where('carikod', $carikod)->first();
            if ($netsiscari) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Eklenemedi', 'text' => 'Bu Cari Kodu Sistemde Kayıtlı!', 'type' => 'error'));
            } else {
                $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
                if (!$subeyetkili) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube Yetkili Uyarısı', 'text' => 'Bu Yere Ait Şube Yetkilisi Yok.', 'type' => 'warning'));
                }
                $yetkili = ServisYetkili::where('kullanici_id', $subeyetkili->kullanici_id)->first();
                if (!$yetkili) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetki Uyarısı', 'text' => 'Bu Kullanıcının Yetkisi Yok.', 'type' => 'warning'));
                }
                $sehir = Sehirsabit::where('SEHIRKODU',$il)->first();
                if(!$sehir){
                    DB::rollBack();
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Eklenemedi', 'text' => 'Netsis Şehir Bilgisi Bulunamadı', 'type' => 'error'));
                }
                $casabit = new Casabit;
                $casabit->SUBE_KODU = $subekodu;
                $casabit->ISLETME_KODU = 1;
                $casabit->CARI_KOD = BackendController::ReverseTrk($carikod);
                $casabit->CARI_TEL = $tel;
                $casabit->ULKE_KODU = BackendController::ReverseTrk($sehir->ULKEKODU);
                $casabit->CARI_IL = BackendController::ReverseTrk($sehir->SEHIRADI);
                $casabit->CARI_ILCE = BackendController::ReverseTrk(BackendController::StrtoUpper($ilce));
                $casabit->CARI_ISIM = BackendController::ReverseTrk(BackendController::StrtoUpper($cariadi));
                $casabit->CARI_TIP = $caritipi;
                $casabit->GRUP_KODU = '';
                $casabit->RAPOR_KODU1 = '';
                $casabit->RAPOR_KODU2 = '';
                $casabit->CARI_ADRES = BackendController::ReverseTrk(BackendController::StrtoUpper($adres));
                $casabit->VERGI_DAIRESI = BackendController::ReverseTrk(BackendController::StrtoUpper($vergidairesi));
                $casabit->VERGI_NUMARASI = $vergino!="" ? BackendController::ReverseTrk($vergino) : null;
                $casabit->EMAIL = $email;
                $casabit->POSTAKODU = $postakodu;
                $casabit->VADE_GUNU = $vadegunu;
                $casabit->ACIK1 = BackendController::ReverseTrk(BackendController::StrtoUpper($yetkiliadi));
                $casabit->ACIK2 = BackendController::ReverseTrk($yetkilitel);
                $casabit->ACIK3 = BackendController::ReverseTrk($efatura ? 'E-FATURA MUKELLEFI' : null);
                $casabit->M_KOD = BackendController::ReverseTrk($carikod);
                $casabit->HESAPTUTMASEKLI = 'Y';
                $casabit->DOVIZLIMI = 'H';
                $casabit->UPDATE_KODU = 'X';
                $casabit->C_YEDEK1 = $caridurum;
                $casabit->KAYITYAPANKUL = $yetkili->netsiskullanici;
                $casabit->KAYITTARIHI = date('Y-m-d');
                $casabit->save();
                $casabitek = new CasabitEk;
                $casabitek->CARI_KOD = BackendController::ReverseTrk($carikod);
                $casabitek->KAYITTARIHI = date('Y-m-d');
                $casabitek->KAYITYAPANKUL = $yetkili->netsiskullanici;
                $casabitek->I_YEDEK1 = $subekodu;
                $casabitek->TCKIMLIKNO = $tckimlikno!="" ? BackendController::ReverseTrk($tckimlikno) : null;
                $casabitek->save();
                $netsiscari = NetsisCari::where('carikod',$carikod)->first();
                if($netsiscari){
                    BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $carikod . ' Cari Kodlu Yeni Cari Bilgisi Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Cari Id:' . $netsiscari->id);
                    DB::commit();
                    return Redirect::to($this->servisadi.'/netsiscari')->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Eklendi', 'text' => 'Cari Bilgisi Başarıyla Eklendi', 'type' => 'success'));
                }else{
                    DB::rollBack();
                    if($casabit) $casabit->delete();
                    if($casabitek) $casabitek->delete();
                    return Redirect::to($this->servisadi.'/netsiscari')->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Eklenemedi', 'text' => 'Cari Bilgisi Eklenirken Hata Oluştu', 'type' => 'error'));
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Eklenemedi', 'text' => 'Cari Bilgisi Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getCariduzenle($id) {
        $netsiscariid=Auth::user()->netsiscari_id;
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        $netsiscari = NetsisCari::find($id);
        $sehirler = Sehirsabit::orderBy('SEHIRADI','asc')->get();
        $netsiscari->cariil = Sehirsabit::where('SEHIRADI',$netsiscari->il)->first();
        return View::make($this->servisadi.'.cariduzenle',array('netsiscari'=>$netsiscari,'sube'=>$sube,'sehirler'=>$sehirler))->with(array('title'=>'Netsis Cari Bilgi Düzenleme Ekranı'));
    }

    public function postCariduzenle($id) {
        try {
            if( Input::get('tckimlikno')!="" ){
                $rules = ['carikod'=>'required','cariadi' => 'required', 'vergidairesi' => 'required', 'tckimlikno' => 'required', 'adres' => 'required', 'il' => 'required', 'ilce' => 'required',
                    'caritipi' => 'required','durum' => 'required'];
            }else{
                $rules = ['carikod'=>'required','cariadi' => 'required', 'vergidairesi' => 'required', 'vergino' => 'required', 'adres' => 'required', 'il' => 'required', 'ilce' => 'required',
                    'caritipi' => 'required','durum' => 'required'];
            }
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $carikod = Input::get('carikod');
            $cariadi = Input::get('cariadi');
            $subekodu = Input::get('subekodu');
            $vergidairesi = Input::get('vergidairesi');
            $vergino = Input::get('vergino');
            $tckimlikno = Input::get('tckimlikno');
            $adres = Input::get('adres');
            $il = Input::get('il');
            $ilce = Input::get('ilce');
            $tel = Input::get('telefon');
            $email = Input::get('email');
            $postakodu = Input::get('postakodu');
            $caritipi = Input::get('caritipi');
            $caridurum = Input::get('durum');
            $yetkiliadi = Input::get('yetkili');
            $yetkilitel = Input::get('yetkilitel');
            $vadegunu = Input::get('vadegunu');
            $efatura = Input::get('EfaturaCari');
            DB::beginTransaction();
            $netsiscari = NetsisCari::where('carikod', $carikod)->where('id', '<>', $id)->first();
            if ($netsiscari) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Cari Bİlgisi Eklenemedi', 'text' => 'Bu Cari Kodu Sistemde Kayıtlı!', 'type' => 'error'));
            } else {
                $netsiscari = NetsisCari::find($id);
                $bilgi = clone $netsiscari;
                $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
                if (!$subeyetkili) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube Yetkili Uyarısı', 'text' => 'Bu Yere Ait Şube Yetkilisi Yok.', 'type' => 'warning'));
                }
                $yetkili = ServisYetkili::where('kullanici_id', $subeyetkili->kullanici_id)->first();
                if (!$yetkili) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetki Uyarısı', 'text' => 'Bu Kullanıcının Yetkisi Yok.', 'type' => 'warning'));
                }
                $sehir = Sehirsabit::where('SEHIRKODU',$il)->first();
                if(!$sehir){
                    DB::rollBack();
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Eklenemedi', 'text' => 'Netsis Şehir Bilgisi Bulunamadı', 'type' => 'error'));
                }
                $casabit = Casabit::where('CARI_KOD',BackendController::ReverseTrk($carikod))->first();
                $casabit->SUBE_KODU = $subekodu;
                $casabit->ISLETME_KODU = 1;
                $casabit->CARI_KOD = BackendController::ReverseTrk($carikod);
                $casabit->CARI_TEL = $tel;
                $casabit->ULKE_KODU = BackendController::ReverseTrk($sehir->ULKEKODU);
                $casabit->CARI_IL = BackendController::ReverseTrk($sehir->SEHIRADI);
                $casabit->CARI_ILCE = BackendController::ReverseTrk($ilce);
                $casabit->CARI_ISIM = BackendController::ReverseTrk($cariadi);
                $casabit->CARI_TIP = $caritipi;
                $casabit->GRUP_KODU = '';
                $casabit->RAPOR_KODU1 = '';
                $casabit->RAPOR_KODU2 = '';
                $casabit->CARI_ADRES = BackendController::ReverseTrk($adres);
                $casabit->VERGI_DAIRESI = BackendController::ReverseTrk($vergidairesi);
                $casabit->VERGI_NUMARASI = $vergino!="" ? BackendController::ReverseTrk($vergino) : null;
                $casabit->EMAIL = $email;
                $casabit->POSTAKODU = $postakodu;
                $casabit->VADE_GUNU = $vadegunu;
                $casabit->ACIK1 = BackendController::ReverseTrk($yetkiliadi);
                $casabit->ACIK2 = BackendController::ReverseTrk($yetkilitel);
                $casabit->ACIK3 = BackendController::ReverseTrk($efatura ? 'E-FATURA MUKELLEFI' : null);
                $casabit->M_KOD = BackendController::ReverseTrk($carikod);
                $casabit->HESAPTUTMASEKLI = 'Y';
                $casabit->DOVIZLIMI = 'H';
                $casabit->UPDATE_KODU = 'X';
                $casabit->C_YEDEK1 = $caridurum;
                $casabit->DUZELTMEYAPANKUL = $yetkili->netsisikullanici;
                $casabit->DUZELTMETARIHI = date('Y-m-d');
                $casabit->save();
                $casabitek = CasabitEk::where('CARI_KOD',BackendController::ReverseTrk($carikod))->first();
                $casabitek->CARI_KOD = BackendController::ReverseTrk($carikod);
                $casabitek->KAYITTARIHI = date('Y-m-d');
                $casabitek->KAYITYAPANKUL = $yetkili->netsiskullanici;
                $casabitek->I_YEDEK1 = $subekodu;
                $casabitek->TCKIMLIKNO = $tckimlikno!="" ? BackendController::ReverseTrk($tckimlikno) : null;
                $casabitek->save();
                BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $carikod . ' Cari Kodlu Cari Bilgisi Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Cari Id:' . $bilgi->id);
                DB::commit();
                return Redirect::to($this->servisadi.'/netsiscari')->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Güncellendi', 'text' => 'Cari Bilgisi Başarıyla Güncellendi', 'type' => 'success'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Güncellenemedi', 'text' => 'Cari Bilgisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getStokhareket() {
        return View::make($this->servisadi.'.stokhareket')->with(array('title'=>$this->servisbilgi.' Servis Stok Hareket Ekranı'));
    }

    public function postStokhareketlist() {
        $netsiscari_id=Input::get('netsiscari_id');
        $sube = null;
        if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $sube=Sube::whereIn('netsiscari_id',$netsiscarilist)->where('aktif',1)->first();
            if($sube){
                $query = StokGirisCikis::where('stokgiriscikis.subekodu',$sube->subekodu)
                    ->select(array("stokgiriscikis.id","stokgiriscikis.db_name","stokgiriscikis.fisno","stokgiriscikis.gsecilenler","stokgiriscikis.ggckod"
                    ,"stokgiriscikis.tarih","kullanici.adi_soyadi","stokgiriscikis.gtarih","stokgiriscikis.nsecilenler","stokgiriscikis.ngckod","kullanici.nadi_soyadi"))
                    ->leftjoin("kullanici","stokgiriscikis.kullanici_id","=","kullanici.id");
            }else{
                $query = StokGirisCikis::select(array("stokgiriscikis.id","stokgiriscikis.db_name","stokgiriscikis.fisno","stokgiriscikis.gsecilenler","stokgiriscikis.ggckod"
                    ,"stokgiriscikis.tarih","kullanici.adi_soyadi","stokgiriscikis.gtarih","stokgiriscikis.nsecilenler","stokgiriscikis.ngckod","kullanici.nadi_soyadi"))
                    ->leftjoin("kullanici","stokgiriscikis.kullanici_id","=","kullanici.id");
            }
        }else{
            $query = StokGirisCikis::select(array("stokgiriscikis.id","stokgiriscikis.db_name","stokgiriscikis.fisno","stokgiriscikis.gsecilenler","stokgiriscikis.ggckod"
                ,"stokgiriscikis.tarih","kullanici.adi_soyadi","stokgiriscikis.gtarih","stokgiriscikis.nsecilenler","stokgiriscikis.ngckod","kullanici.nadi_soyadi"))
                ->leftjoin("kullanici","stokgiriscikis.kullanici_id","=","kullanici.id");
        }
        return Datatables::of($query)
            ->editColumn('tarih', function ($model) {
                $date = new DateTime($model->tarih);
                return $date->format('d-m-Y');
            })

            ->addColumn('islemler', function ($model) use($netsiscari_id,$sube) {
                $root = BackendController::getRootDizin();
                if($netsiscari_id && $sube)
                    return "<a class='btn btn-sm btn-warning' href='".$root."/subedatabase/stokhareketduzenle/".$model->id."' > Düzenle </a>
                    <a href='#portlet-delete' data-toggle='modal' data-id='{$model->id}' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                else
                    return "<a class='btn btn-sm btn-info' href='".$root."/subedatabase/stokhareketgoster/".$model->id."' > Göster </a>";
            })
            ->make(true);
    }

    public function getStokhareketekle() {
        $netsiscariid=Auth::user()->netsiscari_id;
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        if($sube){
            $netsisdepolar = NetsisDepolar::where('subekodu',$sube->subekodu)->get();
            $masrafmerkezi = Masraf::where('SUBE_KODU',$sube->subekodu)->orWhere('SUBE_KODU',-1)->get(array('MKOD',DB::raw('dbo.TRK(ACIKLAMA) as ACIKLAMA')));
            $urunler=SubeUrun::where('subekodu',$sube->subekodu)->where('durum',1)->get();
            foreach($urunler as $urun){
                $urun->netsisstokkod = NetsisStokKod::find($urun->netsisstokkod_id);
                $urun->stok=StBakiye::where('STOK_KODU',$urun->netsisstokkod->kodu)->where('DEPO_KODU',$urun->depokodu)->first();
                if(!$urun->stok)
                    $urun->stok=new StBakiye(array('BAKIYE'=>0));
            }
            return View::make($this->servisadi.'.stokhareketekle',array('netsisdepolar'=>$netsisdepolar,'urunler'=>$urunler,'masrafmerkezi'=>$masrafmerkezi,'sube'=>$sube))->with(array('title'=>$this->servisbilgi.' Stok Hareketi Ekle'));
        }else{
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkisiz', 'text' => 'Stok Hareketi Ekleme Yetkiniz Yok', 'type' => 'error'));
        }
   }

    public function postStokhareketekle() {
        try {
            $rules = ['tarih' => 'required', 'gckod' => 'required', 'harekettur' => 'required', 'masraf' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $stokgiriscikis = new StokGirisCikis;
            $subekodu = Input::get('subekodu');
            $gckod = Input::get('gckod');
            $harekettur = Input::get('harekettur');
            $masraf = Input::get('masraf');
            $aciklama = Input::get('aciklama');
            $aciklama2 = Input::get('aciklama2');
            $aciklama3 = Input::get('aciklama3');
            $kalemadet = Input::get('count');
            $urun = Input::get('urunadi');
            $depokodu = Input::get('depokodu');
            $miktar = Input::get('miktar');
            $tarih = date('Y-m-d', strtotime(Input::get('tarih')));
            $secilenler = "";
            $depokodlari = "";
            $stokkodlari = "";
            $muhkodlari = "";
            $miktarlar = "";
            if ($subekodu == "") {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Şube Hatası', 'text' => 'Kullanıcıya Ait Aktif Bir Şube Tanımlı Değil!', 'type' => 'error'));
            }
            $sube = Sube::where('subekodu', $subekodu)->where('aktif', 1)->first();
            if (!$sube) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Şube Hatası', 'text' => 'Şube Bilgisi Bulunamadı!', 'type' => 'error'));
            }
            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
            if (!$subeyetkili) {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube Yetkili Uyarısı', 'text' => 'Bu Yere Ait Şube Yetkilisi Yok.', 'type' => 'warning'));
            }
            try {
                for ($i = 0; $i < $kalemadet; $i++) {
                    if ($urun[$i] != "") {
                        $subeurun = SubeUrun::find($urun[$i]);
                        if ($subeurun) {
                            $subeurun->netsisstokkod = NetsisStokKod::find($subeurun->netsisstokkod_id);
                            $stok = StBakiye::where('STOK_KODU', $subeurun->netsisstokkod->kodu)->where('DEPO_KODU', $subeurun->depokodu)->first();
                            if (!$stok)
                                $stok = new StBakiye(array('BAKIYE' => 0));

                            if ($gckod == 'C') //çıkış yapılıyorsa
                            {
                                if (intval($stok->BAKIYE) < intval($miktar[$i])) {
                                    DB::rollBack();
                                    Input::flash();
                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Stok Bakiyesi Yetersiz!', 'text' => $subeurun->urunadi . ' İsimli Ürünün Miktarı Yetersiz!', 'type' => 'error'));
                                }
                            }
                            $secilenler .= ($secilenler == "" ? "" : ",") . $urun[$i];
                            $miktarlar .= ($miktarlar == "" ? "" : ",") . $miktar[$i];
                            $depokodlari .= ($depokodlari == "" ? "" : ",") . $depokodu[$i];
                            $stokkodlari .= ($stokkodlari == "" ? "" : ",") . $subeurun->netsisstokkod->kodu;
                            $muhkodlari .= ($muhkodlari == "" ? "" : ",") . '999-001';
                        } else {
                            DB::rollBack();
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Stok Durumu Eşleştirilmemiş!', 'text' => ($i + 1) . '. Sıradaki Ürün, Netsis Stok Kodu ile Eşleştirilmemiş!', 'type' => 'error'));
                        }
                    }
                }
            } catch (Exception $e) {
                Log::error($e);
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ürün Eklemede Hata Oluştu', 'text' => 'Kalem Olarak Eklenen Ürünlerin Bilgilerinde Hata Var!', 'type' => 'error'));
            }
            $kalemler = BackendController::StokHareketGrupla($secilenler, $miktarlar, $depokodlari, $stokkodlari,$muhkodlari);
            if (count($kalemler) == 0) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Kaydedilemedi', 'text' => 'Stok ile İlgili Kalemlerde Hata Var', 'type' => 'error'));
            }

            $stokgiriscikis->subekodu = $subekodu;
            $stokgiriscikis->tarih = $tarih;
            $stokgiriscikis->gckod = $gckod;
            $stokgiriscikis->secilenler = $secilenler;
            $stokgiriscikis->adet = $miktarlar;
            $stokgiriscikis->depokodu = $depokodlari;
            $stokgiriscikis->stokkodu = $stokkodlari;
            $stokgiriscikis->muhkodu = $muhkodlari;
            $stokgiriscikis->harekettur = $harekettur;
            $stokgiriscikis->masrafkodu = $masraf;
            $stokgiriscikis->projekodu = $subeyetkili->projekodu;
            $stokgiriscikis->aciklama = $aciklama;
            $stokgiriscikis->aciklama2 = $aciklama2;
            $stokgiriscikis->aciklama3 = $aciklama3;
            $stokgiriscikis->tarih = date('Y-m-d H:i:s');
            $stokgiriscikis->servis_id = $this->servisid;
            $stokgiriscikis->kullanici_id = Auth::user()->id;
            $stokgiriscikis->durum = 1;
            $stokgiriscikis->save();
            if($stokgiriscikis->gckod=="G"){ // ambar girişi
                $faturano = Fatuno::where('SUBE_KODU', $subekodu)->where('SERI', 'Z')->where('TIP', '9')->first();
                if (!$faturano) {
                    $faturano = new Fatuno;
                    $faturano->SUBE_KODU = $subekodu;
                    $faturano->SERI = 'Z';
                    $faturano->TIP = 9;
                    $faturano->NUMARA = 'Z' . '0';
                    $faturano->save();
                }
                $fisno = BackendController::FaturaNo($faturano->NUMARA, 1);
                Fatuno::where(['SUBE_KODU'=> $subekodu,'SERI' => 'Z','TIP' => '9'])->update(['NUMARA' => $fisno]);
            }else{ //ambar çıkışı
                $faturano = Fatuno::where('SUBE_KODU', $subekodu)->where('SERI', 'Z')->where('TIP', '8')->first();
                if (!$faturano) {
                    $faturano = new Fatuno;
                    $faturano->SUBE_KODU = $subekodu;
                    $faturano->SERI = 'Z';
                    $faturano->TIP = 8;
                    $faturano->NUMARA = 'Z' . '0';
                    $faturano->save();
                }
                $fisno = BackendController::FaturaNo($faturano->NUMARA, 1);
                Fatuno::where(['SUBE_KODU'=> $subekodu,'SERI' => 'Z','TIP' => '8'])->update(['NUMARA' => $fisno]);
            }
            $stokgiriscikis->fisno = $fisno;
            $stokgiriscikis->save();
            $stokhareket = BackendController::NetsisSubeStokHareket($stokgiriscikis, $kalemler);
            if ($stokhareket['durum'] == '0') {
                $silmedurum = BackendController::NetsisSubeStokHareketTemizle($stokgiriscikis);
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Kaydedilemedi', 'text' => $stokhareket['text'].($silmedurum['durum']=='0' ? $silmedurum['text'] : ''), 'type' => 'error'));
            }
            $fatura = $stokhareket['fatura'];
            $stokgiriscikis->db_name = Config::get('database.connections.sqlsrv2.database');
            $stokgiriscikis->fisno = $fatura['FATIRS_NO'];
            $stokgiriscikis->inckeyno = $stokhareket['inckeylist'];
            $stokgiriscikis->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $sube->adi . ' için Stok Hareketi Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Stok Hareket Numarası:' . $stokgiriscikis->id);
            DB::commit();
            return Redirect::to($this->servisadi . '/stokhareket')->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Kaydedildi', 'text' => 'Stok Hareketi Başarıyla Kaydedildi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Input::flash();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Kaydedilemedi', 'text' => 'Stok Hareketi Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getStokhareketduzenle($id) {
        $netsiscariid=Auth::user()->netsiscari_id;
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        $stokgiriscikis=StokGirisCikis::find($id);
        if($sube){
            $netsisdepolar = NetsisDepolar::where('subekodu',$sube->subekodu)->get();
            $masrafmerkezi = Masraf::where('SUBE_KODU',$sube->subekodu)->orWhere('SUBE_KODU',-1)->get(array('MKOD',DB::raw('dbo.TRK(ACIKLAMA) as ACIKLAMA')));
            $urunler=SubeUrun::where('subekodu',$sube->subekodu)->where('durum',1)->get();
            foreach($urunler as $urun){
                $urun->netsisstokkod = NetsisStokKod::find($urun->netsisstokkod_id);
                $urun->stok=StBakiye::where('STOK_KODU',$urun->netsisstokkod->kodu)->where('DEPO_KODU',$urun->depokodu)->first();
                if(!$urun->stok)
                    $urun->stok=new StBakiye(array('BAKIYE'=>0));
            }
            $urunlist=explode(',',$stokgiriscikis->secilenler);
            $secilenler=array();
            for($i=0;$i<count($urunlist);$i++){
                $subeurun=SubeUrun::find($urunlist[$i]);
                array_push($secilenler,$subeurun);
            }
            $stokgiriscikis->urunler=$secilenler;
            $stokgiriscikis->adetler=explode(',',$stokgiriscikis->adet);
            $stokgiriscikis->depokodlari=explode(',',$stokgiriscikis->depokodu);
            return View::make($this->servisadi.'.stokhareketduzenle',array('stokgiriscikis'=>$stokgiriscikis,'netsisdepolar'=>$netsisdepolar,'urunler'=>$urunler,'masrafmerkezi'=>$masrafmerkezi,'sube'=>$sube))->with(array('title'=>$this->servisbilgi.' Stok Hareketi Düzenleme Ekranı'));
        }else{
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkisiz', 'text' => 'Stok Hareketi Düzenleme Yetkiniz Yok', 'type' => 'error'));
        }
    }

    public function postStokhareketduzenle($id) {
        try {
            $rules = ['tarih' => 'required', 'gckod' => 'required', 'harekettur' => 'required', 'masraf' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $stokgiriscikis = StokGirisCikis::find($id);
            $eskibilgi = clone  $stokgiriscikis;
            $subekodu = Input::get('subekodu');
            $gckod = Input::get('gckod');
            $harekettur = Input::get('harekettur');
            $masraf = Input::get('masraf');
            $aciklama = Input::get('aciklama');
            $aciklama2 = Input::get('aciklama2');
            $aciklama3 = Input::get('aciklama3');
            $kalemadet = Input::get('count');
            $urun = Input::get('urunadi');
            $depokodu = Input::get('depokodu');
            $miktar = Input::get('miktar');
            $tarih = date('Y-m-d', strtotime(Input::get('tarih')));
            $secilenler = "";
            $depokodlari = "";
            $stokkodlari = "";
            $muhkodlari = "";
            $miktarlar = "";
            $eskiurun = explode(',',$eskibilgi->secilenler);
            $eskiadet = explode(',',$eskibilgi->adet);
            if ($subekodu == "") {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Şube Hatası', 'text' => 'Kullanıcıya Ait Aktif Bir Şube Tanımlı Değil!', 'type' => 'error'));
            }
            $sube = Sube::where('subekodu', $subekodu)->where('aktif', 1)->first();
            if (!$sube) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Şube Hatası', 'text' => 'Şube Bilgisi Bulunamadı!', 'type' => 'error'));
            }
            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
            if (!$subeyetkili) {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube Yetkili Uyarısı', 'text' => 'Bu Yere Ait Şube Yetkilisi Yok.', 'type' => 'warning'));
            }
            try {
                for ($i = 0; $i < $kalemadet; $i++) {
                    if ($urun[$i] != "") {
                        $subeurun = SubeUrun::find($urun[$i]);
                        if ($subeurun) {
                            $subeurun->netsisstokkod = NetsisStokKod::find($subeurun->netsisstokkod_id);
                            $stok = StBakiye::where('STOK_KODU', $subeurun->netsisstokkod->kodu)->where('DEPO_KODU', $subeurun->depokodu)->first();
                            if (!$stok)
                                $stok = new StBakiye(array('BAKIYE' => 0));

                            if ($gckod == 'C') //çıkış yapılıyorsa
                            {
                                if(in_array($urun[$i],$eskiurun)){
                                    $index = array_search($urun[$i],$eskiurun);
                                    if (intval($stok->BAKIYE) < (intval($eskiadet[$index])-intval($miktar[$i]))) {
                                        DB::rollBack();
                                        Input::flash();
                                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Stok Bakiyesi Yetersiz!', 'text' => $subeurun->urunadi . ' İsimli Ürünün Miktarı Yetersiz!', 'type' => 'error'));
                                    }
                                }else{
                                    if (intval($stok->BAKIYE) < intval($miktar[$i])) {
                                        DB::rollBack();
                                        Input::flash();
                                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Stok Bakiyesi Yetersiz!', 'text' => $subeurun->urunadi . ' İsimli Ürünün Miktarı Yetersiz!', 'type' => 'error'));
                                    }
                                }
                            }
                            $secilenler .= ($secilenler == "" ? "" : ",") . $urun[$i];
                            $miktarlar .= ($miktarlar == "" ? "" : ",") . $miktar[$i];
                            $depokodlari .= ($depokodlari == "" ? "" : ",") . $depokodu[$i];
                            $stokkodlari .= ($stokkodlari == "" ? "" : ",") . $subeurun->netsisstokkod->kodu;
                            $muhkodlari .= ($muhkodlari == "" ? "" : ",") . '999-001';
                        } else {
                            DB::rollBack();
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Stok Durumu Eşleştirilmemiş!', 'text' => ($i + 1) . '. Sıradaki Ürün, Netsis Stok Kodu ile Eşleştirilmemiş!', 'type' => 'error'));
                        }
                    }
                }
            } catch (Exception $e) {
                Log::error($e);
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ürün Eklemede Hata Oluştu', 'text' => 'Kalem Olarak Eklenen Ürünlerin Bilgilerinde Hata Var!', 'type' => 'error'));
            }
            $kalemler = BackendController::StokHareketGrupla($secilenler, $miktarlar, $depokodlari, $stokkodlari,$muhkodlari);
            if (count($kalemler) == 0) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Kaydedilemedi', 'text' => 'Stok ile İlgili Kalemlerde Hata Var', 'type' => 'error'));
            }

            $stokgiriscikis->subekodu = $subekodu;
            $stokgiriscikis->tarih = $tarih;
            $stokgiriscikis->gckod = $gckod;
            $stokgiriscikis->secilenler = $secilenler;
            $stokgiriscikis->adet = $miktarlar;
            $stokgiriscikis->depokodu = $depokodlari;
            $stokgiriscikis->stokkodu = $stokkodlari;
            $stokgiriscikis->muhkodu = $muhkodlari;
            $stokgiriscikis->harekettur = $harekettur;
            $stokgiriscikis->masrafkodu = $masraf;
            $stokgiriscikis->projekodu = $subeyetkili->projekodu;
            $stokgiriscikis->aciklama = $aciklama;
            $stokgiriscikis->aciklama2 = $aciklama2;
            $stokgiriscikis->aciklama3 = $aciklama3;
            $stokgiriscikis->tarih = date('Y-m-d H:i:s');
            $stokgiriscikis->servis_id = $this->servisid;
            $stokgiriscikis->kullanici_id = Auth::user()->id;
            $stokgiriscikis->durum = 1;
            $stokgiriscikis->save();
            $stokhareket = BackendController::NetsisSubeStokHareketDuzenle($stokgiriscikis, $kalemler);
            if ($stokhareket['durum'] == '0') {
                DB::rollBack();
                Input::flash();
                $kalemler = BackendController::StokHareketGrupla($eskibilgi->secilenler, $eskibilgi->adet, $eskibilgi->depokodu, $eskibilgi->stokkodu,$eskibilgi->muhkodu);
                BackendController::NetsisSubeStokHareketGerial($eskibilgi, $kalemler);
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Kaydedilemedi', 'text' => $stokhareket['text'], 'type' => 'error'));
            }
            $stokgiriscikis->inckeyno = $stokhareket['inckeylist'];
            $stokgiriscikis->save();
            BackendController::IslemEkle(2,Auth::user()->id,'label-warning','fa-edit', $sube->adi . ' için Stok Hareketi Güncellendi.', 'Güncelleyen:'.Auth::user()->adi_soyadi.',Stok Hareket Numarası:' . $stokgiriscikis->id);
            DB::commit();
            return Redirect::to($this->servisadi . '/stokhareket')->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Güncellendi', 'text' => 'Stok Hareketi Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Input::flash();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Güncellenemedi', 'text' => 'Stok Hareketi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getStokhareketsil($id)
    {
        try {
            DB::beginTransaction();
            $stokgiriscikis = StokGirisCikis::find($id);
            $eskibilgi = clone $stokgiriscikis;
            if($stokgiriscikis){
                $sube = Sube::where('subekodu', $stokgiriscikis->subekodu)->where('aktif', 1)->first();
                if (!$sube) {
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Şube Hatası', 'text' => 'Şube Bilgisi Bulunamadı!', 'type' => 'error'));
                }
                $stokhareket=BackendController::NetsisSubeStokHareketSil($stokgiriscikis->id);
                if ($stokhareket['durum'] == '0') {
                    $silmedurum = BackendController::NetsisSubeStokHareketTemizle($stokgiriscikis);
                    DB::rollBack();
                    Input::flash();
                    $kalemler = BackendController::StokHareketGrupla($eskibilgi->secilenler, $eskibilgi->adet, $eskibilgi->depokodu, $eskibilgi->stokkodu,$eskibilgi->muhkodu);
                    $eklemestokhareket =BackendController::NetsisSubeStokHareket($eskibilgi, $kalemler);
                    if ($eklemestokhareket['durum'] != '0') {
                        $stokgiriscikis->inckeyno = $eklemestokhareket['inckeylist'];
                        $stokgiriscikis->save();
                        DB::commit();
                    }
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Silinemedi', 'text' => $stokhareket['text'].($silmedurum['durum']=='0' ? $silmedurum['text'] : ''), 'type' => 'error'));
                }
                $stokgiriscikis->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-pencil', $sube->adi . '  Ait ' . $id . ' Numaralı Stok Hareketi Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Stok Hareket Numarası:' . $id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Silindi', 'text' => 'Stok Hareketi Başarıyla Silindi.', 'type' => 'success'));

            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Silinemedi', 'text' => 'Stok Hareketi Bulunamadı!', 'type' => 'error'));
            }
        } catch (Exception $e){
            DB::rollBack();
            Input::flash();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Silinemedi', 'text' => 'Stok Hareketi Silinirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function postStokhareket()
    {
        try {
            $id = Input::get('stokhareket');
            $netsiscariid=Auth::user()->netsiscari_id;
            $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
            $stokgiriscikis=StokGirisCikis::find($id);
            if(!$stokgiriscikis)
                return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Stok Hareketi Bulunamadı', 'type' => 'warning', 'title' => 'Stok Hareket Bilgi Hatası'));
            if(!$sube)
                return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Stok Hareket Bilgisi Çıkarma Yetkiniz Yok', 'type' => 'warning', 'title' => 'Stok Hareket Bilgi Hatası'));

            $raporadi = "StokHareket-" . Str::slug($sube->adi);
            $export = "pdf";
            $kriterler = array();
            $kriterler['id'] = $id;
            JasperPHP::process(public_path('reports/stokhareket/stokhareket.jasper'), public_path('reports/outputs/stokhareket/' . $raporadi),
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
            readfile("reports/outputs/stokhareket/" . $raporadi . "." . $export . "");
            File::delete("reports/outputs/stokhareket/" . $raporadi . "." . $export . "");

        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Stok Hareketi Bulunamadı', 'type' => 'warning', 'title' => 'Stok Hareket Bilgi Hatası'));
        }
    }

    public function getStokhareketgoster($id)
    {
        $stokgiriscikis = StokGirisCikis::find($id);
        $stokgiriscikis->masraf = Masraf::where('MKOD', $stokgiriscikis->masrafkodu)->first(array('MKOD', DB::raw('dbo.TRK(ACIKLAMA) as ACIKLAMA')));

        $urunlist = explode(',', $stokgiriscikis->secilenler);
        $secilenler = array();
        for ($i = 0; $i < count($urunlist); $i++) {
            $subeurun = SubeUrun::find($urunlist[$i]);
            $subeurun->netsisstokkod = NetsisStokKod::find($subeurun->netsisstokkod_id);
            array_push($secilenler, $subeurun);
        }
        $stokgiriscikis->urunler = $secilenler;
        $stokgiriscikis->adetler = explode(',', $stokgiriscikis->adet);
        $stokgiriscikis->depokodlari = explode(',', $stokgiriscikis->depokodu);
        return View::make($this->servisadi . '.stokhareketgoster', array('stokgiriscikis' => $stokgiriscikis))->with(array('title' => $this->servisbilgi . ' Stok Hareketi Bilgi Ekranı'));

    }

}
