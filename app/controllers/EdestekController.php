<?php /** @noinspection PhpUnusedParameterInspection */

//transaction işlemi tamamlandı
class EdestekController extends BackendController {

    public function getKullanici($id){
        $kullanici = Kullanici::find($id);
        return Response::json(array('kullanici' => $kullanici ));
    }
    
    public function getPersonel() {
        $personel = EdestekPersonel::all();
        return View::make('edestek.personel',array('personel'=>$personel))->with('title', 'Yazılım Destek Personel Bilgi Ekranı');
    }

    public function postPersonel()
    {
        try {
            $kriterler = array();
            if (Input::get('tarihcheck')) {
                $tarih = Input::get('tarih');
                $tarih = explode(' - ', $tarih);
                $date = explode('.', $tarih[0]);
                $ilktarih = date('Y-m-d', mktime(0, 0, 0, $date[1], $date[0], $date[2]));
                $date = explode('.', $tarih[1]);
                $sontarih = date('Y-m-d', mktime(0, 0, 0, $date[1], $date[0], $date[2]));
                $kriterler['ilktarih'] = $ilktarih;
                $kriterler['sontarih'] = $sontarih;
            }
            $kriterler['tarihcheck'] = Input::get('tarihcheck');
            if (Input::get('personelcheck')) {
                $personel = Input::get('personel');
                $kriterler['personel'] = $personel;
            }
            $kriterler['personelcheck'] = Input::get('personelcheck');

            $raporadi = "performansdestek";
            $export = "xls";
            JasperPHP::process(public_path('reports/performansdestek/performansdestek.jasper'), public_path('reports/outputs/performansdestek/' . $raporadi),
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
            readfile("reports/outputs/performansdestek/" . $raporadi . "." . $export . "");
            File::delete("reports/outputs/performansdestek/" . $raporadi . "." . $export . "");
            return Redirect::back()->with(array('mesaj' => 'false'));

        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error', 'title' => 'Rapor Çıkarma Hatası'));
        }
    }

    public function getPersonelekle() {
        $ilgiler = EdestekIlgi::all();
        $kullanicilar = Kullanici::all();
        return View::make('edestek.personelekle',array('ilgiler'=>$ilgiler,'kullanicilar'=>$kullanicilar))->with('title', 'Yazılım Destek Personel Ekleme Ekranı');
    }

    public function postPersonelekle() {
        try {
            $rules = ['adisoyadi' => 'required', 'options1' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $personel = new EdestekPersonel;
            $adisoyadi = Input::get('adisoyadi');
            if (Input::has('giristarihi')) {
                $giris = Input::get('giristarihi');
                $giristarihi = date("Y-m-d", strtotime($giris));
                $personel->giristarihi = $giristarihi;
            }
            $kullanici = Input::get('options1');
            If (EdestekPersonel::where('kullanici_id', $kullanici)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu kullanıcı adı başka personelde kayıtlı.', 'type' => 'error'));
            }
            $mail = Input::get('email');
            if (Input::has('meslek')) {
                $meslek = Input::get('meslek');
                $personel->meslek = $meslek;
            }
            if (Input::has('options2')) {
                $ilgilersecilen = Input::get('options2');
                $ilgiler = "";
                foreach ($ilgilersecilen as $ilgi) {
                    $ilgiler .= ($ilgiler == "" ? "" : ",") . $ilgi;
                }
                $personel->ilgilendikleri = $ilgiler;
            }
            If (Input::has('durum')) {
                $durum = 1;
            } else {
                $durum = 0;
            }
            $personel->adisoyadi = $adisoyadi;
            $personel->kullanici_id = $kullanici;
            $personel->mail = $mail;
            $personel->durum = $durum;
            $personel->save();
            DB::commit();
            return Redirect::to('edestek/personel')->with(array('mesaj' => 'true', 'title' => 'Personel Eklendi', 'text' => 'Personel Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Personel Eklenemedi', 'text' => 'Personel Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }
    
    public function getPersonelduzenle($id) {
        $personel = EdestekPersonel::find($id);
        $ilgiler = EdestekIlgi::all();
        $kullanicilar = Kullanici::all();
        return View::make('edestek.personelduzenle',array('personel'=>$personel,'ilgiler'=>$ilgiler,'kullanicilar'=>$kullanicilar))->with('title', 'Yazılım Destek Personel Düzenleme Ekranı');
    }
    
    public function postPersonelduzenle($id){
        try {
            $rules = ['adisoyadi' => 'required', 'options1' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $personel = EdestekPersonel::find($id);
            $adisoyadi = Input::get('adisoyadi');
            if (Input::has('giristarihi')) {
                $giris = Input::get('giristarihi');
                $giristarihi = date("Y-m-d", strtotime($giris));
                $personel->giristarihi = $giristarihi;
            }
            $kullanici = Input::get('options1');
            If (EdestekPersonel::where('kullanici_id', $kullanici)->where('id', '<>', $id)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu kullanıcı adı başka personelde kayıtlı.', 'type' => 'error'));
            }
            $mail = Input::get('email');
            if (Input::has('meslek')) {
                $meslek = Input::get('meslek');
                $personel->meslek = $meslek;
            }
            if (Input::has('options2')) {
                $ilgilersecilen = Input::get('options2');
                $ilgiler = "";
                foreach ($ilgilersecilen as $ilgi) {
                    $ilgiler .= ($ilgiler == "" ? "" : ",") . $ilgi;
                }
                $personel->ilgilendikleri = $ilgiler;
            }
            If (Input::has('durum')) {
                $durum = 1;
            } else {
                $durum = 0;
            }
            $personel->adisoyadi = $adisoyadi;
            $personel->kullanici_id = $kullanici;
            $personel->mail = $mail;
            $personel->durum = $durum;
            $personel->save();
            DB::commit();
            return Redirect::to('edestek/personel')->with(array('mesaj' => 'true', 'title' => 'Personel Güncellendi', 'text' => 'Personel Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Personel Güncellenemedi', 'text' => 'Personel Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }
    
    public function getPersoneldurum($id,$durum){
        $personel = EdestekPersonel::find($id);
        $personel->durum = $durum;
        if($personel->save()){
            return Response::json(array('mesaj' => 'true', 'title' => 'Personel Durumu Güncellendi', 'text' => 'Personel Durumu  Başarıyla Güncellendi.', 'type' => 'success'));
        }else{
            return Response::json(array('mesaj' => 'true', 'title' => 'Personel Durumu  Güncellenemedi', 'text' => 'Personel Durumu  Güncellenirken Sorun Oluştu.', 'type' => 'error'));
        }
    }
    
    public function getPersonelsil($id){
        try {
            DB::beginTransaction();
            $personel = EdestekPersonel::find($id);
            $personel->delete();
            DB::commit();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Personel Silindi', 'text' => 'Personel Başarıyla Silindi.', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Personel Silinemedi', 'text' => 'Personel Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }
    
    public function getHatacozumleri() {
        $cozumler = EdestekHataCozum::all();
        return View::make('edestek.hatacozumleri',array('cozumler'=>$cozumler))->with('title', 'Yazılım Destek Hata Çözümleri Ekranı');
    }
    
    public function getCozumekle() {
        $konular = EdestekKonu::all();
        $detaylar = EdestekKonuDetay::all();
        return View::make('edestek.cozumekle',array('konular'=>$konular,'detaylar'=>$detaylar))->with('title', 'Yazılım Destek Hata Çözümü Ekleme Ekranı');
    }
    
    public function postCozumekle() {
        try {
            $rules = ['problem' => 'required', 'options1' => 'required', 'options2' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $hatacozum = new EdestekHataCozum;
            $konu = Input::get('options1');
            $detay = Input::get('options2');
            $problem = Input::get('problem');
            $cozum = Input::get('cozumyeniid');
            $kullanici = Auth::user()->id;
            If (EdestekPersonel::where('kullanici_id', $kullanici)->first()) {
                $personel = EdestekPersonel::where('kullanici_id', $kullanici)->first()->id;
                $hatacozum->edestekpersonel_id = $personel;
            } else {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu kullanıcının personel bilgisi yok', 'type' => 'error'));
            }
            $hatacozum->edestekkonu_id = $konu;
            $hatacozum->edestekkonudetay_id = $detay;
            $hatacozum->problem = $problem;
            $hatacozum->cozum = $cozum;
            $hatacozum->save();
            DB::commit();
            return Redirect::to('edestek/hatacozumleri')->with(array('mesaj' => 'true', 'title' => 'Hata Çözümü Kaydedildi', 'text' => 'Hata Çözümü Başarıyla Kaydedildi', 'type' => 'success'));

        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Hata Çözümü Kaydedilemedi', 'text' => 'Hata Çözümü Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function postHatacozumekle() {
        try {
            $rules = ['problem' => 'required', 'options1' => 'required', 'options2' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $hatacozum = new EdestekHataCozum;
            $konu = Input::get('options1');
            $detay = Input::get('options2');
            $problem = Input::get('problem');
            $cozum = Input::get('cozumyeniid');
            $kullanici = Auth::user()->id;
            If (EdestekPersonel::where('kullanici_id', $kullanici)->first()) {
                $personel = EdestekPersonel::where('kullanici_id', $kullanici)->first()->id;
                $hatacozum->edestekpersonel_id = $personel;
            } else {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu kullanıcının personel bilgisi yok', 'type' => 'error'));

            }
            $hatacozum->edestekkonu_id = $konu;
            $hatacozum->edestekkonudetay_id = $detay;
            $hatacozum->problem = $problem;
            $hatacozum->cozum = $cozum;
            $hatacozum->save();
            DB::commit();
            return Redirect::to('edestek/edestekkayit')->with(array('mesaj' => 'true', 'title' => 'Hata Çözümü Kaydedildi', 'text' => 'Hata Çözümü Başarıyla Kaydedildi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Hata Çözümü Kaydedilemedi', 'text' => 'Hata Çözümü Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getCozumduzenle($id) {
        $hatacozum = EdestekHataCozum::find($id);
        $konular = EdestekKonu::all();
        $detaylar = EdestekKonuDetay::all();
        return View::make('edestek.cozumduzenle',array('hatacozum'=>$hatacozum,'konular'=>$konular,'detaylar'=>$detaylar))->with('title', 'Yazılım Destek Hata Çözümü Düzenleme Ekranı');
    }
    
    public function postCozumduzenle($id){
        try {
            $rules = ['problem' => 'required', 'options1' => 'required', 'options2' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $hatacozum = EdestekHataCozum::find($id);
            $konu = Input::get('options1');
            $detay = Input::get('options2');
            $problem = Input::get('problem');
            $cozum = Input::get('cozumid');
            $kullanici = Auth::user()->id;
            If (EdestekPersonel::where('kullanici_id', $kullanici)->first()) {
                $personel = EdestekPersonel::where('kullanici_id', $kullanici)->first()->id;
                $hatacozum->guncelleyen_id = $personel;
            } else {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu kullanıcının personel bilgisi yok', 'type' => 'error'));
            }
            If (EdestekHataCozum::where('edestekkonudetay_id', $detay)->where('problem', $problem)->where('id', '<>', $id)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu problem için çözüm mevcut', 'type' => 'error'));
            }
            $hatacozum->edestekkonu_id = $konu;
            $hatacozum->edestekkonudetay_id = $detay;
            $hatacozum->problem = $problem;
            $hatacozum->cozum = $cozum;
            $hatacozum->save();
                return Redirect::to('edestek/hatacozumleri')->with(array('mesaj' => 'true', 'title' => 'Hata Çözümü Güncellendi', 'text' => 'Hata Çözümü Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Hata Çözümü Güncellenemedi', 'text' => 'Hata Çözümü Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }
    
    public function getCozumsil($id){
        try {
            DB::beginTransaction();
            $hatacozum = EdestekHataCozum::find($id);
            $hatacozum->delete();
            DB::commit();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Hata Çözümü Silindi', 'text' => 'Hata Çözümü Başarıyla Silindi.', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Hata Çözümü Silinemedi', 'text' => 'Hata Çözümü Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }
    
    public function getKonu($id){
        $konu = EdestekKonu::find($id);
        return Response::json(array('konu' => $konu));
    }
    
    public function getKonudetay($id){
        $detay = EdestekKonuDetay::find($id);
        $detay->konu = EdestekKonu::find($detay->edestekkonu_id);
        return Response::json(array('detay' => $detay));
    }
    
    public function getDetaylar($id){
        $detay = EdestekKonuDetay::where('edestekkonu_id',$id)->get();
        return Response::json(array('detay' => $detay));
    }

    public function getKonuekle() {
        try {
            $konuadi = Input::get('konu');
            If (EdestekKonu::where('adi', $konuadi)->first()) {
                return Response::json(array('durum' => 0, 'title' => 'Doğrulama Hatası', 'text' => 'Bu Konu adı mevcut.', 'type' => 'warning'));
            }
            DB::beginTransaction();
            $konu = new EdestekKonu;
            $konu->adi = $konuadi;
            $konu->save();
            DB::commit();
            $konular = EdestekKonu::orderBy('adi', 'ASC')->get();
            return Response::json(array('durum' => 1, 'konular' => $konular, 'title' => 'Konu Eklendi', 'text' => 'Konu Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Response::json(array('durum' => 3, 'title' => 'Konu Eklenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }
    
    public function getKonuduzenle() {
        try {
            $id = Input::get('konuid');
            $konuadi = Input::get('konu');
            If (EdestekKonu::where('adi', $konuadi)->where('id', '<>', $id)->first()) {
                return Response::json(array('durum' => 0, 'title' => 'Doğrulama Hatası', 'text' => 'Bu Konu adı mevcut.', 'type' => 'warning'));
            }
            DB::beginTransaction();
            $konu = EdestekKonu::find($id);
            $konu->adi = $konuadi;
            $konu->save();
            DB::commit();
            $konular = EdestekKonu::orderBy('adi', 'ASC')->get();
            return Response::json(array('durum' => 1, 'konular' => $konular, 'title' => 'Konu Güncellendi', 'text' => 'Konu Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Response::json(array('durum' => 3, 'title' => 'Konu Güncellenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }
    
    public function getDetayekle() {
        try {
            $konu = Input::get('konu');
            $detayadi = Input::get('detay');
            If (EdestekKonuDetay::where('detay', $detayadi)->where('edestekkonu_id', $konu)->first()) {
                return Response::json(array('durum' => 0, 'title' => 'Doğrulama Hatası', 'text' => 'Bu konu detayı mevcut.', 'type' => 'warning'));
            }
            DB::beginTransaction();
            $detay = new EdestekKonuDetay;
            $detay->edestekkonu_id = $konu;
            $detay->detay = $detayadi;
            $detay->save();
            DB::commit();
            $detaylar = EdestekKonuDetay::where('edestekkonu_id', $konu)->get();
            return Response::json(array('durum' => 1, 'detaylar' => $detaylar, 'title' => 'Konu Detayı Eklendi', 'text' => 'Konu Detayı Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Response::json(array('durum' => 3, 'title' => 'Konu Detayı Eklenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }
    
    public function getDetayduzenle() {
        try {
            $id = Input::get('detayid');
            $konu = Input::get('konu');
            $detayadi = Input::get('detay');
            If (EdestekKonuDetay::where('detay', $detayadi)->where('edestekkonu_id', $konu)->where('id', '<>', $id)->first()) {
                return Response::json(array('durum' => 0, 'title' => 'Doğrulama Hatası', 'text' => 'Bu konu detayı mevcut.', 'type' => 'warning'));
            }
            DB::beginTransaction();
            $detay = EdestekKonuDetay::find($id);
            $detay->edestekkonu_id = $konu;
            $detay->detay = $detayadi;
            $detay->save();
            DB::commit();
            $detaylar = EdestekKonuDetay::where('edestekkonu_id', $konu)->get();
            return Response::json(array('durum' => 1, 'detaylar' => $detaylar, 'title' => 'Konu Detayı Güncellendi', 'text' => 'Konu Detayı Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Response::json(array('durum' => 3, 'title' => 'Konu Detayı Güncellenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }
    
    public function getProjebilgisi() {
        $musteriler = EdestekMusteri::all();
        return View::make('edestek.projebilgisi',array('musteriler'=>$musteriler))->with('title', 'Yazılım Destek Proje Bilgisi Ekranı');
    }
    
    public function getMusteriekle() {
        $iller = Iller::all();
        $plasiyerler = Plasiyer::all();
        $netsiscariler = NetsisCari::all();
        $urunler = EdestekUrun::all();
        $programlar = EdestekProgram::all();
        $firmalar = EdestekEntegrasyonFirma::all();
        $entegrasyonprogramlar = EdestekEntegrasyonProgram::all();
        $tipler = EdestekEntegrasyonTip::all();
        $versiyonlar = EdestekEntegrasyonVersiyon::all();
        $baskiturler = EdestekBaskiTur::all();
        return View::make('edestek.musteriekle',array('iller'=>$iller,'plasiyerler'=>$plasiyerler,'netsiscariler'=>$netsiscariler,'urunler'=>$urunler,'programlar'=>$programlar,'firmalar'=>$firmalar,'entegrasyonprogramlar'=>$entegrasyonprogramlar,'tipler'=>$tipler,'versiyonlar'=>$versiyonlar,'baskiturler'=>$baskiturler))->with('title', 'Yazılım Destek Proje Bilgisi Ekleme Ekranı');
    }
    
    public function postMusteriekle() {
        try {
            $adi = Input::get('adi');
            DB::beginTransaction();
            If (EdestekMusteri::onlyTrashed()->where('musteriadi', $adi)->first()) {
                try {
                    $edestekmusteri = EdestekMusteri::onlyTrashed()->where('musteriadi', $adi)->first();
                    $edestekmusteri->deleted_at = NULL;
                    $edestekmusteri->save();
                    return Redirect::to('edestek/projebilgisi')->with(array('mesaj' => 'true', 'title' => 'Müşteri Geri Getirildi', 'text' => 'Müşterinin Eski Bilgileri Başarıyla Geri Getirildi.', 'type' => 'success'));
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::to('edestek/projebilgisi')->with(array('mesaj' => 'true', 'title' => 'Müşteri Eklenemedi', 'text' => 'Müşteri Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                }
            }
            $rules = ['adi' => 'required|unique:edestekmusteri,musteriadi', 'resim' => 'image|mimes:jpeg,jpg,png', 'mail' => 'email',
                'suontaraf' => 'image|mimes:jpeg,jpg,png', 'suarkataraf' => 'image|mimes:jpeg,jpg,png', 'klmontaraf' => 'image|mimes:jpeg,jpg,png',
                'klmarkataraf' => 'image|mimes:jpeg,jpg,png', 'trifazeontaraf' => 'image|mimes:jpeg,jpg,png', 'trifazearkataraf' => 'image|mimes:jpeg,jpg,png',
                'monoontaraf' => 'image|mimes:jpeg,jpg,png', 'monoarkataraf' => 'image|mimes:jpeg,jpg,png', 'klimaontaraf' => 'image|mimes:jpeg,jpg,png',
                'klimaarkataraf' => 'image|mimes:jpeg,jpg,png', 'gazontaraf' => 'image|mimes:jpeg,jpg,png', 'gazarkataraf' => 'image|mimes:jpeg,jpg,png',
                'mifareontaraf' => 'image|mimes:jpeg,jpg,png', 'mifarearkataraf' => 'image|mimes:jpeg,jpg,png'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            If (EdestekMusteri::where('musteriadi', $adi)->first()) {
                DB::rollBack();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu Müşteri adı mevcut.', 'type' => 'warning'));
            }

            $edestekmusteri = new EdestekMusteri;
            //ekleme yapılacak
            if (Input::has('adresi') || Input::has('options1') || Input::has('telefon') || Input::has('mail') || Input::has('yetkili') || Input::has('yetkilitel') ||
                Input::has('teamid') || Input::has('teampass') || Input::has('ammyyid') || Input::has('alpemixid') || Input::has('alpemixpass') || Input::has('uzakip') ||
                Input::has('uzakkullanici') || Input::has('uzakpass'))
            { //müşteri bilgisi var
                try {
                    $musteribilgi = new EdestekMusteriBilgi;
                    if (Input::has('adresi')) {
                        $musteribilgi->adresi = Input::get('adresi');
                    }
                    if (Input::has('options1')) {
                        $musteribilgi->iller_id = Input::get('options1');
                    }
                    if (Input::has('telefon')) {
                        $musteribilgi->telefon = Input::get('telefon');
                    }
                    if (Input::has('mail')) {
                        $musteribilgi->mail = Input::get('mail');
                    }
                    if (Input::has('yetkili')) {
                        $musteribilgi->yetkiliadi = Input::get('yetkili');
                    }
                    if (Input::has('yetkilitel')) {
                        $musteribilgi->yetkilitel = Input::get('yetkilitel');
                    }
                    if (Input::has('teamid')) {
                        $musteribilgi->teamid = Input::get('teamid');
                    }
                    if (Input::has('teampass')) {
                        $musteribilgi->teampass = Input::get('teampass');
                    }
                    if (Input::has('ammyyid')) {
                        $musteribilgi->ammyyid = Input::get('ammyyid');
                    }
                    if (Input::has('alpemixid')) {
                        $musteribilgi->alpemixid = Input::get('alpemixid');
                    }
                    if (Input::has('alpemixpass')) {
                        $musteribilgi->alpemixpass = Input::get('alpemixpass');
                    }
                    if (Input::has('uzakip')) {
                        $musteribilgi->uzakip = Input::get('uzakip');
                    }
                    if (Input::has('uzakkullanici')) {
                        $musteribilgi->uzakkullanici = Input::get('uzakkullanici');
                    }
                    if (Input::has('uzakpass')) {
                        $musteribilgi->uzakpass = Input::get('uzakpass');
                    }
                    $musteribilgi->save();
                    $edestekmusteri->edestekmusteribilgi_id = $musteribilgi->id;
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Müşteri Bİlgisi Kaydedilemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning'));
                }
            }
            if (Input::has('cariadi') || Input::has('options2') || Input::has('options3') || Input::has('options4'))
            { //sistem bilgisi var
                try {
                    $sistembilgi = new EdestekSistemBilgi;
                    $programlar = "";
                    $veritabanlari = "";
                    $urunler = "";
                    if (Input::has('options3')) {
                        $urunturleri = Input::get('options3');
                        foreach ($urunturleri as $uruntur) {
                            $urun = new EdestekSistemUrun;
                            switch ($uruntur) {
                                case "1":
                                    $urun->edestekurun_id = 1;
                                    if (Input::has('suadi')) {
                                        $urun->adi = Input::get('suadi');
                                    }
                                    if (Input::has('suadet')) {
                                        $urun->adet = Input::get('suadet');
                                    }
                                    if (Input::has('suissue')) {
                                        $urun->issue = Input::get('suissue');
                                    }
                                    if (Input::has('sudetay')) {
                                        $urun->detay = Input::get('sudetay');
                                    }
                                    $urun->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                    break;
                                case "2":
                                    $urun->edestekurun_id = 2;
                                    if (Input::has('sicakadi')) {
                                        $urun->adi = Input::get('sicakadi');
                                    }
                                    if (Input::has('sicakadet')) {
                                        $urun->adet = Input::get('sicakadet');
                                    }
                                    if (Input::has('sicakissue')) {
                                        $urun->issue = Input::get('sicakissue');
                                    }
                                    if (Input::has('sicakdetay')) {
                                        $urun->detay = Input::get('sicakdetay');
                                    }
                                    $urun->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                    break;
                                case "3":
                                    $urun->edestekurun_id = 3;
                                    if (Input::has('elkadi')) {
                                        $urun->adi = Input::get('elkadi');
                                    }
                                    if (Input::has('elkadet')) {
                                        $urun->adet = Input::get('elkadet');
                                    }
                                    if (Input::has('elkissue')) {
                                        $urun->issue = Input::get('elkissue');
                                    }
                                    if (Input::has('elkdetay')) {
                                        $urun->detay = Input::get('elkdetay');
                                    }
                                    $urun->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                    break;
                                case "4":
                                    $urun->edestekurun_id = 4;
                                    if (Input::has('gazadi')) {
                                        $urun->adi = Input::get('gazadi');
                                    }
                                    if (Input::has('gazadet')) {
                                        $urun->adet = Input::get('gazadet');
                                    }
                                    if (Input::has('gazissue')) {
                                        $urun->issue = Input::get('gazissue');
                                    }
                                    if (Input::has('gazdetay')) {
                                        $urun->detay = Input::get('gazdetay');
                                    }
                                    $urun->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                    break;
                                case "5":
                                    $urun->edestekurun_id = 5;
                                    if (Input::has('isiadi')) {
                                        $urun->adi = Input::get('isiadi');
                                    }
                                    if (Input::has('isiadet')) {
                                        $urun->adet = Input::get('isiadet');
                                    }
                                    if (Input::has('isiissue')) {
                                        $urun->issue = Input::get('isiissue');
                                    }
                                    if (Input::has('isidetay')) {
                                        $urun->detay = Input::get('isidetay');
                                    }
                                    $urun->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                    break;
                                case "6":
                                    $urun->edestekurun_id = 6;
                                    if (Input::has('payolceradi')) {
                                        $urun->adi = Input::get('payolceradi');
                                    }
                                    if (Input::has('payolceradet')) {
                                        $urun->adet = Input::get('payolceradet');
                                    }
                                    if (Input::has('payolcerissue')) {
                                        $urun->issue = Input::get('payolcerissue');
                                    }
                                    if (Input::has('payolcerdetay')) {
                                        $urun->detay = Input::get('payolcerdetay');
                                    }
                                    $urun->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                    break;
                                case "7":
                                    $urun->edestekurun_id = 7;
                                    if (Input::has('terminaladi')) {
                                        $urun->adi = Input::get('terminaladi');
                                    }
                                    if (Input::has('terminaladet')) {
                                        $urun->adet = Input::get('terminaladet');
                                    }
                                    if (Input::has('terminalissue')) {
                                        $urun->issue = Input::get('terminalissue');
                                    }
                                    if (Input::has('terminaldetay')) {
                                        $urun->detay = Input::get('terminaldetay');
                                    }
                                    $urun->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                    break;
                                case "8":
                                    $urun->edestekurun_id = 8;
                                    if (Input::has('kioskadi')) {
                                        $urun->adi = Input::get('kioskadi');
                                    }
                                    if (Input::has('kioskadet')) {
                                        $urun->adet = Input::get('kioskadet');
                                    }
                                    if (Input::has('kioskissue')) {
                                        $urun->issue = Input::get('kioskissue');
                                    }
                                    if (Input::has('kioskdetay')) {
                                        $urun->detay = Input::get('kioskdetay');
                                    }
                                    $urun->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                    break;
                                case "9":
                                    $urun->edestekurun_id = 9;
                                    if (Input::has('klimaadi')) {
                                        $urun->adi = Input::get('klimaadi');
                                    }
                                    if (Input::has('klimaadet')) {
                                        $urun->adet = Input::get('klimaadet');
                                    }
                                    if (Input::has('klimaissue')) {
                                        $urun->issue = Input::get('klimaissue');
                                    }
                                    if (Input::has('klimadetay')) {
                                        $urun->detay = Input::get('klimadetay');
                                    }
                                    $urun->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                    break;
                                case "10":
                                    $urun->edestekurun_id = 10;
                                    if (Input::has('digeradi')) {
                                        $urun->adi = Input::get('digeradi');
                                    }
                                    if (Input::has('digeradet')) {
                                        $urun->adet = Input::get('digeradet');
                                    }
                                    if (Input::has('digerissue')) {
                                        $urun->issue = Input::get('digerissue');
                                    }
                                    if (Input::has('digerdetay')) {
                                        $urun->detay = Input::get('digerdetay');
                                    }
                                    $urun->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                    break;
                                case "11":
                                    $urun->edestekurun_id = 11;
                                    if (Input::has('icuniteadi')) {
                                        $urun->adi = Input::get('icuniteadi');
                                    }
                                    if (Input::has('icuniteadet')) {
                                        $urun->adet = Input::get('icuniteadet');
                                    }
                                    if (Input::has('icuniteissue')) {
                                        $urun->issue = Input::get('icuniteissue');
                                    }
                                    if (Input::has('icunitedetay')) {
                                        $urun->detay = Input::get('icunitedetay');
                                    }
                                    $urun->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                    break;
                                case "12":
                                    $urun->edestekurun_id = 12;
                                    if (Input::has('kartokuyucuadi')) {
                                        $urun->adi = Input::get('kartokuyucuadi');
                                    }
                                    if (Input::has('kartokuyucuadet')) {
                                        $urun->adet = Input::get('kartokuyucuadet');
                                    }
                                    if (Input::has('kartokuyucucesit')) {
                                        $urun->issue = Input::get('kartokuyucucesit');
                                    }
                                    if (Input::has('kartokuyucudetay')) {
                                        $urun->detay = Input::get('kartokuyucudetay');
                                    }
                                    $urun->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                    break;
                            }
                        }
                    }
                    $sistembilgi->urunler = $urunler;

                    if (Input::has('options4')) {
                        $programturleri = Input::get('options4');
                        foreach ($programturleri as $programtur) {
                            $program = new EdestekSistemProgram;
                            switch ($programtur) {
                                case "1":
                                    $program->edestekprogram_id = 1;
                                    if (Input::has('epicversiyon')) {
                                        $program->versiyon = Input::get('epicversiyon');
                                    }
                                    if (Input::has('epickullanici')) {
                                        $program->kullaniciadi = Input::get('epickullanici');
                                    }
                                    if (Input::has('epicsifre')) {
                                        $program->sifre = Input::get('epicsifre');
                                    }
                                    if (Input::has('epicmanas')) {
                                        $program->yetkilisifre = Input::get('epicmanas');
                                    }
                                    if (Input::has('epicdiger')) {
                                        $program->diger = Input::get('epicdiger');
                                    }
                                    $program->save();
                                    $programlar .= ($programlar == "" ? "" : ",") . $program->id;

                                    $database = new EdestekSistemDatabase;
                                    $database->edestekdatabase_id = 1;
                                    if (Input::has('oracleversiyon')) {
                                        $database->versiyon = Input::get('oracleversiyon');
                                    }
                                    if (Input::has('oraclekullanici')) {
                                        $database->kullaniciadi = Input::get('oraclekullanici');
                                    }
                                    if (Input::has('oraclesifre')) {
                                        $database->sifre = Input::get('oraclesifre');
                                    }
                                    if (Input::has('oracleveritabani')) {
                                        $database->adi = Input::get('oracleveritabani');
                                    }
                                    if (Input::has('oraclediger')) {
                                        $database->diger = Input::get('oraclediger');
                                    }
                                    $database->save();
                                    $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $database->id;
                                    break;
                                case "2":
                                    $program->edestekprogram_id = 2;
                                    if (Input::has('smartversiyon')) {
                                        $program->versiyon = Input::get('smartversiyon');
                                    }
                                    if (Input::has('smartkullanici')) {
                                        $program->kullaniciadi = Input::get('smartkullanici');
                                    }
                                    if (Input::has('smartsifre')) {
                                        $program->sifre = Input::get('smartsifre');
                                    }
                                    if (Input::has('smartmanas')) {
                                        $program->yetkilisifre = Input::get('smartmanas');
                                    }
                                    if (Input::has('smartdiger')) {
                                        $program->diger = Input::get('smartdiger');
                                    }
                                    $program->save();
                                    $programlar .= ($programlar == "" ? "" : ",") . $program->id;

                                    $database = new EdestekSistemDatabase;
                                    $database->edestekdatabase_id = 2;
                                    if (Input::has('sqlversiyon')) {
                                        $database->versiyon = Input::get('sqlversiyon');
                                    }
                                    if (Input::has('sqlkullanici')) {
                                        $database->kullaniciadi = Input::get('sqlkullanici');
                                    }
                                    if (Input::has('sqlsifre')) {
                                        $database->sifre = Input::get('sqlsifre');
                                    }
                                    if (Input::has('sqlveritabani')) {
                                        $database->adi = Input::get('sqlveritabani');
                                    }
                                    if (Input::has('sqldiger')) {
                                        $database->diger = Input::get('sqldiger');
                                    }
                                    $database->save();
                                    $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $database->id;
                                    break;
                                case "3":
                                    $program->edestekprogram_id = 3;
                                    if (Input::has('4comversiyon')) {
                                        $program->versiyon = Input::get('4comversiyon');
                                    }
                                    if (Input::has('4comkullanici')) {
                                        $program->kullaniciadi = Input::get('4comkullanici');
                                    }
                                    if (Input::has('4comsifre')) {
                                        $program->sifre = Input::get('4comsifre');
                                    }
                                    if (Input::has('4compower')) {
                                        $program->yetkilisifre = Input::get('4compower');
                                    }
                                    if (Input::has('4comdiger')) {
                                        $program->diger = Input::get('4comdiger');
                                    }
                                    $program->save();
                                    $programlar .= ($programlar == "" ? "" : ",") . $program->id;

                                    $database = new EdestekSistemDatabase;
                                    $database->edestekdatabase_id = 3;
                                    if (Input::has('mysqlversiyon')) {
                                        $database->versiyon = Input::get('mysqlversiyon');
                                    }
                                    if (Input::has('mysqlkullanici')) {
                                        $database->kullaniciadi = Input::get('mysqlkullanici');
                                    }
                                    if (Input::has('mysqlsifre')) {
                                        $database->sifre = Input::get('mysqlsifre');
                                    }
                                    if (Input::has('mysqlveritabani')) {
                                        $database->adi = Input::get('mysqlveritabani');
                                    }
                                    if (Input::has('mysqldiger')) {
                                        $database->diger = Input::get('mysqldiger');
                                    }
                                    $database->save();
                                    $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $database->id;
                                    break;
                                case "4":
                                    $program->edestekprogram_id = 4;
                                    if (Input::has('options5')) {
                                        $program->edestekentegrasyonfirma_id = Input::get('options5');
                                    }
                                    if (Input::has('options6')) {
                                        $program->edestekentegrasyontip_id = Input::get('options6');
                                    }
                                    if (Input::has('options7')) {
                                        $program->edestekentegrasyonprogram_id = Input::get('options7');
                                    }
                                    if (Input::has('options8')) {
                                        $program->edestekentegrasyonversiyon_id = Input::get('options8');
                                    }
                                    if (Input::has('entegrasyondiger')) {
                                        $program->diger = Input::get('entegrasyondiger');
                                    }
                                    $program->save();
                                    $programlar .= ($programlar == "" ? "" : ",") . $program->id;
                                    break;
                                case "5":
                                    $program->edestekprogram_id = 5;
                                    if (Input::has('ipmanversiyon')) {
                                        $program->versiyon = Input::get('ipmanversiyon');
                                    }
                                    if (Input::has('ipmankullanici')) {
                                        $program->kullaniciadi = Input::get('ipmankullanici');
                                    }
                                    if (Input::has('ipmansifre')) {
                                        $program->sifre = Input::get('ipmansifre');
                                    }
                                    if (Input::has('ipmanmanas')) {
                                        $program->yetkilisifre = Input::get('ipmanmanas');
                                    }
                                    if (Input::has('ipmandiger')) {
                                        $program->diger = Input::get('ipmandiger');
                                    }
                                    $program->save();
                                    $programlar .= ($programlar == "" ? "" : ",") . $program->id;

                                    $database = new EdestekSistemDatabase;
                                    $database->edestekdatabase_id = 5;
                                    if (Input::has('ipmansqlversiyon')) {
                                        $database->versiyon = Input::get('ipmansqlversiyon');
                                    }
                                    if (Input::has('ipmansqlkullanici')) {
                                        $database->kullaniciadi = Input::get('ipmansqlkullanici');
                                    }
                                    if (Input::has('ipmansqlsifre')) {
                                        $database->sifre = Input::get('ipmansqlsifre');
                                    }
                                    if (Input::has('ipmanveritabani')) {
                                        $database->adi = Input::get('ipmanveritabani');
                                    }
                                    if (Input::has('ipmansqldiger')) {
                                        $database->diger = Input::get('ipmansqldiger');
                                    }
                                    $database->save();
                                    $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $database->id;
                                    break;
                            }
                        }
                    }
                    $sistembilgi->programlar = $programlar;
                    $sistembilgi->veritabanlari = $veritabanlari;

                    if (Input::has('cariadi')) {
                        $sistembilgi->netsiscari_id = Input::get('cariadi');
                    }
                    if (Input::has('options2')) {
                        $sistembilgi->plasiyer_id = Input::get('options2');
                    }

                    $sistembilgi->save();
                    $edestekmusteri->edesteksistembilgi_id = $sistembilgi->id;
                }  catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sistem Bilgisi Kaydedilemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning'));
                }
            }
            if (Input::has('projedetayyeniid')) {
                $edestekmusteri->projedetay = Input::get('projedetayyeniid');
            }
            if (Input::has('baslangic')) {
                $baslangictarih = date("Y-m-d", strtotime(Input::get('baslangic')));
                $edestekmusteri->baslangictarihi = $baslangictarih;
            }
            if (Input::has('bitis')) {
                $bitistarih = date("Y-m-d", strtotime(Input::get('bitis')));
                $edestekmusteri->bitistarihi = $bitistarih;
            }
            If (Input::hasFile('resim')) {
                try {
                    $resim = Input::file('resim');
                    $uzanti = $resim->getClientOriginalExtension();
                    $isim = Str::slug($adi) . Str::slug(str_random(5)) . '.' . $uzanti;
                    $resim->move('assets/images/proje/', $isim);
                    $image = Image::make('assets/images/proje/' . $isim);
                    $image->fit(200);
                    $image->save();
                    $edestekmusteri->projeresim = $isim;
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'proje Resimi Kaydedilemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning'));
                }
            }
            $baskiidler = "";
            if (Input::has('options9')) {
                try {
                    $baskiturler = Input::get('options9');
                    foreach ($baskiturler as $baskitur) {
                        $baski = new EdestekKartBaski;
                        switch ($baskitur) {
                            case "1":
                                $baski->edestekbaskitur_id = 1;
                                If (Input::hasFile('suontaraf')) {
                                    $ontaraf = Input::file('suontaraf');
                                    $uzanti = $ontaraf->getClientOriginalExtension();
                                    $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                    $ontaraf->move('assets/images/baski/', $isim);
                                    $image = Image::make('assets/images/baski/' . $isim);
                                    $image->fit(400, 256);
                                    $image->save();
                                    $baski->onresim = $isim;
                                }
                                If (Input::hasFile('suarkataraf')) {
                                    $arkataraf = Input::file('suarkataraf');
                                    $uzanti = $arkataraf->getClientOriginalExtension();
                                    $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                    $arkataraf->move('assets/images/baski/', $isim);
                                    $image = Image::make('assets/images/baski/' . $isim);
                                    $image->fit(400, 256);
                                    $image->save();
                                    $baski->arkaresim = $isim;
                                }
                                $baski->save();
                                $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                                break;
                            case "2":
                                $baski->edestekbaskitur_id = 2;
                                If (Input::hasFile('klmontaraf')) {
                                    $ontaraf = Input::file('klmontaraf');
                                    $uzanti = $ontaraf->getClientOriginalExtension();
                                    $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                    $ontaraf->move('assets/images/baski/', $isim);
                                    $image = Image::make('assets/images/baski/' . $isim);
                                    $image->fit(400, 256);
                                    $image->save();
                                    $baski->onresim = $isim;
                                }
                                If (Input::hasFile('klmarkataraf')) {
                                    $arkataraf = Input::file('klmarkataraf');
                                    $uzanti = $arkataraf->getClientOriginalExtension();
                                    $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                    $arkataraf->move('assets/images/baski/', $isim);
                                    $image = Image::make('assets/images/baski/' . $isim);
                                    $image->fit(400, 256);
                                    $image->save();
                                    $baski->arkaresim = $isim;
                                }
                                $baski->save();
                                $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                                break;
                            case "3":
                                $baski->edestekbaskitur_id = 3;
                                $baski->save();
                                $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                                break;
                            case "4":
                                $baski->edestekbaskitur_id = 4;
                                If (Input::hasFile('trifazeontaraf')) {
                                    $ontaraf = Input::file('trifazeontaraf');
                                    $uzanti = $ontaraf->getClientOriginalExtension();
                                    $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                    $ontaraf->move('assets/images/baski/', $isim);
                                    $image = Image::make('assets/images/baski/' . $isim);
                                    $image->fit(400, 256);
                                    $image->save();
                                    $baski->onresim = $isim;
                                }
                                If (Input::hasFile('trifazearkataraf')) {
                                    $arkataraf = Input::file('trifazearkataraf');
                                    $uzanti = $arkataraf->getClientOriginalExtension();
                                    $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                    $arkataraf->move('assets/images/baski/', $isim);
                                    $image = Image::make('assets/images/baski/' . $isim);
                                    $image->fit(400, 256);
                                    $image->save();
                                    $baski->arkaresim = $isim;
                                }
                                $baski->save();
                                $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                                break;
                            case "5":
                                $baski->edestekbaskitur_id = 5;
                                If (Input::hasFile('monoontaraf')) {
                                    $ontaraf = Input::file('monoontaraf');
                                    $uzanti = $ontaraf->getClientOriginalExtension();
                                    $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                    $ontaraf->move('assets/images/baski/', $isim);
                                    $image = Image::make('assets/images/baski/' . $isim);
                                    $image->fit(400, 256);
                                    $image->save();
                                    $baski->onresim = $isim;
                                }
                                If (Input::hasFile('monoarkataraf')) {
                                    $arkataraf = Input::file('monoarkataraf');
                                    $uzanti = $arkataraf->getClientOriginalExtension();
                                    $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                    $arkataraf->move('assets/images/baski/', $isim);
                                    $image = Image::make('assets/images/baski/' . $isim);
                                    $image->fit(400, 256);
                                    $image->save();
                                    $baski->arkaresim = $isim;
                                }
                                $baski->save();
                                $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                                break;
                            case "6":
                                $baski->edestekbaskitur_id = 6;
                                $baski->save();
                                $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                                break;
                            case "7":
                                $baski->edestekbaskitur_id = 7;
                                If (Input::hasFile('klimaontaraf')) {
                                    $ontaraf = Input::file('klimaontaraf');
                                    $uzanti = $ontaraf->getClientOriginalExtension();
                                    $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                    $ontaraf->move('assets/images/baski/', $isim);
                                    $image = Image::make('assets/images/baski/' . $isim);
                                    $image->fit(400, 256);
                                    $image->save();
                                    $baski->onresim = $isim;
                                }
                                If (Input::hasFile('klimaarkataraf')) {
                                    $arkataraf = Input::file('klimaarkataraf');
                                    $uzanti = $arkataraf->getClientOriginalExtension();
                                    $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                    $arkataraf->move('assets/images/baski/', $isim);
                                    $image = Image::make('assets/images/baski/' . $isim);
                                    $image->fit(400, 256);
                                    $image->save();
                                    $baski->arkaresim = $isim;
                                }
                                $baski->save();
                                $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                                break;
                            case "8":
                                $baski->edestekbaskitur_id = 8;
                                If (Input::hasFile('gazontaraf')) {
                                    $ontaraf = Input::file('gazontaraf');
                                    $uzanti = $ontaraf->getClientOriginalExtension();
                                    $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                    $ontaraf->move('assets/images/baski/', $isim);
                                    $image = Image::make('assets/images/baski/' . $isim);
                                    $image->fit(400, 256);
                                    $image->save();
                                    $baski->onresim = $isim;
                                }
                                If (Input::hasFile('gazarkataraf')) {
                                    $arkataraf = Input::file('gazarkataraf');
                                    $uzanti = $arkataraf->getClientOriginalExtension();
                                    $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                    $arkataraf->move('assets/images/baski/', $isim);
                                    $image = Image::make('assets/images/baski/' . $isim);
                                    $image->fit(400, 256);
                                    $image->save();
                                    $baski->arkaresim = $isim;
                                }
                                $baski->save();
                                $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                                break;
                            case "9":
                                $baski->edestekbaskitur_id = 9;
                                If (Input::hasFile('mifareontaraf')) {
                                    $ontaraf = Input::file('mifareontaraf');
                                    $uzanti = $ontaraf->getClientOriginalExtension();
                                    $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                    $ontaraf->move('assets/images/baski/', $isim);
                                    $image = Image::make('assets/images/baski/' . $isim);
                                    $image->fit(400, 256);
                                    $image->save();
                                    $baski->onresim = $isim;
                                }
                                If (Input::hasFile('mifarearkataraf')) {
                                    $arkataraf = Input::file('mifarearkataraf');
                                    $uzanti = $arkataraf->getClientOriginalExtension();
                                    $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                    $arkataraf->move('assets/images/baski/', $isim);
                                    $image = Image::make('assets/images/baski/'.$isim);
                                    $image->fit(400,256);
                                    $image->save();
                                    $baski->arkaresim = $isim;
                                }
                                $baski->save();
                                $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                            break;
                            case "10":
                                $baski->edestekbaskitur_id=10;
                                $baski->save();
                                $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                            break;
                        }
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Baskı Bilgisi Kaydedilemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning'));
                }
            }
            $edestekmusteri->edestekbaskiidler = $baskiidler;
            if (Input::has('options3')) {
                $urunsecilen = Input::get('options3');
                $urunler="";
                foreach($urunsecilen as $urun)
                {
                    $urunler .= ($urunler=="" ? "" : ",").$urun;
                }
                $edestekmusteri->urunturleri = $urunler;
            }
            if (Input::has('options4')) {
                $programsecilen = Input::get('options4');
                $programlar="";
                foreach($programsecilen as $program)
                {
                    $programlar .= ($programlar=="" ? "" : ",").$program;
                }
                $edestekmusteri->programturleri = $programlar;
            }
            $edestekmusteri->musteriadi = $adi;
            $edestekmusteri->save();
            DB::commit();
            return Redirect::to('edestek/projebilgisi')->with(array('mesaj' => 'true', 'title' => 'Müşteri Eklendi', 'text' => 'Müşteri Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Müşteri Eklenemedi', 'text' => 'Müşteri Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }
    
    public function getMusteriduzenle($id) {
        $musteri = EdestekMusteri::find($id);
        $iller = Iller::all();
        $netsiscariler = NetsisCari::all();
        $plasiyerler = Plasiyer::all();
        $urunler = EdestekUrun::all();
        $programlar = EdestekProgram::all();
        $firmalar = EdestekEntegrasyonFirma::all();
        $entegrasyonprogramlar = EdestekEntegrasyonProgram::all();
        $tipler = EdestekEntegrasyonTip::all();
        $versiyonlar = EdestekEntegrasyonVersiyon::all();
        $baskiturler = EdestekBaskiTur::all();
        if($musteri->edesteksistembilgi){
            $ekliurunler = $musteri->edesteksistembilgi->tumurunler();
            $digerurunler = $musteri->edesteksistembilgi->digerurunler();
            $ekliprogramlar = $musteri->edesteksistembilgi->tumprogramlar();
            $digerprogramlar = $musteri->edesteksistembilgi->digerprogramlar();
        }else{
            $ekliurunler = "";
            $digerurunler = "";
            $ekliprogramlar = "";
            $digerprogramlar = "";
        }
        $eklibaskilar = $musteri->tumbaskilar();
        $digerbaskilar = $musteri->digerbaskilar();
        return View::make('edestek.musteriduzenle',array('musteri'=>$musteri,'iller'=>$iller,'plasiyerler'=>$plasiyerler,'netsiscariler'=>$netsiscariler,'urunler'=>$urunler,'programlar'=>$programlar,'ekliurunler'=>$ekliurunler,'ekliprogramlar'=>$ekliprogramlar,'eklibaskilar'=>$eklibaskilar,'digerurunler'=>$digerurunler,'digerprogramlar'=>$digerprogramlar,'digerbaskilar'=>$digerbaskilar,'firmalar'=>$firmalar,'entegrasyonprogramlar'=>$entegrasyonprogramlar,'tipler'=>$tipler,'versiyonlar'=>$versiyonlar,'baskiturler'=>$baskiturler))->with('title', 'Yazılım Destek Proje Bilgisi Düzenleme Ekranı');
    }
    
    public function baskiidkontrol($id,$yeniler){
        if($yeniler)
        {   
            foreach($yeniler as $yeni)
            {
                if($id==$yeni){
                    return true;
                }
            }
            return false;
        }
        return false;
    }

    public function baskiidkontrolyeni($id,$baskilar){
         if($baskilar!="")
        {
            $baskilar = explode(',', $baskilar); 
            foreach($baskilar as $baskiid)
            {
                $baski = EdestekKartBaski::find($baskiid);
                if($baski) {
                    if ($id == $baski->edestekbaskitur_id) {
                        return true;
                    }
                }
            }
            return false;
        }
        return false;
    }
    
    public function urunkontrol($id,$yeniler){
        if($yeniler)
        {
            foreach($yeniler as $yeni)
            {
                if($id==$yeni){
                    return true;
                }
            }
            return false;
        }
        return false;
    }
    
    public function urunkontrolyeni($id,$urunler){
        if($urunler)
        {
            foreach($urunler as $urunid)
            {
                $urun = EdestekSistemUrun::find($urunid);
                if($urun) {
                    if ($id == $urun->edestekurun_id) {
                        return true;
                    }
                }
            }
            return false;
        }
        return false;
    }
    
    public function programkontrol($id,$yeniler){
        if($yeniler)
        {
            foreach($yeniler as $yeni)
            {
                if($id==$yeni){
                    return true;
                }
            }
            return false;
        }
        return false;
    }
    
    public function programkontrolyeni($id,$programlar){
        if($programlar)
        {
            foreach($programlar as $programid)
            {
                $program = EdestekSistemProgram::find($programid);
                if($program) {
                    if($id==$program->edestekprogram_id){
                        return true;
                    }
                }
            }
            return false;
        }
        return false;
    }

    public function veritabanikontrolyeni($id,$veritabanlari){
        if($veritabanlari)
        {
            foreach($veritabanlari as $databaseid)
            {
                $database = EdestekSistemDatabase::find($databaseid);
                if($database) {
                    if($id==$database->edestekdatabase_id){
                        return true;
                    }
                }
            }
            return false;
        }
        return false;
    }
    
    public function postMusteriduzenle($id){
        $rules = ['adi'=>'required|unique:edestekmusteri,musteriadi,'.$id,'resim'=>'image|mimes:jpeg,jpg,png','mail'=>'email',
            'suontaraf'=>'image|mimes:jpeg,jpg,png','suarkataraf'=>'image|mimes:jpeg,jpg,png','klmontaraf'=>'image|mimes:jpeg,jpg,png',
            'klmarkataraf'=>'image|mimes:jpeg,jpg,png','trifazeontaraf'=>'image|mimes:jpeg,jpg,png','trifazearkataraf'=>'image|mimes:jpeg,jpg,png',
            'monoontaraf'=>'image|mimes:jpeg,jpg,png','monoarkataraf'=>'image|mimes:jpeg,jpg,png','klimaontaraf'=>'image|mimes:jpeg,jpg,png',
            'klimaarkataraf'=>'image|mimes:jpeg,jpg,png','gazontaraf'=>'image|mimes:jpeg,jpg,png','gazarkataraf'=>'image|mimes:jpeg,jpg,png',
			'mifareontaraf'=>'image|mimes:jpeg,jpg,png','mifarearkataraf'=>'image|mimes:jpeg,jpg,png'];
        $validate = Validator::make(Input::all(),$rules);
        $messages = $validate->messages();
        if ($validate->fails()) {
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
        }
        try {
            DB::beginTransaction();
            $edestekmusteri = EdestekMusteri::find($id);
            if ($edestekmusteri->edestekmusteribilgi_id) { //önceki kayıt varsa
                $musteribilgi = EdestekMusteriBilgi::find($edestekmusteri->edestekmusteribilgi_id);
                if (Input::has('adresi') || Input::has('options1') || Input::has('telefon') || Input::has('mail') || Input::has('yetkili') || Input::has('yetkilitel') ||
                    Input::has('teamid') || Input::has('teampass') || Input::has('ammyyid') || Input::has('alpemixid') || Input::has('alpemixpass') || Input::has('uzakip') ||
                    Input::has('uzakkullanici') || Input::has('uzakpass'))
                { //müşteri bilgisi var
                    if (Input::has('adresi')) {
                        $musteribilgi->adresi = Input::get('adresi');
                    } else {
                        $musteribilgi->adresi = NULL;
                    }
                    if (Input::has('options1')) {
                        $musteribilgi->iller_id = Input::get('options1');
                    } else {
                        $musteribilgi->iller_id = NULL;
                    }
                    if (Input::has('telefon')) {
                        $musteribilgi->telefon = Input::get('telefon');
                    } else {
                        $musteribilgi->telefon = NULL;
                    }
                    if (Input::has('mail')) {
                        $musteribilgi->mail = Input::get('mail');
                    } else {
                        $musteribilgi->mail = NULL;
                    }
                    if (Input::has('yetkili')) {
                        $musteribilgi->yetkiliadi = Input::get('yetkili');
                    } else {
                        $musteribilgi->yetkiliadi = NULL;
                    }
                    if (Input::has('yetkilitel')) {
                        $musteribilgi->yetkilitel = Input::get('yetkilitel');
                    } else {
                        $musteribilgi->yetkilitel = NULL;
                    }
                    if (Input::has('teamid')) {
                        $musteribilgi->teamid = Input::get('teamid');
                    } else {
                        $musteribilgi->teamid = NULL;
                    }
                    if (Input::has('teampass')) {
                        $musteribilgi->teampass = Input::get('teampass');
                    } else {
                        $musteribilgi->teampass = NULL;
                    }
                    if (Input::has('ammyyid')) {
                        $musteribilgi->ammyyid = Input::get('ammyyid');
                    } else {
                        $musteribilgi->ammyyid = NULL;
                    }
                    if (Input::has('alpemixid')) {
                        $musteribilgi->alpemixid = Input::get('alpemixid');
                    } else {
                        $musteribilgi->alpemixid = NULL;
                    }
                    if (Input::has('alpemixpass')) {
                        $musteribilgi->alpemixpass = Input::get('alpemixpass');
                    } else {
                        $musteribilgi->alpemixpass = NULL;
                    }
                    if (Input::has('uzakip')) {
                        $musteribilgi->uzakip = Input::get('uzakip');
                    } else {
                        $musteribilgi->uzakip = NULL;
                    }
                    if (Input::has('uzakkullanici')) {
                        $musteribilgi->uzakkullanici = Input::get('uzakkullanici');
                    } else {
                        $musteribilgi->uzakkullanici = NULL;
                    }
                    if (Input::has('uzakpass')) {
                        $musteribilgi->uzakpass = Input::get('uzakpass');
                    } else {
                        $musteribilgi->uzakpass = NULL;
                    }
                    $musteribilgi->save();
                    $edestekmusteri->edestekmusteribilgi_id = $musteribilgi->id;
                } else { // müşteri bilgisi yoksa 
                    $musteribilgi->delete();
                    $edestekmusteri->edestekmusteribilgi_id = NULL;
                }
            } else if (Input::has('adresi') || Input::has('options1') || Input::has('telefon') || Input::has('mail') || Input::has('yetkili') || Input::has('yetkilitel') ||
                Input::has('teamid') || Input::has('teampass') || Input::has('ammyyid') || Input::has('alpemixid') || Input::has('alpemixpass') || Input::has('uzakip') ||
                Input::has('uzakkullanici') || Input::has('uzakpass'))
            { //yeni müşteri bilgisi var
                $musteribilgi = new EdestekMusteriBilgi;
                if (Input::has('adresi')) {
                    $musteribilgi->adresi = Input::get('adresi');
                }
                if (Input::has('options1')) {
                    $musteribilgi->iller_id = Input::get('options1');
                }
                if (Input::has('telefon')) {
                    $musteribilgi->telefon = Input::get('telefon');
                }
                if (Input::has('mail')) {
                    $musteribilgi->mail = Input::get('mail');
                }
                if (Input::has('yetkili')) {
                    $musteribilgi->yetkiliadi = Input::get('yetkili');
                }
                if (Input::has('yetkilitel')) {
                    $musteribilgi->yetkilitel = Input::get('yetkilitel');
                }
                if (Input::has('teamid')) {
                    $musteribilgi->teamid = Input::get('teamid');
                }
                if (Input::has('teampass')) {
                    $musteribilgi->teampass = Input::get('teampass');
                }
                if (Input::has('ammyyid')) {
                    $musteribilgi->ammyyid = Input::get('ammyyid');
                }
                if (Input::has('alpemixid')) {
                    $musteribilgi->alpemixid = Input::get('alpemixid');
                }
                if (Input::has('alpemixpass')) {
                    $musteribilgi->alpemixpass = Input::get('alpemixpass');
                }
                if (Input::has('uzakip')) {
                    $musteribilgi->uzakip = Input::get('uzakip');
                }
                if (Input::has('uzakkullanici')) {
                    $musteribilgi->uzakkullanici = Input::get('uzakkullanici');
                }
                if (Input::has('uzakpass')) {
                    $musteribilgi->uzakpass = Input::get('uzakpass');
                }
                $musteribilgi->save();
                $edestekmusteri->edestekmusteribilgi_id = $musteribilgi->id;
            }

            if ($edestekmusteri->edesteksistembilgi_id) { //önceki kayıt varsa
                $sistembilgi = EdestekSistemBilgi::find($edestekmusteri->edesteksistembilgi_id);
                if (Input::has('cariadi') || Input::has('options2') || Input::has('options3') || Input::has('options4'))
                { //sistem bilgisi var
                    $urunler = "";
                    if ($sistembilgi->urunler) //urun onceden varsa
                    {
                        $urun = explode(',', $sistembilgi->urunler);
                        foreach ($urun as $uruneski) {
                            $urunbilgi = EdestekSistemUrun::find($uruneski);
                            if ($urunbilgi->edestekurun_id == 1) {
                                if ((Input::has('suadi') || Input::has('suadet') || Input::has('suissue') || Input::has('sudetay')) && $this->urunkontrol(1, Input::get('options3')))
                                { //su sayacı urun bilgisi var
                                    if (Input::has('suadi')) {
                                        $urunbilgi->adi = Input::get('suadi');
                                    } else {
                                        $urunbilgi->adi = NULL;
                                    }
                                    if (Input::has('suadet')) {
                                        $urunbilgi->adet = Input::get('suadet');
                                    } else {
                                        $urunbilgi->adet = NULL;
                                    }
                                    if (Input::has('suissue')) {
                                        $urunbilgi->issue = Input::get('suissue');
                                    } else {
                                        $urunbilgi->issue = NULL;
                                    }
                                    if (Input::has('sudetay')) {
                                        $urunbilgi->detay = Input::get('sudetay');
                                    } else {
                                        $urunbilgi->detay = NULL;
                                    }
                                    $urunbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urunbilgi->id;
                                } else {
                                    $urunbilgi->delete();
                                }
                            } else if ($urunbilgi->edestekurun_id == 2) {
                                if ((Input::has('sicakadi') || Input::has('sicakadet') || Input::has('sicakissue') || Input::has('sicakdetay')) && $this->urunkontrol(2, Input::get('options3')))
                                { //sıcak su sayacı urun bilgisi var
                                    if (Input::has('sicakadi')) {
                                        $urunbilgi->adi = Input::get('sicakadi');
                                    } else {
                                        $urunbilgi->adi = NULL;
                                    }
                                    if (Input::has('sicakadet')) {
                                        $urunbilgi->adet = Input::get('sicakadet');
                                    } else {
                                        $urunbilgi->adet = NULL;
                                    }
                                    if (Input::has('sicakissue')) {
                                        $urunbilgi->issue = Input::get('sicakissue');
                                    } else {
                                        $urunbilgi->issue = NULL;
                                    }
                                    if (Input::has('sicakdetay')) {
                                        $urunbilgi->detay = Input::get('sicakdetay');
                                    } else {
                                        $urunbilgi->detay = NULL;
                                    }
                                    $urunbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urunbilgi->id;
                                } else {
                                    $urunbilgi->delete();
                                }
                            } else if ($urunbilgi->edestekurun_id == 3) {
                                if ((Input::has('elkadi') || Input::has('elkadet') || Input::has('elkissue') || Input::has('elkdetay')) && $this->urunkontrol(3, Input::get('options3')))
                                { //elektrik sayacı urun bilgisi var
                                    if (Input::has('elkadi')) {
                                        $urunbilgi->adi = Input::get('elkadi');
                                    } else {
                                        $urunbilgi->adi = NULL;
                                    }
                                    if (Input::has('elkadet')) {
                                        $urunbilgi->adet = Input::get('elkadet');
                                    } else {
                                        $urunbilgi->adet = NULL;
                                    }
                                    if (Input::has('elkissue')) {
                                        $urunbilgi->issue = Input::get('elkissue');
                                    } else {
                                        $urunbilgi->issue = NULL;
                                    }
                                    if (Input::has('elkdetay')) {
                                        $urunbilgi->detay = Input::get('elkdetay');
                                    } else {
                                        $urunbilgi->detay = NULL;
                                    }
                                    $urunbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urunbilgi->id;
                                } else {
                                    $urunbilgi->delete();
                                }
                            } else if ($urunbilgi->edestekurun_id == 4) {
                                if ((Input::has('gazadi') || Input::has('gazadet') || Input::has('gazissue') || Input::has('gazdetay')) && $this->urunkontrol(4, Input::get('options3')))
                                { //gaz sayacı urun bilgisi var
                                    if (Input::has('gazadi')) {
                                        $urunbilgi->adi = Input::get('gazadi');
                                    } else {
                                        $urunbilgi->adi = NULL;
                                    }
                                    if (Input::has('gazadet')) {
                                        $urunbilgi->adet = Input::get('gazadet');
                                    } else {
                                        $urunbilgi->adet = NULL;
                                    }
                                    if (Input::has('gazissue')) {
                                        $urunbilgi->issue = Input::get('gazissue');
                                    } else {
                                        $urunbilgi->issue = NULL;
                                    }
                                    if (Input::has('gazdetay')) {
                                        $urunbilgi->detay = Input::get('gazdetay');
                                    } else {
                                        $urunbilgi->detay = NULL;
                                    }
                                    $urunbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urunbilgi->id;
                                } else {
                                    $urunbilgi->delete();
                                }
                            } else if ($urunbilgi->edestekurun_id == 5) {
                                if ((Input::has('isiadi') || Input::has('isiadet') || Input::has('isiissue') || Input::has('isidetay')) && $this->urunkontrol(5, Input::get('options3')))
                                { //ısı sayacı urun bilgisi var
                                    if (Input::has('isiadi')) {
                                        $urunbilgi->adi = Input::get('isiadi');
                                    } else {
                                        $urunbilgi->adi = NULL;
                                    }
                                    if (Input::has('isiadet')) {
                                        $urunbilgi->adet = Input::get('isiadet');
                                    } else {
                                        $urunbilgi->adet = NULL;
                                    }
                                    if (Input::has('isiissue')) {
                                        $urunbilgi->issue = Input::get('isiissue');
                                    } else {
                                        $urunbilgi->issue = NULL;
                                    }
                                    if (Input::has('isidetay')) {
                                        $urunbilgi->detay = Input::get('isidetay');
                                    } else {
                                        $urunbilgi->detay = NULL;
                                    }
                                    $urunbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urunbilgi->id;
                                } else {
                                    $urunbilgi->delete();
                                }
                            } else if ($urunbilgi->edestekurun_id == 6) {
                                if ((Input::has('payolceradi') || Input::has('payolceradet') || Input::has('payolcerissue') || Input::has('payolcerdetay')) && $this->urunkontrol(6, Input::get('options3')))
                                { //payolcer urun bilgisi var
                                    if (Input::has('payolceradi')) {
                                        $urunbilgi->adi = Input::get('payolceradi');
                                    } else {
                                        $urunbilgi->adi = NULL;
                                    }
                                    if (Input::has('payolceradet')) {
                                        $urunbilgi->adet = Input::get('payolceradet');
                                    } else {
                                        $urunbilgi->adet = NULL;
                                    }
                                    if (Input::has('payolcerissue')) {
                                        $urunbilgi->issue = Input::get('payolcerissue');
                                    } else {
                                        $urunbilgi->issue = NULL;
                                    }
                                    if (Input::has('payolcerdetay')) {
                                        $urunbilgi->detay = Input::get('payolcerdetay');
                                    } else {
                                        $urunbilgi->detay = NULL;
                                    }
                                    $urunbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urunbilgi->id;
                                } else {
                                    $urunbilgi->delete();
                                }
                            } else if ($urunbilgi->edestekurun_id == 7) {
                                if ((Input::has('terminaladi') || Input::has('terminaladet') || Input::has('terminalissue') || Input::has('terminaldetay')) && $this->urunkontrol(7, Input::get('options3')))
                                { //terminal urun bilgisi var
                                    if (Input::has('terminaladi')) {
                                        $urunbilgi->adi = Input::get('terminaladi');
                                    } else {
                                        $urunbilgi->adi = NULL;
                                    }
                                    if (Input::has('terminaladet')) {
                                        $urunbilgi->adet = Input::get('terminaladet');
                                    } else {
                                        $urunbilgi->adet = NULL;
                                    }
                                    if (Input::has('terminalissue')) {
                                        $urunbilgi->issue = Input::get('terminalissue');
                                    } else {
                                        $urunbilgi->issue = NULL;
                                    }
                                    if (Input::has('terminaldetay')) {
                                        $urunbilgi->detay = Input::get('terminaldetay');
                                    } else {
                                        $urunbilgi->detay = NULL;
                                    }
                                    $urunbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urunbilgi->id;
                                } else {
                                    $urunbilgi->delete();
                                }
                            } else if ($urunbilgi->edestekurun_id == 8) {
                                if ((Input::has('kioskadi') || Input::has('kioskadet') || Input::has('kioskissue') || Input::has('kioskdetay')) && $this->urunkontrol(8, Input::get('options3')))
                                { //kiosk urun bilgisi var
                                    if (Input::has('kioskadi')) {
                                        $urunbilgi->adi = Input::get('kioskadi');
                                    } else {
                                        $urunbilgi->adi = NULL;
                                    }
                                    if (Input::has('kioskadet')) {
                                        $urunbilgi->adet = Input::get('kioskadet');
                                    } else {
                                        $urunbilgi->adet = NULL;
                                    }
                                    if (Input::has('kioskissue')) {
                                        $urunbilgi->issue = Input::get('kioskissue');
                                    } else {
                                        $urunbilgi->issue = NULL;
                                    }
                                    if (Input::has('kioskdetay')) {
                                        $urunbilgi->detay = Input::get('kioskdetay');
                                    } else {
                                        $urunbilgi->detay = NULL;
                                    }
                                    $urunbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urunbilgi->id;
                                } else {
                                    $urunbilgi->delete();
                                }
                            } else if ($urunbilgi->edestekurun_id == 9) {
                                if ((Input::has('klimaadi') || Input::has('klimaadet') || Input::has('klimaissue') || Input::has('klimadetay')) && $this->urunkontrol(9, Input::get('options3')))
                                { //klima kontrol urun bilgisi var
                                    if (Input::has('klimaadi')) {
                                        $urunbilgi->adi = Input::get('klimaadi');
                                    } else {
                                        $urunbilgi->adi = NULL;
                                    }
                                    if (Input::has('klimaadet')) {
                                        $urunbilgi->adet = Input::get('klimaadet');
                                    } else {
                                        $urunbilgi->adet = NULL;
                                    }
                                    if (Input::has('klimaissue')) {
                                        $urunbilgi->issue = Input::get('klimaissue');
                                    } else {
                                        $urunbilgi->issue = NULL;
                                    }
                                    if (Input::has('klimadetay')) {
                                        $urunbilgi->detay = Input::get('klimadetay');
                                    } else {
                                        $urunbilgi->detay = NULL;
                                    }
                                    $urunbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urunbilgi->id;
                                } else {
                                    $urunbilgi->delete();
                                }
                            } else if ($urunbilgi->edestekurun_id == 10) {
                                if ((Input::has('digeradi') || Input::has('digeradet') || Input::has('digerissue') || Input::has('digerdetay')) && $this->urunkontrol(10, Input::get('options3')))
                                { //diger urun bilgisi var
                                    if (Input::has('digeradi')) {
                                        $urunbilgi->adi = Input::get('digeradi');
                                    } else {
                                        $urunbilgi->adi = NULL;
                                    }
                                    if (Input::has('digeradet')) {
                                        $urunbilgi->adet = Input::get('digeradet');
                                    } else {
                                        $urunbilgi->adet = NULL;
                                    }
                                    if (Input::has('digerissue')) {
                                        $urunbilgi->issue = Input::get('digerissue');
                                    } else {
                                        $urunbilgi->issue = NULL;
                                    }
                                    if (Input::has('digerdetay')) {
                                        $urunbilgi->detay = Input::get('digerdetay');
                                    } else {
                                        $urunbilgi->detay = NULL;
                                    }
                                    $urunbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urunbilgi->id;
                                } else {
                                    $urunbilgi->delete();
                                }
                            } else if ($urunbilgi->edestekurun_id == 11) {
                                if ((Input::has('icuniteadi') || Input::has('icuniteadet') || Input::has('icuniteissue') || Input::has('icunitedetay')) && $this->urunkontrol(11, Input::get('options3')))
                                { //klima kontrol urun bilgisi var
                                    if (Input::has('icuniteadi')) {
                                        $urunbilgi->adi = Input::get('icuniteadi');
                                    } else {
                                        $urunbilgi->adi = NULL;
                                    }
                                    if (Input::has('icuniteadet')) {
                                        $urunbilgi->adet = Input::get('icuniteadet');
                                    } else {
                                        $urunbilgi->adet = NULL;
                                    }
                                    if (Input::has('icuniteissue')) {
                                        $urunbilgi->issue = Input::get('icuniteissue');
                                    } else {
                                        $urunbilgi->issue = NULL;
                                    }
                                    if (Input::has('icunitedetay')) {
                                        $urunbilgi->detay = Input::get('icunitedetay');
                                    } else {
                                        $urunbilgi->detay = NULL;
                                    }
                                    $urunbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urunbilgi->id;
                                } else {
                                    $urunbilgi->delete();
                                }
                            } else if ($urunbilgi->edestekurun_id == 12) {
                                if ((Input::has('kartokuyucuadi') || Input::has('kartokuyucuadet') || Input::has('kartokuyucucesit') || Input::has('kartokuyucudetay')) && $this->urunkontrol(12, Input::get('options3')))
                                { //klima kontrol urun bilgisi var
                                    if (Input::has('kartokuyucuadi')) {
                                        $urunbilgi->adi = Input::get('kartokuyucuadi');
                                    } else {
                                        $urunbilgi->adi = NULL;
                                    }
                                    if (Input::has('kartokuyucuadet')) {
                                        $urunbilgi->adet = Input::get('kartokuyucuadet');
                                    } else {
                                        $urunbilgi->adet = NULL;
                                    }
                                    if (Input::has('kartokuyucucesit')) {
                                        $urunbilgi->issue = Input::get('kartokuyucucesit');
                                    } else {
                                        $urunbilgi->issue = NULL;
                                    }
                                    if (Input::has('kartokuyucudetay')) {
                                        $urunbilgi->detay = Input::get('kartokuyucudetay');
                                    } else {
                                        $urunbilgi->detay = NULL;
                                    }
                                    $urunbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $urunbilgi->id;
                                } else {
                                    $urunbilgi->delete();
                                }
                            }
                        }
                        if (Input::has('options3')) {
                            $urunturler = Input::get('options3');
                            foreach ($urunturler as $uruntur) {
                                if (!$this->urunkontrolyeni($uruntur, $urun)) // yeni ekleme ise
                                {
                                    if ((Input::has('suadi') || Input::has('suadet') || Input::has('suissue') || Input::has('sudetay')) && $uruntur == '1')
                                    { //su sayacı urun bilgisi var
                                        $subilgi = new EdestekSistemUrun;
                                        $subilgi->edestekurun_id = 1;
                                        if (Input::has('suadi')) {
                                            $subilgi->adi = Input::get('suadi');
                                        }
                                        if (Input::has('suadet')) {
                                            $subilgi->adet = Input::get('suadet');
                                        }
                                        if (Input::has('suissue')) {
                                            $subilgi->issue = Input::get('suissue');
                                        }
                                        if (Input::has('sudetay')) {
                                            $subilgi->detay = Input::get('sudetay');
                                        }
                                        $subilgi->save();
                                        $urunler .= ($urunler == "" ? "" : ",") . $subilgi->id;
                                    }
                                    if ((Input::has('sicakadi') || Input::has('sicakadet') || Input::has('sicakissue') || Input::has('sicakdetay')) && $uruntur == '2')
                                    { //sicak su sayacı urun bilgisi var
                                        $sicakbilgi = new EdestekSistemUrun;
                                        $sicakbilgi->edestekurun_id = 2;
                                        if (Input::has('sicakadi')) {
                                            $sicakbilgi->adi = Input::get('sicakadi');
                                        }
                                        if (Input::has('sicakadet')) {
                                            $sicakbilgi->adet = Input::get('sicakadet');
                                        }
                                        if (Input::has('sicakissue')) {
                                            $sicakbilgi->issue = Input::get('sicakissue');
                                        }
                                        if (Input::has('sicakdetay')) {
                                            $sicakbilgi->detay = Input::get('sicakdetay');
                                        }
                                        $sicakbilgi->save();
                                        $urunler .= ($urunler == "" ? "" : ",") . $sicakbilgi->id;
                                    }
                                    if ((Input::has('elkadi') || Input::has('elkadet') || Input::has('elkissue') || Input::has('elkdetay')) && $uruntur == '3')
                                    { //elektrik sayacı urun bilgisi var
                                        $elkbilgi = new EdestekSistemUrun;
                                        $elkbilgi->edestekurun_id = 3;
                                        if (Input::has('elkadi')) {
                                            $elkbilgi->adi = Input::get('elkadi');
                                        }
                                        if (Input::has('elkadet')) {
                                            $elkbilgi->adet = Input::get('elkadet');
                                        }
                                        if (Input::has('elkissue')) {
                                            $elkbilgi->issue = Input::get('elkissue');
                                        }
                                        if (Input::has('elkdetay')) {
                                            $elkbilgi->detay = Input::get('elkdetay');
                                        }
                                        $elkbilgi->save();
                                        $urunler .= ($urunler == "" ? "" : ",") . $elkbilgi->id;
                                    }
                                    if ((Input::has('gazadi') || Input::has('gazadet') || Input::has('gazissue') || Input::has('gazdetay')) && $uruntur == '4')
                                    { //gaz sayacı urun bilgisi var
                                        $gazbilgi = new EdestekSistemUrun;
                                        $gazbilgi->edestekurun_id = 4;
                                        if (Input::has('gazadi')) {
                                            $gazbilgi->adi = Input::get('gazadi');
                                        }
                                        if (Input::has('gazadet')) {
                                            $gazbilgi->adet = Input::get('gazadet');
                                        }
                                        if (Input::has('gazissue')) {
                                            $gazbilgi->issue = Input::get('gazissue');
                                        }
                                        if (Input::has('gazdetay')) {
                                            $gazbilgi->detay = Input::get('gazdetay');
                                        }
                                        $gazbilgi->save();
                                        $urunler .= ($urunler == "" ? "" : ",") . $gazbilgi->id;
                                    }
                                    if ((Input::has('isiadi') || Input::has('isiadet') || Input::has('isiissue') || Input::has('isidetay')) && $uruntur == '5')
                                    { //isi sayacı urun bilgisi var
                                        $isibilgi = new EdestekSistemUrun;
                                        $isibilgi->edestekurun_id = 5;
                                        if (Input::has('isiadi')) {
                                            $isibilgi->adi = Input::get('isiadi');
                                        }
                                        if (Input::has('isiadet')) {
                                            $isibilgi->adet = Input::get('isiadet');
                                        }
                                        if (Input::has('isiissue')) {
                                            $isibilgi->issue = Input::get('isiissue');
                                        }
                                        if (Input::has('isidetay')) {
                                            $isibilgi->detay = Input::get('isidetay');
                                        }
                                        $isibilgi->save();
                                        $urunler .= ($urunler == "" ? "" : ",") . $isibilgi->id;
                                    }
                                    if ((Input::has('payolceradi') || Input::has('payolceradet') || Input::has('payolcerissue') || Input::has('payolcerdetay')) && $uruntur == '6')
                                    { //payolcer urun bilgisi var
                                        $payolcerbilgi = new EdestekSistemUrun;
                                        $payolcerbilgi->edestekurun_id = 6;
                                        if (Input::has('payolceradi')) {
                                            $payolcerbilgi->adi = Input::get('payolceradi');
                                        }
                                        if (Input::has('payolceradet')) {
                                            $payolcerbilgi->adet = Input::get('payolceradet');
                                        }
                                        if (Input::has('payolcerissue')) {
                                            $payolcerbilgi->issue = Input::get('payolcerissue');
                                        }
                                        if (Input::has('payolcerdetay')) {
                                            $payolcerbilgi->detay = Input::get('payolcerdetay');
                                        }
                                        $payolcerbilgi->save();
                                        $urunler .= ($urunler == "" ? "" : ",") . $payolcerbilgi->id;
                                    }
                                    if ((Input::has('terminaladi') || Input::has('terminaladet') || Input::has('terminalissue') || Input::has('terminaldetay')) && $uruntur == '7')
                                    { //el terminali urun bilgisi var
                                        $terminalbilgi = new EdestekSistemUrun;
                                        $terminalbilgi->edestekurun_id = 7;
                                        if (Input::has('terminaladi')) {
                                            $terminalbilgi->adi = Input::get('terminaladi');
                                        }
                                        if (Input::has('terminaladet')) {
                                            $terminalbilgi->adet = Input::get('terminaladet');
                                        }
                                        if (Input::has('terminalissue')) {
                                            $terminalbilgi->issue = Input::get('terminalissue');
                                        }
                                        if (Input::has('terminaldetay')) {
                                            $terminalbilgi->detay = Input::get('terminaldetay');
                                        }
                                        $terminalbilgi->save();
                                        $urunler .= ($urunler == "" ? "" : ",") . $terminalbilgi->id;
                                    }
                                    if ((Input::has('kioskadi') || Input::has('kioskadet') || Input::has('kioskissue') || Input::has('kioskdetay')) && $uruntur == '8')
                                    { //kiosk urun bilgisi var
                                        $kioskbilgi = new EdestekSistemUrun;
                                        $kioskbilgi->edestekurun_id = 8;
                                        if (Input::has('kioskadi')) {
                                            $kioskbilgi->adi = Input::get('kioskadi');
                                        }
                                        if (Input::has('kioskadet')) {
                                            $kioskbilgi->adet = Input::get('kioskadet');
                                        }
                                        if (Input::has('kioskissue')) {
                                            $kioskbilgi->issue = Input::get('kioskissue');
                                        }
                                        if (Input::has('kioskdetay')) {
                                            $kioskbilgi->detay = Input::get('kioskdetay');
                                        }
                                        $kioskbilgi->save();
                                        $urunler .= ($urunler == "" ? "" : ",") . $kioskbilgi->id;
                                    }
                                    if ((Input::has('klimaadi') || Input::has('klimaadet') || Input::has('klimaissue') || Input::has('klimadetay')) && $uruntur == '9')
                                    { //klima kontrol cihazı urun bilgisi var
                                        $klimabilgi = new EdestekSistemUrun;
                                        $klimabilgi->edestekurun_id = 9;
                                        if (Input::has('klimaadi')) {
                                            $klimabilgi->adi = Input::get('klimaadi');
                                        }
                                        if (Input::has('klimaadet')) {
                                            $klimabilgi->adet = Input::get('klimaadet');
                                        }
                                        if (Input::has('klimaissue')) {
                                            $klimabilgi->issue = Input::get('klimaissue');
                                        }
                                        if (Input::has('klimadetay')) {
                                            $klimabilgi->detay = Input::get('klimadetay');
                                        }
                                        $klimabilgi->save();
                                        $urunler .= ($urunler == "" ? "" : ",") . $klimabilgi->id;
                                    }
                                    if ((Input::has('digeradi') || Input::has('digeradet') || Input::has('digerissue') || Input::has('digerdetay')) && $uruntur == '10')
                                    { //diger sayac urun bilgisi var
                                        $digerbilgi = new EdestekSistemUrun;
                                        $digerbilgi->edestekurun_id = 10;
                                        if (Input::has('digeradi')) {
                                            $digerbilgi->adi = Input::get('digeradi');
                                        }
                                        if (Input::has('digeradet')) {
                                            $digerbilgi->adet = Input::get('digeradet');
                                        }
                                        if (Input::has('digerissue')) {
                                            $digerbilgi->issue = Input::get('digerissue');
                                        }
                                        if (Input::has('digerdetay')) {
                                            $digerbilgi->detay = Input::get('digerdetay');
                                        }
                                        $digerbilgi->save();
                                        $urunler .= ($urunler == "" ? "" : ",") . $digerbilgi->id;
                                    }
                                    if ((Input::has('icuniteadi') || Input::has('icuniteadet') || Input::has('icuniteissue') || Input::has('icunitedetay')) && $uruntur == '11')
                                    { //klima kontrol cihazı urun bilgisi var
                                        $icunitebilgi = new EdestekSistemUrun;
                                        $icunitebilgi->edestekurun_id = 11;
                                        if (Input::has('icuniteadi')) {
                                            $icunitebilgi->adi = Input::get('icuniteadi');
                                        }
                                        if (Input::has('icuniteadet')) {
                                            $icunitebilgi->adet = Input::get('icuniteadet');
                                        }
                                        if (Input::has('icuniteissue')) {
                                            $icunitebilgi->issue = Input::get('icuniteissue');
                                        }
                                        if (Input::has('icunitedetay')) {
                                            $icunitebilgi->detay = Input::get('icunitedetay');
                                        }
                                        $icunitebilgi->save();
                                        $urunler .= ($urunler == "" ? "" : ",") . $icunitebilgi->id;
                                    }
                                    if ((Input::has('kartokuyucuadi') || Input::has('kartokuyucuadet') || Input::has('kartokuyucucesit') || Input::has('kartokuyucudetay')) && $uruntur == '12')
                                    { //klima kontrol cihazı urun bilgisi var
                                        $kartokuyucubilgi = new EdestekSistemUrun;
                                        $kartokuyucubilgi->edestekurun_id = 12;
                                        if (Input::has('kartokuyucuadi')) {
                                            $kartokuyucubilgi->adi = Input::get('kartokuyucuadi');
                                        }
                                        if (Input::has('kartokuyucuadet')) {
                                            $kartokuyucubilgi->adet = Input::get('kartokuyucuadet');
                                        }
                                        if (Input::has('kartokuyucucesit')) {
                                            $kartokuyucubilgi->issue = Input::get('kartokuyucucesit');
                                        }
                                        if (Input::has('kartokuyucudetay')) {
                                            $kartokuyucubilgi->detay = Input::get('kartokuyucudetay');
                                        }
                                        $kartokuyucubilgi->save();
                                        $urunler .= ($urunler == "" ? "" : ",") . $kartokuyucubilgi->id;
                                    }
                                }
                            }
                        }
                    } else { //urun yoksa
                        if (Input::has('options3')) {
                            $urunturler = Input::get('options3');
                            foreach ($urunturler as $uruntur) {
                                if ((Input::has('suadi') || Input::has('suadet') || Input::has('suissue') || Input::has('sudetay')) && $uruntur == '1')
                                { //su sayacı urun bilgisi var
                                    $subilgi = new EdestekSistemUrun;
                                    $subilgi->edestekurun_id = 1;
                                    if (Input::has('suadi')) {
                                        $subilgi->adi = Input::get('suadi');
                                    }
                                    if (Input::has('suadet')) {
                                        $subilgi->adet = Input::get('suadet');
                                    }
                                    if (Input::has('suissue')) {
                                        $subilgi->issue = Input::get('suissue');
                                    }
                                    if (Input::has('sudetay')) {
                                        $subilgi->detay = Input::get('sudetay');
                                    }
                                    $subilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $subilgi->id;
                                }
                                if ((Input::has('sicakadi') || Input::has('sicakadet') || Input::has('sicakissue') || Input::has('sicakdetay')) && $uruntur == '2')
                                { //sicak su sayacı urun bilgisi var
                                    $sicakbilgi = new EdestekSistemUrun;
                                    $sicakbilgi->edestekurun_id = 2;
                                    if (Input::has('sicakadi')) {
                                        $sicakbilgi->adi = Input::get('sicakadi');
                                    }
                                    if (Input::has('sicakadet')) {
                                        $sicakbilgi->adet = Input::get('sicakadet');
                                    }
                                    if (Input::has('sicakissue')) {
                                        $sicakbilgi->issue = Input::get('sicakissue');
                                    }
                                    if (Input::has('sicakdetay')) {
                                        $sicakbilgi->detay = Input::get('sicakdetay');
                                    }
                                    $sicakbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $sicakbilgi->id;
                                }
                                if ((Input::has('elkadi') || Input::has('elkadet') || Input::has('elkissue') || Input::has('elkdetay')) && $uruntur == '3')
                                { //elektrik sayacı urun bilgisi var
                                    $elkbilgi = new EdestekSistemUrun;
                                    $elkbilgi->edestekurun_id = 3;
                                    if (Input::has('elkadi')) {
                                        $elkbilgi->adi = Input::get('elkadi');
                                    }
                                    if (Input::has('elkadet')) {
                                        $elkbilgi->adet = Input::get('elkadet');
                                    }
                                    if (Input::has('elkissue')) {
                                        $elkbilgi->issue = Input::get('elkissue');
                                    }
                                    if (Input::has('elkdetay')) {
                                        $elkbilgi->detay = Input::get('elkdetay');
                                    }
                                    $elkbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $elkbilgi->id;
                                }
                                if ((Input::has('gazadi') || Input::has('gazadet') || Input::has('gazissue') || Input::has('gazdetay')) && $uruntur == '4')
                                { //gaz sayacı urun bilgisi var
                                    $gazbilgi = new EdestekSistemUrun;
                                    $gazbilgi->edestekurun_id = 4;
                                    if (Input::has('gazadi')) {
                                        $gazbilgi->adi = Input::get('gazadi');
                                    }
                                    if (Input::has('gazadet')) {
                                        $gazbilgi->adet = Input::get('gazadet');
                                    }
                                    if (Input::has('gazissue')) {
                                        $gazbilgi->issue = Input::get('gazissue');
                                    }
                                    if (Input::has('gazdetay')) {
                                        $gazbilgi->detay = Input::get('gazdetay');
                                    }
                                    $gazbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $gazbilgi->id;
                                }
                                if ((Input::has('isiadi') || Input::has('isiadet') || Input::has('isiissue') || Input::has('isidetay')) && $uruntur == '5')
                                { //isi sayacı urun bilgisi var
                                    $isibilgi = new EdestekSistemUrun;
                                    $isibilgi->edestekurun_id = 5;
                                    if (Input::has('isiadi')) {
                                        $isibilgi->adi = Input::get('isiadi');
                                    }
                                    if (Input::has('isiadet')) {
                                        $isibilgi->adet = Input::get('isiadet');
                                    }
                                    if (Input::has('isiissue')) {
                                        $isibilgi->issue = Input::get('isiissue');
                                    }
                                    if (Input::has('isidetay')) {
                                        $isibilgi->detay = Input::get('isidetay');
                                    }
                                    $isibilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $isibilgi->id;
                                }
                                if ((Input::has('payolceradi') || Input::has('payolceradet') || Input::has('payolcerissue') || Input::has('payolcerdetay')) && $uruntur == '6')
                                { //payolcer urun bilgisi var
                                    $payolcerbilgi = new EdestekSistemUrun;
                                    $payolcerbilgi->edestekurun_id = 6;
                                    if (Input::has('payolceradi')) {
                                        $payolcerbilgi->adi = Input::get('payolceradi');
                                    }
                                    if (Input::has('payolceradet')) {
                                        $payolcerbilgi->adet = Input::get('payolceradet');
                                    }
                                    if (Input::has('payolcerissue')) {
                                        $payolcerbilgi->issue = Input::get('payolcerissue');
                                    }
                                    if (Input::has('payolcerdetay')) {
                                        $payolcerbilgi->detay = Input::get('payolcerdetay');
                                    }
                                    $payolcerbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $payolcerbilgi->id;
                                }
                                if ((Input::has('terminaladi') || Input::has('terminaladet') || Input::has('terminalissue') || Input::has('terminaldetay')) && $uruntur == '7')
                                { //el terminali urun bilgisi var
                                    $terminalbilgi = new EdestekSistemUrun;
                                    $terminalbilgi->edestekurun_id = 7;
                                    if (Input::has('terminaladi')) {
                                        $terminalbilgi->adi = Input::get('terminaladi');
                                    }
                                    if (Input::has('terminaladet')) {
                                        $terminalbilgi->adet = Input::get('terminaladet');
                                    }
                                    if (Input::has('terminalissue')) {
                                        $terminalbilgi->issue = Input::get('terminalissue');
                                    }
                                    if (Input::has('terminaldetay')) {
                                        $terminalbilgi->detay = Input::get('terminaldetay');
                                    }
                                    $terminalbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $terminalbilgi->id;
                                }
                                if ((Input::has('kioskadi') || Input::has('kioskadet') || Input::has('kioskissue') || Input::has('kioskdetay')) && $uruntur == '8')
                                { //kiosk urun bilgisi var
                                    $kioskbilgi = new EdestekSistemUrun;
                                    $kioskbilgi->edestekurun_id = 8;
                                    if (Input::has('kioskadi')) {
                                        $kioskbilgi->adi = Input::get('kioskadi');
                                    }
                                    if (Input::has('kioskadet')) {
                                        $kioskbilgi->adet = Input::get('kioskadet');
                                    }
                                    if (Input::has('kioskissue')) {
                                        $kioskbilgi->issue = Input::get('kioskissue');
                                    }
                                    if (Input::has('kioskdetay')) {
                                        $kioskbilgi->detay = Input::get('kioskdetay');
                                    }
                                    $kioskbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $kioskbilgi->id;
                                }
                                if ((Input::has('klimaadi') || Input::has('klimaadet') || Input::has('klimaissue') || Input::has('klimadetay')) && $uruntur == '9')
                                { //klima kontrol cihazı urun bilgisi var
                                    $klimabilgi = new EdestekSistemUrun;
                                    $klimabilgi->edestekurun_id = 9;
                                    if (Input::has('klimaadi')) {
                                        $klimabilgi->adi = Input::get('klimaadi');
                                    }
                                    if (Input::has('klimaadet')) {
                                        $klimabilgi->adet = Input::get('klimaadet');
                                    }
                                    if (Input::has('klimaissue')) {
                                        $klimabilgi->issue = Input::get('klimaissue');
                                    }
                                    if (Input::has('klimadetay')) {
                                        $klimabilgi->detay = Input::get('klimadetay');
                                    }
                                    $klimabilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $klimabilgi->id;
                                }
                                if ((Input::has('digeradi') || Input::has('digeradet') || Input::has('digerissue') || Input::has('digerdetay')) && $uruntur == '10')
                                { //diger sayac urun bilgisi var
                                    $digerbilgi = new EdestekSistemUrun;
                                    $digerbilgi->edestekurun_id = 10;
                                    if (Input::has('digeradi')) {
                                        $digerbilgi->adi = Input::get('digeradi');
                                    }
                                    if (Input::has('digeradet')) {
                                        $digerbilgi->adet = Input::get('digeradet');
                                    }
                                    if (Input::has('digerissue')) {
                                        $digerbilgi->issue = Input::get('digerissue');
                                    }
                                    if (Input::has('digerdetay')) {
                                        $digerbilgi->detay = Input::get('digerdetay');
                                    }
                                    $digerbilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $digerbilgi->id;
                                }
                                if ((Input::has('icuniteadi') || Input::has('icuniteadet') || Input::has('icuniteissue') || Input::has('icunitedetay')) && $uruntur == '11')
                                { //klima kontrol cihazı urun bilgisi var
                                    $icunitebilgi = new EdestekSistemUrun;
                                    $icunitebilgi->edestekurun_id = 11;
                                    if (Input::has('icuniteadi')) {
                                        $icunitebilgi->adi = Input::get('icuniteadi');
                                    }
                                    if (Input::has('icuniteadet')) {
                                        $icunitebilgi->adet = Input::get('icuniteadet');
                                    }
                                    if (Input::has('icuniteissue')) {
                                        $icunitebilgi->issue = Input::get('icuniteissue');
                                    }
                                    if (Input::has('icunitedetay')) {
                                        $icunitebilgi->detay = Input::get('icunitedetay');
                                    }
                                    $icunitebilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $icunitebilgi->id;
                                }
                                if ((Input::has('kartokuyucuadi') || Input::has('kartokuyucuadet') || Input::has('kartokuyucucesit') || Input::has('kartokuyucudetay')) && $uruntur == '12')
                                { //klima kontrol cihazı urun bilgisi var
                                    $kartokuyucubilgi = new EdestekSistemUrun;
                                    $kartokuyucubilgi->edestekurun_id = 12;
                                    if (Input::has('kartokuyucuadi')) {
                                        $kartokuyucubilgi->adi = Input::get('kartokuyucuadi');
                                    }
                                    if (Input::has('kartokuyucuadet')) {
                                        $kartokuyucubilgi->adet = Input::get('kartokuyucuadet');
                                    }
                                    if (Input::has('kartokuyucucesit')) {
                                        $kartokuyucubilgi->issue = Input::get('kartokuyucucesit');
                                    }
                                    if (Input::has('kartokuyucudetay')) {
                                        $kartokuyucubilgi->detay = Input::get('kartokuyucudetay');
                                    }
                                    $kartokuyucubilgi->save();
                                    $urunler .= ($urunler == "" ? "" : ",") . $kartokuyucubilgi->id;
                                }
                            }
                        }
                    }
                    $sistembilgi->urunler = $urunler;
                    $programlar = "";
                    if ($sistembilgi->programlar) //program onceden varsa
                    {
                        $programsecilen = explode(',', $sistembilgi->programlar);
                        foreach ($programsecilen as $program) {
                            $programbilgi = EdestekSistemProgram::find($program);
                            if ($programbilgi->edestekprogram_id == 1) {
                                if ((Input::has('epicversiyon') || Input::has('epickullanici') || Input::has('epicsifre') || Input::has('epicmanas') || Input::has('epicdiger')) && $this->programkontrol(1, Input::get('options4')))
                                { //epic programı bilgisi var
                                    if (Input::has('epicversiyon')) {
                                        $programbilgi->versiyon = Input::get('epicversiyon');
                                    } else {
                                        $programbilgi->versiyon = NULL;
                                    }
                                    if (Input::has('epickullanici')) {
                                        $programbilgi->kullaniciadi = Input::get('epickullanici');
                                    } else {
                                        $programbilgi->kullaniciadi = NULL;
                                    }
                                    if (Input::has('epicsifre')) {
                                        $programbilgi->sifre = Input::get('epicsifre');
                                    } else {
                                        $programbilgi->sifre = NULL;
                                    }
                                    if (Input::has('epicmanas')) {
                                        $programbilgi->yetkilisifre = Input::get('epicmanas');
                                    } else {
                                        $programbilgi->yetkilisifre = NULL;
                                    }
                                    if (Input::has('epicdiger')) {
                                        $programbilgi->diger = Input::get('epicdiger');
                                    } else {
                                        $programbilgi->diger = NULL;
                                    }
                                    $programbilgi->save();
                                    $programlar .= ($programlar == "" ? "" : ",") . $programbilgi->id;
                                } else {
                                    $programbilgi->delete();
                                }
                            } else if ($programbilgi->edestekprogram_id == 2) {
                                if ((Input::has('smartversiyon') || Input::has('smartkullanici') || Input::has('smartsifre') || Input::has('smartmanas') || Input::has('smartdiger')) && $this->programkontrol(2, Input::get('options4')))
                                { //epicsmart programı bilgisi var
                                    if (Input::has('smartversiyon')) {
                                        $programbilgi->versiyon = Input::get('smartversiyon');
                                    } else {
                                        $programbilgi->versiyon = NULL;
                                    }
                                    if (Input::has('smartkullanici')) {
                                        $programbilgi->kullaniciadi = Input::get('smartkullanici');
                                    } else {
                                        $programbilgi->versiyon = NULL;
                                    }
                                    if (Input::has('smartsifre')) {
                                        $programbilgi->sifre = Input::get('smartsifre');
                                    } else {
                                        $programbilgi->versiyon = NULL;
                                    }
                                    if (Input::has('smartmanas')) {
                                        $programbilgi->yetkilisifre = Input::get('smartmanas');
                                    } else {
                                        $programbilgi->versiyon = NULL;
                                    }
                                    if (Input::has('smartdiger')) {
                                        $programbilgi->diger = Input::get('smartdiger');
                                    } else {
                                        $programbilgi->versiyon = NULL;
                                    }
                                    $programbilgi->save();
                                    $programlar .= ($programlar == "" ? "" : ",") . $programbilgi->id;
                                } else {
                                    $programbilgi->delete();
                                }
                            } else if ($programbilgi->edestekprogram_id == 3) {
                                if ((Input::has('4comversiyon') || Input::has('4comkullanici') || Input::has('4comsifre') || Input::has('4compower') || Input::has('4comdiger')) && $this->programkontrol(3, Input::get('options4')))
                                { //4com programı bilgisi var
                                    if (Input::has('4comversiyon')) {
                                        $programbilgi->versiyon = Input::get('4comversiyon');
                                    } else {
                                        $programbilgi->versiyon = NULL;
                                    }
                                    if (Input::has('4comkullanici')) {
                                        $programbilgi->kullaniciadi = Input::get('4comkullanici');
                                    } else {
                                        $programbilgi->versiyon = NULL;
                                    }
                                    if (Input::has('4comsifre')) {
                                        $programbilgi->sifre = Input::get('4comsifre');
                                    } else {
                                        $programbilgi->versiyon = NULL;
                                    }
                                    if (Input::has('4compower')) {
                                        $programbilgi->yetkilisifre = Input::get('4compower');
                                    } else {
                                        $programbilgi->versiyon = NULL;
                                    }
                                    if (Input::has('4comdiger')) {
                                        $programbilgi->diger = Input::get('4comdiger');
                                    } else {
                                        $programbilgi->versiyon = NULL;
                                    }
                                    $programbilgi->save();
                                    $programlar .= ($programlar == "" ? "" : ",") . $programbilgi->id;
                                } else {
                                    $programbilgi->delete();
                                }
                            } else if ($programbilgi->edestekprogram_id == 4) {
                                if ((Input::has('options5') || Input::has('options6') || Input::has('options7') || Input::has('options8') || Input::has('entegrasyondiger')) && $this->programkontrol(4, Input::get('options4')))
                                { //entegrasyon bilgisi var
                                    $entegrasyonbilgi = new EdestekSistemProgram;
                                    $entegrasyonbilgi->edestekprogram_id = 4;
                                    if (Input::has('options5')) {
                                        $programbilgi->edestekentegrasyonfirma_id = Input::get('options5');
                                    } else {
                                        $programbilgi->edestekentegrasyonfirma_id = NULL;
                                    }
                                    if (Input::has('options6')) {
                                        $programbilgi->edestekentegrasyontip_id = Input::get('options6');
                                    } else {
                                        $programbilgi->edestekentegrasyontip_id = NULL;
                                    }
                                    if (Input::has('options7')) {
                                        $programbilgi->edestekentegrasyonprogram_id = Input::get('options7');
                                    } else {
                                        $programbilgi->edestekentegrasyonprogram_id = NULL;
                                    }
                                    if (Input::has('options8')) {
                                        $programbilgi->edestekentegrasyonversiyon_id = Input::get('options8');
                                    } else {
                                        $programbilgi->edestekentegrasyonversiyon_id = NULL;
                                    }
                                    if (Input::has('entegrasyondiger')) {
                                        $programbilgi->diger = Input::get('entegrasyondiger');
                                    } else {
                                        $programbilgi->diger = NULL;
                                    }
                                    $programbilgi->save();
                                    $programlar .= ($programlar == "" ? "" : ",") . $programbilgi->id;
                                } else {
                                    $programbilgi->delete();
                                }
                            } else if ($programbilgi->edestekprogram_id == 5) {
                                if ((Input::has('ipmanversiyon') || Input::has('ipmankullanici') || Input::has('ipmansifre') || Input::has('ipmanmanas') || Input::has('ipmandiger')) && $this->programkontrol(5, Input::get('options4')))
                                { //ipman programı bilgisi var
                                    if (Input::has('ipmanversiyon')) {
                                        $programbilgi->versiyon = Input::get('ipmanversiyon');
                                    } else {
                                        $programbilgi->versiyon = NULL;
                                    }
                                    if (Input::has('ipmankullanici')) {
                                        $programbilgi->kullaniciadi = Input::get('ipmankullanici');
                                    } else {
                                        $programbilgi->versiyon = NULL;
                                    }
                                    if (Input::has('ipmansifre')) {
                                        $programbilgi->sifre = Input::get('ipmansifre');
                                    } else {
                                        $programbilgi->versiyon = NULL;
                                    }
                                    if (Input::has('ipmanmanas')) {
                                        $programbilgi->yetkilisifre = Input::get('ipmanmanas');
                                    } else {
                                        $programbilgi->versiyon = NULL;
                                    }
                                    if (Input::has('ipmandiger')) {
                                        $programbilgi->diger = Input::get('ipmandiger');
                                    } else {
                                        $programbilgi->versiyon = NULL;
                                    }
                                    $programbilgi->save();
                                    $programlar .= ($programlar == "" ? "" : ",") . $programbilgi->id;
                                } else {
                                    $programbilgi->delete();
                                }
                            }
                        }
                        if (Input::has('options4')) {
                            $programturler = Input::get('options4');
                            foreach ($programturler as $programtur) {
                                if (!$this->programkontrolyeni($programtur, $programsecilen)) // yeni ekleme ise
                                {
                                    if ((Input::has('epicversiyon') || Input::has('epickullanici') || Input::has('epicsifre') || Input::has('epicmanas') || Input::has('epicdiger')) && $programtur == '1')
                                    { //epic programı bilgisi var
                                        $epicbilgi = new EdestekSistemProgram;
                                        $epicbilgi->edestekprogram_id = 1;
                                        if (Input::has('epicversiyon')) {
                                            $epicbilgi->versiyon = Input::get('epicversiyon');
                                        }
                                        if (Input::has('epickullanici')) {
                                            $epicbilgi->kullaniciadi = Input::get('epickullanici');
                                        }
                                        if (Input::has('epicsifre')) {
                                            $epicbilgi->sifre = Input::get('epicsifre');
                                        }
                                        if (Input::has('epicmanas')) {
                                            $epicbilgi->yetkilisifre = Input::get('epicmanas');
                                        }
                                        if (Input::has('epicdiger')) {
                                            $epicbilgi->diger = Input::get('epicdiger');
                                        }
                                        $epicbilgi->save();
                                        $programlar .= ($programlar == "" ? "" : ",") . $epicbilgi->id;
                                    }
                                    if ((Input::has('smartversiyon') || Input::has('smartkullanici') || Input::has('smartsifre') || Input::has('smartmanas') || Input::has('smartdiger')) && $programtur == '2')
                                    { //epicsmart programı bilgisi var
                                        $smartbilgi = new EdestekSistemProgram;
                                        $smartbilgi->edestekprogram_id = 2;
                                        if (Input::has('smartversiyon')) {
                                            $smartbilgi->versiyon = Input::get('smartversiyon');
                                        }
                                        if (Input::has('smartkullanici')) {
                                            $smartbilgi->kullaniciadi = Input::get('smartkullanici');
                                        }
                                        if (Input::has('smartsifre')) {
                                            $smartbilgi->sifre = Input::get('smartsifre');
                                        }
                                        if (Input::has('smartmanas')) {
                                            $smartbilgi->yetkilisifre = Input::get('smartmanas');
                                        }
                                        if (Input::has('smartdiger')) {
                                            $smartbilgi->diger = Input::get('smartdiger');
                                        }
                                        $smartbilgi->save();
                                        $programlar .= ($programlar == "" ? "" : ",") . $smartbilgi->id;
                                    }
                                    if ((Input::has('4comversiyon') || Input::has('4comkullanici') || Input::has('4comsifre') || Input::has('4compower') || Input::has('4comdiger')) && $programtur == '3')
                                    { //4com programı bilgisi var
                                        $dcombilgi = new EdestekSistemProgram;
                                        $dcombilgi->edestekprogram_id = 3;
                                        if (Input::has('4comversiyon')) {
                                            $dcombilgi->versiyon = Input::get('4comversiyon');
                                        }
                                        if (Input::has('4comkullanici')) {
                                            $dcombilgi->kullaniciadi = Input::get('4comkullanici');
                                        }
                                        if (Input::has('4comsifre')) {
                                            $dcombilgi->sifre = Input::get('4comsifre');
                                        }
                                        if (Input::has('4compower')) {
                                            $dcombilgi->yetkilisifre = Input::get('4compower');
                                        }
                                        if (Input::has('4comdiger')) {
                                            $dcombilgi->diger = Input::get('4comdiger');
                                        }
                                        $dcombilgi->save();
                                        $programlar .= ($programlar == "" ? "" : ",") . $dcombilgi->id;
                                    }
                                    if ((Input::has('options5') || Input::has('options6') || Input::has('options7') || Input::has('options8') || Input::has('entegrasyondiger')) && $programtur == '4')
                                    { //entegrasyon bilgisi var
                                        $entegrasyonbilgi = new EdestekSistemProgram;
                                        $entegrasyonbilgi->edestekprogram_id = 4;
                                        if (Input::has('options5')) {
                                            $entegrasyonbilgi->edestekentegrasyonfirma_id = Input::get('options5');
                                        }
                                        if (Input::has('options6')) {
                                            $entegrasyonbilgi->edestekentegrasyontip_id = Input::get('options6');
                                        }
                                        if (Input::has('options7')) {
                                            $entegrasyonbilgi->edestekentegrasyonprogram_id = Input::get('options7');
                                        }
                                        if (Input::has('options8')) {
                                            $entegrasyonbilgi->edestekentegrasyonversiyon_id = Input::get('options8');
                                        }
                                        if (Input::has('entegrasyondiger')) {
                                            $entegrasyonbilgi->diger = Input::get('entegrasyondiger');
                                        }
                                        $entegrasyonbilgi->save();
                                        $programlar .= ($programlar == "" ? "" : ",") . $entegrasyonbilgi->id;
                                    }
                                    if ((Input::has('ipmanversiyon') || Input::has('ipmankullanici') || Input::has('ipmansifre') || Input::has('ipmanmanas') || Input::has('ipmandiger')) && $programtur == '5')
                                    { //ipman programı bilgisi var
                                        $ipmanbilgi = new EdestekSistemProgram;
                                        $ipmanbilgi->edestekprogram_id = 5;
                                        if (Input::has('ipmanversiyon')) {
                                            $ipmanbilgi->versiyon =  Input::get('ipmanversiyon');
                                        }
                                        if (Input::has('ipmankullanici')) {
                                            $ipmanbilgi->kullaniciadi = Input::get('ipmankullanici');
                                        }
                                        if (Input::has('ipmansifre')) {
                                            $ipmanbilgi->sifre = Input::get('ipmansifre');
                                        }
                                        if (Input::has('ipmanmanas')) {
                                            $ipmanbilgi->yetkilisifre = Input::get('ipmanmanas');
                                        }
                                        if (Input::has('ipmandiger')) {
                                            $ipmanbilgi->diger = Input::get('ipmandiger');
                                        }
                                        $ipmanbilgi->save();
                                        $programlar .= ($programlar == "" ? "" : ",") . $ipmanbilgi->id;
                                    }
                                }
                            }
                        }
                    } else {
                        if (Input::has('options4')) {
                            $programturler = Input::get('options4');
                            foreach ($programturler as $programtur) {
                                if ((Input::has('epicversiyon') || Input::has('epickullanici') || Input::has('epicsifre') || Input::has('epicmanas') || Input::has('epicdiger')) && $programtur == '1')
                                { //epic programı bilgisi var
                                    $epicbilgi = new EdestekSistemProgram;
                                    $epicbilgi->edestekprogram_id = 1;
                                    if (Input::has('epicversiyon')) {
                                        $epicbilgi->versiyon = Input::get('epicversiyon');
                                    }
                                    if (Input::has('epickullanici')) {
                                        $epicbilgi->kullaniciadi = Input::get('epickullanici');
                                    }
                                    if (Input::has('epicsifre')) {
                                        $epicbilgi->sifre = Input::get('epicsifre');
                                    }
                                    if (Input::has('epicmanas')) {
                                        $epicbilgi->yetkilisifre = Input::get('epicmanas');
                                    }
                                    if (Input::has('epicdiger')) {
                                        $epicbilgi->diger = Input::get('epicdiger');
                                    }
                                    $epicbilgi->save();
                                    $programlar .= ($programlar == "" ? "" : ",") . $epicbilgi->id;
                                }
                                if ((Input::has('smartversiyon') || Input::has('smartkullanici') || Input::has('smartsifre') || Input::has('smartmanas') || Input::has('smartdiger')) && $programtur == '2')
                                { //epicsmart programı bilgisi var
                                    $smartbilgi = new EdestekSistemProgram;
                                    $smartbilgi->edestekprogram_id = 2;
                                    if (Input::has('smartversiyon')) {
                                        $smartbilgi->versiyon = Input::get('smartversiyon');
                                    }
                                    if (Input::has('smartkullanici')) {
                                        $smartbilgi->kullaniciadi = Input::get('smartkullanici');
                                    }
                                    if (Input::has('smartsifre')) {
                                        $smartbilgi->sifre = Input::get('smartsifre');
                                    }
                                    if (Input::has('smartmanas')) {
                                        $smartbilgi->yetkilisifre = Input::get('smartmanas');
                                    }
                                    if (Input::has('smartdiger')) {
                                        $smartbilgi->diger = Input::get('smartdiger');
                                    }
                                    $smartbilgi->save();
                                    $programlar .= ($programlar == "" ? "" : ",") . $smartbilgi->id;
                                }
                                if ((Input::has('4comversiyon') || Input::has('4comkullanici') || Input::has('4comsifre') || Input::has('4compower') || Input::has('4comdiger')) && $programtur == '3')
                                { //4com programı bilgisi var
                                    $dcombilgi = new EdestekSistemProgram;
                                    $dcombilgi->edestekprogram_id = 3;
                                    if (Input::has('4comversiyon')) {
                                        $dcombilgi->versiyon = Input::get('4comversiyon');
                                    }
                                    if (Input::has('4comkullanici')) {
                                        $dcombilgi->kullaniciadi = Input::get('4comkullanici');
                                    }
                                    if (Input::has('4comsifre')) {
                                        $dcombilgi->sifre = Input::get('4comsifre');
                                    }
                                    if (Input::has('4compower')) {
                                        $dcombilgi->yetkilisifre = Input::get('4compower');
                                    }
                                    if (Input::has('4comdiger')) {
                                        $dcombilgi->diger = Input::get('4comdiger');
                                    }
                                    $dcombilgi->save();
                                    $programlar .= ($programlar == "" ? "" : ",") . $dcombilgi->id;
                                }
                                if ((Input::has('options5') || Input::has('options6') || Input::has('options7') || Input::has('options8') || Input::has('entegrasyondiger')) && $programtur == '4')
                                { //entegrasyon bilgisi var
                                    $entegrasyonbilgi = new EdestekSistemProgram;
                                    $entegrasyonbilgi->edestekprogram_id = 4;
                                    if (Input::has('options5')) {
                                        $entegrasyonbilgi->edestekentegrasyonfirma_id = Input::get('options5');
                                    }
                                    if (Input::has('options6')) {
                                        $entegrasyonbilgi->edestekentegrasyontip_id = Input::get('options6');
                                    }
                                    if (Input::has('options7')) {
                                        $entegrasyonbilgi->edestekentegrasyonprogram_id = Input::get('options7');
                                    }
                                    if (Input::has('options8')) {
                                        $entegrasyonbilgi->edestekentegrasyonversiyon_id = Input::get('options8');
                                    }
                                    if (Input::has('entegrasyondiger')) {
                                        $entegrasyonbilgi->diger = Input::get('entegrasyondiger');
                                    }
                                    $entegrasyonbilgi->save();
                                    $programlar .= ($programlar == "" ? "" : ",") . $entegrasyonbilgi->id;
                                }
                                if ((Input::has('ipmanversiyon') || Input::has('ipmankullanici') || Input::has('ipmansifre') || Input::has('ipmanmanas') || Input::has('ipmandiger')) && $programtur == '5')
                                { //ipman programı bilgisi var
                                    $ipmanbilgi = new EdestekSistemProgram;
                                    $ipmanbilgi->edestekprogram_id = 5;
                                    if (Input::has('ipmanversiyon')) {
                                        $ipmanbilgi->versiyon =  Input::get('ipmanversiyon');
                                    }
                                    if (Input::has('ipmankullanici')) {
                                        $ipmanbilgi->kullaniciadi = Input::get('ipmankullanici');
                                    }
                                    if (Input::has('ipmansifre')) {
                                        $ipmanbilgi->sifre = Input::get('ipmansifre');
                                    }
                                    if (Input::has('ipmanmanas')) {
                                        $ipmanbilgi->yetkilisifre = Input::get('ipmanmanas');
                                    }
                                    if (Input::has('ipmandiger')) {
                                        $ipmanbilgi->diger = Input::get('ipmandiger');
                                    }
                                    $ipmanbilgi->save();
                                    $programlar .= ($programlar == "" ? "" : ",") . $ipmanbilgi->id;
                                }
                            }
                        }
                    }
                    $sistembilgi->programlar = $programlar;
                    $veritabanlari = "";
                    if ($sistembilgi->veritabanlari) //veritabanı onceden varsa
                    {
                        $veritabanisecilen = explode(',', $sistembilgi->veritabanlari);
                        foreach ($veritabanisecilen as $veritabani) {
                            $databasebilgi = EdestekSistemDatabase::find($veritabani);
                            if ($databasebilgi->edestekdatabase_id == 1) {
                                if ((Input::has('oracleversiyon') || Input::has('oracleveritabani') || Input::has('oraclekullanici') || Input::has('oraclesifre') || Input::has('oraclediger')) && $this->programkontrol(1, Input::get('options4')))
                                { // oracle bilgisi var
                                    if (Input::has('oracleversiyon')) {
                                        $databasebilgi->versiyon = Input::get('oracleversiyon');
                                    } else {
                                        $databasebilgi->versiyon = NULL;
                                    }
                                    if (Input::has('oraclekullanici')) {
                                        $databasebilgi->kullaniciadi = Input::get('oraclekullanici');
                                    } else {
                                        $databasebilgi->kullaniciadi = NULL;
                                    }
                                    if (Input::has('oraclesifre')) {
                                        $databasebilgi->sifre = Input::get('oraclesifre');
                                    } else {
                                        $databasebilgi->sifre = NULL;
                                    }
                                    if (Input::has('oracleveritabani')) {
                                        $databasebilgi->adi = Input::get('oracleveritabani');
                                    } else {
                                        $databasebilgi->adi = NULL;
                                    }
                                    if (Input::has('oraclediger')) {
                                        $databasebilgi->diger = Input::get('oraclediger');
                                    } else {
                                        $databasebilgi->diger = NULL;
                                    }
                                    $databasebilgi->save();
                                    $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $databasebilgi->id;
                                } else {
                                    $databasebilgi->delete();
                                }
                            } else if ($databasebilgi->edestekdatabase_id == 2) {
                                if ((Input::has('sqlversiyon') || Input::has('sqlveritabani') || Input::has('sqlkullanici') || Input::has('sqlsifre') || Input::has('sqldiger')) && $this->programkontrol(2, Input::get('options4')))
                                {  //sql server bilgisi var
                                    if (Input::has('sqlversiyon')) {
                                        $databasebilgi->versiyon = Input::get('sqlversiyon');
                                    } else {
                                        $databasebilgi->versiyon = NULL;
                                    }
                                    if (Input::has('sqlkullanici')) {
                                        $databasebilgi->kullaniciadi = Input::get('sqlkullanici');
                                    } else {
                                        $databasebilgi->kullaniciadi = NULL;
                                    }
                                    if (Input::has('sqlsifre')) {
                                        $databasebilgi->sifre = Input::get('sqlsifre');
                                    } else {
                                        $databasebilgi->sifre = NULL;
                                    }
                                    if (Input::has('sqlveritabani')) {
                                        $databasebilgi->adi = Input::get('sqlveritabani');
                                    } else {
                                        $databasebilgi->adi = NULL;
                                    }
                                    if (Input::has('sqldiger')) {
                                        $databasebilgi->diger = Input::get('sqldiger');
                                    } else {
                                        $databasebilgi->diger = NULL;
                                    }
                                    $databasebilgi->save();
                                    $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $databasebilgi->id;
                                } else {
                                    $databasebilgi->delete();
                                }
                            } else if ($databasebilgi->edestekdatabase_id == 3) {
                                if ((Input::has('mysqlversiyon') || Input::has('mysqlveritabani') || Input::has('mysqlkullanici') || Input::has('mysqlsifre') || Input::has('mysqldiger')) && $this->programkontrol(3, Input::get('options4')))
                                { //mysql bilgisi var
                                    if (Input::has('mysqlversiyon')) {
                                        $databasebilgi->versiyon = Input::get('mysqlversiyon');
                                    }
                                    if (Input::has('mysqlkullanici')) {
                                        $databasebilgi->kullaniciadi = Input::get('mysqlkullanici');
                                    }
                                    if (Input::has('mysqlsifre')) {
                                        $databasebilgi->sifre = Input::get('mysqlsifre');
                                    }
                                    if (Input::has('mysqlveritabani')) {
                                        $databasebilgi->adi = Input::get('mysqlveritabani');
                                    }
                                    if (Input::has('mysqldiger')) {
                                        $databasebilgi->diger = Input::get('mysqldiger');
                                    }
                                    $databasebilgi->save();
                                    $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $databasebilgi->id;
                                } else {
                                    $databasebilgi->delete();
                                }
                            } else if ($databasebilgi->edestekdatabase_id == 5) {
                                if ((Input::has('ipmansqlversiyon') || Input::has('ipmanveritabani') || Input::has('ipmansqlkullanici') || Input::has('ipmansqlsifre') || Input::has('ipmansqldiger')) && $this->programkontrol(5, Input::get('options4')))
                                {  //sql server bilgisi var
                                    if (Input::has('ipmansqlversiyon')) {
                                        $databasebilgi->versiyon = Input::get('ipmansqlversiyon');
                                    } else {
                                        $databasebilgi->versiyon = NULL;
                                    }
                                    if (Input::has('ipmansqlkullanici')) {
                                        $databasebilgi->kullaniciadi = Input::get('ipmansqlkullanici');
                                    } else {
                                        $databasebilgi->kullaniciadi = NULL;
                                    }
                                    if (Input::has('ipmansqlsifre')) {
                                        $databasebilgi->sifre = Input::get('ipmansqlsifre');
                                    } else {
                                        $databasebilgi->sifre = NULL;
                                    }
                                    if (Input::has('ipmanveritabani')) {
                                        $databasebilgi->adi = Input::get('ipmanveritabani');
                                    } else {
                                        $databasebilgi->adi = NULL;
                                    }
                                    if (Input::has('ipmansqldiger')) {
                                        $databasebilgi->diger = Input::get('ipmansqldiger');
                                    } else {
                                        $databasebilgi->diger = NULL;
                                    }
                                    $databasebilgi->save();
                                    $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $databasebilgi->id;
                                } else {
                                    $databasebilgi->delete();
                                }
                            }
                        }
                        if (Input::has('options4')) {
                            $programturler = Input::get('options4');
                            foreach ($programturler as $programtur) {
                                if (!$this->veritabanikontrolyeni($programtur, $veritabanisecilen) && $programtur != "4") // yeni ekleme ise
                                {
                                    if ((Input::has('oracleversiyon') || Input::has('oracleveritabani') || Input::has('oraclekullanici') || Input::has('oraclesifre') || Input::has('oraclediger')) && $programtur == '1')
                                    { //epic programı bilgisi var
                                        $oraclebilgi = new EdestekSistemDatabase;
                                        $oraclebilgi->edestekdatabase_id = 1;
                                        if (Input::has('oracleversiyon')) {
                                            $oraclebilgi->versiyon = Input::get('oracleversiyon');
                                        }
                                        if (Input::has('oraclekullanici')) {
                                            $oraclebilgi->kullaniciadi = Input::get('oraclekullanici');;
                                        }
                                        if (Input::has('oraclesifre')) {
                                            $oraclebilgi->sifre = Input::get('oraclesifre');;
                                        }
                                        if (Input::has('oracleveritabani')) {
                                            $oraclebilgi->adi = Input::get('oracleveritabani');
                                        }
                                        if (Input::has('oraclediger')) {
                                            $oraclebilgi->diger = Input::get('oraclediger');
                                        }
                                        $oraclebilgi->save();
                                        $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $oraclebilgi->id;
                                    }
                                    if ((Input::has('sqlversiyon') || Input::has('sqlveritabani') || Input::has('sqlkullanici') || Input::has('sqlsifre') || Input::has('sqldiger')) && $programtur == '2')
                                    {  //sql server bilgisi var
                                        $sqlbilgi = new EdestekSistemDatabase;
                                        $sqlbilgi->edestekdatabase_id = 2;
                                        if (Input::has('sqlversiyon')) {
                                            $sqlbilgi->versiyon = Input::get('sqlversiyon');
                                        }
                                        if (Input::has('sqlkullanici')) {
                                            $sqlbilgi->kullaniciadi = Input::get('sqlkullanici');
                                        }
                                        if (Input::has('sqlsifre')) {
                                            $sqlbilgi->sifre = Input::get('sqlsifre');
                                        }
                                        if (Input::has('sqlveritabani')) {
                                            $sqlbilgi->adi = Input::get('sqlveritabani');
                                        }
                                        if (Input::has('sqldiger')) {
                                            $sqlbilgi->diger = Input::get('sqldiger');
                                        }
                                        $sqlbilgi->save();
                                        $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $sqlbilgi->id;
                                    }
                                    if ((Input::has('mysqlversiyon') || Input::has('mysqlveritabani') || Input::has('mysqlkullanici') || Input::has('mysqlsifre') || Input::has('mysqldiger')) && $programtur == '3')
                                    { //mysql bilgisi var
                                        $mysqlbilgi = new EdestekSistemDatabase;
                                        $mysqlbilgi->edestekdatabase_id = 3;
                                        if (Input::has('mysqlversiyon')) {
                                            $mysqlbilgi->versiyon = Input::get('mysqlversiyon');
                                        }
                                        if (Input::has('mysqlkullanici')) {
                                            $mysqlbilgi->kullaniciadi = Input::get('mysqlkullanici');
                                        }
                                        if (Input::has('mysqlsifre')) {
                                            $mysqlbilgi->sifre = Input::get('mysqlsifre');
                                        }
                                        if (Input::has('mysqlveritabani')) {
                                            $mysqlbilgi->adi = Input::get('mysqlveritabani');
                                        }
                                        if (Input::has('mysqldiger')) {
                                            $mysqlbilgi->diger = Input::get('mysqldiger');
                                        }
                                        $mysqlbilgi->save();
                                        $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $mysqlbilgi->id;
                                    }
                                    if ((Input::has('ipmansqlversiyon') || Input::has('ipmanveritabani') || Input::has('ipmansqlkullanici') || Input::has('ipmansqlsifre') || Input::has('ipmansqldiger')) && $programtur == '5')
                                    {  //sql server bilgisi var
                                        $sqlbilgi = new EdestekSistemDatabase;
                                        $sqlbilgi->edestekdatabase_id = 5;
                                        if (Input::has('ipmansqlversiyon')) {
                                            $sqlbilgi->versiyon = Input::get('ipmansqlversiyon');
                                        }
                                        if (Input::has('ipmansqlkullanici')) {
                                            $sqlbilgi->kullaniciadi = Input::get('ipmansqlkullanici');
                                        }
                                        if (Input::has('ipmansqlsifre')) {
                                            $sqlbilgi->sifre = Input::get('ipmansqlsifre');
                                        }
                                        if (Input::has('ipmanveritabani')) {
                                            $sqlbilgi->adi = Input::get('ipmaneritabani');
                                        }
                                        if (Input::has('ipmansqldiger')) {
                                            $sqlbilgi->diger = Input::get('ipmansqldiger');
                                        }
                                        $sqlbilgi->save();
                                        $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $sqlbilgi->id;
                                    }
                                }
                            }
                        }
                    } else {
                        if (Input::has('options4')) {
                            $programturler = Input::get('options4');
                            foreach ($programturler as $programtur) {
                                if ((Input::has('oracleversiyon') || Input::has('oracleveritabani') || Input::has('oraclekullanici') || Input::has('oraclesifre') || Input::has('oraclediger')) && $programtur == '1')
                                { //epic programı bilgisi var
                                    $oraclebilgi = new EdestekSistemDatabase;
                                    $oraclebilgi->edestekdatabase_id = 1;
                                    if (Input::has('oracleversiyon')) {
                                        $oraclebilgi->versiyon = Input::get('oracleversiyon');
                                    }
                                    if (Input::has('oraclekullanici')) {
                                        $oraclebilgi->kullaniciadi = Input::get('oraclekullanici');;
                                    }
                                    if (Input::has('oraclesifre')) {
                                        $oraclebilgi->sifre = Input::get('oraclesifre');;
                                    }
                                    if (Input::has('oracleveritabani')) {
                                        $oraclebilgi->adi = Input::get('oracleveritabani');
                                    }
                                    if (Input::has('oraclediger')) {
                                        $oraclebilgi->diger = Input::get('oraclediger');
                                    }
                                    $oraclebilgi->save();
                                    $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $oraclebilgi->id;
                                }
                                if ((Input::has('sqlversiyon') || Input::has('sqlveritabani') || Input::has('sqlkullanici') || Input::has('sqlsifre') || Input::has('sqldiger')) && $programtur == '2')
                                {  //sql server bilgisi var
                                    $sqlbilgi = new EdestekSistemDatabase;
                                    $sqlbilgi->edestekdatabase_id = 2;
                                    if (Input::has('sqlversiyon')) {
                                        $sqlbilgi->versiyon = Input::get('sqlversiyon');
                                    }
                                    if (Input::has('sqlkullanici')) {
                                        $sqlbilgi->kullaniciadi = Input::get('sqlkullanici');
                                    }
                                    if (Input::has('sqlsifre')) {
                                        $sqlbilgi->sifre = Input::get('sqlsifre');
                                    }
                                    if (Input::has('sqlveritabani')) {
                                        $sqlbilgi->adi = Input::get('sqlveritabani');
                                    }
                                    if (Input::has('sqldiger')) {
                                        $sqlbilgi->diger = Input::get('sqldiger');
                                    }
                                    $sqlbilgi->save();
                                    $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $sqlbilgi->id;
                                }
                                if ((Input::has('mysqlversiyon') || Input::has('mysqlveritabani') || Input::has('mysqlkullanici') || Input::has('mysqlsifre') || Input::has('mysqldiger')) && $programtur == '3')
                                { //mysql bilgisi var
                                    $mysqlbilgi = new EdestekSistemDatabase;
                                    $mysqlbilgi->edestekdatabase_id = 3;
                                    if (Input::has('mysqlversiyon')) {
                                        $mysqlbilgi->versiyon = Input::get('mysqlversiyon');
                                    }
                                    if (Input::has('mysqlkullanici')) {
                                        $mysqlbilgi->kullaniciadi = Input::get('mysqlkullanici');
                                    }
                                    if (Input::has('mysqlsifre')) {
                                        $mysqlbilgi->sifre = Input::get('mysqlsifre');
                                    }
                                    if (Input::has('mysqlveritabani')) {
                                        $mysqlbilgi->adi = Input::get('mysqlveritabani');
                                    }
                                    if (Input::has('mysqldiger')) {
                                        $mysqlbilgi->diger = Input::get('mysqldiger');
                                    }
                                    $mysqlbilgi->save();
                                    $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $mysqlbilgi->id;
                                }
                                if ((Input::has('ipmansqlversiyon') || Input::has('ipmanveritabani') || Input::has('ipmansqlkullanici') || Input::has('ipmansqlsifre') || Input::has('ipmansqldiger')) && $programtur == '5')
                                {  //sql server bilgisi var
                                    $sqlbilgi = new EdestekSistemDatabase;
                                    $sqlbilgi->edestekdatabase_id = 5;
                                    if (Input::has('ipmansqlversiyon')) {
                                        $sqlbilgi->versiyon = Input::get('ipmansqlversiyon');
                                    }
                                    if (Input::has('ipmansqlkullanici')) {
                                        $sqlbilgi->kullaniciadi = Input::get('ipmansqlkullanici');
                                    }
                                    if (Input::has('ipmansqlsifre')) {
                                        $sqlbilgi->sifre = Input::get('ipmansqlsifre');
                                    }
                                    if (Input::has('ipmanveritabani')) {
                                        $sqlbilgi->adi = Input::get('ipmaneritabani');
                                    }
                                    if (Input::has('ipmansqldiger')) {
                                        $sqlbilgi->diger = Input::get('ipmansqldiger');
                                    }
                                    $sqlbilgi->save();
                                    $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $sqlbilgi->id;
                                }
                            }
                        }
                    }
                    $sistembilgi->veritabanlari = $veritabanlari;
                    if (Input::has('cariadi')) {
                        $sistembilgi->netsiscari_id = Input::get('cariadi');
                    }
                    if (Input::has('options2')) {
                        $sistembilgi->plasiyer_id = Input::get('options2');
                    } else {
                        $sistembilgi->plasiyer_id = NULL;
                    }
                    $sistembilgi->save();
                    $edestekmusteri->edesteksistembilgi_id = $sistembilgi->id;
                }
            } else if (Input::has('cariadi') || Input::has('options2') || Input::has('options3') || Input::has('options4'))
            { //sistem bilgisi var
                $sistembilgi = new EdestekSistemBilgi;
                $programlar = "";$veritabanlari = "";$urunler = "";
                if (Input::has('options3')) {
                    $urunturleri = Input::get('options3');
                    foreach ($urunturleri as $uruntur) {
                        $urun = new EdestekSistemUrun;
                        switch ($uruntur) {
                            case "1":
                                $urun->edestekurun_id = 1;
                                if (Input::has('suadi')) {
                                    $urun->adi = Input::get('suadi');
                                }
                                if (Input::has('suadet')) {
                                    $urun->adet = Input::get('suadet');
                                }
                                if (Input::has('suissue')) {
                                    $urun->issue = Input::get('suissue');
                                }
                                if (Input::has('sudetay')) {
                                    $urun->detay = Input::get('sudetay');
                                }
                                $urun->save();
                                $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                break;
                            case "2":
                                $urun->edestekurun_id = 2;
                                if (Input::has('sicakadi')) {
                                    $urun->adi = Input::get('sicakadi');
                                }
                                if (Input::has('sicakadet')) {
                                    $urun->adet = Input::get('sicakadet');
                                }
                                if (Input::has('sicakissue')) {
                                    $urun->issue = Input::get('sicakissue');
                                }
                                if (Input::has('sicakdetay')) {
                                    $urun->detay = Input::get('sicakdetay');
                                }
                                $urun->save();
                                $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                break;
                            case "3":
                                $urun->edestekurun_id = 3;
                                if (Input::has('elkadi')) {
                                    $urun->adi = Input::get('elkadi');
                                }
                                if (Input::has('elkadet')) {
                                    $urun->adet = Input::get('elkadet');
                                }
                                if (Input::has('elkissue')) {
                                    $urun->issue = Input::get('elkissue');
                                }
                                if (Input::has('elkdetay')) {
                                    $urun->detay = Input::get('elkdetay');
                                }
                                $urun->save();
                                $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                break;
                            case "4":
                                $urun->edestekurun_id = 4;
                                if (Input::has('gazadi')) {
                                    $urun->adi = Input::get('gazadi');
                                }
                                if (Input::has('gazadet')) {
                                    $urun->adet = Input::get('gazadet');
                                }
                                if (Input::has('gazissue')) {
                                    $urun->issue = Input::get('gazissue');
                                }
                                if (Input::has('gazdetay')) {
                                    $urun->detay = Input::get('gazdetay');
                                }
                                $urun->save();
                                $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                break;
                            case "5":
                                $urun->edestekurun_id = 5;
                                if (Input::has('isiadi')) {
                                    $urun->adi = Input::get('isiadi');
                                }
                                if (Input::has('isiadet')) {
                                    $urun->adet = Input::get('isiadet');
                                }
                                if (Input::has('isiissue')) {
                                    $urun->issue = Input::get('isiissue');
                                }
                                if (Input::has('isidetay')) {
                                    $urun->detay = Input::get('isidetay');
                                }
                                $urun->save();
                                $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                break;
                            case "6":
                                $urun->edestekurun_id = 6;
                                if (Input::has('payolceradi')) {
                                    $urun->adi = Input::get('payolceradi');
                                }
                                if (Input::has('payolceradet')) {
                                    $urun->adet = Input::get('payolceradet');
                                }
                                if (Input::has('payolcerissue')) {
                                    $urun->issue = Input::get('payolcerissue');
                                }
                                if (Input::has('payolcerdetay')) {
                                    $urun->detay = Input::get('payolcerdetay');
                                }
                                $urun->save();
                                $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                break;
                            case "7":
                                $urun->edestekurun_id = 7;
                                if (Input::has('terminaladi')) {
                                    $urun->adi = Input::get('terminaladi');
                                }
                                if (Input::has('terminaladet')) {
                                    $urun->adet = Input::get('terminaladet');
                                }
                                if (Input::has('terminalissue')) {
                                    $urun->issue = Input::get('terminalissue');
                                }
                                if (Input::has('terminaldetay')) {
                                    $urun->detay = Input::get('terminaldetay');
                                }
                                $urun->save();
                                $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                break;
                            case "8":
                                $urun->edestekurun_id = 8;
                                if (Input::has('kioskadi')) {
                                    $urun->adi = Input::get('kioskadi');
                                }
                                if (Input::has('kioskadet')) {
                                    $urun->adet = Input::get('kioskadet');
                                }
                                if (Input::has('kioskissue')) {
                                    $urun->issue = Input::get('kioskissue');
                                }
                                if (Input::has('kioskdetay')) {
                                    $urun->detay = Input::get('kioskdetay');
                                }
                                $urun->save();
                                $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                break;
                            case "9":
                                $urun->edestekurun_id = 9;
                                if (Input::has('klimaadi')) {
                                    $urun->adi = Input::get('klimaadi');
                                }
                                if (Input::has('klimaadet')) {
                                    $urun->adet = Input::get('klimaadet');
                                }
                                if (Input::has('klimaissue')) {
                                    $urun->issue = Input::get('klimaissue');
                                }
                                if (Input::has('klimadetay')) {
                                    $urun->detay = Input::get('klimadetay');
                                }
                                $urun->save();
                                $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                break;
                            case "10":
                                $urun->edestekurun_id = 10;
                                if (Input::has('digeradi')) {
                                    $urun->adi = Input::get('digeradi');
                                }
                                if (Input::has('digeradet')) {
                                    $urun->adet = Input::get('digeradet');
                                }
                                if (Input::has('digerissue')) {
                                    $urun->issue = Input::get('digerissue');
                                }
                                if (Input::has('digerdetay')) {
                                    $urun->detay = Input::get('digerdetay');
                                }
                                $urun->save();
                                $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                break;
                            case "11":
                                $urun->edestekurun_id = 11;
                                if (Input::has('icuniteadi')) {
                                    $urun->adi = Input::get('icuniteadi');
                                }
                                if (Input::has('icuniteadet')) {
                                    $urun->adet = Input::get('icuniteadet');
                                }
                                if (Input::has('icuniteissue')) {
                                    $urun->issue = Input::get('icuniteissue');
                                }
                                if (Input::has('icunitedetay')) {
                                    $urun->detay = Input::get('icunitedetay');
                                }
                                $urun->save();
                                $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                break;
                            case "12":
                                $urun->edestekurun_id = 12;
                                if (Input::has('kartokuyucuadi')) {
                                    $urun->adi = Input::get('kartokuyucuadi');
                                }
                                if (Input::has('kartokuyucuadet')) {
                                    $urun->adet = Input::get('kartokuyucuadet');
                                }
                                if (Input::has('kartokuyucucesit')) {
                                    $urun->issue = Input::get('kartokuyucucesit');
                                }
                                if (Input::has('kartokuyucudetay')) {
                                    $urun->detay = Input::get('kartokuyucudetay');
                                }
                                $urun->save();
                                $urunler .= ($urunler == "" ? "" : ",") . $urun->id;
                                break;
                        }
                    }
                }
                $sistembilgi->urunler = $urunler;
                if (Input::has('options4')) {
                    $programturleri = Input::get('options4');
                    foreach ($programturleri as $programtur) {
                        $program = new EdestekSistemProgram;
                        switch ($programtur) {
                            case "1":
                                $program->edestekprogram_id = 1;
                                if (Input::has('epicversiyon')) {
                                    $program->versiyon = Input::get('epicversiyon');
                                }
                                if (Input::has('epickullanici')) {
                                    $program->kullaniciadi = Input::get('epickullanici');
                                }
                                if (Input::has('epicsifre')) {
                                    $program->sifre = Input::get('epicsifre');
                                }
                                if (Input::has('epicmanas')) {
                                    $program->yetkilisifre = Input::get('epicmanas');
                                }
                                if (Input::has('epicdiger')) {
                                    $program->diger = Input::get('epicdiger');
                                }
                                $program->save();
                                $programlar .= ($programlar == "" ? "" : ",") . $program->id;

                                $database = new EdestekSistemDatabase;
                                $database->edestekdatabase_id = 1;
                                if (Input::has('oracleversiyon')) {
                                    $database->versiyon = Input::get('oracleversiyon');
                                }
                                if (Input::has('oraclekullanici')) {
                                    $database->kullaniciadi = Input::get('oraclekullanici');
                                }
                                if (Input::has('oraclesifre')) {
                                    $database->sifre = Input::get('oraclesifre');
                                }
                                if (Input::has('oracleveritabani')) {
                                    $database->adi = Input::get('oracleveritabani');
                                }
                                if (Input::has('oraclediger')) {
                                    $database->diger = Input::get('oraclediger');
                                }
                                $database->save();
                                $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $database->id;
                                break;
                            case "2":
                                $program->edestekprogram_id = 2;
                                if (Input::has('smartversiyon')) {
                                    $program->versiyon = Input::get('smartversiyon');
                                }
                                if (Input::has('smartkullanici')) {
                                    $program->kullaniciadi = Input::get('smartkullanici');
                                }
                                if (Input::has('smartsifre')) {
                                    $program->sifre = Input::get('smartsifre');
                                }
                                if (Input::has('smartmanas')) {
                                    $program->yetkilisifre = Input::get('smartmanas');
                                }
                                if (Input::has('smartdiger')) {
                                    $program->diger = Input::get('smartdiger');
                                }
                                $program->save();
                                $programlar .= ($programlar == "" ? "" : ",") . $program->id;

                                $database = new EdestekSistemDatabase;
                                $database->edestekdatabase_id = 2;
                                if (Input::has('sqlversiyon')) {
                                    $database->versiyon = Input::get('sqlversiyon');
                                }
                                if (Input::has('sqlkullanici')) {
                                    $database->kullaniciadi = Input::get('sqlkullanici');
                                }
                                if (Input::has('sqlsifre')) {
                                    $database->sifre = Input::get('sqlsifre');
                                }
                                if (Input::has('sqlveritabani')) {
                                    $database->adi = Input::get('sqlveritabani');
                                }
                                if (Input::has('sqldiger')) {
                                    $database->diger = Input::get('sqldiger');
                                }
                                $database->save();
                                $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $database->id;
                                break;
                            case "3":
                                $program->edestekprogram_id = 3;
                                if (Input::has('4comversiyon')) {
                                    $program->versiyon = Input::get('4comversiyon');
                                }
                                if (Input::has('4comkullanici')) {
                                    $program->kullaniciadi =Input::get('4comkullanici');
                                }
                                if (Input::has('4comsifre')) {
                                    $program->sifre = Input::get('4comsifre');
                                }
                                if (Input::has('4compower')) {
                                    $program->yetkilisifre = Input::get('4compower');
                                }
                                if (Input::has('4comdiger')) {
                                    $program->diger = Input::get('4comdiger');
                                }
                                $program->save();
                                $programlar .= ($programlar == "" ? "" : ",") . $program->id;

                                $database = new EdestekSistemDatabase;
                                $database->edestekdatabase_id = 3;
                                if (Input::has('mysqlversiyon')) {
                                    $database->versiyon = Input::get('mysqlversiyon');
                                }
                                if (Input::has('mysqlkullanici')) {
                                    $database->kullaniciadi = Input::get('mysqlkullanici');
                                }
                                if (Input::has('mysqlsifre')) {
                                    $database->sifre = Input::get('mysqlsifre');
                                }
                                if (Input::has('mysqlveritabani')) {
                                    $database->adi = Input::get('mysqlveritabani');
                                }
                                if (Input::has('mysqldiger')) {
                                    $database->diger = Input::get('mysqldiger');
                                }
                                $database->save();
                                $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $database->id;
                                break;
                            case "4":
                                $program->edestekprogram_id = 4;
                                if (Input::has('options5')) {
                                    $program->edestekentegrasyonfirma_id = Input::get('options5');
                                }
                                if (Input::has('options6')) {
                                    $program->edestekentegrasyontip_id = Input::get('options6');
                                }
                                if (Input::has('options7')) {
                                    $program->edestekentegrasyonprogram_id = Input::get('options7');
                                }
                                if (Input::has('options8')) {
                                    $program->edestekentegrasyonversiyon_id = Input::get('options8');
                                }
                                if (Input::has('entegrasyondiger')) {
                                    $program->diger = Input::get('entegrasyondiger');
                                }
                                $program->save();
                                $programlar .= ($programlar == "" ? "" : ",") . $program->id;
                                break;
                            case "5":
                                $program->edestekprogram_id = 5;
                                if (Input::has('ipmanversiyon')) {
                                    $program->versiyon = Input::get('ipmanversiyon');
                                }
                                if (Input::has('ipmankullanici')) {
                                    $program->kullaniciadi = Input::get('ipmankullanici');
                                }
                                if (Input::has('ipmansifre')) {
                                    $program->sifre = Input::get('ipmansifre');
                                }
                                if (Input::has('ipmanmanas')) {
                                    $program->yetkilisifre = Input::get('ipmanmanas');
                                }
                                if (Input::has('ipmandiger')) {
                                    $program->diger = Input::get('ipmandiger');
                                }
                                $program->save();
                                $programlar .= ($programlar == "" ? "" : ",") . $program->id;

                                $database = new EdestekSistemDatabase;
                                $database->edestekdatabase_id = 5;
                                if (Input::has('ipmansqlversiyon')) {
                                    $database->versiyon = Input::get('ipmansqlversiyon');
                                }
                                if (Input::has('ipmansqlkullanici')) {
                                    $database->kullaniciadi = Input::get('ipmansqlkullanici');
                                }
                                if (Input::has('ipmansqlsifre')) {
                                    $database->sifre = Input::get('ipmansqlsifre');
                                }
                                if (Input::has('ipmanveritabani')) {
                                    $database->adi = Input::get('ipmanveritabani');
                                }
                                if (Input::has('ipmansqldiger')) {
                                    $database->diger = Input::get('ipmansqldiger');
                                }
                                $database->save();
                                $veritabanlari .= ($veritabanlari == "" ? "" : ",") . $database->id;
                                break;
                        }
                    }
                }
                $sistembilgi->programlar = $programlar;
                $sistembilgi->veritabanlari = $veritabanlari;
                if (Input::has('cariadi')) {
                    $sistembilgi->netsiscari_id = Input::get('cariadi');
                }
                if (Input::has('options2')) {
                    $sistembilgi->plasiyer_id = Input::get('options2');
                }
                $sistembilgi->save();
                $edestekmusteri->edesteksistembilgi_id = $sistembilgi->id;
            }
            $adi = Input::get('adi');
            If (EdestekMusteri::where('musteriadi', $adi)->where('id', '<>', $id)->first()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Bu proje adı mevcut.', 'type' => 'error'));
            }
            if (Input::has('projedetayid')) {
                $edestekmusteri->projedetay = Input::get('projedetayid');
            } else if ($edestekmusteri->projedetay) {
                $edestekmusteri->projedetay = "";
            }
            if (Input::has('baslangic')) {
                $baslangictarih = date("Y-m-d", strtotime(Input::get('baslangic')));
                $edestekmusteri->baslangictarihi = $baslangictarih;
            } else if ($edestekmusteri->baslangictarihi) {
                $edestekmusteri->baslangictarihi = NULL;
            }
            if (Input::has('bitis')) {
                $bitistarih = date("Y-m-d", strtotime(Input::get('bitis')));
                $edestekmusteri->bitistarihi = $bitistarih;
            } else if ($edestekmusteri->bitistarihi) {
                $edestekmusteri->bitistarihi = NULL;
            }
            If (Input::hasFile('resim')) {
                File::delete('assets/images/proje/' . $edestekmusteri->projeresim . '');
                $resim = Input::file('resim');
                $uzanti = $resim->getClientOriginalExtension();
                $isim = Str::slug($adi) . Str::slug(str_random(5)) . '.' . $uzanti;
                $resim->move('assets/images/proje/', $isim);
                $image = Image::make('assets/images/proje/' . $isim);
                $image->fit(200);
                $image->save();
                $edestekmusteri->projeresim = $isim;
            }
            $baskiidler = "";
            if (Input::has('options9')) {
                $baskiturler = Input::get('options9');
                $baskiid = $edestekmusteri->edestekbaskiidler;
                if ($baskiid != "") {
                    try {
                        $baskiideski = explode(',', $baskiid);
                        foreach ($baskiideski as $baskiid) {
                            $baski = EdestekKartBaski::find($baskiid);
                            if ($this->baskiidkontrol($baski->edestekbaskitur_id, $baskiturler)) //guncelleme var
                            {
                                switch ($baski->edestekbaskitur_id) {
                                    case "1":
                                        If (Input::hasFile('suontaraf')) {
                                            File::delete('assets/images/baski/' . $baski->onresim . '');
                                            $ontaraf = Input::file('suontaraf');
                                            $uzanti = $ontaraf->getClientOriginalExtension();
                                            $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                            $ontaraf->move('assets/images/baski/', $isim);
                                            $image = Image::make('assets/images/baski/' . $isim);
                                            $image->fit(400, 256);
                                            $image->save();
                                            $baski->onresim = $isim;
                                        }

                                        If (Input::hasFile('suarkataraf')) {
                                            File::delete('assets/images/proje/' . $baski->arkaresim . '');
                                            $arkataraf = Input::file('suarkataraf');
                                            $uzanti = $arkataraf->getClientOriginalExtension();
                                            $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                            $arkataraf->move('assets/images/baski/', $isim);
                                            $image = Image::make('assets/images/baski/' . $isim);
                                            $image->fit(400, 256);
                                            $image->save();
                                            $baski->arkaresim = $isim;
                                        }
                                        $baski->save();
                                        $baskiidler .= ($baskiidler == "" ? "" : ",") . $baski->id;
                                        break;
                                    case "2":
                                        If (Input::hasFile('klmontaraf')) {
                                            File::delete('assets/images/baski/' . $baski->onresim . '');
                                            $ontaraf = Input::file('klmontaraf');
                                            $uzanti = $ontaraf->getClientOriginalExtension();
                                            $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                            $ontaraf->move('assets/images/baski/', $isim);
                                            $image = Image::make('assets/images/baski/' . $isim);
                                            $image->fit(400, 256);
                                            $image->save();
                                            $baski->onresim = $isim;
                                        }
                                        If (Input::hasFile('klmarkataraf')) {
                                            File::delete('assets/images/proje/' . $baski->arkaresim . '');
                                            $arkataraf = Input::file('klmarkataraf');
                                            $uzanti = $arkataraf->getClientOriginalExtension();
                                            $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                            $arkataraf->move('assets/images/baski/', $isim);
                                            $image = Image::make('assets/images/baski/' . $isim);
                                            $image->fit(400, 256);
                                            $image->save();
                                            $baski->arkaresim = $isim;
                                        }
                                        $baski->save();
                                        $baskiidler .= ($baskiidler == "" ? "" : ",") . $baski->id;
                                        break;
                                    case "3":
                                        $baski->save();
                                        $baskiidler .= ($baskiidler == "" ? "" : ",") . $baski->id;
                                        break;
                                    case "4":
                                        If (Input::hasFile('trifazeontaraf')) {
                                            File::delete('assets/images/baski/' . $baski->onresim . '');
                                            $ontaraf = Input::file('trifazeontaraf');
                                            $uzanti = $ontaraf->getClientOriginalExtension();
                                            $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                            $ontaraf->move('assets/images/baski/', $isim);
                                            $image = Image::make('assets/images/baski/' . $isim);
                                            $image->fit(400, 256);
                                            $image->save();
                                            $baski->onresim = $isim;
                                        }
                                        If (Input::hasFile('trifazearkataraf')) {
                                            File::delete('assets/images/proje/' . $baski->arkaresim . '');
                                            $arkataraf = Input::file('trifazearkataraf');
                                            $uzanti = $arkataraf->getClientOriginalExtension();
                                            $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                            $arkataraf->move('assets/images/baski/', $isim);
                                            $image = Image::make('assets/images/baski/' . $isim);
                                            $image->fit(400, 256);
                                            $image->save();
                                            $baski->arkaresim = $isim;
                                        }
                                        $baski->save();
                                        $baskiidler .= ($baskiidler == "" ? "" : ",") . $baski->id;
                                        break;
                                    case "5":
                                        If (Input::hasFile('monoontaraf')) {
                                            File::delete('assets/images/baski/' . $baski->onresim . '');
                                            $ontaraf = Input::file('monoontaraf');
                                            $uzanti = $ontaraf->getClientOriginalExtension();
                                            $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                            $ontaraf->move('assets/images/baski/', $isim);
                                            $image = Image::make('assets/images/baski/' . $isim);
                                            $image->fit(400, 256);
                                            $image->save();
                                            $baski->onresim = $isim;
                                        }
                                        If (Input::hasFile('monoarkataraf')) {
                                            File::delete('assets/images/proje/' . $baski->arkaresim . '');
                                            $arkataraf = Input::file('monoarkataraf');
                                            $uzanti = $arkataraf->getClientOriginalExtension();
                                            $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                            $arkataraf->move('assets/images/baski/', $isim);
                                            $image = Image::make('assets/images/baski/' . $isim);
                                            $image->fit(400, 256);
                                            $image->save();
                                            $baski->arkaresim = $isim;
                                        }
                                        $baski->save();
                                        $baskiidler .= ($baskiidler == "" ? "" : ",") . $baski->id;

                                        break;
                                    case "6":
                                        $baski->save();
                                        $baskiidler .= ($baskiidler == "" ? "" : ",") . $baski->id;
                                        break;
                                    case "7":
                                        If (Input::hasFile('klimaontaraf')) {
                                            File::delete('assets/images/baski/' . $baski->onresim . '');
                                            $ontaraf = Input::file('klimaontaraf');
                                            $uzanti = $ontaraf->getClientOriginalExtension();
                                            $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                            $ontaraf->move('assets/images/baski/', $isim);
                                            $image = Image::make('assets/images/baski/' . $isim);
                                            $image->fit(400, 256);
                                            $image->save();
                                            $baski->onresim = $isim;
                                        }
                                        If (Input::hasFile('klimaarkataraf')) {
                                            File::delete('assets/images/baski/' . $baski->arkaresim . '');
                                            $arkataraf = Input::file('klimaarkataraf');
                                            $uzanti = $arkataraf->getClientOriginalExtension();
                                            $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                            $arkataraf->move('assets/images/baski/', $isim);
                                            $image = Image::make('assets/images/baski/' . $isim);
                                            $image->fit(400, 256);
                                            $image->save();
                                            $baski->arkaresim = $isim;
                                        }
                                        $baski->save();
                                        $baskiidler .= ($baskiidler == "" ? "" : ",") . $baski->id;
                                        break;
                                    case "8":
                                        If (Input::hasFile('gazontaraf')) {
                                            File::delete('assets/images/baski/' . $baski->onresim . '');
                                            $ontaraf = Input::file('gazontaraf');
                                            $uzanti = $ontaraf->getClientOriginalExtension();
                                            $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                            $ontaraf->move('assets/images/baski/', $isim);
                                            $image = Image::make('assets/images/baski/' . $isim);
                                            $image->fit(400, 256);
                                            $image->save();
                                            $baski->onresim = $isim;
                                        }
                                        If (Input::hasFile('gazarkataraf')) {
                                            File::delete('assets/images/baski/' . $baski->arkaresim . '');
                                            $arkataraf = Input::file('gazarkataraf');
                                            $uzanti = $arkataraf->getClientOriginalExtension();
                                            $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                            $arkataraf->move('assets/images/baski/', $isim);
                                            $image = Image::make('assets/images/baski/' . $isim);
                                            $image->fit(400, 256);
                                            $image->save();
                                            $baski->arkaresim = $isim;
                                        }
                                        $baski->save();
                                        $baskiidler .= ($baskiidler == "" ? "" : ",") . $baski->id;
                                        break;
                                    case "9":
                                        If (Input::hasFile('mifareontaraf')) {
                                            File::delete('assets/images/baski/' . $baski->onresim . '');
                                            $ontaraf = Input::file('mifareontaraf');
                                            $uzanti = $ontaraf->getClientOriginalExtension();
                                            $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                            $ontaraf->move('assets/images/baski/', $isim);
                                            $image = Image::make('assets/images/baski/' . $isim);
                                            $image->fit(400, 256);
                                            $image->save();
                                            $baski->onresim = $isim;
                                        }
                                        If (Input::hasFile('mifarearkataraf')) {
                                            File::delete('assets/images/baski/' . $baski->arkaresim . '');
                                            $arkataraf = Input::file('mifarearkataraf');
                                            $uzanti = $arkataraf->getClientOriginalExtension();
                                            $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                            $arkataraf->move('assets/images/baski/', $isim);
                                            $image = Image::make('assets/images/baski/' . $isim);
                                            $image->fit(400, 256);
                                            $image->save();
                                            $baski->arkaresim = $isim;
                                        }
                                        $baski->save();
                                        $baskiidler .= ($baskiidler == "" ? "" : ",") . $baski->id;
                                        break;
                                    case "10":
                                        $baski->save();
                                        $baskiidler .= ($baskiidler == "" ? "" : ",") . $baski->id;
                                        break;
                                }
                            } else { //eskisi silinmiş
                                if ($baski->onresim) {
                                    File::delete('assets/images/baski/' . $baski->onresim . '');
                                }
                                if ($baski->arkaresim) {
                                    File::delete('assets/images/baski/' . $baski->arkaresim . '');
                                }
                                $baski->delete();
                            }
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Müşteri Güncellenemedi', 'text' => 'Baskı Bilgisi Güncellenirken Karşılaşıldı.', 'type' => 'error'));
                    }
                }
                $baskiid = $edestekmusteri->edestekbaskiidler;
                foreach ($baskiturler as $baskitur) {
                    if (!$this->baskiidkontrolyeni($baskitur, $baskiid)) // yeni ekleme ise
                    {
                        try {
                            $baski = new EdestekKartBaski;
                            switch ($baskitur) {
                                case "1":
                                    $baski->edestekbaskitur_id = 1;
                                    If (Input::hasFile('suontaraf')) {
                                        $ontaraf = Input::file('suontaraf');
                                        $uzanti = $ontaraf->getClientOriginalExtension();
                                        $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                        $ontaraf->move('assets/images/baski/', $isim);
                                        $image = Image::make('assets/images/baski/' . $isim);
                                        $image->fit(400, 256);
                                        $image->save();
                                        $baski->onresim = $isim;
                                    }
                                    If (Input::hasFile('suarkataraf')) {
                                        $arkataraf = Input::file('suarkataraf');
                                        $uzanti = $arkataraf->getClientOriginalExtension();
                                        $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                        $arkataraf->move('assets/images/baski/', $isim);
                                        $image = Image::make('assets/images/baski/' . $isim);
                                        $image->fit(400, 256);
                                        $image->save();
                                        $baski->arkaresim = $isim;
                                    }
                                    $baski->save();
                                    $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                                    break;
                                case "2":
                                    $baski->edestekbaskitur_id = 2;
                                    If (Input::hasFile('klmontaraf')) {
                                        $ontaraf = Input::file('klmontaraf');
                                        $uzanti = $ontaraf->getClientOriginalExtension();
                                        $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                        $ontaraf->move('assets/images/baski/', $isim);
                                        $image = Image::make('assets/images/baski/' . $isim);
                                        $image->fit(400, 256);
                                        $image->save();
                                        $baski->onresim = $isim;
                                    }
                                    If (Input::hasFile('klmarkataraf')) {
                                        $arkataraf = Input::file('klmarkataraf');
                                        $uzanti = $arkataraf->getClientOriginalExtension();
                                        $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                        $arkataraf->move('assets/images/baski/', $isim);
                                        $image = Image::make('assets/images/baski/' . $isim);
                                        $image->fit(400, 256);
                                        $image->save();
                                        $baski->arkaresim = $isim;
                                    }
                                    $baski->save();
                                    $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                                    break;
                                case "3":
                                    $baski->edestekbaskitur_id = 3;
                                    $baski->save();
                                    $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                                    break;
                                case "4":
                                    $baski->edestekbaskitur_id = 4;
                                    If (Input::hasFile('trifazeontaraf')) {
                                        $ontaraf = Input::file('trifazeontaraf');
                                        $uzanti = $ontaraf->getClientOriginalExtension();
                                        $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                        $ontaraf->move('assets/images/baski/', $isim);
                                        $image = Image::make('assets/images/baski/' . $isim);
                                        $image->fit(400, 256);
                                        $image->save();
                                        $baski->onresim = $isim;
                                    }
                                    If (Input::hasFile('trifazearkataraf')) {
                                        $arkataraf = Input::file('trifazearkataraf');
                                        $uzanti = $arkataraf->getClientOriginalExtension();
                                        $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                        $arkataraf->move('assets/images/baski/', $isim);
                                        $image = Image::make('assets/images/baski/' . $isim);
                                        $image->fit(400, 256);
                                        $image->save();
                                        $baski->arkaresim = $isim;
                                    }
                                    $baski->save();
                                    $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                                    break;
                                case "5":
                                    $baski->edestekbaskitur_id = 5;
                                    If (Input::hasFile('monoontaraf')) {
                                        $ontaraf = Input::file('monoontaraf');
                                        $uzanti = $ontaraf->getClientOriginalExtension();
                                        $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                        $ontaraf->move('assets/images/baski/', $isim);
                                        $image = Image::make('assets/images/baski/' . $isim);
                                        $image->fit(400, 256);
                                        $image->save();
                                        $baski->onresim = $isim;
                                    }
                                    If (Input::hasFile('monoarkataraf')) {
                                        $arkataraf = Input::file('monoarkataraf');
                                        $uzanti = $arkataraf->getClientOriginalExtension();
                                        $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                        $arkataraf->move('assets/images/baski/', $isim);
                                        $image = Image::make('assets/images/baski/' . $isim);
                                        $image->fit(400, 256);
                                        $image->save();
                                        $baski->arkaresim = $isim;
                                    }
                                    $baski->save();
                                    $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                                    break;
                                case "6":
                                    $baski->edestekbaskitur_id = 6;
                                    $baski->save();
                                    $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                                    break;
                                case "7":
                                    $baski->edestekbaskitur_id = 7;
                                    If (Input::hasFile('klimaontaraf')) {
                                        $ontaraf = Input::file('klimaontaraf');
                                        $uzanti = $ontaraf->getClientOriginalExtension();
                                        $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                        $ontaraf->move('assets/images/baski/', $isim);
                                        $image = Image::make('assets/images/baski/' . $isim);
                                        $image->fit(400, 256);
                                        $image->save();
                                        $baski->onresim = $isim;
                                    }
                                    If (Input::hasFile('klimaarkataraf')) {
                                        $arkataraf = Input::file('klimaarkataraf');
                                        $uzanti = $arkataraf->getClientOriginalExtension();
                                        $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                        $arkataraf->move('assets/images/baski/', $isim);
                                        $image = Image::make('assets/images/baski/' . $isim);
                                        $image->fit(400, 256);
                                        $image->save();
                                        $baski->arkaresim = $isim;
                                    }
                                    $baski->save();
                                    $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                                    break;
                                case "8":
                                    $baski->edestekbaskitur_id = 8;
                                    If (Input::hasFile('gazontaraf')) {
                                        $ontaraf = Input::file('gazontaraf');
                                        $uzanti = $ontaraf->getClientOriginalExtension();
                                        $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                        $ontaraf->move('assets/images/baski/', $isim);
                                        $image = Image::make('assets/images/baski/' . $isim);
                                        $image->fit(400, 256);
                                        $image->save();
                                        $baski->onresim = $isim;
                                    }
                                    If (Input::hasFile('gazarkataraf')) {
                                        $arkataraf = Input::file('gazarkataraf');
                                        $uzanti = $arkataraf->getClientOriginalExtension();
                                        $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                        $arkataraf->move('assets/images/baski/', $isim);
                                        $image = Image::make('assets/images/baski/' . $isim);
                                        $image->fit(400, 256);
                                        $image->save();
                                        $baski->arkaresim = $isim;
                                    }
                                    $baski->save();
                                    $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                                    break;
                                case "9":
                                    $baski->edestekbaskitur_id = 9;
                                    If (Input::hasFile('mifareontaraf')) {
                                        $ontaraf = Input::file('mifareontaraf');
                                        $uzanti = $ontaraf->getClientOriginalExtension();
                                        $isim = Str::slug($adi) . 'on' . Str::slug(str_random(5)) . '.' . $uzanti;
                                        $ontaraf->move('assets/images/baski/', $isim);
                                        $image = Image::make('assets/images/baski/' . $isim);
                                        $image->fit(400, 256);
                                        $image->save();
                                        $baski->onresim = $isim;
                                    }
                                    If (Input::hasFile('mifarearkataraf')) {
                                        $arkataraf = Input::file('mifarearkataraf');
                                        $uzanti = $arkataraf->getClientOriginalExtension();
                                        $isim = Str::slug($adi) . 'arka' . Str::slug(str_random(5)) . '.' . $uzanti;
                                        $arkataraf->move('assets/images/baski/', $isim);
                                        $image = Image::make('assets/images/baski/' . $isim);
                                        $image->fit(400,256);
                                        $image->save();
                                        $baski->arkaresim = $isim;
                                    }
                                    $baski->save();
                                    $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                                    break;
                                case "10":
                                    $baski->edestekbaskitur_id=10;
                                    $baski->save();
                                    $baskiidler .= ($baskiidler=="" ? "" : ",") .$baski->id;
                                    break;
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Müşteri Güncellenemedi', 'text' => 'Baskı Bilgisi Güncellenemedi', 'type' => 'error'));
                        }
                    }
                }
            }else{
                $baskiid = $edestekmusteri->edestekbaskiidler;
                if($baskiid!="") {
                    try {
                        $baskiideski = explode(',', $baskiid);
                        foreach ($baskiideski as $baskiid) {
                            $baski = EdestekKartBaski::find($baskiid);
                            if ($baski->onresim) {
                                File::delete('assets/images/baski/' . $baski->onresim . '');
                            }
                            if ($baski->arkaresim) {
                                File::delete('assets/images/baski/' . $baski->arkaresim . '');
                            }
                            $baski->delete();
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Müşteri Güncellenemedi', 'text' => 'Baskı Bilgisi Güncellenemedi', 'type' => 'error'));
                    }
                }
            }
            $edestekmusteri->edestekbaskiidler = $baskiidler;
            if (Input::has('options3')) {
                $urunsecilen = Input::get('options3');
                $urunler="";
                foreach($urunsecilen as $urun)
                {
                    $urunler .= ($urunler=="" ? "" : ",").$urun;
                }
                $edestekmusteri->urunturleri = $urunler;
            }else if($edestekmusteri->urunturleri)
            {
                $edestekmusteri->urunturleri="";
            }
            if (Input::has('options4')) {
                $programsecilen = Input::get('options4');
                $programlar="";
                foreach($programsecilen as $program)
                {
                    $programlar .= ($programlar=="" ? "" : ",").$program;
                }
                $edestekmusteri->programturleri = $programlar;
            }else if($edestekmusteri->programturleri)
            {
                $edestekmusteri->programturleri="";
            }
            if (Input::has('options9')) {
                $baskisecilen = Input::get('options9');
                $baskilar="";
                foreach($baskisecilen as $baski)
                {
                    $baskilar .= ($baskilar=="" ? "" : ",").$baski;
                }
                $edestekmusteri->baskiturleri = $baskilar;
            }else if($edestekmusteri->baskiturleri)
            {
                $edestekmusteri->baskiturleri="";
            }
            $edestekmusteri->musteriadi = $adi;
            $edestekmusteri->save();
            DB::commit();
            return Redirect::to('edestek/projebilgisi')->with(array('mesaj' => 'true', 'title' => 'Müşteri Güncellendi', 'text' => 'Müşteri Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Müşteri Güncellenemedi', 'text' => 'Müşteri Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }
    
    public function getMusterisil($id){
        try {
            DB::beginTransaction();
            $edestekmusteri = EdestekMusteri::find($id);
            $edestekmusteribilgi = EdestekMusteriBilgi::find($edestekmusteri->edestekmusteribilgi_id);
            $edesteksistembilgi = EdestekSistemBilgi::find($edestekmusteri->edesteksistembilgi_id);
            $baskilist=($edestekmusteri->edestekbaskiidler!='' ? explode(',',$edestekmusteri->edestekbaskiidler) : array(0));
            $edestekbaskilar=EdestekKartBaski::whereIn('id',$baskilist)->get();
            if($edestekbaskilar){
                $flag=0;
                foreach ($edestekbaskilar as $baski){
                    if(EdestekBaski::where('edestekkartbaski_id',$baski->id)->count()>0)
                        $flag=1;
                }
                if($flag){
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Müşteri Silinemedi', 'text' => 'Kart Baskılarından birine ait baskı yapılmış', 'type' => 'error'));
                }else{
                    foreach ($edestekbaskilar as $baski)
                        $baski->delete();
                }
            }
            if($edesteksistembilgi){
                $urunlist=($edesteksistembilgi->urunler!='' ? explode(',',$edesteksistembilgi->urunler) : array(0));
                $edesteksistemurunler=EdestekSistemUrun::whereIn('id',$urunlist)->get();
                if($edesteksistemurunler){
                    foreach ($edesteksistemurunler as $urun)
                        $urun->delete();
                }
                $programlist=($edesteksistembilgi->programlar!='' ? explode(',',$edesteksistembilgi->programlar) : array(0));
                $edesteksistemprogramlar=EdestekSistemProgram::whereIn('id',$programlist)->get();
                if($edesteksistemprogramlar){
                    foreach ($edesteksistemprogramlar as $program)
                        $program->delete();
                }
                $databaselist=($edesteksistembilgi->veritabanlari!='' ? explode(',',$edesteksistembilgi->veritabanlari) : array(0));
                $edesteksistemveritabanlari=EdestekSistemDatabase::whereIn('id',$databaselist)->get();
                if($edesteksistemveritabanlari){
                    foreach ($edesteksistemveritabanlari as $database)
                        $database->delete();
                }
            }
            if($edestekmusteribilgi)
                $edestekmusteribilgi->delete();
            $edestekmusteri->delete();
            DB::commit();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Müşteri Silindi', 'text' => 'Müşteri Başarıyla Silindi.', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Müşteri Silinemedi', 'text' => 'Müşteri Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }
    
    public function getEdestekkayit() {

        $duzenliislem = EdestekDuzenliIslem::where('durum',1)->get();
        try {
            foreach ($duzenliislem as $islem) {
                $aralikzaman = $islem->aralik * EdestekDuzenliIslem::aralikkatsayi($islem->araliktip);
                if (is_null($islem->sonislemtarihi)) {
                    $comparison = DB::select(DB::raw("SELECT CASE WHEN dateadd(day,0,'" . $islem->baslangictarih . "') <= getdate() THEN 1 ELSE 0 END AS compare"));
                } else {
                    $comparison = DB::select(DB::raw("SELECT CASE WHEN dateadd(day," . $aralikzaman . ",'" . $islem->sonislemtarihi . "') <= getdate() THEN 1 ELSE 0 END AS compare"));
                }
                if ($comparison[0]->compare) {
                    $yeniislem = new EdestekIslem;

                    $musteri = $islem->edestekmusteri_id;
                    $konu = $islem->edestekkonu_id;
                    $konuislem = $islem->edestekkonuislem_id;
                    $personel = $islem->edestekpersonel_id;
                    $durum = 0;

                    $yeniislem->edestekmusteri_id = $musteri;
                    $yeniislem->tarih = date('Y-m-d H:i:s');
                    $yeniislem->edestekkonu_id = $konu;
                    $yeniislem->edestekkonuislem_id = $konuislem;
                    $yeniislem->detay = $islem->detay;
                    $yeniislem->edestekpersonel_id = $personel;
                    $yeniislem->durum = $durum;
                    $yeniislem->save();
                    $islem->sonislemtarihi = $yeniislem->tarih;
                    $islem->save();
                    $kayit = new EdestekKayit;
                    $kayit->edestekmusteri_id = $musteri;
                    $kayit->konu_id = $yeniislem->id;
                    $kayit->edestekkayitkonu_id = 5;
                    $kayit->yapilanislem = $yeniislem->edestekkonu->adi . " Konusunda İşlem Bekliyor.";
                    $kayit->edestekpersonel_id = $personel;
                    $kayit->tarih = $yeniislem->tarih;
                    $kayit->durum = $durum;
                    $kayit->save();
                }
            }
            $kayitlar = EdestekKayit::orderBy('id','desc')->get();
            return View::make('edestek.edestekkayit',array('kayitlar'=>$kayitlar))->with('title', 'Yazılım Destek Kayıt Ekranı');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            $kayitlar = EdestekKayit::orderBy('id','desc')->get();
            return View::make('edestek.edestekkayit',array('kayitlar'=>$kayitlar,'mesaj' => 'true', 'title' => 'Düzenli İşlem Eklenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'))->with('title', 'Yazılım Destek Kayıt Ekranı');
        }
    }

    public function getKayitduzenle($id) {
        $kayit =  EdestekKayit::find($id);
        if($kayit->edestekkayitkonu_id==1) //görüşme
        {
            $gorusme = EdestekGorusme::find($kayit->konu_id);
            $musteriler = EdestekMusteri::orderBy('musteriadi','ASC')->get();
            $konular = EdestekKonu::orderBy('adi','ASC')->get();
            $detaylar = EdestekKonuDetay::orderBy('detay','ASC')->get();
            $personeller = EdestekPersonel::orderBy('adisoyadi','ASC')->get();
            $hatacozumleri = EdestekhataCozum::take(10)->get();
            return View::make('edestek.gorusmeduzenle',array('kayit'=>$kayit,'gorusme'=>$gorusme,'musteriler'=>$musteriler,'konular'=>$konular,'detaylar'=>$detaylar,'personeller'=>$personeller,'hatacozumleri'=>$hatacozumleri))->with('title', 'Yazılım Destek Görüşme Bilgisi Düzenleme Ekranı');
        }else if($kayit->edestekkayitkonu_id==2) //kurulum
        {
            $kurulum = EdestekKurulum::find($kayit->konu_id);
            $musteriler = EdestekMusteri::orderBy('musteriadi','ASC')->get();
            $kurulumturleri = EdestekKurulumTur::all();
            $personeller = EdestekPersonel::orderBy('adisoyadi','ASC')->get();
            return View::make('edestek.kurulumduzenle',array('kayit'=>$kayit,'kurulum'=>$kurulum,'musteriler'=>$musteriler,'kurulumturleri'=>$kurulumturleri,'personeller'=>$personeller))->with('title', 'Yazılım Destek Kurulum Bilgisi Düzenleme Ekranı');
        }else if($kayit->edestekkayitkonu_id==3) //tamir bakım
        {
            $tamir = EdestekTamir::find($kayit->konu_id);
            $musteriler = EdestekMusteri::orderBy('musteriadi','ASC')->get();
            $personeller = EdestekPersonel::orderBy('adisoyadi','ASC')->get();
            $tamirurunler = EdestekTamirUrun::all();
            $tamirislemler = EdestekTamirIslem::all();
            return View::make('edestek.tamirduzenle',array('kayit'=>$kayit,'tamir'=>$tamir,'musteriler'=>$musteriler,'tamirurunler'=>$tamirurunler,'tamirislemler'=>$tamirislemler,'personeller'=>$personeller))->with('title', 'Yazılım Destek Tamir Bakım Bilgisi Düzenleme Ekranı');
        }else if($kayit->edestekkayitkonu_id==4) //kart baskı
        {
            $baski = EdestekBaski::find($kayit->konu_id);
            $musteriler = EdestekMusteri::orderBy('musteriadi','ASC')->get();
            $plasiyerler = Plasiyer::orderBy('plasiyeradi','ASC')->get();
            $baskilar = EdestekKartBaski::all();
            $personeller = EdestekPersonel::orderBy('adisoyadi','ASC')->get();
            return View::make('edestek.baskiduzenle',array('kayit'=>$kayit,'baski'=>$baski,'musteriler'=>$musteriler,'plasiyerler'=>$plasiyerler,'baskilar'=>$baskilar,'personeller'=>$personeller))->with('title', 'Yazılım Destek Kart Baskı Bilgisi Düzenleme Ekranı');
        }else if($kayit->edestekkayitkonu_id==5) //düzenli işlem
        {
            $islem = EdestekIslem::find($kayit->konu_id);
            $musteriler = EdestekMusteri::orderBy('musteriadi','ASC')->get();
            $konular = EdestekKonu::orderBy('adi','ASC')->get();
            $konuislemler = EdestekKonuIslem::orderBy('islem','ASC')->get();
            $personeller = EdestekPersonel::orderBy('adisoyadi','ASC')->get();
            return View::make('edestek.islemkayitduzenle',array('kayit'=>$kayit,'islem'=>$islem,'musteriler'=>$musteriler,'konular'=>$konular,'konuislemler'=>$konuislemler,'personeller'=>$personeller))->with('title', 'Yazılım Destek İşlem Düzenleme Ekranı');
        }
        return '';
    }

    public function getKayitgoster($id) {
        $kayit =  EdestekKayit::find($id);
        if($kayit->edestekkayitkonu_id==1) //görüşme
        {
            $gorusme = EdestekGorusme::find($kayit->konu_id);
            return View::make('edestek.gorusmegoster',array('kayit'=>$kayit,'gorusme'=>$gorusme))->with('title', 'Yazılım Destek Görüşme Bilgisi Ekranı');

        }else if($kayit->edestekkayitkonu_id==2) //kurulum
        {
            $kurulum = EdestekKurulum::find($kayit->konu_id);
            return View::make('edestek.kurulumgoster',array('kayit'=>$kayit,'kurulum'=>$kurulum))->with('title', 'Yazılım Destek Kurulum Bilgisi Ekranı');
        }else if($kayit->edestekkayitkonu_id==3) //tamir bakım
        {
            $tamir = EdestekTamir::find($kayit->konu_id);
            return View::make('edestek.tamirgoster',array('kayit'=>$kayit,'tamir'=>$tamir))->with('title', 'Yazılım Destek Tamir Bakım Bilgisi Ekranı');
        }else if($kayit->edestekkayitkonu_id==4) //kart baskı
        {
            $baski = EdestekBaski::find($kayit->konu_id);
            return View::make('edestek.baskigoster',array('kayit'=>$kayit,'baski'=>$baski))->with('title', 'Yazılım Destek Kart Baskı Bilgisi Ekranı');
        }else if($kayit->edestekkayitkonu_id==5) //düzenli işlem
        {
            $islem = EdestekIslem::find($kayit->konu_id);
            return View::make('edestek.islemkayitgoster',array('kayit'=>$kayit,'islem'=>$islem))->with('title', 'Yazılım Destek İşlem Bilgisi Ekranı');

        }

        return '';
    }

    public function getKayitsil($id){
        try{
            $kayit = EdestekKayit::find($id);
            if($kayit){
                $personel = EdestekPersonel::find($kayit->edestekpersonel_id);
                switch ($kayit->edestekkayitkonu_id){
                    case 1;
                        $gorusme = EdestekGorusme::find($kayit->konu_id);
                        $gorusme->delete();
                        if($gorusme->delete()){
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kayıt Silinemedi', 'text' => 'Kayıt Silinirken Sorun Oluştu.', 'type' => 'error'));
                        }
                        break;
                    case 2:
                        $kurulum = EdestekKurulum::find($kayit->konu_id);
                        $tutanak = $kurulum->tutanak;
                        $kurulum->delete();
                        if($kurulum->delete()){
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kayıt Silinemedi', 'text' => 'Kayıt Silinirken Sorun Oluştu.', 'type' => 'error'));
                        }
                        File::delete('assets/tutanak/'.$tutanak);
                        break;
                    case 3:
                        $tamir = EdestekTamir::find($kayit->konu_id);
                        $tamir->delete();
                        if($tamir->delete()){
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kayıt Silinemedi', 'text' => 'Kayıt Silinirken Sorun Oluştu.', 'type' => 'error'));
                        }
                        break;
                    case 4:
                        $baski = EdestekBaski::find($kayit->konu_id);
                        $baski->delete();
                        if($baski->delete()){
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kayıt Silinemedi', 'text' => 'Kayıt Silinirken Sorun Oluştu.', 'type' => 'error'));
                        }
                        break;
                }

                $kayit->delete();
                if(!$kayit->delete()){
                    $sonislem = EdestekKayit::where('edestekpersonel_id',$personel->id)->orderBy('tarih','desc')->first();
                    $personel->sonislem_id = $sonislem->id;
                    $personel->sonislemtarihi = $sonislem->tarih;
                    $personel->save();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kayıt Silindi', 'text' => 'Kayıt Başarıyla Silindi.', 'type' => 'success'));
                }else{
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kayıt Silinemedi', 'text' => 'Kayıt Silinirken Sorun Oluştu.', 'type' => 'error'));
                }
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kayıt Bulunamadı', 'text' => 'Silmeye Çalışılan Kayıt Bilgisi Bulunamadı', 'type' => 'error'));
            }
        }catch(Exception $e){
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kayıt Silinemedi', 'text' => 'Kayıt Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }
        
    public function getKurulumekle() {
        $musteriler = EdestekMusteri::orderBy('musteriadi','ASC')->get();
        $kurulumturleri = EdestekKurulumTur::all();
        $personeller = EdestekPersonel::orderBy('adisoyadi','ASC')->get();
        return View::make('edestek.kurulumekle',array('musteriler'=>$musteriler,'kurulumturleri'=>$kurulumturleri,'personeller'=>$personeller))->with('title', 'Yazılım Destek Kurulum Bilgisi Ekleme Ekranı');
    }

    public function postKurulumekle() {
        try{
            $rules = ['options1'=>'required','options2'=>'required','options3'=>'required','sure'=>'required'];
            $validate = Validator::make(Input::all(),$rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $kurulum = new EdestekKurulum;
            $musteri = Input::get('options1');
            $personel = Input::get('options2');
            $kurulumtur = Input::get('options3');
            $sure = Input::get('sure');
            $durum = Input::get('options4');
            $durumaciklama = Input::get('aciklama');
            if (Input::has('tarih')) {
                $tarih = Input::get('tarih');
                $kurulumtarih = date("Y-m-d", strtotime($tarih));
                $kurulum->kurulumtarihi = $kurulumtarih;
            }
            $detay = Input::get('kurulumdetayyeniid');
            If (Input::hasFile('tutanak'))
            {
                $dosya = Input::file('tutanak');
                $uzanti = $dosya->getClientOriginalExtension();
                $isim = Str::slug($musteri).'-'.Str::slug(str_random(5)).'.'.$uzanti;
                $dosya->move('assets/tutanak/',$isim);
                $kurulum->tutanak = $isim;
            }

            $kurulum->edestekmusteri_id = $musteri;
            $kurulum->edestekpersonel_id = $personel;
            $kurulum->edestekkurulumtur_id = $kurulumtur;
            $kurulum->detay = $detay;
            $kurulum->sure = $sure;
            $kurulum->durum = $durum;
            if ($kurulum->save()) {
                $kayit = new EdestekKayit;
                $kayit->edestekmusteri_id = $musteri;
                $kayit->konu_id = $kurulum->id;
                $kayit->edestekkayitkonu_id=2;
                if($durum==0){
                    $yapilanislem=$kurulum->edestekkurulumtur->adi." Bekliyor.";
                }else{
                    $yapilanislem=$kurulum->edestekkurulumtur->adi." Yapıldı.";
                }
                $kayit->yapilanislem = $yapilanislem;
                $kayit->edestekpersonel_id = $personel;
                if (Input::has('tarih')) {
                    $tarih = Input::get('tarih');
                    $kurulumtarih = date("Y-m-d", strtotime($tarih));
                    $kayit->tarih = $kurulumtarih;
                }
                $kayit->sure = $sure;
                $kayit->durum = $durum;
                $kayit->durum_aciklama = $durumaciklama;
                if ($kayit->save()) {
                    $personel=EdestekPersonel::find($kayit->edestekpersonel_id);
                    $personel->sonislem_id=$kayit->id;
                    $personel->sonislemtarihi=$kayit->tarih;
                    $personel->save();
                    return Redirect::to('edestek/edestekkayit')->with(array('mesaj' => 'true', 'title' => 'Kurulum Bilgisi Kaydedildi', 'text' => 'Kurulum Bilgisi Başarıyla Kaydedildi', 'type' => 'success'));
                } else {
                    $kurulum->delete();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kurulum Bilgisi Kaydedilemedi', 'text' => 'Kurulum Bilgisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                }
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kurulum Bilgisi Kaydedilemedi', 'text' => 'Kurulum Bilgisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }
        }catch (Exception $e){
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kurulum Bilgisi Kaydedilemedi', 'text' => 'Kurulum Bilgisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function postKurulumduzenle($id) {
        $rules = ['options1'=>'required','options2'=>'required','options3'=>'required','sure'=>'required'];
        $validate = Validator::make(Input::all(),$rules);
        $messages = $validate->messages();
        if ($validate->fails()) {
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
        }
        $kayit = EdestekKayit::find($id);
        if($kayit->edestekkayitkonu_id==2)
        {
            $kurulum = EdestekKurulum::find($kayit->konu_id);
        }else{
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Kurulum kayıdı bulunamadı', 'type' => 'error'));
        }

        $musteri = Input::get('options1');
        $personel = Input::get('options2');
        $kurulumtur = Input::get('options3');
        $sure = Input::get('sure');
        $durum = Input::get('options4');
        $durumaciklama = Input::get('aciklama');
        if (Input::has('tarih')) {
            $tarih = Input::get('tarih');
            $kurulumtarih = date("Y-m-d", strtotime($tarih));
            $kurulum->kurulumtarihi = $kurulumtarih;
            $kayit->tarih = $kurulumtarih;
        }
        $detay = Input::get('kurulumdetayyeniid');
        If (Input::hasFile('tutanak'))
        {
            File::delete('assets/tutanak/'.$kurulum->tutanak);
            $dosya = Input::file('tutanak');
            $uzanti = $dosya->getClientOriginalExtension();
            $isim = Str::slug($musteri).'-'.Str::slug(str_random(5)).'.'.$uzanti;
            $dosya->move('assets/tutanak/',$isim);
            $kurulum->tutanak = $isim;
        }

        $kurulum->edestekmusteri_id = $musteri;
        $kurulum->edestekpersonel_id = $personel;
        $kurulum->edestekkurulumtur_id = $kurulumtur;
        $kurulum->detay = $detay;
        $kurulum->sure = $sure;
        $kurulum->durum = $durum;
        if ($kurulum->save()) {
            $kayit->edestekmusteri_id = $musteri;
            $kayit->edestekkayitkonu_id=2;
            if($durum==0){
                $yapilanislem=$kurulum->edestekkurulumtur->adi." Bekliyor.";
            }else{
                $yapilanislem=$kurulum->edestekkurulumtur->adi." Yapıldı.";
            }
            $kayit->yapilanislem = $yapilanislem;
            $kayit->edestekpersonel_id = $personel;
            $kayit->sure = $sure;
            $kayit->durum = $durum;
            $kayit->durum_aciklama = $durumaciklama;
            if ($kayit->save()) {
                $personel=EdestekPersonel::find($kayit->edestekpersonel_id);
                $personel->sonislem_id=$kayit->id;
                $personel->sonislemtarihi=$kayit->tarih;
                $personel->save();
                return Redirect::to('edestek/edestekkayit')->with(array('mesaj' => 'true', 'title' => 'Kurulum Bilgisi Güncellendi', 'text' => 'Kurulum Bilgisi Başarıyla Güncellendi', 'type' => 'success'));
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kurulum Bilgisi Güncellenemedi', 'text' => 'Kurulum Bilgisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }
        } else {
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kurulum Bilgisi Güncellenemedi', 'text' => 'Kurulum Bilgisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getMusteri($id){
        $musteri = EdestekMusteri::find($id);
        return Response::json(array('musteri' => $musteri));
    }

    public function getMusteritum($id){
        $musteri = EdestekMusteri::find($id);
        $musteribilgi = $musteri->edestekmusteribilgi;
        $urunler = "";
        $programlar = "";
        $veritabanlari = "";
        if(isset($musteribilgi))
            $musteribilgi->il = $musteribilgi->iller_id ? $musteribilgi->iller->adi : '';
        $sistembilgi = $musteri->edesteksistembilgi;
        if(isset($sistembilgi)){
            if($sistembilgi->urunler)
            {
                $urun = explode(',',$sistembilgi->urunler);
                $urunler = EdestekSistemUrun::whereIn('id', $urun)->get();
                foreach($urunler as $ekliurun) {
                    $ekliurun->uruntur=EdestekUrun::find($ekliurun->edestekurun_id);
                }
            }else {
                $urunler = "";
            }
            if($sistembilgi->programlar)
            {
                $program = explode(',',$sistembilgi->programlar);
                $programlar = EdestekSistemProgram::whereIn('id', $program)->get();
                foreach($programlar as $ekliprogram)
                {
                    if($ekliprogram->edestekprogram_id=='4')
                    {
                        $ekliprogram->firma=EdestekEntegrasyonFirma::find($ekliprogram->edestekentegrasyonfirma_id);
                        $ekliprogram->program=EdestekEntegrasyonProgram::find($ekliprogram->edestekentegrasyonprogram_id);
                        $ekliprogram->tip=EdestekEntegrasyonTip::find($ekliprogram->edestekentegrasyontip_id);
                        $ekliprogram->versiyon=EdestekEntegrasyonVersiyon::find($ekliprogram->edestekentegrasyonversiyon_id);
                    }
                }
            }else {
                $programlar = "";
            }
            if($sistembilgi->veritabanlari)
            {
                $veritabani = explode(',',$sistembilgi->veritabanlari);
                $veritabanlari = EdestekSistemDatabase::whereIn('id', $veritabani)->get();
            }else {
                $veritabanlari = "";
            }
            if($sistembilgi->plasiyer_id)
                $sistembilgi->plasiyer=Plasiyer::find($sistembilgi->plasiyer_id);
        }

        if($musteri->urunturleri)
        {
            $uruntur = explode(',',$musteri->urunturleri);
            $urunturleri = EdestekUrun::whereIn('id',$uruntur)->get();
        }else {
            $urunturleri = "";
         }

        $gorusmeler= EdestekKayit::where('edestekmusteri_id',$id)->orderBy('tarih','DESC')->take(5)->get();
        foreach($gorusmeler as $gorusme){
            $gorusme->kayitkonu=$gorusme->edestekkayitkonu->adi;
            $gorusme->personel=$gorusme->edestekpersonel->adisoyadi;
            $gorusme->tarih=date("d-m-Y",strtotime ($gorusme->tarih));
            if($gorusme->durum==0)
                $gorusme->durum="Bekliyor";
            elseif($gorusme->durum==1)
                $gorusme->durum="Tamamlandı";
            elseif($gorusme->durum==2)
                $gorusme->durum="Devredildi";
            elseif($gorusme->durum==3)
                $gorusme->durum="İptal Edildi";
        }
        return Response::json(array('musteribilgi' => $musteribilgi,'sistembilgi'=>$sistembilgi,'urunler' => $urunler,'urunturleri'=>$urunturleri,'gorusmeler'=>$gorusmeler,'programlar'=>$programlar,'veritabanlari'=>$veritabanlari));
    }

    public function getHizlimusteriekle() {
        $musteriadi=Input::get('musteri');
        If(EdestekMusteri::where('musteriadi',$musteriadi)->first())
        {
            return Response::json(array('durum' => 0,'title' => 'Doğrulama Hatası', 'text' => 'Bu Müşteri adı mevcut.', 'type' => 'warning'));
        }

        If(EdestekMusteri::onlyTrashed()->where('musteriadi',$musteriadi)->first())
        {
            $musteri=EdestekMusteri::onlyTrashed()->where('musteriadi',$musteriadi)->first();
            $musteri->deleted_at=NULL;
            if ($musteri->save()) {
                $musteriler = EdestekMusteri::orderBy('musteriadi','ASC')->get();
                return Response::json(array('durum' => 2,'musteriler'=>$musteriler,'title' => 'Müşteri Geri Getirildi', 'text' => 'Müşterinin Eski Bilgileri Başarıyla Geri Getirildi.', 'type' => 'success'));
            } else {
                return Response::json(array('durum' => 3,'title' => 'Müşteri Eklenemedi', 'text' => 'Müşteri Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }
        }
        $musteri = new EdestekMusteri;
        $musteri->musteriadi = $musteriadi;

        if ($musteri->save()) {
            $musteriler = EdestekMusteri::orderBy('musteriadi','ASC')->get();
            return Response::json(array('durum' => 1,'musteriler'=>$musteriler,'title' => 'Müşteri Eklendi', 'text' => 'Müşteri Başarıyla Eklendi', 'type' => 'success'));
        } else {
            return Response::json(array('durum' => 3, 'title' => 'Müşteri Eklenemedi', 'text' => 'Müşteri Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getHizlimusteriduzenle() {
        $id=Input::get('musteriid');
        $musteriadi=Input::get('musteri');
        $musteri = EdestekMusteri::find($id);
        If(EdestekMusteri::where('musteriadi',$musteriadi)->where('id','<>',$id)->first())
        {
            return Response::json(array('durum' => 0,'title' => 'Doğrulama Hatası', 'text' => 'Bu Müşteri adı mevcut.', 'type' => 'warning'));
        }
        If(EdestekMusteri::onlyTrashed()->where('musteriadi',$musteriadi)->where('id','<>',$id)->first())
        {
            return Response::json(array('durum' => 0,'title' => 'Doğrulama Hatası', 'text' => 'Bu müşteri adı mevcut ve silinmiş gözüküyor.', 'type' => 'error'));
        }

        $musteri->musteriadi = $musteriadi;

        if ($musteri->save()) {
            $musteriler = EdestekMusteri::orderBy('musteriadi','ASC')->get();
            return Response::json(array('durum' => 1,'musteriler'=>$musteriler,'title' => 'Müşteri Güncellendi', 'text' => 'Müşteri Başarıyla Güncellendi', 'type' => 'success'));
        } else {
            return Response::json(array('durum' => 3, 'title' => 'Müşteri Güncellenemedi', 'text' => 'Müşteri Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getKurulumtur($id){
        $kurulumtur = EdestekKurulumTur::find($id);
        return Response::json(array('kurulumtur' => $kurulumtur));
    }

    public function getKurulumturekle() {
        $adi=Input::get('tur');
        If(EdestekKurulumTur::where('adi',$adi)->first())
        {
            return Response::json(array('durum' => 0,'title' => 'Doğrulama Hatası', 'text' => 'Bu kurulum türü mevcut.', 'type' => 'warning'));
        }
        $kurulumtur = new EdestekKurulumTur;
        $kurulumtur->adi = $adi;

        if ($kurulumtur->save()) {
            $turler = EdestekKurulumTur::all();
            return Response::json(array('durum' => 1,'turler'=>$turler,'title' => 'Kurulum Türü Eklendi', 'text' => 'Kurulum Türü Başarıyla Eklendi', 'type' => 'success'));
        } else {
            return Response::json(array('durum' => 3, 'title' => 'Kurulum Türü Eklenemedi', 'text' => 'Kurulum Türü Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getKurulumturduzenle() {
        $id=Input::get('turid');
        $adi=Input::get('tur');
        $kurulumtur = EdestekKurulumTur::find($id);
        If(EdestekKurulumTur::where('adi',$adi)->where('id','<>',$id)->first())
        {
            return Response::json(array('durum' => 0,'title' => 'Doğrulama Hatası', 'text' => 'Bu kurulum türü mevcut.', 'type' => 'warning'));
        }
        $kurulumtur->adi = $adi;

        if ($kurulumtur->save()) {
            $turler = EdestekKurulumTur::all();
            return Response::json(array('durum' => 1,'turler'=>$turler,'title' => 'Kurulum Türü Güncellendi', 'text' => 'Kurulum Türü Başarıyla Güncellendi', 'type' => 'success'));
        } else {
            return Response::json(array('durum' => 3, 'title' => 'Kurulum Türü Güncellenemedi', 'text' => 'Kurulum Türü Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getTamirekle() {
        $musteriler = EdestekMusteri::orderBy('musteriadi','ASC')->get();
        $tamirurunler = EdestekTamirUrun::all();
        $tamirislemler = EdestekTamirIslem::all();
        $personeller = EdestekPersonel::orderBy('adisoyadi','ASC')->get();
        return View::make('edestek.tamirekle',array('musteriler'=>$musteriler,'tamirurunler'=>$tamirurunler,'tamirislemler'=>$tamirislemler,'personeller'=>$personeller))->with('title', 'Yazılım Destek Tamir Bakım Bilgisi Ekleme Ekranı');
    }

    public function postTamirekle() {
        try {
            $rules = ['options1' => 'required', 'options2' => 'required', 'options3' => 'required', 'options4' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $tamir = new EdestekTamir;
            $musteri = Input::get('options1');
            $uruncinsi = Input::get('options2');
            $urunadi = Input::get('urunadi');
            $islem = Input::get('options3');
            $personel = Input::get('options4');
            $sure = Input::get('sure');
            $durum = Input::get('options5');
            $durumaciklama = Input::get('aciklama');
            if (Input::has('gelistarih')) {
                $tarih = Input::get('gelistarih');
                $gelistarih = date("Y-m-d", strtotime($tarih));
                $tamir->gelistarihi = $gelistarih;
            }
            $detay = Input::get('tamirdetayyeniid');
            if (Input::has('sevktarih')) {
                $tarih = Input::get('sevktarih');
                $sevktarih = date("Y-m-d", strtotime($tarih));
                $tamir->sevktarihi = $sevktarih;
            }
            If (Input::hasFile('tutanak')) {
                $dosya = Input::file('tutanak');
                $uzanti = $dosya->getClientOriginalExtension();
                $isim = Str::slug($musteri) . '-' . Str::slug(str_random(5)) . '.' . $uzanti;
                $dosya->move('assets/tutanak/', $isim);
                $tamir->tutanak = $isim;
            }

            $tamir->edestekmusteri_id = $musteri;
            $tamir->edestektamirurun_id = $uruncinsi;
            $tamir->urunadi = $urunadi;
            $tamir->edestekpersonel_id = $personel;
            $tamir->edestektamirislem_id = $islem;
            $tamir->detay = $detay;
            $tamir->sure = $sure;
            $tamir->durum = $durum;
            if ($tamir->save()) {
                $kayit = new EdestekKayit;
                $kayit->edestekmusteri_id = $musteri;
                $kayit->konu_id = $tamir->id;
                $kayit->edestekkayitkonu_id = 3;
                if ($durum == 0) {
                    $yapilanislem = $tamir->edestektamirislem->adi . " Bekliyor.";
                } else {
                    $yapilanislem = $tamir->edestektamirislem->adi . " Yapıldı.";
                }
                $kayit->yapilanislem = $yapilanislem;
                $kayit->edestekpersonel_id = $personel;
                if (Input::has('gelistarih')) {
                    $tarih = Input::get('gelistarih');
                    $tamirtarih = date("Y-m-d", strtotime($tarih));
                    $kayit->tarih = $tamirtarih;
                }
                $kayit->sure = $sure;
                $kayit->durum = $durum;
                $kayit->durum_aciklama = $durumaciklama;
                if ($kayit->save()) {
                    $personel = EdestekPersonel::find($kayit->edestekpersonel_id);
                    $personel->sonislem_id = $kayit->id;
                    $personel->sonislemtarihi = $kayit->tarih;
                    $personel->save();
                    return Redirect::to('edestek/edestekkayit')->with(array('mesaj' => 'true', 'title' => 'Tamir Bakım Bilgisi Kaydedildi', 'text' => 'Tamir Bakım Bilgisi Başarıyla Kaydedildi', 'type' => 'success'));
                } else {
                    $tamir->delete();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Tamir Bakım Bilgisi Kaydedilemedi', 'text' => 'Tamir Bakım Bilgisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                }
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Tamir Bakım Bilgisi Kaydedilemedi', 'text' => 'Tamir Bakım Bilgisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }
        }catch (Exception $e){
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Tamir Bakım Bilgisi Kaydedilemedi', 'text' => 'Tamir Bakım Bilgisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function postTamirduzenle($id){
        $rules = ['options1'=>'required','options2'=>'required','options3'=>'required','options4'=>'required'];
        $validate = Validator::make(Input::all(),$rules);
        $messages = $validate->messages();
        if ($validate->fails()) {
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
        }
        $kayit = EdestekKayit::find($id);
        if($kayit->edestekkayitkonu_id==3)
        {
            $tamir = EdestekTamir::find($kayit->konu_id);
        }else{
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Tamir bakım kayıdı bulunamadı', 'type' => 'error'));
        }

        $musteri = Input::get('options1');
        $uruncinsi = Input::get('options2');
        $urunadi = Input::get('urunadi');
        $islem = Input::get('options3');
        $personel = Input::get('options4');
        $sure = Input::get('sure');
        $durum = Input::get('options5');
        $durumaciklama = Input::get('aciklama');
        if (Input::has('gelistarih')) {
            $tarih = Input::get('gelistarih');
            $gelistarih = date("Y-m-d", strtotime($tarih));
            $tamir->gelistarihi = $gelistarih;
        }
        $detay = Input::get('tamirdetayid');
        if (Input::has('sevktarih')) {
            $tarih = Input::get('sevktarih');
            $sevktarih = date("Y-m-d", strtotime($tarih));
            $tamir->sevktarihi = $sevktarih;
        }
		If (Input::hasFile('tutanak'))
        {
            $dosya = Input::file('tutanak');
            $uzanti = $dosya->getClientOriginalExtension();
            $isim = Str::slug($musteri).'-'.Str::slug(str_random(5)).'.'.$uzanti;
            $dosya->move('assets/tutanak/',$isim);
            $tamir->tutanak = $isim;
        }

        $tamir->edestekmusteri_id = $musteri;
        $tamir->edestektamirurun_id = $uruncinsi;
        $tamir->urunadi = $urunadi;
        $tamir->edestekpersonel_id = $personel;
        $tamir->edestektamirislem_id = $islem;
        $tamir->detay = $detay;
        $tamir->sure = $sure;
        $tamir->durum = $durum;
        if ($tamir->save()) {
            $kayit->edestekmusteri_id = $musteri;
            $kayit->konu_id = $tamir->id;
            $kayit->edestekkayitkonu_id=3;
            if($durum==0){
                $yapilanislem=$tamir->edestektamirislem->adi." Bekliyor.";
            }else{
                $yapilanislem=$tamir->edestektamirislem->adi." Yapıldı.";
            }
            $kayit->yapilanislem = $yapilanislem;
            $kayit->edestekpersonel_id = $personel;
            if (Input::has('gelistarih')) {
                $tarih = Input::get('gelistarih');
                $tamirtarih = date("Y-m-d", strtotime($tarih));
                $kayit->tarih = $tamirtarih;
            }
            $kayit->sure = $sure;
            $kayit->durum = $durum;
            $kayit->durum_aciklama = $durumaciklama;
            if ($kayit->save()) {
                $personel=EdestekPersonel::find($kayit->edestekpersonel_id);
                $personel->sonislem_id=$kayit->id;
                $personel->sonislemtarihi=$kayit->tarih;
                $personel->save();
                return Redirect::to('edestek/edestekkayit')->with(array('mesaj' => 'true', 'title' => 'Tamir Bakım Bilgisi Güncellendi', 'text' => 'Tamir Bakım Bilgisi Başarıyla Güncellendi', 'type' => 'success'));
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Tamir Bakım Bilgisi Güncellenemedi', 'text' => 'Tamir Bakım Bilgisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }
        } else {
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Tamir Bakım Bilgisi Güncellenemedi', 'text' => 'Tamir Bakım Bilgisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }
    
    public function getUrunislemler($id){
        $islem = EdestekTamirIslem::where('edestektamirurun_id',$id)->get();
        return Response::json(array('islem' => $islem));
    }
    
    public function getUrunislem($id){
        $islem = EdestekTamirIslem::where('id',$id)->first();
        $islem->tamirurun = EdestekTamirUrun::find($islem->edestektamirurun_id);
        return Response::json(array('islem' => $islem));
    }
    
    public function getUruncinsi($id){
        $tamirurun = EdestekTamirUrun::find($id);
        return Response::json(array('tamirurun' => $tamirurun));
    }
    
    public function getUruncinsiekle() {

        $adi = Input::get('cins');
        If(EdestekTamirUrun::where('adi',$adi)->first())
        {
            return Response::json(array('durum' => 0,'title' => 'Doğrulama Hatası', 'text' => 'Bu ürün cinsi mevcut.', 'type' => 'warning'));
        }

        $tamirurun = new EdestekTamirUrun;
        $tamirurun->adi = $adi;
        
        if ($tamirurun->save()) {
            $urunler = EdestekTamirUrun::all();
            return Response::json(array('durum' => 1,'urunler'=>$urunler,'title' => 'Ürün Cinsi Eklendi', 'text' => 'Ürün Cinsi Başarıyla Eklendi', 'type' => 'success'));
        } else {
            return Response::json(array('durum' => 3, 'title' => 'Ürün Cinsi Eklenemedi', 'text' => 'Ürün Cinsi Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }
    
    public function getUruncinsiduzenle() {
        $id = Input::get('cinsid');
        $adi = Input::get('cins');
        $tamirurun = EdestekTamirUrun::find($id);
        If(EdestekTamirUrun::where('adi',$adi)->where('id','<>',$id)->first())
        {
            return Response::json(array('durum' => 0,'title' => 'Doğrulama Hatası', 'text' => 'Bu ürün cinsi mevcut.', 'type' => 'warning'));
        }
        
        $tamirurun->adi = $adi;
        
        if ($tamirurun->save()) {
            $urunler = EdestekTamirUrun::all();
            return Response::json(array('durum' => 1,'urunler'=>$urunler,'title' => 'Ürün Cinsi Güncellendi', 'text' => 'Ürün Cinsi Başarıyla Güncellendi', 'type' => 'success'));
        } else {
            return Response::json(array('durum' => 3, 'title' => 'Ürün Cinsi Güncellenemedi', 'text' => 'Ürün Cinsi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getUrunislemekle() {

        $tamirurun = Input::get('cins');
        $adi = Input::get('islem');
        If(EdestekTamirIslem::where('adi',$adi)->where('edestektamirurun_id',$tamirurun)->first())
        {
            return Response::json(array('durum' => 0,'title' => 'Doğrulama Hatası', 'text' => 'Bu işlem mevcut.', 'type' => 'warning'));
        }

        $islem = new EdestekTamirIslem;
		$islem->edestektamirurun_id=$tamirurun;
        $islem->adi = $adi;

        if ($islem->save()) {
            $islemler = EdestekTamirIslem::where('edestektamirurun_id',$tamirurun)->get();
            return Response::json(array('durum' => 1,'islemler'=>$islemler,'title' => 'Yapılan İşlem Eklendi', 'text' => 'Yapılan İşlem Başarıyla Eklendi', 'type' => 'success'));
        } else {
            return Response::json(array('durum' => 3, 'title' => 'Yapılan İşlem Eklenemedi', 'text' => 'Yapılan İşlem Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getUrunislemduzenle() {
        $id = Input::get('islemid');
        $tamirurun = Input::get('cins');
        $adi = Input::get('islem');
        $islem = EdestekTamirIslem::find($id);
        If(EdestekTamirIslem::where('adi',$adi)->where('id','<>',$id)->first())
        {
            return Response::json(array('durum' => 0,'title' => 'Doğrulama Hatası', 'text' => 'Bu işlem mevcut.', 'type' => 'warning'));
        }

        $islem->adi = $adi;

        if ($islem->save()) {
            $islemler = EdestekTamirIslem::where('edestektamirurun_id',$tamirurun)->get();
            return Response::json(array('durum' => 1,'islemler'=>$islemler,'title' => 'Yapılan İşlem Güncellendi', 'text' => 'Yapılan İşlem Başarıyla Güncellendi', 'type' => 'success'));
        } else {
            return Response::json(array('durum' => 3, 'title' => 'Yapılan İşlem Güncellenemedi', 'text' => 'Yapılan İşlem Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getBaskiekle() {
        $musteriler = EdestekMusteri::orderBy('musteriadi','ASC')->get();
        $plasiyerler = Plasiyer::orderBy('plasiyeradi','ASC')->get();
        $baskilar = EdestekKartBaski::all();
        $personeller = EdestekPersonel::orderBy('adisoyadi','ASC')->get();
        return View::make('edestek.baskiekle',array('musteriler'=>$musteriler,'plasiyerler'=>$plasiyerler,'baskilar'=>$baskilar,'personeller'=>$personeller))->with('title', 'Yazılım Destek Kart Baskı Bilgisi Ekleme Ekranı');
    }

    public function postBaskiekle()
    {
        try {
            $rules = ['options1' => 'required', 'options2' => 'required', 'options3' => 'required', 'options4' => 'required', 'siparistarih' => 'required', 'miktar' => 'required', 'sure' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $baski = new EdestekBaski;
            $musteri = Input::get('options1');
            $plasiyer = Input::get('options2');
            $kartbaski = Input::get('options3');
            $miktar = Input::get('miktar');
            $personel = Input::get('options4');
            $sure = Input::get('sure');
            $durum = Input::get('options5');
            $durumaciklama = Input::get('aciklama');
            if (Input::has('siparistarih')) {
                $tarih = Input::get('siparistarih');
                $siparistarih = date("Y-m-d", strtotime($tarih));
                $baski->siparistarihi = $siparistarih;
            }
            if (Input::has('teslimtarih')) {
                $tarih = Input::get('teslimtarih');
                $teslimtarih = date("Y-m-d", strtotime($tarih));
                $baski->teslimtarihi = $teslimtarih;
            }

            $baski->edestekmusteri_id = $musteri;
            $baski->plasiyer_id = $plasiyer;
            $baski->miktar = $miktar;
            $baski->edestekkartbaski_id = $kartbaski;
            $baski->edestekpersonel_id = $personel;
            $baski->sure = $sure;
            $baski->durum = $durum;
            if ($baski->save()) {
                $kayit = new EdestekKayit;
                $kayit->edestekmusteri_id = $musteri;
                $kayit->konu_id = $baski->id;
                $kayit->edestekkayitkonu_id = 4;
                if ($durum == 0) {
                    $yapilanislem = $baski->miktar . " Adet Kart Siparişi Açıldı.";
                } else {
                    $yapilanislem = $baski->miktar . " Adet Kart Siparişi Tamamlandı.";
                }
                $kayit->yapilanislem = $yapilanislem;
                $kayit->edestekpersonel_id = $personel;
                if (Input::has('siparistarih')) {
                    $tarih = Input::get('siparistarih');
                    $baskitarih = date("Y-m-d", strtotime($tarih));
                    $kayit->tarih = $baskitarih;
                }
                $kayit->sure = $sure;
                $kayit->durum = $durum;
                $kayit->durum_aciklama = $durumaciklama;
                if ($kayit->save()) {
                    $personel = EdestekPersonel::find($kayit->edestekpersonel_id);
                    $personel->sonislem_id = $kayit->id;
                    $personel->sonislemtarihi = $kayit->tarih;
                    $personel->save();
                    return Redirect::to('edestek/edestekkayit')->with(array('mesaj' => 'true', 'title' => 'Kart Baskı Bilgisi Kaydedildi', 'text' => 'Kart Baskı Bilgisi Başarıyla Kaydedildi', 'type' => 'success'));
                } else {
                    $baski->delete();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kart Baskı Bilgisi Kaydedilemedi', 'text' => 'Kart Baskı Bilgisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                }
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kart Baskı Bilgisi Kaydedilemedi', 'text' => 'Kart Baskı Bilgisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kart Baskı Bilgisi Kaydedilemedi', 'text' => 'Kart Baskı Bilgisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function postBaskiduzenle($id) {
        $rules = ['options1'=>'required','options2'=>'required','options3'=>'required','options4'=>'required','siparistarih'=>'required','miktar'=>'required','sure'=>'required'];
        $validate = Validator::make(Input::all(),$rules);
        $messages = $validate->messages();
        if ($validate->fails()) {
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
        }
        $kayit = EdestekKayit::find($id);
        if($kayit->edestekkayitkonu_id==4)
        {
            $baski = EdestekBaski::find($kayit->konu_id);
        }else{
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Kart Baskı kayıdı bulunamadı', 'type' => 'error'));
        }

        $musteri = Input::get('options1');
        $plasiyer = Input::get('options2');
        $kartbaski = Input::get('options3');
        $miktar = Input::get('miktar');
        $personel = Input::get('options4');
        $sure = Input::get('sure');
        $durum = Input::get('options5');
        $durumaciklama = Input::get('aciklama');
        if (Input::has('siparistarih')) {
            $tarih = Input::get('siparistarih');
            $siparistarih = date("Y-m-d", strtotime($tarih));
            $baski->siparistarihi = $siparistarih;
        }
        if (Input::has('teslimtarih')) {
            $tarih = Input::get('teslimtarih');
            $teslimtarih = date("Y-m-d", strtotime($tarih));
            $baski->teslimtarihi = $teslimtarih;
        }

        $baski->edestekmusteri_id = $musteri;
        $baski->plasiyer_id = $plasiyer;
        $baski->miktar = $miktar;
        $baski->edestekkartbaski_id = $kartbaski;
        $baski->edestekpersonel_id = $personel;
        $baski->sure = $sure;
        $baski->durum = $durum;
        if ($baski->save()) {
            $kayit->edestekmusteri_id = $musteri;
            $kayit->konu_id = $baski->id;
            $kayit->edestekkayitkonu_id=4;
            if($durum==0){
                $yapilanislem=$baski->miktar." Adet Kart Siparişi Açıldı.";
            }else{
                $yapilanislem=$baski->miktar." Adet Kart Siparişi Tamamlandı.";
            }
            $kayit->yapilanislem = $yapilanislem;
            $kayit->edestekpersonel_id = $personel;
            if (Input::has('siparistarih')) {
                $tarih = Input::get('siparistarih');
                $baskitarih = date("Y-m-d", strtotime($tarih));
                $kayit->tarih = $baskitarih;
            }
            $kayit->sure = $sure;
            $kayit->durum = $durum;
            $kayit->durum_aciklama = $durumaciklama;
            if ($kayit->save()) {
                $personel=EdestekPersonel::find($kayit->edestekpersonel_id);
                $personel->sonislem_id=$kayit->id;
                $personel->sonislemtarihi=$kayit->tarih;
                $personel->save();
                return Redirect::to('edestek/edestekkayit')->with(array('mesaj' => 'true', 'title' => 'Kart Baskı Bilgisi Güncellendi', 'text' => 'Kart Baskı Bilgisi Başarıyla Güncellendi', 'type' => 'success'));
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kart Baskı Bilgisi Güncellenemedi', 'text' => 'Kart Baskı Bilgisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }
        } else {
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kart Baskı Bilgisi Güncellenemedi', 'text' => 'Kart Baskı Bilgisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getMusteribaski($id){
        $musteri = EdestekMusteri::where('id',$id)->first();
        $baski="";
        if($musteri->edestekbaskiidler!=""){
            $baskilar = explode(',', $musteri->edestekbaskiidler);
            $baski = EdestekKartBaski::whereIn('id',$baskilar)->get();
        }
        return Response::json(array('baski' => $baski));
    }

    public function getKartbaski($id){
        $kartbaski = EdestekKartBaski::find($id);
        return Response::json(array('kartbaski' => $kartbaski));
    }

    public function getGorusmeekle() {
        $musteriler = EdestekMusteri::orderBy('musteriadi','ASC')->get();
        $konular = EdestekKonu::orderBy('adi','ASC')->get();
        $detaylar = EdestekKonuDetay::orderBy('detay','ASC')->get();
        $personeller = EdestekPersonel::orderBy('adisoyadi','ASC')->get();
        $hatacozumleri = EdestekhataCozum::take(10)->get();
        return View::make('edestek.gorusmeekle',array('musteriler'=>$musteriler,'konular'=>$konular,'detaylar'=>$detaylar,'personeller'=>$personeller,'hatacozumleri'=>$hatacozumleri))->with('title', 'Yazılım Destek Görüşme Bilgisi Ekleme Ekranı');
    }

    public function getHatacozum($id){
        $cozum = EdestekHataCozum::find($id);
        $cozum->konu= EdestekKonu::find($cozum->edestekkonu_id);
        $cozum->konudetay= EdestekKonuDetay::find($cozum->edestekkonudetay_id);
        return Response::json(array('cozum' => $cozum));
    }

    public function getHatacozumlistesi(){
        $arama = Input::get('hata');
        $cozum = EdestekHataCozum::where('problem','LIKE','%'.$arama.'%')->take(20)->get();
        return Response::json(array('cozum' => $cozum));
    }

    public function getProblem($arama=false){
        $problem = EdestekHataCozum::where('problem','LIKE','%'.$arama.'%')->take(20)->get();
        return Response::json(array('problem' => $problem));
    }

    public function getProblemcomplete($arama=false){
        if($_GET['term'])
            $arama=$_GET['term'];
        $problemler = EdestekHataCozum::where('problem','LIKE','%'.$arama.'%')->take(10)->get();
        $array=array();
        foreach($problemler as $problem)
        {
            array_push($array,array('value'=>$problem->problem,'label'=>$problem->problem));
        }
        return Response::json($array);
    }

    public function getDevredilecekler($arama=false){

        $devredilen = EdestekPersonel::where('id','!=',$arama)->get();
        return Response::json(array('devredilen' => $devredilen));
    }

    public function postGorusmeekle() {
        try {
            $rules = ['options1' => 'required', 'options2' => 'required', 'options3' => 'required', 'options4' => 'required', 'tarih' => 'required', 'problem' => 'required', 'sure' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $gorusme = new EdestekGorusme;
            $musteri = Input::get('options1');
            $konu = Input::get('options2');
            $gorusmekonu = EdestekKonu::find($konu);
            $detay = Input::get('options3');
            $gorusmedetay = EdestekKonuDetay::find($detay);
            $personel = Input::get('options4');
            $durum = Input::get('options5');
            $durumaciklama = Input::get('aciklama');
            $yetkili = Input::get('yetkiliadi');
            $telefon = Input::get('telefon');
            $problem = Input::get('problem');
            $sure = Input::get('sure');
            $cozum = Input::get('gorusmedetayyeniid');
            if (Input::has('tarih')) {
                $tarih = Input::get('tarih');
                $gorusmetarih = date("Y-m-d", strtotime($tarih));
                $gorusme->tarih = $gorusmetarih;
            }

            $gorusme->edestekmusteri_id = $musteri;
            $gorusme->yetkiliadi = $yetkili;
            $gorusme->yetkilitel = $telefon;
            $gorusme->edestekkonu_id = $konu;
            $gorusme->edestekkonudetay_id = $detay;
            $gorusme->problem = $problem;
            $gorusme->cozum = $cozum;
            if (Input::has('devreden')) {
                $devreden = Input::get('devreden');
                $gorusme->devretmetarihi = date('Y-m-d H:i:s');
                $gorusme->edestekpersonel_id = $devreden;
                $gorusme->devreden_id = $personel;
            } else {
                $gorusme->edestekpersonel_id = $personel;
            }
            $gorusme->sure = $sure;
            $gorusme->durum = $durum;
            if ($gorusme->save()) {
                $kayit = new EdestekKayit;
                $kayit->edestekmusteri_id = $musteri;
                $kayit->konu_id = $gorusme->id;
                $kayit->edestekkayitkonu_id = 1;
                $kayit->yapilanislem = $gorusme->edestekkonu->adi . " Konusunda Görüşme Yapıldı.";
                $kayit->edestekpersonel_id = $gorusme->edestekpersonel_id;
                $kayit->tarih = $gorusme->tarih;
                $kayit->sure = $sure;
                $kayit->durum = $durum;
                $kayit->durum_aciklama = $durumaciklama;
                if ($kayit->save()) {
                    $personel = EdestekPersonel::find($kayit->edestekpersonel_id);
                    $personel->sonislem_id = $kayit->id;
                    $personel->sonislemtarihi = $kayit->tarih;
                    $personel->save();
                    $edestekmusteri = EdestekMusteri::find($musteri);
                    $musteribilgi = EdestekMusteriBilgi::find($edestekmusteri->edestekmusteribilgi_id);
                    if (isset($musteribilgi)) {
                        if (is_null($musteribilgi->yetkiliadi) || is_null($musteribilgi->yetkilitel) || $musteribilgi->yetkiliadi == "" || $musteribilgi->yetkilitel == "") {
                            $musteribilgi->yetkiliadi = $yetkili;
                            $musteribilgi->yetkilitel = $telefon;
                            $musteribilgi->save();
                        }
                    } else {
                        $musteribilgi = new EdestekMusteriBilgi;
                        $musteribilgi->yetkiliadi = $yetkili;
                        $musteribilgi->yetkilitel = $telefon;
                        $musteribilgi->save();
                        $edestekmusteri->edestekmusteribilgi_id = $musteribilgi->id;
                    }
                    if (is_null(EdestekHataCozum::where('problem', $problem)->first()) && $durum == '1') {
                        return Redirect::to('edestek/edestekkayit')->with(array('mesaj' => 'true', 'title' => 'Görüşme Bilgisi Kaydedildi', 'text' => 'Görüşme Bilgisi Başarıyla Kaydedildi', 'type' => 'success', 'konu' => $gorusmekonu->adi, 'konuid' => $gorusmekonu->id, 'detay' => $gorusmedetay->detay, 'detayid' => $gorusmedetay->id, 'problem' => $problem, 'cozum' => $cozum));
                    }
                    return Redirect::to('edestek/edestekkayit')->with(array('mesaj' => 'true', 'title' => 'Görüşme Bilgisi Kaydedildi', 'text' => 'Görüşme Bilgisi Başarıyla Kaydedildi', 'type' => 'success'));
                } else {
                    $gorusme->delete();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Görüşme Bilgisi Kaydedilemedi', 'text' => 'Görüşme Bilgisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                }
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Görüşme Bilgisi Kaydedilemedi', 'text' => 'Görüşme Bilgisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Görüşme Bilgisi Kaydedilemedi', 'text' => 'Görüşme Bilgisi Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function postGorusmeduzenle($id) {
        try {
            $rules = ['options1' => 'required', 'options2' => 'required', 'options3' => 'required', 'options4' => 'required', 'tarih' => 'required', 'problem' => 'required', 'sure' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $kayit = EdestekKayit::find($id);
            if ($kayit->edestekkayitkonu_id == 1) {
                $gorusme = EdestekGorusme::find($kayit->konu_id);
            } else {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'Müşteri Görüşme kayıdı bulunamadı', 'type' => 'error'));
            }

            $musteri = Input::get('options1');
            $konu = Input::get('options2');
            $gorusmekonu = EdestekKonu::find($konu);
            $detay = Input::get('options3');
            $gorusmedetay = EdestekKonuDetay::find($detay);
            $personel = Input::get('options4');
            $durum = Input::get('options5');
            $durumaciklama = Input::get('aciklama');
            $yetkili = Input::get('yetkiliadi');
            $telefon = Input::get('telefon');
            $problem = Input::get('problem');
            $sure = Input::get('sure');
            $cozum = Input::get('gorusmedetayid');
            if (Input::has('tarih')) {
                $tarih = Input::get('tarih');
                $gorusmetarih = date("Y-m-d", strtotime($tarih));
                $gorusme->tarih = $gorusmetarih;
            }

            $gorusme->edestekmusteri_id = $musteri;
            $gorusme->yetkiliadi = $yetkili;
            $gorusme->yetkilitel = $telefon;
            $gorusme->edestekkonu_id = $konu;
            $gorusme->edestekkonudetay_id = $detay;
            $gorusme->problem = $problem;
            $gorusme->cozum = $cozum;
            if (Input::has('devreden')) {
                $devreden = Input::get('devreden');
                $gorusme->devretmetarihi = date('Y-m-d H:i:s');
                $gorusme->edestekpersonel_id = $devreden;
                $gorusme->devreden_id = $personel;
            } else {
                $gorusme->edestekpersonel_id = $personel;
            }
            $gorusme->sure = $sure;
            $gorusme->durum = $durum;
            if ($gorusme->save()) {
                $kayit->edestekmusteri_id = $musteri;
                $kayit->konu_id = $gorusme->id;
                $kayit->edestekkayitkonu_id = 1;
                $kayit->yapilanislem = $gorusme->edestekkonu->adi . " Konusunda Görüşme Yapıldı.";
                $kayit->edestekpersonel_id = $gorusme->edestekpersonel_id;
                $kayit->tarih = $gorusme->tarih;
                $kayit->sure = $sure;
                $kayit->durum = $durum;
                $kayit->durum_aciklama = $durumaciklama;
                if ($kayit->save()) {
                    $personel = EdestekPersonel::find($kayit->edestekpersonel_id);
                    $personel->sonislem_id = $kayit->id;
                    $personel->sonislemtarihi = $kayit->tarih;
                    $personel->save();
                    $edestekmusteri = EdestekMusteri::find($musteri);
                    $musteribilgi = EdestekMusteriBilgi::find($edestekmusteri->edestekmusteribilgi_id);
                    if (isset($musteribilgi)) {
                        if (is_null($musteribilgi->yetkiliadi) || is_null($musteribilgi->yetkilitel) || $musteribilgi->yetkiliadi == "" || $musteribilgi->yetkilitel == "") {
                            $musteribilgi->yetkiliadi = $yetkili;
                            $musteribilgi->yetkilitel = $telefon;
                            $musteribilgi->save();
                        }
                    } else {
                        $musteribilgi = new EdestekMusteriBilgi;
                        $musteribilgi->yetkiliadi = $yetkili;
                        $musteribilgi->yetkilitel = $telefon;
                        $musteribilgi->save();
                        $edestekmusteri->edestekmusteribilgi_id = $musteribilgi->id;
                    }

                    if (is_null(EdestekHataCozum::where('problem', $problem)->first()) && $durum == '1') {
                        return Redirect::to('edestek/edestekkayit')->with(array('mesaj' => 'true', 'title' => 'Görüşme Bilgisi Güncellendi', 'text' => 'Görüşme Bilgisi Başarıyla Güncellendi', 'type' => 'success', 'konu' => $gorusmekonu->adi, 'konuid' => $gorusmekonu->id, 'detay' => $gorusmedetay->detay, 'detayid' => $gorusmedetay->id, 'problem' => $problem, 'cozum' => $cozum));
                    }
                    return Redirect::to('edestek/edestekkayit')->with(array('mesaj' => 'true', 'title' => 'Görüşme Bilgisi Güncellendi', 'text' => 'Görüşme Bilgisi Başarıyla Güncellendi', 'type' => 'success'));
                } else {
                    $gorusme->delete();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Görüşme Bilgisi Güncellenemedi', 'text' => 'Görüşme Bilgisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                }
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Görüşme Bilgisi Güncellenemedi', 'text' => 'Görüşme Bilgisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Görüşme Bilgisi Güncellenemedi', 'text' => 'Görüşme Bilgisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getIslemler() {
        $islemler = EdestekDuzenliIslem::all();
        return View::make('edestek.islemler',array('islemler'=>$islemler))->with('title', 'Yazılım Destek Düzenli İşlemler Ekranı');
    }

    public function getDuzenliislemkontrol(){

        try {
            $duzenliislem = EdestekDuzenliIslem::where('durum', 1)->get();
            foreach ($duzenliislem as $islem) {
                $aralikzaman = $islem->aralik * EdestekDuzenliIslem::aralikkatsayi($islem->araliktip);
                if (is_null($islem->sonislemtarihi)) {
                    $comparison = DB::select(DB::raw("SELECT CASE WHEN dateadd(day,0,'" . $islem->baslangictarih . "') <= getdate() THEN 1 ELSE 0 END AS compare"));
                } else {
                    $comparison = DB::select(DB::raw("SELECT CASE WHEN dateadd(day," . $aralikzaman . ",'" . $islem->sonislemtarihi . "') <= getdate() THEN 1 ELSE 0 END AS compare"));
                }
                if ($comparison[0]->compare) {
                    $yeniislem = new EdestekIslem;

                    $musteri = $islem->edestekmusteri_id;
                    $konu = $islem->edestekkonu_id;
                    $konuislem = $islem->edestekkonuislem_id;
                    $personel = $islem->edestekpersonel_id;
                    $durum = 0;

                    $yeniislem->edestekmusteri_id = $musteri;
                    $yeniislem->tarih = date('Y-m-d H:i:s');
                    $yeniislem->edestekkonu_id = $konu;
                    $yeniislem->edestekkonuislem_id = $konuislem;
                    $yeniislem->detay = $islem->detay;
                    $yeniislem->edestekpersonel_id = $personel;
                    $yeniislem->durum = $durum;
                    if ($yeniislem->save()) {
                        $islem->sonislemtarihi = $yeniislem->tarih;
                        $islem->save();
                        $kayit = new EdestekKayit;
                        $kayit->edestekmusteri_id = $musteri;
                        $kayit->konu_id = $yeniislem->id;
                        $kayit->edestekkayitkonu_id = 5;
                        $kayit->yapilanislem = $yeniislem->edestekkonu->adi . " Konusunda İşlem Bekliyor.";
                        $kayit->edestekpersonel_id = $personel;
                        $kayit->tarih = $yeniislem->tarih;
                        $kayit->durum = $durum;
                        if ($kayit->save()) {
                            return Response::json(array('durum' => 1));
                        } else {
                            $yeniislem->delete();
                            return Response::json(array('durum' => 0));
                        }
                    } else {
                        return Response::json(array('durum' => 0));
                    }
                }
            }
            return Response::json(array('durum' => 0));
        } catch (Exception $e) {
            return Response::json(array('durum' => 0));
        }
    }

    public function getIslemekle() {
        $musteriler = EdestekMusteri::orderBy('musteriadi','ASC')->get();
        $konular = EdestekKonu::orderBy('adi','ASC')->get();
        $islemler = EdestekKonuIslem::orderBy('islem','ASC')->get();
        $personeller = EdestekPersonel::orderBy('adisoyadi','ASC')->get();
        return View::make('edestek.islemekle',array('musteriler'=>$musteriler,'konular'=>$konular,'islemler'=>$islemler,'personeller'=>$personeller))->with('title', 'Yazılım Destek Düzenli İşlem Bilgisi Ekleme Ekranı');
    }

    public function getKonuislemler($id){
        $islem = EdestekKonuIslem::where('edestekkonu_id',$id)->get();
        return Response::json(array('islem' => $islem));
    }

    public function getKonuislem($id){
        $islem = EdestekKonuIslem::find($id);
        $islem->konu = EdestekKonu::find($islem->edestekkonu_id);
        return Response::json(array('islem' => $islem));
    }

    public function getKonuislemekle() {
        $konu=Input::get('konu');
        $islemadi = Input::get('islemadi');
        If(EdestekKonuIslem::where('islem',$islemadi)->where('edestekkonu_id',$konu)->first())
        {
            return Response::json(array('durum' => 0,'title' => 'Doğrulama Hatası', 'text' => 'Bu yapılacak işlem mevcut.', 'type' => 'warning'));
        }
        $islem = new EdestekKonuIslem;
        $islem->edestekkonu_id = $konu;
        $islem->islem = $islemadi;
        if ($islem->save()) {
            $islemler = EdestekKonuIslem::where('edestekkonu_id',$konu)->orderBy('islem','ASC')->get();
            return Response::json(array('durum' => 1,'islemler'=>$islemler,'title' => 'Yapılacak İşlem Eklendi', 'text' => 'Yapılacak İşlem Başarıyla Eklendi', 'type' => 'success'));
        } else {
            return Response::json(array('durum' => 3, 'title' => 'Yapılacak İşlem Eklenemedi', 'text' => 'Yapılacak İşlem Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getKonuislemduzenle() {
        $id=Input::get('islemid');
        $konu=Input::get('konu');
        $islemadi = Input::get('islem');

        $islem = EdestekKonuIslem::find($id);
        If(EdestekKonuIslem::where('islem',$islemadi)->where('edestekkonu_id',$konu)->where('id','<>',$id)->first())
        {
            return Response::json(array('durum' => 0,'title' => 'Doğrulama Hatası', 'text' => 'Bu yapılacak işlem mevcut.', 'type' => 'warning'));
        }
        $islem->edestekkonu_id = $konu;
        $islem->islem = $islemadi;

        if ($islem->save()) {
            $islemler = EdestekKonuIslem::where('edestekkonu_id',$konu)->orderBy('islem','ASC')->get();
            return Response::json(array('durum' => 1,'islemler'=>$islemler,'title' => 'Yapılacak İşlem Güncellendi', 'text' => 'Yapılacak İşlem Başarıyla Güncellendi', 'type' => 'success'));
        } else {
            return Response::json(array('durum' => 3, 'title' => 'Yapılacak İşlem Güncellenemedi', 'text' => 'Yapılacak İşlem Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function postIslemekle() {
        try {
            $rules = ['options1' => 'required', 'options2' => 'required', 'options3' => 'required', 'options4' => 'required', 'options5' => 'required', 'baslangictarih' => 'required', 'aralik' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $islem = new EdestekDuzenliIslem;
            $musteri = Input::get('options1');
            $araliktip = Input::get('options2');
            $konu = Input::get('options3');
            $konuislem = Input::get('options4');
            $personel = Input::get('options5');
            $durum = Input::get('options6');
            $aralik = Input::get('aralik');
            $detay = Input::get('islemdetayyeniid');
            if (Input::has('baslangictarih')) {
                $tarih = Input::get('baslangictarih');
                $baslangictarih = date("Y-m-d", strtotime($tarih));
                $islem->baslangictarih = $baslangictarih;
            }
            $islem->edestekmusteri_id = $musteri;
            $islem->aralik = $aralik;
            $islem->araliktip = $araliktip;
            $islem->edestekkonu_id = $konu;
            $islem->edestekkonuislem_id = $konuislem;
            $islem->detay = $detay;
            $islem->edestekpersonel_id = $personel;
            $islem->durum = $durum;
            $islem->save();
            DB::commit();
            return Redirect::to('edestek/islemler')->with(array('mesaj' => 'true', 'title' => 'Düzenli İşlem Kaydedildi', 'text' => 'Düzenli İşlem Başarıyla Kaydedildi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Düzenli İşlem Kaydedilemedi', 'text' => 'Düzenli İşlem Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getIslemsil($id) {
        try{
            $duzenliislem = EdestekDuzenliIslem::find($id);
            if ($duzenliislem) {
                DB::beginTransaction();
                $duzenliislem->delete();
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Düzenli İşlem Silindi', 'text' => 'Düzenli İşlem Başarıyla Silindi.', 'type' => 'success'));
            }else {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Düzenli İşlem Silinemedi', 'text' => 'Düzenli İşlem Bulunamadı.', 'type' => 'error'));
            }
        }catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Düzenli İşlem Silinemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getIslemduzenle($id) {
        $duzenliislem = EdestekDuzenliIslem::find($id);
        $musteriler = EdestekMusteri::orderBy('musteriadi','ASC')->get();
        $konular = EdestekKonu::orderBy('adi','ASC')->get();
        $islemler = EdestekKonuIslem::orderBy('islem','ASC')->get();
        $personeller = EdestekPersonel::orderBy('adisoyadi','ASC')->get();
        return View::make('edestek.islemduzenle',array('duzenliislem'=>$duzenliislem,'musteriler'=>$musteriler,'konular'=>$konular,'islemler'=>$islemler,'personeller'=>$personeller))->with('title', 'Yazılım Destek Düzenli İşlem Bilgisi Ekleme Ekranı');
    }

    public function postIslemduzenle($id) {
        try {
            $rules = ['options1' => 'required', 'options2' => 'required', 'options3' => 'required', 'options4' => 'required', 'options5' => 'required', 'baslangictarih' => 'required', 'aralik' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $islem = EdestekDuzenliIslem::find($id);
            $musteri = Input::get('options1');
            $araliktip = Input::get('options2');
            $konu = Input::get('options3');
            $konuislem = Input::get('options4');
            $personel = Input::get('options5');
            $durum = Input::get('options6');
            $aralik = Input::get('aralik');
            $detay = Input::get('islemdetayid');
            if (Input::has('baslangictarih')) {
                $tarih = Input::get('baslangictarih');
                $baslangictarih = date("Y-m-d", strtotime($tarih));
                $islem->baslangictarih = $baslangictarih;
            }
            $islem->edestekmusteri_id = $musteri;
            $islem->aralik = $aralik;
            $islem->araliktip = $araliktip;
            $islem->edestekkonu_id = $konu;
            $islem->edestekkonuislem_id = $konuislem;
            $islem->detay = $detay;
            $islem->edestekpersonel_id = $personel;
            $islem->durum = $durum;
            $islem->save();
            DB::commit();
            return Redirect::to('edestek/islemler')->with(array('mesaj' => 'true', 'title' => 'Düzenli İşlem Güncellendi', 'text' => 'Düzenli İşlem Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Düzenli İşlem Güncellenemedi', 'text' => 'Düzenli İşlem Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function postIslemkayitduzenle($id) {
        try {
            $rules = ['options1' => 'required', 'options2' => 'required', 'options3' => 'required', 'options4' => 'required', 'tarih' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $kayit = EdestekKayit::find($id);
            if ($kayit->edestekkayitkonu_id == 5) {
                $islem = EdestekIslem::find($kayit->konu_id);
            } else {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => 'İşlem kayıdı bulunamadı', 'type' => 'error'));
            }
            $musteri = Input::get('options1');
            $konu = Input::get('options2');
            $konuislem = Input::get('options3');
            $personel = Input::get('options4');
            $durum = Input::get('options5');
            $durumaciklama = Input::get('aciklama');
            $sure = Input::get('sure');
            if ($durum == 0) {
                $yapilanislem = $islem->edestekkonu->adi . " Konusunda İşlem Bekliyor.";
            } else {
                $yapilanislem = $islem->edestekkonu->adi . " Konusunda İşlem Yapıldı.";
            }
            $detay = Input::get('islemdetayid');
            if (Input::has('tarih')) {
                $tarih = Input::get('tarih');
                $islemtarih = date("Y-m-d", strtotime($tarih));
                $islem->tarih = $islemtarih;
            }
            $islem->edestekmusteri_id = $musteri;
            $islem->edestekkonu_id = $konu;
            $islem->edestekkonuislem_id = $konuislem;
            $islem->detay = $detay;
            $islem->edestekpersonel_id = $personel;
            $islem->sure = $sure;
            $islem->durum = $durum;
            $islem->save();
            try {
                $kayit->edestekmusteri_id = $musteri;
                $kayit->konu_id = $islem->id;
                $kayit->edestekkayitkonu_id = 5;
                $kayit->yapilanislem = $yapilanislem;
                $kayit->edestekpersonel_id = $islem->edestekpersonel_id;
                $kayit->tarih = $islem->tarih;
                $kayit->sure = $sure;
                $kayit->durum = $durum;
                $kayit->durum_aciklama = $durumaciklama;
                $kayit->save();
                $personel = EdestekPersonel::find($kayit->edestekpersonel_id);
                $personel->sonislem_id = $kayit->id;
                $personel->sonislemtarihi = $kayit->tarih;
                $personel->save();
                DB::commit();
                return Redirect::to('edestek/edestekkayit')->with(array('mesaj' => 'true', 'title' => 'İşlem Bilgisi Güncellendi', 'text' => 'İşlem Bilgisi Başarıyla Güncellendi', 'type' => 'success'));
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Bilgisi Güncellenemedi', 'text' => 'İşlem Bilgisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'İşlem Bilgisi Güncellenemedi', 'text' => 'İşlem Bilgisi Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

}
