<?php
//transaction işlemi tamamlandı
class ElkdatabaseController extends BackendController {
    public $servisadi = 'elkdatabase';
    public $servisid = 2;
    public $servisbilgi = 'Elektrik';

    public function getSayactip() {
        return View::make($this->servisadi.'.sayactip')->with(array('title'=>$this->servisbilgi.' Sayaç Tipleri'));
    }

    public function postSayactiplist() {
        $query = SayacTip::where('sayactur_id',$this->servisid)
            ->select(array("sayactip.id","sayacmarka.marka","sayactip.tipadi","sayacmarka.nmarka","sayactip.ntipadi"))
            ->leftjoin("sayacmarka", "sayactip.sayacmarka_id", "=", "sayacmarka.id");
        return Datatables::of($query)
            ->addColumn('islemler',function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/sayactipduzenle/".$model->id."'> Düzenle </a>";
            })
            ->make(true);
    }

    public function getSayactipekle() {
        $markalar = SayacMarka::all();
        return View::make($this->servisadi.'.sayactipekle',array('markalar'=>$markalar))->with(array('title'=>$this->servisbilgi.' Sayaç Tipi Ekle'));
    }

    public function postSayactipekle() {
        try {
            $rules = ['tip' => 'required', 'marka' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $sayactip = new SayacTip;
            $tip = Input::get('tip');
            $marka = Input::get('marka');
            $tur = $this->servisid;
            if(SayacTip::where('tipadi',$tip)->where('sayacmarka_id',$marka)->where('sayactur_id',$tur)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Sayaç Tipi Tanımı Zaten Mevcut!', 'type' => 'warning'));
            }
            $sayactip->tipadi = $tip;
            $sayactip->sayacmarka_id = $marka;
            $sayactip->sayactur_id = $tur;
            $sayactip->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $tip . ' İsimli Sayaç Tipi Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Sayaç Tip Numarası:' . $sayactip->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/sayactip')->with(array('mesaj' => 'true', 'title' => 'Sayac Tipi Eklendi', 'text' => 'Sayac Tipi Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Tipi Eklenemedi', 'text' => 'Sayac Tipi Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getSayactipduzenle($id) {
        $sayactip = SayacTip::find($id);
        $markalar = SayacMarka::all();
        return View::make($this->servisadi.'.sayactipduzenle',array('sayactip'=>$sayactip,'markalar'=>$markalar))->with(array('title'=>$this->servisbilgi.' Sayaç Tipi Düzenleme Ekranı'));
    }

    public function postSayactipduzenle($id) {
        try {
            $rules = ['tip' => 'required', 'marka' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $sayactip = SayacTip::find($id);
            $bilgi = clone $sayactip;
            $tip = Input::get('tip');
            $marka = Input::get('marka');
            if(SayacTip::where('tipadi',$tip)->where('sayacmarka_id',$marka)->where('sayactur_id',$this->servisid)->where('id','<>',$id)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Sayaç Tipi Tanımı Zaten Mevcut!', 'type' => 'warning'));
            }
            $sayactip->tipadi = $tip;
            $sayactip->sayacmarka_id = $marka;
            $sayactip->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $bilgi->tipadi . ' İsimli Sayaç Tipi Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Sayaç Tip Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/sayactip')->with(array('mesaj' => 'true', 'title' => 'Sayac Tipi Güncellendi', 'text' => 'Sayac Tipi Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Tipi Güncellenemedi', 'text' => 'Sayac Tipi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getSayactipsil($id){
        try {
            DB::beginTransaction();
            $sayactip = SayacTip::find($id);
            if($sayactip){
                $bilgi = clone $sayactip;
                $sayacadi = SayacAdi::where('sayactip_id', $id)->first();
                if ($sayacadi) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Tipi Silinemez', 'text' => 'Sayaç Tipi Sayaç İsimlerinde Kullanılmış.', 'type' => 'error'));
                }
                $sayactip->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $bilgi->tipadi . ' İsimli Sayaç Tipi Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Sayaç Tip Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Tipi Silindi', 'text' => 'Sayaç Tipi Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Tipi Silinemedi', 'text' => 'Sayaç Tipi Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Tipi Silinemedi', 'text' => 'Sayaç Tipi Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getSayacadi() {
        return View::make($this->servisadi.'.sayacadi')->with(array('title'=>$this->servisbilgi.' Sayaç İsimleri'));
    }

    public function postSayacadilist() {
        $query = SayacAdi::where('sayacadi.sayactur_id',$this->servisid)
            ->select(array("sayacadi.id","sayacadi.sayacadi","sayacmarka.marka","sayactip.tipadi","sayacadi.nsayacadi","sayacmarka.nmarka","sayactip.ntipadi"))
            ->leftjoin("sayactip", "sayacadi.sayactip_id", "=", "sayactip.id")
            ->leftjoin("sayacmarka", "sayactip.sayacmarka_id", "=", "sayacmarka.id");
        return Datatables::of($query)
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/sayacadiduzenle/".$model->id."'> Düzenle </a>".
                "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getSayacadiekle() {
        $sayactipleri = SayacTip::where('sayactur_id',$this->servisid)->get();
        return View::make($this->servisadi.'.sayacadiekle',array('sayactipleri'=>$sayactipleri))->with(array('title'=>$this->servisbilgi.' Sayaç Tipi Ekle'));
    }

    public function postSayacadiekle() {
        try {
            $rules = ['adi' => 'required|unique:sayacadi,sayacadi', 'sayactip' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $sayacadi = new SayacAdi;
            $adi = Input::get('adi');
            $tip = Input::get('sayactip');
            $tur = $this->servisid;
            $cap = 0;
            if(SayacAdi::where('sayacadi',$adi)->where('sayactur_id',$tur)->where('sayactip_id',$tip)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Sayaç Adı Tanımı Zaten Mevcut!', 'type' => 'warning'));
            }
            $sayacadi->sayacadi = $adi;
            $sayacadi->sayactur_id = $tur;
            $sayacadi->sayactip_id = $tip;
            $sayacadi->cap = $cap;
            $sayacadi->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $adi . ' İsimli Sayaç Adı Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Sayaç Adı Numarası:' . $sayacadi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/sayacadi')->with(array('mesaj' => 'true', 'title' => 'Sayac Adı Eklendi', 'text' => 'Sayac Adı Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Adı Eklenemedi', 'text' => 'Sayac Adı Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getSayacadiduzenle($id) {
        $sayacadi = SayacAdi::find($id);
        $sayactipleri = SayacTip::where('sayactur_id',$this->servisid)->get();
        return View::make($this->servisadi.'.sayacadiduzenle',array('sayacadi'=>$sayacadi,'sayactipleri'=>$sayactipleri))->with(array('title'=>$this->servisbilgi.' Sayaç Adı Düzenleme Ekranı'));
    }

    public function postSayacadiduzenle($id) {
        try {
            $rules = ['adi' => 'required|unique:sayacadi,sayacadi,' . $id, 'sayactip' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $sayacadi = SayacAdi::find($id);
            $bilgi = clone $sayacadi;
            $adi = Input::get('adi');
            $tip = Input::get('sayactip');
            $cap = 0;
            if(SayacAdi::where('sayacadi',$adi)->where('sayactur_id',$this->servisid)->where('sayactip_id',$tip)->where('id','<>',$id)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Sayaç Adı Tanımı Zaten Mevcut!', 'type' => 'warning'));
            }
            $sayacadi->sayacadi = $adi;
            $sayacadi->sayactip_id = $tip;
            $sayacadi->cap = $cap;
            $sayacadi->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $bilgi->sayacadi . ' İsimli Sayaç Adı Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Sayaç Adı Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/sayacadi')->with(array('mesaj' => 'true', 'title' => 'Sayac Adı Güncellendi', 'text' => 'Sayac Adı Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Adı Güncellenemedi', 'text' => 'Sayac Adı Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getSayacadisil($id){
        try {
            DB::beginTransaction();
            $sayacadi = SayacAdi::find($id);
            if($sayacadi){
                $bilgi = clone $sayacadi;
                $arizakayit = ArizaKayit::where('sayacadi_id', $id)->first();
                if ($arizakayit) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Silinemez', 'text' => 'Sayaç Adı Arıza Kayıdında Kullanılmış.', 'type' => 'error'));
                }
                $sayacadi->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $bilgi->sayacadi . ' İsimli Sayaç Adı Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Sayaç Adı Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Silindi', 'text' => 'Sayaç Adı Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Silinemedi', 'text' => 'Sayaç Adı Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Silinemedi', 'text' => 'Sayaç Adı Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getArizalar() {
        return View::make($this->servisadi.'.arizalar')->with(array('title'=>$this->servisbilgi.' Sayaç Arızaları'));
    }

    public function postArizalist() {
        $query = ArizaKod::where('arizakod.sayactur_id',$this->servisid)
            ->select(array("arizakod.id","arizakod.kod","arizakod.tanim","arizakod.ggaranti","arizakod.ntanim","arizakod.nkod","arizakod.ngaranti"));
        return Datatables::of($query)
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/arizaduzenle/".$model->id."'> Düzenle </a>".
                "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getArizaekle() {
        return View::make($this->servisadi.'.arizaekle')->with(array('title'=>$this->servisbilgi.' Sayaç Arızası Ekle'));
    }

    public function postArizaekle() {
        try {
            $rules = ['kod' => 'required', 'tanim' => 'required', 'garanti' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $ariza = new ArizaKod;
            $kod = Input::get('kod');
            $tanim = Input::get('tanim');
            $garanti = Input::get('garanti');
            $tur = $this->servisid;
            if(ArizaKod::where('kod',$kod)->where('tanim',$tanim)->where('sayactur_id',$tur)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Arıza Nedeni Tanımı Zaten Mevcut!', 'type' => 'warning'));
            }
            $ariza->kod = $kod;
            $ariza->tanim = $tanim;
            $ariza->sayactur_id = $tur;
            $ariza->garanti = $garanti;
            $ariza->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $tanim . ' Tanımlı Arıza Nedeni Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Arıza Nedeni Numarası:' . $ariza->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/arizalar')->with(array('mesaj' => 'true', 'title' => 'Sayaç Arıza Tanımı Eklendi', 'text' => 'Sayaç Arıza Tanımı Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Arıza Tanımı Eklenemedi', 'text' => 'Sayaç Arıza Tanımı Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getArizaduzenle($id) {
        $ariza = ArizaKod::find($id);
        return View::make($this->servisadi.'.arizaduzenle',array('ariza'=>$ariza))->with(array('title'=>$this->servisbilgi.' Sayaç Arızası Düzenleme Ekranı'));
    }

    public function postArizaduzenle($id) {
        try {
            $rules = ['kod' => 'required', 'tanim' => 'required', 'garanti' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $ariza = ArizaKod::find($id);
            $bilgi = clone $ariza;
            $kod = Input::get('kod');
            $tanim = Input::get('tanim');
            $garanti = Input::get('garanti');
            if(ArizaKod::where('kod',$kod)->where('tanim',$tanim)->where('sayactur_id',$this->servisid)->where('id','<>',$id)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Arıza Nedeni Tanımı Zaten Mevcut!', 'type' => 'warning'));
            }
            $ariza->kod = $kod;
            $ariza->tanim = $tanim;
            $ariza->garanti = $garanti;
            $ariza->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $bilgi->tanim . ' Tanımlı Arıza Nedeni Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Arıza Nedeni Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/arizalar')->with(array('mesaj' => 'true', 'title' => 'Sayac Arıza Tanımı Güncellendi', 'text' => 'Sayac Arıza Tanımı Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Arıza Tanımı Güncellenemedi', 'text' => 'Sayac Arıza Tanımı Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getArizasil($id){
        try {
            DB::beginTransaction();
            $ariza = ArizaKod::find($id);
            if($ariza){
                $bilgi = clone $ariza;
                if ($ariza->kullanim > 0) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Arıza Tanımı Silinemez', 'text' => 'Arıza Tanımı Arıza Kayıdında Kullanılmış.', 'type' => 'error'));
                }
                $ariza->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $bilgi->tanim . ' Tanımlı Arıza Nedeni Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Arıza Nedeni Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Arıza Tanımı Silindi', 'text' => 'Sayaç Arıza Tanımı Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Arıza Tanımı Silinemedi', 'text' => 'Sayaç Arıza Tanımı Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Arıza Tanımı Silinemedi', 'text' => 'Sayaç Arıza Tanımı Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getYapilanlar() {
        $yapilanlar = Yapilanlar::where('sayactur_id',$this->servisid)->get();
        return View::make($this->servisadi.'.yapilanlar',array('yapilanlar'=>$yapilanlar))->with(array('title'=>$this->servisbilgi.' Sayaçları Yapılan İşlemler'));
    }

    public function postYapilanlarlist() {
        $query = Yapilanlar::where('yapilanlar.sayactur_id',$this->servisid)
            ->select(array("yapilanlar.id","yapilanlar.tanim","yapilanlar.gdurum","yapilanlar.ntanim","yapilanlar.ndurum"));
        return Datatables::of($query)
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/yapilanduzenle/".$model->id."'> Düzenle </a>".
                "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getYapilanekle() {
        return View::make($this->servisadi.'.yapilanekle')->with(array('title'=>$this->servisbilgi.' Sayaç Yapılan İşlem Ekle'));
    }

    public function postYapilanekle() {
        try {
            $rules = ['tanim' => 'required', 'durum' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $yapilan = new Yapilanlar;
            $tanim = Input::get('tanim');
            $durum = Input::get('durum');
            $tur = $this->servisid;
            if(Yapilanlar::where('tanim',$tanim)->where('sayactur_id',$tur)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Yapılan İşlem Tanımı Zaten Mevcut!', 'type' => 'warning'));
            }
            $yapilan->tanim = $tanim;
            $yapilan->sayactur_id = $tur;
            $yapilan->durum = $durum;
            $yapilan->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $tanim . ' Tanımlı Yapılan İşlem Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Yapılan İşlem Numarası:' . $yapilan->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/yapilanlar')->with(array('mesaj' => 'true', 'title' => 'Sayaç Yapılan İşlem Tanımı Eklendi', 'text' => 'Sayaç Yapılan İşlem Tanımı Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Yapılan İşlem Tanımı Eklenemedi', 'text' => 'Sayaç Yapılan İşlem Tanımı Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getYapilanduzenle($id) {
        $yapilan = Yapilanlar::find($id);
        return View::make($this->servisadi.'.yapilanduzenle',array('yapilan'=>$yapilan))->with(array('title'=>$this->servisbilgi.' Sayaç Yapılan İşlem Düzenleme Ekranı'));
    }

    public function postYapilanduzenle($id) {
        try {
            $rules = ['tanim' => 'required', 'durum' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $yapilan = Yapilanlar::find($id);
            $bilgi = clone $yapilan;
            $tanim = Input::get('tanim');
            $durum = Input::get('durum');
            if(Yapilanlar::where('tanim',$tanim)->where('sayactur_id',$this->servisid)->where('id','<>',$id)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Yapılan İşlem Tanımı Zaten Mevcut!', 'type' => 'warning'));
            }
            $yapilan->tanim = $tanim;
            $yapilan->durum = $durum;
            $yapilan->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $bilgi->tanim . ' Tanımlı Yapılan İşlem Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Yapılan İşlem Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/yapilanlar')->with(array('mesaj' => 'true', 'title' => 'Sayac Yapılan İşlem Tanımı Güncellendi', 'text' => 'Sayac Yapılan İşlem Tanımı Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Yapılan İşlem Tanımı Güncellenemedi', 'text' => 'Sayac Yapılan İşlem Tanımı Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getYapilansil($id){
        try {
            DB::beginTransaction();
            $yapilan = Yapilanlar::find($id);
            if($yapilan){
                $bilgi = clone $yapilan;
                if ($yapilan->kullanim > 0) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Yapılan İşlem Tanımı Silinemez', 'text' => 'Yapılan İşlem Arıza Kayıdında Kullanılmış.', 'type' => 'error'));
                }
                $yapilan->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $bilgi->tanim . ' Tanımlı Yapılan İşlem Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Yapılan İşlem Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Yapılan İşlem Tanımı Silindi', 'text' => 'Sayaç Yapılan İşlem Tanımı Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Yapılan İşlem Tanımı Silinemedi', 'text' => 'Sayaç Yapılan İşlem Tanımı Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Yapılan İşlem Tanımı Silinemedi', 'text' => 'Sayaç Yapılan İşlem Tanımı Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getDegisenler() {
        return View::make($this->servisadi.'.degisenler')->with(array('title'=>$this->servisbilgi.' Sayaçları Değişen Parçalar'));
    }

    public function postDegisenlerlist() {
        $query = Degisenler::where('degisenler.sayactur_id',$this->servisid)
            ->select(array("degisenler.id","degisenler.tanim","degisenler.gparcadurum","degisenler.gstokkontrol","degisenler.gsabit","degisenler.ntanim",
                "degisenler.nparcadurum","degisenler.nstokkontrol","degisenler.nsabit"));
        return Datatables::of($query)
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/degisenduzenle/".$model->id."'> Düzenle </a>".
                "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getDegisenekle() {
        $parcalar = Degisenler::where('sayactur_id',$this->servisid)->where('parcadurum',2)->get();
        return View::make($this->servisadi.'.degisenekle',array('parcalar'=>$parcalar))->with(array('title'=>$this->servisbilgi.' Sayaç Değişen Parça Ekle'));
    }

    public function postDegisenekle() {
        try {
            $rules = ['tanim' => 'required', 'parcadurum' => 'required', 'stokkontrol' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $degisen = new Degisenler;
            $tanim = Input::get('tanim');
            $parcadurum = Input::get('parcadurum');
            $stokkontrol = Input::get('stokkontrol');
            $sabit = Input::get('durum');
            if (Input::has('parca')) {
                $parcalargelen = Input::get('parca');
                $parcalar = "";
                foreach ($parcalargelen as $parca) {
                    $parcalar .= ($parcalar == "" ? "" : ",") . $parca;
                }
                $degisen->parcalar = $parcalar;
            }
            $tur = $this->servisid;
            if(Degisenler::where('tanim',$tanim)->where('sayactur_id',$tur)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Değişen Parça Tanımı Zaten Mevcut!', 'type' => 'warning'));
            }
            $degisen->tanim = $tanim;
            $degisen->sayactur_id = $tur;
            $degisen->parcadurum = $parcadurum;
            $degisen->stokkontrol = $stokkontrol;
            $degisen->sabit = $sabit;
            $degisen->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $tanim . ' Tanımlı Değişen Parça Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Değişen Parça Numarası:' . $degisen->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/degisenler')->with(array('mesaj' => 'true', 'title' => 'Değişen Parça Tanımı Eklendi', 'text' => 'Sayaç Değişen Parça Tanımı Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Değişen Parça Tanımı Eklenemedi', 'text' => 'Sayaç Değişen Parça Tanımı Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getDegisenduzenle($id) {
        $degisen = Degisenler::find($id);
        $parcalar = Degisenler::where('sayactur_id',$this->servisid)->where('parcadurum',2)->get();
        return View::make($this->servisadi.'.degisenduzenle',array('degisen'=>$degisen,'parcalar'=>$parcalar))->with(array('title'=>$this->servisbilgi.' Sayaç Değişen Parça Düzenleme Ekranı'));
    }

    public function postDegisenduzenle($id) {
        try {
            $rules = ['tanim' => 'required', 'parcadurum' => 'required', 'stokkontrol' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $degisen = Degisenler::find($id);
            $bilgi = clone $degisen;
            $tanim = Input::get('tanim');
            $parcadurum = Input::get('parcadurum');
            $stokkontrol = Input::get('stokkontrol');
            $sabit = Input::get('durum');
            if(Degisenler::where('tanim',$tanim)->where('sayactur_id',$this->servisid)->where('id','<>',$id)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Değişen Parça Tanımı Zaten Mevcut!', 'type' => 'warning'));
            }
            if (Input::has('parca')) {
                $parcalargelen = Input::get('parca');
                $parcalar = "";
                foreach ($parcalargelen as $parca) {
                    $parcalar .= ($parcalar == "" ? "" : ",") . $parca;
                }
                $degisen->parcalar = $parcalar;
            }
            $degisen->tanim = $tanim;
            $degisen->parcadurum = $parcadurum;
            $degisen->stokkontrol = $stokkontrol;
            $degisen->sabit = $sabit;
            $degisen->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $bilgi->tanim . ' Tanımlı Değişen Parça Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Değişen Parça Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/degisenler')->with(array('mesaj' => 'true', 'title' => 'Değişen Parça Tanımı Güncellendi', 'text' => 'Sayac Değişen Parça Tanımı Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Değişen Parça Tanımı Güncellenemedi', 'text' => 'Sayac Değişen Parça Tanımı Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getDegisensil($id){
        try {
            DB::beginTransaction();
            $degisen = Degisenler::find($id);
            if($degisen){
                $bilgi = clone $degisen;
                if ($degisen->kullanim > 0) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Değişen Parça Tanımı Silinemez', 'text' => 'Değişen Parça Arıza Kayıdında Kullanılmış.', 'type' => 'error'));
                }
                $degisen->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $bilgi->tanim . ' Tanımlı Değişen Parça Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Değişen Parça Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Değişen Parça Tanımı Silindi', 'text' => 'Sayaç Değişen Parça Tanımı Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Değişen Parça Tanımı Silinemedi', 'text' => 'Sayaç Değişen Parça Tanımı Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Değişen Parça Tanımı Silinemedi', 'text' => 'Sayaç Değişen Parça Tanımı Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getSayacfiyat() {
        return View::make($this->servisadi.'.sayacfiyat')->with(array('title'=>$this->servisbilgi.' Sayaç Fiyatları'));
    }

    public function postSayacfiyatlist() {
        $query = SayacFiyat::where('sayacfiyat.sayactur_id',$this->servisid)
            ->select(array("sayacfiyat.id","uretimyer.yeradi","sayacadi.sayacadi","sayacfiyat.fiyat","uretimyer.nyeradi","sayacadi.nsayacadi","parabirimi.birimi"))
            ->leftjoin("uretimyer","sayacfiyat.uretimyer_id","=","uretimyer.id")
            ->leftjoin("sayacadi","sayacfiyat.sayacadi_id","=","sayacadi.id")
            ->leftjoin("parabirimi","sayacfiyat.parabirimi_id","=","parabirimi.id");
        return Datatables::of($query)
            ->editColumn('fiyat', function ($model) {
                return $model->fiyat." ".$model->birimi;
            })
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/fiyatduzenle/".$model->id."'> Düzenle </a>".
                "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getFiyatekle() {
        $uretimyerleri = UretimYer::where('mekanik',0)->get();
        $sayacadlari = SayacAdi::where('sayactur_id',$this->servisid)->get();
        return View::make($this->servisadi.'.fiyatekle',array('uretimyerleri'=>$uretimyerleri,'sayacadlari'=>$sayacadlari))->with(array('title'=>$this->servisbilgi.' Sayaç Fiyatı Ekle'));
    }

    public function postFiyatekle() {
        try {
            $rules = ['fiyat' => 'required', 'uretimyer' => 'required', 'sayacadi' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $sayacfiyat = new SayacFiyat;
            $fiyat = doubleval(Input::get('fiyat'));
            $yer = Input::get('uretimyer');
            $sayacadi_id = Input::get('sayacadi');
            $sayacadi = SayacAdi::find($sayacadi_id);
            $cap = 1;
            If (SayacFiyat::where('uretimyer_id', $yer)->where('sayacadi_id', $sayacadi_id)->where('sayaccap_id', $cap)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu sayacın seçilen üretim yerine ait fiyat mevcut.', 'type' => 'warning'));
            }
            $tur = $this->servisid;
            $sayacfiyat->fiyat = $fiyat;
            $sayacfiyat->uretimyer_id = $yer;
            $sayacfiyat->sayacadi_id = $sayacadi_id;
            $sayacfiyat->sayaccap_id = $cap;
            $sayacfiyat->sayactur_id = $tur;
            $sayacfiyat->parabirimi_id = $sayacfiyat->uretimyer->parabirimi_id;
            $sayacfiyat->kullanici_id = Auth::user()->id;
            $sayacfiyat->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $sayacadi->sayacadi . ' Tanımlı Sayaç Adı İçin Fiyat Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Sayaç Fiyat Numarası:' . $sayacfiyat->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/sayacfiyat')->with(array('mesaj' => 'true', 'title' => 'Sayaç Fiyatı Eklendi', 'text' => 'Sayaç Fiyatı Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Fiyatı Eklenemedi', 'text' => 'Sayaç Fiyatı Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getFiyatduzenle($id) {
        $uretimyerleri = UretimYer::where('mekanik',0)->get();
        $sayacadlari = SayacAdi::where('sayactur_id',$this->servisid)->get();
        $sayacfiyat = SayacFiyat::find($id);
        return View::make($this->servisadi.'.fiyatduzenle',array('sayacfiyat'=>$sayacfiyat,'uretimyerleri'=>$uretimyerleri,'sayacadlari'=>$sayacadlari))->with(array('title'=>$this->servisbilgi.' Sayaç Fiyatı Düzenleme Ekranı'));
    }

    public function postFiyatduzenle($id) {
        try {
            $rules = ['fiyat' => 'required', 'uretimyer' => 'required', 'sayacadi' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $sayacfiyat = SayacFiyat::find($id);
            $bilgi = clone $sayacfiyat;
            $fiyat = doubleval(Input::get('fiyat'));
            $yer = Input::get('uretimyer');
            $sayacadi_id = Input::get('sayacadi');
            $sayacadi = SayacAdi::find($sayacadi_id);
            $cap = 1;
            If (SayacFiyat::where('uretimyer_id', $yer)->where('sayacadi_id', $sayacadi_id)->where('sayaccap_id', $cap)->where('id', '<>', $id)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu sayacın seçilen üretim yerine ait fiyat mevcut.', 'type' => 'warning'));
            }
            $sayacfiyat->fiyat = $fiyat;
            $sayacfiyat->uretimyer_id = $yer;
            $sayacfiyat->sayacadi_id = $sayacadi_id;
            $sayacfiyat->sayaccap_id = $cap;
            $sayacfiyat->parabirimi_id = $sayacfiyat->uretimyer->parabirimi_id;
            $sayacfiyat->kullanici_id = Auth::user()->id;
            $sayacfiyat->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $sayacadi->sayacadi . ' Tanımlı Sayaç Adı İçin Fiyat Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Sayaç Fiyat Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/sayacfiyat')->with(array('mesaj' => 'true', 'title' => 'Sayac Fiyatı Güncellendi', 'text' => 'Sayac Fiyatı Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Fiyatı Güncellenemedi', 'text' => 'Sayac Fiyatı Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getFiyatsil($id){
        try {
            DB::beginTransaction();
            $sayacfiyat = SayacFiyat::find($id);
            if($sayacfiyat){
                $sayacadi = SayacAdi::find($sayacfiyat->sayacadi_id);
                $bilgi = clone $sayacfiyat;
                $sayacfiyat->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $sayacadi->sayacadi . ' Tanımlı Sayaç Adı İçin Fiyat Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Sayaç Fiyat Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Fiyatı Silindi', 'text' => 'Sayaç Fiyatı Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Fiyatı Silinemedi', 'text' => 'Sayaç Fiyatı Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Fiyatı Silinemedi', 'text' => 'Sayaç Fiyatı Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getSayacparca() {
        return View::make($this->servisadi.'.sayacparca')->with(array('title'=>$this->servisbilgi.' Sayaç Parçaları'));
    }

    public function postSayacparcalist() {
        $query = SayacParca::where('sayacparca.sayactur_id',$this->servisid)
            ->select(array("sayacparca.id","sayacadi.sayacadi","servisstokkod.stokkodu","sayacparca.parcasayi","sayacadi.nsayacadi"))
            ->leftjoin("sayacadi","sayacparca.sayacadi_id","=","sayacadi.id")
            ->leftJoin("servisstokkod","sayacparca.servisstokkod_id","=","servisstokkod.id");
        return Datatables::of($query)
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/parcaduzenle/".$model->id."'> Düzenle </a>".
                "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getParcaekle() {
        $sayacadlari = SayacAdi::where('sayactur_id',$this->servisid)->get();
        $parcalar = Degisenler::where('sayactur_id',$this->servisid)->where('parcadurum','<>',2)->get();
        $stokkodlari=ServisStokKod::where('servisid',$this->servisid)->where('koddurum',1)->get();
        return View::make($this->servisadi.'.parcaekle',array('sayacadlari'=>$sayacadlari,'parcalar'=>$parcalar,'stokkodlari'=>$stokkodlari))->with(array('title'=>$this->servisbilgi.' Sayaç Parçası Ekle'));
    }

    public function postParcaekle() {
        try {
            $rules = ['sayacadi' => 'required', 'serviskod' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $sayacparca = new SayacParca;
            $sayacadi_id = Input::get('sayacadi');
            $stokkod = Input::get('serviskod');
            $sayacadi = SayacAdi::find($sayacadi_id);
            $cap = 1;
            If (SayacParca::where('sayacadi_id', $sayacadi_id)->where('sayaccap_id', $cap)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu sayacın parçaları mevcut.', 'type' => 'warning'));
            }
            if (Input::has('parca')) {
                $parcalargelen = Input::get('parca');
                $parcalar = "";
                foreach ($parcalargelen as $parca) {
                    $parcalar .= ($parcalar == "" ? "" : ",") . $parca;
                }
                $sayacparca->parcasayi = count($parcalargelen);
                $sayacparca->parcalar = $parcalar;
            }
            $tur = $this->servisid;
            $sayacparca->sayacadi_id = $sayacadi_id;
            $sayacparca->sayaccap_id = $cap;
            $sayacparca->servisstokkod_id = $stokkod;
            $sayacparca->sayactur_id = $tur;
            $sayacparca->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $sayacadi->sayacadi . ' Tanımlı Sayaç Adına Ait Parçalar Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Sayaç Parça Numarası:' . $sayacparca->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/sayacparca')->with(array('mesaj' => 'true', 'title' => 'Sayaç Parçaları Eklendi', 'text' => 'Sayaç Parçaları Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Parçaları Eklenemedi', 'text' => 'Sayaç Parçaları Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getParcaduzenle($id) {
        $sayacadlari = SayacAdi::where('sayactur_id',$this->servisid)->get();
        $parcalar = Degisenler::where('sayactur_id',$this->servisid)->where('parcadurum','<>',2)->get();
        $sayacparca = SayacParca::find($id);
        $stokkodlari=ServisStokKod::where('servisid',$this->servisid)->where('koddurum',1)->get();
        return View::make($this->servisadi.'.parcaduzenle',array('sayacparca'=>$sayacparca,'parcalar'=>$parcalar,'sayacadlari'=>$sayacadlari,'stokkodlari'=>$stokkodlari))->with(array('title'=>$this->servisbilgi.' Sayaç Parçaları Düzenleme Ekranı'));
    }

    public function postParcaduzenle($id) {
        try {
            $rules = ['sayacadi' => 'required', 'serviskod' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $sayacparca = SayacParca::find($id);
            $bilgi = clone $sayacparca;
            $sayacadi_id = Input::get('sayacadi');
            $stokkod = Input::get('serviskod');
            $sayacadi = SayacAdi::find($sayacadi_id);
            $cap = 1;
            If (SayacParca::where('sayacadi_id', $sayacadi_id)->where('sayaccap_id', $cap)->where('id', '<>', $id)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu sayacın parçaları mevcut.', 'type' => 'warning'));
            }
            if (Input::has('parca')) {
                $parcalargelen = Input::get('parca');
                $parcalar = "";
                foreach ($parcalargelen as $parca) {
                    $parcalar .= ($parcalar == "" ? "" : ",") . $parca;
                }
                $sayacparca->parcasayi = count($parcalargelen);
                $sayacparca->parcalar = $parcalar;
            }
            $sayacparca->sayacadi_id = $sayacadi_id;
            $sayacparca->sayaccap_id = $cap;
            $sayacparca->servisstokkod_id = $stokkod;
            $sayacparca->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $sayacadi->sayacadi . ' Tanımlı Sayaç Adına Ait Parçalar Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Sayaç Parça  Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/sayacparca')->with(array('mesaj' => 'true', 'title' => 'Sayaç Parçaları Güncellendi', 'text' => 'Sayaç Parçaları Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Parçaları Güncellenemedi', 'text' => 'Sayaç Parçaları Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getParcasil($id){
        try {
            DB::beginTransaction();
            $sayacparca = SayacParca::find($id);
            if($sayacparca){
                $bilgi = clone $sayacparca;
                $sayacadi = SayacAdi::find($sayacparca->sayacadi_id);
                $sayacgelen = SayacGelen::where('sayacadi_id', $sayacparca->sayacadi_id)->where('sayaccap_id', $sayacparca->sayaccap_id)->first();
                if ($sayacgelen) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaca ait Parçalar Silinemez', 'text' => 'Bu Sayaç Adı Sayaç Kayıdında Kullanılmış.', 'type' => 'error'));
                }
                $sayacparca->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $sayacadi->sayacadi . ' Tanımlı Sayaç Adına Ait Parçalar Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Sayaç Parça  Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaca ait Parçalar Silindi', 'text' => 'Sayaca ait Parçalar Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaca ait Parçalar Silinemedi', 'text' => 'Sayaca ait Parçalar Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaca ait Parçalar Silinemedi', 'text' => 'Sayaca ait Parçalar Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getSayacparcalari(){
        try {
            $adiid=Input::get('adiid');
            $sayacparca = SayacParca::where('sayacadi_id', $adiid)->where('sayaccap_id', 1)->first();
            if ($sayacparca) {
                $sayacparcalar = explode(',', $sayacparca->parcalar);
                $parcalar = Degisenler::whereIn('id', $sayacparcalar)->get();
            } else {
                $parcalar = Degisenler::whereIn('id', array())->get();
            }
            if (Input::has('garanti')) {
                $sayacgaranti = SayacGaranti::where('sayacadi_id', $adiid)->where('sayaccap_id', 1)->first();
                if ($sayacgaranti) {
                    $secilisayacparcalar = explode(',', $sayacgaranti->parcalar);
                    if (count($secilisayacparcalar) == $parcalar->count()) {
                        $garantiler = explode(',', $sayacgaranti->garantiler);
                    } else {
                        $garantiler = null;
                    }
                } else {
                    $garantiler = null;
                }
                return Response::json(array('durum'=>true,'parcalar' => $parcalar, 'garantiler' => $garantiler));
            }
            return Response::json(array('durum'=>true,'parcalar' => $parcalar ));
        } catch (Exception $e) {
            return Response::json(array('durum'=>false,'title' => 'Sayaca Ait Parçalar Getirilirken Hata oluştu','text'=>str_replace("'","\'",$e->getMessage()) ));
        }
    }

    public function getSayacgaranti() {
        return View::make($this->servisadi.'.sayacgaranti')->with(array('title'=>$this->servisbilgi.' Sayaç Garanti Süreleri'));
    }

    public function postSayacgarantilist() {
        $query = SayacGaranti::where('sayacgaranti.sayactur_id',$this->servisid)
            ->select(array("sayacgaranti.id","uretimyer.yeradi","sayacadi.sayacadi","sayacgaranti.garanti","uretimyer.nyeradi","sayacadi.nsayacadi"))
            ->leftjoin("uretimyer","sayacgaranti.uretimyer_id","=","uretimyer.id")
            ->leftjoin("sayacadi","sayacgaranti.sayacadi_id","=","sayacadi.id");
        return Datatables::of($query)
            ->editColumn('garanti', function ($model) {
                return $model->garanti.' yıl';
            })
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/garantiduzenle/".$model->id."'> Düzenle </a>".
                "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getGarantiekle() {
        $uretimyerleri = UretimYer::where('mekanik',0)->get();
        $sayacadlari = SayacAdi::where('sayactur_id',$this->servisid)->get();
        return View::make($this->servisadi.'.garantiekle',array('uretimyerleri'=>$uretimyerleri,'sayacadlari'=>$sayacadlari))->with(array('title'=>$this->servisbilgi.' Sayaç Garanti Süresi Ekle'));
    }

    public function postGarantiekle() {
        try {
            $rules = ['uretimyer' => 'required', 'sayacadi' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $sayacgaranti = new SayacGaranti;
            $uretimyer = Input::get('uretimyer');
            $sayacadi_id = Input::get('sayacadi');
            $garanti = Input::get('spinner');
            $sayacadi = SayacAdi::find($sayacadi_id);
            $cap = 1;
            If (SayacGaranti::where('sayacadi_id', $sayacadi_id)->where('sayaccap_id', $cap)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu sayacın garanti süresi mevcut.', 'type' => 'warning'));
            }
            if (Input::has('parcasayi')) {
                $parcasayi = Input::get('parcasayi');
                $garantisureler = "";
                $parcalar = "";
                for ($i = 1; $i <= $parcasayi; $i++) {
                    $parca = Input::get('parca' . $i);
                    $parcalar .= ($parcalar == "" ? "" : ",") . $parca;
                    $garantisure = Input::get('spinner' . $i);
                    $garantisureler .= ($garantisureler == "" ? "" : ",") . $garantisure;
                }
                $sayacgaranti->garantiler = $garantisureler;
                $sayacgaranti->parcalar = $parcalar;
            }
            $tur = $this->servisid;
            $sayacgaranti->uretimyer_id = $uretimyer;
            $sayacgaranti->sayacadi_id = $sayacadi_id;
            $sayacgaranti->sayaccap_id = $cap;
            $sayacgaranti->sayactur_id = $tur;
            $sayacgaranti->garanti = $garanti;
            $sayacgaranti->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $sayacadi->sayacadi . ' Tanımlı Sayaç Adına Ait Garanti Süresi Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Sayaç Garanti Numarası:' . $sayacgaranti->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/sayacgaranti')->with(array('mesaj' => 'true', 'title' => 'Sayaç Garanti Süresi Eklendi', 'text' => 'Sayaç Garanti Süresi Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Garanti Süresi Eklenemedi', 'text' => 'Sayaç Garanti Süresi Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getGarantiduzenle($id) {
        $uretimyerleri = UretimYer::where('mekanik',0)->get();
        $sayacadlari = SayacAdi::where('sayactur_id',$this->servisid)->get();
        $sayacgaranti = SayacGaranti::find($id);
        return View::make($this->servisadi.'.garantiduzenle',array('sayacgaranti'=>$sayacgaranti,'uretimyerleri'=>$uretimyerleri,'sayacadlari'=>$sayacadlari))->with(array('title'=>$this->servisbilgi.' Sayaç Garanti Süresi Düzenleme Ekranı'));
    }

    public function postGarantiduzenle($id) {
        try {
            $rules = ['uretimyer' => 'required', 'sayacadi' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $sayacgaranti = SayacGaranti::find($id);
            $bilgi = clone $sayacgaranti;
            $uretimyer = Input::get('uretimyer');
            $sayacadi_id = Input::get('sayacadi');
            $garanti = Input::get('spinner');
            $sayacadi = SayacAdi::find($sayacadi_id);
            $cap = 1;
            If (SayacGaranti::where('sayacadi_id', $sayacadi_id)->where('sayaccap_id', $cap)->where('id', '<>', $id)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu sayacın garanti süresi mevcut.', 'type' => 'error'));
            }
            if (Input::has('parcasayi')) {
                $parcasayi = Input::get('parcasayi');
                $garantisureler = "";
                $parcalar = "";
                for ($i = 1; $i <= $parcasayi; $i++) {
                    $parca = Input::get('parca' . $i);
                    $parcalar .= ($parcalar == "" ? "" : ",") . $parca;
                    $garantisure = Input::get('spinner' . $i);
                    $garantisureler .= ($garantisureler == "" ? "" : ",") . $garantisure;
                }
                $sayacgaranti->garantiler = $garantisureler;
                $sayacgaranti->parcalar = $parcalar;
            }
            $sayacgaranti->uretimyer_id = $uretimyer;
            $sayacgaranti->sayacadi_id = $sayacadi_id;
            $sayacgaranti->sayaccap_id = $cap;
            $sayacgaranti->garanti = $garanti;
            $sayacgaranti->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $sayacadi->sayacadi . ' Tanımlı Sayaç Adına Ait Garanti Süresi Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Sayaç Garanti Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/sayacgaranti')->with(array('mesaj' => 'true', 'title' => 'Sayaç Garanti Süresi Güncellendi', 'text' => 'Sayaç Garanti Süresi Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Garanti Süresi Güncellenemedi', 'text' => 'Sayaç Garanti Süresi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getGarantisil($id){
        try {
            DB::beginTransaction();
            $sayacgaranti = SayacGaranti::find($id);
            if($sayacgaranti){
                $bilgi = clone $sayacgaranti;
                $sayacadi = SayacAdi::find($sayacgaranti->sayacadi_id);
                $arizakayit = ArizaKayit::where('sayacadi_id', $sayacgaranti->sayacadi_id)->where('sayaccap_id', $sayacgaranti->sayaccap_id)->first();
                if ($arizakayit) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaca ait Garanti Süresi Silinemez', 'text' => 'Bu Sayaç Adı Arıza Kayıdında Kullanılmış.', 'type' => 'error'));
                }
                $sayacgaranti->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $sayacadi->sayacadi . ' Tanımlı Sayaç Adına Ait Garanti Süresi Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Sayaç Garanti Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaca ait Garanti Süresi Silindi', 'text' => 'Sayaca ait Garanti Süresi Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaca ait Garanti Süresi Silinemedi', 'text' => 'Sayaca ait Garanti Süresi Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaca ait Garanti Süresi Silinemedi', 'text' => 'Sayaca ait Garanti Süresi Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getParcaucret() {
        return View::make($this->servisadi.'.parcaucret')->with(array('title'=>$this->servisbilgi.' Sayaç Parça Ücretleri'));
    }

    public function postParcaucretlist() {
        $query = Fiyat::where('fiyat.sayactur_id',$this->servisid)
            ->select(array("fiyat.id","uretimyer.yeradi","degisenler.tanim","fiyat.fiyat","uretimyer.nyeradi","degisenler.ntanim","parabirimi.birimi"))
            ->leftjoin("uretimyer","fiyat.uretimyer_id","=","uretimyer.id")
            ->leftjoin("degisenler","fiyat.degisenler_id","=","degisenler.id")
            ->leftjoin("parabirimi","fiyat.parabirimi_id","=","parabirimi.id");
        return Datatables::of($query)
            ->editColumn('fiyat', function ($model) {
                return number_format($model->fiyat,2,'.','').' '.$model->birimi;
            })
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/ucretduzenle/".$model->id."'> Düzenle </a>".
                "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getUcretekle() {
        $uretimyerleri = UretimYer::where('mekanik',0)->get();
        $parcalar = Degisenler::where('sayactur_id',$this->servisid)->where('parcadurum','<>',2)->get();
        $parabirimleri = ParaBirimi::all();
        return View::make($this->servisadi.'.ucretekle',array('uretimyerleri'=>$uretimyerleri,'parcalar'=>$parcalar,'parabirimleri'=>$parabirimleri))->with(array('title'=>$this->servisbilgi.' Sayaç Parça Ücreti Ekle'));
    }

    public function postUcretekle() {
        try {
            $rules = ['uretimyer' => 'required', 'parca' => 'required', 'parabirimi' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $parcaucret = new Fiyat;
            if (Input::has('ucret')) {
                $fiyat = doubleval(Input::get('ucret'));
            } else {
                $fiyat = 0.00;
            }
            $yer = Input::get('uretimyer');
            $parca = Input::get('parca');
            $parabirimi = Input::get('parabirimi');
            $degisen = Degisenler::find($parca);
            If (Fiyat::where('uretimyer_id', $yer)->where('degisenler_id', $parca)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu parçanın seçilen üretim yerine ait ücreti mevcut.', 'type' => 'error'));
            }
            $tur = $this->servisid;
            $parcaucret->fiyat = $fiyat;
            $parcaucret->uretimyer_id = $yer;
            $parcaucret->degisenler_id = $parca;
            $parcaucret->sayactur_id = $tur;
            $parcaucret->parabirimi_id = $parabirimi;
            $parcaucret->save();
            $sonuc = BackendController::FiyatGuncelle($parcaucret);
            if($sonuc['durum']){
                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $degisen->tanim . ' Tanımlı Değişen Parçaya Ait Fiyat Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Parça Fiyat Numarası:' . $parcaucret->id);
                DB::commit();
                return Redirect::to($this->servisadi.'/parcaucret')->with(array('mesaj' => 'true', 'title' => 'Parça Ücreti Eklendi', 'text' => 'Parça Ücreti Başarıyla Eklendi', 'type' => 'success'));
            }else{
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Parça Ücreti Eklenemedi', 'text' => 'Parça Ücreti Tüm Fiyatlandırma Bekleyenlere Eklenemedi.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Parça Ücreti Eklenemedi', 'text' => 'Parça Ücreti Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getUcretduzenle($id) {
        $uretimyerleri = UretimYer::where('mekanik',0)->get();
        $parcalar = Degisenler::where('sayactur_id',$this->servisid)->where('parcadurum','<>',2)->get();
        $parcaucret = Fiyat::find($id);
        $parabirimleri = ParaBirimi::all();
        return View::make($this->servisadi.'.ucretduzenle',array('parcaucret'=>$parcaucret,'uretimyerleri'=>$uretimyerleri,'parcalar'=>$parcalar,'parabirimleri'=>$parabirimleri))->with(array('title'=>$this->servisbilgi.' Sayaç Parça Ücreti Düzenleme Ekranı'));
    }

    public function postUcretduzenle($id) {
        try {
            $rules = ['uretimyer' => 'required', 'parca' => 'required', 'parabirimi' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $parcaucret = Fiyat::find($id);
            $bilgi = clone $parcaucret;
            if (Input::has('ucret')) {
                $fiyat = doubleval(Input::get('ucret'));
            } else {
                $fiyat = 0.00;
            }
            $yer = Input::get('uretimyer');
            $parca = Input::get('parca');
            $parabirimi = Input::get('parabirimi');
            $degisen = Degisenler::find($parca);
            If (Fiyat::where('uretimyer_id', $yer)->where('degisenler_id', $parca)->where('id', '<>', $id)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu parçanın seçilen üretim yerine ait ücreti mevcut.', 'type' => 'error'));
            }
            $parcaucret->fiyat = $fiyat;
            $parcaucret->uretimyer_id = $yer;
            $parcaucret->degisenler_id = $parca;
            $parcaucret->parabirimi_id = $parabirimi;
            $parcaucret->save();
            $sonuc = BackendController::FiyatGuncelle($parcaucret);
            if($sonuc['durum']){
                BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $degisen->tanim . ' Tanımlı Değişen Parçaya Ait Fiyat Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Parça Fiyat Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::to($this->servisadi.'/parcaucret')->with(array('mesaj' => 'true', 'title' => 'Parça Ücreti Güncellendi', 'text' => 'Parça Ücreti Başarıyla Güncellendi', 'type' => 'success'));
            }else{
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Parça Ücreti Güncellenemedi', 'text' => 'Parça Ücreti Tüm Fiyatlandırma Bekleyenlerde Güncellenemedi.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Parça Ücreti Güncellenemedi', 'text' => 'Parça Ücreti Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getUcretsil($id){
        try {
            DB::beginTransaction();
            $parcaucret = Fiyat::find($id);
            if($parcaucret){
                $bilgi = clone $parcaucret;
                $degisen = Degisenler::find($parcaucret->degisenler_id);
                $degisenler = Degisenler::where('id', $parcaucret->degisenler_id)->first();

                if ($degisenler && $degisenler->kullanim == 1) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Parça Ücreti Silinemez', 'text' => 'Bu Parça Arıza Kayıdında Kullanılmış.', 'type' => 'error'));
                }
                $parcaucret->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $degisen->tanim . ' Tanımlı Değişen Parçaya Ait Fiyat Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Parça Fiyat Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Parça Ücreti Silindi', 'text' => 'Parça Ücreti Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Parça Ücreti Silinemedi', 'text' => 'Parça Ücreti Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Parça Ücreti Silinemedi', 'text' => 'Parça Ücreti Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getStokdurum() {
        return View::make($this->servisadi.'.stokdurum')->with(array('title'=>$this->servisbilgi.' Sayaç Parça Stok Durumları'));
    }

    public function postStoklist() {
        $query = StokDurum::where('stokdurum.servis_id',$this->servisid)
            ->select(array("stokdurum.id","stokdurum.stokkodu","degisenler.tanim","stokdurum.adet","stokdurum.depokodu",DB::raw("0 as depoadet"),"stokdurum.kalan",
                "stokdurum.biten","stokdurum.nstokkodu","degisenler.ntanim","servis.nservisadi"))
            ->leftjoin("degisenler","stokdurum.degisenler_id","=","degisenler.id")
            ->leftjoin("servis","stokdurum.servis_id","=","servis.id");
        return Datatables::of($query)
            ->editColumn('depoadet', function ($model) {
                    $stok=StBakiye::where('STOK_KODU',$model->stokkodu)->where('DEPO_KODU',$model->depokodu)->first();
                    if(!$stok) $stok=new StBakiye(array('BAKIYE'=>0));
                    return intval($stok->BAKIYE);
            })
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/stokdurumduzenle/".$model->id."'> Düzenle </a>";
            })
            ->make(true);
    }

    public function getStokdurumekle() {
        $netsisstokkodlari = NetsisStokKod::all();
        $netsisdepolar = NetsisDepolar::where('subekodu',8)->get();
        $secililer=StokDurum::where('servis_id',$this->servisid)->get(array('degisenler_id'))->toArray();
        $parcalar = Degisenler::where('sayactur_id',$this->servisid)->where('parcadurum','<>',1)->where('stokkontrol',1)->whereNotIn('id',$secililer)->get();
        return View::make($this->servisadi.'.stokdurumekle',array('netsisstokkodlari'=>$netsisstokkodlari,'parcalar'=>$parcalar,'netsisdepolar'=>$netsisdepolar))->with(array('title'=>$this->servisbilgi.' Stok-Parça Grubu Ekle'));
    }

    public function postStokdurumekle() {
        try {
            $rules = ['parca' => 'required', 'stokadi' => 'required','depokodu'=>'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $stokdurum = new StokDurum;
            $parca = Input::get('parca');
            $stokadi = Input::get('stokadi');
            $depokodu = Input::get('depokodu');
            $adet = Input::get('adet');
            $degisen = Degisenler::find($parca);
            If (StokDurum::where('servis_id', $this->servisid)->where('degisenler_id', $parca)->where('netsisstokkod_id', $stokadi)->where('adet', $adet)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu parçanın stok durumu mevcut.', 'type' => 'error'));
            }
            $netsisstokkod = NetsisStokKod::find($stokadi);
            $stokdurum->degisenler_id = $parca;
            $stokdurum->netsisstokkod_id = $stokadi;
            $stokdurum->stokkodu = $netsisstokkod->kodu;
            $stokdurum->depokodu = $depokodu;
            $stokdurum->adet = $adet;
            $stokdurum->servis_id = $this->servisid;
            $kalan = 0;
            $gelenmiktarlar = StokGelen::where('stokkodu', $netsisstokkod->kodu)->get();
            foreach ($gelenmiktarlar as $gelen) {
                $kalan += $gelen->miktar;
            }
            $stokdurum->kalan = $kalan;
            $stokdurum->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $degisen->tanim . ' Tanımlı Değişen Parçaya Ait Stok Durumu Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Stok Durumu Numarası:' . $stokdurum->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/stokdurum')->with(array('mesaj' => 'true', 'title' => 'Stok-Parça Grubu Eklendi', 'text' => 'Stok-Parça Grubu Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Stok-Parça Grubu Eklenemedi', 'text' => 'Stok-Parça Grubu Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getStokdurumduzenle($id) {
        $stokdurum=StokDurum::find($id);
        $netsisstokkodlari = NetsisStokKod::all();
        $netsisdepolar = NetsisDepolar::where('subekodu',8)->get();
        $secililer=StokDurum::where('servis_id',$this->servisid)->get(array('degisenler_id'))->toArray();
        $parcalar = Degisenler::where('sayactur_id',$this->servisid)->where('parcadurum','<>',1)->where('stokkontrol',1)
            ->where(function($query)use($secililer,$stokdurum){$query->whereNotIn('id',$secililer)->orWhere('id',$stokdurum->degisenler_id);})->get();
        return View::make($this->servisadi.'.stokdurumduzenle',array('stokdurum'=>$stokdurum,'netsisstokkodlari'=>$netsisstokkodlari,'parcalar'=>$parcalar,'netsisdepolar'=>$netsisdepolar))->with(array('title'=>$this->servisbilgi.' Stok-Parça Grubu Düzenle'));
    }

    public function postStokdurumduzenle($id) {
        try {
            $rules = ['parca' => 'required', 'stokadi' => 'required','depokodu'=>'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $stokdurum = StokDurum::find($id);
            $bilgi = clone $stokdurum;
            $kullanilan = $stokdurum->kullanilan;
            $biten = $stokdurum->biten;
            $parca = Input::get('parca');
            $stokadi = Input::get('stokadi');
            $depokodu = Input::get('depokodu');
            $adet = Input::get('adet');
            $degisen = Degisenler::find($parca);
            If (StokDurum::where('servis_id', $this->servisid)->where('degisenler_id', $parca)->where('netsisstokkod_id', $stokadi)->where('adet', $adet)->where('id', '<>', $id)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu parçanın stok durumu mevcut.', 'type' => 'error'));
            }
            if ($biten > 0 || $kullanilan > 0) {
                if ($stokdurum->degisenler_id != $parca || $stokdurum->netsisstokkod_id != $stokadi) {
                    DB::rollBack();
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Stok Durumu Değiştirilemez', 'text' => 'Arıza Tespitinde değişen parça olarak kullanılmıştır.', 'type' => 'error'));
                }else{
                    $netsisstokkod = NetsisStokKod::find($stokadi);
                    $stokdurum->degisenler_id = $parca;
                    $stokdurum->netsisstokkod_id = $stokadi;
                    $stokdurum->stokkodu = $netsisstokkod->kodu;
                    $stokdurum->depokodu = $depokodu;
                    $stokdurum->adet = $adet;
                    $stokdurum->servis_id = $this->servisid;
                }
            } else {
                $netsisstokkod = NetsisStokKod::find($stokadi);
                $stokdurum->degisenler_id = $parca;
                $stokdurum->netsisstokkod_id = $stokadi;
                $stokdurum->stokkodu = $netsisstokkod->kodu;
                $stokdurum->depokodu = $depokodu;
                $stokdurum->adet = $adet;
                $stokdurum->servis_id = $this->servisid;
            }
            $stokdurum->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $degisen->tanim . ' Tanımlı Değişen Parçaya Ait Stok Durumu Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Stok Durumu Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/stokdurum')->with(array('mesaj' => 'true', 'title' => 'Stok-Parça Grubu Güncellendi', 'text' => 'Stok-Parça Grubu Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Stok-Parça Grubu Güncellenemedi', 'text' => 'Stok-Parça Grubu Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getStokgirisi() {
        return View::make($this->servisadi.'.stokgirisi')->with(array('title'=>$this->servisbilgi.' Servis Stok Girişi Ekranı'));
    }

    public function postStokgirislist() {
        $query = StokGirisCikis::where('stokgiriscikis.servis_id',$this->servisid)
            ->select(array("stokgiriscikis.id","stokgiriscikis.stokkodu","degisenler.tanim","servis.servisadi","stokgiriscikis.miktar"
            ,"stokgiriscikis.ggckod","stokgiriscikis.aciklama","stokgiriscikis.tarih","kullanici.adi_soyadi","stokgiriscikis.gtarih"))
            ->leftjoin("degisenler","stokgiriscikis.degisenler_id","=","degisenler.id")
            ->leftjoin("servis","stokgiriscikis.servis_id","=","servis.id")
            ->leftjoin("kullanici","stokgiriscikis.kullanici_id","=","kullanici.id");
        return Datatables::of($query)
            ->editColumn('tarih', function ($model) {
                $date = new DateTime($model->tarih);
                return $date->format('d-m-Y');
            })
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/stokhareketduzenle/".$model->id."'> Düzenle </a>".
                "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getStokadi()
    {
        try {
            $degisenid=Input::get('degisenid');
            $stokdurum = StokDurum::where('degisenler_id', $degisenid)->where('servis_id', $this->servisid)->first();
            if ($stokdurum) {
                $netsisstokkod = NetsisStokKod::find($stokdurum->netsisstokkod_id);
                return Response::json(array('durum' => true, 'netsisstokkod' => $netsisstokkod));
            }
            return Response::json(array('durum' => false,'title'=>'Stok Durum Hatası','text'=>'Stok Eşleşmesi Yok','type'=>'warning'));
        } catch (Exception $e) {
            return Response::json(array('durum' => false,'title'=>'Stok Durum Hatası','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'warning'));
        }
    }

    public function getStokhareketekle() {
        $parcalar = Degisenler::where('sayactur_id',$this->servisid)->where('parcadurum','<>',1)->where('stokkontrol',1)->get();
        return View::make($this->servisadi.'.stokhareketekle',array('parcalar'=>$parcalar))->with(array('title'=>$this->servisbilgi.' Stok Hareketi Ekle'));
    }

    public function postStokhareketekle() {
        try {
            $rules = ['parca' => 'required', 'gckod' => 'required', 'miktar' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $stokgiriscikis = new StokGirisCikis;
            $parca = Input::get('parca');
            $gckod = Input::get('gckod');
            $miktar = Input::get('miktar');
            $aciklama = Input::get('aciklama');
            $degisen = Degisenler::find($parca);
            $stokdurum = StokDurum::where('degisenler_id', $parca)->where('servis_id', $this->servisid)->first();
            if ($gckod == 'C') //çıkış yapılıyorsa
            {
                if (($miktar + $stokdurum->kullanilan) > $stokdurum->kalan) //kalan miktar çıkılacak miktarı karşılamıyorsa
                {
                    DB::rollBack();
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Miktar Yeterli Değil', 'text' => 'Çıkış Yapılacak Miktar Yeterli Değil. Stok Durumunu Kontrol Ediniz. ', 'type' => 'warning'));
                }
            }
            $stokgiriscikis->degisenler_id = $parca;
            $stokgiriscikis->netsisstokkod_id = $stokdurum->netsisstokkod_id;
            $stokgiriscikis->stokkodu = $stokdurum->stokkodu;
            $stokgiriscikis->miktar = $miktar;
            $stokgiriscikis->gckod = $gckod;
            $stokgiriscikis->aciklama = $aciklama;
            $stokgiriscikis->tarih = date('Y-m-d H:i:s');
            $stokgiriscikis->servis_id = $this->servisid;
            $stokgiriscikis->kullanici_id = Auth::user()->id;
            $stokgiriscikis->durum = 1;
            $stokgiriscikis->save();
            if ($gckod == 'G') //giriş yapıldıysa
                $stokdurum->kalan += $miktar;
            else
                $stokdurum->kalan -= $miktar;
            $stokdurum->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $degisen->tanim . ' Tanımlı Değişen Parçaya Ait Stok Hareketi Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Stok Hareket Numarası:' . $stokgiriscikis->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/stokgirisi')->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Kaydedildi', 'text' => 'Stok Hareketi Başarıyla Kaydedildi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Kaydedilemedi', 'text' => 'Stok Hareketi Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getStokhareketduzenle($id) {
        $stokgiriscikis=StokGirisCikis::find($id);
        $parcalar = Degisenler::where('sayactur_id',$this->servisid)->where('parcadurum','<>',1)->where('stokkontrol',1)->get();
        return View::make($this->servisadi.'.stokhareketduzenle',array('stokgiriscikis'=>$stokgiriscikis,'parcalar'=>$parcalar))->with(array('title'=>$this->servisbilgi.' Stok Hareketi Düzenle'));
    }

    public function postStokhareketduzenle($id) {
        try {
            $rules = ['parca' => 'required', 'gckod' => 'required', 'miktar' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $stokgiriscikis = StokGirisCikis::find($id);
            $bilgi = clone $stokgiriscikis;
            $eskimiktar = $stokgiriscikis->miktar;
            $eskigckod = $stokgiriscikis->gckod;
            $parca = Input::get('parca');
            $gckod = Input::get('gckod');
            $miktar = Input::get('miktar');
            $aciklama = Input::get('aciklama');
            $degisen = Degisenler::find($parca);
            $stokdurum = StokDurum::where('degisenler_id', $parca)->where('servis_id', $this->servisid)->first();
            if ($gckod == 'G') //giriş yapılıyorsa
            {
                if ($gckod == $eskigckod) //hareket tipi değişmemişse
                {
                    if ($eskimiktar > $miktar) // azalma olmuş çıkarılacak
                    {
                        $fark = $eskimiktar - $miktar;
                        if (($fark + $stokdurum->kullanilan) > $stokdurum->kalan) //çıkarılacak miktar yeterli değil
                        {
                            DB::rollBack();
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Miktar Yeterli Değil', 'text' => 'Çıkış Yapılacak Miktar Yeterli Değil. Stok Durumunu Kontrol Ediniz. ', 'type' => 'warning'));
                        }
                    }
                }
            } else {  // çıkış yapılıyorsa
                if ($gckod == $eskigckod) //hareket tipi değişmemişse
                {
                    if ($eskimiktar < $miktar) // azalma olmuş çıkarılacak
                    {
                        $fark = $miktar - $eskimiktar;
                        if (($fark + $stokdurum->kullanilan) > $stokdurum->kalan) //çıkarılacak miktar yeterli değil
                        {
                            DB::rollBack();
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Miktar Yeterli Değil', 'text' => 'Çıkış Yapılacak Miktar Yeterli Değil. Stok Durumunu Kontrol Ediniz. ', 'type' => 'warning'));
                        }
                    }
                } else { //hareket tipi değişmiş
                    $fark = $miktar + $eskimiktar;
                    if (($fark + $stokdurum->kullanilan) > $stokdurum->kalan) //çıkarılacak miktar yeterli değil
                    {
                        DB::rollBack();
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Miktar Yeterli Değil', 'text' => 'Çıkış Yapılacak Miktar Yeterli Değil. Stok Durumunu Kontrol Ediniz. ', 'type' => 'warning'));
                    }
                }
            }
            $stokgiriscikis->degisenler_id = $parca;
            $stokgiriscikis->netsisstokkod_id = $stokdurum->netsisstokkod_id;
            $stokgiriscikis->stokkodu = $stokdurum->stokkodu;
            $stokgiriscikis->miktar = $miktar;
            $stokgiriscikis->gckod = $gckod;
            $stokgiriscikis->aciklama = $aciklama;
            $stokgiriscikis->tarih = date('Y-m-d H:i:s');
            $stokgiriscikis->servis_id = $this->servisid;
            $stokgiriscikis->kullanici_id = Auth::user()->id;
            $stokgiriscikis->durum = 1;
            $stokgiriscikis->save();
            if ($gckod == 'G') //giriş yapıldıysa
            {
                if ($gckod == $eskigckod) //hareket tipi değişmemişse
                {
                    if ($eskimiktar > $miktar) // azalma olmuş çıkarılacak
                    {
                        $fark = $eskimiktar - $miktar;
                        $stokdurum->kalan -= $fark;
                    } else {
                        $fark = $miktar - $eskimiktar;
                        $stokdurum->kalan += $fark;
                    }
                } else { //hareket tipi değiştiyse çıkışken giriş olmuş
                    $fark = $miktar + $eskimiktar;
                    $stokdurum->kalan += $fark;
                }
            } else {  // çıkış yapıldıysa
                if ($gckod == $eskigckod) //hareket tipi değişmemişse
                {
                    if ($eskimiktar < $miktar) // azalma olmuş çıkarılacak
                    {
                        $fark = $miktar - $eskimiktar;
                        $stokdurum->kalan -= $fark;
                    } else {
                        $fark = $eskimiktar - $miktar;
                        $stokdurum->kalan += $fark;
                    }
                } else { //hareket tipi değişmiş girişken çıkış olmuş
                    $fark = $miktar + $eskimiktar;
                    $stokdurum->kalan -= $fark;
                }
            }
            $stokdurum->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $degisen->tanim . ' Tanımlı Değişen Parçaya Ait Stok Hareketi Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Stok Hareket Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/stokgirisi')->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Güncellendi', 'text' => 'Stok Hareketi Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Güncellenemedi', 'text' => 'Stok Hareketi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getStokhareketsil($id){
        try {
            DB::beginTransaction();
            $stokgiriscikis = StokGirisCikis::find($id);
            if($stokgiriscikis){
                $bilgi = clone $stokgiriscikis;
                $degisen = Degisenler::find($stokgiriscikis->degisenler_id);
                $stokdurum = StokDurum::where('degisenler_id', $stokgiriscikis->degisenler_id)->where('servis_id', $this->servisid)->first();
                $miktar = $stokgiriscikis->miktar;
                $gckod = $stokgiriscikis->gckod;
                if ($gckod == 'G') //çıkış yapılacak
                {
                    if (($miktar + $stokdurum->kullanilan) > $stokdurum->kalan) {
                        DB::rollBack();
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Miktar Yeterli Değil', 'text' => 'Çıkış Yapılacak Miktar Yeterli Değil. Stok Durumunu Kontrol Ediniz. ', 'type' => 'warning'));
                    } else {
                        $stokdurum->kalan -= $miktar;
                    }
                } else { //giriş yapılacak
                    $stokdurum->kalan += $miktar;
                }
                $stokdurum->save();
                $stokgiriscikis->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $degisen->tanim . ' Tanımlı Değişen Parçaya Ait Stok Hareketi Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Stok Hareket Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Silindi', 'text' => 'Stok Hareketi Başarıyla Silindi.', 'type' => 'success'));

            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Silinemedi', 'text' => 'Stok Hareketi Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Stok Hareketi Silinemedi', 'text' => 'Stok Hareketi Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getSayaclar() {
        return View::make($this->servisadi.'.sayaclar')->with(array('title'=>$this->servisbilgi.' Sayaç Listesi'));
    }

    public function postSayaclist() {
        $query = Sayac::where('sayac.sayactur_id',$this->servisid)
            ->select(array("sayac.id","sayac.serino","sayac.cihazno","sayacadi.sayacadi","uretimyer.yeradi","sayac.uretimtarihi","sayac.guretimtarihi","sayacadi.nsayacadi",
                "uretimyer.nyeradi"))
            ->leftjoin("uretimyer","sayac.uretimyer_id","=","uretimyer.id")
            ->leftjoin("sayacadi","sayac.sayacadi_id","=","sayacadi.id");
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
        $uretimyerleri = UretimYer::where('mekanik',0)->get();
        $sayacadlari = SayacAdi::where('sayactur_id',$this->servisid)->get();
        return View::make($this->servisadi.'.tektekekle',array('uretimyerleri'=>$uretimyerleri,'sayacadlari'=>$sayacadlari))->with(array('title'=>$this->servisbilgi.' Sayaç Ekle'));
    }

    public function postTektekekle() {
        try {
            $rules = ['uretimtarihi' => 'required', 'uretimyer' => 'required', 'serino' => 'required', 'sayacadlari' => 'required'];
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
                    $sayac = Sayac::where('uretimyer_id', $uretimyer_id)->where('serino', $serino)->where('sayactur_id', $this->servisid)->first();
                    if ($sayac) {
                        $flag = 1;
                    } else {
                        if ($serino != "") {
                            $sayac = new Sayac;
                            $sayac->serino = $serino;
                            $sayac->cihazno = $serino;
                            $sayac->sayactur_id = $this->servisid;
                            $sayac->sayacadi_id = $sayacadlari[$i];
                            $sayac->sayaccap_id = 1;
                            $sayac->uretimyer_id = $uretimyer_id;
                            $sayac->uretimtarihi = $uretimtarih;
                            $eklenenler .= ($eklenenler == "" ? "" : ",") . $serino;
                            $sayac->save();
                            $adet++;
                        } else {
                            $flag = ($flag != 1) ? 3 : $flag;
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
                return Redirect::to($this->servisadi.'/sayaclar')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Bazıları Eklendi', 'text' => 'Sayaç Türü Belirsiz Olan Sayaçlar Güncellendi', 'type' => 'success'));
            } else if ($flag == 3) {
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
        $uretimyerleri = UretimYer::where('mekanik',0)->get();
        $sayacadlari = SayacAdi::where('sayactur_id',$this->servisid)->get();
        return View::make($this->servisadi.'.siraliekle',array('uretimyerleri'=>$uretimyerleri,'sayacadlari'=>$sayacadlari))->with(array('title'=>$this->servisbilgi.' Sayaç Ekle'));
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
            $sayaccap_id = 1;
            $adet = 0;
            $flag = 0;
            DB::beginTransaction();
            try {
                $eklenenler="";
                for ($i = $baslangic; $i <= $bitis; $i = $i + $artis) {
                    $sayac = Sayac::where('uretimyer_id', $uretimyer_id)->where('serino', $i)->where('sayactur_id', $this->servisid)->first();
                    if ($sayac) {
                        $flag = 1;
                    } else {
                        $sayac = new Sayac;
                        $sayac->serino = $i;
                        $sayac->cihazno = $i;
                        $sayac->sayactur_id = $this->servisid;
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
        $uretimyerleri = UretimYer::where('mekanik',0)->get();
        $sayacadlari = SayacAdi::where('sayactur_id',$this->servisid)->get();
        $sayac = Sayac::find($id);
        return View::make($this->servisadi.'.sayacduzenle',array('sayac'=>$sayac,'uretimyerleri'=>$uretimyerleri,'sayacadlari'=>$sayacadlari))->with(array('title'=>$this->servisbilgi.' Sayaç Düzenleme Ekranı'));
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
            $sayaccap_id = 1;
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

    public function getYetkilikisi() {
        return View::make($this->servisadi.'.yetkilikisi')->with(array('title'=>'Cari Yetkili Kişiler'));
    }

    public function postYetkililist() {
        $query = Yetkili::select(array("yetkili.id","kullanici.adi_soyadi","netsiscari.cariadi","yetkili.gaktif","kullanici.nadi_soyadi","netsiscari.ncariadi",
            "yetkili.naktif","yetkili.netsiscari_id"))
            ->leftJoin('netsiscari','yetkili.netsiscari_id','=','netsiscari.id')
            ->leftJoin('kullanici','yetkili.kullanici_id','=','kullanici.id');
        return Datatables::of($query)
            ->addColumn('yerler', function ($model) {
                $cariyerler=CariYer::where('netsiscari_id',$model->netsiscari_id)->where('durum',1)->get();
                $uretimyer="";
                foreach($cariyerler as $cariyer){
                    $uretimyer.=($uretimyer=="" ? "" : ",").$cariyer->uretimyer->yeradi;
                }
                return $uretimyer;
            })
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                if($model->netsiscari_id!=2631) //manasa ait cari değilse
                    return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/yetkiliduzenle/".$model->id."'> Düzenle </a>";
                else
                    return "";
            })
            ->make(true);
    }

    public function getYetkiliekle() {
        $netsiscariler = NetsisCari::where('caridurum','A')->whereIn('caritipi',array('A','D'))
            ->whereIn('subekodu',array(-1,8))->whereNotIn('carikod',(function ($query){$query->select('carikod')->from('kodharichar')->where('subekodu', 8);}))
            ->orderBy('carikod','asc')->get();
        $kullanicilar=Kullanici::whereIn('grup_id',array('6','17','19'))->where('aktifdurum',1)->get();
        return View::make($this->servisadi.'.yetkiliekle',array('netsiscariler'=>$netsiscariler,'kullanicilar'=>$kullanicilar))->with(array('title'=>'Cari Yetkili Ekle'));
    }

    public function postYetkiliekle() {
        try {
            $rules = ['kullanici' => 'required', 'netsiscari' => 'required', 'durum' => 'required', 'email' => 'email'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $kullanici_id = Input::get('kullanici');
            $netsiscari_id = Input::get('netsiscari');
            $durum = Input::get('durum');
            $email = Input::get('email');
            $telefon = Input::get('telefon');
            DB::beginTransaction();
            $kullanici = Kullanici::find($kullanici_id);
            $netsiscari = NetsisCari::find($netsiscari_id);
            $yetkili = Yetkili::where('kullanici_id', $kullanici_id)->where('netsiscari_id', $netsiscari_id)->first();
            if ($yetkili) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu yetkili adı bu cari ile zaten eşleştirilmiş', 'type' => 'warning'));
            }
            $yetkili = new Yetkili;
            $yetkili->email = $email;
            $yetkili->telefon = $telefon;
            $yetkili->netsiscari_id = $netsiscari_id;
            $yetkili->kullanici_id = $kullanici_id;
            $yetkili->aktif = $durum;
            $yetkili->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $kullanici->adi_soyadi . ' Kullanıcısı ' . $netsiscari->cariadi . ' Carisi için Yetkili Olarak Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Yetkili Numarası:' . $yetkili->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/yetkilikisi')->with(array('mesaj' => 'true', 'title' => 'Cari Yetkilisi Kaydedildi', 'text' => 'Cari Yetkilisi Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Yetkilisi Kaydedilemedi', 'text' => 'Cari Yetkilisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getYetkiliduzenle($id) {
        $yetkili=Yetkili::find($id);
        $yetkili->kullanici=Kullanici::find($yetkili->kullanici_id);
        $netsiscariler = NetsisCari::where(function($query){$query->where('caridurum','A')->whereIn('caritipi',array('A','D'))->whereIn('subekodu',array(-1,8))
            ->whereNotIn('carikod',(function ($query){$query->select('carikod')->from('kodharichar')->where('subekodu', 8);}));})->orWhere('id',$yetkili->netsiscari_id)
            ->orderBy('carikod','asc')->get();
        $kullanicilar=Kullanici::whereIn('grup_id',array('6','17','19'))->where('aktifdurum',1)->get();
        $cariyerler=CariYer::where('netsiscari_id',$yetkili->netsiscari_id)->where('durum',1)->get();
        $uretimyer="";
        foreach($cariyerler as $cariyer){
            $cariyer->uretimyer=UretimYer::find($cariyer->uretimyer_id);
            $uretimyer.=($uretimyer=="" ? "" : ",").$cariyer->uretimyer->yeradi;
        }
        $yetkili->uretimyer=$uretimyer;
        return View::make($this->servisadi.'.yetkiliduzenle',array('yetkili'=>$yetkili,'netsiscariler'=>$netsiscariler,'kullanicilar'=>$kullanicilar))->with(array('title'=>'Cari Yetkili Düzenleme Ekranı'));
    }

    public function postYetkiliduzenle($id) {
        try {
            $rules = ['kullanici' => 'required', 'netsiscari' => 'required', 'durum' => 'required', 'email' => 'email'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $kullanici_id = Input::get('kullanici');
            $netsiscari_id = Input::get('netsiscari');
            $durum = Input::get('durum');
            $email = Input::get('email');
            $telefon = Input::get('telefon');
            DB::beginTransaction();
            $kullanici = Kullanici::find($kullanici_id);
            $netsiscari = NetsisCari::find($netsiscari_id);
            $yetkili = Yetkili::where('kullanici_id', $kullanici_id)->where('netsiscari_id', $netsiscari_id)->where('id', '<>', $id)->first();
            if ($yetkili) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu yetkili adı bu cari ile zaten eşleştirilmiş', 'type' => 'warning'));
            }
            $yetkili = Yetkili::find($id);
            $bilgi = clone $yetkili;
            $yetkili->email = $email;
            $yetkili->telefon = $telefon;
            $yetkili->netsiscari_id = $netsiscari_id;
            $yetkili->kullanici_id = $kullanici_id;
            $yetkili->aktif = $durum;
            $yetkili->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $kullanici->adi_soyadi . ' Kullanıcısı ' . $netsiscari->cariadi . ' Carisi için Yetkili Olarak Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Yetkili Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/yetkilikisi')->with(array('mesaj' => 'true', 'title' => 'Cari Yetkilisi Güncellendi', 'text' => 'Cari Yetkilisi Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Yetkilisi Güncellenemedi', 'text' => 'Cari Yetkilisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getUretimyeri() {
        return View::make($this->servisadi.'.uretimyeri')->with(array('title'=>'Üretim Yeri Bilgileri'));
    }

    public function postUretimyerilist() {
        $query = UretimYer::where('mekanik',0)->select(array("uretimyer.id","uretimyer.yeradi","uretimyer.issue","parabirimi.gbirimi","uretimyer.nyeradi","parabirimi.nbirimi"))
            ->leftjoin("parabirimi", "uretimyer.parabirimi_id", "=", "parabirimi.id");
        return Datatables::of($query)
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                if($model->id==0)
                    return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/uretimyeriduzenle/".$model->id."'> Düzenle </a>";
                else
                    return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/uretimyeriduzenle/".$model->id."'> Düzenle </a>".
                    "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getUretimyeriekle() {
        $birimler = ParaBirimi::all();
        return View::make($this->servisadi.'.uretimyeriekle',array('birimler'=>$birimler))->with(array('title'=>'Üretim Yeri Ekle'));
    }

    public function postUretimyeriekle() {
        try {
            $adi = BackendController::titleCase(Input::get('yer'));
            $issue = Input::get('issue') == "" ? "00" : Input::get('issue');
            $birim = Input::get('birim');
            DB::beginTransaction();
            If (UretimYer::onlyTrashed()->where('yeradi', $adi)->where('mekanik', 0)->first()) {
                try {
                    $uretimyer = UretimYer::onlyTrashed()->where('yeradi', $adi)->where('mekanik', 0)->first();
                    $uretimyer->deleted_at = NULL;
                    $uretimyer->save();
                    DB::commit();
                    return Redirect::to($this->servisadi.'/uretimyeri')->with(array('mesaj' => 'true', 'title' => 'Üretim Yeri Geri Getirildi', 'text' => 'Üretim Yeri Eski Bilgileri Başarıyla Geri Getirildi.', 'type' => 'success'));
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Üretim Yeri Eklenemedi', 'text' => 'Üretim Yeri Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                }
            }
            $rules = ['yer' => 'required', 'birim' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            If (UretimYer::where('yeradi', $adi)->where('mekanik', 0)->first()) {
                DB::rollBack();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu Üretim yeri mevcut.', 'type' => 'warning'));
            }

            $uretimyer = new UretimYer;
            $uretimyer->yeradi = $adi;
            $uretimyer->issue = $issue;
            $uretimyer->parabirimi_id = $birim;

            $uretimyer->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $adi . ' Tanımlı Üretim Yeri Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Üretim Yeri Numarası:' . $uretimyer->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/uretimyeri')->with(array('mesaj' => 'true', 'title' => 'Üretim Yeri Eklendi', 'text' => 'Üretim Yeri Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Üretim Yeri Eklenemedi', 'text' => 'Üretim Yeri Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getUretimyeriduzenle($id) {
        $uretimyer = UretimYer::find($id);
        $birimler = ParaBirimi::all();
        return View::make($this->servisadi.'.uretimyeriduzenle',array('uretimyer'=>$uretimyer,'birimler'=>$birimler))->with(array('title'=>'Üretim Yeri Düzenleme Ekranı'));
    }

    public function postUretimyeriduzenle($id) {
        try {
            $rules = ['yer' => 'required', 'birim' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $uretimyer = UretimYer::find($id);
            $bilgi = clone $uretimyer;
            $adi = BackendController::titleCase(Input::get('yer'));
            $issue = Input::get('issue') == "" ? "00" : Input::get('issue');
            $birim = Input::get('birim');
            If (UretimYer::where('yeradi', $adi)->where('mekanik', 0)->where('id', '<>', $id)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu Üretim yeri mevcut.', 'type' => 'warning'));
            }
            $uretimyer->yeradi = $adi;
            $uretimyer->issue = $issue;
            $uretimyer->parabirimi_id = $birim;
            $uretimyer->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $bilgi->yeradi . ' Tanımlı Üretim Yeri Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Üretim Yeri Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/uretimyeri')->with(array('mesaj' => 'true', 'title' => 'Üretim Yeri Güncellendi', 'text' => 'Üretim Yeri Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Üretim Yeri Güncellenemedi', 'text' => 'Üretim Yeri Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getUretimyerisil($id){
        try {
            DB::beginTransaction();
            $uretimyer = UretimYer::find($id);
            if($uretimyer){
                $bilgi = clone $uretimyer;
                $sayacgelen = SayacGelen::where('uretimyer_id', $id)->first();
                if ($sayacgelen) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Üretim Yeri Silinemez', 'text' => 'Üretim Yeri Sayaç Kayıdında Kullanılmıştır.', 'type' => 'error'));
                }
                $uretimyer->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $bilgi->yeradi . ' Tanımlı Üretim Yeri Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Üretim Yeri Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Üretim Yeri Silindi', 'text' => 'Üretim Yeri Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Üretim Yeri Silinemedi', 'text' => 'Üretim Yeri Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Üretim Yeri Silinemedi', 'text' => 'Üretim Yeri Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getCariyerbilgi()
    {
        try {
            $netsiscariid=Input::get('netsiscariid');
            $cariyerler = CariYer::where('netsiscari_id', $netsiscariid)->where('durum',1)->get();
            foreach ($cariyerler as $cariyer) {
                $cariyer->uretimyer = UretimYer::find($cariyer->uretimyer_id);
            }
            return Response::json(array('durum'=>true,'cariyerler' => $cariyerler));
        } catch (Exception $e) {
            return Response::json(array('durum'=>false,'title'=>'Cari Bilgisi Alınamadı','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

    public function getHurdaneden() {
        return View::make($this->servisadi.'.hurdaneden')->with(array('title'=>'Sayaç Hurda Nedenleri'));
    }

    public function postHurdanedenilist() {
        $query = HurdaNedeni::where('hurdanedeni.sayactur_id',$this->servisid)
            ->select(array("hurdanedeni.id","hurdanedeni.nedeni","hurdanedeni.nnedeni"));
        return Datatables::of($query)
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/hurdanedeniduzenle/".$model->id."'> Düzenle </a>".
                "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getHurdanedeniekle() {
        return View::make($this->servisadi.'.hurdanedeniekle')->with(array('title'=>'Sayaç Hurda Nedeni Ekle'));
    }

    public function postHurdanedeniekle() {
        try {
            $rules = ['nedeni' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $hurdanedeni = new HurdaNedeni;
            $neden = Input::get('nedeni');
            $tur = $this->servisid;
            if(HurdaNedeni::where('nedeni',$neden)->where('sayactur_id',$tur)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Hurda Nedeni Zaten Mevcut!', 'type' => 'warning'));
            }
            $hurdanedeni->nedeni = $neden;
            $hurdanedeni->sayactur_id = $tur;
            $hurdanedeni->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $neden . ' Tanımlı Hurda Nedeni Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Hurda Neden Numarası:' . $hurdanedeni->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/hurdaneden')->with(array('mesaj' => 'true', 'title' => 'Sayaç Hurda Nedeni Eklendi', 'text' => 'Sayaç Hurda Nedeni Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hurda Nedeni Eklenemedi', 'text' => 'Sayaç Hurda Nedeni Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getHurdanedeniduzenle($id) {
        $hurdanedeni = HurdaNedeni::find($id);
        return View::make($this->servisadi.'.hurdanedeniduzenle',array('hurdanedeni'=>$hurdanedeni))->with(array('title'=>'Sayaç Hurda Nedeni Düzenleme Ekranı'));
    }

    public function postHurdanedeniduzenle($id) {
        try {
            $rules = ['nedeni' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $hurdanedeni = HurdaNedeni::find($id);
            $bilgi = clone $hurdanedeni;
            $neden = Input::get('nedeni');
            if(HurdaNedeni::where('nedeni',$neden)->where('sayactur_id',$this->servisid)->where('id','<>',$id)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Hurda Nedeni Zaten Mevcut!', 'type' => 'warning'));
            }
            $hurdanedeni->nedeni = $neden;
            $hurdanedeni->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $bilgi->nedeni . ' Tanımlı Hurda Nedeni Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Hurda Neden Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/hurdaneden')->with(array('mesaj' => 'true', 'title' => 'Sayaç Hurda Nedeni Güncellendi', 'text' => 'Sayaç Hurda Nedeni Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hurda Nedeni Güncellenemedi', 'text' => 'Sayaç Hurda Nedeni Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getHurdanedenisil($id){
        try {
            DB::beginTransaction();
            $hurdanedeni = HurdaNedeni::find($id);
            if($hurdanedeni){
                $bilgi = clone $hurdanedeni;
                if ($hurdanedeni->kullanim > 0) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hurda Nedeni Silinemez', 'text' => 'Hurda Nedeni Arıza Kayıdında Kullanılmış.', 'type' => 'error'));
                }
                $hurdanedeni->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $bilgi->nedeni . ' Tanımlı Hurda Nedeni Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Hurda Neden Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hurda Nedeni Silindi', 'text' => 'Sayaç Hurda Nedeni Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hurda Nedeni Silinemedi', 'text' => 'Sayaç Hurda Nedeni Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Hurda Nedeni Silinemedi', 'text' => 'Sayaç Hurda Nedeni Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getUyarilar() {
        $uyarilar = Uyarilar::where('sayactur_id',$this->servisid)->get();
        return View::make($this->servisadi.'.uyarilar',array('uyarilar'=>$uyarilar))->with(array('title'=>$this->servisbilgi.' Sayaçları Uyarılar ve Sonuçlar'));
    }

    public function postUyarilarlist() {
        $query = Uyarilar::where('uyarilar.sayactur_id',$this->servisid)
            ->select(array("uyarilar.id","uyarilar.tanim","uyarilar.gdurum","uyarilar.ntanim","uyarilar.ndurum"));
        return Datatables::of($query)
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/uyariduzenle/".$model->id."'> Düzenle </a>".
                    "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getUyariekle() {
        return View::make($this->servisadi.'.uyariekle')->with(array('title'=>$this->servisbilgi.' Sayaç Uyarı ve Sonuç Ekle'));
    }

    public function postUyariekle() {
        try {
            $rules = ['tanim' => 'required', 'durum' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $uyari = new Uyarilar;
            $tanim = Input::get('tanim');
            $durum = Input::get('durum');
            $tur = $this->servisid;
            if(Uyarilar::where('tanim',$tanim)->where('sayactur_id',$tur)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Sayaç Uyarı & Sonuç Tanımı Zaten Mevcut!', 'type' => 'warning'));
            }
            $uyari->tanim = $tanim;
            $uyari->sayactur_id = $tur;
            $uyari->durum = $durum;
            $uyari->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $tanim . ' Tanımlı Uyarı & Sonuç Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Uyarı & Sonuç Numarası:' . $uyari->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/uyarilar')->with(array('mesaj' => 'true', 'title' => 'Sayaç Uyarı & Sonuç Tanımı Eklendi', 'text' => 'Sayaç Uyarı & Sonuç Tanımı Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Uyarı & Sonuç Tanımı Eklenemedi', 'text' => 'Sayaç Uyarı & Sonuç Tanımı Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getUyariduzenle($id) {
        $uyari = Uyarilar::find($id);
        return View::make($this->servisadi.'.uyariduzenle',array('uyari'=>$uyari))->with(array('title'=>$this->servisbilgi.' Sayaç Uyarı & Sonuç Tanımı Düzenleme Ekranı'));
    }

    public function postUyariduzenle($id) {
        try {
            $rules = ['tanim' => 'required', 'durum' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $uyari = Uyarilar::find($id);
            $bilgi = clone $uyari;
            $tanim = Input::get('tanim');
            $durum = Input::get('durum');
            if(Uyarilar::where('tanim',$tanim)->where('sayactur_id',$this->servisid)->where('id','<>',$id)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Sayaç Uyarı & Sonuç Tanımı Zaten Mevcut!', 'type' => 'warning'));
            }
            $uyari->tanim = $tanim;
            $uyari->durum = $durum;
            $uyari->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $bilgi->tanim . ' Tanımlı Uyarı & Sonuç Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Uyarı & Sonuç Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to($this->servisadi.'/uyarilar')->with(array('mesaj' => 'true', 'title' => 'Sayac Uyarı & Sonuç Tanımı Güncellendi', 'text' => 'Sayac Uyarı & Sonuç Tanımı Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Uyarı & Sonuç Tanımı Güncellenemedi', 'text' => 'Sayac Uyarı & Sonuç Tanımı Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getUyarisil($id){
        try {
            DB::beginTransaction();
            $uyari = Uyarilar::find($id);
            if($uyari){
                $bilgi = clone $uyari;
                if ($uyari->kullanim > 0) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Uyarı & Sonuç Tanımı Silinemez', 'text' => 'Uyarı & Sonuç Arıza Kayıdında Kullanılmış.', 'type' => 'error'));
                }
                $uyari->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $bilgi->tanim . ' Tanımlı Uyarı & Sonuç Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Uyarı & Sonuç Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Uyarı & Sonuç Tanımı Silindi', 'text' => 'Sayaç Uyarı & Sonuç Tanımı Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Uyarı & Sonuç Tanımı Silinemedi', 'text' => 'Sayaç Uyarı & Sonuç Tanımı Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Uyarı & Sonuç Tanımı Silinemedi', 'text' => 'Sayaç Uyarı & Sonuç Tanımı Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }
}
