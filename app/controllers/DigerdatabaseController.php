<?php
//transaction işlemi tamamlandı
class DigerdatabaseController extends BackendController {

    public function getSayacmarka() {
        return View::make('digerdatabase.sayacmarka')->with(array('title'=>'Sayaç Markaları'));
    }

    public function postMarkalist() {
        $query = SayacMarka::select(array("sayacmarka.id","sayacmarka.marka","sayacmarka.nmarka"));
        return Datatables::of($query)
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/digerdatabase/markaduzenle/".$model->id."'> Düzenle </a>".
                "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getMarkaekle() {
        return View::make('digerdatabase.markaekle')->with(array('title'=>'Sayaç Markası Ekle'));
    }
    
    public function postMarkaekle() {
        try {
            DB::beginTransaction();
            $adi = Input::get('adi');
            If (SayacMarka::onlyTrashed()->where('marka', $adi)->first()) {
                $marka = SayacMarka::onlyTrashed()->where('marka', $adi)->first();
                $marka->deleted_at = NULL;
                $marka->save();
                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $adi . ' İsimli Marka Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Marka Numarası:' . $marka->id);
                DB::commit();
                return Redirect::to('digerdatabase/sayacmarka')->with(array('mesaj' => 'true', 'title' => 'Sayaç Markası Geri Getirildi', 'text' => 'Sayaç Markası Eski Bilgileri Başarıyla Geri Getirildi.', 'type' => 'success'));
            }
            $rules = ['adi' => 'required|unique:sayacmarka,marka'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            if(SayacMarka::where('marka',$adi)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Sayaç Markası Zaten Mevcut!', 'type' => 'warning'));
            }
            $marka = new SayacMarka;
            $marka->marka = $adi;
            $marka->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $adi . ' İsimli Marka Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Marka Numarası:' . $marka->id);
            DB::commit();
            return Redirect::to('digerdatabase/sayacmarka')->with(array('mesaj' => 'true', 'title' => 'Sayac Markası Eklendi', 'text' => 'Sayac Markası Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Markası Eklenemedi', 'text' => 'Sayaç Markası Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }
    
    public function getMarkaduzenle($id) {
        $marka = SayacMarka::find($id);
        return View::make('digerdatabase.markaduzenle',array('marka'=>$marka))->with(array('title'=>'Sayaç Marka Düzenleme Ekranı'));
    }
    
    public function postMarkaduzenle($id) {
        try {
            DB::beginTransaction();
            $rules = ['adi' => 'required|unique:sayacmarka,marka,' . $id];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $marka = SayacMarka::find($id);
            $bilgi = clone $marka;
            $adi = Input::get('adi');
            if(SayacMarka::where('marka',$adi)->where('id','<>',$id)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Sayaç Markası Zaten Mevcut!', 'type' => 'warning'));
            }
            $marka->marka = $adi;
            $marka->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $bilgi->marka . ' İsimli Marka Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Marka Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to('digerdatabase/sayacmarka')->with(array('mesaj' => 'true', 'title' => 'Sayac Markası Güncellendi', 'text' => 'Sayac Markası Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Markası Güncellenemedi', 'text' => 'Sayac Markası Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));

        }
    }
    
    public function getMarkasil($id){
        try {
            DB::beginTransaction();
            $marka = SayacMarka::find($id);
            if($marka){
                $bilgi = clone $marka;
                if(SayacTip::where('sayacmarka_id',$marka->id)->first()){
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Silme Hatası', 'text' => 'Bu Sayaç Markası Sayaç Tiplerinde Kullanılmış!', 'type' => 'warning'));
                }
                $marka->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $bilgi->marka . ' İsimli Marka Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Marka Numarası:' . $bilgi->id);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Markası Silindi', 'text' => 'Sayaç Markası Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Markası Silinemedi', 'text' => 'Sayaç Markası Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Markası Silinemedi', 'text' => 'Sayaç Markası Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getServisyetkili() {
        return View::make('digerdatabase.servisyetkili')->with(array('title'=>'Servis Yetkili Kişiler'));
    }

    public function postServisyetkililist() {
        $query = ServisYetkili::select(array("servisyetkili.id","kullanici.adi_soyadi","servisyetkili.plasiyerkod","plasiyer.plasiyeradi","servisyetkili.netsiskullanici",
            "kullanici.nadi_soyadi","plasiyer.nplasiyeradi","servisyetkili.nnetsiskullanici"))
            ->leftjoin('kullanici','servisyetkili.kullanici_id','=','kullanici.id')
            ->leftjoin('plasiyer','servisyetkili.plasiyerkod','=','plasiyer.kodu');
        return Datatables::of($query)
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/digerdatabase/servisyetkiliduzenle/".$model->id."'> Düzenle </a>";
            })
            ->make(true);
    }

    public function getServisyetkiliekle() {
        $ozelkodlar=OzelKod::get(array('OZELKOD',DB::raw('dbo.TRK(ACIKLAMA) as ACIKLAMA')));
        $plasiyerler=Plasiyer::orderBy('kodu','asc')->get();
        $kullanicilar=Kullanici::orderBy('adi_soyadi','asc')->get();
        $depolar=NetsisDepolar::all();
        return View::make('digerdatabase.servisyetkiliekle',array('depolar'=>$depolar,'kullanicilar'=>$kullanicilar,'plasiyerler'=>$plasiyerler,'ozelkodlar'=>$ozelkodlar))->with(array('title'=>'Servis Yetkili Ekle'));
    }

    public function postServisyetkiliekle() {
        try {
            $rules = ['kullanici' => 'required', 'plasiyer' => 'required', 'ozelkod' => 'required', 'depo' => 'required', 'netsiskullanici' => 'required', 'netsiskullanicino' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $kullanici_id = Input::get('kullanici');
            $plasiyerkod = Input::get('plasiyer');
            $ozelkod = Input::get('ozelkod');
            $depo = Input::get('depo');
            $netsiskullanici = Input::get('netsiskullanici');
            $netsiskullanici_id = Input::get('netsiskullanicino');
            $kullanici = Kullanici::find($kullanici_id);
            if(ServisYetkili::where('kullanici_id',$kullanici_id)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Servis Yetkilisi Zaten Mevcut!', 'type' => 'warning'));
            }
            $servisyetkili = new ServisYetkili;
            $servisyetkili->kullanici_id = $kullanici_id;
            $servisyetkili->plasiyerkod = $plasiyerkod;
            $servisyetkili->ozelkod = $ozelkod;
            $servisyetkili->depokodu = $depo;
            $servisyetkili->netsiskullanici = $netsiskullanici;
            $servisyetkili->netsiskullanici_id = $netsiskullanici_id;
            $servisyetkili->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $kullanici->adi_soyadi . ' İsimli Kişi Servis Yetkilisi Olarak Eklendi.', 'Kayıt Yapan:' . Auth::user()->adi_soyadi . ',Yetkili Numarası:' . $servisyetkili->id);
            DB::commit();
            return Redirect::to('digerdatabase/servisyetkili')->with(array('mesaj' => 'true', 'title' => 'Servis Yetkilisi Kaydedildi', 'text' => 'Servis Yetkilisi Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Yetkilisi Kaydedilemedi', 'text' => 'Servis Yetkilisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getServisyetkiliduzenle($id) {
        $servisyetkili=ServisYetkili::find($id);
        $ozelkodlar=OzelKod::get(array('OZELKOD',DB::raw('dbo.TRK(ACIKLAMA) as ACIKLAMA')));
        $plasiyerler=Plasiyer::orderBy('kodu','asc')->get();
        $kullanicilar=Kullanici::orderBy('adi_soyadi','asc')->get();
        $depolar=NetsisDepolar::all();
        return View::make('digerdatabase.servisyetkiliduzenle',array('servisyetkili'=>$servisyetkili,'depolar'=>$depolar,'kullanicilar'=>$kullanicilar,'plasiyerler'=>$plasiyerler,'ozelkodlar'=>$ozelkodlar))->with(array('title'=>'Servis Yetkili Düzenleme Ekranı'));
    }

    public function postServisyetkiliduzenle($id) {
        try {
            $rules = ['kullanici' => 'required', 'plasiyer' => 'required', 'ozelkod' => 'required', 'depo' => 'required', 'netsiskullanici' => 'required', 'netsiskullanicino' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $kullanici_id = Input::get('kullanici');
            $plasiyerkod = Input::get('plasiyer');
            $ozelkod = Input::get('ozelkod');
            $depo = Input::get('depo');
            $netsiskullanici = Input::get('netsiskullanici');
            $netsiskullanici_id = Input::get('netsiskullanicino');
            $kullanici = Kullanici::find($kullanici_id);
            if(ServisYetkili::where('kullanici_id', $kullanici_id)->where('id', '<>', $id)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu bilgilere ait servis yetkilisi mevcut', 'type' => 'warning'));
            }
            $servisyetkili = ServisYetkili::find($id);
            $servisyetkili->kullanici_id = $kullanici_id;
            $servisyetkili->plasiyerkod = $plasiyerkod;
            $servisyetkili->ozelkod = $ozelkod;
            $servisyetkili->depokodu = $depo;
            $servisyetkili->netsiskullanici = $netsiskullanici;
            $servisyetkili->netsiskullanici_id = $netsiskullanici_id;
            $servisyetkili->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $kullanici->adi_soyadi . ' İsimli Kişi Servis Yetkilisi Olarak Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Yetkili Numarası:' . $servisyetkili->id);
            DB::commit();
            return Redirect::to('digerdatabase/servisyetkili')->with(array('mesaj' => 'true', 'title' => 'Servis Yetkilisi Güncellendi', 'text' => 'Servis Yetkilisi Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Yetkilisi Güncellenemedi', 'text' => 'Servis Yetkilisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getSayactur() {
        return View::make('digerdatabase.sayactur')->with(array('title'=>'Sayaç Türleri'));
    }

    public function postTurlist() {
        $query = SayacTur::select(array("sayactur.id","sayactur.tur","sayactur.ntur"));
        return Datatables::of($query)
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/digerdatabase/turduzenle/".$model->id."'> Düzenle </a>".
                "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getTurekle() {
        return View::make('digerdatabase.turekle')->with(array('title'=>'Sayaç Türü Ekle'));
    }
    
    public function postTurekle() {
        try {
            $rules = ['tur' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $sayactur = new SayacTur;
            $tur = Input::get('tur');
            if(SayacTur::where('tur',$tur)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Sayaç Türü Zaten Mevcut!', 'type' => 'warning'));
            }
            $sayactur->tur = $tur;
            $sayactur->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $tur . ' İsimli Sayaç Türü Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Sayaç Türü Numarası:' . $sayactur->id);
            DB::commit();
            return Redirect::to('digerdatabase/sayactur')->with(array('mesaj' => 'true', 'title' => 'Sayac Türü Eklendi', 'text' => 'Sayac Türü Başarıyla Eklendi', 'type' => 'success'));

        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Türü Eklenemedi', 'text' => 'Sayac Türü Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }
    
    public function getTurduzenle($id) {
        $sayactur = SayacTur::find($id);
        return View::make('digerdatabase.turduzenle',array('sayactur'=>$sayactur))->with(array('title'=>'Sayaç Türü Düzenleme Ekranı'));
    }
    
    public function postTurduzenle($id) {
        try {
            $rules = ['tur' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $sayactur = SayacTur::find($id);
            $bilgi = clone $sayactur;
            $tur = Input::get('tur');
            if(SayacTur::where('tur',$tur)->where('id','<>',$id)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Sayaç Türü Zaten Mevcut!', 'type' => 'warning'));
            }
            $sayactur->tur = $tur;
            $sayactur->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $bilgi->tur . ' İsimli Sayaç Türü Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Sayaç Türü Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to('digerdatabase/sayactur')->with(array('mesaj' => 'true', 'title' => 'Sayac Türü Güncellendi', 'text' => 'Sayac Türü Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Türü Güncellenemedi', 'text' => 'Sayac Türü Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }
    
    public function getTursil($id){
        try {
            DB::beginTransaction();
            $sayactur = SayacTur::find($id);
            if($sayactur){
                $bilgi = clone $sayactur;
                $sayacadi = SayacAdi::where('sayactur_id', $id)->first();
                if ($sayacadi) //sayactürü kullanılmış
                {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Türü Silinemez', 'text' => 'Sayaç Türü Sayaç İsimlerinde Kullanılmış.', 'type' => 'warning'));
                }
                $sayactur->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $bilgi->tur . ' İsimli Sayaç Türü Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Sayaç Türü Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Türü Silindi', 'text' => 'Sayaç Türü Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Türü Silinemedi', 'text' => 'Sayaç Türü Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Türü Silinemedi', 'text' => 'Sayaç Türü Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }
    
    public function getServisdurum() {
        return View::make('digerdatabase.servisdurum')->with(array('title'=>'Servis Durumları'));
    }

    public function postDurumlist() {
        $query = SayacDurum::select(array("sayacdurum.id","sayacdurum.durumadi","sayacdurum.gozel","sayacdurum.ndurumadi","sayacdurum.nozel"));
        return Datatables::of($query)
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/digerdatabase/durumduzenle/".$model->id."'> Düzenle </a>".
                "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getDurumekle() {
        return View::make('digerdatabase.durumekle')->with(array('title'=>'Servis Durumu Ekle'));
    }
    
    public function postDurumekle() {
        try {
            $rules = ['durum' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $sayacdurum = new SayacDurum;
            $durum = Input::get('durum');
            If (Input::has('ozel')) {
                $ozel = 1;
            } else {
                $ozel = 0;
            }
            if(SayacDurum::where('durumadi',$durum)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Servis Durumu Zaten Mevcut!', 'type' => 'warning'));
            }
            $sayacdurum->durumadi = $durum;
            $sayacdurum->ozel = $ozel;
            $sayacdurum->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $durum . ' İsimli Servis Durumu Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Durum Numarası:' . $sayacdurum->id);
            DB::commit();
            return Redirect::to('digerdatabase/servisdurum')->with(array('mesaj' => 'true', 'title' => 'Servis Durumu Eklendi', 'text' => 'Servis Durumu Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Durumu Eklenemedi', 'text' => 'Servis Durumu Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }
    
    public function getDurumduzenle($id) {
        $sayacdurum = SayacDurum::find($id);
        return View::make('digerdatabase.durumduzenle',array('sayacdurum'=>$sayacdurum))->with(array('title'=>'Servis Durumu Düzenleme Ekranı'));
    }
    
    public function postDurumduzenle($id) {
        try {
            $rules = ['durum' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $sayacdurum = SayacDurum::find($id);
            $bilgi = clone $sayacdurum;
            $durum = Input::get('durum');
            If (Input::has('ozel')) {
                $ozel = 1;
            } else {
                $ozel = 0;
            }
            if(SayacDurum::where('durumadi',$durum)->where('id','<>',$id)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Servis Durumu Zaten Mevcut!', 'type' => 'warning'));
            }
            $sayacdurum->durumadi = $durum;
            $sayacdurum->ozel = $ozel;
            $sayacdurum->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $bilgi->durumadi . ' İsimli Servis Durumu Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Durum Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to('digerdatabase/servisdurum')->with(array('mesaj' => 'true', 'title' => 'Servis Durumu Güncellendi', 'text' => 'Servis Durumu Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Durumu Güncellenemedi', 'text' => 'Servis Durumu Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }
    
    public function getDurumsil($id){
        try {
            DB::beginTransaction();
            $sayacdurum = SayacDurum::find($id);
            if($sayacdurum){
                $bilgi = clone $sayacdurum;
                if ($id < 12) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Durumu Silinemez', 'text' => 'Sayaçlara ait sabit durumlar silinemez.', 'type' => 'warning'));
                }
                if ($sayacdurum->ozel == 0) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Durumu Silinemez', 'text' => 'Sayaçlara ait genel durumlar silinemez.', 'type' => 'warning'));
                }
                $sayacdurum->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $bilgi->durumadi . ' İsimli Servis Durumu Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Durum Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Durumu Silindi', 'text' => 'Servis Durumu Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Durumu Silinemedi', 'text' => 'Servis Durumu Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Durumu Silinemedi', 'text' => 'Servis Durumu Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getNetsisstokkod() {
        return View::make('digerdatabase.netsisstokkod')->with(array('title'=>'Cari Stok Kodları'));
    }

    public function postStokkodlist() {
        $query = ServisStokKod::select(array("servisstokkod.id","servisstokkod.stokkodu","servisstokkod.stokadi","servis.servisadi","servisstokkod.servisbirimi",
            "servisstokkod.gdurum","servisstokkod.nstokadi","servis.nservisadi","servisstokkod.ndurum"))
        ->leftjoin("servis","servisstokkod.servisid","=","servis.id");
        return Datatables::of($query)
            ->editColumn('koddurum', function ($model) {
                return $model->koddurum==0 ? 'Pasif' : 'Aktif';
            })
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/digerdatabase/stokkodduzenle/".$model->id."'> Düzenle </a>";
            })
            ->make(true);
    }

    public function getStokkodduzenle($id) {
        $stokkod = ServisStokKod::find($id);
        $servisler = Servis::all();
        return View::make('digerdatabase.stokkodduzenle',array('stokkod'=>$stokkod,'servisler'=>$servisler))->with(array('title'=>'Servis Stok Kodu Düzenleme Ekranı'));
    }

    public function postStokkodduzenle($id) {
        try {
            DB::beginTransaction();
            $stokkod = ServisStokKod::find($id);
            $bilgi = clone $stokkod;
            $durum = Input::get('durum');
            $servis = Input::get('servis');
            $stokkod->servisid = $servis;
            $stokkod->koddurum = $durum;
            $stokkod->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $bilgi->stokadi . ' İsimli Stok Kodu Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Stok Kodu Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to('digerdatabase/netsisstokkod')->with(array('mesaj' => 'true', 'title' => 'Servis Stok Kodu Güncellendi', 'text' => 'Servis Stok Kodu Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Servis Stok Kodu Güncellenemedi', 'text' => 'Servis Stok Kodu Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getNetsiscari() {
        return View::make('digerdatabase.netsiscari')->with(array('title'=>'Cari Bilgiler'));
    }

    public function postCarilist() {
        $query = NetsisCari::select(array("netsiscari.id","netsiscari.carikod","netsiscari.cariadi","netsiscari.adres","netsiscari.il","netsiscari.ilce",
            "netsiscari.gcaritipi","netsiscari.gdurum","netsiscari.ncariadi","netsiscari.nadres","netsiscari.nil","netsiscari.nilce","netsiscari.ncaritipi","netsiscari.ndurum"));
        return Datatables::of($query)
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/digerdatabase/carigoster/".$model->id."'> Detay </a>";
            })
            ->make(true);
    }

    public function getCarigoster($id) {
        $netsiscari = NetsisCari::find($id);
        return View::make('digerdatabase.netsiscarigoster',array('netsiscari'=>$netsiscari))->with(array('title'=>'Netsis Cari Bilgisi Detayı'));
    }

    public function getSayacadicap(){
        try {
            $id=Input::get('id');
            $sayacadi = SayacAdi::find($id);
            $capdurum = $sayacadi->cap;
            return Response::json(array('durum'=>true,'capdurum' => $capdurum));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title' => 'Sayaç Bilgisi Getirilemedi','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

    public function getSayacadi(){
        try {
            $id=Input::get('id');
            $sayacadi = SayacAdi::where('sayactur_id', $id)->get();
            return Response::json(array('durum'=>true,'sayacadi' => $sayacadi));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title' => 'Sayaç Bilgisi Getirilemedi','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

    public function getSayaclar() {
        return View::make('digerdatabase.sayaclar')->with(array('title'=>'Türü Belli Olmayan Sayaçların Listesi'));
    }

    public function postSayaclist() {
        $query = Sayac::where('sayac.sayactur_id',null)
            ->select(array("sayac.id","sayac.serino","sayac.cihazno","uretimyer.yeradi","sayac.uretimtarihi","sayac.guretimtarihi","uretimyer.nyeradi"))
            ->leftjoin("uretimyer","sayac.uretimyer_id","=","uretimyer.id");
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
                return "<a class='btn btn-sm btn-warning' href='".$root."/digerdatabase/sayacduzenle/".$model->id."'> Düzenle </a>".
                "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getSayacduzenle($id) {
        $uretimyerleri = UretimYer::all();
        $sayacturleri = SayacTur::all();
        $sayacadlari = SayacAdi::all();
        $sayaccaplari = SayacCap::all();
        $sayac = Sayac::find($id);
        return View::make('digerdatabase.sayacduzenle',array('sayac'=>$sayac,'sayacturleri'=>$sayacturleri,'uretimyerleri'=>$uretimyerleri,'sayacadlari'=>$sayacadlari,'sayaccaplari'=>$sayaccaplari))->with(array('title'=>'Sayaç Düzenleme Ekranı'));
    }

    public function postSayacduzenle($id) {
        try {
            $rules = ['uretimtarihi' => 'required', 'uretimyer' => 'required', 'sayactur' => 'required', 'sayacadi' => 'required'];
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
            $sayactur_id = Input::get('sayactur');
            $sayacadi_id = Input::get('sayacadi');
            if (Input::has('sayaccap')) {
                $sayaccap_id = Input::get('sayaccap');
            } else {
                $sayaccap_id = 1;
            }
            DB::beginTransaction();
            if (Sayac::where('serino', $serino)->where('uretimyer_id', $uretimyer_id)->where('id', '<>', $id)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaç Güncellenemedi', 'text' => 'Seri No zaten bu yere ait mevcut', 'type' => 'error'));
            } else {
                $sayac = Sayac::find($id);
                $bilgi = clone $sayac;
                $sayac->serino = $serino;
                $sayac->uretimyer_id = $uretimyer_id;
                $sayac->uretimtarihi = $uretimtarih;
                $sayac->sayactur_id = $sayactur_id;
                $sayac->sayacadi_id = $sayacadi_id;
                $sayac->sayaccap_id = $sayaccap_id;
                $sayac->save();
                BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $bilgi->serino . ' Seri Nolu Sayaç Bilgisi Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Sayaç Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::to('digerdatabase/sayaclar')->with(array('mesaj' => 'true', 'title' => 'Sayac Güncellendi', 'text' => 'Sayac Başarıyla Güncellendi', 'type' => 'success'));
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
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $bilgi->serino . ' Seri Nolu Sayaç Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Sayaç Numarası:' . $bilgi->id);
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

    public function getSubeyetkili() {
        return View::make('digerdatabase.subeyetkili')->with(array('title'=>'Şube subeyetkili Kişiler'));
    }

    public function postSubeyetkililist() {
        $query = SubeYetkili::select(array("subeyetkili.id","kullanici.adi_soyadi","netsiscari.cariadi","subeyetkili.subekodu","subeyetkili.gaktif",
            "kullanici.nadi_soyadi","netsiscari.ncariadi","subeyetkili.naktif"))
            ->leftjoin('netsiscari','subeyetkili.netsiscari_id','=','netsiscari.id')
            ->leftJoin('kullanici','subeyetkili.kullanici_id','=','kullanici.id');
        return Datatables::of($query)
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/digerdatabase/subeyetkiliduzenle/".$model->id."'> Düzenle </a>";
            })
            ->make(true);
    }

    public function getSubeyetkiliekle() {
        $projekodlari=Proje::get(array('PROJE_KODU',DB::raw('dbo.TRK(PROJE_ACIKLAMA) as PROJE_ACIKLAMA')));
        $subekodlari=NetsisSube::where('ISLETME_KODU',1)->where('SUBE_KODU','<>',0)->get(array('SUBE_KODU',DB::raw('dbo.TRK(UNVAN) as UNVAN')));
        $netsiscariler = NetsisCari::where('caridurum','A')->whereIn('caritipi',array('A','D'))->orderBy('carikod','asc')->get();
        $kullanicilar=Kullanici::whereIn('grup_id',array(17))->where('aktifdurum',1)->get();
        return View::make('digerdatabase.subeyetkiliekle',array('netsiscariler'=>$netsiscariler,'kullanicilar'=>$kullanicilar,'projekodlari'=>$projekodlari,'subekodlari'=>$subekodlari))->with(array('title'=>'Şube Yetkili Ekle'));
    }

    public function postSubeyetkiliekle() {
        try {
            $rules = ['kullanici' => 'required', 'netsiscari' => 'required', 'durum' => 'required', 'email' => 'email', 'projekodu' => 'required', 'subekod' => 'required', 'belgeonek' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $kullanici_id = Input::get('kullanici');
            $netsiscari = Input::get('netsiscari');
            $projekodu = Input::get('projekodu');
            $subekodu = Input::get('subekod');
            $onek = Input::get('belgeonek');
            $durum = Input::get('durum');
            $email = Input::get('email');
            $telefon = Input::get('telefon');
            DB::beginTransaction();
            $kullanici = Kullanici::find($kullanici_id);
            if ( SubeYetkili::where('kullanici_id', $kullanici_id)->where('netsiscari_id', $netsiscari)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu şube yetkili adı bu cari ile zaten eşleştirilmiş', 'type' => 'warning'));
            }
            $subeyetkili = new SubeYetkili;
            $subeyetkili->email = $email;
            $subeyetkili->telefon = $telefon;
            $subeyetkili->netsiscari_id = $netsiscari;
            $subeyetkili->projekodu = $projekodu;
            $subeyetkili->subekodu = $subekodu;
            $subeyetkili->kullanici_id = $kullanici_id;
            $subeyetkili->belgeonek = $onek;
            $subeyetkili->aktif = $durum;
            $subeyetkili->save();

            $yetkili = Yetkili::where('kullanici_id', $kullanici_id)->where('netsiscari_id', $netsiscari)->first();
            if ($yetkili) {
                $yetkili->aktif = $durum;
                $yetkili->save();
            }else{
                $yetkili = new Yetkili;
                $yetkili->email = $email;
                $yetkili->telefon = $telefon;
                $yetkili->netsiscari_id = $netsiscari;
                $yetkili->kullanici_id = $kullanici_id;
                $yetkili->aktif = $durum;
                $yetkili->save();
            }
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $kullanici->adi_soyadi . ' İsimli Kullanıcı Şube Yetkilisi Olarak Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Şube Yetkili Numarası:' . $subeyetkili->id);
            DB::commit();
            return Redirect::to('digerdatabase/subeyetkili')->with(array('mesaj' => 'true', 'title' => 'Şube Yetkili Elemanı Kaydedildi', 'text' => 'Şube Yetkili Elemanı Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube Yetkili Elemanı Kaydedilemedi', 'text' => 'Şube Yetkili Elemanı Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getSubeyetkiliduzenle($id) {
        $subeyetkili=SubeYetkili::find($id);
        $subeyetkililer=SubeYetkili::where('aktif',1)->where('kullanici_id','<>',$subeyetkili->kullanici_id)->get(array('kullanici_id'))->toArray();
        $projekodlari=Proje::get(array('PROJE_KODU',DB::raw('dbo.TRK(PROJE_ACIKLAMA) as PROJE_ACIKLAMA')));
        $subekodlari=NetsisSube::where('ISLETME_KODU',1)->where('SUBE_KODU','<>',0)->get(array('SUBE_KODU',DB::raw('dbo.TRK(UNVAN) as UNVAN')));
        $netsiscariler = NetsisCari::where(function($query){$query->where('caridurum','A')->whereIn('caritipi',array('A','D'));})->orWhere('id',$subeyetkili->netsiscari_id)->orderBy('carikod','asc')->get();
        $kullanicilar=Kullanici::whereIn('grup_id',array(17))->where('aktifdurum',1)->whereNotIn('id',$subeyetkililer)->get();
        return View::make('digerdatabase.subeyetkiliduzenle',array('subeyetkili'=>$subeyetkili,'netsiscariler'=>$netsiscariler,'kullanicilar'=>$kullanicilar,'projekodlari'=>$projekodlari,'subekodlari'=>$subekodlari))->with(array('title'=>'Şube Yetkili Düzenleme Ekranı'));
    }

    public function postSubeyetkiliduzenle($id) {
        try {
            $rules = ['kullanici' => 'required', 'netsiscari' => 'required', 'durum' => 'required', 'email' => 'email', 'projekodu' => 'required', 'subekod' => 'required', 'belgeonek' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $kullanici_id = Input::get('kullanici');
            $netsiscari = Input::get('netsiscari');
            $projekodu = Input::get('projekodu');
            $subekodu = Input::get('subekod');
            $onek = Input::get('belgeonek');
            $durum = Input::get('durum');
            $email = Input::get('email');
            $telefon = Input::get('telefon');
            DB::beginTransaction();
            $kullanici = Kullanici::find($kullanici_id);
            if (SubeYetkili::where('kullanici_id', $kullanici_id)->where('netsiscari_id', $netsiscari)->where('id', '<>', $id)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu yetkili adı bu cari ile zaten eşleştirilmiş', 'type' => 'warning'));
            }
            $subeyetkili = SubeYetkili::find($id);
            $bilgi = clone $subeyetkili;
            $subeyetkili->email = $email;
            $subeyetkili->telefon = $telefon;
            $subeyetkili->netsiscari_id = $netsiscari;
            $subeyetkili->projekodu = $projekodu;
            $subeyetkili->subekodu = $subekodu;
            $subeyetkili->kullanici_id = $kullanici_id;
            $subeyetkili->belgeonek = $onek;
            $subeyetkili->aktif = $durum;
            $subeyetkili->save();

            $yetkili = Yetkili::where('kullanici_id', $bilgi->kullanici_id)->where('netsiscari_id', $bilgi->netsiscari_id)->first();
            if ($yetkili) {
                $yetkili->netsiscari_id = $netsiscari;
                $yetkili->kullanici_id = $kullanici_id;
                $yetkili->aktif = $durum;
                $yetkili->save();
            }else{
                $yetkili = new Yetkili;
                $yetkili->email = $email;
                $yetkili->telefon = $telefon;
                $yetkili->netsiscari_id = $netsiscari;
                $yetkili->kullanici_id = $kullanici_id;
                $yetkili->aktif = $durum;
                $yetkili->save();
            }

            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $kullanici->adi_soyadi . ' İsimli Kullanıcı Şube Yetkilisi Olarak Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Şube Yetkili Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to('digerdatabase/subeyetkili')->with(array('mesaj' => 'true', 'title' => 'Şube Yetkili Elemanı Güncellendi', 'text' => 'Şube Yetkili Elemanı Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube Yetkili Elemanı Güncellenemedi', 'text' => 'Şube Yetkili Elemanı Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function postSubelist() {
        $query = Sube::select(array("sube.id","sube.adi","netsiscari.cariadi","sube.subekodu","netsisdepolar.kodu","sube.gaktif","sube.nadi","netsiscari.ncariadi","sube.naktif"))
            ->leftjoin("netsisdepolar","sube.netsisdepolar_id","=","netsisdepolar.id")
            ->leftjoin("netsiscari","sube.netsiscari_id","=","netsiscari.id");
        return Datatables::of($query)
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/digerdatabase/subeduzenle/".$model->id."'> Düzenle </a>".
                "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getSube() {
        return View::make('digerdatabase.sube')->with(array('title'=>'Şubeler'));
    }

    public function getSubeekle() {
        $netsiscariler = NetsisCari::where('caridurum','A')->whereIn('caritipi',array('A','D'))->orderBy('carikod','asc')->get();
        $netsisdepolar=NetsisDepolar::all();
        $subekodlari=NetsisSube::where('ISLETME_KODU',1)->where('SUBE_KODU','<>',0)->get(array('SUBE_KODU',DB::raw('dbo.TRK(UNVAN) as UNVAN')));
        $sayacturleri=SayacTur::all();
        $sayacadlari=SayacAdi::all();
        return View::make('digerdatabase.subeekle',array('netsisdepolar'=>$netsisdepolar,'subekodlari'=>$subekodlari,'netsiscariler'=>$netsiscariler,'sayacturleri'=>$sayacturleri,'sayacadlari'=>$sayacadlari))->with(array('title'=>'Şube ve Deposu Ekle'));
    }

    public function postSubeekle() {
        try {
            $rules = ['adi' => 'required', 'netsiscari' => 'required','subekod'=>'required', 'depo' => 'required', 'sayactur' => 'required', 'sayacadi' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $netsiscari = Input::get('netsiscari');
            $netsisdepo = Input::get('depo');
            $subekodu = Input::get('subekod');
            $adi = Input::get('adi');
            $subelinked = Input::get('linkedserver');
            $linkeddurum = Input::get('linkeddurum');
            $bellinked = Input::get('linkedserverabone');
            $linkedabonedurum = Input::get('linkedabonedurum');
            $sayacturler = Input::get('sayactur');
            $sayacadlari = Input::get('sayacadi');
            $aktif = Input::get('aktif');
            $turler = "";
            foreach ($sayacturler as $sayactur) {
                $turler .= ($turler == "" ? "" : ",") . $sayactur;
            }
            $adlar = "";
            foreach ($sayacadlari as $sayacadi) {
                $adlar .= ($adlar == "" ? "" : ",") . $sayacadi;
            }
            DB::beginTransaction();
            if (Sube::where('netsiscari_id', $netsiscari)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu şube bilgileri zaten kayıtlı', 'type' => 'error'));
            }
            $sube = new Sube;
            $sube->adi = $adi;
            $sube->netsiscari_id = $netsiscari;
            $sube->netsisdepolar_id = $netsisdepo;
            $sube->subekodu = $subekodu;
            $sube->sayactur = $turler;
            $sube->sayacadlari = $adlar;
            $sube->subelinked = $linkeddurum ? $subelinked : "";
            $sube->bellinked = $linkedabonedurum ? $bellinked : "";
            $sube->aktif = $aktif;
            $sube->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $adi . ' İsimli Şube Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Şube Numarası:' . $sube->id);
            DB::commit();
            return Redirect::to('digerdatabase/sube')->with(array('mesaj' => 'true', 'title' => 'Şube ve Deposu Eklendi', 'text' => 'Şube ve Deposu Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube ve Deposu Eklenemedi', 'text' => 'Şube ve Deposu Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getSubeduzenle($id) {
        $sube = Sube::find($id);
        $netsiscariler = NetsisCari::where(function($query){$query->where('caridurum','A')->whereIn('caritipi',array('A','D'));})->orWhere('id',$sube->netsiscari_id)->orderBy('carikod','asc')->get();
        $netsisdepolar=NetsisDepolar::all();
        $subekodlari=NetsisSube::where('ISLETME_KODU',1)->where('SUBE_KODU','<>',0)->get(array('SUBE_KODU',DB::raw('dbo.TRK(UNVAN) as UNVAN')));
        $sayacturleri=SayacTur::all();
        $sayacadlari=SayacAdi::all();
        $sube->linkeddurum = DigerdatabaseController::getLinkedInfo($sube->subelinked);
        $sube->linkedabonedurum = DigerdatabaseController::getLinkedInfo($sube->bellinked);
        return View::make('digerdatabase.subeduzenle',array('sube'=>$sube,'netsisdepolar'=>$netsisdepolar,'subekodlari'=>$subekodlari,'netsiscariler'=>$netsiscariler,'sayacturleri'=>$sayacturleri,'sayacadlari'=>$sayacadlari))->with(array('title'=>'Şube ve Deposu Düzenleme Ekranı'));
    }

    public function postSubeduzenle($id) {
        try {
            $rules = ['adi' => 'required', 'netsiscari' => 'required','subekod'=>'required', 'depo' => 'required', 'sayactur' => 'required', 'sayacadi' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $netsiscari = Input::get('netsiscari');
            $netsisdepo = Input::get('depo');
            $subekodu = Input::get('subekod');
            $adi = Input::get('adi');
            $subelinked = Input::get('linkedserver');
            $linkeddurum = Input::get('linkeddurum');
            $bellinked = Input::get('linkedserverabone');
            $linkedabonedurum = Input::get('linkedabonedurum');
            $sayacturler = Input::get('sayactur');
            $sayacadlari = Input::get('sayacadi');
            $aktif = Input::get('aktif');
            $turler = "";
            foreach ($sayacturler as $sayactur) {
                $turler .= ($turler == "" ? "" : ",") . $sayactur;
            }
            $adlar = "";
            foreach ($sayacadlari as $sayacadi) {
                $adlar .= ($adlar == "" ? "" : ",") . $sayacadi;
            }
            DB::beginTransaction();
            if (Sube::where('netsiscari_id', $netsiscari)->where('id', '<>', $id)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu şube ve deposu zaten kayıtlı', 'type' => 'error'));
            }
            $sube = Sube::find($id);
            $bilgi = clone $sube;
            $sube->adi = $adi;
            $sube->netsiscari_id = $netsiscari;
            $sube->netsisdepolar_id = $netsisdepo;
            $sube->subekodu = $subekodu;
            $sube->sayactur = $turler;
            $sube->sayacadlari = $adlar;
            $sube->subelinked = $linkeddurum ? $subelinked : "";
            $sube->bellinked = $linkedabonedurum ? $bellinked : "";
            $sube->aktif = $aktif;
            $sube->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $bilgi->adi . ' İsimli Şube Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Şube Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to('digerdatabase/sube')->with(array('mesaj' => 'true', 'title' => 'Şube ve Deposu Güncellendi', 'text' => 'Şube ve Deposu Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube ve Deposu Güncellenemedi', 'text' => 'Şube ve Deposu Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getSubesil($id){
        try {
            DB::beginTransaction();
            $sube = Sube::find($id);
            if($sube){
                $bilgi = clone $sube;
                $sayacgelen = SayacGelen::where('netsiscari_id', $sube->netsiscari_id)->where('servis_id', 6)->get();
                if ($sayacgelen->count() > 0) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube ve Deposu Silinemez', 'text' => 'Şube ve Deposunda Kayıtlı sayaç var.', 'type' => 'error'));
                }
                $sube->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $bilgi->adi . ' İsimli Şube Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Şube Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube ve Deposu Silindi', 'text' => 'Şube ve Deposu Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube ve Deposu Silinemedi', 'text' => 'Şube Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube ve Deposu Silinemedi', 'text' => 'Şube ve Deposu Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function postCariyerlist() {
        $query = CariYer::select(array("cariyer.id","uretimyer.yeradi","netsiscari.cariadi","kullanici.adi_soyadi","cariyer.gdurum","cariyer.updated_at",
        "cariyer.gdurumtarihi","uretimyer.nyeradi","netsiscari.ncariadi","kullanici.nadi_soyadi","cariyer.ndurum"))
            ->leftjoin("uretimyer","cariyer.uretimyer_id","=","uretimyer.id")
            ->leftjoin("netsiscari","cariyer.netsiscari_id","=","netsiscari.id")
            ->leftjoin("kullanici","cariyer.kullanici_id","=","kullanici.id");
        return Datatables::of($query)
            ->editColumn('updated_at', function ($model) {
                $date = new DateTime($model->updated_at);
                return $date->format('d-m-Y');
            })
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/digerdatabase/cariyerduzenle/".$model->id."'> Düzenle </a>";
            })
            ->make(true);
    }

    public function getCariyer() {
        return View::make('digerdatabase.cariyer')->with(array('title'=>'Netsis Cari ve Üretim Yeri Bilgileri '));
    }

    public function getCariyerekle() {
        $netsiscariler = NetsisCari::where('caridurum','A')->whereIn('caritipi',array('A','D'))->orderBy('carikod','asc')->get();
        $uretimyerleri=UretimYer::where('id','<>',0)->get();
        return View::make('digerdatabase.cariyerekle',array('uretimyerleri'=>$uretimyerleri,'netsiscariler'=>$netsiscariler))->with(array('title'=>'Cari Üretim Yeri Eşleştirme Ekle'));
    }

    public function postCariyerekle() {
        try {
            $rules = ['uretimyer' => 'required', 'netsiscari' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $netsiscari_id = Input::get('netsiscari');
            $uretimyer_id = Input::get('uretimyer');
            $durum = Input::get('durum');
            DB::beginTransaction();
            $netsiscari = NetsisCari::find($netsiscari_id);
            $uretimyer = UretimYer::find($uretimyer_id);
            if (CariYer::where('netsiscari_id', $netsiscari_id)->where('uretimyer_id', $uretimyer_id)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu eşleştirme zaten kayıtlı', 'type' => 'error'));
            }
            $cariyer = new CariYer;
            $cariyer->uretimyer_id = $uretimyer_id;
            $cariyer->netsiscari_id = $netsiscari_id;
            $cariyer->durum = $durum;
            $cariyer->kullanici_id = Auth::user()->id;
            $cariyer->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $netsiscari->cariadi . '-' . $uretimyer->yeradi . ' Cari Üretim Yeri Eşleştirmesi Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Cari Yer Numarası:' . $cariyer->id);
            DB::commit();
            return Redirect::to('digerdatabase/cariyer')->with(array('mesaj' => 'true', 'title' => 'Netsis Cari ve Üretim Yeri Eşleştirmesi Eklendi', 'text' => 'Netsis Cari ve Üretim Yeri Eşleştirmesi Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari ve Üretim Yeri Eşleştirmesi  Eklenemedi', 'text' => 'Netsis Cari ve Üretim Yeri Eşleştirmesi  Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getCariyerduzenle($id) {
        $cariyer = CariYer::find($id);
        $netsiscariler = NetsisCari::where(function($query){$query->where('caridurum','A')->whereIn('caritipi',array('A','D'));})->orWhere('id',$cariyer->netsiscari_id)->orderBy('carikod','asc')->get();
        $uretimyer=Uretimyer::find($cariyer->uretimyer_id);
        return View::make('digerdatabase.cariyerduzenle',array('cariyer'=>$cariyer,'uretimyer'=>$uretimyer,'netsiscariler'=>$netsiscariler))->with(array('title'=>'Cari Üretim Yeri Eşleştirme Düzenleme Ekranı'));
    }

    public function postCariyerduzenle($id) {
        try {
            $rules = ['netsiscari' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $cariyer = CariYer::find($id);
            $bilgi = clone $cariyer;
            $netsiscari_id = Input::get('netsiscari');
            $durum=Input::get('durum');
            $netsiscari = NetsisCari::find($netsiscari_id);
            $uretimyer = UretimYer::find($cariyer->uretimyer_id);
            if (CariYer::where('netsiscari_id', $netsiscari_id)->where('uretimyer_id', $cariyer->uretimyer_id)->where('id', '<>', $id)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu eşleştirme zaten kayıtlı', 'type' => 'error'));
            }
            $cariyer->netsiscari_id = $netsiscari_id;
            $cariyer->durum = $durum;
            $cariyer->kullanici_id = Auth::user()->id;
            $cariyer->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $netsiscari->cariadi . '-' . $uretimyer->yeradi . ' Cari Üretim Yeri Eşleştirmesi Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Cari Yer Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to('digerdatabase/cariyer')->with(array('mesaj' => 'true', 'title' => 'Netsis Cari ve Üretim Yeri Eşleştirmesi Güncellendi', 'text' => 'Netsis Cari ve Üretim Yeri Eşleştirmesi Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari ve Üretim Yeri Eşleştirmesi Güncellenemedi', 'text' => 'Netsis Cari ve Üretim Yeri Eşleştirmesi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getParcaucret() {
        return View::make('digerdatabase.parcaucret')->with(array('title'=>'Toplu Sayaç Parça Ücretleri'));
    }

    public function postParcaucretlist() {
        $query = Fiyat::select(array("sayactur.tur","uretimyer.yeradi",DB::raw('count(*) as sayi'),"parabirimi.birimi","sayactur.ntur","uretimyer.nyeradi","parabirimi.nbirimi",
            "fiyat.sayactur_id","fiyat.uretimyer_id"))
            ->leftjoin("uretimyer","fiyat.uretimyer_id","=","uretimyer.id")
            ->leftjoin("degisenler","fiyat.degisenler_id","=","degisenler.id")
            ->leftjoin("sayactur","fiyat.sayactur_id","=","sayactur.id")
            ->leftjoin("parabirimi","fiyat.parabirimi_id","=","parabirimi.id")
            ->groupBy(array("sayactur.tur","uretimyer.yeradi","parabirimi.birimi","sayactur.ntur","uretimyer.nyeradi","parabirimi.nbirimi","fiyat.sayactur_id","fiyat.uretimyer_id"));
        return Datatables::of($query)
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/digerdatabase/ucretduzenle/".$model->sayactur_id."/".$model->uretimyer_id."'> Düzenle </a>".
                    "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->sayactur_id."' data-id2='".$model->uretimyer_id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }

    public function getUcretekle() {
        $uretimyerleri = UretimYer::where('id','<>',0)->get();
        $sayacturleri = SayacTur::all();
        $sayaccaplari = SayacCap::all();
        $genel = UretimYer::find(0);
        return View::make('digerdatabase.ucretekle',array('uretimyerleri'=>$uretimyerleri,'sayacturleri'=>$sayacturleri,'sayaccaplari'=>$sayaccaplari,'genel'=>$genel))->with(array('title'=>'Toplu Sayaç Parça Ücreti Ekle'));
    }

    public function getSayacadilist(){
        try {
            $turid = Input::get('id');
            $sayacadlari = SayacAdi::where('sayactur_id', $turid)->get();
            return Response::json(array('durum' => true, 'sayacadlari' => $sayacadlari));
        } catch (Exception $e) {
            return Response::json(array('durum'=>false,'title' => 'Sayaca Ait Parçalar Getirilirken Hata oluştu','text'=>str_replace("'","\'",$e->getMessage()) ));
        }
    }

    public function getSayacparcalari(){
        try {
            $uretimyerid = Input::get('uretimyerid');
            $adiid=Input::get('adiid');
            $turid=Input::get('turid');
            if($adiid==0){ //hepsi
                $sayacparca=array();
            }else if (Input::has('capid')) {
                $capid=Input::get('capid');
                $sayacparca = SayacParca::where('sayacadi_id', $adiid)->where('sayaccap_id', $capid)->first();
            } else {
                $sayacparca = SayacParca::where('sayacadi_id', $adiid)->where('sayaccap_id', 1)->first();
            }
            if ($sayacparca) {
                $sayacparcalar = explode(',', $sayacparca->parcalar);
                $parcalar = Degisenler::whereIn('id', $sayacparcalar)->get();
                foreach ($parcalar as $parca){
                    $parca->genel=Fiyat::where('degisenler_id',$parca->id)->where('uretimyer_id',0)->first();
                    if($parca->genel)
                        $parca->genelparabirimi=ParaBirimi::find($parca->genel->parabirimi_id);
                    else
                        $parca->genelparabirimi=BackendController::getGenelParabirimi();
                    $parca->ozel=Fiyat::where('degisenler_id',$parca->id)->where('uretimyer_id',$uretimyerid)->first();
                    if($parca->ozel)
                        $parca->ozelparabirimi=ParaBirimi::find($parca->ozel->parabirimi_id);
                    else{
                        $parca->ozel = null;
                        $parca->ozelparabirimi=BackendController::getOzelParabirimi($uretimyerid);
                    }
                }
            }else if($adiid==0) {
                $parcalar = Degisenler::where('sayactur_id', $turid)->get();
                foreach ($parcalar as $parca){
                    $parca->genel=Fiyat::where('degisenler_id',$parca->id)->where('uretimyer_id',0)->first();
                    if($parca->genel)
                        $parca->genelparabirimi=ParaBirimi::find($parca->genel->parabirimi_id);
                    else
                        $parca->genelparabirimi=BackendController::getGenelParabirimi();
                    $parca->ozel=Fiyat::where('degisenler_id',$parca->id)->where('uretimyer_id',$uretimyerid)->first();
                    if($parca->ozel)
                        $parca->ozelparabirimi=ParaBirimi::find($parca->ozel->parabirimi_id);
                    else
                        $parca->ozelparabirimi=BackendController::getOzelParabirimi($uretimyerid);
                }
            }else{
                $parcalar = array();
            }
            return Response::json(array('durum'=>true,'parcalar' => $parcalar));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'type'=>'error','title' => 'Sayaca Ait Parçalar Getirilirken Hata oluştu','text'=>str_replace("'","\'",$e->getMessage()) ));
        }
    }

    public function postUcretekle() {
        try {
            $rules = ['uretimyer' => 'required', 'parca' => 'required', 'genel' => 'required', 'ozel' => 'required', 'sayactur' => 'required', 'sayacadi' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $sayacturid=Input::get('sayactur');
            $uretimyerid=Input::get('uretimyer');
            $uretimyer=UretimYer::find($uretimyerid);
            $parca=Input::get('parca');
            $ozelfiyat=Input::get('ozel');
            if(Fiyat::where('uretimyer_id',$uretimyerid)->whereIn('degisenler_id',$parca)->get()->count()>0)
            {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu yere ait seçilen parçaların ücreti mevcut.', 'type' => 'error'));
            }

            for ($i=0;$i<count($parca);$i++)
            {
                $flag=0;
                $parcaucret=Fiyat::where('uretimyer_id', $uretimyerid)->where('degisenler_id', $parca[$i])->first();
                if($parcaucret) {
                    if($parcaucret->fiyat!=doubleval($ozelfiyat[$i])){
                        $parcaucret->fiyat = doubleval($ozelfiyat[$i]);
                        $parcaucret->save();
                        $flag=1;
                    }
                }else{
                    $parcaucret = new Fiyat;
                    $parcaucret->fiyat = doubleval($ozelfiyat[$i]);
                    $parcaucret->uretimyer_id = $uretimyerid;
                    $parcaucret->degisenler_id = $parca[$i];
                    $parcaucret->sayactur_id = $sayacturid;
                    $parcaucret->parabirimi_id = $uretimyer->parabirimi_id;
                    $parcaucret->save();
                    $flag=1;
                }
                if($flag){
                    $sonuc = BackendController::FiyatGuncelle($parcaucret);
                    if(!$sonuc['durum']){
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Parça Ücreti Eklenemedi', 'text' => 'Parça Ücreti Tüm Fiyatlandırma Bekleyenlere Eklenemedi.', 'type' => 'error'));
                    }
                }
            }
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $uretimyer->yeradi . ' Yerine Ait Sayaç Parçalarına Fiyat Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Parça Sayısı:' . count($parca));
            DB::commit();
            return Redirect::to('digerdatabase/parcaucret')->with(array('mesaj' => 'true', 'title' => 'Toplu Parça Ücreti Eklendi', 'text' => 'Toplu Parça Ücreti Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Parça Ücreti Eklenemedi', 'text' => 'Parça Ücreti Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getUcretduzenle($sayacturid,$uretimyerid) {
        $uretimyer = UretimYer::find($uretimyerid);
        $sayactur = SayacTur::find($sayacturid);
        $sayaccaplari = SayacCap::all();
        $parcalar = Degisenler::where('sayactur_id',$sayacturid)->where('parcadurum','<>',2)->get();
        foreach ($parcalar as $parca){
            $parca->genel=Fiyat::where('degisenler_id',$parca->id)->where('uretimyer_id',0)->first();
            $parca->ozel=Fiyat::where('degisenler_id',$parca->id)->where('uretimyer_id',$uretimyerid)->first();
        }
        $genel = UretimYer::find(0);
        return View::make('digerdatabase.ucretduzenle',array('uretimyer'=>$uretimyer,'sayactur'=>$sayactur,'parcalar'=>$parcalar,'sayaccaplari'=>$sayaccaplari,'genel'=>$genel))->with(array('title'=>'Toplu Sayaç Parça Ücreti Düzenleme Ekranı'));
    }

    public function postUcretduzenle($sayacturid,$uretimyerid) {
        try {
            $rules = ['uretimyer' => 'required', 'parca' => 'required', 'genel' => 'required', 'ozel' => 'required', 'sayactur' => 'required', 'sayacadi' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $uretimyer=UretimYer::find($uretimyerid);
            $parca=Input::get('parca');
            $ozelfiyat=Input::get('ozel');
            for ($i=0;$i<count($parca);$i++)
            {
                $flag=0;
                $parcaucret=Fiyat::where('uretimyer_id', $uretimyerid)->where('degisenler_id', $parca[$i])->first();
                if($parcaucret) {
                    if($parcaucret->fiyat!=doubleval($ozelfiyat[$i])){
                        $parcaucret->fiyat = doubleval($ozelfiyat[$i]);
                        $parcaucret->save();
                        $flag=1;
                    }
                }else{
                    $parcaucret = new Fiyat;
                    $parcaucret->fiyat = doubleval($ozelfiyat[$i]);
                    $parcaucret->uretimyer_id = $uretimyerid;
                    $parcaucret->degisenler_id = $parca[$i];
                    $parcaucret->sayactur_id = $sayacturid;
                    $parcaucret->parabirimi_id = $uretimyer->parabirimi_id;
                    $parcaucret->save();
                    $flag=1;
                }
                if($flag){
                    $sonuc = BackendController::FiyatGuncelle($parcaucret);
                    if(!$sonuc['durum']){
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Parça Ücreti Eklenemedi', 'text' => 'Parça Ücreti Tüm Fiyatlandırma Bekleyenlere Eklenemedi.', 'type' => 'error'));
                    }
                }
            }
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $uretimyer->yeradi . ' Yerine Ait Sayaç Parçalarının Fiyatı Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Parça Sayısı:' . count($parca));
            DB::commit();
            return Redirect::to('digerdatabase/parcaucret')->with(array('mesaj' => 'true', 'title' => 'Parça Ücretleri Güncellendi', 'text' => 'Parça Ücretleri Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Parça Ücretleri Güncellenemedi', 'text' => 'Parça Ücreti Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getUcretsil($sayacturid,$uretimyerid){
        try {
            DB::beginTransaction();
            $parcalar = Degisenler::where('sayactur_id',$sayacturid)->where('parcadurum','<>',2)->get();
            $uretimyer = UretimYer::find($uretimyerid);
            if($parcalar->count()>0){
                foreach ($parcalar as $parca){
                    if($parca->kullanim == 1){
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Parça Ücreti Silinemez', 'text' => $parca->tanim.' Adındaki Parça Arıza Kayıdında Kullanılmış.', 'type' => 'error'));
                    }
                    $parcaucret=Fiyat::where('degisenler_id',$parca->id)->where('uretimyer_id',$uretimyerid)->first();
                    $parcaucret->delete();
                }
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $uretimyer->yeradi . ' Üretim Yerine Ait Sayaç Parça Fiyatları Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Parça Sayisi:' . $parcalar->count());
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Parça Ücretleri Silindi', 'text' => 'Parça Ücretleri Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Parça Ücretleri Silinemedi', 'text' => 'Parça Ücretleri Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Parça Ücretleri Silinemedi', 'text' => 'Parça Ücretleri Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getLinkedtest(){
        try {
            $linked=Input::get('linked');
            $result = DB::select("SELECT count(ABOBIL.ID) adet FROM [$linked]..[PGEWOS].[PGEWOS_TB_ABOBIL] [ABOBIL]");
            if(count($result)>0){
                return Response::json(array('durum' => 0));
            }else{
                return Response::json(array('durum' => 1));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => 1));
        }
    }

    public function getLinkedInfo($linked){
        try {
            if($linked!="" && $linked!=null){
                $result = DB::select("SELECT count(ABOBIL.ID) adet FROM [$linked]..[PGEWOS].[PGEWOS_TB_ABOBIL] [ABOBIL]");
                if(count($result)>0){
                    return 1;
                }else{
                    return 0;
                }
            }else{
                return 0;
            }
        } catch (Exception $e) {
            Log::error($e);
            return 0;
        }
    }

    public function postKasakodlist() {
        $query = KasaKod::select(array("kasakod.id","kasakod.kasakod","kasakod.kasaadi","sube.adi","netsiscari.cariadi","kasakod.godemetipi",
            "kasakod.nkasakod","kasakod.nkasaadi","sube.nadi","netsiscari.ncariadi","kasakod.nodemetipi"))
            ->leftjoin("sube","sube.subekodu", "=", "kasakod.subekodu")
            ->leftjoin("netsiscari","sube.netsiscari_id","=","netsiscari.id")
            ->where('sube.aktif',1);
        return Datatables::of($query)
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/digerdatabase/kasakodduzenle/".$model->id."'> Düzenle </a>";
            })
            ->make(true);
    }

    public function getKasakod() {
        return View::make('digerdatabase.kasakod')->with(array('title'=>'Netsis Kasa Tanım Bilgileri '));
    }

    public function getKasakodduzenle($id) {
        $kasakod = KasaKod::find($id);
        $sube = Sube::where('subekodu',$kasakod->subekodu)->where('aktif',1)->first();
        if(!$sube){
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Aktif Şube Bilgisi Bulunamadı', 'text' => 'Kasa Bilgisinin Bulunduğu Şube Aktif Değil', 'type' => 'error'));
        }
        $netsiscari=NetsisCari::find($sube->netsiscari_id);
        return View::make('digerdatabase.kasakodduzenle',array('kasakod'=>$kasakod,'sube'=>$sube,'netsiscari'=>$netsiscari))->with(array('title'=>'Netsis Kasa Tanım Düzenleme Ekranı'));
    }

    public function postKasakodduzenle($id) {
        try {
            $rules = ['odemetipi' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $kasakod = KasaKod::find($id);
            $bilgi = clone $kasakod;
            $odemetipi = Input::get('odemetipi');
            $kasakod->odemetipi = $odemetipi;
            $kasakod->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $kasakod->kasaadi. ' Adına Ait Kasa Tanımı Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Kasa Tanım Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to('digerdatabase/kasakod')->with(array('mesaj' => 'true', 'title' => 'Netsis Kasa Tanımı Güncellendi', 'text' => 'Netsis Kasa Tanımı Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Kasa Tanımı Güncellenemedi', 'text' => 'Netsis Kasa Tanımı Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function postIslemlist() {
        $query = Islem::select(array("islem.id","kullanici.adi_soyadi","islem.aciklama","islem.updated_at","islem.gdurumtarihi","kullanici.nadi_soyadi","islem.naciklama"))
            ->leftjoin("kullanici","islem.kullanici_id","=","kullanici.id");
        return Datatables::of($query)
            ->editColumn('updated_at', function ($model) {
                $date = new DateTime($model->updated_at);
                return $date->format('d-m-Y');
            })
            ->addColumn('islemler', function ($model) {
                return "<a class='btn btn-sm btn-warning detay' href='#detay' data-toggle='modal' data-id='".$model->id."'> Detay </a>";
            })
            ->make(true);
    }

    public function getIslem() {
        return View::make('digerdatabase.islemler')->with(array('title'=>'Sistemde Yapılan Tüm İşlemler'));
    }

    public function getIslemdetay() {
        try {
            $id=Input::get('id');
            $islem = Islem::find($id);
            $islem->kullanici = Kullanici::find($islem->kullanici_id);
            $islem->tarih = date('d-m-Y', strtotime($islem->updated_at));
            return Response::json(array('durum' => true, 'islem' => $islem));
        } catch (Exception $e) {
            return Response::json(array('durum' => false, 'title' => 'İşlem Bilgi Hatası','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

    public function getIslemduzenle() {
        return View::make('digerdatabase.islemduzenle')->with(array('title'=>'İşlem Düzenle'));
    }

    public function getIslembilgi()
    {
        $kriter = Input::get('kriter');
        $servistakip = ServisTakip::where('serino', $kriter)->orWhere('eskiserino', $kriter)->orderBy('id', 'desc')->get();
        $count = $servistakip->count();
        if ($count > 1) {
            foreach ($servistakip as $takip) {
                $takip->sayacdurum = SayacDurum::find($takip->durum);
                $takip->sayacadi = SayacAdi::find($takip->sayacadi_id);
                $takip->uretimyer = UretimYer::find($takip->uretimyer_id);
                $takip->netsiscari = NetsisCari::find($takip->netsiscari_id);
                $takip->depogelen = DepoGelen::find($takip->depogelen_id);
            }
            return array("durum" => true, "count" => $count, "servistakip" => $servistakip);
        } else if ($count > 0) {
            $servistakip = $servistakip->first();
            $servistakip->sayacdurum = SayacDurum::find($servistakip->durum);
            $servistakip->servis = Servis::find($servistakip->servis_id);
            $servistakip->sayacadi = SayacAdi::find($servistakip->sayacadi_id);
            $servistakip->uretimyer = UretimYer::find($servistakip->uretimyer_id);
            $servistakip->netsiscari = NetsisCari::find($servistakip->netsiscari_id);
            $servistakip->depogelen = DepoGelen::find($servistakip->depogelen_id);
            if ($servistakip->depogelen) {
                $servistakip->depogelen->servisstokkod = ServisStokKod::where('stokkodu', $servistakip->depogelen->servisstokkodu)->first();
                $sayaclar = "";
                $servistakip->depogelen->sayaclist = SayacGelen::where('depogelen_id', $servistakip->depogelen_id)->get();
                foreach ($servistakip->depogelen->sayaclist as $sayacgelen) {
                    $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                    if ($sayacgelen->sayaccap_id != 1)
                        $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                    $sayaclar .= ($sayaclar == "" ? "" : "<br/>") . $sayacgelen->serino . " - " . $sayacgelen->sayacadi->sayacadi . ($sayacgelen->sayaccap_id != 1 ? " " . $sayacgelen->sayaccap->capadi : "");
                }
                $servistakip->depogelen->sayaclar = $sayaclar;
            }
            $servistakip->sayacgelen = SayacGelen::find($servistakip->sayacgelen_id);
            if ($servistakip->sayacgelen) {
                $servistakip->sayacgelen->sayacadi = SayacAdi::find($servistakip->sayacgelen->sayacadi_id);
                if ($servistakip->sayacgelen->sayaccap_id != 1)
                    $servistakip->sayacgelen->sayaccap = SayacCap::find($servistakip->sayacgelen->sayaccap_id);
            }
            $servistakip->arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
            if ($servistakip->arizakayit) {
                $servistakip->sayac = Sayac::find($servistakip->arizakayit->sayac_id);
                if ($servistakip->sayac) {
                    $servistakip->sayac->uretimyer = UretimYer::find($servistakip->sayac->uretimyer_id);
                    if ($servistakip->sayac->sayacadi_id) {
                        $servistakip->sayac->sayacadi = SayacAdi::find($servistakip->sayac->sayacadi_id);
                        if ($servistakip->sayac->sayaccap_id != 1)
                            $servistakip->sayac->sayaccap = SayacCap::find($servistakip->sayac->sayaccap_id);
                    }
                }
            }
            $servistakip->arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
            if ($servistakip->arizafiyat) {
                $servistakip->arizafiyat->uretimyer = UretimYer::find($servistakip->arizafiyat->uretimyer_id);
                $servistakip->arizafiyat->sayacadi = SayacAdi::find($servistakip->arizafiyat->sayacadi_id);
                $servistakip->arizafiyat->parabirimi = ParaBirimi::find($servistakip->arizafiyat->parabirimi_id);
                if ($servistakip->arizafiyat->sayaccap_id != 1)
                    $servistakip->arizafiyat->sayaccap = SayacCap::find($servistakip->arizafiyat->sayaccap_id);
                $servistakip->arizafiyat->degisenlist = explode(',', $servistakip->arizafiyat->degisenler);
                $servistakip->arizafiyat->ucretsizlist = explode(',', $servistakip->arizafiyat->ucretsiz);
                $servistakip->arizafiyat->parcalist = Degisenler::whereIn('id', $servistakip->arizafiyat->degisenlist)->get();
                $parcalar = "";
                for ($i = 0; $i < count($servistakip->arizafiyat->parcalist); $i++) {
                    $parcalar .= ($parcalar == "" ? "" : "<br/>") . $servistakip->arizafiyat->parcalist[$i]->tanim .
                        ($servistakip->arizafiyat->ucretsizlist[$i] == 1 ? "-ÜCRETSİZ" : "");
                }
                $servistakip->arizafiyat->parcalar = $parcalar;
            }
            $servistakip->ucretlendirilen = Ucretlendirilen::find($servistakip->ucretlendirilen_id);
            if ($servistakip->ucretlendirilen) {
                $servistakip->ucretlendirilen->uretimyer = UretimYer::find($servistakip->ucretlendirilen->uretimyer_id);
                $servistakip->ucretlendirilen->netsiscari = NetsisCari::find($servistakip->ucretlendirilen->netsiscari_id);
                $servistakip->ucretlendirilen->parabirimi = ParaBirimi::find($servistakip->ucretlendirilen->parabirimi_id);
                $servistakip->ucretlendirilen->secilenlist = explode(',', $servistakip->ucretlendirilen->secilenler);
                $servistakip->ucretlendirilen->arizafiyatlar = ArizaFiyat::whereIn('id', $servistakip->ucretlendirilen->secilenlist)->get();
                $sayaclar = "";
                foreach ($servistakip->ucretlendirilen->arizafiyatlar as $arizafiyat) {
                    $arizafiyat->parabirimi = ParaBirimi::find($arizafiyat->parabirimi_id);
                    $arizafiyat->sayacadi = SayacAdi::find($arizafiyat->sayacadi_id);
                    if ($arizafiyat->sayaccap_id != 1)
                        $arizafiyat->sayaccap = SayacCap::find($arizafiyat->sayaccap_id);
                    $sayaclar .= ($sayaclar == "" ? "" : "<br/>") . $arizafiyat->ariza_serino . " - " . $arizafiyat->sayacadi->sayacadi . ($arizafiyat->sayaccap_id != 1 ? " " . $arizafiyat->sayaccap->capadi : "") . " - " . sprintf('%0.2f', $arizafiyat->toplamtutar) . " " . $arizafiyat->parabirimi->birimi;
                }
                $servistakip->ucretlendirilen->sayaclar = $sayaclar;
            }
            $servistakip->onaylanan = Onaylanan::find($servistakip->onaylanan_id);
            if ($servistakip->onaylanan) {
                $servistakip->onaylanan->yetkili = Yetkili::find($servistakip->onaylanan->yetkili_id);
                if ($servistakip->onaylanan->yetkili) {
                    $servistakip->onaylanan->yetkili->kullanici = Kullanici::find($servistakip->onaylanan->yetkili->kullanici_id);
                }
            }
            $servistakip->kalibrasyon = Kalibrasyon::find($servistakip->kalibrasyon_id);
            if ($servistakip->kalibrasyon) {
                $servistakip->kalibrasyon->sayacadi = SayacAdi::find($servistakip->kalibrasyon->sayacadi_id);
                $servistakip->kalibrasyon->istasyon = Istasyon::find($servistakip->kalibrasyon->istasyon_id);
            }
            $servistakip->depoteslim = DepoTeslim::find($servistakip->depoteslim_id);
            if ($servistakip->depoteslim) {
                $servistakip->depoteslim->netsiscari = NetsisCari::find($servistakip->depoteslim->netsiscari_id);
                $servistakip->depoteslim->secilenlist = explode(',', $servistakip->depoteslim->secilenler);
                $servistakip->depoteslim->arizafiyatlar = ArizaFiyat::whereIn('sayacgelen_id', $servistakip->depoteslim->secilenlist)->get();
                $sayaclar = "";
                foreach ($servistakip->depoteslim->arizafiyatlar as $arizafiyat) {
                    $arizafiyat->parabirimi = ParaBirimi::find($arizafiyat->parabirimi_id);
                    $arizafiyat->sayacadi = SayacAdi::find($arizafiyat->sayacadi_id);
                    if ($arizafiyat->sayaccap_id != 1)
                        $arizafiyat->sayaccap = SayacCap::find($arizafiyat->sayaccap_id);
                    $sayaclar .= ($sayaclar == "" ? "" : "<br/>") . $arizafiyat->ariza_serino . " - " . $arizafiyat->sayacadi->sayacadi . ($arizafiyat->sayaccap_id != 1 ? " " . $arizafiyat->sayaccap->capadi : "") . " - " . sprintf('%0.2f', $arizafiyat->toplamtutar) . " " . $arizafiyat->parabirimi->birimi;
                }
                $servistakip->depoteslim->sayaclar = $sayaclar;
            }
            $servistakip->hurda = Hurda::find($servistakip->hurda_id);
            if ($servistakip->hurda) {
                $servistakip->hurda->hurdanedeni = HurdaNedeni::find($servistakip->hurda->hurdanedeni_id);
            }
            $servistakip->depolararasi = Depolararasi::find($servistakip->depolararasi_id);
            if ($servistakip->depolararasi) {
                $servistakip->depolararasi->netsiscari = NetsisCari::find($servistakip->depolararasi->netsiscari_id);
                $servistakip->depolararasi->secilenlist = explode(',', $servistakip->depolararasi->secilenler);
                $servistakip->depolararasi->sayacgelenler = SayacGelen::whereIn('id', $servistakip->depolararasi->secilenlist)->get();
                $sayaclar = "";
                foreach ($servistakip->depolararasi->sayacgelenler as $sayacgelen) {
                    $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                    if ($sayacgelen->sayaccap_id != 1)
                        $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                    $sayaclar .= ($sayaclar == "" ? "" : "<br/>") . $sayacgelen->serino . " - " . $sayacgelen->sayacadi->sayacadi . ($sayacgelen->sayaccap_id != 1 ? " " . $sayacgelen->sayaccap->capadi : "");
                }
                $servistakip->depolararasi->sayaclar = $sayaclar;
            }
            $servistakip->aboneteslim = AboneTeslim::find($servistakip->aboneteslim_id);
            if ($servistakip->aboneteslim) {
                $servistakip->aboneteslim->abone = Abone::find($servistakip->aboneteslim->abone_id);
                $servistakip->aboneteslim->netsiscari = NetsisCari::find($servistakip->aboneteslim->netsiscari_id);
                $servistakip->aboneteslim->parabirimi = ParaBirimi::find($servistakip->aboneteslim->parabirimi_id);
                $servistakip->aboneteslim->secilenlist = explode(',', $servistakip->aboneteslim->secilenler);
                $servistakip->aboneteslim->sayacgelenler = SayacGelen::whereIn('id', $servistakip->aboneteslim->secilenlist)->get();
                $sayaclar = "";
                foreach ($servistakip->aboneteslim->sayacgelenler as $sayacgelen) {
                    $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                    if ($sayacgelen->sayaccap_id != 1)
                        $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                    $sayacgelen->arizafiyat = ArizaFiyat::where('sayacgelen_id', $sayacgelen->id)->first();
                    $sayacgelen->parabirimi = ParaBirimi::find($sayacgelen->arizafiyat->parabirimi_id);
                    $sayaclar .= ($sayaclar == "" ? "" : "<br/>") . $sayacgelen->serino . " - " . $sayacgelen->sayacadi->sayacadi . ($sayacgelen->sayaccap_id != 1 ? " " . $sayacgelen->sayaccap->capadi : "") . " - " . sprintf('%0.2f', $sayacgelen->arizafiyat->toplamtutar) . " " . $sayacgelen->parabirimi->birimi;
                }
                $servistakip->aboneteslim->sayaclar = $sayaclar;
            }
            return array("durum" => true, "count" => $count, "servistakip" => $servistakip);
        } else {
            return array("durum" => false, "type" => "error", "title" => "Sayaç Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Sayaç Bilgisi Bulunamadı.");
        }
    }

    public function getIslemgelenbilgi()
    {
        $servistakipid = Input::get('servistakipid');
        $servistakip = ServisTakip::find($servistakipid);
        if ($servistakip) {
            $servistakip->sayacdurum = SayacDurum::find($servistakip->durum);
            $servistakip->servis = Servis::find($servistakip->servis_id);
            $servistakip->sayacadi = SayacAdi::find($servistakip->sayacadi_id);
            $servistakip->uretimyer = UretimYer::find($servistakip->uretimyer_id);
            $servistakip->netsiscari = NetsisCari::find($servistakip->netsiscari_id);
            $servistakip->depogelen = DepoGelen::find($servistakip->depogelen_id);
            if ($servistakip->depogelen) {
                $servistakip->depogelen->servisstokkod = ServisStokKod::where('stokkodu', $servistakip->depogelen->servisstokkodu)->first();
                $sayaclar = "";
                $servistakip->depogelen->sayaclist = SayacGelen::where('depogelen_id', $servistakip->depogelen_id)->get();
                foreach ($servistakip->depogelen->sayaclist as $sayacgelen) {
                    $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                    if ($sayacgelen->sayaccap_id != 1)
                        $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                    $sayaclar .= ($sayaclar == "" ? "" : "<br/>") . $sayacgelen->serino . " - " . $sayacgelen->sayacadi->sayacadi . ($sayacgelen->sayaccap_id != 1 ? " " . $sayacgelen->sayaccap->capadi : "");
                }
                $servistakip->depogelen->sayaclar = $sayaclar;
            }
            $servistakip->sayacgelen = SayacGelen::find($servistakip->sayacgelen_id);
            if ($servistakip->sayacgelen) {
                $servistakip->sayacgelen->sayacadi = SayacAdi::find($servistakip->sayacgelen->sayacadi_id);
                if ($servistakip->sayacgelen->sayaccap_id != 1)
                    $servistakip->sayacgelen->sayaccap = SayacCap::find($servistakip->sayacgelen->sayaccap_id);
            }
            $servistakip->arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
            if ($servistakip->arizakayit) {
                $servistakip->sayac = Sayac::find($servistakip->arizakayit->sayac_id);
                if ($servistakip->sayac) {
                    $servistakip->sayac->uretimyer = UretimYer::find($servistakip->sayac->uretimyer_id);
                    if ($servistakip->sayac->sayacadi_id) {
                        $servistakip->sayac->sayacadi = SayacAdi::find($servistakip->sayac->sayacadi_id);
                        if ($servistakip->sayac->sayaccap_id != 1)
                            $servistakip->sayac->sayaccap = SayacCap::find($servistakip->sayac->sayaccap_id);
                    }
                }
            }
            $servistakip->arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
            if ($servistakip->arizafiyat) {
                $servistakip->arizafiyat->uretimyer = UretimYer::find($servistakip->arizafiyat->uretimyer_id);
                $servistakip->arizafiyat->sayacadi = SayacAdi::find($servistakip->arizafiyat->sayacadi_id);
                $servistakip->arizafiyat->parabirimi = ParaBirimi::find($servistakip->arizafiyat->parabirimi_id);
                if ($servistakip->arizafiyat->sayaccap_id != 1)
                    $servistakip->arizafiyat->sayaccap = SayacCap::find($servistakip->arizafiyat->sayaccap_id);
                $servistakip->arizafiyat->degisenlist = explode(',', $servistakip->arizafiyat->degisenler);
                $servistakip->arizafiyat->ucretsizlist = explode(',', $servistakip->arizafiyat->ucretsiz);
                $servistakip->arizafiyat->parcalist = Degisenler::whereIn('id', $servistakip->arizafiyat->degisenlist)->get();
                $parcalar = "";
                for ($i = 0; $i < count($servistakip->arizafiyat->parcalist); $i++) {
                    $parcalar .= ($parcalar == "" ? "" : "<br/>") . $servistakip->arizafiyat->parcalist[$i]->tanim .
                        ($servistakip->arizafiyat->ucretsizlist[$i] == 1 ? "-ÜCRETSİZ" : "");
                }
                $servistakip->arizafiyat->parcalar = $parcalar;
            }
            $servistakip->ucretlendirilen = Ucretlendirilen::find($servistakip->ucretlendirilen_id);
            if ($servistakip->ucretlendirilen) {
                $servistakip->ucretlendirilen->uretimyer = UretimYer::find($servistakip->ucretlendirilen->uretimyer_id);
                $servistakip->ucretlendirilen->netsiscari = NetsisCari::find($servistakip->ucretlendirilen->netsiscari_id);
                $servistakip->ucretlendirilen->parabirimi = ParaBirimi::find($servistakip->ucretlendirilen->parabirimi_id);
                $servistakip->ucretlendirilen->secilenlist = explode(',', $servistakip->ucretlendirilen->secilenler);
                $servistakip->ucretlendirilen->arizafiyatlar = ArizaFiyat::whereIn('id', $servistakip->ucretlendirilen->secilenlist)->get();
                $sayaclar = "";
                foreach ($servistakip->ucretlendirilen->arizafiyatlar as $arizafiyat) {
                    $arizafiyat->parabirimi = ParaBirimi::find($arizafiyat->parabirimi_id);
                    $arizafiyat->sayacadi = SayacAdi::find($arizafiyat->sayacadi_id);
                    if ($arizafiyat->sayaccap_id != 1)
                        $arizafiyat->sayaccap = SayacCap::find($arizafiyat->sayaccap_id);
                    $sayaclar .= ($sayaclar == "" ? "" : "<br/>") . $arizafiyat->ariza_serino . " - " . $arizafiyat->sayacadi->sayacadi . ($arizafiyat->sayaccap_id != 1 ? " " . $arizafiyat->sayaccap->capadi : "") . " - " . sprintf('%0.2f', $arizafiyat->toplamtutar) . " " . $arizafiyat->parabirimi->birimi;
                }
                $servistakip->ucretlendirilen->sayaclar = $sayaclar;
            }
            $servistakip->onaylanan = Onaylanan::find($servistakip->onaylanan_id);
            if ($servistakip->onaylanan) {
                $servistakip->onaylanan->yetkili = Yetkili::find($servistakip->onaylanan->yetkili_id);
                if ($servistakip->onaylanan->yetkili) {
                    $servistakip->onaylanan->yetkili->kullanici = Kullanici::find($servistakip->onaylanan->yetkili->kullanici_id);
                }
            }
            $servistakip->kalibrasyon = Kalibrasyon::find($servistakip->kalibrasyon_id);
            if ($servistakip->kalibrasyon) {
                $servistakip->kalibrasyon->sayacadi = SayacAdi::find($servistakip->kalibrasyon->sayacadi_id);
                $servistakip->kalibrasyon->istasyon = Istasyon::find($servistakip->kalibrasyon->istasyon_id);
            }
            $servistakip->depoteslim = DepoTeslim::find($servistakip->depoteslim_id);
            if ($servistakip->depoteslim) {
                $servistakip->depoteslim->netsiscari = NetsisCari::find($servistakip->depoteslim->netsiscari_id);
                $servistakip->depoteslim->secilenlist = explode(',', $servistakip->depoteslim->secilenler);
                $servistakip->depoteslim->arizafiyatlar = ArizaFiyat::whereIn('sayacgelen_id', $servistakip->depoteslim->secilenlist)->get();
                $sayaclar = "";
                foreach ($servistakip->depoteslim->arizafiyatlar as $arizafiyat) {
                    $arizafiyat->parabirimi = ParaBirimi::find($arizafiyat->parabirimi_id);
                    $arizafiyat->sayacadi = SayacAdi::find($arizafiyat->sayacadi_id);
                    if ($arizafiyat->sayaccap_id != 1)
                        $arizafiyat->sayaccap = SayacCap::find($arizafiyat->sayaccap_id);
                    $sayaclar .= ($sayaclar == "" ? "" : "<br/>") . $arizafiyat->ariza_serino . " - " . $arizafiyat->sayacadi->sayacadi . ($arizafiyat->sayaccap_id != 1 ? " " . $arizafiyat->sayaccap->capadi : "") . " - " . sprintf('%0.2f', $arizafiyat->toplamtutar) . " " . $arizafiyat->parabirimi->birimi;
                }
                $servistakip->depoteslim->sayaclar = $sayaclar;
            }
            $servistakip->hurda = Hurda::find($servistakip->hurda_id);
            if ($servistakip->hurda) {
                $servistakip->hurda->hurdanedeni = HurdaNedeni::find($servistakip->hurda->hurdanedeni_id);
            }
            $servistakip->depolararasi = Depolararasi::find($servistakip->depolararasi_id);
            if ($servistakip->depolararasi) {
                $servistakip->depolararasi->netsiscari = NetsisCari::find($servistakip->depolararasi->netsiscari_id);
                $servistakip->depolararasi->secilenlist = explode(',', $servistakip->depolararasi->secilenler);
                $servistakip->depolararasi->sayacgelenler = SayacGelen::whereIn('id', $servistakip->depolararasi->secilenlist)->get();
                $sayaclar = "";
                foreach ($servistakip->depolararasi->sayacgelenler as $sayacgelen) {
                    $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                    if ($sayacgelen->sayaccap_id != 1)
                        $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                    $sayaclar .= ($sayaclar == "" ? "" : "<br/>") . $sayacgelen->serino . " - " . $sayacgelen->sayacadi->sayacadi . ($sayacgelen->sayaccap_id != 1 ? " " . $sayacgelen->sayaccap->capadi : "");
                }
                $servistakip->depolararasi->sayaclar = $sayaclar;
            }
            $servistakip->aboneteslim = AboneTeslim::find($servistakip->aboneteslim_id);
            if ($servistakip->aboneteslim) {
                $servistakip->aboneteslim->abone = Abone::find($servistakip->aboneteslim->abone_id);
                $servistakip->aboneteslim->netsiscari = NetsisCari::find($servistakip->aboneteslim->netsiscari_id);
                $servistakip->aboneteslim->parabirimi = ParaBirimi::find($servistakip->aboneteslim->parabirimi_id);
                $servistakip->aboneteslim->secilenlist = explode(',', $servistakip->aboneteslim->secilenler);
                $servistakip->aboneteslim->sayacgelenler = SayacGelen::whereIn('id', $servistakip->aboneteslim->secilenlist)->get();
                $sayaclar = "";
                foreach ($servistakip->aboneteslim->sayacgelenler as $sayacgelen) {
                    $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                    if ($sayacgelen->sayaccap_id != 1)
                        $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                    $sayacgelen->arizafiyat = ArizaFiyat::where('sayacgelen_id', $sayacgelen->id)->first();
                    $sayacgelen->parabirimi = ParaBirimi::find($sayacgelen->arizafiyat->parabirimi_id);
                    $sayaclar .= ($sayaclar == "" ? "" : "<br/>") . $sayacgelen->serino . " - " . $sayacgelen->sayacadi->sayacadi . ($sayacgelen->sayaccap_id != 1 ? " " . $sayacgelen->sayaccap->capadi : "") . " - " . sprintf('%0.2f', $sayacgelen->arizafiyat->toplamtutar) . " " . $sayacgelen->parabirimi->birimi;
                }
                $servistakip->aboneteslim->sayaclar = $sayaclar;
            }
            return array("durum" => true, "servistakip" => $servistakip);
        } else {
            return array("durum" => false, "type" => "error", "title" => "Sayaç Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Sayaç Bilgisi Bulunamadı.");
        }
    }

    public function getGenelbilgi(){
        try{
            $servistakipid = Input::get('id');
            $servistakip = ServisTakip::find($servistakipid);
            $servistakip->netsiscari = NetsisCari::find($servistakip->netsiscari_id);
            $servistakip->sayacgelen = SayacGelen::find($servistakip->sayacgelen_id);
            $servistakip->sayacadi = SayacAdi::find($servistakip->sayacgelen->sayacadi_id);
            $servistakip->sayaccap = SayacCap::find($servistakip->sayacgelen->sayaccap_id);
            $servistakip->uretimyer = UretimYer::find($servistakip->uretimyer_id);
            $sayaccaplari = array();
            switch ($servistakip->servis_id) {
                case 1:
                case 4:
                    $sayacadlari = SayacAdi::whereIn('sayactur_id', BackendController::getServisSayacTur($servistakip->servis_id))->get();
                    $sayaccaplari = SayacCap::all();
                    $servisstokkodlari = ServisStokKod::whereIn('servisid', BackendController::getServisSayacTur($servistakip->servis_id))->where('koddurum', 1)->get();
                    $netsiscariler = NetsisCari::where('caridurum', 'A')->whereIn('caritipi', array('A', 'D'))
                        ->whereIn('subekodu', array(-1, 8))->whereNotIn('carikod', (function ($query) {
                            $query->select('carikod')->from('kodharichar')->where('subekodu', 8);
                        }))
                        ->orderBy('carikod', 'asc')->get();
                    $uretimyerleri = UretimYer::where('mekanik', 0)->where('id', '<>', 0)->whereNotNull('oracle_id')->get();
                    break;
                case 2:
                    $sayacadlari = SayacAdi::whereIn('sayactur_id', BackendController::getServisSayacTur($servistakip->servis_id))->get();
                    $servisstokkodlari = ServisStokKod::whereIn('servisid', BackendController::getServisSayacTur($servistakip->servis_id))->where('koddurum', 1)->get();
                    $netsiscariler = NetsisCari::where('caridurum', 'A')->whereIn('caritipi', array('A', 'D'))
                        ->whereIn('subekodu', array(-1, 8))->whereNotIn('carikod', (function ($query) {
                            $query->select('carikod')->from('kodharichar')->where('subekodu', 8);
                        }))
                        ->orderBy('carikod', 'asc')->get();
                    $uretimyerleri = UretimYer::where('mekanik', 0)->where('id', '<>', 0)->get();
                    break;
                case 3:
                    $sayacadlari = SayacAdi::whereIn('sayactur_id', BackendController::getServisSayacTur($servistakip->servis_id))->get();
                    $servisstokkodlari = ServisStokKod::whereIn('servisid', BackendController::getServisSayacTur($servistakip->servis_id))->where('koddurum', 1)->get();
                    $netsiscariler = NetsisCari::where('caridurum', 'A')->whereIn('caritipi', array('A', 'D'))
                        ->whereIn('subekodu', array(-1, 8))->whereNotIn('carikod', (function ($query) {
                            $query->select('carikod')->from('kodharichar')->where('subekodu', 8);
                        }))
                        ->orderBy('carikod', 'asc')->get();
                    $uretimyerleri = UretimYer::where('mekanik', 0)->where('id', '<>', 0)->whereNotNull('oracle_id')->get();
                    break;
                case 5:
                    $sayacadlari = SayacAdi::whereIn('sayactur_id', BackendController::getServisSayacTur($servistakip->servis_id))->get();
                    $servisstokkodlari = ServisStokKod::whereIn('servisid', BackendController::getServisSayacTur($servistakip->servis_id))->where('koddurum', 1)->get();
                    $netsiscariler = NetsisCari::where('caridurum', 'A')->whereIn('caritipi', array('A', 'D'))
                        ->whereIn('subekodu', array(-1, 8))->whereNotIn('carikod', (function ($query) {
                            $query->select('carikod')->from('kodharichar')->where('subekodu', 8);
                        }))
                        ->orderBy('carikod', 'asc')->get();
                    $uretimyerleri = UretimYer::where('mekanik', 1)->where('id', '<>', 0)->get();
                    break;
                case 6:
                    $netsiscariid = Auth::user()->netsiscari_id;
                    $uretimyerid = CariYer::whereIn('netsiscari_id', $netsiscariid)->where('durum', 1)->get(array('uretimyer_id'))->toArray();
                    $uretimyerleri = UretimYer::whereIn('id', $uretimyerid)->get();
                    $sube = Sube::whereIn('netsiscari_id', $netsiscariid)->where('aktif', 1)->first();
                    if ($sube) {
                        $sayacturlist = explode(',', $sube->sayactur);
                        $sayacadilist = explode(',', $sube->sayacadlari);
                        $sayacadlari = SayacAdi::whereIn('sayactur_id', $sayacturlist)->whereIn('id', $sayacadilist)->get();
                        $sayaccaplari = SayacCap::all();
                        $sayacadilist = SayacAdi::whereIn('sayactur_id', $sayacturlist)->whereIn('id', $sayacadilist)->get(array('id'))->toArray();
                        $sayacparcalari = SayacParca::whereIn('sayacadi_id', $sayacadilist)->get(array('servisstokkod_id'))->toArray();
                        $servisstokkodlari = ServisStokKod::where('servisid', '<>', 0)->whereIn('servisid', $sayacturlist)->whereIn('id', $sayacparcalari)->where('koddurum', true)->get();
                    } else {
                        $sayacadlari = SayacAdi::all();
                        $sayaccaplari = SayacCap::all();
                        $servisstokkodlari = ServisStokKod::where('servisid', '<>', 0)->where('koddurum', true)->get();
                    }
                    $netsiscariler = NetsisCari::where('caridurum', 'A')->whereIn('caritipi', array('A', 'D'))
                        ->where(function ($query) use ($netsiscariid, $sube) {
                            $query->whereIn('id', $netsiscariid)->orwhereIn('subekodu', array(-1, $sube->subekodu));
                        })
                        ->whereNotIn('carikod', (function ($query) use ($sube) {
                            $query->select('carikod')->from('kodharichar')->where('subekodu', $sube->subekodu);
                        }))
                        ->orderBy('cariadi', 'asc')->get();
                    break;
                default:
                    $sayacadlari = SayacAdi::whereIn('sayactur_id', BackendController::getServisSayacTur($servistakip->servis_id))->get();
                    $sayaccaplari = SayacCap::all();
                    $servisstokkodlari = ServisStokKod::whereIn('servisid', BackendController::getServisSayacTur($servistakip->servis_id))->where('koddurum', 1)->get();
                    $netsiscariler = NetsisCari::where('caridurum', 'A')->whereIn('caritipi', array('A', 'D'))
                        ->whereIn('subekodu', array(-1, 8))->whereNotIn('carikod', (function ($query) {
                            $query->select('carikod')->from('kodharichar')->where('subekodu', 8);
                        }))
                        ->orderBy('carikod', 'asc')->get();
                    $uretimyerleri = UretimYer::where('mekanik', 0)->where('id', '<>', 0)->whereNotNull('oracle_id')->get();
                    break;
            }
            return array("durum" => true, 'servistakip'=>$servistakip,"netsiscariler" => $netsiscariler,"sayacadlari"=>$sayacadlari,"sayaccaplari"=>$sayaccaplari,"servisstokkodlari"=>$servisstokkodlari,"uretimyerleri"=>$uretimyerleri);
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title' => 'Bilgi Alırken Hata Oluştu!', 'text' => str_replace("'","\'",$e->getMessage()), 'type'=>'warning'));
        }
    }

    public function postCaridegistir(){
        try{
            DB::beginTransaction();
            $servistakipid = Input::get('cariduzenleservistakipid');
            $yenicariid = Input::get('cariduzenleyenicariadi');
            $servistakip = ServisTakip::find($servistakipid);
            $yenicari = NetsisCari::find($yenicariid);
            $eskicari = NetsisCari::find($servistakip->netsiscari_id);
            $eskidepogelen = DepoGelen::find($servistakip->depogelen_id);
            if($servistakip->durum==10 || $servistakip->durum==9){
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Depo teslimi yapılmış sayaç için cari bilgisi düzenlenemez. Önce depo teslimi işlemi için geri alma işlemi yapınız!', 'type' => 'error'));
            }
            $servistakip->netsiscari_id=$yenicariid;
            $servistakip->save();
            switch ($servistakip->servis_id){
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                    if($servistakip->depoteslim_id || $servistakip->depolararasi_id || $servistakip->aboneteslim_id){
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Depo teslimi yapılmış sayaç için cari bilgisi düzenlenemez. Önce depo teslimi işlemi için geri alma işlemi yapınız!', 'type' => 'error'));
                    }
                    $depoteslimdurum=0;
                    if($servistakip->hurda_id){
                        $hurda = Hurda::find($servistakip->hurda_id);
                        if(!$hurda) {
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Hurda Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        $hurda->netsiscari_id=$yenicariid;
                        $hurda->save();
                        if($servistakip->servis_id!=5){
                            $depoteslim = DepoTeslim::where('netsiscari_id',$eskicari->id)->where('depodurum',0)->where('tipi',3)->get();
                            if($depoteslim->count()>0){
                                foreach ($depoteslim as $teslim){
                                    $secilenlist=explode(',',$teslim->secilenler);
                                    if (in_array($servistakip->sayacgelen_id, $secilenlist)) {  //sayaç bu listede ise
                                        if(count($secilenlist)>1){
                                            $eskidepoteslim=DepoTeslim::where('netsiscari_id',$yenicari->id)->where('depodurum',0)->where('tipi',$teslim->tipi)->first();
                                            if($eskidepoteslim){
                                                $eskisecilenlist=explode(',', $eskidepoteslim->secilenler);
                                                if (!in_array($servistakip->sayacgelen_id, $eskisecilenlist)) {  //sayaç bu listede ise
                                                    $eskidepoteslim->secilenler .= ($eskidepoteslim->secilenler == "" ? "" : ",") . $servistakip->sayacgelen_id;
                                                    $eskidepoteslim->sayacsayisi++;
                                                    $eskidepoteslim->save();
                                                }
                                            }else{
                                                $yenidepoteslim = new DepoTeslim;
                                                $yenidepoteslim->servis_id = $teslim->servis_id;
                                                $yenidepoteslim->netsiscari_id = $yenicariid;
                                                $yenidepoteslim->secilenler = $servistakip->sayacgelen_id;
                                                $yenidepoteslim->sayacsayisi = 1;
                                                $yenidepoteslim->depodurum = 0;
                                                $yenidepoteslim->tipi = $teslim->tipi;
                                                $yenidepoteslim->parabirimi_id=$teslim->parabirimi_id;
                                                $yenidepoteslim->parabirimi2_id=$teslim->parabirimi2_id;
                                                $yenidepoteslim->save();
                                                $hurda->depoteslim_id=$yenidepoteslim->id;
                                                $hurda->save();
                                            }
                                            $teslim->secilenler = BackendController::getListeFark($secilenlist,array($servistakip->sayacgelen_id));
                                            $teslim->sayacsayisi--;
                                            $teslim->save();
                                        }else{
                                            $teslim->netsiscari_id=$yenicariid;
                                            $teslim->save();
                                        }
                                        $depoteslimdurum=1;
                                        break;
                                    }
                                }
                            }else{
                                DB::rollBack();
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Depo Teslim Bilgisi Bulunamadı!', 'type' => 'error'));
                            }
                        }
                    }
                    $kalibrasyondurum=0;
                    if($servistakip->kalibrasyon_id){
                        $kalibrasyon=Kalibrasyon::find($servistakip->kalibrasyon_id);
                        if(!$kalibrasyon){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Kalibrasyon Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        $kalibrasyon_grup=KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
                        if(!$kalibrasyon_grup){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Kalibrasyon Grup Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        if($kalibrasyon_grup->adet>1){
                            $kalibrasyon_grup->adet--;
                            if($kalibrasyon->durum!=0)
                                $kalibrasyon_grup->biten--;
                            $kalibrasyon_grup->save();
                            $yenikalibrasyongrup = KalibrasyonGrup::where('netsiscari_id',$yenicariid)->where('kalibrasyondurum',0)
                                ->where('periyodik',0)->orderBy('id','desc')->first();
                            if($yenikalibrasyongrup){
                                $yenikalibrasyongrup->adet++;
                                if($kalibrasyon->durum!=0)
                                    $yenikalibrasyongrup->biten++;
                                $yenikalibrasyongrup->save();
                            }else{
                                $yenikalibrasyongrup = new KalibrasyonGrup;
                                $yenikalibrasyongrup->netsiscari_id=$yenicariid;
                                $yenikalibrasyongrup->adet=1;
                                if($kalibrasyon->durum!=0){
                                    $yenikalibrasyongrup->biten=1;
                                    $yenikalibrasyongrup->kalibrasyondurum=1;
                                }
                                $yenikalibrasyongrup->kayittarihi = date('Y-m-d H:i:s');
                                $yenikalibrasyongrup->save();
                            }
                            $kalibrasyon->kalibrasyongrup_id=$yenikalibrasyongrup->id;
                            $kalibrasyon->save();
                        }else{
                            $kalibrasyon_grup->netsiscari_id=$yenicariid;
                            $kalibrasyon_grup->save();
                        }
                        if($kalibrasyon->durum!=0)
                            $kalibrasyondurum=1;
                    }
                    $onaydurum = 0;
                    $ucretlendirilendurum=0;
                    $ucretlendirilen=null;
                    $yeniucretlendirilen=null;
                    if($servistakip->onaylanan_id){
                        $onaylanan = Onaylanan::find($servistakip->onaylanan_id);
                        if(!$onaylanan){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Onaylama Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        if($onaylanan->onaylamatipi!=0) {
                            $onaydurum = 1;
                            $ucretlendirilen = Ucretlendirilen::find($onaylanan->ucretlendirilen_id);
                            if(!$ucretlendirilen) {
                                DB::rollBack();
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Ücretlendirme Bilgisi Bulunamadı!', 'type' => 'error'));
                            }
                            if($ucretlendirilen->sayacsayisi>1){
                                try {
                                    $yeniucretlendirilen = new Ucretlendirilen;
                                    $yeniucretlendirilen->servis_id = $ucretlendirilen->servis_id;
                                    $yeniucretlendirilen->uretimyer_id = $ucretlendirilen->uretimyer_id;
                                    $yeniucretlendirilen->netsiscari_id = $yenicariid;
                                    $yeniucretlendirilen->secilenler = $servistakip->arizafiyat_id;
                                    $secilenlist = array($servistakip->arizafiyat_id);
                                    $kurtarih = $ucretlendirilen->kurtarihi;
                                    $yenifiyat = 0;
                                    $yenifiyat2 = 0;
                                    $yenigaranti = 1;
                                    $yeniparabirimi = $ucretlendirilen->parabirimi_id;
                                    $yeniparabirimi2 = null;
                                    foreach ($secilenlist as $arizafiyatid) {
                                        $arizafiyat = ArizaFiyat::find($arizafiyatid);
                                        $yenigaranti = $yenigaranti == 0 ? 0 : $arizafiyat->ariza_garanti;
                                        $kur = 1;
                                        if ($yeniparabirimi != $arizafiyat->parabirimi_id) {
                                            if ($yeniparabirimi == 1) { //tl
                                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                                            } else { //euro dolar sterln
                                                if ($arizafiyat->parabirimi_id == 1) {
                                                    $kur = 1 / BackendController::getKurBilgisi($yeniparabirimi, $kurtarih);
                                                } else {
                                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($yeniparabirimi, $kurtarih);
                                                }
                                            }
                                        }
                                        $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                                        if($yeniparabirimi2==null){
                                            if($arizafiyat->parabirimi2_id!=null){
                                                if($arizafiyat->parabirimi2_id==$yeniparabirimi){
                                                    $arizafiyat->fiyat +=$arizafiyat->fiyat2;
                                                    $arizafiyat->fiyat2=0;
                                                    $arizafiyat->parabirimi2_id=null;
                                                }else{
                                                    $yeniparabirimi2=$arizafiyat->parabirimi2_id;
                                                }
                                            }
                                        }else{
                                            if($arizafiyat->parabirimi2_id!=null){
                                                if($arizafiyat->parabirimi2_id==$yeniparabirimi){
                                                    $arizafiyat->fiyat +=$arizafiyat->fiyat2;
                                                    $arizafiyat->fiyat2=0;
                                                    $arizafiyat->parabirimi2_id=null;
                                                }else if($arizafiyat->parabirimi2_id!=$yeniparabirimi2){
                                                    DB::rollBack();
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
                                                }
                                            }
                                        }
                                        $indirim = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat * $arizafiyat->indirimorani) / 100 : 0;
                                        $indirim2 = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat2 * $arizafiyat->indirimorani) / 100 : 0;
                                        $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                                        $arizafiyat->tutar2 = $arizafiyat->fiyat2 - $indirim2;
                                        $arizafiyat->kdv = ($arizafiyat->tutar * 18) / 100;
                                        $arizafiyat->kdv2 = ($arizafiyat->tutar2 * 18) / 100;
                                        $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                                        $arizafiyat->toplamtutar2 = round(($arizafiyat->tutar2 + $arizafiyat->kdv2) * 2) / 2;
                                        $arizafiyat->parabirimi_id = $yeniparabirimi;
                                        $arizafiyat->save();
                                        $yenifiyat += $arizafiyat->toplamtutar;
                                        $yenifiyat2 += $arizafiyat->toplamtutar2;
                                    }
                                    $yeniucretlendirilen->garanti = $yenigaranti;
                                    $yeniucretlendirilen->sayacsayisi = 1;
                                    $yeniucretlendirilen->fiyat = $yenifiyat;
                                    $yeniucretlendirilen->fiyat2 = $yenifiyat2;
                                    $yeniucretlendirilen->parabirimi_id = $yeniparabirimi;
                                    $yeniucretlendirilen->parabirimi2_id = $yeniparabirimi2;
                                    $yeniucretlendirilen->kullanici_id = Auth::user()->id;
                                    $yeniucretlendirilen->kayittarihi = $ucretlendirilen->kayittarihi;
                                    $yeniucretlendirilen->kurtarihi = $ucretlendirilen->kurtarihi;
                                    $yeniucretlendirilen->durum = $ucretlendirilen->durum;
                                    $yeniucretlendirilen->onaytipi = $ucretlendirilen->onaytipi;
                                    $yeniucretlendirilen->gonderimtarihi = $ucretlendirilen->gonderimtarihi;
                                    $yeniucretlendirilen->durumtarihi = $ucretlendirilen->durumtarihi;
                                    $yeniucretlendirilen->yetkili_id=$ucretlendirilen->yetkili_id;
                                    $yeniucretlendirilen->save();
                                    $yenionaylanan = new Onaylanan;
                                    $yenionaylanan->servis_id = $onaylanan->servis_id;
                                    $yenionaylanan->uretimyer_id = $onaylanan->uretimyer_id;
                                    $yenionaylanan->netsiscari_id = $yenicariid;
                                    $yenionaylanan->ucretlendirilen_id = $yeniucretlendirilen->id;
                                    $yenionaylanan->yetkili_id = $onaylanan->yetkili_id;
                                    $yenionaylanan->onaytarihi = $onaylanan->onaytarihi;
                                    $yenionaylanan->onaylamatipi = $onaylanan->onaylamatipi;
                                    $yenionaylanan->save();
                                    $servistakip->ucretlendirilen_id=$yeniucretlendirilen->id;
                                    $servistakip->onaylanan_id=$yenionaylanan->id;
                                    $servistakip->save();
                                    $ucretlendirilendurum=1;
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Seçilen sayaçlara ait yeni ücretlendirme oluşturulamadı', 'type' => 'error'));
                                }
                                try {
                                    $tumu = explode(',',$ucretlendirilen->secilenler);
                                    $kalanlar = BackendController::getListeFark($tumu, $secilenlist);
                                    $kalanlist = explode(',', $kalanlar);
                                    $kalansayi = count($kalanlist);
                                    $kalanfiyat = 0;
                                    $kalanfiyat2 = 0;
                                    $kalangaranti = 1;
                                    $kalanparabirimi = $ucretlendirilen->parabirimi_id;
                                    $kalanparabirimi2 = null;
                                    foreach ($kalanlist as $arizafiyatid) {
                                        $arizafiyat = ArizaFiyat::find($arizafiyatid);
                                        $kalangaranti = $kalangaranti == 0 ? 0 : $arizafiyat->ariza_garanti;
                                        $kur = 1;
                                        if ($kalanparabirimi != $arizafiyat->parabirimi_id) {
                                            if ($kalanparabirimi == 1) { //tl
                                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                                            } else { //euro dolar sterln
                                                if ($arizafiyat->parabirimi_id == 1) {
                                                    $kur = 1 / BackendController::getKurBilgisi($kalanparabirimi, $kurtarih);
                                                } else {
                                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($kalanparabirimi, $kurtarih);
                                                }
                                            }
                                        }
                                        $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                                        if ($kalanparabirimi2 == null) {
                                            if ($arizafiyat->parabirimi2_id != null) {
                                                if ($arizafiyat->parabirimi2_id == $kalanparabirimi) {
                                                    $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                                    $arizafiyat->fiyat2 = 0;
                                                    $arizafiyat->parabirimi2_id = null;
                                                } else {
                                                    $kalanparabirimi2 = $arizafiyat->parabirimi2_id;
                                                }
                                            }
                                        } else {
                                            if ($arizafiyat->parabirimi2_id != null) {
                                                if ($arizafiyat->parabirimi2_id == $kalanparabirimi) {
                                                    $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                                    $arizafiyat->fiyat2 = 0;
                                                    $arizafiyat->parabirimi2_id = null;
                                                } else if ($arizafiyat->parabirimi2_id != $kalanparabirimi2) {
                                                    DB::rollBack();
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
                                                }
                                            }
                                        }
                                        $indirim = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat * $arizafiyat->indirimorani) / 100 : 0;
                                        $indirim2 = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat2 * $arizafiyat->indirimorani) / 100 : 0;
                                        $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                                        $arizafiyat->tutar2 = $arizafiyat->fiyat2 - $indirim2;
                                        $arizafiyat->kdv = ($arizafiyat->tutar * 18) / 100;
                                        $arizafiyat->kdv2 = ($arizafiyat->tutar2 * 18) / 100;
                                        $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                                        $arizafiyat->toplamtutar2 = round(($arizafiyat->tutar2 + $arizafiyat->kdv2) * 2) / 2;
                                        $arizafiyat->parabirimi_id = $kalanparabirimi;
                                        $arizafiyat->save();
                                        $kalanfiyat += $arizafiyat->toplamtutar;
                                        $kalanfiyat2 += $arizafiyat->toplamtutar2;
                                    }
                                    $ucretlendirilen->secilenler = $kalanlar;
                                    $ucretlendirilen->garanti = $kalangaranti;
                                    $ucretlendirilen->sayacsayisi = $kalansayi;
                                    $ucretlendirilen->fiyat = $kalanfiyat;
                                    $ucretlendirilen->fiyat2 = $kalanfiyat2;
                                    $ucretlendirilen->parabirimi_id = $kalanparabirimi;
                                    $ucretlendirilen->parabirimi2_id = $kalanparabirimi2;
                                    $ucretlendirilen->save();
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Kalan Sayaçlara Ait Ücretlendirme Güncellenemedi', 'type' => 'error'));
                                }
                                $eskiyetkili = Yetkili::find($ucretlendirilen->yetkili_id);
                                $yetkili = Yetkili::where('netsiscari_id',$yenicariid)->where('aktif',1)->first();
                                if($yetkili && $yetkili->kullanici_id==$eskiyetkili->kullanici_id){
                                    $yeniucretlendirilen->yetkili_id=$yetkili->id;
                                    $yeniucretlendirilen->save();
                                    $yenionaylanan->yetkili_id=$yetkili->id;
                                    $yenionaylanan->save();
                                }else{
                                    $kullanici = Kullanici::find($eskiyetkili->kullanici_id);
                                    if ($kullanici->grup_id == 19) { // Abone Kullanıcısı ise
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Kullanıcı Grubuna Ait Yetkili Birden Fazla Cariye Yetkili Olamaz!', 'type' => 'error'));
                                    }else{
                                        $yetkili = Yetkili::where('kullanici_id',$eskiyetkili->kullanici_id)->where('netsiscari_id',$yenicariid)->first();
                                        if($yetkili){
                                            $yetkili->aktif=1;
                                            $yetkili->save();
                                            $yeniucretlendirilen->yetkili_id=$yetkili->id;
                                            $yeniucretlendirilen->save();
                                            $yenionaylanan->yetkili_id=$yetkili->id;
                                            $yenionaylanan->save();
                                        }else{
                                            $yetkili = new Yetkili;
                                            $yetkili->email = $kullanici->email;
                                            $yetkili->telefon = $kullanici->telefon;
                                            $yetkili->netsiscari_id = $yenicariid;
                                            $yetkili->kullanici_id = $kullanici->id;
                                            $yetkili->aktif = 1;
                                            $yetkili->save();
                                            $yeniucretlendirilen->yetkili_id=$yetkili->id;
                                            $yeniucretlendirilen->save();
                                            $yenionaylanan->yetkili_id=$yetkili->id;
                                            $yenionaylanan->save();
                                        }
                                    }
                                }
                            }else{
                                $ucretlendirilen->netsiscari_id=$yenicariid;
                                $ucretlendirilen->save();
                                $onaylanan->netsiscari_id = $yenicariid;
                                $onaylanan->save();
                                $yetkili = Yetkili::find($onaylanan->yetkili_id);
                                $kullanici = Kullanici::find($yetkili->kullanici_id);
                                if ($kullanici->grup_id == 19) { // Abone Kullanıcısı ise
                                    try {
                                        $kullanici->girisadi = BackendController::GirisAdiBelirle($yenicariid, $onaylanan->uretimyer_id);
                                        $kullanici->ilkmail = 0;
                                        $kullanici->save();
                                        $yetkili->netsiscari_id = $yenicariid;
                                        $yetkili->aktif = 1;
                                        $yetkili->save();
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Yetkili Bilgisi Kaydedilemedi', 'type' => 'error'));
                                    }
                                }else{
                                    $eskiyetkili = Yetkili::where('kullanici_id',$yetkili->kullanici_id)->where('netsiscari_id',$yenicariid)->first();
                                    if($eskiyetkili){
                                        $eskiyetkili->aktif=1;
                                        $eskiyetkili->save();
                                        $onaylanan->yetkili_id=$eskiyetkili->id;
                                        $onaylanan->save();
                                        $ucretlendirilen->yetkili_id=$eskiyetkili->id;
                                        $ucretlendirilen->save();
                                    }else{
                                        $yetkili->netsiscari_id=$yenicariid;
                                        $yetkili->save();
                                    }
                                }
                            }
                        }
                        if($servistakip->servis_id!=5 && $depoteslimdurum==0){
                            $depoteslim = DepoTeslim::where('netsiscari_id',$eskicari->id)->where('depodurum',0)->get();
                            if($depoteslim->count()>0){
                                foreach ($depoteslim as $teslim){
                                    $secilenlist=explode(',',$teslim->secilenler);
                                    if (in_array($servistakip->sayacgelen_id, $secilenlist)) {  //sayaç bu listede ise
                                        if(count($secilenlist)>1){
                                            $eskidepoteslim=DepoTeslim::where('netsiscari_id',$yenicari->id)->where('depodurum',0)->where('tipi',$teslim->tipi)->first();
                                            if($eskidepoteslim){
                                                $eskisecilenlist=explode(',', $eskidepoteslim->secilenler);
                                                if (!in_array($servistakip->sayacgelen_id, $eskisecilenlist)) {  //sayaç bu listede ise
                                                    $eskidepoteslim->secilenler .= ($eskidepoteslim->secilenler == "" ? "" : ",") . $servistakip->sayacgelen_id;
                                                    $eskidepoteslim->sayacsayisi++;
                                                    $eskidepoteslim->save();
                                                }
                                            }else{
                                                $yenidepoteslim = new DepoTeslim;
                                                $yenidepoteslim->servis_id = $teslim->servis_id;
                                                $yenidepoteslim->netsiscari_id = $yenicariid;
                                                $yenidepoteslim->secilenler = $servistakip->sayacgelen_id;
                                                $yenidepoteslim->sayacsayisi = 1;
                                                $yenidepoteslim->depodurum = 0;
                                                $yenidepoteslim->tipi = $teslim->tipi;
                                                $yenidepoteslim->parabirimi_id=$teslim->parabirimi_id;
                                                $yenidepoteslim->parabirimi2_id=$teslim->parabirimi2_id;
                                                $yenidepoteslim->save();
                                            }
                                            $teslim->secilenler = BackendController::getListeFark($secilenlist,array($servistakip->sayacgelen_id));
                                            $teslim->sayacsayisi--;
                                            $teslim->save();
                                        }else{
                                            $teslim->netsiscari_id=$yenicariid;
                                            $teslim->save();
                                        }
                                        $depoteslimdurum=1;
                                        break;
                                    }
                                }
                            }else{
                                DB::rollBack();
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Depo Teslim Bilgisi Bulunamadı!', 'type' => 'error'));
                            }
                        }
                    }
                    if($servistakip->ucretlendirilen_id && $onaydurum==0){
                        $ucretlendirilen = Ucretlendirilen::find($servistakip->ucretlendirilen_id);
                        if(!$ucretlendirilen) {
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Ücretlendirme Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        if($ucretlendirilen->sayacsayisi>1){
                            try {
                                $yeniucretlendirilen = new Ucretlendirilen;
                                $yeniucretlendirilen->servis_id = $ucretlendirilen->servis_id;
                                $yeniucretlendirilen->uretimyer_id = $ucretlendirilen->uretimyer_id;
                                $yeniucretlendirilen->netsiscari_id = $yenicariid;
                                $yeniucretlendirilen->secilenler = $servistakip->arizafiyat_id;
                                $secilenlist = array($servistakip->arizafiyat_id);
                                $kurtarih = $ucretlendirilen->kurtarihi;
                                $yenifiyat = 0;
                                $yenifiyat2 = 0;
                                $yenigaranti = 1;
                                $yeniparabirimi = $ucretlendirilen->parabirimi_id;
                                $yeniparabirimi2 = null;
                                foreach ($secilenlist as $arizafiyatid) {
                                    $arizafiyat = ArizaFiyat::find($arizafiyatid);
                                    $yenigaranti = $yenigaranti == 0 ? 0 : $arizafiyat->ariza_garanti;
                                    $kur = 1;
                                    if ($yeniparabirimi != $arizafiyat->parabirimi_id) {
                                        if ($yeniparabirimi == 1) { //tl
                                            $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                                        } else { //euro dolar sterln
                                            if ($arizafiyat->parabirimi_id == 1) {
                                                $kur = 1 / BackendController::getKurBilgisi($yeniparabirimi, $kurtarih);
                                            } else {
                                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($yeniparabirimi, $kurtarih);
                                            }
                                        }
                                    }
                                    $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                                    if($yeniparabirimi2==null){
                                        if($arizafiyat->parabirimi2_id!=null){
                                            if($arizafiyat->parabirimi2_id==$yeniparabirimi){
                                                $arizafiyat->fiyat +=$arizafiyat->fiyat2;
                                                $arizafiyat->fiyat2=0;
                                                $arizafiyat->parabirimi2_id=null;
                                            }else{
                                                $yeniparabirimi2=$arizafiyat->parabirimi2_id;
                                            }
                                        }
                                    }else{
                                        if($arizafiyat->parabirimi2_id!=null){
                                            if($arizafiyat->parabirimi2_id==$yeniparabirimi){
                                                $arizafiyat->fiyat +=$arizafiyat->fiyat2;
                                                $arizafiyat->fiyat2=0;
                                                $arizafiyat->parabirimi2_id=null;
                                            }else if($arizafiyat->parabirimi2_id!=$yeniparabirimi2){
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
                                            }
                                        }
                                    }
                                    $indirim = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat * $arizafiyat->indirimorani) / 100 : 0;
                                    $indirim2 = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat2 * $arizafiyat->indirimorani) / 100 : 0;
                                    $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                                    $arizafiyat->tutar2 = $arizafiyat->fiyat2 - $indirim2;
                                    $arizafiyat->kdv = ($arizafiyat->tutar * 18) / 100;
                                    $arizafiyat->kdv2 = ($arizafiyat->tutar2 * 18) / 100;
                                    $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                                    $arizafiyat->toplamtutar2 = round(($arizafiyat->tutar2 + $arizafiyat->kdv2) * 2) / 2;
                                    $arizafiyat->parabirimi_id = $yeniparabirimi;
                                    $arizafiyat->save();
                                    $yenifiyat += $arizafiyat->toplamtutar;
                                    $yenifiyat2 += $arizafiyat->toplamtutar2;
                                }
                                $yeniucretlendirilen->garanti = $yenigaranti;
                                $yeniucretlendirilen->sayacsayisi = 1;
                                $yeniucretlendirilen->fiyat = $yenifiyat;
                                $yeniucretlendirilen->fiyat2 = $yenifiyat2;
                                $yeniucretlendirilen->parabirimi_id = $yeniparabirimi;
                                $yeniucretlendirilen->parabirimi2_id = $yeniparabirimi2;
                                $yeniucretlendirilen->kullanici_id = Auth::user()->id;
                                $yeniucretlendirilen->kayittarihi = $ucretlendirilen->kayittarihi;
                                $yeniucretlendirilen->kurtarihi = $ucretlendirilen->kurtarihi;
                                $yeniucretlendirilen->durum = $ucretlendirilen->durum;
                                $yeniucretlendirilen->onaytipi = $ucretlendirilen->onaytipi;
                                $yeniucretlendirilen->gonderimtarihi = $ucretlendirilen->gonderimtarihi;
                                $yeniucretlendirilen->durumtarihi = $ucretlendirilen->durumtarihi;
                                $yeniucretlendirilen->yetkili_id=$ucretlendirilen->yetkili_id;
                                $yeniucretlendirilen->save();
                                $servistakip->ucretlendirilen_id=$yeniucretlendirilen->id;
                                $servistakip->save();
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Seçilen sayaçlara ait yeni ücretlendirme oluşturulamadı', 'type' => 'error'));
                            }
                            try {
                                $tumu = explode(',',$ucretlendirilen->secilenler);
                                $kalanlar = BackendController::getListeFark($tumu, $secilenlist);
                                $kalanlist = explode(',', $kalanlar);
                                $kalansayi = count($kalanlist);
                                $kalanfiyat = 0;
                                $kalanfiyat2 = 0;
                                $kalangaranti = 1;
                                $kalanparabirimi = $ucretlendirilen->parabirimi_id;
                                $kalanparabirimi2 = null;
                                foreach ($kalanlist as $arizafiyatid) {
                                    $arizafiyat = ArizaFiyat::find($arizafiyatid);
                                    $kalangaranti = $kalangaranti == 0 ? 0 : $arizafiyat->ariza_garanti;
                                    $kur = 1;
                                    if ($kalanparabirimi != $arizafiyat->parabirimi_id) {
                                        if ($kalanparabirimi == 1) { //tl
                                            $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                                        } else { //euro dolar sterln
                                            if ($arizafiyat->parabirimi_id == 1) {
                                                $kur = 1 / BackendController::getKurBilgisi($kalanparabirimi, $kurtarih);
                                            } else {
                                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($kalanparabirimi, $kurtarih);
                                            }
                                        }
                                    }
                                    $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                                    if ($kalanparabirimi2 == null) {
                                        if ($arizafiyat->parabirimi2_id != null) {
                                            if ($arizafiyat->parabirimi2_id == $kalanparabirimi) {
                                                $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                                $arizafiyat->fiyat2 = 0;
                                                $arizafiyat->parabirimi2_id = null;
                                            } else {
                                                $kalanparabirimi2 = $arizafiyat->parabirimi2_id;
                                            }
                                        }
                                    } else {
                                        if ($arizafiyat->parabirimi2_id != null) {
                                            if ($arizafiyat->parabirimi2_id == $kalanparabirimi) {
                                                $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                                $arizafiyat->fiyat2 = 0;
                                                $arizafiyat->parabirimi2_id = null;
                                            } else if ($arizafiyat->parabirimi2_id != $kalanparabirimi2) {
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
                                            }
                                        }
                                    }
                                    $indirim = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat * $arizafiyat->indirimorani) / 100 : 0;
                                    $indirim2 = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat2 * $arizafiyat->indirimorani) / 100 : 0;
                                    $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                                    $arizafiyat->tutar2 = $arizafiyat->fiyat2 - $indirim2;
                                    $arizafiyat->kdv = ($arizafiyat->tutar * 18) / 100;
                                    $arizafiyat->kdv2 = ($arizafiyat->tutar2 * 18) / 100;
                                    $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                                    $arizafiyat->toplamtutar2 = round(($arizafiyat->tutar2 + $arizafiyat->kdv2) * 2) / 2;
                                    $arizafiyat->parabirimi_id = $kalanparabirimi;
                                    $arizafiyat->save();
                                    $kalanfiyat += $arizafiyat->toplamtutar;
                                    $kalanfiyat2 += $arizafiyat->toplamtutar2;
                                }
                                $ucretlendirilen->secilenler = $kalanlar;
                                $ucretlendirilen->garanti = $kalangaranti;
                                $ucretlendirilen->sayacsayisi = $kalansayi;
                                $ucretlendirilen->fiyat = $kalanfiyat;
                                $ucretlendirilen->fiyat2 = $kalanfiyat2;
                                $ucretlendirilen->parabirimi_id = $kalanparabirimi;
                                $ucretlendirilen->parabirimi2_id = $kalanparabirimi2;
                                $ucretlendirilen->save();
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Kalan Sayaçlara Ait Ücretlendirme Güncellenemedi', 'type' => 'error'));
                            }
                            $eskiyetkili = Yetkili::find($ucretlendirilen->yetkili_id);
                            if($eskiyetkili){
                                $yetkili = Yetkili::where('netsiscari_id',$yenicariid)->where('aktif',1)->first();
                                if($yetkili && $yetkili->kullanici_id==$eskiyetkili->kullanici_id){
                                    $yeniucretlendirilen->yetkili_id=$yetkili->id;
                                    $yeniucretlendirilen->save();
                                }else{
                                    $kullanici = Kullanici::find($eskiyetkili->kullanici_id);
                                    if ($kullanici->grup_id == 19) { // Abone Kullanıcısı ise
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Kullanıcı Grubuna Ait Yetkili Birden Fazla Cariye Yetkili Olamaz!', 'type' => 'error'));
                                    }else{
                                        $yetkili = Yetkili::where('kullanici_id',$eskiyetkili->kullanici_id)->where('netsiscari_id',$yenicariid)->first();
                                        if($yetkili){
                                            $yetkili->aktif=1;
                                            $yetkili->save();
                                            $yeniucretlendirilen->yetkili_id=$yetkili->id;
                                            $yeniucretlendirilen->save();
                                        }else{
                                            $yetkili = new Yetkili;
                                            $yetkili->email = $kullanici->email;
                                            $yetkili->telefon = $kullanici->telefon;
                                            $yetkili->netsiscari_id = $yenicariid;
                                            $yetkili->kullanici_id = $kullanici->id;
                                            $yetkili->aktif = 1;
                                            $yetkili->save();
                                            $yeniucretlendirilen->yetkili_id=$yetkili->id;
                                            $yeniucretlendirilen->save();
                                        }
                                    }
                                }
                            }
                        }else{
                            $ucretlendirilen->netsiscari_id=$yenicariid;
                            $ucretlendirilen->save();
                            $yetkili = Yetkili::find($ucretlendirilen->yetkili_id);
                            if($yetkili){
                                $kullanici = Kullanici::find($yetkili->kullanici_id);
                                if ($kullanici->grup_id == 19) { // Abone Kullanıcısı ise
                                    try {
                                        $kullanici->girisadi = BackendController::GirisAdiBelirle($yenicariid, $ucretlendirilen->uretimyer_id);
                                        $kullanici->ilkmail = 0;
                                        $kullanici->save();
                                        $yetkili->netsiscari_id = $yenicariid;
                                        $yetkili->aktif = 1;
                                        $yetkili->save();
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Yetkili Bilgisi Kaydedilemedi', 'type' => 'error'));
                                    }
                                }else{
                                    $eskiyetkili = Yetkili::where('kullanici_id',$yetkili->kullanici_id)->where('netsiscari_id',$yenicariid)->first();
                                    if($eskiyetkili){
                                        $eskiyetkili->aktif=1;
                                        $eskiyetkili->save();
                                        $ucretlendirilen->yetkili_id=$eskiyetkili->id;
                                        $ucretlendirilen->save();
                                    }else{
                                        $yetkili->netsiscari_id=$yenicariid;
                                        $yetkili->save();
                                    }
                                }
                            }
                        }
                    }
                    $arizafiyat=NULL;
                    if($servistakip->arizafiyat_id){
                        $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                        if(!$arizafiyat){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Arıza Fiyat Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        $arizafiyat->netsiscari_id=$yenicariid;
                        $arizafiyat->save();
                        $sonuc = BackendController::OzelFiyatGuncelle($servistakip->arizafiyat_id);
                        if(!$sonuc['durum']){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => $sonuc['error'], 'type' => 'error'));
                        }
                    }
                    $arizakayit=NULL;
                    if($servistakip->arizakayit_id) {
                        $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
                        if (!$arizakayit) {
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Arıza Kayıt Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        $arizakayit->netsiscari_id=$yenicariid;
                        $arizakayit->save();
                        if($depoteslimdurum==0){
                            $depoteslim = DepoTeslim::where('netsiscari_id',$eskicari->id)->where('depodurum',0)->get();
                            if($depoteslim->count()>0){
                                foreach ($depoteslim as $teslim){
                                    $secilenlist=explode(',',$teslim->secilenler);
                                    if (in_array($servistakip->sayacgelen_id, $secilenlist)) {  //sayaç bu listede ise
                                        if(count($secilenlist)>1){
                                            $eskidepoteslim=DepoTeslim::where('netsiscari_id',$yenicari->id)->where('depodurum',0)->whereIn('tipi',$teslim->tipi)->first();
                                            if($eskidepoteslim){
                                                $eskisecilenlist=explode(',', $eskidepoteslim->secilenler);
                                                if (!in_array($servistakip->sayacgelen_id, $eskisecilenlist)) {  //sayaç bu listede ise
                                                    $eskidepoteslim->secilenler .= ($eskidepoteslim->secilenler == "" ? "" : ",") . $servistakip->sayacgelen_id;
                                                    $eskidepoteslim->sayacsayisi++;
                                                    $eskidepoteslim->save();
                                                }
                                            }else{
                                                $yenidepoteslim = new DepoTeslim;
                                                $yenidepoteslim->servis_id = $teslim->servis_id;
                                                $yenidepoteslim->netsiscari_id = $yenicariid;
                                                $yenidepoteslim->secilenler = $servistakip->sayacgelen_id;
                                                $yenidepoteslim->sayacsayisi = 1;
                                                $yenidepoteslim->depodurum = 0;
                                                $yenidepoteslim->tipi = $teslim->tipi;
                                                $yenidepoteslim->parabirimi_id=$teslim->parabirimi_id;
                                                $yenidepoteslim->parabirimi2_id=$teslim->parabirimi2_id;
                                                $yenidepoteslim->save();
                                            }
                                            $teslim->secilenler = BackendController::getListeFark($secilenlist,array($servistakip->sayacgelen_id));
                                            $teslim->sayacsayisi--;
                                            $teslim->save();
                                        }else{
                                            $teslim->netsiscari_id=$yenicariid;
                                            $teslim->save();
                                        }
                                        $depoteslimdurum=1;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    $sayacgelen=NULL;
                    if($servistakip->sayacgelen_id) {
                        $sayacgelen = SayacGelen::find($servistakip->sayacgelen_id);
                        if (!$sayacgelen) {
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Sayaç Kayıt Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        $sayacgelen->netsiscari_id=$yenicariid;
                        $sayacgelen->save();
                    }
                    $depogelendurum=0;
                    $depogeleninckeyno = NULL;
                    if($servistakip->depogelen_id) {
                        $depogelen = DepoGelen::find($servistakip->depogelen_id);
                        if (!$depogelen) {
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Depo Kayıt Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        if ($depogelen->db_name != 'MANAS' . date('Y')) {
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Eski Yıllara Ait Depo Kayıdı Değiştirilemez!', 'type' => 'error'));
                        }
                        $depogeleninckeyno = $depogelen->inckeyno;
                        $eskifaturakalem = Sthar::find($depogelen->inckeyno);
                        $eskifatura = Fatuirs::where('SUBE_KODU', 8)->where('FTIRSIP', '9')->where('FATIRS_NO', BackendController::ReverseTrk($depogelen->fisno))
                            ->where('CARI_KODU', BackendController::ReverseTrk($depogelen->carikod))->first();
                        $eskifaturaek = Fatuek::where('SUBE_KODU', 8)->where('FKOD', '9')->where('FATIRSNO', BackendController::ReverseTrk($depogelen->fisno))
                            ->where('CKOD', BackendController::ReverseTrk($depogelen->carikod))->first();
                        if ($eskifatura->FATKALEM_ADEDI > 1) {
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
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Yeni Depo Kayıdı için Fatura Numarası Alınamadı!', 'type' => 'error'));
                            }
                            try {
                                $fatura = new Fatuirs;
                                $fatura->SUBE_KODU = 8;
                                $fatura->FATIRS_NO = BackendController::ReverseTrk($belgeno);
                                $fatura->FTIRSIP = '9';    // 9 AMBAR GİRİŞİ
                                $fatura->CARI_KODU = BackendController::ReverseTrk($yenicari->carikod);
                                $fatura->TARIH = $eskifatura->TARIH;
                                $fatura->TIPI = 0;
                                $fatura->BRUTTUTAR = 0;
                                $fatura->KDV = 0;
                                $fatura->GENELTOPLAM = 0; //genel toplam
                                $fatura->ACIKLAMA = NULL;
                                $fatura->KOD1 = $eskifatura->KOD1;
                                $fatura->ODEMEGUNU = $eskifatura->ODEMEGUNU;
                                $fatura->ODEMETARIHI = $eskifatura->ODEMETARIHI;
                                $fatura->KDV_DAHILMI = 'H';
                                $fatura->FATKALEM_ADEDI = 1; //kalem adedi
                                $fatura->SIPARIS_TEST = $fatura->TARIH;
                                $fatura->YEDEK22 = 'A';
                                $fatura->YEDEK = 'X';
                                $fatura->PLA_KODU = $eskifatura->PLA_KODU;
                                $fatura->DOVIZTIP = 0;
                                $fatura->DOVIZTUT = 0;
                                $fatura->F_YEDEK4 = 1;
                                $fatura->C_YEDEK6 = 'F';
                                $fatura->D_YEDEK10 = $fatura->TARIH;
                                $fatura->PROJE_KODU = '1';
                                $fatura->KAYITYAPANKUL = $eskifatura->KAYITYAPANKUL;
                                $fatura->KAYITTARIHI = $eskifatura->KAYITTARIHI;
                                $fatura->ISLETME_KODU = 1;
                                $fatura->save();
                                try {
                                    $faturaek = new Fatuek;
                                    $faturaek->SUBE_KODU = 8;
                                    $faturaek->FKOD = '9';
                                    $faturaek->FATIRSNO = BackendController::ReverseTrk($belgeno);
                                    $faturaek->CKOD = BackendController::ReverseTrk($yenicari->carikod);
                                    $faturaek->save();
                                    $i = 1;
                                    if ($depogelen->adet > 1) {
                                        $inckey = array();
                                        try {
                                            $faturakalem = new Sthar;
                                            $faturakalem->STOK_KODU = $eskifaturakalem->STOK_KODU;
                                            $faturakalem->FISNO = BackendController::ReverseTrk($belgeno);
                                            $faturakalem->STHAR_GCMIK = 1;
                                            $faturakalem->STHAR_GCKOD = 'G';
                                            $faturakalem->STHAR_TARIH = $fatura->TARIH;
                                            $faturakalem->STHAR_NF = 0;
                                            $faturakalem->STHAR_BF = 0;
                                            $faturakalem->STHAR_KDV = 18;
                                            $faturakalem->STHAR_DOVTIP = 0;
                                            $faturakalem->STHAR_DOVFIAT = 0;
                                            $faturakalem->DEPO_KODU = $eskifaturakalem->DEPO_KODU;
                                            $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($yenicari->carikod);
                                            $faturakalem->STHAR_FTIRSIP = '9';
                                            $faturakalem->LISTE_FIAT = 0;
                                            $faturakalem->STHAR_HTUR = 'A';
                                            $faturakalem->STHAR_ODEGUN = $eskifaturakalem->STHAR_ODEGUN;
                                            $faturakalem->STHAR_BGTIP = 'I';
                                            $faturakalem->STHAR_KOD1 = NULL;
                                            $faturakalem->STHAR_KOD2 = 'F';
                                            $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($yenicari->carikod);
                                            $faturakalem->STHAR_SIP_TURU = 'F';
                                            $faturakalem->PLASIYER_KODU = $eskifaturakalem->PLASIYER_KODU;
                                            $faturakalem->SIRA = $i;
                                            $faturakalem->STRA_SIPKONT = 0;
                                            $faturakalem->IRSALIYE_NO = NULL;
                                            $faturakalem->IRSALIYE_TARIH = NULL;
                                            $faturakalem->STHAR_TESTAR = NULL;
                                            $faturakalem->OLCUBR = 0;
                                            $faturakalem->VADE_TARIHI = $fatura->ODEMETARIHI;
                                            $faturakalem->SUBE_KODU = 8;
                                            $faturakalem->C_YEDEK6 = 'X';
                                            $faturakalem->D_YEDEK10 = $fatura->TARIH;;
                                            $faturakalem->PROJE_KODU = '1';
                                            $faturakalem->DUZELTMETARIHI = $eskifaturakalem->DUZELTMETARIHI;
                                            $faturakalem->STRA_IRSKONT = 0;
                                            $faturakalem->save();
                                            $id = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU=8 AND FISNO='" . BackendController::ReverseTrk($belgeno) . "' and STHAR_FTIRSIP='9' and STHAR_CARIKOD='" . BackendController::ReverseTrk($yenicari->carikod) . "' ORDER BY INCKEYNO DESC");
                                            $inckeyno = ($id[0]->INCKEYNO);
                                            if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                                                $fatura->delete();
                                                $faturaek->delete();
                                                foreach ($inckey as $inckey_) {
                                                    Sthar::find($inckey_)->delete();
                                                }
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi!', 'type' => 'error'));
                                            }
                                            $depogeleninckeyno = $inckeyno;
                                            $depogelendurum = 1;
                                        } catch (Exception $e) {
                                            DB::rollBack();
                                            Log::error($e);
                                            $fatura->delete();
                                            $faturaek->delete();
                                            foreach ($inckey as $inckey_) {
                                                Sthar::find($inckey_)->delete();
                                            }
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Yeni Depo Kayıdı için Fatura Kalemi Kaydedilemedi!', 'type' => 'error'));
                                        }
                                        $eskifaturakalem->STHAR_GCMIK -= 1;
                                        $eskifaturakalem->save();
                                    } else {
                                        try {
                                            $eskifaturakalem->FISNO = BackendController::ReverseTrk($belgeno);
                                            $eskifaturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($yenicari->carikod);
                                            $eskifaturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($yenicari->carikod);
                                            $eskifaturakalem->save();
                                        } catch (Exception $e) {
                                            DB::rollBack();
                                            Log::error($e);
                                            $fatura->delete();
                                            $faturaek->delete();
                                            $eskifaturakalem->FISNO = $eskifatura->FATIRS_NO;
                                            $eskifaturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($eskicari->carikod);
                                            $eskifaturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($eskicari->carikod);
                                            $eskifaturakalem->save();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Yeni Depo Kayıdı için Fatura Kalemi Kaydedilemedi!', 'type' => 'error'));
                                        }
                                    }
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    $fatura->delete();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Yeni Depo Kayıdı için Fatura Açıklaması Kaydedilemedi!', 'type' => 'error'));
                                }
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Yeni Depo Kayıdı için Fatura Kaydedilemedi!', 'type' => 'error'));
                            }
                        } else {
                            if ($eskifaturakalem->STHAR_GCMIK > 1) {
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
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Yeni Depo Kayıdı için Fatura Numarası Alınamadı!', 'type' => 'error'));
                                }
                                try {
                                    $fatura = new Fatuirs;
                                    $fatura->SUBE_KODU = 8;
                                    $fatura->FATIRS_NO = BackendController::ReverseTrk($belgeno);
                                    $fatura->FTIRSIP = '9';    // 9 AMBAR GİRİŞİ
                                    $fatura->CARI_KODU = BackendController::ReverseTrk($yenicari->carikod);
                                    $fatura->TARIH = $eskifatura->TARIH;
                                    $fatura->TIPI = 0;
                                    $fatura->BRUTTUTAR = 0;
                                    $fatura->KDV = 0;
                                    $fatura->GENELTOPLAM = 0; //genel toplam
                                    $fatura->ACIKLAMA = NULL;
                                    $fatura->KOD1 = $eskifatura->KOD1;
                                    $fatura->ODEMEGUNU = $eskifatura->ODEMEGUNU;
                                    $fatura->ODEMETARIHI = $eskifatura->ODEMETARIHI;
                                    $fatura->KDV_DAHILMI = 'H';
                                    $fatura->FATKALEM_ADEDI = 1; //kalem adedi
                                    $fatura->SIPARIS_TEST = $fatura->TARIH;
                                    $fatura->YEDEK22 = 'A';
                                    $fatura->YEDEK = 'X';
                                    $fatura->PLA_KODU = $eskifatura->PLA_KODU;
                                    $fatura->DOVIZTIP = 0;
                                    $fatura->DOVIZTUT = 0;
                                    $fatura->F_YEDEK4 = 1;
                                    $fatura->C_YEDEK6 = 'F';
                                    $fatura->D_YEDEK10 = $fatura->TARIH;
                                    $fatura->PROJE_KODU = '1';
                                    $fatura->KAYITYAPANKUL = $eskifatura->KAYITYAPANKUL;
                                    $fatura->KAYITTARIHI = $eskifatura->KAYITTARIHI;
                                    $fatura->ISLETME_KODU = 1;
                                    $fatura->save();
                                    try {
                                        $faturaek = new Fatuek;
                                        $faturaek->SUBE_KODU = 8;
                                        $faturaek->FKOD = '9';
                                        $faturaek->FATIRSNO = BackendController::ReverseTrk($belgeno);
                                        $faturaek->CKOD = BackendController::ReverseTrk($yenicari->carikod);
                                        $faturaek->save();
                                        $i = 1;
                                        $inckey = array();
                                        try {
                                            $faturakalem = new Sthar;
                                            $faturakalem->STOK_KODU = $eskifaturakalem->STOK_KODU;
                                            $faturakalem->FISNO = BackendController::ReverseTrk($belgeno);
                                            $faturakalem->STHAR_GCMIK = 1;
                                            $faturakalem->STHAR_GCKOD = 'G';
                                            $faturakalem->STHAR_TARIH = $fatura->TARIH;
                                            $faturakalem->STHAR_NF = 0;
                                            $faturakalem->STHAR_BF = 0;
                                            $faturakalem->STHAR_KDV = 18;
                                            $faturakalem->STHAR_DOVTIP = 0;
                                            $faturakalem->STHAR_DOVFIAT = 0;
                                            $faturakalem->DEPO_KODU = $eskifaturakalem->DEPO_KODU;
                                            $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($yenicari->carikod);
                                            $faturakalem->STHAR_FTIRSIP = '9';
                                            $faturakalem->LISTE_FIAT = 0;
                                            $faturakalem->STHAR_HTUR = 'A';
                                            $faturakalem->STHAR_ODEGUN = $eskifaturakalem->STHAR_ODEGUN;
                                            $faturakalem->STHAR_BGTIP = 'I';
                                            $faturakalem->STHAR_KOD1 = NULL;
                                            $faturakalem->STHAR_KOD2 = 'F';
                                            $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($yenicari->carikod);
                                            $faturakalem->STHAR_SIP_TURU = 'F';
                                            $faturakalem->PLASIYER_KODU = $eskifaturakalem->PLASIYER_KODU;
                                            $faturakalem->SIRA = $i;
                                            $faturakalem->STRA_SIPKONT = 0;
                                            $faturakalem->IRSALIYE_NO = NULL;
                                            $faturakalem->IRSALIYE_TARIH = NULL;
                                            $faturakalem->STHAR_TESTAR = NULL;
                                            $faturakalem->OLCUBR = 0;
                                            $faturakalem->VADE_TARIHI = $fatura->ODEMETARIHI;
                                            $faturakalem->SUBE_KODU = 8;
                                            $faturakalem->C_YEDEK6 = 'X';
                                            $faturakalem->D_YEDEK10 = $fatura->TARIH;;
                                            $faturakalem->PROJE_KODU = '1';
                                            $faturakalem->DUZELTMETARIHI = $eskifaturakalem->DUZELTMETARIHI;
                                            $faturakalem->STRA_IRSKONT = 0;
                                            $faturakalem->save();
                                            $id = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU=8 AND FISNO='" . BackendController::ReverseTrk($belgeno) . "' and STHAR_FTIRSIP='9' and STHAR_CARIKOD='" . BackendController::ReverseTrk($yenicari->carikod) . "' ORDER BY INCKEYNO DESC");
                                            $inckeyno = ($id[0]->INCKEYNO);
                                            if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                                                $fatura->delete();
                                                $faturaek->delete();
                                                foreach ($inckey as $inckey_) {
                                                    Sthar::find($inckey_)->delete();
                                                }
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi!', 'type' => 'error'));
                                            }
                                            $depogeleninckeyno = $inckeyno;
                                            $depogelendurum = 1;
                                        } catch (Exception $e) {
                                            DB::rollBack();
                                            Log::error($e);
                                            $fatura->delete();
                                            $faturaek->delete();
                                            foreach ($inckey as $inckey_) {
                                                Sthar::find($inckey_)->delete();
                                            }
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Yeni Depo Kayıdı için Fatura Kalemi Kaydedilemedi!', 'type' => 'error'));
                                        }
                                        $eskifaturakalem->STHAR_GCMIK -= 1;
                                        $eskifaturakalem->save();
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        $fatura->delete();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Yeni Depo Kayıdı için Fatura Açıklaması Kaydedilemedi!', 'type' => 'error'));
                                    }
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Yeni Depo Kayıdı için Fatura Kaydedilemedi!', 'type' => 'error'));
                                }
                            } else {
                                try {
                                    $eskifatura->CARI_KODU = BackendController::ReverseTrk($yenicari->carikod);
                                    $eskifatura->save();
                                    try {
                                        $eskifaturaek->CKOD = BackendController::ReverseTrk($yenicari->carikod);
                                        $eskifaturaek->save();
                                        try {
                                            $eskifaturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($yenicari->carikod);
                                            $eskifaturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($yenicari->carikod);
                                            $eskifaturakalem->save();
                                        } catch (Exception $e) {
                                            Log::error($e);
                                            $eskifatura->CARI_KODU = BackendController::ReverseTrk($eskicari->carikod);
                                            $eskifatura->save();
                                            $eskifaturaek->CKOD = BackendController::ReverseTrk($eskicari->carikod);
                                            $eskifaturaek->save();
                                            $eskifaturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($eskicari->carikod);
                                            $eskifaturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($eskicari->carikod);
                                            $eskifaturakalem->save();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Yeni Depo Kayıdı için Fatura Kalemi Kaydedilemedi!', 'type' => 'error'));
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        $eskifatura->CARI_KODU = BackendController::ReverseTrk($eskicari->carikod);
                                        $eskifatura->save();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Yeni Depo Kayıdı için Fatura Açıklaması Kaydedilemedi!', 'type' => 'error'));
                                    }
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Yeni Depo Kayıdı için Fatura Kaydedilemedi!', 'type' => 'error'));
                                }
                            }
                        }
                        $cariyer = CariYer::where('netsiscari_id', $yenicari->id)->where('uretimyer_id', $servistakip->uretimyer_id)->where('durum', 1)->first();
                        if (!$cariyer) {
                            $cariyer = CariYer::where('netsiscari_id', $yenicari->id)->where('uretimyer_id', $servistakip->uretimyer_id)->first();
                            if ($cariyer) {
                                $cariyer->durum = 1;
                                $cariyer->save();
                            } else {
                                $cariyer = new CariYer;
                                $cariyer->uretimyer_id = $servistakip->uretimyer_id;
                                $cariyer->netsiscari_id = $yenicari->id;
                                $cariyer->durum = 1;
                                $cariyer->kullanici_id = Auth::user()->id;
                                $cariyer->save();
                            }
                        }
                    }
                    $yenidepogelen=NULL;
                    if($depogelendurum){
                        $yenidepogelen = DepoGelen::where('db_name','MANAS'.date('Y'))->where('inckeyno',$depogeleninckeyno)->first();
                        $now = time();
                        while ($now + 30 > time()) {
                            $yenidepogelen = DepoGelen::where('db_name', 'MANAS'.date('Y'))->where('inckeyno', $depogeleninckeyno)->first();
                            if ($yenidepogelen)
                                break;
                        }
                        if ($yenidepogelen) {
                            $servistakip->depogelen_id = $yenidepogelen->id;
                            $servistakip->save();
                            if($sayacgelen) {
                                $sayacgelen->depogelen_id = $yenidepogelen->id;
                                $sayacgelen->save();
                            }
                            if($arizakayit){
                                $arizakayit->depogelen_id = $yenidepogelen->id;
                                $arizakayit->save();
                            }
                            if($arizafiyat) {
                                $arizafiyat->depogelen_id = $yenidepogelen->id;
                                $arizafiyat->save();
                            }
                        }else{
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Depo Gelen Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        if($servistakip->arizakayit_id) {
                            BackendController::HatirlatmaDuzenle(3, $eskicari->id, $yenicari->id, $servistakip->servis_id, 1, 1, $eskidepogelen->id,$yenidepogelen->id, $eskidepogelen->servisstokkodu,$yenidepogelen->servisstokkodu);
                            BackendController::BildirimDuzenle(3, $eskicari->id, $yenicari->id, $servistakip->servis_id, 1,$eskidepogelen->id, $yenidepogelen->id,$eskidepogelen->servisstokkodu,$yenidepogelen->servisstokkodu);
                        }
                        if($servistakip->sayacgelen_id) {
                            BackendController::HatirlatmaGuncelle(2, $yenicari->id, $servistakip->servis_id, 1, $yenidepogelen->id, $yenidepogelen->servisstokkodu);
                            BackendController::BildirimDuzenle(2, $eskicari->id, $yenicari->id, $servistakip->servis_id, 1, $eskidepogelen->id,$yenidepogelen->id,  $eskidepogelen->servisstokkodu,$yenidepogelen->servisstokkodu);
                        }
                        if($servistakip->durum==1){ //Sayac Kayıdı Yapıldı
                            BackendController::HatirlatmaDuzenle(3, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,0,$eskidepogelen->id,$yenidepogelen->id, $eskidepogelen->servisstokkodu,$yenidepogelen->servisstokkodu);
                        }
                    }
                    if($servistakip->durum==2){ //Arıza Kayıdı Yapıldı
                        BackendController::HatirlatmaDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,0);
                    }else if($servistakip->durum==3){ //Fiyatlandırma Yapıldı
                        BackendController::HatirlatmaDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                        BackendController::BildirimDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                        BackendController::HatirlatmaDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,0);
                    }else if($servistakip->durum==4){ //Onay Formu Gönderildi
                        BackendController::HatirlatmaDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                        BackendController::BildirimDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                        BackendController::HatirlatmaDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                        BackendController::BildirimDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                        BackendController::HatirlatmaDuzenle(6, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,0);
                        if($servistakip->kalibrasyon_id){
                            BackendController::HatirlatmaDuzenle(8, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,$kalibrasyondurum);
                            BackendController::BildirimDuzenle(8, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                        }
                    }else if($servistakip->durum==5){ //Müşteri Onayı Alındı
                        BackendController::HatirlatmaDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                        BackendController::BildirimDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                        BackendController::HatirlatmaDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                        BackendController::BildirimDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                        BackendController::HatirlatmaDuzenle(6, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                        BackendController::BildirimDuzenle(7, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                        if($depoteslimdurum){
                            BackendController::HatirlatmaDuzenle(9, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,0);
                        }
                        if($servistakip->kalibrasyon_id){
                            BackendController::HatirlatmaDuzenle(8, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,$kalibrasyondurum);
                            BackendController::BildirimDuzenle(8, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                        }
                    }else if($servistakip->durum==6){ //Fiyatlandırma Reddedildi
                        BackendController::HatirlatmaDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                        BackendController::BildirimDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                        BackendController::HatirlatmaDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                        BackendController::BildirimDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                        BackendController::HatirlatmaDuzenle(6, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                        BackendController::BildirimDuzenle(6, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                        BackendController::HatirlatmaDuzenle(7, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,0);
                        if($servistakip->kalibrasyon_id){
                            BackendController::HatirlatmaDuzenle(8, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,$kalibrasyondurum);
                            BackendController::BildirimDuzenle(8, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                        }
                    }else if($servistakip->durum==7){ //Tekrar Fiyatlandırıldı
                        BackendController::HatirlatmaDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                        BackendController::BildirimDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                        BackendController::HatirlatmaDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                        BackendController::BildirimDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                        BackendController::HatirlatmaDuzenle(6, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                        BackendController::BildirimDuzenle(6, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                        BackendController::HatirlatmaDuzenle(7, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                        BackendController::HatirlatmaDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,0);
                        if($servistakip->kalibrasyon_id){
                            BackendController::HatirlatmaDuzenle(8, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,$kalibrasyondurum);
                            BackendController::BildirimDuzenle(8, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                        }
                    }else if($servistakip->durum==8){ //Kalibrasyonu Yapıldı
                        BackendController::HatirlatmaDuzenle(8, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                        BackendController::BildirimDuzenle(8, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                        if($servistakip->ucretlendirilen_id){
                            if($servistakip->onaylanan_id){
                                BackendController::HatirlatmaDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                BackendController::BildirimDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                BackendController::HatirlatmaDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                BackendController::BildirimDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                if($depoteslimdurum){
                                    BackendController::HatirlatmaDuzenle(9, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,0);
                                }
                            }else if($ucretlendirilen){
                                if($ucretlendirilen->durum==0){
                                    BackendController::HatirlatmaDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                    BackendController::BildirimDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                    BackendController::HatirlatmaDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,0);
                                }else if($ucretlendirilen->durum==1){
                                    BackendController::HatirlatmaDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                    BackendController::BildirimDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                    BackendController::HatirlatmaDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                    BackendController::BildirimDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                    BackendController::HatirlatmaDuzenle(6, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,0);
                                }else if($ucretlendirilen->durum==2){
                                    BackendController::HatirlatmaDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                    BackendController::BildirimDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                    BackendController::HatirlatmaDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                    BackendController::BildirimDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                    BackendController::HatirlatmaDuzenle(6, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                    BackendController::BildirimDuzenle(6, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                    BackendController::HatirlatmaDuzenle(9, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,0);
                                }else{
                                    BackendController::HatirlatmaDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                    BackendController::BildirimDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                    BackendController::HatirlatmaDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                    BackendController::BildirimDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                    BackendController::HatirlatmaDuzenle(6, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                    BackendController::BildirimDuzenle(6, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                    BackendController::HatirlatmaDuzenle(7, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,0);
                                }
                            }
                        }else if($ucretlendirilendurum){
                            if($yeniucretlendirilen->durum==0){
                                BackendController::HatirlatmaDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                BackendController::BildirimDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                BackendController::HatirlatmaDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,0);
                            }else if($ucretlendirilen->durum==1){
                                BackendController::HatirlatmaDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                BackendController::BildirimDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                BackendController::HatirlatmaDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                BackendController::BildirimDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                BackendController::HatirlatmaDuzenle(6, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,0);
                            }else if($ucretlendirilen->durum==2){
                                BackendController::HatirlatmaDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                BackendController::BildirimDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                BackendController::HatirlatmaDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                BackendController::BildirimDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                BackendController::HatirlatmaDuzenle(6, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                BackendController::BildirimDuzenle(6, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                BackendController::HatirlatmaDuzenle(9, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,0);
                            }else{
                                BackendController::HatirlatmaDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                BackendController::BildirimDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                BackendController::HatirlatmaDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                BackendController::BildirimDuzenle(5, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                BackendController::HatirlatmaDuzenle(6, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,1);
                                BackendController::BildirimDuzenle(6, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                                BackendController::HatirlatmaDuzenle(7, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,0);
                            }
                        }else{
                            BackendController::HatirlatmaDuzenle(4, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,0);
                        }
                    }else if($servistakip->durum==11){ //Hurdaya Ayrıldı
                        BackendController::HatirlatmaDuzenle(9, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1,0);
                        BackendController::BildirimDuzenle(10, $eskicari->id,$yenicari->id, $servistakip->servis_id, 1);
                    }
                    break;
                case 6: //TODO şube için tekrar yapılacak
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Şube için Cari Bilgisi değişikliği Aktif Değil', 'type' => 'error'));
                    break;
            }
            DB::commit();
            return Redirect::to('digerdatabase/islemduzenle')->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alındı', 'text' => '', 'type' => 'success'));
        }catch (Exception $e){
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Cari Bilgisi Düzenleme Hatası', 'text' => 'Cari Bilgisi Düzenlenirken Hata Oluştu.', 'type' => 'error'));
        }
    }

    public function postSayacadidegistir(){
        try{
            DB::beginTransaction();
            $servistakipid = Input::get('sayacadiduzenleservistakipid');
            $yenisayacadiid = Input::get('sayacadiduzenleyenisayacadi');
            $yenisayaccapid = Input::get('sayacadiduzenleyenisayaccap')=="" ? 1 : Input::get('sayacadiduzenleyenisayaccap');
            $servistakip = ServisTakip::find($servistakipid);
            $eskidepogelen = DepoGelen::find($servistakip->depogelen_id);
            if($servistakip->arizakayit_id){
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Düzenleme Hatası', 'text' => 'Arıza kayıdı yapılmış sayaç için sayaç adı düzenlenemez. Önce sayaç üzerindeki işlemleri sayaç kayıt işlemine kadar geri alma işlemi yapınız!', 'type' => 'error'));
            }
            $sayacparca = SayacParca::where('sayacadi_id',$yenisayacadiid)->where('sayaccap_id',$yenisayaccapid)->first();
            if(!$sayacparca)
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Düzenleme Hatası', 'text' => 'Seçilen Sayaç Adı için Eşleşen Stok Kodu mevcut Değil!', 'type' => 'error'));
            $yenistokkod = ServisStokKod::find($sayacparca->servisstokkod_id);
            $eskistokkod = NULL;
            switch ($servistakip->servis_id){
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                    $sayacgelen=NULL;
                    if($servistakip->sayacgelen_id) {
                        $sayacgelen = SayacGelen::find($servistakip->sayacgelen_id);
                        if (!$sayacgelen) {
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Düzenleme Hatası', 'text' => 'Sayaç Kayıt Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        $sayacgelen->sayacadi_id=$yenisayacadiid;
                        $sayacgelen->sayaccap_id=$yenisayaccapid;
                        $sayacgelen->stokkodu = $yenistokkod->stokkodu;
                        $sayacgelen->save();
                    }
                    $depogelendurum=0;
                    $depogeleninckeyno = NULL;
                    if($servistakip->depogelen_id) {
                        $depogelen = DepoGelen::find($servistakip->depogelen_id);
                        if (!$depogelen) {
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Düzenleme Hatası', 'text' => 'Depo Kayıt Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        if ($depogelen->db_name != 'MANAS' . date('Y')) {
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Düzenleme Hatası', 'text' => 'Eski Yıllara Ait Depo Kayıdı Değiştirilemez!', 'type' => 'error'));
                        }
                        $eskistokkod = $depogelen->servisstokkodu;
                        $depogeleninckeyno = $depogelen->inckeyno;
                        $eskifaturakalem = Sthar::find($depogelen->inckeyno);
                        $eskifatura = Fatuirs::where('SUBE_KODU', 8)->where('FTIRSIP', '9')->where('FATIRS_NO', BackendController::ReverseTrk($depogelen->fisno))
                            ->where('CARI_KODU', BackendController::ReverseTrk($depogelen->carikod))->first();
                        if ($eskifatura->FATKALEM_ADEDI > 1) {
                            try {
                                $faturakalem = Sthar::where('STHAR_FTIRSIP','9')->where('SUBE_KODU',8)->where('STHAR_CARIKOD',BackendController::ReverseTrk($depogelen->carikod))
                                ->where('FISNO',BackendController::ReverseTrk($depogelen->fisno))->where('STOK_KODU',$yenistokkod->stokkodu)->first();
                                if($faturakalem){
                                    $faturakalem->STHAR_GCMIK += 1;
                                    $faturakalem->save();
                                    $depogeleninckeyno = $faturakalem->INCKEYNO;
                                    $depogelendurum = 1;
                                    if($eskifaturakalem->STHAR_GCMIK>1){
                                        $eskifaturakalem->STHAR_GCMIK -= 1;
                                        $eskifaturakalem->save();
                                    }else{
                                        $eskifaturakalem->delete();
                                        $eskifatura->FATKALEM_ADEDI -=1;
                                        $eskifatura->save();
                                    }
                                }else{
                                    $inckey = array();
                                    try {
                                        $faturakalem = new Sthar;
                                        $faturakalem->STOK_KODU = $yenistokkod->stokkodu;
                                        $faturakalem->FISNO = $eskifaturakalem->FISNO;
                                        $faturakalem->STHAR_GCMIK = 1;
                                        $faturakalem->STHAR_GCKOD = 'G';
                                        $faturakalem->STHAR_TARIH = $eskifaturakalem->STHAR_TARIH;
                                        $faturakalem->STHAR_NF = 0;
                                        $faturakalem->STHAR_BF = 0;
                                        $faturakalem->STHAR_KDV = 18;
                                        $faturakalem->STHAR_DOVTIP = 0;
                                        $faturakalem->STHAR_DOVFIAT = 0;
                                        $faturakalem->DEPO_KODU = $eskifaturakalem->DEPO_KODU;
                                        $faturakalem->STHAR_ACIKLAMA = $eskifaturakalem->STHAR_ACIKLAMA;
                                        $faturakalem->STHAR_FTIRSIP = '9';
                                        $faturakalem->LISTE_FIAT = 0;
                                        $faturakalem->STHAR_HTUR = 'A';
                                        $faturakalem->STHAR_ODEGUN = $eskifaturakalem->STHAR_ODEGUN;
                                        $faturakalem->STHAR_BGTIP = 'I';
                                        $faturakalem->STHAR_KOD1 = NULL;
                                        $faturakalem->STHAR_KOD2 = 'F';
                                        $faturakalem->STHAR_CARIKOD = $eskifaturakalem->STHAR_CARIKOD;
                                        $faturakalem->STHAR_SIP_TURU = 'F';
                                        $faturakalem->PLASIYER_KODU = $eskifaturakalem->PLASIYER_KODU;
                                        $faturakalem->SIRA = ($eskifatura->FATKALEM_ADEDI)+1;
                                        $faturakalem->STRA_SIPKONT = 0;
                                        $faturakalem->IRSALIYE_NO = NULL;
                                        $faturakalem->IRSALIYE_TARIH = NULL;
                                        $faturakalem->STHAR_TESTAR = NULL;
                                        $faturakalem->OLCUBR = 0;
                                        $faturakalem->VADE_TARIHI = $eskifaturakalem->VADE_TARIHI;
                                        $faturakalem->SUBE_KODU = 8;
                                        $faturakalem->C_YEDEK6 = 'X';
                                        $faturakalem->D_YEDEK10 = $eskifaturakalem->D_YEDEK10;
                                        $faturakalem->PROJE_KODU = '1';
                                        $faturakalem->DUZELTMETARIHI = $eskifaturakalem->DUZELTMETARIHI;
                                        $faturakalem->STRA_IRSKONT = 0;
                                        $faturakalem->save();
                                        $id = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU=8 AND FISNO='" . BackendController::ReverseTrk($depogelen->fisno) . "' and STHAR_FTIRSIP='9' and STHAR_CARIKOD='" . BackendController::ReverseTrk($depogelen->carikod) . "' ORDER BY INCKEYNO DESC");
                                        $inckeyno = ($id[0]->INCKEYNO);
                                        if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                                            foreach ($inckey as $inckey_) {
                                                Sthar::find($inckey_)->delete();
                                            }
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Düzenleme Hatası', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi!', 'type' => 'error'));

                                        }
                                        $eskifatura->FATKALEM_ADEDI +=1;
                                        $eskifatura->save();
                                        $depogeleninckeyno = $inckeyno;
                                        $depogelendurum = 1;
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        foreach ($inckey as $inckey_) {
                                            Sthar::find($inckey_)->delete();
                                        }
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Düzenleme Hatası', 'text' => 'Yeni Depo Kayıdı için Fatura Kalemi Kaydedilemedi!', 'type' => 'error'));
                                    }
                                    if($eskifaturakalem->STHAR_GCMIK>1){
                                        $eskifaturakalem->STHAR_GCMIK -= 1;
                                        $eskifaturakalem->save();
                                    }else{
                                        $eskifaturakalem->delete();
                                        $eskifatura->FATKALEM_ADEDI -=1;
                                        $eskifatura->save();
                                    }
                                }
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Düzenleme Hatası', 'text' => 'Yeni Depo Kayıdı için Fatura Kaydedilemedi!', 'type' => 'error'));
                            }
                        } else {
                            if ($eskifaturakalem->STHAR_GCMIK > 1) {
                                try {
                                    $inckey = array();
                                    try {
                                        $faturakalem = new Sthar;
                                        $faturakalem->STOK_KODU = $yenistokkod->stokkodu;
                                        $faturakalem->FISNO = $eskifaturakalem->FISNO;
                                        $faturakalem->STHAR_GCMIK = 1;
                                        $faturakalem->STHAR_GCKOD = 'G';
                                        $faturakalem->STHAR_TARIH = $eskifaturakalem->STHAR_TARIH;
                                        $faturakalem->STHAR_NF = 0;
                                        $faturakalem->STHAR_BF = 0;
                                        $faturakalem->STHAR_KDV = 18;
                                        $faturakalem->STHAR_DOVTIP = 0;
                                        $faturakalem->STHAR_DOVFIAT = 0;
                                        $faturakalem->DEPO_KODU = $eskifaturakalem->DEPO_KODU;
                                        $faturakalem->STHAR_ACIKLAMA = $eskifaturakalem->STHAR_ACIKLAMA;
                                        $faturakalem->STHAR_FTIRSIP = '9';
                                        $faturakalem->LISTE_FIAT = 0;
                                        $faturakalem->STHAR_HTUR = 'A';
                                        $faturakalem->STHAR_ODEGUN = $eskifaturakalem->STHAR_ODEGUN;
                                        $faturakalem->STHAR_BGTIP = 'I';
                                        $faturakalem->STHAR_KOD1 = NULL;
                                        $faturakalem->STHAR_KOD2 = 'F';
                                        $faturakalem->STHAR_CARIKOD = $eskifaturakalem->STHAR_CARIKOD;
                                        $faturakalem->STHAR_SIP_TURU = 'F';
                                        $faturakalem->PLASIYER_KODU = $eskifaturakalem->PLASIYER_KODU;
                                        $faturakalem->SIRA = ($eskifatura->FATKALEM_ADEDI)+1;
                                        $faturakalem->STRA_SIPKONT = 0;
                                        $faturakalem->IRSALIYE_NO = NULL;
                                        $faturakalem->IRSALIYE_TARIH = NULL;
                                        $faturakalem->STHAR_TESTAR = NULL;
                                        $faturakalem->OLCUBR = 0;
                                        $faturakalem->VADE_TARIHI = $eskifaturakalem->VADE_TARIHI;
                                        $faturakalem->SUBE_KODU = 8;
                                        $faturakalem->C_YEDEK6 = 'X';
                                        $faturakalem->D_YEDEK10 = $eskifaturakalem->D_YEDEK10;
                                        $faturakalem->PROJE_KODU = '1';
                                        $faturakalem->DUZELTMETARIHI = $eskifaturakalem->DUZELTMETARIHI;
                                        $faturakalem->STRA_IRSKONT = 0;
                                        $faturakalem->save();
                                        $id = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU=8 AND FISNO='" . BackendController::ReverseTrk($depogelen->fisno) . "' and STHAR_FTIRSIP='9' and STHAR_CARIKOD='" . BackendController::ReverseTrk($depogelen->carikod) . "' ORDER BY INCKEYNO DESC");
                                        $inckeyno = ($id[0]->INCKEYNO);
                                        if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                                            foreach ($inckey as $inckey_) {
                                                Sthar::find($inckey_)->delete();
                                            }
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Düzenleme Hatası', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi!', 'type' => 'error'));

                                        }
                                        $eskifatura->FATKALEM_ADEDI +=1;
                                        $eskifatura->save();
                                        $depogeleninckeyno = $inckeyno;
                                        $depogelendurum = 1;
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        foreach ($inckey as $inckey_) {
                                            Sthar::find($inckey_)->delete();
                                        }
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Düzenleme Hatası', 'text' => 'Yeni Depo Kayıdı için Fatura Kalemi Kaydedilemedi!', 'type' => 'error'));
                                    }
                                    $eskifaturakalem->STHAR_GCMIK -= 1;
                                    $eskifaturakalem->save();
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Düzenleme Hatası', 'text' => 'Yeni Depo Kayıdı için Fatura Kaydedilemedi!', 'type' => 'error'));
                                }
                            } else {
                                try {
                                    $eskifaturakalem->STOK_KODU = $yenistokkod->stokkodu;
                                    $eskifaturakalem->save();
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    $eskifaturakalem->STOK_KODU = $eskistokkod;
                                    $eskifaturakalem->save();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Düzenleme Hatası', 'text' => 'Yeni Depo Kayıdı için Fatura Kalemi Kaydedilemedi!', 'type' => 'error'));
                                }
                            }
                        }
                    }
                    $yenidepogelen=NULL;
                    if($depogelendurum){
                        $yenidepogelen = DepoGelen::where('db_name','MANAS'.date('Y'))->where('inckeyno',$depogeleninckeyno)->first();
                        $now = time();
                        while ($now + 30 > time()) {
                            $yenidepogelen = DepoGelen::where('db_name', 'MANAS'.date('Y'))->where('inckeyno', $depogeleninckeyno)->first();
                            if ($yenidepogelen)
                                break;
                        }
                        if ($yenidepogelen) {
                            $servistakip->depogelen_id=$yenidepogelen->id;
                            $servistakip->save();
                            if($sayacgelen) {
                                $sayacgelen->depogelen_id=$yenidepogelen->id;
                                $sayacgelen->save();
                            }
                        }else{
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Düzenleme Hatası', 'text' => 'Depo Gelen Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        if($servistakip->sayacgelen_id) {
                            BackendController::HatirlatmaGuncelle(2, $servistakip->netsiscari_id, $servistakip->servis_id, 1, $yenidepogelen->id, $yenidepogelen->servisstokkodu);
                            BackendController::BildirimDuzenle(2, $servistakip->netsiscari_id, $servistakip->netsiscari_id, $servistakip->servis_id, 1, $eskidepogelen->id,$yenidepogelen->id,  $eskidepogelen->servisstokkodu,$yenidepogelen->servisstokkodu);
                        }
                        if($servistakip->durum==1){ //Sayac Kayıdı Yapıldı
                            BackendController::HatirlatmaDuzenle(3, $servistakip->netsiscari_id,$servistakip->netsiscari_id, $servistakip->servis_id, 1,0,$eskidepogelen->id,$yenidepogelen->id, $eskidepogelen->servisstokkodu,$yenidepogelen->servisstokkodu);
                        }
                    }
                    break;
                case 6: //TODO şube için tekrar yapılacak
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Düzenleme Hatası', 'text' => 'Şube için Sayaç Adı değişikliği Aktif Değil', 'type' => 'error'));
                    break;
            }
            DB::commit();
            return Redirect::to('digerdatabase/islemduzenle')->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alındı', 'text' => '', 'type' => 'success'));
        }catch (Exception $e){
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Adı Düzenleme Hatası', 'text' => 'Sayaç Adı Düzenlenirken Hata Oluştu.', 'type' => 'error'));
        }
    }

    public function postSerinodegistir(){
        try{
            DB::beginTransaction();
            $servistakipid = Input::get('serinoduzenleservistakipid');
            $yeniserino = Input::get('serinoduzenleyeniserino');
            $servistakip = ServisTakip::find($servistakipid);
            $eskisayacadi = SayacAdi::find($servistakip->sayacadi_id);
            if($servistakip->durum==10 || $servistakip->durum==9){
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri Numarası Düzenleme Hatası', 'text' => 'Depo teslimi yapılmış sayaç için serino düzenlenemez. Önce depo teslimi işlemi için geri alma işlemi yapınız!', 'type' => 'error'));
            }
            $yenisayac = Sayac::where('serino',$yeniserino)->first();
            if($yenisayac && $eskisayacadi->sayactur_id==$yenisayac->sayactur_id){
                if($yenisayac->uretimyer_id!=$servistakip->uretimyer_id)
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri Numarası Düzenleme Hatası', 'text' => 'Yeni Seri Numarası Başka Üretim Yerine Ait!', 'type' => 'error'));
            }
            $servistakip->serino=$yeniserino;
            $servistakip->save();
            switch ($servistakip->servis_id){
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                    if($servistakip->depoteslim_id || $servistakip->depolararasi_id || $servistakip->aboneteslim_id){
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri Numarası Düzenleme Hatası', 'text' => 'Depo teslimi yapılmış sayaç için seri numarası düzenlenemez. Önce depo teslimi işlemi için geri alma işlemi yapınız!', 'type' => 'error'));
                    }
                    $sayacdurum = 0;
                    if($servistakip->hurda_id){
                        $hurda = Hurda::find($servistakip->hurda_id);
                        if(!$hurda) {
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri Numarası Düzenleme Hatası', 'text' => 'Hurda Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        $sayac = Sayac::find($hurda->sayac_id);
                        if(!$sayac){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri Numarası Düzenleme Hatası', 'text' => 'Sayaç Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        $sayac->serino=$yeniserino;
                        $sayac->save();
                        $sayacdurum=1;
                    }
                    if($servistakip->kalibrasyon_id){
                        $kalibrasyon=Kalibrasyon::find($servistakip->kalibrasyon_id);
                        if(!$kalibrasyon){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri Numarası Düzenleme Hatası', 'text' => 'Kalibrasyon Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        $kalibrasyon->kalibrasyon_seri=$yeniserino;
                        $kalibrasyon->save();
                    }
                    $arizafiyat=NULL;
                    if($servistakip->arizafiyat_id){
                        $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                        if(!$arizafiyat){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri Numarası Düzenleme Hatası', 'text' => 'Arıza Fiyat Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        $arizafiyat->ariza_serino=$yeniserino;
                        $arizafiyat->save();
                        if(!$sayacdurum){
                            $sayac=Sayac::find($arizafiyat->sayac_id);
                            $sayac->serino=$yeniserino;
                            $sayac->save();
                            $sayacdurum=1;
                        }
                    }
                    $arizakayit=NULL;
                    if($servistakip->arizakayit_id) {
                        $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
                        if (!$arizakayit) {
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri Numarası Düzenleme Hatası', 'text' => 'Arıza Kayıt Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        if(!$sayacdurum){
                            $sayac=Sayac::find($arizakayit->sayac_id);
                            $sayac->serino=$yeniserino;
                            $sayac->save();
                        }
                    }
                    $sayacgelen=NULL;
                    if($servistakip->sayacgelen_id) {
                        $sayacgelen = SayacGelen::find($servistakip->sayacgelen_id);
                        if (!$sayacgelen) {
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri Numarası Düzenleme Hatası', 'text' => 'Sayaç Kayıt Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        $sayacgelen->serino=$yeniserino;
                        $sayacgelen->save();
                    }
                    break;
                case 6: //TODO şube için tekrar yapılacak
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri Numarası Düzenleme Hatası', 'text' => 'Şube için Seri Numarası değişikliği Aktif Değil', 'type' => 'error'));
                    break;
            }
            DB::commit();
            return Redirect::to('digerdatabase/islemduzenle')->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alındı', 'text' => '', 'type' => 'success'));
        }catch (Exception $e){
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Seri Numarası Düzenleme Hatası', 'text' => 'Seri Numarası Düzenlenirken Hata Oluştu.', 'type' => 'error'));
        }
    }

    public function postUretimyeridegistir(){
        try{
            DB::beginTransaction();
            $servistakipid = Input::get('uretimyeriduzenleservistakipid');
            $yeniuretimyerid = Input::get('uretimyeriduzenleyeniyeradi');
            $servistakip = ServisTakip::find($servistakipid);
            if($servistakip->durum==10 || $servistakip->durum==9){
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Depo teslimi yapılmış sayaç için geliş yeri bilgisi düzenlenemez. Önce depo teslimi işlemi için geri alma işlemi yapınız!', 'type' => 'error'));
            }
            $servistakip->uretimyer_id=$yeniuretimyerid;
            $servistakip->save();
            switch ($servistakip->servis_id){
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                    if($servistakip->depoteslim_id || $servistakip->depolararasi_id || $servistakip->aboneteslim_id){
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Depo teslimi yapılmış sayaç için geliş yeri bilgisi düzenlenemez. Önce depo teslimi işlemi için geri alma işlemi yapınız!', 'type' => 'error'));
                    }

                    $sayacdurum = 0;
                    if($servistakip->hurda_id){
                        $hurda = Hurda::find($servistakip->hurda_id);
                        if(!$hurda) {
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Hurda Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        $sayac = Sayac::find($hurda->sayac_id);
                        if(!$sayac){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Sayaç Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        $sayac->uretimyer_id=$yeniuretimyerid;
                        $sayac->save();
                        $sayacdurum=1;
                    }

                    $onaydurum = 0;
                    $ucretlendirilen=null;
                    $yeniucretlendirilen=null;
                    if($servistakip->onaylanan_id){
                        $onaylanan = Onaylanan::find($servistakip->onaylanan_id);
                        if(!$onaylanan){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Onaylama Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        if($onaylanan->onaylamatipi!=0) {
                            $onaydurum = 1;
                            $ucretlendirilen = Ucretlendirilen::find($onaylanan->ucretlendirilen_id);
                            if(!$ucretlendirilen) {
                                DB::rollBack();
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Ücretlendirme Bilgisi Bulunamadı!', 'type' => 'error'));
                            }
                            if($ucretlendirilen->sayacsayisi>1){
                                try {
                                    $yeniucretlendirilen = new Ucretlendirilen;
                                    $yeniucretlendirilen->servis_id = $ucretlendirilen->servis_id;
                                    $yeniucretlendirilen->uretimyer_id = $yeniuretimyerid;
                                    $yeniucretlendirilen->netsiscari_id = $ucretlendirilen->netsiscari_id;
                                    $yeniucretlendirilen->secilenler = $servistakip->arizafiyat_id;
                                    $secilenlist = array($servistakip->arizafiyat_id);
                                    $kurtarih = $ucretlendirilen->kurtarihi;
                                    $yenifiyat = 0;
                                    $yenifiyat2 = 0;
                                    $yenigaranti = 1;
                                    $yeniparabirimi = $ucretlendirilen->parabirimi_id;
                                    $yeniparabirimi2 = null;
                                    foreach ($secilenlist as $arizafiyatid) {
                                        $arizafiyat = ArizaFiyat::find($arizafiyatid);
                                        $yenigaranti = $yenigaranti == 0 ? 0 : $arizafiyat->ariza_garanti;
                                        $kur = 1;
                                        if ($yeniparabirimi != $arizafiyat->parabirimi_id) {
                                            if ($yeniparabirimi == 1) { //tl
                                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                                            } else { //euro dolar sterln
                                                if ($arizafiyat->parabirimi_id == 1) {
                                                    $kur = 1 / BackendController::getKurBilgisi($yeniparabirimi, $kurtarih);
                                                } else {
                                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($yeniparabirimi, $kurtarih);
                                                }
                                            }
                                        }
                                        $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                                        if($yeniparabirimi2==null){
                                            if($arizafiyat->parabirimi2_id!=null){
                                                if($arizafiyat->parabirimi2_id==$yeniparabirimi){
                                                    $arizafiyat->fiyat +=$arizafiyat->fiyat2;
                                                    $arizafiyat->fiyat2=0;
                                                    $arizafiyat->parabirimi2_id=null;
                                                }else{
                                                    $yeniparabirimi2=$arizafiyat->parabirimi2_id;
                                                }
                                            }
                                        }else{
                                            if($arizafiyat->parabirimi2_id!=null){
                                                if($arizafiyat->parabirimi2_id==$yeniparabirimi){
                                                    $arizafiyat->fiyat +=$arizafiyat->fiyat2;
                                                    $arizafiyat->fiyat2=0;
                                                    $arizafiyat->parabirimi2_id=null;
                                                }else if($arizafiyat->parabirimi2_id!=$yeniparabirimi2){
                                                    DB::rollBack();
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
                                                }
                                            }
                                        }
                                        $indirim = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat * $arizafiyat->indirimorani) / 100 : 0;
                                        $indirim2 = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat2 * $arizafiyat->indirimorani) / 100 : 0;
                                        $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                                        $arizafiyat->tutar2 = $arizafiyat->fiyat2 - $indirim2;
                                        $arizafiyat->kdv = ($arizafiyat->tutar * 18) / 100;
                                        $arizafiyat->kdv2 = ($arizafiyat->tutar2 * 18) / 100;
                                        $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                                        $arizafiyat->toplamtutar2 = round(($arizafiyat->tutar2 + $arizafiyat->kdv2) * 2) / 2;
                                        $arizafiyat->parabirimi_id = $yeniparabirimi;
                                        $arizafiyat->save();
                                        $yenifiyat += $arizafiyat->toplamtutar;
                                        $yenifiyat2 += $arizafiyat->toplamtutar2;
                                    }
                                    $yeniucretlendirilen->garanti = $yenigaranti;
                                    $yeniucretlendirilen->sayacsayisi = 1;
                                    $yeniucretlendirilen->fiyat = $yenifiyat;
                                    $yeniucretlendirilen->fiyat2 = $yenifiyat2;
                                    $yeniucretlendirilen->parabirimi_id = $yeniparabirimi;
                                    $yeniucretlendirilen->parabirimi2_id = $yeniparabirimi2;
                                    $yeniucretlendirilen->kullanici_id = Auth::user()->id;
                                    $yeniucretlendirilen->kayittarihi = $ucretlendirilen->kayittarihi;
                                    $yeniucretlendirilen->kurtarihi = $ucretlendirilen->kurtarihi;
                                    $yeniucretlendirilen->durum = $ucretlendirilen->durum;
                                    $yeniucretlendirilen->onaytipi = $ucretlendirilen->onaytipi;
                                    $yeniucretlendirilen->gonderimtarihi = $ucretlendirilen->gonderimtarihi;
                                    $yeniucretlendirilen->durumtarihi = $ucretlendirilen->durumtarihi;
                                    $yeniucretlendirilen->yetkili_id=$ucretlendirilen->yetkili_id;
                                    $yeniucretlendirilen->save();
                                    $yenionaylanan = new Onaylanan;
                                    $yenionaylanan->servis_id = $onaylanan->servis_id;
                                    $yenionaylanan->uretimyer_id = $yeniuretimyerid;
                                    $yenionaylanan->netsiscari_id = $onaylanan->netsiscari_id;
                                    $yenionaylanan->ucretlendirilen_id = $yeniucretlendirilen->id;
                                    $yenionaylanan->yetkili_id = $onaylanan->yetkili_id;
                                    $yenionaylanan->onaytarihi = $onaylanan->onaytarihi;
                                    $yenionaylanan->onaylamatipi = $onaylanan->onaylamatipi;
                                    $yenionaylanan->save();
                                    $servistakip->ucretlendirilen_id=$yeniucretlendirilen->id;
                                    $servistakip->onaylanan_id=$yenionaylanan->id;
                                    $servistakip->save();
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Seçilen sayaçlara ait yeni ücretlendirme oluşturulamadı', 'type' => 'error'));
                                }
                                try {
                                    $tumu = explode(',',$ucretlendirilen->secilenler);
                                    $kalanlar = BackendController::getListeFark($tumu, $secilenlist);
                                    $kalanlist = explode(',', $kalanlar);
                                    $kalansayi = count($kalanlist);
                                    $kalanfiyat = 0;
                                    $kalanfiyat2 = 0;
                                    $kalangaranti = 1;
                                    $kalanparabirimi = $ucretlendirilen->parabirimi_id;
                                    $kalanparabirimi2 = null;
                                    foreach ($kalanlist as $arizafiyatid) {
                                        $arizafiyat = ArizaFiyat::find($arizafiyatid);
                                        $kalangaranti = $kalangaranti == 0 ? 0 : $arizafiyat->ariza_garanti;
                                        $kur = 1;
                                        if ($kalanparabirimi != $arizafiyat->parabirimi_id) {
                                            if ($kalanparabirimi == 1) { //tl
                                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                                            } else { //euro dolar sterln
                                                if ($arizafiyat->parabirimi_id == 1) {
                                                    $kur = 1 / BackendController::getKurBilgisi($kalanparabirimi, $kurtarih);
                                                } else {
                                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($kalanparabirimi, $kurtarih);
                                                }
                                            }
                                        }
                                        $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                                        if ($kalanparabirimi2 == null) {
                                            if ($arizafiyat->parabirimi2_id != null) {
                                                if ($arizafiyat->parabirimi2_id == $kalanparabirimi) {
                                                    $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                                    $arizafiyat->fiyat2 = 0;
                                                    $arizafiyat->parabirimi2_id = null;
                                                } else {
                                                    $kalanparabirimi2 = $arizafiyat->parabirimi2_id;
                                                }
                                            }
                                        } else {
                                            if ($arizafiyat->parabirimi2_id != null) {
                                                if ($arizafiyat->parabirimi2_id == $kalanparabirimi) {
                                                    $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                                    $arizafiyat->fiyat2 = 0;
                                                    $arizafiyat->parabirimi2_id = null;
                                                } else if ($arizafiyat->parabirimi2_id != $kalanparabirimi2) {
                                                    DB::rollBack();
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
                                                }
                                            }
                                        }
                                        $indirim = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat * $arizafiyat->indirimorani) / 100 : 0;
                                        $indirim2 = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat2 * $arizafiyat->indirimorani) / 100 : 0;
                                        $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                                        $arizafiyat->tutar2 = $arizafiyat->fiyat2 - $indirim2;
                                        $arizafiyat->kdv = ($arizafiyat->tutar * 18) / 100;
                                        $arizafiyat->kdv2 = ($arizafiyat->tutar2 * 18) / 100;
                                        $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                                        $arizafiyat->toplamtutar2 = round(($arizafiyat->tutar2 + $arizafiyat->kdv2) * 2) / 2;
                                        $arizafiyat->parabirimi_id = $kalanparabirimi;
                                        $arizafiyat->save();
                                        $kalanfiyat += $arizafiyat->toplamtutar;
                                        $kalanfiyat2 += $arizafiyat->toplamtutar2;
                                    }
                                    $ucretlendirilen->secilenler = $kalanlar;
                                    $ucretlendirilen->garanti = $kalangaranti;
                                    $ucretlendirilen->sayacsayisi = $kalansayi;
                                    $ucretlendirilen->fiyat = $kalanfiyat;
                                    $ucretlendirilen->fiyat2 = $kalanfiyat2;
                                    $ucretlendirilen->parabirimi_id = $kalanparabirimi;
                                    $ucretlendirilen->parabirimi2_id = $kalanparabirimi2;
                                    $ucretlendirilen->save();
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Kalan Sayaçlara Ait Ücretlendirme Güncellenemedi', 'type' => 'error'));
                                }
                                $eskiyetkili = Yetkili::find($ucretlendirilen->yetkili_id);
                                $yetkili = Yetkili::where('netsiscari_id',$servistakip->netsiscari_id)->where('aktif',1)->first();
                                if($yetkili && $yetkili->kullanici_id==$eskiyetkili->kullanici_id){
                                    $yeniucretlendirilen->yetkili_id=$yetkili->id;
                                    $yeniucretlendirilen->save();
                                    $yenionaylanan->yetkili_id=$yetkili->id;
                                    $yenionaylanan->save();
                                }else{
                                    $kullanici = Kullanici::find($eskiyetkili->kullanici_id);
                                    if ($kullanici->grup_id == 19) { // Abone Kullanıcısı ise
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Kullanıcı Grubuna Ait Yetkili Birden Fazla Cariye Yetkili Olamaz!', 'type' => 'error'));
                                    }else{
                                        $yetkili = Yetkili::where('kullanici_id',$eskiyetkili->kullanici_id)->where('netsiscari_id',$servistakip->netsiscari_id)->first();
                                        if($yetkili){
                                            $yetkili->aktif=1;
                                            $yetkili->save();
                                            $yeniucretlendirilen->yetkili_id=$yetkili->id;
                                            $yeniucretlendirilen->save();
                                            $yenionaylanan->yetkili_id=$yetkili->id;
                                            $yenionaylanan->save();
                                        }else{
                                            $yetkili = new Yetkili;
                                            $yetkili->email = $kullanici->email;
                                            $yetkili->telefon = $kullanici->telefon;
                                            $yetkili->netsiscari_id = $servistakip->netsiscari_id;
                                            $yetkili->kullanici_id = $kullanici->id;
                                            $yetkili->aktif = 1;
                                            $yetkili->save();
                                            $yeniucretlendirilen->yetkili_id=$yetkili->id;
                                            $yeniucretlendirilen->save();
                                            $yenionaylanan->yetkili_id=$yetkili->id;
                                            $yenionaylanan->save();
                                        }
                                    }
                                }
                            }else{
                                $ucretlendirilen->uretimyer_id=$yeniuretimyerid;
                                $ucretlendirilen->save();
                                $onaylanan->uretimyer_id = $yeniuretimyerid;
                                $onaylanan->save();
                                $yetkili = Yetkili::find($onaylanan->yetkili_id);
                                $kullanici = Kullanici::find($yetkili->kullanici_id);
                                if ($kullanici->grup_id == 19) { // Abone Kullanıcısı ise
                                    try {
                                        $kullanici->girisadi = BackendController::GirisAdiBelirle($servistakip->netsiscari_id, $yeniuretimyerid);
                                        $kullanici->ilkmail = 0;
                                        $kullanici->save();
                                        $yetkili->aktif = 1;
                                        $yetkili->save();
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Yetkili Bilgisi Kaydedilemedi', 'type' => 'error'));
                                    }
                                }else{
                                    $eskiyetkili = Yetkili::where('kullanici_id',$yetkili->kullanici_id)->where('netsiscari_id',$servistakip->netsiscari_id)->first();
                                    if($eskiyetkili){
                                        $eskiyetkili->aktif=1;
                                        $eskiyetkili->save();
                                        $onaylanan->yetkili_id=$eskiyetkili->id;
                                        $onaylanan->save();
                                        $ucretlendirilen->yetkili_id=$eskiyetkili->id;
                                        $ucretlendirilen->save();
                                    }else{
                                        $yetkili->netsiscari_id=$servistakip->netsiscari_id;
                                        $yetkili->save();
                                    }
                                }
                            }
                        }
                    }
                    if($servistakip->ucretlendirilen_id && $onaydurum==0){
                        $ucretlendirilen = Ucretlendirilen::find($servistakip->ucretlendirilen_id);
                        if(!$ucretlendirilen) {
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Ücretlendirme Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        if($ucretlendirilen->sayacsayisi>1){
                            try {
                                $yeniucretlendirilen = new Ucretlendirilen;
                                $yeniucretlendirilen->servis_id = $ucretlendirilen->servis_id;
                                $yeniucretlendirilen->uretimyer_id = $yeniuretimyerid;
                                $yeniucretlendirilen->netsiscari_id = $ucretlendirilen->netsiscari_id;
                                $yeniucretlendirilen->secilenler = $servistakip->arizafiyat_id;
                                $secilenlist = array($servistakip->arizafiyat_id);
                                $kurtarih = $ucretlendirilen->kurtarihi;
                                $yenifiyat = 0;
                                $yenifiyat2 = 0;
                                $yenigaranti = 1;
                                $yeniparabirimi = $ucretlendirilen->parabirimi_id;
                                $yeniparabirimi2 = null;
                                foreach ($secilenlist as $arizafiyatid) {
                                    $arizafiyat = ArizaFiyat::find($arizafiyatid);
                                    $yenigaranti = $yenigaranti == 0 ? 0 : $arizafiyat->ariza_garanti;
                                    $kur = 1;
                                    if ($yeniparabirimi != $arizafiyat->parabirimi_id) {
                                        if ($yeniparabirimi == 1) { //tl
                                            $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                                        } else { //euro dolar sterln
                                            if ($arizafiyat->parabirimi_id == 1) {
                                                $kur = 1 / BackendController::getKurBilgisi($yeniparabirimi, $kurtarih);
                                            } else {
                                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($yeniparabirimi, $kurtarih);
                                            }
                                        }
                                    }
                                    $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                                    if($yeniparabirimi2==null){
                                        if($arizafiyat->parabirimi2_id!=null){
                                            if($arizafiyat->parabirimi2_id==$yeniparabirimi){
                                                $arizafiyat->fiyat +=$arizafiyat->fiyat2;
                                                $arizafiyat->fiyat2=0;
                                                $arizafiyat->parabirimi2_id=null;
                                            }else{
                                                $yeniparabirimi2=$arizafiyat->parabirimi2_id;
                                            }
                                        }
                                    }else{
                                        if($arizafiyat->parabirimi2_id!=null){
                                            if($arizafiyat->parabirimi2_id==$yeniparabirimi){
                                                $arizafiyat->fiyat +=$arizafiyat->fiyat2;
                                                $arizafiyat->fiyat2=0;
                                                $arizafiyat->parabirimi2_id=null;
                                            }else if($arizafiyat->parabirimi2_id!=$yeniparabirimi2){
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
                                            }
                                        }
                                    }
                                    $indirim = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat * $arizafiyat->indirimorani) / 100 : 0;
                                    $indirim2 = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat2 * $arizafiyat->indirimorani) / 100 : 0;
                                    $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                                    $arizafiyat->tutar2 = $arizafiyat->fiyat2 - $indirim2;
                                    $arizafiyat->kdv = ($arizafiyat->tutar * 18) / 100;
                                    $arizafiyat->kdv2 = ($arizafiyat->tutar2 * 18) / 100;
                                    $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                                    $arizafiyat->toplamtutar2 = round(($arizafiyat->tutar2 + $arizafiyat->kdv2) * 2) / 2;
                                    $arizafiyat->parabirimi_id = $yeniparabirimi;
                                    $arizafiyat->save();
                                    $yenifiyat += $arizafiyat->toplamtutar;
                                    $yenifiyat2 += $arizafiyat->toplamtutar2;
                                }
                                $yeniucretlendirilen->garanti = $yenigaranti;
                                $yeniucretlendirilen->sayacsayisi = 1;
                                $yeniucretlendirilen->fiyat = $yenifiyat;
                                $yeniucretlendirilen->fiyat2 = $yenifiyat2;
                                $yeniucretlendirilen->parabirimi_id = $yeniparabirimi;
                                $yeniucretlendirilen->parabirimi2_id = $yeniparabirimi2;
                                $yeniucretlendirilen->kullanici_id = Auth::user()->id;
                                $yeniucretlendirilen->kayittarihi = $ucretlendirilen->kayittarihi;
                                $yeniucretlendirilen->kurtarihi = $ucretlendirilen->kurtarihi;
                                $yeniucretlendirilen->durum = $ucretlendirilen->durum;
                                $yeniucretlendirilen->onaytipi = $ucretlendirilen->onaytipi;
                                $yeniucretlendirilen->gonderimtarihi = $ucretlendirilen->gonderimtarihi;
                                $yeniucretlendirilen->durumtarihi = $ucretlendirilen->durumtarihi;
                                $yeniucretlendirilen->yetkili_id=$ucretlendirilen->yetkili_id;
                                $yeniucretlendirilen->save();
                                $servistakip->ucretlendirilen_id=$yeniucretlendirilen->id;
                                $servistakip->save();
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Seçilen sayaçlara ait yeni ücretlendirme oluşturulamadı', 'type' => 'error'));
                            }
                            try {
                                $tumu = explode(',',$ucretlendirilen->secilenler);
                                $kalanlar = BackendController::getListeFark($tumu, $secilenlist);
                                $kalanlist = explode(',', $kalanlar);
                                $kalansayi = count($kalanlist);
                                $kalanfiyat = 0;
                                $kalanfiyat2 = 0;
                                $kalangaranti = 1;
                                $kalanparabirimi = $ucretlendirilen->parabirimi_id;
                                $kalanparabirimi2 = null;
                                foreach ($kalanlist as $arizafiyatid) {
                                    $arizafiyat = ArizaFiyat::find($arizafiyatid);
                                    $kalangaranti = $kalangaranti == 0 ? 0 : $arizafiyat->ariza_garanti;
                                    $kur = 1;
                                    if ($kalanparabirimi != $arizafiyat->parabirimi_id) {
                                        if ($kalanparabirimi == 1) { //tl
                                            $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                                        } else { //euro dolar sterln
                                            if ($arizafiyat->parabirimi_id == 1) {
                                                $kur = 1 / BackendController::getKurBilgisi($kalanparabirimi, $kurtarih);
                                            } else {
                                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($kalanparabirimi, $kurtarih);
                                            }
                                        }
                                    }
                                    $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                                    if ($kalanparabirimi2 == null) {
                                        if ($arizafiyat->parabirimi2_id != null) {
                                            if ($arizafiyat->parabirimi2_id == $kalanparabirimi) {
                                                $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                                $arizafiyat->fiyat2 = 0;
                                                $arizafiyat->parabirimi2_id = null;
                                            } else {
                                                $kalanparabirimi2 = $arizafiyat->parabirimi2_id;
                                            }
                                        }
                                    } else {
                                        if ($arizafiyat->parabirimi2_id != null) {
                                            if ($arizafiyat->parabirimi2_id == $kalanparabirimi) {
                                                $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                                $arizafiyat->fiyat2 = 0;
                                                $arizafiyat->parabirimi2_id = null;
                                            } else if ($arizafiyat->parabirimi2_id != $kalanparabirimi2) {
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
                                            }
                                        }
                                    }
                                    $indirim = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat * $arizafiyat->indirimorani) / 100 : 0;
                                    $indirim2 = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat2 * $arizafiyat->indirimorani) / 100 : 0;
                                    $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                                    $arizafiyat->tutar2 = $arizafiyat->fiyat2 - $indirim2;
                                    $arizafiyat->kdv = ($arizafiyat->tutar * 18) / 100;
                                    $arizafiyat->kdv2 = ($arizafiyat->tutar2 * 18) / 100;
                                    $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                                    $arizafiyat->toplamtutar2 = round(($arizafiyat->tutar2 + $arizafiyat->kdv2) * 2) / 2;
                                    $arizafiyat->parabirimi_id = $kalanparabirimi;
                                    $arizafiyat->save();
                                    $kalanfiyat += $arizafiyat->toplamtutar;
                                    $kalanfiyat2 += $arizafiyat->toplamtutar2;
                                }
                                $ucretlendirilen->secilenler = $kalanlar;
                                $ucretlendirilen->garanti = $kalangaranti;
                                $ucretlendirilen->sayacsayisi = $kalansayi;
                                $ucretlendirilen->fiyat = $kalanfiyat;
                                $ucretlendirilen->fiyat2 = $kalanfiyat2;
                                $ucretlendirilen->parabirimi_id = $kalanparabirimi;
                                $ucretlendirilen->parabirimi2_id = $kalanparabirimi2;
                                $ucretlendirilen->save();
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Kalan Sayaçlara Ait Ücretlendirme Güncellenemedi', 'type' => 'error'));
                            }
                            $eskiyetkili = Yetkili::find($ucretlendirilen->yetkili_id);
                            if($eskiyetkili){
                                $yetkili = Yetkili::where('netsiscari_id',$servistakip->netsiscari_id)->where('aktif',1)->first();
                                if($yetkili && $yetkili->kullanici_id==$eskiyetkili->kullanici_id){
                                    $yeniucretlendirilen->yetkili_id=$yetkili->id;
                                    $yeniucretlendirilen->save();
                                }else{
                                    $kullanici = Kullanici::find($eskiyetkili->kullanici_id);
                                    if ($kullanici->grup_id == 19) { // Abone Kullanıcısı ise
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Kullanıcı Grubuna Ait Yetkili Birden Fazla Cariye Yetkili Olamaz!', 'type' => 'error'));
                                    }else{
                                        $yetkili = Yetkili::where('kullanici_id',$eskiyetkili->kullanici_id)->where('netsiscari_id',$servistakip->netsiscari_id)->first();
                                        if($yetkili){
                                            $yetkili->aktif=1;
                                            $yetkili->save();
                                            $yeniucretlendirilen->yetkili_id=$yetkili->id;
                                            $yeniucretlendirilen->save();
                                        }else{
                                            $yetkili = new Yetkili;
                                            $yetkili->email = $kullanici->email;
                                            $yetkili->telefon = $kullanici->telefon;
                                            $yetkili->netsiscari_id = $servistakip->netsiscari_id;
                                            $yetkili->kullanici_id = $kullanici->id;
                                            $yetkili->aktif = 1;
                                            $yetkili->save();
                                            $yeniucretlendirilen->yetkili_id=$yetkili->id;
                                            $yeniucretlendirilen->save();
                                        }
                                    }
                                }
                            }
                        }else{
                            $ucretlendirilen->uretimyer_id=$yeniuretimyerid;
                            $ucretlendirilen->save();
                            $yetkili = Yetkili::find($ucretlendirilen->yetkili_id);
                            if($yetkili){
                                $kullanici = Kullanici::find($yetkili->kullanici_id);
                                if ($kullanici->grup_id == 19) { // Abone Kullanıcısı ise
                                    try {
                                        $kullanici->girisadi = BackendController::GirisAdiBelirle($servistakip->netsiscari_id, $yeniuretimyerid);
                                        $kullanici->ilkmail = 0;
                                        $kullanici->save();
                                        $yetkili->aktif = 1;
                                        $yetkili->save();
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Yetkili Bilgisi Kaydedilemedi', 'type' => 'error'));
                                    }
                                }else{
                                    $eskiyetkili = Yetkili::where('kullanici_id',$yetkili->kullanici_id)->where('netsiscari_id',$servistakip->netsiscari_id)->first();
                                    if($eskiyetkili){
                                        $eskiyetkili->aktif=1;
                                        $eskiyetkili->save();
                                        $ucretlendirilen->yetkili_id=$eskiyetkili->id;
                                        $ucretlendirilen->save();
                                    }else{
                                        $yetkili->netsiscari_id=$servistakip->netsiscari_id;
                                        $yetkili->save();
                                    }
                                }
                            }
                        }
                    }
                    $arizafiyat=NULL;
                    if($servistakip->arizafiyat_id){
                        $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                        if(!$arizafiyat){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Arıza Fiyat Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        $arizafiyat->uretimyer_id=$yeniuretimyerid;
                        $arizafiyat->save();
                        if(!$sayacdurum){
                            $sayac=Sayac::find($arizafiyat->sayac_id);
                            $sayac->uretimyer_id=$yeniuretimyerid;
                            $sayac->save();
                        }
                        $sonuc = BackendController::OzelFiyatGuncelle($servistakip->arizafiyat_id);
                        if(!$sonuc['durum']){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => $sonuc['error'], 'type' => 'error'));
                        }
                    }
                    $sayacgelen=NULL;
                    if($servistakip->sayacgelen_id) {
                        $sayacgelen = SayacGelen::find($servistakip->sayacgelen_id);
                        if (!$sayacgelen) {
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Sayaç Kayıt Bilgisi Bulunamadı!', 'type' => 'error'));
                        }
                        $sayacgelen->uretimyer_id=$yeniuretimyerid;
                        $sayacgelen->save();
                    }

                    $cariyer = CariYer::where('netsiscari_id', $servistakip->netsiscari_id)->where('uretimyer_id', $servistakip->uretimyer_id)->where('durum', 1)->first();
                    if (!$cariyer) {
                        $cariyer = CariYer::where('netsiscari_id', $servistakip->netsiscari_id)->where('uretimyer_id', $yeniuretimyerid)->first();
                        if ($cariyer) {
                            $cariyer->durum = 1;
                            $cariyer->save();
                        } else {
                            $cariyer = new CariYer;
                            $cariyer->uretimyer_id = $yeniuretimyerid;
                            $cariyer->netsiscari_id = $servistakip->netsiscari_id;
                            $cariyer->durum = 1;
                            $cariyer->kullanici_id = Auth::user()->id;
                            $cariyer->save();
                        }
                    }
                    break;
                case 6: //TODO şube için tekrar yapılacak
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Şube için Geliş Yeri Bilgisi değişikliği Aktif Değil', 'type' => 'error'));
                    break;
            }
            DB::commit();
            return Redirect::to('digerdatabase/islemduzenle')->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alındı', 'text' => '', 'type' => 'success'));
        }catch (Exception $e){
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Bilgisi Düzenleme Hatası', 'text' => 'Geliş Yeri Bilgisi Düzenlenirken Hata Oluştu.', 'type' => 'error'));
        }
    }

    public function postIslemgerial(){
        try {
            DB::beginTransaction();
            $servistakipid = Input::get('islemgerialservistakipid');
            $yenisondurum = Input::get('islemgerialislemadi');
            $servistakip = ServisTakip::find($servistakipid);
            $sayacgelen = SayacGelen::find($servistakip->sayacgelen_id);
            $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
            $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
            $ucretlendirilen = Ucretlendirilen::find($servistakip->ucretlendirilen_id);
            $onaylanan = Onaylanan::find($servistakip->onaylanan_id);
            $kalibrasyon = Kalibrasyon::find($servistakip->kalibrasyon_id);
            $depoteslim = DepoTeslim::find($servistakip->depoteslim_id);
            $eskidurum = $servistakip->durum;
            if($yenisondurum>=$eskidurum){
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => 'İşlem Geri Alma yalnızca geriye dönük yapılabilir!', 'type' => 'error'));
            }
            $uyari = "";
            switch ($servistakip->servis_id){
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                    switch ($servistakip->durum){
                        case 1: //sayac kayıt edilmiş arızakayıt bekliyor
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => 'Geri Alınacak İşlem Bulunamadı', 'type' => 'error'));
                            break;
                        case 2: //arıza kayıdı yapıldı
                            try {
                                $result = $this->ArizaKayitSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$depoteslim);
                                if($result['durum']){
                                    DB::rollBack();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                }
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => 'İşlem Geri Alınırken Sorun Oluştu.', 'type' => 'error'));
                            }
                            break;//OK
                        case 3: //ücretlendirildi
                            try{
                                switch ($yenisondurum) {
                                    case 1: //arızakayıt sil
                                        $result=$this->UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        if($servistakip->servis_id == 5){
                                            $result=$this->KalibrasyonSil($servistakip,$sayacgelen,$kalibrasyon);
                                            if($result['durum']){
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                            }
                                        }
                                        $result=$this->ArizaKayitSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$depoteslim);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        break;
                                    case 2: //fiyatlandırma bekleyenlere al
                                        $result=$this->UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                }
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => 'İşlem Geri Alınırken Sorun Oluştu.', 'type' => 'error'));
                            }
                            break;//OK
                        case 4: //onay formu gönderildi
                            try{
                                switch ($yenisondurum){
                                    case 1: //arızakayıt sil
                                        $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                        $result=$this->UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        if ($servistakip->servis_id == 5){ //kalibrasyon durumuna göre
                                            $result=$this->KalibrasyonSil($servistakip,$sayacgelen,$kalibrasyon);
                                            if($result['durum']){
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                            }
                                        }
                                        $result=$this->ArizaKayitSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$depoteslim);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        break;
                                    case 2: //fiyatlandırma bekleyenlere al
                                        $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                        $result=$this->UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        break;
                                    case 3: //gönderilmeyi bekliyor şekline al
                                        $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        break;
                                }
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => 'İşlem Geri Alınırken Sorun Oluştu.', 'type' => 'error'));
                            }
                            break;//OK
                        case 5: //onaylandı
                            try{
                                switch ($yenisondurum){
                                    case 1: //arızakayıt sil
                                        if($sayacgelen->kalibrasyon){
                                            $result=$this->DepoTeslimSil($servistakip,$sayacgelen);
                                            if($result['durum']){
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                            }
                                        }
                                        $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                        $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                        $result=$this->UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        if ($servistakip->servis_id == 5){ //kalibrasyon durumuna göre
                                            $result=$this->KalibrasyonSil($servistakip,$sayacgelen,$kalibrasyon);
                                            if($result['durum']){
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                            }
                                        }
                                        $result=$this->ArizaKayitSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$depoteslim);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        break;
                                    case 2: //fiyatlandırma bekleyenlere al
                                        if($sayacgelen->kalibrasyon){
                                            $result=$this->DepoTeslimSil($servistakip,$sayacgelen);
                                            if($result['durum']){
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                            }
                                        }
                                        $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                        $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                        $result=$this->UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        break;
                                    case 3: //gönderilmeyi bekliyor şekline al
                                        if($sayacgelen->kalibrasyon){
                                            $result=$this->DepoTeslimSil($servistakip,$sayacgelen);
                                            if($result['durum']){
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                            }
                                        }
                                        $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                        $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        break;
                                    case 4: //onay formu gönderilmiş haline al
                                        if($sayacgelen->kalibrasyon){
                                            $result=$this->DepoTeslimSil($servistakip,$sayacgelen);
                                            if($result['durum']){
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                            }
                                        }
                                        $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        break;
                                }
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => 'İşlem Geri Alınırken Sorun Oluştu.', 'type' => 'error'));
                            }
                            break;//OK
                        case 6: //reddedildi
                            switch ($yenisondurum){
                                case 1: //arızakayıt sil
                                    $result=$this->MusteriReddiSil($servistakip,$sayacgelen,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    if($servistakip->servis_id == 5){
                                        $result=$this->KalibrasyonSil($servistakip,$sayacgelen,$kalibrasyon);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                    }
                                    $result=$this->ArizaKayitSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$depoteslim);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 2: //fiyatlandırma bekleyenlere al
                                    $result=$this->MusteriReddiSil($servistakip,$sayacgelen,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 3: //gönderilmeyi bekliyor şekline al
                                    $result=$this->MusteriReddiSil($servistakip,$sayacgelen,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 4: //onay formu gönderilmiş haline al
                                    $result=$this->MusteriReddiSil($servistakip,$sayacgelen,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                            }
                            break;//OK
                        case 7: //tekrar ücretlendirildi
                            switch ($yenisondurum){
                                case 1: //arızakayıt sil
                                    $result=$this->TekrarUcretlendirmeSil($servistakip,$sayacgelen,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->MusteriReddiSil($servistakip,$sayacgelen,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    if($servistakip->servis_id == 5){
                                        $result=$this->KalibrasyonSil($servistakip,$sayacgelen,$kalibrasyon);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                    }
                                    $result=$this->ArizaKayitSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$depoteslim);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 2: //fiyatlandırma bekleyenlere al
                                    $result=$this->TekrarUcretlendirmeSil($servistakip,$sayacgelen,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->MusteriReddiSil($servistakip,$sayacgelen,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 3: //gönderilmeyi bekliyor şekline al
                                    $result=$this->TekrarUcretlendirmeSil($servistakip,$sayacgelen,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->MusteriReddiSil($servistakip,$sayacgelen,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 4: //onay formu gönderilmiş haline al
                                    $result=$this->TekrarUcretlendirmeSil($servistakip,$sayacgelen,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->MusteriReddiSil($servistakip,$sayacgelen,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                            }
                            break;
                        case 8: //kalibrasyonu yapıldı
                            switch ($yenisondurum){
                                case 1: //arızakayıt sil
                                    if($sayacgelen->teslimdurum){
                                        $result=$this->DepoTeslimSil($servistakip,$sayacgelen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                    }
                                    if($sayacgelen->musterionay){
                                        $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                        $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    }
                                    if($sayacgelen->fiyatlandirma){
                                        $result=$this->UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                    }
                                    if($servistakip->servis_id == 5){
                                        $result=$this->KalibrasyonSil($servistakip,$sayacgelen,$kalibrasyon);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                    }
                                    $result=$this->ArizaKayitSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$depoteslim);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 2: //fiyatlandırma bekleyenlere al
                                    if($sayacgelen->teslimdurum){
                                        $result=$this->DepoTeslimSil($servistakip,$sayacgelen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                    }
                                    if($sayacgelen->musterionay){
                                        $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                        $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    }
                                    if($sayacgelen->fiyatlandirma){
                                        $result=$this->UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                    }
                                    if($servistakip->durum=8){
                                        $servistakip->durum=2;
                                        $servistakip->save();
                                    }
                                    break;
                                case 3: //gönderilmeyi bekliyor şekline al
                                    if($sayacgelen->teslimdurum){
                                        $result=$this->DepoTeslimSil($servistakip,$sayacgelen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                    }
                                    if($sayacgelen->musterionay){
                                        $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                        $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    }
                                    $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 4: //onay formu gönderilmiş haline al
                                    if($sayacgelen->teslimdurum){
                                        $result=$this->DepoTeslimSil($servistakip,$sayacgelen);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                    }
                                    if($sayacgelen->musterionay){
                                        $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                        if($result['durum']){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                        }
                                    }
                                    if($servistakip->durum=8){
                                        $servistakip->durum=4;
                                        $servistakip->save();
                                    }
                                    break;
                                case 5: //kalibrasyon bekliyor haline al
                                    $result=$this->KalibrasyonGeriAl($servistakip,$sayacgelen,$kalibrasyon);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                            }
                            break;//OK
                        case 9: //teslim edildi
                            switch ($yenisondurum){
                                case 1: //arızakayıt sil
                                    $result=$this->TeslimEdilenDepoTeslimSil($servistakip,$sayacgelen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->KalibrasyonSil($servistakip,$sayacgelen,$kalibrasyon);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->ArizaKayitSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$depoteslim);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 2: //fiyatlandırma bekleyenlere al
                                    $result=$this->TeslimEdilenDepoTeslimSil($servistakip,$sayacgelen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 3: //gönderilmeyi bekliyor şekline al
                                    $result=$this->TeslimEdilenDepoTeslimSil($servistakip,$sayacgelen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 4: //onay formu gönderilmiş haline al
                                    $result=$this->TeslimEdilenDepoTeslimSil($servistakip,$sayacgelen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 5: //kalibrasyon bekliyor haline al
                                    $result=$this->TeslimEdilenDepoTeslimSil($servistakip,$sayacgelen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->KalibrasyonGeriAl($servistakip,$sayacgelen,$kalibrasyon);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 6: //depo teslimi bekliyor haline al
                                    $result=$this->DepoTeslimGeriAl($servistakip);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }else{
                                        if($result['uyari']!="")
                                            $uyari = $result['uyari'];
                                    }
                                    break;
                            }
                            break;//OK
                        case 10: //geri gönderildi
                            switch ($yenisondurum){
                                case 1: //arızakayıt sil
                                    $result=$this->DepoTeslimSil($servistakip,$sayacgelen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->KalibrasyonSil($servistakip,$sayacgelen,$kalibrasyon);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->ArizaKayitSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$depoteslim);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 2: //fiyatlandırma bekleyenlere al
                                    $result=$this->DepoTeslimSil($servistakip,$sayacgelen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 3: //gönderilmeyi bekliyor şekline al
                                    $result=$this->DepoTeslimSil($servistakip,$sayacgelen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 4: //onay formu gönderilmiş haline al
                                    $result=$this->DepoTeslimSil($servistakip,$sayacgelen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 5: //kalibrasyon bekliyor haline al
                                    $result=$this->DepoTeslimSil($servistakip,$sayacgelen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->KalibrasyonGeriAl($servistakip,$sayacgelen,$kalibrasyon);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 6: //depo teslimi bekliyor haline al
                                    $result=$this->DepoTeslimGeriAl($servistakip);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }else{
                                        if($result['uyari']!="")
                                            $uyari = $result['uyari'];
                                    }
                                    break;
                            }
                            break;//OK
                        case 11: //hurdaya ayrıldı
                            switch ($yenisondurum){
                                case 1: //arızakayıt sil
                                    $result=$this->DepoTeslimSil($servistakip,$sayacgelen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->KalibrasyonSil($servistakip,$sayacgelen,$kalibrasyon);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->ArizaKayitSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$depoteslim);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 2: //fiyatlandırma bekleyenlere al
                                    $result=$this->DepoTeslimSil($servistakip,$sayacgelen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 3: //gönderilmeyi bekliyor şekline al
                                    $result=$this->DepoTeslimSil($servistakip,$sayacgelen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $ucretlendirilen = $result['degisenler']['ucretlendirilen'];
                                    $result=$this->OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 4: //onay formu gönderilmiş haline al
                                    $result=$this->DepoTeslimSil($servistakip,$sayacgelen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                                case 5: //kalibrasyon bekliyor haline al
                                    $result=$this->DepoTeslimSil($servistakip,$sayacgelen);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    $result=$this->KalibrasyonGeriAl($servistakip,$sayacgelen,$kalibrasyon);
                                    if($result['durum']){
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alınamadı', 'text' => $result['mesaj'], 'type' => 'error'));
                                    }
                                    break;
                            }
                            break;//OK
                    }
                    break;
                case 6: //TODO şube için tekrar yapılacak
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alma Hatası', 'text' => 'Şube için İşlem Geri Alma Aktif Değil', 'type' => 'error'));
                    break;
            }
            DB::commit();
            if($uyari!=""){
                return Redirect::to('digerdatabase/islemduzenle')->with(array('mesaj' => 'true', 'title' => 'İstenilen İşlem Yapıldı', 'text' => 'İstenilen İşlem Başarıyla Yapıldı.'.$uyari, 'type' => 'warning'));
            }else{
                return Redirect::to('digerdatabase/islemduzenle')->with(array('mesaj' => 'true', 'title' => 'İstenilen İşlem Yapıldı', 'text' => 'İstenilen İşlem Başarıyla Yapıldı.', 'type' => 'success'));
            }
            }catch (Exception $e){
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Geri Alma Hatası', 'text' => 'İşlem Geri Alınırken Hata Oluştu.', 'type' => 'error'));
        }
    }

    public function getUcretlendirilenbilgi() {
        try {
            $id=Input::get('id');
            $servistakip = ServisTakip::find($id);
            $ucretlendirilen = Ucretlendirilen::find($servistakip->ucretlendirilen_id);
            $ucretlendirilen->uretimyer = Uretimyer::find($ucretlendirilen->uretimyer_id);
            $ucretlendirilen->parabirimi = ParaBirimi::find($ucretlendirilen->parabirimi_id);
            $secilenler = explode(',', $ucretlendirilen->secilenler);
            $ucretlendirilen->arizafiyat = ArizaFiyat::whereIn('id', $secilenler)->get();
            foreach ($ucretlendirilen->arizafiyat as $arizafiyat) {
                $arizafiyat->sayacadi = SayacAdi::find($arizafiyat->sayacadi_id);
                $arizafiyat->sayaccap = SayacCap::find($arizafiyat->sayaccap_id);
                $arizafiyat->parabirimi = ParaBirimi::find($arizafiyat->parabirimi_id);
            }
            return Response::json(array('durum'=>true,'ucretlendirilen' => $ucretlendirilen));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title'=>'Ücretlendirme Bilgisi Hatalı','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

    public function getTeslimatbilgi() {
        try {
            $id=Input::get('id');
            $servistakip = ServisTakip::find($id);
            $depoteslim = DepoTeslim::find($servistakip->depoteslim_id);
            $depoteslim->netsiscari = Netsiscari::find($depoteslim->netsiscari_id);
            $depoteslim->servis = Servis::find($depoteslim->servis_id);
            if ($depoteslim->teslimtarihi)
                $depoteslim->teslimtarihi = date('d-m-Y', strtotime($depoteslim->teslimtarihi));
            else
                $depoteslim->teslimtarihi = "";
            $secilenler = explode(',', $depoteslim->secilenler);
            $depoteslim->sayacgelen = SayacGelen::whereIn('id', $secilenler)->get();
            foreach ($depoteslim->sayacgelen as $sayacgelen) {
                $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                $sayacgelen->uretimyer = UretimYer::find($sayacgelen->uretimyer_id);
                if ($sayacgelen->teslimdurum == 0)
                    $sayacgelen->durum = "Teslim Edilmedi";
                else if ($sayacgelen->teslimdurum == 1)
                    $sayacgelen->durum = "Teslimat";
                else if ($sayacgelen->teslimdurum == 2)
                    $sayacgelen->durum = "Geri Gönderim";
                else if ($sayacgelen->teslimdurum == 3)
                    $sayacgelen->durum = "Hurda";
                else if ($sayacgelen->teslimdurum == 4)
                    $sayacgelen->durum = "Depolararası";
                else
                    $sayacgelen->durum = "Periyodik Bakım";
            }
            return Response::json(array('durum'=>true,'depoteslim' => $depoteslim ));
        } catch (Exception $e) {
            return Response::json(array('durum' => false, 'title' => 'Depo Teslimat Bilgisinde Hata', 'text' => str_replace("'","\'",$e->getMessage())));
        }
    }

    public function getDepolararasibilgi() {
        try {
            $id=Input::get('id');
            $servistakip = ServisTakip::find($id);
            $depolararasi = Depolararasi::find($servistakip->depolararasi_id);
            $depolararasi->netsiscari = Netsiscari::find($depolararasi->netsiscari_id);
            $depolararasi->netsisdepo = NetsisDepolar::where('kodu', $depolararasi->aktarilandepo)->first();
            if ($depolararasi->teslimtarihi)
                $depolararasi->teslimtarihi = date('d-m-Y', strtotime($depolararasi->teslimtarihi));
            else
                $depolararasi->teslimtarihi = "";
            $secilenler = explode(',', $depolararasi->secilenler);
            $depolararasi->sayacgelen = SayacGelen::whereIn('id', $secilenler)->get();
            foreach ($depolararasi->sayacgelen as $sayacgelen) {
                $sayacgelen->uretimyer = UretimYer::find($sayacgelen->uretimyer_id);
                $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                if($sayacgelen->teslimdurum == 2)
                    $sayacgelen->durum = "Geri Gönderim";
                else if ($sayacgelen->depolararasi == 0)
                    $sayacgelen->durum = "Teslim Edilmedi";
                else if ($sayacgelen->depolararasi == 1)
                    $sayacgelen->durum = "Teslimat";
                else
                    $sayacgelen->durum = "Hurda";
            }
            return Response::json(array('durum'=>true,'depolararasi' => $depolararasi ));
        } catch (Exception $e) {
            return Response::json(array('durum'=>false,'title'=>'Depolararası Bilgisi Alınamadı!','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

    public function getAboneteslimbilgi() {
        try {
            $id=Input::get('id');
            $servistakip = ServisTakip::find($id);
            $aboneteslim = AboneTeslim::find($servistakip->aboneteslim_id);
            $aboneteslim->abone = Abone::find($aboneteslim->abone_id);
            if ($aboneteslim->teslimtarihi)
                $aboneteslim->teslimtarihi = date('d-m-Y', strtotime($aboneteslim->teslimtarihi));
            else
                $aboneteslim->teslimtarihi = "";
            $secilenler = explode(',', $aboneteslim->secilenler);
            $aboneteslim->sayacgelen = SayacGelen::whereIn('id', $secilenler)->get();
            if($aboneteslim->kasakodu)
                $aboneteslim->kasakod = KasaKod::where('kasakod', $aboneteslim->kasakodu)->first();
            foreach ($aboneteslim->sayacgelen as $sayacgelen) {
                $sayacgelen->uretimyer = UretimYer::find($sayacgelen->uretimyer_id);
                $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                if ($sayacgelen->aboneteslim)
                    $sayacgelen->durum = "Teslim Edildi";
                else
                    $sayacgelen->durum = "Teslim Edilmedi";
                $sayacgelen->arizafiyat = ArizaFiyat::where('sayacgelen_id', $sayacgelen->id)->first();
                $sayacgelen->parabirimi = ParaBirimi::find($sayacgelen->arizafiyat->parabirimi_id);
            }
            return Response::json(array('durum'=>true,'aboneteslim' => $aboneteslim ));
        } catch (Exception $e) {
            return Response::json(array('durum' => false,'title'=>'Abone Teslimat Bilgisi Getirilemedi','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error' ));
        }
    }

    public function getGelenbilgi() {
        try {
            $id=Input::get('id');
            $servistakip = ServisTakip::find($id);
            $depogelen = DepoGelen::find($servistakip->depogelen_id);
            $depogelen->tarih = date("d-m-Y", strtotime($depogelen->tarih));
            $depogelen->netsiscari = NetsisCari::where('carikod', $depogelen->carikod)->first();
            $depogelen->servisstokkod = ServisStokKod::where('stokkodu', $depogelen->servisstokkodu)->first();
            $sayacgelen = SayacGelen::where('depogelen_id', $depogelen->id)->get();
            foreach ($sayacgelen as $gelen) {
                $gelen->servistakip=ServisTakip::where('sayacgelen_id',$gelen->id)->first();
                $gelen->sayacadi = SayacAdi::find($gelen->sayacadi_id);
                $gelen->sayaccap = SayacCap::find($gelen->sayaccap_id);
                $gelen->uretimyer = UretimYer::find($gelen->uretimyer_id);
                $gelen->kayittarihi = date("d-m-Y", strtotime($gelen->eklenmetarihi));
            }
            return Response::json(array('durum' => true, 'depogelen' => $depogelen, 'sayacgelen' => $sayacgelen));
        } catch (Exception $e) {
            return Response::json(array('durum' => false, 'title' => 'Depo Gelen Bilgisinde Hata', 'text' => str_replace("'","\'",$e->getMessage())));
        }
    }

    public function DepoTeslimSil($servistakip,$sayacgelen){
        try {
            $depoteslimler = DepoTeslim::where('netsiscari_id',$sayacgelen->netsiscari_id)->where('depodurum',0)->get();
            if ($depoteslimler->count()>0){
                foreach ($depoteslimler as $depoteslim){
                    $secilenler = $depoteslim->secilenler;
                    $secilenlist = explode(',', $secilenler);
                    $sayacsayi = 1;
                    if(BackendController::Listedemi($servistakip->sayacgelen_id,$secilenlist)){
                        try {
                            if($depoteslim->tipi==3){ //hurda
                                $hurda = Hurda::find($servistakip->hurda_id);
                                $servistakip->hurda_id = NULL;
                                $servistakip->hurdalamatarihi = NULL;
                                $servistakip->save();
                                if($hurda){
                                    $hurdanedeni = HurdaNedeni::find($hurda->hurdanedeni_id);
                                    $hurdanedeni->kullanim-=($hurdanedeni->kullanim==0 ? 0 : 1);
                                    $hurdanedeni->save();
                                    $hurda->delete();
                                }
                            }
                            $servistakip->depoteslim_id = NULL;
                            $servistakip->depoteslimtarihi = NULL;
                            $servistakip->gerigonderimtarihi = NULL;
                            $servistakip->hurdalamatarihi = NULL;
                            $servistakip->depolararasitarihi = NULL;
                            if($servistakip->onaylanan_id){
                                $servistakip->sonislemtarihi = $servistakip->onaylanmatarihi;
                                $servistakip->save();
                            }else if($servistakip->ucretlendirilen_id){
                                $servistakip->sonislemtarihi = $servistakip->ucretlendirmetarihi;
                                $servistakip->save();
                            }else{
                                $servistakip->sonislemtarihi = $servistakip->arizakayittarihi;
                                $servistakip->save();
                            }
                            $sayacgelen->teslimdurum = 0;
                            $sayacgelen->depoteslim = 0;
                            $sayacgelen->save();
                        } catch (Exception $e) {
                            Log::error($e);
                            return array('durum' => true, 'mesaj' => 'Sayaç Gelen ve Servis Takip Bilgisi Güncellenemedi');
                        }
                        try {
                            if($depoteslim->subegonderim){
                                BackendController::HatirlatmaSil(11,$servistakip->netsiscari_id,$servistakip->servis_id,$sayacsayi);
                            }else if($depoteslim->tipi==3){ //Hurda
                                BackendController::HatirlatmaSil(9,$servistakip->netsiscari_id,$servistakip->servis_id,$sayacsayi);
                            }else{
                                BackendController::HatirlatmaSil(9,$servistakip->netsiscari_id,$servistakip->servis_id,$sayacsayi);
                            }
                            if($depoteslim->sayacsayisi>1){
                                $depoteslim->sayacsayisi--;
                                $depoteslim->secilenler = BackendController::getListeFark($secilenlist, array($servistakip->sayacgelen_id));
                                $depoteslim->save();
                            }else{
                                $depoteslim->delete();
                            }
                        } catch (Exception $e) {
                            Log::error($e);
                            return array('durum' => true, 'mesaj' => 'Depo Teslim Silinirken Sorun Oluştu.');
                        }
                    }
                }
            }
            return array('durum' => false, 'mesaj' => '');
        } catch (Exception $e) {
            Log::error($e);
            return array('durum' => true, 'mesaj' => 'İşlem Geri Alınırken Hata Oluştu.');
        }
    }

    public function TeslimEdilenDepoTeslimSil($servistakip,$sayacgelen){
        try {
            $depoteslimler = DepoTeslim::where('netsiscari_id',$sayacgelen->netsiscari_id)->where('depodurum',1)->get();
            if ($depoteslimler->count()>0){
                foreach ($depoteslimler as $depoteslim){
                    $secilenler = $depoteslim->secilenler;
                    $secilenlist = explode(',', $secilenler);
                    $sayacsayi = 1;
                    if(BackendController::Listedemi($servistakip->sayacgelen_id,$secilenlist)){
                        try {
                            if($depoteslim->tipi==3){ //hurda
                                $hurda = Hurda::find($servistakip->hurda_id);
                                $servistakip->hurda_id = NULL;
                                $servistakip->hurdalamatarihi = NULL;
                                $servistakip->save();
                                if($hurda){
                                    $hurdanedeni = HurdaNedeni::find($hurda->hurdanedeni_id);
                                    $hurdanedeni->kullanim-=($hurdanedeni->kullanim==0 ? 0 : 1);
                                    $hurdanedeni->save();
                                    $hurda->delete();
                                }
                            }
                            $servistakip->depoteslim_id = NULL;
                            $servistakip->depoteslimtarihi = NULL;
                            $servistakip->gerigonderimtarihi = NULL;
                            $servistakip->hurdalamatarihi = NULL;
                            $servistakip->depolararasitarihi = NULL;
                            if($servistakip->onaylanan_id){
                                $servistakip->sonislemtarihi = $servistakip->onaylanmatarihi;
                                $servistakip->save();
                            }else if($servistakip->ucretlendirilen_id){
                                $servistakip->sonislemtarihi = $servistakip->ucretlendirmetarihi;
                                $servistakip->save();
                            }else{
                                $servistakip->sonislemtarihi = $servistakip->arizakayittarihi;
                                $servistakip->save();
                            }
                            $sayacgelen->teslimdurum = 0;
                            $sayacgelen->depoteslim = 0;
                            $sayacgelen->save();
                        } catch (Exception $e) {
                            Log::error($e);
                            return array('durum' => true, 'mesaj' => 'Sayaç Gelen ve Servis Takip Bilgisi Güncellenemedi');
                        }
                        try {
                            if($depoteslim->subegonderim){
                                BackendController::HatirlatmaSil(11,$servistakip->netsiscari_id,$servistakip->servis_id,$sayacsayi);
                            }else if($depoteslim->tipi==3){ //Hurda
                                BackendController::HatirlatmaSil(9,$servistakip->netsiscari_id,$servistakip->servis_id,$sayacsayi);
                            }else{
                                BackendController::HatirlatmaSil(9,$servistakip->netsiscari_id,$servistakip->servis_id,$sayacsayi);
                            }
                            if($depoteslim->sayacsayisi>1){
                                $depoteslim->sayacsayisi--;
                                $depoteslim->secilenler = BackendController::getListeFark($secilenlist, array($servistakip->sayacgelen_id));
                                $depoteslim->save();
                            }else{
                                $depoteslim->delete();
                            }
                        } catch (Exception $e) {
                            Log::error($e);
                            return array('durum' => true, 'mesaj' => 'Depo Teslim Silinirken Sorun Oluştu.');
                        }
                    }
                }
            }
            return array('durum' => false, 'mesaj' => '');
        } catch (Exception $e) {
            Log::error($e);
            return array('durum' => true, 'mesaj' => 'İşlem Geri Alınırken Hata Oluştu.');
        }
    }

    public function DepoTeslimGeriAl($servistakip,$sayacgelen,$arizafiyat){
        try {
            $uyari = "";
            $depoteslimler = DepoTeslim::where('netsiscari_id',$sayacgelen->netsiscari_id)->where('depodurum',1)->get();
            if ($depoteslimler->count()>0){
                foreach ($depoteslimler as $depoteslim){
                    $secilenler = $depoteslim->secilenler;
                    $secilenlist = explode(',', $secilenler);
                    if(BackendController::Listedemi($servistakip->sayacgelen_id,$secilenlist)){
                        try {
                            $uyari = is_null($depoteslim->faturano) ? "" : "Netsis Tarafında Fatura Düzenlenmesi Gerekmektedir!";
                            if($depoteslim->sayacsayisi>1){
                                $depoteslim->sayacsayisi--;
                                $depoteslim->secilenler = BackendController::getListeFark($secilenlist, array($servistakip->sayacgelen_id));
                                $depoteslim->save();
                                $yenidepoteslim = new DepoTeslim;
                                $yenidepoteslim->servis_id = $depoteslim->servis_id;
                                $yenidepoteslim->netsiscari_id = $depoteslim->netsiscari_id;
                                $yenidepoteslim->secilenler = $servistakip->sayacgelen_id;
                                $yenidepoteslim->sayacsayisi = 1;
                                $yenidepoteslim->depodurum = 0;
                                $yenidepoteslim->tipi = $depoteslim->tipi;
                                $yenidepoteslim->periyodik = $depoteslim->periyodik;
                                $yenidepoteslim->subegonderim = $depoteslim->subegonderim;
                                $yenidepoteslim->parabirimi_id = $depoteslim->parabirimi_id;
                                $yenidepoteslim->save();
                            }else{
                                $depoteslim->depodurum = 0;
                                $depoteslim->kullanici_id = NULL;
                                $depoteslim->teslimtarihi= NULL;
                                $depoteslim->db_name = NULL;
                                $depoteslim->faturano = NULL;
                                $depoteslim->carikod= NULL;
                                $depoteslim->ozelkod= NULL;
                                $depoteslim->plasiyerkod= NULL;
                                $depoteslim->faturaadres= NULL;
                                $depoteslim->teslimadres= NULL;
                                $depoteslim->depokodu= NULL;
                                $depoteslim->aciklama= NULL;
                                $depoteslim->belge1= NULL;
                                $depoteslim->belge2= NULL;
                                $depoteslim->belge3= NULL;
                                $depoteslim->netsiskullanici= NULL;
                                $depoteslim->save();
                            }
                            $servistakip->depoteslim_id = NULL;
                            $servistakip->depoteslimtarihi = NULL;
                            $servistakip->save();
                            $sayacgelen->depoteslim = 0;
                            $sayacgelen->save();

                            $sayac=Sayac::find($arizafiyat->sayac_id);
                            $songelis = ServisTakip::where('serino',$servistakip->serino)->where('netsiscari_id',$servistakip->netsiscari_id)
                                ->where('sayacadi_id',$servistakip->sayacadi_id)->orderBy('id','desc')->first();
                            $sayac->songelistarihi= $songelis ? $songelis->depoteslimtarihi : NULL;
                            $sayac->save();
                            if($depoteslim->subegonderim){
                                BackendController::HatirlatmaGeriAl(11,$servistakip->netsiscari_id,$servistakip->servis_id,1);
                                BackendController::BildirimGeriAl(11,$servistakip->netsiscari_id,$servistakip->servis_id,1);
                            }else if($depoteslim->tipi==3){ //Hurda
                                BackendController::HatirlatmaGeriAl(9,$servistakip->netsiscari_id,$servistakip->servis_id,1);
                                BackendController::BildirimGeriAl(10,$servistakip->netsiscari_id,$servistakip->servis_id,1);
                            }else{
                                BackendController::HatirlatmaGeriAl(9,$servistakip->netsiscari_id,$servistakip->servis_id,1);
                                BackendController::BildirimGeriAl(9,$servistakip->netsiscari_id,$servistakip->servis_id,1);
                            }

                        } catch (Exception $e) {
                            Log::error($e);
                            return array('durum' => true, 'mesaj' => 'Sayaç Gelen ve Servis Takip Bilgisi Güncellenemedi');
                        }
                    }
                }
            }
            return array('durum' => false, 'mesaj' => '','uyari'=>$uyari);
        } catch (Exception $e) {
            Log::error($e);
            return array('durum' => true, 'mesaj' => 'İşlem Geri Alınırken Hata Oluştu.');
        }
    }

    public function KalibrasyonSil($servistakip,$sayacgelen,$kalibrasyon){
        try{
            if($kalibrasyon){
                $kalibrasyongrup = KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
                if(!$kalibrasyongrup)
                    return array('durum' => true, 'mesaj' => 'Kalibrasyon Grubu Bulunamadı.');

                $servistakip->kalibrasyon_id = NULL;
                $servistakip->kalibrasyontarihi = NULL;
                if($servistakip->tekrarucrettarihi)
                    $servistakip->durum=7;
                else if($servistakip->reddetmetarihi)
                    $servistakip->durum=6;
                else if($servistakip->onaylanmatarihi)
                    $servistakip->durum=5;
                else if($servistakip->gondermetarihi)
                    $servistakip->durum=4;
                else if($servistakip->ucretlendirmetarihi)
                    $servistakip->durum=3;
                else if($servistakip->arizakayittarihi)
                    $servistakip->durum=2;
                $servistakip->save();

                $sayacgelen->kalibrasyon=0;
                $sayacgelen->sayacdurum=1;
                $sayacgelen->teslimdurum=0;
                $sayacgelen->beyanname=-2;
                $sayacgelen->save();

                $kalibrasyon->delete();

                if($kalibrasyongrup->adet>1){
                    $kalibrasyongrup->adet--;
                    if($kalibrasyon->durum<>0)
                        $kalibrasyongrup->biten--;
                    $kalibrasyongrup->save();
                }else{
                    $kalibrasyongrup->delete();
                }
                BackendController::HatirlatmaSil(8, $servistakip->netsiscari_id, $servistakip->servis_id, 1);
                BackendController::BildirimGeriAl(8, $servistakip->netsiscari_id, $servistakip->servis_id, 1);
            }
            return array('durum' => false, 'mesaj' => '');
        } catch (Exception $e) {
            Log::error($e);
            return array('durum' => true, 'mesaj' => 'İşlem Geri Alınırken Hata Oluştu.');
        }
    }

    public function KalibrasyonGeriAl($servistakip,$sayacgelen,$kalibrasyon){
        try{
            if($kalibrasyon){
                $kalibrasyongrup = KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
                if(!$kalibrasyongrup)
                    return array('durum' => true, 'mesaj' => 'Kalibrasyon Grubu Bulunamadı.');

                $servistakip->kalibrasyontarihi = NULL;
                if($servistakip->tekrarucrettarihi)
                    $servistakip->durum=7;
                else if($servistakip->reddetmetarihi)
                    $servistakip->durum=6;
                else if($servistakip->onaylanmatarihi)
                    $servistakip->durum=5;
                else if($servistakip->gondermetarihi)
                    $servistakip->durum=4;
                else if($servistakip->ucretlendirmetarihi)
                    $servistakip->durum=3;
                else if($servistakip->arizakayittarihi)
                    $servistakip->durum=2;
                $servistakip->save();

                $sayacgelen->kalibrasyon=0;
                $sayacgelen->sayacdurum=2;
                $sayacgelen->beyanname=-2;
                $sayacgelen->save();

                $kalibrasyon->durum = 0;
                $kalibrasyon->istasyon_id = NULL;
                $kalibrasyon->sira = NULL;
                $kalibrasyon->kalibrasyonstandart_id = NULL;
                $kalibrasyon->nokta1sapma = NULL;
                $kalibrasyon->sonuc1 = NULL;
                $kalibrasyon->nokta2sapma = NULL;
                $kalibrasyon->sonuc2 = NULL;
                $kalibrasyon->nokta3sapma = NULL;
                $kalibrasyon->sonuc3 = NULL;
                $kalibrasyon->nokta4sapma = NULL;
                $kalibrasyon->sonuc4 = NULL;
                $kalibrasyon->nokta5sapma = NULL;
                $kalibrasyon->sonuc5 = NULL;
                $kalibrasyon->nokta6sapma = NULL;
                $kalibrasyon->sonuc6 = NULL;
                $kalibrasyon->nokta7sapma = NULL;
                $kalibrasyon->sonuc7 = NULL;
                $kalibrasyon->hf2 = 0;
                $kalibrasyon->hf2nokta1sapma = NULL;
                $kalibrasyon->hf2sonuc1 = NULL;
                $kalibrasyon->hf2nokta2sapma = NULL;
                $kalibrasyon->hf2sonuc2 = NULL;
                $kalibrasyon->hf2nokta3sapma = NULL;
                $kalibrasyon->hf2sonuc3 = NULL;
                $kalibrasyon->hf2nokta4sapma = NULL;
                $kalibrasyon->hf2sonuc4 = NULL;
                $kalibrasyon->hf2nokta5sapma = NULL;
                $kalibrasyon->hf2sonuc5 = NULL;
                $kalibrasyon->hf2nokta6sapma = NULL;
                $kalibrasyon->hf2sonuc6 = NULL;
                $kalibrasyon->hf2nokta7sapma = NULL;
                $kalibrasyon->hf2sonuc7 = NULL;
                $kalibrasyon->hf3 = 0;
                $kalibrasyon->hf3nokta1sapma = NULL;
                $kalibrasyon->hf3sonuc1 = NULL;
                $kalibrasyon->hf3nokta2sapma = NULL;
                $kalibrasyon->hf3sonuc2 = NULL;
                $kalibrasyon->hf3nokta3sapma = NULL;
                $kalibrasyon->hf3sonuc3 = NULL;
                $kalibrasyon->hf3nokta4sapma = NULL;
                $kalibrasyon->hf3sonuc4 = NULL;
                $kalibrasyon->hf3nokta5sapma = NULL;
                $kalibrasyon->hf3sonuc5 = NULL;
                $kalibrasyon->hf3nokta6sapma = NULL;
                $kalibrasyon->hf3sonuc6 = NULL;
                $kalibrasyon->hf3nokta7sapma = NULL;
                $kalibrasyon->hf3sonuc7 = NULL;
                $kalibrasyon->kullanici_id = NULL;
                $kalibrasyon->kalibrasyontarih = NULL;
                $kalibrasyon->save();

                $kalibrasyongrup->biten--;
                $kalibrasyongrup->kalibrasyondurum = 0;
                $kalibrasyongrup->save();

                BackendController::HatirlatmaGeriAl(8, $servistakip->netsiscari_id, $servistakip->servis_id, 1);
                BackendController::BildirimGeriAl(8, $servistakip->netsiscari_id, $servistakip->servis_id, 1);
            }
            return array('durum' => false, 'mesaj' => '');
        } catch (Exception $e) {
            Log::error($e);
            return array('durum' => true, 'mesaj' => 'İşlem Geri Alınırken Hata Oluştu.');
        }
    }

    public function MusteriOnaySil($servistakip,$sayacgelen,$ucretlendirilen,$onaylanan){
        try{
            $sonucretlendirilen = $ucretlendirilen;
            if($onaylanan){
                if($ucretlendirilen->sayacsayisi>1){
                    $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                    $ucretlendirilenlist = explode(',', $ucretlendirilen->secilenler);
                    $ucretlendirilenler = "";
                    foreach ($ucretlendirilenlist as $arizafiyatid) {
                        if ($arizafiyatid == $servistakip->arizafiyat_id) {
                            continue;
                        } else {
                            $ucretlendirilenler .= ($ucretlendirilenler == "" ? "" : ",") . $arizafiyatid;
                        }
                    }
                    if ($ucretlendirilenler == "") {
                        $ucretlendirilen->durum = 1;
                        $ucretlendirilen->onaytarihi = NULL;
                        $ucretlendirilen->save();
                        $sayacgelen->musterionay = 0;
                        $sayacgelen->teslimdurum = 0;
                        $sayacgelen->save();
                        $servistakip->onaylanan_id = NULL;
                        $servistakip->durum = 4;
                        $servistakip->onaylanmatarihi = NULL;
                        $servistakip->save();
                        $onaylanan->delete();
                    } else {
                        $ucretlendirilen->secilenler = $ucretlendirilenler;
                        $ucretlendirilen->sayacsayisi = $ucretlendirilen->sayacsayisi - 1;
                        if ($ucretlendirilen->parabirimi_id == $arizafiyat->parabirimi_id) {
                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar;
                            if ($arizafiyat->parabirimi2_id != null) {
                                if ($ucretlendirilen->parabirimi_id == $arizafiyat->parabirimi2_id) {
                                    $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2;
                                } else {
                                    if ($ucretlendirilen->parabirimi2_id == $arizafiyat->parabirimi2_id) {
                                        $ucretlendirilen->fiyat2 -= $arizafiyat->toplamtutar2;
                                    } else {
                                        if ($ucretlendirilen->parabirimi_id == 1) {
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih);
                                        } else {
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarih);
                                        }
                                    }
                                }
                            }
                        } else {
                            if ($ucretlendirilen->parabirimi_id == 1) {
                                $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar * BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarih);
                            } else {
                                $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar * BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarih) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarih);
                            }
                            if ($arizafiyat->parabirimi2_id != null) {
                                if ($ucretlendirilen->parabirimi_id == $arizafiyat->parabirimi2_id) {
                                    $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2;
                                } else {
                                    if ($ucretlendirilen->parabirimi2_id == $arizafiyat->parabirimi2_id) {
                                        $ucretlendirilen->fiyat2 -= $arizafiyat->toplamtutar2;
                                    } else {
                                        if ($ucretlendirilen->parabirimi_id == 1) {
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih);
                                        } else {
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarih);
                                        }
                                    }
                                }
                            }
                        }
                        $ucretlendirilen->save();
                        $yeniucretlendirilen = new Ucretlendirilen;
                        $yeniucretlendirilen->servis_id = $ucretlendirilen->servis_id;
                        $yeniucretlendirilen->uretimyer_id = $ucretlendirilen->uretimyer_id;
                        $yeniucretlendirilen->netsiscari_id = $ucretlendirilen->netsiscari_id;
                        $yeniucretlendirilen->secilenler = $servistakip->arizafiyat_id;
                        $yeniucretlendirilen->garanti = $arizafiyat->ariza_garanti;
                        $yeniucretlendirilen->sayacsayisi = 1;
                        $yeniucretlendirilen->fiyat = $arizafiyat->toplamtutar;
                        $yeniucretlendirilen->fiyat2 = $arizafiyat->toplamtutar2;
                        $yeniucretlendirilen->parabirimi_id = $arizafiyat->parabirimi_id;
                        $yeniucretlendirilen->parabirimi2_id = $arizafiyat->parabirimi2_id;
                        $yeniucretlendirilen->kullanici_id = $ucretlendirilen->kullanici_id;
                        $yeniucretlendirilen->kayittarihi = $ucretlendirilen->kayittarihi;
                        $yeniucretlendirilen->kurtarihi = $ucretlendirilen->kurtarihi;
                        $yeniucretlendirilen->durum = 1;
                        $yeniucretlendirilen->yetkili_id = $ucretlendirilen->yetkili_id;
                        $yeniucretlendirilen->onaytipi = $ucretlendirilen->onaytipi;
                        $yeniucretlendirilen->mail = $ucretlendirilen->mail;
                        $yeniucretlendirilen->gonderimtarihi = $ucretlendirilen->gonderimtarihi;
                        $yeniucretlendirilen->dosyalar = null;
                        $yeniucretlendirilen->durumtarihi = $ucretlendirilen->durumtarihi;
                        $yeniucretlendirilen->save();
                        $sayacgelen->musterionay = 0;
                        $sayacgelen->teslimdurum = 0;
                        $sayacgelen->save();
                        $servistakip->ucretlendirilen_id = $yeniucretlendirilen->id;
                        $servistakip->onaylanan_id = NULL;
                        $servistakip->durum = 4;
                        $servistakip->onaylanmatarihi = NULL;
                        $servistakip->save();
                        $sonucretlendirilen = $yeniucretlendirilen;
                    }
                }else{
                    $ucretlendirilen->durum = 1;
                    $ucretlendirilen->onaytarihi = NULL;
                    $ucretlendirilen->save();
                    $sayacgelen->musterionay = 0;
                    $sayacgelen->teslimdurum = 0;
                    $sayacgelen->save();
                    $servistakip->onaylanan_id = NULL;
                    $servistakip->durum = 4;
                    $servistakip->onaylanmatarihi = NULL;
                    $servistakip->save();
                    $onaylanan->delete();
                }
                BackendController::HatirlatmaGeriAl(6, $servistakip->netsiscari_id, $servistakip->servis_id, 1);
                BackendController::BildirimGeriAl(7, $servistakip->netsiscari_id, $servistakip->servis_id, 1);
            }
            return array('durum' => false, 'mesaj' => '','degisenler'=>array('ucretlendirilen'=>$sonucretlendirilen));
        } catch (Exception $e) {
            Log::error($e);
            return array('durum' => true, 'mesaj' => 'İşlem Geri Alınırken Hata Oluştu.');
        }
    }

    public function MusteriReddiSil($servistakip,$sayacgelen,$arizafiyat,$ucretlendirilen){
        try{
            $sonucretlendirilen = $ucretlendirilen;
            if($ucretlendirilen){
                if($ucretlendirilen->sayacsayisi>1){
                    $ucretlendirilenlist = explode(',', $ucretlendirilen->secilenler);
                    $kalanlist = array();
                    $ucretlendirilenler = "";
                    foreach ($ucretlendirilenlist as $arizafiyatid) {
                        if ($arizafiyatid == $servistakip->arizafiyat_id) {
                            continue;
                        } else {
                            $ucretlendirilenler .= ($ucretlendirilenler == "" ? "" : ",") . $arizafiyatid;
                            array_push($kalanlist,$arizafiyatid);
                        }
                    }
                    if ($ucretlendirilenler == "") {
                        $ucretlendirilen->durum = 1;
                        $ucretlendirilen->reddetmetarihi = NULL;
                        $ucretlendirilen->musterinotu = NULL;
                        $ucretlendirilen->reddedilenler = NULL;
                        $ucretlendirilen->save();
                        $arizafiyat->tekrarkayittarihi = NULL;
                        $arizafiyat->gerigonderimtarihi = NULL;
                        $arizafiyat->durum = 1;
                        $arizafiyat->save();
                        $sayacgelen->fiyatlandirma = 1;
                        $sayacgelen->musterionay = 0;
                        $sayacgelen->teslimdurum = 0;
                        $sayacgelen->save();
                        $servistakip->durum = 4;
                        $servistakip->reddetmetarihi = NULL;
                        $servistakip->save();
                        $arizafiyateski = ArizaFiyatEski::where('arizafiyat_id',$arizafiyat->id)->first();
                        if($arizafiyateski)
                            $arizafiyateski->delete();
                    } else {
                        $ucretlendirilen->secilenler = $ucretlendirilenler;
                        $ucretlendirilen->sayacsayisi = $ucretlendirilen->sayacsayisi - 1;
                        $ucretlendirilen->reddedilenler = BackendController::getListeFark($kalanlist,array($arizafiyat->id));
                        if ($ucretlendirilen->parabirimi_id == $arizafiyat->parabirimi_id) {
                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar;
                            if ($arizafiyat->parabirimi2_id != null) {
                                if ($ucretlendirilen->parabirimi_id == $arizafiyat->parabirimi2_id) {
                                    $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2;
                                } else {
                                    if ($ucretlendirilen->parabirimi2_id == $arizafiyat->parabirimi2_id) {
                                        $ucretlendirilen->fiyat2 -= $arizafiyat->toplamtutar2;
                                    } else {
                                        if ($ucretlendirilen->parabirimi_id == 1) {
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih);
                                        } else {
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarih);
                                        }
                                    }
                                }
                            }
                        } else {
                            if ($ucretlendirilen->parabirimi_id == 1) {
                                $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar * BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarih);
                            } else {
                                $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar * BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarih) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarih);
                            }
                            if ($arizafiyat->parabirimi2_id != null) {
                                if ($ucretlendirilen->parabirimi_id == $arizafiyat->parabirimi2_id) {
                                    $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2;
                                } else {
                                    if ($ucretlendirilen->parabirimi2_id == $arizafiyat->parabirimi2_id) {
                                        $ucretlendirilen->fiyat2 -= $arizafiyat->toplamtutar2;
                                    } else {
                                        if ($ucretlendirilen->parabirimi_id == 1) {
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih);
                                        } else {
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarih);
                                        }
                                    }
                                }
                            }
                        }
                        $ucretlendirilen->save();
                        $yeniucretlendirilen = new Ucretlendirilen;
                        $yeniucretlendirilen->servis_id = $ucretlendirilen->servis_id;
                        $yeniucretlendirilen->uretimyer_id = $ucretlendirilen->uretimyer_id;
                        $yeniucretlendirilen->netsiscari_id = $ucretlendirilen->netsiscari_id;
                        $yeniucretlendirilen->secilenler = $servistakip->arizafiyat_id;
                        $yeniucretlendirilen->garanti = $arizafiyat->ariza_garanti;
                        $yeniucretlendirilen->sayacsayisi = 1;
                        $yeniucretlendirilen->fiyat = $arizafiyat->toplamtutar;
                        $yeniucretlendirilen->fiyat2 = $arizafiyat->toplamtutar2;
                        $yeniucretlendirilen->parabirimi_id = $arizafiyat->parabirimi_id;
                        $yeniucretlendirilen->parabirimi2_id = $arizafiyat->parabirimi2_id;
                        $yeniucretlendirilen->kullanici_id = $ucretlendirilen->kullanici_id;
                        $yeniucretlendirilen->kayittarihi = $ucretlendirilen->kayittarihi;
                        $yeniucretlendirilen->kurtarihi = $ucretlendirilen->kurtarihi;
                        $yeniucretlendirilen->durum = 1;
                        $yeniucretlendirilen->yetkili_id = $ucretlendirilen->yetkili_id;
                        $yeniucretlendirilen->onaytipi = $ucretlendirilen->onaytipi;
                        $yeniucretlendirilen->mail = $ucretlendirilen->mail;
                        $yeniucretlendirilen->gonderimtarihi = $ucretlendirilen->gonderimtarihi;
                        $yeniucretlendirilen->dosyalar = null;
                        $yeniucretlendirilen->durumtarihi = $ucretlendirilen->durumtarihi;
                        $yeniucretlendirilen->save();
                        $arizafiyat->tekrarkayittarihi = NULL;
                        $arizafiyat->gerigonderimtarihi = NULL;
                        $arizafiyat->durum = 1;
                        $arizafiyat->save();
                        $sayacgelen->fiyatlandirma = 1;
                        $sayacgelen->musterionay = 0;
                        $sayacgelen->teslimdurum = 0;
                        $sayacgelen->save();
                        $servistakip->ucretlendirilen_id = $yeniucretlendirilen->id;
                        $servistakip->durum = 4;
                        $servistakip->reddetmetarihi = NULL;
                        $servistakip->save();
                        $arizafiyateski = ArizaFiyatEski::where('arizafiyat_id',$arizafiyat->id)->first();
                        if($arizafiyateski)
                            $arizafiyateski->delete();
                        $sonucretlendirilen = $yeniucretlendirilen;
                    }
                }else{
                    $ucretlendirilen->durum = 1;
                    $ucretlendirilen->reddetmetarihi = NULL;
                    $ucretlendirilen->musterinotu = NULL;
                    $ucretlendirilen->reddedilenler = NULL;
                    $ucretlendirilen->save();
                    $arizafiyat->tekrarkayittarihi = NULL;
                    $arizafiyat->gerigonderimtarihi = NULL;
                    $arizafiyat->durum = 1;
                    $arizafiyat->save();
                    $sayacgelen->fiyatlandirma = 1;
                    $sayacgelen->musterionay = 0;
                    $sayacgelen->teslimdurum = 0;
                    $sayacgelen->save();
                    $servistakip->durum = 4;
                    $servistakip->reddetmetarihi = NULL;
                    $servistakip->save();
                    $arizafiyateski = ArizaFiyatEski::where('arizafiyat_id',$arizafiyat->id)->first();
                    if($arizafiyateski)
                        $arizafiyateski->delete();
                }
                BackendController::HatirlatmaGeriAl(6, $servistakip->netsiscari_id, $servistakip->servis_id, 1);
                BackendController::HatirlatmaSil(7, $servistakip->netsiscari_id, $servistakip->servis_id, 1);
                BackendController::BildirimGeriAl(6, $servistakip->netsiscari_id, $servistakip->servis_id, 1);
            }
            return array('durum' => false, 'mesaj' => '','degisenler'=>array('ucretlendirilen'=>$sonucretlendirilen));
        } catch (Exception $e) {
            Log::error($e);
            return array('durum' => true, 'mesaj' => 'İşlem Geri Alınırken Hata Oluştu.');
        }
    }

    public function TekrarUcretlendirmeSil($servistakip,$sayacgelen,$arizafiyat,$ucretlendirilen){
        try {
            $sonucretlendirilen = $ucretlendirilen;
            if($ucretlendirilen->durum==3){
                if(is_null($ucretlendirilen->reddedilenler)){
                    $ucretlendirilen->reddedilenler =$servistakip->arizafiyat_id;
                    $ucretlendirilen->save();
                }else{
                    $ucretlendirilen->reddedilenler .=($ucretlendirilen->reddedilenler=="" ? ""  : ",").$servistakip->arizafiyat_id;
                    $ucretlendirilen->save();
                }
            }else{ //eski ücretlendirmesine eklenecek yenisinden silinecek
                $ucretlendirilenlist = explode(',', $ucretlendirilen->secilenler);
                $ucretlendirilenler = "";
                foreach ($ucretlendirilenlist as $arizafiyatid) {
                    if ($arizafiyatid == $servistakip->arizafiyat_id) {
                        continue;
                    } else {
                        $ucretlendirilenler .= ($ucretlendirilenler == "" ? "" : ",") . $arizafiyatid;
                    }
                }
                if ($ucretlendirilenler == "") {
                    $servistakip->ucretlendirilen_id = NULL;
                    $servistakip->save();
                    $ucretlendirilen->delete();
                } else {
                    $ucretlendirilen->secilenler = $ucretlendirilenler;
                    $ucretlendirilen->sayacsayisi = $ucretlendirilen->sayacsayisi - 1;
                    if ($ucretlendirilen->parabirimi_id == $arizafiyat->parabirimi_id) {
                        $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar;
                        if ($arizafiyat->parabirimi2_id != null) {
                            if ($ucretlendirilen->parabirimi_id == $arizafiyat->parabirimi2_id) {
                                $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2;
                            } else {
                                if ($ucretlendirilen->parabirimi2_id == $arizafiyat->parabirimi2_id) {
                                    $ucretlendirilen->fiyat2 -= $arizafiyat->toplamtutar2;
                                } else {
                                    if ($ucretlendirilen->parabirimi_id == 1) {
                                        $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih);
                                    } else {
                                        $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarih);
                                    }
                                }
                            }
                        }
                    } else {
                        if ($ucretlendirilen->parabirimi_id == 1) {
                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar * BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarih);
                        } else {
                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar * BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarih) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarih);
                        }
                        if ($arizafiyat->parabirimi2_id != null) {
                            if ($ucretlendirilen->parabirimi_id == $arizafiyat->parabirimi2_id) {
                                $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2;
                            } else {
                                if ($ucretlendirilen->parabirimi2_id == $arizafiyat->parabirimi2_id) {
                                    $ucretlendirilen->fiyat2 -= $arizafiyat->toplamtutar2;
                                } else {
                                    if ($ucretlendirilen->parabirimi_id == 1) {
                                        $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih);
                                    } else {
                                        $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarih);
                                    }
                                }
                            }
                        }
                    }
                    $ucretlendirilen->save();
                }
                $eskiucretlendirilen = Ucretlendirilen::where('netsiscari_id',$ucretlendirilen->netsiscari_id)->where('uretimyer_id',$ucretlendirilen->uretimyer_id)
                    ->where('servis_id',$ucretlendirilen->servis_id)->where('durum',3)->where('secilenler','LIKE','%'.$servistakip->arizafiyat_id.'%')->get();
                foreach ($eskiucretlendirilen as $ucretlendirme){
                    $eskisecilenler = explode(',', $ucretlendirme->secilenler);
                    if(BackendController::Listedemi($servistakip->arizafiyat_id,$eskisecilenler)){
                        if(is_null($ucretlendirme->reddedilenler)){
                            $ucretlendirme->reddedilenler =$servistakip->arizafiyat_id;
                            $ucretlendirme->save();
                        }else{
                            $ucretlendirme->reddedilenler .=($ucretlendirme->reddedilenler=="" ? ""  : ",").$servistakip->arizafiyat_id;
                            $ucretlendirme->save();
                        }
                        $servistakip->ucretlendirilen_id = $ucretlendirme->id;
                        $servistakip->save();
                        $sonucretlendirilen = $ucretlendirme;
                        break;
                    }
                }
            }
            $arizafiyat->tekrarkayittarihi = NULL;
            $arizafiyat->gerigonderimtarihi = NULL;
            $arizafiyat->durum = 2;
            $arizafiyat->save();
            $sayacgelen->fiyatlandirma = 0;
            $sayacgelen->save();
            $servistakip->durum = 6;
            $servistakip->tekrarucrettarihi = NULL;
            $servistakip->sonislemtarihi = $servistakip->reddetmetarihi;
            $servistakip->save();
            BackendController::HatirlatmaSil(7, $servistakip->netsiscari_id, $servistakip->servis_id, 1);
            BackendController::BildirimGeriAl(4, $servistakip->netsiscari_id, $servistakip->servis_id, 1);
            BackendController::HatirlatmaGeriAl(5, $servistakip->netsiscari_id, $servistakip->servis_id, 1);
            return array('durum' => false, 'mesaj' => '','degisenler'=>array('ucretlendirilen'=>$sonucretlendirilen));
        } catch (Exception $e) {
            Log::error($e);
            return array('durum' => true, 'mesaj' => 'İşlem Geri Alınırken Hata Oluştu.');
        }
    }

    public function OnayFormuGonderimiSil($servistakip,$arizafiyat,$ucretlendirilen){
        try {
            if ($ucretlendirilen) {
                $sonucretlendirilen = $ucretlendirilen;
                if ($arizafiyat) {
                    if ($ucretlendirilen->durum < 1) {
                        return array('durum' => true, 'mesaj' => 'Ücretlendirme Zaten Gönderilmeyi Bekliyor.');
                    }
                    $ucretlendirilenlist = explode(',', $ucretlendirilen->secilenler);
                    $ucretlendirilenler = "";
                    foreach ($ucretlendirilenlist as $arizafiyatid) {
                        if ($arizafiyatid == $servistakip->arizafiyat_id) {
                            continue;
                        } else {
                            $ucretlendirilenler .= ($ucretlendirilenler == "" ? "" : ",") . $arizafiyatid;
                        }
                    }
                    if ($ucretlendirilenler == "") {
                        $ucretlendirilen->durum = 0;
                        $ucretlendirilen->yetkili_id = null;
                        $ucretlendirilen->onaytipi = null;
                        $ucretlendirilen->mail = 0;
                        $ucretlendirilen->gonderimtarihi = null;
                        $ucretlendirilen->dosyalar = null;
                        $ucretlendirilen->save();
                    } else {
                        $ucretlendirilen->secilenler = $ucretlendirilenler;
                        $ucretlendirilen->sayacsayisi = $ucretlendirilen->sayacsayisi - 1;
                        if ($ucretlendirilen->parabirimi_id == $arizafiyat->parabirimi_id) {
                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar;
                            if ($arizafiyat->parabirimi2_id != null) {
                                if ($ucretlendirilen->parabirimi_id == $arizafiyat->parabirimi2_id) {
                                    $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2;
                                } else {
                                    if ($ucretlendirilen->parabirimi2_id == $arizafiyat->parabirimi2_id) {
                                        $ucretlendirilen->fiyat2 -= $arizafiyat->toplamtutar2;
                                    } else {
                                        if ($ucretlendirilen->parabirimi_id == 1) {
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih);
                                        } else {
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarih);
                                        }
                                    }
                                }
                            }
                        } else {
                            if ($ucretlendirilen->parabirimi_id == 1) {
                                $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar * BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarih);
                            } else {
                                $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar * BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarih) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarih);
                            }
                            if ($arizafiyat->parabirimi2_id != null) {
                                if ($ucretlendirilen->parabirimi_id == $arizafiyat->parabirimi2_id) {
                                    $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2;
                                } else {
                                    if ($ucretlendirilen->parabirimi2_id == $arizafiyat->parabirimi2_id) {
                                        $ucretlendirilen->fiyat2 -= $arizafiyat->toplamtutar2;
                                    } else {
                                        if ($ucretlendirilen->parabirimi_id == 1) {
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih);
                                        } else {
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarih);
                                        }
                                    }
                                }
                            }
                        }
                        $ucretlendirilen->save();
                        $kurtarih = $ucretlendirilen->kurtarihi;
                        $yeniparabirimi = $ucretlendirilen->parabirimi_id;
                        $yeniparabirimi2 = null;
                        $kur = 1;
                        if ($yeniparabirimi != $arizafiyat->parabirimi_id) {
                            if ($yeniparabirimi == 1) { //tl
                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                            } else { //euro dolar sterln
                                if ($arizafiyat->parabirimi_id == 1) {
                                    $kur = 1 / BackendController::getKurBilgisi($yeniparabirimi, $kurtarih);
                                } else {
                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($yeniparabirimi, $kurtarih);
                                }
                            }
                        }
                        $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                        if ($yeniparabirimi2 == null) {
                            if ($arizafiyat->parabirimi2_id != null) {
                                if ($arizafiyat->parabirimi2_id == $yeniparabirimi) {
                                    $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                    $arizafiyat->fiyat2 = 0;
                                    $arizafiyat->parabirimi2_id = null;
                                } else {
                                    $yeniparabirimi2 = $arizafiyat->parabirimi2_id;
                                }
                            }
                        } else {
                            if ($arizafiyat->parabirimi2_id != null) {
                                if ($arizafiyat->parabirimi2_id == $yeniparabirimi) {
                                    $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                    $arizafiyat->fiyat2 = 0;
                                    $arizafiyat->parabirimi2_id = null;
                                } else if ($arizafiyat->parabirimi2_id != $yeniparabirimi2) {
                                    return array('durum' => true, 'mesaj' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!');
                                }
                            }
                        }
                        $indirim = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat * $arizafiyat->indirimorani) / 100 : 0;
                        $indirim2 = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat2 * $arizafiyat->indirimorani) / 100 : 0;
                        $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                        $arizafiyat->tutar2 = $arizafiyat->fiyat2 - $indirim2;
                        $arizafiyat->kdv = ($arizafiyat->tutar * 18) / 100;
                        $arizafiyat->kdv2 = ($arizafiyat->tutar2 * 18) / 100;
                        $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                        $arizafiyat->toplamtutar2 = round(($arizafiyat->tutar2 + $arizafiyat->kdv2) * 2) / 2;
                        $arizafiyat->parabirimi_id = $yeniparabirimi;
                        $arizafiyat->save();
                        $eskiucretlendirilen = Ucretlendirilen::where('servis_id', $ucretlendirilen->servis_id)->where('uretimyer_id', $ucretlendirilen->uretimyer_id)
                            ->where('netsiscari_id', $ucretlendirilen->netsiscari_id)->where('durum', 0)->first();
                        if ($eskiucretlendirilen) {
                            $eskisecilenler = $eskiucretlendirilen->secilenler;
                            $eskisayi = $eskiucretlendirilen->sayacsayisi;
                            $eskifiyatlar = BackendController::getUcretlendirmeFiyat($eskisecilenler, $ucretlendirilen->kurtarihi, $ucretlendirilen->parabirimi_id, $ucretlendirilen->parabirimi2_id);
                            if ($eskifiyatlar['durum']) {
                                return array('durum' => true, 'mesaj' => $eskifiyatlar['error']);
                            }
                            $eskifiyat = $eskifiyatlar['fiyat'];
                            $eskifiyat2 = $eskifiyatlar['fiyat2'];
                            $birim2 = $eskifiyatlar['yenibirim'] == NULL ? $ucretlendirilen->parabirimi2_id : $eskifiyatlar['yenibirim'];
                            $eskiucretlendirilen->secilenler = $eskisecilenler . ',' . $servistakip->arizafiyat_id;
                            $eskiucretlendirilen->sayacsayisi = $eskisayi + 1;
                            $eskiucretlendirilen->fiyat = $eskifiyat + $arizafiyat->toplamtutar;
                            $eskiucretlendirilen->fiyat2 = $eskifiyat2 + $arizafiyat->toplamtutar2;
                            $eskiucretlendirilen->kurtarihi = $ucretlendirilen->kurtarihi;
                            $eskiucretlendirilen->parabirimi_id = $ucretlendirilen->parabirimi_id;
                            $eskiucretlendirilen->parabirimi2_id = $birim2;
                            $eskiucretlendirilen->save();
                            $sonucretlendirilen = $eskiucretlendirilen;
                        } else {
                            $yeniucretlendirilen = new Ucretlendirilen;
                            $yeniucretlendirilen->servis_id = $ucretlendirilen->servis_id;
                            $yeniucretlendirilen->uretimyer_id = $ucretlendirilen->uretimyer_id;
                            $yeniucretlendirilen->netsiscari_id = $ucretlendirilen->netsiscari_id;
                            $yeniucretlendirilen->secilenler = $servistakip->arizafiyat_id;
                            $yeniucretlendirilen->garanti = $arizafiyat->ariza_garanti;
                            $yeniucretlendirilen->sayacsayisi = 1;
                            $yeniucretlendirilen->fiyat = $arizafiyat->toplamtutar;
                            $yeniucretlendirilen->fiyat2 = $arizafiyat->toplamtutar2;
                            $yeniucretlendirilen->parabirimi_id = $yeniparabirimi;
                            $yeniucretlendirilen->parabirimi2_id = $yeniparabirimi2;
                            $yeniucretlendirilen->kullanici_id = $ucretlendirilen->kullanici_id;
                            $yeniucretlendirilen->kayittarihi = $ucretlendirilen->kayittarihi;
                            $yeniucretlendirilen->kurtarihi = $ucretlendirilen->kurtarihi;
                            $yeniucretlendirilen->durum = 0;
                            $yeniucretlendirilen->yetkili_id = NULL;
                            $yeniucretlendirilen->onaytipi = NULL;
                            $yeniucretlendirilen->mail = 0;
                            $yeniucretlendirilen->gonderimtarihi = NULL;
                            $yeniucretlendirilen->dosyalar = NULL;
                            $yeniucretlendirilen->durumtarihi = $ucretlendirilen->durumtarihi;
                            $yeniucretlendirilen->save();
                            $sonucretlendirilen = $yeniucretlendirilen;
                        }
                    }
                    $servistakip->ucretlendirilen_id = $sonucretlendirilen->id;
                    $servistakip->durum = 3;
                    $servistakip->gondermetarihi = NULL;
                    $servistakip->sonislemtarihi = $servistakip->ucretlendirmetarihi;
                    $servistakip->save();
                    BackendController::HatirlatmaSil(6, $servistakip->netsiscari_id, $servistakip->servis_id, 1);
                    BackendController::BildirimGeriAl(5, $servistakip->netsiscari_id, $servistakip->servis_id, 1);
                    BackendController::HatirlatmaGeriAl(5, $servistakip->netsiscari_id, $servistakip->servis_id, 1);
                    return array('durum' => false, 'mesaj' => '', 'degisenler' => array('ucretlendirilen' => $sonucretlendirilen));
                } else {
                    return array('durum' => true, 'mesaj' => 'Geri Alınacak Arıza Fiyatı Bulunamadı.');
                }
            }
            return array('durum' => false, 'mesaj' => '','degisenler'=>array('ucretlendirilen'=>$ucretlendirilen));
        } catch (Exception $e) {
            Log::error($e);
            return array('durum' => true, 'mesaj' => 'İşlem Geri Alınırken Hata Oluştu.');
        }
    }

    public function UcretlendirmeSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$ucretlendirilen){
        try {
            if ($arizafiyat) {
                if ($sayacgelen->depoteslim) {
                    return array('durum' => true, 'mesaj' => $sayacgelen->serino . ' Nolu Sayacın Depo Teslimatı Var!');
                }
                $arizafiyat->tekrarkayittarihi = NULL;
                $arizafiyat->gerigonderimtarihi = NULL;
                $arizafiyat->durum = 0;
                $arizafiyat->save();
                $sayacgelen->fiyatlandirma = 0;
                $sayacgelen->save();

                $servistakip->ucretlendirilen_id = NULL;
                $servistakip->durum = 2;
                $servistakip->ucretlendirmetarihi = NULL;
                $servistakip->sonislemtarihi = $servistakip->arizakayittarihi;
                $servistakip->kullanici_id = $arizakayit->arizakayit_kullanici_id;
                $servistakip->save();

                if($ucretlendirilen){
                    $ucretlendirilenlist = explode(',', $ucretlendirilen->secilenler);
                    $ucretlendirilenler = "";
                    foreach ($ucretlendirilenlist as $arizafiyatid) {
                        if ($arizafiyatid == $servistakip->arizafiyat_id) {
                            continue;
                        } else {
                            $ucretlendirilenler .= ($ucretlendirilenler == "" ? "" : ",") . $arizafiyatid;
                        }
                    }
                    if ($ucretlendirilenler == "") {
                        $ucretlendirilen->delete();
                    } else {
                        $ucretlendirilen->secilenler = $ucretlendirilenler;
                        $ucretlendirilen->sayacsayisi = $ucretlendirilen->sayacsayisi - 1;
                        if ($ucretlendirilen->parabirimi_id == $arizafiyat->parabirimi_id) {
                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar;
                            if ($arizafiyat->parabirimi2_id != null) {
                                if ($ucretlendirilen->parabirimi_id == $arizafiyat->parabirimi2_id) {
                                    $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2;
                                } else {
                                    if ($ucretlendirilen->parabirimi2_id == $arizafiyat->parabirimi2_id) {
                                        $ucretlendirilen->fiyat2 -= $arizafiyat->toplamtutar2;
                                    } else {
                                        if ($ucretlendirilen->parabirimi_id == 1) {
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih);
                                        } else {
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarih);
                                        }
                                    }
                                }
                            }
                        } else {
                            if ($ucretlendirilen->parabirimi_id == 1) {
                                $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar * BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarih);
                            } else {
                                $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar * BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarih) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarih);
                            }
                            if ($arizafiyat->parabirimi2_id != null) {
                                if ($ucretlendirilen->parabirimi_id == $arizafiyat->parabirimi2_id) {
                                    $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2;
                                } else {
                                    if ($ucretlendirilen->parabirimi2_id == $arizafiyat->parabirimi2_id) {
                                        $ucretlendirilen->fiyat2 -= $arizafiyat->toplamtutar2;
                                    } else {
                                        if ($ucretlendirilen->parabirimi_id == 1) {
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih);
                                        } else {
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2 * BackendController::getKurBilgisi($arizafiyat->parabirimi2_id, $arizafiyat->kurtarih) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarih);
                                        }
                                    }
                                }
                            }
                        }
                        $ucretlendirilen->save();
                    }
                }
                BackendController::HatirlatmaSil(5, $servistakip->netsiscari_id, $servistakip->servis_id, 1);
                BackendController::BildirimGeriAl(4, $servistakip->netsiscari_id, $servistakip->servis_id, 1);
                BackendController::HatirlatmaGeriAl(4, $servistakip->netsiscari_id, $servistakip->servis_id, 1);
                return array('durum' => false, 'mesaj' => '');
            } else {
                return array('durum' => true, 'mesaj' => 'Geri Alınacak Arıza Fiyatı Bulunamadı.');
            }
        } catch (Exception $e) {
            Log::error($e);
            return array('durum' => true, 'mesaj' => 'İşlem Geri Alınırken Hata Oluştu.');
        }
    }

    public static function ArizaKayitSil($servistakip,$sayacgelen,$arizakayit,$arizafiyat,$depoteslim){
        try{
            if(!$arizakayit)
                return array('durum' => true, 'mesaj' => 'Sayaç İçin Arıza Kayıdı Zaten Silinmiş Olabilir!');
            $netsiscari = NetsisCari::find($arizakayit->netsiscari_id);
            $arizafiyateski = ArizaFiyatEski::where('arizakayit_id', $arizakayit->id)->first();
            if ($sayacgelen->fiyatlandirma) {
                return array('durum' => true, 'mesaj' => 'Sayaç Ücretlendirilmiş!');
            }
            $depogelen = DepoGelen::find($sayacgelen->depogelen_id);
            $sayacariza = SayacAriza::find($arizakayit->sayacariza_id);
            $sayacyapilan = SayacYapilan::find($arizakayit->sayacyapilan_id);
            $sayacdegisen = SayacDegisen::find($arizakayit->sayacdegisen_id);
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

            BackendController::HatirlatmaSil(4, $netsiscari->id, $sayacgelen->servis_id, 1);
            BackendController::BildirimGeriAl(3,$netsiscari->id,$sayacgelen->servis_id,1,$depogelen->id,$depogelen->servisstokkodu);
            BackendController::HatirlatmaGeriAl(3,$netsiscari->id,$sayacgelen->servis_id,1,$depogelen->id,$depogelen->servisstokkodu);
            return array('durum' => false, 'mesaj' => '');
        }catch(Exception $e){
            DB::rollBack();
            Log::error($e);
            return array('durum' => true, 'mesaj' => 'İşlem Geri Alınırken Hata Oluştu.');
        }
    }
}
