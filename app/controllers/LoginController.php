<?php
//transaction işlemi tamamlandı
class LoginController extends BackendController {

    public function getIndex() {
        return View::make('loginpage.login');
    }

    public function postIndex() {
        $validate = Validator::make(Input::all(), Kullanici::$rules);
        $messages = $validate->messages();
        if ($validate->fails()) {
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
        }
        if (Auth::attempt(array('girisadi' => Input::get('girisadi'), 'password' => Input::get('sifre')), Input::get('remember'))) {
            Kullanici::find(Auth::user()->id)->update(['online'=>1]);
            BackendController::BildirimUpdate();
            if (Auth::user()->grup_id == "10") {
                return Redirect::to('edestek/edestekkayit');
            } else if (Auth::user()->grup_id == "6") {
                return Redirect::to('ucretlendirme/ucretlendirmekayit');
            } else if (Auth::user()->grup_id == "14" || Auth::user()->grup_id == "15") {
                return Redirect::to('uretim/urunkayit');
            } else if (Auth::user()->grup_id == "18") {
                return Redirect::to('sube/serviskayit');
            }else if (Auth::user()->grup_id == "19") {
                return Redirect::to('abone/musterionay');
            } else {
                return Redirect::intended('index');
            }
        } else {
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Giriş Hatası', 'text' => 'Kullanıcı Adı veya Şifresi Hatalı', 'type' => 'error'));
        }
    }

    public function logout() {

        if (Auth::check()) {
            unset(Auth::user()->hatirlatma);
            unset(Auth::user()->bildirim);
            unset(Auth::user()->personeller);
            unset(Auth::user()->musteriler);
            unset(Auth::user()->page);
            unset(Auth::user()->netsiscari_id);
            unset(Auth::user()->netsiscarilist);
            Kullanici::find(Auth::user()->id)->update(['online'=>0]);
            Auth::logout();
        }
        return Redirect::to('login');
    }

    public function yetkisiz() {
        return View::make('yetkisiz')->with('title', 'Yetkisiz Sayfa');
    }
    
    public function info(){
        echo phpinfo();
    }

    public function bakim() {
        $sabit=SistemSabitleri::where('adi','BakimSonTarih')->first();
        $tarih=date('Y-m-d H:i:s',strtotime($sabit->deger));
        $today=date('Y-m-d H:i:s');
        if(strtotime($today)>=strtotime($tarih)){
            $bakim=SistemSabitleri::where('adi','BakimDurum')->first();
            $bakim->deger=0;
            $bakim->save();
            return Redirect::to('login');
        }

        return View::make('bakim')->with(array('tarih'=>$tarih));
    }

