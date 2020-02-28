<?php
//transaction işlemi tamamlandı
class KalibrasyonController extends BackendController {

    public function getKalibrasyon($hatirlatma_id=false) {
        if($hatirlatma_id)
            return View::make('kalibrasyon.kalibrasyon',array('hatirlatma_id'=>$hatirlatma_id))->with(array('title'=>'Kalibrasyon Bilgi Ekranı'));
        else
            return View::make('kalibrasyon.kalibrasyon')->with(array('title'=>'Kalibrasyon Bilgi Ekranı'));
    }

    public function postKalibrasyonlist() {
        $hatirlatma_id=Input::get('hatirlatma_id');
        $hatirlatma = false;
        if($hatirlatma_id!="") {
            $hatirlatma = Hatirlatma::find($hatirlatma_id);
        }
        if($hatirlatma){
            $query = KalibrasyonGrup::where('kalibrasyongrup.netsiscari_id',$hatirlatma->netsiscari_id)
                ->select(array("kalibrasyongrup.id","netsiscari.cariadi","kalibrasyongrup.adet","kalibrasyongrup.biten","kalibrasyongrup.gdurum",
                    "kalibrasyongrup.kayittarihi","kalibrasyongrup.gkayittarihi","netsiscari.ncariadi","kalibrasyongrup.ndurum"))
                ->leftjoin("netsiscari", "kalibrasyongrup.netsiscari_id", "=", "netsiscari.id");

        }else{
            $query = KalibrasyonGrup::select(array("kalibrasyongrup.id","netsiscari.cariadi","kalibrasyongrup.adet","kalibrasyongrup.biten","kalibrasyongrup.gdurum",
                "kalibrasyongrup.kayittarihi","kalibrasyongrup.gkayittarihi","netsiscari.ncariadi","kalibrasyongrup.ndurum"))
                ->leftjoin("netsiscari", "kalibrasyongrup.netsiscari_id", "=", "netsiscari.id");
        }
        return Datatables::of($query)
            ->editColumn('kayittarihi', function ($model) {
                if($model->kayittarihi){
                    $date = new DateTime($model->kayittarihi);
                    return $date->format('d-m-Y');
                }else{
                    return '';
                }})
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                return "<a class='btn btn-sm btn-warning' href='".$root."/kalibrasyon/kalibrasyondetay/".$model->id."' > Detay </a>";
            })
            ->make(true);
    }

    public function getKalibrasyondetay($id) {
        $grup = KalibrasyonGrup::find($id);
        $grup->netsiscari=NetsisCari::find($grup->netsiscari_id);
        if($grup->netsiscari->caridurum!="A")
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
        return View::make('kalibrasyon.kalibrasyondetay',array('grup'=>$grup))->with(array('title'=>'Kalibrasyon Detay Ekranı'));
    }

    public function postKalibrasyondetaylist() {
        $grup_id=Input::get('grup_id');
        $query = Kalibrasyon::where('kalibrasyon.kalibrasyongrup_id',$grup_id)
            ->select(array("kalibrasyon.id","kalibrasyon.kalibrasyon_seri","sayacadi.sayacadi","kalibrasyon.imalyili","kalibrasyon.gdurum","kullanici.adi_soyadi",
                "kalibrasyon.kalibrasyontarih","kalibrasyon.gkalibrasyontarih","sayacadi.nsayacadi","kalibrasyon.ndurum","kullanici.nadi_soyadi","sayacgelen.kalibrasyon",
                "sayacgelen.depoteslim","kalibrasyongrup.kalibrasyondurum"))
            ->leftjoin("sayacgelen", "kalibrasyon.sayacgelen_id", "=", "sayacgelen.id")
            ->leftjoin("sayacadi", "kalibrasyon.sayacadi_id", "=", "sayacadi.id")
            ->leftjoin("kalibrasyongrup", "kalibrasyon.kalibrasyongrup_id", "=", "kalibrasyongrup.id")
            ->leftJoin("kullanici", "kalibrasyon.kullanici_id", "=", "kullanici.id");
        return Datatables::of($query)
            ->editColumn('imalyili', function ($model) {
                if($model->imalyili){
                    $date = new DateTime($model->imalyili);
                    return $date->format('Y');
                }else{
                    return '';
                }})
            ->editColumn('adi_soyadi', function ($model) {
                if($model->adi_soyadi){
                    return $model->adi_soyadi;
                }else{
                    return '';
                }
            })
            ->editColumn('kalibrasyontarih', function ($model) {
                if($model->kalibrasyontarih){
                    $date = new DateTime($model->kalibrasyontarih);
                    return $date->format('d-m-Y');
                }else{
                    return '';
                }
            })
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                if($model->gdurum=="Hurda"){
                    if( (time()-strtotime($model->kalibrasyontarih))>86400)
                        return "<a class='btn btn-sm btn-info hurdagoster' href='#hurda-goster' data-toggle='modal' data-id='".$model->id."'> Detay </a>";
                    else
                        return "<a class='btn btn-sm btn-warning' href='".$root."/kalibrasyon/hurdaduzenle/".$model->id."' > Düzenle </a>";
                }else{
                    if($model->depoteslim){
                        return "<a class='btn btn-sm btn-info goster' href='#detay-goster' data-toggle='modal' data-id='".$model->id."'> Detay </a>";
                    }else if($model->kalibrasyon){
                        if( (time()-strtotime($model->kalibrasyontarih))>86400)
                            return "<a class='btn btn-sm btn-info goster' href='#detay-goster' data-toggle='modal' data-id='".$model->id."'> Detay </a>";
                        else
                            return "<a class='btn btn-sm btn-warning' href='".$root."/kalibrasyon/kayitduzenle/".$model->id."' > Düzenle </a>";
                    }else{
                        if($model->kalibrasyondurum==1 && (time()-strtotime($model->kalibrasyontarih))>86400 )
                            return "<a class='btn btn-sm btn-info goster' href='#detay-goster' data-toggle='modal' data-id='".$model->id."'> Detay </a>";
                        else if($model->gdurum!="Bekliyor")
                            return "<a class='btn btn-sm btn-warning' href='".$root."/kalibrasyon/kayitduzenle/".$model->id."' > Düzenle </a>";
                        else
                            return "";
                    }
                }


            })
            ->make(true);
    }

    public function getKayitgirisi($id) {
        $grup = KalibrasyonGrup::find($id);
        $grup->netsiscari=NetsisCari::find($grup->netsiscari_id);
        if($grup->netsiscari->caridurum!="A")
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
        $istasyonlar=Istasyon::all();
        $sayacadilist=BackendController::getSayacAdilistesi($id);
        $sayacadlari=SayacAdi::whereIn('id',$sayacadilist)->get();
        $kalibrasyonlar=Kalibrasyon::where('kalibrasyongrup_id',$id)->whereIn('durum',array(0))->get();
        return View::make('kalibrasyon.kayitgirisi',array('grup'=>$grup,'istasyonlar'=>$istasyonlar,'sayacadlari'=>$sayacadlari,'kalibrasyonlar'=>$kalibrasyonlar))->with(array('title'=>'Kalibrasyon Kayıt Ekranı'));
    }

    public function getKalibrasyonstandart() {
        try {
            $grupid=Input::get('grupid');
            $sayacadiid=Input::get('sayacadiid');
            $sayacadi = SayacAdi::find($sayacadiid);
            if ($sayacadi->sayactur_id == 5) // gaz mekanik sayaçları için kalibrasyon sabitleri mevcut
            {
                $kalibrasyonstandart = KalibrasyonStandart::where('kalibrasyontipi_id', $sayacadi->kalibrasyontipi_id)->get();
                $kalibrasyon = Kalibrasyon::where('kalibrasyongrup_id', $grupid)->where('sayacadi_id', $sayacadiid)->whereIn('durum', array(0))->get(array('id', 'kalibrasyon_seri', 'imalyili'));
                return Response::json(array('durum' => true, 'sayacadi' => $sayacadi, 'kalibrasyonstandart' => $kalibrasyonstandart, 'kalibrasyon' => $kalibrasyon));
            }
            return Response::json(array('durum' => false,'title'=>'Kalibrasyon Bilgi Hatası','text'=>'Seçilen Sayaç Adının Kalibrasyon Standart Bilgileri Mevcut Değil','type'=>'warning'));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false,'title'=>'Kalibrasyon Bilgi Hatası','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'warning'));
        }
    }

    public function getKalibrasyonstandartbilgi() {
        try {
            $id=Input::get('id');
            $standart = KalibrasyonStandart::find($id);
            return Response::json(array('durum'=>true,'standart' => $standart));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false,'title'=>'Kalibrasyon Standartları Bilgi Hatası','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'warning'));
        }
    }

    public function getIstasyonbilgi() {
        try {
            $grupid=Input::get('grupid');
            $istasyonid=Input::get('istasyonid');
            $istasyon = Istasyon::find($istasyonid);
            $istasyonsayacadilist = explode(',', $istasyon->sayacadlari);
            $sayacadilist = BackendController::getSayacAdilistesi($grupid);
            $list=array();
            foreach ($sayacadilist as $sayacadi)
            if (in_array($sayacadi, $istasyonsayacadilist)) {
                array_push($list,$sayacadi);
            }
            $sayacadlari = SayacAdi::whereIn('id', $list)->get();
            return Response::json(array('durum' => true, 'istasyon' => $istasyon, 'sayacadlari' => $sayacadlari));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false,'title'=>'İstasyon Bilgi Hatası','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'warning'));
        }
    }

    public function postKayitgirisi($id) {
        try {
            $rules = ['istasyonadi' => 'required', 'hassasiyet' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $grupid = $id;
            $kalibrasyongrup=KalibrasyonGrup::find($grupid);
            $noktasayi = Input::get('noktasayi');
            $secilenlist = Input::get('kalibrasyonid');
            $istasyonadi = Input::get('istasyonadi');
            $hassasiyet = Input::get('hassasiyet');
            $count = 0;
            $basarili = 0;
            $basarisiz = 0;
            DB::beginTransaction();
            if ($noktasayi == 3) {
                $nokta1 = Input::get('nokta1');
                $nokta2 = Input::get('nokta2');
                $nokta3 = Input::get('nokta3');
                $sonuc = Input::get('sonuc');
                try {
                    foreach ($secilenlist as $kalibrasyonid) {
                        $kalibrasyon = Kalibrasyon::find($kalibrasyonid);
                        if($kalibrasyon->durum!=0){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Sayaçların kalibrasyon bilgisi zaten girilmiş olabilir!', 'type' => 'error'));
                        }
                        $kalibrasyon->istasyon_id = $istasyonadi;
                        $kalibrasyon->sira = $count + 1;
                        $kalibrasyon->kalibrasyonstandart_id = $hassasiyet;
                        $kalibrasyon->nokta1sapma = $nokta1;
                        $kalibrasyon->sonuc1 = str_replace(',', '.', $sonuc[$count][0]);
                        $kalibrasyon->nokta2sapma = $nokta2;
                        $kalibrasyon->sonuc2 = str_replace(',', '.', $sonuc[$count][1]);
                        $kalibrasyon->nokta3sapma = $nokta3;
                        $kalibrasyon->sonuc3 = str_replace(',', '.', $sonuc[$count][2]);
                        if (($kalibrasyon->sonuc1 > $kalibrasyon->nokta1sapma) || ($kalibrasyon->sonuc1 < -($kalibrasyon->nokta1sapma)) ||
                            ($kalibrasyon->sonuc2 > $kalibrasyon->nokta2sapma) || ($kalibrasyon->sonuc2 < -($kalibrasyon->nokta2sapma)) ||
                            ($kalibrasyon->sonuc3 > $kalibrasyon->nokta3sapma) || ($kalibrasyon->sonuc3 < -($kalibrasyon->nokta3sapma))
                        ) {
                            $kalibrasyon->durum = 2; //başarısız
                        } else {
                            $kalibrasyon->durum = 1;
                            $basarili++;
                        }
                        $count++;
                        $kalibrasyon->kullanici_id = Auth::user()->id;
                        $kalibrasyon->kalibrasyontarih = date('Y-m-d H:i:s');
                        $kalibrasyon->save();
                        if ($kalibrasyon->durum == 1) { //kalibrasyon geçtiyse ucretlendirme tamamsa depo teslime aktarılacak yoksa bekleyecek
                            try {
                                $sayacgelen = SayacGelen::find($kalibrasyon->sayacgelen_id);
                                $sayacgelen->kalibrasyon = 1;
                                $sayacgelen->beyanname = $sayacgelen->beyanname==-2 ? 0 : $sayacgelen->beyanname;
                                $sayacgelen->save();
                                $servistakip = ServisTakip::where('kalibrasyon_id', $kalibrasyon->id)->first();
                                $servistakip->durum = 8;
                                $servistakip->kalibrasyontarihi = date('Y-m-d H:i:s');
                                $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                                $servistakip->kullanici_id = Auth::user()->id;
                                $servistakip->save();
                                if(!$kalibrasyongrup->periyodik){ //periyodik bakımında arıza kayıdı yapılmıyor
                                    $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
                                    if($arizakayit->arizakayit_durum==4) //şikayetli sayaç ise
                                        $arizakayit->rapordurum = -1;
                                    else
                                        $arizakayit->rapordurum = 0 ;
                                    $arizakayit->save();
                                }
                                $uretimyer=UretimYer::find($servistakip->uretimyer_id);
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Bilgi Güncelleme Hatası', 'text' => 'Kalibrasyon yapılan sayaçların sayaç gelen ve servistakip bilgisi güncellenemedi', 'type' => 'error'));
                            }
                            try {
                                $flag = 0;
                                if($sayacgelen->musterionay || $kalibrasyongrup->periyodik) {
                                    $sayacgelen->teslimdurum=1;
                                    $sayacgelen->save();
                                    if($kalibrasyongrup->periyodik){
                                        $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                            ->where('depodurum', 0)->where('tipi', 0)->where('periyodik', $kalibrasyongrup->periyodik)->where('subegonderim',0)->first();
                                        if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                        {
                                            $secilenlist = explode(',', $depoteslim->secilenler);
                                            if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                $depoteslim->sayacsayisi += 1;
                                                $depoteslim->save();
                                            }else{
                                                $flag = 1;
                                            }
                                        } else { //yeni depo teslimatı yapılacak
                                            $depoteslim = new DepoTeslim;
                                            $depoteslim->servis_id = $sayacgelen->servis_id;
                                            $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                            $depoteslim->secilenler = $sayacgelen->id;
                                            $depoteslim->sayacsayisi = 1;
                                            $depoteslim->depodurum = 0;
                                            $depoteslim->periyodik = $kalibrasyongrup->periyodik;
                                            $depoteslim->parabirimi_id=$uretimyer->parabirimi_id;
                                            $depoteslim->save();
                                        }
                                    }else{
                                        $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                                        $birim = $arizafiyat->parabirimi_id;
                                        $birim2 = $arizafiyat->parabirimi2_id;
                                        $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                        if($arizakayit->arizakayit_durum==7){
                                            $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                ->where('depodurum', 0)->where('tipi', 2)->where('periyodik',0)->where('subegonderim', 0)->first();
                                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                            {
                                                $secilenlist = explode(',', $depoteslim->secilenler);
                                                if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                    $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                    $depoteslim->sayacsayisi += 1;
                                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                    $depoteslim->save();
                                                }else{
                                                    $flag = 1;
                                                }
                                            } else { //yeni depo teslimatı yapılacak
                                                $depoteslim = new DepoTeslim;
                                                $depoteslim->servis_id = $sayacgelen->servis_id;
                                                $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                $depoteslim->secilenler = $sayacgelen->id;
                                                $depoteslim->sayacsayisi = 1;
                                                $depoteslim->depodurum = 0;
                                                $depoteslim->tipi = 2;
                                                $depoteslim->parabirimi_id=$birim;
                                                $depoteslim->parabirimi2_id=$birim2;
                                                $depoteslim->save();
                                            }
                                        }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0){
                                            $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                ->where('depodurum', 0)->where('tipi', 0)->where('periyodik',0)->where('subegonderim', 0)->first();
                                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                            {
                                                $secilenlist = explode(',', $depoteslim->secilenler);
                                                if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                    $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                    $depoteslim->sayacsayisi += 1;
                                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                    $depoteslim->save();
                                                }else{
                                                    $flag = 1;
                                                }
                                            } else { //yeni depo teslimatı yapılacak
                                                $depoteslim = new DepoTeslim;
                                                $depoteslim->servis_id = $sayacgelen->servis_id;
                                                $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                $depoteslim->secilenler = $sayacgelen->id;
                                                $depoteslim->sayacsayisi = 1;
                                                $depoteslim->depodurum = 0;
                                                $depoteslim->parabirimi_id=$birim;
                                                $depoteslim->parabirimi2_id=$birim2;
                                                $depoteslim->save();
                                            }
                                        }else{
                                            $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                ->where('depodurum', 0)->where('tipi',1)->where('periyodik', 0)->where('subegonderim', 0)->first();
                                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                            {
                                                $secilenlist = explode(',', $depoteslim->secilenler);
                                                if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                    $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                    $depoteslim->sayacsayisi += 1;
                                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                    $depoteslim->save();
                                                }else{
                                                    $flag = 1;
                                                }
                                            } else { //yeni depo teslimatı yapılacak
                                                $depoteslim = new DepoTeslim;
                                                $depoteslim->servis_id = $sayacgelen->servis_id;
                                                $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                $depoteslim->secilenler = $sayacgelen->id;
                                                $depoteslim->sayacsayisi = 1;
                                                $depoteslim->depodurum = 0;
                                                $depoteslim->tipi = 1;
                                                $depoteslim->parabirimi_id=$birim;
                                                $depoteslim->parabirimi2_id=$birim2;
                                                $depoteslim->save();
                                            }
                                        }
                                    }
                                    if(!$flag)
                                        BackendController::HatirlatmaEkle(9, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                }
                                if(!$flag){
                                    BackendController::BildirimEkle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                    BackendController::HatirlatmaGuncelle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                }
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Kalibrasyon yapılan sayaçlar depo teslimatına kaydedilemedi', 'type' => 'error'));
                            }
                        } else { //kalibrasyon geçmedi ise kalibrasyon sayısına bakılacak 1. kalibrasyonsa 2. kalibrasyon oluşturulacak 2. kalibrasyonsa hurdaya ayrılacak
                            if ($kalibrasyon->kalibrasyonsayisi == 1) { //2. kalibrasyon oluşturulacak
                                try {
                                    $kalibrasyonyeni = new Kalibrasyon;
                                    $kalibrasyonyeni->sayacgelen_id = $kalibrasyon->sayacgelen_id;
                                    $kalibrasyonyeni->sayacadi_id = $kalibrasyon->sayacadi_id;
                                    $kalibrasyonyeni->kalibrasyon_seri = $kalibrasyon->kalibrasyon_seri;
                                    $kalibrasyonyeni->imalyili = $kalibrasyon->imalyili;
                                    $kalibrasyonyeni->kalibrasyongrup_id = $kalibrasyon->kalibrasyongrup_id;
                                    $kalibrasyonyeni->kalibrasyonsayisi = 2;
                                    $kalibrasyonyeni->durum = 0;
                                    $kalibrasyonyeni->save();
                                    $servistakip = ServisTakip::where('kalibrasyon_id', $kalibrasyon->id)->first();
                                    $servistakip->kalibrasyon_id = $kalibrasyonyeni->id;
                                    $servistakip->save();
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Kalibrasyon yapılamayan sayaçlar için 2. kalibrasyon bilgisi girişi yapılamadı', 'type' => 'error'));
                                }
                            } else { //hurdaya ayrılacak
                                try {
                                    $basarisiz++;
                                    $sayacgelen = SayacGelen::find($kalibrasyon->sayacgelen_id);
                                    $kalibrasyoneski = Kalibrasyon::where('kalibrasyongrup_id', $kalibrasyon->kalibrasyongrup_id)->where('kalibrasyon_seri', $kalibrasyon->kalibrasyon_seri)
                                        ->where('kalibrasyonsayisi', 1)->first();
                                    $sayac = Sayac::where('uretimyer_id', $sayacgelen->uretimyer_id)->where('serino', $sayacgelen->serino)->first();
                                    $hurdakayit = new Hurda;
                                    $hurdakayit->servis_id = $sayacgelen->servis_id;
                                    $hurdakayit->netsiscari_id = $sayacgelen->netsiscari_id;
                                    $hurdakayit->sayac_id = $sayac->id;
                                    $hurdakayit->hurdanedeni_id = 1;
                                    $hurdakayit->hurdatarihi = date('Y-m-d H:i:s');
                                    $hurdakayit->sayacgelen_id = $sayacgelen->id;
                                    $hurdakayit->kalibrasyon_id = $kalibrasyoneski->id;
                                    $hurdakayit->kalibrasyon2_id = $kalibrasyon->id;
                                    $hurdakayit->kullanici_id = Auth::user()->id;
                                    $hurdakayit->save();
                                    try {
                                        $servistakip = ServisTakip::where('serino', $sayacgelen->serino)->where('depogelen_id', $sayacgelen->depogelen_id)->first();
                                        if(!$kalibrasyongrup->periyodik){
                                            $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
                                            $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                                            $sayacyapilan = SayacYapilan::find($arizakayit->sayacyapilan_id);
                                            $sayacuyari = SayacUyari::find($arizakayit->sayacuyari_id);
                                            $arizakayiteski = ArizaKayitEski::where('arizakayit_id',$arizakayit->id)->first();
                                            if($arizakayiteski){
                                                $arizakayiteski->yapilanlar=$sayacyapilan->yapilanlar;
                                                $arizakayiteski->uyarilar=$sayacuyari->uyarilar;
                                                $arizakayiteski->arizakayit_durum=$arizakayit->arizakayit_durum;
                                                $arizakayiteski->save();
                                            }else{
                                                $arizakayiteski = new ArizaKayitEski;
                                                $arizakayiteski->arizakayit_id = $arizakayit->id;
                                                $arizakayiteski->yapilanlar=$sayacyapilan->yapilanlar;
                                                $arizakayiteski->uyarilar=$sayacuyari->uyarilar;
                                                $arizakayiteski->arizakayit_durum=$arizakayit->arizakayit_durum;
                                                $arizakayiteski->save();
                                            }
                                            $sayacyapilan->yapilanlar='58';
                                            $sayacyapilan->save();
                                            $sayacuyari->uyarilar ='11';
                                            $sayacuyari->save();
                                            $arizakayit->arizakayit_durum = 2;
                                            $arizakayit->rapordurum = 0 ;
                                            $arizakayit->save();

                                            if($sayacgelen->fiyatlandirma){
                                                $ucretlendirilen=Ucretlendirilen::find($servistakip->ucretlendirilen_id);
                                                if(!$sayacgelen->musterionay){
                                                    $secilenlist=explode(',',$ucretlendirilen->secilenler);
                                                    $list="";
                                                    foreach ($secilenlist as $secilen){
                                                        if($secilen!=$arizafiyat->id)
                                                            $list.=($list=="" ? "" : ",").$secilen;
                                                    }
                                                    if($ucretlendirilen->sayacsayisi>1){
                                                        $ucretlendirilen->secilenler=$list;
                                                        $ucretlendirilen->sayacsayisi--;
                                                        if($ucretlendirilen->parabirimi_id!=$arizafiyat->parabirimi_id){
                                                            if ($ucretlendirilen->parabirimi_id == 1) { //tl
                                                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi);
                                                            } else { //euro dolar sterln
                                                                if ($arizafiyat->parabirimi_id == 1) {
                                                                    $kur = 1 / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarihi);
                                                                } else {
                                                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $ucretlendirilen->kurtarihi);
                                                                }
                                                            }
                                                            $tutar=$arizafiyat->toplamtutar*$kur;
                                                            if ($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi_id) {
                                                                $tutar += $arizafiyat->toplamtutar2;
                                                            }else if($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi2_id){
                                                                $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                            }else{
                                                                DB::rollBack();
                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                            }
                                                            $ucretlendirilen->fiyat=round(($ucretlendirilen->fiyat - $tutar) * 2) / 2;
                                                        }else{
                                                            $ucretlendirilen->fiyat-=$arizafiyat->toplamtutar;
                                                            if($arizafiyat->parabirimi2_id!=null){
                                                                if($ucretlendirilen->parabirimi2_id!=$arizafiyat->parabirimi2_id){
                                                                    DB::rollBack();
                                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                                }else{
                                                                    $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                                }
                                                            }
                                                        }
                                                        $ucretlendirilen->durum=1;
                                                        $ucretlendirilen->save();
                                                    }else{
                                                        $servistakip->ucretlendirilen_id=NULL;
                                                        $servistakip->save();
                                                        $ucretlendirilen->delete();
                                                    }
                                                    BackendController::HatirlatmaSil(6, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                                }else{
                                                    if($ucretlendirilen->parabirimi_id!=$arizafiyat->parabirimi_id){
                                                        if ($ucretlendirilen->parabirimi_id == 1) { //tl
                                                            $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi);
                                                        } else { //euro dolar sterln
                                                            if ($arizafiyat->parabirimi_id == 1) {
                                                                $kur = 1 / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarihi);
                                                            } else {
                                                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $ucretlendirilen->kurtarihi);
                                                            }
                                                        }
                                                        $tutar=$arizafiyat->toplamtutar*$kur;
                                                        if ($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi_id) {
                                                            $tutar += $arizafiyat->toplamtutar2;
                                                        }else if($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi2_id){
                                                            $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                        }else{
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                        }
                                                        $ucretlendirilen->fiyat=round(($ucretlendirilen->fiyat - $tutar) * 2) / 2;
                                                    }else{
                                                        $ucretlendirilen->fiyat-=$arizafiyat->toplamtutar;
                                                        if($arizafiyat->parabirimi2_id!=null){
                                                            if($ucretlendirilen->parabirimi2_id!=$arizafiyat->parabirimi2_id){
                                                                DB::rollBack();
                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                            }else{
                                                                $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                            }
                                                        }
                                                    }
                                                    $ucretlendirilen->save();
                                                }
                                            }else{
                                                BackendController::HatirlatmaSil(4, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                            }
                                            $arizafiyat->fiyat = 0;
                                            $arizafiyat->fiyat2 = 0;
                                            $arizafiyat->tutar = 0;
                                            $arizafiyat->tutar2 = 0;
                                            $arizafiyat->kdv = 0;
                                            $arizafiyat->kdv2 = 0;
                                            $arizafiyat->toplamtutar = 0;
                                            $arizafiyat->toplamtutar2 = 0;
                                            $arizafiyat->parabirimi2_id = null;
                                            $arizafiyat->durum = 1;
                                            $arizafiyat->save();
                                        }
                                        $uretimyer=UretimYer::find($servistakip->uretimyer_id);
                                        $flag = 0;
                                        $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                            ->where('depodurum',0)->where('tipi',3)->where('periyodik',$kalibrasyongrup->periyodik)->where('subegonderim',0)->first();
                                        if ($depoteslim) {
                                            $secilenlist = explode(',', $depoteslim->secilenler);
                                            if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                $depoteslim->sayacsayisi += 1;
                                            }else{
                                                $flag = 1;
                                            }
                                        } else {
                                            $depoteslim = new DepoTeslim;
                                            $depoteslim->servis_id = $sayacgelen->servis_id;
                                            $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                            $depoteslim->secilenler = $sayacgelen->id;
                                            $depoteslim->sayacsayisi = 1;
                                            $depoteslim->depodurum = 0;
                                            $depoteslim->tipi = 3;
                                            $depoteslim->periyodik = $kalibrasyongrup->periyodik;
                                            $depoteslim->parabirimi_id=$uretimyer->parabirimi_id;
                                        }
                                        $depoteslim->save();
                                        $sayacgelen->kalibrasyon=1;
                                        $sayacgelen->sayacdurum=3;
                                        $sayacgelen->teslimdurum=3;
                                        $sayacgelen->beyanname=-2;
                                        $sayacgelen->save();
                                        $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                        $servistakip->durum = 8;
                                        $servistakip->kalibrasyontarihi = $kalibrasyon->kalibrasyontarih;
                                        $servistakip->sonislemtarihi = $kalibrasyon->kalibrasyontarih;
                                        $servistakip->save();
                                        $hurdakayit->depoteslim_id = $depoteslim->id;
                                        $hurdakayit->save();
                                        if(!$flag){
                                            BackendController::HatirlatmaGuncelle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                            BackendController::HatirlatmaEkle(9, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                            BackendController::BildirimEkle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                            BackendController::BildirimEkle(10, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Kalibrasyon yapılamayan sayaçlar için hurdaya ayırma bilgisi girişi yapılamadı', 'type' => 'error'));
                                    }
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Kalibrasyon yapılamayan sayaçlar için hurdaya ayırma bilgisi girişi yapılamadı', 'type' => 'error'));
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => 'Kalibrasyon Bilgileri Kaydedilirken Hata Oluştu.', 'type' => 'error'));
                }
                try {
                    $kalibrasyongrup = KalibrasyonGrup::find($grupid);
                    $biten = ($basarili + $basarisiz);
                    if ($biten == ($kalibrasyongrup->adet - $kalibrasyongrup->biten)) {
                        $kalibrasyongrup->kalibrasyondurum = 1;
                    }
                    $kalibrasyongrup->biten += $biten;
                    $kalibrasyongrup->save();
                    BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-cog', $kalibrasyongrup->id . ' Numaralı Kalibrasyon Grubuna Ait Kalibrasyon Bilgisi Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Kalibrasyon Grup Numarası:' . $kalibrasyongrup->id);
                    DB::commit();
                    return Redirect::to('kalibrasyon/kalibrasyondetay/' . $id)->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Kaydedildi', 'text' => 'Kalibrasyon Bilgisi Başarıyla Kaydedildi', 'type' => 'success'));
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => 'Kalibrasyon Bilgileri Kaydedilirken Hata Oluştu.', 'type' => 'error'));
                }
            } else {
                $nokta1 = Input::get('nokta1');
                $nokta2 = Input::get('nokta2');
                $nokta3 = Input::get('nokta3');
                $hf2 = 0;
                $hf3 = 0;
                $hf32 = 0;
                if (Input::has('hf2'))
                    $hf2 = 1;
                if (Input::has('hf3'))
                    $hf3 = 1;
                if (Input::has('hf32'))
                    $hf32 = 1;
                $sonuc1 = Input::get('sonuc1');
                $sonuc2 = Input::get('sonuc2');
                $sonuc3 = Input::get('sonuc3');
                $sonuc4 = Input::get('sonuc4');
                try {
                    foreach ($secilenlist as $kalibrasyonid) {
                        $kalibrasyon = Kalibrasyon::find($kalibrasyonid);
                        if($kalibrasyon->durum!=0){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Sayaçların kalibrasyon bilgisi zaten girilmiş olabilir!', 'type' => 'error'));
                        }
                        $kalibrasyon->istasyon_id = $istasyonadi;
                        $kalibrasyon->sira = $count + 1;
                        $kalibrasyon->kalibrasyonstandart_id = $hassasiyet;
                        $flag = 0;
                        switch ($noktasayi) {
                            case 4:
                                $nokta4 = Input::get('nokta4');
                                $kalibrasyon->nokta1sapma = $nokta1;
                                $kalibrasyon->sonuc1 = str_replace(',', '.', $sonuc1[$count][0]);
                                $kalibrasyon->nokta2sapma = $nokta2;
                                $kalibrasyon->sonuc2 = str_replace(',', '.', $sonuc1[$count][1]);
                                $kalibrasyon->nokta3sapma = $nokta3;
                                $kalibrasyon->sonuc3 = str_replace(',', '.', $sonuc1[$count][2]);
                                $kalibrasyon->nokta4sapma = $nokta4;
                                $kalibrasyon->sonuc4 = str_replace(',', '.', $sonuc1[$count][3]);
                                if (($kalibrasyon->sonuc1 > $kalibrasyon->nokta1sapma) || ($kalibrasyon->sonuc1 < -($kalibrasyon->nokta1sapma)) ||
                                    ($kalibrasyon->sonuc2 > $kalibrasyon->nokta2sapma) || ($kalibrasyon->sonuc2 < -($kalibrasyon->nokta2sapma)) ||
                                    ($kalibrasyon->sonuc3 > $kalibrasyon->nokta3sapma) || ($kalibrasyon->sonuc3 < -($kalibrasyon->nokta3sapma)) ||
                                    ($kalibrasyon->sonuc4 > $kalibrasyon->nokta4sapma) || ($kalibrasyon->sonuc4 < -($kalibrasyon->nokta4sapma))
                                ) {
                                    $kalibrasyon->durum = 2; //başarısız
                                    $flag = 1;
                                }
                                if ($hf2) {
                                    $kalibrasyon->hf2 = 1;
                                    $kalibrasyon->hf2nokta1sapma = $nokta1;
                                    $kalibrasyon->hf2sonuc1 = str_replace(',', '.', $sonuc2[$count][0]);
                                    $kalibrasyon->hf2nokta2sapma = $nokta2;
                                    $kalibrasyon->hf2sonuc2 = str_replace(',', '.', $sonuc2[$count][1]);
                                    $kalibrasyon->hf2nokta3sapma = $nokta3;
                                    $kalibrasyon->hf2sonuc3 = str_replace(',', '.', $sonuc2[$count][2]);
                                    $kalibrasyon->hf2nokta4sapma = $nokta4;
                                    $kalibrasyon->hf2sonuc4 = str_replace(',', '.', $sonuc2[$count][3]);
                                    if (!$flag) {
                                        if (($kalibrasyon->hf2sonuc1 > $kalibrasyon->hf2nokta1sapma) || ($kalibrasyon->hf2sonuc1 < -($kalibrasyon->hf2nokta1sapma)) ||
                                            ($kalibrasyon->hf2sonuc2 > $kalibrasyon->hf2nokta2sapma) || ($kalibrasyon->hf2sonuc2 < -($kalibrasyon->hf2nokta2sapma)) ||
                                            ($kalibrasyon->hf2sonuc3 > $kalibrasyon->hf2nokta3sapma) || ($kalibrasyon->hf2sonuc3 < -($kalibrasyon->hf2nokta3sapma)) ||
                                            ($kalibrasyon->hf2sonuc4 > $kalibrasyon->hf2nokta4sapma) || ($kalibrasyon->hf2sonuc4 < -($kalibrasyon->hf2nokta4sapma))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $flag = 1;
                                        }
                                    }
                                }
                                if ($hf3) {
                                    $kalibrasyon->hf3 = 1;
                                    $kalibrasyon->hf3nokta1sapma = $nokta1;
                                    $kalibrasyon->hf3sonuc1 = str_replace(',', '.', $sonuc3[$count][0]);
                                    $kalibrasyon->hf3nokta2sapma = $nokta2;
                                    $kalibrasyon->hf3sonuc2 = str_replace(',', '.', $sonuc3[$count][1]);
                                    $kalibrasyon->hf3nokta3sapma = $nokta3;
                                    $kalibrasyon->hf3sonuc3 = str_replace(',', '.', $sonuc3[$count][2]);
                                    $kalibrasyon->hf3nokta4sapma = $nokta4;
                                    $kalibrasyon->hf3sonuc4 = str_replace(',', '.', $sonuc3[$count][3]);
                                    if (!$flag) {
                                        if (($kalibrasyon->hf3sonuc1 > $kalibrasyon->hf3nokta1sapma) || ($kalibrasyon->hf3sonuc1 < -($kalibrasyon->hf3nokta1sapma)) ||
                                            ($kalibrasyon->hf3sonuc2 > $kalibrasyon->hf3nokta2sapma) || ($kalibrasyon->hf3sonuc2 < -($kalibrasyon->hf3nokta2sapma)) ||
                                            ($kalibrasyon->hf3sonuc3 > $kalibrasyon->hf3nokta3sapma) || ($kalibrasyon->hf3sonuc3 < -($kalibrasyon->hf3nokta3sapma)) ||
                                            ($kalibrasyon->hf3sonuc4 > $kalibrasyon->hf3nokta4sapma) || ($kalibrasyon->hf3sonuc4 < -($kalibrasyon->hf3nokta4sapma))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $flag = 1;
                                        }
                                    }
                                }
                                if ($hf32) {
                                    $kalibrasyon->hf32 = 1;
                                    $kalibrasyon->hf32nokta1sapma = $nokta1;
                                    $kalibrasyon->hf32sonuc1 = str_replace(',', '.', $sonuc4[$count][0]);
                                    $kalibrasyon->hf32nokta2sapma = $nokta2;
                                    $kalibrasyon->hf32sonuc2 = str_replace(',', '.', $sonuc4[$count][1]);
                                    $kalibrasyon->hf32nokta3sapma = $nokta3;
                                    $kalibrasyon->hf32sonuc3 = str_replace(',', '.', $sonuc4[$count][2]);
                                    $kalibrasyon->hf32nokta4sapma = $nokta4;
                                    $kalibrasyon->hf32sonuc4 = str_replace(',', '.', $sonuc4[$count][3]);
                                    if (!$flag) {
                                        if (($kalibrasyon->hf32sonuc1 > $kalibrasyon->hf32nokta1sapma) || ($kalibrasyon->hf32sonuc1 < -($kalibrasyon->hf32nokta1sapma)) ||
                                            ($kalibrasyon->hf32sonuc2 > $kalibrasyon->hf32nokta2sapma) || ($kalibrasyon->hf32sonuc2 < -($kalibrasyon->hf32nokta2sapma)) ||
                                            ($kalibrasyon->hf32sonuc3 > $kalibrasyon->hf32nokta3sapma) || ($kalibrasyon->hf32sonuc3 < -($kalibrasyon->hf32nokta3sapma)) ||
                                            ($kalibrasyon->hf32sonuc4 > $kalibrasyon->hf32nokta4sapma) || ($kalibrasyon->hf32sonuc4 < -($kalibrasyon->hf32nokta4sapma))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $flag = 1;
                                        }
                                    }
                                }
                                break;
                            case 5:
                                $nokta4 = Input::get('nokta4');
                                $nokta5 = Input::get('nokta5');
                                $kalibrasyon->nokta1sapma = $nokta1;
                                $kalibrasyon->sonuc1 = str_replace(',', '.', $sonuc1[$count][0]);
                                $kalibrasyon->nokta2sapma = $nokta2;
                                $kalibrasyon->sonuc2 = str_replace(',', '.', $sonuc1[$count][1]);
                                $kalibrasyon->nokta3sapma = $nokta3;
                                $kalibrasyon->sonuc3 = str_replace(',', '.', $sonuc1[$count][2]);
                                $kalibrasyon->nokta4sapma = $nokta4;
                                $kalibrasyon->sonuc4 = str_replace(',', '.', $sonuc1[$count][3]);
                                $kalibrasyon->nokta5sapma = $nokta5;
                                $kalibrasyon->sonuc5 = str_replace(',', '.', $sonuc1[$count][4]);
                                if (($kalibrasyon->sonuc1 > $kalibrasyon->nokta1sapma) || ($kalibrasyon->sonuc1 < -($kalibrasyon->nokta1sapma)) ||
                                    ($kalibrasyon->sonuc2 > $kalibrasyon->nokta2sapma) || ($kalibrasyon->sonuc2 < -($kalibrasyon->nokta2sapma)) ||
                                    ($kalibrasyon->sonuc3 > $kalibrasyon->nokta3sapma) || ($kalibrasyon->sonuc3 < -($kalibrasyon->nokta3sapma)) ||
                                    ($kalibrasyon->sonuc4 > $kalibrasyon->nokta4sapma) || ($kalibrasyon->sonuc4 < -($kalibrasyon->nokta4sapma)) ||
                                    ($kalibrasyon->sonuc5 > $kalibrasyon->nokta5sapma) || ($kalibrasyon->sonuc5 < -($kalibrasyon->nokta5sapma))
                                ) {
                                    $kalibrasyon->durum = 2; //başarısız
                                    $flag = 1;
                                }
                                if ($hf2) {
                                    $kalibrasyon->hf2 = 1;
                                    $kalibrasyon->hf2nokta1sapma = $nokta1;
                                    $kalibrasyon->hf2sonuc1 = str_replace(',', '.', $sonuc2[$count][0]);
                                    $kalibrasyon->hf2nokta2sapma = $nokta2;
                                    $kalibrasyon->hf2sonuc2 = str_replace(',', '.', $sonuc2[$count][1]);
                                    $kalibrasyon->hf2nokta3sapma = $nokta3;
                                    $kalibrasyon->hf2sonuc3 = str_replace(',', '.', $sonuc2[$count][2]);
                                    $kalibrasyon->hf2nokta4sapma = $nokta4;
                                    $kalibrasyon->hf2sonuc4 = str_replace(',', '.', $sonuc2[$count][3]);
                                    $kalibrasyon->hf2nokta5sapma = $nokta5;
                                    $kalibrasyon->hf2sonuc5 = str_replace(',', '.', $sonuc2[$count][4]);
                                    if (!$flag) {
                                        if (($kalibrasyon->hf2sonuc1 > $kalibrasyon->hf2nokta1sapma) || ($kalibrasyon->hf2sonuc1 < -($kalibrasyon->hf2nokta1sapma)) ||
                                            ($kalibrasyon->hf2sonuc2 > $kalibrasyon->hf2nokta2sapma) || ($kalibrasyon->hf2sonuc2 < -($kalibrasyon->hf2nokta2sapma)) ||
                                            ($kalibrasyon->hf2sonuc3 > $kalibrasyon->hf2nokta3sapma) || ($kalibrasyon->hf2sonuc3 < -($kalibrasyon->hf2nokta3sapma)) ||
                                            ($kalibrasyon->hf2sonuc4 > $kalibrasyon->hf2nokta4sapma) || ($kalibrasyon->hf2sonuc4 < -($kalibrasyon->hf2nokta4sapma)) ||
                                            ($kalibrasyon->hf2sonuc5 > $kalibrasyon->hf2nokta5sapma) || ($kalibrasyon->hf2sonuc5 < -($kalibrasyon->hf2nokta5sapma))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $flag = 1;
                                        }
                                    }
                                }
                                if ($hf3) {
                                    $kalibrasyon->hf3 = 1;
                                    $kalibrasyon->hf3nokta1sapma = $nokta1;
                                    $kalibrasyon->hf3sonuc1 = str_replace(',', '.', $sonuc3[$count][0]);
                                    $kalibrasyon->hf3nokta2sapma = $nokta2;
                                    $kalibrasyon->hf3sonuc2 = str_replace(',', '.', $sonuc3[$count][1]);
                                    $kalibrasyon->hf3nokta3sapma = $nokta3;
                                    $kalibrasyon->hf3sonuc3 = str_replace(',', '.', $sonuc3[$count][2]);
                                    $kalibrasyon->hf3nokta4sapma = $nokta4;
                                    $kalibrasyon->hf3sonuc4 = str_replace(',', '.', $sonuc3[$count][3]);
                                    $kalibrasyon->hf3nokta5sapma = $nokta5;
                                    $kalibrasyon->hf3sonuc5 = str_replace(',', '.', $sonuc3[$count][4]);
                                    if (!$flag) {
                                        if (($kalibrasyon->hf3sonuc1 > $kalibrasyon->hf3nokta1sapma) || ($kalibrasyon->hf3sonuc1 < -($kalibrasyon->hf3nokta1sapma)) ||
                                            ($kalibrasyon->hf3sonuc2 > $kalibrasyon->hf3nokta2sapma) || ($kalibrasyon->hf3sonuc2 < -($kalibrasyon->hf3nokta2sapma)) ||
                                            ($kalibrasyon->hf3sonuc3 > $kalibrasyon->hf3nokta3sapma) || ($kalibrasyon->hf3sonuc3 < -($kalibrasyon->hf3nokta3sapma)) ||
                                            ($kalibrasyon->hf3sonuc4 > $kalibrasyon->hf3nokta4sapma) || ($kalibrasyon->hf3sonuc4 < -($kalibrasyon->hf3nokta4sapma)) ||
                                            ($kalibrasyon->hf3sonuc5 > $kalibrasyon->hf3nokta5sapma) || ($kalibrasyon->hf3sonuc5 < -($kalibrasyon->hf3nokta5sapma))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $flag = 1;
                                        }
                                    }
                                }
                                if ($hf32) {
                                    $kalibrasyon->hf32 = 1;
                                    $kalibrasyon->hf32nokta1sapma = $nokta1;
                                    $kalibrasyon->hf32sonuc1 = str_replace(',', '.', $sonuc4[$count][0]);
                                    $kalibrasyon->hf32nokta2sapma = $nokta2;
                                    $kalibrasyon->hf32sonuc2 = str_replace(',', '.', $sonuc4[$count][1]);
                                    $kalibrasyon->hf32nokta3sapma = $nokta3;
                                    $kalibrasyon->hf32sonuc3 = str_replace(',', '.', $sonuc4[$count][2]);
                                    $kalibrasyon->hf32nokta4sapma = $nokta4;
                                    $kalibrasyon->hf32sonuc4 = str_replace(',', '.', $sonuc4[$count][3]);
                                    $kalibrasyon->hf32nokta5sapma = $nokta5;
                                    $kalibrasyon->hf32sonuc5 = str_replace(',', '.', $sonuc4[$count][4]);
                                    if (!$flag) {
                                        if (($kalibrasyon->hf32sonuc1 > $kalibrasyon->hf32nokta1sapma) || ($kalibrasyon->hf32sonuc1 < -($kalibrasyon->hf32nokta1sapma)) ||
                                            ($kalibrasyon->hf32sonuc2 > $kalibrasyon->hf32nokta2sapma) || ($kalibrasyon->hf32sonuc2 < -($kalibrasyon->hf32nokta2sapma)) ||
                                            ($kalibrasyon->hf32sonuc3 > $kalibrasyon->hf32nokta3sapma) || ($kalibrasyon->hf32sonuc3 < -($kalibrasyon->hf32nokta3sapma)) ||
                                            ($kalibrasyon->hf32sonuc4 > $kalibrasyon->hf32nokta4sapma) || ($kalibrasyon->hf32sonuc4 < -($kalibrasyon->hf32nokta4sapma)) ||
                                            ($kalibrasyon->hf32sonuc5 > $kalibrasyon->hf32nokta5sapma) || ($kalibrasyon->hf32sonuc5 < -($kalibrasyon->hf32nokta5sapma))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $flag = 1;
                                        }
                                    }
                                }
                                break;
                            case 6:
                                $nokta4 = Input::get('nokta4');
                                $nokta5 = Input::get('nokta5');
                                $nokta6 = Input::get('nokta6');
                                $kalibrasyon->nokta1sapma = $nokta1;
                                $kalibrasyon->sonuc1 = str_replace(',', '.', $sonuc1[$count][0]);
                                $kalibrasyon->nokta2sapma = $nokta2;
                                $kalibrasyon->sonuc2 = str_replace(',', '.', $sonuc1[$count][1]);
                                $kalibrasyon->nokta3sapma = $nokta3;
                                $kalibrasyon->sonuc3 = str_replace(',', '.', $sonuc1[$count][2]);
                                $kalibrasyon->nokta4sapma = $nokta4;
                                $kalibrasyon->sonuc4 = str_replace(',', '.', $sonuc1[$count][3]);
                                $kalibrasyon->nokta5sapma = $nokta5;
                                $kalibrasyon->sonuc5 = str_replace(',', '.', $sonuc1[$count][4]);
                                $kalibrasyon->nokta6sapma = $nokta6;
                                $kalibrasyon->sonuc6 = str_replace(',', '.', $sonuc1[$count][5]);
                                if (($kalibrasyon->sonuc1 > $kalibrasyon->nokta1sapma) || ($kalibrasyon->sonuc1 < -($kalibrasyon->nokta1sapma)) ||
                                    ($kalibrasyon->sonuc2 > $kalibrasyon->nokta2sapma) || ($kalibrasyon->sonuc2 < -($kalibrasyon->nokta2sapma)) ||
                                    ($kalibrasyon->sonuc3 > $kalibrasyon->nokta3sapma) || ($kalibrasyon->sonuc3 < -($kalibrasyon->nokta3sapma)) ||
                                    ($kalibrasyon->sonuc4 > $kalibrasyon->nokta4sapma) || ($kalibrasyon->sonuc4 < -($kalibrasyon->nokta4sapma)) ||
                                    ($kalibrasyon->sonuc5 > $kalibrasyon->nokta5sapma) || ($kalibrasyon->sonuc5 < -($kalibrasyon->nokta5sapma)) ||
                                    ($kalibrasyon->sonuc6 > $kalibrasyon->nokta6sapma) || ($kalibrasyon->sonuc6 < -($kalibrasyon->nokta6sapma))
                                ) {
                                    $kalibrasyon->durum = 2; //başarısız
                                    $flag = 1;
                                }
                                if ($hf2) {
                                    $kalibrasyon->hf2 = 1;
                                    $kalibrasyon->hf2nokta1sapma = $nokta1;
                                    $kalibrasyon->hf2sonuc1 = str_replace(',', '.', $sonuc2[$count][0]);
                                    $kalibrasyon->hf2nokta2sapma = $nokta2;
                                    $kalibrasyon->hf2sonuc2 = str_replace(',', '.', $sonuc2[$count][1]);
                                    $kalibrasyon->hf2nokta3sapma = $nokta3;
                                    $kalibrasyon->hf2sonuc3 = str_replace(',', '.', $sonuc2[$count][2]);
                                    $kalibrasyon->hf2nokta4sapma = $nokta4;
                                    $kalibrasyon->hf2sonuc4 = str_replace(',', '.', $sonuc2[$count][3]);
                                    $kalibrasyon->hf2nokta5sapma = $nokta5;
                                    $kalibrasyon->hf2sonuc5 = str_replace(',', '.', $sonuc2[$count][4]);
                                    $kalibrasyon->hf2nokta6sapma = $nokta6;
                                    $kalibrasyon->hf2sonuc6 = str_replace(',', '.', $sonuc2[$count][5]);
                                    if (!$flag) {
                                        if (($kalibrasyon->hf2sonuc1 > $kalibrasyon->hf2nokta1sapma) || ($kalibrasyon->hf2sonuc1 < -($kalibrasyon->hf2nokta1sapma)) ||
                                            ($kalibrasyon->hf2sonuc2 > $kalibrasyon->hf2nokta2sapma) || ($kalibrasyon->hf2sonuc2 < -($kalibrasyon->hf2nokta2sapma)) ||
                                            ($kalibrasyon->hf2sonuc3 > $kalibrasyon->hf2nokta3sapma) || ($kalibrasyon->hf2sonuc3 < -($kalibrasyon->hf2nokta3sapma)) ||
                                            ($kalibrasyon->hf2sonuc4 > $kalibrasyon->hf2nokta4sapma) || ($kalibrasyon->hf2sonuc4 < -($kalibrasyon->hf2nokta4sapma)) ||
                                            ($kalibrasyon->hf2sonuc5 > $kalibrasyon->hf2nokta5sapma) || ($kalibrasyon->hf2sonuc5 < -($kalibrasyon->hf2nokta5sapma)) ||
                                            ($kalibrasyon->hf2sonuc6 > $kalibrasyon->hf2nokta6sapma) || ($kalibrasyon->hf2sonuc6 < -($kalibrasyon->hf2nokta6sapma))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $flag = 1;
                                        }
                                    }
                                }
                                if ($hf3) {
                                    $kalibrasyon->hf3 = 1;
                                    $kalibrasyon->hf3nokta1sapma = $nokta1;
                                    $kalibrasyon->hf3sonuc1 = str_replace(',', '.', $sonuc3[$count][0]);
                                    $kalibrasyon->hf3nokta2sapma = $nokta2;
                                    $kalibrasyon->hf3sonuc2 = str_replace(',', '.', $sonuc3[$count][1]);
                                    $kalibrasyon->hf3nokta3sapma = $nokta3;
                                    $kalibrasyon->hf3sonuc3 = str_replace(',', '.', $sonuc3[$count][2]);
                                    $kalibrasyon->hf3nokta4sapma = $nokta4;
                                    $kalibrasyon->hf3sonuc4 = str_replace(',', '.', $sonuc3[$count][3]);
                                    $kalibrasyon->hf3nokta5sapma = $nokta5;
                                    $kalibrasyon->hf3sonuc5 = str_replace(',', '.', $sonuc3[$count][4]);
                                    $kalibrasyon->hf3nokta6sapma = $nokta6;
                                    $kalibrasyon->hf3sonuc6 = str_replace(',', '.', $sonuc3[$count][5]);
                                    if (!$flag) {
                                        if (($kalibrasyon->hf3sonuc1 > $kalibrasyon->hf3nokta1sapma) || ($kalibrasyon->hf3sonuc1 < -($kalibrasyon->hf3nokta1sapma)) ||
                                            ($kalibrasyon->hf3sonuc2 > $kalibrasyon->hf3nokta2sapma) || ($kalibrasyon->hf3sonuc2 < -($kalibrasyon->hf3nokta2sapma)) ||
                                            ($kalibrasyon->hf3sonuc3 > $kalibrasyon->hf3nokta3sapma) || ($kalibrasyon->hf3sonuc3 < -($kalibrasyon->hf3nokta3sapma)) ||
                                            ($kalibrasyon->hf3sonuc4 > $kalibrasyon->hf3nokta4sapma) || ($kalibrasyon->hf3sonuc4 < -($kalibrasyon->hf3nokta4sapma)) ||
                                            ($kalibrasyon->hf3sonuc5 > $kalibrasyon->hf3nokta5sapma) || ($kalibrasyon->hf3sonuc5 < -($kalibrasyon->hf3nokta5sapma)) ||
                                            ($kalibrasyon->hf3sonuc6 > $kalibrasyon->hf3nokta6sapma) || ($kalibrasyon->hf3sonuc6 < -($kalibrasyon->hf3nokta6sapma))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $flag = 1;
                                        }
                                    }
                                }
                                if ($hf32) {
                                    $kalibrasyon->hf32 = 1;
                                    $kalibrasyon->hf32nokta1sapma = $nokta1;
                                    $kalibrasyon->hf32sonuc1 = str_replace(',', '.', $sonuc4[$count][0]);
                                    $kalibrasyon->hf32nokta2sapma = $nokta2;
                                    $kalibrasyon->hf32sonuc2 = str_replace(',', '.', $sonuc4[$count][1]);
                                    $kalibrasyon->hf32nokta3sapma = $nokta3;
                                    $kalibrasyon->hf32sonuc3 = str_replace(',', '.', $sonuc4[$count][2]);
                                    $kalibrasyon->hf32nokta4sapma = $nokta4;
                                    $kalibrasyon->hf32sonuc4 = str_replace(',', '.', $sonuc4[$count][3]);
                                    $kalibrasyon->hf32nokta5sapma = $nokta5;
                                    $kalibrasyon->hf32sonuc5 = str_replace(',', '.', $sonuc4[$count][4]);
                                    $kalibrasyon->hf32nokta6sapma = $nokta6;
                                    $kalibrasyon->hf32sonuc6 = str_replace(',', '.', $sonuc4[$count][5]);
                                    if (!$flag) {
                                        if (($kalibrasyon->hf32sonuc1 > $kalibrasyon->hf32nokta1sapma) || ($kalibrasyon->hf32sonuc1 < -($kalibrasyon->hf32nokta1sapma)) ||
                                            ($kalibrasyon->hf32sonuc2 > $kalibrasyon->hf32nokta2sapma) || ($kalibrasyon->hf32sonuc2 < -($kalibrasyon->hf32nokta2sapma)) ||
                                            ($kalibrasyon->hf32sonuc3 > $kalibrasyon->hf32nokta3sapma) || ($kalibrasyon->hf32sonuc3 < -($kalibrasyon->hf32nokta3sapma)) ||
                                            ($kalibrasyon->hf32sonuc4 > $kalibrasyon->hf32nokta4sapma) || ($kalibrasyon->hf32sonuc4 < -($kalibrasyon->hf32nokta4sapma)) ||
                                            ($kalibrasyon->hf32sonuc5 > $kalibrasyon->hf32nokta5sapma) || ($kalibrasyon->hf32sonuc5 < -($kalibrasyon->hf32nokta5sapma)) ||
                                            ($kalibrasyon->hf32sonuc6 > $kalibrasyon->hf32nokta6sapma) || ($kalibrasyon->hf32sonuc6 < -($kalibrasyon->hf32nokta6sapma))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $flag = 1;
                                        }
                                    }
                                }
                                break;
                            case 7:
                                $nokta4 = Input::get('nokta4');
                                $nokta5 = Input::get('nokta5');
                                $nokta6 = Input::get('nokta6');
                                $nokta7 = Input::get('nokta7');
                                $kalibrasyon->nokta1sapma = $nokta1;
                                $kalibrasyon->sonuc1 = str_replace(',', '.', $sonuc1[$count][0]);
                                $kalibrasyon->nokta2sapma = $nokta2;
                                $kalibrasyon->sonuc2 = str_replace(',', '.', $sonuc1[$count][1]);
                                $kalibrasyon->nokta3sapma = $nokta3;
                                $kalibrasyon->sonuc3 = str_replace(',', '.', $sonuc1[$count][2]);
                                $kalibrasyon->nokta4sapma = $nokta4;
                                $kalibrasyon->sonuc4 = str_replace(',', '.', $sonuc1[$count][3]);
                                $kalibrasyon->nokta5sapma = $nokta5;
                                $kalibrasyon->sonuc5 = str_replace(',', '.', $sonuc1[$count][4]);
                                $kalibrasyon->nokta6sapma = $nokta6;
                                $kalibrasyon->sonuc6 = str_replace(',', '.', $sonuc1[$count][5]);
                                $kalibrasyon->nokta7sapma = $nokta7;
                                $kalibrasyon->sonuc7 = str_replace(',', '.', $sonuc1[$count][6]);
                                if (($kalibrasyon->sonuc1 > $kalibrasyon->nokta1sapma) || ($kalibrasyon->sonuc1 < -($kalibrasyon->nokta1sapma)) ||
                                    ($kalibrasyon->sonuc2 > $kalibrasyon->nokta2sapma) || ($kalibrasyon->sonuc2 < -($kalibrasyon->nokta2sapma)) ||
                                    ($kalibrasyon->sonuc3 > $kalibrasyon->nokta3sapma) || ($kalibrasyon->sonuc3 < -($kalibrasyon->nokta3sapma)) ||
                                    ($kalibrasyon->sonuc4 > $kalibrasyon->nokta4sapma) || ($kalibrasyon->sonuc4 < -($kalibrasyon->nokta4sapma)) ||
                                    ($kalibrasyon->sonuc5 > $kalibrasyon->nokta5sapma) || ($kalibrasyon->sonuc5 < -($kalibrasyon->nokta5sapma)) ||
                                    ($kalibrasyon->sonuc6 > $kalibrasyon->nokta6sapma) || ($kalibrasyon->sonuc6 < -($kalibrasyon->nokta6sapma)) ||
                                    ($kalibrasyon->sonuc7 > $kalibrasyon->nokta7sapma) || ($kalibrasyon->sonuc7 < -($kalibrasyon->nokta7sapma))
                                ) {
                                    $kalibrasyon->durum = 2; //başarısız
                                    $flag = 1;
                                }
                                if ($hf2) {
                                    $kalibrasyon->hf2 = 1;
                                    $kalibrasyon->hf2nokta1sapma = $nokta1;
                                    $kalibrasyon->hf2sonuc1 = str_replace(',', '.', $sonuc2[$count][0]);
                                    $kalibrasyon->hf2nokta2sapma = $nokta2;
                                    $kalibrasyon->hf2sonuc2 = str_replace(',', '.', $sonuc2[$count][1]);
                                    $kalibrasyon->hf2nokta3sapma = $nokta3;
                                    $kalibrasyon->hf2sonuc3 = str_replace(',', '.', $sonuc2[$count][2]);
                                    $kalibrasyon->hf2nokta4sapma = $nokta4;
                                    $kalibrasyon->hf2sonuc4 = str_replace(',', '.', $sonuc2[$count][3]);
                                    $kalibrasyon->hf2nokta5sapma = $nokta5;
                                    $kalibrasyon->hf2sonuc5 = str_replace(',', '.', $sonuc2[$count][4]);
                                    $kalibrasyon->hf2nokta6sapma = $nokta6;
                                    $kalibrasyon->hf2sonuc6 = str_replace(',', '.', $sonuc2[$count][5]);
                                    $kalibrasyon->hf2nokta7sapma = $nokta7;
                                    $kalibrasyon->hf2sonuc7 = str_replace(',', '.', $sonuc2[$count][6]);
                                    if (!$flag) {
                                        if (($kalibrasyon->hf2sonuc1 > $kalibrasyon->hf2nokta1sapma) || ($kalibrasyon->hf2sonuc1 < -($kalibrasyon->hf2nokta1sapma)) ||
                                            ($kalibrasyon->hf2sonuc2 > $kalibrasyon->hf2nokta2sapma) || ($kalibrasyon->hf2sonuc2 < -($kalibrasyon->hf2nokta2sapma)) ||
                                            ($kalibrasyon->hf2sonuc3 > $kalibrasyon->hf2nokta3sapma) || ($kalibrasyon->hf2sonuc3 < -($kalibrasyon->hf2nokta3sapma)) ||
                                            ($kalibrasyon->hf2sonuc4 > $kalibrasyon->hf2nokta4sapma) || ($kalibrasyon->hf2sonuc4 < -($kalibrasyon->hf2nokta4sapma)) ||
                                            ($kalibrasyon->hf2sonuc5 > $kalibrasyon->hf2nokta5sapma) || ($kalibrasyon->hf2sonuc5 < -($kalibrasyon->hf2nokta5sapma)) ||
                                            ($kalibrasyon->hf2sonuc6 > $kalibrasyon->hf2nokta6sapma) || ($kalibrasyon->hf2sonuc6 < -($kalibrasyon->hf2nokta6sapma)) ||
                                            ($kalibrasyon->hf2sonuc7 > $kalibrasyon->hf2nokta7sapma) || ($kalibrasyon->hf2sonuc7 < -($kalibrasyon->hf2nokta7sapma))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $flag = 1;
                                        }
                                    }
                                }
                                if ($hf3) {
                                    $kalibrasyon->hf3 = 1;
                                    $kalibrasyon->hf3nokta1sapma = $nokta1;
                                    $kalibrasyon->hf3sonuc1 = str_replace(',', '.', $sonuc3[$count][0]);
                                    $kalibrasyon->hf3nokta2sapma = $nokta2;
                                    $kalibrasyon->hf3sonuc2 = str_replace(',', '.', $sonuc3[$count][1]);
                                    $kalibrasyon->hf3nokta3sapma = $nokta3;
                                    $kalibrasyon->hf3sonuc3 = str_replace(',', '.', $sonuc3[$count][2]);
                                    $kalibrasyon->hf3nokta4sapma = $nokta4;
                                    $kalibrasyon->hf3sonuc4 = str_replace(',', '.', $sonuc3[$count][3]);
                                    $kalibrasyon->hf3nokta5sapma = $nokta5;
                                    $kalibrasyon->hf3sonuc5 = str_replace(',', '.', $sonuc3[$count][4]);
                                    $kalibrasyon->hf3nokta6sapma = $nokta6;
                                    $kalibrasyon->hf3sonuc6 = str_replace(',', '.', $sonuc3[$count][5]);
                                    $kalibrasyon->hf3nokta7sapma = $nokta7;
                                    $kalibrasyon->hf3sonuc7 = str_replace(',', '.', $sonuc3[$count][6]);
                                    if (!$flag) {
                                        if (($kalibrasyon->hf3sonuc1 > $kalibrasyon->hf3nokta1sapma) || ($kalibrasyon->hf3sonuc1 < -($kalibrasyon->hf3nokta1sapma)) ||
                                            ($kalibrasyon->hf3sonuc2 > $kalibrasyon->hf3nokta2sapma) || ($kalibrasyon->hf3sonuc2 < -($kalibrasyon->hf3nokta2sapma)) ||
                                            ($kalibrasyon->hf3sonuc3 > $kalibrasyon->hf3nokta3sapma) || ($kalibrasyon->hf3sonuc3 < -($kalibrasyon->hf3nokta3sapma)) ||
                                            ($kalibrasyon->hf3sonuc4 > $kalibrasyon->hf3nokta4sapma) || ($kalibrasyon->hf3sonuc4 < -($kalibrasyon->hf3nokta4sapma)) ||
                                            ($kalibrasyon->hf3sonuc5 > $kalibrasyon->hf3nokta5sapma) || ($kalibrasyon->hf3sonuc5 < -($kalibrasyon->hf3nokta5sapma)) ||
                                            ($kalibrasyon->hf3sonuc6 > $kalibrasyon->hf3nokta6sapma) || ($kalibrasyon->hf3sonuc6 < -($kalibrasyon->hf3nokta6sapma)) ||
                                            ($kalibrasyon->hf3sonuc7 > $kalibrasyon->hf3nokta7sapma) || ($kalibrasyon->hf3sonuc7 < -($kalibrasyon->hf3nokta7sapma))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $flag = 1;
                                        }
                                    }
                                }
                                if ($hf32) {
                                    $kalibrasyon->hf32 = 1;
                                    $kalibrasyon->hf32nokta1sapma = $nokta1;
                                    $kalibrasyon->hf32sonuc1 = str_replace(',', '.', $sonuc4[$count][0]);
                                    $kalibrasyon->hf32nokta2sapma = $nokta2;
                                    $kalibrasyon->hf32sonuc2 = str_replace(',', '.', $sonuc4[$count][1]);
                                    $kalibrasyon->hf32nokta3sapma = $nokta3;
                                    $kalibrasyon->hf32sonuc3 = str_replace(',', '.', $sonuc4[$count][2]);
                                    $kalibrasyon->hf32nokta4sapma = $nokta4;
                                    $kalibrasyon->hf32sonuc4 = str_replace(',', '.', $sonuc4[$count][3]);
                                    $kalibrasyon->hf32nokta5sapma = $nokta5;
                                    $kalibrasyon->hf32sonuc5 = str_replace(',', '.', $sonuc4[$count][4]);
                                    $kalibrasyon->hf32nokta6sapma = $nokta6;
                                    $kalibrasyon->hf32sonuc6 = str_replace(',', '.', $sonuc4[$count][5]);
                                    $kalibrasyon->hf32nokta7sapma = $nokta7;
                                    $kalibrasyon->hf32sonuc7 = str_replace(',', '.', $sonuc4[$count][6]);
                                    if (!$flag) {
                                        if (($kalibrasyon->hf32sonuc1 > $kalibrasyon->hf32nokta1sapma) || ($kalibrasyon->hf32sonuc1 < -($kalibrasyon->hf32nokta1sapma)) ||
                                            ($kalibrasyon->hf32sonuc2 > $kalibrasyon->hf32nokta2sapma) || ($kalibrasyon->hf32sonuc2 < -($kalibrasyon->hf32nokta2sapma)) ||
                                            ($kalibrasyon->hf32sonuc3 > $kalibrasyon->hf32nokta3sapma) || ($kalibrasyon->hf32sonuc3 < -($kalibrasyon->hf32nokta3sapma)) ||
                                            ($kalibrasyon->hf32sonuc4 > $kalibrasyon->hf32nokta4sapma) || ($kalibrasyon->hf32sonuc4 < -($kalibrasyon->hf32nokta4sapma)) ||
                                            ($kalibrasyon->hf32sonuc5 > $kalibrasyon->hf32nokta5sapma) || ($kalibrasyon->hf32sonuc5 < -($kalibrasyon->hf32nokta5sapma)) ||
                                            ($kalibrasyon->hf32sonuc6 > $kalibrasyon->hf32nokta6sapma) || ($kalibrasyon->hf32sonuc6 < -($kalibrasyon->hf32nokta6sapma)) ||
                                            ($kalibrasyon->hf32sonuc7 > $kalibrasyon->hf32nokta7sapma) || ($kalibrasyon->hf32sonuc7 < -($kalibrasyon->hf32nokta7sapma))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $flag = 1;
                                        }
                                    }
                                }
                                break;
                        }
                        if ($flag == 0) {
                            $kalibrasyon->durum = 1;
                            $basarili++;
                        }
                        $count++;
                        $kalibrasyon->kullanici_id = Auth::user()->id;
                        $kalibrasyon->kalibrasyontarih = date('Y-m-d H:i:s');
                        $kalibrasyon->save();
                        if ($kalibrasyon->durum == 1) { //kalibrasyon geçtiyse ucretlendirme tamamsa depo teslime aktarılacak yoksa bekleyecek
                            try {
                                $sayacgelen = SayacGelen::find($kalibrasyon->sayacgelen_id);
                                $sayacgelen->kalibrasyon = 1;
                                $sayacgelen->beyanname = $sayacgelen->beyanname==-2 ? 0 : $sayacgelen->beyanname;
                                $sayacgelen->save();
                                $servistakip = ServisTakip::where('kalibrasyon_id', $kalibrasyon->id)->first();
                                $servistakip->durum = 8;
                                $servistakip->kalibrasyontarihi = date('Y-m-d H:i:s');
                                $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                                $servistakip->kullanici_id = Auth::user()->id;
                                $servistakip->save();
                                if(!$kalibrasyongrup->periyodik){ //periyodik bakımında arıza kayıdı yapılmıyor
                                    $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
                                    if($arizakayit->arizakayit_durum==4) //şikayetli sayaç ise
                                        $arizakayit->rapordurum = -1;
                                    else
                                        $arizakayit->rapordurum = 0 ;
                                    $arizakayit->save();
                                }
                                $uretimyer=UretimYer::find($servistakip->uretimyer_id);
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Bilgi Güncelleme Hatası', 'text' => 'Kalibrasyon yapılan sayaçların sayaç gelen ve servistakip bilgisi güncellenemedi', 'type' => 'error'));
                            }
                            try {
                                $flag = 0;
                                if($sayacgelen->musterionay || $kalibrasyongrup->periyodik) {
                                    $sayacgelen->teslimdurum=1;
                                    $sayacgelen->save();
                                    if($kalibrasyongrup->periyodik){
                                        $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                            ->where('depodurum', 0)->where('tipi', 0)->where('periyodik', $kalibrasyongrup->periyodik)->where('subegonderim',0)->first();
                                        if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                        {
                                            $secilenlist = explode(',', $depoteslim->secilenler);
                                            if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                $depoteslim->sayacsayisi += 1;
                                                $depoteslim->save();
                                            }else{
                                                $flag = 1;
                                            }
                                        } else { //yeni depo teslimatı yapılacak
                                            $depoteslim = new DepoTeslim;
                                            $depoteslim->servis_id = $sayacgelen->servis_id;
                                            $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                            $depoteslim->secilenler = $sayacgelen->id;
                                            $depoteslim->sayacsayisi = 1;
                                            $depoteslim->depodurum = 0;
                                            $depoteslim->periyodik = $kalibrasyongrup->periyodik;
                                            $depoteslim->parabirimi_id=$uretimyer->parabirimi_id;
                                            $depoteslim->save();
                                        }
                                    }else{
                                        $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                                        $birim = $arizafiyat->parabirimi_id;
                                        $birim2 = $arizafiyat->parabirimi2_id;
                                        $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                        if($arizakayit->arizakayit_durum==7){
                                            $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                ->where('depodurum', 0)->where('tipi', 2)->where('periyodik',0)->where('subegonderim', 0)->first();
                                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                            {
                                                $secilenlist = explode(',', $depoteslim->secilenler);
                                                if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                    $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                    $depoteslim->sayacsayisi += 1;
                                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                    $depoteslim->save();
                                                }else{
                                                    $flag = 1;
                                                }
                                            } else { //yeni depo teslimatı yapılacak
                                                $depoteslim = new DepoTeslim;
                                                $depoteslim->servis_id = $sayacgelen->servis_id;
                                                $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                $depoteslim->secilenler = $sayacgelen->id;
                                                $depoteslim->sayacsayisi = 1;
                                                $depoteslim->depodurum = 0;
                                                $depoteslim->tipi = 2;
                                                $depoteslim->parabirimi_id=$birim;
                                                $depoteslim->parabirimi2_id=$birim2;
                                                $depoteslim->save();
                                            }
                                        }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0){
                                            $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                ->where('depodurum', 0)->where('tipi', 0)->where('periyodik',0)->where('subegonderim', 0)->first();
                                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                            {
                                                $secilenlist = explode(',', $depoteslim->secilenler);
                                                if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                    $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                    $depoteslim->sayacsayisi += 1;
                                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                    $depoteslim->save();
                                                }else{
                                                    $flag = 1;
                                                }
                                            } else { //yeni depo teslimatı yapılacak
                                                $depoteslim = new DepoTeslim;
                                                $depoteslim->servis_id = $sayacgelen->servis_id;
                                                $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                $depoteslim->secilenler = $sayacgelen->id;
                                                $depoteslim->sayacsayisi = 1;
                                                $depoteslim->depodurum = 0;
                                                $depoteslim->parabirimi_id=$birim;
                                                $depoteslim->parabirimi2_id=$birim2;
                                                $depoteslim->save();
                                            }
                                        }else{
                                            $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                ->where('depodurum', 0)->where('tipi',1)->where('periyodik', 0)->where('subegonderim', 0)->first();
                                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                            {
                                                $secilenlist = explode(',', $depoteslim->secilenler);
                                                if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                    $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                    $depoteslim->sayacsayisi += 1;
                                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                    $depoteslim->save();
                                                }else{
                                                    $flag = 1;
                                                }
                                            } else { //yeni depo teslimatı yapılacak
                                                $depoteslim = new DepoTeslim;
                                                $depoteslim->servis_id = $sayacgelen->servis_id;
                                                $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                $depoteslim->secilenler = $sayacgelen->id;
                                                $depoteslim->sayacsayisi = 1;
                                                $depoteslim->depodurum = 0;
                                                $depoteslim->tipi = 1;
                                                $depoteslim->parabirimi_id=$birim;
                                                $depoteslim->parabirimi2_id=$birim2;
                                                $depoteslim->save();
                                            }
                                        }
                                    }
                                    if(!$flag)
                                        BackendController::HatirlatmaEkle(9, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                }
                                if(!$flag){
                                    BackendController::BildirimEkle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                    BackendController::HatirlatmaGuncelle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                }
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Kalibrasyon yapılan sayaçlar depo teslimatına kaydedilemedi', 'type' => 'error'));
                            }
                        } else { //kalibrasyon geçmedi ise kalibrasyon sayısına bakılacak 1. kalibrasyonsa 2. kalibrasyon oluşturulacak 2. kalibrasyonsa hurdaya ayrılacak
                            if ($kalibrasyon->kalibrasyonsayisi == 1) { //2. kalibrasyon oluşturulacak
                                try {
                                    $kalibrasyonyeni = new Kalibrasyon;
                                    $kalibrasyonyeni->sayacgelen_id = $kalibrasyon->sayacgelen_id;
                                    $kalibrasyonyeni->sayacadi_id = $kalibrasyon->sayacadi_id;
                                    $kalibrasyonyeni->kalibrasyon_seri = $kalibrasyon->kalibrasyon_seri;
                                    $kalibrasyonyeni->imalyili = $kalibrasyon->imalyili;
                                    $kalibrasyonyeni->kalibrasyongrup_id = $kalibrasyon->kalibrasyongrup_id;
                                    $kalibrasyonyeni->kalibrasyonsayisi = 2;
                                    $kalibrasyonyeni->durum = 0;
                                    $kalibrasyonyeni->save();
                                    $servistakip = ServisTakip::where('kalibrasyon_id', $kalibrasyon->id)->first();
                                    $servistakip->kalibrasyon_id = $kalibrasyonyeni->id;
                                    $servistakip->save();
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Kalibrasyon yapılamayan sayaçlar için 2. kalibrasyon bilgisi girişi yapılamadı', 'type' => 'error'));
                                }
                            } else { //hurdaya ayrılacak
                                try {
                                    $basarisiz++;
                                    $sayacgelen = SayacGelen::find($kalibrasyon->sayacgelen_id);
                                    $kalibrasyoneski = Kalibrasyon::where('kalibrasyongrup_id', $kalibrasyon->kalibrasyongrup_id)->where('kalibrasyon_seri', $kalibrasyon->kalibrasyon_seri)
                                        ->where('kalibrasyonsayisi', 1)->first();
                                    $sayac = Sayac::where('uretimyer_id', $sayacgelen->uretimyer_id)->where('serino', $sayacgelen->serino)->first();
                                    $hurdakayit = new Hurda;
                                    $hurdakayit->servis_id = $sayacgelen->servis_id;
                                    $hurdakayit->netsiscari_id = $sayacgelen->netsiscari_id;
                                    $hurdakayit->sayac_id = $sayac->id;
                                    $hurdakayit->hurdanedeni_id = 1;
                                    $hurdakayit->hurdatarihi = date('Y-m-d H:i:s');
                                    $hurdakayit->sayacgelen_id = $sayacgelen->id;
                                    $hurdakayit->kalibrasyon_id = $kalibrasyoneski->id;
                                    $hurdakayit->kalibrasyon2_id = $kalibrasyon->id;
                                    $hurdakayit->kullanici_id = Auth::user()->id;
                                    $hurdakayit->save();
                                    try {
                                        $servistakip = ServisTakip::where('serino', $sayacgelen->serino)->where('depogelen_id', $sayacgelen->depogelen_id)->first();
                                        if(!$kalibrasyongrup->periyodik){
                                            $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
                                            $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                                            $sayacyapilan = SayacYapilan::find($arizakayit->sayacyapilan_id);
                                            $sayacuyari = SayacUyari::find($arizakayit->sayacuyari_id);
                                            $arizakayiteski = ArizaKayitEski::where('arizakayit_id',$arizakayit->id)->first();
                                            if($arizakayiteski){
                                                $arizakayiteski->yapilanlar=$sayacyapilan->yapilanlar;
                                                $arizakayiteski->uyarilar=$sayacuyari->uyarilar;
                                                $arizakayiteski->arizakayit_durum=$arizakayit->arizakayit_durum;
                                                $arizakayiteski->save();
                                            }else{
                                                $arizakayiteski = new ArizaKayitEski;
                                                $arizakayiteski->arizakayit_id = $arizakayit->id;
                                                $arizakayiteski->yapilanlar=$sayacyapilan->yapilanlar;
                                                $arizakayiteski->uyarilar=$sayacuyari->uyarilar;
                                                $arizakayiteski->arizakayit_durum=$arizakayit->arizakayit_durum;
                                                $arizakayiteski->save();
                                            }
                                            $sayacyapilan->yapilanlar='58';
                                            $sayacyapilan->save();
                                            $sayacuyari->uyarilar ='11';
                                            $sayacuyari->save();
                                            $arizakayit->arizakayit_durum = 2;
                                            $arizakayit->rapordurum = 0 ;
                                            $arizakayit->save();

                                            if($sayacgelen->fiyatlandirma){
                                                $ucretlendirilen=Ucretlendirilen::find($servistakip->ucretlendirilen_id);
                                                if(!$sayacgelen->musterionay){
                                                    $secilenlist=explode(',',$ucretlendirilen->secilenler);
                                                    $list="";
                                                    foreach ($secilenlist as $secilen){
                                                        if($secilen!=$arizafiyat->id)
                                                            $list.=($list=="" ? "" : ",").$secilen;
                                                    }
                                                    if($ucretlendirilen->sayacsayisi>1){
                                                        $ucretlendirilen->secilenler=$list;
                                                        $ucretlendirilen->sayacsayisi--;
                                                        if($ucretlendirilen->parabirimi_id!=$arizafiyat->parabirimi_id){
                                                            if ($ucretlendirilen->parabirimi_id == 1) { //tl
                                                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi);
                                                            } else { //euro dolar sterln
                                                                if ($arizafiyat->parabirimi_id == 1) {
                                                                    $kur = 1 / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarihi);
                                                                } else {
                                                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $ucretlendirilen->kurtarihi);
                                                                }
                                                            }
                                                            $tutar=$arizafiyat->toplamtutar*$kur;
                                                            if ($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi_id) {
                                                                $tutar += $arizafiyat->toplamtutar2;
                                                            }else if($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi2_id){
                                                                $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                            }else{
                                                                DB::rollBack();
                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                            }
                                                            $ucretlendirilen->fiyat=round(($ucretlendirilen->fiyat - $tutar) * 2) / 2;
                                                        }else{
                                                            $ucretlendirilen->fiyat-=$arizafiyat->toplamtutar;
                                                            if($arizafiyat->parabirimi2_id!=null){
                                                                if($ucretlendirilen->parabirimi2_id!=$arizafiyat->parabirimi2_id){
                                                                    DB::rollBack();
                                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                                }else{
                                                                    $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                                }
                                                            }
                                                        }
                                                        $ucretlendirilen->durum=1;
                                                        $ucretlendirilen->save();
                                                    }else{
                                                        $servistakip->ucretlendirilen_id=NULL;
                                                        $servistakip->save();
                                                        $ucretlendirilen->delete();
                                                    }
                                                    BackendController::HatirlatmaSil(6, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                                }else{
                                                    if($ucretlendirilen->parabirimi_id!=$arizafiyat->parabirimi_id){
                                                        if ($ucretlendirilen->parabirimi_id == 1) { //tl
                                                            $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi);
                                                        } else { //euro dolar sterln
                                                            if ($arizafiyat->parabirimi_id == 1) {
                                                                $kur = 1 / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarihi);
                                                            } else {
                                                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $ucretlendirilen->kurtarihi);
                                                            }
                                                        }
                                                        $tutar=$arizafiyat->toplamtutar*$kur;
                                                        if ($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi_id) {
                                                            $tutar += $arizafiyat->toplamtutar2;
                                                        }else if($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi2_id){
                                                            $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                        }else{
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                        }
                                                        $ucretlendirilen->fiyat=round(($ucretlendirilen->fiyat - $tutar) * 2) / 2;
                                                    }else{
                                                        $ucretlendirilen->fiyat-=$arizafiyat->toplamtutar;
                                                        if($arizafiyat->parabirimi2_id!=null){
                                                            if($ucretlendirilen->parabirimi2_id!=$arizafiyat->parabirimi2_id){
                                                                DB::rollBack();
                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                            }else{
                                                                $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                            }
                                                        }
                                                    }
                                                    $ucretlendirilen->save();
                                                }
                                            }else{
                                                BackendController::HatirlatmaSil(4, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                            }
                                            $arizafiyat->fiyat = 0;
                                            $arizafiyat->fiyat2 = 0;
                                            $arizafiyat->tutar = 0;
                                            $arizafiyat->tutar2 = 0;
                                            $arizafiyat->kdv = 0;
                                            $arizafiyat->kdv2 = 0;
                                            $arizafiyat->toplamtutar = 0;
                                            $arizafiyat->toplamtutar2 = 0;
                                            $arizafiyat->parabirimi2_id = null;
                                            $arizafiyat->durum = 1;
                                            $arizafiyat->save();
                                        }
                                        $uretimyer=UretimYer::find($servistakip->uretimyer_id);
                                        $flag = 0;
                                        $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                            ->where('depodurum',0)->where('tipi',3)->where('periyodik',$kalibrasyongrup->periyodik)->where('subegonderim',0)->first();
                                        if ($depoteslim) {
                                            $secilenlist = explode(',', $depoteslim->secilenler);
                                            if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                $depoteslim->sayacsayisi += 1;
                                            }else{
                                                $flag = 1;
                                            }
                                        } else {
                                            $depoteslim = new DepoTeslim;
                                            $depoteslim->servis_id = $sayacgelen->servis_id;
                                            $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                            $depoteslim->secilenler = $sayacgelen->id;
                                            $depoteslim->sayacsayisi = 1;
                                            $depoteslim->depodurum = 0;
                                            $depoteslim->tipi = 3;
                                            $depoteslim->periyodik = $kalibrasyongrup->periyodik;
                                            $depoteslim->parabirimi_id=$uretimyer->parabirimi_id;
                                        }
                                        $depoteslim->save();
                                        $sayacgelen->kalibrasyon=1;
                                        $sayacgelen->sayacdurum=3;
                                        $sayacgelen->teslimdurum=3;
                                        $sayacgelen->beyanname=-2;
                                        $sayacgelen->save();
                                        $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                        $servistakip->durum = 8;
                                        $servistakip->kalibrasyontarihi = $kalibrasyon->kalibrasyontarih;
                                        $servistakip->sonislemtarihi = $kalibrasyon->kalibrasyontarih;
                                        $servistakip->save();
                                        $hurdakayit->depoteslim_id = $depoteslim->id;
                                        $hurdakayit->save();
                                        if(!$flag){
                                            BackendController::HatirlatmaGuncelle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                            BackendController::HatirlatmaEkle(9, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                            BackendController::BildirimEkle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                            BackendController::BildirimEkle(10, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Kalibrasyon yapılamayan sayaçlar için hurdaya ayırma bilgisi girişi yapılamadı', 'type' => 'error'));
                                    }
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Kalibrasyon yapılamayan sayaçlar için 2. kalibrasyon bilgisi girişi yapılamadı', 'type' => 'error'));
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                    Input::flash();
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => 'Kalibrasyon Bilgileri Kaydedilirken Hata Oluştu.', 'type' => 'error'));
                }
                try {
                    $kalibrasyongrup = KalibrasyonGrup::find($grupid);
                    $biten = ($basarili + $basarisiz);
                    if ($biten == ($kalibrasyongrup->adet - $kalibrasyongrup->biten)) {
                        $kalibrasyongrup->kalibrasyondurum = 1;
                    }
                    $kalibrasyongrup->biten += $biten;
                    $kalibrasyongrup->save();
                    BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-cog', $kalibrasyongrup->id . ' Numaralı Kalibrasyon Grubuna Ait Kalibrasyon Bilgisi Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Kalibrasyon Grup Numarası:' . $kalibrasyongrup->id);
                    DB::commit();
                    return Redirect::to('kalibrasyon/kalibrasyondetay/' . $id)->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Kaydedildi', 'text' => 'Kalibrasyon Bilgisi Başarıyla Kaydedildi', 'type' => 'success'));
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => 'Kalibrasyon Bilgileri Kaydedilirken Hata Oluştu.', 'type' => 'error'));
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getKalibrasyonbilgi() {
        try {
            $id=Input::get('id');
            $kalibrasyon = Kalibrasyon::find($id);
            $kalibrasyonstandart = KalibrasyonStandart::find($kalibrasyon->kalibrasyonstandart_id);
            $istasyon = Istasyon::find($kalibrasyon->istasyon_id);
            $kullanici = Kullanici::find($kalibrasyon->kullanici_id);
            $kalibrasyon->sayacadi = SayacAdi::find($kalibrasyon->sayacadi_id);
            $kalibrasyon->imalyili = date("Y", strtotime($kalibrasyon->imalyili));
            $kalibrasyon->kalibrasyontarih = date("d-m-Y H:i:s", strtotime($kalibrasyon->kalibrasyontarih));
            $kalibrasyon->kalibrasyonsayisi = $kalibrasyon->kalibrasyonsayisi . '. Kalibrasyon';
            $kalibrasyon->durum = $kalibrasyon->durum == 1 ? 'Başarılı' : 'Başarısız';
            $kalibrasyon->sonuc1 = number_format($kalibrasyon->sonuc1, 3,'.','');
            $kalibrasyon->sonuc2 = number_format($kalibrasyon->sonuc2, 3,'.','');
            $kalibrasyon->sonuc3 = number_format($kalibrasyon->sonuc3, 3,'.','');
            $kalibrasyon->sonuc4 = number_format($kalibrasyon->sonuc4, 3,'.','');
            $kalibrasyon->sonuc5 = number_format($kalibrasyon->sonuc5, 3,'.','');
            $kalibrasyon->sonuc6 = number_format($kalibrasyon->sonuc6, 3,'.','');
            $kalibrasyon->sonuc7 = number_format($kalibrasyon->sonuc7, 3,'.','');
            $kalibrasyon->hf2sonuc1 = is_null($kalibrasyon->hf2sonuc1) ? '' : number_format($kalibrasyon->hf2sonuc1, 3,'.','');
            $kalibrasyon->hf2sonuc2 = is_null($kalibrasyon->hf2sonuc2) ? '' : number_format($kalibrasyon->hf2sonuc2, 3,'.','');
            $kalibrasyon->hf2sonuc3 = is_null($kalibrasyon->hf2sonuc3) ? '' : number_format($kalibrasyon->hf2sonuc3, 3,'.','');
            $kalibrasyon->hf2sonuc4 = is_null($kalibrasyon->hf2sonuc4) ? '' : number_format($kalibrasyon->hf2sonuc4, 3,'.','');
            $kalibrasyon->hf2sonuc5 = is_null($kalibrasyon->hf2sonuc5) ? '' : number_format($kalibrasyon->hf2sonuc5, 3,'.','');
            $kalibrasyon->hf2sonuc6 = is_null($kalibrasyon->hf2sonuc6) ? '' : number_format($kalibrasyon->hf2sonuc6, 3,'.','');
            $kalibrasyon->hf2sonuc7 = is_null($kalibrasyon->hf2sonuc7) ? '' : number_format($kalibrasyon->hf2sonuc7, 3,'.','');
            $kalibrasyon->hf3sonuc1 = is_null($kalibrasyon->hf3sonuc1) ? '' : number_format($kalibrasyon->hf3sonuc1, 3,'.','');
            $kalibrasyon->hf3sonuc2 = is_null($kalibrasyon->hf3sonuc2) ? '' : number_format($kalibrasyon->hf3sonuc2, 3,'.','');
            $kalibrasyon->hf3sonuc3 = is_null($kalibrasyon->hf3sonuc3) ? '' : number_format($kalibrasyon->hf3sonuc3, 3,'.','');
            $kalibrasyon->hf3sonuc4 = is_null($kalibrasyon->hf3sonuc4) ? '' : number_format($kalibrasyon->hf3sonuc4, 3,'.','');
            $kalibrasyon->hf3sonuc5 = is_null($kalibrasyon->hf3sonuc5) ? '' : number_format($kalibrasyon->hf3sonuc5, 3,'.','');
            $kalibrasyon->hf3sonuc6 = is_null($kalibrasyon->hf3sonuc6) ? '' : number_format($kalibrasyon->hf3sonuc6, 3,'.','');
            $kalibrasyon->hf3sonuc7 = is_null($kalibrasyon->hf3sonuc7) ? '' : number_format($kalibrasyon->hf3sonuc7, 3,'.','');
            $kalibrasyon->hf32sonuc1 = is_null($kalibrasyon->hf32sonuc1) ? '' : number_format($kalibrasyon->hf32sonuc1, 3,'.','');
            $kalibrasyon->hf32sonuc2 = is_null($kalibrasyon->hf32sonuc2) ? '' : number_format($kalibrasyon->hf32sonuc2, 3,'.','');
            $kalibrasyon->hf32sonuc3 = is_null($kalibrasyon->hf32sonuc3) ? '' : number_format($kalibrasyon->hf32sonuc3, 3,'.','');
            $kalibrasyon->hf32sonuc4 = is_null($kalibrasyon->hf32sonuc4) ? '' : number_format($kalibrasyon->hf32sonuc4, 3,'.','');
            $kalibrasyon->hf32sonuc5 = is_null($kalibrasyon->hf32sonuc5) ? '' : number_format($kalibrasyon->hf32sonuc5, 3,'.','');
            $kalibrasyon->hf32sonuc6 = is_null($kalibrasyon->hf32sonuc6) ? '' : number_format($kalibrasyon->hf32sonuc6, 3,'.','');
            $kalibrasyon->hf32sonuc7 = is_null($kalibrasyon->hf32sonuc7) ? '' : number_format($kalibrasyon->hf32sonuc7, 3,'.','');
            return Response::json(array('durum' => true, 'kalibrasyon' => $kalibrasyon, 'kalibrasyonstandart' => $kalibrasyonstandart, 'istasyon' => $istasyon, 'kullanici' => $kullanici));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false,'title'=>'Kalibrasyon Bilgisi Getirilemedi','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error' ));
        }
    }

    public function getKayitduzenle($id) {
        $kalibrasyon=Kalibrasyon::find($id);
        $sayacgelen = SayacGelen::find($kalibrasyon->sayacgelen_id);
        if ($sayacgelen->teslimdurum==1) {
            if( (time()-strtotime($kalibrasyon->kalibrasyontarih))>86400)
                return Redirect::to('kalibrasyon/kalibrasyondetay/'.$kalibrasyon->kalibrasyongrup_id)->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Güncellenemez', 'text' => 'Sayaç Depo Teslimde Gözüküyor!', 'type' => 'error'));
        }
        $grup = KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
        $grup->netsiscari=NetsisCari::find($grup->netsiscari_id);
        if($grup->netsiscari->caridurum!="A")
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
        $kalibrasyon->istasyon=Istasyon::find($kalibrasyon->istasyon_id);
        $kalibrasyon->sayacadi=SayacAdi::find($kalibrasyon->sayacadi_id);
        $kalibrasyonstandart=KalibrasyonStandart::find($kalibrasyon->kalibrasyonstandart_id);
        $kalibrasyon->imalyili=date("Y", strtotime($kalibrasyon->imalyili));
        $kalibrasyon->kalibrasyontarih=date("d-m-Y h:i:s", strtotime($kalibrasyon->kalibrasyontarih));
        $kalibrasyon->kalibrasyonsayisi=$kalibrasyon->kalibrasyonsayisi.'. Kalibrasyon';
        $kalibrasyon->sonuc1=number_format($kalibrasyon->sonuc1,3,'.','');
        $kalibrasyon->sonuc2=number_format($kalibrasyon->sonuc2,3,'.','');
        $kalibrasyon->sonuc3=number_format($kalibrasyon->sonuc3,3,'.','');
        $kalibrasyon->sonuc4=number_format($kalibrasyon->sonuc4,3,'.','');
        $kalibrasyon->sonuc5=number_format($kalibrasyon->sonuc5,3,'.','');
        $kalibrasyon->sonuc6=number_format($kalibrasyon->sonuc6,3,'.','');
        $kalibrasyon->sonuc7=number_format($kalibrasyon->sonuc7,3,'.','');
        $kalibrasyon->hf2sonuc1=is_null($kalibrasyon->hf2sonuc1) ? '' : number_format($kalibrasyon->hf2sonuc1,3,'.','');
        $kalibrasyon->hf2sonuc2=is_null($kalibrasyon->hf2sonuc2) ? '' : number_format($kalibrasyon->hf2sonuc2,3,'.','');
        $kalibrasyon->hf2sonuc3=is_null($kalibrasyon->hf2sonuc3) ? '' : number_format($kalibrasyon->hf2sonuc3,3,'.','');
        $kalibrasyon->hf2sonuc4=is_null($kalibrasyon->hf2sonuc4) ? '' : number_format($kalibrasyon->hf2sonuc4,3,'.','');
        $kalibrasyon->hf2sonuc5=is_null($kalibrasyon->hf2sonuc5) ? '' : number_format($kalibrasyon->hf2sonuc5,3,'.','');
        $kalibrasyon->hf2sonuc6=is_null($kalibrasyon->hf2sonuc6) ? '' : number_format($kalibrasyon->hf2sonuc6,3,'.','');
        $kalibrasyon->hf2sonuc7=is_null($kalibrasyon->hf2sonuc7) ? '' : number_format($kalibrasyon->hf2sonuc7,3,'.','');
        $kalibrasyon->hf3sonuc1=is_null($kalibrasyon->hf3sonuc1) ? '' : number_format($kalibrasyon->hf3sonuc1,3,'.','');
        $kalibrasyon->hf3sonuc2=is_null($kalibrasyon->hf3sonuc2) ? '' : number_format($kalibrasyon->hf3sonuc2,3,'.','');
        $kalibrasyon->hf3sonuc3=is_null($kalibrasyon->hf3sonuc3) ? '' : number_format($kalibrasyon->hf3sonuc3,3,'.','');
        $kalibrasyon->hf3sonuc4=is_null($kalibrasyon->hf3sonuc4) ? '' : number_format($kalibrasyon->hf3sonuc4,3,'.','');
        $kalibrasyon->hf3sonuc5=is_null($kalibrasyon->hf3sonuc5) ? '' : number_format($kalibrasyon->hf3sonuc5,3,'.','');
        $kalibrasyon->hf3sonuc6=is_null($kalibrasyon->hf3sonuc6) ? '' : number_format($kalibrasyon->hf3sonuc6,3,'.','');
        $kalibrasyon->hf3sonuc7=is_null($kalibrasyon->hf3sonuc7) ? '' : number_format($kalibrasyon->hf3sonuc7,3,'.','');
        $kalibrasyon->hf32sonuc1=is_null($kalibrasyon->hf32sonuc1) ? '' : number_format($kalibrasyon->hf32sonuc1,3,'.','');
        $kalibrasyon->hf32sonuc2=is_null($kalibrasyon->hf32sonuc2) ? '' : number_format($kalibrasyon->hf32sonuc2,3,'.','');
        $kalibrasyon->hf32sonuc3=is_null($kalibrasyon->hf32sonuc3) ? '' : number_format($kalibrasyon->hf32sonuc3,3,'.','');
        $kalibrasyon->hf32sonuc4=is_null($kalibrasyon->hf32sonuc4) ? '' : number_format($kalibrasyon->hf32sonuc4,3,'.','');
        $kalibrasyon->hf32sonuc5=is_null($kalibrasyon->hf32sonuc5) ? '' : number_format($kalibrasyon->hf32sonuc5,3,'.','');
        $kalibrasyon->hf32sonuc6=is_null($kalibrasyon->hf32sonuc6) ? '' : number_format($kalibrasyon->hf32sonuc6,3,'.','');
        $kalibrasyon->hf32sonuc7=is_null($kalibrasyon->hf32sonuc7) ? '' : number_format($kalibrasyon->hf32sonuc7,3,'.','');
        if($kalibrasyonstandart->id==1){
            switch($kalibrasyon->sira) {
                case 1:  $kalibrasyon->fark1=0;   $kalibrasyon->fark2=0;        $kalibrasyon->fark3=0;        break;
                case 2:  $kalibrasyon->fark1=0.1; $kalibrasyon->fark2=0.05;     $kalibrasyon->fark3=0.03;     break;
                case 3:  $kalibrasyon->fark1=0.2; $kalibrasyon->fark2=0.10;     $kalibrasyon->fark3=0.06;     break;
                case 4:  $kalibrasyon->fark1=0.3; $kalibrasyon->fark2=0.15;     $kalibrasyon->fark3=0.09;     break;
                case 5:  $kalibrasyon->fark1=0.5; $kalibrasyon->fark2=0.20;     $kalibrasyon->fark3=0.12;     break;
                case 6:  $kalibrasyon->fark1=0.7; $kalibrasyon->fark2=0.25;     $kalibrasyon->fark3=0.15;     break;
                case 7:  $kalibrasyon->fark1=0.9; $kalibrasyon->fark2=0.30;     $kalibrasyon->fark3=0.18;     break;
                case 8:  $kalibrasyon->fark1=1.0; $kalibrasyon->fark2=0.35;     $kalibrasyon->fark3=0.21;     break;
                case 9:  $kalibrasyon->fark1=1.2; $kalibrasyon->fark2=0.40;     $kalibrasyon->fark3=0.24;     break;
                case 10: $kalibrasyon->fark1=1.3; $kalibrasyon->fark2=0.50;     $kalibrasyon->fark3=0.27;     break;
                case 11: $kalibrasyon->fark1=1.4; $kalibrasyon->fark2=0.55;     $kalibrasyon->fark3=0.30;     break;
                case 12: $kalibrasyon->fark1=1.5; $kalibrasyon->fark2=0.60;     $kalibrasyon->fark3=0.33;     break;
            }
        }
        return View::make('kalibrasyon.kayitduzenle',array('kalibrasyon'=>$kalibrasyon,'kalibrasyonstandart'=>$kalibrasyonstandart,'grup'=>$grup))->with(array('title'=>'Kalibrasyon Kayıt Ekranı'));
    }

    public function postKayitduzenle($id) {
        try {
            DB::beginTransaction();
            $kalibrasyon = Kalibrasyon::find($id);
            $sayacgelen = SayacGelen::find($kalibrasyon->sayacgelen_id);
            if ($sayacgelen->teslimdurum==1) {
                if( (time()-strtotime($kalibrasyon->kalibrasyontarih))>86400)
                    return Redirect::to('kalibrasyon/kalibrasyondetay/'.$kalibrasyon->kalibrasyongrup_id)->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Güncellenemez', 'text' => 'Sayaç Depo Teslimde Gözüküyor!', 'type' => 'error'));
            }
            $eskikalibrasyondurum = $kalibrasyon->durum;
            $eskikalibrasyonsayi = $kalibrasyon->kalibrasyonsayisi;
            $grupid = $kalibrasyon->kalibrasyongrup_id;
            $kalibrasyongrup = KalibrasyonGrup::find($grupid);
            $sira = Input::get('sira');
            $noktasayi = Input::get('noktasayi');
            $sayi = 0;
            if ($noktasayi == 3) {
                $nokta1 = Input::get('nokta1');
                $nokta2 = Input::get('nokta2');
                $nokta3 = Input::get('nokta3');
                $sonuc = Input::get('sonuc');
                try {
                    $kalibrasyon->sira = $sira;
                    $kalibrasyon->nokta1sapma = $nokta1;
                    $kalibrasyon->sonuc1 = str_replace(',', '.', $sonuc[0][0]);
                    $kalibrasyon->nokta2sapma = $nokta2;
                    $kalibrasyon->sonuc2 = str_replace(',', '.', $sonuc[0][1]);
                    $kalibrasyon->nokta3sapma = $nokta3;
                    $kalibrasyon->sonuc3 = str_replace(',', '.', $sonuc[0][2]);
                    if (($kalibrasyon->sonuc1 > $kalibrasyon->nokta1sapma) || ($kalibrasyon->sonuc1 < -($kalibrasyon->nokta1sapma)) ||
                        ($kalibrasyon->sonuc2 > $kalibrasyon->nokta2sapma) || ($kalibrasyon->sonuc2 < -($kalibrasyon->nokta2sapma)) ||
                        ($kalibrasyon->sonuc3 > $kalibrasyon->nokta3sapma) || ($kalibrasyon->sonuc3 < -($kalibrasyon->nokta3sapma))
                    ) {
                        $kalibrasyon->durum = 2; //başarısız
                    } else {
                        $kalibrasyon->durum = 1;
                    }
                    $kalibrasyon->kullanici_id = Auth::user()->id;
                    $kalibrasyon->kalibrasyontarih = date('Y-m-d H:i:s');
                    $kalibrasyon->save();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => 'Kalibrasyon Bilgileri Kaydedilirken Hata Oluştu.', 'type' => 'error'));
                }

            } else {
                $nokta1 = Input::get('nokta1');
                $nokta2 = Input::get('nokta2');
                $nokta3 = Input::get('nokta3');
                $hf2 = 0;
                $hf3 = 0;
                $hf32 = 0;
                if (Input::has('hf2'))
                    $hf2 = 1;
                if (Input::has('hf3'))
                    $hf3 = 1;
                if (Input::has('hf32'))
                    $hf32 = 1;
                $sonuc1 = Input::get('sonuc1');
                $sonuc2 = Input::get('sonuc2');
                $sonuc3 = Input::get('sonuc3');
                $sonuc4 = Input::get('sonuc4');
                try {
                    $kalibrasyon->sira = $sira;
                    $flag = 0;
                    switch ($noktasayi) {
                        case 4:
                            $nokta4 = Input::get('nokta4');
                            $kalibrasyon->nokta1sapma = $nokta1;
                            $kalibrasyon->sonuc1 = str_replace(',', '.', $sonuc1[0][0]);
                            $kalibrasyon->nokta2sapma = $nokta2;
                            $kalibrasyon->sonuc2 = str_replace(',', '.', $sonuc1[0][1]);
                            $kalibrasyon->nokta3sapma = $nokta3;
                            $kalibrasyon->sonuc3 = str_replace(',', '.', $sonuc1[0][2]);
                            $kalibrasyon->nokta4sapma = $nokta4;
                            $kalibrasyon->sonuc4 = str_replace(',', '.', $sonuc1[0][3]);
                            if (($kalibrasyon->sonuc1 > $kalibrasyon->nokta1sapma) || ($kalibrasyon->sonuc1 < -($kalibrasyon->nokta1sapma)) ||
                                ($kalibrasyon->sonuc2 > $kalibrasyon->nokta2sapma) || ($kalibrasyon->sonuc2 < -($kalibrasyon->nokta2sapma)) ||
                                ($kalibrasyon->sonuc3 > $kalibrasyon->nokta3sapma) || ($kalibrasyon->sonuc3 < -($kalibrasyon->nokta3sapma)) ||
                                ($kalibrasyon->sonuc4 > $kalibrasyon->nokta4sapma) || ($kalibrasyon->sonuc4 < -($kalibrasyon->nokta4sapma))
                            ) {
                                $kalibrasyon->durum = 2; //başarısız
                                $flag = 1;
                            }
                            if ($hf2) {
                                $kalibrasyon->hf2 = 1;
                                $kalibrasyon->hf2nokta1sapma = $nokta1;
                                $kalibrasyon->hf2sonuc1 = str_replace(',', '.', $sonuc2[0][0]);
                                $kalibrasyon->hf2nokta2sapma = $nokta2;
                                $kalibrasyon->hf2sonuc2 = str_replace(',', '.', $sonuc2[0][1]);
                                $kalibrasyon->hf2nokta3sapma = $nokta3;
                                $kalibrasyon->hf2sonuc3 = str_replace(',', '.', $sonuc2[0][2]);
                                $kalibrasyon->hf2nokta4sapma = $nokta4;
                                $kalibrasyon->hf2sonuc4 = str_replace(',', '.', $sonuc2[0][3]);
                                if (!$flag) {
                                    if (($kalibrasyon->hf2sonuc1 > $kalibrasyon->hf2nokta1sapma) || ($kalibrasyon->hf2sonuc1 < -($kalibrasyon->hf2nokta1sapma)) ||
                                        ($kalibrasyon->hf2sonuc2 > $kalibrasyon->hf2nokta2sapma) || ($kalibrasyon->hf2sonuc2 < -($kalibrasyon->hf2nokta2sapma)) ||
                                        ($kalibrasyon->hf2sonuc3 > $kalibrasyon->hf2nokta3sapma) || ($kalibrasyon->hf2sonuc3 < -($kalibrasyon->hf2nokta3sapma)) ||
                                        ($kalibrasyon->hf2sonuc4 > $kalibrasyon->hf2nokta4sapma) || ($kalibrasyon->hf2sonuc4 < -($kalibrasyon->hf2nokta4sapma))
                                    ) {
                                        $kalibrasyon->durum = 2; //başarısız
                                        $flag = 1;
                                    }
                                }
                            }
                            if ($hf3) {
                                $kalibrasyon->hf3 = 1;
                                $kalibrasyon->hf3nokta1sapma = $nokta1;
                                $kalibrasyon->hf3sonuc1 = str_replace(',', '.', $sonuc3[0][0]);
                                $kalibrasyon->hf3nokta2sapma = $nokta2;
                                $kalibrasyon->hf3sonuc2 = str_replace(',', '.', $sonuc3[0][1]);
                                $kalibrasyon->hf3nokta3sapma = $nokta3;
                                $kalibrasyon->hf3sonuc3 = str_replace(',', '.', $sonuc3[0][2]);
                                $kalibrasyon->hf3nokta4sapma = $nokta4;
                                $kalibrasyon->hf3sonuc4 = str_replace(',', '.', $sonuc3[0][3]);
                                if (!$flag) {
                                    if (($kalibrasyon->hf3sonuc1 > $kalibrasyon->hf3nokta1sapma) || ($kalibrasyon->hf3sonuc1 < -($kalibrasyon->hf3nokta1sapma)) ||
                                        ($kalibrasyon->hf3sonuc2 > $kalibrasyon->hf3nokta2sapma) || ($kalibrasyon->hf3sonuc2 < -($kalibrasyon->hf3nokta2sapma)) ||
                                        ($kalibrasyon->hf3sonuc3 > $kalibrasyon->hf3nokta3sapma) || ($kalibrasyon->hf3sonuc3 < -($kalibrasyon->hf3nokta3sapma)) ||
                                        ($kalibrasyon->hf3sonuc4 > $kalibrasyon->hf3nokta4sapma) || ($kalibrasyon->hf3sonuc4 < -($kalibrasyon->hf3nokta4sapma))
                                    ) {
                                        $kalibrasyon->durum = 2; //başarısız
                                        $flag = 1;
                                    }
                                }
                            }
                            if ($hf32) {
                                $kalibrasyon->hf32 = 1;
                                $kalibrasyon->hf32nokta1sapma = $nokta1;
                                $kalibrasyon->hf32sonuc1 = str_replace(',', '.', $sonuc4[0][0]);
                                $kalibrasyon->hf32nokta2sapma = $nokta2;
                                $kalibrasyon->hf32sonuc2 = str_replace(',', '.', $sonuc4[0][1]);
                                $kalibrasyon->hf32nokta3sapma = $nokta3;
                                $kalibrasyon->hf32sonuc3 = str_replace(',', '.', $sonuc4[0][2]);
                                $kalibrasyon->hf32nokta4sapma = $nokta4;
                                $kalibrasyon->hf32sonuc4 = str_replace(',', '.', $sonuc4[0][3]);
                                if (!$flag) {
                                    if (($kalibrasyon->hf32sonuc1 > $kalibrasyon->hf32nokta1sapma) || ($kalibrasyon->hf32sonuc1 < -($kalibrasyon->hf32nokta1sapma)) ||
                                        ($kalibrasyon->hf32sonuc2 > $kalibrasyon->hf32nokta2sapma) || ($kalibrasyon->hf32sonuc2 < -($kalibrasyon->hf32nokta2sapma)) ||
                                        ($kalibrasyon->hf32sonuc3 > $kalibrasyon->hf32nokta3sapma) || ($kalibrasyon->hf32sonuc3 < -($kalibrasyon->hf32nokta3sapma)) ||
                                        ($kalibrasyon->hf32sonuc4 > $kalibrasyon->hf32nokta4sapma) || ($kalibrasyon->hf32sonuc4 < -($kalibrasyon->hf32nokta4sapma))
                                    ) {
                                        $kalibrasyon->durum = 2; //başarısız
                                        $flag = 1;
                                    }
                                }
                            }
                            break;
                        case 5:
                            $nokta4 = Input::get('nokta4');
                            $nokta5 = Input::get('nokta5');
                            $kalibrasyon->nokta1sapma = $nokta1;
                            $kalibrasyon->sonuc1 = str_replace(',', '.', $sonuc1[0][0]);
                            $kalibrasyon->nokta2sapma = $nokta2;
                            $kalibrasyon->sonuc2 = str_replace(',', '.', $sonuc1[0][1]);
                            $kalibrasyon->nokta3sapma = $nokta3;
                            $kalibrasyon->sonuc3 = str_replace(',', '.', $sonuc1[0][2]);
                            $kalibrasyon->nokta4sapma = $nokta4;
                            $kalibrasyon->sonuc4 = str_replace(',', '.', $sonuc1[0][3]);
                            $kalibrasyon->nokta5sapma = $nokta5;
                            $kalibrasyon->sonuc5 = str_replace(',', '.', $sonuc1[0][4]);
                            if (($kalibrasyon->sonuc1 > $kalibrasyon->nokta1sapma) || ($kalibrasyon->sonuc1 < -($kalibrasyon->nokta1sapma)) ||
                                ($kalibrasyon->sonuc2 > $kalibrasyon->nokta2sapma) || ($kalibrasyon->sonuc2 < -($kalibrasyon->nokta2sapma)) ||
                                ($kalibrasyon->sonuc3 > $kalibrasyon->nokta3sapma) || ($kalibrasyon->sonuc3 < -($kalibrasyon->nokta3sapma)) ||
                                ($kalibrasyon->sonuc4 > $kalibrasyon->nokta4sapma) || ($kalibrasyon->sonuc4 < -($kalibrasyon->nokta4sapma)) ||
                                ($kalibrasyon->sonuc5 > $kalibrasyon->nokta5sapma) || ($kalibrasyon->sonuc5 < -($kalibrasyon->nokta5sapma))
                            ) {
                                $kalibrasyon->durum = 2; //başarısız
                                $flag = 1;
                            }
                            if ($hf2) {
                                $kalibrasyon->hf2 = 1;
                                $kalibrasyon->hf2nokta1sapma = $nokta1;
                                $kalibrasyon->hf2sonuc1 = str_replace(',', '.', $sonuc2[0][0]);
                                $kalibrasyon->hf2nokta2sapma = $nokta2;
                                $kalibrasyon->hf2sonuc2 = str_replace(',', '.', $sonuc2[0][1]);
                                $kalibrasyon->hf2nokta3sapma = $nokta3;
                                $kalibrasyon->hf2sonuc3 = str_replace(',', '.', $sonuc2[0][2]);
                                $kalibrasyon->hf2nokta4sapma = $nokta4;
                                $kalibrasyon->hf2sonuc4 = str_replace(',', '.', $sonuc2[0][3]);
                                $kalibrasyon->hf2nokta5sapma = $nokta5;
                                $kalibrasyon->hf2sonuc5 = str_replace(',', '.', $sonuc2[0][4]);
                                if (!$flag) {
                                    if (($kalibrasyon->hf2sonuc1 > $kalibrasyon->hf2nokta1sapma) || ($kalibrasyon->hf2sonuc1 < -($kalibrasyon->hf2nokta1sapma)) ||
                                        ($kalibrasyon->hf2sonuc2 > $kalibrasyon->hf2nokta2sapma) || ($kalibrasyon->hf2sonuc2 < -($kalibrasyon->hf2nokta2sapma)) ||
                                        ($kalibrasyon->hf2sonuc3 > $kalibrasyon->hf2nokta3sapma) || ($kalibrasyon->hf2sonuc3 < -($kalibrasyon->hf2nokta3sapma)) ||
                                        ($kalibrasyon->hf2sonuc4 > $kalibrasyon->hf2nokta4sapma) || ($kalibrasyon->hf2sonuc4 < -($kalibrasyon->hf2nokta4sapma)) ||
                                        ($kalibrasyon->hf2sonuc5 > $kalibrasyon->hf2nokta5sapma) || ($kalibrasyon->hf2sonuc5 < -($kalibrasyon->hf2nokta5sapma))
                                    ) {
                                        $kalibrasyon->durum = 2; //başarısız
                                        $flag = 1;
                                    }
                                }
                            }
                            if ($hf3) {
                                $kalibrasyon->hf3 = 1;
                                $kalibrasyon->hf3nokta1sapma = $nokta1;
                                $kalibrasyon->hf3sonuc1 = str_replace(',', '.', $sonuc3[0][0]);
                                $kalibrasyon->hf3nokta2sapma = $nokta2;
                                $kalibrasyon->hf3sonuc2 = str_replace(',', '.', $sonuc3[0][1]);
                                $kalibrasyon->hf3nokta3sapma = $nokta3;
                                $kalibrasyon->hf3sonuc3 = str_replace(',', '.', $sonuc3[0][2]);
                                $kalibrasyon->hf3nokta4sapma = $nokta4;
                                $kalibrasyon->hf3sonuc4 = str_replace(',', '.', $sonuc3[0][3]);
                                $kalibrasyon->hf3nokta5sapma = $nokta5;
                                $kalibrasyon->hf3sonuc5 = str_replace(',', '.', $sonuc3[0][4]);
                                if (!$flag) {
                                    if (($kalibrasyon->hf3sonuc1 > $kalibrasyon->hf3nokta1sapma) || ($kalibrasyon->hf3sonuc1 < -($kalibrasyon->hf3nokta1sapma)) ||
                                        ($kalibrasyon->hf3sonuc2 > $kalibrasyon->hf3nokta2sapma) || ($kalibrasyon->hf3sonuc2 < -($kalibrasyon->hf3nokta2sapma)) ||
                                        ($kalibrasyon->hf3sonuc3 > $kalibrasyon->hf3nokta3sapma) || ($kalibrasyon->hf3sonuc3 < -($kalibrasyon->hf3nokta3sapma)) ||
                                        ($kalibrasyon->hf3sonuc4 > $kalibrasyon->hf3nokta4sapma) || ($kalibrasyon->hf3sonuc4 < -($kalibrasyon->hf3nokta4sapma)) ||
                                        ($kalibrasyon->hf3sonuc5 > $kalibrasyon->hf3nokta5sapma) || ($kalibrasyon->hf3sonuc5 < -($kalibrasyon->hf3nokta5sapma))
                                    ) {
                                        $kalibrasyon->durum = 2; //başarısız
                                        $flag = 1;
                                    }
                                }
                            }
                            if ($hf32) {
                                $kalibrasyon->hf32 = 1;
                                $kalibrasyon->hf32nokta1sapma = $nokta1;
                                $kalibrasyon->hf32sonuc1 = str_replace(',', '.', $sonuc4[0][0]);
                                $kalibrasyon->hf32nokta2sapma = $nokta2;
                                $kalibrasyon->hf32sonuc2 = str_replace(',', '.', $sonuc4[0][1]);
                                $kalibrasyon->hf32nokta3sapma = $nokta3;
                                $kalibrasyon->hf32sonuc3 = str_replace(',', '.', $sonuc4[0][2]);
                                $kalibrasyon->hf32nokta4sapma = $nokta4;
                                $kalibrasyon->hf32sonuc4 = str_replace(',', '.', $sonuc4[0][3]);
                                $kalibrasyon->hf32nokta5sapma = $nokta5;
                                $kalibrasyon->hf32sonuc5 = str_replace(',', '.', $sonuc4[0][4]);
                                if (!$flag) {
                                    if (($kalibrasyon->hf32sonuc1 > $kalibrasyon->hf32nokta1sapma) || ($kalibrasyon->hf32sonuc1 < -($kalibrasyon->hf32nokta1sapma)) ||
                                        ($kalibrasyon->hf32sonuc2 > $kalibrasyon->hf32nokta2sapma) || ($kalibrasyon->hf32sonuc2 < -($kalibrasyon->hf32nokta2sapma)) ||
                                        ($kalibrasyon->hf32sonuc3 > $kalibrasyon->hf32nokta3sapma) || ($kalibrasyon->hf32sonuc3 < -($kalibrasyon->hf32nokta3sapma)) ||
                                        ($kalibrasyon->hf32sonuc4 > $kalibrasyon->hf32nokta4sapma) || ($kalibrasyon->hf32sonuc4 < -($kalibrasyon->hf32nokta4sapma)) ||
                                        ($kalibrasyon->hf32sonuc5 > $kalibrasyon->hf32nokta5sapma) || ($kalibrasyon->hf32sonuc5 < -($kalibrasyon->hf32nokta5sapma))
                                    ) {
                                        $kalibrasyon->durum = 2; //başarısız
                                        $flag = 1;
                                    }
                                }
                            }
                            break;
                        case 6:
                            $nokta4 = Input::get('nokta4');
                            $nokta5 = Input::get('nokta5');
                            $nokta6 = Input::get('nokta6');
                            $kalibrasyon->nokta1sapma = $nokta1;
                            $kalibrasyon->sonuc1 = str_replace(',', '.', $sonuc1[0][0]);
                            $kalibrasyon->nokta2sapma = $nokta2;
                            $kalibrasyon->sonuc2 = str_replace(',', '.', $sonuc1[0][1]);
                            $kalibrasyon->nokta3sapma = $nokta3;
                            $kalibrasyon->sonuc3 = str_replace(',', '.', $sonuc1[0][2]);
                            $kalibrasyon->nokta4sapma = $nokta4;
                            $kalibrasyon->sonuc4 = str_replace(',', '.', $sonuc1[0][3]);
                            $kalibrasyon->nokta5sapma = $nokta5;
                            $kalibrasyon->sonuc5 = str_replace(',', '.', $sonuc1[0][4]);
                            $kalibrasyon->nokta6sapma = $nokta6;
                            $kalibrasyon->sonuc6 = str_replace(',', '.', $sonuc1[0][5]);
                            if (($kalibrasyon->sonuc1 > $kalibrasyon->nokta1sapma) || ($kalibrasyon->sonuc1 < -($kalibrasyon->nokta1sapma)) ||
                                ($kalibrasyon->sonuc2 > $kalibrasyon->nokta2sapma) || ($kalibrasyon->sonuc2 < -($kalibrasyon->nokta2sapma)) ||
                                ($kalibrasyon->sonuc3 > $kalibrasyon->nokta3sapma) || ($kalibrasyon->sonuc3 < -($kalibrasyon->nokta3sapma)) ||
                                ($kalibrasyon->sonuc4 > $kalibrasyon->nokta4sapma) || ($kalibrasyon->sonuc4 < -($kalibrasyon->nokta4sapma)) ||
                                ($kalibrasyon->sonuc5 > $kalibrasyon->nokta5sapma) || ($kalibrasyon->sonuc5 < -($kalibrasyon->nokta5sapma)) ||
                                ($kalibrasyon->sonuc6 > $kalibrasyon->nokta6sapma) || ($kalibrasyon->sonuc6 < -($kalibrasyon->nokta6sapma))
                            ) {
                                $kalibrasyon->durum = 2; //başarısız
                                $flag = 1;
                            }
                            if ($hf2) {
                                $kalibrasyon->hf2 = 1;
                                $kalibrasyon->hf2nokta1sapma = $nokta1;
                                $kalibrasyon->hf2sonuc1 = str_replace(',', '.', $sonuc2[0][0]);
                                $kalibrasyon->hf2nokta2sapma = $nokta2;
                                $kalibrasyon->hf2sonuc2 = str_replace(',', '.', $sonuc2[0][1]);
                                $kalibrasyon->hf2nokta3sapma = $nokta3;
                                $kalibrasyon->hf2sonuc3 = str_replace(',', '.', $sonuc2[0][2]);
                                $kalibrasyon->hf2nokta4sapma = $nokta4;
                                $kalibrasyon->hf2sonuc4 = str_replace(',', '.', $sonuc2[0][3]);
                                $kalibrasyon->hf2nokta5sapma = $nokta5;
                                $kalibrasyon->hf2sonuc5 = str_replace(',', '.', $sonuc2[0][4]);
                                $kalibrasyon->hf2nokta6sapma = $nokta6;
                                $kalibrasyon->hf2sonuc6 = str_replace(',', '.', $sonuc2[0][5]);
                                if (!$flag) {
                                    if (($kalibrasyon->hf2sonuc1 > $kalibrasyon->hf2nokta1sapma) || ($kalibrasyon->hf2sonuc1 < -($kalibrasyon->hf2nokta1sapma)) ||
                                        ($kalibrasyon->hf2sonuc2 > $kalibrasyon->hf2nokta2sapma) || ($kalibrasyon->hf2sonuc2 < -($kalibrasyon->hf2nokta2sapma)) ||
                                        ($kalibrasyon->hf2sonuc3 > $kalibrasyon->hf2nokta3sapma) || ($kalibrasyon->hf2sonuc3 < -($kalibrasyon->hf2nokta3sapma)) ||
                                        ($kalibrasyon->hf2sonuc4 > $kalibrasyon->hf2nokta4sapma) || ($kalibrasyon->hf2sonuc4 < -($kalibrasyon->hf2nokta4sapma)) ||
                                        ($kalibrasyon->hf2sonuc5 > $kalibrasyon->hf2nokta5sapma) || ($kalibrasyon->hf2sonuc5 < -($kalibrasyon->hf2nokta5sapma)) ||
                                        ($kalibrasyon->hf2sonuc6 > $kalibrasyon->hf2nokta6sapma) || ($kalibrasyon->hf2sonuc6 < -($kalibrasyon->hf2nokta6sapma))
                                    ) {
                                        $kalibrasyon->durum = 2; //başarısız
                                        $flag = 1;
                                    }
                                }
                            }
                            if ($hf3) {
                                $kalibrasyon->hf3 = 1;
                                $kalibrasyon->hf3nokta1sapma = $nokta1;
                                $kalibrasyon->hf3sonuc1 = str_replace(',', '.', $sonuc3[0][0]);
                                $kalibrasyon->hf3nokta2sapma = $nokta2;
                                $kalibrasyon->hf3sonuc2 = str_replace(',', '.', $sonuc3[0][1]);
                                $kalibrasyon->hf3nokta3sapma = $nokta3;
                                $kalibrasyon->hf3sonuc3 = str_replace(',', '.', $sonuc3[0][2]);
                                $kalibrasyon->hf3nokta4sapma = $nokta4;
                                $kalibrasyon->hf3sonuc4 = str_replace(',', '.', $sonuc3[0][3]);
                                $kalibrasyon->hf3nokta5sapma = $nokta5;
                                $kalibrasyon->hf3sonuc5 = str_replace(',', '.', $sonuc3[0][4]);
                                $kalibrasyon->hf3nokta6sapma = $nokta6;
                                $kalibrasyon->hf3sonuc6 = str_replace(',', '.', $sonuc3[0][5]);
                                if (!$flag) {
                                    if (($kalibrasyon->hf3sonuc1 > $kalibrasyon->hf3nokta1sapma) || ($kalibrasyon->hf3sonuc1 < -($kalibrasyon->hf3nokta1sapma)) ||
                                        ($kalibrasyon->hf3sonuc2 > $kalibrasyon->hf3nokta2sapma) || ($kalibrasyon->hf3sonuc2 < -($kalibrasyon->hf3nokta2sapma)) ||
                                        ($kalibrasyon->hf3sonuc3 > $kalibrasyon->hf3nokta3sapma) || ($kalibrasyon->hf3sonuc3 < -($kalibrasyon->hf3nokta3sapma)) ||
                                        ($kalibrasyon->hf3sonuc4 > $kalibrasyon->hf3nokta4sapma) || ($kalibrasyon->hf3sonuc4 < -($kalibrasyon->hf3nokta4sapma)) ||
                                        ($kalibrasyon->hf3sonuc5 > $kalibrasyon->hf3nokta5sapma) || ($kalibrasyon->hf3sonuc5 < -($kalibrasyon->hf3nokta5sapma)) ||
                                        ($kalibrasyon->hf3sonuc6 > $kalibrasyon->hf3nokta6sapma) || ($kalibrasyon->hf3sonuc6 < -($kalibrasyon->hf3nokta6sapma))
                                    ) {
                                        $kalibrasyon->durum = 2; //başarısız
                                        $flag = 1;
                                    }
                                }
                            }
                            if ($hf32) {
                                $kalibrasyon->hf32 = 1;
                                $kalibrasyon->hf32nokta1sapma = $nokta1;
                                $kalibrasyon->hf32sonuc1 = str_replace(',', '.', $sonuc4[0][0]);
                                $kalibrasyon->hf32nokta2sapma = $nokta2;
                                $kalibrasyon->hf32sonuc2 = str_replace(',', '.', $sonuc4[0][1]);
                                $kalibrasyon->hf32nokta3sapma = $nokta3;
                                $kalibrasyon->hf32sonuc3 = str_replace(',', '.', $sonuc4[0][2]);
                                $kalibrasyon->hf32nokta4sapma = $nokta4;
                                $kalibrasyon->hf32sonuc4 = str_replace(',', '.', $sonuc4[0][3]);
                                $kalibrasyon->hf32nokta5sapma = $nokta5;
                                $kalibrasyon->hf32sonuc5 = str_replace(',', '.', $sonuc4[0][4]);
                                $kalibrasyon->hf32nokta6sapma = $nokta6;
                                $kalibrasyon->hf32sonuc6 = str_replace(',', '.', $sonuc4[0][5]);
                                if (!$flag) {
                                    if (($kalibrasyon->hf32sonuc1 > $kalibrasyon->hf32nokta1sapma) || ($kalibrasyon->hf32sonuc1 < -($kalibrasyon->hf32nokta1sapma)) ||
                                        ($kalibrasyon->hf32sonuc2 > $kalibrasyon->hf32nokta2sapma) || ($kalibrasyon->hf32sonuc2 < -($kalibrasyon->hf32nokta2sapma)) ||
                                        ($kalibrasyon->hf32sonuc3 > $kalibrasyon->hf32nokta3sapma) || ($kalibrasyon->hf32sonuc3 < -($kalibrasyon->hf32nokta3sapma)) ||
                                        ($kalibrasyon->hf32sonuc4 > $kalibrasyon->hf32nokta4sapma) || ($kalibrasyon->hf32sonuc4 < -($kalibrasyon->hf32nokta4sapma)) ||
                                        ($kalibrasyon->hf32sonuc5 > $kalibrasyon->hf32nokta5sapma) || ($kalibrasyon->hf32sonuc5 < -($kalibrasyon->hf32nokta5sapma)) ||
                                        ($kalibrasyon->hf32sonuc6 > $kalibrasyon->hf32nokta6sapma) || ($kalibrasyon->hf32sonuc6 < -($kalibrasyon->hf32nokta6sapma))
                                    ) {
                                        $kalibrasyon->durum = 2; //başarısız
                                        $flag = 1;
                                    }
                                }
                            }
                            break;
                        case 7:
                            $nokta4 = Input::get('nokta4');
                            $nokta5 = Input::get('nokta5');
                            $nokta6 = Input::get('nokta6');
                            $nokta7 = Input::get('nokta7');
                            $kalibrasyon->nokta1sapma = $nokta1;
                            $kalibrasyon->sonuc1 = str_replace(',', '.', $sonuc1[0][0]);
                            $kalibrasyon->nokta2sapma = $nokta2;
                            $kalibrasyon->sonuc2 = str_replace(',', '.', $sonuc1[0][1]);
                            $kalibrasyon->nokta3sapma = $nokta3;
                            $kalibrasyon->sonuc3 = str_replace(',', '.', $sonuc1[0][2]);
                            $kalibrasyon->nokta4sapma = $nokta4;
                            $kalibrasyon->sonuc4 = str_replace(',', '.', $sonuc1[0][3]);
                            $kalibrasyon->nokta5sapma = $nokta5;
                            $kalibrasyon->sonuc5 = str_replace(',', '.', $sonuc1[0][4]);
                            $kalibrasyon->nokta6sapma = $nokta6;
                            $kalibrasyon->sonuc6 = str_replace(',', '.', $sonuc1[0][5]);
                            $kalibrasyon->nokta7sapma = $nokta7;
                            $kalibrasyon->sonuc7 = str_replace(',', '.', $sonuc1[0][6]);
                            if (($kalibrasyon->sonuc1 > $kalibrasyon->nokta1sapma) || ($kalibrasyon->sonuc1 < -($kalibrasyon->nokta1sapma)) ||
                                ($kalibrasyon->sonuc2 > $kalibrasyon->nokta2sapma) || ($kalibrasyon->sonuc2 < -($kalibrasyon->nokta2sapma)) ||
                                ($kalibrasyon->sonuc3 > $kalibrasyon->nokta3sapma) || ($kalibrasyon->sonuc3 < -($kalibrasyon->nokta3sapma)) ||
                                ($kalibrasyon->sonuc4 > $kalibrasyon->nokta4sapma) || ($kalibrasyon->sonuc4 < -($kalibrasyon->nokta4sapma)) ||
                                ($kalibrasyon->sonuc5 > $kalibrasyon->nokta5sapma) || ($kalibrasyon->sonuc5 < -($kalibrasyon->nokta5sapma)) ||
                                ($kalibrasyon->sonuc6 > $kalibrasyon->nokta6sapma) || ($kalibrasyon->sonuc6 < -($kalibrasyon->nokta6sapma)) ||
                                ($kalibrasyon->sonuc7 > $kalibrasyon->nokta7sapma) || ($kalibrasyon->sonuc7 < -($kalibrasyon->nokta7sapma))
                            ) {
                                $kalibrasyon->durum = 2; //başarısız
                                $flag = 1;
                            }
                            if ($hf2) {
                                $kalibrasyon->hf2 = 1;
                                $kalibrasyon->hf2nokta1sapma = $nokta1;
                                $kalibrasyon->hf2sonuc1 = str_replace(',', '.', $sonuc2[0][0]);
                                $kalibrasyon->hf2nokta2sapma = $nokta2;
                                $kalibrasyon->hf2sonuc2 = str_replace(',', '.', $sonuc2[0][1]);
                                $kalibrasyon->hf2nokta3sapma = $nokta3;
                                $kalibrasyon->hf2sonuc3 = str_replace(',', '.', $sonuc2[0][2]);
                                $kalibrasyon->hf2nokta4sapma = $nokta4;
                                $kalibrasyon->hf2sonuc4 = str_replace(',', '.', $sonuc2[0][3]);
                                $kalibrasyon->hf2nokta5sapma = $nokta5;
                                $kalibrasyon->hf2sonuc5 = str_replace(',', '.', $sonuc2[0][4]);
                                $kalibrasyon->hf2nokta6sapma = $nokta6;
                                $kalibrasyon->hf2sonuc6 = str_replace(',', '.', $sonuc2[0][5]);
                                $kalibrasyon->hf2nokta7sapma = $nokta7;
                                $kalibrasyon->hf2sonuc7 = str_replace(',', '.', $sonuc2[0][6]);
                                if (!$flag) {
                                    if (($kalibrasyon->hf2sonuc1 > $kalibrasyon->hf2nokta1sapma) || ($kalibrasyon->hf2sonuc1 < -($kalibrasyon->hf2nokta1sapma)) ||
                                        ($kalibrasyon->hf2sonuc2 > $kalibrasyon->hf2nokta2sapma) || ($kalibrasyon->hf2sonuc2 < -($kalibrasyon->hf2nokta2sapma)) ||
                                        ($kalibrasyon->hf2sonuc3 > $kalibrasyon->hf2nokta3sapma) || ($kalibrasyon->hf2sonuc3 < -($kalibrasyon->hf2nokta3sapma)) ||
                                        ($kalibrasyon->hf2sonuc4 > $kalibrasyon->hf2nokta4sapma) || ($kalibrasyon->hf2sonuc4 < -($kalibrasyon->hf2nokta4sapma)) ||
                                        ($kalibrasyon->hf2sonuc5 > $kalibrasyon->hf2nokta5sapma) || ($kalibrasyon->hf2sonuc5 < -($kalibrasyon->hf2nokta5sapma)) ||
                                        ($kalibrasyon->hf2sonuc6 > $kalibrasyon->hf2nokta6sapma) || ($kalibrasyon->hf2sonuc6 < -($kalibrasyon->hf2nokta6sapma)) ||
                                        ($kalibrasyon->hf2sonuc7 > $kalibrasyon->hf2nokta7sapma) || ($kalibrasyon->hf2sonuc7 < -($kalibrasyon->hf2nokta7sapma))
                                    ) {
                                        $kalibrasyon->durum = 2; //başarısız
                                        $flag = 1;
                                    }
                                }
                            }
                            if ($hf3) {
                                $kalibrasyon->hf3 = 1;
                                $kalibrasyon->hf3nokta1sapma = $nokta1;
                                $kalibrasyon->hf3sonuc1 = str_replace(',', '.', $sonuc3[0][0]);
                                $kalibrasyon->hf3nokta2sapma = $nokta2;
                                $kalibrasyon->hf3sonuc2 = str_replace(',', '.', $sonuc3[0][1]);
                                $kalibrasyon->hf3nokta3sapma = $nokta3;
                                $kalibrasyon->hf3sonuc3 = str_replace(',', '.', $sonuc3[0][2]);
                                $kalibrasyon->hf3nokta4sapma = $nokta4;
                                $kalibrasyon->hf3sonuc4 = str_replace(',', '.', $sonuc3[0][3]);
                                $kalibrasyon->hf3nokta5sapma = $nokta5;
                                $kalibrasyon->hf3sonuc5 = str_replace(',', '.', $sonuc3[0][4]);
                                $kalibrasyon->hf3nokta6sapma = $nokta6;
                                $kalibrasyon->hf3sonuc6 = str_replace(',', '.', $sonuc3[0][5]);
                                $kalibrasyon->hf3nokta7sapma = $nokta7;
                                $kalibrasyon->hf3sonuc7 = str_replace(',', '.', $sonuc3[0][6]);
                                if (!$flag) {
                                    if (($kalibrasyon->hf3sonuc1 > $kalibrasyon->hf3nokta1sapma) || ($kalibrasyon->hf3sonuc1 < -($kalibrasyon->hf3nokta1sapma)) ||
                                        ($kalibrasyon->hf3sonuc2 > $kalibrasyon->hf3nokta2sapma) || ($kalibrasyon->hf3sonuc2 < -($kalibrasyon->hf3nokta2sapma)) ||
                                        ($kalibrasyon->hf3sonuc3 > $kalibrasyon->hf3nokta3sapma) || ($kalibrasyon->hf3sonuc3 < -($kalibrasyon->hf3nokta3sapma)) ||
                                        ($kalibrasyon->hf3sonuc4 > $kalibrasyon->hf3nokta4sapma) || ($kalibrasyon->hf3sonuc4 < -($kalibrasyon->hf3nokta4sapma)) ||
                                        ($kalibrasyon->hf3sonuc5 > $kalibrasyon->hf3nokta5sapma) || ($kalibrasyon->hf3sonuc5 < -($kalibrasyon->hf3nokta5sapma)) ||
                                        ($kalibrasyon->hf3sonuc6 > $kalibrasyon->hf3nokta6sapma) || ($kalibrasyon->hf3sonuc6 < -($kalibrasyon->hf3nokta6sapma)) ||
                                        ($kalibrasyon->hf3sonuc7 > $kalibrasyon->hf3nokta7sapma) || ($kalibrasyon->hf3sonuc7 < -($kalibrasyon->hf3nokta7sapma))
                                    ) {
                                        $kalibrasyon->durum = 2; //başarısız
                                        $flag = 1;
                                    }
                                }
                            }
                            if ($hf32) {
                                $kalibrasyon->hf32 = 1;
                                $kalibrasyon->hf32nokta1sapma = $nokta1;
                                $kalibrasyon->hf32sonuc1 = str_replace(',', '.', $sonuc4[0][0]);
                                $kalibrasyon->hf32nokta2sapma = $nokta2;
                                $kalibrasyon->hf32sonuc2 = str_replace(',', '.', $sonuc4[0][1]);
                                $kalibrasyon->hf32nokta3sapma = $nokta3;
                                $kalibrasyon->hf32sonuc3 = str_replace(',', '.', $sonuc4[0][2]);
                                $kalibrasyon->hf32nokta4sapma = $nokta4;
                                $kalibrasyon->hf32sonuc4 = str_replace(',', '.', $sonuc4[0][3]);
                                $kalibrasyon->hf32nokta5sapma = $nokta5;
                                $kalibrasyon->hf32sonuc5 = str_replace(',', '.', $sonuc4[0][4]);
                                $kalibrasyon->hf32nokta6sapma = $nokta6;
                                $kalibrasyon->hf32sonuc6 = str_replace(',', '.', $sonuc4[0][5]);
                                $kalibrasyon->hf32nokta7sapma = $nokta7;
                                $kalibrasyon->hf32sonuc7 = str_replace(',', '.', $sonuc4[0][6]);
                                if (!$flag) {
                                    if (($kalibrasyon->hf32sonuc1 > $kalibrasyon->hf32nokta1sapma) || ($kalibrasyon->hf32sonuc1 < -($kalibrasyon->hf32nokta1sapma)) ||
                                        ($kalibrasyon->hf32sonuc2 > $kalibrasyon->hf32nokta2sapma) || ($kalibrasyon->hf32sonuc2 < -($kalibrasyon->hf32nokta2sapma)) ||
                                        ($kalibrasyon->hf32sonuc3 > $kalibrasyon->hf32nokta3sapma) || ($kalibrasyon->hf32sonuc3 < -($kalibrasyon->hf32nokta3sapma)) ||
                                        ($kalibrasyon->hf32sonuc4 > $kalibrasyon->hf32nokta4sapma) || ($kalibrasyon->hf32sonuc4 < -($kalibrasyon->hf32nokta4sapma)) ||
                                        ($kalibrasyon->hf32sonuc5 > $kalibrasyon->hf32nokta5sapma) || ($kalibrasyon->hf32sonuc5 < -($kalibrasyon->hf32nokta5sapma)) ||
                                        ($kalibrasyon->hf32sonuc6 > $kalibrasyon->hf32nokta6sapma) || ($kalibrasyon->hf32sonuc6 < -($kalibrasyon->hf32nokta6sapma)) ||
                                        ($kalibrasyon->hf32sonuc7 > $kalibrasyon->hf32nokta7sapma) || ($kalibrasyon->hf32sonuc7 < -($kalibrasyon->hf32nokta7sapma))
                                    ) {
                                        $kalibrasyon->durum = 2; //başarısız
                                        $flag = 1;
                                    }
                                }
                            }
                            break;
                    }
                    if ($flag == 0) {
                        $kalibrasyon->durum = 1;
                    }
                    $kalibrasyon->kullanici_id = Auth::user()->id;
                    $kalibrasyon->kalibrasyontarih = date('Y-m-d H:i:s');
                    $kalibrasyon->save();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => 'Kalibrasyon Bilgileri Kaydedilirken Hata Oluştu.', 'type' => 'error'));
                }
            }
            try {
                $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                $uretimyer=UretimYer::find($servistakip->uretimyer_id);
                if ($eskikalibrasyondurum == 2) //başarısız olan güncellendiyse
                {
                    if ($eskikalibrasyonsayi == 1) //1. kalibrasyon
                    {
                        if ($kalibrasyon->durum == 1) { //kalibrasyon geçtiyse depo teslime aktarılacak 2.kalibrasyon silinecek
                            try {
                                $silinecek = Kalibrasyon::where('sayacgelen_id', $kalibrasyon->sayacgelen_id)->where('kalibrasyonsayisi', 2)->first();
                                if ($silinecek->durum == 2) { //2. kalibrasyon da geçmemişse hurda silinecek
                                    $hurda = Hurda::where('sayacgelen_id', $sayacgelen->id)->where('kalibrasyon_id', $kalibrasyon->id)->first();
                                    $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                        ->where('depodurum',0)->where('tipi',3)->where('periyodik',$kalibrasyongrup->periyodik)->where('subegonderim',0)->first();
                                    $hurdanedeni= HurdaNedeni::find($hurda->hurdanedeni_id);
                                    $hurdanedeni->kullanim-=($hurdanedeni->kullanim==0 ? 0 : 1);
                                    $hurdanedeni->save();
                                    $hurda->delete();
                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                    {
                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                        $yenilist = "";
                                        foreach ($secilenlist as $secilen) {
                                            if ($secilen != $kalibrasyon->sayacgelen_id) {
                                                $yenilist .= ($yenilist == "" ? "" : ",") . $secilen;
                                            }
                                        }
                                        if ($yenilist == "") {
                                            $depoteslim->delete();
                                        } else {
                                            $depoteslim->secilenler = $yenilist;
                                            $depoteslim->sayacsayisi -= 1;
                                            $depoteslim->save();
                                        }
                                    }
                                    try {
                                        if($sayacgelen->musterionay || $kalibrasyongrup->periyodik) {
                                            $sayacgelen->teslimdurum=1;
                                            $sayacgelen->save();
                                            if($kalibrasyongrup->periyodik){
                                                $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                    ->where('depodurum', 0)->where('tipi', 0)->where('periyodik', $kalibrasyongrup->periyodik)->where('subegonderim',0)->first();
                                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                {
                                                    $depoteslim->secilenler .= ',' . $kalibrasyon->sayacgelen_id;
                                                    $depoteslim->sayacsayisi += 1;
                                                    $depoteslim->save();
                                                } else { //yeni depo teslimatı yapılacak
                                                    $depoteslim = new DepoTeslim;
                                                    $depoteslim->servis_id = $sayacgelen->servis_id;
                                                    $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                    $depoteslim->secilenler = $sayacgelen->id;
                                                    $depoteslim->sayacsayisi = 1;
                                                    $depoteslim->depodurum = 0;
                                                    $depoteslim->periyodik = $kalibrasyongrup->periyodik;
                                                    $depoteslim->parabirimi_id=$uretimyer->parabirimi_id;
                                                    $depoteslim->save();
                                                }
                                            }else{
                                                $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                                                $birim = $arizafiyat->parabirimi_id;
                                                $birim2 = $arizafiyat->parabirimi2_id;
                                                $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                                if($arizakayit->arizakayit_durum==7){
                                                    $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                        ->where('depodurum', 0)->where('tipi', 2)->where('periyodik',0)->where('subegonderim', 0)->first();
                                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                    {
                                                        $depoteslim->secilenler .= ',' . $kalibrasyon->sayacgelen_id;
                                                        $depoteslim->sayacsayisi += 1;
                                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id==null ? $birim2 : $depoteslim->parabirimi2_id;
                                                        $depoteslim->save();
                                                    } else { //yeni depo teslimatı yapılacak
                                                        $depoteslim = new DepoTeslim;
                                                        $depoteslim->servis_id = $sayacgelen->servis_id;
                                                        $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                        $depoteslim->secilenler = $sayacgelen->id;
                                                        $depoteslim->sayacsayisi = 1;
                                                        $depoteslim->depodurum = 0;
                                                        $depoteslim->tipi = 2;
                                                        $depoteslim->parabirimi_id=$birim;
                                                        $depoteslim->parabirimi2_id=$birim2;
                                                        $depoteslim->save();
                                                    }
                                                }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0){
                                                    $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                        ->where('depodurum', 0)->where('tipi', 0)->where('periyodik',0)->where('subegonderim', 0)->first();
                                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                    {
                                                        $depoteslim->secilenler .= ',' . $kalibrasyon->sayacgelen_id;
                                                        $depoteslim->sayacsayisi += 1;
                                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id==null ? $birim2 : $depoteslim->parabirimi2_id;
                                                        $depoteslim->save();
                                                    } else { //yeni depo teslimatı yapılacak
                                                        $depoteslim = new DepoTeslim;
                                                        $depoteslim->servis_id = $sayacgelen->servis_id;
                                                        $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                        $depoteslim->secilenler = $sayacgelen->id;
                                                        $depoteslim->sayacsayisi = 1;
                                                        $depoteslim->depodurum = 0;
                                                        $depoteslim->parabirimi_id=$birim;
                                                        $depoteslim->parabirimi2_id=$birim2;
                                                        $depoteslim->save();
                                                    }
                                                }else{
                                                    $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                        ->where('depodurum', 0)->where('tipi',1)->where('periyodik', 0)->where('subegonderim', 0)->first();
                                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                    {
                                                        $depoteslim->secilenler .= ',' . $kalibrasyon->sayacgelen_id;
                                                        $depoteslim->sayacsayisi += 1;
                                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id==null ? $birim2 : $depoteslim->parabirimi2_id;
                                                        $depoteslim->save();
                                                    } else { //yeni depo teslimatı yapılacak
                                                        $depoteslim = new DepoTeslim;
                                                        $depoteslim->servis_id = $sayacgelen->servis_id;
                                                        $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                        $depoteslim->secilenler = $sayacgelen->id;
                                                        $depoteslim->sayacsayisi = 1;
                                                        $depoteslim->depodurum = 0;
                                                        $depoteslim->tipi = 1;
                                                        $depoteslim->parabirimi_id=$birim;
                                                        $depoteslim->parabirimi2_id=$birim2;
                                                        $depoteslim->save();
                                                    }
                                                }
                                            }
                                        }else {
                                            BackendController::HatirlatmaSil(9, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                        }
                                        if(!$kalibrasyongrup->periyodik){
                                            $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                                            $uretimyer = UretimYer::find($arizafiyat->uretimyer_id);
                                            $parabirimi = null;
                                            $parabirimi2 = null;
                                            $fiyat=0;
                                            $fiyat2=0;
                                            $indirim=0;
                                            $indirim2=0;
                                            $ucretsizler=explode(',',$arizafiyat->ucretsiz);
                                            $genelfiyatlar=explode(';',$arizafiyat->genel);
                                            $ozelfiyatlar=explode(';',$arizafiyat->ozel);
                                            $genelbirimler=explode(',',$arizafiyat->genelbirim);
                                            $ozelbirimler=explode(',',$arizafiyat->ozelbirim);
                                            $genelbirimlist=array();
                                            $ozelbirimlist=array();
                                            foreach($genelbirimler as $birimid){
                                                $parabirimi=ParaBirimi::find($birimid);
                                                array_push($genelbirimlist,$parabirimi);
                                            }
                                            foreach($ozelbirimler as $birimid){
                                                $parabirimi=ParaBirimi::find($birimid);
                                                array_push($ozelbirimlist,$parabirimi);
                                            }
                                            $genelbirim = BackendController::getGenelParabirimi();
                                            $ozelbirim = ParaBirimi::find($uretimyer->parabirimi_id);
                                            $kur = 1;
                                            if($genelbirim->id!=$ozelbirim->id){
                                                if ($ozelbirim->id == 1) { //tl
                                                    $kur = BackendController::getKurBilgisi($genelbirim->id, $arizafiyat->kurtarihi);
                                                } else { //euro dolar sterln
                                                    if ($genelbirim->id == 1) {
                                                        $kur = 1 / BackendController::getKurBilgisi($ozelbirim->id, $arizafiyat->kurtarihi);
                                                    } else {
                                                        $kur = BackendController::getKurBilgisi($genelbirim->id, $arizafiyat->kurtarihi) / BackendController::getKurBilgisi($ozelbirim->id, $arizafiyat->kurtarihi);
                                                    }
                                                }
                                            }
                                            if($arizafiyat->garanti) {
                                                $fiyat = 0;
                                                $fiyat2 = 0;
                                            }else if ($arizafiyat->fiyatdurum) { //genel fiyatlar
                                                $parabirimi = $genelbirim;
                                                $parabirimi2 = null;
                                                $index = 0;
                                                foreach($genelfiyatlar as $genelfiyat){
                                                    if($ucretsizler[$index]!='1'){
                                                        if($parabirimi->id==$genelbirimler[$index]){
                                                            $fiyat +=floatval($genelfiyat);
                                                        }else if($parabirimi2==null || $parabirimi2->id==$genelbirimler[$index]){
                                                            $fiyat2 += floatval($genelfiyat);
                                                            $parabirimi2 = $genelbirimler[$index];
                                                        }else{
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                        }
                                                    }
                                                }
                                                $fiyat*=$kur;
                                                if($parabirimi2->id==$ozelbirim->id){
                                                    $fiyat+=$fiyat2;
                                                    $fiyat2=0;
                                                    $parabirimi2=null;
                                                }
                                            } else { //ozel fiyatlar
                                                $parabirimi = $ozelbirim;
                                                $parabirimi2 = null;
                                                $index = 0;
                                                foreach($ozelfiyatlar as $ozelfiyat){
                                                    if($ucretsizler[$index]!='1'){
                                                        if($parabirimi->id==$ozelbirimler[$index]){
                                                            $fiyat +=floatval($ozelfiyat);
                                                        }else if($parabirimi2==null || $parabirimi2->id==$ozelbirimler[$index]){
                                                            $fiyat2 += floatval($ozelfiyat);
                                                            $parabirimi2 = $ozelbirimler[$index];
                                                        }else{
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                        }
                                                    }
                                                }
                                                if($parabirimi2->id==$ozelbirim->id){
                                                    $fiyat+=$fiyat2;
                                                    $fiyat2=0;
                                                    $parabirimi2=null;
                                                }
                                            }
                                            if($sayacgelen->fiyatlandirma){
                                                $arizafiyat->durum=1;
                                                $ucretlendirilen=Ucretlendirilen::find($servistakip->ucretlendirilen_id);
                                                if($ucretlendirilen){
                                                    if($ucretlendirilen->parabirimi_id!=$ozelbirim->id){
                                                        if ($ucretlendirilen->parabirimi_id == 1) { //tl
                                                            $kur = BackendController::getKurBilgisi($ozelbirim->id, $ucretlendirilen->kurtarihi);
                                                        } else { //euro dolar sterln
                                                            if ($ozelbirim->id == 1) {
                                                                $kur = 1 / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $ucretlendirilen->kurtarihi);
                                                            } else {
                                                                $kur = BackendController::getKurBilgisi($ozelbirim->id, $ucretlendirilen->kurtarihi) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $ucretlendirilen->kurtarihi);
                                                            }
                                                        }
                                                        $fiyat *= $kur;
                                                        if ($parabirimi2->id == $ucretlendirilen->parabirimi_id) {
                                                            $fiyat += $fiyat2;
                                                            $fiyat2 = 0;
                                                            $parabirimi2->id=null;
                                                        }else if($parabirimi2->id != $ucretlendirilen->parabirimi2_id){
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                        }
                                                    }else{
                                                        if($parabirimi2!=null){
                                                            if($ucretlendirilen->parabirimi2_id!=$parabirimi2->id){
                                                                DB::rollBack();
                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                            }
                                                        }
                                                    }
                                                    if($arizafiyat->indirim & $arizafiyat->indirimorani>0) {
                                                        $indirim = (($fiyat * floatval($arizafiyat->indirinorani)) / 100);
                                                        $indirim2 = (($fiyat2 * floatval($arizafiyat->indirinorani)) / 100);
                                                        $kdvsiztutar = $fiyat - $indirim;
                                                        $kdvsiztutar2 = $fiyat2 - $indirim2;
                                                    }else{
                                                        $kdvsiztutar = $fiyat;
                                                        $kdvsiztutar2 = $fiyat2;
                                                    }
                                                    $kdv=($kdvsiztutar*18)/100;
                                                    $kdv2=($kdvsiztutar2*18)/100;
                                                    $toplamtutar=$kdvsiztutar+$kdv;
                                                    $toplamtutar=round(($toplamtutar) * 2) / 2;
                                                    $toplamtutar2=$kdvsiztutar2+$kdv2;
                                                    $toplamtutar2=round(($toplamtutar2) * 2) / 2;
                                                    $ucretlendirilen->fiyat=round(($ucretlendirilen->fiyat+$toplamtutar) * 2) / 2;
                                                    $ucretlendirilen->fiyat2=round(($ucretlendirilen->fiyat2+$toplamtutar2) * 2) / 2;
                                                    $ucretlendirilen->save();
                                                    if(!$servistakip->onaylanan_id){
                                                        BackendController::HatirlatmaEkle(5, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                                    }
                                                }else{
                                                    DB::rollBack();
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Sayaca Ait Fiyatlandırma Bulunamadı!', 'type' => 'error'));
                                                }
                                            }else{
                                                $arizafiyat->durum=0;
                                                if($arizafiyat->indirim & $arizafiyat->indirimorani>0) {
                                                    $indirim = (($fiyat * floatval($arizafiyat->indirinorani)) / 100);
                                                    $indirim2 = (($fiyat2 * floatval($arizafiyat->indirinorani)) / 100);
                                                    $kdvsiztutar = $fiyat - $indirim;
                                                    $kdvsiztutar2 = $fiyat2 - $indirim2;
                                                }else{
                                                    $kdvsiztutar = $fiyat;
                                                    $kdvsiztutar2 = $fiyat2;
                                                }
                                                $kdv=($kdvsiztutar*18)/100;
                                                $kdv2=($kdvsiztutar2*18)/100;
                                                $toplamtutar=$kdvsiztutar+$kdv;
                                                $toplamtutar=round(($toplamtutar) * 2) / 2;
                                                $toplamtutar2=$kdvsiztutar2+$kdv2;
                                                $toplamtutar2=round(($toplamtutar2) * 2) / 2;
                                                BackendController::HatirlatmaEkle(4, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                            }
                                            $arizafiyat->fiyat = $fiyat;
                                            $arizafiyat->fiyat2 = $fiyat2;
                                            $arizafiyat->indirim = $indirim;
                                            $arizafiyat->indirim2 = $indirim2;
                                            $arizafiyat->tutar = $kdvsiztutar;
                                            $arizafiyat->tutar2 = $kdvsiztutar2;
                                            $arizafiyat->kdv = $kdv;
                                            $arizafiyat->kdv2 = $kdv2;
                                            $arizafiyat->toplamtutar = $toplamtutar;
                                            $arizafiyat->toplamtutar2 = $toplamtutar2;
                                            $arizafiyat->parabirimi_id=$ozelbirim->id;
                                            $arizafiyat->parabirimi2_id=$parabirimi2==null ? null : $parabirimi2->id;
                                            $arizafiyat->save();
                                        }
                                        BackendController::BildirimGeriAl(10,$sayacgelen->netsiscari_id,$sayacgelen->servis_id,1);
                                        BackendController::BildirimEkle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Kalibrasyon yapılan sayaçlar depo teslimatına kaydedilemedi', 'type' => 'error'));
                                    }
                                } else if ($silinecek->durum == 0) {
                                    $sayi = -1;
                                    try {
                                        if($sayacgelen->musterionay || $kalibrasyongrup->periyodik) {
                                            $sayacgelen->teslimdurum=1;
                                            $sayacgelen->save();
                                            if($kalibrasyongrup->periyodik){
                                                $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                    ->where('depodurum', 0)->where('tipi', 0)->where('periyodik', $kalibrasyongrup->periyodik)->where('subegonderim',0)->first();
                                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                {
                                                    $depoteslim->secilenler .= ',' . $kalibrasyon->sayacgelen_id;
                                                    $depoteslim->sayacsayisi += 1;
                                                    $depoteslim->save();
                                                } else { //yeni depo teslimatı yapılacak
                                                    $depoteslim = new DepoTeslim;
                                                    $depoteslim->servis_id = $sayacgelen->servis_id;
                                                    $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                    $depoteslim->secilenler = $sayacgelen->id;
                                                    $depoteslim->sayacsayisi = 1;
                                                    $depoteslim->depodurum = 0;
                                                    $depoteslim->periyodik = $kalibrasyongrup->periyodik;
                                                    $depoteslim->parabirimi_id=$uretimyer->parabirimi_id;
                                                    $depoteslim->save();
                                                }
                                            }else{
                                                $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                                                $birim = $arizafiyat->parabirimi_id;
                                                $birim2 = $arizafiyat->parabirimi2_id;
                                                $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                                if($arizakayit->arizakayit_durum==7){
                                                    $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                        ->where('depodurum', 0)->where('tipi', 2)->where('periyodik',0)->where('subegonderim', 0)->first();
                                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                    {
                                                        $depoteslim->secilenler .= ',' . $kalibrasyon->sayacgelen_id;
                                                        $depoteslim->sayacsayisi += 1;
                                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id==null ? $birim2 : $depoteslim->parabirimi2_id;
                                                        $depoteslim->save();
                                                    } else { //yeni depo teslimatı yapılacak
                                                        $depoteslim = new DepoTeslim;
                                                        $depoteslim->servis_id = $sayacgelen->servis_id;
                                                        $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                        $depoteslim->secilenler = $sayacgelen->id;
                                                        $depoteslim->sayacsayisi = 1;
                                                        $depoteslim->depodurum = 0;
                                                        $depoteslim->tipi = 2;
                                                        $depoteslim->parabirimi_id=$birim;
                                                        $depoteslim->parabirimi2_id=$birim2;
                                                        $depoteslim->save();
                                                    }
                                                }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0){
                                                    $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                        ->where('depodurum', 0)->where('tipi', 0)->where('periyodik',0)->where('subegonderim', 0)->first();
                                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                    {
                                                        $depoteslim->secilenler .= ',' . $kalibrasyon->sayacgelen_id;
                                                        $depoteslim->sayacsayisi += 1;
                                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id==null ? $birim2 : $depoteslim->parabirimi2_id;
                                                        $depoteslim->save();
                                                    } else { //yeni depo teslimatı yapılacak
                                                        $depoteslim = new DepoTeslim;
                                                        $depoteslim->servis_id = $sayacgelen->servis_id;
                                                        $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                        $depoteslim->secilenler = $sayacgelen->id;
                                                        $depoteslim->sayacsayisi = 1;
                                                        $depoteslim->depodurum = 0;
                                                        $depoteslim->parabirimi_id=$birim;
                                                        $depoteslim->parabirimi2_id=$birim2;
                                                        $depoteslim->save();
                                                    }
                                                }else{
                                                    $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                        ->where('depodurum', 0)->where('tipi',1)->where('periyodik', 0)->where('subegonderim', 0)->first();
                                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                    {
                                                        $depoteslim->secilenler .= ',' . $kalibrasyon->sayacgelen_id;
                                                        $depoteslim->sayacsayisi += 1;
                                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id==null ? $birim2 : $depoteslim->parabirimi2_id;
                                                        $depoteslim->save();
                                                    } else { //yeni depo teslimatı yapılacak
                                                        $depoteslim = new DepoTeslim;
                                                        $depoteslim->servis_id = $sayacgelen->servis_id;
                                                        $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                        $depoteslim->secilenler = $sayacgelen->id;
                                                        $depoteslim->sayacsayisi = 1;
                                                        $depoteslim->depodurum = 0;
                                                        $depoteslim->tipi = 1;
                                                        $depoteslim->parabirimi_id=$birim;
                                                        $depoteslim->parabirimi2_id=$birim2;
                                                        $depoteslim->save();
                                                    }
                                                }
                                            }
                                            BackendController::HatirlatmaEkle(9, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                        }
                                        BackendController::HatirlatmaGuncelle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                        BackendController::BildirimEkle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Kalibrasyon yapılan sayaçlar depo teslimatına kaydedilemedi', 'type' => 'error'));
                                    }
                                }
                                $sayacgelen->kalibrasyon = 1;
                                $sayacgelen->beyanname = $sayacgelen->beyanname==-2 ? 0 : $sayacgelen->beyanname;
                                $sayacgelen->save();
                                $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                $servistakip->save();
                                if(!$kalibrasyongrup->periyodik){
                                    $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
                                    $sayacyapilan = SayacYapilan::find($arizakayit->sayacyapilan_id);
                                    $sayacuyari = SayacUyari::find($arizakayit->sayacuyari_id);
                                    $arizakayiteski = ArizaKayitEski::where('arizakayit_id',$arizakayit->id)->first();
                                    if($arizakayiteski){
                                        $sayacyapilan->yapilanlar=$arizakayiteski->yapilanlar;
                                        $sayacyapilan->save();
                                        $sayacuyari->uyarilar =$arizakayiteski->uyarilar;
                                        $sayacuyari->save();
                                        $arizakayit->arizakayit_durum = $arizakayiteski->arizakayit_durum;
                                        $arizakayit->save();
                                        $arizakayiteski->delete();
                                    }
                                    if($arizakayit->arizakayit_durum==4) //şikayetli sayaç ise
                                        $arizakayit->rapordurum = -1;
                                    else
                                        $arizakayit->rapordurum = 0 ;
                                    $arizakayit->save();
                                }
                                $silinecek->delete();
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => 'Kalibrasyon Bilgileri Kaydedilirken Hata Oluştu.', 'type' => 'error'));
                            }
                        }
                    } else { //2.kalibrasyon
                        if ($kalibrasyon->durum == 1) { //kalibrasyon geçtiyse depo teslime aktarılacak hurda silinecek depo teslim değişecek
                            try {
                                $hurda = Hurda::where('sayacgelen_id', $sayacgelen->id)->where('kalibrasyon2_id', $kalibrasyon->id)->first();
                                $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                    ->where('depodurum',0)->where('tipi',3)->where('periyodik',$kalibrasyongrup->periyodik)->where('subegonderim',0)->first();
                                $hurdanedeni= HurdaNedeni::find($hurda->hurdanedeni_id);
                                $hurdanedeni->kullanim-=($hurdanedeni->kullanim==0 ? 0 : 1);
                                $hurdanedeni->save();
                                $hurda->delete();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $yenilist = "";
                                    foreach ($secilenlist as $secilen) {
                                        if ($secilen != $kalibrasyon->sayacgelen_id) {
                                            $yenilist .= ($yenilist == "" ? "" : ",") . $secilen;
                                        }
                                    }
                                    if ($yenilist == "") {
                                        $depoteslim->delete();
                                    } else {
                                        $depoteslim->secilenler = $yenilist;
                                        $depoteslim->sayacsayisi -= 1;
                                        $depoteslim->save();
                                    }
                                }
                                try {
                                    if($sayacgelen->musterionay || $kalibrasyongrup->periyodik) {
                                        $sayacgelen->teslimdurum=1;
                                        $sayacgelen->save();
                                        if ($kalibrasyongrup->periyodik) {
                                            $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                ->where('depodurum', 0)->where('tipi', 0)->where('periyodik', $kalibrasyongrup->periyodik)->where('subegonderim', 0)->first();
                                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                            {
                                                $depoteslim->secilenler .= ',' . $kalibrasyon->sayacgelen_id;
                                                $depoteslim->sayacsayisi += 1;
                                                $depoteslim->save();
                                            } else { //yeni depo teslimatı yapılacak
                                                $depoteslim = new DepoTeslim;
                                                $depoteslim->servis_id = $sayacgelen->servis_id;
                                                $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                $depoteslim->secilenler = $sayacgelen->id;
                                                $depoteslim->sayacsayisi = 1;
                                                $depoteslim->depodurum = 0;
                                                $depoteslim->periyodik = $kalibrasyongrup->periyodik;
                                                $depoteslim->parabirimi_id = $uretimyer->parabirimi_id;
                                                $depoteslim->save();
                                            }
                                        } else {
                                            $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                                            $birim = $arizafiyat->parabirimi_id;
                                            $birim2 = $arizafiyat->parabirimi2_id;
                                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                            if ($arizakayit->arizakayit_durum == 7) {
                                                $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                    ->where('depodurum', 0)->where('tipi', 2)->where('periyodik', 0)->where('subegonderim', 0)->first();
                                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                {
                                                    $depoteslim->secilenler .= ',' . $kalibrasyon->sayacgelen_id;
                                                    $depoteslim->sayacsayisi += 1;
                                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                    $depoteslim->save();
                                                } else { //yeni depo teslimatı yapılacak
                                                    $depoteslim = new DepoTeslim;
                                                    $depoteslim->servis_id = $sayacgelen->servis_id;
                                                    $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                    $depoteslim->secilenler = $sayacgelen->id;
                                                    $depoteslim->sayacsayisi = 1;
                                                    $depoteslim->depodurum = 0;
                                                    $depoteslim->tipi = 2;
                                                    $depoteslim->parabirimi_id = $birim;
                                                    $depoteslim->parabirimi2_id = $birim2;
                                                    $depoteslim->save();
                                                }
                                            } else if ($arizafiyat->toplamtutar > 0 || $arizafiyat->toplamtutar2 > 0) {
                                                $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                    ->where('depodurum', 0)->where('tipi', 0)->where('periyodik', 0)->where('subegonderim', 0)->first();
                                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                {
                                                    $depoteslim->secilenler .= ',' . $kalibrasyon->sayacgelen_id;
                                                    $depoteslim->sayacsayisi += 1;
                                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                    $depoteslim->save();
                                                } else { //yeni depo teslimatı yapılacak
                                                    $depoteslim = new DepoTeslim;
                                                    $depoteslim->servis_id = $sayacgelen->servis_id;
                                                    $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                    $depoteslim->secilenler = $sayacgelen->id;
                                                    $depoteslim->sayacsayisi = 1;
                                                    $depoteslim->depodurum = 0;
                                                    $depoteslim->parabirimi_id = $birim;
                                                    $depoteslim->parabirimi2_id = $birim2;
                                                    $depoteslim->save();
                                                }
                                            } else {
                                                $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                    ->where('depodurum', 0)->where('tipi', 1)->where('periyodik', 0)->where('subegonderim', 0)->first();
                                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                {
                                                    $depoteslim->secilenler .= ',' . $kalibrasyon->sayacgelen_id;
                                                    $depoteslim->sayacsayisi += 1;
                                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                    $depoteslim->save();
                                                } else { //yeni depo teslimatı yapılacak
                                                    $depoteslim = new DepoTeslim;
                                                    $depoteslim->servis_id = $sayacgelen->servis_id;
                                                    $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                    $depoteslim->secilenler = $sayacgelen->id;
                                                    $depoteslim->sayacsayisi = 1;
                                                    $depoteslim->depodurum = 0;
                                                    $depoteslim->tipi = 1;
                                                    $depoteslim->parabirimi_id = $birim;
                                                    $depoteslim->parabirimi2_id = $birim2;
                                                    $depoteslim->save();
                                                }
                                            }
                                        }
                                    }else{
                                        BackendController::HatirlatmaSil(9, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                    }
                                    if(!$kalibrasyongrup->periyodik){
                                        $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                                        $uretimyer = UretimYer::find($arizafiyat->uretimyer_id);
                                        $parabirimi = null;
                                        $parabirimi2 = null;
                                        $fiyat=0;
                                        $fiyat2=0;
                                        $indirim=0;
                                        $indirim2=0;
                                        $ucretsizler=explode(',',$arizafiyat->ucretsiz);
                                        $genelfiyatlar=explode(';',$arizafiyat->genel);
                                        $ozelfiyatlar=explode(';',$arizafiyat->ozel);
                                        $genelbirimler=explode(',',$arizafiyat->genelbirim);
                                        $ozelbirimler=explode(',',$arizafiyat->ozelbirim);
                                        $genelbirimlist=array();
                                        $ozelbirimlist=array();
                                        foreach($genelbirimler as $birimid){
                                            $parabirimi=ParaBirimi::find($birimid);
                                            array_push($genelbirimlist,$parabirimi);
                                        }
                                        foreach($ozelbirimler as $birimid){
                                            $parabirimi=ParaBirimi::find($birimid);
                                            array_push($ozelbirimlist,$parabirimi);
                                        }
                                        $genelbirim = BackendController::getGenelParabirimi();
                                        $ozelbirim = ParaBirimi::find($uretimyer->parabirimi_id);
                                        $kur = 1;
                                        if($genelbirim->id!=$ozelbirim->id){
                                            if ($ozelbirim->id == 1) { //tl
                                                $kur = BackendController::getKurBilgisi($genelbirim->id, $arizafiyat->kurtarihi);
                                            } else { //euro dolar sterln
                                                if ($genelbirim->id == 1) {
                                                    $kur = 1 / BackendController::getKurBilgisi($ozelbirim->id, $arizafiyat->kurtarihi);
                                                } else {
                                                    $kur = BackendController::getKurBilgisi($genelbirim->id, $arizafiyat->kurtarihi) / BackendController::getKurBilgisi($ozelbirim->id, $arizafiyat->kurtarihi);
                                                }
                                            }
                                        }
                                        if($arizafiyat->garanti) {
                                            $fiyat = 0;
                                            $fiyat2 = 0;
                                        }else if ($arizafiyat->fiyatdurum) { //genel fiyatlar
                                            $parabirimi = $genelbirim;
                                            $parabirimi2 = null;
                                            $index = 0;
                                            foreach($genelfiyatlar as $genelfiyat){
                                                if($ucretsizler[$index]!='1'){
                                                    if($parabirimi->id==$genelbirimler[$index]){
                                                        $fiyat +=floatval($genelfiyat);
                                                    }else if($parabirimi2==null || $parabirimi2->id==$genelbirimler[$index]){
                                                        $fiyat2 += floatval($genelfiyat);
                                                        $parabirimi2 = $genelbirimler[$index];
                                                    }else{
                                                        DB::rollBack();
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                    }
                                                }
                                            }
                                            $fiyat*=$kur;
                                            if($parabirimi2->id==$ozelbirim->id){
                                                $fiyat+=$fiyat2;
                                                $fiyat2=0;
                                                $parabirimi2=null;
                                            }
                                        } else { //ozel fiyatlar
                                            $parabirimi = $ozelbirim;
                                            $parabirimi2 = null;
                                            $index = 0;
                                            foreach($ozelfiyatlar as $ozelfiyat){
                                                if($ucretsizler[$index]!='1'){
                                                    if($parabirimi->id==$ozelbirimler[$index]){
                                                        $fiyat +=floatval($ozelfiyat);
                                                    }else if($parabirimi2==null || $parabirimi2->id==$ozelbirimler[$index]){
                                                        $fiyat2 += floatval($ozelfiyat);
                                                        $parabirimi2 = $ozelbirimler[$index];
                                                    }else{
                                                        DB::rollBack();
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                    }
                                                }
                                            }
                                            if($parabirimi2->id==$ozelbirim->id){
                                                $fiyat+=$fiyat2;
                                                $fiyat2=0;
                                                $parabirimi2=null;
                                            }
                                        }
                                        if($sayacgelen->fiyatlandirma){
                                            $arizafiyat->durum=1;
                                            $ucretlendirilen=Ucretlendirilen::find($servistakip->ucretlendirilen_id);
                                            if($ucretlendirilen){
                                                if($ucretlendirilen->parabirimi_id!=$ozelbirim->id){
                                                    if ($ucretlendirilen->parabirimi_id == 1) { //tl
                                                        $kur = BackendController::getKurBilgisi($ozelbirim->id, $ucretlendirilen->kurtarihi);
                                                    } else { //euro dolar sterln
                                                        if ($ozelbirim->id == 1) {
                                                            $kur = 1 / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $ucretlendirilen->kurtarihi);
                                                        } else {
                                                            $kur = BackendController::getKurBilgisi($ozelbirim->id, $ucretlendirilen->kurtarihi) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $ucretlendirilen->kurtarihi);
                                                        }
                                                    }
                                                    $fiyat *= $kur;
                                                    if ($parabirimi2->id == $ucretlendirilen->parabirimi_id) {
                                                        $fiyat += $fiyat2;
                                                        $fiyat2 = 0;
                                                        $parabirimi2->id=null;
                                                    }else if($parabirimi2->id != $ucretlendirilen->parabirimi2_id){
                                                        DB::rollBack();
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                    }
                                                }else{
                                                    if($parabirimi2!=null){
                                                        if($ucretlendirilen->parabirimi2_id!=$parabirimi2->id){
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                        }
                                                    }
                                                }
                                                if($arizafiyat->indirim & $arizafiyat->indirimorani>0) {
                                                    $indirim = (($fiyat * floatval($arizafiyat->indirinorani)) / 100);
                                                    $indirim2 = (($fiyat2 * floatval($arizafiyat->indirinorani)) / 100);
                                                    $kdvsiztutar = $fiyat - $indirim;
                                                    $kdvsiztutar2 = $fiyat2 - $indirim2;
                                                }else{
                                                    $kdvsiztutar = $fiyat;
                                                    $kdvsiztutar2 = $fiyat2;
                                                }
                                                $kdv=($kdvsiztutar*18)/100;
                                                $kdv2=($kdvsiztutar2*18)/100;
                                                $toplamtutar=$kdvsiztutar+$kdv;
                                                $toplamtutar=round(($toplamtutar) * 2) / 2;
                                                $toplamtutar2=$kdvsiztutar2+$kdv2;
                                                $toplamtutar2=round(($toplamtutar2) * 2) / 2;
                                                $ucretlendirilen->fiyat=round(($ucretlendirilen->fiyat+$toplamtutar) * 2) / 2;
                                                $ucretlendirilen->fiyat2=round(($ucretlendirilen->fiyat2+$toplamtutar2) * 2) / 2;
                                                $ucretlendirilen->save();
                                                if(!$servistakip->onaylanan_id){
                                                    BackendController::HatirlatmaEkle(5, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                                }
                                            }else{
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Sayaca Ait Fiyatlandırma Bulunamadı!', 'type' => 'error'));
                                            }
                                        }else{
                                            $arizafiyat->durum=0;
                                            if($arizafiyat->indirim & $arizafiyat->indirimorani>0) {
                                                $indirim = (($fiyat * floatval($arizafiyat->indirinorani)) / 100);
                                                $indirim2 = (($fiyat2 * floatval($arizafiyat->indirinorani)) / 100);
                                                $kdvsiztutar = $fiyat - $indirim;
                                                $kdvsiztutar2 = $fiyat2 - $indirim2;
                                            }else{
                                                $kdvsiztutar = $fiyat;
                                                $kdvsiztutar2 = $fiyat2;
                                            }
                                            $kdv=($kdvsiztutar*18)/100;
                                            $kdv2=($kdvsiztutar2*18)/100;
                                            $toplamtutar=$kdvsiztutar+$kdv;
                                            $toplamtutar=round(($toplamtutar) * 2) / 2;
                                            $toplamtutar2=$kdvsiztutar2+$kdv2;
                                            $toplamtutar2=round(($toplamtutar2) * 2) / 2;
                                            BackendController::HatirlatmaEkle(4, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                        }
                                        $arizafiyat->fiyat = $fiyat;
                                        $arizafiyat->fiyat2 = $fiyat2;
                                        $arizafiyat->indirim = $indirim;
                                        $arizafiyat->indirim2 = $indirim2;
                                        $arizafiyat->tutar = $kdvsiztutar;
                                        $arizafiyat->tutar2 = $kdvsiztutar2;
                                        $arizafiyat->kdv = $kdv;
                                        $arizafiyat->kdv2 = $kdv2;
                                        $arizafiyat->toplamtutar = $toplamtutar;
                                        $arizafiyat->toplamtutar2 = $toplamtutar2;
                                        $arizafiyat->parabirimi_id=$ozelbirim->id;
                                        $arizafiyat->parabirimi2_id=$parabirimi2==null ? null : $parabirimi2->id;
                                        $arizafiyat->save();
                                    }
                                    BackendController::BildirimGeriAl(10,$sayacgelen->netsiscari_id,$sayacgelen->servis_id,1);
                                    BackendController::BildirimEkle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Kalibrasyon yapılan sayaçlar depo teslimatına kaydedilemedi', 'type' => 'error'));
                                }
                                $sayacgelen->kalibrasyon = 1;
                                $sayacgelen->beyanname = $sayacgelen->beyanname==-2 ? 0 : $sayacgelen->beyanname;
                                $sayacgelen->save();
                                $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                $servistakip->save();
                                if(!$kalibrasyongrup->periyodik) {
                                    $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
                                    $sayacyapilan = SayacYapilan::find($arizakayit->sayacyapilan_id);
                                    $sayacuyari = SayacUyari::find($arizakayit->sayacuyari_id);
                                    $arizakayiteski = ArizaKayitEski::where('arizakayit_id', $arizakayit->id)->first();
                                    if ($arizakayiteski) {
                                        $sayacyapilan->yapilanlar = $arizakayiteski->yapilanlar;
                                        $sayacyapilan->save();
                                        $sayacuyari->uyarilar = $arizakayiteski->uyarilar;
                                        $sayacuyari->save();
                                        $arizakayit->arizakayit_durum = $arizakayiteski->arizakayit_durum;
                                        $arizakayit->save();
                                        $arizakayiteski->delete();
                                    }
                                    if ($arizakayit->arizakayit_durum == 4) //şikayetli sayaç ise
                                        $arizakayit->rapordurum = -1;
                                    else
                                        $arizakayit->rapordurum = 0;
                                    $arizakayit->save();
                                }
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => 'Kalibrasyon Bilgileri Kaydedilirken Hata Oluştu.', 'type' => 'error'));
                            }
                        }
                    }
                } else { //başarılı olan güncellendiyse
                    if ($eskikalibrasyonsayi == 1) //1. kalibrasyon
                    {
                        try {
                            if ($kalibrasyon->durum == 2) { //2. kalibrasyon açılır depo teslim geri alınır
                                $sayi = 1;
                                $kalibrasyonyeni = new Kalibrasyon;
                                $kalibrasyonyeni->sayacgelen_id = $kalibrasyon->sayacgelen_id;
                                $kalibrasyonyeni->sayacadi_id = $kalibrasyon->sayacadi_id;
                                $kalibrasyonyeni->kalibrasyon_seri = $kalibrasyon->kalibrasyon_seri;
                                $kalibrasyonyeni->imalyili = $kalibrasyon->imalyili;
                                $kalibrasyonyeni->kalibrasyongrup_id = $kalibrasyon->kalibrasyongrup_id;
                                $kalibrasyonyeni->kalibrasyonsayisi = 2;
                                $kalibrasyonyeni->durum = 0;
                                $kalibrasyonyeni->save();
                                $servistakip->kalibrasyon_id = $kalibrasyonyeni->id;
                                $servistakip->save();
                                if(!$kalibrasyongrup->periyodik) {
                                    $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
                                    $arizakayit->rapordurum = -1;
                                    $arizakayit->save();
                                }
                                $sayacgelen->kalibrasyon = 0;
                                $sayacgelen->beyanname = $sayacgelen->beyanname==-1 ? $sayacgelen->beyanname : -2;
                                $sayacgelen->teslimdurum=0;
                                $sayacgelen->save();
                                if($sayacgelen->musterionay || $kalibrasyongrup->periyodik) {
                                    if($kalibrasyongrup->periyodik){
                                        $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                            ->where('depodurum',0)->where('tipi',0)->where('periyodik', $kalibrasyongrup->periyodik)->where('subegonderim',0)->first();
                                    }else{
                                        $arizafiyat=ArizaFiyat::find($servistakip->arizafiyat_id);
                                        $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                        if ($arizakayit->arizakayit_durum == 7) {
                                            $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                ->where('depodurum',0)->where('tipi',2)->where('periyodik', 0)->where('subegonderim',0)->first();
                                        }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0){
                                            $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                ->where('depodurum',0)->where('tipi',0)->where('periyodik', 0)->where('subegonderim',0)->first();
                                        }else{
                                            $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                ->where('depodurum',0)->where('tipi',1)->where('periyodik', 0)->where('subegonderim',0)->first();
                                        }
                                    }
                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                    {
                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                        $yenilist = "";
                                        foreach ($secilenlist as $secilen) {
                                            if ($secilen != $kalibrasyon->sayacgelen_id) {
                                                $yenilist .= ($yenilist == "" ? "" : ",") . $secilen;
                                            }
                                        }
                                        if ($yenilist == "") {
                                            $depoteslim->delete();
                                        } else {
                                            $depoteslim->secilenler = $yenilist;
                                            $depoteslim->sayacsayisi -= 1;
                                            $depoteslim->save();
                                        }
                                    }
                                    BackendController::HatirlatmaSil(9, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                }
                                BackendController::BildirimGeriAl(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                BackendController::HatirlatmaGeriAl(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => 'Kalibrasyon Bilgileri Kaydedilirken Hata Oluştu.', 'type' => 'error'));
                        }
                    } else { //2.kalibrasyon
                        if ($kalibrasyon->durum == 2) { //depo teslim geri alınır hurdaya aktarılır
                            try {
                                $arizafiyat=ArizaFiyat::find($servistakip->arizafiyat_id);
                                $birim = $arizafiyat->parabirimi_id;
                                $birim2 = $arizafiyat->parabirimi2_id;
                                $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                if ($sayacgelen->musterionay || $kalibrasyongrup->periyodik) {
                                    if($kalibrasyongrup->periyodik){
                                        $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                            ->where('depodurum',0)->where('tipi',0)->where('periyodik', $kalibrasyongrup->periyodik)->where('subegonderim',0)->first();
                                    }else{
                                        if ($arizakayit->arizakayit_durum == 7) {
                                            $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                ->where('depodurum',0)->where('tipi',2)->where('periyodik', 0)->where('subegonderim',0)->first();
                                        }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0){
                                            $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                ->where('depodurum',0)->where('tipi',0)->where('periyodik', 0)->where('subegonderim',0)->first();
                                        }else{
                                            $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                ->where('depodurum',0)->where('tipi',1)->where('periyodik', 0)->where('subegonderim',0)->first();
                                        }
                                    }
                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                    {
                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                        $yenilist = "";
                                        foreach ($secilenlist as $secilen) {
                                            if ($secilen != $kalibrasyon->sayacgelen_id) {
                                                $yenilist .= ($yenilist == "" ? "" : ",") . $secilen;
                                            }
                                        }
                                        if ($yenilist == "") {
                                            $depoteslim->delete();
                                        } else {
                                            $depoteslim->secilenler = $yenilist;
                                            $depoteslim->sayacsayisi -= 1;
                                            $depoteslim->save();
                                        }
                                    }
                                }
                                $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                    ->where('depodurum', 0)->where('tipi',3)->where('periyodik', $kalibrasyongrup->periyodik)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                        $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                        $depoteslim->sayacsayisi += 1;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $sayacgelen->servis_id;
                                    $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                    $depoteslim->secilenler = $sayacgelen->id;
                                    $depoteslim->sayacsayisi = 1;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 3;
                                    $depoteslim->periyodik = $kalibrasyongrup->periyodik;
                                    $depoteslim->parabirimi_id = $birim;
                                    $depoteslim->parabirimi2_id = $birim2;
                                    $depoteslim->save();
                                }
                                $sayac = Sayac::where('uretimyer_id', $sayacgelen->uretimyer_id)->where('serino', $sayacgelen->serino)->first();
                                $eski = Kalibrasyon::where('sayacgelen_id', $sayacgelen->id)->where('kalibrasyonsayisi', 1)->first();
                                $hurdakayit = new Hurda;
                                $hurdakayit->servis_id = $sayacgelen->servis_id;
                                $hurdakayit->netsiscari_id = $sayacgelen->netsiscari_id;
                                $hurdakayit->sayac_id = $sayac->id;
                                $hurdakayit->hurdanedeni_id = 1;
                                $hurdakayit->hurdatarihi = date('Y-m-d H:i:s');
                                $hurdakayit->sayacgelen_id = $sayacgelen->id;
                                $hurdakayit->kalibrasyon_id = $eski->id;
                                $hurdakayit->kalibrasyon2_id = $kalibrasyon->id;
                                $hurdakayit->kullanici_id = Auth::user()->id;
                                $hurdakayit->save();
                                $hurdanedeni= HurdaNedeni::find(1);
                                $hurdanedeni->kullanim++;
                                $hurdanedeni->save();
                                $sayacgelen->kalibrasyon = 1;
                                $sayacgelen->sayacdurum = 3;
                                $sayacgelen->teslimdurum = 3;
                                $sayacgelen->beyanname = -2;
                                $sayacgelen->save();
                                if(!$kalibrasyongrup->periyodik){
                                    $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
                                    $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                                    $sayacyapilan = SayacYapilan::find($arizakayit->sayacyapilan_id);
                                    $sayacuyari = SayacUyari::find($arizakayit->sayacuyari_id);
                                    $arizakayiteski = ArizaKayitEski::where('arizakayit_id',$arizakayit->id)->first();
                                    if($arizakayiteski){
                                        $arizakayiteski->yapilanlar=$sayacyapilan->yapilanlar;
                                        $arizakayiteski->uyarilar=$sayacuyari->uyarilar;
                                        $arizakayiteski->arizakayit_durum=$arizakayit->arizakayit_durum;
                                        $arizakayiteski->save();
                                    }else{
                                        $arizakayiteski = new ArizaKayitEski;
                                        $arizakayiteski->arizakayit_id = $arizakayit->id;
                                        $arizakayiteski->yapilanlar=$sayacyapilan->yapilanlar;
                                        $arizakayiteski->uyarilar=$sayacuyari->uyarilar;
                                        $arizakayiteski->arizakayit_durum=$arizakayit->arizakayit_durum;
                                        $arizakayiteski->save();
                                    }
                                    $sayacyapilan->yapilanlar='58';
                                    $sayacyapilan->save();
                                    $sayacuyari->uyarilar ='11';
                                    $sayacuyari->save();
                                    $arizakayit->arizakayit_durum = 2;
                                    $arizakayit->rapordurum = 0 ;
                                    $arizakayit->save();

                                    if($sayacgelen->fiyatlandirma){
                                        $ucretlendirilen=Ucretlendirilen::find($servistakip->ucretlendirilen_id);
                                        if(!$sayacgelen->musterionay){
                                            $secilenlist=explode(',',$ucretlendirilen->secilenler);
                                            $list="";
                                            foreach ($secilenlist as $secilen){
                                                if($secilen!=$arizafiyat->id)
                                                    $list.=($list=="" ? "" : ",").$secilen;
                                            }
                                            if($ucretlendirilen->sayacsayisi>1){
                                                $ucretlendirilen->secilenler=$list;
                                                $ucretlendirilen->sayacsayisi--;
                                                if($ucretlendirilen->parabirimi_id!=$arizafiyat->parabirimi_id){
                                                    if ($ucretlendirilen->parabirimi_id == 1) { //tl
                                                        $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi);
                                                    } else { //euro dolar sterln
                                                        if ($arizafiyat->parabirimi_id == 1) {
                                                            $kur = 1 / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarihi);
                                                        } else {
                                                            $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $ucretlendirilen->kurtarihi);
                                                        }
                                                    }
                                                    $tutar=$arizafiyat->toplamtutar*$kur;
                                                    if ($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi_id) {
                                                        $tutar += $arizafiyat->toplamtutar2;
                                                    }else if($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi2_id){
                                                        $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                    }else{
                                                        DB::rollBack();
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                    }
                                                    $ucretlendirilen->fiyat=round(($ucretlendirilen->fiyat - $tutar) * 2) / 2;
                                                }else{
                                                    $ucretlendirilen->fiyat-=$arizafiyat->toplamtutar;
                                                    if($arizafiyat->parabirimi2_id!=null){
                                                        if($ucretlendirilen->parabirimi2_id!=$arizafiyat->parabirimi2_id){
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                        }else{
                                                            $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                        }
                                                    }
                                                }
                                                $ucretlendirilen->durum=1;
                                                $ucretlendirilen->save();
                                            }else{
                                                $servistakip->ucretlendirilen_id=NULL;
                                                $servistakip->save();
                                                $ucretlendirilen->delete();
                                            }
                                            BackendController::HatirlatmaSil(6, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                        }else{
                                            if($ucretlendirilen->parabirimi_id!=$arizafiyat->parabirimi_id){
                                                if ($ucretlendirilen->parabirimi_id == 1) { //tl
                                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi);
                                                } else { //euro dolar sterln
                                                    if ($arizafiyat->parabirimi_id == 1) {
                                                        $kur = 1 / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarihi);
                                                    } else {
                                                        $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $ucretlendirilen->kurtarihi);
                                                    }
                                                }
                                                $tutar=$arizafiyat->toplamtutar*$kur;
                                                if ($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi_id) {
                                                    $tutar += $arizafiyat->toplamtutar2;
                                                }else if($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi2_id){
                                                    $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                }else{
                                                    DB::rollBack();
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                }
                                                $ucretlendirilen->fiyat=round(($ucretlendirilen->fiyat - $tutar) * 2) / 2;
                                            }else{
                                                $ucretlendirilen->fiyat-=$arizafiyat->toplamtutar;
                                                if($arizafiyat->parabirimi2_id!=null){
                                                    if($ucretlendirilen->parabirimi2_id!=$arizafiyat->parabirimi2_id){
                                                        DB::rollBack();
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                    }else{
                                                        $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                    }
                                                }
                                            }
                                            $ucretlendirilen->save();
                                        }
                                    }else{
                                        BackendController::HatirlatmaSil(4, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                    }
                                    $arizafiyat->fiyat = 0;
                                    $arizafiyat->fiyat2 = 0;
                                    $arizafiyat->tutar = 0;
                                    $arizafiyat->tutar2 = 0;
                                    $arizafiyat->kdv = 0;
                                    $arizafiyat->kdv2 = 0;
                                    $arizafiyat->toplamtutar = 0;
                                    $arizafiyat->toplamtutar2 = 0;
                                    $arizafiyat->parabirimi2_id = null;
                                    $arizafiyat->durum = 1;
                                    $arizafiyat->save();
                                }
                                if(!$sayacgelen->musterionay && !$kalibrasyongrup->periyodik)
                                    BackendController::HatirlatmaEkle(9,$sayacgelen->netsiscari_id,$sayacgelen->servis_id,1);
                                BackendController::BildirimGeriAl(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                BackendController::BildirimEkle(10, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => 'Kalibrasyon Bilgileri Kaydedilirken Hata Oluştu.', 'type' => 'error'));
                            }
                        }
                    }
                }
                $kalibrasyongrup = KalibrasyonGrup::find($grupid);
                $kalibrasyongrup->biten -= $sayi;
                if ($sayi > 0){
                    $kalibrasyongrup->kalibrasyondurum = 0;
                }
                $kalibrasyongrup->save();
                BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-cog', $kalibrasyongrup->id . ' Numaralı Kalibrasyon Grubuna Ait Kalibrasyon Bilgisi Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Kalibrasyon Grup Numarası:' . $kalibrasyongrup->id);
                DB::commit();
                return Redirect::to('kalibrasyon/kalibrasyondetay/' . $kalibrasyongrup->id)->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Güncellendi', 'text' => 'Kalibrasyon Bilgisi Başarıyla Güncellendi', 'type' => 'success'));
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => 'Kalibrasyon Bilgileri Kaydedilirken Hata Oluştu.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi GÜncellenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getHurdagirisi($id) {
        $grup = KalibrasyonGrup::find($id);
        $grup->netsiscari=NetsisCari::find($grup->netsiscari_id);
        if($grup->netsiscari->caridurum!="A")
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
        $kalibrasyonlar=Kalibrasyon::where('kalibrasyongrup_id',$id)->whereIn('durum',array(0))->get();
        $nedenler=HurdaNedeni::where('sayactur_id',5)->get();
        return View::make('kalibrasyon.hurdagirisi',array('grup'=>$grup,'nedenler'=>$nedenler,'kalibrasyonlar'=>$kalibrasyonlar))->with(array('title'=>'Kalibrasyon Hurda Kayıt Ekranı'));
    }

    public function postHurdagirisi($id){
        try {
            $grupid = $id;
            $kalibrasyongrup = KalibrasyonGrup::find($grupid);
            $secilenlist = Input::get('kalibrasyonid');
            $hurdanedenleri=Input::get('hurdanedenleri');
            $count = 0;
            $sayi=0;
            DB::beginTransaction();
                try {
                    foreach ($secilenlist as $kalibrasyonid) {
                        $kalibrasyon = Kalibrasyon::find($kalibrasyonid);
                        if($kalibrasyon->durum!=0){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Sayaçların kalibrasyon bilgisi zaten girilmiş olabilir!', 'type' => 'error'));
                        }
                        $kalibrasyon->durum = 3; //1 başarılı 2 başarısız 3 hurda
                        $count++;
                        $kalibrasyon->kullanici_id = Auth::user()->id;
                        $kalibrasyon->kalibrasyontarih = date('Y-m-d H:i:s');
                        $kalibrasyon->save();
                        //hurdaya ayrılacak
                        try {
                            $sayacgelen = SayacGelen::find($kalibrasyon->sayacgelen_id);
                            $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                            $uretimyer=UretimYer::find($servistakip->uretimyer_id);
                            $sayac = Sayac::where('uretimyer_id', $sayacgelen->uretimyer_id)->where('serino', $sayacgelen->serino)->first();
                            $hurdakayit = new Hurda;
                            $hurdakayit->servis_id = $sayacgelen->servis_id;
                            $hurdakayit->netsiscari_id = $sayacgelen->netsiscari_id;
                            $hurdakayit->sayac_id = $sayac->id;
                            $hurdakayit->hurdanedeni_id = $hurdanedenleri[$sayi];
                            $hurdakayit->hurdatarihi = date('Y-m-d H:i:s');
                            $hurdakayit->sayacgelen_id = $sayacgelen->id;
                            $hurdakayit->kalibrasyon_id = $kalibrasyon->id;
                            $hurdakayit->kullanici_id = Auth::user()->id;
                            $hurdakayit->save();
                            $hurdanedeni=HurdaNedeni::find($hurdanedenleri[$sayi]);
                            $hurdanedeni->kullanim++;
                            $hurdanedeni->save();
                            try {
                                $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                    ->where('depodurum',0)->where('tipi', 3)->where('periyodik',$kalibrasyongrup->periyodik)->where('subegonderim', 0)->first();
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
                                    $depoteslim->periyodik=$kalibrasyongrup->periyodik;
                                    $depoteslim->parabirimi_id=$uretimyer->parabirimi_id;
                                }
                                $depoteslim->save();
                                $sayacgelen->kalibrasyon=1;
                                $sayacgelen->sayacdurum=3;
                                $sayacgelen->teslimdurum=3;
                                $sayacgelen->beyanname=-2;
                                $sayacgelen->save();
                                $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                $servistakip->durum = 8;
                                $servistakip->kalibrasyontarihi = $kalibrasyon->kalibrasyontarih;
                                $servistakip->sonislemtarihi = $kalibrasyon->kalibrasyontarih;
                                $servistakip->save();
                                $hurdakayit->depoteslim_id = $depoteslim->id;
                                $hurdakayit->save();
                                if(!$kalibrasyongrup->periyodik){
                                    $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
                                    $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                                    $sayacyapilan = SayacYapilan::find($arizakayit->sayacyapilan_id);
                                    $sayacuyari = SayacUyari::find($arizakayit->sayacuyari_id);
                                    $arizakayiteski = ArizaKayitEski::where('arizakayit_id',$arizakayit->id)->first();
                                    if($arizakayiteski){
                                        $arizakayiteski->yapilanlar=$sayacyapilan->yapilanlar;
                                        $arizakayiteski->uyarilar=$sayacuyari->uyarilar;
                                        $arizakayiteski->arizakayit_durum=$arizakayit->arizakayit_durum;
                                        $arizakayiteski->save();
                                    }else{
                                        $arizakayiteski = new ArizaKayitEski;
                                        $arizakayiteski->arizakayit_id = $arizakayit->id;
                                        $arizakayiteski->yapilanlar=$sayacyapilan->yapilanlar;
                                        $arizakayiteski->uyarilar=$sayacuyari->uyarilar;
                                        $arizakayiteski->arizakayit_durum=$arizakayit->arizakayit_durum;
                                        $arizakayiteski->save();
                                    }
                                    $sayacyapilan->yapilanlar='58';
                                    $sayacyapilan->save();
                                    $sayacuyari->uyarilar ='11';
                                    $sayacuyari->save();
                                    $arizakayit->arizakayit_durum = 2;
                                    $arizakayit->rapordurum = 0 ;
                                    $arizakayit->save();
                                    if($sayacgelen->fiyatlandirma){
                                        $ucretlendirilen=Ucretlendirilen::find($servistakip->ucretlendirilen_id);
                                        if(!$sayacgelen->musterionay){
                                            $secilenlist=explode(',',$ucretlendirilen->secilenler);
                                            $list="";
                                            foreach ($secilenlist as $secilen){
                                                if($secilen!=$arizafiyat->id)
                                                    $list.=($list=="" ? "" : ",").$secilen;
                                            }
                                            if($ucretlendirilen->sayacsayisi>1){
                                                $ucretlendirilen->secilenler=$list;
                                                $ucretlendirilen->sayacsayisi--;
                                                if($ucretlendirilen->parabirimi_id!=$arizafiyat->parabirimi_id){
                                                    if ($ucretlendirilen->parabirimi_id == 1) { //tl
                                                        $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi);
                                                    } else { //euro dolar sterln
                                                        if ($arizafiyat->parabirimi_id == 1) {
                                                            $kur = 1 / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarihi);
                                                        } else {
                                                            $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $ucretlendirilen->kurtarihi);
                                                        }
                                                    }
                                                    $tutar=$arizafiyat->toplamtutar*$kur;
                                                    if ($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi_id) {
                                                        $tutar += $arizafiyat->toplamtutar2;
                                                    }else if($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi2_id){
                                                        $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                    }else{
                                                        DB::rollBack();
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                    }
                                                    $ucretlendirilen->fiyat=round(($ucretlendirilen->fiyat - $tutar) * 2) / 2;
                                                }else{
                                                    $ucretlendirilen->fiyat-=$arizafiyat->toplamtutar;
                                                    if($arizafiyat->parabirimi2_id!=null){
                                                        if($ucretlendirilen->parabirimi2_id!=$arizafiyat->parabirimi2_id){
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                        }else{
                                                            $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                        }
                                                    }
                                                }
                                                $ucretlendirilen->durum=1;
                                                $ucretlendirilen->save();
                                            }else{
                                                $servistakip->ucretlendirilen_id=NULL;
                                                $servistakip->save();
                                                $ucretlendirilen->delete();
                                            }
                                            BackendController::HatirlatmaSil(6, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                        }else{
                                            if($ucretlendirilen->parabirimi_id!=$arizafiyat->parabirimi_id){
                                                if ($ucretlendirilen->parabirimi_id == 1) { //tl
                                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi);
                                                } else { //euro dolar sterln
                                                    if ($arizafiyat->parabirimi_id == 1) {
                                                        $kur = 1 / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarihi);
                                                    } else {
                                                        $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $ucretlendirilen->kurtarihi);
                                                    }
                                                }
                                                $tutar=$arizafiyat->toplamtutar*$kur;
                                                if ($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi_id) {
                                                    $tutar += $arizafiyat->toplamtutar2;
                                                }else if($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi2_id){
                                                    $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                }else{
                                                    DB::rollBack();
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                }
                                                $ucretlendirilen->fiyat=round(($ucretlendirilen->fiyat - $tutar) * 2) / 2;
                                            }else{
                                                $ucretlendirilen->fiyat-=$arizafiyat->toplamtutar;
                                                if($arizafiyat->parabirimi2_id!=null){
                                                    if($ucretlendirilen->parabirimi2_id!=$arizafiyat->parabirimi2_id){
                                                        DB::rollBack();
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                    }else{
                                                        $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                    }
                                                }
                                            }
                                            $ucretlendirilen->save();
                                        }
                                    }else{
                                        BackendController::HatirlatmaSil(4, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                    }
                                    $arizafiyat->fiyat = 0;
                                    $arizafiyat->fiyat2 = 0;
                                    $arizafiyat->tutar = 0;
                                    $arizafiyat->tutar2 = 0;
                                    $arizafiyat->kdv = 0;
                                    $arizafiyat->kdv2 = 0;
                                    $arizafiyat->toplamtutar = 0;
                                    $arizafiyat->toplamtutar2 = 0;
                                    $arizafiyat->parabirimi2_id = null;
                                    $arizafiyat->durum = 1;
                                    $arizafiyat->save();
                                }
                                BackendController::HatirlatmaGuncelle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                BackendController::HatirlatmaEkle(9, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                BackendController::BildirimEkle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                BackendController::BildirimEkle(10, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Girilen sayaçlar için hurdaya ayırma bilgisi girişi yapılamadı', 'type' => 'error'));
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Girilen sayaçlar için hurdaya ayırma bilgisi girişi yapılamadı', 'type' => 'error'));
                        }
                        $sayi++;
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Hurda Kayıt Bilgisi Kaydedilemedi', 'text' => 'Hurda Kayıt Bilgileri Kaydedilirken Hata Oluştu.', 'type' => 'error'));
                }
                try {
                    $kalibrasyongrup = KalibrasyonGrup::find($grupid);
                    if ($count == ($kalibrasyongrup->adet - $kalibrasyongrup->biten)) {
                        $kalibrasyongrup->kalibrasyondurum = 1;
                    }
                    $kalibrasyongrup->biten += $count;
                    $kalibrasyongrup->save();
                    BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-cog', $kalibrasyongrup->id . ' Numaralı Kalibrasyon Grubuna Ait Hurda Sayaç Bilgisi Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Kalibrasyon Grup Numarası:' . $kalibrasyongrup->id);
                    DB::commit();
                    return Redirect::to('kalibrasyon/kalibrasyondetay/' . $id)->with(array('mesaj' => 'true', 'title' => 'Hurda Sayaç Bilgisi Kaydedildi', 'text' => 'Hurda Sayaç  Bilgisi Başarıyla Kaydedildi', 'type' => 'success'));
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Hurda Sayaç  Bilgisi Kaydedilemedi', 'text' => 'Hurda Sayaç  Bilgileri Kaydedilirken Hata Oluştu.', 'type' => 'error'));
                }

        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Hurda Sayaç  Bilgisi Kaydedilemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }

    }

    public function getHurdabilgi() {
        try {
            $id=Input::get('id');
            $kalibrasyon = Kalibrasyon::find($id);
            $kullanici = Kullanici::find($kalibrasyon->kullanici_id);
            $kalibrasyon->kalibrasyontarih = date("d-m-Y H:i:s", strtotime($kalibrasyon->kalibrasyontarih));
            $hurdakayit=Hurda::where('kalibrasyon_id',$id)->first();
            $hurdanedeni=HurdaNedeni::find($hurdakayit->hurdanedeni_id);
            return Response::json(array('durum' => true, 'kalibrasyon' => $kalibrasyon, 'hurdanedeni' => $hurdanedeni, 'kullanici' => $kullanici));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false,'title'=>'Kalibrasyon Bilgisi Getirilemedi','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error' ));
        }
    }

    public function getHurdaduzenle($id) {
        $kalibrasyon=Kalibrasyon::find($id);
        $sayacgelen = SayacGelen::find($kalibrasyon->sayacgelen_id);
        if ($sayacgelen->teslimdurum==1) {
            if( (time()-strtotime($kalibrasyon->kalibrasyontarih))>86400)
                return Redirect::to('kalibrasyon/kalibrasyondetay/'.$kalibrasyon->kalibrasyongrup_id)->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Güncellenemez', 'text' => 'Sayaç Depo Teslimde Gözüküyor!', 'type' => 'error'));
        }
        $kalibrasyon->sayacadi=SayacAdi::find($kalibrasyon->sayacadi_id);
        $grup = KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
        $grup->netsiscari=NetsisCari::find($grup->netsiscari_id);
        $hurdanedenleri=HurdaNedeni::where('sayactur_id',5)->get();
        $hurdakayit=Hurda::where('kalibrasyon_id',$id)->first();
        if($grup->netsiscari->caridurum!="A")
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
        return View::make('kalibrasyon.hurdaduzenle',array('kalibrasyon'=>$kalibrasyon,'hurdakayit'=>$hurdakayit,'grup'=>$grup,'hurdanedenleri'=>$hurdanedenleri))->with(array('title'=>'Hurda Sayaç Kayıt Ekranı'));
    }

    public function postHurdaduzenle($id){
        try {
            DB::beginTransaction();
            $kalibrasyongrup=KalibrasyonGrup::find($id);
            $secilen=Input::get('secilen');
            $kalibrasyon = Kalibrasyon::find($secilen);
            $sayacgelen = SayacGelen::find($kalibrasyon->sayacgelen_id);
            if ($sayacgelen->teslimdurum==1) {
                if( (time()-strtotime($kalibrasyon->kalibrasyontarih))>86400)
                    return Redirect::to('kalibrasyon/kalibrasyondetay/'.$kalibrasyon->kalibrasyongrup_id)->with(array('mesaj' => 'true', 'title' => 'Kalibrasyon Bilgisi Güncellenemez', 'text' => 'Sayaç Depo Teslimde Gözüküyor!', 'type' => 'error'));
            }
            $hurdaneden=Input::get('hurdaneden');
            $durum = Input::get('durum');
            if($durum){ //silinecek
                try {
                    $kalibrasyon->durum=0;
                    $kalibrasyon->kullanici_id = NULL;
                    $kalibrasyon->kalibrasyontarih = NULL;
                    $kalibrasyon->save();
                    $hurdakayit = Hurda::where('kalibrasyon_id',$secilen)->first();
                    if($hurdakayit){
                        $hurdanedeni = HurdaNedeni::find($hurdakayit->hurdanedeni_id);
                        $hurdanedeni->kullanim--;
                        $hurdanedeni->save();
                        $depoteslim = DepoTeslim::find($hurdakayit->depoteslim_id);
                        if ($depoteslim->sayacsayisi > 1) {
                            $secilenlist = explode(',', $depoteslim->secilenler);
                            $silinecek = array($sayacgelen->id);
                            $depoteslim->secilenler = BackendController::getListeFark($secilenlist, $silinecek);
                            $depoteslim->sayacsayisi=count(explode(',',$depoteslim->secilenler));
                            $depoteslim->save();
                        } else {
                            $depoteslim->delete();
                        }
                    }

                    $sayacgelen->kalibrasyon=0;
                    $sayacgelen->teslimdurum=0;
                    $sayacgelen->sayacdurum=1;
                    $sayacgelen->beyanname=-2;
                    $sayacgelen->save();

                    $servistakip = ServisTakip::where('kalibrasyon_id', $kalibrasyon->id)->first();
                    if($servistakip) {
                        $servistakip->kalibrasyon_id = NULL;
                        $servistakip->kalibrasyontarihi = NULL;
                        if ($servistakip->onaylanmatarihi) {
                            $servistakip->durum = 5;
                            $servistakip->sonislemtarihi = $servistakip->onaylanmatarihi;
                        } else if ($servistakip->reddetmetarihi) {
                            $servistakip->durum = 6;
                            $servistakip->sonislemtarihi = $servistakip->onaylanmatarihi;
                        } else if ($servistakip->tekrarucrettarihi) {
                            $servistakip->durum = 7;
                            $servistakip->sonislemtarihi = $servistakip->tekrarucrettarihi;
                        } else if ($servistakip->gondermetarihi) {
                            $servistakip->durum = 4;
                            $servistakip->sonislemtarihi = $servistakip->gondermetarihi;
                        } else if ($servistakip->ucretlendirmetarihi) {
                            $servistakip->durum = 3;
                            $servistakip->sonislemtarihi = $servistakip->ucretlendirmetarihi;
                        } else if ($servistakip->arizakayittarihi) {
                            $servistakip->durum = 2;
                            $servistakip->sonislemtarihi = $servistakip->arizakayittarihi;
                        } else {
                            $servistakip->durum = 1;
                            $servistakip->sonislemtarihi = $servistakip->sayacgiristarihi;
                        }
                        $servistakip->save();

                        if (!$kalibrasyongrup->periyodik) {
                            $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
                            $arizakayit->rapordurum = -1;
                            $arizakayit->save();
                            $sayacyapilan = SayacYapilan::find($arizakayit->sayacyapilan_id);
                            $sayacuyari = SayacUyari::find($arizakayit->sayacuyari_id);
                            $arizakayiteski = ArizaKayitEski::where('arizakayit_id', $arizakayit->id)->first();
                            if ($arizakayiteski) {
                                $sayacyapilan->yapilanlar = $arizakayiteski->yapilanlar;
                                $sayacyapilan->save();
                                $sayacuyari->uyarilar = $arizakayiteski->uyarilar;
                                $sayacuyari->save();
                                $arizakayit->arizakayit_durum = $arizakayiteski->arizakayit_durum;
                                $arizakayit->save();
                                $arizakayiteski->delete();
                            }
                            $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                            $uretimyer = UretimYer::find($arizafiyat->uretimyer_id);
                            $parabirimi = null;
                            $parabirimi2 = null;
                            $fiyat = 0;
                            $fiyat2 = 0;
                            $indirim = 0;
                            $indirim2 = 0;
                            $ucretsizler = explode(',', $arizafiyat->ucretsiz);
                            $genelfiyatlar = explode(';', $arizafiyat->genel);
                            $ozelfiyatlar = explode(';', $arizafiyat->ozel);
                            $genelbirimler = explode(',', $arizafiyat->genelbirim);
                            $ozelbirimler = explode(',', $arizafiyat->ozelbirim);
                            $genelbirimlist = array();
                            $ozelbirimlist = array();
                            foreach ($genelbirimler as $birimid) {
                                $parabirimi = ParaBirimi::find($birimid);
                                array_push($genelbirimlist, $parabirimi);
                            }
                            foreach ($ozelbirimler as $birimid) {
                                $parabirimi = ParaBirimi::find($birimid);
                                array_push($ozelbirimlist, $parabirimi);
                            }
                            $genelbirim = BackendController::getGenelParabirimi();
                            $ozelbirim = ParaBirimi::find($uretimyer->parabirimi_id);
                            $kur = 1;
                            if ($genelbirim->id != $ozelbirim->id) {
                                if ($ozelbirim->id == 1) { //tl
                                    $kur = BackendController::getKurBilgisi($genelbirim->id, $arizafiyat->kurtarihi);
                                } else { //euro dolar sterln
                                    if ($genelbirim->id == 1) {
                                        $kur = 1 / BackendController::getKurBilgisi($ozelbirim->id, $arizafiyat->kurtarihi);
                                    } else {
                                        $kur = BackendController::getKurBilgisi($genelbirim->id, $arizafiyat->kurtarihi) / BackendController::getKurBilgisi($ozelbirim->id, $arizafiyat->kurtarihi);
                                    }
                                }
                            }
                            if ($arizafiyat->garanti) {
                                $fiyat = 0;
                                $fiyat2 = 0;
                            } else if ($arizafiyat->fiyatdurum) { //genel fiyatlar
                                $parabirimi = $genelbirim;
                                $parabirimi2 = null;
                                $index = 0;
                                foreach ($genelfiyatlar as $genelfiyat) {
                                    if ($ucretsizler[$index] != '1') {
                                        if ($parabirimi->id == $genelbirimler[$index]) {
                                            $fiyat += floatval($genelfiyat);
                                        } else if ($parabirimi2 == null || $parabirimi2->id == $genelbirimler[$index]) {
                                            $fiyat2 += floatval($genelfiyat);
                                            $parabirimi2 = $genelbirimler[$index];
                                        } else {
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                        }
                                    }
                                }
                                $fiyat *= $kur;
                                if ($parabirimi2->id == $ozelbirim->id) {
                                    $fiyat += $fiyat2;
                                    $fiyat2 = 0;
                                    $parabirimi2 = null;
                                }
                            } else { //ozel fiyatlar
                                $parabirimi = $ozelbirim;
                                $parabirimi2 = null;
                                $index = 0;
                                foreach ($ozelfiyatlar as $ozelfiyat) {
                                    if ($ucretsizler[$index] != '1') {
                                        if ($parabirimi->id == $ozelbirimler[$index]) {
                                            $fiyat += floatval($ozelfiyat);
                                        } else if ($parabirimi2 == null || $parabirimi2->id == $ozelbirimler[$index]) {
                                            $fiyat2 += floatval($ozelfiyat);
                                            $parabirimi2 = $ozelbirimler[$index];
                                        } else {
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                        }
                                    }
                                }
                                if ($parabirimi2->id == $ozelbirim->id) {
                                    $fiyat += $fiyat2;
                                    $fiyat2 = 0;
                                    $parabirimi2 = null;
                                }
                            }
                            if ($sayacgelen->fiyatlandirma) {
                                $arizafiyat->durum = 1;
                                $ucretlendirilen = Ucretlendirilen::find($servistakip->ucretlendirilen_id);
                                if ($ucretlendirilen) {
                                    if ($ucretlendirilen->parabirimi_id != $ozelbirim->id) {
                                        if ($ucretlendirilen->parabirimi_id == 1) { //tl
                                            $kur = BackendController::getKurBilgisi($ozelbirim->id, $ucretlendirilen->kurtarihi);
                                        } else { //euro dolar sterln
                                            if ($ozelbirim->id == 1) {
                                                $kur = 1 / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $ucretlendirilen->kurtarihi);
                                            } else {
                                                $kur = BackendController::getKurBilgisi($ozelbirim->id, $ucretlendirilen->kurtarihi) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $ucretlendirilen->kurtarihi);
                                            }
                                        }
                                        $fiyat *= $kur;
                                        if ($parabirimi2->id == $ucretlendirilen->parabirimi_id) {
                                            $fiyat += $fiyat2;
                                            $fiyat2 = 0;
                                            $parabirimi2->id = null;
                                        } else if ($parabirimi2->id != $ucretlendirilen->parabirimi2_id) {
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                        }
                                    } else {
                                        if ($parabirimi2 != null) {
                                            if ($ucretlendirilen->parabirimi2_id != $parabirimi2->id) {
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                            }
                                        }
                                    }
                                    if ($arizafiyat->indirim & $arizafiyat->indirimorani > 0) {
                                        $indirim = (($fiyat * floatval($arizafiyat->indirinorani)) / 100);
                                        $indirim2 = (($fiyat2 * floatval($arizafiyat->indirinorani)) / 100);
                                        $kdvsiztutar = $fiyat - $indirim;
                                        $kdvsiztutar2 = $fiyat2 - $indirim2;
                                    } else {
                                        $kdvsiztutar = $fiyat;
                                        $kdvsiztutar2 = $fiyat2;
                                    }
                                    $kdv = ($kdvsiztutar * 18) / 100;
                                    $kdv2 = ($kdvsiztutar2 * 18) / 100;
                                    $toplamtutar = $kdvsiztutar + $kdv;
                                    $toplamtutar = round(($toplamtutar) * 2) / 2;
                                    $toplamtutar2 = $kdvsiztutar2 + $kdv2;
                                    $toplamtutar2 = round(($toplamtutar2) * 2) / 2;
                                    $ucretlendirilen->fiyat = round(($ucretlendirilen->fiyat + $toplamtutar) * 2) / 2;
                                    $ucretlendirilen->fiyat2 = round(($ucretlendirilen->fiyat2 + $toplamtutar2) * 2) / 2;
                                    $ucretlendirilen->save();
                                    if (!$servistakip->onaylanan_id) {
                                        BackendController::HatirlatmaEkle(5, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                    }
                                } else {
                                    DB::rollBack();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Sayaca Ait Fiyatlandırma Bulunamadı!', 'type' => 'error'));
                                }
                            } else {
                                $arizafiyat->durum = 0;
                                if ($arizafiyat->indirim & $arizafiyat->indirimorani > 0) {
                                    $indirim = (($fiyat * floatval($arizafiyat->indirinorani)) / 100);
                                    $indirim2 = (($fiyat2 * floatval($arizafiyat->indirinorani)) / 100);
                                    $kdvsiztutar = $fiyat - $indirim;
                                    $kdvsiztutar2 = $fiyat2 - $indirim2;
                                } else {
                                    $kdvsiztutar = $fiyat;
                                    $kdvsiztutar2 = $fiyat2;
                                }
                                $kdv = ($kdvsiztutar * 18) / 100;
                                $kdv2 = ($kdvsiztutar2 * 18) / 100;
                                $toplamtutar = $kdvsiztutar + $kdv;
                                $toplamtutar = round(($toplamtutar) * 2) / 2;
                                $toplamtutar2 = $kdvsiztutar2 + $kdv2;
                                $toplamtutar2 = round(($toplamtutar2) * 2) / 2;
                                BackendController::HatirlatmaEkle(4, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                            }
                            $arizafiyat->fiyat = $fiyat;
                            $arizafiyat->fiyat2 = $fiyat2;
                            $arizafiyat->indirim = $indirim;
                            $arizafiyat->indirim2 = $indirim2;
                            $arizafiyat->tutar = $kdvsiztutar;
                            $arizafiyat->tutar2 = $kdvsiztutar2;
                            $arizafiyat->kdv = $kdv;
                            $arizafiyat->kdv2 = $kdv2;
                            $arizafiyat->toplamtutar = $toplamtutar;
                            $arizafiyat->toplamtutar2 = $toplamtutar2;
                            $arizafiyat->parabirimi_id = $ozelbirim->id;
                            $arizafiyat->parabirimi2_id = $parabirimi2 == null ? null : $parabirimi2->id;
                            $arizafiyat->save();
                        }
                    }
                    $hurdakayit->delete();

                    BackendController::HatirlatmaGeriAl(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                    BackendController::HatirlatmaSil(9, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                    BackendController::BildirimGeriAl(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                    BackendController::BildirimGeriAl(10, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                    BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-cog', $kalibrasyongrup->id . ' Numaralı Kalibrasyon Grubuna Ait Hurda Sayaç Bilgisi Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Kalibrasyon Grup Numarası:' . $kalibrasyongrup->id);
                    DB::commit();
                    return Redirect::to('kalibrasyon/kalibrasyondetay/' . $id)->with(array('mesaj' => 'true', 'title' => 'Hurda Sayaç Bilgisi Silindi', 'text' => 'Hurda Sayaç  Bilgisi Başarıyla Silindi', 'type' => 'success'));
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Hurda Sayaç  Bilgisi Silinemedi', 'text' => 'Hurda Sayaç  Bilgileri Silinirken Hata Oluştu.', 'type' => 'error'));
                }
            }else{ //güncellenecek
                try {
                    $kalibrasyon->kullanici_id = Auth::user()->id;
                    $kalibrasyon->kalibrasyontarih = date('Y-m-d H:i:s');
                    $kalibrasyon->save();
                    $sayacgelen = SayacGelen::find($kalibrasyon->sayacgelen_id);
                    $hurdanedeni = HurdaNedeni::find($hurdaneden);
                    $hurdakayit = Hurda::where('kalibrasyon_id',$secilen)->first();
                    if($hurdakayit->hurdanedeni_id!=$hurdanedeni->id){
                        $eskihurdanedeni = HurdaNedeni::find($hurdakayit->hurdanedeni_id);
                        $eskihurdanedeni->kullanim--;
                        $eskihurdanedeni->save();
                        $hurdanedeni->kullanim++;
                        $hurdanedeni->save();
                    }
                    $hurdakayit->hurdanedeni_id = $hurdanedeni->id;
                    $hurdakayit->hurdatarihi = date('Y-m-d H:i:s');
                    $hurdakayit->kullanici_id = Auth::user()->id;
                    $hurdakayit->save();
                    $servistakip = ServisTakip::where('serino', $sayacgelen->serino)->where('depogelen_id', $sayacgelen->depogelen_id)->first();
                    $servistakip->kalibrasyon_id = $kalibrasyon->id;
                    $servistakip->kalibrasyontarihi = $kalibrasyon->kalibrasyontarih;
                    $servistakip->sonislemtarihi = $kalibrasyon->kalibrasyontarih;
                    $servistakip->save();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Girilen Sayaç için Hurdaya Ayırma Bilgisi Kayıdı Yapılamadı', 'type' => 'error'));
                }
                try {
                    BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-cog', $kalibrasyongrup->id . ' Numaralı Kalibrasyon Grubuna Ait Hurda Sayaç Bilgisi Güncellendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Kalibrasyon Grup Numarası:' . $kalibrasyongrup->id);
                    DB::commit();
                    return Redirect::to('kalibrasyon/kalibrasyondetay/' . $id)->with(array('mesaj' => 'true', 'title' => 'Hurda Sayaç Bilgisi Güncellendi', 'text' => 'Hurda Sayaç  Bilgisi Başarıyla Güncellendi', 'type' => 'success'));
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Hurda Sayaç  Bilgisi Güncellenemedi', 'text' => 'Hurda Sayaç  Bilgileri Güncellenirken Hata Oluştu.', 'type' => 'error'));
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Hurda Sayaç  Bilgisi Güncellenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function postKalibrasyonexcel(){
        try {
            If (Input::hasFile('file')) {
                DB::beginTransaction();
                $dosya = Input::file('file');
                $noktasayi=Input::get('kayitnoktasayi');
                $basarili=0;$basarisiz=0;
                $hf2 = Input::get('kayithf2');
                $hf3 = Input::get('kayithf3');
                $hf32 = Input::get('kayithf32');
                $uzanti = $dosya->getClientOriginalExtension();
                $isim = Str::slug(str_random(5)) . '.' . $uzanti;
                $dosya->move('assets/temp/', $isim);
                $path = 'assets/temp/' . $isim;
                $errors = array();
                $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {})->get();
                File::delete($path);
                $noktadurum = 0;
                if(!empty($data) && $data->count()) {
                    try {
                        foreach ($data as $key => $value) {
                            if(!$noktadurum){
                                switch ($noktasayi) {
                                    case 3:
                                        if (isset($value->nokta1) && isset($value->nokta2) && isset($value->nokta3))
                                            $noktadurum = 1;
                                        break;
                                    case 5:
                                        if (isset($value->nokta1) && isset($value->nokta2) && isset($value->nokta3) && isset($value->nokta4) && isset($value->nokta5))
                                            $noktadurum = 1;
                                        if ($hf2) {
                                            $noktadurum = 0;
                                            if (isset($value->hf2nokta1) && isset($value->hf2nokta2) && isset($value->hf2nokta3) && isset($value->hf2nokta4) && isset($value->hf2nokta5))
                                                $noktadurum = 1;
                                        }
                                        if ($hf3) {
                                            $noktadurum = 0;
                                            if (isset($value->hf3nokta1) && isset($value->hf3nokta2) && isset($value->hf3nokta3) && isset($value->hf3nokta4) && isset($value->hf3nokta5))
                                                $noktadurum = 1;
                                        }
                                        if ($hf32) {
                                            $noktadurum = 0;
                                            if (isset($value->hf32nokta1) && isset($value->hf32nokta2) && isset($value->hf32nokta3) && isset($value->hf32nokta4) && isset($value->hf32nokta5))
                                                $noktadurum = 1;
                                        }
                                        break;
                                    case 6:
                                        if (isset($value->nokta1) && isset($value->nokta2) && isset($value->nokta3) && isset($value->nokta4) && isset($value->nokta5) && isset($value->nokta6))
                                            $noktadurum = 1;
                                        if ($hf2) {
                                            $noktadurum = 0;
                                            if (isset($value->hf2nokta1) && isset($value->hf2nokta2) && isset($value->hf2nokta3) && isset($value->hf2nokta4) && isset($value->hf2nokta5) && isset($value->hf2nokta6))
                                                $noktadurum = 1;
                                        }
                                        if ($hf3) {
                                            $noktadurum = 0;
                                            if (isset($value->hf3nokta1) && isset($value->hf3nokta2) && isset($value->hf3nokta3) && isset($value->hf3nokta4) && isset($value->hf3nokta5) && isset($value->hf3nokta6))
                                                $noktadurum = 1;
                                        }
                                        if ($hf32) {
                                            $noktadurum = 0;
                                            if (isset($value->hf32nokta1) && isset($value->hf32nokta2) && isset($value->hf32nokta3) && isset($value->hf32nokta4) && isset($value->hf32nokta5) && isset($value->hf32nokta6))
                                                $noktadurum = 1;
                                        }
                                        break;
                                    case 7:
                                        if (isset($value->nokta1) && isset($value->nokta2) && isset($value->nokta3) && isset($value->nokta4) && isset($value->nokta5) && isset($value->nokta6) && isset($value->nokta7))
                                            $noktadurum = 1;
                                        if ($hf2) {
                                            $noktadurum = 0;
                                            if (isset($value->hf2nokta1) && isset($value->hf2nokta2) && isset($value->hf2nokta3) && isset($value->hf2nokta4) && isset($value->hf2nokta5) && isset($value->hf2nokta6) && isset($value->hf2nokta7))
                                                $noktadurum = 1;
                                        }
                                        if ($hf3) {
                                            $noktadurum = 0;
                                            if (isset($value->hf3nokta1) && isset($value->hf3nokta2) && isset($value->hf3nokta3) && isset($value->hf3nokta4) && isset($value->hf3nokta5) && isset($value->hf3nokta6) && isset($value->hf3nokta7))
                                                $noktadurum = 1;
                                        }
                                        if ($hf32) {
                                            $noktadurum = 0;
                                            if (isset($value->hf32nokta1) && isset($value->hf32nokta2) && isset($value->hf32nokta3) && isset($value->hf32nokta4) && isset($value->hf32nokta5) && isset($value->hf32nokta6) && isset($value->hf32nokta7))
                                                $noktadurum = 1;
                                        }
                                        break;
                                }
                            }
                            if(!$noktadurum)
                                return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Kaydetme Hatası', 'text' => 'Kalibrasyon için bilgi alınacak noktalar uygun değil'));
                            $serino = $value->sayac_no;
                            $kalibrasyon = Kalibrasyon::where('kalibrasyon_seri', $serino)->where('durum', 0)->first();
                            if ($kalibrasyon) {
                                $kalibrasyongrup = KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
                                $sayacadi = SayacAdi::find($kalibrasyon->sayacadi_id);
                                $kalibrasyonstandart = KalibrasyonStandart::where('kalibrasyontipi_id', $sayacadi->kalibrasyontipi_id)->where('noktasayisi', $noktasayi)->first();
                                $kalibrasyon->kalibrasyonstandart_id = $kalibrasyonstandart->id;
                                $kalibrasyon->sira = 1;
                                $istasyonlar = Istasyon::all();
                                foreach ($istasyonlar as $istasyon) {
                                    $sayacadlari = explode(',', $istasyon->sayacadlari);
                                    if (in_array($sayacadi->id, $sayacadlari)) {
                                        $kalibrasyon->istasyon_id = $istasyon->id;
                                        break;
                                    }
                                }
                                switch ($noktasayi) {
                                    case 3:
                                        $nokta1 = round($value->nokta1,2);
                                        $nokta2 = round($value->nokta2,2);
                                        $nokta3 = round($value->nokta3,2);
                                        $kalibrasyon->nokta1sapma = $kalibrasyonstandart->sapma1;
                                        $kalibrasyon->nokta2sapma = $kalibrasyonstandart->sapma2;
                                        $kalibrasyon->nokta3sapma = $kalibrasyonstandart->sapma3;
                                        $kalibrasyon->sonuc1 = $nokta1;
                                        $kalibrasyon->sonuc2 = $nokta2;
                                        $kalibrasyon->sonuc3 = $nokta3;
                                        if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                            ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                            ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $basarisiz++;
                                        } else {
                                            $kalibrasyon->durum = 1;
                                            $basarili++;
                                        }
                                        $kalibrasyon->kullanici_id = Auth::user()->id;
                                        $kalibrasyon->kalibrasyontarih = date('Y-m-d H:i:s');
                                        $kalibrasyon->save();
                                        break;
                                    case 5:
                                        $flag = 0;
                                        $nokta1 = round($value->nokta1,2);
                                        $nokta2 = round($value->nokta2,2);
                                        $nokta3 = round($value->nokta3,2);
                                        $nokta4 = round($value->nokta4,2);
                                        $nokta5 = round($value->nokta5,2);
                                        $kalibrasyon->nokta1sapma = $kalibrasyonstandart->sapma1;
                                        $kalibrasyon->nokta2sapma = $kalibrasyonstandart->sapma2;
                                        $kalibrasyon->nokta3sapma = $kalibrasyonstandart->sapma3;
                                        $kalibrasyon->nokta4sapma = $kalibrasyonstandart->sapma4;
                                        $kalibrasyon->nokta5sapma = $kalibrasyonstandart->sapma5;
                                        $kalibrasyon->sonuc1 = $nokta1;
                                        $kalibrasyon->sonuc2 = $nokta2;
                                        $kalibrasyon->sonuc3 = $nokta3;
                                        $kalibrasyon->sonuc4 = $nokta4;
                                        $kalibrasyon->sonuc5 = $nokta5;
                                        if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                            ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                            ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                            ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                            ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $flag = 1;
                                        } else {
                                            $kalibrasyon->durum = 1;
                                        }
                                        if ($hf2) {
                                            $nokta1 = round($value->hf2nokta1,2);
                                            $nokta2 = round($value->hf2nokta2,2);
                                            $nokta3 = round($value->hf2nokta3,2);
                                            $nokta4 = round($value->hf2nokta4,2);
                                            $nokta5 = round($value->hf2nokta5,2);
                                            $kalibrasyon->hf2 = 1;
                                            $kalibrasyon->hf2nokta1sapma = $kalibrasyonstandart->sapma1;
                                            $kalibrasyon->hf2nokta2sapma = $kalibrasyonstandart->sapma2;
                                            $kalibrasyon->hf2nokta3sapma = $kalibrasyonstandart->sapma3;
                                            $kalibrasyon->hf2nokta4sapma = $kalibrasyonstandart->sapma4;
                                            $kalibrasyon->hf2nokta5sapma = $kalibrasyonstandart->sapma5;
                                            $kalibrasyon->hf2sonuc1 = $nokta1;
                                            $kalibrasyon->hf2sonuc2 = $nokta2;
                                            $kalibrasyon->hf2sonuc3 = $nokta3;
                                            $kalibrasyon->hf2sonuc4 = $nokta4;
                                            $kalibrasyon->hf2sonuc5 = $nokta5;
                                            if (!$flag) {
                                                if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                                    ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                                    ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                                    ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                                    ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5))
                                                ) {
                                                    $kalibrasyon->durum = 2; //başarısız
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        if ($hf3) {
                                            $nokta1 = round($value->hf3nokta1,2);
                                            $nokta2 = round($value->hf3nokta2,2);
                                            $nokta3 = round($value->hf3nokta3,2);
                                            $nokta4 = round($value->hf3nokta4,2);
                                            $nokta5 = round($value->hf3nokta5,2);
                                            $kalibrasyon->hf3 = 1;
                                            $kalibrasyon->hf3nokta1sapma = $kalibrasyonstandart->sapma1;
                                            $kalibrasyon->hf3nokta2sapma = $kalibrasyonstandart->sapma2;
                                            $kalibrasyon->hf3nokta3sapma = $kalibrasyonstandart->sapma3;
                                            $kalibrasyon->hf3nokta4sapma = $kalibrasyonstandart->sapma4;
                                            $kalibrasyon->hf3nokta5sapma = $kalibrasyonstandart->sapma5;
                                            $kalibrasyon->hf3sonuc1 = $nokta1;
                                            $kalibrasyon->hf3sonuc2 = $nokta2;
                                            $kalibrasyon->hf3sonuc3 = $nokta3;
                                            $kalibrasyon->hf3sonuc4 = $nokta4;
                                            $kalibrasyon->hf3sonuc5 = $nokta5;
                                            if (!$flag) {
                                                if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                                    ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                                    ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                                    ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                                    ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5))
                                                ) {
                                                    $kalibrasyon->durum = 2; //başarısız
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        if ($hf32) {
                                            $nokta1 = round($value->hf32nokta1,2);
                                            $nokta2 = round($value->hf32nokta2,2);
                                            $nokta3 = round($value->hf32nokta3,2);
                                            $nokta4 = round($value->hf32nokta4,2);
                                            $nokta5 = round($value->hf32nokta5,2);
                                            $kalibrasyon->hf32 = 1;
                                            $kalibrasyon->hf32nokta1sapma = $kalibrasyonstandart->sapma1;
                                            $kalibrasyon->hf32nokta2sapma = $kalibrasyonstandart->sapma2;
                                            $kalibrasyon->hf32nokta3sapma = $kalibrasyonstandart->sapma3;
                                            $kalibrasyon->hf32nokta4sapma = $kalibrasyonstandart->sapma4;
                                            $kalibrasyon->hf32nokta5sapma = $kalibrasyonstandart->sapma5;
                                            $kalibrasyon->hf32sonuc1 = $nokta1;
                                            $kalibrasyon->hf32sonuc2 = $nokta2;
                                            $kalibrasyon->hf32sonuc3 = $nokta3;
                                            $kalibrasyon->hf32sonuc4 = $nokta4;
                                            $kalibrasyon->hf32sonuc5 = $nokta5;
                                            if (!$flag) {
                                                if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                                    ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                                    ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                                    ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                                    ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5))
                                                ) {
                                                    $kalibrasyon->durum = 2; //başarısız
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        if ($flag == 0) {
                                            $kalibrasyon->durum = 1;
                                            $basarili++;
                                        } else {
                                            $basarisiz++;
                                        }
                                        $kalibrasyon->kullanici_id = Auth::user()->id;
                                        $kalibrasyon->kalibrasyontarih = date('Y-m-d H:i:s');
                                        $kalibrasyon->save();
                                        break;
                                    case 6:
                                        $flag = 0;
                                        $nokta1 = round($value->nokta1,2);
                                        $nokta2 = round($value->nokta2,2);
                                        $nokta3 = round($value->nokta3,2);
                                        $nokta4 = round($value->nokta4,2);
                                        $nokta5 = round($value->nokta5,2);
                                        $nokta6 = round($value->nokta6,2);
                                        $kalibrasyon->nokta1sapma = $kalibrasyonstandart->sapma1;
                                        $kalibrasyon->nokta2sapma = $kalibrasyonstandart->sapma2;
                                        $kalibrasyon->nokta3sapma = $kalibrasyonstandart->sapma3;
                                        $kalibrasyon->nokta4sapma = $kalibrasyonstandart->sapma4;
                                        $kalibrasyon->nokta5sapma = $kalibrasyonstandart->sapma5;
                                        $kalibrasyon->nokta6sapma = $kalibrasyonstandart->sapma6;
                                        $kalibrasyon->sonuc1 = $nokta1;
                                        $kalibrasyon->sonuc2 = $nokta2;
                                        $kalibrasyon->sonuc3 = $nokta3;
                                        $kalibrasyon->sonuc4 = $nokta4;
                                        $kalibrasyon->sonuc5 = $nokta5;
                                        $kalibrasyon->sonuc6 = $nokta6;
                                        if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                            ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                            ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                            ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                            ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5)) ||
                                            ($nokta6 > $kalibrasyonstandart->sapma6) || ($nokta6 < -($kalibrasyonstandart->sapma6))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $flag = 1;
                                        } else {
                                            $kalibrasyon->durum = 1;
                                        }
                                        if ($hf2) {
                                            $nokta1 = round($value->hf2nokta1,2);
                                            $nokta2 = round($value->hf2nokta2,2);
                                            $nokta3 = round($value->hf2nokta3,2);
                                            $nokta4 = round($value->hf2nokta4,2);
                                            $nokta5 = round($value->hf2nokta5,2);
                                            $nokta6 = round($value->hf2nokta6,2);
                                            $kalibrasyon->hf2 = 1;
                                            $kalibrasyon->hf2nokta1sapma = $kalibrasyonstandart->sapma1;
                                            $kalibrasyon->hf2nokta2sapma = $kalibrasyonstandart->sapma2;
                                            $kalibrasyon->hf2nokta3sapma = $kalibrasyonstandart->sapma3;
                                            $kalibrasyon->hf2nokta4sapma = $kalibrasyonstandart->sapma4;
                                            $kalibrasyon->hf2nokta5sapma = $kalibrasyonstandart->sapma5;
                                            $kalibrasyon->hf2nokta6sapma = $kalibrasyonstandart->sapma6;
                                            $kalibrasyon->hf2sonuc1 = $nokta1;
                                            $kalibrasyon->hf2sonuc2 = $nokta2;
                                            $kalibrasyon->hf2sonuc3 = $nokta3;
                                            $kalibrasyon->hf2sonuc4 = $nokta4;
                                            $kalibrasyon->hf2sonuc5 = $nokta5;
                                            $kalibrasyon->hf2sonuc6 = $nokta6;
                                            if (!$flag) {
                                                if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                                    ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                                    ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                                    ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                                    ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5)) ||
                                                    ($nokta6 > $kalibrasyonstandart->sapma6) || ($nokta6 < -($kalibrasyonstandart->sapma6))
                                                ) {
                                                    $kalibrasyon->durum = 2; //başarısız
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        if ($hf3) {
                                            $nokta1 = round($value->hf3nokta1,2);
                                            $nokta2 = round($value->hf3nokta2,2);
                                            $nokta3 = round($value->hf3nokta3,2);
                                            $nokta4 = round($value->hf3nokta4,2);
                                            $nokta5 = round($value->hf3nokta5,2);
                                            $nokta6 = round($value->hf3nokta6,2);
                                            $kalibrasyon->hf3 = 1;
                                            $kalibrasyon->hf3nokta1sapma = $kalibrasyonstandart->sapma1;
                                            $kalibrasyon->hf3nokta2sapma = $kalibrasyonstandart->sapma2;
                                            $kalibrasyon->hf3nokta3sapma = $kalibrasyonstandart->sapma3;
                                            $kalibrasyon->hf3nokta4sapma = $kalibrasyonstandart->sapma4;
                                            $kalibrasyon->hf3nokta5sapma = $kalibrasyonstandart->sapma5;
                                            $kalibrasyon->hf3nokta6sapma = $kalibrasyonstandart->sapma6;
                                            $kalibrasyon->hf3sonuc1 = $nokta1;
                                            $kalibrasyon->hf3sonuc2 = $nokta2;
                                            $kalibrasyon->hf3sonuc3 = $nokta3;
                                            $kalibrasyon->hf3sonuc4 = $nokta4;
                                            $kalibrasyon->hf3sonuc5 = $nokta5;
                                            $kalibrasyon->hf3sonuc6 = $nokta6;
                                            if (!$flag) {
                                                if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                                    ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                                    ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                                    ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                                    ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5)) ||
                                                    ($nokta6 > $kalibrasyonstandart->sapma6) || ($nokta6 < -($kalibrasyonstandart->sapma6))
                                                ) {
                                                    $kalibrasyon->durum = 2; //başarısız
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        if ($hf32) {
                                            $nokta1 = round($value->hf32nokta1,2);
                                            $nokta2 = round($value->hf32nokta2,2);
                                            $nokta3 = round($value->hf32nokta3,2);
                                            $nokta4 = round($value->hf32nokta4,2);
                                            $nokta5 = round($value->hf32nokta5,2);
                                            $nokta6 = round($value->hf32nokta6,2);
                                            $kalibrasyon->hf32 = 1;
                                            $kalibrasyon->hf32nokta1sapma = $kalibrasyonstandart->sapma1;
                                            $kalibrasyon->hf32nokta2sapma = $kalibrasyonstandart->sapma2;
                                            $kalibrasyon->hf32nokta3sapma = $kalibrasyonstandart->sapma3;
                                            $kalibrasyon->hf32nokta4sapma = $kalibrasyonstandart->sapma4;
                                            $kalibrasyon->hf32nokta5sapma = $kalibrasyonstandart->sapma5;
                                            $kalibrasyon->hf32nokta6sapma = $kalibrasyonstandart->sapma6;
                                            $kalibrasyon->hf32sonuc1 = $nokta1;
                                            $kalibrasyon->hf32sonuc2 = $nokta2;
                                            $kalibrasyon->hf32sonuc3 = $nokta3;
                                            $kalibrasyon->hf32sonuc4 = $nokta4;
                                            $kalibrasyon->hf32sonuc5 = $nokta5;
                                            $kalibrasyon->hf32sonuc6 = $nokta6;
                                            if (!$flag) {
                                                if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                                    ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                                    ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                                    ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                                    ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5)) ||
                                                    ($nokta6 > $kalibrasyonstandart->sapma6) || ($nokta6 < -($kalibrasyonstandart->sapma6))
                                                ) {
                                                    $kalibrasyon->durum = 2; //başarısız
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        if ($flag == 0) {
                                            $kalibrasyon->durum = 1;
                                            $basarili++;
                                        } else {
                                            $basarisiz++;
                                        }
                                        $kalibrasyon->kullanici_id = Auth::user()->id;
                                        $kalibrasyon->kalibrasyontarih = date('Y-m-d H:i:s');
                                        $kalibrasyon->save();
                                        break;
                                    case 7:
                                        $flag = 0;
                                        $nokta1 = round($value->nokta1,2);
                                        $nokta2 = round($value->nokta2,2);
                                        $nokta3 = round($value->nokta3,2);
                                        $nokta4 = round($value->nokta4,2);
                                        $nokta5 = round($value->nokta5,2);
                                        $nokta6 = round($value->nokta6,2);
                                        $nokta7 = round($value->nokta7,2);
                                        $kalibrasyon->nokta1sapma = $kalibrasyonstandart->sapma1;
                                        $kalibrasyon->nokta2sapma = $kalibrasyonstandart->sapma2;
                                        $kalibrasyon->nokta3sapma = $kalibrasyonstandart->sapma3;
                                        $kalibrasyon->nokta4sapma = $kalibrasyonstandart->sapma4;
                                        $kalibrasyon->nokta5sapma = $kalibrasyonstandart->sapma5;
                                        $kalibrasyon->nokta6sapma = $kalibrasyonstandart->sapma6;
                                        $kalibrasyon->nokta7sapma = $kalibrasyonstandart->sapma7;
                                        $kalibrasyon->sonuc1 = $nokta1;
                                        $kalibrasyon->sonuc2 = $nokta2;
                                        $kalibrasyon->sonuc3 = $nokta3;
                                        $kalibrasyon->sonuc4 = $nokta4;
                                        $kalibrasyon->sonuc5 = $nokta5;
                                        $kalibrasyon->sonuc6 = $nokta6;
                                        $kalibrasyon->sonuc7 = $nokta7;
                                        if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                            ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                            ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                            ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                            ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5)) ||
                                            ($nokta6 > $kalibrasyonstandart->sapma6) || ($nokta6 < -($kalibrasyonstandart->sapma6)) ||
                                            ($nokta7 > $kalibrasyonstandart->sapma7) || ($nokta7 < -($kalibrasyonstandart->sapma7))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $flag = 1;
                                        } else {
                                            $kalibrasyon->durum = 1;
                                        }
                                        if ($hf2) {
                                            $nokta1 = round($value->hf2nokta1,2);
                                            $nokta2 = round($value->hf2nokta2,2);
                                            $nokta3 = round($value->hf2nokta3,2);
                                            $nokta4 = round($value->hf2nokta4,2);
                                            $nokta5 = round($value->hf2nokta5,2);
                                            $nokta6 = round($value->hf2nokta6,2);
                                            $nokta7 = round($value->hf2nokta7,2);
                                            $kalibrasyon->hf2 = 1;
                                            $kalibrasyon->hf2nokta1sapma = $kalibrasyonstandart->sapma1;
                                            $kalibrasyon->hf2nokta2sapma = $kalibrasyonstandart->sapma2;
                                            $kalibrasyon->hf2nokta3sapma = $kalibrasyonstandart->sapma3;
                                            $kalibrasyon->hf2nokta4sapma = $kalibrasyonstandart->sapma4;
                                            $kalibrasyon->hf2nokta5sapma = $kalibrasyonstandart->sapma5;
                                            $kalibrasyon->hf2nokta6sapma = $kalibrasyonstandart->sapma6;
                                            $kalibrasyon->hf2nokta7sapma = $kalibrasyonstandart->sapma7;
                                            $kalibrasyon->hf2sonuc1 = $nokta1;
                                            $kalibrasyon->hf2sonuc2 = $nokta2;
                                            $kalibrasyon->hf2sonuc3 = $nokta3;
                                            $kalibrasyon->hf2sonuc4 = $nokta4;
                                            $kalibrasyon->hf2sonuc5 = $nokta5;
                                            $kalibrasyon->hf2sonuc6 = $nokta6;
                                            $kalibrasyon->hf2sonuc7 = $nokta7;
                                            if (!$flag) {
                                                if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                                    ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                                    ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                                    ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                                    ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5)) ||
                                                    ($nokta6 > $kalibrasyonstandart->sapma6) || ($nokta6 < -($kalibrasyonstandart->sapma6)) ||
                                                    ($nokta7 > $kalibrasyonstandart->sapma7) || ($nokta7 < -($kalibrasyonstandart->sapma7))
                                                ) {
                                                    $kalibrasyon->durum = 2; //başarısız
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        if ($hf3) {
                                            $nokta1 = round($value->hf3nokta1,2);
                                            $nokta2 = round($value->hf3nokta2,2);
                                            $nokta3 = round($value->hf3nokta3,2);
                                            $nokta4 = round($value->hf3nokta4,2);
                                            $nokta5 = round($value->hf3nokta5,2);
                                            $nokta6 = round($value->hf3nokta6,2);
                                            $nokta7 = round($value->hf3nokta7,2);
                                            $kalibrasyon->hf3 = 1;
                                            $kalibrasyon->hf3nokta1sapma = $kalibrasyonstandart->sapma1;
                                            $kalibrasyon->hf3nokta2sapma = $kalibrasyonstandart->sapma2;
                                            $kalibrasyon->hf3nokta3sapma = $kalibrasyonstandart->sapma3;
                                            $kalibrasyon->hf3nokta4sapma = $kalibrasyonstandart->sapma4;
                                            $kalibrasyon->hf3nokta5sapma = $kalibrasyonstandart->sapma5;
                                            $kalibrasyon->hf3nokta6sapma = $kalibrasyonstandart->sapma6;
                                            $kalibrasyon->hf3nokta7sapma = $kalibrasyonstandart->sapma7;
                                            $kalibrasyon->hf3sonuc1 = $nokta1;
                                            $kalibrasyon->hf3sonuc2 = $nokta2;
                                            $kalibrasyon->hf3sonuc3 = $nokta3;
                                            $kalibrasyon->hf3sonuc4 = $nokta4;
                                            $kalibrasyon->hf3sonuc5 = $nokta5;
                                            $kalibrasyon->hf3sonuc6 = $nokta6;
                                            $kalibrasyon->hf3sonuc7 = $nokta7;
                                            if (!$flag) {
                                                if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                                    ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                                    ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                                    ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                                    ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5)) ||
                                                    ($nokta6 > $kalibrasyonstandart->sapma6) || ($nokta6 < -($kalibrasyonstandart->sapma6)) ||
                                                    ($nokta7 > $kalibrasyonstandart->sapma7) || ($nokta7 < -($kalibrasyonstandart->sapma7))
                                                ) {
                                                    $kalibrasyon->durum = 2; //başarısız
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        if ($hf32) {
                                            $nokta1 = round($value->hf32nokta1,2);
                                            $nokta2 = round($value->hf32nokta2,2);
                                            $nokta3 = round($value->hf32nokta3,2);
                                            $nokta4 = round($value->hf32nokta4,2);
                                            $nokta5 = round($value->hf32nokta5,2);
                                            $nokta6 = round($value->hf32nokta6,2);
                                            $nokta7 = round($value->hf32nokta7,2);
                                            $kalibrasyon->hf32 = 1;
                                            $kalibrasyon->hf32nokta1sapma = $kalibrasyonstandart->sapma1;
                                            $kalibrasyon->hf32nokta2sapma = $kalibrasyonstandart->sapma2;
                                            $kalibrasyon->hf32nokta3sapma = $kalibrasyonstandart->sapma3;
                                            $kalibrasyon->hf32nokta4sapma = $kalibrasyonstandart->sapma4;
                                            $kalibrasyon->hf32nokta5sapma = $kalibrasyonstandart->sapma5;
                                            $kalibrasyon->hf32nokta6sapma = $kalibrasyonstandart->sapma6;
                                            $kalibrasyon->hf32nokta7sapma = $kalibrasyonstandart->sapma7;
                                            $kalibrasyon->hf32sonuc1 = $nokta1;
                                            $kalibrasyon->hf32sonuc2 = $nokta2;
                                            $kalibrasyon->hf32sonuc3 = $nokta3;
                                            $kalibrasyon->hf32sonuc4 = $nokta4;
                                            $kalibrasyon->hf32sonuc5 = $nokta5;
                                            $kalibrasyon->hf32sonuc6 = $nokta6;
                                            $kalibrasyon->hf32sonuc7 = $nokta7;
                                            if (!$flag) {
                                                if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                                    ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                                    ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                                    ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                                    ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5)) ||
                                                    ($nokta6 > $kalibrasyonstandart->sapma6) || ($nokta6 < -($kalibrasyonstandart->sapma6)) ||
                                                    ($nokta7 > $kalibrasyonstandart->sapma7) || ($nokta7 < -($kalibrasyonstandart->sapma7))
                                                ) {
                                                    $kalibrasyon->durum = 2; //başarısız
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        if ($flag == 0) {
                                            $kalibrasyon->durum = 1;
                                            $basarili++;
                                        } else {
                                            $basarisiz++;
                                        }
                                        $kalibrasyon->kullanici_id = Auth::user()->id;
                                        $kalibrasyon->kalibrasyontarih = date('Y-m-d H:i:s');
                                        $kalibrasyon->save();
                                        break;
                                }
                                if ($kalibrasyon->durum == 1) { //kalibrasyon geçtiyse depo teslime aktarılacak
                                    try {
                                        $sayacgelen = SayacGelen::find($kalibrasyon->sayacgelen_id);
                                        $sayacgelen->kalibrasyon = 1;
                                        $sayacgelen->beyanname = $sayacgelen->beyanname==-2 ? 0 : $sayacgelen->beyanname;
                                        $sayacgelen->save();
                                        $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                        $uretimyer=UretimYer::find($servistakip->uretimyer_id);
                                        $servistakip->durum = 8;
                                        $servistakip->kalibrasyontarihi = date('Y-m-d H:i:s');
                                        $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                                        $servistakip->kullanici_id = Auth::user()->id;
                                        $servistakip->save();
                                        if(!$kalibrasyongrup->periyodik){ //periyodik bakımında arıza kayıdı yapılmıyor
                                            $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
                                            if($arizakayit->arizakayit_durum==4) //şikayetli sayaç ise
                                                $arizakayit->rapordurum = -1;
                                            else
                                                $arizakayit->rapordurum = 0 ;
                                            $arizakayit->save();
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Bilgi Güncelleme Hatası', 'text' => 'Kalibrasyon yapılan sayaçların sayaç gelen ve servistakip bilgisi güncellenemedi'));
                                    }
                                    try {
                                        $flag = 0;
                                        if($sayacgelen->musterionay || $kalibrasyongrup->periyodik) {
                                            $sayacgelen->teslimdurum=1;
                                            $sayacgelen->save();
                                            if($kalibrasyongrup->periyodik){
                                                $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                    ->where('depodurum', 0)->where('tipi', 0)->where('periyodik', $kalibrasyongrup->periyodik)->where('subegonderim',0)->first();
                                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                {
                                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                                    if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                        $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                        $depoteslim->sayacsayisi += 1;
                                                        $depoteslim->save();
                                                    }else{
                                                        $flag = 1;
                                                    }
                                                } else { //yeni depo teslimatı yapılacak
                                                    $depoteslim = new DepoTeslim;
                                                    $depoteslim->servis_id = $sayacgelen->servis_id;
                                                    $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                    $depoteslim->secilenler = $sayacgelen->id;
                                                    $depoteslim->sayacsayisi = 1;
                                                    $depoteslim->depodurum = 0;
                                                    $depoteslim->periyodik = $kalibrasyongrup->periyodik;
                                                    $depoteslim->parabirimi_id=$uretimyer->parabirimi_id;
                                                    $depoteslim->save();
                                                }
                                            }else{
                                                $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                                                $birim = $arizafiyat->parabirimi_id;
                                                $birim2 = $arizafiyat->parabirimi2_id;
                                                $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                                if($arizakayit->arizakayit_durum==7){
                                                    $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                        ->where('depodurum', 0)->where('tipi', 2)->where('periyodik',0)->where('subegonderim', 0)->first();
                                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                    {
                                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                                        if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                            $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                            $depoteslim->sayacsayisi += 1;
                                                            $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                            $depoteslim->save();
                                                        }else{
                                                            $flag = 1;
                                                        }
                                                    } else { //yeni depo teslimatı yapılacak
                                                        $depoteslim = new DepoTeslim;
                                                        $depoteslim->servis_id = $sayacgelen->servis_id;
                                                        $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                        $depoteslim->secilenler = $sayacgelen->id;
                                                        $depoteslim->sayacsayisi = 1;
                                                        $depoteslim->depodurum = 0;
                                                        $depoteslim->tipi = 2;
                                                        $depoteslim->parabirimi_id=$birim;
                                                        $depoteslim->parabirimi2_id=$birim2;
                                                        $depoteslim->save();
                                                    }
                                                }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0){
                                                    $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                        ->where('depodurum', 0)->where('tipi', 0)->where('periyodik',0)->where('subegonderim', 0)->first();
                                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                    {
                                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                                        if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                            $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                            $depoteslim->sayacsayisi += 1;
                                                            $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                            $depoteslim->save();
                                                        }else{
                                                            $flag = 1;
                                                        }
                                                    } else { //yeni depo teslimatı yapılacak
                                                        $depoteslim = new DepoTeslim;
                                                        $depoteslim->servis_id = $sayacgelen->servis_id;
                                                        $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                        $depoteslim->secilenler = $sayacgelen->id;
                                                        $depoteslim->sayacsayisi = 1;
                                                        $depoteslim->depodurum = 0;
                                                        $depoteslim->parabirimi_id=$birim;
                                                        $depoteslim->parabirimi2_id=$birim2;
                                                        $depoteslim->save();
                                                    }
                                                }else{
                                                    $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                        ->where('depodurum', 0)->where('tipi',1)->where('periyodik', 0)->where('subegonderim', 0)->first();
                                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                    {
                                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                                        if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                            $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                            $depoteslim->sayacsayisi += 1;
                                                            $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                            $depoteslim->save();
                                                        }else{
                                                            $flag = 1;
                                                        }
                                                    } else { //yeni depo teslimatı yapılacak
                                                        $depoteslim = new DepoTeslim;
                                                        $depoteslim->servis_id = $sayacgelen->servis_id;
                                                        $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                        $depoteslim->secilenler = $sayacgelen->id;
                                                        $depoteslim->sayacsayisi = 1;
                                                        $depoteslim->depodurum = 0;
                                                        $depoteslim->tipi = 1;
                                                        $depoteslim->parabirimi_id=$birim;
                                                        $depoteslim->parabirimi2_id=$birim2;
                                                        $depoteslim->save();
                                                    }
                                                }
                                            }
                                            if(!$flag)
                                                BackendController::HatirlatmaEkle(9, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                        }
                                        if(!$flag){
                                            BackendController::BildirimEkle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                            BackendController::HatirlatmaGuncelle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => 'Kalibrasyon yapılan sayaçlar depo teslimatına kaydedilemedi.'));
                                    }
                                } else { //kalibrasyon geçmedi ise  geri iade kalibrasyonlu olacak
                                    try {
                                        $sayacgelen = SayacGelen::find($kalibrasyon->sayacgelen_id);
                                        $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                        $uretimyer=UretimYer::find($servistakip->uretimyer_id);
                                        $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);

                                        $sayacgelen->kalibrasyon = 1;
                                        $sayacgelen->beyanname = -2;
                                        $sayacgelen->save();
                                        $arizakayit->arizakayit_durum = 7;
                                        $arizakayit->rapordurum = 0;
                                        $arizakayit->save();
                                        $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                        $servistakip->durum = 8;
                                        $servistakip->kalibrasyontarihi = $kalibrasyon->kalibrasyontarih;
                                        $servistakip->sonislemtarihi = $kalibrasyon->kalibrasyontarih;
                                        $servistakip->save();
                                        try {
                                            $flag = 0;
                                            if($sayacgelen->musterionay || $kalibrasyongrup->periyodik) {
                                                $sayacgelen->teslimdurum=1;
                                                $sayacgelen->save();
                                                if($kalibrasyongrup->periyodik){
                                                    $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                        ->where('depodurum', 0)->where('tipi', 0)->where('periyodik', $kalibrasyongrup->periyodik)->where('subegonderim',0)->first();
                                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                    {
                                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                                        if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                            $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                            $depoteslim->sayacsayisi += 1;
                                                            $depoteslim->save();
                                                        }else{
                                                            $flag = 1;
                                                        }
                                                    } else { //yeni depo teslimatı yapılacak
                                                        $depoteslim = new DepoTeslim;
                                                        $depoteslim->servis_id = $sayacgelen->servis_id;
                                                        $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                        $depoteslim->secilenler = $sayacgelen->id;
                                                        $depoteslim->sayacsayisi = 1;
                                                        $depoteslim->depodurum = 0;
                                                        $depoteslim->periyodik = $kalibrasyongrup->periyodik;
                                                        $depoteslim->parabirimi_id=$uretimyer->parabirimi_id;
                                                        $depoteslim->save();
                                                    }
                                                }else {
                                                    $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                                                    $birim = $arizafiyat->parabirimi_id;
                                                    $birim2 = $arizafiyat->parabirimi2_id;
                                                    $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                                        ->where('depodurum', 0)->where('tipi', 2)->where('periyodik', 0)->where('subegonderim', 0)->first();
                                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                    {
                                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                                        if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                            $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                            $depoteslim->sayacsayisi += 1;
                                                            $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                            $depoteslim->save();
                                                        } else {
                                                            $flag = 1;
                                                        }
                                                    } else { //yeni depo teslimatı yapılacak
                                                        $depoteslim = new DepoTeslim;
                                                        $depoteslim->servis_id = $sayacgelen->servis_id;
                                                        $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                                        $depoteslim->secilenler = $sayacgelen->id;
                                                        $depoteslim->sayacsayisi = 1;
                                                        $depoteslim->depodurum = 0;
                                                        $depoteslim->tipi = 2;
                                                        $depoteslim->parabirimi_id = $birim;
                                                        $depoteslim->parabirimi2_id = $birim2;
                                                        $depoteslim->save();
                                                    }
                                                }
                                                if(!$flag)
                                                    BackendController::HatirlatmaEkle(9, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                            }
                                            if(!$flag){
                                                BackendController::BildirimEkle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                                BackendController::HatirlatmaGuncelle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                            }
                                        } catch (Exception $e) {
                                            DB::rollBack();
                                            Log::error($e);
                                            return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => 'Kalibrasyon yapılan sayaçlar depo teslimatına kaydedilemedi.'));
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Kaydetme Hatası', 'text' => 'Kalibrasyon yapılamayan sayaçlar için hurdaya ayırma bilgisi girişi yapılamadı'));
                                    }
                                }
                                if (($kalibrasyongrup->adet - $kalibrasyongrup->biten)==1) {
                                    $kalibrasyongrup->kalibrasyondurum = 1;
                                }
                                $kalibrasyongrup->biten += 1;
                                $kalibrasyongrup->save();
                            } else {
                                array_push($errors, $serino);
                            }
                        }
                        $biten = $basarili+$basarisiz;
                        if($biten>0){
                            DB::commit();
                            if(count($errors)>0){
                                Log::info('Hatalı Kalibrasyon Kayıtları:'.implode(" , ",$errors));
                                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-cog', $biten . ' Adet Sayaca Ait Kalibrasyon Bilgisi Eklendi. Hatalı Kayıtlar Var!', 'Ekleyen:' . Auth::user()->adi_soyadi);
                                return Response::json(array('durum' => true, 'errors'=>$errors,'type' => 'warning', 'title' => 'Kalibrasyon Bilgisi Başarıyla Kaydedildi.', 'text' => 'Kalibrasyon Bilgileri Başarıyla Kaydedildi. Bazı Kayıtlar Eklenemedi!'));
                            }else{
                                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-cog', $biten . ' Adet Sayaca Ait Kalibrasyon Bilgisi Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi);
                                return Response::json(array('durum' => true, 'type' => 'success', 'title' => 'Kalibrasyon Bilgisi Başarıyla Kaydedildi', 'text' => 'Kalibrasyon Bilgileri Başarıyla Kaydedildi'));
                            }
                        }else{
                            DB::rollback();
                            return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => 'Kalibrasyon Bilgileri Arasında Uygun Veri Bulunamadı'));
                        }
                    } catch (Exception $e) {
                        Input::flash();
                        DB::rollBack();
                        Log::error($e);
                        return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => 'Kalibrasyon Bilgileri Kaydedilirken Hata Oluştu.'));
                    }
                }else{
                    return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Excelden bilgi alınırken hata oluştu!', 'text' => 'Kaydedilecek Veri Bulunamadı'));
                }
            }else {
                return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Excelden bilgi alınırken hata oluştu!', 'text' => 'Excel Dosyası Bulunamadı'));
            }
        } catch (Exception $e) {
            return Response::json(array('durum' => false, 'type'=>'error','title'=>'Excelden bilgi alınırken hata oluştu!','text'=>str_replace("'","\'",$e->getMessage())));
        }
    }

    public function postHurdaexcel(){
        try {
            If (Input::hasFile('file')) {
                DB::beginTransaction();
                $dosya = Input::file('file');
                $noktasayi=Input::get('hurdanoktasayi');
                $count=0;
                $hf2 = Input::get('hurdahf2');
                $hf3 = Input::get('hurdahf3');
                $hf32 = Input::get('hurdahf32');
                $uzanti = $dosya->getClientOriginalExtension();
                $isim = Str::slug(str_random(5)) . '.' . $uzanti;
                $dosya->move('assets/temp/', $isim);
                $path = 'assets/temp/' . $isim;
                $errors = array();
                $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {})->get();
                File::delete($path);
                $noktadurum = 0;
                if(!empty($data) && $data->count()) {
                    try {
                        foreach ($data as $key => $value) {
                            if(!$noktadurum){
                                switch ($noktasayi) {
                                    case 3:
                                        if (isset($value->nokta1) && isset($value->nokta2) && isset($value->nokta3))
                                            $noktadurum = 1;
                                        break;
                                    case 5:
                                        if (isset($value->nokta1) && isset($value->nokta2) && isset($value->nokta3) && isset($value->nokta4) && isset($value->nokta5))
                                            $noktadurum = 1;
                                        if ($hf2) {
                                            $noktadurum = 0;
                                            if (isset($value->hf2nokta1) && isset($value->hf2nokta2) && isset($value->hf2nokta3) && isset($value->hf2nokta4) && isset($value->hf2nokta5))
                                                $noktadurum = 1;
                                        }
                                        if ($hf3) {
                                            $noktadurum = 0;
                                            if (isset($value->hf3nokta1) && isset($value->hf3nokta2) && isset($value->hf3nokta3) && isset($value->hf3nokta4) && isset($value->hf3nokta5))
                                                $noktadurum = 1;
                                        }
                                        if ($hf32) {
                                            $noktadurum = 0;
                                            if (isset($value->hf32nokta1) && isset($value->hf32nokta2) && isset($value->hf32nokta3) && isset($value->hf32nokta4) && isset($value->hf32nokta5))
                                                $noktadurum = 1;
                                        }
                                        break;
                                    case 6:
                                        if (isset($value->nokta1) && isset($value->nokta2) && isset($value->nokta3) && isset($value->nokta4) && isset($value->nokta5) && isset($value->nokta6))
                                            $noktadurum = 1;
                                        if ($hf2) {
                                            $noktadurum = 0;
                                            if (isset($value->hf2nokta1) && isset($value->hf2nokta2) && isset($value->hf2nokta3) && isset($value->hf2nokta4) && isset($value->hf2nokta5) && isset($value->hf2nokta6))
                                                $noktadurum = 1;
                                        }
                                        if ($hf3) {
                                            $noktadurum = 0;
                                            if (isset($value->hf3nokta1) && isset($value->hf3nokta2) && isset($value->hf3nokta3) && isset($value->hf3nokta4) && isset($value->hf3nokta5) && isset($value->hf3nokta6))
                                                $noktadurum = 1;
                                        }
                                        if ($hf32) {
                                            $noktadurum = 0;
                                            if (isset($value->hf32nokta1) && isset($value->hf32nokta2) && isset($value->hf32nokta3) && isset($value->hf32nokta4) && isset($value->hf32nokta5) && isset($value->hf32nokta6))
                                                $noktadurum = 1;
                                        }
                                        break;
                                    case 7:
                                        if (isset($value->nokta1) && isset($value->nokta2) && isset($value->nokta3) && isset($value->nokta4) && isset($value->nokta5) && isset($value->nokta6) && isset($value->nokta7))
                                            $noktadurum = 1;
                                        if ($hf2) {
                                            $noktadurum = 0;
                                            if (isset($value->hf2nokta1) && isset($value->hf2nokta2) && isset($value->hf2nokta3) && isset($value->hf2nokta4) && isset($value->hf2nokta5) && isset($value->hf2nokta6) && isset($value->hf2nokta7))
                                                $noktadurum = 1;
                                        }
                                        if ($hf3) {
                                            $noktadurum = 0;
                                            if (isset($value->hf3nokta1) && isset($value->hf3nokta2) && isset($value->hf3nokta3) && isset($value->hf3nokta4) && isset($value->hf3nokta5) && isset($value->hf3nokta6) && isset($value->hf3nokta7))
                                                $noktadurum = 1;
                                        }
                                        if ($hf32) {
                                            $noktadurum = 0;
                                            if (isset($value->hf32nokta1) && isset($value->hf32nokta2) && isset($value->hf32nokta3) && isset($value->hf32nokta4) && isset($value->hf32nokta5) && isset($value->hf32nokta6) && isset($value->hf32nokta7))
                                                $noktadurum = 1;
                                        }
                                        break;
                                }
                            }
                            if(!$noktadurum)
                                return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Kaydetme Hatası', 'text' => 'Kalibrasyon için bilgi alınacak noktalar uygun değil'));

                            $serino = $value->sayac_no;
                            $kalibrasyon = Kalibrasyon::where('kalibrasyon_seri', $serino)->where('durum', 0)->first();
                            if ($kalibrasyon) {
                                $sayacadi = SayacAdi::find($kalibrasyon->sayacadi_id);
                                $kalibrasyongrup = KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
                                $kalibrasyonstandart = KalibrasyonStandart::where('kalibrasyontipi_id', $sayacadi->kalibrasyontipi_id)->where('noktasayisi', $noktasayi)->first();
                                $kalibrasyon->kalibrasyonstandart_id = $kalibrasyonstandart->id;
                                $kalibrasyon->sira = 1;
                                $istasyonlar = Istasyon::all();
                                foreach ($istasyonlar as $istasyon) {
                                    $sayacadlari = explode(',', $istasyon->sayacadlari);
                                    if (in_array($sayacadi->id, $sayacadlari)) {
                                        $kalibrasyon->istasyon_id = $istasyon->id;
                                        break;
                                    }
                                }
                                switch ($noktasayi) {
                                    case 3:
                                        $nokta1 = round($value->nokta1,2);
                                        $nokta2 = round($value->nokta2,2);
                                        $nokta3 = round($value->nokta3,2);
                                        $kalibrasyon->nokta1sapma = $kalibrasyonstandart->sapma1;
                                        $kalibrasyon->nokta2sapma = $kalibrasyonstandart->sapma2;
                                        $kalibrasyon->nokta3sapma = $kalibrasyonstandart->sapma3;
                                        $kalibrasyon->sonuc1 = $nokta1;
                                        $kalibrasyon->sonuc2 = $nokta2;
                                        $kalibrasyon->sonuc3 = $nokta3;
                                        if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                            ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                            ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                        } else {
                                            $kalibrasyon->durum = 3; // hurda veri yoksa
                                        }
                                        $kalibrasyon->kullanici_id = Auth::user()->id;
                                        $kalibrasyon->kalibrasyontarih = date('Y-m-d H:i:s');
                                        $kalibrasyon->save();
                                        break;
                                    case 5:
                                        $flag = 0;
                                        $nokta1 = round($value->nokta1,2);
                                        $nokta2 = round($value->nokta2,2);
                                        $nokta3 = round($value->nokta3,2);
                                        $nokta4 = round($value->nokta4,2);
                                        $nokta5 = round($value->nokta5,2);
                                        $kalibrasyon->nokta1sapma = $kalibrasyonstandart->sapma1;
                                        $kalibrasyon->nokta2sapma = $kalibrasyonstandart->sapma2;
                                        $kalibrasyon->nokta3sapma = $kalibrasyonstandart->sapma3;
                                        $kalibrasyon->nokta4sapma = $kalibrasyonstandart->sapma4;
                                        $kalibrasyon->nokta5sapma = $kalibrasyonstandart->sapma5;
                                        $kalibrasyon->sonuc1 = $nokta1;
                                        $kalibrasyon->sonuc2 = $nokta2;
                                        $kalibrasyon->sonuc3 = $nokta3;
                                        $kalibrasyon->sonuc4 = $nokta4;
                                        $kalibrasyon->sonuc5 = $nokta5;
                                        if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                            ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                            ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                            ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                            ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $flag = 1;
                                        } else {
                                            $kalibrasyon->durum = 3; // hurda veri yoksa
                                        }
                                        if ($hf2) {
                                            $nokta1 = round($value->hf2nokta1,2);
                                            $nokta2 = round($value->hf2nokta2,2);
                                            $nokta3 = round($value->hf2nokta3,2);
                                            $nokta4 = round($value->hf2nokta4,2);
                                            $nokta5 = round($value->hf2nokta5,2);
                                            $kalibrasyon->hf2 = 1;
                                            $kalibrasyon->hf2nokta1sapma = $kalibrasyonstandart->sapma1;
                                            $kalibrasyon->hf2nokta2sapma = $kalibrasyonstandart->sapma2;
                                            $kalibrasyon->hf2nokta3sapma = $kalibrasyonstandart->sapma3;
                                            $kalibrasyon->hf2nokta4sapma = $kalibrasyonstandart->sapma4;
                                            $kalibrasyon->hf2nokta5sapma = $kalibrasyonstandart->sapma5;
                                            $kalibrasyon->hf2sonuc1 = $nokta1;
                                            $kalibrasyon->hf2sonuc2 = $nokta2;
                                            $kalibrasyon->hf2sonuc3 = $nokta3;
                                            $kalibrasyon->hf2sonuc4 = $nokta4;
                                            $kalibrasyon->hf2sonuc5 = $nokta5;
                                            if (!$flag) {
                                                if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                                    ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                                    ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                                    ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                                    ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5))
                                                ) {
                                                    $kalibrasyon->durum = 2; //başarısız
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        if ($hf3) {
                                            $nokta1 = round($value->hf3nokta1,2);
                                            $nokta2 = round($value->hf3nokta2,2);
                                            $nokta3 = round($value->hf3nokta3,2);
                                            $nokta4 = round($value->hf3nokta4,2);
                                            $nokta5 = round($value->hf3nokta5,2);
                                            $kalibrasyon->hf3 = 1;
                                            $kalibrasyon->hf3nokta1sapma = $kalibrasyonstandart->sapma1;
                                            $kalibrasyon->hf3nokta2sapma = $kalibrasyonstandart->sapma2;
                                            $kalibrasyon->hf3nokta3sapma = $kalibrasyonstandart->sapma3;
                                            $kalibrasyon->hf3nokta4sapma = $kalibrasyonstandart->sapma4;
                                            $kalibrasyon->hf3nokta5sapma = $kalibrasyonstandart->sapma5;
                                            $kalibrasyon->hf3sonuc1 = $nokta1;
                                            $kalibrasyon->hf3sonuc2 = $nokta2;
                                            $kalibrasyon->hf3sonuc3 = $nokta3;
                                            $kalibrasyon->hf3sonuc4 = $nokta4;
                                            $kalibrasyon->hf3sonuc5 = $nokta5;
                                            if (!$flag) {
                                                if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                                    ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                                    ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                                    ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                                    ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5))
                                                ) {
                                                    $kalibrasyon->durum = 2; //başarısız
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        if ($hf32) {
                                            $nokta1 = round($value->hf32nokta1,2);
                                            $nokta2 = round($value->hf32nokta2,2);
                                            $nokta3 = round($value->hf32nokta3,2);
                                            $nokta4 = round($value->hf32nokta4,2);
                                            $nokta5 = round($value->hf32nokta5,2);
                                            $kalibrasyon->hf32 = 1;
                                            $kalibrasyon->hf32nokta1sapma = $kalibrasyonstandart->sapma1;
                                            $kalibrasyon->hf32nokta2sapma = $kalibrasyonstandart->sapma2;
                                            $kalibrasyon->hf32nokta3sapma = $kalibrasyonstandart->sapma3;
                                            $kalibrasyon->hf32nokta4sapma = $kalibrasyonstandart->sapma4;
                                            $kalibrasyon->hf32nokta5sapma = $kalibrasyonstandart->sapma5;
                                            $kalibrasyon->hf32sonuc1 = $nokta1;
                                            $kalibrasyon->hf32sonuc2 = $nokta2;
                                            $kalibrasyon->hf32sonuc3 = $nokta3;
                                            $kalibrasyon->hf32sonuc4 = $nokta4;
                                            $kalibrasyon->hf32sonuc5 = $nokta5;
                                            if (!$flag) {
                                                if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                                    ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                                    ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                                    ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                                    ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5))
                                                ) {
                                                    $kalibrasyon->durum = 2; //başarısız
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        if ($flag == 0) {
                                            $kalibrasyon->durum = 3; // hiç verisi yoksa hurda
                                        }
                                        $kalibrasyon->kullanici_id = Auth::user()->id;
                                        $kalibrasyon->kalibrasyontarih = date('Y-m-d H:i:s');
                                        $kalibrasyon->save();
                                        break;
                                    case 6:
                                        $flag = 0;
                                        $nokta1 = round($value->nokta1,2);
                                        $nokta2 = round($value->nokta2,2);
                                        $nokta3 = round($value->nokta3,2);
                                        $nokta4 = round($value->nokta4,2);
                                        $nokta5 = round($value->nokta5,2);
                                        $nokta6 = round($value->nokta6,2);
                                        $kalibrasyon->nokta1sapma = $kalibrasyonstandart->sapma1;
                                        $kalibrasyon->nokta2sapma = $kalibrasyonstandart->sapma2;
                                        $kalibrasyon->nokta3sapma = $kalibrasyonstandart->sapma3;
                                        $kalibrasyon->nokta4sapma = $kalibrasyonstandart->sapma4;
                                        $kalibrasyon->nokta5sapma = $kalibrasyonstandart->sapma5;
                                        $kalibrasyon->nokta6sapma = $kalibrasyonstandart->sapma6;
                                        $kalibrasyon->sonuc1 = $nokta1;
                                        $kalibrasyon->sonuc2 = $nokta2;
                                        $kalibrasyon->sonuc3 = $nokta3;
                                        $kalibrasyon->sonuc4 = $nokta4;
                                        $kalibrasyon->sonuc5 = $nokta5;
                                        $kalibrasyon->sonuc6 = $nokta6;
                                        if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                            ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                            ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                            ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                            ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5)) ||
                                            ($nokta6 > $kalibrasyonstandart->sapma6) || ($nokta6 < -($kalibrasyonstandart->sapma6))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $flag = 1;
                                        } else {
                                            $kalibrasyon->durum = 3; //hurda
                                        }
                                        if ($hf2) {
                                            $nokta1 = round($value->hf2nokta1,2);
                                            $nokta2 = round($value->hf2nokta2,2);
                                            $nokta3 = round($value->hf2nokta3,2);
                                            $nokta4 = round($value->hf2nokta4,2);
                                            $nokta5 = round($value->hf2nokta5,2);
                                            $nokta6 = round($value->hf2nokta6,2);
                                            $kalibrasyon->hf2 = 1;
                                            $kalibrasyon->hf2nokta1sapma = $kalibrasyonstandart->sapma1;
                                            $kalibrasyon->hf2nokta2sapma = $kalibrasyonstandart->sapma2;
                                            $kalibrasyon->hf2nokta3sapma = $kalibrasyonstandart->sapma3;
                                            $kalibrasyon->hf2nokta4sapma = $kalibrasyonstandart->sapma4;
                                            $kalibrasyon->hf2nokta5sapma = $kalibrasyonstandart->sapma5;
                                            $kalibrasyon->hf2nokta6sapma = $kalibrasyonstandart->sapma6;
                                            $kalibrasyon->hf2sonuc1 = $nokta1;
                                            $kalibrasyon->hf2sonuc2 = $nokta2;
                                            $kalibrasyon->hf2sonuc3 = $nokta3;
                                            $kalibrasyon->hf2sonuc4 = $nokta4;
                                            $kalibrasyon->hf2sonuc5 = $nokta5;
                                            $kalibrasyon->hf2sonuc6 = $nokta6;
                                            if (!$flag) {
                                                if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                                    ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                                    ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                                    ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                                    ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5)) ||
                                                    ($nokta6 > $kalibrasyonstandart->sapma6) || ($nokta6 < -($kalibrasyonstandart->sapma6))
                                                ) {
                                                    $kalibrasyon->durum = 2; //başarısız
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        if ($hf3) {
                                            $nokta1 = round($value->hf3nokta1,2);
                                            $nokta2 = round($value->hf3nokta2,2);
                                            $nokta3 = round($value->hf3nokta3,2);
                                            $nokta4 = round($value->hf3nokta4,2);
                                            $nokta5 = round($value->hf3nokta5,2);
                                            $nokta6 = round($value->hf3nokta6,2);
                                            $kalibrasyon->hf3 = 1;
                                            $kalibrasyon->hf3nokta1sapma = $kalibrasyonstandart->sapma1;
                                            $kalibrasyon->hf3nokta2sapma = $kalibrasyonstandart->sapma2;
                                            $kalibrasyon->hf3nokta3sapma = $kalibrasyonstandart->sapma3;
                                            $kalibrasyon->hf3nokta4sapma = $kalibrasyonstandart->sapma4;
                                            $kalibrasyon->hf3nokta5sapma = $kalibrasyonstandart->sapma5;
                                            $kalibrasyon->hf3nokta6sapma = $kalibrasyonstandart->sapma6;
                                            $kalibrasyon->hf3sonuc1 = $nokta1;
                                            $kalibrasyon->hf3sonuc2 = $nokta2;
                                            $kalibrasyon->hf3sonuc3 = $nokta3;
                                            $kalibrasyon->hf3sonuc4 = $nokta4;
                                            $kalibrasyon->hf3sonuc5 = $nokta5;
                                            $kalibrasyon->hf3sonuc6 = $nokta6;
                                            if (!$flag) {
                                                if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                                    ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                                    ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                                    ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                                    ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5)) ||
                                                    ($nokta6 > $kalibrasyonstandart->sapma6) || ($nokta6 < -($kalibrasyonstandart->sapma6))
                                                ) {
                                                    $kalibrasyon->durum = 2; //başarısız
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        if ($hf32) {
                                            $nokta1 = round($value->hf32nokta1,2);
                                            $nokta2 = round($value->hf32nokta2,2);
                                            $nokta3 = round($value->hf32nokta3,2);
                                            $nokta4 = round($value->hf32nokta4,2);
                                            $nokta5 = round($value->hf32nokta5,2);
                                            $nokta6 = round($value->hf32nokta6,2);
                                            $kalibrasyon->hf32 = 1;
                                            $kalibrasyon->hf32nokta1sapma = $kalibrasyonstandart->sapma1;
                                            $kalibrasyon->hf32nokta2sapma = $kalibrasyonstandart->sapma2;
                                            $kalibrasyon->hf32nokta3sapma = $kalibrasyonstandart->sapma3;
                                            $kalibrasyon->hf32nokta4sapma = $kalibrasyonstandart->sapma4;
                                            $kalibrasyon->hf32nokta5sapma = $kalibrasyonstandart->sapma5;
                                            $kalibrasyon->hf32nokta6sapma = $kalibrasyonstandart->sapma6;
                                            $kalibrasyon->hf32sonuc1 = $nokta1;
                                            $kalibrasyon->hf32sonuc2 = $nokta2;
                                            $kalibrasyon->hf32sonuc3 = $nokta3;
                                            $kalibrasyon->hf32sonuc4 = $nokta4;
                                            $kalibrasyon->hf32sonuc5 = $nokta5;
                                            $kalibrasyon->hf32sonuc6 = $nokta6;
                                            if (!$flag) {
                                                if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                                    ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                                    ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                                    ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                                    ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5)) ||
                                                    ($nokta6 > $kalibrasyonstandart->sapma6) || ($nokta6 < -($kalibrasyonstandart->sapma6))
                                                ) {
                                                    $kalibrasyon->durum = 2; //başarısız
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        if ($flag == 0) {
                                            $kalibrasyon->durum = 3; //hurda
                                        }
                                        $kalibrasyon->kullanici_id = Auth::user()->id;
                                        $kalibrasyon->kalibrasyontarih = date('Y-m-d H:i:s');
                                        $kalibrasyon->save();
                                        break;
                                    case 7:
                                        $flag = 0;
                                        $nokta1 = round($value->nokta1,2);
                                        $nokta2 = round($value->nokta2,2);
                                        $nokta3 = round($value->nokta3,2);
                                        $nokta4 = round($value->nokta4,2);
                                        $nokta5 = round($value->nokta5,2);
                                        $nokta6 = round($value->nokta6,2);
                                        $nokta7 = round($value->nokta7,2);
                                        $kalibrasyon->nokta1sapma = $kalibrasyonstandart->sapma1;
                                        $kalibrasyon->nokta2sapma = $kalibrasyonstandart->sapma2;
                                        $kalibrasyon->nokta3sapma = $kalibrasyonstandart->sapma3;
                                        $kalibrasyon->nokta4sapma = $kalibrasyonstandart->sapma4;
                                        $kalibrasyon->nokta5sapma = $kalibrasyonstandart->sapma5;
                                        $kalibrasyon->nokta6sapma = $kalibrasyonstandart->sapma6;
                                        $kalibrasyon->nokta7sapma = $kalibrasyonstandart->sapma7;
                                        $kalibrasyon->sonuc1 = $nokta1;
                                        $kalibrasyon->sonuc2 = $nokta2;
                                        $kalibrasyon->sonuc3 = $nokta3;
                                        $kalibrasyon->sonuc4 = $nokta4;
                                        $kalibrasyon->sonuc5 = $nokta5;
                                        $kalibrasyon->sonuc6 = $nokta6;
                                        $kalibrasyon->sonuc7 = $nokta7;
                                        if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                            ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                            ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                            ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                            ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5)) ||
                                            ($nokta6 > $kalibrasyonstandart->sapma6) || ($nokta6 < -($kalibrasyonstandart->sapma6)) ||
                                            ($nokta7 > $kalibrasyonstandart->sapma7) || ($nokta7 < -($kalibrasyonstandart->sapma7))
                                        ) {
                                            $kalibrasyon->durum = 2; //başarısız
                                            $flag = 1;
                                        } else {
                                            $kalibrasyon->durum = 3; //hurda
                                        }
                                        if ($hf2) {
                                            $nokta1 = round($value->hf2nokta1,2);
                                            $nokta2 = round($value->hf2nokta2,2);
                                            $nokta3 = round($value->hf2nokta3,2);
                                            $nokta4 = round($value->hf2nokta4,2);
                                            $nokta5 = round($value->hf2nokta5,2);
                                            $nokta6 = round($value->hf2nokta6,2);
                                            $nokta7 = round($value->hf2nokta7,2);
                                            $kalibrasyon->hf2 = 1;
                                            $kalibrasyon->hf2nokta1sapma = $kalibrasyonstandart->sapma1;
                                            $kalibrasyon->hf2nokta2sapma = $kalibrasyonstandart->sapma2;
                                            $kalibrasyon->hf2nokta3sapma = $kalibrasyonstandart->sapma3;
                                            $kalibrasyon->hf2nokta4sapma = $kalibrasyonstandart->sapma4;
                                            $kalibrasyon->hf2nokta5sapma = $kalibrasyonstandart->sapma5;
                                            $kalibrasyon->hf2nokta6sapma = $kalibrasyonstandart->sapma6;
                                            $kalibrasyon->hf2nokta7sapma = $kalibrasyonstandart->sapma7;
                                            $kalibrasyon->hf2sonuc1 = $nokta1;
                                            $kalibrasyon->hf2sonuc2 = $nokta2;
                                            $kalibrasyon->hf2sonuc3 = $nokta3;
                                            $kalibrasyon->hf2sonuc4 = $nokta4;
                                            $kalibrasyon->hf2sonuc5 = $nokta5;
                                            $kalibrasyon->hf2sonuc6 = $nokta6;
                                            $kalibrasyon->hf2sonuc7 = $nokta7;
                                            if (!$flag) {
                                                if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                                    ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                                    ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                                    ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                                    ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5)) ||
                                                    ($nokta6 > $kalibrasyonstandart->sapma6) || ($nokta6 < -($kalibrasyonstandart->sapma6)) ||
                                                    ($nokta7 > $kalibrasyonstandart->sapma7) || ($nokta7 < -($kalibrasyonstandart->sapma7))
                                                ) {
                                                    $kalibrasyon->durum = 2; //başarısız
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        if ($hf3) {
                                            $nokta1 = round($value->hf3nokta1,2);
                                            $nokta2 = round($value->hf3nokta2,2);
                                            $nokta3 = round($value->hf3nokta3,2);
                                            $nokta4 = round($value->hf3nokta4,2);
                                            $nokta5 = round($value->hf3nokta5,2);
                                            $nokta6 = round($value->hf3nokta6,2);
                                            $nokta7 = round($value->hf3nokta7,2);
                                            $kalibrasyon->hf3 = 1;
                                            $kalibrasyon->hf3nokta1sapma = $kalibrasyonstandart->sapma1;
                                            $kalibrasyon->hf3nokta2sapma = $kalibrasyonstandart->sapma2;
                                            $kalibrasyon->hf3nokta3sapma = $kalibrasyonstandart->sapma3;
                                            $kalibrasyon->hf3nokta4sapma = $kalibrasyonstandart->sapma4;
                                            $kalibrasyon->hf3nokta5sapma = $kalibrasyonstandart->sapma5;
                                            $kalibrasyon->hf3nokta6sapma = $kalibrasyonstandart->sapma6;
                                            $kalibrasyon->hf3nokta7sapma = $kalibrasyonstandart->sapma7;
                                            $kalibrasyon->hf3sonuc1 = $nokta1;
                                            $kalibrasyon->hf3sonuc2 = $nokta2;
                                            $kalibrasyon->hf3sonuc3 = $nokta3;
                                            $kalibrasyon->hf3sonuc4 = $nokta4;
                                            $kalibrasyon->hf3sonuc5 = $nokta5;
                                            $kalibrasyon->hf3sonuc6 = $nokta6;
                                            $kalibrasyon->hf3sonuc7 = $nokta7;
                                            if (!$flag) {
                                                if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                                    ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                                    ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                                    ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                                    ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5)) ||
                                                    ($nokta6 > $kalibrasyonstandart->sapma6) || ($nokta6 < -($kalibrasyonstandart->sapma6)) ||
                                                    ($nokta7 > $kalibrasyonstandart->sapma7) || ($nokta7 < -($kalibrasyonstandart->sapma7))
                                                ) {
                                                    $kalibrasyon->durum = 2; //başarısız
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        if ($hf32) {
                                            $nokta1 = round($value->hf32nokta1,2);
                                            $nokta2 = round($value->hf32nokta2,2);
                                            $nokta3 = round($value->hf32nokta3,2);
                                            $nokta4 = round($value->hf32nokta4,2);
                                            $nokta5 = round($value->hf32nokta5,2);
                                            $nokta6 = round($value->hf32nokta6,2);
                                            $nokta7 = round($value->hf32nokta7,2);
                                            $kalibrasyon->hf32 = 1;
                                            $kalibrasyon->hf32nokta1sapma = $kalibrasyonstandart->sapma1;
                                            $kalibrasyon->hf32nokta2sapma = $kalibrasyonstandart->sapma2;
                                            $kalibrasyon->hf32nokta3sapma = $kalibrasyonstandart->sapma3;
                                            $kalibrasyon->hf32nokta4sapma = $kalibrasyonstandart->sapma4;
                                            $kalibrasyon->hf32nokta5sapma = $kalibrasyonstandart->sapma5;
                                            $kalibrasyon->hf32nokta6sapma = $kalibrasyonstandart->sapma6;
                                            $kalibrasyon->hf32nokta7sapma = $kalibrasyonstandart->sapma7;
                                            $kalibrasyon->hf32sonuc1 = $nokta1;
                                            $kalibrasyon->hf32sonuc2 = $nokta2;
                                            $kalibrasyon->hf32sonuc3 = $nokta3;
                                            $kalibrasyon->hf32sonuc4 = $nokta4;
                                            $kalibrasyon->hf32sonuc5 = $nokta5;
                                            $kalibrasyon->hf32sonuc6 = $nokta6;
                                            $kalibrasyon->hf32sonuc7 = $nokta7;
                                            if (!$flag) {
                                                if (($nokta1 > $kalibrasyonstandart->sapma1) || ($nokta1 < -($kalibrasyonstandart->sapma1)) ||
                                                    ($nokta2 > $kalibrasyonstandart->sapma2) || ($nokta2 < -($kalibrasyonstandart->sapma2)) ||
                                                    ($nokta3 > $kalibrasyonstandart->sapma3) || ($nokta3 < -($kalibrasyonstandart->sapma3)) ||
                                                    ($nokta4 > $kalibrasyonstandart->sapma4) || ($nokta4 < -($kalibrasyonstandart->sapma4)) ||
                                                    ($nokta5 > $kalibrasyonstandart->sapma5) || ($nokta5 < -($kalibrasyonstandart->sapma5)) ||
                                                    ($nokta6 > $kalibrasyonstandart->sapma6) || ($nokta6 < -($kalibrasyonstandart->sapma6)) ||
                                                    ($nokta7 > $kalibrasyonstandart->sapma7) || ($nokta7 < -($kalibrasyonstandart->sapma7))
                                                ) {
                                                    $kalibrasyon->durum = 2; //başarısız
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        if ($flag == 0) {
                                            $kalibrasyon->durum = 3;
                                        }
                                        $kalibrasyon->kullanici_id = Auth::user()->id;
                                        $kalibrasyon->kalibrasyontarih = date('Y-m-d H:i:s');
                                        $kalibrasyon->save();
                                        break;
                                }
                                $count++;
                                try {
                                    $sayacgelen = SayacGelen::find($kalibrasyon->sayacgelen_id);
                                    $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                    $uretimyer=UretimYer::find($servistakip->uretimyer_id);
                                    $sayac = Sayac::where('uretimyer_id', $sayacgelen->uretimyer_id)->where('serino', $sayacgelen->serino)->first();
                                    $hurdakayit = new Hurda;
                                    $hurdakayit->servis_id = $sayacgelen->servis_id;
                                    $hurdakayit->netsiscari_id = $sayacgelen->netsiscari_id;
                                    $hurdakayit->sayac_id = $sayac->id;
                                    $aciklama = isset($value->aciklama) ? trim($value->aciklama) : '';
                                    if($aciklama!=''){
                                        $hurdanedeni = HurdaNedeni::where('nedeni',$aciklama)->where('sayactur_id',5)->first();
                                        if(!$hurdanedeni){
                                            $hurdanedeni = new HurdaNedeni;
                                            $hurdanedeni->sayactur_id=5;
                                            $hurdanedeni->nedeni=$aciklama;
                                            $hurdanedeni->save();
                                        }
                                        $hurdakayit->hurdanedeni_id = $hurdanedeni->id;
                                    }else{
                                        $hurdanedeni = HurdaNedeni::find(1);
                                        $hurdakayit->hurdanedeni_id = 1;
                                    }
                                    $hurdakayit->hurdatarihi = date('Y-m-d H:i:s');
                                    $hurdakayit->sayacgelen_id = $sayacgelen->id;
                                    $hurdakayit->kalibrasyon_id = $kalibrasyon->id;
                                    $hurdakayit->kullanici_id = Auth::user()->id;
                                    $hurdakayit->save();
                                    $hurdanedeni->kullanim++;
                                    $hurdanedeni->save();
                                    try {
                                        $teslimflag=0;
                                        $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                                            ->where('depodurum',0)->where('tipi', 3)->where('periyodik',$kalibrasyongrup->periyodik)->where('subegonderim', 0)->first();
                                        if ($depoteslim) {
                                            $secilenlist = explode(',', $depoteslim->secilenler);
                                            if (!in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listede değilse
                                                $depoteslim->secilenler .= ',' . $sayacgelen->id;
                                                $depoteslim->sayacsayisi += 1;
                                            }else{
                                                $teslimflag = 1;
                                            }
                                        } else {
                                            $depoteslim = new DepoTeslim;
                                            $depoteslim->servis_id = $sayacgelen->servis_id;
                                            $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                                            $depoteslim->secilenler = $sayacgelen->id;
                                            $depoteslim->sayacsayisi = 1;
                                            $depoteslim->depodurum = 0;
                                            $depoteslim->tipi = 3;
                                            $depoteslim->periyodik=$kalibrasyongrup->periyodik;
                                            $depoteslim->parabirimi_id=$uretimyer->parabirimi_id;
                                        }
                                        $depoteslim->save();
                                        $sayacgelen->kalibrasyon=1;
                                        $sayacgelen->sayacdurum=3;
                                        $sayacgelen->teslimdurum=3;
                                        $sayacgelen->beyanname=-2;
                                        $sayacgelen->save();
                                        $servistakip->kalibrasyon_id = $kalibrasyon->id;
                                        $servistakip->durum = 8;
                                        $servistakip->kalibrasyontarihi = $kalibrasyon->kalibrasyontarih;
                                        $servistakip->sonislemtarihi = $kalibrasyon->kalibrasyontarih;
                                        $servistakip->save();
                                        $hurdakayit->depoteslim_id = $depoteslim->id;
                                        $hurdakayit->save();
                                        if(!$kalibrasyongrup->periyodik){
                                            $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
                                            $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                                            $sayacyapilan = SayacYapilan::find($arizakayit->sayacyapilan_id);
                                            $sayacuyari = SayacUyari::find($arizakayit->sayacuyari_id);
                                            $arizakayiteski = ArizaKayitEski::where('arizakayit_id',$arizakayit->id)->first();
                                            if($arizakayiteski){
                                                $arizakayiteski->yapilanlar=$sayacyapilan->yapilanlar;
                                                $arizakayiteski->uyarilar=$sayacuyari->uyarilar;
                                                $arizakayiteski->arizakayit_durum=$arizakayit->arizakayit_durum;
                                                $arizakayiteski->save();
                                            }else{
                                                $arizakayiteski = new ArizaKayitEski;
                                                $arizakayiteski->arizakayit_id = $arizakayit->id;
                                                $arizakayiteski->yapilanlar=$sayacyapilan->yapilanlar;
                                                $arizakayiteski->uyarilar=$sayacuyari->uyarilar;
                                                $arizakayiteski->arizakayit_durum=$arizakayit->arizakayit_durum;
                                                $arizakayiteski->save();
                                            }
                                            $sayacyapilan->yapilanlar='58';
                                            $sayacyapilan->save();
                                            $sayacuyari->uyarilar ='11';
                                            $sayacuyari->save();
                                            $arizakayit->arizakayit_durum = 2;
                                            $arizakayit->rapordurum = 0 ;
                                            $arizakayit->save();
                                            if($sayacgelen->fiyatlandirma){
                                                $ucretlendirilen=Ucretlendirilen::find($servistakip->ucretlendirilen_id);
                                                if(!$sayacgelen->musterionay){
                                                    $secilenlist=explode(',',$ucretlendirilen->secilenler);
                                                    $list="";
                                                    foreach ($secilenlist as $secilen){
                                                        if($secilen!=$arizafiyat->id)
                                                            $list.=($list=="" ? "" : ",").$secilen;
                                                    }
                                                    if($ucretlendirilen->sayacsayisi>1){
                                                        $ucretlendirilen->secilenler=$list;
                                                        $ucretlendirilen->sayacsayisi--;
                                                        if($ucretlendirilen->parabirimi_id!=$arizafiyat->parabirimi_id){
                                                            if ($ucretlendirilen->parabirimi_id == 1) { //tl
                                                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi);
                                                            } else { //euro dolar sterln
                                                                if ($arizafiyat->parabirimi_id == 1) {
                                                                    $kur = 1 / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarihi);
                                                                } else {
                                                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $ucretlendirilen->kurtarihi);
                                                                }
                                                            }
                                                            $tutar=$arizafiyat->toplamtutar*$kur;
                                                            if ($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi_id) {
                                                                $tutar += $arizafiyat->toplamtutar2;
                                                            }else if($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi2_id){
                                                                $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                            }else{
                                                                DB::rollBack();
                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                            }
                                                                $ucretlendirilen->fiyat=round(($ucretlendirilen->fiyat - $tutar) * 2) / 2;
                                                            }else{
                                                                $ucretlendirilen->fiyat-=$arizafiyat->toplamtutar;
                                                                if($arizafiyat->parabirimi2_id!=null){
                                                                if($ucretlendirilen->parabirimi2_id!=$arizafiyat->parabirimi2_id){
                                                                    DB::rollBack();
                                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                                }else{
                                                                    $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                                }
                                                            }
                                                        }
                                                        $ucretlendirilen->durum=1;
                                                        $ucretlendirilen->save();
                                                    }else{
                                                        $servistakip->ucretlendirilen_id=NULL;
                                                        $servistakip->save();
                                                        $ucretlendirilen->delete();
                                                    }
                                                    BackendController::HatirlatmaSil(6, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                                }else{
                                                    if($ucretlendirilen->parabirimi_id!=$arizafiyat->parabirimi_id){
                                                        if ($ucretlendirilen->parabirimi_id == 1) { //tl
                                                            $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi);
                                                        } else { //euro dolar sterln
                                                            if ($arizafiyat->parabirimi_id == 1) {
                                                                $kur = 1 / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $arizafiyat->kurtarihi);
                                                            } else {
                                                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $arizafiyat->kurtarihi) / BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id, $ucretlendirilen->kurtarihi);
                                                            }
                                                        }
                                                        $tutar=$arizafiyat->toplamtutar*$kur;
                                                        if ($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi_id) {
                                                            $tutar += $arizafiyat->toplamtutar2;
                                                        }else if($arizafiyat->parabirimi2_id == $ucretlendirilen->parabirimi2_id){
                                                            $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                        }else{
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                        }
                                                        $ucretlendirilen->fiyat=round(($ucretlendirilen->fiyat - $tutar) * 2) / 2;
                                                    }else{
                                                        $ucretlendirilen->fiyat-=$arizafiyat->toplamtutar;
                                                        if($arizafiyat->parabirimi2_id!=null){
                                                            if($ucretlendirilen->parabirimi2_id!=$arizafiyat->parabirimi2_id){
                                                                DB::rollBack();
                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Ücretlendirmede İki ParaBiriminden Fazla Kullanılamaz!', 'type' => 'error'));
                                                            }else{
                                                                $ucretlendirilen->fiyat2-=$arizafiyat->toplamtutar2;
                                                            }
                                                        }
                                                    }
                                                    $ucretlendirilen->save();
                                                }
                                            }else{
                                                BackendController::HatirlatmaSil(4, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                            }
                                            $arizafiyat->fiyat = 0;
                                            $arizafiyat->fiyat2 = 0;
                                            $arizafiyat->tutar = 0;
                                            $arizafiyat->tutar2 = 0;
                                            $arizafiyat->kdv = 0;
                                            $arizafiyat->kdv2 = 0;
                                            $arizafiyat->toplamtutar = 0;
                                            $arizafiyat->toplamtutar2 = 0;
                                            $arizafiyat->parabirimi2_id = null;
                                            $arizafiyat->durum = 1;
                                            $arizafiyat->save();
                                        }
                                        if(!$teslimflag){
                                            BackendController::HatirlatmaGuncelle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                            BackendController::HatirlatmaEkle(9, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                            BackendController::BildirimEkle(8, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                            BackendController::BildirimEkle(10, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, 1);
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Kaydetme Hatası', 'text' => 'Kalibrasyon yapılamayan sayaçlar için depo teslim bilgisi kaydedilemedi'));
                                    }
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Kaydetme Hatası', 'text' => 'Kalibrasyon yapılamayan sayaçlar için hurdaya ayırma bilgisi girişi yapılamadı'));
                                }
                                if (($kalibrasyongrup->adet - $kalibrasyongrup->biten)==1) {
                                    $kalibrasyongrup->kalibrasyondurum = 1;
                                }
                                $kalibrasyongrup->biten += 1;
                                $kalibrasyongrup->save();
                            } else {
                                array_push($errors, $serino);
                            }
                        }
                    } catch (Exception $e) {
                        Input::flash();
                        DB::rollBack();
                        Log::error($e);
                        return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => 'Kalibrasyon Bilgileri Kaydedilirken Hata Oluştu.'));
                    }
                    try {
                        $biten = $count;
                        if($biten>0){
                            DB::commit();
                            if(count($errors)>0){
                                Log::info('Hatalı Kalibrasyon Kayıtları:'.$errors);
                                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-cog', $biten . ' Adet Sayaca Ait Kalibrasyon Bilgisi Eklendi. Hatalı Kayıtlar Var!', 'Ekleyen:' . Auth::user()->adi_soyadi);
                                return Response::json(array('durum' => true, 'errors'=>$errors,'type' => 'warning', 'title' => 'Kalibrasyon Bilgisi Başarıyla Kaydedildi.', 'text' => 'Kalibrasyon Bilgileri Başarıyla Kaydedildi. Bazı Kayıtlar Eklenemedi!'));
                            }else{
                                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-cog', $biten . ' Adet Sayaca Ait Kalibrasyon Bilgisi Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi);
                                return Response::json(array('durum' => true, 'type' => 'success', 'title' => 'Kalibrasyon Bilgisi Başarıyla Kaydedildi', 'text' => 'Kalibrasyon Bilgileri Başarıyla Kaydedildi'));
                            }
                        }else{
                            DB::rollBack();
                            return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => 'Kalibrasyon Bilgileri Arasında Uygun Veri Bulunamadı.'));
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Kalibrasyon Bilgisi Kaydedilemedi', 'text' => 'Kalibrasyon Bilgileri Kaydedilirken Hata Oluştu.'));
                    }
                }else{
                    return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Excelden bilgi alınırken hata oluştu!', 'text' => 'Kaydedilecek Veri Bulunamadı'));
                }
            }else {
                return Response::json(array('durum' => false, 'type' => 'error', 'title' => 'Excelden bilgi alınırken hata oluştu!', 'text' => 'Excel Dosyası Bulunamadı'));
            }
        } catch (Exception $e) {
            return Response::json(array('durum' => false, 'type'=>'error','title'=>'Excelden bilgi alınırken hata oluştu!','text'=>str_replace("'","\'",$e->getMessage())));
        }
    }
}
