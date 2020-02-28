<?php
//transaction işlemi tamamlandı
class LockController extends BackendController {

    public function getIndex() {
        return View::make('lock')->with('title','Kilitli');
    }

    public function postIndex() {
        $validate = Validator::make(Input::all(),['sifre'=>'required']);
        $messages = $validate->messages();
        if ($validate->fails()) {
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
        }
        if (Auth::check()){
            if (Auth::attempt(array('girisadi' => Auth::user()->girisadi, 'password' => Input::get('sifre')), Input::get('remember'))) {
                if (Auth::user()->grup_id == "10") {
                    return Redirect::intended('edestek/edestekkayit');
                } else if (Auth::user()->grup_id == "6") {
                    return Redirect::intended('ucretlendirme/ucretlendirmekayit');
                } else if (Auth::user()->grup_id == "18") {
                    return Redirect::intended('sube/serviskayit');
                }else if (Auth::user()->grup_id == "19") {
                    return Redirect::intended('abone/musterionay');
                } else {
                    return Redirect::intended('index');
                }
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Giriş Hatası', 'text' => 'Girilen Şifre Yanlış', 'type' => 'error'));
            }
        }else{
            return Redirect::to('logout');
        }
    }
}