    public function postUruntakip() {
        $rules =  array('captcha' => array('required', 'captcha'),'serino' => array('required'),'takipno' => array('required'));
        $validate = Validator::make(Input::all(),$rules);
        if ($validate->fails()) {
            $messages = $validate->messages();
            Input::flash();
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first(). '', 'type' => 'error'));
        }
        $takipno = Input::get('takipno');
        $serino = Input::get('serino');
        $caritakipno = intval(preg_replace("/[^0-9.]/", "", $takipno));
        if( $caritakipno!=""){
            $netsiscari = Netsiscari::where('takipno',$caritakipno)->first();
            if($netsiscari){
                $servistakip = ServisTakip::where('netsiscari_id',$netsiscari->id)->where('serino',$serino)->orderBy('depotarih','desc')->first();
                if($servistakip){
                    $depogelen = DepoGelen::find($servistakip->depogelen_id);
                    $sayacgelen = SayacGelen::find($servistakip->sayacgelen_id);
                    $sayacadi = SayacAdi::find($servistakip->sayacadi_id);
                    $netsisstokkod = NetsisStokKod::where('kodu',$sayacgelen->stokkodu)->first();
                    $uretimyer = UretimYer::find($sayacgelen->uretimyer_id);
                    $kullanici = Kullanici::find($sayacgelen->kullanici_id);
                    $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
                    $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                    $arizakayit->sayacariza = SayacAriza::find($arizakayit->sayacariza_id);
                    $arizakayit->sayacyapilan = SayacYapilan::find($arizakayit->sayacyapilan_id);
                    $arizakayit->sayacdegisen = SayacDegisen::find($arizakayit->sayacdegisen_id);
                    $arizakayit->problemler = explode(',',$arizakayit->sayacariza ? $arizakayit->sayacariza->problemler : '');
                    $arizakayit->yapilanlar = explode(',',$arizakayit->sayacyapilan ? $arizakayit->sayacyapilan->yapilanlar : '');
                    $arizakayit->degisenler = explode(',',$arizakayit->sayacdegisen ? $arizakayit->sayacdegisen->degisenler : '');
                    $arizakodlari = ArizaKod::whereIn('id',$arizakayit->problemler)->get();
                    $yapilanlar = Yapilanlar::whereIn('id',$arizakayit->yapilanlar)->get();
                    $ucretsizler = explode(',',$arizafiyat->ucretsiz ? $arizafiyat->ucretsiz : '');
                    $degisenler = array();
                    for($i=0;$i<count($arizakayit->degisenler);$i++){
                        $degisen=Degisenler::find($arizakayit->degisenler[$i]);
                        $degisen->ucretsiz = $ucretsizler[$i];
                        array_push($degisenler,$degisen);
                    }
                    $arizafiyat->parabirimi = ParaBirimi::find($arizafiyat->parabirimi_id);
                    $arizafiyat->parabirimi2 = ParaBirimi::find($arizafiyat->parabirimi2_id);
                    if($arizafiyat->parabirimi2_id==null){
                        $toplamtutar = $arizafiyat->toplamtutar.' '.$arizafiyat->parabirimi->birimi;
                    }else{
                        $toplamtutar = $arizafiyat->toplamtutar.' '.$arizafiyat->parabirimi->birimi.'+'.$arizafiyat->toplamtutar2.' '.$arizafiyat->parabirimi2->birimi;
                    }
                    $sayac = null;
                    if($arizakayit){
                        $sayac=Sayac::find($arizakayit->sayac_id);
                    }
                    $ucretlendirme = Ucretlendirilen::find($servistakip->ucretlendirilen_id);
                    $onaylanan = Onaylanan::find($servistakip->onaylanan_id);
                    if($onaylanan){
                        $onaylanan->yetkili=Yetkili::find($onaylanan->yetkili_id);
                        $onaylanan->yetkili->kullanici=Kullanici::find($onaylanan->yetkili->kullanici_id);
                    }
                    $depoteslim = DepoTeslim::find($servistakip->depoteslim_id);
                    return View::make('loginpage.uruntakip')->with(array('depogelen'=>$depogelen,'sayacgelen'=>$sayacgelen,'arizakayit'=>$arizakayit,
                        'arizafiyat'=>$arizafiyat,'ucretlendirme'=>$ucretlendirme,'onaylanan'=>$onaylanan,'depoteslim'=>$depoteslim,'kullanici'=>$kullanici,
                        'netsisstokkod'=>$netsisstokkod,'netsiscari'=>$netsiscari,'uretimyer'=>$uretimyer,'sayac'=>$sayac,'sayacadi'=>$sayacadi,
                        'servistakip'=>$servistakip,'arizalar'=>$arizakodlari,'yapilanlar'=>$yapilanlar,'degisenler'=>$degisenler,'toplamtutar'=>$toplamtutar));
                }else{
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Veri Bulunamadı', 'text' => 'Girilen Takip Numarasında Girilen Seri Numarasına Ait Bilgi Bulunamadı.', 'type' => 'error'));
                }
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Takip Numarası Hatalı', 'text' => 'Girilen Takip Numarasına Ait Kayıt Bulunamadı.', 'type' => 'error'));
            }
        }else{
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Takip Numarası Hatalı', 'text' => 'Girilen Takip Numarası Hatalı!', 'type' => 'error'));
        }
    }

}
