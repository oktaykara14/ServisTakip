<?php
//transaction işlemi tamamlandı
class KullaniciController extends BackendController {

    public function getKullanicilistesi() {
        return View::make('kullanicilar.kullanicilistesi')->with(array('title'=>'Kullanıcı Bilgi Ekranı'));
    }

    public function postKullanicilist() {
        $query = Kullanici::select(array("kullanici.id","kullanici.adi_soyadi","kullanici.girisadi","grup.grupadi","kullanici.aktifdurum","kullanici.nadi_soyadi",
            "grup.ngrupadi","kullanici.grup_id"))
            ->leftjoin("grup", "kullanici.grup_id", "=", "grup.id");
        return Datatables::of($query)
            ->editColumn('grupadi', function ($model) {
                if($model->grup_id < 5)
                    return "<span class='label label-mini label-success'>".$model->grupadi."</span>";
                else if ($model->grup_id < 10)
                    return "<span class='label label-mini label-info'> ".$model->grupadi." </span>";
                else if ($model->grup_id == 10)
                    return "<span class='label label-mini label-warning'> ".$model->grupadi." </span>";
                else if($model->grup_id<16)
                    return "<span class='label label-mini label-info'> ".$model->grupadi." </span>";
                else
                    return "<span class='label label-mini label-danger'> ".$model->grupadi." </span>";})
            ->addColumn('aktifdurum', function ($model) {
                if($model->aktifdurum)
                    return "<div class='bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-off bootstrap-switch-animate' style='width:116px;'>".
                        "<input type='checkbox' id='".$model->id."' class='make-switch' data-on-color='success' data-off-color='warning' data-on-text='Aktif' data-off-text='Pasif' ".
                    "checked /></div>";
                else
                    return "<div class='bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-off bootstrap-switch-animate' style='width:116px;'>".
                    "<input type='checkbox' id='".$model->id."' class='make-switch' data-on-color='success' data-off-color='warning' data-on-text='Aktif' data-off-text='Pasif' ".
                    " /></div>";
            })
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/kullanicilar/kullaniciduzenle/".$model->id."'> Düzenle </a>".
                "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
            })
            ->make(true);
    }
    
    public function getKullaniciekle() {
        $servisler = Servis::all();
        $gruplar = Grup::all();
        return View::make('kullanicilar.kullaniciekle',array('servisler'=>$servisler,'gruplar'=>$gruplar))->with(array('title'=>'Kullanıcı Ekleme Ekranı'));
    }
    
    public function postKullaniciekle() {
        try {
            $rules = ['girisadi' => 'required|unique:kullanici,girisadi', 'sifre' => 'required', 'email' => 'email|unique:kullanici,email,NULL', 'avatar' => 'image|mimes:jpeg,jpg,png'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $kullanici = new Kullanici;
            $adi_soyadi = Input::get('adisoyadi');
            $girisadi = Input::get('girisadi');
            $sifre = Hash::make(Input::get('sifre'));
            if (Input::has('email')) {
                $email = Input::get('email');
                $kullanici->email = $email;
            }
            if (Input::has('telefon')) {
                $telefon = Input::get('telefon');
                $kullanici->telefon = $telefon;
            }
            $servis = Input::get('servis');
            $grup = Input::get('grup');
            If (Input::hasFile('avatar')) {
                $avatar = Input::file('avatar');
                $dosyaadi = $avatar->getClientOriginalName();
                $dosyaadi = pathinfo($dosyaadi, PATHINFO_FILENAME);
                $uzanti = $avatar->getClientOriginalExtension();
                $isim = Str::slug($dosyaadi) . Str::slug(str_random(5)) . '.' . $uzanti;
                $avatar->move('assets/images/profilresim/', $isim);
                Image::make('assets/images/profilresim/' . $isim)->resize(160, 160)->save();
                $kullanici->avatar = $isim;
            }
            $kullanici->adi_soyadi = $adi_soyadi;
            $kullanici->girisadi = $girisadi;
            $kullanici->password = $sifre;
            $kullanici->servis_id = $servis;
            $kullanici->grup_id = $grup;
            $kullanici->aktifdurum=1;
            $kullanici->save();
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $adi_soyadi . ' İsimli Kullanıcı Eklendi.', 'Adı:' . $adi_soyadi . ',Kullanıcı Adı:' . $girisadi);
            DB::commit();
            return Redirect::to('kullanicilar/kullanicilistesi')->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Eklendi', 'text' => 'Kullanıcı Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Eklenemedi', 'text' => 'Kullanıcı Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }
    
    public function getKullanicidurum(){
        try {
            $id=Input::get('id');
            $durum=Input::get('durum');
            DB::beginTransaction();
            $kullanici = Kullanici::find($id);
            $kullanici->aktifdurum = $durum;
            $kullanici->save();
            DB::commit();
            return Response::json(array('mesaj' => 'true', 'title' => 'Kullanıcı Güncellendi', 'text' => 'Kullanıcı Başarıyla Güncellendi.', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Response::json(array('mesaj' => 'true', 'title' => 'Kullanıcı Güncellenemedi', 'text' => 'Kullanıcı Güncellenirken Sorun Oluştu.', 'type' => 'error'));
        }
    }
    
    public function getKullanicisil($id){
        try {
            DB::beginTransaction();
            $kullanici = Kullanici::find($id);
            $bilgi = clone $kullanici;
            $kullanici->delete();
            BackendController::IslemEkle(3, Auth::user()->id, 'label-error', 'fa-user', $bilgi->adi_soyadi . ' İsimli Kullanıcı Silindi.', 'Adı:' . $bilgi->adi_soyadi . ',Kullanıcı Adı:' . $bilgi->girisadi);
            DB::commit();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Silindi', 'text' => 'Kullanıcı Başarıyla Silindi.', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Silinemedi', 'text' => 'Kullanıcı Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }
    
    public function getKullaniciduzenle($id) {
        $kullanici = Kullanici::find($id);
        $servisler = Servis::all();
        $gruplar = Grup::all();
        return View::make('kullanicilar.kullaniciduzenle',array('kullanici'=>$kullanici,'servisler'=>$servisler,'gruplar'=>$gruplar))->with(array('title'=>'Kullanıcı Düzenleme Ekranı'));
    }
    
    public function postKullaniciduzenle($id){
        try {
            DB::beginTransaction();
            $kullanici = Kullanici::find($id);
            if (is_null($kullanici->email))
                $rules = ['girisadi' => 'required|unique:kullanici,girisadi,' . $id, 'email' => 'email|unique:kullanici,email', 'avatar' => 'image|mimes:jpeg,jpg,png'];
            else
                $rules = ['girisadi' => 'required|unique:kullanici,girisadi,' . $id, 'email' => 'email|unique:kullanici,email,' . $id . '', 'avatar' => 'image|mimes:jpeg,jpg,png'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $bilgi = clone $kullanici;
            $adi_soyadi = Input::get('adisoyadi');
            $girisadi = Input::get('girisadi');
            if (Input::has('sifre')) {
                $sifre = Hash::make(Input::get('sifre'));
                $kullanici->password = $sifre;
            }
            if (Input::has('email')) {
                $email = Input::get('email');
                $kullanici->email = $email;
            }
            if (Input::has('telefon')) {
                $telefon = Input::get('telefon');
                $kullanici->telefon = $telefon;
            }
            $servis = Input::get('servis');
            $grup = Input::get('grup');
            If (Input::hasFile('avatar')) {
                File::delete('assets/images/profilresim/' . $kullanici->avatar . '');
                $avatar = Input::file('avatar');
                $dosyaadi = $avatar->getClientOriginalName();
                $dosyaadi = pathinfo($dosyaadi, PATHINFO_FILENAME);
                $uzanti = $avatar->getClientOriginalExtension();
                $isim = Str::slug($dosyaadi) . Str::slug(str_random(5)) . '.' . $uzanti;
                $avatar->move('assets/images/profilresim/', $isim);
                Image::make('assets/images/profilresim/' . $isim)->resize(160, 160)->save();
                $kullanici->avatar = $isim;
            }
            $kullanici->adi_soyadi = $adi_soyadi;
            $kullanici->girisadi = $girisadi;
            $kullanici->servis_id = $servis;
            $kullanici->grup_id = $grup;
            $kullanici->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-user', $bilgi->adi_soyadi . ' İsimli Kullanıcının Bilgileri Güncellendi.', 'Adı:' . $adi_soyadi . ',Kullanıcı Adı:' . $girisadi);
            DB::commit();
            return Redirect::to('kullanicilar/kullanicilistesi')->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Güncellendi', 'text' => 'Kullanıcı Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Güncellenemedi', 'text' => 'Kullanıcı Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }
}
