<?php
//transaction tamamlandı
class ProfilController extends BackendController {

    public function getIndex() {
        $userid=Auth::user()->id;
        $islemler=Islem::where('kullanici_id',$userid)->orderBy('id','desc')->take(20)->get();
        foreach($islemler as $islem){
            $islem->tarih=BackendController::time_elapsed($islem->created_at);
        }
        return View::make('profil/profil')->with(array('islemler'=>$islemler,'title'=>'Kullanıcı Profil Detayları'));
    }
    
    public function getProfilbilgi() {
        return View::make('profil/profilbilgi')->with(array('title'=>'Kullanıcı Profil Bilgisi'));
    }
    
    public function postProfilbilgi($id,$tab) {
        try {
            if ($tab == 1) {
                $rules = ['adisoyadi' => 'required|min:2', 'girisadi' => 'required|min:2|unique:kullanici,girisadi,' . $id, 'email' => 'email|unique:kullanici,email,' . $id . ',id'];

            } else if ($tab == 2) {
                $rules = ['avatar' => 'image|mimes:jpeg,jpg,png'];

            } else {
                $rules = ['oldpassword' => 'required', 'password' => 'required|min:5|confirmed', 'password_confirmation' => 'required|min:5'];

            }
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $kullanici = Kullanici::find($id);
            if ($tab == 1) {
                $adi = Input::get('adisoyadi');
                $girisadi = Input::get('girisadi');
                if (Input::has('email')) {
                    $email = Input::get('email');
                    $kullanici->email = $email;
                }
                if (Input::has('telefon')) {
                    $telefon = Input::get('telefon');
                    $kullanici->telefon = $telefon;
                }
                $kullanici->adi_soyadi = $adi;
                $kullanici->girisadi = $girisadi;
            } else if ($tab == 2) {
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
            } else {
                if (Input::has('password')) {
                    $sifre = Hash::make(Input::get('password'));
                    $kullanici->password = $sifre;
                }
            }

            $kullanici->save();
            DB::commit();
            return Redirect::to('profil')->with(array('mesaj' => 'true', 'title' => 'Profil Güncellendi', 'text' => 'Profil Bilgileri Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Profil Güncellenemedi', 'text' => 'Profil Bilgileri Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

}
