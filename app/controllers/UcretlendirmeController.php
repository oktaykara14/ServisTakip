<?php
//transaction işlemi tamamlandı
class UcretlendirmeController extends BackendController {

/*    public function getAll(){
        $app_id = '774256';
        $app_key = '3eb2d03eb0b2222b161f';
        $app_secret = '5ac1610780213fc59b31';
        $app_cluster = 'eu';


        $options = array(
            'cluster' => 'eu',
            'useTLS' => false
        );
        $pusher = new Pusher\Pusher(
            '3eb2d03eb0b2222b161f',
            '5ac1610780213fc59b31',
            '774256',
            $options
        );

        $data['message'] = 'hello world';
        $pusher->trigger('my-channel', 'my-event', $data);
        return 1;
    }*/

    public function getUcretlendirmekayit($hatirlatma_id=false) {
        $parabirimleri = ParaBirimi::all();
        if($hatirlatma_id)
            return View::make('ucretlendirme.ucretlendirmekayit',array('hatirlatma_id'=>$hatirlatma_id,'parabirimleri'=>$parabirimleri))->with(array('title'=>'Ucretlendirme Takip Ekranı'));
        else
            return View::make('ucretlendirme.ucretlendirmekayit',array('parabirimleri'=>$parabirimleri))->with(array('title'=>'Ucretlendirme Takip Ekranı'));
    }

    public function postUcretlendirmekayitlist() {
        $hatirlatma_id=Input::get('hatirlatma_id');
        $netsiscari_id=Input::get('netsiscari_id');
        $servisid=BackendController::getKullaniciServis(1);
        $hatirlatma = false;
        if($hatirlatma_id!="") {
            $hatirlatma = Hatirlatma::find($hatirlatma_id);
        }
        if($hatirlatma){
            $query = ArizaFiyat::where('sayacgelen.netsiscari_id',$hatirlatma->netsiscari_id)->where(function($query) use($servisid){ $query->whereIn('sayacgelen.servis_id',$servisid)
                ->orWhere(function($query) use($servisid){ $query->whereNotIn('sayacgelen.servis_id',$servisid)->where('sayacgelen.teslimdurum',4);});})
                ->where(function($query){ $query->where('arizafiyat.durum',0)->orWhere('arizafiyat.durum',2);})
                ->select(array("arizafiyat.id","arizafiyat.ariza_serino","netsiscari.cariadi","uretimyer.yeradi","sayacadi.sayacadi","arizafiyat.ggaranti",
                    "arizafiyat.toplamtutar","arizafiyat.toplamtutar2","sayacgelen.depotarihi","sayacgelen.gdepotarihi","netsiscari.ncariadi","uretimyer.nyeradi",
                    "sayacadi.nsayacadi","arizafiyat.ngaranti","arizafiyat.subedurum","sayacgelen.musterionay","parabirimi.birimi","arizafiyat.parabirimi_id",
                    "parabirimi2.birimi2","arizafiyat.parabirimi2_id","arizafiyat.durum"))
                ->leftjoin("netsiscari", "arizafiyat.netsiscari_id", "=", "netsiscari.id")
                ->leftjoin("uretimyer", "arizafiyat.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("sayacadi", "arizafiyat.sayacadi_id", "=", "sayacadi.id")
                ->leftjoin("sayacgelen", "arizafiyat.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("parabirimi", "arizafiyat.parabirimi_id", "=", "parabirimi.id")
                ->leftjoin("parabirimi2", "arizafiyat.parabirimi2_id", "=", "parabirimi2.id");
        }else if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $query = ArizaFiyat::whereIn('arizafiyat.netsiscari_id',$netsiscarilist)->where(function($query) use($servisid){ $query->whereIn('sayacgelen.servis_id',$servisid)->orWhere('sayacgelen.teslimdurum',4);})
                ->where(function($query){ $query->where('arizafiyat.durum',0)->orWhere('arizafiyat.durum',2);})
                ->select(array("arizafiyat.id","arizafiyat.ariza_serino","netsiscari.cariadi","uretimyer.yeradi","sayacadi.sayacadi","arizafiyat.ggaranti",
                    "arizafiyat.toplamtutar","arizafiyat.toplamtutar2","sayacgelen.depotarihi","sayacgelen.gdepotarihi","netsiscari.ncariadi","uretimyer.nyeradi",
                    "sayacadi.nsayacadi","arizafiyat.ngaranti","arizafiyat.subedurum","sayacgelen.musterionay","parabirimi.birimi","arizafiyat.parabirimi_id",
                    "parabirimi2.birimi2","arizafiyat.parabirimi2_id","arizafiyat.durum"))
                ->leftjoin("netsiscari", "arizafiyat.netsiscari_id", "=", "netsiscari.id")
                ->leftjoin("uretimyer", "arizafiyat.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("sayacadi", "arizafiyat.sayacadi_id", "=", "sayacadi.id")
                ->leftjoin("sayacgelen", "arizafiyat.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("parabirimi", "arizafiyat.parabirimi_id", "=", "parabirimi.id")
                ->leftjoin("parabirimi2", "arizafiyat.parabirimi2_id", "=", "parabirimi2.id");
        }else{
            $query = ArizaFiyat::whereIn('sayacgelen.servis_id',$servisid)->where(function($query){ $query->where('arizafiyat.durum',0)->orWhere('arizafiyat.durum',2);})
                ->select(array("arizafiyat.id","arizafiyat.ariza_serino","netsiscari.cariadi","uretimyer.yeradi","sayacadi.sayacadi","arizafiyat.ggaranti",
                    "arizafiyat.toplamtutar","arizafiyat.toplamtutar2","sayacgelen.depotarihi","sayacgelen.gdepotarihi","netsiscari.ncariadi","uretimyer.nyeradi",
                    "sayacadi.nsayacadi","arizafiyat.ngaranti","arizafiyat.subedurum","sayacgelen.musterionay","parabirimi.birimi","arizafiyat.parabirimi_id",
                    "parabirimi2.birimi2","arizafiyat.parabirimi2_id","arizafiyat.durum"))
                ->leftjoin("netsiscari", "arizafiyat.netsiscari_id", "=", "netsiscari.id")
                ->leftjoin("uretimyer", "arizafiyat.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("sayacadi", "arizafiyat.sayacadi_id", "=", "sayacadi.id")
                ->leftjoin("sayacgelen", "arizafiyat.sayacgelen_id", "=", "sayacgelen.id")
                ->leftjoin("parabirimi", "arizafiyat.parabirimi_id", "=", "parabirimi.id")
                ->leftjoin("parabirimi2", "arizafiyat.parabirimi2_id", "=", "parabirimi2.id");
        }
        return Datatables::of($query)
            ->editColumn('toplamtutar', function ($model) {
                if($model->ggaranti=='İçinde')
                    return "0.00 ".$model->birimi;
                else
                    if($model->toplamtutar2==0){
                        if($model->toplamtutar==0)
                            return "0.00 ".$model->birimi;
                        else
                            return $model->toplamtutar." ".$model->birimi;
                    }else{
                        if($model->toplamtutar==0)
                            return $model->toplamtutar2." ".$model->birimi2;
                        else
                            return $model->toplamtutar." ".$model->birimi." + ".$model->toplamtutar2." ".$model->birimi2;
                    }
                })
            ->editColumn('depotarihi', function ($model) {
                $date = new DateTime($model->depotarihi);
                return $date->format('d-m-Y');})
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                if(!$model->fiyatlandirma)
                    return "<a class='btn btn-sm btn-warning duzenle' href='#detay-duzenle' data-toggle='modal' data-id='{$model->id}'> Düzenle </a>";
                else
                    return "<a class='btn btn-sm btn-info' href='{$root}/ucretlendirme/ucretlendirmekayitgoster/{$model->id}' > Göster </a>";
            })
            ->make(true);
    }

    public function getAktarmabilgi() {
        try {
            $secilenler=Input::get('secilenler');
            $secilenlist = explode(',', $secilenler);
            $sayacgelenid = ArizaFiyat::whereIn('id', $secilenlist)->get(array('sayacgelen_id'))->toArray();
            $arizafiyat = ArizaFiyat::find($secilenlist[0]);
            $arizafiyat->uretimyer = UretimYer::find($arizafiyat->uretimyer_id);
            $arizafiyat->netsiscari = Netsiscari::find($arizafiyat->netsiscari_id);
            $arizafiyat->netsisdepolar = NetsisDepolar::where('netsiscari_id', $arizafiyat->netsiscari_id)->first();
            $arizafiyat->sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
            $arizafiyat->yetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
            if (!$arizafiyat->yetkili)
                return Response::json(array('durum' => false, 'title' => 'Depolararası Transfer Hatası', 'text' => 'Kullanıcının Yetkisi Yok!', 'type'=>'warning'));
            $sayacgelenler = SayacGelen::whereIn('id', $sayacgelenid)->get();
            $sayacsecilenler = "";
            $adet = 0;
            $uretimyerid = 0;
            foreach ($sayacgelenler as $sayacgelen) {
                $sayacsecilenler .= ($sayacsecilenler == "" ? "" : ",") . $sayacgelen->id;
                $adet++;
                if ($uretimyerid != 0 && $uretimyerid != $sayacgelen->uretimyer_id) {
                    return Response::json(array('durum' => false, 'title' => 'Depolararası Transfer Hatası', 'text' => 'Seçilen sayaçlar farklı yerlere ait olamaz!', 'type'=>'warning'));
                } else {
                    $uretimyerid = $sayacgelen->uretimyer_id;
                    $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                    $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                    if ($sayacgelen->eklenmetarihi)
                        $sayacgelen->tarih = date('d-m-Y', strtotime($sayacgelen->eklenmetarihi));
                    else
                        $sayacgelen->tarih = "";
                }
            }
            return Response::json(array('durum' => true, 'secilen' => $sayacsecilenler, 'secilensayi' => $adet, 'arizafiyat' => $arizafiyat, 'sayacgelen' => $sayacgelenler ));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'title' => 'Hatalı Bilgi Mevcut', 'text' => str_replace("'","\'",$e->getMessage()), 'type'=>'error'));
        }
    }

    public function postDegisenlist() {
        $arizafiyat_id=Input::get('arizafiyat_id');
        $query = ArizaFiyat::where('arizafiyat.id',$arizafiyat_id)
            ->select(array("arizafiyat.id","sayacgelen.musterionay","arizafiyat.ariza_serino","uretimyer.yeradi","sayacadi.sayacadi","arizafiyat.ariza_garanti",
                "arizafiyat.toplamtutar","parabirimi.birimi","arizafiyat.parabirimi_id", "arizafiyat.durum"))
            ->leftjoin("uretimyer", "arizafiyat.uretimyer_id", "=", "uretimyer.id")
            ->leftjoin("sayacadi", "arizafiyat.sayacadi_id", "=", "sayacadi.id")
            ->leftjoin("sayacgelen", "arizafiyat.sayacgelen_id", "=", "sayacgelen.id")
            ->leftjoin("parabirimi", "arizafiyat.parabirimi_id", "=", "parabirimi.id");


        return Datatables::query($query)
            ->addColumn('check',function () {
                return "<input type='checkbox' class='checkboxes'/>"; })
            ->showColumns('id','ariza_serino', 'yeradi', 'sayacadi')
            ->addColumn('ariza_garanti', function ($model) {  if($model->ariza_garanti)  return 'İçinde';  else  return 'Dışında';})
            ->addColumn('toplamtutar', function ($model) {
                if($model->ariza_garanti)
                    return '0.00 '.$model->birimi;
                else
                    if($model->toplamtutar==0)
                        return '0.00 '.$model->birimi;
                    else
                        return $model->toplamtutar.' '.$model->birimi; })
            ->addColumn('islemler', function ($model) {
                $root = BackendController::getRootDizin();
                if(!$model->musterionay)
                    return "<a class='btn btn-sm btn-warning duzenle' href='#detay-duzenle' data-toggle='modal' data-id='".$model->id."'> Düzenle </a>
                        <a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                else
                    return "<a class='btn btn-sm btn-info' href='".$root."/ucretlendirme/ucretlendirmekayitgoster/".$model->id."' > Göster </a>";
            })
            ->showColumns('parabirimi_id')
            ->searchColumns('ariza_serino', 'yeradi','sayacadi', 'ariza_garanti','toplamtutar')
            ->orderColumns('ariza_serino', 'yeradi', 'sayacadi', 'ariza_garanti')
            ->make();
    }

    public function getKayitdetay() {
        try {
            $id=Input::get('id');
            $ucretlendirme = ArizaFiyat::find($id);
            $ucretlendirme->uretimyer = Uretimyer::find($ucretlendirme->uretimyer_id);
            $ucretlendirme->sayacadi = SayacAdi::find($ucretlendirme->sayacadi_id);
            $ucretlendirme->sayac = Sayac::find($ucretlendirme->sayac_id);
            $ucretlendirme->sayaccap = SayacCap::find($ucretlendirme->sayac->sayaccap_id);
            $ucretlendirme->degisen = explode(',', $ucretlendirme->degisenler);
            $ucretlendirme->parcalar = Degisenler::whereIn('id', $ucretlendirme->degisen)->get();
            $ucretlendirme->genelfiyatlar = explode(';', $ucretlendirme->genel);
            $ucretlendirme->ozelfiyatlar = explode(';', $ucretlendirme->ozel);
            $ucretlendirme->ucretsizler = explode(',', $ucretlendirme->ucretsiz);
            $ucretlendirme->genelparabirimi = BackendController::getGenelParabirimi();
            $ucretlendirme->ozelparabirimi = ParaBirimi::find($ucretlendirme->uretimyer->parabirimi_id);
            $genelbirimler=explode(',',$ucretlendirme->genelbirim);
            $ozelbirimler=explode(',',$ucretlendirme->ozelbirim);
            $genelbirim=array();
            $ozelbirim=array();
            foreach($genelbirimler as $birimid){
                $parabirimi=ParaBirimi::find($birimid);
                array_push($genelbirim,$parabirimi);
            }
            foreach($ozelbirimler as $birimid){
                $parabirimi=ParaBirimi::find($birimid);
                array_push($ozelbirim,$parabirimi);
            }
            $ucretlendirme->genelbirimler=$genelbirim;
            $ucretlendirme->ozelbirimler=$ozelbirim;
            if (is_null($ucretlendirme->kurtarihi)) {
                $dovizkuru = DovizKuru::orderBy('tarih', 'desc')->take(3)->get();
                foreach ($dovizkuru as $doviz) {
                    $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
                }
            } else {
                $dovizkuru = DovizKuru::where('tarih', $ucretlendirme->kurtarihi)->take(3)->get();
                foreach ($dovizkuru as $doviz) {
                    $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
                }
            }
            return Response::json(array('durum'=>true,'ucretlendirme' => $ucretlendirme, 'dovizkuru' => $dovizkuru));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'title' => 'Hatalı Bilgi Mevcut', 'text' => str_replace("'","\'",$e->getMessage()), 'type'=>'error'));
        }
    }

    public function getUcretlendirmelistesi() {
        try {
            $list=Input::get('secilenler');
            $secilenler = explode(',', $list);
            $uretimyerid = 0;
            $ucretlendirme = ArizaFiyat::whereIn('id', $secilenler)->get();
            $parabirimi = null;
            $parabirimi2 = null;
            foreach ($ucretlendirme as $ucretlendirilen) {
                if ($uretimyerid != 0 && $uretimyerid != $ucretlendirilen->uretimyer_id) {
                    return Response::json(array('durum' => false, 'title' => 'Ucretlendirme Hatası', 'text' => 'Seçilen sayaçlar farklı yerlere ait olamaz!', 'type'=>'warning'));
                } else {
                    $uretimyerid = $ucretlendirilen->uretimyer_id;
                    $ucretlendirilen->sayacadi = SayacAdi::find($ucretlendirilen->sayacadi_id);
                    $ucretlendirilen->uretimyer = Uretimyer::find($ucretlendirilen->uretimyer_id);
                    $ucretlendirilen->uretimyer->parabirimi = ParaBirimi::find($ucretlendirilen->uretimyer->parabirimi_id);
                    $ucretlendirilen->parabirimi = ParaBirimi::find($ucretlendirilen->parabirimi_id);
                    if(is_null($ucretlendirilen->parabirimi2_id))
                        $ucretlendirilen->parabirimi2 = null;
                    else
                        $ucretlendirilen->parabirimi2 = ParaBirimi::find($ucretlendirilen->parabirimi2_id);
                    $ucretlendirilen->arizakayit = ArizaKayit::find($ucretlendirilen->arizakayit_id);
                    $ucretlendirilen->sayacgelen = SayacGelen::find($ucretlendirilen->arizakayit->sayacgelen_id);
                    $parabirimi=$ucretlendirilen->uretimyer->parabirimi;
                    if(!(is_null($ucretlendirilen->parabirimi2_id)))
                        if($parabirimi2!=null){
                            if($parabirimi->id!=$ucretlendirilen->parabirimi2_id && $parabirimi2->id!=$ucretlendirilen->parabirimi2_id)
                                return Response::json(array('durum' => false, 'title' => 'Hatalı Bilgi Mevcut', 'text' => 'İki Parabiriminden Fazla Para Birimi Kullanılamaz!', 'type'=>'error'));
                        }else{
                            $parabirimi2=$ucretlendirilen->parabirimi2;
                        }
                }
            }
            $dovizkuru = DovizKuru::orderBy('tarih', 'desc')->take(3)->get();
            foreach ($dovizkuru as $doviz) {
                $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
            }
            return Response::json(array('durum' => true, 'ucretlendirme' => $ucretlendirme,'dovizkuru'=>$dovizkuru,'parabirimi'=>$parabirimi,'parabirimi2'=>$parabirimi2 ));
    } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'title' => 'Hatalı Bilgi Mevcut', 'text' => str_replace("'","\'",$e->getMessage()), 'type'=>'error'));
    }
}

    public function postDetayduzenle($arizafiyatid){
        try {
            DB::beginTransaction();
            $arizafiyat = ArizaFiyat::find($arizafiyatid);
            $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
            if ($sayacgelen->fiyatlandirma) {
                return Redirect::to($this->servisadi.'/ucretlendirmekayit')->with(array('mesaj' => 'true', 'title' => 'Sayaç Fiyatlandırması Güncellenemez', 'text' => 'Sayaç Zaten Ücretlendirilmiş!', 'type' => 'error'));
            }
            $bilgi = clone $arizafiyat;
            $fiyatdurum = Input::get('fiyatdurum');
            $garanti = Input::get('garanti');
            $ucretsiz = Input::get('ucretsiz');
            $genel = Input::get('genel');
            $ozel = Input::get('ozel');
            if (Input::has('indirim')) {
                $indirimdurum = true;
                $indirimorani = Input::get('indirimoran');
            } else {
                $indirimdurum = false;
                $indirimorani = 0;
            }
            $genelbirimler=json_decode(Input::get('genelbirimler'));
            $genelbirim="";
            foreach($genelbirimler as $birim){
                $genelbirim.=($genelbirim=="" ? "" : ",").$birim->id;
            }
            $ozelbirimler=json_decode(Input::get('ozelbirimler'));
            $ozelbirim="";
            foreach($ozelbirimler as $birim){
                $ozelbirim.=($ozelbirim=="" ? "" : ",").$birim->id;
            }
            $fiyat = Input::get('fiyattutar');
            $kdvsiztutar = Input::get('kdvsiztutar');
            $kdvtutar = Input::get('kdvtutar');
            $toplamtutar = Input::get('toplamtutar');
            $fiyat2 = Input::get('fiyattutar2');
            $kdvsiztutar2 = Input::get('kdvsiztutar2');
            $kdvtutar2 = Input::get('kdvtutar2');
            $toplamtutar2 = Input::get('toplamtutar2');
            $birim = Input::get('detaybirim');
            $birim2 = Input::get('detaybirim2')=="" ? null : Input::get('detaybirim2');
            $kurtarih = date('Y-m-d', strtotime(Input::get('detaykurtarih')));
            $arizafiyat->ariza_garanti = $garanti;
            $arizafiyat->fiyatdurum = $fiyatdurum;
            $arizafiyat->genel = $genel;
            $arizafiyat->ozel = $ozel;
            $arizafiyat->genelbirim = $genelbirim;
            $arizafiyat->ozelbirim = $ozelbirim;
            $arizafiyat->ucretsiz = $ucretsiz;
            $arizafiyat->fiyat = $fiyat;
            $arizafiyat->fiyat2 = $fiyat2;
            $arizafiyat->indirim = $indirimdurum;
            $arizafiyat->indirimorani = $indirimorani;
            $arizafiyat->tutar = $kdvsiztutar;
            $arizafiyat->tutar2 = $kdvsiztutar2;
            $arizafiyat->kdv = $kdvtutar;
            $arizafiyat->kdv2 = $kdvtutar2;
            $arizafiyat->toplamtutar = $toplamtutar;
            $arizafiyat->toplamtutar2 = $toplamtutar2;
            $arizafiyat->parabirimi_id = $birim;
            $arizafiyat->parabirimi2_id = $birim2;
            $arizafiyat->kurtarihi = $kurtarih;
            $arizafiyat->kullanici_id = Auth::user()->id;
            $arizafiyat->kayittarihi = date('Y-m-d H:i:s');
            $arizafiyat->save();
            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
            $arizakayit->garanti = $garanti;
            $arizakayit->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-check', $arizafiyat->id . ' Numaralı Fiyatlandırma Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Arıza Fiyat Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to('ucretlendirme/ucretlendirmekayit')->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Güncellendi', 'text' => 'Ücretlendirme Kayıdı Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Güncellenemedi', 'text' => 'Ücretlendirme Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function postUcretlendir()
    {
        try {
            $secilenler = Input::get('onizlemesecilenler');
            $secilenlist = explode(',', $secilenler);
            $toplamtutar = Input::get('onizlemetoplamtutar');
            $toplamtutar2 = Input::get('onizlemetoplamtutar2');
            $birim = Input::get('onizlemebirim');
            $birim2 = Input::get('onizlemebirim2')=="" ? null : Input::get('onizlemebirim2') ;
            $sayacsayisi = Input::get('onizlemeadet');
            $servisid = Input::get('onizlemeservis');
            $uretimyerid = Input::get('onizlemeuretimyer');
            $netsiscariid = Input::get('onizlemenetsiscari');
            $garanti = Input::get('onizlemegaranti');
            $kurtarih = date('Y-m-d', strtotime(Input::get('onizlemekurtarih')));
            $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenlist)->get();
            $servistakipler = ServisTakip::whereIn('arizafiyat_id', $secilenlist)->get();
            $tekrarucretlendirilenler = "";
            if ($servisid != "") {
                try {
                    DB::beginTransaction();
                    if (ArizaFiyat::whereIn('id', $secilenlist)->whereIn('durum', array(1, 3, 4))->get()->count() > 0) {
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Seçilen Sayaçlar Zaten Ücretlendirilmiş!', 'type' => 'warning'));
                    }
                    $sube = Sube::where('netsiscari_id', $netsiscariid)->where('aktif', 1)->first();
                    $subedurum = 0;
                    if($sube){
                        foreach ($servistakipler as $servistakip){
                            if($servistakip->subedurum)
                                $subedurum = 1;
                        }
                    }
                    try {
                        $ucretred = Ucretlendirilen::where('servis_id', $servisid)->where('uretimyer_id', $uretimyerid)->where('netsiscari_id', $netsiscariid)
                            ->where('durum', 3)->whereNull('tekrarkayittarihi')->whereNull('gerigonderimtarihi')->orderBy('kayittarihi', 'desc')->first();
                        if ($ucretred) //reddedilmiş o yere ait ucretlendirme varsa
                        {
                            $reddedilenler = $ucretred->reddedilenler;
                            $reddedilenlist = explode(',', $reddedilenler);
                            $yenireddedilenler = "";
                            foreach ($reddedilenlist as $reddedilen) {
                                $flag = 0;
                                foreach ($secilenlist as $secilen) {
                                    if ($reddedilen == $secilen) {
                                        $flag = 1;
                                        break;
                                    }
                                }
                                if ($flag == 0) //reddedilen ucretlendirilmemiş
                                {
                                    $yenireddedilenler .= ($yenireddedilenler == "" ? "" : ",") . $reddedilen;
                                } else {
                                    $tekrarucretlendirilenler .= ($tekrarucretlendirilenler == "" ? "" : ",") . $reddedilen;
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
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Reddedilen Ücretlendirme Tekrar Ücretlendirilirken Hata Oluştu', 'type' => 'error'));
                    }
                    if ($garanti == '1') //garanti içinde ucretlendirilenler sistem tarafından onaylanır
                    {
                        try {
                            $yetkiliid = Auth::user()->id;
                            if($servisid==6) {
                                $yetkili = Yetkili::where('netsiscari_id', $netsiscariid)->where('kullanici_id', $yetkiliid)->where('aktif', 1)->first();
                                if (!$yetkili) {
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Yetkili Bilgisi Bulunamadı!', 'text' => 'Bu Şube için Bu Kullanıcının Aktarma Yetkisi Yok!', 'type' => 'error'));
                                }
                                $sube = Sube::where('netsiscari_id', $yetkili->netsiscari_id)->where('aktif', 1)->first();
                                if (!$sube)
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube Bilgisi Bulunamadı!', 'text' => 'Bu Kullanıcı için Aktif Şube Bilgisi Bulunamadı!', 'type' => 'error'));
                                try {
                                    $ucretlendirilen = new Ucretlendirilen;
                                    $ucretlendirilen->servis_id = $servisid;
                                    $ucretlendirilen->uretimyer_id = $uretimyerid;
                                    $ucretlendirilen->netsiscari_id = $netsiscariid;
                                    $ucretlendirilen->garanti = $garanti;
                                    $ucretlendirilen->secilenler = $secilenler;
                                    $ucretlendirilen->sayacsayisi = $sayacsayisi;
                                    $ucretlendirilen->durum = 2;
                                    $ucretlendirilen->onaytipi = 3;
                                    $ucretlendirilen->fiyat = $toplamtutar;
                                    $ucretlendirilen->parabirimi_id = $birim;
                                    $ucretlendirilen->fiyat2 = $toplamtutar2;
                                    $ucretlendirilen->parabirimi2_id = $birim2;
                                    $ucretlendirilen->kullanici_id = Auth::user()->id;
                                    $ucretlendirilen->kayittarihi = date('Y-m-d H:i:s');
                                    $ucretlendirilen->kurtarihi = $kurtarih;
                                    $ucretlendirilen->onaytarihi = date('Y-m-d H:i:s');
                                    $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                                    $ucretlendirilen->yetkili_id = $yetkili->id;
                                    $ucretlendirilen->save();
                                    $onaylanan = new Onaylanan;
                                    $onaylanan->servis_id = $servisid;
                                    $onaylanan->uretimyer_id = $uretimyerid;
                                    $onaylanan->netsiscari_id = $netsiscariid;
                                    $onaylanan->ucretlendirilen_id = $ucretlendirilen->id;
                                    $onaylanan->yetkili_id = $yetkili->id;
                                    $onaylanan->onaytarihi = date('Y-m-d H:i:s');
                                    $onaylanan->onaylamatipi = 3;
                                    $onaylanan->save();
                                    $secilensayaclar = "";
                                    $secilensayaclist = array();
                                    try {
                                        foreach ($arizafiyatlar as $arizafiyat) {
                                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                            $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                                            array_push($secilensayaclist, $sayacgelen->id);
                                            $secilensayaclar .= ($secilensayaclar == "" ? "" : ",") . $sayacgelen->id;
                                            $sayacgelen->musterionay = 1;
                                            $sayacgelen->teslimdurum = 1;
                                            $sayacgelen->save();
                                            $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                                            $servistakip->ucretlendirilen_id = $ucretlendirilen->id;
                                            $servistakip->onaylanan_id = $onaylanan->id;
                                            $servistakip->durum = 5;
                                            $servistakip->ucretlendirmetarihi = date('Y-m-d H:i:s');
                                            $servistakip->onaylanmatarihi = date('Y-m-d H:i:s');
                                            $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                                            $servistakip->kullanici_id = Auth::user()->id;
                                            $servistakip->save();
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Aktarma Kaydedilemedi', 'text' => 'Sayaç Gelen ve Servis Takip Bilgisi Güncellenemedi', 'type' => 'error'));
                                    }
                                    try {
                                        // abone yoksa aktarma izin verme
                                        $sayacgelenler = SayacGelen::whereIn('id', $secilensayaclist)->get(array('serino'));
                                        $abonesayaclar = AboneSayac::whereIn('serino', $sayacgelenler->toArray())->get(array('id')); //sayacgelenler array değil gibi geliyor
                                        if ($abonesayaclar->count() == $sayacsayisi) { //ücretlendirilen sayaçların hepsi mevcut
                                            $abonetahsisler = AboneTahsis::whereIn('abonesayac_id', $abonesayaclar->toArray())->get();
                                            if ($abonetahsisler->count() == $sayacsayisi) {//sayaçların hepsi tahsisliyse
                                                try {
                                                    foreach ($abonetahsisler as $abonetahsis) {
                                                        $abone = Abone::find($abonetahsis->abone_id);
                                                        $abonesayac = AboneSayac::find($abonetahsis->abonesayac_id);
                                                        $abonesayacgelen = SayacGelen::whereIn('id', $secilensayaclist)->where('serino', $abonesayac->serino)->first();
                                                        if (!$abonesayacgelen) {
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı', 'text' => 'Ücretlendirilen sayaçlardan sayaç kayıdı olmayanlar mevcut!', 'type' => 'error'));
                                                        }
                                                        try {
                                                            $serviskayit = ServisKayit::where('abonetahsis_id', $abonetahsis->id)->where('durum', 0)->first();
                                                            if ($serviskayit) {
                                                                DB::rollBack();
                                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı', 'text' => 'Ücretlendirilen sayaçlardan servis kayıdı olan sayaçlar var!', 'type' => 'error'));
                                                            }
                                                            $serviskayit = new ServisKayit;
                                                            $serviskayit->subekodu = $sube->subekodu;
                                                            $serviskayit->kayitadres = $abonesayac->adres;
                                                            $serviskayit->abonetahsis_id = $abonetahsis->id;
                                                            $serviskayit->netsiscari_id = $abone->netsiscari_id;
                                                            $serviskayit->uretimyer_id = $abone->uretimyer_id;
                                                            $serviskayit->kullanici_id = Auth::user()->id;
                                                            $serviskayit->tipi = 1;
                                                            $serviskayit->acilmatarihi = $ucretlendirilen->onaytarihi;
                                                            $serviskayit->aciklama = $abonesayacgelen->sokulmenedeni;
                                                            $serviskayit->takilmatarihi = $abonesayacgelen->takilmatarihi;
                                                            $serviskayit->ilkendeks = $abonesayacgelen->endeks;
                                                            $serviskayit->sayacborcu = BackendController::SayacBorcuHesapla($abonesayacgelen);
                                                            $serviskayit->servissayaci = 1;
                                                            $serviskayit->save();
                                                            $abonesayac->tamirdurum = 2;
                                                            $abonesayac->save();
                                                        } catch (Exception $e) {
                                                            Log::error($e);
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı', 'text' => 'Ücretlendirilen sayaçlar abone teslimine kaydedilemedi!', 'type' => 'error'));
                                                        }
                                                    }
                                                } catch (Exception $e) {
                                                    Log::error($e);
                                                    DB::rollBack();
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı abone teslimine kaydedilemedi!', 'type' => 'error'));
                                                }
                                                BackendController::HatirlatmaGuncelle(4, $netsiscariid, $servisid, $sayacsayisi);
                                                BackendController::HatirlatmaEkle(13, $netsiscariid, $servisid, $sayacsayisi);
                                                BackendController::BildirimEkle(4, $netsiscariid, $servisid, $sayacsayisi);
                                                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Yetkili Tarafından Aktarıldı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                                                DB::commit();
                                                return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Aktarımı Tamamlandı', 'text' => 'Aktarma Başarıyla Yapıldı', 'type' => 'success'));
                                            } else {//tahsisli olmayan sayaç mevcut
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı aboneye tahsisli değil. Önce tahsis işlemlerinin tamamlanması gerekiyor!', 'type' => 'error'));
                                            }
                                        } else {//listede olmayan sayaç mevcut
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı aboneye tahsisli değil. Önce tahsis işlemlerinin tamamlanması gerekiyor!', 'type' => 'error'));
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Aktarma Kaydedilemedi', 'text' => 'Servis Kayıdı Girilemedi', 'type' => 'error'));
                                    }
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Aktarma Hatası', 'text' => 'Aktarma onaylananlara kaydedilemedi', 'type' => 'error'));
                                }
                            }else{
                                $ucretlendirilen = new Ucretlendirilen;
                                $ucretlendirilen->servis_id = $servisid;
                                $ucretlendirilen->uretimyer_id = $uretimyerid;
                                $ucretlendirilen->netsiscari_id = $netsiscariid;
                                $ucretlendirilen->garanti = $garanti;
                                $ucretlendirilen->secilenler = $secilenler;
                                $ucretlendirilen->sayacsayisi = $sayacsayisi;
                                $ucretlendirilen->durum = 2;
                                $ucretlendirilen->onaytipi = 0;
                                $ucretlendirilen->fiyat = $toplamtutar;
                                $ucretlendirilen->parabirimi_id = $birim;
                                $ucretlendirilen->fiyat2 = $toplamtutar2;
                                $ucretlendirilen->parabirimi2_id = $birim2;
                                $ucretlendirilen->kullanici_id = Auth::user()->id;
                                $ucretlendirilen->kayittarihi = date('Y-m-d H:i:s');
                                $ucretlendirilen->kurtarihi = $kurtarih;
                                $ucretlendirilen->onaytarihi = date('Y-m-d H:i:s');
                                $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                                $ucretlendirilen->yetkili_id = 1;
                                $ucretlendirilen->save();
                                try {
                                    $onaylanan = new Onaylanan;
                                    $onaylanan->servis_id = $servisid;
                                    $onaylanan->uretimyer_id = $uretimyerid;
                                    $onaylanan->netsiscari_id = $netsiscariid;
                                    $onaylanan->ucretlendirilen_id = $ucretlendirilen->id;
                                    $onaylanan->yetkili_id = 1;
                                    $onaylanan->onaytarihi = date('Y-m-d H:i:s');
                                    $onaylanan->onaylamatipi = 0;
                                    $onaylanan->save();
                                    $sayilar[$sayacsayisi] = array();
                                    $i = 0;
                                    $secilensayaclar = "";
                                    $secilensayaclist = array();
                                    $garantisayaclar = "";
                                    $garantisayaclist = array();
                                    $iadesayaclar = "";
                                    $iadesayaclist = array();
                                    try {
                                        $toplamtutarkontrol=0;
                                        $toplamtutar2kontrol=0;
                                        foreach ($arizafiyatlar as $arizafiyat) {
                                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                            $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                                            if ($sayacgelen->fiyatlandirma) {
                                                DB::rollBack();
                                                return Redirect::to($this->servisadi.'/ucretlendirmekayit')->with(array('mesaj' => 'true', 'title' => 'Sayaç Fiyatlandırması Yapılamaz', 'text' => $sayacgelen->serino.' Nolu Sayaç Zaten Ücretlendirilmiş!', 'type' => 'error'));
                                            }
                                            if($arizakayit->arizakayit_durum==7){
                                                array_push($iadesayaclist, $sayacgelen->id);
                                                $iadesayaclar .= ($iadesayaclar == "" ? "" : ",") . $sayacgelen->id;
                                            }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0) {
                                                array_push($secilensayaclist, $sayacgelen->id);
                                                $secilensayaclar .= ($secilensayaclar == "" ? "" : ",") . $sayacgelen->id;
                                            }else{
                                                array_push($garantisayaclist, $sayacgelen->id);
                                                $garantisayaclar .= ($garantisayaclar == "" ? "" : ",") . $sayacgelen->id;
                                            }
                                            $sayilar[$i] = $sayacgelen->stokkodu;
                                            $servistakip = ServisTakip::where('arizakayit_id', $arizafiyat->arizakayit_id)->first();
                                            $arizafiyat->kurtarihi = $kurtarih;
                                            $kur = 1;
                                            if ($birim != $arizafiyat->parabirimi_id) {
                                                if ($birim == "1") { //tl
                                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                                                } else { //euro dolar sterln
                                                    if ($arizafiyat->parabirimi_id == "1") {
                                                        $kur = 1 / BackendController::getKurBilgisi($birim, $kurtarih);
                                                    } else {
                                                        $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($birim, $kurtarih);
                                                    }
                                                }
                                            }
                                            $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                                            if ($arizafiyat->parabirimi2_id == $birim) {
                                                $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                                $arizafiyat->fiyat2 = 0;
                                                $arizafiyat->parabirimi2_id = null;
                                            }
                                            $indirim = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat * $arizafiyat->indirimorani) / 100 : 0;
                                            $indirim2 = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat2 * $arizafiyat->indirimorani) / 100 : 0;
                                            $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                                            $arizafiyat->tutar2 = $arizafiyat->fiyat2 - $indirim2;
                                            $arizafiyat->kdv = ($arizafiyat->tutar * 18) / 100;
                                            $arizafiyat->kdv2 = ($arizafiyat->tutar2 * 18) / 100;
                                            $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                                            $arizafiyat->toplamtutar2 = round(($arizafiyat->tutar2 + $arizafiyat->kdv2) * 2) / 2;
                                            $arizafiyat->parabirimi_id = $birim;
                                            if ($servisid != 6) //şubede ücretlendirilirse subedurum=1 kalacak diğer durumda 0 olacak
                                                $arizafiyat->subedurum = 0;
                                            else
                                                $arizafiyat->subedurum = $subedurum;
                                            $arizafiyat->durum = 1;
                                            $arizafiyat->save();
                                            $toplamtutarkontrol+=$arizafiyat->toplamtutar;
                                            $toplamtutar2kontrol+=$arizafiyat->toplamtutar2;
                                            $sayacgelen->fiyatlandirma = 1;
                                            $sayacgelen->musterionay = 1;
                                            $sayacgelen->teslimdurum = 1;
                                            $sayacgelen->save();
                                            if (!$servistakip) {
                                                $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                $servistakip->arizakayit_id = $arizafiyat->arizakayit_id;
                                                $servistakip->arizafiyat_id = $arizafiyat->id;
                                            }
                                            $servistakip->ucretlendirilen_id = $ucretlendirilen->id;
                                            $servistakip->onaylanan_id = $onaylanan->id;
                                            $servistakip->durum = 5;
                                            $servistakip->ucretlendirmetarihi = date('Y-m-d H:i:s');
                                            $servistakip->onaylanmatarihi = date('Y-m-d H:i:s');
                                            $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                                            $servistakip->kullanici_id = Auth::user()->id;
                                            $servistakip->save();
                                            $i++;
                                        }
                                        if($toplamtutar!=$toplamtutarkontrol || $toplamtutar2!=$toplamtutar2kontrol){
                                            DB::rollBack();
                                            Log::error($toplamtutar.'<->'.$toplamtutarkontrol.' yada '.$toplamtutar2.'<->'.$toplamtutar2kontrol.' eşleşmemiş.');
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Kaydedilemedi', 'text' => 'Ücretlendirmede belirlenen toplam tutar ile kalemlerim toplam tutarı eşleşmiyor!('.$toplamtutar.'<->'.$toplamtutarkontrol.' ya da '.$toplamtutar2.'<->'.$toplamtutar2kontrol.')', 'type' => 'error'));
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Kaydedilemedi', 'text' => 'Servistakip ve Sayaç Gelen Bilgileri Güncellenemedi', 'type' => 'error'));
                                    }
                                    $tekrarucretlendirilenlist = array();
                                    if ($tekrarucretlendirilenler != "")//tekrar ucretlendirilen var
                                    {
                                        try {
                                            $tekrarucretlendirilenlist = explode(",", $tekrarucretlendirilenler);
                                            $arizafiyattekrar = ArizaFiyat::whereIn('id', $tekrarucretlendirilenlist)->get();
                                            foreach ($arizafiyattekrar as $fiyat) {
                                                if ($servisid != 6) //şubede ücretlendirilirse subedurum=1 kalacak diğer durumda 0 olacak
                                                    $fiyat->subedurum = 0;
                                                else
                                                    $fiyat->subedurum = $subedurum;
                                                $fiyat->durum = 3;
                                                $fiyat->tekrarkayittarihi = date('Y-m-d H:i:s');
                                                $fiyat->save();
                                            }
                                            $count = count($tekrarucretlendirilenlist);
                                            BackendController::HatirlatmaGuncelle(7, $netsiscariid, $servisid, $count);
                                        } catch (Exception $e) {
                                            DB::rollBack();
                                            Log::error($e);
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Kaydedilemedi', 'text' => 'Tekrar Ücretlendirilen Fiyatlandırmalar Güncellenemedi', 'type' => 'error'));
                                        }
                                    }
                                    $yenisecilenlist = array();
                                    $yenisecilenler = "";
                                    foreach ($secilenlist as $secilen) {
                                        $flag = 0;
                                        foreach ($tekrarucretlendirilenlist as $tekrar) {
                                            if ($tekrar == $secilen) {
                                                $flag = 1;
                                                break;
                                            }
                                        }
                                        if (!$flag) {
                                            $yenisecilenler .= ($yenisecilenler == "" ? "" : ",") . $secilen;
                                            array_push($yenisecilenlist, $secilen);
                                        }
                                    }
                                    BackendController::HatirlatmaGuncelle(4, $netsiscariid, $servisid, count($yenisecilenlist));
                                    if ($servisid == 5) //kalibrasyon tamamlanmışsa depo teslim yoksa bekleyecek
                                    {
                                        try {
                                            $kalibrasyonsayi=0;
                                            $kalibrasyonsayaclist="";
                                            $garantisayi=0;
                                            $garantisayaclist="";
                                            $geriiadesayi=0;
                                            $geriiadesayaclist="";
                                            $periyodiksayi=0;
                                            $periyodiklist="";
                                            foreach ($arizafiyatlar as $arizafiyat) {
                                                $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                                                $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                                $depogelen=DepoGelen::find($sayacgelen->depogelen_id);
                                                if($arizakayit->arizakayit_durum != 3) { //yedek parça bekliyorsa depo teslim olmaz
                                                    if ($depogelen->periyodik) {
                                                        if ($sayacgelen->kalibrasyon) {
                                                            $periyodiksayi++;
                                                            $periyodiklist .= ($periyodiklist == "" ? "" : ",") . $sayacgelen->id;
                                                        } else {
                                                            $sayacgelen->teslimdurum = 0;
                                                            $sayacgelen->save();
                                                        }
                                                    } else {
                                                        if ($arizakayit->arizakayit_durum == 7) {
                                                            $geriiadesayi++;
                                                            $geriiadesayaclist .= ($geriiadesayaclist == "" ? "" : ",") . $sayacgelen->id;
                                                        } else if ($sayacgelen->kalibrasyon) {
                                                            if ($arizafiyat->toplamtutar > 0 || $arizafiyat->toplamtutar2 > 0) {
                                                                $kalibrasyonsayi++;
                                                                $kalibrasyonsayaclist .= ($kalibrasyonsayaclist == "" ? "" : ",") . $sayacgelen->id;
                                                            } else {
                                                                $garantisayi++;
                                                                $garantisayaclist .= ($garantisayaclist == "" ? "" : ",") . $sayacgelen->id;
                                                            }
                                                        } else {
                                                            $sayacgelen->teslimdurum = 0;
                                                            $sayacgelen->save();
                                                        }
                                                    }
                                                }else{
                                                    $sayacgelen->teslimdurum = 0;
                                                    $sayacgelen->save();
                                                }
                                            }
                                            if($kalibrasyonsayi>0){
                                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                                    ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',0)->first();
                                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                {
                                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                                    $kalibrasyonsayaclar = explode(',', $kalibrasyonsayaclist);
                                                    $secilenler="";
                                                    $adet = 0;
                                                    foreach($kalibrasyonsayaclar as $sayacgelenid){
                                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                            $adet++;
                                                        }
                                                    }
                                                    $kalibrasyonsayi = $adet;
                                                    if ($adet>0) {
                                                        $depoteslim->secilenler .= ',' . $secilenler;
                                                        $depoteslim->sayacsayisi += $adet;
                                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                        $depoteslim->save();
                                                    }
                                                } else { //yeni depo teslimatı yapılacak
                                                    $depoteslim = new DepoTeslim;
                                                    $depoteslim->servis_id = $servisid;
                                                    $depoteslim->netsiscari_id = $netsiscariid;
                                                    $depoteslim->secilenler = $kalibrasyonsayaclist;
                                                    $depoteslim->sayacsayisi = $kalibrasyonsayi;
                                                    $depoteslim->depodurum = 0;
                                                    $depoteslim->parabirimi_id=$birim;
                                                    $depoteslim->parabirimi2_id=$birim2;
                                                    $depoteslim->save();
                                                }
                                            }
                                            if($garantisayi>0){
                                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                                    ->where('depodurum',0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',0)->first();
                                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                {
                                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                                    $garantisayaclar = explode(',', $garantisayaclist);
                                                    $secilenler="";
                                                    $adet = 0;
                                                    foreach($garantisayaclar as $sayacgelenid){
                                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                            $adet++;
                                                        }
                                                    }
                                                    $garantisayi = $adet;
                                                    if ($adet>0) {
                                                        $depoteslim->secilenler .= ',' . $secilenler;
                                                        $depoteslim->sayacsayisi += $adet;
                                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                        $depoteslim->save();
                                                    }
                                                } else { //yeni depo teslimatı yapılacak
                                                    $depoteslim = new DepoTeslim;
                                                    $depoteslim->servis_id = $servisid;
                                                    $depoteslim->netsiscari_id = $netsiscariid;
                                                    $depoteslim->secilenler = $garantisayaclist;
                                                    $depoteslim->sayacsayisi = $garantisayi;
                                                    $depoteslim->depodurum = 0;
                                                    $depoteslim->tipi = 1;
                                                    $depoteslim->parabirimi_id=$birim;
                                                    $depoteslim->parabirimi2_id=$birim2;
                                                    $depoteslim->save();
                                                }
                                            }
                                            if($geriiadesayi>0){
                                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                                    ->where('depodurum',0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',0)->first();
                                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                {
                                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                                    $geriiadesayaclar = explode(',', $geriiadesayaclist);
                                                    $secilenler="";
                                                    $adet = 0;
                                                    foreach($geriiadesayaclar as $sayacgelenid){
                                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                            $adet++;
                                                        }
                                                    }
                                                    $geriiadesayi = $adet;
                                                    if ($adet>0) {
                                                        $depoteslim->secilenler .= ',' . $secilenler;
                                                        $depoteslim->sayacsayisi += $adet;
                                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                        $depoteslim->save();
                                                    }
                                                } else { //yeni depo teslimatı yapılacak
                                                    $depoteslim = new DepoTeslim;
                                                    $depoteslim->servis_id = $servisid;
                                                    $depoteslim->netsiscari_id = $netsiscariid;
                                                    $depoteslim->secilenler = $geriiadesayaclist;
                                                    $depoteslim->sayacsayisi = $geriiadesayi;
                                                    $depoteslim->depodurum = 0;
                                                    $depoteslim->tipi = 2;
                                                    $depoteslim->parabirimi_id=$birim;
                                                    $depoteslim->parabirimi2_id=$birim2;
                                                    $depoteslim->save();
                                                }
                                            }
                                            if($periyodiksayi>0){
                                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                                    ->where('depodurum',0)->where('tipi',0)->where('periyodik',1)->where('subegonderim',0)->first();
                                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                {
                                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                                    $periyodiksayaclar = explode(',', $periyodiklist);
                                                    $secilenler="";
                                                    $adet = 0;
                                                    foreach($periyodiksayaclar as $sayacgelenid){
                                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                            $adet++;
                                                        }
                                                    }
                                                    $periyodiksayi = $adet;
                                                    if ($adet>0) {
                                                        $depoteslim->secilenler .= ',' . $secilenler;
                                                        $depoteslim->sayacsayisi += $adet;
                                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                        $depoteslim->save();
                                                    }
                                                } else { //yeni depo teslimatı yapılacak
                                                    $depoteslim = new DepoTeslim;
                                                    $depoteslim->servis_id = $servisid;
                                                    $depoteslim->netsiscari_id = $netsiscariid;
                                                    $depoteslim->secilenler = $periyodiklist;
                                                    $depoteslim->sayacsayisi = $periyodiksayi;
                                                    $depoteslim->depodurum = 0;
                                                    $depoteslim->periyodik = 1;
                                                    $depoteslim->parabirimi_id=$birim;
                                                    $depoteslim->parabirimi2_id=$birim2;
                                                    $depoteslim->save();
                                                }
                                            }
                                        } catch (Exception $e) {
                                            DB::rollBack();
                                            Log::error($e);
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi.', 'type' => 'error'));
                                        }
                                        if(($kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi)>0)
                                            BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi);
                                        BackendController::BildirimEkle(4, $netsiscariid, $servisid, $sayacsayisi);
                                        BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Sistem Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                                        DB::commit();
                                        return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sistem Tarafından Onaylama Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                                    } else {
                                        try {
                                            if(count($secilensayaclist)>0){
                                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                                    ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                {

                                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                                    $secilenler="";
                                                    $adet = 0;
                                                    foreach($secilensayaclist as $sayacgelenid){
                                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                            $adet++;
                                                        }
                                                    }
                                                    if ($adet>0) {
                                                        $depoteslim->secilenler .= ',' . $secilenler;
                                                        $depoteslim->sayacsayisi += $adet;
                                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                        $depoteslim->save();
                                                    }
                                                } else { //yeni depo teslimatı yapılacak
                                                    $depoteslim = new DepoTeslim;
                                                    $depoteslim->servis_id = $servisid;
                                                    $depoteslim->netsiscari_id = $netsiscariid;
                                                    $depoteslim->secilenler = $secilensayaclar;
                                                    $depoteslim->sayacsayisi = count($secilensayaclist);
                                                    $depoteslim->depodurum = 0;
                                                    $depoteslim->parabirimi_id=$birim;
                                                    $depoteslim->parabirimi2_id=$birim2;
                                                    $depoteslim->save();
                                                }
                                            }
                                            if(count($garantisayaclist)>0){
                                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                                    ->where('depodurum', 0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                {
                                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                                    $secilenler="";
                                                    $adet = 0;
                                                    foreach($garantisayaclist as $sayacgelenid){
                                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                            $adet++;
                                                        }
                                                    }
                                                    if ($adet>0) {
                                                        $depoteslim->secilenler .= ',' . $secilenler;
                                                        $depoteslim->sayacsayisi += $adet;
                                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                        $depoteslim->save();
                                                    }
                                                } else { //yeni depo teslimatı yapılacak
                                                    $depoteslim = new DepoTeslim;
                                                    $depoteslim->servis_id = $servisid;
                                                    $depoteslim->netsiscari_id = $netsiscariid;
                                                    $depoteslim->secilenler = $garantisayaclar;
                                                    $depoteslim->sayacsayisi = count($garantisayaclist);
                                                    $depoteslim->depodurum = 0;
                                                    $depoteslim->tipi = 1;
                                                    $depoteslim->parabirimi_id=$birim;
                                                    $depoteslim->parabirimi2_id=$birim2;
                                                    $depoteslim->save();
                                                }
                                            }
                                            if(count($iadesayaclist)>0){
                                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                                    ->where('depodurum', 0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                                {
                                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                                    $secilenler="";
                                                    $adet = 0;
                                                    foreach($iadesayaclist as $sayacgelenid){
                                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                            $adet++;
                                                        }
                                                    }
                                                    if ($adet>0) {
                                                        $depoteslim->secilenler .= ',' . $secilenler;
                                                        $depoteslim->sayacsayisi += $adet;
                                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                        $depoteslim->save();
                                                    }
                                                } else { //yeni depo teslimatı yapılacak
                                                    $depoteslim = new DepoTeslim;
                                                    $depoteslim->servis_id = $servisid;
                                                    $depoteslim->netsiscari_id = $netsiscariid;
                                                    $depoteslim->secilenler = $iadesayaclar;
                                                    $depoteslim->sayacsayisi = count($iadesayaclist);
                                                    $depoteslim->depodurum = 0;
                                                    $depoteslim->tipi = 2;
                                                    $depoteslim->subegonderim=$subedurum;
                                                    $depoteslim->parabirimi_id=$birim;
                                                    $depoteslim->parabirimi2_id=$birim2;
                                                    $depoteslim->save();
                                                }
                                            }
                                            BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $sayacsayisi);
                                            BackendController::BildirimEkle(4, $netsiscariid, $servisid, $sayacsayisi);
                                            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-check', $ucretlendirilen->id . ' Numaralı Ücretlendirme Sistem Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                                            DB::commit();
                                            return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sistem Tarafından Onaylama Yapıldı', 'text' => 'Garanti içinde olduğundan Sistem tarafından onaylama başarıyla yapıldı', 'type' => 'success'));
                                        } catch (Exception $e) {
                                            DB::rollBack();
                                            Log::error($e);
                                            return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Fiyatlandırma Kaydedilemedi', 'text' => 'Garanti içinde olduğundan Depo Teslimi Sırasında Hata Oluştu', 'type' => 'error'));
                                        }
                                    }
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Kaydedilemedi', 'text' => 'Garanti içinde olduğundan Sistem tarafından Onaylanırken Hata oluştu', 'type' => 'error'));
                                }
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Kaydedilemedi', 'text' => 'Garanti içinde olduğundan Sistem tarafından Onaylanırken Hata oluştu', 'type' => 'error'));
                        }
                    } else {
                        try {
                            $kayit = Ucretlendirilen::where('servis_id', $servisid)->where('uretimyer_id', $uretimyerid)->where('netsiscari_id', $netsiscariid)->where('durum', 0)->first();
                            if ($kayit) //gönderilmemiş o yere ait ucretlendirme varsa birleştir
                            {
                                try {
                                    $eskisecilenler = $kayit->secilenler;
                                    $eskigaranti = $kayit->garanti;
                                    $eskisayi = $kayit->sayacsayisi;
                                    $eskifiyatlar = BackendController::getUcretlendirmeFiyat($eskisecilenler, $kurtarih, $birim, $birim2);
                                    if ($eskifiyatlar['durum']) {
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => $eskifiyatlar['error'], 'type' => 'error'));
                                    }
                                    $eskifiyat = $eskifiyatlar['fiyat'];
                                    $eskifiyat2 = $eskifiyatlar['fiyat2'];
                                    $birim2 = $eskifiyatlar['yenibirim'] == null ? $birim2 : $eskifiyatlar['yenibirim'];
                                    if ($eskigaranti != 0)
                                        $kayit->garanti = $garanti;
                                    $kayit->secilenler = $eskisecilenler . ',' . $secilenler;
                                    $kayit->sayacsayisi = $eskisayi + $sayacsayisi;
                                    $kayit->fiyat = $eskifiyat + $toplamtutar;
                                    $kayit->fiyat2 = $eskifiyat2 + $toplamtutar2;
                                    $kayit->kurtarihi = $kurtarih;
                                    $kayit->parabirimi_id = $birim;
                                    $kayit->parabirimi2_id = $birim2;
                                    $kayit->save();
                                    $sayilar[$sayacsayisi] = array();
                                    $i = 0;
                                    try {
                                        foreach ($arizafiyatlar as $arizafiyat) {
                                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                            $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                                            if ($sayacgelen->fiyatlandirma) {
                                                DB::rollBack();
                                                return Redirect::to($this->servisadi.'/ucretlendirmekayit')->with(array('mesaj' => 'true', 'title' => 'Sayaç Fiyatlandırması Yapılamaz', 'text' => $sayacgelen->serino.' Nolu Sayaç Zaten Ücretlendirilmiş!', 'type' => 'error'));
                                            }
                                            $sayilar[$i] = $sayacgelen->stokkodu;
                                            $servistakip = ServisTakip::where('arizakayit_id', $arizafiyat->arizakayit_id)->first();
                                            $arizafiyat->durum = 1;
                                            $arizafiyat->kurtarihi = $kurtarih;
                                            $kur = 1;
                                            if ($birim != $arizafiyat->parabirimi_id) {
                                                if ($birim == "1") { //tl
                                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                                                } else { //euro dolar sterln
                                                    if ($arizafiyat->parabirimi_id == "1") {
                                                        $kur = 1 / BackendController::getKurBilgisi($birim, $kurtarih);
                                                    } else {
                                                        $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($birim, $kurtarih);
                                                    }
                                                }
                                            }
                                            $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                                            if ($arizafiyat->parabirimi2_id == $birim) {
                                                $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                                $arizafiyat->fiyat2 = 0;
                                                $arizafiyat->parabirimi2_id = null;
                                            }
                                            $indirim = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat * $arizafiyat->indirimorani) / 100 : 0;
                                            $indirim2 = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat2 * $arizafiyat->indirimorani) / 100 : 0;
                                            $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                                            $arizafiyat->tutar2 = $arizafiyat->fiyat2 - $indirim2;
                                            $arizafiyat->kdv = ($arizafiyat->tutar * 18) / 100;
                                            $arizafiyat->kdv2 = ($arizafiyat->tutar2 * 18) / 100;
                                            $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                                            $arizafiyat->toplamtutar2 = round(($arizafiyat->tutar2 + $arizafiyat->kdv2) * 2) / 2;
                                            $arizafiyat->parabirimi_id = $birim;
                                            if ($servisid != 6) //şubede ücretlendirilirse subedurum=1 kalacak diğer durumda 0 olacak
                                                $arizafiyat->subedurum = 0;
                                            else
                                                $arizafiyat->subedurum = $subedurum;
                                            $arizafiyat->save();
                                            $sayacgelen->fiyatlandirma = 1;
                                            $sayacgelen->save();
                                            if (!$servistakip) {
                                                $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                $servistakip->arizakayit_id = $arizafiyat->arizakayit_id;
                                                $servistakip->arizafiyat_id = $arizafiyat->id;
                                            }
                                            $servistakip->ucretlendirilen_id = $kayit->id;
                                            $servistakip->durum = 3;
                                            $servistakip->ucretlendirmetarihi = date('Y-m-d H:i:s');
                                            $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                                            $servistakip->kullanici_id = Auth::user()->id;
                                            $servistakip->save();
                                            $i++;
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Sayaç Gelen ve Servis Takip Bilgisi Güncellenemedi', 'type' => 'error'));
                                    }
                                    try {
                                        if ($tekrarucretlendirilenler != "")//tekrar ucretlendirilen var
                                        {
                                            $tekrarucretlendirilenlist = explode(",", $tekrarucretlendirilenler);
                                            $arizafiyattekrar = ArizaFiyat::whereIn('id', $tekrarucretlendirilenlist)->get();
                                            foreach ($arizafiyattekrar as $fiyat) {
                                                if ($servisid != 6) //şubede ücretlendirilirse subedurum=1 kalacak diğer durumda 0 olacak
                                                    $fiyat->subedurum = 0;
                                                else
                                                    $fiyat->subedurum = $subedurum;
                                                $fiyat->durum = 3;
                                                $fiyat->tekrarkayittarihi = date('Y-m-d H:i:s');
                                                $fiyat->save();
                                            }
                                            $count = count($tekrarucretlendirilenlist);
                                            BackendController::HatirlatmaGuncelle(7, $kayit->netsiscari_id, $kayit->servis_id, $count);
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Tekrar Ücretlendirme Yapılan Fiyatlandırmalar Güncellenemedi', 'type' => 'error'));
                                    }
                                    $tekrarucretlendirilenlist = explode(',', $tekrarucretlendirilenler);
                                    $yenisecilenlist = array();
                                    foreach ($secilenlist as $secilen) {
                                        $flag = 0;
                                        foreach ($tekrarucretlendirilenlist as $tekrar) {
                                            if ($tekrar == $secilen) {
                                                $flag = 1;
                                                break;
                                            }
                                        }
                                        if (!$flag) {
                                            array_push($yenisecilenlist, $secilen);
                                        }
                                    }
                                    BackendController::HatirlatmaGuncelle(4, $netsiscariid, $servisid, count($yenisecilenlist));
                                    BackendController::HatirlatmaEkle(5, $netsiscariid, $servisid, $sayacsayisi);
                                    BackendController::BildirimEkle(4, $netsiscariid, $servisid, $sayacsayisi);
                                    BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-check', $kayit->id . ' Numaralı Ücretlendirme Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $kayit->id);
                                    DB::commit();
                                    return Redirect::to('ucretlendirme/ucretlendirmekayit')->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapıldı', 'text' => 'Ücretlendirme Başarıyla Yapıldı', 'type' => 'success'));
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Ücretlendirme Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                }
                            } else { //o yere ait ucretlendirme yoksa yeni oluştur
                                try {
                                    $ucretlendirilen = new Ucretlendirilen;
                                    $ucretlendirilen->servis_id = $servisid;
                                    $ucretlendirilen->uretimyer_id = $uretimyerid;
                                    $ucretlendirilen->netsiscari_id = $netsiscariid;
                                    $ucretlendirilen->garanti = $garanti;
                                    $ucretlendirilen->secilenler = $secilenler;
                                    $ucretlendirilen->sayacsayisi = $sayacsayisi;
                                    $ucretlendirilen->durum = 0;
                                    $ucretlendirilen->fiyat = $toplamtutar;
                                    $ucretlendirilen->parabirimi_id = $birim;
                                    $ucretlendirilen->fiyat2 = $toplamtutar2;
                                    $ucretlendirilen->parabirimi2_id = $birim2;
                                    $ucretlendirilen->kullanici_id = Auth::user()->id;
                                    $ucretlendirilen->kayittarihi = date('Y-m-d H:i:s');
                                    $ucretlendirilen->kurtarihi = $kurtarih;
                                    $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                                    $ucretlendirilen->save();
                                    $sayilar[$sayacsayisi] = array();
                                    $i = 0;
                                    try {
                                        $toplamtutarkontrol=0;
                                        $toplamtutar2kontrol=0;
                                        foreach ($arizafiyatlar as $arizafiyat) {
                                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                            $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                                            if ($sayacgelen->fiyatlandirma) {
                                                DB::rollBack();
                                                return Redirect::to($this->servisadi.'/ucretlendirmekayit')->with(array('mesaj' => 'true', 'title' => 'Sayaç Fiyatlandırması Yapılamaz', 'text' => $sayacgelen->serino.' Nolu Sayaç Zaten Ücretlendirilmiş!', 'type' => 'error'));
                                            }
                                            $sayilar[$i] = $sayacgelen->stokkodu;
                                            $servistakip = ServisTakip::where('arizakayit_id', $arizafiyat->arizakayit_id)->first();
                                            $arizafiyat->kurtarihi = $kurtarih;
                                            $kur = 1;
                                            if ($birim != $arizafiyat->parabirimi_id) {
                                                if ($birim == "1") { //tl
                                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                                                } else { //euro dolar sterln
                                                    if ($arizafiyat->parabirimi_id == "1") {
                                                        $kur = 1 / BackendController::getKurBilgisi($birim, $kurtarih);
                                                    } else {
                                                        $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($birim, $kurtarih);
                                                    }
                                                }
                                            }
                                            $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                                            if ($arizafiyat->parabirimi2_id == $birim) {
                                                $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                                $arizafiyat->fiyat2 = 0;
                                                $arizafiyat->parabirimi2_id = null;
                                            }
                                            $indirim = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat * $arizafiyat->indirimorani) / 100 : 0;
                                            $indirim2 = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat2 * $arizafiyat->indirimorani) / 100 : 0;
                                            $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                                            $arizafiyat->tutar2 = $arizafiyat->fiyat2 - $indirim2;
                                            $arizafiyat->kdv = ($arizafiyat->tutar * 18) / 100;
                                            $arizafiyat->kdv2 = ($arizafiyat->tutar2 * 18) / 100;
                                            $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                                            $arizafiyat->toplamtutar2 = round(($arizafiyat->tutar2 + $arizafiyat->kdv2) * 2) / 2;
                                            $arizafiyat->parabirimi_id = $birim;
                                            if ($servisid != 6) //şubede ücretlendirilirse subedurum=1 kalacak diğer durumda 0 olacak
                                                $arizafiyat->subedurum = 0;
                                            else
                                                $arizafiyat->subedurum = $subedurum;
                                            $arizafiyat->durum = 1;
                                            $arizafiyat->save();
                                            $toplamtutarkontrol+=$arizafiyat->toplamtutar;
                                            $toplamtutar2kontrol+=$arizafiyat->toplamtutar2;
                                            $sayacgelen->fiyatlandirma = 1;
                                            $sayacgelen->save();
                                            if (!$servistakip) {
                                                $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                $servistakip->arizakayit_id = $arizafiyat->arizakayit_id;
                                                $servistakip->arizafiyat_id = $arizafiyat->id;
                                            }
                                            $servistakip->ucretlendirilen_id = $ucretlendirilen->id;
                                            $servistakip->durum = 3;
                                            $servistakip->ucretlendirmetarihi = date('Y-m-d H:i:s');
                                            $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                                            $servistakip->kullanici_id = Auth::user()->id;
                                            $servistakip->save();
                                            $i++;
                                        }
                                        if($toplamtutar!=$toplamtutarkontrol || $toplamtutar2!=$toplamtutar2kontrol){
                                            DB::rollBack();
                                            Log::error($toplamtutar.'<->'.$toplamtutarkontrol.' yada '.$toplamtutar2.'<->'.$toplamtutar2kontrol.' eşleşmemiş.');
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Kaydedilemedi', 'text' => 'Ücretlendirmede belirlenen toplam tutar ile kalemlerim toplam tutarı eşleşmiyor!('.$toplamtutar.'<->'.$toplamtutarkontrol.' ya da '.$toplamtutar2.'<->'.$toplamtutar2kontrol.')', 'type' => 'error'));
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Sayaç Gelen ve Servis Takip Bilgisi Güncellenemedi', 'type' => 'error'));
                                    }
                                    try {
                                        if ($tekrarucretlendirilenler != "")//tekrar ucretlendirilen var
                                        {
                                            $tekrarucretlendirilenlist = explode(",", $tekrarucretlendirilenler);
                                            $arizafiyattekrar = ArizaFiyat::whereIn('id', $tekrarucretlendirilenlist)->get();
                                            foreach ($arizafiyattekrar as $fiyat) {
                                                if ($servisid != 6) //şubede ücretlendirilirse subedurum=1 kalacak diğer durumda 0 olacak
                                                    $fiyat->subedurum = 0;
                                                else
                                                    $fiyat->subedurum = $subedurum;
                                                $fiyat->durum = 3;
                                                $fiyat->tekrarkayittarihi = date('Y-m-d H:i:s');
                                                $fiyat->save();
                                            }
                                            $count = count($tekrarucretlendirilenlist);
                                            BackendController::HatirlatmaGuncelle(7, $netsiscariid, $servisid, $count);
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Tekrar Ücretlendirme Yapılan Fiyatlandırmalar Güncellenemedi', 'type' => 'error'));
                                    }
                                    $tekrarucretlendirilenlist = explode(',', $tekrarucretlendirilenler);
                                    $yenisecilenlist = array();
                                    foreach ($secilenlist as $secilen) {
                                        $flag = 0;
                                        foreach ($tekrarucretlendirilenlist as $tekrar) {
                                            if ($tekrar == $secilen) {
                                                $flag = 1;
                                                break;
                                            }
                                        }
                                        if (!$flag) {
                                            array_push($yenisecilenlist, $secilen);
                                        }
                                    }
                                    BackendController::HatirlatmaGuncelle(4, $netsiscariid, $servisid, count($yenisecilenlist));
                                    BackendController::HatirlatmaEkle(5, $netsiscariid, $servisid, $sayacsayisi);
                                    BackendController::BildirimEkle(4, $netsiscariid, $servisid, $sayacsayisi);
                                    BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-check', $ucretlendirilen->id . ' Numaralı Ücretlendirme Eklendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                                    DB::commit();
                                    return Redirect::to('ucretlendirme/ucretlendirmekayit')->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapıldı', 'text' => 'Ücretlendirme Başarıyla Yapıldı', 'type' => 'success'));
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Yeni Ücretlendirme Kaydedilemedi', 'type' => 'error'));
                                }
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Ücretlendirme Kaydedilemedi', 'type' => 'error'));
                        }
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => str_replace("'", "\'", $e->getMessage()), 'type' => 'error'));
                }
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Ücretlendirme Detayı Ekrana Gelmeden Onaylanmaya Çalışıldı.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => str_replace("'", "\'", $e->getMessage()), 'type' => 'error'));
        }
    }

    public function getUcretlendirilenler($hatirlatma_id=false) {
        $servisid=Auth::user()->servis_id;
        if($hatirlatma_id)
            return View::make('ucretlendirme.ucretlendirilenler',array('hatirlatma_id'=>$hatirlatma_id,'servis_id'=>$servisid))->with(array('title'=>'Ucretlendirilenler Ekranı'));
        else
            return View::make('ucretlendirme.ucretlendirilenler',array('servis_id'=>$servisid))->with(array('title'=>'Ucretlendirilenler Ekranı'));
    }

    public function postUcretlendirilenkayitlist() {
        $hatirlatma_id=Input::get('hatirlatma_id');
        $netsiscari_id=Input::get('netsiscari_id');
        $servisid=BackendController::getKullaniciServis();
        $kullaniciservis = Auth::user()->servis_id;
        $hatirlatma = false;
        if($hatirlatma_id!="") {
            $hatirlatma = Hatirlatma::find($hatirlatma_id);
        }
        if($hatirlatma){
            $query = Ucretlendirilen::whereNull('ucretlendirilen.tekrarkayittarihi')->whereNull('ucretlendirilen.gerigonderimtarihi')
                ->where('ucretlendirilen.netsiscari_id',$hatirlatma->netsiscari_id)
                ->where(function($query) use($servisid){ $query->whereIn('ucretlendirilen.servis_id',$servisid)->orWhereIn('ucretlendirilen.servisdurum',$servisid);})
                ->select(array("ucretlendirilen.id","netsiscari.cariadi","uretimyer.yeradi","servis.servisadi","ucretlendirilen.sayacsayisi","ucretlendirilen.gdurum","ucretlendirilen.fiyat",
                    "ucretlendirilen.gmail","kullanici.adi_soyadi","ucretlendirilen.durumtarihi","ucretlendirilen.gdurumtarihi","netsiscari.ncariadi","uretimyer.nyeradi",
                    "servis.nservisadi","ucretlendirilen.ndurum", "ucretlendirilen.nmail","kullanici.nadi_soyadi","ucretlendirilen.servis_id","ucretlendirilen.fiyat2","parabirimi.birimi",
                    "parabirimi2.birimi2","ucretlendirilen.garanti"))
                ->leftjoin("netsiscari", "ucretlendirilen.netsiscari_id", "=", "netsiscari.id")
                ->leftjoin("uretimyer", "ucretlendirilen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("servis", "ucretlendirilen.servis_id", "=", "servis.id")
                ->leftjoin("kullanici", "ucretlendirilen.kullanici_id", "=", "kullanici.id")
                ->leftjoin("parabirimi", "ucretlendirilen.parabirimi_id", "=", "parabirimi.id")
                ->leftjoin("parabirimi2", "ucretlendirilen.parabirimi2_id", "=", "parabirimi2.id");
        }else if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $query = Ucretlendirilen::whereNull('ucretlendirilen.tekrarkayittarihi')->whereNull('ucretlendirilen.gerigonderimtarihi')
                ->whereIn('ucretlendirilen.netsiscari_id',$netsiscarilist)->whereIn('ucretlendirilen.servis_id',$servisid)
                ->select(array("ucretlendirilen.id","netsiscari.cariadi","uretimyer.yeradi","servis.servisadi","ucretlendirilen.sayacsayisi","ucretlendirilen.gdurum","ucretlendirilen.fiyat",
                    "ucretlendirilen.gmail","kullanici.adi_soyadi","ucretlendirilen.durumtarihi","ucretlendirilen.gdurumtarihi","netsiscari.ncariadi","uretimyer.nyeradi",
                    "servis.nservisadi","ucretlendirilen.ndurum", "ucretlendirilen.nmail","kullanici.nadi_soyadi","ucretlendirilen.servis_id","ucretlendirilen.fiyat2","parabirimi.birimi",
                    "parabirimi2.birimi2","ucretlendirilen.garanti"))
                ->leftjoin("netsiscari", "ucretlendirilen.netsiscari_id", "=", "netsiscari.id")
                ->leftjoin("uretimyer", "ucretlendirilen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("servis", "ucretlendirilen.servis_id", "=", "servis.id")
                ->leftjoin("kullanici", "ucretlendirilen.kullanici_id", "=", "kullanici.id")
                ->leftjoin("parabirimi", "ucretlendirilen.parabirimi_id", "=", "parabirimi.id")
                ->leftjoin("parabirimi2", "ucretlendirilen.parabirimi2_id", "=", "parabirimi2.id");
        }else{
            $query = Ucretlendirilen::whereNull('ucretlendirilen.tekrarkayittarihi')->whereNull('ucretlendirilen.gerigonderimtarihi')
                ->where(function($query) use($servisid){ $query->whereIn('ucretlendirilen.servis_id',$servisid)->orWhereIn('ucretlendirilen.servisdurum',$servisid);})
                ->select(array("ucretlendirilen.id","netsiscari.cariadi","uretimyer.yeradi","servis.servisadi","ucretlendirilen.sayacsayisi","ucretlendirilen.gdurum","ucretlendirilen.fiyat",
                    "ucretlendirilen.gmail","kullanici.adi_soyadi","ucretlendirilen.durumtarihi","ucretlendirilen.gdurumtarihi","netsiscari.ncariadi","uretimyer.nyeradi",
                    "servis.nservisadi","ucretlendirilen.ndurum", "ucretlendirilen.nmail","kullanici.nadi_soyadi","ucretlendirilen.servis_id","ucretlendirilen.fiyat2","parabirimi.birimi",
                    "parabirimi2.birimi2","ucretlendirilen.garanti"))
                ->leftjoin("netsiscari", "ucretlendirilen.netsiscari_id", "=", "netsiscari.id")
                ->leftjoin("uretimyer", "ucretlendirilen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("servis", "ucretlendirilen.servis_id", "=", "servis.id")
                ->leftjoin("kullanici", "ucretlendirilen.kullanici_id", "=", "kullanici.id")
                ->leftjoin("parabirimi", "ucretlendirilen.parabirimi_id", "=", "parabirimi.id")
                ->leftjoin("parabirimi2", "ucretlendirilen.parabirimi2_id", "=", "parabirimi2.id");
        }
        return Datatables::of($query)
            ->editColumn('fiyat', function ($model) {
                if($model->garanti)
                    return '0.00 '.$model->birimi;
                else
                    if($model->fiyat2==0){
                        if($model->fiyat==0)
                            return "0.00 ".$model->birimi;
                        else
                            return $model->fiyat." ".$model->birimi;
                    }else{
                        if($model->fiyat==0)
                            return $model->fiyat2." ".$model->birimi2;
                        else
                            return $model->fiyat." ".$model->birimi." + ".$model->fiyat2." ".$model->birimi2;
                    }
            })
            ->editColumn('durumtarihi', function ($model) {
                $date = new DateTime($model->durumtarihi);
                return $date->format('d-m-Y');
            })
            ->addColumn('islemler',function ($model) use ($kullaniciservis) {
                if($model->servis_id<>6 && $kullaniciservis==6)
                    return "<a class='btn btn-sm btn-info goster' href='#detay-goster' data-toggle='modal' data-id='{$model->id}'> Detay </a>";
                else if($model->gdurum=='Bekliyor' || $model->gdurum=='Gönderildi')
                    return "<a class='btn btn-sm btn-warning goster' href='#detay-goster' data-toggle='modal' data-id='{$model->id}'> Detay </a>
                    <a href='#portlet-delete' data-toggle='modal' data-id='{$model->id}' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                else if($model->gdurum=='Reddedildi')
                    return "<a href='#redneden' data-toggle='modal' data-id='{$model->id}' class='btn btn-sm btn-danger neden' data-original-title='' title=''>Detay</a>";
                else{
                    if(BackendController::UcretlendirilenDepoTeslimDurum($model->id))
                        return "<a class='btn btn-sm btn-warning goster' href='#detay-goster' data-toggle='modal' data-id='{$model->id}'> Detay </a>
                        <a href='#portlet-delete' data-toggle='modal' data-id='{$model->id}' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                    else
                        return "<a class='btn btn-sm btn-warning goster' href='#detay-goster' data-toggle='modal' data-id='{$model->id}'> Detay </a>";
                }

            })
            ->make(true);
    }

    public function getUcretlendirilensil($id){
        try {
            DB::beginTransaction();
            $ucretlendirilen = Ucretlendirilen::find($id);
            $bilgi = clone $ucretlendirilen;
            if ($ucretlendirilen) {
                $servistakipler = ServisTakip::where('ucretlendirilen_id',$id)->get();
                $sayacsecilenler = array();
                foreach ($servistakipler as $servistakip){
                    array_push($sayacsecilenler,$servistakip->sayacgelen_id);
                }
                $onaylanan = Onaylanan::where('ucretlendirilen_id',$ucretlendirilen->id)->first();
                $secilenler = $ucretlendirilen->secilenler;
                $secilenlist = explode(',', $secilenler);
                $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenlist)->get();
                $ucretred = Ucretlendirilen::where('servis_id', $ucretlendirilen->servis_id)->where('uretimyer_id', $ucretlendirilen->uretimyer_id)
                    ->where('netsiscari_id', $ucretlendirilen->netsiscari_id)->where('durum', 3)->orderBy('kayittarihi', 'desc')->first();
                if ($ucretred) //reddedilmiş o yere ait ucretlendirme varsa
                {
                    try {
                        $reddedilenler = $ucretred->reddedilenler;
                        $redsecilenler = $ucretred->secilenler;
                        $reddedilenlist = explode(',', $redsecilenler);
                        foreach ($secilenlist as $secilen) {
                            $flag = 0;
                            foreach ($reddedilenlist as $reddedilen) {
                                if ($reddedilen == $secilen) {
                                    $flag = 1;
                                    break;
                                }
                            }
                            if ($flag == 1) //reddedilen ucretlendirilmiş geri alınır
                            {
                                $reddedilenler .= ($reddedilenler == "" ? "" : ",") . $secilen;
                            }
                        }
                        if ($reddedilenler != "") //reddedilenlerin tamamı tekrar ucretlendirilmiş
                        {
                            $ucretred->tekrarkayittarihi = NULL;
                        }
                        $ucretred->reddedilenler = $reddedilenler;
                        $ucretred->save();
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Silinemedi', 'text' =>'Reddedilen Ücretlendirme Geri Alınırken Hata Oluştu', 'type' => 'error'));
                    }
                }
                try {
                    foreach ($arizafiyatlar as $arizafiyat) {
                        $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                        $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                        if ($sayacgelen->depoteslim) {
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Fiyatlandırması Silinemez', 'text' => $sayacgelen->serino.' Nolu Sayacın Depo Teslimatı Var!', 'type' => 'error'));
                        }
                        $servistakip = ServisTakip::where('arizakayit_id', $arizafiyat->arizakayit_id)->first();
                        $arizafiyat->durum = 0;
                        $arizafiyat->kurtarihi = NULL;
                        $arizafiyat->save();
                        $sayacgelen->fiyatlandirma = 0;
                        $sayacgelen->musterionay = 0;
                        $sayacgelen->teslimdurum = 0;
                        $sayacgelen->save();
                        if (!$servistakip) {
                            $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                            $servistakip->arizakayit_id = $arizafiyat->arizakayit_id;
                            $servistakip->arizafiyat_id = $arizafiyat->id;
                        }
                        $servistakip->ucretlendirilen_id = NULL;
                        $servistakip->onaylanan_id = NULL;
                        $servistakip->ucretlendirmetarihi=null;
                        $servistakip->gondermetarihi=null;
                        $servistakip->onaylanmatarihi=null;
                        $servistakip->reddetmetarihi=null;
                        $servistakip->tekrarucrettarihi=null;
                        $servistakip->durum = 2;
                        $servistakip->sonislemtarihi = $servistakip->arizakayittarihi;
                        $servistakip->kullanici_id = $arizakayit->arizakayit_kullanici_id;
                        $servistakip->save();
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Silinemedi', 'text' =>'Sayaç Gelen ve Servis Takip Bilgisi Silinemedi', 'type' => 'error'));
                }
                try {
                    if($onaylanan){
                        $depoteslimler = DepoTeslim::where('netsiscari_id',$ucretlendirilen->netsiscari_id)->where('depodurum',0)->get();
                        foreach ($depoteslimler as $depoteslim){
                            $fark = $depoteslim->sayacsayisi;
                            $depoteslimlist = explode(',',$depoteslim->secilenler);
                            $depoteslimsecilenler = BackendController::getListeFark($depoteslimlist,$sayacsecilenler);
                            $depoteslimsecilenlist = explode(',',$depoteslimsecilenler);
                            if($depoteslimsecilenler==""){
                                BackendController::HatirlatmaSil(9,$depoteslim->netsiscari_id,$depoteslim->servis_id,$depoteslim->sayacsayisi);
                                BackendController::BildirimGeriAl(5,$depoteslim->netsiscari_id,$depoteslim->servis_id,$depoteslim->sayacsayisi);
                                BackendController::HatirlatmaGeriAl(5,$depoteslim->netsiscari_id,$depoteslim->servis_id,$depoteslim->sayacsayisi);
                                $depoteslim->delete();
                            }else{
                                $depoteslim->sayacsayisi = count($depoteslimsecilenlist);
                                $depoteslim->secilenler = $depoteslimsecilenler;
                                $depoteslim->save();
                                BackendController::HatirlatmaSil(9,$depoteslim->netsiscari_id,$depoteslim->servis_id,$fark-$depoteslim->sayacsayisi);
                                BackendController::BildirimGeriAl(5,$depoteslim->netsiscari_id,$depoteslim->servis_id,$fark-$depoteslim->sayacsayisi);
                                BackendController::HatirlatmaGeriAl(5,$depoteslim->netsiscari_id,$depoteslim->servis_id,$fark-$depoteslim->sayacsayisi);
                            }
                        }

                        $onaylanan->delete();
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirilen Silinemedi', 'text' => 'Ücretlendirilen Silinirken Sorun Oluştu.', 'type' => 'error'));
                }
                try {
                    $sayacsayi = $ucretlendirilen->sayacsayisi;
                    BackendController::HatirlatmaSil(5,$ucretlendirilen->netsiscari_id,$ucretlendirilen->servis_id,$sayacsayi);
                    BackendController::BildirimGeriAl(4,$ucretlendirilen->netsiscari_id,$ucretlendirilen->servis_id,$sayacsayi);
                    BackendController::HatirlatmaGeriAl(4,$ucretlendirilen->netsiscari_id,$ucretlendirilen->servis_id,$sayacsayi);
                    $ucretlendirilen->delete();
                    BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-check', $bilgi->id . ' Numaralı Ücretlendirme Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $bilgi->id);
                    DB::commit();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirilen Silindi', 'text' => 'Ücretlendirilen Başarıyla Silindi.', 'type' => 'success'));
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirilen Silinemedi', 'text' => 'Ücretlendirilen Silinirken Sorun Oluştu.', 'type' => 'error'));
                }
            }else{
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirilen Silinemedi', 'text' => 'Silinecek Ücretlendirme Bulunamadı.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirilen Silinemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getUcretlendirilenkayitsil($id){
        try {
            DB::beginTransaction();
            $arizafiyat = ArizaFiyat::find($id);
            $bilgi = clone $arizafiyat;
            if ($arizafiyat) {
                $servistakip = ServisTakip::where('arizafiyat_id',$id)->first();
                $ucretlendirilen = Ucretlendirilen::find($servistakip->ucretlendirilen_id);
                $onaylanan = Onaylanan::where('ucretlendirilen_id',$ucretlendirilen->id)->first();
                $ucretred = Ucretlendirilen::where('servis_id', $ucretlendirilen->servis_id)->where('uretimyer_id', $ucretlendirilen->uretimyer_id)
                    ->where('netsiscari_id', $ucretlendirilen->netsiscari_id)->where('durum', 3)->orderBy('kayittarihi', 'desc')->first();
                if ($ucretred) //reddedilmiş o yere ait ucretlendirme varsa
                {
                    try {
                        $redlist = "";
                        $reddedilenler = $ucretred->reddedilenler;
                        $reddedilenlist = explode(',', $reddedilenler);
                        foreach ($reddedilenlist as $reddedilen) {
                            if ($reddedilen == $id) {
                                continue;
                            }else{
                                $redlist .= ($redlist == "" ? "" : ",") . $reddedilen;
                            }
                        }
                        if ($redlist !== "") //reddedilenlerin tamamı tekrar ucretlendirilmiş
                        {
                            $ucretred->tekrarkayittarihi = NULL;
                        }
                        $ucretred->reddedilenler = $redlist;
                        $ucretred->save();
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Silinemedi', 'text' =>'Reddedilen Ücretlendirme Geri Alınırken Hata Oluştu', 'type' => 'error'));
                    }
                }
                try {
                    $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                    $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                    if ($sayacgelen->depoteslim) {
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Fiyatlandırması Silinemez', 'text' => $sayacgelen->serino.' Nolu Sayacın Depo Teslimatı Var!', 'type' => 'error'));
                    }
                    $arizafiyat->durum = 0;
                    $arizafiyat->kurtarihi = NULL;
                    $arizafiyat->save();
                    $sayacgelen->fiyatlandirma = 0;
                    $sayacgelen->musterionay = 0;
                    $sayacgelen->teslimdurum = 0;
                    $sayacgelen->save();
                    if (!$servistakip) {
                        $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                        $servistakip->arizakayit_id = $arizafiyat->arizakayit_id;
                        $servistakip->arizafiyat_id = $arizafiyat->id;
                    }
                    $servistakip->ucretlendirilen_id = NULL;
                    $servistakip->onaylanan_id = NULL;
                    $servistakip->ucretlendirmetarihi=null;
                    $servistakip->gondermetarihi=null;
                    $servistakip->onaylanmatarihi=null;
                    $servistakip->reddetmetarihi=null;
                    $servistakip->tekrarucrettarihi=null;
                    $servistakip->durum = 2;
                    $servistakip->sonislemtarihi = $servistakip->arizakayittarihi;
                    $servistakip->kullanici_id = $arizakayit->arizakayit_kullanici_id;
                    $servistakip->save();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fiyat Geri Alınamadı', 'text' =>'Sayaç Gelen ve Servis Takip Bilgisi Güncellenemedi', 'type' => 'error'));
                }
                try {
                    if($onaylanan){
                        $depoteslimler = DepoTeslim::where('netsiscari_id',$ucretlendirilen->netsiscari_id)->where('depodurum',0)->get();
                        foreach ($depoteslimler as $depoteslim){
                            $fark = $depoteslim->sayacsayisi;
                            $depoteslimlist = explode(',',$depoteslim->secilenler);
                            $depoteslimsecilenler = BackendController::getListeFark($depoteslimlist,array($servistakip->sayacgelen_id));
                            $depoteslimsecilenlist = explode(',',$depoteslimsecilenler);
                            if($depoteslimsecilenler==""){
                                BackendController::HatirlatmaSil(9,$depoteslim->netsiscari_id,$depoteslim->servis_id,$depoteslim->sayacsayisi);
                                BackendController::BildirimGeriAl(5,$depoteslim->netsiscari_id,$depoteslim->servis_id,$depoteslim->sayacsayisi);
                                BackendController::HatirlatmaGeriAl(5,$depoteslim->netsiscari_id,$depoteslim->servis_id,$depoteslim->sayacsayisi);
                                $depoteslim->delete();
                            }else{
                                $depoteslim->sayacsayisi = count($depoteslimsecilenlist);
                                $depoteslim->secilenler = $depoteslimsecilenler;
                                $depoteslim->save();
                                BackendController::HatirlatmaSil(9,$depoteslim->netsiscari_id,$depoteslim->servis_id,$fark-$depoteslim->sayacsayisi);
                                BackendController::BildirimGeriAl(5,$depoteslim->netsiscari_id,$depoteslim->servis_id,$fark-$depoteslim->sayacsayisi);
                                BackendController::HatirlatmaGeriAl(5,$depoteslim->netsiscari_id,$depoteslim->servis_id,$fark-$depoteslim->sayacsayisi);
                            }
                        }
                    }
                    $ucretlendirilenlist=explode(',',$ucretlendirilen->secilenler);
                    $ucretlendirilenler="";
                    foreach ($ucretlendirilenlist as $arizafiyatid){
                        if ($arizafiyatid == $id) {
                            continue;
                        }else{
                            $ucretlendirilenler .= ($ucretlendirilenler == "" ? "" : ",") . $arizafiyatid;
                        }
                    }
                    BackendController::HatirlatmaSil(5,$ucretlendirilen->netsiscari_id,$ucretlendirilen->servis_id,1);
                    BackendController::BildirimGeriAl(4,$ucretlendirilen->netsiscari_id,$ucretlendirilen->servis_id,1);
                    BackendController::HatirlatmaGeriAl(4,$ucretlendirilen->netsiscari_id,$ucretlendirilen->servis_id,1);
                    if ($ucretlendirilenler == "")
                    {
                        if($onaylanan){
                            $onaylanan->delete();
                        }
                        $ucretlendirilen->delete();
                    }else{
                        $ucretlendirilen->secilenler = $ucretlendirilenler;
                        $ucretlendirilen->sayacsayisi = $ucretlendirilen->sayacsayisi-1;
                        if($ucretlendirilen->parabirimi_id==$arizafiyat->parabirimi_id){
                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar;
                            if($arizafiyat->parabirimi2_id!=null){
                                if($ucretlendirilen->parabirimi_id==$arizafiyat->parabirimi2_id){
                                    $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2;
                                }else{
                                    if($ucretlendirilen->parabirimi2_id==$arizafiyat->parabirimi2_id){
                                        $ucretlendirilen->fiyat2 -= $arizafiyat->toplamtutar2;
                                    }else{
                                        if($ucretlendirilen->parabirimi_id==1){
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2*BackendController::getKurBilgisi($arizafiyat->parabirimi2_id,$arizafiyat->kurtarih);
                                        }else{
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2*BackendController::getKurBilgisi($arizafiyat->parabirimi2_id,$arizafiyat->kurtarih)/BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id,$arizafiyat->kurtarih);
                                        }
                                    }
                                }
                            }
                        }else{
                            if($ucretlendirilen->parabirimi_id==1){
                                $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar*BackendController::getKurBilgisi($arizafiyat->parabirimi_id,$arizafiyat->kurtarih);
                            }else{
                                $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar*BackendController::getKurBilgisi($arizafiyat->parabirimi_id,$arizafiyat->kurtarih)/BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id,$arizafiyat->kurtarih);
                            }
                            if($arizafiyat->parabirimi2_id!=null){
                                if($ucretlendirilen->parabirimi_id==$arizafiyat->parabirimi2_id){
                                    $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2;
                                }else{
                                    if($ucretlendirilen->parabirimi2_id==$arizafiyat->parabirimi2_id){
                                        $ucretlendirilen->fiyat2 -= $arizafiyat->toplamtutar2;
                                    }else{
                                        if($ucretlendirilen->parabirimi_id==1){
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2*BackendController::getKurBilgisi($arizafiyat->parabirimi2_id,$arizafiyat->kurtarih);
                                        }else{
                                            $ucretlendirilen->fiyat -= $arizafiyat->toplamtutar2*BackendController::getKurBilgisi($arizafiyat->parabirimi2_id,$arizafiyat->kurtarih)/BackendController::getKurBilgisi($ucretlendirilen->parabirimi_id,$arizafiyat->kurtarih);
                                        }
                                    }
                                }
                            }
                        }
                        $ucretlendirilen->save();
                    }
                    BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-check', $bilgi->id . ' Numaralı Arıza Fiyatı Geri Alındı.', 'İşlemi Yapan:' . Auth::user()->adi_soyadi . ',Arıza Fiyat Numarası:' . $bilgi->id);
                    DB::commit();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Fiyatı Geri Alındı', 'text' => 'Arıza Fiyatı Başarıyla Geri Alındı.', 'type' => 'success'));
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Fiyatı Geri Alınamadı', 'text' => 'Arıza Fiyatı Geri Alınırken Sorun Oluştu.', 'type' => 'error'));
                }
            }else{
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Fiyatı Geri Alınamadı', 'text' => 'Geri Alınacak Arıza Fiyatı Bulunamadı.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Arıza Fiyatı Geri Alınamadı', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getUcretlendirilenbilgi() {
        try {
            $id=Input::get('id');
            $ucretlendirilen = Ucretlendirilen::find($id);
            $ucretlendirilen->uretimyer = Uretimyer::find($ucretlendirilen->uretimyer_id);
            $ucretlendirilen->parabirimi = ParaBirimi::find($ucretlendirilen->parabirimi_id);
            $ucretlendirilen->parabirimi2 = ParaBirimi::find($ucretlendirilen->parabirimi2_id);
            $secilenler = explode(',', $ucretlendirilen->secilenler);
            $ucretlendirilen->arizafiyat = ArizaFiyat::whereIn('id', $secilenler)->get();
            foreach ($ucretlendirilen->arizafiyat as $arizafiyat) {
                $arizafiyat->sayacadi = SayacAdi::find($arizafiyat->sayacadi_id);
                $arizafiyat->sayaccap = SayacCap::find($arizafiyat->sayaccap_id);
                $arizafiyat->parabirimi = ParaBirimi::find($arizafiyat->parabirimi_id);
                $arizafiyat->parabirimi2 = ParaBirimi::find($arizafiyat->parabirimi2_id);
                $arizafiyat->depoteslimdurum = BackendController::ArizaFiyatDepoTeslimDurum($arizafiyat->id);
            }
            $dovizkuru = DovizKuru::where('tarih', $ucretlendirilen->kurtarihi)->take(3)->get();
            foreach ($dovizkuru as $doviz) {
                $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
            }
            return Response::json(array('durum'=>true,'ucretlendirilen' => $ucretlendirilen, 'dovizkuru' => $dovizkuru));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title'=>'Ücretlendirme Bilgisi Hatalı','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

    public function getOnaybilgi() {
        try {
            $id=Input::get('id');
            $ucretlendirilen = Ucretlendirilen::find($id);
            $ucretlendirilen->uretimyer = Uretimyer::find($ucretlendirilen->uretimyer_id);
            $ucretlendirilen->netsiscari = Netsiscari::find($ucretlendirilen->netsiscari_id);
            $ucretlendirilen->parabirimi = ParaBirimi::find($ucretlendirilen->parabirimi_id);
            $ucretlendirilen->parabirimi2 = ParaBirimi::find($ucretlendirilen->parabirimi2_id);
            $secilenler = explode(',', $ucretlendirilen->secilenler);
            $ucretlendirilen->arizafiyat = ArizaFiyat::whereIn('id', $secilenler)->get();
            foreach ($ucretlendirilen->arizafiyat as $arizafiyat) {
                $arizafiyat->sayacadi = SayacAdi::find($arizafiyat->sayacadi_id);
                $arizafiyat->sayaccap = SayacCap::find($arizafiyat->sayaccap_id);
                $arizafiyat->parabirimi = ParaBirimi::find($arizafiyat->parabirimi_id);
                $arizafiyat->parabirimi2 = ParaBirimi::find($arizafiyat->parabirimi2_id);
            }
            $dovizkuru = DovizKuru::where('tarih', $ucretlendirilen->kurtarihi)->take(3)->get();
            foreach ($dovizkuru as $doviz) {
                $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
            }
            $yetkililer = Yetkili::where('netsiscari_id', $ucretlendirilen->netsiscari_id)->where('aktif', 1)->get();
            foreach ($yetkililer as $yetkili) {
                $yetkili->netsiscari = NetsisCari::find($yetkili->netsiscari_id);
                $yetkili->kullanici = Kullanici::find($yetkili->kullanici_id);
            }
            $kullanicilist = Yetkili::where('netsiscari_id', $ucretlendirilen->netsiscari_id)->where('aktif', 1)->get(array('kullanici_id'))->toArray();
            $kullanicilar = Kullanici::where('grup_id', 6)->whereNotIn('id',$kullanicilist)->get();
            $root = BackendController::getRootDizin(true);

            return Response::json(array('durum'=>true,'ucretlendirilen' => $ucretlendirilen, 'dovizkuru' => $dovizkuru, 'yetkili' => $yetkililer,'kullanici' => $kullanicilar, 'yetkilisayi' => $yetkililer->count(), 'root' => $root ));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title'=>'Onaylama Bilgisi Hatalı','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

    public function getYetkilikontrol() {
        try {
            $mail=Input::get('mail');
            $adi=Input::get('adi');
            $soyadi=Input::get('soyadi');
            if(!Input::has('mail') || !Input::has('tel') || !Input::has('adi') || !Input::has('soyadi'))
                return Response::json(array('durum'=>false,'title'=>'Bilgiler Eksik Girildi!','text'=>'Yeni Yetkili Kayıdı için Tüm Bilgilerin Girilmesi Gerekiyor!','type'=>'error'));
            $kullanicilar=Kullanici::where('email',$mail)->get();
            if($kullanicilar->count()>0){
                $flag=0;
                foreach ($kullanicilar as $kullanici){
                    if(mb_strtolower($kullanici->adi_soyadi,"UTF-8")==mb_strtolower($adi.' '.$soyadi,"UTF-8")){
                        $flag=1;
                        break;
                    }
                }
                if($flag)
                    return Response::json(array('durum'=>true));
                else
                    return Response::json(array('durum'=>false,'title'=>'Mail Adresi Başka Kullanıcıya Ait','text'=>'Mail Adresi Başka Kullanıcı Adına Kayıtlı. Yetkili Kontrolü ile Güncelleme Yapılmalı!','type'=>'error'));
            }else{
                return Response::json(array('durum'=>true));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title'=>'Kullanıcı Bilgileri Getirilemedi!','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

    public function postUcretlendirilenler()
    {
        try {
            $ucretlendirilenid = Input::get('ucretlendirilen');
            $raportip = Input::get('rapor');
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            if ($ucretlendirilen) {
                $ucretlendirilen->uretimyer = UretimYer::find($ucretlendirilen->uretimyer_id);
                $ucretlendirilen->yetkili = Yetkili::where('netsiscari_id', $ucretlendirilen->netsiscari_id)->where('aktif', 1)->first();
                if ($raportip == "1") // Onay Formu
                {
                    if (!$ucretlendirilen->yetkili) {
                        return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Seçilen yerin yetkili kişisi ekli değil', 'type' => 'warning', 'title' => 'Rapor Hatası'));
                    }
                    $raporadi = "OnayFormu-" . Str::slug($ucretlendirilen->uretimyer->yeradi);
                    $export = "pdf";
                    $kriterler = array();
                    $kriterler['id'] = $ucretlendirilenid;

                    JasperPHP::process(public_path('reports/onayformu/onayformu.jasper'), public_path('reports/outputs/onayformu/' . $raporadi),
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
                    readfile("reports/outputs/onayformu/" . $raporadi . "." . $export . "");
                    File::delete("reports/outputs/onayformu/" . $raporadi . "." . $export . "");
                } else if ($raportip == "2") { //Fiyatlandırma
                    $raporadi = "Fiyatlandirma-" . Str::slug($ucretlendirilen->uretimyer->yeradi);
                    $export = "pdf";
                    $kriterler = array();
                    $kriterler['id'] = $ucretlendirilenid;

                    JasperPHP::process(public_path('reports/fiyatlandirma/fiyatlandirma.jasper'), public_path('reports/outputs/fiyatlandirma/' . $raporadi),
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
                    readfile("reports/outputs/fiyatlandirma/" . $raporadi . "." . $export . "");
                    File::delete("reports/outputs/fiyatlandirma/" . $raporadi . "." . $export . "");
                } else { //Sayaç Listesi
                    $raporadi = "SayacListesi-" . Str::slug($ucretlendirilen->uretimyer->yeradi);
                    $export = "pdf";
                    $kriterler = array();
                    $kriterler['id'] = $ucretlendirilenid;

                    JasperPHP::process(public_path('reports/ucretlendirilensayaclistesi/ucretlendirilensayaclistesi.jasper'), public_path('reports/outputs/ucretlendirilensayaclistesi/' . $raporadi),
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
                    readfile("reports/outputs/ucretlendirilensayaclistesi/" . $raporadi . "." . $export . "");
                    File::delete("reports/outputs/ucretlendirilensayaclistesi/" . $raporadi . "." . $export . "");
                }
                return Redirect::back()->with(array('mesaj' => 'false'));
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Rapor alınacak ucretlendirme seçilmedi', 'type' => 'warning', 'title' => 'Rapor Hatası'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning', 'title' => 'Rapor Hatası'));
        }
    }

    public function postTelefonileonayla(){
        try {
            if (Input::has('mailvar')) //mail gönderilecek
            {
                $rules = ['teladisoyadi' => 'required', 'telyetkilitel' => 'required', 'telyetkilimail' => 'required|email'];
                $mail = 1;
                if (Input::has('detaylifiyatlandirma')) //mail gönderilecek
                {
                    $detaylifiyat=1;
                }else{
                    $detaylifiyat=0;
                }
            } else // mail gönderilmeyecek
            {
                $rules = ['teladisoyadi' => 'required', 'telyetkilitel' => 'required'];
                $mail = 0;
                $detaylifiyat = 0;
            }
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $digermail = Input::get('telmailcc');
            $digermaillist = explode(';', $digermail);
            if ($digermail != "")
                foreach ($digermaillist as $email) {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Diğer Mail Listesi Hatası', 'text' => $email . ' geçerli bir email değil. Birden fazla girilen mail adreslerini ; ile ayırınız!', 'type' => 'error'));
                    }
                }
            $ucretlendirilenid = Input::get('telid');
            $secilenler = Input::get('telsecilenler');
            $secilenlist = explode(',', $secilenler);
            $tumu = explode(',', Input::get('teltumu'));
            $yetkiliadi = Input::get('teladisoyadi');
            $yetkilitel = Input::get('telyetkilitel');
            $yetkilimail = Input::get('telyetkilimail');
            $yetkiliid = Input::get('telyetkiliid');
            $birim = Input::get('telbirim');
            $birim2 = Input::get('telbirim2')=="" ? null : Input::get('telbirim2');
            DB::beginTransaction();
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            if($ucretlendirilen->durum!=0){
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı!', 'text' => 'Ücretlendirme Zaten Gönderilmiş ya da Onaylanmış !', 'type' => 'warning'));
            }
            $servisid = $ucretlendirilen->servis_id;
            $uretimyerid = $ucretlendirilen->uretimyer_id;
            $uretimyer = UretimYer::find($uretimyerid);
            $netsiscariid = $ucretlendirilen->netsiscari_id;
            $sayacsayisi = count($secilenlist);
            $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenlist)->get();
            $servistakipler = ServisTakip::whereIn('arizafiyat_id', $secilenlist)->get();
            $sube = Sube::where('netsiscari_id', $netsiscariid)->where('aktif', 1)->first();
            $netsiscari = NetsisCari::find($netsiscariid);
            $subedurum = 0;
            if($sube){
                foreach ($servistakipler as $servistakip){
                    if($servistakip->subedurum)
                        $subedurum = 1;
                }
            }
            try {
                if ($yetkiliid == -1) {
                    $kullanici = Kullanici::where('email',$yetkilimail)->whereIn('grup_id',array(6,19))->first();
                    if(!$kullanici){ //kullanıcı yoksa
                        $kullanici=Kullanici::onlyTrashed()->where('email',$yetkilimail)->whereIn('grup_id',array(6,19))->first();
                        if($kullanici){ //kullanıcı silinmişse geri getirilir bilgileri güncellenir. Kullanıcının eski yetkili bilgileri pasife çekilir.
                            $kullanici->restore();
                            $kullanici->adi_soyadi = $yetkiliadi;
                            $kullanici->password = Hash::make('manas123');
                            $kullanici->email = $yetkilimail;
                            $kullanici->telefon = $yetkilitel;
                            $kullanici->servis_id = 0;
                            $kullanici->grup_id = 19;
                            $kullanici->ilkmail=false;
                            $kullanici->aktifdurum = 1;
                            $kullanici->save();
                            $yetkili=Yetkili::where('kullanici_id',$kullanici->id)->get();
                            foreach ($yetkili as $eskiyetkili){
                                $eskiyetkili->aktif=0;
                                $eskiyetkili->save();
                            }
                        }else{ //kullanıcı yoksa yeni oluşturulur.
                            $kullanici = new Kullanici;
                            $kullanici->adi_soyadi = $yetkiliadi;
                            $kullanici->girisadi = BackendController::GirisAdiBelirle($netsiscariid,$uretimyerid);
                            $kullanici->password = Hash::make('manas123');
                            $kullanici->email = $yetkilimail;
                            $kullanici->telefon = $yetkilitel;
                            $kullanici->servis_id = 0;
                            $kullanici->grup_id = 19;
                            $kullanici->aktifdurum = 1;
                            $kullanici->save();
                        }
                    }
                    $yetkili=new Yetkili;
                    $yetkili->email = $yetkilimail;
                    $yetkili->telefon = $yetkilitel;
                    $yetkili->netsiscari_id=$netsiscariid;
                    $yetkili->kullanici_id=$kullanici->id;
                    $yetkili->aktif=1;
                    $yetkili->save();

                }else if($yetkiliid == ""){ //yetkili güncellenecek
                    $yetkili = Yetkili::where('netsiscari_id', $netsiscariid)->where('aktif', 1)->first();
                    $kullanici=Kullanici::find($yetkili->kullanici_id);
                }else{
                    if (strpos($yetkiliid, '0_') !== false) //yetkili yok ve kullanıcı listesinden seçildiyse
                    {
                        $yetkililist=explode('_',$yetkiliid);
                        $kullanici=Kullanici::find($yetkililist[1]);
                        $yetkili=new Yetkili;
                        $yetkili->email=$yetkilimail;
                        $yetkili->telefon=$yetkilitel;
                        $yetkili->aktif=1;
                        $yetkili->netsiscari_id=$netsiscariid;
                        $yetkili->kullanici_id=$kullanici->id;
                        $yetkili->save();
                    }else{
                        $yetkili=Yetkili::find($yetkiliid);
                        $kullanici=Kullanici::find($yetkili->kullanici_id);
                    }
                }
                if ($yetkili) //yetkili varsa
                {
                    $yetkili->email = $yetkilimail;
                    $yetkili->telefon = $yetkilitel;
                    $yetkili->aktif = 1;
                    $yetkili->save();
                    $kullanici->email=($kullanici->email==NULL) ? $yetkilimail : $kullanici->email;
                    $kullanici->telefon=($kullanici->telefon==NULL) ? $yetkilitel : $kullanici->telefon;
                    $kullanici->save();
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Yetkili Bilgisi Hatalı', 'text' => 'Yetkili Bilgisi Kaydedilemedi', 'type' => 'error'));
            }
            $ilkmail = $kullanici->ilkmail;
            $girisadi = $kullanici->girisadi;
            $takipno = $netsiscari->takipno;
            if (count($secilenlist) == count($tumu)) //ucretlendirmedeki tum sayaçlar onaylanmış
            {
                if ($mail) {
                    $fiyatlandirma = BackendController::getFiyatlandirma($ucretlendirilenid);
                    if (is_null($fiyatlandirma)){
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                    }
                    if($detaylifiyat){
                        $detaylifiyatlandirma = BackendController::getDetaylifiyatlandirma($ucretlendirilenid);
                        if (is_null($detaylifiyatlandirma)){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Detaylı Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                        }
                    }
                }
                try {
                    $ucretlendirilen->durum = 2;
                    $ucretlendirilen->onaytipi = 1;
                    $ucretlendirilen->onaytarihi = date('Y-m-d H:i:s');
                    $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                    $ucretlendirilen->yetkili_id = $yetkili->id;
                    $ucretlendirilen->save();
                    $onaylanan = new Onaylanan;
                    $onaylanan->servis_id = $servisid;
                    $onaylanan->uretimyer_id = $uretimyerid;
                    $onaylanan->netsiscari_id = $netsiscariid;
                    $onaylanan->ucretlendirilen_id = $ucretlendirilen->id;
                    $onaylanan->yetkili_id = $yetkili->id;
                    $onaylanan->onaytarihi = date('Y-m-d H:i:s');
                    $onaylanan->onaylamatipi = 1;
                    $onaylanan->save();
                    $secilensayaclar = "";
                    $secilensayaclist = array();
                    $garantisayaclar = "";
                    $garantisayaclist = array();
                    $iadesayaclar = "";
                    $iadesayaclist = array();
                    try {
                        foreach ($arizafiyatlar as $arizafiyat) {
                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                            $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                            if($arizakayit->arizakayit_durum==7){
                                array_push($iadesayaclist, $sayacgelen->id);
                                $iadesayaclar .= ($iadesayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0) {
                                array_push($secilensayaclist, $sayacgelen->id);
                                $secilensayaclar .= ($secilensayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }else{
                                array_push($garantisayaclist, $sayacgelen->id);
                                $garantisayaclar .= ($garantisayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }
                            $sayacgelen->musterionay = 1;
                            $sayacgelen->teslimdurum = 1;
                            $sayacgelen->save();
                            $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                            $servistakip->onaylanan_id = $onaylanan->id;
                            $servistakip->durum = 5;
                            $servistakip->onaylanmatarihi = date('Y-m-d H:i:s');
                            $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                            $servistakip->kullanici_id = Auth::user()->id;
                            $servistakip->save();
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Kaydedilemedi', 'text' => 'Sayaç Gelen ve Servis Takip Bilgisi Güncellenemedi', 'type' => 'error'));
                    }
                    try {
                        if ($mail) {
                            try {
                                Mail::send('mail.bilgilendirme', array('ucretlendirilen' => $ucretlendirilen, 'ilkmail' => $ilkmail, 'girisadi' => $girisadi,'takipno'=>$takipno),
                                    function ($message) use ($yetkili, $uretimyer, $digermaillist, $digermail,$detaylifiyat) {
                                        //$message->cc(Auth::user()->mail,Auth::user()->adi_soyadi);
                                        if ($digermail != "")
                                            foreach ($digermaillist as $email)
                                                $message->cc($email);
                                        $message->to($yetkili->email, $yetkili->adisoyadi)->subject("Manas ServisTakip Fiyatlandırma Bilgilendirme");
                                        $message->attach(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                        if($detaylifiyat){
                                            $message->attach(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                        }
                                    });
                                $ucretlendirilen->mail = 1;
                                $ucretlendirilen->save();
                                $kullanici->ilkmail = 1;
                                $kullanici->save();
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => 'Mail Gönderirken Hata Oluştu', 'type' => 'error'));
                            }
                            if (count(Mail::failures()) > 0) {
                                DB::rollBack();
                                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => 'Mail Gönderirken Hata Oluştu', 'type' => 'error'));
                            } else {
                                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                            }
                        } else {
                            File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                            if($detaylifiyat){
                                File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                            }
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                    }
                    if ($servisid == 5) //kalibrasyon tamamlanmışsa depo teslim yoksa bekleyecek
                    {
                        try {
                            $kalibrasyonsayi=0;
                            $kalibrasyonsayaclist="";
                            $garantisayi=0;
                            $garantisayaclist="";
                            $geriiadesayi=0;
                            $geriiadesayaclist="";
                            $periyodiksayi=0;
                            $periyodiklist="";
                            foreach ($arizafiyatlar as $arizafiyat) {
                                $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                                $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                $depogelen=DepoGelen::find($sayacgelen->depogelen_id);
                                if($arizakayit->arizakayit_durum != 3) { //yedek parça bekliyorsa depo teslim olmaz
                                    if ($depogelen->periyodik) {
                                        if ($sayacgelen->kalibrasyon) {
                                            $periyodiksayi++;
                                            $periyodiklist .= ($periyodiklist == "" ? "" : ",") . $sayacgelen->id;
                                        } else {
                                            $sayacgelen->teslimdurum = 0;
                                            $sayacgelen->save();
                                        }
                                    } else {
                                        if ($arizakayit->arizakayit_durum == 7) {
                                            $geriiadesayi++;
                                            $geriiadesayaclist .= ($geriiadesayaclist == "" ? "" : ",") . $sayacgelen->id;
                                        } else if ($sayacgelen->kalibrasyon) {
                                            if ($arizafiyat->toplamtutar > 0 || $arizafiyat->toplamtutar2 > 0) {
                                                $kalibrasyonsayi++;
                                                $kalibrasyonsayaclist .= ($kalibrasyonsayaclist == "" ? "" : ",") . $sayacgelen->id;
                                            } else {
                                                $garantisayi++;
                                                $garantisayaclist .= ($garantisayaclist == "" ? "" : ",") . $sayacgelen->id;
                                            }
                                        } else {
                                            $sayacgelen->teslimdurum = 0;
                                            $sayacgelen->save();
                                        }
                                    }
                                }else{
                                    $sayacgelen->teslimdurum = 0;
                                    $sayacgelen->save();
                                }
                            }
                            if($kalibrasyonsayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $kalibrasyonsayaclar = explode(',', $kalibrasyonsayaclist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($kalibrasyonsayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $kalibrasyonsayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $kalibrasyonsayaclist;
                                    $depoteslim->sayacsayisi = $kalibrasyonsayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if($garantisayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum',0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $garantisayaclar = explode(',', $garantisayaclist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($garantisayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $garantisayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $garantisayaclist;
                                    $depoteslim->sayacsayisi = $garantisayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 1;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if($geriiadesayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum',0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $geriiadesayaclar = explode(',', $geriiadesayaclist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($geriiadesayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $geriiadesayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $geriiadesayaclist;
                                    $depoteslim->sayacsayisi = $geriiadesayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 2;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if($periyodiksayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum',0)->where('tipi',0)->where('periyodik',1)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $periyodiksayaclar = explode(',', $periyodiklist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($periyodiksayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $periyodiksayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $periyodiklist;
                                    $depoteslim->sayacsayisi = $periyodiksayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->periyodik = 1;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi.', 'type' => 'error'));
                        }
                        BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                        BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                        if(($kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi)>0)
                            BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi);
                        BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Telefon ile Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                        DB::commit();
                        return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Telefon ile Onaylama Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                    } else {
                        try {
                            if(count($secilensayaclist)>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($secilensayaclist as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $secilensayaclar;
                                    $depoteslim->sayacsayisi = count($secilensayaclist);
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->subegonderim=$subedurum;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if(count($garantisayaclist)>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($garantisayaclist as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $garantisayaclar;
                                    $depoteslim->sayacsayisi = count($garantisayaclist);
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 1;
                                    $depoteslim->subegonderim=$subedurum;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if(count($iadesayaclist)>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($iadesayaclist as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $iadesayaclar;
                                    $depoteslim->sayacsayisi = count($iadesayaclist);
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 2;
                                    $depoteslim->subegonderim=$subedurum;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Telefon ile Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                            DB::commit();
                            return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Telefon ile Onaylama Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi', 'type' => 'error'));
                        }
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylama onaylananlara kaydedilemedi', 'type' => 'error'));
                }
            } else { //bazıları onaylanmış
                try {
                    $yeniucretlendirilen = new Ucretlendirilen;
                    $yeniucretlendirilen->servis_id = $servisid;
                    $yeniucretlendirilen->uretimyer_id = $uretimyerid;
                    $yeniucretlendirilen->netsiscari_id = $netsiscariid;
                    $yeniucretlendirilen->secilenler = $secilenler;
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
                            if ($yeniparabirimi === "1") { //tl
                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                            } else { //euro dolar sterln
                                if ($arizafiyat->parabirimi_id === "1") {
                                    $kur = 1 / BackendController::getKurBilgisi($yeniparabirimi, $kurtarih);
                                } else {
                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($yeniparabirimi, $kurtarih);
                                }
                            }
                        }
                        $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                        if($yeniparabirimi2===null){
                            if($arizafiyat->parabirimi2_id!==null){
                                if($arizafiyat->parabirimi2_id===$yeniparabirimi){
                                    $arizafiyat->fiyat +=$arizafiyat->fiyat2;
                                    $arizafiyat->fiyat2=0;
                                    $arizafiyat->parabirimi2_id=null;
                                }else{
                                    $yeniparabirimi2=$arizafiyat->parabirimi2_id;
                                }
                            }
                        }else{
                            if($arizafiyat->parabirimi2_id!=null){
                                if($arizafiyat->parabirimi2_id===$yeniparabirimi){
                                    $arizafiyat->fiyat +=$arizafiyat->fiyat2;
                                    $arizafiyat->fiyat2=0;
                                    $arizafiyat->parabirimi2_id=null;
                                }else if($arizafiyat->parabirimi2_id!==$yeniparabirimi2){
                                    DB::rollBack();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Yapılamadı', 'text' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
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
                    $yeniucretlendirilen->sayacsayisi = $sayacsayisi;
                    $yeniucretlendirilen->fiyat = $yenifiyat;
                    $yeniucretlendirilen->fiyat2 = $yenifiyat2;
                    $yeniucretlendirilen->parabirimi_id = $yeniparabirimi;
                    $yeniucretlendirilen->parabirimi2_id = $yeniparabirimi2;
                    $yeniucretlendirilen->kullanici_id = Auth::user()->id;
                    $yeniucretlendirilen->kayittarihi = date('Y-m-d H:i:s');
                    $yeniucretlendirilen->kurtarihi = $ucretlendirilen->kurtarihi;
                    $yeniucretlendirilen->durum = 2;
                    $yeniucretlendirilen->onaytipi = 1;
                    $yeniucretlendirilen->onaytarihi = date('Y-m-d H:i:s');
                    $yeniucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                    $yeniucretlendirilen->yetkili_id=$yetkili->id;
                    $yeniucretlendirilen->save();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Yapılamadı', 'text' => 'Seçilen Sayaçlara ait Ücretlendirme Kaydedilemedi', 'type' => 'error'));
                }
                DB::commit();
                if ($mail) {
                    $fiyatlandirma = BackendController::getFiyatlandirma($yeniucretlendirilen->id);
                    if (is_null($fiyatlandirma)) {
                        $yeniucretlendirilen->delete();
                        DB::commit();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                    }
                    if($detaylifiyat){
                        $detaylifiyatlandirma = BackendController::getDetaylifiyatlandirma($yeniucretlendirilen->id);
                        if (is_null($detaylifiyatlandirma)){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Detaylı Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                        }
                    }
                }
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
                    if ($kalanparabirimi !== $arizafiyat->parabirimi_id) {
                        if ($kalanparabirimi === "1") { //tl
                            $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                        } else { //euro dolar sterln
                            if ($arizafiyat->parabirimi_id === "1") {
                                $kur = 1 / BackendController::getKurBilgisi($kalanparabirimi, $kurtarih);
                            } else {
                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($kalanparabirimi, $kurtarih);
                            }
                        }
                    }
                    $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                    if ($kalanparabirimi2 === null) {
                        if ($arizafiyat->parabirimi2_id !== null) {
                            if ($arizafiyat->parabirimi2_id === $kalanparabirimi) {
                                $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                $arizafiyat->fiyat2 = 0;
                                $arizafiyat->parabirimi2_id = null;
                            } else {
                                $kalanparabirimi2 = $arizafiyat->parabirimi2_id;
                            }
                        }
                    } else {
                        if ($arizafiyat->parabirimi2_id !== null) {
                            if ($arizafiyat->parabirimi2_id === $kalanparabirimi) {
                                $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                $arizafiyat->fiyat2 = 0;
                                $arizafiyat->parabirimi2_id = null;
                            } else if ($arizafiyat->parabirimi2_id !== $kalanparabirimi2) {
                                DB::rollBack();
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Yapılamadı', 'text' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
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
                try {
                    $ucretlendirilen->secilenler = $kalanlar;
                    $ucretlendirilen->garanti = $kalangaranti;
                    $ucretlendirilen->sayacsayisi = $kalansayi;
                    $ucretlendirilen->fiyat = $kalanfiyat;
                    $ucretlendirilen->fiyat2 = $kalanfiyat2;
                    $ucretlendirilen->parabirimi_id = $kalanparabirimi;
                    $ucretlendirilen->parabirimi2_id = $kalanparabirimi2;
                    $ucretlendirilen->save();
                    $onaylanan = new Onaylanan;
                    $onaylanan->servis_id = $servisid;
                    $onaylanan->uretimyer_id = $uretimyerid;
                    $onaylanan->netsiscari_id = $netsiscariid;
                    $onaylanan->ucretlendirilen_id = $yeniucretlendirilen->id;
                    $onaylanan->yetkili_id = $yetkili->id;
                    $onaylanan->onaytarihi = date('Y-m-d H:i:s');
                    $onaylanan->onaylamatipi = 1;
                    $onaylanan->save();
                    $secilensayaclar = "";
                    $secilensayaclist = array();
                    $garantisayaclar = "";
                    $garantisayaclist = array();
                    $iadesayaclar = "";
                    $iadesayaclist = array();
                    try {
                        foreach ($arizafiyatlar as $arizafiyat) {
                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                            $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                            if($arizakayit->arizakayit_durum==7){
                                array_push($iadesayaclist, $sayacgelen->id);
                                $iadesayaclar .= ($iadesayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0) {
                                array_push($secilensayaclist, $sayacgelen->id);
                                $secilensayaclar .= ($secilensayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }else{
                                array_push($garantisayaclist, $sayacgelen->id);
                                $garantisayaclar .= ($garantisayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }
                            $sayacgelen->musterionay = 1;
                            $sayacgelen->teslimdurum = 1;
                            $sayacgelen->save();
                            $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                            $servistakip->ucretlendirilen_id = $yeniucretlendirilen->id;
                            $servistakip->onaylanan_id = $onaylanan->id;
                            $servistakip->durum = 5;
                            $servistakip->onaylanmatarihi = date('Y-m-d H:i:s');
                            $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                            $servistakip->kullanici_id = Auth::user()->id;
                            $servistakip->save();
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Kaydedilemedi', 'text' => 'Sayaç Gelen ve Servis Takip Bilgisi Güncellenemedi', 'type' => 'error'));
                    }
                    try {
                        if ($mail) {
                            try {
                                Mail::send('mail.bilgilendirme', array('ucretlendirilen' => $yeniucretlendirilen, 'ilkmail' => $ilkmail, 'girisadi' => $girisadi,'takipno'=>$takipno),
                                    function ($message) use ($yetkili, $uretimyer, $digermaillist, $digermail,$detaylifiyat) {
                                        //$message->cc(Auth::user()->mail,Auth::user()->adi_soyadi);
                                        if ($digermail != "")
                                            foreach ($digermaillist as $email)
                                                $message->cc($email);
                                        $message->to($yetkili->email, $yetkili->adisoyadi)->subject("Manas ServisTakip Fiyatlandırma Bilgilendirme");
                                        $message->attach(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                        if($detaylifiyat){
                                            $message->attach(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                        }
                                    });
                                $yeniucretlendirilen->mail = 1;
                                $yeniucretlendirilen->save();
                                $kullanici->ilkmail = 1;
                                $kullanici->save();
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => 'Mail Gönderirken Hata Oluştu', 'type' => 'error'));
                            }
                            if (count(Mail::failures()) > 0) {
                                DB::rollBack();
                                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => 'Mail Gönderirken Hata Oluştu', 'type' => 'error'));
                            } else {
                                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                            }
                        } else {
                            File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                            if($detaylifiyat){
                                File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                            }
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                    }
                    if ($servisid == 5) //kalibrasyon tamamlanmışsa depo teslim yoksa bekleyecek
                    {
                        try {
                            $kalibrasyonsayi=0;
                            $kalibrasyonsayaclist="";
                            $garantisayi=0;
                            $garantisayaclist="";
                            $geriiadesayi=0;
                            $geriiadesayaclist="";
                            $periyodiksayi=0;
                            $periyodiklist="";
                            foreach ($arizafiyatlar as $arizafiyat) {
                                $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                                $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                $depogelen=DepoGelen::find($sayacgelen->depogelen_id);
                                if($arizakayit->arizakayit_durum != 3) { //yedek parça bekliyorsa depo teslim olmaz
                                    if ($depogelen->periyodik) {
                                        if ($sayacgelen->kalibrasyon) {
                                            $periyodiksayi++;
                                            $periyodiklist .= ($periyodiklist == "" ? "" : ",") . $sayacgelen->id;
                                        } else {
                                            $sayacgelen->teslimdurum = 0;
                                            $sayacgelen->save();
                                        }
                                    } else {
                                        if ($arizakayit->arizakayit_durum == 7) {
                                            $geriiadesayi++;
                                            $geriiadesayaclist .= ($geriiadesayaclist == "" ? "" : ",") . $sayacgelen->id;
                                        } else if ($sayacgelen->kalibrasyon) {
                                            if ($arizafiyat->toplamtutar > 0 || $arizafiyat->toplamtutar2 > 0) {
                                                $kalibrasyonsayi++;
                                                $kalibrasyonsayaclist .= ($kalibrasyonsayaclist == "" ? "" : ",") . $sayacgelen->id;
                                            } else {
                                                $garantisayi++;
                                                $garantisayaclist .= ($garantisayaclist == "" ? "" : ",") . $sayacgelen->id;
                                            }
                                        } else {
                                            $sayacgelen->teslimdurum = 0;
                                            $sayacgelen->save();
                                        }
                                    }
                                }else{
                                    $sayacgelen->teslimdurum = 0;
                                    $sayacgelen->save();
                                }
                            }
                            if($kalibrasyonsayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $kalibrasyonsayaclar = explode(',', $kalibrasyonsayaclist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($kalibrasyonsayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $kalibrasyonsayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $kalibrasyonsayaclist;
                                    $depoteslim->sayacsayisi = $kalibrasyonsayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                            if($garantisayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum',0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $garantisayaclar = explode(',', $garantisayaclist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($garantisayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $garantisayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $garantisayaclist;
                                    $depoteslim->sayacsayisi = $garantisayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 1;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                            if($geriiadesayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum',0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $geriiadesayaclar = explode(',', $geriiadesayaclist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($geriiadesayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $geriiadesayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $geriiadesayaclist;
                                    $depoteslim->sayacsayisi = $geriiadesayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 2;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                            if($periyodiksayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum',0)->where('tipi',0)->where('periyodik',1)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $periyodiksayaclar = explode(',', $periyodiklist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($periyodiksayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $periyodiksayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $periyodiklist;
                                    $depoteslim->sayacsayisi = $periyodiksayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->periyodik = 1;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi.', 'type' => 'error'));
                        }
                        BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                        BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                        if(($kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi)>0)
                            BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi);
                        BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Telefon ile Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                        DB::commit();
                        return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Telefon ile Onaylama Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                    } else {
                        try {
                            if(count($secilensayaclist)>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($secilensayaclist as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $secilensayaclar;
                                    $depoteslim->sayacsayisi = count($secilensayaclist);
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->subegonderim=$subedurum;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                            if(count($garantisayaclist)>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($garantisayaclist as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $garantisayaclar;
                                    $depoteslim->sayacsayisi = count($garantisayaclist);
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 1;
                                    $depoteslim->subegonderim=$subedurum;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                            if(count($iadesayaclist)>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($iadesayaclist as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $iadesayaclar;
                                    $depoteslim->sayacsayisi = count($iadesayaclist);
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 2;
                                    $depoteslim->subegonderim=$subedurum;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                            BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $yeniucretlendirilen->id . ' Numaralı Ücretlendirme Telefon ile Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $yeniucretlendirilen->id);
                            DB::commit();
                            return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Telefon ile Onaylama Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi', 'type' => 'error'));
                        }
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylama onaylananlara kaydedilemedi', 'type' => 'error'));
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getFiyatlandirmatablo($ucretlendirilenid,$secilenler)
    {
        try {
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            if ($ucretlendirilen) {
                $ucretlendirilen->uretimyer = UretimYer::find($ucretlendirilen->uretimyer_id);
                $ucretlendirilen->parabirimi = ParaBirimi::find($ucretlendirilen->parabirimi_id);
                switch ($ucretlendirilen->parabirimi_id) {
                    case 1 :
                        $ucretlendirilen->parabirimi->yazi = 'TL';
                        break;
                    case 2 :
                        $ucretlendirilen->parabirimi->yazi = 'Euro';
                        break;
                    case 3 :
                        $ucretlendirilen->parabirimi->yazi = 'Dolar';
                        break;
                    case 4 :
                        $ucretlendirilen->parabirimi->yazi = 'Sterlin';
                        break;
                }
                $fiyat=number_format(0,2,'.','');
                $ucretlendirilen->kullanici = Kullanici::find($ucretlendirilen->kullanici_id);
                $secilenler = explode(',', $secilenler);
                $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenler)->get();
                $ucretlendirilen->yetkili = Yetkili::where('netsiscari_id', $ucretlendirilen->netsiscari_id)->where('aktif', 1)->first();
                if (!$ucretlendirilen->yetkili) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Seçilen yerin yetkili kişisi ekli değil', 'type' => 'warning', 'title' => 'Rapor Hatası'));
                }

                $dovizkuru = DovizKuru::where('tarih', $ucretlendirilen->kurtarihi)->orderBy('parabirimi_id', 'asc')->take(3)->get();
                foreach ($dovizkuru as $doviz) {
                    $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
                }
                $ucretlendirilen->dovizkuru = $dovizkuru;
                $ucretlendirilen->genelparabirimi = BackendController::getGenelParabirimi();
                switch ($ucretlendirilen->genelparabirimi->id) {
                    case 1 :
                        $ucretlendirilen->genelparabirimi->yazi = 'TL';
                        break;
                    case 2 :
                        $ucretlendirilen->genelparabirimi->yazi = 'Euro';
                        break;
                    case 3 :
                        $ucretlendirilen->genelparabirimi->yazi = 'Dolar';
                        break;
                    case 4 :
                        $ucretlendirilen->genelparabirimi->yazi = 'Sterlin';
                        break;
                }
                $ucretlendirilen->ozelparabirimi = Parabirimi::find($ucretlendirilen->uretimyer->parabirimi_id);
                switch ($ucretlendirilen->ozelparabirimi->id) {
                    case 1 :
                        $ucretlendirilen->ozelparabirimi->yazi = 'TL';
                        break;
                    case 2 :
                        $ucretlendirilen->ozelparabirimi->yazi = 'Euro';
                        break;
                    case 3 :
                        $ucretlendirilen->ozelparabirimi->yazi = 'Dolar';
                        break;
                    case 4 :
                        $ucretlendirilen->ozelparabirimi->yazi = 'Sterlin';
                        break;
                }
                foreach ($arizafiyatlar as $arizafiyat) {
                    $arizafiyat->sayacadi = SayacAdi::find($arizafiyat->sayacadi_id);
                    $arizafiyat->sayac = Sayac::find($arizafiyat->sayac_id);
                    $arizafiyat->parabirimi = ParaBirimi::find($arizafiyat->parabirimi_id);
                    switch ($arizafiyat->parabirimi_id) {
                        case 1 :
                            $arizafiyat->parabirimi->yazi = 'TL';
                            break;
                        case 2 :
                            $arizafiyat->parabirimi->yazi = 'Euro';
                            break;
                        case 3 :
                            $arizafiyat->parabirimi->yazi = 'Dolar';
                            break;
                        case 4 :
                            $arizafiyat->parabirimi->yazi = 'Sterlin';
                            break;
                    }
                    $degisenlist = explode(',', $arizafiyat->degisenler);
                    $arizafiyat->genelfiyat = explode(';', $arizafiyat->genel);
                    $arizafiyat->ozelfiyat = explode(';', $arizafiyat->ozel);
                    $arizafiyat->ucretsizler = explode(',', $arizafiyat->ucretsiz);
                    $arizafiyat->degisenler = Degisenler::whereIn('id', $degisenlist)->get();
                    if($ucretlendirilen->parabirimi_id<>$arizafiyat->parabirimi_id){
                        $kurfiyati=BackendController::getKurBilgisi($arizafiyat->parabirimi_id,$ucretlendirilen->kurtarihi);
                        $fiyat+=($arizafiyat->toplamtutar*$kurfiyati);
                    }else{
                        $fiyat+=$arizafiyat->toplamtutar;
                    }
                }
                $ucretlendirilen->fiyat = number_format($fiyat, 2,'.','');
                $ucretlendirilen->arizafiyatlar = $arizafiyatlar;
                $pdf = PDF::loadView('pdf.fiyatlandirma', array('ucretlendirilen' => $ucretlendirilen));
                return $pdf->stream();
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Rapor alınacak ucretlendirme seçilmedi', 'type' => 'warning','title' => 'Rapor Hatası'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning','title' => 'Rapor Hatası'));
        }
    }

    public function getOnayform($ucretlendirilenid,$secilenler)
    {
        try {
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            if ($ucretlendirilen) {
                $ucretlendirilen->uretimyer = UretimYer::find($ucretlendirilen->uretimyer_id);
                $ucretlendirilen->parabirimi = ParaBirimi::find($ucretlendirilen->parabirimi_id);
                switch ($ucretlendirilen->parabirimi_id) {
                    case 1 :
                        $ucretlendirilen->parabirimi->yazi = 'TL';
                        break;
                    case 2 :
                        $ucretlendirilen->parabirimi->yazi = 'Euro';
                        break;
                    case 3 :
                        $ucretlendirilen->parabirimi->yazi = 'Dolar';
                        break;
                    case 4 :
                        $ucretlendirilen->parabirimi->yazi = 'Sterlin';
                        break;
                }
                $fiyat=number_format(0,2,'.','');
                $ucretlendirilen->kullanici = Kullanici::find($ucretlendirilen->kullanici_id);
                $secilenler = explode(',', $secilenler);
                $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenler)->get();
                $ucretlendirilen->yetkili = Yetkili::where('netsiscari_id', $ucretlendirilen->netsiscari_id)->where('aktif', 1)->first();
                if (!$ucretlendirilen->yetkili) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Seçilen yerin yetkili kişisi ekli değil', 'type' => 'warning', 'title' => 'Rapor Hatası'));
                }
                $dovizkuru = DovizKuru::where('tarih', $ucretlendirilen->kurtarihi)->orderBy('parabirimi_id', 'asc')->take(3)->get();
                foreach ($dovizkuru as $doviz) {
                    $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
                }
                $ucretlendirilen->dovizkuru = $dovizkuru;
                $sayacadilist = "";
                $sayactiplist = "";
                $adet = count($secilenler);
                $serinolist = "";
                $count = 0;
                foreach ($arizafiyatlar as $arizafiyat) {
                    if ($adet > 10) { //serinolarına gerek yok
                        $serinolist = $adet . ' Adet';
                    } else { //serinoları da yazacağız
                        $serinolist .= ($serinolist == "" ? "" : (($count > 5) ? ", " : ",")) . $arizafiyat->ariza_serino;
                    }
                    $sayacadilist .= ($sayacadilist == "" ? "" : ",") . $arizafiyat->sayacadi_id;
                    $count++;
                    if($ucretlendirilen->parabirimi_id<>$arizafiyat->parabirimi_id){
                        $kurfiyati=BackendController::getKurBilgisi($arizafiyat->parabirimi_id,$ucretlendirilen->kurtarihi);
                        $fiyat+=($arizafiyat->toplamtutar*$kurfiyati);
                    }else{
                        $fiyat+=$arizafiyat->toplamtutar;
                    }
                }
                $sayacadilist = explode(',', $sayacadilist);
                $sayacadlari = SayacAdi::whereIn('id', $sayacadilist)->get();
                $sayacadilist = "";
                $count = 0;
                foreach ($sayacadlari as $sayacadi) {
                    if ($count <= 2)
                        $sayacadilist .= ($sayacadilist == "" ? "" : ",") . $sayacadi->sayacadi;
                    $sayactiplist .= ($sayactiplist == "" ? "" : ",") . $sayacadi->sayactip_id;
                    $count++;
                }
                if ($count > 2) {
                    $sayacadilist .= ",..";
                }
                $sayactiplist = explode(',', $sayactiplist);
                $sayactipleri = SayacTip::whereIn('id', $sayactiplist)->get();
                $sayactiplist = "";
                foreach ($sayactipleri as $sayactip) {
                    $sayactiplist .= ($sayactiplist == "" ? "" : ",") . $sayactip->tipadi;
                }
                $ucretlendirilen->fiyat = number_format($fiyat, 2,'.','');
                $ucretlendirilen->serinolar = $serinolist;
                $ucretlendirilen->sayacadlari = $sayacadilist;
                $ucretlendirilen->sayactipleri = $sayactiplist;
                $pdf = PDF::loadView('pdf.onayformu', array('ucretlendirilen' => $ucretlendirilen));
                return $pdf->stream();
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Rapor alınacak ucretlendirme seçilmedi', 'type' => 'warning','title' => 'Rapor Hatası'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning','title' => 'Rapor Hatası'));
        }
    }

    public function postMailileonayla(){
        try {
            $rules = ['mailadisoyadi' => 'required', 'mailyetkilimail' => 'required|email', 'mailkonu' => 'required', 'icerik' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $digermail = Input::get('mailcc');
            $digermaillist = explode(';', $digermail);
            if ($digermail != "")
                foreach ($digermaillist as $email) {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Diğer Mail Listesi Hatası', 'text' => $email . ' geçerli bir email değil. Birden fazla girilen mail adreslerini ; ile ayırınız!', 'type' => 'error'));
                    }
                }
            if (Input::has('detaylifiyatlandirma')) //mail gönderilecek
            {
                $detaylifiyat=1;
            }else{
                $detaylifiyat=0;
            }
            $ucretlendirilenid = Input::get('mailid');
            $secilenler = Input::get('mailsecilenler');
            $secilenlist = explode(',', $secilenler);
            $tumu = explode(',', Input::get('mailtumu'));
            $yetkiliadi = ucwords(mb_strtolower(Input::get('mailadisoyadi'),"UTF-8"));
            $yetkilimail = Input::get('mailyetkilimail');
            $yetkiliid = Input::get('mailyetkiliid');
            $garanti = Input::get('mailgaranti');
            $mailicerik = Input::get('icerik');
            $mailbaslik = Input::get('mailkonu');
            $onaylink = Input::get('maillink');
            $root = Input::get('mailroot');
            $birim = Input::get('mailbirim');
            $birim2 = Input::get('mailbirim2')=="" ? null : Input::get('mailbirim2');
            DB::beginTransaction();
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            if($ucretlendirilen->durum!=0){
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı!', 'text' => 'Ücretlendirme Zaten Onaylanmış!', 'type' => 'warning'));
            }
            $servisid = $ucretlendirilen->servis_id;
            $uretimyerid = $ucretlendirilen->uretimyer_id;
            $netsiscariid = $ucretlendirilen->netsiscari_id;
            $sayacsayisi = count($secilenlist);
            $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenlist)->get();
            $servistakipler = ServisTakip::whereIn('arizafiyat_id', $secilenlist)->get();
            $sube = Sube::where('netsiscari_id', $netsiscariid)->where('aktif', 1)->first();
            $netsiscari = NetsisCari::find($netsiscariid);
            $subedurum = 0;
            if($sube){
                foreach ($servistakipler as $servistakip){
                    if($servistakip->subedurum)
                        $subedurum = 1;
                }
            }
            try {
                if ($yetkiliid == -1) {
                    $kullanici = Kullanici::where('email',$yetkilimail)->whereIn('grup_id',array(6,19))->first();
                    if(!$kullanici){ //kullanıcı yoksa
                        $kullanici=Kullanici::onlyTrashed()->where('email',$yetkilimail)->whereIn('grup_id',array(6,19))->first();
                        if($kullanici){ //kullanıcı silinmişse geri getirilir bilgileri güncellenir. Kullanıcının eski yetkili bilgileri pasife çekilir.
                            $kullanici->restore();
                            $kullanici->adi_soyadi = $yetkiliadi;
                            $kullanici->password = Hash::make('manas123');
                            $kullanici->email = $yetkilimail;
                            $kullanici->telefon = '';
                            $kullanici->servis_id = 0;
                            $kullanici->grup_id = 19;
                            $kullanici->ilkmail=false;
                            $kullanici->aktifdurum = 1;
                            $kullanici->save();
                            $yetkili=Yetkili::where('kullanici_id',$kullanici->id)->get();
                            foreach ($yetkili as $eskiyetkili){
                                $eskiyetkili->aktif=0;
                                $eskiyetkili->save();
                            }
                        }else{ //kullanıcı yoksa yeni oluşturulur.
                            $kullanici = new Kullanici;
                            $kullanici->adi_soyadi = $yetkiliadi;
                            $kullanici->girisadi = BackendController::GirisAdiBelirle($netsiscariid,$uretimyerid);
                            $kullanici->password = Hash::make('manas123');
                            $kullanici->email = $yetkilimail;
                            $kullanici->telefon = '';
                            $kullanici->servis_id = 0;
                            $kullanici->grup_id = 19;
                            $kullanici->aktifdurum = 1;
                            $kullanici->save();
                        }
                    }
                    $yetkili=new Yetkili;
                    $yetkili->email = $yetkilimail;
                    $yetkili->telefon = '';
                    $yetkili->netsiscari_id=$netsiscariid;
                    $yetkili->kullanici_id=$kullanici->id;
                    $yetkili->aktif=1;
                    $yetkili->save();
                }else if($yetkiliid == ""){
                    $yetkili = Yetkili::where('netsiscari_id', $netsiscariid)->where('aktif', 1)->first();
                    $kullanici=Kullanici::find($yetkili->kullanici_id);
                }else{
                    if (strpos($yetkiliid, '0_') !== false) //yetkili yok ve kullanıcı listesinden seçildiyse
                    {
                        $yetkililist=explode('_',$yetkiliid);
                        $kullanici=Kullanici::find($yetkililist[1]);
                        $yetkili=new Yetkili;
                        $yetkili->email=$yetkilimail;
                        $yetkili->telefon='';
                        $yetkili->aktif=1;
                        $yetkili->netsiscari_id=$netsiscariid;
                        $yetkili->kullanici_id=$kullanici->id;
                        $yetkili->save();
                    }else{
                        $yetkili=Yetkili::find($yetkiliid);
                        $kullanici=Kullanici::find($yetkili->kullanici_id);
                    }
                }
                if ($yetkili) //yetkili varsa
                {
                    $yetkili->email = $yetkilimail;
                    $yetkili->aktif = 1;
                    $yetkili->save();
                    $kullanici->email=($kullanici->email==NULL) ? $yetkilimail : $kullanici->email;
                    $kullanici->save();
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Yetkili Kaydetme Hatası', 'text' => 'Yetkili Bilgileri Kaydedilirken Hata Oluştu', 'type' => 'error'));
            }
            $ilkmail = $kullanici->ilkmail;
            $girisadi = $kullanici->girisadi;
            $takipno = $netsiscari->takipno;
            $eklenenler = "";
            If (Input::hasFile('dosya')) {
                $dosyalar = Input::file('dosya');
                foreach ($dosyalar as $dosya) {
                    $dosyaadi = $dosya->getClientOriginalName();
                    $dosyaadi = pathinfo($dosyaadi, PATHINFO_FILENAME);
                    $uzanti = $dosya->getClientOriginalExtension();
                    $isim = Str::slug($dosyaadi) . Str::slug(str_random(5)) . '.' . $uzanti;
                    $dosya->move('assets/mailek/', $isim);
                    $eklenenler .= ($eklenenler == "" ? "" : ",") . $isim;
                }
            }

            if (count($secilenlist) == count($tumu)) //ucretlendirmedeki tum sayaçlar seçilmiş
            {
                $fiyatlandirma = BackendController::getFiyatlandirma($ucretlendirilenid);
                if (is_null($fiyatlandirma)){
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                }
                $onayformu = BackendController::getOnayFormu($ucretlendirilenid);
                if (is_null($onayformu)) {
                    DB::rollBack();
                    $uretimyer = UretimYer::find($uretimyerid);
                    File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Onay Formu Oluşturulamadı', 'type' => 'error'));
                }
                if($detaylifiyat){
                    $detaylifiyatlandirma = BackendController::getDetaylifiyatlandirma($ucretlendirilenid);
                    if (is_null($detaylifiyatlandirma)){
                        DB::rollBack();
                        $uretimyer = UretimYer::find($uretimyerid);
                        File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        File::delete(public_path('reports/outputs/onayformu/'.'OnayFormu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Detaylı Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                    }
                }
                if ($garanti == '1') //garanti içindeyse sistem tarafından onaylanır
                {
                    try {
                        $uretimyer = UretimYer::find($uretimyerid);
                        File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        File::delete(public_path('reports/outputs/onayformu/'.'OnayFormu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        if($detaylifiyat){
                            File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        }
                        $ucretlendirilen->durum = 2;
                        $ucretlendirilen->onaytipi = 0;
                        $ucretlendirilen->onaytarihi = date('Y-m-d H:i:s');
                        $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                        $ucretlendirilen->yetkili_id=$yetkili->id;
                        $ucretlendirilen->save();
                        $onaylanan = new Onaylanan;
                        $onaylanan->servis_id = $servisid;
                        $onaylanan->uretimyer_id = $uretimyerid;
                        $onaylanan->netsiscari_id = $netsiscariid;
                        $onaylanan->ucretlendirilen_id = $ucretlendirilen->id;
                        $onaylanan->yetkili_id = 1;
                        $onaylanan->onaytarihi = date('Y-m-d H:i:s');
                        $onaylanan->onaylamatipi = 0;
                        $onaylanan->save();
                        $secilensayaclar = "";
                        $secilensayaclist = array();
                        $garantisayaclar = "";
                        $garantisayaclist = array();
                        $iadesayaclar = "";
                        $iadesayaclist = array();
                        foreach ($arizafiyatlar as $arizafiyat) {
                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                            $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                            if($arizakayit->arizakayit_durum==7){
                                array_push($iadesayaclist, $sayacgelen->id);
                                $iadesayaclar .= ($iadesayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0) {
                                array_push($secilensayaclist, $sayacgelen->id);
                                $secilensayaclar .= ($secilensayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }else{
                                array_push($garantisayaclist, $sayacgelen->id);
                                $garantisayaclar .= ($garantisayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }
                            $sayacgelen->musterionay = 1;
                            $sayacgelen->teslimdurum = 1;
                            $sayacgelen->save();
                            $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                            $servistakip->onaylanan_id = $onaylanan->id;
                            $servistakip->durum = 5;
                            $servistakip->onaylanmatarihi = date('Y-m-d H:i:s');
                            $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                            $servistakip->kullanici_id = Auth::user()->id;
                            $servistakip->save();
                        }
                        if ($servisid == 5) //kalibrasyon tamamlanmışsa depo teslim yoksa bekleyecek
                        {
                            try {
                                $kalibrasyonsayi=0;
                                $kalibrasyonsayaclist="";
                                $garantisayi=0;
                                $garantisayaclist="";
                                $geriiadesayi=0;
                                $geriiadesayaclist="";
                                $periyodiksayi=0;
                                $periyodiklist="";
                                foreach ($arizafiyatlar as $arizafiyat) {
                                    $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                                    $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                    $depogelen=DepoGelen::find($sayacgelen->depogelen_id);
                                    if($arizakayit->arizakayit_durum != 3) { //yedek parça bekliyorsa depo teslim olmaz
                                        if ($depogelen->periyodik) {
                                            if ($sayacgelen->kalibrasyon) {
                                                $periyodiksayi++;
                                                $periyodiklist .= ($periyodiklist == "" ? "" : ",") . $sayacgelen->id;
                                            } else {
                                                $sayacgelen->teslimdurum = 0;
                                                $sayacgelen->save();
                                            }
                                        } else {
                                            if ($arizakayit->arizakayit_durum == 7) {
                                                $geriiadesayi++;
                                                $geriiadesayaclist .= ($geriiadesayaclist == "" ? "" : ",") . $sayacgelen->id;
                                            } else if ($sayacgelen->kalibrasyon) {
                                                if ($arizafiyat->toplamtutar > 0 || $arizafiyat->toplamtutar2 > 0) {
                                                    $kalibrasyonsayi++;
                                                    $kalibrasyonsayaclist .= ($kalibrasyonsayaclist == "" ? "" : ",") . $sayacgelen->id;
                                                } else {
                                                    $garantisayi++;
                                                    $garantisayaclist .= ($garantisayaclist == "" ? "" : ",") . $sayacgelen->id;
                                                }
                                            } else {
                                                $sayacgelen->teslimdurum = 0;
                                                $sayacgelen->save();
                                            }
                                        }
                                    }else{
                                        $sayacgelen->teslimdurum = 0;
                                        $sayacgelen->save();
                                    }
                                }
                                if($kalibrasyonsayi>0){
                                    $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                        ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',0)->first();
                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                    {
                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                        $kalibrasyonsayaclar = explode(',', $kalibrasyonsayaclist);
                                        $secilenler="";
                                        $adet = 0;
                                        foreach($kalibrasyonsayaclar as $sayacgelenid){
                                            if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                $adet++;
                                            }
                                        }
                                        $kalibrasyonsayi = $adet;
                                        if ($adet>0) {
                                            $depoteslim->secilenler .= ',' . $secilenler;
                                            $depoteslim->sayacsayisi += $adet;
                                            $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                            $depoteslim->save();
                                        }
                                    } else { //yeni depo teslimatı yapılacak
                                        $depoteslim = new DepoTeslim;
                                        $depoteslim->servis_id = $servisid;
                                        $depoteslim->netsiscari_id = $netsiscariid;
                                        $depoteslim->secilenler = $kalibrasyonsayaclist;
                                        $depoteslim->sayacsayisi = $kalibrasyonsayi;
                                        $depoteslim->depodurum = 0;
                                        $depoteslim->parabirimi_id=$birim;
                                        $depoteslim->parabirimi2_id=$birim2;
                                        $depoteslim->save();
                                    }
                                }
                                if($garantisayi>0){
                                    $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                        ->where('depodurum',0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',0)->first();
                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                    {
                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                        $garantisayaclar = explode(',', $garantisayaclist);
                                        $secilenler="";
                                        $adet = 0;
                                        foreach($garantisayaclar as $sayacgelenid){
                                            if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                $adet++;
                                            }
                                        }
                                        $garantisayi = $adet;
                                        if ($adet>0) {
                                            $depoteslim->secilenler .= ',' . $secilenler;
                                            $depoteslim->sayacsayisi += $adet;
                                            $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                            $depoteslim->save();
                                        }
                                    } else { //yeni depo teslimatı yapılacak
                                        $depoteslim = new DepoTeslim;
                                        $depoteslim->servis_id = $servisid;
                                        $depoteslim->netsiscari_id = $netsiscariid;
                                        $depoteslim->secilenler = $garantisayaclist;
                                        $depoteslim->sayacsayisi = $garantisayi;
                                        $depoteslim->depodurum = 0;
                                        $depoteslim->tipi = 1;
                                        $depoteslim->parabirimi_id=$birim;
                                        $depoteslim->parabirimi2_id=$birim2;
                                        $depoteslim->save();
                                    }
                                }
                                if($geriiadesayi>0){
                                    $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                        ->where('depodurum',0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',0)->first();
                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                    {
                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                        $geriiadesayaclar = explode(',', $geriiadesayaclist);
                                        $secilenler="";
                                        $adet = 0;
                                        foreach($geriiadesayaclar as $sayacgelenid){
                                            if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                $adet++;
                                            }
                                        }
                                        $geriiadesayi = $adet;
                                        if ($adet>0) {
                                            $depoteslim->secilenler .= ',' . $secilenler;
                                            $depoteslim->sayacsayisi += $adet;
                                            $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                            $depoteslim->save();
                                        }
                                    } else { //yeni depo teslimatı yapılacak
                                        $depoteslim = new DepoTeslim;
                                        $depoteslim->servis_id = $servisid;
                                        $depoteslim->netsiscari_id = $netsiscariid;
                                        $depoteslim->secilenler = $geriiadesayaclist;
                                        $depoteslim->sayacsayisi = $geriiadesayi;
                                        $depoteslim->depodurum = 0;
                                        $depoteslim->tipi = 2;
                                        $depoteslim->parabirimi_id=$birim;
                                        $depoteslim->parabirimi2_id=$birim2;
                                        $depoteslim->save();
                                    }
                                }
                                if($periyodiksayi>0){
                                    $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                        ->where('depodurum',0)->where('tipi',0)->where('periyodik',1)->where('subegonderim',0)->first();
                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                    {
                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                        $periyodiksayaclar = explode(',', $periyodiklist);
                                        $secilenler="";
                                        $adet = 0;
                                        foreach($periyodiksayaclar as $sayacgelenid){
                                            if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                $adet++;
                                            }
                                        }
                                        $periyodiksayi = $adet;
                                        if ($adet>0) {
                                            $depoteslim->secilenler .= ',' . $secilenler;
                                            $depoteslim->sayacsayisi += $adet;
                                            $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                            $depoteslim->save();
                                        }
                                    } else { //yeni depo teslimatı yapılacak
                                        $depoteslim = new DepoTeslim;
                                        $depoteslim->servis_id = $servisid;
                                        $depoteslim->netsiscari_id = $netsiscariid;
                                        $depoteslim->secilenler = $periyodiklist;
                                        $depoteslim->sayacsayisi = $periyodiksayi;
                                        $depoteslim->depodurum = 0;
                                        $depoteslim->periyodik = 1;
                                        $depoteslim->parabirimi_id=$birim;
                                        $depoteslim->parabirimi2_id=$birim2;
                                        $depoteslim->save();
                                    }
                                }
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi.', 'type' => 'error'));
                            }
                            BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                            if(($kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi)>0)
                                BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi);
                            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Sistem Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                            DB::commit();
                            return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sistem Tarafından Onaylama Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                        } else {
                            try {
                                if(count($secilensayaclist)>0){
                                    $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                        ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                    {
                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                        $secilenler="";
                                        $adet = 0;
                                        foreach($secilensayaclist as $sayacgelenid){
                                            if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                $adet++;
                                            }
                                        }
                                        if ($adet>0) {
                                            $depoteslim->secilenler .= ',' . $secilenler;
                                            $depoteslim->sayacsayisi += $adet;
                                            $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                            $depoteslim->save();
                                        }
                                    } else { //yeni depo teslimatı yapılacak
                                        $depoteslim = new DepoTeslim;
                                        $depoteslim->servis_id = $servisid;
                                        $depoteslim->netsiscari_id = $netsiscariid;
                                        $depoteslim->secilenler = $secilensayaclar;
                                        $depoteslim->sayacsayisi = count($secilensayaclist);
                                        $depoteslim->depodurum = 0;
                                        $depoteslim->subegonderim=$subedurum;
                                        $depoteslim->parabirimi_id=$birim;
                                        $depoteslim->parabirimi2_id=$birim2;
                                        $depoteslim->save();
                                    }
                                }
                                if(count($garantisayaclist)>0){
                                    $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                        ->where('depodurum', 0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                    {
                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                        $secilenler="";
                                        $adet = 0;
                                        foreach($garantisayaclist as $sayacgelenid){
                                            if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                $adet++;
                                            }
                                        }
                                        if ($adet>0) {
                                            $depoteslim->secilenler .= ',' . $secilenler;
                                            $depoteslim->sayacsayisi += $adet;
                                            $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                            $depoteslim->save();
                                        }
                                    } else { //yeni depo teslimatı yapılacak
                                        $depoteslim = new DepoTeslim;
                                        $depoteslim->servis_id = $servisid;
                                        $depoteslim->netsiscari_id = $netsiscariid;
                                        $depoteslim->secilenler = $garantisayaclar;
                                        $depoteslim->sayacsayisi = count($garantisayaclist);
                                        $depoteslim->depodurum = 0;
                                        $depoteslim->tipi = 1;
                                        $depoteslim->subegonderim=$subedurum;
                                        $depoteslim->parabirimi_id=$birim;
                                        $depoteslim->parabirimi2_id=$birim2;
                                        $depoteslim->save();
                                    }
                                }
                                if(count($iadesayaclist)>0){
                                    $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                        ->where('depodurum', 0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                    {
                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                        $secilenler="";
                                        $adet = 0;
                                        foreach($iadesayaclist as $sayacgelenid){
                                            if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                $adet++;
                                            }
                                        }
                                        if ($adet>0) {
                                            $depoteslim->secilenler .= ',' . $secilenler;
                                            $depoteslim->sayacsayisi += $adet;
                                            $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                            $depoteslim->save();
                                        }
                                    } else { //yeni depo teslimatı yapılacak
                                        $depoteslim = new DepoTeslim;
                                        $depoteslim->servis_id = $servisid;
                                        $depoteslim->netsiscari_id = $netsiscariid;
                                        $depoteslim->secilenler = $iadesayaclar;
                                        $depoteslim->sayacsayisi = count($iadesayaclist);
                                        $depoteslim->depodurum = 0;
                                        $depoteslim->tipi = 2;
                                        $depoteslim->subegonderim=$subedurum;
                                        $depoteslim->parabirimi_id=$birim;
                                        $depoteslim->parabirimi2_id=$birim2;
                                        $depoteslim->save();
                                    }
                                }
                                BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Sistem Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                                DB::commit();
                                return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sistem Tarafından Onaylama Yapıldı', 'text' => 'Garanti içinde olduğundan Sistem tarafından onaylama başarıyla yapıldı', 'type' => 'success'));
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi', 'type' => 'error'));
                            }
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylama onaylananlara kaydedilemedi', 'type' => 'error'));
                    }
                } else {
                    $ucretlendirilen->durum = 1;
                    $ucretlendirilen->onaytipi = 2;
                    $ucretlendirilen->gonderimtarihi = date('Y-m-d H:i:s');
                    $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                    $ucretlendirilen->dosyalar = $eklenenler;
                    $ucretlendirilen->yetkili_id=$yetkili->id;
                    $ucretlendirilen->save();
                    foreach ($arizafiyatlar as $arizafiyat) {
                        $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                        $servistakip->durum = 4;
                        $servistakip->gondermetarihi = date('Y-m-d H:i:s');
                        $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                        $servistakip->save();
                    }
                    $uretimyer = UretimYer::find($uretimyerid);
                    try {
                        Mail::send('mail.onayformu', array('ucretlendirilen' => $ucretlendirilen, 'mailicerik' => $mailicerik,
                            'onaylink' => $onaylink, 'ilkmail' => $ilkmail, 'girisadi' => $girisadi,'takipno'=>$takipno),
                            function ($message) use ($yetkili, $mailbaslik, $eklenenler, $uretimyer, $digermaillist, $digermail,$detaylifiyat) {
                                //$message->cc(Auth::user()->mail,Auth::user()->adi_soyadi);
                                if ($digermail != "")
                                    foreach ($digermaillist as $email)
                                        $message->cc($email);
                                $message->to($yetkili->email, $yetkili->adisoyadi)->subject($mailbaslik);
                                $message->attach(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                $message->attach(public_path('reports/outputs/onayformu/'.'OnayFormu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    $message->attach(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                                $message->attach(base_path() . '/pages/storage/cari.pdf');
                                $eklenenlist = explode(',', $eklenenler);
                                if ($eklenenler != "")
                                    foreach ($eklenenlist as $dosya) {
                                        $message->attach('assets/mailek/' . $dosya);
                                    }
                            });
                        $ucretlendirilen->mail = 1;
                        $ucretlendirilen->save();
                        $kullanici->ilkmail = 1;
                        $kullanici->save();
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        File::delete(public_path('reports/outputs/onayformu/'.'OnayFormu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        if($detaylifiyat){
                            File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        }
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Müşteri Onayı için Mail Gönderirken Hata ile karşılaşıldı', 'type' => 'error'));
                    }

                    if (count(Mail::failures()) > 0) {
                        DB::rollBack();
                        File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        File::delete(public_path('reports/outputs/onayformu/'.'OnayFormu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        if($detaylifiyat){
                            File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        }
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Müşteri Onayı için Mail Gönderirken Hata ile karşılaşıldı', 'type' => 'error'));
                    } else {
                        File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        File::delete(public_path('reports/outputs/onayformu/'.'OnayFormu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        if($detaylifiyat){
                            File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        }
                        BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                        BackendController::HatirlatmaEkle(6, $netsiscariid, $servisid, $sayacsayisi);
                        BackendController::BildirimEkle(5, $netsiscariid, $servisid, $sayacsayisi);
                        BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-envelope-o', $ucretlendirilen->id . ' Numaralı Ücretlendirme Onay için Mail ile Gönderildi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                        DB::commit();
                        return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Mail Gönderildi', 'text' => 'Müşteri Onayı için Mail Gönderildi', 'type' => 'success'));
                    }
                }
            } else { //bazıları onaylanmış
                try {
                    $yeniucretlendirilen = new Ucretlendirilen;
                    $yeniucretlendirilen->servis_id = $servisid;
                    $yeniucretlendirilen->uretimyer_id = $uretimyerid;
                    $yeniucretlendirilen->netsiscari_id = $netsiscariid;
                    $yeniucretlendirilen->secilenler = $secilenler;
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
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Yapılamadı', 'text' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
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
                    $yeniucretlendirilen->sayacsayisi = $sayacsayisi;
                    $yeniucretlendirilen->fiyat = $yenifiyat;
                    $yeniucretlendirilen->fiyat2 = $yenifiyat2;
                    $yeniucretlendirilen->parabirimi_id = $yeniparabirimi;
                    $yeniucretlendirilen->parabirimi2_id = $yeniparabirimi2;
                    $yeniucretlendirilen->kullanici_id = Auth::user()->id;
                    $yeniucretlendirilen->kayittarihi = date('Y-m-d H:i:s');
                    $yeniucretlendirilen->kurtarihi = $ucretlendirilen->kurtarihi;
                    $yeniucretlendirilen->durum = 1;
                    $yeniucretlendirilen->onaytipi = 2;
                    $yeniucretlendirilen->gonderimtarihi = date('Y-m-d H:i:s');
                    $yeniucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                    $yeniucretlendirilen->yetkili_id=$yetkili->id;
                    $yeniucretlendirilen->save();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Yapılamadı', 'text' => 'Seçilen sayaçlara ait yeni ücretlendirme oluşturulamadı', 'type' => 'error'));
                }
                DB::commit();
                DB::beginTransaction();
                $onaylink=$root.'/musterionay/'.$yeniucretlendirilen->id;
                $fiyatlandirma = BackendController::getFiyatlandirma($yeniucretlendirilen->id);
                if (is_null($fiyatlandirma)) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                }
                $onayformu = BackendController::getOnayFormu($yeniucretlendirilen->id);
                if (is_null($onayformu)) {
                    DB::rollBack();
                    $uretimyer = UretimYer::find($uretimyerid);
                    File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Onay Formu Oluşturulamadı', 'type' => 'error'));
                }
                if($detaylifiyat){
                    $detaylifiyatlandirma = BackendController::getDetaylifiyatlandirma($yeniucretlendirilen->id);
                    if (is_null($detaylifiyatlandirma)){
                        DB::rollBack();
                        $uretimyer = UretimYer::find($uretimyerid);
                        File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        File::delete(public_path('reports/outputs/onayformu/'.'OnayFormu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Detaylı Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                    }
                }
                try {
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
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Yapılamadı', 'text' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
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
                    $uretimyer = UretimYer::find($uretimyerid);
                    File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                    File::delete(public_path('reports/outputs/onayformu/'.'OnayFormu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                    if($detaylifiyat){
                        File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                    }
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Yapılamadı', 'text' => 'Kalan Sayaçlara Ait Ücretlendirme Güncellenemedi', 'type' => 'error'));
                }

                if ($yenigaranti == '1') //garanti içindeyse sistem tarafından onaylanır
                {
                    try {
                        $uretimyer = UretimYer::find($uretimyerid);
                        File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        File::delete(public_path('reports/outputs/onayformu/'.'OnayFormu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        if($detaylifiyat){
                            File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        }
                        $yeniucretlendirilen->durum = 2;
                        $yeniucretlendirilen->onaytipi = 0;
                        $yeniucretlendirilen->gonderimtarihi = NULL;
                        $yeniucretlendirilen->dosyalar = NULL;
                        $yeniucretlendirilen->onaytarihi = date('Y-m-d H:i:s');
                        $yeniucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                        $yeniucretlendirilen->save();
                        $onaylanan = new Onaylanan;
                        $onaylanan->servis_id = $servisid;
                        $onaylanan->uretimyer_id = $uretimyerid;
                        $onaylanan->netsiscari_id = $netsiscariid;
                        $onaylanan->ucretlendirilen_id = $yeniucretlendirilen->id;
                        $onaylanan->yetkili_id = $yetkili->id;
                        $onaylanan->onaytarihi = date('Y-m-d H:i:s');
                        $onaylanan->onaylamatipi = 0;
                        $onaylanan->save();
                        $secilensayaclar = "";
                        $secilensayaclist = array();
                        $garantisayaclar = "";
                        $garantisayaclist = array();
                        $iadesayaclar = "";
                        $iadesayaclist = array();
                        foreach ($arizafiyatlar as $arizafiyat) {
                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                            $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                            if($arizakayit->arizakayit_durum==7){
                                array_push($iadesayaclist, $sayacgelen->id);
                                $iadesayaclar .= ($iadesayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0) {
                                array_push($secilensayaclist, $sayacgelen->id);
                                $secilensayaclar .= ($secilensayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }else{
                                array_push($garantisayaclist, $sayacgelen->id);
                                $garantisayaclar .= ($garantisayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }
                            $sayacgelen->musterionay = 1;
                            $sayacgelen->teslimdurum = 1;
                            $sayacgelen->save();
                            $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                            $servistakip->ucretlendirilen_id = $yeniucretlendirilen->id;
                            $servistakip->onaylanan_id = $onaylanan->id;
                            $servistakip->durum = 5;
                            $servistakip->onaylanmatarihi = date('Y-m-d H:i:s');
                            $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                            $servistakip->save();
                        }
                        if ($servisid == 5) //kalibrasyon tamamlanmışsa depo teslim yoksa bekleyecek
                        {
                            try {
                                $kalibrasyonsayi=0;
                                $kalibrasyonsayaclist="";
                                $garantisayi=0;
                                $garantisayaclist="";
                            $geriiadesayi=0;
                            $geriiadesayaclist="";
                                $periyodiksayi=0;
                                $periyodiklist="";
                                foreach ($arizafiyatlar as $arizafiyat) {
                                    $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                                    $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                    $depogelen=DepoGelen::find($sayacgelen->depogelen_id);
                                    if($arizakayit->arizakayit_durum != 3) { //yedek parça bekliyorsa depo teslim olmaz
                                        if ($depogelen->periyodik) {
                                            if ($sayacgelen->kalibrasyon) {
                                                $periyodiksayi++;
                                                $periyodiklist .= ($periyodiklist == "" ? "" : ",") . $sayacgelen->id;
                                            } else {
                                                $sayacgelen->teslimdurum = 0;
                                                $sayacgelen->save();
                                            }
                                        } else {
                                            if ($arizakayit->arizakayit_durum == 7) {
                                                $geriiadesayi++;
                                                $geriiadesayaclist .= ($geriiadesayaclist == "" ? "" : ",") . $sayacgelen->id;
                                            } else if ($sayacgelen->kalibrasyon) {
                                                if ($arizafiyat->toplamtutar > 0 || $arizafiyat->toplamtutar2 > 0) {
                                                    $kalibrasyonsayi++;
                                                    $kalibrasyonsayaclist .= ($kalibrasyonsayaclist == "" ? "" : ",") . $sayacgelen->id;
                                                } else {
                                                    $garantisayi++;
                                                    $garantisayaclist .= ($garantisayaclist == "" ? "" : ",") . $sayacgelen->id;
                                                }
                                            } else {
                                                $sayacgelen->teslimdurum = 0;
                                                $sayacgelen->save();
                                            }
                                        }
                                    }else{
                                        $sayacgelen->teslimdurum = 0;
                                        $sayacgelen->save();
                                    }
                                }
                                if($kalibrasyonsayi>0){
                                    $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                        ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',0)->first();
                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                    {
                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                        $kalibrasyonsayaclar = explode(',', $kalibrasyonsayaclist);
                                        $secilenler="";
                                        $adet = 0;
                                        foreach($kalibrasyonsayaclar as $sayacgelenid){
                                            if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                $adet++;
                                            }
                                        }
                                        $kalibrasyonsayi = $adet;
                                        if ($adet>0) {
                                            $depoteslim->secilenler .= ',' . $secilenler;
                                            $depoteslim->sayacsayisi += $adet;
                                            $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                            $depoteslim->save();
                                        }
                                    } else { //yeni depo teslimatı yapılacak
                                        $depoteslim = new DepoTeslim;
                                        $depoteslim->servis_id = $servisid;
                                        $depoteslim->netsiscari_id = $netsiscariid;
                                        $depoteslim->secilenler = $kalibrasyonsayaclist;
                                        $depoteslim->sayacsayisi = $kalibrasyonsayi;
                                        $depoteslim->depodurum = 0;
                                        $depoteslim->parabirimi_id=$yeniparabirimi;
                                        $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                        $depoteslim->save();
                                    }
                                }
                                if($garantisayi>0){
                                    $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                        ->where('depodurum',0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',0)->first();
                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                    {
                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                        $garantisayaclar = explode(',', $garantisayaclist);
                                        $secilenler="";
                                        $adet = 0;
                                        foreach($garantisayaclar as $sayacgelenid){
                                            if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                $adet++;
                                            }
                                        }
                                        $garantisayi = $adet;
                                        if ($adet>0) {
                                            $depoteslim->secilenler .= ',' . $secilenler;
                                            $depoteslim->sayacsayisi += $adet;
                                            $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                            $depoteslim->save();
                                        }
                                    } else { //yeni depo teslimatı yapılacak
                                        $depoteslim = new DepoTeslim;
                                        $depoteslim->servis_id = $servisid;
                                        $depoteslim->netsiscari_id = $netsiscariid;
                                        $depoteslim->secilenler = $garantisayaclist;
                                        $depoteslim->sayacsayisi = $garantisayi;
                                        $depoteslim->depodurum = 0;
                                        $depoteslim->tipi = 1;
                                        $depoteslim->parabirimi_id=$yeniparabirimi;
                                        $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                        $depoteslim->save();
                                    }
                                }
                                if($geriiadesayi>0){
                                    $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                        ->where('depodurum',0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',0)->first();
                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                    {
                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                        $geriiadesayaclar = explode(',', $geriiadesayaclist);
                                        $secilenler="";
                                        $adet = 0;
                                        foreach($geriiadesayaclar as $sayacgelenid){
                                            if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                $adet++;
                                            }
                                        }
                                        $geriiadesayi = $adet;
                                        if ($adet>0) {
                                            $depoteslim->secilenler .= ',' . $secilenler;
                                            $depoteslim->sayacsayisi += $adet;
                                            $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                            $depoteslim->save();
                                        }
                                    } else { //yeni depo teslimatı yapılacak
                                        $depoteslim = new DepoTeslim;
                                        $depoteslim->servis_id = $servisid;
                                        $depoteslim->netsiscari_id = $netsiscariid;
                                        $depoteslim->secilenler = $geriiadesayaclist;
                                        $depoteslim->sayacsayisi = $geriiadesayi;
                                        $depoteslim->depodurum = 0;
                                        $depoteslim->tipi = 2;
                                        $depoteslim->parabirimi_id=$yeniparabirimi;
                                        $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                        $depoteslim->save();
                                    }
                                }
                                if($periyodiksayi>0){
                                    $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                        ->where('depodurum',0)->where('tipi',0)->where('periyodik',1)->where('subegonderim',0)->first();
                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                    {
                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                        $periyodiksayaclar = explode(',', $periyodiklist);
                                        $secilenler="";
                                        $adet = 0;
                                        foreach($periyodiksayaclar as $sayacgelenid){
                                            if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                $adet++;
                                            }
                                        }
                                        $periyodiksayi = $adet;
                                        if ($adet>0) {
                                            $depoteslim->secilenler .= ',' . $secilenler;
                                            $depoteslim->sayacsayisi += $adet;
                                            $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                            $depoteslim->save();
                                        }
                                    } else { //yeni depo teslimatı yapılacak
                                        $depoteslim = new DepoTeslim;
                                        $depoteslim->servis_id = $servisid;
                                        $depoteslim->netsiscari_id = $netsiscariid;
                                        $depoteslim->secilenler = $periyodiklist;
                                        $depoteslim->sayacsayisi = $periyodiksayi;
                                        $depoteslim->depodurum = 0;
                                        $depoteslim->periyodik = 1;
                                        $depoteslim->parabirimi_id=$yeniparabirimi;
                                        $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                        $depoteslim->save();
                                    }
                                }
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi.', 'type' => 'error'));
                            }
                            BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                            if(($kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi)>0)
                                BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $kalibrasyonsayi+$geriiadesayi+$garantisayi+$periyodiksayi);
                            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $yeniucretlendirilen->id . ' Numaralı Ücretlendirme Sistem Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $yeniucretlendirilen->id);
                            DB::commit();
                            return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sistem Tarafından Onaylama Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                        } else {
                            try {
                                if(count($secilensayaclist)>0){
                                    $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                        ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                    {
                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                        $secilenler="";
                                        $adet = 0;
                                        foreach($secilensayaclist as $sayacgelenid){
                                            if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                $adet++;
                                            }
                                        }
                                        if ($adet>0) {
                                            $depoteslim->secilenler .= ',' . $secilenler;
                                            $depoteslim->sayacsayisi += $adet;
                                            $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                            $depoteslim->save();
                                        }
                                    } else { //yeni depo teslimatı yapılacak
                                        $depoteslim = new DepoTeslim;
                                        $depoteslim->servis_id = $servisid;
                                        $depoteslim->netsiscari_id = $netsiscariid;
                                        $depoteslim->secilenler = $secilensayaclar;
                                        $depoteslim->sayacsayisi = count($secilensayaclist);
                                        $depoteslim->depodurum = 0;
                                        $depoteslim->subegonderim=$subedurum;
                                        $depoteslim->parabirimi_id=$yeniparabirimi;
                                        $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                        $depoteslim->save();
                                    }
                                }
                                if(count($garantisayaclist)>0){
                                    $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                        ->where('depodurum', 0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                    {
                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                        $secilenler="";
                                        $adet = 0;
                                        foreach($garantisayaclist as $sayacgelenid){
                                            if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                $adet++;
                                            }
                                        }
                                        if ($adet>0) {
                                            $depoteslim->secilenler .= ',' . $secilenler;
                                            $depoteslim->sayacsayisi += $adet;
                                            $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                            $depoteslim->save();
                                        }
                                    } else { //yeni depo teslimatı yapılacak
                                        $depoteslim = new DepoTeslim;
                                        $depoteslim->servis_id = $servisid;
                                        $depoteslim->netsiscari_id = $netsiscariid;
                                        $depoteslim->secilenler = $garantisayaclar;
                                        $depoteslim->sayacsayisi = count($garantisayaclist);
                                        $depoteslim->depodurum = 0;
                                        $depoteslim->tipi = 1;
                                        $depoteslim->subegonderim=$subedurum;
                                        $depoteslim->parabirimi_id=$yeniparabirimi;
                                        $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                        $depoteslim->save();
                                    }
                                }
                                if(count($iadesayaclist)>0){
                                    $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                        ->where('depodurum', 0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                    if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                    {
                                        $secilenlist = explode(',', $depoteslim->secilenler);
                                        $secilenler="";
                                        $adet = 0;
                                        foreach($iadesayaclist as $sayacgelenid){
                                            if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                $adet++;
                                            }
                                        }
                                        if ($adet>0) {
                                            $depoteslim->secilenler .= ',' . $secilenler;
                                            $depoteslim->sayacsayisi += $adet;
                                            $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                            $depoteslim->save();
                                        }
                                    } else { //yeni depo teslimatı yapılacak
                                        $depoteslim = new DepoTeslim;
                                        $depoteslim->servis_id = $servisid;
                                        $depoteslim->netsiscari_id = $netsiscariid;
                                        $depoteslim->secilenler = $iadesayaclar;
                                        $depoteslim->sayacsayisi = count($iadesayaclist);
                                        $depoteslim->depodurum = 0;
                                        $depoteslim->tipi = 2;
                                        $depoteslim->subegonderim=$subedurum;
                                        $depoteslim->parabirimi_id=$yeniparabirimi;
                                        $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                        $depoteslim->save();
                                    }
                                }
                                BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $yeniucretlendirilen->id . ' Numaralı Ücretlendirme Sistem Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $yeniucretlendirilen->id);
                                DB::commit();
                                return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sistem Tarafından Onaylama Yapıldı', 'text' => 'Garanti içinde olduğundan Sistem tarafından onaylama başarıyla yapıldı', 'type' => 'success'));
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi', 'type' => 'error'));
                            }
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylama onaylananlara kaydedilemedi', 'type' => 'error'));
                    }

                } else {
                    $yeniucretlendirilen->dosyalar = $eklenenler;
                    $yeniucretlendirilen->save();
                    foreach ($arizafiyatlar as $arizafiyat) {
                        $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                        $servistakip->ucretlendirilen_id = $yeniucretlendirilen->id;
                        $servistakip->durum = 4;
                        $servistakip->gondermetarihi = date('Y-m-d H:i:s');
                        $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                        $servistakip->save();
                    }
                    $uretimyer = UretimYer::find($uretimyerid);
                    try {
                        Mail::send('mail.onayformu', array('ucretlendirilen' => $yeniucretlendirilen, 'mailicerik' => $mailicerik,
                            'onaylink' => $onaylink, 'ilkmail' => $ilkmail, 'girisadi' => $girisadi,'takipno'=>$takipno),
                            function ($message) use ($yetkili, $mailbaslik, $eklenenler, $uretimyer, $digermaillist, $digermail,$detaylifiyat) {
                                //$message->cc(Auth::user()->mail,Auth::user()->adi_soyadi);
                                if ($digermail != "")
                                    foreach ($digermaillist as $email)
                                        $message->cc($email);
                                $message->to($yetkili->email, $yetkili->adisoyadi)->subject($mailbaslik);
                                $message->attach(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                $message->attach(public_path('reports/outputs/onayformu/'.'OnayFormu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    $message->attach(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                                $message->attach(base_path() . '/pages/storage/cari.pdf');
                                $eklenenlist = explode(',', $eklenenler);
                                if ($eklenenler != "")
                                    foreach ($eklenenlist as $dosya) {
                                        $message->attach('assets/mailek/' . $dosya);
                                    }
                            });
                        $yeniucretlendirilen->mail = 1;
                        $yeniucretlendirilen->save();
                        $kullanici->ilkmail=1;
                        $kullanici->save();
                    }
                    catch(Exception $e){
                        DB::rollBack();
                        Log::error($e);
                        File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        File::delete(public_path('reports/outputs/onayformu/'.'OnayFormu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        if($detaylifiyat){
                            File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        }
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Müşteri Onayı için Mail Gönderirken Hata ile karşılaşıldı', 'type' => 'error'));
                    }

                    if(count(Mail::failures()) > 0){
                        DB::rollBack();
                        File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        File::delete(public_path('reports/outputs/onayformu/'.'OnayFormu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        if($detaylifiyat){
                            File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        }
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Müşteri Onayı için Mail Gönderirken Hata ile karşılaşıldı', 'type' => 'error'));
                    }else{
                        File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        File::delete(public_path('reports/outputs/onayformu/'.'OnayFormu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        if($detaylifiyat){
                            File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        }
                        BackendController::HatirlatmaGuncelle(5,$netsiscariid,$servisid,$sayacsayisi);
                        BackendController::HatirlatmaEkle(6,$netsiscariid,$servisid,$sayacsayisi);
                        BackendController::BildirimEkle(5,$netsiscariid,$servisid,$sayacsayisi);
                        BackendController::IslemEkle(1,Auth::user()->id,'label-success','fa-envelope-o',$yeniucretlendirilen->id.' Numaralı Ücretlendirme için Onay Maili Gönderildi.','Ekleyen:'.Auth::user()->adi_soyadi.',Ücretlendirme Numarası:'.$yeniucretlendirilen->id);
                        DB::commit();
                        return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Mail Gönderildi', 'text' => 'Müşteri Onayı için Mail Gönderildi', 'type' => 'success'));
                    }
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function postTekrarmailgonder(){
        try {
            $rules = ['tekrarmailadisoyadi' => 'required', 'tekrarmailyetkilimail' => 'required|email', 'tekrarmailkonu' => 'required', 'tekraricerik' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $digermail = Input::get('tekrarmailcc');
            $digermaillist = explode(';', $digermail);
            if ($digermail != "")
                foreach ($digermaillist as $email) {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Diğer Mail Listesi Hatası', 'text' => $email . ' geçerli bir email değil. Birden fazla girilen mail adreslerini ; ile ayırınız!', 'type' => 'error'));
                    }
                }
            if (Input::has('detaylifiyatlandirma')) //mail gönderilecek
            {
                $detaylifiyat=1;
            }else{
                $detaylifiyat=0;
            }
            $ucretlendirilenid = Input::get('tekrarmailid');
            $secilenler = Input::get('tekrarmailsecilenler');
            $secilenlist = explode(',', $secilenler);
            $yetkiliadi = ucwords(mb_strtolower(Input::get('tekrarmailadisoyadi'), "UTF-8"));
            $yetkilimail = Input::get('tekrarmailyetkilimail');
            $yetkiliid = Input::get('tekrarmailyetkiliid');
            $mailicerik = Input::get('tekraricerik');
            $mailbaslik = Input::get('tekrarmailkonu');
            $onaylink = Input::get('tekrarmaillink');
            DB::beginTransaction();
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            $uretimyerid = $ucretlendirilen->uretimyer_id;
            $netsiscariid = $ucretlendirilen->netsiscari_id;
            $netsiscari = NetsisCari::find($netsiscariid);
            $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenlist)->get();
            try {
                if ($yetkiliid == -1) {
                    $kullanici = Kullanici::where('email', $yetkilimail)->whereIn('grup_id', array(6, 19))->first();
                    if (!$kullanici) { //kullanıcı yoksa
                        $kullanici = Kullanici::onlyTrashed()->where('email', $yetkilimail)->whereIn('grup_id', array(6, 19))->first();
                        if ($kullanici) { //kullanıcı silinmişse geri getirilir bilgileri güncellenir. Kullanıcının eski yetkili bilgileri pasife çekilir.
                            $kullanici->restore();
                            $kullanici->adi_soyadi = $yetkiliadi;
                            $kullanici->password = Hash::make('manas123');
                            $kullanici->email = $yetkilimail;
                            $kullanici->telefon = '';
                            $kullanici->servis_id = 0;
                            $kullanici->grup_id = 19;
                            $kullanici->ilkmail = false;
                            $kullanici->aktifdurum = 1;
                            $kullanici->save();
                            $yetkili = Yetkili::where('kullanici_id', $kullanici->id)->get();
                            foreach ($yetkili as $eskiyetkili) {
                                $eskiyetkili->aktif = 0;
                                $eskiyetkili->save();
                            }
                        } else { //kullanıcı yoksa yeni oluşturulur.
                            $kullanici = new Kullanici;
                            $kullanici->adi_soyadi = $yetkiliadi;
                            $kullanici->girisadi = BackendController::GirisAdiBelirle($netsiscariid, $uretimyerid);
                            $kullanici->password = Hash::make('manas123');
                            $kullanici->email = $yetkilimail;
                            $kullanici->telefon = '';
                            $kullanici->servis_id = 0;
                            $kullanici->grup_id = 19;
                            $kullanici->aktifdurum = 1;
                            $kullanici->save();
                        }
                    }
                    $yetkili = new Yetkili;
                    $yetkili->email = $yetkilimail;
                    $yetkili->telefon = '';
                    $yetkili->netsiscari_id = $netsiscariid;
                    $yetkili->kullanici_id = $kullanici->id;
                    $yetkili->aktif = 1;
                    $yetkili->save();
                } else if ($yetkiliid == "") {
                    $yetkili = Yetkili::where('netsiscari_id', $netsiscariid)->where('aktif', 1)->first();
                    $kullanici = Kullanici::find($yetkili->kullanici_id);
                } else {
                    if (strpos($yetkiliid, '0_') !== false) //yetkili yok ve kullanıcı listesinden seçildiyse
                    {
                        $yetkililist = explode('_', $yetkiliid);
                        $kullanici = Kullanici::find($yetkililist[1]);
                        $yetkili = new Yetkili;
                        $yetkili->email = $yetkilimail;
                        $yetkili->telefon = '';
                        $yetkili->aktif = 1;
                        $yetkili->netsiscari_id = $netsiscariid;
                        $yetkili->kullanici_id = $kullanici->id;
                        $yetkili->save();
                    } else {
                        $yetkili = Yetkili::find($yetkiliid);
                        $kullanici = Kullanici::find($yetkili->kullanici_id);
                    }
                }
                if ($yetkili) //yetkili varsa
                {
                    $yetkili->email = $yetkilimail;
                    $yetkili->aktif = 1;
                    $yetkili->save();
                    $kullanici->email = ($kullanici->email == NULL) ? $yetkilimail : $kullanici->email;
                    $kullanici->save();
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Yetkili Kaydetme Hatası', 'text' => 'Yetkili Bilgileri Kaydedilirken Hata Oluştu', 'type' => 'error'));
            }
            $ilkmail = $kullanici->ilkmail;
            $girisadi = $kullanici->girisadi;
            $takipno = $netsiscari->takipno;
            $eklenenler = "";
            If (Input::hasFile('tekrardosya')) {
                $dosyalar = Input::file('tekrardosya');
                foreach ($dosyalar as $dosya) {
                    $dosyaadi = $dosya->getClientOriginalName();
                    $dosyaadi = pathinfo($dosyaadi, PATHINFO_FILENAME);
                    $uzanti = $dosya->getClientOriginalExtension();
                    $isim = Str::slug($dosyaadi) . Str::slug(str_random(5)) . '.' . $uzanti;
                    $dosya->move('assets/mailek/', $isim);
                    $eklenenler .= ($eklenenler == "" ? "" : ",") . $isim;
                }
            }

            $fiyatlandirma = BackendController::getFiyatlandirma($ucretlendirilenid);
            if (is_null($fiyatlandirma)) {
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
            }
            $onayformu = BackendController::getOnayFormu($ucretlendirilenid);
            if (is_null($onayformu)) {
                DB::rollBack();
                $uretimyer = UretimYer::find($uretimyerid);
                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Onay Formu Oluşturulamadı', 'type' => 'error'));
            }
            if($detaylifiyat){
                $detaylifiyatlandirma = BackendController::getDetaylifiyatlandirma($ucretlendirilenid);
                if (is_null($detaylifiyatlandirma)){
                    DB::rollBack();
                    $uretimyer = UretimYer::find($uretimyerid);
                    File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                    File::delete(public_path('reports/outputs/onayformu/'.'OnayFormu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Detaylı Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                }
            }
            $ucretlendirilen->durum = 1;
            $ucretlendirilen->onaytipi = 2;
            $ucretlendirilen->gonderimtarihi = date('Y-m-d H:i:s');
            $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
            $ucretlendirilen->dosyalar = $eklenenler;
            $ucretlendirilen->yetkili_id=$yetkili->id;
            $ucretlendirilen->save();
            foreach ($arizafiyatlar as $arizafiyat) {
                $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                $servistakip->durum = 4;
                $servistakip->gondermetarihi = date('Y-m-d H:i:s');
                $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                $servistakip->save();
            }
            $uretimyer = UretimYer::find($uretimyerid);
            try {
                Mail::send('mail.onayformu', array('ucretlendirilen' => $ucretlendirilen, 'mailicerik' => $mailicerik,
                    'onaylink' => $onaylink, 'ilkmail' => $ilkmail, 'girisadi' => $girisadi,'takipno'=>$takipno),
                    function ($message) use ($yetkili, $mailbaslik, $eklenenler, $uretimyer, $digermaillist, $digermail,$detaylifiyat) {
                        //$message->cc(Auth::user()->mail,Auth::user()->adi_soyadi);
                        if ($digermail != "")
                            foreach ($digermaillist as $email)
                                $message->cc($email);
                        $message->to($yetkili->email, $yetkili->adisoyadi)->subject($mailbaslik);
                        $message->attach(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        $message->attach(public_path('reports/outputs/onayformu/'.'OnayFormu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        if($detaylifiyat){
                           $message->attach(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        }
                        $message->attach(base_path() . '/pages/storage/cari.pdf');
                        $eklenenlist = explode(',', $eklenenler);
                        if ($eklenenler != "")
                            foreach ($eklenenlist as $dosya) {
                                $message->attach('assets/mailek/' . $dosya);
                            }
                    });
                $ucretlendirilen->mail = 1;
                $ucretlendirilen->save();
                $kullanici->ilkmail = 1;
                $kullanici->save();
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                File::delete(public_path('reports/outputs/onayformu/'.'OnayFormu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                if($detaylifiyat){
                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                }
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Müşteri Onayı için Mail Gönderirken Hata ile karşılaşıldı', 'type' => 'error'));
            }

            if (count(Mail::failures()) > 0) {
                DB::rollBack();
                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                File::delete(public_path('reports/outputs/onayformu/'.'OnayFormu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                if($detaylifiyat){
                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                }
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Müşteri Onayı için Mail Gönderirken Hata ile karşılaşıldı', 'type' => 'error'));
            } else {
                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                File::delete(public_path('reports/outputs/onayformu/'.'OnayFormu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                if($detaylifiyat){
                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                }
                BackendController::IslemEkle(2, Auth::user()->id, 'label-success', 'fa-envelope-o', $ucretlendirilen->id . ' Numaralı Ücretlendirme Onay için Tekrar Gönderildi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                DB::commit();
                return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Tekrar Mail Gönderildi', 'text' => 'Müşteri Onayı için Tekrar Mail Gönderildi', 'type' => 'success'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function postMailtelefonileonayla(){
        try {
            if (Input::has('mailvar')) //mail gönderilecek
            {
                $rules = ['mailteladisoyadi' => 'required', 'mailtelyetkilitel' => 'required', 'mailtelyetkilimail' => 'required|email'];
                $mail = 1;
                if (Input::has('detaylifiyatlandirma')) //mail gönderilecek
                {
                    $detaylifiyat=1;
                }else{
                    $detaylifiyat=0;
                }
            } else // mail gönderilmeyecek
            {
                $rules = ['mailteladisoyadi' => 'required', 'mailtelyetkilitel' => 'required'];
                $mail = 0;
                $detaylifiyat = 0;
            }
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $digermail = Input::get('mailtelmailcc');
            $digermaillist = explode(';', $digermail);
            if ($digermail != "")
                foreach ($digermaillist as $email) {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Diğer Mail Listesi Hatası', 'text' => $email . ' geçerli bir email değil. Birden fazla girilen mail adreslerini ; ile ayırınız!', 'type' => 'error'));
                    }
                }
            $ucretlendirilenid = Input::get('mailtelid');
            $secilenler = Input::get('mailtelsecilenler');
            $secilenlist = explode(',', $secilenler);
            $yetkiliadi = Input::get('mailteladisoyadi');
            $yetkilitel = Input::get('mailtelyetkilitel');
            $yetkilimail = Input::get('mailtelyetkilimail');
            $yetkiliid = Input::get('mailtelyetkiliid');
            $birim = Input::get('mailtelbirim');
            $birim2 = Input::get('mailtelbirim2')=="" ? null : Input::get('mailtelbirim2');
            DB::beginTransaction();
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            if($ucretlendirilen->durum!=1){
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı!', 'text' => 'Ücretlendirme Zaten Onaylanmış!', 'type' => 'warning'));
            }
            $servisid = $ucretlendirilen->servis_id;
            $uretimyerid = $ucretlendirilen->uretimyer_id;
            $uretimyer = UretimYer::find($uretimyerid);
            $netsiscariid = $ucretlendirilen->netsiscari_id;
            $sayacsayisi = count($secilenlist);
            $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenlist)->get();
            $servistakipler = ServisTakip::whereIn('arizafiyat_id', $secilenlist)->get();
            $sube = Sube::where('netsiscari_id', $netsiscariid)->where('aktif', 1)->first();
            $netsiscari = NetsisCari::find($netsiscariid);
            $subedurum = 0;
            if($sube){
                foreach ($servistakipler as $servistakip){
                    if($servistakip->subedurum)
                        $subedurum = 1;
                }
            }
            try {
                if ($yetkiliid == -1) {
                    $kullanici = Kullanici::where('email',$yetkilimail)->whereIn('grup_id',array(6,19))->first();
                    if(!$kullanici){ //kullanıcı yoksa
                        $kullanici=Kullanici::onlyTrashed()->where('email',$yetkilimail)->whereIn('grup_id',array(6,19))->first();
                        if($kullanici){ //kullanıcı silinmişse geri getirilir bilgileri güncellenir. Kullanıcının eski yetkili bilgileri pasife çekilir.
                            $kullanici->restore();
                            $kullanici->adi_soyadi = $yetkiliadi;
                            $kullanici->password = Hash::make('manas123');
                            $kullanici->email = $yetkilimail;
                            $kullanici->telefon = $yetkilitel;
                            $kullanici->servis_id = 0;
                            $kullanici->grup_id = 19;
                            $kullanici->ilkmail=false;
                            $kullanici->aktifdurum = 1;
                            $kullanici->save();
                            $yetkili=Yetkili::where('kullanici_id',$kullanici->id)->get();
                            foreach ($yetkili as $eskiyetkili){
                                $eskiyetkili->aktif=0;
                                $eskiyetkili->save();
                            }
                        }else{ //kullanıcı yoksa yeni oluşturulur.
                            $kullanici = new Kullanici;
                            $kullanici->adi_soyadi = $yetkiliadi;
                            $kullanici->girisadi = BackendController::GirisAdiBelirle($netsiscariid,$uretimyerid);
                            $kullanici->password = Hash::make('manas123');
                            $kullanici->email = $yetkilimail;
                            $kullanici->telefon = $yetkilitel;
                            $kullanici->servis_id = 0;
                            $kullanici->grup_id = 19;
                            $kullanici->aktifdurum = 1;
                            $kullanici->save();
                        }
                    }
                    $yetkili=new Yetkili;
                    $yetkili->email = $yetkilimail;
                    $yetkili->telefon = $yetkilitel;
                    $yetkili->netsiscari_id=$netsiscariid;
                    $yetkili->kullanici_id=$kullanici->id;
                    $yetkili->aktif=1;
                    $yetkili->save();

                }else if($yetkiliid == ""){ //yetkili güncellenecek
                    $yetkili = Yetkili::where('netsiscari_id', $netsiscariid)->where('aktif', 1)->first();
                    $kullanici=Kullanici::find($yetkili->kullanici_id);
                }else{
                    if (strpos($yetkiliid, '0_') !== false) //yetkili yok ve kullanıcı listesinden seçildiyse
                    {
                        $yetkililist=explode('_',$yetkiliid);
                        $kullanici=Kullanici::find($yetkililist[1]);
                        $yetkili=new Yetkili;
                        $yetkili->email=$yetkilimail;
                        $yetkili->telefon=$yetkilitel;
                        $yetkili->aktif=1;
                        $yetkili->netsiscari_id=$netsiscariid;
                        $yetkili->kullanici_id=$kullanici->id;
                        $yetkili->save();
                    }else{
                        $yetkili=Yetkili::find($yetkiliid);
                        $kullanici=Kullanici::find($yetkili->kullanici_id);
                    }
                }
                if ($yetkili) //yetkili varsa
                {
                    $yetkili->email = $yetkilimail;
                    $yetkili->telefon = $yetkilitel;
                    $yetkili->aktif = 1;
                    $yetkili->save();
                    $kullanici->email=($kullanici->email==NULL) ? $yetkilimail : $kullanici->email;
                    $kullanici->telefon=($kullanici->telefon==NULL) ? $yetkilitel : $kullanici->telefon;
                    $kullanici->save();
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Yetkili Bilgisi Hatalı', 'text' => 'Yetkili Bilgisi Kaydedilemedi', 'type' => 'error'));
            }
            $ilkmail = $kullanici->ilkmail;
            $girisadi = $kullanici->girisadi;
            $takipno = $netsiscari->takipno;
            if ($mail) {
                $fiyatlandirma = BackendController::getFiyatlandirma($ucretlendirilenid);
                if (is_null($fiyatlandirma)){
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                }
                if($detaylifiyat){
                    $detaylifiyatlandirma = BackendController::getDetaylifiyatlandirma($ucretlendirilenid);
                    if (is_null($detaylifiyatlandirma)){
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Detaylı Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                    }
                }
            }
            try {
                $ucretlendirilen->durum = 2;
                $ucretlendirilen->onaytipi = 1;
                $ucretlendirilen->onaytarihi = date('Y-m-d H:i:s');
                $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                $ucretlendirilen->yetkili_id = $yetkili->id;
                $ucretlendirilen->save();
                $onaylanan = new Onaylanan;
                $onaylanan->servis_id = $servisid;
                $onaylanan->uretimyer_id = $uretimyerid;
                $onaylanan->netsiscari_id = $netsiscariid;
                $onaylanan->ucretlendirilen_id = $ucretlendirilen->id;
                $onaylanan->yetkili_id = $yetkili->id;
                $onaylanan->onaytarihi = date('Y-m-d H:i:s');
                $onaylanan->onaylamatipi = 1;
                $onaylanan->save();
                $secilensayaclar = "";
                $secilensayaclist = array();
                $garantisayaclar = "";
                $garantisayaclist = array();
                $iadesayaclar = "";
                $iadesayaclist = array();
                try {
                    foreach ($arizafiyatlar as $arizafiyat) {
                        $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                        $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                        if($arizakayit->arizakayit_durum==7) {
                            array_push($iadesayaclist, $sayacgelen->id);
                            $iadesayaclar .= ($iadesayaclar == "" ? "" : ",") . $sayacgelen->id;
                        }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0) {
                            array_push($secilensayaclist, $sayacgelen->id);
                            $secilensayaclar .= ($secilensayaclar == "" ? "" : ",") . $sayacgelen->id;
                        }else{
                            array_push($garantisayaclist, $sayacgelen->id);
                            $garantisayaclar .= ($garantisayaclar == "" ? "" : ",") . $sayacgelen->id;
                        }
                        $sayacgelen->musterionay = 1;
                        $sayacgelen->teslimdurum = 1;
                        $sayacgelen->save();
                        $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                        $servistakip->onaylanan_id = $onaylanan->id;
                        $servistakip->durum = 5;
                        $servistakip->onaylanmatarihi = date('Y-m-d H:i:s');
                        $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                        $servistakip->kullanici_id = Auth::user()->id;
                        $servistakip->save();
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Kaydedilemedi', 'text' => 'Sayaç Gelen ve Servis Takip Bilgisi Güncellenemedi', 'type' => 'error'));
                }
                try {
                    if ($mail) {
                        try {
                            Mail::send('mail.bilgilendirme', array('ucretlendirilen' => $ucretlendirilen, 'ilkmail' => $ilkmail, 'girisadi' => $girisadi,'takipno'=>$takipno),
                                function ($message) use ($yetkili, $uretimyer, $digermaillist, $digermail,$detaylifiyat) {
                                    //$message->cc(Auth::user()->mail,Auth::user()->adi_soyadi);
                                    if ($digermail != "")
                                        foreach ($digermaillist as $email)
                                            $message->cc($email);
                                    $message->to($yetkili->email, $yetkili->adisoyadi)->subject("Manas ServisTakip Fiyatlandırma Bilgilendirme");
                                    $message->attach(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                    if($detaylifiyat){
                                        $message->attach(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                    }
                                });
                            $ucretlendirilen->mail = 1;
                            $ucretlendirilen->save();
                            $kullanici->ilkmail = 1;
                            $kullanici->save();
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                            if($detaylifiyat){
                                File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                            }
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => 'Mail Gönderirken Hata Oluştu', 'type' => 'error'));
                        }
                        if (count(Mail::failures()) > 0) {
                            DB::rollBack();
                            File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                            if($detaylifiyat){
                                File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                            }
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => 'Mail Gönderirken Hata Oluştu', 'type' => 'error'));
                        } else {
                            File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                            if($detaylifiyat){
                                File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                            }
                        }
                    } else {
                        File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        if($detaylifiyat){
                            File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                        }
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                }
                if ($servisid == 5) //kalibrasyon tamamlanmışsa depo teslim yoksa bekleyecek
                {
                    try {
                        $kalibrasyonsayi=0;
                        $kalibrasyonsayaclist="";
                        $garantisayi=0;
                        $garantisayaclist="";
                        $geriiadesayi=0;
                        $geriiadesayaclist="";
                        $periyodiksayi=0;
                        $periyodiklist="";
                        foreach ($arizafiyatlar as $arizafiyat) {
                            $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                            $depogelen=DepoGelen::find($sayacgelen->depogelen_id);
                            if($arizakayit->arizakayit_durum != 3) { //yedek parça bekliyorsa depo teslim olmaz
                                if ($depogelen->periyodik) {
                                    if ($sayacgelen->kalibrasyon) {
                                        $periyodiksayi++;
                                        $periyodiklist .= ($periyodiklist == "" ? "" : ",") . $sayacgelen->id;
                                    } else {
                                        $sayacgelen->teslimdurum = 0;
                                        $sayacgelen->save();
                                    }
                                } else {
                                    if ($arizakayit->arizakayit_durum == 7) {
                                        $geriiadesayi++;
                                        $geriiadesayaclist .= ($geriiadesayaclist == "" ? "" : ",") . $sayacgelen->id;
                                    } else if ($sayacgelen->kalibrasyon) {
                                        if ($arizafiyat->toplamtutar > 0 || $arizafiyat->toplamtutar2 > 0) {
                                            $kalibrasyonsayi++;
                                            $kalibrasyonsayaclist .= ($kalibrasyonsayaclist == "" ? "" : ",") . $sayacgelen->id;
                                        } else {
                                            $garantisayi++;
                                            $garantisayaclist .= ($garantisayaclist == "" ? "" : ",") . $sayacgelen->id;
                                        }
                                    } else {
                                        $sayacgelen->teslimdurum = 0;
                                        $sayacgelen->save();
                                    }
                                }
                            }else{
                                $sayacgelen->teslimdurum = 0;
                                $sayacgelen->save();
                            }
                        }
                        if($kalibrasyonsayi>0){
                            $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',0)->first();
                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                            {
                                $secilenlist = explode(',', $depoteslim->secilenler);
                                $kalibrasyonsayaclar = explode(',', $kalibrasyonsayaclist);
                                $secilenler="";
                                $adet = 0;
                                foreach($kalibrasyonsayaclar as $sayacgelenid){
                                    if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                        $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                        $adet++;
                                    }
                                }
                                $kalibrasyonsayi = $adet;
                                if ($adet>0) {
                                    $depoteslim->secilenler .= ',' . $secilenler;
                                    $depoteslim->sayacsayisi += $adet;
                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                    $depoteslim->save();
                                }
                            } else { //yeni depo teslimatı yapılacak
                                $depoteslim = new DepoTeslim;
                                $depoteslim->servis_id = $servisid;
                                $depoteslim->netsiscari_id = $netsiscariid;
                                $depoteslim->secilenler = $kalibrasyonsayaclist;
                                $depoteslim->sayacsayisi = $kalibrasyonsayi;
                                $depoteslim->depodurum = 0;
                                $depoteslim->parabirimi_id=$birim;
                                $depoteslim->parabirimi2_id=$birim2;
                                $depoteslim->save();
                            }
                        }
                        if($garantisayi>0){
                            $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                ->where('depodurum',0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',0)->first();
                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                            {
                                $secilenlist = explode(',', $depoteslim->secilenler);
                                $garantisayaclar = explode(',', $garantisayaclist);
                                $secilenler="";
                                $adet = 0;
                                foreach($garantisayaclar as $sayacgelenid){
                                    if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                        $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                        $adet++;
                                    }
                                }
                                $garantisayi = $adet;
                                if ($adet>0) {
                                    $depoteslim->secilenler .= ',' . $secilenler;
                                    $depoteslim->sayacsayisi += $adet;
                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                    $depoteslim->save();
                                }
                            } else { //yeni depo teslimatı yapılacak
                                $depoteslim = new DepoTeslim;
                                $depoteslim->servis_id = $servisid;
                                $depoteslim->netsiscari_id = $netsiscariid;
                                $depoteslim->secilenler = $garantisayaclist;
                                $depoteslim->sayacsayisi = $garantisayi;
                                $depoteslim->depodurum = 0;
                                $depoteslim->tipi = 1;
                                $depoteslim->parabirimi_id=$birim;
                                $depoteslim->parabirimi2_id=$birim2;
                                $depoteslim->save();
                            }
                        }
                        if($geriiadesayi>0){
                            $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                ->where('depodurum',0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',0)->first();
                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                            {
                                $secilenlist = explode(',', $depoteslim->secilenler);
                                $geriiadesayaclar = explode(',', $geriiadesayaclist);
                                $secilenler="";
                                $adet = 0;
                                foreach($geriiadesayaclar as $sayacgelenid){
                                    if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                        $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                        $adet++;
                                    }
                                }
                                $geriiadesayi = $adet;
                                if ($adet>0) {
                                    $depoteslim->secilenler .= ',' . $secilenler;
                                    $depoteslim->sayacsayisi += $adet;
                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                    $depoteslim->save();
                                }
                            } else { //yeni depo teslimatı yapılacak
                                $depoteslim = new DepoTeslim;
                                $depoteslim->servis_id = $servisid;
                                $depoteslim->netsiscari_id = $netsiscariid;
                                $depoteslim->secilenler = $geriiadesayaclist;
                                $depoteslim->sayacsayisi = $geriiadesayi;
                                $depoteslim->depodurum = 0;
                                $depoteslim->tipi = 2;
                                $depoteslim->parabirimi_id=$birim;
                                $depoteslim->parabirimi2_id=$birim2;
                                $depoteslim->save();
                            }
                        }
                        if($periyodiksayi>0){
                            $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                ->where('depodurum',0)->where('tipi',0)->where('periyodik',1)->where('subegonderim',0)->first();
                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                            {
                                $secilenlist = explode(',', $depoteslim->secilenler);
                                $periyodiksayaclar = explode(',', $periyodiklist);
                                $secilenler="";
                                $adet = 0;
                                foreach($periyodiksayaclar as $sayacgelenid){
                                    if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                        $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                        $adet++;
                                    }
                                }
                                $periyodiksayi = $adet;
                                if ($adet>0) {
                                    $depoteslim->secilenler .= ',' . $secilenler;
                                    $depoteslim->sayacsayisi += $adet;
                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                    $depoteslim->save();
                                }
                            } else { //yeni depo teslimatı yapılacak
                                $depoteslim = new DepoTeslim;
                                $depoteslim->servis_id = $servisid;
                                $depoteslim->netsiscari_id = $netsiscariid;
                                $depoteslim->secilenler = $periyodiklist;
                                $depoteslim->sayacsayisi = $periyodiksayi;
                                $depoteslim->depodurum = 0;
                                $depoteslim->periyodik = 1;
                                $depoteslim->parabirimi_id=$birim;
                                $depoteslim->parabirimi2_id=$birim2;
                                $depoteslim->save();
                            }
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi.', 'type' => 'error'));
                    }
                    BackendController::HatirlatmaSil(6, $netsiscariid, $servisid, $sayacsayisi);
                    BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                    if(($kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi)>0)
                        BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi);
                    BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Telefon ile Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                    DB::commit();
                    return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Mail Sonrası Telefon ile Onaylama Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                } else {
                    try {
                        if(count($secilensayaclist)>0){
                            $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                            {
                                $secilenlist = explode(',', $depoteslim->secilenler);
                                $secilenler="";
                                $adet = 0;
                                foreach($secilensayaclist as $sayacgelenid){
                                    if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                        $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                        $adet++;
                                    }
                                }
                                if ($adet>0) {
                                    $depoteslim->secilenler .= ',' . $secilenler;
                                    $depoteslim->sayacsayisi += $adet;
                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                    $depoteslim->save();
                                }
                            } else { //yeni depo teslimatı yapılacak
                                $depoteslim = new DepoTeslim;
                                $depoteslim->servis_id = $servisid;
                                $depoteslim->netsiscari_id = $netsiscariid;
                                $depoteslim->secilenler = $secilensayaclar;
                                $depoteslim->sayacsayisi = count($secilensayaclist);
                                $depoteslim->depodurum = 0;
                                $depoteslim->subegonderim=$subedurum;
                                $depoteslim->parabirimi_id=$birim;
                                $depoteslim->parabirimi2_id=$birim2;
                                $depoteslim->save();
                            }
                        }
                        if(count($garantisayaclist)>0){
                            $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                ->where('depodurum', 0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                            {
                                $secilenlist = explode(',', $depoteslim->secilenler);
                                $secilenler="";
                                $adet = 0;
                                foreach($garantisayaclist as $sayacgelenid){
                                    if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                        $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                        $adet++;
                                    }
                                }
                                if ($adet>0) {
                                    $depoteslim->secilenler .= ',' . $secilenler;
                                    $depoteslim->sayacsayisi += $adet;
                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                    $depoteslim->save();
                                }
                            } else { //yeni depo teslimatı yapılacak
                                $depoteslim = new DepoTeslim;
                                $depoteslim->servis_id = $servisid;
                                $depoteslim->netsiscari_id = $netsiscariid;
                                $depoteslim->secilenler = $garantisayaclar;
                                $depoteslim->sayacsayisi = count($garantisayaclist);
                                $depoteslim->depodurum = 0;
                                $depoteslim->tipi = 1;
                                $depoteslim->subegonderim=$subedurum;
                                $depoteslim->parabirimi_id=$birim;
                                $depoteslim->parabirimi2_id=$birim2;
                                $depoteslim->save();
                            }
                        }
                        if(count($iadesayaclist)>0){
                            $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                ->where('depodurum', 0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                            {
                                $secilenlist = explode(',', $depoteslim->secilenler);
                                $secilenler="";
                                $adet = 0;
                                foreach($iadesayaclist as $sayacgelenid){
                                    if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                        $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                        $adet++;
                                    }
                                }
                                if ($adet>0) {
                                    $depoteslim->secilenler .= ',' . $secilenler;
                                    $depoteslim->sayacsayisi += $adet;
                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                    $depoteslim->save();
                                }
                            } else { //yeni depo teslimatı yapılacak
                                $depoteslim = new DepoTeslim;
                                $depoteslim->servis_id = $servisid;
                                $depoteslim->netsiscari_id = $netsiscariid;
                                $depoteslim->secilenler = $iadesayaclar;
                                $depoteslim->sayacsayisi = count($iadesayaclist);
                                $depoteslim->depodurum = 0;
                                $depoteslim->tipi = 2;
                                $depoteslim->subegonderim=$subedurum;
                                $depoteslim->parabirimi_id=$birim;
                                $depoteslim->parabirimi2_id=$birim2;
                                $depoteslim->save();
                            }
                        }
                        BackendController::HatirlatmaSil(6, $netsiscariid, $servisid, $sayacsayisi);
                        BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $sayacsayisi);
                        BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                        BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Telefon ile Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                        DB::commit();
                        return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Mail Sonrası Telefon ile Onaylama Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi', 'type' => 'error'));
                    }
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylama onaylananlara kaydedilemedi', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getMusterionay($ucretlendirilenid,$secilenler=false){
        $ucretlendirilen=Ucretlendirilen::find($ucretlendirilenid);
        if($secilenler){
            $secilenlist=explode(',',$secilenler);
            foreach ($secilenlist as $secilen){
                $servistakip=ServisTakip::where('arizafiyat_id',$secilen)->first();
                if($servistakip->ucretlendirilen_id==$ucretlendirilenid)
                {
                    break;
                }else{
                    $ucretlendirilen=Ucretlendirilen::find($servistakip->ucretlendirilen_id);
                    $ucretlendirilenid=$servistakip->ucretlendirilen_id;
                    break;
                }
            }
        }
        if($ucretlendirilen){
            $ucretlendirilen->netsiscari=NetsisCari::find($ucretlendirilen->netsiscari_id);
            $ucretlendirilen->uretimyer=UretimYer::find($ucretlendirilen->uretimyer_id);
            if($ucretlendirilen->durum==2) //onaylanmışsa
                return View::make('ucretlendirme.musterionay',array('ucretlendirilenid'=>$ucretlendirilenid,'ucretlendirilen'=>$ucretlendirilen,'durum'=>1))->with(array('title'=>'Ucretlendirme Onay Ekranı'));
            else if($ucretlendirilen->durum==3) //reddedilmişse
                return View::make('ucretlendirme.musterionay',array('ucretlendirilenid'=>$ucretlendirilenid,'ucretlendirilen'=>$ucretlendirilen,'durum'=>0))->with(array('title'=>'Ucretlendirme Onay Ekranı'));
            else if($ucretlendirilen->durum==0) //beklemeye geri alındıysa
                return View::make('ucretlendirme.musterionay',array('ucretlendirilenid'=>$ucretlendirilenid,'ucretlendirilen'=>$ucretlendirilen,'durum'=>2))->with(array('title'=>'Ucretlendirme Onay Ekranı'));
            else
                return View::make('ucretlendirme.musterionay',array('ucretlendirilenid'=>$ucretlendirilenid,'ucretlendirilen'=>$ucretlendirilen))->with(array('title'=>'Ucretlendirme Onay Ekranı'));
        }else{
            return View::make('ucretlendirme.musterionay',array('ucretlendirilenid'=>$ucretlendirilenid,'ucretlendirilen'=>$ucretlendirilen,'durum'=>-1))->with(array('title'=>'Ucretlendirme Onay Ekranı'));
        }
    }

    public function postMusterionaylist() {
        $ucretlendirilenid=Input::get('ucretlendirilenid');
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            $arizafiyatlist = explode(',',$ucretlendirilen->secilenler);
            $query = ArizaFiyat::whereIn('arizafiyat.id',$arizafiyatlist)->where(function($query){ $query->where('arizafiyat.durum',1)->orWhere('arizafiyat.durum',3);})
                ->select(array("arizafiyat.id","arizafiyat.ariza_serino","sayacadi.sayacadi","arizafiyat.ggaranti",
                    "arizafiyat.toplamtutar","arizafiyat.toplamtutar2","parabirimi.birimi","arizafiyat.parabirimi_id","parabirimi2.birimi2","arizafiyat.parabirimi2_id","arizafiyat.durum"))
                ->leftjoin("sayacadi", "arizafiyat.sayacadi_id", "=", "sayacadi.id")
                ->leftjoin("parabirimi", "arizafiyat.parabirimi_id", "=", "parabirimi.id")
                ->leftjoin("parabirimi2", "arizafiyat.parabirimi2_id", "=", "parabirimi2.id");

        return Datatables::of($query)
            ->editColumn('toplamtutar', function ($model) {
                if($model->ggaranti=='İçinde')
                    return "0.00 ".$model->birimi;
                else
                    if($model->toplamtutar2==0){
                        if($model->toplamtutar==0)
                            return "0.00 ".$model->birimi;
                        else
                            return $model->toplamtutar." ".$model->birimi;
                    }else{
                        if($model->toplamtutar==0)
                            return "0.00 ".$model->birimi." + ".$model->toplamtutar2." ".$model->birimi2;
                        else
                            return $model->toplamtutar." ".$model->birimi." + ".$model->toplamtutar2." ".$model->birimi2;
                    }
            })->make(true);
    }

    public function postMusterionay(){
        $isim = "";
        try {
            DB::beginTransaction();
            $ucretlendirilenid = Input::get('ucretlendirilenid');
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            if($ucretlendirilen->durum!=1){
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylama Tamamlanamadı!', 'text' => 'Ücretlendirme Zaten Onaylanmış ya da Reddedilmiş Olabilir!', 'type' => 'warning'));
            }
            $servisid = $ucretlendirilen->servis_id;
            $uretimyerid = $ucretlendirilen->uretimyer_id;
            $uretimyer = UretimYer::find($uretimyerid);
            $netsiscariid = $ucretlendirilen->netsiscari_id;
            $secilenler = $ucretlendirilen->secilenler;
            $secilenlist = explode(',', $secilenler);
            $sayacsayisi = count($secilenlist);
            $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenlist)->get();
            $netsiscari = NetsisCari::find($netsiscariid);
            $yetkili = Yetkili::find($ucretlendirilen->yetkili_id);
            $kullanici=Kullanici::find($yetkili->kullanici_id);
            $birimyonetici = Kullanici::where('grup_id',5)->where('aktifdurum',1)->first();
            $servisyetkili = Kullanici::where('id',$ucretlendirilen->kullanici_id)->first();
            $mailcc = null;
            if($birimyonetici){
                if($servisyetkili){
                    if($birimyonetici->id!=$servisyetkili->id){
                        $mailcc = $birimyonetici->email;
                    }
                }else{
                    $servisyetkili = $birimyonetici;
                }
            }
            $onaylanan = new Onaylanan;
            $servistakipler = ServisTakip::whereIn('arizafiyat_id', $secilenlist)->get();
            $sube = Sube::where('netsiscari_id', $netsiscariid)->where('aktif', 1)->first();
            $subedurum = 0;
            if($sube){
                foreach ($servistakipler as $servistakip){
                    if($servistakip->subedurum)
                        $subedurum = 1;
                }
            }
            If (Input::hasFile('eklenendosya')) {
                $dosya = Input::file('eklenendosya');
                $uzanti = $dosya->getClientOriginalExtension();
                $isim = Str::slug($uretimyer->yeradi) . '_' . Str::slug(str_random(5)) . '.' . $uzanti;
                $dosya->move('assets/onayformu/', $isim);
                $onaylanan->onayformu = $isim;
            }

            $ucretlendirilen->durum = 2;
            $ucretlendirilen->onaytarihi = date('Y-m-d H:i:s');
            $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
            $ucretlendirilen->save();
            $birim = $ucretlendirilen->parabirimi_id;
            $birim2 = $ucretlendirilen->parabirimi2_id;
            $onaylanan->servis_id = $servisid;
            $onaylanan->uretimyer_id = $uretimyerid;
            $onaylanan->netsiscari_id = $netsiscariid;
            $onaylanan->ucretlendirilen_id = $ucretlendirilen->id;
            $onaylanan->yetkili_id = $yetkili->id;
            $onaylanan->onaytarihi = date('Y-m-d H:i:s');
            $onaylanan->onaylamatipi = 2;
            $onaylanan->userip = BackendController::getRealIP();
            $onaylanan->save();
            $secilensayaclar = "";
            $secilensayaclist = array();
            $garantisayaclar = "";
            $garantisayaclist = array();
            $iadesayaclar = "";
            $iadesayaclist = array();
            try {
                foreach ($arizafiyatlar as $arizafiyat) {
                    $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                    $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                    if($arizakayit->arizakayit_durum==7){
                        array_push($iadesayaclist, $sayacgelen->id);
                        $iadesayaclar .= ($iadesayaclar == "" ? "" : ",") . $sayacgelen->id;
                    }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0) {
                        array_push($secilensayaclist, $sayacgelen->id);
                        $secilensayaclar .= ($secilensayaclar == "" ? "" : ",") . $sayacgelen->id;
                    }else{
                        array_push($garantisayaclist, $sayacgelen->id);
                        $garantisayaclar .= ($garantisayaclar == "" ? "" : ",") . $sayacgelen->id;
                    }
                    $sayacgelen->musterionay = 1;
                    $sayacgelen->teslimdurum = 1;
                    $sayacgelen->save();
                    $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                    $servistakip->onaylanan_id = $onaylanan->id;
                    $servistakip->durum = 5;
                    $servistakip->onaylanmatarihi = date('Y-m-d H:i:s');
                    $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                    $servistakip->save();
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Sayaç Gelen ve Servis Takip Bilgileri Kaydedilirken Hata ile Karşılaşıldı.', 'type' => 'error'));
            }
            if ($servisid == 5) //kalibrasyon tamamlanmışsa depo teslim yoksa bekleyecek
            {
                try {
                    $kalibrasyonsayi=0;
                    $kalibrasyonsayaclist="";
                    $garantisayi=0;
                    $garantisayaclist="";
                    $geriiadesayi=0;
                    $geriiadesayaclist="";
                    $periyodiksayi=0;
                    $periyodiklist="";
                    foreach ($arizafiyatlar as $arizafiyat) {
                        $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                        $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                        $depogelen=DepoGelen::find($sayacgelen->depogelen_id);
                        if($arizakayit->arizakayit_durum != 3) { //yedek parça bekliyorsa depo teslim olmaz
                            if ($depogelen->periyodik) {
                                if ($sayacgelen->kalibrasyon) {
                                    $periyodiksayi++;
                                    $periyodiklist .= ($periyodiklist == "" ? "" : ",") . $sayacgelen->id;
                                } else {
                                    $sayacgelen->teslimdurum = 0;
                                    $sayacgelen->save();
                                }
                            } else {
                                if ($arizakayit->arizakayit_durum == 7) {
                                    $geriiadesayi++;
                                    $geriiadesayaclist .= ($geriiadesayaclist == "" ? "" : ",") . $sayacgelen->id;
                                } else if ($sayacgelen->kalibrasyon) {
                                    if ($arizafiyat->toplamtutar > 0 || $arizafiyat->toplamtutar2 > 0) {
                                        $kalibrasyonsayi++;
                                        $kalibrasyonsayaclist .= ($kalibrasyonsayaclist == "" ? "" : ",") . $sayacgelen->id;
                                    } else {
                                        $garantisayi++;
                                        $garantisayaclist .= ($garantisayaclist == "" ? "" : ",") . $sayacgelen->id;
                                    }
                                } else {
                                    $sayacgelen->teslimdurum = 0;
                                    $sayacgelen->save();
                                }
                            }
                        }else{
                            $sayacgelen->teslimdurum = 0;
                            $sayacgelen->save();
                        }
                    }
                    if($kalibrasyonsayi>0){
                        $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                            ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',0)->first();
                        if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                        {
                            $secilenlist = explode(',', $depoteslim->secilenler);
                            $kalibrasyonsayaclar = explode(',', $kalibrasyonsayaclist);
                            $secilenler="";
                            $adet = 0;
                            foreach($kalibrasyonsayaclar as $sayacgelenid){
                                if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                    $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                    $adet++;
                                }
                            }
                            $kalibrasyonsayi = $adet;
                            if ($adet>0) {
                                $depoteslim->secilenler .= ',' . $secilenler;
                                $depoteslim->sayacsayisi += $adet;
                                $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                $depoteslim->save();
                            }
                        } else { //yeni depo teslimatı yapılacak
                            $depoteslim = new DepoTeslim;
                            $depoteslim->servis_id = $servisid;
                            $depoteslim->netsiscari_id = $netsiscariid;
                            $depoteslim->secilenler = $kalibrasyonsayaclist;
                            $depoteslim->sayacsayisi = $kalibrasyonsayi;
                            $depoteslim->depodurum = 0;
                            $depoteslim->parabirimi_id=$birim;
                            $depoteslim->parabirimi2_id=$birim2;
                            $depoteslim->save();
                        }
                    }
                    if($garantisayi>0){
                        $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                            ->where('depodurum',0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',0)->first();
                        if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                        {
                            $secilenlist = explode(',', $depoteslim->secilenler);
                            $garantisayaclar = explode(',', $garantisayaclist);
                            $secilenler="";
                            $adet = 0;
                            foreach($garantisayaclar as $sayacgelenid){
                                if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                    $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                    $adet++;
                                }
                            }
                            $garantisayi = $adet;
                            if ($adet>0) {
                                $depoteslim->secilenler .= ',' . $secilenler;
                                $depoteslim->sayacsayisi += $adet;
                                $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                $depoteslim->save();
                            }
                        } else { //yeni depo teslimatı yapılacak
                            $depoteslim = new DepoTeslim;
                            $depoteslim->servis_id = $servisid;
                            $depoteslim->netsiscari_id = $netsiscariid;
                            $depoteslim->secilenler = $garantisayaclist;
                            $depoteslim->sayacsayisi = $garantisayi;
                            $depoteslim->depodurum = 0;
                            $depoteslim->tipi = 1;
                            $depoteslim->parabirimi_id=$birim;
                            $depoteslim->parabirimi2_id=$birim2;
                            $depoteslim->save();
                        }
                    }
                    if($geriiadesayi>0){
                        $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                            ->where('depodurum',0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',0)->first();
                        if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                        {
                            $secilenlist = explode(',', $depoteslim->secilenler);
                            $geriiadesayaclar = explode(',', $geriiadesayaclist);
                            $secilenler="";
                            $adet = 0;
                            foreach($geriiadesayaclar as $sayacgelenid){
                                if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                    $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                    $adet++;
                                }
                            }
                            $geriiadesayi = $adet;
                            if ($adet>0) {
                                $depoteslim->secilenler .= ',' . $secilenler;
                                $depoteslim->sayacsayisi += $adet;
                                $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                $depoteslim->save();
                            }
                        } else { //yeni depo teslimatı yapılacak
                            $depoteslim = new DepoTeslim;
                            $depoteslim->servis_id = $servisid;
                            $depoteslim->netsiscari_id = $netsiscariid;
                            $depoteslim->secilenler = $geriiadesayaclist;
                            $depoteslim->sayacsayisi = $geriiadesayi;
                            $depoteslim->depodurum = 0;
                            $depoteslim->tipi = 2;
                            $depoteslim->parabirimi_id=$birim;
                            $depoteslim->parabirimi2_id=$birim2;
                            $depoteslim->save();
                        }
                    }
                    if($periyodiksayi>0){
                        $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                            ->where('depodurum',0)->where('tipi',0)->where('periyodik',1)->where('subegonderim',0)->first();
                        if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                        {
                            $secilenlist = explode(',', $depoteslim->secilenler);
                            $periyodiksayaclar = explode(',', $periyodiklist);
                            $secilenler="";
                            $adet = 0;
                            foreach($periyodiksayaclar as $sayacgelenid){
                                if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                    $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                    $adet++;
                                }
                            }
                            $periyodiksayi = $adet;
                            if ($adet>0) {
                                $depoteslim->secilenler .= ',' . $secilenler;
                                $depoteslim->sayacsayisi += $adet;
                                $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                $depoteslim->save();
                            }
                        } else { //yeni depo teslimatı yapılacak
                            $depoteslim = new DepoTeslim;
                            $depoteslim->servis_id = $servisid;
                            $depoteslim->netsiscari_id = $netsiscariid;
                            $depoteslim->secilenler = $periyodiklist;
                            $depoteslim->sayacsayisi = $periyodiksayi;
                            $depoteslim->depodurum = 0;
                            $depoteslim->periyodik = 1;
                            $depoteslim->parabirimi_id=$birim;
                            $depoteslim->parabirimi2_id=$birim2;
                            $depoteslim->save();
                        }
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi.', 'type' => 'error'));
                }
                BackendController::HatirlatmaGuncelle(6, $netsiscariid, $servisid, $sayacsayisi);
                BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                if(($kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi)>0)
                    BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi);
                BackendController::IslemEkle(1, $kullanici->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Mail ile Onaylandı.', 'Ekleyen:' . $kullanici->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                DB::commit();
                try {
                    $hatirlatma = Hatirlatma::where('hatirlatmatip_id',6)->where('servis_id',$servisid)
                        ->where('netsiscari_id',$netsiscariid)->orderBy('id', 'desc')->first();
                    Mail::send('mail.musterionay', array('ucretlendirilen' => $ucretlendirilen,'netsiscari'=>$netsiscari,'hatirlatma'=>$hatirlatma),
                        function ($message) use ($servisyetkili, $ucretlendirilen,$mailcc,$netsiscari) {
                            if(!is_null($mailcc))
                                $message->cc($mailcc);
                            $message->to($servisyetkili->email, $servisyetkili->adi_soyadi)->subject('Servis Fiyatlandirma Müşteri Onayı / '.$netsiscari->cariadi);
                        });
                } catch (Exception $e) {
                    Log::error($e);
                }
                return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Mail ile Onaylama Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
            } else {
                try {
                    if(count($secilensayaclist)>0){
                        $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                            ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                        if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                        {
                            $secilenlist = explode(',', $depoteslim->secilenler);
                            $secilenler="";
                            $adet = 0;
                            foreach($secilensayaclist as $sayacgelenid){
                                if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                    $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                    $adet++;
                                }
                            }
                            if ($adet>0) {
                                $depoteslim->secilenler .= ',' . $secilenler;
                                $depoteslim->sayacsayisi += $adet;
                                $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                $depoteslim->save();
                            }
                        } else { //yeni depo teslimatı yapılacak
                            $depoteslim = new DepoTeslim;
                            $depoteslim->servis_id = $servisid;
                            $depoteslim->netsiscari_id = $netsiscariid;
                            $depoteslim->secilenler = $secilensayaclar;
                            $depoteslim->sayacsayisi = count($secilensayaclist);
                            $depoteslim->depodurum = 0;
                            $depoteslim->subegonderim=$subedurum;
                            $depoteslim->parabirimi_id=$birim;
                            $depoteslim->parabirimi2_id=$birim2;
                            $depoteslim->save();
                        }
                    }
                    if(count($garantisayaclist)>0){
                        $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                            ->where('depodurum', 0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                        if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                        {
                            $secilenlist = explode(',', $depoteslim->secilenler);
                            $secilenler="";
                            $adet = 0;
                            foreach($garantisayaclist as $sayacgelenid){
                                if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                    $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                    $adet++;
                                }
                            }
                            if ($adet>0) {
                                $depoteslim->secilenler .= ',' . $secilenler;
                                $depoteslim->sayacsayisi += $adet;
                                $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                $depoteslim->save();
                            }
                        } else { //yeni depo teslimatı yapılacak
                            $depoteslim = new DepoTeslim;
                            $depoteslim->servis_id = $servisid;
                            $depoteslim->netsiscari_id = $netsiscariid;
                            $depoteslim->secilenler = $garantisayaclar;
                            $depoteslim->sayacsayisi = count($garantisayaclist);
                            $depoteslim->depodurum = 0;
                            $depoteslim->tipi = 1;
                            $depoteslim->subegonderim=$subedurum;
                            $depoteslim->parabirimi_id=$birim;
                            $depoteslim->parabirimi2_id=$birim2;
                            $depoteslim->save();
                        }
                    }
                    if(count($iadesayaclist)>0){
                        $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                            ->where('depodurum', 0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                        if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                        {
                            $secilenlist = explode(',', $depoteslim->secilenler);
                            $secilenler="";
                            $adet = 0;
                            foreach($iadesayaclist as $sayacgelenid){
                                if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                    $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                    $adet++;
                                }
                            }
                            if ($adet>0) {
                                $depoteslim->secilenler .= ',' . $secilenler;
                                $depoteslim->sayacsayisi += $adet;
                                $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                $depoteslim->save();
                            }
                        } else { //yeni depo teslimatı yapılacak
                            $depoteslim = new DepoTeslim;
                            $depoteslim->servis_id = $servisid;
                            $depoteslim->netsiscari_id = $netsiscariid;
                            $depoteslim->secilenler = $iadesayaclar;
                            $depoteslim->sayacsayisi = count($iadesayaclist);
                            $depoteslim->depodurum = 0;
                            $depoteslim->tipi = 2;
                            $depoteslim->subegonderim=$subedurum;
                            $depoteslim->parabirimi_id=$birim;
                            $depoteslim->parabirimi2_id=$birim2;
                            $depoteslim->save();
                        }
                    }
                    BackendController::HatirlatmaGuncelle(6, $netsiscariid, $servisid, $sayacsayisi);
                    BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $sayacsayisi);
                    BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                    BackendController::IslemEkle(1, $kullanici->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Mail ile Onaylandı.', 'Ekleyen:' . $kullanici->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                    DB::commit();
                    try {
                        $hatirlatma = Hatirlatma::where('hatirlatmatip_id',6)->where('servis_id',$servisid)
                            ->where('netsiscari_id',$netsiscariid)->orderBy('id', 'desc')->first();
                        Mail::send('mail.musterionay', array('ucretlendirilen' => $ucretlendirilen,'netsiscari'=>$netsiscari,'hatirlatma'=>$hatirlatma),
                            function ($message) use ($servisyetkili, $ucretlendirilen,$mailcc,$netsiscari) {
                                if(!is_null($mailcc))
                                    $message->cc($mailcc);
                                $message->to($servisyetkili->email, $servisyetkili->adi_soyadi)->subject('Servis Fiyatlandirma Müşteri Onayı / '.$netsiscari->cariadi);
                            });
                    } catch (Exception $e) {
                        Log::error($e);
                    }
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fiyatlandırma Onaylandı', 'text' => 'Sayaçların Fiyatlandırması Onaylandı.Firma ile irtibata geçebilirsiniz.', 'type' => 'success', 'durum' => 1));
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi', 'type' => 'error'));
                }
            }
        } catch (Exception $e) {
            if(File::exists('assets/onayformu/'.$isim.''))
                File::delete('assets/onayformu/'.$isim.'');
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylama onaylananlara kaydedilemedi', 'type' => 'error'));
        }
    }

    public function postMusterireddet()
    {
        try {
            DB::beginTransaction();
            $aciklama = Input::get('aciklama');
            $ucretlendirilenid = Input::get('ucretlendirilenid');
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            if($ucretlendirilen->durum!=1){
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylama Tamamlanamadı!', 'text' => 'Ücretlendirme Zaten Onaylanmış ya da Reddedilmiş Olabilir!', 'type' => 'warning'));
            }
            $servisid = $ucretlendirilen->servis_id;
            $netsiscariid = $ucretlendirilen->netsiscari_id;
            $yetkili = Yetkili::where('netsiscari_id', $netsiscariid)->first();
            $kullanici=Kullanici::find($yetkili->kullanici_id);
            $secilenler = $ucretlendirilen->secilenler;
            $secilenlist = explode(',', $secilenler);
            $sayacsayisi = count($secilenlist);
            $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenlist)->get();
            $netsiscari = NetsisCari::find($netsiscariid);
            $birimyonetici = Kullanici::where('grup_id',5)->where('aktifdurum',1)->first();
            $servisyetkili = Kullanici::where('id',$ucretlendirilen->kullanici_id)->first();
            $mailcc = null;
            if($birimyonetici){
                if($servisyetkili){
                    if($birimyonetici->id!=$servisyetkili->id){
                        $mailcc = $birimyonetici->email;
                    }
                }else{
                    $servisyetkili = $birimyonetici;
                }
            }
            $ucretlendirilen->durum = 3;
            $ucretlendirilen->reddetmetarihi = date('Y-m-d H:i:s');
            $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
            $ucretlendirilen->tekrarkayittarihi = NULL;
            $ucretlendirilen->gerigonderimtarihi = NULL;
            $ucretlendirilen->musterinotu = $aciklama;
            $ucretlendirilen->reddedilenler = $secilenler;
            $ucretlendirilen->save();
            try {
                foreach ($arizafiyatlar as $arizafiyat) {
                    $arizafiyateski=new ArizaFiyatEski;
                    $arizafiyateski->arizafiyat_id=$arizafiyat->id;
                    $arizafiyateski->ariza_serino=$arizafiyat->ariza_serino;
                    $arizafiyateski->sayac_id=$arizafiyat->sayac_id;
                    $arizafiyateski->sayacadi_id=$arizafiyat->sayacadi_id;
                    $arizafiyateski->sayaccap_id=$arizafiyat->sayaccap_id;
                    $arizafiyateski->ariza_garanti=$arizafiyat->ariza_garanti;
                    $arizafiyateski->fiyatdurum=$arizafiyat->fiyatdurum;
                    $arizafiyateski->uretimyer_id=$arizafiyat->uretimyer_id;
                    $arizafiyateski->arizakayit_id=$arizafiyat->arizakayit_id;
                    $arizafiyateski->depogelen_id=$arizafiyat->depogelen_id;
                    $arizafiyateski->sayacgelen_id=$arizafiyat->sayacgelen_id;
                    $arizafiyateski->netsiscari_id=$arizafiyat->netsiscari_id;
                    $arizafiyateski->degisenler=$arizafiyat->degisenler;
                    $arizafiyateski->genel=$arizafiyat->genel;
                    $arizafiyateski->ozel=$arizafiyat->ozel;
                    $arizafiyateski->ucretsiz=$arizafiyat->ucretsiz;
                    $arizafiyateski->fiyat=$arizafiyat->fiyat;
                    $arizafiyateski->indirim=$arizafiyat->indirim;
                    $arizafiyateski->indirimorani=$arizafiyat->indirimorani;
                    $arizafiyateski->tutar=$arizafiyat->tutar;
                    $arizafiyateski->kdv=$arizafiyat->kdv;
                    $arizafiyateski->toplamtutar=$arizafiyat->toplamtutar;
                    $arizafiyateski->parabirimi_id=$arizafiyat->parabirimi_id;
                    $arizafiyateski->durum=$arizafiyat->durum;
                    $arizafiyateski->kullanici_id=$arizafiyat->kullanici_id;
                    $arizafiyateski->kayittarihi=$arizafiyat->kayittarihi;
                    $arizafiyateski->kurtarihi=$arizafiyat->kurtarihi;
                    $arizafiyateski->tekrarkayittarihi=$arizafiyat->tekrarkayittarihi;
                    $arizafiyateski->save();
                    $arizafiyat->durum = 2;
                    $arizafiyat->save();
                    $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                    $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                    $sayacgelen->fiyatlandirma = 0;
                    $sayacgelen->save();
                    $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                    $servistakip->durum = 6;
                    $servistakip->reddetmetarihi = date('Y-m-d H:i:s');
                    $servistakip->save();
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Reddetme Hatası', 'text' => 'Reddedilen Ücretlendirmeye ait Fiyatlar Güncellenemedi', 'type' => 'error'));
            }
            BackendController::HatirlatmaGuncelle(6, $netsiscariid, $servisid, $sayacsayisi);
            BackendController::HatirlatmaEkle(7, $netsiscariid, $servisid, $sayacsayisi);
            BackendController::BildirimEkle(6, $netsiscariid, $servisid, $sayacsayisi);
            BackendController::IslemEkle(1, $kullanici->id, 'label-danger', 'fa-minus-square-o', $ucretlendirilen->id . ' Numaralı Ücretlendirme Reddedildi.', 'Ekleyen:' . $kullanici->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
            DB::commit();
            try {
                $hatirlatma = Hatirlatma::where('hatirlatmatip_id',6)->where('servis_id',$servisid)
                    ->where('netsiscari_id',$netsiscariid)->orderBy('id', 'desc')->first();
                Mail::send('mail.musterionay', array('ucretlendirilen' => $ucretlendirilen,'netsiscari'=>$netsiscari,'hatirlatma'=>$hatirlatma),
                    function ($message) use ($servisyetkili, $ucretlendirilen,$mailcc,$netsiscari) {
                        if(!is_null($mailcc))
                            $message->cc($mailcc);
                        $message->to($servisyetkili->email, $servisyetkili->adi_soyadi)->subject('Servis Fiyatlandirma Müşteri Reddi / '.$netsiscari->cariadi);
                    });
            } catch (Exception $e) {
                Log::error($e);
            }
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fiyatlandırma Reddedildi', 'text' => 'Sayaçların Fiyatlandırması Reddedildi.Firma ile irtibata geçebilirsiniz.', 'type' => 'success', 'durum' => 1));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Reddetme Hatası', 'text' => 'Reddetme kaydedilemedi', 'type' => 'error'));
        }
    }

    public function getReddedilenbilgi() {
        try {
            $id=Input::get('id');
            $ucretlendirilen = Ucretlendirilen::find($id);
            $ucretlendirilen->uretimyer = Uretimyer::find($ucretlendirilen->uretimyer_id);
            $ucretlendirilen->parabirimi = ParaBirimi::find($ucretlendirilen->parabirimi_id);
            $ucretlendirilen->parabirimi2 = ParaBirimi::find($ucretlendirilen->parabirimi2_id);
            $secilenler = explode(',', $ucretlendirilen->reddedilenler);
            $ucretlendirilen->arizafiyat = ArizaFiyat::whereIn('id', $secilenler)->get();
            $ucretlendirilen->reddetmetarihi = date("d-m-Y", strtotime($ucretlendirilen->reddetmetarihi));
            foreach ($ucretlendirilen->arizafiyat as $arizafiyat) {
                $arizafiyat->sayacadi = SayacAdi::find($arizafiyat->sayacadi_id);
                $arizafiyat->sayaccap = SayacCap::find($arizafiyat->sayaccap_id);
                $arizafiyat->parabirimi = ParaBirimi::find($arizafiyat->parabirimi_id);
                $arizafiyat->parabirimi2 = ParaBirimi::find($arizafiyat->parabirimi2_id);
            }
            $dovizkuru = DovizKuru::where('tarih', $ucretlendirilen->kurtarihi)->take(3)->get();
            foreach ($dovizkuru as $doviz) {
                $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
            }
            return Response::json(array('durum'=>true,'ucretlendirilen' => $ucretlendirilen, 'dovizkuru' => $dovizkuru ));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error','title' => 'Reddedilen Bilgi Hatası' ));
        }
    }

    public function getGeriteslimbilgi(){
        try {
            $id=Input::get('id');
            $ucretlendirilen = Ucretlendirilen::find($id);
            $ucretlendirilen->uretimyer = Uretimyer::find($ucretlendirilen->uretimyer_id);
            $ucretlendirilen->netsiscari = Netsiscari::find($ucretlendirilen->netsiscari_id);
            $ucretlendirilen->parabirimi = ParaBirimi::find($ucretlendirilen->parabirimi_id);
            $ucretlendirilen->parabirimi2 = ParaBirimi::find($ucretlendirilen->parabirimi2_id);
            $ucretlendirilen->servis = Servis::find($ucretlendirilen->servis_id);
            $ucretlendirilen->yetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
            if (!$ucretlendirilen->yetkili)
                return Response::json(array('durum' => false,'text' => 'Kişi bu cari için Ücretlendirme Yapamaz.Yetkili olarak eklendikten sonra tekrar deneyiniz', 'type' => 'error','title' => 'Kullanıcı Yetkili Değil'));
            if ($ucretlendirilen->durum == 0 || $ucretlendirilen->durum == 1)
                $secilenler = explode(',', $ucretlendirilen->secilenler);
            else
                $secilenler = explode(',', $ucretlendirilen->reddedilenler);
            $ucretlendirilen->arizafiyat = ArizaFiyat::whereIn('id', $secilenler)->get();
            foreach ($ucretlendirilen->arizafiyat as $arizafiyat) {
                $arizafiyat->sayacadi = SayacAdi::find($arizafiyat->sayacadi_id);
                $arizafiyat->sayaccap = SayacCap::find($arizafiyat->sayaccap_id);
                $arizafiyat->parabirimi = ParaBirimi::find($arizafiyat->parabirimi_id);
                $arizafiyat->parabirimi2 = ParaBirimi::find($arizafiyat->parabirimi2_id);
                $arizafiyat->dovizkuru = DovizKuru::where('tarih', $arizafiyat->kurtarihi)->orderBy('parabirimi_id', 'asc')->take(3)->get();
                if ($arizafiyat->kurtarihi != null)
                    $arizafiyat->kurtarihi = date('d-m-Y', strtotime($arizafiyat->kurtarihi));
                else
                    $arizafiyat->kurtarihi = "";
                foreach ($arizafiyat->dovizkuru as $doviz) {
                    $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
                }
            }
            $teslimadres = DepoTeslim::selectRaw('count(*) AS sayi, teslimadres')->where('netsiscari_id',$ucretlendirilen->netsiscari_id)->where('depodurum',1)->where('teslimadres','<>','')->groupBy('teslimadres')->orderBy('sayi', 'DESC')->get();
            return Response::json(array('durum' => true, 'ucretlendirilen' => $ucretlendirilen,'teslimadres' => $teslimadres ));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error','title' => 'Geri Teslim Bilgisi Hatalı' ));
        }
    }

    public function postGerigonder($id){
        try {
            if (Input::has('gerifaturavar')) //fatura basılacak
                $fatura = 1;
            else // fatura basılmayacak
                $fatura = 0;
            $ucretlendirilenid = $id;
            $secilenler = Input::get('gerisecilenler');
            $adres = Input::get('redcariadres');
            $belge1 = "GERİ GÖNDERİMDİR. FATURA EDİLMEYECEKTİR.";
            $belge2 = Input::get('redaciklama2');
            DB::beginTransaction();
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            $secilenlist = explode(',', $secilenler);
            $garanti = true;
            $birim = $ucretlendirilen->parabirimi_id;
            $netsiscariid = $ucretlendirilen->netsiscari_id;
            $uretimyerid = $ucretlendirilen->uretimyer_id;
            $servisid = $ucretlendirilen->servis_id;
            $sayacsayisi = count($secilenlist);
            $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenlist)->get();
            $secilensayaclar = "";
            $secilensayaclist = array();
            foreach ($arizafiyatlar as $arizafiyat) {
                $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                array_push($secilensayaclist, $sayacgelen->id);
                $secilensayaclar .= ($secilensayaclar == "" ? "" : ",") . $sayacgelen->id;
                //if ($arizafiyat->durum != 2) {
                //    DB::rollBack();
                //    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaçlar Geri Gönderilemedi!', 'text' => 'Seçilen Sayaçlar için Zaten bir Depo Teslimi Mevcut ya da Sayaçlar Tekrar Ücretlendirilmiş Olabilir!', 'type' => 'warning'));
                //}
            }
            $yetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
            $netsiscari = NetsisCari::find($netsiscariid);
            if ($netsiscari->caridurum != "A") {
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
            }
            $servis = Servis::find($servisid);
            $depoteslim = new DepoTeslim;
            $depoteslim->sayacsayisi = count($secilensayaclist);
            $depoteslim->servis_id = $servisid;
            $depoteslim->netsiscari_id = $netsiscariid;
            $depoteslim->secilenler = $secilensayaclar;
            $depoteslim->depodurum = 1;
            $depoteslim->tipi = 2;
            $depoteslim->kullanici_id = Auth::user()->id;
            $depoteslim->parabirimi_id = $birim;
            $depoteslim->teslimtarihi = date('Y-m-d H:i:s');
            $depoteslim->carikod = $netsiscari->carikod;
            $depoteslim->ozelkod = $yetkili->ozelkod;
            $depoteslim->plasiyerkod = $yetkili->plasiyerkod;
            $depoteslim->faturaadres = $netsiscari->adres . ' ' . $netsiscari->il . ' ' . $netsiscari->ilce;
            $depoteslim->teslimadres = $adres;
            $depoteslim->depokodu = $yetkili->depokodu;
            $depoteslim->aciklama = $servis->servisadi;
            $depoteslim->belge1 = $belge1;
            $depoteslim->belge2 = $belge2;
            $depoteslim->netsiskullanici = $yetkili->netsiskullanici;
            $depoteslim->save();
            $reddedilenler = $ucretlendirilen->reddedilenler;
            $reddedilenlist = explode(',', $reddedilenler);
            $yenireddedilenler = "";
            foreach ($reddedilenlist as $reddedilen) {
                $flag = 0;
                foreach ($secilenlist as $secilen) {
                    if ($reddedilen == $secilen) {
                        $flag = 1;
                        break;
                    }
                }
                if ($flag == 0) //reddedilen geri gonderilmemiş
                {
                    $yenireddedilenler .= ($yenireddedilenler == "" ? "" : ",") . $reddedilen;
                }
            }
            if ($yenireddedilenler == "") //reddedilenlerin tamamı geri gonderilmiş
            {
                $ucretlendirilen->gerigonderimtarihi = date('Y-m-d H:i:s');
                $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
            }
            $ucretlendirilen->reddedilenler = $yenireddedilenler;
            $ucretlendirilen->save();
            try {
                $gerigonderim = new Ucretlendirilen;
                $gerigonderim->secilenler = $secilenler;
                $gerigonderim->durum = 4;
                $gerigonderim->sayacsayisi = $sayacsayisi;
                $gerigonderim->uretimyer_id = $uretimyerid;
                $gerigonderim->servis_id = $servisid;
                $gerigonderim->netsiscari_id = $netsiscariid;
                $gerigonderim->garanti = $garanti;
                $gerigonderim->fiyat = 0;
                $gerigonderim->parabirimi_id = $birim;
                $gerigonderim->kullanici_id = Auth::user()->id;
                $gerigonderim->kayittarihi = date('Y-m-d H:i:s');
                $gerigonderim->kurtarihi = $ucretlendirilen->kurtarihi;
                $gerigonderim->durumtarihi = date('Y-m-d H:i:s');
                $gerigonderim->save();
                foreach ($arizafiyatlar as $arizafiyat) {
                    $degisenler = explode(',', $arizafiyat->degisenler);
                    $stokdurumlari = StokDurum::whereIn('degisenler_id', $degisenler)->get();
                    foreach ($stokdurumlari as $stokdurum) {
                        $stokdurum->kullanilan -= ($stokdurum->adet);
                        $stokdurum->save();
                    }
                    $arizafiyat->ariza_garanti = 1;
                    $arizafiyat->fiyat = 0;
                    $arizafiyat->fiyat2 = 0;
                    $arizafiyat->indirim = 0;
                    $arizafiyat->indirimorani = 0;
                    $arizafiyat->tutar = 0;
                    $arizafiyat->tutar2 = 0;
                    $arizafiyat->kdv = 0;
                    $arizafiyat->kdv2 = 0;
                    $arizafiyat->toplamtutar = 0;
                    $arizafiyat->toplamtutar2 = 0;
                    $arizafiyat->parabirimi2_id = null;
                    $arizafiyat->gerigonderimtarihi = date('Y-m-d H:i:s');
                    $arizafiyat->durum = 4;
                    $arizafiyat->save();
                    $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                    $sayacgelen->depoteslim = 1;
                    $sayacgelen->teslimdurum = 2;
                    $sayacgelen->beyanname = -1;
                    $sayacgelen->save();
                    $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                    $servistakip->ucretlendirilen_id = $gerigonderim->id;
                    $servistakip->depoteslim_id = $depoteslim->id;
                    $servistakip->durum = 10;
                    $servistakip->gerigonderimtarihi = $depoteslim->teslimtarihi;
                    $servistakip->kullanici_id = Auth::user()->id;
                    $servistakip->sonislemtarihi = $depoteslim->teslimtarihi;
                    $servistakip->save();
                    $sayac = Sayac::find($arizafiyat->sayac_id);
                    $sayac->songelistarihi = $sayacgelen->depotarihi;
                    $sayac->save();
                    if (!$sayacgelen->kalibrasyon) { //kalibrasyonu yapılmadıysa sil
                        try {
                            $kalibrasyon = Kalibrasyon::where('sayacgelen_id', $sayacgelen->id)->where('durum', 0)->first();
                            if ($kalibrasyon) {
                                $grup = KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
                                if (!$grup->kalibrasyondurum) {
                                    $grup->adet -= 1;
                                    if ($grup->adet == $grup->biten)
                                        $grup->kalibrasyondurum = 1;
                                    $grup->save();
                                }
                                $servistakip->kalibrasyon_id = NULL;
                                $servistakip->save();
                                $kalibrasyon->delete();
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Geri Gönderim Hatası', 'text' => 'Kalibrasyon Bilgisi Silinirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                        }
                        BackendController::HatirlatmaSil(8, $netsiscari->id, $sayacgelen->servis_id, 1);
                    }
                }
                if ($fatura == 0) //fatura basılmayacaksa
                {
                    $durum = 0;
                } else {
                    $durum = BackendController::NetsisFatura($depoteslim->id, 1);
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı yapılamadı', 'text' => 'Geri Gönderim Bilgisi Kaydedilirken Hata Oluştu', 'type' => 'error'));
            }

            if ($durum == 0 || $durum == 1) {
                BackendController::HatirlatmaGeriAl(7, $netsiscariid, $servisid, $sayacsayisi);
                BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $sayacsayisi);
                BackendController::HatirlatmaGuncelle(9, $netsiscariid, $servisid, $sayacsayisi);
                BackendController::BildirimEkle(9, $netsiscariid, $servisid, $sayacsayisi);
                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-truck', $depoteslim->id . ' Numaralı Depo Teslimi ile Sayaçlar İşlem Yapılmadan Geri Gönderildi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Depo Teslim Numarası:' . $depoteslim->id);
                DB::commit();
                if ($durum == 0)
                    return Redirect::to('depo/depoteslim')->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Başarıyla Yapıldı', 'text' => 'Faturası kesilmeden gönderildi', 'type' => 'success'));
                else
                    return Redirect::to('depo/depoteslim')->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Başarıyla Yapıldı', 'text' => 'Faturası kesilerek depo teslimatı gerçekleşti', 'type' => 'success'));
            } else {
                DB::rollBack();
                if ($durum == -1) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Netsise kaydedilemedi', 'type' => 'error'));
                } else if ($durum == 2) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Açıklaması Kaydedilemedi', 'type' => 'error'));
                } else if ($durum == 3) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Genel Bilgileri Kaydedilemedi', 'type' => 'error'));
                } else if ($durum == 4) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Kalemleri Kaydedilemedi', 'type' => 'error'));
                } else if ($durum == 5) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Bugünün Döviz Kuru Netsisten alınamadı', 'type' => 'error'));
                } else if ($durum == 6) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Bu Fatura Numarası Sistemde Kayıtlı', 'type' => 'error'));
                } else {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Kalemleri Kaydedilemedi', 'type' => 'error'));
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı yapılamadı', 'text' => 'Depo Teslimatı yapılırken hata oluştu', 'type' => 'error'));
        }
    }

    public function getGarantiteslimbilgi(){
        try {
            $id=Input::get('id');
            $ucretlendirilen = Ucretlendirilen::find($id);
            $ucretlendirilen->uretimyer = Uretimyer::find($ucretlendirilen->uretimyer_id);
            $ucretlendirilen->netsiscari = Netsiscari::find($ucretlendirilen->netsiscari_id);
            $ucretlendirilen->parabirimi = ParaBirimi::find($ucretlendirilen->parabirimi_id);
            $ucretlendirilen->parabirimi2 = ParaBirimi::find($ucretlendirilen->parabirimi2_id);
            $ucretlendirilen->servis = Servis::find($ucretlendirilen->servis_id);
            $ucretlendirilen->yetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
            if (!$ucretlendirilen->yetkili)
                return Response::json(array('durum' => false,'text' => 'Kişi bu cari için Ücretlendirme Yapamaz.Yetkili olarak eklendikten sonra tekrar deneyiniz', 'type' => 'error','title' => 'Kullanıcı Yetkili Değil'));
            $secilenler = explode(',', $ucretlendirilen->reddedilenler);
            $ucretlendirilen->arizafiyat = ArizaFiyat::whereIn('id', $secilenler)->get();
            foreach ($ucretlendirilen->arizafiyat as $arizafiyat) {
                $arizafiyat->sayacadi = SayacAdi::find($arizafiyat->sayacadi_id);
                $arizafiyat->sayaccap = SayacCap::find($arizafiyat->sayaccap_id);
                $arizafiyat->parabirimi = ParaBirimi::find($arizafiyat->parabirimi_id);
                $arizafiyat->parabirimi2 = ParaBirimi::find($arizafiyat->parabirimi2_id);
                $arizafiyat->dovizkuru = DovizKuru::where('tarih', $arizafiyat->kurtarihi)->orderBy('parabirimi_id', 'asc')->take(3)->get();
                if ($arizafiyat->kurtarihi != null)
                    $arizafiyat->kurtarihi = date('d-m-Y', strtotime($arizafiyat->kurtarihi));
                else
                    $arizafiyat->kurtarihi = "";
                foreach ($arizafiyat->dovizkuru as $doviz) {
                    $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
                }
            }
            return Response::json(array('durum' => true, 'ucretlendirilen' => $ucretlendirilen));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error','title' => 'Garanti Teslim Bilgisi Hatalı' ));
        }
    }

    public function postGarantigonder($id){
        try {
            $ucretlendirilenid = $id;
            $secilenler = Input::get('garantisecilenler');
            DB::beginTransaction();
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            $secilenlist = explode(',', $secilenler);
            $garanti = true;
            $netsiscariid = $ucretlendirilen->netsiscari_id;
            $uretimyerid = $ucretlendirilen->uretimyer_id;
            $servisid = $ucretlendirilen->servis_id;
            $sayacsayisi = count($secilenlist);
            $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenlist)->get();
            $secilensayaclar = "";
            $secilensayaclist = array();
            foreach ($arizafiyatlar as $arizafiyat) {
                $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                array_push($secilensayaclist, $sayacgelen->id);
                $secilensayaclar .= ($secilensayaclar == "" ? "" : ",") . $sayacgelen->id;
                if($arizafiyat->durum!=2){
                    DB::rollBack();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaçlar Garantide Gönderilemedi!', 'text' => 'Seçilen Sayaçlar için Zaten bir Depo Teslimi Mevcut ya da Sayaçlar Tekrar Ücretlendirilmiş Olabilir!', 'type' => 'warning'));
                }
            }
            $netsiscari = NetsisCari::find($netsiscariid);
            if ($netsiscari->caridurum != "A")
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
            $servistakipler = ServisTakip::whereIn('arizafiyat_id', $secilenlist)->get();
            $sube = Sube::where('netsiscari_id', $netsiscariid)->where('aktif', 1)->first();
            $subedurum = 0;
            if($sube){
                foreach ($servistakipler as $servistakip){
                    if($servistakip->subedurum)
                        $subedurum = 1;
                }
            }
            $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                ->where('depodurum',0)->where('tipi', 1)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
            {
                $secilenlist = explode(',', $depoteslim->secilenler);
                $secilenler="";
                $adet = 0;
                foreach($secilensayaclist as $sayacgelenid){
                    if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                        $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                        $adet++;
                    }
                }
                if ($adet>0) {
                    $depoteslim->secilenler .= ',' . $secilenler;
                    $depoteslim->sayacsayisi += $adet;
                    $depoteslim->save();
                }
                $reddedilenler = $ucretlendirilen->reddedilenler;
                $reddedilenlist = explode(',', $reddedilenler);
                $yenireddedilenler = "";
                foreach ($reddedilenlist as $reddedilen) {
                    $flag = 0;
                    foreach ($secilenlist as $secilen) {
                        if ($reddedilen == $secilen) {
                            $flag = 1;
                            break;
                        }
                    }
                    if ($flag == 0) //reddedilen ucretlendirilmemiş
                    {
                        $yenireddedilenler .= ($yenireddedilenler == "" ? "" : ",") . $reddedilen;
                    }
                }

                if ($yenireddedilenler == "") //reddedilenlerin tamamı ucretlendirilmiş
                {
                    $ucretlendirilen->tekrarkayittarihi = date('Y-m-d H:i:s');
                    $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                }
                $ucretlendirilen->reddedilenler = $yenireddedilenler;
                $ucretlendirilen->save();
                $garantigonderim = new Ucretlendirilen;
                $garantigonderim->secilenler = $secilenler;
                $garantigonderim->durum = 2;
                $garantigonderim->sayacsayisi = $sayacsayisi;
                $garantigonderim->uretimyer_id = $uretimyerid;
                $garantigonderim->servis_id = $servisid;
                $garantigonderim->netsiscari_id = $netsiscariid;
                $garantigonderim->garanti = $garanti;
                $garantigonderim->fiyat = 0;
                $garantigonderim->fiyat2 = 0;
                $garantigonderim->onaytipi = 0;
                $garantigonderim->parabirimi_id = $ucretlendirilen->parabirimi_id;
                $garantigonderim->parabirimi2_id = null;
                $garantigonderim->kullanici_id = Auth::user()->id;
                $garantigonderim->kayittarihi = date('Y-m-d H:i:s');
                $garantigonderim->kurtarihi = $ucretlendirilen->kurtarihi;
                $garantigonderim->durumtarihi = date('Y-m-d H:i:s');
                $garantigonderim->save();
                $garantionaylanan = new Onaylanan;
                $garantionaylanan->servis_id = $servisid;
                $garantionaylanan->uretimyer_id = $uretimyerid;
                $garantionaylanan->netsiscari_id = $netsiscariid;
                $garantionaylanan->ucretlendirilen_id = $garantigonderim->id;
                $garantionaylanan->yetkili_id = 1;
                $garantionaylanan->onaytarihi = date('Y-m-d H:i:s');
                $garantionaylanan->onaylamatipi = 0;
                $garantionaylanan->save();
                foreach ($arizafiyatlar as $arizafiyat) {
                    $degisenler = explode(',', $arizafiyat->degisenler);
                    $stokdurumlari = StokDurum::whereIn('degisenler_id', $degisenler)->get();
                    foreach ($stokdurumlari as $stokdurum) {
                        $stokdurum->kullanilan -= ($stokdurum->adet);
                        $stokdurum->save();
                    }
                    $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                    $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                    $sayacgelen->musterionay = 1;
                    $sayacgelen->teslimdurum = 1;
                    $sayacgelen->save();
                    $arizafiyat->ariza_garanti = 1;
                    $arizafiyat->fiyat = 0;
                    $arizafiyat->fiyat2 = 0;
                    $arizafiyat->indirim = 0;
                    $arizafiyat->indirimorani = 0;
                    $arizafiyat->tutar = 0;
                    $arizafiyat->tutar2 = 0;
                    $arizafiyat->kdv = 0;
                    $arizafiyat->kdv2 = 0;
                    $arizafiyat->toplamtutar = 0;
                    $arizafiyat->toplamtutar2 = 0;
                    $arizafiyat->parabirimi2_id = null;
                    $arizafiyat->tekrarkayittarihi = date('Y-m-d H:i:s');
                    $arizafiyat->durum = 3;
                    $arizafiyat->save();
                    $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                    $servistakip->ucretlendirilen_id = $garantigonderim->id;
                    $servistakip->onaylanan_id = $garantionaylanan->id;
                    $servistakip->durum = 5;
                    $servistakip->tekrarucrettarihi = date('Y-m-d H:i:s');
                    $servistakip->onaylanmatarihi = date('Y-m-d H:i:s');
                    $servistakip->kullanici_id = Auth::user()->id;
                    $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                    $servistakip->save();
                    if(!$sayacgelen->kalibrasyon){ //kalibrasyonu yapılmadıysa sil
                        try {
                            $kalibrasyon = Kalibrasyon::where('sayacgelen_id',$sayacgelen->id)->where('durum',0)->first();
                            if($kalibrasyon){
                                $grup=KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
                                if(!$grup->kalibrasyondurum){
                                    $grup->adet-=1;
                                    if($grup->adet==$grup->biten)
                                        $grup->kalibrasyondurum=1;
                                    $grup->save();
                                }
                                $servistakip->kalibrasyon_id = NULL;
                                $servistakip->save();
                                $kalibrasyon->delete();
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Garanti İÇinde Gönderim Hatası', 'text' => 'Kalibrasyon Bilgisi Silinirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                        }
                        BackendController::HatirlatmaSil(8,$netsiscari->id,$sayacgelen->servis_id,1);
                    }
                }
                BackendController::HatirlatmaGuncelle(7, $netsiscariid, $servisid, $sayacsayisi);
                BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $sayacsayisi);
                BackendController::BildirimEkle(4, $netsiscariid, $servisid, $sayacsayisi);
                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-truck', $garantigonderim->id . ' Numaralı Ücretlendirmedeki Sayaçlar Garanti İçinde Depoya Teslim Edildi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Garanti Gönderim Numarası:' . $garantigonderim->id);
                DB::commit();
                return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Fiyatlandırma Tekrar Yapıldı', 'text' => 'Fiyatlandırma Başarıyla Yapıldı', 'type' => 'success'));
            } else { //yeni depo teslimatı yapılacak
                $depoteslim = new DepoTeslim;
                $depoteslim->servis_id = $servisid;
                $depoteslim->netsiscari_id = $netsiscariid;
                $depoteslim->secilenler = $secilensayaclar;
                $depoteslim->sayacsayisi = count($secilensayaclist);
                $depoteslim->depodurum = 0;
                $depoteslim->tipi = 1;
                $depoteslim->subegonderim=$subedurum;
                $depoteslim->parabirimi_id=$ucretlendirilen->parabirimi_id;
                $depoteslim->save();
                $reddedilenler = $ucretlendirilen->reddedilenler;
                $reddedilenlist = explode(',', $reddedilenler);
                $yenireddedilenler = "";
                foreach ($reddedilenlist as $reddedilen) {
                    $flag = 0;
                    foreach ($secilenlist as $secilen) {
                        if ($reddedilen == $secilen) {
                            $flag = 1;
                            break;
                        }
                    }
                    if ($flag == 0) //reddedilen ucretlendirilmemiş
                    {
                        $yenireddedilenler .= ($yenireddedilenler == "" ? "" : ",") . $reddedilen;
                    }
                }

                if ($yenireddedilenler == "") //reddedilenlerin tamamı ucretlendirilmiş
                {
                    $ucretlendirilen->tekrarkayittarihi = date('Y-m-d H:i:s');
                    $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                }
                $ucretlendirilen->reddedilenler = $yenireddedilenler;
                $ucretlendirilen->save();
                $garantigonderim = new Ucretlendirilen;
                $garantigonderim->secilenler = $secilenler;
                $garantigonderim->durum = 2;
                $garantigonderim->sayacsayisi = $sayacsayisi;
                $garantigonderim->uretimyer_id = $uretimyerid;
                $garantigonderim->servis_id = $servisid;
                $garantigonderim->netsiscari_id = $netsiscariid;
                $garantigonderim->garanti = $garanti;
                $garantigonderim->fiyat = 0;
                $garantigonderim->fiyat2 = 0;
                $garantigonderim->onaytipi = 0;
                $garantigonderim->parabirimi_id = $ucretlendirilen->parabirimi_id;
                $garantigonderim->parabirimi2_id = null;
                $garantigonderim->kullanici_id = Auth::user()->id;
                $garantigonderim->kayittarihi = date('Y-m-d H:i:s');
                $garantigonderim->kurtarihi = $ucretlendirilen->kurtarihi;
                $garantigonderim->durumtarihi = date('Y-m-d H:i:s');
                $garantigonderim->save();
                $garantionaylanan = new Onaylanan;
                $garantionaylanan->servis_id = $servisid;
                $garantionaylanan->uretimyer_id = $uretimyerid;
                $garantionaylanan->netsiscari_id = $netsiscariid;
                $garantionaylanan->ucretlendirilen_id = $garantigonderim->id;
                $garantionaylanan->yetkili_id = 1;
                $garantionaylanan->onaytarihi = date('Y-m-d H:i:s');
                $garantionaylanan->onaylamatipi = 0;
                $garantionaylanan->save();
                foreach ($arizafiyatlar as $arizafiyat) {
                    $degisenler = explode(',', $arizafiyat->degisenler);
                    $stokdurumlari = StokDurum::whereIn('degisenler_id', $degisenler)->get();
                    foreach ($stokdurumlari as $stokdurum) {
                        $stokdurum->kullanilan -= ($stokdurum->adet);
                        $stokdurum->save();
                    }
                    $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                    $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                    $sayacgelen->musterionay = 1;
                    $sayacgelen->teslimdurum = 1;
                    $sayacgelen->save();
                    $arizafiyat->ariza_garanti = 1;
                    $arizafiyat->fiyat = 0;
                    $arizafiyat->fiyat2 = 0;
                    $arizafiyat->indirim = 0;
                    $arizafiyat->indirimorani = 0;
                    $arizafiyat->tutar = 0;
                    $arizafiyat->tutar2 = 0;
                    $arizafiyat->kdv = 0;
                    $arizafiyat->kdv2 = 0;
                    $arizafiyat->toplamtutar = 0;
                    $arizafiyat->toplamtutar2 = 0;
                    $arizafiyat->parabirimi2_id = null;
                    $arizafiyat->tekrarkayittarihi = date('Y-m-d H:i:s');
                    $arizafiyat->durum = 3;
                    $arizafiyat->save();
                    $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                    $servistakip->ucretlendirilen_id = $garantigonderim->id;
                    $servistakip->onaylanan_id = $garantionaylanan->id;
                    $servistakip->durum = 5;
                    $servistakip->tekrarucrettarihi = date('Y-m-d H:i:s');
                    $servistakip->onaylanmatarihi = date('Y-m-d H:i:s');
                    $servistakip->kullanici_id = Auth::user()->id;
                    $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                    $servistakip->save();
                    if(!$sayacgelen->kalibrasyon){ //kalibrasyonu yapılmadıysa sil
                        try {
                            $kalibrasyon = Kalibrasyon::where('sayacgelen_id',$sayacgelen->id)->where('durum',0)->first();
                            if($kalibrasyon){
                                $grup=KalibrasyonGrup::find($kalibrasyon->kalibrasyongrup_id);
                                if(!$grup->kalibrasyondurum){
                                    $grup->adet-=1;
                                    if($grup->adet==$grup->biten)
                                        $grup->kalibrasyondurum=1;
                                    $grup->save();
                                }
                                $servistakip->kalibrasyon_id = NULL;
                                $servistakip->save();
                                $kalibrasyon->delete();
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Garanti İçinde Gönderim Hatası', 'text' => 'Kalibrasyon Bilgisi Silinirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                        }
                        BackendController::HatirlatmaSil(8,$netsiscari->id,$sayacgelen->servis_id,1);
                    }
                }

                BackendController::HatirlatmaGuncelle(7, $netsiscariid, $servisid, $sayacsayisi);
                BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $sayacsayisi);
                BackendController::BildirimEkle(4, $netsiscariid, $servisid, $sayacsayisi);
                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-truck', $garantigonderim->id . ' Numaralı Ücretlendirmedeki Sayaçlar Garanti İçinde Depoya Teslim Edildi.','Ekleyen:'.Auth::user()->adi_soyadi.',Garanti Gönderim Numarası:'.$garantigonderim->id);
                DB::commit();
                return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Fiyatlandırma Tekrar Yapıldı', 'text' => 'Fiyatlandırma Başarıyla Yapıldı', 'type' => 'success'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslim Hatası', 'text' => 'Seçilen Sayaçlar depo teslimatına kaydedilemedi', 'type' => 'error'));
        }
    }

    public function getOnaylananlar($hatirlatma_id=false) {
        if($hatirlatma_id)
            return View::make('ucretlendirme.onaylananlar',array('hatirlatma_id'=>$hatirlatma_id))->with(array('title'=>'Onaylananlar Ekranı'));
        else
            return View::make('ucretlendirme.onaylananlar')->with(array('title'=>'Onaylananlar Ekranı'));
    }

    public function postOnaylananlist() {
        $hatirlatma_id=Input::get('hatirlatma_id');
        $netsiscari_id=Input::get('netsiscari_id');
        $servisid=BackendController::getKullaniciServis();
        $hatirlatma = false;
        if($hatirlatma_id!="") {
            $hatirlatma = Hatirlatma::find($hatirlatma_id);
        }
        if($hatirlatma){
            $query = Onaylanan::where('onaylanan.netsiscari_id',$hatirlatma->netsiscari_id)->whereIn('onaylanan.servis_id',$servisid)
                ->select(array("onaylanan.id","netsiscari.cariadi","uretimyer.yeradi","ucretlendirilen.sayacsayisi","kullanici.adi_soyadi",
                    "onaylanan.gonaylamatipi","onaylanan.onaytarihi","onaylanan.gonaytarihi","netsiscari.ncariadi","uretimyer.nyeradi","kullanici.nadi_soyadi",
                    "onaylanan.nonaylamatipi","onaylanan.onayformu"))
                ->leftjoin("ucretlendirilen", "onaylanan.ucretlendirilen_id", "=", "ucretlendirilen.id")
                ->leftjoin("uretimyer", "onaylanan.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("yetkili", "onaylanan.yetkili_id", "=", "yetkili.id")
                ->leftjoin("kullanici", "yetkili.kullanici_id", "=", "kullanici.id")
                ->leftjoin("netsiscari", "onaylanan.netsiscari_id", "=", "netsiscari.id");

        }elseif($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $query = Onaylanan::whereIn('onaylanan.netsiscari_id',$netsiscarilist)->whereIn('onaylanan.servis_id',$servisid)
                ->select(array("onaylanan.id","netsiscari.cariadi","uretimyer.yeradi","ucretlendirilen.sayacsayisi","kullanici.adi_soyadi",
                    "onaylanan.gonaylamatipi","onaylanan.onaytarihi","onaylanan.gonaytarihi","netsiscari.ncariadi","uretimyer.nyeradi","kullanici.nadi_soyadi",
                    "onaylanan.nonaylamatipi","onaylanan.onayformu"))
                ->leftjoin("ucretlendirilen", "onaylanan.ucretlendirilen_id", "=", "ucretlendirilen.id")
                ->leftjoin("uretimyer", "onaylanan.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("yetkili", "onaylanan.yetkili_id", "=", "yetkili.id")
                ->leftjoin("kullanici", "yetkili.kullanici_id", "=", "kullanici.id")
                ->leftjoin("netsiscari", "onaylanan.netsiscari_id", "=", "netsiscari.id");

        }else{
            $query = Onaylanan::whereIn('onaylanan.servis_id',$servisid)
                ->select(array("onaylanan.id","netsiscari.cariadi","uretimyer.yeradi","ucretlendirilen.sayacsayisi","kullanici.adi_soyadi",
                    "onaylanan.gonaylamatipi","onaylanan.onaytarihi","onaylanan.gonaytarihi","netsiscari.ncariadi","uretimyer.nyeradi","kullanici.nadi_soyadi",
                    "onaylanan.nonaylamatipi","onaylanan.onayformu"))
                ->leftjoin("ucretlendirilen", "onaylanan.ucretlendirilen_id", "=", "ucretlendirilen.id")
                ->leftjoin("uretimyer", "onaylanan.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("yetkili", "onaylanan.yetkili_id", "=", "yetkili.id")
                ->leftjoin("kullanici", "yetkili.kullanici_id", "=", "kullanici.id")
                ->leftjoin("netsiscari", "onaylanan.netsiscari_id", "=", "netsiscari.id");

        }
        return Datatables::of($query)
            ->editColumn('onaytarihi', function ($model) {
                $date = new DateTime($model->onaytarihi);
                return $date->format('d-m-Y');})
            ->addColumn('islemler',function ($model) {
                $root = BackendController::getRootDizin();
                if($model->gonaylamatipi=='Email')
                    return "<a class='btn btn-sm btn-warning detay' href='#detay-goster' data-toggle='modal' data-id='".$model->id."'>Detay</a>
                            <a class='btn btn-sm btn-danger goster' target='_blank' href='".$root."/assets/onayformu/".$model->onayformu."' data-id='".$model->id."'>Form</a>";
                else
                    return "<a class='btn btn-sm btn-warning detay' href='#detay-goster' data-toggle='modal' data-id='".$model->id."'>Detay</a>";

            })
            ->make(true);
    }

    public function getOnaylananbilgi() {
        try {
            $id=Input::get('id');
            $onaylanan = Onaylanan::find($id);
            $ucretlendirilen = Ucretlendirilen::find($onaylanan->ucretlendirilen_id);
            $ucretlendirilen->uretimyer = Uretimyer::find($ucretlendirilen->uretimyer_id);
            $ucretlendirilen->parabirimi = ParaBirimi::find($ucretlendirilen->parabirimi_id);
            $ucretlendirilen->parabirimi2 = ParaBirimi::find($ucretlendirilen->parabirimi2_id);
            $secilenler = explode(',', $ucretlendirilen->secilenler);
            $ucretlendirilen->arizafiyat = ArizaFiyat::whereIn('id', $secilenler)->get();
            foreach ($ucretlendirilen->arizafiyat as $arizafiyat) {
                $arizafiyat->sayacadi = SayacAdi::find($arizafiyat->sayacadi_id);
                $arizafiyat->sayaccap = SayacCap::find($arizafiyat->sayaccap_id);
                $arizafiyat->parabirimi = ParaBirimi::find($arizafiyat->parabirimi_id);
                $arizafiyat->parabirimi2 = ParaBirimi::find($arizafiyat->parabirimi2_id);
            }
            $dovizkuru = DovizKuru::where('tarih', $ucretlendirilen->kurtarihi)->take(3)->get();
            foreach ($dovizkuru as $doviz) {
                $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
            }

            return Response::json(array('durum'=>true,'ucretlendirilen' => $ucretlendirilen, 'dovizkuru' => $dovizkuru));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error','title' => 'Onaylama Bilgisi Hatalı' ));
        }
    }

    public function getReddedilenler($hatirlatma_id=false) {
        if($hatirlatma_id)
            return View::make('ucretlendirme.reddedilenler',array('hatirlatma_id'=>$hatirlatma_id))->with(array('title'=>'Reddedilen Fiyatlandırma Ekranı'));
        else
            return View::make('ucretlendirme.reddedilenler')->with(array('title'=>'Reddedilen Fiyatlandırma Ekranı'));
    }

    public function postReddedilenlist() {
        $hatirlatma_id=Input::get('hatirlatma_id');
        $netsiscari_id=Input::get('netsiscari_id');
        $servisid=BackendController::getKullaniciServis();
        if($hatirlatma_id!="") {
            $hatirlatma = Hatirlatma::find($hatirlatma_id);
            $query = Ucretlendirilen::where('netsiscari_id',$hatirlatma->netsiscari_id)->whereIn('ucretlendirilen.servis_id',$servisid)->whereNotNull('ucretlendirilen.reddetmetarihi')
                ->select(array("ucretlendirilen.id","netsiscari.cariadi","uretimyer.yeradi","ucretlendirilen.greddurum",
                    "ucretlendirilen.durumtarihi","ucretlendirilen.gdurumtarihi","netsiscari.ncariadi","uretimyer.nyeradi","ucretlendirilen.nreddurum"))
                ->leftjoin("uretimyer", "ucretlendirilen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("netsiscari", "ucretlendirilen.netsiscari_id", "=", "netsiscari.id");
        }else if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $query = Ucretlendirilen::whereIn('netsiscari_id',$netsiscarilist)->whereIn('ucretlendirilen.servis_id',$servisid)->whereNotNull('ucretlendirilen.reddetmetarihi')
                ->select(array("ucretlendirilen.id","netsiscari.cariadi","uretimyer.yeradi","ucretlendirilen.greddurum",
                    "ucretlendirilen.durumtarihi","ucretlendirilen.gdurumtarihi","netsiscari.ncariadi","uretimyer.nyeradi","ucretlendirilen.nreddurum"))
                ->leftjoin("uretimyer", "ucretlendirilen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("netsiscari", "ucretlendirilen.netsiscari_id", "=", "netsiscari.id");
        }else{
            $query = Ucretlendirilen::whereIn('ucretlendirilen.servis_id',$servisid)->whereNotNull('ucretlendirilen.reddetmetarihi')
                ->select(array("ucretlendirilen.id","netsiscari.cariadi","uretimyer.yeradi","ucretlendirilen.greddurum",
                    "ucretlendirilen.durumtarihi","ucretlendirilen.gdurumtarihi","netsiscari.ncariadi","uretimyer.nyeradi","ucretlendirilen.nreddurum"))
                ->leftjoin("uretimyer", "ucretlendirilen.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("netsiscari", "ucretlendirilen.netsiscari_id", "=", "netsiscari.id");

        }
        return Datatables::of($query)
            ->editColumn('durumtarihi', function ($model) {
                $date = new DateTime($model->durumtarihi);
                return $date->format('d-m-Y');
            })
            ->addColumn('islemler', function ($model) {
                return "<a class='btn btn-sm btn-warning neden' href='#redneden' data-toggle='modal' data-id='".$model->id."'>Detay</a>";
            })
            ->make(true);
    }

    public function getReddedilen() {
        try {
            $id=Input::get('id');
            $ucretlendirilen = Ucretlendirilen::find($id);
            $ucretlendirilen->uretimyer = Uretimyer::find($ucretlendirilen->uretimyer_id);
            $ucretlendirilen->parabirimi = ParaBirimi::find($ucretlendirilen->parabirimi_id);
            $ucretlendirilen->parabirimi2 = ParaBirimi::find($ucretlendirilen->parabirimi2_id);
            $ucretlendirilen->reddetmetarihi=date("d-m-Y H:i:s", strtotime($ucretlendirilen->reddetmetarihi));
            $secilenler = explode(',', $ucretlendirilen->secilenler);
            $ucretlendirilen->arizafiyat = ArizaFiyat::whereIn('id', $secilenler)->get();
            foreach ($ucretlendirilen->arizafiyat as $arizafiyat) {
                $arizafiyat->sayacadi = SayacAdi::find($arizafiyat->sayacadi_id);
                $arizafiyat->sayaccap = SayacCap::find($arizafiyat->sayaccap_id);
                $arizafiyat->parabirimi = ParaBirimi::find($arizafiyat->parabirimi_id);
                $arizafiyat->parabirimi2 = ParaBirimi::find($arizafiyat->parabirimi2_id);
                $arizafiyat->eski = ArizaFiyatEski::where('arizafiyat_id', $arizafiyat->id)->first();
                if($arizafiyat->eski){
                    $arizafiyat->eski->parabirimi = ParaBirimi::find($arizafiyat->eski->parabirimi_id);
                    $arizafiyat->eski->parabirimi2 = ParaBirimi::find($arizafiyat->eski->parabirimi2_id);
                }
            }
            $dovizkuru = DovizKuru::where('tarih', $ucretlendirilen->kurtarihi)->take(3)->get();
            foreach ($dovizkuru as $doviz) {
                $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
            }
            return Response::json(array('durum'=>true,'ucretlendirilen' => $ucretlendirilen, 'dovizkuru' => $dovizkuru));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error','title' => 'Reddedilen Bilgisi Hatalı' ));
        }
    }

    public function postSubegonder(){
        try {
            $secilenler = Input::get('teslimsecilenler');
            $secilenlist = explode(',', $secilenler);
            $sayacsayisi = Input::get('teslimadet');
            $arizafiyatlar = ArizaFiyat::whereIn('sayacgelen_id', $secilenlist)->get();
            $sayacgelen = SayacGelen::find($secilenlist[0]);
            $netsiscari = NetsisCari::find($sayacgelen->netsiscari_id);
            if ($netsiscari->caridurum != "A")
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
            $netsisdepo = NetsisDepolar::where('netsiscari_id', $netsiscari->id)->first();
            if (!$netsisdepo)
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Depo Uyarısı', 'text' => 'Bu Cari için Şubeler Kısmında Netsis Deposu Tanımlı Değil.', 'type' => 'warning'));
            DB::beginTransaction();
            if(SayacGelen::whereIn('id',$secilenlist)->where('depoteslim',1)->get()->count()>0){
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaçlar Şubeye Gönderilemedi!', 'text' => 'Seçilen Sayaçlar için Zaten bir Depo Teslimi Mevcut Olabilir!', 'type' => 'warning'));
            }
            $depoteslim = DepoTeslim::where('servis_id', $sayacgelen->servis_id)->where('netsiscari_id', $sayacgelen->netsiscari_id)
                ->where('depodurum',0)->where('tipi',0)->where('periyodik',0)->where('subegonderim', 1)->first();
            if ($depoteslim) {
                $secilensayaclist = explode(',', $depoteslim->secilenler);
                $secilenler="";
                $adet = 0;
                foreach($secilenlist as $sayacgelenid){
                    if (!in_array($sayacgelenid, $secilensayaclist)) {  //sayaç bu listede ise
                        $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                        $adet++;
                    }
                }
                if ($adet>0) {
                    $depoteslim->secilenler .= ',' . $secilenler;
                    $depoteslim->sayacsayisi += $adet;
                    $depoteslim->save();
                }
                foreach ($arizafiyatlar as $arizafiyat) {
                    $arizafiyat->durum = 0;
                    $arizafiyat->save();
                    $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                    if($sayacgelen->teslimdurum==4){
                        Db::rollBack();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Depolararası Transfer Aktarılamadı!', 'text' => 'Aktarım Zaten Onaylanmış.Depo Tesliminden Kontrol Edilebilir!', 'type' => 'warning'));
                    }
                    $sayacgelen->teslimdurum = 4; //0 bekliyor 1 teslimat 2 geri gönderim 3 hurda 4 depolararasi 5 periyodik bakım
                    $sayacgelen->save();
                    $sayac = Sayac::find($arizafiyat->sayac_id);
                    $sayac->songelistarihi = $sayacgelen->depotarihi;
                    $sayac->save();
                }
                BackendController::HatirlatmaGuncelle(4, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, $sayacsayisi);
                BackendController::HatirlatmaEkle(9, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, $sayacsayisi);
                BackendController::BildirimEkle(4, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, $sayacsayisi);
                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-truck', $depoteslim->id . ' Numaralı Teslimat Depolararası Teslimat Olarak Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Depo Teslim Numarası:' . $depoteslim->id);
                DB::commit();
                return Redirect::to('ucretlendirme/ucretlendirmekayit')->with(array('mesaj' => 'true', 'title' => 'Depolararası Transfer Başarıyla Aktarıldı', 'text' => 'Sayaçlar Depo Teslim Sayfasından Teslim Edilebilir', 'type' => 'success'));
            } else {
                $depoteslim = new DepoTeslim;
                $depoteslim->servis_id = $sayacgelen->servis_id;
                $depoteslim->netsiscari_id = $sayacgelen->netsiscari_id;
                $depoteslim->secilenler = $secilenler;
                $depoteslim->sayacsayisi = $sayacsayisi;
                $depoteslim->depodurum = 0;
                $depoteslim->subegonderim = 1;
                $depoteslim->parabirimi_id=1;
                $depoteslim->save();
                foreach ($arizafiyatlar as $arizafiyat) {
                    $arizafiyat->durum = 1;
                    $arizafiyat->save();
                    $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                    if($sayacgelen->teslimdurum==4){
                        Db::rollBack();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Depolararası Transfer Aktarılamadı!', 'text' => 'Aktarım Zaten Onaylanmış.Depo Tesliminden Kontrol Edilebilir!', 'type' => 'warning'));
                    }
                    $sayacgelen->teslimdurum = 4; //0 bekliyor 1 teslimat 2 geri gönderim 3 hurda 4 depolararasi 5 periyodik bakım
                    $sayacgelen->save();
                    $sayac = Sayac::find($arizafiyat->sayac_id);
                    $sayac->songelistarihi = $sayacgelen->depotarihi;
                    $sayac->save();
                }
                BackendController::HatirlatmaGuncelle(4, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, $sayacsayisi);
                BackendController::HatirlatmaEkle(9, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, $sayacsayisi);
                BackendController::BildirimEkle(4, $sayacgelen->netsiscari_id, $sayacgelen->servis_id, $sayacsayisi);
                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-truck', $depoteslim->id . ' Numaralı Teslimat Depolararası Teslimat Olarak Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Depo Teslim Numarası:' . $depoteslim->id);
                DB::commit();
                return Redirect::to('ucretlendirme/ucretlendirmekayit')->with(array('mesaj' => 'true', 'title' => 'Depolararası Transfer Başarıyla Aktarıldı', 'text' => 'Sayaçlar Depo Teslim Sayfasından Teslim Edilebilir', 'type' => 'success'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı yapılamadı', 'text' => 'Depo Teslimatı yapılırken hata oluştu', 'type' => 'error'));
        }
    }

    public function getYetkilibilgi() {
        try {
            $id=Input::get('id');
            $ucretlendirilen = Ucretlendirilen::find($id);
            $ucretlendirilen->uretimyer = Uretimyer::find($ucretlendirilen->uretimyer_id);
            $ucretlendirilen->netsiscari = Netsiscari::find($ucretlendirilen->netsiscari_id);
            $ucretlendirilen->parabirimi = ParaBirimi::find($ucretlendirilen->parabirimi_id);
            $ucretlendirilen->parabirimi2 = ParaBirimi::find($ucretlendirilen->parabirimi2_id);
            $secilenler = explode(',', $ucretlendirilen->secilenler);
            $ucretlendirilen->arizafiyat = ArizaFiyat::whereIn('id', $secilenler)->get();
            foreach ($ucretlendirilen->arizafiyat as $arizafiyat) {
                $arizafiyat->sayacadi = SayacAdi::find($arizafiyat->sayacadi_id);
                $arizafiyat->sayaccap = SayacCap::find($arizafiyat->sayaccap_id);
                $arizafiyat->parabirimi = ParaBirimi::find($arizafiyat->parabirimi_id);
                $arizafiyat->parabirimi2 = ParaBirimi::find($arizafiyat->parabirimi2_id);
            }
            $dovizkuru = DovizKuru::where('tarih', $ucretlendirilen->kurtarihi)->take(3)->get();
            foreach ($dovizkuru as $doviz) {
                $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
            }
            $yetkili = Auth::user();
            $root = BackendController::getRootDizin(true);

            return Response::json(array('durum'=>true,'ucretlendirilen' => $ucretlendirilen, 'dovizkuru' => $dovizkuru, 'yetkili' => $yetkili, 'root' => $root));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error','title' => 'Yetkili Bilgisi Hatalı' ));
        }
    }

    public function postYetkilionayla(){
        try {
            if (Input::has('onaymailvar')) //mail gönderilecek
            {
                $rules = ['onayyetkilimail' => 'required|email'];
                $mail = 1;
                if (Input::has('detaylifiyatlandirma')) //mail gönderilecek
                {
                    $detaylifiyat=1;
                }else{
                    $detaylifiyat=0;
                }
            } else // mail gönderilmeyecek
            {
                $rules = [];
                $mail = 0;
                $detaylifiyat = 0;
            }
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $digermail = Input::get('onaymailcc');
            $digermaillist = explode(';', $digermail);
            if ($digermail != "")
                foreach ($digermaillist as $email) {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Diğer Mail Listesi Hatası', 'text' => $email . ' geçerli bir email değil. Birden fazla girilen mail adreslerini ; ile ayırınız!', 'type' => 'error'));
                    }
                }
            $ucretlendirilenid = Input::get('onayid');
            $secilenler = Input::get('onaysecilenler');
            $secilenlist = explode(',', $secilenler);
            $tumu = explode(',', Input::get('onaytumu'));
            $yetkilimail = Input::get('onayyetkilimail');
            $yetkiliid = Auth::user()->id;
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            $birim = Input::get('onaybirim');
            $birim2 = Input::get('onaybirim2')=="" ? null : Input::get('onaybirim2');
            DB::beginTransaction();
            if($ucretlendirilen->durum==0){
                $yetkili = Yetkili::where('netsiscari_id',$ucretlendirilen->netsiscari_id)->where('kullanici_id',$yetkiliid)->where('aktif',1)->first();
            }else if($ucretlendirilen->durum==1){
                $yetkili = Yetkili::find($ucretlendirilen->yetkili_id);
            }else{
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı!', 'text' => 'Ücretlendirme Zaten Onaylanmış !', 'type' => 'warning'));
            }
            if(!$yetkili){
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Yetkili Bilgisi Bulunamadı!', 'text' => 'Cari Bilgisine Ait Bu Kullanıcının Onaylama Yetkisi Yok!', 'type' => 'error'));
            }
            $onaydurum=$ucretlendirilen->durum; // 0 bekliyor 1 mail gönderildi 2 onaylandı
            $servisid = $ucretlendirilen->servis_id;
            $uretimyerid = $ucretlendirilen->uretimyer_id;
            $uretimyer = UretimYer::find($uretimyerid);
            $netsiscariid = $ucretlendirilen->netsiscari_id;
            $sayacsayisi = count($secilenlist);
            $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenlist)->get();
            $servistakipler = ServisTakip::whereIn('arizafiyat_id', $secilenlist)->get();
            $sube = Sube::where('netsiscari_id', $netsiscariid)->where('aktif', 1)->first();
            $netsiscari = NetsisCari::find($netsiscariid);
            $subedurum = 0;
            if($sube){
                foreach ($servistakipler as $servistakip){
                    if($servistakip->subedurum)
                        $subedurum = 1;
                }
            }
            $kullanici=Kullanici::find($yetkiliid);
            $ilkmail = $kullanici->ilkmail;
            $girisadi = $kullanici->girisadi;
            $takipno = $netsiscari->takipno;
            if (count($secilenlist) == count($tumu)) //ucretlendirmedeki tum sayaçlar onaylanmış
            {
                if ($mail) {
                    $fiyatlandirma = BackendController::getFiyatlandirma($ucretlendirilenid);
                    if (is_null($fiyatlandirma)){
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                    }
                    if($detaylifiyat){
                        $detaylifiyatlandirma = BackendController::getDetaylifiyatlandirma($ucretlendirilenid);
                        if (is_null($detaylifiyatlandirma)){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Detaylı Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                        }
                    }
                }
                try {
                    $ucretlendirilen->durum = 2;
                    $ucretlendirilen->onaytipi = 3;
                    $ucretlendirilen->yetkili_id=$yetkili->id;
                    $ucretlendirilen->onaytarihi = date('Y-m-d H:i:s');
                    $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                    $ucretlendirilen->save();
                    $onaylanan = new Onaylanan;
                    $onaylanan->servis_id = $servisid;
                    $onaylanan->uretimyer_id = $uretimyerid;
                    $onaylanan->netsiscari_id = $netsiscariid;
                    $onaylanan->ucretlendirilen_id = $ucretlendirilen->id;
                    $onaylanan->yetkili_id = $yetkili->id;
                    $onaylanan->onaytarihi = date('Y-m-d H:i:s');
                    $onaylanan->onaylamatipi = 3;
                    $onaylanan->save();
                    $secilensayaclar = "";
                    $secilensayaclist = array();
                    $garantisayaclar = "";
                    $garantisayaclist = array();
                    $iadesayaclar = "";
                    $iadesayaclist = array();
                    try {
                        foreach ($arizafiyatlar as $arizafiyat) {
                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                            $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                            if($arizakayit->arizakayit_durum==7){
                                array_push($iadesayaclist, $sayacgelen->id);
                                $iadesayaclar .= ($iadesayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0) {
                                array_push($secilensayaclist, $sayacgelen->id);
                                $secilensayaclar .= ($secilensayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }else{
                                array_push($garantisayaclist, $sayacgelen->id);
                                $garantisayaclar .= ($garantisayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }
                            $sayacgelen->musterionay = 1;
                            $sayacgelen->teslimdurum = 1;
                            $sayacgelen->save();
                            $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                            $servistakip->onaylanan_id = $onaylanan->id;
                            $servistakip->durum = 5;
                            $servistakip->onaylanmatarihi = date('Y-m-d H:i:s');
                            $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                            $servistakip->kullanici_id = Auth::user()->id;
                            $servistakip->save();
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Kaydedilemedi', 'text' => 'Sayaç Gelen ve Servis Takip Bilgisi Güncellenemedi', 'type' => 'error'));
                    }
                    try {
                        if ($mail) {
                            try {
                                Mail::send('mail.bilgilendirme', array('ucretlendirilen' => $ucretlendirilen, 'ilkmail' => $ilkmail, 'girisadi' => $girisadi,'takipno'=>$takipno),
                                    function ($message) use ($yetkili, $yetkilimail, $uretimyer, $digermaillist, $digermail,$detaylifiyat) {
                                        //$message->cc(Auth::user()->mail,Auth::user()->adi_soyadi);
                                        if ($digermail != "")
                                            foreach ($digermaillist as $email)
                                                $message->cc($email);
                                        $message->to($yetkilimail, 'Yetkili')->subject("Manas ServisTakip Fiyatlandırma Bilgilendirme");
                                        $message->attach(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                        if($detaylifiyat){
                                            $message->attach(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                        }
                                    });
                                $ucretlendirilen->mail = 1;
                                $ucretlendirilen->save();
                                $kullanici->ilkmail = 1;
                                $kullanici->save();
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => 'Mail Gönderirken Hata Oluştu', 'type' => 'error'));
                            }
                            if (count(Mail::failures()) > 0) {
                                DB::rollBack();
                                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => 'Mail Gönderirken Hata Oluştu', 'type' => 'error'));
                            } else {
                                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                            }
                        } else {
                            File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                            if($detaylifiyat){
                                File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                            }
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                    }
                    if ($servisid == 5) //kalibrasyon tamamlanmışsa depo teslim yoksa bekleyecek
                    {
                        try {
                            $kalibrasyonsayi=0;
                            $kalibrasyonsayaclist="";
                            $garantisayi=0;
                            $garantisayaclist="";
                            $geriiadesayi=0;
                            $geriiadesayaclist="";
                            $periyodiksayi=0;
                            $periyodiklist="";
                            foreach ($arizafiyatlar as $arizafiyat) {
                                $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                                $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                $depogelen=DepoGelen::find($sayacgelen->depogelen_id);
                                if($arizakayit->arizakayit_durum != 3) { //yedek parça bekliyorsa depo teslim olmaz
                                    if ($depogelen->periyodik) {
                                        if ($sayacgelen->kalibrasyon) {
                                            $periyodiksayi++;
                                            $periyodiklist .= ($periyodiklist == "" ? "" : ",") . $sayacgelen->id;
                                        } else {
                                            $sayacgelen->teslimdurum = 0;
                                            $sayacgelen->save();
                                        }
                                    } else {
                                        if ($arizakayit->arizakayit_durum == 7) {
                                            $geriiadesayi++;
                                            $geriiadesayaclist .= ($geriiadesayaclist == "" ? "" : ",") . $sayacgelen->id;
                                        } else if ($sayacgelen->kalibrasyon) {
                                            if ($arizafiyat->toplamtutar > 0 || $arizafiyat->toplamtutar2 > 0) {
                                                $kalibrasyonsayi++;
                                                $kalibrasyonsayaclist .= ($kalibrasyonsayaclist == "" ? "" : ",") . $sayacgelen->id;
                                            } else {
                                                $garantisayi++;
                                                $garantisayaclist .= ($garantisayaclist == "" ? "" : ",") . $sayacgelen->id;
                                            }
                                        } else {
                                            $sayacgelen->teslimdurum = 0;
                                            $sayacgelen->save();
                                        }
                                    }
                                }else{
                                    $sayacgelen->teslimdurum = 0;
                                    $sayacgelen->save();
                                }
                            }
                            if($kalibrasyonsayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $kalibrasyonsayaclar = explode(',', $kalibrasyonsayaclist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($kalibrasyonsayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $kalibrasyonsayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $kalibrasyonsayaclist;
                                    $depoteslim->sayacsayisi = $kalibrasyonsayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if($garantisayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum',0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $garantisayaclar = explode(',', $garantisayaclist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($garantisayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $garantisayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $garantisayaclist;
                                    $depoteslim->sayacsayisi = $garantisayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 1;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if($geriiadesayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum',0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $geriiadesayaclar = explode(',', $geriiadesayaclist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($geriiadesayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $geriiadesayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $geriiadesayaclist;
                                    $depoteslim->sayacsayisi = $geriiadesayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 2;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if($periyodiksayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum',0)->where('tipi',0)->where('periyodik',1)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $periyodiksayaclar = explode(',', $periyodiklist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($periyodiksayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $periyodiksayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $periyodiklist;
                                    $depoteslim->sayacsayisi = $periyodiksayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->periyodik = 1;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi.', 'type' => 'error'));
                        }
                        if($onaydurum==1)
                            BackendController::HatirlatmaGuncelle(6, $netsiscariid, $servisid, $sayacsayisi);
                        else
                            BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                        BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                        if(($kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi)>0)
                            BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi);
                        BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Yetkili Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                        DB::commit();
                        return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Onayı Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                    } else if ($servisid == 6)//abone teslim
                    {
                        // abone yoksa onaylama izin verme
                        $sayacgelenler = SayacGelen::whereIn('id', $secilensayaclist)->get(array('serino'));
                        $abonesayaclar = AboneSayac::whereIn('serino', $sayacgelenler->toArray())->get(array('id')); //sayacgelenler array değil gibi geliyor
                        if ($abonesayaclar->count() == $sayacsayisi) { //ücretlendirilen sayaçların hepsi mevcut
                            $abonetahsisler = AboneTahsis::whereIn('abonesayac_id', $abonesayaclar->toArray())->get();
                            if ($abonetahsisler->count() == $sayacsayisi) {//sayaçların hepsi tahsisliyse
                                try {
                                    $aboneteslimlist = array();
                                    foreach ($abonetahsisler as $abonetahsis) {
                                        $abone = Abone::find($abonetahsis->abone_id);
                                        $abonesayac = AboneSayac::find($abonetahsis->abonesayac_id);
                                        $abonesayacgelen = SayacGelen::whereIn('id', $secilensayaclist)->where('serino', $abonesayac->serino)->first();
                                        try {
                                            $aboneteslim = AboneTeslim::where('abone_id', $abone->id)->where('teslimdurum', 0)->first();
                                            if ($aboneteslim) { //teslim edilmemiş o kişiye ait sayaç varsa
                                                $aboneteslim->secilenler .= ',' . $abonesayacgelen->id;
                                                $aboneteslim->sayacsayisi += 1;
                                                $aboneteslim->parabirimi2_id = $aboneteslim->parabirimi2_id == null ? $birim2 : $aboneteslim->parabirimi2_id;
                                                $aboneteslim->save();
                                                array_push($aboneteslimlist, $aboneteslim->id);
                                            } else { // yeni abone teslimi
                                                $aboneteslim = new AboneTeslim;
                                                $aboneteslim->abone_id = $abone->id;
                                                $aboneteslim->uretimyer_id = $abone->uretimyer_id;
                                                $aboneteslim->netsiscari_id = $abone->netsiscari_id;
                                                $aboneteslim->secilenler = $abonesayacgelen->id;
                                                $aboneteslim->sayacsayisi = 1;
                                                $aboneteslim->teslimdurum = 0;
                                                $aboneteslim->subekodu = $abone->subekodu;
                                                $aboneteslim->parabirimi_id = $birim;
                                                $aboneteslim->parabirimi2_id = $birim2;
                                                $aboneteslim->save();
                                                array_push($aboneteslimlist, $aboneteslim->id);
                                            }
                                        } catch (Exception $e) {
                                            Log::error($e);
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı', 'text' => 'Ücretlendirilen sayaçlar abone teslimine kaydedilemedi!', 'type' => 'error'));
                                        }
                                    }
                                } catch (Exception $e) {
                                    Log::error($e);
                                    DB::rollBack();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı abone teslimine kaydedilemedi!', 'type' => 'error'));
                                }
                                if ($onaydurum == 1)
                                    BackendController::HatirlatmaGuncelle(6, $netsiscariid, $servisid, $sayacsayisi);
                                else
                                    BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::HatirlatmaEkle(12, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Yetkili Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                                DB::commit();
                                return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Onayı Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                            } else {//tahsisli olmayan sayaç mevcut
                                DB::rollBack();
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı aboneye tahsisli değil. Önce tahsis işlemlerinin tamamlanması gerekiyor!', 'type' => 'error'));
                            }
                        } else {//listede olmayan sayaç mevcut
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı aboneye tahsisli değil. Önce tahsis işlemlerinin tamamlanması gerekiyor!', 'type' => 'error'));
                        }
                    } else {
                        try {
                            if(count($secilensayaclist)>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($secilensayaclist as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $secilensayaclar;
                                    $depoteslim->sayacsayisi = count($secilensayaclist);
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->subegonderim=$subedurum;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if(count($garantisayaclist)>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($garantisayaclist as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $garantisayaclar;
                                    $depoteslim->sayacsayisi = count($garantisayaclist);
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 1;
                                    $depoteslim->subegonderim=$subedurum;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if(count($iadesayaclist)>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($iadesayaclist as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $iadesayaclar;
                                    $depoteslim->sayacsayisi = count($iadesayaclist);
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 2;
                                    $depoteslim->subegonderim=$subedurum;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if ($onaydurum == 1)
                                BackendController::HatirlatmaGuncelle(6, $netsiscariid, $servisid, $sayacsayisi);
                            else
                                BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Yetkili Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                            DB::commit();
                            return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Onayı Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi', 'type' => 'error'));
                        }
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylama onaylananlara kaydedilemedi', 'type' => 'error'));
                }
            } else { //bazıları onaylanmış
                try {
                    $yeniucretlendirilen = new Ucretlendirilen;
                    $yeniucretlendirilen->servis_id = $servisid;
                    $yeniucretlendirilen->uretimyer_id = $uretimyerid;
                    $yeniucretlendirilen->netsiscari_id = $netsiscariid;
                    $yeniucretlendirilen->secilenler = $secilenler;
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
                            if ($yeniparabirimi === "1") { //tl
                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                            } else { //euro dolar sterln
                                if ($arizafiyat->parabirimi_id === "1") {
                                    $kur = 1 / BackendController::getKurBilgisi($yeniparabirimi, $kurtarih);
                                } else {
                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($yeniparabirimi, $kurtarih);
                                }
                            }
                        }
                        $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                        if($yeniparabirimi2===null){
                            if($arizafiyat->parabirimi2_id!==null){
                                if($arizafiyat->parabirimi2_id===$yeniparabirimi){
                                    $arizafiyat->fiyat +=$arizafiyat->fiyat2;
                                    $arizafiyat->fiyat2=0;
                                    $arizafiyat->parabirimi2_id=null;
                                }else{
                                    $yeniparabirimi2=$arizafiyat->parabirimi2_id;
                                }
                            }
                        }else{
                            if($arizafiyat->parabirimi2_id!=null){
                                if($arizafiyat->parabirimi2_id===$yeniparabirimi){
                                    $arizafiyat->fiyat +=$arizafiyat->fiyat2;
                                    $arizafiyat->fiyat2=0;
                                    $arizafiyat->parabirimi2_id=null;
                                }else if($arizafiyat->parabirimi2_id!==$yeniparabirimi2){
                                    DB::rollBack();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Yapılamadı', 'text' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
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
                    $yeniucretlendirilen->sayacsayisi = $sayacsayisi;
                    $yeniucretlendirilen->fiyat = $yenifiyat;
                    $yeniucretlendirilen->fiyat2 = $yenifiyat2;
                    $yeniucretlendirilen->parabirimi_id = $yeniparabirimi;
                    $yeniucretlendirilen->parabirimi2_id = $yeniparabirimi2;
                    $yeniucretlendirilen->kullanici_id = Auth::user()->id;
                    $yeniucretlendirilen->kayittarihi = date('Y-m-d H:i:s');
                    $yeniucretlendirilen->kurtarihi = $ucretlendirilen->kurtarihi;
                    $yeniucretlendirilen->durum = 2;
                    $yeniucretlendirilen->onaytipi = 3;
                    $yeniucretlendirilen->onaytarihi = date('Y-m-d H:i:s');
                    $yeniucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                    $yeniucretlendirilen->yetkili_id=$yetkili->id;
                    $yeniucretlendirilen->save();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Yapılamadı', 'text' => 'Seçilen Sayaçlara ait Ücretlendirme Kaydedilemedi', 'type' => 'error'));
                }
                DB::commit();
                if ($mail) {
                    $fiyatlandirma = BackendController::getFiyatlandirma($yeniucretlendirilen->id);
                    if (is_null($fiyatlandirma)) {
                        $yeniucretlendirilen->delete();
                        DB::commit();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                    }
                    if($detaylifiyat){
                        $detaylifiyatlandirma = BackendController::getDetaylifiyatlandirma($yeniucretlendirilen->id);
                        if (is_null($detaylifiyatlandirma)){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Detaylı Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                        }
                    }
                }
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
                    if ($kalanparabirimi !== $arizafiyat->parabirimi_id) {
                        if ($kalanparabirimi === "1") { //tl
                            $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                        } else { //euro dolar sterln
                            if ($arizafiyat->parabirimi_id === "1") {
                                $kur = 1 / BackendController::getKurBilgisi($kalanparabirimi, $kurtarih);
                            } else {
                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($kalanparabirimi, $kurtarih);
                            }
                        }
                    }
                    $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                    if ($kalanparabirimi2 === null) {
                        if ($arizafiyat->parabirimi2_id !== null) {
                            if ($arizafiyat->parabirimi2_id === $kalanparabirimi) {
                                $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                $arizafiyat->fiyat2 = 0;
                                $arizafiyat->parabirimi2_id = null;
                            } else {
                                $kalanparabirimi2 = $arizafiyat->parabirimi2_id;
                            }
                        }
                    } else {
                        if ($arizafiyat->parabirimi2_id !== null) {
                            if ($arizafiyat->parabirimi2_id === $kalanparabirimi) {
                                $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                $arizafiyat->fiyat2 = 0;
                                $arizafiyat->parabirimi2_id = null;
                            } else if ($arizafiyat->parabirimi2_id !== $kalanparabirimi2) {
                                DB::rollBack();
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Yapılamadı', 'text' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
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
                try {
                    $ucretlendirilen->secilenler = $kalanlar;
                    $ucretlendirilen->garanti = $kalangaranti;
                    $ucretlendirilen->sayacsayisi = $kalansayi;
                    $ucretlendirilen->fiyat = $kalanfiyat;
                    $ucretlendirilen->fiyat2 = $kalanfiyat2;
                    $ucretlendirilen->parabirimi_id = $kalanparabirimi;
                    $ucretlendirilen->parabirimi2_id = $kalanparabirimi2;
                    $ucretlendirilen->save();
                    $onaylanan = new Onaylanan;
                    $onaylanan->servis_id = $servisid;
                    $onaylanan->uretimyer_id = $uretimyerid;
                    $onaylanan->netsiscari_id = $netsiscariid;
                    $onaylanan->ucretlendirilen_id = $yeniucretlendirilen->id;
                    $onaylanan->yetkili_id = $yetkili->id;
                    $onaylanan->onaytarihi = date('Y-m-d H:i:s');
                    $onaylanan->onaylamatipi = 3;
                    $onaylanan->save();
                    $secilensayaclar = "";
                    $secilensayaclist = array();
                    $garantisayaclar = "";
                    $garantisayaclist = array();
                    $iadesayaclar = "";
                    $iadesayaclist = array();
                    try {
                        foreach ($arizafiyatlar as $arizafiyat) {
                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                            $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                            if($arizakayit->arizakayit_durum==7){
                                array_push($iadesayaclist, $sayacgelen->id);
                                $iadesayaclar .= ($iadesayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0) {
                                array_push($secilensayaclist, $sayacgelen->id);
                                $secilensayaclar .= ($secilensayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }else{
                                array_push($garantisayaclist, $sayacgelen->id);
                                $garantisayaclar .= ($garantisayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }
                            $sayacgelen->musterionay = 1;
                            $sayacgelen->teslimdurum = 1;
                            $sayacgelen->save();
                            $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                            $servistakip->ucretlendirilen_id = $yeniucretlendirilen->id;
                            $servistakip->onaylanan_id = $onaylanan->id;
                            $servistakip->durum = 5;
                            $servistakip->onaylanmatarihi = date('Y-m-d H:i:s');
                            $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                            $servistakip->kullanici_id = Auth::user()->id;
                            $servistakip->save();
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Kaydedilemedi', 'text' => 'Sayaç Gelen ve Servis Takip Bilgisi Güncellenemedi', 'type' => 'error'));
                    }
                    try {
                        if ($mail) {
                            try {
                                Mail::send('mail.bilgilendirme', array('ucretlendirilen' => $yeniucretlendirilen, 'ilkmail' => $ilkmail, 'girisadi' => $girisadi,'takipno'=>$takipno),
                                    function ($message) use ($yetkili, $yetkilimail, $uretimyer, $digermaillist, $digermail,$detaylifiyat) {
                                        //$message->cc(Auth::user()->mail,Auth::user()->adi_soyadi);
                                        if ($digermail != "")
                                            foreach ($digermaillist as $email)
                                                $message->cc($email);
                                        $message->to($yetkilimail, 'Yetkili')->subject("Manas ServisTakip Fiyatlandırma Bilgilendirme");
                                        $message->attach(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                        if($detaylifiyat){
                                            $message->attach(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                        }
                                    });
                                $yeniucretlendirilen->mail = 1;
                                $yeniucretlendirilen->save();
                                $kullanici->ilkmail = 1;
                                $kullanici->save();
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => 'Mail Gönderirken Hata Oluştu', 'type' => 'error'));
                            }
                            if (count(Mail::failures()) > 0) {
                                DB::rollBack();
                                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => 'Mail Gönderirken Hata Oluştu', 'type' => 'error'));
                            } else {
                                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                            }
                        } else {
                            File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                            if($detaylifiyat){
                                File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                            }
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                    }
                    if ($servisid == 5) //kalibrasyon tamamlanmışsa depo teslim yoksa bekleyecek
                    {
                        try {
                            $kalibrasyonsayi=0;
                            $kalibrasyonsayaclist="";
                            $garantisayi=0;
                            $garantisayaclist="";
                            $geriiadesayi=0;
                            $geriiadesayaclist="";
                            $periyodiksayi=0;
                            $periyodiklist="";
                            foreach ($arizafiyatlar as $arizafiyat) {
                                $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                                $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                $depogelen=DepoGelen::find($sayacgelen->depogelen_id);
                                if($arizakayit->arizakayit_durum != 3) { //yedek parça bekliyorsa depo teslim olmaz
                                    if ($depogelen->periyodik) {
                                        if ($sayacgelen->kalibrasyon) {
                                            $periyodiksayi++;
                                            $periyodiklist .= ($periyodiklist == "" ? "" : ",") . $sayacgelen->id;
                                        } else {
                                            $sayacgelen->teslimdurum = 0;
                                            $sayacgelen->save();
                                        }
                                    } else {
                                        if ($arizakayit->arizakayit_durum == 7) {
                                            $geriiadesayi++;
                                            $geriiadesayaclist .= ($geriiadesayaclist == "" ? "" : ",") . $sayacgelen->id;
                                        } else if ($sayacgelen->kalibrasyon) {
                                            if ($arizafiyat->toplamtutar > 0 || $arizafiyat->toplamtutar2 > 0) {
                                                $kalibrasyonsayi++;
                                                $kalibrasyonsayaclist .= ($kalibrasyonsayaclist == "" ? "" : ",") . $sayacgelen->id;
                                            } else {
                                                $garantisayi++;
                                                $garantisayaclist .= ($garantisayaclist == "" ? "" : ",") . $sayacgelen->id;
                                            }
                                        } else {
                                            $sayacgelen->teslimdurum = 0;
                                            $sayacgelen->save();
                                        }
                                    }
                                }else{
                                    $sayacgelen->teslimdurum = 0;
                                    $sayacgelen->save();
                                }
                            }
                            if($kalibrasyonsayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $kalibrasyonsayaclar = explode(',', $kalibrasyonsayaclist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($kalibrasyonsayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $kalibrasyonsayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $kalibrasyonsayaclist;
                                    $depoteslim->sayacsayisi = $kalibrasyonsayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                            if($garantisayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum',0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $garantisayaclar = explode(',', $garantisayaclist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($garantisayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $garantisayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $garantisayaclist;
                                    $depoteslim->sayacsayisi = $garantisayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 1;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                            if($geriiadesayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum',0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $geriiadesayaclar = explode(',', $geriiadesayaclist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($geriiadesayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $geriiadesayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $geriiadesayaclist;
                                    $depoteslim->sayacsayisi = $geriiadesayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 2;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                            if($periyodiksayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum',0)->where('tipi',0)->where('periyodik',1)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $periyodiksayaclar = explode(',', $periyodiklist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($periyodiksayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $periyodiksayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $periyodiklist;
                                    $depoteslim->sayacsayisi = $periyodiksayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->periyodik = 1;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi.', 'type' => 'error'));
                        }
                        if ($onaydurum == 1)
                            BackendController::HatirlatmaGuncelle(6, $netsiscariid, $servisid, $sayacsayisi);
                        else
                            BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                        BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                        if(($kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi)>0)
                            BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi);
                        BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Yetkili Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                        DB::commit();
                        return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Onayı Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                    } else if ($servisid == 6)//abone teslim
                    {
                        // abone yoksa onaylama izin verme
                        $sayacgelenler = SayacGelen::whereIn('id', $secilensayaclist)->get(array('serino'));
                        $abonesayaclar = AboneSayac::whereIn('serino', $sayacgelenler->toArray())->get(array('id')); //sayacgelenler array değil gibi geliyor
                        if ($abonesayaclar->count() == $sayacsayisi) { //ücretlendirilen sayaçların hepsi mevcut
                            $abonetahsisler = AboneTahsis::whereIn('abonesayac_id', $abonesayaclar->toArray())->get();
                            if ($abonetahsisler->count() == $sayacsayisi) {//sayaçların hepsi tahsisliyse
                                try {
                                    $aboneteslimlist = array();
                                    foreach ($abonetahsisler as $abonetahsis) {
                                        $abone = Abone::find($abonetahsis->abone_id);
                                        $abonesayac = AboneSayac::find($abonetahsis->abonesayac_id);
                                        $abonesayacgelen = SayacGelen::whereIn('id', $secilensayaclist)->where('serino', $abonesayac->serino)->first();
                                        try {
                                            $aboneteslim = AboneTeslim::where('abone_id', $abone->id)->where('teslimdurum', 0)->first();
                                            if ($aboneteslim) { //teslim edilmemiş o kişiye ait sayaç varsa
                                                $aboneteslim->secilenler .= ',' . $abonesayacgelen->id;
                                                $aboneteslim->sayacsayisi += 1;
                                                $aboneteslim->parabirimi2_id = $aboneteslim->parabirimi2_id==null ? $yeniparabirimi2 : $aboneteslim->parabirimi2_id;
                                                $aboneteslim->save();
                                                array_push($aboneteslimlist, $aboneteslim->id);
                                            } else { // yeni abone teslimi
                                                $aboneteslim = new AboneTeslim;
                                                $aboneteslim->abone_id = $abone->id;
                                                $aboneteslim->uretimyer_id = $abone->uretimyer_id;
                                                $aboneteslim->netsiscari_id = $abone->netsiscari_id;
                                                $aboneteslim->secilenler = $abonesayacgelen->id;
                                                $aboneteslim->sayacsayisi = 1;
                                                $aboneteslim->teslimdurum = 0;
                                                $aboneteslim->subekodu = $abone->subekodu;
                                                $aboneteslim->parabirimi_id = $yeniparabirimi;
                                                $aboneteslim->parabirimi2_id = $yeniparabirimi2;
                                                $aboneteslim->save();
                                                array_push($aboneteslimlist, $aboneteslim->id);
                                            }
                                        } catch (Exception $e) {
                                            Log::error($e);
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı', 'text' => 'Ücretlendirilen sayaçlar abone teslimine kaydedilemedi!', 'type' => 'error'));
                                        }
                                    }
                                } catch (Exception $e) {
                                    Log::error($e);
                                    DB::rollBack();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı abone teslimine kaydedilemedi!', 'type' => 'error'));
                                }
                                if ($onaydurum == 1)
                                    BackendController::HatirlatmaGuncelle(6, $netsiscariid, $servisid, $sayacsayisi);
                                else
                                    BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::HatirlatmaEkle(12, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Yetkili Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                                DB::commit();
                                return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Onayı Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                            } else {//tahsisli olmayan sayaç mevcut
                                DB::rollBack();
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı aboneye tahsisli değil. Önce tahsis işlemlerinin tamamlanması gerekiyor!', 'type' => 'error'));
                            }
                        } else {//listede olmayan sayaç mevcut
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı aboneye tahsisli değil. Önce tahsis işlemlerinin tamamlanması gerekiyor!', 'type' => 'error'));
                        }
                    } else {
                        try {
                            if(count($secilensayaclist)>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($secilensayaclist as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $secilensayaclar;
                                    $depoteslim->sayacsayisi = count($secilensayaclist);
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->subegonderim=$subedurum;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                            if(count($garantisayaclist)>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($garantisayaclist as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $garantisayaclar;
                                    $depoteslim->sayacsayisi = count($garantisayaclist);
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 1;
                                    $depoteslim->subegonderim=$subedurum;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                            if(count($iadesayaclist)>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($iadesayaclist as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $iadesayaclar;
                                    $depoteslim->sayacsayisi = count($iadesayaclist);
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 2;
                                    $depoteslim->subegonderim=$subedurum;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if ($onaydurum == 1)
                                BackendController::HatirlatmaGuncelle(6, $netsiscariid, $servisid, $sayacsayisi);
                            else
                                BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Yetkili Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                            DB::commit();
                            return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Onayı Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi', 'type' => 'error'));
                        }
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylama onaylananlara kaydedilemedi', 'type' => 'error'));
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getYetkiliredbilgi(){
        try {
            $id=Input::get('id');
            $ucretlendirilen = Ucretlendirilen::find($id);
            $ucretlendirilen->netsiscari = Netsiscari::find($ucretlendirilen->netsiscari_id);
            $ucretlendirilen->parabirimi = ParaBirimi::find($ucretlendirilen->parabirimi_id);
            $ucretlendirilen->parabirimi2 = ParaBirimi::find($ucretlendirilen->parabirimi2_id);
            $ucretlendirilen->servis = Servis::find($ucretlendirilen->servis_id);
            if ($ucretlendirilen->durum < 2)
                $secilenler = explode(',', $ucretlendirilen->secilenler);
            else
                $secilenler = explode(',', $ucretlendirilen->reddedilenler);
            $ucretlendirilen->arizafiyat = ArizaFiyat::whereIn('id', $secilenler)->get();
            foreach ($ucretlendirilen->arizafiyat as $arizafiyat) {
                $arizafiyat->sayacadi = SayacAdi::find($arizafiyat->sayacadi_id);
                $arizafiyat->sayaccap = SayacCap::find($arizafiyat->sayaccap_id);
                $arizafiyat->parabirimi = ParaBirimi::find($arizafiyat->parabirimi_id);
                $arizafiyat->parabirimi2 = ParaBirimi::find($arizafiyat->parabirimi2_id);
                $arizafiyat->dovizkuru = DovizKuru::where('tarih', $arizafiyat->kurtarihi)->orderBy('parabirimi_id', 'asc')->take(3)->get();
                if ($arizafiyat->kurtarihi != null)
                    $arizafiyat->kurtarihi = date('d-m-Y', strtotime($arizafiyat->kurtarihi));
                else
                    $arizafiyat->kurtarihi = "";
                foreach ($arizafiyat->dovizkuru as $doviz) {
                    $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
                }
            }
            return Response::json(array('durum' => true, 'ucretlendirilen' => $ucretlendirilen ));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error','title' => 'Geri Teslim Bilgisi Hatalı' ));
        }
    }

    public function postYetkilireddet(){
        try {
            DB::beginTransaction();
            $aciklama = Input::get('yetkiliredneden');
            $ucretlendirilenid = Input::get('yetkiliredid');
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            if($ucretlendirilen->durum!=0 && $ucretlendirilen->durum!=1 ){
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylama Tamamlanamadı!', 'text' => 'Ücretlendirme Zaten Onaylanmış ya da Reddedilmiş Olabilir!', 'type' => 'warning'));
            }
            $onaydurum=$ucretlendirilen->durum; // 0 bekliyor 1 mail gönderildi 2 onaylandı
            $servisid = $ucretlendirilen->servis_id;
            $netsiscariid = $ucretlendirilen->netsiscari_id;
            $secilenler = Input::get('yetkiliredsecilenler');
            $secilenlist = explode(',', $secilenler);
            $sayacsayisi = count($secilenlist);
            $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenlist)->get();
            $sayacgelenler = "";
            $ucretlendirilen->durum = 3;
            $ucretlendirilen->reddetmetarihi = date('Y-m-d H:i:s');
            $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
            $ucretlendirilen->tekrarkayittarihi = NULL;
            $ucretlendirilen->gerigonderimtarihi = NULL;
            $ucretlendirilen->musterinotu = $aciklama;
            $ucretlendirilen->reddedilenler = $secilenler;
            $ucretlendirilen->save();
            try {
                foreach ($arizafiyatlar as $arizafiyat) {
                    $arizafiyateski=new ArizaFiyatEski;
                    $arizafiyateski->arizafiyat_id=$arizafiyat->id;
                    $arizafiyateski->ariza_serino=$arizafiyat->ariza_serino;
                    $arizafiyateski->sayac_id=$arizafiyat->sayac_id;
                    $arizafiyateski->sayacadi_id=$arizafiyat->sayacadi_id;
                    $arizafiyateski->sayaccap_id=$arizafiyat->sayaccap_id;
                    $arizafiyateski->ariza_garanti=$arizafiyat->ariza_garanti;
                    $arizafiyateski->fiyatdurum=$arizafiyat->fiyatdurum;
                    $arizafiyateski->uretimyer_id=$arizafiyat->uretimyer_id;
                    $arizafiyateski->arizakayit_id=$arizafiyat->arizakayit_id;
                    $arizafiyateski->depogelen_id=$arizafiyat->depogelen_id;
                    $arizafiyateski->sayacgelen_id=$arizafiyat->sayacgelen_id;
                    $arizafiyateski->netsiscari_id=$arizafiyat->netsiscari_id;
                    $arizafiyateski->degisenler=$arizafiyat->degisenler;
                    $arizafiyateski->genel=$arizafiyat->genel;
                    $arizafiyateski->ozel=$arizafiyat->ozel;
                    $arizafiyateski->genelbirim=$arizafiyat->genelbirim;
                    $arizafiyateski->ozelbirim=$arizafiyat->ozelbirim;
                    $arizafiyateski->ucretsiz=$arizafiyat->ucretsiz;
                    $arizafiyateski->fiyat=$arizafiyat->fiyat;
                    $arizafiyateski->fiyat2=$arizafiyat->fiyat2;
                    $arizafiyateski->indirim=$arizafiyat->indirim;
                    $arizafiyateski->indirimorani=$arizafiyat->indirimorani;
                    $arizafiyateski->tutar=$arizafiyat->tutar;
                    $arizafiyateski->tutar2=$arizafiyat->tutar2;
                    $arizafiyateski->kdv=$arizafiyat->kdv;
                    $arizafiyateski->kdv2=$arizafiyat->kdv2;
                    $arizafiyateski->toplamtutar=$arizafiyat->toplamtutar;
                    $arizafiyateski->toplamtutar2=$arizafiyat->toplamtutar2;
                    $arizafiyateski->parabirimi_id=$arizafiyat->parabirimi_id;
                    $arizafiyateski->parabirimi2_id=$arizafiyat->parabirimi2_id;
                    $arizafiyateski->durum=$arizafiyat->durum;
                    $arizafiyateski->kullanici_id=$arizafiyat->kullanici_id;
                    $arizafiyateski->kayittarihi=$arizafiyat->kayittarihi;
                    $arizafiyateski->kurtarihi=$arizafiyat->kurtarihi;
                    $arizafiyateski->tekrarkayittarihi=$arizafiyat->tekrarkayittarihi;
                    $arizafiyateski->save();
                    $arizafiyat->durum = 2;
                    $arizafiyat->save();
                    $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                    $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                    $sayacgelen->fiyatlandirma = 0;
                    $sayacgelen->save();
                    $sayacgelenler .= ($sayacgelenler == "" ? "" : ",") . $sayacgelen->id;
                    $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                    $servistakip->durum = 6;
                    $servistakip->reddetmetarihi = date('Y-m-d H:i:s');
                    $servistakip->save();
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Reddetme Hatası', 'text' => 'Reddedilen Ücretlendirmeye ait Fiyatlar Güncellenemedi', 'type' => 'error'));
            }
            if ($onaydurum == 1)
                BackendController::HatirlatmaGuncelle(6, $netsiscariid, $servisid, $sayacsayisi);
            else
                BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
            BackendController::HatirlatmaEkle(7, $netsiscariid, $servisid, $sayacsayisi);
            BackendController::BildirimEkle(6, $netsiscariid, $servisid, $sayacsayisi);
            BackendController::IslemEkle(1, Auth::user()->id, 'label-danger', 'fa-minus-square-o', $ucretlendirilen->id . ' Numaralı Ücretlendirme Reddedildi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
            DB::commit();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Fiyatlandırma Reddedildi', 'text' => 'Sayaçların Fiyatlandırması Reddedildi.', 'type' => 'success', 'durum' => 1));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Reddetme Hatası', 'text' => 'Reddetme kaydedilemedi', 'type' => 'error'));
        }
    }

    public function postSubeyetkilionayla(){
        try {
            if (Input::has('subeonaymailvar')) //mail gönderilecek
            {
                $rules = ['onaysubeyetkilimail' => 'required|email'];
                $mail = 1;
                if (Input::has('detaylifiyatlandirma')) //mail gönderilecek
                {
                    $detaylifiyat=1;
                }else{
                    $detaylifiyat=0;
                }
            } else // mail gönderilmeyecek
            {
                $rules = [];
                $mail = 0;
                $detaylifiyat = 0;
            }
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $digermail = Input::get('onaymailcc');
            $digermaillist = explode(';', $digermail);
            if ($digermail != "")
                foreach ($digermaillist as $email) {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Diğer Mail Listesi Hatası', 'text' => $email . ' geçerli bir email değil. Birden fazla girilen mail adreslerini ; ile ayırınız!', 'type' => 'error'));
                    }
                }
            $ucretlendirilenid = Input::get('subeonayid');
            $secilenler = Input::get('subeonaysecilenler');
            $secilenlist = explode(',', $secilenler);
            $tumu = explode(',', Input::get('subeonaytumu'));
            $yetkilimail = Input::get('onaysubeyetkilimail');
            $yetkiliid = Auth::user()->id;
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            $birim =Input::get('subeonaybirim');
            $birim2 =Input::get('subeonaybirim2')=="" ? null : Input::get('subeonaybirim2');
            DB::beginTransaction();
            if($ucretlendirilen->durum==0){
                $yetkili = Yetkili::where('netsiscari_id',$ucretlendirilen->netsiscari_id)->where('kullanici_id',$yetkiliid)->where('aktif',1)->first();
            }else if($ucretlendirilen->durum==1){
                $yetkili = Yetkili::find($ucretlendirilen->yetkili_id);
            }else{
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı!', 'text' => 'Ücretlendirme Zaten Onaylanmış !', 'type' => 'warning'));
            }
            if(!$yetkili){
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Yetkili Bilgisi Bulunamadı!', 'text' => 'Cari Bilgisine Ait Bu Kullanıcının Onaylama Yetkisi Yok!', 'type' => 'error'));
            }
            $onaydurum=$ucretlendirilen->durum; // 0 bekliyor 1 mail gönderildi 2 onaylandı
            $servisid = $ucretlendirilen->servis_id;
            $uretimyerid = $ucretlendirilen->uretimyer_id;
            $uretimyer = UretimYer::find($uretimyerid);
            $netsiscariid = $ucretlendirilen->netsiscari_id;
            $sayacsayisi = count($secilenlist);
            $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenlist)->get();
            $servistakipler = ServisTakip::whereIn('arizafiyat_id', $secilenlist)->get();
            $sube = Sube::where('netsiscari_id', $netsiscariid)->where('aktif', 1)->first();
            $netsiscari = NetsisCari::find($netsiscariid);
            $subedurum = 0;
            if($sube){
                foreach ($servistakipler as $servistakip){
                    if($servistakip->subedurum)
                        $subedurum = 1;
                }
            }
            $kullanici=Kullanici::find($yetkiliid);
            $ilkmail = $kullanici->ilkmail;
            $girisadi = $kullanici->girisadi;
            $takipno = $netsiscari->takipno;
            if (count($secilenlist) == count($tumu)) //ucretlendirmedeki tum sayaçlar onaylanmış
            {
                if ($mail) {
                    $fiyatlandirma = BackendController::getFiyatlandirma($ucretlendirilenid);
                    if (is_null($fiyatlandirma)){
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                    }
                    if($detaylifiyat){
                        $detaylifiyatlandirma = BackendController::getDetaylifiyatlandirma($ucretlendirilenid);
                        if (is_null($detaylifiyatlandirma)){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Detaylı Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                        }
                    }
                }
                try {
                    $ucretlendirilen->durum = 2;
                    $ucretlendirilen->onaytipi = 3;
                    $ucretlendirilen->yetkili_id=$yetkili->id;
                    $ucretlendirilen->onaytarihi = date('Y-m-d H:i:s');
                    $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                    $ucretlendirilen->save();
                    $onaylanan = new Onaylanan;
                    $onaylanan->servis_id = $servisid;
                    $onaylanan->uretimyer_id = $uretimyerid;
                    $onaylanan->netsiscari_id = $netsiscariid;
                    $onaylanan->ucretlendirilen_id = $ucretlendirilen->id;
                    $onaylanan->yetkili_id = $yetkili->id;
                    $onaylanan->onaytarihi = date('Y-m-d H:i:s');
                    $onaylanan->onaylamatipi = 3;
                    $onaylanan->save();
                    $secilensayaclar = "";
                    $secilensayaclist = array();
                    $garantisayaclar = "";
                    $garantisayaclist = array();
                    $iadesayaclar = "";
                    $iadesayaclist = array();
                    try {
                        foreach ($arizafiyatlar as $arizafiyat) {
                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                            $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                            if($arizakayit->arizakayit_durum==7){
                                array_push($iadesayaclist, $sayacgelen->id);
                                $iadesayaclar .= ($iadesayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0) {
                                array_push($secilensayaclist, $sayacgelen->id);
                                $secilensayaclar .= ($secilensayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }else{
                                array_push($garantisayaclist, $sayacgelen->id);
                                $garantisayaclar .= ($garantisayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }
                            $sayacgelen->musterionay = 1;
                            $sayacgelen->teslimdurum = 1;
                            $sayacgelen->save();
                            $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                            $servistakip->onaylanan_id = $onaylanan->id;
                            $servistakip->durum = 5;
                            $servistakip->onaylanmatarihi = date('Y-m-d H:i:s');
                            $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                            $servistakip->kullanici_id = Auth::user()->id;
                            $servistakip->save();
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Kaydedilemedi', 'text' => 'Sayaç Gelen ve Servis Takip Bilgisi Güncellenemedi', 'type' => 'error'));
                    }
                    try {
                        if ($mail) {
                            try {
                                Mail::send('mail.bilgilendirme', array('ucretlendirilen' => $ucretlendirilen, 'ilkmail' => $ilkmail, 'girisadi' => $girisadi,'takipno'=>$takipno),
                                    function ($message) use ($yetkili, $yetkilimail, $uretimyer, $digermaillist, $digermail,$detaylifiyat) {
                                        //$message->cc(Auth::user()->mail,Auth::user()->adi_soyadi);
                                        if ($digermail != "")
                                            foreach ($digermaillist as $email)
                                                $message->cc($email);
                                        $message->to($yetkilimail, 'Yetkili')->subject("Manas ServisTakip Fiyatlandırma Bilgilendirme");
                                        $message->attach(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                        if($detaylifiyat){
                                            $message->attach(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                        }
                                    });
                                $ucretlendirilen->mail = 1;
                                $ucretlendirilen->save();
                                $kullanici->ilkmail = 1;
                                $kullanici->save();
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => 'Mail Gönderirken Hata Oluştu', 'type' => 'error'));
                            }
                            if (count(Mail::failures()) > 0) {
                                DB::rollBack();
                                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => 'Mail Gönderirken Hata Oluştu', 'type' => 'error'));
                            } else {
                                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                            }
                        } else {
                            File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                            if($detaylifiyat){
                                File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                            }
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                    }
                    if ($servisid == 5) //kalibrasyon tamamlanmışsa depo teslim yoksa bekleyecek
                    {
                        try {
                            $kalibrasyonsayi=0;
                            $kalibrasyonsayaclist="";
                            $garantisayi=0;
                            $garantisayaclist="";
                            $geriiadesayi=0;
                            $geriiadesayaclist="";
                            $periyodiksayi=0;
                            $periyodiklist="";
                            foreach ($arizafiyatlar as $arizafiyat) {
                                $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                                $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                $depogelen=DepoGelen::find($sayacgelen->depogelen_id);
                                if($arizakayit->arizakayit_durum != 3) { //yedek parça bekliyorsa depo teslim olmaz
                                    if ($depogelen->periyodik) {
                                        if ($sayacgelen->kalibrasyon) {
                                            $periyodiksayi++;
                                            $periyodiklist .= ($periyodiklist == "" ? "" : ",") . $sayacgelen->id;
                                        } else {
                                            $sayacgelen->teslimdurum = 0;
                                            $sayacgelen->save();
                                        }
                                    } else {
                                        if ($arizakayit->arizakayit_durum == 7) {
                                            $geriiadesayi++;
                                            $geriiadesayaclist .= ($geriiadesayaclist == "" ? "" : ",") . $sayacgelen->id;
                                        } else if ($sayacgelen->kalibrasyon) {
                                            if ($arizafiyat->toplamtutar > 0 || $arizafiyat->toplamtutar2 > 0) {
                                                $kalibrasyonsayi++;
                                                $kalibrasyonsayaclist .= ($kalibrasyonsayaclist == "" ? "" : ",") . $sayacgelen->id;
                                            } else {
                                                $garantisayi++;
                                                $garantisayaclist .= ($garantisayaclist == "" ? "" : ",") . $sayacgelen->id;
                                            }
                                        } else {
                                            $sayacgelen->teslimdurum = 0;
                                            $sayacgelen->save();
                                        }
                                    }
                                }else{
                                    $sayacgelen->teslimdurum = 0;
                                    $sayacgelen->save();
                                }
                            }
                            if($kalibrasyonsayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $kalibrasyonsayaclar = explode(',', $kalibrasyonsayaclist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($kalibrasyonsayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $kalibrasyonsayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $kalibrasyonsayaclist;
                                    $depoteslim->sayacsayisi = $kalibrasyonsayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if($garantisayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum',0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $garantisayaclar = explode(',', $garantisayaclist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($garantisayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $garantisayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $garantisayaclist;
                                    $depoteslim->sayacsayisi = $garantisayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 1;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if($geriiadesayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum',0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $geriiadesayaclar = explode(',', $geriiadesayaclist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($geriiadesayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $geriiadesayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $geriiadesayaclist;
                                    $depoteslim->sayacsayisi = $geriiadesayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 2;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if($periyodiksayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum',0)->where('tipi',0)->where('periyodik',1)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $periyodiksayaclar = explode(',', $periyodiklist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($periyodiksayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $periyodiksayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $periyodiklist;
                                    $depoteslim->sayacsayisi = $periyodiksayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->periyodik = 1;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi.', 'type' => 'error'));
                        }
                        if ($onaydurum == 1)
                            BackendController::HatirlatmaGuncelle(6, $netsiscariid, $servisid, $sayacsayisi);
                        else
                            BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                        BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                        if(($kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi)>0)
                            BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi);
                        BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Yetkili Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                        DB::commit();
                        return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Onayı Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                    } else if ($servisid == 6)//abone teslim
                    {
                        // abone yoksa onaylama izin verme
                        $sayacgelenler = SayacGelen::whereIn('id', $secilensayaclist)->get(array('serino'));
                        $abonesayaclar = AboneSayac::whereIn('serino', $sayacgelenler->toArray())->get(array('id')); //sayacgelenler array değil gibi geliyor
                        if ($abonesayaclar->count() == $sayacsayisi) { //ücretlendirilen sayaçların hepsi mevcut
                            $abonetahsisler = AboneTahsis::whereIn('abonesayac_id', $abonesayaclar->toArray())->get();
                            if ($abonetahsisler->count() == $sayacsayisi) {//sayaçların hepsi tahsisliyse
                                try {
                                    $aboneteslimlist = array();
                                    foreach ($abonetahsisler as $abonetahsis) {
                                        $abone = Abone::find($abonetahsis->abone_id);
                                        $abonesayac = AboneSayac::find($abonetahsis->abonesayac_id);
                                        $abonesayacgelen = SayacGelen::whereIn('id', $secilensayaclist)->where('serino', $abonesayac->serino)->first();
                                        try {
                                            $aboneteslim = AboneTeslim::where('abone_id', $abone->id)->where('teslimdurum', 0)->first();
                                            if ($aboneteslim) { //teslim edilmemiş o kişiye ait sayaç varsa
                                                $aboneteslim->secilenler .= ',' . $abonesayacgelen->id;
                                                $aboneteslim->sayacsayisi += 1;
                                                $aboneteslim->parabirimi2_id = $aboneteslim->parabirimi2_id == null ? $birim2 : $aboneteslim->parabirimi2_id;
                                                $aboneteslim->save();
                                                array_push($aboneteslimlist, $aboneteslim->id);
                                            } else { // yeni abone teslimi
                                                $aboneteslim = new AboneTeslim;
                                                $aboneteslim->abone_id = $abone->id;
                                                $aboneteslim->uretimyer_id = $abone->uretimyer_id;
                                                $aboneteslim->netsiscari_id = $abone->netsiscari_id;
                                                $aboneteslim->secilenler = $abonesayacgelen->id;
                                                $aboneteslim->sayacsayisi = 1;
                                                $aboneteslim->teslimdurum = 0;
                                                $aboneteslim->subekodu = $abone->subekodu;
                                                $aboneteslim->parabirimi_id = $birim;
                                                $aboneteslim->parabirimi2_id = $birim2;
                                                $aboneteslim->save();
                                                array_push($aboneteslimlist, $aboneteslim->id);
                                            }
                                        } catch (Exception $e) {
                                            Log::error($e);
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı', 'text' => 'Ücretlendirilen sayaçlar abone teslimine kaydedilemedi!', 'type' => 'error'));
                                        }
                                    }
                                } catch (Exception $e) {
                                    Log::error($e);
                                    DB::rollBack();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı abone teslimine kaydedilemedi!', 'type' => 'error'));
                                }
                                if ($onaydurum == 1)
                                    BackendController::HatirlatmaGuncelle(6, $netsiscariid, $servisid, $sayacsayisi);
                                else
                                    BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::HatirlatmaEkle(12, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Yetkili Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                                DB::commit();
                                return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Onayı Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                            } else {//tahsisli olmayan sayaç mevcut
                                DB::rollBack();
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı aboneye tahsisli değil. Önce tahsis işlemlerinin tamamlanması gerekiyor!', 'type' => 'error'));
                            }
                        } else {//listede olmayan sayaç mevcut
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı aboneye tahsisli değil. Önce tahsis işlemlerinin tamamlanması gerekiyor!', 'type' => 'error'));
                        }
                    } else {
                        try {
                            if(count($secilensayaclist)>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($secilensayaclist as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $secilensayaclar;
                                    $depoteslim->sayacsayisi = count($secilensayaclist);
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->subegonderim=$subedurum;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if(count($garantisayaclist)>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($garantisayaclist as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $garantisayaclar;
                                    $depoteslim->sayacsayisi = count($garantisayaclist);
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 1;
                                    $depoteslim->subegonderim=$subedurum;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if(count($iadesayaclist)>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($iadesayaclist as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $iadesayaclar;
                                    $depoteslim->sayacsayisi = count($iadesayaclist);
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 2;
                                    $depoteslim->subegonderim=$subedurum;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if ($onaydurum == 1)
                                BackendController::HatirlatmaGuncelle(6, $netsiscariid, $servisid, $sayacsayisi);
                            else
                                BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Yetkili Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                            DB::commit();
                            return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Onayı Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi', 'type' => 'error'));
                        }
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylama onaylananlara kaydedilemedi', 'type' => 'error'));
                }
            } else { //bazıları onaylanmış
                try {
                    $yeniucretlendirilen = new Ucretlendirilen;
                    $yeniucretlendirilen->servis_id = $servisid;
                    $yeniucretlendirilen->uretimyer_id = $uretimyerid;
                    $yeniucretlendirilen->netsiscari_id = $netsiscariid;
                    $yeniucretlendirilen->secilenler = $secilenler;
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
                            if ($yeniparabirimi === "1") { //tl
                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                            } else { //euro dolar sterln
                                if ($arizafiyat->parabirimi_id === "1") {
                                    $kur = 1 / BackendController::getKurBilgisi($yeniparabirimi, $kurtarih);
                                } else {
                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($yeniparabirimi, $kurtarih);
                                }
                            }
                        }
                        $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                        if($yeniparabirimi2===null){
                            if($arizafiyat->parabirimi2_id!==null){
                                if($arizafiyat->parabirimi2_id===$yeniparabirimi){
                                    $arizafiyat->fiyat +=$arizafiyat->fiyat2;
                                    $arizafiyat->fiyat2=0;
                                    $arizafiyat->parabirimi2_id=null;
                                }else{
                                    $yeniparabirimi2=$arizafiyat->parabirimi2_id;
                                }
                            }
                        }else{
                            if($arizafiyat->parabirimi2_id!=null){
                                if($arizafiyat->parabirimi2_id===$yeniparabirimi){
                                    $arizafiyat->fiyat +=$arizafiyat->fiyat2;
                                    $arizafiyat->fiyat2=0;
                                    $arizafiyat->parabirimi2_id=null;
                                }else if($arizafiyat->parabirimi2_id!==$yeniparabirimi2){
                                    DB::rollBack();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Yapılamadı', 'text' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
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
                    $yeniucretlendirilen->sayacsayisi = $sayacsayisi;
                    $yeniucretlendirilen->fiyat = $yenifiyat;
                    $yeniucretlendirilen->fiyat2 = $yenifiyat2;
                    $yeniucretlendirilen->parabirimi_id = $yeniparabirimi;
                    $yeniucretlendirilen->parabirimi2_id = $yeniparabirimi2;
                    $yeniucretlendirilen->kullanici_id = Auth::user()->id;
                    $yeniucretlendirilen->kayittarihi = date('Y-m-d H:i:s');
                    $yeniucretlendirilen->kurtarihi = $ucretlendirilen->kurtarihi;
                    $yeniucretlendirilen->durum = 2;
                    $yeniucretlendirilen->onaytipi = 3;
                    $yeniucretlendirilen->onaytarihi = date('Y-m-d H:i:s');
                    $yeniucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                    $yeniucretlendirilen->yetkili_id=$yetkili->id;
                    $yeniucretlendirilen->save();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Yapılamadı', 'text' => 'Seçilen Sayaçlara ait Ücretlendirme Kaydedilemedi', 'type' => 'error'));
                }
                DB::commit();
                if ($mail) {
                    $fiyatlandirma = BackendController::getFiyatlandirma($yeniucretlendirilen->id);
                    if (is_null($fiyatlandirma)) {
                        $yeniucretlendirilen->delete();
                        DB::commit();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                    }
                    if($detaylifiyat){
                        $detaylifiyatlandirma = BackendController::getDetaylifiyatlandirma($yeniucretlendirilen->id);
                        if (is_null($detaylifiyatlandirma)){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Detaylı Fiyatlandırma Tablosu Oluşturulamadı', 'type' => 'error'));
                        }
                    }
                }
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
                    if ($kalanparabirimi !== $arizafiyat->parabirimi_id) {
                        if ($kalanparabirimi === "1") { //tl
                            $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                        } else { //euro dolar sterln
                            if ($arizafiyat->parabirimi_id === "1") {
                                $kur = 1 / BackendController::getKurBilgisi($kalanparabirimi, $kurtarih);
                            } else {
                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($kalanparabirimi, $kurtarih);
                            }
                        }
                    }
                    $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                    if ($kalanparabirimi2 === null) {
                        if ($arizafiyat->parabirimi2_id !== null) {
                            if ($arizafiyat->parabirimi2_id === $kalanparabirimi) {
                                $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                $arizafiyat->fiyat2 = 0;
                                $arizafiyat->parabirimi2_id = null;
                            } else {
                                $kalanparabirimi2 = $arizafiyat->parabirimi2_id;
                            }
                        }
                    } else {
                        if ($arizafiyat->parabirimi2_id !== null) {
                            if ($arizafiyat->parabirimi2_id === $kalanparabirimi) {
                                $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                $arizafiyat->fiyat2 = 0;
                                $arizafiyat->parabirimi2_id = null;
                            } else if ($arizafiyat->parabirimi2_id !== $kalanparabirimi2) {
                                DB::rollBack();
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Yapılamadı', 'text' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
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
                try {
                    $ucretlendirilen->secilenler = $kalanlar;
                    $ucretlendirilen->garanti = $kalangaranti;
                    $ucretlendirilen->sayacsayisi = $kalansayi;
                    $ucretlendirilen->fiyat = $kalanfiyat;
                    $ucretlendirilen->fiyat2 = $kalanfiyat2;
                    $ucretlendirilen->parabirimi_id = $kalanparabirimi;
                    $ucretlendirilen->parabirimi2_id = $kalanparabirimi2;
                    $ucretlendirilen->save();
                    $onaylanan = new Onaylanan;
                    $onaylanan->servis_id = $servisid;
                    $onaylanan->uretimyer_id = $uretimyerid;
                    $onaylanan->netsiscari_id = $netsiscariid;
                    $onaylanan->ucretlendirilen_id = $yeniucretlendirilen->id;
                    $onaylanan->yetkili_id = $yetkili->id;
                    $onaylanan->onaytarihi = date('Y-m-d H:i:s');
                    $onaylanan->onaylamatipi = 3;
                    $onaylanan->save();
                    $secilensayaclar = "";
                    $secilensayaclist = array();
                    $garantisayaclar = "";
                    $garantisayaclist = array();
                    $iadesayaclar = "";
                    $iadesayaclist = array();
                    try {
                        foreach ($arizafiyatlar as $arizafiyat) {
                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                            $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                            if($arizakayit->arizakayit_durum==7){
                                array_push($iadesayaclist, $sayacgelen->id);
                                $iadesayaclar .= ($iadesayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0) {
                                array_push($secilensayaclist, $sayacgelen->id);
                                $secilensayaclar .= ($secilensayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }else{
                                array_push($garantisayaclist, $sayacgelen->id);
                                $garantisayaclar .= ($garantisayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }
                            $sayacgelen->musterionay = 1;
                            $sayacgelen->teslimdurum = 1;
                            $sayacgelen->save();
                            $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                            $servistakip->ucretlendirilen_id = $yeniucretlendirilen->id;
                            $servistakip->onaylanan_id = $onaylanan->id;
                            $servistakip->durum = 5;
                            $servistakip->onaylanmatarihi = date('Y-m-d H:i:s');
                            $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                            $servistakip->kullanici_id = Auth::user()->id;
                            $servistakip->save();
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Kaydedilemedi', 'text' => 'Sayaç Gelen ve Servis Takip Bilgisi Güncellenemedi', 'type' => 'error'));
                    }
                    try {
                        if ($mail) {
                            try {
                                Mail::send('mail.bilgilendirme', array('ucretlendirilen' => $yeniucretlendirilen, 'ilkmail' => $ilkmail, 'girisadi' => $girisadi,'takipno'=>$takipno),
                                    function ($message) use ($yetkili, $yetkilimail, $uretimyer, $digermaillist, $digermail,$detaylifiyat) {
                                        //$message->cc(Auth::user()->mail,Auth::user()->adi_soyadi);
                                        if ($digermail != "")
                                            foreach ($digermaillist as $email)
                                                $message->cc($email);
                                        $message->to($yetkilimail, 'Yetkili')->subject("Manas ServisTakip Fiyatlandırma Bilgilendirme");
                                        $message->attach(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                        if($detaylifiyat){
                                            $message->attach(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                        }
                                    });
                                $yeniucretlendirilen->mail = 1;
                                $yeniucretlendirilen->save();
                                $kullanici->ilkmail = 1;
                                $kullanici->save();
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => 'Mail Gönderirken Hata Oluştu', 'type' => 'error'));
                            }
                            if (count(Mail::failures()) > 0) {
                                DB::rollBack();
                                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => 'Mail Gönderirken Hata Oluştu', 'type' => 'error'));
                            } else {
                                File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                if($detaylifiyat){
                                    File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                                }
                            }
                        } else {
                            File::delete(public_path('reports/outputs/fiyatlandirma/'.'Fiyatlandirma_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                            if($detaylifiyat){
                                File::delete(public_path('reports/outputs/fiyatraporu/'.'Fiyat_Raporu_' . Str::slug($uretimyer->yeradi) . '.pdf'));
                            }
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                    }
                    if ($servisid == 5) //kalibrasyon tamamlanmışsa depo teslim yoksa bekleyecek
                    {
                        try {
                            $kalibrasyonsayi=0;
                            $kalibrasyonsayaclist="";
                            $garantisayi=0;
                            $garantisayaclist="";
                            $geriiadesayi=0;
                            $geriiadesayaclist="";
                            $periyodiksayi=0;
                            $periyodiklist="";
                            foreach ($arizafiyatlar as $arizafiyat) {
                                $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                                $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                $depogelen=DepoGelen::find($sayacgelen->depogelen_id);
                                if($arizakayit->arizakayit_durum != 3) { //yedek parça bekliyorsa depo teslim olmaz
                                    if ($depogelen->periyodik) {
                                        if ($sayacgelen->kalibrasyon) {
                                            $periyodiksayi++;
                                            $periyodiklist .= ($periyodiklist == "" ? "" : ",") . $sayacgelen->id;
                                        } else {
                                            $sayacgelen->teslimdurum = 0;
                                            $sayacgelen->save();
                                        }
                                    } else {
                                        if ($arizakayit->arizakayit_durum == 7) {
                                            $geriiadesayi++;
                                            $geriiadesayaclist .= ($geriiadesayaclist == "" ? "" : ",") . $sayacgelen->id;
                                        } else if ($sayacgelen->kalibrasyon) {
                                            if ($arizafiyat->toplamtutar > 0 || $arizafiyat->toplamtutar2 > 0) {
                                                $kalibrasyonsayi++;
                                                $kalibrasyonsayaclist .= ($kalibrasyonsayaclist == "" ? "" : ",") . $sayacgelen->id;
                                            } else {
                                                $garantisayi++;
                                                $garantisayaclist .= ($garantisayaclist == "" ? "" : ",") . $sayacgelen->id;
                                            }
                                        } else {
                                            $sayacgelen->teslimdurum = 0;
                                            $sayacgelen->save();
                                        }
                                    }
                                }else{
                                    $sayacgelen->teslimdurum = 0;
                                    $sayacgelen->save();
                                }
                            }
                            if($kalibrasyonsayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $kalibrasyonsayaclar = explode(',', $kalibrasyonsayaclist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($kalibrasyonsayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $kalibrasyonsayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $kalibrasyonsayaclist;
                                    $depoteslim->sayacsayisi = $kalibrasyonsayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                            if($garantisayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum',0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $garantisayaclar = explode(',', $garantisayaclist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($garantisayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $garantisayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $garantisayaclist;
                                    $depoteslim->sayacsayisi = $garantisayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 1;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                            if($geriiadesayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum',0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $geriiadesayaclar = explode(',', $geriiadesayaclist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($geriiadesayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $geriiadesayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $geriiadesayaclist;
                                    $depoteslim->sayacsayisi = $geriiadesayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 2;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                            if($periyodiksayi>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum',0)->where('tipi',0)->where('periyodik',1)->where('subegonderim',0)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $periyodiksayaclar = explode(',', $periyodiklist);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($periyodiksayaclar as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    $periyodiksayi = $adet;
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $periyodiklist;
                                    $depoteslim->sayacsayisi = $periyodiksayi;
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->periyodik = 1;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi.', 'type' => 'error'));
                        }
                        if ($onaydurum == 1)
                            BackendController::HatirlatmaGuncelle(6, $netsiscariid, $servisid, $sayacsayisi);
                        else
                            BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                        BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                        if(($kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi)>0)
                            BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi);
                        BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Yetkili Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                        DB::commit();
                        return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Onayı Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                    } else if ($servisid == 6)//abone teslim
                    {
                        // abone yoksa onaylama izin verme
                        $sayacgelenler = SayacGelen::whereIn('id', $secilensayaclist)->get(array('serino'));
                        $abonesayaclar = AboneSayac::whereIn('serino', $sayacgelenler->toArray())->get(array('id')); //sayacgelenler array değil gibi geliyor
                        if ($abonesayaclar->count() == $sayacsayisi) { //ücretlendirilen sayaçların hepsi mevcut
                            $abonetahsisler = AboneTahsis::whereIn('abonesayac_id', $abonesayaclar->toArray())->get();
                            if ($abonetahsisler->count() == $sayacsayisi) {//sayaçların hepsi tahsisliyse
                                try {
                                    $aboneteslimlist = array();
                                    foreach ($abonetahsisler as $abonetahsis) {
                                        $abone = Abone::find($abonetahsis->abone_id);
                                        $abonesayac = AboneSayac::find($abonetahsis->abonesayac_id);
                                        $abonesayacgelen = SayacGelen::whereIn('id', $secilensayaclist)->where('serino', $abonesayac->serino)->first();
                                        try {
                                            $aboneteslim = AboneTeslim::where('abone_id', $abone->id)->where('teslimdurum', 0)->first();
                                            if ($aboneteslim) { //teslim edilmemiş o kişiye ait sayaç varsa
                                                $aboneteslim->secilenler .= ',' . $abonesayacgelen->id;
                                                $aboneteslim->sayacsayisi += 1;
                                                $aboneteslim->parabirimi2_id = $aboneteslim->parabirimi2_id==null ? $yeniparabirimi2 : $aboneteslim->parabirimi2_id;
                                                $aboneteslim->save();
                                                array_push($aboneteslimlist, $aboneteslim->id);
                                            } else { // yeni abone teslimi
                                                $aboneteslim = new AboneTeslim;
                                                $aboneteslim->abone_id = $abone->id;
                                                $aboneteslim->uretimyer_id = $abone->uretimyer_id;
                                                $aboneteslim->netsiscari_id = $abone->netsiscari_id;
                                                $aboneteslim->secilenler = $abonesayacgelen->id;
                                                $aboneteslim->sayacsayisi = 1;
                                                $aboneteslim->teslimdurum = 0;
                                                $aboneteslim->subekodu = $abone->subekodu;
                                                $aboneteslim->parabirimi_id = $yeniparabirimi;
                                                $aboneteslim->parabirimi2_id = $yeniparabirimi2;
                                                $aboneteslim->save();
                                                array_push($aboneteslimlist, $aboneteslim->id);
                                            }
                                        } catch (Exception $e) {
                                            Log::error($e);
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı', 'text' => 'Ücretlendirilen sayaçlar abone teslimine kaydedilemedi!', 'type' => 'error'));
                                        }
                                    }
                                } catch (Exception $e) {
                                    Log::error($e);
                                    DB::rollBack();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı abone teslimine kaydedilemedi!', 'type' => 'error'));
                                }
                                if ($onaydurum == 1)
                                    BackendController::HatirlatmaGuncelle(6, $netsiscariid, $servisid, $sayacsayisi);
                                else
                                    BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::HatirlatmaEkle(12, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Yetkili Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                                DB::commit();
                                return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Onayı Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                            } else {//tahsisli olmayan sayaç mevcut
                                DB::rollBack();
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı aboneye tahsisli değil. Önce tahsis işlemlerinin tamamlanması gerekiyor!', 'type' => 'error'));
                            }
                        } else {//listede olmayan sayaç mevcut
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Onaylanamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı aboneye tahsisli değil. Önce tahsis işlemlerinin tamamlanması gerekiyor!', 'type' => 'error'));
                        }
                    } else {
                        try {
                            if(count($secilensayaclist)>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($secilensayaclist as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $secilensayaclar;
                                    $depoteslim->sayacsayisi = count($secilensayaclist);
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->subegonderim=$subedurum;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                            if(count($garantisayaclist)>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($garantisayaclist as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $garantisayaclar;
                                    $depoteslim->sayacsayisi = count($garantisayaclist);
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 1;
                                    $depoteslim->subegonderim=$subedurum;
                                    $depoteslim->parabirimi_id=$yeniparabirimi;
                                    $depoteslim->parabirimi2_id=$yeniparabirimi2;
                                    $depoteslim->save();
                                }
                            }
                            if(count($iadesayaclist)>0){
                                $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                    ->where('depodurum', 0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                {
                                    $secilenlist = explode(',', $depoteslim->secilenler);
                                    $secilenler="";
                                    $adet = 0;
                                    foreach($iadesayaclist as $sayacgelenid){
                                        if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                            $adet++;
                                        }
                                    }
                                    if ($adet>0) {
                                        $depoteslim->secilenler .= ',' . $secilenler;
                                        $depoteslim->sayacsayisi += $adet;
                                        $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $yeniparabirimi2 : $depoteslim->parabirimi2_id;
                                        $depoteslim->save();
                                    }
                                } else { //yeni depo teslimatı yapılacak
                                    $depoteslim = new DepoTeslim;
                                    $depoteslim->servis_id = $servisid;
                                    $depoteslim->netsiscari_id = $netsiscariid;
                                    $depoteslim->secilenler = $iadesayaclar;
                                    $depoteslim->sayacsayisi = count($iadesayaclist);
                                    $depoteslim->depodurum = 0;
                                    $depoteslim->tipi = 2;
                                    $depoteslim->subegonderim=$subedurum;
                                    $depoteslim->parabirimi_id=$birim;
                                    $depoteslim->parabirimi2_id=$birim2;
                                    $depoteslim->save();
                                }
                            }
                            if ($onaydurum == 1)
                                BackendController::HatirlatmaGuncelle(6, $netsiscariid, $servisid, $sayacsayisi);
                            else
                                BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Yetkili Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                            DB::commit();
                            return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Onayı Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi', 'type' => 'error'));
                        }
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylama onaylananlara kaydedilemedi', 'type' => 'error'));
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getKayittipi(){
        try {
            $tip=Input::get('tip');
            $netsiscarilist = Auth::user()->netsiscarilist;
            $servisid=BackendController::getKullaniciServis();
            if($netsiscarilist!="" && $servisid){
                $netsiscari_id=explode(',',$netsiscarilist);
                if($tip==1){
                    /** @noinspection PhpMethodParametersCountMismatchInspection */
                    $kriter = NetsisCari::whereIn('netsiscari.id',$netsiscari_id)->where(function($query) use($servisid){ $query->whereIn('sayacgelen.servis_id',$servisid)->orWhere('sayacgelen.teslimdurum',4);})
                        ->where(function($query){ $query->where('arizafiyat.durum',0)->orWhere('arizafiyat.durum',2);})
                        ->select(array("netsiscari.id","netsiscari.cariadi","uretimyer.yeradi"))
                        ->leftjoin("arizafiyat", "arizafiyat.netsiscari_id", "=", "netsiscari.id")
                        ->leftjoin("uretimyer", "arizafiyat.uretimyer_id", "=", "uretimyer.id")
                        ->leftjoin("sayacadi", "arizafiyat.sayacadi_id", "=", "sayacadi.id")
                        ->leftjoin("sayacgelen", "arizafiyat.sayacgelen_id", "=", "sayacgelen.id")
                        ->groupBy(array("netsiscari.id","netsiscari.cariadi","uretimyer.yeradi"))->get(array("netsiscari.id","netsiscari.cariadi","uretimyer.yeradi"))->toArray();
                }else{
                    /** @noinspection PhpMethodParametersCountMismatchInspection */
                    $kriter = NetsisCari::whereIn('netsiscari.id',$netsiscari_id)->where(function($query) use($servisid){ $query->whereIn('sayacgelen.servis_id',$servisid)->orWhere('sayacgelen.teslimdurum',4);})
                        ->where(function($query){ $query->where('arizafiyat.durum',0)->orWhere('arizafiyat.durum',2);})
                        ->select(array("netsiscari.id","netsiscari.cariadi","uretimyer.yeradi"))
                        ->leftjoin("arizafiyat", "arizafiyat.netsiscari_id", "=", "netsiscari.id")
                        ->leftjoin("uretimyer", "arizafiyat.uretimyer_id", "=", "uretimyer.id")
                        ->leftjoin("sayacadi", "arizafiyat.sayacadi_id", "=", "sayacadi.id")
                        ->leftjoin("sayacgelen", "arizafiyat.sayacgelen_id", "=", "sayacgelen.id")
                        ->groupBy(array("netsiscari.id","netsiscari.cariadi","uretimyer.yeradi"))->get(array("netsiscari.id","netsiscari.cariadi","uretimyer.yeradi"))->toArray();
                }
            }else{
                if($tip==1){
                    /** @noinspection PhpMethodParametersCountMismatchInspection */
                    $idler=ArizaFiyat::where(function($query){ $query->where('arizafiyat.durum',0)->orWhere('arizafiyat.durum',2);})->groupBy('netsiscari_id')->get(array('netsiscari_id'))->toArray();
                    $kriter=NetsisCari::whereIn('id',$idler)->get();
                }else{
                    /** @noinspection PhpMethodParametersCountMismatchInspection */
                    $idler=ArizaFiyat::where(function($query){ $query->where('arizafiyat.durum',0)->orWhere('arizafiyat.durum',2);})->groupBy('uretimyer_id')->get(array('uretimyer_id'))->toArray();
                    $kriter=UretimYer::whereIn('id',$idler)->get();
                }
            }
            return Response::json(array('durum' => 1, 'kriter' => $kriter));

        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => 0, 'title' => 'Sayaç Bilgisi Bulunamadı', 'text' =>str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getTopluucretlendirmelistesi(){
        try {
            $kayittipi=Input::get('kayittipi');
            $kayitkriteri=Input::get('kayitkriteri');
            $servisid=BackendController::getKullaniciServis(1);
            if($kayitkriteri!=""){
                $raporid=Input::get('kayitid');
                if($raporid!=""){
                    $arizakayitlar = ArizaKayit::where('servisraporu_id',$raporid)->get(array('id'))->toArray();
                }else{
                    $arizakayitlar = array();
                }
                if($kayittipi==1){
                    if(count($arizakayitlar)>0)
                        $ucretlendirme= ArizaFiyat::where('netsiscari_id',$kayitkriteri)->whereIn('arizakayit_id',$arizakayitlar)->where('durum',0)->get();
                    else
                        $ucretlendirme= ArizaFiyat::leftJoin('sayacgelen','arizafiyat.sayacgelen_id','=','sayacgelen.id')
                            ->where('arizafiyat.netsiscari_id',$kayitkriteri)->whereIn('sayacgelen.servis_id',$servisid)->where(function($query){ $query->where('arizafiyat.durum',0)->orWhere('arizafiyat.durum',2);})
                            ->get(array('arizafiyat.id','arizafiyat.uretimyer_id','arizafiyat.netsiscari_id','arizafiyat.ariza_serino','arizafiyat.ariza_garanti','arizafiyat.fiyatdurum','arizafiyat.sayacadi_id','arizafiyat.arizakayit_id','arizafiyat.indirimorani','arizafiyat.fiyat','arizafiyat.fiyat2','arizafiyat.tutar','arizafiyat.tutar2','arizafiyat.kdv','arizafiyat.kdv2','arizafiyat.toplamtutar','arizafiyat.toplamtutar2','arizafiyat.parabirimi_id','arizafiyat.parabirimi2_id'));
                }else{
                    if(count($arizakayitlar)>0)
                        $ucretlendirme= ArizaFiyat::where('uretimyer_id',$kayitkriteri)->whereIn('arizakayit_id',$arizakayitlar)->where('durum',0)->get();
                    else
                        $ucretlendirme= ArizaFiyat::leftJoin('sayacgelen','arizafiyat.sayacgelen_id','=','sayacgelen.id')
                            ->where('arizafiyat.uretimyer_id',$kayitkriteri)->whereIn('sayacgelen.servis_id',$servisid)->where(function($query){ $query->where('arizafiyat.durum',0)->orWhere('arizafiyat.durum',2);})
                            ->get(array('arizafiyat.id','arizafiyat.uretimyer_id','arizafiyat.netsiscari_id','arizafiyat.ariza_serino','arizafiyat.ariza_garanti','arizafiyat.fiyatdurum','arizafiyat.sayacadi_id','arizafiyat.arizakayit_id','arizafiyat.indirimorani','arizafiyat.fiyat','arizafiyat.fiyat2','arizafiyat.tutar','arizafiyat.tutar2','arizafiyat.kdv','arizafiyat.kdv2','arizafiyat.toplamtutar','arizafiyat.toplamtutar2','arizafiyat.parabirimi_id','arizafiyat.parabirimi2_id'));
                }
                $uretimyerid = 0;
                $parabirimi = null;
                $parabirimi2 = null;
                if($ucretlendirme->count()>0){
                    foreach ($ucretlendirme as $ucretlendirilen) {
                        if ($uretimyerid != 0 && $uretimyerid != $ucretlendirilen->uretimyer_id) {
                            return Response::json(array('durum' => false, 'title' => 'Ucretlendirme Hatası', 'text' => 'Seçilen sayaçlar farklı yerlere ait olamaz!', 'type'=>'warning'));
                        } else {
                            $uretimyerid = $ucretlendirilen->uretimyer_id;
                            $ucretlendirilen->sayacadi = SayacAdi::find($ucretlendirilen->sayacadi_id);
                            $ucretlendirilen->uretimyer = Uretimyer::find($ucretlendirilen->uretimyer_id);
                            $ucretlendirilen->uretimyer->parabirimi = ParaBirimi::find($ucretlendirilen->uretimyer->parabirimi_id);
                            $ucretlendirilen->parabirimi = ParaBirimi::find($ucretlendirilen->parabirimi_id);
                            if(is_null($ucretlendirilen->parabirimi2_id))
                                $ucretlendirilen->parabirimi2 = null;
                            else
                                $ucretlendirilen->parabirimi2 = ParaBirimi::find($ucretlendirilen->parabirimi2_id);
                            $ucretlendirilen->arizakayit = ArizaKayit::find($ucretlendirilen->arizakayit_id);
                            $ucretlendirilen->sayacgelen = SayacGelen::find($ucretlendirilen->arizakayit->sayacgelen_id);
                            $parabirimi=$ucretlendirilen->uretimyer->parabirimi;
                            if(!(is_null($ucretlendirilen->parabirimi2_id)))
                                if($parabirimi2!=null){
                                    if($parabirimi->id!=$ucretlendirilen->parabirimi2_id && $parabirimi2->id!=$ucretlendirilen->parabirimi2_id)
                                        return Response::json(array('durum' => false, 'title' => 'Hatalı Bilgi Mevcut', 'text' => 'İki Parabiriminden Fazla Para Birimi Kullanılamaz!', 'type'=>'error'));
                                }else{
                                    $parabirimi2=$ucretlendirilen->parabirimi2;
                                }
                        }
                    }
                    $dovizkuru = DovizKuru::orderBy('tarih', 'desc')->take(3)->get();
                    foreach ($dovizkuru as $doviz) {
                        $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
                    }
                    return Response::json(array('durum' => true, 'ucretlendirme' => $ucretlendirme,'dovizkuru'=>$dovizkuru,'parabirimi'=>$parabirimi,'parabirimi2'=>$parabirimi2 ));
                }else{
                    return Response::json(array('durum' => false, 'title' => 'Kritere Ait Kayıt Yok', 'text' => 'Kritere Ait Ücretlendirme Bulunmadı!', 'type'=>'warning'));
                }
            }else{
                return Response::json(array('durum' => false, 'title' => 'Kriter Hatası', 'text' => 'Kriter Boş Geçilmiş!', 'type'=>'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'title' => 'Hatalı Bilgi Mevcut', 'text' => str_replace("'","\'",$e->getMessage()), 'type'=>'error'));
        }
    }

    public function postTopluucretlendir()
    {
        try {
            $secilenler = Input::get('topluonizlemesecilenler');
            $secilenlist = explode(',', $secilenler);
            $toplamtutar = Input::get('topluonizlemetoplamtutar');
            $toplamtutar2 = Input::get('topluonizlemetoplamtutar2');
            $birim = Input::get('topluonizlemebirim');
            $birim2 = Input::get('topluonizlemebirim2')=="" ? null : Input::get('topluonizlemebirim2') ;
            $sayacsayisi = Input::get('topluonizlemeadet');
            $servisid = Input::get('topluonizlemeservis');
            $uretimyerid = Input::get('topluonizlemeuretimyer');
            $netsiscariid = Input::get('topluonizlemenetsiscari');
            $garanti = Input::get('topluonizlemegaranti');
            $kurtarih = date('Y-m-d', strtotime(Input::get('topluonizlemekurtarih')));
            $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenlist)->get();
            $tekrarucretlendirilenler = "";
            if ($servisid != "") {
                try {
                    DB::beginTransaction();
                    if (ArizaFiyat::whereIn('id', $secilenlist)->whereIn('durum', array(1, 3, 4))->get()->count() > 0) {
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Seçilen Sayaçlar Zaten Ücretlendirilmiş!', 'type' => 'warning'));
                    }
                    $servistakipler = ServisTakip::whereIn('arizafiyat_id', $secilenlist)->get();
                    $sube = Sube::where('netsiscari_id', $netsiscariid)->where('aktif', 1)->first();
                    $subedurum = 0;
                    if($sube){
                        foreach ($servistakipler as $servistakip){
                            if($servistakip->subedurum)
                                $subedurum = 1;
                        }
                    }
                    try {
                        $ucretred = Ucretlendirilen::where('servis_id', $servisid)->where('uretimyer_id', $uretimyerid)->where('netsiscari_id', $netsiscariid)
                            ->where('durum', 3)->whereNull('tekrarkayittarihi')->whereNull('gerigonderimtarihi')->orderBy('kayittarihi', 'desc')->first();
                        if ($ucretred) //reddedilmiş o yere ait ucretlendirme varsa
                        {
                            $reddedilenler = $ucretred->reddedilenler;
                            $reddedilenlist = explode(',', $reddedilenler);
                            $yenireddedilenler = "";
                            foreach ($reddedilenlist as $reddedilen) {
                                $flag = 0;
                                foreach ($secilenlist as $secilen) {
                                    if ($reddedilen == $secilen) {
                                        $flag = 1;
                                        break;
                                    }
                                }
                                if ($flag == 0) //reddedilen ucretlendirilmemiş
                                {
                                    $yenireddedilenler .= ($yenireddedilenler == "" ? "" : ",") . $reddedilen;
                                } else {
                                    $tekrarucretlendirilenler .= ($tekrarucretlendirilenler == "" ? "" : ",") . $reddedilen;
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
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Reddedilen Ücretlendirme Tekrar Ücretlendirilirken Hata Oluştu', 'type' => 'error'));
                    }
                    if ($garanti == '1') //garanti içinde ucretlendirilenler sistem tarafından onaylanır
                    {
                        try {
                            $ucretlendirilen = new Ucretlendirilen;
                            $ucretlendirilen->servis_id = $servisid;
                            $ucretlendirilen->uretimyer_id = $uretimyerid;
                            $ucretlendirilen->netsiscari_id = $netsiscariid;
                            $ucretlendirilen->garanti = $garanti;
                            $ucretlendirilen->secilenler = $secilenler;
                            $ucretlendirilen->sayacsayisi = $sayacsayisi;
                            $ucretlendirilen->durum = 2;
                            $ucretlendirilen->onaytipi = 0;
                            $ucretlendirilen->fiyat = $toplamtutar;
                            $ucretlendirilen->parabirimi_id = $birim;
                            $ucretlendirilen->fiyat2 = $toplamtutar2;
                            $ucretlendirilen->parabirimi2_id = $birim2;
                            $ucretlendirilen->kullanici_id = Auth::user()->id;
                            $ucretlendirilen->kayittarihi = date('Y-m-d H:i:s');
                            $ucretlendirilen->kurtarihi = $kurtarih;
                            $ucretlendirilen->onaytarihi = date('Y-m-d H:i:s');
                            $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                            $ucretlendirilen->yetkili_id = 1;
                            $ucretlendirilen->save();
                            try {
                                $onaylanan = new Onaylanan;
                                $onaylanan->servis_id = $servisid;
                                $onaylanan->uretimyer_id = $uretimyerid;
                                $onaylanan->netsiscari_id = $netsiscariid;
                                $onaylanan->ucretlendirilen_id = $ucretlendirilen->id;
                                $onaylanan->yetkili_id = 1;
                                $onaylanan->onaytarihi = date('Y-m-d H:i:s');
                                $onaylanan->onaylamatipi = 0;
                                $onaylanan->save();
                                $sayilar[$sayacsayisi] = array();
                                $i = 0;
                                $secilensayaclar = "";
                                $secilensayaclist = array();
                                $garantisayaclar = "";
                                $garantisayaclist = array();
                                $iadesayaclar = "";
                                $iadesayaclist = array();
                                try {
                                    $toplamtutarkontrol=0;
                                    $toplamtutar2kontrol=0;
                                    foreach ($arizafiyatlar as $arizafiyat) {
                                        $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                        $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                                        if ($sayacgelen->fiyatlandirma) {
                                            DB::rollBack();
                                            return Redirect::to($this->servisadi.'/ucretlendirmekayit')->with(array('mesaj' => 'true', 'title' => 'Sayaç Fiyatlandırması Yapılamaz', 'text' => $sayacgelen->serino.' Nolu Sayaç Zaten Ücretlendirilmiş!', 'type' => 'error'));
                                        }
                                        if($arizakayit->arizakayit_durum==7){
                                            array_push($iadesayaclist, $sayacgelen->id);
                                            $iadesayaclar .= ($iadesayaclar == "" ? "" : ",") . $sayacgelen->id;
                                        }else if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0) {
                                            array_push($secilensayaclist, $sayacgelen->id);
                                            $secilensayaclar .= ($secilensayaclar == "" ? "" : ",") . $sayacgelen->id;
                                        }else{
                                            array_push($garantisayaclist, $sayacgelen->id);
                                            $garantisayaclar .= ($garantisayaclar == "" ? "" : ",") . $sayacgelen->id;
                                        }
                                        $sayilar[$i] = $sayacgelen->stokkodu;
                                        $servistakip = ServisTakip::where('arizakayit_id', $arizafiyat->arizakayit_id)->first();
                                        $arizafiyat->kurtarihi = $kurtarih;
                                        $kur = 1;
                                        if ($birim != $arizafiyat->parabirimi_id) {
                                            if ($birim == "1") { //tl
                                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                                            } else { //euro dolar sterln
                                                if ($arizafiyat->parabirimi_id == "1") {
                                                    $kur = 1 / BackendController::getKurBilgisi($birim, $kurtarih);
                                                } else {
                                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($birim, $kurtarih);
                                                }
                                            }
                                        }
                                        $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                                        if ($arizafiyat->parabirimi2_id == $birim) {
                                            $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                            $arizafiyat->fiyat2 = 0;
                                            $arizafiyat->parabirimi2_id = null;
                                        }
                                        $indirim = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat * $arizafiyat->indirimorani) / 100 : 0;
                                        $indirim2 = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat2 * $arizafiyat->indirimorani) / 100 : 0;
                                        $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                                        $arizafiyat->tutar2 = $arizafiyat->fiyat2 - $indirim2;
                                        $arizafiyat->kdv = ($arizafiyat->tutar * 18) / 100;
                                        $arizafiyat->kdv2 = ($arizafiyat->tutar2 * 18) / 100;
                                        $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                                        $arizafiyat->toplamtutar2 = round(($arizafiyat->tutar2 + $arizafiyat->kdv2) * 2) / 2;
                                        $arizafiyat->parabirimi_id = $birim;
                                        if ($servisid != 6) //şubede ücretlendirilirse subedurum=1 kalacak diğer durumda 0 olacak
                                            $arizafiyat->subedurum = 0;
                                        else
                                            $arizafiyat->subedurum = $subedurum;
                                        $arizafiyat->durum = 1;
                                        $arizafiyat->save();
                                        $toplamtutarkontrol+=$arizafiyat->toplamtutar;
                                        $toplamtutar2kontrol+=$arizafiyat->toplamtutar2;
                                        $sayacgelen->fiyatlandirma = 1;
                                        $sayacgelen->musterionay = 1;
                                        $sayacgelen->teslimdurum = 1;
                                        $sayacgelen->save();
                                        if (!$servistakip) {
                                            $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                            $servistakip->arizakayit_id = $arizafiyat->arizakayit_id;
                                            $servistakip->arizafiyat_id = $arizafiyat->id;
                                        }
                                        $servistakip->ucretlendirilen_id = $ucretlendirilen->id;
                                        $servistakip->onaylanan_id = $onaylanan->id;
                                        $servistakip->durum = 5;
                                        $servistakip->ucretlendirmetarihi = date('Y-m-d H:i:s');
                                        $servistakip->onaylanmatarihi = date('Y-m-d H:i:s');
                                        $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                                        $servistakip->kullanici_id = Auth::user()->id;
                                        $servistakip->save();
                                        $i++;
                                    }
                                    if($toplamtutar!=$toplamtutarkontrol || $toplamtutar2!=$toplamtutar2kontrol){
                                        DB::rollBack();
                                        Log::error($toplamtutar.'<->'.$toplamtutarkontrol.' yada '.$toplamtutar2.'<->'.$toplamtutar2kontrol.' eşleşmemiş.');
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Kaydedilemedi', 'text' => 'Ücretlendirmede belirlenen toplam tutar ile kalemlerim toplam tutarı eşleşmiyor!('.$toplamtutar.'<->'.$toplamtutarkontrol.' ya da '.$toplamtutar2.'<->'.$toplamtutar2kontrol.')', 'type' => 'error'));
                                    }
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Kaydedilemedi', 'text' => 'Servistakip ve Sayaç Gelen Bilgileri Güncellenemedi', 'type' => 'error'));
                                }
                                $tekrarucretlendirilenlist = array();
                                if ($tekrarucretlendirilenler != "")//tekrar ucretlendirilen var
                                {
                                    try {
                                        $tekrarucretlendirilenlist = explode(",", $tekrarucretlendirilenler);
                                        $arizafiyattekrar = ArizaFiyat::whereIn('id', $tekrarucretlendirilenlist)->get();
                                        foreach ($arizafiyattekrar as $fiyat) {
                                            if ($servisid != 6) //şubede ücretlendirilirse subedurum=1 kalacak diğer durumda 0 olacak
                                                $fiyat->subedurum = 0;
                                            else
                                                $fiyat->subedurum = $subedurum;
                                            $fiyat->durum = 3;
                                            $fiyat->tekrarkayittarihi = date('Y-m-d H:i:s');
                                            $fiyat->save();
                                        }
                                        $count = count($tekrarucretlendirilenlist);
                                        BackendController::HatirlatmaGuncelle(7, $netsiscariid, $servisid, $count);
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Kaydedilemedi', 'text' => 'Tekrar Ücretlendirilen Fiyatlandırmalar Güncellenemedi', 'type' => 'error'));
                                    }
                                }
                                $yenisecilenlist = array();
                                $yenisecilenler = "";
                                foreach ($secilenlist as $secilen) {
                                    $flag = 0;
                                    foreach ($tekrarucretlendirilenlist as $tekrar) {
                                        if ($tekrar == $secilen) {
                                            $flag = 1;
                                            break;
                                        }
                                    }
                                    if (!$flag) {
                                        $yenisecilenler .= ($yenisecilenler == "" ? "" : ",") . $secilen;
                                        array_push($yenisecilenlist, $secilen);
                                    }
                                }
                                BackendController::HatirlatmaGuncelle(4, $netsiscariid, $servisid, count($yenisecilenlist));
                                if ($servisid == 5) //kalibrasyon tamamlanmışsa depo teslim yoksa bekleyecek
                                {
                                    try {
                                        $kalibrasyonsayi=0;
                                        $kalibrasyonsayaclist="";
                                        $garantisayi=0;
                                        $garantisayaclist="";
                                        $geriiadesayi=0;
                                        $geriiadesayaclist="";
                                        $periyodiksayi=0;
                                        $periyodiklist="";
                                        foreach ($arizafiyatlar as $arizafiyat) {
                                            $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                            $depogelen=DepoGelen::find($sayacgelen->depogelen_id);
                                            if($arizakayit->arizakayit_durum != 3) { //yedek parça bekliyorsa depo teslim olmaz
                                                if ($depogelen->periyodik) {
                                                    if ($sayacgelen->kalibrasyon) {
                                                        $periyodiksayi++;
                                                        $periyodiklist .= ($periyodiklist == "" ? "" : ",") . $sayacgelen->id;
                                                    } else {
                                                        $sayacgelen->teslimdurum = 0;
                                                        $sayacgelen->save();
                                                    }
                                                } else {
                                                    if ($arizakayit->arizakayit_durum == 7) {
                                                        $geriiadesayi++;
                                                        $geriiadesayaclist .= ($geriiadesayaclist == "" ? "" : ",") . $sayacgelen->id;
                                                    } else if ($sayacgelen->kalibrasyon) {
                                                        if ($arizafiyat->toplamtutar > 0 || $arizafiyat->toplamtutar2 > 0) {
                                                            $kalibrasyonsayi++;
                                                            $kalibrasyonsayaclist .= ($kalibrasyonsayaclist == "" ? "" : ",") . $sayacgelen->id;
                                                        } else {
                                                            $garantisayi++;
                                                            $garantisayaclist .= ($garantisayaclist == "" ? "" : ",") . $sayacgelen->id;
                                                        }
                                                    } else {
                                                        $sayacgelen->teslimdurum = 0;
                                                        $sayacgelen->save();
                                                    }
                                                }
                                            }else{
                                                $sayacgelen->teslimdurum = 0;
                                                $sayacgelen->save();
                                            }
                                        }
                                        if($kalibrasyonsayi>0){
                                            $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                                ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',0)->first();
                                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                            {
                                                $secilenlist = explode(',', $depoteslim->secilenler);
                                                $kalibrasyonsayaclar = explode(',', $kalibrasyonsayaclist);
                                                $secilenler="";
                                                $adet = 0;
                                                foreach($kalibrasyonsayaclar as $sayacgelenid){
                                                    if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                        $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                        $adet++;
                                                    }
                                                }
                                                $kalibrasyonsayi = $adet;
                                                if ($adet>0) {
                                                    $depoteslim->secilenler .= ',' . $secilenler;
                                                    $depoteslim->sayacsayisi += $adet;
                                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                    $depoteslim->save();
                                                }
                                            } else { //yeni depo teslimatı yapılacak
                                                $depoteslim = new DepoTeslim;
                                                $depoteslim->servis_id = $servisid;
                                                $depoteslim->netsiscari_id = $netsiscariid;
                                                $depoteslim->secilenler = $kalibrasyonsayaclist;
                                                $depoteslim->sayacsayisi = $kalibrasyonsayi;
                                                $depoteslim->depodurum = 0;
                                                $depoteslim->parabirimi_id=$birim;
                                                $depoteslim->parabirimi2_id=$birim2;
                                                $depoteslim->save();
                                            }
                                        }
                                        if($garantisayi>0){
                                            $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                                ->where('depodurum',0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',0)->first();
                                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                            {
                                                $secilenlist = explode(',', $depoteslim->secilenler);
                                                $garantisayaclar = explode(',', $garantisayaclist);
                                                $secilenler="";
                                                $adet = 0;
                                                foreach($garantisayaclar as $sayacgelenid){
                                                    if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                        $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                        $adet++;
                                                    }
                                                }
                                                $garantisayi = $adet;
                                                if ($adet>0) {
                                                    $depoteslim->secilenler .= ',' . $secilenler;
                                                    $depoteslim->sayacsayisi += $adet;
                                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                    $depoteslim->save();
                                                }
                                            } else { //yeni depo teslimatı yapılacak
                                                $depoteslim = new DepoTeslim;
                                                $depoteslim->servis_id = $servisid;
                                                $depoteslim->netsiscari_id = $netsiscariid;
                                                $depoteslim->secilenler = $garantisayaclist;
                                                $depoteslim->sayacsayisi = $garantisayi;
                                                $depoteslim->depodurum = 0;
                                                $depoteslim->tipi = 1;
                                                $depoteslim->parabirimi_id=$birim;
                                                $depoteslim->parabirimi2_id=$birim2;
                                                $depoteslim->save();
                                            }
                                        }
                                        if($geriiadesayi>0){
                                            $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                                ->where('depodurum',0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',0)->first();
                                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                            {
                                                $secilenlist = explode(',', $depoteslim->secilenler);
                                                $geriiadesayaclar = explode(',', $geriiadesayaclist);
                                                $secilenler="";
                                                $adet = 0;
                                                foreach($geriiadesayaclar as $sayacgelenid){
                                                    if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                        $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                        $adet++;
                                                    }
                                                }
                                                $geriiadesayi = $adet;
                                                if ($adet>0) {
                                                    $depoteslim->secilenler .= ',' . $secilenler;
                                                    $depoteslim->sayacsayisi += $adet;
                                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                    $depoteslim->save();
                                                }
                                            } else { //yeni depo teslimatı yapılacak
                                                $depoteslim = new DepoTeslim;
                                                $depoteslim->servis_id = $servisid;
                                                $depoteslim->netsiscari_id = $netsiscariid;
                                                $depoteslim->secilenler = $geriiadesayaclist;
                                                $depoteslim->sayacsayisi = $geriiadesayi;
                                                $depoteslim->depodurum = 0;
                                                $depoteslim->tipi = 2;
                                                $depoteslim->parabirimi_id=$birim;
                                                $depoteslim->parabirimi2_id=$birim2;
                                                $depoteslim->save();
                                            }
                                        }
                                        if($periyodiksayi>0){
                                            $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                                ->where('depodurum',0)->where('tipi',0)->where('periyodik',1)->where('subegonderim',0)->first();
                                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                            {
                                                $secilenlist = explode(',', $depoteslim->secilenler);
                                                $periyodiksayaclar = explode(',', $periyodiklist);
                                                $secilenler="";
                                                $adet = 0;
                                                foreach($periyodiksayaclar as $sayacgelenid){
                                                    if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                        $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                        $adet++;
                                                    }
                                                }
                                                $periyodiksayi = $adet;
                                                if ($adet>0) {
                                                    $depoteslim->secilenler .= ',' . $secilenler;
                                                    $depoteslim->sayacsayisi += $adet;
                                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                    $depoteslim->save();
                                                }
                                            } else { //yeni depo teslimatı yapılacak
                                                $depoteslim = new DepoTeslim;
                                                $depoteslim->servis_id = $servisid;
                                                $depoteslim->netsiscari_id = $netsiscariid;
                                                $depoteslim->secilenler = $periyodiklist;
                                                $depoteslim->sayacsayisi = $periyodiksayi;
                                                $depoteslim->depodurum = 0;
                                                $depoteslim->periyodik = 1;
                                                $depoteslim->parabirimi_id=$birim;
                                                $depoteslim->parabirimi2_id=$birim2;
                                                $depoteslim->save();
                                            }
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => 'Onaylanan sayaçlar depo teslimatına kaydedilemedi.', 'type' => 'error'));
                                    }
                                    if(($kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi)>0)
                                        BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $kalibrasyonsayi+$garantisayi+$geriiadesayi+$periyodiksayi);
                                    BackendController::BildirimEkle(4, $netsiscariid, $servisid, $sayacsayisi);
                                    BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Sistem Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                                    DB::commit();
                                    return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sistem Tarafından Onaylama Yapıldı', 'text' => 'Onaylama Başarıyla Yapıldı', 'type' => 'success'));
                                } else {
                                    try {
                                        if(count($secilensayaclist)>0){
                                            $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                                ->where('depodurum', 0)->where('tipi',0)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                            {
                                                $secilenlist = explode(',', $depoteslim->secilenler);
                                                $secilenler="";
                                                $adet = 0;
                                                foreach($secilensayaclist as $sayacgelenid){
                                                    if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                        $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                        $adet++;
                                                    }
                                                }
                                                if ($adet>0) {
                                                    $depoteslim->secilenler .= ',' . $secilenler;
                                                    $depoteslim->sayacsayisi += $adet;
                                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                    $depoteslim->save();
                                                }
                                            } else { //yeni depo teslimatı yapılacak
                                                $depoteslim = new DepoTeslim;
                                                $depoteslim->servis_id = $servisid;
                                                $depoteslim->netsiscari_id = $netsiscariid;
                                                $depoteslim->secilenler = $secilensayaclar;
                                                $depoteslim->sayacsayisi = count($secilensayaclist);
                                                $depoteslim->depodurum = 0;
                                                $depoteslim->parabirimi_id=$birim;
                                                $depoteslim->parabirimi2_id=$birim2;
                                                $depoteslim->save();
                                            }
                                        }
                                        if(count($garantisayaclist)>0){
                                            $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                                ->where('depodurum', 0)->where('tipi',1)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                            {
                                                $secilenlist = explode(',', $depoteslim->secilenler);
                                                $secilenler="";
                                                $adet = 0;
                                                foreach($garantisayaclist as $sayacgelenid){
                                                    if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                        $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                        $adet++;
                                                    }
                                                }
                                                if ($adet>0) {
                                                    $depoteslim->secilenler .= ',' . $secilenler;
                                                    $depoteslim->sayacsayisi += $adet;
                                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                    $depoteslim->save();
                                                }
                                            } else { //yeni depo teslimatı yapılacak
                                                $depoteslim = new DepoTeslim;
                                                $depoteslim->servis_id = $servisid;
                                                $depoteslim->netsiscari_id = $netsiscariid;
                                                $depoteslim->secilenler = $garantisayaclar;
                                                $depoteslim->sayacsayisi = count($garantisayaclist);
                                                $depoteslim->depodurum = 0;
                                                $depoteslim->tipi = 1;
                                                $depoteslim->parabirimi_id=$birim;
                                                $depoteslim->parabirimi2_id=$birim2;
                                                $depoteslim->save();
                                            }
                                        }
                                        if(count($iadesayaclist)>0){
                                            $depoteslim = DepoTeslim::where('servis_id', $servisid)->where('netsiscari_id', $netsiscariid)
                                                ->where('depodurum', 0)->where('tipi',2)->where('periyodik',0)->where('subegonderim',$subedurum)->first();
                                            if ($depoteslim) //teslim edilmemiş o yere ait sayaç var
                                            {
                                                $secilenlist = explode(',', $depoteslim->secilenler);
                                                $secilenler="";
                                                $adet = 0;
                                                foreach($iadesayaclist as $sayacgelenid){
                                                    if (!in_array($sayacgelenid, $secilenlist)) {  //sayaç bu listede ise
                                                        $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelenid;
                                                        $adet++;
                                                    }
                                                }
                                                if ($adet>0) {
                                                    $depoteslim->secilenler .= ',' . $secilenler;
                                                    $depoteslim->sayacsayisi += $adet;
                                                    $depoteslim->parabirimi2_id = $depoteslim->parabirimi2_id == null ? $birim2 : $depoteslim->parabirimi2_id;
                                                    $depoteslim->save();
                                                }
                                            } else { //yeni depo teslimatı yapılacak
                                                $depoteslim = new DepoTeslim;
                                                $depoteslim->servis_id = $servisid;
                                                $depoteslim->netsiscari_id = $netsiscariid;
                                                $depoteslim->secilenler = $iadesayaclar;
                                                $depoteslim->sayacsayisi = count($iadesayaclist);
                                                $depoteslim->depodurum = 0;
                                                $depoteslim->tipi = 2;
                                                $depoteslim->subegonderim=$subedurum;
                                                $depoteslim->parabirimi_id=$birim;
                                                $depoteslim->parabirimi2_id=$birim2;
                                                $depoteslim->save();
                                            }
                                        }
                                        BackendController::HatirlatmaEkle(9, $netsiscariid, $servisid, $sayacsayisi);
                                        BackendController::BildirimEkle(4, $netsiscariid, $servisid, $sayacsayisi);
                                        BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-check', $ucretlendirilen->id . ' Numaralı Ücretlendirme Sistem Tarafından Onaylandı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                                        DB::commit();
                                        return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sistem Tarafından Onaylama Yapıldı', 'text' => 'Garanti içinde olduğundan Sistem tarafından onaylama başarıyla yapıldı', 'type' => 'success'));
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Fiyatlandırma Kaydedilemedi', 'text' => 'Garanti içinde olduğundan Depo Teslimi Sırasında Hata Oluştu', 'type' => 'error'));
                                    }
                                }
                            } catch (Exception $e) {
                                DB::rollBack();
                                Log::error($e);
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Kaydedilemedi', 'text' => 'Garanti içinde olduğundan Sistem tarafından Onaylanırken Hata oluştu', 'type' => 'error'));
                            }

                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Kaydedilemedi', 'text' => 'Garanti içinde olduğundan Sistem tarafından Onaylanırken Hata oluştu', 'type' => 'error'));
                        }
                    } else {
                        try {
                            $kayit = Ucretlendirilen::where('servis_id', $servisid)->where('uretimyer_id', $uretimyerid)->where('netsiscari_id', $netsiscariid)->where('durum', 0)->first();
                            if ($kayit) //gönderilmemiş o yere ait ucretlendirme varsa birleştir
                            {
                                try {
                                    $eskisecilenler = $kayit->secilenler;
                                    $eskigaranti = $kayit->garanti;
                                    $eskisayi = $kayit->sayacsayisi;
                                    $eskifiyatlar = BackendController::getUcretlendirmeFiyat($eskisecilenler, $kurtarih, $birim, $birim2);
                                    if ($eskifiyatlar['durum']) {
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => $eskifiyatlar['error'], 'type' => 'error'));
                                    }
                                    $eskifiyat = $eskifiyatlar['fiyat'];
                                    $eskifiyat2 = $eskifiyatlar['fiyat2'];
                                    $birim2 = $eskifiyatlar['yenibirim'] == null ? $birim2 : $eskifiyatlar['yenibirim'];
                                    if ($eskigaranti != 0)
                                        $kayit->garanti = $garanti;
                                    $kayit->secilenler = $eskisecilenler . ',' . $secilenler;
                                    $kayit->sayacsayisi = $eskisayi + $sayacsayisi;
                                    $kayit->fiyat = $eskifiyat + $toplamtutar;
                                    $kayit->fiyat2 = $eskifiyat2 + $toplamtutar2;
                                    $kayit->kurtarihi = $kurtarih;
                                    $kayit->parabirimi_id = $birim;
                                    $kayit->parabirimi2_id = $birim2;
                                    $kayit->save();
                                    $sayilar[$sayacsayisi] = array();
                                    $i = 0;
                                    try {
                                        foreach ($arizafiyatlar as $arizafiyat) {
                                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                            $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                                            if ($sayacgelen->fiyatlandirma) {
                                                DB::rollBack();
                                                return Redirect::to($this->servisadi.'/ucretlendirmekayit')->with(array('mesaj' => 'true', 'title' => 'Sayaç Fiyatlandırması Yapılamaz', 'text' => $sayacgelen->serino.' Nolu Sayaç Zaten Ücretlendirilmiş!', 'type' => 'error'));
                                            }
                                            $sayilar[$i] = $sayacgelen->stokkodu;
                                            $servistakip = ServisTakip::where('arizakayit_id', $arizafiyat->arizakayit_id)->first();
                                            $arizafiyat->durum = 1;
                                            $arizafiyat->kurtarihi = $kurtarih;
                                            $kur = 1;
                                            if ($birim != $arizafiyat->parabirimi_id) {
                                                if ($birim == "1") { //tl
                                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                                                } else { //euro dolar sterln
                                                    if ($arizafiyat->parabirimi_id == "1") {
                                                        $kur = 1 / BackendController::getKurBilgisi($birim, $kurtarih);
                                                    } else {
                                                        $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($birim, $kurtarih);
                                                    }
                                                }
                                            }
                                            $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                                            if ($arizafiyat->parabirimi2_id == $birim) {
                                                $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                                $arizafiyat->fiyat2 = 0;
                                                $arizafiyat->parabirimi2_id = null;
                                            }
                                            $indirim = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat * $arizafiyat->indirimorani) / 100 : 0;
                                            $indirim2 = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat2 * $arizafiyat->indirimorani) / 100 : 0;
                                            $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                                            $arizafiyat->tutar2 = $arizafiyat->fiyat2 - $indirim2;
                                            $arizafiyat->kdv = ($arizafiyat->tutar * 18) / 100;
                                            $arizafiyat->kdv2 = ($arizafiyat->tutar2 * 18) / 100;
                                            $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                                            $arizafiyat->toplamtutar2 = round(($arizafiyat->tutar2 + $arizafiyat->kdv2) * 2) / 2;
                                            $arizafiyat->parabirimi_id = $birim;
                                            if ($servisid != 6) //şubede ücretlendirilirse subedurum=1 kalacak diğer durumda 0 olacak
                                                $arizafiyat->subedurum = 0;
                                            else
                                                $arizafiyat->subedurum = $subedurum;
                                            $arizafiyat->save();
                                            $sayacgelen->fiyatlandirma = 1;
                                            $sayacgelen->save();
                                            if (!$servistakip) {
                                                $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                $servistakip->arizakayit_id = $arizafiyat->arizakayit_id;
                                                $servistakip->arizafiyat_id = $arizafiyat->id;
                                            }
                                            $servistakip->ucretlendirilen_id = $kayit->id;
                                            $servistakip->durum = 3;
                                            $servistakip->ucretlendirmetarihi = date('Y-m-d H:i:s');
                                            $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                                            $servistakip->kullanici_id = Auth::user()->id;
                                            $servistakip->save();
                                            $i++;
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Sayaç Gelen ve Servis Takip Bilgisi Güncellenemedi', 'type' => 'error'));
                                    }
                                    try {
                                        if ($tekrarucretlendirilenler != "")//tekrar ucretlendirilen var
                                        {
                                            $tekrarucretlendirilenlist = explode(",", $tekrarucretlendirilenler);
                                            $arizafiyattekrar = ArizaFiyat::whereIn('id', $tekrarucretlendirilenlist)->get();
                                            foreach ($arizafiyattekrar as $fiyat) {
                                                if ($servisid != 6) //şubede ücretlendirilirse subedurum=1 kalacak diğer durumda 0 olacak
                                                    $fiyat->subedurum = 0;
                                                else
                                                    $fiyat->subedurum = $subedurum;
                                                $fiyat->durum = 3;
                                                $fiyat->tekrarkayittarihi = date('Y-m-d H:i:s');
                                                $fiyat->save();
                                            }
                                            $count = count($tekrarucretlendirilenlist);
                                            BackendController::HatirlatmaGuncelle(7, $kayit->netsiscari_id, $kayit->servis_id, $count);
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Tekrar Ücretlendirme Yapılan Fiyatlandırmalar Güncellenemedi', 'type' => 'error'));
                                    }
                                    $tekrarucretlendirilenlist = explode(',', $tekrarucretlendirilenler);
                                    $yenisecilenlist = array();
                                    foreach ($secilenlist as $secilen) {
                                        $flag = 0;
                                        foreach ($tekrarucretlendirilenlist as $tekrar) {
                                            if ($tekrar == $secilen) {
                                                $flag = 1;
                                                break;
                                            }
                                        }
                                        if (!$flag) {
                                            array_push($yenisecilenlist, $secilen);
                                        }
                                    }
                                    BackendController::HatirlatmaGuncelle(4, $netsiscariid, $servisid, count($yenisecilenlist));
                                    BackendController::HatirlatmaEkle(5, $netsiscariid, $servisid, $sayacsayisi);
                                    BackendController::BildirimEkle(4, $netsiscariid, $servisid, $sayacsayisi);
                                    BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-check', $kayit->id . ' Numaralı Ücretlendirme Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $kayit->id);
                                    DB::commit();
                                    return Redirect::to('ucretlendirme/ucretlendirmekayit')->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapıldı', 'text' => 'Ücretlendirme Başarıyla Yapıldı', 'type' => 'success'));
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Ücretlendirme Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                }
                            } else { //o yere ait ucretlendirme yoksa yeni oluştur
                                try {
                                    $ucretlendirilen = new Ucretlendirilen;
                                    $ucretlendirilen->servis_id = $servisid;
                                    $ucretlendirilen->uretimyer_id = $uretimyerid;
                                    $ucretlendirilen->netsiscari_id = $netsiscariid;
                                    $ucretlendirilen->garanti = $garanti;
                                    $ucretlendirilen->secilenler = $secilenler;
                                    $ucretlendirilen->sayacsayisi = $sayacsayisi;
                                    $ucretlendirilen->durum = 0;
                                    $ucretlendirilen->fiyat = $toplamtutar;
                                    $ucretlendirilen->parabirimi_id = $birim;
                                    $ucretlendirilen->fiyat2 = $toplamtutar2;
                                    $ucretlendirilen->parabirimi2_id = $birim2;
                                    $ucretlendirilen->kullanici_id = Auth::user()->id;
                                    $ucretlendirilen->kayittarihi = date('Y-m-d H:i:s');
                                    $ucretlendirilen->kurtarihi = $kurtarih;
                                    $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                                    $ucretlendirilen->save();
                                    $sayilar[$sayacsayisi] = array();
                                    $i = 0;
                                    try {
                                        $toplamtutarkontrol=0;
                                        $toplamtutar2kontrol=0;
                                        foreach ($arizafiyatlar as $arizafiyat) {
                                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                                            $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                                            if ($sayacgelen->fiyatlandirma) {
                                                DB::rollBack();
                                                return Redirect::to($this->servisadi.'/ucretlendirmekayit')->with(array('mesaj' => 'true', 'title' => 'Sayaç Fiyatlandırması Yapılamaz', 'text' => $sayacgelen->serino.' Nolu Sayaç Zaten Ücretlendirilmiş!', 'type' => 'error'));
                                            }
                                            $sayilar[$i] = $sayacgelen->stokkodu;
                                            $servistakip = ServisTakip::where('arizakayit_id', $arizafiyat->arizakayit_id)->first();
                                            $arizafiyat->kurtarihi = $kurtarih;
                                            $kur = 1;
                                            if ($birim != $arizafiyat->parabirimi_id) {
                                                if ($birim == "1") { //tl
                                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                                                } else { //euro dolar sterln
                                                    if ($arizafiyat->parabirimi_id == "1") {
                                                        $kur = 1 / BackendController::getKurBilgisi($birim, $kurtarih);
                                                    } else {
                                                        $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($birim, $kurtarih);
                                                    }
                                                }
                                            }
                                            $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                                            if ($arizafiyat->parabirimi2_id == $birim) {
                                                $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                                $arizafiyat->fiyat2 = 0;
                                                $arizafiyat->parabirimi2_id = null;
                                            }
                                            $indirim = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat * $arizafiyat->indirimorani) / 100 : 0;
                                            $indirim2 = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat2 * $arizafiyat->indirimorani) / 100 : 0;
                                            $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                                            $arizafiyat->tutar2 = $arizafiyat->fiyat2 - $indirim2;
                                            $arizafiyat->kdv = ($arizafiyat->tutar * 18) / 100;
                                            $arizafiyat->kdv2 = ($arizafiyat->tutar2 * 18) / 100;
                                            $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                                            $arizafiyat->toplamtutar2 = round(($arizafiyat->tutar2 + $arizafiyat->kdv2) * 2) / 2;
                                            $arizafiyat->parabirimi_id = $birim;
                                            if ($servisid != 6) //şubede ücretlendirilirse subedurum=1 kalacak diğer durumda 0 olacak
                                                $arizafiyat->subedurum = 0;
                                            else
                                                $arizafiyat->subedurum = $subedurum;
                                            $arizafiyat->durum = 1;
                                            $arizafiyat->save();
                                            $toplamtutarkontrol+=$arizafiyat->toplamtutar;
                                            $toplamtutar2kontrol+=$arizafiyat->toplamtutar2;
                                            $sayacgelen->fiyatlandirma = 1;
                                            $sayacgelen->save();
                                            if (!$servistakip) {
                                                $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                $servistakip->arizakayit_id = $arizafiyat->arizakayit_id;
                                                $servistakip->arizafiyat_id = $arizafiyat->id;
                                            }
                                            $servistakip->ucretlendirilen_id = $ucretlendirilen->id;
                                            $servistakip->durum = 3;
                                            $servistakip->ucretlendirmetarihi = date('Y-m-d H:i:s');
                                            $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                                            $servistakip->kullanici_id = Auth::user()->id;
                                            $servistakip->save();
                                            $i++;
                                        }
                                        if($toplamtutar!=$toplamtutarkontrol || $toplamtutar2!=$toplamtutar2kontrol){
                                            DB::rollBack();
                                            Log::error($toplamtutar.'<->'.$toplamtutarkontrol.' yada '.$toplamtutar2.'<->'.$toplamtutar2kontrol.' eşleşmemiş.');
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Kaydedilemedi', 'text' => 'Ücretlendirmede belirlenen toplam tutar ile kalemlerim toplam tutarı eşleşmiyor!('.$toplamtutar.'<->'.$toplamtutarkontrol.' ya da '.$toplamtutar2.'<->'.$toplamtutar2kontrol.')', 'type' => 'error'));
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Sayaç Gelen ve Servis Takip Bilgisi Güncellenemedi', 'type' => 'error'));
                                    }
                                    try {
                                        if ($tekrarucretlendirilenler != "")//tekrar ucretlendirilen var
                                        {
                                            $tekrarucretlendirilenlist = explode(",", $tekrarucretlendirilenler);
                                            $arizafiyattekrar = ArizaFiyat::whereIn('id', $tekrarucretlendirilenlist)->get();
                                            foreach ($arizafiyattekrar as $fiyat) {
                                                if ($servisid != 6) //şubede ücretlendirilirse subedurum=1 kalacak diğer durumda 0 olacak
                                                    $fiyat->subedurum = 0;
                                                else
                                                    $fiyat->subedurum = $subedurum;
                                                $fiyat->durum = 3;
                                                $fiyat->tekrarkayittarihi = date('Y-m-d H:i:s');
                                                $fiyat->save();
                                            }
                                            $count = count($tekrarucretlendirilenlist);
                                            BackendController::HatirlatmaGuncelle(7, $netsiscariid, $servisid, $count);
                                        }
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Tekrar Ücretlendirme Yapılan Fiyatlandırmalar Güncellenemedi', 'type' => 'error'));
                                    }
                                    $tekrarucretlendirilenlist = explode(',', $tekrarucretlendirilenler);
                                    $yenisecilenlist = array();
                                    foreach ($secilenlist as $secilen) {
                                        $flag = 0;
                                        foreach ($tekrarucretlendirilenlist as $tekrar) {
                                            if ($tekrar == $secilen) {
                                                $flag = 1;
                                                break;
                                            }
                                        }
                                        if (!$flag) {
                                            array_push($yenisecilenlist, $secilen);
                                        }
                                    }
                                    BackendController::HatirlatmaGuncelle(4, $netsiscariid, $servisid, count($yenisecilenlist));
                                    BackendController::HatirlatmaEkle(5, $netsiscariid, $servisid, $sayacsayisi);
                                    BackendController::BildirimEkle(4, $netsiscariid, $servisid, $sayacsayisi);
                                    BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-check', $ucretlendirilen->id . ' Numaralı Ücretlendirme Eklendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                                    DB::commit();
                                    return Redirect::to('ucretlendirme/ucretlendirmekayit')->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapıldı', 'text' => 'Ücretlendirme Başarıyla Yapıldı', 'type' => 'success'));
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Yeni Ücretlendirme Kaydedilemedi', 'type' => 'error'));
                                }
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Ücretlendirme Kaydedilemedi', 'type' => 'error'));
                        }
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => str_replace("'", "\'", $e->getMessage()), 'type' => 'error'));
                }
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => 'Ücretlendirme Detayı Ekrana Gelmeden Onaylanmaya Çalışıldı.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Yapılamadı', 'text' => str_replace("'", "\'", $e->getMessage()), 'type' => 'error'));
        }
    }

    public function postSubeaktar(){
        try {
            $rules = [];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $ucretlendirilenid = Input::get('subeaktarid');
            $secilenler = Input::get('subeaktarsecilenler');
            $secilenlist = explode(',', $secilenler);
            $tumu = explode(',', Input::get('subeaktartumu'));
            $yetkiliid = Auth::user()->id;
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            DB::beginTransaction();
            if($ucretlendirilen->servis_id==6){
                $yetkili = Yetkili::where('netsiscari_id',$ucretlendirilen->netsiscari_id)->where('kullanici_id',$yetkiliid)->where('aktif',1)->first();
            }else{
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı!', 'text' => 'Ücretlendirme Sadece Şubeler için Aktarılabilir!', 'type' => 'warning'));
            }
            if(!$yetkili){
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Yetkili Bilgisi Bulunamadı!', 'text' => 'Bu Şube için Bu Kullanıcının Aktarma Yetkisi Yok!', 'type' => 'error'));
            }
            $servisid = $ucretlendirilen->servis_id;
            $uretimyerid = $ucretlendirilen->uretimyer_id;
            $netsiscariid = $ucretlendirilen->netsiscari_id;
            $sayacsayisi = count($secilenlist);
            $arizafiyatlar = ArizaFiyat::whereIn('id', $secilenlist)->get();
            $sube = Sube::where('netsiscari_id', $yetkili->netsiscari_id)->where('aktif', 1)->first();
            if(!$sube)
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube Bilgisi Bulunamadı!', 'text' => 'Bu Kullanıcı için Aktif Şube Bilgisi Bulunamadı!', 'type' => 'error'));
            if (count($secilenlist) == count($tumu)) //ucretlendirmedeki tum sayaçlar aktarılmış
            {
                try {
                    $ucretlendirilen->durum = 2;
                    $ucretlendirilen->onaytipi = 3;
                    $ucretlendirilen->yetkili_id=$yetkili->id;
                    $ucretlendirilen->onaytarihi = date('Y-m-d H:i:s');
                    $ucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                    $ucretlendirilen->save();
                    $onaylanan = new Onaylanan;
                    $onaylanan->servis_id = $servisid;
                    $onaylanan->uretimyer_id = $uretimyerid;
                    $onaylanan->netsiscari_id = $netsiscariid;
                    $onaylanan->ucretlendirilen_id = $ucretlendirilen->id;
                    $onaylanan->yetkili_id = $yetkili->id;
                    $onaylanan->onaytarihi = date('Y-m-d H:i:s');
                    $onaylanan->onaylamatipi = 3;
                    $onaylanan->save();
                    $secilensayaclar = "";
                    $secilensayaclist = array();
                    $garantisayaclar = "";
                    $garantisayaclist = array();
                    try {
                        foreach ($arizafiyatlar as $arizafiyat) {
                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                            $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                            if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0) {
                                array_push($secilensayaclist, $sayacgelen->id);
                                $secilensayaclar .= ($secilensayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }else{
                                array_push($garantisayaclist, $sayacgelen->id);
                                $garantisayaclar .= ($garantisayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }
                            $sayacgelen->musterionay = 1;
                            $sayacgelen->teslimdurum = 1;
                            $sayacgelen->save();
                            $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                            $servistakip->onaylanan_id = $onaylanan->id;
                            $servistakip->durum = 5;
                            $servistakip->onaylanmatarihi = date('Y-m-d H:i:s');
                            $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                            $servistakip->kullanici_id = Auth::user()->id;
                            $servistakip->save();
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Aktarma Kaydedilemedi', 'text' => 'Sayaç Gelen ve Servis Takip Bilgisi Güncellenemedi', 'type' => 'error'));
                    }
                    try{
                        // abone yoksa aktarma izin verme
                        $sayacgelenler = SayacGelen::whereIn('id', $secilensayaclist)->get(array('serino'));
                        $abonesayaclar = AboneSayac::whereIn('serino', $sayacgelenler->toArray())->get(array('id')); //sayacgelenler array değil gibi geliyor
                        if ($abonesayaclar->count() == $sayacsayisi) { //ücretlendirilen sayaçların hepsi mevcut
                            $abonetahsisler = AboneTahsis::whereIn('abonesayac_id', $abonesayaclar->toArray())->get();
                            if ($abonetahsisler->count() == $sayacsayisi) {//sayaçların hepsi tahsisliyse
                                try {
                                    foreach ($abonetahsisler as $abonetahsis) {
                                        $abone = Abone::find($abonetahsis->abone_id);
                                        $abonesayac = AboneSayac::find($abonetahsis->abonesayac_id);
                                        $abonesayacgelen = SayacGelen::whereIn('id', $secilensayaclist)->where('serino', $abonesayac->serino)->first();
                                        if(!$abonesayacgelen){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı', 'text' => 'Ücretlendirilen sayaçlardan sayaç kayıdı olmayanlar mevcut!', 'type' => 'error'));
                                        }
                                        try {
                                            $serviskayit = ServisKayit::where('abonetahsis_id',$abonetahsis->id)->where('durum',0)->first();
                                            if($serviskayit){
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı', 'text' => 'Ücretlendirilen sayaçlardan servis kayıdı olan sayaçlar var!', 'type' => 'error'));
                                            }
                                            $serviskayit = new ServisKayit;
                                            $serviskayit->subekodu=$sube->subekodu;
                                            $serviskayit->kayitadres = $abonesayac->adres;
                                            $serviskayit->abonetahsis_id = $abonetahsis->id;
                                            $serviskayit->netsiscari_id = $abone->netsiscari_id;
                                            $serviskayit->uretimyer_id = $abone->uretimyer_id;
                                            $serviskayit->kullanici_id = Auth::user()->id;
                                            $serviskayit->tipi = 1;
                                            $serviskayit->acilmatarihi = $ucretlendirilen->onaytarihi;
                                            $serviskayit->aciklama = $abonesayacgelen->sokulmenedeni;
                                            $serviskayit->takilmatarihi = $abonesayacgelen->takilmatarihi;
                                            $serviskayit->ilkendeks = $abonesayacgelen->endeks;
                                            $serviskayit->sayacborcu = BackendController::SayacBorcuHesapla($abonesayacgelen);
                                            $serviskayit->servissayaci = 1;
                                            $serviskayit->ucretlendirilen_id = $ucretlendirilenid;
                                            $serviskayit->save();
                                            $abonesayac->tamirdurum=2;
                                            $abonesayac->save();
                                        } catch (Exception $e) {
                                            Log::error($e);
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı', 'text' => 'Ücretlendirilen sayaçlar abone teslimine kaydedilemedi!', 'type' => 'error'));
                                        }
                                    }
                                } catch (Exception $e) {
                                    Log::error($e);
                                    DB::rollBack();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı abone teslimine kaydedilemedi!', 'type' => 'error'));
                                }
                                BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::HatirlatmaEkle(13, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Yetkili Tarafından Aktarıldı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                                DB::commit();
                                return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Aktarımı Tamamlandı', 'text' => 'Aktarma Başarıyla Yapıldı', 'type' => 'success'));
                            } else {//tahsisli olmayan sayaç mevcut
                                DB::rollBack();
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı aboneye tahsisli değil. Önce tahsis işlemlerinin tamamlanması gerekiyor!', 'type' => 'error'));
                            }
                        } else {//listede olmayan sayaç mevcut
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı aboneye tahsisli değil. Önce tahsis işlemlerinin tamamlanması gerekiyor!', 'type' => 'error'));
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Aktarma Kaydedilemedi', 'text' => 'Servis Kayıdı Girilemedi', 'type' => 'error'));
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Aktarma Hatası', 'text' => 'Aktarma onaylananlara kaydedilemedi', 'type' => 'error'));
                }
            } else { //bazıları onaylanmış
                try {
                    $yeniucretlendirilen = new Ucretlendirilen;
                    $yeniucretlendirilen->servis_id = $servisid;
                    $yeniucretlendirilen->uretimyer_id = $uretimyerid;
                    $yeniucretlendirilen->netsiscari_id = $netsiscariid;
                    $yeniucretlendirilen->secilenler = $secilenler;
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
                            if ($yeniparabirimi === "1") { //tl
                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                            } else { //euro dolar sterln
                                if ($arizafiyat->parabirimi_id === "1") {
                                    $kur = 1 / BackendController::getKurBilgisi($yeniparabirimi, $kurtarih);
                                } else {
                                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($yeniparabirimi, $kurtarih);
                                }
                            }
                        }
                        $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                        if($yeniparabirimi2===null){
                            if($arizafiyat->parabirimi2_id!==null){
                                if($arizafiyat->parabirimi2_id===$yeniparabirimi){
                                    $arizafiyat->fiyat +=$arizafiyat->fiyat2;
                                    $arizafiyat->fiyat2=0;
                                    $arizafiyat->parabirimi2_id=null;
                                }else{
                                    $yeniparabirimi2=$arizafiyat->parabirimi2_id;
                                }
                            }
                        }else{
                            if($arizafiyat->parabirimi2_id!=null){
                                if($arizafiyat->parabirimi2_id===$yeniparabirimi){
                                    $arizafiyat->fiyat +=$arizafiyat->fiyat2;
                                    $arizafiyat->fiyat2=0;
                                    $arizafiyat->parabirimi2_id=null;
                                }else if($arizafiyat->parabirimi2_id!==$yeniparabirimi2){
                                    DB::rollBack();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Yapılamadı', 'text' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
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
                    $yeniucretlendirilen->sayacsayisi = $sayacsayisi;
                    $yeniucretlendirilen->fiyat = $yenifiyat;
                    $yeniucretlendirilen->fiyat2 = $yenifiyat2;
                    $yeniucretlendirilen->parabirimi_id = $yeniparabirimi;
                    $yeniucretlendirilen->parabirimi2_id = $yeniparabirimi2;
                    $yeniucretlendirilen->kullanici_id = Auth::user()->id;
                    $yeniucretlendirilen->kayittarihi = date('Y-m-d H:i:s');
                    $yeniucretlendirilen->kurtarihi = $ucretlendirilen->kurtarihi;
                    $yeniucretlendirilen->durum = 2;
                    $yeniucretlendirilen->onaytipi = 3;
                    $yeniucretlendirilen->onaytarihi = date('Y-m-d H:i:s');
                    $yeniucretlendirilen->durumtarihi = date('Y-m-d H:i:s');
                    $yeniucretlendirilen->yetkili_id=$yetkili->id;
                    $yeniucretlendirilen->save();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Yapılamadı', 'text' => 'Seçilen Sayaçlara ait Ücretlendirme Kaydedilemedi', 'type' => 'error'));
                }
                DB::commit();
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
                    if ($kalanparabirimi !== $arizafiyat->parabirimi_id) {
                        if ($kalanparabirimi === "1") { //tl
                            $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                        } else { //euro dolar sterln
                            if ($arizafiyat->parabirimi_id === "1") {
                                $kur = 1 / BackendController::getKurBilgisi($kalanparabirimi, $kurtarih);
                            } else {
                                $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($kalanparabirimi, $kurtarih);
                            }
                        }
                    }
                    $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                    if ($kalanparabirimi2 === null) {
                        if ($arizafiyat->parabirimi2_id !== null) {
                            if ($arizafiyat->parabirimi2_id === $kalanparabirimi) {
                                $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                $arizafiyat->fiyat2 = 0;
                                $arizafiyat->parabirimi2_id = null;
                            } else {
                                $kalanparabirimi2 = $arizafiyat->parabirimi2_id;
                            }
                        }
                    } else {
                        if ($arizafiyat->parabirimi2_id !== null) {
                            if ($arizafiyat->parabirimi2_id === $kalanparabirimi) {
                                $arizafiyat->fiyat += $arizafiyat->fiyat2;
                                $arizafiyat->fiyat2 = 0;
                                $arizafiyat->parabirimi2_id = null;
                            } else if ($arizafiyat->parabirimi2_id !== $kalanparabirimi2) {
                                DB::rollBack();
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Yapılamadı', 'text' => 'Seçilen Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
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
                try {
                    $ucretlendirilen->secilenler = $kalanlar;
                    $ucretlendirilen->garanti = $kalangaranti;
                    $ucretlendirilen->sayacsayisi = $kalansayi;
                    $ucretlendirilen->fiyat = $kalanfiyat;
                    $ucretlendirilen->fiyat2 = $kalanfiyat2;
                    $ucretlendirilen->parabirimi_id = $kalanparabirimi;
                    $ucretlendirilen->parabirimi2_id = $kalanparabirimi2;
                    $ucretlendirilen->save();
                    $onaylanan = new Onaylanan;
                    $onaylanan->servis_id = $servisid;
                    $onaylanan->uretimyer_id = $uretimyerid;
                    $onaylanan->netsiscari_id = $netsiscariid;
                    $onaylanan->ucretlendirilen_id = $yeniucretlendirilen->id;
                    $onaylanan->yetkili_id = $yetkili->id;
                    $onaylanan->onaytarihi = date('Y-m-d H:i:s');
                    $onaylanan->onaylamatipi = 3;
                    $onaylanan->save();
                    $secilensayaclar = "";
                    $secilensayaclist = array();
                    $garantisayaclar = "";
                    $garantisayaclist = array();
                    try {
                        foreach ($arizafiyatlar as $arizafiyat) {
                            $arizakayit = ArizaKayit::find($arizafiyat->arizakayit_id);
                            $sayacgelen = SayacGelen::find($arizakayit->sayacgelen_id);
                            if($arizafiyat->toplamtutar>0 || $arizafiyat->toplamtutar2>0) {
                                array_push($secilensayaclist, $sayacgelen->id);
                                $secilensayaclar .= ($secilensayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }else{
                                array_push($garantisayaclist, $sayacgelen->id);
                                $garantisayaclar .= ($garantisayaclar == "" ? "" : ",") . $sayacgelen->id;
                            }
                            $sayacgelen->musterionay = 1;
                            $sayacgelen->teslimdurum = 1;
                            $sayacgelen->save();
                            $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                            $servistakip->ucretlendirilen_id = $yeniucretlendirilen->id;
                            $servistakip->onaylanan_id = $onaylanan->id;
                            $servistakip->durum = 5;
                            $servistakip->onaylanmatarihi = date('Y-m-d H:i:s');
                            $servistakip->sonislemtarihi = date('Y-m-d H:i:s');
                            $servistakip->kullanici_id = Auth::user()->id;
                            $servistakip->save();
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Aktarma Kaydedilemedi', 'text' => 'Sayaç Gelen ve Servis Takip Bilgisi Güncellenemedi', 'type' => 'error'));
                    }
                    try{
                        // abone yoksa aktarma izin verme
                        $sayacgelenler = SayacGelen::whereIn('id', $secilensayaclist)->get(array('serino'));
                        $abonesayaclar = AboneSayac::whereIn('serino', $sayacgelenler->toArray())->get(array('id')); //sayacgelenler array değil gibi geliyor
                        if ($abonesayaclar->count() == $sayacsayisi) { //ücretlendirilen sayaçların hepsi mevcut
                            $abonetahsisler = AboneTahsis::whereIn('abonesayac_id', $abonesayaclar->toArray())->get();
                            if ($abonetahsisler->count() == $sayacsayisi) {//sayaçların hepsi tahsisliyse
                                try {
                                    foreach ($abonetahsisler as $abonetahsis) {
                                        $abone = Abone::find($abonetahsis->abone_id);
                                        $abonesayac = AboneSayac::find($abonetahsis->abonesayac_id);
                                        $abonesayacgelen = SayacGelen::whereIn('id', $secilensayaclist)->where('serino', $abonesayac->serino)->first();
                                        if(!$abonesayacgelen){
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı', 'text' => 'Ücretlendirilen sayaçlardan sayaç kayıdı olmayanlar mevcut!', 'type' => 'error'));
                                        }
                                        try {
                                            $serviskayit = ServisKayit::where('abonetahsis_id',$abonetahsis->id)->where('durum',0)->where('tipi',1)->first();
                                            if($serviskayit){
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı', 'text' => 'Ücretlendirilen sayaçlardan tamir montajı olan sayaçlar var!', 'type' => 'error'));
                                            }
                                            $serviskayit = new ServisKayit;
                                            $serviskayit->subekodu=$sube->subekodu;
                                            $serviskayit->kayitadres = $abonesayac->adres;
                                            $serviskayit->abonetahsis_id = $abonetahsis->id;
                                            $serviskayit->netsiscari_id = $abone->netsiscari_id;
                                            $serviskayit->uretimyer_id = $abone->uretimyer_id;
                                            $serviskayit->kullanici_id = Auth::user()->id;
                                            $serviskayit->tipi = 1;
                                            $serviskayit->acilmatarihi = $ucretlendirilen->onaytarihi;
                                            $serviskayit->ilkendeks = $abonesayacgelen->endeks;
                                            $serviskayit->ucretlendirilen_id = $yeniucretlendirilen->id;
                                            $serviskayit->save();
                                            $abonesayac->tamirdurum=2;
                                            $abonesayac->save();
                                        } catch (Exception $e) {
                                            Log::error($e);
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı', 'text' => 'Ücretlendirilen sayaçlar abone teslimine kaydedilemedi!', 'type' => 'error'));
                                        }
                                    }
                                } catch (Exception $e) {
                                    Log::error($e);
                                    DB::rollBack();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı abone teslimine kaydedilemedi!', 'type' => 'error'));
                                }
                                BackendController::HatirlatmaGuncelle(5, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::HatirlatmaEkle(13, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::BildirimEkle(7, $netsiscariid, $servisid, $sayacsayisi);
                                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $ucretlendirilen->id . ' Numaralı Ücretlendirme Yetkili Tarafından Aktarıldı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ücretlendirme Numarası:' . $ucretlendirilen->id);
                                DB::commit();
                                return Redirect::to('ucretlendirme/ucretlendirilenler')->with(array('mesaj' => 'true', 'title' => 'Sayaçların Aktarımı Tamamlandı', 'text' => 'Aktarma Başarıyla Yapıldı', 'type' => 'success'));
                            } else {//tahsisli olmayan sayaç mevcut
                                DB::rollBack();
                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı aboneye tahsisli değil. Önce tahsis işlemlerinin tamamlanması gerekiyor!', 'type' => 'error'));
                            }
                        } else {//listede olmayan sayaç mevcut
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ücretlendirme Aktarılamadı', 'text' => 'Ücretlendirilen sayaçlardan bir veya birkaçı aboneye tahsisli değil. Önce tahsis işlemlerinin tamamlanması gerekiyor!', 'type' => 'error'));
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Aktarma Kaydedilemedi', 'text' => 'Servis Kayıdı Girilemedi', 'type' => 'error'));
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Aktarma Hatası', 'text' => 'Onaylama onaylananlara kaydedilemedi', 'type' => 'error'));
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Onaylama Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }
}
